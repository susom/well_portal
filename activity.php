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

if(!empty($result[0]["domainorder_ec"])){
	$dom = (current($result));
	asort($dom);
	$ranked 	= array_flip(array_filter($dom));
	$unranked 	= array_diff(array_keys($dom),$ranked);
}

$pageTitle = "Domain Prioritization";
$bodyClass = "resources";
include_once("models/inc/gl_head.php");
?>
<html>
	<head>	
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	</head>
	<body>
		<div class="main-container">
        	<div class="main_wrapper">
        		<div class ="reorganize">
	        		
	        		<div id = "center">
	        			<h4 style = "text-align:left"><strong><?php echo lang("DOMAIN_ORDER_INSTRUCTION") ?></strong></h4>
		        		<!-- <div id = "least_greatest">
							<p>Most Important</p>
							<img class = "arrow" src = "assets/img/two-sided-arrow.png">
							<p>Least Important</p>
						</div> -->
						<div class="domain_prefer">
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
						<div class="domain_prefer">
							<h3><?php echo lang("LEAST_IMPORTANT") ?></h3>
							<ol id="bottom_ranking" class="connectedSortable">
								<?php 
								$bot_r = array();
								foreach(array(10,9,8) as $key){
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
						<div class='ten_domains'>
							<h3><?php echo lang("TEN_DOMAINS") ?></h3>
							<ul id="items" class="connectedSortable">
								<?php 
									$unranked = array_merge($unranked,$ranked);
									$unordered = isset($dom) ? $unranked : $radar_domains;
									foreach($unordered as $key=> $domain){
										if(isset($dom)){
											$key 	= array_search($domain, $redcap_variables);
											$domain = $radar_domains[$key];
										}
										$tooltip 	= $domain_desc[$key];
										echo "<li id='$domain' title='$tooltip'>\r";
										echo "<img class='domain' src=assets/img/0".$key."-domain.png>\r";
										echo $domain;
										echo "</li>\r";
									}
								?>
							</ul>
						</div>
						<div id = "fin">
							<button id = "finish" class = "btn-success">Save My Result</button>
						</div>
					</div>
					
				</div>
			</div>
		</div>
	</body>

</html>

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
    	var button = $("<button>").addClass("alert").addClass("btn-success").addClass("alert").attr("data-dismiss","").text("OK");
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

    	$.ajax({
	      url  		: "reorderPost.php",
	      type 		: 'POST',
	      data 		: {"domains" : order},
          success 	: function(result){
          	console.log(result);
            if(redirect){
        		addModal("<?php echo lang("DOMAIN_SAVED_REDIRECT") ?>");
	    		setTimeout(function(){
        			location.href="survey.php?sid="+redirect;
			    },5000);
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
	width:680px;
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
	float:right;
	clear:right;
	width:320px;
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
#bottom_ranking{
    border:1px solid red;
	background:pink;
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
}

.ten_domains{
	width:300px;
	margin-top:40px;
}

#items{
	display:block;
	list-style: none;
	margin: 0;
	padding:10px;
	border:1px solid #ccc;
	border-radius:5px;
	clear:left;
	min-height:518px;
}

#items li{
  background-color:#f2f2f2;
  display: block;
  border:solid;
  border-width: 3px;
  border-color:white;
  color:black;
  font-weight: bold;
  position: relative;
  padding:1px 0 1px 20px;
  cursor:pointer;
  text-align: left;
  padding-left: 10px;
  margin-bottom:10px; 
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

