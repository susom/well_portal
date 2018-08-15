<?php 
require_once("models/config.php"); 
include("models/inc/checklogin.php");
include("models/inc/scoring_functions.php");

$API_URL      = SurveysConfig::$projects["REDCAP_PORTAL"]["URL"];
$API_TOKEN    = SurveysConfig::$projects["REDCAP_PORTAL"]["TOKEN"];

// ONE TIME ACTION TO UPDATE the generated well_long_score_json variables.
if(isset($_REQUEST["action"])){
  $action = $_REQUEST["action"];
  if($action == "update_domain_json"){
    $extra_params = array(
       "content"  => "record"
      ,"type"     => "flat"
      ,"fields"   => array("well_long_score_json","id")
    );

    $records      = RC::callApi($extra_params, true, $API_URL, $API_TOKEN); 
    $records      = array_filter($records,function($record){
      return $record["well_long_score_json"] != "" && $record["well_long_score_json"] != "[]";
    });

    $data     = array();
    foreach($records as $record){
      $old_json = json_decode($record["well_long_score_json"],1);
      $changed   = false;
      
      if(isset($old_json["Financial Security"])){
        $old_json["Finances"] = $old_json["Financial Security"]; 
        unset($old_json["Financial Security"]);
        $changed = true;
      }

      if(isset($old_json["Lifestyle Behaviors"])){
        $old_json["Lifestyle and Daily Practices"] = $old_json["Lifestyle Behaviors"];
        unset($old_json["Lifestyle Behaviors"]);
        $changed = true;
      }

      if($changed){
        $data[]                 = array(    
           "record"             => $record["id"]
          ,"field_name"         => "well_long_score_json"
          ,"value"              => json_encode($old_json)
          ,"redcap_event_name"  => $record["redcap_event_name"]
        );
      }
    }

    if(!empty($data)){
      $updated = RC::writeToApi($data, array("overwriteBehavior" => "overwite", "type" => "eav"), $API_URL, $API_TOKEN); 
      print_rr($updated);
    }
  }else if($action == "create_combined_diet"){
    $extra_params = array(
       "content"  => "record"
      ,"type"     => "flat"
      ,"fields"   => array("well_score_ls_diet","well_score_ls_diet_old","id")
    );

    $records      = RC::callApi($extra_params, true, $API_URL, $API_TOKEN); 
    
    $data     = array();
    foreach($records as $record){
      $old_diet = $record["well_score_ls_diet_old"];
      $new_diet = $record["well_score_ls_diet"];
      
      $combined_diet = !empty($new_diet) && $new_diet != "N/A" && $new_diet != "NA" ? $new_diet : $old_diet;
      $data[]                 = array(    
         "record"             => $record["id"]
        ,"field_name"         => "well_score_ls_diet_c"
        ,"value"              => $combined_diet
        ,"redcap_event_name"  => $record["redcap_event_name"]
      );
    }

    if(!empty($data)){
      $updated = RC::writeToApi($data, array("overwriteBehavior" => "overwite", "type" => "eav"), $API_URL, $API_TOKEN); 
      print_rr($updated);
    }
  }else if($action == "calc_bmi_edu"){
    // CALCULATE BMI AND US EDUCATION CATAGORY
    $extra_params = array(
       "content"  => "record"
      ,"type"     => "flat"
      ,"fields"   => array("id","core_height_ft","core_height_inch","core_weight_lb","core_education_us_level","well_bmi","well_education")
    );
    $welldata      = RC::callApi($extra_params, true, $API_URL, $API_TOKEN); 
    foreach($welldata as $user){
      $heightinches   = $user["core_height_ft"]*12 + $user["core_height_inch"];
      $weightpounds   = $user["core_weight_lb"];
      $well_bmi       = empty($user["core_height_ft"]) || empty($user["core_weight_lb"]) ? "NA" : round(($weightpounds/pow($heightinches, 2))*703,4);

      switch($user["core_education_us_level"]){
        case 1:
          $category = 1;
        break;

        case 2:
        case 3:
        case 4:
        case 5: 
        case 6: 
        case 7: 
          $category = 2;
        break;

        case 8:
        case 9:
          $category = 3;
        break;

        default:
          $category = 4;
        break;
      }
      $well_education = $category;
      $data[] = array(
         "record"             => $user["id"]
        ,"redcap_event_name"  => $user["redcap_event_name"]
        ,"field_name"         => "well_bmi"
        ,"value"              => $well_bmi
      );
      $data[] = array(
         "record"             => $user["id"]
        ,"redcap_event_name"  => $user["redcap_event_name"]
        ,"field_name"         => "well_education"
        ,"value"              => $well_education
      );
    }
      
    if(!empty($data)){
      $updated = RC::writeToApi($data, array("overwriteBehavior" => "overwite", "type" => "eav"), $API_URL, $API_TOKEN); 
      print_rr($updated);
    }
  }else if($action == "scale_sub_domains"){
    $extra_params = array(
       "content"  => "record"
      ,"type"     => "flat"
      ,"fields"   => array("id","well_score_ls_diet","well_score_ls_diet_old","well_score_ls_sleep","well_score_ls_pa")
    );

    $records      = RC::callApi($extra_params, true, $API_URL, $API_TOKEN); 
    
    $data     = array();
    foreach($records as $record){
      $data[]                 = array(    
         "record"             => $record["id"]
        ,"field_name"         => "well_score_ls_diet"
        ,"value"              => $record["well_score_ls_diet"]*5
        ,"redcap_event_name"  => $record["redcap_event_name"]
      );
      $data[]                 = array(    
         "record"             => $record["id"]
        ,"field_name"         => "well_score_ls_diet_old"
        ,"value"              => $record["well_score_ls_diet_old"]*5
        ,"redcap_event_name"  => $record["redcap_event_name"]
      );
      $data[]                 = array(    
         "record"             => $record["id"]
        ,"field_name"         => "well_score_ls_sleep"
        ,"value"              => $record["well_score_ls_sleep"]*5
        ,"redcap_event_name"  => $record["redcap_event_name"]
      );
      $data[]                 = array(    
         "record"             => $record["id"]
        ,"field_name"         => "well_score_ls_pa"
        ,"value"              => $record["well_score_ls_pa"]*5
        ,"redcap_event_name"  => $record["redcap_event_name"]
      );
    }

    if(!empty($data)){
      print_rr($data);
      // $updated = RC::writeToApi($data, array("overwriteBehavior" => "overwite", "type" => "eav"), $API_URL, $API_TOKEN); 
      print_rr($updated);
    }
  }else if($action == "well_score_calc"){
    $short_q_fields  = array(
         //SOCIAL CONNECTEDNESS
         "core_lack_companionship"
        ,"core_people_upset"
        ,"core_energized_help"

        //Lifestyle BEHAVIORS
        ,"core_vegatables_intro_v2"
        ,"core_vegetables_intro_v2_1"
        ,"core_vegetables_intro_v2_2"
        ,"core_vegetables_intro_v2_3"
        ,"core_sugar_intro_v2"
        ,"core_sugar_intro_v2_1"
        ,"core_sugar_intro_v2_2"
        ,"core_sugar_intro_v2_3"
        ,"core_lpaq"
        ,"core_smoke_100"
        ,"core_smoke_freq"
        ,"core_sleep_quality"
        ,"core_bngdrink_female_freq"
        ,"core_bngdrink_male_freq"

        //STRESS AND RESILIENCE
        ,"core_important_energy"
        ,"core_deal_whatever"

        //EXPERIENCE OF EMOTIONS
        ,"core_joyful"
        ,"core_worried"

        //PHYSICAL HEALTH
        ,"core_fitness_level"

        //PURPOSE AND MEANING
        ,"core_contribute_doing"

        //SENSE OF SELF
        ,"core_satisfied_yourself"

        //FINANCIAL SECURITY/SATISFACTION
        ,"core_money_needs"

        //SPIRITUALITY AND RELIGION
        ,"core_religious_beliefs"

        //EXPLORATION AND CREATIVITY
        ,"core_engage_oppo"
    );

    $short_circuit_diff_ar = array(
         "core_contribute_doing"    => 1
        ,"core_satisfied_yourself"  => 1
        ,"core_money_needs"         => 1
        ,"core_religious_beliefs"   => 1
        ,"core_engage_oppo"         => 1
        ,"core_fitness_level"       => 1
        ,"core_important_energy"    => 1
        ,"core_deal_whatever"       => 1
        ,"core_joyful"              => 1
        ,"core_worried"             => 1
        ,"core_lack_companionship"  => 1
        ,"core_people_upset"        => 1
        ,"core_energized_help"      => 1
        ,"core_lpaq"                => 1
        ,"core_vegatables_intro_v2" => 1
        ,"core_sugar_intro_v2"      => 1
        ,"core_smoke_100"           => 1
        ,"core_sleep_quality"       => 1
    );

    $extra_params = array(
       "content"  => "record"
      ,"type"     => "flat"
      ,"fields"   => array("id","well_score","well_score_long")
    );
    $welldata      = RC::callApi($extra_params, true, $API_URL, $API_TOKEN); 
    
    $arms           = array(); //for updateing
    foreach($welldata as $item){
      $arm              = $item["redcap_event_name"];
      $arms[$arm][]     = $item["id"];
    }

    $calculated     = array();
    $scores         = array();
    foreach($arms as $arm => $ids){
      $use_short_scale    = strpos($arm,"short") > -1 ? true : false;  
      $recordids          = array_values($ids);

      // CALCULATE SHORT SCORES
      array_push($short_q_fields,"id");
      $extra_params = array(
         "content"  => "record"
        ,"type"     => "flat"
        ,"records"  => $recordids
        ,"fields"   => $short_q_fields
        ,"events"   => array($arm)
      );
      $score_components   = RC::callApi($extra_params, true, $API_URL, $API_TOKEN); 

      $data = array();
      foreach($score_components as $item){
          $user_completed_keys  = array_filter(array_intersect_key( $item,  array_flip($short_q_fields)),function($v){
              return $v !== false && !is_null($v) && ($v != '' || $v == '0');
          });
          $missing_data_keys    = array_diff_key($short_circuit_diff_ar,$user_completed_keys);
          
          if(checkMinimumForShortScore($missing_data_keys)){
              $score = getShortScore($user_completed_keys);
              $score = round(array_sum($score));
              //now push it back up to the server
              $data[] = array(
                   "id"            => $item["id"]
                  ,"redcap_event_name"    => $arm
                  ,"well_score"           => $score
              );
              $calculated[] = "WELL Score (Brief) of $score calculated for id #" . $item["id"] . " on $arm\n";
          }
      }
      $updated = RC::writeToApi($data, array("overwriteBehavior" => "overwite", "type" => "eav"), $API_URL, $API_TOKEN); 

      // CALCULATE SHORT SCORE FOR ALL , BUT FOR LONG SCORE ONLY ON LONG YEARS
      if(!$use_short_scale){
        //10 DOMAINS TO CALCULATE THE WELL LONG SCORE
        $domain_mapping = array(
           "well_score_creativity" => "Exploration and Creativity"
          ,"well_score_religion"   => "Spirituality and Religion"
          ,"well_score_financial"  => "Finances"
          ,"well_score_purpose"    => "Purpose and Meaning"
          ,"well_score_health"     => "Physical Health"
          ,"well_score_senseself"  => "Sense of Self"
          ,"well_score_emotion"    => "Experience of Emotions"
          ,"well_score_stress"     => "Stress and Resilience"
          ,"well_score_social"     => "Social Connectedness"
          ,"well_score_ls"         => "Lifestyle and Daily Practices"
        );

        $domain_fields  = array(
           "well_score_creativity"      => array("core_engage_oppo") 
          ,"well_score_religion"        => array("core_religious_beliefs")
          ,"well_score_financial"       => array("core_money_needs")

          ,"well_score_purpose"         => array("core_contribute_doing"
                                                ,"core_contribute_alive")

          ,"well_score_health"            => array("core_fitness_level"
                                                ,"core_health_selfreported"
                                                ,"core_physical_illness"
                                                ,"core_energy_level"
                                                ,"core_interfere_life")

          ,"well_score_senseself"       => array("core_true_person"
                                                ,"core_accepting_yourself"
                                                ,"core_satisfied_yourself"
                                                ,"core_capable"
                                                ,"core_daily_activities")

          ,"well_score_emotion"         => array("core_calm"
                                                ,"core_content"
                                                ,"core_drained"
                                                ,"core_excited"
                                                ,"core_frustrated"
                                                ,"core_happy"
                                                ,"core_hopeless"
                                                ,"core_joyful"
                                                ,"core_sad"
                                                ,"core_secure"
                                                ,"core_worried")

          ,"well_score_stress"      => array("core_bounce_back"
                                                ,"core_adapt_change"
                                                ,"core_deal_whatever"
                                                ,"core_humorous_side"
                                                ,"core_overcome_obstacles"
                                                ,"core_focused_pressure"
                                                ,"core_strong_person"
                                                ,"core_unpleasant_feelings"
                                                ,"core_disheartened_setbacks"
                                                ,"core_important_time"
                                                ,"core_confident_psnlproblem"
                                                ,"core_going_way"
                                                ,"core_overwhelm_difficult"
                                                ,"core_important_energy")

          ,"well_score_social"       => array("core_lack_companionship"
                                                ,"core_left_out"
                                                ,"core_isolated_others"
                                                ,"core_tune_people"
                                                ,"core_people_talk"
                                                ,"core_people_rely"
                                                ,"core_drained_helping"
                                                ,"core_people_close"
                                                ,"core_group_friends"
                                                ,"core_people_upset"
                                                ,"core_meet_expectations"
                                                ,"core_energized_help"
                                                ,"core_help")

          ,"well_score_ls"                => array("core_lpaq"
                                                ,"core_sleep_hh", "core_sleep_mm"
                                                ,"core_fallasleep_min"
                                                ,"core_fallasleep"
                                                ,"core_wokeup"
                                                ,"core_wokeup_early"
                                                ,"core_wokeup_unrefresh"
                                                ,"core_sleep_quality"
                                                
                                                ,"core_vegatables_intro"
                                                ,"core_desserts_intro" 
                                                ,"core_fastfood_day" 
                                                ,"core_sweet_drinks" 
                                                ,"core_processed_intro"

                                                ,"core_vegatables_intro_v2"
                                                ,"core_fruit_intro_v2"
                                                ,"core_grain_intro_v2"
                                                ,"core_bean_intro_v2"
                                                ,"core_sweet_intro_v2"
                                                ,"core_meat_intro_v2"
                                                ,"core_nuts_intro_v2"
                                                ,"core_sodium_intro_v2"
                                                ,"core_sugar_intro_v2"
                                                ,"core_fish_intro_v2"
                                                ,"core_cook_intro_v2"
                                                ,"core_fastfood_intro_v2"
                                                
                                                ,"core_bngdrink_female_freq"
                                                ,"core_bngdrink_male_freq"
                                                ,"core_smoke_100"
                                                ,"core_smoke_freq")
        );
        
        $long_q_fields = array();
        foreach($domain_fields as $domain => $components){
          $long_q_fields = array_merge($long_q_fields,array_values($components));
        }
        array_push($long_q_fields,"id");
        $extra_params = array(
           "content"  => "record"
          ,"type"     => "flat"
          ,"records"  => $recordids
          ,"fields"   => $long_q_fields
          ,"events"   => array($arm)
        );
        $score_components = RC::callApi($extra_params, true, $API_URL, $API_TOKEN); 

        $data = array();
        foreach($score_components as $item){
          $user_completed_keys  = array_filter($item,function($var){
            return ($var !== NULL && $var !== FALSE && $var !== '');
          });

          //MAKE SURE THAT AT LEAST 70% OF THE FIELDS IN EACH DOMAIN IS COMPLETE OR ELSE CANCEL THE SCORING
          $minimumData      = true;
          $no_well_score    = array();
          $no_well_score[]  = "No long well score was calculated for ID:".$item["id"];
          $single_dq        = array();
          $bng_ok           = true;
          $sleep_ok         = true;
          if(!isset($user_completed_keys["core_lpaq"])){
            $minimumData  = false;
            $single_dq[] = "<b>core_lpaq</b>";
          }
          if(!isset($user_completed_keys["core_bngdrink_female_freq"]) && !isset($user_completed_keys["core_bngdrink_male_freq"])){
            $minimumData  = false;
            $single_dq[]  = "<b>core_bngdrink</b>";
          }
          if(!isset($user_completed_keys["core_smoke_100"])){
            $minimumData  = false;
            $single_dq[]  = "<b>core_smoke_100</b>";
          }
          if(isset($user_completed_keys["core_smoke_100"]) && $user_completed_keys["core_smoke_100"] != 0 && !isset($user_completed_keys["core_smoke_freq"]) ){
            $minimumData  = false;
            $single_dq[] = "<b>core_smoke_freq</b>";
          }
          if(!empty($single_dq)){
            $no_well_score[] = "Missing domain questions : (" . implode(", ",$single_dq) .")";
          }

          $check_fields_dq = array();
          foreach($domain_fields as $domain => $fields){
            //TODO adjust threshold based on brancing language for binge drinking, smoking, sleep_mm and other conidtional /branching logics 
            if($domain == "well_score_ls"){
              if(isset($user_completed_keys["core_sleep_hh"])){
                $remove_index = array_search("core_sleep_mm",$fields);
                array_splice($fields,$remove_index,1);
              }
              if(isset($user_completed_keys["core_sleep_mm"])){
                $remove_index = array_search("core_sleep_hh",$fields);
                array_splice($fields,$remove_index,1);
              }

              if(isset($user_completed_keys["core_bngdrink_female_freq"])){
                $remove_index = array_search("core_bngdrink_male_freq",$fields);
                array_splice($fields,$remove_index,1);
              }
              if(isset($user_completed_keys["core_bngdrink_male_freq"])){
                $remove_index = array_search("core_bngdrink_female_freq",$fields);
                array_splice($fields,$remove_index,1);
              }
              if(isset($user_completed_keys["core_smoke_100"]) && $user_completed_keys["core_smoke_100"] < 1){
                $remove_index = array_search("core_smoke_freq",$fields);
                array_splice($fields,$remove_index,1);
              }       
            }
            $check_fields_dq = array_merge($check_fields_dq, $fields);
          }

          $additional_fields       = array("core_vegetables_intro_v2_1"
                                          ,"core_vegetables_intro_v2_2"
                                          ,"core_vegetables_intro_v2_3"
                                          ,"core_fruit_intro_v2_1"
                                          ,"core_fruit_intro_v2_2"
                                          ,"core_fruit_intro_v2_3"
                                          ,"core_grain_intro_v2_1"
                                          ,"core_grain_intro_v2_2"
                                          ,"core_grain_intro_v2_3"
                                          ,"core_bean_intro_v2_1"
                                          ,"core_bean_intro_v2_2"
                                          ,"core_bean_intro_v2_3"
                                          ,"core_sweet_intro_v2_1"
                                          ,"core_sweet_intro_v2_2"
                                          ,"core_sweet_intro_v2_3"
                                          ,"core_meat_intro_v2_1"
                                          ,"core_meat_intro_v2_2"
                                          ,"core_meat_intro_v2_3"
                                          ,"core_nuts_intro_v2_1"
                                          ,"core_nuts_intro_v2_2"
                                          ,"core_nuts_intro_v2_3"
                                          ,"core_sodium_intro_v2_1"
                                          ,"core_sodium_intro_v2_2"
                                          ,"core_sodium_intro_v2_3"
                                          ,"core_sugar_intro_v2_1"
                                          ,"core_sugar_intro_v2_2"
                                          ,"core_sugar_intro_v2_3"
                                          ,"core_fish_intro_v2_1"
                                          ,"core_fish_intro_v2_2"
                                          ,"core_fish_intro_v2_3"
                                          ,"core_cook_intro_v2_1"
                                          ,"core_cook_intro_v2_2"
                                          ,"core_cook_intro_v2_3"
                                          ,"core_fastfood_intro_v2_1"
                                          ,"core_fastfood_intro_v2_2"
                                          ,"core_fastfood_intro_v2_3"
                                          ,"core_vegatables_intro"
                                          ,"core_desserts_intro" 
                                          ,"core_fastfood_day" 
                                          ,"core_sweet_drinks" 
                                          ,"core_processed_intro" 
                                        );
          // DAMNIT TOHELL, GOTTA DO THIS PROCESS AGAIN SINCE THE ABOVE ISNT USED FOR THE "minimum data"
          $extra_params = array(
             "content"  => "record"
            ,"type"     => "flat"
            ,"records"  => array($user_completed_keys["id"])
            ,"fields"   => $additional_fields
            ,"events"   => array($arm)
          );
          $additional_components    = RC::callApi($extra_params, true, $API_URL, $API_TOKEN); 
          $check_user_completed_dq  = $user_completed_keys;
          $user_completed_keys      = array_merge($user_completed_keys, $additional_components[0]);
          
          $temp                     = getLongScores($domain_fields, $user_completed_keys);
          $long_scores              = $temp["scores"];
          $sub_scores               = array_pop($long_scores);
          $pos_neg_subscores        = $temp["pos_neg_subscores"];
          $pos_neg_vals             = array_filter($pos_neg_subscores,'allowZeroFilter');

          if(!empty($long_scores) || !empty($sub_scores)){
            // save individual scores

            // 4 CASES WHERE WE DO NOT CALC WELL SCOre
            // 1. ANY MISSING DOMAIN (eg < 10)
            // 2. ANy MISSING SuB DOmAIN IN LifeSTYLE (eg < 5)
            // 4. ANY MISSING POS/NEG SUBSCore of EMotions/Stress (eg < 4)
            // 3. 30% missing questions total (hmm ah below, gotta branch with new or old diet)
            if(!array_key_exists("well_score_ls_diet",$sub_scores) && array_key_exists("well_score_ls_diet_old",$sub_scores)){
              $remove_from_fields = array( "core_vegatables_intro_v2"
                      ,"core_fruit_intro_v2"
                      ,"core_grain_intro_v2"
                      ,"core_bean_intro_v2"
                      ,"core_sweet_intro_v2"
                      ,"core_meat_intro_v2"
                      ,"core_nuts_intro_v2"
                      ,"core_sodium_intro_v2"
                      ,"core_sugar_intro_v2"
                      ,"core_fish_intro_v2"
                      ,"core_cook_intro_v2"
                      ,"core_fastfood_intro_v2" );
            }else{
              $remove_from_fields = array("core_vegatables_intro"
                      ,"core_desserts_intro" 
                      ,"core_fastfood_day" 
                      ,"core_sweet_drinks" 
                      ,"core_processed_intro");
            }
            $check_fields_dq = array_diff($check_fields_dq, $remove_from_fields);
            $dq_threshold   = ceil(count($check_fields_dq) * .3);
            $missing_keys   = array_diff($check_fields_dq, array_keys($check_user_completed_dq));
            
            if(count($long_scores) < 10 || count($sub_scores) < 5 || count($pos_neg_vals) < 4 || count($missing_keys) >= $dq_threshold){
              $minimumData = false;
            }

            // SAVE ALL THE SUB SCORES
            $save_scores  = array_merge($long_scores,$sub_scores,$pos_neg_subscores); 
            $subdata      = array("id" => $user_completed_keys["id"], "redcap_event_name" => $arm);
            foreach($save_scores as $redcap_var => $value){
              $subdata[$redcap_var] = $value;
            }
            $component_data = $subdata;
            $updated        = RC::writeToApi($subdata, array("overwriteBehavior" => "overwite", "type" => "eav"), $API_URL, $API_TOKEN); 

            // PREPARE JSON BLOCK FOR RADAR CHART MAPPING
            $remapped_long_scores = array();
            foreach($long_scores as $rc_var => $value){
              $remapped_long_scores[$domain_mapping[$rc_var]] = $value;
            }

            // ONLY SAVE SCORES IF MINIMUM DATA PASS
            if($minimumData){
              $data[] = array(
                 "id"                   => $user_completed_keys["id"]
                ,"redcap_event_name"    => $arm
                ,"well_long_score_json" => json_encode($remapped_long_scores)
                ,"well_score_long"      => round(array_sum($remapped_long_scores),4)
              );
              $calculated[] = "WELL Score (Long) of ".round(array_sum($remapped_long_scores))."/100 calculated for id #" . $user_completed_keys["id"] . " on $arm\n";
            }else{
              // ELSE SAVE AS N/A
              $data[] = array(
                 "id"                   => $user_completed_keys["id"]
                ,"redcap_event_name"    => $arm
                ,"well_score_long"      => "N/A"
              );
              $calculated[] = "WELL Score (Long) could not be calculated for id #" . $user_completed_keys["id"] . " on $arm\n";
            }
          }
        }
        $updated  = RC::writeToApi($data, array("overwriteBehavior" => "overwite", "type" => "eav"), $API_URL, $API_TOKEN); 
        print_rr($updated);
      }
    }
    $calculated[] =  "All elgibile WELL Scores have been calculated";
    echo implode("<hr>",$calculated);
  }
}else{
  print_rr("need to add action");
}
    