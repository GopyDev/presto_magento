<?php
      if(isset($_GET["gt"]) && $_GET["gt"]=='sppick' ) {
    $sel_order_picker = "select picker_id from mg_order_picker where window LIKE '%".$_GET["ode"]."' and order_id > 0 group by picker_id";
	$rs_order_picker  =  $connection->fetchAll($sel_order_picker); ?>
	<div id="drypickdiv" style="margin-top:15px;margin-bottom:15px;">
	Product Type :-

	     <a href="<?php echo Mage::getBaseUrl(); ?>?gt=sppick&picker2=141" style="font-weight:bold;font-size:16px !important;">BAKERY</a>&nbsp;&nbsp;<a href="<?php echo Mage::getBaseUrl(); ?>?gt=sppick&picker2=146" style="font-weight:bold;font-size:16px !important;">CUT MEAT</a>&nbsp;&nbsp;<a href="<?php echo Mage::getBaseUrl(); ?>?gt=sppick&picker2=144" style="font-weight:bold;font-size:16px !important;">DELI</a>&nbsp;&nbsp;<a href="<?php echo Mage::getBaseUrl(); ?>?gt=sppick&picker2=145" style="font-weight:bold;font-size:16px !important;">SEA FOOD</a>

	 </div>
	 <?php }
	  if(isset($_GET["picker2"]) && $_GET["picker2"]!="" && $_GET["gt"]=='sppick' ) {
	  ?>

	 <script type="text/javascript">
	  function obccheck() {
				jQuery('html, body').animate({
					scrollTop: jQuery('#location-'+jQuery('#bylocation').val()).offset().top
				}, 2000);
	}

	function dohirens3(id,cust,ddate,infopicker,itemqty,name,itemid)
	{
		  output = jQuery('#sdrypick-'+itemid).val();
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

	function backdrppick() {
	   jQuery('html, body').animate({
					scrollTop: jQuery('.page').offset().top
				}, 2000);
	}
    </script>


	<?php
	$sql3e = "SELECT mei.entity_id,msi.item_id,msi.product_id,mei.value,msi.product_id,mog.`increment_id`,msi.qty_ordered as qotd,msi.name,msi.sku,msi.`order_id`,mop.picker_id,rr.value as val,rr2.value as sval,rr3.value as tval,rr4.value as uval,rr2.store_id,
    ( CASE rr2.value
	WHEN 'Produce' THEN 1
	WHEN 'Dairy' THEN 2
	WHEN '1' THEN 3
	WHEN '2' THEN 4
	WHEN '3' THEN 5
	WHEN '4' THEN 6
	WHEN '5' THEN 7
	WHEN '6' THEN 8
	WHEN '7' THEN 9
	WHEN '8' THEN 10
	WHEN '9' THEN 11
	WHEN 'Bakery' THEN 12
	WHEN 'Deli' THEN 13
	WHEN 'F' THEN 14
	WHEN 'R' THEN 15
	ELSE 16
	END ) as abc,mog.`increment_id` FROM `mg_sales_flat_order_item` msi
						left join mg_sales_flat_order_grid mog on msi.`order_id` = mog.`entity_id`
						left join mg_shipping_delivery_window msw on mog.`increment_id` = msw.`order_number`
						left join mg_order_picker mop on mog.`increment_id` = mop.`order_number`
						left join mg_catalog_product_entity_int mei on msi.product_id = mei.entity_id and mei.attribute_id =  251 and mei.store_id = 0
						left join mg_catalog_product_entity_int rr on msi.product_id = rr.entity_id and rr.attribute_id =  252 and rr.store_id = 0
						left join mg_catalog_product_entity_int qrr on msi.product_id = qrr.entity_id and qrr.attribute_id =  253 and rr.store_id = 0
						left join mg_eav_attribute_option_value_hiren rr2 on rr.value = rr2.option_id and rr2.store_id = 0
						left join mg_eav_attribute_option_value_hiren rr3 on mei.value = rr3.option_id and rr3.store_id = 0
						left join mg_eav_attribute_option_value_hiren rr4 on qrr.value = rr4.option_id and rr4.store_id = 0

						where msw.`window` LIKE '%".$_GET["ode"]."'  and  ( mei.value=".$_GET["picker2"]." )";


						$sql3e.=" group by msi.item_id order by mog.`increment_id`,msi.name";

					if($_GET["picker2"]=="141")
					{
					   $tname="BAKERY";
					}

					if($_GET["picker2"]=="146")
					{
					   $tname="CUT MEAT";
					}

					if($_GET["picker2"]=="144")
					{
					   $tname="DELI";
					}

					if($_GET["picker2"]=="145")
					{
					   $tname="SEA FOOD";
					}

	?>

	 <div id="tbldrypick" style="padding-top:10px">
	 <table border=1 cellpadding="5" cellspacing="5" style="border-collapse:collapse;font-size:13px;width:100%;"  >
				  <tr><td colspan=2 align="center" style="font-size:15px;color:green;"><b> S.D Pick List ( <?php echo $tname; ?> ) </b></td></tr>

                   <?php
                    $rows3e = $connection->fetchAll($sql3e);
	                $green = 0;
	                $bpil = 0;
					$kbr="";
					foreach ($rows3e as $value3e) {

					$sql_picker="select name from `mg_pickers` where id='".$value3e["picker_id"]."'";
		             $picker_name=$connection->fetchOne($sql_picker);


					 $sel_customer_fname="select shipping_name from mg_sales_flat_order_grid where entity_id='".$value3e["order_id"]."' limit 0,1";
					 $customer_fname=$connection->fetchOne($sel_customer_fname);
                     $shipping_address["shipping_name"] = $customer_fname;

					if($value3e["sval"]!=$kbr)
					{
					   echo "<tr id='location-".$value3e["sval"]."' ><td  style='text-align:center;font-size:15px;font-weight:bold;color:#008000;'> Location : ".$value3e["sval"]." ( <a href='javascript:void(0);' onClick='backdrppick();' > Go to Top </a> )</td></tr>";
					}
					$kbr = $value3e["sval"];
					$bpil = $bpil + $value3e["qotd"];
					$green = $green + 1;
					$qrt = explode(".",$value3e["qotd"]);

					$sql22b = "SELECT status FROM supervision where item_id='".$value3e["item_id"]."' and ddate='".$_GET["ode"]."' limit 0,1";
		            $rows22b = $connection->fetchOne($sql22b);


					if($rows22b=="")
					{
					   if($qrt["0"]>=2)
					   {
					   		$bgr='#fff';
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

					   $sql33="select `log` from `supervision` where item_id='".$value3e["item_id"]."' and ddate='".$_GET["ode"]."' limit 0,1";
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

					 $new = 'drypick-'.$value3e["item_id"].'-'.$_GET["picker"].'-'.$_GET["ode"];

                    ?>
                    <tr <?php if($qrt["0"]==2) { ?> style="font-weight:bold;background:<?php echo $bgr; ?>" <?php } ?> <?php if($qrt["0"]>=3) { ?> style="font-weight:bold;background:<?php echo $bgr; ?>" <?php } ?>  <?php if($qrt["0"]==1) { ?> style="background:<?php echo $bgr; ?>" <?php } ?> id="trid-<?php echo $new; ?>"  >
					<?php
				    $sql2 = "SELECT idstring FROM supervision where idstring='".$new."' limit 0,1";
		            $rows2 = $connection->fetchOne($sql2);
			        ?>
					<td style="width:680px; <?php if($qrt["0"]==2) { ?> font-weight:bold;" <?php } ?> <?php if($qrt["0"]>=3) { ?> font-weight:bold;font-style:italic;" <?php } ?> ">
					<?php
				    $rows2745 = $value3e["product_id"];
                    $sel_image="select small_image from mg_catalog_product_flat_1 where entity_id='".$rows2745."' limit 0,1";
					$image=$connection->fetchOne($sel_image);
					$sel_url="select url_path from mg_catalog_product_flat_1 where entity_id='".$rows2745."' limit 0,1";
					$url=$connection->fetchOne($sel_url);
					$sel_hitem="select value from mg_catalog_product_entity_varchar where entity_id='".$rows2745."' and `attribute_id` =255 and value is not NULL limit 0,1";
					$htem=$connection->fetchOne($sel_hitem);
					 ?>

					<div style="font-size:14px;font-weight:bold;">
					<?php echo $value3e["name"]; ?>
					</div>

                    <div style="font-size:14px;font-weight:bold;">
                    <a href="<?php echo Mage::getBaseUrl(); ?>?item_id=<?php echo $value3e["item_id"]; ?>&rpc=<?php echo $_GET["ode"]; ?>" style="text-decoration:underline;color:red;" target="_blank"> Request Change </a>
                    </div>

                    <?php if($htem!="") { ?>
                     <div style="margin-top:10px; margin-bottom:10px; background-color:#FF0000; color:#FFFFFF; padding-left:7px; font-size:14px; padding-right:5px; font-weight:300;">
                     <?php echo $htem; ?>
                     </div>
                     <?php } ?>


					<br/> <a href="<?php echo Mage::getBaseUrl(); ?><?php echo $url; ?>" target="_blank" ><img src="<?php echo Mage::getBaseUrl(); ?>media/catalog/product/cache/1/small_image/200x200/9df78eab33525d08d6e5fb8d27136e95<?php echo $image; ?>" border="0" width="50" alt="" /> </a>

					<?php

					  $sql22b = "SELECT status FROM supervision where item_id='".$value3e["item_id"]."' limit 0,1 ";
		              $rows22b = $connection->fetchOne($sql22b);

					  $sql22bh = "SELECT log FROM supervision where item_id='".$value3e["item_id"]."' limit 0,1 ";
		              $rows22bgh = $connection->fetchOne($sql22bh);

					  if($rows2!="") {
					  $got_qty2 = "SELECT got_qty FROM supervision where item_id='".$value3e["item_id"]."' limit 0,1 ";
		              $got_qty = $connection->fetchOne($got_qty2);
					  }
					  else
					  {
					     $got_qty="";
					  }

					   $sql22fff = "SELECT note FROM supervision where item_id='".$value3e["item_id"]."' and ddate='".$_GET["ode"]."'";
		                   $sql22fffd = $connection->fetchOne($sql22fff);

					if($sql22fffd=="undefined")
					{
					   $sql22fffd="";
					}

					 ?>

					 <textarea style="border:1px solid #000;overflow:auto;" name="drypick-<?php echo $value3e["item_id"]; ?>-<?php echo $_GET["picker"]; ?>-<?php echo $_GET["ode"]; ?>" id="drypick-<?php echo $value3e["item_id"]; ?>-<?php echo $_GET["picker"]; ?>-<?php echo $_GET["ode"]; ?>" ><?php echo $sql22fffd; ?></textarea>

                     <div style="clear:both;margin-left:53px; font-size:14px; width:130px; margin-top:8px;    <?php if($qrt["0"]>=2) { ?> background:yellow;color:green; padding:5px;<?php } ?>"> Qty. Ordered : <?php echo $qrt["0"]; ?> </div>

                     <select name="sdrypick-<?php echo $value3e["item_id"]; ?>" style="width:100px;margin-top:5px; margin-left:53px;border:1px solid #000;" id="sdrypick-<?php echo $value3e["item_id"]; ?>" onChange="dohirens3('drypick-<?php echo $value3e["item_id"]; ?>-<?php echo $_GET["picker"]; ?>-<?php echo $_GET["ode"]; ?>','<?php echo str_replace("'","",$shipping_address["firstname"]); ?> <?php echo str_replace("'","",$shipping_address["lastname"]); ?>','<?php echo $_GET["ode"]; ?>','<?php echo $_GET["picker"]; ?>','<?php echo $qrt["0"]; ?>','<?php echo preg_replace('#[^\w()/.%\-&]#',"",$value3e["name"]); ?> ','<?php echo $value3e["item_id"]; ?>');"
                      >
                     <option value="" style="padding:5px;" <?php if($got_qty=="") { ?> selected <?php } ?> >Select Qty </option>
                     <?php for($dd=0;$dd<=$qrt["0"];$dd++) { ?>
                     <option style="padding:5px;" value="<?php echo $dd; ?>" <?php if($got_qty==$dd && $got_qty!="" ) { ?> selected <?php } ?> ><?php echo $dd; ?></option>
                     <?php } ?>
                     </select>
			      <div id="div-drypick-<?php echo $value3e["item_id"]; ?>-<?php echo $_GET["picker"]; ?>-<?php echo $_GET["ode"]; ?>">
					  <?php if($rows2!="") {
					  $sql22b = "SELECT status FROM supervision where idstring='".$new."'";
		              $rows22b = $connection->fetchOne($sql22b);
                       if($rows22b=='found')
					   {

					   }
					   else
					   {

					       $sql33="select `log` from `supervision` where item_id='".$value3e["item_id"]."' and ddate='".$_GET["ode"]."'";
		                   $rows274 = $connection->fetchOne($sql33);

						   if($rows274=='1')
						   {
						      echo '<span style="color:#999;font-weight:bold;" >  Status :- Substituted <br/> </span>';
						   }

		                   else
						   {
							   $sql22 = "SELECT note FROM supervision where item_id='".$value3e["item_id"]."' and ddate='".$_GET["ode"]."'";
							   $rows22 = $connection->fetchOne($sql22);

								 if($rows274=='2' || $rows274=='')
								 {
								  if($rows22!="")
								  {
									  echo '<span style="color:red;font-weight:bold;" > Note :- '.$rows22.'</span>';
								  }
							   }
							}
					   }

					 }
					   ?>
					</div>

           <div style="margin-top:10px;margin-bottom:10px;">
           Customer : <?php echo str_replace("'","",$shipping_address["shipping_name"]); ?>
           </div>

           <div style="margin-top:10px;margin-bottom:10px;">
           Picker : <?php echo $picker_name; ?>
           </div>

           <div style="margin-top:10px;margin-bottom:10px;">
           # Order Id : <?php echo $value3e["increment_id"]; ?>
           </div>




           <?php
			   $torder_id = $value3e["increment_id"];
			   $select_cnote="select cnote from mg_shipping_delivery_window where order_number='".$torder_id."' limit 0,1";
			   $conote=$connection->fetchOne($select_cnote);
			   if($conote!="")
			   {
				  echo  "<p style='padding:5px;color:green;'> Note :- ".$conote."</p>";
			   }

			   $sel_order_comment="select customer_note from mg_sales_flat_order where entity_id='".$value3e["order_id"]."' and customer_note is not NULL";
			   $comment=$connection->fetchOne($sel_order_comment);

			   if($comment!="")
			   {
			   echo  "<p style='padding:5px;color:green;'> Note :- ".$comment."</p>";
			   }

		   ?>

					 </td>

					</tr>




               <?php
					}
                ?>
</table>
</div>
    <?php }
?>