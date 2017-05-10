<?php
require_once("../../core/DB.php"); 
require_once("../../config/db.php"); 

\Main\DB::init($db["host"], $db["user"], $db["pw"], $db["db"]); 

if(isset($_POST["user"]))
{
    $user = $_POST["user"]; 
    $rst = \Main\DB::select("alerts", "gesehen", "user='".\Main\DB::escape($user)."'", "100", "id DESC");
    $counter = 0; 
    while($row = $rst->fetch_object())
    {
        $gesehen = $row->gesehen; 
        if($gesehen == 0) $counter++; 
    }
    echo $counter; 
}
else echo 0; 
?>