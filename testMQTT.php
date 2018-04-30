<?php
require("phpMQTT.php");
$server = "m23.cloudmqtt.com";     // change if necessary
$port = 10799;                     // change if necessary
$username = "pzudsxkz";                   // set your username
$password = "0U94xMf24_Uz";                   // set your password
$client_id = "phpMQTT-publisher"; // make sure this is unique for connecting to sever - you could use uniqid()
$mqtt = new phpMQTT($server, $port, $client_id);
if ($mqtt->connect(true, NULL, $username, $password)) {
	$mqtt->publish("test", "Hello World! at " . date("r"), 0);
	$mqtt->close();
} else {
    echo "Time out!\n";
}
