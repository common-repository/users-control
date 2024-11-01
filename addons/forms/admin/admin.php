<?php
class UsersControlForm {
	var $options;
	var $custom_forms = array();

	function __construct() {
	
		/* Plugin slug and version */
		$this->slug = 'userscontrol';
		$this->subslug = 'userscontrol-forms';
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$this->plugin_data = get_plugin_data( userscontrol_forms_path . 'index.php', false, false);
		$this->version = $this->plugin_data['Version'];
		
		/* Priority actions */
		add_action('admin_menu', array(&$this, 'add_menu'), 11);
		add_action('admin_enqueue_scripts', array(&$this, 'add_styles'), 9);
		add_action('admin_head', array(&$this, 'admin_head'), 9 );
		add_action('admin_init', array(&$this, 'admin_init'), 9);
		
		add_action( 'wp_ajax_userscontrol_edit_form', array(&$this, 'edit_form' ));
		add_action( 'wp_ajax_userscontrol_edit_form_conf', array(&$this, 'edit_form_conf' ));
		add_action( 'wp_ajax_userscontrol_edit_form_del', array(&$this, 'edit_form_del' ));
	}
	
	function admin_init() {
		$this->tabs = array(
			'manage' => __('Manage Forms','users-control')
			
		);
		$this->default_tab = 'manage';		
	}
	
	public function get_all (){
		global $wpdb;
		$forms = array();		
		$forms = get_option('userscontrol_custom_forms_collection');		
		return $forms;
	}
	
	public function get_copy_paste_shortocde ($id){
		$html = '';		
		$html .= "[userscontrol_user_signup form_id='".$id."']";		
		return $html;	
	}
	
	public function edit_form_del (){
		global $wpdb;		
		$form_id =sanitize_text_field( $_POST["form_id"]);			
		
		if($form_id!= ""){
			$forms = get_option('userscontrol_custom_forms_collection');
			$pos = $form_id;
			unset($forms[$pos]);
			ksort($forms);
			print_r($forms);
			update_option('userscontrol_custom_forms_collection', $forms);
			//delete from 
			$custom_form = 'userscontrol_profile_fields_'.$form_id;
			delete_option($custom_form);
			die();
		}
	}

	public function edit_form_conf (){
		global $wpdb, $userscontrol;		
		$form_id = sanitize_text_field($_POST['form_id']);

		if($_POST['form_id']!="" && $_POST['form_name']!="" ){				
			$forms = get_option('userscontrol_custom_forms_collection');			
			$forms[$form_id] =  array('name' =>sanitize_text_field($_POST['form_name']), 'role' =>$_POST['p_role']);
			ksort($forms);		
			update_option('userscontrol_custom_forms_collection', $forms);
		}		
		echo wp_kses($html, $userscontrol->allowed_html);
		die();
	}
	
	function genRandomString(){
		$length = 5;
		$characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWZYZ";		
		$real_string_legnth = strlen($characters) ;
		$string="";		
		for ($p = 0; $p < $length; $p++){
			$string .= $characters[mt_rand(0, $real_string_legnth-1)];
		}		
		return strtolower($string);
	}
	
	public function edit_form (){
		global  $userscontrol;
		
		$html ='';
		$form_id = sanitize_text_field($_POST['form_id']);
		$forms = get_option('userscontrol_custom_forms_collection');			
		$form = $forms[$form_id] ;
		$form_role =$form['role'];

		$html .="<p>".__( 'Name:', 'users-control')."</p>";				
		$html .="<p><input type='text' value='".$form['name']."' class='userscontrol-input' id='userscontrol_form_name_edit_".$form_id."'></p>";
		$html .="<p><input type='button' class='button-primary userscontrol-form-close' value='".__( 'Close', 'users-control')."' data-form= ".$form_id."> <input type='button'  class='button-primary userscontrol-form-modify' form-id= ".$form_id." value='".__( 'Save', 'users-control')."'> </p>";
		echo wp_kses($html, $userscontrol->allowed_html);
		die();
		
	}	
	function admin_head(){

	}

	function add_styles(){	
		wp_register_script( 'userscontrol_forms_js', userscontrol_forms_url . 'admin/scripts/admin.js', array( 
			'jquery'
		) );
		wp_enqueue_script( 'userscontrol_forms_js' );
		wp_register_style('userscontrol_forms_css', userscontrol_forms_url . 'admin/css/admin.css');
		wp_enqueue_style('userscontrol_forms_css');
	}
	
	function add_menu(){
		add_submenu_page( 'userscontrol', __('Custom Forms','users-control'), __('Custom Forms','users-control'), 'manage_options', 'userscontrol-forms', array(&$this, 'admin_page') );
	}

	function admin_tabs( $current = null ) {
			$tabs = $this->tabs;
			$links = array();
			if ( isset ( $_GET['tab'] ) ) {
				$current = sanitize_text_field($_GET['tab']);
			} else {
				$current = $this->default_tab;
			}
			foreach( $tabs as $tab => $name ) :
				if ( $tab == $current ) :
					$links[] = "<a class='nav-tab nav-tab-active' href='?page=".$this->subslug."&tab=$tab'>$name</a>";
				else :
					$links[] = "<a class='nav-tab' href='?page=".$this->subslug."&tab=$tab'>$name</a>";
				endif;
			endforeach;
			foreach ( $links as $link )
				echo esc_url($link);
	}

	function get_tab_content() {
		$screen = get_current_screen();
		if( strstr($screen->id, $this->subslug ) ) {
			if ( isset ( $_GET['tab'] ) ) {
				$tab = sanitize_text_field($_GET['tab']);
			} else {
				$tab = $this->default_tab;
			}
			require_once userscontrol_forms_path.'admin/panels/'.$tab.'.php';
		}
	}
	
	public function save(){
		global $wpdb, $userscontrol;
		
		if(isset($_POST['form_name'])  && $_POST['form_name']!=""){
			$slug = $this->genRandomString();
			$forms = get_option('userscontrol_custom_forms_collection');
			$new_form[$slug] =  array('name' =>sanitize_text_field($_POST['form_name']), 'role' =>sanitize_text_field($_POST['p_role']));
			if(is_array($forms)){
				$new_forms = array_merge($forms, $new_form);	
			}else{				
				$new_forms =  $new_form;	
			}
			
			ksort($new_forms);			
			update_option('userscontrol_custom_forms_collection',$new_forms);					
			$message = '<div class="updated"><p><strong>'.__('New form has been created.','users-control').'</strong></p></div>';
			echo wp_kses($message, $userscontrol->allowed_html);
		}else{
			$message = '<div class="error"><p><strong>'.__('Please input a name for the new form.','users-control').'</strong></p></div>';
			echo wp_kses($message, $userscontrol->allowed_html);
		}
	}
	
	
	function admin_page() {	
		if (isset($_POST['add-form']) && $_POST['add-form']=='add-form') {
			$this->save();
		}
	?>
	
		<div class="wrap <?php echo $this->slug; ?>-admin">
           <h2>USERS CONTROL FORMS - <?php _e('FORMS','users-control'); ?></h2>
           <div id="icon-users" class="icon32"></div>			
			
			<div class="<?php echo $this->slug; ?>-admin-contain">
				<?php $this->get_tab_content(); ?>
				<div class="clear"></div>
			</div>
		</div>
	<?php }
}
$userscontrol_form = new UsersControlForm();