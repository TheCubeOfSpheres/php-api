<?php

class User {

	var $_nId;
	var $_sUsername;
	var $_sFirstName;
	var $_ssLastName;
	var $_sEmail;
	var $_iSex;
	var $_sAvatar;
	var $_sPassword;
	var $_sAbout;
	var $_sEmailVerified;
	var $_iUserStatus;
	var $_iAvailableStatus;
	var $_iReputation;
	var $_tLastSeen;
	var $_iUserType;
	var $_iLastRoomIn;
	var $_iBanned;
	var $_sRestKey;
	var $_sAccessToken;
	var $_dCreatedAt;
	var $_sIP;
	var $_sTimezone;
	var $_iLegacy;

	function __construct($p_xData) {
		$this->_nId = $p_xData['id'];
		$this->_sUsername = $p_xData['user_name'];
		$this->_sFirstName = $p_xData['first_name'];
		$this->_ssLastName = $p_xData['last_name'];
		$this->_sEmail = $p_xData['email'];
		$this->_dDob = $p_xData['dob'];
		$this->_iSex = $p_xData['sex'];
		$this->_sAvatar = $p_xData['avatar'];
		$this->_sPassword = $p_xData['password'];
		$this->_sAbout = $p_xData['about'];
		$this->_sEmailVerified = $p_xData['email_verified'];
		$this->_iUserStatus = $p_xData['user_status'];
		$this->_iAvailableStatus = $p_xData['available_status'];
		$this->_iReputation = $p_xData['reputation'];
		$this->_tLastSeen = $p_xData['last_seen'];
		$this->_iUserType = $p_xData['user_type'];
		$this->_iLastRoomIn = $p_xData['last_room_in'];
		$this->_iBanned = $p_xData['banned'];
		$this->_sRestKey = $p_xData['reset_key'];
		$this->_sAccessToken = $p_xData['access_token'];
		$this->_dCreatedAt = $p_xData['created_at'];
		$this->_sIP = $p_xData['ip'];
		$this->_sTimezone = $p_xData['timezone'];
		$this->_iLegacy = $p_xData['legacy'];
	}

	public function getId() {
		return $this->_nId;
	}

	public function getUsername() {
		return $this->_sUsername;
	}

	public function getFirstName() {
		return $this->_sUsername;
	}

	public function getLastName() {
		return $this->_sUsername;
	}

	public function getEmail() {
		return $this->_sUsername;
	}

	public function getDob() {
		return $this->_sUsername;
	}

}
?>