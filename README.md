# smartToll_server
<h1>Smart Toll</h1>
<p>This project showcases a simple interconnectivity among a server, a mobile application and a hardware. The mobile application (android) majorly deals with user input and exchanges data with the server (PHP). The server inturn publishes data onto the mqtt channel. The hardware (raspberry pi 3) is connected to the same mqtt channel and depending on the message sent over the channel does specific operation.<p>

<h3>Proposed Working Principle:</h3>
<p>Consider a metro station with tollbooth. Commutators scan a QR Code on the tollbooth which unlocks the toll gate. The travel through the metro and once they reach the destination station, they scan the QR Code on the toll booth again to unlock the gate. The transaction not only gets recorded but the entire process requires no physical exchange of money. <p>
<ul>
  <li>A tollbooth consists a QR Code attached to it.</li>
  <li>The QR Code encodes the station and gate number of the tollbooth. This helps to note which tollbooth is the commutator using. The   information is sent to the server when the QR Code is scanned.</li>
  <li>Use the mobile application to scan the QR code on the tollbooth.</li>
  <li>Data associated with the tollbooth and user is spent over to the server.</li>
  <li>Server updates the database and spends message over MQTT Channel to open the connected toll gate.</li>
  <li>Simlarly the commutator can unlock the gate at the destination station.</li>
</ul>

<h3>Requirements:</h3>
<ol>
  <li>Raspberry Pi/Arduino - This handles the hardware part of the system. We have used Raspberry to avoid the trouble of setting up        ethernet for arduino.</li>
  <li>Ultrasonic Sensor (HC-SR04) - This will enable the auto closing of the gate after a commutator passes through the gate.</li>
  <li>Server</u> - This handles the centralized transaction and updation of user records. We have used XAMPP and coded our server with      PHP.</li>
  <li>Mobile Application - This handles the interaction with the user. The camera of the mobile scans the QR Code and use internet to     relay data to the server. We have used Android.</li>
  <li>MQTT Broker - (Message Queuing Telemetry Transport) In lucid terms it is just a channel where devices can <b>publish</b> or         <b>subscribe</b> to a <b>topic</b>. There are many free brokers available for testing!</li>
   <li>Database System - Store the user data for authentication, transaction and tour details. We have used MYSQL.</li>
  </ol>

<h3>Repositories:</h3>
<ul>
  <li><b>Android (Mobile Application)</b> - </li>
  <li><b>PHP (Server)</b> - https://github.com/iamsudiptasaha/smartToll_server</li>
  <li><b>Raspberry pi (Hardware)</b> - </li>
</ul>
    
 <h3>About this repository:</h3>

<p>This repository contains our PHP functionalities to handle the server. The PHP is coded using vanilla framework, so that developers can get a rough idea of the basic operations. It is recommended to use PHP frameworks that support MVC Architecture. The code is documented as and where required.</p>

<p>Link to download PHP MQTT : https://github.com/bluerhinos/phpMQTT</p>
<p>Link to download XAMPP : https://www.apachefriends.org/download.html</p>

<h3>Set up the MySQL Database:</h3>
<ol>
  <li>Customer Record Table - Stores records of the customers.
    <p>CREATE TABLE `cusrecord` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `username` varchar(50) NOT NULL,
 `password` varchar(100) NOT NULL,
 `name` varchar(100) NOT NULL,
 `phoneno` varchar(15) NOT NULL,
 `joindate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`),
 UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1</p>
  </li>
  
  <li>Tour Record Table - Stores tour information of every user.
    <p>CREATE TABLE `tourdetails` (
 `tourid` varchar(15) NOT NULL,
 `userid` int(11) NOT NULL,
 `station_from` varchar(50) NOT NULL,
 `entry_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `station_to` varchar(50) DEFAULT NULL,
 `exit_time` timestamp NULL DEFAULT NULL,
 `status` int(11) NOT NULL DEFAULT '0',
 `tourcost` int(11) NOT NULL DEFAULT '5',
 PRIMARY KEY (`tourid`),
 UNIQUE KEY `tourid` (`tourid`),
 KEY `userid` (`userid`),
 CONSTRAINT `tourdetails_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `cusrecord` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1</p>
  </li>
  
  
  
  
  <li>Bank Account Table - Stores bank records of the customers. This is a replica of the banking system. 
  In real time you will have to link existing third party banking systems.
    <p>CREATE TABLE `bankaccount` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `username` varchar(50) NOT NULL,
 `money` double NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1</p>
  </li>
  
</ol>

<h3>Server Page Definitions : </h3>
<ol>
  <li>cus.getEntryInfo.smartToll.php - This page returns information about a tour based on tourid.</li>
  <li>cus.getHistoryEntry.smartToll.php - This page returns information about all the tours associated with an user.</li>
  <li>cus.getLastEntry.smartToll.php - This page returns information about the last tour associated with an user.</li>
  <li>cus.login.smartToll.php - This page returns information about an user if correct username-password pair is supplied.</li>
  <li>cus.manageBankAccount.smartToll.php - This page is a replica of a bank account having simple transaction functionalities for the user.</li>
  <li>cus.setEntry.smartToll.php - This page is used to update the current status of an user and set the entry information.</li>
  <li>cus.setExit.smartToll.php - This page is used to update the current status of an user and set the entry information.</li>
  <li>cus.updateBankAccount.smartToll.php - This page is a replica of a bank account having simple transaction functionalities to add money to bank account.</li>
  <li>DatabaseConnect.smartToll.php - This page makes connection to the database.</li>
  <li>phpMQTT.php - The downloaded MQTT Script.</li>
</ol>

<h3>Miscellaneous informations : </h3>
<ul>
  <li>Return values : JSON Encoded data. Contains two mandatory fields to determine successful server operation:
    <p>"sucess" : "true"/"false" - If the operation was successful or not.</p>
    <p>"errorCode" : If sucess is false then we may get the following error codes. The associated error is mentioned alongside.<p>
    <p>
      <ul>
        <li>"1" = Invalid data or empty data.
        <li>"2"= Improper option.
        <li>"3"= Failed DatabaseConnectivity, try again.
        <li>"4"= Duplicate username, rejected.
        <li>"5"= MYSQL error.
        <li>"6"= no such user</li>
     </ul>
    </p>
  </li>
  
   <li>Tour status : Different status codes for tours and their associated status is mentioned along side:
     <p>
      <ul>
        <li>"0" = Default tour status value.
        <li>"1"= Travelling.
        <li>"2"= Completed.
     </ul>
    </p>
  </li>
</ul>












