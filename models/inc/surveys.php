<?php
// markPageLoadTime("start surveys.php");
//DETERMINE WHICH ARM TO BE IN BY CHECKING DAYS SINCE REGISTERED
$consent_date		= strToTime($loggedInUser->consent_ts);
$datediff    		= time() - $consent_date;
$days_active 		= floor($datediff / (60 * 60 * 24));
$update_arm 		= !empty($loggedInUser->user_event_arm) ? false : true;
$user_event_arm     = $loggedInUser->user_event_arm = !empty($loggedInUser->user_event_arm) ? $loggedInUser->user_event_arm : REDCAP_PORTAL_EVENT;

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
if(!isset($_SESSION["user_anniversary"])){
	//ON ANNIVERSARY UPDATE THEIR EVENT ARM AND USE DIFFERENT PROJECT!!
	if( $days_active > 1821 ){
		if($user_event_arm != REDCAP_PORTAL_EVENT_5){
			$user_event_arm = REDCAP_PORTAL_EVENT_5;
			$update_arm = true;
		}
	}else if( $days_active > 1457 && $days_active <= 1821 ){
		if($user_event_arm != REDCAP_PORTAL_EVENT_4){
			$user_event_arm = REDCAP_PORTAL_EVENT_4;
			$update_arm = true;
		}
	}else if( $days_active > 1093 && $days_active <= 1457 ){
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

	$_SESSION["user_anniversary"] = $user_event_arm;
}
$user_event_arm 		= $_SESSION["user_anniversary"];
// markPageLoadTime("END CHECK FOR ANNIVERSARY CHECK");

// markPageLoadTime("BEGIN CHECK SURVEY COMPLETION");
$supp_instrument_ids 	= array_keys(SurveysConfig::$supp_surveys);
$core_instrument_ids 	= array_keys(SurveysConfig::$core_icons);

if(!isset($_SESSION["completed_timestamps"]) || true){
    //TODO FIX THIS WHY IS THIS SO HORRIBLY MISNAMED?
    $all_instruments 	= array_merge($core_instrument_ids,$supp_instrument_ids);

    $CORE_API_URL       = $_CFG->REDCAP_API_URL;
    $CORE_API_TOKEN     = $_CFG->REDCAP_API_TOKEN;
    $_SESSION["completed_timestamps"] = array();
	$extra_params 		= array(
		'content'     	=> 'record',
		'records'     	=> array($loggedInUser->id) ,
		'type'      	=> "flat",
		'events'		=> $user_event_arm,
		'exportSurveyFields' => true
	);
	$core_answers		= RC::callApi($extra_params, true, $CORE_API_URL, $CORE_API_TOKEN);
	$core_answers 		= !empty($core_answers) ? current($core_answers) : array();

	$extra_params 		= array(
		'content'     	=> 'record',
		'records'     	=> array($loggedInUser->id) ,
		'type'      	=> "flat",
		'events'		=> $user_event_arm,
		'exportSurveyFields' => true
	);
	$supp_answers		= RC::callApi($extra_params, true, SurveysConfig::$projects["Supp"]["URL"], SurveysConfig::$projects["Supp"]["TOKEN"]);
    $supp_answers       = !empty($supp_answers) ? current($supp_answers) : array();
	$what_the_fuck = $_SESSION["user_arm_answers"] = array_merge($core_answers, $supp_answers);

	foreach($all_instruments as $instrument_id){
        $completion_flag        = $instrument_id . "_complete";
        $completion_timestamp   = $instrument_id . "_timestamp";
		if(!empty($_SESSION["user_arm_answers"][$completion_timestamp]) || !empty($_SESSION["user_arm_answers"][$completion_flag])){
			array_push($_SESSION["completed_timestamps"],$instrument_id);
		}
	}
}
if(!isset($_SESSION["core_timestamps"])){
    $core_timestamps_complete = array();
    $extra_params 		= array(
        'content'     	=> 'record',
        'records'     	=> array($loggedInUser->id) ,
        'type'      	=> "flat",
//        'fields'        => array("event_name","your_feedback_complete","your_feedback_timestamp","wellbeing_questions_complete", "wellbeing_questions_timestamp"),
        'filterLogic'   => '[wellbeing_questions_complete] = 2 and [your_feedback_complete] = 2',
        'exportSurveyFields' => true
    );

// TODO WONT NEED THIS AFTER WE CONVERT EVERY YEAR BACK TO JUST USING THE LONG SURVEY
    $core_results	    = RC::callApi($extra_params, true, $_CFG->REDCAP_API_URL, $_CFG->REDCAP_API_TOKEN);
    $extra_params 		= array(
        'content'     	=> 'record',
        'records'     	=> array($loggedInUser->id) ,
        'type'      	=> "flat",
//        'fields'        => array("event_name","brief_well_for_life_scale_timestamp","brief_well_for_life_scale_complete"),
        'filterLogic'   => '[brief_well_for_life_scale_complete] = 2',
        'exportSurveyFields' => true
    );
    $core_short_results	= RC::callApi($extra_params, true,SurveysConfig::$projects["SHORT_SCALE"]["URL"], SurveysConfig::$projects["SHORT_SCALE"]["TOKEN"]);
    $core_results_all = array_merge($core_results,$core_short_results);
    foreach($core_results_all as $results){
        $core_timestamps_complete[$results["redcap_event_name"]] = array_key_exists("brief_well_for_life_scale_timestamp",$results) ? $results["brief_well_for_life_scale_timestamp"] : $results["your_feedback_timestamp"];
    }
    $_SESSION["core_timestamps"] = $core_timestamps_complete;
}

$user_arm_answers 		= $_SESSION["user_arm_answers"];
$completed_timestamps 	= $_SESSION["completed_timestamps"];
$core_timestamps        = $_SESSION["core_timestamps"];

$core_complete 			= array_diff($core_instrument_ids, $completed_timestamps);
$core_surveys_complete  = empty($core_complete) ? true : false;

$gamify                 = new Gamify(SurveysConfig::$projects["ADMIN_CMS"]["URL"],SurveysConfig::$projects["ADMIN_CMS"]["TOKEN"]);
$game_points            = $gamify->showPoints() ;

if(!isset($_SESSION["persist_points"])){
    $extra_params   = array(
        'content'   => 'record',
        'format'    => 'json',
        "records"   => $loggedInUser->id,
        "fields"    => "annual_persist_points",
        "events"    => $user_event_arm
    );
    $results        = RC::callApi($extra_params, true, $_CFG->REDCAP_API_URL, $_CFG->REDCAP_API_TOKEN);
    $current        = !empty($results) ? current($results) : array("annual_persist_points" => "");
    $_SESSION["persist_points"] = !empty($current["annual_persist_points"])  ? json_decode($current["annual_persist_points"],1) : array();
}

if(!isset($_SESSION["points"])){
    $_SESSION["points"] = array();
}
$arm_years          = getSessionEventYears();

// markPageLoadTime("END CHECK SURVEY COMPLETION");

// markPageLoadTime("end surveys.php");
