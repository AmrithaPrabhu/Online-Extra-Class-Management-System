<?php 
session_start();
include 'dbconnection.php';
$conn = OpenCon();
$_SESSION["cday"] = flush_database($conn,$_SESSION["cday"]);

$prof=$_SESSION["pid"];
$roomid = "";
$building = "";
$room = "";
$batchid="";
$dept="";
$batch="";
$semester="";
$s="S";
$courseid="";
$courseName="";
$error1="";
$error2="";
$error3="";
$error4="";
$error5="";

$day="";
$slotid1="";
$slotid2="";

$batchIdTemp="";
$batchTemp="";
$branchTemp="";
$semTemp="";

function selected_slot($slot,$day,$conn,$p_id){
    $q="SELECT course.course_id,classroom.building,classroom.room FROM weeklytable INNER JOIN course ON course.course_id=weeklytable.course_id INNER JOIN classroom ON weeklytable.room_id = classroom.room_id WHERE day = ? AND slot_id = ? AND prof_id = ?";
    $q1=$conn->prepare($q);
    $q1->bind_param("sdd",$day,$slot,$p_id);
    $q1->execute();
    $q1->bind_result($course,$building,$room);
    $q1->store_result();
    $flag = 0;
    echo '<td class="t1"><center>';
    while($q1->fetch())
    {
        $flag = 1;
        echo $course.'<br>'.$building.' '.$room.'<br>';
    }
    if(!$flag){
        echo "<br>-<br>";
    }
    echo '</center></td>';
}

function slots_for_day($day,$conn,$p_id,$all_slots){
    echo '<tr>';
    echo '<th class="days"><center>' . $day . '</center></th>';
    foreach ($all_slots as $slot){
        selected_slot($slot,$day,$conn,$p_id);
    }
    echo '</tr>';
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
//once you click book slot
if(isset($_POST['checktt'])){
    if(empty($_POST['day'])){
         $error1="<p>Please enter day.</p>";
    }
    if(!empty($_POST["day"])){
         $day=$_POST["day"];
    }

    if(empty($_POST["room"])){
        $error2="<p>Please enter classroom</p>";
    }
    if(!empty($_POST["room"])){
        $roomid=$_POST["room"];
    }

    if(empty($_POST["start"])){
        $error3="<p>Please enter start time</p>";
    }
    if(!empty($_POST["start"])){
        $slotid1=$_POST["start"];
    }

    if(empty($_POST["end"])){
        $error4="<p>Please enter end time</p>";
    }
    if(!empty($_POST["end"])){
        $slotid2=$_POST["end"];
    }

    if(empty($_POST["course"])){
        $error5="<p>Please enter course</p>";
    }
    if(!empty($_POST["course"])){
        $courseid=$_POST["course"];
    }

    if(!empty($_POST["batch"])){
        $_SESSION["avail"]=1;
    }
    if(empty($_POST["batch"])){
        $_SESSION["avail"]=0;
    }

    $_SESSION["day"]=$day;
    $_SESSION["slotid1"]=$slotid1;
    $_SESSION["slotid2"]=$slotid2;
    
    $_SESSION["courseid"]=$courseid;
    $_SESSION["roomid"]=$roomid;

    if(strlen($error1)==0 && strlen($error2)==0 && strlen($error3)==0 && strlen($error4)==0 && strlen($error5)==0){
        if($slotid1>$slotid2){
            echo '<script>alert("Slot timings are incorrect")</script>';

        }
        if($slotid1==$slotid2){
            $profCheck="SELECT * FROM weeklytable WHERE day=? and slot_id=? and prof_id=?";
            $q1=$conn->prepare($profCheck);
            $q1->bind_param("sdd",$day,$slotid1,$prof);
            $q1->execute();
            
            $flag0=0;
            while($q1->fetch()){
                $flag0=1;
            }
            if($flag0==0){
                $q1->close();
                $roomCheck="SELECT * FROM weeklytable WHERE day=? and slot_id=? and room_id=?";
                $q1=$conn->prepare($roomCheck);
                $q1->bind_param("sdd",$day,$slotid1,$roomid);
                $q1->execute();

                $flag1=0;
                while($q1->fetch()){
                    $flag1=1;
                }
                if($flag1==0){
                    $q1->close();
                    if(!empty($_POST["batch"])){
                        $batchid=$_POST["batch"];
                        $batchCheck="SELECT * from weeklytable WHERE day=? and slot_id=? and batch_id=?";
                        $q1=$conn->prepare($batchCheck);
                        $q1->bind_param("sdd",$day,$slotid1,$batchid);
                        $q1->execute();

                        $flag2=0;
                        while($q1->fetch()){
                            $flag2=1;
                        }
                        if($flag2==0){
                            $q1->close();
                            $_SESSION["booking"]=1;
                            $_SESSION["batchid"]=$batchid;
                            header('Location: ' . "confirmation.php");
                            die();
                        }else{
                            echo '<script>alert("The batch already has a class scheduled")</script>';
                        }
                    }else{
                        $batchCheck="SELECT batch_id, batches.department,batches.batch,batches.semester from batches where batch_id in 
                        (select batch_id from weeklyTable where day = ? and slot_id = ? and batch_id in 
                        (select batch_id from batches where department in (select department from course where course_id = ?)))";
                        $q1=$conn->prepare($batchCheck);
                        $q1->bind_param("sds",$day,$slotid1,$courseid);
                        $q1->execute();
                        $q1->bind_result($batchIdTemp,$branchTemp,$batchTemp,$semTemp);
                        $q1->store_result();
                        $flag2=0;
                        while($q1->fetch()){
                            echo '<p>'.$branchTemp.' '.$batchTemp.' '.$s.$semTemp.' already have class scheduled'."</p>"; 
                            $flag2=1;
                        }
                        if($flag2==0){
                            $q1->close();
                            $_SESSION["batchid"]=-1;
                            $_SESSION["booking"]=2;
                            header('Location: ' . "confirmation.php");
                            die();
                        }
                    }
                }else{
                    echo "<script>alert('The room is already occupied')</script>";
                }
            }else{
                echo "<script>alert('You already have a class scheduled')</script>";
            }
            
        }
        if($slotid2>$slotid1){
            
            $i=$slotid1;
            $flag0=0;
            while($i<=$slotid2){
                $profCheck="SELECT * FROM weeklytable WHERE day=? and slot_id=? and prof_id=?";
                $q1=$conn->prepare($profCheck);
                $q1->bind_param("sdd",$day,$i,$prof);
                $q1->execute();
                while($q1->fetch()){
                    $flag0=1;
                }
                $q1->close();
                $i=$i+1;
            }
            
            if($flag0==0){
                    $roomCheck="SELECT * FROM weeklytable WHERE day=? and slot_id>=? and slot_id<=? and room_id=?";
                    $q1=$conn->prepare($roomCheck);
                    $q1->bind_param("sddd",$day,$slotid1,$slotid2,$roomid);
                    $q1->execute();

                    $flag1=0;
                    while($q1->fetch()){
                        $flag1=1;
                    }
                    if($flag1==0){
                        $q1->close();
                        if(!empty($_POST["batch"])){
                            $batchid=$_POST["batch"];
                            $i=$slotid1;
                            $flag2=0;
                            while($i<=$slotid2){
                                $batchCheck="SELECT * from weeklytable WHERE day=? and slot_id=? and batch_id=?";
                                $q1=$conn->prepare($batchCheck);
                                $q1->bind_param("sdd",$day,$i,$batchid);
                                $q1->execute();
                                if(!is_null($q1->fetch())){
                                    $flag2=1;
                                    
                                }
                                $q1->close();
                                $i=$i+1;
                            }
                            if($flag2==0){
                                //$q1->close();
                                $_SESSION["batchid"]=$batchid;
                                $_SESSION["booking"]=3;
                                header('Location: ' . "confirmation.php");
                                die();
                                
                            }else{
                                echo "<script>alert('The batch already has a class scheduled')</script>";
                            }
                        }else{
                            $i=$slotid1;
                            $flag2=0;
                            
                                $batchCheck="SELECT batch_id, batches.department,batches.batch,batches.semester from batches where batch_id in 
                                (select batch_id from weeklyTable where day = ? and slot_id >=? and slot_id<=? and batch_id in 
                                (select batch_id from batches where department = batches.department))";
                                $q1=$conn->prepare($batchCheck);
                                $q1->bind_param("sd",$day,$slotid1,$slotid2);
                                $q1->execute();
                                $q1->bind_result($batchIdTemp,$branchTemp,$batchTemp,$semTemp);
                                $q1->store_result();
                                
                                while($q1->fetch()){
                                    echo '<p>'.$branchTemp.' '.$batchTemp.' '.$s.$semTemp.' already have class scheduled'."</p>";
                                    $flag2=1;
                                }
                                $i=$i+1;
                                $q1->close();
                                // if($flag2==1){
                                   
                                // }
                            if($flag2==0){
                                // $q1->close();
                                $_SESSION["batchid"] = -1;
                                $_SESSION["booking"]=4;
                                header('Location: ' . "confirmation.php");
                                die();
                                
                            }
                        }
                    }else{
                        echo "<script>alert('The room is already occupied')</script>";
                    }
                }
            else{
                echo "<script>alert('You already have a class scheduled')</script>";
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
    <link rel="stylesheet" href="bg2.css">
    <link rel="stylesheet" href="schedulestylesheet.css">
</head>
    <title>Book Slots</title>
</head>
<body>
<nav>
        <ul>
          <li><a href='functions.php'> &laquo Back</a></li>
         </ul>
       </nav>
       <div class="logo"><span><img src="Nitc_logo.png"> </span></div>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method = "post">
    <div class="neww">
        <label for="" name="day">
            <!--Day:-->
            <div class="custom-select box1">
            <select name="day" id="">
                <option value="" disbaled selected hidden> -Select Day- </option>
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
            </select>
            </div>
           
        </label>
        <br>
        
        <?php 
            echo $error1;
            
        ?>
        <br>
        <label for="">
         <!--Classroom:-->
            <div class="custom-select box2">
            <select name="room" id="">
            <option value="" disbaled selected hidden> -Select Classroom- </option>
                <?php
                    $q = "SELECT* FROM classroom";
                    $q1 = $conn->prepare($q);
                    $q1->execute();
                    $q1->bind_result($roomid,$building,$room);
                    $q1->store_result();
                    while($q1->fetch())
                    {
                        echo '<option value="'.$roomid.'">'.$building.' '.$room.'</option>';
                    }
                
                ?>
               
            </select>
                </div>
                
        </label>
        <br>
        <?php echo $error2; ?>
        <br>
        <label for="">
            <!--Branch and batch:-->
            <div class="custom-select box3">
            <select name="batch" id="">
            <option value="" disbaled selected hidden> -Select Branch and Batch- </option>
                <?php
                    $q = "select batches.batch_id,batches.department,batches.batch,batches.semester from batches
                    inner join professor on professor.department=batches.department where professor.prof_id=?;";
                    $q1 = $conn->prepare($q);
                    $q1->bind_param("d",$prof);
                    $q1->execute();
                    $q1->bind_result($batchid,$dept,$batch,$semester);
                    $q1->store_result();
                    while($q1->fetch())
                    {
                        echo '<option value="'.$batchid.'">'.$dept.' '.$batch.' '.$s.$semester.'</option>';
                    }
                
                ?>
                 <option value="">All students enrolled</option>
            </select>
            </div>
        </label>
        <br>
        <?php echo $error5;?>
        <br>
        <br>
        
        <br>
        <label for="">
           <!--Class Start Time:-->
            <div class="custom-select box4">
            <select name="start" id="">
            <option value="" disbaled selected hidden>   -Select Start Time-   </option>
                <option value="1">8:00</option>
                <option value="2">9:00</option>
                <option value="3">10:15</option>
                <option value="4">11:15</option>
                <option value="5">13:00</option>
                <option value="6">14:00</option>
                <option value="7">15:00</option>
                <option value="8">16:00</option>    
            </select>
                </div>
        </label>
        <br>
        <?php echo $error3; ?>
        <br>
        <label for="">
            <!--Class End Time:-->
            <div class="custom-select box5">
            <select name="end" id="">
            <option value="" disbaled selected hidden> -Select End Time- </option>
                <option value="1">9:00</option>
                <option value="2">10:00</option>
                <option value="3">11:15</option>
                <option value="4">12:15</option>
                <option value="5">14:00</option>
                <option value="6">15:00</option>
                <option value="7">16:00</option>
                <option value="8">17:00</option> 
            </select>
            </div>
        </label>
        <br>
        <?php echo $error4; ?>
        <br>
        <label for="">
            <!--Course:-->
            <div class="custom-select box6">
            <select name="course" id="">
            <option value="" disbaled selected hidden> -Select Course- </option>
            <?php
                    $q = "select teaches.course_id,course.course_name from teaches
                    inner join course on course.course_id=teaches.course_id where teaches.prof_id=?;";
                    $q1 = $conn->prepare($q);
                    $q1->bind_param("d",$prof);
                    $q1->execute();
                    $q1->bind_result($courseid,$courseName);
                    $q1->store_result();
                    while($q1->fetch())
                    {
                        echo '<option value="'.$courseid.'">'.$courseid.' '.$courseName.'</option>';
                    }
                
                ?>
            </select>
            </div>
            
            <script>
var x, i, j, l, ll, selElmnt, a, b, c;
/*look for any elements with the class "custom-select":*/
x = document.getElementsByClassName("custom-select");
l = x.length;
for (i = 0; i < l; i++) {
  selElmnt = x[i].getElementsByTagName("select")[0];
  ll = selElmnt.length;
  /*for each element, create a new DIV that will act as the selected item:*/
  a = document.createElement("DIV");
  a.setAttribute("class", "select-selected");
  a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;
  x[i].appendChild(a);
  /*for each element, create a new DIV that will contain the option list:*/
  b = document.createElement("DIV");
  b.setAttribute("class", "select-items select-hide");
  for (j = 1; j < ll; j++) {
    /*for each option in the original select element,
    create a new DIV that will act as an option item:*/
    c = document.createElement("DIV");
    c.innerHTML = selElmnt.options[j].innerHTML;
    c.addEventListener("click", function(e) {
        /*when an item is clicked, update the original select box,
        and the selected item:*/
        var y, i, k, s, h, sl, yl;
        s = this.parentNode.parentNode.getElementsByTagName("select")[0];
        sl = s.length;
        h = this.parentNode.previousSibling;
        for (i = 0; i < sl; i++) {
          if (s.options[i].innerHTML == this.innerHTML) {
            s.selectedIndex = i;
            h.innerHTML = this.innerHTML;
            y = this.parentNode.getElementsByClassName("same-as-selected");
            yl = y.length;
            for (k = 0; k < yl; k++) {
              y[k].removeAttribute("class");
            }
            this.setAttribute("class", "same-as-selected");
            break;
          }
        }
        h.click();
    });
    b.appendChild(c);
  }
  x[i].appendChild(b);
  a.addEventListener("click", function(e) {
      /*when the select box is clicked, close any other select boxes,
      and open/close the current select box:*/
      e.stopPropagation();
      closeAllSelect(this);
      this.nextSibling.classList.toggle("select-hide");
      this.classList.toggle("select-arrow-active");
    });
}
function closeAllSelect(elmnt) {
  /*a function that will close all select boxes in the document,
  except the current select box:*/
  var x, y, i, xl, yl, arrNo = [];
  x = document.getElementsByClassName("select-items");
  y = document.getElementsByClassName("select-selected");
  xl = x.length;
  yl = y.length;
  for (i = 0; i < yl; i++) {
    if (elmnt == y[i]) {
      arrNo.push(i)
    } else {
      y[i].classList.remove("select-arrow-active");
    }
  }
  for (i = 0; i < xl; i++) {
    if (arrNo.indexOf(i)) {
      x[i].classList.add("select-hide");
    }
  }
}
/*if the user clicks anywhere outside the select box,
then close all select boxes:*/
document.addEventListener("click", closeAllSelect);

    </script>
    
        </label>
        <br>
    <?php echo $error5; ?>
    <br>
</div>
                
        <style>
    <?php include 'ttinschedule.css';?>
    </style>
        <?php
         
            $days = array("Monday","Tuesday","Wednesday","Thursday","Friday");
            $all_slots = array("8-9","9-10","10:15-11:15","11:15-12:15","13:00-14:00","14:00-15:00","15:00-16:00","16:00-17:00");
            $slot_id_arr = array(1,2,3,4,5,6,7,8);
            echo "<center><justify><table class='table1' cellspacing=0 cellpadding=0>";
            echo '<tr><center>';
            echo '<th class="days"><center>' . "DAY/SLOT" . '</center></th>';
            foreach($all_slots as $s){
                echo '<th class="time"><center>' . $s . '</center></th>';
            }
            echo '</center></tr>';
        
            foreach ($days as $d){
                slots_for_day($d,$conn,$prof,$slot_id_arr);
            }
            echo"</table></justify></center><br>";
            echo"</table>";
        ?>
        <center>
            <div class="input__box">
        <input type="submit" name="checktt" value="Check">
        </center>
        </form>
</form>
</form> 
</body>

</html>