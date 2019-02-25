<!DOCTYPE html>
<html ng-app="RadarChart">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" />
    <link rel="stylesheet" href="../assets/css/Radarstyle.css" />
</head>
<body class="container" ng-controller="MainCtrl as radar" data-csvfile="../RadarUserCSV/<?php echo $csv_file ?>Results">
<div class="main container">
    <h2><?php echo lang("RESULTS_SUMMARY"); ?></h2>
    <div class="visualization">
        <radar csv="radar.csv" config="radar.config"></radar>
        <?php
        // only display score for single year views
        if($display_year !== 99){
            echo "<h3>".lang("OVERALL_WELL_BEING_SCORE").": <b>".$well_sum_score."</b></h3>";

            if($well_sum_score !== "NA"){
                echo "<h4>" . lang("RADAR_CHART_MEANING") . "</h4>";
                echo "<p>" . lang("RADAR_CHART_REVIEW") . "</p>";
                echo "<p>" . lang("RADAR_CHART_DOMAINS") . "</p>";
            }else{
                echo "<p>" . lang("SCORE_NA") . "</p>";
            }
        }

        ?>
    </div>
</div>
</body>
<style>
.visualization {text-align: center;}
</style>
<script src="../assets/js/angular.js"></script>
<script src="../assets/js/d3.v3.min.js"></script>
<script>
    var radar_w = window.innerWidth <= 480 ? 280 : 440;
    var radar_h = window.innerWidth <= 480 ? 280 : 440;
    var radar_x = window.innerWidth <= 480 ? 70  : 300;
    var radar_y = window.innerWidth <= 480 ? 180 : 300;
    var labelScale = window.innerWidth <= 480 ? .6 : 0.9;
    var labelScale = window.innerWidth <= 480 ? .6 : 0.9;

</script>
<script src="../assets/js/appRadar.js"></script>
<script src="../assets/js/radar.js"></script>
<script src="../assets/js/radarDraw.js"></script>
</html>