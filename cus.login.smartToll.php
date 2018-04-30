<?php
/**
Page Info
Requires : DatabaseConnect.smartToll
@author : Sudipta Saha
@since : 23rd Jan, 2018
This page returns information about an user if correct username-password pair is supplied.
*/

  /*
  1-Imvalid data or empty data
  2-invalid regex password or email id
  3-FailedDatabase Connectivity, try again.
  4-wrong emailid password pair
  5-MYSQL error
  */

  /*
   * The status codes for tour:
   * 0 - Default tour status value.
   * 1 - Travelling.
   * 2 - Completed.
   */
  $curpage=$_SERVER['SCRIPT_NAME'];
  $db_tablename='cusrecord';
  $errorCode;
  $successvalue;

  if(isset($_POST['username']) && isset($_POST['password'])){
    if(!empty($_POST['username']) && !empty($_POST['password'])){
        //Check for required email format
      if(filter_var($_POST['username'], FILTER_VALIDATE_EMAIL)){

        $username=$_POST['username'];
        $password=$_POST['password'];
        require 'DatabaseConnect.smartToll.php';
        if(makeConnection()){
           //Encrypt the password using md5 hashing
          $password=md5($password);
           //Get data from database
          $query="SELECT *FROM $db_tablename WHERE username=\"".mysqli_real_escape_string($db_con,$username)."\" AND password=\"".mysqli_real_escape_string($db_con,$password)."\"";
          if(!@$db_result=mysqli_query($db_con,$query)){
            $successvalue='false';
            $errorCode="4";
          }
          else{
            if(mysqli_num_rows($db_result)==0){
              $successvalue='false';
              $errorCode='4';
             }
            else if(mysqli_num_rows($db_result)>=1){
              $row=mysqli_fetch_assoc($db_result);
              $id=$row['id'];
              $successvalue='true';
              $name=$row['name'];
              $errorCode='null';
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
        $errorCode='2';
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




  if($successvalue=='false'){
    $result= array('success' => $successvalue, 'errorCode' => (string)$errorCode);
  }
  else{
//Print data which is passed to the caller.
      $result= array('success' => $successvalue, 'errorCode' => (string)$errorCode, 'id'=>$id, 'name' => $name);

}
  $result=json_encode($result);

  echo $result;


 ?>
