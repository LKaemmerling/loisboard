<?php
namespace Main\User; 
class Alert
{

    /**

    * Wann kommt ein Alert:
        . Sobald ein Benutzer auf ein Thema antwortet (alle im Thema involvierten Personen bekommen ein Alert) 
        . Sobald ein Benutzer einem anderen auf die Pinnwand postet

    * Alert Typen: 
        1: der Benutzer hat eine Antwort auf dein Thema verfasst
        2: der Benutzer hat eine Antwort auf ein Thema verfasst auf das du auch bereits geantwortet hast
        3: der Benutzer hat einen Beitrag auf deiner Pinnwand verfasst 
        4: der Benutzer hat auf einen Beitrag auf deiner Pinnwand geantwortet 
        5: der Benutzer hat auf deinen Beitrag auf Benutzers Pinnwand geantwortet 
    */

    public static function UserAnsweredTheme($uid, $tid) 
    {
        $rst = \Main\DB::select("themen", "user", "id='".\Main\DB::escape($tid)."'");
        $row = $rst->fetch_object(); 
        $tuid = $row->user; 

        if($tuid != $uid) 
        {
            self::sendUserAlert($tuid, $uid, 1, $tid, 0); 
        }

        // Alle antworten auf das Thema durchgehen und jedem Benutzer der geantwortet hat (ausser $uid und $tuid) eine Benachrichtung senden
        $rst = \Main\DB::select("posts", "user", "thema='".\Main\DB::escape($tid)."'");
        while($row = $rst->fetch_object())
        {   
            $puid = $row->user; 
            if($puid == $tuid) continue; 
            if($puid == $uid) continue; 
            self::sendUserAlert($puid, $uid, 1, $tid, 0); 
        }
    } 


    public static function sendUserAlert($user, $sender, $typ, $theme, $post) 
    {
        $rst = \Main\DB::select("alerts", "id", "user='".\Main\DB::escape($user)."' AND typ='".\Main\DB::escape($typ)."' AND theme='".\Main\DB::escape($theme)."' AND post='".\Main\DB::escape($post)."' AND gesehen='0'");
        if($rst->num_rows == 0) 
        {
            // Neuen Alert dafür anlegen
            \Main\DB::insert("alerts", array("user" => $user, "typ" => $typ, "time" => time(), "theme" => $theme, "post" => $post));
            $insID = \Main\DB::$insertID; 
            \Main\DB::insert("alert_users", array("user" => $sender, "alert" => $insID));
            return true; 
        }

        // Alert existiert -> User Drauf schreiben 
        $row = $rst->fetch_object(); 
        $aid = $row->id; 

        $rst = \Main\DB::select("alert_users", "id", "user='".\Main\DB::escape($sender)."' AND alert='".\Main\DB::escape($aid)."'");
        if($rst->num_rows == 0) // Nur die Benachrichtigung senden wenn noch keine von diesem Benutzer vorhanden ist. 
            \Main\DB::insert("alert_users", array("user" => $sender, "alert" => $aid));
    }

    // als gelesen markieren 
    public static function checkUserAlert($uid, $alertid) 
    {
        $rst = \Main\DB::select("alerts", "id", "user='".\Main\DB::escape($uid)."' AND id='".\Main\DB::escape($alertid)."'");
        if($rst->num_rows > 0) 
        {
            \Main\DB::update("alerts", $alertid, array("gesehen" => 1));
        }
    }

    // zählt die neuen Benachrichtigungen eines Benutzers
    public static function countUserAlerts($uid) 
    {
        $rst = \Main\DB::select("alerts", "gesehen", "user='".\Main\DB::escape($uid)."'", "100", "id DESC");
        $counter = 0; 
        while($row = $rst->fetch_object())
        {
            $gesehen = $row->gesehen; 
            if($gesehen == 0) $counter++; 
        }
        return $counter; 
    }


}

?>