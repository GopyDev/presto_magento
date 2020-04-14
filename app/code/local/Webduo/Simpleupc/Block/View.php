<?php

class Webduo_Quickview_Block_View extends Mage_Core_Block_Template {
    //protected $_position = 'CONTENT_TOP';
    //protected $_isActive = 1;
    //protected $_banner = array();

	public function _prepareLayout(){
		return parent::_prepareLayout();
    }	
	
	public function getId(){
		return $product_id = $this->getRequest()->getPost('id');
	}
	
	public function menuAction(){
		$view = new Mage_Page_Block_Html_Topmenu;
		echo $this->parse($view->getList());

		//foreach($view->getList() as $list){
		//	echo $list->getName();
		//}
	}
	
	function parse($menu, $parent=true, $level=0){
		$html = '';
		if($parent){
			$html .= '<select>';
		}
		
		$n = 0;
		foreach($menu as $item){
			$line = '';
			for($i=0; $i<$level; $i++){
				$line .= ' - ';
			}
			$html .= '<option>'.$line.$item->getName().'</option>';
			if($item->hasChildren()){
				$html .= $this->parse($item->getChildren(), false, $level+1);
			}
		}
		if($parent){
			$html .= '</select>';
		}
		return $html;
	}
	
}