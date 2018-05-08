<?php 
require_once("models/config.php"); 
include("models/inc/checklogin.php");

//SITE NAV
$navon  = array("home" => "", "reports" => "", "game" => "", "resources" => "", "rewards" => "on");

$avail_surveys      = $available_instruments;
$first_core_survey  = array_splice($avail_surveys,0,1);
$surveyon           = array();
$surveynav          = array_merge($first_core_survey, $supp_surveys_keys);
foreach($surveynav as $surveyitem){
    $surveyon[$surveyitem] = "";
}

$API_URL        = SurveysConfig::$projects["ADMIN_CMS"]["URL"];
$API_TOKEN      = SurveysConfig::$projects["ADMIN_CMS"]["TOKEN"];
$extra_params   = array();
$loc            = !isset($_REQUEST["loc"])  ? 1 : 2; //1 US , 2 Taiwan
$cats           = array();
$domain         = isset($_REQUEST["nav"])  ? str_replace("resources-","",$_REQUEST["nav"]) + 1: 0;


$url = $_SERVER['REQUEST_URI'];
$domain_page = $url[strlen($url)-1];

$pageTitle = "Well v2 Resource Links";
$bodyClass = "rewards";
include_once("models/inc/gl_head.php");
?>
    <div class="main-container">
        <div class="main wrapper clearfix">
          <div class="tree">
<?php  
if(in_array($umbrella_sid, $available_instruments)  || $user_short_scale){
   // if we are on survey page for supplemental survey , that means core surveys are complete. 
  if(!empty($pid) && array_key_exists($pid, SurveysConfig::$projects)){
    $surveycomplete = true;
    $hreflink       = "href";
    $surveylink     = "survey.php?sid=wellbeing_questions";
  }
  $incomplete_complete = $surveycomplete ? "completed_surveys" : "core_surveys";
  array_push($$incomplete_complete, "<li class='".$surveyon[$umbrella_sid]."  $iconcss'>
      <a $hreflink='$surveylink' class='auto' title='".$survey["label"]."'>
        $newbadge                                                   
        <span class='fruit $completeclass'></span>
        <span class='survey_name'>$surveyname</span>     
      </a>
    </li>\n");
}
echo implode("",$core_surveys);

// CUSTOM FLOW FOR UO1 Pilot STUDY
$uo1 = array(  "how_well_do_you_sleep"
              ,"find_out_your_body_type_according_to_chinese_medic");
$uo1_completes = array();
foreach($uo1 as $supp_instrument_id){
  if(isset($supp_instruments[$supp_instrument_id]) && $supp_instruments[$supp_instrument_id]["survey_complete"]){
    $uo1_completes[] = 1;
  }
}
$uo1_complete = array_sum($uo1_completes) == 2 ? true : false;

$fitness    = SurveysConfig::$supp_icons;
foreach($supp_instruments as $supp_instrument_id => $supp_instrument){
    //if bucket is A make sure that three other ones are complete before showing.
    $projnotes    = json_decode($supp_instrument["project_notes"],1);
    $title_trans  = $projnotes["translations"];
    $tooltips     = $projnotes["tooltips"];
    $surveyname   = isset($title_trans[$_SESSION["use_lang"]][$supp_instrument_id]) ?  $title_trans[$_SESSION["use_lang"]][$supp_instrument_id] : $supp_instrument["label"];
    $iconcss      = $fitness[$supp_instrument_id];
    
    // CUSTOM FLOW FOR UO1 Pilot STUDY
    if($core_surveys_complete && isset($all_completed["core_group_id"]) && $all_completed["core_group_id"] == 1001){
      $custom_flow = true;
      if($uo1_complete){
        $custom_flow = false; 
      }else{
        if(in_array($supp_instrument_id,$uo1)){
          $custom_flow = false; 
        }
      }
    }else{
      $custom_flow = false;
    }

    $titletext    = $core_surveys_complete && !$custom_flow ? $tooltips[$supp_instrument_id] : $lang["COMPLETE_CORE_FIRST"];
    $surveylink   = $core_surveys_complete && !$custom_flow ? "survey.php?sid=". $supp_instrument_id. "&project=" . $supp_instrument["project"] : "#";
    $na           = $core_surveys_complete && !$custom_flow ? "" : "na"; //"na"

    $icon_update  = " icon_update";
    $survey_alinks[$supp_instrument_id] = "<a href='$surveylink' title='$titletext'>$surveyname</a>";
    
    $incomplete_complete = $supp_instrument["survey_complete"] ? "completed_surveys" : "suppsurvs";

    if(!empty($na)){
      array_push($$incomplete_complete,  "<li class='fitness $na $icon_update $iconcss  ".$surveyon[$supp_instrument_id]."'>
                        ".$survey_alinks[$supp_instrument_id]." 
                    </li>");
    }else{
      array_unshift($$incomplete_complete,  "<li class='fitness $na $icon_update $iconcss  ".$surveyon[$supp_instrument_id]."'>
                        ".$survey_alinks[$supp_instrument_id]." 
                    </li>");
    }
}

$proj_name  = "foodquestions";
if(!$core_surveys_complete){
  // JUST PUSH DUMMY TEXT
  $a_nutrilink = "<a href='#' class='nutrilink' title='".$lang["TAKE_BLOCK_DIET"]."' target='_blank'>".$lang["HOW_WELL_EAT"]."</a>"; // &#128150 
  array_unshift($suppsurvs ,"<li class='fitness na food'>".$a_nutrilink."</li>");
}else{
  // THIS IS EXPENSIVE OPERATION, DONT DO IT EVERYTIME, AND DONT BOTHER UNLESS CORE SURVEY IS COMPLETE
  if(isset($_SESSION[$proj_name])){
    $ffq = $_SESSION[$proj_name];
  }else{
    $ffq_project  = new PreGenAccounts($loggedInUser, $proj_name, SurveysConfig::$projects[$proj_name]["URL"], SurveysConfig::$projects[$proj_name]["TOKEN"]);
    $_SESSION[$proj_name] = $ffq = $ffq_project->getAccount();
  }
  if(!array_key_exists("error",$ffq)){
    $nutrilink      = $portal_test ? "#" : "https://www.nutritionquest.com/login/index.php?username=".$ffq["ffq_username"]."&password=".$ffq["ffq_password"]."&BDDSgroup_id=747&Submit=Submit";
    $a_nutrilink    = "<a href='$nutrilink' class='nutrilink' title='".$lang["TAKE_BLOCK_DIET"]."' target='_blank'>".$lang["HOW_WELL_EAT"]."</a>"; // &#128150 
    if($_SESSION["use_lang"] !== "sp"){
      array_unshift($suppsurvs ,"<li class='fitness food'>".$a_nutrilink."</li>");
    }
  }
}
echo implode("",$suppsurvs);


echo implode("",$completed_surveys);
?>
          </div>
        </div> <!-- #main -->
    </div> <!-- #main-container -->

<style>
.tree {
  width:800px;
  height:800px;
  margin:0 auto;
  background:url(assets/img/rewards_tree.png) 50% no-repeat;
  border:1px solid red;
}

</style>
