<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $userscontrol, $userscontrol_wooco ;

$current_user = $userscontrol->user->get_user_info();
$user_id = $current_user->ID;

$currency_symbol =  $userscontrol->get_option('paid_membership_symbol');
$date_format =  $userscontrol->get_int_date_format();
$time_format =  $userscontrol->get_time_format();
$datetime_format =  $userscontrol->get_date_to_display();

$active_subscriptions =  $userscontrol->user->get_my_active_subscriptions($user_id);


?>

<h1><?php _e('Billing & Shipping Information','users-control')?></h1>


    
<div class="userscontrol-main-app-list" id="userscontrol-backend-landing-1">

     <?php if(isset($userscontrol_wooco)){ ?>
       
       		<?php echo wp_kses($userscontrol_wooco->get_billing_shipping_info(), $userscontrol->allowed_html);?>
       
       <?php }?>

</div>
