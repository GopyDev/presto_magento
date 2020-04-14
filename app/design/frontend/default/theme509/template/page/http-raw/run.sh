#!/bin/bash
jsonpath=../json
baseapiurl=http://vrp.viamente.com/api/vrp/v2
key=YOUR_API_KEY_HERE

function curlWrap {
  url=$1?key=$key
  post=$2\ $3
  echo
  ## NOTE: For pretty printed output add "| python -mjson.tool" to the line below
  curl -s --header "Accept: application/json" --header "Content-Type: application/json" $post $url
  echo
  echo
  echo "Press Enter to continue..."
  read -r
}

echo "List available subfleets"
curlWrap "$baseapiurl/subfleets"

echo "List saved routeplans"
curlWrap "$baseapiurl/routeplans"

echo "Create new routeplan"
curlWrap "$baseapiurl/routeplans" -d "@$jsonpath/create_new_routeplan_request.json"

echo "List saved routeplans"
curlWrap "$baseapiurl/routeplans"

echo "Done"
