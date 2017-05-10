<?php
session_start(); 

require_once("../../../core/DB.php");
require_once("../../../config/db.php"); 
require_once("../../core/UserControl.php"); 

DB::init($db["host"], $db["user"], $db["pw"], $db["db"]);
UserControl::init();

$id = $_POST["id"];  

if(UserControl::$logged) 
{
	
	DB::delete("kategorien", $id);
	
	$rst = DB::select("themen", "id", "kategorie='".DB::escape($id)."'");
	while($row = $rst->fetch_object())
	{
		$tid = $row->id; 
		
		$sql = "DELETE FROM posts WHERE thema='".DB::escape($tid)."'";
		DB::query($sql); 
		
		DB::delete("themen", $tid); 
	}
	
	
}
?>