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
            
            foreach($data['survey_values'] as $key=>$surveyValue){
                $model = Mage::getModel('surveymanager/surveyresults');
                
                $model->setCustomerId($customerId)
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
            
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('surveymanager')->__('Your Feedback is successfully saved'));

            $this->_redirect('*/*/');
            return;
        }
        
        Mage::getSingleton('core/session')->addError(Mage::helper('surveymanager')->__('Unable to save Feedback'));
        $this->_redirect('*/*/');
    }
}
