<?php
require_once("models/config.php"); 


if($_POST["domains"]){
	$doms = json_decode($_POST["domains"],1);
	print_r($doms);
	$API_URL      = SurveysConfig::$projects["REDCAP_PORTAL"]["URL"];
	$API_TOKEN    = SurveysConfig::$projects["REDCAP_PORTAL"]["TOKEN"];
	
	// $result = RC::writeToApi($data, null, $API_URL, $API_TOKEN);
	// print_r($result);
}

