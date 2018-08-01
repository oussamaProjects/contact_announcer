<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://comenscene.com/
 * @since      1.0.0
 *
 * @package    Contact_announcer
 * @subpackage Contact_announcer/public/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
 


<?php
global $wpdb;
global $redux_demo; 
global $current_user; 
wp_get_current_user(); 

$user_ID = $current_user->ID;
$emailSent = false;

$profileLink = get_the_author_meta( 'user_url', $user_ID );
$contact_email = get_the_author_meta('user_email');
$classieraContactEmailError = __('Sorry, we don\'t accept empty email.', 'fellah');
$classieraContactNameError = __('Sorry, we don\'t accept empty name.', 'fellah');
$classieraConMsgError = __('Sorry, we don`\'t accept empty message.', 'fellah');
$classiera_contact_subject_error = __('Sorry, we don`\'t accept empty subject.', 'fellah');

$classieraContactThankyou = 'contact-thankyou-message';
$classieraRelatedCount = 'classiera_related_ads_count';
$category_icon_code = "";
$category_icon_color = "";
$your_image_url = "";

global $nameError;
global $emailError;
global $commentError;
global $subjectError;
global $humanTestError;
global $hasError;

//If the form is submitted
if (isset($_POST['submitted'])) {
	//Check to make sure that the name field is not empty
	if(trim($_POST['contactName']) === '') {
		$nameError = $classieraContactNameError;
		$hasError = true;
	} elseif(trim($_POST['contactName']) === 'Name*') {
		$nameError = $classieraContactNameError;
		$hasError = true;
	}	else {
		$name = trim($_POST['contactName']);
	}

	//Check to make sure that the subject field is not empty
	if(trim($_POST['subject']) === '') {
		$subjectError = $classiera_contact_subject_error;
		$hasError = true;
	} elseif(trim($_POST['subject']) === 'Subject*') {
		$subjectError = $classiera_contact_subject_error;
		$hasError = true;
	}	else {
		$subject = trim($_POST['subject']);
	}

	//Check to make sure sure that a valid email address is submitted
	if(trim($_POST['email']) === ''){
		$emailError = $classieraContactEmailError;
		$hasError = true;		
	}else{
		$email = trim($_POST['email']);
	}

	//Check to make sure comments were entered	
	if(trim($_POST['comments']) === '') {
		$commentError = $classieraConMsgError;
		$hasError = true;
	} else {
		if(function_exists('stripslashes')) {
			$comments = stripslashes(trim($_POST['comments']));
		} else {
			$comments = trim($_POST['comments']);
		}
	} 

	$classieraPostTitle = $_POST['classiera_post_title'];
	$classieraPostURL = $_POST['classiera_post_url']; 
	//If there is no error, send the email		


	if(!isset($hasError)) { 
			// form fields
		$contactName = trim($_POST['contactName']);
		$email       = trim($_POST['email']);
		$subject     = trim($_POST['subject']);
		$comments    = $_POST['comments'];

		/********** New User Registration Code Start **********/
		if ( ! is_user_logged_in() ) {
			if ( email_exists( $email )){
					// require_once(ABSPATH . WPINC . '/ms-functions.php');
				$user = get_user_by( 'email', $email );			
				$user_id = $user->ID;		 
			} else {
				$new_password = wp_generate_password( 12, false );

				$nameParts = explode(' ', $contactName);
				if (count($nameParts) > 1) {
					$first_name = $nameParts[0];
					unset($nameParts[0]);
					$last_name  = implode(' ', $nameParts);
				} else {
					$first_name = $contactName;
					$last_name  = '';
				}

				$username = $email;

				$userdata = array(
					'user_login'    => $email,
					'user_nicename' => $contactName,
					'user_email'    => $email,
					'display_name'  => $contactName,
					'phone'         => '',
					'nickname'      => $contactName,
					'first_name'    => $first_name,
					'last_name'     => $last_name,
					'user_pass'     => $new_password,
				);

				$user_id                     = wp_insert_user($userdata);
				$login_data['user_login']    = $username;
				$login_data['user_password'] = $new_password;
				$login_data['remember']      = true;
				$user_verify                 = wp_signon( $login_data, false ); 
			}
		} else {
			$user_id = get_current_user_id();
		}
		/********** New User Registration Code End **********/

		/********** New Message Code Start **********/

		$messageData['from_id']    = $user_id;
		$messageData['to_id']      = $post->post_author;
		$messageData['subject']    = $subject;
		$messageData['message']    = $comments;
		$messageData['created_at'] = $messageData['updated_at'] = date('Y-m-d H:i:s');
		$wpdb->insert( "{$wpdb->prefix}messages", $messageData, array('%d', '%d', '%s', '%s', '%s', '%s'));
		$messageId = $wpdb->insert_id; 
		
		/********** Notification Email to User Start **********/
		newMessageNotificationToUser($messageData['to_id'], $current_user->display_name); // notify user about new reply
		/********** Notification Email to User End **********/
		
		$emailSent = true;
		// echo "<pre>";
		// print_r($messageData); 
		// die();
	}
}



?>
<?php if (get_current_user_id() && get_current_user_id() > 0 && $post && $post->post_author == get_current_user_id()) { ?>

<?php } else { ?> 
	<div id="advertisement">

		<?php if (isset($emailSent) && $emailSent == true) { ?>
			<div data-alert class="advert_alert advert_success" style="display: block;">
				<?php esc_html_e( 'Your Message have been sent!', 'fellah' ); ?>
			</div>
		<?php } ?>

		<div class="adverts-single-actions">
			<a href="#" class="adverts-button adverts-show-contact-form">
				<?php _e('CONTACTER L\'ANNONCEUR','fellah'); ?> <span class="adverts-icon-down-open"></span>
			</a>
		</div>

		<div class="adverts-contact-box"> 
			<div class="message_contact_form">
				<form name="contactForm" action="<?php the_permalink(); ?>" id="contact-form" method="post" class="adverts-form adverts-form-aligned contactform" >
					<div class="row">
						<?php if($hasError == true && $emailSent != true) {?>
							<div class="col-md-12">
								<p>
									<?php 
									if(!empty($nameError)){
										echo $nameError."<br />";
									}
									if(!empty($subjectError)){
										echo $subjectError."<br />";
									}
									if(!empty($emailError)){
										echo $emailError."<br />";
									}
									if(!empty($commentError)){
										echo $commentError."<br />";
									}
									if(!empty($humanTestError)){
										echo $humanTestError."<br />";
									}
									?>
								</p>
							</div>
						<?php }?>

					
						
							<div class="col-md-12">
								<label for="contactName">
									<?php _e('Your name', 'fellah'); ?> <span class="adverts-form-required">*</span>
								</label>
								<input type="text" name="contactName" id="contactName" class="" required />
							</div><!--End Name-->
							<div class="col-md-12">
								<label for="email">
									<?php _e('Your email', 'fellah'); ?> <span class="adverts-form-required">*</span>
								</label>
								<input type="email" name="email" id="email" class="" required />
							</div><!--End Email-->
							<div class="col-md-12">
								<label for="subject">
									<?php _e('Subject', 'fellah'); ?> <span class="adverts-form-required">*</span>
								</label>
								<input type="text" name="subject" id="subject" class="" required />
							</div><!--End Subjext-->
							<div class="col-md-12">
								<label for="commentsText">
									<?php _e('Message', 'fellah'); ?> <span class="adverts-form-required">*</span>
								</label>
								<textarea name="comments" id="commentsText" cols="8" rows="5" required></textarea>
							</div><!--End Your Message-->

							<input type="hidden" name="classiera_post_title" id="classiera_post_title" value="<?php the_title(); ?>" />
							<input type="hidden" name="classiera_post_url" id="classiera_post_url" value="<?php the_permalink(); ?>"  />


							<?php 
							$classieraFirstNumber = rand(1,9);
							$classieraLastNumber = rand(1,9);
							$classieraNumberAnswer = $classieraFirstNumber + $classieraLastNumber;
							?> 
							<div class="col-md-12">
								<input class="button round btnfull" name="submitted" type="submit" value="<?php esc_html_e( 'Send Message', 'fellah' ); ?>" class="input-submit"/>
							</div>
				
					</div>
				</form>
			</div>
		</div><!-- End author Message --> 


	</div><!-- End advertisement --> 

<?php } ?>