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

class Rainconcert_SurveyManager_Block_Adminhtml_Questions_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('survey_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('surveymanager')->__('Survey Question/Answer Manager'));
    }

    protected function _beforeToHtml() {
        $questionId = $this->getRequest()->getParam('id');
        
        $this->addTab('form_section', array(
            'label' => Mage::helper('surveymanager')->__('Manage Questions'),
            'title' => Mage::helper('surveymanager')->__('Manage Questions'),
            'content' => $this->getLayout()->createBlock('surveymanager/adminhtml_questions_edit_tab_form')->toHtml(),
        ));
 
        if($questionId && ($this->helper('surveymanager')->getAnsTypeByQid($questionId)!=3
                && $this->helper('surveymanager')->getAnsTypeByQid($questionId)!=4 )){
            $this->addTab('survey_answer', array(
                'label' => Mage::helper('surveymanager')->__('Manage Answers'),
                'title' => Mage::helper('surveymanager')->__('Manage Answers'),
                'content' => $this->getLayout()->createBlock('surveymanager/adminhtml_questions_edit_tab_answers')->toHtml(),
            ));
        }
        
        return parent::_beforeToHtml();
    }

}
