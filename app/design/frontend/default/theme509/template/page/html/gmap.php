<?php

if(isset($_GET["gt"]) && $_GET["gt"]=="map" ) {

  $kbt = date("d_m_Y"); 
   // $kbt = "08_07_2016";
  $sql = "SELECT m.order_number,m.lat,m.lang,m.rc FROM `mg_shipping_delivery_window` m  where m.`window` LIKE '%".$kbt."' and m.driver_email='".$customerData["email"]."' order by m.`rc`";
  $rows = $connection->fetchAll($sql); 
  $ms="";
  $lat="";
  $lang="";
  foreach ($rows as $value) {
			   if($value["order_number"]!="" && $value["order_number"]!='100003190')
			   {
				   $sql2 = "SELECT entity_id FROM mg_sales_flat_order_grid where increment_id='".$value["order_number"]."'";
				   $rows2 = $connection->fetchOne($sql2);
				   
				   $sql22 = "SELECT customer_id FROM mg_sales_flat_order_grid where increment_id='".$value["order_number"]."'";
				   $rows22 = $connection->fetchOne($sql22);
				   
				   $customer = "" ;
				   $customer = Mage::getModel('customer/customer')->load($rows22); 
				   
				   $sql222 = "SELECT entity_id FROM mg_sales_flat_order_grid where customer_id='".$rows22."' and increment_id!='".$value["order_number"]."'";
				   $rows222 = $connection->fetchOne($sql222);
				   
				   $sqlstatus = "SELECT status FROM mg_sales_flat_order_grid  where increment_id='".$value["order_number"]."'";
				   $rowstatus = $connection->fetchOne($sqlstatus);
				 
				   $sql3  = "SELECT SUM(qty_ordered) FROM `mg_sales_flat_order_item` where `order_id`='".$rows2."'";
				   $rows3 = $connection->fetchOne($sql3); 
				   $order = Mage::getModel("sales/order")->load($rows2); //load order by order id 
				   $address = $order->getShippingAddress();
				   $shipping_info = $order->getShippingAddress()->getData();
				   $temp = explode("|",$value['window']);
				   $temp3 = explode(".",$rows3);
				   $check22 = 0;
				   
				if($customer->getId()!="813" && $customer->getId()!="1205"  && $customer->getId()!="52"  && $rowstatus!='canceled')
				   { 
				       
					  // $ms.="['".$value['order_number']."', ".$value['lat'].",".$value['lang'].", ".$value['rc']."],";
					  
			 $ms.= "'".$address->getStreetFull()." ".$address->getCity()." ".$address["postcode"]." ".$address->getRegion()." - ".$value["order_number"]." - ".$address->getName()."',";
			 
			       $string = $value['lat'];
				   $insertion = ".";
				   $index = 2;
				   $result = substr_replace($string, $insertion, $index, 0);
			       $lat.= $result.",";
				  
				   $string = $value['lang'];
				   $insertion = ".";
				   $index = 3;
				   $result = substr_replace($string, $insertion, $index, 0);
			       $lang.= $result.",";
					   
                   }
		 }
	}
	  $ms = rtrim($ms, ",");
	  $lat = rtrim($lat, ",");
	  $lang = rtrim($lang, ",");
	  ?>
      
 <div id="map" style="height: 420px; width: 340px;"></div>

<script src="http://maps.google.com/maps/api/js?key=AIzaSyBpHgHFLBnSLhBk2qP1PIF7r_zU4NqMhfQ&sensor=true" type="text/javascript"></script>

<script type="text/javascript">
  var delay = 1;
  

  var newArr = new Array(<?php echo $lang; ?>);
  var newArr2 = new Array(<?php echo $lat; ?>);
  // res2 = hjtempd.split(",");
  
  var infowindow = new google.maps.InfoWindow();
  var latlng = new google.maps.LatLng(40.4173, 82.9071);
  var mapOptions = {
    zoom: 200,
    center: latlng,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  }
  var geocoder = new google.maps.Geocoder(); 
  var map = new google.maps.Map(document.getElementById("map"), mapOptions);
  var bounds = new google.maps.LatLngBounds();
 i=0;
  function geocodeAddress(address, next) {
 
    geocoder.geocode({address:address}, function (results,status)
      { 
         if (status == google.maps.GeocoderStatus.OK) {
          var p = results[0].geometry.location;
          var lat=p.lat();
          var lng=p.lng();
          // createMarker(address,lat,lng,i);
        }
        else {
           if (status == google.maps.GeocoderStatus.OVER_QUERY_LIMIT) {
            nextAddress--;
            delay++;
          } else {
                        }   
        }
         next();
		 createMarker(address,newArr2[i],newArr[i],i);
		i=i+1;
      }
    );
  }
  
  var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var labelIndex = 0;
	
 function createMarker(add,lat,lng,num2) {

   var contentString = add;
   var dfdf = num2;
   var marker = new google.maps.Marker({
     position: new google.maps.LatLng(lat,lng),
	 label: labels[labelIndex++ % labels.length],
     map: map
           });

  google.maps.event.addListener(marker, 'click', function() {
     infowindow.setContent(contentString); 
     infowindow.open(map,marker);
   });

   bounds.extend(marker.position);

 }
  var locations = [
           <?php echo $ms; ?>
  ];
  var nextAddress = 0;
  function theNext() {
    if (nextAddress < locations.length) {
      setTimeout('geocodeAddress("'+locations[nextAddress]+'",theNext)', delay);
      nextAddress++;
    } else {
      map.fitBounds(bounds);
    }
  }
  theNext();

</script>
      
      <?php
	  
	  exit(0);
	  
	}
?>