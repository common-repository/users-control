<?php
/*
Plugin Name: Users Control
Plugin URI: https://userscontrol.com
Description: Users & Subscriptions Plugin. Recurring Payments, PayPal, Stripe. Partial and Full content Protection. Protect Pages, Posts, Images.
Tested up to: 6.3
Version: 1.0.16
Author: Users Control
Text Domain: users-control
Domain Path: /languages
Author URI: https://userscontrol.com/
*/

define('userscontrol_url',plugin_dir_url(__FILE__ ));
define('userscontrol_path',plugin_dir_path(__FILE__ ));
define('WPUSERSCONTROL_PLUGIN_SETTINGS_URL',"?page=userscontrol&tab=main");
define('WPUSERSCONTROL_PLUGIN_WELCOME_URL',"?page=userscontrol&tab=welcome");
define('WPUSERSCONTROL_MEDIAFOLDER',"users-control");

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$plugin = plugin_basename(__FILE__);

/* Loading Function */
require_once (userscontrol_path . 'functions/functions.php');

/* Init */
define('userscontrol_pro_url','https://userscontrol.com/');

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'userscontrol_settings_link' );


function userscontrol_load_textdomain(){     	  
	   $locale = apply_filters( 'plugin_locale', get_locale(), 'users-control' );	   
       $mofile = userscontrol_path . "languages/users-control-$locale.mo";
			
		// Global + Frontend Locale
		load_textdomain( 'users-control', $mofile );
		load_plugin_textdomain( 'users-control', false, dirname(plugin_basename(__FILE__)).'/languages/' );
}


function userscontrol_settings_link( array $links ) {	
	$url = "https://userscontrol.com/pricing";
	$settings_link = '<a href="' . $url . '" target="_blank" class="userscontrol-plugins-gopro">' . __('Go Pro', 'users-control') . '</a>';
	$links[] = $settings_link;
	return $links;
}

/* Load plugin text domain (localization) */
add_action('init', 'userscontrol_load_textdomain');	
		
/* Master Class  */
require_once (userscontrol_path . 'classes/usercontrol.class.php');

// Helper to activate a plugin on another site without causing a fatal error by
register_activation_hook( __FILE__, 'userscontrol_activation');
 
function  userscontrol_activation( $network_wide ){
	$plugin_path = '';
	$plugin = "users-control/index.php";	
	if ( is_multisite() && $network_wide ){ 
		activate_plugin($plugin_path,NULL,true);
	} else {  	
		activate_plugin($plugin_path,NULL,false);		
	}
}

$userscontrol = new Userscontrol();
$userscontrol->plugin_init();

register_activation_hook(__FILE__, 'userscontrol_my_plugin_activate');
add_action('admin_init', 'userscontrol_my_plugin_redirect');

function userscontrol_my_plugin_activate(){
    add_option('userscontrol_plugin_do_activation_redirect', true);
}

function userscontrol_my_plugin_deactivate() 
{

}
function userscontrol_my_plugin_redirect(){
    if (get_option('userscontrol_plugin_do_activation_redirect', false)) {
        delete_option('userscontrol_plugin_do_activation_redirect');
		
		if (! get_option('userscontrol_ini_setup')){
			wp_redirect(WPUSERSCONTROL_PLUGIN_WELCOME_URL);
		}else{
			wp_redirect(WPUSERSCONTROL_PLUGIN_WELCOME_URL);
		}
    }
}
require_once userscontrol_path . 'addons/forms/index.php';
require_once userscontrol_path . 'addons/pages/index.php';
