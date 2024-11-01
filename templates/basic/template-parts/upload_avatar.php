<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $userscontrol ;

$current_user = $userscontrol->user->get_user_info();
$user_id = $current_user->ID;

$currency_symbol =  $userscontrol->get_option('paid_membership_symbol');
$date_format =  $userscontrol->get_int_date_format();
$time_format =  $userscontrol->get_time_format();
$datetime_format =  $userscontrol->get_date_to_display();
?>

<h1><?php _e('Upload Your Avatar','users-control')?></h1>


    
<div class="userscontrol-main-app-list" id="userscontrol-backend-landing-1">

    
      <?php if($user_id==''){?>	
             
             
                 <div class="userscontrol-staff-left " id="userscontrol-staff-list">           	
            	 </div>
                 
                 <div class="userscontrol-staff-right " id="userscontrol-staff-details">
                 </div>
            
            <?php }else{ //upload avatar?>
            
           <?php
            
            $crop_image = "";
    
            if(isset($_POST['crop_image'])){
                
                        
                 $crop_image = sanitize_text_field($_POST['crop_image']);
                
            }
		   
		   $crop_image = sanitize_text_field($crop_image);
		   if( $crop_image=='crop_image') //displays image cropper
			{
			
			 $image_to_crop = sanitize_text_field($_POST['image_to_crop']);
			 
			
			 ?>
             
             <div class="userscontrol-staff-right-avatar " >
           		  <div class="pr_tipb_be">
                              
                            <?php 
                          //echo wp_kses($userscontrol->profile->display_avatar_image_to_crop($image_to_crop, $user_id), $userscontrol->allowed_html);
                            echo $userscontrol->profile->display_avatar_image_to_crop($image_to_crop, $user_id);
                            ?>                          
                              
                   </div>
                   
             </div>
            
           
		    <?php }else{  
			
			$user = get_user_by( 'id', $user_id );
			?> 
            
            <div class="userscontrol-staff-right-avatar " >
            
           
                   <div class="userscontrol-avatar-drag-drop-sector"  id="userscontrol-drag-avatar-section">
                   
                   <h3> <?php echo esc_attr($user->display_name)?><?php _e("'s Picture",'users-control')?></h3>
                        
                             <?php echo wp_kses($userscontrol->profile->get_user_pic( $user_id, 80, 'avatar', 'rounded', 'dynamic'), $userscontrol->allowed_html); ?>

                                                    
                             <div class="uu-upload-avatar-sect">
                              
                                     <?php echo $userscontrol->profile->avatar_uploader($user_id);
                                   //  echo wp_kses($userscontrol->profile->avatar_uploader($user_id), $userscontrol->allowed_html)
                                     ?>  
                              
                             </div>
                             
                        </div>  
                    
             </div>
             
             
              <?php }  ?>
            
             <?php }?>
</div>
