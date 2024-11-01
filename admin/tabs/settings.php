<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $userscontrol, $userscontrol_activation, $userscontrol_aweber, $userscontrol_mailchimp, $userscontrol_recaptcha;
?>
<h3><?php _e('Plugin Settings','users-control'); ?></h3>
<form method="post" action="">
<input type="hidden" name="userscontrol_update_settings" />


<div id="userscontrol-bupro-settings" class="userscontrol-multi-tab-options">

<ul class="nav-tab-wrapper bup-nav-pro-features">

<li class="nav-tab bup-pro-li"><a href="#tabs-1" title="<?php _e('General','users-control'); ?>"><?php _e('General','users-control'); ?></a></li>

<li class="nav-tab bup-pro-li"><a href="#tabs-messaging" title="<?php _e('Advanced Messaging Settings','users-control'); ?>"><?php _e('Advanced Messaging Settings','users-control'); ?></a></li>


<li class="nav-tab bup-pro-li"><a href="#tabs-bup-newsletter" title="<?php _e('Newsletter','users-control'); ?>"><?php _e('Newsletter','users-control'); ?> </a></li>

<li class="nav-tab bup-pro-li"><a href="#tabs-userscontrol-recaptcha" title="<?php _e('reCaptcha','users-control'); ?>"><?php _e('reCaptcha','users-control'); ?> </a></li>



</ul>



<div id="tabs-userscontrol-recaptcha">


<div class="userscontrol-sect  userscontrol-welcome-panel">
  <h3><?php _e('reCaptcha','users-control'); ?></h3>
  
  <?php if(!isset($userscontrol_recaptcha)){
	  
	  $html = '<div class="userscontrol-ultra-warning">'. __("Please make sure that Users Control reCaptcha (Add-on) plugin is active.", 'users-control').'</div>';
          echo wp_kses($html, $userscontrol->allowed_html); ;
	  ?>
  
  
  
  <?php }?>
  
    
  <p><?php _e('This is a free add-on which was developed to help you to protect your ticket system against spammers.','users-control'); ?></p>
  
    <p><?php _e("You can get the Site Key and Secret Key on Google reCaptcha Dashboard",'users-control'); ?>. <a href="https://www.google.com/recaptcha/admin" target="_blank"> <?php _e("Click here",'users-control'); ?> </a> </p>
    
    <p><?php _e("You may check the reCaptcha setup tutorial as well. ",'users-control'); ?> <a href="http://docs.userscontrol.com/installing-recaptcha/" target="_blank"> <?php _e("Click here",'users-control'); ?> </a> </p>
  
  
  
  <table class="form-table">
<?php


	$this->create_plugin_setting(
			'input',
			'recaptcha_site_key',
			__('Site Key:','users-control'),array(),
			__('Enter your site key here.','users-control'),
			__('Enter your site key here.','users-control')
	);
	
	$this->create_plugin_setting(
			'input',
			'recaptcha_secret_key',
			__('Secret Key:','users-control'),array(),
			__('Enter your site secret here.','users-control'),
			__('Enter your site secret here.','users-control')
	);

	
?>
</table>
</div>


<div class="userscontrol-sect  userscontrol-welcome-panel">
  <h3><?php _e('Where to display?','users-control'); ?></h3>
  
    
  <p><?php _e('Select what forms will be protected by reCaptcha','users-control'); ?></p>
  
  <table class="form-table">
<?php


	$this->create_plugin_setting(
                'checkbox',
                'recaptcha_display_registration',
                __('Registration Form','users-control'),
                '1',
                __('If checked, the reCaptcha will be displayed in the registration form.','users-control'),
                __('If checked, the reCaptcha will be displayed in the registration form.','users-control')
        );
		
	$this->create_plugin_setting(
                'checkbox',
                'recaptcha_display_loginform',
                __('Login Form','users-control'),
                '1',
                __('If checked, the reCaptcha will be displayed in the login form.','users-control'),
                __('If checked, the reCaptcha will be displayed in the login form.','users-control')
        );
		
	
	$this->create_plugin_setting(
                'checkbox',
                'recaptcha_display_forgot_password',
                __('Forgot Password Form','users-control'),
                '1',
                __('If checked, the reCaptcha will be displayed in the forgot password form.','users-control'),
                __('If checked, the reCaptcha will be displayed in the forgot password form.','users-control')
        ); 
		
	
	$this->create_plugin_setting(
                'checkbox',
                'recaptcha_display_comments_native',
                __('Comments','ultimate-captcha'),
                '1',
                __('If checked, the reCaptcha will be displayed in the comments form.','users-control'),
                __('If checked, the reCaptcha will be displayed in the comments form.','users-control')
        ); 
	
?>
</table>
</div>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','users-control'); ?>"  />
</p>

  
</div>


<div id="tabs-1">




<div class="userscontrol-sect  userscontrol-welcome-panel">   
   
   <h3><?php _e('Registration Settings','users-control'); ?></h3>  
   <p><?php _e('These settings allow you to define rules on how the members can register in your website.','users-control'); ?></p>
 
 
   <table class="form-table">
 <?php
 
 
$this->create_plugin_setting(
	'select',
	'registration_rules',
	__('Registration Type','users-control'),
	array(
		
		4 => __('Paid Subscriptions - Enables the Subscriptions Features','users-control'),
		1=> __('Disable Paid Subscriptions - This will remove the Payment Options','users-control')),
		
		
	__('Please note: If you disable the Paid Subscriptions the subscriptions plans and the payment methods will be removed from the reistration form.','users-control'),
  __('Please note: If you disable the Paid Subscriptions the subscriptions plans and the payment methods will be removed from the reistration form.','users-control')
       );

       $this->create_plugin_setting(
	'select',
	'activation_method',
	__('Activation Rule','users-control'),
	array(
		1 => __('Login automatically after registration','users-control'), 
		2 => __('E-mail Activation -  A confirmation link is sent to the user email','users-control'),
		3 => __('Manual Activation - The admin approves the accounts manually','users-control'),
		4 => __('Send Credentials to email - Emai with username and password is sent','users-control')),
		
	__('Please note: Paid Activation does not work with social connects at this moment.','users-control'),
  __('Please note: Paid Activation does not work with social connects at this moment.','users-control')
       );
	   
	   

?>
 
 </table>
 
  
</div>


<div class="userscontrol-sect  userscontrol-welcome-panel">   
   
   <h3><?php _e('Redirection Settings','users-control'); ?></h3>  
   <p><?php _e('These settings allow you to define rules on how the members will be redirected after the registration','users-control'); ?></p>
 
 
   <table class="form-table">
 <?php
 
 
        $this->create_plugin_setting(
                'select',
                'redirect_after_registration_login',
                __('After Registration Page','users-control'),
                $this->get_all_sytem_pages(),
                __('The user will be taken to this page after registration if the account activation is set to automatic ','users-control'),
                __('The user will be taken to this page after registration if the account activation is set to automatic ','users-control')
        );

        $this->create_plugin_setting(
                'select',
                'redirect_confirm_email_account',
                __('Confirm Email After Registration Page','users-control'),
                $this->get_all_sytem_pages(),
                __('The user will be taken to this page after registration with custom message for account verification.','users-control'),
                __('The user will be taken to this page after registration with custom message for account verification.','users-control')
        );

?>
 
 </table>
 
  
</div>





<div class="userscontrol-sect  userscontrol-welcome-panel">
  <h3><?php _e('Miscellaneous  Settings','users-control'); ?></h3>
  
  <p><?php _e('.','users-control'); ?></p>
  
  
  
  <table class="form-table">
<?php 


$this->create_plugin_setting(
        'input',
        'company_name',
        __('Company Name:','users-control'),array(),
        __('Enter your company name here.','users-control'),
        __('Enter your company name here.','users-control')
);

$this->create_plugin_setting(
        'input',
        'company_phone',
        __('Company Phone Nunber:','users-control'),array(),
        __('Enter your company phone number here.','users-control'),
        __('Enter your company phone number here.','users-control')
);

$this->create_plugin_setting(
        'input',
        'allowed_extensions',
        __('Allowed Extensions:','users-control'),array(),
        __('Enter the allowed extensions separated by commas. Example:  jpg,png,gif,jpeg,pdf,doc,docx,xls','users-control'),
        __('Enter the allowed extensions separated by commas. Example: jpg,png,gif,jpeg,pdf,doc,docx,xls','users-control')
);

	   

 $data = array(
		 				'm/d/Y' => date('m/d/Y'),
                        'm/d/y' => date('m/d/y'),
                        'Y/m/d' => date('Y/m/d'),
                        'dd/mm/yy' => date('d/m/Y'),
                        'Y-m-d' => date('Y-m-d'),
                        'd-m-Y' => date('d-m-Y'),
                        'm-d-Y' => date('m-d-Y'),
                        'F j, Y' => date('F j, Y'),
                        'j M, y' => date('j M, y'),
                        'j F, y' => date('j F, y'),
                        'l, j F, Y' => date('l, j F, Y')
                    );
					
		 $data_time = array(
		 				'5' => 5,
                        '10' =>10,
                        '12' => 12,
                        '15' => 15,
                        '20' => 20,
                        '30' =>30,                       
                        '60' =>60
                       
                    );
		
		$data_time_format = array(
		 				
                        'H:i' => date('H:i'),
                        'h:i A' => date('h:i A')
                    );
		 $days_availability = array(
		 				'7' => 7,
                        '10' =>10,
                        '15' => 15,
                        '20' => 20,
                        '25' => 25,
                        '30' =>30,                       
                        '35' =>35,
						'40' =>40,
                       
                    );
   
		$data_picker = array(
		 				'm/d/Y' => date('m/d/Y'),
						'd/m/Y' => date('d/m/Y')
                    );
		$this->create_plugin_setting(
            'select',
            'date_format',
            __('Date Format:','users-control'),
            $data,
            __('Select the date format to be used','users-control'),
            __('Select the date format to be used','users-control')
    );
	
	$this->create_plugin_setting(
            'select',
            'date_picker_format',
            __('Date Picker Format:','users-control'),
            $data_picker,
            __('Select the date format to be used on the Date Picker','users-control'),
            __('Select the date format to be used on the Date Picker','users-control')
    );
	
	$this->create_plugin_setting(
            'select',
            'time_format',
            __('Display Time Format:','users-control'),
            $data_time_format,
            __('Select the time format to be used','users-control'),
            __('Select the time format to be used','users-control')
    );
	
	
	
	
	$this->create_plugin_setting(
	'select',
	'userscontrol_override_avatar',
	__('Use Users Control Avatar','users-control'),
	array(
		'no' => __('No','users-control'), 
		'yes' => __('Yes','users-control'),
		),
		
	__('If you select "yes",Users Control will override the default WordPress Avatar','users-control'),
  __('If you select "yes", Users Control will override the default WordPress Avatar','users-control')
       );
	
	$this->create_plugin_setting(
	'select',
	'avatar_rotation_fixer',
	__('Auto Rotation Fixer','users-control'),
	array(
		'no' => __('No','users-control'), 
		'yes' => __('Yes','users-control'),
		),
		
	__("If you select 'yes', Users Control will Automatically fix the rotation of JPEG images using PHP's EXIF extension, immediately after they are uploaded to the server. This is implemented for iPhone rotation issues",'users-control'),
  __("If you select 'yes', Users Control will Automatically fix the rotation of JPEG images using PHP's EXIF extension, immediately after they are uploaded to the server. This is implemented for iPhone rotation issues",'users-control')
       );

       $this->create_plugin_setting(
	'select',
	'userscontrol_force_cache_issue',
	__('Force Cache Issue','users-control'),
	array(
		'no' => __('No','users-control'), 
		'yes' => __('Yes','users-control'),
		),
		
	__("This option will help you to resolve cache issues when displaying users pictures",'users-control'),
  __("This option will help you to resolve cache issues when displaying users pictures",'users-control')
       );


	   
	   $this->create_plugin_setting(
        'input',
        'media_avatar_width',
        __('Avatar Width:','users-control'),array(),
        __('Width in pixels','users-control'),
        __('Width in pixels','users-control')
);

$this->create_plugin_setting(
        'input',
        'media_avatar_height',
        __('Avatar Height','users-control'),array(),
        __('Height in pixels','users-control'),
        __('Height in pixels','users-control')
);
	
	
	
	 								
	
	  
		
?>
</table>



</div>


<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','users-control'); ?>"  />
</p>




</div>




<div id="tabs-messaging">

<div class="userscontrol-sect  userscontrol-welcome-panel">
   
   
   <h3><?php _e('General Rules','users-control'); ?></h3>  
   <p><?php _e('These settings allow you to define rules on how the users and admin are notified when a new subscription is purchased from the front-end.','users-control'); ?></p>
 
 
   <table class="form-table">
 <?php
 
 
 $this->create_plugin_setting(
	'select',
	'noti_admin',
	__('Send Email Notifications to Admin?:','users-control'),
	array(
		'yes' => __('YES','users-control'),
		'no' => __('NO','users-control') 
		),
		
	__('This allows you to block email notifications that are sent to the admin.','users-control'),
  __('This allows you to block email notifications that are sent to the admin.','users-control')
       );
	   
$this->create_plugin_setting(
	'select',
	'noti_client',
	__('Send Email Notifications to Clients?:','users-control'),
	array(
		'yes' => __('YES','users-control'),
		'no' => __('NO','users-control') 
		),
		
	__('This allows you to block email notifications that are sent to the clients.','users-control'),
  __('This allows you to block email notifications that are sent to the clients.','users-control')
       ); 

?>
 
 </table>
 

  
</div>


<div class="userscontrol-sect  userscontrol-welcome-panel">
   
   
   <h3><?php _e('New Membership Purchase Notifications','users-control'); ?></h3>  
   <p><?php _e('These settings allow you to define rules on how the users and admin are notified when a new subscription is purchased.','users-control'); ?></p>
 
 
   <table class="form-table">
 <?php
 
 
 $this->create_plugin_setting(
	'select',
	'noti_membership_purchase_package_client',
	__('Send Email Notifications to Client?:','users-control'),
	array(
		'yes' => __('YES','users-control'),
		'no' => __('NO','users-control') 
		),
		
	__('This allows you to block email notifications that are sent to the admin.','users-control'),
  __('This allows you to block email notifications that are sent to the admin.','users-control')
       );
	   
$this->create_plugin_setting(
	'select',
	'noti_membership_purchase_package_admin',
	__('Send Email Notifications to Admin?:','users-control'),
	array(
		'yes' => __('YES','users-control'),
		'no' => __('NO','users-control') 
		),
		
	__('This allows you to block email notifications that are sent to the clients.','users-control'),
  __('This allows you to block email notifications that are sent to the clients.','users-control')
       );
  

?>
 
 </table>
 

  
</div>

<div class="userscontrol-sect  userscontrol-welcome-panel">
   
   
   <h3><?php _e('Membership Renewal Notifications','users-control'); ?></h3>  
   <p><?php _e('These settings allow you to define rules on how the users and admin are notified when a new subscription is renewed.','users-control'); ?></p>
 
 
   <table class="form-table">
 <?php
 
 
 $this->create_plugin_setting(
	'select',
	'noti_membership_renewal_package_client',
	__('Send Email Notifications to Client?:','users-control'),
	array(
		'yes' => __('YES','users-control'),
		'no' => __('NO','users-control') 
		),
		
	__('This allows you to block email notifications that are sent to the admin.','users-control'),
  __('This allows you to block email notifications that are sent to the admin.','users-control')
       );
	   
$this->create_plugin_setting(
	'select',
	'noti_membership_renewal_package_admin',
	__('Send Email Notifications to Admin?:','users-control'),
	array(
		'yes' => __('YES','users-control'),
		'no' => __('NO','users-control') 
		),
		
	__('This allows you to block email notifications that are sent to the clients.','users-control'),
  __('This allows you to block email notifications that are sent to the clients.','users-control')
       );
  

?>
 
 </table>
 

  
</div>





<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','users-control'); ?>"  />
</p>


</div>



<div id="tabs-bup-newsletter">
  
  <?php if(isset($userscontrol_aweber) || isset($userscontrol_mailchimp))
{?>


<div class="userscontrol-sect userscontrol-welcome-panel ">
<h3><?php _e('Newsletter Preferences','users-control'); ?></h3>
  
  <p><?php _e('Here you can activate your preferred newsletter tool.','users-control'); ?></p>

<table class="form-table">
<?php 
   
$this->create_plugin_setting(
	'select',
	'newsletter_active',
	__('Activate Newsletter','users-control'),
	array(
		'no' => __('No','users-control'), 
		'aweber' => __('AWeber','users-control'),
		'mailchimp' => __('MailChimp','users-control'),
		),
		
	__('Just set "NO" to deactivate the newsletter tool.','users-control'),
  __('Just set "NO" to deactivate the newsletter tool.','users-control')
       );

	
?>
</table>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','users-control'); ?>"  />
</p>


</div>


<?php }else{?>


<div class="userscontrol-sect  userscontrol-welcome-panel">

<p><?php _e('This function is available only on certain versions.','users-control'); ?>. Click <a href="https://userscontrol.com/compare-packages.php">here</a> to compare packages </p>


</div>

<?php }?> 
  <?php if(isset($userscontrol_aweber))
{?>


<div class="userscontrol-sect userscontrol-welcome-panel ">
<h3><?php _e('Aweber Settings','users-control'); ?></h3>
  
  <p><?php _e('This module gives you the capability to subscribe your clients automatically to any of your Aweber List when they submit a ticket.','users-control'); ?></p>
  
  
<table class="form-table">
<?php 
   
		
$this->create_plugin_setting(
        'input',
        'aweber_app_id',
        __('APP ID','users-control'),array(),
        __('Fill out this field with your AWeber APP ID.','users-control'),
        __('Fill out this field with your AWeber APP ID.','users-control')
);

$this->create_plugin_setting(
        'input',
        'aweber_consumer_key',
        __('Consumer Key','users-control'),array(),
        __('Fill out this field your AWeber Consumer Key.','users-control'),
        __('Fill out this field your AWeber Consumer Key.','users-control')
);

$this->create_plugin_setting(
        'input',
        'aweber_consumer_secret',
        __('Consumer Secret','users-control'),array(),
        __('Fill out this field your AWeber Consumer Secret.','users-control'),
        __('Fill out this field your AWeber Consumer Secret.','users-control')
);




$this->create_plugin_setting(
                'checkbox',
                'aweber_auto_text',
                __('Auto Checked Aweber','users-control'),
                '1',
                __('If checked, the user will not need to click on the AWeber checkbox. It will appear checked already.','users-control'),
                __('If checked, the user will not need to click on the AWeber checkbox. It will appear checked already.','users-control')
        );
$this->create_plugin_setting(
        'input',
        'aweber_text',
        __('Aweber Text','users-control'),array(),
        __('Please input the text that will appear when asking users to get periodical updates.','users-control'),
        __('Please input the text that will appear when asking users to get periodical updates.','users-control')
);

	$this->create_plugin_setting(
        'input',
        'aweber_header_text',
        __('Aweber Header Text','users-control'),array(),
        __('Please input the text that will appear as header when AWeber is active.','users-control'),
        __('Please input the text that will appear as header when AWeber is active.','users-control')
);
	
?>
</table>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','users-control'); ?>"  />
</p>


</div>

<?php }?> 


  <?php if(isset($userscontrol_mailchimp))
{?>


<div class="userscontrol-sect userscontrol-welcome-panel ">
<h3><?php _e('MailChimp Settings','users-control'); ?></h3>
  
  <p><?php _e('.','users-control'); ?></p>
  
  
<table class="form-table">
<?php 
   
		
$this->create_plugin_setting(
        'input',
        'mailchimp_api',
        __('MailChimp API Key','users-control'),array(),
        __('Fill out this field with your MailChimp API key here to allow integration with MailChimp subscription.','users-control'),
        __('Fill out this field with your MailChimp API key here to allow integration with MailChimp subscription.','users-control')
);

$this->create_plugin_setting(
        'input',
        'mailchimp_list_id',
        __('MailChimp List ID','users-control'),array(),
        __('Fill out this field your list ID.','users-control'),
        __('Fill out this field your list ID.','users-control')
);



$this->create_plugin_setting(
                'checkbox',
                'mailchimp_auto_checked',
                __('Auto Checked MailChimp','users-control'),
                '1',
                __('If checked, the user will not need to click on the mailchip checkbox. It will appear checked already.','users-control'),
                __('If checked, the user will not need to click on the mailchip checkbox. It will appear checked already.','users-control')
        );
$this->create_plugin_setting(
        'input',
        'mailchimp_text',
        __('MailChimp Text','users-control'),array(),
        __('Please input the text that will appear when asking users to get periodical updates.','users-control'),
        __('Please input the text that will appear when asking users to get periodical updates.','users-control')
);

	$this->create_plugin_setting(
        'input',
        'mailchimp_header_text',
        __('MailChimp Header Text','users-control'),array(),
        __('Please input the text that will appear as header when mailchip is active.','users-control'),
        __('Please input the text that will appear as header when mailchip is active.','users-control')
);
	
?>
</table>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','users-control'); ?>"  />
</p>


</div>



<?php }?>  
  
  


</div>



</div>




</form>