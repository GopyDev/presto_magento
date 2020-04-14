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
					$message = "Hi ".$params['customername'] .",
					
		Your delivery date for order number : $ordernumber has been changed. Your order will now deliver on  $deliver_date $time1";
					mail($params['customeremail'],'Presto fresh grocery',$message);
			
					
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