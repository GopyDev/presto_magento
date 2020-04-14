<?php 
if(isset($_GET["gt"]) && $_GET["gt"]=='ltime' ) { 
	   // $_GET["ode"] = '15_10_2015';
	   $abpickers="";
	   
	$sel_assign_picker="select picker_id from mg_order_picker where window LIKE '%".$_GET["ode"]."' and order_id > 0 group by picker_id";
	$rs_assign_picker=$connection->fetchAll($sel_assign_picker);
	$total_workings=count($rs_assign_picker);
	
	
    ?>
	<div id="loglist" style="margin-top:15px;margin-bottom:15px;">
	
	<div><b>Today's Working Hours  <br/> <span style="color:red;"> Note :- Enter time to nearest quarter hour (ex: 3h15m should be entered as 3.25) </span> </b> </div> <br/>
	 <?php
	 $i = 1;
	 foreach ($rs_assign_picker as $assign_picker) {
	 
	 $sel_order_picker = "select * from mg_pickers where id='".$assign_picker["picker_id"]."' and role!='driver' and extra='' ";
	 $rs_order_picker  =  $connection->fetchAll($sel_order_picker); 
	 foreach ($rs_order_picker as $order_picker) {
	     $sql_picker="select name from `mg_pickers` where id='".$order_picker["id"]."'";
		 $picker_name=$connection->fetchOne($sql_picker);
		 $check="select pickerst from pickerlog where dodate='".$_GET["ode"]."' and pickerid='".$order_picker["id"]."'";
	     $check_confirm=$connection->fetchOne($check); 
	 ?>
       <div> 
        <form method="post" name="frm-<?php echo $order_picker["id"]; ?>" <?php if($check_confirm!='') { ?> style="background:#efefef"<?php } ?> >
        <span style="width:140px; display:inline-block;color:green;<?php if($check_confirm!='') { ?> background:#d5d5d5;<?php } ?>"> <b>  <?php echo $picker_name; ?> :  </b> </span>
        <input type="text" name="pickerst" id="pickerst" value="<?php echo $check_confirm; ?>"  style="display:inline-block; width:50px;"  />
		<input type="hidden" name="pickerid" id="pickerid" value="<?php echo $order_picker["id"]; ?>"   />
        <input type="hidden" name="dodate" id="dodate" value="<?php echo $_GET["ode"]; ?>"  />
        <input type="submit" style="background-color:green;padding:5px;color:#fff;" name="submit" value="Submit"  />
        </form>
        </div>
	 <?php	
		 $abpickers.=$order_picker["id"];
		 if($total_workings-1>=$i)
		 {
			$abpickers.=",";
		 }
		 
	 $i++;			 
	 } 	
	 }	?>
	 
	  <div style="margin-top:15px;">
	 Additional EMP / Supervisor 
	 </div>
	 
	 <?php
	 $sel_ntassign_picker="select * from mg_pickers where id not in(select picker_id from mg_order_picker where window LIKE '%".$_GET["ode"]."' and order_id > 0 group by picker_id) and role!='driver' and extra='' ";
	 $rs_ntassign_picker=$connection->fetchAll($sel_ntassign_picker);
	 
	 foreach ($rs_ntassign_picker as $order_picker) {
	     $sql_picker="select name from `mg_pickers` where id='".$order_picker["id"]."'";
		 $picker_name=$connection->fetchOne($sql_picker);
		 $check="select pickerst from pickerlog where dodate='".$_GET["ode"]."' and pickerid='".$order_picker["id"]."'";
	     $check_confirm=$connection->fetchOne($check); 
	 ?>
       <div> 
        <form method="post" name="frm-<?php echo $order_picker["id"]; ?>" <?php if($check_confirm!='') { ?> style="background:#efefef"<?php } ?> >
        <span style="width:140px; display:inline-block;color:green;<?php if($check_confirm!='') { ?> background:#d5d5d5;<?php } ?>"> <b>  <?php echo $picker_name; ?> :  </b> </span>
        <input type="text" name="pickerst" id="pickerst" value="<?php echo $check_confirm; ?>"  style="display:inline-block; width:50px;"  />
		<input type="hidden" name="pickerid" id="pickerid" value="<?php echo $order_picker["id"]; ?>"   />
        <input type="hidden" name="dodate" id="dodate" value="<?php echo $_GET["ode"]; ?>"  />
        <input type="submit" style="background-color:green;padding:5px;color:#fff;" name="submit" value="Submit"  />
        </form>
        </div>
	 <?php	
		 $abpickers.=$order_picker["id"];
		 if($total_workings-1>=$i)
		 {
			$abpickers.=",";
		 }
		 
	 $i++;			 
	 } 	
	 
	 $total_workings_not=count($rs_ntassign_picker);
	
	
	 ?>
	 </div>
	 
	
	 
	 
	 
	 
	   <?php
	       $check="select pickerst2 from expenses where dodate2='".$_GET["ode"]."' ";
	       $check_confirm2=$connection->fetchOne($check); 
		   
		   $check="select pickerst3 from expenses where dodate2='".$_GET["ode"]."' ";
	       $check_confirm3=$connection->fetchOne($check); 
		   
		   $check="select pickerst4 from expenses where dodate2='".$_GET["ode"]."' ";
	       $check_confirm4=$connection->fetchOne($check);
	  ?>	 
	 	
		<div><b> Additional Expenses ( <?php echo date("m-d-Y"); ?> ) </b>  </div> 
		<div> 
		
        <form method="post" name="frmgreen" id="frmgreen">
       
		<div>
        <div style="width:145px;display:inline-block;margin-right:10px;margin-top:15px;color:green;" > Product Expense : </div> <input type="text" name="pickerst2" id="pickerst2" value="<?php echo $check_confirm2; ?>"  style="display:inline-block; width:70px;"  />
		 </div>
		 <div> 
        <div style="width:145px;display:inline-block;margin-right:10px;margin-top:5px;color:green;" >Supply Expense : </div>  <input type="text" name="pickerst3" id="pickerst3" value="<?php echo $check_confirm3; ?>"  style="display:inline-block; width:70px;"  />
		  </div>
		  
		
		 <div> 
        <input type="hidden" name="dodate2" id="dodate2" value="<?php echo $_GET["ode"]; ?>"  />
		<input type="hidden" name="semail" id="semail" value="<?php echo $customerData["email"]; ?>"  />
		<br/>
        <input type="submit" style="background-color:green;padding:5px;color:#fff;" name="submit" value="Submit"  />
        </form>
        </div>
        
        <br/>
	 <?php } 
?>