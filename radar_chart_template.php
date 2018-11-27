<?php
include('models/config.php');

//THIS SESSION VARIABLE COMES FROM REPORTS PAGE TO MANAGE THE RADAR CHART
if(!isset($_SESSION["radarchart"])){
    echo "Error: Unable to access WELL Score Data";
    exit;
}
$display_year   = $_SESSION["radarchart"]["display_year"];
$csv_file       = $loggedInUser->id . "_" . $display_year . "_";
?>
<!DOCTYPE html>
<html ng-app="RadarChart">
<head>
  <meta charset="utf-8">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" />
  <link rel="stylesheet" href="assets/css/Radarstyle.css" />
</head>
<body class="container" ng-controller="MainCtrl as radar" data-csvfile="RadarUserCSV/<?php echo $csv_file ?>Results">
  <div class="main container">
    <h2>Results Summary <button id="print_btn" onclick="javascript:window.print()">Print</button></h2>
    <div class="visualization">
        <radar csv="radar.csv" config="radar.config"></radar>
        <?php
          // only display score for single year views
          if($display_year !== 99){
              $well_sum_score = isset($_SESSION["radarchart"]["year"][$display_year]) ? $_SESSION["radarchart"]["year"][$display_year] : "N/A";
              echo "<h3>".lang("OVERALL_WELL_BEING_SCORE").": <b>".$well_sum_score."/100</b></h3>";
          }

          // This PUTS down the Data for prioritized Domains
          if(isset($_SESSION['radarchart']['domain_ranking'])){
            foreach($_SESSION['radarchart']['domain_ranking'] as $index => $domain){
                echo '<div id = "'.$domain.'_L" rank = '.$index.'></div>';
            }
          }
        ?>


        <h4><?php echo lang("RADAR_CHART_MEANING"); ?></h4>
        <p><?php echo lang("RADAR_CHART_REVIEW"); ?></p>
        <p><?php echo lang("RADAR_CHART_DOMAINS"); ?></p>

    </div>
  </div>
</body>
<style>
#print_btn{
    border:1px solid #ccc;
    border-radius:3px;
    background:#efefef;
    box-shadow:1px 1px 1px 0px #666;
    vertical-align: middle;
}
@media print {
    #print_btn {
        display:none;
    }
}
</style>
<script src="assets/js/angular.js"></script>
<script src="assets/js/d3.v3.min.js"></script>
<script>
var radar_w = window.innerWidth <= 480 ? 280 : 440;
var radar_h = window.innerWidth <= 480 ? 280 : 440;
var radar_x = window.innerWidth <= 480 ? 70  : 300;
var radar_y = window.innerWidth <= 480 ? 180 : 300;
var labelScale = window.innerWidth <= 480 ? .6 : 0.9;
var labelScale = window.innerWidth <= 480 ? .6 : 0.9;

</script>
<script src="assets/js/appRadar.js"></script>
<script src="assets/js/radar.js"></script>
<script src="assets/js/radarDraw.js"></script>
</html>


