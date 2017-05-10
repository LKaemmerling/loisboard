<?php
ob_start(); 
session_start(); 
header ('Content-type: text/html; charset=utf-8');

/*
	Namespaces: 
		\Main  (Klassen: DB, Language, Plugins   Daten: php_functions)
		\Main\Admin (Klassen: Control, View, Settings)
		\Main\User (Klassen: Control)
*/

require_once("core/Plugins.php"); 
require_once("../core/DB.php"); 
require_once("core/Language.php"); 
require_once("core/AdminControl.php"); 
require_once("core/AdminView.php"); 
require_once("core/UserControl.php"); 
require_once("core/BoardSettings.php"); 
require_once("../core/php_functions.php"); 

require_once("../config/db.php"); 
\Main\DB::init($db["host"], $db["user"], $db["pw"], $db["db"]);
unset($db); 

\Main\Language::init(); 
\Main\Language::load(\Main\Language::$lang); 

\Main\Plugins::loadActivesPHP(); 

\Main\Admin\Settings::init(); 
\Main\User\Control::init(); 


$html = ""; 
$page_title = ""; 

$page_title .= \Main\Admin\Settings::setPageTitle(); 

$html .= "<div id='middlewindow_bg' style='display:none;' class='middlewindow-bg'><div id='middlewindow' class='middlewindow'></div></div>"; 

$html .= "<div class='pageWrapper'>";

$html .= "<div class='pageHeader container-fluid'>";

	$html .= "<div class='pageMenueContainer container-fluid'>"; //pageMenue Container erstellen
		
		$html .= "<div class='pageMenueBar container'>"; //pageMenue Bar
			$html .= \Main\Admin\View::showMainMenue(); 
		$html .= "</div>"; // pageMenue Bar 

	$html .= "</div>"; //pageMeneu Container verlassen

$html .= "</div>"; //pageHeader


$html .= "<div class='pageContentContainer container-fluid'>";

	$html .= "<div class='pageContent container'>";
	
		$html .= "<div class='pageContentRow row'>";
		
			$html .= "<div class='pageContentRowLeft col-md-3'>";  /// LINKS
			
				if(\Main\User\Control::$logged) 
				{
					$html .= \Main\Admin\View::DisplayMainLeft(); 
				}
			
			$html .= "</div>"; //pageContentRowLeft
			
			
			
			$html .= "<div class='pageContentRowRight col-md-9'>";  /// RECHTS
			
				if(!\Main\User\Control::$logged) // Login anzeigen
				{
					$html .= \Main\Admin\View::DisplayLogin(); 
				}
				else // Inhalt anzeigen
				{
					$html .= \Main\Admin\View::DisplayMain(); 
				}
			
			$html .= "</div>"; //pageContentRowRight
			
		$html .= "</div>"; //pageContentRow
		
	$html .= "</div>"; //pageContent
	
	$html .= "<div class='pageFooter container'>";
	
		// Prüfen wie viele Sprachen verfügbar wären
		$available_languages = 0; 
		$scan = scandir("data/langs/"); 
		foreach($scan as $datei) 
		{
			if($datei == "" || $datei == "." || $datei == ".." || $datei == "...") continue; 
			if(is_dir($datei)) continue; 
			$available_languages++; 
		}
		
		if($available_languages > 1) 
			$html .= \Main\Language::$txt["infos"]["footer_change_language"]; 
			
	$html .= "</div>"; //pageFooter
	
$html .= "</div>"; //pageContentContainer

$html .= "</div>"; //pageWrapper

$html .= "<div class='sideFooter container-fluid'>";
	$html .= "powered by <a target='_blank' href='http://www.loisboard.at'>LoisBoard</a>";
$html .= "</div>"; // sideFooter



if($page_title != "") 
	$page_title .= " - "; 
$page_title .= "Administration"; 
?>

<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="utf8" />
		<link rel="stylesheet" href="../src/css/font-awesome.min.css" />
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,600,400italic,700italic,600italic' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="../src/bootstrap/css/bootstrap.min.css" />
		<link rel="stylesheet" href="config/design.css" />
		<?php echo \Main\Plugins::loadActivesCSS(); ?>
		<link rel="icon" href="../media/upload/Lbicon.png" />
		<script type="text/javascript" src="../src/jquery-3.1.1.min.js"></script>
		<script type="text/javascript" src="../ckeditor/ckeditor.js"></script>
		<script type="text/javascript" src="data/js/main.js"></script>
		<script type="text/javascript" src="data/js/jscolor.min.js"></script>
		<?php echo \Main\Plugins::loadActivesJS(); ?>
		<title><?php echo $page_title; ?></title>
	</head>
	<body>
		<?php echo $html; ?>
	</body>
</html>
<?php ob_end_flush(); ?>