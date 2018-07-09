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
        <i>*To review your score for each individual domain, hover over the data point with your mouse.</i>
        <?php
          if(isset($loggedInUser->compare_all) && !$loggedInUser->compare_all){
        ?>
            <h3>Overall WELL-Being Score: <b><?php echo $loggedInUser->score ?>/100</b></h3>
        <?php
          }
        ?>
        <p>There are 10 constituent domains of well-being. The score for each domain can range from 0-10. A lower score in a domain can indicate: (1) that domain is not of important or of particular value to you, or (2) you have an opportunity for growth in that domain.</p>
        <p>The total score is the sum of the 10 domains. The goal is not to optimize your total score, but to optimize your scores on the domains of importance to you.</p>
         
         <h4>What does this chart mean?</h4>
         <p>*To review your score for each individual domain, hover over the data point with your mouse.There are 10 constituent domains of well-being. The score for each domain can range from 0-10. A lower score in a domain can indicate: (1) that domain is not of important or of particular value to you, or (2) you have an opportunity for growth in that domain.   The total score is the sum of the 10 domains. The goal is not to optimize your total score, but to optimize your scores on the domains of importance to you.</p>
          <p>“Domains that are in green text are domains that you indicated are most important to your well-being. Try to optimize your WELL score in those domains. Domains that are in red text are domains that are less important to your well-being. It is not as important for you to optimize your score in those areas.”</p>

    </div>
  </div>
</body>
<script src="assets/js/angular.js"></script>
<script src="assets/js/d3.v3.min.js"></script>

<script src="assets/js/appRadar.js"></script>
<script src="assets/js/radar.js"></script>
<script src="assets/js/radarDraw.js"></script>
</html>


