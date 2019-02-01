<?php 
require_once("models/config.php"); 
include("models/inc/checklogin.php");

//SITE NAV
$navon              = array("home" => "", "reports" => "", "game" => "", "resources" => "", "rewards" => "on", "activity" => "");

$url                = $_SERVER['REQUEST_URI'];
$domain_page        = $url[strlen($url)-1];

if(isset($_POST["mini_clicked"])){
    $mini_clicked   = $_POST["mini_clicked"];
    $API_URL        = SurveysConfig::$projects["REDCAP_PORTAL"]["URL"];
    $API_TOKEN      = SurveysConfig::$projects["REDCAP_PORTAL"]["TOKEN"];
    $data   = array();
    $data[] = array(
        "record"            => $loggedInUser->id,
        "field_name"        => "portal_bg",
        "value"             => $mini_clicked
    );
    $result = RC::writeToApi($data, array("overwriteBehavior" => "overwite", "type" => "eav"), $API_URL , $API_TOKEN);
    echo json_encode($data);
    exit;
}

$API_URL            = SurveysConfig::$projects["REDCAP_PORTAL"]["URL"];
$API_TOKEN          = SurveysConfig::$projects["REDCAP_PORTAL"]["TOKEN"];

//ACTIVE BG
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
        $portal_bg = $result["portal_bg"];
    }
}

//COMPLETED MINI CHALLENGES
$mini_rewards       = array(
     "mini_bettersleep_2016"                => "forest"
    ,"mini_creativity_2016"                 => "forest_river"
    ,"mini_getoutside_2016"                => "halloween"
    ,"mini_mindfulspending_2016"            => "mountain_lake"
    ,"mini_mindbodyspirit_2016"             => "sand_dune"
    ,"mini_movemoresitless_2016"            => "space"
    ,"mini_purposemeaning_2016"             => "spring"
    ,"mini_socialconnectedness_2016"        => "summer_village"
    ,"mini_bettersleep_2017"                => "treasure_chest"
    ,"mini_creativity_2017"                 => "tropical"
    ,"mini_getoutside_2017"                => "winter"
    ,"mini_mindfulspending_2017"            => "winter_2"
    ,"mini_mindbodyspirit_2017"             => "forest"
    ,"mini_movemoresitless_2017"            => "forest_river"
    ,"mini_purposemeaning_2017"             => "halloween"
    ,"mini_socialconnectedness_2017"        => "mountain_lake"
    ,"mini_bettersleep_2018"                => "sand_dune"
    ,"mini_creativity_2018"                 => "space"
    ,"mini_getoutside_2018"                => "spring"
    ,"mini_mindfulspending_2018"            => "summer_village"
    ,"mini_mindbodyspirit_2018"             => "treasure_chest"
    ,"mini_movemoresitless_2018"            => "tropical"
    ,"mini_purposemeaning_2018"             => "forest"
    ,"mini_socialconnectedness_2018"        => "forest_river"
    ,"mini_bettersleep_2019"                => "halloween"
    ,"mini_creativity_2019"                 => "mountain_lake"
    ,"mini_getoutside_2019"                => "sand_dune"
    ,"mini_mindfulspending_2019"            => "space"
    ,"mini_mindbodyspirit_2019"             => "spring"
    ,"mini_movemoresitless_2019"            => "summer_village"
    ,"mini_purposemeaning_2019"             => "treasure_chest"
    ,"mini_socialconnectedness_2019"        => "tropical"
);
$mini_formatted = array(
     "mini_bettersleep"                 => "Better Sleep"
    ,"mini_creativity"                  => "Creativity"
    ,"mini_getoutside"                  => "Get Outside"
    ,"mini_mindfulspending"             => "Mindful Spending"
    ,"mini_mindbodyspirit"              => "Mind-Body-Spirit"
    ,"mini_movemoresitless"             => "Move More, Sit Less"
    ,"mini_purposemeaning"              => "Purpose & Meaning"
    ,"mini_socialconnectedness"         => "Social Connectedness"
);
$arm_years          = getSessionEventYears();
$extra_params       = array(
    'content'     	=> 'record'
    ,'records'     	=> array($loggedInUser->id)
    ,"type"         => "eav"
    ,"fields"       => array("mini_bettersleep","mini_creativity","mini_getoutside","mini_mindfulspending","mini_mindbodyspirit","mini_movemoresitless","mini_purposemeaning", "mini_socialconnectedness")
);
$mini_c_results   = RC::callApi($extra_params, true, $API_URL, $API_TOKEN);

$pageTitle          = "Well v2 Rewards";
$bodyClass          = "rewards";
include_once("models/inc/gl_head.php");
?>
<div class="main-container">
    <div class="main wrapper clearfix">
            <div id="myrewards" >
                <h3><?php echo lang("NAV_REWARDS") ?></h3>

                <aside class="core_complete">
                    <h4>Completed Core Surveys</h4>
                    <ul>
                        <li class="complete">Year 1 (2016)</li>
                        <li class="complete">Year 2 (2017)</li>
                        <li class=""><a class="points_only" href="tree.php">Year 3 (2018)</a></li>
                    </ul>
                </aside>

                <aside class="mini_challenges">
                    <h4>Mini Challenges Rewards (click to set site background)</h4>
                    <ul>
                        <li class="default <?php echo ($portal_bg == "default" ? " active" :"") ?>">Default (Blank)</li>
                        <?php
                            foreach($mini_c_results as $result){
                                $year   = $arm_years[$result["redcap_event_name"]];
                                $field  = $result["field_name"];
                                $active = $portal_bg == $mini_rewards[$field ."_" .$year] ? " active" :"";
                                echo "<li class='".$mini_rewards[$field ."_" .$year] ."$active'/>".$mini_formatted[$field]." (".$year.")</li>\r";
                            }
                        ?>
                    </ul>
                </aside>

                <aside class="well_of_fortune">
                    <h4>WELL of Fortune</h4>

                    <div class="stats">
                        <div id="guesscount">
                            <h5><a href="game.php" class="">Spins</a></h5>
                            <b>0</b>
                        </div>
                        <div id="totalpoints">
                            <h5>Total Prize</h5>
                            <b>0</b>
                        </div>
                    </div>

                    <h5>Solved Puzzles</h5>
                    <ul class="quotes">
                        <?php
                            $extra_params   = array(
                                'content'   => 'record',
                                'format'    => 'json',
                                "records"   => $loggedInUser->id,
                                "fields"    => "portal_wof_solved",
                                "events"    => "enrollment_arm_1"
                            );
                            $results        = RC::callApi($extra_params, true,  $API_URL, $API_TOKEN);
                            $quotes         = !empty($results) ? current($results) : array();
                            $quotes         = isset($quotes["portal_wof_solved"]) ? json_decode($quotes["portal_wof_solved"],1) : array();

                            $quotes_html  = array();
                            foreach($quotes as $idx => $quote){
                                $quotes_html[] = "<li><blockquote>".$quote["quote"]."</blockquote><cite>~ ".$quote["cite"]."</cite></li>";
                            }
                            $quotes_html = implode("\r\n",$quotes_html);
                            echo $quotes_html;
                        ?>
                    </ul>
                </aside>
           </div>
    </div> <!-- #main -->
</div> <!-- #main-container -->
<style>
.stats{
    overflow:hidden;
    margin-bottom:20px;
}
.stats div {
    float:left;
    width:49%;
    text-align:center;
    background:#efefef;
    border-radius:5px;
    padding: 15px 0;
}
#totalpoints{
    float:right;
}

#myrewards aside:not(:first-of-type) {
    border-top:1px solid #ccc;
    margin-top:15px;
    padding-top:15px;
}

#myrewards aside h4 {
    font-weight:bold;
    font-size:120%;
    margin-bottom:15px;
    color:#C3242F
}

#myrewards ul {
    margin:0;
    padding:0;
    list-style:none;
}

#myrewards h5 {
    font-weight:bold;
    font-size:100%;
    margin-bottom:15px;
    color:#C3242F
}

.core_complete li{
    display:inline-block;
    width:100px;
    text-align:center;
    background:url(assets/img/well_logo_treeonly.png) 50% 0 no-repeat;
    background-size:80%;
    padding-top:100px;
    margin-right:30px;
    filter: grayscale(100%);
}

.core_complete a{
    font-weight:bold;
}

.mini_challenges li.default{
    background:none;
    position:relative;
}
.mini_challenges li.default:before{
    content:"";
    top:0; left:50%;
    margin-left:-57px;
    width:114px;
    height:86px;
    border:1px solid #ccc;
    border-radius:5px;
    position:absolute;
}
.mini_challenges li.default.active:before{
    border:2px solid #a3f1dd;
}
.mini_challenges li.active{
    filter:initial;
}

.mini_challenges li{
    display: inline-block;
    width: 110px;
    text-align: center;
    background: url(assets/img/bg/bg_forest.jpg) 50% 0 no-repeat;
    background-size: auto 85px;
    padding-top: 90px;
    margin-right: 15px;
    filter: grayscale(100%);
    vertical-align: top;
    font-size: 85%;
    line-height: 120%;
    margin-bottom:15px;
}
.mini_challenges li:hover{
    filter:initial;
    cursor:pointer;
}
.mini_challenges li.forest_river{
    background: url(assets/img/bg/bg_forest_river.jpg) 50% 0 no-repeat;
    background-size: auto 85px;
}
.mini_challenges li.halloween{
    background: url(assets/img/bg/bg_halloween.jpg) 50% 0 no-repeat;
    background-size: auto 85px;
}
.mini_challenges li.mountain_lake{
    background: url(assets/img/bg/bg_mountain_lake.jpg) 50% 0 no-repeat;
    background-size: auto 85px;
}
.mini_challenges li.sand_dune{
    background: url(assets/img/bg/bg_sand_dune.png) 50% 0 no-repeat;
    background-size: auto 85px;
}
.mini_challenges li.space{
    background: url(assets/img/bg/bg_space.jpg) 50% 0 no-repeat;
    background-size: auto 85px;
}
.mini_challenges li.spring{
    background: url(assets/img/bg/bg_spring.jpg) 50% 0 no-repeat;
    background-size: auto 85px;
}
.mini_challenges li.summer_village{
    background: url(assets/img/bg/bg_summer_village.jpg) 50% 0 no-repeat;
    background-size: auto 85px;
}
.mini_challenges li.treasure_chest{
    background: url(assets/img/bg/bg_treasure_chest.jpg) 50% 0 no-repeat;
    background-size: auto 85px;
}
.mini_challenges li.tropical{
    background: url(assets/img/bg/bg_tropical.jpg) 50% 0 no-repeat;
    background-size: auto 85px;
}
.mini_challenges li.winter{
    background: url(assets/img/bg/bg_winter.jpg) 50% 0 no-repeat;
    background-size: auto 85px;
}
.mini_challenges li.winter_2{
    background: url(assets/img/bg/bg_winter_2.jpg) 50% 0 no-repeat;
    background-size: auto 85px;
}
.core_complete li.complete{
    filter: initial;
}

.quotes li{
    border-radius:5px;
    padding:15px;
    margin-bottom:20px;
    background:#eee;

}

.quotes blockquote {
    padding: 0;
    margin: 0;
    font-size: 20px;
    border-left: none;
    text-align:center;
    color:#333;
}
.quotes cite{
    display:block;
    text-align:center;
}
</style>
<?php 
include_once("models/inc/gl_foot.php");
?>
<script>
$(document).ready(function(){
    $(".mini_challenges li").on("click",function(){
        $(".mini_challenges li").removeClass("active");
        var miniclass   = $(this).attr("class");
        var dataurl     = "&mini_clicked=" + miniclass;
        $(this).addClass("active");
        $.ajax({
            url:  "rewards.php",
            type:'POST',
            dataType: "JSON",
            data: dataurl,
            success:function(result){
                console.log(result);
            }
        });
        return;
    });
});
</script>

