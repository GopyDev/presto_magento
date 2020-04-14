
<?php

class Sitegurus_Pincode_Adminhtml_PincodeController extends Mage_Adminhtml_Controller_action
{

    protected function _initAction() {
        //DebugBreak();
        $this->loadLayout()
            ->_setActiveMenu('pincode/items')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Pincode Manager'), Mage::helper('adminhtml')->__('Pincode Manager'));
        
        return $this;
    }   
 
    public function indexAction() {
        
        $this->_initAction()
            ->renderLayout();
    }
    
    public function salesreportAction() {

        $this->loadLayout();

        //create a text block with the name of "example-block"
        $block = $this->getLayout()
        ->createBlock('core/template')
        ->setTemplate('pincode/sales_report.phtml');

        $this->_addContent($block);

        $this->renderLayout();
            
    }
    
    
    public function postsalesAction()
    {
        //DebugBreak();
        $post = $this->getRequest()->getPost();
        $area_name = $post['myform']['myfield'];
        try {
            if (empty($post)) {
                Mage::throwException($this->__('Invalid form data.'));
            }
            
            
            $this->loadLayout();
            $block = $this->getLayout()->createBlock('core/template')->setTemplate('pincode/sales_report.phtml');
            $this->_addContent($block);
            $this->renderLayout();
            
            $message = $this->__('Sales orders based on Area are as below:');
            Mage::getSingleton('adminhtml/session')->addSuccess($message);
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        //$this->_redirect('*/*/salesreport');
    }
    
    public function exportordersAction(){
        //DebugBreak();
        $file_path = Mage::getBaseDir().'/var/export/sales_order_export.csv';
        $mage_csv = new Varien_File_Csv(); 
        $information_row = array();    
        $area_name = $this->getRequest()->getParam('area-name');
        $from_date = $this->getRequest()->getParam('from-date');
        $to_date = $this->getRequest()->getParam('to-date');
        
        $data[0]['area'] = 'Area';
        $data[0]['date'] = 'Date';
        $data[0]['count'] = 'Total Orders';
        $data[0]['subtotal'] = 'Subtotal';
        $data[0]['invoiced'] = 'Invoiced';
        $data[0]['refunded'] = 'Refunded';
        $data[0]['tax_amount'] = 'Tax amount';
        $data[0]['shipping_amount'] = 'Shipping Amount';
        $data[0]['discounts'] = 'Discounts';
        $data[0]['cancelled'] = 'Cancelled';
        
        
        
        if($area_name == "All Areas"){
            //DebugBreak();
            $collection = Mage::getModel('pincode/pincode')->getCollection();
            $i = 1;
                    foreach($collection as $collection_area){
                    $area_names[] = $collection_area->getData('area_name');}
                    foreach($area_names as $area_name){
                        
                            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $read= Mage::getSingleton('core/resource')->getConnection('core_read');
        $sql1 = "SELECT pin_code FROM pincode where area_name='".$area_name."'";
        $res = $read->fetchRow($sql1);
        $pincode = $res['pin_code'];
        $sql2 = "SELECT count(postcode),email FROM sales_flat_order_address where postcode in ('".$pincode."') and address_type='billing' group by sales_flat_order_address.postcode";
            $res1 = $read->fetchRow($sql2); 
        
        $sql3 = "select count(sales_flat_order_address.postcode),sum(sales_flat_order.subtotal),sum(sales_flat_order.base_subtotal_invoiced),sum(sales_flat_order.subtotal_refunded),sum(sales_flat_order.base_shipping_tax_amount),sum(sales_flat_order.shipping_amount),sum(sales_flat_order.discount_amount),sum(sales_flat_order.subtotal_canceled),date(sales_flat_order.created_at) from sales_flat_order,sales_flat_order_address where sales_flat_order_address.parent_id = sales_flat_order.entity_id and sales_flat_order_address.postcode in ('".$pincode."')and sales_flat_order_address.address_type='billing' and date(sales_flat_order.created_at) between '".$from_date."' and '".$to_date."' group by sales_flat_order_address.postcode,date(sales_flat_order.created_at)";
            $ress2 = $read->fetchAll($sql3);
            foreach($ress2 as $res2){
                //DebugBreak();
                    $data[$i]['area'] = $area_name;
                    $data[$i]['date'] = $res2['date(sales_flat_order.created_at)'];
                    $data[$i]['count'] = $res2['count(sales_flat_order_address.postcode)'];
                    $data[$i]['subtotal'] = $res2['sum(sales_flat_order.subtotal)'];
                    $data[$i]['invoiced'] = $res2['sum(sales_flat_order.base_subtotal_invoiced)'];
                    $data[$i]['refunded'] = $res2['sum(sales_flat_order.subtotal_refunded)'];
                    $data[$i]['tax_amount'] = $res2['sum(sales_flat_order.base_shipping_tax_amount)'];
                    $data[$i]['shipping_amount'] = $res2['sum(sales_flat_order.shipping_amount)'];
                    $data[$i]['discounts'] = $res2['sum(sales_flat_order.discount_amount)'];
                    $data[$i]['cancelled'] = $res2['sum(sales_flat_order.subtotal_canceled)'];    
                    
                    
                    $orders_placed[] = $res2['count(sales_flat_order_address.postcode)'];
                    $subtotal[] = $res2['sum(sales_flat_order.subtotal)'];
                    $base_subtotal_invoiced[] = $res2['sum(sales_flat_order.base_subtotal_invoiced)'];
                    $subtotal_refunded[] = $res2['sum(sales_flat_order.subtotal_refunded)'];
                    $base_shipping_tax_amount[] = $res2['sum(sales_flat_order.base_shipping_tax_amount)'];
                    $shipping_amount[] = $res2['sum(sales_flat_order.shipping_amount)'];
                    $discount_amount[] = $res2['sum(sales_flat_order.discount_amount)'];
                    $subtotal_canceled[] = $res2['sum(sales_flat_order.subtotal_canceled)'];
                    
                    $i++;
            }
        $data[$i]['area'] = 'Total';
        $data[$i]['date'] = ' ';
        $data[$i]['count'] = array_sum($orders_placed);
        $data[$i]['subtotal'] = array_sum($subtotal);
        $data[$i]['invoiced'] = array_sum($base_subtotal_invoiced);
        $data[$i]['refunded'] = array_sum($subtotal_refunded);
        $data[$i]['tax_amount'] = array_sum($base_shipping_tax_amount);
        $data[$i]['shipping_amount'] = array_sum($shipping_amount);
        $data[$i]['discounts'] = array_sum($discount_amount);
        $data[$i]['cancelled'] = array_sum($subtotal_canceled);
        
                        
                    }
                    foreach($data as $val)
                    $information_row[] = $val;          
                    
        }
        else{
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $read= Mage::getSingleton('core/resource')->getConnection('core_read');
        $sql1 = "SELECT pin_code FROM pincode where area_name='".$area_name."'";
        $res = $read->fetchRow($sql1);
        $pincode = $res['pin_code'];
        $sql2 = "SELECT count(postcode),email FROM sales_flat_order_address where postcode in (".$pincode.") and address_type='billing' group by sales_flat_order_address.postcode";
            $res1 = $read->fetchRow($sql2); 
        $sql3 = "select count(sales_flat_order_address.postcode),sum(sales_flat_order.subtotal),sum(sales_flat_order.base_subtotal_invoiced),sum(sales_flat_order.subtotal_refunded),sum(sales_flat_order.base_shipping_tax_amount),sum(sales_flat_order.shipping_amount),sum(sales_flat_order.discount_amount),sum(sales_flat_order.subtotal_canceled),date(sales_flat_order.created_at) from sales_flat_order,sales_flat_order_address where sales_flat_order_address.parent_id = sales_flat_order.entity_id and sales_flat_order_address.postcode in ('".$pincode."')and sales_flat_order_address.address_type='billing' and date(sales_flat_order.created_at) between '".$from_date."' and '".$to_date."' group by sales_flat_order_address.postcode,date(sales_flat_order.created_at)";
            $ress2 = $read->fetchAll($sql3);
        $i = 1;
        foreach($ress2 as $res2){
                
                    $data[$i]['area'] = $area_name;
                    $data[$i]['date'] = $res2['date(sales_flat_order.created_at)'];
                    $data[$i]['count'] = $res2['count(sales_flat_order_address.postcode)'];
                    $data[$i]['subtotal'] = $res2['sum(sales_flat_order.subtotal)'];
                    $data[$i]['invoiced'] = $res2['sum(sales_flat_order.base_subtotal_invoiced)'];
                    $data[$i]['refunded'] = $res2['sum(sales_flat_order.subtotal_refunded)'];
                    $data[$i]['tax_amount'] = $res2['sum(sales_flat_order.base_shipping_tax_amount)'];
                    $data[$i]['shipping_amount'] = $res2['sum(sales_flat_order.shipping_amount)'];
                    $data[$i]['discounts'] = $res2['sum(sales_flat_order.discount_amount)'];
                    $data[$i]['cancelled'] = $res2['sum(sales_flat_order.subtotal_canceled)'];    
                    
                    
                    $orders_placed[] = $res2['count(sales_flat_order_address.postcode)'];
                    $subtotal[] = $res2['sum(sales_flat_order.subtotal)'];
                    $base_subtotal_invoiced[] = $res2['sum(sales_flat_order.base_subtotal_invoiced)'];
                    $subtotal_refunded[] = $res2['sum(sales_flat_order.subtotal_refunded)'];
                    $base_shipping_tax_amount[] = $res2['sum(sales_flat_order.base_shipping_tax_amount)'];
                    $shipping_amount[] = $res2['sum(sales_flat_order.shipping_amount)'];
                    $discount_amount[] = $res2['sum(sales_flat_order.discount_amount)'];
                    $subtotal_canceled[] = $res2['sum(sales_flat_order.subtotal_canceled)'];
                    
                    $i++;
            }
            
        $data[$i]['area'] = 'Total';
        $data[$i]['date'] = ' ';
        $data[$i]['count'] = array_sum($orders_placed);
        $data[$i]['subtotal'] = array_sum($subtotal);
        $data[$i]['invoiced'] = array_sum($base_subtotal_invoiced);
        $data[$i]['refunded'] = array_sum($subtotal_refunded);
        $data[$i]['tax_amount'] = array_sum($base_shipping_tax_amount);
        $data[$i]['shipping_amount'] = array_sum($shipping_amount);
        $data[$i]['discounts'] = array_sum($discount_amount);
        $data[$i]['cancelled'] = array_sum($subtotal_canceled);
        
        foreach($data as $val)
        $information_row[] = $val;
        
        }
            
        //$data = array();
        
        
    
        $mage_csv->saveData($file_path, $information_row);
        $message = $this->__('CSV is exported');
        Mage::getSingleton('adminhtml/session')->addSuccess($message);
        $this->_redirect('*/*/salesreport');
    }

    public function editAction() {
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('pincode/pincode')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('pincode_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('pincode/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Pincode Manager'), Mage::helper('adminhtml')->__('Pincode Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Pincode News'), Mage::helper('adminhtml')->__('Pincode News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('pincode/adminhtml_pincode_edit'))
                ->_addLeft($this->getLayout()->createBlock('pincode/adminhtml_pincode_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('pincode')->__('Pincode does not exist'));
            $this->_redirect('*/*/');
        }
    }
 
    public function newAction() {
        //DebugBreak();
        //$this->_forward('edit');
        $this->editAction();
    }
 
    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            
            if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
                try {    
                    /* Starting upload */    
                    $uploader = new Varien_File_Uploader('filename');
                    
                    // Any extention would work
                       $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(false);
                    
                    // Set the file upload mode 
                    // false -> get the file directly in the specified folder
                    // true -> get the file in the product like folders 
                    //    (file.jpg will go in something like /media/f/i/file.jpg)
                    $uploader->setFilesDispersion(false);
                            
                    // We set media as the upload dir
                    $path = Mage::getBaseDir('media') . DS ;
                    $uploader->save($path, $_FILES['filename']['name'] );
                    
                } catch (Exception $e) {
              
                }
            
                //this way the name is saved in DB
                  $data['filename'] = $_FILES['filename']['name'];
            }
                  
                  
            $model = Mage::getModel('pincode/pincode');        
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));
            
            try {
                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                        ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }    
                
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('pincode')->__('Pincode was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('pincode')->__('Unable to find Pincode to save'));
        $this->_redirect('*/*/');
    }
 
    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('pincode/pincode');
                 
                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();
                     
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Pincode was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $pincodeIds = $this->getRequest()->getParam('pincode');
        if(!is_array($pincodeIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Pincode(s)'));
        } else {
            try {
                foreach ($pincodeIds as $pincodeId) {
                    $pincode = Mage::getModel('pincode/pincode')->load($pincodeId);
                    $pincode->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($pincodeIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    
    public function massStatusAction()
    {
        $pincodeIds = $this->getRequest()->getParam('pincode');
        if(!is_array($pincodeIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Pincode(s)'));
        } else {
            try {
                foreach ($pincodeIds as $pincodeId) {
                    $pincode = Mage::getSingleton('pincode/pincode')
                        ->load($pincodeId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($pincodeIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
}