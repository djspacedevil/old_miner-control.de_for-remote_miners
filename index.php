<?php
/***************************************************
				Miner-Control.de
			  Author: Sven Gössling
			   Not for Public use!
***************************************************/
error_reporting( E_ALL );

//Smarty
require_once ( __DIR__ . '/configs/mysql_data.php');
require_once ( __DIR__ . '/configs/session.class.php');
require_once ( __DIR__ . '/libs/Smarty.class.php');
require_once ( __DIR__ . '/configs/auth.php');
require_once ( __DIR__ . '/configs/auth_sess.php');
require_once ( __DIR__ . '/functions/mail.php');

//Captcha
include_once (__DIR__ . '/secure/securimage.php');
$securimage = new Securimage();
//
$session = new session();
//Check if SSL and Start Session
if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) {
	$session->start_session('GoesiMinerControlID', true);
} else {
	$session->start_session('GoesiMinerControlID', false);
}
//
/*if (isset($_GET['debug']) && $_GET['debug'] == 'mayo') {
	ini_set('display_errors', 1);
	print_r($_SESSION);
}
*/
$smarty = new Smarty;

if(!file_exists(__DIR__ . '/templates/')) { mkdir(__DIR__ . '/templates/', 0755); }
if(!file_exists(__DIR__ . '/templates_c/')) { mkdir(__DIR__ . '/templates_c/', 0755); }
if(!file_exists(__DIR__ . '/configs/')) { mkdir(__DIR__ . '/configs/', 0755); }
if(!file_exists(__DIR__ . '/cache/')) { mkdir(__DIR__ . '/cache/', 0777); }
$smarty->template_dir = __DIR__ . '/templates/'; 
$smarty->compile_dir = __DIR__ . '/templates_c/';
$smarty->config_dir = __DIR__ . '/configs/';
$smarty->cache_dir = __DIR__ . '/cache/';
$smarty->caching = false;
//
$smarty->assign('login_failed','');
$smarty->assign('captcha_failed', '');
$smarty->assign('show_joinform', 'false');

//** Die folgende Zeile "einkommentieren" um die Debug-Konsole anzuzeigen
$smarty->debugging = false;



//Ausbrechen verhindern
if(isset($_POST)) {
	foreach ($_POST as $key=>$value) {
		$_POST[$key] = addslashes($value);
	}
}

if(isset($_GET['user']) && $_GET['user'] != '' &&
   isset($_GET['register_conf']) && $_GET['register_conf'] != '' ) {
	if (check_register_conf($_GET['user'], $_GET['register_conf'])) {
		$smarty->assign('welcome_message', welcome_message($_GET['user']));
		$smarty->assign('welcome_user', $_GET['user']);
		$smarty->assign('return_url', curPageURL());
		$smarty->display('welcome.tpl');
		exit;
	}
}

if (check_user_login()) {
	//Bereits angemeldet
	header("Location: overview.php");
	session_regenerate_id(true);
	exit;
		
} else if (isset($_POST['user']) && $_POST['user'] != '' &&
	isset($_POST['passwd']) && $_POST['passwd'] != '') {
	
	//User versucht sich einzuloggen
	include_once(__DIR__ . '/functions/user.php');
	$user = new user();
	if(isset($_POST['auth']) && $_POST['auth'] != '') {
		//Google Auth nutzen
		$oneAuthCode = $_POST['auth'];
	} else {
		$oneAuthCode = '0';
	}
	
	if ($user->login_user($_POST['user'], $_POST['passwd'], $oneAuthCode)) {
		if (check_user_login()) {
			header("Location: /overview.php");
			exit;
		}
	
	} else {
	//Gast
	$smarty->assign('login_failed','Failure to login');
	$smarty->display('index.tpl');
	}

	//if(isset($_POST['auth']) && $_POST['auth'] != '') {
	//	//Google Auth nutzen
	//	$Auth = new PHPGangsta_GoogleAuthenticator();
	//	$oneAuthCode = $_POST['auth'];
	//	
	//	$checkResult = $ga->verifyCode($secret, $oneAuthCode, 2); // 2 = 2*30sec clock tolerance
	//	
	//}
	
	
	
} else {
	//Gast // Neuer Member
	if(isset($_POST['join_user']) && $_POST['join_user'] != "" &&
	   isset($_POST['join_passwd']) && $_POST['join_passwd'] != "" &&
	   isset($_POST['join_email']) && $_POST['join_email']) {
	   //Neuer Member
	   include_once(__DIR__ . '/functions/new_user.php');
	   $new_user = new new_user();
	   
	   $smarty->assign('show_joinform', 'true');	
		if ($securimage->check($_POST['captcha_code']) == false ||
			$new_user->check_new_entries($_POST['join_user'], $_POST['join_passwd'], $_POST['join_email']) == false) {
			$smarty->assign('captcha_failed', 'Invalid Captcha, Username or Email');
		} else {
			if ($new_user->register_user($_POST['join_user'], $_POST['join_passwd'], $_POST['join_email'])) {
				$smarty->assign('captcha_failed', 'eMail was send. Check your Mails');	
			} else {
				$smarty->assign('captcha_failed', 'eMail send failed.');	
			}
		}
	} 
	$smarty->display('index.tpl');
}


//$secret = $ga->createSecret();
//$secret = 'PJY3AELZGWLTB2DW';
//echo "Secret is: ".$secret."\n\n";

//$qrCodeUrl = $ga->getQRCodeGoogleUrl('Miner-Control.de-DJSpAcEDeViL', $secret);

//echo 'Google Charts URL for the QR-Code: <img src="'.$qrCodeUrl.'">\n\n';
//$oneCode = $ga->getCode($secret);
//if(isset($_POST['auth']) && $_POST['auth'] != '') $oneCode = $_POST['auth'];
//echo "Checking Code '$oneCode' and Secret '$secret':\n";
//$checkResult = $ga->verifyCode($secret, $oneCode, 2); // 2 = 2*30sec clock tolerance



//////////////////////
//SQL Schliessen
//$con->close();
//
//////////////////////
?>