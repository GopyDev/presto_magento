<?php

class Magestore_Affiliateplusstatistic_Model_Mysql4_Report_Bestsellers_Collection extends Magestore_Affiliateplusstatistic_Model_Mysql4_Report_Collection_Abstract
{
	protected $_periodFormat;
	protected $_selectedColumns	= array();
	
	public function __construct(){
		parent::_construct();
		$this->setModel('adminhtml/report_item');
		$this->_resource = Mage::getResourceModel('sales/report')->init('sales/order_item','order_id');
		$this->setConnection($this->getResource()->getReadConnection());
		
		$this->_applyFilters = true;
		
		$this->_period_column = 'i.created_at';
		//$this->_status_column = 'status';
        $this->_store_column = 'i.store_id';
	}
	
	/**
	 * Retrieve columns for select
	 *
	 * @return array
	 */
	protected function _getSelectedColumns(){
		//$adapter = $this->getConnection();

		if (!$this->_selectedColumns) {
			if ($this->isTotals())
				$this->_selectedColumns = $this->getAggregatedColumns();
			else {
				//$this->_periodFormat = $adapter->getDateFormatSql('created_time', '%Y-%m-%d');
				$this->_periodFormat = "DATE_FORMAT(i.created_at, '%Y-%m-%d')";
				if ('year' == $this->_period)
					//$this->_periodFormat = $adapter->getDateFormatSql('created_time', '%Y');
					$this->_periodFormat = "DATE_FORMAT(i.created_at, '%Y')";
				elseif ('month' == $this->_period)
					//$this->_periodFormat = $adapter->getDateFormatSql('created_time', '%Y-%m');
					$this->_periodFormat = "DATE_FORMAT(i.created_at, '%Y-%m')";
				
				$this->_selectedColumns = array(
					'created_time'			=>  $this->_periodFormat,
					'account_email'			=> 'm.account_email',
					'account_name'			=> 'm.account_name,IF(aa.fhcb_affiliate_id is NULL,bl.name,m.account_name)',
					'fhcb_affiliate_id'     => 'aa.fhcb_affiliate_id,IF(aa.fhcb_affiliate_id is NULL,bl.fhcb_affiliate_id,aa.fhcb_affiliate_id)',
					'valueb'		        => 'caet.value',
					'aid'		            => 'i.item_id',
					'city'		            => 'caet1.value',
					'state'		            => 'caet2.value',
					'zip'		            => 'caet3.value',
					'order_number'		    => 'kl.increment_id',
					'sstatus'               => 'm.status ,(case 
					
					when ( m.status=2 ) THEN "Pending" 
					when ( m.status=3 ) THEN "Canceled"  
					when ( m.status=4 ) THEN "On Hold" 
					else "Complete"
					
					END )',
					
					'sstatus12'		    => 'ce.customer_group_code',
					
					
					
					
					
					'order_id'		        => 'i.order_id',
					'return'			    => 'cg.customer_group_id',
					'customer_group_code123' => 'ce.customer_group_code',
					'customer_group_code4444'	=> '(case 
					when ( cgo.customer_group_code="Active LE" ) THEN "-" 
					when ( cgo.customer_group_code!="Active LE" and c.group_id=4 ) THEN "Active" 
					when ( cgo.customer_group_code!="Active LE" and c.group_id!=4 ) THEN "Non-active" 
					
					END )',
					'order_type'	        => 'cgo.customer_group_code,IF(cgo.customer_group_code="Active LE","wholesale","Retail")',
					'product_name'			=> 'i.name',
					'sku'                   => 'i.sku',
					'vendor'                => 'cpet2.value',
					'base_price'			=> 'i.base_price',
					'qty_ordered'           => 'SUM(i.qty_ordered)',
                    'product_type'          => 'i.product_type',
					'discount'              => 'i.base_discount_amount',
                    'row_total'             => '(i.row_total - i.base_discount_amount)',
					
					'commission1'			=> '(case 
					when ( cpet.value!="89" and cgo.customer_group_code="Active LE" ) THEN ((i.row_total - i.base_discount_amount)*0.10) 
					when ( cpet.value="89"  and cgo.customer_group_code="Active LE" ) THEN ((i.row_total - i.base_discount_amount))*0.10 
					when ( cpet.value="89" and cgo.customer_group_code!="Active LE" ) THEN ((i.row_total - i.base_discount_amount))*0.40  
					when ( cpet.value!="89" and cgo.customer_group_code!="Active LE" ) THEN ((i.row_total - i.base_discount_amount))*0.40 
					
					
					END )',
					
					
					
					'commission2'			=> 'm.commission ,(case 
					when ( cpet.value!="89" and cgo.customer_group_code="Active LE" ) THEN ((i.row_total - i.base_discount_amount))*0
					when ( cpet.value="89"  and cgo.customer_group_code="Active LE" ) THEN ((i.row_total - i.base_discount_amount))*0.8 
					when ( cpet.value!="89" and cgo.customer_group_code!="Active LE" ) THEN ((i.row_total - i.base_discount_amount))*0
					when ( cpet.value="89" and cgo.customer_group_code!="Active LE" ) THEN ((i.row_total - i.base_discount_amount))*0.40
					
					
					END )',
					
					
					'commission3'			=> 'm.commission ,(case 
					when ( cpet.value!="89" and cgo.customer_group_code="Active LE" ) THEN ((i.row_total - i.base_discount_amount))*0.1
					when ( cpet.value="89"  and cgo.customer_group_code="Active LE" ) THEN ((i.row_total - i.base_discount_amount))*0.1 
					when ( cpet.value!="89" and cgo.customer_group_code!="Active LE" ) THEN ((i.row_total - i.base_discount_amount))*0.2
					when ( cpet.value="89" and cgo.customer_group_code!="Active LE" ) THEN ((i.row_total - i.base_discount_amount))*0.2
					
					
					END )',
					
					
					'commission4'			=> 'm.commission ,(case 
					when ( cpet.value!="89" and cgo.customer_group_code="Active LE" ) THEN ((i.row_total - i.base_discount_amount))*0.8
					when ( cpet.value="89"  and cgo.customer_group_code="Active LE" ) THEN ((i.row_total - i.base_discount_amount))*0
					when ( cpet.value!="89" and cgo.customer_group_code!="Active LE" ) THEN ((i.row_total - i.base_discount_amount))*0.4
					when ( cpet.value="89" and cgo.customer_group_code!="Active LE" ) THEN ((i.row_total - i.base_discount_amount))*0
					
					
					END )',
					
					
					
					
					/* 'cop'  => 'i.parent_item_id,(case 
					when ( cpet.value="89" and ce.customer_group_code="Active LE" and cgo.customer_group_code="Active LE" ) THEN ((i.row_total - i.base_discount_amount))*0.40  
					when ( cpet.value="89" and ce.customer_group_code="Active LE" and cgo.customer_group_code!="Active LE" ) THEN ((i.row_total - i.base_discount_amount))*0.40 
					when ( cpet.value!="89" and ce.customer_group_code="Active LE" and cgo.customer_group_code="Active LE" ) THEN ((i.row_total - i.base_discount_amount))*0 
					when ( cpet.value!="89" and ce.customer_group_code="Active LE" and cgo.customer_group_code!="Active LE" ) THEN ((i.row_total - i.base_discount_amount))*0 
					
					when ( cpet.value="89" and ce.customer_group_code!="Active LE" and cgo.customer_group_code="Active LE" ) THEN ((i.row_total - i.base_discount_amount))*0.40 
					when ( cpet.value!="89" and ce.customer_group_code!="Active LE" and cgo.customer_group_code="Active LE" ) THEN ((i.row_total - i.base_discount_amount))*0   
					when ( cpet.value="89" and ce.customer_group_code!="Active LE" and cgo.customer_group_code!="Active LE" ) THEN ((i.row_total - i.base_discount_amount))*0.40 
					when ( cpet.value!="89" and ce.customer_group_code!="Active LE" and cgo.customer_group_code!="Active LE" ) THEN ((i.row_total - i.base_discount_amount))*0 
					
					END )', */
					
					
					
				);
			}
		}
		return $this->_selectedColumns;
		
		
		
		// print_r($requestData);
	}
	
	/**
     * Add selected data
     *
     * @return Magestore_Affiliateplusstatistic_Model_Mysql4_Report_Sales_Collection
     */
	protected function _initSelect(){
		$this->getSelect()->from(array('i' => $this->getResource()->getMainTable()), $this->_getSelectedColumns());
        $itemTable = $this->getResource()->getTable('affiliateplus/transaction');
        $this->getSelect()->joinLeft(array('m' => $itemTable),
            'i.order_id = m.order_id ',
            array()
        );
		
		 $this->getSelect()->joinLeft(array('cpet' => 'catalog_product_entity_int'),
            'i.product_id = cpet.entity_id and cpet.attribute_id=164',
            array()
        );
		
		$this->getSelect()->joinLeft(array('cpet2' => 'eav_attribute_option_value'),
            'cpet.value = cpet2.option_id',
            array()
        );
		
		
		 $this->getSelect()->joinLeft(array('c' => 'customer_entity'),
            'm.account_email = c.email',
            array()
        );
		
		$this->getSelect()->joinLeft(array('cg' => 'sales_flat_order'),
            'i.order_id = cg.entity_id',
            array()
        );
		
		$this->getSelect()->joinLeft(array('kl' => 'sales_flat_order_grid'),
            'i.order_id = kl.entity_id',
            array()
        );
		
		$this->getSelect()->joinLeft(array('bl' => 'affiliateplus_account'),
            'kl.customer_id = bl.customer_id',
            array()
        );
		
		
		$this->getSelect()->joinLeft(array('cgo' => 'customer_group'),
            'cg.customer_group_id = cgo.customer_group_id',
            array()
        );
		
		$this->getSelect()->joinLeft(array('aa' => 'affiliateplus_account'),
            'm.account_id = aa.account_id',
            array()
        );
		
		$this->getSelect()->joinLeft(array('caet' => 'customer_address_entity_text'),
            'aa.address_id = caet.value_id',
            array()
        );
		
		$this->getSelect()->joinLeft(array('caet1' => 'customer_address_entity_varchar'),
            'aa.address_id = caet1.entity_id AND caet1.attribute_id=26',
            array()
        );
		
		$this->getSelect()->joinLeft(array('caet2' => 'customer_address_entity_varchar'),
            'aa.address_id = caet2.entity_id AND caet2.attribute_id=28',
            array()
        );
		
		$this->getSelect()->joinLeft(array('caet3' => 'customer_address_entity_varchar'),
            'aa.address_id = caet3.entity_id AND caet3.attribute_id=30',
            array()
        );
	
		 $this->getSelect()->joinLeft(array('ce' => 'customer_group'),
            'c.group_id = ce.customer_group_id',
            array()
        );
		
		 $this->getSelect()->WHERE(('i.product_id != 1009' ));
		 $this->getSelect()->WHERE(('i.product_id != 1231' ));
		  $this->getSelect()->WHERE(('i.product_id != 1219' ));
		
		if (!$this->isTotals())
			$this->getSelect()
				->group(array('i.item_id'))
				->order('m.created_time');
               // ->order('SUM(i.qty_ordered) DESC')
               // ->order('base_price DESC');
		 return $this;
		
	
		
		//echo $this->getSelect()->__toString();
	}
}