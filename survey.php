<?php 
require_once("models/config.php"); 
include("models/inc/checklogin.php");
include("models/class.Survey.php");

//SPECIAL STOPBANG SCORING
if(isset($_REQUEST["STOPBANG"])){
  $project_name = $_REQUEST["project"] ?: null;
  $projects     = SurveysConfig::$projects;
  $API_TOKEN    = $projects[$project_name]["TOKEN"];
  $API_URL      = $projects[$project_name]["URL"];

  $data         = array();
  $record_id    = $project_name !== $_CFG->SESSION_NAME ? $loggedInUser->{$project_name} : $loggedInUser->id;
  $event_name   = $project_name !== $_CFG->SESSION_NAME ? null : $_SESSION[$_CFG->SESSION_NAME]["survey_context"]["event"];

  $survey_id    = $_REQUEST["sid"] ?: null;
  $value        = $_REQUEST["met_score"] ?: null;

  $data[] = array(
      "record"            => $record_id,
      "field_name"        => 'tcm_score',
      "value"             => $value
    );

  if($event_name){
    $data[0]["redcap_event_name"] = $event_name;
  }
  $result = RC::writeToApi($data, array("overwriteBehavior" => "overwite", "type" => "eav"), $API_URL, $API_TOKEN);
}

//SPECIAL CUSTOM tcm SCORINGCAPTURE
if(isset($_REQUEST["TCM"])){
  $project_name = $_REQUEST["project"] ?: null;
  $projects     = SurveysConfig::$projects;
  $API_TOKEN    = $projects[$project_name]["TOKEN"];
  $API_URL      = $projects[$project_name]["URL"];

  $data         = array();
  $record_id    = $project_name !== $_CFG->SESSION_NAME ? $loggedInUser->{$project_name} : $loggedInUser->id;
  $event_name   = $project_name !== $_CFG->SESSION_NAME ? null : $_SESSION[$_CFG->SESSION_NAME]["survey_context"]["event"];

  $survey_id    = $_REQUEST["sid"] ?: null;
  $value        = $_REQUEST["met_score"] ?: null;

  $data[] = array(
      "record"            => $record_id,
      "field_name"        => 'tcm_score',
      "value"             => $value
    );

  if($event_name){
    $data[0]["redcap_event_name"] = $event_name;
  }
  $result = RC::writeToApi($data, array("overwriteBehavior" => "overwite", "type" => "eav"), $API_URL, $API_TOKEN);
}

//SPECIAL CUSTOM ipaq SCORINGCAPTURE
if(isset($_REQUEST["IPAQ"])){
  $project_name = $_REQUEST["project"] ?: null;
  $projects     = SurveysConfig::$projects;
  $API_TOKEN    = $projects[$project_name]["TOKEN"];
  $API_URL      = $projects[$project_name]["URL"];

  $data         = array();
  $record_id    = $project_name !== $_CFG->SESSION_NAME ? $loggedInUser->{$project_name} : $loggedInUser->id;
  $event_name   = $project_name !== $_CFG->SESSION_NAME ? null : $_SESSION[$_CFG->SESSION_NAME]["survey_context"]["event"];

  $survey_id    = $_REQUEST["sid"] ?: null;
  $value        = $_REQUEST["met_score"] ?: null;

  $data[] = array(
      "record"            => $record_id,
      "field_name"        => 'tcm_score',
      "value"             => $value
    );

  if($event_name){
    $data[0]["redcap_event_name"] = $event_name;
  }
  $result = RC::writeToApi($data, array("overwriteBehavior" => "overwite", "type" => "eav"), $API_URL, $API_TOKEN);

  exit;
}

//SPECIAL CUSTOM MAT SCORINGCAPTURE
if(isset($_REQUEST["mat"])){
  include "supp_feedback/MAT_scoring.php";
  $project_name = $_REQUEST["project"] ?: null;
  $projects     = SurveysConfig::$projects;
  $API_TOKEN    = $projects[$project_name]["TOKEN"];
  $API_URL      = $projects[$project_name]["URL"];

  $data         = array();
  $record_id    = $project_name !== $_CFG->SESSION_NAME ? $loggedInUser->{$project_name} : $loggedInUser->id;
  $event_name   = $project_name !== $_CFG->SESSION_NAME ? null : $_SESSION[$_CFG->SESSION_NAME]["survey_context"]["event"];

  $survey_id    = $_REQUEST["sid"] ?: null;
  $mat_answers  = $_REQUEST["mat_answers"] ?: null;
  $mat_answers  = json_decode($mat_answers,1);

  $matstring  = "";
  foreach($mat_answers as $fieldlabel => $values){
    $mat_key  = $values["vid"];
    $q_val    = $values["value"];
    $mat_category = $MAT_cat[$mat_key];
    $matvalue = getMATscoreCAT($mat_category,$q_val);
    $matstring .= $matvalue;
  }
  
  $matscore = isset($scoring[$matstring]) ? $scoring[$matstring] : 0 ;
  $data[]   = array(
      "record"            => $record_id,
      "field_name"        => 'mat_score',
      "value"             => $matscore
    );

  if($event_name){
    $data[0]["redcap_event_name"] = $event_name;
  }
  $result = RC::writeToApi($data, array("overwriteBehavior" => "overwite", "type" => "eav"), $API_URL, $API_TOKEN);
  $data   = array_shift($data);
  $data["matstring"] = $matstring;
  print_r( json_encode($data) );
  exit;
}

//SPECIAL CUSTOM MET SCORECAPTURE
if(isset($_REQUEST["met"])){
  $project_name = $_REQUEST["project"] ?: null;
  $projects     = SurveysConfig::$projects;
  $API_TOKEN    = $projects[$project_name]["TOKEN"];
  $API_URL      = $projects[$project_name]["URL"];

  $data         = array();
  $record_id    = $project_name !== $_CFG->SESSION_NAME ? $loggedInUser->{$project_name} : $loggedInUser->id;
  $event_name   = $project_name !== $_CFG->SESSION_NAME ? null : $_SESSION[$_CFG->SESSION_NAME]["survey_context"]["event"];

  $survey_id    = $_REQUEST["sid"] ?: null;
  $value        = $_REQUEST["met_score"] ?: null;

  $data[] = array(
      "record"            => $record_id,
      "field_name"        => 'met_score',
      "value"             => $value
    );

  if($event_name){
    $data[0]["redcap_event_name"] = $event_name;
  }
  $result = RC::writeToApi($data, array("overwriteBehavior" => "overwite", "type" => "eav"), $API_URL, $API_TOKEN);
  print_r($data);
  exit;
}

//SPECIAL CUSTOM sleep SCORECAPTURE
if(isset($_REQUEST["sleep"])){
  $project_name = $_REQUEST["project"] ?: null;
  $projects     = SurveysConfig::$projects;
  $API_TOKEN    = $projects[$project_name]["TOKEN"];
  $API_URL      = $projects[$project_name]["URL"];

  $data         = array();
  $record_id    = $project_name !== $_CFG->SESSION_NAME ? $loggedInUser->{$project_name} : $loggedInUser->id;
  $event_name   = $project_name !== $_CFG->SESSION_NAME ? null : $_SESSION[$_CFG->SESSION_NAME]["survey_context"]["event"];

  $survey_id    = $_REQUEST["sid"] ?: null;
  $value        = $_REQUEST["met_score"] ?: null;

  $data[] = array(
      "record"            => $record_id,
      "field_name"        => 'psqi_score',
      "value"             => $value
    );

  if($event_name){
    $data[0]["redcap_event_name"] = $event_name;
  }
  $result = RC::writeToApi($data, array("overwriteBehavior" => "overwite", "type" => "eav"), $API_URL, $API_TOKEN);
  print_r($data);
  exit;
}

//SPECIAL CUSTOM ipaq SCORECAPTURE
if(isset($_REQUEST["ipaq"])){
  $project_name = $_REQUEST["project"] ?: null;
  $projects     = SurveysConfig::$projects;
  $API_TOKEN    = $projects[$project_name]["TOKEN"];
  $API_URL      = $projects[$project_name]["URL"];

  $data         = array();
  $record_id    = $project_name !== $_CFG->SESSION_NAME ? $loggedInUser->{$project_name} : $loggedInUser->id;
  $event_name   = $loggedInUser->user_event_arm;
  $survey_id    = $_REQUEST["sid"] ?: null;

  $ipaqscores   = $_REQUEST["ipaq_scores"] ?: null;
  $scores       = json_decode($ipaqscores,1);
  $data[] = array(
      "record"            => $record_id,
      "field_name"        => 'ipaq_total_walking',
      "value"             => $scores["ipaq_total_walking"]
    );
  $data[] = array(
      "record"            => $record_id,
      "field_name"        => 'ipaq_total_moderate',
      "value"             => $scores["ipaq_total_moderate"]
    );
  $data[] = array(
      "record"            => $record_id,
      "field_name"        => 'ipaq_total_vigorous',
      "value"             => $scores["ipaq_total_vigorous"]
    );
  $data[] = array(
      "record"            => $record_id,
      "field_name"        => 'ipaq_total_overall',
      "value"             => $scores["ipaq_total_overall"]
    );


  if(!empty($event_name)){
    $data[0]["redcap_event_name"] = $event_name;
    $data[1]["redcap_event_name"] = $event_name;
    $data[2]["redcap_event_name"] = $event_name;
    $data[3]["redcap_event_name"] = $event_name;
  }
  $result = RC::writeToApi($data, array("overwriteBehavior" => "overwite", "type" => "eav"), $API_URL, $API_TOKEN);
  print_r($data);
  exit;
}

//POSTING DATA TO REDCAP API
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
  exit;
}

//GET THE CURRENT TOP NAV CATEGORy
$nav    = isset($_REQUEST["nav"]) ? $_REQUEST["nav"] : "home";
$navon  = array("home" => "", "reports" => "", "game" => "", "resources" => "", "rewards" => "", "activity" => "");
$navon[$nav] = "on";

// GET SURVEY DDATa, AND STUFF INTO SESSION
//IF CORE SURVEY GET THE SURVEY ID
$avail_surveys      = $core_instrument_ids;
$first_core_survey  = array_splice($avail_surveys,0,1);
$sid = $current_surveyid = isset($_REQUEST["sid"]) ? $_REQUEST["sid"] : "";

$surveyon       = array();
$surveynav      = array_merge($first_core_survey, $supp_instrument_ids);
foreach($surveynav as $surveyitem){
    $surveyon[$surveyitem] = "";
}
if(!empty($sid)){
    $navon[$nav] = "";
    if(!array_key_exists($sid,$surveyon)){
        $surveyon["wellbeing_questions"] = "on";
    }else{
        $surveyon[$sid] = "on";   
    }
}

// IF SUPP SURVEY GET PROJECT TOO
$pid = $project = isset($_REQUEST["project"]) ? $_REQUEST["project"] : "";

include("models/inc/surveys_data.php");
if(!empty($pid)){
    if(array_key_exists($pid, SurveysConfig::$projects)){
        $supp_project = $supp_surveys[$pid]->getSingleInstrument($sid);
        $surveys      = $supp_surveys[$pid]->getActiveAll();
        $avail_surveys = array($sid);
    }

    if(!array_key_exists($sid,$surveyon)){
      //ONLY ONE MENUITEM HIGHLIGHTED
      $surveyon["wellbeing_questions"] = "on";
    }
}else{
    //ITS A CORESURVEY, FIND THE LATEST INCOMPLETE ONE
    foreach($surveys as $surveyid => $survey){
      $surveycomplete = $survey["survey_complete"];
      if(!$surveycomplete){
        $sid = $current_surveyid = $surveyid;
        break;
      }
    }
}

//GET THE SURVEY DATA
$survey_num = $survey_count = 1;

if(array_key_exists($sid, $surveys)){
    $survey_data    = $surveys[$sid];
    if($loggedInUser->user_event_arm !== "enrollment_arm_1"){ // && in_array($sid,array("about_you","a_little_bit_about_you", "contact_information")
        //for years following "enrollment" , some answers should be auto populated
        $extra_params = array(
            'content' 	=> 'formEventMapping',
        );
        $result = RC::callApi($extra_params, true,SurveysConfig::$projects["REDCAP_PORTAL"]["URL"], SurveysConfig::$projects["REDCAP_PORTAL"]["TOKEN"]);
        $all_events = array_unique(array_column($result,"unique_event_name"));
        $extra_params 		= array(
            'content'     	=> 'record',
            'records'     	=> array($loggedInUser->id) ,
            'type'      	=> "flat",
            'events'        => "'".implode("','",$all_events) ."'",
            'fields'        => $followup_surveys_carryover,
            'exportSurveyFields' => true
        );
        $core_answers	    = RC::callApi($extra_params, true,SurveysConfig::$projects["REDCAP_PORTAL"]["URL"], SurveysConfig::$projects["REDCAP_PORTAL"]["TOKEN"]);

        $older_completed    = array_filter(array_shift($core_answers));
        foreach($core_answers as $arm_core_answers){
            //first time through, over write older completed with current completed to = latest completed;
            $newer_completed  = array_filter($arm_core_answers);
            $older_completed  = array_merge($older_completed, $newer_completed); //this order so the new stuf will override the old
        }
        $survey_data["completed_fields"] = array_merge($older_completed, $survey_data["completed_fields"]);
    }

    //FOLLOW UP YEARS CAN SKIP THESE QUESTIONs
    foreach( $followup_surveys_exclude as $fieldname){
        $hide_field = array_search($fieldname,array_column($survey_data["raw"],"field_name"));
        $survey_data["raw"][$hide_field]["field_annotation"] = "@HIDDEN";
    }

    if($survey_data["project"] == "REDCAP_PORTAL"){
      $survey_num   = array_search($sid, array_keys($surveys)) + 1;
      $survey_count = count($surveys);
    }

    //ON SURVEY PAGE STORE THIS FOR USE WITH THE AJAX EVENTS 
    $_SESSION[SESSION_NAME]["survey_context"] = array("event" => $survey_data["event"]);

    //LOAD UP THE SURVEY PRINTER HERE
//    print_rr($survey_data);
    $active_survey  = new Survey($survey_data);
}else{
  //IF BAD SURVEY ID PASSED, REDIRECT BACK TO DASHBOARD
  $destination = $websiteUrl."/index.php";
  header("Location: " . $destination);
  exit; 
}



//POP UP IN BETWEEN SURVEYS 
//NEEDS TO GO BELOW SUPPLEMENTALL PROJECTS WORK FOR NOW
$forsleep_complete_only_bs = 0;
if(isset($_GET["survey_complete"])){
    //IF SLEEP HABITS COMPLETE, LETS TAKE A BREAK AND REDIRECT THEM AWAY TO DOMAIN RANKING
    //THEN WHEN THEY REORDER THAT THEY CAN RETURN TO "ABOUT_YOU"

    //ONLY LONG ANNIVERSARIES GET POP UP TREATMENT
    //IF NO URL PASSED IN THEN REDIRECT BACK
    $surveyid = $_GET["survey_complete"];

    if(array_key_exists($surveyid,$surveys)){
      $pts_survey_complete = json_decode($game_points["gamify_pts_survey_complete"],1);

      $index        = array_search($surveyid, $all_survey_keys);
      $survey       = $surveys[$surveyid];
      $success_msg  = $lang["YOUVE_BEEN_AWARDED"] . " : <span class='fruit " . SurveysConfig::$fruits[$index] . "'></span>" ;
      if(isset($all_survey_keys[$index+1])){
        $nextlink     = "survey.php?sid=". $all_survey_keys[$index+1];
        $nextsid      = $all_survey_keys[$index+1];
        if($surveyid == "your_sleep_habits"){
          $forsleep_complete_only_bs = 1;
          $success_msg .=  " <br> <span class='earned_points'>You've earned <b>".$pts_survey_complete["value"]."</b> WELL Points!</span> <hr/><p class='organize'><a id='aftersleep_redirect' href='activity.php?nextsid=$nextsid'>".lang("DOMAIN_RANKING_PROMPT")."</a></p>";
        }else{
          $success_msg .= " <span class='earned_points'>You've earned <b>".$pts_survey_complete["value"]."</b> WELL Points!</span>";
        }
        addSessionMessage( $success_msg , "success");
      }
    }

    if(isset($supp_surveys) && array_key_exists($surveyid,$supp_surveys)){
      $index  = array_search($surveyid, $supp_surveys_keys);
      $survey = $supp_surveys[$surveyid];
      $success_msg  = $lang["FITNESS_BADGE"]. ": <span class='fitness " . SurveysConfig::$fitness[$index] . "'></span>" ;
      if(isset($all_survey_keys[$index+1])){
        $success_msg .= $lang["GET_ALL_BADGES"]. "<br> ";
        addSessionMessage( $success_msg , "success");
      }
    }

    if(!in_array($surveyid , $_SESSION["persist_points"])){
        array_push($_SESSION["persist_points"],$surveyid);
        $pt_val           = json_decode($game_points["gamify_pts_survey_complete"],1);
        $persist_pts      = $pt_val["value"];
        $data           = array();
        $data[]         = array(
            "record"            => $loggedInUser->id,
            "field_name"        => "annual_persist_points",
            "value"             => json_encode($_SESSION["persist_points"]),
            "redcap_event_name" => (!empty($loggedInUser->user_event_arm) ? $loggedInUser->user_event_arm : REDCAP_PORTAL_EVENT)
        );
        $result = RC::writeToApi($data, array("overwriteBehavior" => "overwrite", "type" => "eav"), SurveysConfig::$projects["REDCAP_PORTAL"]["URL"], SurveysConfig::$projects["REDCAP_PORTAL"]["TOKEN"]);
        $result = updateGlobalPersistPoints($loggedInUser->id, $persist_pts);


//        $js_update_points = $persist_pts;
    }
}

// INITIAL PROGRESS BAR
$current_section    = array_search($current_surveyid, $avail_surveys);
$section_perc       = round((1/count($avail_surveys))*100);
$starting_width     = round($current_section * $section_perc);
$total_questions    = $active_survey->surveytotal;
$completed_count    = count($active_survey->completed);
$perc_section       = $completed_count/$total_questions;
$percent_complete   = (($perc_section*$section_perc) + $starting_width);
$percent_complete   = $percent_complete . "%";

$pageTitle = "Well v2 Survey";
$bodyClass = "survey";
include_once("models/inc/gl_head.php");
?>
    <div class="main-container">
        <div class="main wrapper clearfix">
            <article class="surveyFrame">
                <?php
                  if(!$active_survey->surveycomplete){
                      ?>
                      <div id="survey_progress" class='progress progress-striped active'>
                        <div class='progress-bar bg-info lter' data-toggle='tooltip' data-original-title='<?php echo $percent_complete?>' style='width: <?php echo $percent_complete?>'></div>
                        <div class='section_progress'><?php echo "Section $survey_num of $survey_count"; ?></div>
                      </div>
                      <?php    
                  }

                  //PRINT OUT THE HTML FOR THIS SURVEY
                  $active_survey->printHTML($survey_data["event"], $sid);
                  if($sid == "wellbeing_questions" && $active_survey->surveycomplete){
                    //for long well_being questions, it wraps up all 11 instruments so we need to print out the other 10 as well
                   
                    foreach($surveys as $survey_id => $survey_data){
                      if($survey_id == $sid){
                        continue;
                      }
                      //LOAD UP THE SURVEY PRINTER HERE
                      $other_core_survey  = new Survey($survey_data);
                      $other_core_survey->printHTML($survey_data["event"], $survey_id);
                    }
                  }
                ?>
            </article>
            <?php 
            include_once("models/inc/gl_surveynav.php");
            ?>
        </div> <!-- #main -->
    </div> <!-- #main-container -->
<?php
include_once("models/inc/gl_foot.php");
?>
<script src="assets/js/custom_assessments.js"></script>
<script>
<?php
  $index      = array_search($current_surveyid, $avail_surveys);
  $nextsurvey = $project == "Supp" ? null : (isset($avail_surveys[$index+1]) ? $avail_surveys[$index+1] : null);
  echo "$('#customform').attr('data-next','". $nextsurvey ."');\n\n";

  $isMET    = $sid == "how_fit_are_you"                                     ? "true" : "false";
  $isMAT    = $sid == "how_physically_mobile_are_you"                       ? "true" : "false";
  $isTCM    = $sid == "find_out_your_body_type_according_to_traditional_c"  ? "true" : "false";
  $isGRIT   = $sid == "how_resilient_are_you_to_stress"                     ? "true" : "false";
  $isSleep  = $sid == "how_well_do_you_sleep"                               ? "true" : "false";
  $isIPAQ   = $sid == "international_physical_activity_questionnaire"       ? "true" : "false";
  $isStopBang  = $sid == "stopbang"                                         ? "true" : "false";
  echo "var isMET               = $isMET ;\n";
  echo "var isMAT               = $isMAT ;\n";
  echo "var isTCM               = $isTCM ;\n";
  echo "var isGRIT              = $isGRIT ;\n";
  echo "var isSleep             = $isSleep ;\n";
  echo "var isIPAQ              = $isIPAQ ;\n";
  echo "var isStopBang          = $isStopBang ;\n";
  echo "var uselang             = ".(isset($_SESSION["use_lang"]) ? "'".$_SESSION["use_lang"]."'" : "'en'").";\n";

  //THIS IS A CONFusINg FUNCTION
  //BUT SINCE THERE ARE CONDITiONALS THAT SPAN INSTRUMENTS OR EVEN PROJECTS, GOTTA TRACK EM  ALL
  //THE $all_branching is done in surveys.php
  $branching_function =  "function checkGeneralBranching(){\n";
    foreach($all_branching as $branch){
      $andor      = $branch["andor"];
      $affected   = $branch["affected"];
      $effectors  = array();
      $ef_only    = array();
      if(!empty($branch["effector"])) {
          foreach ($branch["effector"] as $ef => $values) {
              array_push($ef_only, "all_completed.hasOwnProperty('$ef')");

              $temp_arr = array();
              foreach ($values as $value) {
                  $temp_arr[] = " all_completed['$ef'] == $value ";
              }
              $effectors[] = "(" . implode(" || ", $temp_arr) . ")";
          }

          $branching_function .= "if((".implode(" $andor ", $ef_only).") && (".implode(" $andor ", $effectors).")){\n";
          $branching_function .= "\$('.$affected').slideDown('medium');\n";
          $branching_function .= "}else{\n";
          $branching_function .= "\$('.$affected').slideUp('fast');\n";
          $branching_function .= "}\n";
      }
    }

  $branching_function .= "return;\n";
  $branching_function .= "}\n";

  echo $branching_function;

  $all_completed                = array_merge($all_completed, $active_survey->completed);

  // //PASS FORMS METADATA
  echo "var current_section     = " . ($index ? $index : 0) . ";\n";
  echo "var section_perc        = Math.round((1/".count($avail_surveys).")*100);\n";
  echo "var starting_width      = Math.round(current_section * section_perc,2);\n"; 

  echo "var total_questions     = " . $active_survey->surveytotal . ";\n";
  echo "var completed_count     = " . count($active_survey->completed) . ";\n";

  echo "var user_completed      = " . json_encode($active_survey->completed) . ";\n";
  echo "var all_completed       = " . json_encode($all_completed) .";\n";
  echo "var all_branching       = " . json_encode($all_branching).";\n";
  echo "var surveyhash          = '".http_build_query($active_survey->hash)."';\n";
  echo "var form_metadata       = " . json_encode($active_survey->raw) . ";\n";
  echo "var MET_DATA_DISCLAIM   = '".lang("MAT_DATA_DISCLAIM")."';";
  echo "var mat_score_desc = {
           40  : '".lang("MAT_SCORE_40")."'
          ,50  : '".lang("MAT_SCORE_50")."'
          ,60  : '".lang("MAT_SCORE_60")."'
          ,70  : '".lang("MAT_SCORE_70")."'
        };";
    // echo "console.log(".json_encode($all_completed).");";
?>
  //LAUNCH IT INITIALLY TO CHECK IF PAGE HAS BRANCHING
  // checkGeneralBranching();

  //CUSTOM SCORING FOR MET / MAT / TCM SURVEYS
  var mat_map = {
     "mat_walkonground"          : {"vid" : "Flat_NoRail_Slow" , "value" : null } 
    ,"mat_walkonground_fast"     : {"vid" : "Flat_NoRail_Fast" , "value" : null } 
    ,"mat_jogonground"           : {"vid" : "Flat_NoRail_Jog" , "value" : null } 
    ,"mat_walkincline_handrail"  : {"vid" : "Ramp_12Pcnt_Rail_Med" , "value" : null } 
    ,"mat_walkincline"           : {"vid" : "Ramp_12Pcnt_NoRail_Med" , "value" : null } 
    ,"mat_stepover_lowhurdle"    : {"vid" : "Walk_Hurdles_1" , "value" : null } 
    ,"mat_walkincline_tern"      : {"vid" : "Terrain_4" , "value" : null } 
    ,"mat_walkincline_tern_fast" : {"vid" : "Terrain_5" , "value" : null } 
    ,"mat_walkup3_handrail"      : {"vid" : "Stairs_3Step_1Foot_Rail_MedSlo2" , "value" : null } 
    ,"mat_walkdn3"               : {"vid" : "DownStairs_3Step_2Foot_NoRail_Slow" , "value" : null } 
    ,"mat_walkup3_carry"         : {"vid" : "Bag_Stairs_3Step_1Foot_NoRail_2_3" , "value" : null } 
    ,"mat_walkup9_carry"         : {"vid" : "TWObag_stairs_9step_1foot_norail" , "value" : null } 
  };

  var tcm_req = [
     ['tcm_energy','tcm_optimism','tcm_weight','tcm_stool','tcm_loosestool','tcm_stickystool']
    ,['tcm_energy','tcm_voice','tcm_panting','tcm_tranquility','tcm_colds','tcm_pasweat']
    ,['tcm_handsfeet_cold','tcm_cold_aversion','tcm_sensitive_cold','tcm_cold_tolerant','tcm_pain_eatingcold','tcm_sleepwell']
    ,['tcm_handsfeet_hot','tcm_face_hot','tcm_dryskin','tcm_dryeyes','tcm_constipated','tcm_drylips']
    ,['tcm_sleepy','tcm_sweat','tcm_oily_forehead','tcm_eyelid','tcm_snore','tcm_naturalenv']
    ,['tcm_frustrated','tcm_nose','tcm_acne','tcm_bitter','tcm_ribcage','tcm_scrotum']
    ,['tcm_forget','tcm_bruises_skin','tcm_capillary_cheek','tcm_complexion','tcm_darkcircles','tcm_bodyframe']
    ,['tcm_depressed','tcm_anxious','tcm_melancholy','tcm_scared','tcm_suspicious','tcm_breastpain']
    ,['tcm_sneeze','tcm_cough','tcm_allergies','tcm_hives','tcm_skin_red']
  ];

  function isEmpty(v){
    return v == null || v == undefined;
  }

  var breaklength = 6000; //100000 = 10 minutes
  // var takeBreak = setTimeout(SessionExpireEvent, 300000);
  function SessionExpireEvent() {
      var reqmsg  = $("<div>").addClass("required_message alert alert-info").html("<ul><li>We recommend taking a periodic breaks from looking at the computer screen to reduce eye strain and fatigue.  <br>Click 'Close' to continue survey.</li></ul>");
      reqmsg.append($("<button>").addClass("btn btn-alert takebreak").text("Close").click(function(){
        $("section.vbox").removeClass("blur");
        var takeBreak = setTimeout(SessionExpireEvent, 300000);
      }));
      $("body").append(reqmsg);
      $("section.vbox").addClass("blur");
  }

  var sleep_bs = <?php echo $forsleep_complete_only_bs ?>;
  $(document).ready(function(){
    if(sleep_bs){
      $(".alert-success button").remove();
      setTimeout(function(){
        location.href = $("#aftersleep_redirect").attr("href");
      },5000);
    }
  });


  // CUSTOM FLOW FOR UO1 Pilot STUDY
  // $("#core_group_id").on("change",function(){
  //   if(this.value == 1001){
  //   }
  // });
</script>
<script src="assets/js/survey.js"></script>
<style>
div.alert + #outter_rim:before{
  content:"";
  position:absolute;
  width:100%;
  height:100%;
  top:0; left:0;
  background:#333;
  opacity:.3;
  z-index:999;
}
</style>
<?php
