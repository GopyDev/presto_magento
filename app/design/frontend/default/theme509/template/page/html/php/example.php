<?php

if (version_compare(PHP_VERSION, '5.3.0', '<')){
  die("This example requires PHP 5.3.0 or above to work. <br>You have PHP ".PHP_VERSION." in this system");
}

$jsonPath = dirname( __FILE__ )."/../json";
if (!is_dir($jsonPath)) {
  die("Cannot find folder with JSON data at ".$jsonPath);
}

$baseApiURL = "http://vrp.viamente.com/api/vrp/v2";
$key = 'YOUR_API_KEY_HERE';

if ($key == 'YOUR_API_KEY_HERE') {
  die("Please edit example.php at line \"\$key = 'YOUR_API_KEY_HERE';\" and replace YOUR_API_KEY_HERE with your API key");
}


$create_new_routeplan_request = assoc_array_from_json_file("create_new_routeplan_request.json");

echo "<html><body>\n";
echo "<h1>List available subfleets</h1>\n";
$subfleetsRes = executeRest($baseApiURL."/subfleets", 'GET', $key);

echo "<h1>List saved routeplans</h1>\n";
executeRest($baseApiURL."/routeplans", 'GET', $key);

// For example purposes: get the ID of the first returned subfleet and use that when creating the new routeplan
// NOTE: here we assume that the "List available subfleets" invocation was successul and that there is at least one saved subfleet.
$subfleets = json_decode($subfleetsRes, true);
$subfleetID = $subfleets['subfleets'][0]['id'];
$create_new_routeplan_request['subfleetID'] = $subfleetID;

echo "<h1>Create new routeplan</h1>\n";
$routeplanRes = executeRest($baseApiURL."/routeplans", 'POST', $key, json_encode_ex($create_new_routeplan_request));

$routeplanResArr = json_decode($routeplanRes, true);

echo "<h1>Get details about newly created routeplan</h1>\n";
executeRest($baseApiURL."/routeplans/".$routeplanResArr['id'], 'GET', $key);

echo "<h1>List saved routeplans</h1>\n";
executeRest($baseApiURL."/routeplans", 'GET', $key);

echo "</body></html>\n";
// Done

// JSON helpers
function assoc_array_from_json_file($fileName) {
  global $jsonPath;
  $str = file_get_contents($jsonPath."/".$fileName);
  return json_decode($str, true); // true = decode as associative array
}

function json_encode_ex($data) {
  return json_encode($data, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP);
}

function json_indent($json) {
  $result      = '';
  $pos         = 0;
  $strLen      = strlen($json);
  $indentStr   = '  ';
  $newLine     = "\n";
  $prevChar    = '';
  $outOfQuotes = true;

  for ($i=0; $i<=$strLen; $i++) {
    // Grab the next character in the string.
    $char = substr($json, $i, 1);

    // Are we inside a quoted string?
    if ($char == '"' && $prevChar != '\\') {
      $outOfQuotes = !$outOfQuotes;

    // If this character is the end of an element,
    // output a new line and indent the next line.
    } else if(($char == '}' || $char == ']') && $outOfQuotes) {
      $result .= $newLine;
      $pos --;
      for ($j=0; $j<$pos; $j++) {
        $result .= $indentStr;
      }
    }

    // Add the character to the result string.
    $result .= $char;

    // If the last character was the beginning of an element,
    // output a new line and indent the next line.
    if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
      $result .= $newLine;
      if ($char == '{' || $char == '[') {
        $pos ++;
      }

      for ($j = 0; $j < $pos; $j++) {
        $result .= $indentStr;
      }
    }

    $prevChar = $char;
  }
  return $result;
}

// Rest helper
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
  echo "<br>Response status: ".$status."\n";
  echo "<br>Response body: \n";
  echo "<br><pre>".json_indent($response)."</pre>\n";
  echo "<br><br>\n";

  if(php_sapi_name() != "cli") {
    flush();
    ob_flush();
  }

  return $response;
}
?>
