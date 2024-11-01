if(typeof $ == 'undefined'){
	var $ = jQuery;
}
var ajaxurl = USERSCONTROLFRONTV.ajaxUrl;

(function($) {
    jQuery(document).ready(function () { 
	
	   "use strict";	   
	   
	   $("#userscontrol-client-registration-form").validationEngine({promptPosition: 'inline'});   
	   

	   
	   jQuery(document).on("click", "#userscontrol-btn-conf-upgrade", function(e) {
			
			var frm_validation  = $("#userscontrol-client-registration-form").validationEngine('validate');	
			
			if(frm_validation){
				
				var myRadioPayment = $('input[name=userscontrol_payment_method]');
				var payment_method_selected = myRadioPayment.filter(':checked').val();				
				var payment_method =  jQuery("#userscontrol_payment_method_stripe_hidden").val();
								
				if(payment_method=='stripe' && payment_method_selected=='stripe')
				{					
					var wait_message = '<div class="userscontrol_wait">' + userscontrol_pro_front.wait_submit + '</div>';				
					jQuery('#userscontrol-stripe-payment-errors').html(wait_message);					
					userscontrol_stripe_process_card();
				
				} else if (payment_method=='stripe' && payment_method_selected=='authorize') {
					
				
				}else{
					
					jQuery("#userscontrol-message-submit-booking-conf").html(userscontrol_pro_front.message_wait);					
					$('#userscontrol-btn-conf-signup').prop('disabled', 'disabled');								
					$("#userscontrol-client-registration-form").submit();	
				
				
				}						
				
				
			}else{
				
				
				
			}
			
			
									
    		e.preventDefault();		 
				
        });  
	   
	   jQuery(document).on("click", "#userscontrol-btn-conf-signup", function(e) {			
			

			$("#userscontrol-client-registration-form").validationEngine({promptPosition: 'inline'});				
			var frm_validation  = $("#userscontrol-client-registration-form").validationEngine('validate');
			
			if(frm_validation)	{
				
				var myRadioPayment = $('input[name=userscontrol_payment_method]');
				var payment_method_selected = myRadioPayment.filter(':checked').val();				
				var payment_method =  jQuery("#userscontrol_payment_method_stripe_hidden").val();
								
				if(payment_method=='stripe' && payment_method_selected=='stripe'){
					
					var wait_message = '<div class="userscontrol_wait">' + userscontrol_pro_front.wait_submit + '</div>';				
					jQuery('#userscontrol-stripe-payment-errors').html(wait_message);			
					
					
				
				} else if (payment_method=='stripe' && payment_method_selected=='authorize') {
					
				
				}else{
					
					jQuery("#userscontrol-message-submit-booking-conf").html(userscontrol_pro_front.message_wait);					
					$('#userscontrol-btn-conf-signup').prop('disabled', 'disabled');								
					$("#userscontrol-client-registration-form").submit();	
				
				
				}						
				
				
			}else{
				
				
				
			}
			
			
									
    		e.preventDefault();		 
				
        });
		
		jQuery(document).on("click", ".userscontrol_payment_options", function(e) {		
			
			var payment_method =  jQuery(this).attr("data-method");			
			if(payment_method=='stripe'){
				$(".userscontrol-profile-field-cc").slideDown();
				$("#userscontrol-btn-conf-signup").hide();
				$("#card-button").show();			
				
			}else{
				
				$(".userscontrol-profile-field-cc").slideUp();
				$("#card-button").hide();	
				$("#userscontrol-btn-conf-signup").show();
			}			
       });

	jQuery(document).on("click", "#userscontrol-reset-search", function(e) {
		const url = window.location.href.split('?')[0];		
		window.location.href = url;
    });
	   
	   jQuery(document).on("click", ".userscontrol-front-check-package", function(e) {		
			
			var is_free=  jQuery(this).attr("is-free-package");
			
			if(is_free=='1'){
				
				$(".userscontrol-profile-field-cc").slideUp();				
				$("#userscontrol-payment-header").slideUp();	
				$("#userscontrol-method-paypal").slideUp();
				$("#userscontrol-method-stripe").slideUp();				
				$('.userscontrol_payment_options').prop('checked', false);
				
				$("#card-button").hide();
				$("#userscontrol-btn-conf-signup").show();
				
			}else{
				
				$(".userscontrol-profile-field-cc").slideDown();				
				$("#userscontrol-payment-header").slideDown();
				$("#userscontrol-method-paypal").slideDown();
				$("#userscontrol-method-stripe").slideDown();	

				$("#userscontrol-btn-conf-signup").hide();
				$("#card-button").show();			
				
				
							
			}			
			
		
				
       });
		
 
       
    }); //END READY
})(jQuery);







