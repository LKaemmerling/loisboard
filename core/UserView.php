<?php
namespace Main\User; 
class View
{
	/**
	* UCP Anzeigen
	*
	* Zeigt das User-Control-Panel im rechten Bereich der Seite. Wenn der Benutzer nicht 
	* eingeloggt ist zeigt es stattdesssen die Login-Form
	*
	* @author s-l 
	* @version 0.0.8
	*/
	public static function showUserCP()
	{
		$html = ""; 
		
		$panel_class = "panel-default"; 
		if(\Main\User\Control::$logged == 1) $panel_class = "panel-primary"; 
		
		$html .= "<div class='panel $panel_class pageContentRowRightPanel userControlPanelRight'>";
		
		if(\Main\User\Control::$logged == 0) 
		{
		
			$gets = ""; 
			if(isset($_GET) && is_array($_GET))
			{
				foreach($_GET as $vor => $get) {
					if($gets != "") $gets .= "&"; 
					if($vor != "handle-logout") 
						$gets .= $vor;
					if($get != "") {
						$gets .= "=".$get; 
					}
				}
			}
			if($gets != "") $gets = rtrim(rtrim($gets), "&") . "&"; 
		
			$html .= "<div class='panel-heading'>".\Main\Language::$txt["infos"]["login_right_header"]."</div>";
			
			$html .= "<div class='panel-body'>"; 
			
				$html .= "<form action='index.php?".$gets."handle-login' method='post' class='form-horizontal'>
					<div class='form-group'>
						<div class='col-sm-12'>
							<input class='form-control' name='mail' type='text' placeholder='".\Main\Language::$txt["forms"]["login_placeholder_mail"]."' />
						</div>
					</div>
					<div class='form-group'>
						<div class='col-sm-12'>
							<input class='form-control' name='pass' type='password' placeholder='".\Main\Language::$txt["forms"]["login_placeholder_pw"]."' />
						</div>
					</div>
					<div class='form-group'>
						<div class='col-sm-12'>
							<input type='checkbox' name='stay_logged' /> ".\Main\Language::$txt["forms"]["login_placeholder_stay_logged"]."
						</div>
					</div>
					<div class='form-group'>
						<div class='col-sm-12'>
							<button class='btn btn-default'>".\Main\Language::$txt["buttons"]["login"]."</button>
						</div>
					</div>
					<div class='form-group no-margin-padding-bottom ucpformlastgroup'>
						<div class='col-sm-12'>
							".\Main\Language::$txt["infos"]["no_account_go_register"]."
						</div>
					</div>
				</form>"; 
			
			$html .= "</div>"; //panel-body
		}
		else
		{
			$display_top_name = \Main\User\Control::$data["displayname"]; 
				
			$html .= "<script>
				$(document).ready(function() {
					setOnlineStatus(".Control::$dbid.");
					startCheckAlerts(".Control::$dbid.");
				});
			</script>";
			$html .= "<div class='panel-heading'>".htmlspecialchars($display_top_name)."</div>";
			
			$html .= "<div class='panel-body'>"; 
			
			if(\Main\User\Control::$data["avatar"] != "") 
			{
				$html .= "<img src='".htmlspecialchars(\Main\User\Control::$data["avatar"], ENT_QUOTES)."' class='img-thumbnail floatright imgucpright' />";
			}
			
			$gets = ""; 
			if(isset($_GET) && is_array($_GET))
			{
				foreach($_GET as $vor => $get) {
					if($gets != "") $gets .= "&"; 
					if($vor != "handle-login")
						$gets .= $vor;
					if($get != "") {
						$gets .= "=".$get; 
					}
				}
			}
			if($gets != "") $gets = rtrim(rtrim($gets), "&") . "&"; 
			
			$new_alerts = \Main\User\Alert::countUserAlerts(\Main\User\Control::$dbid); 
			$new_messages = \Main\User\Control::countNewKonversations(\Main\User\Control::$dbid); 

			$bubbles = $new_alerts + $new_messages; 
			$html .= "<script>doBubble($bubbles);</script>";
			
			
			if($new_alerts == 0) 
				$alert_string = "<a class='link_alerts' href='index.php?page=alerts'>".\Main\Language::$txt["infos"]["link_alerts"]."</a>"; 
			else
				$alert_string = "<a class='link_alerts new_alerts' href='index.php?page=alerts'>".\Main\Language::$txt["infos"]["link_alerts"]." ($new_alerts)</a>"; 

			if($new_messages == 0) 
				$messages_string = "<a class='link_conversations' href='index.php?page=conversations'>".\Main\Language::$txt["infos"]["link_conversations"]."</a>";
			else
				$messages_string = "<a class='link_conversations new_conversations' href='index.php?page=conversations'>".\Main\Language::$txt["infos"]["link_conversations"]." ($new_messages)</a>"; 

			$html .= "<p><a class='link_my_profile' href='index.php?page=members&u=".htmlspecialchars(\Main\User\Control::$dbid)."'>".\Main\Language::$txt["infos"]["link_my_profile"]."</a><br /><a href='index.php?page=settings'>".\Main\Language::$txt["infos"]["link_settings"]."</a></p>
			<p>$messages_string
			<br />$alert_string</p>";
			if(\Main\User\Control::$data["arights"]["enter_administration"] == 1)
				$html .= "<p><a class='link_administration' href='acp/index.php' target='_blank'>Administration</a></p>"; 
			$html .= "<p><a class='link_logout' href='index.php?".$gets."handle-logout'>".\Main\Language::$txt["infos"]["link_logout"]."</a></p>"; 
			
			$html .= "</div>"; //panel-body 
		}
			
		
		$html .= "</div>"; 
				
		return $html; 
	}
	
	/**
	* Registrierung
	* 
	* Zeigt die Registrierungsseite an. 
	*
	* @author s-l 
	* @version 0.0.7 
	*/
	public static function showRegister()
	{
		$html = ""; 
		
		$showform = 1; 
		
		if(\Main\User\Control::$logged == 1) 
		{
			$html .= "<div class='alert alert-danger redbox'>
				".\Main\Language::$txt["infos"]["showRegister_already_logged_in_error"]."
			</div>"; 
			return $html; 
		}
		
		$input = array(
			"username" => "",
			"vorname" => "",
			"nachname" => "",
			"mail" => ""
		);
		
		if(isset($_GET["check"], $_POST["username"])) // Register Prozess
		{
			$input = array(
				"username" => $_POST["username"],
				"vorname" => "",
				"nachname" => "",
				"mail" => trim($_POST["mail"]),
				"pw1" => $_POST["pw1"],
				"pw2" => $_POST["pw2"]
			); 
			
			if(isset($_POST["vorname"])) $input["vorname"] = $_POST["vorname"]; 
			if(isset($_POST["nachname"])) $input["nachname"] = $_POST["nachname"]; 
			
			$input["username"] = ltrim(rtrim($input["username"])); 
			
			$error_code = \Main\User\Control::checkDataForRegister($input); 
			
			if($error_code == 0) //Erfolg
			{
				$showform = 0; 
				\Main\User\Control::registerAccount($input);
				$html .= "<div class='alert alert-success greenbox'>
					".\Main\Language::$txt["alerts"]["register_success"]."
				</div>";
			}
			else if($error_code == 1) // Benutzername zu kurz
			{
				$html .= "<div class='alert alert-warning yellowbox'>
					".\Main\Language::$txt["alerts"]["register_error_1"]."
				</div>";
			}
			else if($error_code == 2) // Passwörter passen nicht
			{
				$html .= "<div class='alert alert-warning yellowbox'>
					".\Main\Language::$txt["alerts"]["register_error_2"]."
				</div>";
			}
			else if($error_code == 3) // Passwort zu kurz
			{
				$html .= "<div class='alert alert-warning yellowbox'>
					".\Main\Language::$txt["alerts"]["register_error_3"]."
				</div>"; 
			}
			else if($error_code == 4) // Account existiert bereits
			{
				$html .= "<div class='alert alert-warning yellowbox'>
					".\Main\Language::$txt["alerts"]["register_error_4"]."
				</div>"; 
			}
			else if($error_code == 5) // keine Leerzeichen im Benutzernamen
			{
				$html .= "<div class='alert alert-danger redbox'>
					".\Main\Language::$txt["alerts"]["register_error_5"]."
				</div>";
			}
			
		}
		
		if($showform == 1) // Register Form
		{
			$html .= "<h1>".\Main\Language::$txt["infos"]["register_header"]."</h1>
			".\Main\Language::$txt["infos"]["register_info"];
			
			$html .= "<div class='panel panel-default panel-margin-top'>";
			
				$html .= "<div class='panel-body'>";
				
					$html .= "<form action='index.php?page=register&check' method='post' class='form-horizontal'>
						<div class='form-group'>
							<div class='col-sm-12'>
								<input class='form-control' type='text' name='username' id='uname' placeholder='".\Main\Language::$txt["forms"]["register_placeholder_username"]."' value='".htmlspecialchars($input["username"], ENT_QUOTES)."' required />
							</div>
						</div>
						<div class='form-group'>
							<div class='col-sm-6'>
								<input class='form-control' type='text' name='vorname' placeholder='".\Main\Language::$txt["forms"]["register_placeholder_first_name"]."' value='".htmlspecialchars($input["vorname"], ENT_QUOTES)."' optional />
							</div>
							<div class='col-sm-6'>
								<input class='form-control' type='text' name='nachname' placeholder='".\Main\Language::$txt["forms"]["register_placeholder_second_name"]."' value='".htmlspecialchars($input["nachname"], ENT_QUOTES)."' optional />
							</div>
						</div>
						<div class='form-group register_placeholder'>
							<div class='col-sm-6'>
								&nbsp;
							</div>
						</div>
						<div class='form-group'>
							<div class='col-sm-12'>
								<input class='form-control' type='text' name='mail' placeholder='".\Main\Language::$txt["forms"]["register_placeholder_mail"]."' value='".htmlspecialchars($input["mail"], ENT_QUOTES)."' required />
							</div>
						</div>
						<div class='form-group'>
							<div class='col-sm-6'>
								<input class='form-control' type='password' name='pw1' placeholder='".\Main\Language::$txt["forms"]["register_placeholder_pw"]."' required />
							</div>
							<div class='col-sm-6'>
								<input class='form-control' type='password' name='pw2' placeholder='".\Main\Language::$txt["forms"]["register_placeholder_pw_again"]."' required />
							</div>
						</div>
						<div class='form-group'>
							<div class='col-sm-12 text-center'>
								".\Main\Language::$txt["forms"]["register_privacy"]."
							</div>
						</div>
						<div class='form-group'>
							<div class='col-sm-12 text-center'>
								<button class='btn btn-primary'>".\Main\Language::$txt["buttons"]["register"]."</button>
							</div>
						</div>
					</form>";
				
				$html .= "</div>"; //panel-body
			
			$html .= "</div>";
		}
		
		return $html; 
	}

	/**
	* Einstellungen
	*
	* Zeigt alle Benutzereinstellungen an. Wird nur aufgerufen wenn der Benutzer eingeloggt ist. 
	*
	* @author s-l 
	* @version 0.1.5
	*/
	public static function showSettings()
	{
		$html = ""; 
		
		$setting = "general"; 
		
		if(isset($_GET["setting"]))
			$setting = $_GET["setting"]; 
		
		$general_active = ""; 
		$private_active = ""; 
		$avatar_active = ""; 
		$signature_active = ""; 
		
		if($setting == "general") $general_active = "active"; 
		else if($setting == "private") $private_active = "active"; 
		else if($setting == "avatar") $avatar_active = "active"; 
		else if($setting == "signature") $signature_active = "active"; 
		
		$html .= "<div class='row row-settings'>
			<div class='col-md-4'>
				<div class='panel panel-default panel-settings'>
					<div class='panel-heading'>".\Main\Language::$txt["infos"]["settings_left_header"]."</div>
					<div class='panel-body'>
						
						<a href='index.php?page=settings&setting=general'><div class='panel-settings-item $general_active'>
							".\Main\Language::$txt["buttons"]["settings_general"]."
						</div></a>
						<a href='index.php?page=settings&setting=private'><div class='panel-settings-item $private_active'>
							".\Main\Language::$txt["buttons"]["settings_private"]."
						</div></a>
						<a href='index.php?page=settings&setting=avatar'><div class='panel-settings-item $avatar_active'>
							".\Main\Language::$txt["buttons"]["settings_avatar"]."
						</div></a>
						<a href='index.php?page=settings&setting=signature'><div class='panel-settings-item $signature_active'>
							".\Main\Language::$txt["buttons"]["settings_signature"]."
						</div></a>
						
					</div>
				</div>
			</div>
			
			<div class='col-md-8 col-settings-right'>"; 
			
				if($setting == "general") // Allgemeine Einstellungen
				{
					if(isset($_GET["save"], $_POST["pw"])) {
					
						$input = array(
							"pw" => $_POST["pw"],
							"newun" => $_POST["newun"],
							"newvn" => $_POST["newvn"],
							"newnn" => $_POST["newnn"],
							"newmail" => $_POST["newmail"],
							"newpw1" => $_POST["newpw1"],
							"newpw2" => $_POST["newpw2"]
						);
						
						if(md5($input["pw"]) != $_SESSION["key"])
						{
							$html .= "<div class='alert alert-danger redbox'>
								".\Main\Language::$txt["alerts"]["settings_general_wrong_pw"]."
							</div>";
						}
						else
						{
							$pwrong = 0; 
							if($input["newpw1"] != "" || $input["newpw2"] != "") 
							{
								if($input["newpw1"] == $input["newpw2"])
								{
									$sql = "UPDATE accounts SET passwort=MD5('".\Main\DB::escape($input["newpw1"])."') WHERE id='".\Main\DB::escape(\Main\User\Control::$dbid)."'";
									\Main\DB::query($sql); 
									$_SESSION["key"] = md5($input["newpw1"]); 
								}
								else
								{
									$pwrong = 1; 
									$html .= "<div class='alert alert-danger redbox'>
										".\Main\Language::$txt["alerts"]["settings_general_wrong_newpw"]."
									</div>";
								}
							}
							
							if($pwrong == 0) 
							{
								$unamefail = 0; 
								if($input["newun"] != \Main\User\Control::$data["username"])
								{
								
									if(\Main\User\Control::$data["lastUsernameChange"] > (time() - (182 * 86400)))
									{
										$restStamp = \Main\User\Control::$data["lastUsernameChange"] - (time() - (182 * 86400)); 
										$restDays = ceil($restStamp / 86400);
										$string = \Main\Language::$txt["alerts"]["settings_general_new_uname_in_x_days"]; 
										$string = str_replace("[x]", $restDays, $string);
										$html .= "<div class='alert alert-danger redbox'>
											$string
										</div>";
										$unamefail = 1; 
									}
									else
									{
										\Main\DB::update("accounts", \Main\User\Control::$dbid, array(
											"username" => $input["newun"],
											"lastUsernameChange" => time()
										));
									}
									
								}
								
								if($unamefail == 0) 
								{
									$doact = 1; 

									if(Control::$data["display_fullname"] == 1) 
									{
										if(ltrim(rtrim($input["newvn"])) == "" || ltrim(rtrim($input["newnn"])) == "")
										{
											$html .= "<div class='alert alert-danger'>";
											$html .= "Du musst Vor- und Nachname angeben wenn du deinen Anzeigenamen behalten möchtest."; 
											$html .= "</div>"; 
											$doact = 0; 
										}
										else
										{
											$fullname = ltrim(rtrim($input["newvn"])) . " " . ltrim(rtrim($input["newnn"])); 
											if(Control::existsUserWithFullname($fullname, Control::$dbid)) 
											{
												$html .= "<div class='alert alert-danger'>";
												$html .= "Es existiert bereits ein Benutzer unter diesem Vor- und Nachnamen."; 
												$html .= "</div>"; 
												$doact = 0; 
											}
										}
									}

									if($doact == 1) 
									{
										\Main\DB::update("accounts", \Main\User\Control::$dbid, array(
											"vorname" => $input["newvn"],
											"nachname" => $input["newnn"],
											"mail" => $input["newmail"]
										));
										
										$html .= "<div class='alert alert-success greenbox'>
											".\Main\Language::$txt["alerts"]["settings_general_success"]."
										</div>";
									}
								}
								
								Control::init(); 
								
							}
							
						}
					}	
				
					$html .= "<h2>".\Main\Language::$txt["infos"]["settings_general_header"]."</h2>";

					$html .= "<div class='panel panel-default panel-margin-top panel-settings-right'>";
					
						$html .= "<form action='index.php?page=settings&setting=general&save' method='post' class='form-horizontal'>
							
							<div class='form-group'>
								<div class='col-sm-3 textalignright textpaddingtopform'>
									<label class='nolabel' for='nowpw'>".\Main\Language::$txt["forms"]["settings_general_pw"]."</label>
								</div>
								<div class='col-sm-9'>
									<input id='nowpw' class='form-control' type='password' name='pw' required />
									<small>".\Main\Language::$txt["infos"]["settings_general_pwinfo"]."</small>
								</div>
							</div>
							
							<div class='form-group'>
								<div class='col-sm-3 textalignright textpaddingtopform'>
									<label class='nolabel' for='newun'>".\Main\Language::$txt["forms"]["settings_general_uname"]."</label>
								</div>
								<div class='col-sm-9'>
									<input id='newun' class='form-control' type='text' name='newun' value='".htmlspecialchars(\Main\User\Control::$data["username"], ENT_QUOTES)."' required />
									<small>".\Main\Language::$txt["infos"]["settings_general_unameinfo"]."</small>
								</div>
							</div>
							
							<div class='form-group'>
								<div class='col-sm-3 textalignright textpaddingtopform'>
									<label class='nolabel' for='newvn'>".\Main\Language::$txt["forms"]["settings_general_first_name"]."</label>
								</div>
								<div class='col-sm-9'>
									<input id='newvn' class='form-control' type='text' name='newvn' value='".htmlspecialchars(\Main\User\Control::$data["vorname"], ENT_QUOTES)."' />
								</div>
							</div>
							
							<div class='form-group form-group-low-margin-top'>
								<div class='col-sm-3 textalignright textpaddingtopform'>
									<label class='nolabel' for='newnn'>".\Main\Language::$txt["forms"]["settings_general_second_name"]."</label>
								</div>
								<div class='col-sm-9'>
									<input id='newnn' class='form-control' type='text' name='newnn' value='".htmlspecialchars(\Main\User\Control::$data["nachname"], ENT_QUOTES)."' />
								</div>
							</div>
							
							<div class='form-group'>
								<div class='col-sm-3 textalignright textpaddingtopform'>
									<label class='nolabel' for='newmail'>".\Main\Language::$txt["forms"]["settings_general_mail"]."</label>
								</div>
								<div class='col-sm-9'>
									<input id='newmail' class='form-control' type='text' name='newmail' value='".htmlspecialchars(\Main\User\Control::$data["mail"], ENT_QUOTES)."' required />
								</div>
							</div>
							
							<div class='form-group'>
								<div class='col-sm-3 textalignright textpaddingtopform'>
									<label class='nolabel' for='newpw1'>".\Main\Language::$txt["forms"]["settings_general_newpw"]."</label>
								</div>
								<div class='col-sm-9'>
									<input id='newpw1' class='form-control' type='password' name='newpw1' />
								</div>
							</div>
							<div class='form-group form-group-low-margin-top'>
								<div class='col-sm-3 textalignright textpaddingtopform'>
									<label class='nolabel' for='newpw2'>".\Main\Language::$txt["forms"]["settings_general_newpw_again"]."</label>
								</div>
								<div class='col-sm-9'>
									<input id='newpw2' class='form-control' type='password' name='newpw2' />
								</div>
							</div>
						
							<div class='textright'>
								<button class='btn btn-primary'>".\Main\Language::$txt["buttons"]["save"]."</button>
							</div>
						
						</form>"; 
					
					$html .= "</div>"; 
				}
				
				else if($setting == "private") // Privatsphäre Einstellungen
				{
					if(isset($_POST["art_displayname"])) // Anzeigename updaten
					{
						$art = $_POST["art_displayname"]; 
						if($art != \Main\User\Control::$data["display_fullname"])
						{
							$doart = 1; 
							if($art == 1) // Wenn eine Kombination aus Vor- und Nachname gewählt wird 
							{
								if(\Main\User\Control::$data["vorname"] == "" || \Main\User\Control::$data["nachname"] == "") // Beide Namen müssen angegeben sein
								{
									$html .= "<div class='alert alert-danger'>".\Main\Language::$txt["alerts"]["settings_private_displayname_need_both_names"]."</div>";
									$doart = 0; 
								}
								else
								{
									$fullname = rtrim(ltrim(\Main\User\Control::$data["vorname"])) . " " . rtrim(ltrim(\Main\User\Control::$data["nachname"]));
									if(Control::existsUserWithFullname($fullname)) // es darf nur 1 Benutzer mit dem Displaynamen existieren
									{
										$html .= "<div class='alert alert-danger'>".\Main\Language::$txt["alerts"]["settings_private_displayname_exists"]."</div>";
										$doart = 0; 
									} 
								}
							}

							if($doart == 1) 
							{
								\Main\User\Control::$data["display_fullname"] = $art; 
								\Main\DB::update("accounts", \Main\User\Control::$dbid, array("display_fullname" => $art));
								$html .= "<div class='alert alert-success'>".\Main\Language::$txt["alerts"]["settings_private_displayname_succ"]."</div>"; 
							}
						}
					}
					
					if(isset($_POST["show_memberlist"])) // In Mitgliederliste anzeigen
					{
						$art = $_POST["show_memberlist"]; 
						if($art != \Main\User\Control::$data["hide_in_memberslist"])
						{
							\Main\User\Control::$data["hide_in_memberslist"] = $art; 
							\Main\DB::update("accounts", \Main\User\Control::$dbid, array("hide_in_memberslist" => $art));
							$html .= "<div class='alert alert-success'>".\Main\Language::$txt["alerts"]["settings_private_memberslist_succ"]."</div>"; 
						}
					}
				
					$html .= "<h2>".\Main\Language::$txt["infos"]["settings_private_header"]."</h2>";
					
					// Panel Anzeigename 
					$html .= "<div class='panel panel-default panel-private-displayname panel-margin-top panel-settings-right-nopadding'>";
					$html .= "<div class='panel-heading'>".\Main\Language::$txt["infos"]["settings_private_displayname_h"]."</div>";
					$html .= "<div class='panel-body'>";
						$html .= \Main\Language::$txt["infos"]["settings_private_displayname_info"];
						$html .= "<form action='index.php?page=settings&setting=private' method='post'>
							<select class='form-control' name='art_displayname'>"; 
								if(\Main\User\Control::$data["display_fullname"] == 0) 
								{
									$html .= "<option value='0'>".\Main\Language::$txt["forms"]["settings_private_displayname_0"]."</option>
											<option value='1'>".\Main\Language::$txt["forms"]["settings_private_displayname_1"]."</option>"; 
								}
								else
								{
									$html .= "<option value='1'>".\Main\Language::$txt["forms"]["settings_private_displayname_1"]."</option>
											<option value='0'>".\Main\Language::$txt["forms"]["settings_private_displayname_0"]."</option>"; 
								}
							$html .= "</select>
							<button class='btn btn-primary right buttons-margin-top'>".\Main\Language::$txt["buttons"]["save"]."</button>
						</form>"; 
					
					$html .= "</div>"; //panel-body
					$html .= "</div>"; //panel
					///
					
					// Panel Memberliste 
					$html .= "<div class='panel panel-default panel-private-memberlist panel-margin-top panel-settings-right-nopadding'>";
					$html .= "<div class='panel-heading'>".\Main\Language::$txt["infos"]["settings_private_memberlist_h"]."</div>";
					$html .= "<div class='panel-body'>"; 
						$html .= \Main\Language::$txt["infos"]["settings_private_memberlist_info"];
						$html .= "<form action='index.php?page=settings&setting=private' method='post'>
							<select name='show_memberlist' class='form-control'>";
								
								if(\Main\User\Control::$data["hide_in_memberslist"] == 0) 
								{
									$html .= "<option value='0'>".\Main\Language::$txt["forms"]["settings_private_memberlist_0"]."</option>
									<option value='1'>".\Main\Language::$txt["forms"]["settings_private_memberlist_1"]."</option>"; 
								}
								else
								{
									$html .= "<option value='1'>".\Main\Language::$txt["forms"]["settings_private_memberlist_1"]."</option>
									<option value='0'>".\Main\Language::$txt["forms"]["settings_private_memberlist_0"]."</option>"; 
								}
						
						$html .= "</select>
							<button class='btn btn-primary right buttons-margin-top'>".\Main\Language::$txt["buttons"]["save"]."</button>
						</form>"; 
					$html .= "</div>"; 
					$html .= "</div>"; 
					///
				}
				
				else if($setting == "avatar") // Avatar Einstellungen 
				{
					if(isset($_GET["upload"], $_FILES["newavatar"]))
					{
						$target_dir = "media/upload/"; 
						$target_file = $target_dir . basename($_FILES["newavatar"]["name"]);
						$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
						
						if($imageFileType != "gif" && $imageFileType != "png" && $imageFileType != "jpg" && $imageFileType != "jpeg") 
						{
							$html .= "<div class='alert alert-danger redbox'>
								Du musst einen der folgenden Dateitypen einhalten: png, jp(e)g, gif
							</div>"; 
						}
						else
						{
							$i = 1; 
							while(file_exists($target_file))
							{
								$target_file = $target_dir . $i . "-" . basename($_FILES["newavatar"]["name"]);
								$i ++; 
							}
							
							if(move_uploaded_file($_FILES["newavatar"]["tmp_name"], $target_file)) 
							{
								$sql = "UPDATE bilderUpload SET avatar='0' WHERE user='".\Main\DB::escape(\Main\User\Control::$dbid)."'";
								\Main\DB::query($sql);
								\Main\DB::insert("bilderUpload", array(
									"user" => \Main\User\Control::$dbid,
									"link" => $target_file,
									"avatar" => 1
								));
								$html .= "<div class='alert alert-success greenbox'>
									Du hast deinen Avatar erfolgreich geändert.
								</div>"; 
								\Main\User\Control::init(); 
							}
							else
							{
								$html .= "<div class='alert alert-danger redbox'>
									Beim Upload ist ein Fehler aufgetreten. Versuch es später nochmal oder wende dich an einen Administrator.
								</div>";
							}
						}
						
					}
					else if(isset($_GET["delavatar"]))
					{
						$sql = "UPDATE bilderUpload SET avatar='0' WHERE user='".\Main\DB::escape(\Main\User\Control::$dbid)."'";
						\Main\DB::query($sql); 
						$html .= "<div class='alert alert-warning yellowbox'>
							Dein Profilbild wurde erfolgreich entfernt.
						</div>";
						\Main\User\Control::init(); 
					}
				
					$html .= "<h2>".\Main\Language::$txt["infos"]["settings_avatar_header"]."</h2>"; 
					
					$html .= "<div class='panel panel-default panel-avatar panel-margin-top panel-settings-right'>";
					
						if(\Main\User\Control::$data["avatar"] == "") 
							$html .= \Main\Language::$txt["infos"]["settings_avatar_no_avatar"];
						else 
							$html .= "<img src='".htmlspecialchars(\Main\User\Control::$data["avatar"], ENT_QUOTES)."' class='img-thumbnail' style='max-width:60%;max-height:250px;' />"; 
							
						$html .= "<br /><br />
						<form action='index.php?page=settings&setting=avatar&upload' method='post' enctype='multipart/form-data'>
							<p><input type='file' name='newavatar' /></p>
							<p><button class='btn btn-default'>".\Main\Language::$txt["buttons"]["upload"]."</button></p>
						</form>
						<p><a href='index.php?page=settings&setting=avatar&delavatar'><button class='btn btn-danger'>".\Main\Language::$txt["buttons"]["delavatar"]."</button></a></p>";
						
					
					$html .= "</div>"; 
				}
				
				else if($setting == "signature")  // Signatur Einstellungen
				{
					if(isset($_GET["save"]))
					{
						$si = $_POST["signature"]; 
						\Main\DB::update("accounts", \Main\User\Control::$dbid, array("signature" => $si));
						$html .= "<div class='alert alert-success greenbox'>
							".\Main\Language::$txt["alerts"]["signature_updated_success"]."
						</div>";
						\Main\User\Control::init(); 
					}
				
					$html .= "<h2>".\Main\Language::$txt["infos"]["settings_signature_header"]."</h2>";
					
					$html .= "<div class='panel panel-default panel-signature panel-margin-top panel-settings-right'>";
						
						if(\Main\User\Control::$data["signature"] != "") 
							$html .= \Main\User\Control::$data["signature"]; 
						else 
							$html .= \Main\Language::$txt["infos"]["you_dont_have_any_signature"];
					
					$html .= "</div>";
					
					$html .= "<form action='index.php?page=settings&setting=signature&save' method='post'>
						<textarea name='signature' id='ckedit'></textarea> 
						<script>CKEDITOR.replace('ckedit');</script>
						<div class='textright buttons-margin-top'>
							<button class='btn btn-primary'>".\Main\Language::$txt["buttons"]["save"]."</button>
						</div>
					</form>"; 
				}
			
			$html .= "</div>
		</div>";
		
		return $html; 
	}
	
	/**
	* Konversationen Anzeigen
	*
	* Zeigt die Konversations-Seite im Hauptbereich des Forums an. 
	* 
	* @author s-l 
	* @version 0.0.3 
	* @todo schöner Darstellen, Konversationen löschen können, etc. etc. 
	*/
	public static function showConversationsMain()
	{	
		$html = ""; 
		
		if(\Main\User\Control::$logged == 0) return "<div class='alert alert-danger redbox'>".\Main\Language::$txt["infos"]["not_logged_in"]."</div>"; 
		
		$showmain = 1; 
		
		// Neue Konversation starten
		if(isset($_GET["new_conversation"]))
		{
			$showmain = 0; 
			$shownew = 1; 

			// Konversation starten
			if(isset($_GET["send"], $_POST["titel"]))
			{
				$input = array("titel" => $_POST["titel"], 
					"message" => $_POST["message"]);
				if($_POST["user"] != "none") 
					$input["user"] = $_POST["user"]; 
				else 
					$input["user"] = 0; 
				
				if($input["titel"] == "" || $input["message"] == "") 
				{
					$html .= "<div class='alert alert-danger'>".\Main\Language::$txt["alerts"]["new_conversation_empty"]."</div>"; 
				}
				else
				{
					\Main\DB::insert("conversations", array("name" => $input["titel"], "startTime" => time(), "lastUpdate" => time())); 
					$conv_id = \Main\DB::$insertID; 
					\Main\DB::insert("conversation_users", array("user" => \Main\User\Control::$dbid, "conversation" => $conv_id, "startTime" => time()));
					if($input["user"] != 0 && $input["user"] != \Main\User\Control::$dbid) 
						\Main\DB::insert("conversation_users", array("user" => $input["user"], "conversation" => $conv_id, "startTime" => time()));
					\Main\DB::insert("conversation_msg", array("conversation" => $conv_id, "user" => \Main\User\Control::$dbid, "message" => $input["message"]));
					$shownew = 0; 
					$showmain = 1; 
					$html .= "<div class='alert alert-success'>".\Main\Language::$txt["alerts"]["new_conversation_success"]."</div>";
				}
			}

			if($shownew) 
			{
				$html .= "<h2>".\Main\Language::$txt["infos"]["new_conversation_header"]."</h2>";

				$html .= "<form action='index.php?page=conversations&new_conversation&send' method='post'>";

					$html .= "<p><input name='titel' type='text' class='form-control' placeholder='".\Main\Language::$txt["forms"]["new_conversation_title"]."' /></p>";

					$html .= "<p><select class='form-control' name='user'><option value='none'>".\Main\Language::$txt["forms"]["new_conversation_choose_user"]."</option>"; 
						$rst = \Main\DB::select("accounts", "id", null, null, "id DESC");
						while($row = $rst->fetch_object())
						{
							$acid = $row->id; 
							$data = \Main\User\Control::getUserData($acid); 
							$html .= "<option value='$acid'>".htmlspecialchars($data["displayname"])."</option>"; 
						}
					$html .= "</select></p>";

					$html .= "<p><textarea id='cke' name='message'></textarea></p>";

					$html .= "<p><div class='right'><button class='btn btn-primary'>".\Main\Language::$txt["buttons"]["send"]."</button></div></p>"; 

					$html .= "<script>CKEDITOR.replace('cke');</script>";

				$html .= "</form>";  
			}
		}
		
		// Eine Konversation anzeigen
		if(isset($_GET["c"]))
		{
			$cId = $_GET["c"]; 
			if(\Main\User\Control::IsUserInKonversation(\Main\User\Control::$dbid, $cId))
			{
				$showmain = 0; 
				$rst = \Main\DB::select("conversations", "name", "id='".\Main\DB::escape($cId)."'");
				$row = $rst->fetch_object(); 

				$conversation = array("id" => $cId,
					"name" => $row->name);

				if(isset($_POST["answer"]))
				{
					\Main\DB::insert("conversation_msg", array("user" => \Main\User\Control::$dbid, "conversation" => $conversation["id"], "message" => $_POST["answer"], "time" => time()));
				}
				
				$html .= "<h2>".htmlspecialchars($conversation["name"])."</h2>";

				$html .= "<div class='theme_posts'>";

				$pageNo = 1; 

				$rst = \Main\DB::select("conversation_msg", "id, user, message, time", "conversation='".\Main\DB::escape($conversation["id"])."'", null, "id");
				while($row = $rst->fetch_object())
				{
					$msg = array("id" => $row->id,
					"user" => $row->user,
					"message" => $row->message,
					"time" => $row->time
					);
					$data = \Main\User\Control::getUserData($msg["user"]); 
					\Main\User\Control::setKonversationMsgToSeen(\Main\User\Control::$dbid, $msg["id"]); 

					$html .= "<div class='theme_post'><div class='row'>";

					$html .= "<div class='col-md-3 postLeft'>"; 
						if($data["avatar"] != "") 
								$html .= "<img src='".htmlspecialchars($data["avatar"], ENT_QUOTES)."' class='img-thumbnail img-avatar-post' style='max-width:70%;max-height:200px;' /><br />";
							$html .= "<a class='user' href='index.php?page=members&u=".$msg["user"]."'>".htmlspecialchars($data["displayname"])."</a><br />".$data["rank"];
		 					$html .= "<br /><br />".\Main\Language::$txt["infos"]["post_left_member_since"]."<br />
												".\Main\toTime2($data["registerTime"])."<br />
												".\Main\Language::$txt["infos"]["post_left_posts"]." ".$data["posts"].""; 
					$html .= "</div>";

					$html .= "<div class='col-md-9 postRight'>
						<small>".\Main\toTime($msg["time"])."</small><br />
						<div class='container-fluid container-post'>".$msg["message"]."</div>"; 

						if($data["signature"] != "") 
									$html .= "<div class='container-fluid container-post-signature'>".$data["signature"]."</div>"; 

					$html .= "</div>";


					$html .= "</div></div>"; 
				}

				$html .= "<div class='theme_post theme_answer'>";
					$html .= "<div class='row'><div class='col-md-12'>";
						$html .= "<form action='index.php?page=conversations&c=".$conversation["id"]."&pageNo=$pageNo' method='post'>
							<textarea id='cke' name='answer'></textarea>
							<p><div class='right'><button class='btn btn-primary'>".\Main\Language::$txt["buttons"]["send"]."</button></div></p>
						</form>";

						$html .= "<script>CKEDITOR.replace('cke');</script>";
					$html .= "</div></div>";  
				$html .= "</div>"; 

				$html .= "</div>"; 
			}
		}
		
		// Verteiler anzeigen 
		if($showmain == 1) 
		{
			$html .= "<h2>".\Main\Language::$txt["infos"]["conversations_header"]."</h2>".\Main\Language::$txt["infos"]["conversations_description"];
			
			$html .= "<div class='right'><a href='index.php?page=conversations&new_conversation'><button class='btn btn-primary'>".\Main\Language::$txt["buttons"]["new_conversation"]."</button></a></div>";
			
			$html .= "<div class='clear'></div>";
			$html .= "<div class='panel panel-primary panel-margin-top panel-conversations'>";
			$html .= "<div class='panel-heading'>".\Main\Language::$txt["infos"]["panel_conversations_header"]."</div>";
			$html .= "<div class='panel-body'>";

				$sql = "SELECT conversations.id, conversations.name FROM conversations INNER JOIN conversation_users ON conversation_users.conversation=conversations.id AND conversation_users.user='".\Main\DB::escape(\Main\User\Control::$dbid)."'";
				$rst = \Main\DB::query($sql); 
				while($row = $rst->fetch_object())
				{
					$conversation = array("id" => $row->id,
						"name" => $row->name);
					$unseen = \Main\User\Control::countKonversationUnseenMsgs(\Main\User\Control::$dbid, $conversation["id"]); 
					$html .= "<div>";
						if($unseen > 0) 
							$html .= "<a href='index.php?page=conversations&c=".$conversation["id"]."'>" . htmlspecialchars($conversation["name"]) . " ($unseen)</a>";
						else
							$html .= "<a href='index.php?page=conversations&c=".$conversation["id"]."'>" . htmlspecialchars($conversation["name"]) . "</a>";
					$html .= "</div>"; 
				}
				
			$html .= "</div>"; 
			$html .= "</div>"; 
			$html .= "<div class='clear'></div>";
			$html .= "<div class='right'><a href='index.php?page=conversations&new_conversation'><button class='btn btn-primary'>".\Main\Language::$txt["buttons"]["new_conversation"]."</button></a></div>";
			
		}
		return $html; 
	}
	
	/**
	* Mitglieder 
	*
	* Zeigt alle Mitglieder (Liste). Des weiteren werden hier die Benutzerprofile verlinkt und alles 
	* weitere das nur als "unterseite" zu den Mitglieder angesehen wird. 
	*
	* @author s-l 
	* @version 0.0.2
	*/
	public static function showMembersMain()
	{
		$html = ""; 
		
		$showmain = 1; 

		if(isset($_GET["u"]) && Control::existsUserId($_GET["u"])) # Einen  Benutzer anzeigen 
		{
			$uId = $_GET["u"]; 
			$showmain = 0; 
			$html .= self::showUserProfile($uId); 
		}
		
		
		if($showmain == 1) # Mitgliederliste anzeigen 
		{
			$pageNo = 1; 
			if(isset($_GET["pageNo"])) $pageNo = $_GET["pageNo"]; 
			
			$max_per_page = 15; 
			$rst = \Main\DB::select("accounts", "id", "hide_in_memberslist='0'");
			$anz_accs = $rst->num_rows; 
			
			$max_pages = ceil($anz_accs / $max_per_page); 
			
			if($pageNo > $max_pages) $pageNo = $max_pages; 
			if($pageNo < 1) $pageNo = 1; 
			
			$startlimit = ($pageNo * $max_per_page) - $max_per_page; 
			$limit = $startlimit . ", " . $max_per_page; 
			
			if($max_pages > 1) 
			{
				$html .= "<ul class='pagination'>";
				for($i = 1; $i <= $max_pages; $i++) 
				{
					$html .= "<li";
					if($i == $pageNo) $html .= " class='active'"; 
					$html .= "><a href='index.php?page=members&pageNo=$i'>$i</a></li>"; 
				}
				$html .= "</ul>"; 
				$html .= "<div class='clear'></div>";
			}
			
			$rst = \Main\DB::select("accounts", "id", "hide_in_memberslist='0'", $limit, "points DESC, lastLogin DESC");
			$html .= "<div class='panel panel-primary panel-members'>";
			$html .= "<div class='panel-body'>"; 
			while($row = $rst->fetch_object())
			{
				$account = array(
					"id" => $row->id,
					"data" => \Main\User\Control::getUserData($row->id)
				);
					
				$show_avatar = ""; 
				if(ltrim(rtrim($account["data"]["avatar"])) != "") 
				{
					$show_avatar = "<img class='img-thumbnail memberlist-img' src='".ltrim(rtrim($account["data"]["avatar"]))."' />"; 
				}
				
				$html .= "<div class='panel-member'>";
					
					$html .= "<div class='media'>
						<div class='media-left'>"; 
							$html .= $show_avatar; // Avatar Ausgabe
						$html .= "</div>
						<div class='media-body'>"; 
							
							// HTTP / HTTPS vor die Webseite schreiben
							if($account["data"]["website"] != "") 
							{
								$account["data"]["website"] = ltrim(rtrim($account["data"]["website"]));
								$pos = strpos($account["data"]["website"], "://");
								if($pos == 0 || $pos == false) 
									$account["data"]["website"] = "http://".ltrim($account["data"]["website"], "://"); 
							}
							
							// Right Bar
							$html .= "<div class='right memberlist-controller-right'>";
								if(ltrim(rtrim($account["data"]["website"])) != "") 
									$html .= "<a target='_blank' href='".ltrim(rtrim($account["data"]["website"]))."'><i class='fa fa-home fa-lg'></i></a>"; 
								if(\Main\User\Control::$logged == 1) 
									$html .= "<a><i class='fa fa-comment fa-lg'></i></a>"; 
							$html .= "</div>"; 
							
							// Useranzeige
							$usrank = ""; 
							if($account["data"]["smallrank"] != "")  // Benutzerrang anzeigen
								$usrank = "&nbsp;" . $account["data"]["smallrank"]; 
							$onstring = ""; 
							if(Control::isUserOnline($account["id"]))
								$onstring = " &nbsp; <span class='badge label onlabel'>".\Main\Language::$txt["infos"]["online_label"]."</span>"; 
							$html .= "<a class='memberlist-user-a' href='index.php?page=members&u=".$account["id"]."'>".htmlspecialchars($account["data"]["displayname"])."</a>$onstring$usrank<br />
							".\Main\Language::$txt["infos"]["member_since"]." ".\Main\toTime2JustDat($account["data"]["registerTime"]); 
							if($account["data"]["posts"] > 0) 
								$html .= "<br />".\Main\Language::$txt["infos"]["memberlist_posts"]." ".$account["data"]["posts"]; 
						$html .= "</div>
					</div>"; 
				
				
				$html .= "</div>"; //panel-member
			}
			$html .= "</div>"; 
			$html .= "</div>";
			
			if($max_pages > 1) 
			{
				$html .= "<div class='clear'></div>";	
				$html .= "<ul class='pagination'>";
				for($i = 1; $i <= $max_pages; $i++) 
				{
					$html .= "<li";
					if($i == $pageNo) $html .= " class='active'"; 
					$html .= "><a href='index.php?page=members&pageNo=$i'>$i</a></li>"; 
				}
				$html .= "</ul>"; 
			}
		}
		
		return $html; 
	}

	/**
	* Benutzerprofil 
	*
	* Zeigt das Benutzerprofil von einen bestimmten Account an. Ermittelt automatisch ob es sich um das eigene oder um ein 
	* fremdes Profil handelt. Wird in der Methode showMembersMain verlinkt. Überprüft NICHT ob der Benutzer auch wirklich existiert.
	*
	* @author s-l 
	* @version 0.0.3
	* @todo Profilaufrufe hinzufügen und anzeigen
	*/
	private static function showUserProfile($uId) 
	{
		$html = ""; 

		$ownProfile = false; 
		if(Control::$logged && Control::$dbid == $uId)
			$ownProfile = true; 

		$uData = Control::getUserData($uId);  

		# Profilkommentar senden
		if(isset($_GET["sendComment"], $_POST["comment"]) && Control::$logged)
		{
			$comment = $_POST["comment"]; 
			if($comment != "")
			{
				\Main\DB::insert("user_profile", array("user" => $uId, "sender" => Control::$dbid, "text" => $comment, "time" => time()));
				$insi = \Main\DB::$insertID; 
				if(!$ownProfile)
					Alert::sendUserAlert($uId, Control::$dbid, 3, 0, $insi); 
				header("Location: index.php?page=members&u=$uId#C$insi"); 
			}
		}

		# Antwort senden
		if(isset($_GET["sendAnswer"], $_POST["entryID"]) && Control::$logged) 
		{
			$entryID = $_POST["entryID"]; 
			$answer = $_POST["answer"]; 
			if($answer != "") 
			{
				\Main\DB::insert("user_profile_comments", array("post" => $entryID, "user" => Control::$dbid, "message" => $answer, "time" => time()));
				$insi = \Main\DB::$insertID; 
				if(!$ownProfile)
					Alert::sendUserAlert($uId, Control::$dbid, 4, 0, $entryID); # Alert für den Pinnwand besitzer
				$rst = \Main\DB::select("user_profile", "sender", "id='".\Main\DB::escape($entryID)."'");
				if($rst->num_rows > 0) 
				{
					$row = $rst->fetch_object(); 
					$sender = $row->sender; 
					if($sender != $uId && $sender != Control::$dbid) 
						Alert::sendUserAlert($sender, Control::$dbid, 5, 0, $entryID); # Alert für den Kommentar besitzer 
				}
				# Todo Alerts für alle anderen Kommentare zu diesem Kommentar (x hat ebenfalls auf das kommentar von x auf der Pinnwand von x geantwortet)
				header("Location: index.php?page=members&u=$uId#C$entryID"); 
			}
		}

		$html .= "<div class='panel panel-primary panel-user'>";
		$html .= "<div class='panel-user-sp1'>";

			if($uData["avatar"] != "") # Avatar
			{
				$html .= "<img src='".htmlspecialchars($uData["avatar"], ENT_QUOTES)."' class='img-thumbnail img-avatar-profile' style='max-width:75%;max-height:600px;' />";
				$html .= "<br /><br />"; 
			}

			# Beiträge 
			if($uData["posts"] > 0) 
				$html .= \Main\Language::$txt["infos"]["uprofile_posts::"] . " <strong>" . $uData["posts"] . "</strong>"; 

			# Profilaufrufe

		$html .= "</div>";  # panel-user-sp1 

		$html .= "<div class='panel-user-sp2'>";

			$html .= "<h2 class='user_profile_h2'>".htmlspecialchars($uData["displayname"])." " . $uData["smallrank"] . "</h2>"; 
			//$html .= "&nbsp; " . $uData["rank"] . "<br />";
			$html .= "<p>" . \Main\Language::$txt["infos"]["member_since"] . " " . \Main\toTimeDatNoTime($uData["registerTime"]) . "</p>";  

			# Profil Anzeigen
			$pageNo = 1; 
			if(isset($_GET["pageNo"])) $pageNo = $_GET["pageNo"]; 
			$rst = \Main\DB::select("user_profile", "id", "user='".\Main\DB::escape($uId)."'"); 
			$anz_comments = $rst->num_rows; 
			$max_comments = 15; 

			$anz_pages = ceil($anz_comments / $max_comments); 
			if($anz_pages < 1) $anz_pages = 1; 
			if($pageNo < 1) $pageNo = 1; 
			if($pageNo > $anz_pages) $pageNo = $anz_pages; 

			$startlimit = ($pageNo * $max_comments) - $max_comments; 
			$limit = $startlimit . ", " . $max_comments; 

			if($anz_pages > 1) 
			{
				$html .= "<div class='clear'></div><ul class='pagination pagination-uprofile'>";
				$sentPoints = false; 
				for($i = 1; $i <= $anz_pages; $i++)
				{
					$show = false; 
					if($i == 1) $show = true; 
					if($i == $pageNo || $i == $pageNo - 1 || $i == $pageNo - 2 || $i == $pageNo + 1 || $i == $pageNo + 2) $show = true; 
					if($i == $anz_pages) $show = true; 
					if($show)
					{
						$html .= "<li";
						if($pageNo == $i) $html .= " class='active'"; 
						$html .= "><a href='index.php?page=members&u=$uId&pageNo=$i'>$i</a></li>";  
						$sentPoints = false; 
					}
					else
					{
						if(!$sentPoints) 
						{
							$html .= "<li><a>...</a></li>";
							$sentPoints = true; 
						}
					}
				}
				$html .= "</ul><div class='clear'></div>"; 
			}

			$html .= "<div class='uprofile_over'>";

				if(Control::$logged) # Neuer Eintrag 
				{
					$html .= "<div class='uprofile_answer'>"; 

						$html .= "<form action='index.php?page=members&u=$uId&sendComment' method='post'>
							<textarea name='comment' class='uprofile_answer_textarea' placeholder='". htmlspecialchars(\Main\Language::$txt["forms"]["uprofile_write_comment_placeholder"], ENT_QUOTES) ."'></textarea><br />
							<button class='btn btn-primary'>".\Main\Language::$txt["buttons"]["send"]."</button>
						</form>"; 

					$html .= "</div>";  
				}

				# Beiträge anzeigen 
				$rst = \Main\DB::select("user_profile", "id, sender, text, time", "user='".\Main\DB::escape($uId)."'", $limit, "id DESC"); 
				while($row = $rst->fetch_object())
				{
					$eintrag = array("id" => $row->id,
						"sender" => $row->sender,
						"text" => $row->text,
						"time" => $row->time
					);
					$senderData = Control::getUserData($eintrag["sender"]); 

					# Todo Beiträge Kommentieren können (eingeloggt) 
					# Todo Kommentare zu Beiträgen anzeigen 

					$html .= "<div class='uprofile_entry'>";
						$html .= "<a name='C".$eintrag["id"]."'></a>"; 
						$html .= "<strong><a href='index.php?page=members&u=".$senderData["dbid"]."'>".htmlspecialchars($senderData["displayname"])."</a></strong><br />" . htmlspecialchars($eintrag["text"]) . "<br />"; 
						$html .= self::showProfileCommentComments($eintrag["id"]); 
						if(Control::$logged) {
							$html .= "<small><a href='#C".$eintrag["id"]."' onclick='showUserProfileCommentID(".$eintrag["id"].");'>".\Main\Language::$txt["buttons"]["answer"]."</a></small><br />
							<div id='uprofile_answer_".$eintrag["id"]."' class='uprofile_uentry' style='display:none;'><form action='index.php?page=members&u=".$uData["dbid"]."&sendAnswer' method='post'>
								<input type='hidden' name='entryID' value='".$eintrag["id"]."' />
								<textarea name='answer' class='uprofile_answer_textarea' placeholder='". htmlspecialchars(\Main\Language::$txt["forms"]["uprofile_write_comment_placeholder"], ENT_QUOTES) ."'></textarea>
								<div class='right'><button class='btn btn-primary'>".\Main\Language::$txt["buttons"]["send"]."</button></div>
								<div class='clear'></div>
							</form></div>"; 
						}
					$html .= "</div>";  

					

					if($ownProfile) # Alerts abhacken 
					{
						$sql = "UPDATE alerts SET gesehen='1' WHERE user='".\Main\DB::escape(Control::$dbid)."' AND typ='3' AND post='".\Main\DB::escape($eintrag["id"])."'"; 
						\Main\DB::query($sql); 
					}
				}

			$html .= "</div>";  


		$html .= "</div>"; # panel-user-sp2 
		$html .= "</div>";  
		

		return $html; 
	}

	/**
	* Benutzerprofil Kommentare (Antworten)
	*
	* Diese Methode zeigt alle Antworten die auf ein Kommentar auf einer Benutzerpinnwand hinterlegt wurden. 
	*
	* @author s-l 
	* @version 0.0.1 
	* @return string 
	*/
	private static function showProfileCommentComments($cId) 
	{
		$html = ""; 

		$rst = \Main\DB::select("user_profile_comments", "id, user, message, time, deleted", "post='".\Main\DB::escape($cId)."'", null, "id");
		while($row = $rst->fetch_object())
		{
			$comment = array(
				"id" => $row->id,
				"user" => $row->user,
				"message" => $row->message,
				"time" => $row->time,
				"deleted" => $row->deleted
			);
			if($comment["deleted"] == 1) continue; 
			$comment["user"] = Control::getUserData($comment["user"]); 
			$html .= "<div class='uprofile_uentry'>";
			$html .= "<strong><a href='index.php?page=members&u=".$comment["user"]["dbid"]."'>".htmlspecialchars($comment["user"]["displayname"])."</a></strong><br />" . htmlspecialchars($comment["message"]);
			$html .= "</div>"; 
		}


		return $html; 
	}
	
	/**
	* "UCP" im Hauptmenü
	*
	* Sollte ursprünglich die Benachrichtigungsfenster in einem eigenen Feld im Hauptmenü anzeigen und per Ajax darauf
	* zugegriffen werden. Wurde entfernt, da die Benachrichtigungen nun über das normale UCP angezeigt werden. 
	* 
	* @author s-l 
	* @version 0.0.2 
	*/
	public static function showMenueBar()
	{
		$html = ""; 

		if(!\Main\User\Control::$logged) return $html; 


		$html .= "<div id='user_alerts' style='display:none;' class='user_alerts_window_over'>
			<div class='heading'>".\Main\Language::$txt["infos"]["usr_alerts_header"]."</div>
			<div id='user_alertss'></div>
		</div>"; 

		$html .= "<div id='user_messages' style='display:none;' class='user_alerts_window_over msgwindow'>
			<div class='heading'>".\Main\Language::$txt["infos"]["usr_msgs_header"]."</div>
			<div id='user_msgs'></div>
		</div>"; 

		$html .= "<div class='user_control_bar_mright'>
			<span onclick='toggle_user_alerts();'><i class='fa fa-bell fa-lg'></i></span> 
			<span onclick='toggle_user_msg();'><i class='fa fa-envelope-o fa-lg'></i></span>
		</div>"; 


		return $html; 
	}

	/**
	* Benachrichtigungen
	*
	* Zeigt alle Benachrichtigungen von dem Benutzer an. 
	*
	* @author s-l 
	* @version 0.0.5 
	*/
	public static function showAlertsMain() 
	{
		$html = ""; 

		// Eine Benachrichtigung abchecken
		if(isset($_GET["checkAlert"]))
		{
			\Main\User\Alert::checkUserAlert(\Main\User\Control::$dbid, $_GET["checkAlert"]); 
		}

		$html .= "<h2>".\Main\Language::$txt["infos"]["alerts_header"]."</h2>";

		$rst = \Main\DB::select("alerts", "id", "user='".\Main\DB::escape(\Main\User\Control::$dbid)."'", "100");
		$anz_alerts = $rst->num_rows; 
		$alerts_per_page = 20; 

		$anz_pages = ceil($anz_alerts / $alerts_per_page); 
		if($anz_pages < 1) $anz_pages = 1; 
		$pageNo = 1; 

		if(isset($_GET["pageNo"])) $pageNo = $_GET["pageNo"]; 

		if($pageNo > $anz_pages) $pageNo = $anz_pages; 

		$startlimit = ($pageNo * $alerts_per_page) - $alerts_per_page; 
		$limit = $startlimit . ", " . $alerts_per_page; 

		// Navigation 
		if($anz_pages > 1) 
		{
			$html .= "<ul class='pagination'>"; 
			$showPoints = false; 
				for($i = 1; $i <= $anz_pages; $i++) 
				{	
					$show = false; 
					if($i == 1) $show = true; 
					if($i == $anz_pages) $show = true; 
					if($i == $pageNo || $i == $pageNo - 1 || $i == $pageNo - 2 || $i == $pageNo + 1 || $i == $pageNo + 2) $show = true; 
					if($show)
					{
						$html .= "<li ";
						if($pageNo == $i) $html .= "class='active'"; 
						$html .= "><a href='index.php?page=alerts&pageNo=$i'>$i</a></li>";
						$showPoints = false; 
					}
					else
					{
						if(!$showPoints) 
						{
							$showPoints = true; 
							$html .= "<li><a>...</a></li>"; 
						}
					}
				}
			$html .= "</ul>";
			$html .= "<div class='clear'></div>"; 
		}

		

		$html .= "<div class='panel panel-primary panel-margin-top panel-alerts'>";
			$html .= "<div class='panel-heading'>".\Main\Language::$txt["infos"]["alerts_header"]."</div>"; 
			$html .= "<div class='panel-body'>";

			// Benachrichtigungen anzeigen 
			$rst = \Main\DB::select("alerts", "id, theme, post, time, typ, gesehen", "user='".\Main\DB::escape(\Main\User\Control::$dbid)."'", $limit, "id DESC");
			while($row = $rst->fetch_object())
			{
				$alert = array("id" => $row->id,
					"theme" => $row->theme,
					"post" => $row->post,
					"typ" => $row->typ,
					"gesehen" => $row->gesehen
					);

				$user1 = ""; 
				$user2 = ""; 
				$user3 = ""; 

				$and = \Main\Language::$txt["user_alerts"]["word_and"]; 
				$more = \Main\Language::$txt["user_alerts"]["word_more"]; 

				$result = \Main\DB::select("alert_users", "user", "alert='".\Main\DB::escape($alert["id"])."'");
				$anz_users = $result->num_rows; 
				while($roww = $result->fetch_object())
				{
					$uid = $roww->user; 
					$data = \Main\User\Control::getUserData($uid); 
					if($user1 == "") 
					{
						$user1 = "<a href='index.php?page=members&u=$uid'>" . htmlspecialchars($data["displayname"]) . "</a>"; 
						continue; 
					}
					if($user2 == "" && $anz_users == 2)  
					{
						$user2 = " $and " . "<a href='index.php?page=members&u=$uid'>" . htmlspecialchars($data["displayname"]) . "</a>"; 
						continue; 
					}
					if($user2 == "" && $anz_users > 2) 
					{
						$user2 = ", " . "<a href='index.php?page=members&u=$uid'>" . htmlspecialchars($data["displayname"]) . "</a>"; 
						continue; 
					}
					if($user3 == "" && $anz_users == 3) 
					{
						$user3 = " $and " . "<a href='index.php?page=members&u=$uid'>" . htmlspecialchars($data["displayname"]) . "</a>"; 
						continue; 
					}
					if($user3 == "" && $anz_users > 3) 
					{
						$xwe = $anz_users - 2; 
						$user3 = " $and <strong>$xwe</strong> $more"; 
						continue; 
					}
				}

				$nstring = ""; 
				if($alert["typ"] == 1 || $alert["typ"] == 2) 
				{
					$tname = ""; 
					$result = \Main\DB::select("themen", "name", "id='".\Main\DB::escape($alert["theme"])."'");
					if($result->num_rows > 0) 
					{
						$roww = $result->fetch_object(); 
						$tname = $roww->name; 
					}

					if($anz_users == 1) 
						$nstring = \Main\Language::$txt["user_alerts"]["typ1_one_user"]; 
					else 
						$nstring = \Main\Language::$txt["user_alerts"]["typ1_more_users"]; 
					$nstring = str_replace("[user]", $user1.$user2.$user3, $nstring); 
					$nstring = str_replace("[theme]", "<a href='index.php?page=board&t=".$alert["theme"]."&alert=".$alert["id"]."'>" . htmlspecialchars($tname) . "</a>", $nstring); 
				}
				else if($alert["typ"] == 3) # Benutzer hat einen Beitrag auf deine Pinnwand verfasst 
				{
					$nstring = \Main\Language::$txt["user_alerts"]["typ3_one_user"]; 
					$nstring = str_replace("[user]", $user1, $nstring);
					$nstring = str_replace("[link]", "<a href='index.php?page=members&u=".Control::$dbid."#C".$alert["post"]."'>", $nstring); 
				}
				else if($alert["typ"] == 4) # Benutzer hat auf einen Beitrag auf deiner Pinnwand geantwortet 
				{
					if($anz_users == 1) 
						$nstring = \Main\Language::$txt["user_alerts"]["typ4_one_user"];
					else
						$nstring = \Main\Language::$txt["user_alerts"]["typ4_more_users"]; 
					$nstring = str_replace("[user]", $user1.$user2.$user3, $nstring);
					$nstring = str_replace("[link]", "<a href='index.php?page=members&u=".Control::$dbid."#C".$alert["post"]."'>", $nstring);
				}
				else if($alert["typ"] == 5) # der Benutzer hat auf deinen Beitrag auf Benutzers Pinnwand geantwortet. 
				{
					$result = \Main\DB::select("user_profile", "sender", "id='".\Main\DB::escape($alert["post"])."'");
					if($result->num_rows > 0) 
					{
						$roww = $result->fetch_object(); 
						$sender = $roww->sender; 
						$sData = Control::getUserData($sender); 
						if($anz_users == 1) 
							$nstring = \Main\Language::$txt["user_alerts"]["typ5_one_user"]; 
						else
							$nstring = \Main\Language::$txt["user_alerts"]["typ5_more_users"];
						$nstring = str_replace("[user]", $user1.$user2.$user3, $nstring); 
						$nstring = str_replace("[link]", "<a href='index.php?page=members&u=$sender#C".$alert["post"]."'>", $nstring);
						$nstring = str_replace("[user2]", htmlspecialchars($sData["displayname"]), $nstring);
					} 
				}

				$class_zus = ""; 
				if($alert["gesehen"] == 0) 
					$class_zus = "unseen"; 

				$html .= "<div class='user_alert $class_zus'>";

					if($alert["gesehen"] == 0) 
						$html .= "<div class='right'><a href='index.php?page=alerts&pageNo=$pageNo&checkAlert=".$alert["id"]."'><i class='fa fa-check fa-lg'></i></a></div>"; 

					$html .= $nstring; 
				$html .= "</div>";  
			}

			$html .= "</div>"; 
		$html .= "</div>";  


		// Navigation 
		if($anz_pages > 1) 
		{
			$html .= "<div class='clear'></div>"; 
			$html .= "<ul class='pagination'>"; 
				for($i = 1; $i <= $anz_pages; $i++) 
				{	
					$html .= "<li ";
					if($pageNo == $i) $html .= "class='active'"; 
					$html .= "><a href='index.php?page=alerts&pageNo=$i'>$i</a></li>";
				}
			$html .= "</ul>";
			$html .= "<div class='clear'></div>"; 
		}

		return $html; 
	}

}


?>