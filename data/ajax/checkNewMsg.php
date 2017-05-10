<?php

require_once("../../core/DB.php"); 
require_once("../../config/db.php"); 

\Main\DB::init($db["host"], $db["user"], $db["pw"], $db["db"]); 

function countKonversationUnseenMsgs($user, $cid) 
{
    $count = 0; 
    $rst = \Main\DB::select("conversation_msg", "id", "user!='".\Main\DB::escape($user)."' AND conversation='".\Main\DB::escape($cid)."'");
	while($row = $rst->fetch_object())
	{
		$msgid = $row->id; 
		$result = \Main\DB::select("conversation_msg_seen", "id", "user='".\Main\DB::escape($user)."' AND msg='".\Main\DB::escape($msgid)."'");
		if($result->num_rows == 0) 
			$count++; 
	}
    return $count; 
}

if(isset($_POST["user"]))
{
	$user = $_POST["user"]; 
	$count = 0; 
	$sql = "SELECT conversations.id FROM conversations INNER JOIN conversation_users ON conversation_users.conversation=conversations.id AND conversation_users.user='".\Main\DB::escape($user)."'";
	$rst = \Main\DB::query($sql); 
	while($row = $rst->fetch_object())
	{
		$cid = $row->id; 
		$count += countKonversationUnseenMsgs($user, $cid); 
	}
	echo $count; 
}
else echo 0; 
?>