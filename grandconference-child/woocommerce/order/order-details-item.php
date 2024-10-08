<?php
/**
 * Order Item Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-item.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!apply_filters('woocommerce_order_item_visible', true, $item)) {
    return;
}
?>
<tr
    class="<?php echo esc_attr(apply_filters('woocommerce_order_item_class', 'woocommerce-table__line-item order_item', $item, $order)); ?>">

    <td class="woocommerce-table__product-name product-name">

        <?php

        $is_visible = $product && $product->is_visible();
        // var_dump($product->id);
        $product_id_hotel = $product->id;
        $type = get_post_meta($product_id_hotel, 'phn_type_product', true);
        if ($type === "hotel") {
            $hotel_id = get_post_meta($product_id_hotel, 'hotels_of_product', true);
            $hotel_link = get_permalink($hotel_id);
            $product_permalink = $hotel_link;
        } else {
            $product_permalink = apply_filters('woocommerce_order_item_permalink', $is_visible ? $product->get_permalink($item) : '', $item, $order);
        }
        

        echo wp_kses_post(apply_filters('woocommerce_order_item_name', $product_permalink ? sprintf('<a href="%s">%s</a>', $product_permalink, $item->get_name()) : $item->get_name(), $item, $is_visible));

        $qty = $item->get_quantity();
        $refunded_qty = $order->get_qty_refunded_for_item($item_id);

        if ($refunded_qty) {
            $qty_display = '<del>' . esc_html($qty) . '</del> <ins>' . esc_html($qty - ($refunded_qty * -1)) . '</ins>';
        } else {
            $qty_display = esc_html($qty);
        }

        echo apply_filters('woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf('&times;&nbsp;%s', $qty_display) . '</strong>', $item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        
        do_action('woocommerce_order_item_meta_start', $item_id, $item, $order, false);

        // wc_display_item_meta($item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

        $strings = array();
        $html    = '';
        $args    = wp_parse_args(
            $args,
            array(
                'before'       => '<ul class="wc-item-meta" style="margin:0"><li>',
                'after'        => '</li></ul>',
                'separator'    => '</li><li>',
                'echo'         => true,
                'autop'        => false,
                'label_before' => '<strong class="wc-item-meta-label">',
                'label_after'  => ':</strong> ',
            )
        );
        $i = 0;
        foreach ( $item->get_all_formatted_meta_data() as $meta_id => $meta ) {
            $i++;
            if($i < 3){
                $value     = $args['autop'] ? wp_kses_post( $meta->display_value ) : wp_kses_post( make_clickable( trim( $meta->display_value ) ) );
                $strings[] = $args['label_before'] . wp_kses_post( $meta->display_key ) . $args['label_after'] . $value;
            }
        }

        if ( $strings ) {
            $html = $args['before'] . implode( $args['separator'], $strings ) . $args['after'];
        }

        $html = apply_filters( 'woocommerce_display_item_meta', $html, $item, $args );

        if ( $args['echo'] ) {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $html;
        } else {
            return $html;
        }

        // allow other plugins 
        
        do_action('woocommerce_order_item_meta_end', $item_id, $item, $order, false);

        ?>
    </td>

    <td class="woocommerce-table__product-total product-total">
        <?php echo $order->get_formatted_line_subtotal($item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    </td>

</tr>

<?php if ($show_purchase_note && $purchase_note): ?>

    <tr class="woocommerce-table__product-purchase-note product-purchase-note">

        <td colspan="2">
            <?php echo wpautop(do_shortcode(wp_kses_post($purchase_note))); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </td>

    </tr>

<?php endif; ?>