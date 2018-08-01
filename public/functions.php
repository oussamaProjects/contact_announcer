<?php 
/* New message notification email to user, code start */
function newMessageNotificationToUser($userId, $senderName) {
	$blog_title    = get_bloginfo('name');
	$blog_url      = esc_url( home_url() ) ;
	$adminEmail    = get_bloginfo('admin_email');
	$logo          = get_custom_logo(); 
	$user          = get_user_by('id', $userId);
	// $email_subject = $senderName . ' ' . esc_html( translate( "has replied to your message.", 'classiera' ) );
	$email_subject = $senderName . __(' a répondu à votre message', 'fellah');
	ob_start();	
	include(get_template_directory() . '/templates/email/email-header.php');
	?>

	<div class="logo" style="background:#f0f0f0; padding:30px 0; text-align:center; margin-bottom:20px;">
		<?php if (!empty($logo)) { ?>
			<img src="<?php echo $logo; ?>" alt="Logo" />
		<?php } else { ?>		
			<img src="<?php echo get_template_directory_uri(); ?>/img/logo.png" alt="Logo" />		
		<?php } ?>
	</div>

	<div class="emContent" style="padding:0 15px; font-size:15px; font-family:Open Sans;"> 

		<p><?php echo get_user_meta( $userId, 'first_name', true ); ?> <?php echo get_user_meta( $userId, 'last_name', true ); ?> :<br><br></p>
		<p>
		<?php echo $senderName; ?> 
		<?php _e('vient de répondre a votre message.  Afin de pouvoir lui répondre, vous devrez vous connecter sur', 'fellah') ?>
		<a href="<?php echo get_permalink( 2285 ); ?>"><?php echo get_permalink( 2285 ); ?></a> 
		<?php _e('pour accéder à votre Messagerie.', 'fellah') ?>
		</p>
	</div>

	<?php
		include(get_template_directory() . '/templates/email/email-footer.php');
		$message = ob_get_contents();
		ob_end_clean();
		wp_mail($user->user_email, $email_subject, $message);
}