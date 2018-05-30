<?php
markPageLoadTime("start surveys.php");
//DETERMINE WHICH ARM TO BE IN BY CHECKING DAYS SINCE REGISTERED
$consent_date		= strToTime($loggedInUser->consent_ts);
$datediff    		= time() - $consent_date;
$days_active 		= floor($datediff / (60 * 60 * 24));
$first_year 		= Date("Y", $consent_date);
$this_year      	= Date("Y");
$user_event_arm 	= !empty($loggedInUser->user_event_arm) ? $loggedInUser->user_event_arm : REDCAP_PORTAL_EVENT;
$user_short_scale 	= false;
// print_rr($loggedInUser->user_event_arm);
markPageLoadTime("BEGIN CHECK FOR SHORTSCALE");
// CHECK TO SEE IF THEY STARTED THIS CORESURVEY TO DETERMINE SHORT SCALE
if(isset($_SESSION["user_short_scale"])){
	$user_short_scale = $_SESSION["user_short_scale"];
}else{
	$extra_params = array(
	  'content'   	=> 'record',
	  'records' 	=> array($loggedInUser->id) ,
	  'fields'    	=> ["well_q_2_start_ts"],
	  'events'		=> array(REDCAP_PORTAL_EVENT),
	);
	$result 		= RC::callApi($extra_params, true, $_CFG->REDCAP_API_URL, $_CFG->REDCAP_API_TOKEN); 
	// print_rr($days_active);
	// print_rr($result);
	// print_rr($result[0]["well_q_2_start_ts"]);
	//ON ANNIVERSARY UPDATE THEIR EVENT ARM AND USE DIFFERENT PROJECT!!
	if(
		$days_active > 729 
		&& $user_event_arm !== REDCAP_PORTAL_EVENT_2
		&& !empty($result[0]["well_q_2_start_ts"]) 
	  ){
		unset($_SESSION["user_survey_data"]);
		$loggedInUser->updateUser(array("user_event_arm" => REDCAP_PORTAL_EVENT_2));
		$loggedInUser->user_event_arm = $user_event_arm = REDCAP_PORTAL_EVENT_2;
	}else if( $days_active > 364 
		&& $user_event_arm !== REDCAP_PORTAL_EVENT_1
		&& !empty($result[0]["well_q_2_start_ts"]) 
	  ){
	  	//CLEAR SESSION DATA
		unset($_SESSION["user_survey_data"]);
		$loggedInUser->updateUser(array("user_event_arm" => REDCAP_PORTAL_EVENT_1));
		$loggedInUser->user_event_arm = $user_event_arm = REDCAP_PORTAL_EVENT_1;
	}

	if (strpos($user_event_arm, "short") > -1){
		$user_short_scale = true;
	}
	$_SESSION["user_short_scale"] = $user_short_scale;
	// print_rr("user event arm = $user_event_arm/ days active = " . $days_active);
}
markPageLoadTime("END CHECK FOR SHORTSCALE");

markPageLoadTime("BEGIN arm_years");
// MAP EVENTS TO CALENDAR YEARs, DIFFERENT FOR EVERY USER
if(isset($_SESSION["arm_years"])){
	$armyears = $_SESSION["arm_years"];
}else{
	$extra_params = array(
	  'content'   => 'event',
	);
	$result = RC::callApi($extra_params, true, REDCAP_API_URL, REDCAP_API_TOKEN);
	$events = array();
	foreach($result as $event){
		$events[$event["unique_event_name"]] = 1;
		if($event["unique_event_name"] == $user_event_arm){
			break;
		}
	}
	$armyears  = array();
	foreach(array_keys($events) as $armname){
	  $armyears[$armname] = $first_year;
	  $first_year++;
	}
	$_SESSION["arm_years"] = $armyears;
}
$yeararms = array_flip($armyears);
$current_year = end($armyears);
$current_arm  = end($yeararms);
markPageLoadTime("END arm_years");

markPageLoadTime("BEGIN  _SESSION[user_survey_data]");
if(isset($_SESSION["user_survey_data"])){
	//THE BULK OF IT HAS BEEN CALLED ONCE, NOW JUST REFRESH THE NECESSARY DATA
	// markPageLoadTime("BEGIN REFRESH user_survey_data");
	$user_survey_data = $_SESSION["user_survey_data"];
	$user_survey_data->refreshData();
	// markPageLoadTime("END REFRESH user_survey_data");
}else{
	if ($user_short_scale){
		$api_url 	= SurveysConfig::$projects["SHORT_SCALE"]["URL"];
		$api_token 	= SurveysConfig::$projects["SHORT_SCALE"]["TOKEN"];
		$sesh_name 	= "SHORT_SCALE";
	}else{
		$api_url 	= $_CFG->REDCAP_API_URL;
		$api_token 	= $_CFG->REDCAP_API_TOKEN;
		$sesh_name 	= SESSION_NAME;
	}
	$user_survey_data				= new Project($loggedInUser, $sesh_name, $api_url, $api_token);

	//CUSTOM CUSTOM WORK - NEED core_gender from loggedInUser->gender
	if($user_short_scale){
		$alluseranswers = $user_survey_data->getAllUserAnswers();
		foreach($alluseranswers as $i => $eventarm){
			if($eventarm["redcap_event_name"] == $user_event_arm){
				if(empty($eventarm["core_gender"])){
					$data[] = array(
			          "record"            => $loggedInUser->id,
			          "field_name"        => "core_gender",
			          "value"             => $loggedInUser->gender,
			          "redcap_event_name" => $user_event_arm
			        );
			        $result = RC::writeToApi($data, array("overwriteBehavior" => "overwite", "type" => "eav"), $api_url , $api_token);
				}
			}
		}
	}

	$user_survey_data->refreshData();
	$_SESSION["user_survey_data"] 	= $user_survey_data;
	// WILL NEED TO REFRESH THIS WHEN SURVEY SUBMITTED OR ELSE STALE DATA 
}
markPageLoadTime("END  _SESSION[user_survey_data]");

// markPageLoadTime("BEGIN  _SESSION[long_survey_data]");
// if(isset($_SESSION["long_survey_data"])){
// 	$long_survey_data = $_SESSION["long_survey_data"];
// }else{
// 	if ($user_short_scale){
// 		$long_survey_data = $_SESSION["long_survey_data"] = new Project($loggedInUser, SESSION_NAME, $_CFG->REDCAP_API_URL, $_CFG->REDCAP_API_TOKEN);
// 	}else{
// 		$long_survey_data = $_SESSION["long_survey_data"] = $user_survey_data;
// 	}
// }
// markPageLoadTime("END  _SESSION[long_survey_data]");

//THIS DATA NEEDS TO BE REFRESHED EVERYTIME OR RISK BEING STALE 
$surveys 				= $user_survey_data->getActiveAll();
$all_completed 			= $user_survey_data->getAllComplete(); 
$first_survey 			= reset($surveys);

//THESE ONLY NEED DATA CALL ONCE PER SESSION
$all_branching 			= $user_survey_data->getAllInstrumentsBranching();
$core_surveys_complete 	= $user_survey_data->getUserActiveComplete();
$all_survey_keys  		= array_keys($surveys); 
$fruits  				= SurveysConfig::$fruits;

markPageLoadTime("BEGIN  _SESSION[supplemental_surveys]");
//SUPPLEMENTAL PROJECTS
$supp_instruments  = array();
if(!$core_surveys_complete){
	// IF NOT COMPLETE THEN JUST GET THE STUB DATA
	if(isset($_SESSION["supplemental_surveys"])){
		$supp_instruments  = $_SESSION["supplemental_surveys"];
	}else{
		$extra_params = array(
			'content' 	=> 'project',
		);
		$proj_name 	= "Supp"; 
		$result 	= RC::callApi($extra_params, true, SurveysConfig::$projects[$proj_name]["URL"], SurveysConfig::$projects[$proj_name]["TOKEN"]);
		$supp_instruments  = array();
		foreach(SurveysConfig::$supp_surveys as $supp_instrument_id => $supp_label){
			$supp_instruments[$supp_instrument_id] = array(  "project_notes" 	=> $result["project_notes"]
															,"project" 			=> $proj_name
															,"label" 			=> $supp_label
															,"survey_complete" 	=> false
													);
		}
		$_SESSION["supplemental_surveys"] = $supp_instruments;
	}	
}else{
	if(isset($_SESSION["supplemental_surveys"]) && array_key_exists("Supp",$_SESSION["supplemental_surveys"]) ){
		//THE BULK OF IT HAS BEEN CALLED ONCE, NOW JUST REFRESH THE NECESSARY DATA
		$supp_surveys  = $_SESSION["supplemental_surveys"];
		// markPageLoadTime("BEGIN REFRESH supplemental_surveys");
		foreach($supp_surveys as $supp_survey){
			$supp_survey->refreshData();
			$supp_branching 	= $supp_survey->getAllInstrumentsBranching();
			$all_branching 		= array_merge($all_branching,$supp_branching);
		}
		// markPageLoadTime("END REFRESH supplemental_surveys");
	}else{
		$proj_name 	 				= "Supp";
		$supp_surveys 				= array();
		$supplementalProject 		= new Project($loggedInUser, $proj_name, SurveysConfig::$projects[$proj_name]["URL"], SurveysConfig::$projects[$proj_name]["TOKEN"]);
		$suppsurveys 				= $supplementalProject->getActiveAll();
		$supp_branching 			= $supplementalProject->getAllInstrumentsBranching();
		if(!empty($supp_branching)){
			$all_branching 			= array_merge($all_branching,$supp_branching);
		}
		$supp_surveys[$proj_name] 	= $supplementalProject;

		$_SESSION["supplemental_surveys"] 	= $supp_surveys;
		// WILL NEED TO REFRESH THIS WHEN SURVEY SUBMITTED OR ELSE STALE DATA 
	}

	$supp_instruments = array();
	foreach($_SESSION["supplemental_surveys"] as $projname => $supp_project){
		$supp_instruments 	= array_merge( $supp_instruments,  $supp_project->getActiveAll() );
	} 
}
markPageLoadTime("END  _SESSION[supplemental_surveys]");

$supp_surveys_keys 		= array_keys($supp_instruments);
$available_instruments  = $user_short_scale ? SurveysConfig::$short_surveys : SurveysConfig::$core_surveys;
// print_rr($supp_surveys_keys);
// print_rr($user_short_scale->getActiveAll(),1);
// print_rr($supp_instruments,1);
// print_rr($supp_surveys,1);
// print_rr($surveys ,1);
markPageLoadTime("end surveys.php");
// print_rr($loggedInUser->user_event_arm);