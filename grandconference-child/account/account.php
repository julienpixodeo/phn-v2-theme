<?php
// ajax login
function ajax_login(){
    check_ajax_referer( 'ajax-login-nonce', 'security' );
    $info = array();
    $info['user_login'] = $_POST['username'];
    $info['user_password'] = $_POST['password'];
    $info['remember'] = true;
    $user_signon = wp_signon( $info, true );

    if ( is_wp_error($user_signon) ){
        $data = false;
    } else {
        $data = true;
    }

    $return = array(
        'data' => $data
    );

    wp_send_json($return);
}
add_action( 'wp_ajax_ajaxlogin', 'ajax_login' );
add_action( 'wp_ajax_nopriv_ajaxlogin', 'ajax_login' );

// ajax regiter
function RegisterClient(){
    session_start();
    if(isset($_SESSION['lan_st'])){
        $lan_st = $_SESSION['lan_st'];
    }else{
        $lan_st = 'french';
    }

    if($lan_st === 'french'){
        $message_error = 'Impossible de créer un compte';
        $email_exist = 'L\'e-mail existe déjà';
    }else{
        $message_error = 'Unable to create an account';
        $email_exist = 'Email already exists';
    }
    $email = sanitize_email($_POST['email']);
    $password = sanitize_text_field($_POST['password']);
    $password_confirm = sanitize_text_field($_POST['password_confirm']);
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);

	if(!email_exists($email)){
		$userdata = array(
            'user_login' => $email,
            'user_email' => $email,
            'user_pass'  => $password,
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'role'       => 'subscriber'
		);

		$user_id = wp_insert_user( $userdata );
		 
		if ( ! is_wp_error( $user_id ) ) 
		{
		    $login_data = array();  
		    $login_data['user_login'] = $email;  
		    $login_data['user_password'] = $password;  
		    $login_data['remember'] = true;  
		    $user_verify = wp_signon( $login_data, false ); 
		    $data = true;
		}else{
            $data = $message_error;
        }
	}else{
        $data = $email_exist;
    }

	$return = array(
	    'data' => $data
	);	 

	wp_send_json($return);
}
add_action('wp_ajax_RegisterClient', 'RegisterClient');
add_action('wp_ajax_nopriv_RegisterClient', 'RegisterClient');

// ajax edit client
function EditClient() {
    check_ajax_referer('edit_client_action', 'edit_client_nonce'); // Check nonce
    session_start();
    if(isset($_SESSION['lan_st'])){
        $lan_st = $_SESSION['lan_st'];
    }else{
        $lan_st = 'french';
    }

    if($lan_st === 'french'){
        $message_user = 'Utilisateur non connecté';
        $email_exist = 'L\'e-mail existe déjà';
    }else{
        $message_user = 'User not logged in';
        $email_exist = 'Email already exists';
    }

    $pw = false;
    $user_id = get_current_user_id(); // Get the current user ID
    if (!$user_id) {
        wp_send_json(array('data' => $message_user));
        return;
    }

    $email = sanitize_email($_POST['email']);
    $password = sanitize_text_field($_POST['password']);
    $password_confirm = sanitize_text_field($_POST['password_confirm']);
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);

    // Update email if it's different
    if ($email !== wp_get_current_user()->user_email) {
        if (!email_exists($email)) {
            wp_update_user(array(
                'ID' => $user_id,
                'user_email' => $email,
            ));
        } else {
            wp_send_json(array('data' => $email_exist));
            return;
        }
    }

    // Update first name and last name
    wp_update_user(array(
        'ID' => $user_id,
        'first_name' => $first_name,
        'last_name' => $last_name,
    ));

    // Update password if it's provided and matches the confirmation
    if (!empty($password)) {
        if ($password === $password_confirm) {
            wp_set_password($password, $user_id);
            $pw = true;
        }
    }

    wp_send_json(array('data' => true,'pw' => $pw));
}
add_action('wp_ajax_EditClient', 'EditClient');
add_action('wp_ajax_nopriv_EditClient', 'EditClient');

// check event in order
function event_true($order_id){
    $order = wc_get_order( $order_id );
    foreach ( $order->get_items() as $item_id => $item ) {
        $product_id = $item->get_product_id();
        $type = get_post_meta($product_id, 'phn_type_product', true);
        if ($type === 'event') {
            return false;
        }
    }
    return true;
}

// get id hotel of product
function get_id_hotel($order_id){
    $order = wc_get_order( $order_id );
    foreach ( $order->get_items() as $item_id => $item ) {
        $product_id = $item->get_product_id();
        $hotels_of_product  = get_post_meta($product_id, 'hotels_of_product', true);
    }
    return $hotels_of_product;
}

// get variation id hotel of product
function get_variation_id_hotel($order_id){
    $order = wc_get_order( $order_id );
    foreach ( $order->get_items() as $item_id => $item ) {
        $variation_id = $item->get_variation_id();
    }
    return $variation_id;
}

// get date refund hotel
function get_date_refund_hotel($id_hotel,$event_id,$variation_id_hotel){
    $data_hotel_event = get_post_meta($event_id, 'data_hotel_event', true);
    if($data_hotel_event){
        foreach($data_hotel_event as $key => $value){
            if($value['hotel_id'] == $id_hotel){
                $variations_data = $value['variations_data'];
                break;
            }
        }
    }
    $date_refund = isset($variations_data[0]['date']) ? $variations_data[0]['date'] : '';
    $date_refund_timestamp  = '';
    if($date_refund){
        $date = DateTime::createFromFormat('Y-m-d', $date_refund);
        $date_refund_timestamp = $date->getTimestamp();
    }
    return $date_refund_timestamp;
}

// check show refund all
function check_show_refund_all($order_id){
    $order = wc_get_order( $order_id );
    $event_id  = get_post_meta( $order_id, 'event_id_order', true );
    foreach ( $order->get_items() as $item_id => $item ) {
        $meta_data = $item->get_meta_data();
        $variation_id = $item->get_variation_id();
        $current  = time();
        $product_id = $item->get_product_id();
        $type = get_post_meta($product_id, 'phn_type_product', true);
        if($type === "event"){
            $date_refund = get_field('date_refund',$event_id);
            if($date_refund){
                $date = DateTime::createFromFormat('d/m/Y', $date_refund);
                $date_refund_timestamp = $date->getTimestamp();
            }
        }else{
            $id_hotel  = get_post_meta($product_id, 'hotels_of_product', true);
            $variation_id_hotel = $item->get_variation_id();
            $date_refund_timestamp = get_date_refund_hotel($id_hotel,$event_id,$variation_id_hotel);
        }

        if($date_refund_timestamp > $current && status_item_order($meta_data) == true){
            return true;
        } 
    }
    return false;
}

// get refund amount
function get_refund_amount($order_id){
    $order = wc_get_order( $order_id );
    $item_count = count($order->get_items());
    if($item_count == 1){
        $refund_amount = $order->get_total();
    }else{
        $refund_amount = 0;
        $event_id  = get_post_meta( $order_id, 'event_id_order', true );
        foreach ( $order->get_items() as $item_id => $item ) {
            $meta_data = $item->get_meta_data();
            $variation_id = $item->get_variation_id();
            $current  = time();
            $product_id = $item->get_product_id();
            $type = get_post_meta($product_id, 'phn_type_product', true);
            if($type === "event"){
                $date_refund = get_field('date_refund',$event_id);
                if($date_refund){
                    $date = DateTime::createFromFormat('d/m/Y', $date_refund);
                    $date_refund_timestamp = $date->getTimestamp();
                }
            }else{
                $id_hotel  = get_post_meta($product_id, 'hotels_of_product', true);
                $variation_id_hotel = $item->get_variation_id();
                $date_refund_timestamp = get_date_refund_hotel($id_hotel,$event_id,$variation_id_hotel);
            }
           
            if($date_refund_timestamp > $current && status_item_order($meta_data) == true){
                $product_total_incl_tax = $item->get_total() + $item->get_total_tax();
                $refund_amount += $product_total_incl_tax;
            } 
        }
        $refund_amount = round($refund_amount);
    }
    return $refund_amount;
}

function get_email_by_user_id($user_id) {
    // Get the user data by user ID
    $user_data = get_userdata($user_id);

    // Check if user data exists
    if ($user_data) {
        // Return the user's email
        return $user_data->user_email;
    } else {
        // Return false if no user data found
        return false;
    }
}

// get user orders info
function get_user_orders_info() {
    $user_id = get_current_user_id();
    if(isset($_SESSION['lan_st'])){
        $lan_st = $_SESSION['lan_st'];
    }else{
        $lan_st = 'french';
    }

    if($lan_st === 'french'){
        $text_order_id = 'ID de commande';
        $text_order_date = 'Date de commande';
        $text_order_total = 'Total de la commande';
        $text_order_items = 'Commander des articles';
        $text_quantity = 'Quantité';
        $text_price = 'Prix';
        $text_refund = 'Remboursement';
        $text_refund_all  = 'Rembourser tout';
        $error_user_login = 'Utilisateur non connecté.';
        $error_user_no_order = 'Vous n\'avez pas encore de commandes.';
    }else{
        $text_order_id = 'Order ID';
        $text_order_date = 'Order date';
        $text_order_total = 'Order total';
        $text_order_items = 'Order items';
        $text_quantity = 'Quantity';
        $text_price = 'Price';
        $text_refund = 'Refund';
        $text_refund_all  = 'Refund all';
        $error_user_login = 'User not logged in.';
        $error_user_no_order = 'You have no orders yet.';
    }
    if ($user_id) {
        session_start();
        
        $args = array(
            'customer_id' => $user_id,
            'status'      => 'any',
            'limit'       => -1,
        );

        $orders = wc_get_orders($args);

        ob_start();

        if($orders){   
            // Loop through each order
            foreach ($orders as $order) {
                $order_id = $order->get_id();
                $order_date = $order->get_date_created();
                $order_status = $order->get_status();
                $order_total = $order->get_total();
                $order_currency = $order->get_currency();
                $order_payment_method = $order->get_payment_method_title();

                echo '<div class="order-box" id="order-' . $order_id . '">';
                echo '<div class="order-header"><h4>'.$text_order_id.': ' . $order_id . '</h4></div>';
                echo '<div class="order-details">';
                echo ''.$text_order_date.': ' . $order_date->date('Y-m-d H:i:s') . '<br>';
                echo ''.$text_order_total.': ' . wc_price($order_total) . '<br>';
                echo '</div>';

                $items = $order->get_items();

                echo '<div class="order-items">';
                echo '<h4>'.$text_order_items.':</h4>';

                // Add refund button and message box for each order
                $event_id  = get_post_meta( $order_id, 'event_id_order', true );

                foreach ($items as $item_id => $item) {
                    $product = $item->get_product();
                    $product_name = $item->get_name();
                    $product_quantity = $item->get_quantity();
                    $product_total_incl_tax = $item->get_total() + $item->get_total_tax();
                    $product_id = $product->get_id();
                    $product_sku = $product->get_sku();
                    $phn_type_product = get_post_meta($product_id,'phn_type_product',true);
                    if($lan_st === 'french'){
                        $type = ($phn_type_product === 'event') ? 'Événement' : 'Hôtel';
                    }else{
                        $type = ($phn_type_product === 'event') ? 'Event' : 'Hotel';
                    }
                    // Get meta data
                    $meta_data = $item->get_meta_data();

                    $parent_id = wp_get_post_parent_id($product_id);
                    if($parent_id != 0){
                        $product_id = $parent_id;
                    }

                    $thumbnail_url = get_the_post_thumbnail_url( $product_id, 'full' );

                    if ( empty( $thumbnail_url ) ) {
                        $_product_image_gallery = get_post_meta($product_id,'_product_image_gallery',true);
                        if(!empty($_product_image_gallery)){
                            $_product_image_gallery = explode(",",$_product_image_gallery);
                            $thumbnail_url = wp_get_attachment_url( $_product_image_gallery[0] );
                        }else{
                            $thumbnail_url = get_permalink(28838);
                        }
                    }

                    echo '<div class="product-item">';
                    echo '<img width="300" height="300" src="'.$thumbnail_url.'" class="image-product">';
                    echo '<div class="product-item-content">';
                    echo '<span class="product-name">'.$type.' : ' . $product_name . '</span>';
                    echo '<div>'.$text_quantity.': ' . $product_quantity . '</div>';
                    echo '<div class="wrap-price">'.$text_price.': ' . wc_price($product_total_incl_tax) . '</div>';
                    if (!empty($meta_data)) {
                        foreach ($meta_data as $meta) {
                            $meta_key = $meta->key;
                            $meta_value = $meta->value;
                            if ($meta_key == 'Date') {
                                echo '<div>' . esc_html($meta_key) . ': ' . esc_html($meta_value) . '</div>';
                            }

                            if(status_item_order($meta_data) == false){
                                if ($meta_key == 'Status') {
                                    if(!empty($meta_value)){
                                        if($lan_st === 'french'){
                                            if($meta_key === 'Status'){
                                                $meta_key = 'Statut';
                                            }

                                            if($meta_value === 'Refund'){
                                                $meta_value = 'Remboursement';
                                            }
                                        }
                                    }
                                    echo '<div>' . esc_html($meta_key) . ': ' . esc_html($meta_value) . '</div>';
                                }
                            }
                        }
                    }

                    $type = get_post_meta($product_id, 'phn_type_product', true);
                    $product_id = $item->get_product_id();
                    $current  = time();
                    if($type === "event"){
                        $date_refund = get_field('date_refund',$event_id);
                        if($date_refund){
                            $date = DateTime::createFromFormat('d/m/Y', $date_refund);
                            $date_refund_timestamp = $date->getTimestamp();
                        }
                    }else{
                        $id_hotel  = get_post_meta($product_id, 'hotels_of_product', true);
                        $variation_id_hotel = $item->get_variation_id();
                        $date_refund_timestamp = get_date_refund_hotel($id_hotel,$event_id,$variation_id_hotel);
                    }
               
                    // if ($order_status === 'completed' && count($items) != 1) {
                    if (count($items) != 1 && status_item_order($meta_data) == true && $date_refund_timestamp > $current && $order_status !== 'refunded' && $order_status === 'completed') {
                        echo '<button class="refund-button" data-order-id="' . $order_id . '" 
                        data-message-id="' . $item_id . '" 
                        data-order-item="' . $item_id . '"
                        data-order-price="' . round($product_total_incl_tax) . '">
                        '.$text_refund.'
                        </button>';
                        echo '<div class="message-box" id="message-' . $item_id . '"></div>';
                    }
                    echo '</div>';
                    echo '</div>';
                }
                echo '</div>'; // End of order items

                // if ($order_status === 'completed' && count($items) === 1) {
                if ($order_status !== 'refunded' && $order_status === 'completed') {
                    if (count($items) === 1) {
                        if(event_true($order_id) == false){
                            $date_refund = get_field('date_refund',$event_id);
                            if($date_refund){
                                $date = DateTime::createFromFormat('d/m/Y', $date_refund);
                                $date_refund_timestamp = $date->getTimestamp();
                            }
                        }else{
                            $id_hotel =  get_id_hotel($order_id);
                            $variation_id_hotel = get_variation_id_hotel($order_id);
                            $date_refund_timestamp = get_date_refund_hotel($id_hotel,$event_id,$variation_id_hotel);
                        }
                        $current  = time();
                        if($date_refund_timestamp > $current){
                            echo '<button class="refund-button" data-order-id="' . $order_id . '" data-message-id="' . $order_id . '" >'.$text_refund.'</button>';
                            echo '<div class="message-box" id="message-' . $order_id . '"></div>';
                        }
                    }else{
                        if(check_show_refund_all($order_id) == true){
                            echo '<button class="refund-button" data-order-id="' . $order_id . '" data-message-id="' . $order_id . '" >'.$text_refund_all.'</button>';
                            echo '<div class="message-box" id="message-' . $order_id . '"></div>';
                        }
                    }
                }

                echo '</div>'; // End of order box
            }
            return ob_get_clean();
        }else{
            return $error_user_no_order;
        }  
    } else {
        return $error_user_login;
    }
}

// status item order
function status_item_order($meta_data){
    foreach ($meta_data as $meta) {
        $meta_key = $meta->key;
        $meta_value = $meta->value;
        if ($meta_key == 'Status') {
            if(!empty($meta_value)){
                return false;
            }
        }
    }
    return true;
}

// Register shortcode to display user orders
function register_user_orders_shortcode() {
    add_shortcode('user_orders', 'get_user_orders_info');
}
add_action('init', 'register_user_orders_shortcode');

// process ajax refund
function process_ajax_refund() {
    session_start();
    if(isset($_SESSION['lan_st'])){
        $lan_st = $_SESSION['lan_st'];
    }else{
        $lan_st = 'french';
    }

    if($lan_st === 'french'){
        $message_error = 'Les remboursements ne peuvent pas être traités';
        $error_user_login = 'Utilisateur non connecté.';
        $message_success = 'Remboursement traité avec succès';
    }else{
        $message_error = 'Refunds cannot be processed';
        $error_user_login = 'User not logged in.';
        $message_success = 'Refund processed successfully';
    }
    // Check if the current user is logged in and if the order ID is passed
    if (is_user_logged_in() && isset($_POST['order_id'])) {
        $message = '';
        $status = true;
        $order_item = isset($_POST['order_item']) ? (int) $_POST['order_item'] : '';
        $order_price = isset($_POST['order_price']) ? $_POST['order_price'] : '';
        $order_id = intval($_POST['order_id']);
        $order = wc_get_order($order_id);

        // Ensure the order exists and is paid
        if ( ! $order || ! $order->is_paid() ) {
            $message = $message_error;
            $status = false;
        }

        // Get the transaction ID from the order
        $transaction_id = $order->get_transaction_id();

        // Ensure the order has a transaction ID (Stripe payment)
        if ( ! $transaction_id ) {
            $message = $message_error;
            $status = false;
        }

        if($status == true){
            if(empty($order_item)){
                $refund_amount = get_refund_amount($order_id);
                $reason = 'Refund for Order #' . $order_id;
            }else{
                $refund_amount = $order_price;
                $reason = 'Refund for Item ' . get_name_refund_item_order($order,$order_item);
            }

            $refund = wc_create_refund( array(
                'amount'         => $refund_amount,
                'reason'         => $reason,
                'order_id'       => $order_id,
                'refund_payment' => true,
            ));

            if (is_wp_error($refund)) {
                $message = $message_error;
                $status = false;
            } else {
                if(empty($order_item)){
                    update_stock_each_day_variation_hotel_and_stock_event($order);
                }else{
                    update_stock_each_day_variation_hotel_and_stock_event_item($order,$order_item);   
                }
                add_status_refund_item_order($order,$order_item,$order_id);
                $message = $message_success;
                $status == true;
            }
        }
    } else {
        $message = $error_user_login;
        $status = false;
    }

    $return = array(
	    'message' => $message,
	    'status' => $status,
	);	 

	wp_send_json($return);
}
add_action( 'wp_ajax_process_ajax_refund', 'process_ajax_refund' );
add_action( 'wp_ajax_nopriv_process_ajax_refund', 'process_ajax_refund' );

// process ajax refund modal
function process_ajax_refund_modal() {
    session_start();
    if(isset($_SESSION['lan_st'])){
        $lan_st = $_SESSION['lan_st'];
    }else{
        $lan_st = 'french';
    }

    if($lan_st === 'french'){
        $message_error = 'Les remboursements ne peuvent pas être traités';
        $error_user_login = 'Utilisateur non connecté.';
        $message_success = 'Le montant qui vous sera remboursé est ';
    }else{
        $message_error = 'Refunds cannot be processed';
        $error_user_login = 'User not logged in.';
        $message_success = 'The amount that will be refunded to you is ';
    }
    // Check if the current user is logged in and if the order ID is passed
    if (is_user_logged_in() && isset($_POST['order_id'])) {
        $message = '';
        $status = true;
        $order_item = isset($_POST['order_item']) ? (int) $_POST['order_item'] : '';
        $order_price = isset($_POST['order_price']) ? $_POST['order_price'] : '';
        $order_id = intval($_POST['order_id']);
        $order = wc_get_order($order_id);

        // Ensure the order exists and is paid
        if ( ! $order || ! $order->is_paid() ) {
            $message = $message_error;
            $status = false;
        }

        // Get the transaction ID from the order
        $transaction_id = $order->get_transaction_id();

        // Ensure the order has a transaction ID (Stripe payment)
        if ( ! $transaction_id ) {
            $message = $message_error;
            $status = false;
        }

        if($status == true){
            if(empty($order_item)){
                $refund_amount = wc_price(get_refund_amount($order_id));
            }else{
                $refund_amount = wc_price($order_price);
            }
            $message = $message_success.$refund_amount;
        }
    } else {
        $message = $error_user_login;
        $status = false;
    }

    $return = array(
        'message' => $message,
        'status' => $status,
        'order_id' => $order_id,
        'message_id' => !empty($order_item) ? $order_item : $order_id,
        'order_item' => $order_item,
        'order_price' => $order_price
    );

    wp_send_json($return);
}
add_action( 'wp_ajax_process_ajax_refund_modal', 'process_ajax_refund_modal' );
add_action( 'wp_ajax_nopriv_process_ajax_refund_modal', 'process_ajax_refund_modal' );

// Add status refund item order
function add_status_refund_item_order($order,$order_item,$order_id){
    // Loop through order items.
    $item_count = count($order->get_items());
    foreach ( $order->get_items() as $item_id => $item ) {
        // Check if this is the specific item you want to update.
        if(!empty($order_item)){
            if ( $item_id == $order_item ) {
                // Add metadata to the item.
                $item->add_meta_data( 'Status', 'Refund', true );
                
                // Save the item after adding metadata.
                $item->save();
            }
        }else{
            if($item_count == 1){
                // Add metadata to the item.
                $item->add_meta_data( 'Status', 'Refund', true );
                // Save the item after adding metadata.
                $item->save();
            }
            else{
                $event_id  = get_post_meta( $order_id, 'event_id_order', true );
                $meta_data = $item->get_meta_data();
                $variation_id = $item->get_variation_id();
                $current  = time();
                $product_id = $item->get_product_id();
                $type = get_post_meta($product_id, 'phn_type_product', true);
                if($type === "event"){
                    $date_refund = get_field('date_refund',$event_id);
                    if($date_refund){
                        $date = DateTime::createFromFormat('d/m/Y', $date_refund);
                        $date_refund_timestamp = $date->getTimestamp();
                    }
                }else{
                    $id_hotel  = get_post_meta($product_id, 'hotels_of_product', true);
                    $variation_id_hotel = $item->get_variation_id();
                    $date_refund_timestamp = get_date_refund_hotel($id_hotel,$event_id,$variation_id_hotel);
                }
            
                if($date_refund_timestamp > $current){
                    // Add metadata to the item.
                    $item->add_meta_data( 'Status', 'Refund', true );
                    // Save the item after adding metadata.
                    $item->save();
                } 
            }            
        }
    }
}

// Get name refund item order
function get_name_refund_item_order($order,$order_item){
    // Loop through order items.
    foreach ( $order->get_items() as $item_id => $item ) {
        // Check if this is the specific item you want to update.
        if ( $item_id == $order_item ) {
            $product_name = $item->get_name();
        }
    }
    return $product_name;
}

// update stock each day variation hotel and stock event
function update_stock_each_day_variation_hotel_and_stock_event($order) {
    $stock_day = [];
    $stock_day_data = [];
    
    foreach ( $order->get_items() as $item_id => $item ) {
        $variation_id = $item->get_variation_id();
        $product_id = $item['product_id'];
        $event_id = (int)get_post_meta($item['product_id'] , 'events_of_product' , true);
        if ($event_id != 0) {
            $number_tickets = get_post_meta( $event_id, 'number_tickets', true );
            $new_number_tickets = $number_tickets + $item['quantity'];
            update_post_meta( $event_id, 'number_tickets', $new_number_tickets ); 
            $product_id = get_post_meta($event_id, 'product_of_events', true);
            update_post_meta( $product_id, '_stock', $new_number_tickets ); 
        }

        if($variation_id != 0){
            $quantity = $item->get_quantity();
            $start_day_st = $item->get_meta( 'start_day_st', true );
            $end_day_st = $item->get_meta( 'end_day_st', true );
            $quantity_each_day = quantity_each_day($start_day_st,$end_day_st,$quantity);
            $stock_day[] = [
                $variation_id => $quantity_each_day
            ];
        }
    }

    if(!empty($stock_day)){
        $stock_day_data = mergeArray($stock_day);
    }

    session_start();
    
    if($event_id == 0 || $event_id == ''){
        $event_id = $_SESSION['event_id'];
    }
    $data_hotel_event = get_post_meta($event_id, 'data_hotel_event', true);

    if($data_hotel_event){
        foreach ($data_hotel_event as &$hotel) {
            foreach ($hotel['variations_data'] as &$variation) {
                $variation_id = $variation['variations_id'];
                if (isset($stock_day_data[$variation_id])) {
                    foreach ($variation['date_available'] as $key => &$date_available) {
                        $timestamp = $date_available['date'];
                        if (isset($stock_day_data[$variation_id][$timestamp])) {
                            $date_available['stock'] += $stock_day_data[$variation_id][$timestamp];
                        }
                    }
                    // Re-index the array to maintain numeric keys
                    $variation['date_available'] = array_values($variation['date_available']);
                }
            }
        }        
    }
    update_post_meta($event_id, 'data_hotel_event', $data_hotel_event);
}

// update stock each day variation hotel and stock event item
function update_stock_each_day_variation_hotel_and_stock_event_item($order,$order_item) {
    $stock_day = [];
    $stock_day_data = [];
    
    foreach ( $order->get_items() as $item_id => $item ) {
        if($item_id === $order_item){
            $variation_id = $item->get_variation_id();
            $product_id = $item['product_id'];
            $event_id = (int)get_post_meta($item['product_id'] , 'events_of_product' , true);
            if ($event_id != 0) {
                $number_tickets = get_post_meta( $event_id, 'number_tickets', true );
                $new_number_tickets = $number_tickets + $item['quantity'];
                update_post_meta( $event_id, 'number_tickets', $new_number_tickets ); 
                $product_id = get_post_meta($event_id, 'product_of_events', true);
                update_post_meta( $product_id, '_stock', $new_number_tickets ); 
            }
    
            if($variation_id != 0){
                $quantity = $item->get_quantity();
                $start_day_st = $item->get_meta( 'start_day_st', true );
                $end_day_st = $item->get_meta( 'end_day_st', true );
                $quantity_each_day = quantity_each_day($start_day_st,$end_day_st,$quantity);
                $stock_day[] = [
                    $variation_id => $quantity_each_day
                ];
            }
        }
    }

    if(!empty($stock_day)){
        $stock_day_data = mergeArray($stock_day);
    }

    session_start();
    
    if($event_id == 0 || $event_id == ''){
        $event_id = $_SESSION['event_id'];
    }
    $data_hotel_event = get_post_meta($event_id, 'data_hotel_event', true);

    if($data_hotel_event){
        foreach ($data_hotel_event as &$hotel) {
            foreach ($hotel['variations_data'] as &$variation) {
                $variation_id = $variation['variations_id'];
                if (isset($stock_day_data[$variation_id])) {
                    foreach ($variation['date_available'] as $key => &$date_available) {
                        $timestamp = $date_available['date'];
                        if (isset($stock_day_data[$variation_id][$timestamp])) {
                            $date_available['stock'] += $stock_day_data[$variation_id][$timestamp];
                        }
                    }
                    // Re-index the array to maintain numeric keys
                    $variation['date_available'] = array_values($variation['date_available']);
                }
            }
        }        
    }
    update_post_meta($event_id, 'data_hotel_event', $data_hotel_event);
}

// set language load event
function set_language_load_event() {
	if ( is_singular('tribe_events') && !is_admin() ) {
        $lan = get_field('language',get_the_ID());
        session_start();
        if(isset($lan)){
            $_SESSION['lan_st'] = $lan;
        }else{
            $_SESSION['lan_st'] = 'english';
        }
	}
}
add_action( 'template_redirect', 'set_language_load_event' );
?>