<?php
class UserscontrolMessaging extends UserscontrolCommon 
{
	var $mHeader;
	var $mEmailPlainHTML;
	var $mHeaderSentFromName;
	var $mHeaderSentFromEmail;
	var $mCompanyName;
	
	var $include_ticket_subject;
	var $include_ticket_number;
	

	function __construct() 
	{
		$this->setContentType();
		$this->setFromEmails();				
		$this->set_headers();	
		
	}
	
	function setFromEmails() 
	{
		global $userscontrol;
			
		$from_name =  $this->get_option('messaging_send_from_name'); 
		$from_email = $this->get_option('messaging_send_from_email'); 	
		if ($from_email=="")
		{
			$from_email =get_option('admin_email');
			
		}		
		$this->mHeaderSentFromName=$from_name;
		$this->mHeaderSentFromEmail=$from_email;
		
		
    }
	
	function setContentType() 
	{
		global $userscontrol;			
				
		$this->mEmailPlainHTML="text/html";
    }
	
	/* get setting */
	function get_option($option) 
	{
		$settings = get_option('userscontrol_options');
		if (isset($settings[$option])) 
		{
			return $settings[$option];
			
		}else{
			
		    return '';
		}
		    
	}
	
	public function set_headers() 
	{   			
		//Make Headers aminnistrators	
		$headers[] = "Content-type: ".$this->mEmailPlainHTML."; charset=UTF-8";
		$headers[] = "From: ".$this->mHeaderSentFromName." <".$this->mHeaderSentFromEmail.">";
		$headers[] = "Organization: ".$this->mCompanyName;	
		$this->mHeader = $headers;		
    }
	
	
	public function  send ($to, $subject, $message)
	{
		global $userscontrol , $phpmailer;
		
		$message = nl2br($message);
		//check mailing method	
		$userscontrol_emailer = $userscontrol->get_option('userscontrol_smtp_mailing_mailer');
		
		if($userscontrol_emailer=='mail' || $userscontrol_emailer=='' ) //use the defaul email function
		{
			$err = wp_mail( $to , $subject, $message, $this->mHeader);	
		}elseif($userscontrol_emailer=='mandrill' && is_email($to)){ //send email via Mandrill
		
			$this->send_mandrill( $to , $recipient_name, $subject, $message);
		
		}elseif($userscontrol_emailer=='third-party' && is_email($to)){ //send email via Third-Party
		
			if (function_exists('userscontrol_third_party_email_sender')) 
			{
				
				userscontrol_third_party_email_sender($to , $subject, $message);				
				
			}
			
		}elseif($userscontrol_emailer=='smtp' &&  is_email($to)){ //send email via SMTP
		
			if ( !is_object( $phpmailer ) || !is_a( $phpmailer, 'PHPMailer' ) ) {

				global $wp_version;
				if( $wp_version < '5.5') {
					require_once(ABSPATH . WPINC . '/class-phpmailer.php');
					require_once(ABSPATH . WPINC . '/class-smtp.php');
					$phpmailer = new PHPMailer( true );
				}
				else {
					require_once(ABSPATH . WPINC . '/PHPMailer/PHPMailer.php');
					require_once(ABSPATH . WPINC . '/PHPMailer/SMTP.php');
					require_once(ABSPATH . WPINC . '/PHPMailer/Exception.php');
					$phpmailer = new PHPMailer\PHPMailer\PHPMailer( true );
				}

			}		
			
			
			$phpmailer->IsSMTP(); // use SMTP
			
			
			// Empty out the values that may be set
			$phpmailer->ClearAddresses();
			$phpmailer->ClearAllRecipients();
			$phpmailer->ClearAttachments();
			$phpmailer->ClearBCCs();			
			
			// Set the mailer type as per config above, this overrides the already called isMail method
			$phpmailer->Mailer = $userscontrol_emailer;
						
			$phpmailer->From     = $userscontrol->get_option('messaging_send_from_email');
			$phpmailer->FromName =  $userscontrol->get_option('messaging_send_from_name');
			
			//Set the subject line
			$phpmailer->Subject = $subject;			
			$phpmailer->CharSet     = 'UTF-8';
			
			//Set who the message is to be sent from
			//$phpmailer->SetFrom($phpmailer->FromName, $phpmailer->From);
			
			//Read an HTML message body from an external file, convert referenced images to embedded, convert HTML into a basic plain-text alternative body
			
			
			// Set the Sender (return-path) if required
			if ($userscontrol->get_option('userscontrol_smtp_mailing_return_path')=='1')
				$phpmailer->Sender = $phpmailer->From; 
			
			// Set the SMTPSecure value, if set to none, leave this blank
			$userscontrol_encryption = $userscontrol->get_option('userscontrol_smtp_mailing_encrytion');
			$phpmailer->SMTPSecure = $userscontrol_encryption == 'none' ? '' : $userscontrol_encryption;
			
			// If we're sending via SMTP, set the host
			if ($userscontrol_emailer == "smtp")
			{				
				// Set the SMTPSecure value, if set to none, leave this blank
				$phpmailer->SMTPSecure = $userscontrol_encryption == 'none' ? '' : $userscontrol_encryption;
				
				// Set the other options
				$phpmailer->Host = $userscontrol->get_option('userscontrol_smtp_mailing_host');
				$phpmailer->Port = $userscontrol->get_option('userscontrol_smtp_mailing_port');
				
				// If we're using smtp auth, set the username & password
				if ($userscontrol->get_option('userscontrol_smtp_mailing_authentication') == "true") 
				{
					$phpmailer->SMTPAuth = TRUE;
					$phpmailer->Username = $userscontrol->get_option('userscontrol_smtp_mailing_username');
					$phpmailer->Password = $userscontrol->get_option('userscontrol_smtp_mailing_password');
				}
				
			}
			
			//html plain text			
			$phpmailer->IsHTML(true);	
			$phpmailer->MsgHTML($message);	
			
			//Set who the message is to be sent to
			$phpmailer->AddAddress($to);
			
			//$phpmailer->SMTPDebug = 2;	
			
			//Send the message, check for errors
			if(!$phpmailer->Send()) {
			  echo esc_attr("Mailer Error: " . $phpmailer->ErrorInfo);
			  exit();
			} else {		  
			 
			}
			
		
			//exit;

		
		}
		
		
		
	}
	
	public function  send_mandrill ($to, $recipient_name, $subject, $message_html)
	{
		global $userscontrol , $phpmailer;
		require_once(userscontrol_path."libs/mandrill/Mandrill.php");
		
		$from_email     = $userscontrol->get_option('messaging_send_from_email');
		$from_name =  $userscontrol->get_option('messaging_send_from_name');
		$api_key =  $userscontrol->get_option('userscontrol_mandrill_api_key');
		
					
		$text_html =  $message_html;
		$text_txt =  "";
			
		
		try {
				$mandrill = new Mandrill($api_key);
				$message = array(
					'html' => $text_html,
					'text' => $text_txt,
					'subject' => $subject,
					'from_email' => $from_email,
					'from_name' => $from_name,
					'to' => array(
						array(
							'email' => $to,
							'name' => $recipient_name,
							'type' => 'to'
						)
					),
					'headers' => array('Reply-To' => $from_email, 'Content-type' => $this->mEmailPlainHTML),
					'important' => false,
					'track_opens' => null,
					'track_clicks' => null,
					'auto_text' => null,
					'auto_html' => null,
					'inline_css' => null,
					'url_strip_qs' => null,
					'preserve_recipients' => null,
					'view_content_link' => null,
					/*'bcc_address' => 'message.bcc_address@example.com',*/
					'tracking_domain' => null,
					'signing_domain' => null,
					'return_path_domain' => null
					/*'merge' => true,
					'global_merge_vars' => array(
						array(
							'name' => 'merge1',
							'content' => 'merge1 content'
						)
					),
					
					
					/*'google_analytics_domains' => array('example.com'),
					'google_analytics_campaign' => 'message.from_email@example.com',
					'metadata' => array('website' => 'www.example.com'),*/
					
				);
				$async = false;
				$ip_pool = 'Main Pool';
				$send_at = date("Y-m-d H:i:s");
				$result = $mandrill->messages->send($message, $async);
			} catch(Mandrill_Error $e) {
				// Mandrill errors are thrown as exceptions
				//echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
				// A mandrill error occurred: Mandrill_Unknown_Subaccount - No subaccount exists with the id 'customer-123'
				throw $e;
			}
	}
	
	//--- Parse Custom Fields
	public function  parse_custom_fields($content, $user )
	{
		global $userscontrol, $wptucomplement;
		
		if(isset($wptucomplement))
		{
			
			preg_match_all("/\[([^\]]*)\]/", $content, $matches);
			$results = $matches[1];			
			$custom_fields_col = array();
			
			foreach ($results as $field){
				
				//clean field
				$clean_field = str_replace("USERSCONTROL_CUSTOM_", "", $field);
				$custom_fields_col[] = $clean_field;
			
			}
			
			foreach ($custom_fields_col as $field)
			{
				//get field data from booking table				
				$field_data = $userscontrol->get_user_meta($usr->ID, $field);
				//replace data in template				
				$content = str_replace("[EASY_WPM_CUSTOM_".$field."]", $field_data, $content);				
			}
			
		}
		
		return $content;
		
	}
	
	
	//--- Reset Link	
	public function  send_reset_link($receiver, $link){
		global $userscontrol;
		
		$admin_email =get_option('admin_email'); 
		$company_name = $this->get_option('company_name');
		$company_phone = $this->get_option('company_phone');

		$site_url =site_url("/");
		
		$u_email = $receiver->user_email;
		
		$template_client =stripslashes($this->get_option('email_reset_link_message_body'));
		$subject = $this->get_option('email_reset_link_message_subject');
		
		$template_client = str_replace("{{userscontrol_staff_name}}", $receiver->display_name,  $template_client);				
		$template_client = str_replace("{{userscontrol_reset_link}}", $link,  $template_client);
		
		$template_client = str_replace("{{userscontrol_company_name}}", $company_name,  $template_client);
		$template_client = str_replace("{{userscontrol_company_phone}}", $company_phone,  $template_client);
		$template_client = str_replace("{{userscontrol_company_url}}", $site_url,  $template_client);	
		
		$this->send($u_email, $subject, $template_client);				
		
	}
	
	//--- Send Client Renewal Notice to the client
	public function  send_client_renewal_notice($receiver, $package, $subscription)
	{
		global $userscontrol;
		
		$admin_email =get_option('admin_email'); 
		$company_name = $this->get_option('company_name');
		$company_phone = $this->get_option('company_phone');

		$site_url =site_url("/");
		
		$u_email = $receiver->user_email;
		
		$template_c =stripslashes($this->get_option('email_package_renewal_body'));
		$subject = $this->get_option('email_package_renewal_subject');
		
		/*Update Expiration Dates*/							
		$valid_periods = array();
		$valid_periods  = $userscontrol->membership->get_periods($package);
		$new_period = 	 $valid_periods['starts'].'/'. $valid_periods['ends'];
		
		if( $package->membership_type=='recurring'){			
			$amount =  $userscontrol->get_formated_amount_with_currency($package->membership_subscription_amount);
	
	    }else{			
			$amount =  $userscontrol->get_formated_amount_with_currency($package->membership_initial_amount);
		}
		
		$template_c = str_replace("{{userscontrol_client_name}}", $receiver->display_name,  $template_c);
		$template_c = str_replace("{{userscontrol_subscription_name}}", $package->membership_name,  $template_c);	
		$template_c = str_replace("{{userscontrol_subscription_amount}}",$amount,  $template_c);			
		$template_c = str_replace("{{userscontrol_period}}", $new_period,  $template_c);
		
		$template_c = str_replace("{{userscontrol_company_name}}", $company_name,  $template_c);
		$template_c = str_replace("{{userscontrol_company_phone}}", $company_phone,  $template_c);
		$template_c = str_replace("{{userscontrol_company_url}}", $site_url,  $template_c);
		
		$template_c = $this->parse_custom_fields($template_c,$receiver);			
		
		$this->send($u_email, $subject, $template_c);				
		
	}
	
	//--- Send Admin Renewal Notice to the client
	public function  send_admin_renewal_notice($receiver, $package, $subscription)
	{
		global $userscontrol;
		
		$admin_email =get_option('admin_email'); 
		$company_name = $this->get_option('company_name');
		$company_phone = $this->get_option('company_phone');

		$site_url =site_url("/");
		
		$u_email = $receiver->user_email;
		
		$template_c =stripslashes($this->get_option('email_package_renewal_admin_body'));
		$subject = $this->get_option('email_package_renewal_admin_subject');
		
		/*Update Expiration Dates*/							
		$valid_periods = array();
		$valid_periods  = $userscontrol->membership->get_periods($package);
		$new_period = 	 $valid_periods['starts'].'/'. $valid_periods['ends'];
		
		if( $package->membership_type=='recurring'){			
			$amount =  $userscontrol->get_formated_amount_with_currency($package->membership_subscription_amount);
	
	    }else{			
			$amount =  $userscontrol->get_formated_amount_with_currency($package->membership_initial_amount);
		}
		
		$template_c = str_replace("{{userscontrol_client_name}}", $receiver->display_name,  $template_c);
		$template_c = str_replace("{{userscontrol_subscription_name}}", $package->membership_name,  $template_c);	
		$template_c = str_replace("{{userscontrol_subscription_amount}}",$amount,  $template_c);	
		$template_c = str_replace("{{userscontrol_subscription_id}}",$subscription->subscription_id,  $template_c);
		$template_c = str_replace("{{userscontrol_subscription_profile_id}}",$subscription->subscription_merchant_id ,  $template_c);		
		$template_c = str_replace("{{userscontrol_period}}", $new_period,  $template_c);
		
		$template_c = str_replace("{{userscontrol_company_name}}", $company_name,  $template_c);
		$template_c = str_replace("{{userscontrol_company_phone}}", $company_phone,  $template_c);
		$template_c = str_replace("{{userscontrol_company_url}}", $site_url,  $template_c);
		
		$template_c = $this->parse_custom_fields($template_c,$receiver);			
		
		$this->send($admin_email, $subject, $template_c);				
		
	}

	//--- Link Activation	
	public function  welcome_email_with_activation($u_email, $user_login, $user_pass,  $activation_link){
		global $userscontrol;			

		require_once(ABSPATH . 'wp-includes/link-template.php');			
		$admin_email =get_option('admin_email'); 
		
		$site_url =site_url("/");
		
		$subject = $this->get_option('messaging_welcome_email_client');
		if($subject==''){
					
		}

		$subject = __('Verify your account','users-control');	
		$subject_admin = __('New Account To Verify','users-control');
			
		//get welcome email
		$template_client =stripslashes($this->get_option('new_account_activation_link'));
		$template_admim = stripslashes($this->get_option('new_account_admin_moderation_admin'));
			
		$login_url =site_url("/");
			
		$template_client = str_replace("{{userscontrol_activation_url}}", $activation_link,  $template_client);
		$template_client = str_replace("{{userscontrol_user_email}}", $u_email,  $template_client);
		$template_client = str_replace("{{userscontrol_user_name}}", $user_login,  $template_client);
		$template_client = str_replace("{{userscontrol_pass}}", $user_pass,  $template_client);
		$template_client = str_replace("{{userscontrol_admin_email}}", $admin_email,  $template_client);
			
		//admin
		$template_admim = str_replace("{{userscontrol_user_email}}", $u_email,  $template_admim);
		$template_admim = str_replace("{{userscontrol_user_name}}", $user_login,  $template_admim);
		$template_admim = str_replace("{{userscontrol_admin_email}}", $admin_email,  $template_admim);					
			
		//send user
		$this->send($u_email, $subject, $template_client);			

	}
		
		//---  Activation	
		public function confirm_activation($u_email, $user_login)
		{
			global $userscontrol;
			
			require_once(ABSPATH . 'wp-includes/link-template.php');
			
			
			$admin_email =get_option('admin_email'); 
			
			//get welcome email
			$template_client =stripslashes($this->get_option('admin_account_active_message_body'));
			
			$login_url =site_url("/");
			
			$subject = __('Account Activation','users-control');	
			
			$template_client = str_replace("{{userscontrol_login_url}}", $login_url,  $template_client);				
			$template_client = str_replace("{{userscontrol_admin_email}}", $admin_email,  $template_client);
			$template_client = str_replace("{{userscontrol_user_email}}", $u_email,  $template_client);
			$template_client = str_replace("{{userscontrol_user_name}}", $user_login,  $template_client);
			
					
			
			$this->send($u_email, $subject, $template_client);
			
						
			
		}
		
		//---  Verification Success	
		public function  confirm_verification_sucess($u_email){
			global $userscontrol;			

			require_once(ABSPATH . 'wp-includes/link-template.php');
			$site_url =site_url("/");	
			$admin_email =get_option('admin_email'); 
			
			//get welcome email
			$template_client =stripslashes($this->get_option('account_verified_sucess_message_body'));
			
			$login_url =site_url("/");			
			$subject = __('Account Verified Successfully','users-control');			
			
			$template_client = str_replace("{{userscontrol_login_link}}", $login_url,  $template_client);				
			$template_client = str_replace("{{userscontrol_admin_email}}", $admin_email,  $template_client);		
			$this->send($u_email, $subject, $template_client);
		}
	
	
	//--- Send Client Purchase Notice to the client
	public function  send_client_purchase_notice($receiver, $package, $subscription){
		global $userscontrol;
		
		$admin_email =get_option('admin_email'); 
		$company_name = $this->get_option('company_name');
		$company_phone = $this->get_option('company_phone');

		$site_url =site_url("/");
		
		$u_email = $receiver->user_email;
		
		$template_c =stripslashes($this->get_option('email_package_upgrade_body'));
		$subject = $this->get_option('email_package_upgrade_subject');
		
		/*Update Expiration Dates*/							
		$valid_periods = array();
		$valid_periods  = $userscontrol->membership->get_periods($package);
		$new_period = 	 $valid_periods['starts'].'/'.$valid_periods['ends'];
		
		if( $package->membership_type=='recurring'){			
			$amount =  $userscontrol->get_formated_amount_with_currency($package->membership_subscription_amount);
	
	    }else{			
			$amount =  $userscontrol->get_formated_amount_with_currency($package->membership_initial_amount);
		}
		
		//get payment formated
		$formated_agreement =  $userscontrol->get_formated_agreement($package);
		
		$template_c = str_replace("{{userscontrol_client_name}}", $receiver->display_name,  $template_c);
		$template_c = str_replace("{{userscontrol_subscription_name}}", $package->membership_name,  $template_c);	
		$template_c = str_replace("{{userscontrol_subscription_amount}}",$amount,  $template_c);			
		$template_c = str_replace("{{userscontrol_subscription_id}}",$subscription->subscription_id,  $template_c);
		$template_c = str_replace("{{userscontrol_period}}", $new_period,  $template_c);
		$template_c = str_replace("{{userscontrol_subscription_agreement}}", $new_period,  $template_c);
		
		$template_c = str_replace("{{userscontrol_company_name}}", $company_name,  $template_c);
		$template_c = str_replace("{{userscontrol_company_phone}}", $company_phone,  $template_c);
		$template_c = str_replace("{{userscontrol_company_url}}", $site_url,  $template_c);
		
		$template_c = $this->parse_custom_fields($template_c,$receiver);			
		
		$this->send($u_email, $subject, $template_c);				
		
	}
	
	//--- Send Admin Purchase Notice
	public function  send_admin_purchase_notice($receiver, $package, $subscription)	{
		global $userscontrol;
		
		$admin_email =get_option('admin_email'); 
		$company_name = $this->get_option('company_name');
		$company_phone = $this->get_option('company_phone');
		
		$u_email = $receiver->user_email;

		$site_url =site_url("/");
		
		$template_c =stripslashes($this->get_option('email_package_upgrade_admin_body'));
		$subject = $this->get_option('email_package_upgrade_admin_subject');
		
		/*Update Expiration Dates*/							
		$valid_periods = array();
		$valid_periods  = $userscontrol->membership->get_periods($package);
		$new_period = 	 $valid_periods['starts'].'/'. $valid_periods['ends'];
		
		if( $package->membership_type=='recurring'){			
			$amount =  $userscontrol->get_formated_amount_with_currency($package->membership_subscription_amount);
	
	    }else{			
			$amount =  $userscontrol->get_formated_amount_with_currency($package->membership_initial_amount);
		}
		
		//get payment formated
		$formated_agreement =  $userscontrol->get_formated_agreement($package);
		
		$template_c = str_replace("{{userscontrol_client_name}}", $receiver->display_name,  $template_c);
		$template_c = str_replace("{{userscontrol_subscription_name}}", $package->membership_name,  $template_c);	
		$template_c = str_replace("{{userscontrol_subscription_amount}}",$amount,  $template_c);			
		$template_c = str_replace("{{userscontrol_period}}", $new_period,  $template_c);
		$template_c = str_replace("{{userscontrol_subscription_id}}",$subscription->subscription_id,  $template_c);
		$template_c = str_replace("{{userscontrol_subscription_agreement}}", $formated_agreement,  $template_c);
		$template_c = str_replace("{{userscontrol_company_name}}", $company_name,  $template_c);
		$template_c = str_replace("{{userscontrol_company_phone}}", $company_phone,  $template_c);
		$template_c = str_replace("{{userscontrol_company_url}}", $site_url,  $template_c);
		
		$template_c = $this->parse_custom_fields($template_c,$receiver);			
		
		$this->send($admin_email, $subject, $template_c);				
		
	}

	
	//--- Registration Link
	public function  send_client_registration_link($receiver, $link, $password)
	{
		global $userscontrol;
		
		$admin_email =get_option('admin_email'); 
		$company_name = $this->get_option('company_name');
		$company_phone = $this->get_option('company_phone');
		$site_url =site_url("/");
		
		$u_email = $receiver->user_email;
		
		$template_client =stripslashes($this->get_option('email_registration_body'));
		$subject = $this->get_option('email_registration_subject');
		
		$template_client = str_replace("{{userscontrol_client_name}}", $receiver->display_name,  $template_client);
		$template_client = str_replace("{{userscontrol_user_name}}", $receiver->user_login,  $template_client);	
		$template_client = str_replace("{{userscontrol_user_password}}", $password,  $template_client);			
		$template_client = str_replace("{{userscontrol_login_link}}", $link,  $template_client);
		
		$template_client = str_replace("{{userscontrol_company_name}}", $company_name,  $template_client);
		$template_client = str_replace("{{userscontrol_company_phone}}", $company_phone,  $template_client);
		$template_client = str_replace("{{userscontrol_company_url}}", $site_url,  $template_client);
		
		$template_client = $this->parse_custom_fields($template_client,$receiver);			
		
		$this->send($u_email, $subject, $template_client);				
		
	}
	
	
	//--- New Password Backend
	public function  send_new_password_to_user($staff, $password1){
		global $userscontrol;
				
		$admin_email =get_option('admin_email'); 
		$company_name = $this->get_option('company_name');
		$company_phone = $this->get_option('company_phone');
		
		//get templates	
		$template_client =stripslashes($this->get_option('email_password_change_member_body'));
		
		$site_url =site_url("/");
	
		$subject_client = $this->get_option('email_password_change_member_subject');				
		//client		
		$template_client = str_replace("{{userscontrol_user_name}}", $staff->display_name,  $template_client);	
		$template_client = str_replace("{{userscontrol_company_name}}", $company_name,  $template_client);
		$template_client = str_replace("{{userscontrol_company_phone}}", $company_phone,  $template_client);
		$template_client = str_replace("{{userscontrol_company_url}}", $site_url,  $template_client);										
		//send to client
		$this->send($staff->user_email, $subject_client, $template_client);		
		
	}
	
	
	
	
	
	
	public function  paypal_ipn_debug( $message)
	{
		global $userscontrol;
		$admin_email =get_option('admin_email');		
		$this->send($admin_email, "IPN notification", $message);					
		
	}
	
	public function  custom_email_message( $message, $subject)
	{
		global $userscontrol;
		$admin_email =get_option('admin_email');		
		$this->send($admin_email,  $subject, $message);					
		
	}
	
		

}

$key = "messaging";
$this->{$key} = new UserscontrolMessaging();
