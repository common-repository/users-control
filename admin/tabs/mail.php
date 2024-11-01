<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $userscontrol;
 $label_pro ="";

?>
<h3><?php _e('Advanced Email Options','users-control'); ?></h3>
<form method="post" action="" id="b_frm_settings" name="b_frm_settings">
<input type="hidden" name="userscontrol_update_settings" />
<input type="hidden" name="userscontrol_reset_email_template" id="userscontrol_reset_email_template" />
<input type="hidden" name="email_template" id="email_template" />


  <p><?php _e('Here you can control how Users Control will send the notification to your users.','users-control'); ?></p>


<div class="userscontrol-sect  userscontrol-welcome-panel">  
   <table class="form-table">
<?php 
 

$this->create_plugin_setting(
        'input',
        'messaging_send_from_name',
        __('Send From Name','users-control'),array(),
        __('Enter the your name or company name here.','users-control'),
        __('Enter the your name or company name here.','users-control')
);

$this->create_plugin_setting(
        'input',
        'messaging_send_from_email',
        __('Send From Email','users-control'),array(),
        __('Enter the email address to be used when sending emails.','users-control'),
        __('Enter the email address to be used when sending emails.','users-control')
);

$this->create_plugin_setting(
	'select',
	'userscontrol_smtp_mailing_mailer',
	__('Mailer:','users-control'),
	array(
		'mail' => __('Use the PHP mail() function to send emails','users-control'),
		'smtp' => __('Send all emails via SMTP','users-control'), 
		'mandrill' => __('Send all emails via Mandrill','users-control'),
		'third-party' => __('Send all emails via Third-party plugin','users-control'), 
		
		),
		
	__('Specify which mailer method the pluigin should use when sending emails.','users-control'),
  __('Specify which mailer method the pluigin should use when sending emails.','users-control')
       );
	   
$this->create_plugin_setting(
                'checkbox',
                'userscontrol_smtp_mailing_return_path',
                __('Return Path','users-control'),
                '1',
                __('Set the return-path to match the From Email','users-control'),
                __('Set the return-path to match the From Email','users-control')
        ); 
?>
 </table>

 
 </div>
 
 
 
 <div class="userscontrol-sect  userscontrol-welcome-panel">
 
 <h3><?php _e('SMTP Settings','users-control'); ?></h3>
  <p> <strong><?php _e('This options should be set only if you have chosen to send email via SMTP','users-control'); ?></strong></p>
 
  <table class="form-table">
 <?php
$this->create_plugin_setting(
        'input',
        'userscontrol_smtp_mailing_host',
        __('SMTP Host:','users-control'),array(),
        __('Specify host name or ip address.','users-control'),
        __('Specify host name or ip address.','users-control')
); 

$this->create_plugin_setting(
        'input',
        'userscontrol_smtp_mailing_port',
        __('SMTP Port:','users-control'),array(),
        __('Specify Port.','users-control'),
        __('Specify Port.','users-control')
); 


$this->create_plugin_setting(
	'select',
	'userscontrol_smtp_mailing_encrytion',
	__('Encryption:','users-control'),
	array(
		'none' => __('No encryption','users-control'),
		'ssl' => __('Use SSL encryption','users-control'), 
		'tls' => __('Use TLS encryption','users-control'), 
		
		),
		
	__('Specify the encryption method.','users-control'),
  __('Specify the encryption method.','users-control')
       );
	   
$this->create_plugin_setting(
	'select',
	'userscontrol_smtp_mailing_authentication',
	__('Authentication:','users-control'),
	array(
		'false' => __('No. Do not use SMTP authentication','users-control'),
		'true' => __('Yes. Use SMTP Authentication','users-control'), 
		
		),
		
	__('Specify the authentication method.','users-control'),
  __('Specify the authentication method.','users-control')
       );

$this->create_plugin_setting(
        'input',
        'userscontrol_smtp_mailing_username',
        __('Username:','users-control'),array(),
        __('Specify Username.','users-control'),
        __('Specify Username.','users-control')
); 

$this->create_plugin_setting(
        'input',
        'userscontrol_smtp_mailing_password',
        __('Password:','users-control'),array(),
        __('Input Password.','users-control'),
        __('Input Password.','users-control')
); 


 ?>
 
 </table>
 
 
 </div>
 



<div class="userscontrol-sect  userscontrol-welcome-panel">
  <h3><?php _e('User Registration Email','users-control'); ?> <?php echo esc_attr($label_pro)?> <span class="userscontrol-main-close-open-tab"><a href="#" title="<?php _e('Close','users-control'); ?>" class="userscontrol-widget-home-colapsable" widget-id="666"><i class="fa fa-sort-desc" id="userscontrol-close-open-icon-666"></i></a></span></h3>
  
  <p><?php _e('This message will be sent to the user and it includes the password.','users-control'); ?></p>
<div class="userscontrol-messaging-hidden" id="userscontrol-main-cont-home-666">  
  
   <table class="form-table">

<?php 


$this->create_plugin_setting(
        'input',
        'email_registration_subject',
        __('Subject:','users-control'),array(),
        __('Set Email Subject.','users-control'),
        __('Set Email Subject.','users-control')
); 

$this->create_plugin_setting(
        'textarearich',
        'email_registration_body',
        __('Message','users-control'),array(),
        __('Set Email Message here.','users-control'),
        __('Set Email Message here.','users-control')
);
	
?>

<tr>

<th></th>
<td><input type="button" value="<?php _e('RESTORE DEFAULT TEMPLATE','users-control'); ?>" class="userscontrol_restore_template button" b-template-id='email_registration_body'></td>

</tr>	
</table> 
</div>


</div>


<div class="userscontrol-sect  userscontrol-welcome-panel">
  <h3><?php _e('User Account E-mail Activation','users-control'); ?> <?php echo esc_attr($label_pro)?> <span class="userscontrol-main-close-open-tab"><a href="#" title="<?php _e('Close','users-control'); ?>" class="userscontrol-widget-home-colapsable" widget-id="2020"><i class="fa fa-sort-desc" id="userscontrol-close-open-icon-2020"></i></a></span></h3>
  
  <p><?php _e('This message will be sent to the user if the account needs email activation.','users-control'); ?></p>
<div class="userscontrol-messaging-hidden" id="userscontrol-main-cont-home-2020">  
  
   <table class="form-table">

<?php 


$this->create_plugin_setting(
        'input',
        'new_account_activation_link_subject',
        __('Subject:','users-control'),array(),
        __('Set Email Subject.','users-control'),
        __('Set Email Subject.','users-control')
); 

$this->create_plugin_setting(
        'textarearich',
        'new_account_activation_link',
        __('Message','users-control'),array(),
        __('Set Email Message here.','users-control'),
        __('Set Email Message here.','users-control')
);
	
?>

<tr>

<th></th>
<td><input type="button" value="<?php _e('RESTORE DEFAULT TEMPLATE','users-control'); ?>" class="userscontrol_restore_template button" b-template-id='new_account_activation_link'></td>

</tr>	
</table> 
</div>


</div>

<div class="userscontrol-sect  userscontrol-welcome-panel">
  <h3><?php _e('Admin E-mail Activation','users-control'); ?> <?php echo esc_attr($label_pro)?> <span class="userscontrol-main-close-open-tab"><a href="#" title="<?php _e('Close','users-control'); ?>" class="userscontrol-widget-home-colapsable" widget-id="45885"><i class="fa fa-sort-desc" id="userscontrol-close-open-icon-45885"></i></a></span></h3>
  
  <p><?php _e('This message will be sent to the administration if the account needs email activation.','users-control'); ?></p>
<div class="userscontrol-messaging-hidden" id="userscontrol-main-cont-home-45885">  
  
   <table class="form-table">

<?php 


$this->create_plugin_setting(
        'input',
        'new_account_activation_link_subject',
        __('Subject:','users-control'),array(),
        __('Set Email Subject.','users-control'),
        __('Set Email Subject.','users-control')
); 

$this->create_plugin_setting(
        'textarearich',
        'new_account_activation_link',
        __('Message','users-control'),array(),
        __('Set Email Message here.','users-control'),
        __('Set Email Message here.','users-control')
);
	
?>

<tr>

<th></th>
<td><input type="button" value="<?php _e('RESTORE DEFAULT TEMPLATE','users-control'); ?>" class="userscontrol_restore_template button" b-template-id='new_account_activation_link'></td>

</tr>	
</table> 
</div>


</div>


<div class="userscontrol-sect  userscontrol-welcome-panel">
  <h3><?php _e('User E-mail Activation Successfully ','users-control'); ?> <?php echo esc_attr($label_pro)?> <span class="userscontrol-main-close-open-tab"><a href="#" title="<?php _e('Close','users-control'); ?>" class="userscontrol-widget-home-colapsable" widget-id="698755"><i class="fa fa-sort-desc" id="userscontrol-close-open-icon-698755"></i></a></span></h3>
  
  <p><?php _e('This message will be sent to the user once the account is validated via email.','users-control'); ?></p>
<div class="userscontrol-messaging-hidden" id="userscontrol-main-cont-home-698755">  
  
   <table class="form-table">

<?php 


$this->create_plugin_setting(
        'input',
        'account_verified_sucess_message_subject',
        __('Subject:','users-control'),array(),
        __('Set Email Subject.','users-control'),
        __('Set Email Subject.','users-control')
); 

$this->create_plugin_setting(
        'textarearich',
        'account_verified_sucess_message_body',
        __('Message','users-control'),array(),
        __('Set Email Message here.','users-control'),
        __('Set Email Message here.','users-control')
);
	
?>

<tr>

<th></th>
<td><input type="button" value="<?php _e('RESTORE DEFAULT TEMPLATE','users-control'); ?>" class="userscontrol_restore_template button" b-template-id='account_verified_sucess_message_body'></td>

</tr>	
</table> 
</div>


</div>



<div class="userscontrol-sect  userscontrol-welcome-panel">
  <h3><?php _e('Members Password Reset','users-control'); ?> <span class="userscontrol-main-close-open-tab"><a href="#" title="<?php _e('Close','users-control'); ?>" class="userscontrol-widget-home-colapsable" widget-id="20123"><i class="fa fa-sort-desc" id="userscontrol-close-open-icon-20123"></i></a></span></h3>
  
  <p><?php _e('This message is sent when the password is changed by the members on the front-end','users-control'); ?></p>
<div class="userscontrol-messaging-hidden" id="userscontrol-main-cont-home-20123">  
  
   <table class="form-table">

<?php 


$this->create_plugin_setting(
        'input',
        'email_password_change_member_subject',
        __('Subject:','users-control'),array(),
        __('Set Email Subject.','users-control'),
        __('Set Email Subject.','users-control')
); 

$this->create_plugin_setting(
        'textarearich',
        'email_password_change_member_body',
        __('Message','users-control'),array(),
        __('Set Email Message here.','users-control'),
        __('Set Email Message here.','users-control')
);
	
?>

<tr>

<th></th>
<td><input type="button" value="<?php _e('RESTORE DEFAULT TEMPLATE','users-control'); ?>" class="userscontrol_restore_template button" b-template-id='email_password_change_member_body'></td>

</tr>	
</table> 
</div>


</div>

<div class="userscontrol-sect  userscontrol-welcome-panel">
  <h3><?php _e('Subscription - User Email New Subscription Purchase','users-control'); ?> <?php echo esc_attr($label_pro)?> <span class="userscontrol-main-close-open-tab"><a href="#" title="<?php _e('Close','users-control'); ?>" class="userscontrol-widget-home-colapsable" widget-id="669"><i class="fa fa-sort-desc" id="userscontrol-close-open-icon-669"></i></a></span></h3>
  
  <p><?php _e('This message will be sent to the user when purchasing a new package within his/her account.','users-control'); ?></p>
<div class="userscontrol-messaging-hidden" id="userscontrol-main-cont-home-669">  
  
   <table class="form-table">

<?php 


$this->create_plugin_setting(
        'input',
        'email_package_upgrade_subject',
        __('Subject:','users-control'),array(),
        __('Set Email Subject.','users-control'),
        __('Set Email Subject.','users-control')
); 

$this->create_plugin_setting(
        'textarearich',
        'email_package_upgrade_body',
        __('Message','users-control'),array(),
        __('Set Email Message here.','users-control'),
        __('Set Email Message here.','users-control')
);
	
?>

<tr>

<th></th>
<td><input type="button" value="<?php _e('RESTORE DEFAULT TEMPLATE','users-control'); ?>" class="userscontrol_restore_template button" b-template-id='email_package_upgrade_body'></td>

</tr>	
</table> 
</div>


</div>


<div class="userscontrol-sect  userscontrol-welcome-panel">
  <h3><?php _e('Subscription - Admin Email New Subscription Purchase','users-control'); ?> <?php echo esc_attr($label_pro)?> <span class="userscontrol-main-close-open-tab"><a href="#" title="<?php _e('Close','users-control'); ?>" class="userscontrol-widget-home-colapsable" widget-id="667"><i class="fa fa-sort-desc" id="userscontrol-close-open-icon-667"></i></a></span></h3>
  
  <p><?php _e('This message will be sent to the admin when a client buys a new package within his/her account.','users-control'); ?></p>
<div class="userscontrol-messaging-hidden" id="userscontrol-main-cont-home-667">  
  
   <table class="form-table">

<?php 


$this->create_plugin_setting(
        'input',
        'email_package_upgrade_admin_subject',
        __('Subject:','users-control'),array(),
        __('Set Email Subject.','users-control'),
        __('Set Email Subject.','users-control')
); 

$this->create_plugin_setting(
        'textarearich',
        'email_package_upgrade_admin_body',
        __('Message','users-control'),array(),
        __('Set Email Message here.','users-control'),
        __('Set Email Message here.','users-control')
);
	
?>

<tr>

<th></th>
<td><input type="button" value="<?php _e('RESTORE DEFAULT TEMPLATE','users-control'); ?>" class="userscontrol_restore_template button" b-template-id='email_package_upgrade_admin_body'></td>

</tr>	
</table> 
</div>


</div>


<div class="userscontrol-sect  userscontrol-welcome-panel">
  <h3><?php _e('Subscription - Admin Email Subscription Renewal','users-control'); ?> <?php echo esc_attr($label_pro)?> <span class="userscontrol-main-close-open-tab"><a href="#" title="<?php _e('Close','users-control'); ?>" class="userscontrol-widget-home-colapsable" widget-id="567"><i class="fa fa-sort-desc" id="userscontrol-close-open-icon-567"></i></a></span></h3>
  
  <p><?php _e('This message will be sent to the admin when a subscription is renewed.','users-control'); ?></p>
<div class="userscontrol-messaging-hidden" id="userscontrol-main-cont-home-567">  
  
   <table class="form-table">

<?php 


$this->create_plugin_setting(
        'input',
        'email_package_renewal_admin_subject',
        __('Subject:','users-control'),array(),
        __('Set Email Subject.','users-control'),
        __('Set Email Subject.','users-control')
); 

$this->create_plugin_setting(
        'textarearich',
        'email_package_renewal_admin_body',
        __('Message','users-control'),array(),
        __('Set Email Message here.','users-control'),
        __('Set Email Message here.','users-control')
);
	
?>

<tr>

<th></th>
<td><input type="button" value="<?php _e('RESTORE DEFAULT TEMPLATE','users-control'); ?>" class="userscontrol_restore_template button" b-template-id='email_package_renewal_admin_body'></td>

</tr>	
</table> 
</div>


</div>

<div class="userscontrol-sect  userscontrol-welcome-panel">
  <h3><?php _e('Subscription - Client Email Subscription Renewal','users-control'); ?> <?php echo esc_attr($label_pro)?> <span class="userscontrol-main-close-open-tab"><a href="#" title="<?php _e('Close','users-control'); ?>" class="userscontrol-widget-home-colapsable" widget-id="568"><i class="fa fa-sort-desc" id="userscontrol-close-open-icon-568"></i></a></span></h3>
  
  <p><?php _e('This message will be sent to the client when a subscription is renewd.','users-control'); ?></p>
<div class="userscontrol-messaging-hidden" id="userscontrol-main-cont-home-568">  
  
   <table class="form-table">

<?php 


$this->create_plugin_setting(
        'input',
        'email_package_renewal_subject',
        __('Subject:','users-control'),array(),
        __('Set Email Subject.','users-control'),
        __('Set Email Subject.','users-control')
); 

$this->create_plugin_setting(
        'textarearich',
        'email_package_renewal_body',
        __('Message','users-control'),array(),
        __('Set Email Message here.','users-control'),
        __('Set Email Message here.','users-control')
);
	
?>

<tr>

<th></th>
<td><input type="button" value="<?php _e('RESTORE DEFAULT TEMPLATE','users-control'); ?>" class="userscontrol_restore_template button" b-template-id='email_package_renewal_body'></td>

</tr>	
</table> 
</div>


</div>











<p class="submit">
	<input type="submit" name="mail_setting_submit" id="mail_setting_submit" class="button button-primary" value="<?php _e('Save Changes','users-control'); ?>"  />

</p>

</form>