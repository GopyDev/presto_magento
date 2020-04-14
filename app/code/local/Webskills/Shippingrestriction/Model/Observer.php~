<?php
class Webskills_Shippingrestriction_Model_Observer
{

			public function Shippingzip(Varien_Event_Observer $observer)
			{
				$collection = Mage::getModel('shippingrestriction/shippingzip')->getCollection();
				
				$data = array();
				foreach ($collection as $value)
				{
					$data[] = $value->zipcode;
				}
				$address = Mage::getSingleton('checkout/session')->getQuote()
                                      ->getShippingAddress();	

				if (in_array($address->getData('postcode'),$data))
				{
					throw new Mage_Core_Exception(
					Mage::helper('catalog')->__('Shipping is not available for your location.')
					);die;
				}
				else
				{
				}

			}
		
}
