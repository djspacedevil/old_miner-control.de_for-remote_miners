<?php
/*	INPUT MINER DATAs
*/


if (isset($_POST['transactioncode']) && $_POST['transactioncode'] != "" &&
	isset($_POST['minertoken']) && $_POST['minertoken'] != "" &&
	isset($_POST['miner_infos']) && $_POST['miner_infos'] != "" &&
	isset($_POST['configfile']) && $_POST['configfile'] != "" &&
	count($_POST) == 4
	) {
		//Daten empfangen
		foreach ($_POST as $key => $value) {
			$_POST[$key] = addslashes($value);
		}
		
		$transactioncode 	= $_POST['transactioncode'];
		$minertoken 		= $_POST['minertoken'];
		$miner_info 		= $_POST['miner_infos'];
		$configfile			= $_POST['configfile'];
		
		require_once( __DIR__ . '/configs/mysql_data.php');
		require_once( __DIR__ . '/functions/class.miner.php');
		
		$miner = new miner($transactioncode, $minertoken, $miner_info, $configfile);
		if ( $miner->checkMiner() && $miner->decode_phpMiner() ) {
			//Daten sind JSON
			
			//Update Active Pool
			if($miner->setActivePool()) {
				$miner->updateMinerHistory();
			}
			$updates = $miner->getUpdates();
			if(!empty($updates)) {
				echo json_encode($updates);
			}
	
		}	else {
			//Daten wurden manipuliert
			//Count Manipulate with Blocking
			
			header('HTTP/1.0 400 Bad Request');
			exit;
		}

		
	} else {
		header('HTTP/1.0 404 Not Found');
		exit;
	}
	
?>