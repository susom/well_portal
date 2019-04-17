	<?php include_once("gl_socialfoot.php") ?>
</div>
</div>
<div id="dialog" class="">
    <div class="dialog_inner">

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

    function unlockWOF(){
        var gamehref = "game.php";
        $("#dialog .dialog_inner").removeClass("celebrate").empty();
        var title   = $("<h3>").html("<?php echo lang("WOF_UNLOCKED") ?>");
        var p1      = $("<p>").html("<?php echo lang("WOF_UNLOCKED_BODY") ?>");
        var btn     = $("<a>").html("<?php echo lang("WOF_UNLOCKED_BTN") ?>").data("action","openmini").addClass("btn-success").addClass("btn").attr("href",gamehref);
        var cancel  = $("<button>").addClass("btn btn-danger").addClass("btn btn-danger").html("<?php echo lang("CANCEL") ?>");
        $("#dialog .dialog_inner").append(title);
        $("#dialog .dialog_inner").append(p1);
        $("#dialog .dialog_inner").append(btn);
        $("#dialog .dialog_inner").append(cancel);
        $("#dialog").addClass("show").addClass("celebrate");
        return;
    }

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

    //OPEN MINI CHALLENGE
    $("#dialog").on("click",".btn",function(e){
        $("#dialog").removeClass("show").removeClass("celebrate");
        if($(this).data("action") == "openmini"){
            var minihref= $(this).attr("href");
            window.open(minihref, "_blank");
        }

        e.preventDefault();
    });

    //RECORD POINTS FOR MINI C
    $(".mini_challenges a").on("click",function(e){
        var minihref= $(this).attr("href");

        $("#dialog .dialog_inner").empty();
        var title   = $("<h3>").html("<?php echo lang("WELL_CHALLENGE") ?>");
        var p1      = $("<p>").html("<?php echo lang("WELL_CHALLENGE_BODY1") ?>");
        var p2      = $("<p>").text("<?php echo lang("WELL_CHALLENGE_BODY2") ?>");
        var btn     = $("<a>").html("<?php echo lang("WELL_CHALLENGE_BTN") ?>").data("action","openmini").addClass("btn-success").addClass("btn").attr("href",minihref);
        var cancel  = $("<button>").addClass("btn btn-danger").addClass("btn btn-danger").html("<?php echo lang("CANCEL") ?>");
        $("#dialog .dialog_inner").append(title);
        $("#dialog .dialog_inner").append(p1);
        $("#dialog .dialog_inner").append(p2);
        $("#dialog .dialog_inner").append(btn);
        $("#dialog .dialog_inner").append(cancel);
        $("#dialog").addClass("show");


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

        e.preventDefault();
    });

    <?php
        echo "var wof_status = " . ((!isset($loggedInUser->portal_wof_unlocked) || !$loggedInUser->portal_wof_unlocked) ? 0 : 1) . ";\r\n";
        echo "console.log(wof_status);";

        if( $one_off_wof_unlocked ){
            ?>
            unlockWOF();
            <?php
            }
        ?>

    $("body").on('DOMSubtreeModified', "#global_points a", function() {
        //maybe ajax to see if its been called once yet?
        console.log(wof_status);

        if(parseInt($(this).text()) >=5000 && !wof_status){
            wof_status = 1;
            unlockWOF();
        }
    });


    var js_update_points = <?php echo isset($js_update_points) ? $js_update_points : 0 ; ?>;
    if(js_update_points){
        var curpoints = parseInt($("#global_points a").text());
        curpoints = curpoints + parseInt(js_update_points);
        $("#global_points a").text(curpoints);
    }
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
