<?php
/**
*LoisBoard 1.0 Plugin
**/

\Main\Plugins::add_listener("index.pageFooter.afterDoks", "hooks_01_footer"); 

function hooks_01_footer($args) 
{
    $html = ""; 
	if(is_array($args)) {
		foreach($args as $arg) {
			$html .= $arg; 
		}
	} else $html .= $args; 

    $show = false; 
    if(UserControl::$logged == 1) 
    {
        if(UserControl::$data["arights"]["enter_administration"] == 1) $show = true; 
    }

    if(!$show) return $html; 

    $html .= "<br />";
    foreach(\Main\Plugins::$hooks as $key => $value)
    {
        $html .= "<strong>Hook:</strong> $value &nbsp; | &nbsp; "; 
    }

    return $html; 
}


?>