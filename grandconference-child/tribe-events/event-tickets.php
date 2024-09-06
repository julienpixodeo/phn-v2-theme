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
$lan = get_field('language',$event_id);
$cost = tribe_get_formatted_cost($event_id);
$price = get_post_meta($event_id, '_EventCost', true);
$book = ($lan === 'french')? get_field('text_book_fr','option') : get_field('text_book','option');
$product_id = get_post_meta($event_id, 'product_of_events', true);
$thumbnail_id = get_post_meta($event_id, '_thumbnail_id', true);
$thumbnail_url = wp_get_attachment_image_url($thumbnail_id,'full'); 
$thumbnail_title = get_the_title($thumbnail_id);
$max_tickets = get_post_meta($product_id, '_stock', true);
$tickets_are_sold_out = get_field('tickets_are_sold_out','option');
if(!$thumbnail_url){
    $thumbnail_url = home_url('/wp-content/uploads/woocommerce-placeholder.png');
}
$lan = get_field('language',$event_id);
$text_free = ($lan === 'french') ? get_field('text_free_fr','option') : get_field('text_free','option');
?>
<div class="woocommerce-notices-wrapper"></div>
<div class="wrap-top-tickets">
    <div class="image-event">
        <div class="tribe-events-event-image">
            <a data-fancybox href="<?php echo $thumbnail_url; ?>" data-caption="<?php echo $thumbnail_title; ?>">
                <img src="<?php echo $thumbnail_url; ?>" width="200" height="150" alt="" class="attachment-full size-full wp-post-image"/>
            </a>
        </div>
    </div>
    <div class="content-event">
        <?php echo $title; ?>
        <div class="tribe-events-schedule tribe-clearfix tribe-events-schedule-mg0">
            <?php 
                if($lan === 'french'){
                    $date_event = replace_month_names(tribe_events_event_schedule_details($event_id, '<h2>', '</h2>'));
                    $date_event = html_entity_decode(strip_tags($date_event));
                    echo "<h2>".swapFirstTwoWords($date_event)."</h2>";
                }else{
                    echo tribe_events_event_schedule_details($event_id, '<h2>', '</h2>'); 
                }
            ?>
        </div>
        <?php 
            if (!empty($price)) : 
                echo wc_price($price);
            endif; 
            
            $ticket_description = get_field('ticket_description',$event_id);
            if($ticket_description){
                echo "<div class='ticket-description'>".$ticket_description."</div>";
            }
        ?>
        <?php if($max_tickets != 0): ?>
            <form action="" class="form-add-cart-tickets">
                <div class="quantity buttons_added qty-js">
                    <a href="javascript:void(0)" id="minus_qty" class="minus">-</a>
                    <input type="number" id="" class="input-text qty text" name="quantity" value="1" aria-label="Product quantity" size="4" min="1" max="<?php echo $max_tickets; ?>" step="1" placeholder="" inputmode="numeric" autocomplete="off">
                    <a href="javascript:void(0)" id="plus_qty" class="plus">+</a>
                </div>
                <input type="hidden" class="event_id" value="<?php echo $event_id; ?>">
                <?php
                    if($price === "0"){
                        echo "<span style='display: block;margin-top:5px'>".$text_free."</span>";    
                    }
                ?>
                <button type="submit" name="add-to-cart" value="<?php echo $product_id; ?>" class="single_add_to_cart_button button alt">
                    <?php echo $book; ?>
                </button>
            </form>
        <?php else: ?>
            <p class="out-of-stock-tickets"><?php echo $tickets_are_sold_out; ?></p>
        <?php endif;  ?>
    </div>
</div>