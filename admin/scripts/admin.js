var $ = jQuery;

jQuery(document).ready(function($) {	
	
	$("#userscontrol-registration-form").validationEngine({promptPosition: 'inline'});
	jQuery("#userscontrol-add-new-custom-field-frm").slideUp();	 
	jQuery( "#tabs-bupro" ).tabs({collapsible: false	});
	jQuery( "#userscontrol-bupro-settings" ).tabs({collapsible: false	});	
	
	jQuery(document).on("click", ".userscontrol-location-front", function(e) {

		var dep_id =  jQuery(this).attr("depto-id");
    	jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "userscontrol_get_custom_department_fields", "department_id": dep_id },
					
					success: function(data){							
																
						jQuery("#wp-custom-fields-public").html(data);											

					}
		});			
			
			 
				
    });
	
	/* 	Close Open File Uploader */		
	jQuery(document).on("click", "#userscontrol-ticket-file-uploader-btn", function(e) {
		jQuery("#wp-file-uploader-front").slideToggle();
		e.preventDefault();	
					
	});	
	
	/* 	Close Open Reply Box Uploader */		
	jQuery(document).on("click", "#userscontrol-ticket-reply-btn", function(e) {
		jQuery("#wp-add-reply-box").slideToggle();
		e.preventDefault();	
	});	
	
	
	jQuery(document).on("click", "#userscontrol-edit-subscription", function(e) {		
	
		var subscription_id =  jQuery(this).attr("subscription-id");		
		jQuery("#bup-spinner").show();		
			jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "userscontrol_subscription_edit", 
					"subscription_id": subscription_id,
					"_ajax_nonce": userscontrol_admin_v98.nonce,},
					
					success: function(data){					
					
						var res = data;
						jQuery("#userscontrol-edit-subscription-details" ).html( res );
						jQuery("#userscontrol-edit-subscription-details" ).dialog( "open" );
						
						jQuery( ".userscontrol-datepicker" ).datepicker({ 
								showOtherMonths: true, 
								dateFormat: userscontrol_admin_v98.date_picker_date_format, 
								closeText: userscontrolDatePicker.closeText,
								currentText: userscontrolDatePicker.currentText,
								monthNames: userscontrolDatePicker.monthNames,
								monthNamesShort: userscontrolDatePicker.monthNamesShort,
								dayNames: userscontrolDatePicker.dayNames,
								dayNamesShort: userscontrolDatePicker.dayNamesShort,
								dayNamesMin: userscontrolDatePicker.dayNamesMin,
								firstDay: userscontrolDatePicker.firstDay,
								isRTL: userscontrolDatePicker.isRTL,
								minDate: 0
							 });
							 
						jQuery("#ui-datepicker-div").wrap('<div class="ui-datepicker-wrapper" />');
						jQuery("#bup-spinner").hide();						

					}
				});				
			
				
			
    		e.preventDefault();		 
				
    });
	
	jQuery( "#userscontrol-edit-subscription-details" ).dialog({
			autoOpen: false,			
			width: '400px', // overcomes width:'auto' and maxWidth bug
   			
			responsive: true,
			fluid: true, //new option
			modal: true,
			buttons: {
			"Submit": function() {				
				
				var ret;
				var subscription_id=   jQuery("#subscription_id").val();
				var date_from=   jQuery("#date_from").val();
				var date_to=   jQuery("#date_to").val();
				var membership_status=   jQuery("#membership_status").val();
				var make_lifetime = jQuery("#userscontrol-makelifetime").prop("checked");
				
				if(make_lifetime){ make_lifetime = '1'; }else{make_lifetime = '0';};
				
				
				
				if(date_from==''){alert(wptu_admin_v98.msg_input_private_name); return;}
					
				jQuery("#userscontrol-err-message" ).html( '' );		
							
				jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "userscontrol_edit_subscription_confirm", 
						"subscription_id": subscription_id, 
						"date_from": date_from , 
						"date_to": date_to , 
						"membership_status": membership_status , 
						"make_lifetime": make_lifetime,
						"_ajax_nonce": userscontrol_admin_v98.nonce },
						
						success: function(data){
							
							
							var res =jQuery.parseJSON(data);				
							
							if(res.response=='OK')	
							{
																
								jQuery("#userscontrol-edit-subscription-details" ).dialog( "close" );	
								window.location.reload();								
														
							}else{ //ERROR
							
								jQuery("#userscontrol-message-err" ).html( res.content );	
							
							}
						}
					});
				
			},
			
			"Cancel": function() {
				jQuery( this ).dialog( "close" );
			},
			
			},
			close: function() {
			
			}
	});
		
	
	 // This sends a reset link to a staff member
	jQuery(document).on("click", "#userscontrol-save-acc-send-reset-link-staff", function(e) {
			
			var staff_id =  jQuery(this).attr("userscontrol-staff-id");		
						
			jQuery("#userscontrol-err-message" ).html( '' );	
			jQuery("#userscontrol-loading-animation-acc-resetlink-staff" ).show( );		
						
    		jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "userscontrol_send_welcome_email_to_staff",
					"staff_id": staff_id
					 
					 },
					
					success: function(data){					
						
						var res = data;	
						jQuery("#userscontrol-loading-animation-acc-resetlink-staff" ).hide( );						
						jQuery("#userscontrol-acc-resetlink-staff-message" ).html( res );						
							
						
						
						}
				});
			
			
			 // Cancel the default action
    		e.preventDefault();
			 
				
        });
	
	/* 	Close Open Sections in Dasbhoard */

	jQuery(document).on("click", ".userscontrol-widget-home-colapsable", function(e) {
		
		e.preventDefault();
		var widget_id =  jQuery(this).attr("widget-id");		
		var iconheight = 20;
		
		
		if(jQuery("#userscontrol-main-cont-home-"+widget_id).is(":visible")) 
	  	{
					
			jQuery( "#userscontrol-close-open-icon-"+widget_id ).removeClass( "fa-sort-asc" ).addClass( "fa-sort-desc" );
			
		}else{
			
			jQuery( "#userscontrol-close-open-icon-"+widget_id ).removeClass( "fa-sort-desc" ).addClass( "fa-sort-asc" );			
	 	 }
		
		
		jQuery("#userscontrol-main-cont-home-"+widget_id).slideToggle();	
					
		return false;
	});

	//this will crop the avatar and redirect
	jQuery(document).on("click touchstart", "#userscontrol-confirm-cover-cropping", function(e) {

			
		e.preventDefault();			
			
		var x1 = jQuery('#x1').val();
		var y1 = jQuery('#y1').val();
			
			
		var w = jQuery('#w').val();
		var h = jQuery('#h').val();
		var image_id = $('#image_id').val();
		var user_id = $('#user_id').val();				
			
		if(x1=="" || y1=="" || w=="" || h==""){
			alert("You must make a selection first");
			return false;
		}
			
		jQuery('#userscontrol-cropping-avatar-wait-message').html(message_wait_availability);
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "userscontrol_crop_cover_profile", "x1": x1 , "y1": y1 , "w": w , "h": h  , "image_id": image_id , "user_id": user_id},
				success: function(data){					
					//redirect				
					var site_redir = jQuery('#site_redir').val();
					window.location.replace(site_redir);	
				}
		});
		return false;
    	e.preventDefault();
    });
	
	
	
	//this will crop the avatar and redirect
	jQuery(document).on("click touchstart", "#userscontrol-confirm-avatar-cropping", function(e) {
			
		e.preventDefault();			
			
		var x1 = jQuery('#x1').val();
		var y1 = jQuery('#y1').val();
		var w = jQuery('#w').val();
		var h = jQuery('#h').val();
		var image_id = $('#image_id').val();
		var user_id = $('#user_id').val();				
			
		if(x1=="" || y1=="" || w=="" || h==""){
			alert("You must make a selection first");
			return false;
		}
			
		jQuery('#userscontrol-cropping-avatar-wait-message').html(message_wait_availability);
			
		jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "userscontrol_crop_avatar_user_profile_image", "x1": x1 , "y1": y1 , "w": w , "h": h  , "image_id": image_id , "user_id": user_id},
				
				success: function(data){					
					//redirect				
					var site_redir = jQuery('#site_redir').val();
					window.location.replace(site_redir);	
					
				}
		});
			
		     	
		return false;
    	e.preventDefault();	 
				
    });

	jQuery(document).on("click", "#userscontrol-btn-delete-user-avatar", function(e) {
		e.preventDefault();
		var user_id =  jQuery(this).attr("user-id");
		var redirect_avatar =  jQuery(this).attr("redirect-avatar");
    	jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "userscontrol_delete_user_avatar", "user_id": user_id },
				success: function(data){
				refresh_my_avatar();
				if(redirect_avatar=='yes'){
					var site_redir = jQuery('#site_redir').val();
					window.location.replace(site_redir);
							
					}else{
							
						refresh_my_avatar();
							
					}
											
						
					}
				});
			
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();
    });
		
	
	function refresh_my_avatar (){
		jQuery.post(ajaxurl, {
			action: 'refresh_avatar'}, function (response){									
			jQuery("#uu-backend-avatar-section").html(response);
		});
	}	
	
	jQuery(document).on("click", "#bup_re_schedule", function(e) {
			
		if ($(this).is(":checked")) {
            $("#bup-availability-box").slideDown();
			$("#bup-availability-box-btn").slideDown();
        } else {
				$("#bup-availability-box-btn").slideUp();				
                $("#bup-availability-box").slideUp();
       }			
				
    });
		
	jQuery(document).on("click", "#userscontrol-btn-validate-copy", function(e) {		
		e.preventDefault();
		var p_ded =  $('#p_serial').val();
		jQuery("#loading-animation").slideDown();		
		jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "userscontrol_vv_c_de_a", 
					"p_s_le": p_ded },
						
				success: function(data){
							
					jQuery("#loading-animation").slideUp();							
						
						jQuery("#bup-validation-results").html(data);
						jQuery("#bup-validation-results").slideDown();								
						setTimeout("hidde_noti('bup-validation-results')", 6000);
						setTimeout("window.location.reload();", 3000)
							
				}
		});
				
		return false;
	});
		
	
	/* 	FIELDS CUSTOMIZER -  ClosedEdit Field Form */
	
	jQuery(document).on("click", ".userscontrol-btn-close-edition-field", function(e) {	
		e.preventDefault();
		var block_id =  jQuery(this).attr("data-edition");		
		jQuery("#userscontrol-edit-fields-bock-"+block_id).slideUp();				
	});
	
	/* 	FIELDS CUSTOMIZER -  Add New Field Form */
	jQuery('#userscontrol-add-field-btn').on('click',function(e){
		
		e.preventDefault();
		jQuery("#userscontrol-add-new-custom-field-frm").slideDown();				
		return false;
	});
	
	/* 	FIELDS CUSTOMIZER -  Add New Field Form */
	jQuery('#userscontrol-close-add-field-btn').on('click',function(e){
		e.preventDefault();
		jQuery("#userscontrol-add-new-custom-field-frm").slideUp();				
		return false;
	});
	
	
	/* 	FIELDS CUSTOMIZER -  Edit Field Form */
	jQuery('#userscontrol__custom_registration_form').on('change',function(e){		
		e.preventDefault();
		userscontrol_reload_custom_fields_set();
					
	});
	
	
	/* 	FIELDS CUSTOMIZER - Delete Field */
	jQuery(document).on("click", ".userscontrol-delete-profile-field-btn", function(e) {
		e.preventDefault();		
		var doIt = false;		
		doIt=confirm(custom_fields_del_confirmation);
		  
		if(doIt){

			var p_id =  jQuery(this).attr("data-field");	
			var custom_form =  jQuery('#userscontrol__custom_registration_form').val();
		
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "userscontrol_delete_profile_field", 
				"_item": p_id , "custom_form": custom_form, "_ajax_nonce": userscontrol_admin_v98.nonce },
						
				success: function(data){					
					jQuery("#bup-sucess-delete-fields-"+p_id).slideDown();
					setTimeout("hidde_noti('bup-sucess-delete-fields-" + p_id +"')", 1000);
					jQuery( "#"+p_id ).addClass( "bup-deleted" );
					setTimeout("hidde_noti('" + p_id +"')", 1000);
					userscontrol_reload_custom_fields_set();		
				}
			});
			
		  }
		  else{
			
		  }		
		
				
		return false;
	});
	
	
	/* 	FIELDS CUSTOMIZER - Add New Field Data */
	jQuery('#userscontrol-btn-add-field-submit').on('click',function(e){
		e.preventDefault();		
		
		var _position = $("#userscontrol_position").val();		
		var _type =  $("#userscontrol_type").val();
		var _field = $("#userscontrol_field").val();		
		
		var _meta_custom = $("#userscontrol_meta_custom").val();		
		var _name = $("#userscontrol_name").val();
		var _tooltip =  $("#userscontrol_tooltip").val();	
		var _help_text =  $("#userscontrol_help_text").val();
		
		var _can_edit =  $("#userscontrol_can_edit").val();		
		var _allow_html =  $("#userscontrol_allow_html").val();

		var _social =  $("#userscontrol_social").val();
		var _is_a_link =  $("#userscontrol_is_a_link").val();
		
		
		var _can_edit =  $("#userscontrol_can_edit").val();		
		
		var _can_hide =  $("#userscontrol_can_hide").val();		
				
		var _private = $("#userscontrol_private").val();
		var _required =  $("#userscontrol_required").val();		
		var _show_in_register = $("#userscontrol_show_in_register").val();
		
		var _choices =  $("#userscontrol_choices").val();	
		var _predefined_options =  $("#userscontrol_predefined_options").val();		
		var custom_form =  $('#userscontrol__custom_registration_form').val();	

		
				
		var _icon =  $('input:radio[name=userscontrol_icon]:checked').val();

		//list of role to show		
		//special for roles		
		var _show_to_user_role =  $("#userscontrol_show_to_user_role").val();	
		var _edit_by_user_role =  $("#userscontrol_edit_by_user_role").val();	
		var _show_to_user_role_list = $('.userscontrol_show_roles_ids:checked').map(function() { 
			return this.value; 
		}).get().join(',');
		
				var _edit_by_user_role_list = $('.userscontrol_edit_roles_ids:checked').map(function() { 
			return this.value; 
		}).get().join(',');	
		
				
		jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "userscontrol_add_new_custom_profile_field", 
						"_position": _position , 
						"_type": _type ,
						"_field": _field ,
						"_meta_custom": _meta_custom ,
						"_name": _name  ,						
						"_tooltip": _tooltip ,						
						"_help_text": _help_text ,							
						"_can_edit": _can_edit ,
						"_social": _social ,
						"_is_a_link": _is_a_link ,
						"_can_edit": _can_edit ,
						"_can_hide": _can_hide  ,
						"_allow_html": _allow_html  ,
						"_private": _private, 
						"_required": _required  ,
						"_show_in_register": _show_in_register ,						
						"_choices": _choices,  
						"_predefined_options": _predefined_options , 
						"custom_form": custom_form,	
						"_show_to_user_role": _show_to_user_role,
						"_edit_by_user_role": _edit_by_user_role,
						"_show_to_user_role_list": _show_to_user_role_list,
						"_edit_by_user_role_list": _edit_by_user_role_list,
						"_ajax_nonce": userscontrol_admin_v98.nonce,

						"_icon": _icon },
						
						success: function(data){		
						
													
							jQuery("#userscontrol-sucess-add-field").slideDown();
							setTimeout("hidde_noti('userscontrol-sucess-add-field')", 3000)		
							//alert("done");
							window.location.reload();
							 							
							
							
							}
					});
			
		 
		
				
		return false;
	});
	
	/* 	FIELDS CUSTOMIZER - Update Field Data */
	jQuery(document).on("click", ".userscontrol-btn-submit-field", function(e) {
		
		e.preventDefault();		
		var key_id =  jQuery(this).attr("data-edition");
		
		jQuery('#p_name').val()		  
		
		var _position = $("#userscontrol_" + key_id + "_position").val();		
		var _type =  $("#userscontrol_" + key_id + "_type").val();
		var _field = $("#userscontrol_" + key_id + "_field").val();		
		var _meta =  $("#userscontrol_" + key_id + "_meta").val();
		var _meta_custom = $("#userscontrol_" + key_id + "_meta_custom").val();		
		var _name = $("#userscontrol_" + key_id + "_name").val();				
		var _tooltip =  $("#userscontrol_" + key_id + "_tooltip").val();	
		var _help_text =  $("#userscontrol_" + key_id + "_help_text").val();					
		var _can_edit =  $("#userscontrol_" + key_id + "_can_edit").val();
		var _private = $("#userscontrol_" + key_id + "_private").val();		
		var _required =  $("#userscontrol_" + key_id + "_required").val();		
		var _show_in_register = $("#userscontrol_" + key_id + "_show_in_register").val();				
		var _choices =  $("#userscontrol_" + key_id + "_choices").val();	
		var _predefined_options =  $("#userscontrol_" + key_id + "_predefined_options").val();		
		var _icon =  $('input:radio[name=userscontrol_' + key_id +'_icon]:checked').val();		
		var custom_form =  $('#userscontrol__custom_registration_form').val();

		var _social =  $("#userscontrol_" + key_id + "_social").val();
		var _is_a_link =  $("#userscontrol_" + key_id + "_is_a_link").val();
		var _allow_html =  $("#userscontrol_" + key_id + "_allow_html").val();
		var _can_hide =  $("#userscontrol_" + key_id + "_can_hide").val();	
		
		
		//special for roles		
		var _show_to_user_role =  $("#userscontrol_" + key_id + "_show_to_user_role").val();	
		var _edit_by_user_role =  $("#userscontrol_" + key_id + "_edit_by_user_role").val();	
		
		//list of role to show	 -- added on 11-02-2014	
		var _show_to_user_role_list = $('.userscontrol_' + key_id +'_show_roles_ids:checked').map(function() { 
			return this.value; }).get().join(',');
		
		var _edit_by_user_role_list = $('.userscontrol_' + key_id +'_edit_roles_ids:checked').map(function() { 
			return this.value;	}).get().join(',');	
					


		
		jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "userscontrol_save_fields_settings", 
						"_position": _position , "_type": _type ,
						"_field": _field ,
						"_meta": _meta ,
						"_meta_custom": _meta_custom  
						,"_name": _name  ,	
						
						"_social": _social ,
						"_is_a_link": _is_a_link ,
						"_icon": _icon ,
						"_can_edit": _can_edit ,"_allow_html": _allow_html  ,
						"_can_hide": _can_hide  ,"_private": _private, 
						
						"_tooltip": _tooltip ,
						"_help_text": _help_text ,												
						"_icon": _icon ,						
						"_required": _required  ,
						"_show_in_register": _show_in_register ,						
						"_choices": _choices, 
						"_predefined_options": _predefined_options,
						"pos": key_id  , 
						"custom_form": custom_form ,

						"_show_to_user_role": _show_to_user_role,
						"_edit_by_user_role": _edit_by_user_role,
						"_show_to_user_role_list": _show_to_user_role_list,
						"_edit_by_user_role_list": _edit_by_user_role_list,
						"_ajax_nonce": userscontrol_admin_v98.nonce
						
																	
					},
						
					success: function(data){	
						jQuery("#userscontrol-sucess-fields-"+key_id).slideDown();
						setTimeout("hidde_noti('userscontrol-sucess-fields-" + key_id +"')", 1000);
						userscontrol_reload_custom_fields_set();	
						
							
					}
		});
			
	});
	
	
	/* 	FIELDS CUSTOMIZER -  Edit Field Form */
		
	jQuery(document).on("click", ".userscontrol-btn-edit-field", function(e) {
		
		e.preventDefault();
		var block_id =  jQuery(this).attr("data-edition");			
		
		var custom_form = jQuery('#userscontrol__custom_registration_form').val();
		
		jQuery("#bup-spinner").show();
		
		jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "userscontrol_reload_field_to_edit", 
						"pos": block_id, "custom_form": custom_form, "_ajax_nonce": userscontrol_admin_v98.nonce},
						
						success: function(data){
							
							
							jQuery("#userscontrol-edit-fields-bock-"+block_id).html(data);							
							jQuery("#userscontrol-edit-fields-bock-"+block_id).slideDown();							
							jQuery("#bup-spinner").hide();								
							
							
							}
					});
		
					
		return false;
	});
    
	
	
	
		
	

	
	
	
	
	
	
	
	
	
		
	
	// on window resize run function
	$(window).resize(function () {
		//fluidDialog();
	});
	
	// catch dialog if opened within a viewport smaller than the dialog width
	$(document).on("dialogopen", ".ui-dialog", function (event, ui) {
		//fluidDialog();
	});
	
	function fluidDialog()
	 {
		var $visible = $(".ui-dialog:visible");
		// each open dialog
		$visible.each(function () 
		{
			var $this = $(this);
			
			var dialog = $this.find(".ui-dialog-content").data("dialog");
			
			// if fluid option == true
			if (dialog.options.fluid) {
				var wWidth = $(window).width();
				// check window width against dialog width
				if (wWidth < dialog.options.maxWidth + 50) {
					// keep dialog from filling entire screen
					$this.css("max-width", "90%");
				} else {
					// fix maxWidth bug
					$this.css("max-width", dialog.options.maxWidth);
				}
				//reposition dialog
				dialog.option("position", dialog.options.position);
			}
		});
	
	}


	
	
	/* open priority form */	
	jQuery( "#userscontrol-priority-add-priority-box" ).dialog({
			autoOpen: false,																							
			width: 500,
			modal: true,
			buttons: {
			"Save": function() {
				
				var priority_title=   jQuery("#userscontrol-title").val();
				var priority_id=   jQuery("#userscontrol-priority-id").val(); 
				var priority_color =  jQuery("#userscontrol-priority-color" ).val();
				var reply_within=   jQuery("#userscontrol-reply-within").val();
				var resolve_within=   jQuery("#userscontrol-resolve-within").val();
				var visibility=   jQuery("#userscontrol-priority-private").val();
				
				
				if(priority_title==''){alert(userscontrol_admin_v98.msg_priority_input_title); return;}
				
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "userscontrol_add_priority_confirm",
					"priority_title":priority_title,
					"priority_color":priority_color,
					"priority_id": priority_id,
					"reply_within": reply_within,
					"resolve_within": resolve_within,
					"visibility": visibility},
					
					success: function(data){		
								
						jQuery("#bup-spinner").hide();						
						jQuery("#userscontrol-priority-add-priority-box" ).dialog( "close" );						
						userscontrol_load_priorities();										
						
						}
				});				
				
				
			
			},
			
			"Cancel": function() {
				
				
				jQuery( this ).dialog( "close" );
			},
			
			
			},
			close: function() {
			
			
			}
	});
	
	/* open delete form */	
	jQuery( "#userscontrol-priority-delete-box" ).dialog({
			autoOpen: false,																							
			width: 500,
			modal: true,
			buttons: {
						
			"Cancel": function() {
				
				
				jQuery( this ).dialog( "close" );
			},
			
			
			},
			close: function() {
			
			
			}
	});
	
	/* open delete form */	
	jQuery( "#userscontrol-department-delete-box" ).dialog({
			autoOpen: false,																							
			width: 500,
			modal: true,
			buttons: {
						
			"Cancel": function() {
				
				
				jQuery( this ).dialog( "close" );
			},
			
			
			},
			close: function() {
			
			
			}
	});
	
	/* open delete form */	
	jQuery( "#userscontrol-delete-product-box" ).dialog({
			autoOpen: false,																							
			width: 500,
			modal: true,
			buttons: {
						
			"Cancel": function() {
				
				
				jQuery( this ).dialog( "close" );
			},
			
			
			},
			close: function() {
			
			
			}
	});
	
	/* open department form */	
	jQuery( "#userscontrol-department-add-department-box" ).dialog({
			autoOpen: false,																							
			width: 500,
			modal: true,
			buttons: {
			"Save": function() {
				
				var department_title=   jQuery("#userscontrol-title").val();
				var department_id=   jQuery("#userscontrol_department_id").val();
				var department_site_id=   jQuery("#userscontrol-sites").val();
				var department_color =  jQuery("#userscontrol-category-color" ).val();
				
				if(department_title==''){alert(userscontrol_admin_v98.msg_department_input_title); return;}
				
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "userscontrol_add_department_confirm",
					"department_title":department_title,
					"department_color":department_color,
					"department_id": department_id,
					"department_site_id": department_site_id},
					
					success: function(data){		
								
						jQuery("#bup-spinner").hide();						
						jQuery("#userscontrol-department-add-department-box" ).dialog( "close" );						
						userscontrol_load_departments();
						
						}
				});				
				
				
			
			},
			
			"Cancel": function() {
				
				
				jQuery( this ).dialog( "close" );
			},
			
			
			},
			close: function() {
			
			
			}
	});
	
	
	/* open product form */	
	jQuery( "#userscontrol-edit-product-box" ).dialog({
			autoOpen: false,																							
			width: 500,
			modal: true,
			buttons: {
			"Save": function() {
				
				var product_title=   jQuery("#userscontrol-site-name").val();
				var product_id=   jQuery("#userscontrol_product_id").val();
								
				if(product_title==''){alert(userscontrol_admin_v98.msg_input_site_name); return;}
				
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "userscontrol_add_site_confirm",
					"product_name":product_title,
					"product_id":product_id},
					
					success: function(data){		
								
						jQuery("#bup-spinner").hide();						
						jQuery("#userscontrol-edit-product-box" ).dialog( "close" );						
						userscontrol_load_sites();
						
						}
				});				
				
				
			
			},
			
			"Cancel": function() {
				
				
				jQuery( this ).dialog( "close" );
			},
			
			
			},
			close: function() {
			
			
			}
	});
	
	/* edit department form */	
	jQuery( "#userscontrol-department-edit-department-box" ).dialog({
			autoOpen: false,																							
			width: 500,
			modal: true,
			buttons: {
			"Save": function() {
				
				var department_title=   jQuery("#userscontrol-title").val();
				var department_id=   jQuery("#userscontrol-department-id").val(); 
				var department_site_id=   jQuery("#userscontrol-sites").val();
				var department_color =  jQuery("#userscontrol-category-color" ).val();
				
								
				if(department_title==''){alert(userscontrol_admin_v98.msg_department_input_title); return;}
				
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "userscontrol_add_department_confirm",
					"department_title":department_title,
					"department_color":department_color,
					"department_id": department_id,
					"department_site_id": department_site_id},
					
					success: function(data){		
								
						jQuery("#bup-spinner").hide();						
						jQuery("#userscontrol-department-edit-department-box" ).dialog( "close" );						
						userscontrol_load_departments();
						
						
												
						
						}
				});				
				
				
			
			},
			
			"Cancel": function() {
				
				
				jQuery( this ).dialog( "close" );
			},
			
			
			},
			close: function() {
			
			
			}
	});
	
	/* open category form */	
	jQuery( "#userscontrol-site-add-department-box" ).dialog({
			autoOpen: false,																							
			width: 300,
			modal: true,
			buttons: {
			"Save": function() {
				
				var product_name=   jQuery("#userscontrol-site-name").val();
				var product_id=   jQuery("#userscontrol_site_id").val();
				
				if(product_name==''){alert(userscontrol_admin_v98.msg_input_site_name); return;}
				
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "userscontrol_add_site_confirm",
					"product_name":product_name,
					"product_id": product_id},
					
					success: function(data){		
								
						jQuery("#bup-spinner").hide();						
						jQuery("#userscontrol-site-add-department-box" ).dialog( "close" );						
						userscontrol_load_sites();
						
						
												
						
						}
				});				
				
				
			
			},
			
			"Cancel": function() {
				
				
				jQuery( this ).dialog( "close" );
			},
			
			
			},
			close: function() {
			
			
			}
	});
	
	

		
	//this adds the user and loads the user's details	
	jQuery(document).on("click", "#ubp-save-glogal-business-hours", function(e) {
			
			e.preventDefault();			
			
			var bup_mon_from=   jQuery("#bup-mon-from").val();
			var bup_mon_to=   jQuery("#bup-mon-to").val();			
			var bup_tue_from=   jQuery("#bup-tue-from").val();
			var bup_tue_to=   jQuery("#bup-tue-to").val();			
			var bup_wed_from=   jQuery("#bup-wed-from").val();
			var bup_wed_to=   jQuery("#bup-wed-to").val();			
			var bup_thu_from=   jQuery("#bup-thu-from").val();
			var bup_thu_to=   jQuery("#bup-thu-to").val();			
			var bup_fri_from=   jQuery("#bup-fri-from").val();
			var bup_fri_to=   jQuery("#bup-fri-to").val();			
			var bup_sat_from=   jQuery("#bup-sat-from").val();
			var bup_sat_to=   jQuery("#bup-sat-to").val();			
			var bup_sun_from=   jQuery("#bup-sun-from").val();
			var bup_sun_to=   jQuery("#bup-sun-to").val();
			
			
			
				
			jQuery("#bup-err-message" ).html( '' );	
			jQuery("#bup-loading-animation-business-hours" ).show( );		
			
			
						
    		jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "ubp_update_global_business_hours", 
					"bup_mon_from": bup_mon_from, "bup_mon_to": bup_mon_to ,
					"bup_tue_from": bup_tue_from, "bup_tue_to": bup_tue_to ,
					"bup_wed_from": bup_wed_from, "bup_wed_to": bup_wed_to ,
					"bup_thu_from": bup_thu_from, "bup_thu_to": bup_thu_to ,
					"bup_fri_from": bup_fri_from, "bup_fri_to": bup_fri_to ,
					"bup_sat_from": bup_sat_from, "bup_sat_to": bup_sat_to ,
					"bup_sun_from": bup_sun_from, "bup_sun_to": bup_sun_to ,
					 
					 },
					
					success: function(data){
						
						
						var res = data;		
						
						jQuery("#bup-err-message" ).html( res );						
						jQuery("#bup-loading-animation-business-hours" ).hide( );		
						
						
						
						
						}
				});
			
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();
			 
				
        });
		
		//this adds the user and loads the user's details	
	jQuery(document).on("click", ".userscontrol_restore_template", function(e) {
			
			
			var template_id =  jQuery(this).attr("b-template-id");
			jQuery("#userscontrol_email_template").val(template_id);
			jQuery("#userscontrol_reset_email_template").val('yes');
			
			jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "userscontrol_reset_email_template", 
					"email_template": template_id		,
					"_ajax_nonce": userscontrol_admin_v98.nonce			
					
					 
					 },
					
					success: function(data){
						
						
						var res = data;								
						//$("#b_frm_settings").submit();				
						
						
						}
				});
			
			
			 
				
        });
		
		

	
	/* 	Delete department */
	jQuery(document).on("click", ".userscontrol-department-delete", function(e) {
		
		
			jQuery("#bup-spinner").show();
			  
			var department_id =  jQuery(this).attr("department-id");	
			 
			jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "userscontrol_delete_department_form", 
						"department_id": department_id  },
						
						success: function(data){
							
							jQuery("#userscontrol-department-delete-box" ).html( data);	 
							jQuery("#userscontrol-department-delete-box" ).dialog( "open" );	
							jQuery("#bup-spinner").hide();						
							
							
						}
					});
			
		  	
	});
	
	/* 	Delete product */
	jQuery(document).on("click", ".userscontrol-product-delete", function(e) {
		
		
			jQuery("#bup-spinner").show();
			  
			var product_id =  jQuery(this).attr("product-id");	
			 
			jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "userscontrol_delete_product_form", 
						"product_id": product_id  },
						
						success: function(data){
							
							jQuery("#userscontrol-delete-product-box" ).html( data);	 
							jQuery("#userscontrol-delete-product-box" ).dialog( "open" );	
							jQuery("#bup-spinner").hide();						
							
							
						}
					});
			
		  	
	});
	
	/* 	Delete priority */
	jQuery(document).on("click", ".userscontrol-priority-delete", function(e) {
		
		
			jQuery("#bup-spinner").show();
			  
			var priority_id =  jQuery(this).attr("priority-id");	
			 
			jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "userscontrol_delete_priority_form", 
						"priority_id": priority_id  },
						
						success: function(data){
							
							jQuery("#userscontrol-priority-delete-box" ).html( data);	 
							jQuery("#userscontrol-priority-delete-box" ).dialog( "open" );	
							jQuery("#bup-spinner").hide();						
							
							
						}
					});
			
		  	
	});
	
	
	/* 	delete ticket reply */
	jQuery(document).on("click", ".userscontrol-del-reply", function(e) {
		
		
			e.preventDefault();		
		
			jQuery("#bup-spinner").show();
			  
			var reply_id =  jQuery(this).attr("reply-id");
			
			jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "userscontrol_delete_ticket_reply_confirm", 
						"reply_id": reply_id  },
						
						success: function(data){							
						
							jQuery("#bup-spinner").hide();								 
							jQuery("#userscontrol-reply-unique-id-box-"+reply_id).hide();
							
						}
					});
			
		  	
	});
	
	
	
	/* 	Conf delete product */
	jQuery(document).on("click", "#userscontrol-product-del-conf-btn", function(e) {
		
			e.preventDefault();
		
		
			jQuery("#bup-spinner").show();
			  
			var product_id =  jQuery(this).attr("product-id");
			
			jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "userscontrol_delete_product_confirm", 
						"product_id": product_id  },
						
						success: function(data){							
								 
							jQuery("#userscontrol-delete-product-box" ).dialog( "close" );	
							userscontrol_load_sites();
							
						}
					});
			
		  	
	});
	
	
	/* 	Conf delete department */
	jQuery(document).on("click", "#userscontrol-department-del-conf-btn", function(e) {
		
		
			jQuery("#bup-spinner").show();
			  
			var department_id =  jQuery(this).attr("department-id");
			var department_assign =  jQuery(this).attr("department-assign");
			var new_department_id =  jQuery('#ticket_department').val();
			
			if(new_department_id=='' && department_assign==1)	{alert(userscontrol_admin_v98.set_new_priority);return;}
			//alert(new_priority_id);
			//return
			 
			jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "userscontrol_delete_department_confirm", 
						"department_id": department_id, "new_department_id": new_department_id  },
						
						success: function(data){							
								 
							jQuery("#userscontrol-department-delete-box" ).dialog( "close" );	
							userscontrol_load_departments();
							
						}
					});
			
		  	
	});
	
	/* 	Conf delete priority */
	jQuery(document).on("click", "#userscontrol-priority-del-conf-btn", function(e) {
		
		
			jQuery("#bup-spinner").show();
			  
			var priority_id =  jQuery(this).attr("priority-id");
			var priority_assign=  jQuery(this).attr("priority-assign");
			var new_priority_id =  jQuery('#ticket_priority').val();
			
			if(new_priority_id=='' && priority_assign=='1')	{alert(userscontrol_admin_v98.set_new_priority);return;}
			//alert(new_priority_id);
			//return
			 
			jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "userscontrol_delete_priority_confirm", 
						"priority_id": priority_id, "new_priority_id": new_priority_id  },
						
						success: function(data){							
								 
							jQuery("#userscontrol-priority-delete-box" ).dialog( "close" );	
							userscontrol_load_priorities();
							
						}
					});
			
		  	
	});
	
	
	/* 	Trash Ticket */
	jQuery(document).on("click", ".userscontrol-trash-ticket", function(e) {

		e.preventDefault();
		
		var doIt = false;
		
		doIt=confirm(userscontrol_admin_v98.msg_trash_ticket);
		  
		  if(doIt)
		  {
			  
			  var ticket_id =  jQuery(this).attr("ticket-id");	
			 
			  jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "userscontrol_trash_ticket", 
						"ticket_id": ticket_id  },
						
						success: function(data){
							
							///userscontrol_load_departments();	
							
								
							
							jQuery("#userscontrol_ticket_row_id_"+ticket_id).hide();				
							
							
						}
					});
			
		  }
		  else{
			
		  }		
		
	});
	
	
	/* 	Delete department */
	jQuery(document).on("click", ".userscontrol-department-delete-conf", function(e) {

		e.preventDefault();
		
		var doIt = false;
		
		doIt=confirm(userscontrol_admin_v98.msg_department_delete);
		  
		  if(doIt)
		  {
			  
			  var department_id =  jQuery(this).attr("department-id");	
			 
			  jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "userscontrol_delete_department", 
						"department_id": department_id  },
						
						success: function(data){
							
							userscontrol_load_departments();							
							
							
						}
					});
			
		  }
		  else{
			
		  }		
		
	});
		
	
	/* 	Delete category */
	jQuery(document).on("click", ".userscontrol-category-delete", function(e) {

		e.preventDefault();
		
		var doIt = false;
		
		doIt=confirm(userscontrol_admin_v98.msg_cate_delete);
		  
		  if(doIt)
		  {
			  
			  var cate_id =  jQuery(this).attr("category-id");	
			 
			  jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "bup_delete_category", 
						"cate_id": cate_id  },
						
						success: function(data){
							
							userscontrol_load_departments();							
							
							
						}
					});
			
		  }
		  else{
			
		  }		
		
	});
		
	function isInteger(x) {
        return x % 1 === 0;
    }
	
	
	jQuery(document).on("click", "#userscontrol-add-staff-btn", function(e) {
			
			e.preventDefault();	
			
			jQuery("#bup-spinner").show();		
						
    		jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "userscontrol_get_new_staff" },
					
					success: function(data){								
					
						jQuery("#userscontrol-staff-editor-box" ).html( data );							
						jQuery("#userscontrol-staff-editor-box" ).dialog( "open" );
						jQuery("#bup-spinner").hide();
							
						
						
						}
				});
			
			
			 // Cancel the default action
    		e.preventDefault();
			 
				
        });
		
	

	
	
	
	

	
	jQuery(document).on("click", ".userscontrol-admin-edit-department", function(e) {
			
			e.preventDefault();
			
			var department_id =  jQuery(this).attr("department-id");
						
		
			jQuery("#bup-spinner").show();
				
    		jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "userscontrol_get_department_add_form",  "department_id": department_id },
					
					success: function(data){					
					
						jQuery("#userscontrol-department-edit-department-box" ).html( data );							
						jQuery("#userscontrol-department-edit-department-box" ).dialog( "open" );
						jQuery('.color-picker').wpColorPicker();					
						jQuery("#bup-spinner").hide();	
						
						
						}
				});
			
			
			 // Cancel the default action
    		e.preventDefault();
			 
				
        });
		
		jQuery(document).on("click", ".userscontrol-staff-load", function(e) {
			
			e.preventDefault();
			
			var staff_id =  jQuery(this).attr("staff-id");			
			userscontrol_load_staff_member(staff_id);	
				
    		
    		e.preventDefault();
			 
				
        });
		
		
	
	
		
		
		
		function ubp_get_checked_services ()	
		{
			
			var checkbox_value = "";
			jQuery(".ubp-cate-service-checked").each(function () {
				
				var ischecked = $(this).is(":checked");
			   
				if (ischecked) 
				{
					//get price and quantity
					var bup_price = jQuery("#bup-price-"+$(this).val()).val();
					var bup_qty = jQuery("#bup-qty-"+$(this).val()).val();
					checkbox_value += $(this).val() + "-" + bup_price + "-" + bup_qty + "|";
				}
				
				
			});
			
			return checkbox_value;		
		}
		
		
		
		function ubp_get_checked_locations ()	
		{
			
			var checkbox_value = "";
			jQuery(".ubp-location-checked").each(function () {
				
				var ischecked = $(this).is(":checked");
			   
				if (ischecked) 
				{
					
					checkbox_value += $(this).val()+ "|";
				}
				
				
			});
			
			return checkbox_value;		
		}
		
		
		
		/* 	FIELDS CUSTOMIZER -  restore default */
	jQuery('#bup-restore-fields-btn').on('click',function(e)
	{
		
		e.preventDefault();
		
		doIt=confirm(custom_fields_reset_confirmation);
		  
		  if(doIt)
		  {
			
			var userscontrol_custom_form = jQuery('#userscontrol__custom_registration_form').val();
			  
				jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "custom_fields_reset", 
						"p_confirm": "yes"  , 		"bup_custom_form": userscontrol_custom_form },
						
						success: function(data){
							
							jQuery("#fields-mg-reset-conf").slideDown();			
						
							 window.location.reload();						
							
							
							}
					});
			
		  }
			
					
		return false;
	});
	
	
	
	
	/* 	WIDGETS CUSTOMIZER -  Close Open Widget */
	jQuery('.userscontrol-widgets-icon-close-open, .userscontrol-staff-details-header').on('click',function(e)
	{
		
		e.preventDefault();
		var widget_id =  jQuery(this).attr("widget-id");		
		var iconheight = 20;
		
		
		if(jQuery("#userscontrol-widget-adm-cont-id-"+widget_id).is(":visible")) 
	  	{
			
			jQuery("#userscontrol-widgets-icon-close-open-id-"+widget_id).css('background-position', '0px 0px');
			
			
			
		}else{
			
			jQuery("#userscontrol-widgets-icon-close-open-id-"+widget_id).css('background-position', '0px -'+iconheight+'px');			
	 	 }
		
		
		jQuery("#userscontrol-widget-adm-cont-id-"+widget_id).slideToggle();	
					
		return false;
	});
	
	/* 	FIELDS CUSTOMIZER -  ClosedEdit Field Form */
	jQuery('.userscontrol-btn-close-edition-field').on('click',function(e)
	{
		
		e.preventDefault();
		var block_id =  jQuery(this).attr("data-edition");		
		jQuery("#uu-edit-fields-bock-"+block_id).slideUp();				
		return false;
	});
	
	
	jQuery(document).on("click", "#userscontrol-btn-app-confirm", function(e) {
			
			e.preventDefault();				
			var frm_validation  = $("#userscontrol-registration-form").validationEngine('validate');	
			
			//check if user is a staff member trying to purchase an own service
			
			if(frm_validation)
			{
							
				
					//alert('other then submit');
					
					$("#userscontrol-registration-form").submit();
				
				
				
				
			}else{
				
				
				
			}
			
			
									
    		e.preventDefault();		 
				
        });
	
			
	

	
});






function userscontrol_load_sites ()	
	{
		jQuery("#bup-spinner").show();
		  jQuery.post(ajaxurl, {
							action: 'userscontrol_display_sites'
									
							}, function (response){									
																
							jQuery("#userscontrol-sites-list").html(response);							
							jQuery("#bup-spinner").hide();
							
		 });
}

function userscontrol_load_priorities ()	
	{
		jQuery("#bup-spinner").show();
		  jQuery.post(ajaxurl, {
							action: 'userscontrol_display_priorities'
									
							}, function (response){									
																
							jQuery("#userscontrol-priorities-list").html(response);							
							jQuery("#bup-spinner").hide();
							
		 });
}


function userscontrol_load_departments ()	
	{
		jQuery("#bup-spinner").show();
		  jQuery.post(ajaxurl, {
							action: 'userscontrol_display_departments'
									
							}, function (response){									
																
							jQuery("#userscontrol-departments-list").html(response);							
							jQuery("#bup-spinner").hide();
							
		 });
}




function userscontrol_load_staff_member (staff_id)	
	{
		jQuery("#bup-spinner").show();
		  jQuery.post(ajaxurl, {
							action: 'userscontrol_get_staff_details_ajax', 'staff_id': staff_id
									
							}, function (response){									
																
							jQuery("#userscontrol-staff-details" ).html( response );	
														
							jQuery("#bup-spinner").hide();
							
		 });
}




function get_disabled_modules_list ()	
{
	
	var checkbox_value = "";
    jQuery(".userscontrol-my-modules-checked").each(function () {
		
        var ischecked = $(this).is(":checked");
       
	    if (ischecked) 
		{
            checkbox_value += $(this).val() + "|";
        }
		
		
    });
	
	return checkbox_value;		
}

function sortable_user_menu(){
	 var itemList = jQuery('#userscontrol-user-menu-option-list');
	 
	 itemList.sortable({
		  cursor: 'move',
          update: function(event, ui) {
           // $('#loading-animation').show(); // Show the animate loading gif while waiting

            opts = {
                url: ajaxurl, // ajaxurl is defined by WordPress and points to /wp-admin/admin-ajax.php
                type: 'POST',
                async: true,
                cache: false,
                dataType: 'json',
                data:{
                    action: 'userscontrol_sort_user_menu_ajax', // Tell WordPress how to handle this ajax request
                    order: itemList.sortable('toArray').toString() // Passes ID's of list items in  1,3,2 format
                },
                success: function(response) {
                   // $('#loading-animation').hide(); // Hide the loading animation
				   userscontrol_reload_user_menu_customizer();
				  				   
                    return; 
                },
                error: function(xhr,textStatus,e) {  // This can be expanded to provide more information
                    alert(e);
                    // alert('There was an error saving the updates');
                  //  $('#loading-animation').hide(); // Hide the loading animation
                    return; 
                }
            };
            jQuery.ajax(opts);
        }
    }); 
	
}

function userscontrol_reload_custom_fields_set (){
	
	jQuery("#bup-spinner").show();	
	var custom_form =  jQuery('#userscontrol__custom_registration_form').val();
		
	jQuery.post(ajaxurl, {
							action: 'userscontrol_reload_custom_fields_set', 
							'custom_form': custom_form,
							"_ajax_nonce": userscontrol_admin_v98.nonce
									
							}, function (response){									
																
							jQuery("#uu-fields-sortable").html(response);							
							sortable_fields_list();
							
							jQuery("#bup-spinner").hide();
							
																
														
	});
		
}

function userscontrol_reload_custom_fields_set (){
	
	jQuery("#getbwp-spinner").show();
	
	 var userscontrol_custom_form =  jQuery('#userscontrol__custom_registration_form').val();
		
	jQuery.post(ajaxurl, {
			
		action: 'userscontrol_reload_custom_fields_set', 
		'userscontrol_custom_form': userscontrol_custom_form,
		"_ajax_nonce": userscontrol_admin_v98.nonce
									
	}, function (response){									
																
			jQuery("#uu-fields-sortable").html(response);							
			sortable_fields_list();
							
			jQuery("#getbwp-spinner").hide();			
														
	 });
		
}

function sortable_fields_list ()
{
	var itemList = jQuery('#uu-fields-sortable');	 
	var userscontrol_custom_form =  jQuery('#userscontrol__custom_registration_form').val();
   
    itemList.sortable({
		cursor: 'move',
        update: function(event, ui) {
        jQuery("#userscontrol-spinner").show(); // Show the animate loading gif while waiting

            opts = {
                url: ajaxurl, // ajaxurl is defined by WordPress and points to /wp-admin/admin-ajax.php
                type: 'POST',
                async: true,
                cache: false,
                dataType: 'json',
                data:{
                    action: 'userscontrol_sort_fileds_list', // Tell WordPress how to handle this ajax request
					'userscontrol_custom_form': userscontrol_custom_form, 
					"_ajax_nonce": userscontrol_admin_v98.nonce,
                    order: itemList.sortable('toArray').toString() // Passes ID's of list items in  1,3,2 format
                },
                success: function(response) {
                   // $('#loading-animation').hide(); // Hide the loading animation
				   userscontrol_reload_custom_fields_set();
                    return; 
                },
                error: function(xhr,textStatus,e) {  // This can be expanded to provide more information
                  //  alert(e);
                    // alert('There was an error saving the updates');
                  //  $('#loading-animation').hide(); // Hide the loading animation
                    return; 
                }
            };
            jQuery.ajax(opts);
        }
    }); 
	
	
}






function userscontrol_load_staff_adm(staff_id )	
{

	setTimeout("userscontrol_load_staff_list_adm()", 1000);
	setTimeout("userscontrol_load_staff_details(" + staff_id +")", 1000);
	
}

function userscontrol_load_staff_list_adm()	{
	jQuery("#bup-spinner").show();
	
    jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "userscontrol_get_staff_list_admin_ajax"},
					
					success: function(data){					
						
						var res = data;						
						jQuery("#userscontrol-staff-list").html(res);
						jQuery("#bup-spinner").hide();					    
						
												

						}
				});	
	
}

function userscontrol_load_staff_details(staff_id)	
{
	jQuery("#bup-spinner").show();	
    jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "userscontrol_get_staff_details_admin", "staff_id": staff_id},
					
					success: function(data){					
						
						var res = data;						
						jQuery("#userscontrol-staff-details").html(res);					
						jQuery( "#tabs-bupro" ).tabs({collapsible: false	});						
						jQuery("#bup-spinner").hide();	
										    
						

						}
				});	
	
}

function userscontrol_load_private_credentials (ticket_id)	
{
	
	jQuery.post(ajaxurl, {
							action: 'userscontrol_load_private_credentials','ticket_id': ticket_id
							
									
							}, function (response){									
																
							jQuery("#userscontrol-private-credentials-list").html(response);
							
							//jQuery("#bup-spinner").hide();
							
		 });
}

function userscontrol_load_private_notes (ticket_id)	
{
	
	jQuery("#bup-spinner").show();
	
	jQuery.post(ajaxurl, {
							action: 'userscontrol_load_private_notes','ticket_id': ticket_id
							
									
							}, function (response){									
																
							jQuery("#userscontrol-private-notes-list").html(response);
							
							jQuery("#bup-spinner").hide();
							
		 });
}




function userscontrol_set_auto_c()
{
	  $("#userscontrolclientsel").autocomplete({
		  
	  
	  source: function( request, response ) {
			  $.ajax({
				  url: ajaxurl,
				  dataType: "json",
				  data: {
					  action: 'userscontrol_autocomple_clients_tesearch',
					  term: this.term
				  },
				  
				  success: function( data ) {
					  
					  response( $.map( data.results, function( item ) {
					  return {
						  id: item.id,
						  label: item.label,
						  value: item.label
					  }
					   }));
					   
					   
					  
				  },
				  
				  error: function(jqXHR, textStatus, errorThrown) 
				  {
					  console.log(jqXHR, textStatus, errorThrown);
				  }
				  
			  });
		  },
	  
		  minLength: 2,			
		  
		  // optional (if other layers overlap autocomplete list)
		  open: function(event, ui) {
			  
			  var dialog = $(this).closest('.ui-dialog');
			  if(dialog.length > 0){
				  $('.ui-autocomplete.ui-front').zIndex(dialog.zIndex()+1);
			  }
		  },
		  
		  select: function( event, ui ) {
			  
			  ui.item.ur;			  
			  jQuery( "#userscontrol_client_id" ).val(ui.item.id);
				  
		  }
	  
	  });
  
}

function userscontrol_set_auto_staff()
{
	  $("#userscontrolstaffsel").autocomplete({
		  
	  
	  source: function( request, response ) {
			  $.ajax({
				  url: ajaxurl,
				  dataType: "json",
				  data: {
					  action: 'userscontrol_autocomple_clients_tesearch',
					  'type': 'staff',
					  term: this.term
				  },
				  
				  success: function( data ) {
					  
					  response( $.map( data.results, function( item ) {
					  return {
						  id: item.id,
						  label: item.label,
						  value: item.label
					  }
					   }));
					   
					   
					  
				  },
				  
				  error: function(jqXHR, textStatus, errorThrown) 
				  {
					  console.log(jqXHR, textStatus, errorThrown);
				  }
				  
			  });
		  },
	  
		  minLength: 2,			
		  
		  // optional (if other layers overlap autocomplete list)
		  open: function(event, ui) {
			  
			  var dialog = $(this).closest('.ui-dialog');
			  if(dialog.length > 0){
				  $('.ui-autocomplete.ui-front').zIndex(dialog.zIndex()+1);
			  }
		  },
		  
		  select: function( event, ui ) {
			  
			  ui.item.ur;			  
			  jQuery( "#userscontrol_staff_id" ).val(ui.item.id);
				  
		  }
	  
	  });
  
}


function hidde_noti (div_d)
{
		jQuery("#"+div_d).slideUp();		
		
}
