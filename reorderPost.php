<?php
require_once("models/config.php"); 


if($_POST["domains"]){
	$doms = ($_POST["domains"]);
	print_r($doms);
	$API_URL      = SurveysConfig::$projects["REDCAP_PORTAL"]["URL"];
	$API_TOKEN    = SurveysConfig::$projects["REDCAP_PORTAL"]["TOKEN"];

	$data[] = array(
    	"record"            => $loggedInUser->id,
     	"field_name"        => "domain_order",
      	"value"             => $doms
	);
	
	$result = RC::writeToApi($data, array("overwriteBehavior" => "overwite", "type" => "eav"), $API_URL , $API_TOKEN);
	print_r($result);
}

