

<?php
require("./assets/default.php");
if(LoginCheck()){
echo GetHtmlToBodyCode();
echo GetNavbarCode(1);
?>






<?php

require("./assets/mysql.php");
session_start();

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

//$jsss = '{ "Turno 1":{ "inuse":false, "user":"", "events":{ "2023-12-25":{ "time_start":"06:00", "time_end":"12:00", "hours":"6", "alerts":[ "Hmm" ], "rate":"53" } } }, "Turno 2":{ "inuse":false, "user":"", "events":{ "2023-12-25":{ "time_start":"12:30", "time_end":"20:30", "hours":"8", "alerts":[ "Laaaaange" ], "rate":"53" } } } }';



if(isset($_GET["shifttoactive_json"], $_GET["shifttoactive_shiftname"], $_GET["shifttoactive_dbid"])){
  require('./assets/change_shift_status.php');
  UpdateShiftInDB($_GET["shifttoactive_dbid"], GetNewShiftJsonWithNewUser($_GET["shifttoactive_json"], $_GET["shifttoactive_shiftname"], $_SESSION["username"]));
}
else if(isset($_GET["shifttoinactive_json"], $_GET["shifttoinactive_shiftname"], $_GET["shifttoinactive_dbid"])){
  require('./assets/change_shift_status.php');
  UpdateShiftInDB($_GET["shifttoinactive_dbid"], GetNewShiftJsonWithRemovedUser($_GET["shifttoinactive_json"], $_GET["shifttoinactive_shiftname"]));
}

//$stmt = $mysql->prepare('SELECT * FROM `shifts` WHERE `active` = 1 ORDER BY date_start ASC');
$stmt = $mysql->prepare('SELECT * FROM `shifts` WHERE date_start > :vdate AND `active` = 1 ORDER BY date_start ASC');
$searchdate = date('Y-m-d', strtotime(date("Y-m-d"). ' - 2 days'));
$stmt->bindParam(":vdate", $searchdate);
$stmt->execute();
  if($stmt->rowCount() != 0){
    require('./assets/create_shift_preview.php');
    while($row = $stmt->fetch()){
      echo GetCardShiftPreview($row["name"], str_replace(array("\r", "\n"), '', $row["shifts"]), $row["priority"],$row["description"], $row["date_start"], $row["date_end"], $row["id"]);
      //echo file_get_contents(str_replace(array("\r", "\n"), '', 'https://operadores.techgrief.de/assets/create_shift_preview_old.php?name='.$row["name"].'&json_shifts='.$row["shifts"].'&priority='.$row["priority"].'&date_start='.$row["date_start"].'&date_end='.$row["date_end"].($row["description"] !== null ? ('&description='.$row["description"]) : '')));
    }
  }else{
    echo('<div class="alert alert-warning" role="alert">
    Im Moment sind keine Turnos verfügbar!
  </div>
  ');
  }

?>



<!--
<div class="bootstrap-iso">
  <div class="container-fluid">
   <div class="row">
    <div class="col-md-6 col-sm-6 col-xs-12">
 
     <!-- Form code begins
     <form method="post">
       <div class="form-group"> <!-- Date input 
         <input class="form-control" id="date" name="date" placeholder="MM/DD/YYY" type="text"/>
       </div>
      </form>
      <!-- Form code ends 
 
     </div>
   </div>    
  </div>
 </div>


 
 <script>
  var date_input;
  $(document).ready(function(){
    date_input=$('input[name="date"]'); //our date input has the name "date"
    var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
    var options={
      format: 'dd/mm/yyyy',
      container: container,
      todayHighlight: true,
      language: "de-DE",
      autoclose: true,
      daysOfWeekHighlighted: [0,6],
      //multidate: true,
    };
    date_input.datepicker(options);
    date_input.datepicker("daysOfWeekHighlighted", 6);
    console.log(date_input.datepicker("getDate"));
  })
</script>

<button onclick='$(document).ready(function(){console.log(date_input.datepicker("getDate"));})'>Submit</button>

-->





<?php
echo GetBodyToEndCode();
}else header("Location: ./");
?>


