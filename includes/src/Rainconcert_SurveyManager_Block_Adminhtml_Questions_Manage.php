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

class Rainconcert_SurveyManager_Block_Adminhtml_Questions_Manage extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'surveymanager';
        $this->_controller = 'adminhtml_questions';
        $this->_headerText = Mage::helper('surveymanager')->__('Manage Questions/Answers');
        $this->_addButtonLabel = Mage::helper('surveymanager')->__('Add New Questions/Answers');        
        parent::__construct();
    }
    
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/addQuestions');
    }
}
