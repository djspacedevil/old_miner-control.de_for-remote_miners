<?php
class user {

	protected $protected_pw = '';
	protected $db;
	
	private $salt = 'Gh&fvcDdäöih<DCT-Ic!@??4cddfq3';

	function __construct() {
		global $con;
		$this->db = $con;
	}
	
	function login_user ($username, $password, $oneAuthCode) {

		$protected_pw = hash('sha512', $username.$this->salt.$password);
		$login = false;
		
		$result = $this->db->query("SELECT `id`, `goo_auth`, `goo_active`, `language`, `email` FROM `mi_users` WHERE `username` = '".$username."' AND `password` = '".$protected_pw."' AND role <> 'new_miner'");
		if ($result->num_rows == 1) {
			$user_qu = mysqli_fetch_assoc($result); 
			unset($result);
			if ($user_qu['goo_active'] == 1) {
				$Auth = new PHPGangsta_GoogleAuthenticator();
				$checkResult = $Auth->verifyCode($user_qu['goo_auth'], $oneAuthCode, 2); // 2 = 2*30sec clock tolerance
				if ($checkResult) {
					$login = true;
				}
			} else if ($user_qu['goo_active'] == 0) {
				$login = true;
			}
			
			if ($login) {
				//login erfolgreich
				$user_id = preg_replace("/[^0-9]+/", "", $user_qu['id']);
				$user_browser = $_SERVER['HTTP_USER_AGENT'];
				
				$this->db->real_query("UPDATE `mi_users` SET `login_attempts` = 0, `last_login_date` = NOW(), last_ip = '".$_SERVER['REMOTE_ADDR']."' WHERE `username` = '".$username."' AND `password` = '".$protected_pw."' AND role <> 'new_miner'");
				
				$_SESSION['user_id'] = $user_id;
				$_SESSION['email'] = $user_qu['email'];
				$_SESSION['username'] = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $username);
				$_SESSION['login_string'] = hash('sha512', $password . $user_browser . microtime());
				$_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
				
				return true;
			}
		}
		unset($result);
		
		$result = $this->db->query("SELECT `id`,`email`, `login_attempts`, `language` FROM `mi_users` WHERE `username` = '".$username."' ");
		if ($result->num_rows == 1) {
			$this->db->real_query("UPDATE `mi_users` SET `login_attempts` = login_attempts + 1 WHERE `username` = '".$username."'");
			$failed = mysqli_fetch_assoc($result);
			
			if ($failed['login_attempts'] > 10) {
				$unblock_code = hash('sha256', microtime());
				send_alertmail($username, $failed['email'], $failed['language'], $_SERVER['REMOTE_ADDR'], $unblock_code);
				$this->db->real_query("REPLACE INTO `mi_blocked` (`blocked_ip`, `timestamp`, `unblock_code`) VALUES ('".$_SERVER['REMOTE_ADDR']."', NOW(), '".$unblock_code."')");
			}
		}

		return false;
	}

	
}

class online_user {
	
	protected $_userID;
	protected $_username;
	protected $_userrole;
	protected $_user_ip;
	protected $_email;
	protected $_language;
	protected $_goo_auth;
	protected $_goo_active;
	protected $_transaction_hash;
	protected $db;
	
	private $sha_pool;
	private $sha_user;
	private $sha_password;
	private $scrypt_pool;
	private $scrypt_user;
	private $scrypt_password;
	
	public function __construct($_SESSION) {
		global $con;
		$this->db = $con;
		
		$this->setMasterPools();
		
		$result = $this->db->query("SELECT `role`, `email`, `language`, `goo_auth`, `goo_active`, `transaction_hash`  FROM `mi_users` WHERE `id` = ".$_SESSION['user_id']);
		if ($result->num_rows == 1) {
			$user_infos = mysqli_fetch_assoc($result);
			$this->_userID = $_SESSION['user_id'];
			$this->_username = $_SESSION['username'];
			$this->_userrole = $user_infos['role'];
			$this->_user_ip = $_SESSION['user_ip'];
			$this->_email = $_SESSION['email'];
			$this->_language = $user_infos['language'];
			$this->_goo_auth = $user_infos['goo_auth'];
			$this->_goo_active = $user_infos['goo_active'];
			if ($user_infos['transaction_hash'] == "") {
				$user_infos['transaction_hash'] = $this->create_new_Thash();
				$this->db->real_query("UPDATE `mi_users` SET `transaction_hash`= '".$user_infos['transaction_hash']."' WHERE `id` = ".$_SESSION['user_id']);
			}
			$this->_transaction_hash = $user_infos['transaction_hash'];
		}
	}
	///////////////////////////////////////////////
	// Inputs
	public function setAuthCode($newAuthCode) {
		if($newAuthCode != "") {
			$this->db->real_query("UPDATE `mi_users` SET `goo_auth` = '".$newAuthCode."', `goo_active` = 1 WHERE `id` = ".$this->_userID);
			return true;
		}
		return false;
	}
	
	public function delAuthCode($oldAuthCode) {
		if($oldAuthCode != "") {
			$this->db->real_query("UPDATE `mi_users` SET `goo_auth` = '".$oldAuthCode."', `goo_active` = 0 WHERE `id` = ".$this->_userID);
			return true;
		}
		return false;
	}
	
	public function create_NewMiner() {
		$new_miner = "";
		$transactionHash = $this->_transaction_hash;
		$result = mysqli_fetch_row($this->db->query("SELECT COUNT(*) FROM `mi_miner` WHERE `transactionHash` = '".$transactionHash."'")); 
		$minerName = $this->_username.'_'.(substr(str_shuffle(MD5(microtime())), 0, 3)); //$result['0']+1
		$minerToken = hash('sha512', $minerName.microtime());
		unset($result);
		$this->db->query("INSERT INTO `mi_miner` (`transactionHash`,
											 `minerName`,
											 `minerToken`,
											 `minerTime`
											) VALUES (
											 '".$transactionHash."',
											 '".$minerName."',
											 '".$minerToken."',
											 NOW()
											)
					");
		$result = $this->db->query("SELECT * FROM `mi_miner` WHERE `minerToken` = '".$minerToken."'");
		$new_miner = json_encode(mysqli_fetch_assoc($result));
		
		return $new_miner;
	}
	
	public function delete_Miner ($MinerID, $TransactionHash, $minerToken) {
		$result = $this->db->query("SELECT `id` FROM `mi_miner` WHERE `transactionHash` = '".$TransactionHash."' AND `minerToken` = '".$minerToken."'");
		if ($result->num_rows == 1) {
			$this->db->real_query("DELETE FROM `mi_miner` WHERE `transactionHash` = '".$TransactionHash."' AND `minerToken` = '".$minerToken."'");
			return true;
		}
		return false;
	}
	
	public function setConfigfile($configFile, $minerID) {
		if ($configFile != "") {
			$this->db->real_query("UPDATE `mi_miner` SET `minerConfig` = '".addslashes($configFile)."' WHERE `id` = ".(int)$minerID.";");
			return true;
		}
		return false;
	}
	
	public function deletePool($minerID, $poolID) {
		if ($minerID != "" && $poolID != "") {
			$result = $this->db->query("SELECT `minerDeletePools` FROM `mi_miner` WHERE `id` = ".(int)$minerID.";");
			$res = mysqli_fetch_assoc($result);
			
			$deletePools = json_decode($res['minerDeletePools']);
			$deletePools[] = $poolID;
			$deletePools = json_encode($deletePools);
			
			$this->db->query("UPDATE `mi_miner` SET `minerDeletePools` = '".$deletePools."' WHERE `id` = ".(int)$minerID.";");
			return true;
		}
		return false;
	}
	
	public function setNewPool($minerID, $Pool, $User, $Password) {
		if ($minerID != "" && $Pool != "" && $User != "" && $Password != "") {
			$result = $this->db->query("SELECT `minerNewPools` FROM `mi_miner` WHERE `id` = ".(int)$minerID.";");
			$res = mysqli_fetch_assoc($result);
			
			$newPools = json_decode($res['minerNewPools']);
			$count = count($newPools)+1;
			$newPools[$count]['Pool'] = $Pool;
			$newPools[$count]['User'] = $User;
			$newPools[$count]['Password'] = $Password;
			$newPools = json_encode($newPools);
			
			$this->db->query("UPDATE `mi_miner` SET `minerNewPools` = '".$newPools."' WHERE `id` = ".(int)$minerID.";");
			return true;
		}
		
		return false;
	}
	
	//
	///////////////////////////////////////////////
	
	///////////////////////////////////////////////
	//Outputs
	public function getUserID() {
		return $this->_userID;
	}
	
	public function getUsername() {
		return $this->_username;
	}
	
	public function getUserRole() {
		return $this->_userrole;
	}
	
	public function getEmail() {
		return $this->_email;
	}
	
	public function getLanguage() {
		return $this->_language;
	}
	
	public function getGooAuth() {
		return $this->_goo_auth;
	}
	
	public function getGooActive() {
		return $this->_goo_active;
	}
	
	public function getTransactionHash() {
		return $this->_transaction_hash;
	}
	
	public function getSHAPool() {
		return $this->sha_pool;
	}
	
	public function getSHAUser() {
		return $this->sha_user;
	}
	
	public function getSCRYPTPool() {
		return $this->scrypt_pool;
	}
	
	public function getSCRYPTUser() {
		return $this->scrypt_user;
	}
	
	public function getAllMinerCount() {
		$result = $this->db->query("SELECT COUNT(id) FROM `mi_users`;");
		$res = mysqli_fetch_row($result);
		return $res['0'];
	}
	
	public function getGlobalStat($config, $config_name = '') {
		$result = $this->db->query("SELECT * FROM `mi_global_stats` WHERE `config` = '".$config."' ".(($config_name != '')?"`config_name` = '".$config_name."'":"")." ORDER BY config_value;");
		if ($result->num_rows > 0) {
			$array = array('success' => 'true');
			while ($res = mysqli_fetch_assoc($result)) {
				$array[] = $res;
			}
			return $array;
		}
		return 'false';
	}
	
	public function getTopPools() {
		$result = $this->db->query("SELECT * FROM `mi_global_stats` WHERE `config` = 'top_pool' ORDER BY `config_value` DESC LIMIT 10");
		if ($result->num_rows > 0) {
			while ($res = mysqli_fetch_assoc($result)) {
				$array[] = $res;
			}
			return $array;
		}
		return 'false';
	}
	
	public function getTopMinerSHA() {
		$result = $this->db->query("SELECT mi_users.username,
										   SUM(mi_miner.minerSpeed) as minerSpeed 
									FROM   mi_users,
										   mi_miner 
									WHERE  mi_users.transaction_hash = mi_miner.transactionHash AND
										   mi_miner.minerScrypt = 'sha256' 
									GROUP BY username 
									ORDER BY minerSpeed DESC
									LIMIT 10;");
		if ($result->num_rows > 0) {
			while ($res = mysqli_fetch_assoc($result)) {
				$array[] = $res;
			}
			return $array;
		}
		return 'false';	
	}
	
	public function getTopContributor() {
		$result = $this->db->query("SELECT `username`, `complete_benefit_time`  FROM `mi_users` WHERE `complete_benefit_time` <> 0 ORDER BY `complete_benefit_time` DESC LIMIT 10");
		if ($result->num_rows > 0) {
			while ($res = mysqli_fetch_assoc($result)) {
				$array[] = $res;
			}
			return $array;
		}
		return 'false';	
	}
	
	public function getTopMinerSCRYPT() {
		$result = $this->db->query("SELECT mi_users.username,
										   SUM(mi_miner.minerSpeed) as minerSpeed 
									FROM   mi_users,
										   mi_miner 
									WHERE  mi_users.transaction_hash = mi_miner.transactionHash AND
										   mi_miner.minerScrypt = 'scrypt' 
									GROUP BY username 
									ORDER BY minerSpeed DESC
									LIMIT 10;");
		if ($result->num_rows > 0) {
			while ($res = mysqli_fetch_assoc($result)) {
				$array[] = $res;
			}
			return $array;
		}
		return 'false';	
	}
	
	public function getAllActiveMinerCount() {
		$result = $this->db->query("SELECT COUNT(id) FROM `mi_miner` WHERE `minerTime` > NOW() - INTERVAL 120 SECOND");
		$res = mysqli_fetch_row($result);
		return $res['0'];
	}
	
	public function getMinerDonation() {
		$result = $this->db->query("SELECT `role`, `site_benefit_procent`, `benefit_time`, `round_time_24h` FROM `mi_users` WHERE `id` = ".$this->_userID);
		return mysqli_fetch_assoc($result);
	}
	
	public function setMinerDonation($newBenefit) {
		if (isset($newBenefit) && is_numeric($newBenefit)) {
			if ($newBenefit <= 0) $newBenefit = 1;
			if ($newBenefit >= 100) $newBenefit = 100;
		} else {
			$newBenefit = 100;
		}
		$this->db->real_query("UPDATE `mi_users` SET `site_benefit_procent` = ".$newBenefit."  WHERE `id` = ".$this->_userID);
		return 'true';
	}
	
	public function setSwitchPool($PoolID) {
		$this->db->real_query("UPDATE `mi_users` SET `pool_before_benefit` = ".(int)$PoolID." WHERE `id` = ".$this->_userID);
		return true;
	}
	
	public function getListActiveMiner() {
		$result = $this->db->query("SELECT * FROM `mi_miner` WHERE `transactionHash` = '".$this->_transaction_hash."' AND `minerTime` > NOW() - INTERVAL 120 SECOND ORDER BY `id`");
		$all_miner = array();
		if ($result->num_rows > 0) {
			
			
			while($res = mysqli_fetch_assoc($result)) {
				$all_miner[] = $res;
			}
		}
		return $all_miner;
	}
	
	public function setMinerName($newMinerName, $MinerID) {
		if (is_numeric($MinerID) && $newMinerName != "") {
			$this->db->real_query("UPDATE `mi_miner` SET `minerName` = '".$newMinerName."' WHERE id = ".(int)$MinerID);
			return $newMinerName;
		}
		return '';
	}
	
	public function getListInactiveMiner() {
		$result = $this->db->query("SELECT * FROM `mi_miner` WHERE `transactionHash` = '".$this->_transaction_hash."' AND `minerTime` < NOW() - INTERVAL 120 SECOND ORDER BY `id` ");
		$all_miner = array();
		if ($result->num_rows > 0) {
			
			
			while($res = mysqli_fetch_assoc($result)) {
				$all_miner[] = $res;
			}
		}
		return $all_miner;
	}
	
	public function getMainChart_complete_Speed_Average($timePeriode) {
		$result = $this->db->query("SELECT 	mi_miner_history.minerSpeed, 
										mi_miner_history.timestamp,
										mi_miner.minerName

							  FROM 		mi_miner_history,
										mi_miner

							  WHERE 	mi_miner_history.transactionHash = '".$this->_transaction_hash."' AND
										mi_miner_history.timePeriode = '".$timePeriode."' AND
										mi_miner_history.transactionHash = mi_miner.transactionHash AND
										mi_miner_history.minerToken = mi_miner.minerToken
										
										");
		if ($result->num_rows > 0) {
			
			$output = 'data: [';
			$miner_name = array();
			while ($res = mysqli_fetch_assoc($result)) {
				if (!isset($miner_name[$res['minerName']])) $miner_name[$res['minerName']] = $res['minerName'];
				if ($output != "data: [") $output .= ', ';
				$minerSpeed = $this->transform_Hashpower($res['minerSpeed']);
				$output .= "{ Time: '".$res['timestamp']."', '".$res['minerName']."': '".sprintf("%01.2f", $minerSpeed['speed'])."'}"; 
			}
			$minername = '';
			foreach ($miner_name as $name) {
				if ($minername != '') $minername .= ', ';
				$minername .= "'".$name."'";
			}
			
			$output .= "],
			xkey: 'Time',
			ykeys: [".$minername."],
			labels: [".$minername."],
			postUnits: ' ".$minerSpeed['SpeedUnit']."'";
		return $output;
		}	
		return '';
	}
	
	
	//
	///////////////////////////////////////////////
	
	///////////////////////////////////////////////
	// Functions
	private function create_new_Thash() {
		//Create new Transaction Hashcode
		return hash('whirlpool', hash('sha512', microtime().$this->_username.$this->_email.microtime()));;
	}
	
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
	
	private function transform_Hashpower($speed) {
		$SpeedUnit[0] = 'H/s';
		$SpeedUnit[1] = 'KH/s';
		$SpeedUnit[2] = 'MH/s';
		$SpeedUnit[3] = 'GH/s';
		$SpeedUnit[4] = 'TH/s';
		$SpeedUnit[5] = 'PH/s';
		
		$u = 2;
		while ($speed > 1000) {
				$speed = ($speed/1000);
				$u++;
		}
		
		return array('speed' => $speed, 'SpeedUnit' => $SpeedUnit[$u]);
		
	}
	//
	///////////////////////////////////////////////
}

?>