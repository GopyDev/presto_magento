<?php

class Sitegurus_Pincode_Block_Adminhtml_Pincode_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('pincodeGrid');
      $this->setDefaultSort('pincode_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('pincode/pincode')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('pincode_id', array(
          'header'    => Mage::helper('pincode')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'pincode_id',
      ));

      
      $this->addColumn('area_name', array(
          'header'    => Mage::helper('pincode')->__('Area'),
          'align'     =>'left',
          'index'     => 'area_name',
      ));
      
      
      $this->addColumn('pin_code', array(
          'header'    => Mage::helper('pincode')->__('PinCode'),
          'align'     =>'left',
          'index'     => 'pin_code',
      ));
      
	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('pincode')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */

      $this->addColumn('status', array(
          'header'    => Mage::helper('pincode')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('pincode')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('pincode')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('pincode')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('pincode')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('pincode_id');
        $this->getMassactionBlock()->setFormFieldName('pincode');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('pincode')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('pincode')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('pincode/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('pincode')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('pincode')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}