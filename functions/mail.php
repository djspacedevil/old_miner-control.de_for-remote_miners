<?php

//send Faillogin Mail
	function send_alertmail($username, $user_mail , $lang , $ip, $unblock_code) {
	
	if ($lang == 'DE') {
		$Betreff = 'Jemand versucht sich mit Ihrem Login einzuloggen';
		$nachricht = 'Hallo '.$username.',<br>
		<br>
		Jemand versucht auf Ihr Konto auf Miner-Control.de zuzugreifen.<br>
		Sollten Sie nicht der jenige sein, &auml;ndern Sie aus Sicherheitsgr&uuml;nden umgehend ihr Passwort und/oder aktiveren Sie die 2-Faktoren Autorisation.<br>
		Die IP Adresse wird für die n&auml;chsten 48 Stunden für den Login gesperrt.<br>
		<br>
		Benutzername : '.$username.'<br>
		IP-Adresse: '.$ip.'<br>
		Entsperren : https://miner-control.de/unlock.php?ip='.$ip.'&code='.$unblock_code.'<br>
		<br>
		Solltest du dein Passwort vergessen haben, nutze bitte die entsprechende Funktion.<br>
		<br>
		Dein Miner-Controler Admin<br>
		<br>
		(Automatisch generierte Email)
		';
	} else {
		$Betreff = 'Someone tries to log on with your login';
		$nachricht = 'Hello '.$username.',
		Someone is trying to access your account on Miner-Control.de<br>
		Should not you be the one, change your password immediately for security and / or more active the 2-factor authorization.<br>
		The IP-Address is blocked for the next 48 Hours.<br>
		<br>
		Username: '.$username.'<br> 
		IP-Address: '.$ip.'<br>
		Unblock: https://miner-control.de/unlock.php?ip='.$ip.'&code='.$unblock_code.'<br>
		<br>
		Please enter the appropriate function If you have forgotten your password.<br>
		<br>
		Your Miner-Controler Admin<br>
		<br>
		(Automatically generated email)
		';
	}
	
	
	$header  = 'MIME-Version: 1.0' . "\r\n";
	$header .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	
	$header .= 'To: '.$username.' <'.$user_mail.'>' . "\r\n";
	$header .= 'From: Miner-Control Center <info@miner-control.de>' . "\r\n";
	
	mail($user_mail, $Betreff, $nachricht, $header);
	
	}

?>