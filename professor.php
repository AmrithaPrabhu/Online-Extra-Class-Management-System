<?php

session_start();
include 'dbconnection.php';
$conn = OpenCon();
$_SESSION["cday"] = flush_database($conn,$_SESSION["cday"]);

$id="";
$username="";
$password="";
$u="";
$p="";
$err="";
$err1="";
$err2="";
$random="";

function clean_kuttapi($string){
    $string=trim($string);
    $string = stripslashes($string);
    $string = htmlspecialchars($string);
    return $string;
}

if(isset($_POST['login']))
{
    if(empty($_POST['username']))
    {
        $err1= "<p>Please enter username.</p>";
    }
    else
    {
        $username = explode(" ",clean_kuttapi($_POST['username']));
        $username[1] = implode(" ",array_slice($username,1));
        $username = array_slice($username,0,2);
    }

    if(empty($_POST['password']))
    {
        $err2= "<p>Please enter password</p>";
    }
    else
    {
        $password= clean_kuttapi($_POST['password']);
    }
    if(strlen($err1) == 0 && strlen($err2) == 0){
        $q = "select prof_id, password from professor where first_name = ? and second_name = ? and password = ? limit 1";
        $q1 = $conn->prepare($q);
        $q1->bind_param("sss", $username[0],$username[1],$password);
        $q1->execute();
        $q1->bind_result($id,$p);
        $flag=0;
        $q1->store_result();
        while($q1->fetch()){
            if(strcmp($password,$p)==0)
            {
                $_SESSION["pid"]=$id;
                echo $_SESSION["pid"];
                $flag=1;
            }
        }
        if($flag==0){
            $err= "invalid credentials";
        }
    }
    if(strlen($err) == 0 && strlen($err1) == 0 && strlen($err2) == 0){
        echo "hi";
        header('Location: ' . "functions.php");
        die();
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
    <link rel="stylesheet" href="bg.css">
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
          <div class="logo"><span><img src="Nitc_logo.png"> </span></div>
    <!-- title & content -->
          <section class="header-content">
          <h1> Professor Login</h1>
          </section>
            <div class="glass-panel" >
                <div class="form">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method = "post">
                        <div class="input__box">
                            <input type="text" name="username" placeholder="Username" />
                            <?php echo $err1; ?>
                        </div>
                        <div class="input__box">
                            <input type="password" name="password" placeholder="Password"  />
                            <?php echo $err2; ?>
                        </div>
                        <div>                                
                            <?php
                                if(strlen($err)>0)
                                        echo $err;
                            ?>
                        </div>
                        <div class="input__box">
                                <input type="submit" name ="login" value="Login" />
                        </div>
                    </form>
                </div>

                </div>
              </div>
      </header>
    </div>
</body>
</html>

