<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $userscontrol, $userscontrol_stripe, $userscontrol_recurring, $userscontrol_onlyroles, $userscontrol_assignrole;
$message = __('This feature is available only on Pro version. Should you wish to create membership plans, please consider upgrading your plugin.','users-control');
echo wp_kses($userscontrol->admin->only_pro_users_message($message), $userscontrol->allowed_html);
?>

<form method="post" action="">
<input type="hidden" name="userscontrol_create_membership" />

<?php wp_nonce_field( 'update_settings', 'userscontrol_nonce_check' ); ?>

<div class="userscontrol-sect  userscontrol-welcome-panel"> 

<?php echo wp_kses($userscontrol->membership->get_errors(), $userscontrol->allowed_html);?> 
<?php echo wp_kses($userscontrol->membership->sucess_message, $userscontrol->allowed_html);?> 

<h3><?php _e('Add Subscription', 'users-control'); ?></h3>
       
           <table width="100%" class="">
                      
            <tbody>

                <tr >
                    <td ><?php _e('Name', 'users-control'); ?></td>
                     <td> <input name="userscontrol_subscription_name" id="userscontrol_subscription_name" value="<?php echo $userscontrol->get_post_value('userscontrol_subscription_name')?>" type="text"> </td>        
                </tr>
                
                <tr >
                    <td ><?php _e('Description', 'users-control'); ?></td>
                     <td> <?php echo $userscontrol->admin->get_me_wphtml_editor("userscontrol_subscription_desc", $userscontrol->get_post_value('userscontrol_subscription_desc'), 8);?> </td>        
                </tr>
                
                
                
            </tbody>
        </table>
        
                <h4><?php _e('Billing Details', 'users-control'); ?></h4>
        
         <table width="100%" class="">
                      
            <td width="24%">
            <tbody>
            
             <tr >
                    <td ><?php _e('Type', 'users-control'); ?></td>
                     <td> <select name="userscontrol_subscription_type" id="userscontrol_subscription_type">
             				<option value="onetime" ><?php _e('One-Time', 'users-control'); ?></option> 
                            <?php if(isset($userscontrol_recurring)){?>                            
                            <option value="recurring" ><?php _e('Recurring', 'users-control'); ?></option> 
                            <?php }?>          
             
           				   </select></td>        
                </tr>
                
                
                 <tr >
                    <td ><?php _e('Lifetime Membership?', 'users-control'); ?></td>
                     <td> <select name="userscontrol_subscription_lifetime" id="userscontrol_subscription_lifetime">
             				<option value="0" ><?php _e('NO', 'users-control'); ?></option>                             
                            <option value="1" ><?php _e('YES', 'users-control'); ?></option> 
                                        
             
           				   </select></td>        
                </tr>
          

                <tr >
                    <td ><?php _e('Initial Payment', 'users-control'); ?></td>
                     <td width="76%"> <input name="userscontrol_subscription_initial_payment" id="userscontrol_subscription_initial_payment" value="<?php echo $userscontrol->get_post_value('userscontrol_subscription_initial_payment')?>" type="text"> - <?php _e('Use this for one-time payments or setup payment for recurring subscriptions. Input 0 for free subscriptions', 'users-control'); ?></td>        
                </tr>
                
                 
                
                  
                <tr >
                    <td ><?php _e('Recurring Payment', 'users-control'); ?></td>
                     <td> <input name="userscontrol_subscription_reccurring_amount" id="userscontrol_subscription_reccurring_amount" value="<?php echo $userscontrol->get_post_value('userscontrol_subscription_reccurring_amount')?>" type="text">  </td>        
                </tr>
                
                  <tr >
                    <td ><?php _e('Every', 'users-control'); ?></td>
                     <td> <select name="userscontrol_subscription_every" id="userscontrol_subscription_every">
             				<option value="1" >1</option>
             
                                    
                         <?php
                          
                          $i = 2;			  
                          $html = '';              
                          while($i <=31){
                              
                              $sel = "";				              
                               $html .= '<option value="'.$i.'" '.$sel.'>'.$i.'</option>';  
                          
                            $i++;
                          }
                         
                         $html .= '</select>' ;

                         echo wp_kses($html, $userscontrol->allowed_html);                         
                    ?> 
                         
                         <select name="userscontrol_subscription_period" id="userscontrol_subscription_period">
             				<option value="M" ><?php _e('Month(s)', 'users-control'); ?></option>                             
                            <option value="W" ><?php _e('Week(s)', 'users-control'); ?></option>
                            <option value="D" ><?php _e('Day(s)', 'users-control'); ?></option> 
                            <option value="Y" ><?php _e('Year(s)', 'users-control'); ?></option>              
             
           				   </select></td>        
                </tr>
                
                                
                  <tr >
                    <td ><?php _e('Billing Cycle Limit', 'users-control'); ?></td>
                     <td> <input name="userscontrol_subscription_cycle_period" id="userscontrol_subscription_cycle_period" value="0" type="text"> - <?php _e('This is the total number of recurring billing cicles for this subscription. Set to zero if membership cycle is indefinite', 'users-control'); ?></td>        
                 </tr>
                 
                
                
                
            </tbody>
        </table>
        
        
         <?php if(isset($userscontrol_stripe) && isset($userscontrol_recurring)){
			
			?>
        
         <h4><?php _e('Stripe Settings ', 'users-control'); ?></h4>
         
          <table width="100%" class="">
                      
            <td width="24%"><tbody>         

          	      <tr >
                    <td ><?php _e('Choose Your Plan', 'users-control'); ?></td>
                     <td width="76%"><?php
                     $stripe_plans = $userscontrol_stripe->get_stripe_plans_drop_box($package->membership_stripe_id );
                     echo wp_kses($stripe_plans, $userscontrol->allowed_html); ;?> </td>        
          	      </tr>
                
                    
        	    </tbody>
       	 </table>
        
        
        <?php }?>
        
         <h4><?php _e('Other Settings ', 'users-control'); ?></h4>
         
          <table width="100%" class="">
                      
            <td width="24%"><tbody>
          
 				 <tr >
                    <td ><?php _e('Requires Admin Approvation?', 'users-control'); ?></td>
                     <td> <select name="userscontrol_subscription_requires_approvation" id="userscontrol_subscription_requires_approvation">
             				<option value="0" ><?php _e('NO', 'users-control'); ?></option>                             
                            <option value="1" ><?php _e('YES', 'users-control'); ?></option>
                                       
             
           				   </select> - <?php _e('If YES, The admin will have to approve this account.', 'users-control'); ?></td>        
                 </tr>
                 
                 
 				 <tr >
                    <td ><?php _e('Public Visible?', 'users-control'); ?></td>
                     <td> <select name="userscontrol_subscription_public_visible" id="userscontrol_subscription_public_visible">
             				 <option value="1" ><?php _e('YES', 'users-control'); ?></option>
                            <option value="0" ><?php _e('NO', 'users-control'); ?></option>                             
                           
                                       
             
           				   </select> - <?php _e("If NO, This subscription won't be visible on the public side.", 'users-control'); ?></td>        
                 </tr>
                 
                   <tr >
                    <td ><?php _e('Display Order', 'users-control'); ?></td>
                     <td width="76%"> <input name="userscontrol_display_order" id="userscontrol_display_order" value="<?php echo $userscontrol->get_post_value('userscontrol_display_order')?>" type="text" value="0"> - <?php _e('Input the display ordering for this subscription. Example: 1,2,3', 'users-control'); ?></td>        
                </tr>
                
                <tr >
                    <td ><?php _e('Status', 'users-control'); ?></td>
                     <td> <select name="userscontrol_status" id="userscontrol_status">
             				 <option value="1" ><?php _e('Active', 'users-control'); ?></option>
                            <option value="0" ><?php _e('Deactivated', 'users-control'); ?></option>                             
                           
                                       
             
           				   </select> - <?php _e("If Deactivated, This subscription won't be available on the website.", 'users-control'); ?></td>        
                 </tr>
                 
                 <?php if(isset($userscontrol_assignrole)){?>
                 
                  <tr >
                    <td ><?php _e('Assign Role', 'users-control'); ?></td>
                     <td> 
                            <?php
							
							 $display = '';
               $selected_role = '';
							 
							 $allowed_user_roles = $userscontrol->role->get_available_user_roles();
                            
							 $display .= '<select name="userscontrol_subscription_role_to_assign" id="userscontrol_subscription_role_to_assign">';
							 $display .= '<option value="" >'.__('Do not assign role', 'users-control').'</option>';
         
		
							   foreach ($allowed_user_roles as $key => $val)
							   {
								   $sel ="";
								   if($selected_role==$key) 
								   {
									   $sel = 'selected="selected"';
									  
								   }
								   $display .= '<option value="' . $key . '" '.$sel.' >' . $val . '</option>';
							   }
							  
							  $display .= '</select>';
                echo wp_kses($display, $userscontrol->allowed_html);
							
							?>                           
                           
                                       
             
           				    - <?php _e("This role will be assigned automatically when the user signs up.", 'users-control'); ?></td>        
                 </tr>
                
				<?php }?>
                

                
            </tbody>
        </table>
        
         <h4><?php _e('Content Accessibility ', 'users-control'); ?></h4>
         
          <table width="100%" class="">
            <td width="24%"><tbody>
                <tr >
                    <td ><?php _e('Categories', 'users-control'); ?></td>
                     <td width="76%"><?php 
                     $p = $userscontrol->get_subscription_categories();
                     echo wp_kses($p, $userscontrol->allowed_html);?> </td>        
                </tr>
                
                <?php if(isset($userscontrol_onlyroles)){?>
                  <tr >
                    <td ><?php _e('Roles', 'users-control'); ?></td>
                     <td><?php 
                     $p = $userscontrol->role->get_package_roles();
                     echo  wp_kses($p, $userscontrol->allowed_html);?></td>                       
                  </tr>
                
				 <?php }?>
                

                
            </tbody>
        </table>
        
  <p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Submit','users-control'); ?>"  />
</p>
      


</div>


</form>