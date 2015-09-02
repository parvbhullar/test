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
foreach($arr as $line){
    $lArr = explode("\t", $line);
    $barCode = trim($lArr[0]);
    $pId = trim($lArr[1]);
    //get the file and rename
    //
    //
    //http://finisfun.com/mavericks/

    $basePath = '/home/gce3/nc/OrderImage/';
    $filename = $basePath.$barCode.".png";
    if (file_exists($filename)) {
        echo "The file $filename exists\n";
        $nFileName = $basePath.$pId.".png";
        echo "New file name is ". $nFileName;
        rename($filename, $nFileName);

    } else {
        $nCount++;
        echo "The file $filename does not exist\n";
    }
}

echo "Total not found files $nCount\n";