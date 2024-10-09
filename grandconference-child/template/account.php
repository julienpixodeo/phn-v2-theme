<?php
/* Template Name: Account */
get_header(); 
session_start();
if(isset($_SESSION['lan_st'])){
    $lan_st = $_SESSION['lan_st'];
}else{
    $lan_st = 'french';
}

if($lan_st === 'french'){
    $title = get_the_title();
    $order = 'Commande';
    $first_name = 'Prénom';
    $last_name = 'Nom';
    $email = 'Adresse Email';
    $password = 'Mot de passe nouveau';
    $confirm_password = 'Confirmez votre mot de passe';
    $submit = 'Modifier';
    $error_required = 'Ce champ est obligatoire.';
    $error_valid_email = 'Veuillez entrer une adresse email valide.';
    $error_min_pw = 'Votre mot de passe doit contenir au moins 8 caractères.';
    $error_equa_pw = 'Les mots de passe ne correspondent pas.';
    $success_update = 'Information mise à jour avec succès';
}else{
    $title = "Account";
    $order = 'Order';
    $first_name = 'First name';
    $last_name = 'Last name';
    $email = 'Email';
    $password = 'New password';
    $confirm_password = 'Confirm password';
    $submit = 'Save';
    $error_required = 'This field is required.';
    $error_valid_email = 'Please enter a valid email address.';
    $error_min_pw = 'Your password must contain at least 8 characters.';
    $error_equa_pw = 'Passwords do not match.';
    $success_update = 'Information updated successfully';
} 

?>
<div id="page_caption" class="">
	<div class="page_title_wrapper">
		<div class="standard_wrapper">
			<div class="page_title_inner">
				<div class="page_title_content">
					<h1 class="text-center">
						<?php echo $title; ?>
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
                            <li class="active">
                                <a href="<?php echo get_permalink(); ?>">
                                    <?php echo $title; ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo home_url('commande'); ?>"> 
                                    <?php echo $order; ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <form id="edit-client" action="edit-client" method="post">
                        <input type="hidden" class="error-required" value="<?php echo $error_required; ?>">
                        <input type="hidden" class="error-valid-email" value="<?php echo $error_valid_email; ?>">
                        <input type="hidden" class="error-min-pw" value="<?php echo $error_min_pw; ?>">
                        <input type="hidden" class="error-equa-pw" value="<?php echo $error_equa_pw; ?>">
                        <input type="hidden" class="success-update" value="<?php echo $success_update; ?>">
                        <input type="hidden" class="url-login" value="<?php echo home_url('se-connecter'); ?>">
                        <div class="box-field">
                            <label for=""><?php echo $first_name; ?></label>
                            <input id="first_name" type="text" name="first_name" value="<?php echo esc_attr($current_user->first_name); ?>" placeholder="<?php echo $first_name; ?>" autocomplete="off" required>
                        </div>
                        <div class="box-field">
                            <label for=""><?php echo $last_name; ?></label>
                            <input id="last_name" type="text" name="last_name" value="<?php echo esc_attr($current_user->last_name); ?>" placeholder="<?php echo $last_name; ?>" autocomplete="off" required>
                        </div>
                        <div class="box-field">
                            <label for=""><?php echo $email; ?></label>
                            <input id="email" type="email" name="email" value="<?php echo esc_attr($current_user->user_email); ?>" placeholder="<?php echo $email; ?>" autocomplete="off" required>
                        </div>
                        <div class="box-field">
                            <label for=""><?php echo $password; ?></label>
                            <input id="password" type="password" name="password" placeholder="<?php echo $password; ?>" autocomplete="off">
                        </div>
                        <div class="box-field">
                            <label for=""><?php echo $confirm_password; ?></label>
                            <input id="password_confirm" type="password" name="password_confirm" placeholder="<?php echo $confirm_password; ?>">
                        </div>

                        <div class="message"></div>
                        <input class="submit_button" type="submit" value="<?php echo $submit; ?>" name="submit">
                        <?php wp_nonce_field('edit_client_action', 'edit_client_nonce'); ?>
                    </form>
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
