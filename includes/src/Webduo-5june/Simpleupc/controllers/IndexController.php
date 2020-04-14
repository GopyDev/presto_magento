<?php
class Webduo_Simpleupc_IndexController extends  Mage_Adminhtml_Controller_Action {
	function _getConnection($type = 'core_read'){
		return Mage::getSingleton('core/resource')->getConnection($type);
	}	
	
	public function indexAction(){
		$this->loadLayout();
		
		$this->_setActiveMenu('simpleupc_menu/import_products');  
		
		//prepare template
		$block = $this->getLayout()->createBlock('core/template');
        
		//pass data to template
		$block->setData('back', $this->getUrl('simpleupc/index/index'));
		$block->setData('posturl', $this->getUrl('simpleupc/index/post'));
		
		$block->setTemplate('simple_upc/products.phtml');
		
		$this->getLayout()->getBlock('content')->append($block);	
		
        $this->renderLayout();
	
		//generate url with kay and args
		//$this->getUrl('simpleupc/index/index', array('id'=>27)).
	}
	
	public function searchAction(){
		$this->loadLayout();
		
		$this->_setActiveMenu('simpleupc_menu/search_products');  
		
		//prepare template
		$block = $this->getLayout()->createBlock('core/template');
        
		//pass data to template
		$block->setData('back', $this->getUrl('simpleupc/index/search'));
		$block->setData('posturl', $this->getUrl('simpleupc/index/post'));
		
		$block->setTemplate('simple_upc/search_products.phtml');
		
		$this->getLayout()->getBlock('content')->append($block);	
		
        $this->renderLayout();
	
		//generate url with kay and args
		//$this->getUrl('simpleupc/index/index', array('id'=>27)).
	}
	
	public function postAction(){
		$data = $this->getRequest()->getPost();
		
		{//process data
			$upc_codes = array();
			
			switch($data['upc_code_type']){
				case 'single_upc':
					
					$upc_codes[] = $data['single_upc'];
					
					break;
				
				case 'upc_codes':
					//covert into array
					$upccodes = explode('<br />', nl2br($data['upc_codes']));
					foreach($upccodes as $_upccodes){
						$_upccodes = trim($_upccodes);
						if(!empty($_upccodes) || $_upccodes != ''){
							$upc_codes[] = $_upccodes;
						}
					}
					
					break;
					
				case 'import_file':
					
					//csv to handle here
					break;
			}
			$products = array();
			
			if(isset($data['upc_method']) && $data['upc_method']=='search'){
				$products = $this->fetch_products_by_search($data['keyword']);
			}else{
				if(count($upc_codes)>0){
					$products = $this->fetch_products_by_upc($upc_codes);
				}
			}
			
			
			{//page
				$this->loadLayout();
		
				$this->_setActiveMenu('simpleupc_menu/import_products');  
		
				//prepare template
				$block = $this->getLayout()->createBlock('core/template');
        
				//pass data to template
				$block->setData('products', $products);
				//$block->setData('category', $categories);
				$block->setData('posturl', $this->getUrl('simpleupc/index/upload'));
				
				$block->setTemplate('simple_upc/list.phtml');
		
				$this->getLayout()->getBlock('content')->append($block);	
		
        		$this->renderLayout();
			}
		}
	}
	
	public function uploadAction(){
		$data = $this->getRequest()->getPost();
		
		$attributes = array();
		$products = array();
		foreach($data['products'] as $upc => $product){
			$nutrition_fact = Mage::helper('simpleupc')->nutrition_fact_by_upc($upc);
			$nutritions = $nutrition_fact['result'];
			unset($nutritions['units'], $nutritions['upc'], $nutritions['category'], $nutritions['catID']);
			
			//list all nutritions to check if any one is not added as attribute
			foreach($nutritions as $nutrition_attribute => $nutrition){
				$attributes[] = $nutrition_attribute;
			}
			
			$products[$upc] = array_merge($product, $nutritions);
		}
		
		//check and add new attributes
		$this->manage_attributes($attributes);
		
		//csv
		{
			//mandetory headers
			$csv_headers = array('sku', '_store', '_attribute_set', '_type', '_category', '_product_websites', 'brand', 'description', 'image', 'ingredients', 'manufacturer', 'name', 'price', 'short_description', 'size', 'small_image', 'special_price', 'status', 'supplier', 'thumbnail', 'units', 'upc_category', 'visibility', 'weight', 'qty', 'is_in_stock', 'container');
			
			$csv_headers = array_merge($csv_headers, $attributes);
			
			$csv_headers = array_unique($csv_headers);
			
			$csv = array();
			$n = 0;
			foreach($products as $upc => $product){
				$n++;
				$row = "row_$n";
				$row = array();
				
				
				{//download image
					$image = $product['imageURL'];
			
					$file_data = pathinfo($image);
					$file_name = $file_data['filename'].'.'.$file_data['extension'];
			
					$image_path = Mage::getBaseDir('media') . DS . 'import/' . $file_name;
					if(!file_exists($image_path)){
						$this->downloadImage($image, $image_path);
					}
				}
				
				//product attributes
				//update product attribute option
				//$brand_id = $this->set_attribute_option('brand', $product['brand']);
				//$man_id = $this->set_attribute_option('manufacturer', $product['manufacturer']);
				
				foreach($csv_headers as $k => $h){
					if($h == 'sku')					{ $row[$k] = $upc; }
					elseif($h == '_store')				{ $row[$k] = ''; }
					elseif($h == '_attribute_set')		{ $row[$k] = 'upc products'; }
					elseif($h == '_type')				{ $row[$k] = 'simple'; }
					elseif($h == '_category')			{ $row[$k] = $product['category']; }
					elseif($h == '_product_websites')	{ $row[$k] = 'base'; }
					elseif($h == 'brand')				{ $row[$k] = $product['brand']; }
					elseif($h == 'description')			{ $row[$k] = str_replace(',', '', $product['description']); }
					elseif($h == 'image')				{ $row[$k] = '/'.$file_name; }
					elseif($h == 'ingredients')			{ $row[$k] = str_replace(',', '', $product['ingredients']); }
					elseif($h == 'manufacturer')		{ $row[$k] = str_replace(',', '', $product['manufacturer']); }
					elseif($h == 'name')				{ $row[$k] = str_replace(',', '', $product['brand'] . ' ' . $product['upc_category']); }
					elseif($h == 'price')				{ $row[$k] = $product['price']; }
					elseif($h == 'short_description')	{ $row[$k] = str_replace(',', '', $product['description']); }
					elseif($h == 'size')				{ $row[$k] = $product['size']; }
					elseif($h == 'small_image')			{ $row[$k] = '/'.$file_name; }
					elseif($h == 'special_price')		{ $row[$k] = $product['special_price']; }
					elseif($h == 'status')				{ $row[$k] = 1; }
					elseif($h == 'supplier')			{ $row[$k] = ''; }
					elseif($h == 'thumbnail')			{ $row[$k] = '/'.$file_name; }
					elseif($h == 'units')				{ $row[$k] = $product['units']; }
					elseif($h == 'upc_category')		{ $row[$k] = $product['upc_category']; }
					elseif($h == 'visibility')			{ $row[$k] = Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH; }
					elseif($h == 'weight')				{ $row[$k] = $product['weight'] ? $product['weight'] : ''; }
					elseif($h == 'qty')					{ $row[$k] = $product['qty']; }
					elseif($h == 'is_in_stock')			{ $row[$k] = 1; }
					elseif($h == 'container')			{ $row[$k] = $product['container']; }
					elseif($h == 'brand')				{ $row[$k] = $product['brand']; }
					elseif($h == 'manufacturer')		{ $row[$k] = $product['manufacturer']; }
					
					elseif($product[$h]){
						if($h != 'ingredients'){
							$row[$k] = $product[$h];//044700010907,077400108322,041383096013
						}
					}
				}
				$csv[] = $row;
				unset($row);
			}
			
			$file_name = $order_number . '_measurements.csv';

			$f = fopen('php://memory', 'w'); 
		
			fputcsv($f, $csv_headers, ','); 
			foreach($csv as $line) { 
				fputcsv($f, $line, ','); 
			}
			fseek($f, 0);
			
			header('Content-Type: application/csv');
			header('Content-Disposition: attachement; filename="'.$file_name.'"');
			header('Pragma: no-cache');
			fpassthru($f);
		
			//print_r($csv);
		}
	}
	
	//array of attributes
	//this function validate given attributes and add new if not available in system.
	public function manage_attributes($attributes){
		$type = Mage::getModel('eav/entity_type')->loadByCode(Mage_Catalog_Model_Product::ENTITY);
		$available_attributes = Mage::getResourceModel('eav/entity_attribute_collection')->setEntityTypeFilter($type);
		
		$list = array();
		foreach($available_attributes as $available_attribute){
			$list[] = $available_attribute->getAttributeCode();
		}
		
		foreach($attributes as $attribute){
			if(!in_array($attribute, $list)){
				$this->createAttribute($this->slug($attribute), strtoupper(str_replace('_', ' ', $attribute)), 'text');
			}
		}
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
			//echo '<p>Sorry, error occured while trying to save the attribute. Error: '.$code. ' - '.$e->getMessage().'</p>'; 
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
			//echo '<p>Sorry, error occured while trying to save the attribute. Error: '.$e->getMessage().'</p>'; 
		}
	}
	
	public function uploadsingleAction(){
		$data = $this->getRequest()->getPost();
		
		foreach($data['products'] as $upc => $product){
			
			$product_id = Mage::getModel('catalog/product')->getIdBySku($upc);
			if(!$product_id){//product exists
			
				$attributeSetId = 9;

    			//$newproduct = Mage::getModel('catalog/product');
    			$newproduct = new Mage_Catalog_Model_Product();

    			$newproduct->setTypeId('simple');
    			$newproduct->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH); 
    			$newproduct->setStatus(1);
    			$newproduct->setSku($upc);
    			$newproduct->setTaxClassId(0);
    			$newproduct->setWebsiteIDs(array(1)); 
    			$newproduct->setStoreIDs(array(1)); 
    			$newproduct->setStockData(array( 
        						'is_in_stock' => 1, 
        						'qty' => $product['qty']
    						)); 

    			$newproduct->setAttributeSetId($attributeSetId);
    			$newproduct->setName($product['brand'] . ' ' . $product['upc_category']);
    			$newproduct->setCategoryIds(array($product['category'])); // array of categories it will relate to

    			$newproduct->setDescription($product['description']);
    			$newproduct->setShortDescription($product['description']);
    		
				$newproduct->setPrice($product['price']);
				if($product['special_price'] != ''){
					$newproduct->setSpecialPrice($product['special_price']);
				}
			
				//product attributes
				//update product attribute option
				$brand_id = $this->set_attribute_option('brand', $product['brand']);
				$man_id = $this->set_attribute_option('manufacturer', $product['manufacturer']);
				
				//assign to product
				$newproduct->setBrand($brand_id);
				$newproduct->setManufacturer($man_id);
				
				$newproduct->setContainer($product['container']);
				$newproduct->setUpcCategory($product['upc_category']);
				$newproduct->setSize($product['size']);
				$newproduct->setUnits($product['units']);
				$newproduct->setIngredients($product['ingredients']);
				
				{//download image
					$image = $product['imageURL'];
			
					$file_data = pathinfo($image);
					$file_name = $file_data['filename'].'.'.$file_data['extension'];
			
					$image_path = Mage::getBaseDir('media') . DS . 'import/' . $file_name;
					if(!file_exists($image_path)){
						$this->downloadImage($image, $image_path);
					}
					
					$visibility = array('thumbnail', 'small_image', 'image');
					foreach($visibility as $visibile){
						$newproduct->addImageToMediaGallery($image_path, $visibile, false, false);
					}
				}
			
				try {
        			$newproduct->save();
				} catch (Mage_Core_Exception $e) {
        			$e->getMessage();
    			}
			}//if end
		}//foreach end	
		Mage::getSingleton('core/session')->addSuccess('Products uploaded sucessfully.');	
		$this->_redirect('simpleupc/index/index');
	}
	
	public function fetch_products_by_upc($upc_codes){
		
		$products = array();
		
		foreach($upc_codes as $upc){
			if($upc != ''){
				$request = array("method"=>'FetchProductByUPC', "params"=>array("upc"=>$upc));
				$products[] = Mage::helper('simpleupc')->fetch_upc($request);
				
			}
		}
		
		return $products;			
	}
	
	function category_collection(){
		$view = new Webduo_Simpleupc_Block_Html_Topmenu;
		return $this->parse($view->getList());
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
	
	/*public function category_collection(){
		$rootcatId= Mage::app()->getStore()->getRootCategoryId();
		$categories = Mage::getModel('catalog/category')
    							->getCollection()
     							//->addFieldToFilter('parent_id', array('eq'=>$rootcatId))
    							->addAttributeToFilter('level',2)
								->addAttributeToSelect('*');
		return $categories;
	}*/
	
	public function fetch_products_by_search($keyword){
		
		$products = array();
		
		$request = array("method"=>'FetchProducts',
						 "params"=>	array("search"=> $keyword,
										"field"	=> 'description',
										"requirenutrition" => True,
										"matchtype" => 'present',
										"limit"	=>	20,
										"offset" => 0,
										)
						);
		$_products = Mage::helper('simpleupc')->search_upc($request);
		
		return $_products;			
	}
	
	public function set_attribute_option($attribute_name, $option){
	
		$attribute_model = Mage::getModel('eav/entity_attribute');
		$attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;
   
    	$attribute_code = $attribute_model->getIdByCode('catalog_product', $attribute_name);
    	
		$attribute = $attribute_model->load($attribute_code);
		
		$eav_attribute_option_value = Mage::getSingleton('core/resource')->getTableName('eav_attribute_option_value');
		$eav_attribute_option = Mage::getSingleton('core/resource')->getTableName('eav_attribute_option');
		
		$check = $this->_getConnection()->fetchRow("select * from ".$eav_attribute_option_value." where value='".addslashes($option)."'");
		if($check['value']==''){
			$insert = $this->_getConnection('core_write')->query("insert into ".$eav_attribute_option."(attribute_id,sort_order) values ('".$attribute_code."','0')");
				
			$results = $this->_getConnection()->fetchRow("SELECT MAX(option_id) as max FROM ".$eav_attribute_option);
			$max = $results['max'];
		
			$in = $this->_getConnection('core_write')->query("insert into ".$eav_attribute_option_value."(option_id,store_id,value) values ('".$max."','0','".addslashes($option)."')");
			return $max;
		}else{
			return $check['option_id'];
		}
	}
	
	public function upc_auth_code(){
		return Mage::helper('simpleupc')->upc_auth_code();
	}
	
	public function upc_url(){
		return Mage::helper('simpleupc')->upc_url();
	}
	
	function downloadImage($img, $name){
		
		$fullpath = $name;
		
		$ch = curl_init ($img);
    	
		curl_setopt($ch, CURLOPT_HEADER, 0);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
    	$rawdata=curl_exec($ch);
    	curl_close ($ch);
    	
		$fp = fopen($fullpath,'x');
    	fwrite($fp, $rawdata);
    	fclose($fp);
	}
	
	public function slug($string) {
 	   //convert string to lower case
 	   $string = strtolower($string);
 	   //remove unwanted unwanted characters
 	   $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
 	   //remove multiple dashes or whitespaces
 	   $string = preg_replace("/[\s-]+/", " ", $string);
 	   //convert whitespaces and underscore to $replace
 	   $string = preg_replace("/[\s_]/", '_', $string);
	   
 	   return $string;
	}
}	