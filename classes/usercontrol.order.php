<?php
class UserscontrolOrder {
	var $pages;
	var $total_result;
	var $table_prefix = 'userscontrol';
	var $ajax_p = 'userscontrol';

	function __construct() 	{
		$this->ini_db();		

	}
	
	public function ini_db()
	{
		global $wpdb;			

		// Create table
		$query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . ''.$this->table_prefix.'_orders (
				`order_id` bigint(20) NOT NULL auto_increment,		
				`order_user_id` bigint(20) NOT NULL,		
				`order_subscription_id` bigint(20) NOT NULL,
				`order_method_name`  varchar(100) NOT NULL,				
				`order_key` varchar(250) NOT NULL,
				`order_txt_id` varchar(100) NOT NULL,
				`order_status` int(11) NOT NULL DEFAULT "0",
				`order_ini` int(11) NOT NULL DEFAULT "1",
				`order_type` int(1) NOT NULL DEFAULT "1",
				`order_amount` decimal(11,2) NOT NULL,
				`order_amount_subscription` decimal(11,2) NOT NULL,				
				`order_date` datetime NOT NULL,									 			
				PRIMARY KEY (`order_id`)
			) COLLATE utf8_general_ci;';
	
		$wpdb->query( $query );	
		
		$query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . ''.$this->table_prefix.'_subscriptions (
				`subscription_id` bigint(20) NOT NULL auto_increment,		
				`subscription_user_id` int(11) NOT NULL,
				`subscription_package_id` int(11) NOT NULL,				
				`subscription_status` int(11) NOT NULL DEFAULT "0",					
				`subscription_recurring` int(1) NOT NULL DEFAULT "0",
				`subscription_lifetime` int(1) NOT NULL DEFAULT "0",
				`subscription_has_trial` int(1) NOT NULL DEFAULT "0",	
				`subscription_key` varchar(250) NOT NULL,	
				`subscription_merchant_id` varchar(250) NOT NULL DEFAULT "0",
				`subscription_date` datetime NOT NULL,
				`subscription_date_gmt` int(3) NOT NULL DEFAULT "0",
				`subscription_cancellation_date` datetime NOT NULL,
				`subscription_start_date` datetime NOT NULL,
				`subscription_end_date` datetime NOT NULL,
				`subscription_end_trial_date` datetime NOT NULL,									 			
				PRIMARY KEY (`subscription_id`)
			) COLLATE utf8_general_ci;';

		$wpdb->query( $query );	
				
	}
	
	
	/*Create Order*/
	public function create_subscription ($subscription){
		global $wpdb,  $userscontrol;		
		extract($subscription);		

		//update database
		$query = "INSERT INTO " . $wpdb->prefix . ''.$this->table_prefix."_subscriptions (
		`subscription_user_id`,
		`subscription_package_id`, 
		`subscription_status`, 
		`subscription_lifetime` ,
		`subscription_recurring` ,		
		`subscription_key` , 
		`subscription_date`, `subscription_start_date` , `subscription_end_date`) VALUES 
		('$subscription_user_id','$subscription_package_id','$subscription_status',
		'$subscription_lifetime', '$subscription_recurring',
		 '$subscription_key', '$subscription_date',  
		 '$subscription_start_date' ,  '$subscription_end_date')";					
		$wpdb->query( $query );	
		return $wpdb->insert_id;					
						
	}
	

	/*Create Order*/
	public function create_order ($orderdata){
		global $wpdb,  $userscontrol;
		
		extract($orderdata);
		
		$query = "INSERT INTO " . $wpdb->prefix . ''.$this->table_prefix."_orders 
		(`order_user_id`,`order_subscription_id`, `order_method_name`, 
		 `order_key` ,`order_txt_id` , 
		 `order_status`, `order_date` , `order_amount` , `order_amount_subscription` , `order_ini`)
		 
		 VALUES ('$order_user_id','$order_subscription_id',
		 '$order_method_name','$order_key', 
		 '$order_txt_id', 
		 '$order_status',  '$order_date' ,  '$order_amount' ,  '$order_amount_subscription' ,  '$order_ini')";
				
		$wpdb->query( $query );	
		return $wpdb->insert_id;					
	}
	
	
	
	
	
	
	public function update_order_status ($id,$status){
		global $wpdb,  $userscontrol;
		
		//update database
		$query = "UPDATE " . $wpdb->prefix . $this->table_prefix."_orders SET order_status = '$status' WHERE order_id = '$id' ";
		$wpdb->query( $query );
	
	}
	
	public function update_subscription_status ($id,$status){
		global $wpdb,  $userscontrol;		
		//update database
		$query = "UPDATE " . $wpdb->prefix . $this->table_prefix."_subscriptions SET subscription_status = '$status' WHERE subscription_id= '$id' ";
		$wpdb->query( $query );	
	}
	
	public function update_subscription_expiration ($id,$starts, $ends)	{
		global $wpdb,  $userscontrol;
		
		//update database
		$query = "UPDATE " . $wpdb->prefix . $this->table_prefix."_subscriptions 
				SET subscription_start_date  = '$starts', subscription_end_date   = '$ends' 
				WHERE subscription_id= '$id' ";
		$wpdb->query( $query );	
	}
	
	public function update_subscription_cancelation_date ($id,$date){
		global $wpdb,  $userscontrol;		
		//update database
		$query = "UPDATE " . $wpdb->prefix . $this->table_prefix."_subscriptions SET subscription_cancellation_date = '$date' WHERE subscription_id= '$id' ";
		$wpdb->query( $query );
	
	}
	
	
	
	public function update_subscription_merchant_id ($id,$merchant_id){
		global $wpdb,  $userscontrol;		
		//update database
		$query = "UPDATE " . $wpdb->prefix . $this->table_prefix."_subscriptions SET subscription_merchant_id = '$merchant_id' WHERE subscription_id= '$id' ";
		$wpdb->query( $query );
	
	}
	
		
	public function update_expiration_date ($id,$expiration_date){
		global $wpdb,  $userscontrol;
		$query = "UPDATE " . $wpdb->prefix ."bup_orders SET order_expiration = '$expiration_date' WHERE order_id = '$id' ";
		$wpdb->query( $query );
	
	}
	
	public function update_order_payment_response ($id,$order_txt_id){
		global $wpdb,  $userscontrol;
		$query = "UPDATE " . $wpdb->prefix . $this->table_prefix ."_orders SET order_txt_id = '$order_txt_id' WHERE order_id = '$id' ";
		$wpdb->query( $query );
	}
	
	
	
	public function get_subscription_payments ($id){
		global $wpdb,  $userscontrol;		
		$sql = 'SELECT * FROM ' . $wpdb->prefix . $this->table_prefix . '_orders   ' ;			
		$sql .= " WHERE order_subscription_id = '".$id."' ORDER BY order_id DESC";	
		$orders = $wpdb->get_results($sql );
		return $orders ;		
	}
	
	public function get_subscription_payments_by_user ($user_id){
		global $wpdb,  $userscontrol;
		
		$sql = ' SELECT ord.*, sub.*, package.*, usu.* 
		FROM ' . $wpdb->prefix . $this->table_prefix . '_subscriptions sub  ' ;	
			
	    $sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prefix ."_orders ord ON (ord.order_subscription_id  = sub.subscription_id )";		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prefix ."_membership_packages package ON (package.membership_id = sub.subscription_package_id )";		
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = sub.subscription_user_id)";

		$sql .= " WHERE ord.order_subscription_id  = sub.subscription_id  ";	
	    $sql .= " AND package.membership_id = sub.subscription_package_id  ";
		$sql .= " AND usu.ID = sub.subscription_user_id ";	
		$sql .= " AND sub.subscription_user_id = '".$user_id."' ";		
		$sql .= " ORDER BY ord.order_id DESC";	
		$res = $wpdb->get_results($sql);
		return $res ;		
	}
	
	
	
	
	/*Get Order*/
	public function get_order ($id)
	{
		global $wpdb,  $userscontrol;
		
		$orders = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . $this->table_prefix . '_orders WHERE order_key = "'.$id.'" ' );
		
		if ( empty( $orders ) )
		{
		
		
		}else{
			
			
			foreach ( $orders as $order )
			{
				return $order;			
			
			}
			
		}
		
		
	
	}
	
	/*Get Order*/
	public function get_order_edit ($order_id , $booking_id){
		global $wpdb,  $userscontrol;		
		$orders = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . $this->table_prefix . '_orders WHERE order_id = "'.$order_id.'" AND order_subscription_id = "'.$booking_id.'" ' );
		if ( empty( $orders ) ){		
		
		}else{
			foreach ( $orders as $order ){
				return $order;			
			}
		}
	}
	
	/*This returns all the latest orders*/
	public function get_latest_orders ($howmany) {
		global $wpdb;
		
		$site_date = date( 'Y-m-d', current_time( 'timestamp', 0 ) );
		
		$membership_list = array();	
			  
		$sql = ' SELECT ord.*, sub.*, package.*, usu.* 
		FROM ' . $wpdb->prefix . $this->table_prefix . '_subscriptions sub  ' ;	
			
	    $sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prefix ."_orders ord ON (ord.order_subscription_id  = sub.subscription_id )";		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prefix ."_membership_packages package ON (package.membership_id = sub.subscription_package_id )";		
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = sub.subscription_user_id)";

		$sql .= " WHERE ord.order_subscription_id  = sub.subscription_id  ";	
	    $sql .= " AND package.membership_id = sub.subscription_package_id  ";
		$sql .= " AND usu.ID = sub.subscription_user_id ";			
		$sql .= " ORDER BY ord.order_id DESC";			
	   	$sql .= " LIMIT $howmany"; 
 
		$res = $wpdb->get_results($sql);
		
		return $res;	
	
	}
	
	public function get_sales_gross_total_by_day ($date) {
		global $wpdb, $userscontrol;		
		$site_date = date( 'Y-m-d', current_time( 'timestamp', 0 ) );		
					  
		$sql = ' SELECT (SUM(order_amount )  + SUM(order_amount_subscription )) as total
		FROM ' . $wpdb->prefix . $this->table_prefix . '_orders   ' ;		
		$sql .= " WHERE DATE(order_date)  = '".$date."'  ";				
		$res = $wpdb->get_results($sql);
		
		if ( empty( $res ) ){
			return$row->total;		
		}else{		
			
			foreach ( $res as $row ){
				if($row->total=='' || $row->total==null){
					return 0;		
				}else{
					return $row->total;
				}			
			}			
		}	
	}
	
	public function get_sales_total_by_day ($date) {
		global $wpdb, $userscontrol;
		
		$site_date = date( 'Y-m-d', current_time( 'timestamp', 0 ) );		
		$sql = ' SELECT (SUM(order_amount )  + SUM(order_amount_subscription )) as total
		FROM ' . $wpdb->prefix . $this->table_prefix . '_orders   ' ;		
		$sql .= " WHERE DATE(order_date)  = '".$date."'  ";				
		//$sql .= " AND order_ini   = '1'  ";	 
		$res = $wpdb->get_results($sql);
		
		if ( empty( $res ) ){
			return$row->total;		
		
		}else{		
			
			foreach ( $res as $row ){
				if($row->total=='' || $row->total==null){
					return 0;		
				}else{
					return $row->total;
				}			
			}			
		}	
	}
	
	public function get_sales_total_gross_by_month($month, $year) {
		global $wpdb, $userscontrol;
		
		$site_date = date( 'Y-m-d', current_time( 'timestamp', 0 ) );		
					  
		$sql = ' SELECT (SUM(order_amount )  + SUM(order_amount_subscription )) as total
		FROM ' . $wpdb->prefix . $this->table_prefix . '_orders   ' ;		
		$sql .= " WHERE MONTH(order_date)  = '".$month."' AND YEAR(order_date)  = '".$year."'  ";				
		$res = $wpdb->get_results($sql);
		
		if ( empty( $res ) )
		{
			return $row->total;		
		
		}else{
			
			
			foreach ( $res as $row )
			{
				if($row->total=='' || $row->total==null)
				{
					return 0;		
				}else{
					return $row->total;
				
				}			
			}			
		}	
	}
	
	public function get_sales_total ($when) 
	{
		global $wpdb, $userscontrol;
		
		$site_date = date( 'Y-m-d', current_time( 'timestamp', 0 ) );		
		if($when=='today')
		{
			 $date = date( 'Y-m-d', current_time( 'timestamp', 0 ) );
			 		
		}elseif($when=='week'){
			
			$dt_min = new DateTime("last sunday");
			$dt_max = clone($dt_min);
			$dt_max->modify('+6 days');
			
			$date_from =$dt_min->format('Y-m-d');
			$date_to =$dt_max->format('Y-m-d');
		
		}		
			  
		$sql = ' SELECT (SUM(order_amount )  + SUM(order_amount_subscription )) as total
		FROM ' . $wpdb->prefix . $this->table_prefix . '_orders   ' ;
		
		if($when=='today')
		{
			$sql .= " WHERE DATE(order_date)  = '".$date."'  ";
		
		}elseif($when=='week'){
			
			$sql .= " WHERE DATE(order_date) >= '".$date_from."' AND DATE(order_date) <= '".$date_to."'  ";
				
		}		
		$sql .= " AND order_ini   = '1'  ";	 
		$res = $wpdb->get_results($sql);	

		
		if ( empty( $res ) )
		{
			return $userscontrol->get_formated_amount_with_currency($row->total);		
		
		}else{
			
			
			foreach ( $res as $row )
			{
				if($row->total=='' || $row->total==null)
				{
					return $userscontrol->get_formated_amount_with_currency(0);		
				}else{
					return $userscontrol->get_formated_amount_with_currency($row->total);
				
				}			
			}			
		}	
	}
	
	public function get_gross_total ($when) 
	{
		global $wpdb, $userscontrol;
		
		$site_date = date( 'Y-m-d', current_time( 'timestamp', 0 ) );
		$site_month = date( 'm', current_time( 'timestamp', 0 ) );
		$site_year = date( 'Y', current_time( 'timestamp', 0 ) );
			
		if($when=='today')
		{
			 $date = date( 'Y-m-d', current_time( 'timestamp', 0 ) );
			 		
		}elseif($when=='week'){
			
			$dt_min = new DateTime("last sunday");
			$dt_max = clone($dt_min);
			$dt_max->modify('+6 days');
			
			$date_from =$dt_min->format('Y-m-d');
			$date_to =$dt_max->format('Y-m-d');
		
		}		
			  
		$sql = ' SELECT (SUM(order_amount )  + SUM(order_amount_subscription )) as total
		FROM ' . $wpdb->prefix . $this->table_prefix . '_orders   ' ;
		
		if($when=='today')
		{
			$sql .= " WHERE DATE(order_date)  = '".$date."'  ";
		
		}elseif($when=='week'){
			
			$sql .= " WHERE DATE(order_date) >= '".$date_from."' AND DATE(order_date) <= '".$date_to."'  ";
				
		
		
		}elseif($when=='month'){
			
			$sql .= " WHERE MONTH(order_date)  = '".$site_month."' AND YEAR(order_date)  = '".$site_year."'  ";	
						
		}	
			
		$res = $wpdb->get_results($sql);	
		
		if ( empty( $res ) )
		{
			return $userscontrol->get_formated_amount_with_currency($row->total);		
		
		}else{
			
			
			foreach ( $res as $row )
			{
				if($row->total=='' || $row->total==null)
				{
					return $userscontrol->get_formated_amount_with_currency(0);		
				}else{
					return $userscontrol->get_formated_amount_with_currency($row->total);
				
				}			
			}			
		}	
	}
	
	public function get_graph_total_monthly ()	{
		global $wpdb, $userscontrol;
		
		$date_format =  $userscontrol->get_int_date_format();		
		$days_of_month = date("t");		
		$day = 1; 
        
        $vals = "";

		while($day <= $days_of_month) {
			
			//get sales
			$date = date("Y").'-'.date("m").'-'.$day;
			
			//$date = date("Y").'-2-'.$day;
			
			$total = $this->get_sales_total_by_day($date);
			$total_formated = 	$userscontrol->get_formated_amount_with_currency($total);		
			$day_format =$day;			
			$vals .= "['".$day_format."', $total]";			
			$day++;
			
			if($day <= $days_of_month){
				
				$vals .= ',';		
			}
		} 
		
		return $vals;		
		
	}
	
	public function get_graph_total_daily () 
	{
		global $wpdb, $userscontrol;
		
		$date_format =  $userscontrol->get_int_date_format();		
		$days_of_month = date("t");		
		$day = 1; 
        
         $vals = "";

		while($day <= $days_of_month) {
			
			//get sales
			$date = date("Y").'-'.date("m").'-'.$day;
			
			$total = $this->get_sales_gross_total_by_day($date);
			$total_formated = 	$userscontrol->get_formated_amount_with_currency($total);		
			$day_format =$day;			
			$vals .= "['".$day_format."', $total]";			
			$day++;
			
			if($day <= $days_of_month){
				
				$vals .= ',';		
			}
		} 
		
		return $vals;		
		
	}
	
	public function get_graph_total_gross_by_month () 
	{
		global $wpdb, $wp_locale, $userscontrol;
		
		$date_format =  $userscontrol->get_int_date_format();		
		$current_year = date("Y");		
		$month = 1; 
        
        $vals = "";
        $day = "";
		
		$months_array =  array_values( $wp_locale->month_abbrev );

		while($month <= 12) {
			
			//get sales
			$date = date("Y").'-'.date("m").'-'.$day;
			
			$total = $this->get_sales_total_gross_by_month($month, $current_year);
			
			$total_formated = 	$userscontrol->get_formated_amount_with_currency($total);		
			$day_format =$months_array[$month-1];			
			$vals .= "['".$day_format."', $total]";			
			$month++;
			
			if($month <= 12){
				
				$vals .= ',';		
			}
		} 
		
		return $vals;		
		
	}
	
	
	
	/*Get all*/
	public function get_all_filtered ()
	{
		global $wpdb,  $userscontrol;
		
			
		$keyword = "";
		$month = "";
		$day = "";
		$year = "";
		$howmany = "";
		$ini = "";
        
        $bp_status = "";
		
		$special_filter='';
		
		if(isset($_GET["bp_keyword"]))
		{
			$keyword = sanitize_text_field($_GET["bp_keyword"]);		
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
		
		if(isset($_GET["howmany"]))
		{
			$howmany = sanitize_text_field($_GET["howmany"]);		
		}
		
		if(isset($_GET["bp_special_filter"]))
		{
			$special_filter = sanitize_text_field($_GET["bp_special_filter"]);		
		}
		
		if(isset($_GET["bp_status"]))
		{
			$bp_status = sanitize_text_field($_GET["bp_status"]);		
		}
		
		if(isset($_GET["bp_sites"]))
		{
			$bp_sites = sanitize_text_field($_GET["bp_sites"]);		
		}
		
		
				
		$uri= sanitize_url($_SERVER['REQUEST_URI'] );
		$url = explode("&ini=",$uri);
		
		if(is_array($url ))	{
			if(isset($url["1"])){
				$ini = $url["1"];
			    if($ini == ""){$ini=1;}
			}
		}		
		
		if($howmany == ""){$howmany=50;}
			  
		$sql = ' SELECT count(*) as total, ord.*, sub.*, package.*, usu.* 
		FROM ' . $wpdb->prefix . $this->table_prefix . '_subscriptions sub  ' ;	
			
	    $sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prefix ."_orders ord ON (ord.order_subscription_id  = sub.subscription_id )";		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prefix ."_membership_packages package ON (package.membership_id = sub.subscription_package_id )";		
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = sub.subscription_user_id)";

		$sql .= " WHERE ord.order_subscription_id  = sub.subscription_id  ";	
	    $sql .= " AND package.membership_id = sub.subscription_package_id  ";
		$sql .= " AND usu.ID = sub.subscription_user_id ";				
		
		
		if($bp_status !='')	
		{
			//display only ticket assigned to the staff'de partment			
			$sql .= " AND ord.order_status = '".$bp_status."' ";
			
		}
		
		
		
		if($keyword !='')	
		{				
			$sql .= " AND (usu.display_name LIKE '%".$keyword."%' OR ord.order_txt_id   LIKE '%".$keyword."%')";
			
		}
		
		
		
		
		if($day!=""){$sql .= " AND DAY(ord.order_date) = '$day'  ";	}
		if($month!=""){	$sql .= " AND MONTH(ord.order_date) = '$month'  ";	}		
		if($year!=""){$sql .= " AND YEAR((ord.order_date) = '$year'";}	
		
		$orders = $wpdb->get_results($sql );
		$orders_total = $this->fetch_result($orders);
		$orders_total = $orders_total->total;
		$this->total_result = $orders_total ;
		
		$total_pages = $orders_total;
				
		$limit = "";
		$current_page = $ini;
		$target_page =  site_url()."/wp-admin/admin.php?page=userscontrolember&tab=orders";
		
		$how_many_per_page =  $howmany;
		
		$to = $how_many_per_page;
		
		//caluculate from
		$from = $this->calculate_from($ini,$how_many_per_page,$orders_total );
		
		//get all			
			  
		$sql = ' SELECT ord.*, sub.*, package.*, usu.* 
		FROM ' . $wpdb->prefix . $this->table_prefix . '_subscriptions sub  ' ;	
			
	    $sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prefix ."_orders ord ON (ord.order_subscription_id  = sub.subscription_id )";		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prefix ."_membership_packages package ON (package.membership_id = sub.subscription_package_id )";		
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = sub.subscription_user_id)";

		$sql .= " WHERE ord.order_subscription_id  = sub.subscription_id  ";	
	    $sql .= " AND package.membership_id = sub.subscription_package_id  ";
		$sql .= " AND usu.ID = sub.subscription_user_id ";				
		
		if($bp_status !='')	{
			//display only ticket assigned to the staff'de partment			
			$sql .= " AND ord.order_status = '".$bp_status."' ";
		}
		
		if($keyword !=''){				
			$sql .= " AND (usu.display_name LIKE '%".$keyword."%' OR ord.order_txt_id   LIKE '%".$keyword."%')";
			
		}
		
		if($day!=""){$sql .= " AND DAY(ord.order_date) = '$day'  ";	}
		if($month!=""){	$sql .= " AND MONTH(ord.order_date) = '$month'  ";	}		
		if($year!=""){$sql .= " AND YEAR(ord.order_date) = '$year'";}	
		
		$sql .= " ORDER BY ord.order_id DESC";		
		
	    if($from != "" && $to != ""){	$sql .= " LIMIT $from,$to"; }
	 	if($from == 0 && $to != ""){	$sql .= " LIMIT $from,$to"; }
		$rows = $wpdb->get_results($sql );
		return $rows ;
	}
	
	public function calculate_from($ini, $howManyPagesPerSearch, $total_items)	
	{
		if($ini == ""){$initRow = 0;}else{$initRow = $ini;}
		
		if($initRow<= 1) 
		{
			$initRow =0;
		}else{
			
			if(($howManyPagesPerSearch * $ini)-$howManyPagesPerSearch>= $total_items) {
				$initRow = $totalPages-$howManyPagesPerSearch;
			}else{
				$initRow = ($howManyPagesPerSearch * $ini)-$howManyPagesPerSearch;
			}
		}
		
		
		return $initRow;
		
		
	}
	
	public function fetch_result($results)
	{
		if ( empty( $results ) )
		{
		
		
		}else{
			
			
			foreach ( $results as $result )
			{
				return $result;			
			
			}
			
		}
		
	}
	
	public function get_order_pending ($id)
	{
		global $wpdb,  $userscontrol;
		
		$sql = 'SELECT * FROM ' .  $wpdb->prefix . $this->table_prefix.'_orders WHERE order_key = "'.$id.'"  AND order_status="0" ' ;
		
		$orders = $wpdb->get_results($sql );		
		if ( empty( $orders ) )
		{
		
		
		}else{			
			
			foreach ( $orders as $order )
			{
				return $order;			
			
			}
			
		}
		
	
	}
	
	public function get_subscription_merchant ($id)
	{
		global $wpdb,  $userscontrol;
		
		$sql = 'SELECT * FROM ' .  $wpdb->prefix . $this->table_prefix.'_subscriptions WHERE subscription_merchant_id = "'.$id.'"   ' ;
		
		$orders = $wpdb->get_results( $sql);		
		if ( empty( $orders ) )
		{
		
		
		}else{			
			
			foreach ( $orders as $order )
			{
				return $order;			
			
			}
			
		}
		
	
	}
	
	public function get_subscription ($id)
	{
		global $wpdb,  $userscontrol;
		
		$sql = 'SELECT * FROM ' .  $wpdb->prefix . $this->table_prefix.'_subscriptions WHERE subscription_id = "'.$id.'"   ' ;
		
		$orders = $wpdb->get_results( $sql);		
		if ( empty( $orders ) )
		{
		
		
		}else{			
			
			foreach ( $orders as $order )
			{
				return $order;			
			
			}
			
		}
		
	
	}
	
	public function get_subscription_with_key ($id)
	{
		global $wpdb,  $userscontrol;
		
		$sql = 'SELECT * FROM ' .  $wpdb->prefix . $this->table_prefix.'_subscriptions WHERE subscription_key = "'.$id.'"   ' ;
		
		$orders = $wpdb->get_results( $sql);		
		if ( empty( $orders ) )
		{
		
		
		}else{			
			
			foreach ( $orders as $order )
			{
				return $order;			
			
			}
			
		}
		
	
	}
	
	public function get_subscription_with_merchant_id ($id)
	{
		global $wpdb,  $userscontrol;
		
		$sql = 'SELECT * FROM ' .  $wpdb->prefix . $this->table_prefix.'_subscriptions WHERE subscription_merchant_id = "'.$id.'"   ' ;
		
		$orders = $wpdb->get_results( $sql);		
		if ( empty( $orders ) )
		{
		
		
		}else{			
			
			foreach ( $orders as $order )
			{
				return $order;			
			
			}
			
		}
		
	
	}
	
	public function get_orders_by_status ($status)
	{
		global $wpdb,  $userscontrol;
		
		$rows = $wpdb->get_results( 'SELECT count(*) as total FROM ' . $wpdb->prefix . $this->table_prefix . '_orders WHERE order_status="'.$status.'" ' );	
		
		
		if ( empty( $rows ) )
		{
		
		
		}else{
			
			
			foreach ( $rows as $order )
			{
				return $order->total;			
			
			}
			
		}
				
		
	
	}
	
	public function get_order_confirmed ($id)
	{
		global $wpdb,  $userscontrol;
		
		$orders = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . $this->table_prefix . '_orders WHERE order_key = "'.$id.'"  AND order_status="1" ' );
		
		if ( empty( $orders ) )
		{
		
		
		}else{
			
			
			foreach ( $orders as $order )
			{
				return $order;			
			
			}
			
		}
		
	
	}
	
	
	
	/**
	 * My Orders 
	 */
	function show_my_latest_orders($howmany, $status=null){
		global $wpdb, $current_user, $userscontrol; 
		$currency_symbol =  $xoouserultra->get_option('paid_membership_symbol');
		$user_id = get_current_user_id();
        $drOr = $this->get_latest_user($user_id,30);
		
		if (  empty( $drOr) ){
			$message = '<p>'. __( 'You have no orders.', 'users-control' ). '</p>';
			echo wp_kses($message, $userscontrol->allowed_html);
		}else{
			$n = count( $drOr );
			
			
			?>
			<form action="" method="get">
				<?php wp_nonce_field( 'usersultra-bulk-action_inbox' ); ?>
				<input type="hidden" name="page" value="usersultra_inbox" />
	
				
	
				<table class="widefat fixed" id="table-3" cellspacing="0">
					<thead>
					<tr>
						
                       
						<th class="manage-column" ><?php _e( 'Order #', 'users-control' ); ?></th>
                        <th class="manage-column"><?php _e( 'Total', 'users-control' ); ?></th>
						<th class="manage-column"><?php _e( 'Date', 'users-control' ); ?></th>
						<th class="manage-column" ><?php _e( 'Package', 'users-control' ); ?></th>
                        <th class="manage-column" ><?php _e( 'Status', 'users-control' ); ?></th>
					</tr>
					</thead>
					<tbody>
						<?php
							
							foreach ( $drOr as $order){
							$order_id = $order->order_id;
							
							//get package
							
							$package = $xoouserultra->paypal->get_package($order->order_package_id);
							
							
							//print_r($order );
							
							?>
						<tr>
							                         
                            
							<td>#<?php echo esc_attr($order_id); ?></td>
                            <td><?php echo  esc_attr($currency_symbol.$order->order_amount)?></td>
							<td> <?php echo esc_attr($order->order_date); ?></td>
							<td><?php echo esc_attr($package->package_name); ?></td>
                            <td><?php echo esc_attr($order->order_status); ?></td>
                            
                            
							<?php
	
							}
						?>
					</tbody>
					
				</table>
			</form>
			<?php
	
		}
		?>

	<?php
	}
	
	
	

}
$key = "order";
$this->{$key} = new UserscontrolOrder();