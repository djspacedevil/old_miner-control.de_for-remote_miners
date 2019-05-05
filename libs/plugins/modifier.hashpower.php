<?php
#
# convert a float to hash_power 
#   H/s, MH/s, KH/s, GH/s, TH/s, PH/s 
#  For Miner-Control.de : sven-goessling.de

function fun_hashpower($power) {
	$power = (float)$power;
	
	$hash_string[0] = 'H/s';
	$hash_string[1] = 'KH/s';
	$hash_string[2] = 'MH/s';
	$hash_string[3] = 'GH/s';
	$hash_string[4] = 'TH/s';
	$hash_string[5] = 'PH/s';
	$count = 0;
	while ($power > 1000) {
		$power = ($power/1000);
		$count++;
	}
	
	return sprintf("%.2f",$power).' '.$hash_string[$count];
}

function smarty_modifier_hashpower($arrData) {
   return fun_hashpower($arrData);
}

?>