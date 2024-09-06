<?php
// get variations product
function get_variations_product($parent_id) {
    $args = array(
        'post_type'   => 'product_variation', 
        'post_parent' => $parent_id,
        'fields'      => 'ids',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'ASC'
    );

    $query = new WP_Query($args);
    return $query->posts;
}

// create update variations product
function create_update_variations_product($original_post_id,$product_id){
    $typeOfRooms = get_field('add_type_of_room', $original_post_id);
    $product = wc_get_product($product_id);
    $variations = get_variations_product($product_id);
    if (!empty($typeOfRooms)) {
        if(!empty($variations)){
            $type_of_rooms_values = array();
            foreach($variations as $key => $value){
                $variation_id = $value;
                $type_of_rooms_values[] = $typeOfRooms[$key]['name'];
                wp_update_post(
                    array(
                        'ID' => $variation_id,
                        'post_title' => get_the_title($original_post_id) . ' - ' . $typeOfRooms[$key]['name'],
                    )
                );
                
                update_post_meta($variation_id, 'attribute_type-of-rooms', $typeOfRooms[$key]['name']);
                update_post_meta($variation_id, '_price', $typeOfRooms[$key]['price']);
                update_post_meta($variation_id, '_regular_price', $typeOfRooms[$key]['price']);
                update_post_meta($variation_id, '_manage_stock', 'no');
                update_post_meta($variation_id, '_description', $typeOfRooms[$key]['description']);
            }

            if(count($typeOfRooms) > count($variations)){
                foreach(array_slice($typeOfRooms,count($variations)) as $key => $room){
                    $name = $room['name'];
                    $type_of_rooms_values[] = $name;
                    $price = $room['price'];
                    $description = $room['description'];
                    $post_title_name = get_the_title($original_post_id) .' - ' .$name;
                    $variation_id = wp_insert_post(array(
                        'post_title' => $post_title_name,
                        'post_name' => 'auto-draft-'.$name,
                        'post_status' => 'publish',
                        'post_type' => 'product_variation',
                        'post_parent' => $product_id,
                        'post_content' => $name,
                    ));
                    update_post_meta($variation_id, 'attribute_type-of-rooms', $name);
                    update_post_meta($variation_id, '_price', $price);
                    update_post_meta($variation_id, '_regular_price', $price);
                    update_post_meta($variation_id, '_manage_stock', 'no');
                    update_post_meta($variation_id, '_description', $description);
                }
            }

            if(count($typeOfRooms) < count($variations)){
                foreach(array_slice($variations,count($typeOfRooms)) as $key => $value){
                    wp_delete_post($value, true);
                }
            }
        }else{
            foreach($typeOfRooms as $key => $room){
                $name = $room['name'];
                $type_of_rooms_values[] = $name;
                $price = $room['price'];
                $description = $room['description'];
                $post_title_name = get_the_title($original_post_id) .' - ' .$name;
                $variation_id = wp_insert_post(array(
                    'post_title' => $post_title_name,
                    'post_name' => 'auto-draft-'.$name,
                    'post_status' => 'publish',
                    'post_type' => 'product_variation',
                    'post_parent' => $product_id,
                    'post_content' => $name,
                ));
                update_post_meta($variation_id, 'attribute_type-of-rooms', $name);
                update_post_meta($variation_id, '_price', $price);
                update_post_meta($variation_id, '_regular_price', $price);
                update_post_meta($variation_id, '_manage_stock', 'no');
                update_post_meta($variation_id, '_description', $description);
            }
        }
        $product_attributes = array(
            'type-of-rooms' => array(
                'name' => 'type of rooms',
                'value' => implode(' | ', $type_of_rooms_values),
                'position' => 0,
                'is_visible' => 1,
                'is_variation' => 1,
                'is_taxonomy' => 0
            )
        );
        update_post_meta($product_id, '_product_attributes', $product_attributes);
    }else{
        if(!empty($variations)){
            foreach($variations as $key => $value){
                wp_delete_post($value, true);
            }
        }
        // update_post_meta($product_id, '_product_attributes', '');
        delete_post_meta( $product_id, '_product_attributes', '' );
    }
}

// post name exists
function post_name_exists($post_name) {
    global $wpdb;

    // Query to check if the post_name already exists in the database
    $query = $wpdb->prepare("SELECT post_name FROM $wpdb->posts WHERE post_name = %s LIMIT 1", $post_name);
    $result = $wpdb->get_var($query);

    return $result ? true : false;
}

// Automatically create WooCommerce product from Hotels
function auto_create_woocommerce_product_from_hotels($original_post_id, $original_post) {
    if ($original_post->post_type == 'hotel' && !defined('DOING_AUTOSAVE') && 'publish' === get_post_status($original_post)) {
		remove_action( 'save_post', __FUNCTION__ );

        $type = 'product';
        $price = get_post_meta($original_post_id, 'price', true);
        $thumbnail_image = get_post_meta($original_post_id, '_thumbnail_id', true);
        $rooms  = get_post_meta($original_post_id, 'rooms', true);
        $address  = get_post_meta($original_post_id, 'address', true);
        $gallery  = get_post_meta($original_post_id, 'gallery', true);
        $maximum_guest_per_room  = get_post_meta($original_post_id, 'maximum_guest_per_room', true);
        $country_code = country_code_phn($address);
        $tax_class = ($country_code === 'fr') ? 'hotel' : 'zero-rate';
        $tax  = get_post_meta($original_post_id, 'taxes', true);
        if(!empty($tax)){
            $tax_class = $tax;
        }
        
        $typeOfRooms = get_field('add_type_of_room', $original_post_id);

        if($gallery){
            $gallery = implode(",", $gallery);
        }

        $product_id = get_post_meta($original_post_id, 'product_of_hotels', true);
        if($product_id){
            wp_update_post(array(
                'ID' => $product_id,
                'post_title' => $original_post->post_title,
                'post_content' => $original_post->post_content,
            ));
        }else{
            $original_post_name = $original_post->post_name;
            $counter = 1;
            while (post_name_exists($new_post_name)) {
                $new_post_name = $original_post_name . '-' . $counter;
                $counter++;
            }
            $product_id = wp_insert_post(array(
                'post_title' => $original_post->post_title,
                'post_name' => $new_post_name,
                'post_status' => 'publish',
                'post_type' => $type,
                'post_content' => $original_post->post_content,
            ));
        }

        if ($product_id) {
            update_post_meta($original_post_id, 'product_of_hotels', $product_id);
            update_field('field_660381310b680', $address, $product_id);
            wp_set_object_terms($product_id, 'simple', 'product_type');      
            if(!empty($typeOfRooms)){
                wp_set_object_terms($product_id, 'variable', 'product_type');      
            }else{
                wp_set_object_terms($product_id, 'simple', 'product_type');      
            }
            
            $meta_data = array(
                '_tax_status' => 'taxable',
                '_tax_class' => $tax_class,
                '_regular_price' => $price,
                '_price' => $price,
                '_stock_status' => 'instock',
                '_manage_stock' => 'no',
                'phn_type_product' => 'hotel',
                '_stock' => $rooms,
                '_thumbnail_id' => $thumbnail_image,
                'hotels_of_product' => $original_post_id,
                '_product_image_gallery' => $gallery,
                'maximum_guest_per_room' => $maximum_guest_per_room
            );

            foreach($meta_data as $key => $value){
                update_post_meta($product_id, $key, $value);
            }

            create_update_variations_product($original_post_id,$product_id);
        }
    }
}
add_action('save_post', 'auto_create_woocommerce_product_from_hotels', 20, 2);

// Delete associated WooCommerce product when a 'hotel' post is trashed
function delete_associated_product_on_hotel_trash($post_id = '') {
    // Retrieve post IDs from $_GET if available, or use provided $post_id
    $post_ids = is_array($_GET['post']) ? $_GET['post'] : array($post_id);
    foreach ($post_ids as $id) {
        // Check if the post type is 'hotel'
        if (get_post_type($id) === 'hotel') {
            // Retrieve associated product ID
            $product_id = get_post_meta($id, 'product_of_hotels', true);
            // Check if product exists and is of type 'product'
            if ($product_id && get_post_type($product_id) === 'product') {
                // Delete associated product
                wp_delete_post($product_id, true);
            }
        }
    }
}
add_action('wp_trash_post', 'delete_associated_product_on_hotel_trash');

// Get event first hotel
function get_event_first_hotel($post_id){
    $args = array(
        'post_type'      => 'tribe_events',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
    );
    $event_id = 0;
    $query = new WP_Query( $args );
    if ( $query->have_posts() ){
        while ( $query->have_posts() ){
            $query->the_post();
            $hotels = get_field('hotels');
            $hotel_type_of_room = get_post_meta(get_the_ID(),'hotel_type-of-rooms');
            $hotels = check_event_hotel($hotel_type_of_room);
            // echo"<pre>";
            // var_dump($hotel_type_of_room);
            if(in_array($post_id,$hotels)){
                $event_id = get_the_ID();
                break;
            }
        }
    }
	wp_reset_postdata();
    return $event_id;
}

function check_event_hotel($hotel_type_of_room){
    $event_array = [];
    if($hotel_type_of_room){
        foreach($hotel_type_of_room as $value){
            $event_array[] = $value[0]['hotelId'];
        }
    }
    return $event_array;
}

// Get array id event of hotel
function get_array_id_event_of_hotel($post_id){
    $args = array(
        'post_type'      => 'tribe_events',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
    );
    $event_id = [];
    $query = new WP_Query( $args );
    if ( $query->have_posts() ){
        while ( $query->have_posts() ){
            $query->the_post();
            $hotels = get_field('hotels');
            if(!empty($hotels)){
                if(in_array($post_id,$hotels)){
                    $event_id[] = get_the_ID();
                }
            } 
        }
    }
	wp_reset_postdata();
    return $event_id;
}

// Check item ticket in cart
function ticket_in_cart(){
    $status = false;
    $cart = WC()->cart;
    $cart_items = $cart->get_cart();
    foreach ( $cart_items as $cart_item_key => $cart_item ) {
        $product_id = $cart_item['product_id'];
        $type = get_post_meta( $product_id, 'phn_type_product', true );
        if($type === "event"){
            $status = true;
            break;
        }
    }
    return $status;
}

// Check event of hotel in cart
function check_event_of_hotel_in_cart($post_id){
    $status = false;
    $cart = WC()->cart;
    $cart_items = $cart->get_cart();
    foreach ( $cart_items as $cart_item_key => $cart_item ) {
        $product_id = $cart_item['product_id'];
        $type = get_post_meta( $product_id, 'phn_type_product', true );
        if($type === "event"){
            $array_id_event = get_array_id_event_of_hotel($post_id);
            $event_id = get_post_meta($product_id, 'events_of_product', true);
            if(in_array($event_id,$array_id_event)){
                $status = true;
            }else{
                $status = false;
            }
        }
    }
    return $status;
}

// Function to generate HTML stars based on rating
function generateStars($rating) {
    $fullStars = floor($rating); // Number of full stars
    $halfStar = $rating - $fullStars; // Check if we need to add a half star
    $emptyStars = 5 - ceil($rating); // Remaining empty stars

    $stars = '';
    // Full stars
    for ($i = 1; $i <= $fullStars; $i++) {
        $stars .= '<i class="fas fa-star"></i>'; // Full star
    }
    // Half star
    if ($halfStar >= 0.5) {
        $stars .= '<i class="fad fa-star-half-alt"></i>'; // Half star
    }
    // Empty stars
    // for ($i = 1; $i <= $emptyStars; $i++) {
    //     $stars .= '<i class="fal fa-star"></i>'; // Empty star
    // }
    return $stars;
}
?>