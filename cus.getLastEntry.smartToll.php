
<?php
/**
Page Info
Requires : DatabaseConnect.smartToll
@author : Sudipta Saha
@since : 23rd Jan, 2018
This page returns information about the last tour associated with an user.
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
                  $query="SELECT cusrecord.username, $db_tablename.* FROM $db_tablename INNER JOIN `cusrecord` ON cusrecord.id=$db_tablename.userid WHERE username=\"".mysqli_real_escape_string($db_con,$username)."\" ORDER BY entry_time DESC LIMIT 1";

                  if(!@$db_result=mysqli_query($db_con,$query)){
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
                      $tourid=$row['tourid'];
                      $userid=$row['userid'];
                      $username=$row['username'];
                      $station_from=$row['station_from'];
                      $entry_time=$row['entry_time'];
                      $station_to=$row['station_to'];
                      $exit_time=$row['exit_time'];
                      $status=$row['status'];
                      $tourcost=$row['tourcost'];
                      $successvalue='true';
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
    //Create result array to return
        $result= array('success' => $successvalue, 'errorCode' => (string)$errorCode, 'tourid'=>$tourid, 'userid'=>$userid, 'username'=>$username,'station_from'=>$station_from,'entry_time'=>$entry_time,'station_to'=>$station_to,'exit_time'=>$exit_time,'status'=>$status);

}
$result=json_encode($result);
//Print data which is passed to the caller.
echo $result;
?>
