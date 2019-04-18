<?php 
require_once("models/config.php"); 
include("models/inc/checklogin.php");
include("models/inc/scoring_functions.php");

$navon  = array("home" => "", "reports" => "", "game" => "", "resources" => "", "rewards" => "", "activity" => "on");

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

$redcap_variables = array(
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

$domain_desc = array(
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

$API_URL      = SurveysConfig::$projects["REDCAP_PORTAL"]["URL"];
$API_TOKEN    = SurveysConfig::$projects["REDCAP_PORTAL"]["TOKEN"];
$data = array(
		  'content'     => 'record',
		  "events" 		=> $user_event_arm,
          'records'     => array($loggedInUser->id),
          'fields'      => array("domainorder_ec", "domainorder_lb", "domainorder_sc","domainorder_sr", "domainorder_ee",
          						 "domainorder_ss", "domainorder_ph", "domainorder_pm","domainorder_fs","domainorder_rs")
);

$ranked 	= array();
$unranked 	= array();
$result = RC::callApi($data, true, $API_URL , $API_TOKEN);
if(current($result)) {
    $result = array_filter(current($result));
    if (!empty($result)) {
        $dom = $result;
        asort($dom);
        $ranked = array_flip($dom);
        $unranked = array_diff($redcap_variables, $ranked);
    }
}
$pageTitle = "Domain Prioritization";
$bodyClass = "resources";
include_once("models/inc/gl_head.php");
?>

<div class="main-container">
    <div class ="reorganize">
        <div id="center" class="row">
            <h4 style = "text-align:left" class="col-sm-12"><strong><?php echo lang("DOMAIN_ORDER_INSTRUCTION") ?></strong></h4>

            <div class="col-sm-12 row">
                <div class="domain_prefer col-sm-6">
                    <h3><?php echo lang("MOST_IMPORTANT") ?></h3>
                    <ol id="top_ranking" class="connectedSortable">
                        <?php
                        $top_r = array();
                        foreach(array(1,2,3) as $key){
                            if(isset($ranked[$key])){
                                $domain_code= $ranked[$key];
                                $real_key 	= array_search($domain_code,$redcap_variables);
                                $domain 	= $radar_domains[$real_key];
                                $tooltip 	= $domain_desc[$real_key];
                                $topli =  "<li id='$domain' title='$tooltip'>\r";
                                $topli .= "<img class='domain' src=assets/img/0".($real_key)."-domain.png>\r";
                                $topli .= $domain;
                                $topli .= "</li>\r";
                                $top_r[] = $topli;

                                unset($ranked[$key]);
                            }
                        }
                        echo implode("\r",$top_r);
                        ?>
                    </ol>
                </div>
                <div class="domain_prefer col-sm-6">
                    <h3><?php echo lang("LEAST_IMPORTANT") ?></h3>
                    <ol id="bottom_ranking" class="connectedSortable">
                        <?php
                        $bot_r = array();
                        foreach(array(8,9,10) as $key){
                            if(isset($ranked[$key])){
                                $domain_code= $ranked[$key];
                                $real_key 	= array_search($domain_code,$redcap_variables);
                                $domain 	= $radar_domains[$real_key];
                                $tooltip 	= $domain_desc[$real_key];
                                $botli = "<li id='$domain' title='$tooltip'>\r";
                                $botli .= "<img class='domain' src=assets/img/0".($real_key)."-domain.png>\r";
                                $botli .= $domain;
                                $botli .= "</li>\r";
                                $bot_r[] = $botli;

                                unset($ranked[$key]);
                            }
                        }
                        echo implode("\r",array_reverse($bot_r));
                        ?>
                    </ol>
                </div>

                <div class='ten_domains col-sm-12'>
                    <h3><?php echo lang("TEN_DOMAINS") ?></h3>
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
                                echo "<li id='$domain' title='$tooltip' class='col-sm-12 col-md-6 row domain_item'>\r";
                                echo "<div class='col-sm-2 domain_image' style='background:url(assets/img/0".$real_key."-domain.png) 50% no-repeat; background-size:contain'></div>\r";
                                echo "<div class='col-sm-10 domain_text'>$domain</div>";
                                echo "</li>\r";
                            }
                        ?>
                    </ul>
                </div>
            </div>

            <div id="fin" class="col-sm-12 ">
                <button id = "finish" class = "btn-success">Save My Result</button>
            </div>
        </div>
    </div>
</div>


<?php 
include_once("models/inc/gl_foot.php");
?>
<script>
    $(document).ready(function(){
	    $( "#items, #top_ranking, #bottom_ranking" ).sortable({
	      connectWith: ".connectedSortable",
          receive: function(event, ui) {
		    if ($(this)[0].nodeName == "OL" && $(this).children().length > 3) {
		        $(ui.sender).sortable('cancel');
		    }
		  }
	    });
    });

    function addModal(msg){
    	var div = $("<div>").addClass("alert").addClass("alert-success").addClass("text-center").addClass("mb-30");
    	var button = $("<button>").addClass("btn").addClass("btn-success").attr("data-dismiss","").text("OK");
		var ul = $("<ul>");
		var li = $("<li>");
		var strong = $("<strong>").text(msg);
		li.append(strong);
		var msg = ul.append(li);
		div.append(msg);
		div.append(button);
		$("body").append(div);
		$(".alert").css("opacity",1);
		return;
    }

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

    $("#fin").click(function(){
    	var redirect  	= getUrlVars()["nextsid"];
    	var top 		= $("#top_ranking").sortable("toArray",{attribute: 'id'});
    	var bot 		= $("#bottom_ranking").sortable("toArray",{attribute: 'id'});

    	var order 		= {};
    	for(var i in top){
    		order[parseInt(i)+1] = top[i];
    	}
    	for(var i in bot){
    		order[10-i] = bot[i];
    	}

    	console.log(top);
    	console.log(bot);

    	$.ajax({
	      url  		: "reorderPost.php",
	      type 		: 'POST',
	      data 		: {"domains" : order, "top_bot" : "top"},
          success 	: function(result){
          	// DO ONE FOR TOP PREFS
          	// console.log(result);
          },
          function(err){
          	console.log(err);
          }
      	});
      	$.ajax({
	      url  		: "reorderPost.php",
	      type 		: 'POST',
	      data 		: {"domains" : order, "top_bot" : "bot"},
          success 	: function(result){
          	//NOW DO ONE FOR BOTTOM PREFS, AND ALL THE UI UPDATES
          	// console.log(result);
            if(redirect){
        		addModal("<?php echo lang("DOMAIN_SAVED_REDIRECT") ?>");
	    		setTimeout(function(){
        			location.href="survey.php?sid="+redirect;
			    },3000);
	    	}else{
        		addModal("<?php echo lang("DOMAIN_SAVED") ?>");
	    	}
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
}
#fin button{
	border-radius:5px;
}
#finish{
	margin: 5px;
	width: 150px;
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
	height:220px;
	border-radius:10px;
	background:lightgreen;
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
    max-width: 100%;
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
}

.ten_domains{
	/*width:300px;*/
	margin-top:40px;
}

#items{
	/*display:block;*/
	list-style: none;
	margin: 0;
	padding:10px;
	border:1px solid #ccc;
	border-radius:5px;
	clear:left;
	/*min-height:518px;*/
}

#items li{
  background-color:#f2f2f2;
  border:solid;
  border-width: 3px;
  border-color:white;
  color:black;
  font-weight: bold;
  position: relative;
  padding:10px;
  cursor:pointer;
  text-align: left;
  margin-bottom:10px;
    font-size:88%;
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
		height:9vw;
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
</style>

