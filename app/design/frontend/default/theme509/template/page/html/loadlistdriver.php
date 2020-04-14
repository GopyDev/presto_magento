<?php 

if(isset($_GET["gt"]) && $_GET["gt"]=="llist" ) {
  
  $kbt = date("d_m_Y");
  
$sel_route="select route from mg_shipping_delivery_window where `window` LIKE '%".$kbt."' and driver_email='".$customerData["email"]."' limit 0,1";
			 $route_name=$connection->fetchOne($sel_route);
			  
			  $sel_route="select driverid from mg_shipping_delivery_window where `window` LIKE '%".$kbt."' and driver_email='".$customerData["email"]."' limit 0,1";
			  $route_id=$connection->fetchOne($sel_route);
			  
			  
			 if($route_name!="")
			 {
			    $sel_std="select std from route_driver where route='".$route_name."' and rid='".$route_id."'";
				$std=$connection->fetchOne($sel_std);
				
				$sel_std2="select std2 from route_driver where route='".$route_name."' and rid='".$route_id."'";
				$std2=$connection->fetchOne($sel_std2);
				
				$sel_v="select driver from route_driver where route='".$route_name."' and rid='".$route_id."'";
				$vehicle=$connection->fetchOne($sel_v);
				
				$sel_startime="select starttime from route_driver where route='".$route_name."'and rid='".$route_id."'";
				$starttime=$connection->fetchOne($sel_startime);
				
				$sel_endtime="select endtime from route_driver where route='".$route_name."' and rid='".$route_id."'";
				$endtime=$connection->fetchOne($sel_endtime);
				
			 }

 ?>
         <div id="tbldrypack" style="padding-top:10px;;margin-top:25px;">
		 <table border="1">
		 <?php if($route_id==1) { ?>
		 <tr><td colspan=2 style="background:green;color:#fff;font-size:12px;text-align:center;" > Vehicle Name : Van 1 - Old </td></tr>
		 <?php } else if($route_id==2) { ?>
		 <tr><td colspan=2 style="background:green;color:#fff;font-size:12px;" > Vehicle Name : Van 2 - New </td></tr>
		 <?php } ?>
		 <tr>
		 <td>Order Information</td>
		 <td>Packing Information</td>
		 </tr>
                <?php  
                     $sql32 = "SELECT m.* FROM `mg_shipping_delivery_window` m  where m.`window` LIKE '%".$kbt."' and m.driverid='".$route_id."' order by m.`rc`";
					  $rows3ek = $connection->fetchAll($sql32); 
	                  $green2 = 0;		
					  foreach ($rows3ek as $value) {
					  $value["increment_id"]=$value["order_number"];
	       if($value["increment_id"]!="" && $value["increment_id"]!='100003190')
		   {
		       $sql2        = "SELECT entity_id FROM mg_sales_flat_order_grid where increment_id='".$value["order_number"]."'";
		       $rows2 = $connection->fetchOne($sql2);
			   
			   $sql2        = "SELECT status FROM mg_sales_flat_order_grid where increment_id='".$value["order_number"]."'";
		       $rowstatus   = $connection->fetchOne($sql2);

			   
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
										where ( mei.value=141 or mei.value=143 or mei.value=140 or mei.value=142 or mei.value=144 or mei.value=145 or mei.value=146 )  and msi.`order_id`  = '".$rows2."'  ";
					 $checkcde = $connection->fetchOne($checkc);
					 
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
										where ( mei.value=141 or mei.value=143 or mei.value=140 or mei.value=142 or mei.value=144 or mei.value=145 or mei.value=146 )  and msi.`order_id`  = '".$rows2."' and ss.log2=1 ";
					 $checkcd = $connection->fetchOne($checkc);
					 $total_check = $checkcde - $checkcd ;
			   $sql22        = "SELECT customer_id FROM mg_sales_flat_order_grid where increment_id='".$value["order_number"]."'";
		       $rows22 = $connection->fetchOne($sql22);
			   $customer = "" ;
			   $customer = Mage::getModel('customer/customer')->load($rows22); 
			   $sql222        = "SELECT entity_id FROM mg_sales_flat_order_grid where customer_id='".$rows22."' and increment_id!='".$value["order_number"]."'";
		       $rows222 = $connection->fetchOne($sql222);
			   $sql3        = "SELECT SUM(qty_ordered) FROM `mg_sales_flat_order_item` where `order_id`='".$rows2."'";
			   $rows3 = $connection->fetchOne($sql3); 
			   $order = Mage::getModel("sales/order")->load($rows2); //load order by order id 
			   $address = $order->getShippingAddress();
			   $shipping_info = $order->getShippingAddress()->getData();
			   $temp = explode("|",$value['window']);
			   $temp3 = explode(".",$rows3);
			   $check22 = 0;
			        $sql2g = "SELECT picker_id FROM mg_order_picker where order_number='".$value["order_number"]."'";
		            $rows2g = $connection->fetchOne($sql2g);
				    $sql2gk = "SELECT name FROM mg_pickers where id='".$rows2g."'";
		            $rows2gk = $connection->fetchOne($sql2gk);
			   $bid = $value["id"]; 
			   if($customer->getId()!="813" && $customer->getId()!="1205" && $rowstatus!='canceled' && $rows2g >=1 )
			   { 
	 ?>		   <tr <?php if($value["loaded"]=="1") { ?> style="background:#daebe1;" <?php } ?> id="trload-<?php echo $value["id"]; ?>" >
			   <td <?php if($rows222 == "" ) {  echo 'style="color:#1c60ff;"'; } ?> >
			   <span style="font-size:12px;font-weight:bold;">  <?php $custName = $address->getName(); ?> <?php if(trim($custName)=="") { echo $customer->getName(); } else { echo $custName; } ?> </span>
				<br/> <span style="color:green;font-weight:bold;"> <?php echo $rows2gk; ?></span>
			   </td>
			   <td <?php if($rows222 == "" ) {  echo 'style="color:#1c60ff;"'; } ?> >
			    <?php
				$sqlstatus  = "SELECT d FROM packinginfo  where order_number='".$value["order_number"]."'";
		        $number["d"] = $connection->fetchOne($sqlstatus);
				$sqlstatus  = "SELECT cf FROM packinginfo  where order_number='".$value["order_number"]."'";
		        $number["cf"] = $connection->fetchOne($sqlstatus);
				$sqlstatus  = "SELECT bag FROM packinginfo  where order_number='".$value["order_number"]."'";
		        $number["bag"] = $connection->fetchOne($sqlstatus);
				$sqlstatus  = "SELECT loose FROM packinginfo  where order_number='".$value["order_number"]."'";
		        $number["loose"] = $connection->fetchOne($sqlstatus);
				
				$sqlstatus  = "SELECT cold_bin FROM packinginfo  where order_number='".$value["order_number"]."'";
		        $number["cold_bin"] = $connection->fetchOne($sqlstatus);
				
				$sqlstatus  = "SELECT cold_bag FROM packinginfo  where order_number='".$value["order_number"]."'";
		        $number["cold_bag"] = $connection->fetchOne($sqlstatus);
				
				$sqlstatus  = "SELECT frozen_bin FROM packinginfo  where order_number='".$value["order_number"]."'";
		        $number["frozen_bin"] = $connection->fetchOne($sqlstatus);
				
				$sqlstatus  = "SELECT frozen_bag FROM packinginfo  where order_number='".$value["order_number"]."'";
		        $number["frozen_bag"] = $connection->fetchOne($sqlstatus);
				
			?>	
				
				<?php if($number["d"]!="")
				{
				?>
				Dry Bins :
				
				 <b> <span style="color:green;font-size:14px;" ><?php echo $number["d"]; ?></span> </b> 
				 <br/>
				 <?php } ?>
				
				<?php if($number["bag"]!="")
				{
				?>
				<b>Dry Bags :</b> 
				
				 <span style="color:green;font-size:14px;" ><?php  echo $number["bag"]; ?> </span>
				<br/>
				<?php } ?>
				<?php if($number["loose"]!="")
				{
				?>
				<b>Dry Loose :</b> 
				 <span style="color:green;font-size:14px;" ><?php  echo $number["loose"]; ?> </span>
				<br/>
				<?php } ?>
				
				
				<?php if($value["dryload"]!="1") { ?>
				<div id="trloadtext-<?php echo $value["id"]; ?>">
				<input type="button" style="background-color:black;padding:7px;color:#fff;font-weight:bold;" name="std" value="Load Dry Items" onClick="doloaded(<?php echo $bid; ?>);" />
				</div>
				<?php } else { ?>
				
				<div style="color:green;"> Dry Items Are Already loaded. </div>
				
				<?php } ?>
				<br/>
				
				<?php if($number["cold_bin"]!="")
				{
				?>
				<b>Cold Bin :</b> 
				 <span style="color:green;font-size:14px;" ><?php  echo $number["cold_bin"]; ?> </span>
				<br/>
				<?php } ?>
				
				<?php if($number["cold_bag"]!="")
				{
				?>
				<b>Cold Bag :</b> 
				 <span style="color:green;font-size:14px;" ><?php  echo $number["cold_bag"]; ?> </span>
				<br/>
				<?php } ?>
				
				<?php if($number["frozen_bin"]!="")
				{
				?>
				<b>Frozen Bin :</b> 
				 <span style="color:green;font-size:14px;" ><?php  echo $number["frozen_bin"]; ?> </span>
				<br/>
				<?php } ?>
				
				<?php if($number["frozen_bag"]!="")
				{
				?>
				<b>Frozen Bag :</b> 
				 <span style="color:green;font-size:14px;" ><?php  echo $number["frozen_bag"]; ?> </span>
				<br/>
				<?php } ?>
				
				<?php if($value["coldload"]!="1") { ?>
				<div id="trloadtext2-<?php echo $value["id"]; ?>">
				<input type="button" style="background-color:black;padding:7px;color:#fff;font-weight:bold;" name="std" value="Load C/F Items" onClick="doloaded2(<?php echo $bid; ?>);" />
				</div>
				<?php } else { ?>
				<div style="color:green;"> C/F Items Are Already loaded. </div>
				<?php } ?>
				
				<?php if($total_check>=1) { ?>
				<br/>
				<span style="color:red;"> Note :- Following items still need to be packed : <br/> </span>
				<div style="color:black;padding:5px;">
				<?php 
				$checkcb = "SELECT msi.name,rr2.value,msi.qty_ordered FROM `mg_sales_flat_order_item` msi
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
										where ( mei.value=141 or mei.value=143 or mei.value=140 or mei.value=142 or mei.value=144 or mei.value=145 or mei.value=146 )  and msi.`order_id`  = '".$rows2."' and ( ss.log2=0 or ss.log2 is null) ";
					 $checkcd = $connection->fetchAll($checkcb);
					 $countg = count($checkcd);
					 $cc=0;
					 if($countg>=1)
					 {
					 foreach ($checkcd as $value) {
					 
					 $cc=$cc+1;
					 ?>
					 <div style="color:black"> <?php echo $cc; ?> ). <?php echo $value["name"]; ?> ( Location : <?php echo $value["value"]; ?>)  ( Qty Ordered : <?php echo round($value["qty_ordered"],2); ?>) </div>
					 <?php
					 } } 
					 ?>
					 </div>
					 <?php
				  }
				?>
				</td>
				
			 </tr>
			   <?php	
			   }  
		     }
	     } ?>
		 </table>
         </div>
		<?php } 