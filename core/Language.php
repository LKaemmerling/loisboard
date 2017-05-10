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
	
	public static function load($lang, $publicFolder=false) 
	{
		$file = "data/langs/lang.".$lang.".php";
		$file2 = "data/langs/lang.".self::$defaultLang.".php";
		if(file_exists($file)) 
		{
			if(!$publicFolder)
				require_once(__DIR__.$file); 
			else
				require_once(__DIR__."/../".$file); 
			self::$txt = $language; 
		}
		else if(file_exists($file2))
		{
			if(!$publicFolder)
				require_once(__DIR__.$file2);
			else
				require_once(__DIR__."/../".$file2);  
			self::$txt = $language; 
		}
	}
}

?>