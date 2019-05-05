<?php
/***************************************************
				Miner-Control.de
			  Author: Sven Gössling
			   Not for Public use!
***************************************************/

class menu {
	
	protected $_menu;
	
	public function __construct($language) {
		global $con;
	
		$result = $con->query("SELECT `option_name`, `value` FROM `mi_config` WHERE `option_name` LIKE 'side_menu_%' ORDER BY `sort`");
		if($result->num_rows == 0) {
			$result = $con->query("SELECT `option_name`, `value` FROM `mi_config` WHERE `option_name` LIKE 'side_menu_%' AND language = 'EN' ORDER BY `sort`");	
		}
	
		$array_menu = array();
		while ($res = mysqli_fetch_assoc($result)) {
			$array_menu[$res['option_name']] = $res['value'];
		}
		
		$this->_menu = $array_menu;
	}
	
	//////////////////////////////////////////////////
	//Output
	public function getMenu() {
		return $this->_menu;
	}
	//
	//////////////////////////////////////////////////
}
?>