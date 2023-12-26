<?php
require("./assets/default.php");
if(LoginCheck()){
ReloadSession();
echo GetHtmlToBodyCode();
echo GetNavbarCode(999);
require("./assets/mysql.php");

if(isset($_POST["save_profile"]) && LoginCheck($_POST["old_pw"])){

  if(isset($_POST["new_pw"]) && $_POST["new_pw"] != ""){
    $stmt2 = $mysql->prepare('UPDATE `accounts` SET `phone` = :new_phone, `email` = :new_email, `password` = :new_pw WHERE `name` = :username;');
    $new_pw_hash = password_hash($_POST["new_pw"], PASSWORD_DEFAULT);
    $stmt2->bindParam(":new_pw", $new_pw_hash);
  }else{
    $stmt2 = $mysql->prepare('UPDATE `accounts` SET `phone` = :new_phone, `email` = :new_email WHERE `name` = :username;');
  }
  $username = $_SESSION["username"];
  $stmt2->bindParam(":username", $username);
  $new_phone = $_POST["new_phone"];
  $stmt2->bindParam(":new_phone", $new_phone);
  $new_email = $_POST["new_email"];
  $stmt2->bindParam(":new_email", $new_email);
  $stmt2->execute();
  if($stmt2->rowCount() != 0){
    $result = true;
    $_SESSION["email"] = $new_email;
    $_SESSION["phone"] = $new_phone;
  }
  else $result = false;
}

session_start();

if(isset($_GET["user"])){

  $stmt1 = $mysql->prepare('SELECT * FROM `accounts` WHERE name = :username');
  $username = $_GET["user"];
  $stmt1->bindParam(":username", $username);
  $stmt1->execute();
  if($stmt1->rowCount() != 0){
  $row = $stmt1->fetch();

  $is_admin = $row["admin"];
  $phone = $row["phone"];
  $email = $row["email"];
  $level = $row["level"];
  if($row["active"] != 1) $level = 3;
  $start_date = $row["start_date"]; 
  $end_date = $row["end_date"]; 

  if($end_date == NULL) $today = time();
  else $today = strtotime($end_date);

  // Startdatum in Timestamp umwandeln
  $start_timestamp = strtotime($start_date);
  // Differenz in Sekunden berechnen  
  $diff = $today - $start_timestamp;
  // Differenz in Tage umrechnen
  $days_diff = round($diff / 86400);
  // Jahre, Monate und Tage berechnen
  $years = floor($days_diff / 365);
  $months = floor(($days_diff % 365) / 30); 
  $days = floor(($days_diff % 365) % 30);


  echo('
  <div class="card shadow bg-primar mx-auto" style="max-width: 40rem;">

    <h2 class="card-header bg-primary text-white">
        '.$username.'
    </h2>

    <div class="card-footer">
      '.($is_admin ? '<h3 class="font-weight-bolder text-info"><span class="badge badge-info text-white"><i class="bi bi-patch-check"></i></span>
            Admin
        </h3>' : '' ).'
        <h3 class="font-weight-bold text-'.($level == 0 ? "success" : ($level == 1 ? "danger" : "warning")).'"><span class="badge badge-'.($level == 0 ? "success" : ($level == 1 ? "danger" : "warning")).' text-white"><i class="bi bi-activity"></i></span>
          '.($level == 0 ? "Aktiv" : ($level == 1 ? "Am lernen" : "Inaktiv")).'
        </h3>
    </div>

    <div class="card-footer">
        <h3 class="font-weight-bold text-black-50"><a href="tel:'.$phone.'" class="badge badge-primary"><i class="bi bi-telephone"></i></a> '.$phone.'</h3>
        <h3 class="font-weight-bold text-black-50"><a href="mailto:'.(AdminCheck() ? $email : "?").'" class="badge badge-primary"><i class="bi bi-at"></i></a> 
          '.(AdminCheck() ? $email : "?").'
        </h3>
    </div>

    <div class="card-footer">
        <h3 class="font-weight-bold text-black-50"><span class="badge badge-primary"><i class="bi bi-calendar-check"></i></span> '.$start_date.'</h3>
        '.($end_date != NULL ? '<h3 class="font-weight-bold text-black-50"><span class="badge badge-primary"><i class="bi bi-calendar2-x"></i></span> '.$end_date.'</h3>' : '').'
        <h3 class="font-weight-bold text-black-50"><span class="badge badge-primary"><i class="bi bi-clock"></i></span> '.$years.' Jahr/e, '.$months.' Monat/e und '.$days.' Tag/e</h3>
    </div>

  </div>
  ');
  }else{
    echo('<div class="alert alert-danger" role="alert">
    <strong>Ooooops...</strong> Nutzer nicht gefunden
    </div>
    ');
  }

}else{
  echo('
  <form method="post" action="account.php" enctype="application/x-www-form-urlencoded">
  

  <div class="card shadow bg-primar mx-auto" style="max-width: 40rem;">
  
  
    <h2 class="card-header bg-primary text-white">
        '.$_SESSION["username"].'
    </h2>

    <div class="card-footer">
    
    '.(
      isset($result) ? 
      (
        $result ? '
        <div class="alert alert-success" role="alert">
          <strong>Erfoglreich!</strong> Alle Einstellungen erfolgreich übernommen!
        </div>' 
        : '<div class="alert alert-danger" role="alert">
        <strong>Ooooops...</strong> Unerwarteter Fehler aufgetreten
        </div>'
      )
      : ''
    ).'


        <div class="input-group" style="margin-bottom: 10px;">
          <div class="input-group-prepend">
            <button class="btn btn-primary font-weight-bold" type="button"><i class="bi bi-telephone"></i> Telefonnummer</button>
          </div>
          <input required type="text" class="form-control font-weight-bold" value="'.$_SESSION["phone"].'" name="new_phone">
        </div>
        
        <div class="input-group">
          <div class="input-group-prepend" style="margin-bottom: 10px;">
            <button class="btn btn-primary font-weight-bold" type="button"><i class="bi bi-at"></i> Email</button>
          </div>
          <input required type="email" class="form-control font-weight-bold" value="'.$_SESSION["email"].'" name="new_email">
        </div>

    </div>
    <div class="card-footer">

        <div class="input-group">
          <div class="input-group-prepend" style="margin-bottom: 10px;">
            <button class="btn btn-primary font-weight-bold" type="button"><i class="bi bi-key-fill"></i> Derzeitiges Passwort</button>
          </div>
          <input required type="password" class="form-control font-weight-bold" value="" name="old_pw">
        </div>

        <div class="collapse" id="newPW">
          <div class="input-group">
            <div class="input-group-prepend">
              <button class="btn btn-primary font-weight-bold" type="button"><i class="bi bi-key-fill"></i> Neues Passwort</button>
            </div>
            <input type="password" class="form-control font-weight-bold" name="new_pw">
          </div>
        </div>

        <a class="btn btn-primary font-weight-bold collapse show" id="newPW" data-toggle="collapse" href="#newPW" aria-expanded="false" aria-controls="collapseExample">
          Password ändern
        </a>
        <br>
      <button required type="submit" class="btn btn-primary float-right mt-1" name="save_profile" value="submit" name="btn_save">Speichern</button>
      <a href="?user='.$_SESSION["username"].'" class="card-link float-left mt-2">Öffentliches Profil anzeigen</a>
    </div>


  </div>
  </form>
  ');
}
?>

<?php
echo GetBodyToEndCode();
}else header("Location: ./");
?>
