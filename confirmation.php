<!DOCTYPE HTML>
<html>
<body>
<div class="header"><span><img src="Nitc_logo.png"> </span></div>
<div class="header-content">
<h1>CLASS CONFIRMATION PAGE</h1>
</div>
</body>
</html>
<?php

session_start();
include 'dbconnection.php';
$conn = OpenCon();
$_SESSION["cday"] = flush_database($conn,$_SESSION["cday"]);

$booking=$_SESSION["booking"];
$booked = 0;
$avail=$_SESSION["avail"];
$minus=-1;
$day=$_SESSION["day"];
$slotid1=$_SESSION["slotid1"];
$slotid2=$_SESSION["slotid2"];
$batchid=$_SESSION["batchid"];
$courseid=$_SESSION["courseid"];
$prof=$_SESSION["pid"];
$roomid=$_SESSION["roomid"];
$buttons = '<html><div class="class"><input type="submit" value="Book" name="Book"><input type="submit" value="Cancel" name="Cancel"></div></html>';
$rollno="";
$fname="";
$sname="";
$ba="";
$students="";
if(isset($_POST['MainMenu']))
{
    header('Location: ' . "functions.php");
    die();
    // but i wanted to live :c
}

        $sql="select s.roll_no , s.first_name , s.second_name from student as s, enrolments as e where e.roll_no = s.roll_no and e.course_id = ? and (s.batch_id = ? or ? = -1);";
        $q1=$conn->prepare($sql);
        $q1->bind_param("sdd",$courseid,$batchid,$batchid);
        $q1->execute();
        $q1->bind_result($rollno,$fname,$sname);
        $total=0;
        while($q1->fetch()){
           $total++;
        }
        // echo "<p>".$total." student/s have clashes.";
        $q1->close();

        $sql="select s.roll_no , s.first_name , s.second_name from student as s , weeklytable as w , enrolments as e where e.course_id = w.course_id and e.roll_no = s.roll_no and w.day = ? and w.slot_id >= ? and w.slot_id <= ? and (s.batch_id = w.batch_id or w.batch_id is null) intersect select s.roll_no , s.first_name , s.second_name from student as s, enrolments as e where e.roll_no = s.roll_no and e.course_id = ? and (s.batch_id = ? or ? = -1);";
        $q1=$conn->prepare($sql);
        $q1->bind_param("sddsdd",$day,$slotid1,$slotid2,$courseid,$batchid,$batchid);
        $q1->execute();
        $q1->bind_result($rollno,$fname,$sname);
        $row=0;
        while($q1->fetch()){
           $row++;
        }
        echo "<p>".$row." out of ".$total." student(s) have clashes.</p>";
        $q1->close();
        $q1=$conn->prepare($sql);
        $q1->bind_param("sddsdd",$day,$slotid1,$slotid2,$courseid,$batchid,$batchid);
        $q1->execute();
        $q1->bind_result($rollno,$fname,$sname);
        while($q1->fetch()){
           echo "<p>".$rollno.' '.$fname.' '.$sname."</p>";
        }

if(isset($_POST['Cancel']))
{
    header('Location: ' . "schedule.php");
    die();
}
if(isset($_POST["Book"])){
        $booked = 1;
        if($booking==1){
            $insert="INSERT INTO weeklytable(day,slot_id,batch_id,course_id,prof_id,room_id) VALUES(?,?,?,?,?,?)";
            $q1=$conn->prepare($insert);
            $q1->bind_param("sddsdd",$day,$slotid1,$batchid,$courseid,$prof,$roomid);
            $q1->execute();
            echo "<p>Successfully booked!<p>";
        }
        if($booking==2){
            $insert="INSERT INTO weeklytable(day,slot_id,course_id,prof_id,room_id) VALUES(?,?,?,?,?)";
            $q1=$conn->prepare($insert);
            $q1->bind_param("sdsdd",$day,$slotid1,$courseid,$prof,$roomid);
            $q1->execute();
            echo "<p>Successfully booked!<p>";
        }
        if($booking==3){
            $i=$slotid1;
            while($i<=$slotid2){
                $insert="INSERT INTO weeklytable VALUES(?,?,?,?,?,?)";
                $q1=$conn->prepare($insert);
                $q1->bind_param("sddsdd",$day,$i,$batchid,$courseid,$prof,$roomid);
                $q1->execute();
                $q1->close();
                $i=$i+1;
            }
            echo "<p>Successfully booked!</p>";
        }
        if($booking==4){
            $i=$slotid1;
            while($i<=$slotid2){
                    $insert="INSERT INTO weeklytable(day,slot_id,course_id,prof_id,room_id) VALUES(?,?,?,?,?)";
                    $q1=$conn->prepare($insert);
                    $q1->bind_param("sdsdd",$day,$i,$courseid,$prof,$roomid);
                    $q1->execute();
                    $q1->close();
                    $i=$i+1;                                   
            }
            echo "<p>Successfully booked!</p>";
        }
        $buttons = '<html><div class="class"><input type="submit" value="Go Back to Main Menu" name="MainMenu"></div></html>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="confirmationstylesheet.css">
</head>
<body>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method = "post">
        <?php
            echo $buttons;
        ?>
    </form>

</body>
</html>