<?php
/**
Page Info
Requires : DatabaseConnect.smartToll
@author : Sudipta Saha
@since : 23rd Jan, 2018
This page is a replica of a bank account having simple transaction functionalities to add money to bank account.
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
$curpage=$_SERVER['SCRIPT_NAME'];
$db_tablename='tourDetails';
$errorCode;
$successvalue;
$minbal=5;
if(isset($_POST['username'])){
   if(!empty($_POST['username']) && !empty($_POST['money'])){
       require 'DatabaseConnect.smartToll.php';
       $username=$_POST['username'];
       $money=$_POST['money'];
       //Make connection to database
       if(makeConnection()){
         //update bank information depending on money that is to be added.
         $secondquery="UPDATE `bankaccount` SET money=$money WHERE username='".mysqli_real_escape_string($db_con,$username)."'";
         if($db_result=mysqli_query($db_con,$secondquery)){
           $successvalue='true';
           $errorCode='null';
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
  $result= array('success' => $successvalue, 'errorCode' => (string)$errorCode);
  $result=json_encode($result);

  echo $result;
}
