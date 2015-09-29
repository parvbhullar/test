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
$dir = "G:\\NinjaDocs\\All images\\";
$dh  = opendir($dir);
$baseUrl = 'http://d2k9z4241j7cyd.cloudfront.net/';
while (false !== ($filename = readdir($dh))) {
    echo "File name ".$filename."\n";
    $files[] = $filename;
    $fArr = explode(".", $filename);
    $objId = new MongoId();
    try{
        $objId = new MongoId($fArr[0]);
    } catch (\Exception $ex){

    }

    $obj = $collection->findOne(array("_id" => $objId));
    if($obj){
        $filename = $baseUrl.$obj["_id"].".png";
        //For renaming the file
//        $oldFileName = $dir.$fArr[0].".png";
//        $newFileName = $dir.$obj["_id"].".png";
        echo "New file name is ". $filename."\n";
//        rename($oldFileName, $newFileName);
//        break;

//        $filename = $baseUrl.$obj["_id"].".png";
//        echo "New file url is ". $filename."\n";
        $collection->update(array("_id" => new MongoId($obj["_id"])), array('$set'=> array("imageUrl" => $filename)));
    } else {
        echo "Product not found for ". $fArr[0]."\n";
    }
}


//http://ninjaorderimages.s3-us-west-2.amazonaws.com/OrderImage/
// s3cmd put -r dir1 s3://ninjaorderimages.s3-us-west-2.amazonaws.com/OrderImage
echo "Total not found files $nCount\n";