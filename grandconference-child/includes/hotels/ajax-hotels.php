<?php
// add hotel to cart
function add_hotel_to_cart()
{
    $product_id = (isset($_POST['product_id'])) ? intval($_POST['product_id']) : '';
    $hotel_id = (isset($_POST['hotel_id'])) ? intval($_POST['hotel_id']) : '';
    $quantity = (isset($_POST['quantity'])) ? intval($_POST['quantity']) : '';
    $name_typeOfRoom = (isset($_POST['name_typeOfRoom'])) ? sanitize_text_field($_POST['name_typeOfRoom']) : '';
    $description_number = (isset($_POST['description_number'])) ? sanitize_text_field($_POST['description_number']) : '';
    $price_typeOfRoom = (isset($_POST['price_typeOfRoom'])) ? intval($_POST['price_typeOfRoom']) : '';
    $fooEvents_typeOfRoom = (isset($_POST['fooEvents_typeOfRoom'])) ? intval($_POST['fooEvents_typeOfRoom']) : '';
    $event_id = (isset($_POST['event_id'])) ? intval($_POST['event_id']) : '';
    $cart_url = wc_get_cart_url();
    $lan = get_field('language', $hotel_id);
    $stock = get_post_meta($product_id, '_stock', true);
    $success = false;
    $message_success = ($lan === 'french') ? get_field('room_message_success_fr', 'option') : get_field('room_message_success', 'option');
    $message_limit = ($lan === 'french') ? get_field('room_message_limit_fr', 'option') : get_field('room_message_limit', 'option');
    $message_error = ($lan === 'french') ? get_field('room_message_error_fr', 'option') : get_field('room_message_error', 'option');
    $message_add_tickets_first = ($lan === 'french') ? get_field('add_tickets_to_cart_first_fr', 'option') : get_field('add_tickets_to_cart_first', 'option');
    $message_err_add_hotel = ($lan === 'french') ? get_field('message_err_add_hotel_fr', 'option') : get_field('message_err_add_hotel', 'option');
    $view_cart = ($lan === 'french') ? get_field('view_cart_fr', 'option') : get_field('view_cart', 'option');
    $message = '<a href="' . $cart_url . '" tabindex="1" class="button wc-forward">' . $view_cart . '</a> ';
    $hotel_id = get_post_meta($product_id, 'hotels_of_product', true);

    $product_id_variation = get_post_meta($hotel_id, 'product_of_hotels', true);
    
    if ($product_id && $quantity) {
        if (ticket_in_cart() == true) {
            $id_ticket = (int) id_ticket_in_cart();
            if ($id_ticket === $event_id) {
                $cart_item_data = array(
                    'id_hotelOfRoom' => $product_id,
                    'name_typeOfRoom' => $name_typeOfRoom,
                    'price_typeOfRoom' => $price_typeOfRoom,
                    'fooEvents_typeOfRoom' => $fooEvents_typeOfRoom,
                );
                // 
                
                $args_post = array(
                    'post_type' => 'product_variation',
                    'post_parent' => $product_id_variation,
                    'posts_per_page' => -1,
                );
                $query = new WP_Query($args_post);
                if ($query->have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();
                        $product_variation_title = get_the_content();
                        
                        if ($product_variation_title === $name_typeOfRoom) {
                            $product_id_typeroom = get_the_ID();
                            $array_typeOfRoom = "array_typeOfRoom";
                            $roomTypes[] = array(
                                // 'id_hotelOfRoom' => $product_id,
                                'id' => $product_id_typeroom,
                                'name' => $name_typeOfRoom,
                                'price' => $price_typeOfRoom,
                                'fooEvents' => $fooEvents_typeOfRoom,
                                'description_number' => $description_number,
                            );

                            $cart_item_data[$array_typeOfRoom] = array(
                                'roomTypes' => $roomTypes
                            );
                                
                            $cart_item_key = WC()->cart->add_to_cart($product_id_typeroom, $quantity, 0, array(), $cart_item_data);
                            // $cart_item_key = WC()->cart->add_to_cart($product_id_typeroom, $quantity);
                            if ($cart_item_key) {
                                $message .= $message_success;
                                $success = true;
                            } else {
                                $product_quantity = quantity_product_cart($product_id);
                                $quantity_total = $quantity + $product_quantity;
                                if ($quantity_total > $stock) {
                                    $message_limit = str_replace('##', $stock, $message_limit);
                                    $message_limit = str_replace('%%', $product_quantity, $message_limit);
                                    $message .= $message_limit;
                                } else {
                                    $message .= $message_error;
                                }
                            }
                        }
                    }
                    wp_reset_postdata();
                }
                else{
                    $cart_item_key = WC()->cart->add_to_cart($product_id, $quantity);
                    if ($cart_item_key) {
                        $message .= $message_success;
                        $success = true;
                    } else {
                        $product_quantity = quantity_hotel_product_cart($product_id);
                        $quantity_total = $quantity + $product_quantity;
                        if ($quantity_total > $stock) {
                            $message_limit = str_replace('##', $stock, $message_limit);
                            $message_limit = str_replace('%%', $product_quantity, $message_limit);
                            $message .= $message_limit;
                        } else {
                            $message .= $message_error;
                        }
                    }
                }
                // $cart_item_key = WC()->cart->add_to_cart( $product_id, $quantity );
                
            } else {
                $message .= $message_err_add_hotel;
            }
        } else {
            $message .= $message_add_tickets_first;
        }
    }

    $quantity_total = quantity_cart();

    echo json_encode(array('success' => $success,'message' => $message,'quantity_total' => $quantity_total));

    wp_die();
}
add_action('wp_ajax_add_hotel_to_cart', 'add_hotel_to_cart');
add_action('wp_ajax_nopriv_add_hotel_to_cart', 'add_hotel_to_cart');

function quantity_hotel_product_cart($product_id){
    $cart = WC()->cart;
    $cart_items = $cart->get_cart();
    $product_quantity = 0;
    foreach ( $cart_items as $cart_item_key => $cart_item ) {
        if ( $cart_item['product_id'] === $product_id ) {
            $product_quantity = $cart_item['quantity'];
            break;
        }
    }
    return $product_quantity;
}

// Custom add text after cart item name
// function custom_add_text_after_cart_item_name($cart_item, $cart_item_key){
//     echo woocommerce_add_text_after_item($cart_item['product_id']);
// }
// add_action( 'woocommerce_after_cart_item_name', 'custom_add_text_after_cart_item_name' , 10, 3);

// // Custom add text after checkout item name
// function custom_add_text_after_checkout_item_name($item_id, $item, $order){
//     echo woocommerce_add_text_after_item($item['product_id']);
// }
// add_action( 'woocommerce_order_item_meta_end', 'custom_add_text_after_checkout_item_name' , 10, 3);

// Woocommerce add text after item
// function woocommerce_add_text_after_item($product_id ){
//     $text = '';
//     $type = get_post_meta( $product_id, 'phn_type_product', true );
//     if($type === "hotel"){
//         $per_room_title = get_field('maximum_guest_per_room','option');
//         $per_room = get_post_meta( $product_id, 'maximum_guest_per_room', true );
//         $text = '<p style="margin:0;padding:0;">'.$per_room_title.': '.$per_room.'</p>';
//     }
//     return $text;
// }
?>