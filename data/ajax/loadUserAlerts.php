<?php
session_start(); 

require_once("../../core/php_functions.php"); 
require_once("../../core/DB.php");
require_once("../../config/db.php"); 
require_once("../../core/UserControl.php"); 

DB::init($db["host"], $db["user"], $db["pw"], $db["db"]);
UserControl::init();

$html = ""; 

if(UserControl::$logged) 
{
	$rst = DB::select("alerts", "id, time, typ, theme, post", "user='".DB::escape(UserControl::$dbid)."'", "5", "gesehen, id DESC");
	if($rst->num_rows == 0) 
	{
		$html .= "<div class='alert'>Keine neuen Benachrichtigungen</div>"; 
	}
	else
	{
		$alert = array("id" => $row->id,
				"time" => $row->time,
				"typ" => $row->typ,
				"theme" => $row->theme,
				"post" => $row->post
				);
		$anz_users = 0; 
		$rst = DB::select("alert_users", "user", "alert='".DB::escape($alert["id"])."'");
		$anz_users = $rst->num_rows; 
		$user1 = ""; 
		$user2 = ""; 
		$user3 = ""; 
		$weitere = ""; 

		$counter = 0; 
		while($row = $rst->fetch_object())
		{
			$counter++; 
			
			$user_id = $row->user; 
			$user_Data = UserControl::getUserData($user_id); 
			if($counter == 1) 
			{
				$user1 = htmlspecialchars($user_Data["displayname"]); 
			}
			else if($counter == 2) 
			{
				$user2 = ", " . htmlspecialchars($user_Data["displayname"]); 
			}
			else if($counter == 3) 
			{
				if($anz_users == 3) 
				{
					$user3 = " und " . htmlspecialchars($user_Data["displayname"]);
				}
				else
				{
					$more = $anz_users - 2; 
					$weitere = " und $more weitere"; 
				}
			}


			if($counter == 3) break; 
		}

		$ausgabe = ""; 
		if($alert["typ"] == 1) // 
		{
			//$ausgabe = "$user1$user2$user3$weitere "; 
		}
	}
	
	
}

echo $html; 
?>