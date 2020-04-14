<?php
class Valid_Zipcode_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/zipcode?id=15 
    	 *  or
    	 * http://site.com/zipcode/id/15 	
    	 */
    	
		
		$zipcode_id = $this->getRequest()->getParam('id');

  		if($zipcode_id != null && $zipcode_id != '')	{
			$zipcode = Mage::getModel('zipcode/zipcode')->load($zipcode_id)->getData();
		} else {
			$zipcode = null;
		}	
		
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	
    	if($zipcode == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$zipcodeTable = $resource->getTableName('zipcode');
			
			$select = $read->select()
			   ->from($zipcodeTable,array('id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$zipcode = $read->fetchRow($select);
		}
		Mage::register('zipcode', $zipcode);
		

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}