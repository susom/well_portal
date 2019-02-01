	<?php include_once("gl_socialfoot.php") ?>
</div>
</div>
<script>window.jQuery || document.write('<script src="assets/js/vendor/jquery-1.11.2.min.js"><\/script>')</script>
<!-- Bootstrap -->
<script src="assets/js/bootstrap.js"></script>
<script src="assets/js/jquery.maskedinput.min.js"></script>
<script src="assets/js/slimscroll/jquery.slimscroll.min.js"></script>
<script src="assets/js/charts/easypiechart/jquery.easy-pie-chart.js"></script>
<script src="assets/js/charts/sparkline/jquery.sparkline.min.js"></script>
<script src="assets/js/charts/flot/jquery.flot.min.js"></script>
<script src="assets/js/charts/flot/jquery.flot.tooltip.min.js"></script>
<script src="assets/js/charts/flot/jquery.flot.spline.js"></script>
<script src="assets/js/charts/flot/jquery.flot.pie.min.js"></script>
<script src="assets/js/charts/flot/jquery.flot.resize.js"></script>
<script src="assets/js/charts/flot/jquery.flot.grow.js"></script>
<script src="assets/js/charts/flot/demo.js"></script>
<script src="assets/js/calendar/bootstrap_calendar.js"></script>
<script src="assets/js/calendar/demo.js"></script>
<script src="assets/js/app.plugin.js"></script> 
<script src="assets/js/jquery.visible.min.js"></script>
<script src="assets/js/jquery.simpleWeather.js"></script>
<script src="assets/js/weather.js"></script>
<script src="assets/js/roundslider.js"></script>
<script src="assets/js/verify.notify.min.js"></script>
<script src="assets/js/underscore-min.js"></script>
</body>
</html>
<script>
$(document).ready(function(){
    <?php
    foreach($game_points as $point_type => $values){
        $vals = json_decode($values,1);
        if(!empty($vals)){
            echo "var " .$point_type." = " . $values . ";\n";
        }
    }
    ?>

    //CHECK SESSION BEFORE SCORING
    // .resourceEntry a
    // .resource_links figcaption a

    // .points_mini_challenge
    // .core_supp a

    //ANY LINK GETS 1 POINT PER SESSION EXCEPT
    //points_none
    $("a:not(.points_none)").click(function(){
        var clicked     = $(this).attr("href");

        var minic_      = $(this).hasClass("points_mini_challenge");
        var survey_     = $(this).hasClass("points_survey");
        var resources_  = $(this).hasClass("points_resources");

        var click_val   = parseInt(gamify_pts_any.value);
        var persist_pts = "";
        if(minic_){
            persist_pts = "&persist=" + parseInt(gamify_pts_minichallenge.value);
        }else if(survey_){
            persist_pts = "&persist=" + parseInt(gamify_pts_survey.value);
        }else if(resources_){
            persist_pts = "&persist=" + parseInt(gamify_pts_resources.value);
        }

        console.log("action=session_points&value="+click_val+"&link="+clicked + persist_pts);

        $.ajax({
            url : "ajax_handler.php",
            type: 'POST',
            data: "action=session_points&value="+click_val+"&link="+clicked + persist_pts,
            datatype: 'JSON',
            success:function(result){
                console.log(result);
                //update the points box
                var result = JSON.parse(result);
                if(result.hasOwnProperty("points_added")){
                    var curpoints = parseInt($("#global_points a").text());
                    curpoints = curpoints + parseInt(result.points_added);
                    $("#global_points a").text(curpoints);
                }
            }
        });
    });

    $(".mini_challenges a").on("click",function(){
        var dataurl = "&mini_clicked=" + $(this).parent("li").attr("class");
        $.ajax({
            url:  "index.php",
            type:'POST',
            dataType: "JSON",
            data: dataurl,
            success:function(result){
                console.log(result);
            }
        });
        return;
    });

});


$(document).on('click', function(event) {
    if ($(event.target).not("[target='blank']").closest('.alert').length && !$(event.target).closest("#confirm_email").length) {
        $(".alert").fadeOut("fast",function(){
          $(".alert").remove();
        });
    }

    if (typeof(Storage) !== "undefined" && window.sessionStorage) {
        // CHECK FOR sessionStorage
        if(sessionStorage.getItem("resumeUploads")){
            var something = JSON.parse(localStorage.getItem("resumeUploads"));
        }else{

        }
    }
});

$("a.disabled").click(function(){
  return false;
});

// this will wait to show alert boxes until after page loads
$(".alert").css("opacity",1);
</script>
