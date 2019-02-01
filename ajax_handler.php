<?php
require_once("models/config.php");

$API_URL    = SurveysConfig::$projects["REDCAP_PORTAL"]["URL"];
$API_TOKEN  = SurveysConfig::$projects["REDCAP_PORTAL"]["TOKEN"];

if(isset($_REQUEST["ajax"])){
  $project_name = $_REQUEST["project"] ?: null;
  $projects     = SurveysConfig::$projects;
  $API_TOKEN    = $projects[$project_name]["TOKEN"];
  $API_URL      = $projects[$project_name]["URL"];

  $data         = array();
  $record_id    = $project_name !== $_CFG->SESSION_NAME ? $loggedInUser->{$project_name} : $loggedInUser->id;
  $event_name   = $project_name !== $_CFG->SESSION_NAME ? null : $_SESSION[$_CFG->SESSION_NAME]["survey_context"]["event"];

  $survey_id    = isset($_REQUEST["sid"]) ? $_REQUEST["sid"] : null;
  
  //IF DOING A END OF SURVEY FINAL SUBMIT
  if(isset($_REQUEST["surveycomplete"])){
    $result     = RC::callApi(array(
        "hash"    => $_REQUEST["hash"], 
        "format"  => "csv"
      ), true, $custom_surveycomplete_API, $API_TOKEN);
  }

  //WRITE TO API
  //ADD OVERIDE PARAMETER 
  unset($_POST["project"]);
  foreach($_POST as $field_name => $value){
    if($value === 0){
      $value = 0;
    }else if($value == ""){
      $value = NULL;
    }

    $record_id  = $loggedInUser->id;
    $event_name = $_SESSION[$_CFG->SESSION_NAME]["survey_context"]["event"];

    $is_date    = preg_match('/^\d{2}-\d{2}\-\d{4}$/', $value);
    if($is_date){
      list($mm,$dd,$yyyy) = explode("-",$value);
      $value = "$yyyy-$mm-$dd";
    }

    $data[] = array(
      "record"            => $record_id,
      "field_name"        => $field_name,
      "value"             => $value
    );

    if($event_name){
      $data[0]["redcap_event_name"] = $event_name;
    }
    $result = RC::writeToApi($data, array("overwriteBehavior" => "overwite", "type" => "eav"), $API_URL, $API_TOKEN);
    // print_r($data);
    // print_r($result);
  }
}elseif(isset($_REQUEST["action"]) && $_REQUEST["action"] == "session_points"){
    $points_added = 0;
    if(!empty($_POST["link"])){
        if(!in_array($_POST["link"] , $_SESSION["points"])){
            $persist_pts = 0;
            if(isset($_POST["persist"]) && !in_array($_POST["link"] , $_SESSION["persist_points"])){
                array_push($_SESSION["persist_points"],$_POST["link"]);
                $persist_pts = $_POST["persist"];

                $data   = array();
                $data[] = array(
                    "record"            => $loggedInUser->id,
                    "field_name"        => "annual_persist_points",
                    "value"             => json_encode($_SESSION["persist_points"]),
                    "redcap_event_name" => (!empty($loggedInUser->user_event_arm) ? $loggedInUser->user_event_arm : REDCAP_PORTAL_EVENT)
                );
                $result = RC::writeToApi($data, array("overwriteBehavior" => "overwrite", "type" => "eav"), $API_URL, $API_TOKEN);
            }
            $points_added   = isset($_POST["value"]) ? $_POST["value"] : 0;
            $points_added += $persist_pts;
            array_push($_SESSION["points"],$_POST["link"]);
            $result = updateGlobalPersistPoints($loggedInUser->id, $points_added);
        }
    }
    echo json_encode(array("points_added" => $points_added));
}



exit;