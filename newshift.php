
<?php
require("./assets/default.php");
if(LoginCheck() && AdminCheck()){
    require("./assets/mysql.php");
    require('./assets/create_shift_preview.php');
    session_start();
    echo GetHtmlToBodyCode();
    echo GetNavbarCode(2);

    echo('<div class="container mx-auto p-0 shadow mb-4 p-3 rounded bg-light" style="max-width: 43rem;">');
    
if(!isset($_POST["submit1"]) && !isset($_POST["submit2"])){
if(isset($_GET["notify"]) && $_GET["notify"] == "success"){
    echo('<div class="alert alert-success" role="alert">
                <strong>Erfolgreich hinzugefügt!</strong>
            </div> ');
}
    ?>
    <span class="badge badge-info p-2">1.</span>
    <a href="./dashboard.php"><span class="badge badge-danger p-2" style="cursor:pointer;">Zurück</span></a>
<div class="card mb-2 mt-2">
    <h4 class="card-header text-white bg-primary" style="cursor:pointer;" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
        Presets
    </h4>
    <div class="collapse <?php if(!isset($_GET["date"])) echo "show"; ?>" id="collapseExample">
    <div class="card-body">
        <h4 class="card-title">Wochenende</h4>

        <?php
        $color1 = "secondary";
        $color2 = "dark";
        $color = $color1;
        $firstmonth = 0;
        foreach(getNextSaturdays(10) as $saturday){
            if($firstmonth == 0)$firstmonth = $saturday->format("m");
            if($firstmonth != $saturday->format("m")){
                $firstmonth = $saturday->format("m");
                if($color == $color1) $color = $color2; else $color = $color1;
                echo "<br>";
            }
            $sunday = date('Y-m-d', strtotime($saturday->format("Y-m-d"). ' + 1 days'));
            $day = $saturday->format('d');
            if($saturday->format('m') != date('m', strtotime($sunday))){
                $day .= "/".$saturday->format('m')."-".date('d', strtotime($sunday))."/".date('m', strtotime($sunday));
            }
            else $day .= "-".date('d', strtotime($sunday))."/".$saturday->format('m');
                echo('<a class="btn btn-'.$color.' mr-1 mb-2" href="/newshift.php?date='.$saturday->format("Y-m-d").'">'.$day.'</a>');
        }
        ?>
        <h4 class="card-title mt-2">Feiertage</h4>
        <?php
        $color1 = "secondary";
        $color2 = "dark";
        $color = $color1;
        $json = json_decode(file_get_contents("./assets/holidays.json"));
        $lastyear = 0;
        foreach($json as $x => $var){
            $datum = "01-06";
            $year = date("Y");
            if(date("m-d") > $var->day) {
                $year++; 
            }
            if($lastyear == 0) $lastyear = $year;
            if($lastyear != $year) {echo "<br>"; $lastyear = $year;}
            if($year != date("Y")) $color = $color1; else $color = $color2;
            echo('<a class="btn btn-'.$color.' mr-1 mb-2" href="/newshift.php?date='.$year.'-'.$var->day.'&name='.$x.'&holiday">'.$x.'<br>'.$var->day.'-'.$year.'</a>');
        }
        ?>

    </div>
    </div>
</div>

<form method="POST">
<fieldset <?php echo isset($_POST["submit1"]) ? "disabled class='text-black-50'" : "";?>>

    <div class="form-group">
        <label for="f1">Name <?php if(isset($_GET["holiday"])) echo '<i class="bi bi-check2-circle text-success"></i> <i class="text-success">Automatisch aktualisiert</i>';?></label>
        <div class="input-group mb-2 mr-sm-2">
            <input required class="form-control" name="name" type="text" value="<?php echo isset($_POST["name"]) ? $_POST["name"] : (!isset($_GET["name"]) ? "Wochenende" : $_GET["name"]);?>" id="f1" placeholder="Wochenende">
        </div>
    </div>

    <div class="form-group">
        <label for="f2">Beschreibung</label>
        <div class="input-group mb-2 mr-sm-2">
            <input class="form-control" name="description" value="<?php echo isset($_POST["description"]) ? $_POST["description"] : "";?>" type="text" id="f2">
        </div>
    </div>

    <div class="form-group">
        <label for="f3">Start Datum <?php if(isset($_GET["date"])) echo '<i class="bi bi-calendar-check text-success"></i> <i class="text-success">Automatisch aktualisiert</i>';?></label>
        <div class="input-group mb-2 mr-sm-2">
            <input required class="form-control" name="date_start" type="date" pattern="\d{4}-\d{2}-\d{2}" value="<?php echo isset($_GET["date"]) ? $_GET["date"] : (isset($_POST["date_start"]) ? $_POST["date_start"] : date('Y-m-d'));?>" id="f3" placeholder="1969-07-20">
        </div>
    </div>

    <div class="form-group">
        <label for="f3">Laufzeit <?php if(isset($_GET["holiday"])) echo '<i class="bi bi-check2-circle text-success"></i> <i class="text-success">Automatisch aktualisiert</i>';?></label>
        <div class="custom-control custom-radio">
            <input type="radio" id="customRadio1" name="time" value="1" class="custom-control-input" <?php echo ((isset($_POST["time"]) && $_POST["time"] == "1") ? "checked" : "");?> <?php echo (isset($_GET["holiday"]) ? "checked" : "");?>>
            <label class="custom-control-label" for="customRadio1">1 Tag</label>
        </div>
        <div class="custom-control custom-radio">
            <input type="radio" id="customRadio2" name="time" value="2" class="custom-control-input" <?php echo (isset($_POST["time"]) ? ($_POST["time"] == "2" ? "checked" : "") : (!isset($_GET["holiday"]) ? "checked" : ''));?>>
            <label class="custom-control-label" for="customRadio2">2 Tage (Wochenende)</label>
        </div>
    </div>

    <div class="form-group">
        <label for="f3">Schichten pro Tag</label>
        <div class="custom-control custom-radio">
            <input type="radio" id="customRadio3" class="custom-control-input" checked>
            <label class="custom-control-label" for="customRadio3">2</label>
        </div>
    </div>

    <div class="form-group">
        <label>Extras <?php if(isset($_GET["holiday"])) echo '<i class="bi bi-check2-circle text-success"></i> <i class="text-success">Automatisch aktualisiert</i>';?></label>
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="customSwitch1" name="highpriority" value="true" <?php echo isset($_POST["highpriority"]) ? "checked" : (isset($_GET["holiday"]) ? "checked" : '');?>>
            <label class="custom-control-label" for="customSwitch1">Feiertag/e</label>
        </div>
    </div>

    <div class="form-group">
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="customSwitch2" name="active" value="true" <?php echo ((isset($_POST["submit1"]) && !isset($_POST["active"])) ? "" : "checked");?>>
            <label class="custom-control-label" for="customSwitch2">Aktivieren</label>
        </div>
    </div>

    <button required type="submit" class="btn btn-primary" value="true" name="submit1">Weiter</button>

</fieldset>
</form>

<?php
} else if(isset($_POST["submit1"]) && !isset($_POST["submit2"])){

$v_name = $_POST["name"];
$v_description = $_POST["description"];
$v_date_start = $_POST["date_start"];
$v_date_end = ($_POST["time"] == 2 ? (date('Y-m-d', strtotime($v_date_start. ' + 1 days'))) : date('Y-m-d', strtotime($v_date_start)));
$v_time = $_POST["time"];
$v_priority = (isset($_POST["highpriority"]) ? 1 : 0);
$v_active = (isset($_POST["active"]) ? true : false);

?>
<span class="badge badge-info p-2">2.</span>
<a href="./newshift.php"><span class="badge badge-danger p-2" style="cursor:pointer;">Zurück</span></a>

<form method="POST">


<div class="row mt-2">
  <div class="col ">

    <div class="card h-100 w-100">
        <div class="card-header bg-primary"><h4 class="m-0 font-weight-bold text-light text-monospace">Schicht 1</h4></div>
        <div class="card-body p-3 pb-0 mb-0 text-monospace">

            <div class="input-group mb-2">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="ig1">Name</span>
                </div>
                <input value="Turno 1" aria-describedby="ig1" name="shift1_name" type="text" class="form-control" placeholder="Turno 1" required>
            </div>
            

            <h5 class="card-title w-100"><span class="badge badge-primary w-100">Event 1</span></h5><!--s1-e1-->
            <div class="input-group mb-2"><!--Set Date/name - s1-e1-->
                <div class="input-group-prepend">
                    <span class="input-group-text" id="ig3"><i class="bi bi-calendar"></i></span>
                </div>
                <div class="input-group-prepend">
                    <span class="input-group-text" id="ig3">Datum</span>
                </div>
                <input value="<?php echo $v_date_start; ?>" pattern="\d{4}-\d{2}-\d{2}" aria-describedby="ig3" name="shift1_event1_date" type="date" class="form-control" required readonly>
            </div>
            <div class="input-group mb-2"><!--Set Start Time - s1-e1-->
                <div class="input-group-prepend">
                    <span class="input-group-text" id="ig4"><i class="bi bi-clock"></i></span>
                </div>
                <div class="input-group-prepend">
                    <span class="input-group-text" id="ig4">Von</span>
                </div>
                <input value="<?php echo($v_time == 1 ? "06:00": "12:30");?>" aria-describedby="ig4" name="shift1_event1_time_start" type="time" class="form-control" required>
            </div>
            <div class="input-group mb-2"><!--Set End Time - s1-e1-->
                <div class="input-group-prepend">
                    <span class="input-group-text" id="ig5"><i class="bi bi-clock"></i></span>
                </div>
                <div class="input-group-prepend">
                    <span class="input-group-text" id="ig5">Bis</span>
                </div>
                <input value="<?php echo($v_time == 1 ? "12:00": "17:30");?>" aria-describedby="ig5" name="shift1_event1_time_end" type="time" class="form-control" required>
            </div>
            <div class="input-group mb-2"><!--Set alerts - s1-e1-->
                <div class="input-group-prepend">
                    <span class="input-group-text" id="ig6">Info</span>
                </div>
                <input value="" aria-describedby="ig6" name="shift1_event1_alert" type="text" class="form-control">
            </div>
            
            <?php if($v_time == 2){ ?>

                <h5 class="card-title w-100"><span class="badge badge-primary w-100">Event 2</span></h5><!--s1-e2-->
            <div class="input-group mb-2"><!--Set Date/name - s1-e2-->
                <div class="input-group-prepend">
                    <span class="input-group-text" id="ig7"><i class="bi bi-calendar"></i></span>
                </div>
                <div class="input-group-prepend">
                    <span class="input-group-text" id="ig7">Datum</span>
                </div>
                <input value="<?php echo $v_date_end; ?>" pattern="\d{4}-\d{2}-\d{2}" aria-describedby="ig7" name="shift1_event2_date" type="date" class="form-control" required readonly>
            </div>
            <div class="input-group mb-2"><!--Set Start Time - s1-e2-->
                <div class="input-group-prepend">
                    <span class="input-group-text" id="ig8"><i class="bi bi-clock"></i></span>
                </div>
                <div class="input-group-prepend">
                    <span class="input-group-text" id="ig8">Von</span>
                </div>
                <input value="17:00" aria-describedby="ig8" name="shift1_event2_time_start" type="time" class="form-control" required>
            </div>
            <div class="input-group mb-2"><!--Set End Time - s1-e2-->
                <div class="input-group-prepend">
                    <span class="input-group-text" id="ig9"><i class="bi bi-clock"></i></span>
                </div>
                <div class="input-group-prepend">
                    <span class="input-group-text" id="ig9">Bis</span>
                </div>
                <input value="21:30" aria-describedby="ig9" name="shift1_event2_time_end" type="time" class="form-control" required>
            </div>
            <div class="input-group mb-2"><!--Set alerts - s1-e2-->
                <div class="input-group-prepend">
                    <span class="input-group-text" id="ig10">Info</span>
                </div>
                <input value="" aria-describedby="ig10" name="shift1_event2_alert" type="text" class="form-control">
            </div>


            <?php } ?>


        </div>
    </div>

  </div>
</div>

  <div class="row mt-2">
  <div class="col">
    <div class="card h-100 w-100">
        <div class="card-header bg-primary"><h4 class="m-0 font-weight-bold text-light text-monospace">Schicht 2</h4></div>
        <div class="card-body p-3 pb-0 mb-0 text-monospace">

            <div class="input-group mb-2">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="ig11">Name</span>
                </div>
                <input value="Turno 2" aria-describedby="ig11" name="shift2_name" type="text" class="form-control" placeholder="Turno 2" required>
            </div>
            

            <h5 class="card-title w-100"><span class="badge badge-primary w-100">Event 1</span></h5><!--s2-e1-->
            <div class="input-group mb-2"><!--Set Date/name - s2-e1-->
                <div class="input-group-prepend">
                    <span class="input-group-text" id="ig12"><i class="bi bi-calendar"></i></span>
                </div>
                <div class="input-group-prepend">
                    <span class="input-group-text" id="ig12">Datum</span>
                </div>
                <input value="<?php echo $v_date_start; ?>" pattern="\d{4}-\d{2}-\d{2}" aria-describedby="ig12" name="shift2_event1_date" type="date" class="form-control" required readonly>
            </div>
            <div class="input-group mb-2"><!--Set Start Time - s2-e1-->
                <div class="input-group-prepend">
                    <span class="input-group-text" id="ig13"><i class="bi bi-clock"></i></span>
                </div>
                <div class="input-group-prepend">
                    <span class="input-group-text" id="ig13">Von</span>
                </div>
                <input value="<?php echo($v_time == 1 ? "12:30": "17:30");?>" aria-describedby="ig13" name="shift2_event1_time_start" type="time" class="form-control" required>
            </div>
            <div class="input-group mb-2"><!--Set End Time - s2-e1-->
                <div class="input-group-prepend">
                    <span class="input-group-text" id="ig14"><i class="bi bi-clock"></i></span>
                </div>
                <div class="input-group-prepend">
                    <span class="input-group-text" id="ig14">Bis</span>
                </div>
                <input value="<?php echo($v_time == 1 ? "20:30": "20:30");?>" aria-describedby="ig14" name="shift2_event1_time_end" type="time" class="form-control" required>
            </div>
            <div class="input-group mb-2"><!--Set alerts - s2-e1-->
                <div class="input-group-prepend">
                    <span class="input-group-text" id="ig15">Info</span>
                </div>
                <input value="" aria-describedby="ig15" name="shift2_event1_alert" type="text" class="form-control">
            </div>
            
            <?php if($v_time == 2){ ?>

                <h5 class="card-title w-100"><span class="badge badge-primary w-100">Event 2</span></h5><!--s2-e2-->
            <div class="input-group mb-2"><!--Set Date/name - s2-e2-->
                <div class="input-group-prepend">
                    <span class="input-group-text" id="ig16"><i class="bi bi-calendar"></i></span>
                </div>
                <div class="input-group-prepend">
                    <span class="input-group-text" id="ig16">Datum</span>
                </div>
                <input value="<?php echo $v_date_end; ?>" pattern="\d{4}-\d{2}-\d{2}" aria-describedby="ig16" name="shift2_event2_date" type="date" class="form-control" required readonly>
            </div>
            <div class="input-group mb-2"><!--Set Start Time - s2-e2-->
                <div class="input-group-prepend">
                    <span class="input-group-text" id="ig17"><i class="bi bi-clock"></i></span>
                </div>
                <div class="input-group-prepend">
                    <span class="input-group-text" id="ig17">Von</span>
                </div>
                <input value="06:00" aria-describedby="ig17" name="shift2_event2_time_start" type="time" class="form-control" required>
            </div>
            <div class="input-group mb-2"><!--Set End Time - s2-e2-->
                <div class="input-group-prepend">
                    <span class="input-group-text" id="ig18"><i class="bi bi-clock"></i></span>
                </div>
                <div class="input-group-prepend">
                    <span class="input-group-text" id="ig18">Bis</span>
                </div>
                <input value="10:30" aria-describedby="ig18" name="shift2_event2_time_end" type="time" class="form-control" required>
            </div>
            <div class="input-group mb-2"><!--Set alerts - s2-e2-->
                <div class="input-group-prepend">
                    <span class="input-group-text" id="ig19">Info</span>
                </div>
                <input value="" aria-describedby="ig19" name="shift2_event2_alert" type="text" class="form-control">
            </div>


            <?php } ?>


        </div>
    </div>
  </div>

  <!-- Force next columns to break to new line 
  <div class="w-100"></div>
  <div class="col-11 col-sm-3 bg-secondary">.col-6 .col-sm-3</div>
  <div class="col-11 col-sm-3 bg-info">.col-6 .col-sm-3</div>-->
</div>

<div class="row mt-2">
  <div class="col">
    <button required type="submit" class="btn btn-success float-right" value="true" name="submit2">Speichern</button>
  </div>
  </div>

<h6 class="m-2 text-black-50 text-monospace font-italic" style="display:none;">Card subtitle
        Name: <?php echo $v_name; ?> <input type="hidden" name="v_name" value="<?php echo $v_name; ?>"><br>
        Description: <?php echo $v_description; ?> <input type="hidden" name="v_description" value="<?php echo $v_description; ?>"><br>
        Date_start: <?php echo $v_date_start; ?> <input type="hidden" name="v_date_start" value="<?php echo $v_date_start; ?>"><br>
        Date_end: <?php echo $v_date_end; ?> <input type="hidden" name="v_date_end" value="<?php echo $v_date_end; ?>"><br>
        Priority: <?php echo $v_priority; ?> <input type="hidden" name="v_priority" value="<?php echo $v_priority; ?>"><br>
        Active: <?php echo $v_active; ?> <input type="hidden" name="v_active" value="<?php echo $v_active; ?>"><br>
        Time/s: <?php echo $v_time; ?> <input type="hidden" name="v_time" value="<?php echo $v_time; ?>"><br>
</h6>


</form>

<?php
}else if(isset($_POST["submit2"])){

    $mainTree = new stdClass();
    $mainTree->{$_POST["shift1_name"]} = new stdClass();
    $mainTree->{$_POST["shift1_name"]}->inuse = false;
    $mainTree->{$_POST["shift1_name"]}->user = "";
    $mainTree->{$_POST["shift1_name"]}->events = new stdClass();
    $mainTree->{$_POST["shift1_name"]}->events->{$_POST["shift1_event1_date"]} = new stdClass();
    $mainTree->{$_POST["shift1_name"]}->events->{$_POST["shift1_event1_date"]}->time_start = $_POST["shift1_event1_time_start"];
    $mainTree->{$_POST["shift1_name"]}->events->{$_POST["shift1_event1_date"]}->time_end = $_POST["shift1_event1_time_end"];
    $mainTree->{$_POST["shift1_name"]}->events->{$_POST["shift1_event1_date"]}->hours = (new DateTime($_POST["shift1_event1_time_start"]))->diff(new DateTime($_POST["shift1_event1_time_end"]))->h;
    $mainTree->{$_POST["shift1_name"]}->events->{$_POST["shift1_event1_date"]}->alerts = ($_POST["shift1_event1_alert"] != ("" || null) ? array( $_POST["shift1_event1_alert"]) : array());
    $mainTree->{$_POST["shift1_name"]}->events->{$_POST["shift1_event1_date"]}->rate = '0';
    if(isset($_POST["shift1_event2_date"])){  
        $mainTree->{$_POST["shift1_name"]}->events->{$_POST["shift1_event2_date"]} = new stdClass();
        $mainTree->{$_POST["shift1_name"]}->events->{$_POST["shift1_event2_date"]}->time_start = $_POST["shift1_event2_time_start"];
        $mainTree->{$_POST["shift1_name"]}->events->{$_POST["shift1_event2_date"]}->time_end = $_POST["shift1_event2_time_end"];
        $mainTree->{$_POST["shift1_name"]}->events->{$_POST["shift1_event2_date"]}->hours = (new DateTime($_POST["shift1_event2_time_start"]))->diff(new DateTime($_POST["shift1_event2_time_end"]))->h;
        $mainTree->{$_POST["shift1_name"]}->events->{$_POST["shift1_event2_date"]}->alerts = ($_POST["shift1_event2_alert"] != ("" || null) ? array( $_POST["shift1_event2_alert"]) : array());
        $mainTree->{$_POST["shift1_name"]}->events->{$_POST["shift1_event2_date"]}->rate = '0';
    }
    $mainTree->{$_POST["shift2_name"]} = new stdClass();
    $mainTree->{$_POST["shift2_name"]}->inuse = false;
    $mainTree->{$_POST["shift2_name"]}->user = "";
    $mainTree->{$_POST["shift2_name"]}->events = new stdClass();
    $mainTree->{$_POST["shift2_name"]}->events->{$_POST["shift2_event1_date"]} = new stdClass();
    $mainTree->{$_POST["shift2_name"]}->events->{$_POST["shift2_event1_date"]}->time_start = $_POST["shift2_event1_time_start"];
    $mainTree->{$_POST["shift2_name"]}->events->{$_POST["shift2_event1_date"]}->time_end = $_POST["shift2_event1_time_end"];
    $mainTree->{$_POST["shift2_name"]}->events->{$_POST["shift2_event1_date"]}->hours = (new DateTime($_POST["shift2_event1_time_start"]))->diff(new DateTime($_POST["shift2_event1_time_end"]))->h;
    $mainTree->{$_POST["shift2_name"]}->events->{$_POST["shift2_event1_date"]}->alerts = ($_POST["shift2_event1_alert"] != ("" || null) ? array( $_POST["shift2_event1_alert"]) : array());
    $mainTree->{$_POST["shift2_name"]}->events->{$_POST["shift2_event1_date"]}->rate = '0';
    if(isset($_POST["shift2_event2_date"])){  
        $mainTree->{$_POST["shift2_name"]}->events->{$_POST["shift2_event2_date"]} = new stdClass();
        $mainTree->{$_POST["shift2_name"]}->events->{$_POST["shift2_event2_date"]}->time_start = $_POST["shift2_event2_time_start"];
        $mainTree->{$_POST["shift2_name"]}->events->{$_POST["shift2_event2_date"]}->time_end = $_POST["shift2_event2_time_end"];
        $mainTree->{$_POST["shift2_name"]}->events->{$_POST["shift2_event2_date"]}->hours = (new DateTime($_POST["shift2_event2_time_start"]))->diff(new DateTime($_POST["shift2_event2_time_end"]))->h;
        $mainTree->{$_POST["shift2_name"]}->events->{$_POST["shift2_event2_date"]}->alerts = ($_POST["shift2_event2_alert"] != ("" || null) ? array( $_POST["shift2_event2_alert"]) : array());
        $mainTree->{$_POST["shift2_name"]}->events->{$_POST["shift2_event2_date"]}->rate = '0';
    }
    
    $mainTreeJSON = json_encode($mainTree);
    ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    require("./assets/mysql.php");
    $stmt = $mysql->prepare('INSERT INTO `shifts`(`name`, `description`, `shifts`, `priority`, `date_start`, `date_end`, `active`)
    VALUES (:vname,:vdescription,:vshifts,:vpriority,:vdate_start,:vdate_end,:vactive)');

    $vname = $_POST["v_name"];
    $stmt->bindParam(":vname", $vname);
    
    $vdescription = (isset($_POST["v_description"]) ? $_POST["v_description"] : null);
    $stmt->bindParam(":vdescription", $vdescription);

    $stmt->bindParam(":vshifts", $mainTreeJSON);

    $vpriority = $_POST["v_priority"];
    $stmt->bindParam(":vpriority", $vpriority);

    $vdate_start = $_POST["v_date_start"];
    $stmt->bindParam(":vdate_start", $vdate_start);

    $vdate_end = $_POST["v_date_end"];
    $stmt->bindParam(":vdate_end", $vdate_end);

    $vactive = ($_POST["v_active"] == "1" ? true : false);
    $stmt->bindParam(":vactive", $vactive);

    $stmt->execute();
    if($stmt->rowCount() != 0){
        header('Location: ./newshift.php?notify=success');
    }else{
        echo('<div class="alert alert-danger" role="alert">
                <strong>Fehler aufgetreten!</strong> Bitte dem Fehler melden oder <a href="?notify=error" class="alert-link">nochmals versuchen</a>!
            </div> ');
    }

    /*$myObj = new stdClass();
$myObj->name = new stdClass();
$myObj->name->surname = "john";
$myObj->city = "New York";

$myJSON = json_encode($myObj);
echo $myJSON;

echo "----";

$newObj = json_decode($myJSON);
print_r($newObj);*/
}
?>

</div>
<?php

    echo GetBodyToEndCode();
}

?>