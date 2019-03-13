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
$CMS_API_URL        = SurveysConfig::$projects["ADMIN_CMS"]["URL"];
$CMS_API_TOKEN      = SurveysConfig::$projects["ADMIN_CMS"]["TOKEN"];

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

//MINI C
$extra_params   = array(
    'content'   => 'record',
    'format'    => 'json',
    "fields"    => array("id", "portal_mc_name","portal_mc_year"),
    "filterLogic" => "[portal_mc_name] != '' "
);
$minics = RC::callApi($extra_params, true, $CMS_API_URL, $CMS_API_TOKEN);

$mini_formatted = array();
$mini_rewards   = array();
foreach($minics as $minic){
    $recordid   = $minic["id"];
    $file_curl  = RC::callFileApi($recordid, "portal_mc_img", null, $CMS_API_URL,$CMS_API_TOKEN);
    if(strpos($file_curl["headers"]["content-type"][0],"image") > -1){
        $eventpic = base64_encode($file_curl["file_body"]);
    }

    // make formatted name
    $formatted = str_replace(",", "", strtolower($minic["portal_mc_name"]));
    $formatted = str_replace("-", "", $formatted);
    $formatted = str_replace(" and ", "", $formatted);
    $formatted = str_replace(" ", "", $formatted);
    $mini_formatted["mini_".$formatted] = $minic["portal_mc_name"];
    $mini_rewards["mini_".$formatted."_".$minic["portal_mc_year"]] = $eventpic;
}

$arm_years          = getSessionEventYears();
$extra_params       = array(
    'content'     	=> 'record'
    ,'records'     	=> array($loggedInUser->id)
    ,"type"         => "eav"
    ,"fields"       => array("mini_bettersleep","mini_creativity","mini_getoutside","mini_mindfulspending","mini_mindbodyspirit","mini_movemoresitless","mini_purposemeaning", "mini_socialconnectedness")
);
$mini_c_results   = RC::callApi($extra_params, true, $API_URL, $API_TOKEN);

if(!isset($loggedInUser->portal_wof_unlocked) || !$loggedInUser->portal_wof_unlocked){
    $locked_icon = "locked";
}

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
                        <?php
                            $tree_years     = $arm_years;

                            $current_ann    = array_pop($tree_years);
                            $i              = 1;
                            foreach($tree_years as $ayear){
                                echo "<li class='complete'>Year $i ($ayear)</li>\n";
                                $i++;
                            }
                            echo "<li class=''><a class='' href='tree.php'>Year $i (".$current_ann.")</a></li>\n";
                        ?>
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
                                $active = $portal_bg == $field ."_" .$year ? " active" :"";
                                echo "<li class='".$field ."_" .$year ."$active' data-minic='".$mini_formatted[$field]."'>".$mini_formatted[$field]." (".$year.")</li>\r";
                            }
                        ?>
                    </ul>
                </aside>

                <aside class="well_of_fortune <?php echo $locked_icon; ?>">
                    <h4>WELL of Fortune</h4>

                    <div class="stats">
                        <div id="guesscount">
                            <h5><a href="game.php" class="">Total Spins Used</a></h5>
                            <b>0</b>
                        </div>
                        <div id="totalpoints">
                            <h5>Total Earned Points</h5>
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
                            $quotes         = !empty($quotes["portal_wof_solved"]) ? json_decode($quotes["portal_wof_solved"],1) : array();

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

<?php
echo "//wtpho\r";
foreach($mini_rewards as $mcyear => $base64img){
    echo ".mini_challenges li.$mcyear { \r";
    echo "background: url(data:image/gif;base64,$base64img) 50% 0 no-repeat;\r";
    echo "background-size: auto 85px; }\r\r";
}
?>

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

.locked{
    position:relative;
    min-height: 400px;
}
.locked:before{
    position:absolute;
    content:"Unlock by earning 5000 WELL points";
    top:62px;
    left:0;
    width:100%;
    height:calc(100% - 62px);
    background:#ccc url(https://cdnjs.cloudflare.com/ajax/libs/ionicons/4.4.8/collection/icon/svg/ios-lock.svg) 50% no-repeat;
    background-size:20%;
    opacity:.8;
    border-radius:8px;

    text-align:center;
    font-size: 150%;
    line-height: 420%;
    min-height: 360px;
    color:#000;
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
        var mininame    = $(this).data("minic");

        var dataurl     = "&mini_clicked=" + mininame;
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

