<?php
$endpoint = 'https://api.upcitemdb.com/prod/trial/lookup';

$ch = curl_init();
/* if your client is old and doesn't have our CA certs
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);*/
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
curl_setopt($ch,CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Accept: application/json",
  "Accept-Encoding: gzip,deflate"
));

// HTTP GET
curl_setopt($ch, CURLOPT_POST, 0);
curl_setopt($ch, CURLOPT_URL, $endpoint.'?upc=4002293401102');
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if ($httpcode != 200)
  echo "error status $httpcode...\n";
else 
  echo $response."\n";
/* if you need to run more queries, do them in the same connection.
 * use rawurlencode() instead of URLEncode(), if you set search string
 * as url query param
 * for search requests, change to sleep(6)
 */
sleep(2);
// proceed with other queries
curl_close($ch);