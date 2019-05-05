<?php
/***************************************************
				Miner-Control.de
			  Author: Sven Gössling
			   Not for Public use!
***************************************************/

	//Logout
	if((isset($_POST['logout']) && $_POST['logout'] == "exit") || (isset($_GET['logout']) && $_GET['logout'] == "exit")) {
		ob_clean();
		session_destroy();
		unset($_SESSION);
		exit;
	}
	//
	
	//Refresh Global Overview
	if (isset($_POST['refresh_global_overview']) && $_POST['refresh_global_overview'] != "" &&
	    isset($_POST['global']) && $_POST['global'] != "") {
			//Globale Infos
			$count = 0;
			foreach ($user->getGlobalStat('hash_speed') as $script_speed) {
				if (isset($script_speed['config_name']) && $script_speed['config_name'] != "t") {
					$script['hashspeed'][$count]['config_name'] = str_replace('-', '', strtolower($script_speed['config_name']));
					$script['hashspeed'][$count]['config_value'] = $script_speed['config_value'];
					$count++;
				}
			}
			$script['AllUser'] = $user->getAllMinerCount();
			$script['ActiveUser'] = $user->getAllActiveMinerCount();
			
			//User Infos
			$script['AllActiveMiners'] = $user->getListActiveMiner();
			
			ob_clean();
			echo json_encode($script);
			exit;
		}
	//
	
	//GoogleAuth setzen
	if(isset($_POST['set_Auth_Code']) && $_POST['set_Auth_Code'] != "" &&
	   isset($_POST['new_Auth_Code']) && $_POST['new_Auth_Code'] != "") {
		if ($_POST['set_Auth_Code'] == "activate") {
			if (!$user->setAuthCode(addslashes($_POST['new_Auth_Code']))) {
				ob_clean();
				echo 'Error: Can`t set Auth Code./n/nPlease retry it later.';
				exit;
			} else {
				ob_clean();
				exit;
			}
		} else if ($_POST['set_Auth_Code'] == "deactivate") {
			if (!$user->delAuthCode(addslashes($_POST['new_Auth_Code']))) {
				ob_clean();
				echo 'Error: Can`t remove Auth Code./n/nPlease retry it later.';
				exit;
			} else {
				ob_clean();
				exit;
			}
		}	
	} 
	//
	
	//Create new Miner
	if(isset($_POST['new_miner']) && $_POST['new_miner'] == "create") {
		ob_clean();
		echo $user->create_NewMiner();
		exit;
	}
	//
	
	//Delete Miner
	if(isset($_POST['delMiner']) && $_POST['delMiner'] == "miner" &&
	   isset($_POST['minerID']) && $_POST['minerID'] != "" &&
	   isset($_POST['TransactionHash']) && $_POST['TransactionHash'] != "" &&
	   isset($_POST['minerToken']) && $_POST['minerToken'] != ""
	) {
		if($user->delete_Miner($_POST['minerID'], $_POST['TransactionHash'], $_POST['minerToken'])) {
			ob_clean();
			exit;
		} else {
			ob_clean();
			echo 'Error: Cannot delete Miner from DB.';
			exit;
		}
	}
	//
	
	//Set Miner Donation
	if (isset($_POST['setDonation']) 	&& $_POST['setDonation'] == 'true' &&
		isset($_POST['value']) 			&& is_numeric($_POST['value'])) {
		
		if ($user->setMinerDonation((int)$_POST['value'])) {
			ob_clean();
			echo 'true';
			exit;
		} else {
			ob_clean();
			echo 'Warning! Not allowed! SET YOU to 100%';
			exit;
		}
	}
	//
	
	//Set Miner Name
	if (isset($_POST['editMinerName']) 	&& $_POST['editMinerName'] == "true" &&
		isset($_POST['minerID']) 		&& $_POST['minerID'] != "" &&
		isset($_POST['newMinerName']) 	&& $_POST['newMinerName'] != "") {
		if ($user->setMinerName($_POST['newMinerName'], (int)$_POST['minerID']) != "") {
			ob_clean();
			exit;
		} else {
			ob_clean();
			echo 'false';
			exit;
		}		
	}	
	//
	
	//Switch Pool
	if (isset($_POST['switchPool']) && $_POST['switchPool'] == "true" &&
		isset($_POST['newPoolID']) && $_POST['newPoolID'] != "") {
			
		if ($user->setSwitchPool((int)$_POST['newPoolID'])) {
			ob_clean();
			exit;
		} else {
			ob_clean();
			echo 'false';
			exit;
		}
	}
	//
	
	//Edit ConfigFile
	if(isset($_POST['editMinerConfig']) && $_POST['editMinerConfig'] == "true" &&
	   isset($_POST['minerID']) 		&& $_POST['minerID'] != "" &&
	   isset($_POST['MinerConfig']) 	&& $_POST['MinerConfig'] != "") {
		if($user->setConfigfile($_POST['MinerConfig'], $_POST['minerID'])) {
			ob_clean();
			exit;
		} else {
			ob_clean();
			echo 'false';
			exit;
		}
	
	}
	//
	
	//Neuer Pool hinzufügen
	if(	isset($_POST['newMinerPool']) 	&& $_POST['newMinerPool'] 	== "true" &&
		isset($_POST['MinerID']) 		&& $_POST['MinerID'] 		!= "" &&
		isset($_POST['Pool']) 			&& $_POST['Pool'] 			!= "" &&
		isset($_POST['User']) 			&& $_POST['User'] 			!= "" &&
		isset($_POST['Password']) 		&& $_POST['Password'] 		!= "") {
		   if($user->setNewPool((int)$_POST['MinerID'], $_POST['Pool'], $_POST['User'], $_POST['Password'])) {
			ob_clean();
			exit;
		} else {
			ob_clean();
			echo 'false';
			exit;
		}
	   }
	//
	
	//Delete MinerPool
	if (isset($_POST['delMinerPool']) 	&& $_POST['delMinerPool'] == "true" &&
	isset($_POST['minerID']) 			&& $_POST['minerID'] != "" &&
	isset($_POST['Pool']) 				&& $_POST['Pool'] != "") {
		if($user->deletePool($_POST['minerID'], $_POST['Pool'])) {
			ob_clean();
			exit;
		} else {
			ob_clean();
			echo 'false';
			exit;
		}
	}
	//
?>