<?php
$stopbang_answers 	= $_POST["stopbang"] ?: NULL;
$stopbang_answers 	= json_decode($stopbang_answers,1);
$answers = array();
foreach($stopbang_answers as $pairs){
	$answers[$pairs["name"]] = $pairs["value"];
}
if(count($answers) != 8){
	exit;
}
$STOP 				= array_slice($answers , 0, 4);
$total_yes 			= array_sum($answers);
$STOP_yes 			= array_sum($STOP);
$gender 			= $answers["stop_gender"];
$bmi 				= $answers["stop_bmi"];
$neck 				= $answers["stop_neck"];

if($STOP_yes >= 2){
	if($gender || $bmi || (!$gender && $neck)){
		$risk = "High risk of Obstructive Sleep Apnea";
	}
}else{
	if($total_yes < 3){
		$risk = "Low risk of Obstructive Sleep Apnea";
	}elseif($total_yes < 5){
		$risk = "Intermediate risk of Obstructive Sleep Apnea";
	}
}

// stop_snore
// stop_tired
// stop_observed
// stop_pressure
// stop_bmi
// stop_age
// stop_neck
// stop_gender
?>
<div id="stopbang_results" >
	<blockquote><?php echo $risk?></blockquote>
</div>
<style>
	#stopbang_results blockquote {
		background:#ffc;
		padding:20px; 
		text-align:center;
		border-radius:10px;
	}
	.stop_survey .lang.cn,
	.stop_survey .lang.tw{
		font-size:80%;
	}
</style>