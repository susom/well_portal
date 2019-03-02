<?php
phpinfo();
exit;
require_once("models/config.php");
if(isset($_SERVER['argv'][1])){
    $temp               = explode("=",$_SERVER['argv'][1]);
    $_REQUEST["action"] = $temp[1];
}else{
     include("models/inc/checklogin.php");
}
include("models/inc/scoring_functions.php");


function getScriptOutput($path, $display_year, $user_id, $well_sum_score){
    ob_start();
    $csv_file = $user_id . "_" . $display_year . "_";
    include $path;
    return ob_get_clean();
}

$domain_desc = array(
    "0" =>  lang("DOMAIN_EC_DESC" ),
    "1" =>  lang("DOMAIN_LB_DESC" ),
    "2" =>  lang("DOMAIN_SC_DESC"),
    "3" =>  lang("DOMAIN_SR_DESC" ),
    "4" =>  lang("DOMAIN_EE_DESC" ),
    "5" =>  lang("DOMAIN_SS_DESC" ),
    "6" =>  lang("DOMAIN_PH_DESC" ),
    "7" =>  lang("DOMAIN_PM_DESC" ),
    "8" =>  lang("DOMAIN_FS_DESC" ),
    "9" =>  lang("DOMAIN_RS_DESC" ),
);

//FIRST GET THE RECENTLY UPLOADED
$API_URL    = "https://redcap.stanford.edu/api/";
$API_TOKEN  = "287BA2B137F30F559120779A0DAB3377";
$extra_params = array(
     "content"   => "record"
    ,"fields"   => array("id","well_fp_email")
    ,"type"     => "flat"
    ,"filterLogic" => "[well_fp_radarchart] = ''"
);
$records        = RC::callApi($extra_params, true, $API_URL, $API_TOKEN);
$id_email       = array();
foreach($records as $record){
    $id_email[$record["id"]] = $record["well_fp_email"];
};

//NOW GET ALL THE WELL SCORES FOR ABOVE IDS
$API_TOKEN  = "9886C95CAE55C659264663EE1C24C47A";
$extra_params = array(
    "content"  => "record"
    ,"fields"   => array("id","well_long_score_json","well_score_long","portal_consent_ts")
    ,"type"     => "flat"
    ,"records"  => array_keys($id_email)
);
$records        = RC::callApi($extra_params, true, $API_URL, $API_TOKEN);

//NOW GET THE LATEST OF EACH WELL SCORE
$id_longjson    = array();
$id_consent     = array();
foreach($records as $record){
    //THIS IS FOR GETTING THEIR RELATIVE START TIMES
    if($record["redcap_event_name"] == "enrollment_arm_1"){
        $id_consent[$record["id"]] = $record["portal_consent_ts"];
    }
    $id_longjson[$record["id"]] = array("event" => $record["redcap_event_name"] , "well_long_score_json" => $record["well_long_score_json"], "well_score" => $record["well_score_long"]);
}

$data = array();
foreach($id_longjson as $user_id =>  $user){
    $event          = $user["event"];
    $user_ev_years  = getEventYears($id_consent[$user_id], $event);

    $result_year    = $user_ev_years[$event];
    $users_file_csv = "RadarUserCSV/".$user_id."_".$result_year."_Results.csv";

    $long_scores    = json_decode($user["well_long_score_json"],1);
    $sum_long_score = round($user["well_score"]);

    if(!file_exists($users_file_csv) && !empty($long_scores)){
        $ct         = 0;
        $csv_data   = "group, axis, value, description\n";
        foreach ($long_scores as $key => $value){
            $display = $value;
            $display = number_format($display,2,".","");
            $csv_data .= "Year ".$result_year.", ". $key .", ". $display .", ". $domain_desc[$ct]."\n";
            $ct++;
        }
        $sum_long_score = round(array_sum($long_scores));
        file_put_contents($users_file_csv, $csv_data);
    }

    $html = getScriptOutput("inline_radar_chart_template.php", $result_year, $user_id, "$sum_long_score/100");

    $obfuscated_user_id = substr(md5(uniqid($user_id, true)), 0, 8);
    print_rr($obfuscated_user_id);
    file_put_contents("results/".$obfuscated_user_id.".html", $html);

    $data[] = array(
        "record"            => $user_id,
        "field_name"        => "well_fp_radarchart",
        "value"             => "https://wellforlife-portal.stanford.edu/results/" . $obfuscated_user_id . ".html"
    );
}
$API_TOKEN  = "287BA2B137F30F559120779A0DAB3377";
//$result     = RC::writeToApi($data, array("overwriteBehavior" => "overwite", "type" => "eav"), $API_URL, $API_TOKEN);
exit;


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

        $data         = array();
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
        $extra_params = array(
            "content"            => "record"
        ,'type'      	        => "flat"
        ,'fields'             => array("id")
        ,'exportSurveyFields' => true
        );
        $welldata   = RC::callApi($extra_params, true, $API_URL, $API_TOKEN);
        $unique_ids = array_unique(array_column($welldata,"id"));
        $id_chunks  = array_chunk($unique_ids,50);

        $id_chunks = array(6583);
        $calculated = array();
        foreach($id_chunks as $chunk_of_ids){
            $extra_params = array(
                "content"            => "record"
            ,'type'               => "flat"
            ,"records"            => $chunk_of_ids
            ,'exportSurveyFields' => true
            );
            $welldata   = RC::callApi($extra_params, true, $API_URL, $API_TOKEN);
            print_rr($welldata);
            if(!empty($welldata)){
                foreach($welldata as $i => $res){
                    $long_score     = calculateLongScore($res["id"],$res["redcap_event_name"],$_CFG,$res);
                    $calculated[]   = "WELL score of $long_score was calculated for id# ".$res["id"] ." in " . $res["redcap_event_name"];
                }
            }
        };
        $calculated[] =  "All elgibile WELL Scores have been calculated";
        echo implode("<hr>",$calculated);
    }
}else{
    print_rr("need to add action");
}
    