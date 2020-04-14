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

class Rainconcert_SurveyManager_Block_Adminhtml_Survey_Edit_Tab_Reports extends
                                                        Mage_Adminhtml_Block_Widget_Form
{	
    public function __construct() {
        parent::__construct();
        $this->setTemplate('surveymanager/results.phtml');
    }
    
    public function getResultsCountByQnId( $qnId){
        $surveyResults = Mage::getModel('surveymanager/surveyresults')
                                            ->getCollection()
                                            ->addFieldToFilter( 'survey_qn_id',$qnId);
        return count($surveyResults);        
    }    
    
    public function getResultsByQnId( $qnId){
        $surveyResults = Mage::getModel('surveymanager/surveyresults')
                                            ->getCollection()
                                            ->addFieldToFilter( 'survey_qn_id',$qnId);
        return $surveyResults;
    }
    
    public function getValueByResultId( $resultId){
        $surveyResultValue = Mage::getModel('surveymanager/surveyresultvalue')
                                            ->getValueByResultId( $resultId);
        return $surveyResultValue;
    }
}