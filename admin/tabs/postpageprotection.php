<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $userscontrol,   $userscontrol_stripe;


$message = __('Some features of this module are available only on Pro version. The lite version allows you to protect content from logged in users only.','users-control');
echo wp_kses($userscontrol->admin->only_pro_users_message($message), $userscontrol->allowed_html);
?>

<form method="post" action="">
<input type="hidden" name="userscontrol_update_settings" />



<div class="userscontrol-sect  userscontrol-welcome-panel">
    
<h3><?php _e('Global Protection Settings','users-control'); ?></h3>  
    <p><?php _e("In this section you can manage Posts & Pages Protection module settings.",'users-control'); ?></p>
   <p><?php _e("This module will let you block pages and any post types and make them visible only to logged in users.",'users-control'); ?></p>
  
  
  <h4><?php _e("Posts & Pages Protection Activation.",'users-control'); ?></h4>
  <table class="form-table">
<?php 



$this->create_plugin_setting(
	'select',
	'activate_post_protection_modules',
	__('Protection Active','users-control'),
	array(
		'no' => __('No','users-control'), 
		'yes' => __('Yes','users-control'),
		),
		
	__("By selecting 'yes' the options to protect posts and pages will be enabled.",'users-control'),
  __("By selecting 'yes' the options to protect posts and pages will be enabled.",'users-control')
       );
	   
	   
	   $protections_method = array(
		'loggedin' => __('Logged in users only','users-control'), 
		'membership' => __('Membership only','users-control'),
		'role' => __('Only Certain Roles','users-control')
		);
	   
	   $this->create_plugin_setting(
	'select',
	'post_protection_method',
	__('Protection Method','users-control'),
	$protections_method,
		
	__("By selecting 'Logged in users only' the pots/pages will be visible to logged in usrers only.",'users-control'),
  __("By selecting 'Logged in users only' the pots/pages will be visible to logged in usrers only.",'users-control')
       );
	   
	   
  
		
?>
</table>

  
</div>

<div class="userscontrol-sect  userscontrol-welcome-panel">

<h4><?php _e("Set up the behaviour of locked posts.",'users-control'); ?></h4>
  <table class="form-table">
<?php 



$this->create_plugin_setting(
	'select',
	'userscontrol_loggedin_hide_complete_post',
	__('Hide Complete Posts?','users-control'),
	array(
		'no' => __('No','users-control'), 
		'yes' => __('Yes','users-control'),
		),
		
	__("By selecting 'yes' will hide posts if the user has no access.  <strong>Please note: </strong> a 404 error message will be displayed since the post will be completely locked out.",'users-control'),
  __("By selecting 'yes' will hide posts if the user has no access",'users-control')
       );

$this->create_plugin_setting(
	'select',
	'userscontrol_loggedin_hide_post_title',
	__('Hide Post Title?','users-control'),
	array(
		'no' => __('No','users-control'), 
		'yes' => __('Yes','users-control'),
		),
		
	__("By selecting 'yes' will show the text which is defined at 'Post title' if user has no access.",'users-control'),
  __("By selecting 'yes' will show the text which is defined at 'Post title' if user has no access.",'users-control')
       );
	   
$this->create_plugin_setting( 
        'input',
        'userscontrol_loggedin_post_title',
        __('Post Title:','users-control'),array(),
        __('This will be the displayed text as post title if user has no access.','users-control'),
        __('This will be the displayed text as post title if user has no access.','users-control')
);  


$this->create_plugin_setting(
	'select',
	'userscontrol_loggedin_post_content_before_more',
	__('Show post content before &lt;!--more--&gt; tag?','users-control'),
	array(
		'no' => __('No','users-control'), 
		'yes' => __('Yes','users-control'),
		),
		
	__('By selecting "Yes"  will display the post content before the &lt;!--more--&gt; tag and after that the defined text at "Post content". If no &lt;!--more--&gt;  is set he defined text at "Post content" will shown.','users-control'),
  __('By selecting "Yes"  will display the post content before the &lt;!--more--&gt; tag and after that the defined text at "Post content". If no &lt;!--more--&gt;  is set he defined text at "Post content" will shown.','users-control')
       );


$this->create_plugin_setting(
        'textarea',
        'userscontrol_loggedin_post_content',
        __('Post Content','users-control'),array(),
        __('This content will be displayed if user has no access. ','users-control'),
        __('This content will be displayed if user has no access. ','users-control')
);


$this->create_plugin_setting(
	'select',
	'userscontrol_loggedin_hide_post_comments',
	__('Hide Post Comments?','users-control'),
	array(
		'no' => __('No','users-control'), 
		'yes' => __('Yes','users-control'),
		),
		
	__("By selecting 'yes' will show the text which is defined at 'Post comment text' if user has no access.",'users-control'),
  __("By selecting 'yes' will show the text which is defined at 'Post comment text' if user has no access.",'users-control')
       );
	  
$this->create_plugin_setting( 
        'input',
        'userscontrol_loggedin_post_comment_content',
        __('Post Comment Text:','users-control'),array(),
        __('This will be displayed text as post comment text if user has no access.','users-control'),
        __('This will be displayed text as post comment text if user has no access.','users-control')
);  
$this->create_plugin_setting(
	'select',
	'userscontrol_loggedin_allow_post_comments',
	__('Allows Post Comments?','users-control'),
	array(
		'no' => __('No','users-control'), 
		'yes' => __('Yes','users-control'),
		),
		
	__("By selecting 'yes' allows users to comment on locked posts",'users-control'),
  __("By selecting 'yes' allows users to comment on locked posts",'users-control')
       );	  
		

?>
</table>

</div>

<div class="userscontrol-sect  userscontrol-welcome-panel">

<h4><?php _e("Set up the behaviour of locked pages.",'users-control'); ?></h4>
  <table class="form-table">
<?php 


$this->create_plugin_setting(
	'select',
	'userscontrol_loggedin_hide_complete_page',
	__('Hide Complete Pages?','users-control'),
	array(
		'no' => __('No','users-control'), 
		'yes' => __('Yes','users-control'),
		),
		
	__("By selecting 'yes' will hide pages if the user has no access. <strong>Please note: </strong> a 404 error message will be displayed since the page will be completely locked out.",'users-control'),
  __("By selecting 'yes' will hide pages if the user has no access",'users-control')
       );

$this->create_plugin_setting(
	'select',
	'userscontrol_loggedin_hide_page_title',
	__('Hide Page Title?','users-control'),
	array(
		'no' => __('No','users-control'), 
		'yes' => __('Yes','users-control'),
		),
		
	__("By selecting 'yes' will show the text which is defined at 'Page title' if user has no access.",'users-control'),
  __("By selecting 'yes' will show the text which is defined at 'Page title' if user has no access.",'users-control')
       );
	   
$this->create_plugin_setting( 
        'input',
        'userscontrol_loggedin_page_title',
        __('Page Title:','users-control'),array(),
        __('This will be the displayed text as page title if user has no access.','users-control'),
        __('This will be the displayed text as page title if user has no access.','users-control')
);  


$this->create_plugin_setting(
        'textarea',
        'userscontrol_loggedin_page_content',
        __('Page Content','users-control'),array(),
        __('This content will be displayed if user has no access. ','users-control'),
        __('This content will be displayed if user has no access. ','users-control')
);


$this->create_plugin_setting(
	'select',
	'userscontrol_loggedin_hide_page_comments',
	__('Hide Page Comments?','users-control'),
	array(
		'no' => __('No','users-control'), 
		'yes' => __('Yes','users-control'),
		),
		
	__("By selecting 'yes' will show the text which is defined at 'Page comment text' if user has no access.",'users-control'),
  __("By selecting 'yes' will show the text which is defined at 'Page comment text' if user has no access.",'users-control')
       );
	  
	  
	  	  
$this->create_plugin_setting( 
        'input',
        'userscontrol_loggedin_page_comment_content',
        __('Page Comment Text:','users-control'),array(),
        __('This will be displayed text as page comment text if user has no access.','users-control'),
        __('This will be displayed text as page comment text if user has no access.','users-control')
);  
$this->create_plugin_setting(
	'select',
	'userscontrol_loggedin_allow_page_comments',
	__('Allows Page Comments?','users-control'),
	array(
		'no' => __('No','users-control'), 
		'yes' => __('Yes','users-control'),
		),
		
	__("By selecting 'yes' allows users to comment on locked pages",'users-control'),
  __("By selecting 'yes' allows users to comment on locked pages",'users-control')
       );	 
  
		
?>
</table>

</div>

<div class="userscontrol-sect  userscontrol-welcome-panel">


<h4><?php _e("Other Settings.",'users-control'); ?></h4>
  <table class="form-table">
<?php 



$this->create_plugin_setting(
	'select',
	'userscontrol_loggedin_protect_feed',
	__('Hide Post Feed?','users-control'),
	array(
		'no' => __('No','users-control'), 
		'yes' => __('Yes','users-control'),
		),
		
	__("By selecting 'yes' will show the text which is defined at 'Post title' if user has no access.",'users-control'),
  __("By selecting 'yes' will show the text which is defined at 'Post title' if user has no access.",'users-control')
       );
	   
  
		
?>
</table>
</div>





<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','users-control'); ?>"  />
	
</p>

</form>