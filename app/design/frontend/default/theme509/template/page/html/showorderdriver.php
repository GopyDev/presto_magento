<?php if(isset($_GET["oid"]) && $_GET["oid"]!="" ) {

     if(isset($_GET["dnote"]) && $_GET["dnote"]!="" )
	 {
	     $check="select status from drivlist where ordernumber='".$_GET["oid"]."'";
	     $check_confirm=$connection->fetchOne($check);

		if($check_confirm=="")
	    {
	      $insert="insert into drivlist set dnote='".$_GET["dnote"]."',ordernumber='".$_GET["oid"]."'";
		  $connection->query($insert);
	    }
	   else
	   {
	      $update="update drivlist set dnote='".$_GET["dnote"]."' where ordernumber='".$_GET["oid"]."'";
		  $connection->query($update);
	   }

	 }

      $order = Mage::getModel('sales/order')->load($_GET["oid"], 'increment_id');
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
<h3 class="icon-head head-manage-blog" style="padding-left:0px;color:#eb5e00;">Order Details ( <?php echo $_GET["oid"]; ?> )</h3>
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

	<table width="48%;" style="border:1px solid #1d3e5f;border-collapse:collapse;float:left;margin-left:1%">
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
	</table>	<br/>
		<!-- <b> Customer :- </b>
		<?php echo $custName; ?>
		&nbsp; &nbsp;
	  <?php
	  		$sql55555 = "SELECT `window` FROM `mg_shipping_delivery_window` where `order_number`='".$_GET["oid"]."'";
			$rows55555 = $connection->fetchOne($sql55555);
			 $temp = explode("|",$rows55555);
		 ?>
		 <b> Delivery Date :- </b>
		<?php if($temp[1]=="") { echo $temp[0]; } else { echo $temp[1]; } ?>
		&nbsp; &nbsp;<b> Delivery Time :- </b>
			 <?php if($temp[1]=="") { echo 'Flexible Time'; } else { echo $temp[0]; } ?>
		 <br/><br/> -->
<?php

   $value["order_number"] = $_GET["oid"];
   if($value["order_number"]!="")
		   {
		       $sql2        = "SELECT entity_id FROM mg_sales_flat_order_grid where increment_id='".$value["order_number"]."'";
		       $rows2 = $connection->fetchOne($sql2);
			   echo "<br/>";

				$sql3        = "SELECT msi.item_id,msi.refund,mei.value,msi.product_id,msi.price,msi.row_total,msi.qty_ordered,msi.name,msi.sku FROM `mg_sales_flat_order_item` msi
			    left join mg_catalog_product_entity_int mei on msi.product_id = mei.entity_id and mei.attribute_id =  251
			    where msi.`order_id`='".$rows2."'";

			   $rows3 = $connection->fetchAll($sql3);

			   ?>
			   <div style="clear:both;">&nbsp;</div>
			   <table border=1 cellpadding="5" cellspacing="5" border="1" style="border-collapse:collapse;margin-top:15px;" >
			   <tr>
			   <th>Product Name</th>
			   <th>Qty.</th>
			   <th>Refund</th>
			   </tr>
			    <?php
			   foreach ($rows3 as $value3) {
				$_resource = Mage::getSingleton('catalog/product')->getResource();
				$optionValue1 = $_resource->getAttributeRawValue($value3["product_id"],'protype', Mage::app()->getStore());
				$product = Mage::getModel('catalog/product')->setStoreId($store_id)->setProtype($optionValue1);
                $optionLabel1 = $product->getAttributeText('protype');
				$optionValue4 = $_resource->getAttributeRawValue($value3["product_id"],'vendor_product_id', Mage::app()->getStore());
				$sel_status="select status from `supervision` where item_id='".$value3["item_id"]."'";
				$status=$connection->fetchOne($sel_status);
		    $sel_substitute="select substitute from `supervision` where item_id='".$value3["item_id"]."'";
				$substitute=$connection->fetchOne($sel_substitute);
				if($substitute!= '') {
			   ?>
			    <tr style="background:red;color:#fff;">
				<td style="background:red;color:#fff;" ><?php echo $value3["name"]; ?>

				<?php if($substitute!="") { ?>
				<br/>
				<b>Substitute Item :- <?php echo $substitute; ?> </b>
				<?php } ?>

				</td>
				<td style="background:red;color:#fff;" ><?php echo round($value3["qty_ordered"],0); ?></td>
				<td style="font-weight:bold;font-size:12px;"><div style="color:#000;margin-top:15px;"> <input type="checkbox" name="chkref-<?php echo $value3['item_id']; ?>" id="chkref-<?php echo $value3['item_id']; ?>" <?php if($value3['refund']==1) { echo "checked"; } ?> value="<?php echo $value3['item_id']; ?>" onClick="dorefund(<?php echo $value3['item_id']; ?> )" /> Refund Item </div></td></tr>

				<?php } else { ?>
				<tr >
				<td><?php echo $value3["name"]; ?></td>
				<td style="font-weight:bold;font-size:15px;"><?php echo round($value3["qty_ordered"],0); ?></td>
				<td style="font-weight:bold;font-size:12px;"><div style="color:#000;margin-top:15px;"> <input type="checkbox" name="chkref-<?php echo $value3['item_id']; ?>" id="chkref-<?php echo $value3['item_id']; ?>" <?php if($value3['refund']==1) { echo "checked"; } ?> value="<?php echo $value3['item_id']; ?>" onClick="dorefund(<?php echo $value3['item_id']; ?> )" /> Refund Item </div></td></tr>
				<?php }
			   }
			   ?>
			    </table>
			   <?php
		      }
		   ?>
		   <form method="get" name="frmdnote">
		   <input type="hidden" name="oid" id="oid" value="<?php echo $_GET["oid"]; ?>" />
		    <br/>
		   <b>Delivery Notes</b>
		   <?php
		   $check="select dnote from drivlist where ordernumber='".$_GET["oid"]."'";
	       $check_dnote=$connection->fetchOne($check);
		   ?>
		   <br/>
		   <textarea name="dnote" style="width:280px;border:1px solid #000;"><?php echo $check_dnote; ?></textarea>
		   <br/>
		   <input type="submit" style="background-color:green;padding:5px;color:#fff;margin-top:5px;margin-bottom:5px;" name="std" value="Submit Note"  /> &nbsp; <a href="<?php echo Mage::getBaseUrl(); ?>?ooid=<?php echo $_GET["oid"]; ?>"> Serve Order </a>
		   </form>
		  <?php
		  exit(0);
  } ?>