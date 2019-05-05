<?php
#
# convert a float to hash_power 
#   H/s, MH/s, KH/s, GH/s, TH/s, PH/s 
#  For Miner-Control.de : sven-goessling.de

function fun_hash($power) {
	$power = (float)$power;
	
	$hash_string[0] = 'H';
	$hash_string[1] = 'KH';
	$hash_string[2] = 'MH';
	$hash_string[3] = 'GH';
	$hash_string[4] = 'TH';
	$hash_string[5] = 'PH';
	$count = 0;
	while ($power > 1000) {
		$power = ($power/1000);
		$count++;
	}
	
	return sprintf("%.2f",$power).' '.$hash_string[$count];
}

function smarty_modifier_hash($arrData) {
   return fun_hash($arrData);
}

?>