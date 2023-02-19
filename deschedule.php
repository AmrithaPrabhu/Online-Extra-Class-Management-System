<?php 
    session_start();
    include 'dbconnection.php';
    $conn = OpenCon();
    $_SESSION["cday"] = flush_database($conn,$_SESSION["cday"]);

    $prof = $_SESSION["pid"];
    $proffname = "";
    $profsname = "";
    $error1 = "";
    $days = "";
    $day = "";
    $slotid = "";
    $starttime = "";
    $endtime = "";
    $mondayslots = "";
    $tuesdayslots = "";
    $wednesdayslots = "";
    $thursdayslots = "";
    $fridayslots = "";
    $slot = "";
    $error = "";
    $course = "";
    $course_name = "";
    $builing= "";
    $room = "";

    function clean_kuttapi($string){
        $string=trim($string);
        $string = stripslashes($string);
        $string = htmlspecialchars($string);       
        return $string;
    }

if(isset($_POST['goback']))
{
    header('Location: ' . "functions.php");
    die();
}

if(isset($_POST['logout']))
{
    header('Location: ' . "home.php");
    die();
}
    if(isset($_POST["checktt"]))
    {
        $days = array();
        if(!empty($_POST['Mondayslots']))
        {
            $mondayslots = array("Monday",$_POST["Mondayslots"]);
            array_push($days,$mondayslots);
        }
        if(!empty($_POST['Tuesdayslots']))
        {
            $tuesdayslots = array("Tuesday",$_POST["Tuesdayslots"]);
            array_push($days,$tuesdayslots);
        }
        if(!empty($_POST['Wednesdayslots']))
        {
            $wednesdayslots = array("Wednesday",$_POST["Wednesdayslots"]);
            array_push($days,$wednesdayslots);
        }
        if(!empty($_POST['Thursdayslots']))
        {
            $thursdayslots = array("Thursday",$_POST["Thursdayslots"]);
            array_push($days,$thursdayslots);
        }
        if(!empty($_POST['Fridayslots']))
        {
            $fridayslots = array("Friday",$_POST["Fridayslots"]);
            array_push($days,$fridayslots);
        }

        if(empty($days))
        {
            $error .= '<p> You havent selected any slots </p>';
        }
        else
        {
            foreach($days as $day)
            {
                foreach($day[1] as $slot)
                {
                    $q="DELETE FROM weeklytable WHERE day = ? and slot_id = ? and prof_id = ?";
                    $q1=$conn->prepare($q);
                    $q1->bind_param("sdd",$day[0],$slot,$prof);
                    $q1->execute();
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="bg2.css">
    <link rel="stylesheet" href="tt.css">
    <script type="text/javascript">
        function onload() {
            var tds = document.getElementsByTagName("td");
            for(var i = 0; i < tds.length; i++) {
                tds[i].onclick = 
                                function(td) { 
                                    return function() { 
                                        tdOnclick(td); 
                                    }; 
                                }(tds[i]); 
            }
            var inputs = document.getElementsByTagName("input");
            for(var i = 0; i < inputs.length; i++) {
                inputs[i].onclick = 
                                function(input){ 
                                    return function() { 
                                        inputOnclick(input); 
                                    };
                                }(inputs[i]); 
            }
        }
        function tdOnclick(td) {
            for(var i = 0; i < td.childNodes.length; i++) {
                if(td.childNodes[i].nodeType == 1) {
                    if(td.childNodes[i].nodeName == "INPUT") {
                        if(td.childNodes[i].checked) {
                            td.childNodes[i].checked = false;
                        } else {
                            td.childNodes[i].checked = true;
                        }
                    } else {
                        tdOnclick(td.childNodes[i]);
                    }
                }
            }
        }
        function inputOnclick(input) {
            input.checked = !input.checked;
            return false;
        }
    </script>
</head>
<body onload = "onload()">
<nav>
        <ul>
          <li style="font-family: 'Poppins', sans-serif" ><a href='functions.php'> &laquo Back</a></li>
         </ul>
       </nav>
    <!-- logo -->
          <div class="logo"><span><img src="Nitc_logo.png"> </span></div>
    <center>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method = "post">
        <?php
            $q="SELECT first_name,second_name from professor where prof_id=?";
            $q1=$conn->prepare($q);
            $q1->bind_param("d",$prof);
            $q1->execute();
            $q1->bind_result($proffname,$profsname);
            $q1->store_result();
            while($q1->fetch())
            {
                echo '<h1>'.$proffname.' '.$profsname.'</h1>';
            }
        ?>
        <br>
        <table class='table1'cellspacing=0 cellpadding=0>

            <tr>
                <th class="days">DAY/SLOT</th>
                <?php
                    $q="SELECT starting_time,ending_time from timings";
                    $q1=$conn->prepare($q);
                    $q1->execute();
                    $q1->bind_result($starttime,$endtime);
                    while($q1->fetch())
                    {
                        echo '<th class="days">'.$starttime.'-'.$endtime.'</th>';
                    }
                ?>
            </tr>
            <?php
                $q="SELECT distinct course.course_id FROM weeklytable INNER JOIN course ON course.course_id=weeklytable.course_id  WHERE day = ? AND slot_id = ? AND prof_id = ?";
                $q1=$conn->prepare($q);
                $q1->bind_param("sdd",$day,$slotid,$prof);
                $days = array("Monday","Tuesday","Wednesday","Thursday","Friday");
                foreach($days as $day)
                {
                    echo '<tr>';
                    echo '<th class="days">'.$day.'</th>';
                    for($slotid=1;$slotid<=8;$slotid++)
                    {
                        //echo '<td class="t2"><center>';
                        $flag = 0;
                        $q1->execute();
                        $q1->bind_result($course);
                        $q1->store_result();
                        if($q1->fetch())
                        {   $flag= 1;
                            echo '<td class="t2"><center><input type="checkbox" name="'.$day.'slots[]" value="'.$slotid.'">'.$course.'<br>';
                            $q2="SELECT classroom.building,classroom.room from weeklytable inner join classroom on weeklytable.room_id = classroom.room_id where day = ? and slot_id = ? and prof_id = ?";
                            $q3=$conn->prepare($q2);
                            $q3->bind_param("sdd",$day,$slotid,$prof);
                            $q3->execute();
                            $q3->bind_result($builing,$room);
                            $q3->store_result();
                            while($q3->fetch())
                            {
                                echo $builing.' '.$room.'<br>';
                            }
                            echo '</center></td>';
                        }
                        if(!$flag){
                            echo '<td class="t2"><center><br>-<br></center></td>';
                        }
                        //echo '</center></td>';
                    }
                    echo '</tr>';
                }
            ?>
            
        </table>
        <br>
        <center>
            <justify>
        <table class="table2" cellspacing=0 cellpadding=0>
                <th class="days"><center>COURSE ID</center></th>
                <th class="days"><center>COURSE NAME</center></th>
<br>
            <?php
                $q = "SELECT distinct  weeklytable.course_id,course.course_name from weeklytable inner join course on course.course_id=weeklytable.course_id where prof_id = ?";
                $q1=$conn->prepare($q);
                $q1->bind_param("s",$prof);
                $q1->execute();
                $q1->bind_result($course,$course_name);
                $q1->store_result();
                while($q1->fetch())
                {
                    echo '<tr>';
                    echo '<td class="t3">'.$course.'</td>';
                    echo '<td class=t3>'.$course_name.'</td>';
                    echo '</tr>';
                }
            ?>
        </table>
        <br>
        <div class="input_box">
        <input type="submit" name="checktt" value="Deschedule">
            </div>
    </form>
    <?php
        echo $error;
    ?>
    </center>
</body>
</html>