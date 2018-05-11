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
	$doms = json_decode($_POST["domains"],1);
	print_r($doms);
	$API_URL      = SurveysConfig::$projects["REDCAP_PORTAL"]["URL"];
	$API_TOKEN    = SurveysConfig::$projects["REDCAP_PORTAL"]["TOKEN"];
	foreach($doms as $key => $val){
		$in = array_search($val, $radar_domains);
		$data[] = array(
	    	"record"            => $loggedInUser->id,
	     	"field_name"        => $redcap_variables[$in],
	      	"value"             => $key
		);
		// print_r($data);
	
	}
	$result = RC::writeToApi($data, array("overwriteBehavior" => "overwite", "type" => "eav"), $API_URL , $API_TOKEN);
	print_r($result);
}