<?php
/***************************************************
				Miner-Control.de
			  Author: Sven Gössling
			   Not for Public use!
***************************************************/

if (isset($_GET['ip']) && 
	$_GET['ip'] != "" && 
	filter_var($_GET['ip'], FILTER_VALIDATE_IP) &&
	isset($_GET['code']) &&
	$_GET['code'] != ""
	) {
	$replace = array("'", '"', ' ');
	$with = array('','','');
	$unlock_ip = str_replace($replace, $with, addslashes($_GET['ip']));
	$unlock_code = str_replace($replace, $with, addslashes($_GET['code']));
	
	include_once ( __DIR__ . '/configs/mysql_data.php');
	}

?>