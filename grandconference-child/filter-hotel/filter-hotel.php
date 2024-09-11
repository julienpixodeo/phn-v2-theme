<?php
// haversine distance
function haversine_distance($lat1, $lng1, $lat2, $lng2) {
    $earth_radius = 6371; // Earth radius in kilometers

    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);

    $a = sin($dLat / 2) * sin($dLat / 2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLng / 2) * sin($dLng / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $distance = $earth_radius * $c;

    return $distance;
}

// get posts within distance
function get_posts_within_distance($lat, $lng, $distance) {
    global $wpdb;

    $posts = $wpdb->get_results("
        SELECT p.ID, p.post_title, pm1.meta_value AS lat, pm2.meta_value AS lng
        FROM {$wpdb->posts} p
        JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_latitude'
        JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_longitude'
        WHERE p.post_status = 'publish'
    ");

    $filtered_posts = [];

    foreach ($posts as $post) {
        $post_distance = haversine_distance($lat, $lng, $post->lat, $post->lng);
        if ($post_distance <= $distance) {
            $filtered_posts[] = $post;
        }
    }

    return $filtered_posts;
}

// filter hotel
function filter_hotel() {
    ob_start();
    // Get and sanitize input data
    $min_price = (int) $_POST['min_price'];
    $max_price = (int) $_POST['max_price'];
    $min_distance = (int) $_POST['min_distance'];
    $max_distance = (int) $_POST['max_distance'];
    $event_id = (int) $_POST['event_id'];
    $star = isset($_POST['star']) ? (int) $_POST['star'] : null;

    // Get event data
    $data_hotel = get_post_meta($event_id, 'data_hotel_event', true);
    $lan = get_field('language', $event_id);
    $from = ($lan === 'french') ? 'Prix' : 'Price';
    $location = get_post_meta($event_id, 'location', true);

    // Check if location exists
    if (empty($location)) {
        wp_send_json(['html' => '', 'count' => 0, 'locations' => []]);
        return;
    }

    $lat = $location['lat'];
    $lng = $location['lng'];
    $hotels = [];
    $locations = [];

    // Process hotel data
    if ($data_hotel) {
        foreach ($data_hotel as $data) {
            $hotel_id = $data['hotel_id'];
            $address = get_field('address', $hotel_id);
            $hotel_stars = get_field('hotel_stars', $hotel_id) ?: 5;

            if (!empty($address)) {
                $lat_post = $address['lat'];
                $lng_post = $address['lng'];
                $post_distance = haversine_distance($lat, $lng, $lat_post, $lng_post);
                
                // Skip hotels that don't meet distance criteria
                if ($post_distance > $max_distance || $post_distance < $min_distance) {
                    continue;
                }
            }

            // Get minimum price from variations
            $price_array = array_column($data['variations_data'], 'price');
            $minPrice = !empty($price_array) ? min($price_array) : 0;

            // Filter based on price and star rating
            if ($minPrice >= $min_price && $minPrice <= $max_price && (!$star || $hotel_stars == $star)) {
                $featured_img_url = get_the_post_thumbnail_url($hotel_id, 'full') 
                    ?: wp_get_attachment_image_url(get_field('gallery', $hotel_id)[0], 'full') 
                    ?: home_url('/wp-content/uploads/woocommerce-placeholder.png');
                $url_de_hotel = get_field('url_de_hotel', $hotel_id);
                $url = $url_de_hotel ?: get_permalink($hotel_id) . $event_id;
                $attr = $url_de_hotel ? 'target="_blank"' : '';

                $gallery = get_field('gallery', $hotel_id);

                $gallery_images_urls = array();

                if ($featured_img_url) {
                    $gallery_images_urls[] = $featured_img_url; 
                }

                if ($gallery) {
                    foreach ($gallery as $image_id) {
                        $gallery_images_urls[] = wp_get_attachment_image_url($image_id, 'full');
                    }
                } 

                if (!$featured_img_url && empty($gallery)) {
                    $gallery_images_urls[] = home_url('/wp-content/uploads/woocommerce-placeholder.png');
                }

                // Add hotel to results
                $hotels[] = [
                    'hotel_id' => $hotel_id,
                    'featured_img_url' => $featured_img_url,
                    'address' => $address,
                    'hotel_stars' => $hotel_stars,
                    'url' => $url,
                    'attr' => $attr,
                    'minPrice' => $minPrice,
                ];

                if (!empty($address)) {
                    $locations[] = [
                        'lat' => $address['lat'],
                        'lng' => $address['lng'],
                        'gallery_images' => $gallery_images_urls,
                        'title' => get_the_title($hotel_id),
                        'address' => $address['address'],
                        'minPrice' => $from . " " . $minPrice . get_woocommerce_currency_symbol(get_option('woocommerce_currency')),
                        'price' => $minPrice . " €",
                        'url' => $url,
                        'hotel_stars' => $hotel_stars,
                    ];
                }
            }
        }
    }

    if(!empty($hotels)){
        // Sort hotels by price
        usort($hotels, fn($a, $b) => $a['minPrice'] - $b['minPrice']);

        // Display hotels
        foreach ($hotels as $hotel) {
            ?>
            <div class="item-hotels">
                <a href="<?= esc_url($hotel['url']) ?>" <?= esc_attr($hotel['attr']) ?>>
                    <img src="<?= esc_url($hotel['featured_img_url']); ?>" alt="" class="thumbnail">
                </a>
                <div class="wrap-title-rating">
                    <a href="<?= esc_url($hotel['url']) ?>" <?= esc_attr($hotel['attr']) ?> class="title"><?= esc_html(get_the_title($hotel['hotel_id'])); ?></a>
                    <div class="review-hotel">
                        <div class="list-star">
                            <?= generateStars($hotel['hotel_stars']); ?>
                        </div>
                    </div>
                </div>
                <div class="infor">
                    <?php if (!empty($hotel['address']['address'])) { ?>
                        <span><?= esc_html($hotel['address']['address']); ?></span>
                    <?php } ?>
                </div>
                <?php if ($hotel['minPrice'] > 0) { ?>
                    <h3 class="price">
                        <?= esc_html($from) . " " . esc_html($hotel['minPrice']) . get_woocommerce_currency_symbol(get_option('woocommerce_currency')); ?>
                    </h3>
                <?php } ?>
            </div>
            <?php
        }
        $status = true;
        // Capture and return HTML output
        $html = ob_get_clean();
    }else{
        $status = false;
        $html = "Aucun résultat";
    }
    
    wp_send_json(['html' => $html, 'count' => count($hotels), 'locations' => $locations, 'status' => $status]);
}
add_action('wp_ajax_filter_hotel', 'filter_hotel');
add_action('wp_ajax_nopriv_filter_hotel', 'filter_hotel');