<?php
/**
*LoisBoard 1.0 Plugin
**/

\Main\Plugins::add_listener("index.contentRowRight.afterUCP", "lastxposts_rightContent"); 



function lastxposts_rightContent($args) 
{
    $html = ""; 
    if(is_array($args)) {
        foreach($args as $arg) {
            $html .= $arg; 
        }
    } else $html .= $args; 

    $show = false; 

    if(isset($_GET["page"]))
    {
        if($_GET["page"] == "board") 
        {
            if(!isset($_GET["b"]) && !isset($_GET["k"]) && !isset($_GET["t"])) $show = true; 
        }
    } else $show = true; # Abhängig von der Startseite (derzeit immer die Forumseite)

    if($show) # Die letzten X Posts anzeigen 
    {
        LastXPosts::init(); 
        $html .= "<div class='panel panel-primary'><div class='panel-heading'>Die letzten ".LastXPosts::$anzPosts." Beiträge</div>";
            $html .= LastXPosts::listLastPosts(); 
        $html .= "</div>"; 
    }

    return $html; 
}


class LastXPosts 
{
    public static $anzPosts = 5; 

    public static function init() 
    {
        $sql = "CREATE TABLE IF NOT EXISTS plugin_lastxposts (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            anzPosts INT NOT NULL DEFAULT '0'
        ) Engine=INNODB";
        \Main\DB::query($sql); 

        $rst = \Main\DB::select("plugin_lastxposts", "anzPosts", null, null, "id DESC"); 
        if($rst->num_rows == 0) 
        {
            \Main\DB::insert("plugin_lastxposts", array("anzPosts" => 10));
            $rst = \Main\DB::select("plugin_lastxposts", "anzPosts", null, null, "id DESC"); 
        }
        $row = $rst->fetch_object(); 
        self::$anzPosts = $row->anzPosts; 
    }

    public static function listLastPosts() 
    {
        $html = ""; 

        $count = 0; 
        $rst = \Main\DB::select("posts", "id, user, thema, startTime", "deleted='0'", null, "id DESC"); 
        while($row = $rst->fetch_object())
        {
            if($count >= self::$anzPosts) break; 
            $post = array(
                "id" => $row->id,
                "user" => $row->user,
                "thema" => $row->thema,
                "time" => $row->startTime
            );
            $result = \Main\DB::select("themen", "name, kategorie", "id='".\Main\DB::escape($post["thema"])."'"); 
            $roww = $result->fetch_object(); 
            $kid = $roww->kategorie; 
            $tname = $roww->name; 
            if(\Main\User\Control::IsUserAllowedToSeeKategory(\Main\User\Control::$dbid, $kid))
            {
                $post["user"] = \Main\User\Control::getUserData($post["user"]); 
                $pageNo = \Main\Board\Control::getPostPage($post["id"]); 
                $unseen = \Main\Board\Control::UnseenPostsInThema($post["thema"]); 
                $pstyle = ""; 
                if($unseen > 0) $pstyle = "font-weight:bold;"; 
                $html .= "<div class='lastxposts_row media'>";
                    $html .= "<div class='media-left'>"; # UserBild
                        if($post["user"]["avatar"] != "") 
                        {
                            $html .= "<img class='img-thumbnail' src='".$post["user"]["avatar"]."' style='width:44px;height:44px;max-width:44px;max-height:44px;padding:2px;border-radius:50%;' />";
                        }
                    $html .= "</div>"; 
                    $html .= "<div class='media-body'>"; 
                    $html .= "<a style='$pstyle' href='index.php?page=board&t=".$post["thema"]."&pageNo=$pageNo#post".$post["id"]."'>".$tname."</a><br />"; 
                    $html .= "<small><a href='index.php?page=members&u=".$post["user"]["dbid"]."'>".$post["user"]["displayname"]."</a>, ".\Main\toTime($post["time"])."</small>"; 
                    $html .= "</div>"; 
                $html .= "</div>"; 
                $count++;  
            }
           
        }

        return $html; 
    }
}


?>