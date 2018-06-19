<?php 
require_once("models/config.php"); 
include("models/inc/checklogin.php");
include("models/domain_descriptions.php");

//SITE NAV
$navon  = array("home" => "", "reports" => "on", "game" => "", "resources" => "", "rewards" => "", "activity" => "");

//GET ALL DATA FOR SUPP INSTRUMENTS IN ALL AVAILABLE ARMS
$extra_params = array(
  'content'   => 'event',
);
$result = RC::callApi($extra_params, true, REDCAP_API_URL, REDCAP_API_TOKEN);

foreach($result as $event){
  if($event["unique_event_name"] == $user_event_arm){
    //ALREADY HAVE THIS YEAR SO DONT WASTE RESOURCE GETTING IT
    $events[$event["unique_event_name"]] = $supp_instruments;
    break;
  }else{
    //WILL NEED TO GET RESULTS FROM PREVIOUS YEARS TO SHOW RESULTS/FEEDBACK
    $r_supplementalProject    = new Project($loggedInUser, "Supp", SurveysConfig::$projects["Supp"]["URL"], SurveysConfig::$projects["Supp"]["TOKEN"], $event["unique_event_name"]);
    $r_suppsurveys            = $r_supplementalProject->getActiveAll(); 
    $r_supp_surveys["Supp"]   = $r_supplementalProject;
  
    $r_supp_instruments       = array();
    foreach($r_supp_surveys as $projname => $supp_project){
      $r_supp_instruments   = array_merge( $r_supp_instruments, $supp_project->getActiveAll() );
    } 
    $events[$event["unique_event_name"]] = $r_supp_instruments;
  }
}
$firstyear  = Date("Y", $consent_date);

//SET UP ARRAY OF COMPLETED REPORTS TO USE FOR NAV STATE
$supp_surveys_keys = array();
foreach($events as $arm=> $event){
  if(!array_key_exists($arm,$supp_surveys_keys)){
    $supp_surveys_keys[$arm] = array("wellbeing_questions" => "");
  }
  foreach(array_keys($event) as $instrument_id){
    // $supp_surveys_keys[] = $instrument_id."_".$armyears[$arm];
    $supp_surveys_keys[$arm][$instrument_id] = "";
  }
};

//IF CORE SURVEY GET THE SURVEY ID
if(isset($_REQUEST["arm"]) && $_REQUEST["arm"] == 'ALL'){
  $compare_all = true;
  $sid_arm = $current_arm;
}else{
  $sid_arm = isset($_REQUEST["arm"]) ? $_REQUEST["arm"] : $current_arm; 
}

$sid     = $current_surveyid = isset($_REQUEST["sid"]) ? $_REQUEST["sid"] : "wellbeing_questions";
$supp_surveys_keys[$sid_arm][$sid] = "on";

//GENDER VAR NEEDED FOR ASSESMENTS
$core_gender    = $loggedInUser->gender == 5 || $loggedInUser->gender == 3 ? "m" : "f";

$pageTitle = "Well v2 Assessments";
$bodyClass = "reports";
include_once("models/inc/gl_head.php");
?>
    <div class="main-container">
        <div class="main wrapper clearfix">   
            <aside>
                <h3><?php echo lang("CORE_SURVEYS") ?></h3>
                <ul class="nav">
                    <li class="surveys">
                        <ol>
                            <?php
                            $suppsurvs  = array();
                            $viewlink   = array();
                     
                            foreach($events as $armname => $supp_instruments_event){
                              $fitness  = SurveysConfig::$supp_icons;
                              $index    = -1;
                              $armyear  = $armyears[$armname];
                              $suppsurvs[$armyear][]  = "<ol>";

                              $is_nav_on  = $supp_surveys_keys[$armname]["wellbeing_questions"];
                              $WELL_SCALE = strpos($armname,"short") > -1 ? lang("BRIEF_WELL") : lang("STANFORD_WELL");
                              $survey_alinks["wellbeing_questions"] = "<a class='assessments' href='reports.php?sid=wellbeing_questions&arm=$armname' data-year=$armyear>$WELL_SCALE</a>";
                              $suppsurvs[$armyear][]  = "<li class='assesments fruits $is_nav_on'>
                                                  ".$survey_alinks["wellbeing_questions"]." 
                                              </li>";

                              foreach($supp_instruments_event as $supp_instrument_id => $supp_instrument){
                                $index++;
                                
                                // only want to show link if there are results available in any year
                                if(!$supp_instrument["survey_complete"]){
                                  continue;
                                }

                                //if bucket is A make sure that three other ones are complete before showing.
                                $projnotes    = json_decode($supp_instrument["project_notes"],1);
                                $title_trans  = $projnotes["translations"];
                                $tooltips     = $projnotes["tooltips"];
                                $surveyname   = isset($title_trans[$_SESSION["use_lang"]][$supp_instrument_id]) ?  $title_trans[$_SESSION["use_lang"]][$supp_instrument_id] : $supp_instrument["label"];
                                $iconcss      = $fitness[$supp_instrument_id];
                                $titletext    = $tooltips[$supp_instrument_id];
                                $surveylink   = "?sid=". $supp_instrument_id ."&arm=$armname" ;
                                $icon_update  = " icon_update";
                                $completed    = json_encode($supp_instrument["completed_fields"]);
                                
                                $is_nav_on    = $supp_surveys_keys[$armname][$supp_instrument_id];
                                $survey_alinks[$supp_instrument_id] = "<a href='$surveylink' title='$titletext' data-sid='$supp_instrument_id' data-completed='$completed' data-year=$armyear>$surveyname</a>";
                                $assessmentsclass = $supp_instrument_id !== "international_physical_activity_questionnaire" ? "assessments" :"";
                                
                                $list         = "<li class='$assessmentsclass fitness  $icon_update $iconcss $is_nav_on'>
                                                    ".$survey_alinks[$supp_instrument_id]." 
                                                </li>";
                                if(!array_key_exists($supp_instrument_id,$suppsurvs)){
                                  $suppsurvs[$armyear][]  = $list;
                                }

                                if($is_nav_on == "on"){
                                  $year = $armyears[$armname];
                                  $viewlink = "<a class='viewassessment' href='$surveylink' title='$titletext' data-sid='$supp_instrument_id' data-completed='$completed' target='theFrame'>$year $surveyname</a>";
                                }
                              }
                              $suppsurvs[$armyear][]  = "</ol>";
                            }

                            if(count($suppsurvs)){
                              krsort($suppsurvs);

                              foreach($suppsurvs as $armyear => $html){
                                $default_open = $armyears[$sid_arm] == $armyear ? "open" : "";
                                echo "<details $default_open>";
                                echo "<summary>$armyear</summary>";
                                echo implode("",$html);
                                echo "</details>";
                              }
                            }else{
                                echo "<i>None Available</i>";
                            }
                            //EDIT HERE
                            ?>
                        </ol>

                        <h4><?php echo lang("CERTIFICATES") ?></h4>
                        <ol>
                          <?php
                            $filename         = array();
                            $filename[]       = $loggedInUser->id;
                            $filename[]       = $loggedInUser->firstname;
                            $filename[]       = $loggedInUser->lastname;
                            $user_folder      = implode("_",$filename);

                            $cert_year        = array();
                            // var comes from surveys.php
                            while($firstyear <= $this_year){
                              $curyear        = $firstyear;
                              $file_cert      = "PDF/certs/$user_folder/" . $user_folder . "_$curyear.pdf";
                              if(file_exists($file_cert)){
                                $cert_year[] = "<li class='nofruit'><a class='certcomplete' target='blank' href='$file_cert'>$curyear</a></li>";
                              }
                              $firstyear++;
                            }
                            rsort($cert_year);
                            echo implode("\n",$cert_year);
                          ?>
                        </ol>
                        <h4>Compare Results</h4>
                        <a href='reports.php?sid=wellbeing_questions&arm=ALL' data-year=$armyear>WELL Comparison</a> 


                    </li>
                </ul>
            </aside>
            <article>
                <script src="assets/js/custom_assessments.js"></script>
                <div id="results" class="assessments">
                <?php 
                if(count($suppsurvs)){
                    if(!empty($sid)){
                        echo "<div id='results'>";
                        switch($sid){
                            case "wellbeing_questions":
                              $well_score   = strpos($sid_arm,"short") > -1 ? "well_score" : "well_long_score_json" ;
                              
                              if(isset($compare_all) && $compare_all == true){ //only when clicking on the compare tab: 
                                $extra_params = array(
                                'content'     => 'record',
                                'records'     => array($loggedInUser->id) ,
                                'fields'      => array("id",$well_score),
                                'events'      => array('enrollment_arm_1','anniversary_2_arm_1')
                                );
                                $loggedInUser->compare_all = true;
                              }else{
                                $extra_params = array(
                                  'content'     => 'record',
                                  'records'     => array($loggedInUser->id) ,
                                  'fields'      => array("id",$well_score),
                                  'events'      => $sid_arm
                                );
                                $loggedInUser->compare_all = false;
                              }

                                  $user_ws      = RC::callApi($extra_params, true, $_CFG->REDCAP_API_URL, $_CFG->REDCAP_API_TOKEN); 
                              if(strpos($sid_arm,"short") > -1){
                                $brief_score = $user_ws[0]["well_score"];
                                if(!empty($brief_score)){
                                  echo "<blockquote>".lang("BRIEF_SCORE",array($armyears[$sid_arm], $brief_score*2)). "</blockquote>";
                                  echo lang("BREIF_DISCLAIMER");
                                }else{
                                  echo lang("BRIEF_SORRY_NA");
                                }
                              }else{
                                $csv_data = "group, axis, value, description\n";
                                foreach($user_ws as $e_arms){
                                  $count_year = $armyears[$e_arms["redcap_event_name"]];
                                  $long_scores = json_decode($e_arms["well_long_score_json"],1);
                                  
                                  if(!empty($long_scores)){
                                    $users_file_csv = "RadarUserCSV/".$loggedInUser->id."Results.csv";
                                    
                                    $ct = 0;
                                    foreach ($long_scores as $key => $value){
                                      $display = $value;
                                      $display = number_format($display,2,".","");
                                      $ct++;
                                      $csv_data .= "Year ".$count_year.", ". $key .", ". $display .", ". $domain_desc[$ct]."\n";
                                    }
                                    $sum_long_score = round(array_sum($long_scores));
                                    $loggedInUser->score = $sum_long_score;
                                    $count_year++;

                                  }//ifempty
                                  file_put_contents($users_file_csv, $csv_data);
                                   
                                }

                                  ?>
                                    <object type = "text/html" data = "radar_chart_template.php" width=100%></object>
                                  <?php
                                  // Display their domain Ranking
                                  if($compare_all == false){
                                    $domain_ranking_arm = isset($_GET["arm"]) ? $_GET["arm"] : $user_event_arm;
                                    $API_URL      = SurveysConfig::$projects["REDCAP_PORTAL"]["URL"];
                                    $API_TOKEN    = SurveysConfig::$projects["REDCAP_PORTAL"]["TOKEN"];
                                    $data = array(
                                          'content'     => 'record',
                                          "events"    => $domain_ranking_arm,
                                              'records'     => array($loggedInUser->id),
                                              'fields'      => array("domainorder_ec", "domainorder_lb", "domainorder_sc","domainorder_sr", "domainorder_ee",
                                                           "domainorder_ss", "domainorder_ph", "domainorder_pm","domainorder_fs","domainorder_rs")
                                    );
                                    $result = RC::callApi($data, true, $API_URL , $API_TOKEN);
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
                                    $redcap_variables = array(
                                      "0" => "domainorder_ec",
                                      "1" => "domainorder_lb",
                                      "2" => "domainorder_sc",
                                      "3" => "domainorder_sr",
                                      "4" => "domainorder_ee",
                                      "5" => "domainorder_ss",
                                      "6" => "domainorder_ph",
                                      "7" => "domainorder_pm",
                                      "8" => "domainorder_fs",
                                      "9" => "domainorder_rs"
                                    );
                                    if(!empty($result[0]["domainorder_ec"])){
                                      $dom = ($result[0]);
                                      asort($dom);
                                      echo "<h3 style='text-align:center'>".lang("YOUR_DOMAIN_RANKING")."</h3>";
                                      echo "<ol>";
                                      foreach($dom as $k => $val){
                                          $k--;
                                          $key = array_search($k,$redcap_variables);
                                         echo "<li id ='".$radar_domains[$key]."'>".$radar_domains[$key]."</li>";
                                      }
                                      echo "</ol>";
                                    }
                                  }
                              }
                            break;

                            case "international_physical_activity_questionnaire":
                                $API_TOKEN    = SurveysConfig::$projects["Supp"]["TOKEN"];
                                $API_URL      = SurveysConfig::$projects["Supp"]["URL"];
                                $extra_params = array(
                                  'content'     => 'record',
                                  'records'     => [$loggedInUser->id] ,
                                  'fields'      => ["ipaq_total_overall"],
                                  'events'      => $sid_arm
                                );
                                $result         = RC::callApi($extra_params, true, $API_URL, $API_TOKEN); 
                                $ipaq           = isset($result[0]["ipaq_total_overall"]) ? $result[0]["ipaq_total_overall"] : "N/A";
                                ?>
                                <div id="ipaq_results">
                                    <h3><?php echo lang("YOUR_MET") ?>: <b><?php echo $ipaq ?></b></h3>
                                </div>
                                <?php
                            break;

                            default:
                              echo $viewlink;
                            break;

                        }
                        echo "</div>";
                    }
                }else{
                    echo "<p><i>".lang("NO_ASSESSMENTS")."</i></p>";
                }
                ?>

                </div>
            </article>
        </div> <!-- #main -->
    </div> <!-- #main-container -->
<?php 
include_once("models/inc/gl_foot.php");
?>
<style>
blockquote{
  font-size:200%;
  padding:0;
  border:0;
  text-align:center;
}
blockquote b{
  display:block; 
  padding:20px 0;
}
details {
  margin-bottom:50px;
}
.main > aside a.certcomplete {
    display:inline-block;
    vertical-align: bottom;
    padding-left:48px;
    height:32px;
    background:url(PDF/ico_cert_completion.png) 0 0 no-repeat;
    background-size:37px 30px;
    line-height:170%;
}
.main > aside li li.nofruit{
  padding-left:10px;
}
.main > aside li li.nofruit:hover{
  background:none;
}
.main > aside li li.nofruit:before {
  display:none;
}

#ipaq_results{
    background:#f2f2f2;
    border-radius:10px;
    padding:20px;
    box-shadow: 2px 2px 5px #999;
}
#ipaq_results h3{
    color:#000;
}
.viewassessment {
  opacity:0;
  position:absolute;
  left:-5000px;
}

summary{
  font-size:130%;
}

object{
  height:800px !important;
}
</style>
<script src="assets/js/custom_assessments.js"></script>
<script>
<?php 
echo "var uselang   = '" . $_SESSION["use_lang"] . "';\n";
echo "var poptitle  = '".lang("YOUR_ASSESSMENT")."';\n";
?>
function centeredNewWindow(title,insertHTML,styles,scrips,bottom_scrips){
  var winwidth        = Math.round(.85 * $( window ).width() );
  var winheight       = Math.round(.85 * $( window ).height() );
  var dualScreenLeft  = window.screenLeft != undefined ? window.screenLeft : screen.left;
  var dualScreenTop   = window.screenTop != undefined ? window.screenTop : screen.top;
  var width           = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
  var height          = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;
  var left            = ((width / 2) - (winwidth / 2)) + dualScreenLeft;
  var top             = ((height / 2) - (winheight / 2)) + dualScreenTop;
  
  var newwin          = window.open("",'theFrame','toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width='+winwidth+', height=' + winheight + ', top=' + top + ', left=' + left);
  newwin.document.write('<html><head>');
  newwin.document.write('<title>'+title+'</title>');
  for(var i in styles){
    var linkhref = styles[i].href;
    if(linkhref){
      newwin.document.write('<link rel="stylesheet" type="text/css" href="'+linkhref+'">');
    }
  }
  newwin.document.write('<style>#content { margin:20px; }</style>');
  newwin.document.write('</head><body>');
  newwin.document.write('<div id="content" style="color:#222">');
  newwin.document.write('<h2 style="margin:0 0 40px; padding-bottom:10px; color:#333; border-bottom:1px solid #999">'+poptitle+' : "'+title+'"</h2>');
  newwin.document.write(insertHTML);
  newwin.document.write('</div>');
  for(var i in scrips){
    var scriptsrc = scrips[i].src;
    var newscript = document.createElement('script');
    newscript.src = scriptsrc;
    if(scriptsrc){
      newwin.document.body.appendChild(newscript);
    }
  }
  newwin.document.write(bottom_scrips);
  newwin.document.write('</body></html>');
  return;
}

$(document).ready(function(){
  $("summary").click(function(){
    $("details").removeAttr("open");
  });

  $(".viewassessment").click(function(){
    var sid       = $(this).data("sid");
    var udata     = $(this).data("completed");
    var title     = $(this).text();

    //get proper styles/ sripts and div containers for the pop up page
    var sheets    = document.styleSheets;
    var scrips    = document.scripts;
    var bottom_scrips = "";

    switch(sid){
      case "find_out_your_body_type_according_to_chinese_medic":
        showTCMScoring(udata, function(resultData){
          bottom_scrips += "<script>";
          bottom_scrips += "setTimeout(function(){";
          bottom_scrips += "$('.constitution dt').click(function(){";
          bottom_scrips += "if($(this).next('dd').is(':visible')){";
          bottom_scrips += "$(this).next('dd').slideUp();";
          bottom_scrips += "}else{";
          bottom_scrips += "$(this).next('dd').slideDown();";
          bottom_scrips += "}";
          bottom_scrips += "return false;";
          bottom_scrips += "});";
          bottom_scrips += "},1500);";
          bottom_scrips += "<\/script>";

          centeredNewWindow(title,resultData,sheets,scrips,bottom_scrips);
        });
      break;

      case "how_well_do_you_sleep":
        showSleepScoring(udata,function(resultData){
          bottom_scrips += "<script>";
          bottom_scrips += "setTimeout(function(){";
          bottom_scrips += "  $('#psqi_slider').roundSlider({";
          bottom_scrips += "    sliderType: 'min-range',";
          bottom_scrips += "    handleShape: 'square',";
          bottom_scrips += "    circleShape: 'half-top',";
          bottom_scrips += "    showTooltip: false,";
          bottom_scrips += "    handleSize: 0,";
          bottom_scrips += "    radius: 120,";
          bottom_scrips += "    width: 14,";
          bottom_scrips += "    min: 0,";
          bottom_scrips += "    max: 21,";
          bottom_scrips += "    value: 10";
          bottom_scrips += "  });";
          bottom_scrips += "},2000);";
          bottom_scrips += "<\/script>";

          centeredNewWindow(title,resultData,sheets,scrips,bottom_scrips);
        });
      break;

      case "how_fit_are_you":
        showMETScoring(udata,function(resultData){
          bottom_scrips += "<style>";
          bottom_scrips += "#met_results {opacity:1}";
          bottom_scrips += "<\/style>";

          bottom_scrips += "<script>";
          bottom_scrips += "$('a.moreinfo').click(function(){";
          bottom_scrips += "  var content   = $(this).parent().next();";
          bottom_scrips += "  content.slideDown('medium');";
          bottom_scrips += "});";
          bottom_scrips += "$('a.closeparent').click(function(){";
          bottom_scrips += "  $(this).parent().slideUp('fast');";
          bottom_scrips += "});";
          bottom_scrips += "<\/script>";
           
          centeredNewWindow(title,resultData,sheets,scrips,bottom_scrips);
        });
      break;

      case "how_resilient_are_you_to_stress":
        udata["core_gender"] = '<?php echo $core_gender ?>';
        showGRITScoring(udata,function(resultData){
          bottom_scrips += "<script>";
          bottom_scrips += "setTimeout(function(){";
          bottom_scrips += "var _this = $('#grit_results');";
          bottom_scrips += "customGRIT_BS(_this);";
          bottom_scrips += "},1500);";
          bottom_scrips += "<\/script>";
          centeredNewWindow(title,resultData,sheets,scrips,bottom_scrips);
        });
      break;

      case "how_physically_mobile_are_you":
        showMATScoring(udata,function(resultData){
          var mat_score_desc = {
             40  : '<?php echo lang("MAT_SCORE_40") ?>'
            ,50  : '<?php echo lang("MAT_SCORE_50") ?>'
            ,60  : '<?php echo lang("MAT_SCORE_60") ?>'
            ,70  : '<?php echo lang("MAT_SCORE_70") ?>'
          };

          var matscore  = resultData.value;
          if(matscore < 40){
              var picperc = 7;
              var desc = mat_score_desc[40];
          }else if(matscore < 50){
              var picperc = 5;
              var desc = mat_score_desc[50];
          }else if(matscore < 60){
              var picperc = 3;
              var desc = mat_score_desc[60];
          }else{
              var picperc = 0;
              var desc = mat_score_desc[70];
          }

          var makeHTML   = "<div id='mat_results'>";
          makeHTML      += "  <div id='matscore'></div>";
          makeHTML      += "  <div id='mat_pic'>";
          makeHTML      += "    <ul>";
          for(var i = 0;i < 10; i++){
            if(i < picperc){
              makeHTML      += "      <li class='dead'></li>";
            }else{
              makeHTML      += "      <li></li>";
            }
          }
          makeHTML      += "    </ul>";
          makeHTML      += "  </div>";
          makeHTML      += "  <div id='mat_text'>"+desc+"</div>";
          makeHTML      += "</div>";

          bottom_scrips += "<script>";
          bottom_scrips += "setTimeout(function(){";
          bottom_scrips += "var _this = $('#mat_results');";
          bottom_scrips += "customMAT_BS(_this);";
          bottom_scrips += "},1500);";
          bottom_scrips += "<\/script>";
          centeredNewWindow(title,makeHTML,sheets,scrips,bottom_scrips);
        });
      break;
    }
    return false;
  });

  $(".viewassessment").trigger("click");
});
</script>
