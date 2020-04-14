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

class Rainconcert_SurveyManager_Block_Adminhtml_Questions_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('survey_questions_form', array('legend' => Mage::helper('surveymanager')->__('Add Question Details')));

        $fieldset->addField('survey_qn_title', 'text', array(
            'label' => Mage::helper('surveymanager')->__('Question Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'survey_qn_title',
        ));

        
        $fieldset->addField('survey_qn_desc', 'textarea', array(
            'label' => Mage::helper('surveymanager')->__('Question Description'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'survey_qn_desc',
        ));
        
        $fieldset->addField('survey_ans_type', 'select', array(
            'label' => Mage::helper('surveymanager')->__('Answer Type!'),
            'name' => 'survey_ans_type',
            'values' => array(
               /* array(
                    'value' => 1,
                    'label' => Mage::helper('surveymanager')->__('Satisfaction Bubbles'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('surveymanager')->__('Excellent-Poor Bubbles'),
                ),*/
                array(
                    'value' => 3,
                    'label' => Mage::helper('surveymanager')->__('Text Field'),
                ),
                array(
                    'value' => 4,
                    'label' => Mage::helper('surveymanager')->__('Text Area'),
                ),
                /*array(
                    'value' => 5,
                    'label' => Mage::helper('surveymanager')->__('5 Star Rating'),
                ),*/
                array(
                    'value' => 6,
                    'label' => Mage::helper('surveymanager')->__('Yes/No'),
                ),
                array(
                    'value' => 7,
                    'label' => Mage::helper('surveymanager')->__('Custom Radio'),
                ),
                array(
                    'value' => 8,
                    'label' => Mage::helper('surveymanager')->__('Check Box'),
                ),
                array(
                    'value' => 9,
                    'label' => Mage::helper('surveymanager')->__('Select Box'),
                ),
            ),
        ));
        
        $fieldset->addField('is_active', 'select', array(
            'label' => Mage::helper('surveymanager')->__('Status'),
            'name' => 'is_active',
            'values' => array(
                array(
                    'value' => 0,
                    'label' => Mage::helper('surveymanager')->__('InActive'),
                ),
                array(
                    'value' => 1,
                    'label' => Mage::helper('surveymanager')->__('Active'),
                ),
            ),
        ));

        if (Mage::getSingleton('adminhtml/session')->getSurveyQuestionData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getSurveyQuestionData());
            Mage::getSingleton('adminhtml/session')->setSurveyQuestionData(null);
        } elseif (Mage::registry('survey_question_data')) {
            $form->setValues(Mage::registry('survey_question_data')->getData());
        }
        return parent::_prepareForm();
    }

}
