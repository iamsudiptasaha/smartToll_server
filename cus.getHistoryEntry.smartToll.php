<?php
/**
Page Info
Requires : DatabaseConnect.smartToll
@author : Sudipta Saha
@since : 23rd Jan, 2018
This page returns information about all the tours associated with an user.
*/
/*
1-Imvalid data or empty data
2-invalid regex password or email id
3-FailedDatabase Connectivity, try again.
4-MYSQL error
5-no such entry
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
if(isset($_POST['username'])){
    if(!empty($_POST['username'])){
          //Check for required email format
      if(filter_var($_POST['username'], FILTER_VALIDATE_EMAIL)){
            $username=$_POST['username'];
            require 'DatabaseConnect.smartToll.php';
              if(makeConnection()){
                   //Get data from database
                  $query="SELECT cusrecord.username, $db_tablename.* FROM $db_tablename INNER JOIN `cusrecord` ON cusrecord.id=$db_tablename.userid WHERE username=\"".mysqli_real_escape_string($db_con,$username)."\" AND status=\"2\" ORDER BY entry_time DESC";

                  if(!@$db_result=mysqli_query($db_con,$query)){
                    $successvalue='false';
                    $errorCode="4";
                  }
                  else{
                    if(mysqli_num_rows($db_result)==0){
                      $successvalue='false';
                      $errorCode='5';
                     }
                    else{
                      $successvalue='true';
                      $errorCode='null';
                        $i=0;
                          //Create result array to return
                        while($row=mysqli_fetch_assoc($db_result)){
                            $result[++$i]=$row;
                        }
                        $result['nooftours']=$i;


                  }
                    mysqli_close($db_con);
              }
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
$result['success'] = $successvalue;
$result['errorCode'] = (string)$errorCode;
}
$result=json_encode($result);
//Print data which is passed to the caller.
echo $result;
?>
