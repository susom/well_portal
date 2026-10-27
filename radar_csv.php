<?php
// Serves the logged-in user's own radar chart CSV (appRadar.js appends ".csv" to the request)
require_once("models/config.php");

if(!isUserLoggedIn() || !isset($_SESSION["radarchart"]["display_year"])){
    http_response_code(403);
    exit;
}

$file = radarCsvPath($loggedInUser->id, $_SESSION["radarchart"]["display_year"]);
if(!is_file($file)){
    http_response_code(404);
    exit;
}

header("Content-Type: text/csv; charset=utf-8");
header("Cache-Control: private, no-store");
readfile($file);
