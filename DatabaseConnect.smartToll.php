<?php

/**
Page Info
@author : Sudipta Saha
@since : 23rd Jan, 2018
This page makes connection to the database.
*/
  //Information about your database
  $db_host='localhost';
  $db_username='root';
  $db_password='';
  $db_databasename='smart_toll';


  $db_con;
  /*Call function makeConnection to connect to database with above credentials
  */
  function makeConnection(){
    global $db_con,$db_host,$db_username,$db_password,$db_databasename;
  //  echo $db_con;
    if($db_con=mysqli_connect($db_host,$db_username,$db_password)){
     if(mysqli_select_db($db_con,$db_databasename)){

        return true;
    }
  }
    else {
      return false;
    }
  }


  //echo makeConnection();

 ?>
