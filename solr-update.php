<?php
/**
 * Created by PhpStorm.
 * User: hp 450
 * Date: 9/2/15
 * Time: 1:07 PM
 */
require(__DIR__.'/vendor/autoload.php');

//$config = array(
//    'endpoint' => array(
//        'localhost' => array(
//            'host' => '188.40.100.77',
//            'port' => 8080,
//            'path' => '/solr',
//            'timeout'=> 50,
//            'core' => 'collectionv2',
//        )
//    )
//);

$limit = 1000;
$offset = 0;
// create a client instance
$client = new Solarium_Client($config);

// get a select query instance
$query = $client->createSelect();

$limit = isset($argv[2]) ? $argv[2] : 1000;
$offset = isset($argv[1]) ? $argv[1] : 0;
//$keyword = str_replace(" ", "*", $keyword);
//$matchingText = explode(" ",trim($keyword));
//for($i=0; $i<count($matchingText);$i++){
//    if($i == 0){
//        $keyword = $matchingText[$i]."+".$matchingText[$i]."*";
//    }else{
//        $keyword = $keyword."+".$matchingText[$i]."+".$matchingText[$i]."*";
//    }
//}

// apply settings using the API
//$query->setQuery($q);
//$query->setStart($offset)->setRows($limit);
//$query->setFields(array('id','name','price'));
//$query->addSort('price', $query::SORT_ASC);

//$resultset = $client->select($query);

//print_r(json_decode($resultset->getResponse()->getBody(), true));
//

//$keyword = trim(getField('matchingText'));
$nq = $keyword;
//if (strpos($keyword,' ') !== false) {
////    echo 'true';
//    $nq = str_replace(" ", " ", trim($keyword));
//}

$partnerId = "55787dcbf41d992b0e8d3e46";

$q = ("(name:($nq)) OR (name:($nq*)) OR (name_suggest:($keyword)^5)");
$q = "($q) AND (partnerId:$partnerId)";

$q = "*:*";
$q = urlencode($q);

reindex($q, $limit, $offset);


function reindex($q, $limit = 1000, $offset = 0)
{
    $server = "178.63.79.68";
//$server = "188.40.100.77";

    $updateServer = $server;//"188.40.100.77";
//$url = "http://$server:8080/solr/collectionv2/select?q=$q&rows=$limit&start=$offset&wt=json&indent=true";
    $url = "http://$server:8080/solr/collectionv2/select?q=$q&rows=$limit&start=$offset&wt=json&indent=true";
    $results = json_decode(file_get_contents($url), true);
//Get total again push it
    $results = $results["response"];

//    print_r($results);

    $numFound = $results["numFound"];
    $docs = isset($results["docs"]) ? $results["docs"] : false;

    $transaction = $docs;

    if ($transaction) {

        $dq = "update?stream.body=<delete><query>*:*</query></delete>&commit=true";
//        $ch = curl_init();
//        $url = 'http://178.63.79.68:8080/solr/collectionv2/update?stream.body=%3Cdelete%3E%3Cquery%3EpartnerId:' . $partnerId . '%3C/query%3E%3C/delete%3E&commit=true';
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
//        curl_setopt($ch, CURLOPT_POST, TRUE);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
//        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($transaction));
//        $response = curl_exec($ch);
//        if ($response === FALSE) {
//            die('Curl failed: ' . curl_error($ch));
//        }
//
//        // Close connection
//        curl_close($ch);


//        $transaction = $GLOBALS['DB']->v2GetDirtySkuForSolrUpload($partnerId);
        $ch = curl_init();
        $url = "http://$updateServer:8080/solr/collectionv2/update?wt=json&commit=true";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($transaction));
        $response = curl_exec($ch);
        if ($response === FALSE) {
            print_r('Curl failed: ' . curl_error($ch));
        }

        // Close connection
        curl_close($ch);

        if($numFound > $offset){
            $offset = $offset + $limit;
            echo "Reindex started - ". $offset.PHP_EOL;
            reindex($q, $limit, $offset);
        }

    }
}

echo "Success Done";

?>
