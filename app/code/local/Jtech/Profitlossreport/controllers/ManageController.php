<?php
/************************************************************************
 * 
 * jtechextensions @ J-Tech LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.jtechextensions.com/LICENSE-M1.txt
 *
 * @package    Inventory Report
 * @copyright  Copyright (c) 2012-2013 jtechextensions @ J-Tech LLC. (http://www.jtechextensions.com)
 * @license    http://www.jtechextensions.com/LICENSE-M1.txt
************************************************************************/

class Jtech_Profitlossreport_ManageController extends Mage_Adminhtml_Controller_Action
{
    public function _initAction()
    {
    	// Highlights the menu items to signify selection.
        $this->loadLayout()
        	->_setActiveMenu('report/marcore/profitlossreport')
            ->_addBreadcrumb(Mage::helper('profitlossreport')->__('Reports'), Mage::helper('profitlossreport')->__('Reports'))
            ->_addBreadcrumb(Mage::helper('profitlossreport')->__('Jtech Adavanced Reporting'), Mage::helper('profitlossreport')->__('Jtech Adavanced Reporting'))
            ->_addBreadcrumb(Mage::helper('profitlossreport')->__('Profit and Loss Report'), Mage::helper('profitlossreport')->__('Profit and Loss Report'));
        
        return $this;
    }
    
    public function indexAction()
    {
    	$this->_redirect('*/*/viewall');
    }
    
	/*
	 *	Default action.
	 */
	public function viewallAction()
	{	
		$this->_initAction()
			->_addBreadcrumb(Mage::helper('profitlossreport')->__('Manage & View Reports'), Mage::helper('profitlossreport')->__('Manage & View Reports'));
			
		$this->renderLayout();
	}
	
	public function editAction()
	{
		$ReportId = $this->getRequest()->getParam('id');
		$ReportModel = Mage::getModel('profitlossreport/report')->load($ReportId);
		
		if ( $ReportModel->getId() || $ReportId == 0 )
		{
			Mage::register('report_data', $ReportModel);
			
			$this->_initAction()
			->_addBreadcrumb(Mage::helper('profitlossreport')->__('Edit Report'), Mage::helper('profitlossreport')->__('Edit Report'));
			
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addLeft($this->getLayout()->createBlock('profitlossreport/manager_edit_tabs'));

			$jsBlock = $this->getLayout()->createBlock('core/text')->setText('
				<script type="text/javascript">
				    function saveview() {
						var form = document.getElementById("edit_form");
				    	
						var saveViewElement = document.getElementById("saveview");
						if (saveViewElement == null) {
							var saveViewElement = document.createElement("input");
							saveViewElement.setAttribute("type", "hidden");
							saveViewElement.setAttribute("value", "1");
							saveViewElement.setAttribute("name", "saveview");
							saveViewElement.setAttribute("id", "saveview");
							
							form.appendChild(saveViewElement);
						} else {
							saveViewElement.setAttribute("value", "1");
						}

						editForm.submit();
					}
					
					function justsave() {
						var saveViewElement = document.getElementById("saveview");
						if (saveViewElement != null) {
							saveViewElement.setAttribute("value", "0");
						}
						
						editForm.submit();
					}
				</script>
			');
			$this->_addJs($jsBlock);
			
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('profitlossreport')->__('The report does not exist'));
			$this->_redirect('*/*/viewall');
		}
	}
	
	public function newAction()
	{
		$this->_forward('edit');
	}
	
	public function saveAction()
	{
		$currId = $this->getRequest()->getParam('id');
		$postData = $this->getRequest()->getPost();
		
		// handle save view functionality
		if (isset($postData['saveview'])) {
			if ($postData['saveview'] == '1') {
				$saveThenView = true;
			} else {
				$saveThenView = false;
			}
			
			unset($postData['saveview']);
		} else {
			$saveThenView = false;
		}

		try
		{
			$resource = Mage::getSingleton('core/resource');
			$read = $resource->getConnection('core_read');
			$write = $resource->getConnection('core_write');
		
			$reportsTable = $resource->getTableName('jtech_pl_reports');
			$reportAttTable = $resource->getTableName('jtech_pl_reports_attributes');
			$attributesTable = $resource->getTableName('jtech_pl_attributes');
			
			// Get current date and time and set it to created and updated fields
			$currentTime = Mage::getSingleton('core/date')->gmtDate();
			
			if ($currId <= 0)
			{
				// Handle for new report
				$write->query('INSERT INTO ' . $reportsTable . ' (name, date_created, date_updated) VALUES ("' . $postData['report_name'] . '", "' . $currentTime . '", "' . $currentTime . '")'); 
				
				// Fetch the last inserted ID value for use in next query.
				$lastReportId = $write->lastInsertId();
				
				// Mass Insert for all values
				
				$select = 'SELECT * 
							FROM ' . $attributesTable .' a';
				$attributes = $read->fetchAll($select);
				
				foreach ($attributes as $attribute)
				{
					$write->query('INSERT INTO ' . $reportAttTable . ' (report_id, att_id, att_name, is_parent, parent_att_id, att_type_id, value) VALUES (' . $lastReportId . ', ' . $attribute['att_id'] . ', "' . $attribute['name'] . '", ' . $attribute['is_parent'] . ', ' . ( ($attribute['parent_id']) ? $attribute['parent_id'] : 'null' ) . ', ' . $attribute['att_type_id'] . ', "")');
				}
				
				// Parse default required fields and insert them to DB.
				foreach ($postData as $att_id => $value)
				{
					if (is_int($att_id))
					{
						$select = 'SELECT a.name as name, a.att_type_id as type
									FROM ' . $attributesTable .' a
									WHERE a.att_id = ' . $att_id;
						$currAttNameType = $read->fetchRow($select);
					} else {
						$currAttNameType = -1;
					}
					
					if ($att_id == 'show_order_statuses'){
						$useSpecOrderStatus = $value;
					}
					
					if ($att_id != 'form_key' && $att_id != 'report_name' && $att_id != 'show_order_statuses')
					{
						if ($att_id == 'order_statuses')
						{
							if ($useSpecOrderStatus == 1) {
								// Is a sub array of statuses
								for ($i = 0; $i < count($value); $i++)
								{
									if ($i == 0)
									{
										$orderStatus = $value[$i];
									} else {
										$orderStatus = $orderStatus . ', ' . $value[$i];
									}
								}
							} else {
								$orderStatus = '';
							}

							$write->query('UPDATE ' . $reportAttTable . ' SET value = "' . $orderStatus . '" WHERE report_id = ' . $lastReportId . ' AND att_id = 6');
						} else if ($currAttNameType['type'] != 1) {
							// Is a currency item
							$select = 'SELECT a.is_parent as is_parent, a.parent_id as parent_id, a.att_type_id as att_type_id
										FROM ' . $attributesTable .' a
										WHERE a.att_id = ' . $att_id;
							$currAttSettings = $read->fetchRow($select);
							
							$write->query('UPDATE ' . $reportAttTable . ' SET value = "' . (($value != '') ? number_format($value, 2, '.', '') : '') . '" WHERE report_id = ' . $lastReportId . ' AND att_id = ' . $att_id);
						} else if ($currAttNameType['name'] == 'date_from' || $currAttNameType['name'] == 'date_to') {
							$rawDate = explode('/', $value);
							if ($currAttNameType['name'] == 'date_to') {
								$time = strtotime($rawDate[2] . '-' . $rawDate[1] . '-' . $rawDate[0] . ' 23:59:59');
							} else {
								$time = strtotime($rawDate[2] . '-' . $rawDate[1] . '-' . $rawDate[0] . ' 00:00:00');
							}
							$myDate = date('Y-m-d H:i:s', $time);
							$write->query('UPDATE ' . $reportAttTable . ' SET value = "' . $myDate . '" WHERE report_id = ' . $lastReportId . ' AND att_id = ' . $att_id);
						} else {
							$write->query('UPDATE ' . $reportAttTable . ' SET value = "' . $value . '" WHERE report_id = ' . $lastReportId . ' AND att_id = ' . $att_id);
						}
					}
				}
			} else {
				// Handle for updating an existing report
				
				$write->query('UPDATE ' . $reportsTable . ' SET name = "' . $postData['report_name'] . '", date_updated = "' . $currentTime . '" WHERE report_id = ' . $currId); 

				// Delete all old attributes and insert the new modified versions
				$write->query('DELETE FROM ' . $reportAttTable . ' WHERE report_id = ' . $currId);
				
				// Assign the last report ID to the one we want to modify
				$lastReportId = $currId;
				
				// Mass Insert for all values
				
				$select = 'SELECT * 
							FROM ' . $attributesTable .' a';
				$attributes = $read->fetchAll($select);
				
				foreach ($attributes as $attribute)
				{
					$write->query('INSERT INTO ' . $reportAttTable . ' (report_id, att_id, att_name, is_parent, parent_att_id, att_type_id, value) VALUES (' . $lastReportId . ', ' . $attribute['att_id'] . ', "' . $attribute['name'] . '", ' . $attribute['is_parent'] . ', ' . ( ($attribute['parent_id']) ? $attribute['parent_id'] : 'null' ) . ', ' . $attribute['att_type_id'] . ', "")');
				}
				
				// Parse default required fields and insert them to DB.
				foreach ($postData as $att_id => $value)
				{
					if (is_int($att_id))
					{
						$select = 'SELECT a.name as name, a.att_type_id as type
									FROM ' . $attributesTable .' a
									WHERE a.att_id = ' . $att_id;
						$currAttNameType = $read->fetchRow($select);
					} else {
						$currAttNameType = -1;
					}
					
					if ($att_id == 'show_order_statuses'){
						$useSpecOrderStatus = $value;
					}
					
					if ($att_id != 'form_key' && $att_id != 'report_name' && $att_id != 'show_order_statuses')
					{
						if ($att_id == 'order_statuses')
						{
							if ($useSpecOrderStatus == 1) {
								// Is a sub array of statuses
								for ($i = 0; $i < count($value); $i++)
								{
									if ($i == 0)
									{
										$orderStatus = $value[$i];
									} else {
										$orderStatus = $orderStatus . ', ' . $value[$i];
									}
								}
							} else {
								$orderStatus = '';
							}

							$write->query('UPDATE ' . $reportAttTable . ' SET value = "' . $orderStatus . '" WHERE report_id = ' . $lastReportId . ' AND att_id = 6');
						} else if ($currAttNameType['type'] != 1) {
							// Is a currency item
							$select = 'SELECT a.is_parent as is_parent, a.parent_id as parent_id, a.att_type_id as att_type_id
										FROM ' . $attributesTable .' a
										WHERE a.att_id = ' . $att_id;
							$currAttSettings = $read->fetchRow($select);
							
							$write->query('UPDATE ' . $reportAttTable . ' SET value = "' . (($value != '') ? number_format($value, 2, '.', '') : '') . '" WHERE report_id = ' . $lastReportId . ' AND att_id = ' . $att_id);
						} else if ($currAttNameType['name'] == 'date_from' || $currAttNameType['name'] == 'date_to') {
							$rawDate = explode('/', $value);
							if ($currAttNameType['name'] == 'date_to') {
								$time = strtotime($rawDate[2] . '-' . $rawDate[1] . '-' . $rawDate[0] . ' 23:59:59');
							} else {
								$time = strtotime($rawDate[2] . '-' . $rawDate[1] . '-' . $rawDate[0] . ' 00:00:00');
							}
							$myDate = date('Y-m-d H:i:s', $time);
							$write->query('UPDATE ' . $reportAttTable . ' SET value = "' . $myDate . '" WHERE report_id = ' . $lastReportId . ' AND att_id = ' . $att_id);
						} else {
							$write->query('UPDATE ' . $reportAttTable . ' SET value = "' . $value . '" WHERE report_id = ' . $lastReportId . ' AND att_id = ' . $att_id);
						}
					}
				}
			}
			
			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The report was successfully saved'));
			Mage::getSingleton('adminhtml/session')->getReportData(false);
			
			if ($saveThenView) {
				$this->_redirect('*/*/view', array('id' => $lastReportId));
			} else {
				$this->_redirect('*/*/viewall');
			}

			return;
		} catch (Exception $e) {
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			Mage::getSingleton('adminhtml/session')->setReportData($postData);
			$this->_redirect('*/*/edit', array('id' => $currId));
			return;
		}

		$this->_redirect('*/*/viewall');
	}
	
	public function deleteAction()
	{
		$currId = $this->getRequest()->getParam('id');
		
		if ($currId > 0)
		{
			try
			{
				$resource = Mage::getSingleton('core/resource');
				$write = $resource->getConnection('core_write');
			
				$reportsTable = $resource->getTableName('jtech_pl_reports');
				
				$write->query('DELETE FROM ' . $reportsTable . ' WHERE report_id = ' . $currId);
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The report was successfully deleted'));
				$this->_redirect('*/*/viewall');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*,*,edit', array('id' => $currId));
			}
			$this->_redirect('*/*/viewall');
		}
	}
	
	public function viewAction()
	{
		$this->_initAction()
			->_addBreadcrumb(Mage::helper('profitlossreport')->__('View Report'), Mage::helper('profitlossreport')->__('View Report'));
			
		$this->renderLayout();
	}
	
	/***************************************************************************************
	 * 
	 * 		Attribute Management Actions
	 * 
	 **************************************************************************************/
	
	public function viewattributeAction()
	{
		$this->_initAction()
			->_addBreadcrumb(Mage::helper('profitlossreport')->__('Manage Attributes'), Mage::helper('profitlossreport')->__('Manage Attributes'));
			
		$this->renderLayout();
	}
	
	/*
	 * 	Revenue Items
	 */
	public function editrevenueAction()
	{
		$AttributeId = $this->getRequest()->getParam('id');
		$AttributeModel = Mage::getModel('profitlossreport/attribute')->load($AttributeId);
		
		if ( $AttributeModel->getId() || $AttributeId == 0 )
		{
			Mage::register('attribute_data', $AttributeModel);
			
			$this->_initAction()
			->_addBreadcrumb(Mage::helper('profitlossreport')->__('Edit Revenue'), Mage::helper('profitlossreport')->__('Edit Revenue'));
			
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock('profitlossreport/manager_rev_edit'));
			
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('profitlossreport')->__('The revenue item does not exist'));
			$this->_redirect('*/*/viewattribute');
		}
	}
	
	public function newrevenueAction()
	{
		$this->_forward('editrevenue');
	}
	
	public function saverevenueAction()
	{
		if ( $this->getRequest()->getPost() )
		{
			try {
				$postData = $this->getRequest()->getPost();
				
				$attId = $this->getRequest()->getParam('id');
				$name = $postData['name'];
				if ($postData['parent_id'] != 0)
				{
					$parentId = $postData['parent_id'];
				} else {
					$parentId = 'null';
				}
				
				$resource = Mage::getSingleton('core/resource');
				$read = $resource->getConnection('core_read');
				$write = $resource->getConnection('core_write');
		
				$attributeTable = $resource->getTableName('jtech_pl_attributes');
				
				if( $attId <= 0 )
				{
					// New Item
					// Insert Item
					$write->query('INSERT INTO ' . $attributeTable . ' (name, att_type_id, parent_id) VALUES ("' . $name . '", 2, ' . $parentId . ')'); 
					
					// Look for parent ID and make it a parent
					if ($parentId != 'null')
						$write->query('UPDATE ' . $attributeTable . ' SET is_parent = 1 WHERE att_id = ' . $parentId);
						
				} else {
					//Modify Item
					// get old parent value if existant
					$select = $read->select()
						->from($attributeTable, array('parent_id'))
						->where('att_id = ' . $attId);
					$oldParentId = $read->fetchRow($select);
					$oldParentId = $oldParentId['parent_id'];
					
					// Update name and parent_id
					$write->query('UPDATE ' . $attributeTable . ' SET name = "' . $name . '", parent_id = ' . $parentId . ' WHERE att_id = ' . $attId);
					
					// update parent to become a parent if not already so
					if ($parentId != 'null')
						$write->query('UPDATE ' . $attributeTable . ' SET is_parent = 1 WHERE att_id = ' . $parentId);
					
					// check if old parent still has children and unasign parent status if not so
					if ($oldParentId != null)
					{
						$select = $read->select()
							->from($attributeTable, array('parent_id'))
							->where('parent_id = ' . $oldParentId);
						$isParent = $read->fetchRow($select);
						
						if ($isParent == null)
							$write->query('UPDATE ' . $attributeTable . ' SET is_parent = 0 WHERE att_id = ' . $oldParentId);
					}
				}
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Revenue item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setAttributeData(false);
				
				$this->_redirect('*/*/viewattribute');
								
				return;
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setAttributeData($this->getRequest()->getPost());
				
				$this->_redirect('*/*/editrevenue', array('id' => $this->getRequest()->getParam('id')));
				
				return;
			}
			
			$this->redirect('*/*/viewattribute');
		}
	}
	
	public function deleterevenueAction()
	{
		$currId = $this->getRequest()->getParam('id');
		
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
		$write = $resource->getConnection('core_write');
		
		$attributeTable = $resource->getTableName('jtech_pl_attributes');

		if ($currId > 0)
		{
			try
			{
				// Get the parent ID value
				$select = '
					SELECT parent_id
					FROM ' . $attributeTable . '
					WHERE att_id = ' . $currId . '
				';
				$parentId = $read->fetchRow($select);
				$parentId = $parentId['parent_id'];
				
				$this->cascadeDelete($currId);
				
				// If the Parent ID is not NULL then check for more children
				if (isset($parentId)) {
					$select = '
						SELECT att_id
						FROM ' . $attributeTable . '
						WHERE parent_id = ' . $parentId . '
					';
					$otherChildren = $read->fetchAll($select);
					
					// If there are no other children, make the parent a child
					if (count($otherChildren) < 1) {
						$write->query('UPDATE ' . $attributeTable . ' SET is_parent = 0 WHERE att_id = ' . $parentId);
					}
				}
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The item and any existing sub-items were successfully deleted'));
				$this->_redirect('*/*/viewattribute');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__($e->getMessage()));
				$this->_redirect('*/*/editrevenue', array('id' => $currId));
			}
		}
		
		$this->_redirect('*/*/viewattribute');
	}
	
	/*
	 *	Expense Items 
	 */
	public function editexpenseAction()
	{
		$AttributeId = $this->getRequest()->getParam('id');
		$AttributeModel = Mage::getModel('profitlossreport/attribute')->load($AttributeId);
		
		if ( $AttributeModel->getId() || $AttributeId == 0 )
		{
			Mage::register('attribute_data', $AttributeModel);
			
			$this->_initAction()
			->_addBreadcrumb(Mage::helper('profitlossreport')->__('Edit Expense'), Mage::helper('profitlossreport')->__('Edit Expense'));
			
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock('profitlossreport/manager_exp_edit'));
			
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('profitlossreport')->__('The expense item does not exist'));
			$this->_redirect('*/*/viewattribute');
		}
	}
	
	public function newexpenseAction()
	{
		$this->_forward('editexpense');
	}
	
	public function saveexpenseAction()
	{
		if ( $this->getRequest()->getPost() )
		{
			try {
				$postData = $this->getRequest()->getPost();
				
				$attId = $this->getRequest()->getParam('id');
				$name = $postData['name'];
				if ($postData['parent_id'] != 0)
				{
					$parentId = $postData['parent_id'];
				} else {
					$parentId = 'null';
				}
				
				$resource = Mage::getSingleton('core/resource');
				$read = $resource->getConnection('core_read');
				$write = $resource->getConnection('core_write');
		
				$attributeTable = $resource->getTableName('jtech_pl_attributes');
				
				if( $attId <= 0 )
				{
					// New Item
					// Insert Item
					$write->query('INSERT INTO ' . $attributeTable . ' (name, att_type_id, parent_id) VALUES ("' . $name . '", 3, ' . $parentId . ')'); 
					
					// Look for parent ID and make it a parent
					if ($parentId != 'null')
						$write->query('UPDATE ' . $attributeTable . ' SET is_parent = 1 WHERE att_id = ' . $parentId);
						
				} else {
					//Modify Item
					// get old parent value if existant
					$select = $read->select()
						->from($attributeTable, array('parent_id'))
						->where('att_id = ' . $attId);
					$oldParentId = $read->fetchRow($select);
					$oldParentId = $oldParentId['parent_id'];
					
					// Update name and parent_id
					$write->query('UPDATE ' . $attributeTable . ' SET name = "' . $name . '", parent_id = ' . $parentId . ' WHERE att_id = ' . $attId);
					
					// update parent to become a parent if not already so
					if ($parentId != 'null')
						$write->query('UPDATE ' . $attributeTable . ' SET is_parent = 1 WHERE att_id = ' . $parentId);
					
					// check if old parent still has children and unasign parent status if not so
					if ($oldParentId != null)
					{
						$select = $read->select()
							->from($attributeTable, array('parent_id'))
							->where('parent_id = ' . $oldParentId);
						$isParent = $read->fetchRow($select);
						
						if ($isParent == null)
							$write->query('UPDATE ' . $attributeTable . ' SET is_parent = 0 WHERE att_id = ' . $oldParentId);
					}
				}
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Expense item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setAttributeData(false);
				
				$this->_redirect('*/*/viewattribute');
								
				return;
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setAttributeData($this->getRequest()->getPost());
				
				$this->_redirect('*/*/editexpense', array('id' => $this->getRequest()->getParam('id')));
				
				return;
			}
			
			$this->redirect('*/*/viewattribute');
		}
	}
	
	public function deleteexpenseAction()
	{
		$currId = $this->getRequest()->getParam('id');
		
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
		$write = $resource->getConnection('core_write');
		
		$attributeTable = $resource->getTableName('jtech_pl_attributes');
		
		if ($currId > 0)
		{
			try
			{
				// Get the parent ID value
				$select = '
					SELECT parent_id
					FROM ' . $attributeTable . '
					WHERE att_id = ' . $currId . '
				';
				$parentId = $read->fetchRow($select);
				$parentId = $parentId['parent_id'];
				
				$this->cascadeDelete($currId);
				
				// If the Parent ID is not NULL then check for more children
				if (isset($parentId)) {
					$select = '
						SELECT att_id
						FROM ' . $attributeTable . '
						WHERE parent_id = ' . $parentId . '
					';
					$otherChildren = $read->fetchAll($select);
					
					// If there are no other children, make the parent a child
					if (count($otherChildren) < 1) {
						$write->query('UPDATE ' . $attributeTable . ' SET is_parent = 0 WHERE att_id = ' . $parentId);
					}
				}
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The item and any existing sub-items were successfully deleted'));
				$this->_redirect('*/*/viewattribute');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__($e->getMessage()));
				$this->_redirect('*/*/editexpense', array('id' => $currId));
			}
		}
		
		$this->_redirect('*/*/viewattribute');
	}
	
	
	/***************************************************************************************
	 * 
	 * 		Common Functions
	 * 
	 ***************************************************************************************/
	
	protected function cascadeDelete($id)
	{
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
		$write = $resource->getConnection('core_write');
		
		$attributeTable = $resource->getTableName('jtech_pl_attributes');
		
		// Delete the attribute
		$write->query('DELETE FROM ' . $attributeTable . ' WHERE att_id = ' . $id);
		
		// Delete the attributes children
		$select = $read->select()
			->from($attributeTable, array('att_id'))
			->where('parent_id = ' . $id);
		$attributes = $read->fetchAll($select);
		
    	foreach ($attributes as $attribute)
    	{
    		$this->cascadeDelete($attribute['att_id']);
    	}
    	
    	
	}
}