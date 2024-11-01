jQuery(document).ready(function($) {
	
	jQuery("body").on("click", ".userscontrol-form-edit", function(e) {
		e.preventDefault();		
		 
		 var form_id =  jQuery(this).attr("data-form");	
		
		jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "userscontrol_edit_form", 
						"form_id": form_id },
						
						success: function(data){					
						
							 $("#uu-edit-form-box-"+form_id).html(data);
							 jQuery("#uu-edit-form-box-"+form_id).slideDown();							
							
							
							}
					});
		return false;
	});
	
	jQuery("body").on("click", ".userscontrol-form-modify", function(e) {
		e.preventDefault();		
		 
		 var form_id=  jQuery(this).attr("form-id");		
		 var form_name = $('#userscontrol_form_name_edit_'+form_id).val();
		
		 
				
		jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "userscontrol_edit_form_conf", "form_id": form_id, 
						"form_name": form_name },
						
						success: function(data){									
							//refresh
							window.location.reload();								
													
							
							
							}
					});
		return false;
	});
	
	jQuery("body").on("click", ".userscontrol-form-del", function(e) {
		e.preventDefault();		
		 
		 var form_id=  jQuery(this).attr("data-form");		
		 
		 
		 doIt=confirm(custom__del_confirmation);
		  
		  if(doIt)
		  {
			
				jQuery.ajax({
								type: 'POST',
								url: ajaxurl,
								data: {"action": "userscontrol_edit_form_del", "form_id": form_id 
								 },
								
								success: function(data){
									
									jQuery("#uu-edit-form-row-"+form_id).slideUp();						
								
															
									
									
									}
							});
			
			}
		return false;
	});
	
	jQuery("body").on("click", ".userscontrol-form-close", function(e) {
		e.preventDefault();		
		 
		 var cate_id =  jQuery(this).attr("data-form");			
		jQuery("#uu-edit-form-box-"+cate_id).slideUp();
		return false;
	});
	
});