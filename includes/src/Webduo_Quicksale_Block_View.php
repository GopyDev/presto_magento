<?php

class Webduo_Quicksale_Block_View extends Mage_Core_Block_Template {
    //protected $_position = 'CONTENT_TOP';
    //protected $_isActive = 1;
    //protected $_banner = array();

	public function _prepareLayout(){
		return parent::_prepareLayout();
    }	
	
	public function getOrdereditems(){
		$orders = Mage::getModel('sales/order')->getCollection()
					->addAttributeToSelect('*')
					->addAttributeToSort('increment_id', 'desc')
					->setPageSize(5);
		
		$ordered_items = array();
		$items_count = 0;
		foreach($orders as $order){
			foreach($order->getAllVisibleItems() as $item){
				if($items_count < 5){
					$ordered_items[] = $item;
				}
				$items_count++;
			}
		}
		return $ordered_items;				
	}
	
	public function getId(){
		return $product_id = $this->getRequest()->getPost('id');
	}
	
	/*public function getProduct(){
		return Mage::getModel('catalog/product')->load($this->getId());
	}
	
	public function getConfigAttributes($product){
		//$product = Mage::getModel('catalog/product')->load(406486);
		//Mage::register('product', $product);
		
		$getJsonConfig = new Mage_Catalog_Block_Product_View_Type_Configurable;
		$product_config = json_decode($getJsonConfig->getJsonConfig($product), true);
		
		$chooseText = $product_config['chooseText'];
		$html = '<div class="product-options" id="product-options-wrapper"><dl>';
		foreach($product_config['attributes'] as $attribute_code => $attributes){
			$html .= "<dt>";
				$html .= "<label class='required'>".$attributes['label']."</label>";
			$html .= "</dt>";
			
			$html .= "<dd class=''>";
				$html .= "<div class='input-box'>";
					$html .= "<select name='super_attribute[".$attribute_code."]' id='attribute".$attribute_code."' class='required-entry super-attribute-select'>";
						$html .= "<option value=''>".$chooseText."</option>";
						foreach($attributes['options'] as $attribute_option => $attribute_options){
							$html .= "<option value='".$attribute_options['id']."' price='".$attribute_options['price']."'>".$attribute_options['label']."</option>";
						}
					$html .= "</select>";
				$html .= "</div>";
			$html .= "</dd>";	
		}
		$html .= '</dl></div>';
		
		unset($getJsonConfig);
		return $html;
	}
	
	public function getJsonConfig($product){
		$getJsonConfig = new Mage_Catalog_Block_Product_View;
		return $getJsonConfig->getJsonConfig($product);
	}
	
	public function productTypeData($product){
		$product = $this->getProduct();//Mage::getModel('catalog/product')->load(65798);
		$typeData = false;
		if($product){
			Mage::register('product', $product);
			if($product->getTypeId() == 'simple'){
				$typeData = $this->getLayout()->createBlock('catalog/product_view_type_configurable')->setTemplate('catalog/product/view/type/default.phtml')->toHtml();
			}elseif($product->getTypeId() == 'configurable'){
				$typeData = $this->getLayout()->createBlock('catalog/product_view_type_configurable')->setTemplate('catalog/product/view/type/default.phtml')->toHtml();
			}	
		}
		return $typeData;
	}
	
	public function getCart(){
		$cart = Mage::getModel('checkout/cart');
		$cart->init();
		
		return $cart;
	}*/
	
}