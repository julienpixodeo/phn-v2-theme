<?php
/* Template Name: Login */
get_header(); 
if (isset($_SERVER['HTTP_REFERER'])) {
    $referring_url = $_SERVER['HTTP_REFERER'];
} else {
    $referring_url = home_url();
}
session_start();
if(isset($_SESSION['lan_st'])){
    $lan_st = $_SESSION['lan_st'];
}else{
    $lan_st = 'french';
}
if($lan_st === 'french'){
    $title = get_the_title(); 
	$user_name = 'Nom d\'utilisateur';
	$password = 'Mot de passe';
	$create_account = 'Créer un compte ?';
	$btn_login = 'Se connecter';
	$error_required = 'Ce champ est obligatoire.';
	$message_error = 'Nom d\'utilisateur ou mot de passe incorrect.';
	$message_success = 'Connectez-vous avec succès';
}else{
    $title = "Login";
	$user_name = 'User name';
	$password = 'Password';
	$create_account = 'Create an account?';
	$btn_login = 'Login';
	$error_required = 'This field is required.';
	$message_error = 'Incorrect username or password.';
	$message_success = 'Login successfully';
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
                <form id="login" action="login" method="post">
					<input type="hidden" class="error-required" value="<?php echo $error_required; ?>">
					<input type="hidden" class="message-success" value="<?php echo $message_success; ?>">
                    <input type="hidden" value="<?php echo $referring_url; ?>" class="referring-url">
                    <div class="box-field">
                        <label for=""><?php echo $user_name; ?></label>
                        <input id="username" type="text" name="username" placeholder="<?php echo $user_name; ?>" autocomplete="off" required>
                    </div>
                    <div class="box-field">
                        <label for=""><?php echo $password; ?></label>
                        <input id="password" type="password" name="password" placeholder="<?php echo $password; ?>" autocomplete="off" required>
                    </div>
                    <!-- <a class="lost" href="<?php echo wp_lostpassword_url(); ?>">Mot de passe oublié ?</a> -->
                    <a class="lost" href="<?php echo home_url('creer-un-compte'); ?>"><?php echo $create_account; ?></a>
                    <div class="message"><?php echo $message_error; ?></div>
                    <input class="submit_button" type="submit" value="<?php echo $btn_login; ?>" name="submit">
                    <?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
                </form>
			</div>
		</div>
		<!-- End main content -->
	</div> 
</div>
<?php 
echo footer_elementor();
get_footer(); 
?>
