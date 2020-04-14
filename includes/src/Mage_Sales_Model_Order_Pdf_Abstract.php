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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales Order PDF abstract model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Sales_Model_Order_Pdf_Abstract extends Varien_Object
{
    /**
     * Y coordinate
     *
     * @var int
     */
    public $y;

    /**
     * Item renderers with render type key
     *
     * model    => the model name
     * renderer => the renderer model
     *
     * @var array
     */
    protected $_renderers = array();

    /**
     * Predefined constants
     */
    const XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID       = 'sales_pdf/invoice/put_order_id';
    const XML_PATH_SALES_PDF_SHIPMENT_PUT_ORDER_ID      = 'sales_pdf/shipment/put_order_id';
    const XML_PATH_SALES_PDF_CREDITMEMO_PUT_ORDER_ID    = 'sales_pdf/creditmemo/put_order_id';

    /**
     * Zend PDF object
     *
     * @var Zend_Pdf
     */
    protected $_pdf;

    /**
     * Default total model
     *
     * @var string
     */
    protected $_defaultTotalModel = 'sales/order_pdf_total_default';

    /**
     * Retrieve PDF
     *
     * @return Zend_Pdf
     */
    abstract public function getPdf();

    /**
     * Returns the total width in points of the string using the specified font and
     * size.
     *
     * This is not the most efficient way to perform this calculation. I'm
     * concentrating optimization efforts on the upcoming layout manager class.
     * Similar calculations exist inside the layout manager class, but widths are
     * generally calculated only after determining line fragments.
     *
     * @param  string $string
     * @param  Zend_Pdf_Resource_Font $font
     * @param  float $fontSize Font size in points
     * @return float
     */
    public function widthForStringUsingFontSize($string, $font, $fontSize)
    {
        $drawingString = '"libiconv"' == ICONV_IMPL ?
            iconv('UTF-8', 'UTF-16BE//IGNORE', $string) :
            @iconv('UTF-8', 'UTF-16BE', $string);

        $characters = array();
        for ($i = 0; $i < strlen($drawingString); $i++) {
            $characters[] = (ord($drawingString[$i++]) << 8) | ord($drawingString[$i]);
        }
        $glyphs = $font->glyphNumbersForCharacters($characters);
        $widths = $font->widthsForGlyphs($glyphs);
        $stringWidth = (array_sum($widths) / $font->getUnitsPerEm()) * $fontSize;
        return $stringWidth;

    }

    /**
     * Calculate coordinates to draw something in a column aligned to the right
     *
     * @param  string $string
     * @param  int $x
     * @param  int $columnWidth
     * @param  Zend_Pdf_Resource_Font $font
     * @param  int $fontSize
     * @param  int $padding
     * @return int
     */
    public function getAlignRight($string, $x, $columnWidth, Zend_Pdf_Resource_Font $font, $fontSize, $padding = 5)
    {
        $width = $this->widthForStringUsingFontSize($string, $font, $fontSize);
        return $x + $columnWidth - $width - $padding;
    }

    /**
     * Calculate coordinates to draw something in a column aligned to the center
     *
     * @param  string $string
     * @param  int $x
     * @param  int $columnWidth
     * @param  Zend_Pdf_Resource_Font $font
     * @param  int $fontSize
     * @return int
     */
    public function getAlignCenter($string, $x, $columnWidth, Zend_Pdf_Resource_Font $font, $fontSize)
    {
        $width = $this->widthForStringUsingFontSize($string, $font, $fontSize);
        return $x + round(($columnWidth - $width) / 2);
    }

    /**
     * Insert logo to pdf page
     *
     * @param Zend_Pdf_Page $page
     * @param null $store
     */
    protected function insertLogo(&$page, $store = null)
    {
        $this->y = $this->y ? $this->y : 815;
        $image = Mage::getStoreConfig('sales/identity/logo', $store);
        if ($image) {
            $image = Mage::getBaseDir('media') . '/sales/store/logo/' . $image;
            if (is_file($image)) {
                $image       = Zend_Pdf_Image::imageWithPath($image);
                $top         = 830; //top border of the page
                $widthLimit  = 270; //half of the page width
                $heightLimit = 270; //assuming the image is not a "skyscraper"
                $width       = $image->getPixelWidth();
                $height      = $image->getPixelHeight();
				
                //preserving aspect ratio (proportions)
                $ratio = $width / $height;
                if ($ratio > 1 && $width > $widthLimit) {
                    $width  = $widthLimit;
                    $height = $width / $ratio;
                } elseif ($ratio < 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width  = $height * $ratio;
                } elseif ($ratio == 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width  = $widthLimit;
                }

                $y1 = $top - $height;
                $y2 = $top;
                $x1 = 25;
                $x2 = $x1 + $width;
			
                //coordinates after transformation are rounded by Zend
                $page->drawImage($image, $x1, $y1, $x2, $y2);
				
				//$page->drawImage($image, 25, 800, 425, 900);
				
                $this->y = $y1 - 10;
			//$this->y =  760;
            }
        }
    }

    /**
     * Insert address to pdf page
     *
     * @param Zend_Pdf_Page $page
     * @param null $store
     */
    protected function insertAddress(&$page, $store = null)
    {
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $font = $this->_setFontRegular($page, 10);
        $page->setLineWidth(0);
        $this->y = $this->y ? $this->y : 815;
        $top = 815;
        foreach (explode("\n", Mage::getStoreConfig('sales/identity/address', $store)) as $value){
            if ($value !== '') {
                $value = preg_replace('/<br[^>]*>/i', "\n", $value);
                foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
                    $page->drawText(trim(strip_tags($_value)),
                        $this->getAlignRight($_value, 130, 440, $font, 10),
                        $top,
                        'UTF-8');
                    $top -= 10;
                }
            }
        }
        $this->y = ($this->y > $top) ? $top : $this->y;
    }

    /**
     * Format address
     *
     * @param  string $address
     * @return array
     */
    protected function _formatAddress($address)
    {
        $return = array();
        foreach (explode('|', $address) as $str) {
            foreach (Mage::helper('core/string')->str_split($str, 45, true, true) as $part) {
                if (empty($part)) {
                    continue;
                }
                $return[] = $part;
            }
        }
        return $return;
    }

    /**
     * Calculate address height
     *
     * @param  array $address
     * @return int Height
     */
    protected function _calcAddressHeight($address)
    {
        $y = 0;
        foreach ($address as $value){
            if ($value !== '') {
                $text = array();
                foreach (Mage::helper('core/string')->str_split($value, 55, true, true) as $_value) {
                    $text[] = $_value;
                }
                foreach ($text as $part) {
                    $y += 15;
                }
            }
        }
        return $y;
    }

    /**
     * Insert order to pdf page
     *
     * @param Zend_Pdf_Page $page
     * @param Mage_Sales_Model_Order $obj
     * @param bool $putOrderId
     */
    protected function insertOrder(&$page, $obj, $putOrderId = true)
    {
        if ($obj instanceof Mage_Sales_Model_Order) {
            $shipment = null; 
            $order = $obj;
        } elseif ($obj instanceof Mage_Sales_Model_Order_Shipment) {
            $shipment = $obj;
            $order = $shipment->getOrder();
        }

        $this->y = $this->y ? $this->y : 815;
        $top = $this->y;
 // 0 to print black, 1 to print white
	  // $page->setFillColor(new Zend_Pdf_Color_GrayScale(0)); 
     // $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.45));
       $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.45));
        $page->drawRectangle(25, $top, 570, $top - 55);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->setDocHeaderCoordinates(array(25, $top, 570, $top - 55));
//        $this->_setFontRegular($page, 10);
		$this->_setFontBold($page, 12);

        if ($putOrderId) {
            $page->drawText(
                Mage::helper('sales')->__('Order # ') . $order->getRealOrderId(), 35, ($top -= 30), 'UTF-8'
            );
        }
        $page->drawText(
            Mage::helper('sales')->__('Order Date: ') . Mage::helper('core')->formatDate(
                $order->getCreatedAtStoreDate(), 'medium', false
            ),
            35,
            ($top -= 15),
            'UTF-8'
        );
		/*GROUP*/
		$customer_email = $order->getCustomerEmail();
		$customer_id =  $order->getCustomerId();
		$customer = Mage::getModel("customer/customer"); 
		$customer->setWebsiteId(Mage::app()->getWebsite()->getId()); 
		$customer->loadByEmail($customer_email); 
		$customer_code = $customer['group_id'];
		$group_data = Mage::getModel('customer/group')->load($customer_code);
		/**/
	//	$page->drawText(Mage::helper('sales')->__('Group Name : '). $group_data['customer_group_code'], 35, ($top -= 10), 'UTF-8');
		
        $top -= 10;
        $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $top, 570, ($top - 25));
      //  $page->drawRectangle(275, $top, 570, ($top - 25));

        /* Calculate blocks info */

        /* Billing Address */
        $billingAddress = $this->_formatAddress($order->getBillingAddress()->format('pdf'));

        /* Payment */
        $paymentInfo = Mage::helper('payment')->getInfoBlock($order->getPayment())
            ->setIsSecureMode(true)
            ->toPdf();
        $paymentInfo = htmlspecialchars_decode($paymentInfo, ENT_QUOTES);
        $payment = explode('{{pdf_row_separator}}', $paymentInfo);
        foreach ($payment as $key=>$value){
            if (strip_tags(trim($value)) == '') {
                unset($payment[$key]);
            }
        }
        reset($payment);

        /* Shipping Address and Method */
        if (!$order->getIsVirtual()) {
            /* Shipping Address */
            $shippingAddress = $this->_formatAddress($order->getShippingAddress()->format('pdf'));
            $shippingMethod  = $order->getShippingDescription();
			
			//array_push($shippingAddress, strlen($order->getShippingAddress()->getInformation());
			//Mage::log(print_r($shippingAddress,true),null,'shippingAddress.log');
			//$shippingAddress = $shippingAddress . ''.$order->getShippingAddress()->getInformation();
			$dummytext =  $order->getShippingAddress()->getInformation();
        }

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontBold($page, 12);
        //$page->drawText(Mage::helper('sales')->__('Sold to:'), 35, ($top - 15), 'UTF-8');

        if (!$order->getIsVirtual()) {
            $page->drawText(Mage::helper('sales')->__('Delivered to:'), 35, ($top - 15), 'UTF-8');
        }/* else {
            $page->drawText(Mage::helper('sales')->__('Payment:'), 35, ($top - 15), 'UTF-8');
        }*/

        $addressesHeight = $this->_calcAddressHeight($billingAddress);
        if (isset($shippingAddress)) {
            $addressesHeight = max($addressesHeight, $this->_calcAddressHeight($shippingAddress));
        }
		//Mage::log($dummytext,null,'dummytext.log');
	/*	$text1 = explode(' ', $dummytext);
		 $page->drawText($dummytext, 35, $this->y, 'UTF-8'); 
foreach ($shippingAddress as $statushistory) {  
   $page->drawText($statushistory, 35,$this->y - 100, 'UTF-8');
  // $this->y -= 10;
}*/
		//$page->drawText($text1, $top - 50, $this->y, 'UTF-8');
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $page->drawRectangle(25, ($top - 25), 570, $top - 33 - $addressesHeight);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
	
		//$dummy = Mage::helper('core/string')->str_split($dummytext, 65, true, true);
		//$page->drawText(strip_tags(ltrim($dummytext)), $top-30, $this->y, 'UTF-8');
        $this->_setFontRegular($page, 10);
        $this->y = $top - 40;
        $addressesStartY = $this->y;
		
		//Billing address removed
        /*foreach ($billingAddress as $value){
            if ($value !== '') {
                $text = array();
                foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
                    $text[] = $_value;
                }
                foreach ($text as $part) {
                    $page->drawText(strip_tags(ltrim($part)), 35, $this->y, 'UTF-8');
                    $this->y -= 15;
                }
            }
        }*/

        $addressesEndY = 570;

        if (!$order->getIsVirtual()) {
            $this->y = $addressesStartY;
            foreach ($shippingAddress as $value){
                if ($value!=='') {
                    $text = array();
                    foreach (Mage::helper('core/string')->str_split($value, 60, true, true) as $_value) {
                        $text[] = $_value;
                    }
                    foreach ($text as $part) {
                        $page->drawText(strip_tags(ltrim($part)), 45, $this->y, 'UTF-8');
                        $this->y -= 15;
                    }
                }
            }

            $addressesEndY = min($addressesEndY, $this->y);
            $this->y = $addressesEndY;

            $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 375, $this->y-25);
            $page->drawRectangle(275, $this->y, 570, $this->y-25);
			  

            $this->y -= 15;
            $this->_setFontBold($page, 12);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            //$page->drawText(Mage::helper('sales')->__('Payment'), 35, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Delivery:'), 35, $this->y , 'UTF-8');

            $this->y -=10;
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));

            $this->_setFontRegular($page, 10);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

            $paymentLeft = 35;
            $yPayments   = $this->y - 15;
        }
        else {
            $yPayments   = $addressesStartY;
            $paymentLeft = 285;
        }
		
		//Payment method removed
        /*foreach ($payment as $value){
            if (trim($value) != '') {
                //Printing "Payment Method" lines
                $value = preg_replace('/<br[^>]*>/i', "\n", $value);
                foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
                    $page->drawText(strip_tags(trim($_value)), $paymentLeft, $yPayments, 'UTF-8');
                    $yPayments -= 15;
                }
            }
        }*/

        if ($order->getIsVirtual()) {
            // replacement of Shipments-Payments rectangle block 
            $yPayments = min($addressesEndY, $yPayments);
            $page->drawLine(25,  ($top - 25), 25,  $yPayments);
            $page->drawLine(570, ($top - 25), 570, $yPayments);
            $page->drawLine(25,  $yPayments,  570, $yPayments);

            $this->y = $yPayments - 15;
        } else {
            $topMargin    = 15;
            $methodStartY = $this->y;
            $this->y     -= 15;
			
	    $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
       // $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $productTable = 'mg_customer_subsciption';
        $query = "SELECT * FROM " . $productTable . " WHERE customer_id = '".$customer_id."'";
        $result = $connection->query($query);
        $row = $result->fetch();
        
		$query2 = "SELECT * FROM mg_aw_sarp2_profile  WHERE `customer_id` LIKE '$customer_id' AND `status` LIKE 'active'";
		$result2 = $connection->query($query2);
		$row2 = $result2->fetch();
		
		if($row['active'] == 'Yes' || $row2['status'] == 'active'){
			$member = '(PrestoPremier Member)';	
		}
				$Myaccount = Mage::helper('sales')->__('ACCOUNT :  ') .''. $group_data['customer_group_code'] .' '. $member   ;
          //  foreach (Mage::helper('core/string')->str_split($shippingMethod, 45, true, true) as $_value) {
                $page->drawText(strip_tags(trim($Myaccount)), 45, $this->y, 'UTF-8');
                $this->y -= 10;
				
           // }
			
			$page->drawText('', 285, $this->y, 'UTF-8');

            $yShipments = $this->y;
		
           // $totalShippingChargesText = "(" . Mage::helper('sales')->__('Total Delivery Charges') . " "
               // . $order->formatPriceTxt($order->getShippingAmount()) . ")";
				
				/**/
				$order;
				
					$con  = mysql_connect('localhost','prestofr_stage','GiAZ8sc7gpJS');
					$sqlconnect = mysql_select_db('prestofr_stage',$con) or die(mysql_error());
					$sql = "select * from mg_shipping_delivery_window where order_number ='".$order->getRealOrderId()."'";
					$result = mysql_query($sql) or die(mysql_error());
					 
					while($row = mysql_fetch_array($result)){
				
						$a = explode('|',$row['window']);
						$array = sizeof($a);
						if($array == '2'){
						//echo $row['window'];
							
							$a[1];
							$date = str_replace('_','/',$a[1]);
								list($dd,$mm,$yy) = explode("/", $date); 
								$yy = substr($yy,2,4);
								$ddate = $mm.'/'.$dd.'/'.$yy;	
							
							$a[0];
							$time = str_replace('_',':',$a[0]);
							$time1 = str_replace('pm:','pm - ',$time);
							$dtime = $time1;
							
						} else{
							$row['window'];
							$date = str_replace('_','/',$row['window']);
							list($dd,$mm,$yy) = explode("/", $date); 
							$yy = substr($yy,2,4);
							$ddate = $mm.'/'.$dd.'/'.$yy;			
							}
							}
				 $deliveryinfo =   Mage::helper('sales')->__('Delivery Date/Time : ') . " ".''.$ddate.'   ' .$dtime  ;
				
				/**/
				$customerAddressId = $order->getData('customer_id');
				//if ($customerAddressId){
				$address = Mage::getModel('customer/address')->load($customerAddressId);
				 $delivery_inst_text = 'Additional Delivery Instructions : ';
//				 $valuehtml = 'This is Westlake Village main entrance - Independent Living Apt C102 please ask front desk how to get to the apartment in the independent living section';//$order->getShippingAddress()->getInformation();
				 $delivery_text_value = strlen($order->getShippingAddress()->getInformation());
				 
				 $instruction  = $order->getShippingAddress()->getInformation();  
				  $text = substr($instruction, 0, 100); //First chop the string to the given character length
				    //if(substr($text, 0, strrpos($text, ' '))!='') $text = substr($text, 0, strrpos($text, ' '));
				
				 if($delivery_text_value >= 110){
					//if(substr($text, 0, strrpos($text, ' '))!='')
					 //$text = substr($text, 0, strrpos($text, ' '));
					 $text =  substr($instruction, 0 ,  120);
					 $remain = strlen($instruction) - 100;
					 $delivery_text_value2 =   substr($instruction, 100, $remain);
				}else{
						 $delivery_text_value = $instruction;
				}

						//$address->getData('information');
				//}
            $page->drawText($deliveryinfo , 45, $yShipments - $topMargin, 'UTF-8');
			$page->drawText($delivery_inst_text, 45, $yShipments - 35, 'UTF-8');
			//$page->drawText(wordwrap($text, 100, "\n"), 45, $yShipments - 50, 'UTF-8');
			//$page->drawText(wordwrap($delivery_text_value2, 100, "\n"), 45, $yShipments - 60, 'UTF-8');
			//$page->drawText(wordwrap($delivery_text_value, 100, "\n"), 45, $yShipments - 70, 'UTF-8');
		//	$shipaddressesHeight = $this->_calcAddressHeight($instruction);
       /* if (isset($instruction)) {
            $addressesvalHeight = max($shipaddressesHeight, $this->_calcAddressHeight($instruction));
        }			
			 $page->drawRectangle(45, ($top - 25), 570, $top - 50 - $addressesvalHeight);*/
			//$page->drawText($addressesvalHeight, 45, $yShipments - 50, 'UTF-8');
			/*$textChunk = wordwrap($instruction, 100, "<br />");
foreach(explode("<br />", $textChunk) as $textLine){
  if ($textLine!=='') {
    $page->drawText(strip_tags(ltrim($textLine)), 45, $yShipments - 50, 'UTF-8');
   // $line -=14;
  }
}*/
			
			//$page->drawText($delivery_text_value2, 45, $yShipments - 65, 'UTF-8');
			
			
            $yShipments -= $topMargin + 20;

            $tracks = array();
            if ($shipment) {
                $tracks = $shipment->getAllTracks();
            }
            if (count($tracks)) {
                $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
                $page->setLineWidth(0.5);
                $page->drawRectangle(285, $yShipments, 510, $yShipments - 10);
                $page->drawLine(400, $yShipments, 400, $yShipments - 10);
                //$page->drawLine(510, $yShipments, 510, $yShipments - 10);

                $this->_setFontRegular($page, 9);
                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
                //$page->drawText(Mage::helper('sales')->__('Carrier'), 290, $yShipments - 7 , 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('Title'), 290, $yShipments - 7, 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('Number'), 410, $yShipments - 7, 'UTF-8');

                $yShipments -= 20;
                $this->_setFontRegular($page, 8);
                foreach ($tracks as $track) {

                    $CarrierCode = $track->getCarrierCode();
                    if ($CarrierCode != 'custom') {
                        $carrier = Mage::getSingleton('shipping/config')->getCarrierInstance($CarrierCode);
                        $carrierTitle = $carrier->getConfigData('title');
                    } else {
                        $carrierTitle = Mage::helper('sales')->__('Custom Value');
                    }

                    //$truncatedCarrierTitle = substr($carrierTitle, 0, 35) . (strlen($carrierTitle) > 35 ? '...' : '');
                    $maxTitleLen = 45;
                    $endOfTitle = strlen($track->getTitle()) > $maxTitleLen ? '...' : '';
                    $truncatedTitle = substr($track->getTitle(), 0, $maxTitleLen) . $endOfTitle;
                    //$page->drawText($truncatedCarrierTitle, 285, $yShipments , 'UTF-8');
                    $page->drawText($truncatedTitle, 292, $yShipments , 'UTF-8');
                    $page->drawText($track->getNumber(), 410, $yShipments , 'UTF-8');
                    $yShipments -= $topMargin - 5;
                }
            } else {
                $yShipments -= $topMargin - 5;
            }

            $currentY = min($yPayments, $yShipments);

            // replacement of Shipments-Payments rectangle block
            $page->drawLine(25,  $methodStartY, 25,  $currentY); //left
            $page->drawLine(25,  $currentY,     570, $currentY); //bottom
            $page->drawLine(570, $currentY,     570, $methodStartY); //right

            $this->y = $currentY;
            $this->y -= 15;
        }
    }

	
	
	
    /**
     * Insert title and number for concrete document type
     *
     * @param  Zend_Pdf_Page $page
     * @param  string $text
     * @return void
     */
    public function insertDocumentNumber(Zend_Pdf_Page $page, $text)
    {
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
       // $this->_setFontRegular($page, 10);
		$this->_setFontBold($page, 12);

        $docHeader = $this->getDocHeaderCoordinates();
        $page->drawText($text, 35, $docHeader[1] - 15, 'UTF-8');
    }

    /**
     * Sort totals list
     *
     * @param  array $a
     * @param  array $b
     * @return int
     */
    protected function _sortTotalsList($a, $b) {
        if (!isset($a['sort_order']) || !isset($b['sort_order'])) {
            return 0;
        }

        if ($a['sort_order'] == $b['sort_order']) {
            return 0;
        }

        return ($a['sort_order'] > $b['sort_order']) ? 1 : -1;
    }

    /**
     * Return total list
     *
     * @param  Mage_Sales_Model_Abstract $source 
     * @return array
     */
    protected function _getTotalsList($source)
    {
        $totals = Mage::getConfig()->getNode('global/pdf/totals')->asArray();
        usort($totals, array($this, '_sortTotalsList'));
        $totalModels = array();
		
        foreach ($totals as $index => $totalInfo) {
			
            if (!empty($totalInfo['model'])) {
                $totalModel = Mage::getModel($totalInfo['model']);
				
                if ($totalModel instanceof Mage_Sales_Model_Order_Pdf_Total_Default) {
					
                    $totalInfo['model'] = $totalModel;
                } else {
                    Mage::throwException(
                        Mage::helper('sales')->__('PDF total model should extend Mage_Sales_Model_Order_Pdf_Total_Default')
                    );
                }
            } else {
                $totalModel = Mage::getModel($this->_defaultTotalModel);
            }
            $totalModel->setData($totalInfo);
            $totalModels[] = $totalModel;
        }

        return $totalModels;
    }

    /**
     * Insert totals to pdf page
     *
     * @param  Zend_Pdf_Page $page
     * @param  Mage_Sales_Model_Abstract $source
     * @return Zend_Pdf_Page
     */
    protected function insertTotals($page, $source){
        $order = $source->getOrder();
        $totals = $this->_getTotalsList($source);
        $lineBlock = array(
            'lines'  => array(),
            'height' => 15
        );

	  $quote_id = $order->getQuoteId();
	  $resource = Mage::getSingleton('core/resource');
	  $readConnection = $resource->getConnection('core_read');
	  $table = $resource->getTableName('shipping_delivery_window');
	  $shipcharge = $readConnection->fetchCol('SELECT shipcharge  FROM ' . $table .' WHERE quote_id LIKE '.$quote_id.'');
	  //$discount_amount = $readConnection->fetchCol('SELECT discount_amount  FROM ' . $table .' WHERE quote_id LIKE '.$quote_id.'');
	  $discountammount1 = number_format($order['discount_amount'],2);
	  
		$pos = strpos($discountammount1,'-');
				if ($pos === false) {
						$discount_ammount =  '-$'.$discountammount1;
				}
				else{
						$discount_ammount =  str_replace('-','-$',$discountammount1);
				}
		
		$flag = 0; 
        foreach ($totals as $total) {
            $total->setOrder($order)
                ->setSource($source);
            if ($total->canDisplay()) {
                $total->setFontSize(10);
                foreach ($total->getTotalsForDisplay() as $totalData) {
					
					if($totalData['label'] == 'Grand Total (Incl. Tax):'){
						$lineBlock['lines'][] = array(  
						  array(
                            'text'      => $totalData['label'],
                            'feed'      => 475,
                            'align'     => 'right',
                            'font_size' => $totalData['font_size'],
                            'font'      => 'bold'
                        ),
						array(
                            'text'      => '$'.number_format($order->getData('base_total_invoiced'),2),
                            'feed'      => 565,
                            'align'     => 'right',
                            'font_size' => $totalData['font_size'],
                            'font'      => 'bold'
                        ),
						);
						
					}else{
					///
					
					if($flag == 0 && $totalData['label'] == 'Delivery & Handling:'){
					
						if($shipcharge[0] != ''){
							$lineBlock['lines'][] = array(
								  array(
								'text'      => 'Additional Delivery Fee for Overnight Processing',
								'feed'      => 475,
								'align'     => 'right',
								'font_size' => $totalData['font_size'],
								'font'      => 'bold'
							),
							array(
								'text'      => '$'.number_format($shipcharge[0],2),
								'feed'      => 565,
								'align'     => 'right',
								'font_size' => $totalData['font_size'],
								'font'      => 'bold'
							),
							);
						} 
						
					/*   if($discount_amount[0] != 0){
							$lineBlock['lines'][] = array(
								  array(
								'text'      => 'Discount (Delivery Discount)',
								'feed'      => 475,
								'align'     => 'right',
								'font_size' => $totalData['font_size'],
								'font'      => 'bold'
							),
							array(
								'text'      => $discount_ammount,
								'feed'      => 565,
								'align'     => 'right',
								'font_size' => $totalData['font_size'],
								'font'      => 'bold'
							),
							);
						}*/
					   $flag = 1;
					}
					 ///
					 if($totalData['label'] == 'Grand Total (Excl. Tax):'){
						 /* 	$lineBlock['lines'][] = array(
                     array(
                            'text'      => $totalData['label'],
                            'feed'      => 475,
                            'align'     => 'right',
                            'font_size' => $totalData['font_size'],
                            'font'      => 'bold'
                        ),
                        array(
                            'text'      => $totalData['amount'],
                            'feed'      => 565,
                            'align'     => 'right',
                            'font_size' => $totalData['font_size'],
                            'font'      => 'bold'
                        ),
                    );*/
					 }else{
						 if($order->getCouponCode()){
							 $code = '('.$order->getCouponCode().')';
							 }
						if($totalData['label'] == 'Discount:'){
						$lineBlock['lines'][] = array(  
						  array(
                            'text'      => 'Discount '.$code,
                            'feed'      => 475,
                            'align'     => 'right',
                            'font_size' => $totalData['font_size'],
                            'font'      => 'bold'
                        ),
						array(
                            'text'      => $discount_ammount,
                            'feed'      => 565,
                            'align'     => 'right',
                            'font_size' => $totalData['font_size'],
                            'font'      => 'bold'
                        ),
						);
					}else{ 
					$lineBlock['lines'][] = array(
                      array(
                            'text'      => $totalData['label'],
                            'feed'      => 475,
                            'align'     => 'right',
                            'font_size' => $totalData['font_size'],
                            'font'      => 'bold'
                        ),
                        array(
                            'text'      => $totalData['amount'],
                            'feed'      => 565,
                            'align'     => 'right',
                            'font_size' => $totalData['font_size'],
                            'font'      => 'bold'
                        ),
                    );
					}
					 }
					}
                }
				
            }
        }

        $this->y -= 20;
        $page = $this->drawLineBlocks($page, array($lineBlock));
		
        return $page;
    }
	
	protected function insertBottomLine($page, $source){
        $order = $source->getOrder();

		$lineBlock = array(
            'lines'  => array(),
            'height' => 15
        );
        $lineBlock['lines'][] = array(
									array(
										'text'      => '---------------------------------------------------------------------------------',
										'feed'      => 100,
										'align'     => 'right',
										'font_size' => 10,
										'font'      => 'bold'
									),
								);
        $this->y -= 30;
        $page = $this->drawLineBlocks($page, array($lineBlock));
		
        return $page;
    }
	
	protected function insertBottomText($page, $source, $label, $cord=10){
        
		$order = $source->getOrder();

		$lineBlock = array(
            'lines'  => array(),
            'height' => 15
        );
		
		$lineBlock['lines'][] = array(
									array(
										'text'      => $label,
										'feed'      => 565,
										'align'     => 'right',
										'font_size' => 10,
										'font'      => 'bold'
									),
								);	
		$this->y -= $cord ;
        $page = $this->drawLineBlocks($page, array($lineBlock));
		return $page;
    }
		
	
    /**
     * Parse item description
     *
     * @param  Varien_Object $item
     * @return array
     */
    protected function _parseItemDescription($item)
    {
        $matches = array();
        $description = $item->getDescription();
        if (preg_match_all('/<li.*?>(.*?)<\/li>/i', $description, $matches)) {
            return $matches[1];
        }

        return array($description);
    }

    /**
     * Before getPdf processing
     */
    protected function _beforeGetPdf() {
        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);
    }

    /**
     * After getPdf processing
     */
    protected function _afterGetPdf() {
        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(true);
    }

    /**
     * Format option value process
     *
     * @param  array|string $value
     * @param  Mage_Sales_Model_Order $order
     * @return string
     */
    protected function _formatOptionValue($value, $order)
    {
        $resultValue = '';
        if (is_array($value)) {
            if (isset($value['qty'])) {
                $resultValue .= sprintf('%d', $value['qty']) . ' x ';
            }

            $resultValue .= $value['title'];

            if (isset($value['price'])) {
                $resultValue .= " " . $order->formatPrice($value['price']);
            }
            return  $resultValue;
        } else {
            return $value;
        }
    }

    /**
     * Initialize renderer process
     *
     * @param string $type
     */
    protected function _initRenderer($type)
    {
        $node = Mage::getConfig()->getNode('global/pdf/' . $type);
        foreach ($node->children() as $renderer) {
            $this->_renderers[$renderer->getName()] = array(
                'model'     => (string)$renderer,
                'renderer'  => null
            );
        }
    }

    /**
     * Retrieve renderer model
     *
     * @param  string $type
     * @throws Mage_Core_Exception
     * @return Mage_Sales_Model_Order_Pdf_Items_Abstract
     */
    protected function _getRenderer($type)
    {
        if (!isset($this->_renderers[$type])) {
            $type = 'default';
        }

        if (!isset($this->_renderers[$type])) {
            Mage::throwException(Mage::helper('sales')->__('Invalid renderer model'));
        }

        if (is_null($this->_renderers[$type]['renderer'])) {
            $this->_renderers[$type]['renderer'] = Mage::getSingleton($this->_renderers[$type]['model']);
        }

        return $this->_renderers[$type]['renderer'];
    }

    /**
     * Public method of protected @see _getRenderer()
     *
     * Retrieve renderer model
     *
     * @param  string $type
     * @return Mage_Sales_Model_Order_Pdf_Items_Abstract
     */
    public function getRenderer($type)
    {
        return $this->_getRenderer($type);
    }

    /**
     * Draw Item process
     *
     * @param  Varien_Object $item
     * @param  Zend_Pdf_Page $page
     * @param  Mage_Sales_Model_Order $order
     * @return Zend_Pdf_Page
     */
    protected function _drawItem(Varien_Object $item, Zend_Pdf_Page $page, Mage_Sales_Model_Order $order)
    {
        $type = $item->getOrderItem()->getProductType();
        $renderer = $this->_getRenderer($type);
        $renderer->setOrder($order);
        $renderer->setItem($item);
//echo '<pre>';print_r($item);exit;
        $renderer->setPdf($this);
        $renderer->setPage($page);
        $renderer->setRenderedModel($this);

        $renderer->draw();

        return $renderer->getPage();
    }

    /**
     * Set font as regular
     *
     * @param  Zend_Pdf_Page $object
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontRegular($object, $size = 7)
    {
        $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertine_Re-4.4.1.ttf');
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Set font as bold
     *
     * @param  Zend_Pdf_Page $object
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontBold($object, $size = 7)
    {
        $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertine_Bd-2.8.1.ttf');
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Set font as italic
     *
     * @param  Zend_Pdf_Page $object
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontItalic($object, $size = 7)
    {
        $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertine_It-2.8.2.ttf');
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Set PDF object
     *
     * @param  Zend_Pdf $pdf
     * @return Mage_Sales_Model_Order_Pdf_Abstract
     */
    protected function _setPdf(Zend_Pdf $pdf)
    {
        $this->_pdf = $pdf;
        return $this;
    }

    /**
     * Retrieve PDF object
     *
     * @throws Mage_Core_Exception
     * @return Zend_Pdf
     */
    protected function _getPdf()
    {
        if (!$this->_pdf instanceof Zend_Pdf) {
            Mage::throwException(Mage::helper('sales')->__('Please define PDF object before using.'));
        }

        return $this->_pdf;
    }

    /**
     * Create new page and assign to PDF object
     *
     * @param  array $settings
     * @return Zend_Pdf_Page
     */
    public function newPage(array $settings = array())
    {
        $pageSize = !empty($settings['page_size']) ? $settings['page_size'] : Zend_Pdf_Page::SIZE_A4;
        $page = $this->_getPdf()->newPage($pageSize);
        $this->_getPdf()->pages[] = $page;
        $this->y = 800;

        return $page;
    }

    /**
     * Draw lines
     *
     * draw items array format:
     * lines        array;array of line blocks (required)
     * shift        int; full line height (optional)
     * height       int;line spacing (default 10)
     *
     * line block has line columns array
     *
     * column array format
     * text         string|array; draw text (required)
     * feed         int; x position (required)
     * font         string; font style, optional: bold, italic, regular
     * font_file    string; path to font file (optional for use your custom font)
     * font_size    int; font size (default 7)
     * align        string; text align (also see feed parametr), optional left, right
     * height       int;line spacing (default 10)
     *
     * @param  Zend_Pdf_Page $page
     * @param  array $draw
     * @param  array $pageSettings
     * @throws Mage_Core_Exception
     * @return Zend_Pdf_Page
     */
    public function drawLineBlocks(Zend_Pdf_Page $page, array $draw, array $pageSettings = array())
    {
        foreach ($draw as $itemsProp) {
            if (!isset($itemsProp['lines']) || !is_array($itemsProp['lines'])) {
                Mage::throwException(Mage::helper('sales')->__('Invalid draw line data. Please define "lines" array.'));
            }
            $lines  = $itemsProp['lines'];
            $height = isset($itemsProp['height']) ? $itemsProp['height'] : 10;

            if (empty($itemsProp['shift'])) {
                $shift = 0;
                foreach ($lines as $line) {
                    $maxHeight = 0;
                    foreach ($line as $column) {
                        $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                        if (!is_array($column['text'])) {
                            $column['text'] = array($column['text']);
                        }
                        $top = 0;
                        foreach ($column['text'] as $part) {
                            $top += $lineSpacing;
                        }

                        $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                    }
                    $shift += $maxHeight;
                }
                $itemsProp['shift'] = $shift;
            }

            if ($this->y - $itemsProp['shift'] < 15) {
                $page = $this->newPage($pageSettings);
            }

            foreach ($lines as $line) {
                $maxHeight = 0;
                foreach ($line as $column) {
                    $fontSize = empty($column['font_size']) ? 10 : $column['font_size'];
                    if (!empty($column['font_file'])) {
                        $font = Zend_Pdf_Font::fontWithPath($column['font_file']);
                        $page->setFont($font, $fontSize);
                    } else {
                        $fontStyle = empty($column['font']) ? 'regular' : $column['font'];
                        switch ($fontStyle) {
                            case 'bold':
                                $font = $this->_setFontBold($page, $fontSize);
                                break;
                            case 'italic':
                                $font = $this->_setFontItalic($page, $fontSize);
                                break;
                            default:
                                $font = $this->_setFontRegular($page, $fontSize);
                                break;
                        }
                    }

                    if (!is_array($column['text'])) {
                        $column['text'] = array($column['text']);
                    }

                    $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                    $top = 0;
                    foreach ($column['text'] as $part) {
                        if ($this->y - $lineSpacing < 15) {
                            $page = $this->newPage($pageSettings);
                        }

                        $feed = $column['feed'];
                        $textAlign = empty($column['align']) ? 'left' : $column['align'];
                        $width = empty($column['width']) ? 0 : $column['width'];
                        switch ($textAlign) {
                            case 'right':
                                if ($width) {
                                    $feed = $this->getAlignRight($part, $feed, $width, $font, $fontSize);
                                }
                                else {
                                    $feed = $feed - $this->widthForStringUsingFontSize($part, $font, $fontSize);
                                }
                                break;
                            case 'center':
                                if ($width) {
                                    $feed = $this->getAlignCenter($part, $feed, $width, $font, $fontSize);
                                }
                                break;
                        }
                        $page->drawText($part, $feed, $this->y-$top, 'UTF-8');
                        $top += $lineSpacing;
                    }

                    $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                }
                $this->y -= $maxHeight;
            }
        }

        return $page;
    }
}
