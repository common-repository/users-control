<?php
class Userscontrol {
	public $classes_array = array();
	public $membership_status_options = array();	
	public $registration_fields;
	public $login_fields;
	public $fields;
	public $allowed_inputs;
	public $allowed_html;
	public $use_captcha = "no";
	
	var $ajax_p = 'userscontrol';
	var $table_prefix = 'userscontrol';
	var $upload_folder_temp = 'userscontrol_temp_files';

		
	public function __construct(){		
		$this->logged_in_user = 0;
		$this->login_code_count = 0;
		$this->current_page = sanitize_url($_SERVER['REQUEST_URI']);
		$this->set_membership_status();
		$this->set_allowed_html();
		$this->set_default_user_nav();
    }
	
	public function plugin_init(){	
		
		/*Load Amin Classes*/		
		if (is_admin()) {
			$this->set_admin_classes();
			$this->load_classes();					
		
		}else{
			
			/*Load Main classes*/
			$this->set_main_classes();
			$this->load_classes();
		}
		//ini settings
		$this->intial_settings();
	}

	function set_default_user_nav(){
		$this->nav_user_items = array(			
			'about' =>array('label'=> __('About','users-control'), 'icon'=>'fa-user') ,
			'posts' =>array('label'=> __('Posts','users-control'), 'icon'=>'fa-edit') ,
			'albums' =>array('label'=> __('Photos','users-control'), 'icon'=>'fa-photo') ,
		);

		if(!get_option('userscontrol_profile_nav')){			
			update_option('userscontrol_profile_nav', $this->nav_user_items);
		}
	}

	public function set_allowed_html(){
		global $allowedposttags;

		$allowed_html = wp_kses_allowed_html( 'post' );

		$allowed_html['select'] = array(
			'name' => array(),
			'id' => array(),
			'class' => array(),
			'style' => array()
		);

		$allowed_html['option'] = array(
			'name' => array(),
			'id' => array(),
			'class' => array(),
			'value' => array(),
			'selected' => array(),
			'style' => array()
		);

		$allowed_html['input'] = array(
			'name' => true,
			'id' => true,
			'class' => true,
			'value' => true,
			'selected' => true,
			'style' =>true
		);

		$allowed_html['table'] = array(
			'name' => true,
			'id' => true,
			'class' => true,			
			'style' => true
		);

		$allowed_html['td'] = array(
			'name' =>true,
			'id' => true,
			'class' => true,
			'style' => true
		);

		$allowed_html['tr'] = array(
			'name' => array(),
			'id' => array(),
			'class' => array(),
			
		);

		$allowed_atts = array(
			'align'      => array(),
			'span'      => array(),
			'checked'      => array(),
			'class'      => array(),
			'selected'      => array(),
			'type'       => array(),
			'id'         => array(),
			'dir'        => array(),
			'lang'       => array(),
			'style'      => array(),
			'display'      => array(),
			'xml:lang'   => array(),
			'src'        => array(),
			'alt'        => array(),
			'href'       => array(),
			'hidden'       => array(),
			'rel'        => array(),
			'rev'        => array(),
			'target'     => array(),
			'novalidate' => array(),
			'type'       => array(),
			'value'      => array(),
			'name'       => array(),
			'tabindex'   => array(),
			'action'     => array(),
			'method'     => array(),
			'for'        => array(),
			'width'      => array(),
			'height'     => array(),
			'data'       => array(),
			'title'      => array(),
			'userscontrol-data-date'      => array(),
			'userscontrol-data-timeslot'      => array(),
			'userscontrol-data-service-staff'      => array(),
			'userscontrol-max-capacity'      => array(),
			'userscontrol-max-available'      => array(),
			'data-nuve-rand-id'      => array(),
			'data-nuve-rand-key'      => array(),
			'data-location'      => array(),
			'data-cate-id'      => array(),
			'data-category-id'      => array(),
			'data-staff-id'      => array(),
			'data-staff_id'      => array(),
			'data-id'      => array(),
			'appointment-id'      => array(),
			'message-id'      => array(),			
			'>'      => array(),
			'userscontrol-staff-id'      => array(),				
			'service-id'      => array(),			
			'staff-id'      => array(),	
			'user-id'      => array(),	
			'staff_id'      => array(),		
			'widget-id'      => array(),
			'day-id'      => array(),
			'break-id'      => array(),	
			'category-id'      => array(),			
			'/option'      => array(),
			'label'      => array(),


			
		);



		$allowedposttags['button']     = $allowed_atts;
		$allowedposttags['form']     = $allowed_atts;
		$allowedposttags['label']    = $allowed_atts;
		$allowedposttags['input']    = $allowed_atts;
		$allowedposttags['hidden']    = $allowed_atts;
		$allowedposttags['textarea'] = $allowed_atts;
		$allowedposttags['iframe']   = $allowed_atts;
		$allowedposttags['script']   = $allowed_atts;
		$allowedposttags['style']    = $allowed_atts;
		$allowedposttags['display']    = $allowed_atts;	
		$allowedposttags['select']    = $allowed_atts;
		$allowedposttags['option']    = $allowed_atts;
		$allowedposttags['optgroup']    = $allowed_atts;
		$allowedposttags['strong']   = $allowed_atts;
		$allowedposttags['small']    = $allowed_atts;
		$allowedposttags['table']    = $allowed_atts;
		$allowedposttags['span']     = $allowed_atts;
		$allowedposttags['abbr']     = $allowed_atts;
		$allowedposttags['code']     = $allowed_atts;
		$allowedposttags['pre']      = $allowed_atts;
		$allowedposttags['div']      = $allowed_atts;
		$allowedposttags['img']      = $allowed_atts;
		$allowedposttags['h1']       = $allowed_atts;
		$allowedposttags['h2']       = $allowed_atts;
		$allowedposttags['h3']       = $allowed_atts;
		$allowedposttags['h4']       = $allowed_atts;
		$allowedposttags['h5']       = $allowed_atts;
		$allowedposttags['h6']       = $allowed_atts;
		$allowedposttags['ol']       = $allowed_atts;
		$allowedposttags['ul']       = $allowed_atts;
		$allowedposttags['li']       = $allowed_atts;
		$allowedposttags['em']       = $allowed_atts;
		$allowedposttags['hr']       = $allowed_atts;
		$allowedposttags['br']       = $allowed_atts;
		$allowedposttags['tr']       = $allowed_atts;
		$allowedposttags['td']       = $allowed_atts;
		$allowedposttags['p']        = $allowed_atts;
		$allowedposttags['a']        = $allowed_atts;
		$allowedposttags['b']        = $allowed_atts;
		$allowedposttags['i']        = $allowed_atts;
		$allowedposttags['>']        = $allowed_atts;

		$this->allowed_html = $allowedposttags;

	}
	
	
	
	
	
	
	public function intial_settings(){							
			 			 
		$this->include_for_validation = array('text','fileupload','textarea','select','radio','checkbox','password');
			
		add_action('wp_enqueue_scripts', array(&$this, 'add_front_end_styles'), 10); 
		add_action('admin_enqueue_scripts', array(&$this, 'add_styles_scripts'), 9);
		
		/*Create a generic profile page*/
		add_action( 'init', array(&$this, 'activate_profile_module'), 9);

		
		/* Remove bar except for admins */
		add_action('init', array(&$this, 'remove_admin_bar'), 9);	
		
		/* Create Standar Fields */		
		add_action('init', array(&$this, 'create_standard_fields'));
		add_action('admin_init', array(&$this, 'create_standard_fields'));	
		
					
		/*Setup redirection*/
				
		add_action( 'wp_ajax_'.$this->ajax_p.'_front_upload_files', array( &$this, 'front_upload_files' ));
		add_action( 'wp_ajax_nopriv_'.$this->ajax_p.'_front_upload_files', array( &$this, 'front_upload_files' ));
				
		
	}
	
 
	public function set_main_classes(){
		  $this->classes_array = array( "commmonmethods" =>"usercontrol.common" ,
		  "role" =>"usercontrol.role", 				  
		  "order" =>"usercontrol.order",  
		  "membership" =>"usercontrol.membership", 
		  "user" =>"usercontrol.user", 
		  "imagecrop" =>"usercontrol.cropimage",
		  "messaging" =>"usercontrol.messaging", 			
		  "shortcode" =>"usercontrol.shorcodes",
		  "profile" =>"usercontrol.profile" ,
		  "postprotection" =>"usercontrol.postprotection"
		  
		   ); 
	
	}
	
	public function set_admin_classes()	{
				 
		 $this->classes_array = array( "commmonmethods" =>"usercontrol.common" ,
		  "role" =>"usercontrol.role", 			
		  "order" =>"usercontrol.order", 		
		  "membership" =>"usercontrol.membership", 
		  "user" =>"usercontrol.user", 
		   "imagecrop" =>"usercontrol.cropimage",
		  "messaging" =>"usercontrol.messaging",			
		  "shortcode" =>"usercontrol.shorcodes",
		  "profile" =>"usercontrol.profile" ,
		  "admin" =>"usercontrol.admin"	,	
		  "postprotection" =>"usercontrol.postprotection"	
		  
		   ); 	
		 
		
	}
	
	
	
	public function get_date_format_conversion()
    {
		
		$date_format = $this->get_option('date_picker_format');
		
		if($date_format==''){
			
			$date_format = 'm/d/Y';
			
		}
        return $date_format;
    }
	
	public function set_membership_status ()	
	{
		
		//status   0 - pending
		//         1 - active
		//         2 - cancelled
		//         3 - recurring payment failed
		//         4 - recurring agreement expired
		
		
		/* Core login fields */
		$membership_status_options = array( 
			0 => array( 
				'value' => 0, 
				'text' => __('Pending','users-control') 
				
			),
			1=> array( 
				'value' =>1, 
				'text' => __('Active','users-control') 
				
			),
			2 => array( 
				'value' =>2, 
				'text' => __('Cancelled','users-control') 
				
			),
			3 => array( 
				'value' =>3, 
				'text' => __('Recurring Faild','users-control') 
				
			),
			4 => array( 
				'value' =>4, 
				'text' => __('Recurring Expired','users-control') 
				
			)
		);
		
		$this->membership_status_options = $membership_status_options;
		
		
	}
	
	public  function get_int_date_format( )
    {
		global  $userscontrol;
		
		$date_format = $this->get_option('usercontrol_date_admin_format');
		
		if($date_format==''){			
			
			$date_format = 'm/d/Y';					
		}
        return $date_format;
		
	
	}
	
	// Modified version of the timezone list function from http://stackoverflow.com/a/17355238/507629
	// Includes current time for each timezone (would help users who don't know what their timezone is)

	function generate_timezone_list() 
	{
		static $regions = array(
			DateTimeZone::AFRICA,
			DateTimeZone::AMERICA,
			DateTimeZone::ANTARCTICA,
			DateTimeZone::ASIA,
			DateTimeZone::ATLANTIC,
			DateTimeZone::AUSTRALIA,
			DateTimeZone::EUROPE,
			DateTimeZone::INDIAN,
			DateTimeZone::PACIFIC,
		);
	
		$timezones = array();
		foreach( $regions as $region )
		{
			$timezones = array_merge( $timezones, DateTimeZone::listIdentifiers( $region ) );
		}
	
		$timezone_offsets = array();
		foreach( $timezones as $timezone )
		{
			$tz = new DateTimeZone($timezone);
			$timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
		}
	
		// sort timezone by timezone name
		ksort($timezone_offsets);
	
		$timezone_list = array();
		foreach( $timezone_offsets as $timezone => $offset )
		{
			$offset_prefix = $offset < 0 ? '-' : '+';
			$offset_formatted = gmdate( 'H:i', abs($offset) );
	
			$pretty_offset = "UTC${offset_prefix}${offset_formatted}";
			
			$t = new DateTimeZone($timezone);
			$c = new DateTime(null, $t);
			$current_time = $c->format('g:i A');
	
			$timezone_list[$timezone] = "(${pretty_offset}) $timezone - $current_time";
		}
	
		return $timezone_list;
	}
	
	function nicetime($date)
	{
		if(empty($date)) {
			return "No date provided";
				}
	   
		$periods         = array(__("second", 'users-control'), 
							     __("minute", 'users-control'), 
								 __("hour", 'users-control'), 
								 __("day", 'users-control'), 
								 __("week", 'users-control'), 
								 __("month", 'users-control'), 
								 __("year", 'users-control'), 
								 __("decade", 'users-control'));
		$lengths         = array("60","60","24","7","4.35","12","10");
	   
		$now             = time();
		$now =  current_time( 'timestamp', 0 );
		$unix_date         = strtotime($date);
		
		
	   
		   // check validity of date
		if(empty($unix_date)) {   
			return "Bad date";
		}
	
		// is it future date or past date
		if($now > $unix_date) {   
			$difference     = $now - $unix_date;
			$tense         =  __("ago", 'users-control');
		   
		} else {
			$difference     = $unix_date - $now;
			$tense         =  __("from now", 'users-control');
		}
	   
		for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
			$difference /= $lengths[$j];
		}
	   
		$difference = round($difference);
	   
		if($difference != 1) 
		{
			$periods[$j].= "s";
		}
	   
		return "$difference $periods[$j] {$tense}";
	}
	
		
	
	function get_time_format()
	{
		global  $userscontrol;
	
		$data = $this->get_option('usercontrol_time_format');
		
		if($data=='')
		{
			$data = 'h:i A';
		
		}
		
		return $data;
	}
	
	function isWeekend($date) 
	{
		$weekDay = date('w', strtotime($date));
		return ($weekDay == 0 || $weekDay == 6);
	}	
	
	function get_date_to_display()
	{
		global  $userscontrol;
		
		$ret = '';
	
		$time_format = $this->get_option('usercontrol_time_format');
		
		if($time_format=='')
		{
			$time_format = 'h:i A';
		}
		
		$date_format = $this->get_option('usercontrol_date_admin_format');
		
		if($date_format==''){			
			
			$date_format = 'm/d/Y';					
		}
		
		$ret = $date_format.' '.$time_format;
		
		return $ret;
	}	
	
	public function get_user_meta($user_id, $meta) {
		$data = get_user_meta($user_id, $meta, true);
		return $data;
	}
	
	public function cut_string($txt, $length) 
	{
		$txt =mb_strimwidth($txt, 0,$length, "...");
		$txt = strtolower($txt);
		$txt = ucwords($txt);
				
		return $txt;
	}
	
	public function format_subject_string($txt) 	{
		$txt = strtolower($txt);
		$txt = ucwords($txt);
		return $txt;
	}
	
	function text_message_formatting($content){
		global $userscontrol;
		$target = 'target="_blank"';			
		$c =  preg_replace('!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;//=]+)!i', '<a href="$1" rel="nofollow" '.$target.' >$1</a>', $content);
		$content =  $c ;	
		 return $content;
	 }
	
	
	
	
	
	
	
	
	
	public function activate_profile_module (){
		$this->create_initial_pages();
	}
	
		
	public  function get_date_picker_format( )
    {
		global  $userscontrol;
		
		$date_format = $this->get_option('date_picker_format');
		
		if($date_format=='d/m/Y'){			
			
			$date_format = 'dd/mm/yy';
			
		}elseif($date_format=='m/d/Y'){
			
			$date_format = 'mm/dd/yy';			
			
		}else{
			
			$date_format = 'mm/dd/yy';
			
		}
        return $date_format;
		
	
	}
	
	public  function get_date_picker_date( )
    {
		global  $userscontrol;
		
		$date_format = $userscontrol->get_option('userscontrol_date_picker_format');
		
		if($date_format==''){			
			
			$date_format = 'm/d/Y';					
		}
        return $date_format;
		
	
	}
	
	public function get_login_page_url($with_a_tag=false)
    {
		global $userscontrol, $wp_rewrite ;
		
		$wp_rewrite = new WP_Rewrite();		
		
		$account_page_id = $userscontrol->get_option('usercontrol_user_login_page');		
		$my_account_url = get_permalink($account_page_id);
		
		if($with_a_tag)
		{
			$my_account_url = '<a href="'.$my_account_url.'" target="_blank">'.$my_account_url.'</a>';
			
		}
		
		return $my_account_url;
	
	}
	
	public function create_initial_pages ()
	{
		global $userscontrol;
		
		$fresh_page_creation  = get_option( 'userscontrol_auto_page_creation' );			
		$profile_page_id = $this->get_option('profile_page_id');
		
		if($profile_page_id!='' && is_page($profile_page_id))		
		{
			$profile_page = get_post($profile_page_id);
			$slug =  $profile_page->post_name;
			
			if($fresh_page_creation==1) //user wants to recreate pages
			{						
				 //pages created
				 update_option('userscontrol_auto_page_creation',0);			 
				
				add_rewrite_rule("$slug/([^/]+)/?",'index.php?page_id='.$profile_page_id.'&userscontrol_username=$matches[1]', 'top');		
				//this rules is for displaying the user's profiles
				add_rewrite_rule("([^/]+)/$slug/([^/]+)/?",'index.php?page_id='.$profile_page_id.'&userscontrol_username=$matches[2]', 'top');
				
				flush_rewrite_rules(false);
			
			 }else{			
					
				add_rewrite_rule("$slug/([^/]+)/?",'index.php?page_id='.$profile_page_id.'&userscontrol_username=$matches[1]', 'top');		
				//this rules is for displaying the user's profiles
				add_rewrite_rule("([^/]+)/$slug/([^/]+)/?",'index.php?page_id='.$profile_page_id.'&userscontrol_username=$matches[2]', 'top');
			
			}
		
		}
			
		/* Setup query variables */
		 add_filter( 'query_vars',   array(&$this, 'userscontrol_uid_query_var') );				
			
	}
	
	public function userscontrol_uid_query_var( $query_vars )
	{
		$query_vars[] = 'userscontrol_username';
		//$query_vars[] = 'searchuser';
		return $query_vars;
	}
	
	public function create_rewrite_rules() {
		global  $userscontrol;
		
		//$slug = $bookingultrapro->get_option("usercontrol_slug"); // Profile Slug
		$profile_page_id = $this->get_option('profile_page_id');
		$profile_page = get_post($profile_page_id);
		$slug =  $profile_page->post_name;
		
		add_rewrite_rule("$slug/([^/]+)/?",'index.php?page_id='.$profile_page_id.'&userscontrol_username=$matches[1]', 'top');		
		//this rules is for displaying the user's profiles
		add_rewrite_rule("([^/]+)/$slug/([^/]+)/?",'index.php?page_id='.$profile_page_id.'&userscontrol_username=$matches[2]', 'top');
		
		flush_rewrite_rules(false);
	
	
	}

	
	
	/******************************************
	Check if user exists by ID
	******************************************/
	function user_exists( $user_id ) 
	{
		$aux = get_userdata( $user_id );
		if($aux==false){
			return false;
		}
		return true;
	}
	
	
		
	
	
	
	public function create_default_pages_auto () 
	{
		update_option('userscontrol_auto_page_creation',1);
		
	}
	
	
	//display message
	public function userscontrol_fresh_install_message ($message) {
		global $userscontrol;
		if ($errormsg) 	{
			$message= '<div id="message" class="error">';
			echo wp_kses($message, $userscontrol->allowed_html);
			
		}else{
			$message= '<div id="message" class="updated fade">';
			echo wp_kses($message, $userscontrol->allowed_html);
		}
		$message=  "<p><strong>$message</strong></p></div>";
		echo wp_kses($message, $userscontrol->allowed_html);
	}
	
	public function fetch_result($results){
		if ( empty( $results ) )
		{
		
		
		}else{
			
			
			foreach ( $results as $result )
			{
				return $result;			
			
			}
			
		}
		
	}
	

	function remove_admin_bar() 
	{
		if (!current_user_can('manage_options') && !is_admin())
		{
			
			if ($this->get_option('hide_admin_bar')==1) 
			{
				
				show_admin_bar(false);
			}
		}
	}
	
	function convert_date($date) 
	{
		
		$custom_date_format = $this->get_option('date_format');
			
		if ($custom_date_format) 
		{
			$date = date($custom_date_format, strtotime($date));
		}
		
		
		return $date;
	}
	
	
	public function get_formated_agreement($subscription) 
	{
		
		$html = '';
		
		if( $subscription->membership_type=='recurring'){
					
			$html =  $this->get_formated_amount_with_currency($subscription->membership_subscription_amount).' /' .__(' every ','users-control').$subscription->membership_every.' '. $this->get_friendly_period($subscription->membership_time_period, $subscription->membership_every) ;
				
		}else{
			
			$html =  $this->get_formated_amount_with_currency($subscription->membership_initial_amount). ' /' .__(' one-time ','users-control');
			
		}		
		
		return $html;
		
	
	}
	
	public function get_friendly_period($period, $quantity) 
	{
		
		if($period=='Y'){			
			
			$period=  __('year','users-control');
			
		}elseif($period=='M'){	
		
			$period=  __('month','users-control');
		
		}elseif($period=='W'){	
		
			$period=  __('week','users-control');
		
		}elseif($period=='D'){	
		
			$period=  __('day','users-control');		
		
		}
		
		if($quantity>1){$period=$period.'s';	}
		
		
		return $period;
		
	}
	
	public function get_formated_amount_with_currency($amount) 
	{		
		
		$currency_symbol_before_after = $this->get_option('currency_symbol_before_after');
		
		//after symbol
		if($currency_symbol_before_after=='1'){
			
			$amount = $amount.$this->get_currency_symbol();		
		
		}else{
			
			$amount = $this->get_currency_symbol().$amount;			
		
		}
		
		return $amount;
		
	
	}
		
		
	
	public function get_currency_symbol() 
	{
		
		$currency_symbol = $this->get_option('currency_symbol');
			
		if ($currency_symbol=='') 
		{
			$currency_symbol = '$';
		}
		
		
		return $currency_symbol;
	}
	
	
	
	public function get_logout_url ()
	{
		
		$redirect_to = $this->current_page;
			
		return wp_logout_url($redirect_to);
	}
	
	
	public function custom_logout_page ($atts)
	{
		global $xoouserultra, $wp_rewrite ;
		
		$wp_rewrite = new WP_Rewrite();		
		
		extract( shortcode_atts( array(	
			
			'redirect_to' => '', 		
							
			
		), $atts ) );
		
		
		
		//check redir		
		$account_page_id = get_option('usercontrol_my_account_page');
		$my_account_url = get_permalink($account_page_id);
		
		if($redirect_to=="")
		{
				$redirect_to =$my_account_url;
		
		}
		$logout_url = wp_logout_url($redirect_to);
		
		//quick patch =
		
		$logout_url = str_replace("amp;","",$logout_url);
	
		wp_redirect($logout_url);
		exit;
		
	}
	
	public function get_redirection_link ($module)
	{
		$url ="";
		
		if($module=="profile")
		{
			//get profile url
			$url = $this->get_option('profile_page_id');			
		
		}
		
		return $url;
		
	}
	
		
		
	
			
	
	/*Create login page */
	public function create_login_page() 
	{
		
	}
	
	/*Create register page */
	public function create_register_page() 
	{
		
	}
	
		
		
	public function userscontrol_set_option($option, $newvalue)
	{
		$settings = get_option('userscontrol_options');
		$settings[$option] = $newvalue;
		update_option('userscontrol_options', $settings);
	}
	
	
	public function get_fname_by_userid($user_id) 
	{
		$f_name = get_user_meta($user_id, 'first_name', true);
		$l_name = get_user_meta($user_id, 'last_name', true);
		
		$f_name = str_replace(' ', '_', $f_name);
		$l_name = str_replace(' ', '_', $l_name);
		$name = $f_name . '-' . $l_name;
		return $name;
	}
	
	public function userscontrol_get_user_meta($user_id, $meta) 
	{
		$data = get_user_meta($user_id, $meta, true);
		
		return $data;
	}
	
	public function get_priority_options_drop_down ()	
	{
		
	}
	
	
	public function create_standard_fields ()	
	{
		
		/* Allowed input types */
		$this->allowed_inputs = array(
			'text' => __('Text','users-control'),			
			'textarea' => __('Textarea','users-control'),
			'select' => __('Select Dropdown','users-control'),
			'radio' => __('Radio','users-control'),
			'checkbox' => __('Checkbox','users-control'),			
		    'datetime' => __('Date Picker','users-control')
		);
		
		/* Core registration fields */
		$set_pass = $this->get_option('set_password');
		if ($set_pass) 
		{
			$this->registration_fields = array( 
			50 => array( 
				'icon' => 'user', 
				'field' => 'text', 
				'type' => 'usermeta', 
				'meta' => 'display_name', 
				'name' => __('Your Name', 'users-control'),
				'required' => 1
			),
			
			70 => array( 
				'icon' => 'user', 
				'field' => 'text', 
				'type' => 'usermeta', 
				'meta' => 'last_name', 
				'name' => __('Last Name', 'users-control'),
				'required' => 1
			),
			100 => array( 
				'icon' => 'envelope', 
				'field' => 'text', 
				'type' => 'usermeta', 
				'meta' => 'user_email', 
				'name' => __('E-mail','users-control'),
				'required' => 1,
				'can_hide' => 1,
			),
			
			250 => array( 
				'icon' => 'phone', 
				'field' => 'text', 
				'type' => 'usermeta', 
				'meta' => 'userscontrol_subject', 
				'name' => __('Subject','users-control'),
				'required' => 1,
				'can_hide' => 1
			),
			
			450 => array( 
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
		
		
		} else {
			
		$this->registration_fields = array( 
			50 => array( 
				 
				'field' => 'text', 
				'type' => 'usermeta', 
				'meta' => 'display_name', 
				'name' => __('Your Name','users-control'),
				'required' => 1,
				'width' => 'full'
				
			),
			
			70 => array( 
				 
				'field' => 'text', 
				'type' => 'usermeta', 
				'meta' => 'last_name', 
				'name' => __('Last Name', 'users-control'),
				'required' => 1,
				'width' => 'full'
				
				
			),
			100 => array( 
				
				'field' => 'text', 
				'type' => 'usermeta', 
				'meta' => 'user_email', 
				'name' => __('E-mail','users-control'),
				'required' => 1,
				'width' => 'full',
				'can_hide' => 1,
				'help' => __('A confirmation email will be sent to this email address. If you are already a client  please use the <strong>same email</strong> used to create your previous requests. Login information will be sent so you can check the status of your request.','users-control')
			),
			
			250 => array( 
				 
				'field' => 'text', 
				'type' => 'usermeta', 
				'meta' => 'userscontrol_subject', 
				'name' => __('Subject','users-control'),
				'required' => 1,
				'can_hide' => 1,
				'width' => 'full',
			),
			
			450 => array( 
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
				'help_text' => '',
				'width' => 'full',
			
			)
			
			
		);
		}
		
		/* Core login fields */
		$this->login_fields = array( 
			50 => array( 
				'icon' => 'user', 
				'field' => 'text', 
				'type' => 'usermeta', 
				'meta' => 'user_login', 
				'name' => __('Username or Email','users-control'),
				'required' => 1
				
			),
			100 => array( 
				'icon' => 'lock', 
				'field' => 'password', 
				'type' => 'usermeta', 
				'meta' => 'login_user_pass', 
				'name' => __('Password','users-control'),
				'required' => 1
			)
		);
		
		
				/* These are the basic profile fields */
		$this->fields = array(
			80 => array( 
			  'position' => '50',
				'type' => 'separator', 
				'name' => __('Appointment Info','users-control'),
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
		if (!get_option('userscontrol_profile_fields'))
		{
			update_option('userscontrol_profile_fields', $this->fields);
		}			
		
	}
	
		
	function get_the_guid( $id = 0 )
	{
		$post = get_post($id);
		return apply_filters('get_the_guid', $post->guid);
	}
	   	
	function load_classes() 
	{	
		
		foreach ($this->classes_array as $key => $class) 
		{
			if (file_exists(userscontrol_path."classes/$class.php")) 
			{
				require_once(userscontrol_path."classes/$class.php");
						
					
			}
				
		}	
	}
	
	
	function theme_add_editor_styles( $mce_css ) 
	{
	  if ( !empty( $mce_css ) )
		$mce_css .= ',';
		$mce_css .=  userscontrol_url.'templates/'.usercontrol_template.'/css/editor-style.css';
		return $mce_css;
	}
	  
	  
	  /* register admin scripts */
	public function add_styles_scripts()	{	
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_style("wp-jquery-ui-dialog");
		wp_enqueue_script('jquery-ui-datepicker' );
		
		wp_enqueue_script('plupload-all');	
		wp_enqueue_script('jquery-ui-progressbar');	
		wp_register_script( 'form-validate-lang', userscontrol_url.'js/languages/jquery.validationEngine-en.js',array('jquery'));
		wp_enqueue_script('form-validate-lang');		
			
		wp_register_script( 'form-validate', userscontrol_url.'js/jquery.validationEngine.js',array('jquery'));
		wp_enqueue_script('form-validate');		
	}
	
	/* register styles */
	public function add_front_end_styles(){
		global $wp_locale, $userscontrol_stripe;
		
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_style("wp-jquery-ui-dialog");
		wp_enqueue_script('jquery-ui-datepicker');			
		
		/*uploader*/					
		wp_enqueue_script('jquery-ui');			
		wp_enqueue_script('plupload-all');	
		wp_enqueue_script('jquery-ui-progressbar');		
		
		

		/* Font Awesome */
		wp_register_style('userscontrol_font_awesome', userscontrol_url.'css/css/font-awesome.min.css');
		wp_enqueue_style('userscontrol_font_awesome');
		
		//----MAIN STYLES		
				
		/* Custom style */		
		wp_register_style('userscontrol_style', userscontrol_url.'templates/basic/css/styles.css');
		wp_enqueue_style('userscontrol_style');	


		/* Profile style */		
		wp_register_style('userscontrol_profile_style', userscontrol_url.'templates/basic/user_profile_v1.css');
		wp_enqueue_style('userscontrol_profile_style');
		

		/* Directory style */		
		wp_register_style('userscontrol_directory_style', userscontrol_url.'templates/basic/directory-default-styles.css');
		wp_enqueue_style('userscontrol_directory_style');	

		$this->include_custom_css_styles();
		
		wp_register_style('userscontrol_captchastyle', userscontrol_url.'templates/basic/css/captchastyles.css');
		wp_enqueue_style('userscontrol_captchastyle');					
		
		/*Users JS*/		
		wp_register_script( 'userscontrolfront_js', userscontrol_url.'js/userscontrol-front.js',array('jquery'),  null);
		wp_enqueue_script('userscontrolfront_js');

		wp_add_inline_script( 'userscontrolfront_js', 'const USERSCONTROLFRONTV = ' . json_encode( array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),			
			) ), 'before' );
		
	
		
		wp_register_script('userscontrol-form-validate-lang', userscontrol_url.'js/languages/jquery.validationEngine-en.js',array('jquery'));
		wp_enqueue_script('userscontrol-form-validate-lang');
					
		wp_register_script( 'userscontrol-form-validate', userscontrol_url.'js/jquery.validationEngine.js',array('jquery'));
		wp_enqueue_script('userscontrol-form-validate');
		
		/*Validation Engibne JS*/		
			
		wp_register_script( 'userscontrol-form-validate-lang', userscontrol_url.'js/languages/jquery.validationEngine-en.js',array('jquery'));			
		wp_enqueue_script('userscontrol-form-validate-lang');	
				
		wp_register_script('userscontrol-form-validate', userscontrol_url.'js/jquery.validationEngine.js',array('jquery'));
		wp_enqueue_script('userscontrol-form-validate');
		
		$message_wait_submit ='<img src="'.userscontrol_url.'admin/images/loaderB16.gif" width="16" height="16" /></span>&nbsp; '.__("Please wait ...","users-control").'';		
		
		
		if($this->get_option('gateway_stripe_active') == '1' && isset($userscontrol_stripe)){
			$stripe_is_active = 1;	

		}else{

			$stripe_is_active = 0;		
		}	
		
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

				
		$date_picker_format = $this->get_date_picker_format();	
		
		wp_localize_script( 'userscontrolfront_js', 'userscontrol_pro_front', array(
            'message_wait'     => $message_wait_submit,
			'wait_submit'     => $message_wait_submit,
			'message_wait_staff_box'     => __("Please wait ...","users-control"),
			'message_wait_submit_payment'     => __("Please wait ...","users-control"),
			'message_submit_payment'     => __("SUBMIT","users-control"),
			'delete_confirm_message'     => __("Do you really want to delete this item?","users-control"),
			'userscontrol_date_picker_format'     => $date_picker_format,
			'nonce' => wp_create_nonce('ajax-nonce') ,
			'stripe_is_active'     => $stripe_is_active           
            
        ) );
		
		
		
		$date_picker_array = array(
		            'closeText' =>  __('Done',"users-control"),
		            'prevText' =>  __('Prev',"users-control"),
		            'nextText' => __('Next',"users-control"),
		            'currentText' => __('Today',"users-control"),
		            'monthNames' => array(
		                        'Jan' =>  __('January',"users-control"),
    		                    'Feb' =>  __('February',"users-control"),
    		                    'Mar' =>  __('March',"users-control"),
    		                    'Apr' =>  __('April',"users-control"),
    		                    'May' =>  __('May',"users-control"),
    		                    'Jun' =>  __('June',"users-control"),
    		                    'Jul' =>  __('July',"users-control"),
    		                    'Aug' =>  __('August',"users-control"),
    		                    'Sep' =>  __('September',"users-control"),
    		                    'Oct' => __('October' ,"users-control"),
    		                    'Nov' =>  __('November' ,"users-control"),
    		                    'Dec' =>  __('December' ,"users-control")
		                    ),
		            'monthNamesShort' => array(
		                        'Jan' => __('Jan' ,"users-control") ,
    		                    'Feb' => __('Feb' ,"users-control"),
    		                    'Mar' => __('Mar' ,"users-control"),
    		                    'Apr' => __('Apr' ,"users-control"),
    		                    'May' => __('May' ,"users-control"),
    		                    'Jun' => __('Jun' ,"users-control"),
    		                    'Jul' => __('Jul' ,"users-control"),
    		                    'Aug' => __('Aug' ,"users-control"),
    		                    'Sep' => __('Sep' ,"users-control"),
    		                    'Oct' =>__('Oct' ,"users-control"),
    		                    'Nov' => __('Nov' ,"users-control"),
    		                    'Dec' => __('Dec' ,"users-control")
		                    ),
		            'dayNames' => array(
		                        'Sun' => __('Sunday'  ,"users-control"),
    		                    'Mon' =>  __('Monday'  ,"users-control"),
    		                    'Tue' => __( 'Tuesday'  ,"users-control"),
    		                    'Wed' =>  __( 'Wednesday'  ,"users-control"),
    		                    'Thu' =>  __(  'Thursday'  ,"users-control"),
    		                    'Fri' =>   __('Friday'  ,"users-control"),
    		                    'Sat' =>  __('Saturday'  ,"users-control")
		                    ),
		            'dayNamesShort' => array(
		                        'Sun' => __('Sun'  ,"users-control") ,
    		                    'Mon' => __('Mon'  ,"users-control"),
    		                    'Tue' => __('Tue'  ,"users-control"),
    		                    'Wed' => __('Wed'  ,"users-control"),
    		                    'Thu' => __('Thu'  ,"users-control"),
    		                    'Fri' =>__('Fri'  ,"users-control"),
    		                    'Sat' =>__('Sat'  ,"users-control")
		                    ),
		            'dayNamesMin' => array(
		                        'Sun' => __('Su'  ,"users-control"),
    		                    'Mon' => __('Mo'  ,"users-control"),
    		                    'Tue' => __('Tu'  ,"users-control"),
    		                    'Wed' => __('We'  ,"users-control"),
    		                    'Thu' => __('Th'  ,"users-control"),
    		                    'Fri' => __('Fr'  ,"users-control"),
    		                    'Sat' => __('Sa'  ,"users-control")
		                    ),
		            'weekHeader' => 'Wk'
		        );
				
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
				
				
				wp_localize_script('userscontrol-front_js', 'EASYWPMDatePicker', $date_picker_array);
				
		
		
	}

	function include_custom_css_styles(){

		$theme_path = get_template_directory();		
		
		if(file_exists($theme_path."/userscontrol/directory-default-styles.css"))	{
			/* Directory style */		
			wp_register_style('userscontrol_directory_ccu', $theme_path."/userscontrol/directory-default-styles.css");
			wp_enqueue_style('userscontrol_directory_ccu');	
		}
	}
	
	/* Custom WP Query*/
	public function get_results( $query ) {
		$wp_user_query = new WP_User_Query($query);						
		return $wp_user_query;
		
	
	}
	
	/* Show registration form on booking steps */
	function get_registration_form( $args=array() ){

		global $post;		
		
				
		/* Arguments */
		$defaults = array(       
			'redirect_to' => null,
			'form_header_text' => __('Sign Up','users-control'),			
			'service_id' => '',
			'site_id' => '',
			'product_id' => '',
			'display_sites' => '',	
			'display_alignment' => '',	
			'on_backend' => false,	
			'staff_id' => '' 			
        		    
		);
		$args = wp_parse_args( $args, $defaults );
		$args_2 = $args;
		extract( $args, EXTR_SKIP );
						
		// Default set to blank
		$this->captcha = '';
		
		
		$display = null;
		
		
		
		   $display .= '<div class="wptu-user-data-registration-form">					';				
								
								
						 /*Display sucess message*/	
						 
						 if ( (isset($_GET['userscontrol_ticket_key']) && $_GET['userscontrol_ticket_key'] !='' ) && (isset($_GET['userscontrol_status']) && $_GET['userscontrol_status'] =='ok' ) ) 
						{
							 $display .= '<div class="wptu-ultra-success"><span><i class="fa fa-check"></i>'.__('Your request has been sent successfully. Please check your email.','users-control').'</span></div>';
						 
						 }
													
													
						/*Display errors*/
						if (isset($_POST['wptu-register-form'])) 
						{
							$display .= $this->register->get_errors();
						}
						
						if($on_backend){
							
							$display .= $this->display_ticket_form( $redirect_to, $args_2);						
							
						
						}else{
							
							if(is_user_logged_in())
		 					{
								
								$display .= '<div class="wptu-ultra-success"><span><i class="fa fa-check"></i>'.__('Please submit a ticket through your account.','users-control').'</span></div>';
								
								return $display;
								
							
							}else{
								
								$display .= $this->display_ticket_form( $redirect_to, $args_2);							
							
							}				
						
						}
						
						

				$display .= '';
		
		
		return $display;
		
	}

	function get_message_box_display($type, $message){

		$ico = '';
		if($type=='success'){
			$ico = 'fa-check';
		}elseif($type=='error'){
			//$ico = 'fa-check';

			
		}

		$html = '<div class="userscontrol-ultra-'.$type.'"><span><i class="fa '.$ico.'"></i>'.$message.'</span></div>';
		return $html;

	}
	
	function get_time_duration_format($seconds){
		global $wpdb, $userscontrol;
		
		$time_formated = $userscontrol->commmonmethods->secondsToTime($seconds);
		
		
		if($seconds<3600) //less than an hour
		{
			$str = $time_formated["m"] . " min ";		
		
		}else{
			
			$str = $time_formated["h"] ." h ";
			
			
			if($time_formated["m"] > 0)
			 {
				$str =  $str." ".$time_formated["m"]." min ";
			
			}
			
		
		
		}
		
		
		
		return $str;
	
	
	}
	
	
	/* Show ticket form as admin */
	function get_registration_form_on_admin( $args=array() ){

		global $post;		
		
				
		/* Arguments */
		$defaults = array(       
			'redirect_to' => null,
			'form_header_text' => __('Sign Up','users-control'),			
			'service_id' => '',
			'site_id' => '',
			'product_id' => '',
			'display_sites' => '',	
			'display_alignment' => '',		
			'staff_id' => '' 			
        		    
		);
		$args = wp_parse_args( $args, $defaults );
		$args_2 = $args;
		extract( $args, EXTR_SKIP );
						
		// Default set to blank
		$this->captcha = '';
		
		
		$display = null;
		
		
		
		   $display .= '<div class="wptu-user-data-registration-form">					';				
								
								
						 /*Display sucess message*/	
						 
						 if ( (isset($_GET['userscontrol_ticket_key']) && $_GET['userscontrol_ticket_key'] !='' ) && (isset($_GET['userscontrol_status']) && $_GET['userscontrol_status'] =='ok' ) ) 
						{
							 $display .= '<div class="wptu-ultra-success"><span><i class="fa fa-check"></i>'.__('Your request has been sent successfully. Please check your email.','users-control').'</span></div>';
						 
						 }
													
													
						/*Display errors*/
						if (isset($_POST['wptu-register-form'])) 
						{
							$display .= $this->register->get_errors();
						}
						
						$display .= $this->display_ticket_form_as_admin( $redirect_to, $args_2);

				$display .= '';
		
		
		return $display;
		
	}
	
	function get_priority_values()
	{
		
				
		$html = '';
		
		$val_rows = $this->priority->get_all_public();
		
		
		$html .= '<select name="wptu-priority" id="wptu-priority" data-errormessage-value-missing="'.__(' * This field is required!','users-control').'" class="validate[required]">';
		$html .= '<option value="" selected="selected">'.__('Select','users-control').'</option>';
		
		foreach ( $val_rows as $val )
		{		
			
			$html .= '<option value="'.$val->priority_id.'" '.$selected.' >'.$val->priority_name.'</option>';
				
		}			
		
		
		$html .= '</select>';
		
		return $html;
		
	}
	
	function get_custom_department_fields_ajax(){
		global $userscontrol;
		$depto_id = sanitize_text_field($_POST['department_id']); 
		$html =$this->get_custom_department_fields($depto_id);		
		echo wp_kses($html, $userscontrol->allowed_html);
		die();
		
	}
	
	function get_custom_department_fields($department_id = null){
		global $userscontrol_register,  $userscontrol_captcha_loader, $wptucomplement;
		$display = null;
		
		$array = array();
		
		$custom_form = 'userscontrol_profile_fields_'.$department_id;		
		$array = get_option($custom_form);			
		$fields_set_to_update =$custom_form;
		
				
		
		if(!is_array($array))$array = array();
		

		foreach($array as $key=>$field) 
		{		     
		    $exclude_array = array('user_pass', 'user_pass_confirm', 'user_email');
		    if(isset($field['meta']) && in_array($field['meta'], $exclude_array))
		    {
		        unset($array[$key]);
		    }
		}
		
		$i_array_end = end($array);
		
		if(isset($i_array_end['position']))
		{
		    $array_end = $i_array_end['position'];
		    
			if (isset($array[$array_end]['type']) && $array[$array_end]['type'] == 'seperator') 
			{
				if(isset($array[$array_end]))
				{
					unset($array[$array_end]);
				}
			}
		}
		
		
		/*Display custom profile fields added by the user*/		
		foreach($array as $key => $field) 
		{

			extract($field);
			
			// WP 3.6 Fix
			if(!isset($deleted))
			    $deleted = 0;
			
			if(!isset($private))
			    $private = 0;
			
			if(!isset($required))
			    $required = 0;
			
			$required_class = '';
			$required_text = '';
			if($required == 1 && in_array($field, $this->include_for_validation))
			{				
			    $required_class = 'validate[required] ';
				$required_text = '(*)';				
			}
			
			
			$name = stripslashes($name);
			
			
			/* This is a Fieldset seperator */
						
			/* separator */
            if ($type == 'separator' && $deleted == 0 && $private == 0 && isset($array[$key]['show_in_register']) && $array[$key]['show_in_register'] == 1) 
			{
                   $display .= '<div class="wptu-profile-separator">'.$name.'</div>';
				   
            }
			
					
			//check if display emtpy				
				
			if ($type == 'usermeta' && $deleted == 0 && $private == 0 && isset($array[$key]['show_in_register']) && $array[$key]['show_in_register'] == 1) 
			{
								
				$display .= '<div class="wptu-profile-field">';
				
				/* Show the label */
				if (isset($array[$key]['name']) && $name)
				 {
					$display .= '<label class="wptu-field-type" for="'.$meta.'">';	
					
					if (isset($array[$key]['icon']) && $icon) 
					{
						
                            $display .= '<i class="fa fa-' . $icon . '"></i>';
                    } else {
                           // $display .= '<i class="fa fa-icon-none"></i>';
                    }
					
					
											
					$tooltipip_class = '';					
					if (isset($array[$key]['tooltip']) && $tooltip)
					{
						$qtip_classes = 'qtip-light ';	
						$qtip_style = '';					
					
						 $tooltipip_class = '<a class="'.$qtip_classes.' wptu-tooltip" title="' . $tooltip . '" '.$qtip_style.'><i class="fa fa-info-circle reg_tooltip"></i></a>';
					} 
					
											
					$display .= '<span>'.stripslashes($name). ' '.$required_text.' '.$tooltipip_class.'</span></label>';
					
					
				} else {
					$display .= '<label class="">&nbsp;</label>';
				}
				
				$display .= '<div class="wptu-field-value">';
					
					switch($field) {
					
						case 'textarea':
							$display .= '<textarea class="'.$required_class.' wptu-input wptu-input-text-area" rows="10" name="'.$meta.'" id="'.$meta.'" title="'.$name.'" data-errormessage-value-missing="'.__(' * This input is required!','xoousers').'">'.$this->get_post_value($meta).'</textarea>';
							break;
							
						case 'text':
							$display .= '<input type="text" class="'.$required_class.' wptu-input"  name="'.$meta.'" id="'.$meta.'" value="'.$this->get_post_value($meta).'"  title="'.$name.'" data-errormessage-value-missing="'.__(' * This input is required!','users-control').'"/>';
							break;							
							
						case 'datetime':						
						    $display .= '<input type="text" class="'.$required_class.' wptu-input wptu-datepicker" name="'.$meta.'" id="'.$meta.'" value="'.$this->get_post_value($meta).'"  title="'.$name.'" />';
						    break;
							
						case 'select':						
							if (isset($array[$key]['predefined_options']) && $array[$key]['predefined_options']!= '' && $array[$key]['predefined_options']!= '0' )
							
							{
								$loop = $this->commmonmethods->get_predifined( $array[$key]['predefined_options'] );
								
							}elseif (isset($array[$key]['choices']) && $array[$key]['choices'] != '') {
								
															
								$loop = $this->userscontrol_one_line_checkbox_on_window_fix($choices);
								 	
								
							}
							
							if (isset($loop)) 
							{
								$display .= '<select class="'.$required_class.' wptu-input" name="'.$meta.'" id="'.$meta.'" title="'.$name.'" data-errormessage-value-missing="'.__(' * This input is required!','users-control').'">';
								
								foreach($loop as $option)
								{
									
								$option = trim(stripslashes($option));
								
								    
								$display .= '<option value="'.$option.'" '.selected( $this->get_post_value($meta), $option, 0 ).'>'.$option.'</option>';
								}
								$display .= '</select>';
							}
							
							break;
							
						case 'radio':
						
						$display .= '<ul>';						
						
							if($required == 1 && in_array($field, $this->include_for_validation))
							{
								$required_class = "validate[required] radio ";
							}
						
							if (isset($array[$key]['choices']))
							{				
													
								
								 $loop = $this->userscontrol_one_line_checkbox_on_window_fix($choices);
								
							}
							if (isset($loop) && $loop[0] != '') 
							{
							  $counter =0;
							  
								foreach($loop as $option)
								{
								    if($counter >0)
								        $required_class = '';
								    
								    $option = trim(stripslashes($option));
									
									$display .= '<li>';	
									
									$display .= '<input type="radio" class="'.$required_class.'" title="'.$name.'" name="'.$meta.'" id="userscontrol_multi_radio_'.$meta.'_'.$counter.'" value="'.$option.'" '.checked( $this->get_post_value($meta), $option, 0 );
									$display .= '/> <label for="userscontrol_multi_radio_'.$meta.'_'.$counter.'"><span>'.$option.'</span></label>';
									
									$display .= '</li>';	
									
									$counter++;
									
								}
							}
							
							
							$display .= '</ul>';	
							
							break;
							
						case 'checkbox':
						
						$display .= '<ul>';	
						
						
							if($required == 1 && in_array($field, $this->include_for_validation))
							{
								$required_class = "validate[required] checkbox ";
							}						
						
							if (isset($array[$key]['choices'])) 
							{
																
								 $loop = $this->userscontrol_one_line_checkbox_on_window_fix($choices);
								
								
							}
							
							if (isset($loop) && $loop[0] != '') 
							{
							  $counter =0;
							  
								foreach($loop as $option)
								{
								   
								   if($counter >0)
								        $required_class = '';
								  
								  $option = trim(stripslashes($option));
								  
								  $display .= '<li>';
								  
								  $display .= '<input type="checkbox" class="'.$required_class.'" title="'.$name.'" name="'.$meta.'[]" id="userscontrol_multi_box_'.$meta.'_'.$counter.'" value="'.$option.'" ';
									if (is_array($this->get_post_value($meta)) && in_array($option, $this->get_post_value($meta) )) {
									$display .= 'checked="checked"';
									}
									$display .= '/> <label for="userscontrol_multi_box_'.$meta.'_'.$counter.'"> '.$option.'</label> ';
									
									$display .= '<li>';
									$counter++;
								}
							}
							
							$display .= '</ul>';	
							
							break;
							
						
													
						case 'password':						
							$display .= '<input type="password" class="bup-input'.$required_class.'" title="'.$name.'" name="'.$meta.'" id="'.$meta.'" value="'.$this->get_post_value($meta).'" />';
							
							
							break;
							
					}
					
					
					if (isset($array[$key]['help_text']) && $help_text != '') 
					{
						$display .= '<div class="wptu-help">'.$help_text.'</div>';
					}
							
					
				$display .= '</div>';
				$display .= '</div>';
				
			}
		} //end while
		
		
		return $display;
		
		
	}
	
	function get_sites_to_submit_by_admin($user_id)
	{
		
		global $userscontrol_register,  $userscontrol_captcha_loader, $wptucomplement;
		
		$html = '';
		
		
		$sites = $this->get_my_allowed_sites_list($user_id);
		
		if (!empty($sites))
		{
			$html .= '<ul>';				
			foreach($sites as $site) 
			{
				
						
				$html .= '<li><a href="?page=wpticketultra&tab=createticket&userscontrol_site='.$site->site_id.'">'.$site->site_name.'</a></li>';		
				
				
			}
			$html .= '</ul>';	
		}
		
		return $html;
		
	}
	
	function get_sites_to_submit_by_staff($user_id)
	{
		
		global $userscontrol_register,  $userscontrol_captcha_loader, $wptucomplement;
		
		$html = '';
		
		
		$sites = $this->get_my_allowed_sites_list($user_id);
		
		if (!empty($sites))
		{
			$html .= '<ul class="wptu-products-select">';				
			foreach($sites as $site) 
			{
				
						
				$html .= '<li><a href="?module=submit&userscontrol_site='.$site->site_id.'">'.$site->site_name.'</a></li>';		
				
				
			}
			$html .= '</ul>';	
		}
		
		return $html;
		
	}
	
	function get_my_allowed_sites_list($user_id)
	{
		
		global $userscontrol, $wpdb  , $wptucomplement;
		
		
		$is_client  = $userscontrol->profile->is_client($user_id); 
		
		$is_super_admin =  $userscontrol->profile->is_user_admin($user_id);
		
		if($is_client || $is_super_admin)	 // is a client or admin, then get all websites
		{
			$sql = ' SELECT * FROM ' . $wpdb->prefix . $this->table_prefix.'_sites ORDER BY site_name ' ;
			
		}else{ //is a staff member then get all websites the staff can submit a post in
			
			$departments_ids = $userscontrol->userpanel->get_all_staff_allowed_deptos_list($user_id);			

		
			$sql = ' SELECT  site.*, dep.* ,  deptoallowed.* FROM ' . $wpdb->prefix . $this->table_prefix.'_sites as site ' ;
				
			$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prefix."_departments dep ON (dep.department_site_id = site.site_id)";				
				
			$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prefix."_department_staff deptoallowed ON (deptoallowed.depto_department_id = dep.department_id)";		
						
			$sql .= " WHERE dep.department_site_id = site.site_id AND deptoallowed.depto_department_id = dep.department_id AND deptoallowed.depto_staff_id = '".$user_id."' " ;			
		
			//display only allowed departments		
			$sql .= " AND dep.department_id  IN (".$departments_ids.") ";
			$sql .= " GROUP BY site.site_id ";
		
		}
		
				
		$res = $wpdb->get_results($sql);
		return $res;
	
	}
	
		
	
	// File upload handler:
	function front_upload_files()
	{
		global $userscontrol;
		global $wpdb;
		
		
		$site_url = site_url()."/";
		
		// Check referer, die if no ajax:
		check_ajax_referer('photo-upload');
		
		/// Upload file using Wordpress functions:
		$file = $_FILES['async-upload'];
		
		
		$original_max_width = $userscontrol->get_option('media_avatar_width'); 
        $original_max_height =$userscontrol->get_option('media_avatar_height');
		
		$allowed_extensions =$userscontrol->get_option('allowed_extensions');
		$allowed_extensions = explode(",", $allowed_extensions);
		
		
		if($original_max_width=="" || $original_max_height=="")
		{			
			$original_max_width = 80;			
			$original_max_height = 80;
			
		}
		
			
				
		$o_id = sanitize_text_field($_POST['temp_ticket_id']);
		$upload_folder_temp = $this->upload_folder_temp;
		
				
		$info = pathinfo($file['name']);
		$real_name = $file['name'];
        $ext = $info['extension'];
		$ext=strtolower($ext);
		
		$rand = $this->userpanel->genRandomString();
		
		$rand_name = "file_".$rand."_".time(); 		
	
		$upload_dir = wp_upload_dir(); 
		$path_pics =   $upload_dir['basedir'];
			
			
		if(in_array($ext,$allowed_extensions)) 
		{
			if($o_id != '')
			{
				
				if(!is_dir($path_pics."/".$upload_folder_temp."")) 
				{
					 wp_mkdir_p( $path_pics."/".$upload_folder_temp );							   
				}
				
				if(!is_dir($path_pics."/".$o_id."")) 
				{
					 wp_mkdir_p( $path_pics."/".$upload_folder_temp."/".$o_id );							   
				}					
										
				$pathBig = $path_pics."/".$upload_folder_temp."/".$o_id."/".$rand_name.".".$ext;						
					
					
					if (copy($file['tmp_name'], $pathBig)) 
					{
						//check auto-rotation						
						if($userscontrol->get_option('avatar_rotation_fixer')=='yes')
						{
							//$this->orient_image($pathBig);
						
						}
						
						$upload_folder = $userscontrol->get_option('media_uploading_folder');				
						$path = $site_url.$upload_folder."/".$o_id."/";
						
						//check max width												
						list( $source_width, $source_height, $source_type ) = getimagesize($pathBig);
						
						if($source_width > $original_max_width) 
						{
							//resize
						//	if ($this->createthumb($pathBig, $pathBig, $original_max_width, $original_max_height,$ext)) 
							//{
								//$old = umask(0);
								//chmod($pathBig, 0755);
								//umask($old);
														
							//}
						
						
						}
						
						
						
						$new_avatar = $rand_name.".".$ext;						
						$new_avatar_url = $path.$rand_name.".".$ext;
					
						
						//update user meta
						
					}
									
					
			     }  		
			
        } // image type
		
			
		// Create response array:
		$uploadResponse = array('image' => $new_avatar);
		
		// Return response and exit:
		echo json_encode($uploadResponse);		

		die();
		
	}
	
	
	public function front_end_file_uploader() 
	{
		
		$rand = $this->userpanel->genRandomString();
		
		$temp_ticket_id = "userscontrol_".$rand."_".time();
		
		$allowed_extensions = $this->get_option('allowed_extensions');
		
				
		// Uploading functionality trigger:
		// (Most of the code comes from media.php and handlers.js)
		 $template_dir = get_template_directory_uri();
		 

		
		$plupload_init = array(
				'runtimes'            => 'html5,silverlight,flash,html4',
				'browse_button'       => 'wptu-browse-button-files',
				'container'           => 'wp-file-uploader-front',
				'drop_element'        => 'drag-drop-area-sitewidewall',
				'file_data_name'      => 'async-upload',
				'multiple_queues'     => true,
				'multi_selection'	  => true,
				'max_file_size'       => wp_max_upload_size().'b',
				'url'                 => admin_url('admin-ajax.php'),
				'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
				'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
				
				'filters'             => array(array('title' => __('Allowed Files', "wpticku"), 'extensions' => "$allowed_extensions")),
				'multipart'           => true,
				'urlstream_upload'    => true,

				// Additional parameters:
				'multipart_params'    => array(
					'_ajax_nonce' => wp_create_nonce('photo-upload'),
					'temp_ticket_id' => $temp_ticket_id,
					'action'      => 'userscontrol_front_upload_files' // The AJAX action name
					
				),
			);
			
			//print_r($plupload_init);

			
		
		 
		   
		   $html = '';
		   
				
		   $html = '<div id="uploadContainer" style="margin-top: 10px;" class="wptu-avatar-drag-drop-sector">
		   
		   <input type="hidden" name="userscontrol_temp_ticket_id" value="'.$temp_ticket_id.'" id="userscontrol_temp_ticket_id" />
			
			
			<!-- Uploader section -->
			<div id="uploaderSection" style="position: relative;">
			
				<div id="plupload-upload-ui-sitewidewall" class="hide-if-no-js">
				
				
                
					<div id="drag-drop-area-sitewidewall">';					
					
					$html .= '<div class="drag-drop-inside">';
					
						$html .= '<p class="drag-drop-info">'.__('Drop files here or', 'users-control').'</p>';
																	
								
						$html .= '<p>
														  
								<button name="wptu-browse-button-files" type="button" id="wptu-browse-button-files" class="wptu-button-upload-avatar" ><span><i class="fa fa-files-o"></i></span>'.__('Select Files', 'users-control').'</button>
								</p>';
					
					$html .= '</div>';
					
					
					
					
						                        
                   $html .= '     <div id="progressbar-sitewidewall"></div>                 
                         <div id="userscontrol_filelist_sitewidewall" class="cb"></div>
						 
						 
						 
					</div>
					
				
				</div>
                
                 
			
			</div>
            
           
		</div>';
		
		
			 
			 
			$js_messages_one_file = __("'You may only upload one image at a time!'", 'users-control');
			$js_messages_file_size_limit = __("'The file you selected exceeds the maximum filesize limit.'", 'users-control');
			$js_messages_upload_completed = __("Upload Completed!", 'users-control');
			
			

			$html .= '<script type="text/javascript">';
			
			$html .= "jQuery(document).ready(function($){
					
					// Create uploader and pass configuration:
					var uploader_sitewidewall = new plupload.Uploader(".json_encode($plupload_init).");

					// Check for drag'n'drop functionality:
					uploader_sitewidewall.bind('Init', function(up){
						
					var uploaddiv_sitewidewall = $('#wp-file-uploader-front');
						
						// Add classes and bind actions:
						if(up.features.dragdrop){
							uploaddiv_sitewidewall.addClass('drag-drop');
							
							$('#drag-drop-area-sitewidewall')
								.bind('dragover.wp-uploader', function(){ uploaddiv_sitewidewall.addClass('drag-over'); })
								.bind('dragleave.wp-uploader, drop.wp-uploader', function(){ uploaddiv_sitewidewall.removeClass('drag-over'); });

						} else{
							uploaddiv_sitewidewall.removeClass('drag-drop');
							$('#drag-drop-area').unbind('.wp-uploader');
						}

					});

					
					// Init ////////////////////////////////////////////////////
					uploader_sitewidewall.init(); 
					
					// Selected Files //////////////////////////////////////////
					uploader_sitewidewall.bind('FilesAdded', function(up, files) {
						
						
						var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10);
						
												
						// Loop through files:
						plupload.each(files, function(file){
							
							// Handle maximum size limit:
							if (max > hundredmb && file.size > hundredmb && up.runtime != 'html5'){
								alert($js_messages_file_size_limit);
								return false;
							}
						
						});
						
						jQuery.each(files, function(i, file) {
							
							//fix this
							
							jQuery('#userscontrol_filelist_sitewidewall').append('<div class=addedFile id=' + file.id + '>' + file.name + '</div>');
						});
						
						up.refresh(); 
						uploader_sitewidewall.start();
						
						//alert('start here');
						$( '#wptu-wall-photo-uploader-box' ).slideDown();
						$( '#progressbar-sitewidewall' ).slideDown();
						
						
					});
					
					// A new file was uploaded:
					uploader_sitewidewall.bind('FileUploaded', function(up, file, response){
						
						var obj = jQuery.parseJSON(response.response);												
						var img_name = obj.image;
						
						var reset_value = 0;
						
						jQuery('#progressbar-sitewidewall').html('<span class=progressTooltip>' + reset_value + '%</span>');
						
						
						
						
						
					
					});
					
					// Error Alert /////////////////////////////////////////////
					uploader_sitewidewall.bind('Error', function(up, err) {
						alert('Error: ' + err.code + ', Message: ' + err.message + (err.file ? ', File: ' + err.file.name : '') );
						up.refresh(); 
					});
					
					// Progress bar ////////////////////////////////////////////
					uploader_sitewidewall.bind('UploadProgress', function(up, file) {
						
						var progressBarValue = up.total.percent;
						
						jQuery('#progressbar-sitewidewall').fadeIn().progressbar({
							value: progressBarValue
						});
						
						//fix this
						
						//jQuery('#progressbar-sitewidewall').html('<span class='progressTooltip'>' + up.total.percent + '%</span>');
						
						jQuery('#progressbar-sitewidewall').html('<span class=progressTooltip>' + up.total.percent + '%</span>');
						
						
						
					});
					
					// Close window after upload ///////////////////////////////
					uploader_sitewidewall.bind('UploadComplete', function() {
						
						//jQuery('.uploader').fadeOut('slow');						
						jQuery('#progressbar-sitewidewall').fadeIn().progressbar({
							value: 0
						});
						
						
						jQuery('#progressbar-sitewidewall').html('<span class=progressTooltip>".$js_messages_upload_completed."</span>');
						
						
					});
					
					
					
				}); ";
				
					
			$html .= '</script>';
			
			
			//}
			
			// Apply filters to initiate plupload:
			$plupload_init = apply_filters('plupload_init', $plupload_init);
			
		
		
		return $html;
	
	
	}
	
	public function backend_end_file_uploader() 
	{
		
		$rand = $this->userpanel->genRandomString();
		
		$temp_ticket_id = "userscontrol_".$rand."_".time();
		
				
		// Uploading functionality trigger:
		// (Most of the code comes from media.php and handlers.js)
		 $template_dir = get_template_directory_uri();
		 
		 $allowed_extensions = $this->get_option('allowed_extensions');
		 
		 ///"php","asp","aspx","cmd","csh","bat","html","htm","hta","jar","exe","com","js","lnk","htaccess","phtml","ps1","ps2","php3","php4","php5","php6","py","rb","tmp"
		
		$plupload_init = array(
				'runtimes'            => 'html5,silverlight,flash,html4',
				'browse_button'       => 'wptu-browse-button-files',
				'container'           => 'plupload-upload-ui-sitewidewall',
				'drop_element'        => 'wp-file-uploader-front',
				'file_data_name'      => 'async-upload',
				'multiple_queues'     => true,
				'multi_selection'	  => true,
				'max_file_size'       => wp_max_upload_size().'b',
				'url'                 => admin_url('admin-ajax.php'),
				'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
				'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
				//'filters'             => array(array('title' => __('Allowed Files', $this->text_domain), 'extensions' => "jpg,png,gif,bmp,mp4,avi")),
				'filters'             => array(array('title' => __('Allowed Files', "wpticku"), 'extensions' => "$allowed_extensions")),
				'multipart'           => true,
				'urlstream_upload'    => true,

				// Additional parameters:
				'multipart_params'    => array(
					'_ajax_nonce' => wp_create_nonce('photo-upload'),
					'temp_ticket_id' => $temp_ticket_id,
					'action'      => 'userscontrol_front_upload_files' // The AJAX action name
					
				),
			);
			
			//print_r($plupload_init);

			
		
		 
		   
		   
		 //  if(!is_user_logged_in())
		  // {
			  // $html .="<p>".__("You have to be logged in to upload photos ",'d')."</p>";
			
		  // }else{

		
		   $html = '<div style="margin-top: 10px;" class="wptu-avatar-drag-drop-sector">
		   
		   <input type="hidden" name="userscontrol_temp_ticket_id" value="'.$temp_ticket_id.'" id="userscontrol_temp_ticket_id" />
			
			
			<!-- Uploader section -->
			<div id="uploaderSection" style="position: relative;">
			
				<div id="plupload-upload-ui-sitewidewall" class="hide-if-no-js">	
				
                
					<div id="drag-drop-area-sitewidewall">';					
					
					$html .= '<div class="drag-drop-inside">';
					
						$html .= '<p class="drag-drop-info">'.__('Drop files here or', 'users-control').'</p>';
																	
								
						/*$html .= '<p>
														  
								<button name="wptu-browse-button-files" type="button" id="wptu-browse-button-files" class="wptu-button-upload-avatar" ><span><i class="fa fa-files-o"></i></span>'.__('Select Files', 'users-control').'</button>
								</p>';*/
								
					$html .= '
														  
								<div name="wptu-browse-button-files" id="wptu-browse-button-files" class="wptu-button-upload-avatar" ><span><i class="fa fa-files-o"></i></span>'.__('Select Files', 'users-control').'</div>
								';
					
					$html .= '</div>';
					
					
					
					
						                        
                   $html .= '     <div id="progressbar-sitewidewall"></div>                 
                         <div id="userscontrol_filelist_sitewidewall" class="cb"></div>
						 
						 
						 
					</div>
					
					
				
				</div>
                
                 
			
			</div>
            
           
		</div>';
			 
			 
			$js_messages_one_file = __("'You may only upload one image at a time!'", 'users-control');
			$js_messages_file_size_limit = __("'The file you selected exceeds the maximum filesize limit.'", 'users-control');
			$js_messages_upload_completed = __("Upload Completed!", 'users-control');
			
			

			$html .= '<script type="text/javascript">';
			
			$html .= "jQuery(document).ready(function($){
					
					// Create uploader and pass configuration:
					var uploader_sitewidewall = new plupload.Uploader(".json_encode($plupload_init).");

					// Check for drag'n'drop functionality:
					uploader_sitewidewall.bind('Init', function(up){
						
					var uploaddiv_sitewidewall = $('#plupload-upload-ui-sitewidewall');
						
						// Add classes and bind actions:
						if(up.features.dragdrop){
							uploaddiv_sitewidewall.addClass('drag-drop');
							
							$('#drag-drop-area-sitewidewall')
								.bind('dragover.wp-uploader', function(){ uploaddiv_sitewidewall.addClass('drag-over'); })
								.bind('dragleave.wp-uploader, drop.wp-uploader', function(){ uploaddiv_sitewidewall.removeClass('drag-over'); });

						} else{
							uploaddiv_sitewidewall.removeClass('drag-drop');
							$('#drag-drop-area').unbind('.wp-uploader');
						}

					});

					
					// Init ////////////////////////////////////////////////////
					uploader_sitewidewall.init(); 
					
					// Selected Files //////////////////////////////////////////
					uploader_sitewidewall.bind('FilesAdded', function(up, files) {
						
						
						var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10);
						
												
						// Loop through files:
						plupload.each(files, function(file){
							
							// Handle maximum size limit:
							if (max > hundredmb && file.size > hundredmb && up.runtime != 'html5'){
								alert($js_messages_file_size_limit);
								return false;
							}
						
						});
						
						jQuery.each(files, function(i, file) {
							
							//fix this
							
							jQuery('#userscontrol_filelist_sitewidewall').append('<div class=addedFile id=' + file.id + '>' + file.name + '</div>');
						});
						
						up.refresh(); 
						uploader_sitewidewall.start();
						
						//alert('start here');
						$( '#wptu-wall-photo-uploader-box' ).slideDown();
						$( '#progressbar-sitewidewall' ).slideDown();
						
						
					});
					
					// A new file was uploaded:
					uploader_sitewidewall.bind('FileUploaded', function(up, file, response){
						
						var obj = jQuery.parseJSON(response.response);												
						var img_name = obj.image;
						
						var reset_value = 0;
						
						jQuery('#progressbar-sitewidewall').html('<span class=progressTooltip>' + reset_value + '%</span>');
						
						
											
						
					
					});
					
					// Error Alert /////////////////////////////////////////////
					uploader_sitewidewall.bind('Error', function(up, err) {
						alert('Error: ' + err.code + ', Message: ' + err.message + (err.file ? ', File: ' + err.file.name : '') );
						up.refresh(); 
					});
					
					// Progress bar ////////////////////////////////////////////
					uploader_sitewidewall.bind('UploadProgress', function(up, file) {
						
						var progressBarValue = up.total.percent;
						
						jQuery('#progressbar-sitewidewall').fadeIn().progressbar({
							value: progressBarValue
						});
						
						//fix this
						
						//jQuery('#progressbar-sitewidewall').html('<span class='progressTooltip'>' + up.total.percent + '%</span>');
						
						jQuery('#progressbar-sitewidewall').html('<span class=progressTooltip>' + up.total.percent + '%</span>');
						
						
						
					});
					
					// Close window after upload ///////////////////////////////
					uploader_sitewidewall.bind('UploadComplete', function() {
						
						//jQuery('.uploader').fadeOut('slow');						
						jQuery('#progressbar-sitewidewall').fadeIn().progressbar({
							value: 0
						});
						
						
						jQuery('#progressbar-sitewidewall').html('<span class=progressTooltip>".$js_messages_upload_completed."</span>');
						
						
					});
					
					
					
					
					
					
				}); ";
				
					
			$html .= ' </script>';
			
			
			//}
			
			
			// Apply filters to initiate plupload:
			$plupload_init = apply_filters('plupload_init', $plupload_init);
			
		
		
		return $html;
	
	
	}
	
	
	
	/**
	 * This has been added to avoid the window server issues
	 */
	public function userscontrol_one_line_checkbox_on_window_fix($choices)
	{		
		
		if($this->userscontrol_if_windows_server()) //is window
		{
			$loop = array();		
			$loop = explode(",", $choices);
		
		}else{ //not window
		
			$loop = array();		
			$loop = explode(PHP_EOL, $choices);	
			
		}	
		
		
		return $loop;
	
	}
	
	public function userscontrol_if_windows_server()
	{
		$os = PHP_OS;
		$os = strtolower($os);			
		$pos = strpos($os, "win");	
		
		if ($pos === false) {
			
			return false;
		} else {
			return true;
		}			
	
	}
	
		
	
	
	


	

	
		
		
	public function get_current_url(){
		$result = 'http';
		$script_name = "";
		if(isset($_SERVER['REQUEST_URI'])){
			$script_name = sanitize_url( $_SERVER['REQUEST_URI']);
		}else{
			$script_name = sanitize_url($_SERVER['PHP_SELF']);
			if($_SERVER['QUERY_STRING']>' ') {
				$script_name .=  '?'.sanitize_text_field($_SERVER['QUERY_STRING']);
			}
		}
		
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') {
			$result .=  's';
		}
		$result .=  '://';
		
		if($_SERVER['SERVER_PORT']!='80'){
			$result .= sanitize_url($_SERVER['HTTP_HOST']).':'.sanitize_text_field($_SERVER['SERVER_PORT'].$script_name);
		}else{
			$result .=  sanitize_url($_SERVER['HTTP_HOST']).$script_name;
		}
	
		return $result;
	}
	
	function get_me_wphtml_editor($meta, $content)
	{
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
	
	/* get setting */
	function get_option($option) 
	{
		$settings = get_option('userscontrol_options');
		if (isset($settings[$option])) 
		{
			if(is_array($settings[$option]))
			{
				return $settings[$option];
			
			}else{
				
				return stripslashes($settings[$option]);
			}
			
		}else{
			
		    return '';
		}
		    
	}
	
	/* Get post value */
	function userscontrol_admin_post_value($key, $value, $post){
		if (isset($_POST[$key])){
			if ($_POST[$key] == $value)
				echo esc_attr('selected="selected"');
		}
	}
	
	public function get_subscription_categories($selected_cate = null) 
	{
		global $wpdb, $userscontrol;
		
		$display = "";
        $key = "";
		$categories = get_categories(array('hide_empty' => 0));	
		$meta= 'userscontrol_subscription_categories[]';
		
	
           foreach ($categories as $category )
		   {
			   $sel ="";
			   if($selected_cate==$key) 
			   {
				   $sel = 'selected="selected"';
				  
			   }
			   
			   $display .= '<label>
   					 <input type="checkbox" name="'. $meta .'" value="'. $category->term_id .'" id="'.$meta.'" />'. $category->name.'</label>';
			 
           }
          
		
									
		return  $display;
	
	
	}
	
	public function get_subscription_categories_admin($package = null) 
	{
		global $wpdb, $userscontrol;
		
		$display = "";				
		$categories = get_categories(array('hide_empty' => 0));	
		$meta= 'userscontrol_subscription_categories[]';
		
		$selected_cate = array();
		
		if($package->membership_access_categories!=''){
			$selected_cate = explode(',',$package->membership_access_categories );		
		}
		
        foreach ($categories as $category )
		{
			   $sel ="";
			   if(in_array($category->term_id,$selected_cate)) 
			   {
				   $sel = 'checked="checked"';
				  
			   }
			   
			   $display .= '<label>
   					 <input type="checkbox" name="'. $meta .'" value="'. $category->term_id .'" id="'.$meta.'"  '. $sel .'/>'. $category->name.'</label>';
			 
        }
          
		return  $display;
	
	}
	
	/*Post value*/
	function get_post_value($meta) {		
		if (isset($_POST[$meta]) ) {
			return sanitize_text_field($_POST[$meta]);
		}		
	}
}
?>