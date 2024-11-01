<?php
class UserscontrolPostProtection{
	var $table_prefix = 'userscontrol';
	var $ajax_p = 'userscontrol';
	
	var $_aPostableTypes = array(
        'post',
        'page',
		'product',
        'attachment',
    );
	
	function __construct(){
		add_action( 'save_post',  array( &$this, 'userscontrol_save_post_feature' ), 89);
		$this->post_protection_logged_in();
	}
	
	// post protection by logged in users	
	function post_protection_logged_in(){
		global  $userscontrol;		
		if($this->get_option('activate_post_protection_modules')=='yes'){			
			
			$protection_method = $this->get_option('post_protection_method');	
					
			if($protection_method=='loggedin' || $protection_method==''){
				add_filter('the_posts', array(&$this, 'userscontrol_showPost'), 89);	
				add_filter('get_pages', array(&$this, 'userscontrol_showPage'),77);					
				add_action('add_meta_boxes', array(&$this, 'userscontrol_post_protection_add_meta_box' ));				
				add_action('save_post',  array( &$this, 'userscontrol_save_post_logged_in_protect' ), 93);
			}
			
			if($protection_method=='membership' ){
				add_action('save_post',  array( &$this, 'userscontrol_save_post_memmbership_protect' ), 94);							
				add_action('add_meta_boxes', array(&$this, 'userscontrol_pp_membership_add_meta_box' ));	
				add_filter('the_posts', array(&$this, 'userscontrol_showPostMembership'), 89);	
				add_filter('get_pages', array(&$this, 'userscontrol_showPageMembership'),77);	
			}
			
			if($protection_method=='role' ){
				add_action('save_post',  array( &$this, 'userscontrol_save_post_role_protect' ), 94);							
				add_action('add_meta_boxes', array(&$this, 'userscontrol_pp_role_add_meta_box' ));	
				add_filter('the_posts', array(&$this, 'userscontrol_showPostRole'), 89);	
				//add_filter('get_pages', array(&$this, 'userscontrol_showPageMembership'),77);	
			}
		
		}
	}
	
	
	 /**
     * The function for the get_pages filter.
     * 
     * @param array $aPages The pages.
     * 
     * @return array
     */
    public function userscontrol_showPage($aPages = array()){
		global $userscontrol;
        $aShowPages = array(); 			    
        foreach ($aPages as $oPage){
            if ($this->get_option('userscontrol_loggedin_hide_complete_page') == 'yes'   ){		          
				if ($this->checkAccessToPost($oPage->ID)){					
					// $oPage->post_title =$userscontrol->get_option('userscontrol_loggedin_page_title');
					// $oPage->post_content = $userscontrol->get_option('userscontrol_loggedin_page_content') ;
					
					$aShowPages[] = $oPage;
				}
				
            }else{

                if (!$this->checkAccessToPost($oPage->ID)){
					if ($this->get_option('userscontrol_loggedin_hide_page_title') == 'yes'){
						$oPage->post_title =$this->get_option('userscontrol_loggedin_page_title');
					}
                    $oPage->post_content = $this->get_option('userscontrol_loggedin_page_content') ;					
                }
                $aShowPages[] = $oPage;
            }		
        }
        $aPages = $aShowPages;
        return $aPages;
    }
	
	public function userscontrol_showPageMembership($aPages = array()) {
		global $userscontrol;
        $aShowPages = array();   
        foreach ($aPages as $oPage){
            if ($userscontrol->get_option('userscontrol_loggedin_hide_complete_page') == 'yes'   ){
               // $oPage->post_title .= $this->adminOutput( $oPage->post_type, $oPage->ID );
			    if ($this->checkAccessToPostMembership($oPage->ID)){
               	 	$aShowPages[] = $oPage;  
				}            
				
            } else {
				
                if (!$this->checkAccessToPostMembership($oPage->ID)){
					
					if ($userscontrol->get_option('userscontrol_loggedin_hide_page_title') == 'yes') {
						$oPage->post_title =$userscontrol->get_option('userscontrol_loggedin_page_title');
					}

                    $oPage->post_content = $userscontrol->get_option('userscontrol_loggedin_page_content');;
                }
               // $oPage->post_title .= $this->adminOutput($oPage->post_type, $oPage->ID);
                $aShowPages[] = $oPage;
            }
        }
        $aPages = $aShowPages;
        return $aPages;
    }
	
	 /**
     * The function for the the_posts filter.
     * 
     * @param array $aPosts The posts.
     * 
     * @return array
     */
    public function userscontrol_showPost($aPosts = array()){
		global $userscontrol;
        $aShowPosts = array();	       
        if (!is_feed() || ($this->get_option('userscontrol_loggedin_protect_feed') == 'yes'  && is_feed())){
            foreach ($aPosts as $iPostId){
                if ($iPostId !== null){
                    $oPost = $this->_getPost($iPostId);
                    if ($oPost !== null){
                        $aShowPosts[] = $oPost;
                    }
                }
            }
            $aPosts = $aShowPosts;
        }
        return $aPosts;
    }
	
	public function userscontrol_showPostMembership($aPosts = array()){
		global $userscontrol;
        $aShowPosts = array();
        if (!is_feed() || ($this->get_option('userscontrol_loggedin_protect_feed') == 'yes'  && is_feed())){

            foreach ($aPosts as $iPostId){
                if ($iPostId !== null){
                    $oPost = $this->_getPostMembership($iPostId);
                    if ($oPost !== null){
                        $aShowPosts[] = $oPost;
                    }
                }
            }
            $aPosts = $aShowPosts;
        }        
        return $aPosts;
    }
	
	public function userscontrol_showPostRole($aPosts = array()){
		global $userscontrol;
        $aShowPosts = array();       
        if (!is_feed() || ($this->get_option('userscontrol_loggedin_protect_feed') == 'yes'  && is_feed())) {
            foreach ($aPosts as $iPostId){
                if ($iPostId !== null){
                    $oPost = $this->_getPostRole($iPostId);
                    if ($oPost !== null){
                        $aShowPosts[] = $oPost;
                    }
                }
            }
            $aPosts = $aShowPosts;
        }        
        return $aPosts;
    }
	
	
	 /**
     * Modifies the content of the post by the given settings.
     * 
     * @param object $oPost The current post.
     * 
     * @return object|null
     */
    protected function _getPost($oPost){
		global $userscontrol;       
        $sPostType = $oPost->post_type;
		if ($sPostType != 'post' && $sPostType != 'page' ){
			$sPostType = 'post';			
        }elseif ($sPostType != 'post' && $sPostType != 'page'){
            return $oPost;
        }
        
        if ($this->get_option('userscontrol_loggedin_hide_complete_'.$sPostType.'') == 'yes' ){      
			if ($this->checkAccessToPost($oPost->ID)){
				 return $oPost;
				 
					// $oPost->post_title =$userscontrol->get_option('userscontrol_loggedin_'.$sPostType.'_title');
					// $oPost->post_content =  $userscontrol->get_option('userscontrol_loggedin_'.$sPostType.'_content');
			}
        }else{
			
            if (!$this->checkAccessToPost($oPost->ID)){
                $oPost->isLocked = true;
                $uultraPostContent = $this->get_option('userscontrol_loggedin_'.$sPostType.'_content');
                
                if ($this->get_option('userscontrol_loggedin_hide_'.$sPostType.'_title') == 'yes'){
                    $oPost->post_title =$this->get_option('userscontrol_loggedin_'.$sPostType.'_title');
                }
                
                if ($this->get_option('userscontrol_loggedin_allow_'.$sPostType.'_comments') == 'no'){
                    $oPost->comment_status = 'close';
                }

                if ($userscontrol->get_option('userscontrol_loggedin_post_content_before_more') == 'yes'
                    && $sPostType == "post" && preg_match('/<!--more(.*?)?-->/', $oPost->post_content, $aMatches)) 	{
                    $oPost->post_content = explode($aMatches[0], $oPost->post_content, 2);
                    $uultraPostContent = $oPost->post_content[0] . " " . $uultraPostContent;
                }

                $oPost->post_content = stripslashes($uultraPostContent);
            }
            return $oPost;
        }
        return null;
    }
	
	protected function _getPostMembership($oPost){
		global $userscontrol;	
        $sPostType = $oPost->post_type;

		if ($sPostType != 'post' && $sPostType != 'page' ){			
			   $sPostType = 'post';			
        }elseif ($sPostType != 'post' && $sPostType != 'page'){
            return $oPost;
        }
        
        if ($this->get_option('userscontrol_loggedin_hide_complete_'.$sPostType.'') == 'yes' ){         
			
			if ($this->checkAccessToPostMembership($oPost->ID)){
				 return $oPost;
				 
					// $oPost->post_title =$userscontrol->get_option('userscontrol_loggedin_'.$sPostType.'_title');
					// $oPost->post_content =  $userscontrol->get_option('userscontrol_loggedin_'.$sPostType.'_content');
			}
        } else {
			
            if (!$this->checkAccessToPostMembership($oPost->ID)) {
                $oPost->isLocked = true;
                
                $uultraPostContent = '<div class="userscontrol-ultra-info">'.$this->get_option('userscontrol_loggedin_'.$sPostType.'_content').'</div>';
                
                if ($this->get_option('userscontrol_loggedin_hide_'.$sPostType.'_title') == 'yes'){
                    $oPost->post_title =$this->get_option('userscontrol_loggedin_'.$sPostType.'_title');
                }
                
                if ($this->get_option('userscontrol_loggedin_allow_'.$sPostType.'_comments') == 'no'){
                    $oPost->comment_status = 'close';
                }

                if ($userscontrol->get_option('userscontrol_loggedin_post_content_before_more') == 'yes'
                    && $sPostType == "post" && preg_match('/<!--more(.*?)?-->/', $oPost->post_content, $aMatches)) {
                    $oPost->post_content = explode($aMatches[0], $oPost->post_content, 2);
                    $uultraPostContent = $oPost->post_content[0] . " " . $uultraPostContent;
                }

                $oPost->post_content = stripslashes($uultraPostContent);
            }
            return $oPost;
        }
        return null;
    }
	
	protected function _getPostRole($oPost){
		global $userscontrol;
        $sPostType = $oPost->post_type;
		if ($sPostType != 'post' && $sPostType != 'page' ){			
			   $sPostType = 'post';			
        }elseif ($sPostType != 'post' && $sPostType != 'page'){
            return $oPost;
        }
        
        if ($this->get_option('userscontrol_loggedin_hide_complete_'.$sPostType.'') == 'yes' ) {         
			
			if ($this->checkAccessToPostRole($oPost->ID)){
				 return $oPost;
				 
					// $oPost->post_title =$userscontrol->get_option('userscontrol_loggedin_'.$sPostType.'_title');
					// $oPost->post_content =  $userscontrol->get_option('userscontrol_loggedin_'.$sPostType.'_content');
			}
        } else {
			
            if (!$this->checkAccessToPostRole($oPost->ID)) {
                $oPost->isLocked = true;                
                $uultraPostContent = $this->get_option('userscontrol_loggedin_'.$sPostType.'_content');
                
                if ($this->get_option('userscontrol_loggedin_hide_'.$sPostType.'_title') == 'yes'){
                    $oPost->post_title =$this->get_option('userscontrol_loggedin_'.$sPostType.'_title');
                }
                
                if ($this->get_option('userscontrol_loggedin_allow_'.$sPostType.'_comments') == 'no'){
                    $oPost->comment_status = 'close';
                }

                if ($userscontrol->get_option('userscontrol_loggedin_post_content_before_more') == 'yes'
                    && $sPostType == "post" && preg_match('/<!--more(.*?)?-->/', $oPost->post_content, $aMatches)) {
                    $oPost->post_content = explode($aMatches[0], $oPost->post_content, 2);
                    $uultraPostContent = $oPost->post_content[0] . " " . $uultraPostContent;
                }
                $oPost->post_content = stripslashes($uultraPostContent);
            }            
            return $oPost;
        }	
        return null;
    }
	
	public function checkAccessToPostMembership($post_id) {
		global $userscontrol;		
		
		$res = true;		
		$post_groups = $userscontrol->membership->get_all_post_memberships($post_id);
		
		//print_r($post_groups);
		
		//if this post in a group?		
		if (count($post_groups) == 0 ) {
			$res = true;
			
		}else{
			
			//this post has some membership rules
			$current_user = wp_get_current_user();
			$user_id = $current_user->ID;
			
			//is this the super admin?
			if(is_super_admin( $user_id )){
				$res = true;		
				
			}elseif(!is_user_logged_in()){
				
				$res = false;				
				
			}else{
				
				//is this user allowed to see this post.				
				$user_memberships =$userscontrol->membership->get_all_user_active_memberships($user_id);
				
				foreach ($user_memberships as $membership)	{					
					if(in_array($membership, $post_groups))
					{
						return true; //user belongs to this group					
					}				
				
				}
				
				$res = false;		
			
			}		
		
		}
		
		return $res;
		
	
	}
	
	public function checkAccessToPostRole($post_id) {
		global $userscontrol;		
		
		$res = true;		
		$post_roles = $userscontrol->membership->get_all_post_roles($post_id);		
		
		//if this post in a group?		
		if (count($post_roles) == 0 ) {
			$res = true;
			
		}else{
			
			$current_user = wp_get_current_user();
			$user_id = $current_user->ID;
			
			//is this the super admin?
			if(is_super_admin( $user_id )){
				$res = true;		
				
			}elseif(!is_user_logged_in()){
				
				$res = false;				
				
			}else{
				
				//is this user allowed to see this post.				
				//$user_memberships =$userscontrol->membership->get_all_user_active_memberships($user_id);
				
				foreach ($post_roles as $role)
			 	{					
					if($userscontrol->membership->is_user_in_role($user_id, $role))
					{
						return true; 			
					}				
				
				}
				
				$res = false;		
			}		
		}
		return $res;
	}
	
	public function checkAccessToPost($post_id){
		global $userscontrol;	
	
		$res = true;		
		$userscontrol_protect_logged_in = get_post_meta( $post_id, 'userscontrol_protect_logged_in' , true);
		
		if ($userscontrol_protect_logged_in == 'yes' ){
			if(!is_user_logged_in()){
				$res = false;				
			}
			
		}else{		
			$res = true;		
		}
		
		return $res;
		
	
	}
	
	function userscontrol_save_post_logged_in_protect( $post_id ) {	
		// If this is just a revision, don't send the email.
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave($post_id) )
			return;
			
				 
		 $post = get_post($post_id);
        if($post->post_status == 'trash' ){
            return $post_id;
        }
		
		$aFormData = array();      
        $is_protected = "";
		
		if (isset($_GET['userscontrol_update_logged_in_access']) || isset($_POST['userscontrol_update_logged_in_access'])){
            
            if(isset($_GET['userscontrol_protect_logged_in']) ){
                $is_protected = sanitize_text_field($_GET['userscontrol_protect_logged_in']);       
            }
            
            if(isset($_POST['userscontrol_protect_logged_in']) ){
                $is_protected = sanitize_text_field($_POST['userscontrol_protect_logged_in']);       
            }
			
			update_post_meta($post_id, 'userscontrol_protect_logged_in', $is_protected);		 
						
		}
        
	}
	
	function userscontrol_save_post_role_protect( $post_id ) {
		global $userscontrol;
	
		// If this is just a revision, don't send the email.
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave($post_id) )
			return;
			
				 
		 $post = get_post($post_id);
        if($post->post_status == 'trash' ){
                return $post_id;
        }
		
		$aFormData = array();	
		
		if (isset($_POST['userscontrol_update_groups']) || isset($_GET['userscontrol_update_groups'])){
			$selected_groups =array();
            
            if (isset($_POST['userscontrol_group_list']) ){
                $selected_groups = $_POST['userscontrol_group_list'];            
            }            
            
            if (isset($_GET['userscontrol_group_list']) ){
                $selected_groups = $_GET['userscontrol_group_list'];            
            }
            
			//loop through selected groups
			$userscontrol->membership->post_role_del($post_id);
			 
			foreach ($selected_groups as $membership_id) {
				$userscontrol->membership->save_post_role_rel($post_id, $membership_id);			 
			}	
		}			
	}
	
	
	function userscontrol_save_post_memmbership_protect( $post_id ){
		global $userscontrol;
	
		// If this is just a revision, don't send the email.
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave($post_id) )
			return;
				 
		 $post = get_post($post_id);
        if($post->post_status == 'trash' ){
                return $post_id;
        }
		
		$aFormData = array();
		
		if (isset($_POST['userscontrol_update_groups']) || isset($_GET['userscontrol_update_groups'])){
			$selected_groups = array();       
            
            
            if (isset($_POST['userscontrol_group_list'])) {
                $selected_groups = $_POST['userscontrol_group_list'];
            } elseif (isset($_GET['userscontrol_group_list'])) {
                $selected_groups = $_GET['userscontrol_group_list'];
            }            
			 
			//loop through selected groups
			$userscontrol->membership->post_membership_del($post_id);
			foreach ($selected_groups as $membership_id){
				$userscontrol->membership->save_post_membership_rel($post_id, $membership_id);			 
			}	
		}			
	}
	
	
	function userscontrol_pp_role_add_meta_box(){
		$this->_aPostableTypes = array_merge($this->_aPostableTypes, get_post_types(array('publicly_queryable' => true), 'names'));
        $this->_aPostableTypes = array_unique($this->_aPostableTypes);
		$aPostableTypes = $this->getPostableTypes();
        foreach ($aPostableTypes as $sPostableType){
			add_meta_box('userscontrol_post_access_byrole', 'UsersControl Restriction by Role', array(&$this, 'editPostContentRole'), $sPostableType, 'normal');
        }
	}
	
	
	

	
	function userscontrol_pp_membership_add_meta_box(){
		$this->_aPostableTypes = array_merge($this->_aPostableTypes, get_post_types(array('publicly_queryable' => true), 'names'));
        $this->_aPostableTypes = array_unique($this->_aPostableTypes);
	
		$aPostableTypes = $this->getPostableTypes();
                
        foreach ($aPostableTypes as $sPostableType) {
			add_meta_box('userscontrol_post_access_bymembership', 'UsersControl Membership Protection', array(&$this, 'editPostContentMembership'), $sPostableType, 'normal');
        }
	}
	
	public function editPostContentMembership($oPost)   {
		global $userscontrol;
		
        $iObjectId = $oPost->ID;
	   
		if (isset($_GET['attachment_id'])) {
				$iObjectId = $sanitize_text_field(_GET['attachment_id']);
		} elseif (!isset($iObjectId)) {
				$iObjectId = 0;
		}
			
		$oPost = get_post($iObjectId);
		$sObjectType = $oPost->post_type;
		
		$sAddition = '';
		//get all groups		
		$aUUltraUserGroups = $userscontrol->membership->get_all();
		
		$groups_list = array();		
		$groups_list = $userscontrol->membership->get_all_post_memberships($iObjectId);
		
		$html = '';		
		$html .= '<div class="userscontrolembers-protect-group-options">	';
		$html .= '<ul>	';
				
		if (count($aUUltraUserGroups) > 0){
			$html .= '<input type="hidden" name="userscontrol_update_groups" value="true" />	';	
		
			foreach ($aUUltraUserGroups as $oUUltraUserGroup){
				 $checked = '';
				 $sAttributes = '';	
				 
				if (in_array($oUUltraUserGroup->membership_id, $groups_list)){
					$checked = 'checked="checked"';
				}
				 
				$html .= ' <li>';				
				$html .= '<input type="checkbox" id="'.$oUUltraUserGroup->membership_id.'-'.$oUUltraUserGroup->membership_id.'" value="'.$oUUltraUserGroup->membership_id.'" name="userscontrol_group_list[]" '.$checked.' /> ';
                 
       			$html .= '  <label for="'.$oUUltraUserGroup->membership_name.'-'.$oUUltraUserGroup->membership_id.'" class="selectit" style="display:inline;" >
            '.$oUUltraUserGroup->membership_name.$sAddition.'
        </label>';
				
				$html .= ' </li>';
			
			}
		
		} else {
			
				$html .= "<a href='admin.php?page=membership'>".__('Please create a subscription first.','users-control')."</a>";
		
		} //end if

		$html .= '</ul>	';
		
		$html .= ' </div>';
		echo wp_kses($html, $userscontrol->allowed_html);

    }
	
	public function editPostContentRole($oPost) {
		global $userscontrol;
		
        $iObjectId = $oPost->ID;
	   
		if (isset($_GET['attachment_id'])) {
				$iObjectId = sanitize_text_field($_GET['attachment_id']);
		} elseif (!isset($iObjectId)) {
				$iObjectId = 0;
		}
			
		$oPost = get_post($iObjectId);
		$sObjectType = $oPost->post_type;
		
		$user_id = get_current_user_id();
		
		//get all groups		
		$allowed_user_roles = $userscontrol->role->get_available_roles();
		
		$roles_list = array();		
		$roles_list = $userscontrol->membership->get_all_post_roles($iObjectId);
		
				
		$html = '';		
		$html .= '<div class="userscontrolembers-protect-group-options">	';
		$html .= '<ul>	';
				
		if (count($allowed_user_roles) > 0){
			$html .= '<input type="hidden" name="userscontrol_update_groups" value="true" />	';	
		
			foreach ($allowed_user_roles as $key => $val){
				 $checked = '';
				 $sAttributes = '';					 
				if (in_array($key, $roles_list)){
					$checked = 'checked="checked"';
				}
				 
				$html .= ' <li>';				
				$html .= '<input type="checkbox" id="'.$key.'-'.$key.'" value="'.$key.'" name="userscontrol_group_list[]" '.$checked.' /> ';
                 
       $html .= '  <label for="'.$val.'-'.$key.'" class="selectit" style="display:inline;" >
            '.$val.$sAddition.'
        </label>';
				
				$html .= ' </li>';
			
			}
		
		} else {
			
				$html .= "<a href='admin.php?page=membership'>".__('Please create a subscription first.','users-control')."</a>";
		
		} //end if

		$html .= '</ul>	';		
		$html .= ' </div>';		
		
		echo wp_kses($html, $userscontrol->allowed_html);

    }
	
	function userscontrol_post_protection_add_meta_box() {
		$this->_aPostableTypes = array_merge($this->_aPostableTypes, get_post_types(array('publicly_queryable' => true), 'names'));
        $this->_aPostableTypes = array_unique($this->_aPostableTypes);
		$aPostableTypes = $this->getPostableTypes();                
        foreach ($aPostableTypes as $sPostableType)	{
			add_meta_box('userscontrol_post_access_logged_in', 'Users Control Protection', array(&$this, 'editPostContent'), $sPostableType, 'normal');
        }
	}
	
	public function getPostableTypes(){
        return $this->_aPostableTypes;
    }
	
	public function editPostContent($oPost){
		global $userscontrol;
		
        $iObjectId = $oPost->ID;
	   
		if (isset($_GET['attachment_id'])) {
				$iObjectId = sanitize_text_field($_GET['attachment_id']);
		} elseif (!isset($iObjectId)) {
				$iObjectId = 0;
		}
			
		$oPost = get_post($iObjectId);
		$sObjectType = $oPost->post_type;
		
		$userscontrol_protect_logged_in = get_post_meta( $iObjectId, 'userscontrol_protect_logged_in' , true);
		
		$html = '';
		
		$html .= '<div class="userscontrolembers-protect-group-options">	';				
		$html .= '<input type="hidden" name="userscontrol_update_logged_in_access" value="true" />	';			
			
		$checked = '';		 
		if ($userscontrol_protect_logged_in=='yes'){
			$checked = 'checked="checked"';
		}
				 
		$html .= ' <ul>';	
		$html .= ' <li>';			
		$html .= '<input type="checkbox" id="userscontrol_protect_logged_in" value="yes" name="userscontrol_protect_logged_in" '.$checked.' /> ';
                 
       $html .= ' <label for="userscontrol_protect_logged_in" class="selectit" style="display:inline;" >
            '.__('Only Logged in Users','users-control').'
        </label>';
				
		$html .= ' </li>';	
		$html .= ' </ul>';	
		$html .= ' </div>';

		echo wp_kses($html, $userscontrol->allowed_html);		

    }	
	
	function userscontrol_save_post_feature( $post_id ) {	
		// If this is just a revision, don't send the email.
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave($post_id) )
			return;
			
		 // stop on autosave
		 if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
			return;
		 }
		 
		 $post = get_post($post_id);
		 
        if($post->post_status == 'trash' || $post->post_status == 'draft' || $post->post_status == 'auto-draft' || $post->post_status == 'pending' || $post->post_type == 'page' || $post->post_type == 'product'){
                return $post_id;
        }
		
		if( $post->post_type != 'post'){
                return $post_id;
        }
		
		//check if is admin	post should be included in wall	
		$admin_post=$this->get_option('userscontrol_user_wall_enable_new_post_as_admin');		
		$is_admin = current_user_can( 'manage_options' );
				
		$logged_user_id = get_current_user_id();
					
		if($admin_post=='no' && $is_admin && $logged_user_id== $post->post_author){
			//return $post_id;
		
		}else{			
			
			//check if new posts should be included in the activity module		
			
		
		}
		
			
	}
	
		/* get setting */
	function get_option($option){
		$settings = get_option('userscontrol_options');
		if (isset($settings[$option])) {
			if(is_array($settings[$option])){
				return $settings[$option];
			}else{
				return stripslashes($settings[$option]);
			}
			
		}else{
			
		    return '';
		}
	}
	
	public function show_protected_content($atts, $content)	{
		global  $userscontrol;	
		
		extract( shortcode_atts( array(	
			
			'display_rule' => 'logged_in_based', //logged_in_based, membership_based, role_based
			'roles' => '', //administrator,subscriber			
			'membership_id' => '', // the ID of the membership package separated by commas
			'custom_message_loggedin' =>'', // custom message
			'custom_message_capability' =>__("You can't see this content.",'users-control'), // custom message
			'ccap' =>'', // custom capabilities
			'custom_message_membership' =>'', // custom message
			'custom_message_role' =>''
			
		), $atts ) );
		
		$package_list = array();
		 
		 if($custom_message_loggedin == "") {
			$custom_message_loggedin =  __('Content visible only for registered users. ','users-control');
					
		 }elseif($custom_message_loggedin == "_blank"){
			 
			 $custom_message_loggedin =  "";		 
		
		}
		 
		if($membership_id != "") {
			 $package_list  = explode(',', $membership_id);					
		}		
			
		
		if($display_rule == "logged_in_based")
		{
			//logged in based			
			if (!is_user_logged_in() && $custom_message_loggedin != "_blank") {
				return  '<div class="userscontrol-ultra-info">'.$custom_message_loggedin.'</div>';
				
			} else {
				
				if($ccap==''){
					//the users is logged in then display content
					return do_shortcode($content);	
				
				}else{
					
					//check for especial capabilities					
					$user_id = get_current_user_id();
					
					if($this->check_user_special_capability($user_id, $ccap)){						
						return do_shortcode($content);					
					
					}else{						
						
						return  '<div class="userscontrol-ultra-info">'.$custom_message_capability.'</div>';					
					
					}
				}
				
							
				
			}	
		
		}elseif($display_rule == "role_based"){					
			
			//logged in based			
			if (!is_user_logged_in()) {
				return  '<div class="userscontrol-ultra-info">'.$custom_message_role.'</div>';
				
			} else {
				
				//the user is logged in
				$user_id = get_current_user_id();					
								
				if($this->check_user_content_roles($user_id, $roles)){
					//the users is logged in then display content
					return do_shortcode($content);
				
				}else{
					
					return  '<div class="userscontrol-ultra-info">'.$custom_message_role.'</div>';
				}
			
			}
				
		}elseif($display_rule == "membership_based"){			
			
			//check logged in		
			if (!is_user_logged_in() && $custom_message_membership != "_blank"){
				return  '<div class="userscontrol-ultra-info">'.$custom_message_membership.'</div>';
			} else {
				
				//the user is logged in
				$user_id = get_current_user_id();		
						
				//is this the super admin?
				if(is_super_admin( $user_id )){
					return do_shortcode($content);							
					
				}else{
					
					//gel all membership packages of this user.			
					$user_memberships =$userscontrol->membership->get_all_user_active_memberships($user_id);
					
					foreach ($user_memberships as $membership){					
						if(in_array($membership, $package_list)){
							//yes, the member has this subscription plan.							
							if($ccap==''){
								//the users is logged in then display content
								return do_shortcode($content);	
							
							}else{
								
								//check if the user has the current capability								
								if($this->check_user_special_capability($user_id, $ccap))
								{						
									return do_shortcode($content);					
								
								}else{						
									
									return  '<div class="userscontrol-ultra-info">'.$custom_message_capability.'</div>';					
								
								}						
							
							
							} //end if ccap
								
								
								
						} //end if in subscription plan
					
					} //end for each					
					
						
				} //end if user is logged in
					
				
				if ( in_array($package , $package_list) )
				{
					
					
				}else{
					
					return  '<div class="uupublic-ultra-info">'.$custom_message_membership.'</div>';
					
				}
				
				//the users is logged in then display content								
				
			}		
			
		
		}
	
	}
	
	public function check_user_content_roles($user_id, $roles){
		global $wpdb,  $userscontrol;
		$roles_that_can_see = array();
		$roles_that_can_see  = explode(',', $roles);	
		
		foreach ($roles_that_can_see as $role)
		{			
			if($userscontrol->membership->is_user_in_role($user_id, $role)) 
			{
				return true;
			
			}
			
		
		}
		
		return false;
	
	}
	
	//Check if user can see this content based on special capabilities	
	public function check_user_special_capability($user_id, $ccap){		
		global $wpdb,  $userscontrol;
		
		//get user's ccap		
		$user_ccap_list = get_user_meta($user_id, 'ccap', true);
		
		if($user_ccap_list != "")
		{
			$user_ccap_array = array();			
			$user_ccap_array  = explode(',', $user_ccap_list);	
			
			//check if user can see this content			
			if ( in_array($ccap , $user_ccap_array) )
			{
					return true;
						
			}else{					
					return  false;
						
			}
		
		}else{
			
			return false;		
		
		}		
	}
	
}
$key = "postprotection";
$this->{$key} = new UserscontrolPostProtection();
?>