<?xml version="1.0" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta name="author" content="Sven Goessling" />
		<meta name="copyright" content="Miner-Control.de" />
		<meta name="robots" content="index,follow" />
		<meta name="revisit-after" content="15 days" />
		<meta name="keywords" content="Miner, Bitcoin, Config, Einstellung, fernsteuern, Controlling, CGMiner, BFGMiner, Script, Mining" />
		<meta name="description" content="Mit dieser Online Seite ist es dir m&ouml;glich, deinen Miner zuhause &uuml;ber das Internet fernzusteuern" />
		<title>Miner-Control.de Control your Miners around the World.</title>
		<link type="text/css" media="all" rel="stylesheet" href="/css/home.css" />
		<script language="javascript" type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
		<script language="javascript" type="text/javascript" src="js/start.js"></script>
	</head>
	<body>
	<div id="Main_frame">
		<div id="Main_login" {if $show_joinform == "true"} style="display:none !important;" {/if}>
			<div class="head">Login Miner-Control.de</div>
			<div class="body_login failed"><b>{$login_failed}</b></div>
			<div class="body_login">
				<form action="#" method="post">
				<div id="login_box"> 
					<div class="input_label user"> 
						<label for="name"> 
							User
						</label>
					</div> 
					<input name="user" type="text" id="name" class="name" size="30" value=""> 
				</div>
	
				<div id="login_box"> 
					<div class="input_label user"> 
						<label for="name"> 
							Password
						</label>
					</div> 
					<input name="passwd" type="password" id="name" class="name" size="30" value=""> 
				</div>
				
				<div id="login_box"> 
					<div class="input_label user"> 
						<label for="name"> 
							Auth-Code
						</label>
					</div> 
					<input name="auth" type="text" id="name" class="name" size="30" value=""> 
				</div>
				
				<br>
				<input class="loginbutton" name="submit_login" type="submit" value="Login" />
				</form>
				
				<input class="joinbutton" name="submit_join" type="submit" value="JOIN THE BETA" />
			</div>
		</div>
		<div id="Join_form" {if $show_joinform == "true"} style="display:block !important;" {/if}>
			<div class="head">Join Miner-Control.de <input class="join_close" name="join_close" type="submit" value="X" /></div>
			
			<div class="body_captcha failed"><b>{$captcha_failed}</b></div>
			<div class="body_captcha">
				<form action="#" method="post">
				<div id="login_box"> 
					<div class="input_label user"> 
						<label for="name"> 
							User(4)
						</label>
					</div> 
					<input name="join_user" type="text" id="name" class="name" size="30" value=""> 
				</div>
	
				<div id="login_box"> 
					<div class="input_label user"> 
						<label for="name"> 
							Password(7)
						</label>
					</div> 
					<input name="join_passwd" type="password" id="name" class="name" size="30" value=""> 
				</div>
				
				<div id="login_box"> 
					<div class="input_label user"> 
						<label for="name"> 
							eMail
						</label>
					</div> 
					<input name="join_email" type="text" id="name" class="name" size="30" value=""> 
				<br><br>
				</div>
				<img id="captcha" src="/secure/securimage_show.php" alt="CAPTCHA Image" />
				<br>
				<input type="text" name="captcha_code" size="10" maxlength="6" />
				<a href="#" onclick="document.getElementById('captcha').src = '/secure/securimage_show.php?' + Math.random(); return false">[ Different Image ]</a>
				<br>
				<input class="loginbutton" name="submit_join" type="submit" value="Join" />
				</form>
				
			</div>
			
		</div>
		
		<div id="mining_control">
				<img src="images/Miner_Control.png" />
		</div>
		
		<div id="mining_pool">
			<a href="http://miner-control.de:8000/" alt="Direct Mining Pool" title="Mining without registration">
				<img src="images/Mining_Pool.png" />
			</a>
		</div>
		<div id="clear">&nbsp;</div>
		<div id="push_control">
			You want to push the Site forward? Donate &amp; send your  Feature Comment ;)<br>
			<br>Please Donate To Bitcoin Address: <br>
			 <b><a href="https://blockchain.info/address/13CRQ7DeRgTKbJpByhy1P99P3TyDhAxrnT" target="_blank">13CRQ7DeRgTKbJpByhy1P99P3TyDhAxrnT</a></b><br>
			 <img src="images/donate_address.png" width="150px">
		</div>
		
	</div>
	
	</body>
</html>
