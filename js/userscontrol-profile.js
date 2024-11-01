jQuery(document).ready(function($) {
	
		/* 	Close Open Sections in Main Admin Page */		
	jQuery(document).on("click", ".userscontrol-widget-backend-colapsable", function(e) {	
	
		
		var widget_id =  jQuery(this).attr("widget-id");		
		var iconheight = 20;
		
		
		if(jQuery("#userscontrol-backend-landing-"+widget_id).is(":visible")) 
	  	{					
			
			jQuery( "#userscontrol-close-open-icon-"+widget_id ).removeClass( "fa-sort-asc" ).addClass( "fa-sort-desc" );
			
		}else{			
			
			jQuery( "#userscontrol-close-open-icon-"+widget_id ).removeClass( "fa-sort-desc" ).addClass( "fa-sort-asc" );			
	 	 }
		
		
		jQuery("#userscontrol-backend-landing-"+widget_id).slideToggle();	
					
	});
		
	/* 	Close Open Sections in Dasbhoard */		
	jQuery(document).on("click", ".userscontrol-widget-home-colapsable", function(e) {	
	
		
		e.preventDefault();
		var widget_id =  jQuery(this).attr("widget-id");		
		var iconheight = 20;
		
		if(jQuery("#userscontrol-staff-box-cont-"+widget_id).is(":visible")) 
	  	{
					
			jQuery( "#userscontrol-close-open-icon-"+widget_id ).removeClass( "fa-sort-desc" ).addClass( "fa-sort-asc" );
			
		}else{
			
			jQuery( "#userscontrol-close-open-icon-"+widget_id ).removeClass( "fa-sort-asc" ).addClass( "fa-sort-desc" );			
	 	 }
		
		
		jQuery("#userscontrol-staff-box-cont-"+widget_id).slideToggle();	
					
		return false;
	});
	
		jQuery(document).on("click", "#userscontrol-btn-book-app-confirm-resetlink", function(e) {		
			
				var p1= $("#user_login_reset").val()	;
				var p2= $("#user_password_reset_2").val()	;
				var u_key= $("#userscontrol_reset_key").val()	;
				
				jQuery("#userscontrol-pass-reset-message").html(userscontrol_profile_v98.msg_wait);
				
									
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "userscontrol_confirm_reset_password", "p1": p1, "p2": p2, "key": u_key },
					
					success: function(data){						
					
						jQuery("#userscontrol-pass-reset-message").html(data);											
						
						}
				});
			
			
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
				alert(bup_profile_v98.msg_make_selection);
				return false;
			}
			
			
			jQuery('#userscontrol-cropping-avatar-wait-message').html(userscontrol_profile_v98.msg_wait_cropping);
			
			
			
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "userscontrol_crop_avatar_user_profile_image_staff", "x1": x1 , "y1": y1 , "w": w , "h": h  , "image_id": image_id , "user_id": user_id},
				
				success: function(data){					
					//redirect				
					var site_redir = jQuery('#site_redir').val();
					window.location.replace(site_redir);	
								
					
					
					}
			});
			
					
					
		     	
    		e.preventDefault();
			 

				
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
			
		//jQuery('#userscontrol-cropping-avatar-wait-message').html(message_wait_availability);
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

	jQuery(document).on("click", "#btn-delete-user-avatar", function(e) {
			
			e.preventDefault();
			
			var user_id =  jQuery(this).attr("user-id");
			var redirect_avatar =  jQuery(this).attr("redirect-avatar");
			
    		jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "userscontrol_delete_user_avatar_staff" },
					
					success: function(data){
												
						refresh_my_avatar();
						
						if(redirect_avatar=='yes')
						{
							var site_redir = jQuery('#site_redir').val();
							window.location.replace(site_redir);
							
						}else{
							
							refresh_my_avatar();
							
						}
											
						
					}
				});
			
			
    		e.preventDefault();
			 
				
        });
		
	
	function refresh_my_avatar ()
		{
			
			 jQuery.post(ajaxurl, {
							action: 'refresh_avatar'}, function (response){									
																
							jQuery("#uu-backend-avatar-section").html(response);
									
									
					
			});
			
		}
		

}); 
	