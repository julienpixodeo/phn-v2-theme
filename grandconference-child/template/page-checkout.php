<?php
/* Template Name: Checkout */
/**
 * The main template file for display page.
 *
 * @package WordPress
*/

//Check if single attachment page
if($post->post_type == 'attachment')
{
	get_template_part("single-attachment");
	die;
}

//Check if content builder preview
if(isset($_GET['rel']) && !empty($_GET['rel']) && isset($_GET['ppb_preview']))
{
	get_template_part("page-preview");
	die;
}

//Check if content builder preview page
if(isset($_GET['ppb_preview_page']))
{
	get_template_part("page-preview-page");
	die;
}

/**
*	Get Current page object
**/
if(!is_null($post))
{
	$page_obj = get_page($post->ID);
}

$current_page_id = '';

/**
*	Get current page id
**/

if(!is_null($post) && isset($page_obj->ID))
{
	$current_page_id = $page_obj->ID;
}

get_header(); 
?>

<?php
//Get Page Menu Transparent Option
$page_menu_transparent = get_post_meta($current_page_id, 'page_menu_transparent', true);

//Get page header display setting
$page_title = get_the_title();
$page_show_title = get_post_meta($current_page_id, 'page_show_title', true);

if(empty($page_show_title))
{
	//Get current page tagline
	$page_tagline = get_post_meta($current_page_id, 'page_tagline', true);

	$pp_page_bg = '';
	//Get page featured image
	if(has_post_thumbnail($current_page_id, 'full'))
	{
		$image_id = get_post_thumbnail_id($current_page_id); 
		$image_thumb = wp_get_attachment_image_src($image_id, 'full', true);
		
		if(isset($image_thumb[0]) && !empty($image_thumb[0]))
		{
			$pp_page_bg = $image_thumb[0];
		}
	}
	
	//Check if add parallax effect
	$tg_page_header_bg_parallax = get_theme_mod('tg_page_header_bg_parallax', 1);
	
	//Check if enable content builder
	$ppb_enable = get_post_meta($current_page_id, 'ppb_enable', true);
	
	$grandconference_topbar = grandconference_get_topbar();
	$page_header_type = '';
	
	//Get header featured content
	$page_header_type = get_post_meta(get_the_ID(), 'page_header_type', true);
	
	$video_url = '';
				
	if($page_header_type == 'Youtube Video' OR $page_header_type == 'Vimeo Video')
	{
		//Add jarallax video script
		wp_enqueue_script("jarallax-video", get_template_directory_uri()."/js/jarallax-video.js", false, GRANDCONFERENCE_THEMEVERSION, true);
		
		if($page_header_type == 'Youtube Video')
		{
			$page_header_youtube = get_post_meta(get_the_ID(), 'page_header_youtube', true);
			$video_url = 'https://www.youtube.com/watch?v='.$page_header_youtube;
		}
		else
		{
			$page_header_vimeo = get_post_meta(get_the_ID(), 'page_header_vimeo', true);
			$video_url = 'https://vimeo.com/'.$page_header_vimeo;
		}
	}
?>
<div id="page_caption" class="<?php if(!empty($pp_page_bg) OR $page_header_type == 'Youtube Video' OR $page_header_type == 'Vimeo Video') { ?>hasbg <?php if(!empty($tg_page_header_bg_parallax)) { ?>parallax<?php } ?> <?php } ?> <?php if(!empty($grandconference_topbar)) { ?>withtopbar<?php } ?> <?php if(!empty($grandconference_screen_class)) { echo esc_attr($grandconference_screen_class); } ?> <?php if(!empty($grandconference_page_content_class)) { echo esc_attr($grandconference_page_content_class); } ?>" <?php if(!empty($pp_page_bg)) { ?>style="background-image:url(<?php echo esc_url($pp_page_bg); ?>);"<?php } ?> <?php if($page_header_type == 'Youtube Video' OR $page_header_type == 'Vimeo Video') { ?>data-jarallax-video="<?php echo esc_url($video_url); ?>"<?php } ?>>

	<?php
		if(empty($page_show_title))
		{
	?>
	<div class="page_title_wrapper">
		<div class="standard_wrapper">
			<div class="page_title_inner">
				<div class="page_title_content">
                    <h1>
						<?php 
						if(isset($wp->query_vars['order-received'])){
							$id_ticket = 0;
							$order_id = absint($wp->query_vars['order-received']);
							$order = wc_get_order( $order_id );
							foreach ( $order->get_items() as $item_id => $item ) {
								$product_id = $item->get_product_id();
								$type = get_post_meta($product_id, 'phn_type_product', true);
								if ($type === "event") {
									$id_ticket = get_post_meta($product_id, 'events_of_product', true);
									break;
								}
							}
							if($id_ticket == 0){
								$id_ticket = get_post_meta( $order_id, 'event_id_order', true );
							}
						}else{
							$id_ticket = id_ticket_in_cart();
						}
						session_start();
						if($id_ticket == 0){
							$id_ticket = $_SESSION['event_id'];
						}
						if($id_ticket != 0){
							$lan = get_field('language',$id_ticket);
							if($lan === 'french'){
								echo get_field('title_page_french');
							}else{
								echo esc_html($page_title);
							}
						}
						?>
					</h1>
				</div>
			</div>
		</div>
	</div>
	<?php
		}
	?>

</div>
<?php
}
?>

<?php
	//Check if use page builder
	$ppb_form_data_order = '';
	$ppb_form_item_arr = array();
	$ppb_enable = get_post_meta($current_page_id, 'ppb_enable', true);
	
	$grandconference_topbar = grandconference_get_topbar();
?>
<?php
	if(!empty($ppb_enable))
	{
		$grandconference_screen_class = grandconference_get_screen_class();
		grandconference_set_screen_class('ppb_wrapper');
		
		//if dont have password set
		if(!post_password_required())
		{
		wp_enqueue_script("grandconference-custom-onepage", get_template_directory_uri()."/js/custom_onepage.js", false, GRANDCONFERENCE_THEMEVERSION, true);
?>
<div class="ppb_wrapper <?php if(!empty($pp_page_bg)) { ?>hasbg<?php } ?> <?php if(!empty($pp_page_bg) && !empty($grandconference_topbar)) { ?>withtopbar<?php } ?>">
<?php
		grandconference_apply_builder($current_page_id);
?>
</div>
<?php		
		} //end if dont have password set
		else
		{
?>
<div id="page_content_wrapper" class="<?php if(!empty($pp_page_bg)) { ?>hasbg<?php } ?> <?php if(!empty($pp_page_bg) && !empty($grandconference_topbar)) { ?>withtopbar<?php } ?>">
	<div class="inner">
		<!-- Begin main content -->
		<div class="inner_wrapper">
			<div class="sidebar_content full_width"><br/><br/>
<?php
			the_content();
?>
			<br/><br/></div>
		</div>
	</div>
</div><br class="clear"/><br/>
<?php
		}
	}
	else
	{
?>
<!-- Begin content -->
<div id="page_content_wrapper" class="<?php if(!empty($pp_page_bg)) { ?>hasbg<?php } ?> <?php if(!empty($pp_page_bg) && !empty($grandconference_topbar)) { ?>withtopbar<?php } ?>">
	<div class="inner">
		<!-- Begin main content -->
		<div class="inner_wrapper">
			<div class="sidebar_content full_width">
			<?php 
				if ( have_posts() ) {
				while ( have_posts() ) : the_post(); ?>		
		
				<?php the_content(); break;  ?>

			<?php endwhile; 
			}

			if (comments_open($post->ID)) 
			{
			?>
			<div class="fullwidth_comment_wrapper">
				<?php comments_template( '', true ); ?>
			</div>
			<?php
			}
			?>
			</div>
		</div>
		<!-- End main content -->
	</div> 
</div><br class="clear"/>
<?php
}
?>
<?php 
echo footer_elementor();
get_footer(); 
?>