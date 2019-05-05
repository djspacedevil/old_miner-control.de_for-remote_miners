<html>
	<?xml version="1.0" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<!-- <meta http-equiv="refresh" content="5; url=http://miner-control.de:8000/" /> -->
		<meta name="author" content="Sven Goessling" />
		<meta name="copyright" content="Miner-Control.de" />
		<meta name="robots" content="index,follow" />
		<meta name="revisit-after" content="15 days" />
		<meta name="keywords" content="Miner, Bitcoin, Config, Einstellung, fernsteuern, Controlling, CGMiner, BFGMiner, Script, Mining" />
		<meta name="description" content="Mit dieser Online Seite ist es dir m&ouml;glich, deinen Miner zuhause &uuml;ber das Internet fernzusteuern" />
		<title>Miner-Control.de Control your Miners around the World.</title>
		<link type="text/css" media="all" rel="stylesheet" href="/css/overview.css" />
		<link type="text/css" media="all" rel="stylesheet" href="/css/jquery.range.css" />
		<link type="text/css" media="all" rel="stylesheet" href="/css/toggles-full.css" />
		<!-- <link type="text/css" media="all" rel="stylesheet" href="/css/morris.css"> -->
		
		<script language="javascript" type="text/javascript" src="/js/jquery-2.1.1.min.js"></script>
		<script language="javascript" type="text/javascript" src="/js/date.format.js"></script>
		<script language="javascript" type="text/javascript" src="/js/toggles.min.js"></script>
		<script language="javascript" type="text/javascript" src="/js/d3.js"></script>
		<script language="javascript" type="text/javascript" src="/js/nv.d3.js"></script>
		<script language="javascript" type="text/javascript" src="/js/jquery.range.js"></script>
		<script language="javascript" type="text/javascript" src="/js/overview.js"></script>
	</head>
	<body>
	
	<div id="left_side">
		<div id="logo">&nbsp;</div>
		<div id="menu_entries">
			{foreach key=id_entry item=entry from=$main_menu}
				<div id="{$id_entry}">{$entry}</div>
			{/foreach}
		</div>
		
		<div id="own_risk">
				Use at your own risk.<br>We are not liable for many losses.
			</div>
		
		<div id="menu_footer">
		 
		<div class="toggle-modern toggle" data-toggle-on="{if isset($google_auth_status) && $google_auth_status == true}true{else}false{/if}" data-toggle-height="20" data-toggle-width="70"></div>
			 <a href="https://support.google.com/accounts/answer/1066447?hl={$user_language}" target="_blank" title="Google Authenticator Help">Google Auth Code</a><br>
			 <a href="{$qrCodeUrl}" class="preview" target="_blank">
				<img src="{$qrCodeUrl}">
			</a><br>
			<span id="google_code">{$secret_code}</span>
		</div>
	</div>
	<div id="Main_frame">
	<div id="Main_welcome">
			<div class="head">Miner-Control.de</div>
	</div>
	{*Globale Übersicht*}
		{include file="global_overview.tpl"}
	{*Miner Übersicht*}
		{include file="miner_overview.tpl"}
	{*Erste Schritte und FAQ*}
		{include file="first_steps_faq.tpl"}
	</div>
	<div id="Logout">&nbsp;</div>
	</body>
</html>