<?php

class Customer_Track_Adminhtml_TrackController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("track/track")->_addBreadcrumb(Mage::helper("adminhtml")->__("Track  Manager"),Mage::helper("adminhtml")->__("Track Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Track"));
			    $this->_title($this->__("Manager Track"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Track"));
				$this->_title($this->__("Track"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("track/track")->load($id);
				if ($model->getId()) {
					Mage::register("track_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("track/track");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Track Manager"), Mage::helper("adminhtml")->__("Track Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Track Description"), Mage::helper("adminhtml")->__("Track Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("track/adminhtml_track_edit"))->_addLeft($this->getLayout()->createBlock("track/adminhtml_track_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("track")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Track"));
		$this->_title($this->__("Track"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("track/track")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("track_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("track/track");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Track Manager"), Mage::helper("adminhtml")->__("Track Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Track Description"), Mage::helper("adminhtml")->__("Track Description"));


		$this->_addContent($this->getLayout()->createBlock("track/adminhtml_track_edit"))->_addLeft($this->getLayout()->createBlock("track/adminhtml_track_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("track/track")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Track was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setTrackData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setTrackData($this->getRequest()->getPost());
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
						$model = Mage::getModel("track/track");
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
                      $model = Mage::getModel("track/track");
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
			$fileName   = 'track.csv';
			$grid       = $this->getLayout()->createBlock('track/adminhtml_track_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'track.xml';
			$grid       = $this->getLayout()->createBlock('track/adminhtml_track_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
