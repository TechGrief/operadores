<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
session_start();
if(isset($_POST["username"]) && isset($_POST["password"])){
//echo realpath("./assets/mysql.php");
    require("./assets/mysql.php");
    $stmt = $mysql->prepare('SELECT * FROM `accounts` WHERE `name` = :uname OR `email` = :uname OR `phone` = :uname');
    $stmt->bindParam(":uname", $_POST["username"]);
    
    if($stmt->execute() && $stmt->rowCount() != 0){
        $row = $stmt->fetch();
        if(!$row["active"] && !$row["admin"]){
            header("Location: ./?alert=Dein Account wurde suspendiert!");
            die();
        }else {
            if(password_verify($_POST["password"], $row["password"])){
           //echo("-->".$_POST["return"]);
                $_SESSION["loggedin"] = true;
                $_SESSION["username"] = $row["name"];
                $_SESSION["id"] = $row["id"];
                $_SESSION["email"] = $row["email"];
                $_SESSION["phone"] = $row["phone"];
                $_SESSION["start_date"] = $row["start_date"];
                $_SESSION["active"] = $row["active"];
                $_SESSION["end_date"] = $row["end_date"];
                $_SESSION["admin"] = $row["admin"];
                $_SESSION["level"] = $row["level"];
                header("Location: ".$_POST["return"]);
                die();
            }else{
                header("Location: ./?alert=Benutzername und/oder Passwort falsch");
                die();
            }
        }
    }else{
        header("Location: ./?alert=Benutzername und/oder Passwort falsch");
        die();
    }
}

?>
