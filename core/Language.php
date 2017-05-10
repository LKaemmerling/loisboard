<?php
namespace Main; 
class Language 
{

	public static $lang; 
	public static $defaultLang; 
	public static $txt = array(); 

	public static function init()
	{
		$rst = \Main\DB::select("settings", "defaultLang", "id='1'");
		$row = $rst->fetch_object(); 
		
		self::$defaultLang = $row->defaultLang; 
		
		if(isset($_COOKIE["lang"])) {
			self::$lang = $_COOKIE["lang"]; 
		}
		else { self::$lang = self::$defaultLang; }
			
	}
	
	public static function load($lang) 
	{
		$file = "data/langs/lang.".$lang.".php";
		$file2 = "data/langs/lang.".self::$defaultLang.".php";
		if(file_exists($file)) 
		{
			require_once($file); 
			self::$txt = $language; 
		}
		else if(file_exists($file2))
		{
			require_once($file2); 
			self::$txt = $language; 
		}
	}
}

?>