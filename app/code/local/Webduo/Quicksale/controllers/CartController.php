<?php
require_once 'Mage/Checkout/controllers/CartController.php';
class Webduo_Quickview_CartController extends Mage_Checkout_CartController {
	
	function _getConnection($type = 'core_read'){
		return Mage::getSingleton('core/resource')->getConnection($type);
	}
	
	private function getCart(){
		$cart = Mage::getModel('checkout/cart');
		$cart->init();
		
		return $cart;
	}
		
	public function addAction(){
		
		if($this->getRequest()->isPost()){
			
			$cart = $this->getCart();
			$params = $this->getRequest()->getParams();
			
			$response = array();
			try {
				if (isset($params['qty'])) {
					$filter = new Zend_Filter_LocalizedToNormalized(
					array('locale' => Mage::app()->getLocale()->getLocaleCode())
					);
					$params['qty'] = $filter->filter($params['qty']);
				}

				$product = Mage::getModel('catalog/product')->load($params['product']);//$this->_initProduct(); 
				$related = $this->getRequest()->getParam('related_product');
				
				if (!$product) {
					$response['status'] = 'ERROR';
					$response['message'] = $this->__('Unable to find Product ID');
				}

				$cart->addProduct($product, $params);
				if (!empty($related)) {
					$productarray = array_unique(explode(',', $related));
					$cart->addProductsByIds($productarray);
				}

				$cart->save();

				$this->_getSession()->setCartWasUpdated(true); 
				
				Mage::dispatchEvent('checkout_cart_add_product_complete',
				array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
				);

				if (!$cart->getQuote()->getHasError()){
					$message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->htmlEscape($product->getName()));
					$response['status'] = 'SUCCESS';
					$response['message'] = $message;
				}
				
				if( in_array($params['product'], $cart->getQuoteProductIds()) ){
					$response['status'] = 'SUCCESS';
					$response['message'] = 'Item added in cart.';
					$response['cart_qty'] = $cart->getItemsQty();
					die(json_encode($response));
					return;
				}
				
				die(json_encode($response));
				return;
			} catch (Mage_Core_Exception $e) {
				$msg = "";
				if ($this->_getSession()->getUseNotice(true)) {
					$msg = $e->getMessage();
				} else {
					$messages = array_unique(explode("\n", $e->getMessage()));
					foreach ($messages as $message) {
						$msg .= $message.'<br/>';
					}
				}

				$response['status'] = 'ERROR';
				$response['message'] = $msg;
				$response['cart_qty'] = $cart->getItemsQty();
				die(json_encode($response));
				return;
			} catch (Exception $e) {
				$response['status'] = 'ERROR';
				$response['message'] = $this->__('Cannot add the item to shopping cart.');
				$response['cart_qty'] = $cart->getItemsQty();
				Mage::logException($e);
				die(json_encode($response));
			return;
			}
			//$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			//echo Mage::helper('core')->jsonEncode($response);
			die(json_encode($response));
			return;
		}
		return false;
	}
}