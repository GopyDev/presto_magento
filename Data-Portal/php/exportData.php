<?php
//include database configuration file
include 'dbConfig.php';

$options = $_REQUEST['options'];
$dateRange=0;
    switch ($options) {
        case '1':
            # code...
            $dateRange = 365;
            break;
        case '2':
            # code...
            $dateRange = 90;
        break;
        case '3':
            # code...
            $dateRange = 30;
        break;
        default:
            # code...
            $dateRange = 365;
            break;
    }
$report = $_REQUEST['report'];


if($report == 1){
    //get records from database
    if($options !=4) {
        $query = $db->query('SELECT a.customer_firstname,a.customer_lastname,a.customer_email,b.street,b.city,b.postcode,count(*) as order_count, sum(a.subtotal_invoiced)
            as order_value, avg(a.subtotal_invoiced) as avg_value FROM (select tbl1.* from mg_sales_flat_order tbl1,(select a.entity_id, a.timediff from (SELECT entity_id, TIMESTAMPDIFF(DAY,updated_at,Now()) as timediff from mg_sales_flat_order) a 
            where a.timediff < "'.$dateRange.'") tbl2 where tbl1.entity_id = tbl2.entity_id) as a left join mg_sales_flat_order_address as b on a.entity_id = b.parent_id where b.address_type ="shipping" Group by a.customer_email ORDER BY a.entity_id ASC ');
    } else{
        $fromdate = $_REQUEST['fromdate'];
        $todate = $_REQUEST['todate'];
        $time = strtotime($fromdate);
        $fromdate = date('Y-m-d',$time);
        $time = strtotime($todate);
        $todate = date('Y-m-d',$time);

        $query = $db->query('SELECT a.customer_firstname,a.customer_lastname,a.customer_email,b.street,b.city,b.postcode,count(*) as order_count, sum(a.subtotal_invoiced)
        as order_value, avg(a.subtotal_invoiced) as avg_value FROM (select tbl1.* from mg_sales_flat_order tbl1,(select a.entity_id, a.timediff from (SELECT entity_id, updated_at as timediff from mg_sales_flat_order) a 
        where a.timediff < "'.$todate.'" and a.timediff > "'.$fromdate.'" ) tbl2 where tbl1.entity_id = tbl2.entity_id) as a left join mg_sales_flat_order_address as b on a.entity_id = b.parent_id where b.address_type ="shipping" Group by a.customer_email ORDER BY a.entity_id ASC ');
    }
    if($query->num_rows > 0){
        $delimiter = ",";
        if($options != 4){ 
        
            $nowDate = date('m-d-Y');
            $filename = "Recent_Customers_" . $dateRange . "_Day_Report_" . $nowDate . ".csv";
        } else {
            $fromdate = $_REQUEST['fromdate'];
            $todate = $_REQUEST['todate'];
            $time = strtotime($fromdate);
            $fromdate = date('m-d-Y',$time);
            $time = strtotime($todate);
            $todate = date('m-d-Y',$time);
            $filename = "Recent_Customers_" . $fromdate .  "_to_" . $todate . ".csv"; }
        
        //create a file pointer
        $f = fopen('php://memory', 'w');
        
        //set column headers
        $fields = array('First Name', 'Last Name', 'Email Address', 'Street Address','City','Zipcode','Total Orders ','Total Order Value','Average Order Value','Order1_Date','Order1_Value',
                'Order2_Date','Order2_Value','Order3_Date','Order3_Value','Order4_Date','Order4_Value','Order5_Date','Order5_Value');
        fputcsv($f, $fields, $delimiter);
        
        //output each row of the data, format line as csv and write to file pointer
        while($row = $query->fetch_assoc()){
            
            $lineData = array($row['customer_firstname'], $row['customer_lastname'], $row['customer_email'], $row['street'], $row['city'],$row['postcode'],
                $row['order_count'],$row['order_value'],$row['avg_value']);

            // get 5 recent orders.
            $i = 0 ;
            $order_date = array();
            $order_value = array();
            if($options != 4) {
                $sql1 = 'SELECT tbl1.updated_at, tbl1.subtotal_invoiced
                from mg_sales_flat_order tbl1,(select a.entity_id, a.timediff
                from (SELECT entity_id, TIMESTAMPDIFF(DAY,updated_at,Now()) as timediff from mg_sales_flat_order) a 
                where a.timediff < "'.$dateRange.'") tbl2 
                where tbl1.entity_id = tbl2.entity_id and tbl1.customer_email = "'.$row['customer_email'].'" ORDER BY updated_at DESC ';
            } else {
                $fromdate = $_REQUEST['fromdate'];
                $todate = $_REQUEST['todate'];
                $time = strtotime($fromdate);
                $fromdate = date('Y-m-d',$time);
                $time = strtotime($todate);
                $todate = date('Y-m-d',$time);
                $sql1 = 'SELECT tbl1.updated_at, tbl1.subtotal_invoiced
                from mg_sales_flat_order tbl1,(select a.entity_id, a.timediff
                from (SELECT entity_id, updated_at as timediff from mg_sales_flat_order) a 
                where a.timediff < "'.$todate.'" and a.timediff > "'.$fromdate.'") tbl2 
                where tbl1.entity_id = tbl2.entity_id and tbl1.customer_email = "'.$row['customer_email'].'" ORDER BY updated_at DESC ';
            }
        
            $query_rec_order = $db->query($sql1);
            if ($query_rec_order->num_rows > 0) {
                while($row_order = $query_rec_order->fetch_assoc()){
                    if($i<5){
                        array_push($lineData, $row_order['updated_at']);
                        array_push($lineData, $row_order['subtotal_invoiced']);
                        $i++;
                    } else break;
                }
            }   
            fputcsv($f, $lineData, $delimiter);
        }
        
        //move back to beginning of file
        fseek($f, 0);
        
        //set headers to download file rather than displayed
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');
        
        //output all remaining data on a file pointer
        fpassthru($f);
        } else {echo "Sorry, there isn't any available data for this query.";}
    exit;
}
else {

    if($options != 4){

        $query = $db->query('SELECT a.*, b.email as Email, b.created_at as RegistrationDate, c.value as Address
        from (
        select
          entity_id,
          max(case when attribute_id = 20 then value end) Firstname,
          max(case when attribute_id = 22 then value end) Lastname,
          max(case when attribute_id = 26 then value end) City,
          max(case when attribute_id = 28 then value end) State,
          max(case when attribute_id = 30 then value end) Zipcode,
          max(case when attribute_id = 31 then value end) Phone
        
        from mg_customer_address_entity_varchar
        group by entity_id) a inner join (select entity_id, email, created_at from mg_customer_entity where created_at > DATE_SUB( NOW() , INTERVAL "'.$dateRange.'" DAY ) and created_at < NOW() and entity_id not in (select distinct(customer_id) as id from mg_sales_flat_order where customer_id >= 0)) b on a.entity_id = b.entity_id left join mg_customer_address_entity_text c on b.entity_id = c.entity_id
        ');

    } else {

        $fromdate = $_REQUEST['fromdate'];
        $todate = $_REQUEST['todate'];
        $time = strtotime($fromdate);
        $fromdate = date('Y-m-d',$time);
        $time = strtotime($todate);
        $todate = date('Y-m-d',$time);
        $query = $db->query('SELECT a.*, b.email as Email, b.created_at as RegistrationDate, c.value as Address
        from (
        select
        entity_id,
        max(case when attribute_id = 20 then value end) Firstname,
        max(case when attribute_id = 22 then value end) Lastname,
        max(case when attribute_id = 26 then value end) City,
        max(case when attribute_id = 28 then value end) State,
        max(case when attribute_id = 30 then value end) Zipcode,
        max(case when attribute_id = 31 then value end) Phone
        
        from mg_customer_address_entity_varchar
        group by entity_id) a inner join (select entity_id, email, created_at from mg_customer_entity where created_at > "'.$fromdate.'" and created_at < "'.$todate.'" and entity_id not in (select distinct(customer_id) as id from mg_sales_flat_order where customer_id >= 0)) b on a.entity_id = b.entity_id left join mg_customer_address_entity_text c on b.entity_id = c.entity_id');
        }
        if($query->num_rows > 0){

            $delimiter = ",";
            if($options != 4){ 
        
                $nowDate = date('m-d-Y');
                $filename = "Registered_Customers_" . $dateRange . "_Day_Report_" . $nowDate . ".csv";
            } else {
                $fromdate = $_REQUEST['fromdate'];
                $todate = $_REQUEST['todate'];
                $time = strtotime($fromdate);
                $fromdate = date('m-d-Y',$time);
                $time = strtotime($todate);
                $todate = date('m-d-Y',$time);
                $filename = "Registered_Customers_" . $fromdate .  "_to_" . $todate . ".csv";  }
            
            //create a file pointer
            $f = fopen('php://memory', 'w');
            
            //set column headers
            $fields = array('First Name', 'Last Name','Customer Address', 'City', 'State','Zipcode','Phone','Email Address','Registration Date');
            fputcsv($f, $fields, $delimiter);
            
            //output each row of the data, format line as csv and write to file pointer
            while($row = $query->fetch_assoc()){
                
                $lineData = array($row['Firstname'], $row['Lastname'], $row['Address'],$row['City'], $row['State'], $row['Zipcode'],$row['Phone'],
                    $row['Email'],$row['RegistrationDate']);  
                fputcsv($f, $lineData, $delimiter);
            }
            
            //move back to beginning of file
            fseek($f, 0);
            
            //set headers to download file rather than displayed
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '";');
            
            //output all remaining data on a file pointer
            fpassthru($f);
            } else {echo "Sorry, there isn't any available data for this query.";}
        exit;
    
}

?>