<?php

function OpenCon()
{
    $dbhost = "localhost";
    $dbuser = "root";
    $dbpass = "";
    $db = "extra_class";

    $conn = new mysqli($dbhost, $dbuser, $dbpass,$db) or die("Connect failed: %s\n". $conn -> error);

    return $conn;
}

function flush_database($conn,$current_day){
    $days = array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
    $today = date("l");
    if( array_search($today,$days)+1 > (array_search($current_day,$days)+1)%7 ){
      $sql = "DELETE FROM weeklytable where day = '".$current_day."'";
      $q1 = $conn -> prepare($sql);
      $q1 -> execute();
      $q1 -> close();
      $sql = "INSERT INTO weeklytable SELECT * FROM permanenttable where day = '".$current_day."'";
      $q1 = $conn -> prepare($sql);
      $q1 -> execute();
      $current_day = $today;
    }
    return date("l");

}

function CloseCon($conn)
{
    $conn -> close();
}

?>