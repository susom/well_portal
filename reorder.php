<?php 
require_once("models/config.php"); 
include("models/inc/checklogin.php");
include("models/inc/scoring_functions.php");

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

// GLOBAL NAV SET STATE
$nav    = isset($_REQUEST["nav"]) ? $_REQUEST["nav"] : "home";
$navon  = array("home" => "", "reports" => "", "game" => "", "resources" => "");
$navon[$nav] = "on";

markPageLoadTime("BEGIN HEAD AREA");
$avail_surveys      = $available_instruments;
$first_core_survey  = array_splice($avail_surveys,0,1);
$surveyon           = array();
$surveynav          = array_merge($first_core_survey, $supp_surveys_keys);
foreach($surveynav as $surveyitem){
    $surveyon[$surveyitem] = "";
}
include_once("models/inc/gl_head.php");


?>
<html>
	<head>		
	</head>
	<body>
		<div class="main-container">
        	<div class="main_wrapper">
        		<div class ="reorganize">
	        		<h3>Please order these categories in order of importance</h3>
					<ul id = "items" >
						<li id = "<?php echo $radar_domains[0]; ?>">
							<img src = assets/img/00-domain.png> 
							<?php echo $radar_domains[0]; ?>
						</li>	
						<li id = "<?php echo $radar_domains[1]; ?>">
							<img src = assets/img/01-domain.png> 
							<?php echo $radar_domains[1]; ?>
						</li>	
						<li id = "<?php echo $radar_domains[2]; ?>">
							<img src = assets/img/02-domain.png> 
							<?php echo $radar_domains[2]; ?>
						</li >
						<li id = "<?php echo $radar_domains[3]; ?>">
							<img src = assets/img/03-domain.png> 
							<?php echo $radar_domains[3]; ?>
						</li>	
						<li id = "<?php echo $radar_domains[4]; ?>">
							<img src = assets/img/04-domain.png> 
							<?php echo $radar_domains[4]; ?>
						</li>	
						<li id = "<?php echo $radar_domains[5]; ?>">
							<img src = assets/img/05-domain.png> 
							<?php echo $radar_domains[5]; ?>
						</li>	
						<li id = "<?php echo $radar_domains[6]; ?>">
							<img src = assets/img/06-domain.png> 
							<?php echo $radar_domains[6]; ?>
						</li>	
						<li id = "<?php echo $radar_domains[7]; ?>">
							<img src = assets/img/07-domain.png> 
							<?php echo $radar_domains[7]; ?>
						</li>	
						<li id = "<?php echo $radar_domains[8]; ?>">
							<img src = assets/img/08-domain.png> 
							<?php echo $radar_domains[8]; ?>
						</li>	
						<li id = "<?php echo $radar_domains[9]; ?>">
							<img src = assets/img/09-domain.png> 
							<?php echo $radar_domains[9]; ?>
						</li>	
					</ul>
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
        $("#items").sortable({
        	appendTo: document.body,
        	cursor: "move",
        	stop: function(event, ui){
        		console.log($("#items").sortable("toArray", {attribute: 'id'}));

        	}
        });
    });//ready
    
   

</script>

<style>
.reorganize{
	text-align: center;
}
#items li{
  background-color:#f2f2f2;
  display: block;
  border:solid;
  border-width: 1px;
  text-align: center;
  color:black;
  font-weight: bold;
  line-height: 40px;
  max-width: 50%;
  left:24%;
  position: relative;
  box-shadow: 5px 5px 5px gray;
}
#items li:first-child{
	border-top-right-radius: 12px;
	border-top-left-radius:12px;
	margin-top: 25px;
}
#items li:last-child{
	border-bottom-left-radius: 12px;
	border-bottom-right-radius:12px;
} 

img{
	max-width: 40px;
	max-height: 40px;
	margin:5px;
}

</style>