<?php 
    session_start();
    include 'dbconnection.php';
    $conn = OpenCon();
    $_SESSION["cday"] = "Monday";
    $_SESSION["cday"] = flush_database($conn,$_SESSION["cday"]);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="wel.css">
        <meta charset="UTF-8">
        <script src="https://kit.fontawesome.com/faeaa9a8c9.js" crossorigin="anonymous"></script>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel = "stylesheet" href = "style.css">
        <title>Document</title>
        <style>
            .fa-solid{
                font-size: 150pt;
                color: #000000; 
            }
            .flex-child {
              padding: 30px;
              border: 1px ;
            }
        </style>
    </head>
    <body>
        <div class="header">
          <a href="#default" class="logo"><span><img src="Nitc_logo.png"> </span>Online Extra Class Management System</a>
        </div>

        <div class="boody">
        
        <div class="flex-container">
        
          <div onclick="window.location.href='student.php';" class="flex-child magenta">
          <img src="stud.png" class="stud">
          <center>
            <section class="header-content">
                <a href="student.php">
                    <button onclick="window.location.href='student.php';">Student</button>
                </a>

            </section>
            </center>
          </div>
          
          <div onclick="window.location.href='professor.php';" class="flex-child green">
          <img src="prof.png" class="stud">
            <center>
                <section class="header-content">
            <button onclick="window.location.href='professor.php';">Professor</button>
              </section>
            </center>
          </div>
          
        </div>
        
        </div>
        </div> 
      </body>
</html>