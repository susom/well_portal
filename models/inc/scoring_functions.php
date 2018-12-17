<?php
function calculateLongScore($loggedInUser, $user_event_arm, $_CFG, $all_completed){
    $record_id = !is_object($loggedInUser) ? $loggedInUser : $loggedInUser->id;

    // CHECK IF EXISTING LONG SCORE
    $extra_params = array(
        'content'     => 'record',
        'records'     => array($record_id) ,
        'fields'      => array("id","well_long_score_json"),
        'events'      => $user_event_arm
    );
    $user_ws      = RC::callApi($extra_params, true, $_CFG->REDCAP_API_URL, $_CFG->REDCAP_API_TOKEN);
    $long_score   = null;

    if(true || !isset($user_ws[0]) || (isset($user_ws[0]) && empty( json_decode($user_ws[0]["well_long_score_json"],1) )) ){
        //10 DOMAINS TO CALCULATE THE WELL LONG SCORE
        $domain_mapping = array(
            "well_score_creativity" => lang("RESOURCE_CREATIVITY")
        ,"well_score_religion"   => lang("RESOURCE_RELIGION")
        ,"well_score_financial"  => lang("RESOURCE_FINANCIAL")
        ,"well_score_purpose"    => lang("RESOURCE_PURPOSE")
        ,"well_score_health"     => lang("RESOURCE_PHYSICAL")
        ,"well_score_senseself"  => lang("RESOURCE_SELF")
        ,"well_score_emotion"    => lang("RESOURCE_EMOTIONS")
        ,"well_score_stress"     => lang("RESOURCE_STRESS")
        ,"well_score_social"     => lang("RESOURCE_SOCIAL")
        ,"well_score_ls"         => lang("RESOURCE_LIFESTYLE")
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
        if(!isset($user_completed_keys["core_lpaq"])){
            $minimumData  = false;
        }
        if(!isset($user_completed_keys["core_bngdrink_female_freq"]) && !isset($user_completed_keys["core_bngdrink_male_freq"])){
            $minimumData  = false;
        }
        if(!isset($user_completed_keys["core_smoke_100"])){
            $minimumData  = false;
        }
        if(isset($user_completed_keys["core_smoke_100"]) && $user_completed_keys["core_smoke_100"] != 0 && !isset($user_completed_keys["core_smoke_freq"]) ){
            $minimumData  = false;
        }

        $check_fields_dq = array();
        foreach($domain_fields as $domain => $fields){
            //DO THE DQ_THRESHOLD PER DOMAIN
            //CALCULATE AND SAVE FOR EACH DOMAIN THAT DOES PASS
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
        $check_user_completed_dq  = $user_completed_keys;

        // DAMNIT TOHELL, GOTTA DO THIS PROCESS AGAIN SINCE THE ABOVE ISNT USED FOR THE "minimum data"
        $user_completed_keys = array_filter(array_intersect_key( $all_completed, array_flip($q_fields) ),function($v){
            return $v !== false && !is_null($v) && ($v != '' || $v == '0');
        });

        // TRY TO CALCULATE SUBDOMAIN SCORES REGARDLESS
        $temp               = getLongScores($domain_fields, $user_completed_keys);
        $long_scores        = $temp["scores"];
        $sub_scores         = array_pop($long_scores);
        $pos_neg_subscores  = $temp["pos_neg_subscores"];
        $pos_neg_vals       = array_filter($pos_neg_subscores,'allowZeroFilter');

        if(!empty($long_scores)){
            // save individual scores

            // 4 CASES WHERE WE DO NOT CALC WELL SCOre
            // 1. ANY MISSING DOMAIN (eg < 10)
            // 2. ANy MISSING SuB DOmAIN IN LifeSTYLE (eg < 5)
            // 4. ANY MISSING POS/NEG SUBSCore of EMotions/Stress (eg < 4)
            // 3. 30% missing questions total (hmm ah below, gotta branch with new or old diet)
            if(array_key_exists("well_score_ls_diet_old",$sub_scores)){
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

            $check_fields_dq  = array_diff($check_fields_dq, $remove_from_fields);
            $dq_threshold     = ceil(count($check_fields_dq) * .3);
            $missing_keys     = array_diff($check_fields_dq, array_keys($check_user_completed_dq));
            if(count($long_scores) < 10 || count($sub_scores) < 5 || count($pos_neg_vals) < 4 || count($missing_keys) >= $dq_threshold){
                $minimumData = false;
            }

            // SAVE ALL THE SUB SCORES
            $data         = array();
            $save_scores  = array_merge($long_scores,$sub_scores, $pos_neg_vals);
            foreach($save_scores as $well_var => $well_val){
                $data[] = array(
                    "record"            => $record_id,
                    "field_name"        => $well_var,
                    "value"             => $well_val,
                    "redcap_event_name" => $user_event_arm
                );
            }
            $result =  RC::writeToApi($data, array("overwriteBehavior" => "overwite", "type" => "eav"), $_CFG->REDCAP_API_URL, $_CFG->REDCAP_API_TOKEN);

            // PREPARE JSON BLOCK FOR RADAR CHART MAPPING
            $remapped_long_scores = array();
            foreach($long_scores as $rc_var => $value){
                $remapped_long_scores[$domain_mapping[$rc_var]] = $value;
            }

            if($minimumData){
                $data       = array();
                $data[]     = array(
                    "record"            => $record_id,
                    "field_name"        => "well_long_score_json",
                    "value"             => json_encode($remapped_long_scores),
                    "redcap_event_name" => $user_event_arm
                );

                $long_score = round(array_sum($remapped_long_scores),4);
                $data[]     = array(
                    "record"            => $record_id,
                    "field_name"        => "well_score_long",
                    "value"             => $long_score,
                    "redcap_event_name" => $user_event_arm
                );
            }else{
                // ELSE SAVE AS NA
                $data   = array(
                    "record"            => $record_id,
                    "field_name"        => "well_score_long",
                    "value"             => "NA",
                    "redcap_event_name" => $user_event_arm
                );
                $long_score = "NA";
            }
            $result = RC::writeToApi($data, array("overwriteBehavior" => "overwite", "type" => "eav"), $_CFG->REDCAP_API_URL, $_CFG->REDCAP_API_TOKEN);
        }
    }else{
        $remapped_long_scores   = json_decode($user_ws[0]["well_long_score_json"],1);
        $long_score             = round(array_sum($remapped_long_scores),4);
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

        $score[$domain]   = "NA";
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
                    $missing      = 0;
                    foreach($fields as $field){
                        $denom          = $field == "core_fitness_level" ? 5 : 4;
                        if(!isset($user_completed_fields[$field])){
                            $missing++;
                            continue;
                        }

                        if($field == "core_interfere_life"){
                            $domain_items[] = (5-$user_completed_fields[$field])/$denom;
                        }else{
                            $domain_items[] = ($user_completed_fields[$field]-1)/$denom;
                        }
                    }
                    $non_missing    = count($fields) - $missing;
                    $temp_score     = (10/$non_missing)*array_sum($domain_items);
                    $score[$domain] = round(scaleDomainScore($temp_score, count($domain_items), count($fields)),4);
                }
                break;

            case "well_score_purpose" :
            case "well_score_senseself" :
                if($non_answered < $dq_num){
                    $domain_items = array();
                    $missing      = 0;
                    foreach($fields as $field){
                        if(!isset($user_completed_fields[$field])){
                            $missing++;
                            continue;
                        }
                        $denom = 4;
                        $domain_items[] = ($user_completed_fields[$field]-1)/$denom;
                    }
                    $non_missing      = count($fields) - $missing;
                    $weight = $domain == "well_score_senseself" ? $non_missing : 2;
                    $temp_score       = (10/$weight)*array_sum($domain_items);
                    $score[$domain] = round(scaleDomainScore($temp_score, count($domain_items), count($fields)),4);
                }
                break;

            case "well_score_social" :
                if($non_answered < $dq_num){
                    $domain_items = array();
                    $missing      = 0;
                    foreach($fields as $field){
                        if(!isset($user_completed_fields[$field])){
                            $missing++;
                            continue;
                        }
                        $denom = 4;

                        if($field == "core_lack_companionship" || $field == "core_left_out" || $field == "core_isolated_others" || $field == "core_drained_helping" || $field == "core_people_upset" || $field == "core_meet_expectations"){
                            $domain_items[] = (5-$user_completed_fields[$field])/$denom;
                        }else{
                            $domain_items[] = ($user_completed_fields[$field]-1)/$denom;
                        }
                    }
                    $non_missing    = count($fields) - $missing;
                    $temp_score     = (10/$non_missing)*(array_sum($domain_items));
                    $score[$domain] = round(scaleDomainScore($temp_score, count($domain_items), count($fields)),4);
                }
                break;

            case "well_score_emotion" :
                if($non_answered < $dq_num){
                    $domain_items = array();
                    $pos_items    = array();
                    $neg_items    = array();
                    $missing_pos  = $missing_neg = 0;
                    $pos_fields   = $neg_fields = 0;
                    foreach($fields as $field){
                        if($field == "core_drained" || $field == "core_frustrated" || $field == "core_hopeless" || $field == "core_sad" || $field == "core_worried"){
                            $neg_fields++;
                            if(!isset($user_completed_fields[$field])){
                                $missing_neg++;
                                continue;
                            }
                        }else{
                            $pos_fields++;
                            if(!isset($user_completed_fields[$field])){
                                $missing_pos++;
                                continue;
                            }
                        }

                        $denom = 4;
                        if($field == "core_drained" || $field == "core_frustrated" || $field == "core_hopeless" || $field == "core_sad" || $field == "core_worried"){
                            $neg_items[] = $domain_items[] = (5-$user_completed_fields[$field])/$denom;
                        }else{
                            $pos_items[] = $domain_items[] = ($user_completed_fields[$field]-1)/$denom;
                        }
                    }

                    $non_missing_pos    = $pos_fields - $missing_pos;
                    $pos_dq             = $missing_pos/$pos_fields >= .3;
                    $non_missing_neg    = $neg_fields - $missing_neg;
                    $neg_dq             = $missing_neg/$neg_fields >= .3;
                    $emo_positive_dom   = empty($pos_items) || $pos_dq ? "NA" : (10/$non_missing_pos)*(array_sum($pos_items));
                    $emo_negative_dom   = empty($neg_items) || $neg_dq ? "NA" : (10/$non_missing_neg)*(array_sum($neg_items));

                    $temp_score     = $emo_positive_dom === "NA" || $emo_negative_dom === "NA" ? "NA" : ($emo_positive_dom + $emo_negative_dom)/2; //(10/11)*(array_sum($domain_items));
                    $score[$domain] = $temp_score === "NA" ? "NA" : round(scaleDomainScore($temp_score, count($domain_items), count($fields)),4);
                }
                break;

            case "well_score_stress" :
                if($non_answered < $dq_num){
                    $domain_items = array();
                    $pos_items    = array();
                    $neg_items    = array();

                    $missing_pos  = $missing_neg = 0;
                    $pos_fields   = $neg_fields = 0;
                    foreach($fields as $field){
                        if($field == "core_important_time" || $field == "core_overwhelm_difficult" || $field == "core_important_energy" || $field == "core_confident_psnlproblem" || $field == "core_going_way"){
                            $neg_fields++;
                            if(!isset($user_completed_fields[$field])){
                                $missing_neg++;
                                continue;
                            }
                        }else{
                            $pos_fields++;
                            if(!isset($user_completed_fields[$field])){
                                $missing_pos++;
                                continue;
                            }
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

                    $non_missing_pos    = $pos_fields - $missing_pos;
                    $pos_dq             = $missing_pos/$pos_fields >= .3;
                    $non_missing_neg    = $neg_fields - $missing_neg;
                    $neg_dq             = $missing_neg/$neg_fields >= .3;
                    $stress_positive_dom   = empty($pos_items) || $pos_dq ? "NA" : (10/$non_missing_pos)*(array_sum($pos_items));
                    $stress_negative_dom   = empty($neg_items) || $neg_dq ? "NA" : (10/$non_missing_neg)*(array_sum($neg_items));

                    $temp_score     = $stress_positive_dom === "NA" || $stress_negative_dom === "NA" ? "NA" : ($stress_positive_dom + $stress_negative_dom)/2;//(10/14)*(array_sum($domain_items));
                    $score[$domain] = $temp_score === "NA" ? "NA" : round(scaleDomainScore($temp_score, count($domain_items), count($fields)),4);
                }
                break;

            case "well_score_ls" :
                $calc_lifestyle = true;
                $domain_items   = array();

                //physical activity
                if(isset($user_completed_fields["core_lpaq"])){
                    $domain_items["well_score_ls_pa"] = round(10*($user_completed_fields["core_lpaq"]-1)/5,4);
                }else{
                    $domain_items["well_score_ls_pa"]  = "NA";
                    $calc_lifestyle = false;
                }

                //alchohol
                if( (isset($user_completed_fields["core_bngdrink_male_freq"]) &&  $user_completed_fields["core_bngdrink_male_freq"] == 1)
                    || (isset($user_completed_fields["core_bngdrink_female_freq"]) && $user_completed_fields["core_bngdrink_female_freq"] == 1 )){
                    $domain_items["well_score_ls_alchohol"] =  0;
                }else{
                    if( (isset($user_completed_fields["core_bngdrink_male_freq"]) ) || (isset($user_completed_fields["core_bngdrink_female_freq"]) )){
                        $domain_items["well_score_ls_alchohol"] = 10;
                    }else{
                        $domain_items["well_score_ls_alchohol"] = "NA";
                    }
                }

                //smoking
                if(isset($user_completed_fields["core_smoke_freq"])){
                    $domain_items["well_score_ls_smoke"] = $user_completed_fields["core_smoke_freq"] > 1 ? 0 : 10;
                }else{
                    // core_smoke_freq = NA
                    if(isset($user_completed_fields["core_smoke_100"]) && $user_completed_fields["core_smoke_100"] == 0){
                        //only allowable option here is core_smoke_100 = 0;
                        $domain_items["well_score_ls_smoke"] =  10 ;
                    }else{
                        $domain_items["well_score_ls_smoke"] =  "NA" ;
                    }
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
                    if(isset($user_completed_fields["core_sleep_hh"]) || isset($user_completed_fields["core_sleep_mm"])){
                        $sleep_hr   = isset($user_completed_fields["core_sleep_hh"]) ? $user_completed_fields["core_sleep_hh"] : 0;
                        $sleep_min  = isset($user_completed_fields["core_sleep_mm"]) ? $user_completed_fields["core_sleep_mm"] : 0;
                        $core_sleep_total   = 60*$sleep_hr + $sleep_min;
                        $sleep_score["sleep_min"]         = $core_sleep_total >= 420 && $core_sleep_total <= 540 ? 1 : 0;
                    }
                    if(isset($user_completed_fields["core_fallasleep_min"])){
                        $sleep_score["fallasleep_min"]    = (7-$user_completed_fields["core_fallasleep_min"])/6;
                    }
                    if(isset($user_completed_fields["core_fallasleep"])){
                        $sleep_score["fallasleep"]        = (5-$user_completed_fields["core_fallasleep"])/4;
                    }
                    if(isset($user_completed_fields["core_wokeup"])){
                        $sleep_score["wokeup"]            = (5-$user_completed_fields["core_wokeup"])/4;
                    }
                    if(isset($user_completed_fields["core_wokeup_early"])){
                        $sleep_score["wokeup_early"]      = (5-$user_completed_fields["core_wokeup_early"])/4;
                    }
                    if(isset($user_completed_fields["core_wokeup_unrefresh"])){
                        $sleep_score["unrefreshed"]      = (5-$user_completed_fields["core_wokeup_unrefresh"])/4;
                    }
                    if(isset($user_completed_fields["core_sleep_quality"])){
                        $sleep_score["quality"]          = ($user_completed_fields["core_sleep_quality"] - 1)/3;
                    }

                    $temp_score         = (10/count($sleep_score))*array_sum($sleep_score);
                    $domain_items["well_score_ls_sleep"]  = round(scaleDomainScore($temp_score, count($sleep_score), 7),4);
                }else{
                    $calc_lifestyle = false;
                    $domain_items["well_score_ls_sleep"] = "NA";
                }

                //diet
                $old_diet_req = array( "core_vegatables_intro" => 1
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
                        if(isset($user_completed_fields[$secondary_var])){
                            $diet_score[$secondary_var]   =  $temp_ar[$user_completed_fields[$primary_var]][$user_completed_fields[$secondary_var]];
                        }
                    }

                    if(isset($user_completed_fields["core_fruit_intro_v2"])){
                        $temp_ar = array(
                            1 => array(0,0,1),
                            2 => array(2,4,6),
                            3 => array(8,10,10,10)
                        );
                        $primary_var    = "core_fruit_intro_v2";
                        $secondary_var  = $primary_var . "_" . $user_completed_fields[$primary_var];
                        if(isset($user_completed_fields[$secondary_var])){
                            $diet_score[$secondary_var]   =  $temp_ar[$user_completed_fields[$primary_var]][$user_completed_fields[$secondary_var]];
                        }
                    }

                    if(isset($user_completed_fields["core_grain_intro_v2"])){
                        $temp_ar = array(
                            1 => array(0,0,1),
                            2 => array(2,4,6),
                            3 => array(8,10,10,8)
                        );
                        $primary_var    = "core_grain_intro_v2";
                        $secondary_var  = $primary_var . "_" . $user_completed_fields[$primary_var];
                        if(isset($user_completed_fields[$secondary_var])){
                            $diet_score[$secondary_var]   = $temp_ar[$user_completed_fields[$primary_var]][$user_completed_fields[$secondary_var]];
                        }
                    }

                    if(isset($user_completed_fields["core_bean_intro_v2"])){
                        $temp_ar = array(
                            1 => array(0,1,2),
                            2 => array(4,6,8),
                            3 => array(9,10,10,10)
                        );
                        $primary_var    = "core_bean_intro_v2";
                        $secondary_var  = $primary_var . "_" . $user_completed_fields[$primary_var];
                        if(isset($user_completed_fields[$secondary_var])){
                            $diet_score[$secondary_var]   = $temp_ar[$user_completed_fields[$primary_var]][$user_completed_fields[$secondary_var]] ;
                        }
                    }

                    if(isset($user_completed_fields["core_sweet_intro_v2"])){
                        $temp_ar = array(
                            1 => array(10,9,8),
                            2 => array(6,4,1),
                            3 => array(0,0,0,0)
                        );
                        $primary_var    = "core_sweet_intro_v2";
                        $secondary_var  = $primary_var . "_" . $user_completed_fields[$primary_var];
                        if(isset($user_completed_fields[$secondary_var])){
                            $diet_score[$secondary_var]   =  $temp_ar[$user_completed_fields[$primary_var]][$user_completed_fields[$secondary_var]];
                        }
                    }

                    if(isset($user_completed_fields["core_meat_intro_v2"])){
                        $temp_ar = array(
                            1 => array(10,10,8),
                            2 => array(6,4,2),
                            3 => array(1,0,0,0)
                        );
                        $primary_var    = "core_meat_intro_v2";
                        $secondary_var  = $primary_var . "_" . $user_completed_fields[$primary_var];
                        if(isset($user_completed_fields[$secondary_var])){
                            $diet_score[$secondary_var]   = $temp_ar[$user_completed_fields[$primary_var]][$user_completed_fields[$secondary_var]];
                        }
                    }

                    if(isset($user_completed_fields["core_nuts_intro_v2"])){
                        $temp_ar = array(
                            1 => array(0,1,2),
                            2 => array(4,6,8),
                            3 => array(10,10,8,6)
                        );
                        $primary_var    = "core_nuts_intro_v2";
                        $secondary_var  = $primary_var . "_" . $user_completed_fields[$primary_var];
                        if(isset($user_completed_fields[$secondary_var])){
                            $diet_score[$secondary_var]   =  $temp_ar[$user_completed_fields[$primary_var]][$user_completed_fields[$secondary_var]];
                        }
                    }

                    if(isset($user_completed_fields["core_sodium_intro_v2"])){
                        $temp_ar = array(
                            1 => array(10,9,8),
                            2 => array(6,4,2),
                            3 => array(1,0,0,0)
                        );
                        $primary_var    = "core_sodium_intro_v2";
                        $secondary_var  = $primary_var . "_" . $user_completed_fields[$primary_var];
                        if(isset($user_completed_fields[$secondary_var])){
                            $diet_score[$secondary_var]   =  $temp_ar[$user_completed_fields[$primary_var]][$user_completed_fields[$secondary_var]];
                        }
                    }

                    if(isset($user_completed_fields["core_sugar_intro_v2"])){
                        $temp_ar = array(
                            1 => array(10,9,8),
                            2 => array(6,4,1),
                            3 => array(0,0,0,0)
                        );
                        $primary_var    = "core_sugar_intro_v2";
                        $secondary_var  = $primary_var . "_" . $user_completed_fields[$primary_var];
                        if(isset($user_completed_fields[$secondary_var])){
                            $diet_score[$secondary_var]   =  $temp_ar[$user_completed_fields[$primary_var]][$user_completed_fields[$secondary_var]];
                        }
                    }

                    if(isset($user_completed_fields["core_fish_intro_v2"])){
                        $temp_ar = array(
                            1 => array(0,4,7),
                            2 => array(10,10,10),
                            3 => array(10,10,10,10)
                        );
                        $primary_var    = "core_fish_intro_v2";
                        $secondary_var  = $primary_var . "_" . $user_completed_fields[$primary_var];
                        if(isset($user_completed_fields[$secondary_var]) ){
                            $diet_score[$secondary_var]   = $temp_ar[$user_completed_fields[$primary_var]][$user_completed_fields[$secondary_var]];
                        }
                    }

                    if(isset($user_completed_fields["core_cook_intro_v2"])){
                        $temp_ar = array(
                            1 => array(0,1,1),
                            2 => array(2,4,6),
                            3 => array(8,10,10,10)
                        );
                        $primary_var    = "core_cook_intro_v2";
                        $secondary_var  = $primary_var . "_" . $user_completed_fields[$primary_var];
                        if(isset($user_completed_fields[$secondary_var]) ){
                            $diet_score[$secondary_var]   = $temp_ar[$user_completed_fields[$primary_var]][$user_completed_fields[$secondary_var]];
                        }
                    }

                    if(isset($user_completed_fields["core_fastfood_intro_v2"])){
                        $temp_ar = array(
                            1 => array(10,8,5),
                            2 => array(2,0,0),
                            3 => array(0,0,0,0)
                        );
                        $primary_var    = "core_fastfood_intro_v2";
                        $secondary_var  = $primary_var . "_" . $user_completed_fields[$primary_var];
                        if(isset($user_completed_fields[$secondary_var])){
                            $diet_score[$secondary_var]   = $temp_ar[$user_completed_fields[$primary_var]][$user_completed_fields[$secondary_var]];
                        }
                    }

                    $temp_score     = array_sum($diet_score)/count($diet_score);
                    $domain_items["well_score_ls_diet"] = $temp_score;//round(scaleDomainScore($temp_score, count($diet_score), 12),4);
                }else{
                    $domain_items["well_score_ls_diet"] = "NA";
                }

                if($old_available){
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
                        $temp_ar = array(10,6,4,1,0,0,0,0);
                        $diet_score["core_sweet_drinks"]   = $temp_ar[ $user_completed_fields["core_sweet_drinks"] - 1];
                    }

                    if(isset($user_completed_fields["core_fastfood_day"])){
                        $diet_score["core_fastfood"] = 0;
                        if($diet_score["core_fastfood_day"] == 1){
                            if($user_completed_fields["core_fastfood_freq"] < 2){
                                $diet_score["core_fastfood"] = 2;
                            }
                        }elseif($diet_score["core_fastfood_day"] == 0){
                            $diet_score["core_fastfood"] = 10;
                        }
                    }

                    $temp_score     = count($diet_score) ? array_sum($diet_score)/count($diet_score) : 0;
                    $domain_items["well_score_ls_diet_old"] = $temp_score;
                }

                //IF NEITHER THAN NO CALC
                if($non_answered >= $dq_num && !$old_available){
                    $calc_lifestyle = false;
                }

                if($calc_lifestyle){
                    $lifestyle_items        = $domain_items;
                    if(isset($lifestyle_items["well_score_ls_diet"])){
                        unset($lifestyle_items["well_score_ls_diet_old"]);
                    }
                    $score["well_score_ls"] = round(array_sum($lifestyle_items)/5,4);
                }
                $score["ls_sub_domains"] = $domain_items;
                break;
        }
    }

    return array( "scores" => $score  ,"pos_neg_subscores" => array(
        "well_score_emotion_pos"  =>  $emo_positive_dom
    ,"well_score_emotion_neg"  =>  $emo_negative_dom
    ,"well_score_stress_pos"   =>  $stress_positive_dom
    ,"well_score_stress_neg"   =>  $stress_negative_dom
    ));
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
