<?php

namespace Main\User; 
class Control
{

	public static $logged = 0; 
	public static $dbid = 0; 
	public static $data = array(); 
	public static $design = 0; 
	public static $design_inc = ""; 
	public static $design_footer = ""; 
	
	public static function init() 
	{
		self::checkOnlineUsers(); 
		self::checkOnlineGuests(); 

		if(isset($_SESSION["logged"]) && $_SESSION["logged"] == 1) 
		{
			$uId = $_SESSION["dbid"]; 
			$key = $_SESSION["key"]; 
			
			$rst = \Main\DB::select("accounts", "id", "id='".\Main\DB::escape($uId)."' AND passwort='".\Main\DB::escape($key)."'");
			if($rst->num_rows > 0) 
			{
				self::$logged = 1; 
				self::$dbid = $uId; 
			}
		}
		
		if(self::$logged == 0) 
		{
			// Automatischer Login 
			if(isset($_COOKIE["stay_in_u"]))
			{
				$user = $_COOKIE["stay_in_u"]; 
				$key1 = $_COOKIE["stay_in_key1"]; 
				$key2 = $_COOKIE["stay_in_key2"]; 
				$stamp = $_COOKIE["stay_in_stamp"]; 
				
				$rst = \Main\DB::select("stay_logged_keys", "id", "user='".\Main\DB::escape($user)."' AND key1='".\Main\DB::escape($key1)."' AND key2='".\Main\DB::escape($key2)."' AND stamp='".\Main\DB::escape($stamp)."'");
				if($rst->num_rows > 0) 
				{
					$row = $rst->fetch_object(); 
					$key_id = $row->id; 
					
					$_SESSION["dbid"] = $user; 
					$_SESSION["logged"] = 1; 
					$_SESSION["key"] = $key1; 
					
					self::$logged = 1; 
					self::$dbid = $user; 
					
					\Main\DB::delete("stay_logged_keys", $key_id); 
					
					$key2 = \Main\createRandomKey(); 
					$stamp = time(); 
					
					\Main\DB::insert("stay_logged_keys", array(
						"user" => $user,
						"key1" => $key1,
						"key2" => $key2,
						"stamp" => $stamp
					));
					
					setcookie("stay_in_u", $user, time() + (86400 * 30), "/");
					setcookie("stay_in_key1", $key1, time() + (86400 * 30), "/");
					setcookie("stay_in_key2", $key2, time() + (86400 * 30), "/");
					setcookie("stay_in_stamp", $stamp, time() + (86400 * 30), "/"); // 86400 = 1 day
				}
				else
				{
					unset($_COOKIE["stay_in_u"]); 
					unset($_COOKIE["stay_in_key1"]); 
					unset($_COOKIE["stay_in_key2"]); 
					unset($_COOKIE["stay_in_stamp"]); 
				}
			}
		}
		
		self::$data = self::getUserData(self::$dbid); 

		if(self::$logged == 1) 
			self::setUserOnline(self::$dbid); 
		else
			self::setGuestOnline(); 
		
		
		
		if(isset($_COOKIE["Design_id"]))
		{
			$did = $_COOKIE["Design_id"]; 
			$rst = \Main\DB::select("designs", "id", "id='".\Main\DB::escape($did)."' AND active='1'");
			if($rst->num_rows > 0) 
			{	
				self::$design = $did; 
			}
		}
		else
		{
			$rst = \Main\DB::select("designs", "id", "standard='1' AND active='1'", "1");
			if($rst->num_rows > 0) 
			{
				$row = $rst->fetch_object(); 
				self::$design = $row->id; 
			}
			else
			{
				self::$design = 0; 
			}
		}
		
		if(self::$design != 0) 
		{
			$rst = \Main\DB::select("designs", "fname, footer_txt", "id='".\Main\DB::escape(self::$design)."' AND active='1'");
			if($rst->num_rows > 0) 
			{
				$row = $rst->fetch_object(); 
				$fname = $row->fname; 
				$footer = $row->footer_txt; 
				self::$design_inc = "<link rel='stylesheet' href='data/designs/$fname' />"; 
				if($footer != "") self::$design_footer = "<br  />".$footer; 
			}
		}
	}
	
	public static function checkDataForRegister($input) 
	{
		
		if(strlen($input["username"]) < 5) 
		{
			return 1; 
		}
		
		if($input["pw1"] != $input["pw2"])
		{
			return 2; 
		}
		
		if(strlen($input["pw1"]) < 6)
		{
			return 3; 
		}
		
		$rst = \Main\DB::select("accounts", "id", "username='".\Main\DB::escape($input["username"])."' OR mail='".\Main\DB::escape($input["mail"])."'");
		if($rst->num_rows > 0) 
			return 4; 
		
		if(strpos(ltrim(rtrim($input["username"])), " ") != 0)
			return 5; 
		
		return 0; 
	}
	
	public static function registerAccount($input) 
	{
		$sql = "INSERT INTO accounts (username, vorname, nachname, mail, passwort, registerTime) VALUES 
		('".\Main\DB::escape(ltrim(rtrim($input["username"])))."', '".\Main\DB::escape($input["vorname"])."', '".\Main\DB::escape($input["nachname"])."', '".\Main\DB::escape($input["mail"])."', MD5('".\Main\DB::escape($input["pw1"])."'), '".time()."')";
		
		\Main\DB::query($sql); 
		self::handleLogin($input); 
		return true; 
	}
	
	public static function handleLogin($input, $stay_logged=0) 
	{
		$rst = \Main\DB::select("accounts", "id", "(mail='".\Main\DB::escape($input["mail"])."' OR username='".\Main\DB::escape($input["mail"])."') AND passwort=MD5('".\Main\DB::escape($input["pw1"])."')");
		if($rst->num_rows > 0) 
		{
			$row = $rst->fetch_object(); 
			
			$id = $row->id; 
			$_SESSION["logged"] = 1; 
			$_SESSION["dbid"] = $id; 
			$_SESSION["key"] = md5($input["pw1"]); 
			self::init();

			\Main\DB::update("accounts", $id, array("lastLogin" => time()));
			
			if($stay_logged == 1) 
			{
				$key1 = md5($input["pw1"]); 
				$key2 = \Main\createRandomKey(); 
				$stamp = time(); 
			
				setcookie("stay_in_u", $id, time() + (86400 * 30), "/");
				setcookie("stay_in_key1", $key1, time() + (86400 * 30), "/");
				setcookie("stay_in_key2", $key2, time() + (86400 * 30), "/");
				setcookie("stay_in_stamp", $stamp, time() + (86400 * 30), "/"); // 86400 = 1 day
				
				\Main\DB::insert("stay_logged_keys", array(
					"user" => $id,
					"key1" => $key1,
					"key2" => $key2,
					"stamp" => $stamp
				));
			}
			
			
			return true; 
		}
		
		return false; 
	}
	
	public static function existsUserId($uId) 
	{
		$rst = \Main\DB::select("accounts", "id", "id='".\Main\DB::escape($uId)."'");
		if($rst->num_rows > 0) 
			return true; 
		return false; 
	}
	
	public static function getUserData($uId) 
	{
		if(!self::existsUserId($uId))
		{
			$array = array(
				"dbid" => 0,
				"username" => "Gast",
				"displayname" => "Gast",
				"vorname" => "",
				"nachname" => "",
				"mail" => "",
				"avatar" => "",
				"registerTime" => 0,
				"lastUsernameChange" => 0,
				"posts" => 0,
				"rank" => "",
				"smallrank" => "",
				"signature" => "",
				"display_fullname" => 0,
				"hide_in_memberslist" => 1,
				"website" => "",
				"arights" => array(
								
								"enter_administration" => 0,
								"edit_themes" => 0,
								"move_themes" => 0,
								"close_themes" => 0,
								"tag_themes" => 0,
								"edit_posts" => 0,
								"del_posts" => 0
								
								)
			);
		}
		else
		{
			self::pointAccount($uId); 
			$rst = \Main\DB::select("accounts", "username, vorname, nachname, mail, registerTime, lastUsernameChange, signature, website, display_fullname, hide_in_memberslist, points", "id='".\Main\DB::escape($uId)."'");
			$row = $rst->fetch_object(); 
			$array = array(
				"dbid" => $uId,
				"username" => $row->username,
				"displayname" => $row->username,
				"vorname" => $row->vorname,
				"nachname" => $row->nachname,
				"lastUsernameChange" => $row->lastUsernameChange,
				"registerTime" => $row->registerTime,
				"mail" => $row->mail,
				"avatar" => self::getUserAvatar($uId),
				"posts" => self::countUserPosts($uId),
				"rank" => self::getUserRank($uId),
				"smallrank" => self::getUserRank($uId, true),
				"signature" => $row->signature,
				"display_fullname" => $row->display_fullname,
				"hide_in_memberslist" => $row->hide_in_memberslist,
				"website" => $row->website,
				"points" => $row->points,
				"arights" => array(
								
								"enter_administration" => 0,
								"edit_themes" => 0,
								"move_themes" => 0,
								"close_themes" => 0,
								"tag_themes" => 0,
								"edit_posts" => 0,
								"del_posts" => 0
								
								)
			);
			
			if($array["display_fullname"] == 1) 
			{
				if($array["vorname"] != "" || $array["nachname"] != "") 
					$array["displayname"] = ltrim(rtrim($array["vorname"] . " " . $array["nachname"]));
			}
			
			$rst = \Main\DB::select("gruppen_user", "gruppe", "user='".\Main\DB::escape($uId)."'");
			while($row = $rst->fetch_object())
			{
				$gruppe = $row->gruppe; 
				$result = \Main\DB::select("gruppen", "enter_administration, edit_themes, move_themes, close_themes, tag_themes, edit_posts, del_posts", "id='".\Main\DB::escape($gruppe)."'");
				if($result->num_rows > 0) 
				{
					$roww = $result->fetch_object(); 
					
					if($roww->enter_administration == 1) 
						$array["arights"]["enter_administration"] = 1; 
						
					if($roww->edit_themes == 1) 
						$array["arights"]["edit_themes"] = 1; 
						
					if($roww->move_themes == 1) 
						$array["arights"]["move_themes"] = 1; 
						
					if($roww->close_themes == 1) 
						$array["arights"]["close_themes"] = 1; 
						
					if($roww->tag_themes == 1) 
						$array["arights"]["tag_themes"] = 1; 
						
					if($roww->edit_posts == 1) 
						$array["arights"]["edit_posts"] = 1; 
						
					if($roww->del_posts == 1) 
						$array["arights"]["del_posts"] = 1; 
				}
			}
			
			
			
		}
		return $array; 
	}
	
	public static function getUserRank($uId, $small=false) 
	{
		$html = ""; 

		$rank_id = 0; 

		$rst = \Main\DB::select("user_ranks", "id", null, null, "priority, id");
		if($rst->num_rows > 0) 
		{
			while($row = $rst->fetch_object())
			{
				$rid = $row->id; 
				if(self::hasUserRank($uId, $rid)) 
					$rank_id = $rid; 
			}
		}	

		if($rank_id != 0) 
		{
			$rst = \Main\DB::select("user_ranks", "name, show_picture, picture, bgcol, textcol", "id='".\Main\DB::escape($rank_id)."'");
			$row = $rst->fetch_object(); 
			$rank = array("name" => $row->name,
				"show_picture" => $row->show_picture,
				"picture" => $row->picture,
				"bgcol" => $row->bgcol,
				"textcol" => $row->textcol
				);
				// Normalen Rang anzeigen
			if($rank["show_picture"] == 0) 
			{
				$style_bg = ""; 
				$style_col = ""; 
				if($rank["bgcol"] != "" && $rank["bgcol"] != null) 
					$style_bg = "background:#" . $rank["bgcol"] . ";"; 
				if($rank["textcol"] != "" && $rank["textcol"] != null) 
					$style_col = "color:#" . $rank["textcol"] . ";";

				$html .= "<span class='badge user_rank' style='$style_bg $style_col'>".htmlspecialchars($rank["name"])."</span>"; 
			}
				// Bild Rang anzeigen
			else
			{
				if(!$small)
					$html .= "<img src='".$rank["picture"]."' style='max-width:90%;margin-left:auto;margin-right:auto;' />";
				else
					 $html .= "<img src='".$rank["picture"]."' style='max-width:180px;max-height:40px;' />";
			}
		}

		return $html; 
	}

	public static function hasUserRank($uId, $rank) 
	{
		$user_posts = self::countUserPosts($uId); 

		$rst = \Main\DB::select("user_ranks_gains", "gain_posts, gain_group", "rank='".\Main\DB::escape($rank)."'");
		if($rst->num_rows > 0) 
		{
			while($row = $rst->fetch_object())
			{
				$gain_posts = $row->gain_posts; 
				$gain_group = $row->gain_group; 
				if($gain_posts != 0 && $gain_posts < $user_posts) return true; 
				if($gain_group != 0) 
				{
					if(self::IsUserInGruppe($uId, $gain_group)) return true; 
				}
				if($gain_posts == 0 && $gain_group == 0) return true; 
			}
		}

		return false; 
	}
	
	public static function countUserPosts($uId) 
	{
		$rst = \Main\DB::select("posts", "id", "user='".\Main\DB::escape($uId)."' AND deleted='0'");
		return $rst->num_rows; 
	}
	
	public static function getUserAvatar($uId) 
	{
		$html = ""; 
		$rst = \Main\DB::select("bilderUpload", "link", "user='".\Main\DB::escape($uId)."' AND avatar='1'");
		if($rst->num_rows > 0) 
		{
			$row = $rst->fetch_object(); 
			$link = $row->link; 
			$html .= $link; 
		}
		return $html; 
	}
	
	public static function IsUserInGruppe($uId, $gId) 
	{
		$rst = \Main\DB::select("gruppen_user", "id", "user='".\Main\DB::escape($uId)."' AND gruppe='".\Main\DB::escape($gId)."'");
		if($rst->num_rows > 0) return true; 
		return false; 
	}
	
	public static function IsUserAllowedToSeeForum($uId, $fId) 
	{
		$rst = \Main\DB::select("gruppen_foren", "gruppe, permission_see, permission_write", "forum='".\Main\DB::escape($fId)."'");
		if($rst->num_rows <= 0) return true; 
		
		$allowed = false; 
		while($row = $rst->fetch_object())
		{
			$gf = array(
				"gruppe" => $row->gruppe,
				"permission_see" => $row->permission_see,
				"permission_write" => $row->permission_write
			);
			
			if($gf["permission_see"] == 1) {
				if(self::IsUserInGruppe($uId, $gf["gruppe"])) {
					$allowed = true; 
				}
			}
		}
		
		return $allowed; 
	}
	
	public static function IsUserAllowedToSeeKategory($uId, $kId) 
	{
		$allowed = false; 
		$rst = \Main\DB::select("gruppen_kats", "gruppe, permission_see, permission_write", "kategory='".\Main\DB::escape($kId)."'");
		if($rst->num_rows <= 0) $allowed = true; 
		
		while($row = $rst->fetch_object())
		{
			$gk = array(
				"gruppe" => $row->gruppe,
				"permission_see" => $row->permission_see,
				"permission_write" => $row->permission_write
			);
			
			if($gk["permission_see"] == 1) {
				if(self::IsUserInGruppe($uId, $gk["gruppe"])) {
					$allowed = true; 
				}
			}
		}
		
		if($allowed == true) 
		{
			$rst = \Main\DB::select("kategorien", "forum, kategorie", "id='".\Main\DB::escape($kId)."'");
			$row = $rst->fetch_object(); 
			$kf = $row->forum; 
			$kk = $row->kategorie; 
			
			if($kf != 0) 
			{
				$allowed = self::IsUserAllowedToSeeForum($uId, $kf); 
			}
			else if($kk != 0) 
			{
				$allowed = self::IsUserAllowedToSeeKategory($uId, $kk); 
			}
		}
		
		return $allowed; 
	}
	
	public static function IsUserAllowedToWriteForum($uId, $fId) 
	{
		if($uId == 0) return false; 
		$rst = \Main\DB::select("gruppen_foren", "gruppe, permission_see, permission_write", "forum='".\Main\DB::escape($fId)."'");
		if($rst->num_rows <= 0) return true; 
		
		$allowed = false; 
		while($row = $rst->fetch_object())
		{
			$gf = array(
				"gruppe" => $row->gruppe,
				"permission_see" => $row->permission_see,
				"permission_write" => $row->permission_write
			);
			
			if($gf["permission_write"] == 1) {
				if(self::IsUserInGruppe($uId, $gf["gruppe"])) {
					$allowed = true; 
				}
			}
		}
		
		return $allowed; 
	}
	
	public static function IsUserAllowedToWriteKategory($uId, $kId) 
	{
		if($uId == 0) return false; 
		$allowed = false; 
		$rst = \Main\DB::select("gruppen_kats", "gruppe, permission_see, permission_write", "kategory='".\Main\DB::escape($kId)."'");
		if($rst->num_rows <= 0) $allowed = true; 
		
		while($row = $rst->fetch_object())
		{
			$gk = array(
				"gruppe" => $row->gruppe,
				"permission_see" => $row->permission_see,
				"permission_write" => $row->permission_write
			);
			
			if($gk["permission_write"] == 1) {
				if(self::IsUserInGruppe($uId, $gk["gruppe"])) {
					$allowed = true; 
				}
			}
		}
		
		if($allowed == true) 
		{
			$rst = \Main\DB::select("kategorien", "forum, kategorie", "id='".\Main\DB::escape($kId)."'");
			$row = $rst->fetch_object(); 
			$kf = $row->forum; 
			$kk = $row->kategorie; 
			
			if($kf != 0) 
			{
				$allowed = self::IsUserAllowedToWriteForum($uId, $kf); 
			}
			else if($kk != 0) 
			{
				$allowed = self::IsUserAllowedToWriteKategory($uId, $kk); 
			}
		}
		
		return $allowed; 
	}

	public static function gainAutogainGroups($uId) 
	{
		if($uId == 0) 
		{
			return false; 
		}
		
		$rst = \Main\DB::select("gruppen", "id", "autogain='1'");
		while($row = $rst->fetch_object())
		{
			$gid = $row->id; 
			$result = \Main\DB::select("gruppen_user", "id", "user='".\Main\DB::escape($uId)."' AND gruppe='".\Main\DB::escape($gid)."'");
			if($result->num_rows == 0) 
			{
				\Main\DB::insert("gruppen_user", array("user" => $uId, "gruppe" => $gid));
			}
		}
	}

	public static function IsUserInKonversation($uId, $cId) 
	{
		$rst = \Main\DB::select("conversation_users", "id", "conversation='".\Main\DB::escape($cId)."' AND user='".\Main\DB::escape($uId)."'");
		if($rst->num_rows > 0) return true; 
		return false; 
	}

	public static function countKonversationUnseenMsgs($uId, $cId) 
	{
		$unseen = 0; 
		$rst = \Main\DB::select("conversation_msg", "id", "user!='".\Main\DB::escape($uId)."' AND conversation='".\Main\DB::escape($cId)."'");
		while($row = $rst->fetch_object())
		{
			$msgid = $row->id; 
			$result = \Main\DB::select("conversation_msg_seen", "id", "user='".\Main\DB::escape($uId)."' AND msg='".\Main\DB::escape($msgid)."'");
			if($result->num_rows == 0) 
				$unseen++; 
		}
		return $unseen; 
	}

	public static function setKonversationMsgToSeen($uId, $mId) 
	{
		$rst = \Main\DB::select("conversation_msg_seen", "id", "user='".\Main\DB::escape($uId)."' AND msg='".\Main\DB::escape($mId)."'");
		if($rst->num_rows == 0) 
		{
			\Main\DB::insert("conversation_msg_seen", array("user" => $uId, "msg" => $mId));
		}
	}

	public static function countNewKonversations($uId) 
	{
		$count = 0; 
		$sql = "SELECT conversations.id FROM conversations INNER JOIN conversation_users ON conversation_users.conversation=conversations.id AND conversation_users.user='".\Main\DB::escape($uId)."'";
		$rst = \Main\DB::query($sql); 
		while($row = $rst->fetch_object())
		{
			$cid = $row->id; 
			$count += self::countKonversationUnseenMsgs($uId, $cid); 
		}
		return $count; 
	}

	public static function existsUserWithFullname($fullname, $user_ausnahme=0) 
	{
		$rst = \Main\DB::select("accounts", "id");
		if($rst->num_rows > 0) 
		{
			while($row = $rst->fetch_object())
			{
				$acc = $row->id; 
				if($user_ausnahme == $acc) continue; 
				$data = self::getUserData($acc); 
				if($data["displayname"] == $fullname) 
					return true; 
			}
		}
		return false; 
	}

	/**
	* Punkte verteilen
	*
	* Diese Funktion überprüft die Punkte von jedem Benutzer und trägt Sie in die Benutzer-Tabelle ein. 
	* Wird verwendet um die Mitglieder-Liste nach Punkten (Beiträgen) zu sortieren. 
	*
	* HOOKED 
	* 
	* @author s-l 
	* @version 0.0.1 
	*/
	public static function pointAccounts() 
	{
		$rst = \Main\DB::select("accounts", "id");
		while($row = $rst->fetch_object())
		{
			$accId = $row->id; 
			$posts = self::countUserPosts($accId); 
			$avatar = self::getUserAvatar($accId); 
			if(trim($avatar) == "") $avatar = 0;
			else $avatar = 1; 

			$points = 0; 
			$points += $posts;

			if($avatar == 1) $points += 10; 

			$array = array("account" => $accId,
				"data" => array(
					"posts" => $posts,
					"avatar" => $avatar,
					"points" => $points
				)
			);
			$json = json_encode($array); 
			\Main\Plugins::hook("UserControl.pointAccounts.array.json", $json); 
			$array = json_decode($json, true); 
			$points = $array["data"]["points"]; 

			\Main\DB::update("accounts", $accId, array("points" => $points)); 
		}
	}

	public static function pointAccount($accId) 
	{
		$posts = self::countUserPosts($accId); 
		$avatar = self::getUserAvatar($accId); 
		if(trim($avatar) == "") $avatar = 0;
		else $avatar = 1; 

		$points = 0; 
		$points += $posts;

		if($avatar == 1) $points += 10; 

		$array = array("account" => $accId,
				"data" => array(
					"posts" => $posts,
					"avatar" => $avatar,
					"points" => $points
				)
		);
		$json = json_encode($array); 
		\Main\Plugins::hook("UserControl.pointAccounts.array.json", $json); 
		$array = json_decode($json, true); 
		$points = $array["data"]["points"]; 

		\Main\DB::update("accounts", $accId, array("points" => $points)); 
	}

	
	public static function checkOnlineGuests() 
	{
		$rst = \Main\DB::select("guests_online", "id, lastCheck");
		while($row = $rst->fetch_object())
		{
			$id = $row->id; 
			$time = $row->lastCheck; 
			if(time() - $time >= 60 * 4) // 4 Minuten 
			{
				\Main\DB::delete("guests_online", $id); 
			}
		}
	}

	/**
	* Online Gästesitzung
	*
	* Überprüft ob der Typ bereits einen Guest-Key gespeichert hat. Falls nicht wird einer erstellt. Ein neuer Key wird
	* in die Datenbank eingetragen und ein bestehender upgedated. 
	*
	* @author s-l 
	* @version 0.0.1 
	*/
	public static function setGuestOnline() 
	{
		function generateGuestKey() 
		{
			$key = ""; 

			$bstb = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");

			while(strlen($key) < 35) 
			{
				$len = mt_rand(1, 7); 
				for($i = 0; $i < $len; $i++) 
				{
					$typ = mt_rand(1,2); 
					if($typ == 1) // bstb 
					{
						$big = mt_rand(1, 2); 
						$bst = mt_rand(0, count($bstb)-1); 
						if($big == 1) $key .= strtoupper($bstb[$bst]); 
						else $key .= $bstb[$bst]; 
					}
					else
					{
						$num = mt_rand(0, 99); 
						$key .= $num; 
					}
				}
			}

			return $key; 
		}

		if(self::$logged) return; 

		if(isset($_SESSION["guestKey"]))
			$key = $_SESSION["guestKey"]; 
		else $key = generateGuestKey(); 
		$_SESSION["guestKey"] = $key; 

		if(!isset($_SESSION["guestKey"])) return; # nicht weiter wenn die Session (offensichtlich) nicht gespeichert wird 

		$user_ip = $_SERVER["REMOTE_ADDR"];

		//$rst = \Main\DB::select("guests_online", "id", "guestKey='".\Main\DB::escape($key)."'");
		$rst = \Main\DB::select("guests_online", "id, guestKey", "adress='".\Main\DB::escape($user_ip)."'");
		if($rst->num_rows > 0) 
		{
			$row = $rst->fetch_object(); 
			$id = $row->id; 
			$guestKey = $row->guestKey; 
			\Main\DB::update("guests_online", $id, array("lastCheck" => time(), "guestKey" => $key));
		}
		else
		{
			\Main\DB::insert("guests_online", array("guestKey" => $key, "lastCheck" => time(), "adress" => $user_ip));
		}
	}

	public static function countGuestsOnline() 
	{
		$rst = \Main\DB::select("guests_online", "id");
		return $rst->num_rows; 
	}

	/**
	* Online Benutzer Überprüfung
	*
	* Checkt alle aktuell als online eingetragenen Benutzer. Wenn seit 5 Minuten keine Rückmeldung von einem Benutzer gekommen ist 
	* wird dieser automatisch als Offline eingestuft. 
	*
	* @author s-l 
	* @version 0.0.1 
	*/
	public static function checkOnlineUsers() 
	{
		$rst = \Main\DB::select("accounts_online", "id, lastCheck");
		while($row = $rst->fetch_object())
		{
			$id = $row->id; 
			$lastCheck = $row->lastCheck; 
			if(time() - $lastCheck >= 60) // 1 Minute untätig
			{
				\Main\DB::delete("accounts_online", $id); 
			}
		}
	}

	/**
	* Online Status setzen
	*
	* Setzt den Online-Status des Benutzers. Falls bereits ein Online-Status existiert aktualisiert er die Zeit des letzten Checks. 
	* 
	* @author s-l 
	* @version 0.0.1 
	*/
	public static function setUserOnline($uId) 
	{
		$rst = \Main\DB::select("accounts_online", "id", "user='".\Main\DB::escape($uId)."'");
		if($rst->num_rows > 0) 
		{
			$row = $rst->fetch_object(); 
			$oid = $row->id; 
			\Main\DB::update("accounts_online", $oid, array("lastCheck" => time()));
		}
		else
		{
			\Main\DB::insert("accounts_online", array("user" => $uId, "lastCheck" => time()));
		}
	}
	
	/**
	* Online Status prüfen
	*
	* Überprüft den Online-Status der angegebenen Benutzer ID. Gibt wieder ob er Online/Offline ist. 
	*
	* @author s-l 
	* @version 0.0.1 
	* @return bool 
	*/
	public static function isUserOnline($uId) 
	{
		$rst = \Main\DB::select("accounts_online", "id", "user='".\Main\DB::escape($uId)."'");
		if($rst->num_rows > 0) return true; 
		return false; 
	}

	/**
	* Online Benutzer auflisten
	*
	* Listet alle Online Benutzer in einem Array auf.  
	* 
	* @author s-l 
	* @version 0.0.2 
	* @return array 
	*/
	public static function listOnlineUsers() 
	{
		$array = array(); 

		$rst = \Main\DB::select("accounts_online", "user");
		while($row = $rst->fetch_object())
		{
			$accId = $row->user; 
			$array[$accId] = self::getUserData($accId); 
		}
		
		return $array; 
	}

	public static function countAccounts() 
	{
		$rst = \Main\DB::select("accounts", "id");
		return $rst->num_rows; 
	}

	public static function getNewestAccount() 
	{
		$rst = \Main\DB::select("accounts", "id", null, null, "id DESC");
		$row = $rst->fetch_object(); 
		return self::getUserData($row->id); 
	}

	public static function checkIfNewUserRecord($members, $guests) 
	{
		$count = $members + $guests; 
		$rst = \Main\DB::select("online_records", "id", "onlines>='".\Main\DB::escape($count)."'"); 
		if($rst->num_rows == 0) 
		{
			\Main\DB::insert("online_records", array("onlines" => $count, "time" => time()));
		}
	}

	public static function getUserRecord() 
	{
		$html = ""; 

		$rst = \Main\DB::select("online_records", "time, onlines", null, "1", "id DESC");
		if($rst->num_rows > 0) 
		{
			$row = $rst->fetch_object(); 
			$time = $row->time; 
			$onlines = $row->onlines; 

			$rec = \Main\Language::$txt["statistic"]["record"]; 
			$rec = str_replace("[x]", $onlines, $rec); 
			$rec = str_replace("[date]", \Main\toTimeDatNoTime($time), $rec); 
			$html .= $rec; 
		}

		return $html; 
	}
}

?>