<?php
namespace Main\Board; 
class Control
{
	/**
	* Ein Thema Aufrufen
	*
	* Wenn ein Thema geöffnet wird, wird durch diese Funktion ein Seitenaufruf für das Thema gewertet. 
	* Wird jetzt zusätzlich noch in eine eigene Datenbank-Tabelle geschrieben um nachzuvollziehen zu welcher Zeit und von welchem Benutzer (falls eingeloggt)
	* die Seite aufgerufen wurde. 
	* 
	* @author s-l 
	* @version 0.0.2
	*/
	public static function hitThema($tId) 
	{
		$rst = \Main\DB::select("themen", "hits", "id='".\Main\DB::escape($tId)."'"); 
		if($rst->num_rows > 0) 
		{
			$row = $rst->fetch_object(); 
			$hits = $row->hits; 
			$hits++; 
			\Main\DB::update("themen", $tId, array("hits" => $hits));
			\Main\DB::insert("themes_hits", array("thema" => $tId, "user" => \Main\User\Control::$dbid, "time" => time()));
		}
	}

	/**
	* Aufrufe zählen
	*
	* Zählt wie oft ein Thema bereits aufgerufen wurde und gibt die Zahl wieder. 
	*
	* @author s-l 
	* @version 0.0.1 
	* @return int 
	*/
	public static function countThemaHits($tId) 
	{
		$rst = \Main\DB::select("themen", "hits", "id='".\Main\DB::escape($tId)."'");
		$row = $rst->fetch_object(); 
		$hits = $row->hits; 
		return $hits; 
	}

	/**
	* Beiträge zählen
	*
	* Zählt alle Beiträge eines Themas und gibt an wie viele es sind. 
	* 
	* @author s-l 
	* @version 0.0.1 
	* @return int 
	*/
	public static function countThemaPosts($tId) 
	{
		$rst = \Main\DB::select("posts", "id", "thema='".\Main\DB::escape($tId)."'");
		return $rst->num_rows; 
	}
	
	/**
	* Ungelesene Beiträge eines Themas
	*
	* Zählt wie viele Beiträge von dem angegebenen Thema noch (für den Benutzer) ungelesen sind und gibt die Anzahl zurück. 
	*
	* @version 0.1.0 
	* @author s-l 
	* @return int 
	*/
	public static function UnseenPostsInThema($tId) 
	{
		if(\Main\User\Control::$dbid == 0) return 0; 
		$unseen = 0; 
		$nowtime = time(); 

		$lastUserTime = 0; 
		$rst = \Main\DB::select("themes_seen", "stamp", "user='".\Main\DB::escape(\Main\User\Control::$dbid)."' AND thema='".\Main\DB::escape($tId)."'");
		if($rst->num_rows > 0) 
		{
			$row = $rst->fetch_object(); 
			$lastUserTime = $row->stamp; 
		}
			
			
		$rst = \Main\DB::select("posts", "startTime, lastEditTime", "thema='".\Main\DB::escape($tId)."'");
		while($row = $rst->fetch_object())
		{
			$startTime = $row->startTime; 
			$lastEditTime = $row->lastEditTime; 
			
			if($startTime > $lastUserTime && $startTime > ($nowtime-(86400 * 4))) $unseen ++; 
			else if($lastEditTime > $lastUserTime && $lastEditTime > ($nowtime-(86400 * 4))) $unseen ++; 
		}
			
		
		return $unseen; 
	}
	
	/**
	* Ungelesene Beiträge einer Kategorie 
	*
	* Diese Funktion zählt die (für den Benutzer) ungelesenen Beiträge einer Kategorie und gibt die Anzahl zurück. 
	*
	* @version 0.1.0 
	* @author s-l 
	* @return int 
	*/
	public static function UnseenPostsInKategory($kId) 
	{
		if(\Main\User\Control::$dbid == 0) return 0; 
		$unseen = 0; 
		$nowtime = time(); 
		
		$rst = \Main\DB::select("themen", "id, lastChange", "kategorie='".\Main\DB::escape($kId)."'");
		while($row = $rst->fetch_object())
		{
			$lastChange = $row->lastChange;
			$tId = $row->id; 
			
			$lastUserTime = 0; 
			$result = \Main\DB::select("themes_seen", "stamp", "user='".\Main\DB::escape(\Main\User\Control::$dbid)."' AND thema='".\Main\DB::escape($tId)."'");
			if($result->num_rows > 0) 
			{
				$roww = $result->fetch_object(); 
				$lastUserTime = $roww->stamp; 
			}
			
			if($lastChange > $lastUserTime && $lastChange > ($nowtime-(86400 * 4))) $unseen ++; 
		}
		
		return $unseen; 
	}
	
	/**
	* Tag in Kategorie erlaubt? 
	*
	* Überprüft ob der angegebene Tag in der angegebenen Kategorie verwendet werden darf. 
	*
	* @author s-l 
	* @version 0.0.1 
	* @return bool 
	*/
	public static function TagAvailableInKategory($tag, $k) 
	{
		$rst = \Main\DB::select("tags_foren", "kategory", "tag='" . \Main\DB::escape($tag) . "'");
		if($rst->num_rows == 0) return true; 
		
		$available = false; 
		
		while($row = $rst->fetch_object()) 
		{
			$tf = array("kategory" => $row->kategory);	
			if($tf["kategory"] == $k) 	
				$available = true; 
		}		
			
		if(!$available) 
		{
			$result = \Main\DB::select("kategorien", "kategorie, forum", "id='" . \Main\DB::escape($k) . "'");
			$roww = $result->fetch_object(); 
			$kat = array("kategorie" => $roww->kategorie,
						"forum" => $roww->forum);
						
			if($kat["kategorie"] != 0) 
				$available = self::TagAvailableInKategory($tag, $kat["kategorie"]); 
			else if($kat["forum"] != 0) 
				$available = self::TagAvailableInForum($tag, $kat["forum"]); 
		}
		
		return $available; 
	}
		
	/**
	* Tag im Forum erlaubt? 
	*
	* Überprüft ob der angegebene Tag in dem angegebenen Forum verwendet werden darf. 
	*
	* @author s-l 
	* @version 0.0.1 
	* @return bool 
	*/	
	public static function TagAvailableInForum($tag, $f)
	{
		$rst = \Main\DB::select("tags_foren", "forum", "tag='" . \Main\DB::escape($tag) . "'");
		if($rst->num_rows == 0) return true; 
		
		$available = false; 
		
		while($row = $rst->fetch_object()) 
		{
			$tf = $row->forum; 
			if($f == $tf) 
				$available = true; 
		}
			
		return $available; 
	}

	/**
	* Tag für Benutzer erlaubt? 
	*
	* Überprüft ob der angegebene Tag von dem Benutzer verwendet werden darf 
	*
	* @author s-l 
	* @version 0.0.1 
	* @return bool 
	*/	
	public static function TagAvailableForUser($tag) 
	{
		$rst = \Main\DB::select("tags_gruppen", "gruppe", "tag='".\Main\DB::escape($tag)."'");
		if($rst->num_rows == 0) return true; 
		
		$available = false; 
		
		while($row = $rst->fetch_object())
		{
			$gruppe = $row->gruppe; 
			if(\Main\User\Control::IsUserInGruppe(\Main\User\Control::$dbid, $gruppe)) 
				$available = true; 
		}
		
		return $available; 
	}
	
	public static function countNewThemaAvailableTags($kid) 
	{
		$count = 0; 
		$rst = \Main\DB::select("tags", "id", "useable='1' AND typ='0'");
		while($row = $rst->fetch_object())
		{
			$tag = array("id" => $row->id);
			if(self::TagAvailableInKategory($tag["id"], $kid)) 
			{
				if(self::TagAvailableForUser($tag["id"])) 
					$count++; 
			}
		}
		return $count; 
	}
	
	public static function countNewThemaAvailableLabels($kid) 
	{
		$count = 0; 
		$rst = \Main\DB::select("tags", "id", "typ='1'");
		while($row = $rst->fetch_object())
		{
			$tag = array("id" => $row->id);
			if(self::TagAvailableInKategory($tag["id"], $kid)) 
			{
				if(self::TagAvailableForUser($tag["id"])) 
					$count++; 
			}
		}
		return $count; 
	}
	
	public static function listTagOptionsForKategory($kid) 
	{
		$html = ""; 

		$rst = \Main\DB::select("tags", "id, name", "useable='1' AND typ='0'");
		while($row = $rst->fetch_object())
		{
			$tag = array("id" => $row->id,
						"name" => $row->name);
			if(self::TagAvailableInKategory($tag["id"], $kid))
			{
				if(self::TagAvailableForUser($tag["id"]))
				{
					$html .= "<option value='".$tag["id"]."'>".htmlspecialchars($tag["name"])."</option>"; 
				}
			}
		}

		return $html; 
	}

	public static function listLabelOptionsForKategory($kid, $editTheme=false, $editingTheme=0) 
	{
		$html = ""; 

		

		$rst = \Main\DB::select("tags", "id, name, backgroundcolor, textcolor", "typ='1'");
		while($row = $rst->fetch_object())
		{
			$tag = array("id" => $row->id,
						"name" => $row->name,
						"bgcol" => $row->backgroundcolor,
						"txtcol" => $row->textcolor);

			if(self::TagAvailableInKategory($tag["id"], $kid))
			{
				if(self::TagAvailableForUser($tag["id"]))
				{
					$checked = ""; 
					if($editTheme) 
					{
						$result = \Main\DB::select("themen_labels", "id", "thema='" . \Main\DB::escape($editingTheme) . "' AND label='" . \Main\DB::escape($tag["id"]) . "'");
						if($result->num_rows > 0) 
						{
							$checked = "checked"; 
						}
					}

					$bgcolstring = ""; 
					$txtcolstring = ""; 
					if($tag["bgcol"] != "") 
						$bgcolstring = "background:".$tag["bgcol"].";";
					if($tag["txtcol"] != "") 
						$txtcolstring = "color:".$tag["txtcol"].";";  

					if($html != "") $html .= " &nbsp; &nbsp; &nbsp; &nbsp; "; 
					$html .= "<input type='checkbox' class='' name='tag_" . $tag["id"] . "' value='1' $checked  /> " . "<span class='badge label' style='$bgcolstring $txtcolstring'>" . htmlspecialchars($tag["name"]) . "</span>";
				}
			}
		}

		return $html; 
	}

	public static function setLabelForTheme($tid, $lid) 
	{
		\Main\DB::insert("themen_labels", array("thema" => $tid, "label" => $lid));
	}

	/**
	* Foren-Struktur auflisten
	*
	* Listet zum Beispiel beim verschieben eines Themas alle Foren/Kategorien auf. 
	* 
	* @author s-l 
	* @version 0.0.1 
	* @return string 
	*/
	public static function listBoardOptions() 
	{
		function listBoardKategories($bid) 
		{
			$html = ""; 
			$leer = "&nbsp;&nbsp;&nbsp;";
			$rst = \Main\DB::select("kategorien", "id, name", "forum='".\Main\DB::escape($bid)."'", null, "orderId, id");
			while($row = $rst->fetch_object())
			{
				$kid = $row->id; 
				$kname = $row->name; 
				$html .= "<option value='k$kid'>$leer".htmlspecialchars($kname)."</option>"; 
				$html .= listKategoryKategories($kid, 6); 
			}

			return $html; 
		}

		function listKategoryKategories($kid, $leer=0) 
		{
			$html = ""; 

			$leers = ""; 
			for($i = 0; $i < $leer; $i++) 
			{
				$leers .= "&nbsp;"; 
			}

			$rst = \Main\DB::select("kategorien", "id, name", "kategorie='".\Main\DB::escape($kid)."'", null, "orderId, id");
			while($row = $rst->fetch_object())
			{
				$kid = $row->id; 
				$kname = $row->name; 
				$html .= "<option value='k$kid'>$leers".htmlspecialchars($kname)."</option>";
				$html .= listKategoryKategories($kid, $leer + 3);  
			}

			return $html; 
		}

		$html = ""; 

		$rst = \Main\DB::select("kategorien", "id, name", "forum='0' AND kategorie='0'", null, "orderId, id");
		while($row = $rst->fetch_object())
		{
			$kid = $row->id; 
			$kname = $row->name; 
			$html .= "<option value='k$kid'>".htmlspecialchars($kname)."</option>";
			$html .= listKategoryKategories($kid, 3);  
		}

		$rst = \Main\DB::select("foren", "id, name", null, null, "orderId, id");
		while($row = $rst->fetch_object())
		{
			$bid = $row->id; 
			$bname = $row->name; 
			$html .= "<option value='b$bid'>".htmlspecialchars($bname)."</option>";
			$html .= listBoardKategories($bid); 
		}

		return $html; 
	}

	public static function countThemes() 
	{
		$rst = \Main\DB::select("themen", "id");
		return $rst->num_rows; 
	}

	public static function countPosts() 
	{
		$rst = \Main\DB::select("posts", "id");
		return $rst->num_rows; 
	}

	/**
	* Beitrag Seite ermitteln
	*
	* Diese Methode ermittelt die Seite (pageNo) auf der sich der angegebene Beitrag in seinem Thema befindet. 
	* 
	* @author s-l 
	* @version 0.0.1 
	* @return int 
	*/
	public static function getPostPage($post) 
	{
		$rst = \Main\DB::select("posts", "thema", "id='".\Main\DB::escape($post)."'");
		if($rst->num_rows == 0) return 0; 
		$row = $rst->fetch_object(); 
		$thema = $row->thema; 

		$max_posts_per_page = 13; 

		$count = 0; 
		$npage = 1; 
		$rst = \Main\DB::select("posts", "id", "thema='".\Main\DB::escape($thema)."'", null, "id");
		while($row = $rst->fetch_object())
		{
			$pid = $row->id; 
			$count++; 
			if($pid == $post) 
			{
				return $npage; 
			}
			if($count >= 13) 
			{
				$npage++; 
				$count = 0; 
			}
		}
		return 0; 
	}

	
}

?>