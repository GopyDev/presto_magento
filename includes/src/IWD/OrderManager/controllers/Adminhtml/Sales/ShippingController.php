<?php
class IWD_OrderManager_Adminhtml_Sales_ShippingController extends Mage_Adminhtml_Controller_Action
{
    public function editShippingAction()
    {
        $result = array('status' => 1);

        try {
            $params = $this->getRequest()->getParams();
			Mage::log(print_r($params,true),null,'params.log');
			//Save Delviery dates in order
					
					if($params['delviery_Time'] != ''){
						$window = $params['delviery_Time'] .'|'.$params['delviery_Date'];
					}else{
						$window = $params['delviery_Date'];
					}
					
					if($params['delviery_Date'] != ''){
					$ordernumber = $params['ordernumber'];
					$tablename = 'mg_shipping_delivery_window';
					$write = Mage::getSingleton('core/resource')->getConnection('core_write');
					$query2 =  "update $tablename set window='$window' where order_number=$ordernumber";
					$execute = $write->query($query2);
					
				$findme   = '|';
				$pos = strpos($window, $findme);
				if ($pos !== false) {
					list($time,$deliver_date) =  explode('|',$window);
					list($dd,$mm,$yy) = explode('_',$deliver_date);
					$deliver_date = $mm.'/'.$dd.'/'.$yy;
					// echo "The string '$findme' was found in the string '$mystring'";
					  //   echo " and exists at position $pos";
				} else {
					list($dd,$mm,$yy) = explode('_',$window);
					$deliver_date = $mm.'/'.$dd.'/'.$yy;
					 //echo "The string '$findme' was not found in the string '$mystring'";
				}
					
					$time = str_replace('_',':',$time);
					$time = str_replace('pm',' pm ',$time);
					$time = str_replace(' pm :',' pm to ',$time);
					
					
					//$deliver_date1 = str_replace('_','/',$deliver_date);
					if($time){
						$time1 = 'between ' . $time;	
					}
				//	$deliver_date1 = list()
					$message = '<body style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<div style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<table cellspacing="0" cellpadding="0" border="0" width="100%">
<tr>
    <td align="center" valign="top" style="padding:20px 0 20px 0;">
        <table bgcolor="#FFFFFF" cellspacing="0" cellpadding="10" border="0" width="650" style="border:1px solid #E0E0E0;">
           
            <tr>
                <td valign="top"><a href="http://www.prestofreshgrocery.com/"><img src="http://www.prestofreshgrocery.com/skin/frontend/default/theme509/images/logo_jpg.png" alt="PrestoFresh Grocerry" style="margin-bottom:10px;" border="0"/></a></td>
            </tr>
           
            <tr>
                <td valign="top">
                    <h1 style="font-size:22px; font-weight:normal; line-height:22px; margin:0 0 11px 0;">Hello, '.$params['customername'].'</h1>
					<p style="font-size:14px; line-height:16px; margin:0;">
                        Thank you for your order!
                    </p>
					<p>&nbsp;</p>
					<p style="font-size:14px; line-height:16px; margin:0;"><b>Your order #'.$ordernumber.' was updated</b></p>
					<p style="font-size:14px; line-height:16px; margin:0;">
                       <b>Please review the updates made to your order:</b>
                    </p>
					<p>&nbsp;</p>
					<p style="font-size:14px; line-height:16px; margin:0;">
                      Updated Order Delivery Date/Time:
					</p>
					<p style="font-size:14px; line-height:16px; margin:0;">'.
						 $deliver_date.', &nbsp; '.  $time1
					.'</p>
                 </td>
			</tr>
			<tr>
				<td>
				 
                    <p style="font-size:12px; font-weight:normal; line-height:22px;">
                        If you have any questions about your order, please email us at <a href="mailto:support@prestofreshgrocery.com" style="color:#1E7EC8;">support@prestofreshgrocery.com</a> and we will respond as soon as possible 
                    </p>
                 </td>
            </tr>
         </table>
    </td>
</tr>
</table>
</div>
</body>';
					$headers = "MIME-Version: 1.0" . "\r\n";
					$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
					mail($params['customeremail'],'Presto fresh grocery',$message,$headers);
			
					
					//Update on delviery date order table 
					list($dd,$mm,$yy) = explode('_',$params['delviery_Date']);
					$flexible_delvierydate = $mm.'/'.$dd.'/'.$yy; 
					$table2 = 'mg_deliverydate_order';
					$query3 =  "update $table2 set delivery_date='$flexible_delvierydate' where order_id=$ordernumber";
					$query3_exe = $write->query($query3);
					
					mail($to,$subject,$message);
					}
			//
            Mage::getModel('iwd_ordermanager/shipping')->updateOrderShipping($params);
        } catch (Exception $e) {
            Mage::log($e, null, 'iwd_order_manager.log');
            $result = array('status' => 0, 'error'=>$e->getMessage());
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function getEditShippingFormAction()
    {Mage::log('saveshipping',null,'saveshiping.log');
        $result['status'] = 0;

        try {
            $order_id = $this->getRequest()->getPost('order_id');
            $order = Mage::getModel('sales/order')->load($order_id);

            if ($order) {
                $result['form'] = $this->getLayout()
                    ->createBlock('iwd_ordermanager/adminhtml_sales_order_shipping_form')
                    ->setData('order', $order)
                    ->toHtml();

                $result['status'] = 1;
            }
        } catch (Exception $e) {
            Mage::log($e, null, 'iwd_order_manager.log');
            $result = array('status' => 0, 'error' => $e->getMessage());
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
}