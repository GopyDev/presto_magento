<?php
class Webskills_Shippingrestriction_ShippingzipController extends Mage_Core_Controller_Front_Action{
		public function IndexAction() {
				  
				  $this->loadLayout();   
				  $this->getLayout()->getBlock("head")->setTitle($this->__("Shippingzip"));
						$breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
				  $breadcrumbs->addCrumb("home", array(
							"label" => $this->__("Home Page"),
							"title" => $this->__("Home Page"),
							"link"  => Mage::getBaseUrl()
					   ));

				  $breadcrumbs->addCrumb("shippingrestriction", array(
							"label" => $this->__("Shippingzip"),
							"title" => $this->__("Shippingzip")
					   ));

				  $this->renderLayout(); 
				  
		}
}