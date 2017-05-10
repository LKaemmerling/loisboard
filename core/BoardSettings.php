<?php
namespace Main\Board; 
class Settings
{

	public static $settings = array(); 
	
	public static function init() 
	{
		$rst = \Main\DB::select("settings", "*", "id='1'");
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
		self::$settings["dok_impressum_txt"] = $row->dok_impressum_txt; 
		self::$settings["allow_change_design_footer"] = $row->allow_change_design_footer; 
		self::$settings["allow_change_design"] = $row->allow_change_design; 
		self::$settings["allow_change_lang"] = $row->allow_change_lang; 
		self::$settings["show_statistic_right"] = $row->show_statistic_right; 
	}
	
	public static function setPageTitle()
	{
		$html = ""; 
		
		$html .= self::$settings["page_title"];
		
		$boardstart = 0; 
		$memstart = 0; 
		
		if(isset($_GET["page"]))
		{
			///// *** BOARD *** //////
			if($_GET["page"] == "board") 
			{
				if(isset($_GET["b"]))
				{
					$rst = \Main\DB::select("foren", "name", "id='".\Main\DB::escape($_GET["b"])."'");
					if($rst->num_rows > 0) 
					{
						$row = $rst->fetch_object(); 
						$name = $row->name; 
						$html .= " - " . htmlspecialchars($name); 
					}
				}
				else if(isset($_GET["k"]))
				{
					$rst = \Main\DB::select("kategorien", "name", "id='".\Main\DB::escape($_GET["k"])."'");
					if($rst->num_rows > 0) 
					{
						$row = $rst->fetch_object(); 
						$name = $row->name; 
						$html .= " - " . htmlspecialchars($name); 
					}
				}
				else if(isset($_GET["t"]))
				{
					$rst = \Main\DB::select("themen", "name", "id='".\Main\DB::escape($_GET["t"])."'");
					if($rst->num_rows > 0) 
					{
						$row = $rst->fetch_object(); 
						$name = $row->name; 
						$html .= " - " . htmlspecialchars($name); 
					}
				}
				else 
					$boardstart = 1; 
			}
			///// *** MEMBERS *** //////
			else if($_GET["page"] == "members") 
			{
				if(isset($_GET["u"]))
				{
				
				}
				else
					$memstart = 1; 
				
			}
			///// *** SETTINGS *** //////
			else if($_GET["page"] == "settings")
			{
				$general = 0; 
				$privat = 0; 
				$avatar = 0; 
				$signatur = 0; 
				if(isset($_GET["setting"]))
				{
					if($_GET["setting"] == "general") 
						$general = 1; 
					else if($_GET["setting"] == "private") 
						$privat = 1; 
					else if($_GET["setting"] == "avatar") 
						$avatar = 1; 
					else if($_GET["setting"] == "signature") 
						$signatur = 1; 
				}
				else
					$general = 1; 
					
				if($general == 1) 
					$html .= " - " . \Main\Language::$txt["infos"]["page_title_settings_general"]; 
				else if($privat == 1) 
					$html .= " - " . \Main\Language::$txt["infos"]["page_title_settings_private"]; 
				else if($avatar == 1) 
					$html .= " - " . \Main\Language::$txt["infos"]["page_title_settings_avatar"];
				else if($signatur == 1) 
					$html .= " - " . \Main\Language::$txt["infos"]["page_title_settings_signature"];
			}
		}
		else 
		{
			if(self::$settings["homelinkPage"] == "board") $boardstart = 1; 
			else if(self::$settings["homelinkPage"] == "members") $memstart = 1; 
		}
			
		if($boardstart == 1) 
			// Wenn die Startseite vom Forum aufgerufen wird! 
			$html .= " - " . \Main\Language::$txt["infos"]["page_title_board_start"]; 
		
		
		if($memstart == 1) 
			// Wenn die Startseite von den Mitgliedern aufgerufen wird! 
			$html .= " - " . \Main\Language::$txt["infos"]["page_title_members_start"]; 
		
		
		return $html; 
	}

}

?>