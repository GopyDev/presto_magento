$connection = Mage::getSingleton('core/resource')->getConnection('core_write');

     if(isset($_GET["note"]))
	 {
	      $query = "insert into `supervision` set `idstring` = '".$_GET["idstring"]."',infopicker='".$_GET["infopicker"]."',itemqty='".$_GET["itemqty"]."',cust='".$_GET["cust"]."',note='".$_GET["note"]."',ddate='".$_GET["ddate"]."' "; 
		  $connection->query($query);
	 }