<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $userscontrol ;

$current_user = $userscontrol->user->get_user_info();

$user_id = $current_user->ID;
$user_email = $current_user->user_email;
$howmany = 5;

$currency_symbol =  $userscontrol->get_option('paid_membership_symbol');
$date_format =  $userscontrol->get_int_date_format();
$time_format =  $userscontrol->get_time_format();
$datetime_format =  $userscontrol->get_date_to_display();


$module = "main";
$part = "";
$act= "";
$view= "";
$reply= "";


if(isset($_GET["module"])){	$module =  sanitize_text_field($_GET["module"]);	}
if(isset($_GET["act"])){$act =  sanitize_text_field($_GET["act"]);	}
if(isset($_GET["view"])){	$view =  sanitize_text_field($_GET["view"]);}
if(isset($_GET["reply"])){	$reply =  sanitize_text_field($_GET["reply"]);}


?>
<div class="userscontrol-user-dahsboard-cont">


<?php //include header

echo wp_kses($userscontrol->profile->get_user_header(), $userscontrol->allowed_html);

?>


	
    <div class="userscontrol-centered-cont">
    
    
		<?php
    
          // echo wp_kses($userscontrol->profile->get_template_part($module), $userscontrol->allowed_html) ;
         echo $userscontrol->profile->get_template_part($module);
        
        ?>
    
    
    </div>
   

</div>


	