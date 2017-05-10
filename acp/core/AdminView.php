<?php

namespace Main\Admin; 
class View
{
	
	public static function showMainMenue() 
	{
		$html = ""; 
		
		$html .= "<ul class='nav nav-pills pageMenueItems'>"; 
			
			if(\Main\User\Control::$logged == 1) 
			{
				$system_active = ""; 
				$users_active = ""; 
				$plugins_active = ""; 
				$designs_active = ""; 
				$content_active = ""; 
				
				if(isset($_GET["page"]))
				{
					if($_GET["page"] == "system") $system_active = "active"; 
					else if($_GET["page"] == "users") $users_active = "active"; 
					else if($_GET["page"] == "content") $content_active = "active"; 
					else if($_GET["page"] == "plugins") $plugins_active = "active"; 
					else if($_GET["page"] == "designs") $designs_active = "active"; 
				}
				else
					$system_active = "active"; 
				
				$html .= \Main\Plugins::hook("AdminView.showMainMenue.Logged.beforeMain", "");
				$html .= "<li class='$system_active'><a href='index.php?page=system'>".\Main\Language::$txt["buttons"]["menue_system"]."</a></li>";
				$html .= "<li class='$users_active'><a href='index.php?page=users'>".\Main\Language::$txt["buttons"]["menue_users"]."</a></li>";
				$html .= "<li class='$content_active'><a href='index.php?page=content'>".\Main\Language::$txt["buttons"]["menue_content"]."</a></li>";
				$html .= "<li class='$plugins_active'><a href='index.php?page=plugins'>".\Main\Language::$txt["buttons"]["menue_plugins"]."</a></li>";
				$html .= "<li class='$designs_active'><a href='index.php?page=designs'>".\Main\Language::$txt["buttons"]["menue_designs"]."</a></li>";
				$html .= \Main\Plugins::hook("AdminView.showMainMenue.Logged.afterMain", "");
			}
			else
			{
				$html .= "<li class='active'><a href='index.php'>".\Main\Language::$txt["buttons"]["menue_login"]."</a></li>"; 
			}
		
		$html .= "</ul>"; 
		
		return $html; 
	}
	
	public static function DisplayLogin()
	{
		$html = ""; 
		
		if(isset($_GET["handle-login"], $_POST["user"]))
		{
			$html .= "<div class='alert alert-danger'>
				".\Main\Language::$txt["alerts"]["login_wrong"]."
			</div>";
		}
		
		$html .= "<div class='panel panel-primary panel-login'>";
			
			$html .= "<div class='panel-heading'>".\Main\Language::$txt["infos"]["login_panel_header"]."</div>";
			
			$html .= "<div class='panel-body'>"; 
				$html .= "<small>".\Main\Language::$txt["infos"]["login_panel_info"]."</small>"; 
				$html .= "<form class='form-horizontal form-margin-top' action='index.php?handle-login' method='post'>
					<input class='form-control' type='text' name='user' placeholder='".\Main\Language::$txt["forms"]["login_user"]."' /><br />
					<input class='form-control' type='password' name='pw' placeholder='".\Main\Language::$txt["forms"]["login_pw"]."' /><br />
					<div class='textright'><button class='btn btn-primary'>".\Main\Language::$txt["buttons"]["login"]."</button></div>
				</form>";
			$html .= "</div>"; 		
					
		$html .= "</div>"; 
					
		return $html; 
	}
	
	public static function DisplayMainLeft() // Hauptverteiler für linken Part
	{
		$html = ""; 
		
		$d_system = 0; 
		$d_users = 0; 
		$d_content = 0; 
		$d_plugins = 0; 
		$d_designs = 0; 
		
		if(isset($_GET["page"])) 
		{
			if($_GET["page"] == "system") $d_system = 1; 
			else if($_GET["page"] == "users") $d_users = 1; 
			else if($_GET["page"] == "content") $d_content = 1; 
			else if($_GET["page"] == "plugins") $d_plugins = 1; 
			else if($_GET["page"] == "designs") $d_designs = 1; 
		}
		else
			$d_system = 1; 
			
		if($d_system) 
		{
			$html .= self::showSystemLeft(); 
		}
		else if($d_users) 
		{
			$html .= self::showUsersLeft(); 
		}
		else if($d_content) 
		{
			$html .= self::showContentLeft();
		}
		else if($d_plugins) 
		{
			$html .= self::showPluginsLeft(); 
		}
		else if($d_designs) 
		{
			$html .= self::showDesignsLeft(); 
		}	
		else if(\Main\Plugins::hook("AdminView.DisplayMainLeft.isPage.".$_GET["page"], "0") == "1")
		{
			$html .= \Main\Plugins::hook("AdminView.DisplayMainLeft.isPage.".$_GET["page"].".content", "");
		}
		
		return $html; 
	}
	
	public static function DisplayMain() // Hauptverteiler für rechten Part
	{
		$html = ""; 
		
		$d_system = 0; 
		$d_users = 0; 
		$d_plugins = 0; 
		$d_designs = 0; 
		$d_content = 0; 
		
		if(isset($_GET["page"])) 
		{
			if($_GET["page"] == "system") $d_system = 1; 
			else if($_GET["page"] == "users") $d_users = 1; 
			else if($_GET["page"] == "content") $d_content = 1; 
			else if($_GET["page"] == "plugins") $d_plugins = 1; 
			else if($_GET["page"] == "designs") $d_designs = 1; 
		}
		else
			$d_system = 1; 
			
		if(isset($_GET["handle-login"], $_POST["user"]))
		{
			$html .= "<div class='alert alert-success alert-margin-bottom'>
				".\Main\Language::$txt["alerts"]["login_success"]."
			</div>";
		}
		
		if($d_system) 
		{
			$html .= self::showSystem(); 
		}
		else if($d_users) 
		{
			$html .= self::showUsers(); 
		}
		else if($d_content) 
		{
			$html .= self::showContent(); 
		}
		else if($d_plugins) 
		{
			$html .= self::showPlugins(); 
		}
		else if($d_designs) 
		{
			$html .= self::showDesigns(); 
		}
		else if(\Main\Plugins::hook("AdminView.DisplayMain.isPage.".$_GET["page"], "0") == "1")
		{
			$html .= \Main\Plugins::hook("AdminView.DisplayMain.isPage.".$_GET["page"].".content", ""); 
		}
		
		return $html; 
	}
	
	private static function showSystem() 
	{
		$html = ""; 
		
		$up_general = 0; 
		$up_protocol = 0; 
		$up_impressum = 0; 
		$up_termsofuse = 0; 
		$up_disclaimer = 0; 
		
		if(isset($_GET["up"]))
		{
			if($_GET["up"] == "general") $up_general = 1; 
			else if($_GET["up"] == "protocol") $up_protocol = 1; 
			else if($_GET["up"] == "impressum") $up_impressum = 1; 
			else if($_GET["up"] == "termsofuse") $up_termsofuse = 1; 
			else if($_GET["up"] == "disclaimer") $up_disclaimer = 1; 
		}
		else
			$up_general = 1; 
			
		if($up_general) // Allgemeines Speichern
		{
			$html .= \Main\Admin\Settings::saveSystemGeneral(); 
		}	
		else if($up_impressum) // Impressum Speichern
		{
			$html .= \Main\Admin\Settings::saveSystemImpressum(); 
		}
		else if($up_termsofuse) // Nutzungsbestimmungen Speichern
		{
			$html .= \Main\Admin\Settings::saveSystemTermsOfUse(); 
		}
		else if($up_disclaimer) // Disclaimer Speichern
		{
			$html .= \Main\Admin\Settings::saveSystemDisclaimer(); 
		}
		
		$html .= "<div class='panel panel-primary'><div class='panel-body'>";
		
			if($up_general) // Allgemein 
			{
				$settings = \Main\Admin\Settings::loadGeneral(); 
				$html .= "<h3>".\Main\Language::$txt["infos"]["system_general_header"]."</h3>";
				
				$html .= "<form action='index.php?page=system&up=general&save' method='post' class='form-horizontal form-margin-top'>";
				
				$html .= "<div class='row'>"; 
				
					// PAGE TITLE
					$html .= "<div class='col-md-3 textalignright settings_desc'>
						<label for='page_title'>".\Main\Language::$txt["forms"]["system_general_page_title"]."</label>
					</div>";
					$html .= "<div class='col-md-9'>"; 
						$html .= "<input type='text' id='page_title' name='page_title' value='".htmlspecialchars($settings["page_title"], ENT_QUOTES)."' class='form-control' />";
					$html .= "</div>";

					$html .= "<div class='settings_placeholder'></div>";
					
					// LANGUAGE
					$html .= "<div class='col-md-3 textalignright settings_desc'>
						<label for='board_title'>".\Main\Language::$txt["forms"]["system_general_language"]."</label>
					</div>";
					$html .= "<div class='col-md-9'>"; 
					
						$html .= "<select class='form-control' name='defaultLang'>"; 
							
							$rst = \Main\DB::select("settings", "defaultLang", "id='1'");
							$row = $rst->fetch_object(); 
							$defaultLang = $row->defaultLang; 
							
							$file = "../data/langs/lang.".$defaultLang.".php";
							require_once($file); 
							
							// Anzeige der aktuellen Lang als erstes
							$html .= "<option value='".htmlspecialchars($defaultLang, ENT_QUOTES)."'>".htmlspecialchars($language["lang_longname"], ENT_QUOTES)."</option>"; 
							
							$allelangs = scandir("../data/langs/");
							foreach($allelangs as $datei) 
							{
								// Keine NICHT Dateien und keine Ordner auslesen
								if($datei == "" || $datei == "." || $datei == ".." || $datei == "...")
									continue; 
								if(is_dir($datei)) continue; 
									
								$short_name = substr($datei, 5);
								$php_pos = strpos($short_name, ".php");
								$short_name = substr($short_name, 0, $php_pos);
								$file = "../data/langs/".$datei;

								if($short_name != $defaultLang) 
								{
									require_once($file); 
									$html .= "<option value='".htmlspecialchars($short_name, ENT_QUOTES)."'>".htmlspecialchars($language["lang_longname"], ENT_QUOTES)."</option>"; 
								}
							}
						$html .= "</select>";
					
					$html .= "</div>"; 					
					
					$html .= "<div class='settings_placeholder'></div>";
					
					// FORUM TITLE
					$html .= "<div class='col-md-3 textalignright settings_desc'>
						<label for='board_title'>".\Main\Language::$txt["forms"]["system_general_board_title"]."</label>
					</div>";
					$html .= "<div class='col-md-9'>"; 
						$html .= "<input type='text' id='board_title' name='board_title' value='".htmlspecialchars($settings["forum_title"], ENT_QUOTES)."' class='form-control' />";
					$html .= "</div>"; 
					
					$html .= "<div class='settings_placeholder'></div>";
					
					// FORUM DESCRIPTION
					$html .= "<div class='col-md-3 textalignright settings_desc'>
						<label for='board_desc'>".\Main\Language::$txt["forms"]["system_general_board_description"]."</label>
					</div>";
					$html .= "<div class='col-md-9'>"; 
						$html .= "<textarea class='form-control' id='board_desc' name='board_desc'>".htmlspecialchars($settings["forum_description"], ENT_QUOTES)."</textarea>";
					$html .= "</div>"; 
					
					$html .= "<div class='settings_placeholder'></div>";
					
					// BUTTONS
					$html .= "<div class='col-md-3 textalignright settings_desc'>
					</div>";
					$html .= "<div class='col-md-9'>"; 
						$html .= "<button name='btnaction' value='save' class='btn btn-primary'>".\Main\Language::$txt["buttons"]["save"]."</button> &nbsp;";
						$html .= "<button name='btnaction' value='reset' class='btn btn-default'>".\Main\Language::$txt["buttons"]["reset"]."</button>";
					$html .= "</div>"; 
					
				$html .= "</div>"; 
				
				$html .= "</form>"; 
				
			 }
			else if($up_protocol) 
			{
				$p = ""; 
				if(isset($_GET["p"]))
				{ $p = $_GET["p"]; }
				
				
				if($p == "") 
				{
					$html .= "<h3>".\Main\Language::$txt["infos"]["system_protocol_header"]."</h3><br />
					<a href='index.php?page=system&up=protocol&p=admin-login'>".\Main\Language::$txt["infos"]["system_protocol_admin_login_log"]."</a>"; 
					
					$html .= "<br  /><br />"; 
				}
				else if($p == "admin-login") // Admin Login LOG 
				{
					$html .= "<h3>".\Main\Language::$txt["infos"]["system_protocol_admin_login_header"]."</h3>";
					
					if(isset($_GET["clearLog"])) // Log löschen
					{
						$sql = "UPDATE log_admin_login SET deleted='1'";
						\Main\DB::query($sql); 
					}	
					else if(isset($_GET["gBack"])) // Log wiederherstellen
					{
						$sql = "UPDATE log_admin_login SET deleted='0'";
						\Main\DB::query($sql); 
					}
					
					$pageNr = 1; 
					if(isset($_GET["pageNo"])) $pageNr = $_GET["pageNo"]; 
					
					$max_per_page = 20; 
					
					$rst = \Main\DB::select("log_admin_login", "id", "deleted='0'");
					$alle_eintraege = $rst->num_rows; 
					
					$max_pages = ceil($alle_eintraege / $max_per_page); 
					
					if($pageNr > $max_pages) $pageNr = $max_pages; 
					if($pageNr < 1) $pageNr = 1; 
					$startlimit = ($pageNr * $max_per_page) - $max_per_page; 
					if($startlimit < 0) $startlimit = 0; 
					$limit = $startlimit . ", " . $max_per_page; 
					
					$html .= "<a href='index.php?page=system&up=protocol&p=admin-login&clearLog'>".\Main\Language::$txt["infos"]["clear_log"]."</a> &nbsp; 
							<a href='index.php?page=system&up=protocol&p=admin-login&gBack'>".\Main\Language::$txt["infos"]["get_log_back"]."</a><br /><br />"; 
					
					if($max_pages > 1) 
					{
						$html .= "<ul class='pagination'>";
						for($i = 1; $i <= $max_pages; $i++) 
						{
							$html .= "<li";
							if($i == $pageNr) 
								$html .= " class='active'";
							$html .= "><a href='index.php?page=system&up=protocol&p=admin-login&pageNo=$i'>$i</a></li>"; 
						}	
						$html .= "</ul>"; 
					}
					
					$html .= "<div class='panel panel-default'>";
					$rst = \Main\DB::select("log_admin_login", "user, ipadress, stamp, success", "deleted='0'", $limit, "id DESC");
					if($rst->num_rows <= 0) 
					{
						$html .= "<div class='panel-entry'>".\Main\Language::$txt["infos"]["log_no_entries"]."</div>";
					}
						
					while($row = $rst->fetch_object())
					{
						$log_user = $row->user; 
						$log_ip = $row->ipadress; 
						$log_stamp = \Main\toTimeDat($row->stamp); 
						$log_success = $row->success; 
						
						if($log_success) $log_success = "<font class='log_green'>".\Main\Language::$txt["infos"]["system_protocol_admin_login_success_1"]."</font>"; 
						else $log_success = "<font class='log_red'>".\Main\Language::$txt["infos"]["system_protocol_admin_login_success_0"]."</font>"; 
						
						$html .= "<div class='panel-entry'>";
						$html .= "<div class='row'>";
							$html .= "<div class='col-md-3'>".htmlspecialchars($log_user)."</div>";
							$html .= "<div class='col-md-3'>$log_ip</div>";
							$html .= "<div class='col-md-3'>$log_stamp</div>";
							$html .= "<div class='col-md-3'>$log_success</div>";
						$html .= "</div>";
						$html .= "</div>"; 
					}
					$html .= "</div>"; 
					
					if($max_pages > 1) 
					{
						$html .= "<ul class='pagination'>";
						for($i = 1; $i <= $max_pages; $i++) 
						{
							$html .= "<li";
							if($i == $pageNr) 
								$html .= " class='active'";
							$html .= "><a href='index.php?page=system&up=protocol&p=admin-login&pageNo=$i'>$i</a></li>"; 
						}	
						$html .= "</ul>"; 
					}
				}
			 }
			else if($up_impressum) 
			{
				$settings = \Main\Admin\Settings::loadSettingsImpressum(); 
				$html .= "<h3>".\Main\Language::$txt["infos"]["system_impressum_header"]."</h3><br />"; 
				
				$de_checked = ""; 
				$ac_checked = ""; 
				if($settings["impressum"] == 0) $de_checked = "checked"; 
				else $ac_checked = "checked"; 
				
				$html .= "<form action='index.php?page=system&up=impressum&save' method='post'>
					<p>
					<input type='radio' name='active' value='1' $ac_checked /> ".\Main\Language::$txt["infos"]["activated"]." &nbsp; 
					<input type='radio' name='active' value='0' $de_checked /> ".\Main\Language::$txt["infos"]["unactivated"]."</p><p>
					
					<textarea id='cke' name='cke'>".htmlspecialchars($settings["impressum_txt"])."</textarea>
					<script>CKEDITOR.replace('cke');</script>
					</p><p>
					<button class='btn btn-primary right'>".\Main\Language::$txt["buttons"]["save"]."</button>
					</p>
				</form>";
			}
			else if($up_termsofuse) 
			{
				$settings = \Main\Admin\Settings::loadSettingsTermsOfUse(); 
				$html .= "<h3>".\Main\Language::$txt["infos"]["system_termsofuse_header"]."</h3><br />"; 
				
				$de_checked = ""; 
				$ac_checked = ""; 
				if($settings["terms"] == 0) $de_checked = "checked"; 
				else $ac_checked = "checked"; 
				
				$html .= "<form action='index.php?page=system&up=termsofuse&save' method='post'>
					<p>
					<input type='radio' name='active' value='1' $ac_checked /> ".\Main\Language::$txt["infos"]["activated"]." &nbsp; 
					<input type='radio' name='active' value='0' $de_checked /> ".\Main\Language::$txt["infos"]["unactivated"]."</p><p>
					
					<textarea id='cke' name='cke'>".htmlspecialchars($settings["terms_txt"])."</textarea>
					<script>CKEDITOR.replace('cke');</script>
					</p><p>
					<button class='btn btn-primary right'>".\Main\Language::$txt["buttons"]["save"]."</button>
					</p>
				</form>";
			}
			else if($up_disclaimer) 
			{
				$settings = \Main\Admin\Settings::loadSettingsDisclaimer(); 
				$html .= "<h3>".\Main\Language::$txt["infos"]["system_disclaimer_header"]."</h3><br />"; 
				
				$de_checked = ""; 
				$ac_checked = ""; 
				if($settings["disclaimer"] == 0) $de_checked = "checked"; 
				else $ac_checked = "checked"; 
				
				$html .= "<form action='index.php?page=system&up=disclaimer&save' method='post'>
					<p>
					<input type='radio' name='active' value='1' $ac_checked /> ".\Main\Language::$txt["infos"]["activated"]." &nbsp; 
					<input type='radio' name='active' value='0' $de_checked /> ".\Main\Language::$txt["infos"]["unactivated"]."</p><p>
					
					<textarea id='cke' name='cke'>".htmlspecialchars($settings["disclaimer_txt"])."</textarea>
					<script>CKEDITOR.replace('cke');</script>
					</p><p>
					<button class='btn btn-primary right'>".\Main\Language::$txt["buttons"]["save"]."</button>
					</p>
				</form>";
			}
		$html .= "</div></div>"; 
		
		return $html; 
	}
	
	private static function showSystemLeft() 
	{
		$html = ""; 
		
		$up_general = ""; 
		$up_protocol = "";
		$up_impressum = ""; 
		$up_terms_of_use = ""; 
		$up_disclaimer = ""; 
		
		if(isset($_GET["up"]))
		{
			if($_GET["up"] == "general") $up_general = "active"; 
			else if($_GET["up"] == "protocol") $up_protocol = "active"; 
			else if($_GET["up"] == "termsofuse") $up_terms_of_use = "active"; 
			else if($_GET["up"] == "disclaimer") $up_disclaimer = "active"; 
			else if($_GET["up"] == "impressum") $up_impressum = "active"; 
		}
		else
			$up_general = "active"; 
		
		$html .= "<div class='panel panel-primary panel-left'>";
			$html .= "<div class='panel-heading'>".\Main\Language::$txt["buttons"]["menue_system"]."</div>";
			$html .= "<div class='panel-body'>"; 
			
				$html .= "<ul class='nav left_menue_ul'>";
					$html .= "<li class='$up_general'><a href='index.php?page=system&up=general'>".\Main\Language::$txt["buttons"]["menue_system_general"]."</a></li>";
					$html .= "<li class='$up_impressum'><a href='index.php?page=system&up=impressum'>".\Main\Language::$txt["buttons"]["menue_system_impressum"]."</a></li>";
					$html .= "<li class='$up_terms_of_use'><a href='index.php?page=system&up=termsofuse'>".\Main\Language::$txt["buttons"]["menue_system_termsofuse"]."</a></li>";
					$html .= "<li class='$up_disclaimer'><a href='index.php?page=system&up=disclaimer'>".\Main\Language::$txt["buttons"]["menue_system_disclaimer"]."</a></li>";
					$html .= "<li class='$up_protocol'><a href='index.php?page=system&up=protocol'>".\Main\Language::$txt["buttons"]["menue_system_protocol"]."</a></li>";
				$html .= "</ul>"; 
			
			$html .= "</div>"; 
		$html .= "</div>"; 
		
		return $html; 
	}
	
	private static function showContentLeft() 
	{
		$html = ""; 
		
		$b_list = ""; 
		$b_add = ""; 
		$tag_list = ""; 
		$tag_add = ""; 
		
		if(isset($_GET["up"]))
		{
			if($_GET["up"] == "list-b") $b_list = "active"; 
			else if($_GET["up"] == "add-b") $b_add = "active"; 
			else if($_GET["up"] == "list-tags") $tag_list = "active"; 
			else if($_GET["up"] == "add-tag") $tag_add = "active"; 
		}
		else
			$b_list = "active"; 
		
		$html .= "<div class='panel panel-primary panel-left'>";
			$html .= "<div class='panel-heading'>".\Main\Language::$txt["infos"]["content_left_header"]."</div>";
			$html .= "<div class='panel-body'>";
				$html .= "<ul class='nav left_menue_ul'>";
					$html .= "<li class='$b_list'><a href='index.php?page=content&up=list-b'>".\Main\Language::$txt["infos"]["content_left_list_board"]."</a></li>"; 
					$html .= "<li class='$b_add'><a href='index.php?page=content&up=add-b'>".\Main\Language::$txt["infos"]["content_left_add_board"]."</a></li>"; 
					$html .= "<li class='$tag_list'><a href='index.php?page=content&up=list-tags'>".\Main\Language::$txt["infos"]["content_left_list_tags"]."</a></li>"; 
					$html .= "<li class='$tag_add'><a href='index.php?page=content&up=add-tag'>".\Main\Language::$txt["infos"]["content_left_add_tag"]."</a></li>"; 
				$html .= "</ul>";
			$html .= "</div>"; 
		$html .= "</div>"; 
		
		return $html; 
	}
	
	private static function showContent() 
	{
		// Zum Forum hinzufügen benötigt
		function listAddOptions() 
		{
			$html = ""; 
			
			$rst = \Main\DB::select("kategorien", "id, name", "forum='0' AND kategorie='0'", null, "orderId, id");
			while($row = $rst->fetch_object())
			{
				$kat = array(
					"id" => $row->id,
					"name" => $row->name
				);
				
				$html .= "<option value='k".$kat["id"]."'>".htmlspecialchars($kat["name"])."</option>"; 
				$html .= listAddOptionsK(0, $kat["id"]); 
			}
			
			$rst = \Main\DB::select("foren", "id, name", null, null, "orderId, id");
			while($row = $rst->fetch_object())
			{
				$forum = array(
					"id" => $row->id,
					"name" => $row->name
				);
				
				$html .= "<option value='b".$forum["id"]."'>".htmlspecialchars($forum["name"])."</option>"; 
				$html .= listAddOptionsK($forum["id"], 0); 
			}
			
			return $html; 
		}
		function listAddOptionsK($forum, $kat, $space=4)
		{
			$html = ""; 
			
			if($forum != 0) 
			{
				$rst = \Main\DB::select("kategorien", "id, name", "forum='".\Main\DB::escape($forum)."'", null, "orderId, id");
			}
			else if($kat != 0) 
			{
				$rst = \Main\DB::select("kategorien", "id, name", "kategorie='".\Main\DB::escape($kat)."'", null, "orderId, id");
			}
			
			$spstring = ""; 
			for($i = 0; $i < $space; $i++) {
				$spstring .= "&nbsp;"; 
			}
			
			while($row = $rst->fetch_object())
			{
				$kate = array(
					"id" => $row->id,
					"name" => $row->name
				);
				
				$html .= "<option value='k".$kate["id"]."'>".$spstring.htmlspecialchars($kate["name"])."</option>"; 
				$html .= listAddOptionsK(0, $kate["id"], $space+4); 
			}
			
			return $html; 
		}
		///
	
		$html = "";

		$up = "list-b";
		if(isset($_GET["up"])) $up = $_GET["up"]; 
		
		$html .= "<div class='panel panel-primary'><div class='panel-body'>";
		
			if($up == "list-b") // Foren auflisten
			{
				if(isset($_GET["bGroups"]))
				{
					$html .= self::showBoardGroups($_GET["bGroups"]); 
				}
				else if(isset($_GET["kGroups"]))
				{
					$html .= self::showKategoryGroups($_GET["kGroups"]); 
				}
				else
					$html .= self::showContentFList(); 
			}
			else if($up == "add-b") // Forum hinzufügen / bearbeiten
			{
				if(isset($_GET["save"], $_POST["name"]) && !isset($_GET["edit-b"]) && !isset($_GET["edit-k"]))
				{
					$input = array(
						"name" => $_POST["name"], 
						"desc" => $_POST["desc"],
						"type" => $_POST["type"],
						"sel-board" => $_POST["sel-board"],
						"orderId" => $_POST["orderId"],
						"cssclass" => $_POST["cssclass"]
					);
					
					if($input["orderId"] == "") $input["orderId"] = 0; 
					
					if($input["type"] == 0) // Forum 
					{
						\Main\DB::insert("foren", array(
							"name" => $input["name"],
							"description" => $input["desc"],
							"orderId" => $input["orderId"],
							"cssclass" => $input["cssclass"]
						));
						$html .= "<div class='alert alert-success'>".\Main\Language::$txt["alerts"]["content_add_board_board_success"]."</div>";
					}
					else if($input["type"] == 1) // Kategorie 
					{
						$ov_board = 0; 
						$ov_kat = 0; 
						$prefix = substr($input["sel-board"], 0, 1);
						if($prefix == "b") 
							$ov_board = substr($input["sel-board"], 1);
						if($prefix == "k") 
							$ov_kat = substr($input["sel-board"], 1); 
							
						\Main\DB::insert("kategorien", array(
							"name" => $input["name"],
							"description" => $input["desc"],
							"orderId" => $input["orderId"],
							"forum" => $ov_board,
							"kategorie" => $ov_kat,
							"cssclass" => $input["cssclass"]
						)); 
						$html .= "<div class='alert alert-success'>".\Main\Language::$txt["alerts"]["content_add_board_kategory_success"]."</div>";
					}
				}
			
				if(isset($_GET["save"], $_POST["name"], $_GET["edit-b"])) // Bearbeitung von Forum speichern
				{
					$input = array(
						"bid" => $_POST["bid"],
						"name" => $_POST["name"], 
						"desc" => $_POST["desc"],
						"orderId" => $_POST["orderId"],
						"cssclass" => $_POST["cssclass"]
					);
					
					\Main\DB::update("foren", $input["bid"], array(
						"name" => $input["name"],
						"description" => $input["desc"],
						"orderId" => $input["orderId"],
						"cssclass" => $input["cssclass"]
					));
					
					$html .= "<div class='alert alert-success'>".\Main\Language::$txt["alerts"]["content_edit_board_save_board"]."</div>"; 
				}
				if(isset($_GET["save"], $_POST["name"], $_GET["edit-k"])) // Bearbeitung von Kategorie speichern
				{
					$input = array(
						"kid" => $_POST["kid"],
						"name" => $_POST["name"], 
						"desc" => $_POST["desc"],
						"orderId" => $_POST["orderId"],
						"cssclass" => $_POST["cssclass"],
						"sel-board" => $_POST["sel-board"]
					);
					
					$ov_board = 0; 
					$ov_kat = 0; 
					$prefix = substr($input["sel-board"], 0, 1);
					if($prefix == "b") 
						$ov_board = substr($input["sel-board"], 1);
					if($prefix == "k") 
						$ov_kat = substr($input["sel-board"], 1); 
						
					if($ov_kat == $input["kid"]) $ov_kat = 0; 
					
					\Main\DB::update("kategorien", $input["kid"], array(
						"name" => $input["name"],
						"description" => $input["desc"],
						"orderId" => $input["orderId"],
						"cssclass" => $input["cssclass"],
						"forum" => $ov_board,
						"kategorie" => $ov_kat
					));
					
					$html .= "<div class='alert alert-success'>".\Main\Language::$txt["alerts"]["content_edit_board_save_kategory"]."</div>"; 
				}
			
				$lplus = ""; 
				$lidplus = ""; 
				$radio1checked = "checked"; 
				$radio2checked = ""; 
				$seldp = "display:none;"; 
				$fopt = ""; 
				$rabled = ""; 
				$val_name = ""; 
				$val_desc = ""; 
				$val_order = ""; 
				$val_css = ""; 
				if(isset($_GET["edit-b"])) // Daten Laden 
				{
					$rst = \Main\DB::select("foren", "name, description, orderId, cssclass", "id='".\Main\DB::escape($_GET["edit-b"])."'");
					$row = $rst->fetch_object(); 
					$val_name = $row->name; 
					$val_desc = $row->description; 
					$val_order = $row->orderId; 
					$val_css = $row->cssclass; 
				
					$lplus = "&edit-b=".$_GET["edit-b"]; 
					$lidplus = "<input type='hidden' name='bid' value='".htmlspecialchars($_GET["edit-b"], ENT_QUOTES)."' />"; 
					$rabled = "disabled"; 
				}
				if(isset($_GET["edit-k"])) // Daten Laden
				{
					$rst = \Main\DB::select("kategorien", "name, description, orderId, cssclass, kategorie, forum", "id='".\Main\DB::escape($_GET["edit-k"])."'");
					$row = $rst->fetch_object(); 
					$ukat = $row->kategorie; 
					$uf = $row->forum; 
					$val_name = $row->name; 
					$val_desc = $row->description; 
					$val_order = $row->orderId; 
					$val_css = $row->cssclass; 
				
					$lplus = "&edit-k=".$_GET["edit-k"];
					$lidplus = "<input type='hidden' name='kid' value='".htmlspecialchars($_GET["edit-k"], ENT_QUOTES)."' />";
					$radio1checked = ""; 
					$radio2checked = "checked"; 
					$seldp = "display:block;"; 
					$rabled = "disabled";  
					
					if($ukat != 0) 
					{
						$rst = \Main\DB::select("kategorien", "name", "id='".\Main\DB::escape($ukat)."'");
						$row = $rst->fetch_object(); 
						$name = $row->name; 
						$fopt = "<option value='k$ukat'>".htmlspecialchars($name)."</option>";
					}
					else if($uf != 0) 
					{
						$rst = \Main\DB::select("foren", "name", "id='".\Main\DB::escape($uf)."'");
						$row = $rst->fetch_object(); 
						$name = $row->name; 
						$fopt = "<option value='b$uf'>".htmlspecialchars($name)."</option>";
					}
					
				}
			
				$html .= "<h3>".\Main\Language::$txt["infos"]["content_add_board_header"]."</h3>"; 
				$html .= "<form action='index.php?page=content&up=add-b$lplus&save' method='post'>$lidplus
					<input type='text' name='name' placeholder='".\Main\Language::$txt["forms"]["content_add_board_placeholder_name"]."' value='".htmlspecialchars($val_name, ENT_QUOTES)."' class='form-control' />
					<textarea class='form-control form-margin-top-low' name='desc' placeholder='".\Main\Language::$txt["forms"]["content_add_board_placeholder_desc"]."'>".htmlspecialchars($val_desc)."</textarea>
					<div class='form-margin-top'></div>
					<input type='radio' onchange='checkNewBRadios();' name='type' value='0' $radio1checked $rabled /> ".\Main\Language::$txt["forms"]["content_add_board_placeholder_radio0"]." &nbsp; <input onchange='checkNewBRadios();' type='radio' name='type' value='1' $radio2checked $rabled /> ".\Main\Language::$txt["forms"]["content_add_board_placeholder_radio1"]."
					<select id='b_sel' style='$seldp' class='form-control form-margin-top-low' name='sel-board'>
						$fopt<option value='none'>".\Main\Language::$txt["forms"]["content_add_board_placeholder_sel_board"]."</option>".listAddOptions()."
					</select>
					<input class='form-control form-margin-top' type='number' name='orderId' placeholder='".\Main\Language::$txt["forms"]["content_add_board_placeholder_orderId"]."' value='".htmlspecialchars($val_order, ENT_QUOTES)."' />
					<input class='form-control form-margin-top' type='text' name='cssclass' placeholder='".\Main\Language::$txt["forms"]["content_add_board_placeholder_cssclass"]."' value='".htmlspecialchars($val_css, ENT_QUOTES)."' />
					<button class='btn btn-primary right btn-margin-top'>".\Main\Language::$txt["buttons"]["save"]."</button>
				</form>"; 
			}
			else if($up == "list-tags") // Tags auflisten 
			{
				if(isset($_GET["groups"]))
					$html .= self::showTagGroups($_GET["groups"]); 
				else if(isset($_GET["boards"]))
					$html .= self::showTagBoards($_GET["boards"]); 
				else if(isset($_GET["colors"]))
					$html .= self::showTagColors($_GET["colors"]); 
				else
					$html .= self::showTagsList(); 
			}
			else if($up == "add-tag") // Tag hinzufügen 
			{
				$html .= self::showAddTag(); 
			}
		
		
		$html .= "</div></div>"; // panel-body panel
		return $html; 
	}
	
	// Zeigt eine Liste mit allen Tags an
	private static function showTagsList()
	{
		$html = ""; 
		
		if(isset($_GET["sdel"])) // Fragen ob Tag gelöscht wird
		{
			$rst = \Main\DB::select("tags", "name", "id='".\Main\DB::escape($_GET["sdel"])."'");
			if($rst->num_rows > 0) 
			{
				$row = $rst->fetch_object(); 
				$tname = $row->name; 
				$string = \Main\Language::$txt["infos"]["tag_rly_del?"]; 
				$string = str_replace("[tag]", htmlspecialchars($tname), $string);
				$html .= "<strong>".$string."</strong><br />
				<a href='index.php?page=content&up=list-tags&del=".$_GET["sdel"]."'><button class='btn btn-success button-margin-top'>".\Main\Language::$txt["buttons"]["yes"]."</button></a> &nbsp; 
				<a href='index.php?page=content&up=list-tags'><button class='btn btn-danger button-margin-top'>".\Main\Language::$txt["buttons"]["no"]."</button></a><br /><br />";
			}
		}
		
		if(isset($_GET["del"])) // Tag entgültig löschen
		{
			$html .= \Main\Admin\Control::deleteTag($_GET["del"]); 
		}
		
		$html .= "<h3>".\Main\Language::$txt["infos"]["tags_list_header"]."</h3><br /><br />"; 
		
		$html .= "<div class='panel panel-primary panel-tags-list'><div class='panel-heading'>".\Main\Language::$txt["infos"]["tags_tbl_list_header"]."</div>"; 
		$html .= "<div class='panel-body'>";
			$rst = \Main\DB::select("tags", "id, name, useable, typ, backgroundcolor, textcolor", null, null, "id");
			while($row = $rst->fetch_object())
			{
				$tag = array("id" => $row->id,
							 "name" => $row->name,
							 "useable" => $row->useable,
							 "typ" => $row->typ,
							 "backgroundcolor" => $row->backgroundcolor,
							 "textcolor" => $row->textcolor
							 );
							 
							 
				$useable_string = \Main\Language::$txt["infos"]["tag_not_useable"];
				
				if($tag["useable"] == 1) 
					$useable_string = \Main\Language::$txt["infos"]["tag_useable"]; 
							
				$werestring = \Main\Language::$txt["infos"]["tag_everywhere_autogain_off"]; 			
				$result = \Main\DB::select("tags_foren", "forum, kategory, autogain", "tag='".\Main\DB::escape($tag["id"])."'");
				if($result->num_rows == 1) 
				{
					$roww = $result->fetch_object(); 
					$tag_forumid = $roww->forum; 
					$tag_kid = $roww->kategory; 
					$tag_autogain = $roww->autogain; 
					
					if($tag_forumid != 0) 
					{
						$result = \Main\DB::select("foren", "name", "id='".\Main\DB::escape($tag_forumid)."'");
						$roww = $result->fetch_object(); 
						
						$werestring = \Main\Language::$txt["infos"]["tag_in_board"]." <strong>".htmlspecialchars($roww->name)."</strong>"; 
					}
					else if($tag_kid != 0) 
					{
						$result = \Main\DB::select("kategorien", "name", "id='".\Main\DB::escape($tag_kid)."'");
						$roww = $result->fetch_object(); 
						
						$werestring = \Main\Language::$txt["infos"]["tag_in_kategory"]." <strong>".htmlspecialchars($roww->name)."</strong>"; 
					}
					
					if($tag_autogain == 0) $werestring .= " | ".\Main\Language::$txt["infos"]["tag_autogain"]." <strong>".\Main\Language::$txt["infos"]["tag_autogain_off"]."</strong>";
					else $werestring .= " | ".\Main\Language::$txt["infos"]["tag_autogain"]." <strong>".\Main\Language::$txt["infos"]["tag_autogain_on"]."</strong>"; 
				}
				
				if($result->num_rows > 1) 
				{
					$werestring = \Main\Language::$txt["infos"]["tag_available_in_more_than_one"];					
				}
				
				$tagColorsString = "<i title='".htmlspecialchars(\Main\Language::$txt["titles"]["tags_edit_color_no"], ENT_QUOTES)."' class='fa fa-square fa-lg'></i>"; 
				$innerString = htmlspecialchars($tag["name"]);
				if($tag["typ"] == 0) 
				{
					$innerString = "<span class='badge'>" . $innerString . "</span>"; 
				}
				if($tag["typ"] == 1) 
				{
					$tagColorsString = "<a title='".htmlspecialchars(\Main\Language::$txt["titles"]["tags_edit_color"], ENT_QUOTES)."' href='index.php?page=content&up=list-tags&colors=".$tag["id"]."'><i class='fa fa-square fa-lg square-tags-colors'></i></a>"; 

					$bgcolstring = ""; 
					$txtcolstring = ""; 
					if($tag["backgroundcolor"] != "") 
						$bgcolstring = "background:".$tag["backgroundcolor"].";";
					if($tag["textcolor"] != "") 
						$txtcolstring = "color:".$tag["textcolor"].";";  
					$innerString = "<span class='badge label' style='$bgcolstring $txtcolstring'>" . $innerString . "</span>"; 
				}
				
							
				$html .= "<div>";
				
					$html .= "<div class='right'>
						$tagColorsString
						<a title='".htmlspecialchars(\Main\Language::$txt["titles"]["tags_edit_groups"], ENT_QUOTES)."' href='index.php?page=content&up=list-tags&groups=".$tag["id"]."'><i class='fa fa-users fa-lg'></i></a>&nbsp;
						<a title='".htmlspecialchars(\Main\Language::$txt["titles"]["tags_edit_boards"], ENT_QUOTES)."' href='index.php?page=content&up=list-tags&boards=".$tag["id"]."'><i class='fa fa-bars fa-lg'></i></a>&nbsp;
						<a title='".htmlspecialchars(\Main\Language::$txt["titles"]["tags_edit_del"], ENT_QUOTES)."' href='index.php?page=content&up=list-tags&sdel=".$tag["id"]."'><i class='fa fa-times fa-lg'></i></a>
					</div>"; 
					
					$html .= "<div class='right' style='margin-right:15px; '>
						$useable_string
					</div>"; 
					
				
				$html .= "<div style='float:left;width:calc(100% - 250px);'><div class='right'>$werestring</div>" . $innerString . "</div>";
				
				
				$html .= "</div><div class='clear'></div>"; 
			}
		$html .= "</div></div>";
		
		return $html; 
	}
	
	// Zeigt alle Gruppen eines Tags an
	private static function showTagGroups($tid) 
	{
		$html = "";		
		
		$rst = \Main\DB::select("tags", "name", "id='".\Main\DB::escape($tid)."'");
		if($rst->num_rows == 0) {
			return $html; 
		}
		$row = $rst->fetch_object(); 
		
		$tname = $row->name; 
		
		if(isset($_GET["addg"]))
		{
			if($_POST["addgroup"] != "none") 
				$html .= \Main\Admin\Control::createTagGroup($tid, $_POST["addgroup"]); 
		}
		
		if(isset($_GET["delg"]))
		{
			$html .= \Main\Admin\Control::deleteTagGroup($tid, $_GET["delg"]); 
		}
		
		$html .= "<h3>".\Main\Language::$txt["infos"]["tag_groups_grouprights"]."</h3><strong>".\Main\Language::$txt["infos"]["tag::"]." ".htmlspecialchars($tname)."</strong><br /><br />";
		
		$html .= "<div class='panel panel-primary panel-tag-groups'><div class='panel-heading'>".\Main\Language::$txt["infos"]["tag_groups_panel_heading"]."</div>";
		$html .= "<div class='panel-body'>";
			$rst = \Main\DB::select("tags_gruppen", "id, gruppe", "tag='".\Main\DB::escape($tid)."'", null, "id");
			if($rst->num_rows == 0) 
			{
				$html .= \Main\Language::$txt["infos"]["tag_useable_by_everybody"]; 
			}
			while($row = $rst->fetch_object())
			{
				$tg = array("id" => $row->id,
							"gruppe" => $row->gruppe);
							
				$result = \Main\DB::select("gruppen", "name", "id='".\Main\DB::escape($tg["gruppe"])."'");
				$roww = $result->fetch_object(); 
				
				$gruppe = array("name" => $roww->name);
							
				$html .= "<div>
					<div class='right'>
						<a href='index.php?page=content&up=list-tags&groups=$tid&delg=".$tg["gruppe"]."'><i class='fa fa-times fa-lg'></i></a>
					</div>
					".htmlspecialchars($gruppe["name"])."
				</div>"; 
			}
		$html .= "</div></div>";
		
		$html .= "<br /><br />
		<form action='index.php?page=content&up=list-tags&groups=$tid&addg' method='post'>
			<select name='addgroup'>
				<option value='none'>".\Main\Language::$txt["infos"]["tag_groups_choose_group"]."</option>"; 
				$rst = \Main\DB::select("gruppen", "id, name", null, null, "id");
				while($row = $rst->fetch_object())
				{
					$gruppe = array("id" => $row->id,
									"name" => $row->name);
									
					$rstt = \Main\DB::select("tags_gruppen", "id", "tag='".\Main\DB::escape($tid)."' AND gruppe='".\Main\DB::escape($gruppe["id"])."'");
					if($rstt->num_rows == 0) 
					{
						$html .= "<option value='".$gruppe["id"]."'>".htmlspecialchars($gruppe["name"], ENT_QUOTES)."</option>"; 
					}
				}
			$html .= "</select><br />
			<button class='btn btn-primary button-margin-top'>".\Main\Language::$txt["buttons"]["add"]."</button>
		</form>"; 
		return $html; 
	}
	
	// Zeigt alle Foren/Kategorie Regeln für einen Tag an
	private static function showTagBoards($tid) 
	{
		function ListBoards() 
		{
			$html = ""; 
			
			$rst = \Main\DB::select("foren", "id, name", null, null, "orderId, id");
			while($row = $rst->fetch_object())
			{
				$forum = array("id" => $row->id,
							   "name" => $row->name
							   );
							   
				$html .= "<option value='f".$forum["id"]."'>".htmlspecialchars($forum["name"])."</option>"; 
				$html .= ListKategories($forum["id"], 0, 4); 
			}
			
			return $html; 
		}
	
		function ListKategories($fid=0, $kid=0, $leer=0) 
		{
			$html = ""; 
			
			if($fid != 0 && $kid == 0) 
			{
				$rst = \Main\DB::select("kategorien", "id, name", "forum='".\Main\DB::escape($fid)."'", null, "orderId, id");
			}
			
			if($fid == 0 && $kid != 0) 
			{
				$rst = \Main\DB::select("kategorien", "id, name", "kategorie='".\Main\DB::escape($kid)."'", null, "orderId, id");
			}
			
			// Kategorien Auflisten die nirgends enthalten sind
			if($fid == 0 && $kid == 0) 
			{
				$rst = \Main\DB::select("kategorien", "id, name", "forum='0' AND kategorie='0'", null, "orderId, id");
			}
			
			$leers = ""; 
			for($i = 0; $i < $leer; $i++) 
			{
				$leers .= "&nbsp;"; 
			}
			
			while($row = $rst->fetch_object())
			{
				$kat = array("id" => $row->id,
							 "name" => $row->name
							 );
							 
				$html .= "<option value='k".$kat["id"]."'>$leers".htmlspecialchars($kat["name"])."</option>";
				$html .= ListKategories(0, $kat["id"], $leer + 4); 
			}
			
			return $html; 
		}
		
		if(isset($_POST["addb"])) // Forum/Kategorie hinzufügen
		{
			$typ = substr($_POST["addb"], 0, 1);
			$wid = substr($_POST["addb"], 1); 
			
			if($typ == "f") // Forum 
			{
				\Main\DB::insert("tags_foren", array("tag" => $tid, "forum" => $wid));
			}
			if($typ == "k") // Kategorie 
			{
				\Main\DB::insert("tags_foren", array("tag" => $tid, "kategory" => $wid));
			}
		}
		
		$html = ""; 
		
		$rst = \Main\DB::select("tags", "name", "id='".\Main\DB::escape($tid)."'");
		if($rst->num_rows == 0) {
			return $html; 
		}
		$row = $rst->fetch_object(); 
		
		$tname = $row->name; 
		
		if(isset($_GET["del"])) // Einen Eintrag löschen 
		{
			$html .= \Main\Admin\Control::deleteTagBoard($_GET["del"]); 
		}
		
		if(isset($_GET["setautogain0"])) // Autogain auf 0 setzen
		{
			$tfid = $_GET["setautogain0"];
			\Main\DB::update("tags_foren", $tfid, array("autogain" => 0));
		}
		
		if(isset($_GET["setautogain1"])) // Autogain auf 1 setzen
		{
			$tfid = $_GET["setautogain1"];
			\Main\DB::update("tags_foren", $tfid, array("autogain" => 1)); 
		}
		
		$html .= "<h3>".\Main\Language::$txt["infos"]["tags_allowed_boards"]."</h3><strong>".\Main\Language::$txt["infos"]["tag::"]." ".htmlspecialchars($tname)."</strong><br /><br />"; 
		
		$html .= "<div class='panel panel-primary panel-tag-boards'><div class='panel-heading'>".\Main\Language::$txt["infos"]["tag_boards_panel_heading"]."</div>
			<div class='panel-body'>";
		
		$rst = \Main\DB::select("tags_foren", "id, forum, kategory, autogain", "tag='".\Main\DB::escape($tid)."'");
		if($rst->num_rows == 0) 
		{
			$html .= \Main\Language::$txt["infos"]["tag_allowed_everywhere"]; 
		}
		while($row = $rst->fetch_object())
		{
			$tf = array("id" => $row->id, 
						"forum" => $row->forum,
						"kategory" => $row->kategory,
						"autogain" => $row->autogain);
						
			$string = \Main\Language::$txt["infos"]["undefined"]; 
			if($tf["forum"] != 0) 
			{
				$result = \Main\DB::select("foren", "name", "id='".\Main\DB::escape($tf["forum"])."'");
				$roww = $result->fetch_object(); 
				$string = \Main\Language::$txt["infos"]["board::"]." <strong>".htmlspecialchars($roww->name)."</strong>"; 
			}
			else if($tf["kategory"] != 0) 
			{
				$result = \Main\DB::select("kategorien", "name", "id='".\Main\DB::escape($tf["kategory"])."'");
				$roww = $result->fetch_object(); 
				$string = \Main\Language::$txt["infos"]["kategory::"]." <strong>".htmlspecialchars($roww->name)."</strong>"; 
			}
			
			$circle = "<a href='index.php?page=content&up=list-tags&boards=$tid&setautogain1=".$tf["id"]."'><i class='fa fa-circle fa-lg circle-grey'></i></a>"; 
			if($tf["autogain"] == 1) $circle = "<a href='index.php?page=content&up=list-tags&boards=$tid&setautogain0=".$tf["id"]."'><i class='fa fa-circle fa-lg circle-green'></i></a>"; 			
						
			$html .= "<div>";
			
				$html .= "<div class='right'>
					$circle
					<a href='index.php?page=content&up=list-tags&boards=$tid&del=".$tf["id"]."'><i class='fa fa-times fa-lg'></i></a>
				</div>"; 
			
				$html .= $string; 
			
			$html .= "</div>"; 
		}
		
		$html .= "</div></div>";
		
		$html .= "<br /><br />
		<form action='index.php?page=content&up=list-tags&boards=$tid' method='post'>
			<select class='form-control' name='addb'>
				<option value='none'>".\Main\Language::$txt["infos"]["tag_choose_board_kat"]."</option>"; 
				$html .= ListKategories(); 
				$html .= ListBoards(); 
			$html .= "</select>	
			<button class='btn btn-primary button-margin-top'>".\Main\Language::$txt["buttons"]["add"]."</button>
		</form>"; 
		
		return $html; 
	}

	private static function showTagColors($tid) 
	{
		$html = ""; 

		$rst = \Main\DB::select("tags", "name, backgroundcolor, textcolor", "id='".\Main\DB::escape($tid)."' AND typ='1'");
		if($rst->num_rows == 0) return $html; 

		$row = $rst->fetch_object(); 
		$tag = array("id" => $tid,
					 "name" => $row->name,
					 "backgroundcolor" => $row->backgroundcolor,
					 "textcolor" => $row->textcolor
					 );
		$show_cols = 1; 

		if(isset($_GET["save"]))
		{
			$input = array("backgroundcolor" => "#" . $_POST["bgcol"],
						"textcolor" => "#" . $_POST["txtcol"]);
			\Main\DB::update("tags", $tid, $input);
			$html .= "<div class='alert alert-success'>".\Main\Language::$txt["alerts"]["tag_colors_updated"]."</div>"; 
			$show_cols = 0; 
			$html .= self::showTagsList(); 
		}

		if($show_cols)
		{
			$hstring = \Main\Language::$txt["infos"]["tag_edit_colors_for_tag"]; 
			$hstring = str_replace("[tag]", htmlspecialchars($tag["name"]), $hstring); 

			$html .= "<h3>$hstring</h3>"; 
			$html .= "<br />";

			$gbg = ltrim($tag["backgroundcolor"], "#"); 
			$txt = ltrim($tag["textcolor"], "#");

			$html .= "<form action='index.php?page=content&up=list-tags&colors=$tid&save' method='post'>
				<strong>".\Main\Language::$txt["infos"]["backgroundcolor::"]."</strong><br />
				<input name='bgcol' class='form-control jscolor' value='$gbg' /><br />
				<strong>".\Main\Language::$txt["infos"]["textcolor::"]."</strong><br />
				<input name='txtcol' class='form-control jscolor' value='$txt' /><br />
				<button class='btn btn-primary right'>".\Main\Language::$txt["buttons"]["save"]."</button>
			</form>"; 
		}


		return $html; 
	}
	
	// Zeigt die Form zum hinzufügen von Tags an
	private static function showAddTag()
	{
		$html = ""; 
		
		// Tag hinzufügen 
		if(isset($_GET["add"], $_POST["name"]))
		{
			$input = array("name" => $_POST["name"],
							"useable" => $_POST["useable"],
							"typ" => $_POST["typ"]
							);
							
			\Main\DB::insert("tags", $input);
			$html .= "<div class='alert alert-success'>".\Main\Language::$txt["alerts"]["tag_create_success"]."</div>"; 
		}
		
		$html .= "<h3>".\Main\Language::$txt["infos"]["tag_add_header"]."</h3>"; 
		
		$html .= "<form action='index.php?page=content&up=add-tag&add' method='post'>
			<br />
			<input type='text' name='name' placeholder='".\Main\Language::$txt["forms"]["tag_add_name"]."' class='form-control' />
			<br />
			<strong>".\Main\Language::$txt["forms"]["tag_add_useable"]."</strong><br />
			<input type='radio' name='useable' value='0' id='us_no' /> <label for='us_no'>".\Main\Language::$txt["buttons"]["no"]."</label> &nbsp;  &nbsp;
			<input type='radio' name='useable' value='1' id='us_ja' checked /> <label for='us_ja'>".\Main\Language::$txt["buttons"]["yes"]."</label>
			<br />
			<strong>".\Main\Language::$txt["forms"]["tag_add_typ"]."</strong><br />
			<select name='typ' class='form-control'>
				<option value='0'>".\Main\Language::$txt["forms"]["tag_add_typ_0"]."</option>
				<option value='1'>".\Main\Language::$txt["forms"]["tag_add_typ_1"]."</option>
			</select>
			<br />
			<div class='right'><button class='btn btn-primary'>".\Main\Language::$txt["buttons"]["save"]."</button></div>
		</form>"; 
		
		return $html; 
	}
	
	// Zeigt die Gruppenverwaltung für Foren an
	private static function showBoardGroups($bid) 
	{
		$html = ""; 
		
		// Eine neue Gruppe hinzufügen ! 
		if(isset($_POST["addgroup"]))
		{
			$gid = $_POST["addgroup"]; 
			if($gid != "none") 
			{
				$rst = \Main\DB::select("gruppen_foren", "id", "forum='".\Main\DB::escape($bid)."' AND gruppe='".\Main\DB::escape($gid)."'");
				if($rst->num_rows == 0) 
				{
					\Main\DB::insert("gruppen_foren", array("gruppe" => $gid, "forum" => $bid));
				}
			}
		}
		
		// Eine Gruppe löschen
		if(isset($_GET["delg"]))
		{
			$sql = "DELETE FROM gruppen_foren WHERE forum='".\Main\DB::escape($bid)."' AND gruppe='".\Main\DB::escape($_GET["delg"])."'";
			\Main\DB::query($sql); 
		}
		
		// Permission See auf true setzen
		if(isset($_GET["sps1"]))
		{
			\Main\DB::update("gruppen_foren", $_GET["sps1"], array("permission_see" => 1));
		}
		// Permission See auf false setzen
		if(isset($_GET["sps0"]))
		{
			\Main\DB::update("gruppen_foren", $_GET["sps0"], array("permission_see" => 0));
		}
		
		// Permission write auf true setzen
		if(isset($_GET["spw1"]))
		{
			\Main\DB::update("gruppen_foren", $_GET["spw1"], array("permission_write" => 1));
		}
		// Permission write auf false setzen
		if(isset($_GET["spw0"]))
		{
			\Main\DB::update("gruppen_foren", $_GET["spw0"], array("permission_write" => 0));
		}
		
		$fname = ""; 
		$rst = \Main\DB::select("foren", "name", "id='".\Main\DB::escape($bid)."'");
		if($rst->num_rows > 0) 
		{
			$row = $rst->fetch_object(); 
			$fname = $row->name; 
		}
		
		$html .= "<h3>".\Main\Language::$txt["infos"]["bgroups_header"]."</h3><strong>".\Main\Language::$txt["infos"]["bgroups_board"]." ".htmlspecialchars($fname)."</strong><br /><br />"; 
		
		$html .= "<div class='panel panel-primary panel-grights'><div class='panel-heading'>".\Main\Language::$txt["infos"]["bgroups_current_rights"]."</div>";
		$html .= "<div class='panel-heading2'>
			<div>
				<div class='right'>
					<div class='ritem1'>".\Main\Language::$txt["infos"]["bgroups_ritem_1"]."</div>
					<div class='ritem2'>".\Main\Language::$txt["infos"]["bgroups_ritem_2"]."</div>
					<div class='ritem3'>".\Main\Language::$txt["infos"]["bgroups_ritem_3"]."</div>
				</div>
				".\Main\Language::$txt["infos"]["bgroups_ritem_0"]."
			</div>       
			
			
			</div>";
		$html .= "<div class='panel-body'>"; 
			$rst = \Main\DB::select("gruppen_foren", "id, gruppe, permission_see, permission_write", "forum='".\Main\DB::escape($bid)."'", null, "id");
			if($rst->num_rows == 0) 
			{
				$html .= "<div>".\Main\Language::$txt["infos"]["bgroups_no_rights"]."</div>"; 
			}
			while($row = $rst->fetch_object())
			{
				$gf = array("id" => $row->id,
							"gruppe" => $row->gruppe,
							"permission_see" => $row->permission_see,
							"permission_write" => $row->permission_write
							);
							
				$result = \Main\DB::select("gruppen", "name", "id='".\Main\DB::escape($gf["gruppe"])."'");
				if($result->num_rows > 0) 
				{
					$roww = $result->fetch_object(); 
					$gf["gname"] = $roww->name; 
					
					$ps = "<a href='index.php?page=content&up=list-b&bGroups=$bid&sps1=".$gf["id"]."'><i class='fa fa-square-o fa-lg'></i></a>";
					$pw = "<a href='index.php?page=content&up=list-b&bGroups=$bid&spw1=".$gf["id"]."'><i class='fa fa-square-o fa-lg'></i></a>";
					
					if($gf["permission_see"] == 1) 
						$ps = "<a href='index.php?page=content&up=list-b&bGroups=$bid&sps0=".$gf["id"]."'><i class='fa fa-check-square-o fa-lg'></i></a>";

					if($gf["permission_write"] == 1) 
						$pw = "<a href='index.php?page=content&up=list-b&bGroups=$bid&spw0=".$gf["id"]."'><i class='fa fa-check-square-o fa-lg'></i></a>";
					
					$html .= "<div><div class='right'>
							<div class='ritem1'>$ps</div> <div class='ritem2'>$pw</div>
							<div class='ritem3'><a href='index.php?page=content&up=list-b&bGroups=$bid&delg=".$gf["gruppe"]."'><i class='fa fa-times fa-lg'></i></a></div>
						</div>".htmlspecialchars($gf["gname"])."</div>"; 
				}
			}
		$html .= "</div></div>"; 

		$html .= "<br /><br />";
		$html .= "<strong>".\Main\Language::$txt["infos"]["bgroups_add_right"]."</strong><br />";
		$html .= "<form action='index.php?page=content&up=list-b&bGroups=$bid' method='post'><select class='form-control' name='addgroup'><option value='none'>".\Main\Language::$txt["infos"]["bgroups_add_right_choose_group"]."</option>";
			$rst = \Main\DB::select("gruppen", "id, name", null, null, "id");
			while($row = $rst->fetch_object())
			{
				$grp = array("id" => $row->id,
							 "name" => $row->name);
				$html .= "<option value='".$grp["id"]."'>".htmlspecialchars($grp["name"], ENT_QUOTES)."</option>"; 
			}
		$html .= "</select><br /><button class='btn btn-primary button-margin-top'>".\Main\Language::$txt["buttons"]["add"]."</button></form>"; 
		return $html; 
	}
	
	// Zeigt die Gruppenverwaltung für Kategorien an
	private static function showKategoryGroups($kid) 
	{
		$html = ""; 
		
		// Eine neue Gruppe hinzufügen ! 
		if(isset($_POST["addgroup"]))
		{
			$gid = $_POST["addgroup"]; 
			if($gid != "none") 
			{
				$rst = \Main\DB::select("gruppen_kats", "id", "kategory='".\Main\DB::escape($kid)."' AND gruppe='".\Main\DB::escape($gid)."'");
				if($rst->num_rows == 0) 
				{
					\Main\DB::insert("gruppen_kats", array("gruppe" => $gid, "kategory" => $kid));
				}
			}
		}
		
		// Eine Gruppe löschen
		if(isset($_GET["delg"]))
		{
			$sql = "DELETE FROM gruppen_kats WHERE kategory='".\Main\DB::escape($kid)."' AND gruppe='".\Main\DB::escape($_GET["delg"])."'";
			\Main\DB::query($sql); 
		}
		
		// Permission See auf true setzen
		if(isset($_GET["sps1"]))
		{
			\Main\DB::update("gruppen_kats", $_GET["sps1"], array("permission_see" => 1));
		}
		// Permission See auf false setzen
		if(isset($_GET["sps0"]))
		{
			\Main\DB::update("gruppen_kats", $_GET["sps0"], array("permission_see" => 0));
		}
		
		// Permission write auf true setzen
		if(isset($_GET["spw1"]))
		{
			\Main\DB::update("gruppen_kats", $_GET["spw1"], array("permission_write" => 1));
		}
		// Permission write auf false setzen
		if(isset($_GET["spw0"]))
		{
			\Main\DB::update("gruppen_kats", $_GET["spw0"], array("permission_write" => 0));
		}
		
		$html .= "<div class='panel panel-primary panel-grights'><div class='panel-heading'>".\Main\Language::$txt["infos"]["kgroups_current_rights"]."</div>";
		$html .= "<div class='panel-heading2'>
			<div>
				<div class='right'>
					<div class='ritem1'>".\Main\Language::$txt["infos"]["kgroups_ritem_1"]."</div>
					<div class='ritem2'>".\Main\Language::$txt["infos"]["kgroups_ritem_2"]."</div>
					<div class='ritem3'>".\Main\Language::$txt["infos"]["kgroups_ritem_3"]."</div>
				</div>
				".\Main\Language::$txt["infos"]["kgroups_ritem_0"]."
			</div>       
			
			
			</div>";
		$html .= "<div class='panel-body'>"; 
		
			$rst = \Main\DB::select("gruppen_kats", "id, gruppe, permission_see, permission_write", "kategory='".\Main\DB::escape($kid)."'", null, "id");
			if($rst->num_rows == 0) 
			{
				$html .= "<div>".\Main\Language::$txt["infos"]["kgroups_no_rights"]."</div>"; 
			}
			while($row = $rst->fetch_object())
			{
				$kg = array("id" => $row->id,
							"gruppe" => $row->gruppe,
							"permission_see" => $row->permission_see,
							"permission_write" => $row->permission_write
							);
							
				$result = \Main\DB::select("gruppen", "name", "id='".\Main\DB::escape($kg["gruppe"])."'");
				if($result->num_rows > 0) 
				{
					$roww = $result->fetch_object(); 
					$gname = $roww->name;
					
					$ps = "<a href='index.php?page=content&up=list-b&kGroups=$kid&sps1=".$kg["id"]."'><i class='fa fa-square-o fa-lg'></i></a>";
					$pw = "<a href='index.php?page=content&up=list-b&kGroups=$kid&spw1=".$kg["id"]."'><i class='fa fa-square-o fa-lg'></i></a>";
					
					if($kg["permission_see"] == 1) 
						$ps = "<a href='index.php?page=content&up=list-b&kGroups=$kid&sps0=".$kg["id"]."'><i class='fa fa-check-square-o fa-lg'></i></a>";

					if($kg["permission_write"] == 1) 
						$pw = "<a href='index.php?page=content&up=list-b&kGroups=$kid&spw0=".$kg["id"]."'><i class='fa fa-check-square-o fa-lg'></i></a>";
					
					$html .= "<div>
						<div class='right'>
							<div class='ritem1'>$ps</div>
							<div class='ritem2'>$pw</div>
							<div class='ritem3'><a href='index.php?page=content&up=list-b&kGroups=$kid&delg=".$kg["gruppe"]."'><i class='fa fa-times fa-lg'></i></a></div>
						</div>
						".htmlspecialchars($gname)."
					</div>"; 
				}
			}
		
		
		$html .= "</div></div>"; 
		
		$html .= "<br /><br />";
		$html .= "<strong>".\Main\Language::$txt["infos"]["kgroups_add_right"]."</strong><br />";
		$html .= "<form action='index.php?page=content&up=list-b&kGroups=$kid' method='post'><select class='form-control' name='addgroup'><option value='none'>".\Main\Language::$txt["infos"]["bgroups_add_right_choose_group"]."</option>";
			$rst = \Main\DB::select("gruppen", "id, name", null, null, "id");
			while($row = $rst->fetch_object())
			{
				$grp = array("id" => $row->id,
							 "name" => $row->name);
				$html .= "<option value='".$grp["id"]."'>".htmlspecialchars($grp["name"], ENT_QUOTES)."</option>"; 
			}
		$html .= "</select><br /><button class='btn btn-primary button-margin-top'>".\Main\Language::$txt["buttons"]["add"]."</button></form>"; 
		
		
		return $html; 
	}
	
	// Forenstruktur mit sämtlichen Foren/Kategorien auflisten
	private static function showContentFList()
	{
		function showKats($fId, $kId, $padding=15) 
		{
			$html = ""; 
			
			if($fId != 0) 
			{
				$rst = \Main\DB::select("kategorien", "id, name", "forum='".\Main\DB::escape($fId)."'", null, "orderId, id");
			}
			else if($kId != 0) 
			{
				$rst = \Main\DB::select("kategorien", "id, name", "kategorie='".\Main\DB::escape($kId)."'", null, "orderId, id");
			}
			else
			{
				$rst = \Main\DB::select("kategorien", "id, name", "forum='0' AND kategorie='0'", null, "orderId, id");
			}
			
			$newpadding = $padding + 15; 
			
			while($row = $rst->fetch_object())
			{
				$kat = array(
					"id" => $row->id,
					"name" => $row->name
				);
				
				$html .= "<div class='kitem' style='padding-left:".$padding."px;padding-right:5px; '>"; 
				$html .= "<div class='right kitem-control'>
									<a href='index.php?page=content&up=list-b&kGroups=".$kat["id"]."'><i class='fa fa-users fa-lg'></i></a>
									<a href='index.php?page=content&up=add-b&edit-k=".$kat["id"]."' onclick=''><i class='fa fa-pencil fa-lg'></i></a> 
									<a href='#' onclick='showDelKategory(".$kat["id"].");'><i class='fa fa-times fa-lg'></i></a></div>";
				$html .= htmlspecialchars($kat["name"])."</div>"; 
				
				$kats = showKats(0, $kat["id"], $newpadding); 
				if($kats != "") 
					$html .= "<div class='fkats'>".$kats."</div>"; 
			}
			
			return $html; 
		}
	
		$html = ""; 
		
		$html .= "<div class='forums_list'>";
		$html .= showKats(0,0,5); 
		$rst = \Main\DB::select("foren", "id, name", null, null, "orderId, id");
		while($row = $rst->fetch_object())
		{
			$forum = array(
				"id" => $row->id,
				"name" => $row->name
			);
			
			$padding = 15; 
			$html .= "<div class='fitem'><div class='right fitem-control'>
								<a href='index.php?page=content&up=list-b&bGroups=".$forum["id"]."'><i class='fa fa-users fa-lg'></i></a>
								<a href='index.php?page=content&up=add-b&edit-b=".$forum["id"]."'><i class='fa fa-pencil fa-lg'></i></a> 
								<a href='#del' onclick='showDelBoard(".$forum["id"].");'><i class='fa fa-times fa-lg'></i></a>
							</div>".htmlspecialchars($forum["name"])."</div>"; 
			$kats = showKats($forum["id"], 0, $padding); 
			if($kats != "") 
			{
				$html .= "<div class='fkats'>";
					$html .= $kats; 
				$html .= "</div>"; 
			}
		}
		$html .= "</div>"; 
		
		return $html; 
	}
	
	private static function showUsersLeft()
	{
		$html = ""; 
		
		$list_active = ""; 
		$add_active = ""; 
		$ug_list_active = ""; 
		$ug_add_active = ""; 
		$ur_list_active = ""; 
		$ur_add_active = ""; 
		
		if(isset($_GET["up"])) 
		{
			if($_GET["up"] == "list") $list_active = "active"; 
			else if($_GET["up"] == "add") $add_active = "active"; 
			else if($_GET["up"] == "usergroups") $ug_list_active = "active"; 
			else if($_GET["up"] == "usergroup_add") $ug_add_active = "active"; 
			else if($_GET["up"] == "userranks") $ur_list_active = "active"; 
			else if($_GET["up"] == "userrank_add") $ur_add_active = "active"; 
		} else $list_active = "active"; 
		
		$html .= "<div class='panel panel-primary panel-left'>";
			$html .= "<div class='panel-heading'>".\Main\Language::$txt["infos"]["users_left_header"]."</div>";
			$html .= "<div class='panel-body'>"; 
				$html .= "<ul class='nav left_menue_ul'>
					<li class='$list_active'><a href='index.php?page=users&up=list'>".\Main\Language::$txt["infos"]["users_left_list"]."</a></li>
					<li class='$add_active'><a href='index.php?page=users&up=add'>".\Main\Language::$txt["infos"]["users_left_add"]."</a></li>
					<li class='$ug_list_active'><a href='index.php?page=users&up=usergroups'>".\Main\Language::$txt["infos"]["users_left_usergroups_list"]."</a></li>
					<li class='$ug_add_active'><a href='index.php?page=users&up=usergroup_add'>".\Main\Language::$txt["infos"]["users_left_usergroup_add"]."</a></li>
					<li class='$ur_list_active'><a href='index.php?page=users&up=userranks'>".\Main\Language::$txt["infos"]["users_left_userranks_list"]."</a></li>
					<li class='$ur_add_active'><a href='index.php?page=users&up=userrank_add'>".\Main\Language::$txt["infos"]["users_left_userrank_add"]."</a></li>
				</ul>"; 
			$html .= "</div>"; 
		$html .= "</div>"; 
		
		return $html; 
	}
	
	private static function showUsers()
	{
		$html = ""; 
		
		$up = "list"; 
		if(isset($_GET["up"])) $up = $_GET["up"]; 
		
		$html .= "<div class='panel panel-primary'><div class='panel-body'>";
		if($up == "list") // Benutzer auflisten
		{	
		
			if(isset($_GET["ugroups"])) // Alle Gruppen eines Benutzers anzeigen 
			{
				$uid = $_GET["ugroups"]; 
				$uData = \Main\User\Control::getUserData($uid); 
				if($uData["id"] == 0) // Der Benutzer existiert nicht
				{
					$html .= "<div class='alert alert-danger'>".\Main\Language::$txt["alerts"]["user_doesnt_exist"]."</div></div></div>";
					return $html; 
				}
				
				if(isset($_GET["rma"])) // Fragen ob Benutzergruppe entfernt werden soll 
				{
					$gid = $_GET["rma"]; 
					$gdata = \Main\Admin\Control::getUsergroupData($gid); 
					if(isset($gdata["name"]))
					{
						$ask = \Main\Language::$txt["infos"]["kick_user_from_group"]; 
						$ask = str_replace("[user]", htmlspecialchars($uData["username"]), $ask);
						$ask = str_replace("[group]", htmlspecialchars($gdata["name"]), $ask);
						$html .= "<strong>$ask</strong><br />
						<a href='index.php?page=users&up=list&ugroups=$uid&rmu=$gid'><button class='btn btn-success'>".\Main\Language::$txt["buttons"]["yes"]."</button></a> &nbsp; <a href='index.php?page=users&up=list&ugroups=$uid'><button class='btn btn-danger'>".\Main\Language::$txt["buttons"]["no"]."</button></a>
						<br /><br />";
					}
				}
				
				if(isset($_GET["rmu"])) // Benutzergruppe entfernen
				{
					$gid = $_GET["rmu"]; 
					$sql = "DELETE FROM gruppen_user WHERE user='".\Main\DB::escape($uid)."' AND gruppe='".\Main\DB::escape($gid)."'"; 
					\Main\DB::query($sql); 
				}
				
				if(isset($_GET["add"])) // Benutzergruppe hinzufügen 
				{
					$gid = $_POST["add_ug"]; 
					if($gid != "none") 
					{
						\Main\DB::insert("gruppen_user", array(
							"gruppe" => $gid,
							"user" => $uid
						));
					}
				}
				
				$from = \Main\Language::$txt["infos"]["ugs_from"]; 
				$from = str_replace("[user]", htmlspecialchars($uData["username"]), $from); 
				$html .= "<h3>$from</h3><br />"; 
				
				$html .= "<div class='panel panel-primary group-memberlist'><div class='panel-body'>"; 
				$rst = \Main\DB::select("gruppen_user", "id, gruppe", "user='".\Main\DB::escape($uid)."'");
				while($row = $rst->fetch_object())
				{
					$guid = $row->id; 
					$gid = $row->gruppe; 
					$data = \Main\Admin\Control::getUsergroupData($gid); 
					if(isset($data["name"]))
					{
						$html .= "<div>
							<div class='right'><a href='index.php?page=users&up=list&ugroups=$uid&rma=$gid'><i class='fa fa-times fa-lg'></i></a></div>
							".htmlspecialchars($data["name"])."
						</div>"; 
					}
				}
				$html .= "</div></div>
				<br /><br />
				<h4>".\Main\Language::$txt["infos"]["ug_add"]."</h4>
				<form action='index.php?page=users&up=list&ugroups=$uid&add' method='post'>
					<select name='add_ug' class='form-control'>
						<option value='none'>".\Main\Language::$txt["infos"]["ug_choose"]."</option>"; 
						$rst = \Main\DB::select("gruppen", "id, name");
						while($row = $rst->fetch_object())
						{
							$gid = $row->id; 
							$gname = $row->name; 
							if(!\Main\Admin\Control::isUserInGroup($uid, $gid)) 
							{
								$html .= "<option value='$gid'>".htmlspecialchars($gname, ENT_QUOTES)."</option>"; 
							}
						}
					$html .= "</select>
					<button class='btn btn-primary button-margin-top'>".\Main\Language::$txt["buttons"]["add"]."</button>
				</form>"; 
				
				$html .= "</div></div>";
				return $html; 
			}
		
			$pageNo = 1; 
			if(isset($_GET["pageNo"]))
				$pageNo = $_GET["pageNo"]; 
				
			$rst = \Main\DB::select("accounts", "id");
			$max_users = $rst->num_rows; 
			
			$max_per_page = 20; 
			
			$max_pages = ceil($max_users / $max_per_page);
			
			if($pageNo < 1) $pageNo = 1; 
			if($pageNo > $max_pages) $pageNo = $max_pages; 
			
			$startlimit = ($pageNo * $max_per_page) - $max_per_page; 
			$limit = $startlimit.", ".$max_per_page; 
				
			if($max_pages > 1) 
			{
				$html .= "<ul class='pagination'>";
				for($i = 1; $i <= $max_pages; $i++) 
				{
					$html .= "<li"; 
					if($pageNo == $i) $html .= " class='active'";
					$html .= "><a href='index.php?page=users&up=list&pageNo=$i'>$i</a></li>"; 
				}
				$html .= "</ul>"; 
			}
			
			$html .= "<div class='users_list'>";
			$rst = \Main\DB::select("accounts", "id,username,vorname,nachname", null, $limit, "id");
			while($row = $rst->fetch_object())
			{
				$acc = array(
					"id" => $row->id,
					"username" => $row->username,
					"vorname" => $row->vorname,
					"nachname" => $row->nachname
				);
				
				$full_name = $acc["vorname"] . " " . $acc["nachname"]; 
				$full_name = rtrim(ltrim($full_name));
				
				if($full_name != "") $full_name = " ($full_name)"; 
				
				$html .= "<div class='item'>";
					$html .= "<div class='row'>";
						$html .= "<div class='col-md-1'>#".$acc["id"]."</div>";
						$html .= "<div class='col-md-7'>".htmlspecialchars($acc["username"].$full_name)."</div>";
						$html .= "<div class='col-md-4'>";
							$html .= "<a href='index.php?page=users&up=list&ugroups=".$acc["id"]."'><i class='fa fa-users fa-lg'></i></a>&nbsp;"; 
							$html .= "<!--a><i class='fa fa-pencil fa-lg'></i></a--> ";
							$html .= "<a href='#' onclick='showDelUser(".$acc["id"].", ".$pageNo.");'><i class='fa fa-times fa-lg'></i></a> ";
						$html .= "</div>"; 
					$html .= "</div>"; 
				$html .= "</div>"; 
			}
			$html .= "</div>"; 
			
			if($max_pages > 1) 
			{
				$html .= "<ul class='pagination'>";
				for($i = 1; $i <= $max_pages; $i++) 
				{
					$html .= "<li"; 
					if($pageNo == $i) $html .= " class='active'";
					$html .= "><a href='index.php?page=users&up=list&pageNo=$i'>$i</a></li>"; 
				}
				$html .= "</ul>"; 
			}
		}
		else if($up == "add") // Benutzer hinzufügen / bearbeiten 
		{
			$html .= "<h3>".\Main\Language::$txt["infos"]["users_add_header"]."</h3>".\Main\Language::$txt["infos"]["users_add_desc"]."<br /><br />";
			$input = array(
				"username" => "",
				"vorname" => "",
				"nachname" => "",
				"mail" => ""
			);
			if(isset($_POST["username"]) && !isset($_GET["save"])) // Neuen Benutzer speichern
			{
				$input = array(
					"username" => $_POST["username"],
					"vorname" => $_POST["vorname"], 
					"nachname" => $_POST["nachname"],
					"mail" => $_POST["mail"],
					"passwort" => "",
					"registerTime" => time()
				); 
				
				$pw1 = $_POST["password1"]; 
				$pw2 = $_POST["password2"]; 
				
				$rst = \Main\DB::select("accounts", "id", "username='".\Main\DB::escape($input["username"])."' OR mail='".\Main\DB::escape($input["mail"])."'");
				if($rst->num_rows > 0) 
				{
					$html .= "<div class='alert alert-danger'>".\Main\Language::$txt["alerts"]["users_add_exists"]."</div>";
				}
				else
				{
					if($pw1 != $pw2 || strlen($pw1) < 5) 
					{
						$html .= "<div class='alert alert-danger'>".\Main\Language::$txt["alerts"]["users_add_pw_wrong"]."</div>";
					}
					else
					{
						$input["passwort"] = $pw1; 
						\Main\DB::insert("accounts", $input); 
						$html .= "<div class='alert alert-success'>".\Main\Language::$txt["alerts"]["users_add_success"]."</div>";
					}
				}
			}
		
			$html .= "<form action='index.php?page=users&up=add' method='post'>
				<input type='text' class='form-control' name='username' placeholder='".\Main\Language::$txt["forms"]["users_add_username"]."' value='".htmlspecialchars($input["username"], ENT_QUOTES)."' /> 
				<input type='text' class='form-control form-margin-top-low' name='vorname' placeholder='".\Main\Language::$txt["forms"]["users_add_first-name"]."' value='".htmlspecialchars($input["vorname"], ENT_QUOTES)."' /> 
				<input type='text' class='form-control form-margin-top-low' name='nachname' placeholder='".\Main\Language::$txt["forms"]["users_add_second-name"]."' value='".htmlspecialchars($input["nachname"], ENT_QUOTES)."' /> 
				<input type='text' class='form-control form-margin-top-low' name='mail' placeholder='".\Main\Language::$txt["forms"]["users_add_mail"]."' value='".htmlspecialchars($input["mail"], ENT_QUOTES)."' /> 
				<input type='password' class='form-control form-margin-top-low' name='password1' placeholder='".\Main\Language::$txt["forms"]["users_add_pw1"]."' /> 
				<input type='password' class='form-control form-margin-top-low' name='password2' placeholder='".\Main\Language::$txt["forms"]["users_add_pw2"]."' /> 
				<button class='btn btn-primary right form-margin-top-low'>".\Main\Language::$txt["buttons"]["save"]."</button>
			</form>"; 
		}
		else if($up == "usergroups") // Benutzergruppen auflisten 
		{
			if(isset($_GET["members"])) // Mitglieder anzeigen 
			{
				$id = $_GET["members"]; 
				$data = \Main\Admin\Control::getUsergroupData($id); 
				if(!isset($data["name"])) // Prüfen ob die Gruppe existiert! 
				{
					$html .= "<div class='alert alert-danger'>".\Main\Language::$txt["alerts"]["ug_doesnt_exist"]."</div>";
					$html .= "</div></div>"; 
					return $html; 
				}
				
				if(isset($_GET["add"])) // Mitglied hinzufügen 
				{
					$uid = $_POST["new_member"]; 
					if($uid != "none") 
					{
						\Main\DB::insert("gruppen_user", array(
							"user" => $uid,
							"gruppe" => $id
						));
						$html .= "<div class='alert alert-success'>".\Main\Language::$txt["alerts"]["ug_add_member_success"]."</div>"; 
					}
				}
				
				if(isset($_GET["rma"])) // Fragen ob der Benutzer wirklich raus geworfen werden soll! 
				{
					$uData = \Main\User\Control::getUserData($_GET["rma"]); 
					$quest = \Main\Language::$txt["infos"]["kick_user_from_group"]; 
					$quest = str_replace("[user]", htmlspecialchars($uData["username"]), $quest);
					$quest = str_replace("[group]", htmlspecialchars($data["name"]), $quest);
					$html .= "<strong>$quest</strong><br />
							<a href='index.php?page=users&up=usergroups&members=$id&rmu=".$uData["id"]."'><button class='btn btn-success'>".\Main\Language::$txt["buttons"]["yes"]."</button></a> &nbsp; <a href='index.php?page=users&up=usergroups&members=$id'><button class='btn btn-danger'>".\Main\Language::$txt["buttons"]["no"]."</button></a><br /><br />"; 
				}
				
				if(isset($_GET["rmu"])) // Benutzer rauswerfen
				{
					$uid = $_GET["rmu"]; 
					$sql = "DELETE FROM gruppen_user WHERE user='".\Main\DB::escape($uid)."' AND gruppe='".\Main\DB::escape($id)."'"; 
					\Main\DB::query($sql); 
					$html .= "<div class='alert alert-success'>".\Main\Language::$txt["alerts"]["ug_kicked_user_success"]."</div>"; 
				}
				
				$html .= "<h3>".\Main\Language::$txt["infos"]["members_from"]." ".htmlspecialchars($data["name"])."</h3>"; 
				
				$html .= "<br /><br />"; 
				$html .= "<div class='panel panel-primary group-memberlist'>";
					$html .= "<div class='panel-body'>"; 
						$rst = \Main\DB::select("gruppen_user", "id, user", "gruppe='".\Main\DB::escape($id)."'");
						while($row = $rst->fetch_object())
						{
							if(!\Main\User\Control::existsUserId($row->user) && $row->user != 0) 
							{
								\Main\DB::delete("gruppen_user", $row->id);
								continue; 
							}
							$uData = \Main\User\Control::getUserData($row->user);
							if($row->user == 0) $uData["username"] = \Main\Language::$txt["infos"]["users_usergroups_unregistered_users"];  
							$html .= "<div>";
								$html .= "<div class='right'><a href='index.php?page=users&up=usergroups&members=$id&rma=".$uData["id"]."'><i class='fa fa-times fa-lg'></i></a></div>"; 
								$html .= htmlspecialchars($uData["username"]);
							$html .= "</div>"; 
						}
					$html .= "</div>"; 
				$html .= "</div>"; 
				
				$html .= "<br /><br />
				<h4>".\Main\Language::$txt["infos"]["ug_add_member"]."</h4>
				<form action='index.php?page=users&up=usergroups&members=$id&add' method='post'>
					<select class='form-control' name='new_member'>
						<option value='none'>".\Main\Language::$txt["forms"]["ug_choose_user"]."</option>"; 
						$html .= "<option value='0'>".\Main\Language::$txt["infos"]["users_usergroups_unregistered_users"]."</option>";
						$rst = \Main\DB::select("accounts", "id, username", null, null, "id DESC");
						while($row = $rst->fetch_object())
						{
							$uid = $row->id; 
							$uname = $row->username; 
							
							if(!\Main\Admin\Control::isUserInGroup($uid, $id)) 
							{
								$html .= "<option value='$uid'>".htmlspecialchars($uname, ENT_QUOTES)."</option>"; 
							}
						}
					$html .= "</select>
					<p><button class='btn btn-primary button-margin-top'>".\Main\Language::$txt["buttons"]["add"]."</button></p>
				</form>";
				
				$html .= "</div></div>"; // Gesamtdivs abschließen für Return! 
				return $html; 
			}
		
			$html .= "<h3>".\Main\Language::$txt["infos"]["users_usergroups_header"]."</h3>"; 
			
			// Umblätterfunktion berechnen 
			$rst = \Main\DB::select("gruppen", "id");
			$anz_gruppen = $rst->num_rows; 
			$max_pro_seite = 15; 
			
			$anz_seiten = ceil($anz_gruppen / $max_pro_seite); 
			$pageNo = 1; 
			if(isset($_GET["pageNo"])) $pageNo = $_GET["pageNo"]; 
			if($pageNo < 1) $pageNo = 1; 
			if($pageNo > $anz_seiten) $pageNo = $anz_seiten; 
			
			$startlimit = ($pageNo * $max_pro_seite) - $max_pro_seite; 
			$limit = $startlimit . ", " . $max_pro_seite; 
			
			if($anz_seiten > 1) // Navigation 
			{
				$html .= "<div class='clear'></div>";
				$html .= "<ul class='pagination'>"; 
					for($i = 1; $i <= $anz_seiten; $i++) 
					{
						$html .= "<li";
						if($pageNo == $i) $html .= " class='active'";
						$html .= "><a href='index.php?page=users&up=usergroups&pageNo=$i'>$i</a></li>"; 
					}
				$html .= "</ul>"; 
				$html .= "<div class='clear'></div>";
			}
			
			$rst = \Main\DB::select("gruppen", "id, name", null, $limit, "id");
			
			$html .= "<div class='panel panel-primary usergroups-list'>";
			$html .= "<div class='panel-heading usergroups-header'>".\Main\Language::$txt["infos"]["ug_list_header"]."</div>"; 
			$html .= "<div class='panel-body usergroups-body'>"; 
			
			while($row = $rst->fetch_object())
			{
				$gruppe = array("id" => $row->id,
								"name" => $row->name);
				$html .= "<div>";
					$html .= htmlspecialchars($gruppe["name"]);
					$html .= "<div class='right'>
						<a href='index.php?page=users&up=usergroups&members=".$gruppe["id"]."'><i class='fa fa-users fa-lg'></i></a>&nbsp;
						<a href='index.php?page=users&up=usergroup_add&edit=".$gruppe["id"]."'><i class='fa fa-pencil fa-lg'></i></a>&nbsp;
						<a href='#' onclick='showDelUserGroup(".$gruppe["id"].");'><i class='fa fa-times fa-lg'></i></a>
					</div>	"; 
				$html .= "</div>"; 
			}
			
			$html .= "</div>"; 
			$html .= "</div>"; 
			
			if($anz_seiten > 1) // Navigation 
			{
				$html .= "<div class='clear'></div>";
				$html .= "<ul class='pagination'>"; 
					for($i = 1; $i <= $anz_seiten; $i++) 
					{
						$html .= "<li";
						if($pageNo == $i) $html .= " class='active'";
						$html .= "><a href='index.php?page=users&up=usergroups&pageNo=$i'>$i</a></li>"; 
					}
				$html .= "</ul>"; 
				$html .= "<div class='clear'></div>";
			}
		}
		else if($up == "usergroup_add") // Benutzergruppe hinzufügen 
		{
			if(isset($_GET["save"], $_POST["name"]) && !isset($_POST["id"])) // Neue Gruppe erstellen 
			{
				$name = $_POST["name"]; 
				$enter_administration = $_POST["enter_administration"]; 
				$edit_themes = $_POST["edit_themes"]; 
				$move_themes = $_POST["move_themes"]; 
				$close_themes = $_POST["close_themes"]; 
				$tag_themes = $_POST["tag_themes"]; 
				$edit_posts = $_POST["edit_posts"]; 
				$del_posts = $_POST["del_posts"]; 
				$autogain = $_POST["autogain"]; 
				\Main\Admin\Control::createUsergroup($name, $enter_administration, $edit_themes, $move_themes, $close_themes, $tag_themes, $edit_posts, $del_posts, $autogain);
				$html .= "<div class='alert alert-success'>".\Main\Language::$txt["alerts"]["ug_new_success"]."</div>"; 
			}
			if(isset($_GET["save"], $_POST["name"], $_POST["id"])) // Gruppe speichern
			{
				$id = $_POST["id"]; 
				$name = $_POST["name"]; 
				$enter_administration = $_POST["enter_administration"]; 
				$edit_themes = $_POST["edit_themes"]; 
				$move_themes = $_POST["move_themes"]; 
				$close_themes = $_POST["close_themes"]; 
				$tag_themes = $_POST["tag_themes"]; 
				$edit_posts = $_POST["edit_posts"]; 
				$del_posts = $_POST["del_posts"]; 
				$autogain = $_POST["autogain"]; 
				\Main\Admin\Control::saveUsergroup($id, $name, $enter_administration, $edit_themes, $move_themes, $close_themes, $tag_themes, $edit_posts, $del_posts, $autogain); 
				$html .= "<div class='alert alert-success'>".\Main\Language::$txt["alerts"]["ug_save_success"]."</div>"; 
			}
			
			$name = ""; 
			$ag_0 = "checked";   $ag_1 = ""; 
			$ea_0 = "checked";   $ea_1 = ""; 
			$et_0 = "checked";   $et_1 = ""; 
			$mt_0 = "checked";   $mt_1 = ""; 
			$ct_0 = "checked";   $ct_1 = ""; 
			$tt_0 = "checked";   $tt_1 = ""; 
			$ep_0 = "checked";   $ep_1 = ""; 
			$dp_0 = "checked";   $dp_1 = ""; 
			
			$string = ""; 
			if(isset($_GET["edit"]))
			{
				$array = \Main\Admin\Control::getUsergroupData($_GET["edit"]); 
				if(isset($array["name"]))
				{
					$string = "<input type='hidden' name='id' value='".$_GET["edit"]."' />"; 
					$name = $array["name"]; 
					if($array["autogain"] == 1) { $ag_0 = ""; $ag_1 = "checked"; }
					if($array["enter_administration"] == 1) { $ea_0 = ""; $ea_1 = "checked"; }
					if($array["edit_themes"] == 1) { $et_0 = ""; $et_1 = "checked"; }
					if($array["move_themes"] == 1) { $mt_0 = ""; $mt_1 = "checked"; }
					if($array["close_themes"] == 1) { $ct_0 = ""; $ct_1 = "checked"; }
					if($array["tag_themes"] == 1) { $tt_0 = ""; $tt_1 = "checked"; }
					if($array["edit_posts"] == 1) { $ep_0 = ""; $ep_1 = "checked"; }
					if($array["del_posts"] == 1) { $dp_0 = ""; $dp_1 = "checked"; }
				}
			}
			
			$html .= "<h3>".\Main\Language::$txt["infos"]["users_usergroups_add_header"]."</h3><p>&nbsp;</p>"; 
			
			$html .= "<form action='index.php?page=users&up=usergroup_add&save' method='post'>
				$string
				<input type='text' class='form-control' name='name' placeholder='".htmlspecialchars(\Main\Language::$txt["forms"]["ug_name"], ENT_QUOTES)."' value='".htmlspecialchars($name, ENT_QUOTES)."' /><br />
				<strong>".\Main\Language::$txt["forms"]["ug_autogain"]."</strong><br />
				<input type='radio' name='autogain' value='0' id='ag_0' $ag_0 /> <label for='ag_0'>".\Main\Language::$txt["infos"]["unactivated"]."</label> &nbsp; 
				<input type='radio' name='autogain' value='1' id='ag_1' $ag_1 /> <label for='ag_1'>".\Main\Language::$txt["infos"]["activated"]."</label>
				<hr />
				<h4 style='margin-top:0;margin-bottom:22px; '>".\Main\Language::$txt["forms"]["ug_adminrights"]."</h4>
				<strong>".\Main\Language::$txt["forms"]["ug_enter_admin"]."</strong><br />
				<input type='radio' name='enter_administration' id='enter_administration_0' value='0' $ea_0 /> <label for='enter_administration_0'>".\Main\Language::$txt["infos"]["unactivated"]."</label> &nbsp; 
				<input type='radio' name='enter_administration' id='enter_administration_1' value='1' $ea_1 /> <label for='enter_administration_1'>".\Main\Language::$txt["infos"]["activated"]."</label>
				<br /><br />
				<strong>".\Main\Language::$txt["forms"]["ug_edit_themes"]."</strong><br />
				<input type='radio' name='edit_themes' value='0' id='et_0' $et_0 /> <label for='et_0'>".\Main\Language::$txt["infos"]["unactivated"]."</label> &nbsp; 
				<input type='radio' name='edit_themes' value='1' id='et_1' $et_1 /> <label for='et_1'>".\Main\Language::$txt["infos"]["activated"]."</label>
				<br /><br />
				<strong>".\Main\Language::$txt["forms"]["ug_move_themes"]."</strong><br />
				<input type='radio' name='move_themes' value='0' id='mt_0' $mt_0 /> <label for='mt_0'>".\Main\Language::$txt["infos"]["unactivated"]."</label> &nbsp; 
				<input type='radio' name='move_themes' value='1' id='mt_1' $mt_1 /> <label for='mt_1'>".\Main\Language::$txt["infos"]["activated"]."</label>
				<br /><br />
				<strong>".\Main\Language::$txt["forms"]["ug_close_themes"]."</strong><br />
				<input type='radio' name='close_themes' value='0' id='ct_0' $ct_0 /> <label for='ct_0'>".\Main\Language::$txt["infos"]["unactivated"]."</label> &nbsp; 
				<input type='radio' name='close_themes' value='1' id='ct_1' $ct_1 /> <label for='ct_1'>".\Main\Language::$txt["infos"]["activated"]."</label>
				<br /><br />
				<strong>".\Main\Language::$txt["forms"]["ug_tag_themes"]."</strong><br />
				<input type='radio' name='tag_themes' value='0' id='tt_0' $tt_0 /> <label for='tt_0'>".\Main\Language::$txt["infos"]["unactivated"]."</label> &nbsp; 
				<input type='radio' name='tag_themes' value='1' id='tt_1' $tt_1 /> <label for='tt_1'>".\Main\Language::$txt["infos"]["activated"]."</label> 
				<br /><br />
				<strong>".\Main\Language::$txt["forms"]["ug_edit_posts"]."</strong><br />
				<input type='radio' name='edit_posts' value='0' id='ep_0' $ep_0 /> <label for='ep_0'>".\Main\Language::$txt["infos"]["unactivated"]."</label> &nbsp; 
				<input type='radio' name='edit_posts' value='1' id='ep_1' $ep_1 /> <label for='ep_1'>".\Main\Language::$txt["infos"]["activated"]."</label>
				<br /><br />
				<strong>".\Main\Language::$txt["forms"]["ug_del_posts"]."</strong><br />
				<input type='radio' name='del_posts' value='0' id='dp_0' $dp_0 /> <label for='dp_0'>".\Main\Language::$txt["infos"]["unactivated"]."</label> &nbsp; 
				<input type='radio' name='del_posts' value='1' id='dp_1' $dp_1 /> <label for='dp_1'>".\Main\Language::$txt["infos"]["activated"]."</label>
				<br /><br />
				<button class='btn btn-primary right'>".\Main\Language::$txt["buttons"]["save"]."</button>
			</form>"; 
			
			/*
			id
			name
			enter_administration 
			edit_themes 	
			move_themes 		
			close_themes 	
			tag_themes 
			edit_posts 	
			del_posts 	
			autogain*/
		}
		else if($up == "userranks") // Benutzerränge auflisten
		{
			$showranks = 1; 

			if($showranks) // Alle ränge auflisten 
			{ // Todo: link zum Bearbeiten eines Rangs 

				if(isset($_GET["del"]))
				{
					\Main\DB::delete("user_ranks", $_GET["del"]); 
				}

				$html .= "<h3>".\Main\Language::$txt["infos"]["users_userranks_header"]."</h3>";

				$html .= "<div class='panel panel-primary panel-userranks'><div class='panel-heading'>".\Main\Language::$txt["infos"]["users_userranks_header"]."</div>
				<div class='panel-body'>";

				$rst = \Main\DB::select("user_ranks", "id, name, priority", null, null, "priority, id");
				while($row = $rst->fetch_object())
				{
					$rank = array("id" => $row->id,
						"name" => $row->name,
						"priority" => $row->priority
						);
					$html .= "<div>";
						$html .= "<div class='right textalignright'><a href='index.php?page=users&up=userranks&del=".$rank["id"]."'><i class='fa fa-times fa-lg'></i></a></div>";
						$html .= "<div class='right'>".$rank["priority"]."</div>";
						$html .= "<a href='index.php?page=users&up=userrank_add&edit=".$rank["id"]."'>" . htmlspecialchars($rank["name"]) . "</a>";
					$html .= "</div>";
				}

				$html .= "</div></div>";

			}
		}
		else if($up == "userrank_add") // Benutzerrang hinzufügen TODO
		{
			$showadd = 1; 

			if(isset($_GET["edit"])) // Benutzerrang bearbeiten
			{
				$rank_id = $_GET["edit"]; 

				if(isset($_GET["save"])) // Bearbeiten speichern
				{
					$input = array("name" => $_POST["name"],
						"priority" => $_POST["priority"],
						"show_picture" => $_POST["show_picture"],
						"bgcol" => $_POST["bgcol"],
						"textcol" => $_POST["textcol"]
						);
					
					if(isset($_FILES["picture"]))
					{
						if($_FILES["picture"]["size"] > 0) 
						{
							$upload_dir = "../media/upload/";
							$dbdir = "media/upload/";

							$uploadfile = $upload_dir . basename($_FILES["picture"]["name"]);
							$dbfile = $dbdir . basename($_FILES["picture"]["name"]);

							$i = 1; 
							while(file_exists($uploadfile)) 
							{
								$uploadfile = $upload_dir . $i . basename($_FILES["picture"]["name"]);
								$dbfile = $dbdir . $i . basename($_FILES["picture"]["name"]);
								$i++; 
							}

							if(move_uploaded_file($_FILES["picture"]["tmp_name"], $uploadfile))
							{
								$input["picture"] = $dbfile; 
							}
						}
					}

					\Main\DB::update("user_ranks", $rank_id, $input);
					$html .= "<div class='alert alert-success'>".\Main\Language::$txt["alerts"]["ur_edit_success"]."</div>";
				}

				$rst = \Main\DB::select("user_ranks", "name, priority, show_picture, picture, bgcol, textcol", "id='".\Main\DB::escape($rank_id)."'");
				if($rst->num_rows > 0) 
				{
					$showadd = 0; 
					$row = $rst->fetch_object(); 

					$rank = array("id" => $rank_id,
						"name" => $row->name,
						"priority" => $row->priority,
						"use_picture" => $row->show_picture,
						"picture" => $row->picture,
						"bgcol" => $row->bgcol,
						"textcol" => $row->textcol
						);

					$tcheck = ""; 
					$pcheck = ""; 
					if($rank["use_picture"] == 1) 
						$pcheck = "checked"; 
					else
						$tcheck = "checked"; 

					if($rank["picture"] != "") 
						$rank["picture"] = "../" . $rank["picture"];

					$html .= "<div class='right'><a href='index.php?page=users&up=userrank_add&editrules=$rank_id'><i class='fa fa-bars fa-lg'></i></a></div>"; 

					$html .= "<h3>".\Main\Language::$txt["infos"]["users_userrank_edit_header"]."</h3><br />";

					$html .= "<form action='index.php?page=users&up=userrank_add&edit=$rank_id&save' method='post' enctype='multipart/form-data'>";

						$html .= "<p><input type='text' name='name' placeholder='".\Main\Language::$txt["forms"]["ur_name"]."' value='".htmlspecialchars($rank["name"], ENT_QUOTES)."' class='form-control' /></p>";
						$html .= "<p><input type='number' name='priority' value='".$rank["priority"]."' class='form-control' /></p>";
						$html .= "<p><input type='radio' name='show_picture' value='0' $tcheck /> ".\Main\Language::$txt["forms"]["ur_use_text"]." &nbsp; 
							   		 <input type='radio' name='show_picture' value='1' $pcheck /> ".\Main\Language::$txt["forms"]["ur_use_picture"]."</p>"; 

						if($rank["picture"] != "") 
						{
							$html .= "<img src='".htmlspecialchars($rank["picture"], ENT_QUOTES)."' style='max-width:60%;' />";
						}

						$html .= "<p>".\Main\Language::$txt["forms"]["ur_choose_picture"]." <input type='file' name='picture' /></p>"; 
						$html .= "<p>".\Main\Language::$txt["forms"]["ur_choose_bgcol"]." <input name='bgcol' class='form-control jscolor' value='".$rank["bgcol"]."' /></p>";
						$html .= "<p>".\Main\Language::$txt["forms"]["ur_choose_textcol"]." <input name='textcol' class='form-control jscolor' value='".$rank["textcol"]."' /></p>"; 
						$html .= "<p><button class='btn btn-primary right'>".\Main\Language::$txt["buttons"]["save"]."</button></p>";
					$html .= "</form>"; 
				}
			}

			if(isset($_GET["editrules"])) // Benutzerrang Regeln bearbeiten
			{
				$rank_id = $_GET["editrules"]; 

				if(isset($_GET["addrule"])) // Eine Regel hinzufügen
				{
					$rule_typ = $_POST["rule_typ"]; 
					if($rule_typ == 0) // Auto
					{
						\Main\DB::insert("user_ranks_gains", array("rank" => $rank_id));
					}
					else if($rule_typ == 1 && $_POST["posts"] > 0) // Posts
					{
						\Main\DB::insert("user_ranks_gains", array("rank" => $rank_id, "gain_posts" => $_POST["posts"]));
					}
					else if($rule_typ == 2 && $_POST["group"] != "none") // Gruppe 
					{
						\Main\DB::insert("user_ranks_gains", array("rank" => $rank_id, "gain_group" => $_POST["group"]));
					}
				}

				if(isset($_GET["delrule"])) // Eine Regel löschen
				{
					$rule_id = $_GET["delrule"]; 
					\Main\DB::delete("user_ranks_gains", $rule_id); 
				}

				$rst = \Main\DB::select("user_ranks", "name", "id='".\Main\DB::escape($rank_id)."'");
				if($rst->num_rows > 0) 
				{
					$showadd = 0; 

					$row = $rst->fetch_object(); 
					$ur_name = $row->name; 

					$html .= "<h3>".htmlspecialchars($ur_name)."</h3><br />";

					$html .= "<div class='panel panel-primary panel-userrank-rules'><div class='panel-heading'>".\Main\Language::$txt["infos"]["users_userrank_rules_header"]."</div><div class='panel-body'>";

					$rst = \Main\DB::select("user_ranks_gains", "id, gain_posts, gain_group", "rank='".\Main\DB::escape($rank_id)."'");
					if($rst->num_rows == 0) 
					{
						$html .= "<div>".\Main\Language::$txt["infos"]["users_userrank_no_rules_yet"]."</div>";
					}
					else
					{
						while($row = $rst->fetch_object())
						{
							$rule = array("id" => $row->id,
								"gain_posts" => $row->gain_posts,
								"gain_group" => $row->gain_group
								);
							$html .= "<div>";

								$html .= "<div class='right'><a href='index.php?page=users&up=userrank_add&editrules=$rank_id&delrule=".$rule["id"]."'><i class='fa fa-times fa-lg'></i></a></div>";

								if($rule["gain_posts"] == 0 && $rule["gain_group"] == 0) 
								{
									$html .= \Main\Language::$txt["infos"]["users_userrank_rule_auto_membership"]; 
								}
								else if($rule["gain_posts"] != 0) 
								{
									$html .= \Main\Language::$txt["infos"]["users_userrank_rule_posts"] . " <strong>" . $rule["gain_posts"] . "</strong>"; 
								}
								else if($rule["gain_group"] != 0) 
								{
									$html .= \Main\Language::$txt["infos"]["users_userrank_rule_group"] . " <strong>" . htmlspecialchars(\Main\Admin\Control::getUsergroupName($rule["gain_group"])) . "</strong>";
								}

							$html .= "</div>";
						}
					}

					$html .= "</div></div>";

					$html .= "<br /><br />
					<h3>".\Main\Language::$txt["infos"]["users_userrank_add_rule_h"]."</h3>
					<form action='index.php?page=users&up=userrank_add&editrules=$rank_id&addrule' method='post'>
						<input id='memship_auto' type='radio' name='rule_typ' value='0' checked /> <label for='memship_auto'>".\Main\Language::$txt["infos"]["users_userrank_rule_auto_membership"]."</label><br /><br />

						<input id='memship_posts' type='radio' name='rule_typ' value='1' /> <label for='memship_posts'>".\Main\Language::$txt["infos"]["users_userrank_memship_with_x_posts"]."</label><br />
						<input type='number' name='posts' value='1' class='form-control' /> <br /><br />

						<input id='memship_group' type='radio' name='rule_typ' value='2' /> <label for='memship_group'>".\Main\Language::$txt["infos"]["users_userrank_memship_with_group"]."</label><br />
						<select name='group' class='form-control'>
							<option value='none'>".\Main\Language::$txt["infos"]["users_userrank_memship_choose_group"]."</option>"; 
							$rst = \Main\DB::select("gruppen", "id, name", null, null, "id");
							if($rst->num_rows > 0) 
							{
								while($row = $rst->fetch_object())
								{
									$group = array("id" => $row->id,
										"name" => $row->name);
									$html .= "<option value='".$group["id"]."'>".htmlspecialchars($group["name"])."</option>";
								}
							}
						$html .= "</select><br />
						<button class='btn btn-primary right'>".\Main\Language::$txt["buttons"]["add"]."</button>
					</form>";
				}
			}

			if(isset($_GET["add"])) // Den Rang hinzufügen 
			{
				$input = array("name" => $_POST["name"],
					"priority" => $_POST["priority"],
					"show_picture" => $_POST["show_picture"],
					"bgcol" => $_POST["bgcol"],
					"textcol" => $_POST["textcol"],
					"picture" => ""
					);

				if(isset($_FILES["picture"]))
				{
					if($_FILES["picture"]["size"] > 0) 
					{
						$upload_dir = "../media/upload/";
						$db_dir = "media/upload/"; 

						$uploadfile = $upload_dir . basename($_FILES["picture"]["name"]); 
						$dbfile = $db_dir . basename($_FILES["picture"]["name"]);

						$i = 1; 
						while(file_exists($uploadfile)) 
						{
							$uploadfile = $upload_dir . $i . basename($_FILES["picture"]["name"]);
							$dbfile = $db_dir . $i . basename($_FILES["picture"]["name"]);
							$i++;
						}

						if(move_uploaded_file($_FILES["picture"]["tmp_name"], $uploadfile))
						{
							$input["picture"] = $dbfile; 
							//$html .= "<div class='alert alert-success'>Bild hochgeladen! </div>";
						}
					}
				}

				if($input["picture"] == "" && $input["show_picture"] == 1) 
				{
					$input["show_picture"] = 0; 
				}

				\Main\DB::insert("user_ranks", $input);
				$html .= "<div class='alert alert-success'>".\Main\Language::$txt["alerts"]["ur_new_success"]."</div>";

			}

			if($showadd) 
			{
				$html .= "<h3>".\Main\Language::$txt["infos"]["users_userrank_add_header"]."</h3>";
				$html .= "<br />";

				$html .= "<form action='index.php?page=users&up=userrank_add&add' method='post' enctype='multipart/form-data'>
					<p><input type='text' name='name' placeholder='".\Main\Language::$txt["forms"]["ur_name"]."' class='form-control' /></p>
					<p><input type='number' name='priority' value='0' class='form-control' /></p>
					<p><input type='radio' name='show_picture' value='0' checked /> ".\Main\Language::$txt["forms"]["ur_use_text"]." &nbsp; 
					   <input type='radio' name='show_picture' value='1' /> ".\Main\Language::$txt["forms"]["ur_use_picture"]." </p>
					<p>".\Main\Language::$txt["forms"]["ur_choose_picture"]." <input type='file' name='picture' /></p>
					<p>".\Main\Language::$txt["forms"]["ur_choose_bgcol"]." <input name='bgcol' class='form-control jscolor' value='666' /></p>
					<p>".\Main\Language::$txt["forms"]["ur_choose_textcol"]." <input name='textcol' class='form-control jscolor' value='fff' /></p>
					<p><button class='btn btn-primary right'>".\Main\Language::$txt["buttons"]["add"]."</button></p>
				</form>";
			}
		}
		$html .= "</div></div>"; 
		
		return $html; 
	}
	
	private static function showPluginsLeft()
	{
		$html = ""; 
		
		$list_active = ""; 
		$add_active = ""; 
		
		if(isset($_GET["up"]))
		{
			if($_GET["up"] == "list") $list_active = "active"; 
			else if($_GET["up"] == "add") $add_active = "active"; 
		} else $list_active = "active"; 
		
		$html .= "<div class='panel panel-primary panel-left'>";
			$html .= "<div class='panel-heading'>".\Main\Language::$txt["infos"]["plugins_left_header"]."</div>";
			$html .= "<div class='panel-body'>"; 
				$html .= "<ul class='nav left_menue_ul'>
					<li class='$list_active'><a href='index.php?page=plugins&up=list'>".\Main\Language::$txt["infos"]["plugins_left_list"]."</a></li>
					<li class='$add_active'><a href='index.php?page=plugins&up=add'>".\Main\Language::$txt["infos"]["plugins_left_add"]."</a></li>"; 
					$html .= \Main\Plugins::hook("AdminView.showPluginsLeft.addMenueItem", "");
				$html .= "</ul>"; 
			$html .= "</div>"; 
		$html .= "</div>"; 
		
		return $html; 
	}
	
	private static function showPlugins() 
	{
		function showList() 
		{
			$html = ""; 
			
			if(isset($_GET["ua"])) //unactive
			{
				\Main\DB::update("plugins", $_GET["ua"], array("active" => 0));
			}
			if(isset($_GET["aa"])) //active
			{
				\Main\DB::update("plugins", $_GET["aa"], array("active" => 1));
			}
			if(isset($_GET["adel"])) // Löschen Fragen 
			{
				$rst = \Main\DB::select("plugins", "name", "id='".\Main\DB::escape($_GET["adel"])."'");
				$row = $rst->fetch_object(); 
				$adelname = $row->name; 
				$string = \Main\Language::$txt["infos"]["plugin_rly_del?"]; 
				$string = str_replace("[plugin]", htmlspecialchars($adelname), $string); 
				$html .= "<strong>$string</strong><br />
				<a href='index.php?page=plugins&up=list&del=".$_GET["adel"]."'><button class='btn btn-success'>".\Main\Language::$txt["buttons"]["yes"]."</button></a> &nbsp; <a href='index.php?page=plugins&up=list'><button class='btn btn-danger'>".\Main\Language::$txt["buttons"]["no"]."</button></a>
				<br /><br />"; 
			}
			if(isset($_GET["del"])) // Löschen
			{
				$rst = \Main\DB::select("plugins", "foldername", "id='".\Main\DB::escape($_GET["del"])."'");
				$row = $rst->fetch_object(); 
				$fname = $row->foldername; 
				$dir = "../data/plugins/".$fname;
				if(is_dir($dir)) 
				{
					\Main\rrmdir($dir); 
				}
				\Main\DB::delete("plugins", $_GET["del"]);
			}
			
			
			$html .= "<h3>".\Main\Language::$txt["infos"]["plugins_list_header"]."</h3><br />"; 
			
			$html .= "<div class='panel panel-primary panel-plugins'><div class='panel-heading'>".\Main\Language::$txt["infos"]["plugins"]."</div><div class='panel-body'>";
			
			$rst = \Main\DB::select("plugins", "id, name, foldername, author, active", null, null, "id");
			while($row = $rst->fetch_object())
			{
				$plugin = array("id" => $row->id,
						"name" => $row->name,
						"foldername" => $row->foldername,
						"author" => $row->author,
						"active" => $row->active);
						
				$html .= "<div>";
					
					$html .= "<div class='right'>";
						// Activitätsanzeige
						if($plugin["active"] == 1) $html .= "<a href='index.php?page=plugins&up=list&ua=".$plugin["id"]."'><i class='fa fa-circle fa-lg circle-green'></i></a>"; 
						else $html .= "<a href='index.php?page=plugins&up=list&aa=".$plugin["id"]."'><i class='fa fa-circle fa-lg circle-grey'></i></a>"; 
						$html .= "&nbsp;"; 
						$html .= "<a href='index.php?page=plugins&up=list&adel=".$plugin["id"]."'><i class='fa fa-times fa-lg'></i></a>";
						
					$html .= "</div>";
					$html .= "<div class='right plugins-mrg-right'><small>".\Main\Language::$txt["infos"]["plugins_folder"]." ".htmlspecialchars($plugin["foldername"])."</small></div>";
					$html .= htmlspecialchars($plugin["name"]); 
					 
				$html .= "</div>"; 
			}
			
			$html .= "</div></div>"; 
			
			return $html; 
		}
		
		function showAdd() 
		{
			$html = ""; 
			
			if(isset($_GET["do"], $_POST["name"])) // Neues hinzufügen! 
			{
				$html .= \Main\Admin\Control::createPlugin($_POST["name"], $_POST["foldername"], $_POST["author"], $_POST["active"]);
			}
			
			$html .= "<h3>".\Main\Language::$txt["infos"]["plugins_add_header"]."</h3><br />"; 
			
			$html .= "<form action='index.php?page=plugins&up=add&do' method='post'>
				<p><input type='text' name='name' placeholder='".\Main\Language::$txt["forms"]["plugins_ph_name"]."' class='form-control' /></p>
				<p><input type='text' name='foldername' placeholder='".\Main\Language::$txt["forms"]["plugins_ph_fname"]."' class='form-control' />
					<small><b>".\Main\Language::$txt["infos"]["plugins_add_folder_info"]."</b></small></p>
				<p><input type='text' name='author' placeholder='".\Main\Language::$txt["forms"]["plugins_ph_author"]."' class='form-control' /></p>
				<p><input type='radio' name='active' value='0' id='unact' checked /> <label for='unact'>".\Main\Language::$txt["infos"]["unactivated"]."</label> &nbsp; 
				<input type='radio' name='active' value='1' id='act' /> <label for='act'>".\Main\Language::$txt["infos"]["activated"]."</label></p>
				<p><button class='btn btn-primary right'>".\Main\Language::$txt["buttons"]["save"]."</button></p>
			</form>"; 
			
			return $html; 
		}
	
		$html = ""; 
		
		$list_active = 0; 
		$add_active = 0; 
		
		if(isset($_GET["up"]))
		{
			if($_GET["up"] == "list") $list_active = 1; 
			else if($_GET["up"] == "add") $add_active = 1; 
		} else $list_active = 1; 
		
		if(isset($_GET["up"])) $up = $_GET["up"]; 
		else $up = "null"; 
		if($up == null || $up == "") $up = "null"; 
		
		$html .= "<div class='panel panel-primary'><div class='panel-body'>";
		
			if($list_active == 1) $html .= showList(); 
			
			if($add_active == 1) $html .= showAdd(); 
			
			if(\Main\Plugins::hook("AdminView.showPlugins.isUp.".$up, "0") == "1") $html .= \Main\Plugins::hook("AdminView.showPlugins.isUp.".$up.".content", ""); 
			
		$html .= "</div></div>"; 
		
		return $html; 
	}

	private static function showDesignsLeft()
	{
		$html = ""; 
		
		$list_active = ""; 
		$add_active = ""; 
		
		if(isset($_GET["up"]))
		{
			if($_GET["up"] == "list") $list_active = "active"; 
			else if($_GET["up"] == "add") $add_active = "active"; 
		} else $list_active = "active"; 
		
		$html .= "<div class='panel panel-primary panel-left'>";
			$html .= "<div class='panel-heading'>".\Main\Language::$txt["infos"]["designs_left_header"]."</div>";
			$html .= "<div class='panel-body'>"; 
				$html .= "<ul class='nav left_menue_ul'>
					<li class='$list_active'><a href='index.php?page=designs&up=list'>".\Main\Language::$txt["infos"]["designs_left_list"]."</a></li>
					<li class='$add_active'><a href='index.php?page=designs&up=add'>".\Main\Language::$txt["infos"]["designs_left_add"]."</a></li>"; 
				$html .= "</ul>"; 
			$html .= "</div>"; 
		$html .= "</div>"; 
		
		return $html; 
	}
	
	private static function showDesigns()
	{
		function showList() 
		{
			$html = ""; 
			
			if(isset($_GET["aa"])) // Set to active
			{
				\Main\DB::update("designs", $_GET["aa"], array("active" => 1));
			}
			if(isset($_GET["ua"])) // Set to unactive
			{
				\Main\DB::update("designs", $_GET["ua"], array("active" => 0, "standard" => 0));
			}
			if(isset($_GET["adel"])) // Ask for DEL Design
			{
				$did = $_GET["adel"]; 
				$rst = \Main\DB::select("designs", "name", "id='".\Main\DB::escape($did)."'");
				$row = $rst->fetch_object(); 
				$dname = $row->name; 
				$string = \Main\Language::$txt["infos"]["design_ask_del"]; 
				$string = str_replace("[design]", htmlspecialchars($dname), $string);
				$html .= "<strong>".$string."</strong><br />
				<a href='index.php?page=designs&up=list&del=$did'><button class='btn btn-success'>".\Main\Language::$txt["buttons"]["yes"]."</button></a> &nbsp; <a href='index.php?page=designs&up=list'><button class='btn btn-danger'>".\Main\Language::$txt["buttons"]["no"]."</button></a>
				<br /><br />"; 
			}
			if(isset($_GET["del"])) // DEL Design
			{
				$rst = \Main\DB::select("designs", "fname", "id='".\Main\DB::escape($_GET["del"])."'");
				$row = $rst->fetch_object(); 
				$fname = $row->fname; 
				$fpos = "../data/designs/$fname";
				if(file_exists($fpos)) 
				{
					unlink($fpos); 
				}
				\Main\DB::delete("designs", $_GET["del"]); 
			}
			if(isset($_GET["sstd"])) // Set Standard Design
			{
				$did = $_GET["sstd"]; 
				$sql = "UPDATE designs SET standard='0'";
				\Main\DB::query($sql); 
				\Main\DB::update("designs", $did, array("standard" => 1));
			}
			
			$html .= "<h3>".\Main\Language::$txt["infos"]["designs_list_header"]."</h3><br />"; 
			
			$html .= "<div class='panel panel-primary panel-plugins'><div class='panel-heading'>".\Main\Language::$txt["infos"]["designs"]."</div><div class='panel-body'>";
				$rst = \Main\DB::select("designs", "id, name, fname, active, autor, standard", null, null, "id");
				while($row = $rst->fetch_object())
				{
					$design = array("id" => $row->id,
									"name" => $row->name,
									"fname" => $row->fname,
									"active" => $row->active,
									"autor" => $row->autor,
									"standard" => $row->standard
									);

					$html .= "<div>";
					
						$html .= "<div class='right'>";
						// Activitätsanzeige
						if($design["standard"] == 1) $html .= "<i class='fa fa-home fa-lg circle-green'></i>"; 
						else $html .= "<a href='index.php?page=designs&up=list&sstd=".$design["id"]."'><i class='fa fa-home fa-lg circle-grey'></i></a>"; 
						$html .= "&nbsp;"; 
						if($design["active"] == 1) $html .= "<a href='index.php?page=designs&up=list&ua=".$design["id"]."'><i class='fa fa-circle fa-lg circle-green'></i></a>"; 
						else $html .= "<a href='index.php?page=designs&up=list&aa=".$design["id"]."'><i class='fa fa-circle fa-lg circle-grey'></i></a>"; 
						$html .= "&nbsp;"; 
						$html .= "<a href='index.php?page=designs&up=list&adel=".$design["id"]."'><i class='fa fa-times fa-lg'></i></a>";
						
					$html .= "</div>";
					$html .= "<div class='right plugins-mrg-right'><small>".\Main\Language::$txt["infos"]["design_filename"]." ".htmlspecialchars($design["fname"])."</small></div>";
						$html .= htmlspecialchars($design["name"]);
					$html .= "</div>"; 
				}
			$html .= "</div></div>"; 
			return $html; 
		}
		
		function showAdd()
		{
			$html = "";
			
			if(isset($_GET["do"], $_POST["name"]))
			{ 
				$html .= \Main\Admin\Control::createDesign($_POST["name"], $_POST["fname"], $_POST["author"], $_POST["footertxt"], $_POST["active"], $_POST["standard"]); 
			}
			
			$html .= "<h3>".\Main\Language::$txt["infos"]["designs_add_header"]."</h3><br />"; 
			
			$html .= "<form action='index.php?page=designs&up=add&do' method='post'>
				<p><input type='text' class='form-control' name='name' placeholder='".\Main\Language::$txt["forms"]["designs_ph_name"]."' /></p>
				<p><input type='text' class='form-control' name='fname' placeholder='".\Main\Language::$txt["forms"]["designs_ph_filename"]."' /></p>
				<p><input type='text' class='form-control' name='author' placeholder='".\Main\Language::$txt["forms"]["designs_ph_author"]."' /></p>
				<p><input type='text' class='form-control' name='footertxt' placeholder='".\Main\Language::$txt["forms"]["designs_ph_footertxt"]."' /></p>
				<p><strong>".\Main\Language::$txt["infos"]["design_add_active"]."</strong><br /><input type='radio' name='active' value='0' id='unact' checked /> <label for='unact'>".\Main\Language::$txt["infos"]["unactivated"]."</label> &nbsp; 
				   <input type='radio' name='active' value='1' id='act' /> <label for='act'>".\Main\Language::$txt["infos"]["activated"]."</label></p>
				<p><strong>".\Main\Language::$txt["infos"]["design_add_standard"]."</strong><br /><input type='radio' name='standard' value='0' id='unstd' checked /> <label for='unstd'>".\Main\Language::$txt["infos"]["unactivated"]."</label> &nbsp; 
				   <input type='radio' name='standard' value='1' id='std' /> <label for='std'>".\Main\Language::$txt["infos"]["activated"]."</label></p>
				<p><button class='btn btn-primary right'>".\Main\Language::$txt["buttons"]["save"]."</button></p>
			</form>"; 
			
			return $html; 
		}
	
		$html = ""; 
		
		$list_active = 0; 
		$add_active = 0; 
		
		if(isset($_GET["up"]))
		{
			if($_GET["up"] == "list") $list_active = 1; 
			else if($_GET["up"] == "add") $add_active = 1; 
		} else $list_active = 1; 
		
		$html .= "<div class='panel panel-primary'><div class='panel-body'>";
		
		if($list_active) $html .= showList(); 
		
		if($add_active) $html .= showAdd(); 
		
		$html .= "</div></div>"; 
		return $html; 
	}
	
	
}

?>