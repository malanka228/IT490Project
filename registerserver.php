#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function requestProcessor($request)
{

  echo "received request".PHP_EOL;
  var_dump($request);
	/*$result = doRegister($request['firstname'],$request['lastname'],$request['username'],$request['email'],$request['password']); 
	if(true){*/
	    return array("returnCode" => '0', 'message'=>"Server received request and processed");
	//send back to client
    /*}else{
		return array("returnCode" => '1', 'message'=>"Server received request and processed");
	//send back to client
	}
    echo "it works!"; */
    
}   
$server = new rabbitMQServer("testRabbitMQ.ini","testServer");
//register.ini talks to different exchange and queue because issues happened using same testExchange

$server->process_requests('requestProcessor');
exit();
?>
