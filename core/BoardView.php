<?php
namespace Main\Board; 
class View
{
	/**
	* Hauptverteiler
	*
	* Hier kommt zuerst mal alles mit page=board vorbei. Wird von dieser Methode dann auf die jeweilige nächste
	* Methode weitergeleitet. Die Hauptseite (Startseite) wird direkt über diese Methode angezeigt. 
	* 
	* @author s-l 
	* @version 0.4.2 
	* @todo 
	*/
	public static function showBoardMain()
	{
		$html = ""; 
		
		$showhome = 1; 
		
		if(isset($_GET["b"])) // Ein Forum anzeigen
		{
			$rst = \Main\DB::select("foren", "name, description", "id='".\Main\DB::escape($_GET["b"])."'");
			if($rst->num_rows > 0 && \Main\User\Control::IsUserAllowedToSeeForum(\Main\User\Control::$dbid, $_GET["b"])) 
			{
				$showhome = 0; 
				
				$row = $rst->fetch_object(); 
				$forum = array(
					"id" => $_GET["b"],
					"name" => $row->name,
					"desc" => $row->description
				);
				
				$html .= self::makeForumNavi(); 
				
				$html .= "<h2>".htmlspecialchars($forum["name"])."</h2>
				<p>".htmlspecialchars($forum["desc"])."</p>";
				
				$html .= "<div class='panel panel-primary panel-forum panel-margin-top'>";
					
					$html .= "<div class='panel-body'>
						".self::showForumKats($forum["id"])."
					</div>"; 
					
				$html .= "</div>"; 
			}
		}
		
		if(isset($_GET["k"])) // Eine Kategorie anzeigen
		{
			$rst = \Main\DB::select("kategorien", "name, description", "id='".\Main\DB::escape($_GET["k"])."'");
			if($rst->num_rows > 0 && \Main\User\Control::IsUserAllowedToSeeKategory(\Main\User\Control::$dbid, $_GET["k"])) 
			{
				$showhome = 0; 
				
				$row = $rst->fetch_object(); 
				$kategorie = array(
					"id" => $_GET["k"],
					"name" => $row->name,
					"desc" => $row->description
				);
				
				$showmain = 1; 
				
				// Neues Thema erstellen
				if(isset($_GET["new_t"]) && \Main\User\Control::IsUserAllowedToWriteKategory(\Main\User\Control::$dbid, $kategorie["id"]))
				{
					$showmain = 0; 
					
					$input = array(
						"title" => "",
						"message" => ""
					);
					
					$showform = 1; 
					
					# Erstellen
					if(isset($_GET["sendt"], $_POST["title"])) 
					{
						$input = array(
							"title" => ltrim(rtrim($_POST["title"])),
							"message" => ltrim(rtrim($_POST["message"]))
						);
						
						$btnaction = $_POST["btnaction"]; 
						
						if($btnaction == "preview") // VORSCHAU
						{
							$html .= "<div class='panel panel-default panel-new-theme-preview'>
								".$input["message"]."
							</div>";
						}
						else // THEMA SENDEN
						{
							if(strlen($input["title"]) < 3 || strlen($input["title"]) > 120) // Länge Name
							{
								$html .= "<div class='alert alert-danger redbox'>
									".\Main\Language::$txt["alerts"]["new_theme_strlen_title"]."
								</div>";
							}
							else
							{
								if(strlen($input["message"]) < 10 || strlen($input["message"]) > 10000)  // Länge Nachricht
								{
									$html .= "<div class='alert alert-danger redbox'>
										".\Main\Language::$txt["alerts"]["new_theme_strlen_message"]."
									</div>";
								}
								else
								{
									$tag = 0; 

									if(isset($_POST["tag"]))
									{
										$tag = $_POST["tag"]; 
									}

									\Main\DB::insert("themen", array(
										"name" => $input["title"],
										"user" => \Main\User\Control::$dbid,
										"startTime" => time(),
										"lastChange" => time(),
										"kategorie" => $kategorie["id"],
										"tag" => $tag
									));
									
									$new_tId = \Main\DB::$insertID; 
									
									\Main\DB::insert("posts", array(
										"user" => \Main\User\Control::$dbid,
										"message" => $input["message"],
										"thema" => $new_tId,
										"startTime" => time()
									));

									$new_pId = \Main\DB::$insertID; 

									$has_labels = \Main\Board\Control::countNewThemaAvailableLabels($kategorie["id"]); 
									if($has_labels > 0) // Labels setzen
									{
										$rst = \Main\DB::select("tags", "id, name", "typ='1'");
										while($row = $rst->fetch_object())
										{
											$tag = array("id" => $row->id,
														"name" => $row->name);
											if(\Main\Board\Control::TagAvailableInKategory($tag["id"], $kategorie["id"]))
											{
												if(\Main\Board\Control::TagAvailableForUser($tag["id"]))
												{
													if(isset($_POST["tag_" . $tag["id"]]))
													{
														if($_POST["tag_" . $tag["id"]] == 1)
														{
															\Main\Board\Control::setLabelForTheme($new_tId, $tag["id"]); 
														}
													}
												}
											}
										}
									}

									if(isset($_FILES["files"])) # Dateiupload
									{
										$anz_files = count($_FILES["files"]["name"]); 
										for($i = 0; $i < $anz_files; $i++) 
										{
											$tmpPath = $_FILES["files"]["tmp_name"][$i]; 
											if($tmpPath != "") 
											{
												$newPath = "media/upload/"; 

												$newFilePath = $newPath . $_FILES["files"]["name"][$i]; 
												$fileName = $_FILES["files"]["name"][$i]; 
												$stackvar = 0; 
												while(file_exists($newFilePath)) # Leeren Platz finden 
												{
													$stackvar++;
													$newFilePath = $newPath . $stackvar . $_FILES["files"]["name"][$i]; 
													$fileName = $stackvar . $_FILES["files"]["name"][$i]; 
												}

												if(move_uploaded_file($tmpPath, $newFilePath)) # Hochladen 
												{
													\Main\DB::insert("post_files", array("post" => $new_pId, "file" => $fileName));
												}
											}
										}
									}
									
									$html .= "<div class='alert alert-success greenbox'>
										".\Main\Language::$txt["alerts"]["new_theme_success"]."
									</div>";
									
									$showform = 0; 
									$_GET["t"] = $new_tId; 
									header("Location: index.php?page=board&t=" . $new_tId . "#post" . $new_pId); # Weiterleitung 
								}
							}
						}
					}
					
					# Form anzeigen 
					if($showform == 1) 
					{
						$has_tags = \Main\Board\Control::countNewThemaAvailableTags($kategorie["id"]); 
						$has_labels = \Main\Board\Control::countNewThemaAvailableLabels($kategorie["id"]); 
					
					
						$html .= "<h2>".\Main\Language::$txt["infos"]["new_theme_header"]."</h2>"; 
						$html .= \Main\Language::$txt["infos"]["new_theme_kat"]." ".htmlspecialchars($kategorie["name"]);
						
						$html .= "<div class='panel panel-default panel-margin-top panel-new-theme'>
							<form action='index.php?page=board&k=".$kategorie["id"]."&new_t&sendt' method='post' class='form-horizontal' enctype='multipart/form-data'>
							
							<div class='form-group'>
								<div class='col-sm-12'>
									<input class='form-control' type='text' name='title' placeholder='".\Main\Language::$txt["forms"]["new_theme_name"]."' value='".htmlspecialchars($input["title"], ENT_QUOTES)."' required />
								</div>
							</div>"; 
							
							if($has_tags > 0) 
							{
								$html .= "<div class='form-group'>
									<div class='col-sm-2'>
										".\Main\Language::$txt["forms"]["tag::"]."
									</div>
									<div class='col-sm-10'>
										<select name='tag'>
											<option value='0'>".\Main\Language::$txt["forms"]["tag_no_tag"]."</option>
											".\Main\Board\Control::listTagOptionsForKategory($kategorie["id"])."
										</select>
									</div>
								</div>"; 
							}
							
							if($has_labels > 0) 
							{
								$html .= "<div class='form-group'>
									<div class='col-sm-2'>
										".\Main\Language::$txt["forms"]["labels::"]." 
									</div>
									<div class='col-sm-10'>
										".\Main\Board\Control::listLabelOptionsForKategory($kategorie["id"])."
									</div>	
								</div>"; 
							}
							
							$html .= "<textarea class='new_t' id='ckedit' name='message'>".$input["message"]."</textarea>
							<script>CKEDITOR.replace('ckedit');</script>
							
							<div class='editor-upload'>
								<div class='item'><a onclick='toggledateianhaenge();'>". \Main\Language::$txt["buttons"]["dataupload"] ."</a></div>
							</div>
							<div class='editor-upload' id='ckeditor_dateiupload' style='display:none;'>
								<input type='file' name='files[]' multiple /> 
							</div>

							<div class='textright panel-margin-top'>
								<button name='btnaction' value='preview' class='btn btn-default'>".\Main\Language::$txt["buttons"]["new_theme_preview"]."</button>
								<button name='btnaction' value='send' class='btn btn-primary'>".\Main\Language::$txt["buttons"]["new_theme_send"]."</button>
							</div>
							
							</form>
						</div>";
					}
				}
				
				// Kategorie Anzeigen
				if($showmain == 1) 
				{
					$html .= self::makeKategorieNavi($kategorie["id"]); 
					$html .= "<h2>".htmlspecialchars($kategorie["name"])."</h2>
					<p>".htmlspecialchars($kategorie["desc"])."</p>"; 
					
					$katsstring = self::showKatKats($kategorie["id"]); 
					if($katsstring != "") 
					{
						$html .= "<div class='panel panel-primary panel-forum panel-margin-top'>";
							$html .= "<div class='panel-body'>";
								$html .= $katsstring; 
							$html .= "</div>"; 
						$html .= "</div>"; 
					}
					

					$html .= self::showKatThemes($kategorie["id"]); 
				}
			}
		}
		
		if(isset($_GET["t"])) // Ein Thema anzeigen
		{
			$rst = \Main\DB::select("themen", "name, user, startTime, kategorie, tag, closed", "id='".\Main\DB::escape($_GET["t"])."'");
			if($rst->num_rows > 0) 
			{
				$row = $rst->fetch_object(); 
				$thema = array(
					"id" => $_GET["t"],
					"name" => $row->name,
					"user" => $row->user,
					"startTime" => $row->startTime,
					"kategorie" => $row->kategorie,
					"tag" => $row->tag,
					"closed" => $row->closed
				);
				
				if(\Main\User\Control::IsUserAllowedToSeeKategory(\Main\User\Control::$dbid, $thema["kategorie"]))
				{
					$showhome = 0; 
					
					
					
					if(\Main\User\Control::$logged == 1) 
					{
						$rst = \Main\DB::select("themes_seen", "id", "user='".\Main\DB::escape(\Main\User\Control::$dbid)."' AND thema='".\Main\DB::escape($thema["id"])."'");
						if($rst->num_rows > 0) 
						{
							$row = $rst->fetch_object(); 
							$tsid = $row->id; 
							\Main\DB::update("themes_seen", $tsid, array("stamp" => time()));
						}
						else
						{
							\Main\DB::insert("themes_seen", array(
								"user" => \Main\User\Control::$dbid,
								"thema" => $thema["id"],
								"stamp" => time()
							)); 
						}

						if(isset($_GET["alert"]))
						{
							\Main\User\Alert::checkUserAlert(\Main\User\Control::$dbid, $_GET["alert"]); 
						}
					}

					$show_t = 1; 

					if(isset($_GET["editt"]) && (\Main\User\Control::$data["arights"]["edit_themes"] || \Main\User\Control::$data["arights"]["tag_themes"] || \Main\User\Control::$dbid == $thema["user"]))
					{
						$show_t = 0; 
						$show_edit = 1; 

						// Thema bearbeiten 
						$allow_edit = 0; 
						$allow_tag = 0; 

						if(\Main\User\Control::$data["arights"]["edit_themes"] || \Main\User\Control::$dbid == $thema["user"])
						{
							$allow_edit = 1; 
						}
						if(\Main\User\Control::$data["arights"]["tag_themes"] || \Main\User\Control::$dbid == $thema["user"])
						{
							$allow_tag = 1; 
						}

						$count_tags = \Main\Board\Control::countNewThemaAvailableTags($thema["kategorie"]);
						$count_labels = \Main\Board\Control::countNewThemaAvailableLabels($thema["kategorie"]); 

						if(isset($_GET["save"])) // Thema speichern 
						{
							$update = array(); 
							if($allow_edit == 1) 
							{
								$update["name"] = $_POST["name"]; 
							}
							if($allow_tag == 1) 
							{
								if($count_tags > 0) 
									$update["tag"] = $_POST["tag"]; 
								else
									$update["tag"] = 0; 

								if($count_labels > 0) 
								{
									// Labels durchgehen und bei bedarf welche löschen / addieren 
									$rst = \Main\DB::select("themen_labels", "id, label", "thema='".\Main\DB::escape($thema["id"])."'");
									while($row = $rst->fetch_object())
									{
										$lbid = $row->id; 
										$label = $row->label; 
										if(!isset($_POST["tag_" . $label]))
										{
											\Main\DB::delete("themen_labels", $lbid); 
										}
									}

									$rst = \Main\DB::select("tags", "id");
									while($row = $rst->fetch_object())
									{
										$tid = $row->id; 
										if(isset($_POST["tag_".$tid]))
										{
											$result = \Main\DB::select("themen_labels", "id", "label='".\Main\DB::escape($tid)."' AND thema='".\Main\DB::escape($thema["id"])."'");
											if($result->num_rows == 0) 
											{
												\Main\DB::insert("themen_labels", array("thema" => $thema["id"], "label" => $tid));
											}
										}
									}
								}
							}

							\Main\DB::update("themen", $thema["id"], $update);
							if(isset($update["name"]))
								$thema["name"] = $update["name"]; 
							if(isset($update["tag"]))
								$thema["tag"] = $update["tag"]; 

							$html .= "<div class='alert alert-success'>".\Main\Language::$txt["alerts"]["theme_edit_saved"]."</div>"; 
							$show_edit = 0; 
							$show_t = 1; 
						}

						if($show_edit) // Form anzeigen 
						{
							$html .= "<br /><form action='index.php?page=board&t=".$thema["id"]."&editt&save' method='post'>
								<p><input type='text' name='name' class='form-control' value='".htmlspecialchars($thema["name"], ENT_QUOTES)."' /></p>"; 

								if($count_tags > 0) 
								{
									$disabled = ""; 
									if($allow_tag == 0) $disabled = "disabled"; 
									$html .= "<p><select id='selectbox_edit_theme' class='form-control' name='tag' $disabled><option value='0'>".\Main\Language::$txt["forms"]["tag_no_tag"]."</option>".\Main\Board\Control::listTagOptionsForKategory($thema["kategorie"])."</select></p>"; 

									if($thema["tag"] != 0) 
									{
										// Den Tag in der Select Box richtig setzen 
										$html .= "<script>setEditThemeSelectBoxTag(".$thema["tag"].");</script>"; 
									}
								}

								if($count_labels > 0) 
								{
									if($allow_tag == 1) 
									{
										$html .= "<p>";
										$html .= \Main\Board\Control::listLabelOptionsForKategory($thema["kategorie"], true, $thema["id"]); 
										$html .= "</p>";
									}
								}

								$html .= "<p><button class='btn btn-primary floatright'>".\Main\Language::$txt["buttons"]["save"]."</button></p>"; 

							$html .= "</form>"; 
						}
					}

					if(isset($_GET["close"]) && (\Main\User\Control::$data["arights"]["close_themes"] || \Main\User\Control::$dbid == $thema["user"]))
					{
						// Thema schließen
						$todo = 1; 
						if($thema["closed"] == 1) $todo = 0; 

						\Main\DB::update("themen", $thema["id"], array("closed" => $todo));
						$thema["closed"] = $todo; 
						if($todo == 1) 
							$html .= "<div class='alert alert-danger'>".\Main\Language::$txt["alerts"]["theme_closed_success"]."</div>"; 
						else
							$html .= "<div class='alert alert-success'>".\Main\Language::$txt["alerts"]["theme_opened_success"]."</div>"; 
					}

					if(isset($_GET["move"]) && \Main\User\Control::$data["arights"]["move_themes"]) // TODO 
					{
						$show_t = 0; 
						$show_move = 1; 

						if(isset($_GET["do"], $_POST["position"])) // Position Ändern
						{
							$pos = $_POST["position"]; 
							$pos_prefix = substr($pos, 0, 1); 
							$pos_id = substr($pos, 1); 
							if($pos_prefix == "b")
							{
								// Redbox: Du kannst es nicht in ein Forum schieben 
								$html .= "<div class='alert alert-danger'>".\Main\Language::$txt["alerts"]["theme_move_not_in_board"]."</div>"; 
							}
							else
							{
								$show_move = 0; 
								// Verschieben 

								\Main\DB::update("themen", $thema["id"], array("kategorie" => $pos_id));
								$thema["kategorie"] = $pos_id; 
								$show_t = 1; 
								$html .= "<div class='alert alert-success'>".\Main\Language::$txt["alerts"]["theme_move_success"]."</div>";
							}
						}

						if($show_move) 
						{
							$hda = \Main\Language::$txt["infos"]["theme_move_where"]; 
							$hda = str_replace("[theme]", htmlspecialchars($thema["name"]), $hda); 
							$html .= "<h3>".$hda."</h3>"; 
							$html .= "<br  />"; 
							$html .= "<form action='index.php?page=board&t=".$thema["id"]."&move&do' method='post'>
								<select id='selbox_move_theme' name='position' class='form-control'>
									".\Main\Board\Control::listBoardOptions()."
								</select>
								<br />
								<button class='btn btn-primary floatright'>".\Main\Language::$txt["buttons"]["save"]."</button>
							</form>
							<script>
								setMoveThemeSelectBoxPos('k".$thema["kategorie"]."');
							</script>"; 
						}
					}

					if($show_t) // Das normale Thema anzeigen 
					{
						Control::hitThema($thema["id"]);
						$html .= self::makeKategorieNavi($thema["kategorie"], 0); 
						$uData = \Main\User\Control::getUserData($thema["user"]); 
						
						$html .= "<h2><a class='theme_header_a' href='index.php?page=board&t=".$thema["id"]."'>".htmlspecialchars($thema["name"])."</a></h2>";
						$html .= \Main\Language::$txt["infos"]["theme_started_by"] . " <a href='index.php?page=members&u=".$thema["user"]."'>" . htmlspecialchars($uData["displayname"]) . "</a><div class='clear'></div>"; 
						
						if(\Main\User\Control::$data["arights"]["edit_themes"] || \Main\User\Control::$data["arights"]["tag_themes"] || (\Main\User\Control::$dbid == $thema["user"] && $thema["closed"] == 0))
						{
							//$html .= "<a href='index.php?page=board&t=".$thema["id"]."&edit'><i class='fa fa-pencil fa-lg'></i></a> "; 
							$html .= "<a href='index.php?page=board&t=".$thema["id"]."&editt'><button class='btn btn-primary btn-margin-bot'>".\Main\Language::$txt["buttons"]["edit_theme"]."</button></a> ";
						}
						if(\Main\User\Control::$data["arights"]["move_themes"])
						{
							$html .= "<a href='index.php?page=board&t=".$thema["id"]."&move'><button class='btn btn-primary btn-margin-bot'>".\Main\Language::$txt["buttons"]["move_theme"]."</button></a> "; 
						}
						if(\Main\User\Control::$data["arights"]["close_themes"] || (\Main\User\Control::$dbid == $thema["user"] && $thema["closed"] == 0))
						{
							//$html .= "<a href='index.php?page=board&t=".$thema["id"]."&close'><i class='fa fa-times fa-lg'></i></a> "; 
							if($thema["closed"] == 0)
								$html .= "<a href='index.php?page=board&t=".$thema["id"]."&close'><button class='btn btn-danger btn-margin-bot'>".\Main\Language::$txt["buttons"]["close_theme"]."</button></a> "; 
							else
								$html .= "<a href='index.php?page=board&t=".$thema["id"]."&close'><button class='btn btn-success btn-margin-bot'>".\Main\Language::$txt["buttons"]["open_theme"]."</button></a> "; 
						}

						$html .= self::showThemaPosts($thema["id"]); 
					}
				}
			}
		}
		
		
		if($showhome == 1)  // Alles Anzeigen
		{
			
			$html .= "<h2 class='board_title_h2'>".\Main\Board\Settings::$settings["forum_title"]."</h2>".\Main\Board\Settings::$settings["forum_description"];
			
			$html .= "<div class='panel-margin-top'></div>";
			$html .= \Main\Plugins::hook("BoardView.showBoardMain.displayFull.beforeBoards", "");
			$mainKats = self::showForumKats(0); 
			if($mainKats != "") 
			{
				$html .= "<div class='panel panel-primary panel-forum'>";
					$html .= "<div class='panel-body'>";
						$html .= $mainKats; 
					$html .= "</div>"; 
				$html .= "</div>"; 
			}
			
			// Alle Foren anzeigen
			$rst = \Main\DB::select("foren", "id, name, description", null, null, "orderId, id");
			while($row = $rst->fetch_object())
			{
				$forum = array(
					"id" => $row->id,
					"name" => $row->name,
					"desc" => $row->description
				);
				
				if(\Main\User\Control::IsUserAllowedToSeeForum(\Main\User\Control::$dbid, $forum["id"]))
				{
				
					$html .= "<div class='panel panel-primary panel-forum'>";
						$html .= "<div class='panel-heading'><a href='index.php?page=board&b=".$forum["id"]."'>".htmlspecialchars($forum["name"])."</a>";
						if($forum["desc"] != "") $html .= "<p>".htmlspecialchars($forum["desc"])."</p>"; 
						$html .= "</div>";
						
						$html .= "<div class='panel-body'>
							".self::showForumKats($forum["id"])."
						</div>"; 
						
					$html .= "</div>";
					
				}				
			}
		}
		
		return $html; 
	}
	
	/**
	* Beiträge anzeigen
	*
	* Zeigt alle Beiträge von einem Thema an. Zusätzlich werden über diese Methode Dinge wie das Antworten, löschen, etc verwaltet.
	* Update 18.04.17: 	Ändert die Location des Browsers damit der Benutzer nach einer neuen Antwort gleich bei seinem Post steht. 
	* 		  			Ändert ebenfalls die Seite falls notwendig. 
	* Update 19.04.17: 	Grundgerüst für Dateiupload - einfacher Upload möglich (noch keine Beschränkungen) 
	* Update 05.05.17:  Falsche verlinkung (pageNo wurde nicht berücksichtigt) beim Editieren/Löschen von Beiträgen
	* 
	* @author s-l 
	* @version 0.7.5
	* @todo Dateiupload prüfungen wie Dateityp, Größe etc 
	*/
	public static function showThemaPosts($tId) 
	{
		$html = ""; 
		
		$rst = \Main\DB::select("themen", "kategorie, closed", "id='".\Main\DB::escape($tId)."'");
		$row = $rst->fetch_object(); 
		$kId = $row->kategorie; 
		$tclosed = $row->closed; 
		
		# Antwort Posten 
		if(isset($_GET["send"], $_POST["nachricht"]) && \Main\User\Control::IsUserAllowedToWriteKategory(\Main\User\Control::$dbid, $kId))
		{
			$act = 1; 
			$msg = $_POST["nachricht"]; 
			if(isset($_SESSION["last_posted_thema"]))
			{
				if($_SESSION["last_posted_thema"] == $tId && $_SESSION["last_posted_msg"] == $msg)
					$act = 0; 
			}
			
			if($act == 1) 
			{
				$_SESSION["last_posted_thema"] = $tId; 
				$_SESSION["last_posted_msg"] = $msg; 
				\Main\DB::insert("posts", array(
					"thema" => $tId,
					"user" => \Main\User\Control::$dbid,
					"message" => $msg,
					"startTime" => time()
				));
				$insi = \Main\DB::$insertID; 
				
				\Main\DB::update("themen", $tId, array(
					"lastChange" => time() 
				));

				if(isset($_FILES["files"])) # Dateiupload 
				{
					$anz_files = count($_FILES["files"]["name"]); 
					for($i = 0; $i < $anz_files; $i++) 
					{
						$tmpPath = $_FILES["files"]["tmp_name"][$i]; 
						if($tmpPath != "") 
						{
							$newPath = "media/upload/"; 

							$newFilePath = $newPath . $_FILES["files"]["name"][$i]; 
							$fileName = $_FILES["files"]["name"][$i]; 
							$ext = substr($fileName, strlen($fileName)-3); 
							$stackvar = 0; 
							while(file_exists($newFilePath)) # Leeren Platz finden 
							{
								$stackvar++;
								$newFilePath = $newPath . $stackvar . $_FILES["files"]["name"][$i]; 
								$fileName = $stackvar . $_FILES["files"]["name"][$i]; 
							}

							if($ext != "php") # Schnell Lösung gegen die .php Attacken von Developer ;D 
							{
								if(move_uploaded_file($tmpPath, $newFilePath)) # Hochladen 
								{
									\Main\DB::insert("post_files", array("post" => $insi, "file" => $fileName));
								}
							}
						}
					}
				}
				
				$html .= "<div class='alert alert-success greenbox'>
					".\Main\Language::$txt["alerts"]["theme_answered"]."
				</div>";

				if(isset($_GET["pageNo"])) $pageNo = $_GET["pageNo"]; 
				else $pageNo = 1; 
				$use_nextpage = $_POST["next_page"]; 
				if($use_nextpage == 1) $pageNo ++; 
				\Main\User\Alert::UserAnsweredTheme(\Main\User\Control::$dbid, $tId); 
				header("Location: index.php?page=board&t=$tId&pageNo=$pageNo#post$insi"); # Zum richtigen Beitrag springen 
			}
			else
			{
				$html .= "<div class='alert alert-danger redbox'>
					".\Main\Language::$txt["alerts"]["theme_reanswered"]."
				</div>";
			}
		}
		
		
		
		$rst = \Main\DB::select("posts", "id", "thema='".\Main\DB::escape($tId)."'");
		$max_posts_per_page = 13; 
		$posts_ges = $rst->num_rows; 
		
		$max_pages = ceil($posts_ges / $max_posts_per_page); 
		
		if(isset($_GET["pageNo"])) $pageNo = $_GET["pageNo"]; 
		else $pageNo = 1; 
		
		if($pageNo > $max_pages) $pageNo = $max_pages; 
		
		$startlimit = ($pageNo * $max_posts_per_page) - $max_posts_per_page; 
		
		$limit = $startlimit . ", " . $max_posts_per_page; 
		
		# Navigation
		if($max_pages > 1) 
		{
			$html .= "<div class='clear'></div>";
			$html .= "<ul class='pagination'>"; 
				$showPoints = false; 
				for($i = 1; $i <= $max_pages; $i++) 
				{	
					$showi = false;
					if($i == 1 || $i == $max_pages) $showi = true; 
					if($i == $pageNo || $i == $pageNo - 1 || $i == $pageNo - 2 || $i == $pageNo + 1 || $i == $pageNo + 2) $showi = true; 

					if($showi) 
					{ 
						$html .= "<li ";
						if($pageNo == $i) $html .= "class='active'"; 
						$html .= "><a href='index.php?page=board&t=$tId&pageNo=$i'>$i</a></li>";
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
		
		$html .= "<div class='theme_posts'>";
		$rst = \Main\DB::select("posts", "id, user, message, startTime, edits, lastEditor, lastEditTime, deleted, deletedUser, deletedReason", "thema='".\Main\DB::escape($tId)."'", $limit, "startTime, id");
		$issec = false;
		$postcount = 0;  
		while($row = $rst->fetch_object())
		{
			$post = array(
				"id" => $row->id,
				"user" => $row->user,
				"message" => $row->message,
				"startTime" => $row->startTime,
				"edits" => $row->edits,
				"lastEditor" => $row->lastEditor,
				"lastEditTime" => $row->lastEditTime,
				"deleted" => $row->deleted,
				"deletedUser" => $row->deletedUser,
				"deletedReason" => $row->deletedReason
			);
			$postcount++; 
			$html .= "<a name='post".$post["id"]."'></a>"; 
			
			$zu2cl = ""; 
			if($issec == false) $issec = true; 
			else if($issec == true) 
			{
				$issec = false; 
				$zu2cl = "sec";
			}
			
			$uData = \Main\User\Control::getUserData($post["user"]); 
			
			// Rechte 
			$showadm = 0; 
			$showadmdel = 0; 
			$showadmedit = 0; 
			if(($post["user"] == \Main\User\Control::$dbid && \Main\User\Control::$logged == 1))
			{
				$showadm = 1; 
				$showadmdel = 1; 
				$showadmedit = 1; 
			}
			if(\Main\User\Control::$logged == 1 && (\Main\User\Control::$data["arights"]["edit_posts"] == 1 || \Main\User\Control::$data["arights"]["del_posts"] == 1))
			{
				$showadm = 1; 
				if(\Main\User\Control::$data["arights"]["edit_posts"] == 1) 
					$showadmedit = 1; 
				if(\Main\User\Control::$data["arights"]["del_posts"] == 1) 
					$showadmdel = 1; 
			}
			
			// Entgültig Löschen
			if(isset($_GET["dodel"], $_POST["postId"]) && $_POST["postId"] == $post["id"] && $showadmdel == 1)
			{
				$reason = $_POST["reason"]; 
				$btnaction = $_POST["btnaction"]; 
				
				if($btnaction == "ja") 
				{
					$post["deleted"] = 1; 
					$post["deletedUser"] = \Main\User\Control::$dbid; 
					$post["deletedReason"] = $reason; 
					\Main\DB::update("posts", $post["id"], array(
						"deleted" => 1,
						"deletedUser" => \Main\User\Control::$dbid,
						"deletedReason" => $reason
					));
					
					$html .= "<div class='alert alert-success greenbox post-margin-top'>
						".\Main\Language::$txt["alerts"]["post_deleted"]."
					</div>";
				}
			}
			
			// Bearbeiten Fertig
			if(isset($_GET["finish_edit"]) && $_GET["finish_edit"] == $post["id"] && $showadmedit == 1)
			{
				$post["message"] = $_POST["editstring"]; 
				$post["edits"] += 1; 
				$post["lastEditor"] = \Main\User\Control::$dbid; 
				$post["lastEditTime"] = time(); 
				\Main\DB::update("posts", $post["id"], array(
					"message" => $post["message"],
					"edits" => $post["edits"],
					"lastEditor" => $post["lastEditor"],
					"lastEditTime" => $post["lastEditTime"]
				)); 
				\Main\DB::update("themen", $tId, array("lastChange" => time()));
				
				$html .= "<div class='alert alert-success greenbox post-margin-top'>
					".\Main\Language::$txt["alerts"]["post_edit_success"]."
				</div>";
			}
			
			// Löschen Fragen
			$askdel = 0; 
			if(isset($_GET["askdel"]) && $_GET["askdel"] == $post["id"] && $showadmdel == 1)
			{
				$askdel = 1; 
			}
			
			if($post["deleted"] == 0) # Beitrag anzeigen 
			{
				$html .= "<a name='post".$post["id"]."'></a><div class='theme_post'>";
				
					$html .= "<div class='row'>";
					
						$html .= "<div class='col-md-3 postLeft $zu2cl'>";
							$onstring = ""; 
							if(\Main\User\Control::isUserOnline($post["user"]))
								$onstring = "<br /><span class='badge label onlabel'>".\Main\Language::$txt["infos"]["online_label"]."</span>";
							if($uData["avatar"] != "") 
								$html .= "<img src='".htmlspecialchars($uData["avatar"], ENT_QUOTES)."' class='img-thumbnail img-avatar-post' style='max-width:70%;max-height:200px;' /><br />";
							$html .= "<a class='user' href='index.php?page=members&u=".$post["user"]."'>".htmlspecialchars($uData["displayname"])."</a>$onstring<br />".$uData["rank"];
		 					$html .= "<br /><br />".\Main\Language::$txt["infos"]["post_left_member_since"]."<br />
												".\Main\toTime($uData["registerTime"])."<br />
												".\Main\Language::$txt["infos"]["post_left_posts"]." ".$uData["posts"].""; 
						$html .= "</div>"; 
						
						$html .= "<div class='col-md-9 postRight'>";
							$html .= "<small>".\Main\toTime($post["startTime"])."</small><br />";
							// Bearbeiten
							if(isset($_GET["edit"]) && $_GET["edit"] == $post["id"] && $showadmedit == 1)
							{
								$html .= "<div class='container-fluid container-post'>"; 
									$html .= "<form action='index.php?page=board&t=".$tId."&pageNo=$pageNo&finish_edit=".$post["id"]."#post".$post["id"]."' method='post'>
										<textarea id='ckedita' name='editstring'>".$post["message"]."</textarea><br />
										<div class='textright'><button class='btn btn-primary'>".\Main\Language::$txt["buttons"]["save"]."</button></div>
										<script>CKEDITOR.replace('ckedita');</script>
									</form>";
								$html .= "</div><br />";
							}
							// Beitrag anzeigen
							else
							{
								$post["message"] = self::preparePostMessageForShow($post["message"]); 
								$html .= "<div class='container-fluid container-post'>".$post["message"]."</div>"; 

								# Dateiuploads anzeigen 
								$has_files = false; 
								$rstt = \Main\DB::select("post_files", "file", "post='".\Main\DB::escape($post["id"])."'");
								if($rst->num_rows > 0) $has_files = true; 
								if($has_files) 
								{
									$html .= "<div class='container-fluid container-post-uploads'>";
									$dateistring = ""; 
									$picstring = "";  
									while($roww = $rstt->fetch_object())
									{
										$file = "media/upload/" . $roww->file;  
										if(!file_exists($file)) continue; 
										$filename = $roww->file; 
										$ext = pathinfo($file, PATHINFO_EXTENSION);
										$ext = strtolower($ext); 
										if($ext == "png" || $ext == "jpg" || $ext == "jpeg" || $ext == "gif") # Bilder anzeigen  
										{
											$picstring .= "<a href='".htmlspecialchars($file, ENT_QUOTES)."' target='_blank'><img src='".htmlspecialchars($file, ENT_QUOTES)."' style='max-width:180px;max-height:180px;' /></a> &nbsp;";
										} 
										else if($ext == "txt" || $ext == "log" || $ext == "ini" || $ext == "cfg") # Datei verlinken (Für Live-View)
										{
											$dateistring .= "<a href='".htmlspecialchars($file, ENT_QUOTES)."' target='_blank'>" . $filename . "</a> &nbsp;"; 
										}
										else if($ext == "php") 
										{
											$dateistring .= "NEIN"; 
										}
										else # Datei Download verlinken 
										{
											$dateistring .= "<a href='".htmlspecialchars($file, ENT_QUOTES)."' target='_blank'>" . $filename . "</a> &nbsp;"; 
										}
									}
									$html .= $dateistring . "<br />" . $picstring;
									$html .= "</div>"; 
								}
							}
							// Edits, Signatur, ADM
							if($askdel == 0) 
							{						

								if($uData["signature"] != "") 
									$html .= "<div class='container-fluid container-post-signature'>".$uData["signature"]."</div>"; 

								if($post["edits"] > 0) 
								{
									$editorData = \Main\User\Control::getUserData($post["lastEditor"]); 
								
									$string = \Main\Language::$txt["infos"]["post_edited_string"]; 
									$string = str_replace("[x]", $post["edits"], $string);
									$string = str_replace("[user]", $editorData["displayname"], $string);
									$string = str_replace("[time]", \Main\toTime($post["lastEditTime"]), $string);
									
									$html .= "<div class='container-fluid container-post-edits'>";
										$html .= $string;
									$html .= "</div>"; 
								}
								
								if($showadm == 1) 
								{
									$html .= "<div class='container-fluid container-post-control'>";
									if($showadmdel == 1) 
										$html .= "<a href='index.php?page=board&t=".$tId."&pageNo=$pageNo&askdel=".$post["id"]."#post".$post["id"]."'><i class='fa fa-times fa-lg post-del-icon'></i></a>";
									if($showadmedit == 1) 
										$html .= "<a href='index.php?page=board&t=".$tId."&pageNo=$pageNo&edit=".$post["id"]."#post".$post["id"]."'><i class='fa fa-edit fa-lg post-edit-icon'></i></a>"; 
									
									$html .= "</div>"; 
								}
							}
							// Löschen Fragen 
							else
							{
								$html .= "<form action='index.php?page=board&t=$tId&pageNo=$pageNo&dodel#post".$post["id"]."' method='post'>
									<h4>".\Main\Language::$txt["infos"]["ask_post_del"]."</h4>
									<input type='hidden' name='postId' value='".$post["id"]."' />
									<input type='text' name='reason' placeholder='".htmlspecialchars(\Main\Language::$txt["forms"]["post_del_reason"], ENT_QUOTES)."' class='form-control' /><br />
									<div class='center'>
										<button name='btnaction' value='ja' class='btn btn-success'>".\Main\Language::$txt["buttons"]["post_del"]."</button> 
										<button name='btnaction' value='nein' class='btn btn-danger'>".\Main\Language::$txt["buttons"]["post_dont_del"]."</button>
									</div><br />
								</form>"; 
							}
							
						$html .= "</div>"; 
					
					$html .= "</div>"; 
				
				$html .= "</div>"; 
			}
			else //  Anzeigen wenn er gelöscht wurde 
			{
				$delDat = \Main\User\Control::getUserData($post["deletedUser"]); 
				if($post["deletedReason"] == "") $post["deletedReason"] = \Main\Language::$txt["infos"]["post_del_noreason"];
				$puDat = \Main\User\Control::getUserData($post["user"]);  
				
				$string = \Main\Language::$txt["infos"]["post_del_by_reason"]; 
				$string = str_replace("[startuser]", "<a href='index.php?page=members&u=".$post["user"]."'>".htmlspecialchars($puDat["displayname"])."</a>", $string); 
				$string = str_replace("[user]", "<a href='index.php?page=members&u=".$post["deletedUser"]."'>".htmlspecialchars($delDat["displayname"])."</a>", $string); 
				$string = str_replace("[reason]", htmlspecialchars($post["deletedReason"]), $string); 

				if($post["deletedUser"] == $post["user"])
				{
					$string = \Main\Language::$txt["infos"]["post_del_by_creator"]; 
					$string = str_replace("[user]", "<a href='index.php?page=members&u=".$post["deletedUser"]."'>".htmlspecialchars($delDat["displayname"])."</a>", $string);
					$string = str_replace("[reason]", htmlspecialchars($post["deletedReason"]), $string); 
				}
				
				$html .= "<div class='alert alert-warning post-margin-top yellowbox post-deleted-warning'>$string</div>"; 
			}
		}
		if($pageNo >= $max_pages && \Main\User\Control::$logged == 1 && \Main\User\Control::IsUserAllowedToWriteKategory(\Main\User\Control::$dbid, $kId) && ($tclosed == 0 || \Main\User\Control::$data["arights"]["edit_themes"] || \Main\User\Control::$data["arights"]["close_themes"] || \Main\User\Control::$data["arights"]["enter_administration"])) 
		{
			$npage = 0; 
			if($postcount >= $max_posts_per_page) $npage = 1; # Wird der gesendete Beitrag auf der nächsten Seite stehen? 
			$html .= "<div class='theme_post theme_answer'>
				<div class='row'>
					<div class='col-md-12'>
						<form action='index.php?page=board&t=$tId&pageNo=$pageNo&send' method='post' enctype='multipart/form-data'>
						<input type='hidden' name='next_page' value='$npage' />
						<textarea id='ckedit' name='nachricht' class='theme_answer'></textarea>
						
						<div class='editor-upload'>
							<div class='item'><a onclick='toggledateianhaenge();'>". \Main\Language::$txt["buttons"]["dataupload"] ."</a></div>
						</div>
						<div class='editor-upload' id='ckeditor_dateiupload' style='display:none;'>
							<input type='file' name='files[]' multiple /> 
						</div>

						<div class='right ckform-right'>
							<button class='btn btn-primary'>".\Main\Language::$txt["buttons"]["send"]."</button>
						</div>
						</form>

						<script>CKEDITOR.replace('ckedit');</script>
					</div>
				</div>
			</div>";
		}
		$html .= "</div>"; 
		
		
		if($max_pages > 1) 
		{
			$html .= "<div class='clear'></div>";
			$html .= "<ul class='pagination'>"; 
				for($i = 1; $i <= $max_pages; $i++) 
				{	
					$html .= "<li ";
					if($pageNo == $i) $html .= "class='active'"; 
					$html .= "><a href='index.php?page=board&t=$tId&pageNo=$i'>$i</a></li>";
				}
			$html .= "</ul>";
			$html .= "<div class='clear'></div>";
		}
		
		return $html; 
	}
	
	/**
	* Themen anzeigen
	*
	* Zeigt alle Themen einer Kategorien in Form einer Liste an. Ebenfalls enthalten ist eine umblätter Funktion.
	*
	* @author s-l 
	* @version 0.1.0
	* @todo 
	*/
	public static function showKatThemes($kId)
	{
		$html = ""; 
		
		if(!\Main\User\Control::IsUserAllowedToSeeKategory(\Main\User\Control::$dbid, $kId)) return ""; 
		
		$rst = \Main\DB::select("themen", "id", "kategorie='".\Main\DB::escape($kId)."'");
		$max_themes_per_page = 15; 
		$themes_ges = $rst->num_rows; 
		
		$max_pages = ceil($themes_ges / $max_themes_per_page); 
		
		if($max_pages == 0) $max_pages = 1; 
		
		if(isset($_GET["pageNo"])) $pageNo = $_GET["pageNo"]; 
		else $pageNo = 1; 
		
		if($pageNo > $max_pages) $pageNo = $max_pages; 
		
		$startlimit = ($pageNo * $max_themes_per_page) - $max_themes_per_page; 
		
		$limit = $startlimit . ", " . $max_themes_per_page; 
		
		if(\Main\User\Control::IsUserAllowedToWriteKategory(\Main\User\Control::$dbid, $kId))
		{
			$html .= "<div class='right'><a href='index.php?page=board&k=$kId&new_t'><button class='btn btn-primary'>".\Main\Language::$txt["buttons"]["new_theme"]."</button></a></div>";
		} 
		
		if($max_pages > 1) 
		{
			$html .= "<ul class='pagination'>"; 
				$showPoints = false; 
				for($i = 1; $i <= $max_pages; $i++) 
				{	
					$showi = false; 
					if($i == 1 || $i == $max_pages) $showi = true; 
					if($i == $pageNo || $i == $pageNo - 1 || $i == $pageNo - 2 || $i == $pageNo + 1 || $i == $pageNo + 2) $showi = true; 
					if($showi)
					{
						$html .= "<li ";
						if($pageNo == $i) $html .= "class='active'"; 
						$html .= "><a href='index.php?page=board&k=$kId&pageNo=$i'>$i</a></li>";
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
		}
		
		$html .= "<div class='clear'></div>";
		
		$html .= "<div class='panel panel-primary panel-forum panel-themes panel-margin-top'>";
		$html .= "<div class='panel-heading'>".\Main\Language::$txt["infos"]["themes_header"]."</div>";
		$html .= "<div class='panel-heading2'>
			<div class='row'>
				<div class='col-1'>
					".\Main\Language::$txt["infos"]["themes_header2_col1"]."
				</div>
				<div class='col-2'>
					".\Main\Language::$txt["infos"]["themes_header2_col2"]."
				</div>
				<div class='col-3'>
					".\Main\Language::$txt["infos"]["themes_header2_col3"]."
				</div>
			</div>
		</div>";
		$html .= "<div class='panel-body'>"; 
		 

		$rst = \Main\DB::select("themen", "id, name, lastChange, startTime, user, deleted, deleteUser, deleteReason, tag", "kategorie='".\Main\DB::escape($kId)."'", $limit, "tag DESC, lastChange DESC, id DESC");
		while($row = $rst->fetch_object())
		{
			$thema = array(
				"id" => $row->id,
				"name" => $row->name,
				"lastChange" => $row->lastChange,
				"startTime" => $row->startTime,
				"user" => $row->user,
				"deleted" => $row->deleted,
				"deleteUser" => $row->deleteUser,
				"deleteReason" => $row->deleteReason,
				"tag" => $row->tag
			);
			
			# Alle als gesehen markieren 
			if(isset($_GET["unmark-all"]) && \Main\User\Control::$logged == 1) 
			{
				$result = \Main\DB::select("themes_seen", "id", "user='".\Main\DB::escape(\Main\User\Control::$dbid)."' AND thema='".\Main\DB::escape($thema["id"])."'");
				if($result->num_rows > 0) 
				{
					$roww = $result->fetch_object(); 
					\Main\DB::update("themes_seen", $roww->id, array("stamp" => time()));
				}
				else
				{
					\Main\DB::insert("themes_seen", array(
						"user" => \Main\User\Control::$dbid,
						"thema" => $thema["id"],
						"stamp" => time()
					));
				}
			}
			
			$uData = \Main\User\Control::getUserData($thema["user"]); 
			
			if($thema["deleted"] == 1) 
			{
				$aData = \Main\User\Control::getUserData($thema["deleteUser"]); 
				
				$string = \Main\Language::$txt["infos"]["theme_deleted_txt"]; 
				$string = str_replace("[theme]", "<a>".htmlspecialchars($thema["name"])."</a>", $string);
				$string = str_replace("[user1]", "<a>".htmlspecialchars($uData["displayname"])."</a>", $string); 
				$string = str_replace("[user2]", "<a>".htmlspecialchars($aData["displayname"])."</a>", $string); 
				$string = str_replace("[reason]", htmlspecialchars($thema["deleteReason"]), $string); 
				
				$html .= "<div class='row theme'>
					<div class='col-md-12'>
						$string
					</div>
				</div>";
			}
			else
			{
				$answers = \Main\Board\Control::countThemaPosts($thema["id"]) - 1; 
				$hits = Control::countThemaHits($thema["id"]); 
				$unseenPosts = \Main\Board\Control::UnseenPostsInThema($thema["id"]); 
				$unstring = ""; 
				if($unseenPosts > 0) $unstring = "<span class='badge'>$unseenPosts</span>"; 
				
				$lpstring = ""; 
				$result = \Main\DB::select("posts", "id, user, startTime", "thema='".\Main\DB::escape($thema["id"])."' AND deleted='0'", "1", "startTime DESC, id DESC");
				if($result->num_rows > 0) 
				{
					$roww = $result->fetch_object(); 
					$lp = array(
						"id" => $roww->id,
						"user" => $roww->user,
						"startTime" => $roww->startTime
					);
					$lpuData = \Main\User\Control::getUserData($lp["user"]); ;
					$lpstring = \Main\Language::$txt["infos"]["theme_started_by"]." <a>".htmlspecialchars($lpuData["displayname"])."</a>"; 
					$lpstring .= "<br />".\Main\toTime($lp["startTime"]);
				}
				
				$tagstring = ""; 
				if($thema["tag"] != 0) 
				{
					$result = \Main\DB::select("tags", "name", "id='" . $thema["tag"] . "' AND typ='0' AND useable='1'");
					if($result->num_rows > 0) 
					{
						$roww = $result->fetch_object(); 
						$tname = $roww->name; 
						$tagstring = "<span class='badge'>" . htmlspecialchars($tname) . "</span>"; 
					}
				}
				
				$labelstring = ""; 
				$result = \Main\DB::select("themen_labels", "label", "thema='" . $thema["id"] . "'");
				while($roww = $result->fetch_object())
				{
					$labelid = $roww->label; 
					$rstt = \Main\DB::select("tags", "name, backgroundcolor, textcolor", "id='" . \Main\DB::escape($labelid) . "' AND typ='1'");
					if($rstt->num_rows > 0) 
					{
						if($labelstring != "") $labelstring .= " "; 
						$rowe = $rstt->fetch_object();
						$bgcol = $rowe->backgroundcolor; 
						$txtcol = $rowe->textcolor; 
						$bgcolstring = ""; 
						$txtcolstring = ""; 
						if($bgcol != "") 
						{
							$bgcolstring = "background:$bgcol;"; 
						} 
						if($txtcol != "") 
						{
							$txtcolstring = "color:$txtcol;"; 
						}
						$labelstring .= "<span class='badge label' style='$bgcolstring $txtcolstring'>" . htmlspecialchars($rowe->name) . "</span>"; 
					}
				}

				$html .= "<div class='row theme'>
					<div class='col-theme-1'>
						<i class='fa fa-file-o fa-lg'></i>
					</div>
					<div class='col-theme-2'>
						$tagstring <a href='index.php?page=board&t=".$thema["id"]."'>".htmlspecialchars($thema["name"])." $unstring</a> $labelstring<br />
						<font class='usize'>".\Main\Language::$txt["infos"]["theme_started_by"]." <a href='index.php?page=members&u=".$thema["user"]."'>".htmlspecialchars($uData["displayname"])."</a>, <i class='fa fa-clock fa-lg'></i> ".\Main\toTime($thema["startTime"])."</font>
					</div>
					<div class='col-theme-3'>
						".\Main\Language::$txt["infos"]["theme_answers"]." $answers<br />
						".\Main\Language::$txt["infos"]["theme_hits"]." $hits
					</div>
					<div class='col-theme-4'>
						<div class='media'>
							<div class='media-left'>
							
							</div>
							<div class='media-body'>
								$lpstring
							</div>
						</div>
					</div>
				</div>";
			}
		}
		
		$html .= "</div>"; 
		if(\Main\User\Control::$logged == 1) 
		{
			$html .= "<div class='panel-footer theme-options-box'>
				<div class='right'>"; 
				if(\Main\User\Control::$logged == 1) 
					$html .= "<a title='".htmlspecialchars(\Main\Language::$txt["infos"]["unmark_all_themes"], ENT_QUOTES)."' href='index.php?page=board&k=$kId&pageNo=$pageNo&unmark-all'><i class='fa fa-eye fa-lg'></i></a>"; 
				$html .= "</div>
			</div>";
		}
		$html .= "</div>";

		if($max_pages > 1) 
		{
			$html .= "<ul class='pagination'>"; 
				$showPoints = false; 
				for($i = 1; $i <= $max_pages; $i++) 
				{	
					$showi = false; 
					if($i == 1 || $i == $max_pages) $showi = true; 
					if($i == $pageNo || $i == $pageNo - 1 || $i == $pageNo - 2 || $i == $pageNo + 1 || $i == $pageNo + 2) $showi = true; 
					if($showi)
					{
						$html .= "<li ";
						if($pageNo == $i) $html .= "class='active'"; 
						$html .= "><a href='index.php?page=board&k=$kId&pageNo=$i'>$i</a></li>";
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
		}
		
		if(\Main\User\Control::IsUserAllowedToWriteKategory(\Main\User\Control::$dbid, $kId))
		{
			$html .= "<div class='right'><a href='index.php?page=board&k=$kId&new_t'><button class='btn btn-primary'>".\Main\Language::$txt["buttons"]["new_theme"]."</button></a></div>";
		}		
		
		return $html; 
	}
	
	public static function makeKategorieNavi($kId, $blockcurrent=1)
	{
		function makeRow($kId, $first=0) 
		{
			$html = ""; 
			
			$rst = \Main\DB::select("kategorien", "id, name, kategorie, forum", "id='".\Main\DB::escape($kId)."'");
			if($rst->num_rows > 0) 
			{
				$row = $rst->fetch_object(); 
				$kategorie = array(
					"id" => $row->id,
					"name" => $row->name,
					"kategorie" => $row->kategorie,
					"forum" => $row->forum
				); 
				if($first == 0) 
					$html .= "<div class='pageNaviItem'><i class='fa fa-arrow-right fa-lg naviArrow'></i> <a href='index.php?page=board&k=".$kategorie["id"]."'>".htmlspecialchars($kategorie["name"])."</a></div>";
				
				if($kategorie["kategorie"] != 0) 
				{
					$html = makeRow($kategorie["kategorie"]) . $html; 
				}
				else if($kategorie["forum"] != 0) 
				{
					$rst = \Main\DB::select("foren", "name", "id='".\Main\DB::escape($kategorie["forum"])."'");
					if($rst->num_rows > 0) 
					{
						$row = $rst->fetch_object(); 
						$bname = $row->name; 
						$html = "<div class='pageNaviItem'><i class='fa fa-arrow-right fa-lg naviArrow'></i> <a href='index.php?page=board&b=".$kategorie["forum"]."'>".htmlspecialchars($bname)."</a></div>" . $html; 
					}
				}
			}
			
			return $html; 
		}
		
		$html = ""; 
		
		$sstring = "Startseite"; 
		if(\Main\Board\Settings::$settings["homelinkPage"] == "board") 
		{	
			$sstring = "Forum"; 
		}
		
		$html .= "<div class='pageNavi'>
			<div class='pageNaviItem'><a href='index.php'>$sstring</a></div>"; 
			if(\Main\Board\Settings::$settings["homelinkPage"] != "board") 
			{
				$html .= "<div class='pageNaviItem'><i class='fa fa-arrow-right fa-lg naviArrow'></i> <a href='index.php?page=board'>Forum</a></div>";
			}
			$html .= makeRow($kId, $blockcurrent); 
		$html .= "</div>"; 
		
		return $html; 
	}
	
	public static function makeForumNavi()
	{
		$html = ""; 
		
		$sstring = "Startseite"; 
		if(\Main\Board\Settings::$settings["homelinkPage"] == "board") 
		{	
			$sstring = "Forum"; 
		}
		
		$html .= "<div class='pageNavi'>
			<div class='pageNaviItem'><a href='index.php'>$sstring</a></div>"; 
			if(\Main\Board\Settings::$settings["homelinkPage"] != "board") 
			{
				$html .= "<div class='pageNaviItem'><a href='index.php?page=board'>Forum</a></div>";
			}
		$html .= "</div>"; 
		
		return $html; 
	}
	
	/**
	* Last-Beitrag-Box
	*
	* Zeigt die Last-Beitrag-Box für eine Kategorie an. Sprich es wird geprüft in welchem Thema (in der Kategorie) der letzte Beitrag 
	* getätigt wurde und das Thema samt der Beitrag wird dann in einer Box dargestellt. Wir überall da verwendet, wo irgendwelche Kategorien
	* aufgelistet werden. 
	* 
	* @author s-l 
	* @version 0.1.1
	* @todo 
	*/
	public static function showKatLbBox($kId) 
	{
		$html = ""; 
		
		# Thema herausfinden
		$rst = \Main\DB::select("themen", "id, name, user, lastChange", "kategorie='".\Main\DB::escape($kId)."' AND deleted='0'", "1", "lastChange DESC, id DESC");
		if($rst->num_rows <= 0) return $html; 
		$row = $rst->fetch_object(); 

		$thema = array(
			"id" => $row->id,
			"name" => $row->name,
			"user" => $row->user,
			"lastChange" => $row->lastChange
		);
		
		$uDat = \Main\User\Control::getUserData($thema["user"]); 

		# Beirag herausfinden 
		$rst = \Main\DB::select("posts", "user", "thema='".\Main\DB::escape($thema["id"])."'", "1", "id DESC");
		if($rst->num_rows <= 0) return $html; 
		$row = $rst->fetch_object(); 

		$post = array("user" => $row->user);
		$uDat = \Main\User\Control::getUserData($post["user"]); 

		# Box anzeigen 
		$html .= "<div class='lbbox'><div class='media'>
		<div class='media-left'>"; 
			if($uDat["avatar"] != "") 
					$html .= "<img src='".htmlspecialchars($uDat["avatar"], ENT_QUOTES)."' class='img-thumbnail avatar-lbbox' />"; 
		$html .= "</div>
		<div class='media-body'>
			<a href='index.php?page=board&t=".$thema["id"]."'>".htmlspecialchars($thema["name"])."</a><br />
			<font class='lbbox_user'>&nbsp;".\Main\Language::$txt["infos"]["lbbox_from"]." <a href='index.php?page=members&u=".$thema["user"]."'>".htmlspecialchars($uDat["displayname"])."</a></font><br />
			<font class='lbbox_time'>&nbsp;".\Main\toTime($thema["lastChange"])."</font>
		</div>
		</div>
		</div>";
		
		return $html; 
	}
	
	/**
	* Kategorien anzeigen
	*
	* Listet alle Kategorien eines Forums auf. 
	* 
	* @author s-l 
	* @version 0.1.4 
	*/
	public static function showForumKats($fId) 
	{
		$html = ""; 
		
		$rst = \Main\DB::select("kategorien", "id, name, description", "forum='".\Main\DB::escape($fId)."' AND kategorie='0'", null, "orderId, id");
		while($row = $rst->fetch_object())
		{
			$kategorie = array(
				"id" => $row->id,
				"name" => $row->name,
				"description" => $row->description
			);
			if(\Main\User\Control::IsUserAllowedToSeeKategory(\Main\User\Control::$dbid, $kategorie["id"]))
			{
				
				$unseenPosts = Control::UnseenPostsInKategory($kategorie["id"]); 
				
				$ustring1 = "<i class='fa fa-folder-o fa-lg'></i>";
				$ustring2 = ""; 
				if($unseenPosts > 0) 
				{
					$ustring1 = "<i class='fa fa-folder fa-lg'></i>";
					$ustring2 = "<span class='badge'>$unseenPosts</span>"; 
				}
				
				$html .= "<div class='media'>
					<div class='media-left'>
						$ustring1
					</div>
					<div class='media-body'>
						<h5 class='media-heading'><a href='index.php?page=board&k=".$kategorie["id"]."'>".htmlspecialchars($kategorie["name"])." $ustring2</a></h5>
						<p>".htmlspecialchars($kategorie["description"])."</p>
						".self::fastShowuKats($kategorie["id"])."
					</div>
					<div class='media-right'>
						<!--div style='width:200px;height:20px;background:orange;'>LBBOX</div-->
						".self::showKatLbBox($kategorie["id"])."
					</div>
				</div>";
			
			}
		}
		
		return $html; 
	}
	
	public static function showKatKats($kId) 
	{
		$html = ""; 
		
		$rst = \Main\DB::select("kategorien", "id, name, description", "kategorie='".\Main\DB::escape($kId)."' AND forum='0'", null, "orderId, id");
		while($row = $rst->fetch_object())
		{
			$kategorie = array(
				"id" => $row->id,
				"name" => $row->name,
				"description" => $row->description
			);
			if(\Main\User\Control::IsUserAllowedToSeeKategory(\Main\User\Control::$dbid, $kategorie["id"]))
			{
			
				$html .= "<div class='media'>
					<div class='media-left'>
						<i class='fa fa-folder-o fa-lg'></i>
					</div>
					<div class='media-body'>
						<h5 class='media-heading'><a href='index.php?page=board&k=".$kategorie["id"]."'>".htmlspecialchars($kategorie["name"])."</a></h5>
						<p>".htmlspecialchars($kategorie["description"])."</p>
						".self::fastShowuKats($kategorie["id"])."
					</div>
					<div class='media-right'>
						<!--div style='width:200px;height:20px;background:orange;'>LBBOX</div-->
						".self::showKatLbBox($kategorie["id"])."
					</div>
				</div>";
			
			}
		}
		
		return $html; 
	}
	
	/**
	* Unterkategorien Anzeigen
	*
	* Diese Methode zeigt die Unterkategorien von einer Kategorie (klein unter der Beschreibung der Kategorie). 
	* Update 18.04.17: Zeigt nun alle Unterkategorien und nicht nur 3
	* 
	* @author s-l 
	* @version 0.0.4 
	*/
	public static function fastShowuKats($kId) 
	{
		$html = ""; 
		
		$rst = \Main\DB::select("kategorien", "id, name, description", "kategorie='".\Main\DB::escape($kId)."'", null, "orderId, id");
		$counter = 0; 
		
		while($row = $rst->fetch_object())
		{
			$kategorie = array(
				"id" => $row->id,
				"name" => $row->name,
				"description" => $row->description
			);
			if(\Main\User\Control::IsUserAllowedToSeeKategory(\Main\User\Control::$dbid, $kategorie["id"]) /*&& $counter < 4*/)
			{
				$counter ++; 
				$html .= "<div class='underkat'>
					<i class='fa fa-folder-o fa-lg'></i> <a href='index.php?page=board&k=".$kategorie["id"]."'>".htmlspecialchars($kategorie["name"])."</a>
				</div>";
			}
		}
		
		
		return $html; 
	}
	
	public static function showPrivacyPolicy() 
	{
		$html = ""; 
		
		if(\Main\Board\Settings::$settings["dok_datenschutz"] == 0) 
		{
			$html .= "<h2>".\Main\Language::$txt["infos"]["privacy_policy"]."</h2>"; 
			$html .= "<div class='alert alert-danger redbox'>
				".\Main\Language::$txt["alerts"]["privacy_not_active"]."
			</div>";
			return $html; 
		}
		
		//$html .= self::makeForumNavi(); 
		
		$html .= "<h2>".\Main\Language::$txt["infos"]["privacy_policy"]."</h2>"; 
		$html .= "<div class='panel panel-default panel-margin-top panel-privacys panel-privacy'>";
			$html .= \Main\Board\Settings::$settings["dok_datenschutz_txt"]; 
		$html .= "</div>"; 
		
		return $html; 
	}
	
	public static function showTermsOfUse()
	{
		$html = ""; 
		
		if(\Main\Board\Settings::$settings["dok_terms"] == 0) 
		{
			$html .= "<h2>".\Main\Language::$txt["infos"]["terms_of_use"]."</h2>"; 
			$html .= "<div class='alert alert-danger redbox'>
				".\Main\Language::$txt["alerts"]["terms_not_active"]."
			</div>";
			return $html; 
		}
		
		//$html .= self::makeForumNavi(); 
		
		$html .= "<h2>".\Main\Language::$txt["infos"]["terms_of_use"]."</h2>"; 
		$html .= "<div class='panel panel-default panel-margin-top panel-privacys panel-terms'>";
			$html .= \Main\Board\Settings::$settings["dok_terms_txt"]; 
		$html .= "</div>"; 
		
		return $html; 
	}
	
	public static function showImpressum()
	{
		$html = ""; 
		
		if(\Main\Board\Settings::$settings["dok_impressum"] == 0) 
		{
			$html .= "<h2>".\Main\Language::$txt["infos"]["impressum"]."</h2>"; 
			$html .= "<div class='alert alert-danger redbox'>
				".\Main\Language::$txt["alerts"]["impressum_not_active"]."
			</div>";
			return $html; 
		}
		
		//$html .= self::makeForumNavi(); 
		
		$html .= "<h2>".\Main\Language::$txt["infos"]["impressum"]."</h2>"; 
		$html .= "<div class='panel panel-default panel-margin-top panel-privacys panel-impressum'>";
			$html .= \Main\Board\Settings::$settings["dok_impressum_txt"]; 
		$html .= "</div>"; 
		
		return $html; 
	}
	
	public static function showMainMenue() // <-- HOOKED! 
	{
		$html = ""; 
		$html .= \Main\Plugins::hook("BoardView.showMainMenue.beforeMenue", "");
		$html .= \Main\Plugins::hook("BoardView.showMainMenue.MenueUL.replace", "<ul class='nav nav-pills pageMenueItems'>"); 
			$html .= \Main\Plugins::hook("BoardView.showMainMenue.menueList.beforeMenueList", "");
		
			$rst = \Main\DB::select("menuePoints", "name, pageName", null, null, "orderId, id");
			while($row = $rst->fetch_object())
			{
				$mp = array(
					"name" => $row->name,
					"page" => $row->pageName
				);
				
				$active = ""; 
				
				if(isset($_GET["page"]))
				{
					if($_GET["page"] == $mp["page"]) 
						$active = "active"; 
				}
				else
				{
					if(\Main\Board\Settings::$settings["homelinkPage"] == $mp["page"])
						$active = "active"; 
				}
				
				$html .= "<li class='$active'><a href='index.php?page=".$mp["page"]."'>".htmlspecialchars($mp["name"])."</a></li>"; 
				
				//$html .= ""; 
			}
			$html .= \Main\Plugins::hook("BoardView.showMainMenue.menueList.afterMenueList", "");
		
		$html .= \Main\Plugins::hook("BoardView.showMainMenue.MenueUL.replace.End", "</ul>");
		$html .= \Main\Plugins::hook("BoardView.showMainMenue.afterMenue", ""); 
		
		return $html; 
	}
	
	public static function showDesignChoose() // <-- HOOKED! 
	{
		$html = ""; 
		
		if(!\Main\Board\Settings::$settings["allow_change_design"])
		{
			return $html;  
		}
		
		if(isset($_GET["choose"]))
		{
			$id = $_GET["choose"]; 
			setcookie("Design_id", $id, time()+(86400*365));
		}
		
		$html .= "<div class='panel panel-default panel-designs'>";
		$html .= "<h2>".\Main\Language::$txt["infos"]["designs_header"]."</h2>"; 
			$rst = \Main\DB::select("designs", "id, name", "active='1'", null, "id");
			$string = ""; 
			$string .= \Main\Plugins::hook("BoardView.showDesignChoose.DesignString.beforeDesigns", "<div class='panel panel-primary panel-designs-sm'><div class='panel-heading'>".\Main\Language::$txt["infos"]["design_panel_header"]."</div><div class='panel-body'>");
			$count = 0; 
			while($row = $rst->fetch_object())
			{
				$design = array(
					"id" => $row->id,
					"name" => $row->name
				);
				if($count > 0) $string .= "<br />";
				$count++; 
				$string .= "<a href='index.php?page=designs&choose=".$design["id"]."'>".htmlspecialchars($design["name"])."</a>"; 
			}
			$string .= \Main\Plugins::hook("BoardView.showDesignChoose.DesignString.afterDesigns", "</div></div>");
			$string = \Main\Plugins::hook("BoardView.showDesignChoose.DesignString.replace", $string);
			$html .= $string; 
		$html .= "</div>"; 
		
		return $html; 
	}

	/**
	* Statistik anzeigen
	*
	* Zeigt die Statistik der Seite (Online User, etc) auf der rechten Seite des Forums an. 
	* 03.05.17: Zeigt jetzt auch die Online Gäste an und hat eine Rekordstatistik (zeichnet den Moment an dem am meisten Nutzer on waren auf)
	*
	* @author s-l 
	* @version 0.0.4
	* @return string 
	*/
	public static function showStatisticsRight() 
	{
		$html = ""; 

		$members = \Main\User\Control::listOnlineUsers(); 
		$members = json_encode($members); 
		$members = \Main\Plugins::hook("BoardView.showStatistics.onlineList.json", $members); 
		$members = json_decode($members, true); 
		$members_on = count($members); 
		$guests_on = \Main\Plugins::hook("BoardView.showStatistics.countOnlineGuests.int", \Main\User\Control::countGuestsOnline()); 
		$members_no_list_on = 0; 
		foreach($members as $member) 
		{
			if($member["hide_in_memberslist"] == 1) $members_no_list_on++; 
		}

		\Main\User\Control::checkIfNewUserRecord($members_on, $guests_on);

		$anz_accounts = \Main\Plugins::hook("BoardView.showStatistics.countAccounts.int", \Main\User\Control::countAccounts()); 
		$anz_themes = \Main\Plugins::hook("BoardView.showStatistics.countThemes.int", Control::countThemes()); 
		$anz_posts = \Main\Plugins::hook("BoardView.showStatistics.countPosts.int", Control::countPosts()); 
		$newest_member = \Main\User\Control::getNewestAccount(); 
		$newest_member = json_encode($newest_member); 
		$newest_member = \Main\Plugins::hook("BoardView.showStatistics.newestAccount.json", $newest_member); 
		$newest_member = json_decode($newest_member, true); 

		$html .= "<div class='panel panel-primary'>";
		$html .= "<div class='panel-heading'>".\Main\Language::$txt["statistic"]["header"]."</div>";
		$html .= "<div class='panel-body'>";

		/// Forenstatistiken 
		$memstring = \Main\Language::$txt["statistic"]["member_multi"]; 
		if($anz_accounts == 1) $memstring = \Main\Language::$txt["statistic"]["member_single"]; 
		$memstring = str_replace("[x]", $anz_accounts, $memstring); 

		$tstring = \Main\Language::$txt["statistic"]["themes_multi"]; 
		if($anz_themes == 1) $tstring = \Main\Language::$txt["statistic"]["themes_single"]; 
		$tstring = str_replace("[x]", $anz_themes, $tstring);

		$pstring = \Main\Language::$txt["statistic"]["posts_multi"]; 
		if($anz_posts == 1) $pstring = \Main\Language::$txt["statistic"]["posts_single"];
		$pstring = str_replace("[x]", $anz_posts, $pstring); 

		# Anzahl Mitglieder - Themen - Posts 
		$html .= "$memstring$tstring$pstring"; 
		$html .= "<br />";
		# Neuestes Mitglied 
		$memstring = \Main\Language::$txt["statistic"]["newest_member"]; 
		$memstring = str_replace("[user]", "<a href='index.php?page=members&u=".$newest_member["dbid"]."'>" . $newest_member["displayname"] . "</a>", $memstring);
		$html .= $memstring; 

		$html .= "<br /><br />";

		# Wer ist Online 
		if($members_on == 1) $memstring = \Main\Language::$txt["statistic"]["member_online"]; 
		else $memstring = \Main\Language::$txt["statistic"]["members_online"]; 
		$memstring = str_replace("[x]", $members_on, $memstring);
		$invstring = ""; 
		if($members_no_list_on > 0) $invstring = str_replace("[x]", $members_no_list_on, \Main\Language::$txt["statistic"]["members_invis"]);
		$memstring = str_replace("[invisible]", $invstring, $memstring);
		$gstring = ""; 
		if($guests_on == 1) $gstring = str_replace("[x]", $guests_on, \Main\Language::$txt["statistic"]["and_single_guest"]);
		else if($guests_on > 1) $gstring = str_replace("[x]", $guests_on, \Main\Language::$txt["statistic"]["and_multi_guest"]);
		$memstring = str_replace("[guests]", $gstring, $memstring); 

		$html .= $memstring . "<br />"; 
		
		$list = ""; 
		foreach($members as $member) # Mitglieder auflisten 
		{
			if($member["hide_in_memberslist"] == 1) continue; 
			if($list != "") $list .= ", "; 
			$list .= "<a href='index.php?page=members&u=".$member["dbid"]."'>".$member["displayname"]."</a>"; 
		}
		$html .= $list; 

		$record = \Main\Plugins::hook("BoardView.showStatistics.userRecord.string", \Main\User\Control::getUserRecord()); 
		if($record != "")
			$html .= "<br />" . \Main\Language::$txt["statistic"]["record::"] . " " . $record; 

		$html .= "</div>"; 
		$html .= "</div>"; 

		return $html; 
	}

	/**
	* Nachricht vorbereiten
	*
	* Bereitet eine Nachricht für die Anzeige vor. Soll Links einfügen und eventuell auf ungültige/verbotene Wörter prüfen etc. 
	*
	* @author s-l 
	* @version 0.0.1 
	* @return string 
	*/
	public static function preparePostMessageForShow($message) 
	{
		 

		//$message = \Main\AutoLinkUrls($message); 
		//$message = "X" + $message; 
		//print($message); 
		//while(strpos($message, "http://") != 0) 
		//{
		//	$pos = strpos($message, "http://"); 
			//$first_part = substr($message, 0, $pos); 
			//$last_part = substr($message, $pos); 
			//$message = $first_part . "LINK" . $last_part; 
		//}

		//$message = substr($message, 1); 
		return $message; 
	}

}

?>