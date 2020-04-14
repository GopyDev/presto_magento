<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml customer groups edit form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Group_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form for render
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
//        $form = new Varien_Data_Form();
		$form = new Varien_Data_Form(array('enctype' => 'multipart/form-data','method'=>'post')); //Add By Monika
        $customerGroup = Mage::registry('current_group');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('customer')->__('Group Information')));

        $validateClass = sprintf('required-entry validate-length maximum-length-%d',
            Mage_Customer_Model_Group::GROUP_CODE_MAX_LENGTH);
        $name = $fieldset->addField('customer_group_code', 'text',
            array(
                'name'  => 'code',
                'label' => Mage::helper('customer')->__('Group Name'),
                'title' => Mage::helper('customer')->__('Group Name'),
                'note'  => Mage::helper('customer')->__('Maximum length must be less then %s symbols', Mage_Customer_Model_Group::GROUP_CODE_MAX_LENGTH),
                'class' => $validateClass,
                'required' => true,
            )
        );

        if ($customerGroup->getId()==0 && $customerGroup->getCustomerGroupCode() ) {
            $name->setDisabled(true);
        }

        $fieldset->addField('tax_class_id', 'select',
            array(
                'name'  => 'tax_class',
                'label' => Mage::helper('customer')->__('Tax Class'),
                'title' => Mage::helper('customer')->__('Tax Class'),
                'class' => 'required-entry',
                'required' => true,
                'values' => Mage::getSingleton('tax/class_source_customer')->toOptionArray()
            )
        );
		//Add by Monika
		$CustomerGroupImage = $customerGroup->getCustomerGroupImage();
		$customerGroup->setCustomerGroupImage($CustomerGroupImage);
		$fieldset->addField('customer_group_image', 'image',
					array(
						'name'  => 'group_image',
						'label' => Mage::helper('customer')->__('Group Image'),
						'title' => Mage::helper('customer')->__('Group Image'),
						'note'  => Mage::helper('customer')->__('Upload group logo here'),
						//'value' => $CustomerGroupImage,
					//	'required' => false,
					)
				);
		$fieldset->addField('customer_orderlimit', 'text',
			array(
				'name'  => 'customer_orderlimit',
				'label' => Mage::helper('customer')->__('Max number of corp orders'),
				'title' => Mage::helper('customer')->__(''),
				'note'  => Mage::helper('customer')->__('Numeric value only.'),
				'value' => '',
				'required' => false,
			)
		);
		 $fieldset->addField('customer_street_address', 'text',
					array(
						'name'  => 'customer_street_address',
						'label' => Mage::helper('customer')->__('Street Address'),
						'title' => Mage::helper('customer')->__(''),
						//'note'  => Mage::helper('customer')->__('Any notes for this field yoiu can add here'),
						'value' => '',
					//	'required' => false,
					)
				);
		$fieldset->addField('customer_phone_number', 'text',
			array(
				'name'  => 'customer_phone_number',
				'label' => Mage::helper('customer')->__('Phone Number'),
				'title' => Mage::helper('customer')->__(''),
				//'note'  => Mage::helper('customer')->__('Any notes for this field yoiu can add here'),
				'value' => '',
			//	'required' => false,
			)
		);
		
		$fieldset->addField('customer_city', 'text',
			array(
				'name'  => 'customer_city',
				'label' => Mage::helper('customer')->__('City'),
				'title' => Mage::helper('customer')->__(''),
				//'note'  => Mage::helper('customer')->__('Any notes for this field yoiu can add here'),
				'value' => '',
			//	'required' => false,
			)
		);
		
		$country = $fieldset->addField('customer_country', 'select',
			array(
				'name'  => 'customer_country',
				'label' => Mage::helper('customer')->__('Country'),
				'title' => Mage::helper('customer')->__(''),
				//'note'  => Mage::helper('customer')->__('Any notes for this field yoiu can add here'),
				'values'    => Mage::getResourceModel('directory/country_collection')->loadByStore()->toOptionArray(),
				'onchange' => 'getstate(this)'
			//	'required' => false,
			)
		);
		$country_name = trim($customerGroup->getCustomerCountry());
		//Mage::log('Your Data :' . print_r($country_name, true),null,'country_name.log');
		if($country_name != ''):
            $stateCollection = Mage::getModel('directory/region')->getResourceCollection()->addCountryFilter($country_name)->load();
            $state = "";
            foreach ($stateCollection as $_state) {
                $state[]= array('value'=>$_state->getCode(),'label'=>$_state->getDefaultName());
            }
            $fieldset->addField('customer_state', 'select', array(
                'label' => Mage::helper('customer')->__('State/Province'),
                'required' => false,
                'name' => 'customer_state',
                'selected' => 'selected',
                'values' => $state,
            ));
        else:
            $fieldset->addField('customer_state', 'select', array(
                'name'  => 'customer_state',
                'required' => false,
                'label' => Mage::helper('customer')->__('State/Province.'),
                'values' => '--Please Select Country--',
            ));
        endif;

		$fieldset->addField('customer_zipcode', 'text',
			array(
				'name'  => 'customer_zipcode',
				'label' => Mage::helper('customer')->__('Zip/Postal Code'),
				'title' => Mage::helper('customer')->__(''),
				//'note'  => Mage::helper('customer')->__('Any notes for this field yoiu can add here'),
				'value' => '',
			//	'required' => false,
			)
		);
		
		$date = $customerGroup->getDate();
		$customerGroup->setDate($date);
		$fieldset->addField('date', 'select', array(
          'label'     => Mage::helper('customer')->__('Select Days'),
          'class'     => 'required-entry',
          'name'      => 'date',
          'onclick' => "",
          'onchange' => "",
          'value'  => '1',
          'values' => array('-1'=>'Please Select','Monday' => 'Monday','Tuesday' => 'Tuesday', 'Wednesday' => 'Wednesday', 'Thursday' => 'Thursday', 'Friday' => 'Friday', 'saturday' => 'saturday', 'Sunday' => 'Sunday'),
          'disabled' => false,
          'readonly' => false
         ));
		
		$temp = explode('|',$customerGroup->getTime());
		$dummy = preg_replace('/_/', ':', $temp[0], 1); 
		$dummy = preg_replace('/_/', ' - ', $dummy, 1); 
		$dummy = preg_replace('/_/', ':', $dummy, 1); 
		$customerGroup->setTime($dummy);
		 $fieldset->addField('time', 'text', array(
          'label'     => Mage::helper('customer')->__('Select Time'),
          'class'     => 'required-entry',
          'name'      => 'time',
          'onclick' => "",
          'onchange' => "",
          'value'  => '1',
          'values' => '',
		  'note'  => Mage::helper('customer')->__('Please follow this format 01:00pm - 03:00pm'),
        //  'disabled' => false,
          //'readonly' => false
         ));
		 
		 $fieldset->addField('is_shipping', 'checkbox', array(
          'label'     => Mage::helper('customer')->__('Shipping Charge'), 
          'name'      => 'is_shipping',
		  'value'	  => 1,
		  'checked'   => ($customerGroup->getIsShipping() == 1) ? 1 : '',
	      'onclick'   => 'this.value = this.checked ? 1 : 0;',
		  'note'      => Mage::helper('customer')->__('NOTE : 1.checked for shipiing charge. 2.unchecked for FREE shipiing .'),
         ));
		 
		 $fieldset->addField('choose_delivery_address', 'checkbox', array(
          'label'     => Mage::helper('customer')->__('Flexible delivery address'),
          'name'      => 'choose_delivery_address',
          'checked'   => ($customerGroup->getChooseDeliveryAddress() == 1) ? 1 : '',
	      'onclick'   => 'this.value = this.checked ? 1 : 0;',
		  'value'	  => 1,
		  'note'  => Mage::helper('customer')->__('Checked for allow to choose delivery address'),
         ));
		 
		
		 /*
         * Add Ajax to the Country select box html output
         */
         $country->setAfterElementHtml("<script type=\"text/javascript\">
            function getstate(selectElement){
                var reloadurl = '/getAllStates.php?country=' + selectElement.value;
                new Ajax.Request(reloadurl, {
                    method: 'get',
                    onLoading: function (stateform) {
                        $('customer_state').update('Searching...');
                    },
                    onComplete: function(stateform) {
                        $('customer_state').update(stateform.responseText);
                    }
                });
            }
        </script>");
	    
		 
		//End
        if (!is_null($customerGroup->getId())) {
            // If edit add id
            $form->addField('id', 'hidden',
                array(
                    'name'  => 'id',
                    'value' => $customerGroup->getId(),
                )
            );
        }

        if( Mage::getSingleton('adminhtml/session')->getCustomerGroupData() ) {
            $form->addValues(Mage::getSingleton('adminhtml/session')->getCustomerGroupData());
            Mage::getSingleton('adminhtml/session')->setCustomerGroupData(null);
        } else {
            $form->addValues($customerGroup->getData());
        }

        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('*/*/save'));
        $this->setForm($form);
    }
	

}
