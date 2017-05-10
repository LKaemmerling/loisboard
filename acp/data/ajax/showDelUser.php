<?php
session_start(); 

require_once("../../../core/DB.php");
require_once("../../../config/db.php"); 
require_once("../../core/UserControl.php"); 

DB::init($db["host"], $db["user"], $db["pw"], $db["db"]);
UserControl::init();

$id = $_POST["id"];  
$num = $_POST["num"]; 

$html = ""; 
if(UserControl::$logged) 
{
	
	$rst = DB::select("accounts", "username", "id='".DB::escape($id)."'");
	if($rst->num_rows > 0) 
	{
		$row = $rst->fetch_object(); 
		$name = $row->username; 
		
		$html .= "<h3>Möchtest du wirklich den Benutzer ".htmlspecialchars($name)." unwiederruflich löschen?</h3>
		<br /><br />
		<button onclick='DelUser(".htmlspecialchars($id, ENT_QUOTES).", $num);' class='btn btn-success'>Ja</button> &nbsp; <button onclick='hideMiddleWindow();' class='btn btn-danger'>Nein</button>"; 
	}
	else
	{
		$html .= "<div class='alert alert-danger'>Dieser Account existiert nicht!</div>"; 
	}
	
	
}

echo $html; 
?>