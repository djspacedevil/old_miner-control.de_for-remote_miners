<?php
/**
 * @copyright 2010 MAPIX Technologies Ltd, UK, http://mapix.com/
 * @license http://en.wikipedia.org/wiki/BSD_licenses  BSD License
 * @package Smarty
 * @subpackage PluginsModifier
 */


function smarty_modifier_seconds_to_words($seconds) {
	if ($seconds < 0) throw new Exception("Can't do negative numbers!");
	if ($seconds == 0) return "zero seconds";
	
	$days = intval($seconds/(24*pow(60,2)));
	if ($days > 0) {
		return $days . " day". ($days > 1 ? "s" : "");
	} else {
	        $hours = intval($seconds/pow(60,2))%24;
       	 	$minutes = intval($seconds/60)%60;
        	$seconds = $seconds%60;
        	$out = "";

		if ($hours > 0) $out .= $hours . " Hour". ($hours > 1 ? "(s)" : "")." ";
		if ($minutes > 0) $out .= $minutes . " Min". ($minutes > 1 ? "(s)" : "")." ";
		if ($seconds > 0) $out .= $seconds . " Sec". ($seconds > 1 ? "(s)" : "");
		return trim($out);
	}	
}

?>