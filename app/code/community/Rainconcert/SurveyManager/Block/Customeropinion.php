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

class Rainconcert_SurveyManager_Block_Customeropinion extends Mage_Core_Block_Template
{
    protected function _prepareLayout() {
        
        $surveyId = $this->getRequest()->getParam('survey_id');
        
        if($surveyId==null)
            return;
        
        $surveyLayoutTemplate = $this->helper('surveymanager')->getNewLayoutBySurveyId( $surveyId);
        
        if($surveyLayoutTemplate)
        {       
            $this->getLayout()->getBlock('root')
                              ->setTemplate( $surveyLayoutTemplate);
        }
        
        parent::_prepareLayout();        
        return $this;
    }
    
    public function getSurveyDetails()
    {        
        $surveyId = $this->getSurveyId();
        
        if($surveyId!=NULL)
        {
            $surveyDetails = Mage::getModel('surveymanager/survey')
                                        ->getSurveyDetailsById( $surveyId);
            return $surveyDetails;
        }
        return null;
    }
    
    public function getSurveyId()
    {
        $surveyId = $this->getRequest()->getParam('survey_id'); 
        
        if($surveyId!=NULL){
            return $surveyId;
        }
        
        return null;
    }
    
    public function getSurveySubmitUrl(){
        return $this->getUrl('').'surveymanager/index/result/';
    }
}
