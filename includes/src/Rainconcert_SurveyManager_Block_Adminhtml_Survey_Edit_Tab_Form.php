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

class Rainconcert_SurveyManager_Block_Adminhtml_Survey_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('survey_form', array('legend' => Mage::helper('surveymanager')->__('Survey information')));

        $fieldset->addField('survey_name', 'text', array(
            'label' => Mage::helper('surveymanager')->__('Survey Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'survey_name',
        ));

        $fieldset->addField('survey_url', 'text', array(
            'label' => Mage::helper('surveymanager')->__('Survey Url'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'survey_url',
            'class' => 'surveymanager-validate-identifier',
            /*'after_element_html' => '<span class="hint">' . Mage::helper('surveymanager')->__('(eg: domain.com/surveymanager/identifier)') . '</span>'
            . "<script>
                    Validation.add(
                    'surveymanager-validate-identifier', 
                    '" . addslashes(Mage::helper('surveymanager')->__("Please use only letters (a-z or A-Z), numbers (0-9) or symbols '-' and '_' in this field")) . "',
                    function(v, elm) {
                                    var regex = new RegExp(/^[a-zA-Z0-9_-]+$/);
                                    return v.match(regex);
                         }
                    );
                    </script>",*/)
        );
        
        $fieldset->addField('survey_layout_template', 'select', array(
            'label' => Mage::helper('surveymanager')->__('Survey Layout Template'),
            'required' => true,
            'name' => 'survey_layout_template',
            'values' => array(
                array(
                    'value' => 'empty',
                    'label' => Mage::helper('surveymanager')->__('Empty'),
                ),
                array(
                    'value' => 'one_column',
                    'label' => Mage::helper('surveymanager')->__('1 Column'),
                ),
                array(
                    'value' => 'two_columns_left',
                    'label' => Mage::helper('surveymanager')->__('2 Columns-left'),
                ),
                array(
                    'value' => 'two_columns_right',
                    'label' => Mage::helper('surveymanager')->__('2 Columns-right'),
                ),
                array(
                    'value' => 'three_columns',
                    'label' => Mage::helper('surveymanager')->__('3 Column'),
                )
            ))
        );
        
        $fieldset->addField('survey_desc', 'textarea', array(
            'label' => Mage::helper('surveymanager')->__('Survey Description'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'survey_desc',
        ));

        if (Mage::getSingleton('adminhtml/session')->getSurveyData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getSurveyData());
            Mage::getSingleton('adminhtml/session')->setSurveyData(null);
        } elseif (Mage::registry('survey_data')) {
            $form->setValues(Mage::registry('survey_data')->getData());
        }
        return parent::_prepareForm();
    }

}
