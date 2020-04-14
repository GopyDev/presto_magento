<?php 
ini_set('memory_limit', '3G');
set_time_limit(360000);
error_reporting(E_ALL | E_STRICT);
require_once 'app/Mage.php';
ini_set('display_errors', 1);
umask(0);
Mage::app();

$countrycode = @$_GET['country'];
$state = "<option value=''>Please Select</option>";
if ($countrycode != '') {
	$statearray = Mage::getModel('directory/region')->getResourceCollection() ->addCountryFilter($countrycode)->load();
	foreach ($statearray as $_state) {
		$state .= "<option value='" . $_state->getCode() . "'>" . $_state->getDefaultName() . "</option>";
	}
}
echo $state;
?>