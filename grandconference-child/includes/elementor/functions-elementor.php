<?php


function acf_load_option_footer_field_choices($field)
{

    $field['choices'] = array();
    $args = array(
        'post_type' => 'footer',
        'posts_per_page' => -1,
    );

    $query = new WP_Query($args);
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $title_post = get_the_title($post_id);
            $field['choices'][$post_id] = $title_post;
        }
    }


    return $field;

}
add_filter('acf/load_field/name=select_option_footer', 'acf_load_option_footer_field_choices');

// Footer elementor
function footer_elementor()
{
    ob_start();
    if (class_exists("\\Elementor\\Plugin")) {
        $post_id_footer = get_field('select_option_footer', 'option');
        // var_dump($post_id_footer);
        // $post_ID = 29052;
        $post_ID = $post_id_footer;
        $html = '';
        $pluginElementor = \Elementor\Plugin::instance();
        $contentElementor = $pluginElementor->frontend->get_builder_content($post_ID);
        $html .= '<div id="footer" class="footer-content footer-content-single avsss3 '.$post_id_footer.'">';
        // $html .= apply_filters('the_content', $contentElementor);
        $html .= do_shortcode('[SHORTCODE_ELEMENTOR id="' . $post_id_footer . '"]');
        $html .= '</div>';
        $html = force_balance_tags($html);
    }
    ob_get_clean();
    return $html;
}
?>