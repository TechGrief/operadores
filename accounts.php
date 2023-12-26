<?php
require("./assets/default.php");
if(LoginCheck() && AdminCheck()){
echo GetHtmlToBodyCode();
echo GetNavbarCode(3);
session_start();
require("./assets/mysql.php");
echo('<div class="container mx-auto p-0 pt-3" style="max-width: 43rem;">');

if(isset($_GET["userid"])){
  if(isset($_GET["new_lvl"])){
    $stmt2 = $mysql->prepare('UPDATE `accounts` SET `level` = :new_lvl WHERE `id` = :userid;');
    
    $new_lvl = $_GET["new_lvl"];
    $stmt2->bindParam(":new_lvl", $new_lvl);
    $userid = $_GET["userid"];
    $stmt2->bindParam(":userid", $userid);
    $stmt2->execute();
    if($stmt2->rowCount() == 0){
      echo('<div class="alert alert-danger" role="alert">
      <strong>Ooooops...</strong> Unerwarteter Fehler aufgetreten
      </div>');
    }else header('Location: accounts.php');
  }else if(isset($_GET["active"])){
    if($_GET["active"] == "true") $active = "1"; else $active = "0";
    $stmt2 = $mysql->prepare('UPDATE `accounts` SET `active` = :active WHERE `id` = :userid;');
    
    $stmt2->bindParam(":active", $active);
    $userid = $_GET["userid"];
    $stmt2->bindParam(":userid", $userid);
    $stmt2->execute();
    if($stmt2->rowCount() == 0){
      echo('<div class="alert alert-danger" role="alert">
      <strong>Ooooops...</strong> Unerwarteter Fehler aufgetreten
      </div>');
    }else {
      if(!$active) header("Location: accounts.php?userid=$userid&new_lvl=3");
      else header('Location: accounts.php');
    }
    die();
  }
  else if(isset($_GET["new_admin"])){
    $stmt3 = $mysql->prepare('UPDATE `accounts` SET `admin` = :new_admin WHERE `id` = :userid;');
    
    $new_admin = $_GET["new_admin"];
    $stmt3->bindParam(":new_admin", $new_admin);
    $userid = $_GET["userid"];
    $stmt3->bindParam(":userid", $userid);
    $stmt3->execute();
    if($stmt3->rowCount() == 0){
      echo('<div class="alert alert-danger" role="alert">
      <strong>Ooooops...</strong> Unerwarteter Fehler aufgetreten
      </div>');
    }else header('Location: accounts.php');
  }
  else if(isset($_GET["new_pw"])){
    $stmt3 = $mysql->prepare('UPDATE `accounts` SET `password` = :new_pw WHERE `id` = :userid;');
    $new_pw = generateRandomString(8);
    $new_pw_hash = password_hash($new_pw, PASSWORD_DEFAULT);;

    $stmt3->bindParam(":new_pw", $new_pw_hash);
    $userid = $_GET["userid"];
    $stmt3->bindParam(":userid", $userid);
    $stmt3->execute();
    if($stmt3->rowCount() == 0){
      echo('<div class="alert alert-danger" role="alert">
      <strong>Ooooops...</strong> Unerwarteter Fehler aufgetreten
      </div>');
    }else header('Location: accounts.php?alert=Passwort: '.$new_pw.'. Dieses wird dir nur hier <u>einmal</u> angezeigt!');
  }
  else if(isset($_GET["delete"])){
    $stmt3 = $mysql->prepare('DELETE FROM `accounts` WHERE `id` = :userid;');
    
    $userid = $_GET["userid"];
    $stmt3->bindParam(":userid", $userid);
    $stmt3->execute();
    if($stmt3->rowCount() == 0){
      echo('<div class="alert alert-danger" role="alert">
      <strong>Ooooops...</strong> Unerwarteter Fehler aufgetreten
      </div>');
    }else header('Location: accounts.php');
  }
}
$GENERATEDREGKEYS = array();

if(isset($_GET["generateurl"])){
  $genkey = generateRandomString(32);
  $smtp_inserkey = $mysql->prepare("INSERT INTO `reg_keys` (`key`) VALUES (:genkey);");
  $smtp_inserkey->bindParam(":genkey", $genkey);
  if(!$smtp_inserkey->execute() || $smtp_inserkey->rowCount() == 0){
    echo('<div class="alert alert-danger" role="alert">
    <strong>Ooooops...</strong> Unerwarteter Fehler aufgetreten
    </div>');
  }
}else if(isset($_GET["generateadminurl"])){
  $genkey = "KEYWILLBEDELETEDAFTERFIRSTUSE";
  $smtp_inserkey = $mysql->prepare("INSERT INTO `reg_keys` (`key`) VALUES (:genkey);");
  $smtp_inserkey->bindParam(":genkey", $genkey);
  if(!$smtp_inserkey->execute() || $smtp_inserkey->rowCount() == 0){
    echo('<div class="alert alert-danger" role="alert">
    <strong>Ooooops...</strong> Unerwarteter Fehler aufgetreten
    </div>');
  }
}else if(isset($_GET["deleteallgenerateurl"])){
  $sql_deleteallgenurl = $mysql->prepare("DELETE FROM `reg_keys`;");
  if(!$sql_deleteallgenurl->execute() || $sql_deleteallgenurl->rowCount() == 0){
    echo('<div class="alert alert-danger" role="alert">
    <strong>Ooooops...</strong> Unerwarteter Fehler aufgetreten
    </div>');
  }
}else if(isset($_GET["deletegenerateurl"])){
  $sql_deleteallgenurl = $mysql->prepare("DELETE FROM `reg_keys` WHERE `key` = :deletegenerateurl;");
  $deletegenerateurl = $_GET["deletegenerateurl"];
  $sql_deleteallgenurl->bindParam(":deletegenerateurl", $deletegenerateurl);
  if(!$sql_deleteallgenurl->execute() || $sql_deleteallgenurl->rowCount() == 0){
    echo('<div class="alert alert-danger" role="alert">
    <strong>Ooooops...</strong> Unerwarteter Fehler aufgetreten
    </div>');
  }
}else if(isset($_GET["alert"])){
  echo('<div class="alert alert-success" role="alert">
      <strong>'.$_GET["alert"].'</strong>
      </div>');
}

  $stmt1 = $mysql->prepare('SELECT * FROM `accounts`');
  $stmt1->execute();
  if($stmt1->rowCount() != 0){
    
    echo('
  

  <div class="card shadow mb-4">
  
  <h2 class="card-header bg-primary text-white">
    Nutzer hinzufügen
  </h2>
  <div class="card-body">
    <p class="card-text">
    Klicke auf "Link generieren", mit diesem kann jeweils nur ein Account erstellt werden!
    </p>
    
    <table class="table table-bordered shadow ">
      <thead class="thead-dark">
        <tr>
          <th>Key</th>
          <th>Shortcuts</th>
        </tr>
      </thead><tbody>');
      $smtp_getkeys = $mysql->prepare("SELECT * from `reg_keys`;");
      if($smtp_getkeys->execute() && $smtp_getkeys->rowCount() != 0){
        while($key = $smtp_getkeys->fetch()){
            echo('<tr '.(isset($genkey) && $key["key"] == $genkey ? 'class="table-success"':'').'>
            <th scope="row">'.$key["key"].'</th>
            <td>
              <a href="accounts.php?deletegenerateurl='.$key["key"].'" class="btn btn-danger mt-1"><i class="bi bi-trash"></i></a>
              <a target="_blank" href="https://wa.me/?text=https://'.$_SERVER["SERVER_NAME"].'/?reg_key='.$key["key"].'" class="btn btn-success mt-1"><i class="bi bi-whatsapp"></i></a>
              <a target="_blank" href="/?reg_key='.$key["key"].'" class="btn btn-primary mt-1"><i class="bi bi-box-arrow-up-right"></i></a>
            </td>
            </tr>');
        }
      }
      
      echo('</tbody>
    </table>

  <a href="?generateurl" type="submit" class="btn btn-primary float-right ml-2 mt-1">Key generieren</a>
  <a href="?generateadminurl" type="submit" class="btn btn-secondary float-right ml-2 mt-1">Admin key generieren</a>
  <a href="?deleteallgenerateurl" type="submit" class="btn btn-danger float-right ml-2 mt-1">Alle löschen</a>
  </div>

  </div>
  ');
    
    echo('<table class="table table-bordered shadow ">
      <thead class="thead-dark">
        <tr>
          <th>id</th>
          <th>Name</th>
          <th>Status</th>
          <th class="col-4">Shortcuts</th>
        </tr>
      </thead><tbody>');

    while($row = $stmt1->fetch()){
      $level = $row["level"];
      //if($row["active"] != 1) $level = 3;
      echo('
        <tr>
          <th scope="row">'.$row["id"].'</th>
          <td>'.$row["name"].'
          '.($row["admin"] ? '<span class="badge badge-info text-white"><i class="bi bi-patch-check"></i></span>' : '' ).'
          '.(!$row["active"] ? '<span class="badge badge-danger text-white"><i class="bi bi-ban"></i></span>' : '' ).'
          </td>
          <td class="font-weight-bold text-'.($level == 0 ? "success" : ($level == 1 ? "danger" : "warning")).'"><span class="badge badge-'.($level == 0 ? "success" : ($level == 1 ? "danger" : "warning")).' text-white"><i class="bi bi-activity"></i></span>
          '.($level == 0 ? "Aktiv" : ($level == 1 ? "Am lernen" : "Inaktiv")).'
          </td>
          <td class="col-4">
          <button type="button" class="btn btn-primary mt-1" data-toggle="modal" data-target="#modal_contact_'.$row["id"].'">
          <i class="bi bi-gear-fill"></i>
          </button>
          <a href="tel:'.$row["phone"].'" class="btn btn-primary mt-1"><i class="bi bi-telephone"></i></a>
          <a href="mailto:'.$row["email"].'" class="btn btn-primary mt-1"><i class="bi bi-envelope"></i></a>
          <a href="account.php?user='.$row["name"].'" class="btn btn-primary mt-1"><i class="bi bi-person"></i></a>
            
          </td>
        </tr>
      ');
      echo('<!-- Settings Modal -->
      <div class="modal fade" id="modal_contact_'.$row["id"].'" tabindex="-1" role="dialog" aria-labelledby="modal_contact_'.$row["id"].'Label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modal_contact_'.$row["id"].'">'.$row["name"].'</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <h3 class="font-weight-bold text-black-50"><a href="tel:'.$row["phone"].'" class="badge badge-primary"><i class="bi bi-telephone"></i></a> '.$row["phone"].'</h3>
              <h3 class="font-weight-bold text-black-50"><a href="mailto:'.$row["email"].'" class="badge badge-primary"><i class="bi bi-at"></i></a> '.$row["email"].'</h3>
              
              <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle font-weight-bold"
                        type="button" id="dropdownMenu1" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-activity"></i> Status
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenu1">
                  <a class="dropdown-item text-success font-weight-bold '.($level == 0 ? 'disabled':'" href="?userid='.$row["id"].'&new_lvl=0').'"> Aktiv  '.($level == 0 ? '<span class="font-italic"> (Derzeit)</span>':'').'</a>
                  <a class="dropdown-item text-danger font-weight-bold '.($level == 1 ? 'disabled':'" href="?userid='.$row["id"].'&new_lvl=1').'"> Am lernen  '.($level == 1 ? '<span class="font-italic"> (Derzeit)</span>':'').'</a>
                  <a class="dropdown-item text-warning font-weight-bold '.(($level != 0 && $level != 1 )? 'disabled':'" href="?userid='.$row["id"].'&new_lvl=2').'"> Inaktiv  '.(($level != 0 && $level != 1 ) ? '<span class="font-italic"> (Derzeit)</span>':'').'</a>
                </div>
              </div>
              
              <a href="?userid='.$row["id"].'&new_pw" class="btn btn-info font-weight-bold text-white" style="margin-top: 10px;">
                <i class="bi bi-key"></i> Neues Passwort generieren
              </a>
              <br>

              '.($row["admin"] ? '<a href="?userid='.$row["id"].'&new_admin=0" class="btn btn-danger font-weight-bold text-white '.($row["id"] == $_SESSION["id"] ? 'disabled':'').'" style="margin-top: 10px;">
                <i class="bi bi-patch-check"></i> Vom Admin-Status herabstufen
              </a>'
              :'<a href="?userid='.$row["id"].'&new_admin=1" class="btn btn-danger font-weight-bold text-white" style="margin-top: 10px;">
              <i class="bi bi-patch-check"></i> Zum Admin befördern
            </a>').'
            <br>
            <a href="?userid='.$row["id"].'&delete" class="btn btn-warning font-weight-bold text-black-50 '.($row["id"] == $_SESSION["id"] ? 'disabled':'').'" style="margin-top: 10px;">
              <i class="bi bi-trash"></i> Konto löschen
            </a>
            <br>
            '.($row["active"] ? '<a href="?userid='.$row["id"].'&active=false" class="btn btn-light font-weight-bold border border-danger text-danger '.($row["id"] == $_SESSION["id"] ? 'disabled':'').'" style="margin-top: 10px;">
              <i class="bi bi-ban"></i> Konto sperren
            </a>'
            :'<a href="?userid='.$row["id"].'&active=true" class="btn btn-light font-weight-bold border border-success text-success '.($row["id"] == $_SESSION["id"] ? 'disabled':'').'" style="margin-top: 10px;">
            <i class="bi bi-ban"></i> Konto entsperren
          </a>').'

            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Ok</button>
            </div>
          </div>
        </div>
      </div>');

    }
    echo('</tbody></table></div>');

  }else{
    echo('<div class="alert alert-danger" role="alert">
    <strong>Ooooops...</strong> Nutzer nicht gefunden
    </div>
    ');
  }

?>

<?php
echo GetBodyToEndCode();
}else header("Location: ./");


function generateRandomString($length = 10) {
  return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}
?>
