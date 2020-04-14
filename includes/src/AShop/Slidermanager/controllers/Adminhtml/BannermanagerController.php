<?php
/**
 * AShop Slider
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the AccessshopThemes.com license that is
 * available through the world-wide-web at this URL:
 * http://www.accessshopThemes.com
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	AShop Slider
 * @package 	AShop_Slidermanager
 * @copyright 	Copyright (c) 2015 Accessshop (http://www.accessshopThemes.com)
 * @license     http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

/**
 * Accessshop Block
 * 
 * @category 	AShop Slider
 * @package 	AShop_Slidermanager
 * @author  	AccessShop Developer
 */
class AShop_Slidermanager_Adminhtml_BannermanagerController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('slidermanager/slider')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Slider Manager'), Mage::helper('adminhtml')->__('Slider Manager'));

		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('slidermanager/bannermanager')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('bannermanager_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('slidermanager/slider');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Slider Manager'), Mage::helper('adminhtml')->__('Slider Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Slider Manager'), Mage::helper('adminhtml')->__('Slider Manager'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('slidermanager/adminhtml_bannermanager_edit'))
				->_addLeft($this->getLayout()->createBlock('slidermanager/adminhtml_bannermanager_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('slidermanager')->__('Slider does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			$model = Mage::getModel('slidermanager/bannermanager');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}
                $model->save();	
				if (isset($data['slider_banner'])) {
                    $bannerIds = array();
                    $bannerOrders = array();

                    $test = Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['slider_banner']);
                    foreach ($test as $key => $value) {
                        $bannerIds[] = $key;
                        $bannerOrders[] = $value['order_banner_slider'];
                    }

                    $unSelecteds = Mage::getResourceModel('slidermanager/slidermanager_collection')
                            ->addFieldToFilter('bannermanager_id', $model->getId());
                    if (count($bannerIds))
                        $unSelecteds->addFieldToFilter('slidermanager_id', array('nin' => $bannerIds));
                    foreach ($unSelecteds as $banner) {
                        $banner->setBannermanagerId(0)
                                ->setOrderBanner(0)->save();
                    }
                    
                    $selectBanner = Mage::getResourceModel('slidermanager/slidermanager_collection')
                            ->addFieldToFilter('slidermanager_id', array('in' => $bannerIds));					
                    $i = -1;
                    foreach ($selectBanner as $banner) {
                        $banner->setBannermanagerId($model->getId())
                                ->setOrderBanner($bannerOrders[++$i])->save();
                                
                    }
                }
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('slidermanager')->__('Slider was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('slidermanager')->__('Unable to find slider to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('slidermanager/bannermanager');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Slider was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $slidermanagerIds = $this->getRequest()->getParam('bannermanager');
        if(!is_array($slidermanagerIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select slider(s)'));
        } else {
            try {
                foreach ($slidermanagerIds as $slidermanagerId) {
                    $slidermanager = Mage::getModel('slidermanager/bannermanager')->load($slidermanagerId);
                    $slidermanager->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($slidermanagerIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $slidermanagerIds = $this->getRequest()->getParam('bannermanager');
        if(!is_array($slidermanagerIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select slider(s)'));
        } else {
            try {
                foreach ($slidermanagerIds as $slidermanagerId) {
                    $slidermanager = Mage::getSingleton('slidermanager/bannermanager')
                        ->load($slidermanagerId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($slidermanagerIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'slidermanager.csv';
        $content    = $this->getLayout()->createBlock('slidermanager/adminhtml_bannermanager_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'slidermanager.xml';
        $content    = $this->getLayout()->createBlock('slidermanager/adminhtml_bannermanager_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }
    protected function customAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('slider.edit.tab.custom')
                ->setCustom($this->getRequest()->getPost('banner', null));
        $this->renderLayout();
    }
    
    public function customgridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('slider.edit.tab.custom')
                ->setCustom($this->getRequest()->getPost('banner', null));
        $this->renderLayout();
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}