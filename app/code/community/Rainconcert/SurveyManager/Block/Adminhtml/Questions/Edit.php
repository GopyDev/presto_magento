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

class Rainconcert_SurveyManager_Block_Adminhtml_Questions_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'surveymanager';
        $this->_controller = 'adminhtml_questions';

        $this->_updateButton('save', 'label', Mage::helper('surveymanager')->__('Save Survey Question/Answers'));
        $this->_updateButton('delete', 'label', Mage::helper('surveymanager')->__('Delete Survey Question'));

        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);

        
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('surveymanager_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'post_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'post_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/editQuestions/');
            }
        ";
    }
    
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/deleteQuestion', array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
    }
    
    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/questions');
    }
    
    public function getHeaderText() {
        if (Mage::registry('surveymanager_data') && Mage::registry('surveymanager_data')->getId()) {
            return Mage::helper('surveymanager')->__("Edit Survey Question/Answers '%s'", $this->htmlEscape(Mage::registry('surveymanager_data')->getTitle()));
        } else {
            return Mage::helper('surveymanager')->__('Add Survey Question/Answers');
        }
    }

}
