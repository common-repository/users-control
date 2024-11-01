<?php
global $userscontrol, $userscontrol_activation;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$va = get_option('userscontrol_c_key');
$domain = $_SERVER['SERVER_NAME'];
	
?>

 <div class="userscontrol-sect userscontrol-welcome-panel ">
 
 
  <?php if($va!='' ){ //user is running a validated copy?>
  
  <h3><?php _e('Congratulations!','users-control'); ?></h3>
   <p><?php _e("Your copy has been validated. You should be able to update the plugin through your WP Update sections. Also, you should start receiving an notice every time the plugin is updated.",'users-control'); ?></p>

   <?php }else{?>
        
        <h3><?php _e('Validate your copy','users-control'); ?></h3>
        <p><?php _e("Please fill out the form below with the serial number generated when you registered your domain through your account at userscontorl.com",'users-control'); ?></p>
        
        <p> <?php _e('INPUT YOUR SERIAL KEY','users-control'); ?></p>
         <p><input type="text" name="p_serial" id="p_serial" style="width:200px" /></p>
        
        
        <p class="submit">
	<input type="submit" name="submit" id="userscontrol-btn-validate-copy" class="button button-primary " value="<?php _e('CLICK HERE TO VALIDATE YOUR COPY','users-control'); ?>"  /> &nbsp; <span id="loading-animation"> &nbsp; <?php _e('Please wait ...','users-control'); ?> </span>
	
       </p>
       
       
        <?php }?>
       
       <p id='bup-validation-results'>
       
       </p>
                     
       
    
</div>  

