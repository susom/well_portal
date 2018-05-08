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
}
</style>
