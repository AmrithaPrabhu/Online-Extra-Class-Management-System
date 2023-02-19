<?php 
    session_start();
    include 'dbconnection.php';
    $conn = OpenCon();
    $_SESSION["cday"] = flush_database($conn,$_SESSION["cday"]);
    
    $prof=$_SESSION["pid"];
    

    if(isset($_POST['views']))
    {
        if(!empty($_POST['batch'])) 
        {
            $selected_batch = $_POST['batch'];
            $_SESSION["selectedbatch"]=$selected_batch;
            header('Location: ' . "prof_print_studenttt.php");
            die();
        } 
        else 
        {
            echo 'Please select the value.';
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
</head>
<body>
    <!-- Background & animion & navbar & title -->
  <div class="container-fluid">
    <!-- Background animtion-->
        <div class="background">
           <div class="cube"></div>
           <div class="cube"></div>
           <div class="cube"></div>
           <div class="cube"></div>
          <div class="cube"></div>
        </div>
    <!-- header -->
       <header>
        <!-- navbar -->
     <nav>
        <ul>
          <li><a href='functions.php'> &laquo Back</a></li>
         </ul>
       </nav>
    <!-- logo -->
          <div class="logo"><span><img src="Nitc_logo.png"> </span></div>

            <div class="glass-panel" >
                <div class="form">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <label for="">

<div class="select">
            <select name="batch" id="">
            <option value="" disbaled selected hidden>Select Branch and Batch&nbsp&#x25BC</option>
</div>
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
                        echo '<option value="'.$batchid.'">'.$dept.' '.$batch.' S'.$semester.'</option>';
                    }
                ?>
            </select>
                </div>
    </label>
<div class="input_box">
    <input type="submit" name="views" value="VIEW TIMETABLE">
                </div>
</form>
                </div>

                </div>
              </div>
      </header>
    </div>
</body>
</html>

