<?php
class Webduo_Simpleupc_ProductsController extends  Mage_Core_Controller_Front_Action {
	function _getConnection($type = 'core_read'){
		return Mage::getSingleton('core/resource')->getConnection($type);
	}	
	
	//Add Custom Attribute to Customer
	//public function caAction(){
//		$setup = Mage::getModel('customer/entity_setup', 'core_setup');
//
//$setup->addAttribute('customer', 'accept_substitutes', array(
//    'type' => 'int',
//    'input'                => 'select',
//    'label' => 'Accept Substitutes',
//    'global' => 1,
//    'visible' => 1,
//    'required' => 0,
//    'user_defined' => 0,
//    'default' => '',
//    'visible_on_front' => 0,
//    'source'               => 'eav/entity_attribute_source_boolean',
//));
//		/*$customer = Mage::getResourceModel('eav/entity_setup')->getAllAttributeSetIds();
//		echo count($customer);*/
//		
//		///*Mage::getModel('eav/entity_setup')->addAttribute($customer, 'accept_substitutes', array(
////						'type' => 'text',				/* input type */
////						'label' => 'Accept Substitutes',	/* Label for the user to read */
////						'input' => 'text',				/* input type */
////						'visible' => TRUE,				/* users can see it */
////						'required' => FALSE,			/* is it required, self-explanatory */
////						'default_value' => 'default',	/* default value */
////						'adminhtml_only' => '1'			/* use in admin html only */
////					));*/
//	}	
	
	public function cAction(){
		$this->loadLayout();
		echo $this->getLayout()->getBlock("cart_sidebar")->toHtml();
	}
	
	public function upc_auth_code(){
		return Mage::helper('simpleupc')->upc_auth_code();
	}
	
	public function upc_url(){
		return Mage::helper('simpleupc')->upc_url();
	}
	
	public function manage_attributesAction($attributes=''){
		$type = Mage::getModel('eav/entity_type')->loadByCode(Mage_Catalog_Model_Product::ENTITY);
		$attributes = Mage::getResourceModel('eav/entity_attribute_collection')->setEntityTypeFilter($type);
		
		$list = array();
		foreach($attributes as $attribute){
			$list[] = $attribute->getAttributeCode();
		}
		
		$this->createAttribute(strtolower("ShirtType"), "ShirtType", "text");
		
		//print_r($list);
		
		/*$as_model = Mage::getModel('eav/entity_setup','core_setup');
		$attributeId = $as_model->getAttribute('catalog_product', 'calories_per_serving');
		print_r($attributeId);
		echo $attributeSetId = $as_model->getAttributeSetId('catalog_product', 'upc products');
		
		$attributeGroupId = $as_model->getAttributeGroup('catalog_product', $attributeSetId, 'Nutritions');
		echo '<br />';
		print_r($attributeGroupId);*/
	}
	
	private function createAttribute($code, $label, $attribute_type){
    	$_attribute_data = array(
        	'attribute_code' => $code,
        	'is_global' => '1',
        	'frontend_input' => $attribute_type, //'boolean',
        	'default_value_text' => '',
        	'default_value_yesno' => '0',
        	'default_value_date' => '',
        	'default_value_textarea' => '',
        	'is_unique' => '0',
        	'is_required' => '0',
        	'apply_to' => array('simple', 'configurable', 'grouped'), //array('grouped')
        	'is_configurable' => '0',
        	'is_searchable' => '0',
        	'is_visible_in_advanced_search' => '0',
        	'is_comparable' => '0',
        	'is_used_for_price_rules' => '0',
        	'is_wysiwyg_enabled' => '0',
        	'is_html_allowed_on_front' => '0',
        	'is_visible_on_front' => '1',
        	'used_in_product_listing' => '0',
        	'used_for_sort_by' => '0',
        	'frontend_label' => array($label)
    	);
    	$model = Mage::getModel('catalog/resource_eav_attribute');
    	if (!isset($_attribute_data['is_configurable'])) {
    	    $_attribute_data['is_configurable'] = 0;
    	}
    	if (!isset($_attribute_data['is_filterable'])) {
        	$_attribute_data['is_filterable'] = 0;
    	}
 	   	if (!isset($_attribute_data['is_filterable_in_search'])) {
     	   $_attribute_data['is_filterable_in_search'] = 0;
    	}
   	 	if (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0) {
        	$_attribute_data['backend_type'] = $model->getBackendTypeByInput($_attribute_data['frontend_input']);
    	}
    	//$defaultValueField = $model->getDefaultValueByInput($_attribute_data['frontend_input']);
    	//if($defaultValueField) {
        //	$_attribute_data['default_value'] = $this->getRequest()->getParam($defaultValueField);
    	//}
    	$model->addData($_attribute_data);
    	$model->setEntityTypeId(Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId());
    	$model->setIsUserDefined(1);
    	try{
        	$model->save();
    	}catch(Exception $e){
			echo '<p>Sorry, error occured while trying to save the attribute. Error: '.$e->getMessage().'</p>'; 
		}
		
		//assign to attribute set
		
		$as_model = Mage::getModel('eav/entity_setup','core_setup');
		$attribute_data = $as_model->getAttribute('catalog_product', $code);
		$attributeId = $attribute_data['attribute_id'];
		
		$attributeSetId = $as_model->getAttributeSetId('catalog_product', 'upc products');
		
		//Get attribute group info
		$attribute_group = $as_model->getAttributeGroup('catalog_product', $attributeSetId, 'Nutritions');
		$attributeGroupId = $attribute_group['attribute_group_id'];
		//add attribute to a set
		try{
			$as_model->addAttributeToSet('catalog_product', $attributeSetId, $attributeGroupId, $attributeId);
		}catch(Exception $e){
			echo '<p>Sorry, error occured while trying to save the attribute. Error: '.$e->getMessage().'</p>'; 
		}
	}

	function menuAction(){
		$view = new Webduo_Simpleupc_Block_Html_Topmenu;
		$menu = $this->parse($view->getList());
		$o = new stdClass;
		$o->menu = $menu;
		echo json_encode($o);	
	}
	
	function parse($menu, $parent=true, $level=0){
		$html = '';
		if($parent){
			//$html .= '<select class="cat_menu">';
		}
		
		$n = 0;
		foreach($menu as $item){
			$line = '';
			for($i=0; $i<$level; $i++){
				$line .= ' - ';
			}
			$html .= '<option value="'.$item->getName().'">'.$line.$item->getName().'</option>';
			if($item->hasChildren()){
				$html .= $this->parse($item->getChildren(), false, $level+1);
			}
		}
		if($parent){
			//$html .= '</select>';
		}
		return $html;
	}

	function ggAction(){
	
		$attribute_name = 'brand';
	
		$attribute_model = Mage::getModel('eav/entity_attribute');
		$attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;
   
    	$attribute_code = $attribute_model->getIdByCode('catalog_product', $attribute_name);
    	
		$attribute = $attribute_model->load($attribute_code);
		
		$check = $this->_getConnection()->fetchRow("select * from eav_attribute_option_value where value='new brands'");
		if($check['value']==''){
			$insert = $this->_getConnection('core_write')->query("insert into eav_attribute_option(attribute_id,sort_order) values ('".$attribute_code."','0')");
				
			$results = $this->_getConnection()->fetchRow("SELECT MAX(option_id) as max FROM eav_attribute_option ");
			$max = $results['max'];
		
			$in = $this->_getConnection('core_write')->query("insert into eav_attribute_option_value(option_id,store_id,value) values ('".$max."','0','new brands')");
			var_dump($in);
		}
		
		//$attribute_table = $attribute_options_model->setAttribute($attribute);
    	//$options = $attribute_options_model->getAllOptions(false);
		
		//print_r($options);
		
		
	}
	
	public function catAction(){
		$rootcatId= Mage::app()->getStore()->getRootCategoryId();
		$categories = Mage::getModel('catalog/category')
    							->getCollection()
     							->addFieldToFilter('parent_id', array('eq'=>$rootcatId))
    							//->addFieldToFilter('is_active', array('neq'=>'1'))
    							->addAttributeToSelect('*');
		$category_list = '<select name="products[4545][category]" class="required-select">';
		foreach($categories as $category){
			$category_list .= '<option value="'.$category->getName().'">'.$category->getName().'</option>';		
		}
		echo $category_list .= '</select>';						
	}
	
	function  get_categories($categories) {
    	$array= '<ul>';
    	foreach($categories as $category) {
        	$cat = Mage::getModel('catalog/category')->load($category->getId());
        	//$count = $cat->getProductCount();
        	$array .= '<li>'.
        			'<a href="' . Mage::getUrl($cat->getUrlPath()). '">' . $category->getName() ."</a>\n";
        				if($category->hasChildren()) {
            				$children = Mage::getModel('catalog/category')->getCategories($category->getId());
             				$array .=  $this->get_categories($children);
    	    			}
       			$array .= '</li>';
    		}
    	return  $array . '</ul>';
	}
	
	public function fetch_products_by_upcAction(){
		
		$upcs = array('077400108322', '044500976489', '044500976496', '044500976458', '044700019238', '025317128612', '037600398855', '037600188081', '025317856003', '071871546670', '044700010907', '025317579001', '025317581004', '077400127330', '077400108339', '027815179905');
		
		$request = array("method"=>'FetchProductByUPC',
						"params"=>array("upc"=>'077400108322', '044500976489', '044500976496')//array of upc codesimplode(',', $upcs)
					);
		$products = $this->fetch_upc($request);
		
		print_r($products);			
	}
	
	public function goAction(){
		 //error_reporting(E_ALL | E_STRICT);
$mageFilename = 'app/Mage.php';
require_once $mageFilename;
Mage::setIsDeveloperMode(true);
umask(0);
Mage::app();
Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID)); 


		$request = array("method"=>'FetchImageByUPC',
						"params"=>array("upc"=>'077400108322'));
		$data = $this->fetch_upc($request);
		//print_r($data);
		
		$image = $data['result']['imageURL'];
		
		$file_data = pathinfo($image);
		$file_name = $file_data['filename'].'.'.$file_data['extension'];
		
		$path = Mage::getBaseDir('media') . DS . 'import/' . $file_name;
		
		if(!file_exists($path)){
			$this->downloadImage($data['result']['imageURL'], $path);
		}
		$pro = Mage::getModel('catalog/product')->load(7);
		$pro->addImageToMediaGallery($path, 'image', false);
		$pro->save();
		//file_put_contents($file_name, file_get_contents($data['result']['imageURL']));
	}
	
	function downloadImage($img, $name){
		
		$fullpath = $name;
		
		$ch = curl_init ($img);
    	
		curl_setopt($ch, CURLOPT_HEADER, 0);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
    	$rawdata=curl_exec($ch);
    	curl_close ($ch);
    	//if(file_exists($fullpath)){
    	//    unlink($fullpath);
    	//}
    	$fp = fopen($fullpath,'x');
    	fwrite($fp, $rawdata);
    	fclose($fp);
	}
	
	//fetching method
	function fetch_upc($request){
		{//prepare request
			$auth_code = $this->upc_auth_code();
			$upc_url = $this->upc_url();
			
			//add auth code in request array
			$request['auth'] = $auth_code;
			
			$json = json_encode($request);
	
			$ch = curl_init($upc_url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/json'));
			curl_setopt($ch, CURLOPT_POSTFIELDS, "$json");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch);
			curl_close($ch);

			return $output = json_decode($output, true);
		}
	}
	
	public function indexedAction(){
		//error_reporting(E_ALL);
		$data = $this->getRequest()->getParams();
		$cat_id = $data['cat_id'];
		
		{//paging
			$page = trim($data['page']);
			$page = ($page == '') ? 1 : $page;
			$number_of_products = 9;
		}
	
		{//filters
			$_filters = $data['filters'];
			
			if(stristr($_filters, '|')){//if multiple filters than explode
				$_filters = explode('|', rtrim($_filters, '|'));
				foreach($_filters as $filters){
					if(stristr($filters, 'cat')){
						$_cat_id = explode('=', $filters);
						$cat_id = $_cat_id[1];
					}
				}
			}else{//if only single filter
				if(strlen($_filters)>0){
					$__filters = array();
					if(stristr($_filters, 'cat')){
						$_cat_id = explode('=', $_filters);
						$cat_id = $_cat_id[1];
					}
					$__filters[] = $_filters;
					$_filters = $__filters;
				}
			}
		}
		
		$category = Mage::getModel('catalog/category')->load($cat_id);
		$filters = array();
		
		//price filter does not work with grouped products
		$price_filter = false;
		$price_filters = array();
		
		foreach($_filters as $attributes){
			list($attribute, $value) = explode('=', $attributes);
			if($attribute == 'price'){
				$price_filter = true;
				$price = explode('-', $value);
				if($price[0] != ''){
					$price_filters[] = $price[0];
				}
				if($price[1] != ''){
					$price_filters[] = $price[1];
				}
			}elseif($attribute != 'cat'){
				if(!array_key_exists($attribute, $filters)){
					$filters[$attribute] = array();
				}
				$filters[$attribute][] = $value;
			}
		}
		array_multisort($price_filters);

		$collection = Mage::getModel('catalog/product')
						->getCollection()
						->addCategoryFilter($category)
						->addAttributeToSelect('*')
						//->addAttributeToFilter($filters)
						//->addAttributeToFilter(array(array('attribute'=>'color', array('in'=>array(18)))))
						//->addAttributeToFilter(array(array('attribute'=>'price', array('gt'=>20))))
						->addAttributeToSort('position', 'asc')
						//->addAttributeToFilter(array(array('attribute'=>'color', array('eq'=>6))))

						->setStoreId(Mage::app()->getStore()->getStoreId())
                        ->addStoreFilter(Mage::app()->getStore()->getStoreId())
						->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
						;
		
		if(count($filters) > 0){
			foreach($filters as $attribute => $filter){
				$collection->addAttributeToFilter(array( array('attribute'=>$attribute, array('in'=>$filter)) ));
			}
		}
		
		//price filter
		if(is_array($price_filters)){
			if(isset($price_filters[0]) && $price_filters[0]!=''){
				$collection->addFieldToFilter('price', array('gteq' => $price_filters[0]));
   			}
			if(isset($price_filters[count($price_filters)-1]) && ($price_filters[count($price_filters)-1]!='') && (count($price_filters)>1)){
				$collection->addFieldToFilter('price', array('lteq' => $price_filters[count($price_filters)-1]));
   			}
		}
		
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
		$collection->load();
		
		$collection->getSelect()->joinLeft(array('stock' => 'cataloginventory_stock_status'), "e.entity_id = stock.product_id", array('stock.stock_status'))->where('stock.stock_status = 1');
		
		/*echo '<br />';
		foreach($collection as $col){
			echo $col->getPrice();
			echo ' - ';
			echo $col->getSize();
			echo ' - ';
			echo $col->getColor();
			echo '<br />';
		}
		exit;*/
		{//list all final the skus
			$_skus = array();
			foreach($collection as $item){
				
				if($item->getTypeId() == 'simple'){
					$_skus;
					$_rel = $this->_getConnection()->fetchAll('select * from catalog_product_relation where child_id = '.$item->getId());
					if(count($_rel)>0){
						foreach($_rel as $rel){
							$_skus[] = $rel['parent_id'];
						}
					}else{
						$_skus[] = $item->getId();
					}
				}else{
					$_skus[] = $item->getId();
				}
			}
		}
		
		$collection = Mage::getModel('catalog/product')->getCollection()
						->addAttributeToSelect('*')
						->addAttributeToFilter('entity_id', array('in'=>$_skus));
		
		//if price filter than apply visibility and paging
		//if($price_filter){
		$collection = $collection->addAttributeToFilter('visibility', 4)->setPage($page, $number_of_products)->load();
		//}else{
			//$collection = $collection->load();
		//}
		
		/*echo '<br />';
		foreach($collection as $col){
			echo $col->getPrice();
			echo ' - ';
			echo $col->getSize();
			echo ' - ';
			echo $col->getColor();
			echo '<br />';
		}
		exit;*/
		
		$this->loadLayout();
		$html = $this->getLayout()
				->createBlock("core/template")
				->setData('loaded_product_collection', $collection)
				->setData('request_type', 'ajax')//->getToolbarHtml('')
				->setData('category', $category)
				->setData('column_count', 3)
				->setData('mode', 'grid')
				->setTemplate("catfilter/list.phtml")
				->toHtml();
		
		$return = new stdClass;
		$return->products_count = count($collection);
		$return->do_continue = ($number_of_products==$return->products_count) ? true : false;
		$return->html = $html;
		
		die(json_encode($return));
	
	}
	
	public function entityAction(){
		$setup = Mage::getModel('customer/entity_setup');
		$setup->addAttribute('customer', 'company_name', array(
    			'type' => 'varchar',
    			'input' => 'text',
    			'label' => 'Company',
    			'global' => 1,
    			'visible' => 1,

    			'required' => 1,

    			'user_defined' => 1,

    			'default' => '0',

    			'visible_on_front' => 1,

        ));

		if (version_compare(Mage::getVersion(), '1.6.0', '<=')){

	      $customer = Mage::getModel('customer/customer');

	      $attrSetId = $customer->getResource()->getEntityType()->getDefaultAttributeSetId();

	      $setup->addAttributeToSet('customer', $attrSetId, 'General', 'company_name');

		}

		if (version_compare(Mage::getVersion(), '1.4.2', '>=')){

		    Mage::getSingleton('eav/config')

    		->getAttribute('customer', 'company_name')

    		->setData('used_in_forms', array('adminhtml_customer','customer_account_create','customer_account_edit','checkout_register'))

    		->save();

		}

	}
	
	public function deleteAction(){
		$ids = $this->getRequest()->getParam('ids');
		
		if(stristr($ids, '_')){
			$_ids = explode('_', $ids);
		}else{
			$_ids[] = $ids;
		}

		if(count($_ids) > 0){
			foreach($_ids as $id){
				try {
        	        $cart = Mage::getModel('checkout/cart');
	    			$cart->init();
					$cart->removeItem($id)->save();
        	    	unset($cart);
				} catch (Exception $e) {
        	        echo 'asdf';
        	        exit;
        	    }
        	}
        }
		$this->_redirect('checkout/cart');
	}

}	