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
7= not enough balance
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
$minbal=5;
$tourid;
if(isset($_POST['username']) && isset($_POST['station_from']) && isset($_POST['gate']) ){
   if(!empty($_POST['username']) && !empty($_POST['station_from']) && !empty($_POST['status']) && !empty($_POST['gate'])){
       require 'DatabaseConnect.smartToll.php';
       $username=$_POST['username'];
       $station_from=$_POST['station_from'];
       $status=$_POST['status'];
       $gate=$_POST['gate'];
       if(makeConnection()){
      //Get bank information
      $query="SELECT money FROM `bankaccount`WHERE username=\"".mysqli_real_escape_string($db_con,$username)."\"";

      if(!@$db_result=mysqli_query($db_con,$query)){
        $successvalue='false';
        $errorCode='5';

      }
      else{
          if(mysqli_num_rows($db_result)==0){
            $successvalue='false';
            $errorCode='6';

          }
          else{
            $row=mysqli_fetch_assoc($db_result);
            $money=floatval($row['money']);
            //Get id of the associated user.
            if($money>=$minbal){
              $query="SELECT id FROM `cusrecord` WHERE username=\"".mysqli_real_escape_string($db_con,$username)."\"";
              if(!@$db_result=mysqli_query($db_con,$query)){
                $successvalue='false';
                $errorCode='5';
              }
              else{
                if(mysqli_num_rows($db_result)==0){
                  $successvalue='false';
                  $errorCode='6';

            }

            else{
              $row=mysqli_fetch_assoc($db_result);
              $userid=$row['id'];
              //Making a unique tourid
              $query="SELECT COUNT(*) FROM `tourdetails` WHERE userid=$userid";

              if(!@$db_result=mysqli_query($db_con,$query)){
                $successvalue='false';
                $errorCode='5';

              }
              else{
              $row=mysqli_fetch_assoc($db_result);
              $curmax=$row['COUNT(*)'];
              $curmax=$curmax+1;
              $tourid=$userid."_".$curmax;
              //Setting new tour
              $query= "INSERT INTO `$db_tablename` (`tourid`,`userid`, `station_from`, `status`) VALUES (\"$tourid\", $userid, '".mysqli_real_escape_string($db_con,$station_from)."', '".mysqli_real_escape_string($db_con,$status)."')";
                //echo $query;
                if($db_result=mysqli_query($db_con,$query)){
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
                    $info['station']=$station_from;
                    $info=json_encode($info);
                    //Publish information on the channel of the associated gate that needs to be opened
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



            //
            }
}
}

        else{
          $successvalue='false';
          $errorCode='7';

        }
      }

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
  $result= array('success' => $successvalue, 'errorCode' => (string)$errorCode, 'tourid' => $tourid);
  $result=json_encode($result);

  echo $result;
}
 ?>
