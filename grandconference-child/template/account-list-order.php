<?php
/* Template Name: Account list order*/
get_header(); 
session_start();
if(isset($_SESSION['lan_st'])){
    $lan_st = $_SESSION['lan_st'];
}else{
    $lan_st = 'french';
}

if($lan_st === 'french'){
    $title = get_the_title();
    $account = 'Compte';
	$ok = 'Remboursement';
	$close = 'Fermer';
}else{
    $title = "Order";
    $account = 'Account';
	$ok = 'Refund';
	$close = 'Close';
} 
?>
<div id="page_caption" class="">
	<div class="page_title_wrapper">
		<div class="standard_wrapper">
			<div class="page_title_inner">
				<div class="page_title_content">
					<h1 class="text-center">
						<?php 
						    echo $title;
						?>
					</h1>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="page_content_wrapper" class=" ">
	<div class="inner">
		<!-- Begin main content -->
		<div class="inner_wrapper">
			<div class="sidebar_content full_width">
                <?php
                    $current_user = wp_get_current_user();
                ?>
                <div class="wrap-content-sidebar">
                    <div class="sidebar-account">
                        <ul>
                            <li><a href="<?php echo home_url('compte'); ?>"><?php echo $account; ?></a></li>
                            <li class="active"><a href="<?php echo get_permalink(); ?>"><?php echo $title; ?></a></li>
                        </ul>
                    </div>
                    <div class="wrap-list-order">
						<?php echo do_shortcode('[user_orders]'); ?>

						<!-- Modal -->
						<div class="modal fade" id="modal-alert-refund" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-dialog-centered">
								<div class="modal-content">
								<div class="modal-header">
									<h1 class="modal-title fs-5" id="exampleModalLabel">Message</h1>
									<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
								</div>
								<div class="modal-body">
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $close; ?></button>
									<button type="button" class="btn btn-primary refund-button-action"><?php echo $ok; ?></button>
								</div>
								</div>
							</div>
						</div>
                    </div>
                </div>
			</div>
		</div>
		<!-- End main content -->
	</div> 
</div>
<?php 
echo footer_elementor();
get_footer(); 
?>
