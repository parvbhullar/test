<?php

include_once("vendor/autoload.php");

// If you don't want to setup permissions the proper way, just uncomment the following PHP line
// read http://symfony.com/doc/current/book/installation.html#configuration-and-setup for more information
//umask(0000);

class UpdateProducts
{
    function __construct()
    {
        //echo ("Auto bucketing for profiles\n");
    }

    public function handleRestCall()
    {
        try {
//            $inputFileName = './php-excel/final-item-suggestion-sheet.xlsx';
            $inputFileName = './data/bigbasket-price-check.xlsx';
            $rows = $this->readExcel($inputFileName);
            $this->updateMongo($rows);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function readExcel($inputFileName)
    {
        echo 'Loading file ', pathinfo($inputFileName, PATHINFO_BASENAME), ' using PHPExcel_Reader_Excel5<br />';
//        $objReader = new PHPExcel_Reader_Excel5();
        $objReader = new PHPExcel_Reader_Excel2007();
//	$objReader = new PHPExcel_Reader_Excel2003XML();
//	$objReader = new PHPExcel_Reader_OOCalc();
//	$objReader = new PHPExcel_Reader_SYLK();
//	$objReader = new PHPExcel_Reader_Gnumeric();
//	$objReader = new PHPExcel_Reader_CSV();
        $objPHPExcel = $objReader->load($inputFileName);
//        echo '<hr />';
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
        return $sheetData;
    }

//
//barcode
//BB ID
//Done BY
//name	quantity	Qty in BB	Correct	MRP Price	price	category	categoryId	subCategory	subCategoryId
    public function updateMongo($rows)
    {

        //    55cd2a13f41d99e551778361 , 1159 line number updated to prod

        $dbhost = '178.63.79.68'; //188.40.100.77  QA, 178.63.79.68 Prod
//        $dbhost = '188.40.100.77'; //188.40.100.77  QA, 178.63.79.68 Prod
//        $dbhost = 'localhost'; //188.40.100.77  QA, 178.63.79.68 Prod
        $dbname = 'NinjaCart';
// Connect to test database
//$m = new Mongo("mongodb://$dbhost");
        $m = new Mongo("mongodb://Ninja_Cart_Admin:Ucanwin888@$dbhost/NinjaCart", array("connectTimeoutMS" => "180000"));
//        $m =  new Mongo("mongodb://$dbhost/NinjaCart");
        $db = $m->$dbname;

// select the collection
        $collection = $db->ProductMasterTable;

// pull a cursor query
//$cursor = $collection->find();
        //get all products from excel file
        $i = 0;
        $startFrom = 0;
        foreach ($rows as $line) {
            if ($i == 0 || $i <= $startFrom) {
                $i++;
                continue;
            }
//            print_r($line);exit;
//            $lArr = explode("\t", $line);
            $barCode = trim($line["A"]);
            $bbBarCode = trim($line["B"]);
            $pId = trim($line["C"]);

            $product = $line["H"];
            $brand = $line["I"];
            $variant = $line["J"];

//            echo "Updating for $pId\n";
//            try {
//                $pId = new MongoId($pId);
//            } catch (Exception $ex) {
//                print_r($ex->getMessage());
//                $pId = false;
//            }
            echo "$barCode - $bbBarCode \n";

            if (trim($bbBarCode)) {
                $result = $collection->update(
                    array("barcode" => $barCode),
                    array('$set' =>
                        array(
                            "bigBasketBarcode" => trim($bbBarCode) == "0" ? "" : trim($bbBarCode)
                        )
                    ),
                    array(
                        'upsert' => false,
                        'multiple' => true
                    )
                );
//                print_r($result)
            }
            $i++;

//            if ($i > 2)
//                break;
        }
    }
}

$autoBucket = new UpdateProducts();
$autoBucket->handleRestCall();



