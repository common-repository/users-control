<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $userscontrol ;

$current_user = $userscontrol->user->get_user_info();
$user_id = $current_user->ID;

$currency_symbol =  $userscontrol->get_option('paid_membership_symbol');
$date_format =  $userscontrol->get_int_date_format();
$time_format =  $userscontrol->get_time_format();
$datetime_format =  $userscontrol->get_date_to_display();
$active_subscriptions =  $userscontrol->user->get_my_active_subscriptions($user_id);


?>

<h1><?php _e('Dashboard','users-control')?></h1>

<h2><?php _e('Active Subscriptions','users-control')?> <span class="userscontrol-widget-backend-colspan"><a href="#" title="<?php _e('Close','users-control')?> " class="userscontrol-widget-backend-colapsable" widget-id="0"><i class="fa fa-sort-asc" id="userscontrol-close-open-icon-0"></i></a></span></h2>
    
<div class="userscontrol-main-app-list" id="userscontrol-backend-landing-1">


 <?php	if (!empty($active_subscriptions)){ ?>
       
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
			foreach($active_subscriptions as $subscription) {
				
				$date_created=  date($date_format, strtotime($subscription->subscription_date ));
				
				$date_from=  date($date_format, strtotime($subscription->subscription_start_date  ));
				$date_to=  date($date_format, strtotime($subscription->subscription_end_date  ));
				
				if($subscription->subscription_lifetime==1)		
				{
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
                      <td ><?php echo wp_kses($status, $userscontrol->allowed_html); ?></td>
                      <td> <a href="?module=subscription_detail&id=<?php echo esc_attr($subscription->subscription_id)?>" class="userscontrol-appointment-edit-module" appointment-id="<?php echo  esc_attr($subscription->subscription_id)?>" title="<?php _e('Edit','users-control'); ?>"><i class="fa fa-edit"></i></a>
                   
                                 
                   
                   </td>
                </tr>
                
                
                <?php
					}
					
					} else {
			?>
			<p><?php _e("You don't have active subscriptions",'users-control'); ?></p>
			<?php	} ?>

            </tbody>
        </table>
        

</div>
