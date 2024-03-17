#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once 'vendor/autoload.php';
function searchProduct($productName)
{
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://ebay-search-result.p.rapidapi.com/search/{$productName}",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "X-RapidAPI-Host: ebay-search-result.p.rapidapi.com",
            "X-RapidAPI-Key: 3bc5931b43msh2b05b74223976d9p1d275bjsn0a5404ff0276",
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
        return array("returnCode" => '0', 'message' => "Error", 'responseData' => null);
    } else {
        $decodedResponse = json_decode($response, true);
        if ($decodedResponse && isset($decodedResponse['results'])) {
            return array("returnCode" => '1', 'message' => "Product Found", 'responseData' => $decodedResponse['results']);
        } else {
            echo "Error decoding JSON response or 'results' key not found";
            return array("returnCode" => '0', 'message' => "Error decoding JSON response or 'results' key not found", 'responseData' => null);
        }
    }
}
function Recommend($commonWord)
{
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://ebay-search-result.p.rapidapi.com/search/{$commonWord}",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "X-RapidAPI-Host: ebay-search-result.p.rapidapi.com",
            "X-RapidAPI-Key: 3bc5931b43msh2b05b74223976d9p1d275bjsn0a5404ff0276",],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
        return array("returnCode" => '0', 'message' => "Error", 'responseData' => null);
    } else {
        $decodedResponse = json_decode($response, true);
        if ($decodedResponse && isset($decodedResponse['results'])) {
            return array("returnCode" => '1', 'message' => "Recommendations Found", 'responseData' => $decodedResponse['results']);
        } else {
            echo "Error decoding JSON response or 'results' key not found";
            return array("returnCode" => '0', 'message' => "Error decoding JSON response or 'results' key not found", 'responseData' => null);
        }
    }
}

function requestProcessor($request)
{
    echo "received request".PHP_EOL;
    var_dump($request);
    if (!isset($request['type'])) {
        return "ERROR: unsupported message type";
    }
    switch ($request['type']) {
        case 'searchProduct':
            if(
              isset($request['productName'])
            ){
              return searchProduct(
                $request['productName']
              );
            }else{
              return array("returnCode" => '0', 'message' => 'Not set');
            }
        case 'Recommendation':
            if(isset($request['commonWord'])
            ){
                return Recommend(
                    $request['commonWord']
                );
            }else{
                return array("returnCode" => '0', 'message' => 'Not set');
            }
    }
}
$server = new rabbitMQServer("testRabbitMQ.ini", "testDMZ");
$server->process_requests('requestProcessor');
exit();
?>
