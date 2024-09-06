<?php
/**
 * "Order received" message.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.3.0
 *
 * @var WC_Order|false $order
 */

defined('ABSPATH') || exit;
global $wpdb;
global $wp;
$order_id = absint($wp->query_vars['order-received']);

// if ( function_exists( 'create_entry_infor_customer_buy_ticket' ) ) {
//     create_entry_infor_customer_buy_ticket($order_id);

// }
$query_event = $wpdb->prepare("
    SELECT product_id
    FROM {$wpdb->prefix}wc_order_product_lookup
    WHERE order_id = %d AND variation_id = 0
", $order_id);

$results_event = $wpdb->get_results($query_event);
// var_dump($results_event);
if (isset($results_event[0])) {
	$event_id = (int) $results_event[0]->product_id;
}


$query = $wpdb->prepare("
    SELECT product_id, product_qty,variation_id
    FROM {$wpdb->prefix}wc_order_product_lookup
    WHERE order_id = %d AND variation_id != 0
", $order_id);

$results = $wpdb->get_results($query);
// var_dump($results);
// $query_type = $wpdb->prepare( "
//     SELECT order_item_name
//     FROM {$wpdb->prefix}woocommerce_order_items
//     WHERE order_id = %d 
// ", $order_id );

// $results_type = $wpdb->get_results( $query_type );

// $query = $wpdb->prepare("
//     SELECT oip.order_item_name, op.product_qty, op.product_id
//     FROM {$wpdb->prefix}wc_order_product_lookup AS op
//     INNER JOIN {$wpdb->prefix}woocommerce_order_items AS oip ON op.order_item_id = oip.order_item_id
//     WHERE op.order_id = %d
// ", $order_id);

$results = $wpdb->get_results($query);

if ($results) {
	$newTypeOfRoom = array();
	foreach ($results as $result) {
		$product_id = $result->product_id;
		$product_qty = (int) $result->product_qty;
		$variation_id = (int) $result->variation_id;
		$event_id = (int) get_post_meta($event_id, 'events_of_product', true);
		$typeOfRoom = get_post_meta($event_id, 'hotel_type-of-rooms', true);
		// echo "<pre>";
		// var_dump("typeOfRoom" ,$typeOfRoom);
		$typeOfRoomNew = [];
		if ($typeOfRoom) {
			foreach ($typeOfRoom as $hotel) {
				$hotelId = $hotel["hotelId"];
				$roomTypes = [];
				foreach ($hotel["roomTypes"] as $roomType) {
					$id = (int) $roomType["id"];
					$roomName = $roomType["name"];
					$price = $roomType["price"];
					$pricenew = $roomType["pricenew"];
					$quantity = (int) $roomType["quantity"];
					$fooEvents = (int) $roomType["fooEvents"];
					if ($id == $variation_id) {
						$fooEvents = $fooEvents - $product_qty;
					}
					$roomTypes[] = array(
						"id" => $id,
						"name" => $roomName,
						"price" => $price,
						"pricenew" => $pricenew,
						"quantity" => $quantity,
						"fooEvents" => $fooEvents,
						"descriptionTypeRoom" => $roomType["descriptionTypeRoom"]
					);
				}
				$typeOfRoomNew[] = array(
					'hotelId' => $hotelId,
					'roomTypes' => $roomTypes
				);
			}
		}

		// echo "<pre>";
		// var_dump("typeOfRoom new" ,$variation_id);
		// echo "------------------------------------";
		update_post_meta($event_id, 'hotel_type-of-rooms', $typeOfRoomNew);

	}

} else {

}
?>
<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received">
	<?php
	/**
	 * Filter the message shown after a checkout is complete.
	 *
	 * @since 2.2.0
	 *
	 * @param string         $message The message.
	 * @param WC_Order|false $order   The order created during checkout, or false if order data is not available.
	 */
	$message = apply_filters(
		'woocommerce_thankyou_order_received_text',
		esc_html(__('Thank you. Your order has been received.', 'woocommerce')),
		$order
	);

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $message;
	?>
</p>