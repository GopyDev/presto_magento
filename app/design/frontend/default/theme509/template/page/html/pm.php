<script type="text/javascript" language="javascript">
function donamesave(id)
{
	  noteb = jQuery('#namee-'+id).val();
	  jQuery.ajax({
           url: "<?php echo Mage::getBaseUrl(); ?>ajaxnamesave.php?rand="+Math.random()+"&note="+noteb+"&id="+id,
           context: document.body
			}).done(function(data) {
			 jQuery('#puddiv-'+id).html('Product name has been updated successfully');
		 });
}
function dopricesave(id)
{
	      noteb = jQuery('#pricee-'+id).val();
	 	  jQuery.ajax({
           url: "<?php echo Mage::getBaseUrl(); ?>ajaxpricesave.php?rand="+Math.random()+"&note="+noteb+"&id="+id,
           context: document.body
			}).done(function(data) {
			 jQuery('#puddiv-'+id).html('Product price has been updated successfully');
		 });
}
function dostatussave(id)
{
	  noteb = jQuery('#statuse-'+id).val();
	  jQuery.ajax({
           url: "<?php echo Mage::getBaseUrl(); ?>ajaxstatussave.php?rand="+Math.random()+"&note="+noteb+"&id="+id,
           context: document.body
			}).done(function(data) {
			 jQuery('#puddiv-'+id).html('Product status has been updated successfully');
		 });
}
function dotypechange(id)
{
	  noteb = jQuery('#ptype-'+id).val();
	  jQuery.ajax({
           url: "<?php echo Mage::getBaseUrl(); ?>ajaxtypesave.php?rand="+Math.random()+"&note="+noteb+"&id="+id,
           context: document.body
			}).done(function(data) {
			 jQuery('#puddiv-'+id).html('Product type has been updated successfully');
		 });
}
function dolocationchange(id)
{
	  noteb = jQuery('#plocation-'+id).val();
	  jQuery.ajax({
           url: "<?php echo Mage::getBaseUrl(); ?>ajaxlocationsave.php?rand="+Math.random()+"&note="+noteb+"&id="+id,
           context: document.body
			}).done(function(data) {
			 jQuery('#puddiv-'+id).html('Product location has been updated successfully');
		 });
}
function doorderchange(id)
{
	  noteb = jQuery('#porder-'+id).val();
	  jQuery.ajax({
           url: "<?php echo Mage::getBaseUrl(); ?>ajaxordersave.php?rand="+Math.random()+"&note="+noteb+"&id="+id,
           context: document.body
			}).done(function(data) {
			 jQuery('#puddiv-'+id).html('Product order has been updated successfully');
		 });
}
function opentdiv(id)
{
   jQuery('#t'+id).toggle();
}
</script>
<?php
if(isset($_GET["gt"]) & $_GET["gt"]=='pm' ) {

    if(isset($_GET["usku"]))
	{
	   $_POST["stype"]="database";
	   $_POST["sku"]=$_GET["usku"];
	}

	if(isset($_GET["back"]))
			{
	?>
              <script type="text/javascript" >
				jQuery(document).ready(function() {
				   jQuery('html, body').animate({
					scrollTop: jQuery('#tgb-<?php echo $_GET["eid"]; ?>').offset().top
				}, 2000);

				});
				</script>
    <?php
			}

	     if(isset($_GET["update"]) && $_GET["update"]==1)
		 {

			 $string="";
			 for($p=0;$p<=$_POST["total"];$p++)
			 {
			     if(isset($_POST["chk-".$p]))
				 {
				    $string.=$_POST["chk-".$p].",";
				 }
			 }
			   $_newProduct = Mage::getModel('catalog/product')->load($_POST["poid"]);
			   $string = rtrim($string,',');
			   $_newProduct->setName($_POST["name"]);
			   $_newProduct->setPrice($_POST["price"]);
			   $_newProduct->setSize($_POST["size"]);
			   $_newProduct->setUnits($_POST["units"]);
			   $categories=explode(",",$string);
               $_newProduct->setCategoryIds($categories);
			   $_newProduct->setProtype($_POST["protype"]);
			   $_newProduct->setProlocation($_POST["prolocation"]);
			   $_newProduct->setProorder($_POST["proorder"]);
			   $_newProduct->setStatus($_POST["status"]);
               $_newProduct->setTaxClassId($_POST["tax_class_id"]);
			   $_newProduct->save();

			   Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
			   $_newProduct = Mage::getModel('catalog/product')->load($_POST["poid"]);
			   $string = rtrim($string,',');
			   $_newProduct->setName($_POST["name"]);
			   $_newProduct->setPrice($_POST["price"]);
			   $_newProduct->setSize($_POST["size"]);
			   $_newProduct->setUnits($_POST["units"]);
			   $categories=explode(",",$string);
               $_newProduct->setCategoryIds($categories);
			   $_newProduct->setProtype($_POST["protype"]);
			   $_newProduct->setProlocation($_POST["prolocation"]);
			   $_newProduct->setProorder($_POST["proorder"]);
			   $_newProduct->setStatus($_POST["status"]);
               $_newProduct->setTaxClassId($_POST["tax_class_id"]);
			   $_newProduct->save();
			  $done=1;

		 }

		  if(isset($_GET["entity_id"]) && $_GET["entity_id"]>=1)
		  {
		       $_newProduct = Mage::getModel('catalog/product')->load($_GET["entity_id"]);
			   $_newProduct->setName($_GET["namee-".$_GET["entity_id"]]);
			   $_newProduct->setPrice($_GET["pricee-".$_GET["entity_id"]]);
			   $_newProduct->setStatus($_GET["statuse-".$_GET["entity_id"]]);
			   $_newProduct->setProtype($_GET["ptype-".$_GET["entity_id"]]);
			   $_newProduct->setProlocation($_GET["plocation-".$_GET["entity_id"]]);
			   $_newProduct->setProorder($_GET["porder-".$_GET["entity_id"]]);

			   $_newProduct->save();

			   Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
			   $_newProduct = Mage::getModel('catalog/product')->load($_GET["entity_id"]);
			   $_newProduct->setName($_GET["namee-".$_GET["entity_id"]]);
			   $_newProduct->setPrice($_GET["pricee-".$_GET["entity_id"]]);
			   $_newProduct->setStatus($_GET["statuse-".$_GET["entity_id"]]);
			   $_newProduct->setProtype($_GET["ptype-".$_GET["entity_id"]]);
			   $_newProduct->setProlocation($_GET["plocation-".$_GET["entity_id"]]);
			   $_newProduct->setProorder($_GET["porder-".$_GET["entity_id"]]);
			   $_newProduct->save();

			   $_POST["stype"]="sitesearch";
			   $_POST["sku"]=$_GET["sku"];
			   $_POST["desc"]=$_GET["desc"];
			   $_POST["proorder"]=$_GET["proorder"];
			   $_POST["prolocation"]=$_GET["prolocation"];
			   $_POST["protype"]=$_GET["protype"];
			   ?>
               <script type="text/javascript" >
				jQuery(document).ready(function() {

				if(jQuery('#tgb2-<?php echo $_GET["k"]; ?>').length)
				{
					   jQuery('html, body').animate({
						scrollTop: jQuery('#tgb2-<?php echo $_GET["k"]; ?>').offset().top
					}, 2000);
				}

				});
				</script>
               <?php
			   }
	if(isset($_GET["upc"]))
	{
	   $_POST["sku"] = $_GET["upc"];
	   $_newProduct= Mage::getModel('catalog/product')->load($_GET["ppid"]);
	   $catIds=$_newProduct->getCategoryIds();
	   $storeId=1;
	   $protype = Mage::getResourceModel('catalog/product')->getAttributeRawValue($_newProduct->getId(),'protype',$storeId);
	   $prolocation = Mage::getResourceModel('catalog/product')->getAttributeRawValue($_newProduct->getId(),'prolocation',$storeId);
	   $proorder = Mage::getResourceModel('catalog/product')->getAttributeRawValue($_newProduct->getId(),'proorder',$storeId);
    ?>

	<script type="text/javascript" >
	jQuery(document).ready(function() {
			  jQuery('#protype').val(<?php echo $protype; ?>);
			  jQuery('#prolocation').val(<?php echo $prolocation; ?>);
			  jQuery('#proorder').val(<?php echo $proorder; ?>);
			  jQuery('#status').val(<?php echo $_newProduct->getStatus(); ?>);
			  jQuery('#tax_class_id').val(<?php echo $_newProduct->getTaxClassId(); ?>);
    });
	</script>
    <?php
	      if($done==1)
		  {
	?>
		     <div style="background:green;padding:15px;color:#fff;margin-bottom:15px;"> Product has been updated successfully</div>
	<?php
		  }
	?>
	<form method="POST" action="<?php echo Mage::getBaseUrl(); ?>?gt=pm&update=1&ppid=<?php echo $_GET["ppid"]; ?>&sku=<?php echo $_GET["sku"]; ?>&desc=<?php echo $_GET["desc"]; ?>&proorder=<?php echo $_GET["proorder"]; ?>&prolocation=<?php echo $_GET["prolocation"]; ?>&protype=<?php echo $_GET["protype"]; ?>&upc=<?php echo $_GET["upc"]; ?>">

	<div style="margin-top:10px;margin-bottom:10px;"><a href="<?php echo Mage::getBaseUrl(); ?>?gt=pm&back=1&sku=<?php echo $_GET["sku"]; ?>&desc=<?php echo $_GET["desc"]; ?>&proorder=<?php echo $_GET["proorder"]; ?>&prolocation=<?php echo $_GET["prolocation"]; ?>&protype=<?php echo $_GET["protype"]; ?>&eid=<?php echo $_GET["ppid"]; ?>"> Go back to search result. </a></div>

	<table border="0" style="border:none !important;" cellpadding="5" cellspacing="5" >

	<tr>
	<td>Name :</td>
	<td>
	<textarea style="border:1px solid #000;width:220px;" name="name" ><?php echo $_newProduct->getName(); ?></textarea>
	</td>
	</tr>

	<tr>
	<td>Status :</td>
	<td>
	<select name="status" id="status" style="border:1px solid #000">
	<option value="1" >Enabled</option>
	<option value="2" >Disabled</option>
	</select>
	</td>
	</tr>

	<tr>
	<td>Tax Class :</td>
	<td>
	<select name="tax_class_id" id="tax_class_id" style="border:1px solid #000">
	<option value="2" >Taxable Goods</option>
	<option value="0" >None</option>
	</select>
	</td>
	</tr>


	<input type="hidden" name="poid" value="<?php echo $_newProduct->getId(); ?>" />

	<tr>
	<td>Size :</td>
	<td><input type="text" name="size" id="size" value="<?php echo $_newProduct->getSize(); ?>" ></td>
	</tr>

	<tr>
	<td>Units :</td>
	<td><input type="text" name="units" id="units" value="<?php echo $_newProduct->getUnits(); ?>" ></td>
	</tr>

	<tr>
	<td>Price :</td>
	<td><input type="text" name="price" id="price" value="<?php echo $_newProduct->getPrice(); ?>" ></td>
	</tr>

	<tr>
	<td>Type :</td>
	<td><select id="protype" name="protype" class="select" style="border:1px solid #000">
	    <option value=""></option>
		<option value="141">BAKERY</option>
		<option value="140">COLD</option>
		<option value="146">CUT MEAT</option>
		<option value="144">Deli</option>
		<option value="143">DRY</option>
		<option value="142">FROZEN</option>
		<option value="145">SF</option>
        </select></td>
	</tr>

	<tr>
	<td>Location :</td>
	<td>
	<select id="prolocation" name="prolocation" class="select" style="border:1px solid #000" >
	<option value=""></option>
	<option value="338">0</option>
	<option value="342">1</option>
	<option value="341">2</option>
	<option value="340">3</option>
	<option value="325">3.5FM: Meat &amp; Seafood/Chicken</option>
	<option value="339">4</option>
	<option value="337">5</option>
	<option value="336">6</option>
	<option value="335">7</option>
	<option value="329">8</option>
	<option value="328">9</option>
	<option value="334">Bakery</option>
	<option value="327">Cut Meat</option>
	<option value="333">Dairy</option>
	<option value="331">Deli</option>
	<option value="432">F</option>
	<option value="330">Packed Meat</option>
	<option value="332">Produce</option>
	<option value="431">R</option>
	<option value="326">Seafood</option>
	</select></td>
	</tr>

	<tr>
	<td> Order :</td>
	<td><select id="proorder" name="proorder" class="select" style="border:1px solid #000" >
	<option value=""></option>
	<option value="355">0.5</option>
	<option value="360">1</option>
	<option value="359">2</option>
	<option value="363">3</option><option value="358" >4</option>
	<option value="356">5</option><option value="362">6</option>
	<option value="364">7</option>
	<option value="361">8</option>
	<option value="357">9</option>
	<option value="365">Bakery</option>
	</select></td>
	</tr>
	<?php
	function getCategoriesTreeView() {
		// Get category collection
		$categories = Mage::getModel('catalog/category')
			->getCollection()
			->addAttributeToSelect('name')
			->addAttributeToSort('path', 'asc')
			->addFieldToFilter('is_active', array('eq'=>'1'))
			->load()
			->toArray();
		// Arrange categories in required array
		$categoryList = array();
		foreach ($categories as $catId => $category) {
			if (isset($category['name'])) {
				$categoryList[] = array(
					'label' => $category['name'],
					'level'  =>$category['level'],
					'value' => $catId
				);
			}
		}
		return $categoryList;
	}
	?>
	<style>
	#tree1 li.level2
	{
	   margin-left:25px;
	   color:green;
	}
	#tree1 li.level3
	{
	  margin-left:50px;
	  color:red;
	}

	#tree1 li.level4
	{
	  margin-left:50px;
	  color:black;
	}

	#tree1 input
	{
	   width:20px;
	   height:30px;
	}

	#tree1 li
	{
	   margin-bottm:13px;
	}

	</style>
	<script type="text/javascript">
	function showd(id)
	{
	   jQuery("#a-"+id).toggle();
	}

	function showdb(id,sid)
	{
	   // jQuery("#beee"+id+sid).show();
	   jQuery("#beee"+id+sid).toggle();
	}

	</script>
	<tr>
	<td colspan="2" > Categories :
   <?php
    $cats = $_newProduct->getCategoryIds();
	$greengb=count($cats);
	$bbg=0;
foreach ($cats as $category_id) {
    $_cat = Mage::getModel('catalog/category')->load($category_id) ;
    echo $_cat->getName();
	if($greengb-1>$bbg)
	{
	   echo "&nbsp;,&nbsp;";
	}

	$bbg=$bbg+1;
}
   ?>
    </td>
	</tr>
	<tr>
	<td colspan="2" >
	<?php $_helper = Mage::helper('catalog/category') ?>
<?php $_categories = $_helper->getStoreCategories() ?>
<?php $currentCategory = Mage::registry('current_category') ?>
<?php if (count($_categories) > 0): ?>
<div id="tabs-1">
    <ul id="tree1">
        <?php $qt=0; $new=0; foreach($_categories as $_category):
		$qt++; $new++; ?>
            <li class="level1" >
                    <input type="checkbox" name="chk-<?php echo $qt; ?>" value="<?php echo $_category->getId() ?>" <?php if(in_array($_category->getId(), $catIds)) { echo "checked"; } ?> > <a href="javascript:void(0);" style="color:#000;text-decoration:underline;" onClick="showd(<?php echo $new; ?>);" > <label><?php echo $_category->getName() ?></label> </a>
                <?php $_category = Mage::getModel('catalog/category')->load($_category->getId()) ?>
                <?php $_subcategories = $_category->getChildrenCategories() ?>
                <?php if (count($_subcategories) > 0):
				$new2=0;
				?>
                    <ul id="a-<?php echo $new; ?>" style="display:none;" >
                        <?php foreach($_subcategories as $_subcategory):
						   $qt++;
						   $new2++;
						 ?>
                            <li class="level2" >
                                <input type="checkbox" name="chk-<?php echo $qt; ?>" value="<?php echo $_subcategory->getId() ?>" <?php if(in_array($_subcategory->getId(), $catIds)) { echo "checked"; } ?> > <a href="javascript:void(0);" style="color:green;text-decoration:underline;" onClick="showdb(<?php echo $new2; ?>,<?php echo $_subcategory->getId(); ?>);" ><label><?php echo $_subcategory->getName() ?></label></a>
                   <?php $_category = Mage::getModel('catalog/category')->load($_subcategory->getId()) ?>
                   <?php $_subsubcategories = $_subcategory->getChildrenCategories() ?>
                   <?php if (count($_subsubcategories) > 0): ?>
				   <div id="beee<?php echo $new2; ?><?php echo $_subcategory->getId(); ?>" style="display:none;">
                       <ul>
                           <?php foreach($_subsubcategories as $_subsubcategory):
						      $qt++;
						   ?>
                               <li class="level3" >
                                   <input type="checkbox" name="chk-<?php echo $qt; ?>" value="<?php echo $_subsubcategory->getId() ?>" <?php if(in_array($_subsubcategory->getId(), $catIds)) { echo "checked"; } ?> ><label><?php echo $_subsubcategory->getName() ?></label>
                   <?php $_subsubcategories2 = $_subsubcategory->getChildrenCategories() ?>
                   <?php if (count($_subsubcategories2) > 0): ?>
				   <div id="Abeee<?php echo $new2; ?><?php echo $_subsubcategory->getId(); ?>" >
                       <ul>
                           <?php foreach($_subsubcategories2 as $_subsubcategory2):
						      $qt++;
						   ?>
                               <li class="level4" >
                                   <input type="checkbox" name="chk-<?php echo $qt; ?>" value="<?php echo $_subsubcategory2->getId() ?>" <?php if(in_array($_subsubcategory2->getId(), $catIds)) { echo "checked"; } ?> ><label><?php echo $_subsubcategory2->getName() ?></label></li>
                           <?php endforeach; ?>
                       </ul>
                    <div>
                   <?php endif; ?>
                                   </li>
                           <?php endforeach; ?>
                       </ul>
                       </div>
                   <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>
	</td>
	</tr>
	<tr>
	<td colspan="2">
	<input type="hidden" name="total" value="<?php echo $qt; ?>" />
	<input type="hidden" name="usku" value="<?php echo $_GET["upc"]; ?>" />
	<input type="submit" style="background-color:green;padding:5px;color:#fff;" name="std" value="Update"  />  </td>
	</tr>
	</table>
	<div></div>
	</from>
    <?php
	exit(0);
	}
	if(isset($_GET["back"]))
			{
			   $_POST["stype"]="sitesearch";
			   $_POST["sku"]=$_GET["sku"];
			   $_POST["desc"]=$_GET["desc"];
			   $_POST["proorder"]=$_GET["proorder"];
			   $_POST["prolocation"]=$_GET["prolocation"];
			   $_POST["protype"]=$_GET["protype"];
	?>
              <script type="text/javascript" >
				jQuery(document).ready(function() {
				   jQuery('html, body').animate({
					scrollTop: jQuery('#tgb-<?php echo $_GET["eid"]; ?>').offset().top
				}, 2000);

				});
				</script>
    <?php
			}
	?>
	<script type="text/javascript" >
	jQuery(document).ready(function() {
			  jQuery('#protype').val(<?php echo $_POST["protype"]; ?>);
			  jQuery('#prolocation').val(<?php echo $_POST["prolocation"]; ?>);
			  jQuery('#proorder').val(<?php echo $_POST["proorder"]; ?>);
    });
	</script>
	<form method="POST" action="<?php echo Mage::getBaseUrl(); ?>?gt=pm">
	<table border="0" style="border:none !important;" cellpadding="5" cellspacing="5" >
	<tr>
	<td>Sku :</td>
	<td><input type="text" name="sku" id="sku" value="<?php echo $_POST["sku"]; ?>" ></td>
	</tr>
	<tr>
	<td style="text-align:center" colspan="2"><b>OR</b></td>
	</tr>
	<tr>
	<td>Description :</td>
	<td><input type="text" name="desc" id="desc" value="<?php echo $_POST["desc"]; ?>"></td>
	</tr>
	<tr class="protype">
    <td class="label"><label for="protype">Type</label></td>
    <td class="value">
        <select id="protype" name="protype" style="border:1px solid #000;">
<option value=""></option>
<option value="141">BAKERY</option>
<option value="140">COLD</option>
<option value="146">CUT MEAT</option>
<option value="144">Deli</option>
<option value="143">DRY</option>
<option value="142">FROZEN</option>
<option value="145">SF</option>
</select></td>
    </tr>
	<tr class="prolocation">
    <td class="label"><label for="prolocation">Location</label></td>
    <td class="value">
        <select id="prolocation" name="prolocation" style="border:1px solid #000;">
<option value=""></option>
<option value="338">0</option>
<option value="342">1</option>
<option value="341">2</option>
<option value="340">3</option>
<option value="325">3.5FM: Meat &amp; Seafood/Chicken</option>
<option value="339">4</option>
<option value="337">5</option>
<option value="336">6</option>
<option value="335">7</option>
<option value="329">8</option>
<option value="328">9</option>
<option value="334">Bakery</option>
<option value="327">Cut Meat</option>
<option value="333">Dairy</option>
<option value="331">Deli</option>
<option value="432">F</option>
<option value="330">Packed Meat</option>
<option value="332">Produce</option>
<option value="431">R</option>
<option value="326">Seafood</option>
</select>            </td>
    </tr>
	<tr class="proorder">
    <td class="label"><label for="proorder">Order</label></td>
    <td class="value">
        <select id="proorder" name="proorder" style="border:1px solid #000" >
<option value=""></option>
<option value="355">0.5</option>
<option value="360">1</option>
<option value="359">2</option>
<option value="363">3</option>
<option value="358">4</option>
<option value="356">5</option><option value="362">6</option>
<option value="364">7</option>
<option value="361">8</option>
<option value="357">9</option>
<option value="365">Bakery</option>
</select></td>
    </tr>
	<tr>
	<td>Search Type :</td>
	<td><select name="stype" id="stype" style="border:1px solid #000;">
	<option value="sitesearch" <?php if(isset($_POST["stype"]) && $_POST["stype"]=='sitesearch' ) { echo "selected"; } ?> >Check PrestoFresh</option>
	<option value="database" <?php if(isset($_POST["stype"]) && $_POST["stype"]=='database' ) { echo "selected"; } ?> >Check Database</option>
	<option value="both" <?php if(isset($_POST["stype"]) && $_POST["stype"]=='both' ) { echo "selected"; } ?> >Check Both</option>
	</select>
	</td>
	</tr>
    <?php
	    if($_POST["rlimit"]=="")
		{
		   // $lmo="0,50";
		}
		else
		{
		   $lmo=$_POST["rlimit"];
		}
	?>
    <tr>
	<td>Record Limit :</td>
	<td>
    <input type="text" name="rlimit" id="rlimit" value="<?php echo $lmo; ?>">
    Note : ( Enter with Comma for first 50 records enter 0,50 . for next 50 records enter 51,100 and so on .)
	</td>
	</tr>


	<tr>
	<td colspan="2"> <input type="submit" style="background-color:green;padding:5px;color:#fff;" name="std" value="Submit"  />  </td>
	</tr>
	</table>
	</form>
	<?php
	if(isset($_POST["desc"]) || isset($_POST["sku"]) )
	{
	$_GET["desc"] = $_POST["desc"];

	require_once( Mage::getBaseDir('base').'/_db.config.inc.php' );
    $con = mysql_connect(DBHOSTNAME, DBUSERNAME, DBPASSWORD);
    $sqlconnect = mysql_select_db( DBDATABASE, $con ) or die(mysql_error());

			if($_POST["stype"]=="both" || $_POST["stype"]=="database" )
			{
			  if(isset($_POST["sku"]) && $_POST["sku"]!="")
			  {
			  $curl = curl_init('https://api.itemmaster.com/v2/item/?upc='.$_POST["sku"].'&epl=50&epf=50&epr=50&ef=jpg');
			  curl_setopt($curl, CURLOPT_HTTPHEADER, array(
				'username:sdemoulpied',
				'password:Password1'
			));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
			$response = curl_exec($curl);
			$resultStatus = curl_getinfo($curl);
			 $resultStatus['http_code'];
			if($resultStatus['http_code'] == 200) {
				$string = $response;
				$xml = simplexml_load_string($string);
				$array=array();
				$value9=array();
				$json = json_encode($xml);
				$array = json_decode($json,TRUE);
				$value9=$array["item"]
				?>
				<table border="1" style="border-collapse:collapse;" cellpadding="5" cellspacing="5" >
				<tr><td colspan="4" style="font-weight:bold;text-align:center;">Database Search</td></tr>
				<tr>
				<th>Name</th>
				<th>Sku</th>
				<th>Image</th>
				<th>In Magneto</th>
				</tr>
				<?php
				$aa=array();
				$i=0;
				   $sel_number="select * from mg_catalog_product_entity where sku='".$value9["upcs"]["upc"]."'";
				   $rs_number=mysql_query($sel_number);
				   $total_number=mysql_fetch_array($rs_number);
				   $aa[$i] = $value9["upcs"]["upc"];
				   $i++;
				?>
				<tr>
				<td><?php echo $value9["name"]; ?> <br/> Size :-
				<?php echo $value9["packageData"]["packageSize"]["measure"]; ?> <?php echo $value9["packageData"]["packageSize"]["uom"]; ?>
				</td>
				<td><?php echo $value9["upcs"]["upc"]; ?></td>
				<td>
				<a href="<?php echo $value9["media"]["medium"][0]["url"]; ?>" target="_blank"><img src="<?php echo $value9["media"]["medium"][0]["url"]; ?>" width="50" height="50"  /></a>
				</td>
				<td><?php echo $total_number["entity_id"]; ?>
				<?php if($total_number["entity_id"]=="") { ?>
				 <a href="<?php echo Mage::getBaseUrl(); ?>addandsave.php?curp=<?php echo $value9["upcs"]["upc"]; ?>" target="_blank"> Add Product </a>
				<?php } else {  ?>
				<a href="<?php echo Mage::getBaseUrl(); ?>importProductNutri.php?curp=<?php echo $value9["upcs"]["upc"]; ?>" target="_blank" > Update Product </a>
				<?php } ?>
				</td>
				</tr>
				</table>
				<?php
					curl_close($curl);
					}
				 }
			  else {
			          $disco = str_replace(" ","-",$_POST["desc"]);
					  $curl = curl_init('https://api.itemmaster.com/v2/item/?q='.$disco.'&epl=50&epf=50&epr=50&ef=jpg');
					  curl_setopt($curl, CURLOPT_HTTPHEADER, array(
						'username:sdemoulpied',
						'password:Password1'
					));
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
					$response = curl_exec($curl);
					$resultStatus = curl_getinfo($curl);
					if($resultStatus['http_code'] == 200) {
						$string = $response;
						$xml = simplexml_load_string($string);
						$array=array();
						$json = json_encode($xml);
						$array = json_decode($json,TRUE);
						?>
						<table border="1" style="border-collapse:collapse;" cellpadding="5" cellspacing="5" >
						<tr><td colspan="4" style="font-weight:bold;text-align:center;">Database Search</td></tr>
						<tr>
						<th>Name</th>
						<th>Sku</th>
						<th>Image</th>
						<th>In Magneto</th>
						</tr>
						<?php
						$aa=array();
						$value3=array();
						$i=0;
						foreach ($array["item"] as $value3) {
						   $sel_number="select * from mg_catalog_product_entity where sku='".$value3["upcs"]["upc"]."'";
						   $rs_number=mysql_query($sel_number);
						   $total_number=mysql_fetch_array($rs_number);
						   $aa[$i] = $value3["upcs"]["upc"];
						   $i++;
						?>
						<tr>
						<td><?php echo $value3["name"]; ?> <br/> Size :-
						<?php echo $value3["packageData"]["packageSize"]["measure"]; ?> <?php echo $value3["packageData"]["packageSize"]["uom"]; ?>
						</td>
						<td><?php echo $value3["upcs"]["upc"]; ?></td>
						<td>
						<img src="<?php echo $value3["media"]["medium"][0]["url"]; ?>" width="50" height="50"  />
						</td>
						<td><?php echo $total_number["entity_id"]; ?>
						<?php if($total_number["entity_id"]=="") { ?>
						 <a href="<?php echo Mage::getBaseUrl(); ?>addandsave.php?curp=<?php echo $value3["upcs"]["upc"]; ?>" target="_blank"> Add Product </a>
						<?php } else {  ?>
						<a href="<?php echo Mage::getBaseUrl(); ?>importProductNutri.php?curp=<?php echo $value3["upcs"]["upc"]; ?>" target="_blank" > Update Product </a>
						<?php } ?>
						</td>
						</tr>
						<?php } ?>
						</table>
						<?php
							curl_close($curl);
							}
					}
	        }

			if(isset($_GET["back"]))
			{
			   $_POST["stype"]="sitesearch";
			   $_POST["sku"]=$_GET["sku"];
			   $_POST["desc"]=$_GET["desc"];
			   $_POST["proorder"]=$_GET["proorder"];
			   $_POST["prolocation"]=$_GET["prolocation"];
			   $_POST["protype"]=$_GET["protype"];

			}

			if($_POST["stype"]=="both" || $_POST["stype"]=="sitesearch" )
			{
        ?>
        <table border="1" style="border-collapse:collapse;margin-top:15px;" cellpadding="5" cellspacing="5" width="99%" >
		<tr><td colspan="3" style="text-align:center;"> Site Search </td></tr>
        <tr>
        <th colspan="3">Product Information </th>
        </tr>
        <?php
    if(isset($_POST["sku"]) && $_POST["sku"]!="")
	{
	    $sel_number="select mf.entity_id,n.value as name,s.value as size,mf.sku,ss.value as status,i.value as small_image,p.value as price,mei.value as ptype,rr.value as plocation,qrr.value as porder from mg_catalog_product_entity mf
	    left join mg_catalog_product_entity_varchar n on n.entity_id = mf.entity_id and n.attribute_id =  71
		left join mg_catalog_product_entity_varchar s on s.entity_id = mf.entity_id and s.attribute_id =  137
		left join mg_catalog_product_entity_varchar i on i.entity_id = mf.entity_id and i.attribute_id =  85
		left join mg_catalog_product_entity_int ss on ss.entity_id = mf.entity_id and ss.attribute_id =  96
		left join mg_catalog_product_entity_decimal p on p.entity_id = mf.entity_id and p.attribute_id =  75
		left join mg_catalog_product_entity_int mei on mei.entity_id = mf.entity_id and mei.attribute_id =  251
		left join mg_catalog_product_entity_int rr on rr.entity_id = mf.entity_id and rr.attribute_id =  252
		left join mg_catalog_product_entity_int qrr on qrr.entity_id = mf.entity_id and qrr.attribute_id =  253
		where  mf.sku LIKE '%".$_POST["sku"]."%' or mf.sku='".$_POST["sku"]."' group by mf.sku";

	}
	else if($_POST["desc"]!="" || $_POST["protype"]!="" || $_POST["prolocation"]!="" || $_POST["proorder"]!="" )
	{
	    $sel_number="select mf.entity_id,n.value as name,s.value as size,mf.sku,ss.value as status,i.value as small_image,p.value as price,mei.value as ptype,rr.value as plocation,qrr.value as porder from mg_catalog_product_entity mf
	    left join mg_catalog_product_entity_varchar n on n.entity_id = mf.entity_id and n.attribute_id =  71
		left join mg_catalog_product_entity_varchar s on s.entity_id = mf.entity_id and s.attribute_id =  137
		left join mg_catalog_product_entity_varchar i on i.entity_id = mf.entity_id and i.attribute_id =  85 and i.store_id=0
		left join mg_catalog_product_entity_int ss on ss.entity_id = mf.entity_id and ss.attribute_id =  96
		left join mg_catalog_product_entity_decimal p on p.entity_id = mf.entity_id and p.attribute_id =  75
		left join mg_catalog_product_entity_int mei on mei.entity_id = mf.entity_id and mei.attribute_id =  251
		left join mg_catalog_product_entity_int rr on rr.entity_id = mf.entity_id and rr.attribute_id =  252
		left join mg_catalog_product_entity_int qrr on qrr.entity_id = mf.entity_id and qrr.attribute_id =  253
		where mf.entity_id>=1 ";
		if($_POST["desc"]!="" && $_POST["protype"]=="" && $_POST["prolocation"]=="" && $_POST["proorder"]=="" )
		{
		    $sel_number.=" and n.value LIKE '%".$_POST["desc"]."%'";
		}
		if($_POST["desc"]!="" && ( $_POST["protype"]!="" || $_POST["prolocation"]!="" || $_POST["proorder"]!="") )
		{
		   $sel_number.=" and n.value LIKE '%".$_POST["desc"]."%'";
		}
		if($_POST["protype"]!="")
		{
		   $sel_number.=" and mei.value='".$_POST["protype"]."'";
		}
		if($_POST["prolocation"]!="")
		{
		   $sel_number.=" and rr.value='".$_POST["prolocation"]."'";
		}
		if($_POST["proorder"]!="")
		{
		   $sel_number.=" and qrr.value='".$_POST["proorder"]."'";
		}

		if($_POST["rlimit"]!="")
		{
		$sel_number.=" group by mf.sku limit ".$_POST["rlimit"];
		}
		else
		{
		   $sel_number.=" group by mf.sku limit 0,3000";
		}
	}
		   Mage::getSingleton('core/session')->setMyValue($sel_number);
		   $rs_number=mysql_query($sel_number);
		   $k=0;
		   while($total_number=mysql_fetch_array($rs_number))
		   {
		       if (in_array($total_number["sku"], $aa))
			   {
			      $gr=1;
			   }
			   else
			   {
			     $gr=0;
			   }

			   $k++;
		?>
		<tr <?php if($total_number["status"]!=1) { ?> style="background:#ffcaca" <?php } ?> id="tgb-<?php echo $total_number["entity_id"]; ?>">
        <td colspan="3" id="tgb2-<?php echo $k; ?>">
        <div id="puddiv-<?php echo $total_number["entity_id"]; ?>" style="color:green;font-size:14px;font-weight:bold; margin-top:10px; margin-bottom:10px;"></div>
        <div style="float:left;width:250px;">
        <form method="GET" action="<?php echo Mage::getBaseUrl(); ?>">
		<textarea name="namee-<?php echo $total_number["entity_id"]; ?>" style="border:1px solid #666; width:98%; margin-bottom:5px; font-size:14px; font-weight:bold;" id="namee-<?php echo $total_number["entity_id"]; ?>" onblur="donamesave(<?php echo $total_number["entity_id"]; ?>);"><?php echo $total_number["name"]; ?> </textarea>
             <input type="hidden" name="gt" id="gt" value="pm" />
             <input type="hidden" name="stype" id="stype" value="sitesearch" />
             <input type="hidden" name="k" id="k" value="<?php echo $k; ?>" />
             <input type="hidden" name="sku" id="sku" value="<?php echo $_POST["sku"]; ?>" />
             <input type="hidden" name="desc" id="desc" value="<?php echo $_POST["desc"]; ?>" />
             <input type="hidden" name="proorder" id="proorder" value="<?php echo $_POST["proorder"]; ?>" />
             <input type="hidden" name="prolocation" id="prolocation" value="<?php echo $_POST["prolocation"]; ?>" />
             <input type="hidden" name="protype" id="protype" value="<?php echo $_POST["protype"]; ?>" />
             <input type="hidden" name="entity_id" id="entity_id" value="<?php echo $total_number["entity_id"]; ?>" />
        <br/> Price : $
        <input type="text" name="pricee-<?php echo $total_number["entity_id"]; ?>" id="pricee-<?php echo $total_number["entity_id"]; ?>" value="<?php echo round($total_number["price"],2); ?>" onblur="dopricesave(<?php echo $total_number["entity_id"]; ?>);" style="margin-bottom:5px;" />
		  <br/>  Status :
		  <select name="statuse-<?php echo $total_number["entity_id"]; ?>" id="statuse-<?php echo $total_number["entity_id"]; ?>" style="border:1px solid #000; margin-top:5px;" onchange="dostatussave(<?php echo $total_number["entity_id"]; ?>);" >
          <option value="1" <?php if($total_number["status"]==1) { echo "selected"; } ?> >Enabled</option>
          <option value="2" <?php if($total_number["status"]==2) { echo "selected"; } ?> >Disabled</option>
          </select>
          <script type="text/javascript" >
			jQuery(document).ready(function() {
					  jQuery('#ptype-<?php echo $total_number["entity_id"];?>').val(<?php echo $total_number["ptype"]; ?>);
					  jQuery('#plocation-<?php echo $total_number["entity_id"];?>').val(<?php echo $total_number["plocation"]; ?>);
					  jQuery('#porder-<?php echo $total_number["entity_id"];?>').val(<?php echo $total_number["porder"]; ?>);
			});
	      </script>
          <br/> Type :
           <select id="ptype-<?php echo $total_number["entity_id"]; ?>" name="ptype-<?php echo $total_number["entity_id"]; ?>" style="border:1px solid #000;margin-top:5px;" onchange="dotypechange(<?php echo $total_number["entity_id"]; ?>);">
            <option value="141">BAKERY</option>
            <option value="140">COLD</option>
            <option value="146">CUT MEAT</option>
            <option value="144">Deli</option>
            <option value="143">DRY</option>
            <option value="142">FROZEN</option>
            <option value="145">SF</option>
            </select>
        <br/> Location :
           <select id="plocation-<?php echo $total_number["entity_id"]; ?>" name="plocation-<?php echo $total_number["entity_id"]; ?>" style="border:1px solid #000;margin-top:5px; width:80px;" onchange="dolocationchange(<?php echo $total_number["entity_id"]; ?>);">
            <option value=""></option>
            <option value="338">0</option>
            <option value="342">1</option>
            <option value="341">2</option>
            <option value="340">3</option>
            <option value="325">3.5FM: Meat &amp; Seafood/Chicken</option>
            <option value="339">4</option>
            <option value="337">5</option>
            <option value="336">6</option>
            <option value="335">7</option>
            <option value="329">8</option>
            <option value="328">9</option>
            <option value="334">Bakery</option>
            <option value="327">Cut Meat</option>
            <option value="333">Dairy</option>
            <option value="331">Deli</option>
            <option value="432">F</option>
            <option value="330">Packed Meat</option>
            <option value="332">Produce</option>
            <option value="431">R</option>
            <option value="326">Seafood</option>
            </select>
            <br/> Order :
            <select id="porder-<?php echo $total_number["entity_id"]; ?>" name="porder-<?php echo $total_number["entity_id"]; ?>" style="border:1px solid #000;margin-top:5px;" onchange="doorderchange(<?php echo $total_number["entity_id"]; ?>);" >
            <option value=""></option>
            <option value="355">0.5</option>
            <option value="360">1</option>
            <option value="359">2</option>
            <option value="363">3</option>
            <option value="358">4</option>
           <option value="356">5</option><option value="362">6</option>
           <option value="364">7</option>
            <option value="361">8</option>
            <option value="357">9</option>
            <option value="365">Bakery</option>
            </select>
          </form>
          </div>

          <div style="float:left; width:150px; margin-left:10px;">
           <br/> <span style="color:green;" > Size : <?php echo $total_number["size"]; ?></span>
          <?php
		   $start_date = date("Y-m-d");
	       $start_date1 = date('Y-m-d',strtotime($start_date . " +0 day"));
           $end_date1 = date('Y-m-d',strtotime($adate22 . " -30 day"));
		   $qty_ordered = 0;

		    $sel_qty_ordered="select SUM(qty_ordered) from mg_sales_flat_order_item where created_at>='".$end_date1."' and  created_at<='".$start_date1."' and sku='".$total_number["sku"]."'";
		   $qty_ordered=$connection->fetchOne($sel_qty_ordered);

		   if($qty_ordered=="")
		   {
		      $qty_ordered=0;
		   }
		   else
		   {
		     $qty_ordered=round($qty_ordered);
		   }
		   ?>

          <br/>
        SKU :  <?php echo $total_number["sku"]; ?>
		<a href="<?php echo Mage::getBaseUrl(); ?>media/catalog/product/<?php echo $total_number["small_image"]; ?>" target="_blank">
		<img src="<?php echo Mage::getBaseUrl(); ?>media/catalog/product/<?php echo $total_number["small_image"]; ?>" width="80" height="80"  /></a>
        <br/>
        <br/>
        <a href="<?php echo Mage::getBaseUrl(); ?>?gt=pm&usku=<?php echo $total_number["sku"]; ?>" target="_blank">Check Database</a> &nbsp;  &nbsp;  <a href="<?php echo Mage::getBaseUrl(); ?>?gt=pm&upc=<?php echo $total_number["sku"]; ?>&ppid=<?php echo $total_number["entity_id"]; ?>&sku=<?php echo $_POST["sku"]; ?>&desc=<?php echo $_POST["desc"]; ?>&proorder=<?php echo $_POST["proorder"]; ?>&prolocation=<?php echo $_POST["prolocation"]; ?>&protype=<?php echo $_POST["protype"]; ?>">Edit Product</a>
        </div>
        <div style="clear:both;">&nbsp;</div>

        <div>
        <?php
		   $_GET["sh"]=$total_number["entity_id"];
		   $start_date = date("Y-m-d");
	       $start_date1 = date('Y-m-d',strtotime($start_date . " +0 day"));
           $end_date1 = date('Y-m-d',strtotime($adate22 . " -365 day"));

		   $sel_qty_ordered="select count(product_id) from mg_sales_flat_order_item where created_at>='".$end_date1."' and  created_at<='".$start_date1."' and product_id='".$_GET["sh"]."'";
		   $qty_ordered2=$connection->fetchOne($sel_qty_ordered);
		   if($qty_ordered2=="")
		   {
		      $qty_ordered2=0;
		   }
		   else
		   {
		     $qty_ordered2=round($qty_ordered2);
		   }
		   $sel_qty_nf="select count(s.item_id) from supervision s
			 left join mg_sales_flat_order_item mi on s.item_id=mi.item_id
			 where mi.product_id='".$_GET["sh"]."' and ( s.realdate>='".$end_date1."' and s.realdate<='".$start_date1."' ) and s.status!='found'";
		   $qty_nf=$connection->fetchOne($sel_qty_nf);
		   if($qty_nf=="")
		   {
		      $qty_nf=0+1;
		   }
		   else
		   {
		      $qty_nf=round($qty_nf)+1;
		   }
		    $sel_qty_sf="select count(s.item_id) from supervision s
			 left join mg_sales_flat_order_item mi on s.item_id=mi.item_id
			 where mi.product_id='".$_GET["sh"]."' and ( s.realdate>='".$end_date1."' and s.realdate<='".$start_date1."' ) and s.status!='found' and s.substitute!=' '";
		   $qty_sf=$connection->fetchOne($sel_qty_sf);

		   if($qty_sf=="")
		   {
		      $qty_sf=0;
		   }
		   else
		   {
		      $qty_sf=round($qty_sf);
		   }
		   ?>
            <br/> <span style="color:green;" > Qty Ordered (last 30 Days): <?php echo $qty_ordered; ?></span>
            <br/>
           <a href="javascript:void(0);" onclick="opentdiv(<?php echo $total_number["entity_id"]; ?>);" style="color:green; text-decoration:underline;font-size:14px;"> Substituted (last 365 days) : <?php echo $qty_sf; ?> </a>
		   <?php
		    echo "<div style='color:green;display:none;' id='t".$total_number["entity_id"]."' >Qty Ordered (365 Days):".$qty_ordered2;
		    echo "<br/>";
		    echo "No. of times on Not Found list (365 Days):".$qty_nf;
		    echo "<br/>";
		    echo "% of times on Not Found list (365 days): ".round($qty_nf/$qty_ordered2,2)*100 ."%";
		    echo "<br/>";
		    echo "% of times Substituted (365 days):".round($qty_sf/$qty_ordered2,2)*100 ."%";
		    echo "</div>";
		   ?>
        </div>
        <br/>
        <a href="<?php echo Mage::getBaseUrl(); ?>?&sh=<?php echo $total_number["entity_id"]; ?>" target="_blank" style="color:red; text-decoration:underline;font-size:14px;"> Sub. History </a>
        <br/>
		 <a href="<?php echo Mage::getBaseUrl(); ?>?product_id=<?php echo $total_number["entity_id"]; ?>&rpc=<?php echo date("d-m-Y"); ?>" style="text-decoration:underline;color:red;" target="_blank"> Product Change </a>
        </td>
        </tr>
        <?php
		}
	    ?>
        </table>
		<?php }
		  }
		  exit(0);
		}
      ?>