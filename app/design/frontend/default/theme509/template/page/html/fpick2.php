 <?php
      if(isset($_GET["gt"]) && $_GET["gt"]=='fpick2' ) {

	$_GET["ode"]="25_02_2016";

    $sel_order_picker = "select picker_id from mg_order_picker where window LIKE '%".$_GET["ode"]."' and order_id > 0 group by picker_id";
	$rs_order_picker  =  $connection->fetchAll($sel_order_picker); ?>

	<div id="drypickdiv" style="margin-top:15px;margin-bottom:15px;">
	Frozen Pick List :-
	 <?php
	 foreach ($rs_order_picker as $order_picker) {

	                 $sql_picker="select name from `mg_pickers` where id='".$order_picker["picker_id"]."'";
		             $picker_name=$connection->fetchOne($sql_picker);
	 ?>
	     <a href="<?php echo Mage::getBaseUrl(); ?>?gt=fpick2&picker2=<?php echo $order_picker["picker_id"]; ?>"><?php echo $picker_name; ?></a>&nbsp;&nbsp;

	 <?php
	 }
	 ?>
	 </div>
	 <?php }
	  if(isset($_GET["picker2"]) && $_GET["picker2"]!="" && $_GET["gt"]=='fpick2' ){
	  ?>

	    <script type="text/javascript">
	  function obccheck() {
				jQuery('html, body').animate({
					scrollTop: jQuery('#location-'+jQuery('#bylocation').val()).offset().top
				}, 2000);
	}

	function backdrppick() {
	   jQuery('html, body').animate({
					scrollTop: jQuery('.page').offset().top
				}, 2000);
	}



	function dohirens3(id,cust,ddate,infopicker,itemqty,name,itemid)
	{
		  output = jQuery('#sfrozenpick-'+itemid).val();
		  if(output!="")
		  {
		  if(output==itemqty)
		  {
		     noteb = jQuery('#'+id.trim()).val();
			  jQuery('#div-'+id).html('<span style="font-weight:bold;color:#999;">Processing....</span>');
			  jQuery("#trid-"+id).css("background-color", "#d5ffd5");
			   jQuery.ajax({
				   url: "<?php echo Mage::getBaseUrl(); ?>ajaxupdates2.php?rand="+Math.random()+"&note="+noteb+"&cust="+cust+"&idstring="+id+"&ddate="+ddate+"&infopicker="+infopicker+"&itemqty="+itemqty+"&itemid="+itemid,
				   context: document.body
					}).done(function(data) {
					jQuery('#div-'+id).html('<span style="font-weight:bold;color:green;">Status :- Found<br/> </span>');
				 });

		  }

		 else if(output==0)
		  {
		      noteb = jQuery('#'+id.trim()).val();
			  jQuery('#div-'+id).html('<span style="font-weight:bold;color:#999;">Processing....</span>');
			  jQuery("#trid-"+id).css("background-color", "#ffd9d9");
			   jQuery.ajax({
				   url: "<?php echo Mage::getBaseUrl(); ?>ajaxupdates.php?rand="+Math.random()+"&note="+noteb+"&cust="+cust+"&idstring="+id+"&ddate="+ddate+"&infopicker="+infopicker+"&itemqty="+itemqty+"&itemid="+itemid,
				   context: document.body
					}).done(function(data) {
					jQuery('#div-'+id).html('<span style="font-weight:bold;color:red;">Status :- Not Found<br/> Note :- '+noteb+'</span>');
				 });
		  }
		  else
		  {
		      supernote="Have "+output+", Need "+(itemqty-output);
			  jQuery('#'+id.trim()).val(supernote);
		      noteb = jQuery('#'+id.trim()).val();
			  jQuery('#div-'+id).html('<span style="font-weight:bold;color:#999;">Processing....</span>');
			  jQuery("#trid-"+id).css("background-color", "#ffd9d9");
			   jQuery.ajax({
				   url: "<?php echo Mage::getBaseUrl(); ?>ajaxupdates3.php?rand="+Math.random()+"&note="+noteb+"&cust="+cust+"&idstring="+id+"&ddate="+ddate+"&infopicker="+infopicker+"&itemqty="+itemqty+"&itemid="+itemid+"&gqty="+output,
				   context: document.body
					}).done(function(data) {
					jQuery('#div-'+id).html('<span style="font-weight:bold;color:red;">Status :- Not Found<br/> Note :- '+noteb+'</span>');
				 });
		  }
		 }
	}

    </script>

	  <form method="get" action="<?php echo Mage::getBaseUrl(); ?>" style="margin-top:9px;margin-bottom:9px;">
	  <input type="hidden" name="picker2" value="<?php echo $_GET["picker2"]; ?>" />
	  <input type="hidden" name="gt" value="<?php echo $_GET["gt"]; ?>" />
	  Location : <select name="bylocation" id="bylocation" onChange="obccheck();" style="border:1px solid #000;">

	  <option value="">Select</option>
	  <?php
	  $sel_location="SELECT rr2.value as sval FROM `mg_sales_flat_order_item` msi
						left join mg_sales_flat_order_grid mog on msi.`order_id` = mog.`entity_id`
						left join mg_shipping_delivery_window msw on mog.`increment_id` = msw.`order_number`
						left join mg_order_picker mop on mog.`increment_id` = mop.`order_number`
						left join mg_catalog_product_entity_int mei on msi.product_id = mei.entity_id and mei.attribute_id =  251 and mei.store_id = 0
						left join mg_catalog_product_entity_int rr on msi.product_id = rr.entity_id and rr.attribute_id =  252 and rr.store_id = 0
						left join mg_eav_attribute_option_value rr2 on rr.value = rr2.option_id and rr2.store_id = 0
						where msw.`window` LIKE '%".$_GET["ode"]."' and mop.picker_id='".$_GET["picker2"]."' and ( mei.value=142 )  group by rr2.value";
	$location = $connection->fetchAll($sel_location);
	foreach ($location as $newloc) {
	?>
	<option value="<?php echo $newloc["sval"]; ?>" <?php if($_GET["bylocation"]==$newloc["sval"]) { ?> selected <?php } ?> ><?php echo $newloc["sval"]; ?></option>
	<?php
	}
	?>
	</select>
	</form>
	  <?php
	  $sql3e = "SELECT msi.item_id,mei.value,msi.product_id,msi.qty_ordered as qotd,msi.name,msi.sku,msi.`order_id`,mop.picker_id,rr.value as val,rr2.value as sval,rr3.value as tval,rr4.value as uval,mog.`increment_id`,
    ( CASE rr2.value
	WHEN '8' THEN 1
	WHEN '9' THEN 2
	WHEN 'Seafood' THEN 3
	WHEN 'Packed Meat' THEN 4
	WHEN 'Cut Meat' THEN 5
	WHEN 'Dairy' THEN 6
	ELSE 7
	END ) as abc FROM `mg_sales_flat_order_item` msi

							left join mg_sales_flat_order_grid mog on msi.`order_id` = mog.`entity_id`
							left join mg_shipping_delivery_window msw on mog.`increment_id` = msw.`order_number`
							left join mg_order_picker mop on mog.`increment_id` = mop.`order_number`
							left join mg_catalog_product_entity_int mei on msi.product_id = mei.entity_id and mei.attribute_id =  251 and mei.store_id = 0
							left join mg_catalog_product_entity_int rr on msi.product_id = rr.entity_id and rr.attribute_id =  252 and rr.store_id = 0
							left join mg_catalog_product_entity_int qrr on msi.product_id = qrr.entity_id and qrr.attribute_id =  253 and rr.store_id = 0
							left join mg_eav_attribute_option_value rr2 on rr.value = rr2.option_id and rr2.store_id = 0
							left join mg_eav_attribute_option_value rr3 on mei.value = rr3.option_id and rr3.store_id = 0
							left join mg_eav_attribute_option_value rr4 on qrr.value = rr4.option_id and rr4.store_id = 0
							where msw.`window` LIKE '%".$_GET["ode"]."' and mop.picker_id='".$_GET["picker2"]."' and ( mei.value=142 ) group by msi.item_id order by abc,rr4.value,msi.name";

							$sql_picker="select name from `mg_pickers` where id='".$_GET["picker2"]."'";
		                    $picker_name=$connection->fetchOne($sql_picker);
					        $_GET["picker"] = $_GET["picker2"];
		?>

		<div id="tblfrozenpick" style="padding-top:10px;">
	    <table border=1 cellpadding="5" cellspacing="5" style="border-collapse:collapse;page-break-before:always;font-size:11px;" >
				  <tr <?php if($qrt["0"]==2) { ?> style="background:#e0e0e0;font-weight:bold;" <?php } ?> <?php if($qrt["0"]>=3) { ?> style="background:#e0e0e0;font-weight:bold;font-style:italic;" <?php } ?> ><td colspan=6 align="center"><b>Frozen Pick List ( <?php echo $picker_name; ?> ) </b></td></tr>
				   <tr>
                     <th style="text-align:left;width:430px;" >Product Name</th>
				     <th style="text-align:left;width:25px;">Qty</th> <th style="text-align:left;width:225px;">Customer Name</th>
				   </tr>
                    <?php
					$rows3e = $connection->fetchAll($sql3e);
					$green = 0;
					$bpil = 0;
					$kbr = "";
					foreach ($rows3e as $value3e) {

					$sel_customer_fname="select customer_firstname from mg_sales_flat_order where entity_id='".$value3e["order_id"]."'";
					 $customer_fname=$connection->fetchOne($sel_customer_fname);

                    $shipping_address["firstname"] = $customer_fname;


					$sel_customer_fname="select customer_lastname from mg_sales_flat_order where entity_id='".$value3e["order_id"]."'";
					 $customer_fname=$connection->fetchOne($sel_customer_fname);

                    $shipping_address["lastname"] = $customer_fname;

					$bpil = $bpil + $value3e["qotd"];
					$green = $green + 1;
					$qrt = explode(".",$value3e["qotd"]);
					 if($value3e["sval"]!=$kbr)
					{
					   echo "<tr id='location-".$value3e["sval"]."' ><td colspan='3' style='text-align:center;font-size:12px;font-weight:bold;color:#008000;'> Location : ".$value3e["sval"]." ( <a href='javascript:void(0);' onClick='backdrppick();' > Go to Top </a> ) </td></tr>";
					}
					$kbr = $value3e["sval"];

					$new = 'frozenpick-'.$value3e["item_id"].'-'.$_GET["picker"].'-'.$_GET["ode"];
				    $sql2 = "SELECT idstring FROM supervision where idstring='".$new."'";
		            $rows2 = $connection->fetchOne($sql2);

					$rows2745 = $value3e["product_id"];

                    $sel_image="select small_image from mg_catalog_product_flat_1 where entity_id='".$rows2745."'";
					$image=$connection->fetchOne($sel_image);

					$sel_url="select url_path from mg_catalog_product_flat_1 where entity_id='".$rows2745."'";
					$url=$connection->fetchOne($sel_url);

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

			       ?>

                    <tr  <?php if($qrt["0"]==2) { ?> style="font-weight:bold;background:<?php echo $bgr; ?>" <?php } ?> <?php if($qrt["0"]>=3) { ?> style="font-weight:bold;font-style:italic;background:<?php echo $bgr; ?>" <?php } ?>  <?php if($qrt["0"]==1) { ?> style="background:<?php echo $bgr; ?>" <?php } ?> id="trid-<?php echo $new; ?>" >

					<td style="width:430px;">


					<span style="font-size:12px;font-weight:bold;">
					<?php echo $value3e["name"]; ?>
					</span>

                    <br/> <a href="<?php echo Mage::getBaseUrl(); ?><?php echo $url; ?>" target="_blank" ><img src="<?php echo Mage::getBaseUrl(); ?>media/catalog/product/cache/1/small_image/200x200/9df78eab33525d08d6e5fb8d27136e95<?php echo $image; ?>" border="0" width="50" alt="" /> </a>


                    <?php

					  $sql22b = "SELECT status FROM supervision where item_id='".$value3e["item_id"]."' limit 0,1 ";
		              $rows22b = $connection->fetchOne($sql22b);

					  $sql22bh = "SELECT log FROM supervision where item_id='".$value3e["item_id"]."' limit 0,1 ";
		              $rows22bgh = $connection->fetchOne($sql22bh);

					  if($rows22b!="") {
					  $got_qty2 = "SELECT got_qty FROM supervision where item_id='".$value3e["item_id"]."' limit 0,1 ";
		              $got_qty = $connection->fetchOne($got_qty2);
					  }
					  else
					  {
					     $got_qty="";
					  }

					   $sql22fff = "SELECT note FROM supervision where item_id='".$value3e["item_id"]."' and ddate='".$_GET["ode"]."'";
		                   $sql22fffd = $connection->fetchOne($sql22fff);


					 ?>

					 <textarea style="border:1px solid #000;overflow:auto;" name="frozenpick-<?php echo $value3e["item_id"]; ?>-<?php echo $_GET["picker"]; ?>-<?php echo $_GET["ode"]; ?>" id="frozenpick-<?php echo $value3e["item_id"]; ?>-<?php echo $_GET["picker"]; ?>-<?php echo $_GET["ode"]; ?>" ><?php echo $sql22fffd; ?></textarea>

                     <div style="clear:both;"></div>

                     <select name="sfrozenpick-<?php echo $value3e["item_id"]; ?>" style="width:100px;margin-top:5px; margin-left:53px;border:1px solid #000;" id="sfrozenpick-<?php echo $value3e["item_id"]; ?>" onChange="dohirens3('frozenpick-<?php echo $value3e["item_id"]; ?>-<?php echo $_GET["picker"]; ?>-<?php echo $_GET["ode"]; ?>','<?php echo str_replace("'","",$shipping_address["firstname"]); ?> <?php echo str_replace("'","",$shipping_address["lastname"]); ?>','<?php echo $_GET["ode"]; ?>','<?php echo $_GET["picker"]; ?>','<?php echo $qrt["0"]; ?>','<?php echo preg_replace('#[^\w()/.%\-&]#',"",$value3e["name"]); ?> ','<?php echo $value3e["item_id"]; ?>');"
                      >
                     <option value="" style="padding:5px;" <?php if($got_qty=="") { ?> selected <?php } ?> >Select Qty </option>
                     <?php for($dd=0;$dd<=$qrt["0"];$dd++) { ?>
                     <option style="padding:5px;" value="<?php echo $dd; ?>" <?php if($got_qty==$dd && $got_qty!="" ) { ?> selected <?php } ?> ><?php echo $dd; ?></option>
                     <?php } ?>

                     </select>

					<div id="div-frozenpick-<?php echo $value3e["item_id"]; ?>-<?php echo $_GET["picker"]; ?>-<?php echo $_GET["ode"]; ?>">
					  <?php if($rows2!="") {


					  $sql22b = "SELECT status FROM supervision where item_id='".$value3e["item_id"]."'";
		              $rows22b = $connection->fetchOne($sql22b);

					  $sql22bh = "SELECT log FROM supervision where item_id='".$value3e["item_id"]."'";
		              $rows22bgh = $connection->fetchOne($sql22bh);

                       if($rows22b=='found' || $rows22bgh==3 )
					   {

					       $sql22 = "SELECT note FROM supervision where item_id='".$value3e["item_id"]."' and ddate='".$_GET["ode"]."'";
		                   $rows22 = $connection->fetchOne($sql22);
					?>

					<?php

							  echo '<span style="color:green;font-weight:bold;" >  Status :- Found <br/> </span>';

							  if($rows22!="")
							  {

								  // echo '<span style="color:green;font-weight:bold;" > Note :- '.$rows22.'</span>';
							  }
					   }
					   else
					   {   $sql33="select `log` from `supervision` where item_id='".$value3e["item_id"]."' and ddate='".$_GET["ode"]."'";
		                   $rows274 = $connection->fetchOne($sql33);


						   $sql22 = "SELECT note FROM supervision where item_id='".$value3e["item_id"]."' and ddate='".$_GET["ode"]."'";
		                   $rows22 = $connection->fetchOne($sql22);

						   if($rows274=='3')
						   {
						      echo '<span style="color:green;font-weight:bold;" >  Status :- Found <br/> </span>';
						   }

						   if($rows274=='1')
						   {
						      echo '<span style="color:#999;font-weight:bold;" >  Status :- Substituted <br/> </span>';

							    echo '<span style="color:red;font-weight:bold;" > Note :- '.$rows22.'</span>';
						   }

		                   else
						   {
							   $sql22 = "SELECT note FROM supervision where item_id='".$value3e["item_id"]."' and ddate='".$_GET["ode"]."'";
							   $rows22 = $connection->fetchOne($sql22);

							  echo '<span style="color:red;font-weight:bold;" >Status :- Not Found <br/> </span>';
							  if($rows22!="")
							  {
								  echo '<span style="color:red;font-weight:bold;" > Note :- '.$rows22.'</span>';
							  }
							}
					     }

					    }
					   ?>
					</div>


					<br/>
		   <a href="<?php echo Mage::getBaseUrl(); ?>?item_id=<?php echo $value3e["item_id"]; ?>&rpc=<?php echo $_GET["ode"]; ?>" style="text-decoration:underline;color:red;" target="_blank"> Product Change </a>

					</td>
					<td style="width:25px;font-size:15px; <?php if($qrt["0"]>=2) { ?> background:yellow;color:green;padding:3px;font-weight:bold; <?php } ?> "><?php echo $qrt["0"]; ?></td><td style="width:225px;"><?php echo str_replace("'","",$shipping_address["firstname"]); ?> <?php echo str_replace("'","",$shipping_address["lastname"]); ?>


                     <br/>
					   <?php
                        $torder_id = $value3e["increment_id"];
                       $select_cnote="select cnote from mg_shipping_delivery_window where order_number='".$torder_id."'";
                       $conote=$connection->fetchOne($select_cnote);
                       if($conote!="")
                       {
                          echo  "<p style='padding:5px;color:green;'> Note :- ".$conote."</p>";
                       } ?>

                    </td>
					</tr>
                  <?php
					}
                   ?>
</table>
       </div>
     <?php }
?>