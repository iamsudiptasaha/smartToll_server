<?php
/**
Page Info
Requires : DatabaseConnect.smartToll
@author : Sudipta Saha
@since : 23rd Jan, 2018
This page is a replica of a bank account having simple transaction functionalities for the user.
*/

/*
1= Invalid data or empty data
2= Improper option
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

$curpage=$_SERVER['SCRIPT_NAME'];
$db_tablename='tourDetails';
$errorCode;
$successvalue;

/*Here option is to SET and GET
 *Set - set transaction information of an user.
 *Get - get transaction information of an user.
 */

if(isset($_POST['username']) && isset($_POST['option'])){
   if(!empty($_POST['username']) && !empty($_POST['option'])){
       require 'DatabaseConnect.smartToll.php';
       $username=$_POST['username'];
       $option=$_POST['option'];
       //Make database connection
       if(makeConnection()){
         if($option=="set"){
           $money=$_POST['money'];
           //update bank information depending on money spent.
           $secondquery="UPDATE `bankaccount` SET money=$money WHERE username='".mysqli_real_escape_string($db_con,$username)."'";
           if($db_result=mysqli_query($db_con,$secondquery)){
             $successvalue='true';
             $errorCode='null';
              $result['money']=$money;
           }
           else{
             $successvalue='false';
             $errorCode='5';
           }
         }
         else if($option=="get"){
           //get bank information of the user.
           $firstquery="SELECT money FROM `bankaccount` WHERE username='".mysqli_real_escape_string($db_con,$username)."'";
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
               $result['money']=$row['money'];
               $successvalue='true';
               $errorCode='null';
             }
           }


         }
         else{
           $errorCode="2";
           $successvalue="false";
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
  $result['success']=  $successvalue;
  $result['errorCode'] = (string)$errorCode;
  $result=json_encode($result);
  //Print data which is passed to the caller.
  echo $result;
}




 ?>
