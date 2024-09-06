<?php
if (!defined('ABSPATH')) {
    die('-1');
}

$events_label_singular = tribe_get_event_label_singular();
$events_label_plural = tribe_get_event_label_plural();

$event_id = Tribe__Events__Main::postIdHelper(get_the_ID());

// Allow filtering of the event ID
$event_id = apply_filters('tec_events_single_event_id', $event_id);

// Allow filtering of the single event template title classes
$title_classes = apply_filters('tribe_events_single_event_title_classes', ['tribe-events-single-event-title'], $event_id);
$title_classes = implode(' ', tribe_get_classes($title_classes));

// Allow filtering of the single event template title before HTML
$before = apply_filters('tribe_events_single_event_title_html_before', '<h1 class="' . $title_classes . '">');

// Allow filtering of the single event template title after HTML
$after = apply_filters('tribe_events_single_event_title_html_after', '</h1>');

// Allow filtering of the single event template title HTML
$title = apply_filters('tribe_events_single_event_title_html', the_title($before, $after, false), $event_id);
$lan = get_field('language', $event_id);
$cost = tribe_get_formatted_cost($event_id);
$location = get_post_meta($event_id, 'location', true);
$title_location = ($lan === 'french') ? get_field('locations_evhotel_fr', 'option') : get_field('locations_evhotel', 'option');
$get_maps_direction = ($lan === 'french') ? get_field('get_maps_direction_fr', 'option') : get_field('get_maps_direction', 'option');
$partnering_hotels = ($lan === 'french') ? get_field('partnering_hotels_fr', 'option') : get_field('partnering_hotels', 'option');
$sub_partnering_hotels = ($lan === 'french') ? get_field('sub_partnering_hotels_fr', 'option') : get_field('sub_partnering_hotels', 'option');
// $hotels = get_post_meta($event_id, 'hotels', true);
$list_event = get_post_meta($event_id, 'hotel_type-of-rooms', true);
$hotels = array();
$data_hotel = get_post_meta($event_id, 'data_hotel_event', true);

if ($list_event) {
    foreach ($list_event as $event) {
        $hotels[] = $event['hotelId'];
    }
}

if ($data_hotel) {
    foreach ($data_hotel as $data) {
        $hotels_data[] = $data['hotel_id'];

    }
}

$from = ($lan === 'french') ? 'Prix' : 'Price';
?>
<div class="container-hotel-list-wrap">
    <div class="top-heading">
        <h2 class="sub-title">
            <?php echo $partnering_hotels; ?>
        </h2>
        <div class="description-sub">
            <?php echo $sub_partnering_hotels; ?>
        </div>
    </div>
    <div class="filter-hotel-wrap">
        <form action="" class="filter-hotel">
            <div class="wrap-item-filter">
                <button class="show-filter">Price<i class="fal fa-angle-down"></i></button>
                <div class="drop-filter">
                    Test
                </div>
            </div>

            <div class="wrap-item-filter">
                <button class="show-filter">Stars<i class="fal fa-angle-down"></i></button>
                <div class="drop-filter">
                    Test Stars
                </div>
            </div>

            <div class="wrap-item-filter">
                <button class="show-filter">Distance <i class="fal fa-angle-down"></i></button>
                <div class="drop-filter">
                    Test distance 
                </div>
            </div>
        </form>
        <div class="show-hide-map">

        </div>
    </div>
    <div class="wrap-list-hotel">
        <div class="list-hotels-event">
            <?php
            if ($data_hotel) {
                // Step 1: Collect all the hotel data with the minimum price
                $hotels = array();

                foreach ($data_hotel as $data) {
                    $hotel_id = $data['hotel_id'];
                    $featured_img_url = get_the_post_thumbnail_url($hotel_id, 'full');
                    $address = get_field('address', $hotel_id);
                    $price = get_field('price', $hotel_id);
                    $hotel_stars = (!empty(get_field('hotel_stars', $hotel_id))) ? get_field('hotel_stars', $hotel_id) : 5;
                    $url_de_hotel = get_field('url_de_hotel', $hotel_id);

                    if ($url_de_hotel) {
                        $url = $url_de_hotel;
                        $attr = 'target="_blank"';
                    } else {
                        $url = get_permalink($hotel_id) . $event_id;
                        $attr = '';
                    }

                    if (!$featured_img_url) {
                        $gallery = get_field('gallery', $hotel_id);
                        if ($gallery) {
                            $featured_img_url = wp_get_attachment_image_url($gallery[0], 'full');
                        } else {
                            $featured_img_url = home_url('/wp-content/uploads/woocommerce-placeholder.png');
                        }
                    }

                    $rooms_hotel = get_field('rooms', $hotel_id);
                    $list_event = $data['variations_data'];
                    $price_array = array();

                    if ($list_event) {
                        foreach ($list_event as $event) {
                            if (isset($event['price'])) {
                                $price_array[] = $event['price'];
                            }
                        }
                    }

                    $minPrice = !empty($price_array) ? min($price_array) : 0;

                    $hotels[] = array(
                        'hotel_id' => $hotel_id,
                        'featured_img_url' => $featured_img_url,
                        'address' => $address,
                        'hotel_stars' => $hotel_stars,
                        'url' => $url,
                        'attr' => $attr,
                        'minPrice' => $minPrice,
                    );
                }

                // Step 2: Sort the collected data by the minimum price
                usort($hotels, function ($a, $b) {
                    return $a['minPrice'] - $b['minPrice'];
                });

                // Step 3: Display the sorted data
                foreach ($hotels as $hotel) {
                    ?>
                    <div class="item-hotels">
                        <a href="<?= $hotel['url'] ?>" <?= $hotel['attr'] ?>>
                            <img src="<?= $hotel['featured_img_url']; ?>" alt="" class="thumbnail">
                        </a>
                        <div class="wrap-title-rating">
                            <a href="<?= $hotel['url'] ?>" <?= $hotel['attr'] ?>
                                class="title"><?= get_the_title($hotel['hotel_id']); ?></a>
                            <div class="review-hotel">
                                <!-- <span class="rate"><?php echo number_format($hotel['hotel_stars'], 1); ?></span> -->
                                <div class="list-star">
                                    <?php echo generateStars($hotel['hotel_stars']); ?>
                                </div>
                            </div>
                        </div>
                        <div class="infor">
                            <?php
                            if (!empty($hotel['address']['address']) && !empty($hotel['address'])) {
                                ?>
                                <span><?= $hotel['address']['address']; ?></span>
                                <?php
                            }
                            ?>
                        </div>
                        <?php
                        if ($hotel['minPrice'] > 0) {
                            ?>
                            <h3 class="price">
                                <?php echo $from . " " . $hotel['minPrice'] . get_woocommerce_currency_symbol(get_option('woocommerce_currency')); ?>
                            </h3>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
        <div class="list-hotel-map-wrap">
            <div id="list-hotel-map"></div>
        </div>
    </div>
    
</div>
<?php
if (!empty($location)) {
    ?>
    <script>
        jQuery(document).ready(function ($) {
            function initMap() {
                var myLatLng = { lat: <?php echo $location['lat']; ?>, lng: <?php echo $location['lng']; ?>};
                var map = new google.maps.Map(document.getElementById('list-hotel-map'), {
                    zoom: 16,
                    disableDefaultUI: true,
                    center: myLatLng
                });
                var marker = new google.maps.Marker({
                    position: myLatLng,
                    map: map,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        fillColor: '#ff2d55',
                        fillOpacity: 0.5,
                        scale: 50,
                        strokeColor: 'transparent',
                        strokeWeight: 0,
                    },
                });
            }
            initMap();

            $('body').on('click','.show-filter',function(event){
                event.preventDefault();
                $(this).closest('.wrap-item-filter').find('.drop-filter').slideToggle(100);
            })
        });
    </script>
<?php }
