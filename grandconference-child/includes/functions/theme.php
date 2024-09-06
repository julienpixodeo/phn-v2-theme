<?php
add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );
add_filter( 'use_widgets_block_editor', '__return_false' );
add_filter( 'use_block_editor_for_post', '__return_false' );

// Add rewrite endpoint phn
function add_rewrite_endpoint_phn() {
	add_rewrite_endpoint('tickets', EP_PERMALINK);
    add_rewrite_endpoint('planning', EP_PERMALINK);
    add_rewrite_endpoint('hotels', EP_PERMALINK);
    add_rewrite_endpoint('partners', EP_PERMALINK);
}
add_action('init','add_rewrite_endpoint_phn');

// Request phn
function request_phn($vars) {
	if (isset($vars['tickets'])){
		$vars['tickets'] = true;
	}
    if (isset($vars['planning'])){
		$vars['planning'] = true;
	}
    if (isset($vars['hotels'])){
		$vars['hotels'] = true;
	}
    if (isset($vars['partners'])){
		$vars['partners'] = true;
	}
	return $vars;
}
add_filter('request','request_phn');

// Add rewrite rule
function phn_post_type_and_rule() {
    add_rewrite_tag( '%event_id%', '([^/]*)' );
    add_rewrite_rule(
        '^hotels/([^/]*)/([^/]*)/?$',
        'index.php?hotel=$matches[1]&event_id=$matches[2]',
        'top'
    );
}
add_action( 'init', 'phn_post_type_and_rule' );

// Acf add options page
if( function_exists('acf_add_options_page') ) {
    acf_add_options_page();
}

// My acf google map api
function my_acf_google_map_api( $api ){
    $api['key'] = API_MAP;
    return $api;
}
add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');

// Custom post type hotels
function custom_post_type_hotels() {
    $labels = array(
        'name'               => _x( 'Hotels', 'post type general name', 'your-text-domain' ),
        'singular_name'      => _x( 'Hotel', 'post type singular name', 'your-text-domain' ),
        'menu_name'          => _x( 'Hotels', 'admin menu', 'your-text-domain' ),
        'name_admin_bar'     => _x( 'Hotel', 'add new on admin bar', 'your-text-domain' ),
        'add_new'            => _x( 'Add New', 'hotel', 'your-text-domain' ),
        'add_new_item'       => __( 'Add New Hotel', 'your-text-domain' ),
        'new_item'           => __( 'New Hotel', 'your-text-domain' ),
        'edit_item'          => __( 'Edit Hotel', 'your-text-domain' ),
        'view_item'          => __( 'View Hotel', 'your-text-domain' ),
        'all_items'          => __( 'All Hotels', 'your-text-domain' ),
        'search_items'       => __( 'Search Hotels', 'your-text-domain' ),
        'parent_item_colon'  => __( 'Parent Hotels:', 'your-text-domain' ),
        'not_found'          => __( 'No hotels found.', 'your-text-domain' ),
        'not_found_in_trash' => __( 'No hotels found in Trash.', 'your-text-domain' )
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __( 'Description.', 'your-text-domain' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'hotels' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
        // 'taxonomies'         => array( 'category', 'post_tag' )
    );

    register_post_type( 'hotel', $args );
}
add_action( 'init', 'custom_post_type_hotels' );

// Custom post type partners event
function custom_post_type_partners() {
    $labels = array(
        'name'               => _x( 'Partners Event', 'post type general name', 'your-text-domain' ),
        'singular_name'      => _x( 'Partners Event', 'post type singular name', 'your-text-domain' ),
        'menu_name'          => _x( 'Partners Event', 'admin menu', 'your-text-domain' ),
        'name_admin_bar'     => _x( 'Partners Event', 'add new on admin bar', 'your-text-domain' ),
        'add_new'            => _x( 'Add New', 'Partners Event', 'your-text-domain' ),
        'add_new_item'       => __( 'Add New Partners Event', 'your-text-domain' ),
        'new_item'           => __( 'New Partners Event', 'your-text-domain' ),
        'edit_item'          => __( 'Edit Partners Event', 'your-text-domain' ),
        'view_item'          => __( 'View Partners Event', 'your-text-domain' ),
        'all_items'          => __( 'All Partners Event', 'your-text-domain' ),
        'search_items'       => __( 'Search Partners Event', 'your-text-domain' ),
        'parent_item_colon'  => __( 'Parent Partners Event:', 'your-text-domain' ),
        'not_found'          => __( 'No hotels found.', 'your-text-domain' ),
        'not_found_in_trash' => __( 'No hotels found in Trash.', 'your-text-domain' )
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __( 'Description.', 'your-text-domain' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'partners_event' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
        // 'taxonomies'         => array( 'category', 'post_tag' )
    );

    register_post_type( 'partners_event', $args );
}
add_action( 'init', 'custom_post_type_partners' );

// Get country code
function country_code_phn($location){
    $country_code = NULL;
    if($location){
        $lat = $location['lat'];
        $lng = $location['lng'];
    }
    $url = "https://api.geoapify.com/v1/geocode/reverse?lat={$lat}&lon={$lng}&apiKey=053d3c1b237b463b81d9e61788cc79d3";
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    if($data){
        $country_code = $data['features'][0]['properties']['country_code'];
    }
    return $country_code;
}

// CSS for admin panel cf7
function css_admin_cf7() {
    echo '<style>
        #wpcf7-admin-form-element #post-body-content .inside,
        #wpcf7-admin-form-element #mail-panel-tab,
        #wpcf7-admin-form-element #messages-panel-tab,
        #wpcf7-admin-form-element #additional-settings-panel-tab,
        #wpcf7-admin-form-element .keyboard-interaction
        {
            display: none;
        }
        .toplevel_page_wpcf7 .fixed .column-title {
            width: 70%;
        }
    </style>';
}
add_action('admin_head', 'css_admin_cf7');

// Change text admin
function change_text_admin( $translated_text, $text, $domain ) {
    if ( is_admin() ) {
        switch ( $translated_text ) {
            case 'Contact' :
                $translated_text = __( 'Form Ticket', 'phn' );
                break;
            case 'Contact Forms' :
                $translated_text = __( 'Form Ticket', 'phn' );
                break;
            case 'Add New Contact Form' :
                $translated_text = __( 'Add New Form Ticket', 'phn' );
                break;
            case 'Edit Contact Form' :
                $translated_text = __( 'Edit Form Ticket', 'phn' );
                break;
            case 'Search Contact Forms' :
                $translated_text = __( 'Search Form Ticket', 'phn' );
                break;
        }
    }
    
    return $translated_text;
}
add_filter( 'gettext', 'change_text_admin', 20, 3 );

// Custom locale
function custom_locale() {
    $locale = 'en_US';
    session_start();
    $url_server = $_SERVER['REQUEST_URI'];
    $post_id = url_to_postid(home_url($url_server));
    $lan = get_field('language',$post_id);

    if(isset($_SESSION['locale'])){
        $locale = $_SESSION['locale'];
    }
    
    if($lan === 'french'){
        $locale = 'fr_FR';
    }
    
    if($lan === 'english'){
        $locale = 'en_US';
    }

    if(!is_admin()){
        switch_to_locale($locale);
    }
}
add_action( 'init', 'custom_locale' );

// Replace month names
function replace_month_names($input) {
    // English month names
    $englishMonths = array(
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    );
    
    // French month names (short form)
    $frenchMonths = array(
        'janv.', 'févr.', 'mars', 'avril', 'mai', 'juin',
        'juil.', 'août', 'sept.', 'oct.', 'nov.', 'déc.'
    );
    
    // Replace English month names with French month names (short form)
    $output = str_replace($englishMonths, $frenchMonths, $input);
    
    return $output;
}

function swapFirstTwoWords($str) {
    // $words = explode(" ", $str);
    
    // if (count($words) < 2) {
    //     return $str;
    // }
    
    // $temp = $words[0];
    // $words[0] = $words[1];
    // $words[1] = $temp;

    // $temp1 = $words[5];
    // $words[5] = $words[6];
    // $words[6] = $temp1;
    
    // $newStr = implode(" ", $words);  
    return $str;
}

function custom_tribe_events_event_schedule_details( $event = null, $before = '', $after = '', $html = true ) {
	static $cache_var_name = __FUNCTION__;

	if ( is_null( $event ) ) {
		global $post;
		$event = $post;
	}

	if ( is_numeric( $event ) ) {
		$event = get_post( $event );
	}

	// if the post is password protected, don't return the schedule details
	if ( post_password_required( $event ) ) {
		return '';
	}

	$cache_details     = tribe_get_var( $cache_var_name, [] );
	$cache_details_key = "{$event->ID}:{$before}:{$after}:{$html}";

	// If the event has no start or end date, avoid returning empty schedule details.
	if ( empty( tribe_get_start_date( $event->ID ) )
	     && empty( tribe_get_end_date( $event->ID ) )
	) {
		return '';
	}

	if ( ! isset( $cache_details[ $cache_details_key ] ) ) {
		$inner                    = $html ? '<span class="tribe-event-date-start">' : '';
		$format                   = '';
		$date_without_year_format = tribe_get_date_format();
		$date_with_year_format    = tribe_get_date_format( true );
		$time_format              = get_option( 'time_format' );
		$datetime_separator       = tribe_get_option( 'dateTimeSeparator', ' @ ' );
		$time_range_separator     = tribe_get_option( 'timeRangeSeparator', ' - ' );

		$settings = [
			'show_end_time' => true,
			'time'          => true,
		];

		$settings = wp_parse_args( apply_filters( 'tribe_events_event_schedule_details_formatting', $settings ), $settings );
		if ( ! $settings['time'] ) {
			$settings['show_end_time'] = false;
		}

		/**
		 * @var $show_end_time
		 * @var $time
		 */
		extract( $settings );

		$format = $date_with_year_format;

		/**
		 * If a yearless date format should be preferred.
		 *
		 * By default, this will be true if the event starts and ends in the current year.
		 *
		 * @param bool    $use_yearless_format
		 * @param WP_Post $event
		 */
		$use_yearless_format = apply_filters( 'tribe_events_event_schedule_details_use_yearless_format',
			(
				tribe_get_start_date( $event, false, 'Y' ) === date_i18n( 'Y' )
				&& tribe_get_end_date( $event, false, 'Y' ) === date_i18n( 'Y' )
			),
			$event
		);

		if ( $use_yearless_format ) {
			$format = $date_with_year_format;
		}

		if ( tribe_event_is_multiday( $event ) ) { // multi-date event

			$format2ndday = apply_filters( 'tribe_format_second_date_in_range', $format, $event );

			if ( tribe_event_is_all_day( $event ) ) {
				$inner .= tribe_get_start_date( $event, true, $format );
				$inner .= ( $html ? '</span>' : '' ) . $time_range_separator;
				$inner .= $html ? '<span class="tribe-event-date-end">' : '';

				$end_date_full           = tribe_get_end_date( $event, true, Tribe__Date_Utils::DBDATETIMEFORMAT );
				$end_date_full_timestamp = strtotime( $end_date_full );

				// if the end date is <= the beginning of the day, consider it the previous day
				if ( $end_date_full_timestamp <= strtotime( tribe_beginning_of_day( $end_date_full ) ) ) {
					$end_date = tribe_format_date( $end_date_full_timestamp - DAY_IN_SECONDS, false, $format2ndday );
				} else {
					$end_date = tribe_get_end_date( $event, false, $format2ndday );
				}

				$inner .= $end_date;
			} else {
				$inner .= tribe_get_start_date( $event, false, $format ) . ( $time ? $datetime_separator . tribe_get_start_date( $event, false, $time_format ) : '' );
				$inner .= ( $html ? '</span>' : '' ) . $time_range_separator;
				$inner .= $html ? '<span class="tribe-event-date-end">' : '';
				$inner .= tribe_get_end_date( $event, false, $format2ndday ) . ( $time ? $datetime_separator . tribe_get_end_date( $event, false, $time_format ) : '' );
			}
		} elseif ( tribe_event_is_all_day( $event ) ) { // all day event
			$inner .= tribe_get_start_date( $event, true, $format );
		} else { // single day event
			if ( tribe_get_start_date( $event, false, 'g:i A' ) === tribe_get_end_date( $event, false, 'g:i A' ) ) { // Same start/end time
				$inner .= tribe_get_start_date( $event, false, $format ) . ( $time ? $datetime_separator . tribe_get_start_date( $event, false, $time_format ) : '' );
			} else { // defined start/end time
				$inner .= tribe_get_start_date( $event, false, $format ) . ( $time ? $datetime_separator . tribe_get_start_date( $event, false, $time_format ) : '' );
				$inner .= ( $html ? '</span>' : '' ) . ( $show_end_time ? $time_range_separator : '' );
				$inner .= $html ? '<span class="tribe-event-time">' : '';
				$inner .= ( $show_end_time ? tribe_get_end_date( $event, false, $time_format ) : '' );
			}
		}

		$inner .= $html ? '</span>' : '';

		$cache_details[ $cache_details_key ] = $inner;
		tribe_set_var( $cache_var_name, $cache_details );
	}

	/**
	 * Provides an opportunity to modify the *inner* schedule details HTML (ie before it is
	 * wrapped).
	 *
	 * @param string $inner_html the output HTML
	 * @param int    $event_id   post ID of the event we are interested in
	 */
	$inner = apply_filters( 'tribe_events_event_schedule_details_inner', $cache_details[ $cache_details_key ], $event->ID );

	// Wrap the schedule text
	$schedule = $before . $inner . $after;

	/**
	 * Provides an opportunity to modify the schedule details HTML for a specific event after
	 * it has been wrapped in the before and after markup.
	 *
	 * @param string $schedule the output HTML
	 * @param int    $event_id post ID of the event we are interested in
	 * @param string $before   part of the HTML wrapper that was prepended
	 * @param string $after    part of the HTML wrapper that was appended
	 */
	return apply_filters( 'tribe_events_event_schedule_details', $schedule, $event->ID, $before, $after );
}
?>