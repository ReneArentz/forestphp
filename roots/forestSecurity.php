<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.2.0 (0x1 00006)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * main class for security information
 * holding session values, user information, user rights and static functions for fphp framework security
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.0 alpha	renatus		2019-08-04	first build
 * 0.1.1 alpha	renatus		2019-08-08	add session and forestDateTime functionality
 * 0.1.5 alpha	renatus		2019-10-08	added GenerateCaptchaCharacter function
 */

class forestSecurity {
	use forestData;
	
	/* Fields */
	
	const SessionStatusAccess = 'access';
	 
	private $Access;
	private $InitAccess;
	private $SessionData;
	private $SessionId;
	private $SessionUUID;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct($p_b_debug = false) {
		$this->Access = new forestBool(null, false);
		$this->InitAccess = new forestBool(null, false);
		$this->SessionData = new forestObject(new forestObjectList('stdClass'), false);
		$this->SessionId = new forestString(session_id(), false, false);
		$this->SessionUUID = new forestString(null, false);
		
		if ($p_b_debug) { echo '<pre>#01__$_SESSION: '; print_r($_SESSION); echo '</pre>'; }
		
		/* fill session data object list container with current session data */
		foreach ($_SESSION as $s_session_key => $s_session_value) {
			$this->SessionData->value->Add($s_session_value, $s_session_key);
		}
	}
	
	public function init($p_b_debug = false) {
		$o_glob = forestGlobals::init();
		
		if ($p_b_debug) { echo '<hr /><pre>SECURITY DEBUG START<br /><br />'; }
		
		/* delete expired session records */
		$this->DeleteExpiredSessions($p_b_debug);
		
		if ($this->SessionData->value->Exists('session_uuid')) {
			$this->SessionUUID->value = $this->SessionData->value->{'session_uuid'};
		}
		
		if ($p_b_debug) { echo '#02__SessionUUID: ' . $this->SessionUUID->value . '<br />'; }
		
		if (issetStr($this->SessionUUID->value)) {
			if ($p_b_debug) { echo '#04__SessionUUID is set: ' . $this->SessionUUID->value . '<br />'; }
			$o_sessionTwig = new sessionTwig();
			
			if ($o_sessionTwig->GetRecord(array($this->SessionUUID->value))) {
				if (!$o_glob->FastProcessing) {
					if ($p_b_debug) { echo '#05__Active session found, update Timestamp<br />'; }
					
					/* active guest session found, update timestamp */
					$o_sessionTwig->Timestamp = new forestDateTime($o_glob->Trunk->DateTimeSqlFormat);
					
					if ($o_sessionTwig->UpdateRecord() == -1) {
						throw new forestException($o_glob->Temp->{'UniqueIssue'});
					}
					
					if ($p_b_debug) { echo '#06__Active session updated with Timestamp;set Access true<br />'; }
				}
				
				$this->Access->value = true;
			} else {
				if ($p_b_debug) { echo '#07__No active session found;delete SessionData and InitAccess true<br />'; }
				
				/* no guest session found, clear all information and set init flag */
				$this->SessionData->value->Del('session_uuid');
				$this->SessionData->value->Del('session_status');
			
				$this->InitAccess->value = true;
			}
		} else {
			if ($p_b_debug) { echo '#08__SessionUUID is NOT set;InitAccess true<br />'; }
			$this->InitAccess->value = true;
		}
		
		if ($this->InitAccess->value) {
			/* create new session entry, set session information and guest flague */
			$o_sessionTwig = new sessionTwig();
			$o_sessionTwig->Timestamp = new forestDateTime($o_glob->Trunk->DateTimeSqlFormat);
			
			if (!$o_glob->FastProcessing) {
				if ($o_sessionTwig->InsertRecord() == -1) {
					throw new forestException($o_glob->Temp->{'UniqueIssue'});
				}
			}
			
			if ($p_b_debug) { echo '#09__created session record; update SessionData<br />'; }
			$this->SessionUUID->value = $o_sessionTwig->UUID;
			
			$this->SessionData->value->Add($this->SessionUUID->value, 'session_uuid');
			$this->SessionData->value->Add(forestSecurity::SessionStatusAccess, 'session_status');
		}
		
		if ( (!$this->InitAccess->value) && (!$this->Access->value) ) {
			/* no session found, clear data and throw exception */
			$o_exception = new forestException(0x10000600);
			$this->Logout();
			throw $o_exception;
		}
		
		if ($p_b_debug) { echo '#24__sync SessionData<br />'; }
		$this->SyncSessionData($p_b_debug);
		if ($p_b_debug) { echo 'SECURITY DEBUG END</pre><hr />'; }
	}
	
	/* synchronize data in object list with $_SESSION array */
	public function SyncSessionData($p_b_debug = false) {
		foreach ($_SESSION as $s_session_key => $s_session_value) {
			$b_session_key_exists = false;
			
			foreach ($this->SessionData->value as $s_internalSession_key => $s_internalSession_value) {
				if ($s_session_key == $s_internalSession_key) {
					$b_session_key_exists = true;
				}
			}
			
			if (!$b_session_key_exists) {
				unset($_SESSION[$s_session_key]);
			}
		}
		
		foreach ($this->SessionData->value as $s_session_key => $s_session_value) {
			$_SESSION[$s_session_key] = $s_session_value;
		}
		
		if ($p_b_debug) { echo '#25__$_SESSION: '; print_r($_SESSION); echo '<br />'; }
	}
	
	/* logout function and cleanup */
	public function Logout() {
		$o_sessionTwig = new sessionTwig;
		
		if (issetStr($this->SessionUUID->value)) {
			if ($o_sessionTwig->GetRecord(array($this->SessionUUID->value))) {
				$o_sessionTwig->DeleteRecord();
			}
		}
		
		unset($_SESSION);
		unset($this->SessionUUID->value);
		unset($this->SessionData->value);
		unset($this->SessionId->value);
		@session_destroy();
	}
	
	/* delete expired session records */
	public function DeleteExpiredSessions($p_b_debug = false) {
		$o_glob = forestGlobals::init();
		
		/* calculate expired time */
		$o_DI = new forestDateInterval($o_glob->Trunk->SessionIntervalGuest);
		$o_now = new forestDateTime($o_glob->Trunk->DateTimeSqlFormat);
		
		$o_now->SubDateInterval($o_DI->y, $o_DI->m, $o_DI->d, $o_DI->h, $o_DI->i, $o_DI->s);
		
		/* create select query to delete expired sessions */
		$o_querySelect = new forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, forestSQLQuery::SELECT, 'sys_fphp_session');
			$o_uuid = new forestSQLColumn($o_querySelect);
				$o_uuid->Column = 'UUID';
			
			$o_timestamp = new forestSQLColumn($o_querySelect);
				$o_timestamp->Column = 'Timestamp';
			
			$o_querySelect->Query->Columns->Add($o_uuid);
			$o_querySelect->Query->Columns->Add($o_timestamp);
			
			$o_whereTimestamp = new forestSQLWhere($o_querySelect);
				$o_whereTimestamp->Column = $o_timestamp;
				$o_whereTimestamp->Value = $o_whereTimestamp->ParseValue($o_now->ToString());
				$o_whereTimestamp->Operator = '<';
				
			$o_querySelect->Query->Where->Add($o_whereTimestamp);
		
		$o_result = $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_querySelect);
		
		foreach($o_result->Twigs as $o_session) {
			/* delete session record */
			$o_session->DeleteRecord();
		}
	}
	
	/* generates uuid with php-function uniqid and '-'-delimiter */
	public function GenUUID() {
		$s_foo = md5(uniqid(mt_rand(), true));
		$a_blockDelimiter = array(8,13,18,23);
		$s_uuid = '';
		
		/* replace characters with block delimiter */
		for ($i = 0; $i < 32; $i++) {
			if (in_array($i, $a_blockDelimiter)) {
				$s_uuid .= '-';
			} else {
				$s_uuid .= $s_foo[$i];
			}
		}
		
		/* add 4 random characters because we added 4 block delimiters */
		$s_uuid .= $this->GenRandomString(4);
		
		return $s_uuid;
	}
	
	/* generates random string with Hex-Value and length parameters */
	private function GenRandomString($p_i_length = 0) {
		$s_foo = $this->GenRandomHash();
		
		if ($p_i_length > 32) {
  			throw new forestException('The amount of characters cannot be over 32');
		}
		
		if ($p_i_length < 1) {
  			throw new forestException('The amount of characters cannot be lower than 1');
		}
				
		$s_foo = substr($s_foo, 0, $p_i_length);
		
		return $s_foo;
	}
	
	/* generates random hash string */
	public function GenRandomHash() {
		mt_srand((double)microtime()*73291);
		return md5((time() - 7 * mt_rand(1,9901)) * 9739 / 6581);
	}
	
	/* generates hash string with sha512-algorithm */
	public function HashString(&$p_s_value, $p_s_salt, $p_i_loop) {
		for ($i = 0; $i < $p_i_loop; $i++) {
			$p_s_value = hash('sha512', $p_s_salt . $p_s_value);
			$p_s_value = md5($p_s_value . $p_s_salt);
		}
	}
	
	/* generates random character for captcha image */
	public function GenerateCaptchaCharacter($p_i_strength = 10) {
		$s_permittedChars = 'ABCDEFGHJKLMNPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
		$s_foo = '';
		
		for($i = 0; $i < $p_i_strength; $i++) {
			$s_foo = $s_permittedChars[mt_rand(0, strlen($s_permittedChars) - 1)];
		}
		
		return $s_foo;
	}
}
?>