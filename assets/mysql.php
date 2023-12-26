<?php
$host = $_SERVER["mysql_host"];
$name = $_SERVER["mysql_db"];
$user = $_SERVER["mysql_user"];
$passwort = $_SERVER["mysql_pw"];
try{
    $mysql = new PDO("mysql:host=$host;dbname=$name", $user, $passwort);
} catch (PDOException $e){
    echo "SQL Error: ".$e->getMessage();
}
?>
