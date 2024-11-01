<?php
global $userscontrol, $userscontrol_staff_profile;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>

<form method="post" action="">
<input type="hidden" name="update_settings" />
<input type="hidden" name="update_usercontrol_slugs" id="update_usercontrol_slugs" value="userscontrol_slugs" />

<div class="userscontrol-sect  userscontrol-welcome-panel ">

 <h3><?php _e('Users Control Pages','users-control'); ?></h3>
        
              <p><?php _e('Here you can set your custom pages for the members.','users-control'); ?></p>
        
  <table class="form-table">
<?php 


$userscontrol->admin->create_plugin_setting(
        'select',
        'profile_page_id',
        __('User Profile Page','users-control'),
        $userscontrol->admin->get_all_sytem_pages(),
        __('Make sure you have the <code>[userscontrol_profile]</code> shortcode on this page.','users-control'),
        __('This page is where users will be able to sign up to your website.','users-control')
);


$userscontrol->admin->create_plugin_setting(
            'select',
            'registration_page',
            __('Registration Page','users-control'),
            $userscontrol->admin->get_all_sytem_pages(),
            __('Make sure you have the <code>[userscontrol_user_signup]</code> shortcode on this page.','users-control'),
            __('This page is where users will be able to sign up to your website.','users-control')
);

	
$userscontrol->admin->create_plugin_setting(
            'select',
            'my_account_page',
            __('My Account Page','users-control'),
            $userscontrol->admin->get_all_sytem_pages(),
            __('Make sure you have the <code>[userscontrol_account]</code> shortcode on this page.','users-control'),
            __('This page is where users and staff members will be able to manage their appointments.','users-control')
);
	
	$userscontrol->admin->create_plugin_setting(
            'select',
            'user_login_page',
            __('Users Login Page','users-control'),
            $userscontrol->admin->get_all_sytem_pages(),
            __('Make sure you have the <code>[userscontrol_user_login]</code> shortcode on this page.','users-control'),
            __('This page is where users and staff members & clients will be able to recover to login to their accounts.','users-control')
    );
	
	
		$userscontrol->admin->create_plugin_setting(
            'select',
            'password_reset_page',
            __('Password Recover Page','users-control'),
            $userscontrol->admin->get_all_sytem_pages(),
            __('Make sure you have the <code>[userscontrol_user_recover_password]</code> shortcode on this page.','users-control'),
            __('This page is where users and staff members will be able to recover their passwords.','users-control')
    );
	
	
			
	$userscontrol->admin->create_plugin_setting(
	'select',
	'hide_admin_bar',
	__('Hide WP Admin Tool Bar?','users-control'),
	array(
		0 => __('NO','users-control'), 		
		1 => __('YES','users-control')),
		
	__('If checked, User will not see the WP Admin Tool Bar','users-control'),
  __('If checked, User will not see the WP Admin Tool Bar','users-control')
       );
	   
	     
	
	   
		
?>
</table>      
   

             

</div>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','users-control'); ?>"  />
	
</p>

</form>

