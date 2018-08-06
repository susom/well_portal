<?php 
require_once("models/config.php"); 

$API_URL      = SurveysConfig::$projects["REDCAP_PORTAL"]["URL"];
$API_TOKEN    = SurveysConfig::$projects["REDCAP_PORTAL"]["TOKEN"];
$extra_params = array(
   "content"  => "record"
  ,"type"     => "flat"
  ,"fields"   => array("well_long_score_json","id")
);

$records      = RC::callApi($extra_params, true, $API_URL, $API_TOKEN); 
$records      = array_filter($records,function($record){
  return $record["well_long_score_json"] != "" && $record["well_long_score_json"] != "[]";
});

$data     = array();
foreach($records as $record){
  $old_json = json_decode($record["well_long_score_json"],1);
  $changed   = false;
  
  if(isset($old_json["Financial Security"])){
    $old_json["Finances"] = $old_json["Financial Security"]; 
    unset($old_json["Financial Security"]);
    $changed = true;
  }

  if(isset($old_json["Lifestyle Behaviors"])){
    $old_json["Lifestyle and Daily Practices"] = $old_json["Lifestyle Behaviors"];
    unset($old_json["Lifestyle Behaviors"]);
    $changed = true;
  }

  if($changed){
    $data[]                 = array(    
       "record"             => $record["id"]
      ,"field_name"         => "well_long_score_json"
      ,"value"              => json_encode($old_json)
      ,"redcap_event_name"  => $record["redcap_event_name"]
    );
  }
}

if(!empty($data)){
  $updated = RC::writeToApi($data, array("overwriteBehavior" => "overwite", "type" => "eav"), $API_URL, $API_TOKEN); 
  print_rr($updated);
}