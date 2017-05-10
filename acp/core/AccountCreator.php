<?php

function CreateAccounts($anz) 
{
	for($i = 0; $i < $anz; $i++) 
	{
		CreateAccount();
	}
	return true; 
}

function CreateAccount() 
{
	$name = GetUserName(); 
	while(strlen($name) < 6) {
		$name = GetUserName(); 
	}
	$rst = \Main\DB::select("accounts", "id", "username='".\Main\DB::escape($name)."'");
	while($rst->num_rows > 0) {
		$name = GetUserName(); 
		$rst = \Main\DB::select("accounts", "id", "username='".\Main\DB::escape($name)."'");
	}
	
	\Main\DB::insert("accounts", array(
		"username" => $name,
		"passwort" => "ebbe3343d8b875accb9e1272d1e429f7",
		"registerTime" => time(),
		"lastLogin" => time()
	));
}

function GetUserName() 
{
	$html = ""; 
	
	$pres = array("Jo", "He", "Bn", "Ro", "We", "wE", "G.I.", "PP.", "PP", "Fun", "Gamer");
	$mid = array("", "nix", "", "", "rix", "", "");
	$aft = array("hn", "69", "_", "2", "_pipe", "P", "Z", "Q", "_HE", "H<per");
	
	$rand = mt_rand(0, count($pres)-1);
	$html .= $pres[$rand]; 
	$rand = mt_rand(0, count($mid)-1); 
	$html .= $mid[$rand]; 
	$rand = mt_rand(0, count($aft)-1); 
	$html .= $aft[$rand]; 
	
	return $html; 
}

require_once("../../core/DB.php");
require_once("../../config/db.php");

\Main\DB::init($db["host"], $db["user"], $db["pw"], $db["db"]);

//CreateAccounts(1); 
if(isset($_GET["do"]))
{
	CreateAccounts($_GET["do"]); 
	echo "Es wurden ".$_GET["do"]." Accounts erstellt!!<br /><br />";
}
echo "Wie viele Accounts erstellen?<br /><br />
<form action='AccountCreator.php' method='get'>
	<input type='number' value='0' name='do' /><br />
	<button>Erstellen</button>
</form>"; 
?>