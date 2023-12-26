<?php



function GetNewShiftJsonWithNewUser($json, $shiftname, $user){
    $json_shifts_decoded = json_decode($json);
    if(isset($json_shifts_decoded->$shiftname)){
        $json_shifts_decoded->$shiftname->user = $user;
        $json_shifts_decoded->$shiftname->inuse = true;
        return json_encode($json_shifts_decoded);
    }
    return false;
}

function GetNewShiftJsonWithRemovedUser($json, $shiftname){
    $json_shifts_decoded = json_decode($json);
    if(isset($json_shifts_decoded->$shiftname)){
        $json_shifts_decoded->$shiftname->user = "";
        $json_shifts_decoded->$shiftname->inuse = false;
        return json_encode($json_shifts_decoded);
    }
    return false;
}

function UpdateShiftInDB($id, $newjson){
    require("mysql.php");
    $stmt2 = $mysql->prepare('UPDATE `shifts` SET `shifts` = :njson WHERE `shifts`.`id` = :nid;');
    $stmt2->bindParam(":njson", $newjson);
    $stmt2->bindParam(":nid", $id);
    $stmt2->execute();
    if($stmt2->rowCount() != 0){
        
    }
}

?>