<?php
  include('models/config.php');
?>
<!DOCTYPE html>
<html ng-app="RadarChart">
<head>
  <meta charset="utf-8">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" />
  <link rel="stylesheet" href="assets/css/Radarstyle.css" />
</head>
<body class="container" ng-controller="MainCtrl as radar" data-csvfile="RadarUserCSV/<?php echo $loggedInUser->id; ?>Results">
  <div class="main container">
    <h2>Results Summary</h2>
    <div class="visualization">
        <radar csv="radar.csv" config="radar.config"></radar>
        <?php
          if(isset($loggedInUser->compare_all) && !$loggedInUser->compare_all){
        ?>
            <h3><?php echo lang("OVERALL_WELL_BEING_SCORE"); ?>: <b><?php echo $loggedInUser->score ?>/100</b></h3>
        <?php
            // print_rr($_SESSION['ranking']);
          }
          if(isset($_SESSION['ranking'])){
            foreach($_SESSION['ranking'] as $index => $domain){
                // print_rr($domain);
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


