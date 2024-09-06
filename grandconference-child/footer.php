<?php
/**
 * The template for displaying the footer.
 *
 * @package WordPress
 */
 
?>

<?php
	//Check if blank template
	$grandconference_is_no_header = grandconference_get_is_no_header();
	$grandconference_screen_class = grandconference_get_screen_class();
	
	if(!is_bool($grandconference_is_no_header) OR !$grandconference_is_no_header)
	{
	$grandconference_homepage_style = grandconference_get_homepage_style();
	
	$tg_footer_content = get_theme_mod('tg_footer_content', 'sidebar');
	$tg_footer_sidebar = get_theme_mod('tg_footer_sidebar', 3);
?>

<?php
	if($tg_footer_content != 'hide' && !empty($tg_footer_sidebar) && $grandconference_homepage_style != 'fullscreen' && $grandconference_homepage_style != 'fullscreen_white' && $grandconference_homepage_style != 'split')
	{
		$footer_class = '';
		
		switch($tg_footer_sidebar)
		{
			case 1:
				$footer_class = 'one';
			break;
			case 2:
				$footer_class = 'two';
			break;
			case 3:
				$footer_class = 'three';
			break;
			case 4:
				$footer_class = 'four';
			break;
			default:
				$footer_class = 'four';
			break;
		}
?>
<div id="footer" class="abc footer-<?php echo esc_attr($tg_footer_content); ?> <?php if(isset($grandconference_homepage_style) && !empty($grandconference_homepage_style)) { echo esc_attr($grandconference_homepage_style); } ?> <?php if(!empty($grandconference_screen_class)) { echo esc_attr($grandconference_screen_class); } ?>">
<?php
	//if using footer post content
	if($tg_footer_content == 'content')
	{
		if(is_page())
		{
			$tg_footer_content_default = get_post_meta($post->ID, 'page_footer', true);
			
			if(empty($tg_footer_content_default))
			{
				$tg_footer_content_default = get_theme_mod('tg_footer_content_default');
			}
		}
		else
		{
			$tg_footer_content_default = get_theme_mod('tg_footer_content_default');
		}
		
		//Add Polylang plugin support
		if (function_exists('pll_get_post')) {
			$tg_footer_content_default = pll_get_post($tg_footer_content_default);
		}
		
		//Add WPML plugin support
		if (function_exists('icl_object_id')) {
			$tg_footer_content_default = icl_object_id($tg_footer_content_default, 'page', false, ICL_LANGUAGE_CODE);
		}
	
		if(!empty($tg_footer_content_default) && class_exists("\\Elementor\\Plugin"))
		{
			echo grandconference_get_elementor_content($tg_footer_content_default);
		}	
	}
	//end if using footer post content
	
	//if use footer sidebar as content
	else if($tg_footer_content == 'sidebar')
	{
		if(is_active_sidebar('Footer Sidebar')) 
		{
?>
		<ul class="sidebar_widget <?php echo esc_attr($footer_class); ?>">
			<?php dynamic_sidebar('Footer Sidebar'); ?>
		</ul>
<?php
		}
	}
?>
</div>
<?php
	}
?>

<?php	
	//If display photostream
	$pp_photostream = get_option('pp_photostream');
	if(GRANDCONFERENCE_THEMEDEMO && isset($_GET['footer']) && !empty($_GET['footer']))
	{
		$pp_photostream = 0;
	}

	if(!empty($pp_photostream) && $grandconference_homepage_style != 'fullscreen' && $grandconference_homepage_style != 'fullscreen_white' && $grandconference_homepage_style != 'split')
	{
		$photos_arr = array();
	
		if($pp_photostream == 'flickr')
		{
			$pp_flickr_id = get_option('pp_flickr_id');
			$photos_arr = grandconference_get_flickr(array('type' => 'user', 'id' => $pp_flickr_id, 'items' => 30));
		}
		else
		{
			$pp_instagram_username = get_option('pp_instagram_username');
			$is_instagram_authorized = grandconference_check_instagram_authorization();
			
			if(is_bool($is_instagram_authorized) && $is_instagram_authorized)
			{
				$photos_arr = grandconference_get_instagram_using_plugin('photostream');
			}
			else
			{
				echo $is_instagram_authorized;
			}
		}
		
		if(!empty($photos_arr) && $grandconference_screen_class != 'split' && $grandconference_screen_class != 'split wide' && $grandconference_homepage_style != 'fullscreen' && $grandconference_homepage_style != 'flow')
		{
?>
<br class="clear"/>
<div id="footer_photostream" class="footer_photostream_wrapper ri-grid ri-grid-size-3">
	<ul>
		<?php
			foreach($photos_arr as $photo)
			{
		?>
			<li><a target="_blank" href="<?php echo esc_url($photo['link']); ?>"><img src="<?php echo esc_url($photo['thumb_url']); ?>" alt="" /></a></li>
		<?php
			}
		?>
	</ul>
</div>
<?php
		}
	}
?>

<?php
if($grandconference_homepage_style != 'fullscreen' && $grandconference_homepage_style != 'fullscreen_white' && $grandconference_homepage_style != 'split' && $tg_footer_content == 'sidebar')
{
	//Get Footer Sidebar
	$tg_footer_sidebar = get_theme_mod('tg_footer_sidebar', 3);
	if(GRANDCONFERENCE_THEMEDEMO && isset($_GET['footer']) && !empty($_GET['footer']))
	{
		$tg_footer_sidebar = 0;
	}
?>
<div class="footer_bar <?php if(isset($grandconference_homepage_style) && !empty($grandconference_homepage_style)) { echo esc_attr($grandconference_homepage_style); } ?> <?php if(!empty($grandconference_screen_class)) { echo esc_attr($grandconference_screen_class); } ?> <?php if(empty($tg_footer_sidebar)) { ?>noborder<?php } ?>">

	<div class="footer_bar_wrapper <?php if(isset($grandconference_homepage_style) && !empty($grandconference_homepage_style)) { echo esc_attr($grandconference_homepage_style); } ?>">
		<?php
			//Check if display social icons or footer menu
			$tg_footer_copyright_right_area = get_theme_mod('tg_footer_copyright_right_area', 'menu');
			
			if($tg_footer_copyright_right_area=='social')
			{
				if($grandconference_homepage_style!='flow' && $grandconference_homepage_style!='fullscreen' && $grandconference_homepage_style!='carousel' && $grandconference_homepage_style!='flip' && $grandconference_homepage_style!='fullscreen_video')
				{	
					//Check if open link in new window
					$tg_footer_social_link = get_theme_mod('tg_footer_social_link', 1);
			?>
			<div class="social_wrapper">
				<ul>
					<?php
						$pp_facebook_url = get_option('pp_facebook_url');
						
						if(!empty($pp_facebook_url))
						{
					?>
					<li class="facebook"><a <?php if(!empty($tg_footer_social_link)) { ?>target="_blank"<?php } ?> href="<?php echo esc_url($pp_facebook_url); ?>"><i class="fa fa-facebook-official"></i></a></li>
					<?php
						}
					?>
					<?php
						$pp_twitter_username = get_option('pp_twitter_username');
						
						if(!empty($pp_twitter_username))
						{
					?>
					<li class="twitter"><a <?php if(!empty($tg_footer_social_link)) { ?>target="_blank"<?php } ?> href="https://twitter.com/<?php echo esc_attr($pp_twitter_username); ?>"><i class="fa fa-twitter"></i></a></li>
					<?php
						}
					?>
					<?php
						$pp_flickr_username = get_option('pp_flickr_username');
						
						if(!empty($pp_flickr_username))
						{
					?>
					<li class="flickr"><a <?php if(!empty($tg_footer_social_link)) { ?>target="_blank"<?php } ?> title="Flickr" href="https://flickr.com/people/<?php echo esc_attr($pp_flickr_username); ?>"><i class="fa fa-flickr"></i></a></li>
					<?php
						}
					?>
					<?php
						$pp_youtube_url = get_option('pp_youtube_url');
						
						if(!empty($pp_youtube_url))
						{
					?>
					<li class="youtube"><a <?php if(!empty($tg_footer_social_link)) { ?>target="_blank"<?php } ?> title="Youtube" href="<?php echo esc_url($pp_youtube_url); ?>"><i class="fa fa-youtube"></i></a></li>
					<?php
						}
					?>
					<?php
						$pp_vimeo_username = get_option('pp_vimeo_username');
						
						if(!empty($pp_vimeo_username))
						{
					?>
					<li class="vimeo"><a <?php if(!empty($tg_footer_social_link)) { ?>target="_blank"<?php } ?> title="Vimeo" href="https://vimeo.com/<?php echo esc_attr($pp_vimeo_username); ?>"><i class="fa fa-vimeo-square"></i></a></li>
					<?php
						}
					?>
					<?php
						$pp_tumblr_username = get_option('pp_tumblr_username');
						
						if(!empty($pp_tumblr_username))
						{
					?>
					<li class="tumblr"><a <?php if(!empty($tg_footer_social_link)) { ?>target="_blank"<?php } ?> title="Tumblr" href="https://<?php echo esc_attr($pp_tumblr_username); ?>.tumblr.com"><i class="fa fa-tumblr"></i></a></li>
					<?php
						}
					?>
					<?php
						$pp_dribbble_username = get_option('pp_dribbble_username');
						
						if(!empty($pp_dribbble_username))
						{
					?>
					<li class="dribbble"><a <?php if(!empty($tg_footer_social_link)) { ?>target="_blank"<?php } ?> title="Dribbble" href="https://dribbble.com/<?php echo esc_attr($pp_dribbble_username); ?>"><i class="fa fa-dribbble"></i></a></li>
					<?php
						}
					?>
					<?php
						$pp_linkedin_url = get_option('pp_linkedin_url');
						
						if(!empty($pp_linkedin_url))
						{
					?>
					<li class="linkedin"><a <?php if(!empty($tg_footer_social_link)) { ?>target="_blank"<?php } ?> title="Linkedin" href="<?php echo esc_url($pp_linkedin_url); ?>"><i class="fa fa-linkedin"></i></a></li>
					<?php
						}
					?>
					<?php
						$pp_pinterest_username = get_option('pp_pinterest_username');
						
						if(!empty($pp_pinterest_username))
						{
					?>
					<li class="pinterest"><a <?php if(!empty($tg_footer_social_link)) { ?>target="_blank"<?php } ?> title="Pinterest" href="https://pinterest.com/<?php echo esc_attr($pp_pinterest_username); ?>"><i class="fa fa-pinterest"></i></a></li>
					<?php
						}
					?>
					<?php
						$pp_instagram_username = get_option('pp_instagram_username');
						
						if(!empty($pp_instagram_username))
						{
					?>
					<li class="instagram"><a <?php if(!empty($tg_footer_social_link)) { ?>target="_blank"<?php } ?> title="Instagram" href="https://instagram.com/<?php echo esc_attr($pp_instagram_username); ?>"><i class="fa fa-instagram"></i></a></li>
					<?php
						}
					?>
					<?php
						$pp_behance_username = get_option('pp_behance_username');
						
						if(!empty($pp_behance_username))
						{
					?>
					<li class="behance"><a <?php if(!empty($tg_footer_social_link)) { ?>target="_blank"<?php } ?> title="Behance" href="https://behance.net/<?php echo esc_attr($pp_behance_username); ?>"><i class="fa fa-behance-square"></i></a></li>
					<?php
						}
					?>
					<?php
						$pp_500px_url = get_option('pp_500px_url');
						
						if(!empty($pp_500px_url))
						{
					?>
					<li class="500px"><a <?php if(!empty($tg_footer_social_link)) { ?>target="_blank"<?php } ?> title="500px" href="<?php echo esc_url($pp_500px_url); ?>"><i class="fa fa-500px"></i></a></li>
					<?php
						}
					?>
				</ul>
			</div>
		<?php
				}
			} //End if display social icons
			else
			{
				if ( has_nav_menu( 'footer-menu' ) ) 
				{
					wp_nav_menu( 
							array( 
								'menu_id'			=> 'footer_menu',
								'menu_class'		=> 'footer_nav',
								'theme_location' 	=> 'footer-menu',
							) 
					); 
				}
			}
		?>
		<?php
			//Display copyright text
			$tg_footer_copyright_text = get_theme_mod('tg_footer_copyright_text');

			if(!empty($tg_footer_copyright_text))
			{
				echo '<div id="copyright">'.wp_kses_post(htmlspecialchars_decode($tg_footer_copyright_text)).'</div><br class="clear"/>';
			}
		?>
	</div>
</div>
<?php
}
?>
</div>

<?php
	//Check if display to top button
	$tg_footer_copyright_totop = get_theme_mod('tg_footer_copyright_totop', 1);
	
	if(!empty($tg_footer_copyright_totop))
	{
?>
	<a id="toTop" href="javascript:;"><i class="fa fa-angle-up"></i></a>
<?php
	}
?>

<?php
	} //End if not blank template
?>

<div id="side_menu_wrapper" class="overlay_background">
	<a id="close_share" href="javascript:;"><span class="ti-close"></span></a>
	<?php
		if(is_single())
		{
	?>
	<div id="fullscreen_share_wrapper">
		<div class="fullscreen_share_content">
		<?php
			get_template_part("/templates/template-share");
		?>
		</div>
	</div>
	<?php
		}
	?>
</div>

<?php
	//Check if theme demo then enable layout switcher
	if(GRANDCONFERENCE_THEMEDEMO)
	{	
?>
	<div id="option_wrapper">
	<div class="inner">
		<div style="text-align:center">
			<div class="purchase_theme_button">
				<a class="button" href="<?php echo esc_url(THEMEGOODS_PURCHASE_URL); ?>" target="_blank">Purchase Theme $59</a>
			</div>
			
			<h5>Ready to use Websites</h5>
			<div class="demo_desc">
				Here are example of predefined websites that can be installed within one click.
			</div>
			<?php
				$customizer_styling_arr = array( 
					array(
						'id'	=>	15, 
						'image' => 'https://grandconference.themegoods.com/v5/landing/wp-content/uploads/sites/9/2024/01/Grand-Conference-City-Events-WordPress-Theme.jpg', 
						'url' => grandconference_get_demo_url('grandconferencev5-2', 'multi-events'),
						'label' => 'New',
					),
					array(
						'id'	=>	14, 
						'image' => 'https://grandconference.themegoods.com/v5/landing/wp-content/uploads/sites/9/2024/01/Grand-Conference-Multi-Events-WordPress-Theme.jpg', 
						'url' => grandconference_get_demo_url('grandconferencev5-2', 'multi-events'),
						'label' => 'New',
					),
					array(
						'id'	=>	13, 
						'image' => 'https://grandconference.themegoods.com/v5/landing/wp-content/uploads/sites/9/2024/01/Grand-Conference-Education-Event-Conference-WordPress-Theme.jpg', 
						'url' => grandconference_get_demo_url('grandconferencev5-2', ''),
						'label' => 'New',
					),
					array(
						'id'	=>	12, 
						'image' => 'https://grandconference.themegoods.com/v5/landing/wp-content/uploads/sites/9/2022/02/Grand-Conference-Design-Awards-Conference-WordPress-Theme-%E2%80%93-Just-another-Grand-Conference-Event-Conference-WordPress-Theme-Sites-site.jpg', 
						'url' => grandconference_get_demo_url('grandconference', 'v5/awards'),
					),
					array(
						'id'	=>	11, 
						'image' => 'https://grandconference.themegoods.com/v5/landing/wp-content/uploads/sites/9/2022/02/Grand-Conference-Design-Conference-WordPress-Theme-%E2%80%93-Just-another-Grand-Conference-Event-Conference-WordPress-Theme-Sites-site.jpg', 
						'url' => grandconference_get_demo_url('grandconference', 'v5/design'),
					),
					array(
						'id'	=>	10, 
						'image' => 'https://grandconference.themegoods.com/v5/landing/wp-content/uploads/sites/9/2022/02/Grand-Conference-Business-Conference-WordPress-Theme-%E2%80%93-Just-another-Grand-Conference-Event-Conference-WordPress-Theme-Sites-site.jpg', 
						'url' => grandconference_get_demo_url('grandconference', 'v5/business'),
					),
					array(
						'id'	=>	9, 
						'image' => 'https://grandconference.themegoods.com/v5/landing/wp-content/uploads/sites/9/2022/02/Home-5-%E2%80%93-Business-Forum-%E2%80%93-Grand-Conference-Event-Conference-WordPress-Theme.jpg', 
						'url' => grandconference_get_demo_url('grandconference', 'v5/home-5-business-forum'),
					),
					array(
						'id'	=>	8, 
						'image' => 'https://grandconference.themegoods.com/v5/landing/wp-content/uploads/sites/9/2022/02/Grand-Conference-Grand-Digital-Tech-Event-WordPress-Theme-%E2%80%93-Just-another-Grand-Conference-Event-Conference-WordPress-Theme-Sites-site.jpg', 
						'url' => grandconference_get_demo_url('grandconference', 'v5/digital'),
					),
					array(
						'id'	=>	7, 
						'image' => 'https://grandconference.themegoods.com/v5/landing/wp-content/uploads/sites/9/2022/02/Grand-Conference-Grand-Tech-Event-WordPress-Theme-%E2%80%93-Just-another-Grand-Conference-Event-Conference-WordPress-Theme-Sites-site-1.jpg', 
						'url' => grandconference_get_demo_url('grandconference', 'v5/grandtech'),
					),
					array(
						'id'	=>	6, 
						'image' => 'https://grandconference.themegoods.com/v5/landing/wp-content/uploads/sites/9/2022/02/Grand-Conference-VR-Event-WordPress-Theme-%E2%80%93-Just-another-Grand-Conference-Event-Conference-WordPress-Theme-Sites-site.jpg', 
						'url' => grandconference_get_demo_url('grandconference', 'v5/vr'),
					),
					array(
						'id'	=>	5, 
						'image' => 'https://grandconference.themegoods.com/v5/landing/wp-content/uploads/sites/9/2022/02/Grand-Conference-Music-Event-WordPress-Theme-%E2%80%93-Just-another-Grand-Conference-Event-Conference-WordPress-Theme-Sites-site.jpg', 
						'url' => grandconference_get_demo_url('grandconference', 'v5/music'),
					),
					array(
						'id'	=>	4, 
						'image' => 'https://grandconference.themegoods.com/v5/landing/wp-content/uploads/sites/9/2022/02/Grand-Conference-Event-Conference-WordPress-Theme-%E2%80%93-Just-another-WordPress-site.jpg', 
						'url' => grandconference_get_demo_url('grandconference', 'v5'),
					),
					array(
						'id'	=>	3, 
						'image' => 'https://grandconference.themegoods.com/v5/landing/wp-content/uploads/sites/9/2022/02/Home-4-%E2%80%93-Grand-Conference-Event-Conference-WordPress-Theme.jpg', 
						'url' => grandconference_get_demo_url('grandconference', 'v5/home-4'),
					),
					array(
						'id'	=>	2, 
						'image' => 'https://grandconference.themegoods.com/v5/landing/wp-content/uploads/sites/9/2022/03/Home-3-%E2%80%93-Grand-Conference-Event-Conference-WordPress-Theme.jpg', 
						'url' => grandconference_get_demo_url('grandconference', 'v5/home-3'),
					),
					array(
						'id'	=>	1, 
						'image' => 'https://grandconference.themegoods.com/v5/landing/wp-content/uploads/sites/9/2022/02/Home-2-%E2%80%93-Grand-Conference-Event-Conference-WordPress-Theme.jpg', 
						'url' => grandconference_get_demo_url('grandconference', 'v5/home-2'),
					),
				);
			?>
			<ul class="demo_list">
				<?php
					foreach($customizer_styling_arr as $customizer_styling)
					{
				?>
				<li>
					<a href="<?php echo esc_url($customizer_styling['url']); ?>" target="_blank">
						<img src="<?php echo esc_url($customizer_styling['image']); ?>" alt="" class="no_blur"/>
						<?php
							if(isset($customizer_styling['label']))	{
						?>
							<div class="demo_label"><?php echo esc_html($customizer_styling['label']); ?></div>
						<?php
							}
						?>
					</a>  
				</li>
				<?php
					}
				?>
			</ul>
		</div>
	</div>
	</div>
	<div id="option_btn">
		<a href="javascript:;" class="demotip" title="Choose Theme Demos"><span class="ti-settings"></span></a>
		
		<a href="https://docs.themegoods.com/grand-conference" class="demotip" title="Theme Documentation" target="_blank"><span class="ti-book"></span></a>
		
		<a href="<?php echo esc_url(THEMEGOODS_PURCHASE_URL); ?>" title="Purchase Theme" class="demotip" target="_blank"><span class="ti-shopping-cart"></span></a>
	</div>
<?php
		wp_enqueue_script("grandconference-jquery-cookie", get_template_directory_uri()."/js/jquery.cookie.js", false, GRANDCONFERENCE_THEMEVERSION, true);
		wp_enqueue_script("grandconference-script-demo", get_template_directory_uri()."/js/custom-demo.js", false, GRANDCONFERENCE_THEMEVERSION, true);
	}
?>

<?php
	//Display fullscreen menu
	$tg_fullmenu_default = get_theme_mod('tg_fullmenu_default');
	
	if(is_page())
	{
		$tg_fullmenu_default = get_post_meta($post->ID, 'page_fullmenu', true);
		
		if(empty($tg_fullmenu_default))
		{
			$tg_fullmenu_default = get_theme_mod('tg_fullmenu_default');
		}
	}
	else
	{
		$tg_fullmenu_default = get_theme_mod('tg_fullmenu_default');
	}
	
	if(!empty($tg_fullmenu_default))
	{
		//Add Polylang plugin support
		if (function_exists('pll_get_post')) {
			$tg_fullmenu_default = pll_get_post($tg_fullmenu_default);
		}
		
		//Add WPML plugin support
		if (function_exists('icl_object_id')) {
			$tg_fullmenu_default = icl_object_id($tg_fullmenu_default, 'page', false, ICL_LANGUAGE_CODE);
		}
		
		if(!empty($tg_fullmenu_default) && class_exists("\\Elementor\\Plugin"))
		{
?>
	<div id="fullmenu-wrapper-<?php echo esc_attr($tg_fullmenu_default); ?>" class="fullmenu-wrapper">
<?php
			echo grandconference_get_elementor_content($tg_fullmenu_default);
?>
	</div>
<?php
		}
	}
?>

<?php
	$tg_frame = get_theme_mod('tg_frame', 0);
	if(GRANDCONFERENCE_THEMEDEMO && isset($_GET['frame']) && !empty($_GET['frame']))
	{
		$tg_frame = 1;
	}
	
	if(!empty($tg_frame))
	{
?>
	<div class="frame_top"></div>
	<div class="frame_bottom"></div>
	<div class="frame_left"></div>
	<div class="frame_right"></div>
<?php
	}
?>

<?php
	/* Always have wp_footer() just before the closing </body>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to reference JavaScript files.
	 */

	wp_footer();
?>
</body>
</html>
