<?php
// update meta product wooCommerce events
function update_meta_product_wooCommerce_events() {
    // Ensure the function only runs once
    if (get_option('update_meta_product_wooCommerce_events_action')) {
        return;
    }

    // Query for all WooCommerce products
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1
    );

    $products = new WP_Query($args);

    // Loop through each product
    if ($products->have_posts()) {
        while ($products->have_posts()) {
            $products->the_post();
            $product_id = get_the_ID();

            // Get the product thumbnail URL
            $thumbnail_id = get_post_thumbnail_id($product_id);
            $thumbnail_url = wp_get_attachment_url($thumbnail_id);
            $phn_type_product = get_post_meta($product_id, 'phn_type_product', true);
            if($phn_type_product === "event"){
                update_post_meta($product_id, 'WooCommerceEventsEmailSubjectSingle', '{OrderNumber} Billet');
            }
        }
    }

    // Restore original Post Data
    wp_reset_postdata();

    // Set an option to indicate that the function has run
    update_option('update_meta_product_wooCommerce_events_action', true);
}
add_action('init', 'update_meta_product_wooCommerce_events');

// Automatically create WooCommerce product from Tribe Events
function auto_create_woocommerce_product_from_events($original_post_id, $original_post)
{
    if ($original_post->post_type == 'tribe_events' && !defined('DOING_AUTOSAVE') && 'publish' === get_post_status($original_post)) {
        remove_action('save_post', __FUNCTION__);

        $calendar_reminders = [
            ["amount" => "1", "unit" => "weeks"],
            ["amount" => "1", "unit" => "days"],
            ["amount" => "1", "unit" => "hours"],
            ["amount" => "15", "unit" => "minutes"]
        ];
        $type = 'product';
        $price = get_post_meta($original_post_id, '_EventCost', true);
        $thumbnail_image = get_post_meta($original_post_id, '_thumbnail_id', true);
        $event_start_date = get_post_meta($original_post_id, '_EventStartDate', true);
        $event_start_date_timestamp = strtotime($event_start_date);
        $event_start_date_format = date_format(date_create($event_start_date), "F d, Y");
        $event_end_date = get_post_meta($original_post_id, '_EventEndDate', true);
        $event_end_date_timestamp = strtotime($event_end_date);
        $event_end_date_format = date_format(date_create($event_end_date), "F d, Y");
        $period = (date_format(date_create($event_start_date), "a") == 'am') ? 'a.m.' : 'p.m.';
        $hour = date_format(date_create($event_start_date), "h");
        $minutes = date_format(date_create($event_start_date), "i");
        $endperiod = (date_format(date_create($event_end_date), "a") == 'am') ? 'a.m.' : 'p.m.';
        $hour_end = date_format(date_create($event_end_date), "h");
        $minutes_end = date_format(date_create($event_end_date), "i");
        $ticket_theme = get_home_path() . 'wp-content/uploads/fooevents/themes/default_ticket_theme';
        $pdf_ticket_theme = get_home_path() . 'wp-content/uploads/fooevents/themes/default_pdf_single';
        if(!empty($thumbnail_image)){
            $ticket_logo = wp_get_attachment_image_src( $thumbnail_image, 'full' )[0];
        }else{
            $ticket_logo = get_option('globalWooCommerceEventsTicketLogo');
        }
        $ticket_header_image = get_option('globalWooCommerceEventsTicketHeaderImage');
        $timezone = get_post_meta($original_post_id, '_EventTimezone', true);
        $number_tickets = get_post_meta($original_post_id, 'number_tickets', true);
        $address = get_post_meta($original_post_id, 'location', true);
        $country_code = country_code_phn($address);
        $tax_class = ($country_code === 'fr') ? 'ticket' : 'zero-rate';
        $tax = get_post_meta($original_post_id, 'taxes', true);
        if (!empty($tax)) {
            $tax_class = $tax;
        }

        // Check if post exists
        if (!does_post_exist_by_slug($original_post->post_name, $type)) {
            $product_id = wp_insert_post(
                array(
                    'post_title' => $original_post->post_title,
                    'post_name' => $original_post->post_name,
                    'post_status' => 'publish',
                    'post_type' => $type,
                    'post_content' => $original_post->post_content,
                )
            );
        } else {
            $product_id = get_post_meta($original_post_id, 'product_of_events', true);
            wp_update_post(
                array(
                    'ID' => $product_id,
                    'post_title' => $original_post->post_title,
                    'post_content' => $original_post->post_content,
                )
            );
        }

        // Update or create product meta
        if ($product_id) {
            update_post_meta($original_post_id, 'product_of_events', $product_id);
            update_field('field_660381310b680', $address, $product_id);
            wp_set_object_terms($product_id, 'simple', 'product_type');
            $meta_data = array(
                '_tax_status' => 'taxable',
                '_tax_class' => $tax_class,
                '_regular_price' => $price,
                '_price' => $price,
                '_stock_status' => 'instock',
                '_manage_stock' => 'yes',
                '_stock' => $number_tickets,
                '_thumbnail_id' => $thumbnail_image,
                'events_of_product' => $original_post_id,
                'WooCommerceEventsEvent' => 'Event',
                'phn_type_product' => 'event',
                'WooCommerceEventsType' => 'sequential',
                'fooevents_bookings_options_serialized' => 'null',
                'fooevents_custom_attendee_fields_options_serialized' => '{}',
                'WooCommerceEventsDate' => $event_start_date_format,
                'WooCommerceEventsDateMySQLFormat' => $event_start_date,
                'WooCommerceEventsDateTimestamp' => $event_start_date_timestamp,
                'WooCommerceEventsDateTimeTimestamp' => $event_start_date_timestamp,
                'WooCommerceEventsEndDate' => $event_end_date_format,
                'WooCommerceEventsEndDateMySQLFormat' => $event_end_date,
                'WooCommerceEventsEndDateTimestamp' => $event_end_date_timestamp,
                'WooCommerceEventsEndDateTimeTimestamp' => $event_end_date_timestamp,
                'WooCommerceEventsTicketExpirationType' => 'select',
                'WooCommerceEventsHour' => $hour,
                'WooCommerceEventsMinutes' => $minutes,
                'WooCommerceEventsHourEnd' => $hour_end,
                'WooCommerceEventsMinutesEnd' => $minutes_end,
                'WooCommerceEventsTextColor' => '#ffffff',
                'WooCommerceEventsTicketIdentifierOutput' => 'ticketid',
                'WooCommerceEventsTicketNumberOutput' => 1,
                'WooCommerceEventsEmailSubjectSingle' => '{OrderNumber} Billet',
                'WooCommerceEventsViewBookingsOptions' => 'checkout',
                'WooCommerceEventsBookingsMethod' => 'slotdate',
                'WooCommercePrintTicketSort' => 'most_recent',
                'WooCommercePrintTicketSize' => 'tickets_avery_letter_10',
                'WooCommercePrintTicketNrColumns' => 3,
                'WooCommercePrintTicketNrRows' => 3,
                'WooCommerceEventsZoomType' => 'meetings',
                'WooCommerceEventsZoomMultiOption' => 'single',
                'WooCommerceEventsZoomDurationHour' => 1,
                'WooCommerceEventsZoomDurationMinute' => 0,
                'WooCommerceEventsTicketAddCalendarReminders' => $calendar_reminders,
                'WooCommerceEventsTicketTheme' => $ticket_theme,
                'WooCommerceEventsPDFTicketTheme' => $pdf_ticket_theme,
                'WooCommerceEventsTicketLogo' => $ticket_logo,
                'WooCommerceEventsTicketHeaderImage' => $ticket_header_image,
                'WooCommerceEventsBookingsExpirePassedDate' => 'off',
                'WooCommerceEventsBookingsExpireValue' => 'off',
                'WooCommerceEventsBookingsExpireUnit' => 'off',
                'WooCommerceEventsSelectGlobalTime' => 'off',
                'WooCommerceEventsTicketDisplayPrice' => 'off',
                'WooCommerceEventsTicketDisplayZoom' => 'off',
                'WooCommerceEventsTicketDisplayBookings' => 'off',
                'WooCommerceEventsTicketDisplayMultiDay' => 'on',
                'WooCommerceEventsIncludeCustomAttendeeDetails' => 'off',
                'WooCommerceEventsCaptureAttendeeDetails' => 'off',
                'WooCommerceEventsCaptureAttendeeEmail' => 'off',
                'WooCommerceEventsEmailAttendee' => 'off',
                'WooCommerceEventsCaptureAttendeeTelephone' => 'off',
                'WooCommerceEventsCaptureAttendeeCompany' => 'off',
                'WooCommerceEventsCaptureAttendeeDesignation' => 'off',
                'WooCommerceEventsUniqueEmail' => 'off',
                'WooCommerceEventsExportUnpaidTickets' => 'off',
                'WooCommerceEventsExportBillingDetails' => 'off',
                'WooCommerceEventsViewSeatingOptions' => 'off',
                'WooCommerceEventsViewSeatingChart' => 'off',
                'WooCommerceEventsEventDetailsNewOrder' => 'off',
                'WooCommerceEventsDisplayAttendeeNewOrder' => 'off',
                'WooCommerceEventsDisplayBookingsNewOrder' => 'off',
                'WooCommerceEventsDisplaySeatingsNewOrder' => 'off',
                'WooCommerceEventsDisplayCustAttNewOrder' => 'off',
                'WooCommerceEventsHideBookingsDisplayTime' => 'off',
                'WooCommerceEventsHideBookingsSlotCalendar' => 'off',
                'WooCommerceEventsHideBookingsStockAvailability' => 'off',
                'WooCommerceEventsViewBookingsStockDropdowns' => 'off',
                'WooCommerceEventsViewOutOfStockBookings' => 'off',
                'WooCommerceEventsBookingsHideDateSingleDropDown' => 'off',
                'WooCommerceEventsTicketPurchaserDetails' => 'on',
                'WooCommerceEventsTicketAddCalendar' => 'on',
                'WooCommerceEventsTicketAttachICS' => 'on',
                'WooCommerceEventsTicketDisplayDateTime' => 'on',
                'WooCommerceEventsTicketDisplayBarcode' => 'on',
                'WooCommerceEventsSendEmailTickets' => 'on',
                'WooCommerceEventsCutLinesPrintTicket' => 'on',
                "FooEventsPDFTicketsEmailText" => "",
                "FooEventsTicketFooterText" => "",
                "WooCommerceEventsExpire" => "",
                "WooCommerceEventsExpireTimestamp" => "",
                "WooCommerceEventsExpireMessage" => "",
                "WooCommerceEventsTicketsExpireSelect" => "",
                "WooCommerceEventsTicketsExpireSelectTimestamp" => "",
                "WooCommerceEventsPeriod" => $period,
                "WooCommerceEventsTimeZone" => $timezone,
                "WooCommerceEventsLocation" => "",
                "WooCommerceEventsPrintTicketLogo" => "",
                "WooCommerceEventsTicketText" => "",
                "WooCommerceEventsThankYouText" => "",
                "WooCommerceEventsEventDetailsText" => "",
                "WooCommerceEventsSupportContact" => "",
                "WooCommerceEventsEndPeriod" => $endperiod,
                "WooCommerceEventsAddEventbrite" => "",
                "WooCommerceEventsGPS" => "",
                "WooCommerceEventsDirections" => "",
                "WooCommerceEventsEmail" => "",
                "WooCommerceEventsTicketBackgroundColor" => "",
                "WooCommerceEventsTicketButtonColor" => "",
                "WooCommerceEventsTicketTextColor" => "",
                "WooCommerceEventsBackgroundColor" => "",
                "WooCommerceEventsGoogleMaps" => "",
                "WooCommerceEventsTicketNumberPrefix" => "",
                "WooCommerceEventsTicketNumberSuffix" => "",
                "wooCommerceEventsEmailTicketAdmin" => "",
                "WooCommerceEventsAttendeeOverride" => "",
                "WooCommerceEventsAttendeeOverridePlural" => "",
                "WooCommerceEventsEventDetailsOverride" => "",
                "WooCommerceEventsTicketOverride" => "",
                "WooCommerceEventsTicketOverridePlural" => "",
                "WooCommerceEventsBookingsDateOverride" => "",
                "WooCommerceEventsBookingsDateOverridePlural" => "",
                "WooCommerceEventsBookingsBookingDetailsOverride" => "",
                "WooCommerceEventsBookingsBookingDetailsOverridePlural" => "",
                "WooCommerceEventsBookingsSlotOverride" => "",
                "WooCommerceEventsBookingsSlotOverridePlural" => "",
                "WooCommerceBadgeFieldTopLeft" => "",
                "WooCommerceBadgeFieldTopMiddle" => "",
                "WooCommerceBadgeFieldTopRight" => "",
                "WooCommerceBadgeField_a_4" => "",
                "WooCommerceBadgeFieldMiddleLeft" => "",
                "WooCommerceBadgeFieldMiddleMiddle" => "",
                "WooCommerceBadgeFieldMiddleRight" => "",
                "WooCommerceBadgeField_b_4" => "",
                "WooCommerceBadgeFieldBottomLeft" => "",
                "WooCommerceBadgeFieldBottomMiddle" => "",
                "WooCommerceBadgeFieldBottomRight" => "",
                "WooCommerceBadgeField_c_4" => "",
                "WooCommerceBadgeField_d_1" => "",
                "WooCommerceBadgeField_d_2" => "",
                "WooCommerceBadgeField_d_3" => "",
                "WooCommerceBadgeField_d_4" => "",
                "WooCommerceBadgeFieldTopLeft_logo" => "",
                "WooCommerceBadgeFieldTopMiddle_logo" => "",
                "WooCommerceBadgeFieldTopRight_logo" => "",
                "WooCommerceBadgeField_a_4_logo" => "",
                "WooCommerceBadgeFieldMiddleLeft_logo" => "",
                "WooCommerceBadgeFieldMiddleMiddle_logo" => "",
                "WooCommerceBadgeFieldMiddleRight_logo" => "",
                "WooCommerceBadgeField_b_4_logo" => "",
                "WooCommerceBadgeFieldBottomLeft_logo" => "",
                "WooCommerceBadgeFieldBottomMiddle_logo" => "",
                "WooCommerceBadgeFieldBottomRight_logo" => "",
                "WooCommerceBadgeField_c_4_logo" => "",
                "WooCommerceBadgeField_d_1_logo" => "",
                "WooCommerceBadgeField_d_2_logo" => "",
                "WooCommerceBadgeField_d_3_logo" => "",
                "WooCommerceBadgeField_d_4_logo" => "",
                "WooCommerceBadgeFieldTopLeft_custom" => "",
                "WooCommerceBadgeFieldTopMiddle_custom" => "",
                "WooCommerceBadgeFieldTopRight_custom" => "",
                "WooCommerceBadgeField_a_4_custom" => "",
                "WooCommerceBadgeFieldMiddleLeft_custom" => "",
                "WooCommerceBadgeFieldMiddleMiddle_custom" => "",
                "WooCommerceBadgeFieldMiddleRight_custom" => "",
                "WooCommerceBadgeField_b_4_custom" => "",
                "WooCommerceBadgeFieldBottomLeft_custom" => "",
                "WooCommerceBadgeFieldBottomMiddle_custom" => "",
                "WooCommerceBadgeFieldBottomRight_custom" => "",
                "WooCommerceBadgeField_c_4_custom" => "",
                "WooCommerceBadgeField_d_1_custom" => "",
                "WooCommerceBadgeField_d_2_custom" => "",
                "WooCommerceBadgeField_d_3_custom" => "",
                "WooCommerceBadgeField_d_4_custom" => "",
                "WooCommercePrintTicketNumbers" => "",
                "WooCommercePrintTicketOrders" => "",
                "WooCommerceEventsTicketBackgroundImage" => "",
                "WooCommerceEventsZoomHost" => "",
                "WooCommerceEventsZoomWebinar" => ""
            );

            foreach ($meta_data as $key => $value) {
                update_post_meta($product_id, $key, $value);
            }
        }
    }
}
add_action('save_post', 'auto_create_woocommerce_product_from_events', 20, 2);

// Check if post exists by slug
function does_post_exist_by_slug($post_slug, $type)
{
    $loop_posts = new WP_Query(array('post_type' => $type, 'name' => $post_slug, 'posts_per_page' => -1));
    return $loop_posts->have_posts();
}

// CSS for admin panel
function hide_tribe_events()
{
    echo '<style>.tribe-allday,#event_cost tr:nth-child(2),#event_cost tr:nth-child(3) {display: none;}</style>';
}
add_action('admin_head', 'hide_tribe_events');

// Delete associated WooCommerce product when a 'tribe_events' post is trashed
function delete_associated_product_on_events_trash($post_id = '')
{
    // Retrieve post IDs from $_GET if available, or use provided $post_id
    $post_ids = is_array($_GET['post']) ? $_GET['post'] : array($post_id);
    foreach ($post_ids as $id) {
        // Check if the post type is 'tribe_events'
        if (get_post_type($id) === 'tribe_events') {
            // Retrieve associated product ID
            $product_id = get_post_meta($id, 'product_of_events', true);
            // Check if product exists and is of type 'product'
            if ($product_id && get_post_type($product_id) === 'product') {
                // Delete associated product
                wp_delete_post($product_id, true);
            }
        }
    }
}
add_action('wp_trash_post', 'delete_associated_product_on_events_trash');

// Add footer to single event
function add_footer_to_single_event($html)
{
    ob_start();
    if (get_query_var('hotels')) {
        get_template_part('tribe-events/event', 'hotels');
        $content_hotels = ob_get_contents();
        $html = $html . $content_hotels;
    }
    if (class_exists("\\Elementor\\Plugin")) {
        $post_id_footer = get_field('select_option_footer', 'option');
        // $post_ID = 29052;
        // $pluginElementor = \Elementor\Plugin::instance();
        // $contentElementor = $pluginElementor->frontend->get_builder_content($post_ID);
        $html .= '<div id="footer" class="footer-content footer-content-single">';
        // $html .= apply_filters('the_content',$contentElementor);
        $html .= do_shortcode('[SHORTCODE_ELEMENTOR id="' . $post_id_footer . '"]');
        $html .= '</div>';
    }
    ob_get_clean();
    return force_balance_tags($html);
}
add_filter('tribe_events_views_v2_bootstrap_html', 'add_footer_to_single_event');

// Partners event elementor
function partners_event_elementor($partners)
{
    ob_start();
    $html = '';
    if (class_exists("\\Elementor\\Plugin") && $partners) {
        // $pluginElementor = \Elementor\Plugin::instance();
        // $contentElementor = $pluginElementor->frontend->get_builder_content($partners);
        // $html = apply_filters('the_content',$contentElementor);
        $html = do_shortcode('[SHORTCODE_ELEMENTOR id=' . $partners . ']');
    }
    ob_get_clean();
    return force_balance_tags($html);
}

// Wc remove product from cart
function wc_remove_product_from_cart($product_id)
{
    $cart = WC()->cart;
    $cart_items = $cart->get_cart();
    $id_ticket = id_ticket_in_cart();
    $event_id = get_post_meta($product_id, 'events_of_product', true);
    if($id_ticket != $event_id){
        WC()->cart->empty_cart();
    }
}

// Reset session form ticket
function reset_session_form_ticket()
{
    if (is_page_template('template/during-checkout.php')) {
        session_start();
        unset($_SESSION['infor_ticket']);
    }
}
add_action('template_redirect', 'reset_session_form_ticket');
// Reset session form room
function reset_session_form_room()
{
    if (is_page_template('template/form-room-checkout.php')) {
        session_start();
        unset($_SESSION['infor_room']);
    }
}
add_action('template_redirect', 'reset_session_form_room');
function create_entry_infor_customer_buy_room($order_id)
{
    session_start();
    if (isset($_SESSION['infor_room']) && $order_id && class_exists('vxcf_form')) {
        $field_name = [];
        $infor_room = $_SESSION['infor_room'];
        $form_id = (int) $infor_room[0]['_wpcf7'];
        $track = true;
        $form_title = get_the_title($form_id);
        $tags = vxcf_form::get_form_fields('cf_' . $form_id);
        $vxcf_form = new vxcf_form();
        $form_arr = array('id' => $form_id, 'name' => $form_title, 'fields' => $tags);
        // var_dump($infor_room);
        if ($infor_room && $tags) {
            foreach ($infor_room as $key => $value) {
                foreach ($tags as $k => $v) {
                    $field_name[$k] = isset($value[$k]) ? $value[$k] : "";
                }
                $field_name['order_id'] = $order_id;
                $field_name['rooms_id'] = $value['rooms_id'];
                $lead = $field_name;
                $entry_id = $vxcf_form->create_entry($lead, $form_arr, 'cf', '', $track);
                update_column_entry_leads_room($entry_id, 12092001);
            }
        }
    }
    unset($_SESSION['infor_room']);
}
// add_action('wp', 'create_entry_infor_customer_buy_room');
function update_column_entry_leads_room($entry_id, $room_id)
{
    if ($entry_id && $room_id) {
        global $wpdb;
        $table_leads = $wpdb->prefix . 'vxcf_leads';
        $wpdb->update(
            $table_leads,
            array(
                'meta' => $room_id
            ),
            array('id' => $entry_id)
        );
    }
}
function custom_vxcf_entries_before_saving_lead_main_room($main)
{
    $main['url'] = home_url();
    return $main;
}
add_filter('vxcf_entries_plugin_before_saving_lead_main', 'custom_vxcf_entries_before_saving_lead_main_room');
// Create entry infor customer buy ticket
function create_entry_infor_customer_buy_ticket($order_id)
{
    session_start();
    if (isset($_SESSION['infor_ticket']) && $order_id && class_exists('vxcf_form')) {
        global $wpdb;
        $field_name = [];
        $ticket_id = [];
        $infor_ticket = $_SESSION['infor_ticket'];
        $form_id = (int) $infor_ticket[0]['_wpcf7'];
        $track = true;
        $form_title = get_the_title($form_id);
        $tags = vxcf_form::get_form_fields('cf_' . $form_id);
        $vxcf_form = new vxcf_form();
        $form_arr = array('id' => $form_id, 'name' => $form_title, 'fields' => $tags);
        $sql = $wpdb->prepare("SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = %s AND meta_value = %d", 'WooCommerceEventsOrderID', $order_id);
        $results = $wpdb->get_results($sql);

        if ($results) {
            foreach ($results as $value) {
                $ticket_id[] = (int) get_post_meta((int) $value->post_id, 'WooCommerceEventsTicketID', true);
            }
        }
        // var_dump($wpdb);
        // var_dump($ticket_id);
        if ($infor_ticket && $tags) {
            foreach ($infor_ticket as $key => $value) {
                foreach ($tags as $k => $v) {
                    $field_name[$k] = isset($value[$k]) ? $value[$k] : "";
                }
                // echo $ticket_id[$key];
                $field_name['order_id'] = $order_id;
                $field_name['ticket_id'] = $ticket_id[$key];
                $field_name['order_id_type'] = $order_id;
                $lead = $field_name;
                $entry_id = $vxcf_form->create_entry($lead, $form_arr, 'cf', '', $track);
                update_column_entry_leads($entry_id, $ticket_id[$key]);
            }
        }
    }
    unset($_SESSION['infor_ticket']);
}
// add_action('wp', 'create_entry_infor_customer_buy_ticket');
// Update column entry leads
function update_column_entry_leads($entry_id, $ticket_id)
{
    if ($entry_id && $ticket_id) {
        global $wpdb;
        $table_leads = $wpdb->prefix . 'vxcf_leads';
        $wpdb->update(
            $table_leads,
            array(
                'meta' => $ticket_id
            ),
            array('id' => $entry_id)
        );
    }
}

// Custom vxcf entries before saving lead main
function custom_vxcf_entries_before_saving_lead_main($main)
{
    $main['url'] = home_url();
    return $main;
}
add_filter('vxcf_entries_plugin_before_saving_lead_main', 'custom_vxcf_entries_before_saving_lead_main');

// Export data entry form
function export_data_entry_form($form_id)
{
    if (!empty($_GET['phn_crm_form_action']) && $_GET['phn_crm_form_action'] == 'download_csv_phn') {
        $form_id = $_GET['form_id'];
        $id_form_room = "cf_30436";
        if ($form_id === $id_form_room) {
            global $wpdb;
            header(
                "Content-disposition: attachment; filename=" .
                date("Y-m-d", current_time("timestamp")) .
                ".csv"
            );
            header("Content-Transfer-Encoding: binary");
            $now = gmdate("D, d M Y H:i:s");
            header("Expires: Tue, 03 Jul 2000 06:00:00 GMT");
            header(
                "Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate"
            );
            header("Last-Modified: {$now} GMT");
            header("Content-Type: text/html; charset=UTF-8");
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");

            $field_titles = [];
            $_row = [];
            $sql = "SELECT id,created FROM {$wpdb->prefix}vxcf_leads WHERE form_id = '$id_form_room'";
            $result = $wpdb->get_results($sql);
            if ($result) {
                $tags = vxcf_form::get_form_fields($id_form_room);
                $tags['order_id'] = array('label' => 'Order id', 'values' => '');
                $tags['rooms_id'] = array('label' => 'Room id', 'values' => '');
                $i = 0;
                if ($tags) {
                    $field_titles[] = "#";
                    foreach ($tags as $value) {
                        if ($value['values']) {
                            $title = $value['values'][0]['label'];
                        } else {
                            $title = $value['label'];
                        }
                        $field_titles[] = $title;
                    }
                    $field_titles[] = "Created";
                }
                $fp = fopen("php://output", "w");
                fputcsv($fp, $field_titles);
                foreach ($result as $row) {
                    $i++;
                    $lead_id = (int) $row->id;
                    $created = $row->created;
                    $vxcf_form = new vxcf_form();
                    $entry_detail = $vxcf_form->get_entry_detail($lead_id);
                    if ($entry_detail && $tags) {
                        $_row[] = $i;
                        foreach ($entry_detail as $key => $value) {
                            $_row[] = $value['value'];
                        }
                        $_row[] = $created;
                    }
                    fputcsv($fp, $_row);
                    $_row = [];
                }
            }
            fclose($fp);
        } else {
            global $wpdb;
            header(
                "Content-disposition: attachment; filename=" .
                date("Y-m-d", current_time("timestamp")) .
                ".csv"
            );
            header("Content-Transfer-Encoding: binary");
            $now = gmdate("D, d M Y H:i:s");
            header("Expires: Tue, 03 Jul 2000 06:00:00 GMT");
            header(
                "Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate"
            );
            header("Last-Modified: {$now} GMT");
            header("Content-Type: text/html; charset=UTF-8");
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");

            $field_titles = [];
            $_row = [];
            $sql = "SELECT id,created FROM {$wpdb->prefix}vxcf_leads WHERE form_id = '$form_id'";
            $result = $wpdb->get_results($sql);
            if ($result) {
                $tags = vxcf_form::get_form_fields($form_id);
                $tags['order_id'] = array('label' => 'Order id', 'values' => '');
                $tags['ticket_id'] = array('label' => 'Ticket id', 'values' => '');
                $i = 0;
                if ($tags) {
                    $field_titles[] = "#";
                    foreach ($tags as $value) {
                        if ($value['values']) {
                            $title = $value['values'][0]['label'];
                        } else {
                            $title = $value['label'];
                        }
                        $field_titles[] = $title;
                    }
                    $field_titles[] = "Created";
                }
                $fp = fopen("php://output", "w");
                fputcsv($fp, $field_titles);
                foreach ($result as $row) {
                    $i++;
                    $lead_id = (int) $row->id;
                    $created = $row->created;
                    $vxcf_form = new vxcf_form();
                    $entry_detail = $vxcf_form->get_entry_detail($lead_id);
                    if ($entry_detail && $tags) {
                        $_row[] = $i;
                        foreach ($entry_detail as $key => $value) {
                            $_row[] = $value['value'];
                        }
                        $_row[] = $created;
                    }
                    fputcsv($fp, $_row);
                    $_row = [];
                }
            }
            fclose($fp);
        }

        die();
    }
}
add_action('init', 'export_data_entry_form');

// Add button export entry
function add_button_export_entry()
{
    global $wpdb;
    if (isset($_GET['form_id'])) {
        $form_id = $_GET['form_id'];
    } else {
        $args = array(
            'post_type' => 'wpcf7_contact_form',
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'ASC',
        );
        $forms = get_posts($args);
        if ($forms) {
            $form_id = $forms[0]->ID;
            $form_id = "cf_" . $form_id;
        }
    }
    $sql = "SELECT id FROM {$wpdb->prefix}vxcf_leads WHERE form_id = '$form_id'";
    $result = $wpdb->get_results($sql);
    if ($result) {
        echo '<a class="button-secondary button export_csv_entry" href="?phn_crm_form_action=download_csv_phn&form_id=' . $form_id . '">Download CSV</a>';
    }
}
add_action('vxcf_entries_table_title_end', 'add_button_export_entry');
?>