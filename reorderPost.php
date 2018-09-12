<?php
require_once("models/config.php"); 
//initial ordering
$radar_domains = array( 
  "0" => lang("RESOURCE_CREATIVITY"),
  "1" => lang("RESOURCE_LIFESTYLE"),
  "2" => lang("RESOURCE_SOCIAL"),
  "3" => lang("RESOURCE_STRESS"),
  "4" => lang("RESOURCE_EMOTIONS"),
  "5" => lang("RESOURCE_SELF"),
  "6" => lang("RESOURCE_PHYSICAL"),
  "7" => lang("RESOURCE_PURPOSE"),
  "8" => lang("RESOURCE_FINANCIAL"),
  "9" => lang("RESOURCE_RELIGION")
);
$redcap_variables = array(
  "0" => "domainorder_ec",
  "1" => "domainorder_lb",
  "2" => "domainorder_sc",
  "3" => "domainorder_sr",
  "4" => "domainorder_ee",
  "5" => "domainorder_ss",
  "6" => "domainorder_ph",
  "7" => "domainorder_pm",
  "8" => "domainorder_fs",
  "9" => "domainorder_rs"
);

if($_POST["domains"]){
	$doms          = json_decode($_POST["domains"],1);
	$API_URL       = SurveysConfig::$projects["REDCAP_PORTAL"]["URL"];
	$API_TOKEN     = SurveysConfig::$projects["REDCAP_PORTAL"]["TOKEN"];

  $half          = count($doms)/2 - 1;

	foreach($doms as $key => $val){
		$in             = array_search($val, $radar_domains);
    $user_event_arm = empty($loggedInUser->user_event_arm) ? REDCAP_PORTAL_EVENT : $loggedInUser->user_event_arm;

    $value  = $key > $half ? 13 - $key : $key + 1;

		$data[] = array(
      "redcap_event_name" => $user_event_arm,
      "record"            => $loggedInUser->id,
      "field_name"        => $redcap_variables[$in],
      "value"             => $value
		);
  }
 
  $result = server_pull($user_event_arm,array($loggedInUser->id),array("first_update","times_updated"));
  
  if(empty($result[0]['first_update'])){
    array_push($data,addEntry($data,$user_event_arm,$loggedInUser->id,'first_update',date("Y-m-d h:i:sa")));
  }

  if(!isset($result[0]['times_updated']))
    array_push($data,addEntry($data,$user_event_arm,$loggedInUser->id,'times_updated',1));
  else
    array_push($data,addEntry($data,$user_event_arm,$loggedInUser->id,'times_updated',intval($result[0]['times_updated'])+1));

  array_push($data,addEntry($data,$user_event_arm,$loggedInUser->id,'last_update',date("Y-m-d h:i:sa")));
 
	$result = RC::writeToApi($data, array("overwriteBehavior" => "overwite", "type" => "eav"), $API_URL , $API_TOKEN);
}

function addEntry($array,$event_arm,$user,$field,$val){
  $data = array(
    "redcap_event_name" => $event_arm,
    "record"            => $user,
    "field_name"        => $field,
    "value"             => $val
  );

  return $data;
}
function server_pull($event, $record,$fields){
  $API_URL       = SurveysConfig::$projects["REDCAP_PORTAL"]["URL"];
  $API_TOKEN     = SurveysConfig::$projects["REDCAP_PORTAL"]["TOKEN"];
  $pull = array(
      'content'     => 'record',
      'events'      => $event,
      'records'     => $record,
      'fields'      => $fields
  );

  $result = RC::callApi($pull, true, $API_URL , $API_TOKEN);
  return $result;
}
