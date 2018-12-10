<?php 
require_once("models/config.php"); 
include("models/inc/checklogin.php");

//SITE NAV
$navon              = array("home" => "", "reports" => "", "game" => "", "resources" => "", "rewards" => "on", "activity" => "");

//GET THE CURRENT POSITION FOR THE FRUIT FROM THIS ARM
$API_URL            = SurveysConfig::$projects["REDCAP_PORTAL"]["URL"];
$API_TOKEN          = SurveysConfig::$projects["REDCAP_PORTAL"]["TOKEN"];
$extra_params       = array(
    'content'     	=> 'record'
    ,'records'     	=> array($loggedInUser->id)
    ,"fields"       => array("reward_tree_positions")
    ,"events"       => $user_event_arm
);
$events             = RC::callApi($extra_params, true, $API_URL, $API_TOKEN);
$event              = current($events);
$fruit_positions    = !empty($event["reward_tree_positions"]) ? json_decode($event["reward_tree_positions"],1) : false;

//GET THE ICONS FOR THE FRUItS REPRESENTING THE SURVEYS
$core_icons         = SurveysConfig::$core_icons;
$supp_icons         = SurveysConfig::$supp_icons;
$completed_surveys  = array();
$incomplete_surveys = array();
foreach($completed_timestamps as $completed_sid){
    $completed_surveys[] = "<a href='#' class='".$core_icons[$completed_sid]." draggable' title='$completed_sid' data-sid='$completed_sid' data-fruit='".$core_icons[$completed_sid]."'></a>";
}

$url                = $_SERVER['REQUEST_URI'];
$domain_page        = $url[strlen($url)-1];

$pageTitle          = "Well v2 Resource Links";
$bodyClass          = "rewards";
include_once("models/inc/gl_head.php");
?>
<div class="main-container">
    <div class="main wrapper clearfix">
            <div class="mytree droppable">
                <h3 id="mywelltree"><?php echo lang("MYWELLTREE") ?></h3>

                <span id="mytree_leaves"></span>
                <span id="mytree_berries"></span>
            </div>

            <div class='myrewards complete'>
                <?php
                echo implode(" ",$completed_surveys);
                ?>
                <blockquote><b>To claim your badges:</b> Drag your rewarded badges to your WELL tree.</blockquote>
            </div>

            <div class="myrewards incomplete">
                <?php
                echo implode(" ",$incomplete_surveys);
                ?>
                <blockquote><b>To get more badges:</b> Fill out more WELL surveys or join WELL challenges.</blockquote>
            </div>
    </div> <!-- #main -->
</div> <!-- #main-container -->
<?php 
include_once("models/inc/gl_foot.php");
?>
<style>
.mytree {
  float:left;
  width:600px; height:800px;
  margin:0 0 0 40px;
  background: url(assets/img/well_tree_big_baretree.png) 0 0 no-repeat;
  background-size:contain;
  position:relative;
}

#mytree_leaves{
    content:" ";
    position:absolute;
    top:0; left:0;
    width:600px; height:800px;
    z-index:-2;
    background: url(assets/img/well_tree_big_justleaves.png) 0 0 no-repeat;
    background-size:contain;
    opacity:.1;
}
#mytree_berries{
    content:" ";
    position:absolute;
    top:0; left:0;
    width:600px; height:800px;
    z-index:-1;
    background: url(assets/img/well_tree_big_justberries.png) 0 0 no-repeat;
    background-size:contain;
    opacity:.1;
}
#mywelltree{
  position:absolute;
  bottom:-60px;
  width:100%;
  text-align:center;
  color:#333;
}

.myrewards{
  width:320px;
  float:right;
  clear:right;
  margin:30px 20px 0 0;
  border:1px solid #ccc;
  border-radius:5px;
  padding:10px;
}
.myrewards blockquote{
  margin:10px 0;
  border:none;
}

.myrewards a{
  width:60px;
  height:60px;
  display:inline-block;
  background:url(assets/img/sprites_fruits.png) 0px -246px no-repeat;
  background-size:220%;
}

.rewards a.strawberry{
    /*transform: translateY(-50%);*/
    /*content:"";*/
}

.rewards a.grapes{
    background-position:0 0;
}
.rewards a.watermelon{
    background-position:0 -370px;
}
.rewards a.peach{
    background-position:0 -186px;
}
.rewards a.bananas{
    background-position:0 -730px;
}
.rewards a.raspberry{
    background-position:0 -550px;
}
.rewards a.greenapple{
    background-position:0 -610px;
}
.rewards a.pear{
    background-position:0 -796px;
}
.rewards a.cherries{
    background-position:0 -1014px;
}
.rewards a.plum{
    background-position:0 -670px;
}
.rewards a.pomegranate{
    background-position:0 -66px;
}
.rewards a.mango{
    background-position:0 -430px;
}
.rewards a.redapple{
    background-position:0 -918px;
}
.rewards a.ranier{
    background-position:0 -856px;
}
.rewards a.orange{
    background-position:0 -308px;
}
.rewards a.apricot{
    background-position:0 -124px;
}
.rewards a.lime{
    background-position:0 -490px;
}
.rewards a.lemon{
    background-position:0 -978px;
}

.rewards a.running{
    background:url(assets/img/ico_mat.png) top left no-repeat;
    background-size:200%;
}
.rewards a.biking{
    background:url(assets/img/ico_met.png) top left no-repeat;
    background-size:200%;
}
.rewards a.weightlifting{
    background:url(assets/img/ico_stress.png) top left no-repeat;
    background-size:200%;
}
.rewards a.cardio{
    background:url(assets/img/ico_sleep.png) top left  no-repeat;
    background-size:200%;
}
.rewards a.tbone{
    background:url(assets/img/ico_chinesemed.png) top left  no-repeat;
    background-size:200%;
}
.rewards a.carrot{
    background:url(assets/img/ico_ipaq.png) top left  no-repeat;
    background-size:200%;
}
.rewards a.snoring{
    background:url(assets/img/ico_snoring.png) top left  no-repeat;
    background-size:200%;
}

.rewards a.na:before{
    background-position:top right;
}
</style>
<script>
$(document).ready(function(){
    $(".draggable").draggable(function(){
        console.log("dragggable!");
    });

    $(".droppable").droppable({
         accept : ".draggable"
        ,drop   : function( event, ui ) {
            console.log(event);
            console.log(ui);
            // PlaySound("assets/sounds/reverse_bass_flip.flac");
            // PlaySound("assets/sounds/plunger_pop_1.wav");
            PlaySound("assets/sounds/plunger_pop_2.wav");
            // PlaySound("assets/sounds/bubble_small.wav");

            var leaves_op   = parseFloat($("#mytree_leaves").css("opacity")  );
            var berries_op  = parseFloat($("#mytree_berries").css("opacity") );

            leaves_op += .1;
            berries_op += .3;

            $("#mytree_leaves").css("opacity", leaves_op);
            $("#mytree_berries").css("opacity", berries_op);
        }
    });
});

function PlaySound(melody) {
    if(!window.musico){
        window.musico = document.createElement("audio");
    }
    window.musico.setAttribute("src", melody);
    window.musico.play();
}
</script>