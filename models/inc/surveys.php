<?php
// markPageLoadTime("start surveys.php");
//DETERMINE WHICH ARM TO BE IN BY CHECKING DAYS SINCE REGISTERED
$consent_date		= strToTime($loggedInUser->consent_ts);
$datediff    		= time() - $consent_date;
$days_active 		= floor($datediff / (60 * 60 * 24));
$update_arm 		= !empty($loggedInUser->user_event_arm) ? false : true;
$user_event_arm 	= !empty($loggedInUser->user_event_arm) ? $loggedInUser->user_event_arm : REDCAP_PORTAL_EVENT;
$user_short_scale 	= false;

// markPageLoadTime("BEGIN CHECK FOR PROJECT INFO");
// put this in sesssion
if(!isset($_SESSION["project_info"])){
	$_SESSION["project_info"] = array();
	$extra_params 		= array("content" 	=> "project"
								,"format" 	=> "json"
	);
	$project_info 		= RC::callApi($extra_params, true, $_CFG->REDCAP_API_URL, $_CFG->REDCAP_API_TOKEN);
	$_SESSION["project_info"]["project_notes"] = json_decode($project_info["project_notes"],1);

	$extra_params 		= array("content" 	=> "project"
								,"format" 	=> "json"
	);
	$supp_project_info 	= RC::callApi($extra_params, true, SurveysConfig::$projects["Supp"]["URL"], SurveysConfig::$projects["Supp"]["TOKEN"]);
	$_SESSION["project_info"]["supp_project_notes"] = json_decode($supp_project_info["project_notes"],1);
}
$project_notes 			= $_SESSION["project_info"]["project_notes"];
$supp_project_notes		= $_SESSION["project_info"]["supp_project_notes"];
// markPageLoadTime("END CHECK FOR PROJECT INFO");

// markPageLoadTime("BEGIN CHECK FOR SHORTSCALE");
// CHECK TO SEE IF THEY STARTED THIS CORESURVEY TO DETERMINE SHORT SCALE
if(!isset($_SESSION["user_short_scale"])){
	//ON ANNIVERSARY UPDATE THEIR EVENT ARM AND USE DIFFERENT PROJECT!!
	if( $days_active > 1093 ){
		if($user_event_arm != REDCAP_PORTAL_EVENT_3){
			$user_event_arm = REDCAP_PORTAL_EVENT_3;
			$update_arm = true;
		}
	}else if( $days_active > 729 && $days_active <= 1093 ){
		if($user_event_arm != REDCAP_PORTAL_EVENT_2){
			$user_event_arm = REDCAP_PORTAL_EVENT_2;
			$update_arm = true;
		}
	}else if( $days_active > 364 && $days_active <= 729 ){
		if($user_event_arm != REDCAP_PORTAL_EVENT_1 ){
			$user_event_arm = REDCAP_PORTAL_EVENT_1;
			$update_arm = true;
		}
	}
		
	if($update_arm){
		unset($_SESSION["user_survey_data"]);
		$loggedInUser->updateUser(array("user_event_arm" => $user_event_arm));
		$loggedInUser->user_event_arm = $user_event_arm;
	}

	if (strpos($user_event_arm, "short") > -1){
		$user_short_scale = true;
	}
	$_SESSION["user_short_scale"] = $user_short_scale;
}
$user_short_scale 		= $_SESSION["user_short_scale"];
// markPageLoadTime("END CHECK FOR SHORTSCALE");

// markPageLoadTime("BEGIN CHECK SURVEY COMPLETION");
$supp_instrument_ids 	= array_keys(SurveysConfig::$supp_surveys);
$core_instrument_ids 	= $user_short_scale ? array_values(SurveysConfig::$short_surveys) : array_keys(SurveysConfig::$core_icons);
if(!isset($_SESSION["completed_timestamps"])){
	$_SESSION["completed_timestamps"] = array();
	$all_instruments 	= array_merge($core_instrument_ids,$supp_instrument_ids);
	$extra_params 		= array(
		'content'     	=> 'record',
		'records'     	=> array($loggedInUser->id) ,
		'type'      	=> "flat",
		'events'		=> $user_event_arm,
		'exportSurveyFields' => true
	);
	$_SESSION["user_arm_answers"]	= RC::callApi($extra_params, true, $_CFG->REDCAP_API_URL, $_CFG->REDCAP_API_TOKEN); 
	$_SESSION["user_arm_answers"] 	= current($_SESSION["user_arm_answers"]);
	foreach($all_instruments as $instrument_id){
		$completion_timestamp = $instrument_id . "_timestamp";
		if(!empty($_SESSION["user_arm_answers"][$completion_timestamp])){
			array_push($_SESSION["completed_timestamps"],$instrument_id);
		}
	}
}
$user_arm_answers 		= $_SESSION["user_arm_answers"];
$completed_timestamps 	= $_SESSION["completed_timestamps"];
$core_complete 			= array_diff($core_instrument_ids, $completed_timestamps);
$core_surveys_complete  = empty($core_complete) ? true : false;
// markPageLoadTime("END CHECK SURVEY COMPLETION");

// markPageLoadTime("end surveys.php");
