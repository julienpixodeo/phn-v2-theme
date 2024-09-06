<?php
// Acf load form event field choices
function acf_load_form_event_field_choices( $field ) {
    $field['choices'] = array();
    $choices = get_contact_form_7_list();
    if($choices) {
        $field['choices'][''] = 'Select form';
        foreach( $choices as $key => $value ) {
            $field['choices'][ $key ] = $value;
        }
    }
    return $field;
    
}
add_filter('acf/load_field/name=form_event', 'acf_load_form_event_field_choices');

// Get contact form 7 list
function get_contact_form_7_list() {
    $args = array(
        'post_type'      => 'wpcf7_contact_form',
        'posts_per_page' => -1,
    );
    $forms = get_posts( $args );
    $form_list = array();
    foreach ( $forms as $form ) {
        $form_list[$form->ID] = $form->post_title;
    }
    return $form_list;
}

// Acf load TAX field choices
function acf_load_tax_field_choices( $field ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wc_tax_rate_classes';
    $query = "SELECT * FROM $table_name";
    $results = $wpdb->get_results($query, ARRAY_A);
    $field['choices'] = array();

    if($results) {
        $field['choices'][''] = 'Select tax';
        foreach( $results as $key => $value ) {
            $field['choices'][ $value['slug'] ] = $value['name'];
        }
    }
    
    return $field;
}
add_filter('acf/load_field/name=taxes', 'acf_load_tax_field_choices');
?>