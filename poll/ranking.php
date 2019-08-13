<?php 
require_once("../models/config.php");

$radar_domains      = array(
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
$redcap_variables   = array(
  "0" => "domainorder_ec",
  "1" => "domainorder_lb",
  "2" => "domainorder_sc",
  "3" => "domainorder_sr",
  "4" => "domainorder_ee",
  "5" => "domainorder_ss",
  "6" => "domainorder_ph",
  "7" => "domainorder_pm",
  "8" => "domainorder_fs",
  "9" => "domainorder_rs"
);
$domain_desc        = array(
	"0" =>  lang("DOMAIN_EC_DESC" ),
	"1" =>  lang("DOMAIN_LB_DESC" ),
	"2" =>  lang("DOMAIN_SC_DESC"),
	"3" =>  lang("DOMAIN_SR_DESC" ),
	"4" =>  lang("DOMAIN_EE_DESC" ),
	"5" =>  lang("DOMAIN_SS_DESC" ),
	"6" =>  lang("DOMAIN_PH_DESC" ),
	"7" =>  lang("DOMAIN_PM_DESC" ),
	"8" =>  lang("DOMAIN_FS_DESC" ),
	"9" =>  lang("DOMAIN_RS_DESC" ),
);
$var_map = array(
    lang("RESOURCE_CREATIVITY")  => "domainorder_ec",
    lang("RESOURCE_LIFESTYLE")	 => "domainorder_lb",
    lang("RESOURCE_SOCIAL")	 	 => "domainorder_sc",
    lang("RESOURCE_STRESS")	 	 => "domainorder_sr",
    lang("RESOURCE_EMOTIONS")	 => "domainorder_ee",
    lang("RESOURCE_SELF")		 => "domainorder_ss",
    lang("RESOURCE_PHYSICAL")	 => "domainorder_ph",
    lang("RESOURCE_PURPOSE")	 => "domainorder_pm",
    lang("RESOURCE_FINANCIAL")	 => "domainorder_fs",
    lang("RESOURCE_RELIGION")	 => "domainorder_rs",
);

//GET THE TOTAL
$API_URL        = SurveysConfig::$projects["POLL_PROJECT"]["URL"];
$API_TOKEN      = SurveysConfig::$projects["POLL_PROJECT"]["TOKEN"];

if(!isset($_SESSION["poll_ts"])){
    $_SESSION["poll_ts"] = md5(time());
}

//SAVE THE RESULTS
if(isset($_POST["domains"]) && isset($_POST["siggy"]) && $_POST["siggy"] == $_SESSION["poll_ts"]){
    unset($_SESSION["poll_ts"]);
    $poll_order     = $_POST["domains"];

    //FIRST GET CURRENT AGGRAGATE RESULTS
    $data = array(
        'content'   => 'record',
        'records'   => '1',
        'fields'    => $redcap_variables
    );
    $result = RC::callApi($data, true, $API_URL , $API_TOKEN);
    if(!empty($result) && current($result)) {
        $current_totals = current($result);
    }else{
        // ALL Start at 0
        $current_totals = array_flip($redcap_variables);
        $current_totals = array_fill_keys(array_keys($current_totals), json_encode(array()));
    }

    $data       = array();
    $agg_data   = array();
    foreach($poll_order as $order => $domain){
        $redcap_var     = $var_map[$domain];
        $convert_json   = json_decode($current_totals[$redcap_var],1);
        array_push($convert_json,$order);
        $data[] = array(
            "record"            => 1,
            "field_name"        => $var_map[$domain],
            "value"             => json_encode($convert_json)
        );

        $agg_data[$var_map[$domain]] = array_sum($convert_json)/count($convert_json);
    }
    $result = RC::writeToApi($data, array("overwriteBehavior" => "overwite", "type" => "eav"), $API_URL , $API_TOKEN);
    asort($agg_data);

    $html   = array();
    $i      = 1;
    foreach($agg_data as $domain => $order){
        $real_key 	= array_search($domain, $redcap_variables);
        $domain     = $radar_domains[$real_key];
        $tooltip 	= $domain_desc[$real_key];

        $html[] =  "<li id='".$domain."' title='$tooltip' class='col-sm-12 row domain_item'>\r";
        $html[] =  "<div class='col-sm-2 domain_image' style='background:url(../assets/img/0".$real_key."-domain.png) 50% no-repeat; background-size:contain'></div>\r";
        $html[] =  "<div class='col-sm-10 domain_text'><span>$i.</span> $domain</div>";
        $html[] =  "</li>\r";
        $i++;
    }
    echo implode("",$html);
    exit;
}

$ranked         = array();
$unranked       = array();
$pageTitle      = "Domain Prioritization Poll";
$bodyClass      = "resources";
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="google" content="notranslate">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php echo $pageTitle ?></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--<link rel="stylesheet" href="assets/css/mini-default.min.css">-->
    <link rel="stylesheet" href="../assets/css/mini-default.css">
    <link rel="stylesheet" href="../assets/css/roundslider.css" />
    <link rel="stylesheet" href="../assets/css/main_rwd.css">
    <script src="../assets/js/jquery-1.12.4.js"></script>
    <script src="../assets/js/vendor/modernizr-2.8.3-respond-1.4.2.min.js"></script>
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/jquery-ui.js"></script>
    <script src="../assets/js/jquery.ui.touch-punch.min.js"></script>
    <!-- Facebook Pixel Code -->
    <!--
<?php
    $trackpage = isset($trackpage) ? $trackpage : $bodyClass;
    ?>
<script>
!function(f,b,e,v,n,t,s){
if(f.fbq)return;n=f.fbq=function(){n.callMethod ? n.callMethod.apply(n,arguments) : n.queue.push(arguments)};
if(!f._fbq) f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '411269625926334');
fbq('track', '<?php echo $trackpage; ?>');
</script>
<noscript>
<img height="1" width="1" src="https://www.facebook.com/tr?id=411269625926334&ev=<?php echo $trackpage; ?>&noscript=1"/>
</noscript>
-->
    <!-- End Facebook Pixel Code -->
</head>
<body class="<?php echo $bodyClass ?>">
<div id="outter_rim">
    <div id="inner_rim" class="container">
    <header class="row">
        <a href="#" class="hamburger points_none"></a>
        <a class="title logo"><?php echo lang("WELL_FOR_LIFE") ?></a>
    </header>
    <div class="splash-container">
        <div class="wrapper row">
            <?php
            if(isset($cats[1])){
                if(isset($cats[1]["subject"]) && isset($cats[1]["content"])){
                    ?>
                    <h2 class="col-sm-12" ><?php echo $cats[1]["subject"]?></h2>
                    <blockquote class="col-sm-10 col-sm-offset-1 col-lg-offset-4 col-lg-4">
                        <?php echo $cats[1]["content"]?>
                    </blockquote>
                    <style>
                        .splash-container h2:before {
                            background: url(<?php echo $cats[1]["pic"] ?>) 50% no-repeat;
                            background-size:cover;
                        }
                    </style>
                    <?php
                }
            }
            ?>
        </div>
    </div>
    <div class="main-container">
        <div class ="reorganize row">
            <h4 style = "text-align:left" class="col-sm-12"><strong>Please think about how important each of the 10 domains of well-being are to your personal well-being. Then drag-and-drop in order of importance. (If you don't know what a certain domain means, don't worry, you can hover over it for the definition.)</strong></h4>

            <div class="col-sm-12 row">
                <div class='ten_domains col-sm-6'>
                    <h3>Domains of Well-Being</h3>
                    <ul id="items" class="connectedSortable row">
                        <?php
                            $unranked 	= array_merge($unranked,$ranked);
                            $unordered 	= isset($dom) ? $unranked : $radar_domains;
                            $forshuffle = $unordered;
                            shuffle($forshuffle);
                            foreach($forshuffle as $domain){
                                $real_key = array_search($domain, $unordered);

                                if(isset($dom)){
                                    $real_key 	= array_search($domain, $redcap_variables);
                                    $domain = $radar_domains[$real_key];
                                }
                                $tooltip 	= $domain_desc[$real_key];
                                echo "<li id='$domain' title='$tooltip' class='col-sm-12 row domain_item'>\r";
                                echo "<div class='col-sm-2 domain_image' style='background:url(../assets/img/0".$real_key."-domain.png) 50% no-repeat; background-size:contain'></div>\r";
                                echo "<div class='col-sm-10 domain_text'><span></span> $domain</div>";
                                echo "</li>\r";
                            }
                        ?>
                    </ul>
                </div>

                <div class="domain_prefer col-sm-6">
                    <h3>Your Rankings</h3>
                    <ol id="top_ranking" class="connectedSortable">
                    </ol>
                </div>
            </div>

            <div id="fin" class="col-sm-12 ">
                <button id="resetpoll" href='ranking.php' class="btn-info">Reset Poll</button>
                <button id="finish" class="btn-success">Compare My Result</button>
            </div>
        </div>
    </div>
</div>
</div>
<script>window.jQuery || document.write('<script src="assets/js/vendor/jquery-1.11.2.min.js"><\/script>')</script>
<!-- Bootstrap -->
<script src="../assets/js/bootstrap.js"></script>
<script src="../assets/js/jquery.visible.min.js"></script>
<script src="../assets/js/underscore-min.js"></script>
</body>
</html>
<script>
    $(document).ready(function(){
	    $( "#items, #top_ranking, #bottom_ranking" ).sortable({
            connectWith: ".connectedSortable",
            receive: function(event, ui) {
                // if ($(this)[0].nodeName == "OL" && $(this).children().length > 10) {
                //     $(ui.sender).sortable('cancel');
                // }
            },
            beforeStop: function(event, ui) {
                $("#top_ranking .domain_text span").each(function(idx){
                    var i = idx +1;
                    $(this).text(i + ".");
                });
            }
	    });
    });

    // Read a page's GET URL variables and return them as an associative array.
	function getUrlVars(){
	    var vars = [], hash;
	    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
	    for(var i = 0; i < hashes.length; i++)
	    {
	        hash = hashes[i].split('=');
	        vars.push(hash[0]);
	        vars[hash[0]] = hash[1];
	    }
	    return vars;
	}
    $("#resetpoll").click(function(){
        location.href=$(this).attr("href");
    });
    $("#finish").click(function(){
    	var redirect  	= getUrlVars()["nextsid"];
    	var top 		= $("#top_ranking").sortable("toArray",{attribute: 'id'});

        var order 		= {};
    	for(var i in top){
    		order[parseInt(i)+1] = top[i];
    	}

    	if($("#top_ranking .domain_text").length < 10){
    	    alert("Please rank all the Domains of Well-Being.");
    	    return false;
        }

        $.ajax({
	      url  		: "ranking.php",
	      type 		: 'POST',
	      data 		: {"domains" : order, "siggy" : "<?php echo $_SESSION["poll_ts"]?>"},
          success 	: function(result){
          	// Calculate and Show

            $(".ten_domains h3").addClass("show_results").html("Community Aggregate Rankings");
            $("#items").addClass("show_results").html(result);

            $("#fin").unbind();
          },
          function(err){
          	console.log(err);
          }
      	});
    });
</script>
<style>
button.btn-success.alert{
	top:78%;
	padding-top:5px;
	padding-bottom:5px;
	width: 20%;
    margin-left: -10%;
}


#fin{
	margin-top: 30px;
	text-align:center;
	clear:both;
    margin-bottom:200px;
}
#fin button{
	border-radius:5px;
}
#finish{
	margin: 5px;
	width: auto;
	height: 40px;
	text-align: center;
}

#least_greatest{
	display:none;
	position:absolute;
	margin-top: 8%;
	left: -60px;
}
.reorganize{
	margin-top: 25px;
	display: block;
	position:relative;
	width:100%;
}
#center{
	/*width:680px;*/
	margin:0 auto;
	display: block;
	position: relative;
	overflow:hidden;
}
#center h4 strong {
	/*font-weight:normal; */
	text-align:justify;
	display:block;
}
.domain_prefer,.ten_domains{
	/*float:right;*/
	clear:right;
	/*width:320px;*/
	margin-top:10px;
}
.ten_domains{
	float:none;
	clear:initial;
	width:initial;
	margin-top:initial;
}
.domain_prefer h3,.ten_domains h3{
	text-align:center;
	font-size:20px;
}
.domain_prefer ol{
    border:1px solid green;
	padding:10px;
	height:420px;
	border-radius:10px;
    border-style: dashed;
}
.domain_item{
    background:#000;
    padding:15px 10px
}
.domain_text{

}
domain_image{

}
#bottom_ranking{
    border:1px solid red;
	background:pink;
}

#bottom_ranking li,#top_ranking li{
    width: 100%;
    max-width: 97%;
}
.domain_prefer li{
	list-style-position: inside;

	background-color:#f2f2f2;
	border:solid;
	border-width: 3px;
	border-color:transparent;
	color:black;
	font-weight: bold;
	position: relative;
	padding:1px 0 1px 20px;
	cursor:pointer;
	text-align: left;
	padding-left: 10px;
	margin-bottom:10px;
    font-size:88%;
    border-radius: 5px;
}

.ten_domains{
	/*width:300px;*/
	margin-top:10px;
}

#items{
	/*display:block;*/
	list-style: none;
	margin: 0;
	padding:10px;
	border:1px solid #ccc;
	border-radius:10px;
	clear:left;
	min-height:443px;
}
#items span{ display:none;}
#items.show_results span{display:inline-block;}
#items li{
    list-style-position: inside;
    background-color: #f2f2f2;
    border: solid;
    border-width: 3px;
    border-color: transparent;
    color: black;
    font-weight: bold;
    position: relative;
    padding: 1px 0 1px 20px;
    cursor: pointer;
    text-align: left;
    padding-left: 10px;
    margin-bottom: 10px;
    font-size: 88%;
    border-radius: 5px;
    max-height:33px;
}

#items.show_results li:nth-child(-n+3) .domain_text,
#top_ranking li:nth-child(-n+3) .domain_text{
    color:limegreen;
}
#items.show_results li:nth-child(8) .domain_text,
#items.show_results li:nth-child(9) .domain_text,
#items.show_results li:nth-child(10) .domain_text,
#top_ranking li:nth-child(8) .domain_text,
#top_ranking li:nth-child(9) .domain_text,
#top_ranking li:nth-child(10) .domain_text{
    color:red;
}
.domain{
	max-width: 40px;
	max-height: 40px;
	margin:5px;
}


@media only screen
and (min-device-width : 320px)
and (max-device-width : 480px){
	#least_greatest{
		left:2vw;
	}
	#least_greatest p{
		font-size: 13px;
	}
	#center h4{
		max-width: 100vw;
		padding:0px 5px 0px 15px;

	}
	#items li{
	  font-size: 13px;
	  margin-bottom:0;
	}
	.domain{
		max-width:30px;
	}
	#finish{
		margin:0px;
	}

	#center {
		width:373px;
	}
	#items {
		margin:0 auto;
		min-height:initial;
	}
	.domain_prefer {
		float:none; 
	    margin: 10px auto;
	}
	.ten_domains{
		margin: 25px auto 0;
	}
	.domain_prefer h3,.ten_domains h3{
	    font-size: 108%;
	}
	.domain_prefer ol{
		height:146px;
	}
	.domain_prefer li{
		border:none;
		margin-bottom:2px; 
		font-weight:normal;
		padding:0 0 0 20px;
	}
}

h3.show_results{
    color:#8c1515;
    font-weight:bold;
}
#items.show_results{
    background:#8c1515;

}
#items.show_results li{
    background: #F9F6EF;
    color:#2F2424;
}
</style>

