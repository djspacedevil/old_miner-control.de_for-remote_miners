<?php
/*CRON JOB*/
include_once(__DIR__. '/mysql_data.php');

//Alte Einträge von Minern löschen
$con->real_query("UPDATE mi_miner SET minerSpeed = 0, minerJSON = '' WHERE minerTime < NOW() - INTERVAL 1 DAY");

/*Blocked IPs unblocking */
$con->real_query("DELETE FROM mi_blocked WHERE timestamp < NOW() - INTERVAL 30 DAY");

/*Zero Miner Speeds*/
$con->real_query("UPDATE `mi_miner` SET `minerSpeed` = 0 WHERE `minerTime` < NOW() - INTERVAL 120 SECOND");

/*Script Speeds sammeln*/
$script_result = $con->query("SELECT `id`, `config_name` FROM `mi_global_stats` WHERE `config` = 'hash_speed';");
if ($script_result->num_rows > 0) {
	while ($script_res = mysqli_fetch_assoc($script_result)) {
		$result = $con->query("SELECT `minerSpeed` FROM `mi_miner` WHERE minerScrypt= '".str_replace('-', '', strtolower($script_res['config_name']))."' AND `minerTime` > NOW() - INTERVAL 120 SECOND");
		$speed = 0;
		if ($result->num_rows > 0) {
			while ($res = mysqli_fetch_row($result)) {
				$speed += $res['0'];
			}
		}
		$con->real_query("UPDATE `mi_global_stats` SET `config_value` = ".$speed." WHERE `id` = ".$script_res['id'].";");
	}	
}

/*Top Pools sammeln*/
$con->real_query("UPDATE `mi_global_stats` SET `config_value` = 0 WHERE `config` ='top_pool';");
$pool_result = $con->query("SELECT Count(minerPool) as Counter, `minerPool` as Pool FROM `mi_miner` WHERE `minerPool` != '' GROUP BY `minerPool`;");
if ($pool_result->num_rows > 0) {
	while ($pool_res = mysqli_fetch_assoc($pool_result)) {
		$stat_pool_result = $con->query("SELECT `id` FROM `mi_global_stats` WHERE `config` = 'top_pool' AND `config_name` = '".$pool_res['Pool']."';");
		if ($stat_pool_result->num_rows == 1) {
			//Update Top Pools
			$stat_pool_res = mysqli_fetch_assoc($stat_pool_result);
			$con->real_query("UPDATE `mi_global_stats` SET `config_value` = ".$pool_res['Counter']." WHERE `id` =".$stat_pool_res['id'].";");
		} else if ($stat_pool_result->num_rows == 0) {
			//Insert Top Pools
			$con->real_query("INSERT INTO `mi_global_stats` (`config`, `config_name`, `config_value`) VALUES ('top_pool','".$pool_res['Pool']."', ".$pool_res['Counter'].");");
		} else {
			//Doppelte Einträge löschen
			$con->real_query("DELETE FROM `mi_global_stats` WHERE WHERE `config` = 'top_pool' AND `config_name` = '".$pool_res['Pool']."';");
			$con->real_query("INSERT INTO `mi_global_stats` (`config`, `config_name`, `config_value`) VALUES ('top_pool','".$pool_res['Pool']."', ".$pool_res['Counter'].");");
		}		
	}
}

/*Reset Pool Donations*/

$con->real_query("UPDATE `mi_users` SET `round_time_24h` = 86400, `round_benefit_time` = 0 WHERE `round_benefit_time` >= (site_benefit_procent*benefit_time); ");


?>