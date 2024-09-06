<?php
/**
 * Single Event Template
 * A single event. This displays the event title, description, meta, and
 * optionally, the Google map for the event.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/single-event.php
 *
 * @package TribeEventsCalendar
 * @version 4.6.19
 *
 */

if (!defined('ABSPATH')) {
    die('-1');
}

$events_label_singular = tribe_get_event_label_singular();
$events_label_plural   = tribe_get_event_label_plural();

$event_id = Tribe__Events__Main::postIdHelper(get_the_ID());

/**
 * Allows filtering of the event ID.
 *
 * @since 6.0.1
 *
 * @param int $event_id
 */
$event_id = apply_filters('tec_events_single_event_id', $event_id);

/**
 * Allows filtering of the single event template title classes.
 *
 * @since 5.8.0
 *
 * @param array  $title_classes List of classes to create the class string from.
 * @param string $event_id The ID of the displayed event.
 */
$title_classes = apply_filters('tribe_events_single_event_title_classes', ['tribe-events-single-event-title'], $event_id);
$title_classes = implode(' ', tribe_get_classes($title_classes));

/**
 * Allows filtering of the single event template title before HTML.
 *
 * @since 5.8.0
 *
 * @param string $before HTML string to display before the title text.
 * @param string $event_id The ID of the displayed event.
 */
$before = apply_filters('tribe_events_single_event_title_html_before', '<h1 class="' . $title_classes . '">', $event_id);

/**
 * Allows filtering of the single event template title after HTML.
 *
 * @since 5.8.0
 *
 * @param string $after HTML string to display after the title text.
 * @param string $event_id The ID of the displayed event.
 */
$after = apply_filters('tribe_events_single_event_title_html_after', '</h1>', $event_id);

/**
 * Allows filtering of the single event template title HTML.
 *
 * @since 5.8.0
 *
 * @param string $after HTML string to display. Return an empty string to not display the title.
 * @param string $event_id The ID of the displayed event.
 */
$title = apply_filters('tribe_events_single_event_title_html', the_title($before, $after, false), $event_id);
$cost  = tribe_get_formatted_cost($event_id);
$planning = get_field('planning', $event_id);
$lan = get_field('language',$event_id);
$by = ($lan === 'french') ? "Par" : "By";
$where = ($lan === 'french') ? "LOCALISATION" : "Where";
?>
<?php if (!empty($planning)): ?>
    <div class="wrap-scheduleday">
        <ul class="scheduleday_wrapper tab">
            <?php foreach ($planning as $key => $value): ?>
                <?php $class = ($key == 0) ? 'active' : ''; ?>
                <li data-tab="day-<?php echo $key + 1; ?>" class="scheduleday_title <?php echo $class; ?>">
                    <div class="scheduleday_title_content">
                        <h4 style=""><?php echo $value['title_day']; ?></h4>
                    </div>
                    <br class="clear">
                </li>
            <?php endforeach; ?>
        </ul>
        
        <?php foreach ($planning as $key => $value): ?>
            <?php $class = ($key != 0) ? 'hide' : ''; ?>
            <ul id="day-<?php echo $key + 1; ?>" class="scheduleday_wrapper themeborder tab_content <?php echo $class; ?>">
                <?php if (!empty($value['planning_day'])): ?>
                    <?php foreach ($value['planning_day'] as $k => $v): $avatar = (!empty($v['author_avatar'])) ? $v['author_avatar'] : 'https://secure.gravatar.com/avatar/c888ab8fdff3ed4194cebf46e62e0991?s=50&d=mm&r=g'  ?>
                        <li class="themeborder business engineering growth platform">
                            <div class="session_content_wrapper expandable">
                                <div class="session_speaker_thumb">
                                    <img decoding="async" src="<?php echo $avatar; ?>" alt="">
                                </div>
                                <div class="session_content has_speaker_thumb">
                                    <div class="session_start_time"><?php echo $v['time']; ?></div>
                                    <div class="session_title">
                                        <h6><?php echo $v['title']; ?></h6>
                                    </div>
                                    <div class="session_speakers">
                                        <strong><a href=""><?php echo $v['author_name']; ?></a></strong> 
                                        <?php echo $v['author_position']; ?>
                                        <i class="fas fa-chevron-down session_speakers_action"></i>
                                        <i class="fas fa-chevron-up session_speakers_action"></i>
                                    </div>
                                </div>
                                <br class="clear">
                            </div>
                            <div class="session_content_extend hide session_content_wrapper">
                                <div class="session_content has_speaker_thumb">
                                    <div class="session_excerpt"><?php echo $v['description']; ?></div>
                                    <?php if (!empty($v['bookmark'])): ?>
                                        <div class="session_title_list">
                                            <span class="ti-bookmark"></span>
                                            <?php foreach ($v['bookmark'] as $bm): ?>
                                                <div class="session_title_item"><?php echo $bm['name']; ?></div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="session_location themeborder">
                                        <div class="session_location_label skin_color"><?php echo $where; ?></div>
                                        <div class="session_location_content"><?php echo $v['where']; ?></div>
                                    </div>
                                </div>
                                <br class="clear">
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        <?php endforeach; ?>
    </div>
<?php endif; ?>