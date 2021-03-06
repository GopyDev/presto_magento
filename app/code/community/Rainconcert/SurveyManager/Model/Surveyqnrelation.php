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

class Rainconcert_SurveyManager_Model_Surveyqnrelation extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('surveymanager/surveyqnrelation');
    }
    
    public function getRelationBySurveyId( $surveyId)
    {
        $surveyQnRelations = $this->getCollection()
                                  ->addFieldToFilter('survey_id',$surveyId);
        
        return $surveyQnRelations;
    }
}
