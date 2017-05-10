<?php

require_once("../../core/DB.php"); 
require_once("../../config/db.php"); 

\Main\DB::init($db["host"], $db["user"], $db["pw"], $db["db"]); 

if(isset($_POST["user"]))
{
    $uId = $_POST["user"]; 
    $sql = "UPDATE accounts_online SET lastCheck='".time()."' WHERE user='".\Main\DB::escape($uId)."'";
    \Main\DB::query($sql); 
}

?>