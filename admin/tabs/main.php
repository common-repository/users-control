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

/*Gross sales*/

$gross_today = $userscontrol->order->get_gross_total('today');
$gross_week = $userscontrol->order->get_gross_total('week');
$gross_month = $userscontrol->order->get_gross_total('month');

//active subscriptions
$active_subscriptions_total = $userscontrol->membership->get_all_active_memberships();
$expired_subscriptions_total = $userscontrol->membership->get_all_expired_memberships();


$result_users = count_users();
$total_members =$result_users['total_users'];

//total subscriptions plan
$total_subscriptions_plan = $userscontrol->membership->get_total_active_subscription_plans();
echo wp_kses($this->display_warning_messages(), $userscontrol->allowed_html);
        
?>

<div class="userscontrol-welcome-panel">


	<h2><?php _e('Sales Summary','users-control')?> <span class="userscontrol-widget-backend-colspan"><a href="#" title="<?php _e('Close','users-control')?> " class="userscontrol-widget-home-colapsable" widget-id="0"><i class="fa fa-sort-asc" id="userscontrol-close-open-icon-0"></i></a></span></h2>
    
     <div class="userscontrol-main-sales-summary" id="userscontrol-main-cont-home-0">
     
     
          <div class="userscontrol-main-dashcol-1" >
          	 <div id='userscontrol-gcharthome' style="width: 100%; height: 180px;">
          	 </div>
          </div>
          
          <div class="userscontrol-main-dashcol-2" >
          	
            
             <div class="userscontrol-main-quick-summary" >
          
         	   <ul>
                   <li>                    
                     
                      <p style="color: #3C0"> <?php echo esc_attr($sales_today)?></p>  
                       <small><?php _e('Today','users-control')?> </small>                  
                    </li>
                
                	<li>                    
                     
                      <p style="color:"> <?php echo esc_attr($sales_week)?></p> 
                       <small><?php _e('This Week','users-control')?> </small>                   
                    </li>
              </ul>
              
            </div>
          
          </div>
          
          <div class="userscontrol-main-dashcol-3" >
     
     		 <div class="userscontrol-main-ticket-summary" >
             
             	<ul>
                
                   <li>                    
                      <small><?php _e('Members','users-control')?> </small>
                      <p style="color: #333"> <?php echo esc_attr($total_members)?></p>                    
                    </li>
                
                	<li>                    
                      <small><?php _e('Active Subscriptions','users-control')?> </small>
                      <p style="color:"> <?php echo esc_attr($active_subscriptions_total)?></p>                    
                    </li>
                    
                    <li> 
                    
                    <a href="#" title="<?php _e('Open','users-control')?>">                   
                      <small><?php _e('Subscription Plans','users-control')?> </small>
                      <p style="color:"> <?php echo esc_attr($total_subscriptions_plan)?></p>  
                      
                      </a>                  
                    </li>
                    
                    <li>     
                    
                       <a href="#" title="<?php _e('Pending','users-control')?>">               
                      <small><?php _e('Expired','users-control')?> </small>
                      <p style="color: #F90000"> <?php echo esc_attr($expired_subscriptions_total)?></p> 
                      
                       </a>                     
                    </li>
                    
                                  
                </ul>             
             </div>
             
            
             </div>
     
     	
     
     </div>
     
      
    
 <div class="userscontrol-main-blocksec" >
 
     <div class="userscontrol-main-2-col-1" >
	<h2><?php _e('Latest Subscriptions','users-control')?> <span class="userscontrol-widget-backend-colspan"><a href="#" title="<?php _e('Close','users-control')?> " class="userscontrol-widget-home-colapsable" widget-id="1"><i class="fa fa-sort-asc" id="userscontrol-close-open-icon-1"></i></a></span></h2>
    	 <div class="userscontrol-main-app-list" id="userscontrol-main-cont-home-1"> 
        
        <?php	if (!empty($last_subscriptions)){ ?>
       
           <table width="100%" class="">
            <thead>
                <tr>
                    <th width="4%" ><?php _e('#', 'users-control'); ?></th>
                    <th width="13%"><?php _e('Started', 'users-control'); ?></th> 
                    <th width="13%"><?php _e('Member', 'users-control'); ?></th> 
                     <th width="13%"><?php _e('Name', 'users-control'); ?></th>
                     <th width="14%"><?php _e('Valid From', 'users-control'); ?></th>
                    <th width="14%"><?php _e('Valid To', 'users-control'); ?></th>                   
                     <th width="14%" ><?php _e('Status', 'users-control'); ?></th>
                    <th width="5%"><?php _e('Actions', 'users-control'); ?></th>
                </tr>
            </thead>
            
            <tbody>
            
            <?php 
			$filter_name= '';
			$phone= '';
			foreach($last_subscriptions as $subscription) {
				$date_created=  date($date_format, strtotime($subscription->subscription_date ));
				$date_from=  date($date_format, strtotime($subscription->subscription_start_date  ));
				$date_to=  date($date_format, strtotime($subscription->subscription_end_date  ));
				
				if($subscription->subscription_lifetime==1)	{
					$date_to=__("Lifetime",'users-control');
				}

                $status_legend = $userscontrol->membership->get_subscription_status_legend($subscription->subscription_status);
			?>
              

                <tr>
                    <td ><?php echo esc_attr($subscription->subscription_id); ?></td>
                     <td><?php echo esc_attr($date_created); ?>   </td> 
                      <td ><?php echo esc_attr($subscription->display_name); ?> </td>    
                      <td ><?php echo esc_attr($subscription->membership_name); ?> </td>
                      <td ><?php echo esc_attr($date_from); ?></td>
                      <td><?php echo esc_attr($date_to); ?> </td>                      
                      <td ><?php echo wp_kses($status_legend, $userscontrol->allowed_html); ?></td>
                      <td> <a href="?page=userscontrol&tab=subscriptions-edit&id=<?php echo esc_attr($subscription->subscription_id)?>" class="userscontrol-appointment-edit-module"  title="<?php _e('Edit','users-control'); ?>"><i class="fa fa-edit"></i></a>
                   
                                 
                   
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
        
        <h2><?php _e('Latest Payments','users-control')?> <span class="userscontrol-widget-backend-colspan"><a href="#" title="<?php _e('Close','users-control')?> " class="userscontrol-widget-home-colapsable" widget-id="32"><i class="fa fa-sort-asc" id="userscontrol-close-open-icon-32"></i></a></span></h2>
    	 <div class="userscontrol-main-app-list" id="userscontrol-main-cont-home-32"> 
        
       <?php	if (!empty($latest_orders)){ ?>
       
           <table width="100%" class="">
            <thead>
                <tr>
                    <th width="5%" ><?php _e('#', 'users-control'); ?></th>
                    <th width="14%"><?php _e('Date', 'users-control'); ?></th> 
                    <th width="8%"><?php _e('Method', 'users-control'); ?></th>
                    <th width="13%"><?php _e('Transaction ID', 'users-control'); ?></th>
                     <th width="5%" ><?php _e('Subscription', 'users-control'); ?></th>
                    <th width="12%"><?php _e('Amount', 'users-control'); ?></th>
                   
                   
                </tr>
            </thead>
            
            <tbody>
            
            <?php 
			$filter_name= '';
			$phone= '';
			$i = 0;
			foreach($latest_orders as $payment) {
				
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
                    <td ><?php echo esc_attr($payment->order_id); ?></td>
                     
                     <td><?php echo esc_attr($date_created); ?>   </td>     
                      <td ><?php echo esc_attr($payment->order_method_name); ?> </td>
                      <td ><?php echo esc_attr($payment->order_txt_id); ?></td> 
                      <td ><?php echo esc_attr($payment->order_subscription_id); ?> </td>                                      
                      <td ><?php echo esc_attr($amount); ?></td>
                      
                </tr>
                
                
                <?php
					}
					
					} else {
			?>
			<p><?php _e("There are no recent payments",'users-control'); ?></p>
			<?php	} ?>

            </tbody>
        </table>
        
        </div>
        
         </div>
         
         
         <div class="userscontrol-main-2-col-2" >
            	<h2><?php _e('Gross Incomes Summary','users-control')?> <span class="userscontrol-widget-backend-colspan"><a href="#" title="<?php _e('Close','users-control')?> " class="userscontrol-widget-home-colapsable" widget-id="6"><i class="fa fa-sort-asc" id="userscontrol-close-open-icon-6"></i></a></span></h2>
                <div class="userscontrol-main-app-list" id="userscontrol-main-cont-home-6">
                
                
                    <div class="userscontrol-income" >
              
                       <ul>
                           <li>                    
                             
                              <p style="color: #333"> <?php echo esc_attr($gross_today)?></p>  
                               <small><?php _e('Today Incomes','users-control')?> </small>                  
                            </li>
                        
                            <li>                    
                             
                              <p style="color:"> <?php echo esc_attr($gross_week)?></p> 
                               <small><?php _e('This Week Incomes','users-control')?> </small>                   
                            </li>
                            
                             <li>                    
                             
                              <p style="color:"> <?php echo esc_attr($gross_month)?></p> 
                               <small><?php _e('This Month Incomes','users-control')?> </small>                   
                            </li>
                            
                      </ul>
                  
                  </div>
                  
                   <div id='userscontrol-grossdaily' style="width: 100%; height: 180px;">
          		 </div>
                  
                   <div id='userscontrol-grossmonthly' style="width: 100%; height: 180px;">
          		 </div>
                 
                  
             
             
                </div>
                
                
          </div>    
         
         </div>
        

</div>

<?php

$sales_val= $userscontrol->order->get_graph_total_monthly();
$sales_val_daily= $userscontrol->order->get_graph_total_daily();


$sales_gross_monthly_val= $userscontrol->order->get_graph_total_gross_by_month();

$months_array = array_values( $wp_locale->month );
$current_month = date("m");
$current_month_legend = $months_array[$current_month -1];

?>

<script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
		  
        var data = google.visualization.arrayToDataTable([
          ["<?php _e('Day','users-control')?>", "<?php _e('Sales','users-control')?>"],
         <?php echo wp_kses($sales_val,  $userscontrol->allowed_html)?>
        ]);

        var options = {
        
          hAxis: {title: '<?php printf(__( 'Month: %s', 'users-control' ),
    $current_month_legend);?> ',  titleTextStyle: {color: '#333'},  textStyle: {fontSize: '9'}},
          vAxis: {minValue: 0},		 
		  legend: { position: "none" }
        };

        var chart_1 = new google.visualization.AreaChart(document.getElementById('userscontrol-gcharthome'));
        chart_1.draw(data, options);
		
		//gross montlhly sales
		 var data = google.visualization.arrayToDataTable([
          ["<?php _e('Day','users-control')?>", "<?php _e('Sales','users-control')?>"],
         <?php echo wp_kses($sales_gross_monthly_val,  $userscontrol->allowed_html)?>
        ]);

        var options = {
		  title: "<?php _e('Current Month Gross Sales','users-control')?>",        
          hAxis: {title: '<?php printf(__( 'Year: %s', 'users-control' ),
    date("Y"));?> ',  titleTextStyle: {color: '#333'},  textStyle: {fontSize: '9'}},
          vAxis: {minValue: 0},		 
		  legend: { position: "none" }
        };

        var chart_2 = new google.visualization.AreaChart(document.getElementById('userscontrol-grossmonthly'));
        chart_2.draw(data, options);
		
		
		//gross daily sales		
		 var data = google.visualization.arrayToDataTable([
          ["<?php _e('Day','users-control')?>", "<?php _e('Sales','users-control')?>"],

         <?php echo wp_kses($sales_val_daily,  $userscontrol->allowed_html)?>
        ]);

        var options = {
			
		 title: "<?php _e('Current Month Daily Gross Sales','users-control')?>", 
        
          hAxis: {title: '<?php printf(__( '%s', 'users-control' ),
    $current_month_legend);?> ',  titleTextStyle: {color: '#333'},  textStyle: {fontSize: '8'}},
          vAxis: {minValue: 0},		 
		  legend: { position: "none" }
        };

        var chart_3 = new google.visualization.AreaChart(document.getElementById('userscontrol-grossdaily'));
        chart_3.draw(data, options);
		
		
		
		
		
		
      }
    </script>

     
