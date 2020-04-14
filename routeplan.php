<?php

error_reporting( E_ALL ); 
function json_encode_ex($data) {
  return json_encode($data, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP);
}
 function executeRest($url, $method, $key, $data = "") {
  $headers = array(
    'Accept: application/json',
    'Content-Type: application/json'
  );

  $url .= '?key='.$key;

  $handle = curl_init();
  curl_setopt($handle, CURLOPT_URL, $url);
  curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 10);
  curl_setopt($handle, CURLOPT_TIMEOUT, 360);

  switch($method)
  {
    case 'GET':
    break;

    case 'POST':
    curl_setopt($handle, CURLOPT_POST, true);
    curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
    break;

    case 'PUT':
    curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
    break;

    case 'DELETE':
    curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
    break;
  }

  $response = curl_exec($handle);
  $status = curl_getinfo($handle, CURLINFO_HTTP_CODE);
 
  if(php_sapi_name() != "cli") {
    flush();
    ob_flush();
  }

  return json_indent($response);
  
  }
  
  $baseApiURL = "https://fleet.workwave.com/api/v1/territories/d6cf918e-390d-4f2c-88c0-e9f088fcfbf6/orders";
  $key = 'e8712eea-32c3-4536-83fd-91fa0c82698c';
  $srb = ' { "orders":[{
 
 "name": "Order 2",

 "eligibility": {

 "type": "on",

 "onDates": [

 "20160326"

 ]

 },

 "forceVehicleId": null,

 "priority": 20,

 "loads": {},

 "pickup": null,

 "delivery": {

 "depotId": null,

 "location": {

 "address": "701-799 Birmingham Ave, Jasper, AL 35501, USA"

 },

 "timeWindows": [

 {

 "startSec": 32400,

 "endSec": 43200

 }

 ],

 "notes": "Driver must use the back door to enter the building. Demonstrate the

concept of notes and priority",

 "serviceTimeSec": 1800,

 "tagsIn": [],

 "tagsOut": [],

 "customFields": {}

 },

 "isService": false
 
 }] }';
  $routeplanRes = executeRest($baseApiURL, 'POST', $key, $srb);
  
  print_r($routeplanRes);
 ?>