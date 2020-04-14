<?php
 $customer_id = Mage::getSingleton('customer/session')->getId();
 $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
  if(isset($_POST["abc"]))
  {
     $jo="";

	 for($jj=1;$jj<=13;$jj++)
	 {
	     if(isset($_POST["chk-".$jj]))
		 {
		    $jo.=$_POST["chk-".$jj].",";
		 }
	 }


	 $update="update mg_customer_entity set subs='".$jo."' where entity_id='".$customer_id."'";
	 $connection->query($update);
  }
?>
<style>
.mainEstimated_sub{
	border: 1px solid #000;
    border-radius: 8px;
    float: left;
    margin-bottom: 12px;
    margin-top: 12px;
    padding: 21px;
    width: 95%;
}
.estimated_heading {
    margin-bottom: 13px;
}
.text {
    float: left;
    margin-right: 27px;
	width:68%;
	font-size:13px;
}
.estimated_fields {
    float: left;
	margin-top:-6px;
    /*width: 48%;*/
}
.red{color:red;}
.green{color:green}
.subButtom {
    -moz-border-bottom-colors: none;
    -moz-border-left-colors: none;
    -moz-border-right-colors: none;
    -moz-border-top-colors: none;
    background-attachment: scroll;
    background-clip: border-box;
    background-color: #5d8c12 !important;
    background-image: -moz-linear-gradient(center top , #9dc31f, #649513);
    background-origin: padding-box;
    background-position: 0 0;
    background-repeat: repeat;
    background-size: auto auto;
    border-color: #5d8c12 #5d8c12 #36520b;
    border-image: none;
    border-radius: 3px;
    border-style: solid;
    border-width: 1px;
    color: #fff;
    cursor: pointer;
    font-family: "Open Sans",sans-serif;
    font-size: 13px;
    height: 30px;
    line-height: 25px;
    padding-left: 4px;
	padding-right: 4px;
	background:-webkit-gradient(linear,left top,left bottom,from(#9dc31f),to(#649513));
}
.subButtom:hover{
	background: rgba(0, 0, 0, 0) -moz-linear-gradient(center top , #659614, #659614) repeat scroll 0 0;
    border-color: #36520b #5d8c12 #5d8c12;
    color: #fff;
}
#msg{float:left;}
</style>
		<?php
	$customer_id = Mage::getSingleton('customer/session')->getId();
	$resource = Mage::getSingleton('core/resource');
	$conn = $resource->getConnection('core_read');

?>
<div class="dashboard">
    <div class="page-title">
        <h1><?php echo $this->__('Substitute Management') ?></h1>
    </div>
	<div>
    <form method="post" action="<?=Mage::getBaseUrl()?>customer/account/?sub=1">
    <?php
    $select="select subs from mg_customer_entity where entity_id='".$customer_id."'";
	$de_select=$connection->fetchOne($select);
	// echo $de_select;
	$jemp=explode(",",$de_select);
    ?>
    <div>



                             <div style="clear:both;">
                                 <br/>

                            <label for="ordercomment-comment" style="clear:both;display:block;margin-bottom:5px;"><?php echo Mage::helper('ordercomment')->__('Substitute Preferences') ?>  (check all that apply): </label>
							 </div>

                            <div style="float:left;width:250px;color:#000;">
							 <input type="checkbox" name="chk-1" value="Caffeine Free" <?php if(in_array("Caffeine Free", $jemp)) { ?>
                             checked <?php } ?>  > Caffeine Free <br/>
							 <input type="checkbox" name="chk-2"  value="Gluten Free" <?php if(in_array("Gluten Free", $jemp)) { ?>
                             checked <?php } ?> > Gluten Free <br/>
							 <input type="checkbox" name="chk-3"  value="GMO Free" <?php if(in_array("GMO Free", $jemp)) { ?>
                             checked <?php } ?> > GMO Free <br/>
							 <input type="checkbox" name="chk-4" value="Kosher" <?php if(in_array("Kosher", $jemp)) { ?>
                             checked <?php } ?> > Kosher <br/>
							 <input type="checkbox" name="chk-5" value="Lactose Free" <?php if(in_array("Lactose Free", $jemp)) { ?>
                             checked <?php } ?> > Lactose Free <br/>
							 <input type="checkbox" name="chk-6" value="Low Carb" <?php if(in_array("Low Carb", $jemp)) { ?>
                             checked <?php } ?> > Low Carb <br/>
							 </div>
							 <div style="float:left;width:250px;color:#000;">
							 <input type="checkbox" name="chk-7"  value="Low Cholesterol" <?php if(in_array("Low Cholesterol", $jemp)) { ?> checked <?php } ?>  > Low Cholesterol <br/>
							 <input type="checkbox" name="chk-8" value="Low Fat" <?php if(in_array("Low Fat", $jemp)) { ?> checked <?php } ?>  > Low Fat <br/>
							 <input type="checkbox" name="chk-9" value="No Fat" <?php if(in_array("No Fat", $jemp)) { ?> checked <?php } ?> > No Fat <br/>
							 <input type="checkbox" name="chk-10" value="No Pork" <?php if(in_array("No Pork", $jemp)) { ?> checked <?php } ?> > No Pork <br/>
							 <input type="checkbox" name="chk-11" value="Organic" <?php if(in_array("Organic", $jemp)) { ?> checked <?php } ?> > Organic <br/>
							 <input type="checkbox" name="chk-12" value="Vegan" <?php if(in_array("Vegan", $jemp)) { ?> checked <?php } ?> > Vegan <br/>
							 <input type="checkbox" name="chk-13" value="Vegetarian" <?php if(in_array("Vegetarian", $jemp)) { ?> checked <?php } ?> > Vegetarian
							 </div>
                              <div style="clear:both;">
                             <input type="submit" name="abc" id="go_search" class="category-go" value="Save" onclick="" alt="" style="float:none;">                 </div>
                             </div>
    </form>
    </div>
</div>
