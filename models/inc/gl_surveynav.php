<aside class="col-sm-12 col-md-4">
    <ul class="nav">
        <li class="surveys container">
            <h4 class="mini_header"><?php echo lang("MY_WELL") ?></h4>
            <ol class="core_supp row">
                <?php
                // markPageLoadTime("BEGIN GL_surveynav");
                $new                    = null;
                $core_surveys           = array();
                $completed_surveys      = array();
                $suppsurvs              = array();

                $title_trans            = $project_notes["translations"];
                $umbrella_sid           = "wellbeing_questions";
                $surveyname             = lang("STANFORD_WELL");

                foreach($core_instrument_ids as $instrument_id){
                  $surveycomplete       = in_array($instrument_id,$completed_timestamps);
                  $linksid              = $surveycomplete ? $umbrella_sid : $instrument_id;
                  $surveylink           = "survey.php?sid=" . $linksid;

                  $instrument_label     = $_SESSION["use_lang"] !== "en" && isset($title_trans[$_SESSION["use_lang"]][$instrument_id]) ?  $title_trans[$_SESSION["use_lang"]][$instrument_id] :  $surveyname;
                  $instrument_label     = str_replace("_"," ",$instrument_label);
                  $surveylabel          = ucwords(str_replace("And","&",$instrument_label));
                  $newbadge             = is_null($new) && !$surveycomplete ? "<b class='badge bg-danger pull-right'>new!</b>" :"<b class='badge bg-danger pull-right'>new!</b>";

                  if(!$surveycomplete){
                    $next_survey  =  $surveylink;
                    break;
                  }
                }

                if(in_array($umbrella_sid, $core_instrument_ids)){
                  // if we are on survey page for supplemental survey , that means core surveys are complete.
                  if(!empty($pid) && array_key_exists($pid, SurveysConfig::$projects)){
                    $surveycomplete = true;
                  }
                  $incomplete_complete = $surveycomplete ? "completed_surveys" : "core_surveys";
                  array_push($$incomplete_complete, "<li class='".$surveyon[$umbrella_sid]." col-sm-12'>
                    <a href='$surveylink' class='auto points_survey' title='".$surveylabel."'>
                      $newbadge                                                   
                      <span class='fruit'></span>
                      <span class='survey_name'>$surveyname</span>     
                    </a>
                  </li>\n");
                }
                echo implode("",$core_surveys);

                // CUSTOM FLOW FOR UO1 Pilot STUDY
                $uo1 = array(  "how_well_do_you_sleep"
                              ,"find_out_your_body_type_according_to_chinese_medic");
//                $uo1_completes = array();
//                foreach($uo1 as $supp_instrument_id){
//                  if(isset($supp_instruments[$supp_instrument_id]) && $supp_instruments[$supp_instrument_id]["survey_complete"]){
//                    $uo1_completes[] = 1;
//                  }
//                }
                $uo1_complete = true;//array_sum($uo1_completes) == 2 ? true : false;

                $fitness           = SurveysConfig::$supp_icons;
                $supp_title_trans  = $supp_project_notes["translations"];
                $supp_tooltips     = $supp_project_notes["tooltips"];
                foreach($supp_instrument_ids as $supp_instrument_id){
                  $surveycomplete     = in_array($supp_instrument_id,$completed_timestamps);

                  $instrument_label   = $_SESSION["use_lang"] != "en" && isset($supp_title_trans[$_SESSION["use_lang"]][$supp_instrument_id]) ?  $supp_title_trans[$_SESSION["use_lang"]][$supp_instrument_id] :  SurveysConfig::$supp_surveys[$supp_instrument_id];
                  $instrument_label   = str_replace("_"," ",$instrument_label);

                  $surveylabel        = ucwords(str_replace("And","&",$instrument_label));
                  $iconcss            = $fitness[$supp_instrument_id];

                  // CUSTOM FLOW FOR UO1 Pilot STUDY
                  if($core_surveys_complete && isset($user_arm_answers["core_group_id"]) && $user_arm_answers["core_group_id"] == 1001){
                    $custom_flow = true;
                    if($uo1_complete){
                      $custom_flow = false;
                    }else{
                      if(in_array($supp_instrument_id,$uo1)){
                        $custom_flow = false;
                      }
                    }
                  }else{
                    $custom_flow = false;
                  }

                  $titletext          = $core_surveys_complete && !$custom_flow ? $supp_tooltips[$supp_instrument_id] : $lang["COMPLETE_CORE_FIRST"];
                  $surveylink         = $core_surveys_complete && !$custom_flow ? "survey.php?sid=". $supp_instrument_id. "&project=Supp"  : "#";
                  $na                 = $core_surveys_complete && !$custom_flow ? "" : "na"; //"na"

                  $icon_update                        = " icon_update";
                  $survey_alinks[$supp_instrument_id] = "<a href='$surveylink' class='points_survey ".(!$surveycomplete ? "" : "")."' title='$titletext'>$surveylabel</a>";
                  $incomplete_complete                = $surveycomplete ? "completed_surveys" : "suppsurvs";

                  if(!empty($na)){
                    array_push($$incomplete_complete,  "<li class='fitness $na $icon_update $iconcss  ".$surveyon[$supp_instrument_id]." col-sm-12'>
                                      ".$survey_alinks[$supp_instrument_id]." 
                                  </li>");
                  }else{
                    array_unshift($$incomplete_complete,  "<li class='fitness $na $icon_update $iconcss  ".$surveyon[$supp_instrument_id]." col-sm-12'>
                                      ".$survey_alinks[$supp_instrument_id]." 
                                  </li>");
                  }
                }

                $proj_name      = "foodquestions";
                if(!$core_surveys_complete){
                  // JUST PUSH DUMMY TEXT
                  $a_nutrilink  = "<a href='#' class='nutrilink points_survey points_none' title='".$lang["TAKE_BLOCK_DIET"]."' target='_blank'>".$lang["HOW_WELL_EAT"]."</a>"; // &#128150
                  array_unshift($suppsurvs ,"<li class='fitness na food'>".$a_nutrilink."</li>");
                }else{
                  // THIS IS EXPENSIVE OPERATION, DONT DO IT EVERYTIME, AND DONT BOTHER UNLESS CORE SURVEY IS COMPLETE
                  if(isset($_SESSION[$proj_name])){
                    $ffq = $_SESSION[$proj_name];
                  }else{
                    $ffq_project  = new PreGenAccounts($loggedInUser, $proj_name, SurveysConfig::$projects[$proj_name]["URL"], SurveysConfig::$projects[$proj_name]["TOKEN"]);
                    $_SESSION[$proj_name] = $ffq = $ffq_project->getAccount();
                  }
                  if(!array_key_exists("error",$ffq)){
                    $nutrilink      = $portal_test ? "#" : "https://www.nutritionquest.com/login/index.php?username=".$ffq["ffq_username"]."&password=".$ffq["ffq_password"]."&BDDSgroup_id=747&Submit=Submit";
                    $a_nutrilink    = "<a href='$nutrilink' class='nutrilink points_survey' title='".$lang["TAKE_BLOCK_DIET"]."' target='_blank'>".$lang["HOW_WELL_EAT"]."</a>"; // &#128150
                    if($_SESSION["use_lang"] !== "sp"){
                      array_unshift($suppsurvs ,"<li class='fitness food'>".$a_nutrilink."</li>");
                    }
                  }
                }
                echo implode("",$suppsurvs);
                ?>
            </ol>

            <h4 class="mini_header"><?php echo lang("COMPLETED_SURVEYS") ?></h4>
            <ol class="completed row">
            <?php
              echo implode("",$completed_surveys);
              // markPageLoadTime("END GL_surveynav");
            ?>
            </ol>

            <h4 class="mini_header"><?php echo lang("NAV_CHALLENGES") ?></h4>
            <ol class="mini_challenges row">
                <?php
                $extra_params   = array(
                    'content'       => 'record',
                    'format'        => 'json',
                    "fields"        => array("id", "portal_mc_name","portal_mc_year","portal_mc_link"),
                    "filterLogic"   => "[portal_mc_name] != '' AND [portal_mc_year] = '".Date("Y")."'"
                );
                $minics        = RC::callApi($extra_params, true, SurveysConfig::$projects["ADMIN_CMS"]["URL"], SurveysConfig::$projects["ADMIN_CMS"]["TOKEN"]);

                foreach($minics as $minic){
                    $shortname = strtolower($minic["portal_mc_name"]);
                    $shortname = str_replace(" ","",$shortname);
                    $shortname = str_replace("-","",$shortname);
                    $shortname = str_replace("and","",$shortname);
                    $shortname = str_replace(",","",$shortname);
                    echo "<li class='mini_".$shortname." col-sm-4 col-md-12'><a href='".$minic["portal_mc_link"]."' class='points_mini_challenge' data-challenge='mini_".$shortname."' target='_blank'>".$minic["portal_mc_name"]."</a></li>\n";
                }
                ?>
            </ol>
        </li>
    </ul>
</aside>
<script>
$(document).ready(function(){
    $(".survey h4.mini_header").next().slideUp("fast");

    $("h4.mini_header").click(function(){
        if($(this).next().is(":hidden")){
            $(this).next().slideDown();
        }else{
            $(this).next().slideUp();
        }
    });
})
</script>
