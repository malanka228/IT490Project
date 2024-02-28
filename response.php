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
        $stmt->close();
        return array("returnCode" => '1', 'message' => 'Login Successful', 'jwt' => $jwtT);
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
    }
}
$server = new rabbitMQServer("testRabbitMQ.ini", "testServer");
$server->process_requests('requestProcessor');
exit();
?>
