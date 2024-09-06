<?php
//List event of hotel
function list_event_hotel_shortcode($atts){
    ob_start();
    $atts = shortcode_atts( array(
		'post_id' => ''
	), $atts );

	$post_id = (int) $atts['post_id'];

    $args = array(
        'post_type'      => 'tribe_events',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
    );

    $query = new WP_Query( $args );

    if ( $query->have_posts() ){
        echo '<div class="event-hotel-list">';
        while ( $query->have_posts() ){
            $query->the_post();
            $hotels = get_field('hotels');
            if(!empty($hotels)){
                if(in_array($post_id,$hotels)){
                    echo '<a href="'.get_permalink().'">'.get_the_title().'</a>';
                }
            } 
        }
        echo '</div>';
    }

	wp_reset_postdata();
    $result = ob_get_contents();
    ob_end_clean();
    return $result;
}
add_shortcode( 'list_event_hotel', 'list_event_hotel_shortcode' );
?>