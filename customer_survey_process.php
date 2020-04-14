<?php
	/************************************
			Customer Survey Process
				  28/04/2016
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
<form name="survey_process" id="survey_process_form" method="post" action="acceptOrder.php" style="display:none;" />
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
