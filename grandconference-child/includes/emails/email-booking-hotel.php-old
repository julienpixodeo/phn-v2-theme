<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
// Load colors.
$bg        = get_option( 'woocommerce_email_background_color' );
$body      = get_option( 'woocommerce_email_body_background_color' );
$base      = get_option( 'woocommerce_email_base_color' );
$base_text = wc_light_or_dark( $base, '#202020', '#ffffff' );
$text      = get_option( 'woocommerce_email_text_color' );

// Pick a contrasting color for links.
$link_color = wc_hex_is_light( $base ) ? $base : $base_text;

if ( wc_hex_is_light( $body ) ) {
	$link_color = wc_hex_is_light( $base ) ? $base_text : $base;
}

$bg_darker_10    = wc_hex_darker( $bg, 10 );
$body_darker_10  = wc_hex_darker( $body, 10 );
$base_lighter_20 = wc_hex_lighter( $base, 20 );
$base_lighter_40 = wc_hex_lighter( $base, 40 );
$text_lighter_20 = wc_hex_lighter( $text, 20 );
$text_lighter_40 = wc_hex_lighter( $text, 40 );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />
		<title><?php echo get_bloginfo( 'name', 'display' ); ?></title>
        <style>
            body {
                padding: 0;
            }

            #wrapper {
                background-color: <?php echo esc_attr( $bg ); ?>;
                margin: 0;
                padding: 70px 0;
                -webkit-text-size-adjust: none !important;
                width: 100%;
            }

            #template_container {
                box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1) !important;
                background-color: <?php echo esc_attr( $body ); ?>;
                border: 1px solid <?php echo esc_attr( $bg_darker_10 ); ?>;
                border-radius: 3px !important;
            }

            #template_header {
                background-color: <?php echo esc_attr( $base ); ?>;
                border-radius: 3px 3px 0 0 !important;
                color: <?php echo esc_attr( $base_text ); ?>;
                border-bottom: 0;
                font-weight: bold;
                line-height: 100%;
                vertical-align: middle;
                font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
            }

            #template_header h1,
            #template_header h1 a {
                color: <?php echo esc_attr( $base_text ); ?>;
                background-color: inherit;
            }

            #template_header_image img {
                margin-left: 0;
                margin-right: 0;
            }

            #template_footer td {
                padding: 0;
                border-radius: 6px;
            }

            #template_footer #credit {
                border: 0;
                color: <?php echo esc_attr( $text_lighter_40 ); ?>;
                font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
                font-size: 12px;
                line-height: 150%;
                text-align: center;
                padding: 24px 0;
            }

            #template_footer #credit p {
                margin: 0 0 16px;
            }

            #body_content {
                background-color: <?php echo esc_attr( $body ); ?>;
            }

            #body_content table td {
                padding: 48px 48px 32px;
            }

            #body_content table td td {
                padding: 12px;
            }

            #body_content table td th {
                padding: 12px;
            }

            #body_content td ul.wc-item-meta {
                font-size: small;
                margin: 1em 0 0;
                padding: 0;
                list-style: none;
            }

            #body_content td ul.wc-item-meta li {
                margin: 0.5em 0 0;
                padding: 0;
            }

            #body_content td ul.wc-item-meta li p {
                margin: 0;
            }

            #body_content p {
                margin: 0 0 16px;
            }

            #body_content_inner {
                color: <?php echo esc_attr( $text_lighter_20 ); ?>;
                font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
                font-size: 14px;
                line-height: 150%;
                text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
            }

            .td {
                color: <?php echo esc_attr( $text_lighter_20 ); ?>;
                border: 1px solid <?php echo esc_attr( $body_darker_10 ); ?>;
                vertical-align: middle;
            }

            .address {
                padding: 12px;
                color: <?php echo esc_attr( $text_lighter_20 ); ?>;
                border: 1px solid <?php echo esc_attr( $body_darker_10 ); ?>;
            }

            .text {
                color: <?php echo esc_attr( $text ); ?>;
                font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
            }

            .link {
                color: <?php echo esc_attr( $link_color ); ?>;
            }

            #header_wrapper {
                padding: 36px 48px;
                display: block;
            }

            h1 {
                color: <?php echo esc_attr( $base ); ?>;
                font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
                font-size: 30px;
                font-weight: 300;
                line-height: 150%;
                margin: 0;
                text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
                text-shadow: 0 1px 0 <?php echo esc_attr( $base_lighter_20 ); ?>;
            }

            h2 {
                color: <?php echo esc_attr( $base ); ?>;
                display: block;
                font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
                font-size: 18px;
                font-weight: bold;
                line-height: 130%;
                margin: 0 0 18px;
                text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
            }

            h3 {
                color: <?php echo esc_attr( $base ); ?>;
                display: block;
                font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
                font-size: 16px;
                font-weight: bold;
                line-height: 130%;
                margin: 16px 0 8px;
                text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
            }

            a {
                color: <?php echo esc_attr( $link_color ); ?>;
                font-weight: normal;
                text-decoration: underline;
            }

            img {
                border: none;
                display: inline-block;
                font-size: 14px;
                font-weight: bold;
                height: auto;
                outline: none;
                text-decoration: none;
                text-transform: capitalize;
                vertical-align: middle;
                margin-<?php echo is_rtl() ? 'left' : 'right'; ?>: 10px;
                max-width: 100%;
            }
            #body_content td ul.wc-item-meta li p {
                padding: 0;
            }
            #body_content td ul.wc-item-meta li:nth-child(3),
            #body_content td ul.wc-item-meta li:nth-child(4){
                display: none;
            }
        </style>
	</head>
	<body <?php echo is_rtl() ? 'rightmargin' : 'leftmargin'; ?>="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
		<div id="wrapper" dir="<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>">
			<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
				<tr>
					<td align="center" valign="top">
						<div id="template_header_image">
							<?php
							if ( $img = get_option( 'woocommerce_email_header_image' ) ) {
								echo '<p style="margin-top:0;"><img src="' . esc_url( $img ) . '" alt="' . get_bloginfo( 'name', 'display' ) . '" /></p>';
							}
							?>
						</div>
						<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_container">
							<tr>
								<td align="center" valign="top">
									<!-- Header -->
									<table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_header">
										<tr>
											<td id="header_wrapper">
												<h1>Merci pour votre commande</h1>
											</td>
										</tr>
									</table>
									<!-- End Header -->
								</td>
							</tr>
							<tr>
								<td align="center" valign="top">
									<!-- Body -->
									<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_body">
										<tr>
											<td valign="top" id="body_content">
												<!-- Content -->
												<table border="0" cellpadding="20" cellspacing="0" width="100%">
													<tr>
														<td valign="top">
															<div id="body_content_inner">
                                                                <?php
                                                                    // $order = wc_get_order( $order_id );
                                                                    $items = $order->get_items();
                                                                    $text_align  = is_rtl() ? 'right' : 'left';
                                                                    $margin_side = is_rtl() ? 'left' : 'right';
                                                                ?>
                                                                <p><?php printf( esc_html__( 'Bonjour %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) ); ?></p>
                                                                <p><?php printf( esc_html__( 'Juste pour vous informer - nous avons reçu votre commande #%s et elle est en cours de traitement:', 'woocommerce' ), esc_html( $order->get_order_number() ) ); ?></p>
                                                                <h2>
                                                                    <?php
                                                                        setlocale(LC_TIME, 'fr_FR.utf8', 'fr_FR', 'fr');
                                                                        $dateString = $order->get_date_created();
                                                                        $timestamp = strtotime($dateString);
                                                                        $formattedDate = strftime("%e %B %Y", $timestamp);
                                                                        echo '[Commande #'.$order->get_order_number().'] ('.$formattedDate.')';
                                                                    ?>
                                                                </h2>
                                                                <div style="margin-bottom:40px;">
                                                                    <table class="" cellspacing="0" cellpadding="6" border="1" style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;width:100%;font-family:'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" width="100%">
                                                                        <thead>
                                                                            <tr>
                                                                            <th class="" scope="col" style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;" align="left">Produit</th>
                                                                            <th class="" scope="col" style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;" align="left">Quantité</th>
                                                                            <th class="" scope="col" style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;" align="left">Prix</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php
                                                                            $total_order = 0;
                                                                            foreach ( $items as $item_id => $item ) {
                                                                                $product       = $item->get_product();
                                                                                $sku           = '';
                                                                                $purchase_note = '';
                                                                                $image         = '';

                                                                                if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
                                                                                    continue;
                                                                                }

                                                                                if ( is_object( $product ) ) {
                                                                                    $sku           = $product->get_sku();
                                                                                    $purchase_note = $product->get_purchase_note();
                                                                                    $image         = $product->get_image('full');
                                                                                }

                                                                                $product_id = $item->get_product_id();

                                                                                $type = get_post_meta( $product_id, 'phn_type_product', true );

                                                                                if($type === "event"){
                                                                                    continue;
                                                                                }

                                                                                $total_order_sub = $item->get_total() + $item->get_subtotal_tax();
                                                                                $total_order += $total_order_sub;
                                                                                ?>
                                                                                <tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
                                                                                    <td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">
                                                                                    <?php

                                                                                    echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) );
                                                                                    // allow other plugins to add additional product information here.
                                                                                    do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order );

                                                                                    wc_display_item_meta(
                                                                                        $item,
                                                                                        array(
                                                                                            'label_before' => '<strong class="wc-item-meta-label" style="float: ' . esc_attr( $text_align ) . '; margin-' . esc_attr( $margin_side ) . ': .25em; clear: both">',
                                                                                        )
                                                                                    );

                                                                                    // allow other plugins to add additional product information here.
                                                                                    do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order );

                                                                                    ?>
                                                                                    </td>
                                                                                    <td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
                                                                                        <?php
                                                                                        $qty          = $item->get_quantity();
                                                                                        $refunded_qty = $order->get_qty_refunded_for_item( $item_id );

                                                                                        if ( $refunded_qty ) {
                                                                                            $qty_display = '<del>' . esc_html( $qty ) . '</del> <ins>' . esc_html( $qty - ( $refunded_qty * -1 ) ) . '</ins>';
                                                                                        } else {
                                                                                            $qty_display = esc_html( $qty );
                                                                                        }
                                                                                        echo wp_kses_post( apply_filters( 'woocommerce_email_order_item_quantity', $qty_display, $item ) );
                                                                                        ?>
                                                                                    </td>
                                                                                    <td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
                                                                                        <?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?>
                                                                                    </td>
                                                                                </tr>
                                                                                <?php
                                                                            }
                                                                            ?>
                                                                        </tbody>
                                                                        <tfoot>
                                                                            <tr>
                                                                                <th class="" scope="row" colspan="2" style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;border-top-width:4px;" align="left">Sous-total:</th>
                                                                                <td class="" style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;border-top-width:4px;" align="left">
                                                                                <span><?php echo wc_price($total_order); ?></span>
                                                                                </span>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th class="" scope="row" colspan="2" style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;" align="left">Mode de paiement:</th>
                                                                                <td class="" style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;" align="left"><?php echo $order->get_payment_method_title(); ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th class="" scope="row" colspan="2" style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;" align="left">Total:</th>
                                                                                <td class="" style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;" align="left">
                                                                                <span><?php echo wc_price($total_order); ?></span> 
                                                                                </td>
                                                                            </tr>
                                                                            <?php
                                                                                if ( $order->get_customer_note() ) {
                                                                                    ?>
                                                                                    <tr>
                                                                                        <th class="td" scope="row" colspan="2" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Note:', 'woocommerce' ); ?></th>
                                                                                        <td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
                                                                                    </tr>
                                                                                    <?php
                                                                                }
                                                                            ?>
                                                                        </tfoot>
                                                                    </table>
                                                                </div>
                                                                <?php
                                                                   $text_align = is_rtl() ? 'right' : 'left';
                                                                   $address    = $order->get_formatted_billing_address();
                                                                   $shipping   = $order->get_formatted_shipping_address();
                                                                ?>
                                                                <table id="addresses" cellspacing="0" cellpadding="0" style="width: 100%; vertical-align: top; margin-bottom: 40px; padding:0;" border="0">
                                                                    <tr>
                                                                        <td style="text-align:<?php echo esc_attr( $text_align ); ?>; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; border:0; padding:0;" valign="top" width="50%">
                                                                            <h2 style="color: #7f54b3; display: block; font-family:Helvetica Neue&,Helvetica,Roboto,Arial,sans-serif; font-size: 18px; font-weight: bold; line-height: 130%; margin: 0 0 18px; text-align: left;">
                                                                                <?php esc_html_e( 'Adresse de facturation', 'woocommerce' ); ?>
                                                                            </h2>
                                                                
                                                                            <address class="address">
                                                                                <p style="margin:0 0 0px;">
                                                                                    <?php
                                                                                    $order_id = $order->get_id();
                                                                                    $billing_genre = get_post_meta($order_id, 'billing_genre', true);
                                                                                    echo $billing_genre;
                                                                                    ?>
                                                                                </p>
                                                                                <?php echo wp_kses_post( $address ? $address : esc_html__( 'N/A', 'woocommerce' ) ); ?>
                                                                                <?php if ( $order->get_billing_phone() ) : ?>
                                                                                    <br/><?php echo wc_make_phone_clickable( $order->get_billing_phone() ); ?>
                                                                                <?php endif; ?>
                                                                                <?php if ( $order->get_billing_email() ) : ?>
                                                                                    <br/><?php echo esc_html( $order->get_billing_email() ); ?>
                                                                                <?php endif; ?>
                                                                            </address>
                                                                        </td>
                                                                        <?php if ( ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() && $shipping ) : ?>
                                                                            <td style="text-align:<?php echo esc_attr( $text_align ); ?>; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; padding:0;" valign="top" width="50%">
                                                                            <h2 style="color: #7f54b3; display: block; font-family:Helvetica Neue&,Helvetica,Roboto,Arial,sans-serif; font-size: 18px; font-weight: bold; line-height: 130%; margin: 0 0 18px; text-align: left;">
                                                                                    <?php esc_html_e( 'Adresse de livraison', 'woocommerce' ); ?>
                                                                                </h2>
                                                                
                                                                                <address class="address">
                                                                                    <?php echo wp_kses_post( $shipping ); ?>
                                                                                    <?php if ( $order->get_shipping_phone() ) : ?>
                                                                                        <br /><?php echo wc_make_phone_clickable( $order->get_shipping_phone() ); ?>
                                                                                    <?php endif; ?>
                                                                                </address>
                                                                            </td>
                                                                        <?php endif; ?>
                                                                    </tr>
                                                                </table>
                                                                <p style="margin:0 0 16px;">Merci d'utiliser 
                                                                    <a class="defaultMailLink defaultMailLink" href="<?php echo home_url(); ?>" target="_blank" rel="noopener noreferrer" data-ik="ik-secure">
                                                                        <?php echo str_replace('https://','',home_url());  ?>
                                                                    </a>!
                                                                </p>
                                                            </div>
														</td>
													</tr>
												</table>
												<!-- End Content -->
											</td>
										</tr>
									</table>
									<!-- End Body -->
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
	</body>
</html>