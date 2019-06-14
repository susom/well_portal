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
              $eventpic = "<a href='$file' target='blank' class='col-sm-12 col-md-3'><img class='event_img' src='$file'></a>";
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
  $current_year =  Date("Y");
  $surveyid     = $_GET["survey_complete"];
  array_push($_SESSION["completed_timestamps"],$surveyid);

  //LETS UNSET SOME OF THESE SO THE CACHE WILL REFRESH
  unset($_SESSION["supp_surveys"][$current_year]);
  unset($_SESSION["core_timestamps"]);

  $completed_timestamps = $_SESSION["completed_timestamps"];
  include("models/inc/surveys_data.php");
  $complete_surveys_keys    = array_keys(SurveysConfig::$supp_surveys);
  $final_well_core          = "your_feedback";
  array_push($complete_surveys_keys, $final_well_core);
  if(in_array($surveyid,$complete_surveys_keys)){
      //wtf is this
      $completed_timestamps  = $_SESSION["completed_timestamps"]  = array_merge($_SESSION["completed_timestamps"],$core_instrument_ids);
      $pts_survey_complete = json_decode($game_points["gamify_pts_survey_complete"],1);

      $success_arr = array();
      $success_arr[] = "<div id='confirm_email'>";
      if($surveyid == $final_well_core){
          //CALCULATE WELL SCORES
          if ($core_surveys_complete) {
              // ONLY CALCULATE LONG SCORE DURING LONG YEARS
              $long_score = calculateLongScore($loggedInUser, $loggedInUser->user_event_arm, $_CFG, $all_completed);
          }

          $success_arr[] = $lang["CONGRATS_FRUITS"] . "<br> <span class='earned_points'>You've earned <b>".$pts_survey_complete["value"]."</b> WELL Points!</span> <br><br>";
          if ($loggedInUser->user_event_arm == "enrollment_arm_1" || $loggedInUser->user_event_arm == "") {
              $success_arr[] = $lang["CONGRATS_CERT"];
              $success_arr[] = "<div class='input_group'><input type='text' name='confirm_email' placeholder='Confirm Email'/> ";
              $success_arr[] = "<input type='submit' value='confirm'></div>";
              $success_arr[] = "</div>";
          }
          $success_arr[]  = "<div id='cert_n_score'>";
          //GENERATE CERTIFICATE REAL TIME ONLY ,  NO CACHE
          $filename = "PDF/generatePDFcertificate.php";
          $success_arr[]  = lang("THANKS") ."<br><br>";
          $success_arr[]  = "<a target='blank' href='$filename'>[".lang("CERT_DL")."]</a>";
          $long_score     = empty($long_score) ? "NA" : $long_score;
          // if this is the first one just show the orange ball, otherwise show comparison graph
          $success_arr[]  = "<p>".lang("WELL_SCORE_YEAR", array($current_year, round($long_score,2) ))."</p>";
      }else{
          $success_arr[] = $lang["YOUVE_BEEN_AWARDED"];
          $icon_class    = SurveysConfig::$supp_icons[$surveyid];
          $success_arr[] = "<div class='myrewards $icon_class'></div>";
          $success_arr[] = $lang["FITNESS_BADGE"] . "<br> <span class='earned_points'>You've earned <b>".$pts_survey_complete["value"]."</b> WELL Points!</span> <br><br>";
      }
      $success_arr[]  = "</div>";
      $success_msg      = implode($success_arr);
      addSessionMessage( $success_msg , "success");
  }

  // ONCE PER YEAR COMPLETION POINTS FOR SURVEYS
  if(!in_array($surveyid , $_SESSION["persist_points"])){
    array_push($_SESSION["persist_points"],$surveyid);
    $pt_val           = json_decode($game_points["gamify_pts_survey_complete"],1);
    $persist_pts      = $pt_val["value"];
    $data             = array();
    $data[]           = array(
        "record"            => $loggedInUser->id,
        "field_name"        => "annual_persist_points",
        "value"             => json_encode($_SESSION["persist_points"]),
        "redcap_event_name" => (!empty($loggedInUser->user_event_arm) ? $loggedInUser->user_event_arm : REDCAP_PORTAL_EVENT)
    );
    $result = RC::writeToApi($data, array("overwriteBehavior" => "overwrite", "type" => "eav"), SurveysConfig::$projects["REDCAP_PORTAL"]["URL"], SurveysConfig::$projects["REDCAP_PORTAL"]["TOKEN"]);
    $result = updateGlobalPersistPoints($loggedInUser->id, $persist_pts);

    $js_update_points = $persist_pts;
  }
}
// markPageLoadTime("END HEAD AREA");

if(isset($_POST["confirm_email"])){

    if(isset($_POST["resave"])){
        //first check if there is an email with that name already;
        $newemail = strtolower(trim($_POST["confirm_email"]));
        $check = getUserByEmail($newemail);

        if(!$check){
            $loggedInUser->updateUser(array(
                "portal_username"  => $newemail
            ,"portal_email"    => $newemail
            ));
            $return = array("success" => "dance of joy");
        }else{
            $return = array("error" => "existing");
        }
    }elseif($loggedInUser->email == strtolower(trim($_POST["confirm_email"]))){
        $return = array("success" => "dance of joy");
    }else{
        $return = array("error" => "no match");
    };
    echo json_encode($return);
    exit;
}

if(isset($_POST["mini_clicked"])){
    $mini_clicked = $_POST["mini_clicked"];

    $API_URL    = SurveysConfig::$projects["REDCAP_PORTAL"]["URL"];
    $API_TOKEN  = SurveysConfig::$projects["REDCAP_PORTAL"]["TOKEN"];
    $data   = array();
    $data[] = array(
        "redcap_event_name" => $user_event_arm,
        "record"            => $loggedInUser->id,
        "field_name"        => $mini_clicked,
        "value"             => 1
    );

    $result = RC::writeToApi($data, array("overwriteBehavior" => "overwite", "type" => "eav"), $API_URL , $API_TOKEN);
    echo json_encode($data);
    exit;
}

if(!$core_surveys_complete && !isset($_SESSION["incomplete_core"])){
    $_SESSION["incomplete_core"] = 1;
    header("Location: survey.php?sid=" . SurveysConfig::$core_surveys[0]); //survey link of first survey
    exit;
}

$API_URL            = SurveysConfig::$projects["REDCAP_PORTAL"]["URL"];
$API_TOKEN          = SurveysConfig::$projects["REDCAP_PORTAL"]["TOKEN"];
$extra_params       = array(
     'content'     	=> 'record'
    ,'records'     	=> array($loggedInUser->id)
    ,"fields"       => array("portal_bg")
);
$results   = RC::callApi($extra_params, true, $API_URL, $API_TOKEN);
$portal_bg = "";
if(!empty($results)){
    $result     = current($results);
    if(isset($result["portal_bg"])) {
        $portal_bg =  $result["portal_bg"];
    }
}

$pageTitle = "Well v2 Home Page";
$bodyClass = "home";
$trackpage = "dashboard_home";
include_once("models/inc/gl_head.php");
?>
<div class="main row">
    <?php
        include_once("models/inc/gl_surveynav.php");
    ?>
    <div class="hidden-sm col-md-1"></div>
    <article class="resource_links col-sm-12 col-md-7 ">
        <h3><?php echo lang("ENHANCE_WELLBEING") ?></h3>
        <?php
        // markPageLoadTime("BEGIN CONTENT AREA");
        if(isset($cats[0])){
            $content_html = array();
            foreach($cats[0] as $event){
                $content_html[] = "<section class=''>";
                $content_html[] = "<figure  class='row'>";

                $content_html[] = $event["pic"];

                $content_html[] = "<figcaption class='col-sm-12 col-md-7'>";
                $content_html[] = "<h2>".$event["subject"]."</h2>";
                $content_html[] = "<p>".$event["content"]."</p>";
                if(!empty($event["link"])){
                    if(in_array($event["link"], $supp_instrument_ids )){
                        $content_html[] = "<a class='points_survey' href='survey.php?sid=".$event["link"]."&project=Supp'>".lang("GO_TO_SURVEY")."</a>";
                    }else{
                        $read_more_link = $event["link"] == "wellbeing_questions" ? "survey.php?sid=".$event["link"] : $event["link"];
                        $content_html[] = "<a class='".($event["link"] == "wellbeing_questions" ? "points_survey" : "points_resources")."' href='".$read_more_link."'>".lang("READ_MORE")."</a>";
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
</div>
<?php 
include_once("models/inc/gl_foot.php");
?>
<style>
<?php
$CMS_API_URL    = SurveysConfig::$projects["ADMIN_CMS"]["URL"];
$CMS_API_TOKEN  = SurveysConfig::$projects["ADMIN_CMS"]["TOKEN"];
$extra_params   = array(
    'content'   => 'record',
    'format'    => 'json',
    "fields"    => array("id", "portal_mc_name","portal_mc_year"),
    "filterLogic" => "[portal_mc_name] = '$portal_bg' "
);
$minics = RC::callApi($extra_params, true, $CMS_API_URL, $CMS_API_TOKEN);

foreach($minics as $minic){
    $recordid   = $minic["id"];
    $file_curl  = RC::callFileApi($recordid, "portal_mc_img", null, $CMS_API_URL,$CMS_API_TOKEN);
    if(strpos($file_curl["headers"]["content-type"][0],"image") > -1){
        $eventpic = base64_encode($file_curl["file_body"]);
    }

    echo "body { \r";
    echo "background: url(data:image/gif;base64,$eventpic) 50% 0 no-repeat;\r";
    echo "background-size: cover; \r\r";
    echo "background-attachment: fixed;} \r\r";
    break;
}

if($loggedInUser->user_event_arm == "enrollment_arm_1"  || $loggedInUser->user_event_arm == ""){
?>
#cert_n_score {
    display: none;
}
<?php
}
?>
</style>
<script>
$(document).ready(function(){
    $("#confirm_email input[type='submit']").on("click",function(){
        var og_input    = $("#confirm_email input[name='confirm_email']");
        var resave      = "";
        if($("input[name='confirm_email_b']").length){
            if($("input[name='confirm_email_b']").val() !== og_input.val()){
                alert("These emails don't match");
                $("input[name='confirm_email_b']").val("");
                return false;
            }else{
                resave = "&resave=true";
            }
        }

        var dataurl = "&confirm_email=" + og_input.val() + resave;
        $.ajax({
            url:  "index.php",
            type:'POST',
            dataType: "JSON",
            data: dataurl,
            success:function(result){
                console.log(result);
                if(result.hasOwnProperty("success")){
                    $("#confirm_email").slideUp("medium");
                    $("#cert_n_score").slideDown("slow");
                }else{
                    if(result.error == "existing"){
                        alert("That email is already taken.");
                        $("input[name='confirm_email']").val("");
                        $("input[name='confirm_email_b']").val("");
                    }else{
                        og_input.after(function(){
                            return $(this).clone().val("").attr("name","confirm_email_b").attr("placeholder", "Re-Confirm Email");
                        }).css("display","block");
                    }
                }
            }
        });
    });
});
</script>
<?php
// markPageLoadTime("end page load");
