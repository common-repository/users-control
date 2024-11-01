<?php
class UserscontrolRole{
	var $table_prefix = 'userscontrol';
	var $ajax_p = 'userscontrol';
	
	function __construct(){		
	
	}
	
	public function get_available_user_roles(){
        global $wp_roles;
        $user_roles = array();

        if ( ! isset( $wp_roles ) ) 
            $wp_roles = new WP_Roles(); 

        $skipped_roles = array('administrator');

        foreach( $wp_roles->role_names as $role => $name ) {
			
            if(!in_array($role, $skipped_roles)){
				
                $user_roles[$role] = $name;
            }
        }

        return $user_roles;
    }
	
	public function get_available_roles(){
        global $wp_roles;
        $user_roles = array();

        if ( ! isset( $wp_roles ) ) 
            $wp_roles = new WP_Roles(); 

       // $skipped_roles = array('administrator');

        foreach( $wp_roles->role_names as $role => $name ) {
			
           // if(!in_array($role, $skipped_roles)){
				
                $user_roles[$role] = $name;
            //}
        }

        return $user_roles;
    }
	
	public function get_package_roles($package = null){
		global $wpdb, $userscontrol;
		
		$display = "";				
		$allowed_user_roles = $this->get_available_user_roles();		
		$meta= 'userscontrol_subscription_roles[]';
		$selected_roles = array();
		
		if($package!=null){
			
			if($package->membership_role!=''){
				$selected_roles = explode(',',$package->membership_role );		
			}
		
		}
		
        foreach ($allowed_user_roles as $key => $val){

			$sel ="";
			if(in_array($key,$selected_roles)) {
				$sel = 'checked="checked"';
			}
			   
			$display .= '<label>
   					 <input type="checkbox" name="'. $meta .'" value="'. $key .'" id="'.$meta.'"  '. $sel .'/>'. $val .'</label>';
			 
        }
		return  $display;
	}

	/* Setting for alowed user roles from available user roles */
	public function uultra_allowed_user_roles_registration() {
		global $wp_roles;
	   
		$user_roles = array();
	   
		if ( ! isset( $wp_roles ) ) 
		   $wp_roles = new WP_Roles(); 
	   
		$current_option = get_option('userscontrol_options');
		$user_roles_registration = $current_option['choose_roles_for_registration'];
	   
			   
		$allowed_user_roles = is_array($user_roles_registration) ? $user_roles_registration : array($user_roles_registration);
	   
			   $default_role = get_option("default_role");
			   if(!in_array($default_role, $allowed_user_roles)){
				   array_push($allowed_user_roles, $default_role);
			   }
	   
			   if('' == $current_option['choose_roles_for_registration']){
				   $user_roles[$default_role] = $wp_roles->role_names[$default_role];
				   return $user_roles;
			   }
	   
			   foreach ($allowed_user_roles as $usr_role) {
				   $user_roles[$usr_role] = $wp_roles->role_names[$usr_role];
			   }
	   
			   
			   return $user_roles;
		   }
		   
	/* This will give us the roles of the given user */
	public function get_user_roles_by_id($user_id){
		$user = new WP_User($user_id);			   
		if (!empty($user->roles) && is_array($user->roles)) {
			$this->user_roles = $user->roles;
			return $user->roles;
		} else {
			$this->user_roles = array();
			return array();
		}
	}
		   
	/* Check the permission to show/edit given field by user Id */
	public function fields_by_user_role($user_role, $user_role_list) {
	   
		$show_status = FALSE;
			   if ('0' == $user_role) 
			   {
				   $show_status = TRUE;
				   
			   } else {
	   
				  if('' !=  $user_role_list)
				  {
					   $user_role_list = explode(',', $user_role_list);
					   
					   foreach ($this->user_roles as $role)
					   {
						   if (in_array($role, $user_role_list)) {
							   $show_status = TRUE;
						   }
					   }
				   }
				   
			   }
		return $show_status;
	}
	
	
	
}
$key = "role";
$this->{$key} = new UserscontrolRole();
?>