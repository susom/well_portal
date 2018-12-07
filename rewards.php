<?php 
require_once("models/config.php"); 
include("models/inc/checklogin.php");

//SITE NAV
$navon              = array("home" => "", "reports" => "", "game" => "", "resources" => "", "rewards" => "on", "activity" => "");

$API_URL            = SurveysConfig::$projects["ADMIN_CMS"]["URL"];
$API_TOKEN          = SurveysConfig::$projects["ADMIN_CMS"]["TOKEN"];
$extra_params       = array();
$loc                = !isset($_REQUEST["loc"])  ? 1 : 2; //1 US , 2 Taiwan
$cats               = array();
$domain             = isset($_REQUEST["nav"])  ? str_replace("resources-","",$_REQUEST["nav"]) + 1: 0;


$core_icons         = SurveysConfig::$core_icons;
$supp_icons         = SurveysConfig::$supp_icons;
$completed_surveys  = array();
$incomplete_surveys = array();
foreach($completed_timestamps as $completed_sid){
    $completed_surveys[] = "<a href='#' class='".$core_icons[$completed_sid]." draggable' title='$completed_sid'></a>";
}

$url                = $_SERVER['REQUEST_URI'];
$domain_page        = $url[strlen($url)-1];

$pageTitle          = "Well v2 Resource Links";
$bodyClass          = "rewards";
include_once("models/inc/gl_head.php");
?>
    <div class="main-container">
        <div class="main wrapper clearfix">
          <div class="tree droppable">
            <div class='rewards complete'>
              <?php
                echo implode(" ",$completed_surveys);
              ?>
              <blockquote><b>To claim your badges:</b> Drag your rewarded badges to your WELL tree.</blockquote>
            </div>
            
            <div class="rewards incomplete">
              <?php
                echo implode(" ",$incomplete_surveys);
              ?>
              <blockquote><b>To get more badges:</b> Fill out more WELL surveys or join WELL challenges.</blockquote>
            </div>
            <h3 id="mywelltree"><?php echo lang("MYWELLTREE") ?></h3>
          </div>
        </div> <!-- #main -->
    </div> <!-- #main-container -->
<?php 
include_once("models/inc/gl_foot.php");
?>
<style>
.tree {
  width:1024px;
  height:760px;
  margin:0 auto;
  background: url(assets/img/well_tree_big.png) 20% 50% no-repeat;
    background-size:50%;
  position:relative;
}
.tree #mywelltree{
  position:absolute;
  bottom:-20px;
  left:34%;
  width:300px;
  margin-left:-150px;
  text-align:center;
  color:#333;
}

.tree .rewards{
  width:200px;
  float:right;
  clear:right;
  margin:30px 20px 0 0;

}
.tree .rewards blockquote{
  border:1px solid #ccc;
  border-radius:5px;
  padding:10px;
  margin:10px 0;
}

.tree a{
  width:30px;
  height:30px;
  display:inline-block;
  background:url(assets/img/sprites_fruits.png) 0px -123px no-repeat;
  background-size:220%;
}

.tree .rewards a.strawberry{
    /*transform: translateY(-50%);*/
    /*content:"";*/
}

.tree .rewards a.grapes{
    background-position:0 0;
}
.tree .rewards a.watermelon{
    background-position:0 -185px;
}
.tree .rewards a.peach{
    background-position:0 -93px;
}
.tree .rewards a.bananas{
    background-position:0 -365px;
}
.tree .rewards a.raspberry{
    background-position:0 -275px;
}
.tree .rewards a.greenapple{
    background-position:0 -305px;
}
.tree .rewards a.pear{
    background-position:0 -398px;
}
.tree .rewards a.cherries{
    background-position:0 -517px;
}
.tree .rewards a.plum{
    background-position:0 -335px;
}
.tree .rewards a.pomegranate{
    background-position:0 -33px;
}
.tree .rewards a.mango{
    background-position:0 -215px;
}
.tree .rewards a.redapple{
    background-position:0 -459px;
}
.tree .rewards a.ranier{
    background-position:0 -428px;
}
.tree .rewards a.orange{
    background-position:0 -154px;
}
.tree .rewards a.apricot{
    background-position:0 -62px;
}
.tree .rewards a.lime{
    background-position:0 -245px;
}
.tree .rewards a.lemon{
    background-position:0 -489px;
}

.tree .rewards a.running{
    background:url(assets/img/ico_mat.png) top left no-repeat;
    background-size:200%;
}
.tree .rewards a.biking{
    background:url(assets/img/ico_met.png) top left no-repeat;
    background-size:200%;
}
.tree .rewards a.weightlifting{
    background:url(assets/img/ico_stress.png) top left no-repeat;
    background-size:200%;
}
.tree .rewards a.cardio{
    background:url(assets/img/ico_sleep.png) top left  no-repeat;
    background-size:200%;
}
.tree .rewards a.tbone{
    background:url(assets/img/ico_chinesemed.png) top left  no-repeat;
    background-size:200%;
}
.tree .rewards a.carrot{
    background:url(assets/img/ico_ipaq.png) top left  no-repeat;
    background-size:200%;
}
.tree .rewards a.snoring{
    background:url(assets/img/ico_snoring.png) top left  no-repeat;
    background-size:200%;
}

.tree .rewards a.na:before{
    background-position:top right;
}
</style>
<script>
$(document).ready(function(){
    $(".draggable").draggable();
    $(".droppable").droppable();
});
</script>