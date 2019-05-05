<?php
/***************************************************
				Miner-Control.de
			  Author: Sven Gцssling
			   Not for Public use!
***************************************************/
//Smarty
require_once ( __DIR__ . '/configs/mysql_data.php');
require_once ( __DIR__ . '/configs/session.class.php');
require_once ( __DIR__ . '/libs/Smarty.class.php');
require_once ( __DIR__ . '/configs/auth.php');
require_once ( __DIR__ . '/configs/auth_sess.php');
require_once ( __DIR__ . '/functions/mail.php');
require_once ( __DIR__ . '/functions/menu.php');
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

$smarty = new Smarty;
$smarty->template_dir = __DIR__ . '/templates/'; 
$smarty->compile_dir = __DIR__ . '/templates_c/';
$smarty->config_dir = __DIR__ . '/configs/';
$smarty->cache_dir = __DIR__ . '/cache/';
$smarty->caching = false;
//** Die folgende Zeile "einkommentieren" um die Debug-Konsole anzuzeigen
$smarty->debugging = false;

if (check_user_login()) {
	//Bereits angemeldet
	include_once(__DIR__ . '/functions/user.php');
	//Loading Classes
	$user = new online_user($_SESSION);
	$menu = new menu($user->getLanguage());
	$ga = new PHPGangsta_GoogleAuthenticator();
	//
	if (isset($_POST)) {
		foreach ($_POST as $post) {
			if(!preg_match("/[A-Za-z0-9ьдц№ƒ÷я _]+/", $post) == TRUE){
				ob_clean();
				echo 'Not allowed. Dont do this or you will banned!';
				exit;
			}
		}
		ob_clean();
		require_once(__DIR__ . '/functions/post_overview.php');
		
	}
	
	
	//GoogleAuth inaktive/aktive
	$secret = $ga->createSecret();
	if ($user->getGooActive() == true) $secret = $user->getGooAuth();
	$qrCodeUrl = $ga->getQRCodeGoogleUrl('Miner-Control.de-'.$user->getUsername(), $secret);
	//
	$list_all_active_miners = $user->getListActiveMiner();
	$active_miners = $complete_hashpower = 0;
	$server_time = time()-120;
	$count = 0;
	foreach ($list_all_active_miners as $active_miner) {
		$list_all_active_miners[$count]['minerJSON'] = json_decode($list_all_active_miners[$count][minerJSON], true);
		$miner_time = strtotime($active_miner['minerTime']);
		if (($miner_time - $server_time) > 0) {
			$active_miners++;
			$complete_hashpower += $active_miner['minerSpeed'];
		}
		$count++;
	}
	unset($count);
	
	$list_all_inactive_miners = $user->getListInactiveMiner();
	
	$overview_complete_chart_data_1min   = $user->getMainChart_complete_Speed_Average('1min');
	$overview_complete_chart_data_5min   = $user->getMainChart_complete_Speed_Average('5min');
	$overview_complete_chart_data_1hour  = $user->getMainChart_complete_Speed_Average('1h');
	$overview_complete_chart_data_1day   = $user->getMainChart_complete_Speed_Average('1day');
	$overview_complete_chart_data_1week  = $user->getMainChart_complete_Speed_Average('1week');
	$overview_complete_chart_data_1month = $user->getMainChart_complete_Speed_Average('1month');
	
	$overview_complete_chart_1min   = '';
	$overview_complete_chart_5min   = '';
	$overview_complete_chart_1hour  = '';
	$overview_complete_chart_1day   = '';
	$overview_complete_chart_1week  = '';
	$overview_complete_chart_1month = '';
	
	if ($overview_complete_chart_data_1min   != "") {$overview_complete_chart_1min   = "function renderChart_1min() { return Morris.Line({ element: 'Main_static_grid_1min',".$overview_complete_chart_data_1min."});}";}
	if ($overview_complete_chart_data_5min   != "") {$overview_complete_chart_5min   = "function renderChart_5min() { return Morris.Line({ element: 'Main_static_grid_5min',".$overview_complete_chart_data_5min."});}";}
	if ($overview_complete_chart_data_1hour  != "") {$overview_complete_chart_1hour  = "function renderChart_1hour() { return Morris.Line({ element: 'Main_static_grid_1hour',".$overview_complete_chart_data_1hour."});}";}
	if ($overview_complete_chart_data_1day   != "") {$overview_complete_chart_1day   = "function renderChart_1day() { return Morris.Line({ element: 'Main_static_grid_1day',".$overview_complete_chart_data_1day."});}";}
	if ($overview_complete_chart_data_1week  != "") {$overview_complete_chart_1week  = "function renderChart_1week() { return Morris.Line({ element: 'Main_static_grid_1week',".$overview_complete_chart_data_1week."});}";}
	if ($overview_complete_chart_data_1month != "") {$overview_complete_chart_1month = "function renderChart_1month() { return Morris.Line({ element: 'Main_static_grid_1month',".$overview_complete_chart_data_1month."});}";}
	
	$smarty->assign('overview_complete_chart_1min', $overview_complete_chart_1min);
	$smarty->assign('overview_complete_chart_5min', $overview_complete_chart_5min);
	$smarty->assign('overview_complete_chart_1hour', $overview_complete_chart_1hour);
	$smarty->assign('overview_complete_chart_1day', $overview_complete_chart_1day);
	$smarty->assign('overview_complete_chart_1week', $overview_complete_chart_1week);
	$smarty->assign('overview_complete_chart_1month', $overview_complete_chart_1month);
	
	//Global Stat
	$smarty->assign('CountAllMembers', $user->getAllMinerCount());
	$smarty->assign('AllActiveMinerCount', $user->getAllActiveMinerCount());
	foreach ($user->getGlobalStat('hash_speed') as $script_speed) {
		if (isset($script_speed['config_name']) && $script_speed['config_name'] != "")
		$smarty->assign(str_replace('-', '', strtolower($script_speed['config_name'])), $script_speed['config_value']);
	}
	
	$smarty->assign('top_pools', $user->getTopPools());
	$smarty->assign('top_miner_sha', $user->getTopMinerSHA());
	$smarty->assign('top_miner_scrypt', $user->getTopMinerSCRYPT());
	$smarty->assign('top_Contributors', $user->getTopContributor());
	//
	
	//Miner Spenden
	$smarty->assign('minerBenefit', $user->getMinerDonation());
	$smarty->assign('sha_donation_pool', $user->getSHAPool());
	$smarty->assign('sha_donation_user', $user->getSHAUser());
	$smarty->assign('scrypt_donation_pool', $user->getSCRYPTPool());
	$smarty->assign('scrypt_donation_user', $user->getSCRYPTUser());
	//
	
	$smarty->assign('complete_hashpower', $complete_hashpower);
	$smarty->assign('active_miners', $active_miners);
	$smarty->assign('count_all_miner', count($list_all_active_miners));
	$smarty->assign('list_all_active_miners', $list_all_active_miners);
	$smarty->assign('list_all_inactive_miners', $list_all_inactive_miners);
	$smarty->assign('user_language', $user->getLanguage());
	$smarty->assign('qrCodeUrl', $qrCodeUrl);
	$smarty->assign('secret_code', $secret);
	$smarty->assign('google_auth_status', $user->getGooActive());
	$smarty->assign('main_menu', $menu->getMenu());
	
	
	$smarty->display('overview.tpl');
		
} else {
	//Rausschmeissen
	header("refresh:0;url=/");
	exit;
}

?>