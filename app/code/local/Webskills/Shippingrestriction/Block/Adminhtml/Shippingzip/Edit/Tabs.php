<?php
class Webskills_Shippingrestriction_Block_Adminhtml_Shippingzip_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("shippingzip_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("shippingrestriction")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("shippingrestriction")->__("Item Information"),
				"title" => Mage::helper("shippingrestriction")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("shippingrestriction/adminhtml_shippingzip_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
