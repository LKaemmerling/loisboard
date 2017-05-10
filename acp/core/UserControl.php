<?php

namespace Main\User; 
class Control
{

	public static $logged = 0; 
	public static $dbid = 0; 
	public static $data = array(); 

	// Login erkennen
	public static function init()
	{
		$user_ip = getenv ("REMOTE_ADDR");
		if(isset($_SESSION["a_logged"]) && $_SESSION["a_logged"] == 1) 
		{
			$user = $_SESSION["a_dbid"]; 
			$key = $_SESSION["a_key"]; 
			
			$rst = \Main\DB::select("accounts", "id", "id='".\Main\DB::escape($user)."' AND passwort='".\Main\DB::escape($key)."'");
			if($rst->num_rows > 0) 
			{
				if(self::isUserAdmin($user)) 
				{
					self::$logged = 1; 
					self::$dbid = $user;
				}
			}
		}
		
		if(self::$logged == 0) 
		{
			if(isset($_GET["handle-login"], $_POST["user"], $_POST["pw"]))
			{
				$rst = \Main\DB::select("accounts", "id", "(username='".\Main\DB::escape($_POST["user"])."' OR mail='".\Main\DB::escape($_POST["user"])."') AND passwort=MD5('".\Main\DB::escape($_POST["pw"])."')");
				if($rst->num_rows > 0) 
				{ 
					$row = $rst->fetch_object(); 
					
					$uid = $row->id; 
					if(self::isUserAdmin($uid))
					{
						self::$logged = 1; 
						self::$dbid = $uid; 
						$_SESSION["a_logged"] = 1; 
						$_SESSION["a_dbid"] = $uid; 
						$_SESSION["a_key"] = md5($_POST["pw"]); 
						\Main\DB::insert("log_admin_login", array(
							"user" => $_POST["user"],
							"ipadress" => $user_ip,
							"stamp" => time(),
							"success" => 1
						));
					}
					else
					{
						\Main\DB::insert("log_admin_login", array(
							"user" => $_POST["user"],
							"ipadress" => $user_ip,
							"stamp" => time(),
							"success" => 0
						));
					}
				}
				else
				{
					\Main\DB::insert("log_admin_login", array(
						"user" => $_POST["user"],
						"ipadress" => $user_ip,
						"stamp" => time(),
						"success" => 0
					));
				}
			}
		}
		else
		{
			if(isset($_GET["handle-logout"]))
			{
				$_SESSION["a_logged"] = 0; 
				self::$logged = 0; 
				self::$dbid = 0; 
			}
		}
		
		self::$data = self::getUserData(self::$dbid); 
	}
	
	// Benutzerdaten abfragen 
	public static function getUserData($uId) 
	{
		$array = array(); 
		
		if(!self::existsUserId($uId))
		{
			$array = array(
				"id" => 0,
				"username" => "Gast"
			);
			return $array; 
		}
		
		$rst = \Main\DB::select("accounts", "username", "id='".\Main\DB::escape($uId)."'");
		$row = $rst->fetch_object(); 
		
		$array = array(
			"id" => $uId,
			"username" => $row->username
		);
		
		return $array; 
	}
	
	// existiert ein Account unter dieser ID? 
	public static function existsUserId($uId) 
	{
		$rst = \Main\DB::select("accounts", "id", "id='".\Main\DB::escape($uId)."'");
		if($rst->num_rows > 0) return true; 
		return false; 
	}

	// ob er erlaubt ist die Administration zu betreten
	public static function isUserAdmin($uId) 
	{
		$is = false; 
		
		$rst = \Main\DB::select("gruppen_user", "gruppe", "user='".\Main\DB::escape($uId)."'");
		
		while($row = $rst->fetch_object())
		{
			$gruppe = $row->gruppe; 
			
			$result = \Main\DB::select("gruppen", "enter_administration", "id='".\Main\DB::escape($gruppe)."'");
			if($result->num_rows > 0)
			{
				$roww = $result->fetch_object(); 
				if($roww->enter_administration == 1) 
					$is = true; 
			}
		}
		
		return $is; 
	}
	
	
	
}

?>