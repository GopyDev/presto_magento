<?php

    require('config.inc.php');
    require('AuthnetXML.class.php');
	//require('../app/Mage.php');
	
	ini_set('display_errors', 1);
   
	
	/*************************************
		Query for fetching records
		which profiles are Active
	/*************************************/
	//$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
	$database_host = "localhost";
	$database_user = "prestofr_stage";
	$database_password = "GiAZ8sc7gpJS";
	$magento_database = "prestofr_stage";
	$db = mysql_connect($database_host, $database_user, $database_password);
	mysql_select_db($magento_database);
	$sql  = "Select * from mg_aw_sarp2_profile WHERE status = 'active' ORDER BY entity_id DESC";
	$result = mysql_query($sql) or die('Err');
		
		
		$xml = new AuthnetXML(AUTHNET_LOGIN, AUTHNET_TRANSKEY, AuthnetXML::USE_DEVELOPMENT_SERVER);
		$count  = 0;
	while(@$row = mysql_fetch_array($result)) {
		
		$id = "'".$row['reference_id']."'";
		//echo "ID is ".$id;
		$xml->ARBGetSubscriptionStatusRequest(array(
        'refId' => 'Sample',
		
		'subscriptionId' => $row['reference_id']
		));
		
			$date = date('Y-m-d h:m:s');
		if($xml->status != '')
		{
			/*echo "XML status is ".$xml->status;
			echo "<br/>Row status is ".$row['status'];
			exit;*/
			if($xml->status != $row['status']){
				$sql  = "Update mg_aw_sarp2_profile SET status = '".$xml->status."', updated_at = '".$date."'  WHERE reference_id = '".$row['reference_id']."' ";
				$result = mysql_query($sql) or die('Err');
				//$rows       = $connection->fetchAll($sql);
				echo "<br/>";
				echo "ID updated is ".$row['reference_id'];
				echo "<br/>";
			}
		}
		echo "status is ".$xml->status. ' - '.$id;
		echo "<br/>";
		
	}

 
?>

<!DOCTYPE html>
<html>
<html lang="en">
    <head>
        <title></title>
        <style type="text/css">
            table
            {
                border: 1px solid #cccccc;
                margin: auto;
                border-collapse: collapse;
                max-width: 90%;
            }

            table td
            {
                padding: 3px 5px;
                vertical-align: top;
                border-top: 1px solid #cccccc;
            }

            pre
            {
            	overflow-x: auto; /* Use horizontal scroller if needed; for Firefox 2, not needed in Firefox 3 */
            	white-space: pre-wrap; /* css-3 */
            	white-space: -moz-pre-wrap !important; /* Mozilla, since 1999 */
            	white-space: -pre-wrap; /* Opera 4-6 */
            	white-space: -o-pre-wrap; /* Opera 7 */ /*
            	width: 99%; */
            	word-wrap: break-word; /* Internet Explorer 5.5+ */
            }

            table th
            {
                background: #e5e5e5;
                color: #666666;
            }

            h1, h2
            {
                text-align: center;
            }
        </style>
    </head>
    <body>
        <h1>
            ARB :: Get Subscription Status
        </h1>
        <h2>
            Results
        </h2>
        <table>
            <tr>
                <th>Response</th>
                <td><?php echo $xml->messages->resultCode; ?></td>
            </tr>
            <tr>
                <th>code</th>
                <td><?php echo $xml->messages->message->code; ?></td>
            </tr>
            <tr>
                <th>Successful?</th>
                <td><?php echo ($xml->isSuccessful()) ? 'yes' : 'no'; ?></td>
            </tr>
            <tr>
                <th>Error?</th>
                <td><?php echo ($xml->isError()) ? 'yes' : 'no'; ?></td>
            </tr>
            <tr>
                <th>status</th>
                <td><?php echo $xml->status; ?></td>
            </tr>
        </table>
        <h2>
            Raw Input/Output
        </h2>
<?php
    echo $xml;
?>
    </body>
</html>
