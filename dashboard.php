<?php
require("./assets/default.php");
if(LoginCheck() && AdminCheck()){
    require("./assets/mysql.php");
    require('./assets/create_shift_preview.php');
    session_start();
    echo GetHtmlToBodyCode();
    echo GetNavbarCode(2);
    echo('<div class="container mx-auto p-0 shadow mb-4 p-3 rounded bg-light" style="max-width: 43rem;">');
    
    
    
    if(isset($_POST["btn_save"])){
        $stmt3 = $mysql->prepare('SELECT shifts FROM `shifts` WHERE id = :vid');
        $vid = $_POST["btn_save"];
        $stmt3->bindParam(":vid", $vid);
        $stmt3->execute();
        if($stmt3->rowCount() != 0){
            $row = $stmt3->fetch();
            $new["description"]=$_POST["db_description"];
            $new["priority"]=(isset($_POST["db_priority"]) ? "1" : "0");
            $new["active"]=(isset($_POST["db_active"]) ? true : false);

            $ALLjsondec = json_decode($row["shifts"]);
            $error = false;
            if($_POST["json_shiftcount"] == count((array)$ALLjsondec)){
                $new["shifts"] = new stdClass();
                $c = 0;
                foreach($ALLjsondec as $name => $data){
                    $c++;
                    $new["shifts"]->{$_POST["json_shift_".$c."_name"]} = new stdClass();
                    $new["shifts"]->{$_POST["json_shift_".$c."_name"]} = $data;
                    
                    $i = 0;
                    if(count((array)$data->events) == $_POST["json_event_".$c."_count"]){
                        //$new["shifts"]->{$_POST["json_shift_".$i."_name"]}->time_start = $_POST["json_shift_".$i."_name"];
                        foreach($new["shifts"]->{$_POST["json_shift_".$c."_name"]}->events as $eventname => $eventdata){
                            $i++;
                            $new["shifts"]->{$_POST["json_shift_".$c."_name"]}->events->{$eventname}->time_start = $_POST["json_event_".$c."_".$i."_time_start"];
                            $new["shifts"]->{$_POST["json_shift_".$c."_name"]}->events->{$eventname}->time_end = $_POST["json_event_".$c."_".$i."_time_end"];
                            if($_POST["json_event_".$c."_".$i."_alerts"] != ("" || null)){
                                $new["shifts"]->{$_POST["json_shift_".$c."_name"]}->events->{$eventname}->alerts = array();
                                $new["shifts"]->{$_POST["json_shift_".$c."_name"]}->events->{$eventname}->alerts[0] = $_POST["json_event_".$c."_".$i."_alerts"];
                            }
                        }
                    }
                    else $error = true;
                }
                $new["shifts"] = json_encode($new["shifts"]);
                //$stmt4 = $mysql->prepare('INSERT INTO `shifts` (`description`, `priority`, `active`, `shifts`) VALUES (:xdescription,:xpriority,:xactive,:xshifts)');
                $stmt4 = $mysql->prepare('UPDATE `shifts` SET `description` = :xdescription, `priority` = :xpriority, `active` = :xactive, `shifts` = :xshifts WHERE `id` = :vid;');
                $stmt4->bindParam(":vid", $vid);
                $stmt4->bindParam(":xdescription", $new["description"]);
                $stmt4->bindParam(":xpriority", $new["priority"]);
                $stmt4->bindParam(":xactive", $new["active"]);
                $stmt4->bindParam(":xshifts", $new["shifts"]);
                $stmt4->execute();
                if($stmt4->rowCount() != 0){
                    echo ('<div class="alert alert-primary" role="alert"><strong>Alle änderungen wurden erfolgreich übernommen!</strong></div>');
                    if($error) echo ('<div class="alert alert-warning" role="alert"><strong>Es wurde ein möglicher Datenverlust festgestellt, bitte überprüfen sie die vorgenommenen änderungen Manuel.</strong></div>');
                }else{
                    $error = true;
                }
            }
            else $error = true;
            
            if($error) echo ('<div class="alert alert-danger" role="alert"><strong>Oooops</strong> Es ist was schiefgelaufen, bitte dem Admin kontaktieren.Möglicherweise sind die übertragenen Daten nicht korrekt verarbeitet worden.</div>');

        }
    }else if(isset($_GET["activate"])){
            $stmt5 = $mysql->prepare('UPDATE `shifts` SET `active`= :vv WHERE date_start > :vdate');   
            $searchdate = date('Y-m-d', strtotime(date("Y-m-d"). ' - 2 days'));
            $stmt5->bindParam(":vdate", $searchdate);
            $vv = ($_GET["activate"] == "1" ? true : false);
            $stmt5->bindParam(":vv", $vv);
            
            if($stmt5->execute())
            echo ('<div class="alert alert-primary" role="alert"><strong>Alle Termine wurden erfolgreich aktualisiert!</strong></div>');
            else
            echo ('<div class="alert alert-danger" role="alert"><strong>Oooops</strong> Ein unbekanter Fehler ist aufgetreten!</div>');
        
    }else if(isset($_GET["deleteall"])){
        $stmt10 = $mysql->prepare('SELECT `name`,`id` FROM `shifts` WHERE date_end > :vdate ORDER BY date_start ASC');
        $searchdate = date('Y-m-d', strtotime(date("Y-m-d"). ' - 0 days'));
        $stmt10->bindParam(":vdate", $searchdate);
        if($stmt10->execute() && $stmt10->rowCount() != 0){
            while($row = $stmt10->fetch()){
                $stmt9 = $mysql->prepare('DELETE FROM `shifts` WHERE `id` = :vid');
                $vid = $row["id"];
                $stmt9->bindParam(":vid", $vid);
                if(!$stmt9->execute())
                echo ('<div class="alert alert-danger" role="alert"><strong>Oooops</strong> Ein unbekanter Fehler ist beim löschen von '.$row["name"].' ['.$row["id"].'] aufgetreten!</div>');
            }
        }
}else if(isset($_GET["delete"])){
        $stmt6 = $mysql->prepare('DELETE FROM `shifts` WHERE `id` = :vid');
        $vid = $_GET["delete"];
        $stmt6->bindParam(":vid", $vid);
        
        if($stmt6->execute())
        echo ('<div class="alert alert-primary" role="alert"><strong>Termin erfolgreich gelöscht!</strong></div>');
        else
        echo ('<div class="alert alert-danger" role="alert"><strong>Oooops</strong> Ein unbekanter Fehler ist aufgetreten!</div>');
    
}

    ?>

    <div class="card">
    <?php
    $stmt2 = $mysql->prepare('SELECT shifts FROM `shifts` WHERE date_end > :vdate ORDER BY date_start ASC');
    $searchdate = date('Y-m-d', strtotime(date("Y-m-d"). ' - 0 days'));
    $stmt2->bindParam(":vdate", $searchdate);
    $stmt2->execute();
    $ALLtotaleventsused =0;
    $ALLtotalevents = 0;
    if($stmt2->rowCount() != 0){
        while($row = $stmt2->fetch()){
            $ALLjsondec = json_decode($row["shifts"]);
            foreach($ALLjsondec as $data){
                if($data->inuse) $ALLtotaleventsused++;
            }
            $ALLtotalevents = $ALLtotalevents + count((array)$ALLjsondec);
        }
    }
    ?>
    <div class="card-header progress m-0 p-0 font-weight-bold" style="height: 30px; border-radius: 4px; border-bottom-left-radius: 0px;border-bottom-right-radius: 0px;">
        
    <?php if($ALLtotaleventsused != 0 && $ALLtotalevents != 0) { ?>
        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo ($ALLtotaleventsused * 100 / $ALLtotalevents); ?>%;" aria-valuenow="<?php echo ($ALLtotaleventsused * 100 / $ALLtotalevents); ?>" aria-valuemin="0" aria-valuemax="100"><?php echo ($ALLtotaleventsused); ?></div>
        <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo (($ALLtotalevents-$ALLtotaleventsused) * 100 / $ALLtotalevents); ?>%;" aria-valuenow="<?php echo (($ALLtotalevents-$ALLtotaleventsused) * 100 / $ALLtotalevents); ?>" aria-valuemin="0" aria-valuemax="100"><?php echo ($ALLtotalevents-$ALLtotaleventsused); ?></div>
    <?php }else{?>
        <div class="progress-bar bg-danger" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"><i>0</i></div>
    <?php }?>
    </div>

        <h5 class="card-header">Termine</h5>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <a href="./newshift.php" class="btn btn-primary mr-1 mt-1 mb-1"><i class="bi bi-plus-lg"></i> Neu</a>
                <div class="btn-group" role="group" aria-label="Basic example">
                    <?php
                        /*$color1 = "secondary";
                        $color2 = "dark";
                        $color = $color2;
                        foreach(getNextSaturdays() as $saturday){
                            if($color == $color1) $color = $color2; else $color = $color1;
                            $sunday = date('Y-m-d', strtotime($saturday->format("Y-m-d"). ' + 1 days'));

                            $day = $saturday->format('d');
                            if($saturday->format('m') != date('m', strtotime($sunday))){
                                $day .= "/".$saturday->format('m')."-".date('d', strtotime($sunday))."/".date('m', strtotime($sunday));
                            }
                            else $day .= "-".date('d', strtotime($sunday))."/".$saturday->format('m');
                            echo('<a type="button" class="btn btn-'.$color.'" href="/newshift.php?date='.$saturday->format("Y-m-d").'">'.$day.'</a>');
                        }*/
                    ?>
                </div>

                <a href="?activate=0" class="btn btn-danger mr-1 mt-1 mb-1"><i class="bi bi-toggle2-off mr-1"></i> Alle deaktivieren</a>
                <a href="?activate=1" class="btn btn-info mr-1 mt-1 mb-1"><i class="bi bi-toggle2-on mr-1"></i> Alle Aktivieren</a>
                <a href="/?live" class="btn btn-secondary mr-1 mt-1 mb-1"><i class="bi bi-record-circle-fill mr-1"></i> Live anzeigen</a>
                <a href="?deleteall" class="btn btn-dark mr-1 mt-1 mb-1"><i class="bi bi-trash"></i> Alle löschen</a>
            </li>
       
            <?php
                //$stmt = $mysql->prepare('SELECT * FROM `shifts` WHERE `active` = 1 ORDER BY date_start ASC');
                //$stmt = $mysql->prepare('SELECT * FROM `shifts` ORDER BY date_start < :vdate,IF(date_start >= :vdate, 1, -1) * DATEDIFF(date_start, :vdate)');
                $stmt = $mysql->prepare('SELECT * FROM `shifts` WHERE date_end > :vdate ORDER BY date_start ASC');
                
                $searchdate = date('Y-m-d', strtotime(date("Y-m-d"). ' - 0 days'));
                $stmt->bindParam(":vdate", $searchdate);
                $stmt->execute();
                if($stmt->rowCount() != 0){
                    while($row = $stmt->fetch()){
                        
                        $date = date("d",strtotime($row["date_start"]));
                        if(date("d",strtotime($row["date_end"])) != date("d",strtotime($row["date_start"]))){
                            if(date("m",strtotime($row["date_end"])) != date("m",strtotime($row["date_start"]))){
                                $date .= " ".getMonthName($row["date_start"])." - ".date("d",strtotime($row["date_end"]))." ".getMonthName($row["date_end"]);
                            }else{
                                $date .= "-".date("d",strtotime($row["date_end"]))." ".getMonthName($row["date_end"]);
                            }
                        }else{
                            $date .= " ".getMonthName($row["date_start"]);
                        }

                        $jsondec = json_decode($row["shifts"]);
                        
                        $totaleventsused = 0;
                        

                        foreach($jsondec as $data){
                            if($data->inuse) $totaleventsused++;
                        }
                        
                        echo('
                        <!-- Modal -->
                        <div class="modal fade" id="shiftmodal_'.$row["id"].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">'.$row["name"].' 
                                '.($row["active"] 
                                ?'<span class="badge badge-success text-white-50" style="font-size: 15px; letter-spacing: .5px;"><i class="bi bi-check-circle-fill"></i> Aktiv</span>'
                                :'<span class="badge badge-warning text-black-50" style="font-size: 15px; letter-spacing: .5px;"><i class="bi bi-x-circle-fill"></i> Inaktiv</span>').'
                                '.($row["priority"] == 1 ? '<span class="badge badge-danger" style="font-size: 15px; letter-spacing: .5px;">Feiertag/e</span>' : '').'
                                <span class="badge badge-'.(count((array)$jsondec) == $totaleventsused ? "success text-white-50" : "warning text-black-50").'" style="font-size: 15px; letter-spacing: .5px;"><i class="bi bi-person-check"></i> '.$totaleventsused.'/'.count((array)$jsondec).'</span>
                                '.($row["date_end"] == date('Y-m-d') || $row["date_start"] == date('Y-m-d') 
                                        ? '<span class="badge badge-white rounded" style="border: 2px solid indianred; color:indianred; opacity: 0.8; font-size: 14px; letter-spacing: .5px;">
                                        <i class="bi bi-record-circle-fill" style="color: indianred;"></i>
                                        Live
                                        </span>' : '').'
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <div class="modal-body">
                                '.(!$row["active"]  ? '<div class="alert alert-warning text-black-50" role="alert">
                                    <strong>Hinweis:</strong> Solange der Termin nicht aktiviert ist, ist er für Nutzer nicht sichtbar!
                                        </div>':'').'

                              <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    <a class="nav-item nav-link active" id="modal_nav_edit_tab_id'.$row["id"].'" data-toggle="tab" href="#modal_nav_edit_id'.$row["id"].'" role="tab" aria-controls="nav-home" aria-selected="true">Bearbeiten</a>
                                    <a class="nav-item nav-link" id="modal_nav_preview_tab_id'.$row["id"].'" data-toggle="tab" href="#modal_nav_preview_id'.$row["id"].'" role="tab" aria-controls="nav-profile" aria-selected="false">Vorschau</a>
                                </div>
                              </nav>
                            
                            <div class="tab-content border border-top-0 rounded-bottom" id="nav-tabContent">
                              
                              <div class="tab-pane fade show active" id="modal_nav_edit_id'.$row["id"].'" role="tabpanel" aria-labelledby="modal_nav_edit_tab_id'.$row["id"].'">
                        ');
                              
                        
                        echo('
                        <form method="post" action="dashboard.php" enctype="application/x-www-form-urlencoded" id="modal_form_'.$row["id"].'">
                            <ul class="list-group">
                                <li class="list-group-item">


                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Datum</span>
                                        </div>
                                        <input value="'.$row["date_start"].($row["date_start"] != $row["date_end"] ? ' & '.$row["date_start"] : '').'" type="text" class="form-control" readonly>
                                    </div>

                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Beschreibung</span>
                                        </div>
                                        <input name="db_description" value="'.$row["description"].'" type="text" class="form-control">
                                    </div>
                                    
                                    <div class="form-group mb-2">
                                        <div class="custom-control custom-switch">
                                            <input name="db_active" type="checkbox" class="custom-control-input" id="modal_input_active_id'.$row["id"].'" value="'.($row["active"] ? 'true" checked' : 'false"').'>
                                            <label class="custom-control-label" for="modal_input_active_id'.$row["id"].'">Aktivieren</label>
                                        </div>
                                    </div>

                                    <div class="form-group mb-2">
                                        <div class="custom-control custom-switch">
                                            <input name="db_priority" type="checkbox" class="custom-control-input" id="modal_input_priority_id'.$row["id"].'" value="'.($row["priority"] == 1 ? 'true" checked' : 'false"').'>
                                            <label class="custom-control-label" for="modal_input_priority_id'.$row["id"].'">Feiertag/e</label>
                                        </div>
                                    </div>



                                <!--</li>
                            </ul>-->

                            ');

$c = 0;


foreach($jsondec as $shifts=>$data){
    $c++;
    
    echo('

    <input type="hidden" required name="json_shift_'.$c.'_name" value="'.$shifts.'">
    <div class="card m-0 mb-3">
        <div class="card-header bg-'.($data->inuse ? 'secondary' : 'danger').'" data-toggle="collapse" style="cursor:pointer;"
            href="#modal_single_shift_id'.$row["id"].'_position'.$c.'" role="button"
            aria-expanded="false"
            aria-controls="modal_single_shift_id'.$row["id"].'_position'.$c.'">
            <h4 class="m-0 font-weight-bold text-light text-monospace" style="display:inline">'.$shifts.' <span class="badge badge-dark">'.$data->user.'</span></h4>

            <span class="float-right font-weight-bold text-light mt-1">
            <i class="bi bi-diamond-fill"></i>
            </span>
        </div>
        <div class="collapse" id="modal_single_shift_id'.$row["id"].'_position'.$c.'">
        <div class="card-body p-3 pb-0 mb-0 text-monospace">
        

            '.($data->inuse ? '<div class="alert alert-info" role="alert">
                <strong>Reserviert von: </strong> '.$data->user.'
            </div>' : '').'
    ');
    $i = 0;
    foreach($data->events as $eventname=>$event){
            $i++;
            echo('
            <input type="hidden" required name="json_event_'.$c.'_'.$i.'_name" value="'.$eventname.'">
            <h5 class="card-title w-100"><span class="badge badge-'.($data->inuse ? 'secondary' : 'danger').' w-100">Event '.$i.'</span></h5><!--s1-e1-->
            <div class="input-group mb-2">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                </div>
                <div class="input-group-prepend">
                    <span class="input-group-text">'.getWeekdayName($eventname).'</span>
                </div>
                <input value="'.$eventname.'" type="date" class="form-control" readonly />
            </div>
            <div class="input-group mb-2">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="bi bi-clock"></i></span>
                </div>
                <div class="input-group-prepend">
                    <span class="input-group-text">Von</span>
                </div>
                <input required name="json_event_'.$c.'_'.$i.'_time_start" value="'.$event->time_start.'" type="time" class="form-control" />
            </div>
            <div class="input-group mb-2">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="bi bi-clock"></i></span>
                </div>
                <div class="input-group-prepend">
                    <span class="input-group-text">Bis</span>
                </div>
                <input required name="json_event_'.$c.'_'.$i.'_time_end" value="'.$event->time_end.'" type="time" class="form-control" />
            </div>
            <div class="input-group mb-2"><!--Set alerts - s1-e1-->
                <div class="input-group-prepend">
                    <span class="input-group-text">Info</span>
                </div>
                <input name="json_event_'.$c.'_'.$i.'_alerts" value="'.(isset($event->alerts[0]) ? $event->alerts[0] : '').'" type="text" class="form-control" />
            </div>
            '); 
            }
        echo('<input required name="json_event_'.$c.'_count" value="'.$i.'" type="hidden">');
    ?>
            

        </div>
        </div>
    </div>


    <?php } 
    
    //echo('<input required name="json_eventcount" value="'.$i.'" type="hidden">');
    echo('<input required name="json_shiftcount" value="'.$c.'" type="hidden">');
    ?>

  <div class="w-100"></div>
  <button required type="submit" class="btn btn-success float-right mt-1" value="<?php echo($row["id"]); ?>" name="btn_save">Speichern</button>
  <a href="?delete=<?php echo($row["id"]); ?>" class="btn btn-danger float-right text-light mt-1 mr-2">Löschen</a>


  <!-- Force next columns to break to new line 
  <div class="w-100"></div>
  <div class="col-11 col-sm-3 bg-secondary">.col-6 .col-sm-3</div>
  <div class="col-11 col-sm-3 bg-info">.col-6 .col-sm-3</div>-->

<?php
                              
                        echo('
                        </li>
                            </ul>
                              </form>
                              </div>
                              
                              
                              <div class="tab-pane fade p-3" id="modal_nav_preview_id'.$row["id"].'" role="tabpanel" aria-labelledby="modal_nav_preview_tab_id'.$row["id"].'">
                              '.
                                GetCardShiftPreview($row["name"], str_replace(array("\r", "\n"), '', $row["shifts"]),
                                $row["priority"],$row["description"], $row["date_start"], $row["date_end"], -999)     
                               .'
                              </div>
                            </div>


                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
                              </div>
                            </div>
                          </div>
                        </div>');//Modal END

                        echo('
                        <li class="list-group-item" data-toggle="modal" data-target="#shiftmodal_'.$row["id"].'" style="cursor:pointer;">
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: '.($totaleventsused * 100 / count((array)$jsondec)).'%;" aria-valuenow="'.($totaleventsused * 100 / count((array)$jsondec)).'" aria-valuemin="0" aria-valuemax="100"></div>
                                <div class="progress-bar bg-danger" role="progressbar" style="width: '.((count((array)$jsondec)-$totaleventsused) * 100 / count((array)$jsondec)).'%;" aria-valuenow="'.((count((array)$jsondec)-$totaleventsused) * 100 / count((array)$jsondec)).'" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <h6 class="card-title mt-2 mb-0 ">
                                '.$row["name"].'
                                '.($row["active"] 
                                    ?'<span class="badge badge-success text-white-50 pb-1 pt-1" style="font-size: 14px; letter-spacing: .5px;"><i class="bi bi-check-circle-fill"></i> Aktiv</span>'
                                    :'<span class="badge badge-warning text-black-50 pb-1 pt-1" style="font-size: 14px; letter-spacing: .5px;"><i class="bi bi-x-circle-fill"></i> Inaktiv</span>').'
                                    '.($row["priority"] == 1 ? '<span class="badge badge-danger pb-1 pt-1" style="font-size: 14px; letter-spacing: .5px;">Feiertag/e</span>' : '').'
                                    '.($row["date_end"] == date('Y-m-d') || $row["date_start"] == date('Y-m-d') 
                                        ? '<span class="badge badge-white rounded" style="border: 2px solid indianred; color:indianred; opacity: 0.8; font-size: 14px; letter-spacing: .5px;">
                                        <i class="bi bi-record-circle-fill" style="color: indianred;"></i>
                                        Live
                                        </span>' : '').'
                            </h6>
                            <i class="bi bi-calendar "></i>
                                <span class="font-weight-normal">
                                    '.$date.'
                                </span>
                            <br>
                            <i class="bi bi-person-check"></i>
                                <span class="font-weight-normal">
                                    '.$totaleventsused.'/'.count((array)$jsondec).'
                                </span>
                            ');
                            
                            foreach($jsondec as $shifts=>$data){
                                echo('<fieldset class="font-weight-'.($data->inuse ? 'normal text-success' : 'bold text-danger font-italic').'"><i class="bi bi-info-circle"></i>');
                                echo('<span>
                                        '.$shifts.'
                                    </span>');
                                if($data->inuse){
                                    echo('<br><i class="bi bi-arrow-return-right ml-4"></i>');
                                    echo('<span class="font-weight-normal">
                                        '.$data->user.'
                                    </span>');
                                }
                                echo('</fieldset>');
                            }
                            
                            echo ('</li>');
                } } ?>
        </ul>
    </div>

    <div class="card mt-3">
    <div class="card-header bg-secondary">
        <h5 class="text-white" style="display:inline">Abgelaufene Termine</h5>
    </div>

        <ul class="list-group list-group-flush">
            <li class="list-group-item">

                <?php 
                $stmt8 = $mysql->prepare('SELECT DATE_FORMAT(date_start, \'%Y-%m\') AS month, COUNT(*) AS num_entries FROM shifts WHERE date_end < :vdate GROUP BY month ORDER BY month DESC;');
                $searchdate = date('Y-m-d', strtotime(date("Y-m-d"). ' - 0 days'));
                $stmt8->bindParam(":vdate", $searchdate);
                $stmt8->execute();
                $av_months_data = array();
                if($stmt8->rowCount() != 0){
                    $id = -1;
                    while($row = $stmt8->fetch()){
                        $id++;
                        array_push($av_months_data, array("name" => $row["month"], "count" => $row["num_entries"], "id" => $id));
                    }
                    
                    $selected = 0;
                    $sortdate = date('Y-m');
                    if(isset(array_values($av_months_data)[0]["name"])){
                        $sortdate = array_values($av_months_data)[0]["name"];
                    }
                    if(isset($_GET["sort_old"])){
                        $searchin = (int)$_GET["sort_old"];
                        if(isset(array_values($av_months_data)[$searchin]["name"])){
                            $sortdate = array_values($av_months_data)[$searchin]["name"];
                            $selected = $searchin;
                        }
                    }
                    
                    ?>
                <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?php
                     echo getMonthName(array_values($av_months_data)[$selected]["name"])." ".date("Y", strtotime(array_values($av_months_data)[$selected]["name"])) .' ('.array_values($av_months_data)[$selected]["count"].')'; 
                     ?>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                <?php
                
                foreach($av_months_data as $key=>$data){
                    if($data["id"] != $selected)
                    echo('<a class="dropdown-item" href="?sort_old='.$data["id"].'">'.getMonthName($data["name"])." ".date("Y", strtotime($data["name"])).' ('.$data["count"].')</a>');
                }
                ?>

                </div>
                </div>

                <?php
                }
                ?>


            </li>
       
            <?php
                $stmt7 = $mysql->prepare('SELECT * FROM `shifts` WHERE date_end < :vdate 
                AND DATE_FORMAT(date_start, \'%Y-%m\') =  :sortdate ORDER BY date_start ASC');
                
                $searchdate = date('Y-m-d', strtotime(date("Y-m-d"). ' - 0 days'));

                $stmt7->bindParam(":vdate", $searchdate);
                $stmt7->bindParam(":sortdate", $sortdate);
                $stmt7->execute();
                if($stmt7->rowCount() != 0){
                    while($row = $stmt7->fetch()){
                        
                        $date = date("d",strtotime($row["date_start"]));
                        if(date("d",strtotime($row["date_end"])) != date("d",strtotime($row["date_start"]))){
                            if(date("m",strtotime($row["date_end"])) != date("m",strtotime($row["date_start"]))){
                                $date .= " ".getMonthName($row["date_start"])." - ".date("d",strtotime($row["date_end"]))." ".getMonthName($row["date_end"]);
                            }else{
                                $date .= "-".date("d",strtotime($row["date_end"]))." ".getMonthName($row["date_end"]);
                            }
                        }else{
                            $date .= " ".getMonthName($row["date_start"]);
                        }

                        $jsondec = json_decode($row["shifts"]);
                        
                        $totaleventsused = 0;
                        foreach($jsondec as $data){
                            if($data->inuse) $totaleventsused++;
                        }
                        

                        echo('
                        <li class="list-group-item">
                           
                            <h6 class="card-title mt-2 mb-0 ">
                                '.$row["name"].'
                                '.($totaleventsused == count((array)$jsondec)
                                    ? ''
                                    :'<span class="badge badge-warning text-black-50 pb-1 pt-1" style="font-size: 14px; letter-spacing: .5px;"><i class="bi bi-x-circle-fill"></i> Nicht komplett</span>').'
                                '.($row["priority"] == 1 ? '<span class="badge badge-danger pb-1 pt-1" style="font-size: 14px; letter-spacing: .5px;">Feiertag/e</span>' : '').'
                            </h6>
                            <i class="bi bi-calendar "></i>
                                <span class="font-weight-normal">
                                    '.$date.'
                                </span>
                            <br>
                            <i class="bi bi-person-check"></i>
                                <span class="font-weight-normal">
                                    '.$totaleventsused.'/'.count((array)$jsondec).'
                                </span>
                            ');
                            
                            foreach($jsondec as $shifts=>$data){
                                echo('<fieldset class="font-weight-'.($data->inuse ? 'normal text-success' : 'bold text-danger font-italic').'"><i class="bi bi-info-circle"></i>');
                                echo('<span>
                                        '.$shifts.'
                                    </span>');
                                if($data->inuse){
                                    echo('<br><i class="bi bi-arrow-return-right ml-4"></i>');
                                    echo('<span class="font-weight-normal">
                                        '.$data->user.'
                                    </span>');
                                }
                                echo('</fieldset>');
                            }
                            
                            echo ('</li>');
                } } ?>
        </ul>

    </div>
</div>

<?php
    echo GetBodyToEndCode();
}

?>