<?php

	
	function check_user_login() {
		//Prüfe Session
		if (isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['login_string'])) {
		global $con;
			//Check DB
			
			//
			return true;
		}
		return false;
	}	

	function check_register_conf($username, $register_conf_code) {
		//Prüfe neuen User
		global $con;
		$result = $con->query("SELECT `id` FROM `mi_users` WHERE `username` = '".$username."' AND `role` = 'new_miner' AND `goo_auth` = '".$register_conf_code."' AND `goo_active` = 1");
		if ($result->num_rows == 1) {
			//Neuen User gefunden
			$con->real_query("UPDATE `mi_users` SET `role` = 'normal_miner', 
													`goo_auth` = '".strtoupper($register_conf_code)."', 
													`goo_active` = 0,
													`last_login_date` = NOW(),
													`last_ip` = '".$_SERVER['REMOTE_ADDR']."'
											  WHERE `username` = '".$username."' AND 
											        `role` = 'new_miner' AND 
													`goo_auth` = '".$register_conf_code."'
							");
			return true;
		}
		return false;
	}
	
	function welcome_message($username) {
		//Willkommens Nachricht
		global $con;
		$result = $con->query("SELECT miu.language as userlanguage, 
									  mic.language as language,
									  mic.value
							  FROM `mi_config` AS mic 
							  LEFT JOIN `mi_users` as miu ON
									  miu.username = '".$username."'
							  WHERE   mic.option_name = 'register_welcome_message' AND 
									  (mic.language = miu.language || mic.language = 'EN');
							 ");
		if($result->num_rows == 1) {
			$message = mysqli_fetch_assoc($result);
			return  $message['value'];
		} else if ($result->num_rows > 1) {
			while($res = mysqli_fetch_assoc($result)) {
				if ($res['userlanguage'] == $res['language']) return $res['value'];	
			}	
		} else {
			return 'Welcome on Board,';
		}
	return '';
	}
	
	function curPageURL() {
		$pageURL = 'http';
		if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
	return $pageURL;
	}
?>