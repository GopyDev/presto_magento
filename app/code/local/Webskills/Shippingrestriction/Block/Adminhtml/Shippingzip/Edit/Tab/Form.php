<?php
class Webskills_Shippingrestriction_Block_Adminhtml_Shippingzip_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("shippingrestriction_form", array("legend"=>Mage::helper("shippingrestriction")->__("Item information")));

				
						$fieldset->addField("zipcode", "text", array(
						"label" => Mage::helper("shippingrestriction")->__("Zip Code"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "zipcode",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getShippingzipData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getShippingzipData());
					Mage::getSingleton("adminhtml/session")->setShippingzipData(null);
				} 
				elseif(Mage::registry("shippingzip_data")) {
				    $form->setValues(Mage::registry("shippingzip_data")->getData());
				}
				return parent::_prepareForm();
		}
}
