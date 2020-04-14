<?php
     if(!isset($_GET["gt"])) {
	   $j=0;
	   for($i=0;$i<=0;$i++){
       $kbt = date("d_m_Y");
	// $kbt = "06_10_2016";
	 $seconds = time();
     $rounded_seconds = round($seconds / (15 * 60)) * (15 * 60);
     echo "Current Time : " . date('H:i', $rounded_seconds) . "\n";
	 $kpo=str_replace(":","",date('H:i', $rounded_seconds));
	 ?>
	 <?php $kbt2 = date("m-d-Y");
	 $sql = "SELECT m.* FROM `mg_shipping_delivery_window` m  where m.`window` LIKE '%".$kbt."' and driver_email='".$customerData["email"]."' order by m.`rc`";
	 $rows = $connection->fetchAll($sql);
	 $total_rows = count($rows);
		 if($total_rows>=1)
		 {
		      $sel_route="select route from mg_shipping_delivery_window where `window` LIKE '%".$kbt."' and driver_email='".$customerData["email"]."'  limit 0,1";
			  $route_name=$connection->fetchOne($sel_route);

			  $sel_route="select driverid from mg_shipping_delivery_window where `window` LIKE '%".$kbt."' and driver_email='".$customerData["email"]."'  limit 0,1";
			  $route_id=$connection->fetchOne($sel_route);

			 if($route_name!="")
			 {
			    $sel_std="select std from route_driver where route='".$route_name."' and rid='".$route_id."'";
				$std=$connection->fetchOne($sel_std);

				$sel_rnote="select note from route_driver where route='".$route_name."' and rid='".$route_id."'";
				$rnote=$connection->fetchOne($sel_rnote);

				$sel_std2="select std2 from route_driver where route='".$route_name."' and rid='".$route_id."'";
				$std2=$connection->fetchOne($sel_std2);

				$sel_v="select driver from route_driver where route='".$route_name."' and rid='".$route_id."'";
				$vehicle=$connection->fetchOne($sel_v);

				$sel_startime="select starttime from route_driver where route='".$route_name."'and rid='".$route_id."'";
				$starttime=$connection->fetchOne($sel_startime);

				$sel_endtime="select endtime from route_driver where route='".$route_name."' and rid='".$route_id."'";
				$endtime=$connection->fetchOne($sel_endtime);

				$sel_exptype="select exptype from route_driver where route='".$route_name."' and rid='".$route_id."'";
				$exptype=$connection->fetchOne($sel_exptype);

				$sel_expr="select expr from route_driver where route='".$route_name."' and rid='".$route_id."'";
				$expr=$connection->fetchOne($sel_expr);
			 }
		 ?>

	 <script type="text/javascript">
	 function dostarttime(gti)
	 {
	     jQuery("#starttime").val(gti);
		 jQuery("#frmdrive").submit();

	 }
	  function doendtime(gti)
	 {
	     jQuery("#endtime").val(gti);
		 jQuery("#frmdrive").submit();

	 }
	 </script>
		    <form method="post" name="frmdrive" id="frmdrive" action="">
		    <table border=1 style="border-collapse:collapse;font-size:11px;margin-top:10px;margin-bottom:10px;border:1px solid #000;">
			<tr>
			<td><b>Scheduled Departure Time</b></td>
			<td><b><?php echo $std; ?></b></td>
			<td style="text-align:left;"><b>Scheduled Finish Time</b> </td>
			<td><b><?php echo $std2; ?></b></td>
			</tr>
			 <?php if(trim($vehicle)=="Box Truck")
				   {
				       $vkt = "Van 2 - New";
				   }

				   if(trim($vehicle)=="Van 1")
				   {
				      $vkt = "Van 1 - Old";
				   }
			?>

			<tr>
			<td colspan="4"> <b> Vehicle Name :- <?php echo $vkt; ?> </b> </td>
			</tr>
		    <tr <?php if($starttime!="" && $endtime!="") { ?> style="background:#d5d5d5;" <?php } ?> >
				<td colspan="4">
				<br/>
				<div style="width:100%;text-align:left;"> Shift Timings & Expenses </div>
				<br/>
				<div <?php if($starttime!="") { ?>  style="background:#dfffdf;" <?php } ?> >
				<b> Actual Start Time  </b>   &nbsp;&nbsp;&nbsp;
				 <select name="starttime" id="starttime" style="border:1px solid #000; margin-top:5px;" >
                 <option value="" > Select </option>
<option value="0900" <?php if($starttime=='0900') { ?> selected <?php } ?> >09:00</option>
<option value="0915" <?php if($starttime=='0915') { ?> selected <?php } ?> >09:15</option>
<option value="0930" <?php if($starttime=='0930') { ?> selected <?php } ?> >09:30</option>
<option value="0945" <?php if($starttime=='0945') { ?> selected <?php } ?> >09:45</option>
<option value="1000" <?php if($starttime=='1000') { ?> selected <?php } ?> >10:00</option>
<option value="1015" <?php if($starttime=='1015') { ?> selected <?php } ?> >10:15</option>
<option value="1030" <?php if($starttime=='1030') { ?> selected <?php } ?> >10:30</option>
<option value="1045" <?php if($starttime=='1045') { ?> selected <?php } ?> >10:45</option>
<option value="1100" <?php if($starttime=='1100') { ?> selected <?php } ?> >11:00</option>
<option value="1115" <?php if($starttime=='1115') { ?> selected <?php } ?> >11:15</option>
<option value="1130" <?php if($starttime=='1130') { ?> selected <?php } ?> >11:30</option>
<option value="1145" <?php if($starttime=='1145') { ?> selected <?php } ?> >11:45</option>
<option value="1200" <?php if($starttime=='1200') { ?> selected <?php } ?> >12:00</option>
<option value="1215" <?php if($starttime=='1215') { ?> selected <?php } ?> >12:15</option>
<option value="1230" <?php if($starttime=='1230') { ?> selected <?php } ?> >12:30</option>
<option value="1245" <?php if($starttime=='1245') { ?> selected <?php } ?> >12:45</option>
<option value="1300" <?php if($starttime=='1300') { ?> selected <?php } ?> >13:00</option>
<option value="1315" <?php if($starttime=='1315') { ?> selected <?php } ?> >13:15</option>
<option value="1330" <?php if($starttime=='1330') { ?> selected <?php } ?> >13:30</option>
<option value="1345" <?php if($starttime=='1345') { ?> selected <?php } ?> >13:45</option>
<option value="1400" <?php if($starttime=='1400') { ?> selected <?php } ?> >14:00</option>
<option value="1415" <?php if($starttime=='1415') { ?> selected <?php } ?> >14:15</option>
<option value="1430" <?php if($starttime=='1430') { ?> selected <?php } ?> >14:30</option>
<option value="1445" <?php if($starttime=='1445') { ?> selected <?php } ?> >14:45</option>
<option value="1500" <?php if($starttime=='1500') { ?> selected <?php } ?> >15:00</option>
<option value="1515" <?php if($starttime=='1515') { ?> selected <?php } ?> >15:15</option>
<option value="1530" <?php if($starttime=='1530') { ?> selected <?php } ?> >15:30</option>
<option value="1545" <?php if($starttime=='1545') { ?> selected <?php } ?> >15:45</option>
<option value="1600" <?php if($starttime=='1600') { ?> selected <?php } ?> >16:00</option>
<option value="1615" <?php if($starttime=='1615') { ?> selected <?php } ?> >16:15</option>
<option value="1630" <?php if($starttime=='1630') { ?> selected <?php } ?> >16:30</option>
<option value="1645" <?php if($starttime=='1645') { ?> selected <?php } ?> >16:45</option>
<option value="1700" <?php if($starttime=='1700') { ?> selected <?php } ?> >17:00</option>
<option value="1715" <?php if($starttime=='1715') { ?> selected <?php } ?> >17:15</option>
<option value="1730" <?php if($starttime=='1730') { ?> selected <?php } ?> >17:30</option>
<option value="1745" <?php if($starttime=='1745') { ?> selected <?php } ?> >17:45</option>
<option value="1800" <?php if($starttime=='1800') { ?> selected <?php } ?> >18:00</option>
<option value="1815" <?php if($starttime=='1815') { ?> selected <?php } ?> >18:15</option>
<option value="1830" <?php if($starttime=='1830') { ?> selected <?php } ?> >18:30</option>
<option value="1845" <?php if($starttime=='1845') { ?> selected <?php } ?> >18:45</option>
<option value="1900" <?php if($starttime=='1900') { ?> selected <?php } ?> >19:00</option>
<option value="1915" <?php if($starttime=='1915') { ?> selected <?php } ?> >19:15</option>
<option value="1930" <?php if($starttime=='1930') { ?> selected <?php } ?> >19:30</option>
<option value="1945" <?php if($starttime=='1945') { ?> selected <?php } ?> >19:45</option>
<option value="2000" <?php if($starttime=='2000') { ?> selected <?php } ?> >20:00</option>
<option value="2015" <?php if($starttime=='2015') { ?> selected <?php } ?> >20:15</option>
<option value="2030" <?php if($starttime=='2030') { ?> selected <?php } ?> >20:30</option>
<option value="2045" <?php if($starttime=='2045') { ?> selected <?php } ?> >20:45</option>
<option value="2100" <?php if($starttime=='2100') { ?> selected <?php } ?> >21:00</option>
<option value="2115" <?php if($starttime=='2115') { ?> selected <?php } ?> >21:15</option>
<option value="2130" <?php if($starttime=='2130') { ?> selected <?php } ?> >21:30</option>
<option value="2145" <?php if($starttime=='2145') { ?> selected <?php } ?> >21:45</option>
<option value="2200" <?php if($starttime=='2200') { ?> selected <?php } ?> >22:00</option>
<option value="2215" <?php if($starttime=='2215') { ?> selected <?php } ?> >22:15</option>
<option value="2230" <?php if($starttime=='2230') { ?> selected <?php } ?> >22:30</option>
<option value="2245" <?php if($starttime=='2245') { ?> selected <?php } ?> >22:45</option>
<option value="2300" <?php if($starttime=='2300') { ?> selected <?php } ?> >23:00</option>
<option value="2315" <?php if($starttime=='2315') { ?> selected <?php } ?> >23:15</option>
<option value="2330" <?php if($starttime=='2330') { ?> selected <?php } ?> >23:30</option>
<option value="2345" <?php if($starttime=='2345') { ?> selected <?php } ?> >23:45</option>
<option value="0100" <?php if($starttime=='0100') { ?> selected <?php } ?> >01:00</option>
<option value="0115" <?php if($starttime=='0115') { ?> selected <?php } ?> >01:15</option>
<option value="0130" <?php if($starttime=='0130') { ?> selected <?php } ?> >01:30</option>
<option value="0145" <?php if($starttime=='0145') { ?> selected <?php } ?> >01:45</option>
<option value="0200" <?php if($starttime=='0200') { ?> selected <?php } ?> >02:00</option>
<option value="0215" <?php if($starttime=='0215') { ?> selected <?php } ?> >02:15</option>
<option value="0230" <?php if($starttime=='0230') { ?> selected <?php } ?> >02:30</option>
<option value="0245" <?php if($starttime=='0245') { ?> selected <?php } ?> >02:45</option>
<option value="0300" <?php if($starttime=='0300') { ?> selected <?php } ?> >03:00</option>
<option value="0315" <?php if($starttime=='0315') { ?> selected <?php } ?> >03:15</option>
<option value="0330" <?php if($starttime=='0330') { ?> selected <?php } ?> >03:30</option>
<option value="0345" <?php if($starttime=='0345') { ?> selected <?php } ?> >03:45</option>
<option value="0400" <?php if($starttime=='0400') { ?> selected <?php } ?> >04:00</option>
<option value="0415" <?php if($starttime=='0415') { ?> selected <?php } ?> >04:15</option>
<option value="0430" <?php if($starttime=='0430') { ?> selected <?php } ?> >04:30</option>
<option value="0445" <?php if($starttime=='0445') { ?> selected <?php } ?> >04:45</option>
<option value="0500" <?php if($starttime=='0500') { ?> selected <?php } ?> >05:00</option>
<option value="0515" <?php if($starttime=='0515') { ?> selected <?php } ?> >05:15</option>
<option value="0530" <?php if($starttime=='0530') { ?> selected <?php } ?> >05:30</option>
<option value="0545" <?php if($starttime=='0545') { ?> selected <?php } ?> >05:45</option>
<option value="0600" <?php if($starttime=='0600') { ?> selected <?php } ?> >06:00</option>
<option value="0615" <?php if($starttime=='0615') { ?> selected <?php } ?> >06:15</option>
<option value="0630" <?php if($starttime=='0630') { ?> selected <?php } ?> >06:30</option>
<option value="0645" <?php if($starttime=='0645') { ?> selected <?php } ?> >06:45</option>
<option value="0700" <?php if($starttime=='0700') { ?> selected <?php } ?> >07:00</option>
<option value="0715" <?php if($starttime=='0715') { ?> selected <?php } ?> >07:15</option>
<option value="0730" <?php if($starttime=='0730') { ?> selected <?php } ?> >07:30</option>
<option value="0745" <?php if($starttime=='0745') { ?> selected <?php } ?> >07:45</option>
<option value="0800" <?php if($starttime=='0800') { ?> selected <?php } ?> >08:00</option>
<option value="0815" <?php if($starttime=='0815') { ?> selected <?php } ?> >08:15</option>
<option value="0830" <?php if($starttime=='0830') { ?> selected <?php } ?> >08:30</option>
<option value="0845" <?php if($starttime=='0845') { ?> selected <?php } ?> >08:45</option>
</select>  &nbsp; &nbsp; <?php if($starttime=="") { ?>
	 <input type="button" style="background-color:green;padding:5px;color:#fff;" name="std" value="Clock In" onClick="dostarttime('<?php echo $kpo ?>');">
	    <?php } ?>
		 </div>
		 <div <?php if($endtime!="") { ?>  style="background:#dfffdf;" <?php } ?> >
			      <b> Actual Finish Time  </b>  &nbsp;&nbsp;
			       <select name="endtime" id="endtime" style="border:1px solid #000; margin-top:5px;" >
			<option value="" > Select </option>
			<option value="0900" <?php if($endtime=='0900') { ?> selected <?php } ?> >09:00</option>
<option value="0915" <?php if($endtime=='0915') { ?> selected <?php } ?> >09:15</option>
<option value="0930" <?php if($endtime=='0930') { ?> selected <?php } ?> >09:30</option>
<option value="0945" <?php if($endtime=='0945') { ?> selected <?php } ?> >09:45</option>

<option value="1000" <?php if($endtime=='1000') { ?> selected <?php } ?> >10:00</option>
<option value="1015" <?php if($endtime=='1015') { ?> selected <?php } ?> >10:15</option>
<option value="1030" <?php if($endtime=='1030') { ?> selected <?php } ?> >10:30</option>
<option value="1045" <?php if($endtime=='1045') { ?> selected <?php } ?> >10:45</option>


<option value="1100" <?php if($endtime=='1100') { ?> selected <?php } ?> >11:00</option>
<option value="1115" <?php if($endtime=='1115') { ?> selected <?php } ?> >11:15</option>
<option value="1130" <?php if($endtime=='1130') { ?> selected <?php } ?> >11:30</option>
<option value="1145" <?php if($endtime=='1145') { ?> selected <?php } ?> >11:45</option>


<option value="1200" <?php if($endtime=='1200') { ?> selected <?php } ?> >12:00</option>
<option value="1215" <?php if($endtime=='1215') { ?> selected <?php } ?> >12:15</option>
<option value="1230" <?php if($endtime=='1230') { ?> selected <?php } ?> >12:30</option>
<option value="1245" <?php if($endtime=='1245') { ?> selected <?php } ?> >12:45</option>


<option value="1300" <?php if($endtime=='1300') { ?> selected <?php } ?> >13:00</option>
<option value="1315" <?php if($endtime=='1315') { ?> selected <?php } ?> >13:15</option>
<option value="1330" <?php if($endtime=='1330') { ?> selected <?php } ?> >13:30</option>
<option value="1345" <?php if($endtime=='1345') { ?> selected <?php } ?> >13:45</option>

<option value="1400" <?php if($endtime=='1400') { ?> selected <?php } ?> >14:00</option>
<option value="1415" <?php if($endtime=='1415') { ?> selected <?php } ?> >14:15</option>
<option value="1430" <?php if($endtime=='1430') { ?> selected <?php } ?> >14:30</option>
<option value="1445" <?php if($endtime=='1445') { ?> selected <?php } ?> >14:45</option>

<option value="1500" <?php if($endtime=='1500') { ?> selected <?php } ?> >15:00</option>
<option value="1515" <?php if($endtime=='1515') { ?> selected <?php } ?> >15:15</option>
<option value="1530" <?php if($endtime=='1530') { ?> selected <?php } ?> >15:30</option>
<option value="1545" <?php if($endtime=='1545') { ?> selected <?php } ?> >15:45</option>


<option value="1600" <?php if($endtime=='1600') { ?> selected <?php } ?> >16:00</option>
<option value="1615" <?php if($endtime=='1615') { ?> selected <?php } ?> >16:15</option>
<option value="1630" <?php if($endtime=='1630') { ?> selected <?php } ?> >16:30</option>
<option value="1645" <?php if($endtime=='1645') { ?> selected <?php } ?> >16:45</option>


<option value="1700" <?php if($endtime=='1700') { ?> selected <?php } ?> >17:00</option>
<option value="1715" <?php if($endtime=='1715') { ?> selected <?php } ?> >17:15</option>
<option value="1730" <?php if($endtime=='1730') { ?> selected <?php } ?> >17:30</option>
<option value="1745" <?php if($endtime=='1745') { ?> selected <?php } ?> >17:45</option>


<option value="1800" <?php if($endtime=='1800') { ?> selected <?php } ?> >18:00</option>
<option value="1815" <?php if($endtime=='1815') { ?> selected <?php } ?> >18:15</option>
<option value="1830" <?php if($endtime=='1830') { ?> selected <?php } ?> >18:30</option>
<option value="1845" <?php if($endtime=='1845') { ?> selected <?php } ?> >18:45</option>


<option value="1900" <?php if($endtime=='1900') { ?> selected <?php } ?> >19:00</option>
<option value="1915" <?php if($endtime=='1915') { ?> selected <?php } ?> >19:15</option>
<option value="1930" <?php if($endtime=='1930') { ?> selected <?php } ?> >19:30</option>
<option value="1945" <?php if($endtime=='1945') { ?> selected <?php } ?> >19:45</option>


<option value="2000" <?php if($endtime=='2000') { ?> selected <?php } ?> >20:00</option>
<option value="2015" <?php if($endtime=='2015') { ?> selected <?php } ?> >20:15</option>
<option value="2030" <?php if($endtime=='2030') { ?> selected <?php } ?> >20:30</option>
<option value="2045" <?php if($endtime=='2045') { ?> selected <?php } ?> >20:45</option>


<option value="2100" <?php if($endtime=='2100') { ?> selected <?php } ?> >21:00</option>
<option value="2115" <?php if($endtime=='2115') { ?> selected <?php } ?> >21:15</option>
<option value="2130" <?php if($endtime=='2130') { ?> selected <?php } ?> >21:30</option>
<option value="2145" <?php if($endtime=='2145') { ?> selected <?php } ?> >21:45</option>


<option value="2200" <?php if($endtime=='2200') { ?> selected <?php } ?> >22:00</option>
<option value="2215" <?php if($endtime=='2215') { ?> selected <?php } ?> >22:15</option>
<option value="2230" <?php if($endtime=='2230') { ?> selected <?php } ?> >22:30</option>
<option value="2245" <?php if($endtime=='2245') { ?> selected <?php } ?> >22:45</option>


<option value="2300" <?php if($endtime=='2300') { ?> selected <?php } ?> >23:00</option>
<option value="2315" <?php if($endtime=='2315') { ?> selected <?php } ?> >23:15</option>
<option value="2330" <?php if($endtime=='2330') { ?> selected <?php } ?> >23:30</option>
<option value="2345" <?php if($endtime=='2345') { ?> selected <?php } ?> >23:45</option>


<option value="0100" <?php if($endtime=='0100') { ?> selected <?php } ?> >01:00</option>
<option value="0115" <?php if($endtime=='0115') { ?> selected <?php } ?> >01:15</option>
<option value="0130" <?php if($endtime=='0130') { ?> selected <?php } ?> >01:30</option>
<option value="0145" <?php if($endtime=='0145') { ?> selected <?php } ?> >01:45</option>


<option value="0200" <?php if($endtime=='0200') { ?> selected <?php } ?> >02:00</option>
<option value="0215" <?php if($endtime=='0215') { ?> selected <?php } ?> >02:15</option>
<option value="0230" <?php if($endtime=='0230') { ?> selected <?php } ?> >02:30</option>
<option value="0245" <?php if($endtime=='0245') { ?> selected <?php } ?> >02:45</option>

<option value="0300" <?php if($endtime=='0300') { ?> selected <?php } ?> >03:00</option>
<option value="0315" <?php if($endtime=='0315') { ?> selected <?php } ?> >03:15</option>
<option value="0330" <?php if($endtime=='0330') { ?> selected <?php } ?> >03:30</option>
<option value="0345" <?php if($endtime=='0345') { ?> selected <?php } ?> >03:45</option>
<option value="0400" <?php if($endtime=='0400') { ?> selected <?php } ?> >04:00</option>
<option value="0415" <?php if($endtime=='0415') { ?> selected <?php } ?> >04:15</option>
<option value="0430" <?php if($endtime=='0430') { ?> selected <?php } ?> >04:30</option>
<option value="0445" <?php if($endtime=='0445') { ?> selected <?php } ?> >04:45</option>
<option value="0500" <?php if($endtime=='0500') { ?> selected <?php } ?> >05:00</option>
<option value="0515" <?php if($endtime=='0515') { ?> selected <?php } ?> >05:15</option>
<option value="0530" <?php if($endtime=='0530') { ?> selected <?php } ?> >05:30</option>
<option value="0545" <?php if($endtime=='0545') { ?> selected <?php } ?> >05:45</option>
<option value="0600" <?php if($endtime=='0600') { ?> selected <?php } ?> >06:00</option>
<option value="0615" <?php if($endtime=='0615') { ?> selected <?php } ?> >06:15</option>
<option value="0630" <?php if($endtime=='0630') { ?> selected <?php } ?> >06:30</option>
<option value="0645" <?php if($endtime=='0645') { ?> selected <?php } ?> >06:45</option>
<option value="0700" <?php if($endtime=='0700') { ?> selected <?php } ?> >07:00</option>
<option value="0715" <?php if($endtime=='0715') { ?> selected <?php } ?> >07:15</option>
<option value="0730" <?php if($endtime=='0730') { ?> selected <?php } ?> >07:30</option>
<option value="0745" <?php if($endtime=='0745') { ?> selected <?php } ?> >07:45</option>
<option value="0800" <?php if($endtime=='0800') { ?> selected <?php } ?> >08:00</option>
<option value="0815" <?php if($endtime=='0815') { ?> selected <?php } ?> >08:15</option>
<option value="0830" <?php if($endtime=='0830') { ?> selected <?php } ?> >08:30</option>
<option value="0845" <?php if($endtime=='0845') { ?> selected <?php } ?> >08:45</option>
</select> &nbsp;&nbsp; <?php if($starttime!="" && $endtime=="" ) { ?>
	 <input type="button" style="background-color:red;padding:5px;color:#fff;" name="std" value="Clock Out" onClick="doendtime('<?php echo $kpo ?>');" >


	 <?php } ?>
     <input type="hidden" name="routename" value="<?php echo $route_name; ?>" />
				<br/>
				 <b> <span style="color:red;font-size:11px;font-weight:normal" > Note :- please add time in military time format like 13:00 for 1pm </span>  </b><br/>
				</div>

			<p>
			<div style="margin-bottom:10px;margin-top:8px;">
			Expense Type &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :   <input type="text" name="exptype" value="<?php echo $exptype; ?>" style="border:1px soild #000;width:90px;" />
			<br/>
			</div>
			<div style="" >
			Expense Amount : $ <input type="text" name="expr" value="<?php echo $expr; ?>" style="border:1px soild #000;width:30px;" />
			</div>
			<input type="hidden" name="driverc" value="<?php echo $vehicle; ?>" /> <br/> <input type="submit" style="background-color:green;padding:5px;color:#fff;" name="std" value="Submit Expenses"  />
			</p>

		   <br/>
			</td>
			</tr>
			</table>
			</from>
				<div style="width:99%; background:grey; padding:5px; margin-bottom:7px;">
			<a href="http://www.shell.us/motorist/shell-station-locator-and-route-planner.html#iframe-Lz9jb3VudHJ5PVVTJmxhbmd1YWdlPWVuX1VTLz9jb3VudHJ5PVVTJmxhbmd1YWdlPWVuX1VTJnNpdGU9cnRsJm1vZGVzZWxlY3RlZD10cnVl" target="_blank" style="color:#fff;text-decoration:underline;">Fuel Station Finder</a>
			</div>

			<?php
    $sel_last="select popup from mg_shipping_delivery_window where `window` LIKE '%".$kbt."' and driver_email='".$customerData["email"]."' and popup is not null order by `nowtime` desc limit 0,1";
			  $last=$connection->fetchOne($sel_last);
			  if($last!="") {
	?>

			 <button class="my_popup_open" style="background-color:green;padding:5px;color:#fff;margin-top:10px;margin-bottom:10px;">Track Time </button>

  <!-- Add content to the popup -->

    <?php if($last>0 && $last>=15 )
	{
	?>
	 <div id="my_popup" style="width:200px;height:auto;background:#d5ffd5;color:#000;padding:30px;">
	<div> Note : You were <?php echo $last; ?> minutes earlier to deliver last order. It appears that you are slightly off of your route plan timing. Please be sure to call any of your future customers if it appears that you will be early or late to deliver. </div>
    <?php }  else if($last<0 && $last<=-15 ) { ?>
	 <div id="my_popup" style="width:200px;height:auto;background:#ffd9d9;color:#000;padding:30px;">
	<div> Note : You were <?php echo abs($last); ?> minutes late to deliver last order. It appears that you are slightly off of your route plan timing. Please be sure to call any of your future customers if it appears that you will be early or late to deliver. </div>
	<?php } ?>

    <!-- Add an optional button to close the popup -->
    <button class="my_popup_close" style="background-color:red;padding:5px;color:#fff;margin-top:10px;margin-bottom:10px;">Close</button>

  </div>


  <!-- Include jQuery Popup Overlay -->


  <style>
  #fade,
#fade_wrapper,

#fade_background {
  -webkit-tranzition: all 0.3s;
     -moz-tranzition: all 0.3s;
          transition: all 0.3s;
}
  </style>
  <script>

  /*!
 * jQuery Popup Overlay
 *
 * @version 1.6.0
 * @requires jQuery v1.7.1+
 * @link http://vast-eng.github.com/jquery-popup-overlay/
 */
(function ($) {

    var $window = $(window);
    var options = {};
    var zindexvalues = [];
    var lastclicked = [];
    var onevisible = false;
    var oneormorevisible = false;
    var scrollbarwidth;
    var focushandler = null;
    var blurhandler = null;
    var escapehandler = null;
    var bodymarginright = null;
    var opensuffix = '_open';
    var closesuffix = '_close';
    var focusedelementbeforepopup = null;

    var methods = {

        _init: function (el) {
            var $el = $(el);
            var options = $el.data('popupoptions');
            lastclicked[el.id] = false;
            zindexvalues[el.id] = 0;

            if (!$el.data('popup-initialized')) {
                $el.attr('data-popup-initialized', 'true');
                methods._initonce(el);
            }

            if (options.autoopen) {
                setTimeout(function() {
                    methods.show(el, 0);
                }, 0);
            }
        },

        _initonce: function (el) {
            var $body = $('body');
            var $wrapper;
            var options = $el.data('popupoptions');
            bodymarginright = parseInt($body.css('margin-right'), 10);

            if (options.type == 'tooltip') {
                options.background = false;
                options.scrolllock = false;
            }

            if (options.scrolllock) {
                // Calculate the browser's scrollbar width dynamically
                var parent;
                var child;
                if (typeof scrollbarwidth === 'undefined') {
                    parent = $('<div style="width:50px;height:50px;overflow:auto"><div/></div>').appendTo('body');
                    child = parent.children();
                    scrollbarwidth = child.innerWidth() - child.height(99).innerWidth();
                    parent.remove();
                }
            }

            if (!$el.attr('id')) {
                $el.attr('id', 'j-popup-' + parseInt(Math.random() * 100000000));
            }

            $el.addClass('popup_content');

            $body.prepend(el);

            $el.wrap('<div id="' + el.id + '_wrapper" class="popup_wrapper" />');

            $wrapper = $('#' + el.id + '_wrapper');

            $wrapper.css({
                opacity: 0,
                visibility: 'hidden',
                position: 'absolute',
                overflow: 'auto'
            });

            $el.css({
                opacity: 0,
                visibility: 'hidden',
                display: 'inline-block'
            });

            if (options.setzindex && !options.autozindex) {
                $wrapper.css('z-index', '2001');
            }

            if (!options.outline) {
                $el.css('outline', 'none');
            }

            if (options.transition) {
                $el.css('transition', options.transition);
                $wrapper.css('transition', options.transition);
            }

            // Hide popup content from screen readers initially
            $(el).attr('aria-hidden', true);

            if ((options.background) && (!$('#' + el.id + '_background').length)) {

                var popupbackground = '<div id="' + el.id + '_background" class="popup_background"></div>';

                $body.prepend(popupbackground);

                var $background = $('#' + el.id + '_background');

                $background.css({
                    opacity: 0,
                    visibility: 'hidden',
                    backgroundColor: options.color,
                    position: 'fixed',
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 0
                });

                if (options.setzindex && !options.autozindex) {
                    $background.css('z-index', '2000');
                }

                if (options.transition) {
                    $background.css('transition', options.transition);
                }
            }

            if (options.type == 'overlay') {
                $el.css({
                    textAlign: 'left',
                    position: 'relative',
                    verticalAlign: 'middle'
                });

                $wrapper.css({
                    position: 'fixed',
                    top: 0,
                    right: 0,
                    left: 0,
                    bottom: 0,
                    textAlign: 'center'
                });

                // CSS vertical align helper
                $wrapper.append('<div class="popup_align" />');

                $('.popup_align').css({
                    display: 'inline-block',
                    verticalAlign: 'middle',
                    height: '100%'
                });
            }

            // Add WAI ARIA role to announce dialog to screen readers
            $el.attr('role', 'dialog');

            var openelement =  (options.openelement) ? options.openelement : ('.' + el.id + opensuffix);

            $(openelement).each(function (i, item) {
                $(item).attr('data-popup-ordinal', i);

                if (!$(item).attr('id')) {
                    $(item).attr('id', 'open_' + parseInt((Math.random() * 100000000), 10));
                }
            });

            // Set aria-labelledby (if aria-label or aria-labelledby is not set in html)
            if (!($el.attr('aria-labelledby') || $el.attr('aria-label'))) {
                $el.attr('aria-labelledby', $(openelement).attr('id'));
            }

            $(document).on('click', openelement, function (e) {
                if (!($el.data('popup-visible'))) {
                    var ord = $(this).data('popup-ordinal');

                    // Show element when clicked on `open` link.
                    // setTimeout is to allow `close` method to finish (for issues with multiple tooltips)
                    setTimeout(function() {
                        methods.show(el, ord);
                    }, 0);

                    e.preventDefault();
                }
            });

            // Handler: `close` element
            var closeelement = (options.closeelement) ? options.closeelement : ('.' + el.id + closesuffix);
            $(document).on('click', closeelement, function (e) {
                methods.hide(el);
                e.preventDefault();
            });

            if (options.detach) {
                $el.hide().detach();
            } else {
                $wrapper.hide();
            }
        },

        /**
         * Show method
         *
         * @param {object} el - popup instance DOM node
         * @param {number} ordinal - order number of an `open` element
         */
        show: function (el, ordinal) {
            var $el = $(el);

            if ($el.data('popup-visible')) return;

            // Initialize if not initialized. Required for: $('#popup').popup('show')
            if (!$el.data('popup-initialized')) {
                methods._init(el);
            }
            $el.attr('data-popup-initialized', 'true');

            var $body = $('body');
            var options = $el.data('popupoptions');
            var $wrapper = $('#' + el.id + '_wrapper');
            var $background = $('#' + el.id + '_background');

            // `beforeopen` callback event
            callback(el, ordinal, options.beforeopen);

            // Remember last clicked place
            lastclicked[el.id] = ordinal;

            if (options.detach) {
                $wrapper.prepend(el);
                $el.show();
            } else {
                $wrapper.show();
            }

            setTimeout(function() {

                $wrapper.css({
                    visibility: 'visible',
                    opacity: 1
                });

                $('html').addClass('popup_visible').addClass('popup_visible_' + el.id);
                $el.addClass('popup_content_visible');
            }, 20);


            $el.css({
                'visibility': 'visible',
                'opacity': 1
            });

            // Disable background layer scrolling when popup is opened
            if (options.scrolllock) {
                $body.css('overflow', 'hidden');
                if ($body.height() > $window.height()) {
                    $body.css('margin-right', bodymarginright + scrollbarwidth);
                }
            }

            setTimeout(function () {
                // Set event handlers
                if(!onevisible) {
                    if (options.keepfocus) {
                        $(document).on('focusin', focushandler)
                    };

                    if (options.blur) {
                        $(document).on('click', blurhandler);
                    }

                    if (options.escape) {
                        $(document).on('keydown', escapehandler);
                    }
                }

                // Set plugin state
                if (!onevisible) {
                    onevisible = true;
                } else {
                    oneormorevisible = true;
                }
            }, 0);

            $el.data('popup-visible', true);

            // Position popup
            methods.reposition(el, ordinal);

            // Show background
            if (options.background) {
                $background.css({
                    'visibility': 'visible',
                    'opacity': options.opacity
                });

                // Fix IE8 issue with background not appearing
                setTimeout(function() {
                    $background.css({
                        'opacity': options.opacity
                    });
                }, 0);
            }

            // Remember which element had focus before opening a popup
            focusedelementbeforepopup = document.activeElement;

            // Handler: Keep focus inside dialog box
            if (options.keepfocus) {

                // Make holder div focusable
                $el.attr('tabindex', -1);

                // Focus popup or user specified element.
                // Initial timeout of 50ms is set to give some time to popup to show after clicking on
                // `open` element, and after animation is complete to prevent background scrolling.
                setTimeout(function() {
                    if (options.focuselement) {
                        $(options.focuselement).focus();
                    } else {
                        $el.focus();
                    }
                }, options.focusdelay);

                // Handler for keyboard focus
                focushandler = function(event) {
                    var dialog = document.getElementById(el.id);
                    if (!dialog.contains(event.target)) {
                        event.stopPropagation();
                        dialog.focus();
                    }
                };
            }

            // Calculating maximum z-index
            if (options.autozindex) {

                var elements = document.getElementsByTagName('*');
                var len = elements.length;
                var maxzindex = 0;

                for(var i=0; i<len; i++){

                    var elementzindex = $(elements[i]).css('z-index');

                    if(elementzindex !== 'auto'){

                      elementzindex = parseInt(elementzindex);

                      if(maxzindex < elementzindex){
                        maxzindex = elementzindex;
                      }
                    }
                }

                zindexvalues[el.id] = maxzindex;

                // Add z-index to the wrapper
                if (zindexvalues[el.id] > 0) {
                    $wrapper.css({
                        zIndex: (zindexvalues[el.id] + 2)
                    });
                }

                // Add z-index to the background
                if (options.background) {
                    if (zindexvalues[el.id] > 0) {
                        $('#' + el.id + '_background').css({
                            zIndex: (zindexvalues[el.id] + 1)
                        });
                    }
                }
            }

            // Handler: Hide popup if clicked outside of it
            if (options.blur) {
                blurhandler = function (e) {
                    if (!$(e.target).parents().andSelf().is('#' + el.id)) {
                        methods.hide(el);
                    }
                };
            }

            // Handler: Close popup on ESC key
            if (options.escape) {
                escapehandler = function (e) {
                    if (e.keyCode == 27 && $el.data('popup-visible')) {
                        methods.hide(el);
                    }
                };
            }

            // Hide main content from screen readers
            $(options.pagecontainer).attr('aria-hidden', true);

            // Reveal popup content to screen readers
            $el.attr('aria-hidden', false);

            $wrapper.one('transitionend', function() {
                callback(el, ordinal, options.opentransitionend);
            });

            callback(el, ordinal, options.onopen);
        },

        /**
         * Hide method
         *
         * @param {object} el - popup instance DOM node
         */
        hide: function (el) {

            var $body = $('body');
            var $el = $(el);
            var options = $el.data('popupoptions');
            var $wrapper = $('#' + el.id + '_wrapper');
            var $background = $('#' + el.id + '_background');

            $el.data('popup-visible', false);

            if (oneormorevisible) {
                $('html').removeClass('popup_visible_' + el.id);
                oneormorevisible = false;
            } else {
                $('html').removeClass('popup_visible').removeClass('popup_visible_' + el.id);
                onevisible = false;
            }

            $el.removeClass('popup_content_visible');

            // Re-enable scrolling of background layer
            if (options.scrolllock) {
                setTimeout(function() {
                    $body.css({
                        overflow: 'visible',
                        'margin-right': bodymarginright
                    });
                }, 10); // 10ms added for CSS transition in Firefox which doesn't like overflow:auto
            }

            // Unbind blur handler
            if (options.blur) {
                $(document).off('click', blurhandler);
            }

            if (options.keepfocus) {

                // Unbind focus handler
                $(document).off('focusin', focushandler);

                // Focus back on saved element
                setTimeout(function() {
                    if ($(focusedelementbeforepopup).is(':visible')) {
                        focusedelementbeforepopup.focus();
                    }
                }, 0);
            }

            // Unbind ESC key handler
            if (options.escape) {
                $(document).off('keydown', escapehandler);
            }

            // Hide popup
            $wrapper.css({
                'visibility': 'hidden',
                'opacity': 0
            });
            $el.css({
                'visibility': 'hidden',
                'opacity': 0
            });

            // Hide background
            if (options.background) {
                $background.css({
                    'visibility': 'hidden',
                    'opacity': 0
                });
            }

            // After closing CSS transition is over... (if transition is set and supported)
            $el.one('transitionend', function(e) {

                if (!($el.data('popup-visible'))) {
                    if (options.detach) {
                        $el.hide().detach();
                    } else {
                        $wrapper.hide();
                    }
                }

                if (!options.notransitiondetach) {
                    callback(el, lastclicked[el.id], options.closetransitionend);
                }
            });

            if (options.notransitiondetach) {
                if (options.detach) {
                    $el.hide().detach();
                } else {
                    $wrapper.hide();
                }
            }

            // Reveal main content to screen readers
            $(options.pagecontainer).attr('aria-hidden', false);

            // Hide popup content from screen readers
            $el.attr('aria-hidden', true);

            // `onclose` callback event
            callback(el, lastclicked[el.id], options.onclose);
        },

        /**
         * Toggle method
         *
         * @param {object} el - popup instance DOM node
         * @param {number} ordinal - order number of an `open` element
         */
        toggle: function (el, ordinal) {
            if ($el.data('popup-visible')) {
                methods.hide(el);
            } else {
                setTimeout(function() {
                    methods.show(el, ordinal);
                }, 0);
            }
        },

        /**
         * Reposition method
         *
         * @param {object} el - popup instance DOM node
         * @param {number} ordinal - order number of an `open` element
         */
        reposition: function (el, ordinal) {
            var $el = $(el);
            var options = $el.data('popupoptions');
            var $wrapper = $('#' + el.id + '_wrapper');
            var $background = $('#' + el.id + '_background');

            ordinal = ordinal || 0;

            // Tooltip type
            if (options.type == 'tooltip') {
                $wrapper.css({
                    'position': 'absolute'
                });
                var openelement =  (options.openelement) ? options.openelement : ('.' + el.id + opensuffix);
                var $elementclicked = $(openelement + '[data-popup-ordinal="' + ordinal + '"]');
                var linkOffset = $elementclicked.offset();

                // Horizontal position for tooltip
                if (options.horizontal == 'right') {
                    $wrapper.css('left', linkOffset.left + $elementclicked.outerWidth() + options.offsetleft);
                } else if (options.horizontal == 'leftedge') {
                    $wrapper.css('left', linkOffset.left + $elementclicked.outerWidth() - $elementclicked.outerWidth() +  options.offsetleft);
                } else if (options.horizontal == 'left') {
                    $wrapper.css('right', $(window).width() - linkOffset.left  - options.offsetleft);
                } else if (options.horizontal == 'rightedge') {
                    $wrapper.css('right', $(window).width()  - linkOffset.left - $elementclicked.outerWidth() - options.offsetleft);
                } else {
                    $wrapper.css('left', linkOffset.left + ($elementclicked.outerWidth() / 2) - ($el.outerWidth() / 2) - parseFloat($el.css('marginLeft')) + options.offsetleft);
                }

                // Vertical position for tooltip
                if (options.vertical == 'bottom') {
                    $wrapper.css('top', linkOffset.top + $elementclicked.outerHeight() + options.offsettop);
                } else if (options.vertical == 'bottomedge') {
                    $wrapper.css('top', linkOffset.top + $elementclicked.outerHeight() - $el.outerHeight() + options.offsettop);
                } else if (options.vertical == 'top') {
                    $wrapper.css('bottom', $(window).height() - linkOffset.top - options.offsettop);
                } else if (options.vertical == 'topedge') {
                    $wrapper.css('bottom', $(window).height() - linkOffset.top - $el.outerHeight() - options.offsettop);
                } else {
                    $wrapper.css('top', linkOffset.top + ($elementclicked.outerHeight() / 2) - ($el.outerHeight() / 2) - parseFloat($el.css('marginTop')) + options.offsettop);

                }

            // Overlay type
            } else if (options.type == 'overlay') {

                // Horizontal position for overlay
                if (options.horizontal) {
                    $wrapper.css('text-align', options.horizontal);
                } else {
                    $wrapper.css('text-align', 'center');
                }

                // Vertical position for overlay
                if (options.vertical) {
                    $el.css('vertical-align', options.vertical);
                } else {
                    $el.css('vertical-align', 'middle');
                }
            }
        }

    };

    /**
     * Callback event calls
     *
     * @param {object} el - popup instance DOM node
     * @param {number} ordinal - order number of an `open` element
     * @param {function} func - callback function
     */
    var callback = function (el, ordinal, func) {
        var openelement =  (options.openelement) ? options.openelement : ('.' + el.id + opensuffix);
        var elementclicked = $(openelement + '[data-popup-ordinal="' + ordinal + '"]');
        if (typeof func == 'function') {
            func(elementclicked);
        }
    };

    /**
     * Plugin API
     */
    $.fn.popup = function (customoptions) {
        return this.each(function () {

            $el = $(this);

            if (typeof customoptions === 'object') {  // e.g. $('#popup').popup({'color':'blue'})
                var opt = $.extend({}, $.fn.popup.defaults, customoptions);
                $el.data('popupoptions', opt);
                options = $el.data('popupoptions');

                methods._init(this);

            } else if (typeof customoptions === 'string') { // e.g. $('#popup').popup('hide')
                if (!($el.data('popupoptions'))) {
                    $el.data('popupoptions', $.fn.popup.defaults);
                    options = $el.data('popupoptions');
                }

                methods[customoptions].call(this, this);

            } else { // e.g. $('#popup').popup()
                if (!($el.data('popupoptions'))) {
                    $el.data('popupoptions', $.fn.popup.defaults);
                    options = $el.data('popupoptions');
                }

                methods._init(this);

            }

        });
    };

    $.fn.popup.defaults = {
        type: 'overlay',
        autoopen: false,
        background: true,
        color: 'black',
        opacity: '0.5',
        horizontal: 'center',
        vertical: 'middle',
        offsettop: 0,
        offsetleft: 0,
        escape: true,
        blur: true,
        setzindex: true,
        autozindex: false,
        scrolllock: false,
        keepfocus: true,
        focuselement: null,
        focusdelay: 50,
        outline: false,
        pagecontainer: null,
        detach: false,
        openelement: null,
        closeelement: null,
        transition: null,
        notransitiondetach: false,
        beforeopen: function(){},
        onclose: function(){},
        onopen: function(){},
        opentransitionend: function(){},
        closetransitionend: function(){}
    };

})(jQuery);

  </script>
  <script>
    jQuery(document).ready(function() {

      // Initialize the plugin
     jQuery('#my_popup').popup({

	 <?php if(isset($_GET["tip"])) { ?>
            opacity: 0.3,
            transition: 'all 0.3s',
			autoopen: true
	 <?php } else { ?>
	        opacity: 0.3,
            transition: 'all 0.3s'
	 <?php } ?>

         });

    });
  </script>

  <?php } ?>

			<table border=1 style="border-collapse:collapse;font-size:11px;margin-bottom:120px;">
			<?php

			$sel_rnote="select note from route_driver where route='".$route_name."' and rid='".$route_id."'";
		    $rnote=$connection->fetchOne($sel_rnote);

			 if($rnote!="") { ?>

			<tr style="background-color:red;color:#fff;">
			<th style="text-align:left;font-size:12px;background-color:red;text-align:center;" colspan="2"><b>Note</b></th>
			</tr>

			<tr >
			<th style="text-align:left;font-size:12px;background-color:white;font-weight:normal;padding:8px;color:#000;" colspan="2"><?php echo $rnote; ?></th>
			</tr>


			<?php } ?>

			<tr style="background-color:green;color:#fff;">
			<th style="text-align:left;font-size:12px;"><b>Customer Information</b></th>
			</tr>

		 <?php
		 }

		  $check=explode("-",$kbt2);
		  $check2[$i] =  $check[1];

		  foreach ($rows as $value) {
			   if($value["order_number"]!="" && $value["order_number"]!='100003190')
			   {
				   $sql2 = "SELECT entity_id FROM mg_sales_flat_order_grid where increment_id='".$value["order_number"]."'";
				   $rows2 = $connection->fetchOne($sql2);

				   $sql22 = "SELECT customer_id FROM mg_sales_flat_order_grid where increment_id='".$value["order_number"]."'";
				   $rows22 = $connection->fetchOne($sql22);

				   $customer = "" ;
				   $customer = Mage::getModel('customer/customer')->load($rows22);

				   $sql222 = "SELECT entity_id FROM mg_sales_flat_order_grid where customer_id='".$rows22."' and increment_id!='".$value["order_number"]."'";
				   $rows222 = $connection->fetchOne($sql222);


				   $sql22444 = "SELECT unattended_shipping FROM `mg_shipping_delivery_window` where `order_number`='".$value['order_number']."'";
				  $rows12224 = $connection->fetchOne($sql22444);


				   $efefe = "SELECT ideltime FROM `mg_shipping_delivery_window` where `order_number`='".$value['order_number']."'";
				   $ideltime = $connection->fetchOne($efefe);


				   $sel_sql="select status from drivlist where ordernumber='".$value["order_number"]."'";
				   $color=$connection->fetchOne($sel_sql);

				   $sqlstatus = "SELECT status FROM mg_sales_flat_order_grid  where increment_id='".$value["order_number"]."'";
				   $rowstatus = $connection->fetchOne($sqlstatus);

				   $sql3  = "SELECT SUM(qty_ordered) FROM `mg_sales_flat_order_item` where `order_id`='".$rows2."'";
				   $rows3 = $connection->fetchOne($sql3);
				   $order = Mage::getModel("sales/order")->load($rows2); //load order by order id
				   $address = $order->getShippingAddress();
				   $shipping_info = $order->getShippingAddress()->getData();
				   $temp = explode("|",$value['window']);
				   $temp3 = explode(".",$rows3);
				   $check22 = 0;

				if($customer->getId()!="813" && $customer->getId()!="1205" && $rowstatus!='canceled')
				   {

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

					if($ideltime>=60)
					{
					    echo '<tr style="height:40px;background:red;padding:5px;color:#fff;text-align:center;">
						<td colspan="2" style="text-align:center;"> Idle Time : '.round($ideltime/60,0).' Minutes </td><tr/>';
					}

		 ?>		   <tr <?php if($color==1) { ?> style="background:#daebe1;" <?php } ?> >
				   <td <?php if($rows222 == "" ) {  echo 'style="color:#1c60ff;"'; } ?> >

				   <?php if($rows12224=='yes')
					{
					echo "<span style='color:red;font-size:15px;font-weight:bold;' >Unattended Order</span><br/> ";
					}
				  ?>

				   <span style="font-size:12px;font-weight:bold;"> <a href="<?php echo Mage::getBaseUrl(); ?>?ooid=<?php echo $value["order_number"]; ?>" <?php if($rows222 == "" ) {  echo 'style="color:#1c60ff;font-weight:bold;font-size:12px;text-decoration:underline;"'; } else { echo 'style="color:#000;font-weight:bold;font-size:12px;text-decoration:underline;"'; } ?> onClick="return docheck('<?php echo $number["d"]; ?>','<?php echo $number["cf"]; ?>','<?php echo $number["bag"]; ?>','<?php echo $number["loose"]; ?>','<?php echo $number["cold_bin"]; ?>','<?php echo $number["cold_bag"]; ?>','<?php echo $number["frozen_bin"]; ?>','<?php echo $number["frozen_bag"]; ?>','<?php echo $value["loaded"]; ?>');" > <?php $custName = $address->getName(); ?> <?php if(trim($custName)=="") { echo $customer->getName(); } else { echo $custName; } ?> </a> # <?php echo $value["rc"]; ?> </span>
					<br/>
					 <?php echo $value["rt"]; ?> <span style=""> ( ETA : <?php echo $value["estt"]; ?>) </span>  <br/>
					<?php
					 $custAddr = $address->getStreetFull();
					 $region2 = $address->getCity();
					 $region = $address->getRegion();
					 $country = $address["postcode"];
	                ?>
	<a href="http://maps.google.com/?q=<?php echo $custAddr." ".$region2." ".$region." ".$country ; ?> " target= _blank  style="text-decoration:underline;">View Map</a> <br/>
					<?php
					echo $custAddr = $address->getStreetFull()."<br/>";
					echo $region2 = $address->getCity()."&nbsp;&nbsp;";
					echo $region = $address->getRegion()."&nbsp;&nbsp;";
					echo $country = $address["postcode"];
					?>	<br/>
						<span style="color:#000;font-family:verdana;font-weight:bold;"> <?php echo $shipping_info["telephone"]; ?></span>					<?php $groupId = $customer->getGroupId();
				         $sel_group_name="select customer_group_code from mg_customer_group where customer_group_id='".$groupId."'";
				         $group_name=$connection->fetchOne($sel_group_name);
						?>
						 <br/>
						<span style="color:black;font-family:verdana;font-weight:bold;"> Group :- <?php echo $group_name; ?></span>
					<?php
						if($shipping_info["information"]!="")
						{
					?>
						<br/>
					<span style="color:red;font-family:verdana;font-weight:normal;"> Delivery Instruction :- <?php echo $shipping_info["information"]; ?></span>
					<?php } ?>
						<br/>
						<div style="background-color:#ffd7c4;">
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
					</div>
					</td>
				   </tr>
				   <?php
				   }
			     }
			  }
		  ?>
		</table>
		<?php
		  }
	  }
?>

<?php include 'gmap.php'; ?>