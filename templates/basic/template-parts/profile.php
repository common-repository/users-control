<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $userscontrol ;

$current_user = $userscontrol->user->get_user_info();
$user_id = $current_user->ID;

$currency_symbol =  $userscontrol->get_option('paid_membership_symbol');
$date_format =  $userscontrol->get_int_date_format();
$time_format =  $userscontrol->get_time_format();
$datetime_format =  $userscontrol->get_date_to_display();
$active_subscriptions =  $userscontrol->user->get_my_active_subscriptions($user_id);


?>

<div class="userscontrol-main-app-list userscontrol-prof-cover" id="">

    <ul>

        <li>
            <div class="userscontrol-common-cont"> 
                 <div class="userscontrol-div-for-media-upload"> <a href="?module=upload_avatar"><i class="fa fa-camera"></i><?php _e('Update Avatar','users-control')?> </a></div>
            </div>

        </li>
        <li>
            <div class="userscontrol-common-cont"> 
                 <div class="userscontrol-div-for-media-upload"> <a href="?module=upload_cover"><i class="fa fa-image"></i><?php _e('Profile Photo Cover','users-control')?></a></div>
            </div>

        </li>

    </ul>

</div>

    
<div class="userscontrol-main-app-list" id="userscontrol-backend-landing-1">

<?php echo $userscontrol->user->edit_profile_form();?>

</div>
