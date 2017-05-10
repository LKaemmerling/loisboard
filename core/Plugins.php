<?php
namespace Main; 
class Plugins 
{
	public static $listeners = array(); 
	public static $hooks = array(); 
	
	public static function hook() 
	{
		$num_args = func_num_args(); 
		$args = func_get_args(); 
			
		$hook_name = array_shift($args); 
		array_push(self::$hooks, $hook_name); 
		$html = ""; 
		
		if(!isset(self::$listeners[$hook_name])) {
			foreach($args as $arg) {
				$html .= $arg; 
			}
			return $html; 
		}
		 
			
		foreach(self::$listeners[$hook_name] as $func) {
			$args = $func($args); 
		}
		
		return $args; 
	}
	
	public static function add_listener($hook, $function_name) 
	{
		self::$listeners[$hook][] = $function_name; 
	}
	
	public static function loadActivesPHP()
	{
		$rst = \Main\DB::select("plugins", "foldername", "active='1'");
		while($row = $rst->fetch_object())
		{
			$fname = $row->foldername; 
			$dir = "data/plugins/".$fname;
			if(is_dir($dir)) 
			{
				$file = $dir."/plugin.php";
				require_once($file); 
			}
		}
	}
	
	public static function loadActivesJS() 
	{
		$html = ""; 
		$rst = \Main\DB::select("plugins", "foldername", "active='1'");
		while($row = $rst->fetch_object())
		{
			$fname = $row->foldername; 
			$dir = "data/plugins/".$fname;
			if(is_dir($dir)) 
			{
				$file = $dir."/plugin.js"; 
				$html .= "<script type='text/javascript' src='$file'></script>"; 
			}
		}
		return $html; 
	}
	
	public static function loadActivesCSS() 
	{
		$html = ""; 
		$rst = \Main\DB::select("plugins", "foldername", "active='1'");
		while($row = $rst->fetch_object())
		{
			$fname = $row->foldername; 
			$dir = "data/plugins/".$fname;
			if(is_dir($dir)) 
			{
				$file = $dir."/plugin.css"; 
				$html .= "<link rel='stylesheet' href='$file' />"; 
			}
		}
		return $html; 
	}
	
}

?>