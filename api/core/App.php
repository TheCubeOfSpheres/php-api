<?php

class App {
//Singleton

	private static $_xInstance = null;
	private $_bConnected = false;
	private $_xDBConnection = null;
	private $_aConnectionDetails = null;
	private $_xMongoConnection = null;

	public static function getInstance() {
		if (!self::$_xInstance){
			self::$_xInstance = new App();
		}
		return self::$_xInstance;
	}

	public function setConnectionDetails($p_sDBHost,$p_sDBUsername,$p_DBPassword,$p_DBName){
		$this->_aConnectionDetails = array('host' => $p_sDBHost, 'username' => $p_sDBUsername, 'pass' => $p_DBPassword, 'db' => $p_DBName);
		$this->connectToResource();
	}

	public function connectToResource(){
		$this->_xDBConnection = new mysqli($this->_aConnectionDetails['host'],$this->_aConnectionDetails['username'],$this->_aConnectionDetails['pass'],$this->_aConnectionDetails['db']);
		if(!$this->_xDBConnection){
			error_log('Unable to connect to host: '.$p_sDBHost);
			exit;
		}elseif($this->_xDBConnection->connect_errno > 0){
			error_log('Unable to connect to host: '.$p_sDBHost);
			exit;
		}
		$this->_xDBConnection->set_charset('utf8mb4');
		$this->_bConnected = true;
	}

	public function getConnection(){
		return $this->_xDBConnection;
	}

	public function db_last_insert_id(){
		return mysqli_insert_id($this->_xDBConnection);
	}

	public function escapeString($p_sStr){
		return mysqli_real_escape_string(App::getInstance()->getConnection(),$p_sStr);
	}


	private function coreInit() {
		if(!$this->_xDBConnection){
			$this->_xDBConnection = App::getInstance()->getConnection();
		}
	}

	public function closeConnection() {
		$this->getConnection()->close();
	}

	public function runQuery($p_sSql,$p_bReturnData = false, $p_bSingleRow = false){

		if(!$this->_xDBConnection)
			$this->coreInit();

		error_log($p_sSql);

		if($p_bReturnData){
			$l_xData = mysqli_query($this->_xDBConnection,$p_sSql);
			error_log('---------------SQL ERROR---------------------');
			error_log(mysqli_error($this->_xDBConnection) );

			$l_aReturnArray = array();
			if(mysqli_num_rows($l_xData) > 0){
				while($l_xRow = mysqli_fetch_assoc($l_xData)){
					$l_aReturnArray[] = $l_xRow;
				}
			}
			if($p_bSingleRow){
				if(count($l_aReturnArray) > 0){
					return $l_aReturnArray[0];
				}
			}
			return $l_aReturnArray;
		}else{
			mysqli_query($this->_xDBConnection,$p_sSql);
			return mysqli_insert_id($this->_xDBConnection);
		}

	}

	public function setMongoConnection() {
		// $mongoClient = new \MongoDB\Client("mongodb://pc_db:0JHHJaNBXkMgT76Z@10.132.0.5:27017/pc_db");
		// $this->_xMongoConnection = $mongoClient->pc_db;
	}

	public function getMongoConnection() {
		return $this->_xMongoConnection;
	}

}