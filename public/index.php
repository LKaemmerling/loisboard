<?php
session_start(); 
header ('Content-type: text/html; charset=utf-8');

/*
	Namespaces: 
		\Main  (Klassen: DB, Language, Plugins   Daten: php_functions)
		\Main\Board (Klassen: View, Control, Settings)
		\Main\User (klassen: View, Control, Alert)
*/

require_once(__DIR__."/../core/Plugins.php"); 
require_once(__DIR__."/../core/DB.php"); 
require_once(__DIR__."/../core/Language.php"); 
require_once(__DIR__."/../core/UserControl.php"); 
require_once(__DIR__."/../core/UserView.php");
require_once(__DIR__."/../core/BoardSettings.php"); 
require_once(__DIR__."/../core/BoardControl.php"); 
require_once(__DIR__."/../core/BoardView.php");  
require_once(__DIR__."/../core/AlertControl.php"); 
require_once(__DIR__."/../core/php_functions.php"); 



// Datenbankverbindung
require_once(__DIR__."/../config/db.php"); 
\Main\DB::init($db["host"], $db["user"], $db["pw"], $db["db"]);
unset($db); 

\Main\Language::init(); 
\Main\Language::load(\Main\Language::$lang); 

\Main\Plugins::loadActivesPHP(); 

\Main\Board\Settings::init(); 
\Main\User\Control::init(); 

if(\Main\User\Control::$logged) 
	\Main\User\Control::gainAutogainGroups(\Main\User\Control::$dbid); 


// DEVELOPMENT
//unset($_SESSION["errors"]); 
if(isset($_SESSION["errors"]))
	print_r($_SESSION["errors"]); 
error_reporting(E_ALL); 
ini_set("display_errors", 1); 
ini_set("display_startup_errors", 1); 
unset($_SESSION["errors"]); 


$html = ""; 
$page_title = ""; 
$page_title .= \Main\Plugins::hook("index.pageTitle.additional.beforeDefault", "");
$page_title .= \Main\Board\Settings::setPageTitle(); 
$page_title .= \Main\Plugins::hook("index.pageTitle.additional.afterDefault", "");
$page_title = \Main\Plugins::hook("index.pageTitle.replace", $page_title);

$html .= "<div class='pageWrapper'>";

$html .= \Main\Plugins::hook("index.pageTopLink.replace", "<a name='pageTop'></a>");

$html .= "<div class='pageHeader container-fluid'>"; // pageHeader erstellen

	$html .= "<div class='pageHeaderControlContainer container-fluid'>";
	
	
	
		// SUCHE
		$html .= "<div class='pageSearchContainer container'>";
		
			$html .= "<div class='pageSearch'>";
			
			$html .= "</div>"; // pageSearch
		
		$html .= "</div>"; //pageSearchContainer
		/////
		
		
	
	$html .= "</div>"; // pageHeaderControlContainer
	
	
	

	$html .= "<div class='pageMenueContainer container-fluid'>"; //pageMenue Container erstellen
		
		$html .= "<div class='pageMenueBar container'>"; //pageMenue Bar
			//$html .= "<div class='right'>".\Main\User\View::showMenueBar()."</div>";
			$html .= \Main\Board\View::showMainMenue(); 
		$html .= "</div>"; // pageMenue Bar 

	$html .= "</div>"; //pageMeneu Container verlassen


$html .= "</div>";  // pageHeader verlassen





$html .= "<div class='pageContentContainer container-fluid'>";

	$html .= "<div class='pageContent container'>";
	
		$html .= "<div class='pageContentRow row'>";
		
			$html .= \Main\Plugins::hook("index.pageContentRowLeft.div.start", "<div class='pageContentRowLeft col-md-9'>");
			
				$showhome = 0; 
				if(!isset($_GET["page"]))
				{
					$showhome = 1; 
				}
				
				// Login 
				if(isset($_GET["handle-login"]) && \Main\User\Control::$logged == 0)
				{
					$input = array(
						"mail" => $_POST["mail"],
						"pw1" => $_POST["pass"]
					);
					
					\Main\Plugins::hook("progress.handle-login.control", ""); 
					
					$json = json_encode($input); 
					$json = \Main\Plugins::hook("progress.handle-login.logindata.json", $json);
					$input = json_decode($json, true); 
					
					$stay_logged = 0; 
					if(isset($_POST["stay_logged"]) && $_POST["stay_logged"] == "on")
						$stay_logged = 1; 
						
					$stay_logged = \Main\Plugins::hook("progress.handle-login.stay-logged.int", $stay_logged);
					
					if(\Main\User\Control::handleLogin($input, $stay_logged))
					{
						$html .= "<div class='alert alert-success greenbox'>
							".\Main\Plugins::hook("alert.handle-login.success.txt", \Main\Language::$txt["alerts"]["login_success"])."
						</div>";
					}
					else
					{
						$html .= "<div class='alert alert-warning yellowbox'>
							".\Main\Plugins::hook("alert.handle-login.fail.txt", \Main\Language::$txt["alerts"]["login_fail"])."
						</div>";
					}
				}
				
				// Logout
				if(isset($_GET["handle-logout"]) && \Main\User\Control::$logged == 1) 
				{
					$_SESSION["logged"] = 0; 
					\Main\User\Control::$logged = 0; 
					\Main\User\Control::$dbid = 0; 
					
					\Main\Plugins::hook("progress.handle-logout.control", "");
					
					if(isset($_COOKIE["stay_in_u"]))
					{
						$sql = "DELETE FROM stay_logged_keys WHERE user='".\Main\DB::escape($_COOKIE["stay_in_u"])."' AND key1='".\Main\DB::escape($_COOKIE["stay_in_key1"])."' AND key2='".\Main\DB::escape($_COOKIE["stay_in_key2"])."' AND stamp='".\Main\DB::escape($_COOKIE["stay_in_stamp"])."'";
						\Main\DB::query($sql); 
						unset($_COOKIE["stay_in_u"]); 
						unset($_COOKIE["stay_in_key1"]); 
						unset($_COOKIE["stay_in_key2"]); 
						unset($_COOKIE["stay_in_stamp"]); 
					}
					
					$html .= "<div class='alert alert-danger redbox'>
						".\Main\Plugins::hook("alert.handle-logout.success.txt", \Main\Language::$txt["alerts"]["logout_success"])."
					</div>";
				}
				
				// Page-Seiten
				if(isset($_GET["page"]) || $showhome == 1)
				{
					if(isset($_GET["page"])) $showPage = $_GET["page"]; 
					else $showPage = \Main\Board\Settings::$settings["homelinkPage"]; 
					
					if($showPage == "register") 
					{
						$html .= \Main\User\View::showRegister(); 
					}
					
					else if($showPage == "board") 
					{
						$html .= \Main\Board\View::showBoardMain();
					}
					
					else if($showPage == "members") 
					{ 
						$html .= \Main\User\View::showMembersMain(); 
					}
					
					else if($showPage == "conversations") 
					{
						$html .= \Main\User\View::showConversationsMain(); 
					}

					else if($showPage == "alerts" && \Main\User\Control::$logged == 1) 
					{
						$html .= \Main\User\View::showAlertsMain(); 
					}
					
					else if($showPage == "privacy-policy") 
					{
						$html .= \Main\Board\View::showPrivacyPolicy(); 
					}
					
					else if($showPage == "terms") 
					{
						$html .= \Main\Board\View::showTermsOfUse(); 
					}
					
					else if($showPage == "impressum") 
					{
						$html .= \Main\Board\View::showImpressum(); 
					}
					
					else if($showPage == "settings" && \Main\User\Control::$logged == 1) 
					{
						$html .= \Main\User\View::showSettings(); 
					}
					
					else if($showPage == "designs" && \Main\Board\Settings::$settings["allow_change_design"]) 
					{
						$html .= \Main\Board\View::showDesignChoose(); 
					}
					
					else if(\Main\Plugins::hook("index.showPage.$showPage.int", "0") == "1")
					{
						$html .= \Main\Plugins::hook("index.showPage.$showPage.txt", "");
					}
				}
				
			$html .= \Main\Plugins::hook("index.pageContentRowLeft.div.end", "</div>"); //pageContentRowLeft
			
			
			$html .= \Main\Plugins::hook("index.pageContentRowRight.div.start", "<div class='pageContentRowRight col-md-3'>");
				$html .= \Main\Plugins::hook("index.contentRowRight.beforeUCP", "");
				$html .= \Main\Plugins::hook("index.contentRowRight.UCP", \Main\User\View::showUserCp()); 
				$html .= \Main\Plugins::hook("index.contentRowRight.afterUCP", "");
				if(\Main\Board\Settings::$settings["show_statistic_right"] == 1)
					$html .= \Main\Plugins::hook("index.contentRowRight.Statistics", \Main\Board\View::showStatisticsRight());
				$html .= \Main\Plugins::hook("index.contentRowRight.afterStatistics", "");
				
			$html .= \Main\Plugins::hook("index.pageContentRowRight.div.end", "</div>"); //pageContentRowRight
		
		$html .= "</div>"; //pageContentRow
	
	$html .= "</div>"; //pageContent
	
	$dstring = ""; 
	if(\Main\Board\Settings::$settings["allow_change_design_footer"] && \Main\Board\Settings::$settings["allow_change_design"])
		$dstring = "<a class='change-design-a' href='index.php?page=designs'>".\Main\Language::$txt["infos"]["footer_change_design"]."</a>"; 
	
	$html .= "<div class='pageFooter container'>";

		// Datum Rechts
		$html .= "<div class='right'>
			$dstring &nbsp;
			<i class='fa fa-clock-o fa-lg'></i> ".\Main\toTimeDat(time())." &nbsp;
			<a id='pageTopA' onclick='scrollToTop();'><i class='fa fa-arrow-up fa-lg'></i></a>
		</div>";

		$html .= \Main\Plugins::hook("index.pageFooter.beforeDoks", "");

		if(\Main\Board\Settings::$settings["dok_datenschutz"] == 1) 
			$html .= "<a class='privacy-policy' href='index.php?page=privacy-policy'>".\Main\Language::$txt["infos"]["privacy_policy"]."</a>"; 
		if(\Main\Board\Settings::$settings["dok_terms"] == 1) 
			$html .= " &nbsp; <a class='terms-of-use' href='index.php?page=terms'>".\Main\Language::$txt["infos"]["terms_of_use"]."</a>"; 
		if(\Main\Board\Settings::$settings["dok_impressum"] == 1) 
			$html .= " &nbsp; <a class='impressum' href='index.php?page=impressum'>".\Main\Language::$txt["infos"]["impressum"]."</a>"; 

		$html .= \Main\Plugins::hook("index.pageFooter.afterDoks", "");

	$html .= "</div>"; //pageFooter

$html .= "</div>"; //pageContentContainer


$html .= "</div>"; //pageWrapper

$html .= "<div class='sideFooter container-fluid'>";
	$html .= "powered by <a target='_blank' href='http://www.loisboard.at'>LoisBoard</a>";
	$html .= "<small>".\Main\User\Control::$design_footer."</small>"; 
	$html .= \Main\Plugins::hook("index.sideFooter.afterLB", "");
$html .= "</div>"; //sideFooter

$pageDescription = ""; 
$pageKeywords = ""; 

?>

<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="author" content="Sascha Loishandl" /> 
		<meta name="description" content='<?php echo $pageDescription; ?>' /> 
		<meta name="keywords" content='<?php echo $pageKeywords; ?>' /> 
		<link rel="stylesheet" href="src/css/font-awesome.min.css" />
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,600,400italic,700italic,600italic' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="src/bootstrap/css/bootstrap.min.css" />
		<?php echo \Main\Plugins::loadActivesCSS(); ?>
		<link rel="stylesheet" href="config/design.css" />
		<?php echo \Main\User\Control::$design_inc; ?>
		<link rel="shortcut icon" href="media/upload/Lbicon.png" type="image/png" />
		<script type="text/javascript" src="src/jquery-3.1.1.min.js"></script>
		<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
		<script type="text/javascript" src="data/js/notificon.js"></script>
		<script type="text/javascript" src="data/js/main.js"></script>
		<?php echo \Main\Plugins::loadActivesJS();  ?>
		<title><?php echo $page_title;  ?></title>
	</head>
	<body>
		<?php echo $html; ?>
		<!-- Google Analytics -->
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

			ga('create', 'UA-96446486-1', 'auto');
			ga('send', 'pageview');

		</script>

	</body>
</html>