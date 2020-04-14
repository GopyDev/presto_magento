<?php
class Webduo_Simpleupc_Block_Html_Topmenu extends Mage_Page_Block_Html_Topmenu{

	public function getList(){
		 Mage::dispatchEvent('page_block_html_topmenu_gethtml_before', array(
            'menu' => $this->_menu
        ));
		
		$this->_menu->setOutermostClass('');
        $this->_menu->setChildrenWrapClass('');
		
		$html = $this->_getHtmlList($this->_menu, '');
		return $html;
	}
	
	protected function _getHtmlList(Varien_Data_Tree_Node $menuTree, $childrenWrapClass){
      // Mage::log($menuTree->getChildren(),null,'children.log');
	   return $children = $menuTree->getChildren();
		
    }
	
}
