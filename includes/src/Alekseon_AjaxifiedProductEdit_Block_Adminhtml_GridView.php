<?php /** * @author    Marcin Frymark
 * @email     contact@alekseon.com
 * @company   Alekseon
 * @website   www.alekseon.com
 */
 class Alekseon_AjaxifiedProductEdit_Block_Adminhtml_GridView extends Mage_Adminhtml_Block_Widget_Container
 {
    protected function _prepareLayout()
    {
        $this->_addButton('back', array(
            'label'   => Mage::helper('alekseon_ajaxifiedProductEdit')->__('Back'),
            'onclick' => "if (alertIfDataHasChanged()) { setLocation('{$this->getUrl('adminhtml/catalog_product', array('_current'=>true))}'); } else { return false; }",
            'class'   => 'back'
        ));

        $this->setChild('gridView_grid', $this->getLayout()->createBlock('alekseon_ajaxifiedProductEdit/adminhtml_gridView_grid', 'gridView.grid'));
        return parent::_prepareLayout();
    }
 
    public function isSingleStoreMode()
    {
        if (!Mage::app()->isSingleStoreMode()) {
            return false;
        }
        return true;
    }
    
    public function getGridHtml()
    {
        return $this->getChildHtml('gridView_grid');
    }
    
    public function getStoreId()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return $storeId;
    }
 }