<?php 
require_once("models/config.php"); 
include("models/inc/checklogin.php");

$radar_domains = array(
  "0" => lang("RESOURCE_CREATIVITY"),
  "1" => lang("RESOURCE_LIFESTYLE"),
  "2" => lang("RESOURCE_SOCIAL"),
  "3" => lang("RESOURCE_STRESS"),
  "4" => lang("RESOURCE_EMOTIONS"),
  "5" => lang("RESOURCE_SELF"),
  "6" => lang("RESOURCE_PHYSICAL"),
  "7" => lang("RESOURCE_PURPOSE"),
  "8" => lang("RESOURCE_FINANCIAL"),
  "9" => lang("RESOURCE_RELIGION")
);

//SITE NAV
$navon  = array("home" => "", "reports" => "", "game" => "", "resources" => "on", "rewards" => "", "activity" => "");
$domain_on = array();
for($i = 0; $i < 10; $i++){
  $resource_n = "resources-$i";
  $domain_on[$resource_n] = "";
}
if(isset($_GET["nav"]) && in_array($_GET["nav"],array_keys($domain_on) )){
  $domain_on[$_GET["nav"]] = "on";
}

$API_URL        = SurveysConfig::$projects["ADMIN_CMS"]["URL"];
$API_TOKEN      = SurveysConfig::$projects["ADMIN_CMS"]["TOKEN"];
$extra_params   = array();
$loc            = !isset($_REQUEST["loc"])  ? 1 : 2; //1 US , 2 Taiwan
$cats           = array();
$domain         = isset($_REQUEST["nav"]) && strpos($_REQUEST["nav"],"-") ? str_replace("resources-","",$_REQUEST["nav"]) + 1: 0;

$filterlogic                    = array();
$filterlogic[]                  = "[well_cms_loc] = $loc";
$filterlogic[]                  = "[well_cms_catagory] = 2";
$filterlogic[]                  = "[well_cms_active] = 1";
$filterlogic[]                  = "[well_cms_domain] = $domain";
$extra_params["filterLogic"]    = implode(" and ", $filterlogic);
$events                         = RC::callApi($extra_params, true, $API_URL, $API_TOKEN); 
//is resources
$cats = array();

foreach($events as $event){
    $recordid   = $event["id"];
    $eventpic   = "";
    $file_curl  = RC::callFileApi($recordid, "well_cms_pic", null, $API_URL,$API_TOKEN);
    if(strpos($file_curl["headers"]["content-type"][0],"image") > -1){
      $split    = explode("; ",$file_curl["headers"]["content-type"][0]);
      $mime     = $split[0];
      $split2   = explode('"',$split[1]);
      $imgname  = $split2[1];
      $eventpic = '<img class="event_img" src="data:'.$mime.';base64,' . base64_encode($file_curl["file_body"]) . '">';
    }
    $order = intval($event["well_cms_displayord"]);
    $cats[$order] = array(
         "pic"            => $eventpic
        ,"link"           => $event["well_cms_event_link"] 
        ,"domain"         => $event["well_cms_domain"]
        ,"content"        => $event["well_cms_content"]
        ,"link-text"      => $event["well_cms_subject"]
        ,"image-catagory" => $event["well_cms_image_catagory"]
    );
}
ksort($cats);

// if( then render)
// $default = "?domain=creativity" 
$url = $_SERVER['REQUEST_URI'];
$domain_page = $url[strlen($url)-1];

$pageTitle = "Well v2 Resource Links";
$bodyClass = "resources";
include_once("models/inc/gl_head.php");
?>
<div class="main">
    <div class="domains row">
        <?php
        if(!is_numeric($domain_page)){
            foreach($radar_domains as $key=>$val){
          ?>
            <section class="domain_big col-sm-12">
                <a href = "resources.php?nav=resources-<?php echo $key; ?>">
                  <img src = assets/img/0<?php echo $key;?>-domain.png>
                  <?php echo $radar_domains[$key]; ?>
                </a>
            </section>
        <?php
            }//foreach
        }//if isset
        else{
           include_once("models/inc/gl_domainSideNav.php");
        }
        ?>
    </div>
</div> <!-- #main-container -->
<?php
include_once("models/inc/gl_foot.php");
?>
<style>
li{
  display: block;
}


.domain_big {
    margin-bottom:30px;
}
.domain_big a{
  display:block; 
  text-align:center; 
  color:#333;
}
.domain_big a img {
  display:block;
  margin:0 auto;
  max-width: 72px;
  fmax-height: 
}
</style>
