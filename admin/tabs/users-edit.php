<?php
global $userscontrol;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$currency_symbol =  $userscontrol->get_option('paid_membership_symbol');
$date_format =  $userscontrol->get_int_date_format();
$time_format =  $userscontrol->get_time_format();
$datetime_format =  $userscontrol->get_date_to_display();

if(isset($_GET['id']) && $_GET['id']!=''){
	
	$user_id = sanitize_text_field($_GET['id']);
	
	// Get user
	$user = get_user_by( 'id', $user_id );	
	
	if(!isset($user->ID)){
		$message = '<div class="userscontrol-ultra-warning"><span><i class="fa fa-check"></i>'.__("Oops! Invalid User.",'users-control').'</span></div>';
 		echo wp_kses($message, $userscontrol->allowed_html);
        exit;
	}
	

}else{
	
    $message = '<div class="userscontrol-ultra-warning"><span><i class="fa fa-check"></i>'.__("Oops! Invalid ID.",'users-control').'</span></div>';
 	echo wp_kses($message, $userscontrol->allowed_html);
    exit;
}


			
$date_created=  date($date_format, strtotime($user->user_registered ));	

//get active subscriptions
$user_memberships =$userscontrol->membership->get_all_user_active_memberships($user_id);
$active_membership_count = count($user_memberships);

$user_expired_memberships =$userscontrol->membership->get_all_user_expired_memberships($user_id);
$expired_membership_count = count($user_expired_memberships);




	

		

?>

<div class="userscontrol-welcome-panel">

<h1><?php _e('Member: ','users-control')?><strong><?php echo esc_attr($user->display_name)?></strong>  </h1>

	  <div class="userscontrol-subscriptiondetail-header-details" >
      
          <ul class="order_details">
          
				<li>
					<?php _e('ID:','users-control')?>	<strong><?php echo esc_attr($user_id)?></strong>
				</li>
                
                

				<li>
					<?php _e('Register Date:','users-control')?><strong><?php echo esc_attr($date_created);?></strong>
				</li>

					<li >
							<?php _e('Active Subscriptions:','users-control')?><strong><?php echo esc_attr($active_membership_count);?></strong>
					</li>
                    
                  <li>
							<?php _e('Expired Subscriptions:','users-control')?><strong><?php echo esc_attr($expired_membership_count);?></strong>
				  </li>
				
				
                
               

				
				
		</ul>
     
     		    	
     
      </div>
      
      
 
  <h2><?php _e('Subscriptions','users-control')?> </h2>
    
<div class="userscontrol-main-app-list" id="userscontrol-backend-landing-1">

<?php

// get member's plans
$all_subscriptions =  $userscontrol->user->get_my_subscriptions($user_id);

?>


 <?php	if (!empty($all_subscriptions)){ ?>
       
           <table width="100%" class="">
            <thead>
                <tr>
                    <th width="4%" ><?php _e('#', 'users-control'); ?></th>
                    <th width="13%"><?php _e('Started', 'users-control'); ?></th> 
                     <th width="13%"><?php _e('Name', 'users-control'); ?></th>
                     <th width="14%"><?php _e('Valid From', 'users-control'); ?></th>
                    <th width="14%"><?php _e('Valid To', 'users-control'); ?></th>                   
                     <th width="14%" ><?php _e('Status', 'users-control'); ?></th>
                    <th width="7%"><?php _e('Actions', 'users-control'); ?></th>
                </tr>
            </thead>
            
            <tbody>
            
            <?php 
			$filter_name= '';
			$phone= '';
			foreach($all_subscriptions as $subscription) {
				$date_created=  date($date_format, strtotime($subscription->subscription_date ));
				$date_from=  date($date_format, strtotime($subscription->subscription_start_date  ));
				$date_to=  date($date_format, strtotime($subscription->subscription_end_date  ));
				
				if($subscription->subscription_lifetime==1)	{
					$date_to=__("Lifetime",'users-control');					
				}

                $status = $userscontrol->membership->get_subscription_status_legend($subscription->subscription_status);
				
			?>
              

                <tr>
                    <td ><?php echo esc_attr($subscription->subscription_id); ?></td>
                     <td><?php echo esc_attr($date_created); ?>   </td>     
                      <td ><?php echo esc_attr($subscription->membership_name); ?> </td>
                      <td ><?php echo esc_attr($date_from); ?></td>
                      <td><?php echo esc_attr($date_to); ?> </td>                      
                      <td ><?php echo  wp_kses($status, $userscontrol->allowed_html)?></td>
                      <td> <a href="?page=userscontrol&tab=subscriptions-edit&id=<?php echo esc_attr($subscription->subscription_id); ?>" class="userscontrol-appointment-edit-module" appointment-id="<?php echo  esc_attr($subscription->subscription_id)?>" title="<?php _e('Edit','users-control'); ?>"><i class="fa fa-edit"></i></a>
                   
                                 
                   
                   </td>
                </tr>
                
                
                <?php
					}
					
					} else {
			?>
			<p><?php _e("You don't have subscriptions",'users-control'); ?></p>
			<?php	} ?>

            </tbody>
        </table>
        

</div>
      
      
      <h2><?php _e('Payments','users-control')?> </h2>
    
<div class="userscontrol-main-app-list" id="userscontrol-backend-landing-1">


<?php

// get member's plans
$all_payments =  $userscontrol->order->get_subscription_payments_by_user($user_id);

?>


 <?php	if (!empty($all_payments)){ ?>
       
           <table width="100%" class="">
            <thead>
                <tr>
                    <th width="4%" ><?php _e('#', 'users-control'); ?></th>
                    <th width="8%"><?php _e('Date', 'users-control'); ?></th> 
                     <th width="8%"><?php _e('Plan', 'users-control'); ?></th> 
                    <th width="13%"><?php _e('Payment Method', 'users-control'); ?></th>
                    <th width="4%"><?php _e('Plan ID', 'users-control'); ?></th>
                    <th width="12%"><?php _e('Transaction ID', 'users-control'); ?></th>
                    <th width="4%"><?php _e('Amount', 'users-control'); ?></th>
                   
                   
                </tr>
            </thead>
            
            <tbody>
            
            <?php 
			$filter_name= '';
			$phone= '';
			$i = 0;
			foreach($all_payments as $payment) {
				
				$date_created=  date($datetime_format, strtotime($payment->order_date ));
				
				$i++;
				
				if( $payment->membership_type=='recurring')
				{									
					$amount = $userscontrol->get_formated_amount_with_currency($payment->order_amount_subscription);
								
				}else{
					
					$amount = $userscontrol->get_formated_amount_with_currency($payment->order_amount);
				
				}
				
			?>
              

                <tr>
                    <td ><?php echo esc_attr($i); ?></td>
                     <td><?php echo esc_attr($date_created); ?>   </td>  
                      <td ><?php echo esc_attr($payment->membership_name); ?> </td>   
                      <td ><?php echo esc_attr($payment->order_method_name); ?> </td>
                      <td ><a href="?page=userscontrol&tab=subscriptions-edit&id=<?php echo esc_attr($payment->subscription_id); ?>"><?php echo esc_attr($payment->subscription_id); ?> </a></td>
                      <td ><?php echo esc_attr($payment->order_txt_id); ?></td>                                       
                      <td ><?php echo esc_attr($amount); ?></td>
                      
                </tr>
                
                
                <?php
					}
					
					} else {
			?>
			<p><?php _e("There are no payments for this user",'users-control'); ?></p>
			<?php	} ?>

            </tbody>
        </table>
        

</div>

	
    
 
</div>


<div id="bup-spinner" class="userscontrol-spinner" style="display:none">
            <span> </span>&nbsp; <?php echo __('Please wait ...','wp-ticket-ultra')?>
	</div>


