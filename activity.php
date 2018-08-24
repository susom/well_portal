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

$API_URL      = SurveysConfig::$projects["REDCAP_PORTAL"]["URL"];
$API_TOKEN    = SurveysConfig::$projects["REDCAP_PORTAL"]["TOKEN"];
$data = array(
		  'content'     => 'record',
		  "events" 		=> $user_event_arm,
          'records'     => array($loggedInUser->id),
          'fields'      => array("domainorder_ec", "domainorder_lb", "domainorder_sc","domainorder_sr", "domainorder_ee",
          						 "domainorder_ss", "domainorder_ph", "domainorder_pm","domainorder_fs","domainorder_rs")
);

$result = RC::callApi($data, true, $API_URL , $API_TOKEN);
if(!empty($result[0]["domainorder_ec"])){
	$dom = ($result[0]);
	asort($dom);
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
	        			<h4 style = "text-align:left"><strong>Please order (drag-drop) these 10 well-being domains from most important to least important, according to how important they are to you.</strong></h4>
		        		<div id = "least_greatest">
							<p>Most Important</p>
							<img class = "arrow" src = "assets/img/two-sided-arrow.png">
							<p>Least Important</p>
						</div>
						<ul id = "items" >
							<?php
							if(isset($dom)){ //if an ordering exists already
								foreach($dom as $k => $val){
									 $key = array_search($k,$redcap_variables);
									 echo "<li id ='".$radar_domains[$key]."'> 
										   <img class = 'domain' src = assets/img/0".$key."-domain.png>".$radar_domains[$key]."</li>";
								}
							}else{
							?>
								<li id = "<?php echo $radar_domains[0]; ?>">
									<img class = "domain" src = assets/img/00-domain.png> 
									<?php echo $radar_domains[0]; ?>
								</li>	
								<li id = "<?php echo $radar_domains[1]; ?>">
									<img class = "domain" src = assets/img/01-domain.png> 
									<?php echo $radar_domains[1]; ?>
								</li>	
								<li id = "<?php echo $radar_domains[2]; ?>">
									<img class = "domain" src = assets/img/02-domain.png> 
									<?php echo $radar_domains[2]; ?>
								</li >
								<li id = "<?php echo $radar_domains[3]; ?>">
									<img class = "domain" src = assets/img/03-domain.png> 
									<?php echo $radar_domains[3]; ?>
								</li>	
								<li id = "<?php echo $radar_domains[4]; ?>">
									<img class = "domain" src = assets/img/04-domain.png> 
									<?php echo $radar_domains[4]; ?>
								</li>	
								<li id = "<?php echo $radar_domains[5]; ?>">
									<img class = "domain" src = assets/img/05-domain.png> 
									<?php echo $radar_domains[5]; ?>
								</li>	
								<li id = "<?php echo $radar_domains[6]; ?>">
									<img class = "domain" src = assets/img/06-domain.png> 
									<?php echo $radar_domains[6]; ?>
								</li>	
								<li id = "<?php echo $radar_domains[7]; ?>">
									<img class = "domain" src = assets/img/07-domain.png> 
									<?php echo $radar_domains[7]; ?>
								</li>	
								<li id = "<?php echo $radar_domains[8]; ?>">
									<img class = "domain" src = assets/img/08-domain.png> 
									<?php echo $radar_domains[8]; ?>
								</li>	
								<li id = "<?php echo $radar_domains[9]; ?>">
									<img class = "domain" src = assets/img/09-domain.png> 
									<?php echo $radar_domains[9]; ?>
								</li>	
								<?php 
							}//else
								?>
						</ul>
					</div>
					<div id = "fin">
						<button id = "finish" class = "btn-success">Save My Result</button>
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
	<?php

	?>

    $(document).ready(function(){
        $("#items").sortable({
        	appendTo: document.body,
        	cursor: "move"
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
    	var ret 		= $("#items").sortable("toArray",{attribute: 'id'});

    	$.ajax({
	      url  		: "reorderPost.php",
	      type 		: 'POST',
	      data 		: "&domains=" + JSON.stringify(ret),
          success 	: function(result){
          	console.log(result);
            if(redirect){
        		addModal("Domain Priorities Saved.  Redirecting back to Well-Being survey in 3...2..1");
	    		setTimeout(function(){
        			location.href="survey.php?sid="+redirect;
			    },3000);
	    	}else{
        		addModal("Domain Priorities Saved!");
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
#center{
	text-align:center;
	width:500px;
	display: inline-block;
	position: relative;
}

#fin{
	margin-top: 20px;
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
	display:block;
	position:absolute;
	margin-top: 8%;
	left: -60px;
}
.reorganize{
	text-align: center;
	margin-top: 25px;
	display: block;
	position:relative;
}
#items{
	display:inline-block;
	margin: 0;
	padding:0;
	list-style: none;
	width:350px;
	margin-top:25px;
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
		margin-top:-5px;

	}
	#items li{
	  max-width: 68vw;
	  left:8vw;
	  font-size: 13px;
	}
	.domain{
		max-width:30px;
	}
	#finish{
		height:9vw;
		margin:0px;
	}
}
</style>

