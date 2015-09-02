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

foreach($arr as $line){
    $lArr = explode("\t", $line);
    $barCode = trim($lArr[0]);
    $pId = trim($lArr[1]);

    //get the file and rename
    //
    //http://finisfun.com/mavericks/
    rename("/tmp/tmp_file.txt", "/home/user/login/docs/my_file.txt");
}