#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
function doRegister($firstName, $lastName, $password, $username)
{
    $firstName = sanitize($firstName);
    $lastName = sanitize($lastName);
    $password = sanitize($password);
    $username = sanitize($username);
    $mydb = new mysqli('127.0.0.1', 'testUser', '12345', 'testdb');
    if ($mydb->errno != 0) {
        echo "failed to connect to database: " . $mydb->error . PHP_EOL;
        return array("returnCode" => '0', 'message' => "Database is not connected");
    } else {
        $exist = "SELECT COUNT(*) as c FROM Users WHERE username = '$username'";
        $checkExist = $mydb->query($exist);
        if($checkExist === false){
          return array("returnCode" => '0', 'message' => "failed to check username");
        }
        $r = $checkExist->fetch_assoc();
        $user = (int)$r['c'];
        if($user > 0){
          return array("returnCode" => '0', 'message' => "Username already exists");
        }
        $query = "INSERT INTO Users (firstName, lastName, password, browserSession, username, JWTTime) 
                  VALUES (?, ?, ?, '', ?, 0)";
        $stmt = $mydb->prepare($query);
        $stmt->bind_param("ssss", $firstName, $lastName, $password, $username);
        $stmt->execute();
        if ($stmt->errno != 0) {
            echo "failed to execute query:" . PHP_EOL;
            echo __FILE__ . ':' . __LINE__ . ":error: " . $stmt->error . PHP_EOL;
            return array("returnCode" => '0', 'message' => "Failed to execute");
        }
        $stmt->close();
        return array("returnCode" => '1', 'message' => "Account is registered and connected");
    }
}
function doLogin($username, $password){
  $username = sanitize($username);
  $password = sanitize($password);
  $mydb = new mysqli('127.0.0.1', 'testUser', '12345', 'testdb');
  if ($mydb->errno != 0) {
    echo "failed to connect to database: " . $mydb->error . PHP_EOL;
      return array("returnCode" => '0', 'message' => "Database is not connected");
  }else{
    $query = "SELECT * FROM Users WHERE username = ?";
    $stmt = $mydb->prepare($query);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $res = $stmt->get_result();
    if($res->num_rows === 1){
      $row = $res->fetch_assoc();
      if(password_verify($password, $row['password'])){
        $ExpTime = time() + 3600;
        $jwtT = JWTtok($row['username'], $ExpTime);
        $update = "UPDATE Users SET browserSession = ?, JWTTime = ? WHERE username = ?";
        $stmtup = $mydb->prepare($update);
        $stmtup->bind_param('sis', $jwtT, $ExpTime, $row['username']);
        $stmtup->execute();
        $stmtup->close();
        $wishlistQ = "SELECT ItemTitle FROM Wishlist WHERE UserID = ?";
        $wishlistStmt = $mydb->prepare($wishlistQ);
        $wishlistStmt->bind_param('i', $row['UserID']);
        $wishlistStmt->execute();
        $wishlistR = $wishlistStmt->get_result();
        $wishlist = [];
        while ($wishlistRow = $wishlistR->fetch_assoc()){
          $wishlist[] =$wishlistRow['ItemTitle'];
        }
        $wishlistStmt->close();
        $stmt->close();
        return array("returnCode" => '1', 'message' => 'Login Successful', 'jwt' => $jwtT, 'wishlist' => $wishlist);
      }else{
        $stmt->close();
        return array("returnCode" => '0', 'message' => 'Invalid login/password');
      }
    }else{
      $stmt->close();
      return array("returnCode" => '0', 'message' => 'Invalid login/password');
    }
  }
}
function doLogout($username){
    $username = sanitize($username);
    $mydb = new mysqli('127.0.0.1', 'testUser', '12345', 'testdb');
    if ($mydb->errno != 0) {
    echo "failed to connect to database: " . $mydb->error . PHP_EOL;
      return array("returnCode" => '0', 'message' => "Database is not connected");
    }else{
      $query = "UPDATE Users SET browserSession = '', JWTTime = 0 WHERE username = ?";
      $stmtup = $mydb->prepare($query);
      $stmtup->bind_param('s', $username);
      $stmtup->execute();
      $stmtup->close();
      return array("returnCode" => '1', 'message' => 'Logged Out');
    }

}
function doWishlist($username, $wishlistItems) {
    $username = sanitize($username);
    $mydb = new mysqli('127.0.0.1', 'testUser', '12345', 'testdb');
    if ($mydb->errno != 0) {
        echo "Failed to connect to database: " . $mydb->error . PHP_EOL;
        return array("returnCode" => '0', 'message' => "Database is not connected");
    } else {
        $userID = getUserID($mydb, $username);
        if ($userID === false) {
            return array("returnCode" => '0', 'message' => "User not found");
        }
        $insertQuery = "INSERT INTO Wishlist (UserID, ItemTitle, Price, ItemURL) VALUES (?, ?, ?, ?)";
        $insertStmt = $mydb->prepare($insertQuery);
        $insertStmt->bind_param('isds', $userID, $itemTitle, $price, $itemURL);
        foreach ($wishlistItems as $item) {
            $itemData = explode('|', $item);
            $itemTitle = $itemData[0];
            $price = preg_replace("/[^0-9.]/", "", $itemData[1]);
            $price = (float) $price;
            $itemURL = $itemData[2];
            $insertStmt->execute();
        }
        $insertStmt->close();

        return array("returnCode" => '1', 'message' => "Wishlist stored successfully");
    }
}
function fetchWishlist($username)
{
    $mydb = new mysqli('127.0.0.1', 'testUser', '12345', 'testdb');
    if ($mydb->connect_error) {
        return array("returnCode" => '0', 'message' => "Database connection error");
    }
    $username = sanitize($username);
    $userID = getUserID($mydb, $username);
    if ($userID === false) {
        $mydb->close();
        return array("returnCode" => '0', 'message' => "User not found");
    }
    $query = "SELECT ItemTitle, Price, ItemURL FROM Wishlist WHERE UserID = ?";
    $stmt = $mydb->prepare($query);
    $stmt->bind_param('i', $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $wishlist = [];
    while ($row = $result->fetch_assoc()) {
        $wishlist[] = array(
            'ItemTitle' => $row['ItemTitle'],
            'Price' => $row['Price'],
            'ItemURL' => $row['ItemURL']
        );
    }
    $stmt->close();
    $mydb->close();
    return array("returnCode" => '1', 'message' => 'Wishlist retrieved successfully', 'wishlist' => $wishlist);
}
function clearWishlist($username, $itemTitles)
{
    $mydb = new mysqli('127.0.0.1', 'testUser', '12345', 'testdb');
    if ($mydb->connect_error) {
        return array("returnCode" => '0', 'message' => "Database connection error");
    }
    $username = sanitize($username);
    $userID = getUserID($mydb, $username);
    if ($userID === false) {
        $mydb->close();
        return array("returnCode" => '0', 'message' => "User not found");
    }
    $deleteQuery = "DELETE FROM Wishlist WHERE UserID = ? AND ItemTitle = ?";
    $deleteStmt = $mydb->prepare($deleteQuery);
    $deleteStmt->bind_param('is', $userID, $itemTitle);
    foreach ($itemTitles as $itemTitle) {
        $deleteStmt->execute();
    }
    $deleteStmt->close();
    $mydb->close();

    return array("returnCode" => '1', 'message' => "Items removed from wishlist successfully");
}
function submitReview($username, $itemTitle, $reviewText) {
    $mydb = new mysqli('127.0.0.1', 'testUser', '12345', 'testdb');
    if ($mydb->connect_error) {
        return array("returnCode" => '0', 'message' => "Database connection error");
    }
    $username = sanitize($username);
    $userID = getUserID($mydb, $username);
    if ($userID === false) {
        $mydb->close();
        return array("returnCode" => '0', 'message' => "User not found");
    }
    $timestamp = date('Y-m-d H:i:s');
    $insertQuery = "INSERT INTO Reviews (UserID, ItemTitle, ReviewText, Username, Timestamp) VALUES (?, ?, ?, ?, ?)";
    $insertStmt = $mydb->prepare($insertQuery);
    $insertStmt->bind_param('issss', $userID, $itemTitle, $reviewText, $username, $timestamp);
    $insertStmt->execute();
    $insertStmt->close();
    $mydb->close();
    return array("returnCode" => '1', 'message' => "Review submitted successfully");
}
function fetchReviews()
{
    $mydb = new mysqli('127.0.0.1', 'testUser', '12345', 'testdb');
    if ($mydb->connect_error) {
        return array("returnCode" => '0', 'message' => "Database connection error");
    }

    $query = "SELECT * FROM Reviews";
    $result = $mydb->query($query);

    if (!$result) {
        return array("returnCode" => '0', 'message' => "Error fetching reviews");
    }

    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $reviews[] = array(
            'UserID' => $row['UserID'],
            'ItemTitle' => $row['ItemTitle'],
            'ReviewText' => $row['ReviewText'],
            'Username' => $row['Username'],
            'Timestamp' => $row['Timestamp']
        );
    }

    $result->close();
    $mydb->close();

    return array("returnCode" => '1', 'message' => 'Reviews retrieved successfully', 'reviews' => $reviews);
}
function getUserID($mydb, $username) {
    $query = "SELECT UserID FROM Users WHERE username = ?";
    $stmt = $mydb->prepare($query);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return ($row) ? $row['UserID'] : false;
}
function sanitize($input) {
    $mydb = new mysqli('127.0.0.1', 'testUser', '12345', 'testdb');
    if ($mydb->connect_error) {
        die("Connection failed: " . $mydb->connect_error);
    }
    $sanitizedInput = $mydb->real_escape_string($input);
    $mydb->close();
    return $sanitizedInput;
}
function JWTtok($username, $expire){
    $secret = "secret";
    $data = array(
        'username' => $username,
        'time' => $expire
    );
    $jwt = Firebase\JWT\JWT::encode($data, $secret, 'HS256');
    return $jwt;
}
/*function removeJWT(){
  $time = time();
  $mydb = new mysqli('127.0.0.1', 'testUser', '12345', 'testdb');
  $sql = "DELETE FROM Users WHERE"
  $stmt = $mydb->prepare)
}*/
function requestProcessor($request)
{
    echo "received request".PHP_EOL;
    var_dump($request);
    if (!isset($request['type'])) {
        return "ERROR: unsupported message type";
    }
    switch ($request['type']) {
        case 'Register':
            if(
              isset($request['firstName']) && isset($request['lastName']) &&
              isset($request['password']) && isset($request['username'])
            ){
                return doRegister(
                    $request['firstName'],
                    $request['lastName'],
                    $request['password'],
                    $request['username']
                );
            }else {
              return array("returnCode" => '0', 'message' => 'Missing');
            }
            break;
        case 'Login':
            if(
              isset($request['username']) && isset($request['password'])
            ){
              return doLogin(
                $request['username'],
                $request['password']
              );
            }else{
              return array("returnCode" => '0', 'message' => 'Missing');
            }
            break;
        case 'Logout':
            if(
              isset($request['username'])
            ){
              return doLogout(
                $request['username']
              );
            }else{
              return array("returnCode" => '0', 'message' => 'Not Logged In');
            }
            break;
        case 'Wishlist':
            if (isset($request['username']) && isset($request['wishlist'])) {
                return doWishlist($request['username'], $request['wishlist']);
            } else {
                return array("returnCode" => '0', 'message' => 'Missing data for wishlist');
            }
            break;
        case 'FetchWishlist':
            if(isset($request['username'])){
              return fetchWishlist($request['username']);
            }else{
              return array("returnCode" => '0', 'message' => 'Missing username for fetching wishlist');
            }
            break;
        case 'ClearWishlist':
            if (isset($request['username']) && isset($request['itemTitle'])) {
                return clearWishlist($request['username'], $request['itemTitle']);
            } else {
                return array("returnCode" => '0', 'message' => 'Missing data for clearing wishlist');
            }
            break;
        case 'SubmitReview':
            if (isset($request['username']) && isset($request['itemTitle']) && isset($request['reviewText'])) {
                return submitReview($request['username'], $request['itemTitle'], $request['reviewText']);
            } else {
                return array("returnCode" => '0', 'message' => 'Missing data for submitting review');
            }
            break;
        case 'FetchReviews':
            return fetchReviews();
            break;
    }
}
$server = new rabbitMQServer("testRabbitMQ.ini", "testServer");
$server->process_requests('requestProcessor');
exit();
?>
