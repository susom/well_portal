<?php
//Some Heavier Required Scripts Holding Up Pageload.  Run Here.
//THEN STUFF THEM IN THE SESSION
if(empty($pid)){
	//CORE SURVEY DATA
	// markPageLoadTime("BEGIN  _SESSION[user_survey_data]");
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

	//THIS DATA NEEDS TO BE REFRESHED EVERYTIME OR RISK BEING STALE 
	$surveys 				= $user_survey_data->getActiveAll();
	$all_completed_temp		= $user_survey_data->getAllComplete(); 
	$all_completed_keys 	= array_filter(array_keys($all_completed_temp),function($key){
	 return $key !== "id" && !strpos($key,"_ts");
	});
	$all_completed 			= array_intersect_key($all_completed_temp, array_flip($all_completed_keys));
	$first_survey 			= reset($surveys);

	//THESE ONLY NEED DATA CALL ONCE PER SESSION
	$all_branching 			= $user_survey_data->getAllInstrumentsBranching();
	$core_surveys_complete 	= $user_survey_data->getUserActiveComplete();
	$all_survey_keys  		= array_keys($surveys); 
	// markPageLoadTime("END  _SESSION[user_survey_data]");
	
	$raw_fields = array();
	foreach($all_survey_keys as $key){
		$raw_fields = array_merge($raw_fields,$surveys[$key]["raw"]);
	}
	$all_fields_temp 	= array_column($raw_fields,"field_name");
	$all_fields 		= array_filter($all_fields_temp,function($val){
	 return $val !== "id" && !strpos($val,"_ts");
	});
	// print_rr($surveys); 
	// print_rr($all_completed);
	// print_rr($core_surveys_complete);
	// print_rr($all_branching );
}else{
	// markPageLoadTime("BEGIN  _SESSION[supplemental_surveys]");
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
		$all_branching = array();
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

		$all_completed = array_filter($user_arm_answers);
	}
	// markPageLoadTime("END  _SESSION[supplemental_surveys]");
}
?>