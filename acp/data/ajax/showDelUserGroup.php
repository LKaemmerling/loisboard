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
	
	$rst = DB::select("gruppen", "name", "id='".DB::escape($id)."'");
	if($rst->num_rows > 0) 
	{
		$row = $rst->fetch_object(); 
		$name = $row->name; 
		
		$html .= "<h3>Möchtest du wirklich die Benutzergruppe ".htmlspecialchars($name)." unwiederruflich löschen?</h3>
		<br /><br />
		<button onclick='DelUserGroup(".htmlspecialchars($id, ENT_QUOTES).");' class='btn btn-success'>Ja</button> &nbsp; <button onclick='hideMiddleWindow();' class='btn btn-danger'>Nein</button>"; 
	}
	else
	{
		$html .= "<div class='alert alert-danger'>Diese Gruppe existiert nicht!</div>"; 
	}
	
	
}

echo $html; 
?>