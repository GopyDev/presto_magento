<?php
require_once 'Mage/Adminhtml/controllers/Catalog/ProductController.php';

class Demac_ProductOrders_Adminhtml_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController
{
	/**
	 * Export product order grid item to CSV format
	 */
	public function exportCsvAction()
	{
		$product = $this->_initProduct();
		$fileName = 'orders_' . $product->getUrlKey() . '_' . Mage::getSingleton('core/date')->date('Y-m-d_H-i-s') . '.csv';
		$content = $this->getLayout()->createBlock('demac_productorders/adminhtml_catalog_product_edit_tab_orders');
		$this->_prepareDownloadResponse($fileName, $content->getCsvFile());
	}

	/**
	 * Get product order gride and serializer block
	 */
	public function productordersAction()
	{
		$this->_initProduct();
		$this->loadLayout();
		$this->getLayout()->getBlock('catalog.product.edit.tab.orders');
		$this->renderLayout();
	}

	/**
	 * Product order grid for AJAX request
	 */
	public function productordersGridAction()
	{
		$this->_initProduct();
		$this->loadLayout();
		$this->getLayout()->getBlock('catalog.product.edit.tab.orders');
		$this->renderLayout();
	}
}