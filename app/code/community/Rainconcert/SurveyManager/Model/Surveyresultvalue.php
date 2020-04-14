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

class Rainconcert_SurveyManager_Model_Surveyresultvalue extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('surveymanager/surveyresultvalue');
    }
    
    public function getResultsCountByAnsId( $ansId)
    {
        $surveyResultValue = $this->getCollection()
                                  ->addFieldToFilter( 'survey_result_value',$ansId);
        return count($surveyResultValue);
    }
    
    public function getValueByResultId( $resultId){
        $surveyResultValues = $this->getCollection()
                                  ->addFieldToFilter( 'survey_result_id',$resultId);
        
        foreach($surveyResultValues as $surveyResultValue){            
            $resultValue = $surveyResultValue->getSurveyResultValue();
        }
        
        return $resultValue;
    }
}
