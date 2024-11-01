<?php
global $userscontrol;

if(!$userscontrol->profile->has_profile_bg($user_id)){	
	$user_prof_bg_color =$userscontrol->profile->get_cover_bg_color('user_prof_bg_color');	
    $cover_image = '';
    $bg_css_class = 'ucontrol-bg-color-custom';										
}else{
    $user_prof_bg_color = '';    
    $cover_image =$userscontrol->profile->get_cover_bg_image($user_id);	
    $bg_css_class = '';	
}

?>

<div class="userscontrol-profile-wrap">

    <div class="userscontrol-profile-header-cont <?php echo $bg_css_class;?> " <?php echo $user_prof_bg_color;?> >
        <div class="ucontrol-profile-header-img">
             <?php echo  wp_kses($cover_image, $userscontrol->allowed_html);?>

             <div class="ucontrol-profile-header-sub-img">
                <div class="ucontrol-thum">
                    <?php echo  wp_kses($userscontrol->profile->get_user_avatar_top($user_id), $userscontrol->allowed_html);?>   
                </div>
                <div class="ucontrol-uinfo">
                    <h1> <?php echo  wp_kses($user_display_name, $userscontrol->allowed_html);?> </h1>
                </div>
              </div>

        </div>
    </div>

    <div class="userscontrol-profile-menu-cont">
         <?php echo  wp_kses($profile_nav, $userscontrol->allowed_html);?>
    </div>

    <div class="userscontrol-profile-menu-cont-before">
         <?php
         $menu_after_cont = apply_filters( 'userscontrol_profile_after_menu',null);
         echo  wp_kses($menu_after_cont, $userscontrol->allowed_html); ?>
    </div>

    <div class="userscontrol-profile-main-cont">
        
        <div class="pr-col1">
             <?php echo  wp_kses($col1_cont, $userscontrol->allowed_html);?>
        </div>
    </div>
</div>