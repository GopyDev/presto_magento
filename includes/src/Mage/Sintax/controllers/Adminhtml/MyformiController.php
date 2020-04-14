<?php

class Mage_Sintax_Adminhtml_MyformiController extends Mage_Adminhtml_Controller_Action
{
    public function listAction()
    {
        //$this->loadLayout()->renderLayout();
		echo "hiii";
		exit(0);
    }
	
	
	public function indexAction()
    {
       // $this->loadLayout()->renderLayout();
	   
	   echo "hiii";
		exit(0);
		header("location:google.com");
    }
	
	
    
    public function postAction()
    {   
	
	    echo "hiren";
		exit(0);
		
        $post = $this->getRequest()->getPost();
        try {
            if (empty($post)) {
                Mage::throwException($this->__('Invalid form data.'));
            }
            
            /* here's your form processing */
            
            // $message = $this->__('Your form has been submitted successfully.');
            // Mage::getSingleton('adminhtml/session')->addSuccess($message);
			
			// print_r($post);
			
			$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
			$sql3eb = "select * from affiliateplus_account where fhcb_affiliate_id='".$post["ordernumber"]."'";
	        $orders_row2 = $connection->fetchAll($sql3eb); 
	
	        // print_r($orders_row2);
						
			$update="update affiliateplus_transaction set account_id='".$orders_row2[0]["account_id"]."',account_name='".$orders_row2[0]["name"]."',account_email='".$orders_row2[0]["email"]."' where transaction_id='".$post["transaction_id"]."'";
			$connection->query($update);
			
			$message = $this->__('Affliate has been change successfully.');
            Mage::getSingleton('adminhtml/session')->addSuccess($message);
			
			
			
			
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
         $this->_redirect('*/*/?id='.$post["oid"]);
    }
}