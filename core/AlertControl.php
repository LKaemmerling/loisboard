<?php
namespace Main\User; 
class Alert
{
    /**
    * Alert Typen: 
        1: der Benutzer hat eine Antwort auf dein Thema verfasst
        2: der Benutzer hat eine Antwort auf ein Thema verfasst auf das du auch bereits geantwortet hast
        3: der Benutzer hat einen Beitrag auf deiner Pinnwand verfasst 
        4: der Benutzer hat auf einen Beitrag auf deiner Pinnwand geantwortet 
        5: der Benutzer hat auf deinen Beitrag auf Benutzers Pinnwand geantwortet 
    */

    /**
    * Benutzer hat auf ein Thema geantwortet
    *
    * Sendet alle Benachrichtigungen aus wenn ein Benutzer eine Antwort auf ein Thema verfasst hat. 
    * Sendet eine Benachrichtigung an den Themen Ersteller
    * Sendet allen eine Benachrichtigung die bereits auf das Thema geantwortet haben
    * 
    * @author s-l 
    * @version 0.0.6 
    */
    public static function UserAnsweredTheme($uid, $tid) 
    {
        $rst = \Main\DB::select("themen", "user", "id='".\Main\DB::escape($tid)."'");
        $row = $rst->fetch_object(); 
        $tuid = $row->user; 
        /*  Richtig? 
        $rst = \Main\PDB::select("themen", "user", "id=?", null, null, array($tid));
        $tuid = $rst[0]["user"]; 
        */

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
        /* Richtig? 
        $rst = \Main\PDB::select("posts", "user", "thema=?", null, null, array($tid));
        foreach($rst as $row) 
        {
            $puid = $row["user"]; 
            if($puid == $tuid) continue; 
            if($puid == $uid) continue; 
            self::sendUserAlert($puid, $uid, 1, $tid, 0); 
        }
        */
    } 

    /**
    * Alert senden 
    *
    * Überprüft ob bereits ein Alert mit den angegebenen Parametern existiert (das ebenfalls noch nicht gesehen wurde) 
    * -> Wenn ja: Schreibt den Benutzer zum Alert dazu
    * -> Wenn nein: Erstellt ein neues Alert und schreibt den Benutzer drauf 
    *
    * @author s-l 
    * @version 0.0.5 
    */
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
        /*
        $rst = \Main\PDB::select("alerts", "id", "user=? AND typ=? AND theme=? AND post=? AND gesehen='0'", null, null, array($user, $typ, $theme, $post));
        if(count($rst) == 0) 
        {
            \Main\PDB::insert("alerts", array("user" => $user, "typ" => $typ, "time" => time(), "theme" => $theme, "post" => $post));
            $insID = \Main\PDB::insertID(); 
            \Main\PDB::insert("alert_users", array("user" => $sender, "alert" => $insID));
            return true;
        }
        */

        # Alert existiert -> User dazu schreiben 
        $row = $rst->fetch_object(); 
        $aid = $row->id; 

        /*
        $aid = $rst[0]["id"]; 
        */

        $rst = \Main\DB::select("alert_users", "id", "user='".\Main\DB::escape($sender)."' AND alert='".\Main\DB::escape($aid)."'");
        if($rst->num_rows == 0) // Nur die Benachrichtigung senden wenn noch keine von diesem Benutzer vorhanden ist. 
            \Main\DB::insert("alert_users", array("user" => $sender, "alert" => $aid));

        /*
        $rst = \Main\PDB::select("alert_users", "id", "user=? AND alert=?", null, null, array($sender, $aid));
        if(count($rst) == 0) 
            \Main\PDB::insert("alert_users", array("user" => $sender, "alert" => $aid));
        */
    }

    /**
    * Benachrichtigung als gesehen markieren
    *
    * Markiert die Benachrichtigung $alertid für den Benutzer $uid als gesehen (sofern die Benachrichtigung dem Benutzer gehört) 
    *
    * @author s-l 
    * @version 0.0.3 
    */
    public static function checkUserAlert($uid, $alertid) 
    {
        $rst = \Main\DB::select("alerts", "id", "user='".\Main\DB::escape($uid)."' AND id='".\Main\DB::escape($alertid)."'");
        if($rst->num_rows > 0) 
        {
            \Main\DB::update("alerts", $alertid, array("gesehen" => 1));
        }
        /*
        $rst = \Main\PDB::select("alerts", "id", "user=? AND id=?", null, null, array($uid, $alertid));
        if(count($rst) > 0) 
        {
            \Main\PDB::update("alerts", $alertid, "gesehen='1'");
        }
        */
    }

    /**
    * Benachrichtigungen Zählen
    *
    * Zählt die ungesehenen Benachrichtigungen eines Benutzers
    * 
    * @author s-l 
    * @version 0.0.2 
    * @return int 
    */
    public static function countUserAlerts($uid) 
    {
        $rst = \Main\DB::select("alerts", "gesehen", "user='".\Main\DB::escape($uid)."'", "100", "id DESC");
        $counter = 0; 
        while($row = $rst->fetch_object())
        {
            $gesehen = $row->gesehen; 
            if($gesehen == 0) $counter++; 
        }

        /*
        $rst = \Main\PDB::select("alerts", "id", "user=? AND gesehen='0'", "id DESC", "100", array($uid));
        $counter = count($rst); 
        */
        return $counter; 
    }


}

?>