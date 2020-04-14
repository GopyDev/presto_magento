<?php
/************************************************************************
 * 
 * jtechextensions @ J-Tech LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.jtechextensions.com/LICENSE-M1.txt
 *
 * @package    Inventory Report
 * @copyright  Copyright (c) 2012-2013 jtechextensions @ J-Tech LLC. (http://www.jtechextensions.com)
 * @license    http://www.jtechextensions.com/LICENSE-M1.txt
************************************************************************/

class Jtech_Profitlossreport_Block_Manager_Edit_Tabs_Filterform extends Mage_Adminhtml_Block_Widget_Form
{
	protected $_currId = NULL;
    protected $_fieldVisibility = array();
    protected $_fieldOptions = array();
    protected $_reportName = '';
    protected $_reportOptions = array(
    	'store_id'	=> '',
    	'match_period'	=> '',
    	'period'	=> '',
    	'date_from'	=> NULL,
    	'date_to'	=> NULL,
    	'status'	=> '',
    	'shipping_flag'	=> '',
    	'cost_source'	=> '',
    );
    
	protected function _construct()
    {
    	$this->_currId = $this->getRequest()->getParam('id');
    	
        parent::_construct();
        
        if (isset($this->_currId))
        	$this->getReportOptions();
        
        $this->setTemplate('profitlossreport/filterform.phtml');
    }
    
    protected function getReportOptions()
    {
    	$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
		
		$reportTable = $resource->getTableName('jtech_pl_reports');
		$reportAttTable = $resource->getTableName('jtech_pl_reports_attributes');
		
		$select = '
			SELECT name
			FROM ' . $reportTable . '
			WHERE report_id = ' . $this->_currId . '
		';
		
    	$name = $read->fetchRow($select);
    	$this->_reportName = $name['name'];
    	
    	$select = '
    		SELECT att_name,
				value
			FROM ' . $reportAttTable . '
			WHERE att_type_id = 1
			AND report_id = ' . $this->_currId . '
    	';
    	
    	$reportOptions = $read->fetchAll($select);

    	foreach ($reportOptions as $option) {
    		$this->_reportOptions[$option['att_name']] = $option['value'];
    	}
    }
    
	public function setFieldVisibility($fieldId, $visibility)
    {
        $this->_fieldVisibility[$fieldId] = (bool)$visibility;
    }
	
	public function getFieldVisibility($fieldId, $defaultVisibility = true)
    {
        if (!array_key_exists($fieldId, $this->_fieldVisibility)) {
            return $defaultVisibility;
        }
        return $this->_fieldVisibility[$fieldId];
    }
    
    protected function getStoreIdsArray()
    {
    	$storeIds = array();
		$storeIds[''] = 'All';
		
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
		
		$websiteTable = $resource->getTableName('core_website');
		$groupTable = $resource->getTableName('core_store_group');
		$storeTable = $resource->getTableName('core_store');
		
		$select = $read->select()
			->from($websiteTable, array('website_id', 'name'))
			->where('website_id != 0')
			->order('website_id ASC');
    	$websites = $read->fetchAll($select);
			
		if ($websites)
		{
			foreach ($websites as $website)
			{
				$storeIds['w,' . $website['website_id']] = $website['name'];
				
				$select = $read->select()
					->from($groupTable, array('group_id', 'name'))
					->where('group_id != 0 AND website_id = ' . $website['website_id'])
					->order('group_id ASC');
		    	$groups = $read->fetchAll($select);
		    	
		    	foreach ($groups as $group)
		    	{
		    		$storeIds['g,' . $group['group_id']] = '-- ' . $group['name'];
		    		
		    		$select = $read->select()
						->from($storeTable, array('store_id', 'name'))
						->where('store_id != 0 AND group_id = ' . $group['group_id'])
						->order('store_id ASC');
					$stores = $read->fetchAll($select);
			    	
			    	foreach ($stores as $store)
			    	{
			    		$storeIds['s,' . $store['store_id']] = '---- ' . $store['name'];
			    	}
		    	}
			}
			
			return $storeIds;
		} else {
			return $storeIds;
		}	
    }
    
    protected function getStatuses()
    {
    	$statuses = Mage::getModel('sales/order_config')->getStatuses();
        $values = array();
        foreach ($statuses as $code => $label) {
            if (false === strpos($code, 'pending')) {      	
            	$values[$code] = Mage::helper('profitlossreport')->__($label);
            }
        }
        
        return $values;
    }
    
    /*
    protected function getFormValues()
    {
    	$currId = $this->getRequest()->getParam('id');
    	
    	$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
		$reportTable = $resource->getTableName('jtech_pl_reports');
		$reportAttTable = $resource->getTableName('jtech_pl_reports_attributes');
		
		$select = $read->select()
			->from($reportTable, 'name')
			->where('report_id = ' . $currId);
		$attributes = $read->fetchRow($select);
	
		$select = $read->select()
			->from($reportAttTable, array('att_id', 'value'))
			->where('report_id = ' . $currId . ' AND att_id < 7');
		foreach ($read->fetchAll($select) as $attribute)
		{
			$attributes[$attribute['att_id']] = $attribute['value'];			
		}
		
		return $attributes;
    }
    */
	protected function _prepareForm()
	{
		//$formValues = $this->getFormValues();
		
		$form = new Varien_Data_Form();
		$this->setForm($form);
		
		$fieldset = $form->addFieldset('reportsfilter_form_', array('legend' => Mage::helper('profitlossreport')->__('Report Filter')));
		
		$dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
		
		$storeIds =	$this->getStoreIdsArray();	
		
		$fieldset->addField('report_name', 'text', array(
			'name'		=> 'report_name',
			'label'		=> Mage::helper('profitlossreport')->__('Report Name'),
			'required'  => true			
		));
		
		$fieldset->addField('store_ids', 'select', array(
			'name'		=> '1',
			'options'	=> $storeIds,
			'label'		=> Mage::helper('profitlossreport')->__('Show Report For')
		));

        $fieldset->addField('report_type', 'select', array(
            'name'      => '2',
            'options'   => array(
        		'created_at_order'	=> Mage::helper('profitlossreport')->__('Order Created Date'),
        		'updated_at_order'	=> Mage::helper('profitlossreport')->__('Order Updated Date')
        	),
            'label'     => Mage::helper('profitlossreport')->__('Match Period To'),
        	'note'		=> Mage::helper('profitlossreport')->__('Order Updated Date report is real-time, does not need statistics refreshing.')
        ));

        $fieldset->addField('period_type', 'select', array(
            'name' => '3',
            'options' => array(
        		'order'			=> Mage::helper('profitlossreport')->__('By Order'),
                'day'   		=> Mage::helper('profitlossreport')->__('Daily'),
        		'week'   		=> Mage::helper('profitlossreport')->__('Weekly'),
        		'fortnight'   	=> Mage::helper('profitlossreport')->__('Fortnightly'),
                'month' 		=> Mage::helper('profitlossreport')->__('Monthly'),
        		'quarter' 		=> Mage::helper('profitlossreport')->__('Quarterly'),
        		'bi-year' 		=> Mage::helper('profitlossreport')->__('Bi Yearly'),
                'year'  		=> Mage::helper('profitlossreport')->__('Yearly')
            ),
            'label' => Mage::helper('profitlossreport')->__('Period'),
            'title' => Mage::helper('profitlossreport')->__('Period')
        ));

        $fieldset->addField('from', 'date', array(
            'name'      => '4',
            'format'    => $dateFormatIso,
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'label'     => Mage::helper('profitlossreport')->__('From'),
            'title'     => Mage::helper('profitlossreport')->__('From'),
            'required'  => true
        ));

        $fieldset->addField('to', 'date', array(
            'name'      => '5',
            'format'    => $dateFormatIso,
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'label'     => Mage::helper('profitlossreport')->__('To'),
            'title'     => Mage::helper('profitlossreport')->__('To'),
            'required'  => true
        ));

		$statuses = Mage::getModel('sales/order_config')->getStatuses();
        $values = array();
        foreach ($statuses as $code => $label) {
            if (false === strpos($code, 'pending')) {
                $values[] = array(
                    'label' => Mage::helper('profitlossreport')->__($label),
                    'value' => $code
                );
            }
        }

        $fieldset->addField('show_order_statuses', 'select', array(
            'name'      => 'show_order_statuses',
            'label'     => Mage::helper('profitlossreport')->__('Order Status'),
            'options'   => array(
                    '0' => Mage::helper('profitlossreport')->__('Any'),
                    '1' => Mage::helper('profitlossreport')->__('Specified'),
                ),
            'note'      => Mage::helper('profitlossreport')->__('Applies to Any of the Specified Order Statuses'),
        ), 'to');

        $fieldset->addField('order_statuses', 'multiselect', array(
            'name'      => 'order_statuses',
            'values'    => $values,
            'display'   => 'none'
        ), 'show_order_statuses');

        /*
        // define field dependencies
        if ($this->getFieldVisibility('show_order_statuses') && $this->getFieldVisibility('order_statuses')) {
            $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
                ->addFieldMap("show_order_statuses", 'show_order_statuses')
                ->addFieldMap("order_statuses", 'order_statuses')
                ->addFieldDependence('order_statuses', 'show_order_statuses', '1')
            );
        }
        */
        
        $fieldset->addField('shipping_flag', 'select', array(
            'name' 		=> '7',
            'values' 	=> array(
        		'true'   		=> Mage::helper('profitlossreport')->__('Yes'), 
        		'false'			=> Mage::helper('profitlossreport')->__('No')
            ),
            'label' 	=> Mage::helper('profitlossreport')->__('Use Magento shipping charged value'),
            'title' 	=> Mage::helper('profitlossreport')->__('Charged Shipping'),
            'note'		=> Mage::helper('profitlossreport')->__('Select "No" if you plan on using an additional expense for shipping')
        ));
        
        $fieldset->addField('cost_att_opt', 'select', array(
            'name' 		=> '8',
            'values' 	=> array(
        		'order'   		=> Mage::helper('profitlossreport')->__('Order Table'), 
        		'product'			=> Mage::helper('profitlossreport')->__('Product Cost Attribute'),
        		'hybrid'			=> Mage::helper('profitlossreport')->__('Hybrid of Both')
            ),
            'label' 	=> Mage::helper('profitlossreport')->__('Use product cost from'),
            'title' 	=> Mage::helper('profitlossreport')->__('Cost Value Source'),
            'note'		=> Mage::helper('profitlossreport')->__('The hybrid uses the product cost attribute when no value exists for cost in the order tables')
        ));
        
		if ( Mage::getSingleton('adminhtml/session')->getReportData() )
		{
			$form->setValues(Mage::getSingleton('adminhtml/session')->getReportData());
			Mage::getSingleton('adminhtml/session')->setReportData(null);
		} elseif ( Mage::registry('report_data') ) {
			$form->setValues(Mage::registry('report_data')->getData());
		}
		
		return parent::_prepareForm();
	}
	
	protected function _beforeToHtml()
    {
        $result = parent::_beforeToHtml();

        /** @var Varien_Data_Form_Element_Fieldset $fieldset */
        $fieldset = $this->getForm()->getElement('base_fieldset');

        if (is_object($fieldset) && $fieldset instanceof Varien_Data_Form_Element_Fieldset) {
            // apply field visibility
            foreach ($fieldset->getElements() as $field) {
                if (!$this->getFieldVisibility($field->getId())) {
                    $fieldset->removeField($field->getId());
                }
            }
            // apply field options
            foreach ($this->_fieldOptions as $fieldId => $fieldOptions) {
                $field = $fieldset->getElements()->searchById($fieldId);
                /** @var Varien_Object $field */
                if ($field) {
                    foreach ($fieldOptions as $k => $v) {
                        $field->setDataUsingMethod($k, $v);
                    }
                }
            }
        }

        return $result;
    }
}