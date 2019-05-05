<?php
#
# convert a float to hash_power 
#   H/s, MH/s, KH/s, GH/s, TH/s, PH/s 
#  For Miner-Control.de : sven-goessling.de

function fun_numbertrimmer($power) {
	$power = (float)$power;
	
	$hash_string[0] = '';
	$hash_string[1] = 'K';
	$hash_string[2] = 'M';
	$hash_string[3] = 'G';
	$hash_string[4] = 'T';
	
	$count = 0;
	while ($power > 1000) {
		$power = ($power/1000);
		$count++;
	}
	
	return sprintf("%.2f",$power).' '.$hash_string[$count];
}

function smarty_modifier_numbertrimmer($arrData) {
   return fun_numbertrimmer($arrData);
}

?>