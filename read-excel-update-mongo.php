<?php

include_once("vendor/autoload.php");

// If you don't want to setup permissions the proper way, just uncomment the following PHP line
// read http://symfony.com/doc/current/book/installation.html#configuration-and-setup for more information
//umask(0000);

class UpdateProducts
{
    function __construct(){
        //echo ("Auto bucketing for profiles\n");
    }

    public function handleRestCall() {
        try {
            $inputFileName = './php-excel/final-item-suggestion-sheet.xlsx';
            $rows = $this->readExcel($inputFileName);
            $this->updateMongo($rows);
        } catch(\Exception $e) {
            throw $e;
        }
    }

    public function readExcel($inputFileName){
        echo 'Loading file ',pathinfo($inputFileName,PATHINFO_BASENAME),' using PHPExcel_Reader_Excel5<br />';
//        $objReader = new PHPExcel_Reader_Excel5();
        $objReader = new PHPExcel_Reader_Excel2007();
//	$objReader = new PHPExcel_Reader_Excel2003XML();
//	$objReader = new PHPExcel_Reader_OOCalc();
//	$objReader = new PHPExcel_Reader_SYLK();
//	$objReader = new PHPExcel_Reader_Gnumeric();
//	$objReader = new PHPExcel_Reader_CSV();
        $objPHPExcel = $objReader->load($inputFileName);
//        echo '<hr />';
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        return $sheetData;
    }

//[A] barcode => 8.44E+10
//[B] d => 0
//[C] _id => 55cd2a14f41d99e5517787eb
//[D] TYPE=>
//[E] NAME => St Dalfour Wild Blueberry Jam
//[F] quantity=> 284 g
//[G] Price => 240
//[H] product => Jam
//[I] brand => St Dalfour
//[J] variant => Wild Blueberry
//[K] category => Preserved & Ready To Cook Items
//[L] categoryId => 4
//[M] subCategory => Spreads & Jam
//[N] subCategoryId => 9
//[O] imageUrl => http://d2k9z4241j7cyd.cloudfront.net/55cd2a14f41d99e5517787eb.png
//[P] city => Bangalore
//[Q] cityId => 1
//[R] Store Sales Data => 2
//[S] NC Sales Data => 0

    public function updateMongo($rows){
        $dbhost = '178.63.79.68'; //188.40.100.77  QA, 178.63.79.68 Prod
        $dbhost = '188.40.100.77'; //188.40.100.77  QA, 178.63.79.68 Prod
//        $dbhost = 'localhost'; //188.40.100.77  QA, 178.63.79.68 Prod
        $dbname = 'NinjaCart';
// Connect to test database
//$m = new Mongo("mongodb://$dbhost");
        $m =  new Mongo("mongodb://Ninja_Cart_Admin:Ucanwin888@$dbhost/NinjaCart", array("connectTimeoutMS" => "180000"));
//        $m =  new Mongo("mongodb://$dbhost/NinjaCart");
        $db = $m->$dbname;

// select the collection
        $collection = $db->ProductMasterTable;

// pull a cursor query
//$cursor = $collection->find();
        //get all products from excel file
        $i = 0;
        foreach($rows as $line){
            if($i == 0){
                $i++;
                continue;
            }
//            print_r($line);
//            $lArr = explode("\t", $line);
            $barCode = trim($line["A"]);
            $pId = trim($line["C"]);

            $product = $line["H"];
            $brand = $line["I"];
            $variant = $line["J"];

            echo "Updating for $pId\n";
            try{
                $pId = new MongoId($pId);
            } catch(Exception $ex){
                print_r($ex->getMessage());
                $pId = false;
            }

            if($pId){
                $result =  $collection->update(
                    array("_id" => $pId),
                    array('$set'=>
                        array(
                            "product" => trim($product),
                            "brand" => trim($brand),
                            "variant" => trim($variant)
                        )
                    ),
                    array(
                        'upsert' => false,
                        'multiple' => true
                    )
                );
//                print_r($result);
                if($result){
                    echo "Updating ProductShopTable for $pId\n";
                    $collection2 = $db->ProductShopTable;
                    $result =  $collection2->update(
                        array("productId" => $pId),
                        array('$set'=>
                            array(
                                "product" => trim($product),
                                "brand" => trim($brand),
                                "variant" => trim($variant)
                            )
                        ),
                        array(
                            'upsert' => false,
                            'multiple' => true
                        )
                    );
//                    print_r($result);
                }
            }
            $i++;

//    if($nCount > 2)
//        break;
        }
    }
}

$autoBucket = new UpdateProducts();
$autoBucket->handleRestCall();



