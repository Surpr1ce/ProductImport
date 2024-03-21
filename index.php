<?php
global $dsn, $username, $password, $options;
include_once "ProductImport.php";
include_once "Config.php";

$database = new Database($dsn, $username, $password, $options);
$product_import = new ProductImport($database);
$product_import->import('sourceData/stockData.csv', 'sourceData/commonData.csv');