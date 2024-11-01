<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $userscontrol, $userscontrol_wooco, $userscontrol_photomedia ;
$current_user = $userscontrol->user->get_user_info();
$user_id = $current_user->ID;
$user_type_legend = "";
?>
<div class="userscontrol-top-header">   
    
    	<?php echo  wp_kses($userscontrol->profile->get_user_avatar_top($user_id), $userscontrol->allowed_html);?>   
        
        
        <div class="userscontrol-staff-profile-name">
        	<h1><?php echo esc_attr($current_user->display_name)?></h1>
            <small><?php echo esc_attr($user_type_legend)?></small>
        </div>
        
        <div class="userscontrol-top-options-book">            
            	                
               
                                
        </div>
        
        
        <div class="userscontrol-top-options"> 
             <ul>            
             
                <li><?php echo wp_kses($userscontrol->profile->get_user_backend_menu_new('main', 'Main','fa-home'), $userscontrol->allowed_html);?></li>                                              
                
                  <?php if( isset($userscontrol_wooco) ){?>
                 
                <li><?php echo wp_kses($userscontrol->profile->get_user_backend_menu_new('orders_list', 'Orders','fa-list'), $userscontrol->allowed_html); ?></li>   
                 
                  <?php } ?>  

                <?php if( isset($userscontrol_photomedia) ){?>
                  <li><?php echo wp_kses( $userscontrol->profile->get_user_backend_menu_new('photos', 'Photos','fa-photo'), $userscontrol->allowed_html); ?></li>
                <?php } ?>  
                <li><?php echo wp_kses( $userscontrol->profile->get_user_backend_menu_new('subscriptions', 'Subscriptions','fa-list'), $userscontrol->allowed_html);?></li>   
                <li><?php echo wp_kses( $userscontrol->profile->get_user_backend_menu_new('profile', 'Profile','fa-address-card-o'), $userscontrol->allowed_html); ?></li>
                <li><?php echo wp_kses( $userscontrol->profile->get_user_backend_menu_new('account', 'Account','fa-gear'), $userscontrol->allowed_html); ?></li>

                <li><?php echo wp_kses($userscontrol->profile->get_user_backend_menu_new('logout', 'Logout','fa-sign-out'), $userscontrol->allowed_html) ;?></li>
            
             </ul>
         
         </div> 
             
    </div>


