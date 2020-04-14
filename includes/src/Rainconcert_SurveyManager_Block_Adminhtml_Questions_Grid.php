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

class Rainconcert_SurveyManager_Block_Adminhtml_Questions_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('surveyQuestionGrid');
        $this->setDefaultSort('created_time');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('surveymanager/surveyquestions')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('survey_qn_id', array(
            'header' => Mage::helper('surveymanager')->__('Question ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'survey_qn_id',
        ));

        $this->addColumn('survey_qn_title', array(
            'header' => Mage::helper('surveymanager')->__('Question'),
            'align' => 'left',
            'index' => 'survey_qn_title',
        ));
        
        $this->addColumn('survey_qn_desc', array(
            'header' => Mage::helper('surveymanager')->__('Question Description'),
            'align' => 'left',
            'index' => 'survey_qn_desc',
        ));

        $this->addColumn('survey_ans_type', array(
            'header' => Mage::helper('surveymanager')->__('Answer Type'),
            'align' => 'left',
            'index' => 'survey_ans_type',
            'type'  => 'options',
            'options' => array(
                1 => Mage::helper('surveymanager')->__('Satisfaction Bubbles'),
                2 => Mage::helper('surveymanager')->__('Excellent-Poor Bubbles'),
                3 => Mage::helper('surveymanager')->__('Text Field'),
                4 => Mage::helper('surveymanager')->__('Text Area'),
                5 => Mage::helper('surveymanager')->__('5 Star Rating'),
                6 => Mage::helper('surveymanager')->__('Yes/No'),
                7 => Mage::helper('surveymanager')->__('Custom Radio'),
                8 => Mage::helper('surveymanager')->__('Check Box'),
                9 => Mage::helper('surveymanager')->__('Select Box'),
            ),
        ));
        
        $this->addColumn('created_time', array(
            'header' => Mage::helper('surveymanager')->__('Created at'),
            'index' => 'created_time',
            'type' => 'datetime',
            'width' => '120px',
            'gmtoffset' => true,
            'default' => ' -- '
        ));

        $this->addColumn('update_time', array(
            'header' => Mage::helper('surveymanager')->__('Updated at'),
            'index' => 'update_time',
            'width' => '120px',
            'type' => 'datetime',
            'gmtoffset' => true,
            'default' => ' -- '
        ));

        $this->addColumn('is_active', array(
            'header' => Mage::helper('surveymanager')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'is_active',
            'type' => 'options',
            'options' => array(
                1 => Mage::helper('surveymanager')->__('Enabled'),
                0 => Mage::helper('surveymanager')->__('Disabled'),
            ),
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('surveymanager')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('surveymanager')->__('Edit'),
                    'url' => array('base' => '*/*/editQuestions'),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));
       
        return parent::_prepareColumns();
    }


    public function getRowUrl($row) {
        return $this->getUrl('*/*/editQuestions', array('id' => $row->getId()));
    }

}
