<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $userscontrol ;

$current_user = $userscontrol->user->get_user_info();
$user_id = $current_user->ID;
$user_email = $current_user->user_email;

$currency_symbol =  $userscontrol->get_option('paid_membership_symbol');
$date_format =  $userscontrol->get_int_date_format();
$time_format =  $userscontrol->get_time_format();
$datetime_format =  $userscontrol->get_date_to_display();


$active_subscriptions =  $userscontrol->user->get_my_active_subscriptions($user_id);


?>
    
<div class="userscontrol-main-app-list" id="userscontrol-backend-landing-1">


     
     <h2><i class="fa fa-lock"></i> <?php _e('Update your Password','users-control')?> </h2>
       <div class="userscontrol-common-cont">                      
                                           
                     
                       <form method="post" name="userscontrol-close-account" >
                       <p><?php  _e('Type your New Password','users-control');?></p>
                 			 <p><input type="password" name="p1" id="p1" /></p>
                            
                             <p><?php  _e('Re-type your New Password','users-control');?></p>
                 			 <p><input type="password"  name="p2" id="p2" /></p>
                            
                         <p>
                                                  
                         <button name="userscontrol-backenedb-eset-password" id="userscontrol-backenedb-eset-password" class="userscontrol-button-submit-changes" ><?php  _e('RESET PASSWORD','users-control');?>	</button>
                         
                         </p>
                         
                         <p id="userscontrol-p-reset-msg"></p>
               		  </form> 
                                           
                     </div>
                     
                     
           <h2> <i class="fa fa-envelope-o"></i> <?php  _e('Update Your Email','users-control');?>  </h2> 
           
                   <div class="userscontrol-common-cont">                                           
                     
                       <form method="post" name="userscontrol-change-email" >
                       <p><?php  _e('Type your New Email','users-control');?></p>
                 			 <p><input type="text" name="bup_email" id="bup_email" value="<?php echo esc_attr($user_email)?>" /></p>
                                                        
                         <p>
                                                  
                         <button name="userscontrol-backenedb-update-email" id="userscontrol-backenedb-update-email" class="userscontrol-button-submit-changes"><?php  _e('CHANGE EMAIL','users-control');?>	</button>
                         
                         </p>                         
                         <p id="userscontrol-p-changeemail-msg"></p>
               		  </form>
                      
                      </div>

</div>
