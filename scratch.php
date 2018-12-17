<?php
require_once("models/config.php");
if(isset($_SERVER['argv'][1])){
    $temp               = explode("=",$_SERVER['argv'][1]);
    $_REQUEST["action"] = $temp[1];
}else{
    // include("models/inc/checklogin.php");
}
include("models/inc/scoring_functions.php");

$API_URL      = SurveysConfig::$projects["REDCAP_PORTAL"]["URL"];
$API_TOKEN    = SurveysConfig::$projects["REDCAP_PORTAL"]["TOKEN"];

// ONE TIME ACTION TO UPDATE the generated well_long_score_json variables.
if(isset($_REQUEST["action"]) || true){
    // $action = $_REQUEST["action"];
    $action = "well_score_calc";
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

        $calculated = array();
        foreach($id_chunks as $chunk_of_ids){
            $extra_params = array(
                "content"            => "record"
            ,'type'               => "flat"
            ,"records"            => $chunk_of_ids
            ,'exportSurveyFields' => true
            );
            $welldata   = RC::callApi($extra_params, true, $API_URL, $API_TOKEN);

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
    