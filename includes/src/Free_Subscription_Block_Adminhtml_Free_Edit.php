<?php
	
class Free_Subscription_Block_Adminhtml_Free_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "subscription";
				$this->_controller = "adminhtml_free";
				$this->_updateButton("save", "label", Mage::helper("subscription")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("subscription")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("subscription")->__("Save And Continue Edit"),
					"onclick"   => "saveAndContinueEdit()",
					"class"     => "save",
				), -100);



				$this->_formScripts[] = "

							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
						";
		}

		public function getHeaderText()
		{
				if( Mage::registry("free_data") && Mage::registry("free_data")->getId() ){

				    return Mage::helper("subscription")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("free_data")->getId()));

				} 
				else{

				     return Mage::helper("subscription")->__("Add Item");

				}
		}
}