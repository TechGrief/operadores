<?php
//css isnt provided. Requieres bootstrap 4.2
//json example: { "Turno 1":{ "events":{ "2023-12-26":{ "time_start":"06:00", "time_end":"12:00", "hours":"6", "alerts":[ "Hmm" ], "rate":"53" } } }, "Turno 2":{ "events":{ "2023-12-26":{ "time_start":"12:30", "time_end":"20:30", "hours":"8", "alerts":[ "Laaaaange" ], "rate":"53" } } } }
function GetCardShiftPreview($name = "ERROR@NAME", $json_shifts = "{}", $prioritycode = 0, $description = null, $date_start = "2999-12-31", $date_end = "2999-12-31", $dbid = -1){

    $date_start = date_create($date_start);
    $date_end = date_create($date_end);
    $code = "";
    $json_shifts_decoded = json_decode($json_shifts);
    $priority = 'primary';
    if($prioritycode == 1) $priority = "danger";
    else if($prioritycode == 2) $priority = "secondary";

    //add name
    $code .= '<div class="card mb-3"> <span class="badge badge-'.$priority.' card-header pt-2"> <h4>'.$name;

    //add timespan
    //adds days+months for duration validation
    $timespan = date_format($date_start,"d");


    if(date_format($date_start,"m") == date_format($date_end,"m")){
        if(date_format($date_start,"d") != date_format($date_end,"d")){
            $timespan .= "-".date_format($date_end,"d");
        }
        $timespan .= " ".getMonthName($date_start);
    }else{
        $timespan .= " ".getMonthName($date_start)." - ".date_format($date_end,"d")." ".getMonthName($date_end);
    }
    $code .= '<span class="badge badge-light text-primary ml-2">'.$timespan.'</span></h4></span>';

    //add description if asked
    if($description != null)
    $code .= '<div class="card-body shadow"> <div class="alert alert-info" role="alert"> '.$description.' </div> <div class="card-'.($dbid == -999 ? "deck" : "group").'">';
    else
    $code .= '<div class="card-body shadow"> <div class="card-'.($dbid == -999 ? "deck" : "group").'">';

    //add shifts from shifts-json
    $code .= '';
    foreach($json_shifts_decoded as $shiftname => $events){
        $code .= '<div class="card"> <div class="card-body pb-3"> <h4 class="card-title">'.$shiftname.'</h4>';
        //$code .= '<a href="#!" class="btn btn-primary">Reservieren</a>';//Button for -> order event
        $active = false;
        foreach($events as $event){//events for name (Turno 1)
            if($events->inuse) $active = $events->user;
            if(($event instanceof stdClass)){

                if($dbid != -1 && $dbid != -999){
                    if($active != false){//card selected
                        session_start();
                        if($_SESSION["username"] == $active){//selected by current user
                            $code .= '<a href=\''.('?shifttoinactive_dbid='.$dbid.'&shifttoinactive_json='.str_replace(array("\r", "\n"), '', $json_shifts)."&shifttoinactive_shiftname=".$shiftname).'\' class="btn btn-danger">Abbrechen</a>';
                        }else{//selected by another person
                            $code .= '<a href="account.php?user='.$active.'" class="btn btn-secondary">'.$active.'</a>';
                        }
                    }else{//not selected
                        $code .= '<a href=\''.('?shifttoactive_dbid='.$dbid.'&shifttoactive_json='.str_replace(array("\r", "\n"), '', $json_shifts)."&shifttoactive_shiftname=".$shiftname).'\' class="btn btn-primary">Reservieren</a>';
                    }
                    //$code .= '<a href=\''.($active != false ? "#" : ('?shifttoactive_dbid='.$dbid.'&shifttoactive_json='.str_replace(array("\r", "\n"), '', $json_shifts)."&shifttoactive_shiftname=".$shiftname)).'\' class="btn btn-'.($active != false ? "secondary" : "primary").'">'.($active != false ? $active : "Reservieren").'</a>';
                }elseif($dbid == -999){
                    if($active != false){//card selected
                        $code .= '<a href="account.php?user='.$active.'" class="btn btn-success" target="_blank">'.$active.'</a>';
                    }else{
                        $code .= '<a href="#" class="btn btn-danger">Nicht ausgewählt</a>';
                    }
                }else {
                    $code .= '<a href="#" class="btn btn-secondary"><s>Vorschau</s></a>';
                }
                
                $code .= '</div> <ul class="list-group list-group-flush">';
                foreach($event as $eventname => $data){ //information for event
                    $eventname;//Date
                    $data->time_start;
                    $data->time_end;
                    $data->hours;
                    $data->rate;

                    $code .= '<li class="list-group-item">'.getWeekdayName($eventname).', '.date_format(date_create($eventname),"d") .', '.$data->time_start.'-'.$data->time_end.' <span class="badge badge-secondary">'.$data->hours.'H</span>';


                    foreach($data->alerts as $alert){
                        $code .= '<div class="alert alert-warning mt-1 mb-0" role="alert">'.$alert.'</div>';
                    }
                }
                $code .= '</ul></div>';
            }
        }
        
    }

    //add last end...
    $code .= '</div></div></div>';

    
    
    return $code;

}

function getMonthName($plaindate){
    if ($plaindate instanceof DateTime) {
        $plaindate = $plaindate->format('Y-m-d');
    }
    if(setlocale(LC_ALL, 'de_DE') == "de_DE"){
        return strftime("%B", strtotime($plaindate));
    }else {
    	return date_format(date_create($plaindate),"F");
    }
}

function getWeekdayName($plaindate){
    if ($plaindate instanceof DateTime) {
        $plaindate = $plaindate->format('Y-m-d');
    }
    if(setlocale(LC_ALL, 'de_DE') == "de_DE"){
        return strftime("%A", strtotime($plaindate));
    }else {
    	return date_format(date_create($plaindate),"l");
    }
}

function getNextSaturdays($count = 5)
{
    $current = new \DateTime('saturday this week');

    $results = [];
    for ($i = 0; $i < $count; $i++) {
        $results[] = clone $current->modify('next saturday');
    }

    return $results;
}

?>