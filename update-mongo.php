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

// pull a cursor query
//$cursor = $collection->find();
foreach($arr as $line){
    $lArr = explode("\t", $line);
    $barCode = trim($lArr[0]);
    $pId = trim($lArr[1]);

    $baseUrl = 'http://d2k9z4241j7cyd.cloudfront.net/';
    $basePath = '/home/gce3/nc/OrderImage/';
    $filename = $basePath.$pId.".png";
    if (file_exists($filename)) {
        echo "The file $filename exists\n";
        $nFileName = $baseUrl.$pId.".png";
        echo "New file url is ". $nFileName."\n";
        $collection->update(array("_id" => new MongoId($pId)), array('$set'=> array("imageUrl" => $nFileName)));
        $nCount++;
    } else {
        $nFileName = $baseUrl.$barCode.".png";
        echo "File not exists, not changing url - ". $nFileName."\n";
    }

    if($nCount > 2)
        break;
}

//http://ninjaorderimages.s3-us-west-2.amazonaws.com/OrderImage/
// s3cmd put -r dir1 s3://ninjaorderimages.s3-us-west-2.amazonaws.com/OrderImage
echo "Total not found files $nCount\n";