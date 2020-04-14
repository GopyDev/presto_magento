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
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer groups controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Customer_GroupController extends Mage_Adminhtml_Controller_Action
{
    protected function _initGroup()
    {
        $this->_title($this->__('Customers'))->_title($this->__('Customer Groups'));

        Mage::register('current_group', Mage::getModel('customer/group'));
        $groupId = $this->getRequest()->getParam('id');
        if (!is_null($groupId)) {
            Mage::registry('current_group')->load($groupId);
        }

    }
    /**
     * Customer groups list.
     */
    public function indexAction()
    {
        $this->_title($this->__('Customers'))->_title($this->__('Customer Groups'));

        $this->loadLayout();
        $this->_setActiveMenu('customer/group');
        $this->_addBreadcrumb(Mage::helper('customer')->__('Customers'), Mage::helper('customer')->__('Customers'));
        $this->_addBreadcrumb(Mage::helper('customer')->__('Customer Groups'), Mage::helper('customer')->__('Customer Groups'));
        $this->renderLayout();
    }

    /**
     * Edit or create customer group.
     */
    public function newAction()
    {
        $this->_initGroup();
        $this->loadLayout();
        $this->_setActiveMenu('customer/group');
        $this->_addBreadcrumb(Mage::helper('customer')->__('Customers'), Mage::helper('customer')->__('Customers'));
        $this->_addBreadcrumb(Mage::helper('customer')->__('Customer Groups'), Mage::helper('customer')->__('Customer Groups'), $this->getUrl('*/customer_group'));

        $currentGroup = Mage::registry('current_group');

        if (!is_null($currentGroup->getId())) {
            $this->_addBreadcrumb(Mage::helper('customer')->__('Edit Group'), Mage::helper('customer')->__('Edit Customer Groups'));
        } else {
            $this->_addBreadcrumb(Mage::helper('customer')->__('New Group'), Mage::helper('customer')->__('New Customer Groups'));
        }

        $this->_title($currentGroup->getId() ? $currentGroup->getCode() : $this->__('New Group'));

        $this->getLayout()->getBlock('content')
            ->append($this->getLayout()->createBlock('adminhtml/customer_group_edit', 'group')
                        ->setEditMode((bool)Mage::registry('current_group')->getId()));

        $this->renderLayout();
    }

    /**
     * Edit customer group action. Forward to new action.
     */
    public function editAction()
    {
        $this->_forward('new');
    }

    /**
     * Create or save customer group.
     */
    public function saveAction()
    {
        $customerGroup = Mage::getModel('customer/group');
        $id = $this->getRequest()->getParam('id');
        if (!is_null($id)) {
            $customerGroup->load((int)$id);
        }

        $taxClass = (int)$this->getRequest()->getParam('tax_class');

        if ($taxClass) {
            try {
                $customerGroupCode = (string)$this->getRequest()->getParam('code');
				//Add by xoom
				$customerStreetAddress = (string)$this->getRequest()->getParam('customer_street_address');
				$customerPhoneNumber	= $this->getRequest()->getParam('customer_phone_number');
				$customer_city	= $this->getRequest()->getParam('customer_city');
				$customer_state	= $this->getRequest()->getParam('customer_state');
				$customer_orderlimit 	= intval($this->getRequest()->getParam('customer_orderlimit'));
				$customer_zipcode	= $this->getRequest()->getParam('customer_zipcode');
				$customer_country	= $this->getRequest()->getParam('customer_country');
				$date	= $this->getRequest()->getParam('date');
				$time	= $this->getRequest()->getParam('time');
				$time = $time ;
				$is_shipping = intval($this->getRequest()->getParam('is_shipping'));
				$choose_delivery_address = intval($this->getRequest()->getParam('choose_delivery_address'));
				//Add by xoom
				//Add by xoom
				$data = $this->getRequest()->getPost();
				//Mage::log('Your Data :'.print_r($customerGroup, true), Zend_Log::DEBUG, 'customerGroup.log');
				$customerGroupImage = $_FILES['group_image']['name'];
					if($customerGroupImage){
						try{
							$uploader = new Varien_File_Uploader('group_image');
							$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
							$uploader->setAllowRenameFiles(false);
							$uploader->setFilesDispersion(false);
				
							$path = Mage::getBaseDir('media') . DS . 'image'  ;
							$uploader->save($path, $customerGroupImage);					
							$customerGroupImage = 'image'.DS.$customerGroupImage;
				
						}catch(Exception $e){}
					}
					if($data['group_image']['delete'] == 1){
							$customerGroupImage=''; 
						}
				
				//end
                if (!empty($customerGroupCode)) {
                    $customerGroup->setCode($customerGroupCode);
                }
				if(empty($customerGroupImage)){
					$customerGroupImage = $customerGroup['customer_group_image'];
					}
				//Add by xoom
				$customerGroup->setCustomerGroupImage($customerGroupImage);
				$customerGroup->setCustomerStreetAddress($customerStreetAddress);
				$customerGroup->setCustomerPhoneNumber($customerPhoneNumber);
				$customerGroup->setCustomerCity($customer_city);
				$customerGroup->setCustomerState($customer_state);
				$customerGroup->setCustomerZipcode($customer_zipcode);
				$customerGroup->setCustomerCountry($customer_country);
				$customerGroup->setCustomerOrderlimit($customer_orderlimit);
				$customerGroup->setDate($date);
				$customerGroup->setTime($time);
				$customerGroup->setIsShipping($is_shipping);
				$customerGroup->setChooseDeliveryAddress($choose_delivery_address);
				//end
                $customerGroup->setTaxClassId($taxClass)->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('customer')->__('The customer group has been saved.'));
                $this->getResponse()->setRedirect($this->getUrl('*/customer_group'));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setCustomerGroupData($customerGroup->getData());
                $this->getResponse()->setRedirect($this->getUrl('*/customer_group/edit', array('id' => $id)));
                return;
            }
        } else {
            $this->_forward('new');
        }
    }

    /**
     * Delete customer group action
     */
    public function deleteAction()
    {
        $customerGroup = Mage::getModel('customer/group');
        if ($id = (int)$this->getRequest()->getParam('id')) {
            try {
                $customerGroup->load($id);
                $customerGroup->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('customer')->__('The customer group has been deleted.'));
                $this->getResponse()->setRedirect($this->getUrl('*/customer_group'));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->getResponse()->setRedirect($this->getUrl('*/customer_group/edit', array('id' => $id)));
                return;
            }
        }

        $this->_redirect('*/customer_group');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('customer/group');
    }
}
