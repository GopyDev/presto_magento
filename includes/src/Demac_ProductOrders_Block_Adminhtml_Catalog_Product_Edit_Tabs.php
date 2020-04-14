<?php

class Demac_ProductOrders_Block_Adminhtml_Catalog_Product_Edit_Tabs extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
	/**
	 * @var
	 */
	private $parent;

	/**
	 * @return mixed Mage_Core_Block_Abstract
	 */
	protected function _prepareLayout()
	{
		// get all existing tabs
		$this->parent = parent::_prepareLayout();

		// add new tab
		$this->addTab('orders', array(
			'label'   => Mage::helper('catalog')->__('Orders'),
			'content' => $this->getLayout()->createBlock('demac_productorders/adminhtml_catalog_product_edit_tab_orders')->toHtml(),
		));

		return $this->parent;
	}
}