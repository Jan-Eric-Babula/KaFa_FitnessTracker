<?php
include "_dbconfig.php";

function sql_get_connection():mysqli {
    $con = mysqli_connect($db_ip, $db_user, $db_pw, $db_dbname);

    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    return $con;
}

function sql_close_connection(mysqli $con){
    mysqli_close($con);
}

/* SQL Timeframes
 - 0 / All
 - 1 / Heute
 - 2 / Gestern
 - 3 / 7 Tage
 - 4 / 30 Tage
*/
function sql_get_timeframe_string(int $timeframe): string
{
    switch($timeframe){
        case 0:
            return "1=1";
        case 1:
            return "DATE(timestamp) = DATE(NOW())";
        case 2:
            return "DATE(timestamp) = DATE_ADD(DATE(now()), INTERVAL -1 DAY)";
        case 3:
            return "DATE(timestamp) >= DATE_ADD(DATE(now()), INTERVAL -7 DAY)";
        case 4:
            return "DATE(timestamp) >= DATE_ADD(DATE(now()), INTERVAL -30 DAY)";
    }
    return "1=0";
}

function sql_get_reference_search_string(string $input):string {
    $ret = "";

    foreach (explode(" ",$input) as $term){
        $ret = $ret . "('%" . $term . "%'),";
    }

    return substr($ret, 0, -1);
}

function get_daily_percentage(int $calories, $timeframe):float{

    $calories = $calories *100.0;

    if($timeframe==1 ||$timeframe==2)
        return round(($calories / 2000.0));
    if($timeframe==3)
        return round(($calories / (2000.0*7.0)));
    if($timeframe==4)
        return round(($calories / (2000.0*30.0)));
    return 0;
}

function sql_query_calories_time(int $timeframe):array{
    $con = sql_get_connection();
    $qry = " SELECT timestamp, calories, description FROM vw_calories WHERE ". sql_get_timeframe_string($timeframe) ." ORDER BY timestamp ASC ";
    $result = mysqli_query($con, $qry);

    $ret = array();
    foreach($result as $row){
        $ret[] = array("timestamp" => $row["timestamp"], "calories" => $row["calories"], "description" => $row["description"]);
    }
    sql_close_connection($con);
    return $ret;
}

function sql_query_calories_compact(int $timeframe):array{
    $con = sql_get_connection();
    $qry = " SELECT COUNT(timestamp) AS amount, MIN(calories) AS calories_single, SUM(calories) as calories, description  FROM vw_calories WHERE "
        . sql_get_timeframe_string($timeframe)
        . " GROUP BY description ORDER BY SUM(calories) DESC, COUNT(timestamp) DESC ";
    $result = mysqli_query($con, $qry);

    $ret = array();
    foreach($result as $row){
        $ret[] = array("amount" => $row["amount"], "calories" => $row["calories"], "description" => $row["description"], "calories_single" => $row["calories_single"]);
    }
    sql_close_connection($con);
    return $ret;
}

function sql_query_weight(int $timeframe):array{
    $con = sql_get_connection();
    $qry = " SELECT timestamp, weight, diff, dur FROM vw_weight WHERE "
        . sql_get_timeframe_string($timeframe)
        . " ORDER BY timestamp ASC ";
    $result = mysqli_query($con, $qry);

    $ret = array();
    foreach($result as $row){
        $ret[] = array("timestamp" => $row["timestamp"], "weight" => $row["weight"], "diff" => $row["diff"], "dur" => $row["dur"]);
    }
    sql_close_connection($con);
    return $ret;
}

function sql_query_weight_last():array{
    $con = sql_get_connection();
    $qry = " SELECT timestamp, weight FROM vw_weight ORDER BY timestamp DESC LIMIT 1 ";
    $result = mysqli_query($con, $qry);
    $row = mysqli_fetch_assoc($result);
    $ret = array("timestamp" => $row["timestamp"], "weight" => $row["weight"]);
    sql_close_connection($con);
    return $ret;
}

function sql_query_reference_all(string $order = null):array{
    if (!$order){
        $order = "description ASC";
    }
    $con = sql_get_connection();
    $qry = " SELECT id, calories, description FROM reference_list WHERE deleted = FALSE "
        . " ORDER BY " . $order;
    $result = mysqli_query($con, $qry);
    $ret = array();
    foreach($result as $row){
        $ret[] = array("id" => $row["id"], "calories" => $row["calories"], "description" => $row["description"]);
    }
    sql_close_connection($con);
    return $ret;
}

function sql_query_reference_one(int $id):array{
    $con = sql_get_connection();
    $qry = " SELECT id, calories, description, description_clean FROM reference_list WHERE deleted = FALSE AND id=".$id;
    $result = mysqli_query($con, $qry);
    $row = mysqli_fetch_assoc($result);
    $ret = array("id" => $row["id"], "calories" => $row["calories"], "description" => $row["description"], "description_clean" => $row["description_clean"]);
    sql_close_connection($con);
    return $ret;
}

function sql_query_reference_search(string $searchterm):array{
    $con = sql_get_connection();
    $qry = " SELECT id, calories, description FROM "
        . " (SELECT id, calories,description, description_clean LIKE v.`@` AS test FROM reference_list rl "
        . " CROSS JOIN (VALUES ('@'), "
        . sql_get_reference_search_string($searchterm)
        . " ) AS v WHERE rl.deleted = FALSE) AS rslt "
        . " GROUP BY id, calories, description HAVING SUM(test)>0 ORDER BY SUM(test) DESC, description ASC ";
    $result = mysqli_query($con, $qry);

    $ret = array();
    foreach($result as $row){
        $ret[] = array("id" => $row["id"], "calories" => $row["calories"], "description" => $row["description"]);
    }
    sql_close_connection($con);
    return $ret;
}

function sql_delete_reference(int $id):bool{
    $con = sql_get_connection();
    $qry = "UPDATE reference_list SET deleted = TRUE WHERE id=".$id;
    $ret = mysqli_query($con, $qry);
    sql_close_connection($con);
    return $ret;
}

function sql_insert_reference(int $cal, string $name, string $clean) {
    $con = sql_get_connection();
    $qry = "INSERT INTO reference_list(calories, description, description_clean) VALUES ("
        .$cal.",'"
        .htmlspecialchars($name, ENT_QUOTES)."','"
        .$clean
        ."')";
    $suc = mysqli_query($con, $qry);
    if($suc){
        $ret = mysqli_insert_id($con);
    }else{
        $ret = null;
    }
    sql_close_connection($con);
    return $ret;
}

function sql_insert_calories_reference(int $ref){
    $con = sql_get_connection();
    $qry = " INSERT INTO calories(reference) VALUES ( " . $ref . " ) ";
    $suc = mysqli_query($con, $qry);
    if($suc){
        $ret = true;
    }else{
        $ret = null;
    }
    sql_close_connection($con);
    return $ret;
}

function sql_insert_calories_custom(int $cal, string $name){
    $con = sql_get_connection();
    $qry = " INSERT INTO calories(custom_calories, custom_description) VALUES ( "
        . $cal . " , '" .$name . "' ) ";
    $suc = mysqli_query($con, $qry);
    if($suc){
        $ret = true;
    }else{
        $ret = null;
    }
    sql_close_connection($con);
    return $ret;
}

function sql_insert_weight(int $weight){
    $con = sql_get_connection();
    $qry = " INSERT INTO weight(weight) VALUES ( " . $weight . " ) ";
    $suc = mysqli_query($con, $qry);
    if($suc){
        $ret = true;
    }else{
        $ret = null;
    }
    sql_close_connection($con);
    return $ret;
}

function sql_update_reference(int $id, int $cal, string $name, string $clean):bool{
    $con = sql_get_connection();
    $qry = " UPDATE reference_list SET "
        ." calories=".$cal.", "
        ." description='".htmlspecialchars($name, ENT_QUOTES)."', "
        ." description_clean='".$clean."' "
        ." WHERE id=".$id;
    $ret = mysqli_query($con, $qry);
    sql_close_connection($con);
    return $ret;
}
