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
	
	DB::delete("foren", $id);
	
	
}
?>