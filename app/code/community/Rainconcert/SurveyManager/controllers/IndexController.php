<?php
/**
* Rainconcert Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * Rainconcert does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   Rainconcert
 * @package    Rainconcert_SurveyManager
 * @version    1.1.1
 */


class Rainconcert_SurveyManager_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {

        $this->loadLayout();

        $this->renderLayout();
    }
    
    public function resultAction() {
        
        if ($data = $this->getRequest()->getPost()) {
            
            $customerId = NULL;
            if(Mage::getSingleton('customer/session')->isLoggedIn()) {
                $customerData = Mage::getSingleton('customer/session')->getCustomer();
                $customerId = $customerData->getId();
            }
            
            $sessionId = Mage::getSingleton("core/session")->getEncryptedSessionId();
			$db_write1 = Mage::getSingleton('core/resource')->getConnection('core_write');
			$driverId = $_REQUEST['driverId'];
			$customerId = $_REQUEST['customerId'];
			$pickerId = $_REQUEST['pickerId'];
			$orderId = $_REQUEST['orderId'];
			
			$qry115 = "SELECT record_id FROM mg_survey_result where order_id='".$_REQUEST['orderId']."'";
			$result115 = $db_write1->fetchOne($qry115);
			if($result115>=1)
			{
			exit(0);
			}
			
			$qry11 = "SELECT record_id FROM mg_survey_result ORDER BY record_id DESC LIMIT 0,1";
			$result11 = $db_write1->fetchAll($qry11);
			$RecordId = $result11[0]['record_id'];
			if($RecordId > 0){
				$RecordId = $RecordId + 1;
			}else{
				$RecordId = 1;
			}
			/*$driverId = $_REQUEST['driverId'];
			$customerId = $_REQUEST['customerId'];
			$pickerId = $_REQUEST['pickerId'];*/
			//echo "Record Id is ".$RecordId;
			//exit;
            foreach($data['survey_values'] as $key=>$surveyValue){
                $model = Mage::getModel('surveymanager/surveyresults');
                $model->setCustomerId($customerId)
					  ->setrecord_id($RecordId)
					  ->setOrderId($orderId)
					  ->setDriverId($driverId)
					  ->setPickerId($pickerId)
                      ->setSessionId($sessionId)  
                      ->setSurveyQnId($key)
                      ->setIsActive(1);
                        
                $format = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
                if (isset($data['created_time']) && $data['created_time']) {
                    $dateFrom = Mage::app()->getLocale()->date($data['created_time'], $format);
                    $model->setCreatedTime(Mage::getModel('core/date')->gmtDate(null, $dateFrom->getTimestamp()));
                    $model->setUpdateTime(Mage::getModel('core/date')->gmtDate());
                } else {
                    $model->setCreatedTime(Mage::getModel('core/date')->gmtDate());
                }
                $model->save();

                if($model->getId()){               

                    $modelSurveyResultValue = Mage::getModel('surveymanager/surveyresultvalue');
                    $modelSurveyResultValue->setData('survey_result_id', $model->getId())
                                ->setData('survey_result_value', $surveyValue)
                                ->setData('survey_ans_type_id', $data['survey_ans_type'][$key]);
                    
                    if (isset($data['created_time']) && $data['created_time']) {
                        $dateFrom = Mage::app()->getLocale()->date($data['created_time'], $format);
                        $modelSurveyResultValue->setCreatedTime(Mage::getModel('core/date')->gmtDate(null, $dateFrom->getTimestamp()));
                        $modelSurveyResultValue->setUpdateTime(Mage::getModel('core/date')->gmtDate());
                    } else {
                        $modelSurveyResultValue->setCreatedTime(Mage::getModel('core/date')->gmtDate());
                    }
                
                    $modelSurveyResultValue->save();
                }
            }
            
			
			/***************************
				For update points
			/***************************/
			//$db_write1 = Mage::getSingleton('core/resource')->getConnection('core_write');
			
			/*********************
				Insert point for displaying
				my reward section
			/**********************/
			$qry1 = "SELECT reward_id FROM mg_rewardpoints_customer WHERE customer_id = '".$customerId."' ";
			$result1 = $db_write1->fetchAll($qry1);
			$rewardId = $result1[0]['reward_id'];
			
			
			$qry2 = "SELECT email FROM mg_sales_flat_order_address WHERE customer_id = '".$customerId."' LIMIT 0,1";
			$result2 = $db_write1->fetchAll($qry2);
			$customerEmail = $result2[0]['email'];
			$surveyTitle = 'Survey Points for Order #'.$orderId;
			
			$exp_date='2019-05-20 11:10:01';
			
			$qry2 = "INSERT INTO mg_rewardpoints_transaction
					(reward_id,customer_id,customer_email,title,action,action_type,store_id,point_amount,point_used,real_point,status,created_time,updated_time,expire_email,order_id,order_increment_id,order_base_amount,order_amount,base_discount,discount,extra_content)
					VALUES('".$rewardId."','".$customerId."','".$customerEmail."', '".$surveyTitle."', 'survey_points', '1', '1', '50', '0', '50', '3', now(), '".$exp_date."', '0', '".$OrderId."', '".$OrderIncrementId."','".$OrderBaseAmount."', '".$OrderAmount."', 'NULL', 'NULL', 'survey_point')";
				$db_write1->query($qry2);
				
				
				//exit;
			/*********************
				End
			/*********************/
			$qry = "SELECT point_balance FROM mg_rewardpoints_customer where customer_id ='".$customerId."' ";
			$result = $db_write1->fetchAll($qry);
			$availablePoint = $result[0]['point_balance'];
			//echo "<pre>";
			//print_r($result);
			//echo "Available point is ".$availablePoint;
			//exit;
			if($availablePoint>0){
				$point = $availablePoint;
				$point = $point + 50;
				$updateQue = 'UPDATE mg_rewardpoints_customer SET point_balance = "'.$point.'" WHERE customer_id = "'.$customerId.'" ';
				//exit;
				$db_write1->query($updateQue);
				
			}else{
				$point = '50';
				$updateQue = 'UPDATE mg_rewardpoints_customer SET point_balance = "'.$point.'" WHERE customer_id = "'.$customerId.'" ';
				$db_write1->query($updateQue);
			}
			/************************
				End update points
			/************************/
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('surveymanager')->__('Your bonus rewards points have been added to your account. Thank you!'));

            $this->_redirect('*/*/');
            return;
        }
        
        Mage::getSingleton('core/session')->addError(Mage::helper('surveymanager')->__('Unable to save Feedback'));
        $this->_redirect('*/*/');
    }
}
