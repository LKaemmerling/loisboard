<?php

namespace Main; 

function toTime($stamp) 
{
	$html = ""; 

	$day_names = array(
		"Monday" => \Main\Language::$txt["monday"],
		"Tuesday" => \Main\Language::$txt["tuesday"],
		"Wednesday" => \Main\Language::$txt["wednesday"],
		"Thursday" => \Main\Language::$txt["thursday"],
		"Friday" => \Main\Language::$txt["friday"],
		"Saturday" => \Main\Language::$txt["saturday"],
		"Sunday" => \Main\Language::$txt["sunday"]
	);
	$month_names = array(
		\Main\Language::$txt["january"],
		\Main\Language::$txt["february"],
		\Main\Language::$txt["march"],
		\Main\Language::$txt["april"],
		\Main\Language::$txt["may"],
		\Main\Language::$txt["june"],
		\Main\Language::$txt["july"],
		\Main\Language::$txt["august"],
		\Main\Language::$txt["september"],
		\Main\Language::$txt["october"],
		\Main\Language::$txt["november"],
		\Main\Language::$txt["december"]
	);

	$nstamp = time();  
	$total_time = $nstamp - $stamp; 
	$days       = floor($total_time /86400);        
	$hours      = floor($total_time /3600);     
	$minutes    = intval(($total_time/60) % 60);        
	$seconds    = intval($total_time % 60);   
	$day_of_the_year = date("z", $stamp); 
	$day_of_the_year_now = date("z", $nstamp); 
	$week_of_the_year = date("W", $stamp); 
	$week_of_the_year_now = date("W", $nstamp); 
	$year = date("Y", $stamp); 
	$year_now = date("Y", $nstamp); 
	$month = date("m", $stamp); 

	if($day_of_the_year == $day_of_the_year_now) # Am gleichen Tag
	{
		if($hours == 0) # Vor x Minuten 
		{
			$html = \Main\Language::$txt["toTime_xminsago"];
			$html = str_replace("[x]", $minutes, $html); 
			return $html; 
		}
		else # Vor x Stunden
		{
			$html = \Main\Language::$txt["toTime_xhoursago"];
			$html = str_replace("[x]", $hours, $html); 
			return $html; 
		}
	}
	else
	{
		if($week_of_the_year == $week_of_the_year_now) # In der gleichen Kalenderwoche 
		{
			$day_name_en = date("l", $stamp);
			$day_name = $day_names[$day_name_en];  
			$html = $day_name . ", " . date("H:i", $stamp); 
			return $html; 
		}
		else # Länger her, Datum anzeigen 
		{
			if($year == $year_now) # Im gleichen Jahr -> Ausgeschriebenes Monat, keine Jahreszahl 
			{
				$month_name = $month_names[$month - 1]; 
				$html = date("d.", $stamp) . " " . $month_name . ", " . date("H:i", $stamp); 
				return $html; 
			}
			else
			{
				$html = date("d.m.Y, H:i", $stamp); 
				return $html; 
			}
		}
	}

	$html = date("d.m.Y, H:i", $stamp); 
	return $html; 
}

function toTimeDat($stamp)
{
	$html = ""; 
	
	$months = array(
		\Main\Language::$txt["january"], 
		\Main\Language::$txt["february"], 
		\Main\Language::$txt["march"], 
		\Main\Language::$txt["april"], 
		\Main\Language::$txt["may"], 
		\Main\Language::$txt["june"], 
		\Main\Language::$txt["july"], 
		\Main\Language::$txt["august"], 
		\Main\Language::$txt["september"], 
		\Main\Language::$txt["october"], 
		\Main\Language::$txt["november"], 
		\Main\Language::$txt["december"]
	);
	
	$day = date("d", $stamp); 
	$month = date("m", $stamp); 
	$year = date("Y", $stamp); 
	$hour = date("H",$stamp); 
	$min = date("i", $stamp); 
	
	$html = $day.". ".$months[$month-1]." ".$year.", ".$hour.":".$min; 
	
	return $html; 
}

function toTimeDatNoTime($stamp)
{
	$html = ""; 
	
	$months = array(
		\Main\Language::$txt["january"], 
		\Main\Language::$txt["february"], 
		\Main\Language::$txt["march"], 
		\Main\Language::$txt["april"], 
		\Main\Language::$txt["may"], 
		\Main\Language::$txt["june"], 
		\Main\Language::$txt["july"], 
		\Main\Language::$txt["august"], 
		\Main\Language::$txt["september"], 
		\Main\Language::$txt["october"], 
		\Main\Language::$txt["november"], 
		\Main\Language::$txt["december"]
	);
	
	$day = date("d", $stamp); 
	$month = date("m", $stamp); 
	$year = date("Y", $stamp); 
	
	$html = $day.". ".$months[$month-1]." ".$year; 
	
	return $html; 
}

function toTime2($stamp) 
{	
	$html = ""; 

	$nstamp = time();  
	$total_time = $nstamp - $stamp; 
	$days       = floor($total_time /86400);        
	$hours      = floor($total_time /3600);     
	$minutes    = intval(($total_time/60) % 60);        
	$seconds    = intval($total_time % 60);     

	if($days == 0) 
	{
		$html .= \Main\Language::$txt["toTime_today"]; 
	}
	if($days == 1) 
	{
		$html .= \Main\Language::$txt["toTime_yesterday"]; 
	}
	if($days > 1) 
	{		
		$html = ""; 
		$string = \Main\Language::$txt["toTime2_atxdat"]; 
		$string = str_replace("[x]", date("d.m.Y", $stamp), $string);
		$html .= $string; 
	}
	
	$html .= date("H:i", $stamp);
	
	return $html; 
}

function toTime2JustDat($stamp) 
{	
	$html = ""; 
	
	$months = array(\Main\Language::$txt["january"], \Main\Language::$txt["february"], \Main\Language::$txt["march"], \Main\Language::$txt["april"], \Main\Language::$txt["may"], \Main\Language::$txt["june"], \Main\Language::$txt["july"], \Main\Language::$txt["august"], \Main\Language::$txt["september"], \Main\Language::$txt["october"], \Main\Language::$txt["november"], \Main\Language::$txt["december"]);
	
	$day = date("d", $stamp); 
	$month = date("m", $stamp); 
	$year = date("Y", $stamp); 
	
	$html = $day.". ".$months[$month-1]." ".$year; 
	
	return $html; 
}

function createRandomKey() 
{
	$html = ""; 
	
	$bu = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
	
	for($i = 0; $i < 30; $i++) 
	{
		$random = mt_rand(0, count($bu)-1); 
		
		$upper = mt_rand(1, 2); 
		if($upper == 1) $html .= strtoupper($bu[$random]);
		else $html .= $bu[$random]; 
		
	}
	
	return $html; 
}

function rrmdir($dir) { 
   if (is_dir($dir)) { 
     $objects = scandir($dir); 
     foreach ($objects as $object) { 
       if ($object != "." && $object != "..") { 
         if (is_dir($dir."/".$object))
            \Main\rrmdir($dir."/".$object);
         else
           unlink($dir."/".$object); 
       } 
     }
     rmdir($dir); 
   } 
 }


function makeLinks($str) {
	$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
	//$reg_exUrl = "/(((http|https|ftp|ftps)\:\/\/)|(www\.))[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\:[0-9]+)?(\/\S*)?/";
	$urls = array();
	$urlsToReplace = array();
	if(preg_match_all($reg_exUrl, $str, $urls)) {
		$numOfMatches = count($urls[0]);
		$numOfUrlsToReplace = 0;
		for($i=0; $i<$numOfMatches; $i++) {
			$alreadyAdded = false;
			$numOfUrlsToReplace = count($urlsToReplace);
			for($j=0; $j<$numOfUrlsToReplace; $j++) {
				if($urlsToReplace[$j] == $urls[0][$i]) {
					$alreadyAdded = true;
				}
			}
			if(!$alreadyAdded) {
				array_push($urlsToReplace, $urls[0][$i]);
			}
		}
		$numOfUrlsToReplace = count($urlsToReplace);
		for($i=0; $i<$numOfUrlsToReplace; $i++) {
			$str = str_replace($urlsToReplace[$i], "<a target='_blank' href=\"".$urlsToReplace[$i]."\">".$urlsToReplace[$i]."</a> ", $str);
		}
		return $str;
	} else {
		return $str;
	}
}

function makelink($text_msg) {
	return preg_replace('/(http[s]{0,1}\:\/\/\S{4,})\s{0,}/ims', '<a href="$1" target="_blank">$1</a> ', $text_msg);
}

function MakeUrls($str)
{
$find=array('`((?:https?|ftp)://\S+[[:alnum:]]/?)`si','`((?<!//)(www\.\S+[[:alnum:]]/?))`si');

$replace=array('<a href="$1" target="_blank">$1</a>', '<a href="http://$1" target="_blank">$1</a>');

return preg_replace($find,$replace,$str);
}

function AutoLinkUrls($str,$popup = FALSE){
    if (preg_match_all("#(^|\s|\()((http(s?)://)|(www\.))(\w+[^\s\)\<]+)#i", $str, $matches)){
        $pop = ($popup == TRUE) ? " target=\"_blank\" " : "";
        for ($i = 0; $i < count($matches['0']); $i++){
            $period = '';
            if (preg_match("|\.$|", $matches['6'][$i])){
                $period = '.';
                $matches['6'][$i] = substr($matches['6'][$i], 0, -1);
            }
            $str = str_replace($matches['0'][$i],
                    $matches['1'][$i].'<a href="http'.
                    $matches['4'][$i].'://'.
                    $matches['5'][$i].
                    $matches['6'][$i].'"'.$pop.'>http'.
                    $matches['4'][$i].'://'.
                    $matches['5'][$i].
                    $matches['6'][$i].'</a>'.
                    $period, $str);
        }//end for
    }//end if
    return $str;
}//end AutoLinkUrls

?>