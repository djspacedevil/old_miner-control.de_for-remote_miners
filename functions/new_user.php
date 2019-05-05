<?php

class new_user {
	
	protected $protected_pw = '';
	private $salt = 'Gh&fvcDdäöih<DCT-Ic!@??4cddfq3';

	function check_new_entries($username, $password, $email) {
		//prüfen ob der User vorhanden ist und ob die Daten nutzbar sind
		
			if (!preg_match('/[^A-Za-z0-9]/', $username) && strlen($username) >= 4 &&
				//Disallowed Usernames
				strtolower($username) != "admin" &&
				strtolower($username) != "administrator" &&
				strtolower($username) != "operator" &&
				strtolower($username) != "control" &&
				strtolower($username) != "controll" &&
				strtolower($username) != "sven" &&
				strtolower($username) != "webmaster" &&
				strtolower($username) != "master" &&
				strtolower($username) != "miner-control" &&
				strtolower($username) != "miner_control" &&
				strtolower($username) != "miner-controll" &&
				strtolower($username) != "miner_controll" &&
				strtolower($username) != "miner-operator" &&
				strtolower($username) != "miner_operator" &&
				//
				$password != "" && strlen($password) >= 7 &&
				filter_var($email, FILTER_VALIDATE_EMAIL)
			) {
				global $con;
				$result = $con->query("SELECT `id` FROM `mi_users` WHERE (`username` = '".$username."' || `email` = '".$email."')");
				if ($result->num_rows == 0) {
					return true;
				}
			}
		return false;
	}
	
	function register_user($username, $password, $email) {
		//Benutzer registieren
		global $con;
		$protected_pw = hash('sha512', $username.$this->salt.$password);
		$conf_hash = substr(hash('md5', $username), 0, 10);
		$login = false;
		$lang = strtoupper(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
		if ($lang == "") $lang = 'EN';
		
		$result = $con->query("SELECT `id` FROM `mi_users` WHERE (`username` = '".$username."' || `email` = '".$email."')");
		if ($result->num_rows == 0) {
			
			$con->real_query("INSERT INTO mi_users(`role`,
												   `username`,
												   `password`,
												   `email`,
												   `language`,
												   `goo_auth`,
												   `goo_active`,
												   `last_ip`
												  ) VALUES (
												  'new_miner',
												  '".$username."',
												  '".$protected_pw."',
												  '".$email."',
												  '".$lang."',
												  '".$conf_hash."',
												  1,
												  '".$_SERVER['REMOTE_ADDR']."'
												  )");
			$result = $con->query("SELECT `id` FROM `mi_users` WHERE `username` = '".$username."' && `email` = '".$email."' && `password` = '".$protected_pw."'");
			if ($result->num_rows == 1) {
				if ($this->send_register_mail($username, $email, $lang, $conf_hash)) {
					return true;
				}
			}
		}

		return false;
	}
	
	function send_register_mail($username, $email, $lang, $conf_hash) {
		//RegisterMail versenden
		global $con;
		//
		$result = $con->query("SELECT * FROM `mi_config` WHERE (`option_name` = 'register_email_plain' || 
																`option_name` = 'register_email_html'  ||
																`option_name` = 'register_email_subject' ||
																`option_name` = 'host_email_norelay'  
																) && `language` = '".$lang."'");
		if($result->num_rows == 0) {
			unset($result);
			$result = $con->query("SELECT * FROM `mi_config` WHERE (`option_name` = 'register_email_plain' || 
																`option_name` = 'register_email_html'  ||
																`option_name` = 'register_email_subject' ||
																`option_name` = 'host_email_norelay'  
																) && `language` = 'EN'");
			if($result->num_rows == 0) { 
				return false;
			}
		}
		$register_email = array();
		while($res = mysqli_fetch_assoc($result)) {
			$register_email[$res['option_name']] = $res['value'];
		}
		//Replace User und Email
		$replace = array('%username%', '%email%', '%conf_code%');
		$with = array($username, $email, $conf_hash);
		
		$subject = $register_email['register_email_subject'];
		$register_email['register_email_plain'] = str_replace($replace, $with, $register_email['register_email_plain']);
		$register_email['register_email_html'] = str_replace($replace, $with, $register_email['register_email_html']);
		//
		
		require_once(__DIR__ .'/PHPMailerAutoload.php');
		$mail = new PHPMailer;
		
		$mail->From = ((isset($register_email['host_email_norelay']) && $register_email['host_email_norelay'] != "")?$register_email['host_email_norelay']:'norelay@miner-control.de');
		$mail->FromName = 'Miner-Control.de';
		$mail->addAddress($email);     // Add a recipient
		
		$mail->isHTML(true);                                  // Set email format to HTML

		$mail->Subject = $subject;
		$mail->Body    = "<html><body>".$register_email['register_email_html']."</body></html>";
		$mail->AltBody = $register_email['register_email_plain'];

		if($mail->send()) {
			return true;
		} 
		unset($result);
		//
		
		return false;
	}
}

?>