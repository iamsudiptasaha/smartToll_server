<?php
/**
Page Info
Requires : DatabaseConnect.smartToll
@author : Sudipta Saha
@since : 23rd Jan, 2018
This page handles user registration.
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

if(isset($_POST['name']) && isset($_POST['username'])  && isset($_POST['password']) ){
    if(!empty($_POST['name']) && !empty($_POST['username']) && !empty($_POST['password'])){
  //Check for required email format
        if(filter_var($_POST['username'], FILTER_VALIDATE_EMAIL)){
            require 'DatabaseConnect.smartToll.php';
            $name=$_POST['name'];
            $username=$_POST['username'];
            $password=$_POST['password'];
            if(makeConnection()){
               //Check for duplicate entry
                $query="SELECT *FROM $db_tablename WHERE username=\"".mysqli_real_escape_string($db_con,$username)."\"";
                if(!@$db_result=mysqli_query($db_con,$query)){
                    $successvalue='false';
                    $errorCode='5';
                }
                else{
                    if(mysqli_num_rows($db_result)==0){
                      //Encrypt the password using md5 hashing
                        $password=md5($password);
                        $query= "INSERT INTO `$db_tablename` (`username`, `password`, `name`,`joindate`) VALUES ('".mysqli_real_escape_string($db_con,$username)."', '$password', '".mysqli_real_escape_string($db_con,$name)."',  NOW())";


                        if($db_result=mysqli_query($db_con,$query)){
                          $db_tablename="bankaccount";
                            $query= "INSERT INTO `$db_tablename` (`username`, `money`) VALUES ('".mysqli_real_escape_string($db_con,$username)."', 5000)";
                            if($db_result=mysqli_query($db_con,$query)){
                              $successvalue='true';
                              $errorCode='null';

                            }
                            else{
                              $successvalue='false';
                              $errorCode='5';
                            }

                        }
                        else {
                            $successvalue='false';
                            $errorCode='5';
                        }
                    }
                    else if(mysqli_num_rows($db_result)>=1){
                        $successvalue='false';
                        $errorCode='4';
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
//return the resultant sucess and errorCode
if(!empty($successvalue)){
    $result= array('success' => $successvalue, 'errorCode' => (string)$errorCode);
    $result=json_encode($result);

    echo $result;
}
?>
