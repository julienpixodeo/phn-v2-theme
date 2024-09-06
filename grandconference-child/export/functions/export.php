<?php
// export admin enqueue
function export_admin_enqueue($hook) {
    wp_enqueue_style('export-css', get_stylesheet_directory_uri() . '/export/css/export.css', array(), time(), 'all');
    wp_enqueue_script('export-script', get_stylesheet_directory_uri() . '/export/js/export.js', array('jquery'), time(), true);
}
add_action('admin_enqueue_scripts', 'export_admin_enqueue');

// register menu page export
function register_menu_page_export(){
    add_menu_page( 
        __( 'Export ticket list', 'textdomain' ),
        'Export ticket list',
        'manage_options',
        'export_ticket_list',
        'export_ticket_list_page',
        'dashicons-database-export',
        10
    );
    
    add_submenu_page(
        'export_ticket_list',
        __( 'Export rooming list', 'textdomain' ),
        __( 'Export rooming list', 'textdomain' ),
        'manage_options',
        'export_rooming_list',
        'export_rooming_list_page',
    );
}
add_action( 'admin_menu', 'register_menu_page_export' );

// export ticket list page
function export_ticket_list_page() {
    $args = array(
        'post_type' => 'tribe_events',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'order' => 'DESC', 
        'orderby' => 'date'
    );
    $query = new WP_Query($args);

    ?>
    <div id="wpbody-content" aria-label="Main content" tabindex="0">
        <div class="wrap">
            <h1 class="wp-heading-inline">Export ticket list</h1>
            <div class="inside">
                <form method="post" action="">
                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row"><label for="formule">Select Event</label></th>
                                <td>
                                    <?php
                                        if ($query->have_posts()) {
                                            echo '<select name="event_export" required>';
                                                echo '<option value="">Select event</option>';
                                                while ($query->have_posts()) {
                                                    $query->the_post();
                                                    $post_id = get_the_ID();
                                                    $product_id = get_post_meta($post_id, 'product_of_events', true);
                                                    $post_title = get_the_title();
                                                    $selected = "";
                                                    if(isset($_POST['event_export']) && $_POST['event_export'] == $product_id){
                                                        $selected = "selected";
                                                    }
                                                    echo '<option '.$selected.' value="' . esc_attr($product_id) . '">' . esc_html($post_title) . '</option>';
                                                }
                                            echo '</select>';
                                        } else {
                                            echo 'No posts found.';
                                        }
                                        wp_reset_postdata();
                                    ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <?php 
                        submit_button('Export'); 
                    ?>
                </form>
            </div>
        </div>
    </div>
    <?php
}

// get woocommerce events order tickets
function get_woocommerce_events_order_tickets($order_id){
    $tickets_query = new WP_Query(
        array(
            'post_type' => array('event_magic_tickets'),
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'WooCommerceEventsOrderID',
                    'value' => $order_id,
                    'compare' => '=',
                ),
            ),
        )
    );
    $event_tickets = $tickets_query->get_posts();
    $config = new FooEvents_Config();
    $FooEvents_Orders_Helper = new FooEvents_Orders_Helper($config);
    $woocommerce_events_order_tickets = $FooEvents_Orders_Helper->process_event_tickets_for_display($event_tickets);
    return $woocommerce_events_order_tickets;
}

// export ticket list
function export_ticket_list() {
    if (isset($_POST['event_export']) && !empty($_POST['event_export'])) {
        $order_data = get_order_data();

        header("Content-Disposition: attachment; filename=" . date("Y-m-d", current_time("timestamp")) . ".csv");
        header("Content-Transfer-Encoding: binary");
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2000 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");
        header("Content-Type: text/csv; charset=UTF-8");
        
        // Send UTF-8 BOM to ensure Excel reads the file as UTF-8
        echo "\xEF\xBB\xBF";

        $headers = array(
            'Date de commande',
            'Numéro de commande',
            'Genre',
            'Prénom',
            'Nom',
            'Entreprise',
            'Email',
            'Address 1',
            'Address 2',
            'Code postal',
            'Ville',
            'Pays',
            'Numéro de facture',
            'Prix',
            'Type de carte',
            'Numéro de carte',
            'Informations sur les billets'
        );

        $fp = fopen('php://output', 'w');
        fputcsv($fp, $headers, ';');

        if (!empty($order_data)) {
            foreach ($order_data as $order) {
                $woocommerce_events_order_tickets = get_woocommerce_events_order_tickets($order->ID);
                if(!empty($woocommerce_events_order_tickets)){
                    foreach ( $woocommerce_events_order_tickets as $event ){
                        if(isset($event['tickets']) && !empty($event['tickets'])){
                            foreach ( $event['tickets'] as $ticket ) {
                                $data_form = '';
                                $ticket_id = $ticket['WooCommerceEventsTicketID'];
                                $information_user_each_ticket = information_user_each_ticket($ticket_id);
                                if($information_user_each_ticket){
                                    $total = count($information_user_each_ticket);
                                    $current = 0;
                                    foreach($information_user_each_ticket as $value) {
                                        if (is_serialized($value['value'])) {
                                            $value['value'] = unserialize($value['value']);
                                            if (is_array($value['value'])) {
                                                $value['value'] = implode(', ', $value['value']);
                                            }
                                        }
                                        $current++; 
                                        $data_form .= esc_attr($value['name']) . ": " . esc_attr($value['value']);
                                        if ($current < $total) {
                                            $data_form .= " | ";
                                        }
                                    }
                                }
                                $row = array(
                                    $order->date_created_gmt,
                                    $order->ID,
                                    $order->gender,
                                    $order->first_name,
                                    $order->last_name,
                                    $order->company,
                                    $order->email,
                                    $order->address_1,
                                    $order->address_2,
                                    $order->postcode,
                                    $order->city,
                                    $order->country,
                                    $order->invoice_number,
                                    $order->price,
                                    $order->means_of_payment,
                                    $order->card_number,
                                    $data_form
                                );
                                fputcsv($fp, $row, ';');
                            }
                        }
                    }
                }
            }
        }

        fclose($fp);
        exit();
    }
}
add_action("admin_init","export_ticket_list");

// get order data
function get_order_data(){
    $product_id = $_POST['event_export'];
    global $wpdb;
    $sql = "SELECT 
                o.date_created_gmt,
                o.ID,
                d.first_name,
                d.last_name,
                d.company,
                d.email,
                d.address_1,
                d.address_2,
                d.postcode,
                d.city,
                d.country,
                st.total_sales AS price,
                lk.product_id,
                om_card.meta_value AS card_number,
                om_means.meta_value AS means_of_payment,
                om_gender.meta_value AS gender,
                mt.meta_value AS invoice_number
            FROM 
                {$wpdb->prefix}wc_orders o
            LEFT JOIN 
                {$wpdb->prefix}wc_order_addresses d ON o.ID = d.order_id AND d.address_type = 'billing'
            LEFT JOIN 
                {$wpdb->prefix}wc_order_stats st ON o.ID = st.order_id
            LEFT JOIN 
                {$wpdb->prefix}wc_order_product_lookup lk ON o.ID = lk.order_id
            LEFT JOIN 
                {$wpdb->prefix}wc_orders_meta om_card ON o.ID = om_card.order_id AND om_card.meta_key = 'Card number'
            LEFT JOIN 
                {$wpdb->prefix}wc_orders_meta om_means ON o.ID = om_means.order_id AND om_means.meta_key = 'Means of payment'
            LEFT JOIN 
                {$wpdb->prefix}wc_orders_meta om_gender ON o.ID = om_gender.order_id AND om_gender.meta_key = '_billing_genre'
            LEFT JOIN 
                {$wpdb->prefix}postmeta mt ON o.ID = mt.post_id AND mt.meta_key = 'invoice_number'
            WHERE 
                o.type = 'shop_order'
                AND o.status = 'wc-completed'
                AND lk.product_id = $product_id
            ORDER BY 
                o.ID DESC";
    $order_data = $wpdb->get_results($sql);
    return $order_data;
}

// export rooming list page
function export_rooming_list_page() {
    $args = array(
        'post_type' => 'tribe_events',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'order' => 'DESC', 
        'orderby' => 'date'
    );
    $query = new WP_Query($args);

    ?>
    <div id="wpbody-content" aria-label="Main content" tabindex="0">
        <div class="wrap">
            <h1 class="wp-heading-inline">Export rooming list</h1>
            <div class="inside">
                <form method="post" action="">
                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row"><label for="formule">Select Event</label></th>
                                <td>
                                    <?php
                                        if ($query->have_posts()) {
                                            echo '<select name="room_export" required>';
                                                echo '<option value="">Select event</option>';
                                                while ($query->have_posts()) {
                                                    $query->the_post();
                                                    $post_id = get_the_ID();
                                                    $product_id = get_post_meta($post_id, 'product_of_events', true);
                                                    $post_title = get_the_title();
                                                    $selected = "";
                                                    if(isset($_POST['room_export']) && $_POST['room_export'] == $product_id){
                                                        $selected = "selected";
                                                    }
                                                    echo '<option '.$selected.' value="' . esc_attr($product_id) . '">' . esc_html($post_title) . '</option>';
                                                }
                                            echo '</select>';
                                        } else {
                                            echo 'No posts found.';
                                        }
                                        wp_reset_postdata();
                                    ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <?php 
                        submit_button('Export'); 
                    ?>
                </form>
            </div>
        </div>
    </div>
    <?php
}

// export rooming list
function export_rooming_list() {
    if (isset($_POST['room_export']) && !empty($_POST['room_export'])) {
        $order_data = get_order_data_export_room();
        
        header("Content-Disposition: attachment; filename=" . date("Y-m-d", current_time("timestamp")) . ".csv");
        header("Content-Transfer-Encoding: binary");
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2000 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");
        header("Content-Type: text/csv; charset=UTF-8");
        
        // Send UTF-8 BOM to ensure Excel reads the file as UTF-8
        echo "\xEF\xBB\xBF";
        
        $headers = array(
            'Date de commande',
            'Numéro de commande',
            'Nom de l\'hôtel',
            'Type de chambre',
            'Date d\'arrivée',
            'Date de départ',
            'Nombre de nuit',
            'Informations chambre'
        );
        
        $fp = fopen('php://output', 'w');
        fputcsv($fp, $headers, ';');
        
        if (!empty($order_data)) {
            foreach ($order_data as $order) {
                if (in_array((int)$_POST['room_export'], get_all_item_order((int)$order->ID))) {
                    if ((int)$order->product_id != (int)$_POST['room_export']) {
                        $order_id = (int)$order->ID;
                        $date_created_gmt = $order->date_created_gmt;
                        $rooms_id = (int)$order->variation_id;
                        $title_hotel = get_post_title_by_id((int)$order->product_id);
                        $title_room = get_variation_title_by_id($rooms_id);
                        $date = $order->t_date;
                        $start_day_ts = $order->start_day_ts;
                        $end_day_ts = $order->end_day_ts;
                        
                        if (!empty($date)) {
                            if ($start_day_ts == $end_day_ts) {
                                $start = $end = $date;
                            } else {
                                $array_date = explode(" to ", $date);
                                $start = $array_date[0];
                                $end = $array_date[1];
                            }
                        } else {
                            $start = $end = '';
                        }
                        
                        if (!empty($start_day_ts) && !empty($end_day_ts)) {
                            if ($start_day_ts == $end_day_ts) {
                                $night = 1;
                            } else {
                                $night = ((int)$end_day_ts - (int)$start_day_ts) / 86400;
                            }
                        } else {
                            $night = '';
                        }

                        $result = information_each_room($order_id,$rooms_id);
    
                        if ($result) {
                            foreach ($result as $value) {
                                $lead_id = $value->lead_id;
                                $form_id = $value->form_id;
                                $tags = vxcf_form::get_form_fields($form_id);
                                $vxcf_form = new vxcf_form();
                                $entry_detail = $vxcf_form->get_entry_detail($lead_id);
                                $data_form = '';
                                if ($entry_detail && $tags) {
                                    unset($entry_detail['order_id']);
                                    unset($entry_detail['rooms_id']);
                                    $total = count($entry_detail);
                                    $current = 0;
                                    $data_form = '';
                                    foreach ($entry_detail as $key => $value) {
                                        $name = $tags[$key]['label'];
                                        if (is_serialized($value['value'])) {
                                            $value['value'] = unserialize($value['value']);
                                            if (is_array($value['value'])) {
                                                $value['value'] = implode(', ', $value['value']);
                                            }
                                        }
                                        $current++; 
                                        $data_form .= esc_attr($name) . ": " . esc_attr($value['value']);
                                        if ($current < $total) {
                                            $data_form .= " | ";
                                        }
                                    }
                                }
                                $row = array(
                                    $date_created_gmt,
                                    $order_id,
                                    $title_hotel,
                                    $title_room,
                                    $start,
                                    $end,
                                    $night,
                                    $data_form
                                );
                                
                                fputcsv($fp, $row, ';');
                            }
                        }
                    }
                }
            }
        }
        
        fclose($fp);
        exit();
    }
}
add_action("admin_init","export_rooming_list");

// get order data export rooming
function get_order_data_export_room(){
    $product_id = $_POST['room_export'];
    global $wpdb;
    $sql = "SELECT 
                o.date_created_gmt,
                o.ID,
                lk.product_id,
                lk.variation_id,
                it_start.meta_value AS start_day_ts,
                it_end.meta_value AS end_day_ts,
                it_date.meta_value AS t_date
            FROM 
                {$wpdb->prefix}wc_orders o
            LEFT JOIN 
                {$wpdb->prefix}wc_order_product_lookup lk ON o.ID = lk.order_id
            LEFT JOIN 
                {$wpdb->prefix}woocommerce_order_itemmeta it_start ON it_start.order_item_id = lk.order_item_id AND it_start.meta_key = 'start_day_ts'
            LEFT JOIN 
                {$wpdb->prefix}woocommerce_order_itemmeta it_end ON it_end.order_item_id = lk.order_item_id AND it_end.meta_key = 'end_day_ts'
            LEFT JOIN 
                {$wpdb->prefix}woocommerce_order_itemmeta it_date ON it_date.order_item_id = lk.order_item_id AND it_date.meta_key = 'Date'
            WHERE 
                o.type = 'shop_order'
                AND o.status = 'wc-completed'
            ORDER BY 
                o.ID DESC";
    $order_data = $wpdb->get_results($sql);
    return $order_data;
}

// get post title by id
function get_post_title_by_id($post_id) {
    global $wpdb;

    // Prepare and execute the query to get the post title
    $post_title = $wpdb->get_var($wpdb->prepare(
        "SELECT post_title FROM $wpdb->posts WHERE ID = %d",
        $post_id
    ));

    return $post_title;
}

// get variation title by id
function get_variation_title_by_id($post_id) {
    global $wpdb;

    // Prepare and execute the query to get the post title
    $post_title = $wpdb->get_var($wpdb->prepare(
        "SELECT post_content FROM $wpdb->posts WHERE ID = %d",
        $post_id
    ));

    return $post_title;
}

// get all item order
function get_all_item_order($order_id) {
    global $wpdb;

    $sql = "SELECT product_id FROM {$wpdb->prefix}wc_order_product_lookup WHERE order_id = $order_id";

    $item_data = $wpdb->get_results($sql);

    $item = [];
    if(!empty($item_data)){
        foreach($item_data as $data){
            $item[] = (int) $data->product_id;
        }
        $event_id = get_post_meta($order_id, 'event_id_of_hotel', true);
        if(!empty($event_id)){
            $product_id = get_post_meta($event_id, 'product_of_events', true);
            $item[] = (int) $product_id;
        } 
    }
    return $item;
}

// information each room
function information_each_room($order_id,$rooms_id){
    global $wpdb;
    $information = [];
    $name_order = 'order_id';
    $name_room = 'rooms_id';

    $query = $wpdb->prepare("
        SELECT ld1.lead_id, l.form_id
        FROM {$wpdb->prefix}vxcf_leads_detail ld1
        INNER JOIN {$wpdb->prefix}vxcf_leads_detail ld2 ON ld1.lead_id = ld2.lead_id
        INNER JOIN {$wpdb->prefix}vxcf_leads l ON ld1.lead_id = l.id
        WHERE (ld1.name = %s AND ld1.value = %d)
        AND (ld2.name = %s AND ld2.value = %d)
    ", $name_order, $order_id, $name_room, $rooms_id);

    $result = $wpdb->get_results($query);
    
    return $result;
}
?>