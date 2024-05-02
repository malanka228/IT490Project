<?php

$ch = curl_init('https://textbelt.com/text');
$data = array(
  'phone' => '2017791019',
  'message' => 'PricifyInspect Alert' . PHP_EOL .'Hello there, An item you have wishlisted is now available at a lower price!',
  'key' => '8744a12a45acd1f135117147582f54d9cd99923anzLl36JKrCnL2njBmO7MRezTI_test',
);

curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
echo $response;
curl_close($ch);

?>