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
$API_URL      = SurveysConfig::$projects["REDCAP_PORTAL"]["URL"];
$API_TOKEN    = SurveysConfig::$projects["REDCAP_PORTAL"]["TOKEN"];
$data = array(
    	  'content'     => 'record',
          'records'     => array($loggedInUser->id),
          'fields'      => array("domain_order")
);
$result = RC::callApi($data, true, $API_URL , $API_TOKEN);

if(!empty($result))
	$dom = json_decode($result[0]["domain_order"]); 


include_once("models/inc/gl_head.php");
?>
<html>
	<head>		
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
								foreach($dom as $in){
									$key = array_search($in,$radar_domains);
									echo "<li id ='".$in."'> 
										<img class = 'domain' src = assets/img/0".$key."-domain.png>".$in."</li>";
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
    $(document).ready(function(){
        $("#items").sortable({
        	appendTo: document.body,
        	cursor: "move"
        });
    });//ready
    
    $("#finish").click(function(){
    	var domains = JSON.stringify($("#items").sortable("toArray",{attribute:'id'}));
    	$.ajax({
          url:  "reorderPost.php",
          type:'POST',
          data: "&domains="+ domains,
          success:function(result){
            console.log(result);
          }        
            //THIS JUST STORES IS 
          },function(err){
          console.log("ERRROR");
          console.log(err);
        });

    });
   

</script>
<style>
#center{
	text-align:center;
	width:500px;
	display: inline-block;
	position: relative;
}

#fin{
	margin-top: 20px;
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
  padding:1px 0;
  cursor:pointer;
  text-align: left;
  padding-left: 10px;
}

.domain{
	max-width: 40px;
	max-height: 40px;
	margin:5px;
}

</style>

