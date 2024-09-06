<?php
// add tickets to cart
function add_tickets_to_cart(){
    $product_id = (isset($_POST['product_id'])) ? intval( $_POST['product_id'] ) : '';
    $event_id = (isset($_POST['event_id'])) ? intval( $_POST['event_id'] ) : '';
    $quantity = (isset($_POST['quantity'])) ? intval( $_POST['quantity'] ) : '';
    $lan = get_field('language',$event_id);
    $cart_url = wc_get_cart_url();
    $stock = get_post_meta($product_id, '_stock', true);
    $success = false;
    $message_success = ($lan === 'french') ? get_field('tickets_message_success_fr','option') : get_field('tickets_message_success','option');
    $message_limit = ($lan === 'french') ? get_field('tickets_message_limit_fr','option') : get_field('tickets_message_limit','option');
    $message_error = ($lan === 'french') ? get_field('tickets_message_error_fr','option') : get_field('tickets_message_error','option');
    $view_cart = ($lan === 'french') ? get_field('view_cart_fr','option') : get_field('view_cart','option');
    $message = '<a href="'.$cart_url.'" tabindex="1" class="button wc-forward">'.$view_cart.'</a> ';

    session_start();
    if($lan === 'french'){
        $_SESSION['locale'] = 'fr_FR';
    }else{
        $_SESSION['locale'] = 'en_US';
    }

    if($product_id && $quantity){
        wc_remove_product_from_cart($product_id);
        $cart_item_key = WC()->cart->add_to_cart( $product_id, $quantity );
    }

    if ($cart_item_key){
        $message .= $message_success;
        $success = true;
    }else{
        $product_quantity = quantity_product_cart($product_id);
        $quantity_total = $quantity + $product_quantity;
        if($quantity_total > $stock){
            $message_limit = str_replace('##',$stock,$message_limit);
            $message_limit = str_replace('%%',$product_quantity,$message_limit);
            $message .= $message_limit;
        }else{
            $message .= $message_error;
        }
    }

    $quantity_total = quantity_cart();

    echo json_encode(array('success' => $success,'message' => $message,'quantity_total' => $quantity_total));

    wp_die();
}
add_action('wp_ajax_add_tickets_to_cart', 'add_tickets_to_cart');
add_action('wp_ajax_nopriv_add_tickets_to_cart', 'add_tickets_to_cart');

// Set session form ticket
function set_session_form_ticket(){
    session_start();
    $_SESSION['infor_ticket'][] = $_POST;
    if(isset($_SESSION['infor_ticket'])){
        $infor_ticket = $_SESSION['infor_ticket'];
        $count = count($infor_ticket);
        $number_ticket = number_ticket_in_cart();
        if($count == $number_ticket){
            $redirect = false;
        }else{
            $redirect = true;
        }
    }
    $number_room = number_room_in_cart();
    if($number_room != 0){
        echo json_encode(array('redirect' => $redirect,'checkout_url'=> '/form-room-checkout'));
    }else{
        echo json_encode(array('redirect' => $redirect,'checkout_url'=> wc_get_checkout_url()));
    }
    // echo json_encode(array('redirect' => $redirect,'checkout_url'=> wc_get_checkout_url()));
    
    wp_die();
}
add_action('wp_ajax_set_session_form_ticket', 'set_session_form_ticket');
add_action('wp_ajax_nopriv_set_session_form_ticket', 'set_session_form_ticket');

function set_session_form_room(){
    session_start();
    $_SESSION['infor_room'][] = $_POST;
    if(isset($_SESSION['infor_room'])){
        $infor_room = $_SESSION['infor_room'];
        $count = count($infor_room);
        $number_room = number_room_in_cart();
        
        if($count == $number_room){
            $redirect = false;
        }else{
            $redirect = true;
        }
    }
    echo json_encode(array('redirect' => $redirect,'room_checkout_url'=> wc_get_checkout_url()));
    wp_die();
}
add_action('wp_ajax_set_session_form_room', 'set_session_form_room');
add_action('wp_ajax_nopriv_set_session_form_room', 'set_session_form_room');
?>