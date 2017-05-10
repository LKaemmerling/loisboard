<?php
session_start(); 

require_once("../../../core/DB.php");
require_once("../../../config/db.php"); 
require_once("../../core/UserControl.php"); 

DB::init($db["host"], $db["user"], $db["pw"], $db["db"]);
UserControl::init();

$id = $_POST["id"];  

$html = ""; 
if(UserControl::$logged) 
{
	
	$rst = DB::select("foren", "name", "id='".DB::escape($id)."'");
	if($rst->num_rows > 0) 
	{
		$row = $rst->fetch_object(); 
		$name = $row->name; 
		
		$html .= "<h3>Möchtest du wirklich das Forum ".htmlspecialchars($name)." unwiederruflich löschen?</h3>
		Alle darin enthaltenen Kategorien sowie sämtliche Themen werden ebenfalls unwiederruflich gelöscht werden!<br /><br />
		<button onclick='DelBoard(".htmlspecialchars($id, ENT_QUOTES).");' class='btn btn-success'>Ja</button> &nbsp; <button onclick='hideMiddleWindow();' class='btn btn-danger'>Nein</button>"; 
	}
	else
	{
		$html .= "<div class='alert alert-danger'>Dieses Forum existiert nicht!</div>"; 
	}
	
	
}

echo $html; 
?>