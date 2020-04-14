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

class Rainconcert_SurveyManager_Block_Adminhtml_Questions_Edit_Tab_Answers extends
                                                        Mage_Adminhtml_Block_Widget_Form
{	
    public function __construct() {
        parent::__construct();
        $this->setTemplate('surveymanager/answers.phtml');
    }
    
    public function getSurveyAnswers()
    {
        $answers = Mage::getModel('surveymanager/surveyanswer')->getCollection()
                                        ->addFieldToFilter('is_active',1);
        
        return $answers;
    }
    
    public function getSurveyAnswerByQn( $qnId)
    {
        $answers = Mage::getModel('surveymanager/surveyanswer')
                                            ->getAnswerByQnId( $qnId);
        
        return $answers;
    }
    
}