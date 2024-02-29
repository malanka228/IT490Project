<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.upcdatabase.org/search/?query=The%20Dog&page=1',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer 6BBC5DB15EAEA0F840373568E2E5AFDE',
    'Cookie: upcdatabaseorg=k1ojqnark963pr3ae2jt4rb4sk'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
