<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $userscontrol,   $userscontrol_stripe;

$message = __('Some features of this module are available only on Pro version. Should you wish to start receiving recurring payments from your members, please consider upgrading your plugin.','users-control');
echo wp_kses($userscontrol->admin->only_pro_users_message($message), $userscontrol->allowed_html);
?>
<h3><?php _e('Payment Gateways Settings','users-control'); ?></h3>
<form method="post" action="">
<input type="hidden" name="userscontrol_update_settings" />


<?php if(isset($userscontrol_stripe))
{?>
<div class="userscontrol-sect  userscontrol-welcome-panel ">
  <h3><?php _e('Stripe Settings','users-control'); ?></h3>
  
  <p><?php _e("Stripe is a payment gateway for mechants. If you don't have a Stripe account, you can <a href='https://stripe.com/'> sign up for one account here</a> ",'users-control'); ?></p>
  
  <p><?php _e('Here you can configure Stripe if you wish to accept credit card payments directly in your website. Find your Stripe API keys here <a href="https://dashboard.stripe.com/account/apikeys">https://dashboard.stripe.com/account/apikeys</a>','users-control'); ?></p>
  
  
  <table class="form-table">
<?php 

$this->create_plugin_setting(
                'checkbox',
                'gateway_stripe_active',
                __('Activate Stripe','users-control'),
                '1',
                __('If checked, Stripe will be activated as payment method','users-control'),
                __('If checked, Stripe will be activated as payment method','users-control')
        ); 


$this->create_plugin_setting(
        'input',
        'test_secret_key',
        __('Test Secret Key','users-control'),array(),
        __('You can get this on stripe.com','users-control'),
        __('You can get this on stripe.com','users-control')
);

$this->create_plugin_setting(
        'input',
        'test_publish_key',
        __('Test Publishable Key','users-control'),array(),
        __('You can get this on stripe.com','users-control'),
        __('You can get this on stripe.com','users-control')
);

$this->create_plugin_setting(
        'input',
        'live_secret_key',
        __('Live Secret Key','users-control'),array(),
        __('You can get this on stripe.com','users-control'),
        __('You can get this on stripe.com','users-control')
);

$this->create_plugin_setting(
        'input',
        'live_publish_key',
        __('Live Publishable Key','users-control'),array(),
        __('You can get this on stripe.com','users-control'),
        __('You can get this on stripe.com','users-control')
);


$this->create_plugin_setting(
        'input',
        'signing_secret',
        __('Signing secret','users-control'),array(),
        __('You can get this on Stripe - WebHooks link','users-control'),
        __('You can get this on Stripe - WebHooks link','users-control')
);


$this->create_plugin_setting(
        'input',
        'gateway_stripe_currency',
        __('Currency','users-control'),array(),
        __('Please enter the currency, example USD.','users-control'),
        __('Please enter the currency, example USD.','users-control')
);

$this->create_plugin_setting(
        'textarea',
        'gateway_stripe_success_message',
        __('Custom Message','users-control'),array(),
        __('Input here a custom message that will be displayed to the client once the booking has been confirmed at the front page.','users-control'),
        __('Input here a custom message that will be displayed to the client once the booking has been confirmed at the front page.','users-control')
);

$this->create_plugin_setting(
                'checkbox',
                'gateway_stripe_success_active',
                __('Custom Success Page Redirect ','users-control'),
                '1',
                __('If checked, the users will be taken to this page once the payment has been confirmed','users-control'),
                __('If checked, the users will be taken to this page once the payment has been confirmed','users-control')
        ); 


$this->create_plugin_setting(
            'select',
            'gateway_stripe_success',
            __('Success Page','users-control'),
            $this->get_all_sytem_pages(),
            __("Select the sucess page. The user will be taken to this page if the payment was approved by stripe.",'users-control'),
            __('Select the sucess page. The user will be taken to this page if the payment was approved by stripe.','users-control')
    );


$this->create_plugin_setting(
	'select',
	'enable_live_key',
	__('Mode','users-control'),
	array(
		1 => __('Production Mode','users-control'), 
		2 => __('Test Mode (Sandbox)','users-control')
		),
		
	__('.','users-control'),
  __('.','users-control')
       );
	   



		
?>
</table>

  
</div>

<?php }?>


<?php if(isset($bupcomplement))
{?>
<div class="userscontrol-sect  userscontrol-welcome-panel" style="display:none">
  <h3><?php _e('Authorize.NET AIM Settings','users-control'); ?></h3>
  
  <p><?php _e(" ",'users-control'); ?></p>
  
  <p><?php _e(' ','users-control'); ?></p>
  
  
  <table class="form-table">
<?php 

$this->create_plugin_setting(
                'checkbox',
                'gateway_authorize_active',
                __('Activate Authorize','users-control'),
                '1',
                __('If checked, Authorize will be activated as payment method','users-control'),
                __('If checked, Authorize will be activated as payment method','users-control')
        ); 



$this->create_plugin_setting(
        'input',
        'authorize_login',
        __('API Login ID','users-control'),array(),
        __('You can get this on authorize.net','users-control'),
        __('You can get this on authorize.net','users-control')
);

$this->create_plugin_setting(
        'input',
        'authorize_key',
        __('API Transaction Key','users-control'),array(),
        __('You can get this on authorize.net','users-control'),
        __('You can get this on authorize.net','users-control')
);


$this->create_plugin_setting(
        'input',
        'authorize_currency',
        __('Currency','users-control'),array(),
        __('Please enter the currency, example USD.','users-control'),
        __('Please enter the currency, example USD.','users-control')
);

$this->create_plugin_setting(
        'textarea',
        'gateway_authorize_success_message',
        __('Custom Message','users-control'),array(),
        __('Input here a custom message that will be displayed to the client once the booking has been confirmed at the front page.','users-control'),
        __('Input here a custom message that will be displayed to the client once the booking has been confirmed at the front page.','users-control')
);

$this->create_plugin_setting(
                'checkbox',
                'gateway_authorize_success_active',
                __('Custom Success Page Redirect ','users-control'),
                '1',
                __('If checked, the users will be taken to this page once the payment has been confirmed','users-control'),
                __('If checked, the users will be taken to this page once the payment has been confirmed','users-control')
        ); 


$this->create_plugin_setting(
            'select',
            'gateway_authorize_success',
            __('Success Page','users-control'),
            $this->get_all_sytem_pages(),
            __("Select the sucess page. The user will be taken to this page if the payment was approved by Authorize.net ",'users-control'),
            __('Select the sucess page. The user will be taken to this page if the payment was approved by Authorize.net','users-control')
    );


$this->create_plugin_setting(
	'select',
	'authorize_mode',
	__('Mode','users-control'),
	array(
		1 => __('Production Mode','users-control'), 
		2 => __('Test Mode (Sandbox)','users-control')
		),
		
	__('.','users-control'),
  __('.','users-control')
       );
	   



		
?>
</table>

  
</div>

<?php }?>

<div class="userscontrol-sect  userscontrol-welcome-panel">
  <h3><?php _e('PayPal','users-control'); ?></h3>
  
  <p><?php _e('Here you can configure PayPal if you wish to accept paid registrations','users-control'); ?></p>
    <p><?php _e("Please note: You have to set a right currency <a href='https://developer.paypal.com/docs/classic/api/currency_codes/' target='_blank'> check supported currencies here </a> ",'users-control'); ?></p>
  
  
  <table class="form-table">
<?php 

$this->create_plugin_setting(
                'checkbox',
                'gateway_paypal_active',
                __('Activate PayPal','users-control'),
                '1',
                __('If checked, PayPal will be activated as payment method','users-control'),
                __('If checked, PayPal will be activated as payment method','users-control')
        ); 

$this->create_plugin_setting(
	'select',
	'send_paypal_ipn_to_admin',
	__('The Paypal IPN response will be sent to the admin','users-control'),
	array(
		'no' => __('No','users-control'), 
		'yes' => __('Yes','users-control'),
		),
		
	__("If 'yes' the admin will receive the whole Paypal IPN response. This helps to troubleshoot issues.",'users-control'),
  __("If 'yes' the admin will receive the whole Paypal IPN response. This helps to troubleshoot issues.",'users-control')
       );

$this->create_plugin_setting(
        'input',
        'gateway_paypal_email',
        __('PayPal Email Address','users-control'),array(),
        __('Enter email address associated to your PayPal account.','users-control'),
        __('Enter email address associated to your PayPal account.','users-control')
);

$this->create_plugin_setting(
        'input',
        'gateway_paypal_sandbox_email',
        __('Paypal Sandbox Email Address','users-control'),array(),
        __('This is not used for production, you can use this email for testing.','users-control'),
        __('This is not used for production, you can use this email for testing.','users-control')
);

$this->create_plugin_setting(
        'input',
        'gateway_paypal_currency',
        __('Currency','users-control'),array(),
        __('Please enter the currency, example USD.','users-control'),
        __('Please enter the currency, example USD.','users-control')
);


$this->create_plugin_setting(
                'checkbox',
                'gateway_paypal_success_active',
                __('Custom Success Page Redirect ','users-control'),
                '1',
                __('If checked, the users will be taken to this page once the payment has been confirmed','users-control'),
                __('If checked, the users will be taken to this page once the payment has been confirmed','users-control')
        ); 


$this->create_plugin_setting(
            'select',
            'gateway_paypal_success',
            __('Success Page','users-control'),
            $this->get_all_sytem_pages(),
            __("Select the sucess page. The user will be taken to this page if the payment was approved by stripe.",'users-control'),
            __('Select the sucess page. The user will be taken to this page if the payment was approved by stripe.','users-control')
    );
	
	
	$this->create_plugin_setting(
                'checkbox',
                'gateway_paypal_cancel_active',
                __('Custom Cancellation Page Redirect ','users-control'),
                '1',
                __('If checked, the users will be taken to this page if the payment is cancelled at PayPal website','users-control'),
                __('If checked, the users will be taken to this page if the payment is cancelled at PayPal website','users-control')
        ); 
		
		
		$this->create_plugin_setting(
            'select',
            'gateway_paypal_cancel',
            __('Cancellation Page','users-control'),
            $this->get_all_sytem_pages(),
            __("Select the cancellation page. The user will be taken to this page if the payment is cancelled at PayPal Website",'users-control'),
            __('Select the cancellation page. The user will be taken to this page if the payment is cancelled at PayPal Website','users-control')
    );


$this->create_plugin_setting(
	'select',
	'gateway_paypal_mode',
	__('Mode','users-control'),
	array(
		1 => __('Production Mode','users-control'), 
		2 => __('Test Mode (Sandbox)','users-control')
		),
		
	__('.','users-control'),
  __('.','users-control')
       );
	   





		
?>
</table>

  
</div>


<div class="userscontrol-sect  userscontrol-welcome-panel ">
  <h3><?php _e('Bank Deposit/Cash Other','users-control'); ?></h3>
  
  <p><?php _e('Here you can configure the information that will be sent to the client. This could be your bank account details.','users-control'); ?></p>
  
  
  <table class="form-table">
<?php 

$this->create_plugin_setting(
                'checkbox',
                'gateway_bank_active',
                __('Activate Bank Deposit','users-control'),
                '1',
                __('If checked, Bank Payment Deposit will be activated as payment method','users-control'),
                __('If checked, Bank Payment Deposit will be activated as payment method','users-control')
        ); 


$this->create_plugin_setting(
        'input',
        'gateway_bank_label',
        __('Custom Label','users-control'),array(),
        __('Example: Bank Deposit , Cash, Wire etc.','users-control'),
        __('Example: Bank Deposit , Cash, Wire etc.','users-control')
);


$this->create_plugin_setting(
        'textarea',
        'gateway_bank_success_message',
        __('Custom Message','users-control'),array(),
        __('Input here a custom message that will be displayed to the client once the booking has been confirmed at the front page.','users-control'),
        __('Input here a custom message that will be displayed to the client once the booking has been confirmed at the front page.','users-control')
);



$this->create_plugin_setting(
                'checkbox',
                'gateway_bank_success_active',
                __('Custom Success Page Redirect ','users-control'),
                '1',
                __('If checked, the users will be taken to this page ','users-control'),
                __('If checked, the users will be taken to this page ','users-control')
        ); 


$this->create_plugin_setting(
            'select',
            'gateway_bank_success',
            __('Success Page','users-control'),
            $this->get_all_sytem_pages(),
            __("Select the sucess page. The user will be taken to this page on purchase confirmation",'users-control'),
            __('Select the sucess page. The user will be taken to this page on purchase confirmation','users-control')
    );
	
	$data_status = array(
		 				'0' => 'Pending',
                        '1' =>'Approved'
                       
                    );
$this->create_plugin_setting(
            'select',
            'gateway_bank_default_status',
            __('Default Status for Local Payments','users-control'),
            $data_status,
            __("Set the default status a subscription will have when using local payment method. ",'users-control'),
            __('et the default status a subscription will have when using local payment method.','users-control')
    );	

		
?>
</table>

  
</div>



<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','users-control'); ?>"  />
	
</p>

</form>