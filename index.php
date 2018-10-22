<?php 
require_once("models/config.php"); 
include("models/inc/checklogin.php");
include("models/inc/scoring_functions.php");

// GLOBAL NAV SET STATE
$navon  = array("home" => "on", "reports" => "", "game" => "", "resources" => "", "rewards" => "", "activity" => "");

// markPageLoadTime("BEGIN HEAD AREA");
$avail_surveys      = $core_instrument_ids;
$first_core_survey  = array_splice($avail_surveys,0,1);
$surveyon           = array();
$surveynav          = array_merge($first_core_survey, $supp_instrument_ids);
foreach($surveynav as $surveyitem){
    $surveyon[$surveyitem] = "";
}

$API_URL        = SurveysConfig::$projects["ADMIN_CMS"]["URL"];
$API_TOKEN      = SurveysConfig::$projects["ADMIN_CMS"]["TOKEN"];
$extra_params   = array();
$loc            = !isset($_REQUEST["loc"])  ? 1 : 2; //1 US , 2 Taiwan
$cats           = array(0,1);
$languages = array(
  "en" => 1,
  "sp" => 2,
  "cn" => 3,
  "tw" => 4,
);

// LOAD THE CMS EDITORIAL CONTENT FOR THE HOME PAGE INTO SESSION FOR 15 MINUTES
if(isset($_SESSION['LAST_CMS_LOAD'])) {
  if((time() - $_SESSION['LAST_CMS_LOAD'] > 900)){
    $_SESSION['LAST_CMS_LOAD'] = time();
    unset($_SESSION['monthly_goals']);
    unset($_SESSION['editorial_events']);
  }
}else{
  $_SESSION['LAST_CMS_LOAD'] = time();
}
foreach($cats as $cat){
    $filterlogic                    = array();
    $filterlogic[]                  = '[well_cms_loc] = "'.$loc.'"';
    $filterlogic[]                  = '[well_cms_catagory] = "'.$cat.'"';
    $filterlogic[]                  = '[well_cms_active] = "1"';
    $filterlogic[]                  = '[well_cms_lang] = "'.$languages[isset($_SESSION["use_lang"]) ? $_SESSION["use_lang"] : "en"].'"';
    $extra_params["filterLogic"]    = implode(" and ", $filterlogic);
    
    if($cat == 0 ){
      // EVENTS RESOURCES
      if(isset($_SESSION['editorial_events'])){
        $cats[0]  = $_SESSION['editorial_events'];
      }else{
        $events   = RC::callApi($extra_params, true, $API_URL, $API_TOKEN); 
        $cats[0]  = array();
        foreach($events as $event){
            $recordid   = $event["id"];
            $eventpic   = "";
            $file_curl  = RC::callFileApi($recordid, "well_cms_pic", null, $API_URL,$API_TOKEN);
            if(strpos($file_curl["headers"]["content-type"][0],"image") > -1){
              $split    = explode("; ",$file_curl["headers"]["content-type"][0]);
              $mime     = $split[0];
              $split2   = explode('"',$split[1]);
              $imgname  = $split2[1];
              
              $file     = "temp/" . $imgname;
              if(!file_exists($file)){
                file_put_contents($file, $file_curl["file_body"]);
              }
              $eventpic = "<a href='$file' target='blank'><img class='event_img' src='$file'></a>";
            }

            $order = intval($event["well_cms_displayord"]) - 1;
            if($order == 0 && $core_surveys_complete){
                //first event is only for core survey incomplete people
                continue;
            }
            $cats[0][$order] = array(
                 "subject"  => $event["well_cms_subject"] 
                ,"content"  => $event["well_cms_content"] 
                ,"pic"      => $eventpic
                ,"link"     => $event["well_cms_event_link"] 
            );
        }
        ksort($cats[0]);
        $_SESSION['editorial_events'] = $cats[0];
      }
    }

    if($cat == 1){
      // MONTHLY GOALS
      if(isset($_SESSION['monthly_goals'])){
        $cats[1]    = $_SESSION['monthly_goals'];
      }else{
        $events     = RC::callApi($extra_params, true, $API_URL, $API_TOKEN); 
        if(!empty($events)){
          $recordid   = $events[0]["id"];
          $eventpic   = "";
          $file_curl  = RC::callFileApi($recordid, "well_cms_pic", null, $API_URL,$API_TOKEN);
          if(strpos($file_curl["headers"]["content-type"][0],"image") > -1){
            $split    = explode("; ",$file_curl["headers"]["content-type"][0]);
            $mime     = $split[0];
            $split2   = explode('"',$split[1]);
            $imgname  = $split2[1];
            $eventpic = "data:".$mime.";base64,". base64_encode($file_curl["file_body"]);
          }
          $cats[1] = array(
               "subject"  => $events[0]["well_cms_subject"] 
              ,"content"  => $events[0]["well_cms_content"] 
              ,"pic"      => $eventpic 
          );
        }
        $_SESSION['monthly_goals'] = $cats[1];
      }
    }
}

//print_rr($_SESSION["supp_surveys"][$thisyear]);
//NEEDS TO GO BELOW SHORTSCALE WORK FOR NOW
if(isset($_GET["survey_complete"])){
  //IF NO URL PASSED IN THEN REDIRECT BACK
  $surveyid = $_GET["survey_complete"];
  array_push($_SESSION["completed_timestamps"],$surveyid);
  $thisyear =  Date("Y");
  unset($_SESSION["supp_surveys"][$thisyear]);
  $completed_timestamps = $_SESSION["completed_timestamps"];
  
  $armyears       = getSessionEventYears();
  $current_year   = end($armyears);
  include("models/inc/surveys_data.php");
  if(array_key_exists($surveyid,$surveys)){
    $index  = array_search($surveyid, $all_survey_keys);
    $survey = $surveys[$surveyid];
    if(!isset($all_survey_keys[$index+1])){ 
      //CALCULATE WELL SCORES
      if($core_surveys_complete){
        // CaLCULATE SHORT SCORE FOR LONG AND SHORT YEARS, SAVE TO the well_score sum variable but in the correct ARM
        $short_score  = calculateShortScore($loggedInUser, $user_event_arm, $_CFG, $user_survey_data);

        // ONLY CALCULATE LONG SCORE DURING LONG YEARS
        if(!$user_short_scale){
          $long_score = calculateLongScore($loggedInUser, $user_event_arm, $_CFG, $all_completed);
        }
      }

      //TODO ONLY  USE BREIF SCALE CALCULATION from "well_score"  
      $success_arr    = array();
      $success_arr[]  = $lang["CONGRATS_FRUITS"];
      
      // will pass $current_year into the include
      $armyears       = getSessionEventYears();
      $current_year   = end($armyears);

      //GENERATE CERTIFICATE REAL TIME ONLY ,  NO CACHE
      $filename = "PDF/generatePDFcertificate.php";
      $success_arr[]  = "<a target='blank' href='$filename'>[".lang("CERT_DL")."]</a>";

      if(!$user_short_scale){
        $completed_timestamps   = $_SESSION["completed_timestamps"]  = array_merge($_SESSION["completed_timestamps"],$core_instrument_ids);

        $long_score     = empty($long_score) ? "NA" : $long_score;
        // if this is the first one just show the orange ball, otherwise show comparison graph
        $success_arr[]  = "<p>".lang("WELL_SCORE_YEAR", array($current_year, round($long_score,2) ))."</p>";
      }else{
        $extra_params = array(
          'content'     => 'record',
          'records'     => array($loggedInUser->id) ,
          'fields'      => array("id","well_score")
        );
        $user_ws        = RC::callApi($extra_params, true, $_CFG->REDCAP_API_URL, $_CFG->REDCAP_API_TOKEN); 
        $user_scores    = array();
        foreach($user_ws as $arm_score){
          $user_scores[$arm_score["redcap_event_name"]]  = array("year" => $armyears[$arm_score["redcap_event_name"]], "well_score" => $arm_score["well_score"]);
        }
        $success_arr[]  = printWELLOverTime($user_scores);
      }


       // CUSTOM FLOW FOR UO1 Pilot STUDY
      if(isset($all_completed["core_group_id"]) && $all_completed["core_group_id"] == 1001){
        $success_arr[]  = "<p class='alert_reminder'>".lang("UO1_REMINDER")."</p>";
      }else{
       // $success_arr[]   = "<hr/><p class='organize'><a href='activity.php' target='blank'>".lang("DOMAIN_RANKING_PROMPT")."</a></p><hr/>";
      }

      $success_msg      = implode($success_arr);
      addSessionMessage( $success_msg , "success");
    }
  }
}
// markPageLoadTime("END HEAD AREA");

$pageTitle = "Well v2 Home Page";
$bodyClass = "home";
$trackpage = "dashboard_home";
include_once("models/inc/gl_head.php");
?>
    <div class="main-container">
        <div class="main wrapper clearfix">
            <article>
                <h3><?php echo lang("ENHANCE_WELLBEING") ?></h3>
                <?php  
                // markPageLoadTime("BEGIN CONTENT AREA");
                if(isset($cats[0])){
                    $content_html = array();
                    foreach($cats[0] as $event){
                      $content_html[] = "<section>";
                      $content_html[] = "<figure>";
                      
                      $content_html[] = $event["pic"];

                      $content_html[] = "<figcaption>";
                      $content_html[] = "<h2>".$event["subject"]."</h2>";
                      $content_html[] = "<p>".$event["content"]."</p>";
                      if(!empty($event["link"])){
                        if(in_array($event["link"], $supp_instrument_ids )){
                          $content_html[] = "<a href='survey.php?sid=".$event["link"]."&project=Supp'>".lang("GO_TO_SURVEY")."</a>";
                        }else{
                          $read_more_link = $event["link"] == "wellbeing_questions" ? "survey.php?sid=".$event["link"] : $event["link"];
                          $content_html[] = "<a href='".$read_more_link."'>".lang("READ_MORE")."</a>";
                        }
                      }
                      $content_html[] = "</figcaption>";
                      $content_html[] = "</figure>";
                      $content_html[] = "</section>";
                    }
                    echo implode("\r\n",$content_html);
                }
                // markPageLoadTime("END CONTENT AREA");
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
<style>
.well_scores{
  margin:20px 0 20px;
  text-align:left;
}
.well_scores .anchor {
  border-top:3px dashed #ccc;
  color:#8a6d3b;
  font-weight:bold;
  padding-top:5px;
  position:relative;
}
.well_scores .anchor:after{
  position: absolute;
  content: "";
  top: -12px;
  right: -2px;
  width: 0;
  height: 0;
  border-top: 10px solid transparent;
  border-bottom: 10px solid transparent;
  border-left: 10px solid #ccc;
}
.well_scores .hundred{
  float:right;
}
.well_scores .fifty{
  position:absolute;
  left:50%;
  top:5px;
}
.well_score{
  margin-bottom:10px;
  height:30px;
  background:#efefef;
}
.well_score b{
  display:inline-block; 
  vertical-align:middle;
  position: absolute;
}
.well_score span {
  display:inline-block;
  height:30px;
  vertical-align:middle;
  margin-right:10px;
  min-width:46px;
}

.well_score span i {
  font-style: normal;
  font-weight:bold;
  font-size:120%;
  color:#fff;
  line-height: 160%;
  margin-left: 5px;
  display: inline-block;
}

.user_score span{
  background:#0BA5A3;
  box-shadow:0 0 5px #28D1D8;
}
.user_score.yearx span{
  background:#FEC83B;
  box-shadow:0 0 5px #9ABC46;
}
.user_score.yearxx span{
  background:#126C97;
  box-shadow:0 0 5px #9ABC46;
}
.user_score.yearxxx span{
  background:#E02141;
  box-shadow:0 0 5px #9ABC46;
}
.user_score.yearxxxx span{
  background:#328443;
  box-shadow:0 0 5px #9ABC46;
}

.other_score span{
  background:#FEC83B;
  box-shadow:0 0 5px #9ABC46;
}

.alert.text-center ul {
  margin:20px 40px 20px;
}

.alert_reminder{
      background: #ffc;
    border-radius: 5px;
    padding: 10px 5px;
    line-height: 120%;
    font-size: 24px;
    color: darkorange;
    font-weight: normal;
}
</style>
<?php
// markPageLoadTime("end page load");
