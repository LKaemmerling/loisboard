<?php
namespace Main\Admin; 
class Settings
{
	public static $settings = array(); 
	
	public static function init() 
	{
		/*$rst = \Main\DB::select("settings", "*", "id='1'");
		$row = $rst->fetch_object(); 
		
		self::$settings["page_title"] = $row->page_title; 
		self::$settings["forum_title"] = $row->forum_title; 
		self::$settings["forum_description"] = $row->forum_description; 
		self::$settings["defaultLang"] = $row->defaultLang; 
		self::$settings["homelinkPage"] = $row->homelinkPage; 
		self::$settings["dok_datenschutz"] = $row->dok_datenschutz; 
		self::$settings["dok_datenschutz_txt"] = $row->dok_datenschutz_txt; 
		self::$settings["dok_terms"] = $row->dok_terms; 
		self::$settings["dok_terms_txt"] = $row->dok_terms_txt; 
		self::$settings["dok_impressum"] = $row->dok_impressum; 
		self::$settings["dok_impressum_txt"] = $row->dok_impressum_txt; */
	}
	
	public static function loadGeneral()
	{
		$rst = \Main\DB::select("settings", "page_title, forum_title, forum_description", "id='1'");
		$row = $rst->fetch_object(); 
		
		$array = array(
			"page_title" => $row->page_title,
			"forum_title" => $row->forum_title,
			"forum_description" => $row->forum_description
		);
		
		return $array; 
	}
	
	public static function saveSystemGeneral() 
	{
		$html = ""; 
		
		if(isset($_GET["save"])) 
		{
			if($_POST["btnaction"] == "save") 
			{
				$page_title = $_POST["page_title"]; 
				$board_title = $_POST["board_title"]; 
				$forum_desc = $_POST["board_desc"]; 
				$defaultLang = $_POST["defaultLang"]; 
				\Main\DB::update("settings", "1", array(
					"page_title" => $page_title,
					"forum_title" => $board_title,
					"forum_description" => $forum_desc,
					"defaultLang" => $defaultLang
				));
				$html .= "<div class='alert alert-success'>
					".\Main\Language::$txt["alerts"]["system_general_saved"]."
				</div>";
			}
		}
		
		return $html; 
	}
	
	public static function setPageTitle()
	{
		$html = ""; 
		
		$rst = \Main\DB::select("settings", "page_title", "id='1'");
		$row = $rst->fetch_object(); 
		
		$html .= htmlspecialchars($row->page_title);
		
		return $html; 
	}
	
	public static function loadSettingsImpressum() 
	{
		$rst = \Main\DB::select("settings", "dok_impressum, dok_impressum_txt", "id='1'");
		$row = $rst->fetch_object(); 
		
		$array = array(
			"impressum" => $row->dok_impressum,
			"impressum_txt" => $row->dok_impressum_txt
		);
		
		return $array; 
	}
	
	public static function saveSystemImpressum() 
	{
		$html = ""; 
		
		if(isset($_GET["save"], $_POST["active"]))
		{
			$input = array(
				"dok_impressum" => $_POST["active"],
				"dok_impressum_txt" => $_POST["cke"]
			);
			
			\Main\DB::update("settings", "1", $input);
			$html .= "<div class='alert alert-success'>".\Main\Language::$txt["alerts"]["system_impressum_saved"]."</div>"; 
		}
		
		return $html; 
	}
	
	public static function loadSettingsTermsOfUse()
	{
		$rst = \Main\DB::select("settings", "dok_terms, dok_terms_txt", "id='1'");
		$row = $rst->fetch_object(); 
		
		$array = array(
			"terms" => $row->dok_terms,
			"terms_txt" => $row->dok_terms_txt
		);
		
		return $array; 
	}
	
	public static function saveSystemTermsOfUse()
	{
		$html = ""; 
		
		if(isset($_GET["save"], $_POST["active"]))
		{
			$input = array(
				"dok_terms" => $_POST["active"],
				"dok_terms_txt" => $_POST["cke"]
			);
			
			\Main\DB::update("settings", "1", $input);
			$html .= "<div class='alert alert-success'>".\Main\Language::$txt["alerts"]["system_terms_saved"]."</div>"; 
		}
		
		return $html; 
	}
	
	public static function loadSettingsDisclaimer() 
	{
		$rst = \Main\DB::select("settings", "dok_datenschutz, dok_datenschutz_txt", "id='1'");
		$row = $rst->fetch_object(); 
		 
		$array = array(
			"disclaimer" => $row->dok_datenschutz,
			"disclaimer_txt" => $row->dok_datenschutz_txt
		);
		
		return $array; 
	}
	
	public static function saveSystemDisclaimer() 
	{
		$html = ""; 
		
		if(isset($_GET["save"], $_POST["active"]))
		{
			$input = array(
				"dok_datenschutz" => $_POST["active"],
				"dok_datenschutz_txt" => $_POST["cke"]
			);
			
			\Main\DB::update("settings", "1", $input); 
			$html .= "<div class='alert alert-success'>".\Main\Language::$txt["alerts"]["system_disclaimer_saved"]."</div>"; 
		}
		
		return $html; 
	}
}

?>