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
 * Adminhtml sales orders grid
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Invoice_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_invoice_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
       return 'sales/order_invoice_grid_collection';
	  // return 'sales/order_invoice_grid_collection';
		
    }

    protected function _prepareCollection()
    {
	
	

		
		//$collection->addAttributeToSelect(array('name', 'url_key', 'type_id')); 
		//$collection = Mage::getResourceModel($this->_getCollectionClass());
		/*
					$_order = $this->getOrder() ;
	
					  // echo $_order;
						 
						 
						$resource = Mage::getSingleton('core/resource');
					$readConnection = $resource->getConnection('core_read');
					$table = $resource->getTableName('mg_shipping_delivery_window');
				//	$query = "select * from mg_shipping_delivery_window where order_number ='".$_order->getRealOrderId()."'";
				$query = "select * from mg_shipping_delivery_window";
					 $result = $readConnection->fetchAll($query);
					 
					 $collection=$result;
				//	 $collection->addAttributeToSelect(array('window')); 
					 
					 
					 
					 $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
 
$select = $connection->select()->from('mg_shipping_delivery_window', array('window')); // select * from tablename or use array('id','title') selected values
  //  ->where('id=?',1)               // where id =1
  //  ->group('title');               // group by title
 
$rowsArray = $connection->fetchAll($select); // return all rows
$rowArray =$connection->fetchRow($select); 





 //echo "<pre>";
				//	print_r($rowsArray);
					
					
				//	echo "mehul";
				//	exit;
					
					

				//	 echo "<pre>";
					// print_r($collection);
					 
					 
				foreach($result as $key=>$row ){
				
						$a = explode('|',$row['window']);
					//	echo "Delivery date is ".$result[$key]['window'];
					//	echo "<br/>";
						$array = sizeof($a);
						if($array == '2'){
						$a[1];
							$date = str_replace('_','/',$a[1]);
							list($dd,$mm,$yy) = explode("/", $date); 
							$yy = substr($yy,2,4);
						//	echo '<strong>Delivery date </strong>: '.$mm.'/'.$dd.'/'.$yy;	
						//	echo '<br />';	
							$a[0];
							$time = str_replace('_',':',$a[0]);
							$time1 = str_replace('pm:','pm - ',$time);
							//echo '<strong>Delivery Time </strong>: '.$time1;
							
						} else{
							$row['window'];
							$date = str_replace('_','/',$row['window']);
							list($dd,$mm,$yy) = explode("/", $date); 
							$yy = substr($yy,2,4);
						//echo '<strong>Delivery date </strong>: '. $mm.'/'.$dd.'/'.$yy;			
							}
							}
						
			
	*/
		
		//$collection = Mage::getResourceModel($this->_getCollectionClass());
	
//	$collection->join('order', 'main_table.order_id = order.entity_id', 'delivery_date');
		//$collection->join('order', 'main_table.order_id = order.entity_id','order_increment_id','order_created_at','billing_name');
		
	//	$collection->join('mg_sales_flat_order','main_table.order_id = mg_sales_flat_order.entity_id', 'delivery_date');
			
			
			//$collection->join('order', 'main_table.order_id = order.entity_id','order_increment_id','order_created_at','billing_name');
			
		//	$collection->getSelect()->joinLeft('mg_sales_flat_invoice_grid','main_table.order_id = mg_sales_flat_invoice_grid.entity_id',array('order_created_at','billing_name'));
//$collection->getSelect()->joinLeft('mg_sales_flat_invoice_grid','main_table.order_id = mg_sales_flat_invoice_grid.entity_id',array('order_created_at','billing_name'));
			
//$collection->getSelect()->joinLeft('mg_sales_flat_order', 'main_table.order_id = mg_sales_flat_order.entity_id',array('order_created_at','billing_name'));

//$collection->getSelect()->joinLeft('mg_sales_flat_invoice', 'main_table.entity_id = mg_sales_flat_invoice.entity_id',array('delivery_date'));


//$collection->getSelect()->joinLeft('mg_order_picker', 'main_table.order_id = mg_order_picker.entity_id',array('delivery_date'));

// work  $collection->getSelect()->joinLeft('mg_shipping_delivery_window','main_table.order_id = mg_shipping_delivery_window.order_number',array('window'));
//$collection = Mage::getSingleton('core/resource')->getTableName('mg_shipping_delivery_window');


//$collection->getSelect()->joinLeft('mg_shipping_delivery_window','main_table.order_id = mg_shipping_delivery_window.order_number',array('window'));


//$collection->getSelect()->joinLeft('mg_order_picker','main_table.order_id = mg_order_picker.order_id',array('window'));



 // $collection->getSelect()->joinLeft('sales_flat_order_payment', 'sales_flat_order_payment.parent_id = main_table.entity_id',array('method'));   

/*
 $_order = $this->getOrder() ;
	
				//	   echo $_order;
						 
						 
						$resource = Mage::getSingleton('core/resource');
					$readConnection = $resource->getConnection('core_read');
					$table = $resource->getTableName('mg_shipping_delivery_window');
				//	$query = "select * from mg_shipping_delivery_window where order_number ='".$_order->getRealOrderId()."'";
				$query = "select * from mg_shipping_delivery_window";
					 $result = $readConnection->fetchAll($query);
					 
					 $collection=$result;
					 
					 
					 */
				//	 $collection->addAttributeToSelect(array('window')); 
					 
				
					 
	//				 $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
 
//$select = $connection->select()->from('mg_shipping_delivery_window', array('window')); // select * from tablename or use array('id','title') selected values
  //  ->where('id=?',1)               // where id =1
  //  ->group('title');               // group by title
 
//$rowsArray = $connection->fetchAll($select); // return all rows
//$rowArray =$connection->fetchRow($select); 

 
 
  //mmmmm $collection->getSelect()->join('mg_shipping_delivery_window', array('window'));   
 
 //$collection->getSelect()->joinLeft('mg_shipping_delivery_window','main_table.order_id = mg_shipping_delivery_window.order_number',array('window'));
 
  //$collection->getSelect()->join('mg_shipping_delivery_window', 'main_table.entity_id = mg_shipping_delivery_window.parent_id',array('window'));
  /*
   $_order = $this->getOrder() ;
	
					   echo $_order;
						 
						 
						$resource = Mage::getSingleton('core/resource');
					$readConnection = $resource->getConnection('core_read');
					$table = $resource->getTableName('mg_shipping_delivery_window');
				//	$query = "select * from mg_shipping_delivery_window where order_number ='".$_order->getRealOrderId()."'";
				$query = "select * from mg_shipping_delivery_window";
					 $result = $readConnection->fetchAll($query);
					 
					 $collection=$result;
				//	 $collection->addAttributeToSelect(array('window')); 
					 
					 
					 
					 $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
					 
					 
  
 $select = $connection->select()->from('mg_shipping_delivery_window', array('window'));
 
 //echo $select;
 
 
 
 $rowsArray = $connection->fetchAll($select); // return all rows
 
// echo $rowArray; 
$rowArray =$connection->fetchRow($select); 
 
 echo $rowArray; 
 
*/
 //exit;
 
 
 
 
//$collection->getSelect();
 

 // $collection = Mage::getResourceModel($this->_getCollectionClass());

 
 $collection = Mage::getResourceModel($this->_getCollectionClass());
 
 
 

		
		//$collection->addAttributeToSelect(array('name', 'url_key', 'type_id')); 
		//$collection = Mage::getResourceModel($this->_getCollectionClass());
		
					$_order = $this->getOrder() ;
	
					  // echo $_order;
						 
						 
					$resource = Mage::getSingleton('core/resource');
					$readConnection = $resource->getConnection('core_read');
					$table = $resource->getTableName('mg_shipping_delivery_window');
				//	$query = "select * from mg_shipping_delivery_window where order_number ='".$_order->getRealOrderId()."'";
				$query = "select * from mg_shipping_delivery_window";
					 $result = $readConnection->fetchAll($query);
					 
					 $collection=$result;
				//	 $collection->addAttributeToSelect(array('window')); 
					 
					 
					 
					 $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
 
$select = $connection->select()->from('mg_shipping_delivery_window', array('window')); // select * from tablename or use array('id','title') selected values
  //  ->where('id=?',1)               // where id =1
  //  ->group('title');               // group by title
 
$rowsArray = $connection->fetchAll($select); // return all rows
$rowArray =$connection->fetchRow($select); 





//echo "<pre>";
			//print_r($rowsArray);
					
					
			//	echo "mehul";
		//	exit;
					
					

				// echo "<pre>";
				// print_r($collection);
					 
					 
				foreach($result as $key=>$row ){
				
						$a = explode('|',$row['window']);
				//	echo "Delivery date is ".$result[$key]['window'];
					//	echo "<br/>";
						$array = sizeof($a);
						if($array == '2'){
						$a[1];
							$date = str_replace('_','/',$a[1]);
							list($dd,$mm,$yy) = explode("/", $date); 
							$yy = substr($yy,2,4);
							//echo '<strong>Delivery date </strong>: '.$mm.'/'.$dd.'/'.$yy;	
							
							$mydate=$mm.'/'.$dd.'/'.$yy;
							
							//echo "mydate";
						//	echo $mydate;
							
						//	echo '<br />';	
							$a[0];
							$time = str_replace('_',':',$a[0]);
							$time1 = str_replace('pm:','pm - ',$time);
							//echo '<strong>Delivery Time </strong>: '.$time1;
							
						} else{
							$row['window'];
							$date = str_replace('_','/',$row['window']);
							list($dd,$mm,$yy) = explode("/", $date); 
							$yy = substr($yy,2,4);
						//echo '<strong>Delivery date </strong>: '. $mm.'/'.$dd.'/'.$yy;			
							}
							}
						
		$mydate=$mm.'/'.$dd.'/'.$yy;	
 echo "mydate";
  echo $mydate;

 
 


$collection = Mage::getResourceModel($this->_getCollectionClass());

$orders = Mage::getModel('sales/order')->getCollection()
                ->addFieldToFilter('status', $statusToExport)
                ->addFieldToFilter('store_id', $this->processingStoreId)
                ->addFieldToFilter('updated_at', array('gteq' => date('Y-m-d H:i:s', $mydate)));


 $collection->getSelect()->joinLeft('mg_shipping_delivery_window','main_table.order_id = mg_shipping_delivery_window.order_number',array('window'));

//$collection->getSelect()->joinLeft('mg_sales_flat_invoice', 'main_table.entity_id = mg_sales_flat_invoice.entity_id',array('delivery_date'));


//$collection->getSelect()->joinLeft('mg_sales_flat_order', 'main_table.entity_id = mg_sales_flat_order.entity_id',array('delivery_date'));
$collection->getSelect()->joinLeft('mg_sales_flat_order', 'main_table.order_id  = mg_sales_flat_order.entity_id',array('delivery_date'));


//exit;

		$this->setCollection($collection);
		//$this->setCollection($collection1);
		return parent::_prepareCollection();
		
		
		
	//	exit;
		
		
		
    }



    protected function _prepareColumns()
    {
        $this->addColumn('increment_id', array(
            'header'    => Mage::helper('sales')->__('Invoice #'),
            'index'     => 'increment_id',
            'type'      => 'text', 
        ));

        $this->addColumn('created_at', array( 
            'header'    => Mage::helper('sales')->__('Invoice Date'),
            'index'     => 'created_at',
            'type'      => 'datetime',
        ));
		
		
		
		  $this->addColumn('updated_at', array( 
            'header'    => Mage::helper('sales')->__('Invoice Date updated_at'),
            'index'     => 'updated_at',
            'type'      => 'datetime',
        ));

		

        $this->addColumn('order_increment_id', array( 
            'header'    => Mage::helper('sales')->__('Order #'),
            'index'     => 'order_increment_id',
            'type'      => 'text',
        ));

        $this->addColumn('order_created_at', array(
            'header'    => Mage::helper('sales')->__('Order Date'),
            'index'     => 'order_created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
        ));

        $this->addColumn('state', array(
            'header'    => Mage::helper('sales')->__('Status'),
            'index'     => 'state',
            'type'      => 'options',
            'options'   => Mage::getModel('sales/order_invoice')->getStates(),
        ));
 		
		
	
	/*
		$this->addColumn('in_product', array(
		 'header'    => Mage::helper('sales')->__('Delivery Date'),
    'type'            => 'text',
    'name'            => 'in_product',
    'values'          => $this->Result(),
    'align'           => 'center',
    
 ));
	
		*/
		
		
			$this->addColumn('delivery_date', array(
            'header'    => Mage::helper('sales')->__('Delivery Date'),
            'index'     => 'delivery_date',
            'type'      => 'text',
         //   'align'     => 'right',
           // 'currency'  => 'order_currency_code',
        ));
		$this->addColumn('window', array(
            'header'    => Mage::helper('sales')->__('window'),
            'index'     => 'window',
            'type'      => 'text',
         //   'align'     => 'right',
           // 'currency'  => 'order_currency_code',
        ));
		
	/*	
		
	$this->addColumn('delivery_date', array(
            'header'    => Mage::helper('sales')->__('Delivery Date'),
            'index'     => 'delivery_date',
            'type'      => 'text',
         //   'align'     => 'right',
           // 'currency'  => 'order_currency_code',
        ));
		
		
		
		
			
	$this->addColumn('delivery_date', array(
            'header'    => Mage::helper('sales')->__('Delivery Date1'),
            'index'     => $row['window'],
            'type'      => 'text',
         //   'align'     => 'right',
           // 'currency'  => 'order_currency_code',
        ));
		
		
		
			
		
		
		
	$this->addColumn('delivery_date', array(
            'header'    => Mage::helper('sales')->__('Delivery Date'),
            'index'     =>$result[$key]['window'],
			'values'   =>$result[$key]['window'],
			'type'      => 'text',
         //   'align'     => 'right',
           // 'currency'  => 'order_currency_code',
        ));
	
	
	
	
	
	
		$this->addColumn('window', array(
            'header'    => Mage::helper('sales')->__('window'),
            'index'     => 'window',
            'type'      => 'text',
         //   'align'     => 'right',
           // 'currency'  => 'order_currency_code',
        ));
		*/

	/*
		$this->addColumn('delivery_date',array(
				'header'=> Mage::helper('sales')->__('delivery_date'),
				'renderer'  => 'Mage_Adminhtml_Block_Sales_Invoice_Renderer_Deliverydate',// THIS IS WHAT THIS POST IS ALL ABOUT
				));
	
	*/
		
        $this->addColumn('grand_total', array(
            'header'    => Mage::helper('customer')->__('Amount'),
            'index'     => 'grand_total',
            'type'      => 'currency',
            'align'     => 'right',
            'currency'  => 'order_currency_code',
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('sales')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('sales')->__('View'),
                        'url'     => array('base'=>'*/sales_invoice/view'),
                        'field'   => 'invoice_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('invoice_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        $this->getMassactionBlock()->addItem('pdfinvoices_order', array(
             'label'=> Mage::helper('sales')->__('PDF Invoices'),
             'url'  => $this->getUrl('*/sales_invoice/pdfinvoices'),
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        if (!Mage::getSingleton('admin/session')->isAllowed('sales/order/invoice')) {
            return false;
        }

        return $this->getUrl('*/sales_invoice/view',
            array(
                'invoice_id'=> $row->getId(),
            )
        );
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
	
		public function Result()
        {
     


	
	
		 $_order = $this->getOrder() ;//echo $_order;
						 
				    $resource = Mage::getSingleton('core/resource');
					$readConnection = $resource->getConnection('core_read');
					$table = $resource->getTableName('mg_shipping_delivery_window');
				//	$query = "select * from mg_shipping_delivery_window where order_number ='".$_order->getRealOrderId()."'";
				$query = "select * from mg_shipping_delivery_window";
					 $result = $readConnection->fetchAll($query);
					 
					 $collection=$result;
				//	 $collection->addAttributeToSelect(array('window')); 
					
					 $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
 
$select = $connection->select()
    ->from('mg_shipping_delivery_window', array('window')); // select * from tablename or use array('id','title') selected values
  //  ->where('id=?',1)               // where id =1
  //  ->group('title');               // group by title
 
$rowsArray = $connection->fetchAll($select); // return all rows
$rowArray =$connection->fetchRow($select); 

 //echo "<pre>";
				//	print_r($rowsArray);
				//	echo "mehul";
				//	exit;
				//	 echo "<pre>";
					// print_r($collection);
					 
				foreach($result as $key=>$row ){
				
						$a = explode('|',$row['window']);
					//	echo "Delivery date is ".$result[$key]['window'];
					//	echo "<br/>";
						$array = sizeof($a);
						if($array == '2'){
						$a[1];
							$date = str_replace('_','/',$a[1]);
							list($dd,$mm,$yy) = explode("/", $date); 
							$yy = substr($yy,2,4);
						//echo '<strong>Delivery date </strong>: '.$mm.'/'.$dd.'/'.$yy;	
						//	echo '<br />';	
							$a[0];
							$time = str_replace('_',':',$a[0]);
							$time1 = str_replace('pm:','pm - ',$time);
							//echo '<strong>Delivery Time </strong>: '.$time1;
							
						} else{
							$row['window'];
							$date = str_replace('_','/',$row['window']);
							list($dd,$mm,$yy) = explode("/", $date); 
							$yy = substr($yy,2,4);
						//echo '<strong>Delivery date </strong>: '. $mm.'/'.$dd.'/'.$yy;			
							}
							}
						
			
	
	
	
	
	// $products = $result[$key]['window'];
	 
	// $products = $result[$key]['window'];
//echo '<strong>Delivery date </strong>: '.$mm.'/'.$dd.'/'.$yy;	
	  $result=$row['window'];
	 
    /*  foreach (Mage::registry('current_product')->getUpSellProducts() as $product) {
        $products[$product->getId()] = array('position' => $product->getPosition());
      }*/
	  
	  
	/*echo "<pre>";
	  echo $products;
	  
	  exit;*/
	  
    return $result;
	
	
	
	
	
	
        }
	
	

	

}

