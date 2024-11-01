<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $userscontrol;

$all_packages = $userscontrol->membership->get_all();	

$message = __('This feature is available only on Pro version. Should you wish to create membership plans, please consider upgrading your plugin.','users-control');
echo wp_kses($userscontrol->admin->only_pro_users_message($message), $userscontrol->allowed_html);
?>




<div class="userscontrol-sect  userscontrol-welcome-panel">  


<div class="userscontrol-top-options-book">            
            	                
                <a class="userscontrol-btn-top1-book" href="?page=userscontrol&tab=membership-add" title="<?php _e('Create New', 'users-control'); ?>"><span><i class="fa fa-plus fa-2x"></i><?php _e('Create New', 'users-control'); ?></span></a>                     
                                
           </div>
           

<h3><?php _e('Subscription Plans', 'users-control'); ?></h3>


 <?php	if (!empty($all_packages)){ ?>
       
           <table width="100%" class="">
            <thead>
                <tr>
                    <th width="4%" ><?php _e('ID', 'users-control'); ?></th>
                     <th width="4%" ><?php _e('Order', 'users-control'); ?></th>
                    <th width="10%"><?php _e('Name', 'users-control'); ?></th> 
                     
                      <th width="12%" ><?php _e('Type', 'users-control'); ?></th>  
                      
                        <th width="8%" ><?php _e('Initial Payment', 'users-control'); ?></th> 
                        <th width="18%"><?php _e('Agreement', 'users-control'); ?></th>
                    
                    
                     
                     <th width="7%"><?php _e('Status', 'users-control'); ?></th>
                    <th width="10%"><?php _e('Actions', 'users-control'); ?></th>
                </tr>
            </thead>
            
            <tbody>
            
            <?php 
			$filter_name= '';
			$phone= '';
			foreach($all_packages as $package) {
				
				$recurring_amount = 'N/A';
				if( $package->membership_type=='recurring'){
					
					$recurring_amount = $userscontrol->get_formated_amount_with_currency($package->membership_subscription_amount);
				
				}				
				
				$initial_amount = $userscontrol->get_formated_amount_with_currency($package->membership_initial_amount);
				
				//get payment formated
				$formated_agreement =  $userscontrol->get_formated_agreement($package);
				
				
				if($package->membership_status == 1){					
					$status=__('Active', 'users-control'); 
				}else{
					$status=__('Deactivated', 'users-control'); 
				}
				
					
			?>
              

                <tr >
                    <td ><?php echo esc_attr($package->membership_id); ?></td>
                     <td ><?php echo esc_attr($package->membership_order); ?></td>
                     <td><?php echo esc_attr($package->membership_name); ?>     </td>
                     <td><?php echo esc_attr($package->membership_type); ?>     </td>
                       <td ><?php echo esc_attr($initial_amount); ?> </td>                     
                           
                      <td ><?php echo esc_attr($formated_agreement) ; ?></td>                   
                   
                    <td><?php echo  esc_attr($status); ?></td>                  
                     
                      
                   <td> <a href="?page=userscontrol&tab=membership-edit&id=<?php echo esc_attr($package->membership_id)?>" class="userscontrol-appointment-edit-module" title="<?php _e('Edit','users-control'); ?>"><i class="fa fa-edit"></i></a>
                   
                  
                   &nbsp;<a href="#" class="userscontrol-appointment-delete-module" ticket-id="<?php echo esc_attr($package->membership_id)?>" title="<?php _e('Delete','users-control'); ?>"><i class="fa fa-trash-o"></i></a>
                  
              
                  
                   
                   </td>
                </tr>
                
                
                <?php
					}
					
					} else {
			?>
			<p><?php _e('There are no subscription packages at the moment.','users-control'); ?></p>
			<?php	} ?>

            </tbody>
        </table>

</div>