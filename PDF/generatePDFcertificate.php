<?php
require_once("../models/config.php");

require_once('fpdf181/fpdf.php');
require_once('FPDI-2.0.1/src/autoload.php');

$current_year   = !empty($_GET["complete_date"]) ? Date("Y",$_GET["complete_date"]) : Date("Y");
$current_day    = !empty($_GET["complete_date"]) ? Date("l",$_GET["complete_date"]) : Date("l");
$current_date   = !empty($_GET["complete_date"]) ? Date("M d Y",$_GET["complete_date"]) : Date("M d Y");

if(empty($loggedInUser)){
    exit;
}

// initiate FPDI
$pdf = new \setasign\Fpdi\Fpdi();
// add a page
$pdf->AddPage("L");
// set the source file
$pdf->setSourceFile('cert_of_completion.pdf');
// import page 1
$tplIdx = $pdf->importPage(1);
// use the imported page and place it at position 10,10 with a width of 100 mm
$pdf->useTemplate($tplIdx, -8, 0, 305);

// now write some text above the imported page
$pdf->SetFont('Arial');
$pdf->SetFontSize(24);
$pdf->SetTextColor(0, 0, 0);
$_w     = $pdf->GetPageWidth();
$_h     = $pdf->GetPageHeight();
$txt    = ucfirst($loggedInUser->firstname) . " " . ucfirst($loggedInUser->lastname) . " on $current_day, $current_date";
$_wtext = $pdf->GetStringWidth($txt);

$pdf->SetXY(($_w/2)-($_wtext/2), ($_h/2));
$pdf->Write(0, $txt);

//$userfolder = $loggedInUser->id . "_" . $loggedInUser->firstname . "_" . $loggedInUser->lastname;
//if (!file_exists("PDF/certs/$userfolder")) {
//    mkdir("PDF/certs/$userfolder", 0777, true);
//}

$filename 	= array();
$filename[] = $loggedInUser->id;
$filename[] = $loggedInUser->firstname;
$filename[] = $loggedInUser->lastname;
$filename[] = $current_year;
$filename   = implode("_",$filename).".pdf";
$pdf->Output($filename,'I'); //USE F for save to File