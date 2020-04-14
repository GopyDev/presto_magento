<?php

class Ajzele_CustomProduct_Model_Observer
{    
    public function hookIntoCatalogProductNewAction($observer)
    {
        $product = $observer->getEvent()->getProduct();
        //echo 'Inside hookIntoCatalogProductNewAction observer...'; exit;
        //Implement the "catalog_product_new_action" hook
        return $this;    	
    }
    
    public function hookIntoCatalogProductEditAction($observer)
    {
        $product = $observer->getEvent()->getProduct();
        //echo 'Inside hookIntoCatalogProductEditAction observer...'; exit;
        //Implement the "catalog_product_edit_action" hook
        return $this;    	
    }    
    
    public function hookIntoCatalogProductPrepareSave($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $event = $observer->getEvent();
        //echo 'Inside hookIntoCatalogProductPrepareSave observer...'; exit;
        //Implement the "catalog_product_prepare_save" hook
        return $this;    	
    }

    public function hookIntoSalesOrderItemSaveAfter($observer)
    {
        //$event = $observer->getEvent();
        //echo 'Inside hookIntoSalesOrderItemSaveAfter observer...'; exit;
        //Implement the "sales_order_item_save_after" hook
        return $this;    	
    }

    public function hookIntoSalesOrderSaveBefore($observer)
    {
        //$event = $observer->getEvent();
        //echo 'Inside hookIntoSalesOrderSaveBefore observer...'; exit;
        //Implement the "sales_order_save_before" hook
        return $this;    	
    }     
    
    public function hookIntoSalesOrderSaveAfter($observer)
    {
        $product = $observer->getEvent()->getProduct();
        //echo 'Inside hookIntoSalesOrderSaveAfter observer...'; exit;
        //Implement the "sales_order_save_after" hook
        return $this;    	
    } 

    public function hookIntoCatalogProductDeleteBefore($observer)
    {
        $product = $observer->getEvent()->getProduct();
        //echo 'Inside hookIntoCatalogProductDeleteBefore observer...'; exit;
        //Implement the "catalog_product_delete_before" hook
        return $this;    	
    }    
    
    public function hookIntoCatalogruleBeforeApply($observer)
    {
        //$event = $observer->getEvent();
        //echo 'Inside hookIntoCatalogruleBeforeApply observer...'; exit;
        //Implement the "catalogrule_before_apply" hook
        return $this;    	
    }  

    public function hookIntoCatalogruleAfterApply($observer)
    {
        //$event = $observer->getEvent();
        //echo 'Inside hookIntoCatalogruleAfterApply observer...'; exit;
        //Implement the "catalogrule_after_apply" hook
        return $this;    	
    }    
    
    public function hookIntoCatalogProductSaveAfter($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $event = $observer->getEvent();
        //echo 'Inside hookIntoCatalogProductSaveAfter observer...'; exit;
        //Implement the "catalog_product_save_after" hook
        return $this;    	
    }   
	
    public function hookIntoCatalogProductStatusUpdate($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $event = $observer->getEvent();
        //echo 'Inside hookIntoCatalogProductStatusUpdate observer...'; exit;
        //Implement the "catalog_product_status_update" hook
        return $this;    	
    }

    public function hookIntoCatalogEntityAttributeSaveAfter($observer)
    {
        //$event = $observer->getEvent();
        
        //Implement the "catalog_entity_attribute_save_after" hook
        return $this;    	
    }
    
    public function hookIntoCatalogProductDeleteAfterDone($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $event = $observer->getEvent();
        //echo 'Inside hookIntoCatalogProductDeleteAfterDone observer...'; exit;
        //Implement the "catalog_product_delete_after_done" hook
        return $this;    	
    }
    
    public function hookIntoCustomerLogin($observer)
    {
        $event = $observer->getEvent();
        //echo 'Inside hookIntoCustomerLogin observer...'; exit;
        //Implement the "customer_login" hook
        return $this;    	
    }       

    public function hookIntoCustomerLogout($observer)
    {
        $event = $observer->getEvent();
        //echo 'Inside hookIntoCustomerLogout observer...'; exit;
        //Implement the "customer_logout" hook
        return $this;    	
    }

    public function hookIntoSalesQuoteSaveAfter($observer)
    {
        $event = $observer->getEvent();
        //echo 'Inside hookIntoSalesQuoteSaveAfter observer...'; exit;
        //Implement the "sales_quote_save_after" hook
        return $this;    	
    }

    public function hookIntoCatalogProductCollectionLoadAfter($observer)
    {
        $event = $observer->getEvent();
        //echo 'Inside hookIntoCatalogProductCollectionLoadAfter observer...'; exit;
        //Implement the "catalog_product_collection_load_after" hook
        return $this;    	
    }
	public function checkFreeshipping(Varien_Event_Observer $observer){
	//	Mage::log($customer_id,null,'test5.log');
		  $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
		  $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
	//Mage::log($customer_id,null,'test5.log');
		  $productTable = 'mg_customer_subsciption';
		  $query = "SELECT * FROM " . $productTable . " WHERE customer_id = '".$customer_id."'";
		  $result = $connection->query($query);
		  $row = $result->fetch();
		  if($row['active'] == 'Yes'){
			 $quote      = Mage::getSingleton('checkout/cart')->getQuote();
				//if(!$quote->getShippingAddress()->getShippingMethod())
				//{
					$quote->getShippingAddress()
							->setShippingMethod('freeshipping_freeshipping')
							->save();
					return true;
			//	}
		  }
		 $event = $observer->getEvent();
		 return $this;    
		
		}
	public function checkAddOnProduct(Varien_Event_Observer $observer)
			{
				Mage::log('check',null,'cehck.log') ;
			$action = $observer->getControllerAction();
			$productId = (int) Mage::app()->getRequest()->getParam('product');
			 if ($productId){
				$product = Mage::getModel('catalog/product')
					->setStoreId(Mage::app()->getStore()->getId())
					->load($productId);
			}
			if($product->getTypeId() != "customproduct")
			{
				$url = $action->getRequest()->getParam('uenc');
				$refererUrl = Mage::helper('core')->urlDecode($url);
				$action->getResponse()->setRedirect($refererUrl)->sendResponse();
				return;
			}
			$session = Mage::getSingleton('checkout/session');
			$output = false;
			
			foreach ($session->getQuote()->getAllItems() as $item) {
				$abc =  Mage::getModel('catalog/product')->load($item->getProductId());
				if($abc->getTypeId() != "customproduct")
				{
					
					$output = true;
				}
			}	
			if (!$product->getTypeId() == "customproduct"){
				return;
			}
			
				if ($output === false)
				{
					$session->addError('You can not Buy product with other product.');
					//$productUrl = Mage::helper('catalog/product')->getProductUrl($product);
					$url = $action->getRequest()->getParam('uenc');
					$refererUrl = Mage::helper('core')->urlDecode($url);
					$action->getResponse()->setRedirect($refererUrl)->sendResponse();
					//$action->getResponse()->setRedirect($productUrl)->sendResponse();
					 
					exit;
				} 
				else
				{  /*if($output === true && $product->getTypeId() == "customproduct"){
						$session->addError('You cannot addtocart this product');
						$url = $action->getRequest()->getParam('uenc');
					$refererUrl = Mage::helper('core')->urlDecode($url);
					$action->getResponse()->setRedirect($refererUrl)->sendResponse();
						exit;
					}*/
					$url = $action->getRequest()->getParam('uenc');
					$refererUrl = Mage::helper('core')->urlDecode($url);
					$action->getResponse()->setRedirect($refererUrl)->sendResponse();
					return;
				}
			}
    
}