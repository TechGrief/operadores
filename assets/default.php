<?php
session_start();

function GetNavbarCode($active = 1){
  if(AdminCheck()){
    echo('
    <form action="/adminer/index.php" method="post" id="dbform" target="_blank">
      <input type="hidden" name="auth[username]" value="'.$_SERVER["mysql_user"].'">
      <input type="hidden" name="auth[password]" value="'.$_SERVER["mysql_pw"].'">
      <input type="hidden" name="auth[db]" value="'.$_SERVER["mysql_db"].'">
      <input type="hidden" name="auth[driver]" value="server">
      <input type="hidden" name="auth[server]" value="'.$_SERVER["mysql_host"].'">
    </form>
    ');
  }
    return '
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand" href="/">
      <img src="/assets/favicon.ico" width="30" height="30" class="d-inline-block align-top" alt="">
    </a>
  
    <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
      <ul class="navbar-nav mr-auto mt-2 mt-md-0">
        <li class="nav-item '.($active == 1 ? 'active' : '').'">
          <a class="nav-link" href="../mainpage.php">Übersicht '.($active == 1 ? '<span class="sr-only">(current)</span>' : '').'</a>
        </li>
        '.(AdminCheck() ? '
          <li class="nav-item '.($active == 2 ? 'active' : '').'">
            <a class="nav-link" href="../dashboard.php">Dashboard '.($active == 2 ? '<span class="sr-only">(current)</span>' : '').'</a>
          </li>
          <li class="nav-item '.($active == 3 ? 'active' : '').'">
            <a class="nav-link" href="../accounts.php">Accounts '.($active == 3 ? '<span class="sr-only">(current)</span>' : '').'</a>
          </li>':'').'
      </ul>
    </div>

    <div class="btn-group navbar-brand float-right">
      <i class="bi bi-person-circle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="cursor:pointer;"></i>
      <div class="dropdown-menu dropdown-menu-right">
        
      <a class="dropdown-item" style="cursor:pointer;" href="/account.php"><i class="bi bi-person-fill text-success" style="font-size: 16px; -webkit-text-stroke: 1px;"></i> Account</a>
      
      '.(AdminCheck() ? '<button type="submit" value="Submit" form="dbform" class="dropdown-item" style="cursor:pointer;" href="/?logoff"><i class="bi bi-database text-warning" style="font-size: 16px; -webkit-text-stroke: 1px;"></i> Datenbank</button>'
        : '').'
        
        <a class="dropdown-item" style="cursor:pointer;" href="/?logoff"><i class="bi bi-power text-danger" style="font-size: 16px; -webkit-text-stroke: 1px;"></i> Abmelden</a>
      </div>
    </div>

  </nav>

  <div class="m-2 mt-3 rounded" style="border-width:2.5px !important;">
  ';
}


function GetHtmlToBodyCode(){
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
  return '
  <!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.2.1/dist/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.2.1/dist/js/bootstrap.js"></script>


    <!--  jQuery -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
    <!-- Bootstrap Date-Picker Plugin -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker3.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.de.min.js"></script>
  
    <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS 
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  --><script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.6/dist/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.2.1/dist/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>

  
<title>ZP30</title>
<link rel="icon" type="image/x-icon" href="/assets/favicon.ico">

  <style>
    body {
      background-color: #F3EEEA;//#F3EEEA
    }
  </style>

</head>

<body>
';
}
function GetBodyToEndCode(){
  return '
  </div>


  
</body>
</html>
';
}


function LoginCheck($verify = false){
  $login = (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] = true);
  if(!$login){
    header("Location: /?return=".$_SERVER['REQUEST_URI']);
    die();
  }
  if($verify != false){


    require("./assets/mysql.php");
    $stmt = $mysql->prepare('SELECT * FROM `accounts` WHERE `name` = :uname');
    $stmt->bindParam(":uname", $_SESSION["username"]);
    $stmt->execute();
    if($stmt->rowCount() != 0){
        $row = $stmt->fetch();
        if(password_verify($verify, $row["password"])){
          return true;
        }else{
          header("Location: /?logoff");
          die();
        }
    }else{
      header("Location: /?logoff");
      die();
    }



  }else return $login;
  
  return false;
}
function ReloadSession(){
  if(LoginCheck()){
    require("./assets/mysql.php");
    $stmt = $mysql->prepare('SELECT * FROM `accounts` WHERE `name` = :uname');
    $stmt->bindParam(":uname", $_SESSION["username"]);
    $stmt->execute();
    if($stmt->rowCount() != 0){
        $row = $stmt->fetch();
        $_SESSION["username"] = $row["name"];
        $_SESSION["id"] = $row["id"];
        $_SESSION["email"] = $row["email"];
        $_SESSION["phone"] = $row["phone"];
        $_SESSION["start_date"] = $row["start_date"];
        $_SESSION["active"] = $row["active"];
        $_SESSION["end_date"] = $row["end_date"];
        $_SESSION["admin"] = $row["admin"];
        $_SESSION["level"] = $row["level"];
    }else{
      header("Location: /?logoff");
      die();
    }
  }
}
function AdminCheck(){
  return ($_SESSION["admin"]);
}
?>