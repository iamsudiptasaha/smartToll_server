<?php
/**
Page Info
Requires : DatabaseConnect.smartToll
@author : Sudipta Saha
@since : 23rd Jan, 2018
This page is used to update the current status of an user and set the entry information.
*/

/*
1= Invalid data or empty data
2= Improper email id and password
3= Failed DatabaseConnectivity, try again.
4= Duplicate username, rejected.
5= MYSQL error
6= no such user
*/

/*
 * The status codes for tour:
 * 0 - Default tour status value.
 * 1 - Travelling.
 * 2 - Completed.
 */
require("phpMQTT.php");
$curpage=$_SERVER['SCRIPT_NAME'];
$db_tablename='tourDetails';
$errorCode;
$successvalue;

/*
 *This function manages a demo-table for tour cost. Can use database to store this!
*/
function getTourCost($station_from, $station_to){
  $station_code = array('A' => 0, 'B' => 1, 'C' => 2, 'D' => 3, 'E' => 4);
  $price_array = array(
      array(5, 5, 10, 15, 20),
      array(5, 5, 5, 10, 15),
      array(10, 5, 5, 5, 10),
      array(15, 10, 5, 5, 5),
      array(20, 15, 10, 5, 5),
  );

  return $price_array[$station_code[$station_from]][$station_code[$station_to]];
}


if(isset($_POST['username']) && isset($_POST['tourid']) && isset($_POST['station_to']) && isset($_POST['gate'])){
   if(!empty($_POST['username'])  && !empty($_POST['tourid']) && !empty($_POST['station_to']) && !empty($_POST['status']) && !empty($_POST['gate'])){
       require 'DatabaseConnect.smartToll.php';
       $username=$_POST['username'];
       $tourid=$_POST['tourid'];
       $station_to=$_POST['station_to'];
       $status=$_POST['status'];
       $gate=$_POST['gate'];

       if(makeConnection()){
          //Get data from database
                $firstquery="SELECT station_from FROM `tourDetails` INNER JOIN `cusrecord` ON cusrecord.id=$db_tablename.userid WHERE tourid=\"".mysqli_real_escape_string($db_con,$tourid)."\" AND cusrecord.username='".mysqli_real_escape_string($db_con,$username)."'";

                if(!@$db_result=mysqli_query($db_con,$firstquery)){
                  $successvalue='false';
                  $errorCode="4";
                }
                else{
                  if(mysqli_num_rows($db_result)==0){
                    $successvalue='false';
                    $errorCode='5';
                   }
                  else if(mysqli_num_rows($db_result)>=1){
                    $row=mysqli_fetch_assoc($db_result);
                    $station_from=$row['station_from'];

                  }
                }
                $tourcost=getTourCost($station_from,$station_to);

       $firstquery="UPDATE `$db_tablename` INNER JOIN `cusrecord` ON cusrecord.id=$db_tablename.userid SET station_to='".mysqli_real_escape_string($db_con,$station_to)."', status=".mysqli_real_escape_string($db_con,$status).", exit_time=NOW(), tourcost=$tourcost WHERE tourid=\"".mysqli_real_escape_string($db_con,$tourid)."\" AND cusrecord.username='".mysqli_real_escape_string($db_con,$username)."'";
       #echo "<br>$query<br>";

       if($db_result=mysqli_query($db_con,$firstquery)){
         //Update the cost in bank details
         $secondquery="UPDATE `bankaccount` SET money=money-$tourcost WHERE username='".mysqli_real_escape_string($db_con,$username)."'";
         if($db_result=mysqli_query($db_con,$secondquery)){

           $successvalue='true';
           $errorCode='null';

           //Use details of your MQTT client
           /*$server = "...";     // change if necessary
           $port = ...;                     // change if necessary
           $username = "...";                   // set your username
           $password = "...";                   // set your password
           */
           $client_id = "phpMQTT-publisher"; // make sure this is unique for connecting to sever - you could use uniqid()

           $mqtt = new phpMQTT($server, $port, $client_id);
           if ($mqtt->connect(true, NULL, $username, $password)) {
             $info['gate']=$gate;
             $info['station']=$station_to;
             $info=json_encode($info);
             //Publish into channel to open the gates
             $mqtt->publish("smartToll", $info , 0);
             $mqtt->close();
           } else {
               echo "Time out!\n";
           }
         }
         else{
           $successvalue='false';
           $errorCode='5';
         }


       }
       else{
         $successvalue='false';
         $errorCode='5';
      }


  mysqli_close($db_con);
       }

     else{
       $successvalue='false';
       $errorCode='3';
     }

   }
   else{
     $successvalue='false';
     $errorCode='1';
   }
 }

 else{
   $successvalue='false';
   $errorCode='1';
 }

if(!empty($successvalue)){
  if(!isset($tourcost))
    $tourcost=0;
    $result= array('success' => $successvalue, 'errorCode' => (string)$errorCode, 'tourcost' => $tourcost);
  $result=json_encode($result);

  echo $result;
}




 ?>
