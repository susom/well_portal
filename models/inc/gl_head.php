<!doctype html>
<html class="no-js" lang="en">
<head>
<meta charset="utf-8">
<meta name="google" content="notranslate">
<meta http-equiv="Content-Language" content="en">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title><?php echo $pageTitle ?></title>
<meta name="description" content="">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="assets/css/normalize.min.css">
<link rel="stylesheet" href="assets/css/bootstrap.css" type="text/css" />
<link rel="stylesheet" href="assets/css/animate.css" type="text/css" />
<link rel="stylesheet" href="assets/css/font-awesome.min.css" type="text/css" />
<link rel="stylesheet" href="assets/css/icon.css" type="text/css" />
<link rel="stylesheet" href="assets/css/font.css" type="text/css" />
<link rel="stylesheet" href="assets/css/roundslider.css" />
<link rel="stylesheet" href="assets/css/main.css">
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/vendor/modernizr-2.8.3-respond-1.4.2.min.js"></script>
<script src="assets/js/main.js"></script>
<script src="assets/js/jquery-ui-1.10.3.custom.min.js"></script>
<script src="assets/js/jquery.ui.touch-punch.min.js"></script>

<!-- Facebook Pixel Code -->
<!-- <?php
$trackpage = isset($trackpage) ? $trackpage : $bodyClass;
?>
<script>
!function(f,b,e,v,n,t,s){
if(f.fbq)return;n=f.fbq=function(){n.callMethod ? n.callMethod.apply(n,arguments) : n.queue.push(arguments)};
if(!f._fbq) f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '411269625926334');
fbq('track', '<?php echo $trackpage; ?>');
</script>
<noscript>
<img height="1" width="1" src="https://www.facebook.com/tr?id=411269625926334&ev=<?php echo $trackpage; ?>&noscript=1"/>
</noscript> -->
<!-- End Facebook Pixel Code -->
</head>
<body class="<?php echo $bodyClass ?>">
<?php
  print getSessionMessages();
?>
<?php
if(isset($_SESSION["admin_user"])){
	echo "<a href='cms.php' id='returnadmin'>Return to admin</a>";
}
?>
<div id="outter_rim">
<div id="inner_rim">
	<?php include("gl_headernav.php"); ?>