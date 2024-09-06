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

$cost = tribe_get_formatted_cost($event_id);
$EventCost = get_field('_EventCost',$event_id);
$lan = get_field('language',$event_id);

?>

<p class="tribe-events-back">
    <a href="<?php echo esc_url(tribe_get_events_link()); ?>"><?php printf('&laquo; ' . esc_html_x('All %s', '%s Events plural label', 'the-events-calendar'), $events_label_plural); ?></a>
</p>

<!-- Notices -->
<?php tribe_the_notices() ?>

<div class="top-event">
    <div class="top-event-left">
        <div class="wrap-title-image">
            <?php echo tribe_event_featured_image($event_id, 'full', false); ?>
            <?php echo $title; ?>
        </div>
        <div class="tribe-events-schedule tribe-clearfix">
            <?php 
                // if($lan === 'french'){
                //     echo replace_month_names(tribe_events_event_schedule_details($event_id, '<h2>', '</h2>')); 
                // }else{
                //     echo tribe_events_event_schedule_details($event_id, '<h2>', '</h2>'); 
                // }
                if ($lan === 'french') {
                    echo replace_month_names(custom_tribe_events_event_schedule_details($event_id, '<h2>', '</h2>'));
                } else {
                    echo custom_tribe_events_event_schedule_details($event_id, '<h2>', '</h2>');
                }
            ?>
            <?php if (!empty($EventCost) && $EventCost != 0): ?>
                <span class="tribe-events-cost"><?php echo esc_html($cost) ?></span>
            <?php endif; ?>
        </div>
    </div>
    <div class="top-event-right">
        <?php do_action('tribe_events_single_event_after_the_content') ?>
    </div>
</div>

<!-- Event header -->
<div id="tribe-events-header" <?php tribe_events_the_header_attributes() ?>>
    <!-- Navigation -->
    <nav class="tribe-events-nav-pagination" aria-label="<?php printf(esc_html__('%s Navigation', 'the-events-calendar'), $events_label_singular); ?>">
        <ul class="tribe-events-sub-nav">
            <li class="tribe-events-nav-previous"><?php tribe_the_prev_event_link('<span>&laquo;</span> %title%') ?></li>
            <li class="tribe-events-nav-next"><?php tribe_the_next_event_link('%title% <span>&raquo;</span>') ?></li>
        </ul>
        <!-- .tribe-events-sub-nav -->
    </nav>
</div>
<!-- #tribe-events-header -->

<?php while (have_posts()) :  the_post(); ?>
    <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <!-- Added by ThemeGoods -->
        <div class="grandconference-tribe-events-content">
            <!-- Event featured image, but exclude link -->

            <!-- Event content -->
            <?php do_action('tribe_events_single_event_before_the_content') ?>
            <!-- <div class="tribe-events-single-event-description tribe-events-content"> -->
            <?php
            // the_content(); 
            ?>
            <!-- </div> -->
            <!-- .tribe-events-single-event-description -->
        </div>
        <div class="grandconference-tribe-events-meta">
            <!-- Event meta -->
            <?php do_action('tribe_events_single_event_before_the_meta') ?>
            <?php tribe_get_template_part('modules/meta'); ?>
            <?php do_action('tribe_events_single_event_after_the_meta') ?>
        </div>

        <div class="tribe-events-single-event-description tribe-events-content">
            <?php the_content(); ?>
        </div>
    </div> <!-- #post-x -->
    <?php if (get_post_type() == Tribe__Events__Main::POSTTYPE && tribe_get_option('showComments', false)) comments_template() ?>
<?php endwhile; ?>
<?php 
// echo footer_elementor();
// get_footer(); s
?>