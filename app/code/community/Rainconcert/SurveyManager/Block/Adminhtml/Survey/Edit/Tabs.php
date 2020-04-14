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

class Rainconcert_SurveyManager_Block_Adminhtml_Survey_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('survey_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('surveymanager')->__('Survey Manager'));
    }

    protected function _beforeToHtml() {
        $this->addTab('form_section', array(
            'label' => Mage::helper('surveymanager')->__('Survey Manager'),
            'title' => Mage::helper('surveymanager')->__('Survey Manager'),
            'content' => $this->getLayout()->createBlock('surveymanager/adminhtml_survey_edit_tab_form')->toHtml(),
        ));
        
        $this->addTab('survey_manage', array(
            'label' => Mage::helper('surveymanager')->__('Assign Questions to Survey'),
            'title' => Mage::helper('surveymanager')->__('Assign Questions to Survey'),
            'content' => $this->getLayout()->createBlock('surveymanager/adminhtml_survey_edit_tab_managesurvey')->toHtml(),
        ));
        
        $this->addTab('survey_results', array(
            'label' => Mage::helper('surveymanager')->__('Survey Report'),
            'title' => Mage::helper('surveymanager')->__('Survey Report'),
            'content' => $this->getLayout()->createBlock('surveymanager/adminhtml_survey_edit_tab_reports')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }

}
