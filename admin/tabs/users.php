<?php
global $userscontrol, $wp_locale;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$how_many_upcoming_app = 20;


$howmany = 5;

$currency_symbol =  $userscontrol->get_option('paid_membership_symbol');
$date_format =  $userscontrol->get_int_date_format();
$time_format =  $userscontrol->get_time_format();
$datetime_format =  $userscontrol->get_date_to_display();


$last_subscriptions = $userscontrol->membership->get_latest_subscriptions(5);
$latest_orders = $userscontrol->order->get_latest_orders(5);

$sales_today = $userscontrol->order->get_sales_total('today');
$sales_week = $userscontrol->order->get_sales_total('week');

$howmany = "";
$year = "";
$month = "";
$day = "";
$special_filter = "";
$bup_staff_calendar = "";

$bp_status ="";
$bp_keyword ="";



if(isset($_GET["howmany"]))
{
  $howmany = sanitize_text_field($_GET["howmany"]);		
}

if(isset($_GET["bp_month"]))
{
  $month = sanitize_text_field($_GET["bp_month"]);		
}

if(isset($_GET["bp_day"]))
{
  $day = sanitize_text_field($_GET["bp_day"]);		
}

if(isset($_GET["bp_year"]))
{
  $year = sanitize_text_field($_GET["bp_year"]);		
}

if(isset($_GET["bp_status"]))
{
  $bp_status = sanitize_text_field($_GET["bp_status"]);		
}

if(isset($_GET["special_filter"]))
{
  $special_filter = sanitize_text_field($_GET["special_filter"]);		
}

if(isset($_GET["bp_sites"]))
{
  $bp_sites = sanitize_text_field($_GET["bp_sites"]);		
}

if(isset($_GET["bp_keyword"]))
{
  $bp_keyword = sanitize_text_field($_GET["bp_keyword"]);		
}

$all_usu =$userscontrol->user->get_all_filtered();

        
?>

<div class="userscontrol-welcome-panel">

<h1><?php _e('MEMBERS','users-control')?></h1>

	<h2><?php _e('All members','users-control')?> <span class="userscontrol-widget-backend-colspan"><a href="#" title="<?php _e('Close','users-control')?> " class="userscontrol-widget-home-colapsable" widget-id="0"><i class="fa fa-sort-asc" id="userscontrol-close-open-icon-0"></i></a></span></h2>
    
     <div class="userscontrol-main-sales-summary" id="userscontrol-main-cont-home-0">  
     
     
      <div class="userscontrol-tickets-module-filters">
         
          <form action="" method="get">
         <input type="hidden" name="page" value="userscontrol" />
         <input type="hidden" name="tab" value="subscriptions" />
         
         
          <input type="text" name="bp_keyword" id="bp_keyword" value="<?php echo esc_attr($bp_keyword)?>" placeholder="<?php _e('input some text here','users-control'); ?>" />
          
         
              <select name="bp_month" id="bp_month">
               <option value="" selected="selected"><?php _e('All Months','users-control'); ?></option>
               <?php
			  
			  $i = 1;
              
			  while($i <=12){
			  ?>
               <option value="<?php echo esc_attr($i)?>"  <?php if($i==$month) echo esc_attr('selected="selected"');?>><?php echo esc_attr($i)?></option>
               <?php 
			    $i++;
			   }?>
             </select>
             
             <select name="bp_day" id="bp_day">
               <option value="" selected="selected"><?php _e('All Days','users-control'); ?></option>
               <?php
			  
			  $i = 1;
              
			  while($i <=31){
			  ?>
               <option value="<?php echo esc_attr($i)?>"  <?php if($i==$day) echo esc_attr('selected="selected"');?>><?php echo esc_attr($i)?></option>
               <?php 
			    $i++;
			   }?>
             </select>
             
             <select name="bp_year" id="bp_year">
               <option value="" selected="selected"><?php _e('All Years','users-control'); ?></option>
               <?php
			  
			  $i = 2014;
              
			  while($i <=2020){
			  ?>
               <option value="<?php echo esc_attr($i)?>" <?php if($i==$year) echo esc_attr('selected="selected"');?> ><?php echo esc_attr( $i)?></option>
               <?php 
			    $i++;
			   }?>
             </select>
                
           
                       
            <select name="howmany" id="howmany">
               <option value="50" <?php if(50==$howmany ||$howmany =="" ) echo esc_attr('selected="selected"');?>>50 <?php _e('Per Page','users-control'); ?></option>
                <option value="80" <?php if(80==$howmany ) echo esc_attr('selected="selected"');?>>80 <?php _e('Per Page','users-control'); ?></option>
                 <option value="100" <?php if(100==$howmany ) echo esc_attr('selected="selected"');?>>100 <?php _e('Per Page','users-control'); ?></option>
                  <option value="150" <?php if(150==$howmany ) echo esc_attr('selected="selected"');?>>150 <?php _e('Per Page','users-control'); ?></option>
                   <option value="200" <?php if(200==$howmany ) echo esc_attr('selected="selected"');?>>200 <?php _e('Per Page','users-control'); ?></option>
               
          </select>
          
                       <button name="userscontrol-btn-ticket-filter-appo" id="userscontrol-btn-ticket-filter-appo" class="userscontrol-button-submit-filter" type="submit"><?php _e('Filter','users-control')?>	</button>
                               
                
            
        
        
         </form>
         
                 
         
         </div>
           
         
             	
     
     </div>
     
     <div class="userscontrol-main-sales-summary" > 
     
      <?php	if (!empty($all_usu)){ ?>
       
           <table width="100%" class="">
            <thead>
                <tr>
                    <th width="4%" ><?php _e('#', 'users-control'); ?></th>                    
                    <th width="8%"><?php _e('Registration Date', 'users-control'); ?></th>                    
                     <th width="8%"><?php _e('Name', 'users-control'); ?></th>
                      <th width="8%"><?php _e('Username', 'users-control'); ?></th>
                       <th width="8%"><?php _e('Email', 'users-control'); ?></th>
                                    
                     <th width="10%" ><?php _e('Subscriptions', 'users-control'); ?></th>
                    <th width="5%"><?php _e('Actions', 'users-control'); ?></th>
                </tr>
            </thead>
            
            <tbody>
            
            <?php 
			$filter_name= '';
			$phone= '';
			foreach($all_usu as $user) {
				
				$date_created=  date($date_format, strtotime($user->user_registered ));
				
				//howmany subsriptions
				
		
				
			?>
              

                <tr>
                    <td ><a href="?page=userscontrolembers&tab=users-edit&id=<?php esc_attr($user->ID)?>" class="userscontrol-appointment-edit-module"  title="<?php _e('Edit','users-control'); ?>"><?php echo esc_attr($user->ID); ?></a></td> 
                    
                     <td><?php echo esc_attr($date_created); ?>   </td> 
                      <td ><?php echo esc_attr($user->display_name); ?> </td> 
                      <td ><?php echo esc_attr($user->user_login); ?> </td> 
                      <td ><?php echo esc_attr($user->user_email); ?> </td>   
                                               
                      <td ><a href="#" class="userscontrol-user-subscriptions-module" user-id=<?php echo esc_attr($user->ID)?>  title="<?php _e('See Subscriptions','users-control'); ?>"><?php echo esc_attr($user->ID); ?></a></td>
                      <td> <a href="?page=userscontrol&tab=users-edit&id=<?php echo esc_attr($user->ID)?>" class="userscontrol-appointment-edit-module"  title="<?php _e('Edit','users-control'); ?>"><i class="fa fa-edit"></i></a>
                   
                                 
                   
                   </td>
                </tr>
                
                
                <?php
					}
					
					} else {
			?>
			<p><?php _e("There are no recent subscriptions subscriptions",'users-control'); ?></p>
			<?php	} ?>

            </tbody>
        </table>   
         
             	
     
     </div>
     
      
    
 
</div>

