<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$event_id = Tribe__Events__Main::postIdHelper(get_the_ID());
$event_id = apply_filters('tec_events_single_event_id', $event_id);

$title_classes = apply_filters('tribe_events_single_event_title_classes', ['tribe-events-single-event-title'], $event_id);
$title_classes = implode(' ', tribe_get_classes($title_classes));

$before = apply_filters('tribe_events_single_event_title_html_before', '<h1 class="' . $title_classes . '">');
$after = apply_filters('tribe_events_single_event_title_html_after', '</h1>');

$title = apply_filters('tribe_events_single_event_title_html', get_the_title(), $event_id);
$cost = tribe_get_formatted_cost($event_id);
$lan = get_field('language',$event_id);

$text_menu_evh = ($lan === 'french')? get_field('text_menu_evh_fr', 'option') : get_field('text_menu_evh', 'option');
$text_menu_evt = ($lan === 'french')? get_field('text_menu_evt_fr', 'option') :  get_field('text_menu_evt', 'option');
$text_menu_evp = ($lan === 'french')? get_field('text_menu_evp_fr', 'option') :  get_field('text_menu_evp', 'option');
$text_menu_evhotel = ($lan === 'french')? get_field('text_menu_evhotel_fr', 'option') :  get_field('text_menu_evhotel', 'option');
$text_menu_evpartners = ($lan === 'french')? get_field('text_menu_evpartners_fr', 'option') :  get_field('text_menu_evpartners', 'option');

$link = get_the_permalink($event_id);

$class = $class_tickets = $class_planning = $class_hotels = $class_partners = '';
if (!get_query_var('tickets') && !get_query_var('planning') && !get_query_var('hotels') && !get_query_var('partners')) {
    $class = 'active';
} else {
    if (get_query_var('tickets')) {
        $class_tickets = 'active';
    }
    if (get_query_var('planning')) {
        $class_planning = 'active';
    }
    if (get_query_var('hotels')) {
        $class_hotels = 'active';
    }
    if (get_query_var('partners')) {
        $class_partners = 'active';
    }
}

$hide_ticket = get_field('hide_ticket',$event_id);
$hide_book_your_hotel = get_field('hide_book_your_hotel',$event_id);

?>

<div id="tribe-events-content" class="tribe-events-single">

    <ul class="menu-single-event">
        <li>
            <a href="<?php echo esc_url($link); ?>" class="themelink <?php echo esc_attr($class); ?>"><?php echo esc_html($text_menu_evh); ?></a>
        </li>
        <?php
            if(!$hide_ticket || empty($hide_ticket)){
                ?>
                <li>
                    <a href="<?php echo esc_url($link . 'tickets'); ?>" class="themelink <?php echo esc_attr($class_tickets); ?>"><?php echo esc_html($text_menu_evt); ?></a>
                </li>
                <?php
            }
        ?>
        <li>
            <a href="<?php echo esc_url($link . 'planning'); ?>" class="themelink <?php echo esc_attr($class_planning); ?>"><?php echo esc_html($text_menu_evp); ?></a>
        </li>
        <?php
            if(!$hide_book_your_hotel || empty($hide_book_your_hotel)){
                ?>
                <li>
                    <a href="<?php echo esc_url($link . 'hotels'); ?>" class="themelink <?php echo esc_attr($class_hotels); ?>"><?php echo esc_html($text_menu_evhotel); ?></a>
                </li>
                <?php
            }
        ?>
        <li>
            <a href="<?php echo esc_url($link . 'partners'); ?>" class="themelink <?php echo esc_attr($class_partners); ?>"><?php echo esc_html($text_menu_evpartners); ?></a>
        </li>
        <?php cart_icon_phn(); ?>
    </ul>

    <?php
    if (!get_query_var('tickets') && !get_query_var('planning') && !get_query_var('hotels') && !get_query_var('partners')) {
        get_template_part('tribe-events/event', 'home');
    } else {
        if (get_query_var('tickets')) {
            get_template_part('tribe-events/event', 'tickets');
        }
        if (get_query_var('planning')) {
            get_template_part('tribe-events/event', 'planning');
        }
        if (get_query_var('partners')) {
            get_template_part('tribe-events/event', 'partners');
        }
        // if (get_query_var('hotels')) {
        //     get_template_part('tribe-events/event', 'hotels');
        // }
    }
    ?>

</div><!-- #tribe-events-content -->