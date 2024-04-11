#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once 'vendor/autoload.php';

function requestProcessor($request)
{
    echo "Received request" . PHP_EOL;
    var_dump($request);
    if (!isset($request['type'])) {
        return "ERROR: unsupported message type";
    }
    switch ($request['type']) {
        case "Transfer Package To Deployment":
            $sourceFile = $request['sourceFile'];
            $username = $request['username'];
            $destinationIP = $request['destinationIP'];
            $destinationDir = $request['destinationDir'];
            $command = "scp \"$username@$destinationIP:$sourceFile\" \"$destinationDir\"";
            exec($command, $output, $returnCode);
            if ($returnCode !== 0) {
                return array("returnCode" => '0', 'message' => 'Error: Failed to transfer file');
            }
            $parts = explode('_', basename($sourceFile));
            if (count($parts) != 2) {
                return array("returnCode" => '0', 'message' => 'Error: Invalid source file format');
            }
            $packageName = $parts[0];
            $version = str_replace('.tar.gz', '', $parts[1]);
            $mydb = new mysqli('127.0.0.1', 'testUser', '12345', 'testdb');
            if ($mydb->connect_errno) {
                echo "Failed to connect to database: " . $mydb->connect_error . PHP_EOL;
                return array("returnCode" => '0', 'message' => "Database connection failed");
            }
            $result = $mydb->query("SELECT * FROM Packages WHERE packageName = '$packageName'");
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $currentVersion = $row['versionNum'];
                $status = $row['status'];
                
                if ($status == 1) {
                    $newVersion = floatval($currentVersion) + 1.0;
                    
                    $insertQuery = "INSERT INTO Packages (packageName, versionNum, status) 
                                    VALUES ('$packageName', '$newVersion', '0')";
                    if ($mydb->query($insertQuery) === TRUE) {
                        return array("returnCode" => '1', 'message' => 'File transferred successfully and saved to database');
                    } else {
                        return array("returnCode" => '0', 'message' => 'Error: Failed to save file to the database');
                    }
                } else {
                    $newVersion = floatval($currentVersion) + 1.0; // Increment version by 0.1
                    $updateQuery = "UPDATE Packages SET versionNum = '$newVersion' WHERE packageName = '$packageName'";
                    if ($mydb->query($updateQuery) === FALSE) {
                        return array("returnCode" => '0', 'message' => 'Error: Failed to update version number');
                    }
                    return array("returnCode" => '1', 'message' => 'Version number incremented successfully');
                }
                
            }
            $insertQuery = "INSERT INTO Packages (packageName, versionNum, status) 
                            VALUES ('$packageName', '$version', '0')";
            if ($mydb->query($insertQuery) === TRUE) {
                return array("returnCode" => '1', 'message' => 'File transferred successfully and saved to database');
            } else {
                return array("returnCode" => '0', 'message' => 'Error: Failed to save file to the database');
            }
        case "Update Status Code":
            $sourceFile = $request['sourceFile'];
            $versionNumber = $request['versionNumber'];
            $statusCode = $request['status'];
            $mydb = new mysqli('127.0.0.1', 'testUser', '12345', 'testdb');
            if ($mydb->connect_errno) {
                echo "Failed to connect to database: " . $mydb->connect_error . PHP_EOL;
                return array("returnCode" => '0', 'message' => "Database connection failed");
            }
            $updateQuery = "UPDATE Packages SET status = '$statusCode' 
                            WHERE packageName = '$sourceFile' AND versionNum = '$versionNumber'";
            if ($mydb->query($updateQuery) === TRUE) {
                return array("returnCode" => '1', 'message' => 'Status code updated successfully');
            } else {
                return array("returnCode" => '0', 'message' => 'Error: Failed to update status code');
            }
    }
}

$server = new rabbitMQServer("testRabbitMQ.ini", "testDMZ");
$server->process_requests('requestProcessor');
exit();
?>
