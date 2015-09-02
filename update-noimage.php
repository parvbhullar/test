<?php
/**
 * Created by PhpStorm.
 * User: hp 450
 * Date: 9/2/15
 * Time: 3:44 PM
 */

$file = __DIR__."/data/barcode-pid.txt";

$content = file_get_contents($file);

$arr = explode("\n", $content);
$nCount = 0;

$dbhost = '178.63.79.68'; //188.40.100.77  QA, 178.63.79.68 Prod
$dbname = 'NinjaCart';

// Connect to test database
//$m = new Mongo("mongodb://$dbhost");
$m =  new Mongo("mongodb://Ninja_Cart_Admin:Ucanwin888@$dbhost/NinjaCart");
$db = $m->$dbname;

// select the collection
$collection = $db->ProductMasterTable;

$baseUrl = 'http://d2k9z4241j7cyd.cloudfront.net/';
$basePath = '/home/gce3/nc/OrderImage/';
$nFileName = $baseUrl."noimage.png";

$collection->update(array("imageUrl" => "http://finisfun.com/mavericks/OrderImage/noimage.png"),
    array('$set'=> array("imageUrl" => $nFileName)),
    array("upsert" => true, "multiple" => true)
);

//http://ninjaorderimages.s3-us-west-2.amazonaws.com/OrderImage/
// s3cmd put -r dir1 s3://ninjaorderimages.s3-us-west-2.amazonaws.com/OrderImage
echo "Total found files $nCount\n";