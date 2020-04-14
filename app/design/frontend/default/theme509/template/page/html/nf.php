<style>
a
{
    font-size:15px !important;
}
</style>
<?php
if(isset($_GET["sh"]) && $_GET["sh"]!="")
{
           $start_date = date("Y-m-d");
	       $start_date1 = date('Y-m-d',strtotime($start_date . " +0 day"));
           $end_date1 = date('Y-m-d',strtotime($adate22 . " -365 day"));

		   $sel_qty_ordered="select count(product_id) from mg_sales_flat_order_item where created_at>='".$end_date1."' and  created_at<='".$start_date1."' and product_id='".$_GET["sh"]."'";
		   $qty_ordered=$connection->fetchOne($sel_qty_ordered);
		   if($qty_ordered=="")
		   {
		      $qty_ordered=0;
		   }
		   else
		   {
		      $qty_ordered=round($qty_ordered);
		   }

		   $sel_qty_nf="select count(s.item_id) from supervision s
			 left join mg_sales_flat_order_item mi on s.item_id=mi.item_id
			 where mi.product_id='".$_GET["sh"]."' and ( s.realdate>='".$end_date1."' and s.realdate<='".$start_date1."' ) and s.status!='found'";
		   $qty_nf=$connection->fetchOne($sel_qty_nf);
		   if($qty_nf=="")
		   {
		      $qty_nf=0+1;
		   }
		   else
		   {
		      $qty_nf=round($qty_nf)+1;
		   }

		    $sel_qty_sf="select count(s.item_id) from supervision s
			 left join mg_sales_flat_order_item mi on s.item_id=mi.item_id
			 where mi.product_id='".$_GET["sh"]."' and ( s.realdate>='".$end_date1."' and s.realdate<='".$start_date1."' ) and s.status!='found' and s.substitute!=' '";

		   $qty_sf=$connection->fetchOne($sel_qty_sf);

		   if($qty_sf=="")
		   {
		      $qty_sf=0;
		   }
		   else
		   {
		      $qty_sf=round($qty_sf);
		   }

		   $sel_name="select name from mg_sales_flat_order_item where product_id='".$_GET["sh"]."'";
		   $name=$connection->fetchOne($sel_name);

		   echo "<div style='color:green;'>Product Name :".$name;
		   echo "<br/><br/>";
		   echo "No. of times Ordered :".$qty_ordered;
		   echo "<br/><br/>";
		   echo "No. of times on Not Found list :".$qty_nf;
		   echo "<br/><br/>";
		   echo "No. of times Substituted :".$qty_sf;
		   echo "<br/><br/>";
		   echo "% of times on Not Found list in last 365 days : ".round($qty_nf/$qty_ordered,2)*100 ."%";
		    echo "<br/><br/>";
		   echo "% of times Substituted in last 365 days : ".round($qty_sf/$qty_ordered,2)*100 ."%";
		    echo "<br/><br/></div>";

		   $sel_ss="select mi.name,s.substitute,s.cust,s.ddate from supervision s
			 left join mg_sales_flat_order_item mi on s.item_id=mi.item_id
			 where mi.product_id='".$_GET["sh"]."' and ( s.realdate>='".$end_date1."' and s.realdate<='".$start_date1."' ) and s.status!='found' and s.substitute!=' '";
		   $ssf=$connection->fetchAll($sel_ss);
		   ?>

           <table style="width:90%;" border="1" style="border-collapse:">
           <tr>
           <td>Substitute</td>
           <td>Customer</td>
           <td>Date</td>
           </tr>
		   <?php
           foreach($ssf as $td)
		   {
		      $gbp=explode("_",$td["ddate"]);
		   ?>
           <tr>
           <td><?php echo $td["substitute"]; ?></td>
           <td><?php echo $td["cust"]; ?></td>
           <td><?php echo $gbp[1]."/".$gbp[0]."/".$gbp[2]; ?></td>
           </tr>
           <?php
		   }
         }
       if(isset($_GET["gt"]) && $_GET["gt"]=='nf' ) {
    ?>
	 <script type="text/javascript">
	  function obccheck() {
				jQuery('html, body').animate({
					scrollTop: jQuery('#location-'+jQuery('#bylocation').val()).offset().top
				}, 2000);
	}
	 function obproce() {
				jQuery('html, body').animate({
					scrollTop: jQuery('#location-'+jQuery('#productsel').val()).offset().top
				}, 2000);
	}
	function backdrppick() {
	   jQuery('html, body').animate({
					scrollTop: jQuery('.page').offset().top
				}, 2000);
	}

	function showhkdiv(id)
	{
	    jQuery('#hkdiv-'+id).toggle();
	}

	function dorefund(itemid,idstring)
	{

	   bcd=(jQuery('#chkref-'+itemid).is(':checked'));

	   if(bcd==true)
	   {
		  bcd=1;
		  k=confirm('Are you sure you want to refund this item ?');

	   }
	   else
	   {
		 bcd=0;
		 k=confirm('Are you sure you remove this item from refund list?');
	   }

	   if(k==true)
	   {

		  jQuery.ajax({
			   url: "<?php echo Mage::getBaseUrl(); ?>ajaxrefund.php?rand="+Math.random()+"&itemid="+itemid+"&refund="+bcd,
			   context: document.body
				}).done(function(data) {
				 if(data==1)
				 {
					alert("item has been refunded successfully");
				 }
				 else if(data==0)
				 {
				   alert("item has been removed from refund list successfully");
				 }
			 });
	   }
	}
    </script>
	<div style="color:red;margin-top:10px;margin-bottom:10px"> Itmes : <?php if($_GET["it"]=="a") { echo " Incomplete Items"; } else if($_GET["it"]=="b") { echo "Not Packed Items"; } else { echo "All Items"; } ?> , Vehicle : <?php if($_GET["v"]=="v") { echo " Van 1"; } else if($_GET["v"]=="b") { echo "Box Truck"; } else { echo "All"; } ?>
	</div>
    <div style="float:left; width:33%">
    <table border="1" style="width:100%">
    <tr>
    <th style="text-align:center; background:#003300; color:#fff;">
    Items
    </th>
    </tr>
    <tr style="text-align:left;">
    <td>
	<a href="<?php echo Mage::getBaseUrl(); ?>?gt=nf&it=all&t=<?php echo $_GET["t"]; ?>" style="text-decoration:underline;<?php if(!isset($_GET["it"]) || $_GET["it"]=='all' || $_GET["it"]=='' ) { echo 'text-decoration:none;color:red'; } ?>"  > All Items </a> <br/> <a href="<?php echo Mage::getBaseUrl(); ?>?gt=nf&it=a&t=<?php echo $_GET["t"]; ?>" style="text-decoration:underline;<?php if(isset($_GET["it"]) && $_GET["it"]=='a') { echo 'text-decoration:none;color:red;'; } ?>"> Incomplete  </a> <br/> <a href="<?php echo Mage::getBaseUrl(); ?>?gt=nf&it=b&t=<?php echo $_GET["t"]; ?>" style="text-decoration:underline;<?php if(isset($_GET["it"]) && $_GET["it"]=='b') { echo 'text-decoration:none;color:red;'; } ?>"> Not Packed  </a>
    <div>&nbsp;</div>
    </td>
    </tr>
    </table>
	</div>
    <div style="float:left; width:33%">
    <table border="1" style="width:100%">
    <tr>
    <th style="text-align:center; background:#003300; color:#fff;">
    Vehicle
    </th>
    </tr>
    <tr>
    <td>
    <a style="text-decoration:underline;<?php if(!isset($_GET["v"]) || $_GET["v"]=='all' || $_GET["v"]=='' ) { echo 'text-decoration:none;color:red'; } ?>" href="<?php echo Mage::getBaseUrl(); ?>?gt=nf&it=<?php echo $_GET["it"]; ?>&t=<?php echo $_GET["t"]; ?>" > All  </a>
    <br/>
	<a style="text-decoration:underline;<?php if(isset($_GET["v"]) && $_GET["v"]=='b') { echo 'text-decoration:none;color:red;'; } ?>" href="<?php echo Mage::getBaseUrl(); ?>?gt=nf&it=<?php echo $_GET["it"]; ?>&v=b&t=<?php echo $_GET["t"]; ?>" > Van 2 - New  </a> <br/>
    <a style="text-decoration:underline;<?php if(isset($_GET["v"]) && $_GET["v"]=='v') { echo 'text-decoration:none;color:red;'; } ?>" href="<?php echo Mage::getBaseUrl(); ?>?gt=nf&it=<?php echo $_GET["it"]; ?>&v=v&t=<?php echo $_GET["t"]; ?>" > Van 1 - Old  </a>
    <div>&nbsp;</div>
    </td>
    </tr>
    </table>
	</div>
    <div style="float:left; width:33%">
    <table border="1" style="width:100%">
    <tr>
    <th style="text-align:center; background:#003300; color:#fff;">
    Type
    </th>
    </tr>
    <tr>
    <td>
    <a style="text-decoration:underline;<?php if(!isset($_GET["t"]) || $_GET["t"]=='all' || $_GET["t"]=='' ) { echo 'text-decoration:none;color:red'; } ?>" href="<?php echo Mage::getBaseUrl(); ?>?gt=nf&it=<?php echo $_GET["it"]; ?>&v=<?php echo $_GET["v"]; ?>" > All  </a>
    <br/>
	<a style="text-decoration:underline;<?php if(isset($_GET["t"]) && $_GET["t"]=='d') { echo 'text-decoration:none;color:red;'; } ?>" href="<?php echo Mage::getBaseUrl(); ?>?gt=nf&it=<?php echo $_GET["it"]; ?>&v=<?php echo $_GET["v"]; ?>&t=d" > Dry  </a> <br/>
    <a style="text-decoration:underline;<?php if(isset($_GET["t"]) && $_GET["t"]=='c') { echo 'text-decoration:none;color:red;'; } ?>" href="<?php echo Mage::getBaseUrl(); ?>?gt=nf&it=<?php echo $_GET["it"]; ?>&v=<?php echo $_GET["v"]; ?>&t=c" > Cold
      </a>
       <br/>
      <a style="text-decoration:underline;<?php if(isset($_GET["t"]) && $_GET["t"]=='f') { echo 'text-decoration:none;color:red;'; } ?>" href="<?php echo Mage::getBaseUrl(); ?>?gt=nf&it=<?php echo $_GET["it"]; ?>&v=<?php echo $_GET["v"]; ?>&t=f" > Frozen
      </a>
    </td>
    </tr>
    </table>
	</div>
	<?php
	    if(isset($_GET["v"]))
		{
		    if($_GET["v"]=='b')
			{
			   $vong="2";
			}

			if($_GET["v"]=='v')
			{
			   $vong="1";
			}
		}


		if(isset($_GET["t"]))
		{
		    if($_GET["t"]=='d')
			{
			   $tong="143";
			}

			if($_GET["t"]=='c')
			{
			   $tong="140";
			}

			if($_GET["t"]=='f')
			{
			   $tong="142";
			}
		}

	?>

	<?php
	    if(isset($_GET["it"]) && $_GET["it"]=='a')
		{
		     $sql3e = "SELECT s.*,rr2.value,
		 ( CASE rr2.value
	WHEN 'Deli' THEN 1
	WHEN 'Bakery' THEN 1
	WHEN 'Seafood' THEN 2
	WHEN '9' THEN 3
	WHEN '8' THEN 4
	WHEN 'Packed Meat' THEN 5
	WHEN '7' THEN 6
	WHEN '6' THEN 7
	WHEN '5' THEN 8
	WHEN '4' THEN 9
	WHEN '3' THEN 10
	WHEN '2' THEN 11
	WHEN '1' THEN 12
	WHEN 'Dairy' THEN 13
	WHEN 'Produce' THEN 14
	WHEN 'R' THEN 15
	ELSE 16
	END ) as abc,rim.drivername FROM  `supervision` s
			 left join mg_sales_flat_order_item msi on msi.item_id = s.item_id
			  left join mg_sales_flat_order mop on msi.order_id = mop.entity_id
			 left join mg_shipping_delivery_window rim on rim.order_number = mop.increment_id
			 left join mg_catalog_product_entity_int mei on msi.product_id = mei.entity_id and mei.attribute_id =  251 and mei.store_id = 0
						left join mg_catalog_product_entity_int rr on msi.product_id = rr.entity_id and rr.attribute_id =  252 and rr.store_id = 0
						left join mg_catalog_product_entity_int qrr on msi.product_id = qrr.entity_id and qrr.attribute_id =  253 and rr.store_id = 0
						left join mg_eav_attribute_option_value rr2 on rr.value = rr2.option_id and rr2.store_id = 0

			 where s.ddate='".$_GET["ode"]."' and s.status!='found' and ( s.log=2 or s.log=0)";

			 if($vong!="")
			 {
			    $sql3e.=" and rim.driverid='".$vong."'";
			 }

			 if($tong!="")
			 {
			    $sql3e.=" and mei.value='".$tong."'";
			 }

			  $sql3e.=" group by s.item_id order by abc,msi.name";
		}

		else if(isset($_GET["it"]) && $_GET["it"]=='b')
		{

		 $sql3e = "SELECT s.*,rr2.value,
		 ( CASE rr2.value
	WHEN 'Deli' THEN 1
	WHEN 'Bakery' THEN 1
	WHEN 'Seafood' THEN 2
	WHEN '9' THEN 3
	WHEN '8' THEN 4
	WHEN 'Packed Meat' THEN 5
	WHEN '7' THEN 6
	WHEN '6' THEN 7
	WHEN '5' THEN 8
	WHEN '4' THEN 9
	WHEN '3' THEN 10
	WHEN '2' THEN 11
	WHEN '1' THEN 12
	WHEN 'Dairy' THEN 13
	WHEN 'Produce' THEN 14
	WHEN 'R' THEN 15
	ELSE 16
	END ) as abc FROM  `supervision` s
			 left join mg_sales_flat_order_item msi on msi.item_id = s.item_id
			  left join mg_sales_flat_order mop on msi.order_id = mop.entity_id
			 left join mg_shipping_delivery_window rim on rim.order_number = mop.increment_id
			 left join mg_catalog_product_entity_int mei on msi.product_id = mei.entity_id and mei.attribute_id =  251 and mei.store_id = 0
						left join mg_catalog_product_entity_int rr on msi.product_id = rr.entity_id and rr.attribute_id =  252 and rr.store_id = 0
						left join mg_catalog_product_entity_int qrr on msi.product_id = qrr.entity_id and qrr.attribute_id =  253 and rr.store_id = 0
						left join mg_eav_attribute_option_value rr2 on rr.value = rr2.option_id and rr2.store_id = 0


			 where s.ddate='".$_GET["ode"]."' and s.status!='found'  and (s.log=1 or s.log=3) and s.log2!=1 ";

			  if($vong!="")
			  {
			    $sql3e.=" and rim.driverid='".$vong."'";
			  }


			 if($tong!="")
			 {
			    $sql3e.=" and mei.value='".$tong."'";
			 }


			  $sql3e.=" group by s.item_id order by abc,msi.name";

		}

		else if(isset($_GET["it"]) && $_GET["it"]=='all')
		{
		     $sql3e = "SELECT s.*,rr2.value,
			 ( CASE rr2.value
				WHEN 'Deli' THEN 1
				WHEN 'Bakery' THEN 1
				WHEN 'Seafood' THEN 2
				WHEN '9' THEN 3
				WHEN '8' THEN 4
				WHEN 'Packed Meat' THEN 5
				WHEN '7' THEN 6
				WHEN '6' THEN 7
				WHEN '5' THEN 8
				WHEN '4' THEN 9
				WHEN '3' THEN 10
				WHEN '2' THEN 11
				WHEN '1' THEN 12
				WHEN 'Dairy' THEN 13
				WHEN 'Produce' THEN 14
				WHEN 'R' THEN 15
				ELSE 16
				END ) as abc,rim.driverid FROM  `supervision` s
			 left join mg_sales_flat_order_item msi on msi.item_id = s.item_id
			  left join mg_sales_flat_order mop on msi.order_id = mop.entity_id
			 left join mg_shipping_delivery_window rim on rim.order_number = mop.increment_id
			 left join mg_catalog_product_entity_int mei on msi.product_id = mei.entity_id and mei.attribute_id =  251 and mei.store_id = 0
						left join mg_catalog_product_entity_int rr on msi.product_id = rr.entity_id and rr.attribute_id =  252 and rr.store_id = 0
						left join mg_catalog_product_entity_int qrr on msi.product_id = qrr.entity_id and qrr.attribute_id =  253 and rr.store_id = 0
						left join mg_eav_attribute_option_value rr2 on rr.value = rr2.option_id and rr2.store_id = 0

			 where s.ddate='".$_GET["ode"]."' and s.status!='found' ";

			 if($vong!="")
			 {
			    $sql3e.=" and rim.driverid='".$vong."'";
			 }

			 if($tong!="")
			 {
			    $sql3e.=" and mei.value='".$tong."'";
			 }

			  $sql3e.=" group by s.item_id order by abc,msi.name";

		}
		else
		{
		     $sql3e = "SELECT s.*,rr2.value,
			 ( CASE rr2.value
				WHEN 'Deli' THEN 1
				WHEN 'Bakery' THEN 1
				WHEN 'Seafood' THEN 2
				WHEN '9' THEN 3
				WHEN '8' THEN 4
				WHEN 'Packed Meat' THEN 5
				WHEN '7' THEN 6
				WHEN '6' THEN 7
				WHEN '5' THEN 8
				WHEN '4' THEN 9
				WHEN '3' THEN 10
				WHEN '2' THEN 11
				WHEN '1' THEN 12
				WHEN 'Dairy' THEN 13
				WHEN 'Produce' THEN 14
				WHEN 'R' THEN 15
				ELSE 16
				END ) as abc FROM `supervision` s
			 left join mg_sales_flat_order_item msi on msi.item_id = s.item_id
			   left join mg_sales_flat_order mop on msi.order_id = mop.entity_id
			 left join mg_shipping_delivery_window rim on rim.order_number = mop.increment_id
			 left join mg_catalog_product_entity_int mei on msi.product_id = mei.entity_id and mei.attribute_id =  251 and mei.store_id = 0
						left join mg_catalog_product_entity_int rr on msi.product_id = rr.entity_id and rr.attribute_id =  252 and rr.store_id = 0
						left join mg_catalog_product_entity_int qrr on msi.product_id = qrr.entity_id and qrr.attribute_id =  253 and rr.store_id = 0
						left join mg_eav_attribute_option_value rr2 on rr.value = rr2.option_id and rr2.store_id = 0
			 where s.ddate='".$_GET["ode"]."' and s.status!='found' ";

			 if($vong!="")
			 {
			    $sql3e.=" and rim.driverid='".$vong."'";
			 }

			 if($tong!="")
			 {
			    $sql3e.=" and mei.value='".$tong."'";
			 }

			  $sql3e.=" group by s.item_id order by abc,msi.name";

		} ?>

		<div style="clear:both;height:10px;">&nbsp;</div>

		<form method="get" action="<?php echo Mage::getBaseUrl(); ?>" style="clear:both;margin-top:9px;margin-bottom:9px;">
	  <input type="hidden" name="picker2" value="<?php echo $_GET["picker2"]; ?>" />
	  <input type="hidden" name="gt" value="<?php echo $_GET["gt"]; ?>" />
	  Location : <select name="bylocation" id="bylocation" onChange="obccheck();" style="border:1px solid #000;">
	  <option value="">Select</option>
	  <?php

	   $sql3ehh = "SELECT s.*,rr2.value,
			 ( CASE rr2.value
				WHEN 'Deli' THEN 1
				WHEN 'Bakery' THEN 1
				WHEN 'Seafood' THEN 2
				WHEN '9' THEN 3
				WHEN '8' THEN 4
				WHEN 'Packed Meat' THEN 5
				WHEN '7' THEN 6
				WHEN '6' THEN 7
				WHEN '5' THEN 8
				WHEN '4' THEN 9
				WHEN '3' THEN 10
				WHEN '2' THEN 11
				WHEN '1' THEN 12
				WHEN 'Dairy' THEN 13
				WHEN 'Produce' THEN 14
				WHEN 'R' THEN 15
				ELSE 16
				END ) as abc FROM  `supervision` s
			 left join mg_sales_flat_order_item msi on msi.item_id = s.item_id
			 left join mg_catalog_product_entity_int mei on msi.product_id = mei.entity_id and mei.attribute_id =  251 and mei.store_id = 0
						left join mg_catalog_product_entity_int rr on msi.product_id = rr.entity_id and rr.attribute_id =  252 and rr.store_id = 0
						left join mg_catalog_product_entity_int qrr on msi.product_id = qrr.entity_id and qrr.attribute_id =  253 and rr.store_id = 0
						left join mg_eav_attribute_option_value rr2 on rr.value = rr2.option_id and rr2.store_id = 0
			 where s.ddate='".$_GET["ode"]."' and s.status!='found' group by rr2.value order by abc";


	$location = $connection->fetchAll($sql3ehh);
	foreach ($location as $newloc) {
	?>
	<option value="<?php echo $newloc["value"]; ?>" <?php if($_GET["bylocation"]==$newloc["value"]) { ?> selected <?php } ?> ><?php echo $newloc["value"]; ?></option>
	<?php
	}
	?>
	</select> <div style="margin-top:8px;">   Product : &nbsp;<select name="productsel" id="productsel" onChange="obproce();" style="border:1px solid #000;width:150px">
	  <option value="">Select</option>
	  <?php
	$location = $connection->fetchAll($sql3e);
	foreach ($location as $newloc) {

	 $temp = explode("-",$newloc["idstring"]);

		   $sql33="select msi.name FROM `mg_sales_flat_order_item` msi where msi.item_id='".$temp[1]."' and  msi.qty_ordered='".$newloc["itemqty"]."'";
		   $rows274pro = $connection->fetchOne($sql33);

	?>
	<option value="<?php echo $newloc["item_id"]; ?>" ><?php echo $rows274pro; ?></option>
	<?php
	}
	?>
	</select> </div>
	</form>

	<?php
		$rows3e = $connection->fetchAll($sql3e);
		$count=count($rows3e);
		   if($count>=1)
		   {
		   ?>
		<table border=1 cellpadding="5" cellspacing="5" style="border-collapse:collapse;font-size:11px;margin-top:15px;border:1px solid #000;width:97%">
					  <tr><td colspan=6 align="center" style="background:black;color:#fff;font-size:12px;"><b> Not Found List for <?php echo $_GET["ode"]; ?> </b></td></tr>
					   <tr>
						<th style="text-align:left;padding:5px;" >Product Name</th>
						<th style="text-align:left;padding:5px;">Information</th>
					   </tr>
	   <?php
	   foreach ($rows3e as $value3e) {

		   $temp = explode("-",$value3e["idstring"]);

		   $sql33="select msi.name FROM `mg_sales_flat_order_item` msi where msi.item_id='".$temp[1]."' and  msi.qty_ordered='".$value3e["itemqty"]."'";
		   $rows274pro = $connection->fetchOne($sql33);

		   $sql334="select msi.product_id FROM `mg_sales_flat_order_item` msi where msi.item_id='".$temp[1]."' and  msi.qty_ordered='".$value3e["itemqty"]."'";
		   $rows2745 = $connection->fetchOne($sql334);

		   $sql334="select rr2.value FROM `mg_sales_flat_order_item` msi
		   left join mg_catalog_product_entity_int mei on msi.product_id = mei.entity_id and mei.attribute_id =  251 and mei.store_id = 0
						left join mg_catalog_product_entity_int rr on msi.product_id = rr.entity_id and rr.attribute_id =  252 and rr.store_id = 0
						left join mg_catalog_product_entity_int qrr on msi.product_id = qrr.entity_id and qrr.attribute_id =  253 and rr.store_id = 0
						left join mg_eav_attribute_option_value rr2 on rr.value = rr2.option_id and rr2.store_id = 0

		   where msi.item_id='".$temp[1]."' and  msi.qty_ordered='".$value3e["itemqty"]."'";
		    $rows2745l = $connection->fetchOne($sql334);

		    $sql334e="select msi.order_id FROM `mg_sales_flat_order_item` msi where msi.item_id='".$temp[1]."' and  msi.qty_ordered='".$value3e["itemqty"]."'";

		    $order_id = $connection->fetchOne($sql334e);

		    $ob=Mage::getModel('sales/order')->load($order_id);

		    $ccde =  Mage::getModel("customer/customer")->load($ob->getCustomerId());

            if($ccde->getAcceptSubstitutes())
			{
			   $pick = "Yes";
			}
			else
			{
			  $pick = "No";
			}

		   $product222 = Mage::getModel('catalog/product')->load($rows2745);

		   $sql33="select `log` from `supervision` where idstring='".$value3e["idstring"]."' ";
		   $rows274 = $connection->fetchOne($sql33);

		   if($rows274==1)
		   {
		      $bgk='#d9d9d9';
		   }
		   else if($rows274==2)
		   {
		      $bgk='#ffd9d9';
		   }

		   else if($rows274==3)
		   {
		      $bgk='#d5ffd5';
		   }

		   else
		   {
		     $bgk='#fff';
		   }


		   if (strpos($value3e["note"],'Need') !== false) {
             $blue=1;
           }
		   else if(strpos($value3e["note"],'need') !== false) {
		       $blue=1;
		   }
		   else
		   {
		     $blue=0;
		   }

		  ?>

          <tr style="background-color:<?php echo $bgk; ?>" id="tr-<?php echo $value3e["idstring"]; ?>" ><td colspan="2" >
        <div id="location-<?php echo $value3e["item_id"]; ?>" style="color:#000;padding:5px;font-size:15px;" >
       <a href="<?php echo $product222->getProductUrl(); ?>" target="_blank" ><img src="<?php echo $this->helper('catalog/image')->init($product222, 'small_image')->resize(50) ?>" border="0" width="50" alt="<?php echo $this->htmlEscape($product222->getName()) ?>" /> </a>        <div style="display:inline-block; width:75%; verticle-align:middle;">  <?php echo $rows274pro; ?><br/><a href="<?php echo Mage::getBaseUrl(); ?>?item_id=<?php echo $value3e["item_id"]; ?>&rpc=<?php echo $_GET["ode"]; ?>" style="text-decoration:underline;color:red;font-size:12px;" target="_blank"> Product Change </a> &nbsp;<a href='javascript:void(0);' style="text-decoration:underline;color:green;font-size:12px;" onClick='backdrppick();' > ( Go Top ) </a>  </div>
		 </div>
          <div style="clear:both; background:#000;margin-top:5px;margin-bottom:5px;height:1px">&nbsp;</div>
          <div style="width:48%; float:left;">
          <div style="font-size:14px;font-weight:bold;width:100%;display:block; background:#ffff01;color:#000;padding:5px;margin-top:5px;"><b> Required Qty :- <?php echo $value3e["itemqty"]-$value3e["got_qty"]; ?></b></div>
		   <div style="background-color:green;color:#fff;padding:5px;font-size:14px;font-weight:bold;width:100%;" id="location-<?php echo $rows2745l; ?>"> Location : <?php echo $rows2745l; ?>  </div>
		</div>
		 <div style="width:48%; float:right;">
          <?php
		   $sql33="select `substitute` from `supervision` where idstring='".$value3e["idstring"]."' ";
		   $rows274 = $connection->fetchOne($sql33);

		   $sql_picker="select name from `mg_pickers` where id='".$value3e["infopicker"]."'";
		   $picker_name=$connection->fetchOne($sql_picker);


		   $sql22b = "SELECT log FROM supervision where item_id='".$value3e["item_id"]."' and ddate='".$_GET["ode"]."'";
		   $rows22brr = $connection->fetchOne($sql22b);

		 if($value3e["note"]!="" && $value3e["note"]!="undefined") { ?>
		 <div style="width:95%;padding:5px;background:red;color:#fff;font-size:12px;font-weight:bold; margin-top:5px;"><?php echo $picker_name; ?> Note :-
		 <textarea id="nf-<?php echo $value3e['item_id']; ?>" name="nf-<?php echo $value3e['item_id']; ?>"
         style="border:1px solid #000; width:99%;" onBlur="donfnote('<?php echo $value3e["item_id"]; ?>');" ><?php echo $value3e["note"]; ?></textarea></div>
		 <div id="divnfres-<?php echo $value3e['item_id']; ?>"></div>
		 <?php } else { ?>
		 <div style="width:95%;padding:5px;background:none;color:#000;font-size:12px;font-weight:bold; margin-top:5px;"><?php echo $picker_name; ?> Note :-
		 <textarea id="nf-<?php echo $value3e['item_id']; ?>" name="nf-<?php echo $value3e['item_id']; ?>"
         style="border:1px solid #000; width:99%;" onBlur="donfnote('<?php echo $value3e["item_id"]; ?>');" ></textarea></div>
          <div id="divnfres-<?php echo $value3e['item_id']; ?>"></div>
		 <?php } ?>
		  <div>
					   <?php
                       $torder_id = $ob->getIncrementId();
					   $ooid = $ob->getId();
                       $select_cnote="select cnote from mg_shipping_delivery_window where order_number='".$torder_id."'";
                       $conote=$connection->fetchOne($select_cnote);
                       if($conote!="")
                       {
                          echo  "<p style='padding:5px;color:#fff;background-color:red;margin-top:5px;width:95%'> Customer Note :- ".$conote."</p>";
                       }


                        $sel_order_comment="select customer_note from mg_sales_flat_order where entity_id='".$ooid."' and customer_note is not NULL";
		                $comment=$connection->fetchOne($sel_order_comment);

						   if($comment!="")
						   {
						  echo  "<p style='padding:5px;color:#fff;background-color:red;margin-top:5px;width:95%'> Customer Note :- ".$comment."</p>";
						   }


						    $sel_order_sp="select spt from sp where order_id='".$torder_id."'";
		                    $order_sp=$connection->fetchOne($sel_order_sp);

						   if($order_sp!="")
						   {
							   $order_sp=rtrim($order_sp, ",");
						       echo  "<p style='padding:5px;color:#fff;background-color:red;margin-top:5px;width:95%'> Substitute Preference :- ".$order_sp."</p>";
						   }
		   ?>
            </div>
         		<?php
			    $value3e["order_number"]=$ob["increment_id"];
				?>
                </div>
             <div style="clear:both; "></div>
             <div style="clear:both; background:#000;margin-top:5px;margin-bottom:5px;height:1px">&nbsp;</div>
             <div style="" id="div-snote-<?php echo $value3e["idstring"]; ?>" >
            <div style="padding:5px; width:100%; margin-top:5px; margin-bottom:5px; display:none;" id="ooop-<?php echo $value3e["idstring"]; ?>" > </div>
		   <input type="button" style="background-color:green;padding:5px;color:#fff;" name="std" value="Item Found" onClick="dosname('<?php echo $value3e["idstring"]; ?>','<?php echo $temp[1]; ?>');" /> &nbsp;&nbsp;
		  <input type="button" style="background-color:red;padding:5px;color:#fff;" name="std" value="Substitute Needed" onClick="dosname2('<?php echo $value3e["idstring"]; ?>');" />
		   <br/>
           <div id="sso-<?php echo $value3e["idstring"]; ?>" <?php if( $rows274=="" && $rows22brr!="2" ) { ?> style="display:none;" <?php } ?> >
           <div style="font-weight:bold;padding:5px;font-size:12px;"> Substitute Info </div>
		  <textarea style="border:1px solid #000;height:auto;width:250px;margin-left:5px;" name="snote-<?php echo $value3e["idstring"]; ?>" id="snote-<?php echo $value3e["idstring"]; ?>"  onBlur="dosname('<?php echo $value3e["idstring"]; ?>','<?php echo $temp[1]; ?>');" ><?php echo $rows274; ?></textarea>
             <?php if($pick=="No") { ?>
		  <div style="font-weight:bold;padding:5px;font-size:12px;color:red;">Accept Substitute : <?php echo $pick; ?> <a href="<?php echo Mage::getBaseUrl(); ?>?&sh=<?php echo $rows2745; ?>&item_id=<?php echo $value3e["item_id"]; ?>" target="_blank" style="font-weight:bold;padding:5px;font-size:12px;color:green;text-decoration:underline"> Sub. History </a>  </div>
		  <?php } else { ?>
		  <div style="font-weight:bold;padding:5px;font-size:12px;color:black;">Accept Substitute : <?php echo $pick; ?>  <a href="<?php echo Mage::getBaseUrl(); ?>?&sh=<?php echo $rows2745; ?>&item_id=<?php echo $value3e["item_id"]; ?>" target="_blank" style="font-weight:bold;padding:5px;font-size:12px;color:green;text-decoration:underline"> Sub. History </a>  </div>
		  <?php } ?>
		  <a href="?increid=<?php echo $ob["increment_id"]; ?>" target="_blank" style="margin-top:15px;font-size:12px;text-decoration:underline" > View Order (+) </a>
		  <?php if($ccde->getPrimaryBillingAddress()!="") { ?>
					<span style="font-weight:bold;padding:5px;font-size:12px;color:black;"> &nbsp; Ph. <?php echo $ccde->getPrimaryBillingAddress()->getTelephone(); ?>
					</span>
		<?php } ?>
            </div>
           <div style="clear:both; background:#000;margin-top:5px;margin-bottom:5px;height:1px">&nbsp;</div>
          <?php
		  $sql22b = "SELECT log2 FROM supervision where item_id='".$value3e["item_id"]."' and ddate='".$_GET["ode"]."'";
		  $rows22b = $connection->fetchOne($sql22b);
		 ?>
		   <div id="nfdiv-<?php echo $temp[1]; ?>" style="float:left; margin-left:10px; margin-top:5px;  width:106px; <?php if($rows22brr=="2" || $rows22brr=="0" ) { ?> display:none; <?php } ?>"  >
				<?php
					  if($rows22b!='1')
					   {
					   ?>
					   <input type="button" style="background-color:black;padding:5px;color:#fff;" name="std" value="Packed" onClick="dopacked('<?php echo $temp[1]; ?>','<?php echo $_GET["ode"]; ?>');" />
					   <?php } else {
					    echo '<div style="font-weight:bold;color:green;padding:5px;width:106px;" > Already Packed </div>';
					    } ?>
					   </div>


            <div style="clear:both;">&nbsp;</div>
            <div style="float:left;width:25%">
          <span style="font-weight:bold;font-size:12px;color:black;"><?php $sel_customer_fname="select shipping_name from mg_sales_flat_order_grid where entity_id='".$ob->getId()."' limit 0,1";
					echo $customer_fname=$connection->fetchOne($sel_customer_fname);
                      ?></span>
          <?php
		  $sel_vname="select drivername from mg_shipping_delivery_window where order_number='".$ob["increment_id"]."'";
		  $vname=$connection->fetchOne($sel_vname);

		  $sel_rc="select rc from mg_shipping_delivery_window where order_number='".$ob["increment_id"]."'";
		  $rc=$connection->fetchOne($sel_rc);

		  if($vname!="")
		  {
		  ?>
		  <span style="font-size:14px;font-weight:bold;color:green;"><br/><b> <?php echo $vname; ?></b> # <?php echo $rc; ?></span>
		  <?php
		  }
		  ?>
          </div>
            <div style="float:left;width:62%;margin-left:5px; border-left:1px solid #000; padding-left:5px; ">


                <?php
				$sqlstatus  = "SELECT d FROM packinginfo  where order_number='".$value3e["order_number"]."'";
		        $number["d"] = $connection->fetchOne($sqlstatus);

				$sqlstatus  = "SELECT cf FROM packinginfo  where order_number='".$value3e["order_number"]."'";
		        $number["cf"] = $connection->fetchOne($sqlstatus);

				$sqlstatus  = "SELECT bag FROM packinginfo  where order_number='".$value3e["order_number"]."'";
		        $number["bag"] = $connection->fetchOne($sqlstatus);

				$sqlstatus  = "SELECT loose FROM packinginfo  where order_number='".$value3e["order_number"]."'";
		        $number["loose"] = $connection->fetchOne($sqlstatus);

				$sqlstatus  = "SELECT cold_bin FROM packinginfo  where order_number='".$value3e["order_number"]."'";
		        $number["cold_bin"] = $connection->fetchOne($sqlstatus);

				$sqlstatus  = "SELECT cold_bag FROM packinginfo  where order_number='".$value3e["order_number"]."'";
		        $number["cold_bag"] = $connection->fetchOne($sqlstatus);


				$sqlstatus  = "SELECT frozen_bin FROM packinginfo  where order_number='".$value3e["order_number"]."'";
		        $number["frozen_bin"] = $connection->fetchOne($sqlstatus);

				$sqlstatus  = "SELECT frozen_bag FROM packinginfo  where order_number='".$value3e["order_number"]."'";
		        $number["frozen_bag"] = $connection->fetchOne($sqlstatus);

				?>



			    <div style="color:green;font-weight:bold;font-size:14px" id="updateinfo"></div>

				<div style="margin-top:10px;margin-bottom:10px;background:green;color:#fff;font-weight:bold;width:98%;text-align:center;">Dry Containers</div>

				<b>Clear/Blue Bins :</b> &nbsp;<input type="text" style="width:180px;height: 25px;font-size: 17px;font-weight: bold;margin-bottom:5px;resize:both;" name="txt-d-<?php echo $value3e["order_number"]; ?>" id="txt-d-<?php echo $value3e["order_number"]; ?>" value="<?php echo $number["d"]; ?>" onBlur="dopackinfo('<?php echo $value3e["order_number"]; ?>','<?php echo $new_cust; ?>');" />
				<br/>

				<b>Clear Bags :</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="txt-b-<?php echo $value3e["order_number"]; ?>" id="txt-b-<?php echo $value3e["order_number"]; ?>" style="width:180px;height: 25px;font-size: 17px;font-weight: bold;margin-bottom:5px;resize:both;" value="<?php echo $number["bag"]; ?>" onBlur="dopackinfo('<?php echo $value3e["order_number"]; ?>','<?php echo $new_cust; ?>');" />
				<br/>
				<b>Loose :</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="txt-l-<?php echo $value3e["order_number"]; ?>" id="txt-l-<?php echo $value3e["order_number"]; ?>" value="<?php echo $number["loose"]; ?>" style="width:180px;height: 25px;font-size: 17px;font-weight: bold;margin-bottom:5px;resize:both;" onBlur="dopackinfo('<?php echo $value3e["order_number"]; ?>','<?php echo $new_cust; ?>');" />
				<br/>


				<div style="margin-top:10px;margin-bottom:10px;background:green;color:#fff;font-weight:bold;width:98%;text-align:center;">Cold Containers</div>

				<b>Gray Bins :</b> &nbsp;<input type="text" name="txt-cold_bin-<?php echo $value3e["order_number"]; ?>" id="txt-cold_bin-<?php echo $value3e["order_number"]; ?>" onBlur="dopackinfo('<?php echo $value3e["order_number"]; ?>','<?php echo $new_cust; ?>');" value="<?php echo $number["cold_bin"]; ?>" style="width:180px;height: 25px;font-size: 17px;font-weight: bold;margin-bottom:5px;resize:both;" />
				<br/>
				<b>Black Bags :</b> <input type="text" name="txt-cold_bag-<?php echo $value3e["order_number"]; ?>" id="txt-cold_bag-<?php echo $value3e["order_number"]; ?>" onBlur="dopackinfo('<?php echo $value3e["order_number"]; ?>','<?php echo $new_cust; ?>');" value="<?php echo $number["cold_bag"]; ?>" style="width:180px;height: 25px;font-size: 17px;font-weight: bold;margin-bottom:5px;resize:both;" />

				<div style="margin-top:10px;margin-bottom:10px;background:green;color:#fff;font-weight:bold;width:98%;text-align:center;">Frozen  Containers</div>
				<b>Bins :</b> &nbsp;<input type="text" name="txt-frozen_bin-<?php echo $value3e["order_number"]; ?>" id="txt-frozen_bin-<?php echo $value3e["order_number"]; ?>" onBlur="dopackinfo('<?php echo $value3e["order_number"]; ?>','<?php echo $new_cust; ?>');" value="<?php echo $number["frozen_bin"]; ?>" style="width:180px;height: 25px;font-size: 17px;font-weight: bold;margin-bottom:5px;resize:both;" />
				<br/>
				<b>Black Bags :</b> <input type="text" name="txt-frozen_bag-<?php echo $value3e["order_number"]; ?>" id="txt-frozen_bag-<?php echo $value3e["order_number"]; ?>" onBlur="dopackinfo('<?php echo $value3e["order_number"]; ?>','<?php echo $new_cust; ?>');" value="<?php echo $number["frozen_bag"]; ?>" style="width:180px;height: 25px;font-size: 17px;font-weight: bold;margin-bottom:5px;resize:both;" />

                </div>
                <div style="clear:both;"></div>
             <?php
                $sel_refunds="select refund from `mg_sales_flat_order_item` where `item_id`='".$value3e['item_id']."'";
			    $refund = $connection->fetchOne($sel_refunds);
			   ?>
			<div style="color:red;margin-top:15px;"> <input type="checkbox" name="chkref-<?php echo $value3e['item_id']; ?>" id="chkref-<?php echo $value3e['item_id']; ?>" <?php if($refund==1) { echo "checked"; } ?> value="<?php echo $value3e['item_id']; ?>" onClick="dorefund('<?php echo $value3e['item_id']; ?>','<?php echo $value3e["idstring"]; ?>')" /> Price Adjustment Needed </div>
            </div>
          </td>
          </tr>
          <tr style="height:5px;"><td colspan="2" style="background:#7e7e7e;height:5px;"></td></tr>
	  <?php
	   }
	   ?>
	   </table>
	   <?php
	   }
	       else
		   {
		      echo'<div style="width:100%;text-align:center;margin-top:10px;color:red;"> No Item Found !</div>';
		   }
	   }
?>