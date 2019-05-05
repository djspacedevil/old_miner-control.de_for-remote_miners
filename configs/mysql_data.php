<?php
///////////////////////////////////////////
// SQL
	$DB_HOST = 'localhost'; //
	$DB_USER = 'miner_controller'; // miner_controller
	$DB_PASSWORD = ''; // set password
	$DB_DATABASE = 'miner-control_mainsite'; // miner-control_mainsite

	$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_DATABASE);
	if ($con->connect_errno) {
		printf("Connect failed: %s\n", $con->connect_error);
		exit();
	}
	
	//IPs entblocken
	if(isset($unlock_ip) && $unlock_ip != '' && isset($unlock_code) && $unlock_code != '') {
		$result = $con->query("SELECT `id` FROM `mi_blocked` WHERE `blocked_ip` = '".$unlock_ip."' AND `unblock_code` = '".$unlock_code."'");
		if ($result->num_rows == 1) {
			$con->real_query("DELETE FROM `mi_blocked` WHERE `blocked_ip` = '".$unlock_ip."' AND `unblock_code` = '".$unlock_code."'");
			header("Location: /");
			exit;
		}
	}
	
	//IPs blockieren die mehr als 10 fehlerhafte Loginversuche hatten
	if (php_sapi_name() !== 'cli') {
		$result = $con->query("SELECT `id` FROM `mi_blocked` WHERE `blocked_ip` = '".$_SERVER['REMOTE_ADDR']."'");
		if ($result->num_rows > 0) {
			header('HTTP/1.1 403 Forbidden');
			echo 'Banned for 30 days. Check your eMails to unlock.';
			exit();
		}
		unset($result);
	}
	
//
///////////////////////////////////////////
?>