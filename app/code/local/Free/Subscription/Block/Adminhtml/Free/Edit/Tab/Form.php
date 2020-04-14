<?php
class Free_Subscription_Block_Adminhtml_Free_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("subscription_form", array("legend"=>Mage::helper("subscription")->__("Item information")));

				
						$fieldset->addField("id", "text", array(
						"label" => Mage::helper("subscription")->__("Id"),
						"name" => "id",
						));
					
						/*$fieldset->addField("customer_name", "text", array(
						"label" => Mage::helper("subscription")->__("Cusomer Name"),
						"name" => "customer_name",
						));
					
						$fieldset->addField("customer_emailid", "text", array(
						"label" => Mage::helper("subscription")->__("Customer Email-id"),
						"name" => "customer_emailid",
						));
					
						$fieldset->addField("start_date", "text", array(
						"label" => Mage::helper("subscription")->__("Start Date"),
						"name" => "start_date",
						));
					
						$fieldset->addField("expiry_date", "text", array(
						"label" => Mage::helper("subscription")->__("Expiry Date"),
						"name" => "expiry_date",
						));
					
						$fieldset->addField("duration", "text", array(
						"label" => Mage::helper("subscription")->__("Duration"),
						"name" => "duration",
						));
					
						$fieldset->addField("amount", "text", array(
						"label" => Mage::helper("subscription")->__("Amount"),
						"name" => "amount",
						));*/
					$fieldset->addField('active', 'select', array(
					  'label'     => Mage::helper('subscription')->__('Active'),
					  'class'     => 'required-entry',
					  'name'      => 'active',
					  'values' => array('-1'=>'Please Select','Yes' => 'Yes','No' => 'No'),
					  'disabled' => false,
					  'readonly' => false
					 ));
		 
					/*	$fieldset->addField("active", "select", array(
						"label" => Mage::helper("subscription")->__("Active"),
						"name" => "active",
						));*/
					

				if (Mage::getSingleton("adminhtml/session")->getFreeData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getFreeData());
					Mage::getSingleton("adminhtml/session")->setFreeData(null);
				} 
				elseif(Mage::registry("free_data")) {
				    $form->setValues(Mage::registry("free_data")->getData());
				}
				return parent::_prepareForm();
		}
}
