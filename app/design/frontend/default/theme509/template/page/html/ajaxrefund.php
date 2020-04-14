<?php

    require_once( Mage::getBaseDir('base').'/_db.config.inc.php' );
    $con = mysql_connect(DBHOSTNAME, DBUSERNAME, DBPASSWORD);
    $selected = mysql_select_db( DBDATABASE, $con ) or die(mysql_error());


     if($_GET["refund"]=="0")
	 {
	      $query = "update mg_sales_flat_order_item set refund=0,dodate='".date("Y-m-d")."' where item_id='".$_GET["itemid"]."'";
		  mysql_query($query);
		  echo "0";
	  }
	  else if($_GET["refund"]=="1")
	  {
	      $query = "update mg_sales_flat_order_item set refund=1,dodate='".date("Y-m-d")."' where item_id='".$_GET["itemid"]."'";
		  mysql_query($query);

		  $sel_data="select * from mg_sales_flat_order_item where item_id='".$_GET["itemid"]."'";
		  $rs_data= mysql_query($sel_data);
		  $data=mysql_fetch_array($rs_data);

		  $sel_order_data="select increment_id,customer_firstname,customer_lastname from mg_sales_flat_order where entity_id='".$data["order_id"]."'";
		  $rs_order_data= mysql_query($sel_order_data);
		  $order_data=mysql_fetch_array($rs_order_data);

		  $sel_sub="select substitute from supervision where item_id='".$_GET["itemid"]."'";
		  $rs_sub=mysql_query($sel_sub);
		  $sub=mysql_fetch_array($rs_sub);

		   echo "1";

		     $to = "steve@prestofreshgrocery.com";
			 $subject = "Refund Request Received for Order no. ".$order_data['increment_id'];
			 $txt = "Customer :".$order_data["customer_firstname"]." ".$order_data['customer_lastname']."<br/>";
			 $txt.= "Item :".$data['name']."<br/>";
			 $txt.= "Item Qty:".$data['qty_ordered']."<br/>";
			 $txt.= "Item Price:".$data['price']."<br/>";
			 $txt.= "Total:".$data['row_total']."<br/>";
			 $txt.= "Substitute:".$sub['substitute']."<br/>";
			 $txt.= "Order no:".$order_data['increment_id']."<br/>";

			 $headers = "MIME-Version: 1.0" . "\r\n";
             $headers.= "Content-type:text/html;charset=UTF-8" . "\r\n";
			 $headers.= "From: refund@prestofreshgrocery.com" . "\r\n";
			 mail($to,$subject,$txt,$headers);
	  }

mysql_close();
?>