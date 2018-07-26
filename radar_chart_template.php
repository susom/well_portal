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

<script src="assets/js/appRadar.js"></script>
<script src="assets/js/radar.js"></script>
<script src="assets/js/radarDraw.js"></script>
</html>


