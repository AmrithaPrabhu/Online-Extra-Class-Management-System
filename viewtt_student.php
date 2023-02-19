<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="bg.css">
    <link href='https://css.gg/log-out.css' rel='stylesheet'>
    <script src="https://kit.fontawesome.com/faeaa9a8c9.js" crossorigin="anonymous"></script>
</head>
    <nav>
            <div class="logout" name="View" onclick="window.location.href='student.php';" formaction=# value="View" ><img src="logout.png" class="log"></i>
            </div> 
    </nav>
        <!-- logo -->
            <div class="logo"><span><img src="Nitc_logo.png"> </span></div>
            <body>
<style>
<?php include 'tt.css'; ?>

</style>
</body>
</html>
<?php 
    session_start();
    include 'dbconnection.php';
    $conn = OpenCon();
    $conn2 = OpenCon();
    $_SESSION["cday"] = flush_database($conn,$_SESSION["cday"]);
    if(isset($_POST["back"])){
        header('Location: ' . "functions.php");
        die();
    }
    $batch_id=$_SESSION["bid"];
    $roll=$_SESSION["rollnum"];
    $days = array("Monday","Tuesday","Wednesday","Thursday","Friday");
    $all_slots = array("8-9 AM","9-10 AM","10:15-11:15 AM","11:15-12:15 PM","1-2 PM","2-3 PM","3-4 PM","4-5 PM");

    $q="SELECT first_name,second_name from student where roll_no=?";
    $q1=$conn->prepare($q);
    $q1->bind_param("s",$roll);
    $q1->execute();
    $q1->bind_result($studfname,$studsname);
    $q1->store_result();
    while($q1->fetch())
    {
        echo '<h1>'.$studfname.' '.$studsname.'</h1>';
    }

    echo "<html><body><center><justify><table class='table1'cellspacing=0 cellpadding=0>";
    echo '<tr><center>';
    echo '<th class="days"><center>' . "DAY/SLOT" . '</center></th>';

    foreach($all_slots as $s) 
    {
        echo '<th class="time"><center>' . $s . '</center></th>';
    }
    echo '</center></tr>';
    
    foreach($days as $day)
    {
        echo '<tr>';
        echo '<th class="days">' . $day. '</th>';
        $cl=0;
        for($slotid=1;$slotid<=8;$slotid++)
        {
            $sql="select course_id , room , building from weeklyTable as w,classroom as c where w.room_id = c.room_id and day = '".$day."' and slot_id = ".$slotid." and course_id in (select course_id from enrolments where roll_no = '".$roll."') and( batch_id=".$batch_id." or batch_id IS NULL)";
            $q2=$conn->prepare($sql);
            $q2->execute();
            $q2->bind_result($course_id,$room,$building);
            $q2->store_result();

            $flag=0;
            $course_array=array();
            while($q2->fetch())
            {
                array_push($course_array,array($course_id,$building,$room));
                $flag=1;
            }
            if($flag==0)
            {
                echo '<td class="t2"><br>' ."-". '<br></td>';
            }
            else
            {
                echo '<td class="t1"><center>';
                foreach($course_array as $c)
                {
                    echo $c[0]. "<br>" . $c[1] . " " . $c[2] ."<br>";
                }
                echo '</center></td>';
            }
        }
        echo '</tr>';
    }

    echo "<center><justify><table class='table2' cellspacing=0 cellpadding=0>";
    $sql="select c.course_id, c.course_name from course as c,enrolments as e where c.course_id = e.course_id and e.roll_no = '".$roll."'";
    $q2=$conn->prepare($sql);
    $q2->execute();
    $q2->bind_result($cid,$cname);
    $q2->store_result();
    echo '<th class="days"><center>Course ID</center></th>';
    echo '<th class="days"><center>Course</center></th>';
    echo '<br>';
    while($q2->fetch())
    {
        echo '<tr>';
        echo '<td class="t3">' . $cid . '</td>';
        echo '<td class="t3">' . $cname . '</td>';
        echo '</tr>';
    }

    if(isset($_POST["back"])){
        header('Location: ' . "home.php");
        die();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
</body>
</html>