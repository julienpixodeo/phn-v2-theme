<?php
/**
 * Order tickets generated template
 *
 * @link https://www.fooevents.com
 * @package woocommerce_events
 */
$config = new FooEvents_Config();
$FooEvents_Orders_Helper = new FooEvents_Orders_Helper($config);
?>
<div id="fooevents-orders-ticket-details">
	<div class="fooevents-notice">
		<p><em><?php esc_attr_e( 'The following ticket/s have been generated for this order.', 'woocommerce-events' ); ?></em></p>
	</div>
	<div class="clear"></div>
	<?php foreach ( $woocommerce_events_order_tickets as $event ) : ?>
		<div class="fooevents-orders-ticket-details-container">

			<div class="fooevents-orders-ticket-details-tickets">
				<?php foreach ( $event['tickets'] as $ticket ) : ?>
					<div class="fooevents-orders-ticket-details-tickets-inner"> 

						<div id="fooevents-ticket-details-head">
							<img src="<?php echo esc_attr( $FooEvents_Orders_Helper->config->barcode_url ) . esc_attr( $ticket['WooCommerceEventsTicketHash'] ) . '-' . esc_attr( $ticket['WooCommerceEventsTicketID'] ); ?>.png" class="ticket-code" />
							<?php if ( ! empty( $ticket['WooCommerceEventsAttendeeName'] ) && ! empty( $ticket['WooCommerceEventsAttendeeLastName'] ) ) : ?>
							<h1><?php echo esc_attr( $ticket['WooCommerceEventsAttendeeName'] ) . ' ' . esc_attr( $ticket['WooCommerceEventsAttendeeLastName'] ); ?></h1>  
							<?php else : ?>
							<h1><?php echo esc_attr( $ticket['customerFirstName'] ) . ' ' . esc_attr( $ticket['customerLastName'] ); ?></h1>  
							<?php endif; ?>
							<h3><a href="post.php?post=<?php echo esc_attr( $ticket['ID'] ); ?>&action=edit" target="_BLANK">#<?php echo esc_attr( $ticket['WooCommerceEventsTicketID'] ); ?></a></h3>
							<?php if ( 'ticketnumberformatted' === $ticket['WooCommerceEventsTicketIdentifierOutput'] ) : ?>
								<h3><?php echo esc_attr( $ticket['WooCommerceEventsTicketNumberFormatted'] ); ?></h3>
							<?php endif; ?>
							<div class="clear"></div>
						</div>
						<table id="fooevents-order-attendee-details" cellpadding="0" cellspacing="0"> 
							<?php
								$ticket_id = $ticket['WooCommerceEventsTicketID'];
								$information_user_each_ticket = information_user_each_ticket($ticket_id);
								if($information_user_each_ticket){
									foreach($information_user_each_ticket as $value){
										if (is_serialized($value['value'])) {
											$value['value'] = unserialize($value['value']);
											if (is_array($value['value'])) {
												$value['value'] = implode(', ', $value['value']);
											}
										}
										?>
										<tr>
											<td><strong><?php echo esc_attr( $value['name'] ); ?>:</strong></td>
											<td>
												<?php 
													if(filter_var($value['value'], FILTER_VALIDATE_EMAIL)) {
														?>
														<a href="mailto:<?php echo esc_attr( $value['value'] ); ?>"><?php echo esc_attr( $value['value'] ); ?></a>
														<?php
													}
													else {
														echo esc_attr( $value['value'] );
													}
												?>
											</td>
										</tr>
										<?php
									}
								}
							?>
						</table>
						<div class="clear"></div>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="fooevents-orders-ticket-details-events">
				<div class="fooevents-orders-ticket-details-events-inner">
					<h3><?php echo esc_attr( $event['WooCommerceEventsName'] ); ?></h3>
						<table id="fooevents-order-attendee-details" cellpadding="0" cellspacing="0"> 
						<?php if ( ! empty( $event['WooCommerceEventsDate'] ) && trim( $event['WooCommerceEventsDate'] ) !== '' ) : ?>
							<tr>
								<td><strong><?php esc_attr_e( 'Date:', 'woocommerce-events' ); ?></strong></td>
								<td><?php echo( empty( $event['WooCommerceEventsDate'] ) ) ? '' : esc_attr( $event['WooCommerceEventsDate'] ); ?></td>
							</tr>
						<?php endif; ?>
						<?php if ( ! empty( $event['WooCommerceEventsStartTime'] ) && trim( $event['WooCommerceEventsStartTime'] ) !== '' && empty( $ticket['WooCommerceEventsBookingSlot'] ) ) : ?>
							<tr>
								<td><strong><?php esc_attr_e( 'Time:', 'woocommerce-events' ); ?></strong></td>
								<td><?php echo( empty( $event['WooCommerceEventsStartTime'] ) ) ? '' : esc_attr( $event['WooCommerceEventsStartTime'] ); ?> <?php echo( empty( $event['WooCommerceEventsEndTime'] ) ) ? '' : ' - ' . $event['WooCommerceEventsEndTime']; ?> </td>
							</tr>
						<?php endif; ?>
						<?php if ( ! empty( $event['WooCommerceEventsLocation'] ) && trim( $event['WooCommerceEventsLocation'] ) !== '' ) : ?>
							<tr>
								<td><strong><?php esc_attr_e( 'Venue:', 'woocommerce-events' ); ?></strong></td>
								<td><?php echo( empty( $event['WooCommerceEventsLocation'] ) ) ? '' : esc_attr( $event['WooCommerceEventsLocation'] ); ?></td>
							</tr> 
						<?php endif; ?>
						<?php if ( ! empty( $event['WooCommerceEventsZoomText'] ) && trim( $event['WooCommerceEventsZoomText'] ) !== '' ) : ?>        
							<tr>
								<td valign="top"><strong><?php esc_attr_e( 'Zoom Meetings / Webinars: ', 'woocommerce-events' ); ?></strong></td>
								<td valign="top"><?php echo nl2br( wp_kses_post( $event['WooCommerceEventsZoomText'] ) ); ?></td>
							</tr>
						<?php endif; ?>
						<tr>
							<td colspan="2">
								<a href="<?php echo esc_attr( $event['WooCommerceEventsURL'] ); ?>" target="_BLANK" class="button">View</a>
								<a href="post.php?post=<?php echo esc_attr( $event['WooCommerceEventsProductID'] ); ?>&action=edit" target="_BLANK" class="button">Edit</a>
							</td>
						</tr>
					</table>
					<div class="clear"></div>
				</div>
			</div>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
	<?php endforeach; ?>
</div>