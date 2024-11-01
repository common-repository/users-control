<?php
global $userscontrol;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$currency_symbol =  $userscontrol->get_option('paid_membership_symbol');
$date_format =  $userscontrol->get_int_date_format();
$time_format =  $userscontrol->get_time_format();
$datetime_format =  $userscontrol->get_date_to_display();

if(isset($_GET['id']) && $_GET['id']!=''){
	
	$subscription_id = sanitize_text_field($_GET['id']);
	
	// Get Subscription
	$subscription = $userscontrol->order->get_subscription($subscription_id);
	$subscription_user_id = $subscription->subscription_user_id;
	
	

}else{
	$message = '<div class="userscontrol-ultra-warning"><span><i class="fa fa-check"></i>'.__("Oops! Invalid Subscription.",'users-control').'</span></div>';
	echo wp_kses($message, $userscontrol->allowed_html);
 	exit;
}

$package = $userscontrol->membership->get_one($subscription->subscription_package_id );
$subscription_payments =  $userscontrol->order->get_subscription_payments($subscription_id);

$date_created=  date($date_format, strtotime($subscription->subscription_date ));				
$date_from=  date($date_format, strtotime($subscription->subscription_start_date  ));
$date_to=  date($date_format, strtotime($subscription->subscription_end_date  ));	


if($subscription->subscription_lifetime==1){
	$date_to=__("Lifetime",'users-control');					
}

$type_legend = __('One-time','users-control');
if( $package->membership_type=='recurring'){
	$type_legend = __('Recurring ','users-control');
}				
				
$initial_amount = $userscontrol->get_formated_amount_with_currency($package->membership_initial_amount);
				
//get payment formated
$formated_agreement =  $userscontrol->get_formated_agreement($package);	

?>

<div class="userscontrol-welcome-panel">

<h1><?php _e('Membership: ','users-control')?><strong><?php echo esc_attr($package->membership_name)?></strong> 
<span class="userscontrol-sub-tasks"><button class="userscontrol-btn-quick-actions-btn close" title="<?php _e('Edit Membership: ','users-control')?>" subscription-id="<?php echo esc_attr($subscription->subscription_id);?>" id="userscontrol-edit-subscription"><i class="fa fa-edit"></i><?php _e('Edit Membership','users-control')?> </button></span></h1>


	  <div class="userscontrol-subscriptiondetail-header-details" >
      
          <ul class="order_details">
          
				<li>
					<?php _e('ID:','users-control')?>	<strong><?php echo esc_attr($subscription_id)?></strong>
				</li>
                
                <?php if( $package->membership_type=='recurring'){?>
                
                <li>
					<?php _e('Profile ID:','users-control')?>	<strong><?php echo esc_attr($subscription->subscription_merchant_id) ?></strong>
				</li>
                
                <?php }?>

				<li>
					<?php _e('Date:','users-control')?><strong><?php echo esc_attr($date_created);?></strong>
				</li>

					<li >
							<?php _e('Starts:','users-control')?><strong><?php echo esc_attr($date_from);?></strong>
					</li>
                    
                  <li>
							<?php _e('Ends:','users-control')?><strong><?php echo esc_attr($date_to);?></strong>
				  </li>
				
				<li >
						<?php _e('Type:','users-control')?><strong><span ><?php echo esc_attr($type_legend);?></span></strong>
				</li>
                
                <li >
						<?php _e('Status:','users-control')?><strong><span ><?php echo wp_kses($userscontrol->membership->get_subscription_status_legend($subscription->subscription_status), $userscontrol->allowed_html);?></span></strong>
				</li>

				<li class="userscontrolwoopaymentmethod" >
							<?php _e('Subscription Agreement:','users-control')?>	<strong><?php echo esc_attr($formated_agreement);?></strong>
			    </li>
				
		</ul>
     
     		    	
     
      </div>
      
      
      <h2><?php _e('Payments','users-control')?> </h2>
    
<div class="userscontrol-main-app-list" id="userscontrol-backend-landing-1">


 <?php	if (!empty($subscription_payments)){ ?>
       
           <table width="100%" class="">
            <thead>
                <tr>
                    <th width="4%" ><?php _e('#', 'users-control'); ?></th>
                    <th width="13%"><?php _e('Date', 'users-control'); ?></th> 
                    <th width="13%"><?php _e('Payment Method', 'users-control'); ?></th>
                    <th width="13%"><?php _e('Transaction ID', 'users-control'); ?></th>
                    <th width="14%"><?php _e('Amount', 'users-control'); ?></th>
                   
                   
                </tr>
            </thead>
            
            <tbody>
            
            <?php 
			$filter_name= '';
			$phone= '';
			$i = 0;
			foreach($subscription_payments as $payment) {
				
				$date_created=  date($datetime_format, strtotime($payment->order_date ));
				
				$i++;
				
				if( $package->membership_type=='recurring')
				{									
					$amount = $userscontrol->get_formated_amount_with_currency($payment->order_amount_subscription);
								
				}else{
					
					$amount = $userscontrol->get_formated_amount_with_currency($payment->order_amount);
				
				}
				
			?>
              

                <tr>
                    <td ><?php echo esc_attr($i); ?></td>
                     <td><?php echo esc_attr($date_created); ?>   </td>     
                      <td ><?php echo esc_attr($payment->order_method_name); ?> </td>
                      <td ><?php echo esc_attr($payment->order_txt_id); ?></td>                                       
                      <td ><?php echo esc_attr($amount); ?></td>
                      
                </tr>
                
                
                <?php
					}
					
					} else {
			?>
			<p><?php _e("There are no payments for this subscription",'users-control'); ?></p>
			<?php	} ?>

            </tbody>
        </table>
        

</div>

	
    
 
</div>

   <div id="userscontrol-edit-subscription-details" title="<?php _e('Edit Membership','users-control')?>"></div>


<div id="bup-spinner" class="userscontrol-spinner" style="display:none">
            <span> </span>&nbsp; <?php  _e('Please wait ...','wp-ticket-ultra')?>
	</div>


