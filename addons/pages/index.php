<?php
global $userscontrol;

define('userscontrol_profiles_url',plugin_dir_url(__FILE__ ));
define('userscontrol_profiles_path',plugin_dir_path(__FILE__ ));

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(isset($userscontrol)){

	/* administration */
	if (is_admin()){
		foreach (glob(userscontrol_profiles_path . 'admin/*.php') as $filename) { include $filename; }
	}
	
}