<?php
class UserscontrolAdmin extends UserscontrolCommon {

	var $options;
	var $wp_all_pages = false;
	var $userscontrol_default_options;
	var $valid_c;
	
	var $ajax_prefix = 'userscontrol';	
	var $table_prefix = 'userscontrol';
	
	var $notifications_email = array();

	function __construct() {
	
		/* Plugin slug and version */
		$this->slug = 'userscontrol';
		
		$this->set_default_email_messages();				
		$this->update_default_option_ini();		
		$this->set_font_awesome();
		
		
		add_action('admin_menu', array(&$this, 'add_menu'), 11);
	
		add_action('admin_enqueue_scripts', array(&$this, 'add_styles'), 9);
		add_action('admin_head', array(&$this, 'admin_head'), 9 );
		add_action('admin_init', array(&$this, 'admin_init'), 9);
		add_action('admin_init', array(&$this, 'do_valid_checks'), 9);
				
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_save_fields_settings', array( &$this, 'save_fields_settings' ));
				
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_add_new_custom_profile_field', array( &$this, 'add_new_custom_profile_field' ));
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_delete_profile_field', array( &$this, 'delete_profile_field' ));
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_sort_fileds_list', array( &$this, 'sort_fileds_list' ));
		
		//user to get all fields
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_reload_custom_fields_set', array( &$this, 'reload_custom_fields_set' ));
		//used to edit a field
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_reload_field_to_edit', array( &$this, 'reload_field_to_edit' ));	
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_reset_email_template', array( &$this, 'reset_email_template' ));
		
	}
	
	function admin_init() {
		
		$this->tabs = array(	  		
		
						
			
					
		
				
			'help' => __('Doc','users-control'),
			
		);
		
		$this->tabs_icons = array(
		    'main' => '',
			'tickets' => '',
			'departments' => '',
			'priority' =>'',
			'users' =>'',						
			'fields' => '',
						
			'mail' => '',		
			'help' => '',
		);		
		$this->default_tab = 'main';			
		
		$this->default_tab_membership = 'main';
		
		
	}
	
	public function update_default_option_ini () {
		$this->options = get_option('userscontrol_options');		
		$this->userscontrol_set_default_options();
		
		if (!get_option('userscontrol_options')) {
			update_option('userscontrol_options', $this->userscontrol_default_options );
		}
		
		if (!get_option('userscontrol_pro_active')){
			update_option('userscontrol_pro_active', true);
		}	
	}
	
	
	function get_me_wphtml_editor($meta, $content, $rows){
		// Turn on the output buffer
		ob_start();
		
		$editor_id = $meta;				
		$editor_settings = array('media_buttons' => false , 'textarea_rows' => $rows , 'teeny' =>true); 
		wp_editor( $content, $editor_id , $editor_settings);
		
		// Store the contents of the buffer in a variable
		$editor_contents = ob_get_clean();
		// Return the content you want to the calling function
		return $editor_contents;
	
	}	
	
	public function display_warning_messages()	{
		global $userscontrol;
			
		$account_page_id = $userscontrol->get_option('my_account_page');		
		$my_account_url = get_permalink($account_page_id);
		
		//rate plugin		
		$rate_plugin = get_option('userscontrol_rate_message');
		
		$message ='';
		
		$message_SAMPLE = '<div id="message" class="updated userscontrol-message wc-connect">
				<a class="userscontrol-message-close notice-dismiss" href="#" message-id="13"> '.__('Dismiss','users-control').'</a>
			
				<p><strong>'.__("IMPORTANT: Member Account:",'users-control').'</strong> – '.__("It's very important that you set the member's accuont page.",'users-control').'</p>
				
				<p class="submit">
					
					<a href="admin.php?page=userscontrol-pages" class="button-secondary" > '.__('Set Members Account Page','users-control').'</a>
				</p>
	      </div>';
		
			
		if($my_account_url=="" )
		{
		
			$message .= '<div id="message" class="updated userscontrol-message wc-connect">				
			
				<p><strong>'.__("IMPORTANT: Member Account:",'users-control').'</strong> – '.__("It's very important that you set the member's accuont page.",'users-control').'</p>
				
				<p class="submit">
					
					<a href="admin.php?page=userscontrol-pages" class="button-secondary" > '.__('Set Members Account Page','users-control').'</a>
				</p>
	      </div>';
			
			
		
		
		}
		
		if($rate_plugin=="" )
		{
		
		/*	$message .= '<div id="message" class="updated userscontrol-message-green wc-connect">				
			<a class="userscontrol-message-close notice-dismiss" href="#" message-id="userscontrol_rate_message"> '.__('Dismiss','users-control').'</a>
			
				<p><strong>'.__("Do you find this plugin useful?",'users-control').'</strong> – '.__("We offer free support, we love to do that, please consider leaving a 5 stars review on WordPress. That motivates us a lot to keep offering the best support for free.",'users-control').'</p>
				
				<p class="submit">
					
					<a href="https://wordpress.org/support/plugin/users-control/reviews/#new-post" class="button-secondary" > '.__('Rate Plugin!','users-control').'</a>
				</p>
	      </div>'; */
			
			
		
		
		}
		echo wp_kses($message, $userscontrol->allowed_html);
		

		
	}
	
	
	function get_pending_verify_requests_count(){
		$count = 0;	
		if ($count > 0){
			return '<span class="upadmin-bubble-new">'.$count.'</span>';
		}
	}
	
	function get_pending_verify_requests_count_only(){
		$count = 0;
		if ($count > 0){
			return $count;
		}
	}
	
	
	
	
	function admin_head(){
		$screen = get_current_screen();
		$slug = $this->slug;
		
	}

	function add_styles()
	{
		
		 global $wp_locale, $userscontrol , $pagenow;
		 
		 if('customize.php' != $pagenow )
        {
		 
			wp_register_style('userscontrol_admin', userscontrol_url.'admin/css/admin.css');
			wp_enqueue_style('userscontrol_admin');
			
			wp_register_style('userscontrol_datepicker', userscontrol_url.'admin/css/datepicker.css');
			wp_enqueue_style('userscontrol_datepicker');
			
			
			/*google graph*/		
			wp_register_script('userscontrol_jsgooglapli', 'https://www.gstatic.com/charts/loader.js');
			wp_enqueue_script('userscontrol_jsgooglapli');			
							
				
			//color picker		
			 wp_enqueue_style( 'userscontrol-color-picker' );	
				 
			 wp_register_script( 'userscontrol_color_picker', userscontrol_url.'admin/scripts/color-picker-js.js', array( 
				'userscontrol-color-picker'
			) );
			wp_enqueue_script( 'userscontrol_color_picker' );
			
			
			wp_register_script( 'userscontrol_admin',userscontrol_url.'admin/scripts/admin.js', array( 
				'jquery','jquery-ui-core','jquery-ui-draggable','jquery-ui-droppable',	'jquery-ui-sortable', 'jquery-ui-tabs', 'jquery-ui-autocomplete', 'jquery-ui-widget', 'jquery-ui-position'	), null );
			wp_enqueue_script( 'userscontrol_admin' );
			
			
			/* Font Awesome */
			wp_register_style( 'userscontrol_font_awesome', userscontrol_url.'css/css/font-awesome.min.css');
			wp_enqueue_style('userscontrol_font_awesome');
			
			// Using imagesLoaded? Do this.
			//wp_enqueue_script('imagesloaded',  userscontrol_url.'js/qtip/imagesloaded.pkgd.min.js' , null, false, true);
			
			// Add the styles first, in the <head> (last parameter false, true = bottom of page!)
			wp_enqueue_style('qtip', userscontrol_url.'js/qtip/jquery.qtip.min.css' , null, false, false);
			wp_enqueue_script('qtip',  userscontrol_url.'js/qtip/jquery.qtip.min.js', array('jquery', 'imagesloaded'), false, true);
			
		}
		
		$date_picker_format = $userscontrol->get_date_picker_format();
		 
		
		 wp_localize_script( 'userscontrol_admin', 'userscontrol_admin_v98', array(

			'nonce' => wp_create_nonce('ajax-nonce'),
            'msg_cate_delete'  => __( 'Are you totally sure that you wan to delete this category?', 'users-control' ),
			'msg_department_delete'  => __( 'Are you totally sure that you wan to delete this department?', 'users-control' ),
			
			'msg_trash_ticket'  => __( 'Are you totally sure that you wan to send this ticket to the trash?', 'users-control' ),
			'are_you_sure'  => __( 'Are you totally sure?', 'users-control' ),
			'set_new_priority'  => __( 'Set a new priority', 'users-control' ),
			'msg_department_edit'  => __( 'Edit Department', 'users-control' ),
			'msg_department_add'  => __( 'Add Department', 'users-control' ),
			'msg_department_input_title'  => __( 'Please input a name', 'users-control' ),
			
			'msg_priority_edit'  => __( 'Edit Priority', 'users-control' ),
			'msg_priority_add'  => __( 'Add Priority', 'users-control' ),
			'msg_priority_input_title'  => __( 'Please input a name', 'users-control' ),
			
			'msg_ticket_empty_reply'  => '<div class="userscontrol-ultra-error"><span><i class="fa fa-ok"></i>'.__('ERROR!. Please write a message ',"users-control").'</span></div>' ,
			
			'msg_ticket_submiting_reply'  => '<div class="userscontrol-ultra-wait"><span><i class="fa fa-ok"></i>'.__(' <img src="'.userscontrol_url.'/templates/images/loaderB16.gif" width="16" height="16" /> &nbsp; Please wait ... ',"users-control").'</span></div>' ,
			'msg_wait'  => __( '<img src="'.userscontrol_url.'/templates/images/loaderB16.gif" width="16" height="16" /> &nbsp; Please wait ... ', 'users-control' ) ,
			
			'msg_site_edit'  => __( 'Edit Product', 'users-control' ),
			'msg_site_add'  => __( 'Add Product', 'users-control' ),
			
			'msg_note_edit'  => __( 'Edit Note', 'users-control' ),
			
			'msg_category_edit'  => __( 'Edit Category', 'users-control' ),
			'msg_category_add'  => __( 'Add Category', 'users-control' ),
			
			'msg_category_input_title'  => __( 'Please input a title', 'users-control' ),
			'msg_category_delete'  => __( 'Are you totally sure that you wan to delete this service?', 'users-control' ),
			'msg_user_delete'  => __( 'Are you totally sure that you wan to delete this user?', 'users-control' ),
			
			'msg_status_change'  => __( 'Please set a new status', 'users-control' ),
			'msg_priority_change'  => __( 'Please set a new priority', 'users-control' ),
			
			'message_wait_staff_box'     => __("Please wait ...","users-control"),
			'msg_input_site_name'  => __( 'Please input a name', 'users-control' ),
			
			'msg_input_note_name'  => __( 'Please input a name', 'users-control' ),
			
			'date_picker_date_format'     => $date_picker_format

           
            
        ) );
		
		
		//localize our js
		$date_picker_array = array(
					'closeText'         => __( 'Done', "users-control" ),
					'currentText'       => __( 'Today', "users-control" ),
					'prevText' =>  __('Prev',"users-control"),
		            'nextText' => __('Next',"users-control"),				
					'monthNames'        => array_values( $wp_locale->month ),
					'monthNamesShort'   => array_values( $wp_locale->month_abbrev ),
					'monthStatus'       => __( 'Show a different month', "users-control" ),
					'dayNames'          => array_values( $wp_locale->weekday ),
					'dayNamesShort'     => array_values( $wp_locale->weekday_abbrev ),
					'dayNamesMin'       => array_values( $wp_locale->weekday_initial ),					
					// get the start of week from WP general setting
					'firstDay'          => get_option( 'start_of_week' ),
					// is Right to left language? default is false
					'isRTL'             => $wp_locale->is_rtl(),
				);
				
				
				wp_localize_script('userscontrol_admin', 'EASYWPMDatePicker', $date_picker_array);
				
		
	}
	
	public  function convertFormat( $source_format, $to )
    {
		global $bookingultrapro ;
		
        switch ( $source_format ) 
		{
            case 'date':
                $php_format = get_option( 'date_format', 'Y-m-d' );
                break;
            case 'time':
                $php_format = get_option( 'time_format', 'H:i' );
                break;
            default:
                $php_format = $source_format;
        }
		
		 switch ( $to ) {
            case 'fc' :
			
                $replacements = array(
                    'd' => 'DD',   '\d' => '[d]',
                    'D' => 'ddd',  '\D' => '[D]',
                    'j' => 'D',    '\j' => 'j',
                    'l' => 'dddd', '\l' => 'l',
                    'N' => 'E',    '\N' => 'N',
                    'S' => 'o',    '\S' => '[S]',
                    'w' => 'e',    '\w' => '[w]',
                    'z' => 'DDD',  '\z' => '[z]',
                    'W' => 'W',    '\W' => '[W]',
                    'F' => 'MMMM', '\F' => 'F',
                    'm' => 'MM',   '\m' => '[m]',
                    'M' => 'MMM',  '\M' => '[M]',
                    'n' => 'M',    '\n' => 'n',
                    't' => '',     '\t' => 't',
                    'L' => '',     '\L' => 'L',
                    'o' => 'YYYY', '\o' => 'o',
                    'Y' => 'YYYY', '\Y' => 'Y',
                    'y' => 'YY',   '\y' => 'y',
                    'a' => 'a',    '\a' => '[a]',
                    'A' => 'A',    '\A' => '[A]',
                    'B' => '',     '\B' => 'B',
                    'g' => 'h',    '\g' => 'g',
                    'G' => 'H',    '\G' => 'G',
                    'h' => 'hh',   '\h' => '[h]',
                    'H' => 'HH',   '\H' => '[H]',
                    'i' => 'mm',   '\i' => 'i',
                    's' => 'ss',   '\s' => '[s]',
                    'u' => 'SSS',  '\u' => 'u',
                    'e' => 'zz',   '\e' => '[e]',
                    'I' => '',     '\I' => 'I',
                    'O' => '',     '\O' => 'O',
                    'P' => '',     '\P' => 'P',
                    'T' => '',     '\T' => 'T',
                    'Z' => '',     '\Z' => '[Z]',
                    'c' => '',     '\c' => 'c',
                    'r' => '',     '\r' => 'r',
                    'U' => 'X',    '\U' => 'U',
                    '\\' => '',
                );
                return strtr( $php_format, $replacements );
			}
	}
	
	function add_menu() {
		global $userscontrol_activation ;
        $pending_count =0;		
		
		$pending_title = esc_attr( sprintf(__( '%d new manual activation requests','users-control'), $pending_count ) );
		if ($pending_count > 0)	{
			$menu_label = sprintf( __( 'Users Control %s','users-control' ), "<span class='update-plugins count-$pending_count' title='$pending_title'><span class='update-count'>" . number_format_i18n($pending_count) . "</span></span>" );
			
		} else {
			
			$menu_label = __('Users Control','users-control');
		}
		
		add_menu_page( __('Users Control','users-control'), $menu_label, 'manage_options', $this->slug, array(&$this, 'admin_page'), userscontrol_url .'admin/images/small_logo_16x16.png', '159.140');
		if(!isset($userscontrol_activation)){
			add_submenu_page( $this->slug, __('More Functionality!','users-control'), __('More Functionality!','users-control'), 'manage_options', 'userscontrol&tab=pro', array(&$this, 'admin_page') );
		}
		

		add_submenu_page( $this->slug, __('Content Protection','users-control'), __('Content Protection','users-control'), 'manage_options', 'userscontrol&tab=postpageprotection', array(&$this, 'admin_page') );
		add_submenu_page( $this->slug, __('Subscription Plans','users-control'), __('Subscription Plans','users-control'), 'manage_options', 'userscontrol&tab=membership', array(&$this, 'admin_page') );
		add_submenu_page( $this->slug, __('Payment Gateways','users-control'), __('Payment Gateways','users-control'), 'manage_options', 'userscontrol&tab=gateway', array(&$this, 'admin_page') );
		add_submenu_page( $this->slug, __('Subscriptions','users-control'), __('Subscriptions','users-control'), 'manage_options', 'userscontrol&tab=subscriptions', array(&$this, 'admin_page') );
		add_submenu_page( $this->slug, __('Notifications','users-control'), __('Notifications','users-control'), 'manage_options', 'userscontrol&tab=mail', array(&$this, 'admin_page') );
		add_submenu_page( $this->slug, __('Custom Fields','users-control'), __('Custom Fields','users-control'), 'manage_options', 'userscontrol&tab=fields', array(&$this, 'admin_page') );
		add_submenu_page( $this->slug, __('Settings','users-control'), __('Settings','users-control'), 'manage_options', 'userscontrol&tab=settings', array(&$this, 'admin_page') );
		add_submenu_page( $this->slug, __('Licensing','users-control'), __('Licensing','users-control'), 'manage_options', 'userscontrol&tab=licence', array(&$this, 'admin_page') );
		do_action('userscontrol_admin_menu_hook');
	}

	function admin_tabs( $current = null ) {		
		global $userscontrolcomplement, $userscontrol_custom_fields;
        $custom_badge = '';
		$tabs = $this->tabs;
		$links = array();
		if ( isset ( $_GET['tab'] ) ) {
			$current = sanitize_text_field($_GET['tab']);
		} else {
			$current = $this->default_tab;
		}
		foreach( $tabs as $tab => $name ) :
			
			
			    if($tab=="pro"){
					
					$custom_badge = 'userscontrol-pro-tab-bubble ';
					
				}
				
				if($tab=="fields" && !isset($userscontrol_custom_fields)){continue;}
				
				if(isset($userscontrolcomplement) && $tab=="pro"){continue;}
				
				
				if ( $tab == $current ) :
					$links[] = "<a class='nav-tab nav-tab-active ".$custom_badge."' href='?page=".$this->slug."&tab=$tab'><span class='wptu-adm-tab-legend'>".$name."</span></a>";
				else :
					$links[] = "<a class='nav-tab ".$custom_badge."' href='?page=".$this->slug."&tab=$tab'><span class='wptu-adm-tab-legend'>".$name."</span></a>";
				endif;
			endforeach;
			foreach ( $links as $link )
				echo esc_url($link);
	}
	
	function do_action(){
		global $userscontrol;
	}
	
	/* set a global option */
	function userscontrol_set_option($option, $newvalue){
		$settings = get_option('userscontrol_options');		
		$settings[$option] = $newvalue;
		update_option('userscontrol_options', $settings);
	}
	
	/* default options */
	function userscontrol_set_default_options(){
	
		$this->userscontrol_default_options = array(									
						
						'messaging_send_from_name' => get_option('blogname'),
						
						'userscontrol_noti_admin' => 'yes',
						'userscontrol_noti_staff' => 'yes',
						'userscontrol_noti_client' => 'yes',
						'messaging_send_from_email' => get_option( 'admin_email' ),
						'company_name' => get_option('blogname'),	
						
						'allowed_extensions' => 'jpg,png,gif,jpeg,pdf,doc,docx,xls',	
						
																		
						'email_reset_link_message_body' => $this->get_email_template('email_reset_link_message_body'),
						'email_reset_link_message_subject' => __('Password Reset','users-control'),
			
			
						'email_password_change_member_body' => $this->get_email_template('email_password_change_member_body'),
						'email_password_change_member_subject' => __('Password Reset Confirmation','users-control'),
			
						'email_registration_body' => $this->get_email_template('email_registration_body'),
						'email_registration_subject' => __('Your Account Details','users-control'),
						
						'email_package_upgrade_body' => $this->get_email_template('email_package_upgrade_body'),
						'email_package_upgrade_subject' => __('Purchase Confirmation','users-control'),
						
						'email_package_upgrade_admin_body' => $this->get_email_template('email_package_upgrade_admin_body'),
						'email_package_upgrade_admin_subject' => __('Purchase Confirmation','users-control'),
						
						'email_package_renewal_body' => $this->get_email_template('email_package_renewal_body'),
						'email_package_renewal_subject' => __('Membership Renewal Notification','users-control'),
						
						'email_package_renewal_admin_body' => $this->get_email_template('email_package_renewal_admin_body'),
						'email_package_renewal_admin_subject' => __('Membership Renewal Notification','users-control'),

						'new_account_activation_link' => $this->get_email_template('new_account_activation_link'),
						'new_account_activation_link_subject' =>  __('Account Activation','users-control'),						
						
						'messaging_admin_moderation_user' => $this->get_email_template('new_account_admin_moderation'),
						'new_account_admin_moderation_admin' => $this->get_email_template('new_account_admin_moderation_admin'),
						
						'messaging_re_send_activation_link' => $this->get_email_template('messaging_re_send_activation_link'),					
						'account_verified_sucess_message_body' => $this->get_email_template('account_verified_sucess_message_body'),
						'account_verified_sucess_message_subject' =>  __('Your account has been activated','users-control'),		
				);
		
	}
	
	public function set_default_email_messages(){
		$line_break = "\r\n";	
				
		//Staff Password Reset	
		$email_body =  '{{userscontrol_user_name}},'.$line_break.$line_break;
		$email_body .= __("Please use the following link to reset your password.","users-control") . $line_break.$line_break;			
		$email_body .= "{{userscontrol_reset_link}}".$line_break.$line_break;
		$email_body .= __('If you did not request a new password delete this email.','users-control'). $line_break.$line_break;	
			
		$email_body .= __('Best Regards!','users-control'). $line_break;
		$email_body .= '{{userscontrol_company_name}}'. $line_break;
		$email_body .= '{{userscontrol_company_phone}}'. $line_break;
		$email_body .= '{{userscontrol_company_url}}'. $line_break. $line_break;		
	    $this->notifications_email['email_reset_link_message_body'] = $email_body;
		
		$email_body =  '{{userscontrol_user_name}},'.$line_break.$line_break;
		$email_body .= __("Your password has been updated successfully","users-control") . $line_break.$line_break;			
			
		$email_body .= __('Best Regards!','users-control'). $line_break;
		$email_body .= '{{userscontrol_company_name}}'. $line_break;
		$email_body .= '{{userscontrol_company_phone}}'. $line_break;
		$email_body .= '{{userscontrol_company_url}}'. $line_break. $line_break;		
	    $this->notifications_email['email_password_change_member_body'] = $email_body;

		//Account Verified Sucess
		$email_body = __('Hi,' ,"users-control") . $line_break.$line_break;
		$email_body .= __("Your account has been verified.","xoousers") . $line_break.$line_break;	
		$email_body .= __('Please use the link below to get in your account.','users-control'). $line_break.$line_break;	
		$email_body .=   "{{userscontrol_login_link}}".$line_break.$line_break;	
		$email_body .= __('Best Regards!','users-control') . $line_break.$line_break;		
	    $this->notifications_email['account_verified_sucess_message_body'] = $email_body;
		
		
				
		//User Registration Email
		$email_body =  __('Hello ','users-control') .'{{userscontrol_client_name}},'.$line_break.$line_break;
		$email_body .= __("Thank you for your registration. Your login details for your account are as follows:","users-control") . $line_break.$line_break;
		$email_body .= __('Username: {{userscontrol_user_name}}','users-control') . $line_break;
		$email_body .= __('Password: {{userscontrol_user_password}}','users-control') . $line_break;
		$email_body .= __("Please use the following link to login to your account.","users-control") . $line_break.$line_break;			
		$email_body .= "{{userscontrol_login_link}}".$line_break.$line_break;
			
		$email_body .= __('Best Regards!','users-control'). $line_break;
		$email_body .= '{{userscontrol_company_name}}'. $line_break;
		$email_body .= '{{userscontrol_company_phone}}'. $line_break;
		$email_body .= '{{userscontrol_company_url}}'. $line_break. $line_break;
		
	    $this->notifications_email['email_registration_body'] = $email_body;
		
		//User Package Purchase 
		$email_body =  __('Hello ','users-control') .'{{userscontrol_client_name}},'.$line_break.$line_break;
		$email_body .= __("Thank you very much for your purchase. This email contains details about your recent purchase and subscription, please keep it as receipt.","users-control") . $line_break.$line_break;
		$email_body .= "--------------------------------------------" . $line_break.$line_break;
		$email_body .= __("Subscription Details. :","users-control") . $line_break.$line_break;

		$email_body .= __('Subscription: {{userscontrol_subscription_name}}','users-control') . $line_break;
		$email_body .= __('ID: {{userscontrol_subscription_id}}','users-control') . $line_break;
		$email_body .= __('Amount: {{userscontrol_subscription_amount}}','users-control') . $line_break;
		$email_body .= __('Period: {{userscontrol_period}}','users-control') . $line_break;
		$email_body .= __('Agreement: {{userscontrol_subscription_agreement}}','users-control') . $line_break. $line_break;

		$email_body .= __('Best Regards!','users-control'). $line_break;
		$email_body .= '{{userscontrol_company_name}}'. $line_break;
		$email_body .= '{{userscontrol_company_phone}}'. $line_break;
		$email_body .= '{{userscontrol_company_url}}'. $line_break. $line_break;
		
	    $this->notifications_email['email_package_upgrade_body'] = $email_body;
		
		//Admin Notification Package Purchase 
		$email_body =  __('Hello Admin, ','users-control') .$line_break.$line_break;
		$email_body .= __("This email is to notify you that a new subscription has been purchased. Please keep this email as a receipt.","users-control") . $line_break.$line_break;
		$email_body .= "--------------------------------------------" . $line_break.$line_break;
		$email_body .= __("Subscription Details. :","users-control") . $line_break.$line_break;

		$email_body .= __('Subscription: {{userscontrol_subscription_name}}','users-control') . $line_break;
		$email_body .= __('ID: {{userscontrol_subscription_id}}','users-control') . $line_break;
		$email_body .= __('Amount: {{userscontrol_subscription_amount}}','users-control') . $line_break;
		$email_body .= __('Client: {{userscontrol_client_name}}','users-control') . $line_break;
		$email_body .= __('Period: {{userscontrol_period}}','users-control') . $line_break;
		$email_body .= __('Agreement: {{userscontrol_subscription_agreement}}','users-control') . $line_break. $line_break;

		$email_body .= __('Best Regards!','users-control'). $line_break;
		$email_body .= '{{userscontrol_company_name}}'. $line_break;
		$email_body .= '{{userscontrol_company_phone}}'. $line_break;
		$email_body .= '{{userscontrol_company_url}}'. $line_break. $line_break;
		
	    $this->notifications_email['email_package_upgrade_admin_body'] = $email_body;
		
		//Admin Notification Package Renewal 
		$email_body =  __('Hello Admin, ','users-control') .$line_break.$line_break;
		$email_body .=  __('This emails is a confirmation that a subscription has been renewed successfully. Please keep this email as receipt of the renewal. ','users-control') .$line_break.$line_break;
		$email_body .= "--------------------------------------------" . $line_break.$line_break;
		$email_body .= __("Subscription Renewal Details. :","users-control") . $line_break.$line_break;
		$email_body .= __('Subscription: {{userscontrol_subscription_name}}','users-control') . $line_break;
		$email_body .= __('ID: {{userscontrol_subscription_id}}','users-control') . $line_break;
		$email_body .= __('Merchant ID: {{userscontrol_subscription_profile_id}}','users-control') . $line_break;
		$email_body .= __('Amount: {{userscontrol_subscription_amount}}','users-control') . $line_break;
		$email_body .= __('Client: {{userscontrol_client_name}}','users-control') . $line_break;
		$email_body .= __('Period: {{userscontrol_period}}','users-control') . $line_break. $line_break;
		
			
		$email_body .= __('Best Regards!','users-control'). $line_break;
		$email_body .= '{{userscontrol_company_name}}'. $line_break;
		$email_body .= '{{userscontrol_company_phone}}'. $line_break;
		$email_body .= '{{userscontrol_company_url}}'. $line_break. $line_break;		
	    $this->notifications_email['email_package_renewal_admin_body'] = $email_body;
		
		//Client Notification Package Renewal 
		$email_body =  __('Hello ','users-control') .'{{userscontrol_client_name}},'.$line_break.$line_break;
		$email_body .= __("Thank you very much for renewing your subscription. This email contains useful information about the subscription renewal.","users-control") . $line_break.$line_break;
		$email_body .= "--------------------------------------------" . $line_break.$line_break;
		$email_body .= __("Subscription Renewal Details. :","users-control") . $line_break.$line_break;
		$email_body .= __('Subscription: {{userscontrol_subscription_name}}','users-control') . $line_break;
		$email_body .= __('Amount: {{userscontrol_subscription_amount}}','users-control') . $line_break;
		$email_body .= __('Period: {{userscontrol_period}}','users-control') . $line_break. $line_break;
			
		$email_body .= __('Best Regards!','users-control'). $line_break;
		$email_body .= '{{userscontrol_company_name}}'. $line_break;
		$email_body .= '{{userscontrol_company_phone}}'. $line_break;
		$email_body .= '{{userscontrol_company_url}}'. $line_break. $line_break;		
	    $this->notifications_email['email_package_renewal_body'] = $email_body;		

				
		$email_body = __('Hi,' ,"users-control") . $line_break.$line_break;
		$email_body .= __("Thanks for registering. Your account needs activation.","users-control") .  $line_break.$line_break;
		$email_body .= __("Please click on the link below to activate your account:","users-control") .  $line_break.$line_break;
		$email_body .= "{{userscontrol_activation_url}}" . $line_break.$line_break;
		$email_body .= __('Your account e-mail: {{userscontrol_user_email}}','users-control') . $line_break.$line_break;
		$email_body .= __('Your account username: {{userscontrol_user_name}}','users-control') . $line_break.$line_break;
		$email_body .= __('Your account password: {{userscontrol_pass}}','users-control') . $line_break.$line_break;
		$email_body .= __('If you have any problems, please contact us at {{userscontrol_admin_email}}.','users-control') . $line_break.$line_break;
		$email_body .= __('Best Regards!','users-control');
	    $this->notifications_email['new_account_activation_link'] = $email_body;
		
		$email_body = __('Hi,' ,"users-control") . $line_break.$line_break;
		$email_body .= __("We are re-sending you the activation link.","users-control") .  $line_break.$line_break;
		$email_body .= __("Please click on the link below to activate your account:","users-control") .  $line_break.$line_break;
		$email_body .= "{{userscontrol_activation_url}}" . $line_break.$line_break;
		$email_body .= __('If you have any problems, please contact us at {{userscontrol_admin_email}}.','users-control') . $line_break.$line_break;
		$email_body .= __('Best Regards!','users-control');
	    $this->notifications_email['messaging_re_send_activation_link'] = $email_body;
		
		//admin
		$email_body = __('Hi Admin,' ,"users-control") . $line_break.$line_break;
		$email_body .= __("An account needs activation.","users-control") .  $line_break.$line_break;		
		$email_body .= __('Account e-mail: {{userscontrol_user_email}}','users-control') . $line_break;
		$email_body .= __('Account username: {{userscontrol_user_name}}','users-control') . $line_break;
		$email_body .= __('Please login to your admin to activate the account.','users-control') . $line_break.$line_break;
		$email_body .= __('Best Regards!','users-control');
	    $this->notifications_email['new_account_activation_link_admin'] = $email_body;	
		
		//admin manually approved 
		$email_body = __('Hi Admin,' ,"users-control") . $line_break.$line_break;
		$email_body .= __("An account needs approval.","users-control") .  $line_break.$line_break;		
		$email_body .= __('Account e-mail: {{userscontrol_user_email}}','users-control') . $line_break;
		$email_body .= __('Account username: {{userscontrol_user_name}}','users-control') . $line_break;
		$email_body .= __('Please login to your admin to activate the account.','users-control') . $line_break.$line_break;
		$email_body .= __('Best Regards!','users-control');
	    $this->notifications_email['new_account_admin_moderation_admin'] = $email_body;
		
		//client manually approved 
		$email_body = __('Hi {{userscontrol_user_name}},' ,"users-control") . $line_break.$line_break;
		$email_body .= __("Your account will be reviewed by the admin soon.","users-control") .  $line_break.$line_break;		
		$email_body .= __('Best Regards!','users-control');
	    $this->notifications_email['new_account_admin_moderation'] = $email_body;
		
			
	
	}
	
	public function get_email_template($key){
		$res ='' ;
		if(isset($this->notifications_email[$key])){
			$res =$this->notifications_email[$key] ;
		}
		return $res;
	}
	
	public function set_font_awesome()
	{
		        /* Store icons in array */
        $this->fontawesome = array(
                'cloud-download','cloud-upload','lightbulb','exchange','bell-alt','file-alt','beer','coffee','food','fighter-jet',
                'user-md','stethoscope','suitcase','building','hospital','ambulance','medkit','h-sign','plus-sign-alt','spinner',
                'angle-left','angle-right','angle-up','angle-down','double-angle-left','double-angle-right','double-angle-up','double-angle-down','circle-blank','circle',
                'desktop','laptop','tablet','mobile-phone','quote-left','quote-right','reply','github-alt','folder-close-alt','folder-open-alt',
                'adjust','asterisk','ban-circle','bar-chart','barcode','beaker','beer','bell','bolt','book','bookmark','bookmark-empty','briefcase','bullhorn',
                'calendar','camera','camera-retro','certificate','check','check-empty','cloud','cog','cogs','comment','comment-alt','comments','comments-alt',
                'credit-card','dashboard','download','download-alt','edit','envelope','envelope-alt','exclamation-sign','external-link','eye-close','eye-open',
                'facetime-video','film','filter','fire','flag','folder-close','folder-open','gift','glass','globe','group','hdd','headphones','heart','heart-empty',
                'home','inbox','info-sign','key','leaf','legal','lemon','lock','unlock','magic','magnet','map-marker','minus','minus-sign','money','move','music',
                'off','ok','ok-circle','ok-sign','pencil','picture','plane','plus','plus-sign','print','pushpin','qrcode','question-sign','random','refresh','remove',
                'remove-circle','remove-sign','reorder','resize-horizontal','resize-vertical','retweet','road','rss','screenshot','search','share','share-alt',
                'shopping-cart','signal','signin','signout','sitemap','sort','sort-down','sort-up','spinner','star','star-empty','star-half','tag','tags','tasks',
                'thumbs-down','thumbs-up','time','tint','trash','trophy','truck','umbrella','upload','upload-alt','user','volume-off','volume-down','volume-up',
                'warning-sign','wrench','zoom-in','zoom-out','file','cut','copy','paste','save','undo','repeat','text-height','text-width','align-left','align-right',
                'align-center','align-justify','indent-left','indent-right','font','bold','italic','strikethrough','underline','link','paper-clip','columns',
                'table','th-large','th','th-list','list','list-ol','list-ul','list-alt','arrow-down','arrow-left','arrow-right','arrow-up','caret-down',
                'caret-left','caret-right','caret-up','chevron-down','chevron-left','chevron-right','chevron-up','circle-arrow-down','circle-arrow-left',
                'circle-arrow-right','circle-arrow-up','hand-down','hand-left','hand-right','hand-up','play-circle','play','pause','stop','step-backward',
                'fast-backward','backward','forward','step-forward','fast-forward','eject','fullscreen','resize-full','resize-small','phone','phone-sign',
                'facebook','facebook-sign','twitter','twitter-sign','github','github-sign','linkedin','linkedin-sign','pinterest','pinterest-sign',
                'google-plus','google-plus-sign','sign-blank' ,'instagram' ,'youtube'
        );
        asort($this->fontawesome);
		
	
	
	}

	function validate_auth_access(){
		// Check for nonce security      
		if ( ! wp_verify_nonce( sanitize_text_field($_POST['_ajax_nonce']), 'ajax-nonce' ) ) {
			die ( 'Busted!');
		}

		if ( !current_user_can( 'manage_options' ) ) {
			die ( 'Not authorized!');		
		}
	}	
		
	
	/*This Function Change the Profile Fields Order when drag/drop */	
	public function sort_fileds_list()	{
		global $wpdb;
		
		// Check for nonce security      
		$this->validate_auth_access();
			
		$order = explode(',', sanitize_text_field($_POST['order']));
		$counter = 0;
		$new_pos = 10;
		
		//multi fields		
		$custom_form = sanitize_text_field($_POST["userscontrol_custom_form"]);
		
		if($custom_form!=""){
			$custom_form = 'userscontrol_profile_fields_'.$custom_form;		
			$fields = get_option($custom_form);			
			$fields_set_to_update =$custom_form;
		}else{			
			$fields = get_option('userscontrol_profile_fields');
			$fields_set_to_update ='userscontrol_profile_fields';
		}
		
		$new_fields = array();		
		$fields_temp = $fields;
		ksort($fields);
		
		foreach ($fields as $field){
			
			$fields_temp[$order[$counter]]["position"] = $new_pos;			
			$new_fields[$new_pos] = $fields_temp[$order[$counter]];				
			$counter++;
			$new_pos=$new_pos+10;
		}
		ksort($new_fields);		
		update_option($fields_set_to_update, $new_fields);		
		print_r($new_fields);
		die();
    }

	/*  delete profile field */
    public function delete_profile_field(){		
		
		// Check for nonce security      
		$this->validate_auth_access();
		
		if($_POST['_item']!= ""){		
			$custom_form = sanitize_text_field($_POST["userscontrol_custom_form"]);
			if($custom_form!=""){
				$custom_form = 'userscontrol_profile_fields_'.$custom_form;		
				$fields = get_option($custom_form);			
				$fields_set_to_update =$custom_form;
				
			}else{
				
				$fields = get_option('userscontrol_profile_fields');
				$fields_set_to_update ='userscontrol_profile_fields';
			}
			
			$pos = sanitize_text_field($_POST['_item']);
			unset($fields[$pos]);
			ksort($fields);
			print_r($fields);
			update_option($fields_set_to_update, $fields);
		}
	
	}
	
	 /* create new custom profile field */
    public function add_new_custom_profile_field(){
		
		// Check for nonce security      
		$this->validate_auth_access();
		
		if($_POST['_meta']!= ""){
			$meta = sanitize_text_field($_POST['_meta']);
		}else{			
			$meta = sanitize_text_field($_POST['_meta_custom']);
		}	
		
		//multi fields		
		$custom_form = sanitize_text_field( $_POST["custom_form"]);
		
		if($custom_form!="")
		{
			$custom_form = 'userscontrol_profile_fields_'.$custom_form;		
			$fields = get_option($custom_form);			
			$fields_set_to_update =$custom_form;
			
		}else{
			
			$fields = get_option('userscontrol_profile_fields');
			$fields_set_to_update ='userscontrol_profile_fields';
		
		}
		
		$min = min(array_keys($fields)); 
		
		$pos = $min-1;
		
		$fields[$pos] =array(
			  'position' => $pos,
				'icon' => sanitize_text_field($_POST['_icon']),
				'type' => sanitize_text_field($_POST['_type']),
				'field' => sanitize_text_field($_POST['_field']),
				'meta' => sanitize_text_field($meta),
				'name' => sanitize_text_field($_POST['_name']),				
				'tooltip' => sanitize_text_field($_POST['_tooltip']),
				'help_text' => sanitize_text_field($_POST['_help_text']),							
				'can_edit' => sanitize_text_field($_POST['_can_edit']),
				'allow_html' => sanitize_text_field($_POST['_allow_html']),
				'can_hide' => sanitize_text_field($_POST['_can_hide']),	
				'social' =>  sanitize_text_field($_POST['_social']),			
				'private' => sanitize_text_field($_POST['_private']),
				'required' => sanitize_text_field($_POST['_required']),
				'show_in_register' => sanitize_text_field($_POST['_show_in_register']),
				'predefined_options' => sanitize_text_field($_POST['_predefined_options']),				
				'choices' => sanitize_text_field($_POST['_choices']),												
				'deleted' => 0,
				'show_to_user_role' => sanitize_text_field($_POST['_show_to_user_role']),
                'edit_by_user_role' => sanitize_text_field($_POST['_edit_by_user_role'])
				

			);		
			
			// Save user roles which has permission for view and edit the field
            if (isset($_POST['_show_to_user_role_list']) ){
                    $fields[$pos]['show_to_user_role_list'] = sanitize_text_field($_POST['_show_to_user_role_list']);					
            }
           
		   if (isset($_POST['_edit_by_user_role_list']) ){
                    $fields[$pos]['edit_by_user_role_list'] = sanitize_text_field($_POST['_edit_by_user_role_list']);
           }
					
			ksort($fields);
			print_r($fields);			
		    update_option($fields_set_to_update, $fields);         


    }

    // save form
    public function save_fields_settings()	{
		global $userscontrol;	
		
		// Check for nonce security      
		$this->validate_auth_access();

		$pos = sanitize_text_field($_POST['pos']);
		if($_POST['_meta']!= ""){
			$meta = sanitize_text_field($_POST['_meta']);		
		}else{			
			$meta = sanitize_text_field($_POST['_meta_custom']);
		}	
		$custom_form = sanitize_text_field($_POST["custom_form"]);
		
		if($custom_form!=""){
			$custom_form = 'userscontrol_profile_fields_'.$custom_form;		
			$fields = get_option($custom_form);			
			$fields_set_to_update =$custom_form;			
		}else{			
			$fields = get_option('userscontrol_profile_fields');
			$fields_set_to_update ='userscontrol_profile_fields';
		}
		
		$fields[$pos] =array(
			  'position' => $pos,
				'icon' => sanitize_text_field($_POST['_icon']),
				'type' => sanitize_text_field($_POST['_type']),
				'field' => sanitize_text_field($_POST['_field']),
				'meta' => sanitize_text_field($meta),
				'name' => sanitize_text_field($_POST['_name']),
				'ccap' => sanitize_text_field($_POST['_ccap']),
				'tooltip' => sanitize_text_field($_POST['_tooltip']),
				'help_text' => sanitize_text_field($_POST['_help_text']),
				'social' =>  sanitize_text_field($_POST['_social']),
				'is_a_link' =>  sanitize_text_field($_POST['_is_a_link']),
				'can_edit' => sanitize_text_field($_POST['_can_edit']),
				'allow_html' => sanitize_text_field($_POST['_allow_html']),				
				'required' => sanitize_text_field($_POST['_required']),
				'show_in_register' => sanitize_text_field($_POST['_show_in_register']),				
				'predefined_options' => sanitize_text_field($_POST['_predefined_options']),				
				'choices' => $_POST['_choices'],
				//'choices' => wp_kses($_POST['_choices'], $userscontrol->allowed_html),												
				'deleted' => 0,
				'show_to_user_role' => sanitize_text_field($_POST['_show_to_user_role']),
                'edit_by_user_role' => sanitize_text_field($_POST['_edit_by_user_role'])
		);

		// Save user roles which has permission for view and edit the field
		if (isset($_POST['_show_to_user_role_list']) ){
				$fields[$pos]['show_to_user_role_list'] = sanitize_text_field($_POST['_show_to_user_role_list']);					
		}
	   
	   if (isset($_POST['_edit_by_user_role_list']) ){
				$fields[$pos]['edit_by_user_role_list'] = sanitize_text_field($_POST['_edit_by_user_role_list']);
	   }

		print_r($fields);
		update_option($fields_set_to_update , $fields);

    }
		

	function reload_field_to_edit(){
		global $userscontrol;	
		
		// Check for nonce security      
		$this->validate_auth_access();

		//get field
		$pos = sanitize_text_field($_POST["pos"]);		
		
		//multi fields		
		$custom_form = sanitize_text_field($_POST["custom_form"]);
		
		if($custom_form!=""){
			$custom_form = 'userscontrol_profile_fields_'.$custom_form;		
			$fields = get_option($custom_form);			
			$fields_set_to_update =$custom_form;			
		}else{			
			$fields = get_option('userscontrol_profile_fields');
			$fields_set_to_update ='userscontrol_profile_fields';
		}
		
		$array = $fields[$pos];
		extract($array); $i++;

		if(!isset($required))
		       $required = 0;

		    if(!isset($fonticon))
		        $fonticon = '';				
				
			if ($type == 'seperator' || $type == 'separator') {
				$class = "separator";
				$class_title = "";
			} else {
				$class = "profile-field";
				$class_title = "profile-field";
			}
		
		
		?>
		
		

				<p>
					<label for="userscontrol_<?php echo esc_attr($pos); ?>_position"><?php _e('Position','users-control'); ?>
					</label> <input name="userscontrol_<?php echo esc_attr($pos); ?>_position"
						type="text" id="userscontrol_<?php echo esc_attr($pos); ?>_position"
						value="<?php echo esc_attr($pos); ?>" class="small-text" /> <i
						class="userscontrol_icon-question-sign userscontrol-tooltip2"
						title="<?php _e('Please use a unique position. Position lets you place the new field in the place you want exactly in Profile view.','users-control'); ?>"></i>
				</p>

				<p>
					<label for="userscontrol_<?php echo esc_attr($pos); ?>_type"><?php _e('Field Type','users-control'); ?>
					</label> <select name="userscontrol_<?php echo esc_attr($pos); ?>_type"
						id="userscontrol_<?php echo esc_attr($pos); ?>_type">
						<option value="usermeta" <?php selected('usermeta', $type); ?>>
							<?php _e('Profile Field','users-control'); ?>
						</option>
						<option value="separator" <?php selected('separator', $type); ?>>
							<?php _e('Separator','users-control'); ?>
						</option>
					</select> <i class="userscontrol-icon-question-sign userscontrol-tooltip2"
						title="<?php _e('You can create a separator or a usermeta (profile field)','users-control'); ?>"></i>
				</p> 
				
				<?php if ($type != 'separator') { ?>

				<p class="userscontrol-inputtype">
					<label for="userscontrol_<?php echo esc_attr($pos); ?>_field"><?php _e('Field Input','users-control'); ?>
					</label> <select name="userscontrol_<?php echo esc_attr($pos); ?>_field"
						id="userscontrol_<?php echo esc_attr($pos); ?>_field">
						<?php
						
						 foreach($userscontrol->allowed_inputs as $input=>$label) { ?>
						<option value="<?php echo esc_attr($input); ?>"
						<?php selected($input, $field); ?>>
							<?php echo esc_attr($label); ?>
						</option>
						<?php } ?>
					</select> <i class="userscontrol-icon-question-sign userscontrol-tooltip2"
						title="<?php _e('When user edit profile, this field can be an input (text, textarea, image upload, etc.)','users-control'); ?>"></i>
				</p>

				
				<p>
					<label for="userscontrol_<?php echo esc_attr($pos); ?>_meta_custom"><?php _e('Custom Meta Field','users-control'); ?>
					</label> <input name="userscontrol_<?php echo esc_attr($pos); ?>C"
						type="text" id="userscontrol_<?php echo esc_attr($pos); ?>_meta_custom"
						value="<?php if (!isset($all_meta_for_user[$meta])) echo esc_attr($meta); ?>" />
					<i class="userscontrol-icon-question-sign userscontrol-tooltip2"
						title="<?php _e('Enter a custom meta key for this profile field if do not want to use a predefined meta field above. It is recommended to only use alphanumeric characters and underscores, for example my_custom_meta is a proper meta key.','users-control'); ?>"></i>
				</p> <?php } ?>

				
                
                
                <p>
					<label for="userscontrol_<?php echo esc_attr($pos); ?>_name"><?php _e('Label / Name','users-control'); ?>
					</label> <input name="userscontrol_<?php echo esc_attr($pos); ?>_name" type="text"
						id="userscontrol_<?php echo esc_attr($pos); ?>_name" value="<?php echo esc_attr($name); ?>" />
					<i class="userscontrol-icon-question-sign userscontrol-tooltip2"
						title="<?php _e('Enter the label / name of this field as you want it to appear in front-end (Profile edit/view)','users-control'); ?>"></i>
				</p>
                
                

			<?php if ($type != 'separator' ) { ?>

				
				<p>
					<label for="userscontrol_<?php echo esc_attr($pos); ?>_tooltip"><?php _e('Tooltip Text','users-control'); ?>
					</label> <input name="userscontrol_<?php echo esc_attr($pos); ?>_tooltip" type="text"
						id="userscontrol_<?php echo esc_attr($pos); ?>_tooltip"
						value="<?php echo esc_attr($tooltip); ?>" /> <i
						class="userscontrol-icon-question-sign userscontrol-tooltip2"
						title="<?php _e('A tooltip text can be useful for social buttons on profile header.','users-control'); ?>"></i>
				</p> 
                
               <p>
               
               <label for="userscontrol_<?php echo esc_attr($pos); ?>_help_text"><?php _e('Help Text','users-control'); ?>
                </label><br />
                    <textarea class="userscontrol-help-text" id="userscontrol_<?php echo esc_attr($pos); ?>_help_text" name="userscontrol_<?php echo esc_attr($pos); ?>_help_text" title="<?php _e('A help text can be useful for provide information about the field.','users-control'); ?>" ><?php echo esc_attr($help_text); ?></textarea>
                    <i class="userscontrol-icon-question-sign userscontrol-tooltip2"
                                title="<?php _e('Show this help text under the profile field.','users-control'); ?>"></i>
                              
               </p> 
				
				
				
                
               				
				<?php 
				if(!isset($can_edit))
				    $can_edit = '1';
				?>
				<p>
					<label for="userscontrol_<?php echo esc_attr($pos); ?>_can_edit"><?php _e('User can edit','users-control'); ?>
					</label> <select name="userscontrol_<?php echo esc_attr($pos); ?>_can_edit"
						id="userscontrol_<?php echo esc_attr($pos); ?>_can_edit">
						<option value="1" <?php selected(1, $can_edit); ?>>
							<?php _e('Yes','users-control'); ?>
						</option>
						<option value="0" <?php selected(0, $can_edit); ?>>
							<?php _e('No','users-control'); ?>
						</option>
					</select> <i class="userscontrol-icon-question-sign userscontrol-tooltip2"
						title="<?php _e('Users can edit this profile field or not.','users-control'); ?>"></i>
				</p> 
				
				<?php if (!isset($array['allow_html'])) { 
				    $allow_html = 0;
				} ?>

				<p>
					<label for="userscontrol_<?php echo esc_attr($pos); ?>_allow_html"><?php _e('Allows HTML','users-control'); ?>
					</label> <select name="userscontrol_<?php echo esc_attr($pos); ?>_allow_html"
						id="userscontrol_<?php echo esc_attr($pos); ?>_allow_html">
						<option value="1" <?php selected(1, $allow_html); ?>>
							<?php _e('Yes','users-control'); ?>
						</option>
						<option value="0" <?php selected(0, $allow_html); ?>>
							<?php _e('No','users-control'); ?>
						</option>
					</select> <i class="userscontrol-icon-question-sign userscontrol-tooltip2"
						title="<?php _e('Users can input HTML tags.','users-control'); ?>"></i>
				</p> 


				<?php if ($private != 1) { 
				     
					 if(!isset($can_hide))
						 $can_hide = '0';
					 ?>
				 <p>
					 <label for="userscontrol_<?php echo esc_attr($pos); ?>_can_hide"><?php _e('User can hide','users-control'); ?>
					 </label> <select name="userscontrol_<?php echo esc_attr($pos); ?>_can_hide"
						 id="userscontrol_<?php echo esc_attr($pos); ?>_can_hide">
						 <option value="1" <?php selected(1, $can_hide); ?>>
							 <?php _e('Yes','users-control'); ?>
						 </option>
						 <option value="0" <?php selected(0, $can_hide); ?>>
							 <?php _e('No','users-control'); ?>
						 </option>
					 </select> <i class="userscontrol-icon-question-sign userscontrol-tooltip2"
						 title="<?php _e('Allow users to hide this profile field from public viewing or not. Selecting No will cause the field to always be publicly visible if you have public viewing of profiles enabled. Selecting Yes will give the user a choice if the field should be publicly visible or not. Private fields are not affected by this option.','users-control'); ?>"></i>
				 </p> 
				 <?php } ?> 

				 <?php if ($field != 'password') { ?>
				<p>
					<label for="userscontrol_<?php echo esc_attr($pos); ?>_social"><?php _e('This field is social','users-control'); ?>
					</label> <select name="userscontrol_<?php echo esc_attr($pos); ?>_social"
						id="userscontrol_<?php echo esc_attr($pos); ?>_social">
						<option value="0" <?php selected(0, $social); ?>>
							<?php _e('No','users-control'); ?>
						</option>
						<option value="1" <?php selected(1, $social); ?>>
							<?php _e('Yes','users-control'); ?>
						</option>
					</select> <i class="userscontrol-icon-question-sign userscontrol-tooltip2"
						title="<?php _e('A social field can show a button with your social profile in the head of your profile. Such as Facebook page, Twitter, etc.','users-control'); ?>"></i>
				</p> 
				<?php } ?> 
				 
				 <?php 
				 if(!isset($private))
					 $private = '0';
				 ?>
				 <p>
					 <label for="userscontrol_<?php echo esc_attr($pos); ?>_private"><?php _e('This field is private','users-control'); ?>
					 </label> <select name="userscontrol_<?php echo esc_attr($pos); ?>_private"
						 id="userscontrol_<?php echo esc_attr($pos); ?>_private">
						 <option value="0" <?php selected(0, $private); ?>>
							 <?php _e('No','users-control'); ?>
						 </option>
						 <option value="1" <?php selected(1, $private); ?>>
							 <?php _e('Yes','users-control'); ?>
						 </option>
					 </select> <i class="userscontrol-icon-question-sign userscontrol-tooltip2"
						 title="<?php _e('Make this field Private. Only admins can see private fields.','users-control'); ?>"></i>
				 </p>
								
				
				
				<?php 
				if(!isset($required))
				    $required = '0';
				?>
				<p>
					<label for="userscontrol_<?php echo esc_attr($pos); ?>_required"><?php _e('This field is Required','users-control'); ?>
					</label> <select name="userscontrol_<?php echo esc_attr($pos); ?>_required"
						id="userscontrol_<?php echo esc_attr($pos); ?>_required">
						<option value="0" <?php selected(0, $required); ?>>
							<?php _e('No','users-control'); ?>
						</option>
						<option value="1" <?php selected(1, $required); ?>>
							<?php _e('Yes','users-control'); ?>
						</option>
					</select> <i class="userscontrol-icon-question-sign userscontrol-tooltip2"
						title="<?php _e('Selecting yes will force user to provide a value for this field at registration and edit profile. Registration or profile edits will not be accepted if this field is left empty.','users-control'); ?>"></i>
				</p> <?php } ?> <?php

				/* Show Registration field only when below condition fullfill
				1) Field is not private
				2) meta is not for email field
				3) field is not fileupload */
				if(!isset($private))
				    $private = 0;

				if(!isset($meta))
				    $meta = '';

				if(!isset($field))
				    $field = '';


				//if($type == 'separator' ||  ($private != 1 && $meta != 'user_email' ))
				if($type == 'separator' ||  ($private != 1 && $meta != 'user_email' ))
				{
				    if(!isset($show_in_register))
				        $show_in_register= 0;
						
					 if(!isset($show_in_widget))
				        $show_in_widget= 0;
				    ?>
				<p>
					<label for="userscontrol_<?php echo esc_attr($pos); ?>_show_in_register"><?php _e('Show on Registration Form','users-control'); ?>
					</label> <select name="userscontrol_<?php echo esc_attr($pos); ?>_show_in_register"
						id="userscontrol_<?php echo esc_attr($pos); ?>_show_in_register">
						<option value="0" <?php selected(0, $show_in_register); ?>>
							<?php _e('No','users-control'); ?>
						</option>
						<option value="1" <?php selected(1, $show_in_register); ?>>
							<?php _e('Yes','users-control'); ?>
						</option>
					</select> <i class="userscontrol-icon-question-sign userscontrol-tooltip2"
						title="<?php _e('Show this profile field on the registration form','users-control'); ?>"></i>
				</p>    
               
                
                 <?php } ?>
                 
			<?php if ($type != 'seperator' || $type != 'separator') { ?>

		  <?php if (in_array($field, array('select','radio','checkbox')))
				 {
				    $show_choices = null;
				} else { $show_choices = 'userscontrol-hide';
				
				
				} ?>

				<p class="userscontrol-choices <?php echo esc_attr($show_choices); ?>">
					<label for="userscontrol_<?php echo esc_attr($pos); ?>_choices"
						style="display: block"><?php _e('Available Choices','users-control'); ?> </label>
					<textarea name="userscontrol_<?php echo esc_attr($pos); ?>_choices" type="text" id="userscontrol_<?php echo esc_attr($pos); ?>_choices" class="large-text"><?php if (isset($array['choices'])) echo esc_attr(trim($choices)); ?></textarea>
                    
                    <?php
                    
					if($userscontrol->userscontrol_if_windows_server())
					{
						$txt = ' <p>'.__('<strong>PLEASE NOTE: </strong>Enter values separated by commas, example: 1,2,3. The choices will be available for front end user to choose from.').'</p>';
						echo wp_kses($txt, $userscontrol->allowed_html);				
					}else{
						$txt = ' <p>'.__('<strong>PLEASE NOTE: </strong>Enter one choice per line please. The choices will be available for front end user to choose from.').'</p>';
						echo wp_kses($txt, $userscontrol->allowed_html);						
					}
					
					?>
                    <p>
                    
                    
                    </p>
					<i class="userscontrol-icon-question-sign userscontrol-tooltip2"
						title="<?php _e('Enter one choice per line please. The choices will be available for front end user to choose from.','users-control'); ?>"></i>
				</p> <?php //if (!isset($array['predefined_loop'])) $predefined_loop = 0;
				
				if (!isset($predefined_options)) $predefined_options = 0;
				
				 ?>

				<p class="userscontrol_choices <?php echo esc_attr($show_choices); ?>">
					<label for="userscontrol_<?php echo esc_attr($pos); ?>_predefined_options" style="display: block"><?php _e('Enable Predefined Choices','users-control'); ?>
					</label> 
                    <select name="userscontrol_<?php echo esc_attr($pos); ?>_predefined_options"id="userscontrol_<?php echo esc_attr($pos); ?>_predefined_options">
						<option value="0" <?php selected(0, $predefined_options); ?>>
							<?php _e('None','users-control'); ?>
						</option>
						<option value="countries" <?php selected('countries', $predefined_options); ?>>
							<?php _e('List of Countries','users-control'); ?>
						</option>
                        
                        <option value="age" <?php selected('age', $predefined_options); ?>>
							<?php _e('Age','users-control'); ?>
						</option>
					</select> <i class="userscontrol-icon-question-sign userscontrol-tooltip2"
						title="<?php _e('You can enable a predefined filter for choices. e.g. List of countries It enables country selection in profiles and saves you time to do it on your own.','users-control'); ?>"></i>
				</p>

				
				<div class="clear"></div> 
				
				<?php } ?>

				<p>                
                
    			<label for="userscontrol_<?php echo esc_attr($pos); ?>_show_to_user_role"><?php _e('Display by User Role','users-control'); ?>
        		</label>
                
                <br />
        		<select name="userscontrol_<?php echo esc_attr($pos); ?>_show_to_user_role" id="userscontrol_<?php echo esc_attr($pos); ?>_show_to_user_role" class="userscontrol_show_to_user_role_edit" >
        				<option value="0" <?php selected(0, $show_to_user_role); ?>>
        					<?php _e('No','users-control'); ?>
        				</option>
        				<option value="1" <?php selected(1, $show_to_user_role); ?>>
        					<?php _e('Yes','users-control'); ?>
        				</option>
        		</select> <i class="userscontrol-icon-question-sign userscontrol-tooltip2"
        			title="<?php _e('If no, this field will be displayed on profiles of all User Roles. Select yes to display this field only on profiles of specific User Roles.','users-control'); ?>"></i>
        		
            
            	</p>
                
                 <p style="display:" id="userscontrol_<?php echo esc_attr($pos); ?>_show_to_user_role_list_div">
                 
                 
        		<label for="userscontrol_<?php echo esc_attr($pos); ?>_show_to_user_role_list"><?php _e('Select User Roles','users-control'); ?>
        		</label>
                  <br />
        		<?php 
        			  $roles = 	$userscontrol->role->get_available_user_roles();
					  
					  //get role list
					  
					  $curren_role_list = array();
        			  foreach($roles as $role_key => $role_display)
					  {
						  $curren_role_list = explode(",",$show_to_user_role_list);
						  
						  $checked_l = '';
						  
						  if(in_array($role_key,$curren_role_list)){
							   $checked_l = 'checked="checked"';
						  }
        		?>
        			  <input type='checkbox' name='userscontrol_<?php echo esc_attr($pos); ?>_show_to_user_role_list[]' id='userscontrol_<?php echo esc_attr($pos); ?>_show_to_user_role_list' value='<?php echo $role_key; ?>' class="userscontrol_<?php echo esc_attr($pos); ?>_show_roles_ids"  <?php echo  $checked_l; ?>/>
        			  <label class='userscontrol-role-name'><?php echo esc_attr($role_display); ?></label>
        		<?php
        			  }
        		?>
        		 <i class="userscontrol-icon-question-sign userscontrol-tooltip2"
        			title="<?php _e('This field will only be displayed on users of the selected User Roles.','users-control'); ?>"></i>
        		
                 
                  </p>
                  
                    <p >                
                
    			<label for="userscontrol_<?php echo esc_attr($pos); ?>_edit_by_user_role"><?php _e('Editable by Users of Role','users-control'); ?>
        		</label>
                
                  <br />
        		<select name="userscontrol_<?php echo esc_attr($pos); ?>_edit_by_user_role" id="userscontrol_<?php echo esc_attr($pos); ?>_edit_by_user_role" class="userscontrol_edit_by_user_role_edit">
        				<option value="0" <?php selected(0, $edit_by_user_role); ?>>
        					<?php _e('No','users-control'); ?>
        				</option>
        				<option value="1" <?php selected(1, $edit_by_user_role); ?>>
        					<?php _e('Yes','users-control'); ?>
        				</option>
        		</select> <i class="userscontrol-icon-question-sign userscontrol-tooltip2"
        			title="<?php _e('If yes, available user roles will be displayed for selection.','users-control'); ?>"></i>
        		
            
            	</p>
                
                
                <p style="display:" id="userscontrol_<?php echo esc_attr($pos); ?>_edit_by_user_role_list_div">
                 
                 
        		<label for="userscontrol_<?php echo esc_attr($pos); ?>_show_to_user_role_list"><?php _e('Select User Roles','users-control'); ?>
        		</label>
                
                  <br />
        		<?php 
        			  $roles = 	$userscontrol->role->get_available_user_roles('edit');
					  
					  //get role list
					  
					  $curren_role_list = array();
        			  foreach($roles as $role_key => $role_display)
					  {
						  $curren_role_list = explode(",",$edit_by_user_role_list);
						  
						  $checked_l = '';
						  
						  if(in_array($role_key,$curren_role_list)){
							   $checked_l = 'checked="checked"';
						  }
        		?>
        			  <input type='checkbox' name='userscontrol_<?php echo esc_attr($pos); ?>_edit_by_user_role_list[]' id='userscontrol_<?php echo esc_attr($pos); ?>_edit_by_user_role_list' value='<?php echo esc_attr($role_key); ?>' class="userscontrol_<?php echo esc_attr($pos); ?>_edit_roles_ids"  <?php echo  esc_attr($checked_l); ?>/>
        			  <label class='userscontrol-role-name'><?php echo esc_attr($role_display); ?></label>
        		<?php
        			  }
        		?>
        		 <i class="userscontrol-icon-question-sign userscontrol-tooltip2"
        			title="<?php _e('This field will only be displayed on users of the selected User Roles.','users-control'); ?>"></i>
        		
                 
                  </p>


				  <p>

					<span style="display: block; font-weight: bold; margin: 0 0 10px 0"><?php _e('Field Icon:','users-control'); ?>&nbsp;&nbsp;
						<?php if ($icon) { ?>
						
						<i class="fa fa-<?php echo $icon; ?>"></i>
						
						<?php } else { 
						
						_e('None','users-control'); 
						
						} ?>
						
						&nbsp;&nbsp; <a href="#changeicon"
						class="button button-secondary userscontrol-inline-icon-userscontrol-edit"><?php _e('Change Icon','users-control'); ?>
					</a> </span> <label class="userscontrol-icons">

					<input type="radio"	name="userscontrol_<?php echo $pos; ?>_icon" value=""
						<?php checked('', $fonticon); ?> /> <?php _e('None','users-control'); ?> </label>
						
						
						

					<?php 

					foreach($this->fontawesome as $fonticon) { 


					?>
					
					
					<label class="userscontrol-icons"><input type="radio"	name="userscontrol_<?php echo $pos; ?>_icon" value="<?php echo $fonticon; ?>"
						<?php checked($fonticon, $icon); ?> />

						<i class="fa fa-<?php echo $fonticon; ?> userscontrol-tooltip3"
						title="<?php echo $fonticon; ?>"></i> </label>
						
						
					<?php } //for each ?>



					</p>


  <div class="userscontrol-ultra-success userscontrol-notification" id="bup-sucess-fields-<?php echo esc_attr($pos); ?>"><?php _e('Success ','users-control'); ?></div>
				<p>
                
               
                 
				<input type="button" name="submit"	value="<?php _e('Update','users-control'); ?>"						class="button button-primary userscontrol-btn-submit-field"  data-edition="<?php echo esc_attr($pos); ?>" /> 
                   <input type="button" value="<?php _e('Cancel','users-control'); ?>"
						class="button button-secondary userscontrol-btn-close-edition-field" data-edition="<?php echo esc_attr($pos); ?>" />
				</p>
                
      <?php
	  
	  die();
		
	}
	
	public function create_standard_form_fields ($form_name )	
	{		
	
		/* These are the basic profile fields */
		$fields_array = array(
			80 => array( 
			  'position' => '50',
				'type' => 'separator', 
				'name' => __('Registration Info','users-control'),
				'private' => 0,
				'show_in_register' => 1,
				'deleted' => 0,
				'show_to_user_role' => 0
			),			
			
			170 => array( 
			  'position' => '200',
				'icon' => 'pencil',
				'field' => 'textarea',
				'type' => 'usermeta',
				'meta' => 'special_notes',
				'name' => __('Comments','users-control'),
				'can_hide' => 0,
				'can_edit' => 1,
				'show_in_register' => 1,
				'private' => 0,
				'social' => 0,
				'deleted' => 0,
				'allow_html' => 1,				
				'help_text' => ''
			
			)
		);
		
		/* Store default profile fields for the first time */
		if (!get_option($form_name))
		{
			if($form_name!="")
			{
				update_option($form_name,$fields_array);
			
			}
			
		}	
	}
	
	/*Loads all field list */	
	function reload_custom_fields_set (){
		
		global $userscontrol;
		
		// Check for nonce security      
		$this->validate_auth_access();
					
		$custom_form = sanitize_text_field($_POST["userscontrol_custom_form"]);	
		if($custom_form!=""){
			//check if fields have been added			
			$custom_form = 'userscontrol_profile_fields_'.$custom_form;
				
			if (!get_option($custom_form)){
				$this->create_standard_form_fields($custom_form);									
				$fields = get_option($custom_form);
					
			}else{
				$fields = get_option($custom_form);
			}
			
		}else{ //use the default registration from
				$fields = get_option('userscontrol_profile_fields');
		}
			
		ksort($fields);		
			
			$i = 0;
			foreach($fields as $pos => $array){
				extract($array); $i++;
	
				if(!isset($required))
					$required = 0;
	
				if(!isset($fonticon))
					$fonticon = '';
					
					
				if ($type == 'seperator' || $type == 'separator') {
				   
					$class = "separator";
					$class_title = "";
					$class_h3 = "";
				} else {
				  
					$class = "profile-field";
					$class_title = "profile-field";
					$class_h3 = "profile-h3";
				}
				?>
				
			  <li class="userscontrol-profile-fields-row <?php echo esc_attr($class_title)?>" id="<?php echo esc_attr($pos); ?>">
				
				
				<div class="heading_title  <?php echo esc_attr($class)?>">
				
				<h3 class="<?php echo esc_attr($class_h3)?>">
				<?php
	
				if ($type == 'separator'){	
					_e('<span class="userscontrol-field-separator"><i class="fa fa-list"></i> </span>');
				}
				
				if (isset($array['name']) && $array['name']){	
					echo  esc_attr($array['name']);
				}
				?>
				
				<?php
				if ($type == 'separator') {
					
					 _e(' - Separator','users-control');
					
				} else {				
				   
					
				}
				?>
				
				</h3>
				
				
				  <div class="options-bar">
				 
						  <p>             
						
						<a class="button button-secondary userscontrol-delete-profile-field-btn" data-field="<?php echo esc_attr($pos); ?>"><i class="fa fa-trash-o"></i> </a> 
						<a class="button userscontrol-btn-edit-field button-primary" data-edition="<?php echo esc_attr($pos); ?>"><i class="fa fa-edit fa-lg"></i> </a>
						</p>
				
				 </div>          
				
			  
	
				</div>
				
				 
				 <div class="userscontrol-ultra-success userscontrol-notification" id="userscontrol-sucess-delete-fields-<?php echo esc_attr($pos); ?>"><?php _e('Success! This field has been deleted ','users-control'); ?></div>
				
			   
			
			  <!-- edit field -->
			  
			  <div class="user-ultra-sect-second userscontrol-fields-edition user-ultra-rounded"  id="userscontrol-edit-fields-bock-<?php echo esc_attr($pos); ?>">
			
			  </div>
			  
			  
			  <!-- edit field end -->
	
		   </li>
	
	
	
	
	
	
	
		<?php
		
		}
			
			die();
			
		
	}
		
	// update settings
    function update_settings(){
		global  $userscontrol;
		foreach($_POST as $key => $value) {
            if ($key != 'submit'){
				if (strpos($key, 'html_') !== false){
                }else{
					
                }                                   
                
				$this->userscontrol_set_option($key, $value) ; 
				if($key=="userscontrol_my_account_page"){						
					update_option('userscontrol_my_account_page',$value);				 
				}  
            }
        }		

		if ( isset ( $_GET['tab'] ) ){
			$current = sanitize_text_field($_GET['tab']);
         }else{
            $current =  sanitize_text_field($_GET['page']);
         }	 
            
		$special_with_check = $this->get_special_checks($current);
         
        foreach($special_with_check as $key) {           
            if(!isset($_POST[$key])){			
                $value= '0';
			}else{
				$value= sanitize_text_field($_POST[$key]);
			}	 	
			$this->userscontrol_set_option($key, $value) ;  
        }
         
      $this->options = get_option('userscontrol_options');
	  $message = '<div class="updated"><p><strong>'.__('Settings saved.','users-control').'</strong></p></div>';
	  echo wp_kses($message, $userscontrol->allowed_html);
    }
	
	public function get_special_checks($tab) 	{
		$special_with_check = array();
		
		if($tab=="settings")
		{				
		
		 $special_with_check = array( 'userscontrol_loggedin_activated', 'private_message_system','redirect_backend_profile','redirect_backend_registration', 'redirect_registration_when_social','redirect_backend_login', 'social_media_fb_active',  'social_media_google', 'twitter_connect',  'mailchimp_active', 'mailchimp_auto_checked',  'aweber_active', 'aweber_auto_checked','recaptcha_display_registration', 'recaptcha_display_loginform' ,'recaptcha_display_ticketform','recaptcha_display_forgot_password');
		 
		}elseif($tab=="gateway"){
			
			 $special_with_check = array('gateway_paypal_active', 'gateway_bank_active', 'gateway_stripe_active', 'gateway_stripe_success_active' ,'gateway_bank_success_active', 'gateway_free_success_active',  'gateway_paypal_success_active' ,  'appointment_cancellation_active');
		
		}elseif($tab=="mail"){
			
			 $special_with_check = array('userscontrol_smtp_mailing_return_path', 'userscontrol_smtp_mailing_html_txt');
		 
		
		
		}
		
		if($tab=="userscontrol-passwordstrength"){				
		
			 $special_with_check = array('registration_password_ask','registration_password_ask_confirmation', 'registration_password_lenght','registration_password_1_letter_1_number' ,'registration_password_one_uppercase','registration_password_one_lowercase');		
		 
		}
	
	return  $special_with_check ;
	
	}	
	
	public function do_valid_checks(){
		
		global $userscontrol_activation ;		
		$va = get_option('userscontrol_c_key');
		
		if(isset($userscontrol_activation))		
		{		
			if($va=="")
			{
				//
				//$this->valid_c = "no";
			
			}
		
		}	
	
	}

	public function is_pro_version(){		
		global $userscontrol_activation ;		
		$va = get_option('userscontrol_c_key');
		$res = false;
		
		if(isset($userscontrol_activation))	{		
			$res = true;
		}else{
			$res = false;
		}
		return $res ;
	}

	public function is_pro_version_active(){		
		global $userscontrol_activation ;		
		$va = get_option('userscontrol_c_key');
		$res = false;
		
		if(isset($userscontrol_activation) && $va!='')	{		
			$res = true;
		}else{
			$res = false;
		}
		return $res ;
	}

	
	function initial_setup() {		
		global $userscontrol, $wpdb, $userscontrolcomplement ;		
		$inisetup   = get_option('userscontrol_ini_setup');
		
		if (!$inisetup) {					
			update_option('userscontrol_ini_setup', true);
		}		
	}
	
	function include_tab_content() {		
		global $userscontrol, $wpdb, $userscontrolcomplement ;
		
		$screen = get_current_screen();
		
		if( strstr($screen->id, $this->slug ) ) {
			if ( isset ( $_GET['tab'] ) ) {
				$tab = sanitize_text_field($_GET['tab']);
				
			} else {
				
				$tab = $this->default_tab;
			}
			

			if (! get_option('userscontrol_ini_setup')) {
				//this is the first time
				$this->initial_setup();
				
				$tab = "welcome";				
				require_once (userscontrol_path.'admin/tabs/'.$tab.'.php');				
				
				
			}else{
			
				if($this->valid_c=="" )	{
					require_once (userscontrol_path.'admin/tabs/'.$tab.'.php');			
				
				}else{ //no validated
					
					$tab = "licence";				
					require_once (userscontrol_path.'admin/tabs/'.$tab.'.php');
					
				}
			
			}
			
			
		}
	}
	
	function reset_email_template() {
		global  $userscontrol;
		
		// Check for nonce security      
		$this->validate_auth_access();

		$template = sanitize_text_field($_POST['email_template']);
		$new_template = $this->get_email_template($template);

		echo "new email: " .$new_template;
		$this->userscontrol_set_option($template, $new_template);
		die();
	}
	
	function get_sites_drop_down_admin($department_id = null){
		global  $userscontrol;
		
		$html = '';
		
		$site_rows = $userscontrol->site->get_all();		
		
		$html .= '<select name="userscontrol__custom_registration_form" id="userscontrol__custom_registration_form">';
		$html .= '<option value="" selected="selected">'.__('Select a Department','users-control').'</option>';
		
		foreach ( $site_rows as $site )	{		
			
			$html .= '<optgroup label="'.$site->site_name.'" >';
			
			//get services						
			$deptos_rows = $userscontrol->department->get_all_departments($site->site_id);
			foreach ( $deptos_rows as $depto ){
				$selected = '';
				if($depto->department_id==$service_id){$selected = 'selected';}
				$html .= '<option value="'.$depto->department_id.'" '.$selected.' >'.$depto->department_name.'</option>';
			}
			
			$html .= '</optgroup>';
		}
		$html .= '</select>';
		return $html;
	}

	public function only_pro_users_message($message){

		$html = '<div class="userscontrol-card userscontrol-banner has-call-to-action is-upgrade-premium ">';
		
		   $html .= '<div class="userscontrol-banner__content">';

		 	  $html .= '<div class="userscontrol-banner__info">';

		   		 $html .= '<div class="userscontrol-banner__title">'.__('Empower your Membership Plugin','users-control').'</div>';
		  		 $html .= ' <div class="userscontrol-banner__description">'.$message.'</div>';
		  
			 $html .= ' </div>';

		   $html .= '</div>';

		   $html .= '<div class="userscontrol-banner__buttons_container">';

		 	  $html .='<div class="userscontrol-banner__action" id="userscontrol-banner__activate">
			  <a href="https://userscontrol.com/pricing/"  type="button" class="userscontrol-button is-compact is-primary "  > '.__('GO PRO NOW!','users-control').'</a></div>';
		   $html .= '</div>';

		$html .= '</div>'; //end content

		if($this->is_pro_version_active()){
			$html = '';
		}

		return $html;



	}
	
	function admin_page() {
		global $userscontrol;

		$va = get_option('userscontrol_c_key');
		$tab = '';

		if (isset($_POST['userscontrol_update_settings']) &&  $_POST['userscontrol_reset_email_template']==''){
            $this->update_settings();
        }
		
		if (isset($_POST['userscontrol_update_settings']) && $_POST['userscontrol_reset_email_template']=='yes' && $_POST['userscontrol_email_template']!='') {
			$message =  '<div class="updated"><p><strong>'.__('Email Template has been restored.','users-control').'</strong></p></div>';
			echo wp_kses($message, $userscontrol->allowed_html);
		}
		
		if (isset($_POST['update_userscontrol_slugs']) && $_POST['update_userscontrol_slugs']=='userscontrol_slugs'){
           $userscontrol->create_rewrite_rules();
           flush_rewrite_rules();
		   $message = '<div class="updated"><p><strong>'.__('Rewrite Rules were Saved.','users-control').'</strong></p></div>';
		   echo wp_kses($message, $userscontrol->allowed_html);
        }

		if($this->is_pro_version()){
			$va = get_option('userscontrol_c_key');
			$expiration_date = get_option('userscontrol_c_expiration');
			$activate_text = '';
			if($va=='' || $expiration_date==''){
				$activate_text= ' (<span class="userscontrol-activatecopywarning"><a href="?page=userscontrol&tab=licence">'.__('Activate your plugin','users-control').'</a></span>)';
			}
			$ver  = __('PRO Version','users-control').$activate_text;
			$cl_ver  = 'prover';
		}else{
			$ver  = __('LITE Version','users-control');
			$cl_ver  = 'litever';
		}
        
         
           		
	?>

<div class="wrap <?php echo esc_attr($this->slug); ?>-admin"> 

<?php if($tab !='welcome' && $tab !='pro'){ ?>            
	
	<div class="wrap userscontrol-top-main-bar">
		<div class="userscontrol-top-main-texts">
		<div class="userscontrol-top-main-plugin-name">

			<?php
			$urlText = __('USERS CONTROL','users-control');
			_e('<a href="?page=userscontrol">'.$urlText.'</a>');?>

			<div class="<?php echo esc_attr($cl_ver);?>"><?php echo ($ver);?></div>
			
		</div>			
			<ul>
				<li>
					<a href="?page=userscontrol"><i class="fa fa-home fa-2x"></i><p><?php _e('DASHBOARD','users-control');?></p></a>
				</li>	

				<li>   
					<a href="?page=userscontrol&tab=users"><i class="fa fa-users fa-2x"></i><p><?php _e('MEMBERS','users-control');?></p></a>
				</li>
				
				<li>   
					<a href="?page=userscontrol&tab=orders"><i class="fa fa-list fa-2x"></i><p><?php _e('ORDERS','users-control');?></p></a>
				</li>

				<li class="pro">   
					<a href="https://userscontrol.com/pricing/"><i class="fa fa-unlock fa-2x pro"></i><p><?php _e('GO PRO','users-control');?></p></a>
				</li>
			</ul>
		</div>
	</div>

	<?php  }?>
	

		
</div>


               

           
	
		<div class="wrap <?php echo esc_attr($this->slug); ?>-admin"> 
            
          
            
            
            
        

			<div class="<?php echo esc_attr($this->slug); ?>-admin-contain">    
            
				<?php 		
				
					$this->include_tab_content(); 
				?>
				
				<div class="clear"></div>
                
                
				
			</div>
            
            <div class="clear"><?php
			
			$link = "<a href='https://wordpress.org/support/plugin/users-control/reviews/?filter=5' target='_blank'> 5 stars </a>";
			printf(__("If you like <strong>Users Control<strong> please consider leaving us a %s rating. A huge thank you from the Users Control Team in advanced.",'users-control'), $link)?></div>
			
		</div>

	<?php }

}

$key = "admin";
$this->{$key} = new UserscontrolAdmin();