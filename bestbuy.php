<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eBay Search</title>
</head>
<body>

<form method="get" action="">
    <label for="searchTerm">Enter item to search:</label>
    <input type="text" id="searchTerm" name="searchTerm" required>
    <button type="submit">Search</button>
</form>

<?php

if (isset($_GET['searchTerm'])) {
    $searchTerm = urlencode($_GET['searchTerm']);

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://target1.p.rapidapi.com/products/v3/get-details?tcin={$searchTerm}&store_id=911",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "X-RapidAPI-Host: target1.p.rapidapi.com",
            "X-RapidAPI-Key: 3bc5931b43msh2b05b74223976d9p1d275bjsn0a5404ff0276",
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        // Decode the JSON response into an associative array
        $data = json_decode($response, true);

        // Check if the decoding was successful and if 'product' key exists
        if ($data !== null && isset($data['data'])) {
            // Access 'product' array
            $product = $data['data'];

            // Traverse through nested arrays to access 'reg_price'
            $price = traverseArray($product, ['product', 'price', 'reg_retail']);

            // Display the extracted data
            echo "<b>Regular Price:</b> $price" . "<br>";

            // ... You can access other elements within the 'product' array as needed ...
        } else {
            echo "Error decoding JSON response or 'product' key not found";
        }
    }
}

// Function to traverse nested arrays
function traverseArray($array, $keys) {
    foreach ($keys as $key) {
        if (isset($array[$key])) {
            $array = $array[$key];
        } else {
            return null; // Key not found
        }
    }
    return $array;
}

?>
</body>
</html>
