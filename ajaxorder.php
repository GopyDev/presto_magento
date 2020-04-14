<?php

    require_once( '_db.config.inc.php' );

//connection to the database
$dbhandle = mysql_connect(DBHOSTNAME, DBUSERNAME, DBPASSWORD)
 or die("Unable to connect to MySQL");

//select a database to work with
$selected = mysql_select_db( DBDATABASE, $dbhandle)
  or die("Could not select examples");

         $select_cust_address="select entity_id from mg_sales_flat_order where customer_id='".$_GET["custid"]."' order by entity_id desc limit 0,1";
		  $rs_cust_address_id = mysql_query($select_cust_address);
		  $cust_address_id=mysql_fetch_array($rs_cust_address_id);

		   $current_address_id="select entity_id from mg_sales_flat_order_address where parent_id='".$_GET["oid"]."' and address_type='shipping' order by entity_id desc limit 0,1";
		   $rs_current_address_id=mysql_query($current_address_id);
		   $current_address_id_shipping=mysql_fetch_array($rs_current_address_id);


		    $select_cust_address2="select * from mg_sales_flat_order_address where parent_id='".$cust_address_id["entity_id"]."' and address_type='shipping' order by entity_id desc limit 0,1";
		   $rs_cust_address_id2 = mysql_query($select_cust_address2);
		   $cust_address_id2=mysql_fetch_array($rs_cust_address_id2);


		  $update_address="update mg_sales_flat_order_address set customer_address_id='".$cust_address_id2["customer_address_id"]."',quote_address_id='".$cust_address_id2["quote_address_id"]."',region_id='".$cust_address_id2["region_id"]."',customer_id='".$cust_address_id2["customer_id"]."',fax='".$cust_address_id2["fax="]."',postcode='".$cust_address_id2["postcode"]."',lastname='".$cust_address_id2["lastname"]."',street='".$cust_address_id2["street"]."',city='".$cust_address_id2["city"]."',telephone='".$cust_address_id2["telephone"]."',country_id='".$cust_address_id2["country_id"]."',address_type='".$cust_address_id2["address_type"]."',prefix='".$cust_address_id2["prefix"]."',middlename='".$cust_address_id2["middlename"]."',suffix='".$cust_address_id2["suffix"]."',company='".$cust_address_id2["company"]."',firstname='".$cust_address_id2["firstname"]."' where entity_id='".$current_address_id_shipping["entity_id"]."' and address_type='shipping'";

		  mysql_query($update_address);



		   $current_address_id="select entity_id from mg_sales_flat_order_address where parent_id='".$_GET["oid"]."' and address_type='billing' order by entity_id desc limit 0,1";
		   $rs_current_address_id=mysql_query($current_address_id);
		   $current_address_id_shipping=mysql_fetch_array($rs_current_address_id);


		    $select_cust_address2="select * from mg_sales_flat_order_address where parent_id='".$cust_address_id["entity_id"]."' and address_type='billing' order by entity_id desc limit 0,1";
		   $rs_cust_address_id2 = mysql_query($select_cust_address2);
		   $cust_address_id2=mysql_fetch_array($rs_cust_address_id2);


		  $update_address="update mg_sales_flat_order_address set customer_address_id='".$cust_address_id2["customer_address_id"]."',quote_address_id='".$cust_address_id2["quote_address_id"]."',region_id='".$cust_address_id2["region_id"]."',customer_id='".$cust_address_id2["customer_id"]."',fax='".$cust_address_id2["fax="]."',postcode='".$cust_address_id2["postcode"]."',lastname='".$cust_address_id2["lastname"]."',street='".$cust_address_id2["street"]."',city='".$cust_address_id2["city"]."',telephone='".$cust_address_id2["telephone"]."',country_id='".$cust_address_id2["country_id"]."',address_type='".$cust_address_id2["address_type"]."',prefix='".$cust_address_id2["prefix"]."',middlename='".$cust_address_id2["middlename"]."',suffix='".$cust_address_id2["suffix"]."',company='".$cust_address_id2["company"]."',firstname='".$cust_address_id2["firstname"]."' where entity_id='".$current_address_id_shipping["entity_id"]."' and address_type='billing'";

		  mysql_query($update_address);



	      $query = "update mg_sales_flat_order set customer_id='".$_GET["custid"]."' where entity_id='".$_GET["oid"]."'";
		  mysql_query($query);

		  $query = "update mg_sales_flat_order_grid set customer_id='".$_GET["custid"]."' where entity_id='".$_GET["oid"]."'";
		  mysql_query($query);

mysql_close();
?>