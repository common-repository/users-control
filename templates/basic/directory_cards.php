<?php
global $userscontrol;
$display_default_search = true;
?>

<div class="userscontrol-front-directorycard-wrap">


    	<ul class="userscontrol-front-default-results">
        
        <?php foreach($users_list['users'] as $user) : $user_id = $user->ID; 
		
		   if($pic_boder_type=="rounded") {
			   $class_avatar = "userscrontrol-avatar";			   
		   }

           if(!$userscontrol->profile->has_profile_bg($user_id)){	
            $user_prof_bg_color =$userscontrol->profile->get_cover_bg_color('user_prof_bg_color');	
            $cover_image = '';
            $bg_css_class = 'userscontrol-prof-cover';										
           }else{
            $user_prof_bg_color = '';    
            $cover_image =$userscontrol->profile->get_cover_bg_image($user_id);	
            $bg_css_class = '';	
           }
		
		?>     
            
            <li class="rounded <?php echo esc_attr($item_row_class)?>" >

            <div class="uu-front-list-cv-img <?php echo esc_attr($bg_css_class);?>">
                <?php echo wp_kses($cover_image, $userscontrol->allowed_html);?>
            </div>

            <div class="userscontrol-prof-photo">
               
                <?php                    
                    $userpic = $userscontrol->profile->get_user_pic( $user_id, $pic_size, $pic_type, $pic_boder_type, $pic_size_type);
                    echo wp_kses($userpic, $userscontrol->allowed_html);
                ?>             
               
            </div> 

              
               
               
               
               
            <div class="info-div">
            
			
				<p class="uu-direct-name"><?php echo esc_attr($userscontrol->user->get_display_name($user_id));?></p>
                
                
                <div class="social-icon-divider">                                       
                 
                </div> 
                
                 <?php if ($optional_fields_to_display!="") { ?>
                 
                 
                   <?php echo $userscontrol->user->display_optional_fields( $user_id,$display_country_flag, $optional_fields_to_display)?>   
                 
                 
                
                  <?php } ?>
                
                 </div> 
                 
                  <div class="userscontrol-view-profile-bar">
                  
                    <a class="userscontrol-btn-profile" href="<?php  echo esc_url($userscontrol->user->get_user_profile_permalink( $user_id));?>"><?php _e('See Profile','users-control')?></a>
                  
                 </div> 
            
            
            </li>
            
            
       <?php endforeach; ?>
                  
        
        </ul>
        
      

</div>