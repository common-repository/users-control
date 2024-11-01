<?php
class UserscontrolProfile
{
	var $table_prfix = 'userscontrol';
	var $ajax_p = 'userscontrol';
    
    var $get_sucess_message_reset;
	
	function __construct() 	{
		$this->current_page = sanitize_url($_SERVER['REQUEST_URI']);
		$this->include_for_validation = array('text','fileupload','textarea','select','radio','checkbox','password');		
		add_action( 'init',   array(&$this,'profile_shortcodes'));	
		add_action( 'init', array($this, 'handle_init' ) );
		add_action('wp_enqueue_scripts', array(&$this, 'add_front_end_styles'), 11);		
		add_action( 'wp_ajax_'.$this->ajax_p.'_confirm_reset_password', array( $this, 'confirm_reset_password' ));		
		add_action( 'wp_ajax_nopriv_'.$this->ajax_p.'_confirm_reset_password', array( $this, 'confirm_reset_password' ));
		
		add_action( 'wp_ajax_'.$this->ajax_p.'_confirm_reset_password_user', array( $this, 'confirm_reset_password_user' ));
		add_action( 'wp_ajax_'.$this->ajax_p.'_confirm_update_email_user', array( $this, 'confirm_update_email_user' ));		
		add_action( 'wp_ajax_'.$this->ajax_p.'_update_personal_data_profile', array( $this, 'update_personal_data_profile' ));	
		
		add_action( 'wp_ajax_'.$this->ajax_p.'_ajax_upload_avatar_staff', array( &$this, 'userscontrol_ajax_upload_avatar' ));
		add_action( 'wp_ajax_'.$this->ajax_p.'_crop_avatar_user_profile_image_staff', array( &$this, 'userscontrol_crop_avatar_user_profile_image' ));
		
		add_action( 'wp_ajax_'.$this->ajax_p.'_ajax_upload_cover_profile', array( &$this, 'upload_cover_profile' ));
		add_action( 'wp_ajax_'.$this->ajax_p.'_crop_cover_profile', array( &$this, 'crop_cover_profile' ));
		add_action( 'wp_ajax_'.$this->ajax_p.'_delete_user_avatar_staff', array( &$this, 'delete_user_avatar' ));
		
		
	
		/* Remove bar except for admins */
		add_action('init', array(&$this, 'remove_admin_bar'), 9);
		
	
	}
	
	
	/* front styles */
	public function add_front_end_styles()	{
		global $wp_locale, $userscontrol;
		
		$theme_path = get_template_directory();	
	
		/* Custom style */		
		wp_register_style('userscontrol_account_style', userscontrol_url.'templates/basic/user-account-styles.css',null,null);
		wp_enqueue_style('userscontrol_account_style');	
			
		
		$date_picker_format = $userscontrol->get_date_picker_format();
		
		wp_register_script( 'userscontrol_profile_js', userscontrol_url.'js/userscontrol-profile.js', array( 
			'jquery','jquery-ui-core','jquery-ui-autocomplete'),null );
		wp_enqueue_script( 'userscontrol_profile_js' );
		
		 wp_localize_script( 'userscontrol_profile_js', 'userscontrol_profile_v98', array(
            'msg_wait_cropping'  => __( 'Please wait ...', 'users-control' ) ,
			'msg_wait'  => __( '<img src="'.userscontrol_url.'/templates/images/loaderB16.gif" width="16" height="16" /> &nbsp; Please wait ... ', 'users-control' ) ,
			
			'msg_ticket_empty_reply'  => '<div class="userscontrol-ultra-error"><span><i class="fa fa-ok"></i>'.__('ERROR!. Please write a message ',"wpticku").'</span></div>' ,
			'msg_ticket_submiting_reply'  => '<div class="userscontrol-ultra-wait"><span><i class="fa fa-ok"></i>'.__(' <img src="'.userscontrol_url.'/templates/images/loaderB16.gif" width="16" height="16" /> &nbsp; Please wait ... ',"wpticku").'</span></div>' ,
			'msg_make_selection'  => __( 'You must make a selection first', 'users-control' ) ,
			'msg_wait_reschedule'  => __( 'Please wait ...', 'users-control' ) ,
			
			'err_message_private_credential_title'  => __( 'Please input a name', 'users-control' ) ,
			
			'err_message_private_notes_title'  => __( 'Please input a name', 'users-control' ) ,
			
			
			'are_you_sure'     => __( 'Are you sure?',     'users-control' ),			
			'err_message_note_title'  => __( 'Please input a title', 'users-control' ) ,
			'err_message_note_text'  => __( 'Please write a message', 'users-control' ),
			
						
			'bb_date_picker_format'     => $date_picker_format                
            
        ) );
		
		
		//localize our js
		$date_picker_array = array(
					'closeText'         => __( 'Done', 'users-control' ),
					'currentText'       => __( 'Today', 'users-control' ),
					'prevText' =>  __('Prev','users-control'),
		            'nextText' => __('Next','users-control'),				
					'monthNames'        => array_values( $wp_locale->month ),
					'monthNamesShort'   => array_values( $wp_locale->month_abbrev ),
					'monthStatus'       => __( 'Show a different month', 'users-control' ),
					'dayNames'          => array_values( $wp_locale->weekday ),
					'dayNamesShort'     => array_values( $wp_locale->weekday_abbrev ),
					'dayNamesMin'       => array_values( $wp_locale->weekday_initial ),					
					// get the start of week from WP general setting
					'firstDay'          => get_option( 'start_of_week' ),
					// is Right to left language? default is false
					'isRTL'             => $wp_locale->is_rtl(),
				);
				
				
		wp_localize_script('userscontrol_profile_js', 'EASYWPMDatePicker', $date_picker_array);
		
	}
	
	function handle_init() {
		
		/*Form is when login*/
		if (isset($_POST['userscontrol-client-form-confirm'])) {
						
			/* Prepare array of fields */
			$this->prepare( $_POST );
			
			// Setting default to false;
			$this->errors = false;
			
			/* Validate, get errors, etc before we login a user */
			$this->handle();

		}
		
		/*Form reset password*/
		if (isset($_POST['userscontrol-client-recover-pass-form-confirm'])) {						
					
			// Setting default to false;
			$this->errors = false;
			
			/* Validate, get errors, etc before we login a user */
			$this->handle_password_reset();

		}
		
		/*Registration Form is fired*/
		if (isset($_POST['userscontrol-client-form-registration-confirm'])) {
			$this->prepare( $_POST );			
			$this->errors = false;			
			$this->handle_registration();

		}
		
		
		/*Upgrade Form is fired*/
		if (isset($_POST['userscontrol-client-form-upgrade-confirm'])) {
						
			$this->prepare( $_POST );			
			$this->errors = false;			
			$this->handle_registration_upgrade_subscription();

		}

		/*Handle Acctoun Verification */
		if (isset($_GET['act_link'])) {					
			/* */
			$this->handle_account_conf_link();
		}
		
				
	}
	
	function remove_admin_bar() {
		global  $userscontrol;
		if (!current_user_can('manage_options') && !is_admin())	{
			
			if ($userscontrol->get_option('hide_admin_bar')==1)	{				
				show_admin_bar(false);
			}
		}
	}
	
	function is_user_admin($user_id) {
		global  $userscontrol;
		
		if(user_can( $user_id, 'manage_options' ))
		{
			return true;
			
		
		}else{
			
			return false;
			
		
		}
		
		
	}
	
	
		/* Get picture by ID */
	function get_user_pic( $id, $size, $pic_type=NULL, $pic_boder_type= NULL, $size_type=NULL, $with_url=true ){
  	
		global  $userscontrol;
		$dimension_2 = "";	
		 
		$site_url = site_url()."/";
		
		//rand_val_cache		
		$cache_rand = time();
			 
		$avatar = "";
		$pic_size = "";		
		$upload_dir = wp_upload_dir(); 
		$path =   $upload_dir['baseurl']."/".WPUSERSCONTROL_MEDIAFOLDER.'/'.$id."/";
		$author_pic = get_the_author_meta('user_pic', $id);
		//get user url
		$user_url=$userscontrol->user->get_user_profile_permalink($id);
		
		if($pic_boder_type=='none'){$pic_boder_type='userscontrol-none';}
		
		
		if($size_type=="fixed" || $size_type==""){
			$dimension = "max-width:";
			$dimension_2 = "max-height:";
		}
		
		if($size_type=="dynamic" ){
			$dimension = "max-width:";
		}
		
		if($size!=""){
			$pic_size = $dimension.$size."px".";".$dimension_2.$size."px";
		}
		
		if($userscontrol->get_option('userscontrol_force_cache_issue')=='yes'){
			$cache_by_pass = '?rand_cache='.$cache_rand;
		}
		
		$user = get_user_by( 'id', $id );
		if ($author_pic  != '')	{
			$avatar_pic = $path.$author_pic;			
			if($with_url){
		 
					$avatar= '<a href="'.$user_url.'">'. '<img src="'.$avatar_pic.'" class="avatar '.$pic_boder_type.'" style="'.$pic_size.' "   id="userscontrol-avatar-img-'.$id.'" title="'.$user->display_name.'" /></a>';
				
			}else{
					
					$avatar=  '<img src="'.$avatar_pic.'" class="avatar '.$pic_boder_type.'" style="'.$pic_size.' "   id="userscontrol-avatar-img-'.$id.'" title="'.$user->display_name.'" />';
				
			}
				
				
				
			} else {
				
				$user = get_user_by( 'id', $id );		
				$avatar = get_avatar( $user->user_email, $size );
		
	    	}
		
		return $avatar;
	}

	public function profile_cover_uploader($staff_id=NULL) 	{
		
		// Uploading functionality trigger:
	   // (Most of the code comes from media.php and handlers.js)
		   $template_dir = get_template_directory_uri();
		   $avatar_is_called = "";
		   $my_account_url = '';
		   
		   
		   $plupload_init = array(
				 'runtimes'            => 'html5,silverlight,flash,html4',
				 'browse_button'       => 'plupload-browse-button-avatar',
				 'container'           => 'plupload-upload-ui-avatar',
				 'drop_element'        => 'userscontrol-drag-avatar-section',
				 'file_data_name'      => 'async-upload',
				 'multiple_queues'     => true,
				 'multi_selection'	  => false,
				 'max_file_size'       => wp_max_upload_size().'b',
				 //'max_file_size'       => get_option('drag-drop-filesize').'b',
				 'url'                 => admin_url('admin-ajax.php'),
				 'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
				 'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
				 //'filters'             => array(array('title' => __('Allowed Files', $this->text_domain), 'extensions' => "jpg,png,gif,bmp,mp4,avi")),
				 'filters'             => array(array('title' => __('Allowed Files', 'users-control'), 'extensions' => "jpg,png,gif,jpeg")),
				 'multipart'           => true,
				 'urlstream_upload'    => true,
 
				 // Additional parameters:
				 'multipart_params'    => array(
					 '_ajax_nonce' => wp_create_nonce('photo-upload'),
					 'staff_id' => $staff_id,
					 'action'      => 'userscontrol_ajax_upload_cover_profile' // The AJAX action name
					 
				 ),
			 );
			 
			 //print_r($plupload_init);
 
			 // Apply filters to initiate plupload:
			 $plupload_init = apply_filters('plupload_init', $plupload_init); 
 ?>
		 
		 <div id="uploadContainer" style="margin-top: 10px;">
			 
			 
			 <!-- Uploader section -->
			 <div id="uploaderSection" style="position: relative;">
				 <div id="plupload-upload-ui-avatar" class="hide-if-no-js">
				 
					 <div id="drag-drop-area-avatar">
						 <div class="drag-drop-inside">
							 <p class="drag-drop-info"><?php	_e('Drop '.$avatar_is_called.' here', 'users-control') ; ?></p>
							 <p><?php _ex('or', 'Uploader: Drop files here - or - Select Files'); ?></p>
														 
							 
							 <p>
													   
							 <button name="plupload-browse-button-avatar" id="plupload-browse-button-avatar" class="userscontrol-button-upload-avatar" type="button"><span><i class="fa fa-camera"></i></span> <?php	_e('Select Image', 'users-control') ; ?>	</button>
							 </p>
							 
							 <p>
													   
							 <button name="plupload-browse-button-avatar" id="btn-delete-user-avatar" class="userscontrol-button-delete-avatar" user-id="<?php echo esc_attr($staff_id)?>" redirect-avatar="yes" type="button"><span><i class="fa fa-times"></i></span> <?php	_e('Remove', 'users-control') ; ?>	</button>
							 </p>
							 
							 <p>
							 <a href="?module=main" class="userscontrol-remove-cancel-avatar-btn"><?php	_e('Cancel', 'users-control') ; ?></a>
							 </p>
														 
							
														 
						 </div>
						 
						 <div id="progressbar-avatar"></div>                 
						  <div id="userscontrol_filelist_avatar" class="cb"></div>
					 </div>
				 </div>
				 
				  
			 
			 </div>
			 
			
		 </div>
		 
		  <form id="userscontrol_frm_img_cropper" name="userscontrol_frm_img_cropper" method="post">                
				 
					 <input type="hidden" name="image_to_crop" value="" id="image_to_crop" />
					 <input type="hidden" name="crop_image" value="crop_image" id="crop_image" />
					 
					 <input type="hidden" name="site_redir" value="<?php echo esc_url($my_account_url."?module=upload_cover");?>" id="site_redir" />                   
				 
				 </form>
 
		 <?php
			 
			 ?>
 
			 <script type="text/javascript">
			 
				 jQuery(document).ready(function($){
					 
					 // Create uploader and pass configuration:
					 var uploader_avatar = new plupload.Uploader(<?php echo json_encode($plupload_init); ?>);
 
					 // Check for drag'n'drop functionality:
					 uploader_avatar.bind('Init', function(up){
						 
						 var uploaddiv_avatar = $('#plupload-upload-ui-avatar');
						 
						 // Add classes and bind actions:
						 if(up.features.dragdrop){
							 uploaddiv_avatar.addClass('drag-drop');
							 
							 $('#drag-drop-area-avatar')
								 .bind('dragover.wp-uploader', function(){ uploaddiv_avatar.addClass('drag-over'); })
								 .bind('dragleave.wp-uploader, drop.wp-uploader', function(){ uploaddiv_avatar.removeClass('drag-over'); });
 
						 } else{
							 uploaddiv_avatar.removeClass('drag-drop');
							 $('#drag-drop-area').unbind('.wp-uploader');
						 }
 
					 });
 
					 
					 // Init ////////////////////////////////////////////////////
					 uploader_avatar.init(); 
					 
					 // Selected Files //////////////////////////////////////////
					 uploader_avatar.bind('FilesAdded', function(up, files) {
						 
						 
						 var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10);
						 
						 // Limit to one limit:
						 if (files.length > 1){
							 alert("<?php _e('You may only upload one image at a time!', 'users-control'); ?>");
							 return false;
						 }
						 
						 // Remove extra files:
						 if (up.files.length > 1){
							 up.removeFile(uploader_avatar.files[0]);
							 up.refresh();
						 }
						 
						 // Loop through files:
						 plupload.each(files, function(file){
							 
							 // Handle maximum size limit:
							 if (max > hundredmb && file.size > hundredmb && up.runtime != 'html5'){
								 alert("<?php _e('The file you selected exceeds the maximum filesize limit.', 'users-control'); ?>");
								 return false;
							 }
						 
						 });
						 
						 jQuery.each(files, function(i, file) {
							 jQuery('#userscontrol_filelist_avatar').append('<div class="addedFile" id="' + file.id + '">' + file.name + '</div>');
						 });
						 
						 up.refresh(); 
						 uploader_avatar.start();
						 
					 });
					 
					 // A new file was uploaded:
					 uploader_avatar.bind('FileUploaded', function(up, file, response){					
						 
						 
						 
						 var obj = jQuery.parseJSON(response.response);												
						 var img_name = obj.image;							
						 
						 $("#image_to_crop").val(img_name);
						 $("#userscontrol_frm_img_cropper").submit();				 
						 
						 
						 jQuery.ajax({
							 type: 'POST',
							 url: ajaxurl,
							 data: {"action": "refresh_avatar"},
							 
							 success: function(data){
								 
								 //$( "#uu-upload-avatar-box" ).slideUp("slow");								
								 $("#uu-backend-avatar-section").html(data);
								 
								 //jQuery("#uu-message-noti-id").slideDown();
								 //setTimeout("hidde_noti('uu-message-noti-id')", 3000)	;
								 
								 
								 }
						 });
						 
						 
					 
					 });
					 
					 // Error Alert /////////////////////////////////////////////
					 uploader_avatar.bind('Error', function(up, err) {
						 alert("Error: " + err.code + ", Message: " + err.message + (err.file ? ", File: " + err.file.name : "") + "");
						 up.refresh(); 
					 });
					 
					 // Progress bar ////////////////////////////////////////////
					 uploader_avatar.bind('UploadProgress', function(up, file) {
						 
						 var progressBarValue = up.total.percent;
						 
						 jQuery('#progressbar-avatar').fadeIn().progressbar({
							 value: progressBarValue
						 });
						 
						 jQuery('#progressbar-avatar').html('<span class="progressTooltip">' + up.total.percent + '%</span>');
					 });
					 
					 // Close window after upload ///////////////////////////////
					 uploader_avatar.bind('UploadComplete', function() {
						 
						 //jQuery('.uploader').fadeOut('slow');						
						 jQuery('#progressbar-avatar').fadeIn().progressbar({
							 value: 0
						 });
						 
						 
					 });
					 
					 
					 
				 });
				 
					 
			 </script>
			 
		 <?php
	 
	 
	 }
	
	public function avatar_uploader($staff_id=NULL) 	{
		
	   // Uploading functionality trigger:
	  // (Most of the code comes from media.php and handlers.js)
	      $template_dir = get_template_directory_uri();
          $avatar_is_called = "";
		  $my_account_url = '';
		  
		  
		  $plupload_init = array(
				'runtimes'            => 'html5,silverlight,flash,html4',
				'browse_button'       => 'plupload-browse-button-avatar',
				'container'           => 'plupload-upload-ui-avatar',
				'drop_element'        => 'userscontrol-drag-avatar-section',
				'file_data_name'      => 'async-upload',
				'multiple_queues'     => true,
				'multi_selection'	  => false,
				'max_file_size'       => wp_max_upload_size().'b',
				//'max_file_size'       => get_option('drag-drop-filesize').'b',
				'url'                 => admin_url('admin-ajax.php'),
				'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
				'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
				//'filters'             => array(array('title' => __('Allowed Files', $this->text_domain), 'extensions' => "jpg,png,gif,bmp,mp4,avi")),
				'filters'             => array(array('title' => __('Allowed Files', 'users-control'), 'extensions' => "jpg,png,gif,jpeg")),
				'multipart'           => true,
				'urlstream_upload'    => true,

				// Additional parameters:
				'multipart_params'    => array(
					'_ajax_nonce' => wp_create_nonce('photo-upload'),
					'staff_id' => $staff_id,
					'action'      => 'userscontrol_ajax_upload_avatar_staff' // The AJAX action name
					
				),
			);
			
			//print_r($plupload_init);

			// Apply filters to initiate plupload:
			$plupload_init = apply_filters('plupload_init', $plupload_init); 
?>
		
		<div id="uploadContainer" style="margin-top: 10px;">
			
			
			<!-- Uploader section -->
			<div id="uploaderSection" style="position: relative;">
				<div id="plupload-upload-ui-avatar" class="hide-if-no-js">
                
					<div id="drag-drop-area-avatar">
						<div class="drag-drop-inside">
							<p class="drag-drop-info"><?php	_e('Drop '.$avatar_is_called.' here', 'users-control') ; ?></p>
							<p><?php _ex('or', 'Uploader: Drop files here - or - Select Files'); ?></p>
							                            
                            
							<p>
                                                      
                            <button name="plupload-browse-button-avatar" id="plupload-browse-button-avatar" class="userscontrol-button-upload-avatar" type="button"><span><i class="fa fa-camera"></i></span> <?php	_e('Select Image', 'users-control') ; ?>	</button>
                            </p>
                            
                            <p>
                                                      
                            <button name="plupload-browse-button-avatar" id="btn-delete-user-avatar" class="userscontrol-button-delete-avatar" user-id="<?php echo esc_attr($staff_id)?>" redirect-avatar="yes" type="button"><span><i class="fa fa-times"></i></span> <?php	_e('Remove', 'users-control') ; ?>	</button>
                            </p>
                            
                            <p>
                            <a href="?module=main" class="userscontrol-remove-cancel-avatar-btn"><?php	_e('Cancel', 'users-control') ; ?></a>
                            </p>
                                                        
                           
														
						</div>
                        
                        <div id="progressbar-avatar"></div>                 
                         <div id="userscontrol_filelist_avatar" class="cb"></div>
					</div>
				</div>
                
                 
			
			</div>
            
           
		</div>
        
         <form id="userscontrol_frm_img_cropper" name="userscontrol_frm_img_cropper" method="post">                
                
                	<input type="hidden" name="image_to_crop" value="" id="image_to_crop" />
                    <input type="hidden" name="crop_image" value="crop_image" id="crop_image" />
                    
                    <input type="hidden" name="site_redir" value="<?php echo esc_url($my_account_url."?module=upload_avatar");?>" id="site_redir" />                   
                
                </form>

		<?php
			
			?>

			<script type="text/javascript">
			
				jQuery(document).ready(function($){
					
					// Create uploader and pass configuration:
					var uploader_avatar = new plupload.Uploader(<?php echo json_encode($plupload_init); ?>);

					// Check for drag'n'drop functionality:
					uploader_avatar.bind('Init', function(up){
						
						var uploaddiv_avatar = $('#plupload-upload-ui-avatar');
						
						// Add classes and bind actions:
						if(up.features.dragdrop){
							uploaddiv_avatar.addClass('drag-drop');
							
							$('#drag-drop-area-avatar')
								.bind('dragover.wp-uploader', function(){ uploaddiv_avatar.addClass('drag-over'); })
								.bind('dragleave.wp-uploader, drop.wp-uploader', function(){ uploaddiv_avatar.removeClass('drag-over'); });

						} else{
							uploaddiv_avatar.removeClass('drag-drop');
							$('#drag-drop-area').unbind('.wp-uploader');
						}

					});

					
					// Init ////////////////////////////////////////////////////
					uploader_avatar.init(); 
					
					// Selected Files //////////////////////////////////////////
					uploader_avatar.bind('FilesAdded', function(up, files) {
						
						
						var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10);
						
						// Limit to one limit:
						if (files.length > 1){
							alert("<?php _e('You may only upload one image at a time!', 'users-control'); ?>");
							return false;
						}
						
						// Remove extra files:
						if (up.files.length > 1){
							up.removeFile(uploader_avatar.files[0]);
							up.refresh();
						}
						
						// Loop through files:
						plupload.each(files, function(file){
							
							// Handle maximum size limit:
							if (max > hundredmb && file.size > hundredmb && up.runtime != 'html5'){
								alert("<?php _e('The file you selected exceeds the maximum filesize limit.', 'users-control'); ?>");
								return false;
							}
						
						});
						
						jQuery.each(files, function(i, file) {
							jQuery('#userscontrol_filelist_avatar').append('<div class="addedFile" id="' + file.id + '">' + file.name + '</div>');
						});
						
						up.refresh(); 
						uploader_avatar.start();
						
					});
					
					// A new file was uploaded:
					uploader_avatar.bind('FileUploaded', function(up, file, response){					
						
						
						
						var obj = jQuery.parseJSON(response.response);												
						var img_name = obj.image;							
						
						$("#image_to_crop").val(img_name);
						$("#userscontrol_frm_img_cropper").submit();

						
						
						
						jQuery.ajax({
							type: 'POST',
							url: ajaxurl,
							data: {"action": "refresh_avatar"},
							
							success: function(data){
								
								//$( "#uu-upload-avatar-box" ).slideUp("slow");								
								$("#uu-backend-avatar-section").html(data);
								
								//jQuery("#uu-message-noti-id").slideDown();
								//setTimeout("hidde_noti('uu-message-noti-id')", 3000)	;
								
								
								}
						});
						
						
					
					});
					
					// Error Alert /////////////////////////////////////////////
					uploader_avatar.bind('Error', function(up, err) {
						alert("Error: " + err.code + ", Message: " + err.message + (err.file ? ", File: " + err.file.name : "") + "");
						up.refresh(); 
					});
					
					// Progress bar ////////////////////////////////////////////
					uploader_avatar.bind('UploadProgress', function(up, file) {
						
						var progressBarValue = up.total.percent;
						
						jQuery('#progressbar-avatar').fadeIn().progressbar({
							value: progressBarValue
						});
						
						jQuery('#progressbar-avatar').html('<span class="progressTooltip">' + up.total.percent + '%</span>');
					});
					
					// Close window after upload ///////////////////////////////
					uploader_avatar.bind('UploadComplete', function() {
						
						//jQuery('.uploader').fadeOut('slow');						
						jQuery('#progressbar-avatar').fadeIn().progressbar({
							value: 0
						});
						
						
					});
					
					
					
				});
				
					
			</script>
			
		<?php
	
	
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

	//crop cover image
	function crop_cover_profile(){
		global $userscontrol;
		global $wpdb;
			
		$site_url = site_url()."/";		
		
		/// Upload file using Wordpress functions:
		$x1 = sanitize_text_field($_POST['x1']);
		$y1 = sanitize_text_field($_POST['y1']);		
		$x2 = sanitize_text_field($_POST['x2']);
		$y2= sanitize_text_field($_POST['y2']);
		$w = sanitize_text_field($_POST['w']);
		$h = sanitize_text_field($_POST['h']);			
		$image_id =   sanitize_text_field($_POST['image_id']);
		
		$current_user = $userscontrol->user->get_user_info();
		$user_id = $current_user->ID;	
			
		if($user_id==''){echo esc_attr('error');exit();}			
			
		$userscontrol->imagecrop->setDimensions($x1, $y1, $w, $h)	;
			
		$upload_dir = wp_upload_dir(); 
		$path_pics =   $upload_dir['basedir'];		
		$src = $path_pics.'/'.WPUSERSCONTROL_MEDIAFOLDER.'/'.$user_id.'/'.$image_id;

		if(!is_dir($path_pics.'/'.WPUSERSCONTROL_MEDIAFOLDER)) {
			wp_mkdir_p( $path_pics.'/'.WPUSERSCONTROL_MEDIAFOLDER );							   
		}
		
		if(!is_dir($path_pics.'/'.WPUSERSCONTROL_MEDIAFOLDER.'/'.$user_id)) {
			wp_mkdir_p( $path_pics.'/'.WPUSERSCONTROL_MEDIAFOLDER.'/'.$user_id );							   
		}


		
		//new random image and crop procedure				
		$userscontrol->imagecrop->setImage($src);
		$userscontrol->imagecrop->createThumb();		
		$info = pathinfo($src);
		$ext = $info['extension'];
		$ext=strtolower($ext);		
		$new_i = 'profile_cover_cropp_'.time().".". $ext;		
		$new_name =  $path_pics.'/'.WPUSERSCONTROL_MEDIAFOLDER.'/'.$user_id.'/'.$new_i;				
		$userscontrol->imagecrop->renderImage($new_name);
		//end cropping
			
		//check if there is another avatar						
		$user_pic = get_user_meta($user_id, 'user_cover_profile', true);		

				
							
		if ( $user_pic!="" ){
			$path_avatar = $path_pics['baseurl']."/".$user_id."/".$image_id;					
				update_user_meta($user_id, 'user_cover_profile', $new_i);  
		}else{
				  //update meta
				update_user_meta($user_id, 'user_cover_profile', $new_i);
		}		  
			  

		
		// Create response array:
		$uploadResponse = array('image' => $new_name);		
		// Return response and exit:
		echo json_encode($uploadResponse);		
		die();		
	}
	
	//crop avatar image
	function userscontrol_crop_avatar_user_profile_image(){
		global $userscontrol;
		global $wpdb;
		
		$site_url = site_url()."/";		
	
		/// Upload file using Wordpress functions:
		$x1 = sanitize_text_field($_POST['x1']);
		$y1 = sanitize_text_field($_POST['y1']);		
		$x2 = sanitize_text_field($_POST['x2']);
		$y2= sanitize_text_field($_POST['y2']);
		$w = sanitize_text_field($_POST['w']);
		$h = sanitize_text_field($_POST['h']);			
		$image_id =   sanitize_text_field($_POST['image_id']);
	
		$current_user = $userscontrol->user->get_user_info();
		$user_id = $current_user->ID;	
		
		if($user_id==''){echo esc_attr('error');exit();}			
		
		$userscontrol->imagecrop->setDimensions($x1, $y1, $w, $h)	;
		
		$upload_dir = wp_upload_dir(); 
		$path_pics =   $upload_dir['basedir'];		
		$src = $path_pics.'/'.WPUSERSCONTROL_MEDIAFOLDER.'/'.$user_id.'/'.$image_id;

		if(!is_dir($path_pics.'/'.WPUSERSCONTROL_MEDIAFOLDER)) {
			wp_mkdir_p( $path_pics.'/'.WPUSERSCONTROL_MEDIAFOLDER );							   
		}
		
		if(!is_dir($path_pics.'/'.WPUSERSCONTROL_MEDIAFOLDER.'/'.$user_id)) {
			wp_mkdir_p( $path_pics.'/'.WPUSERSCONTROL_MEDIAFOLDER.'/'.$user_id );							   
		}
		
		//new random image and crop procedure				
		$userscontrol->imagecrop->setImage($src);
		$userscontrol->imagecrop->createThumb();		
		$info = pathinfo($src);
        $ext = $info['extension'];
		$ext=strtolower($ext);		
		$new_i = time().".". $ext;		
		$new_name =  $path_pics.'/'.WPUSERSCONTROL_MEDIAFOLDER.'/'.$user_id.'/'.$new_i;				
		$userscontrol->imagecrop->renderImage($new_name);
		//end cropping
		
		//check if there is another avatar						
		$user_pic = get_user_meta($user_id, 'user_pic', true);	
		
		//resize
		//check max width		
		$original_max_width = $userscontrol->get_option('media_avatar_width'); 
        $original_max_height =$userscontrol->get_option('media_avatar_height'); 
		
		if($original_max_width=="" || $original_max_height=="")	{			
			$original_max_width = 250;			
			$original_max_height = 250;			
		}
														
		list( $source_width, $source_height, $source_type ) = getimagesize($new_name);
		
		if($source_width > $original_max_width) {
			if ($this->image_resize($new_name, $new_name, $original_max_width, $original_max_height,0)) {
				$old = umask(0);
				chmod($new_name, 0755);
				umask($old);										
			}		
		}					
						
		if ( $user_pic!="" ){
			$path_avatar = $path_pics['baseurl']."/".$user_id."/".$image_id;					
			update_user_meta($user_id, 'user_pic', $new_i);  
		}else{
			  //update meta
			update_user_meta($user_id, 'user_pic', $new_i);
		}		  
		  
		if(file_exists($src)){
			unlink($src);
		}	 
	
		// Create response array:
		$uploadResponse = array('image' => $new_name);		
		// Return response and exit:
		echo json_encode($uploadResponse);		
		die();		
	}

	public function get_profile_bg($user_id){
		global $userscontrol;		
		require_once(ABSPATH . 'wp-includes/link-template.php');		
		$site_url = site_url()."/";
		$upload_dir = wp_upload_dir(); 
		$path_pics =   $upload_dir['basedir'];		

		$path =   $upload_dir['baseurl']."/".WPUSERSCONTROL_MEDIAFOLDER.'/'.$user_id."/";

		$html = "";		
	
		$user_pic = get_user_meta($user_id, 'user_cover_profile', true);		
		
		if($user_pic!="")
		{
			$src =$path .$user_pic;			
			$html .= '<img src="'.$src.'" id="uultra-profile-cover-horizontal"/>';			
		} 
		
		
		return $html;
	
	
	}
	
	public function get_profile_bg_url($user_id){
		global $userscontrol;
		
		require_once(ABSPATH . 'wp-includes/link-template.php');
		$upload_dir = wp_upload_dir(); 
		$path_pics =   $upload_dir['basedir'];	
		$path =   $upload_dir['baseurl']."/".WPUSERSCONTROL_MEDIAFOLDER.'/'.$user_id."/";

		$src = "";			
		$user_pic = get_user_meta($user_id, 'user_cover_profile', true);			
		
		if($user_pic!=""){
			$src = $path .$user_pic;			
		} 
		
		return $src;
	
	
	}
	
	function image_resize($src, $dst, $width, $height, $crop=0)	{
		
		  if(!list($w, $h) = getimagesize($src)) return "Unsupported picture type!";
		
		  $type = strtolower(substr(strrchr($src,"."),1));
		  if($type == 'jpeg') $type = 'jpg';
		  switch($type){
			case 'bmp': $img = imagecreatefromwbmp($src); break;
			case 'gif': $img = imagecreatefromgif($src); break;
			case 'jpg': $img = imagecreatefromjpeg($src); break;
			case 'png': $img = imagecreatefrompng($src); break;
			default : return "Unsupported picture type!";
		  }
		
		  // resize
		  if($crop){
			if($w < $width or $h < $height) return "Picture is too small!";
			$ratio = max($width/$w, $height/$h);
			$h = $height / $ratio;
			$x = ($w - $width / $ratio) / 2;
			$w = $width / $ratio;
		  }
		  else{
			if($w < $width and $h < $height) return "Picture is too small!";
			$ratio = min($width/$w, $height/$h);
			$width = $w * $ratio;
			$height = $h * $ratio;
			$x = 0;
		  }
		
		  $new = imagecreatetruecolor($width, $height);
		
		  // preserve transparency
		  if($type == "gif" or $type == "png"){
			imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
			imagealphablending($new, false);
			imagesavealpha($new, true);
		  }
		
		  imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);
		
		  switch($type){
			case 'bmp': imagewbmp($new, $dst); break;
			case 'gif': imagegif($new, $dst); break;
			case 'jpg': imagejpeg($new, $dst,100); break;
			case 'jpeg': imagejpeg($new, $dst,100); break;
			case 'png': imagepng($new, $dst,9); break;
		  }
		  return true;
	}
	
	function display_avatar_image_to_crop($image, $user_id=NULL){
		 global $userscontrol;
		
		/* Custom style */		
		wp_register_style( 'userscontrol_image_cropper_style',userscontrol_url.'js/cropper/cropper.min.css');
		wp_enqueue_style('userscontrol_image_cropper_style');	
					
		wp_enqueue_script('userscontrol_simple_cropper',  userscontrol_url.'js/cropper/cropper.min.js' , array('jquery'), false, false);
		
	  
	    $template_dir = get_template_directory_uri();				
		$site_url = site_url()."/";
		
		$html = "";
		
		$upload_dir = wp_upload_dir(); 
		$upload_folder =   $upload_dir['basedir'];		
				
		$user_pic = get_user_meta($user_id, 'user_profile_bg', true);		
		
		if($image!="")
		{
			$url_image_to_crop = $upload_dir['baseurl'].'/'.WPUSERSCONTROL_MEDIAFOLDER.'/'.$user_id.'/'.$image;			
			$html_image = '<img src="'.$url_image_to_crop.'" id="userscontrol-profile-cover-horizontal" />';					
			
		}
		
		$my_account_url = $userscontrol->user->get_my_account_direct_link ;
		
		
		
		?>
        
        
      	<div id="userscontrol-dialog-user-bg-cropper-div" class="userscontrol-dialog-user-bg-cropper"  >	
				<?php
				
				echo wp_kses($html_image, $userscontrol->allowed_html);?>                   
		</div>          
             
             
             <p>
                                                      
                            <button name="plupload-browse-button-avatar" id="userscontrol-confirm-avatar-cropping" class="userscontrol-button-upload-avatar" type="link"><span><i class="fa fa-crop"></i></span> <?php	_e('Crop & Save', 'users-control') ; ?>	</button>
                            <div class="userscontrol-please-wait-croppingmessage" id="userscontrol-cropping-avatar-wait-message">&nbsp;</div>
                            </p>                           
                            
                            <div class="userscontrol-uploader-buttons-delete-cancel" id="btn-cancel-avatar-cropping" >
                            <a href="<?php echo esc_url($my_account_url)?>" class="userscontrol-remove-cancel-avatar-btn"><?php	_e('Cancel', 'users-control') ; ?></a>
                            </div>
            
     			<input type="hidden" name="x1" value="0" id="x1" />
				<input type="hidden" name="y1" value="0" id="y1" />				
				<input type="hidden" name="w" value="<?php echo esc_attr($w)?>" id="w" />
				<input type="hidden" name="h" value="<?php echo $h?>" id="h" />
                <input type="hidden" name="image_id" value="<?php echo esc_attr($image)?>" id="image_id" />
                <input type="hidden" name="user_id" value="<?php echo esc_attr($user_id)?>" id="user_id" />
                <input type="hidden" name="site_redir" value="<?php echo esc_url($my_account_url."?module=upload_avatar&")?>" id="site_redir" />
                
		
		<script type="text/javascript">
		
		
				jQuery(document).ready(function($){
					
				
					<?php
					
					
					
					$source_img = $upload_folder.'/'.WPUSERSCONTROL_MEDIAFOLDER.'/'.$user_id.'/'.$image;	
									 
					 $r_width = $this->getWidth($source_img);
					 $r_height= $this->getHeight($source_img);
					 
					$original_max_width = $userscontrol->get_option('media_avatar_width'); 
					$original_max_height =$userscontrol->get_option('media_avatar_height'); 
					
					if($original_max_width=="" || $original_max_height=="")
					{			
						$original_max_width = 250;			
						$original_max_height = 250;
						
					}
					
					$aspectRatio = $original_max_width/$original_max_height;
					
					
					 
						 ?>
						var $image = jQuery(".userscontrol-dialog-user-bg-cropper img"),
						$x1 = jQuery("#x1"),
						$y1 = jQuery("#y1"),
						$h = jQuery("#h"),
						$w = jQuery("#w");
					
					$image.cropper({
								  aspectRatio: <?php echo esc_attr($aspectRatio)?>,
								  autoCropArea: 0.6, // Center 60%
								  zoomable: false,
								  preview: ".img-preview",
								  done: function(data) {
									$x1.val(Math.round(data.x));
									$y1.val(Math.round(data.y));
									$h.val(Math.round(data.height));
									$w.val(Math.round(data.width));
								  }
								});
			
			})	
				
									
			</script>
		
		
	<?php	
		
	}

	function display_cover_image_to_crop($image, $user_id=NULL)	{
		 global $userscontrol;
		
		/* Custom style */		
		wp_register_style( 'userscontrol_image_cropper_style',userscontrol_url.'js/cropper/cropper.min.css');
		wp_enqueue_style('userscontrol_image_cropper_style');	
					
		wp_enqueue_script('userscontrol_simple_cropper',  userscontrol_url.'js/cropper/cropper.min.js' , array('jquery'), false, false);
		
	  
	    $template_dir = get_template_directory_uri();				
		$site_url = site_url()."/";
		
		$html = "";
		
		$upload_dir = wp_upload_dir(); 
		$upload_folder =   $upload_dir['basedir'];		
				
		$user_pic = get_user_meta($user_id, 'user_profile_cover', true);		
		
		if($image!="")
		{
			$url_image_to_crop = $upload_dir['baseurl'].'/'.WPUSERSCONTROL_MEDIAFOLDER.'/'.$user_id.'/'.$image;			
			$html_image = '<img src="'.$url_image_to_crop.'" id="userscontrol-profile-cover-horizontal" />';					
			
		}
		
		$my_account_url = $userscontrol->user->get_my_account_direct_link ;
		
		
		
		?>
        
        
      	<div id="userscontrol-dialog-user-bg-cropper-div" class="userscontrol-dialog-user-bg-cropper"  >	
				<?php
				
				echo wp_kses($html_image, $userscontrol->allowed_html);?>                   
		</div>          
             
             
             <p>
                                                      
                            <button name="plupload-browse-button-avatar" id="userscontrol-confirm-cover-cropping" class="userscontrol-button-upload-avatar" type="link"><span><i class="fa fa-crop"></i></span> <?php	_e('Crop & Save', 'users-control') ; ?>	</button>
                            <div class="userscontrol-please-wait-croppingmessage" id="userscontrol-cropping-avatar-wait-message">&nbsp;</div>
                            </p>                           
                            
                            <div class="userscontrol-uploader-buttons-delete-cancel" id="btn-cancel-avatar-cropping" >
                            <a href="<?php echo esc_url($my_account_url)?>" class="userscontrol-remove-cancel-avatar-btn"><?php	_e('Cancel', 'users-control') ; ?></a>
                            </div>
            
     			<input type="hidden" name="x1" value="0" id="x1" />
				<input type="hidden" name="y1" value="0" id="y1" />				
				<input type="hidden" name="w" value="<?php echo esc_attr($w)?>" id="w" />
				<input type="hidden" name="h" value="<?php echo $h?>" id="h" />
                <input type="hidden" name="image_id" value="<?php echo esc_attr($image)?>" id="image_id" />
                <input type="hidden" name="user_id" value="<?php echo esc_attr($user_id)?>" id="user_id" />
                <input type="hidden" name="site_redir" value="<?php echo esc_url($my_account_url."?module=upload_cover&")?>" id="site_redir" />
                
		
		<script type="text/javascript">
		
		
				jQuery(document).ready(function($){
					
				
					<?php
					
					
					
					$source_img = $upload_folder.'/'.WPUSERSCONTROL_MEDIAFOLDER.'/'.$user_id.'/'.$image;	
									 
					 $r_width = $this->getWidth($source_img);
					 $r_height= $this->getHeight($source_img);					
					
					
					 
						 ?>
						var $image = jQuery(".userscontrol-dialog-user-bg-cropper img"),
						$x1 = jQuery("#x1"),
						$y1 = jQuery("#y1"),
						$h = jQuery("#h"),
						$w = jQuery("#w");
					
					$image.cropper({
								  aspectRatio: 2.6,
								  autoCropArea: 0.6, // Center 60%
								  zoomable: false,
								  preview: ".img-preview",
								  done: function(data) {
									$x1.val(Math.round(data.x));
									$y1.val(Math.round(data.y));
									$h.val(Math.round(data.height));
									$w.val(Math.round(data.width));
								  }
								});
			
			})	
				
									
			</script>
		
		
	<?php	
		
	}
	
	
	//You do not need to alter these functions
	function getHeight($image) {
		$size = getimagesize($image);
		$height = $size[1];
		return $height;
	}

	//You do not need to alter these functions
	function getWidth($image) {
		$size = getimagesize($image);
		$width = $size[0];
		return $width;
	}
	
	
	// File upload handler:
	function userscontrol_ajax_upload_avatar(){
		global $userscontrol;
		
		$site_url = site_url()."/";
		
		// Check referer, die if no ajax:
		check_ajax_referer('photo-upload');		
		/// Upload file using Wordpress functions:
		$file = $_FILES['async-upload'];		
		
		$original_max_width = $userscontrol->get_option('media_avatar_width'); 
        $original_max_height =$userscontrol->get_option('media_avatar_height'); 
		
		if($original_max_width=="" || $original_max_height=="")	{			
			$original_max_width = 100;			
			$original_max_height = 100;			
		}	
			
	
		$current_user = $userscontrol->user->get_user_info();
		$o_id = $current_user->ID;		

	
				
		$info = pathinfo($file['name']);
		$real_name = $file['name'];
        $ext = $info['extension'];
		$ext=strtolower($ext);
		
		$rand = $userscontrol->commmonmethods->getRandomString(10);
		$rand_name = "avatar_".$rand."_".session_id()."_".time();		
	
		$upload_dir = wp_upload_dir(); 
		$path_pics =   $upload_dir['basedir'];		
			
		if($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif'){
			if($o_id != ''){
				if(!is_dir($path_pics.'/'.WPUSERSCONTROL_MEDIAFOLDER)) {
					wp_mkdir_p( $path_pics.'/'.WPUSERSCONTROL_MEDIAFOLDER );							   
				}
				
				if(!is_dir($path_pics.'/'.WPUSERSCONTROL_MEDIAFOLDER.'/'.$o_id)) {
					wp_mkdir_p( $path_pics.'/'.WPUSERSCONTROL_MEDIAFOLDER.'/'.$o_id );							   
				}					
										
				$pathBig = $path_pics."/".WPUSERSCONTROL_MEDIAFOLDER.'/'.$o_id."/".$rand_name.".".$ext;						
					
					
				if (copy($file['tmp_name'], $pathBig)){

					//check auto-rotation						
					if($userscontrol->get_option('avatar_rotation_fixer')=='yes'){
						$this->orient_image($pathBig);
					}

						
				
						
							
						
						
						$new_avatar = $rand_name.".".$ext;						
						$new_avatar_url = $path.$rand_name.".".$ext;
						
						
						//check if there is another avatar						
						$user_pic = get_user_meta($o_id, 'user_pic', true);	
						
						
						
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

	// File upload handler:
	function upload_cover_profile(){
		global $userscontrol;
		
		$site_url = site_url()."/";
		
		// Check referer, die if no ajax:
		check_ajax_referer('photo-upload');		
		/// Upload file using Wordpress functions:
		$file = $_FILES['async-upload'];		
		
		$original_max_width = $userscontrol->get_option('media_avatar_width'); 
        $original_max_height =$userscontrol->get_option('media_avatar_height'); 
		
		if($original_max_width=="" || $original_max_height=="")	{			
			$original_max_width = 100;			
			$original_max_height = 100;			
		}	
			
	
		$current_user = $userscontrol->user->get_user_info();
		$o_id = $current_user->ID;		
	
				
		$info = pathinfo($file['name']);
		$real_name = $file['name'];
        $ext = $info['extension'];
		$ext=strtolower($ext);
		
		$rand = $userscontrol->commmonmethods->getRandomString(10);
		$rand_name = "cover_".$rand."_".session_id()."_".time();		
	
		$upload_dir = wp_upload_dir(); 
		$path_pics =   $upload_dir['basedir'];		
			
		if($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif'){
			if($o_id != ''){
				if(!is_dir($path_pics.'/'.WPUSERSCONTROL_MEDIAFOLDER)) {
					wp_mkdir_p( $path_pics.'/'.WPUSERSCONTROL_MEDIAFOLDER );							   
				}
				
				if(!is_dir($path_pics.'/'.WPUSERSCONTROL_MEDIAFOLDER.'/'.$o_id)) {
					wp_mkdir_p( $path_pics.'/'.WPUSERSCONTROL_MEDIAFOLDER.'/'.$o_id );							   
				}				
										
				$pathBig = $path_pics.'/'.WPUSERSCONTROL_MEDIAFOLDER."/".$o_id."/".$rand_name.".".$ext;						
					
					
				if (copy($file['tmp_name'], $pathBig)){

					
					$upload_folder = $userscontrol->get_option('media_uploading_folder');	
					if($upload_folder==''){
						$upload_folder = 'userscontrol-media';
					}			
					$path = $site_url.$upload_folder."/".$o_id."/";
						
										
						
					$new_avatar = $rand_name.".".$ext;						
					$new_avatar_url = $path.$rand_name.".".$ext;
						
								
					
						
					}
									
					
			     }  		
			
        } // image type
		
		// Create response array:
		$uploadResponse = array('image' => $new_avatar);
		
		// Return response and exit:
		echo json_encode($uploadResponse);
		die();
	}
	
	
	
	public function orient_image($file_path) 
	{
       
        $exif = @exif_read_data($file_path);
        if ($exif === false) {
            return false;
        }
        $orientation = intval(@$exif['Orientation']);
        if (!in_array($orientation, array(3, 6, 8))) {
            return false;
        }
        $image = @imagecreatefromjpeg($file_path);
        switch ($orientation) {
            case 3:
                $image = @imagerotate($image, 180, 0);
                break;
            case 6:
                $image = @imagerotate($image, 270, 0);
                break;
            case 8:
                $image = @imagerotate($image, 90, 0);
                break;
            default:
                return false;
        }
        $success = imagejpeg($image, $file_path);
        // Free up memory (imagedestroy does not delete files):
        @imagedestroy($image);
        return $success;
    }
	
	public function confirm_reset_password_user(){
		global $wpdb,  $userscontrol, $wp_rewrite;
	
		$wp_rewrite = new WP_Rewrite();		
		$user_id = get_current_user_id();		
				
		//check redir		
		$account_page_id = $userscontrol->get_option('login_page_id');		
		$my_account_url = get_permalink($account_page_id);		
		$PASSWORD_LENGHT =7;
		$password1 = sanitize_text_field($_POST['p1']);
		$password2 = sanitize_text_field($_POST['p2']);
		$html = '';
		$validation = '';		
	
		if($password1!=$password2){
			$validation .= "<div class='userscontrol-ultra-error'>".__(" ERROR! Password must be identical ", 'users-control')."</div>";
			$html = $validation;			
		}
		
		if(strlen($password1)<$PASSWORD_LENGHT){
			$validation .= "<div class='userscontrol-ultra-error'>".__(" ERROR! Password should contain at least 7 alphanumeric characters ", 'users-control')."</div>";
			$html = $validation;		
		}		
		
		if($validation=="" ){		
			if($user_id >0 ){
				$user = get_userdata($user_id);
				//print_r($user);
				$user_id = $user->ID;
				$user_email = $user->user_email;
				$user_login = $user->user_login;		
					
				wp_set_password( $password1, $user_id ) ;
					
				//notify user					
				$userscontrol->messaging->send_new_password_to_user($user, $password1);
					
				$html = "<div class='userscontrol-ultra-success'>".__(" Success!! The new password has been changed. Please click on the login link to get in your account.  ", 'users-control')."</div>";
					
				// Here is the magic:
				wp_cache_delete($user_id, 'users');
				wp_cache_delete($username, 'userlogins'); // This might be an issue for how you are doing it. Presumably you'd need to run this for the ORIGINAL user login name, not the new one.
				wp_logout();
				wp_signon(array('user_login' => $user_login, 'user_password' => $password1));
					
			}else{
								
			}
					
		}
		echo wp_kses($html, $userscontrol->allowed_html);
		die();
	}
	
	public function update_personal_data_profile(){
		global $wpdb,  $userscontrol, $wp_rewrite;
		
		$user_id = get_current_user_id();	
		$display_name = sanitize_text_field($_POST['userscontrol_display_name']);
		$country =  sanitize_text_field($_POST['userscontrol_country']);
		$city =  sanitize_text_field($_POST['userscontrol_city']);
		$address = sanitize_text_field($_POST['userscontrol_address']);
		$description = sanitize_text_field( $_POST['desc_text']);
		$summary = sanitize_text_field( $_POST['summary_text']);
		$html = '';
		$validation = '';
		
		wp_update_user( array( 'ID' => $user_id, 'display_name' => $display_name ) );
		//update meta
		update_user_meta ($user_id, 'userscontrol_description', $description);
		update_user_meta ($user_id, 'userscontrol_summary', $summary);	
		update_user_meta ($user_id, 'country', $country);
		update_user_meta ($user_id, 'city', $city);
		update_user_meta ($user_id, 'address', $address);
	    $html = "<div class='userscontrol-ultra-success'>".__(" Success!! Your Personal Details Were Updated  ", 'users-control')."</div>";
		echo wp_kses($html, $userscontrol->allowed_html);
		die();
	}
	
	
	public function confirm_update_email_user(){
		global $wpdb,  $userscontrol, $wp_rewrite;

		$wp_rewrite = new WP_Rewrite();
		$user_id = get_current_user_id();
	
		$email =  sanitize_text_field($_POST['email']);
		$html = '';
		$validation = '';
	
		//validate if it's a valid email address	
		$ret_validate_email = $this->validate_valid_email($email);
		
		if($email==""){
			$validation .= "<div class='userscontrol-ultra-error'>".__(" ERROR! Please type your new email ", 'users-control')."</div>";
			$html = $validation;			
		}
		
		if(!$ret_validate_email){
			$validation .= "<div class='userscontrol-ultra-error'>".__(" ERROR! Please type a valid email address ", 'users-control')."</div>";
			$html = $validation;			
		}
		
		$current_user = get_userdata($user_id);
		$current_user_email = $current_user->user_email;
		
		$check_user = get_user_by('email',$email);
		$user_check_id = $check_user->ID;
		$user_check_email = $check_user->ID;
		
		if($validation=="" ){
		
			if($user_check_id==$user_id) {
				$validation .= "<div class='userscontrol-ultra-error'>".__(" ERROR! You haven't changed your email. ", 'users-control')."</div>";
				$html = $validation;
				
			
			}else{ //email already used by another user
			
				if($user_check_email!=""){
					$validation .= "<div class='userscontrol-ultra-error'>".__(" ERROR! The email is in use already ", 'users-control')."</div>";
					$html = $validation;
				}else{
					
				}
			
			}
		}
		
		if($validation=="" ){
		
			if($user_id >0 )
			{
					$user = get_userdata($user_id);
					$user_id = $user->ID;
					$user_email = $user->user_email;
					$user_login = $user->user_login;	
					
					$user_id = wp_update_user( array( 'ID' => $user_id, 'user_email' => $email ) );
					
					//update mailchimp?
					$mail_chimp = get_user_meta( $user_id, 'userscontrol_mailchimp', true);
					
					if($mail_chimp==1) //the user has a mailchip account, then we have to sync
					{
						if($userscontrol->get_option('mailchimp_api'))
						{
							$list_id =  $userscontrol->get_option('mailchimp_list_id');					 
							//$userscontrol->newsletter->mailchimp_subscribe($user_id, $list_id);
						}
					}
					
					
																
										
					$html = "<div class='userscontrol-ultra-success'>".__(" Success!! Your email account has been changed to : ".$email."  ", 'users-control')."</div>";
					
																			
				}else{
					
									
				}
					
			}
		 echo wp_kses($html, $userscontrol->allowed_html);
		 die();
		
	
	}
	
	function validate_valid_email ($myString)
	{
		$ret = true;
		if (!filter_var($myString, FILTER_VALIDATE_EMAIL)) {
    		// invalid e-mail address
			$ret = false;
		}
					
		return $ret;	
	
	}
	
	/**
	Get Menu Links
	******************************************/
	public function get_user_backend_menu_new($slug, $title , $icon = null)	{
		global $userscontrol;
		
		$url = "";        
        $module = array();		
		$uri = $this->build_user_menu_uri($slug);
		
		$url = '<a class="userscontrol-btn-u-menu" href="'.$uri.'" title="'.$title.'"><span class="userscontrol-user-menu-ico"><i class="fa '.$icon.' fa-2x"></i></span><span class="userscontrol-user-menu-text">'.$title.'</span></a>';
		if($slug=='profile'){
			$user_id = get_current_user_id();
			$url = '<a class="userscontrol-btn-u-menu" href="'.$uri.'" title="'.$title.'"><span class="userscontrol-user-menu-ico"><i class="fa '.$icon.' fa-2x"></i></span><span class="userscontrol-user-menu-text">'.$title.'</span></a>';	
		}	
				
		//messsages
		if($slug=='messages'){
			$user_id = get_current_user_id();
			if($total>0){
				$url .= '<div class="userscontrol-noti-bubble" title="'.__('Unread Messages', 'users-control').'">'.$total.'</div>';			
			}			
		}
		return $url;	
	}
	
	function build_user_menu_uri($slug){
		global $userscontrol;
		$uri = "";	
		if(!isset($_GET["page_id"])){
			$uri = '?module='.$slug;
		}else{
			$uri = '?page_id='.sanitize_text_field($_GET["page_id"]).'&module='.$slug;
		}
		if($slug=='logout'){
			$uri = $this->get_logout_url();
		}
		return $uri;
	}
	
	public function get_logout_url (){
		$redirect_to = $this->current_page;
		return wp_logout_url($redirect_to);
	}
	
	
	/*Prepare user meta*/
	function prepare ($array ) 
	{
		foreach($array as $k => $v) {
			if ($k == 'userscontrol-client-form') continue;
			$this->usermeta[$k] = $v;
		}
		return sanitize_text_field($this->usermeta);
	}
	
	/*Handle Registration*/
	function handle_registration_upgrade_subscription() 	{
	    global $userscontrol, $blog_id, $userscontrol_aweber, $userscontrol_recaptcha, $userscontrol_stripe, $userscontrol_paypal;
	    	
		if ( empty( $GLOBALS['wp_rewrite'] ) )
		{
			 $GLOBALS['wp_rewrite'] = new WP_Rewrite();
	    }
		
		if(!session_id()) {
			session_start();
   		}
		
		$current_user = $userscontrol->user->get_user_info();
		$user_id = $current_user->ID;
		
		/* Create account, update user meta */				
		$visitor_ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);		
				
		if($user_id){
			
			
			//set custom role for this user
			if($new_role!="")
			{
				$user = new WP_User( $user_id );
				//$user->set_role( $new_role );						
			}
			
			$login_link_id = $userscontrol->get_option('user_login_page');				
			$login_link = get_page_link($login_link_id);			
			$user = get_user_by( 'id', $user_id );			

			
			//Paid Membership active		
			if($userscontrol->get_option('registration_rules')==4)	{
				//create transaction key
				$transaction_key = session_id()."_".time();					
				
				//payment Method
				$payment_method = sanitize_text_field($_POST["userscontrol_payment_method"]);					
				$package_id = sanitize_text_field($_POST["userscontrol_package_id"]);					
				$package = $userscontrol->membership->get_one($package_id);
				
				$site_date = date( 'Y-m-d', current_time( 'timestamp', 0 ) );	
				
				$valid_periods = array();
				$valid_periods  = $userscontrol->membership->get_periods($package);
				
				if( $package->membership_type=='recurring'){
					
					$isrecurring = 1;					
					$amount_subscription = $package->membership_subscription_amount;
					$amount = $package->membership_initial_amount;
			
				}else{
					
					$isrecurring = 0;						
					$amount = $package->membership_initial_amount;					
				}
				
					
				$payment_procesor = false;
				  
				if($_POST["userscontrol_payment_method"]=='' || $_POST["userscontrol_payment_method"]=='paypal')
				{
					$payment_procesor = true;
					$payment_method="paypal";	
					 
				
				}elseif($_POST["userscontrol_payment_method"]=='bank'){
					
					$payment_method="bank";
					$payment_procesor = false;
					   
				 }elseif($_POST["userscontrol_payment_method"]=='stripe'){
						 
					$payment_method="stripe";
					$payment_procesor = true;
				
				 }elseif($_POST["userscontrol_payment_method"]=='authorize'){  
				  
					 $payment_method="authorize";
					 $payment_procesor = true;
				 }
				 
				 
				  //create order					  
				  $subscription_data = array(
					 'subscription_user_id' => $user_id,
					 'subscription_package_id' => $package_id,						 
					 'subscription_status' => 0 ,
					 'subscription_recurring' => $isrecurring ,						 
					 'subscription_lifetime' => $package->membership_lifetime ,						 
					 'subscription_key' => $transaction_key,
					 'subscription_date' => $site_date ,
					 'subscription_start_date' => $valid_periods['starts'],		
					 'subscription_end_date' => $valid_periods['ends'] ); 	
					
				$subscription_id = $userscontrol->order->create_subscription($subscription_data);	  
				
				if(($payment_method=="paypal" && $amount > 0 && $payment_procesor) || ($payment_method=="paypal" && $amount_subscription > 0 && $payment_procesor) )
				{
					if(isset($userscontrol_paypal)){
					  $ipn = $$userscontrol_paypal->get_ipn_link($package, $subscription_data, 'upgrade');
					 //redirect to paypal
					  header("Location: $ipn");
					  exit;

					}
					  
				}elseif($payment_method=="stripe" && $amount > 0 && $payment_procesor){
					
					
					if(isset($userscontrol_stripe))
					{
						$stripe_token = sanitize_text_field( $_POST['easywp_stripe_token']);	
						
						if($isrecurring==0){ //onetime payment
							
							$res = array();											
							
							$res = 	$userscontrol_stripe->charge_credit_card_one_time_upgrade($stripe_token, $package, $subscription_data);
							
							if($res['result']=='ok')
							{
								$userscontrol_stripe->process_order_upgrade_onetime($transaction_key, $subscription_id, $res);						
																									
								//redir
								$this->handle_redir_success_backend($transaction_key, $subscription_id );								
							
							}else{
								
											
							
							}
							
						}elseif($isrecurring==1){	//recurring payment
						
							$res = 	$userscontrol_stripe->charge_credit_card_upgrade_recurring($stripe_token, $package, $subscription_data);
							
							if($res['result']=='ok')
							{
								$userscontrol_stripe->process_order_upgrade_recurring($transaction_key,$subscription_id, $res);
																						
								//redir
								$this->handle_redir_success_backend($transaction_key, $subscription_id );								
							
							}else{
								
												
							
							}
						
						exit();
							
						} //end if recurring					
					
					} //end if  userscontrolstripe plugin
				  
				}elseif($amount <= 0){
					
					//update status							
					$userscontrol->order->update_subscription_status($subscription_id,1);						
					
					//this is a free package							
					$userscontrol->messaging->send_client_registration_link($user, $login_link, $user_pass);
					$this->handle_redir_success_backend($transaction_key, $subscription_id );	
					
				} //endif payment gateways
			
			}else{
				$this->handle_redir_success_backend($transaction_key, $subscription_id );				
				
			} //end if paid subscription
		}		
	}
	
	/*Handle Registration*/
	function handle_registration() 	{
	    global $userscontrol, $blog_id, $userscontrol_aweber, 
		$userscontrol_recaptcha, 
		$userscontrol_passwordstrength,
		 $userscontrol_paypal,
		 $userscontrol_stripe;
	    	
		if ( empty( $GLOBALS['wp_rewrite'] ) ){
			 $GLOBALS['wp_rewrite'] = new WP_Rewrite();			 
	    }
		
		if(!session_id()) {
			session_start();
   		}   
			
		/* Create account, update user meta */				
		$visitor_ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);
		
		$g_recaptcha_response = '';		
		if(isset($_POST['g-recaptcha-response']) && $_POST['g-recaptcha-response']!=''){
			$g_recaptcha_response = sanitize_text_field($_POST['g-recaptcha-response']);		
		}
		
		//check reCaptcha
		$is_valid_recaptcha = true;	
		if(isset($userscontrol_recaptcha) && $userscontrol->get_option('recaptcha_site_key')!='' && $userscontrol->get_option('recaptcha_secret_key')!='' && $userscontrol->get_option('recaptcha_display_registration')=='1' ){
			$is_valid_recaptcha = $userscontrol_recaptcha->validate_recaptcha_field($g_recaptcha_response);	
		}
		
		$ask_password=false;		
		$password_strength = true;			
		//check Password Strenght		
		if(isset( $userscontrol_passwordstrength)){			
			if($userscontrol->get_option('registration_password_ask')==1){
				$ask_password=true;				
				$password_strength = $userscontrol_passwordstrength->ucaptcha_check_pass_strenght(sanitize_text_field($_POST['user_password']));			
			}		
		}
        
        $nonce_control = true;
         //CHECK NONCE
        if(!isset($_POST['userscontrol_csrf_token'])){
            $this->errors[] = __('<strong>ERROR:</strong> Nonce not received.','users-control');  
            $nonce_control = false;
        }else{
            
            if(wp_verify_nonce($_POST['userscontrol_csrf_token'], 'userscontrol_reg_action')){
                
            }else{
                $nonce_control = false;
            }
        }
        
        //END NONCE
		if($_POST['first_name']==''){
			$this->errors[] = __('<strong>ERROR:</strong> Please input your First Name.','users-control');
		
		}elseif($_POST['last_name']==''){
			$this->errors[] = __('<strong>ERROR:</strong> Please input your Last Name.','users-control');
		}elseif($_POST['email']==''){
			$this->errors[] = __('<strong>ERROR:</strong> Please input an email address.','users-control');	
		}elseif(!$is_valid_recaptcha){
			$this->errors[] = __('<strong>ERROR:</strong> reCaptcha validation failed.','users-control');
        }elseif(!$nonce_control){
            $this->errors[] = __('<strong>ERROR:</strong> Nonce Error.','users-control');          
		}elseif(!$password_strength && $ask_password){
			$this->errors[] =$userscontrol_passwordstrength->errors;		
		}else{
								
			if(email_exists($_POST['email'])){			
				$this->errors[] = __('<strong>ERROR:</strong> The email address already exists.','users-control');
			}elseif(username_exists(sanitize_text_field($_POST['user_name']))){
				$this->errors[] = __('<strong>ERROR:</strong> The username already exists.','users-control');
			}else{ // new user we have to create it.			
							
				$sanitized_user_login = sanitize_user($_POST['user_name']);
			
				/* We create the New user */
				if(isset($_POST['user_password']) && $_POST['user_password']!=''){
					$user_pass = sanitize_text_field($_POST['user_password']);
				}else{
					$user_pass = wp_generate_password( 8, false);						
				}
                
                $ee =sanitize_text_field($_POST['email']);
				
				$user_id = wp_create_user( $sanitized_user_login, $user_pass, $ee );	
				wp_update_user( array('ID' => $user_id, 'first_name' => sanitize_text_field($_POST['first_name'])) );
				
				if($user_id){					
					update_user_meta($user_id, 'userscontrol_user_registered_ip', $visitor_ip);					
					update_user_meta($user_id, 'userscontrol_is_client', 1);					
					update_user_meta($user_id, 'last_name',  sanitize_text_field($_POST['last_name']));							
					update_user_meta($user_id, 'first_name', sanitize_text_field($_POST['first_name']));
										
					//set account status						
					$verify_key = $this->get_unique_verify_account_id();					
					update_user_meta ($user_id, 'userscontrol_ultra_very_key', $verify_key);
					
					//assign default role for this user						
					$new_role = 'userscontrol_user';
					
					//set custom role for this user
					if($new_role!=""){
						$user = new WP_User( $user_id );
						//$user->set_role( $new_role );						
					}

					$custom_form  = '';
					if(isset($_POST["userscontrol_custom_form"])) {
						$custom_form = sanitize_text_field($_POST["userscontrol_custom_form"]);	
					}
					
					if($custom_form!=""){
						$custom_form = 'userscontrol_profile_fields_'.$custom_form;				
						$fields = get_option($custom_form);
						update_user_meta($user_id, 'userscontrol_custom_registration_form', $custom_form);	
					}else{
						$fields = get_option('userscontrol_profile_fields');
					}			

					ksort($fields);	
					/*Go through the fields*/		
					foreach($fields as $key => $field) {
						extract($field);
						if (isset($_POST[$meta]) ) {
							$meta_field_val= sanitize_text_field($_POST[$meta]);
							if (is_array($meta_field_val)){
								$meta_field_val = implode(',', $meta_field_val);
							}
							update_user_meta($user_id, $meta, esc_attr($meta_field_val));		
						}
					}
					
					$login_link_id = $userscontrol->get_option('user_login_page');				
					$login_link = get_page_link($login_link_id);
					
					$user = get_user_by( 'id', $user_id );					
					
					if(isset($userscontrol_aweber)){
						
						 //aweber	
						 $list_id = get_option( "userscontrol_aweber_list");				 
						 if(isset($_POST["userscontrol-aweber-confirmation"]) && $_POST["userscontrol-aweber-confirmation"]==1 && $list_id !='')	
						  {						 
							// $user_l = get_user_by( 'id', $user_id ); 				 
							 $userscontrol_aweber->userscontrol_subscribe($user, $list_id);
							 update_user_meta ($user_id, 'userscontrol_aweber', 1);				 						
						}						
					
					} //endif aweber			
				
				
					//Paid Membership active		
					if($userscontrol->get_option('registration_rules')==4 || $userscontrol->get_option('registration_rules')==''){
						//create transaction key
						$transaction_key = session_id()."_".time();								
						$amount_subscription = 0;
						
						//payment Method
						$payment_method ='';
						if(isset($_POST["userscontrol_payment_method"])){
							$payment_method = sanitize_text_field($_POST["userscontrol_payment_method"]);
						}
							
						$package_id = sanitize_text_field($_POST["userscontrol_package_id"]);					
						$package = $userscontrol->membership->get_one($package_id);
						$site_date = date( 'Y-m-d', current_time( 'timestamp', 0 ) );	
						
						$valid_periods = array();
						$valid_periods  = $userscontrol->membership->get_periods($package);
						
						if( $package->membership_type=='recurring'){
							$isrecurring = 1;					
							$amount_subscription = $package->membership_subscription_amount;
						}else{
							$isrecurring = 0;						
							$amount = $package->membership_initial_amount;					
						}						
							
						$payment_procesor = false;
						  
						if($payment_method=='' || $payment_method=='paypal'){
							$payment_procesor = true;
							$payment_method="paypal";	
						}elseif($payment_method=='bank'){
							$payment_method="bank";
							$payment_procesor = false;
						 }elseif($payment_method=='stripe'){
							$payment_method="stripe";
							$payment_procesor = true;
						 }elseif($payment_method=='authorize'){  
							 $payment_method="authorize";
							 $payment_procesor = true;
						 }
						 
						//create order					  
						$subscription_data = array(
							 'subscription_user_id' => $user_id,
							 'subscription_package_id' => $package_id,						 
							 'subscription_status' => 0 ,
							 'subscription_recurring' => $isrecurring ,						 
							 'subscription_lifetime' => $package->membership_lifetime ,						 
							 'subscription_key' => $transaction_key,
							 'subscription_date' => $site_date ,
							 'subscription_start_date' => $valid_periods['starts'],		
							 'subscription_end_date' => $valid_periods['ends'] ); 	
							
						$subscription_id = $userscontrol->order->create_subscription($subscription_data);	  
						
						if(($payment_method=="paypal" && $amount > 0 && $payment_procesor) || ($payment_method=="paypal" && $amount_subscription > 0 && $payment_procesor) ){

							if(isset($userscontrol_paypal)){
								$ipn =$userscontrol_paypal->get_ipn_link($package, $subscription_data, 'ini');	
								header("Location: $ipn");
							}
							exit;
						}elseif($payment_method=="stripe" && $amount > 0 && $payment_procesor){

							$userscontrol_payment_method_intent =  sanitize_text_field($_SESSION['payment_intent_id']);	
							
							if(isset($userscontrol_stripe)){

								$intent = 	$userscontrol_stripe->get_transaction_intent($userscontrol_payment_method_intent);

								if($isrecurring==0){ //onetime payment

									if($intent->status == 'succeeded'){
										$userscontrol_stripe->process_order_onetime($transaction_key, $subscription_id, $user_id, $intent);
										//send welcome email																		
										$userscontrol->messaging->send_client_registration_link($user, $login_link, $user_pass);

										$this->handle_redir_success($transaction_key, $user_id);								
									
									}else{				
										echo wp_kses($intent->status, $userscontrol->allowed_html);
									}

								}elseif($isrecurring==1){ // is recurring payment

									if($intent->status == 'succeeded'){
										$userscontrol_stripe->process_order_upgrade_recurring($transaction_key, $subscription_id, $user_id, $intent);
										$this->handle_redir_success($transaction_key, $user_id);								
										
									}else{				
										echo wp_kses($intent->status, $userscontrol->allowed_html);
									}

								}

							}
												
						}elseif($amount <= 0){
							
							
							//update status							
							$userscontrol->order->update_subscription_status($subscription_id,1);						
							
							//this is a free package							
						//	$userscontrol->messaging->send_client_registration_link($user, $login_link, $user_pass);

							$this->user_account_activation($user, $user_pass);							
							$this->handle_redir_success($verify_key, $user_id);	
							
						
						 } //endif payment gateways
						   
						   
					
					}else{ //this is triggered only if we have removed the payment options		
											
						//$userscontrol->messaging->send_client_registration_link($user, $login_link, $user_pass);

						$this->user_account_activation($user,  $user_pass);
						$this->handle_redir_success($verify_key, $user_id);	
						
					} //end if paid subscription
				
				} //end if email exists
				
			} //end if required fields
		
		}		
			
	}

	/*---->> Notify User ****/  
	public function user_account_activation($user,  $user_pass){
		global $userscontrol;	

		require_once(ABSPATH . 'wp-includes/link-template.php');

		//1 => __('Login automatically after registration','users-control'), 
		//2 => __('E-mail Activation -  A confirmation link is sent to the user email','users-control'),
		//3 => __('Manual Activation - The admin approves the accounts manually','users-control'),
		//4 => __('Send Credentials to email - Emai with username and password is sent','users-control')),

		$u_email =  $user->user_email;
		$user_id =  $user->ID;
		$user_login=  $user->user_login;
		
		//check if login automatically
		$activation_type= $userscontrol->get_option('activation_method');
		
		if($activation_type==1 || $activation_type==4 || $activation_type==''){

			update_user_meta($user_id, 'userscontrol_account_status', 'active');

			$login_link_id = $userscontrol->get_option('user_login_page');				
			$login_link = get_page_link($login_link_id);
			//automatic activation		
			$userscontrol->messaging->send_client_registration_link($user, $login_link, $user_pass);							
		
		}elseif($activation_type==2){

			update_user_meta($user_id, 'userscontrol_account_status', 'pending');
			
			//email activation link				  
			$web_url =$this->get_my_account_direct_link();		  
			$current_url =sanitize_url($_SERVER['REQUEST_URI']);
			$pos = strpos($current_url, "page_id");	
			$unique_key = get_user_meta($user_id, 'userscontrol_ultra_very_key', true);
				
			if ($pos === false) { // this is a tweak that applies when not Friendly URL is set. NOT found
				  $activation_link = $web_url."?act_link=".$unique_key;
			} else {
			   // found then we're using seo links					 
			   $activation_link = $web_url."&act_link=".$unique_key;
			}
			
			//send link to user
			$userscontrol->messaging->welcome_email_with_activation($u_email, $user_login, $user_pass, $activation_link);
			
		
		}elseif($activation_type==3){	

			update_user_meta($user_id, 'userscontrol_account_status', 'pending_admin');
			  
			//admin approval
			$userscontrol->messaging->welcome_email_with_admin_activation($u_email, $user_login, $user_pass, $activation_link);
			
			
			
		   
		
		
		}
	  
	}

	/*Handle Account Email Confirmation*/
	public function handle_account_conf_link() 	{
		global $userscontrol ;
		$act_link = $_GET["act_link"];
		
		if(isset($act_link) && $act_link!=""){
			$user = $this->get_user_with_key($act_link);
			if($user!="error"){
				$secure ="";
				//activate user
				$user_id = $user->ID;
				$user_email = $user->user_email;
				update_user_meta ($user_id, 'userscontrol_account_status', 'active');				
				$userscontrol->messaging->confirm_verification_sucess($user_email);
				//login user and take them to account				
				wp_set_auth_cookie( $user_id, true, $secure );				
				$this->login_registration_afterlogin();
			}else{
				
				//wrong key, display message at the screen
				echo wp_kses("INVALID", $userscontrol->allowed_html);
				exit();			
			}
		}
	}
	
	//get user with kewy - used for confirmation link only
	public function get_user_with_key( $uniquekey )
	{
		global  $wpdb,  $userscontrol;
		
		$args = array(
		
			'meta_query' => array(
					'relation' => 'AND',
					array(
						'key' => 'userscontrol_account_status',
						'value' => 'pending',
						'compare' => '='
					),
					array(
						'key' => 'userscontrol_ultra_very_key',
						'value' => $uniquekey ,
						'compare' => '='
					),
			
			)
		);


		$wp_user_query = new WP_User_Query($args);		
		$res = $wp_user_query->results;
		
		if(!empty($res)) 
		{
			
			foreach ( $res as $user )
			{
				return $user;
			
			
			}
		
		
		}else{
			
			return "error";
			
		}
			
		
	}

	public function get_my_account_direct_link()	{
		global $userscontrol, $wp_rewrite ;		
		$wp_rewrite = new WP_Rewrite();
		
		require_once(ABSPATH . 'wp-includes/link-template.php');		
		//require_once(ABSPATH . 'wp-includes/pluggable.php');

		$account_page_id = $userscontrol->get_option('my_account_page');
		$my_account_url = get_permalink($account_page_id);	
		
		return $my_account_url;
	
	}
	
	//this is the custom redirecton after ticket submission sucess
	public function handle_redir_success_backend($key, $sub_id)	{
		global $userscontrol, $userscontrolcomplement, $wp_rewrite ;		
		$wp_rewrite = new WP_Rewrite();				
		$url = '';
		$my_success_url = '';	
		$url = '?module=subscription_detail&id='.$sub_id.'&userscontrol_registration=ok&userscontrol_u_key='.$key;		
		wp_redirect( $url );
		exit;
	}
	
	public function handle_redir_success($key, $user_id)	{
		global $userscontrol, $userscontrolcomplement, $wp_rewrite ;
		
		$wp_rewrite = new WP_Rewrite();		
		
		$url = '';
		$my_success_url = '';		
        
        if(isset($_GET['redirect_to'])){
            $my_success_url = sanitize_text_field($_GET['redirect_to']);	
        }elseif(isset($_POST['redirect_to'])){
             $my_success_url = sanitize_text_field($_POST['redirect_to']);	
        }
		
		if($my_success_url=="")		{
			$url = sanitize_url($_SERVER['REQUEST_URI']).'?userscontrol_registration=ok&userscontrol_u_key='.$key;
		}else{									
			$url = $my_success_url.'?userscontrol_status=ok&userscontrol_u_key='.$key;				
		}
		wp_redirect( $url );
		exit;
	}
	
	public function get_unique_verify_account_id(){

		if(!session_id()) {
			session_start();
   		} 
		$rand = $this->genRandomStringActivation(8);
		$key = session_id()."_".time()."_".$rand;		  
		return $key;
	}
	  
	public function genRandomStringActivation($length){
			
			$characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWZYZ";
			
			$real_string_legnth = strlen($characters) ;
			//$real_string_legnth = $real_string_legnth– 1;
			$string="ID";
			
			for ($p = 0; $p < $length; $p++)
			{
				$string .= $characters[mt_rand(0, $real_string_legnth-1)];
			}
			
			return strtolower($string);
	}
	
	
	/*Handle commons login*/
	function handle() {
	    global $userscontrol, $blog_id, $userscontrol_recaptcha;
	    	
		if ( empty( $GLOBALS['wp_rewrite'] ) ){
			 $GLOBALS['wp_rewrite'] = new WP_Rewrite();
	    }		
		
		$noactive = false;
		foreach($this->usermeta as $key => $value) 
		{
		
			if ($key == 'user_login') 
			{
				if (sanitize_user($value) == '')
				{
					$this->errors[] = __('<strong>ERROR:</strong> The username field is empty.','users-control');
				}
			}
			
			if ($key == 'user_pass')
			{
				if (esc_attr($value) == '') 
				{
					$this->errors[] = __('<strong>ERROR:</strong> The password field is empty.','users-control');
				}
			}
		}
		
		
		$g_recaptcha_response = '';		
		if(isset($_POST['g-recaptcha-response']) && $_POST['g-recaptcha-response']!=''){
			
			$g_recaptcha_response = sanitize_text_field($_POST['g-recaptcha-response']);		
		}
		
		
		//check reCaptcha		
		$is_valid_recaptcha = true;
		if(isset($userscontrol_recaptcha) && $userscontrol->get_option('recaptcha_site_key')!='' && $userscontrol->get_option('recaptcha_secret_key')!='' && $userscontrol->get_option('recaptcha_display_loginform')=='1' ){
			
			$is_valid_recaptcha = $userscontrol_recaptcha->validate_recaptcha_field($g_recaptcha_response);	
		
		}
		
		if(!$is_valid_recaptcha){
			
			$this->errors[] = __('<strong>ERROR:</strong> The captcha validation is wrong.','users-control');
		
		}	
	
			/* attempt to signon */
			if (!is_array($this->errors)) 
			{				
				$creds = array();
				
				// Adding support for login by email
				if(is_email($_POST['user_login']))
				{
				    $user = get_user_by( 'email', sanitize_text_field($_POST['user_login']) );
				    
				    if(isset($user->data->user_login))
					{
				        $creds['user_login'] = $user->data->user_login;
						
				    }else{
						
				        $creds['user_login'] = '';
						$this->errors[] = __('<strong>ERROR:</strong> Invalid Email was entered.','users-control');				
					}
					
					// check if active					
					$user_id =$user->ID;				
					if(!$this->is_active($user_id))
					{
						$noactive = true;
						
					}else{
						
						
					}		
				
				}else{
					
					// User is trying to login using username					
					$user = get_user_by('login',sanitize_text_field($_POST['user_login']));
					
					// check if active and it's not an admin		
					if(isset($user) && isset($user->ID))	
					{
						$user_id =$user->ID;	
						
					
					}else{
						
						$user_id ="";
						
					}
							
					if(!$this->is_active($user_id) && !is_super_admin($user_id))
					{
						$noactive = true;						
					}				
					
					$creds['user_login'] = sanitize_text_field($_POST['user_login']);			
				
				}
				
				$creds['user_password'] = sanitize_text_field($_POST['login_user_pass']);
                
                if(isset( $_POST['rememberme'])){
                    
                    $creds['remember'] = sanitize_text_field($_POST['rememberme']);
                    
                }
				
				
							
				if(!$noactive )
				{					
					if(!is_array($this->errors))	
					{
						
					  $user = wp_signon( $creds, false );			
	  
					  if ( is_wp_error($user) ) 
					  {						
						  if ($user->get_error_code() == 'invalid_username') {
							  $this->errors[] = __('<strong>ERROR:</strong> Invalid Username was entered.','users-control');
						  }
						  if ($user->get_error_code() == 'incorrect_password') {
							  $this->errors[] = __('<strong>ERROR:</strong> Incorrect password was entered.','users-control');
						  }
						  
						  if ($user->get_error_code() == 'empty_password') {
							  $this->errors[] = __('<strong>ERROR:</strong> Please provide Password.','users-control');
						  }
						  
											  
					  }else{						
						  
						  $this->userscontrol_auto_login($user->user_login);						
						  $this->login_registration_afterlogin();					
					  }
					
					}
					
				
				}else{
					
					//not active
					$this->errors[] = __('<strong>ERROR:</strong> Your account is not active.','users-control');
				 
				}
			}
			
	}
	
	/*Send Welcome Email to Staff Member*/
	function send_welcome_email_to_staff() {
	    global $userscontrol, $blog_id;
	    		
		if ( empty( $GLOBALS['wp_rewrite'] ) ){
			$GLOBALS['wp_rewrite'] = new WP_Rewrite();
	    }
		
		$staff_id	=sanitize_text_field($_POST['staff_id']);
	
		$user = get_user_by( 'id', $staff_id );
		$user_id =$user->ID;		
		
		//generate reset link
		$unique_key =  $this->get_unique_verify_account_id();
				
		//web url
		$web_url = $this->get_password_reset_page_direct_link();				
		$pos = strpos("page_id", $web_url);  
				
		if ($pos === false) {
			$reset_link = $web_url."?resskey=".$unique_key;
			  
		} else {
			   
			// found then we're using seo links					 
			$reset_link = $web_url."&resskey=".$unique_key;					  
		}
		
		//update meta
		update_user_meta ($user_id, 'userscontrol_ultra_very_key', $unique_key);	
		
		//notify users			  
		$userscontrol->messaging->send_welcome_email_link($user, $reset_link);			  
		
		//send reset link to user		  			  
		 $html = "<div class='userscontrol-ultra-success'>".__(" A reset link has been sent to the user. ", 'users-control')."</div>";
		echo wp_kses($html, $userscontrol->allowed_html);
		die(); 
		
    }
	
		
	
	/*Handle password reest*/
	function handle_password_reset() 
	{
	    global $userscontrol, $blog_id, $userscontrol_recaptcha;
        
        $user_id = "";
	    		
		if ( empty( $GLOBALS['wp_rewrite'] ) )
		{
			 $GLOBALS['wp_rewrite'] = new WP_Rewrite();
	    }
		
		$noactive = false;	
					 
		if( isset($_POST['user_login_reset']))
	    {
			$user_login = sanitize_text_field($_POST['user_login_reset']);
			 
		}else{
			
			$user_login ='';			 
			 
		}
		
		
		$g_recaptcha_response = '';		
		if(isset($_POST['g-recaptcha-response']) && $_POST['g-recaptcha-response']!=''){
			
			$g_recaptcha_response = sanitize_text_field($_POST['g-recaptcha-response']);		
		}
		
		//check reCaptcha
		$is_valid_recaptcha = true;	
		if(isset($userscontrol_recaptcha) && $userscontrol->get_option('recaptcha_site_key')!='' && $userscontrol->get_option('recaptcha_secret_key')!='' && $userscontrol->get_option('recaptcha_display_forgot_password')=='1' ){
			
			$is_valid_recaptcha = $userscontrol_recaptcha->validate_recaptcha_field($g_recaptcha_response);		
		}
		
		 if( !$is_valid_recaptcha)
		 {			 
			 $this->errors[] = __('<strong>ERROR:</strong> The captcha validation is wrong..','users-control');	 
		 }
		 
		$user_login =sanitize_user($user_login);
		 
		 if( $user_login=='')
		 {			 
			 $this->errors[] = __('<strong>ERROR:</strong> The username field is empty.','users-control');	 
		 }
		  
   		 /* attempt to get recover */
		 if (!is_array($this->errors)){			 
			 
			 // Adding support for login by email
			 if(is_email($_POST['user_login_reset']))
			 {				 
				 $user = get_user_by( 'email',  sanitize_text_field($_POST['user_login_reset']) );
				 
				 // check if we have a valid username		
				 if(isset($user) && $user != false)	
				 {
					 
					$user_id =$user->ID;		
					
				 }else{
											
					$user_id ="";	
					$this->errors[] = __('<strong>ERROR:</strong> Invalid Email or Username.','users-control');					
					
				 }
									
				 if(!$this->is_active($user_id))
				 {
					 $noactive = true;						
				 }
				
			  }else{				  
				   					
					// User is trying to login using username					
					$user = get_user_by('login', sanitize_text_field($_POST['user_login_reset']));
					
					// check if we have a valid username		
					if(isset($user) && $user != false)	
					{
						$user_id =$user->ID;		
					
					}else{
												
						$user_id ="";	
						$this->errors[] = __('<strong>ERROR:</strong> Invalid Email or Username.','users-control');					
					}
							
					if(!$this->is_active($user_id) && !is_super_admin($user_id))
					{
						$noactive = true;
						
					}				
					
					$user_login = sanitize_user($_POST['user_login_reset']);	
				
				 }
				
			
				
				if(!$noactive)
				{								
					
								
				}else{
					
					//not active
					$this->errors[] = __('<strong>ERROR:</strong> Your account is not active.','users-control');
				 
				}				
				
			}else{				
				
				
			}
			
			
			//we send notification emails			
			if($user_id!="" && isset($user) && $user != false)
		  	{				
				//generate reset link
				$unique_key =  $this->get_unique_verify_account_id();
				
				//web url
				$web_url = $this->get_password_reset_page_direct_link();
				
				$pos = strpos("page_id", $web_url);
  
				
				if ($pos === false) //not page_id found
				{
					  //
					  $reset_link = $web_url."?resskey=".$unique_key;
					  
				} else {
					   
					   // found then we're using seo links					 
					   $reset_link = $web_url."&resskey=".$unique_key;					  
				}
				
				//update meta
				update_user_meta ($user_id, 'userscontrol_ultra_very_key', $unique_key);	
				
				//notify users			  
				$userscontrol->messaging->send_reset_link($user, $reset_link);			  
				
				//send reset link to user		  			  
				 $html = "<div class='userscontrol-ultra-success'>".__(" A reset link has been sent to your email. ", 'users-control')."</div>";
				 
				 $this->get_sucess_message_reset= $html; 
			 
		  	} ///end send emails
		
    }
  
  /*---->> Check if user is active before login  ****/
	function is_active($user_id){
		global $userscontrol ;		
		
		$checkuser = get_user_meta($user_id, 'userscontrol_account_status', true);
		$res = '';
		if ($checkuser == 'active') //this is a tweak for already members
		{	$res = true; //the account is active
		
		}else{
			
			$res = false;
		
		}
		
		return $res;		
		
   }
  
  public function get_login_page_direct_link()
  {
		global $userscontrol, $wp_rewrite ;
		
		$wp_rewrite = new WP_Rewrite();		
		
		$account_page_id = $userscontrol->get_option('user_login_page');		
		$my_account_url = get_permalink($account_page_id);
		
		return $my_account_url;
	
  }
	
  public function get_password_reset_page_direct_link()
  {
		global $userscontrol, $wp_rewrite ;
		
		$wp_rewrite = new WP_Rewrite();		
      
      
		$account_page_id = $userscontrol->get_option('password_reset_page');		
		$my_account_url = get_permalink($account_page_id);
		
		return $my_account_url;
	
	}
	

  
  public function genRandomString($length){
		
		$characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWZYZ";		
		$real_string_legnth = strlen($characters) ;
		//$real_string_legnth = $real_string_legnth– 1;
		$string="ID";
		
		for ($p = 0; $p < $length; $p++)
		{
			$string .= $characters[mt_rand(0, $real_string_legnth-1)];
		}
		
		return strtolower($string);
	}
	
	public function get_password_recover_page()
	{
		global $userscontrol, $wp_rewrite, $blog_id ; 
		
		$wp_rewrite = new WP_Rewrite();
		
		$account_page_id = $userscontrol->get_option('password_reset_page');				
		$my_account_url = get_page_link($account_page_id);				
						
		if($account_page_id=="")
		{
			$url = "NO";						
		}else{
			
			$url = $my_account_url;		
						
		}
		
		return $url;					
				
		
	}
	
	public function get_login_page()
	{
		global $userscontrol, $wp_rewrite, $blog_id ; 
		
		$wp_rewrite = new WP_Rewrite();
		
		$account_page_id = $userscontrol->get_option('user_login_page');				
		$my_account_url = get_page_link($account_page_id);				
						
		if($account_page_id=="")
		{
			$url = "NO";						
		}else{
			
			$url = $my_account_url;		
						
		}
		
		return $url;					
				
		
	}
	
	public function get_registration_page()
	{
		global $userscontrol, $wp_rewrite, $blog_id ; 
		
		$wp_rewrite = new WP_Rewrite();
		$account_page_id = $userscontrol->get_option('registration_page');				
		$my_account_url = get_page_link($account_page_id);				
						
		if($account_page_id=="")
		{
			$url = "NO";						
		}else{
			
			$url = $my_account_url;		
						
		}
		
		return $url;					
				
		
	}
	
	public function login_registration_afterlogin()
	{
		global $userscontrol, $wp_rewrite, $blog_id ; 
		
		$wp_rewrite = new WP_Rewrite();
		
		if (isset($_REQUEST['redirect_to']))
		{
			$url = sanitize_text_field($_REQUEST['redirect_to']);
				
		} elseif (isset($_POST['redirect_to'])) {
		
			$url = sanitize_text_field($_POST['redirect_to']);
				
		} else {
								
					//$redirect_custom_page = $userscontrol->get_option('redirect_after_registration_login');				
					//$url = get_page_link($redirect_custom_page);
					
					//if($url=='' || $redirect_custom_page=='')
					//{						
						//check redir		
						$account_page_id = $userscontrol->get_option('my_account_page');				
						$my_account_url = get_page_link($account_page_id);				
						
						if($my_account_url=="")
						{
							$url = sanitize_url($_SERVER['REQUEST_URI']);
						
						}else{
							
							$url = $my_account_url;				
						
						}
						
		}			
		wp_redirect( $url );
		exit();
	
	}
	
	/* Auto login user */
	function userscontrol_auto_login( $username, $remember=true ) 
	{
		ob_start();
		if ( !is_user_logged_in() ) {
			$user = get_user_by('login', $username );
			$user_id = $user->ID;
			wp_set_current_user( $user_id, $username );
			wp_set_auth_cookie( $user_id, $remember );
			do_action( 'wp_login', $user->user_login, $user );
		} else {
			wp_logout();
			$user = get_user_by('login', $username );
			$user_id = $user->ID;
			wp_set_current_user( $user_id, $username );
			wp_set_auth_cookie( $user_id, $remember );
			do_action( 'wp_login', $user->user_login, $user );
		}
		ob_end_clean();
	}
	
		
	
	/**
	* Add the shortcodes
	*/
	function profile_shortcodes(){	
		add_shortcode( 'userscontrol_user_login', array(&$this,'user_login') );
		add_shortcode( 'userscontrol_profile', array(&$this,'user_profile') );
		add_shortcode( 'userscontrol_user_recover_password', array(&$this,'user_recover_password') );
		add_shortcode( 'userscontrol_account', array(&$this,'user_account') );	
		add_shortcode( 'userscontrol_user_signup', array(&$this,'user_signup') );
		add_shortcode( 'userscontrol_protect', array(&$this,'funnction_protect_content') );	
	}
	
	//Protect Content
	public function funnction_protect_content( $atts, $content = null ){
		global $userscontrol;
		return $userscontrol->postprotection->show_protected_content( $atts, $content );	
	}

	public function  user_profile($atts){
		global $userscontrol;				
		return $this->get_user_profile($atts);			
	}

		/******************************************
	Get user by ID, username
	******************************************/
	function get_user_data_by_uri() {
		global  $userscontrol, $wpdb;	
		
		$u_nick = get_query_var('userscontrol_username');
		if($u_nick=="")	{
			$u_nick=$this->parse_user_id_from_url();			
		}		
		
		$nice_url_type = 'username';	
		
		if ($nice_url_type == 'ID' || $nice_url_type == '' ) {			
			$user = get_user_by('id',$u_nick);				
		}elseif ($nice_url_type == 'username') {			
			$user = get_user_by('slug',$u_nick);
		}elseif ($nice_url_type == 'user_nicename') {			
			$user = get_user_by('slug',$u_nick);
		}
		$user = get_user_by('slug',$u_nick);
		return $user;
	}

	function parse_user_id_from_url()
	{
		$user_id="";
		
		if(isset($_GET["page_id"]) && $_GET["page_id"]>0)
		{
			$page_id = sanitize_text_field($_GET["page_id"]);
			$user_id = $this->extract_string($page_id, '/', '/');
		
		
		}
		
		return $user_id;
	
	}
	
	function extract_string($str, $start, $end)
	{
		$str_low = $str;
		$pos_start = strpos($str_low, $start);
		$pos_end = strpos($str_low, $end, ($pos_start + strlen($start)));
		if ( ($pos_start !== false) && ($pos_end !== false) )
		{
		$pos1 = $pos_start + strlen($start);
		$pos2 = $pos_end - $pos1;
		return substr($str, $pos1, $pos2);
		}
	}
			
	function get_user_profile( $atts){
		global $userscontrol;		
		
		extract( shortcode_atts( array(	
			
			'tabs' => '', //separated by commas
			'tabs_fields' => '', //example Field Name:Metaname
			'disable' => '',
			'template' => 'user_profile_v1',
			'registration_form_template_id' => '',
							
			
		), $atts ) );

		//get styles

		
		
		//get current user			
		$current_user = $this->get_user_data_by_uri();
		
		if(isset($current_user->ID)){
			$user_id = $current_user->ID;
		}

	
		if(isset($_GET['userscontroltab'])){
			$tab = sanitize_text_field($_GET['userscontroltab']);

		}else{
			$tab = '';
		}		
		
		$user_display_name = $userscontrol->user->get_user_meta_custom ($user_id, 'first_name').' '. $userscontrol->user->get_user_meta_custom ($user_id, 'last_name');
				
		$col1_cont = $this->get_user_profile_part($tab, $user_id);
		//turn on output buffering to capture script output

		//profile nav

		$profile_nav = $this->get_user_profile_nav($user_id);

        ob_start();
		
        //include the specified file			
		$theme_path = get_template_directory();				
		if(file_exists($theme_path."/userscontrol/".$template.".php"))	{
			include($theme_path."/userscontrol/".$template.".php");
		}else{
			include(userscontrol_path.'/templates/basic/'.$template.'.php');		
		}		
		//assign the file output to $content variable and clean buffer
        $content = ob_get_clean();
		return  $content;
	}

	function get_user_profile_nav( $user_id){
		global $userscontrol, $userscontrol_photomedia;
		$html ='';

		$nav_user_items = get_option('userscontrol_profile_nav');

		$html .='<ul>';
		foreach( $nav_user_items as $tab => $name ) {
			
			if($tab=='albums' && !isset($userscontrol_photomedia)){
				continue;
			}
			
			$icon = '<span class="ucontrol-prof-nav-ico"><i class="fa '.$name['icon'].'"></i></span>';
			$html .='<li><a href="?userscontroltab='.$tab.'">'.$icon.$name['label'].'</a></li>';
		}
		$html .='</ul>';
		return $html;
	}

	

	function get_user_profile_part($part, $user_id){

		$html = '';

		if($part=='' || $part=='about'){
			$html = $this->get_user_profile_about_tabs($part, $user_id);			
		}elseif($part=='posts'){
			$html = $this->get_user_profile_posts_tab($part, $user_id);

		}elseif($part=='albums'){

			$html = $this->get_user_profile_albums_tab($user_id);

		}else{

			$html = '';
		}	

		return $html ;
	}


	function get_user_profile_albums_tab($user_id){
		global $userscontrol, $userscontrol_photomedia;
		$html = '';

		if(isset($userscontrol_photomedia)){
			if(isset($_GET['gal'])){
				$gal_id = sanitize_text_field($_GET['gal']);
				$html = $userscontrol_photomedia->show_my_album_photos_in_profile($user_id,$gal_id);
			}elseif(isset($_GET['photo'])){
				$photo_id = sanitize_text_field($_GET['photo']);
				$html = $userscontrol_photomedia->show_single_photo_in_profile($user_id,$photo_id);
			}else{
				$html = $userscontrol_photomedia->show_my_albums_in_profile($user_id);
			}
		}

		return $html;
	}

	function get_user_profile_posts_tab($part, $user_id){
		global $userscontrol, $userscontrol_posts;

		$html = '';

		if(isset($userscontrol_posts)){
			$html = $userscontrol_posts->show_my_posts_in_profile($user_id,'post');
		}

		return $html;
	}


	function get_user_profile_about_tabs($part, $user_id){
		global $userscontrol;

		$html = '';

		//get user profile form userscontrol_custom_registration_form
		$custom_form = $userscontrol->user->get_user_meta_custom ($user_id, 'userscontrol_custom_registration_form');

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


		foreach($array as $key => $field) {
			
			$show_to_user_role_list = '';
			$show_to_user_role = 0;			
			$edit_by_user_role = 0;
			$edit_by_user_role_list = '';
            $disabled = null;	

			extract($field);
			
			if(!isset($private))
			    $private = 0;

			
			$required_class = '';
			if($required == 1 && in_array($field, $userscontrol->include_for_validation)){
			    $required_class = ' required';
			}

			$required_text='';
			$can_hide = null;

			$show_field_status = true;
			
			/* Fieldset separator */
			if ( $type == 'separator' && $deleted == 0 && $private == 0 ){
				if ($show_field_status){
					$html .= '<div class="userscontrol-profile-info-seperator">'.$name.'</div>';
				}
			}

			
			
			if ( $type == 'usermeta' && $deleted == 0 && $private == 0){
	
							
			    if ($show_field_status){
				
				    $html .= '<div class="userscontrol-profile-info-field">';
				
                    /* Show the label */
                    if (isset($array[$key]['name']) && $name){

						if (isset($array[$key]['icon']) && $icon) {
							$html .= '<i class="fa fa-'.$icon.'"></i>';
						} else {
						//$display .= '<i class="fa fa-none"></i>';
						}
                        $html .= '<label class="userscontrol-field-type" for="'.$meta.'">';						
                        $html .= '<span>'.$name.'</span></label>';					
                    } else {
                        $html .= '<label class="userscontrol-field-type">&nbsp;</label>';
                    }
                    
                	$html .= '<div class="userscontrol-field-value">';               
				
					switch($field) {
						case 'textarea':
							$html .=$this->format_line_breaks($userscontrol->user->get_user_meta_custom($user_id, $meta));
							break;							
						case 'text':
							$html .= $userscontrol->user->get_user_meta_custom($user_id, $meta);
							break;
						case 'datetime':
							$html .= $userscontrol->user->get_user_meta_custom($user_id, $meta);
							break;
						case 'select':
									
							$html .= $userscontrol->user->get_user_meta_custom( $user_id,$meta);							
							break;

						case 'radio':						
							$html .= '<div class="userscontrol-clear"></div>';
							break;							
						case 'checkbox':						
							break;
							
					}				
					
					
				$html .= '</div>';
				$html .= '</div><div class="userscontrol-clear"></div>';
				
				} 
				
			} //end if user meta
		}


		return $html;

		
	}

	public function format_line_breaks($text){
        
        return nl2br($text);
        
     }
	
		
	public function  user_login ($atts)	{
		global $userscontrol;				
		return $this->get_client_login_form($atts);		
		
	}
	
	public function  user_signup ($atts){
		global $userscontrol;				
		return $this->get_client_signup_form($atts);			
	}
	
	public function  user_recover_password ($atts)
	{
		global $userscontrol;				
		return $this->get_client_recover_password_form($atts);		
		
	}
	
	public function  user_account ($atts)
	{
		global $userscontrol;
		
		
			if (!is_user_logged_in()) 
			{				
				
				return $this->get_client_login_form( $atts );
				
			}else{				
				
				return $this->get_my_account_page( $atts );
			}	
							
			
	}
	
	function get_user_avatar_top($staff_id)	{
		global $wpdb,  $userscontrol, $wp_rewrite;
		
		$html = '';		
		$html .='<div class="userscontrol-staff-profile-top" >
		'.$this->get_user_pic( $staff_id, 120, 'avatar', null, null, false).'
		
		</div>';		
		return $html;
	}

	public function has_profile_bg($user_id){
		global $userscontrol;		
		require_once(ABSPATH . 'wp-includes/link-template.php');		
		$user_pic = get_user_meta($user_id, 'user_cover_profile', true);		
		
		if($user_pic!=""){
			return true;
		}else{			
			return false;
		} 
	}

	public function get_cover_bg_image($user_id){
		global $userscontrol;		
		require_once(ABSPATH . 'wp-includes/link-template.php');		

		$upload_dir = wp_upload_dir(); 
		$path_pics =   $upload_dir['basedir'];		
		$path =   $upload_dir['baseurl']."/".WPUSERSCONTROL_MEDIAFOLDER.'/'.$user_id."/";

		$html = '';		
		
		$user_bg_pic = get_user_meta($user_id, 'user_cover_profile', true);	
		if($user_bg_pic!=""){
			$src = $path.$user_bg_pic;
			$html .= '<img class="landscape" src="'.$src.'" />';
		
		} 

		return $html ;
	}


	

	public function get_cover_bg_color($user_id){
		global $userscontrol;		
		require_once(ABSPATH . 'wp-includes/link-template.php');		
		$bg_color = get_user_meta($user_id, 'user_cover_bg_color', true);		
		
		if($bg_color==""){
			$bg_color='style="background-color:#b8e9f6e8;height:200px "';
		}else{

			$bg_color='style="background-color:'.$bg_color.'"';
		}

		return $bg_color;
	}

	
	
	
	
	/**
	 * Users Dashboard
	 */
	public function get_my_account_page($atts )
	{
		global $wpdb, $current_user;		
		$user_id = get_current_user_id();
		
		extract( shortcode_atts( array(	
			
			'disable' => ''						
			
		), $atts ) );
		
		$modules = array();
		$modules  = explode(',', $disable);	
      
        $content = $this->get_user_account();
		return  $content;
		  
	}
	
	function get_user_account(){
		
		global $userscontrol;		
		
		//turn on output buffering to capture script output
        ob_start();
		
        //include the specified file			
		$theme_path = get_template_directory();		
		
		if(file_exists($theme_path."/userscontrol/basic/dashboard.php"))
		{			
			include($theme_path."/userscontrol/basic/dashboard.php");
		
		}else{
			
			include(userscontrol_path.'/templates/basic/dashboard.php');
		
		}		
		//assign the file output to $content variable and clean buffer
        $content = ob_get_clean();
		return  $content;		
	
	}
	
	function get_user_header(){
		
		global $userscontrol;		
		
		//turn on output buffering to capture script output
        ob_start();
		
        //include the specified file			
		$theme_path = get_template_directory();		
		
		if(file_exists($theme_path."/userscontrol/basic/template-parts/header.php"))
		{			
			include($theme_path."/userscontrol/basic/template-parts/header.php");
		
		}else{
			
			include(userscontrol_path.'/templates/basic/template-parts/header.php');
		
		}		
		//assign the file output to $content variable and clean buffer
        $content = ob_get_clean();
		return  $content;		
	
	}
	
	function get_template_part($part){
		
		global $userscontrol;		
		
		//turn on output buffering to capture script output
        ob_start();
		
        //include the specified file			
		$theme_path = get_template_directory();		
		
		if(file_exists($theme_path."/userscontrol/basic/template-parts/".$part.".php"))	{			
			include($theme_path."/userscontrol/basic/template-parts/".$part.".php");

		}elseif($part=='photos'){

			$part='albums';

			if(isset($_GET["part"])){	$part =  sanitize_text_field($_GET["part"]);	}

			if($part=='albums'){
				
			}
			include(userscontrol_photomedia_path."/template-parts/".$part.".php");

		}elseif($part=='posts'){	

			if(isset($_GET["part"])){	$part =  sanitize_text_field($_GET["part"]);	}
			include(userscontrol_posts_path."/template-parts/".$part.".php");
		
		}else{
			
			include(userscontrol_path."/templates/basic/template-parts/".$part.".php");
		
		}		
		//assign the file output to $content variable and clean buffer
        $content = ob_get_clean();
		return  $content;		
	
	}
	
	
	
	
	/*Get errors display*/
	function get_errors()
	 {
		global $userscontrol;
		
		$display = null;
		
		if (isset($this->errors) && is_array($this->errors))  
		{
		    $display .= '<div class="userscontrol-ultra-error">';
		
			foreach($this->errors as $newError) 
			{
				
				$display .= '<span class="userscontrol-error userscontrol-error-block"><i class="userscontrol-icon-remove"></i>'.$newError.'</span>';
			
			}
		$display .= '</div>';
		
		
		} else {
			
			if (isset($_REQUEST['redirect_to'])){
				$url = sanitize_text_field($_REQUEST['redirect_to']);
			} elseif (isset($_POST['redirect_to'])){
				$url = sanitize_text_field($_POST['redirect_to']);
			}else{
				$url = sanitize_url($_SERVER['REQUEST_URI']);
			}
			wp_redirect( $url );
		}
		return $display;
	}
	
	
	/*Get errors display*/
	function get_errors_reset() {
		global $userscontrol;
		
		$display = null;
		
		if (isset($this->errors) && is_array($this->errors))  
		{
		    $display .= '<div class="userscontrol-ultra-error">';
		
			foreach($this->errors as $newError) 
			{
				
				$display .= '<span class="userscontrol-error userscontrol-error-block"><i class="userscontrol-icon-remove"></i>'.$newError.'</span>';
			
			}
		$display .= '</div>';
		
		
		
		}
		return $display;
	}
	
	//registration form
	public function get_client_signup_form($args=array()) {
		
		global $userscontrol, $userscontrol_stripe, $userscontrol_aweber, $userscontrol_recaptcha, $userscontrol_passwordstrength;
        
        $required_text ="";
        $required_class ="validate[required]";
		
		/* Arguments */
		$defaults = array(       
			'redirect_to' => null,
			'form_id' => null,
			'style' => null,
			'placeholders' => 'yes',			
			'form_header_text' => __('Sign Up','users-control')			
        		    
		);
		$args = wp_parse_args( $args, $defaults );
		$args_2 = $args;
		extract( $args, EXTR_SKIP );

		$two_cols='';
		if($style=="two-cols"){

			$two_cols='userscontrol-profile-field-half';
		}
		
		$display = null;	
		
		$display .= '<div class="userscontrol-front-cont">';		
	    $display .= '<div class="userscontrol-user-data-registration-form">';
		
		/*Display errors*/
		if (isset($_POST['userscontrol-client-form-registration-confirm']))	{
			$display .= $this->get_errors();			
		}
		
		/*Display errors*/
		if (isset($_GET['userscontrol_registration'])){
			$display .= '<div class="userscontrol-ultra-success"><span><i class="fa fa-check"></i>'.__('Your request has been sent successfully. Please check your email.','users-control').'</span></div>';
		}		
		
		$display .= '<form action="" method="post" id="userscontrol-client-registration-form" name="wuserscontrol-client-registration-form" enctype="multipart/form-data">';
		
		$display .= '<input type="hidden" name="userscontrol-client-form-registration-confirm" id="userscontrol-client-form-confirm-registration-confirm" >';
		$display .= '<input type="hidden" name="userscontrol_payment_method_intent" id="userscontrol_payment_method_intent" >';
       
		

        $display .= wp_nonce_field('userscontrol_reg_action', 'userscontrol_csrf_token');
		$display .= '<div class="userscontrol-profile-separator">'.__('Account Data','users-control').'</div>';
		
		//name
		$display .= '<div class="userscontrol-profile-field '.$two_cols.'">';									
		$display .= '<label class="userscontrol-field-type" for="first_name">';
		//$display .= '<i class="fa fa-user"></i>';	
		$display .= '<span>'.__('First Name', 'users-control').' '.$required_text.'</span></label>';					
		$display .= '<div class="userscontrol-field-value">';
		
					$display .= '<input type="text" class="'.$required_class.' userscontrol-input " name="first_name" id="first_name" value="'.$userscontrol->get_post_value('first_name').'" title="'.__('Type your First Name','users-control').'"  placeholder="'.__('Type your First Name','users-control').'" data-errormessage-value-missing="'.__(' * This input is required!','users-control').'"/>';					
					$display .= '</div>'; //end field value
					
		$display .= '</div>'; //end field
		
		//Last name
		$display .= '<div class="userscontrol-profile-field '.$two_cols.'">';									
		$display .= '<label class="userscontrol-field-type" for="last_name">';
		//$display .= '<i class="fa fa-user"></i>';	
		$display .= '<span>'.__('Last Name', 'users-control').' '.$required_text.'</span></label>';					
		$display .= '<div class="userscontrol-field-value">';
		
					$display .= '<input type="text" class="'.$required_class.' userscontrol-input " name="last_name" id="last_name" value="'.$userscontrol->get_post_value('last_name').'" title="'.__('Type your Last Name','users-control').'"  placeholder="'.__('Type your Last Name','users-control').'" data-errormessage-value-missing="'.__(' * This input is required!','users-control').'"/>';					
					$display .= '</div>'; //end field value
					
		$display .= '</div>'; //end field
		
		//User
		$display .= '<div class="userscontrol-profile-field '.$two_cols.'">';									
		$display .= '<label class="userscontrol-field-type" for="user_name">';
		//$display .= '<i class="fa fa-user"></i>';	
		$display .= '<span>'.__('Username', 'users-control').' '.$required_text.'</span></label>';					
		$display .= '<div class="userscontrol-field-value">';
		
					$display .= '<input type="text" class="'.$required_class.' userscontrol-input " name="user_name" id="user_name" value="'.$userscontrol->get_post_value('user_name').'" title="'.__('Type your Username','users-control').'"  placeholder="'.__('Type your Username','users-control').'" data-errormessage-value-missing="'.__(' * This input is required!','users-control').'"/>';					
					$display .= '</div>'; //end field value
					
		$display .= '</div>'; //end field	
		
		//check Password Strenght		
		if(isset( $userscontrol_passwordstrength)){
			
			if($userscontrol->get_option('registration_password_ask')==1){
				
				//Password
				$display .= '<div class="userscontrol-profile-field '.$two_cols.'">';									
				$display .= '<label class="userscontrol-field-type" for="user_password">';
				//$display .= '<i class="fa fa-user"></i>';	
				$display .= '<span>'.__('Password', 'users-control').' '.$required_text.'</span></label>';					
				$display .= '<div class="userscontrol-field-value">';
				
				$display .= '<input type="password" class="'.$required_class.' userscontrol-input " name="user_password" id="user_password" value="'.$userscontrol->get_post_value('user_password').'" title="'.__('Type your Password','users-control').'"  placeholder="'.__('Type your Password','users-control').'" data-errormessage-value-missing="'.__(' * This input is required!','users-control').'"/>';					
				$display .= '</div>'; //end field value
							
				$display .= '</div>'; //end field	
			
			}
		
		}	
		
		
		$display .= '<div class="userscontrol-profile-field '.$two_cols.'">';									
		$display .= '<label class="userscontrol-field-type" for="email">';
		//$display .= '<i class="fa fa-user"></i>';	
		$display .= '<span>'.__('Email', 'users-control').' '.$required_text.'</span></label>';	
		
		
		$help = __('The login information will be sent to this email address. A random password will be generated and you can change it later from your account.','users-control');	
					
		$display .= '<div class="userscontrol-field-value">';
		
					$display .= '<input type="text" class="'.$required_class.' userscontrol-input " name="email" id="email" value="'.$userscontrol->get_post_value('email').'" title="'.__('Type your Email','users-control').'"  placeholder="'.__('Type your Email','users-control').'" data-errormessage-value-missing="'.__(' * This input is required!','users-control').'"/>';
					$display .= '<div class="userscontrol-help">'.$help.'</div>';
									
					$display .= '</div>'; //end field value
					
		$display .= '</div>'; //end field

		//--------CUSTOM FIELDS SECTION
		$display .= $this->get_custom_signup_fields($args, $two_cols ); 
		//--------ENDS FIELDS SECTION
		
		//Paid Membership active		
		if($userscontrol->get_option('registration_rules')==4){
			$display .= '<div class="userscontrol-profile-separator">'.__('Membership Options','users-control').'</div>';
			$display .= '<div class="userscontrol-profile-field">';				
			$display .=$userscontrol->membership->get_public_packages();			
			$display .= '</div>'; //end field
			$display .= '<div class="userscontrol-profile-separator" id="userscontrol-payment-header">'.__('Payment Options','users-control').'</div>';
			$display .=$this->get_available_payment_options();			
		}
		
		
		/*If aweber*/		
		if($userscontrol->get_option('newsletter_active')=='aweber' && $userscontrol->get_option('aweber_consumer_key')!="" && isset($userscontrol_aweber) && !is_user_logged_in()){
			
			//new aweber field			
			$aweber_text = stripslashes($userscontrol->get_option('aweber_text'));
			$aweber_header_text = stripslashes($userscontrol->get_option('aweber_header_text'));
			
			if($aweber_header_text==''){
				$aweber_header_text = __('Receive Daily Updates ', 'users-control');				
			}	
			
			if($aweber_text==''){
				$aweber_text = __('Yes, I want to receive daily updates. ', 'users-control');				
			}			
			
			$aweber_autchecked = $userscontrol->get_option('aweber_auto_checked');
			
			$aweber_auto = '';
			if($aweber_autchecked==1){				
				$aweber_auto = 'checked="checked"';				
			}
			
			 $display .= '<div class="userscontrol-profile-separator">'.$aweber_header_text.'</div>';			 
			 $display .= '<div class="userscontrol-profile-field " style="text-align:left">';			
						
			 $display .= '<input type="checkbox"  title="'.$aweber_header_text.'" name="userscontrol-aweber-confirmation"  id="userscontrol-aweber-confirmation" value="1"  '.$aweber_auto.' > <label for="userscontrol-aweber-confirmation"><span></span>'.$aweber_text.'</label>' ;								
			 $display .= '</div>';
			
		
		}
		
		//recaptcha			
		if(isset($userscontrol_recaptcha) && $userscontrol->get_option('recaptcha_site_key')!='' && $userscontrol->get_option('recaptcha_secret_key')!='' && $userscontrol->get_option('recaptcha_display_registration')=='1'){	
			$display .= '<div class="userscontrol-profile-field">';			
			$display .= $userscontrol_recaptcha->recaptcha_field(); 				
			$display .= '</div>'; 		
		}
		
		$display .= '<div class="userscontrol-profile-field">';

		if($userscontrol->get_option('gateway_stripe_active')=='1' && isset($userscontrol_stripe) ){
			$display .= '<button name="userscontrol-btn-book-app-confirm"  id="card-button" class="userscontrol-button-submit-changes">'.__('Submit','users-control').'	</button>';
			
			$other_payment_button_visible = 'style="display:none"';
			$card_button_visible = 'style="display:none"';


		}else{

			$other_payment_button_visible = '';
		}
			
		
		$display .= '<button type="button" '.$other_payment_button_visible.' id="userscontrol-btn-conf-signup" class="userscontrol-button-submit-changes">'.__('Submit','users-control').'	</button>';	
					
			$display .= '<br><br>';	
			$display .= '<p id="userscontrol-stripe-payment-errors"></p>';
					
			$login_link = $this->get_login_page();
					
			if($login_link=='NO'){
				$login_page = __('Please set a login page.','users-control');
			}else{
				$login_page = '<a href="'.$login_link.'">'.__('Already have an account?','users-control').'</a>';					
			}
					
			$display .= '<p class="userscontrol-pass-reset-link">'.$login_page.'</p>';				
		$display .= '</div>'; //end submit button	
		
		$display .= '</form>'; //end registration form
		$display .= '</div>'; //end registration form
		$display .= '</div>'; //end bup main cont
		
		
		return $display;
	}

	public function get_custom_signup_fields($args=array(), $two_cols){
		global $userscontrol;

		extract( $args, EXTR_SKIP );

		$display = '';

		$custom_form = '';
		if(isset($_GET["userscontrol-custom-form-id"])){ 
			$custom_form=sanitize_text_field($_GET["userscontrol-custom-form-id"]);
		}
		
		/* Get end of array */			
		if($form_id!="" || $custom_form !=""){
			//do we have a pre-set value in the get?			
			if($custom_form !=""){
				$form_id =$custom_form;			
			}
			
			$custom_form = 'userscontrol_profile_fields_'.$form_id;		
			$array = get_option($custom_form);			
			$fields_set_to_update =$custom_form;
		}else{
			
			$array = get_option('userscontrol_profile_fields');
			$fields_set_to_update ='userscontrol_profile_fields';
		}
		
		if(!is_array($array)){
			$array = array();
		
		}	

		
		$i_array_end = end($array);		
		if(isset($i_array_end['position'])){
		    $array_end = $i_array_end['position'];		    
			if (isset($array[$array_end]['type']) && $array[$array_end]['type'] == 'seperator'){
				if(isset($array[$array_end])){
					unset($array[$array_end]);
				}
			}
		}		
		
		/*Display custom profile fields added by the user*/		
		foreach($array as $key => $field) {

			extract($field);
			$field_legends = '';
			
			// WP 3.6 Fix
			if(!isset($deleted))
			    $deleted = 0;
			
			if(!isset($private))
			    $private = 0;
			
			if(!isset($required))
			    $required = 0;
			
			$required_class = '';
			$required_text = '';
			if($required == 1 && in_array($field, $this->include_for_validation)){				
			    $required_class = 'validate[required] ';
				$required_text = '(*)';				
			}
						
			/* separator */
            if ($type == 'separator' && $deleted == 0 && $private == 0 && isset($array[$key]['show_in_register']) && $array[$key]['show_in_register'] == 1){
                $display .= '<div class="userscontrol-profile-separator">'.$name.'</div>';
            }			
					
			//check if display emtpy		
				
			if ($type == 'usermeta' && $deleted == 0 && $private == 0 && isset($array[$key]['show_in_register']) && $array[$key]['show_in_register'] == 1){
				
				if($field!='textarea'){
					$display .= '<div class="userscontrol-profile-field '.$two_cols.'">';
				}else{
					$display .= '<div class="userscontrol-profile-field">';
				}
				
				/* Show the label */
				if (isset($array[$key]['name']) && $name){
					 
					if ( $field_legends!='no'){
						
						$display .= '<label class="userscontrol-field-type" for="'.$meta.'">';	
						
						$tooltipip_class = '';					
						if (isset($array[$key]['tooltip']) && $tooltip)	{
							$qtip_classes = 'qtip-light ';	
							$qtip_style = '';			
							 $tooltipip_class = '<a class="'.$qtip_classes.' userscontrol-tooltip" title="' . $tooltip . '" '.$qtip_style.'><i class="fa fa-info-circle reg_tooltip"></i></a>';
						} 	
						
						if (isset($array[$key]['icon']) && $icon) {
							$display .= '<i class="fa fa-'.$icon.'"></i>';
						} else {

						}

												
						$display .= '<span>'.$name. ' '.$required_text.' '.$tooltipip_class.'</span></label>';
						
					}				
					
				}else{
					
					$display .= '<label class="">&nbsp;</label>';
				}
				
				$display .= '<div class="userscontrol-field-value">';
				
				$placeholder = '';				
				if($placeholders=='yes'){
					$placeholder = 'placeholder="'.$name.'"';
				}
					
				switch($field) {
					
					case 'textarea':
							$display .= '<textarea class="'.$required_class.' userscontrol-input userscontrol-input-text-area" rows="10" name="'.$meta.'" id="'.$meta.'" title="'.$name.'" '.$placeholder.' data-errormessage-value-missing="'.__(' * This input is required!','users-control').'">'.$this->get_post_value($meta).'</textarea>';
							break;
							
					case 'text':
							$display .= '<input type="text" class="'.$required_class.' userscontrol-input"  name="'.$meta.'" id="'.$meta.'" value="'.$this->get_post_value($meta).'"  title="'.$name.'"  '.$placeholder.' data-errormessage-value-missing="'.__(' * This input is required!','users-control').'"/>';
							break;							
							
					case 'datetime':						
						    $display .= '<input type="text" class="'.$required_class.' userscontrol-input userscontrol-datepicker" name="'.$meta.'" id="'.$meta.'" value="'.$this->get_post_value($meta).'"  title="'.$name.'" />';
						    break;
							
					case 'select':						
							
						if (isset($array[$key]['predefined_options']) && $array[$key]['predefined_options']!= '' && $array[$key]['predefined_options']!= '0' ){
								$loop = $userscontrol->commmonmethods->get_predifined( $array[$key]['predefined_options'] );
								
							}elseif (isset($array[$key]['choices']) && $array[$key]['choices'] != '') {
								
															
								$loop = $userscontrol->userscontrol_one_line_checkbox_on_window_fix($choices);
								 	
								
							}
							
						if (isset($loop)){
								$display .= '<select class="'.$required_class.' userscontrol-input" name="'.$meta.'" id="'.$meta.'" title="'.$name.'" data-errormessage-value-missing="'.__(' * This input is required!','users-control').'">';
								
								foreach($loop as $option){									
									$option = trim(stripslashes($option));						
									$display .= '<option value="'.$option.'" '.selected( $this->get_post_value($meta), $option, 0 ).'>'.$option.'</option>';
								}
								$display .= '</select>';
						}
							
							break;
							
					case 'radio':						
						
							if($required == 1 && in_array($field, $this->include_for_validation)){
								$required_class = "validate[required] radio ";
							}
						
							if (isset($array[$key]['choices']))	{		
								 $loop = $userscontrol->userscontrol_one_line_checkbox_on_window_fix($choices);
							}
							if (isset($loop) && $loop[0] != '') {
							  $counter =0;
							  
								foreach($loop as $option){
								    if($counter >0)
								        $required_class = '';
								    
								    $option = trim(stripslashes($option));
									$display .= '<input type="radio" class="'.$required_class.'" title="'.$name.'" name="'.$meta.'" id="userscontrol_multi_radio_'.$meta.'_'.$counter.'" value="'.$option.'" '.checked( $this->get_post_value($meta), $option, 0 );
									$display .= '/> <label for="userscontrol_multi_radio_'.$meta.'_'.$counter.'"><span></span>'.$option.'</label>';
									
									$counter++;									
								}
							}
							
							break;
							
						case 'checkbox':
						
						
							if($required == 1 && in_array($field, $this->include_for_validation)){
								$required_class = "validate[required] checkbox ";
							}						
						
							if (isset($array[$key]['choices']))	{
																
								 $loop = $userscontrol->userscontrol_one_line_checkbox_on_window_fix($choices);
							}
							
							if (isset($loop) && $loop[0] != '') {
							  $counter =0;
							  
								foreach($loop as $option){
								   
								   if($counter >0)
								        $required_class = '';
								  
								  $option = trim(stripslashes($option));
								  
								  $display .= '<div class="userscontrol-checkbox"><input type="checkbox" class="'.$required_class.'" title="'.$name.'" name="'.$meta.'[]" id="userscontrol_multi_box_'.$meta.'_'.$counter.'" value="'.$option.'" ';
									if (is_array($this->get_post_value($meta)) && in_array($option, $this->get_post_value($meta) )) {
									$display .= 'checked="checked"';
									}
									$display .= '/> <label for="userscontrol_multi_box_'.$meta.'_'.$counter.'"> '.$option.'</label> </div>';
									
									
									$counter++;
								}
							}
							
							break;
							
						
													
						case 'password':						
							$display .= '<input type="password" class="userscontrol-input'.$required_class.'" title="'.$name.'" name="'.$meta.'" id="'.$meta.'" value="'.$this->get_post_value($meta).'" />';
							
							if ($meta == 'user_pass') {
								
							$display .= '<div class="userscontrol-help">'.__('If you would like to change the password type a new one. Otherwise leave this blank.','users-control').'</div>';
							
							} elseif ($meta == 'user_pass_confirm') {
								
							$display .= '<div class="userscontrol-help">'.__('Type your new password again.','users-control').'</div>';
							
							}
							break;
							
					}					
					
					if (isset($array[$key]['help_text']) && $help_text != '') {
						$display .= '<div class="userscontrol-help">'.$help_text.'</div>';
					}						
				$display .= '</div>';
				$display .= '</div>';
			}
		}

		return $display;




	}

	/*Post value*/
	function get_post_value($meta) {				
		if (isset($_POST['userscontrol-register-form'])) {
			if (isset($_POST[$meta]) ) {
				return sanitize_text_field($_POST[$meta]);
			}
		} else {
			if (strstr($meta, 'country')) {
			return 'United States';
			}
		}
	}

	/**
	 * This has been added to avoid the window server issues
	 */
	public function userscontrol_one_line_checkbox_on_window_fix($choices){		
		
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
	
	public function userscontrol_if_windows_server(){
		$os = PHP_OS;
		$os = strtolower($os);			
		$pos = strpos($os, "win");	
		if ($pos === false) {			
			return false;
		} else {
			return true;
		}			
	}
	
	function get_available_payment_options(){
		
		global $userscontrol, $userscontrol_recaptcha, $userscontrol_stripe;
		
		$display = '';
		
		$required_class = ' validate[required]';
		
		 /*Bank*/		
		if($userscontrol->get_option('gateway_bank_active')=='1')
		{
			//custom label
			
			$custmom_label = $userscontrol->get_option('gateway_bank_label');
			if($custmom_label=='')
			{
				$custmom_label = __('I will pay locally','users-control');
			
			}
			
			$display_payment_method = '<input type="radio" class="'.$required_class.' userscontrol_payment_options" title="" name="userscontrol_payment_method" id="userscontrol_payment_method_bank" value="bank" data-method="bank" /> <label for="userscontrol_payment_method_bank"><span></span>'.$custmom_label.'</label>';
												 
			$display .= '<div class="userscontrolprofile-field">';
			$display .= '<label class="userscontrol-field-type" for="userscontrol_payment_method_bank">';			
			$display .= '<span>'.$display_payment_method.' </span></label>';
			$display .= '<div class="userscontrol-field-value">';
			$display .= '</div>';				
			$display .= '</div>';				
			
		
		
		}
		
		
		/*Paypal*/		
		if($userscontrol->get_option('gateway_paypal_active')=='1')	{
			$paypal_logo = userscontrol_url.'templates/basic/img/paypal-logo.jpg';
			$display_payment_method = '<input type="radio" class="'.$required_class.' userscontrol_payment_options" title="" name="userscontrol_payment_method" id="userscontrol_payment_method_paypal" value="paypal" data-method="paypal"/> <label for="userscontrol_payment_method_paypal"><span></span>'.__('Pay with PayPal','users-control').'<br><img align="absmiddle"  src="'.$paypal_logo.'" style="top:5px;"></label>';	
			
												 
			$display .= '<div class="userscontrol-profile-field" id="userscontrol-method-paypal">';
			$display .= '<label class="userscontrol-field-type" for="userscontrol_payment_method_paypal">';			
			$display .= '<span>'.$display_payment_method.' </span></label>';
			$display .= '<div class="userscontrol-field-value">';
			$display .= '</div>';				
			$display .= '</div>';		
		
		}
		
		/*Stripe*/		
		$display_card_button = false;
		if($userscontrol->get_option('gateway_stripe_active')=='1' && isset($userscontrol_stripe))
		{

				$cc_logo = userscontrol_url.'templates/basic/img/creditcard-icon.png';
				$display_payment_method = '<input type="radio" class="'.$required_class.' userscontrol_payment_options" title="" name="userscontrol_payment_method" id="userscontrol_payment_method_stripe" value="stripe"  data-method="stripe" checked /> <label for="userscontrol_payment_method_stripe"><span></span>'.__('Pay with Credit Card','users-control').'<br><img align="absmiddle"  src="'.$cc_logo.'" style="top:5px; "></label>';	
				
				$display .= '<input type="hidden"  name="userscontrol_payment_method_stripe_hidden" id="userscontrol_payment_method_stripe_hidden" value="stripe" >';
										 
				$display .= '<div class="userscontrol-profile-field" id="userscontrol-method-stripe">';
				$display .= '<label class="userscontrol-field-type" for="userscontrol_payment_method_stripe">';			
				$display .= '<span>'.$display_payment_method.' </span></label>';
				
				$display .= '<div class="userscontrol-field-value">';
				$display .= '</div>';				
				$display .= '</div>'; 
				$display .= '<div class="userscontrol-profile-field-cc" id="userscontrol-strip-cc-form">';
				$display_card_button = true;							
				$display .= '<label><input type="radio" name="nuv_payment_method" class="nuv_payment_method" value="cc" id="RadioGroup1_0" checked />
											'.__('Credit Card','users-control').'';

				$display .= '</label>';

				$display .='<div id="nuva-creditcard-option" class="nuva-p-options-div"> ';							
				$display .='<div id="card-field" class="userscontrol-cc-fieldform"></div>
											<span id="card-errors" class="card-errors"></span> ';	
				$display .='</div>';					
				$display .= '</div>'; //field

		



			
			//cc form
			
		/*	$display .= '<div class="userscontrol-profile-field-cc" id="userscontrol-strip-cc-form">';
			
			$display .= '<div class="userscontrol-cc-frm-left" >';
			
			$display .= '<label class="ab-formLabel"><strong class="bup-cc-strong-t"> '.__('Credit Card Number','users-control').'</strong></label>';
			$display .= '<div class="userscontrol-profile-field"><input class="card-number" type="text" id="userscontrol_card_number"  autocomplete="off" data-stripe="number">'.'</div>';
			
			$display .= '</div>'; //left
			
			$display .= '<div class="userscontrol-cc-frm-right" >';				
			$display .= '<label class="userscontrol-formLabel"> <strong class="userscontrol-cc-strong-t">'.__('Expiration Date','users-control').'</strong></label>';
			$display .= '<div class="userscontrol-profile-field"><select id="userscontrol_card_exp_month" class="card-expiry-month" style="width: 60px;float: left; margin-left: 10px;" data-stripe="exp-month">'.$userscontrol->commmonmethods->get_select_value(1,12).'</select><select id="userscontrol_card_exp_year" class="userscontrol-expiry-year" style="width: 80px;float: left; margin-left: 10px;" data-stripe="exp-year">'.$userscontrol->commmonmethods->get_select_value(date('Y'),date('Y')+10).'</select>'.'</div>';
			
			$display .= '</div>'; //right				
							
			$display .= '</div>'; //field
			
			$display .= '<div class="userscontrol-profile-field-cc" id="userscontrol-strip-cc-form-sec">';
			
			$display .= '<div class="userscontrol-cc-frm-left" >';
			
			$display .= '<label class="userscontrol-formLabel"><strong class="userscontrol-cc-strong-t"> '.__('Card Security Code','users-control').'</strong></label>';
			$display .= '<div class="userscontrol-profile-field"><input class="card-cvc" type="text" id="userscontrol_card_number"  autocomplete="off" style="width:60px" data-stripe="cvc">'.'</div>';
			
			$display .= '</div>'; //left
			
			$display .= '</div>'; //field */
					
		
		}	
		
			
			return $display;
		
	
	
	}
	
	public function get_client_login_form($args=array()) 
	{
		
		global $userscontrol, $userscontrol_recaptcha;
        $required_text ="";
        $required_class ="";
		
		/* Arguments */
		$defaults = array(       
			'redirect_to' => null,
			'form_header_text' => __('Login','users-control')
			
        		    
		);
		$args = wp_parse_args( $args, $defaults );
		$args_2 = $args;
		extract( $args, EXTR_SKIP );
		
		$display = null;	
		
		$display .= '<div class="userscontrol-front-cont">';
		
	    $display .= '<div class="userscontrol-user-data-registration-form">';
		
		/*Display errors*/
		if (isset($_POST['userscontrol-client-form-confirm']))
		{
			$display .= $this->get_errors();
		}
		
		
		$display .= '<form action="" method="post" id="userscontrol-client-form" name="userscontrol-client-form" enctype="multipart/form-data">';
		
		$display .= '<input type="hidden" name="userscontrol-client-form-confirm" id="userscontrol-client-form-confirm" >';

		
		$display .= '<div class="userscontrol-profile-separator">'.__('Login data','users-control').'</div>';
		
		
		$display .= '<div class="userscontrol-profile-field">';									
		$display .= '<label class="userscontrol-field-type" for="user_email_2">';
		$display .= '<i class="fa fa-user"></i>';	
		$display .= '<span>'.__('Username or Email', 'users-control').' '.$required_text.'</span></label>';
						
					
					
		$display .= '<div class="userscontrol-field-value">';
		
					$display .= '<input type="text" class="'.$required_class.' userscontrol-input " name="user_login" id="user_login" value="'.$userscontrol->get_post_value('user_login').'" title="'.__('Type your Username or Email','users-control').'"  placeholder="'.__('Type your Username or Email','users-control').'" data-errormessage-value-missing="'.__(' * This input is required!','users-control').'"/>';					
					$display .= '</div>'; //end field value
					
		$display .= '</div>'; //end field
		
		
		$display .= '<div class="userscontrol-profile-field">';									
		$display .= '<label class="userscontrol-field-type" for="login_user_pass">';
		$display .= '<i class="fa fa-lock"></i>';	
		$display .= '<span>'.__('Password', 'users-control').' '.$required_text.'</span></label>';
						
					
					
		$display .= '<div class="userscontrol-field-value">';
		
					$display .= '<input type="password" class="'.$required_class.' userscontrol-input " name="login_user_pass" id="login_user_pass" value="'.$userscontrol->get_post_value('login_user_pass').'" title="'.__('Type your Password','users-control').'"  placeholder="'.__('Type your Password','users-control').'" data-errormessage-value-missing="'.__(' * This input is required!','users-control').'"/>';					
					$display .= '</div>'; //end field value
					
		$display .= '</div>'; //end field
		
		
		//recaptcha			
		if(isset($userscontrol_recaptcha) && $userscontrol->get_option('recaptcha_site_key')!='' && $userscontrol->get_option('recaptcha_secret_key')!='' && $userscontrol->get_option('recaptcha_display_loginform')=='1'){	
		
			$display .= '<div class="userscontrol-profile-field">';			
			$display .= $userscontrol_recaptcha->recaptcha_field(); 				
			$display .= '</div>'; 		
		}
		
		$display .= '<div class="userscontrol-profile-field">';
		
					$display .= '<button name="userscontrol-btn-book-app-confirm-login" type="submit"  class="userscontrol-button-submit-changes">'.__('Submit','users-control').'	</button>';	
					
					$display .= '<br><br>';	
					
					$reset_link = $this->get_password_recover_page();
					
					if($reset_link=='NO'){
						
						$reset_password = __('Please set a password reset page.','users-control');
						
					}else{
						
						$reset_password = '<a href="'.$reset_link.'">'.__('Forgot Password?','users-control').'</a>';				
					
					}
					
					$display .= '<p class="userscontrol-pass-reset-link">'.$reset_password.'</p>';
					
					$signup_link = $this->get_registration_page();
					
					if($signup_link=='NO'){
						
						$registrationpage = __('Please set a registration page.','users-control');
						
					}else{
						
						$registrationpage = '<a href="'.$signup_link.'">'.__("Don't you have an account?",'users-control').'</a>';				
					
					}
					
					$display .= '<p class="userscontrol-pass-reset-link">'.$registrationpage.'</p>';
					
					
					
					
									
								
				
		$display .= '</div>'; //end submit button	
		
		
		
		
		$display .= '</form>'; //end registration form
		$display .= '</div>'; //end registration form
		$display .= '</div>'; //end bup main cont
		
		
		return $display;
	}
	
	public function get_client_recover_password_form($args=array()) 
	{
		
		global $userscontrol, $userscontrol_recaptcha;
		
        $required_text ="";
        $required_class = "";
       
        
		/* Arguments */
		$defaults = array(       
			'redirect_to' => null,
			'form_header_text' => __('Recover Password','xoousers')
			
        		    
		);
		$args = wp_parse_args( $args, $defaults );
		$args_2 = $args;
		extract( $args, EXTR_SKIP );
		
		$display = null;	
		
		$display .= '<div class="userscontrol-front-cont">';		
	    $display .= '<div class="userscontrol-user-data-registration-form">';
		
		/*Display errors*/
		if (isset($_POST['userscontrol-client-recover-pass-form-confirm']))
		{
			$display .= $this->get_errors_reset();
			$display .= $this->get_sucess_message_reset;
		}		
		
		$display .= '<form action="" method="post" id="userscontrol-client-recover-pass-form" name="userscontrol-client-recover-pass-form" enctype="multipart/form-data">';
		
		if(isset($_GET['resskey']) && $_GET['resskey']!='') //this is a reset confirmation form
		{   
		    $icon = 'fa fa-lock';
			$type = 'password';
			$type_password=true;
			$reset_password_button='userscontrol-reset-password-button-conf';
            
            $resskey = sanitize_text_field($_GET['resskey']);
			
			$display .= '<input type="hidden" name="userscontrol-client-recover-pass-form-confirm-reset" id="userscontrol-client-recover-pass-form-confirm-reset" >';	
			
			$display .= '<input type="hidden" name="userscontrol_reset_key" id="userscontrol_reset_key" value="'. $resskey.'" >';	
			
			$legend = __('Type your new password', 'users-control');
			$legend2 = __('Re-Type your password', 'users-control');
				
		
		}else{ //the user is requestin a new password
		
			$icon = 'fa fa-user';
			$type = 'text';
			$reset_password_button='';
			$type_password=false;	
			$legend = __('Username or Email', 'users-control');		
			$display .= '<input type="hidden" name="userscontrol-client-recover-pass-form-confirm" id="userscontrol-client-recover-pass-form-confirm" >';						
		
		}
		
		$display .= '<div class="userscontrol-profile-separator">'.__('Recover your password','users-control').'</div>';					
		
		$display .= '<div class="userscontrol-profile-field">';									
		$display .= '<label class="userscontrol-field-type" for="user_email_2">';
		$display .= '<i class="'.$icon.'"></i>';
		
		$display .= '<span>'.$legend .' '.$required_text.'</span></label>';					
					
		$display .= '<div class="userscontrol-field-value">';
		
					$display .= '<input type="'.$type .'" class="'.$required_class.' userscontrol-input " name="user_login_reset" id="user_login_reset" value="'.$userscontrol->get_post_value('user_login_reset').'" title="'.__('Type your Password','users-control').'"  placeholder="'.__('Type your Password','users-control').'" data-errormessage-value-missing="'.__(' * This input is required!','users-control').'"/>';					
		
		
		$display .= '</div>'; //end field value
					
		$display .= '</div>'; //end field
		
		if($type_password){
			
			$display .= '<div class="userscontrol-profile-field">';									
			$display .= '<label class="userscontrol-field-type" for="user_email_2">';
			$display .= '<i class="'.$icon.'"></i>';
			
			$display .= '<span>'.$legend2 .' '.$required_text.'</span></label>';					
						
			$display .= '<div class="userscontrol-field-value">';
			
						$display .= '<input type="password" class="'.$required_class.' userscontrol-input " name="user_password_reset_2" id="user_password_reset_2" value="'.$userscontrol->get_post_value('user_login_reset').'" title="'.__('Type your new password again','users-control').'"  placeholder="'.__('Type your new password again','users-control').'" data-errormessage-value-missing="'.__(' * This input is required!','users-control').'"/>';					
			
			$display .= '</div>'; //end field value						
			$display .= '</div>'; //end field
		}
		
		//recaptcha			
		if(isset($userscontrol_recaptcha) && $userscontrol->get_option('recaptcha_site_key')!='' && $userscontrol->get_option('recaptcha_secret_key')!='' && $userscontrol->get_option('recaptcha_display_forgot_password')=='1'){	
			
			if(!isset($_GET['resskey']) ) //do not display for password reset confirmation
			{
				$display .= '<div class="userscontrol-profile-field">';			
				$display .= $userscontrol_recaptcha->recaptcha_field(); 				
				$display .= '</div>'; 			
			}		
		}
		
		
		$display .= '<div class="userscontrol-profile-field">';
		 			
					if(!isset($_GET['resskey']) ) //do not display for password reset confirmation
					{
						$display .= '<button name="userscontrol-btn-book-app-confirm-resetlink" id="userscontrol-btn-book-app-confirm-resetlink-1" type="submit"  class="userscontrol-button-submit-changes '.$reset_password_button.'">'.__('Submit','users-control').'	</button>';
					
					}else{
						$display .= '<button name="userscontrol-btn-book-app-confirm-resetlink" id="userscontrol-btn-book-app-confirm-resetlink" type="button"  class="userscontrol-button-submit-changes '.$reset_password_button.'">'.__('Confirm','users-control').'	</button>';
					}
					
					$display .= '<span id="userscontrol-pass-reset-message">&nbsp;</span>';
					
					$display .= '<br><br>';	
					
					$reset_link = $this->get_login_page();
					
					if($reset_link=='NO'){
						
						$reset_password = __('Please set a login page.','users-control');
						
					}else{
						
						$reset_password = '<a href="'.$reset_link.'">'.__('Login to your account?','users-control').'</a>';					
					}
					
					$display .= '<p class="userscontrol-pass-reset-link">'.$reset_password.'</p>';							
				
		$display .= '</div>'; //end submit button	
		
		
		$display .= '</form>'; //end registration form
		$display .= '</div>'; //end registration form
		$display .= '</div>'; //end bup main cont
		
		return $display;
	}
	
	
	public function confirm_reset_password(){
		global $wpdb,  $userscontrol, $wp_rewrite;	
		$wp_rewrite = new WP_Rewrite();
		
		//check redir		
		$account_page_id = $userscontrol->get_option('login_page_id');
		$my_account_url = get_permalink($account_page_id);
		$PASSWORD_LENGHT =7;
		$password1 =  sanitize_text_field($_POST['p1']);
		$password2 =  sanitize_text_field($_POST['p2']);
		$key = sanitize_text_field( $_POST['key']);
		
		$html = '';
		$validation = '';
		
		//check password		
		if($password1!=$password2){
			$validation .= "<div class='userscontrol-ultra-error'>".__(" ERROR! Password must be identical ", 'users-control')."</div>";
			$html = $validation;			
		}
		
		if(strlen($password1)<$PASSWORD_LENGHT){
			$validation .= "<div class='userscontrol-ultra-error'>".__(" ERROR! Password should contain at least 7 alphanumeric characters ", 'users-control')."</div>";
			$html = $validation;		
		}		
		
		$user = $this->get_one_user_with_key($key);		
		if($validation=="" ){			
			if($user->ID >0 ){
				$user_id = $user->ID;
				$user_email = $user->user_email;
				$user_login = $user->user_login;
				wp_set_password( $password1, $user_id ) ;
				//notify user				
				$userscontrol->messaging->send_new_password_to_user($user, $password1);				
				$html = "<div class='userscontrol-ultra-success'>".__(" Success!! The new password has been changed. Please click on the login link to get in your account.", 'users-control')."</div>";
			}else{
				$html = "<div class='userscontrol-ultra-error'>".__(" ERROR! Invalid reset link ", 'users-control')."</div>";
			}					
		}
		
		echo wp_kses($html, $userscontrol->allowed_html);
		die();
	}
	
	
	function get_one_user_with_key($key)
	{
		global $wpdb,  $userscontrol;
		
		$args = array( 	
						
			'meta_key' => 'userscontrol_ultra_very_key',                    
			'meta_value' => $key,                  
			'meta_compare' => '=',  
			'count_total' => true,   


			);
		
		 // Create the WP_User_Query object
		$user_query = new WP_User_Query( $args );
		 
		// Get the results//
		$users = $user_query->get_results();	
		
		if(count($users)>0)
		{
			foreach ($users as $user)
			{
				return $user;
			
			}
			
		
		}else{			
			
			
		}		
	
	}	
	
	
}
$key = "profile";
$this->{$key} = new UserscontrolProfile();
?>