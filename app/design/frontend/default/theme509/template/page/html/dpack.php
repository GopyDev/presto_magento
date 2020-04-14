<?php
      if(isset($_GET["gt"]) && $_GET["gt"]=='dpack' ) {

        // $_GET["ode"] = '17_05_2016';

	    $sel_order_picker = "select picker_id from mg_order_picker where window LIKE '%".$_GET["ode"]."' and order_id > 0 group by picker_id";
	    $rs_order_picker  =  $connection->fetchAll($sel_order_picker); ?>
	<div id="drypackdiv" style="margin-top:15px;margin-bottom:15px;">
	Dry Pack List :-
	 <?php
	 foreach ($rs_order_picker as $order_picker) {

	                 $sql_picker="select name from `mg_pickers` where id='".$order_picker["picker_id"]."'";
		             $picker_name=$connection->fetchOne($sql_picker);
	 ?>
	     <a href="<?php echo Mage::getBaseUrl(); ?>?gt=dpack&picker2=<?php echo $order_picker["picker_id"]; ?>" style="font-size:16px !important ;"><?php echo $picker_name; ?></a>&nbsp;&nbsp;
	 <?php
	 }
	 ?>
	 </div>
	 <?php }
	  if(isset($_GET["picker2"]) && $_GET["picker2"]!="" && $_GET["gt"]=='dpack' ) {
	                 $_GET["picker"] = $_GET["picker2"];
	                 $sql_picker="select name from `mg_pickers` where id='".$_GET["picker2"]."'";
		             $picker_name=$connection->fetchOne($sql_picker);
					 $sql3eb = "SELECT SUM(msi.qty_ordered) as gpb,mog.shipping_name,mcv.value as fname,mcv2.value as lname,msi.`order_id`,mog.customer_id,msw.drivername,msw.order_number FROM `mg_sales_flat_order_item` msi
					left join mg_sales_flat_order_grid mog on msi.`order_id` = mog.`entity_id`
					left join mg_shipping_delivery_window msw on mog.`increment_id` = msw.`order_number`
					left join mg_order_picker mop on mog.`increment_id` = mop.`order_number`
					left join mg_catalog_product_entity_int mei on msi.product_id = mei.entity_id and mei.attribute_id =  251 and mei.store_id = 0
					left join mg_catalog_product_entity_int rr on msi.product_id = rr.entity_id and rr.attribute_id =  252 and rr.store_id = 0
					left join mg_catalog_product_entity_int qrr on msi.product_id = qrr.entity_id and qrr.attribute_id =  253 and rr.store_id = 0
					left join mg_eav_attribute_option_value rr2 on rr.value = rr2.option_id and rr2.store_id = 0
					left join mg_eav_attribute_option_value rr3 on mei.value = rr3.option_id and rr3.store_id = 0
					left join mg_eav_attribute_option_value rr4 on qrr.value = rr4.option_id and rr4.store_id = 0
					left join mg_customer_entity_varchar mcv on mog.customer_id = mcv.entity_id and mcv.attribute_id = 5
					left join mg_customer_entity_varchar mcv2 on mog.customer_id = mcv2.entity_id and mcv2.attribute_id = 7
					where msw.`window` LIKE '%".$_GET["ode"]."' and mop.picker_id='".$_GET["picker2"]."' and ( mei.value=141 or mei.value=143 ) group by mog.`increment_id` order by gpb ";
					$rows3ek = $connection->fetchAll($sql3eb);
	 ?>
	 Customer List :-
	   <?php  foreach ($rows3ek as $value3ek) {

	                $order4444 = Mage::getModel("sales/order")->load($value3ek["order_id"]); //load order by order id
                    $shipping_address = $order4444->getShippingAddress();
					$kbr=explode(".",$value3ek["gpb"]);

					$checkce = "SELECT count(msi.item_id) FROM `mg_sales_flat_order_item` msi
										left join mg_sales_flat_order_grid mog on msi.`order_id` = mog.`entity_id`
										left join mg_shipping_delivery_window msw on mog.`increment_id` = msw.`order_number`
										left join mg_order_picker mop on mog.`increment_id` = mop.`order_number`
										left join mg_catalog_product_entity_int mei on msi.product_id = mei.entity_id and mei.attribute_id =  251 and mei.store_id = 0
										left join mg_catalog_product_entity_int rr on msi.product_id = rr.entity_id and rr.attribute_id =  252 and rr.store_id = 0
										left join mg_catalog_product_entity_int qrr on msi.product_id = qrr.entity_id and qrr.attribute_id =  253 and rr.store_id = 0
										left join mg_eav_attribute_option_value rr2 on rr.value = rr2.option_id and rr2.store_id = 0
										left join mg_eav_attribute_option_value rr3 on mei.value = rr3.option_id and rr3.store_id = 0
										left join mg_eav_attribute_option_value rr4 on qrr.value = rr4.option_id and rr4.store_id = 0
										left join mg_customer_entity_varchar mcv on mog.customer_id = mcv.entity_id and mcv.attribute_id = 5
										left join mg_customer_entity_varchar mcv2 on mog.customer_id = mcv2.entity_id and mcv2.attribute_id = 7

										where ( mei.value=141 or mei.value=143 )  and msi.`order_id`  = '".$value3ek["order_id"]."' ";
					 $checkcde = $connection->fetchOne($checkce);
					 $checkc = "SELECT count(msi.item_id) FROM `mg_sales_flat_order_item` msi
										left join mg_sales_flat_order_grid mog on msi.`order_id` = mog.`entity_id`
										left join mg_shipping_delivery_window msw on mog.`increment_id` = msw.`order_number`
										left join mg_order_picker mop on mog.`increment_id` = mop.`order_number`
										left join mg_catalog_product_entity_int mei on msi.product_id = mei.entity_id and mei.attribute_id =  251 and mei.store_id = 0
										left join mg_catalog_product_entity_int rr on msi.product_id = rr.entity_id and rr.attribute_id =  252 and rr.store_id = 0
										left join mg_catalog_product_entity_int qrr on msi.product_id = qrr.entity_id and qrr.attribute_id =  253 and rr.store_id = 0
										left join mg_eav_attribute_option_value rr2 on rr.value = rr2.option_id and rr2.store_id = 0
										left join mg_eav_attribute_option_value rr3 on mei.value = rr3.option_id and rr3.store_id = 0
										left join mg_eav_attribute_option_value rr4 on qrr.value = rr4.option_id and rr4.store_id = 0
										left join mg_customer_entity_varchar mcv on mog.customer_id = mcv.entity_id and mcv.attribute_id = 5
										left join mg_customer_entity_varchar mcv2 on mog.customer_id = mcv2.entity_id and mcv2.attribute_id = 7

										left join supervision ss on ss.item_id = msi.item_id
										where ( mei.value=141 or mei.value=143 )  and msi.`order_id`  = '".$value3ek["order_id"]."' and ss.log2=1 ";
					$checkcd = $connection->fetchOne($checkc);
					$checkcd = $checkcde - $checkcd;
		?>
		<?php if($checkcd<=0) { ?>
		<a href="<?php echo Mage::getBaseUrl(); ?>?gt=dpack&picker2=<?php echo $_GET["picker2"]; ?>&custid=<?php echo $value3ek["order_id"]; ?>" style="color:green;text-decoration:underline;display:block;padding-top:5px;"><?php echo $shipping_address["firstname"]." ".$shipping_address["lastname"]; ?> # <?php echo $value3ek["order_number"]; ?> ( <?php echo $kbr[0]; ?> Items ) </a>
		<?php } else { ?>
		<a href="<?php echo Mage::getBaseUrl(); ?>?gt=dpack&picker2=<?php echo $_GET["picker2"]; ?>&custid=<?php echo $value3ek["order_id"]; ?>" style="color:red;text-decoration:underline;display:block;padding-top:5px;"><?php echo $shipping_address["firstname"]." ".$shipping_address["lastname"]; ?> # <?php echo $value3ek["order_number"]; ?> ( <?php echo $kbr[0]; ?> Items ) </a>
		<?php } ?>
		<?php }
		if(isset($_GET["custid"])) {
		?>
         <div id="tbldrypack" style="padding-top:10px;;margin-top:25px;">
                <?php
	                    $green2 = 0;
					    $_GET["picker"] =  $_GET["picker2"];

						$order4444 = Mage::getModel("sales/order")->load($_GET["custid"]); //load order by order id
						$shipping_address = $order4444->getShippingAddress();
						$cust_idb = $order4444->getCustomerId();
					    $bpil = 0;
					    $sql3e = "SELECT msi.item_id,mei.value,msi.product_id,msi.qty_ordered as qotd,msi.qty_ordered as qotd,msi.name,msi.sku,msi.`order_id`,mop.picker_id,rr.value as val,rr2.value as sval,rr3.value as tval,rr4.value as uval,mcv.value as fname,mcv2.value as lname,msw.`order_number`,msw.drivername,msw.rc,mog.customer_id FROM `mg_sales_flat_order_item` msi
										left join mg_sales_flat_order_grid mog on msi.`order_id` = mog.`entity_id`
										left join mg_shipping_delivery_window msw on mog.`increment_id` = msw.`order_number`
										left join mg_order_picker mop on mog.`increment_id` = mop.`order_number`
										left join mg_catalog_product_entity_int mei on msi.product_id = mei.entity_id and mei.attribute_id =  251 and mei.store_id = 0
										left join mg_catalog_product_entity_int rr on msi.product_id = rr.entity_id and rr.attribute_id =  252 and rr.store_id = 0
										left join mg_catalog_product_entity_int qrr on msi.product_id = qrr.entity_id and qrr.attribute_id =  253 and rr.store_id = 0
										left join mg_eav_attribute_option_value rr2 on rr.value = rr2.option_id and rr2.store_id = 0
										left join mg_eav_attribute_option_value rr3 on mei.value = rr3.option_id and rr3.store_id = 0
										left join mg_eav_attribute_option_value rr4 on qrr.value = rr4.option_id and rr4.store_id = 0
										left join mg_customer_entity_varchar mcv on mog.customer_id = mcv.entity_id and mcv.attribute_id = 5
										left join mg_customer_entity_varchar mcv2 on mog.customer_id = mcv2.entity_id and mcv2.attribute_id = 7
										where msw.`window` LIKE '%".$_GET["ode"]."' and mop.picker_id='".$_GET["picker"]."' and ( mei.value=141 or mei.value=143 )  and msi.`order_id`  = '".$_GET["custid"]."' order by rr3.value,rr2.value desc,rr4.value desc,msi.name";
					$drypacklist[$green2] = $shipping_address["firstname"]." ".$shipping_address["lastname"];
					$drycustlist[$green2] = $value3ek["customer_id"];
?>
    <br/>
	<table border=1 cellpadding="5" cellspacing="5" id="tbl-<?php echo $drypacklist[$green2]; ?>" style="border-collapse:collapse;<?php if($green2>=0) { echo 'page-break-before: always;'; } ?>font-size:11px;" >
				   <tr><td colspan=3 align="center" style="font-size:15px;font-weight:bold;background-color:black;color:#fff;"><b>Dry List</b> <b> <?php echo str_replace("'","",$shipping_address["firstname"]); ?> <?php echo str_replace("'","",$shipping_address["lastname"]); ?>  ( <?php echo $picker_name; ?> ) </b></td></tr>
				   <tr>
				   <th style="text-align:left;width:600px;text-align:left;" >Product Name</th>
				   <th style="text-align:left;width:100px;">Qty</th>
				   </tr>
                    <?php
                    $rows3e = $connection->fetchAll($sql3e);
	                $kbr = "";
					foreach ($rows3e as $value3e) {
					 $bpil = $bpil + $value3e["qotd"];
					 $green = $green + 1;
					 $qrt = explode(".",$value3e["qotd"]);

					 $sql22b = "SELECT status FROM supervision where item_id='".$value3e["item_id"]."' and ddate='".$_GET["ode"]."'";
		             $rows22b = $connection->fetchOne($sql22b);

					if($rows22b=="")
					{
					   if($qrt["0"]>=2)
					   {
					   		$bgr='#e0e0e0';
					   }
					   else
					   {
					        $bgr='#fff';
					   }
					}
					else if($rows22b=='found')
					{
					   $bgr='#d5ffd5';
					}
					else
					{
					   $sql33="select `log` from `supervision` where item_id='".$value3e["item_id"]."' and ddate='".$_GET["ode"]."'";
					   $rows274 = $connection->fetchOne($sql33);

					   if($rows274==1)
					   {
						  $bgr='#d9d9d9';
					   }
					   else if($rows274==2)
					   {
						  $bgr='#ffd9d9';
					   }
					   else if($rows274==3)
					   {
						  $bgr='#d5ffd5';
					   }
					   else
					   {
					      $bgr='#ffd9d9';
					   }
					}
					 if($value3e["tval"]!=$kbr)
					  {
					   echo "<tr><td colspan='3' style='text-align:center;font-weight:bold;font-size:15px;color:red;'> Product Type : ".$value3e["tval"]."</td></tr>";
					  }
					$kbr = $value3e["tval"];
					$new = 'drypack-'.$value3e["item_id"].'-'.$_GET["picker"].'-'.$_GET["ode"];
?>
                    <tr <?php if($qrt["0"]==2) { ?> style="font-weight:bold;background:<?php echo $bgr; ?>" <?php } ?> <?php if($qrt["0"]>=3) { ?> style="font-weight:bold;font-style:italic;background:<?php echo $bgr; ?>" <?php } ?>  <?php if($qrt["0"]==1) { ?> style="background:<?php echo $bgr; ?>" <?php } ?> id="trid-<?php echo $new; ?>"  >

					<?php
				    $sql2 = "SELECT idstring FROM supervision where item_id='".$value3e["item_id"]."' and ddate='".$_GET["ode"]."'";
		            $rows2 = $connection->fetchOne($sql2);
			        ?>
					<td>
					<span style="font-size:14px;font-weight:bold;">
					<?php echo $value3e["name"]; ?>
					</span>
					<div id="div-drypack-<?php echo $value3e["item_id"]; ?>-<?php echo $_GET["picker"]; ?>-<?php echo $_GET["ode"]; ?>">
					<?php if($rows2=="") { ?>

					 <textarea style="border:1px solid #000;overflow:auto;" name="dpack-<?php echo $value3e["item_id"]; ?>-<?php echo $_GET["picker"]; ?>-<?php echo $_GET["ode"]; ?>" id="dpack-<?php echo $value3e["item_id"]; ?>-<?php echo $_GET["picker"]; ?>-<?php echo $_GET["ode"]; ?>" ></textarea>
					 <br/> <input type="button" style="background-color:red;padding:5px;color:#fff;" name="std" value="Not Found" onClick="dohiren('drypack-<?php echo $value3e["item_id"]; ?>-<?php echo $_GET["picker"]; ?>-<?php echo $_GET["ode"]; ?>','<?php echo str_replace("'","",$shipping_address["firstname"]); ?> <?php echo str_replace("'","",$shipping_address["lastname"]); ?>','<?php echo $_GET["ode"]; ?>','<?php echo $_GET["picker"]; ?>','<?php echo $qrt["0"]; ?>','<?php echo $value3e["item_id"]; ?>');" />  &nbsp; <input type="button" style="background-color:green;padding:5px;color:#fff;"  value="Found" onClick="dohiren2h('drypack-<?php echo $value3e["item_id"]; ?>-<?php echo $_GET["picker"]; ?>-<?php echo $_GET["ode"]; ?>','<?php echo str_replace("'","",$shipping_address["firstname"]); ?> <?php echo str_replace("'","",$shipping_address["lastname"]); ?>','<?php echo $_GET["ode"]; ?>','<?php echo $_GET["picker"]; ?>','<?php echo $qrt["0"]; ?>','<?php echo preg_replace('#[^\w()/.%\-&]#',"",$value3e["name"]); ?>','<?php echo $value3e["item_id"]; ?>');" />

					 <?php } else {

				 $sql22b = "SELECT status FROM supervision where item_id='".$value3e["item_id"]."' and ddate='".$_GET["ode"]."'";
		         $rows22b = $connection->fetchOne($sql22b);

                       if($rows22b=='found')
					   {
					       $sql22 = "SELECT note FROM supervision where item_id='".$value3e["item_id"]."' and ddate='".$_GET["ode"]."'";
		                   $rows22 = $connection->fetchOne($sql22);

							  echo '<span style="color:green;font-weight:bold;" >  Status :- Found <br/> </span>';

							  if($rows22!="")
							  {

								  echo '<span style="color:green;font-weight:bold;" > Note :- '.$rows22.'</span>';
							  }
					   }
					   else
					   {

					       $sql33="select `log` from `supervision` where item_id='".$value3e["item_id"]."' and ddate='".$_GET["ode"]."'";
		                   $rows274 = $connection->fetchOne($sql33);

						   if($rows274=='3')
						   {
						      echo '<span style="color:green;font-weight:bold;" >  Status :- Found <br/> </span>';
						   }

						   if($rows274=='1')
						   {
						      echo '<span style="color:#999;font-weight:bold;" >  Status :- Substituted <br/> </span>';
						   }

		                   else
						   {
							   $sql22 = "SELECT note FROM supervision where item_id='".$value3e["item_id"]."' and ddate='".$_GET["ode"]."'";
							   $rows22 = $connection->fetchOne($sql22);

							  if($rows22!="")
							  {
								  echo '<span style="color:red;font-weight:bold;" > Note :- '.$rows22.'</span>';
							  }
							}
					     }
					 }

					 $_product = Mage::getModel('catalog/product')->load($value3e["product_id"]);
					 $tag=$_product->getData('removetag');
					   ?>
					  </div>
					  <?php if($tag==1) { ?>
					  <div style="background:red;color:#fff;font-weight:bold;padding:4px;font-size:14px;"> Remove Price Tag  </div>
					   <?php } ?>


					   <div id="cfegreen-<?php echo $value3e["item_id"]; ?>">

					    <?php $sql22b = "SELECT log2 FROM supervision where item_id='".$value3e["item_id"]."' and ddate='".$_GET["ode"]."'";
		              $rows22b = $connection->fetchOne($sql22b);
					  if($rows22b!='1'  )
					   {
					   ?>

					   <?php if($bgr=='#d5ffd5' )
					   {
					   ?>

					   <div id="packdiv-<?php echo $value3e["item_id"]; ?>" style="display:block">

					   <?php } else { ?>

					   <div id="dpackdiv-<?php echo $value3e["item_id"]; ?>" style="display:none;">

					   <?php } ?>

					   <input type="button" style="background-color:black;padding:5px;color:#fff;" name="std" value="Packed" onClick="dohiren32('drypack-<?php echo $value3e["item_id"]; ?>-<?php echo $_GET["picker"]; ?>-<?php echo $_GET["ode"]; ?>','<?php echo str_replace("'","",$shipping_address["firstname"]); ?> <?php echo str_replace("'","",$shipping_address["lastname"]); ?>','<?php echo $_GET["ode"]; ?>','<?php echo $_GET["picker"]; ?>','<?php echo $qrt["0"]; ?>','<?php echo $value3e["item_id"]; ?>');" />

					   </div>



					   <?php } else {
					    echo '<span style="color:green;font-weight:bold;" > Already Packed </span>';
					    } ?>
					   </div>


					</td>
					<td style="font-weight:bold;font-size:15px;color:red;padding-right:5px;<?php if($qrt["0"]>=2) { ?>
					background:#ffff01;color:#000; <?php } ?>"><?php echo $qrt["0"]; ?></td>
					</tr>
                <?php
					}
                 ?>


                 <?php if(trim($value3e["drivername"])=="Box Truck")
				   {
				       $vkt = "Van 2 - New";
				   }

				   if(trim($value3e["drivername"])=="Van 1")
				   {
				      $vkt = "Van 1 - Old";
				   }
			?>


<tr><td colspan="3" align="right" style="text-align:right;color:green;font-size:15px;padding-right:5px;">Total = <?php echo $bpil; ?> </td></tr>
<tr><td colspan="3"> <span style="color:red;font-weight:bold;font-size:15px;"> Vehicle Name : </span>  <?php echo $vkt; ?> # <?php echo $value3e["rc"]; ?> <br/> <span style="color:red;font-weight:bold;font-size:15px;">  Customer : </span> <?php echo str_replace("'","",$shipping_address["firstname"]); ?> <?php echo str_replace("'","",$shipping_address["lastname"]); ?>
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


				$sel_new_cust = "SELECT entity_id FROM mg_sales_flat_order_grid where customer_id='".$cust_idb."' and increment_id!='".$value3e["order_number"]."'";
		        $new_cust = $connection->fetchOne($sel_new_cust);

				if($new_cust=="")
				{
			?>

			<div style="margin-top:10px;margin-bottom:10px;background-color:red;color:#fff;font-weight:bold;font-size:12px;padding:5px;"> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="bck-<?php echo $value3e['order_number']; ?>" id="bck-<?php echo $value3e["order_number"]; ?>" onClick="dobpack('<?php echo $value3e["order_number"]; ?>');" /> Pack New Customer Brochure </div>

            <div id="pckdiv" style="color:red;font-size:14px;"> Dont Forget to Pack New Customer Brochure. </div>
			<?php
				}
			?>



			    <div style="color:green;font-weight:bold;font-size:14px" id="updateinfo"></div>

				<div style="margin-top:10px;margin-bottom:10px;background:green;color:#fff;font-weight:bold;width:98%;text-align:center;">Dry Containers</div>

				<b>Clear/Blue Bins :</b> &nbsp;<input type="text" style="width:380px;height: 25px;font-size: 17px;font-weight: bold;margin-bottom:5px;resize:both;" name="txt-d-<?php echo $value3e["order_number"]; ?>" id="txt-d-<?php echo $value3e["order_number"]; ?>" value="<?php echo $number["d"]; ?>" onBlur="dopackinfo('<?php echo $value3e["order_number"]; ?>','<?php echo $new_cust; ?>');" />
				<br/>

				<!-- <b>Gray Bins :</b> &nbsp;&nbsp;<input type="text" style="width:380px;height: 25px;font-size: 17px;font-weight: bold;margin-bottom:5px;resize:both;" name="txt-cf-<?php echo $value3e["order_number"]; ?>" id="txt-cf-<?php echo $value3e["order_number"]; ?>" value="<?php echo $number["cf"]; ?>" onBlur="dopackinfo('<?php echo $value3e["order_number"]; ?>','<?php echo $new_cust; ?>');" /> -->


				<b>Clear Bags :</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="txt-b-<?php echo $value3e["order_number"]; ?>" id="txt-b-<?php echo $value3e["order_number"]; ?>" style="width:380px;height: 25px;font-size: 17px;font-weight: bold;margin-bottom:5px;resize:both;" value="<?php echo $number["bag"]; ?>" onBlur="dopackinfo('<?php echo $value3e["order_number"]; ?>','<?php echo $new_cust; ?>');" />
				<br/>
				<b>Loose :</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="txt-l-<?php echo $value3e["order_number"]; ?>" id="txt-l-<?php echo $value3e["order_number"]; ?>" value="<?php echo $number["loose"]; ?>" style="width:380px;height: 25px;font-size: 17px;font-weight: bold;margin-bottom:5px;resize:both;" onBlur="dopackinfo('<?php echo $value3e["order_number"]; ?>','<?php echo $new_cust; ?>');" />
				<br/>


				<div style="margin-top:10px;margin-bottom:10px;background:green;color:#fff;font-weight:bold;width:98%;text-align:center;">Cold Containers</div>

				<b>Gray Bins :</b> &nbsp;<input type="text" name="txt-cold_bin-<?php echo $value3e["order_number"]; ?>" id="txt-cold_bin-<?php echo $value3e["order_number"]; ?>" onBlur="dopackinfo('<?php echo $value3e["order_number"]; ?>','<?php echo $new_cust; ?>');" value="<?php echo $number["cold_bin"]; ?>" style="width:380px;height: 25px;font-size: 17px;font-weight: bold;margin-bottom:5px;resize:both;" />
				<br/>
				<b>Black Bags :</b> <input type="text" name="txt-cold_bag-<?php echo $value3e["order_number"]; ?>" id="txt-cold_bag-<?php echo $value3e["order_number"]; ?>" onBlur="dopackinfo('<?php echo $value3e["order_number"]; ?>','<?php echo $new_cust; ?>');" value="<?php echo $number["cold_bag"]; ?>" style="width:380px;height: 25px;font-size: 17px;font-weight: bold;margin-bottom:5px;resize:both;" />

				<div style="margin-top:10px;margin-bottom:10px;background:green;color:#fff;font-weight:bold;width:98%;text-align:center;">Frozen  Containers</div>
				<b>Bins :</b> &nbsp;<input type="text" name="txt-frozen_bin-<?php echo $value3e["order_number"]; ?>" id="txt-frozen_bin-<?php echo $value3e["order_number"]; ?>" onBlur="dopackinfo('<?php echo $value3e["order_number"]; ?>','<?php echo $new_cust; ?>');" value="<?php echo $number["frozen_bin"]; ?>" style="width:380px;height: 25px;font-size: 17px;font-weight: bold;margin-bottom:5px;resize:both;" />
				<br/>
				<b>Black Bags :</b> <input type="text" name="txt-frozen_bag-<?php echo $value3e["order_number"]; ?>" id="txt-frozen_bag-<?php echo $value3e["order_number"]; ?>" onBlur="dopackinfo('<?php echo $value3e["order_number"]; ?>','<?php echo $new_cust; ?>');" value="<?php echo $number["frozen_bag"]; ?>" style="width:380px;height: 25px;font-size: 17px;font-weight: bold;margin-bottom:5px;resize:both;" />

				</td></tr>
</table>
<?php
$green2 = $green2 + 1 ;
           ?>
         </div>
	   <? } }
?>