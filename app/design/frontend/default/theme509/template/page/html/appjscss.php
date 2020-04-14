<?php
if(isset($_GET["picker2"]) && $_GET["picker2"]!="" )
{
   $_GET["picker"] = $_GET["picker2"];
}
?>
<script>
function dohiren(id,cust,ddate,infopicker,itemqty,itemid)
{
	  noteb = jQuery('#'+id.trim()).val();
	  jQuery('#div-'+id).html('<span style="font-weight:bold;color:#999;">Processing....</span>');
	   jQuery("#trid-"+id).css("background-color", "#ffd9d9");

	  jQuery.ajax({
           url: "<?php echo Mage::getBaseUrl(); ?>ajax.php?rand="+Math.random()+"&note="+noteb+"&cust="+cust+"&idstring="+id+"&ddate="+ddate+"&infopicker="+infopicker+"&itemqty="+itemqty+"&itemid="+itemid,
           context: document.body
			}).done(function(data) {
			 jQuery('#div-'+id).html('<span style="font-weight:bold;color:red;">Status :- Not Found<br/> Note :- '+noteb+'</span>');
		 });
}

function donfnote(item_id)
{
   noteb = jQuery('#nf-'+item_id.trim()).val();

    jQuery.ajax({
           url: "<?php echo Mage::getBaseUrl(); ?>ajaxnf.php?rand="+Math.random()+"&note="+noteb+"&itemid="+item_id,
           context: document.body
			}).done(function(data) {
			 jQuery('#divnfres-'+item_id).html('<span style="font-weight:bold;color:red;">Updated successfully.</span>');
		 });

}



function dodrivenote(rid,route)
{
   note= jQuery('#textnote-'+rid.trim()).val();

   jQuery.ajax({
           url: "<?php echo Mage::getBaseUrl(); ?>ajaxdrivenote.php?rand="+Math.random()+"&note="+note+"&rid="+rid+"&route="+route,
           context: document.body
			}).done(function(data) {
			alert("driver note has been updated successfully");
		 });
}




function dobpack(oid)
{
    jQuery.ajax({
           url: "<?php echo Mage::getBaseUrl(); ?>ajaxbpack.php?rand="+Math.random()+"&oid="+oid,
           context: document.body
			}).done(function(data) {
			jQuery('#pckdiv').html("Brochure has been Packed successfully");
		 });
}

function docheck(d,cf,bag,loose,cold_bin,cold_bag,frozen_bin,frozen_bag,load)
{
  if(load=="")
  {
     alert("Order has been not loaded please check load list.")
	 return false;
  }
  else
  {
  b='Please confirm that you have pulled all of the following items/containers: \n\n Dry Bin :  \t'+d+'\n Dry Bag:  \t'+bag+'\n DryLoose:  \t'+loose+'\n Cold Bin:  \t'+cold_bin+'\n Cold Bag:  \t'+cold_bag+'\n Frozen Bin:  \t'+frozen_bin+'\n Frozen Bag:  \t'+frozen_bag
  return confirm(b);
  }

}
function dohirenupdate(id,cust,ddate,infopicker,itemqty,itemid)
{
	  noteb = jQuery('#'+id.trim()).val();
	  jQuery('#div-'+id).html('<span style="font-weight:bold;color:#999;">Processing....</span>');
	  jQuery.ajax({
           url: "<?php echo Mage::getBaseUrl(); ?>ajaxupdate.php?rand="+Math.random()+"&note="+noteb+"&cust="+cust+"&idstring="+id+"&ddate="+ddate+"&infopicker="+infopicker+"&itemqty="+itemqty+"&itemid="+itemid,
           context: document.body
			}).done(function(data) {
			alert("information has been updated successfully");
			 // jQuery('#div-'+id).html('<span style="font-weight:bold;color:red;">Status :- Not Found<br/> Note :- '+noteb+'</span>');
		 });
}

function dohirenupdate(id,cust,ddate,infopicker,itemqty,itemid)
{
	  noteb = jQuery('#'+id.trim()).val();
	  jQuery('#div-'+id).html('<span style="font-weight:bold;color:#999;">Processing....</span>');
	  jQuery.ajax({
           url: "<?php echo Mage::getBaseUrl(); ?>ajaxupdate.php?rand="+Math.random()+"&note="+noteb+"&cust="+cust+"&idstring="+id+"&ddate="+ddate+"&infopicker="+infopicker+"&itemqty="+itemqty+"&itemid="+itemid,
           context: document.body
			}).done(function(data) {
			// alert("information has been updated successfully");
			 jQuery('#div-'+id).html('<span style="font-weight:bold;color:red;">Status :- Not Found<br/> Note :- '+noteb+'</span>');
		 });
}

function dohirenupdate2(id,cust,ddate,infopicker,itemqty,itemid)
{
	  noteb = jQuery('#'+id.trim()).val();
	  jQuery('#div-'+id).html('<span style="font-weight:bold;color:#999;">Processing....</span>');
	  jQuery("#trid-"+id).css("background-color", "#ffd9d9");
	   jQuery.ajax({
           url: "<?php echo Mage::getBaseUrl(); ?>ajaxupdate.php?rand="+Math.random()+"&note="+noteb+"&cust="+cust+"&idstring="+id+"&ddate="+ddate+"&infopicker="+infopicker+"&itemqty="+itemqty+"&itemid="+itemid,
           context: document.body
			}).done(function(data) {
			// alert("information has been updated successfully");
			 jQuery('#div-'+id).html('<span style="font-weight:bold;color:red;">Status :- Not Found<br/> Note :- '+noteb+'</span>');
		 });
}
function dohiren32(id,cust,ddate,infopicker,itemqty,itemid)
{
	  noteb = jQuery('#'+id.trim()).val();
	  jQuery.ajax({
           url: "<?php echo Mage::getBaseUrl(); ?>ajaxr.php?rand="+Math.random()+"&note="+noteb+"&cust="+cust+"&idstring="+id+"&ddate="+ddate+"&infopicker="+infopicker+"&itemqty="+itemqty+"&itemid="+itemid,
           context: document.body
			}).done(function(data) {
			 jQuery('#cfe'+id).html('<span style="font-weight:bold;color:green;">Already Packed</span>');
			 jQuery('#cfegreen-'+itemid).html('<span style="font-weight:bold;color:green;">Already Packed</span>');
		 });
}

function dopacked(itemid,ddate)
{
	  jQuery.ajax({
           url: "<?php echo Mage::getBaseUrl(); ?>ajaxr.php?rand="+Math.random()+"&ddate="+ddate+"&itemid="+itemid,
           context: document.body
			}).done(function(data) {
			 jQuery('#nfdiv-'+itemid).html('<span style="font-weight:bold;color:green;">Already Packed</span>');
		 });
}
function doloaded(id)
{
   jQuery.ajax({
           url: "<?php echo Mage::getBaseUrl(); ?>ajaxl.php?rand="+Math.random()+"&id="+id,
           context: document.body
			}).done(function(data) {
			 jQuery('#trloadtext-'+id).html('<span style="font-weight:bold;color:green;">Dry Items Already Loaded</span>');
			 if(data==1)
			 {
			 jQuery("#trload-"+id).css("background-color", "#daebe1");
			 }
		 });
}

function doloaded2(id)
{
   jQuery.ajax({
           url: "<?php echo Mage::getBaseUrl(); ?>ajaxl2.php?rand="+Math.random()+"&id="+id,
           context: document.body
			}).done(function(data) {
			 jQuery('#trloadtext2-'+id).html('<span style="font-weight:bold;color:green;">C/F Items Already Loaded</span>');
			 if(data==1)
			 {
			 jQuery("#trload-"+id).css("background-color", "#daebe1");
			 }
		 });
}

function dosname(id,tempid)
{
	  noteb = jQuery('#snote-'+id.trim()).val();
	  if(noteb=="")
	  {
	  jQuery("#tr-"+id).css("background-color", "#d5ffd5");
	  <?php
	    if(isset($_GET["it"]) && $_GET["it"]=='a')
		{
	  ?>
	  jQuery("#tr-"+id).hide();
	  <?php } ?>
	  }
	  else
	  {
	     jQuery("#tr-"+id).css("background-color", "#d9d9d9");
		  <?php
	    if(isset($_GET["it"]) && $_GET["it"]=='a')
		{
	  ?>
	  jQuery("#tr-"+id).hide();
	  <?php } ?>
	  }
	  jQuery.ajax({
            url: "<?php echo Mage::getBaseUrl(); ?>ajax222.php?rand="+Math.random()+"&note="+noteb+"&idstring="+id,
           context: document.body
			}).done(function(data) {
			    // alert("Information has been updated successfully");
				jQuery("#ooop-"+id).html("");
				jQuery("#ooop-"+id).html("Item has been updated successfully");
				 jQuery("#ooop-"+id).show();
		 });

	jQuery("#nfdiv-"+tempid).show();
}
function dosname2(id)
{

      jQuery('#sso-'+id).show();
	  noteb = jQuery('#snote-'+id.trim()).val();
	  jQuery("#tr-"+id).css("background-color", "#ffd9d9");
	  jQuery.ajax({
            url: "<?php echo Mage::getBaseUrl(); ?>ajax3222.php?rand="+Math.random()+"&note="+noteb+"&idstring="+id,
           context: document.body
			}).done(function(data) {
			      jQuery("#ooop-"+id).html("");
			     jQuery("#ooop-"+id).html("Item has been updated successfully");
				 jQuery("#ooop-"+id).show();
		 });
}
function dopackinfo(id,newcust)
{
	  if(newcust==1)
	  {
				  d = jQuery('#txt-d-'+id.trim()).val();
				  cf = jQuery('#txt-cf-'+id.trim()).val();
				  l = jQuery('#txt-l-'+id.trim()).val();
				  b = jQuery('#txt-b-'+id.trim()).val();
				  cold_bin = jQuery('#txt-cold_bin-'+id.trim()).val();
				  cold_bag = jQuery('#txt-cold_bag-'+id.trim()).val();
				  frozen_bag = jQuery('#txt-frozen_bag-'+id.trim()).val();
				  frozen_bin = jQuery('#txt-frozen_bin-'+id.trim()).val();


				  jQuery.ajax({
						url: "<?php echo Mage::getBaseUrl(); ?>ajaxpackinfo2.php?rand="+Math.random()+"&id="+id+"&d="+d+"&cf="+cf+"&l="+l+"&b="+b+"&cold_bin="+cold_bin+"&cold_bag="+cold_bag+"&frozen_bag="+frozen_bag+"&frozen_bin="+frozen_bin,
						context: document.body
						}).done(function(data) {
							// alert("packing information has been updated");
							jQuery('#updateinfo').html('Packing information has been updated. <div><br/></div>');
					 });

	  }

	  else
	  {

		     if(newcust=="")
			 {

		          jQuery('#updateinfo').html('');
				  d = jQuery('#txt-d-'+id.trim()).val();
				  cf = jQuery('#txt-cf-'+id.trim()).val();
				  l = jQuery('#txt-l-'+id.trim()).val();
				  b = jQuery('#txt-b-'+id.trim()).val();
				  cold_bin = jQuery('#txt-cold_bin-'+id.trim()).val();
				  cold_bag = jQuery('#txt-cold_bag-'+id.trim()).val();
				  frozen_bag = jQuery('#txt-frozen_bag-'+id.trim()).val();
				  frozen_bin = jQuery('#txt-frozen_bin-'+id.trim()).val();

				  jQuery.ajax({
						url: "<?php echo Mage::getBaseUrl(); ?>ajaxpackinfo2.php?rand="+Math.random()+"&id="+id+"&d="+d+"&cf="+cf+"&l="+l+"&b="+b+"&cold_bin="+cold_bin+"&cold_bag="+cold_bag+"&frozen_bag="+frozen_bag+"&frozen_bin="+frozen_bin,
						context: document.body
						}).done(function(data) {
							// alert("packing information has been updated");
							jQuery('#updateinfo').html('Packing information has been updated. <div><br/></div>');
					 });

			 }
			 else
			 {

		          jQuery('#updateinfo').html('');
				  d = jQuery('#txt-d-'+id.trim()).val();
				  cf = jQuery('#txt-cf-'+id.trim()).val();
				  l = jQuery('#txt-l-'+id.trim()).val();
				  b = jQuery('#txt-b-'+id.trim()).val();
				  cold_bin = jQuery('#txt-cold_bin-'+id.trim()).val();
				  cold_bag = jQuery('#txt-cold_bag-'+id.trim()).val();
				  frozen_bag = jQuery('#txt-frozen_bag-'+id.trim()).val();
				  frozen_bin = jQuery('#txt-frozen_bin-'+id.trim()).val();

				  jQuery.ajax({
						url: "<?php echo Mage::getBaseUrl(); ?>ajaxpackinfo2.php?rand="+Math.random()+"&id="+id+"&d="+d+"&cf="+cf+"&l="+l+"&b="+b+"&cold_bin="+cold_bin+"&cold_bag="+cold_bag+"&frozen_bag="+frozen_bag+"&frozen_bin="+frozen_bin,
						context: document.body
						}).done(function(data) {
							// alert("packing information has been updated");
							jQuery('#updateinfo').html('Packing information has been updated. <div><br/></div>');
					 });

			 }


	  }
}
function dohiren2(id,cust,ddate,infopicker,itemqty,name,itemid)
{
        noteb = jQuery('#'+id.trim()).val();
	    jQuery('#div-'+id).html('<span style="font-weight:bold;color:#999;">Processing....</span>');
	    jQuery("#trid-"+id).css("background-color", "#d5ffd5");
        jQuery.ajax({
url: "<?php echo Mage::getBaseUrl(); ?>ajax2.php?rand="+Math.random()+"&note="+noteb+"&cust="+cust+"&idstring="+id+"&ddate="+ddate+"&infopicker="+infopicker+"&itemqty="+itemqty+"&itemid="+itemid,
           context: document.body
			}).done(function(data) {
			  jQuery('#div-'+id).html('<span style="font-weight:bold;color:green;">Status :- Found<br/> Note :- '+noteb+'</span>');
			  jQuery('#packdiv-'+id).show();
		 });

}
function dohiren2h(id,cust,ddate,infopicker,itemqty,name,itemid)
{
       jQuery('#dpackdiv-'+itemid).show();
       noteb = jQuery('#'+id.trim()).val();
	   jQuery('#div-'+id).html('<span style="font-weight:bold;color:#999;">Processing....</span>');
	    jQuery("#trid-"+id).css("background-color", "#d5ffd5");
       jQuery.ajax({
url: "<?php echo Mage::getBaseUrl(); ?>ajax2.php?rand="+Math.random()+"&note="+noteb+"&cust="+cust+"&idstring="+id+"&ddate="+ddate+"&infopicker="+infopicker+"&itemqty="+itemqty+"&itemid="+itemid,
           context: document.body
			}).done(function(data) {
			  jQuery('#div-'+id).html('<span style="font-weight:bold;color:green;">Status :- Found<br/> Note :- '+noteb+'</span>');
			  jQuery('#dpackdiv-'+itemid).show();
		 });
}
function dohiren2hk(id,cust,ddate,infopicker,itemqty,name,itemid)
{
       jQuery('#dpackdiv-'+itemid).show();
       noteb = jQuery('#'+id.trim()).val();
	   jQuery('#div-'+id).html('<span style="font-weight:bold;color:#999;">Processing....</span>');
	    jQuery("#trid-"+id).css("background-color", "#d5ffd5");
       jQuery.ajax({
url: "<?php echo Mage::getBaseUrl(); ?>ajax2.php?rand="+Math.random()+"&note="+noteb+"&cust="+cust+"&idstring="+id+"&ddate="+ddate+"&infopicker="+infopicker+"&itemqty="+itemqty+"&itemid="+itemid,
           context: document.body
			}).done(function(data) {
			  jQuery('#div-'+id).html('<span style="font-weight:bold;color:green;">Status :- Found<br/> Note :- '+noteb+'</span>');
		 });
}
function dohiren333(id)
{
   k=confirm('Are you sure this product is found ? ');
   if(k==true)
   {
       jQuery.ajax({
            url: "<?php echo Mage::getBaseUrl(); ?>ajax3uu.php?rand="+Math.random()+"&idstring="+id,
           context: document.body
			}).done(function(data) {
			    jQuery('#tr-'+id).hide();
		 });
   }
}
function ddohiren333(id)
{
   k=confirm('Are you sure this product is found ? ');
   if(k==true)
   {
       jQuery.ajax({
            url: "<?php echo Mage::getBaseUrl(); ?>ajax333.php?rand="+Math.random()+"&idstring="+id,
           context: document.body
			}).done(function(data) {
			    jQuery('#tr-'+id).hide();
		 });
   }
}
</script>
<style>
.mini-search select
{
    width:158px !important;
}
</style>