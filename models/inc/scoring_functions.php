<?php
function calculateLongScore($loggedInUser, $user_event_arm, $_CFG, $all_completed){
  // CHECK IF EXISTING LONG SCORE
  $extra_params = array(
    'content'     => 'record',
    'records'     => array($loggedInUser->id) ,
    'fields'      => array("id","well_long_score_json"),
    'events'      => $user_event_arm
  );
  $user_ws      = RC::callApi($extra_params, true, $_CFG->REDCAP_API_URL, $_CFG->REDCAP_API_TOKEN); 
  
  $long_score   = 0;
  if(true|| !isset($user_ws[0]) || (isset($user_ws[0]) && empty( json_decode($user_ws[0]["well_long_score_json"],1) )) ){
    //10 DOMAINS TO CALCULATE THE WELL LONG SCORE
   
    $domain_mapping = array(
       "well_score_creativity" => "Exploration and Creativity"
      ,"well_score_religion"   => "Spirituality and Religion"
      ,"well_score_financial"  => "Financial Security"
      ,"well_score_purpose"    => "Purpose and Meaning"
      ,"well_score_health"     => "Physical Health"
      ,"well_score_senseself"  => "Sense of Self"
      ,"well_score_emotion"    => "Experience of Emotions"
      ,"well_score_stress"     => "Stress and Resilience"
      ,"well_score_social"     => "Social Connectedness"
      ,"well_score_ls"         => "Lifestyle Behaviors"
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

      ,"lifestyle"                => array("core_lpaq"
                                            ,"core_sleep_hh", "core_sleep_mm"
                                            ,"core_fallasleep_min"
                                            ,"core_fallasleep"
                                            ,"core_wokeup"
                                            ,"core_wokeup_early"
                                            ,"core_wokeup_unrefresh"
                                            ,"core_sleep_quality"
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
    
    //JUST GET THE INDIVIDUAL FIELDS
    $q_fields   = array();
    foreach($domain_fields as $domains){
      $q_fields = array_merge($q_fields, array_values($domains));
    }

    //INTERSECT ALL USER COMPLETED FIELDS WITH THE REQUIRED ONES TO GET THE USER ANSWERS
    $user_completed_keys = array_filter(array_intersect_key( $all_completed, array_flip($q_fields) ),function($v){
        return $v !== false && !is_null($v) && ($v != '' || $v == '0');
    });

    //MAKE SURE THAT AT LEAST 70% OF THE FIELDS IN EACH DOMAIN IS COMPLETE OR ELSE CANCEL THE SCORING
    $minimumData = true;
    foreach($domain_fields as $domain => $fields){

      //DO THE DQ_THRESHOLD PER DOMAIN
      //CALCULATE AND SAVE FOR EACH DOMAIN THAT DOES PASS 
      
      $dq_threshold   = count($fields) * .3; 
      $missing_keys   = array_diff($fields, array_keys($user_completed_keys)) ;
      if(count($missing_keys) > $dq_threshold){
        $minimumData  = false;
      }
    }

    $q_fields = array_merge($q_fields, array("core_vegetables_intro_v2_1"
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
                                          ) );

    // DAMNIT TOHELL, GOTTA DO THIS PROCESS AGAIN SINCE THE ABOVE ISNT USED FOR THE "minimum data"
    $user_completed_keys = array_filter(array_intersect_key( $all_completed, array_flip($q_fields) ),function($v){
        return $v !== false && !is_null($v) && ($v != '' || $v == '0');
    });

    // TRY TO CALCULATE SUBDOMAIN SCORES REGARDLESS
    $temp = getLongScores($domain_fields, $user_completed_keys);
    $long_scores  = $temp["scores"];
    $sub_scores   = $temp["pos_neg_subscores"];

    // MAYBE I DONT EVEN NEED MINIMUM DATA CHECK OVERALL ANYMORE?
    // if(!$minimumData){
    //   $long_scores = array();
    // }

    // save individual scores
    if(!array_key_exists ( "well_score_ls" , $long_scores )){
      $minimumData = false;
    }
    foreach($long_scores as $redcap_var => $value){
      if($redcap_var == "ls_sub_domains"){
        foreach($value as $rc_var => $val){
          $data = array(
            "record"            => $loggedInUser->id,
            "field_name"        => $rc_var,
            "value"             => $val,
            "redcap_event_name" => $user_event_arm
          );
          $result =  RC::writeToApi(array($data), array("overwriteBehavior" => "overwite", "type" => "eav"), $_CFG->REDCAP_API_URL, $_CFG->REDCAP_API_TOKEN);
        }
      }else{
        $data = array(
          "record"            => $loggedInUser->id,
          "field_name"        => $redcap_var,
          "value"             => $value,
          "redcap_event_name" => $user_event_arm
        );
        $result = RC::writeToApi(array($data), array("overwriteBehavior" => "overwite", "type" => "eav"), $_CFG->REDCAP_API_URL, $_CFG->REDCAP_API_TOKEN);
      }
    }

    // save the entire block as json
    array_pop($long_scores);
    $remapped_long_scores = array();
    foreach($long_scores as $rc_var => $value){
      $remapped_long_scores[$domain_mapping[$rc_var]] = $value;
    }

    if($minimumData){
      $data = array(
        "record"            => $loggedInUser->id,
        "field_name"        => "well_long_score_json",
        "value"             => json_encode($remapped_long_scores),
        "redcap_event_name" => $user_event_arm
      );
      $result = RC::writeToApi(array($data), array("overwriteBehavior" => "overwite", "type" => "eav"), $_CFG->REDCAP_API_URL, $_CFG->REDCAP_API_TOKEN);

      $long_score = round(array_sum($remapped_long_scores),4);
      $data = array(
        "record"            => $loggedInUser->id,
        "field_name"        => "well_score_long",
        "value"             => $long_score,
        "redcap_event_name" => $user_event_arm
      );
      $result = RC::writeToApi(array($data), array("overwriteBehavior" => "overwite", "type" => "eav"), $_CFG->REDCAP_API_URL, $_CFG->REDCAP_API_TOKEN);
    }
      
    //write the subscores to 
    foreach($sub_scores as $well_var => $well_val){
      $data = array(
        "record"            => $loggedInUser->id,
        "field_name"        => $well_var,
        "value"             => $well_val,
        "redcap_event_name" => $user_event_arm
      );
      $result =  RC::writeToApi(array($data), array("overwriteBehavior" => "overwite", "type" => "eav"), $_CFG->REDCAP_API_URL, $_CFG->REDCAP_API_TOKEN);
    }
  }else{
    $remapped_long_scores = json_decode($user_ws[0]["well_long_score_json"],1);
    $long_score = round(array_sum($remapped_long_scores),4);
  }
  return $long_score;
}

function getLongScores($domain_fields, $user_completed_fields){
  // 10 domains
  // Each domain counts for 10 points
  // Total score is 100
  $score  = array();
  $emo_positive_dom     = null;
  $emo_negative_dom     = null;
  $stress_positive_dom  = null;
  $stress_negative_dom  = null;
  foreach($domain_fields as $domain => $fields){
    $flipped_answered = array_flip($fields);
    $num_fields       = count($fields);
    $num_answered     = count(array_intersect_key($flipped_answered,$user_completed_fields));
    $non_answered     = $num_fields - $num_answered;
    $dq_num           = ceil($num_fields*.3);

    switch($domain){
      case "well_score_creativity" :
      case "well_score_religion" :
      case "well_score_financial" :
        $field        = array_pop($fields);
        if(isset($user_completed_fields[$field])){
          $denom = $domain == "well_score_financial" ? 5 : 4;
          $score[$domain] = round(10*($user_completed_fields[$field] - 1)/$denom,4);
        }
      break;
            
      case "well_score_health" :
        if($non_answered < $dq_num){
          $domain_items = array();
          foreach($fields as $field){
            $denom          = $field == "core_fitness_level" ? 5 : 4;
            if(!isset($user_completed_fields[$field])){
              continue;
            }

            if($field == "core_interfere_life"){
              $domain_items[] = (5-$user_completed_fields[$field])/$denom;
            }else{
              $domain_items[] = ($user_completed_fields[$field]-1)/$denom;
            }
          }
          $temp_score     = 2*array_sum($domain_items);
          $score[$domain] = round(scaleDomainScore($temp_score, count($domain_items), count($fields)),4);
        }
      break;
      
      case "well_score_purpose" :
      case "well_score_senseself" :
        if($non_answered < $dq_num){
          $domain_items = array();
          foreach($fields as $field){
            if(!isset($user_completed_fields[$field])){
              continue;
            }
            $denom = 4;
            $domain_items[] = ($user_completed_fields[$field]-1)/$denom;
          }
          $weight = $domain == "well_score_senseself" ? 2 : 5;
          $temp_score     = $weight*array_sum($domain_items);
          $score[$domain] = round(scaleDomainScore($temp_score, count($domain_items), count($fields)),4);
        }
      break;
      
      case "well_score_emotion" :
        if($non_answered < $dq_num){
          $domain_items = array();
          $pos_items    = array();
          $neg_items    = array();
          foreach($fields as $field){
            if(!isset($user_completed_fields[$field])){
              continue;
            }

            $denom = 4;
            if($field == "core_drained" || $field == "core_frustrated" || $field == "core_hopeless" || $field == "core_sad" || $field == "core_worried"){
              $neg_items[] = $domain_items[] = (5-$user_completed_fields[$field])/$denom;
            }else{
              $pos_items[] = $domain_items[] = ($user_completed_fields[$field]-1)/$denom;
            }
          }

          $emo_positive_dom   = (5/6)*(array_sum($pos_items));
          $emo_negative_dom   = (5/5)*(array_sum($neg_items));

          $temp_score     = $emo_positive_dom + $emo_negative_dom; //(10/11)*(array_sum($domain_items));
          $score[$domain] = round(scaleDomainScore($temp_score, count($domain_items), count($fields)),4);
        }
      break;
      
      case "well_score_stress" :
        if($non_answered < $dq_num){
          $domain_items = array();
          $pos_items    = array();
          $neg_items    = array();
          foreach($fields as $field){
            if(!isset($user_completed_fields[$field])){
              continue;
            }

            $denom = 4;
            if($field == "core_important_time" || $field == "core_overwhelm_difficult" || $field == "core_important_energy" || $field == "core_confident_psnlproblem" || $field == "core_going_way"){
              if($field == "core_confident_psnlproblem" || $field == "core_going_way"){
                $neg_items[] = $domain_items[] = ($user_completed_fields[$field]-1)/$denom;
              }else{
                $neg_items[] = $domain_items[] = (5-$user_completed_fields[$field])/$denom;
              }
            }else{
              $pos_items[] = $domain_items[] = ($user_completed_fields[$field]-1)/$denom;
            }
          }

          $stress_positive_dom   = (5/9)*(array_sum($pos_items));
          $stress_negative_dom   = (5/5)*(array_sum($neg_items));

          $temp_score     = $stress_positive_dom + $stress_negative_dom;//(10/14)*(array_sum($domain_items));
          $score[$domain] = round(scaleDomainScore($temp_score, count($domain_items), count($fields)),4);
        }
      break;
      
      case "well_score_social" :
        if($non_answered < $dq_num){
          $domain_items = array();
          foreach($fields as $field){
            if(!isset($user_completed_fields[$field])){
              continue;
            }
            $denom = 4;
            if($field == "core_lack_companionship" || $field == "core_left_out" || $field == "core_isolated_others" || $field == "core_drained_helping" || $field == "core_people_upset" || $field == "core_meet_expectations"){
              $domain_items[] = (5-$user_completed_fields[$field])/$denom;
            }else{
              $domain_items[] = ($user_completed_fields[$field]-1)/$denom;
            }
          }
          $temp_score     = (10/13)*(array_sum($domain_items));
          $score[$domain] = round(scaleDomainScore($temp_score, count($domain_items), count($fields)),4);
        }
      break;
      
      case "lifestyle" :
        $calc_lifestyle = true;
        $domain_items   = array();

        //physical activity
        if(isset($user_completed_fields["core_lpaq"])){
          $domain_items["well_score_ls_pa"] = round(2*($user_completed_fields["core_lpaq"]-1)/5,4);
        }else{
          $calc_lifestyle = false;
        }

        //alchohol
        if(isset($user_completed_fields["core_bngdrink_male_freq"]) || isset($user_completed_fields["core_bngdrink_female_freq"])){
          $domain_items["well_score_ls_alchohol"] = ((isset($user_completed_fields["core_bngdrink_male_freq"]) && $user_completed_fields["core_bngdrink_male_freq"] == 1) || (isset($user_completed_fields["core_bngdrink_female_freq"]) && $user_completed_fields["core_bngdrink_female_freq"]) ) ? 0 : 2;
        }else{
          $calc_lifestyle = false;
        }
        
        //smoking
        if(isset($user_completed_fields["core_smoke_100"])){
          $domain_items["well_score_ls_smoke"] = ( $user_completed_fields["core_smoke_100"] == 0 || ($user_completed_fields["core_smoke_100"] == 1 && $user_completed_fields["core_smoke_freq"] == 1) ) ? 2 : 0;
        }else{
          $calc_lifestyle = false;
        }

        //sleep
        $sleep_req  = array( "core_sleep_hh"                   => 1
                            ,"core_sleep_mm"                   => 1
                            ,"core_fallasleep_min"             => 1
                            ,"core_fallasleep"                 => 1
                            ,"core_wokeup"                     => 1
                            ,"core_wokeup_early"               => 1
                            ,"core_wokeup_unrefresh"           => 1
                            ,"core_sleep_quality"              => 1
                          );
        $num_fields       = count($sleep_req);
        $num_answered     = count(array_intersect_key($sleep_req,$user_completed_fields));
        $non_answered     = $num_fields - $num_answered;
        if(!isset($user_completed_fields["core_sleep_hh"]) || !isset($user_completed_fields["core_sleep_mm"])){
          // these 2 are seperate fields but count only as 1
          $num_fields--;
        }
        $dq_num           = ceil($num_fields*.3);
        if($non_answered < $dq_num){
          $sleep_score = array();
          if(isset($user_completed_fields["core_sleep_hh"]) && isset($user_completed_fields["core_sleep_mm"])){
            $core_sleep_total   = 60*$user_completed_fields["core_sleep_hh"] + $user_completed_fields["core_sleep_mm"];
            $sleep_score[]      = $core_sleep_total >= 420 && $core_sleep_total <= 540 ? 5/5 : 0;
          }
          if(isset($user_completed_fields["core_fallasleep_min"])){
            $sleep_score[]      = (7-$user_completed_fields["core_fallasleep_min"])/6;
          }
          if(isset($user_completed_fields["core_fallasleep"])){
            $sleep_score[]      = (5-$user_completed_fields["core_fallasleep"])/4;
          }
          if(isset($user_completed_fields["core_wokeup"])){
            $sleep_score[]      = (5-$user_completed_fields["core_wokeup"])/4;
          }
          if(isset($user_completed_fields["core_wokeup_early"])){
            $sleep_score[]      = (5-$user_completed_fields["core_wokeup_early"])/4;
          }
          if(isset($user_completed_fields["core_wokeup_unrefresh"])){
            $sleep_score[]      = (5-$user_completed_fields["core_wokeup_unrefresh"])/4;
          }
          if(isset($user_completed_fields["core_sleep_quality"])){
            $sleep_score[]      = ($user_completed_fields["core_sleep_quality"]-1)/3;
          }

          $temp_score         = (2/7)*array_sum($sleep_score); 
          $domain_items["well_score_ls_sleep"]  = round(scaleDomainScore($temp_score, count($sleep_score), 7),4);
        }else{
          $calc_lifestyle = false;
        }
          
        //diet
        $old_diet_req = array("core_vegatables_intro" => 1
                              ,"core_desserts_intro" => 1
                              ,"core_fastfood_day" => 1
                              ,"core_sweet_drinks" => 1
                              ,"core_processed_intro" => 1);
        $num_fields       = count($old_diet_req);
        $num_answered     = count(array_intersect_key($old_diet_req,$user_completed_fields));
        $old_non_answered = $num_fields - $num_answered;
        $old_dq_num       = ceil($num_fields*.3);
        $old_available    = $old_non_answered < $old_dq_num ? true :false;

        $diet_req = array( "core_vegatables_intro_v2" => 1
                          ,"core_fruit_intro_v2"      => 1
                          ,"core_grain_intro_v2"      => 1
                          ,"core_bean_intro_v2"       => 1
                          ,"core_sweet_intro_v2"      => 1
                          ,"core_meat_intro_v2"       => 1
                          ,"core_nuts_intro_v2"       => 1
                          ,"core_sodium_intro_v2"     => 1
                          ,"core_sugar_intro_v2"      => 1
                          ,"core_fish_intro_v2"       => 1
                          ,"core_cook_intro_v2"       => 1
                          ,"core_fastfood_intro_v2"   => 1
                          );
        $num_fields       = count($diet_req);
        $num_answered     = count(array_intersect_key($diet_req,$user_completed_fields));
        $non_answered     = $num_fields - $num_answered;
        $dq_num           = ceil($num_fields*.3);
        // well_score_ls_diet_old
        if($non_answered < $dq_num){
          $diet_score = array();
          if(isset($user_completed_fields["core_vegatables_intro_v2"])){
            $temp_ar = array(
              1 => array(0,0,1),
              2 => array(2,4,6),
              3 => array(8,9,10,10)
            );
            $primary_var    = "core_vegatables_intro_v2";
            $secondary_var  = "core_vegetables_intro_v2" . "_" . $user_completed_fields[$primary_var];
            $diet_score[$secondary_var]   = isset($user_completed_fields[$secondary_var]) ? $temp_ar[$user_completed_fields[$primary_var]][$user_completed_fields[$secondary_var]] : 0;
          }

          if(isset($user_completed_fields["core_fruit_intro_v2"])){
            $temp_ar = array(
              1 => array(0,0,1),
              2 => array(2,4,6),
              3 => array(8,10,10,10)
            );
            $primary_var    = "core_fruit_intro_v2";
            $secondary_var  = $primary_var . "_" . $user_completed_fields[$primary_var];
            $diet_score[$secondary_var]   = isset($user_completed_fields[$secondary_var]) ? $temp_ar[$user_completed_fields[$primary_var]][$user_completed_fields[$secondary_var]] : 0;
          }

          if(isset($user_completed_fields["core_grain_intro_v2"])){
            $temp_ar = array(
              1 => array(0,0,1),
              2 => array(2,4,6),
              3 => array(8,10,10,8)
            );
            $primary_var    = "core_grain_intro_v2";
            $secondary_var  = $primary_var . "_" . $user_completed_fields[$primary_var];
            $diet_score[$secondary_var]   = isset($user_completed_fields[$secondary_var]) ? $temp_ar[$user_completed_fields[$primary_var]][$user_completed_fields[$secondary_var]] : 0;
          }

          if(isset($user_completed_fields["core_bean_intro_v2"])){
            $temp_ar = array(
              1 => array(0,1,2),
              2 => array(4,6,8),
              3 => array(9,10,10,10)
            );
            $primary_var    = "core_bean_intro_v2";
            $secondary_var  = $primary_var . "_" . $user_completed_fields[$primary_var];
            $diet_score[$secondary_var]   = isset($user_completed_fields[$secondary_var]) ? $temp_ar[$user_completed_fields[$primary_var]][$user_completed_fields[$secondary_var]] : 0;
          }

          if(isset($user_completed_fields["core_sweet_intro_v2"])){
            $temp_ar = array(
              1 => array(10,9,8),
              2 => array(6,4,1),
              3 => array(0,0,0,0)
            );
            $primary_var    = "core_sweet_intro_v2";
            $secondary_var  = $primary_var . "_" . $user_completed_fields[$primary_var];
            $diet_score[$secondary_var]   = isset($user_completed_fields[$secondary_var]) ? $temp_ar[$user_completed_fields[$primary_var]][$user_completed_fields[$secondary_var]] : 0;
          }

          if(isset($user_completed_fields["core_meat_intro_v2"])){
            $temp_ar = array(
              1 => array(10,10,8),
              2 => array(6,4,2),
              3 => array(1,0,0,0)
            );
            $primary_var    = "core_meat_intro_v2";
            $secondary_var  = $primary_var . "_" . $user_completed_fields[$primary_var];
            $diet_score[$secondary_var]   = isset($user_completed_fields[$secondary_var]) ? $temp_ar[$user_completed_fields[$primary_var]][$user_completed_fields[$secondary_var]] : 0;
          }

          if(isset($user_completed_fields["core_nuts_intro_v2"])){
            $temp_ar = array(
              1 => array(0,1,2),
              2 => array(4,6,8),
              3 => array(10,10,8,6)
            );
            $primary_var    = "core_nuts_intro_v2";
            $secondary_var  = $primary_var . "_" . $user_completed_fields[$primary_var];
            $diet_score[$secondary_var]   = isset($user_completed_fields[$secondary_var]) ? $temp_ar[$user_completed_fields[$primary_var]][$user_completed_fields[$secondary_var]] : 0;
          }

          if(isset($user_completed_fields["core_sodium_intro_v2"])){
            $temp_ar = array(
              1 => array(10,9,8),
              2 => array(6,4,2),
              3 => array(1,0,0,0)
            );
            $primary_var    = "core_sodium_intro_v2";
            $secondary_var  = $primary_var . "_" . $user_completed_fields[$primary_var];
            $diet_score[$secondary_var]   = isset($user_completed_fields[$secondary_var]) ? $temp_ar[$user_completed_fields[$primary_var]][$user_completed_fields[$secondary_var]] : 0;
          }

          if(isset($user_completed_fields["core_sugar_intro_v2"])){
            $temp_ar = array(
              1 => array(10,9,8),
              2 => array(6,4,1),
              3 => array(0,0,0,0)
            );
            $primary_var    = "core_sugar_intro_v2";
            $secondary_var  = $primary_var . "_" . $user_completed_fields[$primary_var];
            $diet_score[$secondary_var]   = isset($user_completed_fields[$secondary_var]) ? $temp_ar[$user_completed_fields[$primary_var]][$user_completed_fields[$secondary_var]] : 0;
          }

          if(isset($user_completed_fields["core_fish_intro_v2"])){
            $temp_ar = array(
              1 => array(0,4,7),
              2 => array(10,10,10),
              3 => array(10,10,10,10)
            );
            $primary_var    = "core_fish_intro_v2";
            $secondary_var  = $primary_var . "_" . $user_completed_fields[$primary_var];
            $diet_score[$secondary_var]   = isset($user_completed_fields[$secondary_var]) ? $temp_ar[$user_completed_fields[$primary_var]][$user_completed_fields[$secondary_var]] : 0;
          }

          if(isset($user_completed_fields["core_cook_intro_v2"])){
            $temp_ar = array(
              1 => array(0,1,1),
              2 => array(2,4,6),
              3 => array(8,10,10,10)
            );
            $primary_var    = "core_cook_intro_v2";
            $secondary_var  = $primary_var . "_" . $user_completed_fields[$primary_var];
            $diet_score[$secondary_var]   = isset($user_completed_fields[$secondary_var]) ? $temp_ar[$user_completed_fields[$primary_var]][$user_completed_fields[$secondary_var]] : 0;
          }

          if(isset($user_completed_fields["core_fastfood_intro_v2"])){
            $temp_ar = array(
              1 => array(10,8,5),
              2 => array(2,0,0),
              3 => array(0,0,0,0)
            );
            $primary_var    = "core_fastfood_intro_v2";
            $secondary_var  = $primary_var . "_" . $user_completed_fields[$primary_var];
            $diet_score[$secondary_var]   = isset($user_completed_fields[$secondary_var]) ? $temp_ar[$user_completed_fields[$primary_var]][$user_completed_fields[$secondary_var]] : 0;
          }
          $temp_score     = array_sum($diet_score)/count($diet_score);
          $domain_items["well_score_ls_diet"] = $temp_score/5;//round(scaleDomainScore($temp_score, count($diet_score), 12),4);
        }elseif($old_available){
          $diet_score = array();
          if(isset($user_completed_fields["core_vegatables_intro"])){
            $temp_ar = array(0,8,9,9,10,10,10,10,10,10,10);
            $diet_score["core_vegatables_intro"]   =$temp_ar[$user_completed_fields["core_vegatables_intro"]];
          }

          if(isset($user_completed_fields["core_desserts_intro"])){
            $temp_ar = array(10,0,0,0,0,0,0,0,0,0,0);
            $diet_score["core_desserts_intro"]   = $temp_ar[$user_completed_fields["core_desserts_intro"]];
          }

          if(isset($user_completed_fields["core_processed_intro"])){
            $temp_ar = array(10,6,6,4,4,2,2,1,0,0,0);
            $diet_score["core_processed_intro"]   = $temp_ar[$user_completed_fields["core_processed_intro"]];
          }

          if(isset($user_completed_fields["core_sweet_drinks"])){
            $temp_ar = array(0,10,6,4,1,0,0,0,0);
            $diet_score["core_sweet_drinks"]   = $temp_ar[$user_completed_fields["core_sweet_drinks"]];
          }

          if(isset($user_completed_fields["core_fastfood_day"]) && isset($user_completed_fields["core_fastfood_freq"])){
            $diet_score["core_fastfood"] = 0;
            if($diet_score["core_fastfood_day"] == 1){
              if($user_completed_fields["core_fastfood_freq"] < 2){
                $diet_score["core_fastfood"] = 2;
              }
            }elseif($diet_score["core_fastfood_day"] == 0){
              $diet_score["core_fastfood"] = 10;
            }
          }

          $temp_score     = array_sum($diet_score)/count($diet_score);
          $domain_items["well_score_ls_diet_old"] = $temp_score/25;
        }else{
          $calc_lifestyle = false;
        }
          
        if($calc_lifestyle){
          $score["well_score_ls"] = round(array_sum($domain_items),4);
        }
        $score["ls_sub_domains"] = $domain_items;
      break;
    }
  }
  return array( "scores" => $score 
               ,"pos_neg_subscores" => array( "well_score_emotion_pos"  =>  $emo_positive_dom 
                                             ,"well_score_emotion_neg"  =>  $emo_negative_dom 
                                             ,"well_score_stress_pos"   =>  $stress_positive_dom
                                             ,"well_score_stress_neg"   =>  $stress_negative_dom
                                      )
              );
}

function calculateShortScore($loggedInUser, $user_event_arm, $_CFG, $user_survey_data){
  $extra_params = array(
    'content'     => 'record',
    'records'     => array($loggedInUser->id) ,
    'fields'      => array("id","well_score"),
    'events'      => $user_event_arm
  );
  $user_ws      = RC::callApi($extra_params, true, $_CFG->REDCAP_API_URL, $_CFG->REDCAP_API_TOKEN); 

  // ONLY WANT TO SHOW IT IF AT LEAST THE 1st anniversary WAS COMPLETED
  $short_score  = 0;


  if( empty($user_ws[0]["well_score"]) ){
    //CALCULATE WELL_SCORE FOR CURRENT USER IF NOT ALREADY STORED
    //SHORT SCALE SCORE
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
      ,"core_important_time"
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
      "core_contribute_doing"     => 1
      ,"core_satisfied_yourself"  => 1
      ,"core_money_needs"         => 1
      ,"core_religious_beliefs"   => 1
      ,"core_engage_oppo"         => 1
      ,"core_fitness_level"       => 1
      ,"core_important_time"      => 1
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

    $arms_answers = array();
    $user_answers   = $user_survey_data->getUserAnswers($loggedInUser->id,$short_q_fields,$user_event_arm);
    $user_completed_keys        = array_filter(array_intersect_key( $user_answers[0],  array_flip($short_q_fields)),function($v){
      return $v !== false && !is_null($v) && ($v != '' || $v == '0');
    });
    $missing_data_keys          = array_diff_key($short_circuit_diff_ar,$user_completed_keys);
    $minimumData                = checkMinimumForShortScore($missing_data_keys);

    //ENOUGH DATA TO CALC SCORE
    $arms_answers[$user_event_arm] = $minimumData ? $user_completed_keys : array();
    $short_scores = getShortScores($arms_answers);
    if(isset($short_scores[$user_event_arm])){
      $short_score = $score  = round(array_sum($short_scores[$user_event_arm]));
      $data[] = array(
        "record"            => $loggedInUser->id,
        "field_name"        => "well_score",
        "value"             => $score,
        "redcap_event_name" => $user_event_arm
      );
      $result = RC::writeToApi($data, array("overwriteBehavior" => "overwite", "type" => "eav"), $_CFG->REDCAP_API_URL, $_CFG->REDCAP_API_TOKEN);
    }
  }else{
    $short_score = $user_ws[0]["well_score"];
  }
  return $short_score; 
}

// HELPING FUNCTIONS FOR ABOVE
function checkMinimumForShortScore($missing_data_keys){
  $skip_score = 0;
  foreach($missing_data_keys as $missing_key => $junk){
    switch($missing_key){
      //PURPOSE AND MEANING
      case "core_contribute_doing":
      case "core_satisfied_yourself":
      case "core_money_needs":
      case "core_religious_beliefs":
      case "core_engage_oppo":
      case "core_fitness_level":
        $skip_score++;
        break;
      case "core_important_energy":
      case "core_deal_whatever":
      case "core_joyful":
      case "core_worried":
        $skip_score = $skip_score + .5;
        break;
      case "core_lack_companionship":
      case "core_people_upset":
      case "core_energized_help":
        $skip_score = $skip_score + .333;
        break;
      case "core_lpaq":
      case "core_smoke_100":
      case "core_sleep_quality":
        $skip_score = $skip_score + .2;
        break;
      case "core_vegatables_intro_v2":
      case "core_sugar_intro_v2":
        $skip_score = $skip_score + .1;
        break;
    }
  }

  if( empty($missing_data_keys["core_bngdrink_female_freq"]) && empty($missing_data_keys["core_bngdrink_male_freq"]) ){
    $skip_score = $skip_score + .2;
  }

  if($skip_score > 3){
    return false;
  }
  return true;
}

function getShortScores($arm_answers){
  $scores = array();
  foreach($arm_answers as $arm => $answers){
    $scores[$arm] = getShortScore($answers);
  }
  return $scores;
}

function getShortScore($answers){
  // $answers = array_filter($answers);
  $score  = array();

  if(empty($answers)){
    return array();
  }

  //SOCIAL CONNECTEDNESS
  //
  $sc_a   = isset($answers["core_lack_companionship"]) ? 5/3 * ((6 - $answers["core_lack_companionship"])/5) : 0;
  $sc_b   = isset($answers["core_people_upset"]) ? 5/3 * ((6 - $answers["core_people_upset"])/5) : 0;
  $sc_c   = isset($answers["core_energized_help"]) ? 5/3 * ($answers["core_energized_help"]/5) : 0;
  $score["soc_con"] = $sc_a + $sc_b + $sc_c;


  if(isset($answers["core_vegatables_intro_v2"])){
	  //Lifestyle BEHAVIORS
	  $veg_ar = array(
	    1 => array(0,0,1),
	    2 => array(2,4,6),
	    3 => array(8,9,10,10)
	  );
	  $veg_score = 0;

	  if(isset($answers["core_vegatables_intro_v2"])){
	    $veg_a  = $answers["core_vegatables_intro_v2"];
	    $veg_b  = $answers["core_vegetables_intro_v2_" . $veg_a];
	    $veg_score = (($veg_ar[$veg_a][$veg_b])/10) * .5;
	  }
  }elseif(isset($answers["core_vegatables_intro"])){
  	  $veg_ar = array(
	    0 => 0,
	    1 => 8,
	    2 => 9,
	    3 => 9
	  );
	  $veg_score = $answers["core_vegatables_intro"] > 3 ? 10 : $veg_ar[$answers["core_vegatables_intro"]];
  }

  if(isset($answers["core_sugar_intro_v2"])){
	  $sugar_ar = array(
	    1 => array(10,9,8),
	    2 => array(6,4,1),
	    3 => array(0,0,0,0)
	  );
	  $sug_score = 0;
	  if(isset($answers["core_sugar_intro_v2"])){
	    $sug_a  = $answers["core_sugar_intro_v2"];
	    $sug_b  = $answers["core_sugar_intro_v2_" . $sug_a];
	    $sug_score = (($sugar_ar[$sug_a][$sug_b])/10) * .5;
	  }
  }elseif(isset($answers["core_sugar_intro"])){
	  $sug_score = $answers["core_sugar_intro"] == 0 ? 10 : 0;
  }

  $dietscore  = $veg_score + $sug_score;

  $smokescore = 0;
  if(isset($answers["core_smoke_100"])){
    $smokecfn = $answers["core_smoke_100"];
    $smok_frq = isset($answers["core_smoke_freq"]) ? $answers["core_smoke_freq"] : 0;
    if($smok_frq === 3){
      $smokecfn = 2;
    }
    $smokecfn++;
    $smokescore   = (4 - $smokecfn)/3;
  }
  
  $lpaqscore    = isset($answers["core_lpaq"]) ? $answers["core_lpaq"]/6 : 0;
  $slepscore    = isset($answers["core_sleep_quality"]) ? $answers["core_sleep_quality"]/4 : 0;

  $bng          = isset($answers["core_bngdrink_female_freq"]) ? $answers["core_bngdrink_female_freq"] : 0;
  $bng          = isset($answers["core_bngdrink_male_freq"]) ?  $answers["core_bngdrink_male_freq"] : $bng;
  $bng++;
  $bngscore     = (3 - $bng)/2;

  $score["lif_beh"] = $bngscore + $slepscore + $lpaqscore + $smokescore + $dietscore;

  //STRESS AND RESILIENCE
  $sr_a     = isset($answers["core_important_time"]) ? ((6 - $answers["core_important_time"])/5) * 2.5 : 0;
  $sr_b     = isset($answers["core_deal_whatever"]) ? ($answers["core_deal_whatever"]/5) * 2.5 : 0;
  $score["stress_res"]  = $sr_a + $sr_b;

  //EXPERIENCE OF EMOTIONS
  $eom_a    = isset($answers["core_joyful"]) ? ($answers["core_joyful"]/5) * 2.5 : 0;
  $eom_b    = isset($answers["core_worried"]) ? ((6 - $answers["core_worried"])/5) * 2.5 : 0;
  $score["exp_emo"]     = $eom_a + $eom_b;

  //PHYSICAL HEALTH
  $score["phys_health"] = isset($answers["core_fitness_level"]) ? $answers["core_fitness_level"] * (5/6) : 0;

  //PURPOSE AND MEANING
  $score["purp_mean"]   = isset($answers["core_contribute_doing"]) ? $answers["core_contribute_doing"] : 0;

  //SENSE OF SELF
  $score["sens_self"]   = isset($answers["core_satisfied_yourself"]) ? $answers["core_satisfied_yourself"] : 0;

  //FINANCIAL SECURITY/SATISFACTION
  $score["fin_sat"]     = isset($answers["core_money_needs"]) ? $answers["core_money_needs"] * (5/6) : 0;

  //SPIRITUALITY AND RELIGION
  $score["spirit_rel"]  = isset($answers["core_religious_beliefs"]) ? $answers["core_religious_beliefs"] : 0;

  //EXPLORATION AND CREATIVITY
  $score["exp_cre"]     = isset($answers["core_engage_oppo"]) ? $answers["core_engage_oppo"] : 0;

  return $score;
}

function printWELLOverTime($user_scores){
  global $loggedInUser, $lang;

  $year_css = "year";
  $html_arr = array();
  $html_arr[] = "<div class='well_scores'>";
  foreach($user_scores as $arm => $score){
    $score_year       = $score["year"];
    $user_score       = !empty($score["well_score"]) ? round($score["well_score"]) : array();
    
    $user_score_txt   = !empty($user_score) ? round(($user_score/50)*100) . "%" : $lang["NOT_ENOUGH_OTHER_DATA"];
    $user_bar         = !empty($user_score) ? round(($user_score/50)*100) : "0%";
    
    $html_arr[]       = "<div class='well_score user_score $year_css'><span style='width:$user_bar%'><i>$score_year</i></span><b>$user_score_txt</b></div>";
    
    $year_css = $year_css . "x";
  }
  $html_arr[] = "<div class='anchor'>";
  $html_arr[] = "<span class='zero'>0% (".$lang["LOWER_WELLBEING"].")</span>";
  $html_arr[] = "<span class='fifty'>50%</span>";
  $html_arr[] = "<span class='hundred'> (".$lang["HIGHER_WELLBEING"].") 100%</span>";
  $html_arr[] = "</div>";
  $html_arr[] = "</div>";

  return implode("",$html_arr);
}

function scaleDomainScore($domain_score, $q_answered, $q_max){
  return $domain_score;
  // return $domain_score*$q_max/$q_answered;
}





// NO USE RIGHT NOW
function printWELLComparison($eventarm, $user_score, $other_score){
  global $loggedInUser, $lang, $all_completed;

  $user_score       = !empty($user_score) ? round(array_sum($user_score)) : array();
  $user_score_txt   = !empty($user_score) ? $lang["USERS_SCORE"] . " : " . $user_score . "/50" : $lang["NOT_ENOUGH_USER_DATA"];
  $user_bar         = ($user_score*100)/70;

  $other_score      = !empty($other_score) ? round(array_sum($other_score)) : array();
  $other_score_txt  = !empty($other_score) ? $lang["OTHERS_SCORE"] . " : " . $other_score . "/50" : $lang["NOT_ENOUGH_OTHER_DATA"];
  $other_bar        = ($other_score*100)/70;
  
  // $armtime          = ucfirst(str_replace("_"," ",str_replace("_arm_1","",$eventarm)));
  //TODO , short arm uses diet_start_ts_v2, long arm uses your_feedback_ts?
  $armtime          = substr($all_completed["diet_start_ts_v2"],0,strpos($all_completed["diet_start_ts_v2"],"-"));
  
  echo "<div class='well_scores'>";
  echo "<div class='well_score user_score'><span style='width:$user_bar%'></span><b>$user_score_txt</b></div>";
  echo "<div class='well_score other_score'><span style='width:$other_bar%'></span><b>$other_score_txt</b></div>";
  echo "<h4>$armtime</h4>";  
  echo "</div>";
}

function getAvgWellScoreOthers($others_scores){
  $sum = 0;
  foreach($others_scores as $user){
    $sum = $sum + intval($user["well_score"]);
  }

  return round($sum/count($others_scores));
}
