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
$heading_partners = ($lan === 'french') ? get_field('heading_partners_fr','option') : get_field('heading_partners','option');
$partners = get_field('partners',$event_id);
?>
<div class="top-heading mgt0">
    <h2 class="sub-title"><?php echo $heading_partners; ?></h2>
</div>
<?php if($partners): ?>
<div class="wrap-list-partners">
<?php echo partners_event_elementor($partners); ?>
</div>
<?php endif; ?>