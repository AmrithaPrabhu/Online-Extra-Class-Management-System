<?php
session_start();
include 'dbconnection.php';
$conn = OpenCon();
$_SESSION["cday"] = flush_database($conn,$_SESSION["cday"]);


$roll="";
$error1="";
$wrong="";

function clean_kuttapi($string){
    $string=trim($string);
    $string = stripslashes($string);
    $string = htmlspecialchars($string);
    return $string;
}

if(isset($_POST['checktt']))
{
    if(empty($_POST['rollno']))
    {
        $error1= "<p>please enter roll number</p>";
    }
    if(!empty($_POST['rollno']))
    {
        $roll= clean_kuttapi($_POST['rollno']);
    }

    if(strlen($error1)==0)
    {
        $q="SELECT batch_id FROM student WHERE roll_no=? limit 1";
        $q1=$conn->prepare($q);
        $q1->bind_param("s",$roll);
        $q1->execute();
        $q1->bind_result($batch_id);
        $q1->store_result();
        $flag=0;
            while($q1->fetch())
            {
                $_SESSION["bid"]=$batch_id;
                $_SESSION["rollnum"]=$roll;
                $flag=1;
            }
           if($flag==0)
           {
             $wrong= "Roll No doesnt exist";
           }
           if($flag==1)
           {
            $_SESSION["rollnum"]=$roll;
            header('Location: ' . "viewtt_student.php");
            die();
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
    <link rel="stylesheet" href="bg.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <title>Student Login</title>
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
               <li><a href='home.php'> &laquo Home</a></li>
             </ul>
           </nav>
    <!-- logo -->
          <div class="logo"><span>
            <img src="Nitc_logo.png">
        </span></div>
    <!-- title & content -->
          <section class="header-content">
             <h1>Student Login</h1>
          </section>

        <div class="glass-panel">
            <div class="form">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="input__box">
                        <input type="text" name="rollno" placeholder="Roll Number" class = "inp"/>
                        <?php echo $error1; ?>
                        <?php echo "<br>"; ?>
                        <?php echo $wrong; ?>
                    </div>
                    <div class="input__box">
                        <br>
                        <center>
                            <input type="submit" name="checktt"value="Login" class="inp_butt"/>    
                        </center>
                    </div>                
                </form>
            </div>
        </div>
    </div>
    </header>
</body>
</html>
