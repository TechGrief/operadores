<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if(isset($_POST["username"], $_POST["password"], $_POST["email"], $_POST["phone"], $_POST["key"])){//NOT SETUP
    require("./assets/mysql.php");

    $sql_keycheck = $mysql->prepare('SELECT * from `reg_keys` WHERE `key` = :rkey');
    $rkey = $_POST["key"];
    $sql_keycheck->bindParam(":rkey", $rkey);
    if($sql_keycheck->execute() && $sql_keycheck->rowCount() != 0){
        $sql_keydelete = $mysql->prepare('DELETE from `reg_keys` WHERE `key` = :rkey');
        $rkey = $_POST["key"];
        $sql_keydelete->bindParam(":rkey", $rkey);
        
        if($sql_keydelete->execute() && $sql_keydelete->rowCount() != 0){
            
            $stmt = $mysql->prepare('INSERT INTO `accounts` (`name`, `email`, `phone`, `admin`, `password`, `active`, `level`) VALUES (:xname,:xemail,:xphone,:xadmin,:xpassword, 1, 2)');
            $xname = $_POST["username"];
            $stmt->bindParam(":xname", $xname);
            $xpassword = password_hash($_POST["password"], PASSWORD_DEFAULT);
            $stmt->bindParam(":xpassword", $xpassword);
            $xemail = $_POST["email"];
            $stmt->bindParam(":xemail", $xemail);
            $xphone = $_POST["phone"];
            $stmt->bindParam(":xphone", $xphone);
            
            $isadmin = ($_POST["key"] == "KEYWILLBEDELETEDAFTERFIRSTUSE" ? 1 : 0);
            $stmt->bindParam(":xadmin", $isadmin);
            
            if($stmt->execute() && $stmt->rowCount() != 0){
                header("Location: ./?alert=Willkommen \"".$_POST["username"]."\"!");
                die();
            }
        }
    }
    
}
header("Location: /?alert=Irgendwas tut hier nicht 0_0");
die();
?>