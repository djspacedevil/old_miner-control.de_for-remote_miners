<?php

class miner {

	protected $_TransactionHash;
	protected $_MinerTokenHash;
	protected $_JSON_miner_info;
	protected $_configfile;
	protected $_security_encode = 'iqfWvXPa66ytDokyiDCgekOXepxzUKtou0OIN3qVd0SCk46Xwa4qiJdr40pKmQZeDNpW5rPXBDyvOPCWGKQWY9bG1bls2nQCRF4BAr5V6VZtv0gG1m4ANRfwQ5NvgzIbefTpJOZJuXVI4b99J6KBsZIKbwwwx5WemXfse9BVYErxNPuobMZZL0MzlKQcFavxpjcIflrSxFq9337cdfsUSLZkoSJmmp08EkSNQ9bXCES8sKX82h22TgyLAWapmjufS1iCvW8iAzxmoMiCaFF8UtuKH4CGykvSPhWesLvqQbK4';
	protected $_decoded_miner_info;
	protected $db;
	
	protected $_newConfigfile;
	protected $_newActivePoolID;
	
	private $miner_id;
	private $sha_pool;
	private $sha_user;
	private $sha_password;
	private $scrypt_pool;
	private $scrypt_user;
	private $scrypt_password;

	//Contruction
	function __construct($transactioncode, $minertoken, $miner_info, $configfile) {
		global $con;
		$this->db = $con;
		
		$this->_TransactionHash = $transactioncode;
		$this->_MinerTokenHash 	= $minertoken;
		$this->_JSON_miner_info = $miner_info;
		$this->_configfile 		= $configfile;
		
		$this->_newConfigfile = '';
		$this->_newActivePoolID = '';
		
		$this->setMasterPools();
	}
	
	//Zugängliche Functionen
	public function checkMiner() {
		
		$result = $this->db->query("SELECT `id` FROM `mi_miner` WHERE `transactionHash` = '".$this->_TransactionHash."' AND `minerToken` = '".$this->_MinerTokenHash."'");
		if ($result->num_rows == 1) {
			$res = mysqli_fetch_row($result);
			$this->miner_id = $res['0'];
			return true;
		}
		return false;
	}

	public function decode_phpMiner() {
		$decoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($this->_security_encode), base64_decode($this->_JSON_miner_info), MCRYPT_MODE_CBC, md5(md5($this->_security_encode))), "\0");
		if ($this->isJSON($decoded)) {
			$this->_decoded_miner_info = json_decode($decoded, true);
			return true;
		}
		return false;
	}
	
	public function getDecodeMinerInfo() {
		return $this->_decoded_miner_info;
	}
	
	public function setActivePool() {

		if(isset($this->_decoded_miner_info['MINERNAME'])) $minerName = $this->_decoded_miner_info['MINERNAME'];
		if(isset($this->_decoded_miner_info['SUMMERY']['SUMMARY']['0']['MHS 1m'])) $minerSpeed = $this->_decoded_miner_info['SUMMERY']['SUMMARY']['0']['MHS 1m'];
		
		foreach ($this->_decoded_miner_info['POOLS'] as $pool) {
			if ($pool['Priority'] == 0) {
				$active_pool = str_replace('stratum+tcp://', '', $pool['URL']);
				$active_pool_user = $pool['User'];
				$active_pool_id = $pool['POOL'];
			}
		}
		
		$result = $this->db->query("SELECT 	mi_miner.minerConfig,
											mi_miner.minerTime, 
											mi_miner.minerPoolID,
											mi_users.site_benefit_procent,
											mi_users.round_time_24h,
											mi_users.benefit_time,
											mi_users.pool_before_benefit		
									FROM 	`mi_miner`,
											`mi_users`
									WHERE 	mi_miner.transactionHash = '".$this->_TransactionHash."' AND 
											mi_miner.minerToken = '".$this->_MinerTokenHash."' AND
											mi_miner.transactionHash = mi_users.transaction_hash;
									");
		if ($result->num_rows == 1) {							
			$res = mysqli_fetch_assoc($result);
			//Config wurde geändert
			if ($res['minerConfig'] != stripslashes($this->_configfile)) {
				$this->_newConfigfile = $res['minerConfig'];
				$this->_configfile = $res['minerConfig'];
			}
			
			//Pool Switch bekommen
			if ((int)$res['minerPoolID'] != (int)$active_pool_id) {
				$this->_newActivePoolID = $res['minerPoolID'];
				$active_pool_id = $res['minerPoolID'];
			}
			
			//Kommt von Donation zurück
			if ($res['round_time_24h'] > ($res['site_benefit_procent'] * $res['benefit_time'])) {
				if ($res['pool_before_benefit'] != $active_pool_id) {
				$this->_newActivePoolID = $res['pool_before_benefit'];
				$active_pool_id = $res['pool_before_benefit'];
				}
			}
			
		
			//Zeit differenz abziehen
			$date = date_create();
			$now = date_timestamp_get($date);
			$miner_lastseen_time = strtotime($res['minerTime']);
			$reduce_time = $now - $miner_lastseen_time;
			if ($reduce_time < 0) $reduce_time = $reduce_time*-1;
			//
		
			if (isset($minerSpeed) && isset($this->_decoded_miner_info) && isset($active_pool)) {
				$this->db->real_query("UPDATE `mi_miner` SET ".((isset($minerName))?"`minerName` = '".$minerName."',":"")."
														`minerSpeed` = '".$minerSpeed."',
														`minerPoolID` = '".$active_pool_id."',														
														`minerPool` = '".$active_pool."', 
														`minerConfig` = '".$this->_configfile."',
														`minerJSON` = '".json_encode($this->_decoded_miner_info)."',
														`minerScrypt` = '".$this->_decoded_miner_info['COIN']['0']['Hash Method']."',
														`minerTime` = NOW() 
												 WHERE `transactionHash` = '".$this->_TransactionHash."' AND 
												       `minerToken` = '".$this->_MinerTokenHash."'
								");
								
				//Spenden Zeit abziehen				
				$this->db->real_query("UPDATE `mi_users` SET `round_time_24h` = round_time_24h - ".$reduce_time." WHERE `transaction_hash` = '".$this->_TransactionHash."';");
				if (($active_pool != $this->sha_pool && $active_pool_user != $this->sha_user.'_'.$this->miner_id) ||
					($active_pool != $this->scrypt_pool && $active_pool_user != $this->scrypt_user)) {
					$this->db->real_query("UPDATE `mi_users` SET `pool_before_benefit` = ".$active_pool_id." WHERE `transaction_hash` = '".$this->_TransactionHash."';");
				}
				
				if (($this->_decoded_miner_info['COIN']['0']['Hash Method'] == 'sha256' && $active_pool == $this->sha_pool && $active_pool_user == $this->sha_user.'_'.$this->miner_id) ||
					($this->_decoded_miner_info['COIN']['0']['Hash Method'] == 'scrypt' && $active_pool == $this->scrypt_pool && $active_pool_user == $this->scrypt_user)) {
					//SHAspende läuft
					
					$this->db->real_query("UPDATE `mi_users` SET 	`round_benefit_time` = round_benefit_time + ".$reduce_time.", 
																	`complete_benefit_time` = complete_benefit_time + ".$reduce_time."
															 WHERE 	`transaction_hash` = '".$this->_TransactionHash."';");
															 
				}
				//
				
				return true;
			}
		}						
		return false;
	}
	
	public function getUpdates() {
		$array = array();
		$result = $this->db->query("SELECT 	mi_users.site_benefit_procent,
											mi_users.round_time_24h, 
											mi_users.benefit_time,
											mi_miner.minerDeletePools,
											mi_miner.minerNewPools
									FROM 	`mi_users`,
									        `mi_miner`
									WHERE 	mi_users.transaction_hash = '".$this->_TransactionHash."' AND
											mi_miner.minerToken = '".$this->_MinerTokenHash."';");
									
		$res = mysqli_fetch_assoc($result);
		
		if (($res['round_time_24h'] - ($res['site_benefit_procent']*$res['benefit_time'])) <= ($res['site_benefit_procent']*$res['benefit_time'])) {
			//Miner befindet sich in der Spendenzeit
			if ($this->_decoded_miner_info['COIN']['0']['Hash Method'] == 'sha256') {
				$array['DONATETIME']['pool'] = $this->sha_pool;
				$array['DONATETIME']['user'] = $this->sha_user.'_'.$this->miner_id;
				$array['DONATETIME']['password'] = $this->sha_password;
			} else {
				$array['DONATETIME']['pool'] = $this->scrypt_pool;
				$array['DONATETIME']['user'] = $this->scrypt_user;
				$array['DONATETIME']['password'] = $this->scrypt_password;
			}
		} else {
			if (isset($this->_newActivePoolID) && $this->_newActivePoolID != "") {
				$array['SWITCH POOL'] = $this->_newActivePoolID;
			}
		}
		
		if (isset($this->_newConfigfile) && $this->_newConfigfile != "") {
			$array['NEWCONFIGFILE'] = stripslashes($this->_newConfigfile);
		}
		
		if (isset($res['minerDeletePools']) && $res['minerDeletePools'] != "") {
			$array['DELETEPOOLS'] = $res['minerDeletePools'];
			$this->db->real_query("UPDATE `mi_miner` SET `minerDeletePools` = '' WHERE `id` = ".$this->miner_id.";");
		}
		if (isset($res['minerNewPools']) && $res['minerNewPools'] != "") {
			$array['NEWPOOLS'] = $res['minerNewPools'];
			$this->db->real_query("UPDATE `mi_miner` SET `minerNewPools` = '' WHERE `id` = ".$this->miner_id.";");
		}
		
		
		return $array;
		
	}

	public function updateMinerHistory() {
		//Cleanup 1min Dust
		$result_history = $this->db->query("SELECT `TotalminerShares` FROM `mi_miner_history` WHERE 
																							`transactionHash` = '".$this->_TransactionHash."' AND 
																							`minerToken` = '".$this->_MinerTokenHash."' AND
																							`timePeriode` = '1min' AND 
																							`timestamp` >= NOW() - INTERVAL 90 SECOND ORDER BY `id` DESC LIMIT 1");
		if ($result_history->num_rows == 1) {
			unset($res_history);
			$res_history = mysqli_fetch_assoc($result_history);
			$now_accepted = ($this->_decoded_miner_info['SHARES']['Accepted'] - $res_history['TotalminerShares']);
		}
		
		$this->db->real_query("INSERT INTO `mi_miner_history` (`transactionHash`,
																  `minerToken`,
																  `timePeriode`,
																  `minerSpeed`,
																  `minerShares`,
																  `TotalminerShares`,
																  `timestamp`
																  ) VALUES (
																  '".$this->_TransactionHash."',
																  '".$this->_MinerTokenHash."',
																  '1min',
																  '".$this->_decoded_miner_info['SUMMERY']['SUMMARY']['0']['MHS 1m']."',
																  '".((isset($now_accepted))?$now_accepted:$this->_decoded_miner_info['SHARES']['Accepted'])."',
																  '".$this->_decoded_miner_info['SHARES']['Accepted']."',
																  NOW()
																	)");
		$this->db->real_query("DELETE FROM `mi_miner_history` WHERE 
																		`transactionHash` = '".$this->_TransactionHash."' AND 
																		`minerToken` = '".$this->_MinerTokenHash."' AND
																		`timePeriode` = '1min' AND 
																		`timestamp` < NOW() - INTERVAL 300 SECOND");
																		
		//Update 5 min
		$this->update_history('1min', '5min', 5);
		//Update 1 Hour
		$this->update_history('5min', '1h', 60);
		//Update 1 Day
		$this->update_history('1h', '1day', 1440);
		//Update 1 Week
		$this->update_history('1day', '1week', 10080);
		//Update 1 Month
		$this->update_history('1week', '1month', 40320);
		
		
		
		
		
	}
	
	//
	
	//Private Funktionen
	private function setMasterPools() {
		$result = $this->db->query("SELECT 	`config`, 
											`config_name` 
									FROM 	`mi_global_stats` 
									WHERE 	`config` = 'SHA-256_master_user' OR 
											`config` = 'SHA-256_master_password' OR 
											`config` = 'SCRYPT_master_user' OR 
											`config` = 'SCRYPT_master_password' OR
											`config` = 'SHA-256_master_pool' OR
											`config` = 'SCRYPT_master_pool';");
		while ($res = mysqli_fetch_assoc($result)) {
			if ($res['config'] == 'SHA-256_master_password') {
				$this->sha_password = $res['config_name'];
			}
			if ($res['config'] == 'SHA-256_master_user') {
				$this->sha_user = $res['config_name'];
			}
			if ($res['config'] == 'SCRYPT_master_password') {
				$this->scrypt_password = $res['config_name'];
			}
			if ($res['config'] == 'SCRYPT_master_user') {
				$this->scrypt_user = $res['config_name'];
			}
			if ($res['config'] == 'SHA-256_master_pool') {
				$this->sha_pool = $res['config_name'];
			}
			if ($res['config'] == 'SCRYPT_master_pool') {
				$this->scrypt_pool = $res['config_name'];
			}
		}
	}
	
	private function isJSON($string){
		return is_string($string) && is_object(json_decode($string)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
	}
	
	private function update_history($oldtimePeriod, $newtimePeriod, $time) {
		$transactioncode = $this->_TransactionHash;
		$minertoken = $this->_MinerTokenHash;
		$miner_data = $this->_decoded_miner_info;
		
		$result_count_time = $this->db->query("SELECT `timestamp` FROM `mi_miner_history` WHERE 
																								 `transactionHash` = '".$transactioncode."' AND 
																								  `minerToken` = '".$minertoken."' AND
																								  `timePeriode` = '".$newtimePeriod."' AND 
																								  `timestamp` >= NOW() - INTERVAL ".$time." MINUTE");
		if ($result_count_time->num_rows == 0) { 																						  
		$result_history = $this->db->query("SELECT `id`,`minerSpeed`,`minerShares` FROM `mi_miner_history` WHERE 
																								 `transactionHash` = '".$transactioncode."' AND 
																								  `minerToken` = '".$minertoken."' AND
																								  `timePeriode` = '".$oldtimePeriod."' AND 
																								  `timestamp` >= NOW() - INTERVAL ".$time." MINUTE");
			if ($result_history->num_rows > 0) {
				$minerShares = 0;
				$average_count = 0;
				$average_speed = 0;
				$minerSpeed = 0;
				$del_old = array();
					
				if ($newtimePeriod == '1min') $maxload = 1;
				if ($newtimePeriod == '5min') $maxload = 5;
				if ($newtimePeriod == '15min') $maxload = 15;
				if ($newtimePeriod == '1h') $maxload = 10;
				if ($newtimePeriod == '1day') $maxload = 12;
				if ($newtimePeriod == '1week') $maxload = 7;
				if ($newtimePeriod == '1month') $maxload = 4;
					
				while($res_history = mysqli_fetch_assoc($result_history)) {
					$average_count++;
					$minerShares = $minerShares+$res_history['minerShares'];
					$minerSpeed = $minerSpeed+$res_history['minerSpeed'];
				}
				//Insert x min Einträge
				if ($newtimePeriod == '1min') {
					$average_speed = $miner_data['SUMMERY']['SUMMARY']['0']['MHS 1m'];
					$average_count++;
				}
				else if ($newtimePeriod == '5min') {$average_speed = $miner_data['SUMMERY']['SUMMARY']['0']['MHS 5m'];}
				else if ($newtimePeriod == '15min') {$average_speed = $miner_data['SUMMERY']['SUMMARY']['0']['MHS 15m'];}
				else {$average_speed = ($minerSpeed / $average_count);}
				if ($average_count >= $maxload) {
					$this->db->real_query("INSERT INTO `mi_miner_history` (`transactionHash`,
																  `minerToken`,
																  `timePeriode`,
																  `minerSpeed`,
																  `minerShares`,
																  `TotalminerShares`,
																  `timestamp`
																  ) VALUES (
																  '".$transactioncode."',
																  '".$minertoken."',
																  '".$newtimePeriod."',
																  '".$average_speed."',
																  '".((isset($minerShares) && $minerShares < $miner_data['SHARES']['Accepted'])?$minerShares:$miner_data['SHARES']['Accepted'])."',
																  '".$miner_data['SHARES']['Accepted']."',
																  NOW()
																	)");
					
					
				}
				//Lösche alte Einträge
				/*file_put_contents('error.log', "DELETE FROM `mi_miner_history` WHERE 
																		`transactionHash` = '".$transactioncode."' AND 
																		`minerToken` = '".$minertoken."' AND
																		`timePeriode` = '".$oldtimePeriod."' AND 
																		`timestamp` < NOW() - INTERVAL ".$time." MINUTE");
				*/
				$this->db->real_query("DELETE FROM `mi_miner_history` WHERE 
																		`transactionHash` = '".$transactioncode."' AND 
																		`minerToken` = '".$minertoken."' AND
																		`timePeriode` = '".$oldtimePeriod."' AND 
																		`timestamp` < NOW() - INTERVAL ".$time." MINUTE");
				}
		}
	}

	//
	
}

?>