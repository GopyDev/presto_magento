<?php

class Free_Subscription_Adminhtml_FreeController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("subscription/free")->_addBreadcrumb(Mage::helper("adminhtml")->__("Free  Manager"),Mage::helper("adminhtml")->__("Free Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Subscription"));
			    $this->_title($this->__("Manager Free"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Subscription"));
				$this->_title($this->__("Free"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("subscription/free")->load($id);
				if ($model->getId()) {
					Mage::register("free_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("subscription/free");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Free Manager"), Mage::helper("adminhtml")->__("Free Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Free Description"), Mage::helper("adminhtml")->__("Free Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("subscription/adminhtml_free_edit"))->_addLeft($this->getLayout()->createBlock("subscription/adminhtml_free_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("subscription")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Subscription"));
		$this->_title($this->__("Free"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("subscription/free")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("free_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("subscription/free");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Free Manager"), Mage::helper("adminhtml")->__("Free Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Free Description"), Mage::helper("adminhtml")->__("Free Description"));


		$this->_addContent($this->getLayout()->createBlock("subscription/adminhtml_free_edit"))->_addLeft($this->getLayout()->createBlock("subscription/adminhtml_free_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("subscription/free")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Free was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setFreeData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setFreeData($this->getRequest()->getPost());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					return;
					}

				}
				$this->_redirect("*/*/");
		}



		public function deleteAction()
		{
				if( $this->getRequest()->getParam("id") > 0 ) {
					try {
						$model = Mage::getModel("subscription/free");
						$model->setId($this->getRequest()->getParam("id"))->delete();
						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
						$this->_redirect("*/*/");
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					}
				}
				$this->_redirect("*/*/");
		}

		
		public function massRemoveAction()
		{
			try {
				$ids = $this->getRequest()->getPost('ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("subscription/free");
					  $model->setId($id)->delete();
				}
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully removed"));
			}
			catch (Exception $e) {
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			}
			$this->_redirect('*/*/');
		}
			
		/**
		 * Export order grid to CSV format
		 */
		public function exportCsvAction()
		{
			$fileName   = 'free.csv';
			$grid       = $this->getLayout()->createBlock('subscription/adminhtml_free_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'free.xml';
			$grid       = $this->getLayout()->createBlock('subscription/adminhtml_free_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
