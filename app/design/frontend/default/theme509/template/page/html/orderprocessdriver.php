<?php
 if(isset($_GET["ooid"]) && $_GET["ooid"]!="" ) {

    if(isset($_GET["sign"]) && $_GET["sign"]!="" )
	 {
	   $check="select status from drivlist where ordernumber='".$_GET["ooid"]."'";
	     $check_confirm=$connection->fetchOne($check); 
		 
		 
		  $sel_estt="select estt from mg_shipping_delivery_window where order_number='".$_GET["ooid"]."'";
		  $rs_estt=$connection->fetchOne($sel_estt);
		  
		
		 $time = $rs_estt ; 
         $estt=date("H:i", strtotime($time));
		 $estt=$estt.":00";
		 $estt=strtotime($estt);
		 
		 $curtime = now();
		 $newb=explode(" ",$curtime);
		 $hhmm=explode(":",$newb[1]);
		 $realtime=$hhmm[0].":".$hhmm[1].":00";
		 $realtime=strtotime($realtime);
		 
		 $diff = $estt - $realtime;
		 
		 $minutes = $diff / 60 ;
		 
		 
		if($check_confirm=="")
	    {
		
	      $insert="insert into drivlist set sign='".$_GET["sign"]."',ordernumber='".$_GET["ooid"]."',status=1,tip='".$_GET["tip"]."',nowtime='".now()."',popup='".$minutes."'";
		  $connection->query($insert);
		  
		  $update="update mg_shipping_delivery_window set popup='".$minutes."' where order_number='".$_GET["ooid"]."'";
		  $connection->query($update);
		  
	   }
	   else
	   {
	      $update="update drivlist set sign='".$_GET["sign"]."',tip='".$_GET["tip"]."',status=1,popup='".$minutes."',nowtime='".now()."' where ordernumber='".$_GET["ooid"]."'";
		  $connection->query($update);
		  
		  $update="update mg_shipping_delivery_window set popup='".$minutes."' where order_number='".$_GET["ooid"]."'";
		  $connection->query($update);
		  
	   }
	   
	 
	   
	   /************************************/
	include('connection.php');
	//echo "Call file customer survey process";
	$ooid = $_REQUEST['ooid'];
	$qry = "SELECT customer_email, customer_firstname, customer_lastname FROM mg_sales_flat_order WHERE increment_id = '".$ooid."' ";
	$rs = mysql_query($qry);
	$result = mysql_fetch_array($rs);
	
	$Dqry = "SELECT customer_id,driver_email FROM mg_shipping_delivery_window WHERE order_number = '".$ooid."' ";
	$Drs = mysql_query($Dqry);
	$Dresult = mysql_fetch_array($Drs);
	
	$Pqry = "SELECT picker_id FROM mg_order_picker WHERE order_number = '".$ooid."' ";
	$Prs = mysql_query($Pqry);
	$Presult = mysql_fetch_array($Prs);
	
	$sel_driver_id="SELECT id from mg_pickers where email='".$Dresult[1]."'";
	$rs_driver_id= mysql_query($sel_driver_id);
	$driver_id = mysql_fetch_array($rs_driver_id);
	
?>
		<form name="survey_process" id="survey_process_form" method="post" action="http://prestofreshgrocery.com/acceptOrder.php" style="display:none;" />
			<input type="text" name="customer_email" value="<?php echo $result[0];?>" placeholder="Customer Email" />
			<input type="text" name="order_id" value="<?php echo $ooid; ?>" placeholder="Order Id" />
			<input type="text" name="first_name" value="<?php echo $result[1];?>" placeholder="Customer First Name" />
			<input type="text" name="last_name" value="<?php echo $result[2];?>" placeholder="Customer Last Name" />
			<input type="text" name="customer_id" value="<?php echo $Dresult[0];?>" placeholder="Driver ID" />
			<input type="text" name="driver_id" value="<?php echo $driver_id[0];?>" placeholder="Driver ID" />
			<input type="text" name="picker_id" value="<?php echo $Presult[0];?>" placeholder="Picker ID" />
			<input type="submit" id="submit" name="submit" value="submit" />
		</form>
		<script>
		document.getElementById("submit").click();
		</script>

	<?php   
	 }
    
     $order = Mage::getModel('sales/order')->load($_GET["ooid"], 'increment_id');
	 $order->getAllVisibleItems();
	 $address = $order->getShippingAddress();
	  $address2 = $order->getBillingAddress();
	  
	 $orderValue = $order->getSubtotal();
	 $orderValue2 = $order->getBaseGrandTotal();
?>	 
<style>
.page
{
   width:90%;
}

td, th 
{
   padding:5px !important;
   font-size:12px;
   color:#000;
}

table
{
   border-color:#000;
}

th 
{
   color:#fff;
   background-color:green;
}
</style>
<div style="border-bottom:0px solid #dfdfdf;margin-bottom:5px;" >
<h3 class="icon-head head-manage-blog" style="padding-left:0px;color:#eb5e00;">Order No :- <a href="https://www.prestofreshgrocery.com?oid=<?php echo $_GET["ooid"]; ?>"><?php echo $_GET["ooid"]; ?></a></h3>
<?php 
$currentUrl = Mage::helper('core/url')->getCurrentUrl();
$url77 = explode("?oid=",$currentUrl);
?>

</div>
     <table width="48%;" style="border:1px solid #1d3e5f;border-collapse:collapse;float:left;">
	 <tr >
	 <td style="color:green;"> <b> Delivery Address  </b> </td>
	 </tr>
	 <tr>
	 <td>
	  
	            <?php 
				echo $custName = $address->getName()."<br/>";
				echo $custAddr = $address->getStreetFull()."<br/>";
				echo $region2 = $address->getCity()."<br/>";
				echo $region = $address->getRegion()."<br/>";
				echo $country = $address["postcode"]; 
				?>	
	</td>
	</tr>
	</table>	
	
	<table width="48%;"" style="border:1px solid #1d3e5f;border-collapse:collapse;float:left;margin-left:1%">
	 <tr >
	 <td style="color:green;"> <b> Billing Address  </b> </td>
	 </tr>
	 <tr>
	 <td>
	  
	            <?php 
				echo $custName = $address2->getName()."<br/>";
				echo $custAddr = $address2->getStreetFull()."<br/>";
				echo $region2 = $address2->getCity()."<br/>";
				echo $region = $address2->getRegion()."<br/>";
				echo $country = $address2["postcode"]; 
				?>	
	</td>
	</tr>
	</table>	
<?php 

   $value["order_number"] = $_GET["ooid"]; ?>
  
		     <?php $sql3    = "SELECT ss.substitute ,msi.item_id,msi.product_id,msi.price,msi.row_total,msi.qty_ordered,msi.name,msi.sku FROM `mg_sales_flat_order_item` msi
			    left join supervision ss on msi.item_id = ss.item_id  where msi.`order_id`='". $order["entity_id"]."' and ss.substitute is not NULL";
				
			   $rows3 = $connection->fetchAll($sql3);
			   $cc=count($rows3);
			   if($cc>=1) { ?>
			   <div style="clear:both;">&nbsp;</div>
			   <div style="margin-top:10px;clear:both;">
			   <table border=1 cellpadding="5" cellspacing="5" border="1" style="border-collapse:collapse" >
			   
			   <?php
			   foreach ($rows3 as $value3) {
			   if($value3["substitute"]!="") {
			   
			     $bbb=1;
				 
				 if($bbb==1 && $jkl!=22) {
				 
			   ?>
			   
			    <tr style="background-color:green;color:#fff;font-weight:bold;">
			   <th style="color:#fff;">Product Name</th>
			   <th style="color:#fff;">Qty.</th>
			   <th style="color:#fff;">Substitute Info</th>
			   </tr>
			   
			   
			   <?php $jkl=22; } ?>
			   
			   <tr >
				<td><?php echo $value3["name"]; ?></td>
				<td style="font-weight:bold;font-size:15px;"><?php echo round($value3["qty_ordered"],0); ?></td>
				<td><?php echo $value3["substitute"]; ?></td>
				</tr>
				
			   
			   <?php } } ?>
			   </table>
			   </div>
			   <?php } ?>
			   
			  
			   
		   
		   <?php if(isset($_GET["sign2"]) && $_GET["sign2"]!="" ) {
		     
			 echo '<div style="font-weight:bold;color:green;margin-top:15px;">Thank You For Your Order . </div>';
		   }
		   else
		   {
		   ?>
		   <br/>
		   <form method="get" name="frmtip">
		   <input type="hidden" name="ooid" id="ooid" value="<?php echo $_GET["ooid"]; ?>" />
		    <div style="margin-top:10px;color:green;clear:both;">
		   Grand Total : $<?php echo round($orderValue2,2); ?>
		   </div>
		   <?php
		   $sql22444 = "SELECT unattended_shipping FROM `mg_shipping_delivery_window` where `order_number`='".$_GET["ooid"]."'";
		   $rows12224 = $connection->fetchOne($sql22444); 
		   if($rows12224=='yes')
		   {
		   ?>
		   
		   <div style="margin-top:10px;font-weight:bold;color:red;">This is an Unattended Delivery. Be sure you are at correct address - any delivery corrections will be deducted from driver pay. Customer is supposed to leave a cooler for perishable items and the location for leaving items should be secure. If cooler is not present or area is not secure, contact Ops Manager for direction.</div>
		   <?php } ?>
			<div  style="margin-top:10px;">
		   <b>Gratuity (applied to card) &nbsp;</b>
		   <?php
		   $check="select tip from drivlist where ordernumber='".$_GET["ooid"]."'";
	       $check_tip=$connection->fetchOne($check); 
		   
		   $check="select sign from drivlist where ordernumber='".$_GET["ooid"]."'";
	       $check_sign=$connection->fetchOne($check);  ?>
		 $<input type="text" name="tip" id="tip" style="width:90px;border:1px solid #000;height:25px" value="<?php echo $check_tip; ?>" />
		   <br/>
		  </div>
		  <div style="margin-top:10px;">
		    Initial to Accept Delivery &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="sign" id="sign" style="width:90px;border:1px solid #000;height:25px" value="<?php echo $check_sign; ?>" /> 
			</div>
			
			<br/> <input type="submit" style="background-color:green;padding:5px;color:#fff;margin-top:5px;margin-bottom:5px;font-size:12px;font-weight:bold;" name="std" value="Accept Delivery"  />
			
			</form>
			<?php } ?>
		   
		   
		  <?php
		  exit(0);
  } 
 ?>