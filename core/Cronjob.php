<?php

require_once("DB.php"); 
require_once("../config/db.php"); 

\Main\DB::init($db["host"], $db["user"], $db["pw"], $db["db"]); 

// Kategorien aussortieren
$rst = \Main\DB::select("kategorien", "id, kategorie, forum");
while($row = $rst->fetch_object())
{
	$kategorie = array(
		"id" => $row->id,
		"kategorie" => $row->kategorie,
		"forum" => $row->forum
	);
	
	if($kategorie["kategorie"] == 0 && $kategorie["forum"] == 0) 
		continue; 
		
	if($kategorie["kategorie"] != 0 && $kategorie["forum"] == 0) 
	{
		$result = \Main\DB::select("kategorien", "id", "id='".\Main\DB::escape($kategorie["kategorie"])."'");
		if($result->num_rows == 0) 
			\Main\DB::delete("kategorien", $kategorie["id"]); 
	}
	
	if($kategorie["forum"] != 0 && $kategorie["kategorie"] == 0) 
	{
		$result = \Main\DB::select("foren", "id", "id='".\Main\DB::escape($kategorie["forum"])."'");
		if($result->num_rows == 0) 
			\Main\DB::delete("kategorien", $kategorie["id"]); 
	}
}

// Themen aussortieren
$rst = \Main\DB::select("themen", "id, kategorie");
while($row = $rst->fetch_object())
{
	$thema = array(
		"id" => $row->id,
		"kategorie" => $row->kategorie
	);
	
	$result = \Main\DB::select("kategorien", "id", "id='".\Main\DB::escape($thema["kategorie"])."'");
	if($result->num_rows == 0) 
		\Main\DB::delete("themen", $thema["id"]); 
}

// Beiträge aussortieren
$rst = \Main\DB::select("posts", "id, thema");
while($row = $rst->fetch_object())
{	
	$post = array(
		"id" => $row->id,
		"thema" => $row->thema
	);
	
	$result = \Main\DB::select("themen", "id", "id='".\Main\DB::escape($post["thema"])."'");
	if($result->num_rows == 0) 
		\Main\DB::delete("posts", $post["id"]); 
}

// Verweise auf Benutzergruppen aussortieren
$rst = \Main\DB::select("gruppen_user", "id, gruppe");
while($row = $rst->fetch_object())
{
	$gu = array("id" => $row->id,
	"gruppe" => $row->gruppe
	);
	$result = \Main\DB::select("gruppen", "id", "id='".\Main\DB::escape($gu["gruppe"])."'");
	if($result->num_rows == 0) 
	{
		\Main\DB::delete("gruppen_user", $gu["id"]); 
	}
}

//echo "Cronjob: OK"; 
?>