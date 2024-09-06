<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
define('API_MAP', get_field('key_map','option'));
// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );

// END ENQUEUE PARENT ACTION

/**
 * Enqueue scripts and styles.
 */
function phn_scripts() {
	wp_enqueue_style('font-awesome-css', 'https://pro.fontawesome.com/releases/v5.10.0/css/all.css', array(), '0.1.0', 'all');
	wp_enqueue_style('slick-css', get_stylesheet_directory_uri() . '/assets/slick/slick.css', array(), time(), 'all');
    wp_enqueue_style('slick-theme-css', get_stylesheet_directory_uri() . '/assets/slick/slick-theme.css', array(), time(), 'all');
	wp_enqueue_style('fancyapps-css', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css', array(), time(), 'all');
    wp_enqueue_style('theme-css', get_stylesheet_directory_uri() . '/assets/css/theme.css', array(), time(), 'all');
    wp_enqueue_style('event-css', get_stylesheet_directory_uri() . '/assets/css/event.css', array(), time(), 'all');
	wp_enqueue_script('maps-script', 'https://maps.googleapis.com/maps/api/js?libraries=places&key='.API_MAP.'&language=fr&region=FR', array('jquery'), time(), true);
	wp_enqueue_script('fancyapps-script', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js', array('jquery'), time(), true);
	wp_enqueue_script('slick-script', get_stylesheet_directory_uri() . '/assets/slick/slick.min.js', array('jquery'), time(), true);
	wp_enqueue_script('theme-script', get_stylesheet_directory_uri() . '/assets/js/theme.js', array('jquery'), time(), true);
    wp_enqueue_script('ajax-script', get_stylesheet_directory_uri() . '/assets/js/ajax.js', array('jquery'), time(), true);
    wp_enqueue_script('event-script', get_stylesheet_directory_uri() . '/assets/js/event.js', array('jquery'), time(), true);
	wp_localize_script('ajax-script', 'jaxsr',
		array(
			'url' => admin_url('admin-ajax.php'),
		)
	);
	if (is_admin()) {
		wp_enqueue_script('ajax-admin-script', get_stylesheet_directory_uri() . '/assets/js/ajax-admin.js', array('jquery'), time(), true);
		wp_localize_script('ajax-script', 'jaxsr',
			array(
				'url' => admin_url('admin-ajax.php'),
			)
		);
    }
	if (is_page_template('template/during-checkout.php')) {
		wp_enqueue_style('jvcf7_client', get_stylesheet_directory_uri() . '/validation-cf7/css/jvcf7_client.css', array(), time(), 'all');
		wp_enqueue_script('jquery-validate', get_stylesheet_directory_uri() . '/validation-cf7/js/jquery.validate.min.js', array('jquery'), time(), true);
    	wp_enqueue_script('jvcf7_validation', get_stylesheet_directory_uri() . '/validation-cf7/js/jvcf7_validation.js', array('jquery'), time(), true);
		$scriptData = jvcf7_get_data_for_client_script();
  		wp_localize_script( 'jvcf7_validation', 'scriptData', $scriptData );
    }
	if (is_page_template('template/form-room-checkout.php')) {
		wp_enqueue_style('jvcf7_client', get_stylesheet_directory_uri() . '/validation-cf7/css/jvcf7_client.css', array(), time(), 'all');
		wp_enqueue_script('jquery-validate', get_stylesheet_directory_uri() . '/validation-cf7/js/jquery.validate.min.js', array('jquery'), time(), true);
    	wp_enqueue_script('jvcf7_validation', get_stylesheet_directory_uri() . '/validation-cf7/js/jvcf7_validation.js', array('jquery'), time(), true);
		$scriptData = jvcf7_get_data_for_client_script();
  		wp_localize_script( 'jvcf7_validation', 'scriptData', $scriptData );
    }
}
add_action( 'wp_enqueue_scripts', 'phn_scripts' );

// Jvcf7 get data for client script
function jvcf7_get_data_for_client_script(){
	$scriptData = array(			
		'jvcf7_default_settings' => array(
			'jvcf7_show_label_error'	=> "errorMsgshow",
			'jvcf7_invalid_field_design'	=> "theme_0"
		)
	);
	return $scriptData;
}

/**
 * functions events
 */
require get_theme_file_path('/includes/events/functions-events.php');

/**
 * functions events admin
 */
// require get_theme_file_path('/includes/events/functions-events-admin.php');

/**
 * ajax events
 */
require get_theme_file_path('/includes/events/ajax-events.php');

/**
 * theme
 */
require get_theme_file_path('/includes/functions/theme.php');

/**
 * woocommerce
 */
require get_theme_file_path('/includes/woocommerce/woocommerce.php');

/**
 * shortcode
 */
require get_theme_file_path('/includes/shortcode/shortcode.php');

/**
 * functions hotels
 */
require get_theme_file_path('/includes/hotels/functions-hotels.php');

/**
 * ajax hotels
 */
require get_theme_file_path('/includes/hotels/ajax-hotels.php');

/**
 * functions elementor
 */
require get_theme_file_path('/includes/elementor/functions-elementor.php');

/**
 * functions acf
 */
require get_theme_file_path('/includes/acf/acf-functions.php');

/**
 * functions admin event
 */
require get_theme_file_path('/admin/functions/admin-event.php');

/**
 * functions booking
 */
require get_theme_file_path('/booking/functions/booking.php');

/**
 * functions export
 */
require get_theme_file_path('/export/functions/export.php');