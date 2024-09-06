<?php
/* Template Name: Form Room Checkout */
get_header(); 
?>
<div id="page_caption" class="">
	<div class="page_title_wrapper">
		<div class="standard_wrapper">
			<div class="page_title_inner">
				<div class="page_title_content">
					<h1>
						<?php 
						$id_ticket = id_ticket_in_cart();
						if($id_ticket != 0){
							$lan = get_field('language',$id_ticket);
							if($lan === 'french'){
								echo get_field('title_page_french');
							}else{
								echo get_the_title();
							}
						}
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
				<?php echo multiple_forms_room_shortcode(); ?>
			</div>
		</div>
		<!-- End main content -->
	</div> 
</div>
<?php 
echo footer_elementor();
get_footer(); 
?>
