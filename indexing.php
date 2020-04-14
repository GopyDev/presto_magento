<?php
require_once 'Mage.php';
$app = Mage::app('admin');
for ($indexer = 1; $indexer <= 9; $indexer++)
{
	
     $reindexProcess = Mage::getModel('index/process')->load($indexer);
     $reindexProcess->reindexAll();
	 echo $indexer;
}
/*
where $indexer ids are for
1 = Product Attributes
2 = Product Price
3 = Catalog URL Rewrites
4 = Product Flat Data
5 = Category Flat Data
6 = Category Products
7 = Catalog Search Index
8 = Stock Status
9 = Tag Aggregation Data
*/
?>