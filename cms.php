<?php
require_once("models/config.php");
include("models/inc/checklogin.php");

if(isset($_GET["user_record_id"]) ){
  $checkuser = getUserByRecordID($_GET["user_record_id"]);

  if($checkuser){
    // LETS ADD a NEW SESSION TO MEMBER THE ADMIN USER IN CASE THEY GO VIEW AND COME BACK
    $_SESSION["admin_user"] = $loggedInUser;

    //NOW LETS RESET THE "current user" TO BE THE ID

    unset($_SESSION["arm_years"]);
    unset($_SESSION["completed_timestamps"]);
    unset($_SESSION["core_timestamps"]);
    unset($_SESSION["supplemental_surveys"]);
    unset($_SESSION["user_survey_data"]);
    unset($_SESSION["user_anniversary"]);
    setSessionUser($checkuser);
    header("location:index.php");
    exit;
  }
  addSessionMessage( "User Id Not Found" , "alert");
  $_REQUEST["cat"] = 3;
}

if(isset($_SESSION["admin_user"])){
  $loggedInUser = $_SESSION["admin_user"];
  unset($_SESSION["user_survey_data"]);
  unset($_SESSION["arm_years"]);
  unset($_SESSION["completed_timestamps"]);
  unset($_SESSION["core_timestamps"]);
  unset($_SESSION["supplemental_surveys"]);
  setSessionUser($loggedInUser);
  unset($_SESSION["admin_user"]);
}

$radar_domains = array(
  "0" => "Exploration and Creativity",
  "1" => "Lifestyle and Daily Practices",
  "2" => "Social Connectedness",
  "3" => "Stress and Resilience",
  "4" => "Emotional and Mental Health",
  "5" => "Sense of Self",
  "6" => "Physical Health",
  "7" => "Purpose and Meaning",
  "8" => "Finances",
  "9" => "Spirituality and Religion"
);

$image_catagory = array(
  "1" => "Podcast",
  "2" => "Video",
  "3" => "Article",
  "4" => "Scientific Publication",
  "5" => "WELL content",
  "6" => "Partner content"
);

$languages      = array(
  "1" => "English",
  "2" => "Spanish",
  "3" => "Chinese Simplified",
  "4" => "Chinese Traditional",
);
$gamify_fields  = array("gamify_pts_any","gamify_pts_survey","gamify_pts_minichallenge","gamify_pts_resources", "gamify_pts_survey_complete", "gamify_pts_register", "gamify_pts_login", "gamify_pts_tree_redeem", "gamify_pts_wof");

$lang_req     = isset($_GET["lang"]) ? "?lang=".$_GET["lang"] : "";
$pg_title     = "$websiteName";
$bodyClass    = "cms";

$API_URL      = SurveysConfig::$projects["ADMIN_CMS"]["URL"];
$API_TOKEN    = SurveysConfig::$projects["ADMIN_CMS"]["TOKEN"];
if(!empty($_POST) && isset($_POST["action"])){
    if($_POST["action"] == "newevent"){
      unset($_POST["submit"]);
      unset($_POST["action"]);
      unset($_POST["loc"]);
      unset($_POST["cat"]);

      //import the record
      $ts   = date('Y-m-d H:i:s');
      $data = array(
           "well_cms_create_ts" => $ts
          ,"well_cms_update_ts" => $ts
          ,"id" => "whatever_required_but_wont_be_used"
        );

      foreach($_POST as $key => $val){
        if($key == "surveylink_well_cms_event_link"){
          continue;
        }
        $data[$key] = $val;
      }

      if(empty($_POST["well_cms_event_link"])){
        $data["well_cms_event_link"] = $_POST["surveylink_well_cms_event_link"];
      }

      if(!isset($data["well_cms_active"])){
        $data["well_cms_active"] = "0";
      }
      $result = RC::writeToApi($data, array("forceAutoNumber" => "true", "returnContent" => "auto_ids", "overwriteBehavior" => "overwite", "type" => "flat"), $API_URL, $API_TOKEN);

      //import the picture file
      $split  = explode(",",$result[0]);
      $new_id = $split[0];
      if(!empty($_FILES["well_cms_pic"])){
        RC::writeFileToApi($_FILES["well_cms_pic"], $new_id, "well_cms_pic", null, $API_URL, $API_TOKEN);
      }
    }elseif($_POST["action"] == "delete"){
      if(!empty($_POST["id"])){
        $data = array(
             "action"       => "delete"
            ,"content"      => "record"
            ,"records"      => array($_POST["id"])
          );
        $result = RC::callApi($data, array(), $API_URL, $API_TOKEN);
      }
      exit;
    }elseif($_POST["action"] == "edit"){
      if(!empty($_POST["id"])){
        $data[]   = array(
             "record"     => $_POST["id"]
            ,"field_name" => $_POST["field_name"]
            ,"value"      => $_POST["value"]
          );
        $result   = RC::writeToApi($data, array("format" => "json", "overwriteBehavior" => "overwite", "type" => "eav"), $API_URL, $API_TOKEN);

        $data[]   = array(
             "record"     => $_POST["id"]
            ,"field_name" => "well_cms_update_ts"
            ,"value"      => date('Y-m-d H:i:s')
        );
        $result   = RC::writeToApi($data, array("format" => "json", "overwriteBehavior" => "overwite", "type" => "eav"), $API_URL, $API_TOKEN);
      }
      exit;
    }elseif($_POST["action"] == "edit_img"){
      if(!empty($_POST["id"])){
        RC::writeFileToApi($_FILES["well_cms_pic"], $_POST["id"], "well_cms_pic", null, $API_URL, $API_TOKEN);

        $data[]   = array(
             "record"     => $_POST["id"]
            ,"field_name" => "well_cms_update_ts"
            ,"value"      => date('Y-m-d H:i:s')
        );
        $result   = RC::writeToApi($data, array("format" => "json", "overwriteBehavior" => "overwite", "type" => "eav"), $API_URL, $API_TOKEN);
      }
    }elseif($_POST["action"] == "minichallenge"){
        if(isset($_POST["portal_mc_name"]) && isset($_POST["portal_mc_year"])){
            $data = array(
                 "portal_mc_name" => $_POST["portal_mc_name"]
                ,"portal_mc_year" => $_POST["portal_mc_year"]
                ,"portal_mc_link" => $_POST["portal_mc_link"]
                ,"id" => "whatever_required_but_wont_be_used"
            );
            $result = RC::writeToApi($data, array("forceAutoNumber" => "true", "returnContent" => "auto_ids", "overwriteBehavior" => "overwite", "type" => "flat"), $API_URL, $API_TOKEN);

            //import the picture file
            $split  = explode(",",$result[0]);
            $new_id = $split[0];

            if(!empty($_FILES["portal_mc_img"])){
                RC::writeFileToApi($_FILES["portal_mc_img"], $new_id, "portal_mc_img", null, $API_URL, $API_TOKEN);
            }
        }
    }elseif($_POST["action"] == "gamify_points"){
      if(!empty($_POST["id"])){
            $data = array();
            foreach($gamify_fields as $var){
                if($var == "gamify_pts_wof"){
                    continue;
                }
                $persist    = isset($_POST[$var."_persist"]) ? true : false;
                $session    = isset($_POST[$var."_session"]) ? true : false;

                $data[]     = array(
                    "record"      => $_POST["id"]
                    ,"field_name" => $var
                    ,"value"      => json_encode(array("value" => $_POST[$var], "persist" => $persist, "session" => $session))
                );
            }
            $result   = RC::writeToApi($data, array("format" => "json", "overwriteBehavior" => "overwite", "type" => "eav"), $API_URL, $API_TOKEN);
      }
    }elseif($_POST["action"] == "wof_quotes"){
        if(!empty($_POST["id"])){
            $full_json = !empty($_POST["full_json"]) ? json_decode($_POST["full_json"],1) : array();

            if(!empty($_POST["quote"])){
                array_push($full_json, array("cite" => $_POST["cite"], "quote" => $_POST["quote"]));
            }

            $data = array();
            $data[] = array(
                "record"       => $_POST["id"]
            ,"field_name"   => "wof_quotes"
            ,"value"        => json_encode($full_json)
            );

            $result   = RC::writeToApi($data, array("format" => "json", "overwriteBehavior" => "overwite", "type" => "eav"), $API_URL, $API_TOKEN);
        }
    }

}

// DEFAULT VALUES
$loc          = isset($_REQUEST["loc"]) ? $_REQUEST["loc"] : "1";
$cat          = isset($_REQUEST["cat"]) ? $_REQUEST["cat"] : "1";

$types        = array(0 => "Events", 1 => "Monthly Goals", 2 => "Resources", 3 => "Gamification", 4 => "Mini Challenges", 5=> "Tools");
$locs         = array(1 => "US", 2 => "Taiwan");

include("models/inc/gl_header.php");
?>
<style>
form {
  clear:both;
  margin:20px;
}

#main-content .well {
  background-size:7%;
  padding-top:88px;
}
#cms h2{
  font-size:200%;
  margin-bottom:60px;
}
#cms a{
  text-decoration:none;
}

#cms label {
    display:block;
    margin-bottom:15px;
}

input[type='number']{
    width:50px;
}

#gamify input[type='checkbox']{
    margin-left:10px;
}
#ed_items{
  width:calc(100% - 20px); margin:10px;
  border-top:1px solid #333;
  border-right:1px solid #333;
}
#ed_items th, #ed_items td {
  border-bottom:1px solid #333;
  border-left:1px solid #333;
  padding:5px 8px;
  font-size:77%;
  vertical-align: top;
}


#loc {
  float:right;
  margin:68px 10px 0;
}
#folders{
  margin:0; padding:10px;
  float:left;
}
#folders li {
  display:inline-block;
  list-style:none;
  margin-left:20px;
}
#folders li:first-child{
  margin-left:0;
}
#folders a{
  display:inline-block;
  text-align:center;
  width:120px;
  height:80px;
  border:1px solid #ccc;
  border-radius:5px;
  box-shadow:1px 1px 3px #ccc;
  line-height:80px;
}
#folders a.on{
  background:#efefef;
  box-shadow:1px 1px 3px #333;
}

#newevent {
  margin:10px;
  border:1px solid #ccc;
  border-radius:5px;
}
#newevent fieldset {
  padding:10px;
}

#additem {
  width:200px;
  display:block;
  margin:0 auto;
  font-weight:bold;
  text-decoration: none;
  transition:.5s opacity;
  opacity: 1;
}
#additem.fadeout {
  opacity:0;
}

#newevent{
  display:none;
}
#newevent h3{
  text-transform:capitalize;
  font-size:180%;
  margin-bottom:20px;
}
#newevent input[type="submit"]{
  display:block;
  width:200px;
  margin:20px auto;
  border-radius:5px;
}

.newevent_item{
  margin-bottom:15px;
}
.newevent_item label{
  display:block;
}
.newevent_item label span{
  display:block;
}

.edit_img i,
.newevent_item label i{
  color:red;
  font-weight:normal;
  font-style:italic;
}
.newevent_item input[type="text"],
.newevent_item textarea{
  width:50%;
  border-radius:5px;
}
.newevent_item label em {
  display:inline-block;
  margin-right:10px;
  font-style:normal;
}

.content textarea,
.active select,
.order input,
.link input,
.subject input {
  width:100%;
  border:1px solid transparent;
  padding:0 5px; 
  background:none;
  cursor:pointer;
  height:30px;
}

.content textarea:focus,
.active select:focus,
.order input:focus,
.link input:focus,
.subject input:focus{
  border:1px solid yellow;
  box-shadow:0 0 5px 5px yellow;
}

.content textarea.edited,
.active select.edited,
.order input.edited,
.link input.edited,
.subject input.edited{
  border:1px solid yellow;
  box-shadow:0 0 3px 5px lightgreen;
}

.content textarea {
  padding:4px 5px;
}
.active select{
  width:50px;
}
.order input {
  width:40px;
}


#ed_items th {
  vertical-align: bottom
}
.order{
  width:58px;
}
.active {
  width:68px;
}
.actions{
  width:82px;
}


.well .event_img {
  max-width:200px;
}

.well .actions{
  width:100%;
  padding:15px;
}

#wof_game cite{
    display:block;
}
#wof_game blockquote{
    margin-top:10px;
    padding-top:0;
    padding-bottom:0;
    position:relative;
}
#wof_game blockquote .delete_quote{
    position: absolute;
    top: 0px;
    left: -3px;
    border: 1px solid red;
    background: red;
    color: #fff;
    border-radius: 50px;
    width: 20px;
    height: 20px;
    text-align: center;
    line-height: 100%;
}
#wof_game details {
    margin: 20px 0px;
    font-size: 120%;
    background: #efefef;
    padding: 10px;
    border-radius: 5px;
    cursor: pointer;
}
#wof_game textarea{
    width: 50%;
    height: 100px;
}

textarea[name='full_json']{
    position:absolute;
    z-index:-999;
    visibility:hidden;
}

#mini_cs td, #mini_cs th{
    padding:5px;
}
#minic {
    padding-top: 40px;
}
#minic input, #minic select{
    display:block;
}
.mc_year {
    text-align:center;
}
.mc_img img{
    max-width:200px;
    max-height:150px;
}
</style>
<div id="content" class="container" role="main" tabindex="0">
<div class="row"> 
  <div id="main-content" class="col-md-12" role="main">
    <div id="cms" class="well">
      <h2>WELL Portal Admin CMS</h2>
      <select id='loc'>
        <option value=1 <?php if($loc == 1) echo "selected"?>>US</option>
        <option value=2 <?php if($loc == 2) echo "selected"?>>Taiwan</option>
      </select>
      
      <ul id="folders">
        <li><a href="?cat=1"  data-val=1 class="<?php if($cat == 1) echo "on"?>"><?php echo $types[1] ?></a></li>
        <li><a href="?cat=0"  data-val=0 class="<?php if($cat == 0) echo "on"?>"><?php echo $types[0] ?></a></li>
        <li><a href="?cat=2" data-val=2 class="<?php if($cat == 2) echo "on"?>"><?php echo $types[2] ?></a></li>
        <li><a href="?cat=4" data-val=4 class="<?php if($cat == 4) echo "on"?>"><?php echo $types[4] ?></a></li>
        <li><a href="?cat=3" data-val=3 class="<?php if($cat == 3) echo "on"?>"><?php echo $types[3] ?></a></li>
        <li><a href="?cat=5" data-val=5 class="<?php if($cat == 5) echo "on"?>"><?php echo $types[5] ?></a></li>
      </ul>

      <?php
      if($cat < 3){
        $api_url      = SurveysConfig::$projects["ADMIN_CMS"]["URL"];
        $api_token    = SurveysConfig::$projects["ADMIN_CMS"]["TOKEN"];
        $extra_params = array(
          'content'   => 'metadata',
          'format'    => 'json'
        );

        $results      = RC::callApi($extra_params, true, $api_url, $api_token); 

        $fields       = array_column($results, 'field_name'); 
        $labels       = array_column($results, 'field_label'); 
        
        // [0] => id
        // [1] => well_cms_catagory
        // [2] => well_cms_loc
        // [3] => well_cms_lang
        // [4] => well_cms_subject
        // [5] => well_cms_content
        // [6] => well_cms_pic
        // [7] => well_cms_event_link
        // [8] => well_cms_text_link
        // [9] => well_cms_active
        // [10] => well_cms_displayord
        // [11] => well_cms_create_ts
        // [12] => well_cms_update_ts
        // [13] => well_cms_domain
        // [14] => well_cms_image_catagory
        // [15] => well_cms_surveylink

        $mon_display  = array(3,4,5,6,9,12);
        $evt_display  = array(10,3,7,4,5,6,9,12);
        $res_display  = array(10,3,7,4,5,13,14,9,12);

        $extra_params = array(
          'content'   => 'record',
          'format'    => 'json'
        );
        $filterlogic  = array();
        if($loc){
          $filterlogic[] = '[well_cms_loc] = "'.$loc.'"';
        }
        if($cat || $cat === "0"){
          $filterlogic[] = '[well_cms_catagory] = "'.$cat.'"';
        }
        if(count($filterlogic)){
          $extra_params["filterLogic"] = implode(" and ", $filterlogic);
        }
        $events       = RC::callApi($extra_params, true, $api_url, $api_token); 
        ?>
        <table id="ed_items">
          <thead>
            <tr>
              <?php
              $display = $cat;
              // == 0 ? $evt_display : $mon_display;
              switch($display){
                case 0:
                  $display = $evt_display;
                  break;
                case 1:
                  $display = $mon_display;
                  break;
                case 2:
                  $display = $res_display;
                  break;
                default:
                  $display = $mon_display;
              }
             // print_r($labels);
              foreach($display as $item){
                echo "<th>".$labels[$item]."</th>\n";
              }
              ?>
            <th class='actions'></th>
            </tr> 
          </thead>
          <tbody>
              <?php
              $trs            = array();
              $monthly_active = false;

              foreach($events as $event){
                $eventpic   = "";
                $recordid   = $event["id"];
                $file_curl  = RC::callFileApi($recordid, "well_cms_pic", null, $API_URL,$API_TOKEN);
                if(strpos($file_curl["headers"]["content-type"][0],"image") > -1){
                  $split    = explode("; ",$file_curl["headers"]["content-type"][0]);
                  $mime     = $split[0];
                  $split2   = explode('"',$split[1]);
                  $imgname  = $split2[1];
                  $eventpic = '<img class="event_img" src="data:'.$mime.';base64,' . base64_encode($file_curl["file_body"]) . '">';
                }
                $selected = array("Yes" => "", "No" => "");

                $trs[]    = "<tr data-id='$recordid' class='editable'>";
                $active   = $event["well_cms_active"] ? "Yes" : "No";
                $selected[$active] = "selected";

                if($cat == 0){ //event 
                  $trs[] = "<td class='order'><input type='number' name='well_cms_displayord' value='".$event["well_cms_displayord"] ."'/></td>";
                  
                  $selected_lang = isset($event["well_cms_lang"]) ? $event["well_cms_lang"] : null;
                  $trs[] = "<td ><select name='well_cms_lang'>";
                  foreach($languages as $idx => $language){
                    $langselected = $selected_lang && $selected_lang == $idx ? "selected" : "";
                    $trs[] = "<option value='$idx' $langselected>$language</option>";
                  }
                  $trs[] = "</select></td>";

                  $trs[] = "<td class='link'><input type='text' name='well_cms_event_link' value='".$event["well_cms_event_link"] ."'/></td>";
                  $trs[] = "<td class='subject'><input type='text' name='well_cms_subject' value='".$event["well_cms_subject"]  ."'/></td>";
                  $trs[] = "<td class='content'><textarea name='well_cms_content'>".$event["well_cms_content"]."</textarea></td>";
                  $trs[] = "<td class='pic'>$eventpic";
                  $trs[] = "<form class='edit_img' action='cms.php' method='post' enctype='multipart/form-data'>";
                  $trs[] = "<input type='hidden' name='action' value='edit_img'/>";
                  $trs[] = "<input type='hidden' name='id' value='$recordid'/>";
                  $trs[] = "<input type='file' name='well_cms_pic'/>";
                  $trs[] = "<i>WxH must be 3:4 ratio (ie. 300px by 400px)</i>";
                  $trs[] = "</form>";
                  $trs[] = "</td>";
                }elseif ($cat == 1){ //monthly goals
                  if(!$monthly_active && $active == "Yes"){
                    $monthly_active = true;
                  }
                  $selected_lang = isset($event["well_cms_lang"]) ? $event["well_cms_lang"] : null;
                  $trs[] = "<td ><select name='well_cms_lang'>";
                  foreach($languages as $idx => $language){
                    $langselected = $selected_lang && $selected_lang == $idx ? "selected" : "";
                    $trs[] = "<option value='$idx' $langselected>$language</option>";
                  }
                  $trs[] = "</select></td>";
                  
                  $trs[] = "<td class='subject'><input type='text' name='well_cms_subject' value='".$event["well_cms_subject"]  ."'/></td>";
                  $trs[] = "<td class='content'><textarea name='well_cms_content'>".$event["well_cms_content"]."</textarea></td>";
                  $trs[] = "<td class='pic'>$eventpic";
                  $trs[] = "<form class='edit_img' action='cms.php' method='post' enctype='multipart/form-data'>";
                  $trs[] = "<input type='hidden' name='action' value='edit_img'/>";
                  $trs[] = "<input type='hidden' name='id' value='$recordid'/>";
                  $trs[] = "<input type='file' name='well_cms_pic'/>";
                  $trs[] = "<i>WxH must be 3:4 ratio (ie. 300px by 400px)</i>";
                  $trs[] = "</form>";
                  $trs[] = "</td>";
                }elseif ($cat == 2){
                  $trs[] = "<td class='order'><input type='number' name='well_cms_displayord' value='".$event["well_cms_displayord"] ."'/></td>";
                  
                  $selected_lang = isset($event["well_cms_lang"]) ? $event["well_cms_lang"] : null;
                  $trs[] = "<td ><select name='well_cms_lang'>";
                  foreach($languages as $idx => $language){
                    $langselected = $selected_lang && $selected_lang == $idx ? "selected" : "";
                    $trs[] = "<option value='$idx' $langselected>$language</option>";
                  }
                  $trs[] = "</select></td>";

                  $trs[] = "<td class='link'><input type='text' name='well_cms_event_link' value='".$event["well_cms_event_link"] ."'/></td>";
                  $trs[] = "<td class='link'><input type='text' name='well_cms_subject' value='".$event["well_cms_subject"] ."'/></td>";
                  $trs[] = "<td class='content'><textarea name='well_cms_content'>".$event["well_cms_content"]."</textarea></td>";
                  $trs[] = "<td class='domain'>".$radar_domains[$event["well_cms_domain"]-1]."</td>";
                  $selected_image_type = isset($event["well_cms_image_catagory"]) ? $event["well_cms_image_catagory"] : null;
                  
                  $trs[] = "<td ><select name='well_cms_image_catagory'>";
                  foreach($image_catagory as $idx => $imgtype){
                    $imgselected = $selected_image_type && $selected_image_type == $idx ? "selected" : "";
                    $trs[] = "<option value='$idx' $imgselected>$imgtype</option>";
                  }
                  $trs[] = "</select></td>";
                }


                $trs[] = "<td class='active'><select name='well_cms_active'>";
                $trs[] = "<option value='0' ".$selected["No"].">No</option>";
                $trs[] = "<option value='1' ".$selected["Yes"].">Yes</option>";
                $trs[] = "</select></td>";
                $trs[] = "<td class='updated'>".$event["well_cms_update_ts"]."</td>";
               // $trs[] = "<td class='domain'>".$event["well_cms_domain"]."</td>";
                $trs[] = "<td class='editbtns'><a href='#' class='deleteid btn btn-danger' data-id='".$event["id"]."'>Delete</a></td>";
                $trs[] = "</tr>";
              }
              echo implode("\n",$trs);
              ?>
          </tbody>
          <tfoot>
            <tr class="addnew">
            <td colspan="<?php echo count($display) + 1 ?>">
              <a id="additem" href="#">+ add item to <?php echo $types[$cat] ?></a>
              
              <form id="newevent" action="cms.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="newevent"/>
                <input type="hidden" name="well_cms_loc" value="<?php echo $loc?>"/>
                <input type="hidden" name="well_cms_catagory" value="<?php echo $cat?>"/>
                <input type="hidden" name="loc" value="<?php echo $loc?>"/>
                <input type="hidden" name="cat" value="<?php echo $cat?>"/>
                <fieldset>
                <h3>New <?php echo substr($types[$cat],0,strlen($types[$cat]) - 1) ?> for <?php echo $locs[$loc] ?></h3>
                <?php
                $fields     = array();
                $new_fields = $display;
                array_pop($new_fields);

                foreach($new_fields as $idx){
                  $field = $results[$idx];
                  $label = $field["field_label"];
                  $varid = $field["field_name"];
                  $type  = $field["field_type"];
                  $setan = $field["select_choices_or_calculations"];

                  $fields[] = "<div class='newevent_item'>";
                  switch($type){
                    case "dropdown":
                      $split = explode("|",$setan);
                      
                      $fields[] = "<label name='$varid'>";
                      $fields[] = "<span>$label</span>";
                      $fields[] = "<select name='$varid'>";
                      foreach($split as $pair){
                        $value_label  = explode(", ",$pair);
                        $key          = trim($value_label[0]); 
                        $val          = trim($value_label[1]); 
                        $fields[]     = "<option value='".$key."' >".$val."</option>";
                      }
                      $fields[] = "</select>";
                      $fields[] = "</label>";
                    break;

                    case "truefalse":
                      $checked  = array("y" => "", "n" => "");
                      $disabled = "";
                      if($cat == 1 && $varid == "well_cms_active" && $monthly_active){
                        $checked["n"] = "checked";
                        // $disabled = "disabled=true";
                      }else{
                        $checked["y"] = "checked";
                      }
                      $fields[] = "<label name='$varid'>";
                      $fields[] = "<span>$label</span>";
                      $fields[] = "<em><input name='$varid' type='radio' ".$checked["y"]." $disabled value='1'> Yes</em>";
                      $fields[] = "<em><input name='$varid' type='radio' ".$checked["n"]." $disabled value='0'> No</em>";
                      $fields[] = "</label>";
                    break;

                    case "notes":
                      $fields[] = "<label name='$varid'>";
                      $fields[] = "<span>$label</span>";
                      $fields[] = "<textarea name='$varid'></textarea>";
                      $fields[] = "</label>";
                    break;

                    case "file":
                      $fields[] = "<label name='$varid'>";
                      $fields[] = "<span>$label</span>";
                      $fields[] = "<input type='file' name='$varid'/>";
                      if($varid == "well_cms_pic"){
                        $fields[] = "<i>WxH must be 3:4 ratio (ie. 300px by 400px)</i>";
                      }
                      $fields[] = "</label>";
                    break;

                    default: //text
                      $type = "text";
                      $val  = "";
                      if($varid == "well_cms_displayord"){
                        $type = "number";
                        $val  = count($events) + 1;
                      }elseif($cat==0 && $varid == "well_cms_event_link"){
                        $fields[] = "<fieldset style='border:1px solid #333'>";
                        $fields[] = "<label name='$varid'>";
                        $fields[] = "<span>$label/Survey Link</span>";
                        $fields[] = "<select name='surveylink_$varid'>";
                        $fields[] = "<option>-</option>";
                        foreach(SurveysConfig::$supp_surveys as $suppname => $supplabel){
                          $fields[]     = "<option value='".$suppname."' >".$supplabel."</option>";
                        }
                        $fields[]     = "<option value='wellbeing_questions' >Stanford WELL for Life</option>";
                        $fields[] = "</select>";
                        $fields[] = "</label>";
                        $label    = "<b>OR</b> Event Link:";
                      }
                      $fields[] = "<label name='$varid'>";
                      $fields[] = "<span>$label</span>";
                      $fields[] = "<input type='$type' name='$varid' value='$val'/>";
                      $fields[] = "</label>";
                      if($cat==0 && $varid == "well_cms_event_link"){
                        $fields[] = "</fieldset>";
                      }
                    break;
                  }
                  $fields[] = "</div>";
                }
                print(implode("\n",$fields));
                ?>
                </fieldset>
                <input type="submit" name="submit" class="btn btn-success" value="Save to <?php echo $types[$cat]?>"/>
              </form>
            </td>
            </tr>
          </tfoot>
        </table>
        <?php
      }elseif($cat == 4){
          ?>
          <div>
              <form id="minic" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="action" value="minichallenge"/>
                  <fieldset>
                      <h3>Add/Edit Mini-Challenge:</h3>
                      <label>Mini Challenge Name: <input type="text" name="portal_mc_name"/></label>
                      <label>Mini Challenge link: <input type="text" name="portal_mc_link"/></label>
                      <label>Mini Challenge Year: <select name="portal_mc_year">
                              <option val="2019">2019</option>
                              <option val="2020">2020</option>
                              <option val="2021">2021</option>
                              <option val="2022">2022</option>
                              <option val="2023">2023</option>
                              <option val="2024">2024</option>
                          </select></label>
                      <label>Mini Challenge Reward Background: <input type="file" name="portal_mc_img"/></label>
                      <input type="submit" value="Save Mini Challenge"/>
                      <hr>
                      <?php
                      $extra_params   = array(
                          'content'   => 'record',
                          'format'    => 'json',
                          "fields"    => array("id", "portal_mc_name","portal_mc_year","portal_mc_link"),
                          "filterLogic" => "[portal_mc_name] != '' "
                      );
                      $minics        = RC::callApi($extra_params, true, $API_URL, $API_TOKEN);
                      ?>
                      <table id="mini_cs" border="1" width="100%">
                          <thead><th>Mini Challenge</th><th class="mc_year">Year</th><th>Link</th><th>Reward img</th></thead>
                          <tbody>
                          <?php
                            foreach($minics as $minic){
                                $recordid   = $minic["id"];
                                $file_curl  = RC::callFileApi($recordid, "portal_mc_img", null, $API_URL,$API_TOKEN);
                                if(strpos($file_curl["headers"]["content-type"][0],"image") > -1){
                                    $split    = explode("; ",$file_curl["headers"]["content-type"][0]);
                                    $mime     = $split[0];
                                    $split2   = explode('"',$split[1]);
                                    $imgname  = $split2[1];
                                    $eventpic = '<img class="portal_mc_img" src="data:'.$mime.';base64,' . base64_encode($file_curl["file_body"]) . '">';
                                }
                                $shortname = strtolower($minic["portal_mc_name"]);
                                $shortname = str_replace(" ","",$shortname);
                                $shortname = str_replace("-","",$shortname);
                                $shortname = str_replace("and","",$shortname);
                                $shortname = str_replace(",","",$shortname);

                                echo "<tr><td>".$minic["portal_mc_name"]."</td><td class='mc_year'>".$minic["portal_mc_year"]."</td><td>".$minic["portal_mc_link"]."</td><td class='mc_img'>".$eventpic."</td></tr>";
                            }
                          ?>
                          </tbody>
                      </table>

                  </fieldset>

              </form>

          </div>
          <?php
      }elseif($cat == 3){
          ?>
          <div>
              <form id="gamify" method="post">
                  <input type="hidden" name="action" value="gamify_points"/>
                  <input type="hidden" name="id" value="9999"/>
                  <fieldset>
                      <h3>Point Values for Click Actions (Site Wide):</h3>
                      <?php
                      $extra_params   = array(
                          'content'   => 'record',
                          'format'    => 'json',
                          "records"   => 9999,
                          "fields"    => $gamify_fields
                      );
                      $results        = RC::callApi($extra_params, true, $API_URL, $API_TOKEN);
                      $gamify         = !empty($results) ? current($results) : $gamify_fields;

                      foreach($gamify as $var => $decode){
                          if($var == "gamify_pts_wof"){
                              echo "special case for wof";
                              continue;
                          }
                          $vals = json_decode($decode,1);
                          $persist_checked = $vals["persist"] ? "checked" : "";
                          $session_checked = $vals["session"] ? "checked" : "";
                          echo "<label><b>$var:</b><br>
                                    <input type='number' name='$var' value='".$vals["value"]."'> 
                                    <input type='checkbox' name='".$var."_persist' $persist_checked/> Persist
                                    <input type='checkbox' name='".$var."_session' $session_checked/> Session
                                   </label>";
                      }
                      ?>
                  </fieldset>
                  <input type="submit" value="Save Point Values"/>
              </form>
              <hr>

              <form id="wof_game" method="post">
                  <input type="hidden" name="action" value="wof_quotes"/>
                  <input type="hidden" name="id" value="9999"/>

                  <fieldset>
                      <h3>Add new puzzle/quote to WELL OF FORTUNE game:</h3>
                      <?php
                      $extra_params   = array(
                          'content'   => 'record',
                          'format'    => 'json',
                          "records"   => 9999,
                          "fields"    => "wof_quotes"
                      );
                      $results        = RC::callApi($extra_params, true, $API_URL, $API_TOKEN);
                      $quotes         = !empty($results) ? current($results) : array();
                      $full_json      = isset($quotes["wof_quotes"]) && !is_null($quotes["wof_quotes"]) ? $quotes["wof_quotes"] : "[]";
                      $quotes         = json_decode($full_json,1);

                      $quotes_html  = array();
                      if(is_array($quotes)){
                          foreach($quotes as $idx => $quote){
                              $quotes_html[] = "<blockquote><a href='#' rel='$idx' class='delete_quote'>x</a>".$quote["quote"]."<cite>~".$quote["cite"]."</cite></blockquote>";
                          }
                      }
                      $quotes_html = implode("\r\n",$quotes_html);
                      echo "<details>
                            <summary>Quotes in Rotation</summary>
                            $quotes_html
                        </details>";
                      ?>

                      <textarea name="full_json"><?php echo $full_json?></textarea>
                      <label><div>Citation</div>
                          <input type="text" name="cite"/></label>
                      <label><div>Quote</div>
                          <textarea name="quote"></textarea></label>
                  </fieldset>
                  <input type="submit" value="Save New Quote"/>
              </form>
              <hr>
          </div>
          <?php
        }else{
          ?>
          <div >
            <form id="viewas" method="GET">
              <fieldset>
                <h3>View portal as a User:</h3>
                <label>
                  <b>User Id:</b>
                  <input type="number" name="user_record_id"/>
                  <input type="submit"  value="Go"/>
                </label>
              </fieldset>
            </form>
            <hr>

            <div class="actions">
              <h3>One off actions</h3>
              <ul>
                <li><a href="scratch.php?action=well_score_calc">Recalculate Well Score</a></li>
                <li><a href="scratch.php?action=calc_bmi_edu">Recalculate BMI and Education</a></li>
                <li><a href="scratch.php?action=update_domain_json">Recalculate Domain JSON scores/labels</a></li>
              </ul>
            </div>
          </div>
          <?php
        }
        ?>
      </div>  
    </div>
  </div>
</div>
<?php 
include("models/inc/gl_footer.php");
?>
<script>
$(document).ready(function(){
  $("#loc").change(function(){
    var loc       = $(this).val();
    var cat       = $("#folders .on").data("val");
    var link      = window.location.href.split('?')[0];
    location.href = link + "?cat=" + cat + "&loc=" + loc;
  });
  $("#folders a").click(function(){
    var loc       = $("#loc").val();
    var cat       = $(this).data("val");
    var link      = window.location.href.split('?')[0];
    location.href = link + "?cat=" + cat + "&loc=" + loc;
    return false;
  });
  $("#additem").click(function(){
    var el = $(this);
    el.addClass("fadeout");
    setTimeout(function(){
      el.slideUp();
      $("#newevent").slideDown();
    },500);
    return false;
  });
  $(".deleteid").click(function(){
    if(confirm("Confirm deletion of this event")){
      var id_to_delete = $(this).data("id");
      $.ajax({
        url : "cms.php",
        type: 'POST',
        data: "action=delete&id=" + id_to_delete,
        success:function(result){
          $("tr[data-id='"+id_to_delete+"']").fadeOut();
        }
      });
    }
    return false;
  });
  $("#ed_items tbody :input[name!='well_cms_pic']").change(function(){
    var el = $(this);
    var id_to_edit = $(this).parents("tr").data("id");
    var field_name = $(this).attr("name");
    var value      = $(this).val();
    $.ajax({
      url : "cms.php",
      type: 'POST',
      data: "action=edit&id=" + id_to_edit + "&field_name=" + field_name + "&value=" + value,
      success:function(result){
        el.addClass("edited");
        setTimeout(function(){
          el.removeClass("edited");
        },1000);
      }
    });
    return false;
  });
  $("#ed_items tbody :input[name='well_cms_pic']").change(function(){
    $(this).parents("form").submit();
    console.log($(this).parents("form"));
    return false;
  });

  $(".delete_quote").click(function(){
    var idx = $(this).attr("rel");
    $(this).parent("blockquote").fadeOut(function(){
        $(this).remove();
        var full_json = JSON.parse($("input[name='full_json']").val());
        full_json.splice(idx,1);
        $("input[name='full_json']").val(JSON.stringify(full_json));
        $("#wof_game").submit();
    });
    return false;
  });
});
</script>

