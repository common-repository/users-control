<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $userscontrol, $userscontrol_stripe, $userscontrol_recurring, $userscontrol_onlyroles, $userscontrol_assignrole;

if(isset($_GET['id']) && $_GET['id']!=''){	
	$membership_id = sanitize_text_field($_GET['id']);	
	// Get Subscription
	$package = $userscontrol->membership->get_one($membership_id);

}else{

 
	
  $message =  '<div class="userscontrol-ultra-warning"><span><i class="fa fa-check"></i>'.__("Oops! Invalid Subscription.",'users-control').'</span></div>';
  echo wp_kses($message, $userscontrol->allowed_html);
  exit;
	
	
}
		
?>

<form method="post" action="">
<input type="hidden" name="userscontrol_edit_membership" />
<input type="hidden" name="subscription_id" value="<?php echo esc_attr($membership_id);?>"/>

<?php wp_nonce_field( 'update_settings', 'userscontrol_nonce_check' ); ?>

<div class="userscontrol-sect  userscontrol-welcome-panel"> 

<?php echo wp_kses($userscontrol->membership->get_errors(), $userscontrol->allowed_html);?> 
<?php echo wp_kses( $userscontrol->membership->sucess_message, $userscontrol->allowed_html);?> 

<h3><?php _e('Edit Subscription', 'users-control'); ?></h3>


           <table width="100%" class="">
                      
            <tbody>
          

                <tr >
                    <td ><?php _e('Name', 'users-control'); ?></td>
                     <td> <input name="userscontrol_subscription_name" id="userscontrol_subscription_name" value="<?php echo esc_attr($package->membership_name) ?>" type="text"> </td>        
                </tr>
                
                <tr >
                    <td ><?php _e('Description', 'users-control'); ?></td>
                     <td> <?php echo $userscontrol->admin->get_me_wphtml_editor("userscontrol_subscription_desc", $package->membership_description, 8);?> </td>        
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
             				<option value="onetime"  <?php if( $package->membership_type=='onetime'){ echo esc_attr('selected="selected"');}?> ><?php _e('One-Time', 'users-control'); ?></option>                             
                            
                            <?php if(isset($userscontrol_recurring)){?> 
                            <option value="recurring" <?php if( $package->membership_type=='recurring'){ echo esc_attr('selected="selected"');}?>><?php _e('Recurring', 'users-control'); ?></option> 
                              <?php }?>             
             
           				   </select></td>        
                </tr>
                
                
                 <tr >
                    <td ><?php _e('Lifetime Membership?', 'users-control'); ?></td>
                     <td> <select name="userscontrol_subscription_lifetime" id="userscontrol_subscription_lifetime">
             				<option value="0" <?php if( $package->membership_lifetime =='0'){ echo esc_attr('selected="selected"');}?>><?php _e('NO', 'users-control'); ?></option>                             
                            <option value="1" <?php if( $package->membership_lifetime =='1'){ echo esc_attr('selected="selected"');}?>><?php _e('YES', 'users-control'); ?></option> 
                                        
             
           				   </select></td>        
                </tr>
          

                <tr >
                    <td ><?php _e('Initial Payment', 'users-control'); ?></td>
                     <td width="76%"> <input name="userscontrol_subscription_initial_payment" id="userscontrol_subscription_initial_payment" value="<?php echo esc_attr($package->membership_initial_amount); ?>" type="text"> - <?php _e('Use this for one-time payments or setup payment for recurring subscriptions. Input 0 for free subscriptions', 'users-control'); ?></td>        
                </tr>
                
                 
                
                  
                <tr >
                    <td ><?php _e('Recurring Payment', 'users-control'); ?></td>
                     <td> <input name="userscontrol_subscription_reccurring_amount" id="userscontrol_subscription_reccurring_amount" value="<?php echo esc_attr($package->membership_subscription_amount);?>" type="text">  </td>        
                </tr>
                
                  <tr >
                    <td ><?php _e('Every', 'users-control'); ?></td>
                     <td> <select name="userscontrol_subscription_every" id="userscontrol_subscription_every">
             				             
                                    
                         <?php
                          
                          $i = 1;			  
                          $html = '';              
                          while($i <=31){
                              
                              $sel = "";
							  if($i==$package->membership_every )	{$sel = 'selected="selected"';}	
							                
                              $html .= '<option value="'.$i.'" '.$sel.' >'.$i.'</option>';  
                          
                            $i++;
                          }
                         
                         $html .= '</select>' ;
                         
                         echo  $html;?> <select name="userscontrol_subscription_period" id="userscontrol_subscription_period">
             				<option value="M" <?php if( $package->membership_time_period  =='M'){ echo esc_attr( 'selected="selected"');}?>><?php _e('Month(s)', 'users-control'); ?></option>                             
                            <option value="W"  <?php if( $package->membership_time_period  =='W'){ echo esc_attr('selected="selected"');}?>><?php _e('Week(s)', 'users-control'); ?></option>
                            <option value="D"  <?php if( $package->membership_time_period  =='D'){ echo esc_attr('selected="selected"');}?>><?php _e('Day(s)', 'users-control'); ?></option> 
                            <option value="Y"  <?php if( $package->membership_time_period  =='Y'){ echo esc_attr('selected="selected"');}?>><?php _e('Year(s)', 'users-control'); ?></option>              
             
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
                     <td width="76%"><?php  echo wp_kses($userscontrol_stripe->get_stripe_plans_drop_box($package->membership_stripe_id ), $userscontrol->allowed_html);?> </td>        
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
             				<option value="0"  <?php if( $package->membership_approvation   =='0'){ echo esc_attr('selected="selected"');}?>><?php _e('NO', 'users-control'); ?></option>                             
                            <option value="1" <?php if( $package->membership_approvation   =='1'){ echo esc_attr('selected="selected"');}?>><?php _e('YES', 'users-control'); ?></option>
                                       
             
           				   </select> - <?php _e('If YES, The admin will have to approve this account.', 'users-control'); ?></td>        
                 </tr>
                 
                 
 				 <tr >
                    <td ><?php _e('Public Visible?', 'users-control'); ?></td>
                     <td> <select name="userscontrol_subscription_public_visible" id="userscontrol_subscription_public_visible">
             				 <option value="1" <?php if( $package->membership_public_visible    =='1'){ echo esc_attr('selected="selected"');}?>><?php _e('YES', 'users-control'); ?></option>
                            <option value="0" <?php if( $package->membership_public_visible    =='0'){ echo esc_attr('selected="selected"');}?>><?php _e('NO', 'users-control'); ?></option>                             
                           
                                       
             
           				   </select> - <?php _e("If NO, This subscription won't be visible on the public side.", 'users-control'); ?></td>        
                 </tr>
                 
                  <tr >
                    <td ><?php _e('Display Order', 'users-control'); ?></td>
                     <td width="76%"> <input name="userscontrol_display_order" id="userscontrol_display_order" value="<?php echo esc_attr($package->membership_order);?>" type="text" value="0"> - <?php _e('Input the display ordering for this subscription. Example: 1,2,3', 'users-control'); ?></td>        
                </tr>
                
                
                <tr >
                    <td ><?php _e('Status', 'users-control'); ?></td>
                     <td> <select name="userscontrol_status" id="userscontrol_status">
             				 <option value="1"  <?php if( $package->membership_status    =='1'){ echo esc_attr('selected="selected"');}?>><?php _e('Active', 'users-control'); ?></option>
                            <option value="0"  <?php if( $package->membership_status    =='0'){ echo esc_attr('selected="selected"');}?>><?php _e('Deactivated', 'users-control'); ?></option>                             
                           
                                       
             
           				   </select> - <?php _e("If Deactivated, This subscription won't be available on the website.", 'users-control'); ?></td>        
                 </tr>
                 
                  <?php if(isset($userscontrol_assignrole)){?>
                  <tr >
                    <td ><?php _e('Assign Role', 'users-control'); ?></td>
                     <td> 
                            <?php
							
							 $display = '';
							 
							 $allowed_user_roles = $userscontrol->role->get_available_user_roles();
                            
							 $display .= '<select name="userscontrol_subscription_role_to_assign" id="userscontrol_subscription_role_to_assign">';
							 $display .= '<option value="" >'.__('Do not assign role', 'users-control').'</option>';
         
		
							foreach ($allowed_user_roles as $key => $val) {
								$sel ="";
								if($package->membership_role_to_assign  ==$key){
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
                     <td width="76%"><?php $p =  $userscontrol->get_subscription_categories_admin($package);
                     echo wp_kses($p, $userscontrol->allowed_html);
                     ?> </td>        
                </tr>
                
                 <?php if(isset($userscontrol_onlyroles)){?>
                
                 	 <tr >
                  	  <td ><?php _e('Roles', 'users-control'); ?></td>
                   	  <td><?php $p =  $userscontrol->role->get_package_roles($package);
                      echo wp_kses($p, $userscontrol->allowed_html);?></td>  
                     
                           
                	</tr>
                <?php }?>

                

                
            </tbody>
        </table>
        
  <p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','users-control'); ?>"  />
</p>
      


</div>


</form>