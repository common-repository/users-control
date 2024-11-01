<?php
class UserscontrolUser
{
	var $table_prefix = 'userscontrol';
	var $ajax_p = 'userscontrol';
    var $output = '';
	
	function __construct() 	{
		$this->current_page = sanitize_url($_SERVER['REQUEST_URI']);		
		add_action( 'wp_ajax_'.$this->ajax_p.'_subscriptions_by_member', array( &$this, 'subscriptions_by_member' ));
        add_action('init', array( &$this, 'handle_init' ));	

		$this->method_dect = array(
            'text' => 'text_box',
            'fileupload' => '',
            'textarea' => 'text_box',
            'select' => 'drop_down',
            'radio' => 'drop_down',
            'checkbox' => 'drop_down',
            'password' => '',
            'datetime' => 'text_box'
        );

		$this->inshort_codes();
       
	}

	public function inshort_codes()	{
		add_shortcode( 'userscontrol_directory', array(&$this,'_users_directory') );
		add_shortcode( 'userscontrol_user_cards', array(&$this,'_user_cards') );
		add_shortcode( 'userscontrol_searchbox', array(&$this,'_searchbox') );			
	}

    function handle_init(){

        if (isset($_POST['userscontrol-profile-edition-form'])){			
			/* This prepares the array taking values from the POST */
			$this->prepare( $_POST );         
       			
			/* We validate everthying before updateing the profile */
			//$this->handle();
			
			/* Let's Update the Profile */
			$this->update_profile_user();
				
		}	
    }

    function update_profile_user() 	{
		global  $userscontrol;
        require_once(ABSPATH . 'wp-includes/user.php');	
		$user_id = get_current_user_id();		
		$logged_in_user = get_user_by('id',$user_id);
		$custom_form = $this->get_user_meta( 'userscontrol_custom_registration_form');
		$array = array();
		
		if($custom_form!=""){			
			$custom_form = 'userscontrol_profile_fields_'.$custom_form;		
			$array = get_option($custom_form);		
		}else{			
			$array = get_option('userscontrol_profile_fields');			
		}
		
		$array_check = $array;		
		 // Get list of dattime fields
        $date_time_fields = array();
		if(!is_array($array)){$array=array();}
        foreach ($array as $key => $field){
            extract($field);
            if (isset($array[$key]['field']) && $array[$key]['field'] == 'checkbox'){
                update_user_meta($user_id, $meta, null);
            }
            // Filter date/time custom fields
            if (isset($array[$key]['field']) && $array[$key]['field'] == 'datetime'){
                array_push($date_time_fields, $array[$key]['meta']);
            }	
        }

		/* Check if the were errors before updating the profile */
		if (!isset($this->errors)) {
			/* Now update all user meta */
			foreach($this->usermeta as $key => $value) {
				// save checkboxes
                if (is_array($value)) 	{ // checkboxes
                    $value = implode(',', $value);
                }
				update_user_meta($user_id, "hide_".$key, "");
				if($key=="display_name"){
					wp_update_user( array( 'ID' => $user_id, 'display_name' => esc_attr($value) ) );
				}
					
				if ($this->field_allow_html($key,$array_check)) {
					update_user_meta($user_id, $key, $value);
					}else{
					update_user_meta($user_id, $key, esc_attr($value));
				}	
			}
		}

        //ouput messsage
        $message = $userscontrol->get_message_box_display('success', __('The profile has been updated', 'users-control'));
        $this->output = $message ;

	}

    function field_allow_html ($field_to_check, $fields_set){
		foreach ($fields_set as $key => $field){
            extract($field);			
			if($meta==$field_to_check){
                if (isset($allow_html) && $allow_html == '1') {
					return true;					
				}else{					
					return false;
				}  			
			} 		                    		
        }
		return false;
	}

    /*Prepare user meta*/
	function prepare ($array ){
		foreach($array as $k => $v){
			if ($k == 'usersultra-update' || $k == 'userscontrol-profile-edition-form'  ) continue;			
			$this->usermeta[$k] = sanitize_text_field($v);
		}
		return $this->usermeta;
	}

	public function  _users_directory ($atts){
		global $userscontrol;	        
        return $this->show_users_directory($atts);
	}

	public function  _user_cards ($atts){
		global $userscontrol;	        
        return $this->show_user_cards($atts);
	}

	

	public function  _searchbox ($atts){
		global $userscontrol;
		return $this->_search_form( $atts );			
	}
	
	public function get_user_info(){
		$current_user = wp_get_current_user();
		return $current_user;	
	}

	public function show_user_cards($atts)	{
		global $userscontrol;
		
		$atts_temp = $atts;
		
		extract( shortcode_atts( array(
		
			'template' => 'directory_cards', //this is the template file's name			
			'container_width' => '100%', // this is the main container dimension
			'item_cols' => 4, // this is the width of each item or user in the directory
			'item_height' => 'auto', // auto height
			'list_per_page' => 4, // how many items per page
			'pic_type' => 'avatar', // display either avatar or main picture of the user
			'pic_boder_type' => 'none', // rounded
			'pic_size_type' => 'dynamic', // dynamic or fixed			
			'pic_size' => 100, // size in pixels of the user's picture
			'optional_fields_to_display' => '', // 
			
			'display_to_logged_in_only' => '', // yes or null or empy
			'display_to_logged_in_only_text' => __('Only logged in users can see this page', 'users-control'), 
			
			'display_social' => 'no', // display social
			'display_country_flag' => 'name', // display flag, no,yes,only, both. Only won't display name
			'display_total_found' => 'no', // display total found
			'display_total_found_text' => __('Users', 'users-control'), // display total found			
			'list_order' => 'ASC', // asc or desc ordering
			'sort_by' => 'ID', // 
			'role' => '', // filter by role
			'relation' => 'AND', // relation
			'exclude' => NULL // exclude by user id
		), $atts ) );
		
		$page = $this->get_current_page();		
		$search_array = array('list_per_page' => $list_per_page, 'order' => $list_order, 'sortby' => $sort_by);	
				
		$args= array('per_page' => $list_per_page, 
		'relation' => $relation,
		 'role' => $role, 
		 'exclude' => $exclude, 
		 'order' => $list_order,
		 'sortby' => $sort_by);

		if($item_cols==1){
			$item_row_class = 'uc-1';
		}elseif($item_cols==2){	
			$item_row_class = 'uc-2';
		}elseif($item_cols==3){
			$item_row_class = 'uc-3';
		}elseif($item_cols==4){
			$item_row_class = 'uc-4';
		}	
		
		
		$html ='';		
		$html .='<div class="userscontrol-front-directory-wrap">
		       	<div class="userscontrol-searcher">
			    </div>';
				
		//only logged in  
		
		if($display_to_logged_in_only=='yes' && !is_user_logged_in()){
			$html .=' <p>'. $display_to_logged_in_only_text.'</p>';
		
		}else{
			
			//display to all users		
			$this->current_users_page = $page;		
			$this->search_result($args);
						
			$users_list = $this->searched_users;

			$users_tot = 0;
			if(isset($users_list['total'])){
				$users_tot = $users_list['total'];
			}
			
			//display pages
			$disp_array = array('total' => $users_tot, 'text' => $display_total_found_text);		
			$total_f = $this->get_total_found($disp_array);	
			if ($display_total_found=='yes') {			
				$html .=$total_f;
			}
			
			if(isset($users_list['users']) && count($users_list['users'])>0){
			
				if($template == 'directory_cards'){

					 ob_start();
					 $theme_path = get_template_directory();
					 if(file_exists($theme_path."/userscontrol/".$template.".php"))	{
						 include($theme_path."/userscontrol/".$template.".php");					 
					 }else{			 
						 include(userscontrol_path.'/templates/basic/'.$template.'.php');
					 }		
					 $content = ob_get_clean();
					return  $content;	
					
				} // end if
				 
		   } // end if

		 
	     } //end if logged in users

 		$html .='</div>';
		return $html;
	}

    public function show_users_directory($atts)	{
		global $userscontrol;
		
		$atts_temp = $atts;
		
		extract( shortcode_atts( array(
		
			'template' => 'directory_default', //this is the template file's name			
			'container_width' => '100%', // this is the main container dimension
			'item_cols' => 4, // this is the width of each item or user in the directory
			'item_height' => 'auto', // auto height
			'list_per_page' => 4, // how many items per page
			'pic_type' => 'avatar', // display either avatar or main picture of the user
			'pic_boder_type' => 'none', // rounded
			'pic_size_type' => 'dynamic', // dynamic or fixed			
			'pic_size' => 100, // size in pixels of the user's picture
			'optional_fields_to_display' => '', // 
			
			'display_to_logged_in_only' => '', // yes or null or empy
			'display_to_logged_in_only_text' => __('Only logged in users can see this page', 'users-control'), 
			
			'display_social' => 'no', // display social
			'display_country_flag' => 'name', // display flag, no,yes,only, both. Only won't display name
			'display_total_found' => 'yes', // display total found
			'display_total_found_text' => __('Users', 'users-control'), // display total found			
			'list_order' => 'ASC', // asc or desc ordering
			'sort_by' => 'ID', // 
			'role' => '', // filter by role
			'relation' => 'AND', // relation
			'exclude' => NULL // exclude by user id
		), $atts ) );
		
		$page = $this->get_current_page();		
		$search_array = array('list_per_page' => $list_per_page, 'order' => $list_order, 'sortby' => $sort_by);	
				
		$args= array('per_page' => $list_per_page, 
		'relation' => $relation,
		 'role' => $role, 
		 'exclude' => $exclude, 
		 'order' => $list_order,
		 'sortby' => $sort_by);

		if($item_cols==1){
			$item_row_class = 'uc-1';
		}elseif($item_cols==2){	
			$item_row_class = 'uc-2';
		}elseif($item_cols==3){
			$item_row_class = 'uc-3';
		}elseif($item_cols==4){
			$item_row_class = 'uc-4';
		}	
		
		
		$html ='';		
		$html .='<div class="userscontrol-front-directory-wrap">
		       	<div class="userscontrol-searcher">
			    </div>';
				
		//only logged in  
		
		if($display_to_logged_in_only=='yes' && !is_user_logged_in()){
			$html .=' <p>'. $display_to_logged_in_only_text.'</p>';
		
		}else{
			
			//display to all users		
			$this->current_users_page = $page;		
			$this->search_result($args);
						
			$users_list = $this->searched_users;

			$users_tot = 0;
			if(isset($users_list['total'])){
				$users_tot = $users_list['total'];
			}
			
			//display pages
			$disp_array = array('total' => $users_tot, 'text' => $display_total_found_text);		
			$total_f = $this->get_total_found($disp_array);	
			if ($display_total_found=='yes') {			
				$html .=$total_f;
			}
			
			if(isset($users_list['users']) && count($users_list['users'])>0){
			
				if($template == 'directory_default' || $template == 'directory_2'){

					 ob_start();
					 $theme_path = get_template_directory();
					 if(file_exists($theme_path."/userscontrol/".$template.".php"))	{
						 include($theme_path."/userscontrol/".$template.".php");					 
					 }else{			 
						 include(userscontrol_path.'/templates/basic/'.$template.'.php');
					 }		
					 $content = ob_get_clean();
					return  $content;	

				}elseif($template == 'directory_table'){
					
					//columns = 
					$table_columns = array();
					$table_columns =  explode(",", $columns);
					
					$table_headers = array();
					$table_metas = array();				
					foreach($table_columns as $col)	{
						$col_data = explode(":",$col);					
						$table_headers[] = array('label'=>$col_data[0], 'tooltip'=>$col_data[3]);
						$table_metas[] = array('meta'=>$col_data[1] , 'visible'=>$col_data[2]);				
					
					}
					
					 ob_start();
					 $theme_path = get_template_directory();
					 if(file_exists($theme_path."/userscontrol/".$template.".php"))	{
						 include($theme_path."/userscontrol/".$template.".php");					 
					 }else{			 
						 include(userscontrol_path.'/templates/basic/'.$template.'.php');
					 }		
					 $content = ob_get_clean();
					return  $content;					
				
				}elseif($template == 'directory_minified'){
					
					ob_start();
					$theme_path = get_template_directory();
					if(file_exists($theme_path."/userscontrol/".$template.".php"))	{
						include($theme_path."/userscontrol/".$template.".php");					 
					}else{			 
						include(userscontrol_path.'/templates/basic/'.$template.'.php');
					}		
					$content = ob_get_clean();
				   return  $content;						
				 
				} //end if			 
				 
		   } // end if

		 
	     } //end if logged in users

 		$html .='</div>';
		return $html;
	}

   

    public function edit_profile_form( $sidebar_class=null, $redirect_to=null )	{
		global  $userscontrol;
		$html = null;
		
		$user_id = get_current_user_id();
        //get user form		
		$custom_form = $this->get_user_meta( 'userscontrol_custom_registration_form');

		$html .= '<div class="userscontrol-clear"></div>';				
		$html .= '<form action="" method="post" id="userscontrol-profile-edition-form">';	
        $html .='<input type="hidden" name="userscontrol-custom-form" value="'.$custom_form.'" />';	
		
		$array = array();
		if($custom_form!=""){			
			$custom_form = 'userscontrol_profile_fields_'.$custom_form;		
			$array = get_option($custom_form);		
		}else{
			$array = get_option('userscontrol_profile_fields');			
		}	
		
		if(!is_array($array)){
			$array = array();		
		}
		
		$i_array_end = end($array);
		if(isset($i_array_end['position'])){
		    $array_end = $i_array_end['position'];
		    if ($array[$array_end]['type'] == 'separator') {
		        unset($array[$array_end]);
		    }
		}
		

    
        foreach($array as $key => $field) {
			
			$show_to_user_role_list = '';
			$show_to_user_role = 0;			
			$edit_by_user_role = 0;
			$edit_by_user_role_list = '';
            $disabled = null;	

			extract($field);
			
			// WP 3.6 Fix
			if(!isset($deleted))
			    $deleted = 0;
			
			if(!isset($private))
			    $private = 0;
			
			if(!isset($required))
			    $required = 0;
			
			$required_class = '';
			if($required == 1 && in_array($field, $userscontrol->include_for_validation)){
			    $required_class = ' required';
			}

			$required_text='';
			$can_hide = null;
			
			
					
			/* Fieldset separator */
			if ( $type == 'separator' && $deleted == 0 && $private == 0 ){
				if(!isset($show_to_user_role) || $show_to_user_role ==""){
					$show_to_user_role = 0;			
				}
				
				if(!isset($show_to_user_role_list) || $show_to_user_role_list =="")	{
					$show_to_user_role_list = '';	
				}
				
				$userscontrol->role->get_user_roles_by_id($user_id);
				$show_field_status =  $userscontrol->role->fields_by_user_role($show_to_user_role, $show_to_user_role_list);
				
				if ($show_field_status){
					$html .= '<div class="userscontrol-field userscontrol-seperator userscontrol-edit userscontrol-edit-show">'.$name.'</div>';
				}
				
			}
			
			if ( $type == 'usermeta' && $deleted == 0 && $private == 0){
	
			
				if(!isset($show_to_user_role) || $show_to_user_role ==""){
					$show_to_user_role = 0;			
				}
				
				if(!isset($show_to_user_role_list) || $show_to_user_role_list ==""){
					$show_to_user_role_list = '';					
				}else{
				}
			 
				$userscontrol->role->get_user_roles_by_id($user_id);
				$show_field_status =  $userscontrol->role->fields_by_user_role($show_to_user_role, $show_to_user_role_list);
				 
			    if ($show_field_status){
				
				    $html .= '<div class="userscontrol-field userscontrol-edit userscontrol-edit-show">';
				
                    /* Show the label */
                    if (isset($array[$key]['name']) && $name){
                        $html .= '<label class="userscontrol-field-type" for="'.$meta.'">';						
                        $html .= '<span>'.$name.' '.$required_text.' </span></label>';					
                    } else {
                        $html .= '<label class="userscontrol-field-type">&nbsp;</label>';
                    }
                    
                    $html .= '<div class="userscontrol-field-value">';
                    
                    if ($can_edit == 0){					
                        $disabled = 'disabled="disabled"';                        
                    }else{                     
                        $disabled = null;
                    }
                    
				
                    if(!isset($edit_by_user_role) || $edit_by_user_role =="")
                    {
                        $edit_by_user_role = 0;			
                    }
                    
                    if(!isset($edit_by_user_role_list) || $edit_by_user_role_list =="") {
                        $edit_by_user_role_list = '';	
                        
                    }
				
                    $userscontrol->role->get_user_roles_by_id($user_id);
                    $edit_field_status =  $userscontrol->role->fields_by_user_role($edit_by_user_role, $edit_by_user_role_list);
                    
                    if (!$edit_field_status ) {
                        $disabled = 'disabled="disabled"';
                    }
					
				switch($field) {
					case 'textarea':
						//check if html editor active
						$html .= $this->get_me_wphtml_editor($meta, $this->get_user_meta( $meta));
						break;							
					case 'text':
						$html .= '<input type="text" class="userscontrol-input'.$required_class.'" name="'.$meta.'" id="'.$meta.'" value="'.$this->get_user_meta( $meta).'"  title="'.$name.'"  '.$disabled.'/>';
						break;
					case 'datetime':
						$html .= '<input type="text" class="userscontrol-input'.$required_class.' userscontrol-datepicker" name="'.$meta.'" id="'.$meta.'" value="'.$this->get_user_meta( $meta).'"  title="'.$name.'"  '.$disabled.'/>';
						break;
					case 'select':

						$option = null;
						
						if (isset($array[$key]['predefined_options']) && $array[$key]['predefined_options']!= '' && $array[$key]['predefined_options']!= '0' ) {
							$loop = $userscontrol->commmonmethods->get_predifined( $array[$key]['predefined_options'] );
						}elseif(isset($array[$key]['choices']) && $array[$key]['choices'] != '') {								
							$loop = $userscontrol->userscontrol_one_line_checkbox_on_window_fix($array[$key]['choices']);
						}
							
						if (isset($loop)) {
							$html .= '<select class="userscontrol-input'.$required_class.'" name="'.$meta.'" id="'.$meta.'" title="'.$name.'" '.$disabled.'>';							
							foreach($loop as $sh) {
								$option = trim($option);								    
								$html .= '<option value="'.$sh.'" '.selected( $this->get_user_meta( $meta), $sh, 0 ).' '.$disabled.'>'.$sh.'</option>';
							}
								
							$html .= '</select>';
						}
							$html .= '<div class="userscontrol-clear"></div>';
							
						break;
							
					case 'radio':
						if (isset($array[$key]['choices'])){
							$loop = $userscontrol->userscontrol_one_line_checkbox_on_window_fix($choices);
						}
							
						if (isset($loop) && $loop[0] != '') {
							 $counter =0;
							  
							foreach($loop as  $option) {
								if($counter >0)
								   $required_class = '';
								    
								$option = trim($option);									
								$html .= '<label class="userscontrol-radio"><input type="radio" class="'.$required_class.'" title="'.$name.'" '.$disabled.' id="userscontrol_multi_radio_'.$meta.'_'.$counter.'" name="'.$meta.'" value="'.$option.'" '.checked( $this->get_user_meta( $meta), $option, 0 );
								$html .= '/> <label for="userscontrol_multi_radio_'.$meta.'_'.$counter.'"><span></span>'.$option.'</label> </label>';
								$counter++;
							}
						}
							$html .= '<div class="userscontrol-clear"></div>';
							break;
							
					case 'checkbox':

						if (isset($array[$key]['choices'])) {
							$loop = $userscontrol->userscontrol_one_line_checkbox_on_window_fix($choices);
						}

						if (isset($loop) && $loop[0] != '') {
							  $counter =0;
								foreach($loop as $option) {
								   
								   if($counter >0)
								        $required_class = '';
								  
								  $option = trim($option);
									$html .= '<div class="userscontrol-checkbox"><input type="checkbox" class="'.$required_class.'" title="'.$name.'" name="'.$meta.'[]" id="userscontrol_multi_box_'.$meta.'_'.$counter.'" value="'.$option.'" '.$disabled.' ';
									
									
									$values = explode(',', $this->get_user_meta($meta));
									
									if (in_array($option, $values)) {
										
									$html .= 'checked="checked"';
									}
									$html .= '/> <label  for="userscontrol_multi_box_'.$meta.'_'.$counter.'"><span></span>'.$option.'</label></div>';
									
									$counter++;
								}
							}
							$html .= '<div class="userscontrol-clear"></div>';
							break;
							
					}
					
					if (isset($array[$key]['help_text']) && $help_text != ''){
						$html .= '<div class="userscontrol-help">'.$help_text.'</div><div class="userscontrol-clear"></div>';
					}					
				
					
					/*User can hide this from public*/
					if (isset($array[$key]['can_hide']) && $can_hide == 1) {
						
						//get meta
						$check_va = "";
						$ischecked = $this->get_user_meta("hide_".$meta);
						 
						 if($ischecked==1) $check_va = 'checked="checked"';
						
						$html .= '<div class="userscontrol-hide-from-public">
										<input type="checkbox" name="hide_'.$meta.'" id="hide_'.$meta.'" value="1" '.$check_va.' /> <label for="hide_'.$meta.'"><span></span>'.__('Hide from Public','users-control').'</label>
									</div>';

					} elseif ($can_hide == 0 && $private == 0) {
						
						
					   
					}
					
				$html .= '</div>';
				$html .= '</div><div class="userscontrol-clear"></div>';
				
				} //end if roles
				
			} //end if user meta
		}
		
		
		$html .= '<div class="userscontrol-field userscontrol-edit userscontrol-edit-show">
						<label class="userscontrol-field-type userscontrol-field-type-'.$sidebar_class.'">&nbsp;</label>
						<div class="userscontrol-field-value">
						    <input type="hidden" name="userscontrol-profile-edition-form" value="userscontrol-profile-edition-form" />
							<input type="submit" name="userscontrol-update" id="userscontrol-update" class="userscontrol-button" value="'.__('Update','users-control').'" />
						</div>
					</div><div class="userscontrol-clear"></div>';
					
		
		$html .= '</form>';
		

		
		return $html;
	}

	function get_me_wphtml_editor($meta, $content){
		// Turn on the output buffer
		ob_start();		
		$editor_id = $meta;				
		$editor_settings = array('media_buttons' => false , 'textarea_rows' => 15 , 'teeny' =>true); 
		wp_editor( $content, $editor_id , $editor_settings);
		// Store the contents of the buffer in a variable
		$editor_contents = ob_get_clean();
		// Return the content you want to the calling function
		return $editor_contents;	
	}

    public function get_user_meta ($meta){
		$user_id = get_current_user_id();		
		return get_user_meta( $user_id, $meta, true);		
	}
	
	public function get_user_meta_custom ($user_id, $meta){
		return get_user_meta( $user_id, $meta, true);		
	}

    public function get_display_name($user_id)	{
		global  $userscontrol;
		
		$display_name = "";
		
		$display_type = $userscontrol->get_option('uprofile_setting_display_name');
		$display_type = 'display_name';
		
		$user = get_user_by('id',$user_id);
		
		if ($display_type == 'fr_la_name' || $display_type == '' ){
			$f_name = get_user_meta($user_id, 'first_name', true);
	        $l_name = get_user_meta($user_id, 'last_name', true);	
			
			$display_name = $f_name. " " .  $l_name;			
			
		}elseif ($display_type == 'username'){
				
			$display_name =$user->user_login;
		
		}elseif($display_type == 'user_nicename'){
			$display_name =$user->user_nicename;
		}elseif($display_type == 'display_name'){
			$display_name =$user->display_name;
		}
		return ucfirst($display_name);
	}

    /******************************************
	Get permalink for user
	******************************************/
	function get_user_profile_permalink( $user_id=0){		
		global  $userscontrol;		
		$wp_rewrite = new WP_Rewrite();		
		require_once(ABSPATH . 'wp-includes/link-template.php');			
				
		if ($user_id > 0) {
		
			$user = get_userdata($user_id);
			$nice_url_type = $userscontrol->get_option('userscontrol_permalink_type');
			$nice_url_type =  'username';			
						
			if ($nice_url_type == 'ID' || $nice_url_type == '' ) {
				$formated_user_login = $user_id;
			}elseif ($nice_url_type == 'username') {
				$formated_user_login = $user->user_nicename;
				$formated_user_login = str_replace(' ','-',$formated_user_login);
			}elseif ($nice_url_type == 'name'){
				$formated_user_login = $userscontrol->get_fname_by_userid( $user_id );
			}elseif ($nice_url_type == 'display_name'){
				$formated_user_login = get_user_meta( $user_id, 'display_name', true);					
				$formated_user_login = str_replace(' ','-',$formated_user_login);
			}elseif ($nice_url_type == 'custom_display_name'){
				$formated_user_login = get_user_meta( $user_id, 'display_name', true);					
				$formated_user_login = str_replace(' ','-',$formated_user_login);						
			}
			
			$formated_user_login = strtolower ($formated_user_login);
			$profile_page_id = $userscontrol->get_option('profile_page_id');	
			
    		if ( $nice_url_type == '' )	{
				$link = add_query_arg( 'userscontrol_username', $formated_user_login, get_page_link($profile_page_id) );
			}else{
				$link = trailingslashit ( trailingslashit( get_page_link($profile_page_id) ) . $formated_user_login );
			}
		
		}else{
			$link = get_page_link($page_id);
		}

		return $link;
	}

    function get_front_template($template_name,  $users_list, $atts){
        //turn on output buffering to capture script output
        ob_start();

		extract($atts);
        //include the specified file			
		$theme_path = get_template_directory();
		if(file_exists($theme_path."/userscontrol/".$template_name.".php"))	{
			include($theme_path."/userscontrol/".$template_name.".php");
		
		}else{

			include(userscontrol_path.'/templates/basic/'.$template_name.'.php');
		}		
        $content = ob_get_clean();
		return  $content;
    }

    public function get_total_found($users_list){
		extract($users_list);		
		if($total=="" ){$total=0;}		
		$html = '<div class="userscontrol-search-results">
			<h1>'.__('Total found: ','users-control').''.$total .' '.$text.'</h1>
			
			</div>';			
		return $html;
	}

    /* Apply search params and Generate Results */	
	function search_result($args) {		
		global $wpdb,$blog_id, $wp_query, $wp_rewrite, $paged;	
		extract($args);		

		$arr = array();
		
		$memberlist_verified = 1;		
		$blog_id = get_current_blog_id();
		$wp_query->query_vars['paged'] > 1 ? $page = $wp_query->query_vars['paged'] : $page = 1;
		
		$offset = ( ($page -1) * $per_page);

		/** QUERY ARGS BEGIN **/		
		if (isset($args['exclude']) && $args['exclude']!=''){
			$exclude = explode(',',$args['exclude']);
			$query['exclude'] = $exclude;
		}
		
		
		/*This is applied only if we have to filder certain roles*/
		if (isset($role) &&  $role!="")	{
			$roles = explode(',',$role);
            if (count($roles) >= 2){				
				$query['meta_query'] = array('relation' => 'OR' );
			}
			
			foreach($roles as $subrole){				
				$query['meta_query'][] = array(
					'key' => $wpdb->get_blog_prefix( $blog_id ) . 'capabilities',
					'value' => $subrole,
					'compare' => 'like'
				);
			}
			
		}else{
			$query['meta_query'] = array('relation' => strtoupper($relation) );
		}
	
	    if (isset($_GET['userscontrol_search'])){
            foreach ($_GET['userscontrol_search'] as $key => $value){		
                $target =  $value;
                            /*if ($->field_type($key) == 'multiselect' ||
                                $->field_type($key) == 'checkbox' ||
                                $userscontrol->field_type($key) == 'checkbox-full'
                                ) {
                                $like = 'like';
                            } else {
                                $like = '=';
                            }*/
                        
                $like = 'like';
                if (isset($target)  && $target != '' && $key != 'role' ){
                    if (isset($args[$key])  && substr( trim( htmlspecialchars_decode($args[$key])  ) , 0, 1) === '>'){
                        $choices = explode('>', trim(  htmlspecialchars_decode($args[$key]) ));
                        $target = $choices[1];
                        $query['meta_query'][] = array(
                                        'key' => $key,
                                        'value' => $target,
                                        'compare' => '>'
                            );
                            
                }elseif (isset($args[$key])  && substr( trim(  htmlspecialchars_decode($args[$key]) ) , 0, 1) === '<') {
                                    $choices = explode('<', trim(  htmlspecialchars_decode($args[$key]) ));
                                    $target = $choices[1];
                                    $query['meta_query'][] = array(
                                        'key' => $key,
                                        'value' => $target,
                                        'compare' => '<'
                                    );
                                    
                } elseif (isset($args[$key])  && strstr( esc_attr( trim(  $args[$key] ) ) , ':'))				{
                                    $choices = explode(':', esc_attr( trim(  $args[$key] ) ));
                                    $min = $choices[0];
                                    $max = $choices[1];
                                    $query['meta_query'][] = array(
                                        'key' => $key,
                                        'value' => array($min, $max),
                                        'compare' => 'between'
                                    );
                                    
                } elseif (isset($args[$key])  && strstr( esc_attr( trim( $args[$key] ) ) , ',')){
                                $choices = explode(',', esc_attr( trim(  $args[$key] ) ));
                                    foreach($choices as $choice){
                                        $query['meta_query'][] = array(
                                            'key' => $key,
                                            'value' => $choice,
                                            'compare' => $like
                                        );
                                    }
                    } else {
                        
                            if(!is_string($target)) $target= '';
                                    
                                $query['meta_query'][] = array(
                                'key' => $key,
                                'value' => esc_attr( trim( $target ) ),
                                'compare' => $like
                            );
                    }
                                
                }
                    
                            
                            
                            
            } //end for each
				 
		} //end if 
	
		if ($memberlist_verified){
            $query['meta_query'][] = array(
					'key' => 'userscontrol_account_status',
					'value' => 'active',
					'compare' => 'LIKE'
			);
		}
			
		if (isset($memberlist_withavatar) && $memberlist_withavatar == 1){
			$query['meta_query'][] = array(
					'key' => 'profilepicture',
					'value' => '',
					'compare' => '!='
			);
		}			
			
		//CUSTOM SEARCH FILTERS 		
		if (isset($_GET['userscontrol_combined_search'])){
			
			/* Searchuser query param */
			$search_string = esc_attr( trim( get_value('userscontrol_combined_search') ) );
			
			if ($search_string != ''){
                if (get_value('userscontrol_combined_search_fields') != '' && get_value('userscontrol_combined_search') != ''){
					//$customfilters = explode(',',$args['memberlist_filters']);
					
				   $customfilters = explode(',', get_value('userscontrol_combined_search_fields'));
				   $combined_search_text = esc_sql(get_value('userscontrol_combined_search'));

					
					if ($customfilters){
						if (count($customfilters) > 1){
							//$query['meta_query']['relation'] = 'or';
						}				
										
						$query['meta_query'][] = array(
							'key' => 'first_name',
							'value' => $search_string,
							'compare' => 'LIKE'
						);
					}
				}
				
			}
			
		}				
			
		if ($sortby) $query['orderby'] = $sortby;			
		if ($order) $query['order'] = strtoupper($order); 
			
		/** QUERY ARGS END **/			
		$query['number'] = $per_page;
		$query['offset'] = $offset;
	
			/* Search mode */
		if ( ( isset($_GET['userscontrol_search']) && !empty($_GET['userscontrol_search']) ) || count($query['meta_query']) > 1 ){
			$count_args = array_merge($query, array('number'=>10000));
			unset($count_args['offset']);
			$user_count_query = new WP_User_Query($count_args);
		}

		if ($per_page){			
			/* Get Total Users */
			if ( ( isset($_GET['userscontrol_search']) && !empty($_GET['userscontrol_search']) ) || count($query['meta_query']) > 1 ){
				$user_count = $user_count_query->get_results();								
				$total_users = $user_count ? count($user_count) : 1;				
			} else {		
				$result = count_users();
				$total_users = $result['total_users'];
			}			
			$total_pages = ceil($total_users / $per_page);		
		}	
		
		$wp_user_query = new WP_User_Query($query);
		
		//print_r($query);
		if (! empty( $wp_user_query->results )){
			$arr['total'] = $total_users;
			$arr['paginate'] = paginate_links( array(
					//'base'         => @add_query_arg('paged','%#%'),
					'total'        => $total_pages,
					'current'      => $page,
					'show_all'     => false,
					'end_size'     => 1,
					'mid_size'     => 2,
					'prev_next'    => true,
					'prev_text'    => __('« Previous','users-control'),
					'next_text'    => __('Next »','users-control'),
					'type'         => 'plain',
				));
			$arr['users'] = $wp_user_query->results;
		}
		
		$this->searched_users = $arr;
    }

    public function get_current_page(){
		$page = "";		
		if(isset($_GET["userscontrol-page"])){
			$page = sanitize_text_field($_GET["userscontrol-page"]);		
		}else{			
			$page = 1;		
		}		
		return $page;
	}

    /*Used for the directory listings*/	
	public  function display_optional_fields ($user_id, $display_country_flag, $fields) {
		global  $userscontrol;
		
		$fields_list = "";
		$fields  = explode(',', $fields);
		
		foreach ($fields as $field) {
			//get meta			
			$u_meta = get_user_meta($user_id, $field, true);
			
			if( $field =='country'){
				//rule applied to country only
			
				if($display_country_flag=='only'){ //only flag
					if($u_meta==""){
						//$fields_list .= __("Country not available", 'users-control');						
					
					}else{

						//get country ISO code		
						$isocode = array_search($u_meta, $userscontrol->commmonmethods->get_predifined('countries'));				
						$isocode  = userscontrol_url."libs/flags/24/".$isocode.".png";					
						$img = '<img src="'.$isocode.'"  alt="'.$u_meta.'" title="'.$u_meta.'" class="userscontrol-country-flag"/>';					
						$fields_list .= "<p class='country_name'>".$img."</p>";
					}					
									
				}elseif($display_country_flag=='both'){
					
					if($u_meta=="") {
						$fields_list .= "<p class='country_name'></p>";
					}else{
					
						$isocode = array_search($u_meta, $userscontrol->commmonmethods->get_predifined('countries'));				
						if($isocode!="0"){
							$isocode  = userscontrol_url."libs/flags/24/".$isocode.".png";					
							$img = '<img src="'.$isocode.'"  alt="'.$u_meta.'" title="'.$u_meta.'" class="userscontrol-country-flag"/>';					
							$fields_list .= "<p class='country_name'>".$img."  ".$u_meta."</p>";
						}
					}
				
				}elseif($display_country_flag=='name'){				
					
					$fields_list .= "<p class='country_name'>".$u_meta."</p>";		
						
				
				}
			
			}elseif($field =='description'){
				
				if($u_meta=="")	{
					$u_meta = __("This user hasn't entered a description yet", 'users-control');	
				}else{					
					$u_meta = $this->get_user_desc_exerpt($u_meta,15);					
				}
				
				$fields_list .= "<p class='userscontrol-card-profile-desc'>".$u_meta."</p>";
				
			}elseif($field =='badges'){					

				
			}elseif($field =='social'){ //this rule applies only to social icons				
								
				//get user form		
				$custom_form = get_user_meta($user_id, 'userscontrol_custom_registration_form', true);						
				if($custom_form!=""){
					$custom_form = 'userscontrol_profile_fields_'.$custom_form;	 	
					$array = get_option($custom_form);						
				}else{						
					$array = get_option('userscontrol_profile_fields');
				}						
							
				$html_social ="<div class='userscontrol-prof-social-icon'>";
					
                
				if(is_array($array)){

					foreach($array as $key=>$field){
						$_fsocial = "";
						
						if(isset($field['social']))	{
							$_fsocial = $field['social'];					
						}	
						
						if($_fsocial==1){
												
							//$icon = $field['icon'];
							$icon = $field['meta'];
							$social_meta = get_user_meta($user_id, $field['meta'], true);						
							 if($social_meta!=""){
									$social_meta = apply_filters('userscontrol_social_url_' .$field['meta'], $social_meta);								
									$html_social .="<a href='".$social_meta."' target='_blank'><i class='userscontrol-social-ico fa fa-".$icon." '></i></a>";					
							 }
						}
						
					} //end for each
				
				} //end if	
				
				$html_social .="</div>";
				$fields_list .= $html_social;
			}elseif($field =='rating'){ //this rule applies only to rating
			
			   				
				$fields_list.= "<div class='ratebox'>";
				$fields_list.= $userscontrol->rating->get_rating($user_id,"user_id");
				$fields_list.= "</div>";			
			
			}elseif($field =='like'){ //like rules			   				
				
				$fields_list.= $userscontrol->social->get_item_likes($user_id,"user");	
			
			}elseif($field =='friend'){ //like rules			   				
				
				$fields_list.= $userscontrol->social->get_friends($user_id);
			
			}elseif($field =='follow'){ //add follow button			   				
				
				$fields_list.= $userscontrol->social->get_follow_button($user_id);						
			
			}else{
					
				$fields_list .= "<p class='uu_custom_meta uu_custom_meta_".$field."' id='uu_custom_meta_".$field."' >".$u_meta."</p>";		
			
			}		
			
		
		}		
		return $fields_list;
	
	
	}

    function get_user_desc_exerpt($the_excerpt,$excerpt_length){
		$the_excerpt = strip_tags(strip_shortcodes($the_excerpt)); //Strips tags and images
		$words = explode(' ', $the_excerpt, $excerpt_length + 1);
	
		if(count($words) > $excerpt_length) :
			array_pop($words);
			array_push($words, '… ');
			$the_excerpt = implode(' ', $words);
		endif;	
		$the_excerpt = '' . $the_excerpt . '';	
		return $the_excerpt;
	}
	
	public function subscriptions_by_member(){		
		global $wpdb, $userscontrol;	
		$html = '';		
		$user_id =  sanitize_text_field($_POST['client_id']);	
		$currency_symbol =  $wpticketuserscontrol->get_option('paid_membership_symbol');
		$date_format =  $userscontrol->get_int_date_format();
		$time_format =  $userscontrol->get_time_format();
		$datetime_format =  $userscontrol->get_date_to_display();

		$html .= '<div class="userscontrol-welcome-panel">' ;
		$subscriptions_rows = $this->get_my_subscriptions($user_id);
		
		if (!empty($subscriptions_rows)){		
		
		$html .= ' <table width="100%" class="">
            <thead>
                <tr>
                    <th width="4%" class="bp_table_row_hide" >'.__('#', 'users-control').'</th>
                    <th width="13%">'.__('Date', 'users-control').'</th> ';
                    
                    
                                         
                     $html .= '  <th width="14%" id="wptu-ticket-col-department">'.__('Department', 'users-control').'</th>
                   
                    
                    <th width="14%" id="wptu-ticket-col-staff">'.__('Last Replier', 'users-control').'</th>
                   
                   
                     <th width="18%">'.__('Subject', 'users-control').'</th>
                     <th width="12%"  id="wptu-ticket-col-lastupdate">'.__('Last Update', 'users-control').'</th>
                    <th width="10%">'.__('Priority', 'users-control').'</th>
                    
                     
                     <th width="14%" id="wptu-ticket-col-status">'.__('Status', 'users-control').'</th>
					 <th width="14%" id="wptu-ticket-col-actions">'.__('Actions', 'users-control').'</th>
                    
                </tr>
            </thead>
            
            <tbody>';
            
           
			$filter_name= '';
			$phone= '';
			foreach($subscriptions_rows as $ticket) {

               $html .= ' <tr>
                    <td class="bp_table_row_hide">'.$ticket->ticket_id.'</td>
                     <td>'. $date_submited.' </td>';        
                     
                                         
					  $html .= '  <td id="wptu-ticket-col-department">'.$ticket->department_name.' </td>
                     
                      <td id="wptu-ticket-col-staff">'.$last_replier_label .'</td>
                   
                    <td>'.$wpticketuserscontrol->cut_string($ticket->ticket_subject,20).' </td>
                     <td  id="wptu-ticket-col-lastupdate">'. $nice_time_last_update.'</td>
                    
                    
                    <td>'.  $priority_legend.'</td>                  
                     
                      <td id="wptu-ticket-col-status">'.$status_legend.'</td>
                   <td> <a href="?page=wpticketuserscontrol&tab=ticketedit&see&id='.$ticket->ticket_id.'" class="wptu-appointment-edit-module" appointment-id="'.$ticket->ticket_id.'" title="'.__('Edit','users-control').'"><i class="fa fa-edit"></i></a>
                   
				
                  
                    </td> </tr>';         
                
              
				} //end for each
				
				 $html .= ' </tbody> </table>';
			}else{
			
				$html .= " <p>".__("There are no subscriptions .","users-control")."</p>";
			} 
		
		$html .= '</div>' ;	
		echo wp_kses($html, $userscontrol->allowed_html);
		die();		
	}
	
	public function get_my_subscriptions($user_id){		
		global $wpdb,  $userscontrol;		
		$sql = 'SELECT membership.*, subs.*	 
		FROM ' . $wpdb->prefix . ''.$this->table_prefix . '_subscriptions subs ' ;
				
		$sql .= " RIGHT JOIN ". $wpdb->prefix.$this->table_prefix."_membership_packages membership ON (membership.membership_id = subs.subscription_package_id )";				
		$sql .= " WHERE membership.membership_id = subs.subscription_package_id  ";	
		$sql .= " AND subs.subscription_user_id  ='".$user_id."'  ";
		$sql .= " ORDER BY  subs.subscription_id DESC";
	
		$rows = $wpdb->get_results($sql );	
		return $rows ;		
	}
	
	public function get_my_active_subscriptions($user_id){
		
		global $wpdb,  $userscontrol;
		
		$sql = 'SELECT membership.*, subs.*	 
		FROM ' . $wpdb->prefix . ''.$this->table_prefix . '_subscriptions subs ' ;
				
		$sql .= " RIGHT JOIN ". $wpdb->prefix.$this->table_prefix."_membership_packages membership ON (membership.membership_id = subs.subscription_package_id )";				
		$sql .= " WHERE (membership.membership_id = subs.subscription_package_id AND subs.subscription_status = 1 ";	
		$sql .= " AND subs.subscription_user_id  = '".$user_id."' ) ";	
		
		$sql .= " OR  (membership.membership_id = subs.subscription_package_id AND subs.subscription_status = 2 AND subs.subscription_user_id  = '".$user_id."'  ) ";
		$sql .= " ORDER BY  subs.subscription_id DESC";		
		//$wpdb->prepare($sql, array($user_id, $user_id));
		$rows = $wpdb->get_results($sql );		
		return $rows ;		
	}
	
	/*Get all*/
	public function get_all_filtered ()	{
		global $wpdb,  $userscontrol;
		
			
		$keyword = "";
		$month = "";
		$day = "";
		$year = "";
		$howmany = "";
		$ini = "";
		
		$special_filter='';
		
		if(isset($_GET["bp_keyword"]))
		{
			$keyword = sanitize_text_field($_GET["bp_keyword"]);		
		}
		
		if(isset($_GET["bp_month"]))
		{
			$month = sanitize_text_field($_GET["bp_month"]);		
		}
		
		if(isset($_GET["bp_day"]))
		{
			$day = sanitize_text_field($_GET["bp_day"]);		
		}
		
		if(isset($_GET["bp_year"]))
		{
			$year = sanitize_text_field($_GET["bp_year"]);		
		}
		
		if(isset($_GET["howmany"]))
		{
			$howmany = sanitize_text_field($_GET["howmany"]);		
		}
		
		if(isset($_GET["bp_special_filter"]))
		{
			$special_filter = sanitize_text_field($_GET["bp_special_filter"]);		
		}
		
		if(isset($_GET["bp_status"]))
		{
			$bp_status = sanitize_text_field($_GET["bp_status"]);		
		}
		
		if(isset($_GET["bp_sites"]))
		{
			$bp_sites = sanitize_text_field($_GET["bp_sites"]);		
		}
		
		$uri= sanitize_url($_SERVER['REQUEST_URI']) ;
		$url = explode("&ini=",$uri);
		
		if(is_array($url ))	{
			if(isset($url["1"])){
				$ini = $url["1"];
			    if($ini == ""){$ini=1;}			
			}
		}		
		
		if($howmany == ""){$howmany=50;}
		
		$sql = ' SELECT count(*) as total,  usu.* 
		FROM ' . $wpdb->users . ' usu  ' ;	
		
		if($keyword !=''){				
			$sql .= " AND (usu.display_name LIKE '%".$keyword."%')";
		}
		
		if($day!=""){$sql .= " AND DAY(usu.user_registered ) = '$day'  ";	}
		if($month!=""){	$sql .= " AND MONTH(usu.user_registered ) = '$month'  ";	}		
		if($year!=""){$sql .= " AND YEAR(usu.user_registered ) = '$year'";}	
		
		$orders = $wpdb->get_results($sql );
		$orders_total = $this->fetch_result($orders);
		$orders_total = $orders_total->total;
		$this->total_result = $orders_total ;
		
		$total_pages = $orders_total;
				
		$limit = "";
		$current_page = $ini;
		$target_page =  site_url()."/wp-admin/admin.php?page=bookinguserscontrol&tab=appointments";
		
		$how_many_per_page =  $howmany;
		
		$to = $how_many_per_page;
		
		//caluculate from
		$from = $this->calculate_from($ini,$how_many_per_page,$orders_total );
		
		//get all			
		$sql = ' SELECT  usu.* 
		FROM ' .$wpdb->users . ' usu  ' ;	
			
	//	$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prefix ."_membership_packages package ON (package.membership_id = sub.subscription_package_id )";		
		//$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = sub.subscription_user_id)";

	//	$sql .= " WHERE package.membership_id = sub.subscription_package_id  ";	
		//$sql .= " AND usu.ID = sub.subscription_user_id ";			
		
		
		if($keyword !=''){				
			$sql .= " AND (usu.display_name LIKE '%".$keyword."%' )";
		}
		
		if($day!=""){$sql .= " AND DAY(usu.user_registered ) = '$day'  ";	}
		if($month!=""){	$sql .= " AND MONTH(usu.user_registered ) = '$month'  ";	}		
		if($year!=""){$sql .= " AND YEAR(usu.user_registered ) = '$year'";}	
		
		$sql .= " ORDER BY usu.ID DESC";		
		
	    if($from != "" && $to != ""){	$sql .= " LIMIT $from,$to"; }
	 	if($from == 0 && $to != ""){	$sql .= " LIMIT $from,$to"; }	
					
		$rows = $wpdb->get_results($sql );	
		return $rows ;
		
	
	}
	
	public function calculate_from($ini, $howManyPagesPerSearch, $total_items){
		if($ini == ""){$initRow = 0;}else{$initRow = $ini;}
		
		if($initRow<= 1){
			$initRow =0;
		}else{

			if(($howManyPagesPerSearch * $ini)-$howManyPagesPerSearch>= $total_items) {
				$initRow = $totalPages-$howManyPagesPerSearch;
			}else{
				$initRow = ($howManyPagesPerSearch * $ini)-$howManyPagesPerSearch;
			}
		}
		return $initRow;
	}

	/* Setup search form */
    public function _search_form($args=array())	{
		global $userscontrol, $predefined;		

        // Determine search form is loaded
        $this->userscontrol_search = true;
        /* Default Arguments */
        $defaults = array(
            'fields' => null,
            'filters' => null,
			'filter_labels' => null, //separated by commas age:From
            'exclude_fields' => null,
            'operator' => 'AND',
			'width' => 'AND',
			'custom_form' => '',
			'target_page_url' => '',
            'use_in_sidebar' => null,
            'users_are_called' => __('Users', 'users-control'),
            'combined_search_text' =>  __('type user name here', 'users-control'),
            'button_text' =>  __('Search', 'users-control'),
            'reset_button_text' =>__('Reset', 'users-control')
        );
		
		
        $this->search_args = wp_parse_args($args, $defaults);
        $this->search_operator = $this->search_args['operator'];

		$filter_lab = array();
		
		if($this->search_args['filter_labels']!=null){			
			$filter_lab = explode(',',$this->search_args['filter_labels']);			
		}		


        if (strtolower($this->search_args['operator']) != 'and' && strtolower($this->search_args['operator']) != 'or') {
            $this->search_args['operator'] = 'AND';
        }

        // Prepare array of all fields to load
        $this->build_search_field_array($this->search_args['custom_form']);
		
		$action_url = '';
		
		if( $this->search_args['target_page_url']!=''){			
			$action_url = "action='".$this->search_args['target_page_url']."'";			
		}
		
		

        $sidebar_class = null;
        if ($this->search_args['use_in_sidebar'])
            $sidebar_class = 'userscontrol-sidebar';

        $display = null;
        $display.='<div class="userscontrol-wrap userscontrol-wrap-form userscontrol-search-wrap' . $sidebar_class . '">';
        $display.='<div class="userscontrol-inner userscontrol-clearfix">';
        $display.='<div class="userscontrol-head">' . sprintf(__('Search %s', 'users-control'), $this->search_args['users_are_called']) . '</div>';
		
        $display.='<form method="get" id="userscontrol_search_form" name="userscontrol_search_form" class="userscontrol-search-form userscontrol-clearfix" '.$action_url.'>';

        // Check For default fields Start
        if ($this->show_combined_search_field === true) {            
		
			$display.='<p class="userscontrol-p userscontrol-search-p">';
            $display.= $userscontrol->commmonmethods->text_box(array(
                        'class' => 'userscontrol-search-input userscontrol-combined-search',
                        'value' => isset($_GET['userscontrol_combined_search']) ? sanitize_text_field($_GET['userscontrol_combined_search'] ) : '',
                        'name' => 'userscontrol_combined_search',
                        'placeholder' => $this->search_args['combined_search_text']
                    ));

            if (count($this->combined_search_field) > 0) {
                $display.='<input type="hidden" name="userscontrol_combined_search_fields" value="' . implode(',', $this->combined_search_field) . '" />';
            } else {
                $display.='<input type="hidden" name="userscontrol_combined_search_fields" value="' . implode(',', $this->all_text_search_field) . '" />';
            }


            $display.='</p>';
        }

        // Check For default fields End
        // Custom Search Fields Creation Starts
        if ($this->show_nontext_search_fields === true) {	
			
			
            $counter = 0;
            $display.='<p class="userscontrol-p userscontrol-search-p">';
            foreach ($this->nontext_search_fields as $key => $value){				
				
                $method_name = '';
                $method_name = $this->method_dect[$value['field']];
                if ($method_name != '') {					
					
                    if ($counter > 0 && $counter % 2 == 0) {
                        $display.='</p>';
                        $display.='<p class="userscontrol-p userscontrol-search-p">';
                    }

                    $counter++;

                    $class = 'userscontrol-search-input userscontrol-search-input-left userscontrol-search-meta-' . $value['meta'];
                    if ($counter > 0 && $counter % 2 == 0)
                        $class = 'userscontrol-search-input userscontrol-search-input-right userscontrol-search-meta-' . $value['meta'];

                    if ($method_name == 'drop_down'){
                        $loop = array();				
                        if (isset($value['predefined_options']) && $value['predefined_options'] != '' && $value['predefined_options'] != '0') {
							$defined_loop = $userscontrol->commmonmethods->get_predifined( $value['predefined_options'] );
                            foreach ($defined_loop as $option) {
                                if ($option == '' || $option == null) {
                                    $loop[$option] = $value['name'];
                                } else {
                                    $loop[$option] = $option;
                                }
                            }
                        } else if (isset($value['choices']) && $value['choices'] != '') {
							
							$loop_default = $userscontrol->userscontrol_one_line_checkbox_on_window_fix($value['choices'] );
                            $loop[''] = $value['name'];

                            foreach ($loop_default as $option)
                                $loop[$option] = $option;
                        }

                        if (isset($_POST['userscontrol_search'][$value['meta']]))
                            $_POST['userscontrol_search'][$value['meta']] = stripslashes_deep(sanitize_text_field($_GET['userscontrol_search'][$value['meta']]));

                        $default = isset($_GET['userscontrol_search'][$value['meta']]) ? sanitize_text_field($_GET['userscontrol_search'][$value['meta']]) : '0';
                        $name = 'userscontrol_search[' . $value['meta'] . ']';

                        if ($value['field'] == 'checkbox') {
                            $default = isset($_GET['userscontrol_search'][$value['meta']]) ? sanitize_text_field($_GET['userscontrol_search'][$value['meta']]) : array();
                            $name = 'userscontrol_search[' . $value['meta'] . '][]';
                        }

                        if (count($loop) > 0) {
                            $display.= $userscontrol->commmonmethods->drop_down(array(
                                        'class' => $class,
                                        'name' => $name,
                                        'placeholder' => $value['name']
                                            ), $loop, $default);
                        }
						
                    } else if ($method_name == 'text_box') {
                        if (isset($_GET['userscontrol_search'][$value['meta']]))
                            $_GET['userscontrol_search'][$value['meta']] = stripslashes_deep(sanitize_text_field($_GET['userscontrol_search'][$value['meta']]));


                        $default = isset($_GET['userscontrol_search'][$value['meta']]) ? sanitize_text_field($_GET['userscontrol_search'][$value['meta']]) : '';
                        $name = 'userscontrol_search[' . $value['meta'] . ']';

                        $display.= $userscontrol->commmonmethods->text_box(array(
                                    'class' => $class,
                                    'name' => $name,
                                    'placeholder' => $value['name'],
                                    'value' => $default
                                ));
                    }
                }
            }
            $display.='</p>';


            if (isset($this->checkbox_search_fields) && count($this->checkbox_search_fields) > 0) {
				
				
                foreach ($this->checkbox_search_fields as $key => $value) 
				{					
                    $display.='<p class="userscontrol-p userscontrol-search-p userscontrol-multiselect-p">';

                    $method_name = '';
                    $method_name = $this->method_dect[$value['field']];
                    if ($method_name != '') {
                        $class = 'userscontrol-search-input userscontrol-search-multiselect userscontrol-search-meta-' . $value['meta'];

                        $loop = array();

                        if (isset($value['predefined_loop']) && $value['predefined_loop'] != '' && $value['predefined_loop'] != '0') {
                            //$defined_loop = $predefined->get_array($value['predefined_loop']);
							$defined_loop = $userscontrol->commmonmethods->get_predifined( $value['predefined_options'] );
							

                            foreach ($defined_loop as $option)
                                $loop[$option] = $option;
                        } else if (isset($value['choices']) && $value['choices'] != '') {
							
                            //$loop_default = explode(PHP_EOL, $value['choices']);
							
							$loop_default = $userscontrol->userscontrol_one_line_checkbox_on_window_fix($value['choices']);
							
                            $loop[''] = $value['name'];

                            foreach ($loop_default as $option)
                                $loop[$option] = $option;
                        }

                        if (isset($_GET['userscontrol_search'][$value['meta']]))
                            $_GET['userscontrol_search'][$value['meta']] = stripslashes_deep(sanitize_text_field($_GET['userscontrol_search'][$value['meta']]));

                        $default = isset($_GET['userscontrol_search'][$value['meta']]) ? sanitize_text_field($_GET['userscontrol_search'][$value['meta']]) : '0';
                        $name = 'userscontrol_search[' . $value['meta'] . ']';
                        if ($value['field'] == 'checkbox') 
						{
                            $default = isset($_GET['userscontrol_search'][$value['meta']]) ? sanitize_text_field($_GET['userscontrol_search'][$value['meta']]) : array();
                            $name = 'userscontrol_search[' . $value['meta'] . '][]';
                        }

                        if (count($loop) > 0) 
						{
                            $display.= $userscontrol->commmonmethods->drop_down(array(
                                        'class' => $class,
                                        'name' => $name,
                                        'placeholder' => $value['name']
                                            ), $loop, $default);
                        }
						
						//between 
						
						
                    }

                    $display.='</p>';
                }
            }
        }

        $display.='<input type="hidden" name="userspage" id="userspage" value="" />';
        $display.='<input type="hidden" name="userscontrol-search-fired" id="userscontrol-search-fired" value="1" />';

        // Custom Search Fields Creation Ends
        // Submit Button
        $display.='<div class="userscontrol-searchbtn-div">';
        $display.=$userscontrol->commmonmethods->button('submit', array(
                    'class' => 'userscontrol-button-alt userscontrol-button userscontrol-search-submit',
                    'name' => 'userscontrol-search',
                    'value' => $this->search_args['button_text']
                ));
        $display.='&nbsp;';
        $display.=$userscontrol->commmonmethods->button('button', array(
                    'class' => 'userscontrol-button-alt userscontrol-button userscontrol-search-reset',
                    'name' => 'userscontrol-search-reset',
                    'value' => $this->search_args['reset_button_text'],
                    'id' => 'userscontrol-reset-search'
                ));

        $display.='</div>';
        $display.='</form>';

        $display.='</div>';
        $display.='</div>';
        /* Extra Clearfix for Avada Theme */
        $display.='<div class="userscontrol-clearfix"></div>';

        return $display;
    }

	private function build_search_field_array($custom_form) {
		
		if($custom_form!=""){
			$custom_form = 'userscontrol_profile_fields_'.$custom_form;		
			$custom_fields = get_option($custom_form);				
		}else{	
			$custom_fields = get_option('userscontrol_profile_fields');			
		}
	
        $this->search_banned_field_type = array('fileupload', 'password', 'datetime');
        $this->show_combined_search_field = false;
        $this->show_nontext_search_fields = false;
        $this->all_text_search_field = array();
        $this->combined_search_field = array();
        $this->nontext_search_fields = array();
        $this->checkbox_search_fields = array();

        $included_fields = '';
        if ($this->search_args['fields'] != '')
            $included_fields = explode(',', $this->search_args['fields']);

        $excluded_fields = explode(',', $this->search_args['exclude_fields']);

        $search_filters = array();
        $search_filters = explode(',', $this->search_args['filters']);

        foreach ($custom_fields as $key => $value){
            if (isset($value['type']) && $value['type'] == 'usermeta') {
                if (isset($value['field']) && !in_array($value['field'], $this->search_banned_field_type)) {
                    if (isset($value['meta']) && !in_array($value['meta'], $excluded_fields)) {
                        switch ($value['field']) {
                            
                            case 'textarea':
                            case 'text':							

                                if (is_array($search_filters) && in_array($value['meta'], $search_filters)) {
                                    if ($this->show_nontext_search_fields === false) {
                                        $this->show_nontext_search_fields = true;
                                    }

                                    $this->nontext_search_fields[] = $value;


									
                                }else{
                                    if ($this->show_combined_search_field === false)
                                        $this->show_combined_search_field = true;

                                    $this->all_text_search_field[] = $value['meta'];

                                    if (is_array($included_fields) && count($included_fields) > 0 && in_array($value['meta'], $included_fields))
                                        $this->combined_search_field[] = $value['meta'];
                                }
                                break;

                            case 'select':
                            case 'radio':

                                $is_in_field = false;
                                $is_in_filter = false;

                                if (is_array($search_filters) && in_array($value['meta'], $search_filters))
                                    $is_in_filter = true;

                                if (is_array($included_fields) && count($included_fields) > 0 && in_array($value['meta'], $included_fields))
                                    $is_in_field = true;

                                if ($is_in_field == true || $is_in_filter == true) {
                                    if ($this->show_nontext_search_fields === false) {
                                        $this->show_nontext_search_fields = true;
                                    }

                                    $this->nontext_search_fields[] = $value;
                                }
                                break;

                            case 'checkbox':

                                $is_in_field = false;
                                $is_in_filter = false;

                                if (is_array($search_filters) && in_array($value['meta'], $search_filters))
                                    $is_in_filter = true;

                                if (is_array($included_fields) && count($included_fields) > 0 && in_array($value['meta'], $included_fields))
                                    $is_in_field = true;

                                if ($is_in_filter == true || $is_in_field == true) {
                                    if ($this->show_nontext_search_fields === false) {
                                        $this->show_nontext_search_fields = true;
                                    }

                                    $this->checkbox_search_fields[] = $value;
                                }
                                break;

                            default:
                                break;
                        }
                    }
                }
            }
        }
    }
	
	
	public function fetch_result($results)
	{
		if ( empty( $results ) )
		{
		
		
		}else{
			
			
			foreach ( $results as $result )
			{
				return $result;			
			
			}
			
		}
		
	}
	
	
	
}
$key = "user";
$this->{$key} = new UserscontrolUser();
?>