<?php
/**
 * main class for security information
 * holding session values, user information, user rights and static functions for fphp framework security
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestphp.de>
 * @copyright   (c) 2019 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     0.9.0 beta
 * @link        http://www.forestphp.de/
 * @object-id   0x1 00006
 * @since       File available since Release 0.1.0 alpha
 * @deprecated  -
 *
 * @version log Version		Developer	Date		Comment
 * 		0.1.0 alpha	renatus		2019-08-04	first build
 * 		0.1.1 alpha	renatus		2019-08-08	add session and forestDateTime functionality
 * 		0.1.5 alpha	renatus		2019-10-08	added GenerateCaptchaCharacter function
 * 		0.4.0 beta	renatus		2019-11-11	added enhanced user administration functionalities
 * 		0.4.0 beta	renatus		2019-11-12	distinguish between guest and user
 * 		0.4.0 beta	renatus		2019-11-13	added ListUserPermissions and CheckUserPermission functions
 * 		0.8.0 beta	renatus		2020-01-17	added account record access in init() to overwrite language settings
 */

namespace fPHP\Security;

use \fPHP\Roots\{forestString, forestList, forestNumericString, forestInt, forestFloat, forestBool, forestArray, forestObject, forestLookup};
use \fPHP\Helper\forestObjectList;
use \fPHP\Roots\forestException as forestException;

class forestSecurity {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	const SessionStatusGuest = 'guest';
	const SessionStatusUser = 'user';
	 
	private $UserAccess;
	private $GuestAccess;
	private $InitAccess;
	private $UserUUID;
	private $User;
	private $RootUser;
	private $SessionData;
	private $SessionId;
	private $SessionUUID;
	private $UserPermissions;
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * constructor of forestSecurity class
	 *
	 * @param bool $p_b_debug  flag to show debug information
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __construct($p_b_debug = false) {
		$this->UserAccess = new forestBool(null, false);
		$this->GuestAccess = new forestBool(null, false);
		$this->InitAccess = new forestBool(null, false);
		$this->UserUUID = new forestString(null, false);
		$this->User = new forestString(null, false);
		$this->RootUser = new forestBool(false, false);
		$this->SessionData = new forestObject(new forestObjectList('stdClass'), false);
		$this->SessionId = new forestString(session_id(), false, false);
		$this->SessionUUID = new forestString(null, false);
		$this->UserPermissions = new forestObject('stdClass', false);
		
		if ($p_b_debug) { echo '<pre>#01__$_SESSION: '; print_r($_SESSION); echo '</pre>'; }
		
		/* fill session data object list container with current session data */
		foreach ($_SESSION as $s_session_key => $s_session_value) {
			$this->SessionData->value->Add($s_session_value, $s_session_key);
		}
	}
	
	/**
	 * init function of forestSecurity class, handling session records, guest and user access
	 *
	 * @param bool $p_b_debug  flag to show debug information
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function init($p_b_debug = false) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		if ($p_b_debug) { echo '<hr /><pre>SECURITY DEBUG START<br /><br />'; }
		
		/* delete expired session records */
		$this->DeleteExpiredSessions($p_b_debug);
		
		if ($this->SessionData->value->Exists('session_uuid')) {
			$this->SessionUUID->value = $this->SessionData->value->{'session_uuid'};
		}
		
		if ($p_b_debug) { echo '#02__SessionUUID: ' . $this->SessionUUID->value . '<br />'; }
		
		$b_guest = true;
		
		if ($this->SessionData->value->Exists('session_status')) {
			if ($this->SessionData->value->{'session_status'} == forestSecurity::SessionStatusUser) {
				$b_guest = false;
			}
		}
		
		/* if user has guest access we update the session or create a new one */
		/* otherwise we check if user session still exists */
		if ($b_guest) {
			if ($p_b_debug) { echo '#03__Session is guest<br />'; }
			if (issetStr($this->SessionUUID->value)) {
				if ($p_b_debug) { echo '#04__SessionUUID is set: ' . $this->SessionUUID->value . '<br />'; }
				$o_sessionTwig = new \fPHP\Twigs\sessionTwig();
				
				if ($o_sessionTwig->GetRecord(array($this->SessionUUID->value))) {
					if (!$o_glob->FastProcessing) {
						if ($p_b_debug) { echo '#05__Active session found, update Timestamp<br />'; }
						
						/* active guest session found, update timestamp */
						$o_sessionTwig->Timestamp = new \fPHP\Helper\forestDateTime($o_glob->Trunk->DateTimeSqlFormat);
						
						if ($o_sessionTwig->UpdateRecord() == -1) {
							throw new forestException($o_glob->Temp->{'UniqueIssue'});
						}
						
						if ($p_b_debug) { echo '#06__Active session updated with Timestamp;set GuestAccess true<br />'; }
					}
					
					$this->GuestAccess->value = true;
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
				$o_sessionTwig = new \fPHP\Twigs\sessionTwig();
				$o_sessionTwig->Timestamp = new \fPHP\Helper\forestDateTime($o_glob->Trunk->DateTimeSqlFormat);
				$o_sessionTwig->UserUUID = $o_glob->Trunk->UUIDGuest;
				
				if (!$o_glob->FastProcessing) {
					if ($o_sessionTwig->InsertRecord() == -1) {
						throw new forestException($o_glob->Temp->{'UniqueIssue'});
					}
				}
				
				if ($p_b_debug) { echo '#09__created session record; update SessionData<br />'; }
				$this->SessionUUID->value = $o_sessionTwig->UUID;
				
				$this->SessionData->value->Add($this->SessionUUID->value, 'session_uuid');
				$this->SessionData->value->Add(forestSecurity::SessionStatusGuest, 'session_status');
			}
		} else {
			if ($p_b_debug) { echo '#16__Session is no guest<br />'; }
			$o_sessionTwig = new \fPHP\Twigs\sessionTwig();
			
			/* session information says we have a user, checking record */
			if ( (issetStr($this->SessionUUID->value)) && ($o_sessionTwig->GetRecord(array($this->SessionUUID->value))) ) {
				if (!$o_glob->FastProcessing) {
					if ($p_b_debug) { echo '#17__Active session found, update Timestamp and UserUUID[' . (($o_glob->Temp->Exists('fphp_UserUUID')) ? $o_glob->Temp->{'fphp_UserUUID'} : 'not necessary') . ']<br />'; }
					
					/* active session found, update timestamp and user uuid */
					$o_sessionTwig->Timestamp = new \fPHP\Helper\forestDateTime($o_glob->Trunk->DateTimeSqlFormat);
					
					if ($o_glob->Temp->Exists('fphp_UserUUID')) {
						$o_sessionTwig->UserUUID = $o_glob->Temp->{'fphp_UserUUID'};
					}
					
					if ($o_sessionTwig->UpdateRecord() == -1) {
						throw new forestException($o_glob->Temp->{'UniqueIssue'});
					}
					
					if ($p_b_debug) { echo '#18__Active session updated with Timestamp and UserUUID;set UserAccess true<br />'; }
				}
				
				$this->UserAccess->value = true;
				$o_glob->Trunk->NavbarShowLoginPart = false;
			} else {
				if ($p_b_debug) { echo '#19__No active session found;delete SessionData and throw exception<br />'; }
				
				/* no session found, clear data and throw exception */
				$o_exception = new forestException(0x10000600);
				$this->Logout();
				throw $o_exception;
			}
		}
		
		if ( (($this->GuestAccess->value) || ($this->UserAccess->value)) ) {
			$o_sessionTwig = new \fPHP\Twigs\sessionTwig();
			if ($p_b_debug) { echo '#20__Recheck session record<br />'; }
			
			if ($o_sessionTwig->GetRecord(array($this->SessionUUID->value))) {
				if ($p_b_debug) { echo '#21__session record found;read UserUUID<br />'; }
				$this->UserUUID->value = $o_sessionTwig->UserUUID->PrimaryValue;
			}
			
			$o_userTwig = new \fPHP\Twigs\userTwig();
			
			if ($o_userTwig->GetRecord(array($this->UserUUID->value))) {
				if ($p_b_debug) { echo '#22__user record found;read User[' . $o_userTwig->User . ']<br />'; }
				$this->User->value = $o_userTwig->User;
				
				/* check locked status */
				if ($o_userTwig->Locked) {
					$this->Logout();
					header('Location: ./');
					exit();
				}
				
				if ($o_userTwig->RootUser) {
					$this->RootUser->value = true;
				}
			}
			
			/* if exists, overwrite language-code settings from account settings */
			$o_accountTwig = new \fPHP\Twigs\accountTwig();
			
			if ($o_accountTwig->GetRecord(array($this->UserUUID->value))) {
				if ($p_b_debug) { echo '#23__account record found;read LanguageCode<br />'; }
				
				if (issetStr($o_accountTwig->LanguageCode->PrimaryValue)) {
					$o_glob->Trunk->LanguageCode = $o_accountTwig->LanguageCode;
				}
			}
		}
		
		if ($p_b_debug) { echo '#24__sync SessionData<br />'; }
		$this->SyncSessionData($p_b_debug);
		if ($p_b_debug) { echo 'SECURITY DEBUG END</pre><hr />'; }
	}
	
	/**
	 * synchronize data in object list with $_SESSION array
	 *
	 * @param bool $p_b_debug  flag to show debug information
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
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
	
	/**
	 * list permission records for user and save them in internal object twig list
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function ListUserPermissions() {
		/*
		SELECT `sys_fphp_permission`.`Branch`, `sys_fphp_permission`.`Action`, `sys_fphp_permission`.`Name` FROM `sys_fphp_user`
		INNER JOIN `sys_fphp_usergroup_user` ON `sys_fphp_usergroup_user`.`userUUID` = `sys_fphp_user`.`UUID`
		INNER JOIN `sys_fphp_usergroup_role` ON `sys_fphp_usergroup_role`.`usergroupUUID` = `sys_fphp_usergroup_user`.`usergroupUUID`
		INNER JOIN `sys_fphp_role_permission` ON `sys_fphp_role_permission`.`roleUUID` = `sys_fphp_usergroup_role`.`roleUUID`
		INNER JOIN `sys_fphp_permission` ON `sys_fphp_permission`.`UUID` = `sys_fphp_role_permission`.`permissionUUID`
		WHERE `sys_fphp_user`.`UUID` = '8f8861d9-9c31-3303-7660-5b588ca0050f' AND `sys_fphp_user`.`Locked` = 0
		ORDER BY `sys_fphp_permission`.`Branch`, `sys_fphp_permission`.`Action`
		*/
		
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$o_querySelect = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::SELECT, 'sys_fphp_user');
		
		$o_permission_uuid = new \fPHP\Base\forestSQLColumn($o_querySelect);
			$o_permission_uuid->Table = 'sys_fphp_permission';
			$o_permission_uuid->Column = 'UUID';
			
		$o_permission_branch = new \fPHP\Base\forestSQLColumn($o_querySelect);
			$o_permission_branch->Table = 'sys_fphp_permission';
			$o_permission_branch->Column = 'Branch';
		
		$o_permission_action = new \fPHP\Base\forestSQLColumn($o_querySelect);
			$o_permission_action->Table = 'sys_fphp_permission';
			$o_permission_action->Column = 'Action';
		
		$o_usergroupuser_useruuid = new \fPHP\Base\forestSQLColumn($o_querySelect);
			$o_usergroupuser_useruuid->Table = 'sys_fphp_usergroup_user';
			$o_usergroupuser_useruuid->Column = 'userUUID';
			
		$o_usergroupuser_usergroupuuid = new \fPHP\Base\forestSQLColumn($o_querySelect);
			$o_usergroupuser_usergroupuuid->Table = 'sys_fphp_usergroup_user';
			$o_usergroupuser_usergroupuuid->Column = 'usergroupUUID';
		
		$o_user_uuid = new \fPHP\Base\forestSQLColumn($o_querySelect);
			$o_user_uuid->Column = 'UUID';
			
		$o_user_locked = new \fPHP\Base\forestSQLColumn($o_querySelect);
			$o_user_locked->Column = 'Locked';
			
		$o_usergrouprole_usergroupuuid = new \fPHP\Base\forestSQLColumn($o_querySelect);
			$o_usergrouprole_usergroupuuid->Table = 'sys_fphp_usergroup_role';
			$o_usergrouprole_usergroupuuid->Column = 'usergroupUUID';
			
		$o_usergrouprole_roleuuid = new \fPHP\Base\forestSQLColumn($o_querySelect);
			$o_usergrouprole_roleuuid->Table = 'sys_fphp_usergroup_role';
			$o_usergrouprole_roleuuid->Column = 'roleUUID';
		
		$o_rolepermission_roleuuid = new \fPHP\Base\forestSQLColumn($o_querySelect);
			$o_rolepermission_roleuuid->Table = 'sys_fphp_role_permission';
			$o_rolepermission_roleuuid->Column = 'roleUUID';
			
		$o_rolepermission_permissionuuid = new \fPHP\Base\forestSQLColumn($o_querySelect);
			$o_rolepermission_permissionuuid->Table = 'sys_fphp_role_permission';
			$o_rolepermission_permissionuuid->Column = 'permissionUUID';
			
		$o_querySelect->Query->Columns->Add($o_permission_branch);
		$o_querySelect->Query->Columns->Add($o_permission_action);
		
		$join_A = new \fPHP\Base\forestSQLJoin($o_querySelect);
			$join_A->JoinType = 'INNER JOIN';
			$join_A->Table = 'sys_fphp_usergroup_user';
		
			$relation_A = new \fPHP\Base\forestSQLRelation($o_querySelect);
				$relation_A->ColumnLeft = $o_usergroupuser_useruuid;
				$relation_A->ColumnRight = $o_user_uuid;
				$relation_A->Operator = '=';
			
			$join_A->Relations->Add($relation_A);
		
		$join_B = new \fPHP\Base\forestSQLJoin($o_querySelect);
			$join_B->JoinType = 'INNER JOIN';
			$join_B->Table = 'sys_fphp_usergroup_role';
		
			$relation_A = new \fPHP\Base\forestSQLRelation($o_querySelect);
				$relation_A->ColumnLeft = $o_usergrouprole_usergroupuuid;
				$relation_A->ColumnRight = $o_usergroupuser_usergroupuuid;
				$relation_A->Operator = '=';
			
			$join_B->Relations->Add($relation_A);
		
		$join_C = new \fPHP\Base\forestSQLJoin($o_querySelect);
			$join_C->JoinType = 'INNER JOIN';
			$join_C->Table = 'sys_fphp_role_permission';
		
			$relation_A = new \fPHP\Base\forestSQLRelation($o_querySelect);
				$relation_A->ColumnLeft = $o_rolepermission_roleuuid;
				$relation_A->ColumnRight = $o_usergrouprole_roleuuid;
				$relation_A->Operator = '=';
			
			$join_C->Relations->Add($relation_A);
		
		$join_D = new \fPHP\Base\forestSQLJoin($o_querySelect);
			$join_D->JoinType = 'INNER JOIN';
			$join_D->Table = 'sys_fphp_permission';
		
			$relation_A = new \fPHP\Base\forestSQLRelation($o_querySelect);
				$relation_A->ColumnLeft = $o_permission_uuid;
				$relation_A->ColumnRight = $o_rolepermission_permissionuuid;
				$relation_A->Operator = '=';
			
			$join_D->Relations->Add($relation_A);
			
		$o_querySelect->Query->Joins->Add($join_A);
		$o_querySelect->Query->Joins->Add($join_B);
		$o_querySelect->Query->Joins->Add($join_C);
		$o_querySelect->Query->Joins->Add($join_D);
		
		$where_A = new \fPHP\Base\forestSQLWhere($o_querySelect);
			$where_A->Column = $o_user_uuid;
			$where_A->Value = $where_A->ParseValue($this->UserUUID->value);
			$where_A->Operator = '=';
			
		$where_B = new \fPHP\Base\forestSQLWhere($o_querySelect);
			$where_B->Column = $o_user_locked;
			$where_B->Value = $where_B->ParseValue(0);
			$where_B->Operator = '=';
			$where_B->FilterOperator = 'AND';
			
		$o_querySelect->Query->Where->Add($where_A);
		$o_querySelect->Query->Where->Add($where_B);
		
		$this->UserPermissions->value = $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_querySelect, false, false);
	}
	
	/**
	 * check if permission records for current branch/action access are available for guest or user
	 *
	 * @param string $p_s_branch  name of branch
	 * @param string $p_s_action  name of action
	 *
	 * @return bool  true - user/guest has permission, false - user/guest has no permission
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function CheckUserPermission($p_s_branch = null, $p_s_action = null) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		$a_branchTree = $o_glob->BranchTree;
		
		/*d2c('Entry Branch: ' . $p_s_branch);*/
		/*d2c('Entry Action: ' . $p_s_action);*/
		
		if (!is_null($p_s_branch)) {
			if (!array_key_exists($p_s_branch, $a_branchTree['Name'])) {
				throw new forestException('Branch[%0] could not be found', array($p_s_branch));
			}
			
			$p_s_branch = $a_branchTree['Name'][$p_s_branch];
		} else {
			$p_s_branch = $o_glob->URL->BranchId;
		}
		
		/*d2c('Branch: ' . $p_s_branch);*/
		
		if (!is_null($p_s_action)) {
			if (!array_key_exists($p_s_action, $a_branchTree['Id'][$p_s_branch]['actions']['Name'])) {
				throw new forestException('Action[%0] could not be found', array($p_s_action));
			}
			
			$p_s_action = $a_branchTree['Id'][$p_s_branch]['actions']['Name'][$p_s_action];
		} else {
			if ($o_glob->URL->ActionId == 0) {
				$p_s_action = $a_branchTree['Id'][$p_s_branch]['actions']['Name']['init'];
			} else {
				$p_s_action = $o_glob->URL->ActionId;
			}
		}
		
		/*d2c('Action: ' . $p_s_action);*/
		
		/* check permission inheritance and start recursive check for user permission */
		if ($a_branchTree['Id'][$p_s_branch]['PermissionInheritance']) {
			return $this->CheckUserPermission($a_branchTree['Id'][$a_branchTree['Id'][$p_s_branch]['ParentBranch']]['Name'], $a_branchTree['Id'][$p_s_branch]['actions']['Id'][$p_s_action]);
		}
		
		foreach($this->UserPermissions->value as $a_userpermission) {
			if ( ($a_userpermission['Branch'] == $p_s_branch) && ($a_userpermission['Action'] == $p_s_action) ) {
				return true;
			} else if ( ($a_userpermission['Branch'] == 0) && ($a_branchTree['Zero']['actions']['Id'][$a_userpermission['Action']] == $a_branchTree['Id'][$p_s_branch]['actions']['Id'][$p_s_action]) ) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * logout function and cleanup
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function Logout() {
		$o_sessionTwig = new \fPHP\Twigs\sessionTwig;
		
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
	
	/**
	 * delete expired guest and user session records
	 *
	 * @param bool $p_b_debug  flag to show debug information
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function DeleteExpiredSessions($p_b_debug = false) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* calculate expired guest time */
		$o_DIGuest = new \fPHP\Helper\forestDateInterval($o_glob->Trunk->SessionIntervalGuest);
		$o_nowGuest = new \fPHP\Helper\forestDateTime($o_glob->Trunk->DateTimeSqlFormat);
		
		$o_nowGuest->SubDateInterval($o_DIGuest->y, $o_DIGuest->m, $o_DIGuest->d, $o_DIGuest->h, $o_DIGuest->i, $o_DIGuest->s);
		
		/* calculate expired user time */
		$o_DIUser = new \fPHP\Helper\forestDateInterval($o_glob->Trunk->SessionIntervalUser);
		$o_nowUser = new \fPHP\Helper\forestDateTime($o_glob->Trunk->DateTimeSqlFormat);
		$o_nowUser->SubDateInterval($o_DIUser->y, $o_DIUser->m, $o_DIUser->d, $o_DIUser->h, $o_DIUser->i, $o_DIUser->s);
		
		/* create select query to delete expired guest sessions */
		$o_querySelect = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::SELECT, 'sys_fphp_session');
			$o_uuid = new \fPHP\Base\forestSQLColumn($o_querySelect);
				$o_uuid->Column = 'UUID';
			
			$o_timestamp = new \fPHP\Base\forestSQLColumn($o_querySelect);
				$o_timestamp->Column = 'Timestamp';
			
			$o_userUUID = new \fPHP\Base\forestSQLColumn($o_querySelect);
				$o_userUUID->Column = 'UserUUID';
			
			$o_querySelect->Query->Columns->Add($o_uuid);
			$o_querySelect->Query->Columns->Add($o_timestamp);
			
			$o_whereUserUUID = new \fPHP\Base\forestSQLWhere($o_querySelect);
				$o_whereUserUUID->Column = $o_userUUID;
				$o_whereUserUUID->Value = $o_whereUserUUID->ParseValue($o_glob->Trunk->UUIDGuest);
				$o_whereUserUUID->Operator = '=';
			
			$o_whereTimestamp = new \fPHP\Base\forestSQLWhere($o_querySelect);
				$o_whereTimestamp->Column = $o_timestamp;
				$o_whereTimestamp->Value = $o_whereTimestamp->ParseValue($o_nowGuest->ToString());
				$o_whereTimestamp->Operator = '<';
				$o_whereTimestamp->FilterOperator = 'AND';
			
			$o_querySelect->Query->Where->Add($o_whereUserUUID);
			$o_querySelect->Query->Where->Add($o_whereTimestamp);
		
		$o_result = $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_querySelect);
		
		foreach($o_result->Twigs as $o_guest) {
			/* delete guest session record */
			$o_guest->DeleteRecord();
		}
		
		/* create select query to delete expired user sessions */
		$o_querySelect = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::SELECT, 'sys_fphp_session');
			$o_uuid = new \fPHP\Base\forestSQLColumn($o_querySelect);
				$o_uuid->Column = 'UUID';
			
			$o_timestamp = new \fPHP\Base\forestSQLColumn($o_querySelect);
				$o_timestamp->Column = 'Timestamp';
			
			$o_userUUID = new \fPHP\Base\forestSQLColumn($o_querySelect);
				$o_userUUID->Column = 'UserUUID';
			
			$o_querySelect->Query->Columns->Add($o_uuid);
			$o_querySelect->Query->Columns->Add($o_timestamp);
			
			$o_whereUserUUID = new \fPHP\Base\forestSQLWhere($o_querySelect);
				$o_whereUserUUID->Column = $o_userUUID;
				$o_whereUserUUID->Value = $o_whereUserUUID->ParseValue($o_glob->Trunk->UUIDGuest);
				$o_whereUserUUID->Operator = '<>';
			
			$o_whereTimestamp = new \fPHP\Base\forestSQLWhere($o_querySelect);
				$o_whereTimestamp->Column = $o_timestamp;
				$o_whereTimestamp->Value = $o_whereTimestamp->ParseValue($o_nowUser->ToString());
				$o_whereTimestamp->Operator = '<';
				$o_whereTimestamp->FilterOperator = 'AND';
			
			$o_querySelect->Query->Where->Add($o_whereUserUUID);
			$o_querySelect->Query->Where->Add($o_whereTimestamp);
			
		$o_result = $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_querySelect);
		
		foreach($o_result->Twigs as $o_user) {
			/* delete user session record */
			$o_user->DeleteRecord();
		}
	}
	
	/**
	 * generates uuid with php-function uniqid and '-'-delimiter
	 *
	 * @return string  uuid as string value
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
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
	
	/**
	 * generates random string with Hex-Value and length parameters
	 *
	 * @param integer $p_s_name  length of random string which will be generated
	 *
	 * @return string  random string with length between 1..32
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static no
	 */
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
	
	/**
	 * generates random hash string
	 *
	 * @return string  random hash string
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function GenRandomHash() {
		mt_srand((double)microtime()*73291);
		return md5((time() - 7 * mt_rand(1,9901)) * 9739 / 6581);
	}
	
	/**
	 * generates hash string with sha512-algorithm
	 *
	 * @return string  random hash string
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function HashString(&$p_s_value, $p_s_salt, $p_i_loop) {
		for ($i = 0; $i < $p_i_loop; $i++) {
			$p_s_value = hash('sha512', $p_s_salt . $p_s_value);
			$p_s_value = md5($p_s_value . $p_s_salt);
		}
	}
	
	/**
	 * generates random character for captcha image
	 *
	 * @param integer $p_i_strength  how many times a random character should be generated with mt_rand function
	 *
	 * @return char  random character
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
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