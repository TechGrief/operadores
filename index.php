
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.2.1/dist/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <title> Operadores - ZP30 </title>

  <style>
    body {
      background-color: #F3EEEA;
    }
  </style>

</head>

<body>

<?php
session_start();
require("./assets/default.php");
require("./assets/popup.php");
if(isset($_GET["logoff"])){
    // Finds all server sessions
    session_start();
    // Stores in Array
    $_SESSION = array();
    // Swipe via memory
    if (ini_get("session.use_cookies")) {
        // Prepare and swipe cookies
        $params = session_get_cookie_params();
        // clear cookies and sessions
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    // Just in case.. swipe these values too
    ini_set('session.gc_max_lifetime', 0);
    ini_set('session.gc_probability', 1);
    ini_set('session.gc_divisor', 1);
    // Completely destroy our server sessions..
    session_destroy();
    header("Location: /");
    die();
    exit(0);
}
else if(isset($_GET["alert"])){//Displays all kinds of warnings
    $p2 = new PopUp(
        title: "Warnung",
        description: $_GET["alert"],
        type: "alert",
        allowclose: false,
        submit_text: "Weiter",
        submit_url: "./"
    );
    if($p2->generateCode()) {
        echo($p2->getCode());
        echo('<script>'.$p2->script_js().'</script>');
    }
    else {
        echo("ERROR, contact admin please! x2");
    }
}else if(isset($_GET["live"])){//Displays all kinds of warnings
    require("./assets/mysql.php");
    $stmt1 = $mysql->prepare('SELECT * FROM `shifts` WHERE date_start = :vdate OR date_end = :vdate');
    $searchdate = date('Y-m-d', strtotime(date("Y-m-d"). ' - 0 days'));
    $searchdateDAY = date("d");
    $searchdateMONTH = date("m");
    date_default_timezone_set("America/Asuncion");
    $searchdateHour = date("H");
    $searchdateMINUTE = date("i");
    $searchdateTIME = date("H:i");
    //echo "The time is " . date("h:i:sa");
    $stmt1->bindParam(":vdate", $searchdate);
    if($stmt1->execute()){
        if($stmt1->rowCount() != 0){
            $row = $stmt1->fetch();
            $ALLjsondec = json_decode($row["shifts"]);
            $desc = "";
            foreach($ALLjsondec as $shifts=>$data){
                $desc .= ('<fieldset class="font-weight-'.($data->inuse ? 'normal text-success' : 'bold text-danger font-italic').'"><i class="bi bi-info-circle"></i>');
                $desc .= ('<span>
                        '.$shifts.' '.(!$data->inuse ? '(?)':"").'
                    </span>');
                if($data->inuse){
                    $desc .= ('<br><i class="bi bi-arrow-return-right ml-4"></i>');
                    $desc .= ('<span class="font-weight-normal">
                        '.$data->user.'
                    </span>');
                    foreach($data->events as $eventname=>$eventdata){
                        $desc .= ('<br><i class="bi bi-arrow-return-right ml-4 invisible"></i><i class="bi bi-arrow-return-right ml-1"></i>
                        <span class="font-weight-normal">
                        '.date("d/m", strtotime($eventname)).' ('.$eventdata->time_start.' -> '.$eventdata->time_end.')
                        '.((date("d", strtotime($eventname)) == $searchdateDAY && date("m", strtotime($eventname) == $searchdateMONTH)
                        && date("H:i", strtotime($eventdata->time_start)) < $searchdateTIME && date("H:i", strtotime($eventdata->time_end)) > $searchdateTIME
                        )
                        ? '<span class="badge badge-white rounded" style="border: 2px solid indianred; color:indianred; opacity: 0.8; font-size: 14px; letter-spacing: .5px;">
                            <i class="bi bi-record-circle-fill" style="color: indianred;"></i>
                            Live
                           </span>'
                        : '').'
                        </span>');
                    }
                }else{
                    $desc .= ('<br><i class="bi bi-arrow-return-right ml-4 text-danger"></i>');
                    $desc .= ('<span class="font-weight-normal">
                        ?
                    </span>');
                }
                $desc .= ('</fieldset>');
            }
            $p2 = new PopUp(
                title: "Live: ".$row["name"],
                description: $desc,
                type: "alert",
                allowclose: false,
                submit_text: "Login",
                submit_url: "./"
            );
            if($p2->generateCode()) {
                echo($p2->getCode());
                echo('<script>'.$p2->script_js().'</script>');
            }
            else {
                echo("ERROR, contact admin please! x4");
            }
            die;
        }
    }
    $p2 = new PopUp(
        title: "N/A",
        description: 'Heute ist hier nichts los',
        type: "alert",
        allowclose: false,
        submit_text: "Login",
        submit_url: "./"
    );
    if($p2->generateCode()) {
        echo($p2->getCode());
        echo('<script>'.$p2->script_js().'</script>');
    }
    else {
        echo("ERROR, contact admin please! x3");
    }
}
else if(isset($_GET["setup"])){//Only works if there arent any user in accounts-table-db. Creates the first admin [the key "KEYWILLBEDELETEDAFTERFIRSTUSE" needs to be added in "reg_keys"]
    require("./assets/mysql.php");
    $sql_checkiftablesexist = $mysql->prepare('SELECT `TABLE_SCHEMA` FROM information_schema.tables WHERE table_schema = :dbname AND table_name IN ("reg_keys", "shifts", "accounts")');
    $dbname = $name;
    $sql_checkiftablesexist->bindParam(":dbname",$dbname);

    if($sql_checkiftablesexist->execute()){
        if($sql_checkiftablesexist->rowCount() < 3){
            $sql_createtable_accounts = $mysql->prepare('CREATE TABLE `accounts` (`id` BIGINT NOT NULL AUTO_INCREMENT , `name` VARCHAR(64) NOT NULL DEFAULT "Unknown" , `email` VARCHAR(128) NOT NULL DEFAULT "Unknown@domain" , `phone` varchar(32) NOT NULL DEFAULT "0" , `start_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `active` BOOLEAN NULL DEFAULT NULL , `end_date` TIMESTAMP NULL DEFAULT NULL , `admin` BOOLEAN NULL DEFAULT NULL , `level` INT NULL DEFAULT NULL , `password` VARCHAR(255) NOT NULL, PRIMARY KEY (`id`)) ENGINE = InnoDB;');
            $sql_createtable_accounts->execute();
            $sql_createtable_shifts = $mysql->prepare('CREATE TABLE `shifts` (`id` BIGINT NOT NULL AUTO_INCREMENT , `name` VARCHAR(64) NOT NULL DEFAULT "Undefined-Shift" , `description` VARCHAR(255) NULL DEFAULT NULL , `shifts` JSON NOT NULL , `priority` TINYINT(16) NOT NULL DEFAULT "0" , `date_start` DATE NOT NULL , `date_end` DATE NOT NULL , `active` BOOLEAN NULL DEFAULT TRUE, PRIMARY KEY (`id`)) ENGINE = InnoDB;');
            $sql_createtable_shifts->execute();
            $sql_createtable_reg_keys = $mysql->prepare('CREATE TABLE `reg_keys` (`key` varchar(32) NOT NULL);');
            $sql_createtable_reg_keys->execute();
            $genkey = "KEYWILLBEDELETEDAFTERFIRSTUSE";
            $sql_inserkey = $mysql->prepare("INSERT INTO `reg_keys` (`key`) VALUES (:genkey);");
            $sql_inserkey->bindParam(":genkey", $genkey);
            $sql_inserkey->execute();
        }
        $stmt = $mysql->prepare('SELECT COUNT(*) FROM accounts;');
        $stmt->execute();
        if($stmt->rowCount() != 0){
            $row = $stmt->fetch();
            if($row["COUNT(*)"] == 0){
                $p1 = new PopUp(
                    title: "Willkommen",
                    submit_text: "Loslegen",
                    submit_url: "./register.php",
                    allowclose: false,
                    method: "post",
                    description: "Beginne mit dem Einrichten deines neuen Administratorkontos",
                    type: "input_1"
                );
                $p1->addInput(new Input(id: "username", type: "text", title: "Benutzername"));
                $p1->addInput(new Input(id: "email", type: "email", title: "EMail"));
                $p1->addInput(new Input(id: "phone", type: "text", title: "Telefonnummer", placeholder:"595 9XX XXX XXX"));
                $p1->addInput(new Input(id: "password", type: "password", title: "Passwort"));
                //$p1->addInput(new Input(id: "admin", type: "hidden", value: "true"));
                $p1->addInput(new Input(id: "key", type: "hidden", value: "KEYWILLBEDELETEDAFTERFIRSTUSE"));
                if($p1->generateCode()) {
                    echo($p1->getCode());
                    echo('<script>'.$p1->script_js().'</script>');
                }
                else {
                    echo("ERROR, contact admin please! x3");
                }
            }else{
                header("Location: ?alert=Ohohhhhh... STOP!");
            }
        }
    }else{
        header("Location: ?alert=Ohohhhhh... SQL befehlt konnte nicht ausgeführt werden!");
        die();
    }
}
else if(!isset($_SESSION["loggedin"]) && isset($_GET["reg_key"])){//Register with reg_key
    require("./assets/mysql.php");
    $sql_keycheck = $mysql->prepare('SELECT * from `reg_keys` WHERE `key` = :rkey');
    $rkey = $_GET["reg_key"];
    $sql_keycheck->bindParam(":rkey", $rkey);
    if(!$sql_keycheck->execute() || $sql_keycheck->rowCount() == 0){
        header("Location: ?alert=Ohohhhhh... Du brauchst einen neuen key 0_0");
        die();
    }
    $p1 = new PopUp(
        title: "Willkommen",
        submit_text: "Loslegen",
        submit_url: "./register.php",
        allowclose: false,
        method: "post",
        description: "Es ist soweit!<br>Erstelle dir nun deinen eigenen <u>Operadores</u> Account.",
        type: "input_1", //can be alert or input_1 (alert isn't that usefull)
        img: "https://i.postimg.cc/hGrMLqdG/2023-07-09-20-04-33-482-Photo-Room.jpg"
    );
    $p1->addInput(new Input(id: "username", type: "text", title: "Benutzername"));
    $p1->addInput(new Input(id: "email", type: "email", title: "Email"));
    $p1->addInput(new Input(id: "phone", type: "text", title: "Telefonnummer", placeholder:"595 9XX XXX XXX"));
    $p1->addInput(new Input(id: "password", type: "password", title: "Passwort"));
    $p1->addInput(new Input(id: "key", type: "hidden", value: $_GET["reg_key"]));
    if($p1->generateCode()) {
        echo($p1->getCode());
        echo('<script>'.$p1->script_js().'</script>');
    }
    else {
        echo("ERROR, contact admin please! x4");
    }
}
else if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] != true){//Login
    $p1 = new PopUp(
        title: "Bitte anmelden",
        submit_text: "Anmelden",
        submit_url: "./login.php",
        allowclose: false,
        method: "post",
        description: (isset($_GET["alert"]) ? $_GET["alert"]: null),
        type: "input_1", //can be alert or input_1 (alert isn't that usefull)
        img: "https://i.postimg.cc/hGrMLqdG/2023-07-09-20-04-33-482-Photo-Room.jpg"
    );
    $p1->addInput(new Input(id: "username", type: "text", title: "Benutzername"));
    $p1->addInput(new Input(id: "password", type: "password", title: "Passwort"));
    $p1->addInput(new Input(id: "return", type: "hidden", value: (isset($_GET["return"]) ? $_GET["return"] : "/")));
    if($p1->generateCode()) {
        echo($p1->getCode());
        echo('<script>'.$p1->script_js().'</script>');
    }
    else {
        echo("ERROR, contact admin please! x1");
    }
}else if(isset($_GET["out"])){
    session_unset();
    session_destroy();
    header("Location: ./");
}else if(LoginCheck()){
    
    ?>

    
<iframe src="./mainpage.php" style="position:fixed; top:0; left:0; bottom:0; right:0; width:100%; height:100%; border:none; margin:0; padding:0; overflow:hidden; z-index:999999;">
    Your browser doesn't support iframes
</iframe>
    


    <?php

}
?>
