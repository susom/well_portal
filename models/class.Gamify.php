<?php
/**
 * Created by PhpStorm.
 * User: irvins
 * Date: 1/22/19
 * Time: 10:46 AM
 */

class Gamify {
    protected $API_URL;
    protected $API_TOKEN;

    protected $points;
    protected $quotes;

    public function __construct($api_url,$api_token){
        $this->API_URL 	        = $api_url;
        $this->API_TOKEN        = $api_token;
        $this->points           = self::getPoints();
        $this->quotes           = self::getQuotes();
    }

    private function getQuotes(){
        $extra_params   = array(
            'content'   => 'record',
            'format'    => 'json',
            "records"   => 9999,
            "fields"    => "wof_quotes"
        );
        $results        = RC::callApi($extra_params, true, $this->API_URL, $this->API_TOKEN);
        $quotes         = !empty($results) ? current($results) : array();
        $quotes         = json_decode($quotes["wof_quotes"],1);
        return $quotes;
    }

    private function getPoints(){
        $gamify_fields  = array("gamify_pts_any","gamify_pts_survey","gamify_pts_minichallenge","gamify_pts_resources", "gamify_pts_survey_complete", "gamify_pts_register", "gamify_pts_login", "gamify_pts_tree_redeem", "gamify_pts_wof");

        $extra_params   = array(
            'content'   => 'record',
            'format'    => 'json',
            "records"   => 9999,
            "fields"    => $gamify_fields
        );
        $results        = RC::callApi($extra_params, true, $this->API_URL, $this->API_TOKEN);
        $points         = !empty($results) ? current($results) : $gamify_fields;
        return $points;
    }

    public function showPoints(){
        return $this->points;
    }

    public function showQuotes(){
        return $this->quotes;
    }
}