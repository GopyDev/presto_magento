@echo off
set jsonpath=../json
set baseapiurl=http://vrp.viamente.com/api/vrp/v2
set key=YOUR_API_KEY_HERE

echo List available subfleets
call :curlWrap "%baseapiurl%/subfleets"

echo List saved routeplans
call :curlWrap "%baseapiurl%/routeplans"

echo Create new routeplan
call :curlWrap "%baseapiurl%/routeplans" -d "@%jsonpath%/create_new_routeplan_request.json"

echo List saved routeplans
call :curlWrap "%baseapiurl%/routeplans"

echo Done

goto :eof

:curlWrap
echo.
set url=%~1?key=%key%
set post=%2 %3
curl.exe --header "Accept: application/json" --header "Content-Type: application/json" %post% %url%

echo.
echo.
pause
goto :eof
