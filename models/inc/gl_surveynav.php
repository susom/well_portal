<aside>
    <h3><?php echo lang("MY_WELL") ?></h3>
    <ul class="nav">
        <li class="surveys">
            <h4><?php echo lang("CORE_SURVEYS") ?></h4>
            <ol>
                <?php
                $new = null;
                $core_surveys           = array();
                $supp_surveys           = array();
                $completed_surveys      = array();
                $suppsurvs              = array();
                $fruits                 = SurveysConfig::$fruits;
                $iconcss                = "";
                if(isset($sid)){
                  $index   = array_search($sid, $all_survey_keys);
                  $iconcss = $fruits[$index];
                }
                
                $umbrella_sid     = "wellbeing_questions";
                foreach($surveys as $surveyid => $survey){
                  $surveycomplete = $survey["survey_complete"];
                  $index          = array_search($surveyid, $all_survey_keys);
                  $projnotes      = json_decode($survey["project_notes"],1);
                  $title_trans    = $projnotes["translations"];
                  $linksid        = $surveycomplete ? $umbrella_sid : $surveyid;
                  $surveylink     = "survey.php?sid=" . $linksid;

                  $longorshort    = $user_short_scale ? lang("BRIEF_WELL") : lang("STANFORD_WELL");
                  //every one of the core surveys will be labled "wellbeing_questions"
                  $surveyname     = isset($title_trans[$_SESSION["use_lang"]][$umbrella_sid]) ?  $title_trans[$_SESSION["use_lang"]][$umbrella_sid] : $longorshort;

                  $completeclass  = ($surveycomplete ? "completed":"");
                  $hreflink       = (is_null($new) || $surveycomplete ? "href" : "rel");
                  $newbadge       = (is_null($new) && !$surveycomplete ? "<b class='badge bg-danger pull-right'>new!</b>" :"<b class='badge bg-danger pull-right'>new!</b>");

                  if(!$surveycomplete && is_null($new)){
                    // $new = $index;
                    $next_survey  =  $surveylink;
                    break;
                  }
                }

                if($user_short_scale){
                  $umbrella_sid = "brief_well_for_life_scale";
                }
                if(in_array($umbrella_sid, $available_instruments)  || $user_short_scale){
                   // if we are on survey page for supplemental survey , that means core surveys are complete. 
                  if(!empty($pid) && array_key_exists($pid, SurveysConfig::$projects)){
                    $surveycomplete = true;
                    $hreflink       = "href";
                    $surveylink     = "survey.php?sid=wellbeing_questions";
                  }
                  $incomplete_complete = $surveycomplete ? "completed_surveys" : "core_surveys";
                  array_push($$incomplete_complete, "<li class='".$surveyon[$umbrella_sid]."  $iconcss'>
                      <a $hreflink='$surveylink' class='auto' title='".$survey["label"]."'>
                        $newbadge                                                   
                        <span class='fruit $completeclass'></span>
                        <span class='survey_name'>$surveyname</span>     
                      </a>
                    </li>\n");
                }
                echo implode("",$core_surveys);
                
                // CUSTOM FLOW FOR UO1 Pilot STUDY
                $uo1 = array(  "how_well_do_you_sleep"
                              ,"find_out_your_body_type_according_to_chinese_medic");
                $uo1_completes = array();
                foreach($uo1 as $supp_instrument_id){
                  if(isset($supp_instruments[$supp_instrument_id]) && $supp_instruments[$supp_instrument_id]["survey_complete"]){
                    $uo1_completes[] = 1;
                  }
                }
                $uo1_complete = array_sum($uo1_completes) == 2 ? true : false;
                
                $fitness    = SurveysConfig::$supp_icons;
                foreach($supp_instruments as $supp_instrument_id => $supp_instrument){
                    //if bucket is A make sure that three other ones are complete before showing.
                    $projnotes    = json_decode($supp_instrument["project_notes"],1);
                    $title_trans  = $projnotes["translations"];
                    $tooltips     = $projnotes["tooltips"];
                    $surveyname   = isset($title_trans[$_SESSION["use_lang"]][$supp_instrument_id]) ?  $title_trans[$_SESSION["use_lang"]][$supp_instrument_id] : $supp_instrument["label"];
                    $iconcss      = $fitness[$supp_instrument_id];
                    
                    // CUSTOM FLOW FOR UO1 Pilot STUDY
                    if($core_surveys_complete && isset($all_completed["core_group_id"]) && $all_completed["core_group_id"] == 1001){
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

                    $titletext    = $core_surveys_complete && !$custom_flow ? $tooltips[$supp_instrument_id] : $lang["COMPLETE_CORE_FIRST"];
                    $surveylink   = $core_surveys_complete && !$custom_flow ? "survey.php?sid=". $supp_instrument_id. "&project=" . $supp_instrument["project"] : "#";
                    $na           = $core_surveys_complete && !$custom_flow ? "" : "na"; //"na"

                    $icon_update  = " icon_update";
                    $survey_alinks[$supp_instrument_id] = "<a href='$surveylink' title='$titletext'>$surveyname</a>";
                    
                    $incomplete_complete = $supp_instrument["survey_complete"] ? "completed_surveys" : "suppsurvs";
 
                    if(!empty($na)){
                      array_push($$incomplete_complete,  "<li class='fitness $na $icon_update $iconcss  ".$surveyon[$supp_instrument_id]."'>
                                        ".$survey_alinks[$supp_instrument_id]." 
                                    </li>");
                    }else{
                      array_unshift($$incomplete_complete,  "<li class='fitness $na $icon_update $iconcss  ".$surveyon[$supp_instrument_id]."'>
                                        ".$survey_alinks[$supp_instrument_id]." 
                                    </li>");
                    }
                }

                $proj_name  = "foodquestions";
                if(!$core_surveys_complete){
                  // JUST PUSH DUMMY TEXT
                  $a_nutrilink = "<a href='#' class='nutrilink' title='".$lang["TAKE_BLOCK_DIET"]."' target='_blank'>".$lang["HOW_WELL_EAT"]."</a>"; // &#128150 
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
                    $a_nutrilink    = "<a href='$nutrilink' class='nutrilink' title='".$lang["TAKE_BLOCK_DIET"]."' target='_blank'>".$lang["HOW_WELL_EAT"]."</a>"; // &#128150 
                    if($_SESSION["use_lang"] !== "sp"){
                      array_unshift($suppsurvs ,"<li class='fitness food'>".$a_nutrilink."</li>");
                    }
                  }
                }
                echo implode("",$suppsurvs);
                ?>  
            </ol>
            <h4><?php echo lang("COMPLETED_SURVEYS") ?></h4>
            <ol class="completed">
            <?php
              echo implode("",$completed_surveys);
            ?>
            </ol>
        </li>
    </ul>
</aside>
