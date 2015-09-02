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
//$server = "178.63.79.68";
$server = "188.40.100.77";
$config = array(
    'adapteroptions' => array(
        'host' => $server,
        'port' => 8080,
        'path' => '/solr/',
        'core' => 'collectionv2'
    )
);

$limit = 5;
$offset = 0;
// create a client instance
$client = new Solarium_Client($config);

// get a select query instance
$query = $client->createSelect();

$keyword = isset($argv[1]) ? $argv[1] : "*";
//$keyword = str_replace(" ", "*", $keyword);
//$matchingText = explode(" ",trim($keyword));
//for($i=0; $i<count($matchingText);$i++){
//    if($i == 0){
//        $keyword = $matchingText[$i]."+".$matchingText[$i]."*";
//    }else{
//        $keyword = $keyword."+".$matchingText[$i]."+".$matchingText[$i]."*";
//    }
//}

$nq = $keyword;
if (strpos($keyword,' ') !== false) {
    echo 'true';
    $nq = str_replace(" ", " ", $keyword);
}
$q = ("(name:($nq)) OR (name_s:\"$keyword\")^5 OR (name_suggester:\"$keyword\")^5");

// apply settings using the API
$query->setQuery($q);
$query->setStart($offset)->setRows($limit);
//$query->setFields(array('id','name','price'));
//$query->addSort('price', $query::SORT_ASC);

//$resultset = $client->select($query);

//print_r(json_decode($resultset->getResponse()->getBody(), true));
//
$aq = urlencode("name:(Onion OR Pickle)");
$q = urlencode($q);
$url = "http://$server:8080/solr/collectionv2/select?q=$q&rows=$limit&start=$offset&wt=json&indent=true";
$code = json_decode(file_get_contents($url),true);

print_r($code);

?>
