<?php
include('connection.php');
ini_set('memory_limit', '3G');
set_time_limit(360000);
error_reporting(E_ALL | E_STRICT);
require_once 'app/Mage.php';
?>
<html>

<body style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<div style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<table cellspacing="0" cellpadding="0" border="0" width="100%">
<tr>
	<td align="center" valign="top" style="padding:20px 0 20px 0;">
		<table bgcolor="#FFFFFF" cellspacing="0" cellpadding="10" border="0" width="650" style="border:1px solid #E0E0E0;">
		
			<tr>
				<td valign="top"><a href="http://www.prestofreshgrocery.com/"><img src="http://www.prestofreshgrocery.com/skin/frontend/default/theme509/images/logo_jpg.png" alt="PrestoFresh Grocerry" style="margin-bottom:10px;" border="0"/></a></td>
			</tr>
		
			<tr>
				<td>
					<?php
						$customerId = $_REQUEST['customerId'];
						$qry1 = "SELECT count(subscribe) FROM mg_survey_subscribe WHERE customer_id = '".$customerId."' ";
						$rs1 = mysql_query($qry1);
						$count = mysql_num_rows($rs1);
						if($count>0){
							$qry = "Update mg_survey_subscribe SET subscribe = '0' WHERE customer_id = '".$customerId."' ";
							$rs = mysql_query($qry);
							if($rs){
								echo "<h2>You are successfully unsubscribe survey email.</h2>";
							}
						}
						else{
							echo "<h2>Please contact to site administrator.</h2>";
						}
					?>
				</td>
			</tr>
		</table>
	</td>
</tr>
</table>
</div>
</body>
</html>