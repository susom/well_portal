<?php
//REDIRECT USERS THAT ARE NOT LOGGED IN
if(!isUserLoggedIn()) { 
    $basename = basename($_SERVER['REQUEST_URI'], '');
    $_SESSION["login_redirect"] = !strpos($basename,".php") ? "index.php" : $basename;
    $destination                = $websiteUrl . "login.php";
    header("Location: " . $destination);
    exit;
}elseif(!isUserActive()) { 
    $destination = $websiteUrl . "consent.php";
    header("Location: " . $destination);
    exit;
}else{
    $variant = "A"; //THIS WILL DETERMINE THE BUCKETS I GUESS

    // GET SURVEY LINKS
    // markPageLoadTime("checklogin : before surveys.php");
    include("models/inc/surveys.php");
    // markPageLoadTime("checklogin : after surveys.php");

    // only relevant if not not enrollment arm 1
    if(!isset($loggedInUser->consent_clicked)){
        if($user_event_arm != "enrollment_arm_1"){
            $extra_params 		= array(
                'content'     	=> 'record',
                'records'     	=> array($loggedInUser->id) ,
                'fields'        => array("portal_consent_ts","portal_consent_click_ts"),
                'events'		=> $user_event_arm,
            );
            $consent_stuff		= RC::callApi($extra_params, true, $CORE_API_URL, $CORE_API_TOKEN);
            $consent_clicks     = current($consent_stuff);
            if(empty($consent_clicks["portal_consent_click_ts"])){
                $_SESSION["needs_consent"] = true;
                $destination = $websiteUrl . "consent.php";
                header("Location: " . $destination);
                exit;
            }else{
                $loggedInUser->consent_clicked = true;
                setSessionUser($loggedInUser);
            }
        }
    }

    $one_off_wof_unlocked = false;
    // Monitor Points on Every Page?
    if((!isset($loggedInUser->portal_wof_unlocked) || !$loggedInUser->portal_wof_unlocked) && $loggedInUser->portal_game_points >= 5000){
        $loggedInUser->portal_wof_unlocked = 1;
        $one_off_wof_unlocked = true;
        $loggedInUser->updateUser(array("portal_wof_unlocked" => 1));
    }
}