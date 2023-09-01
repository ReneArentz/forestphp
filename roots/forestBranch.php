<?php
/**
 * abstract class for all branches
 * core functionality for rendering and editing records which are managed in the twig object
 * sub record editing support as well
 * all functions can be overwritten for user specific use case
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestphp.de>
 * @copyright   (c) 2019 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     0.9.0 beta
 * @link        http://www.forestphp.de/
 * @object-id   0x1 00014
 * @since       File available since Release 0.1.0 alpha
 * @deprecated  -
 *
 * @version log Version		Developer	Date		Comment
 * 		0.1.0 alpha	renatus		2019-08-04	first build
 * 		0.1.1 alpha	renatus		2019-08-07	added view property and landing page function
 * 		0.1.2 alpha	renatus		2019-08-23	added list view and view functionalities + CRUD actions
 * 		0.1.3 alpha	renatus		2019-09-05	added formkey and validationrules
 * 		0.1.4 alpha	renatus		2019-09-20	added file upload and cleanup functionality
 * 		0.1.4 alpha	renatus		2019-09-23	added dropzone and richtext functionality
 * 		0.1.5 alpha	renatus		2019-10-02	added sort functionality
 * 		0.1.5 alpha	renatus		2019-10-04	added moveUp and moveDown functionality
 * 		0.1.5 alpha	renatus		2019-10-05	added Captcha and thumbnail functionality
 * 		0.1.5 alpha	renatus		2019-10-08	added forestLookup and forestCombination functionality
 * 		0.2.0 beta	renatus		2019-10-25	added forestRootBranch inheritance and activated RootMenu rendering
 * 		0.4.0 beta	renatus		2019-11-13	added login, logout and signIn functionality
 * 		0.4.0 beta	renatus		2019-11-18	added permission checks to all standard actions
 * 		0.5.0 beta	renatus		2019-11-17	added checkin action
 * 		0.5.0 beta	renatus		2019-11-28	added checkout action
 * 		0.5.0 beta	renatus		2019-12-02	added honeypot fields functionality
 * 		0.5.0 beta	renatus		2019-12-04	added verification of checked out elements on all standard actions
 * 		0.5.0 beta	renatus		2019-12-17	added info columns
 * 		0.5.0 beta	renatus		2019-12-18	added versioning
 * 		0.5.0 beta	renatus		2019-12-19	added restoreFile action
 * 		0.7.0 beta	renatus		2020-01-02	added identifier display and money-format
 * 		0.8.0 beta	renatus		2020-01-14	added fphp_flex functionality and actions
 * 		0.8.0 beta	renatus		2020-01-16	added log functionality
 * 		0.8.0 beta	renatus		2020-01-17	added account record creation on sign in action
 * 		0.9.0 beta	renatus		2020-01-28	transferred FilterForm from forestGlobals to forestBranch class
 * 		0.9.0 beta	renatus		2020-01-29	changes for bootstrap 4
 */

namespace fPHP\Branches;

use \fPHP\Roots\{forestString, forestList, forestNumericString, forestInt, forestFloat, forestBool, forestArray, forestObject, forestLookup};
use \fPHP\Roots\forestException as forestException;

abstract class forestBranch extends forestRootBranch {
	use \fPHP\Roots\forestData;

	/* Fields */
	
	const LIST = 'list';
	const DETAIL = 'detail';
	const FLEX = 'flex';
	
	protected $Twig;
	protected $NextAction;
	protected $Filter;
	protected $StandardView;
	protected $KeepFilter;
	protected $OriginalView;
	
	private $FilterForm;
	
	/* Properties */
	
	/* Methods */
	
	/* every class which inherits forestTwig must implement initBranch-function */
	abstract protected function initBranch();
	
	/* every class which inherits forestBranch must implement init-function */
	abstract protected function init();
	
	/**
	 * constructor of forestBranch class
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __construct() {
		$this->NextAction = new forestBool;
		$this->Filter = new forestBool;
		$this->KeepFilter = new forestBool;
		
		$this->FilterForm = new forestObject('forestForm');
		
		/* call initBranch method to set branch properties within forestBranch objects */
		$this->initBranch();
				
		if (!isset($this->StandardView)) {
			$this->StandardView = \fPHP\Branches\forestBranch::LIST;
		}
	
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$this->OriginalView = $this->StandardView;
		$o_glob->OriginalView = $this->OriginalView;
		
		if (!$o_glob->FastProcessing) {
			/* init navigation object */
			$o_glob->Navigation->InitNavigation();
			
			/* init root menu and store it to global variable */
			$o_glob->RootMenu = $this->RenderRootMenu();
			
			$i_lastBranchId = 0;
			$i_lastActionId = 0;
			
			/* get using branch and action-id out of session */
			if ($o_glob->Security->SessionData->Exists('lastBranchId')) {
				$i_lastBranchId = $o_glob->Security->SessionData->{'lastBranchId'};
			}
			
			if ($o_glob->Security->SessionData->Exists('lastActionId')) {
				$i_lastActionId = $o_glob->Security->SessionData->{'lastActionId'};
			}
			
			$o_glob->URL->LastBranchId = $i_lastBranchId;
			$o_glob->URL->LastActionId = $i_lastActionId;
			
			/* save filters from last request if you may use them in the new request */
			if ($o_glob->Security->SessionData->Exists('filter')) {
				$o_glob->Security->SessionData->Add($o_glob->Security->SessionData->{'filter'}, 'last_filter');
			} else {
				$o_glob->Security->SessionData->Del('last_filter');
			}
			
			/* if branch or action-id changes in the new request we delete old filter options in user's session */
			if (($o_glob->URL->BranchId != $i_lastBranchId) || ($o_glob->URL->ActionId != $i_lastActionId)) {
				$o_glob->Security->SessionData->Del('filter');
				
				if ($o_glob->URL->BranchId != $i_lastBranchId) {
					$o_glob->Security->SessionData->Del('last_filter');
				}
			}
			
			/* save used branch and action-id in session */
			$o_glob->Security->SessionData->Add($o_glob->URL->BranchId, 'lastBranchId');
			$o_glob->Security->SessionData->Add($o_glob->URL->ActionId, 'lastActionId');
			
			/* if available get twig of branch object */
			if (!isset($this->Twig)) {
				$s_foo = '\\fPHP\\Twigs\\' . $o_glob->URL->Branch . 'Twig';
				
				if (\fPHP\Roots\forestAutoLoad::IsReadable('./twigs/' . $s_foo . '.php')) {
					$this->Twig = new $s_foo;
				} else {
					$this->Twig = null;
					
					if (isset($this->Filter)) {
						$this->Filter->value = false;
					}
				}
			}
			
			/* check honeypot fields */
			$this->HandleHoneypotFields();
		}
		
		/* handle branch's action */
		$s_action = $o_glob->URL->Action;		
		
		if (!issetStr($s_action)) {
			$o_glob->URL->Action = 'init';
		}
		
		/* if filter-flag of branch object is true we init our global filter mask */
		if (isset($this->Filter)) {
			if ( ($this->Filter->value) && (!$o_glob->FastProcessing) ) {
				$this->InitFilter();
			}
		}
		
		do {
			$this->NextAction->value = false;
			
			/* if standard action is 'init' we do not need to attach .'Action' to it */
			$s_action = ($o_glob->URL->Action == 'init') ? $o_glob->URL->Action : $o_glob->URL->Action . 'Action';
			$this->$s_action();
			
			if (!$o_glob->FastProcessing) {
				global $b_transaction_active;
				if ($b_transaction_active) {
					$o_glob->Base->{$o_glob->ActiveBase}->ManualCommit();
				}
			}
			
			/* check if SortColumn of current branch has unique constraint in twig object for this column */
			if ( (!$o_glob->FastProcessing) && ($o_glob->URL->Action == 'init') && (isset($this->Twig)) ) {
				if (array_key_exists($this->Twig->fphp_TableUUID, $o_glob->TablesInformation)) {
					if (issetStr($o_glob->TablesInformation[$this->Twig->fphp_TableUUID]['SortColumn'])) {
						if ( ($o_sortColumn = $o_glob->GetTablefieldsDictionaryByUUID($o_glob->TablesInformation[$this->Twig->fphp_TableUUID]['SortColumn']) ) != null) {
							$b_found = false;
							
							/* check every unique constraints */
							foreach ($this->Twig->fphp_Unique as $s_unique_constraint) {
								/* ignore unique constraints which consists only of column Id or UUID */
								if ( ($s_unique_constraint == 'Id') || ($s_unique_constraint == 'UUID') ) {
									continue;
								}
								
								if ($s_unique_constraint == $o_sortColumn->FieldName) {
									$b_found = true;
								}
							}
							
							if (!$b_found) {
								$o_glob->SystemMessages->Add(new forestException(0x1000142A, array($o_sortColumn->FieldName)));
							}
						}
					}
				}
			}
			
			/* init global filter mask again, because we do not know how many actions are going to be handled in this do-while-loop */
			if (isset($this->Filter)) {
				if (($this->NextAction->value) && ($this->Filter->value) && (!$o_glob->FastProcessing)) {
					$this->InitFilter();
				}
			}
		} while ($this->NextAction->value);
		
		if (!$o_glob->FastProcessing) {
			/* clean up routine for temporary files */
			if ($o_glob->Security->RootUser) {
				$this->CleanUpTempFiles();
			}
		}
	}
	
	/**
	 * individual branch-classes are calling this method to set next action
	 *
	 * @param string $p_s_nextAction  name of action
	 * @param string $p_s_nextActionAfterReload  name of action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function SetNextAction($p_s_nextAction, $p_s_nextActionAfterReload = null) {
		if ($p_s_nextAction != null) {
			$o_glob = \fPHP\Roots\forestGlobals::init();
			$a_branchTree = $o_glob->BranchTree;
			
			if ($p_s_nextAction == 'RELOADBRANCH') {
				/* before we reload branch page, we need to commit all transactions */
				if (!$o_glob->FastProcessing) {
					global $b_transaction_active;
					if ($b_transaction_active) {
						$o_glob->Base->{$o_glob->ActiveBase}->ManualCommit();
					}
				}
				
				if ($p_s_nextActionAfterReload != null) {
					header('Location: '. \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, $p_s_nextActionAfterReload));
				} else {
					header('Location: '. \fPHP\Helper\forestLink::Link($o_glob->URL->Branch));
				}
			}
			
			/* check if action really exists */
			if (!array_key_exists($p_s_nextAction, $a_branchTree['Id'][$o_glob->URL->BranchId]['actions']['Name'])) {
				throw new forestException('Action[%0] with BranchId[%1] could not be found', array($p_s_nextAction, $o_glob->URL->BranchId));
			}
			
			$o_glob->URL->Action = $p_s_nextAction;
			$o_glob->URL->ActionId = $a_branchTree['Id'][$o_glob->URL->BranchId]['actions']['Name'][$p_s_nextAction];
			
			$this->NextAction->value = true;
		}
	}
	
	/**
	 * inits global filter mask for all branches and actions
	 *
	 * @return null
	 *
	 * @access protected
	 * @static no
	 */
	protected function InitFilter() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* if filter form has been used */
		if ($o_glob->IsPost) {
			/* set filter post action */
			if (array_key_exists('filterSubmit', $_POST)) {
				/* clear page option */
				$o_glob->Limit->Page = 0;
				
				$a_filter = array();
				
				/* get existing filter values out of session */
				if ($o_glob->Security->SessionData->Exists('filter')) {
					$a_filter = $o_glob->Security->SessionData->{'filter'};
				}
				
				/* handle delete action of filter terms */
				if (array_key_exists('deleteFilterColumn', $_POST)) {
					unset($a_filter[$_POST['deleteFilterColumn']]);
				}
				
				/* add new filter to our filter-array if the new line of our filter formular has been used */
				if ((array_key_exists('newFilterColumn', $_POST)) && (array_key_exists('newFilterValue', $_POST))) {
					if ((!empty($_POST['newFilterColumn'])) && (!empty($_POST['newFilterValue']))) {
						/* interpret user name to user uuid */
						if ( ($_POST['newFilterColumn'] == 'CreatedBy') || ($_POST['newFilterColumn'] == 'ModifiedBy') ) {
							foreach($o_glob->UsersDictionary as $s_userUUID => $s_user) {
								if (strtolower($s_user) == strtolower($_POST['newFilterValue'])) {
									$_POST['newFilterValue'] = $s_userUUID;
								}
							}
						}
						
						$a_filter[$_POST['newFilterColumn']] = $_POST['newFilterValue'];
					}
				}
				
				/* if current filter array is not empty, set the new filter values into session or clear filter values out of session */
				if (!empty($a_filter)) {
					$o_glob->Security->SessionData->Add($a_filter, 'filter');
				} else {
					$o_glob->Security->SessionData->Del('filter');
				}
			}
		}
		
		/* create form for filter */
		$this->FilterForm = new \fPHP\Forms\forestForm($this->Twig);
		
		$this->FilterForm->FormObject->Id = $o_glob->URL->Branch . $o_glob->URL->Action . $this->Twig->fphp_Table . 'FilterForm';
		$this->FilterForm->FormObject->Name = $this->FilterForm->FormObject->Id;
		$this->FilterForm->FormObject->Class = 'form-inline';
		
		$o_filterInput = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::TEXT);
		$o_filterInput->Id = 'newFilterValue';
		$o_filterInput->Class = 'form-control';
		$o_filterInput->Placeholder = $o_glob->GetTranslation('FilterInputPlaceholder', 1);
		
		$o_filterInputColumn = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
		$o_filterInputColumn->Id = 'newFilterColumn';
		$o_filterInputColumn->Value = '';
		
		$o_filterDeleteColumn = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
		$o_filterDeleteColumn->Id = 'deleteFilterColumn';
		$o_filterDeleteColumn->Value = '';
		
		$o_button = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::BUTTON);
		$o_button->Class = 'btn btn-light';
		$o_button->Id = 'filterSubmit';
		$o_button->ButtonText = '<span class="fas fa-search"></span>';
		$o_button->WrapSpanClass = 'input-group-btn';
		
		$this->FilterForm->FormElements->Add($o_filterInput);
		$this->FilterForm->FormElements->Add($o_filterInputColumn);
		$this->FilterForm->FormElements->Add($o_filterDeleteColumn);
		$this->FilterForm->FormElements->Add($o_button);
	}
	
	/**
	 * clean up temp files and file records
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function CleanUpTempFiles() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* delete old files in temp_files folder */
		$a_files = scandir('./temp_files/');
		
		/*echo '<pre>';
		print_r($a_files);
		echo '</pre>';*/
		
		foreach ($a_files as $o_file) {
			if ( ($o_file == '.') || ($o_file == '..') ) {
				continue;
			}
			
			$o_fileStat = stat('./temp_files/' . $o_file);
			
			if ($o_fileStat) {
				/* calculate last modification datetime of file */
				$o_fileDT = \fPHP\Helper\forestDateTime::UnixTimestampToDateTime($o_fileStat['mtime']);
				
				$o_DIDeleteFile = new \fPHP\Helper\forestDateInterval($o_glob->Trunk->TempFilesLifetime);
				$o_nowDT = new \fPHP\Helper\forestDateTime;
				$o_nowDT->subDateInterval($o_DIDeleteFile->y, $o_DIDeleteFile->m, $o_DIDeleteFile->d, $o_DIDeleteFile->h, $o_DIDeleteFile->i, $o_DIDeleteFile->s);
				
				if ($o_fileDT->DateTime < $o_nowDT->DateTime) {
					/* delete file */
					if (!@unlink('./temp_files/' . $o_file)) {
						throw new forestException(0x10001422, array('./temp_files/' . $o_file));
					}
				}
			}
		}
		
		/* delete old file records, where files do not exist anymore */
		$o_filesTwig = new \fPHP\Twigs\filesTwig; 
		
		$o_files = $o_filesTwig->GetAllRecords(true);
		
		foreach ($o_files->Twigs as $o_file) {
			$s_folder = substr(pathinfo($o_file->Name, PATHINFO_FILENAME), 6, 2);
				
			$o_glob->SetVirtualTarget($o_file->BranchId);
		
			/* generate path */
			$s_path = '';
			
			if (count($o_glob->URL->VirtualBranches) > 0) {
				foreach($o_glob->URL->VirtualBranches as $s_value) {
					$s_path .= $s_value . '/';
				}
			} else {
				$s_path .= $o_glob->URL->VirtualBranch . '/';
			}
			
			$s_path = './trunk/' . $s_path . 'fphp_files/' . $s_folder . '/';
			
			if (!file_exists($s_path . $o_file->Name)) {
				/* delete file record */
				$i_return = $o_file->DeleteRecord();
				
				/* evaluate the result */
				if ($i_return <= 0) {
					throw new forestException(0x10001423);
				}
			}
		}
	}
	
	/**
	 * handle form key functionality
	 *
	 * @param string $p_s_formId  id of form element
	 * @param bool $p_b_postChain  true - init form key again for multiple step forms, false - normal behavior
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function HandleFormKey($p_s_formId = 'NULL', $p_b_postChain = false) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		if ($o_glob->Trunk->FormKey) {
			/* delete old invalid form keys */
			$o_DIFormKey = new \fPHP\Helper\forestDateInterval($o_glob->Trunk->FormKeyInterval);
			$o_nowFormKey = new \fPHP\Helper\forestDateTime($o_glob->Trunk->DateTimeSqlFormat);
			$o_nowFormKey->SubDateInterval($o_DIFormKey->y, $o_DIFormKey->m, $o_DIFormKey->d, $o_DIFormKey->h, $o_DIFormKey->i, $o_DIFormKey->s);
			
			$o_querySelect = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::SELECT, 'sys_fphp_formkey');
					
				$o_column_A = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$o_column_A->Column = 'UUID';
				
				$o_column_B = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$o_column_B->Column = 'Timestamp';
				
			$o_querySelect->Query->Columns->Add($o_column_A);
			$o_querySelect->Query->Columns->Add($o_column_B);
				
				$o_where_A = new \fPHP\Base\forestSQLWhere($o_querySelect);
					$o_where_A->Column = $o_column_B;
					$o_where_A->Value = $o_where_A->ParseValue($o_nowFormKey->ToString());
					$o_where_A->Operator = '<=';
					
			$o_querySelect->Query->Where->Add($o_where_A);
			
			$o_formKeys = $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_querySelect);
			
			if ($o_formKeys->Twigs->Count() > 0) {
				foreach ($o_formKeys->Twigs as $o_formKey) {
					$o_formKey->DeleteRecord();
				}
			}
		
			if ( ($o_glob->IsPost) && (!$p_b_postChain) ) {
				/* check if form key is valid */
				$o_formkeyTwig = new \fPHP\Twigs\formkeyTwig;
				$s_formkey = 'NULL';
				
				if ($o_glob->Security->SessionData->Exists('formkey')) {
					$s_formkey = $o_glob->Security->SessionData->{'formkey'};
				}
				
				/* if we cannot find form key record or transmitted hash does not match */
				if ( (!$o_formkeyTwig->GetRecordPrimary(
						array(
							$s_formkey,
							$o_glob->Security->SessionUUID,
							$o_glob->URL->BranchId,
							$o_glob->URL->ActionId,
							$p_s_formId
						),
						array(
							'UUID',
							'SessionUUID',
							'BranchId',
							'ActionId',
							'FormId'
						)
					)
				) || (!password_verify($s_formkey, $_POST['sys_fphp_formkeyHash'])) ) {
					throw new forestException(0x10001429);
				} else {
					if (issetStr($o_glob->Trunk->FormKeyMinimumInterval)) {
						$o_DIFormKeyMinimumInterval = new \fPHP\Helper\forestDateInterval($o_glob->Trunk->FormKeyMinimumInterval);
						$o_formkeyTwig->Timestamp->AddDateInterval($o_DIFormKeyMinimumInterval->y, $o_DIFormKeyMinimumInterval->m, $o_DIFormKeyMinimumInterval->d, $o_DIFormKeyMinimumInterval->h, $o_DIFormKeyMinimumInterval->i, $o_DIFormKeyMinimumInterval->s);
						$o_now = new \fPHP\Helper\forestDateTime($o_glob->Trunk->DateTimeSqlFormat);
						
						/* if minimum time for a form has not expired */
						if ($o_now < $o_formkeyTwig->Timestamp) {
							throw new forestException(0x10001429);
						}
					}
					
					/* after validating the form key, it is obsolete and can be deleted */
					$o_formkeyTwig->DeleteRecord();
				}
			} else {
				/* init form key */
				
				/* delete current form key if form has been reloaded only without post */
				$o_formkeyTwig = new \fPHP\Twigs\formkeyTwig;
				$s_formkey = 'NULL';
				
				if ($o_glob->Security->SessionData->Exists('formkey')) {
					$s_formkey = $o_glob->Security->SessionData->{'formkey'};
				}
				
				if ($o_formkeyTwig->GetRecord(array($s_formkey))) {
					$o_formkeyTwig->DeleteRecord();
				}
				
				/* create new form key */
				$o_formkeyTwig = new \fPHP\Twigs\formkeyTwig;
				$o_formkeyTwig->SessionUUID = $o_glob->Security->SessionUUID;
				$o_formkeyTwig->Timestamp = new \fPHP\Helper\forestDateTime($o_glob->Trunk->DateTimeSqlFormat);
				$o_formkeyTwig->BranchId = $o_glob->URL->BranchId;
				$o_formkeyTwig->ActionId = $o_glob->URL->ActionId;
				$o_formkeyTwig->FormId = $p_s_formId;
				
				$o_formkeyTwig->InsertRecord();
				
				/* insert new form key into session */
				$o_glob->Security->SessionData->Add($o_formkeyTwig->UUID, 'formkey');
			}
		}
	}
	
	/**
	 * handle honeypot fields functionality
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function HandleHoneypotFields() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		if ( ($o_glob->IsPost) && ($o_glob->Trunk->HoneypotFields) ) {
			if ($o_glob->Security->SessionData->Exists('sys_fphp_honeypotfields')) {
				$s_honeypotFields = $o_glob->Security->SessionData->{'sys_fphp_honeypotfields'};
				$a_honeypotFields = explode(';', $s_honeypotFields);
				
				/* check each honeypot field */
				foreach ($a_honeypotFields as $s_honeypotField) {
					if (array_key_exists($s_honeypotField, $_POST)) {
						/* if honeypot field is not empty, a bot has entered some value */
						if (!empty($_POST[$s_honeypotField])) {
							/* access denied */
							throw new forestException(0x10000100);
						}
					}
				}
			}
		}
	}
	
	/**
	 * help function to create a new entry in the log
	 *
	 * @param string $p_s_message  message for log entry
	 * @param forestTwig $p_o_twig  record data which can be added to the log message
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function CreateLogEntry($p_s_message = null, \fPHP\Twigs\forestTwig $p_o_twig = null) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$a_newActions = array('new','signUp');
		$a_editActions = array('checkin','checkout','edit','editFlex','moveDown','moveUp','replaceFile','restoreFile','login','logout');
		$a_deleteActions = array('delete','truncateTwig');
		
		/* check if we activated trunk setting to log new actions */
		if ( (in_array($o_glob->URL->Action, $a_newActions)) && (!$o_glob->Trunk->LogNew) ) {
			return;
		}
		
		/* check if we activated trunk setting to log edit actions */
		if ( (in_array($o_glob->URL->Action, $a_editActions)) && (!$o_glob->Trunk->LogEdit) ) {
			return;
		}

		/* check if we activated trunk setting to log delete actions */
		if ( (in_array($o_glob->URL->Action, $a_deleteActions)) && (!$o_glob->Trunk->LogDelete) ) {
			return;
		}
		
		$o_logTwig = new \fPHP\Twigs\logTwig;
		
		$o_logTwig->Branch = $o_glob->URL->Branch;
		$o_logTwig->Action = $o_glob->URL->Action;
		$o_logTwig->Session = $o_glob->Security->SessionUUID;
		
		$s_message = '';
		
		if ($p_s_message != null) {
			$s_message .= $p_s_message . "\n";
		}
		
		if ( ($p_o_twig != null) && ($o_glob->Trunk->LogRecord) ) {
			$s_message .= $p_o_twig->ShowFields(false) . "\n";
		}
		
		$o_logTwig->Event = $s_message;
		
		$o_logTwig->Created = new \fPHP\Helper\forestDateTime;
		$o_logTwig->CreatedBy = $o_glob->Security->UserUUID;
		
		/* insert record */
		$i_result = $o_logTwig->InsertRecord();
	}
	
	
	/**
	 * generates landing page
	 *
	 * @return null
	 *
	 * @access protected
	 * @static no
	 */
	protected function GenerateLandingPage() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		$s_landingPageNavigation = $o_glob->Navigation->RenderLandingPage();
		
		/* use template to render landing page */
		$o_glob->Templates->Add(new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::LANDINGPAGE, array($s_landingPageNavigation)), $o_glob->URL->Branch . 'LandingPage');
	}
	
	
	/**
	 * handle login action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function loginAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		$s_nextAction = 'init';
		
		if ($o_glob->Security->UserUUID != $o_glob->Trunk->UUIDGuest->PrimaryValue) {
			throw new forestException(0x1000143E);
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'targetBranch'), 'targetBranch' );
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'targetAction'), 'targetAction' );
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'targetParametersKeys'), 'targetParametersKeys' );
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'targetParametersValues'), 'targetParametersValues' );
		
		if (!$o_glob->IsPost) {
			/* create modal form for login */
			$o_glob->PostModalForm = new \fPHP\Forms\forestForm(new \fPHP\Twigs\trunkTwig);
			$s_title = $o_glob->GetTranslation('LoginModalTitle', 1);
			$o_glob->PostModalForm->CreateModalForm(new \fPHP\Twigs\trunkTwig, $s_title);
			
			/* add username field */
			$o_username = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::TEXT);
			$o_username->Label = $o_glob->GetTranslation('formUsernameLabel');
			$o_username->Id = 'sys_fphp_login_Username';
			$o_username->Placeholder = $o_glob->GetTranslation('formUsernamePlaceholder');
			$o_username->ValMessage = $o_glob->GetTranslation('formUsernameValMessage');
			$o_username->Required = true;
			$o_glob->PostModalForm->FormElements->Add($o_username);
			
			/* add password field */
			$o_password = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::PASSWORD);
			$o_password->Label = $o_glob->GetTranslation('formPasswordLabel');
			$o_password->Id = 'sys_fphp_login_Password';
			$o_password->Placeholder = $o_glob->GetTranslation('formPasswordPlaceholder');
			$o_password->ValMessage = $o_glob->GetTranslation('formPasswordValMessage');
			$o_password->Required = true;
			$o_glob->PostModalForm->FormElements->Add($o_password);
			
			/* add validation rules for manual created form elements */
			$o_glob->PostModalForm->FormObject->ValRules->Add(new \fPHP\Forms\forestFormValidationRule('sys_fphp_login_Username', 'required', 'true'));
			$o_glob->PostModalForm->FormObject->ValRules->Add(new \fPHP\Forms\forestFormValidationRule('sys_fphp_login_Password', 'required', 'true'));
			
			/* change submit button text to Login */
			$o_submitElement = $o_glob->PostModalForm->GetFormElementByFormId('sys_fphp_SubmitStandard');
			
			if ($o_submitElement != null) {
				$o_submitElement->ButtonText = '<span class="fas fa-check"></span> Login';
			}
			
			/* add hidden target branch for automatic forwarding */
			if ( ($o_glob->Temp->Exists('targetBranch')) && ($o_glob->Temp->{'targetBranch'} != null) ) {
				$o_hiddenBranch = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
				$o_hiddenBranch->Id = 'sys_fphp_targetBranch';
				$o_hiddenBranch->Value = $o_glob->Temp->{'targetBranch'};
				$o_glob->PostModalForm->FormElements->Add($o_hiddenBranch);
			}
			
			/* add hidden target action for automatic forwarding */
			if ( ($o_glob->Temp->Exists('targetAction')) && ($o_glob->Temp->{'targetAction'} != null) ) {
				$o_hiddenAction = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
				$o_hiddenAction->Id = 'sys_fphp_targetAction';
				$o_hiddenAction->Value = $o_glob->Temp->{'targetAction'};
				$o_glob->PostModalForm->FormElements->Add($o_hiddenAction);
			}
			
			/* add hidden target parameters keys for automatic forwarding */
			if ( ($o_glob->Temp->Exists('targetParametersKeys')) && ($o_glob->Temp->{'targetParametersKeys'} != null) ) {
				$o_hiddenParametersKeys = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
				$o_hiddenParametersKeys->Id = 'sys_fphp_targetParametersKeys';
				$o_hiddenParametersKeys->Value = $o_glob->Temp->{'targetParametersKeys'};
				$o_glob->PostModalForm->FormElements->Add($o_hiddenParametersKeys);
			}
			
			/* add hidden target parameters values for automatic forwarding */
			if ( ($o_glob->Temp->Exists('targetParametersValues')) && ($o_glob->Temp->{'targetParametersValues'} != null) ) {
				$o_hiddenParametersValues = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
				$o_hiddenParametersValues->Id = 'sys_fphp_targetParametersValues';
				$o_hiddenParametersValues->Value = $o_glob->Temp->{'targetParametersValues'};
				$o_glob->PostModalForm->FormElements->Add($o_hiddenParametersValues);
			}
		} else {
			$o_userTwig = new \fPHP\Twigs\userTwig;
			
			if (!$o_userTwig->GetRecordPrimary(array($_POST['sys_fphp_login_Username']), array('User'))) {
				/* delay server answer, because of bot attacks */
				sleep(3);
				
				throw new forestException(0x1000142E);
			}
			
			if (!password_verify($_POST['sys_fphp_login_Password'], $o_userTwig->Password)) {
				if ($o_userTwig->UUID != $o_glob->Trunk->UUIDGuest) {
					/* only increase failed login counter if the user is not locked */
					if (!$o_userTwig->Locked) {
						$o_userTwig->FailLogin = $o_userTwig->FailLogin + 1;
						
						if ($o_userTwig->FailLogin >= $o_glob->Trunk->MaxLoginTrials) {
							$o_userTwig->Locked = true;
							$this->CreateLogEntry('lock user', $o_userTwig);
						} else {
							$this->CreateLogEntry('user failed login', $o_userTwig);
						}
						
						/* edit user recrod */
						$i_result = $o_userTwig->UpdateRecord();
						
						/* evaluate result */
						if ($i_result == -1) {
							throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
						}
						
						
					}
				}
				
				/* delay server answer, because of bot attacks */
				sleep(3);
				
				throw new forestException(0x1000142E);
			} else {
				/* check locked status */
				if ($o_userTwig->Locked) {
					$this->CreateLogEntry('user locked after login', $o_userTwig);
					throw new forestException(0x1000142F);
				}
				
				$o_userTwig->FailLogin = 0;
				
				/* edit user recrod */
				$i_result = $o_userTwig->UpdateRecord();
				
				/* evaluate result */
				if ($i_result == -1) {
					throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
				}
				
				/* update last login in account record */
				$o_accountTwig = new \fPHP\Twigs\accountTwig;
			
				if (!$o_accountTwig->GetRecord(array($o_userTwig->UUID))) {
					throw new forestException(0x10001402, array($o_accountTwig->fphp_Table));
				}
				
				$o_accountTwig->LastLogin = new \fPHP\Helper\forestDateTime;
				
				/* edit account recrod */
				$i_result = $o_accountTwig->UpdateRecord();
				
				/* evaluate result */
				if ($i_result == -1) {
					throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
				}
				
				/* set security state to user */
				$o_glob->Security->SessionData->Add(\fPHP\Security\forestSecurity::SessionStatusUser, 'session_status');
				$o_glob->Temp->Add($o_userTwig->UUID, 'fphp_UserUUID');
				$o_glob->Security->init();
				
				/* system message login successful */
				$o_glob->SystemMessages->Add(new forestException(0x10001430));
				
				$this->CreateLogEntry('user logged in', $o_userTwig);
				
				/* check if we have target elements for automatic forwarding */
				if (array_key_exists('sys_fphp_targetBranch', $_POST)) {
					$s_branch = $_POST['sys_fphp_targetBranch'];
					$s_action = null;
					$a_parameters = array();
					
					/* target action in post data */
					if (array_key_exists('sys_fphp_targetAction', $_POST)) {
						$s_action = $_POST['sys_fphp_targetAction'];
					}
					
					/* target parameter keys in post data */
					if (array_key_exists('sys_fphp_targetParametersKeys', $_POST)) {
						$a_parameterKeys = explode('~', $_POST['sys_fphp_targetParametersKeys']);
					}
					
					/* target parameter values in post data */
					if (array_key_exists('sys_fphp_targetParametersValues', $_POST)) {
						$a_parameterValues = explode('~', $_POST['sys_fphp_targetParametersValues']);
					}
					
					/* target parameter keys and values must have the same amount */
					if (count($a_parameterKeys) == count($a_parameterValues)) {
						for ($i = 0; $i < count($a_parameterKeys); $i++) {
							$a_parameters[$a_parameterKeys[$i]] = $a_parameterValues[$i];
						}
					}
					
					/* do automatic forwarding after successful login */
					header('Location: ' . \fPHP\Helper\forestLink::Link($s_branch, $s_action, $a_parameters));
					exit;
				} else {
					$s_nextAction = 'RELOADBRANCH';
				}
			}
		}
		
		if (isset($this->KeepFilter)) {
			if ($this->KeepFilter->value) {
				if ($o_glob->Security->SessionData->Exists('last_filter')) {
					$o_glob->Security->SessionData->Add($o_glob->Security->SessionData->{'last_filter'}, 'filter');
				}
			}
		}
		
		$this->SetNextAction($s_nextAction);
	}
	
	/**
	 * handle logout action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function logoutAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		$this->CreateLogEntry('user logged out: ' . $o_glob->GetUserNameByUUID($o_glob->Security->UserUUID));
		$o_glob->Security->Logout();
		
		header('Location: ./');
		exit();
	}
	
	/**
	 * handle sign up action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function signUpAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			/* create modal form for login */
			$o_glob->PostModalForm = new \fPHP\Forms\forestForm(new \fPHP\Twigs\trunkTwig);
			$s_title = $o_glob->GetTranslation('SignModalTitle', 1);
			$o_glob->PostModalForm->CreateModalForm(new \fPHP\Twigs\trunkTwig, $s_title);
			
			/* add username field */
			$o_username = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::TEXT);
			$o_username->Label = $o_glob->GetTranslation('formUsernameLabel');
			$o_username->Id = 'sys_fphp_signUp_Username';
			$o_username->Placeholder = $o_glob->GetTranslation('formUsernamePlaceholder');
			$o_username->ValMessage = $o_glob->GetTranslation('formUsernameValMessage');
			$o_username->Required = true;
			$o_username->Description = $o_glob->GetTranslation('formUsernameHint');
			$o_username->DescriptionClass = 'text-right text-info';
			$o_glob->PostModalForm->FormElements->Add($o_username);
			
			/* add email field */
			$o_username = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::TEXT);
			$o_username->Label = $o_glob->GetTranslation('formEmailLabel');
			$o_username->Id = 'sys_fphp_signUp_Email';
			$o_username->Placeholder = $o_glob->GetTranslation('formEmailPlaceholder');
			$o_username->ValMessage = $o_glob->GetTranslation('formEmailValMessage');
			$o_username->Required = true;
			$o_glob->PostModalForm->FormElements->Add($o_username);
			
			/* add password field */
			$o_password = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::PASSWORD);
			$o_password->Label = $o_glob->GetTranslation('formPasswordLabel');
			$o_password->Id = 'sys_fphp_signUp_Password';
			$o_password->Placeholder = $o_glob->GetTranslation('formPasswordPlaceholder');
			$o_password->ValMessage = $o_glob->GetTranslation('formPasswordValMessage');
			$o_password->Required = true;
			$o_password->Description = $o_glob->GetTranslation('formPasswordHint');
			$o_password->DescriptionClass = 'text-right text-info';
			$o_glob->PostModalForm->FormElements->Add($o_password);
			
			/* add password repeat field */
			$o_passwordRepeat = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::PASSWORD);
			$o_passwordRepeat->Label = $o_glob->GetTranslation('formPasswordRepeatLabel');
			$o_passwordRepeat->Id = 'sys_fphp_signUp_PasswordRepeat';
			$o_passwordRepeat->Placeholder = $o_glob->GetTranslation('formPasswordRepeatPlaceholder');
			$o_passwordRepeat->ValMessage = $o_glob->GetTranslation('formPasswordRepeatValMessage');
			$o_passwordRepeat->Required = true;
			$o_passwordRepeat->Description = $o_glob->GetTranslation('formPasswordHint');
			$o_passwordRepeat->DescriptionClass = 'text-right text-info';
			$o_glob->PostModalForm->FormElements->Add($o_passwordRepeat);
			
			/* query captcha form element */
			$o_formelementTwig = new \fPHP\Twigs\formelementTwig;
			
			if (!($o_formelementTwig->GetRecordPrimary(array(\fPHP\Forms\forestFormElement::CAPTCHA), array('Name')))) {
				throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
			}
			
			/* create captcha form element and adjust settings */
			$o_captcha = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::CAPTCHA);
			$o_captcha->loadJSON($o_formelementTwig->JSONEncodedSettings);
			$o_captcha->Id = 'sys_fphp_signUp_Captcha';
			$o_glob->PostModalForm->FormElements->Add($o_captcha);
			
			/* add validation rules for manual created form elements */
			$o_glob->PostModalForm->FormObject->ValRules->Add(new \fPHP\Forms\forestFormValidationRule('sys_fphp_signUp_Username', 'fphp_username', 'true'));
			$o_glob->PostModalForm->FormObject->ValRules->Add(new \fPHP\Forms\forestFormValidationRule('sys_fphp_signUp_Username', 'rangelength', '10', '36'));
			$o_glob->PostModalForm->FormObject->ValRules->Add(new \fPHP\Forms\forestFormValidationRule('sys_fphp_signUp_Email', 'email', 'true'));
			$o_glob->PostModalForm->FormObject->ValRules->Add(new \fPHP\Forms\forestFormValidationRule('sys_fphp_signUp_Password', 'fphp_password', 'true'));
			$o_glob->PostModalForm->FormObject->ValRules->Add(new \fPHP\Forms\forestFormValidationRule('sys_fphp_signUp_Password', 'minlength', '10'));
			$o_glob->PostModalForm->FormObject->ValRules->Add(new \fPHP\Forms\forestFormValidationRule('sys_fphp_signUp_PasswordRepeat', 'fphp_password', 'true'));
			$o_glob->PostModalForm->FormObject->ValRules->Add(new \fPHP\Forms\forestFormValidationRule('sys_fphp_signUp_PasswordRepeat', 'minlength', '10'));
			$o_glob->PostModalForm->FormObject->ValRules->Add(new \fPHP\Forms\forestFormValidationRule('sys_fphp_signUp_PasswordRepeat', 'equalTo', '#sys_fphp_signUp_Password'));
			$o_glob->PostModalForm->FormObject->ValRules->Add(new \fPHP\Forms\forestFormValidationRule('sys_fphp_signUp_Captcha', 'required', 'true'));
			
			/* change submit button text to Sign Up */
			$o_submitElement = $o_glob->PostModalForm->GetFormElementByFormId('sys_fphp_SubmitStandard');
			
			if ($o_submitElement != null) {
				$o_submitElement->ButtonText = '<span class="fas fa-check"></span> ' . $s_title;
			}
		} else {
			if (array_key_exists('sys_fphp_signUp_Captcha', $_POST)) {
				/* handle captcha */
				if (!array_key_exists('sys_fphp_signUp_Captcha_Hidden', $_POST)) {
					/* delay server answer, because of bot attacks */
					sleep(3);
					
					throw new forestException(0x10001420);
				}
				
				if (!password_verify($_POST['sys_fphp_signUp_Captcha'], $_POST['sys_fphp_signUp_Captcha_Hidden'])) {
					/* delay server answer, because of bot attacks */
					sleep(3);
					
					throw new forestException(0x10001421);
				}
				
				if ($o_glob->Security->SessionData->Exists('fphp_captcha')) {
					$o_glob->Security->SessionData->Del('fphp_captcha');
				}
				
				if ($o_glob->Security->SessionData->Exists('fphp_captcha_length')) {
					$o_glob->Security->SessionData->Del('fphp_captcha_length');
				}
			}
			
			$o_userTwig = new \fPHP\Twigs\userTwig;
			
			if ($o_userTwig->GetRecordPrimary(array($_POST['sys_fphp_signUp_Username']), array('User'))) {
				/* delay server answer, because of bot attacks */
				sleep(3);
				
				throw new forestException(0x10001431, array($_POST['sys_fphp_signUp_Username']));
			} else {
				if ($_POST['sys_fphp_signUp_Password'] != $_POST['sys_fphp_signUp_PasswordRepeat']) {
					/* delay server answer, because of bot attacks */
					sleep(3);
					
					throw new forestException(0x10001432);
				} else {
					$o_userTwig->User = strval($_POST['sys_fphp_signUp_Username']);
					$o_userTwig->Password = password_hash(strval($_POST['sys_fphp_signUp_PasswordRepeat']), PASSWORD_DEFAULT);
					$o_userTwig->Created = new \fPHP\Helper\forestDateTime;
					/* create user with status locked */
					$o_userTwig->Locked = true;
					
					/* insert user record */
					$i_result = $o_userTwig->InsertRecord();
					
					/* evaluate result */
					if ($i_result == -1) {
						$this->CreateLogEntry('user already exists', $o_userTwig);
						throw new forestException(0x10001431, array($_POST['sys_fphp_signUp_Username']));
					} else if ($i_result == 0) {
						$this->CreateLogEntry('could not create user', $o_userTwig);
						throw new forestException(0x10001402);
					}
					
					$this->CreateLogEntry('new user signed in', $o_userTwig);
					
					/* add user to standard usergroup */
					$o_usergroup_userTwig = new \fPHP\Twigs\usergroup_userTwig;
					$o_usergroup_userTwig->usergroupUUID = $o_glob->Trunk->UUIDUsergroup->PrimaryValue;
					$o_usergroup_userTwig->userUUID = $o_userTwig->UUID;
					
					/* insert membership record */
					$i_result = $o_usergroup_userTwig->InsertRecord(true);
					
					/* evaluate result */
					if ($i_result == -1) {
						throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
					} else if ($i_result == 0) {
						throw new forestException(0x10001402);
					}
					
					/* create account record */
					$o_accountTwig = new \fPHP\Twigs\accountTwig;
					$i_id = 1;
					
					/* get new account id */
					if ($o_accountTwig->GetLastRecord()) {
						$i_id = $o_accountTwig->Id + 1;
					}
					
					$o_accountTwig = new \fPHP\Twigs\accountTwig;
					$o_accountTwig->Id = $i_id;
					$o_accountTwig->UUID = $o_userTwig->UUID;
					$o_accountTwig->Mail = strval($_POST['sys_fphp_signUp_Email']);
					$o_accountTwig->LanguageCode = $o_glob->Trunk->LanguageCode->PrimaryValue;
					$o_accountTwig->Joined = new \fPHP\Helper\forestDateTime;
					
					/* insert user record */
					$i_result = $o_accountTwig->InsertRecord(true);
					
					/* evaluate result */
					if ($i_result == -1) {
						$this->CreateLogEntry('account already exists', $o_accountTwig);
						throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
					} else if ($i_result == 0) {
						$this->CreateLogEntry('could not create account', $o_accountTwig);
						throw new forestException(0x10001402);
					}
					
					/* system message sign up successful */
					$o_glob->SystemMessages->Add(new forestException(0x10001433));
				}
			}
		}
		
		if (isset($this->KeepFilter)) {
			if ($this->KeepFilter->value) {
				if ($o_glob->Security->SessionData->Exists('last_filter')) {
					$o_glob->Security->SessionData->Add($o_glob->Security->SessionData->{'last_filter'}, 'filter');
				}
			}
		}
		
		$this->SetNextAction('init');
	}
	
	
	/**
	 * generates list view
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function GenerateListView() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$o_records = $this->Twig->GetAllViewRecords();
		$o_glob->BackupLimit();
		
		$o_options = '<div class="btn-group d-none d-xl-flex">' . "\n" . '<div class="btn-group-lg">' . "\n";
		$o_options_dropdown = '<div class="dropdown d-xl-none dropright">' . "\n" . '<button class="btn btn-light btn-lg" type="button" data-toggle="dropdown"><span class="fas fa-ellipsis-h"></span></button>' . "\n" . '<div class="dropdown-menu btn-actions-collapse">';

		/* new link */
		$a_parameters = $o_glob->URL->Parameters;
		unset($a_parameters['newKey']);
		unset($a_parameters['viewKey']);
		unset($a_parameters['editKey']);
		unset($a_parameters['deleteKey']);
		unset($a_parameters['editSubKey']);
		unset($a_parameters['deleteSubKey']);
		unset($a_parameters['deleteFileKey']);
		unset($a_parameters['subConstraintKey']);
		
		if ($o_glob->Security->CheckUserPermission(null, 'new')) {
			$o_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'new', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnNewText', 1) . '"><span class="fas fa-plus text-success"></span></a>' . "\n";
			$o_options_dropdown .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'new', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnNewText', 1) . '"><span class="fas fa-plus text-success"></span></a>' . '<br>' . "\n";
		}

		/* edit link */
		$a_parameters = $o_glob->URL->Parameters;
		unset($a_parameters['newKey']);
		unset($a_parameters['viewKey']);
		unset($a_parameters['editKey']);
		unset($a_parameters['deleteKey']);
		unset($a_parameters['editSubKey']);
		unset($a_parameters['deleteSubKey']);
		unset($a_parameters['deleteFileKey']);
		unset($a_parameters['subConstraintKey']);
		$a_parameters['editKey'] = 'inserteditkey';
		
		if ($o_glob->Security->CheckUserPermission(null, 'edit')) {
			$o_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'edit', $a_parameters) . '" id="' . $o_glob->URL->Branch . $o_glob->URL->Action . $this->Twig->fphp_Table . 'Edit" class="btn btn-light a-button-edit-record disabled" title="' . $o_glob->GetTranslation('btnEditText', 1) . '"><span class="fas fa-edit"></span></a>' . "\n";
			$o_options_dropdown .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'edit', $a_parameters) . '" id="' . $o_glob->URL->Branch . $o_glob->URL->Action . $this->Twig->fphp_Table . 'Edit" class="btn btn-light a-button-edit-record disabled" title="' . $o_glob->GetTranslation('btnEditText', 1) . '"><span class="fas fa-edit"></span></a>' . '<br>' . "\n";
		}

		/* delete link */
		$a_parameters = $o_glob->URL->Parameters;
		unset($a_parameters['newKey']);
		unset($a_parameters['viewKey']);
		unset($a_parameters['editKey']);
		unset($a_parameters['deleteKey']);
		unset($a_parameters['editSubKey']);
		unset($a_parameters['deleteSubKey']);
		unset($a_parameters['deleteFileKey']);
		unset($a_parameters['subConstraintKey']);
		$a_parameters['deleteKey'] = 'insertdeletekey';
		
		if ($o_glob->Security->CheckUserPermission(null, 'delete')) {
			$o_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'delete', $a_parameters) . '" id="' . $o_glob->URL->Branch . $o_glob->URL->Action . $this->Twig->fphp_Table . 'Delete" class="btn btn-light a-button-delete-record disabled" title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="fas fa-trash text-danger"></span></a>' . "\n";
			$o_options_dropdown .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'delete', $a_parameters) . '" id="' . $o_glob->URL->Branch . $o_glob->URL->Action . $this->Twig->fphp_Table . 'Delete" class="btn btn-light a-button-delete-record disabled" title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="fas fa-trash text-danger"></span></a>' . '<br>' . "\n";
		}

		/* check if we have SortColumn set for current branch */
		if (array_key_exists($this->Twig->fphp_TableUUID, $o_glob->TablesInformation)) {
			if (issetStr($o_glob->TablesInformation[$this->Twig->fphp_TableUUID]['SortColumn']->PrimaryValue)) {
				/* move up link */
				$a_parameters = $o_glob->URL->Parameters;
				unset($a_parameters['newKey']);
				unset($a_parameters['viewKey']);
				unset($a_parameters['editKey']);
				unset($a_parameters['deleteKey']);
				unset($a_parameters['editSubKey']);
				unset($a_parameters['deleteSubKey']);
				unset($a_parameters['deleteFileKey']);
				unset($a_parameters['subConstraintKey']);
				$a_parameters['editKey'] = 'inserteditkey';
				
				if ($o_glob->Security->CheckUserPermission(null, 'moveUp')) {
					$o_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'moveUp', $a_parameters) . '" id="' . $o_glob->URL->Branch . $o_glob->URL->Action . $this->Twig->fphp_Table . 'MoveUp" class="btn btn-light a-button-moveUp-record disabled" title="' . $o_glob->GetTranslation('btnMoveUpText', 1) . '"><span class="fas fa-caret-up"></span></a>' . "\n";
					$o_options_dropdown .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'moveUp', $a_parameters) . '" id="' . $o_glob->URL->Branch . $o_glob->URL->Action . $this->Twig->fphp_Table . 'MoveUp" class="btn btn-light a-button-moveUp-record disabled" title="' . $o_glob->GetTranslation('btnMoveUpText', 1) . '"><span class="fas fa-caret-up"></span></a>' . '<br>' . "\n";
				}

				/* move down link */
				$a_parameters = $o_glob->URL->Parameters;
				unset($a_parameters['newKey']);
				unset($a_parameters['viewKey']);
				unset($a_parameters['editKey']);
				unset($a_parameters['deleteKey']);
				unset($a_parameters['editSubKey']);
				unset($a_parameters['deleteSubKey']);
				unset($a_parameters['deleteFileKey']);
				unset($a_parameters['subConstraintKey']);
				$a_parameters['editKey'] = 'inserteditkey';
				
				if ($o_glob->Security->CheckUserPermission(null, 'moveDown')) {
					$o_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'moveDown', $a_parameters) . '" id="' . $o_glob->URL->Branch . $o_glob->URL->Action . $this->Twig->fphp_Table . 'MoveDown" class="btn btn-light a-button-moveDown-record disabled" title="' . $o_glob->GetTranslation('btnMoveDownText', 1) . '"><span class="fas fa-caret-down"></span></a>' . "\n";
					$o_options_dropdown .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'moveDown', $a_parameters) . '" id="' . $o_glob->URL->Branch . $o_glob->URL->Action . $this->Twig->fphp_Table . 'MoveDown" class="btn btn-light a-button-moveDown-record disabled" title="' . $o_glob->GetTranslation('btnMoveDownText', 1) . '"><span class="fas fa-caret-down"></span></a>' . '<br>' . "\n";
				}
			}
		}
		
		/* check versioning settings of twig */
		if ($o_glob->TablesInformation[$this->Twig->fphp_TableUUID]['Versioning'] > 1) {
			/* checkout link */
			$a_parameters = $o_glob->URL->Parameters;
			unset($a_parameters['newKey']);
			unset($a_parameters['viewKey']);
			unset($a_parameters['editKey']);
			unset($a_parameters['deleteKey']);
			unset($a_parameters['editSubKey']);
			unset($a_parameters['deleteSubKey']);
			unset($a_parameters['deleteFileKey']);
			unset($a_parameters['subConstraintKey']);
			$a_parameters['editKey'] = 'insertcheckoutkey';
			
			if ($o_glob->Security->CheckUserPermission(null, 'checkout')) {
				$o_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'checkout', $a_parameters) . '" id="' . $o_glob->URL->Branch . $o_glob->URL->Action . $this->Twig->fphp_Table . 'Checkout" class="btn btn-light a-button-checkout-record disabled" title="' . $o_glob->GetTranslation('btnCheckoutText', 1) . '"><span class="fas fa-lock text-warning"></span></a>' . "\n";
				$o_options_dropdown .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'checkout', $a_parameters) . '" id="' . $o_glob->URL->Branch . $o_glob->URL->Action . $this->Twig->fphp_Table . 'Checkout" class="btn btn-light a-button-checkout-record disabled" title="' . $o_glob->GetTranslation('btnCheckoutText', 1) . '"><span class="fas fa-lock text-warning"></span></a>' . '<br>' . "\n";
			}
			
			/* checkin link */
			$a_parameters = $o_glob->URL->Parameters;
			unset($a_parameters['newKey']);
			unset($a_parameters['viewKey']);
			unset($a_parameters['editKey']);
			unset($a_parameters['deleteKey']);
			unset($a_parameters['editSubKey']);
			unset($a_parameters['deleteSubKey']);
			unset($a_parameters['deleteFileKey']);
			unset($a_parameters['subConstraintKey']);
			$a_parameters['editKey'] = 'insertcheckinkey';
			
			if ($o_glob->Security->CheckUserPermission(null, 'checkin')) {
				$o_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'checkin', $a_parameters) . '" id="' . $o_glob->URL->Branch . $o_glob->URL->Action . $this->Twig->fphp_Table . 'Checkin" class="btn btn-light a-button-checkin-record disabled" title="' . $o_glob->GetTranslation('btnCheckinText', 1) . '"><span class="fas fa-lock-open text-primary"></span></a>' . "\n";
				$o_options_dropdown .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'checkin', $a_parameters) . '" id="' . $o_glob->URL->Branch . $o_glob->URL->Action . $this->Twig->fphp_Table . 'Checkin" class="btn btn-light a-button-checkin-record disabled" title="' . $o_glob->GetTranslation('btnCheckinText', 1) . '"><span class="fas fa-lock-open text-primary"></span></a>' . '<br>' . "\n";
			}
		}
		
		/* view link */
		$a_parameters = $o_glob->URL->Parameters;
		unset($a_parameters['newKey']);
		unset($a_parameters['viewKey']);
		unset($a_parameters['editKey']);
		unset($a_parameters['deleteKey']);
		unset($a_parameters['editSubKey']);
		unset($a_parameters['deleteSubKey']);
		unset($a_parameters['deleteFileKey']);
		unset($a_parameters['subConstraintKey']);
		$a_parameters['viewKey'] = 'insertviewkey';
		
		if ($o_glob->Security->CheckUserPermission(null, 'view')) {
			$o_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'view', $a_parameters) . '" id="' . $o_glob->URL->Branch . $o_glob->URL->Action . $this->Twig->fphp_Table . 'View" class="btn btn-light a-button-view-record disabled" title="' . $o_glob->GetTranslation('btnViewText', 1) . '"><span class="fas fa-search-plus"></span></a>' . "\n";
			$o_options_dropdown .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'view', $a_parameters) . '" id="' . $o_glob->URL->Branch . $o_glob->URL->Action . $this->Twig->fphp_Table . 'View" class="btn btn-light a-button-view-record disabled" title="' . $o_glob->GetTranslation('btnViewText', 1) . '"><span class="fas fa-search-plus"></span></a>' . '<br>'. "\n";
		}

		/* details link */
		$a_parameters = $o_glob->URL->Parameters;
		unset($a_parameters['viewKey']);
		unset($a_parameters['editKey']);
		unset($a_parameters['deleteKey']);
		unset($a_parameters['editSubKey']);
		unset($a_parameters['deleteSubKey']);
		unset($a_parameters['deleteFileKey']);
		unset($a_parameters['subConstraintKey']);
		
		if ($o_glob->Security->CheckUserPermission(null, 'view')) {
			$o_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, (($this->OriginalView == \fPHP\Branches\forestBranch::LIST) ? 'view' : null), $a_parameters) . '" class="btn btn-light"><span class="fas fa-info-circle text-info" title="' . $o_glob->GetTranslation('btnDetailsText', 1) . '"></span></a>' . "\n";
			$o_options_dropdown .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, (($this->OriginalView == \fPHP\Branches\forestBranch::LIST) ? 'view' : null), $a_parameters) . '" class="btn btn-light"><span class="fas fa-info-circle text-info" title="' . $o_glob->GetTranslation('btnDetailsText', 1) . '"></span></a>' . '<br>' . "\n";
		}
		
		/* add columns link */
		$o_options .= '<a href="#" class="btn btn-light modal-call" data-modal-call="#' . $o_glob->URL->Branch . $o_glob->URL->Action . $this->Twig->fphp_Table . 'ListViewAddColumns" title="' . $o_glob->GetTranslation('btnAddColumnsText', 1) . '"><span class="fas fa-cog"></span></a>' . "\n";
		$o_options_dropdown .= '<a href="#" class="btn btn-light modal-call" data-modal-call="#' . $o_glob->URL->Branch . $o_glob->URL->Action . $this->Twig->fphp_Table . 'ListViewAddColumns" title="' . $o_glob->GetTranslation('btnAddColumnsText', 1) . '"><span class="fas fa-cog"></span></a>' . '<br>' . "\n";
		
		$o_options .= '</div>' . "\n" . '</div>' . "\n";
		$o_options_dropdown .= '</div>' . "\n" . '</div>' . "\n";
		
		$o_options = $o_options . $o_options_dropdown;

		$s_tableHead = '';
		
		/* render table head */
		/* if identifier is configured */
		if (issetStr($o_glob->TablesInformation[$this->Twig->fphp_TableUUID]['Identifier']->PrimaryValue)) {
			$s_tableHead .= '<th>' . $o_glob->GetSort('Identifier')->ToString($o_glob->GetTranslation('sortIdentifier')) . '</th>' . "\n";
		}
		
		$s_formElement = '';
		$s_forestData = '';
		
		foreach ($this->Twig->fphp_View as $s_columnHead) {
			if ($o_glob->TablefieldsDictionary->Exists($this->Twig->fphp_Table . '_' . $s_columnHead)) {
				$s_formElement = $o_glob->TablefieldsDictionary->{$this->Twig->fphp_Table . '_' . $s_columnHead}->FormElementName;
				$s_forestData = $o_glob->TablefieldsDictionary->{$this->Twig->fphp_Table . '_' . $s_columnHead}->ForestDataName;
			} else {
				if ( ($s_columnHead == 'Created') || ($s_columnHead == 'Modified') ) {
					$s_formElement = \fPHP\Forms\forestFormElement::DATETIMELOCAL;
					$s_forestData = 'forestObject(\'forestDateTime\')';
				} else if ( ($s_columnHead == 'CreatedBy') || ($s_columnHead == 'ModifiedBy') ) {
					$s_formElement = \fPHP\Forms\forestFormElement::TEXT;
					$s_forestData = 'forestString';
				}
			}
			
			if ( ($s_formElement == \fPHP\Forms\forestFormElement::FORM) || ($s_formElement == \fPHP\Forms\forestFormElement::PASSWORD) ||($s_formElement == \fPHP\Forms\forestFormElement::RICHTEXT) || ($s_formElement == \fPHP\Forms\forestFormElement::CAPTCHA) || ($s_formElement == \fPHP\Forms\forestFormElement::DROPZONE) ) {
				continue;
			}
			
			if ($s_forestData == 'forestCombination') {
				$s_tableHead .= '<th><div class="btn-group"><a href="#" class="btn btn-light">' . $o_glob->GetTranslation('sort' . $s_columnHead) . '</a></div></th>' . "\n";
			} else {
				$s_tableHead .= '<th>' . $o_glob->GetSort($s_columnHead)->ToString($o_glob->GetTranslation('sort' . $s_columnHead)) . '</th>' . "\n";
			}
		}
		
		$s_tableRows = '';
		
		/* render table rows */
		if ($o_records->Twigs->Count() > 0) {
			foreach ($o_records->Twigs as $o_record) {
				$s_tableRows .=  '<tr';
				
				if ($o_record->fphp_HasUUID) {
					if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_record->UUID), array('ForeignUUID'))) {
						$s_tableRows .= ' class="bg-warning"';
					}
					
					$s_tableRows .= ' data-fphp_uuid="' . $o_glob->URL->Branch . $o_glob->URL->Action . $this->Twig->fphp_Table . ';' . $o_record->UUID;
				}
				
				$s_tableRows .= '">' . "\n";
				
				/* if identifier is configured */
				if (issetStr($o_glob->TablesInformation[$this->Twig->fphp_TableUUID]['Identifier']->PrimaryValue)) {
					$s_tableRows .=  '<td><span>' . $o_record->Identifier . '</span></td>' . "\n";
				}
				
				foreach ($this->Twig->fphp_View as $s_column) {
					if ($o_glob->TablefieldsDictionary->Exists($this->Twig->fphp_Table . '_' . $s_column)) {
						$s_formElement = $o_glob->TablefieldsDictionary->{$this->Twig->fphp_Table . '_' . $s_column}->FormElementName;
						$s_forestData = $o_glob->TablefieldsDictionary->{$this->Twig->fphp_Table . '_' . $s_column}->ForestDataName;
					} else {
						if ( ($s_column == 'Created') || ($s_column == 'Modified') ) {
							$s_formElement = \fPHP\Forms\forestFormElement::DATETIMELOCAL;
							$s_forestData = 'forestObject(\'forestDateTime\')';
						} else if ( ($s_column == 'CreatedBy') || ($s_column == 'ModifiedBy') ) {
							$s_formElement = \fPHP\Forms\forestFormElement::TEXT;
							$s_forestData = 'forestString';
						}
					}
					
					if ( ($s_formElement == \fPHP\Forms\forestFormElement::FORM) || ($s_formElement == \fPHP\Forms\forestFormElement::PASSWORD) ||($s_formElement == \fPHP\Forms\forestFormElement::RICHTEXT) || ($s_formElement == \fPHP\Forms\forestFormElement::CAPTCHA) || ($s_formElement == \fPHP\Forms\forestFormElement::DROPZONE) ) {
						continue;
					}
					
					$s_value = '-';
					/* render value */
					$this->ListViewRenderColumnValue($s_value, $s_formElement, $s_column, $o_record, $this->Twig->fphp_Table . '_' . $s_column);
					
					$s_tableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
				}
					
				$s_tableRows .=  '</tr>' . "\n";
			}
		} else {
			/* no records found */
			$o_glob->SystemMessages->Add(new forestException(0x10001400));
		}
		
		/* create modal form for adding hidden columns */
		$o_addColumnsForm = new \fPHP\Forms\forestForm($this->Twig);
		
		$this->GenerateHiddenColumnsModal($o_addColumnsForm);
		
		/* create search form */
		$s_searchForm = '';
		$s_searchTerms = '';
		$this->RenderSearchForm($s_searchForm, $s_searchTerms);
		
		$o_glob->RestoreLimit();
		$o_glob->Security->SessionData->Add(\fPHP\Branches\forestBranch::LIST, 'lastView');
		
		$o_listViewOptionsTop = new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::LISTVIEWOPTIONSTOP, array( $o_options, strval($o_glob->Limit), $s_searchForm, $s_searchTerms, strval($o_addColumnsForm) ));
		$o_listViewOptionsDown = new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::LISTVIEWOPTIONSDOWN, array($o_options, strval($o_glob->Limit)));
		
		/* use template to render general list view */
		$o_glob->Templates->Add(new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::LISTVIEW, array($o_listViewOptionsTop, $s_tableHead, $o_glob->URL->Branch . $o_glob->URL->Action . $this->Twig->fphp_Table . 'ListView', $s_tableRows, $o_listViewOptionsDown)), $o_glob->URL->Branch . 'ListView');
	}
	
	/**
	 * render column value for list view
	 *
	 * @param string $p_s_value  value which should be set, passed by reference
	 * @param string $p_s_formElement  forestFormElement type
	 * @param string $p_s_column  name of column/field
	 * @param forestTwig $p_o_record  record of type forestTwig
	 * @param string $p_s_dictionaryKey  name of dictionary key, syntax: fulltablename_fieldname
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function ListViewRenderColumnValue(&$p_s_value, $p_s_formElement, $p_s_column, $p_o_record, $p_s_dictionaryKey) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		if ( ($p_s_formElement == \fPHP\Forms\forestFormElement::CHECKBOX) && ($o_glob->TablefieldsDictionary->{$p_s_dictionaryKey}->ForestDataName == 'forestBool') ) {
			/* render checkbox value as icons */
			if (boolval($p_o_record->{$p_s_column}) == true) {
				$p_s_value = '<span class="fas fa-check text-success"></span>';
			} else {
				$p_s_value = '<span class="fas fa-times text-danger"></span>';
			}
		} else if ($p_s_formElement == \fPHP\Forms\forestFormElement::WEEK) {
			/* render calender week value */
			if (issetStr($p_o_record->{$p_s_column})) {
				$s_year = substr($p_o_record->{$p_s_column}, 0, 4);
				
				$s_cw = substr($p_o_record->{$p_s_column}, (strlen($p_o_record->{$p_s_column}) - 2), 2);
				
				$p_s_value = $s_year . ' ' . $o_glob->GetTranslation('calenderWeek', 1) . $s_cw;
			}
		} else if ($p_s_formElement == \fPHP\Forms\forestFormElement::MONTH) {
			/* render month value */
			if (strval($p_o_record->{$p_s_column}) != 'NULL') {
				$p_s_value = $o_glob->GetTranslation('month' . $p_o_record->{$p_s_column}->ToString('F'), 1) . ' ' . $p_o_record->{$p_s_column}->ToString('Y');
			}
		} else if ($p_s_formElement == \fPHP\Forms\forestFormElement::TIME) {
			/* render time value */
			if (strval($p_o_record->{$p_s_column}) != 'NULL') {
				$p_s_value = $p_o_record->{$p_s_column}->ToString($o_glob->Trunk->TimeFormat);
			}
		} else if ($p_s_formElement == \fPHP\Forms\forestFormElement::DATE) {
			/* render date value */
			if (strval($p_o_record->{$p_s_column}) != 'NULL') {
				$p_s_value = $p_o_record->{$p_s_column}->ToString($o_glob->Trunk->DateFormat);
			}
		} else if ($p_s_formElement == \fPHP\Forms\forestFormElement::DATETIMELOCAL) {
			/* render datetime value */
			if (strval($p_o_record->{$p_s_column}) != 'NULL') {
				$p_s_value = $p_o_record->{$p_s_column}->ToString($o_glob->Trunk->DateTimeFormat);
			}
		} else if ($p_s_formElement == \fPHP\Forms\forestFormElement::URL) {
			/* render url value as usable link element */
			if (issetStr($p_o_record->{$p_s_column})) {
				$p_s_value = '<a href="' . $p_o_record->{$p_s_column} . '" target="_blank" title="' . $p_o_record->{$p_s_column} . '"><span class="fas fa-link"></span></a>';
			}
		} else if ($p_s_formElement == \fPHP\Forms\forestFormElement::COLOR) {
			/* render color value by showing a small rectangle with the color as background color */
			if (issetStr($p_o_record->{$p_s_column})) {
				$p_s_value = '<span style="padding:3px 10px; background-color: ' . $p_o_record->{$p_s_column} . ';"></span>';
			}
		} else if ( ($p_s_formElement == \fPHP\Forms\forestFormElement::RICHTEXT) /*&& (is_a($p_o_record, 'subrecordsTwig'))*/ ) {
			/* render richtext value, only for sub records */
			if (issetStr($p_o_record->{$p_s_column})) {
				$p_s_value = $p_o_record->{$p_s_column};
				$p_s_value = str_replace('&amp;', '&', $p_s_value);
				$p_s_value = str_replace('&gt;', '>', $p_s_value);
				$p_s_value = str_replace('&lt;', '<', $p_s_value);
				$p_s_value = str_replace('&aquota;', '"', $p_s_value);
				$p_s_value = htmlspecialchars_decode($p_s_value, ( ENT_QUOTES | ENT_HTML5 ));
			}
		} else if ($p_s_formElement == \fPHP\Forms\forestFormElement::RADIO) {
			/* render radio value by evaluating json settings */
			$o_tempFormElement = new \fPHP\Forms\forestFormElement($p_s_formElement);
			$o_tempFormElement->loadJSON($o_glob->TablefieldsDictionary->{$p_s_dictionaryKey}->JSONEncodedSettings);
			
			if (count($o_tempFormElement->Options) > 0) {
				$b_isAssoc = ( array_keys($o_tempFormElement->Options) !== range(0, count($o_tempFormElement->Options) - 1) );
				
				foreach ($o_tempFormElement->Options as $s_option_label => $s_option_value) {
					if ($p_o_record->{$p_s_column} == intval($s_option_value)) {
						$p_s_value = (($b_isAssoc) ? $s_option_label : $s_option_value);
					}
				}
			}
		} else if ($p_s_formElement == \fPHP\Forms\forestFormElement::FILE) {
			/* render file value */
			$o_filesTwig = new \fPHP\Twigs\filesTwig;
			
			if ($o_filesTwig->GetRecord(array($p_o_record->{$p_s_column}))) {
				$s_folder = substr(pathinfo($o_filesTwig->Name, PATHINFO_FILENAME), 6, 2);
				
				$s_path = '';

				if (count($o_glob->URL->Branches) > 0) {
					foreach($o_glob->URL->Branches as $s_branch) {
						$s_path .= $s_branch . '/';
					}
				}
				
				$s_path .= $o_glob->URL->Branch . '/';
				
				$s_path = './trunk/' . $s_path . 'fphp_files/' . $s_folder . '/';
				
				if (is_dir($s_path)) {
					if (file_exists($s_path . $o_filesTwig->Name)) {
						/* check if we have activated thumbnaiil option within json settings of file field */
						$b_thumbnail = false;
						$s_JSONEncodedSettings = str_replace('&quot;', '"', $o_glob->TablefieldsDictionary->{$p_s_dictionaryKey}->JSONEncodedSettings);
						$a_settings = json_decode($s_JSONEncodedSettings, true);
						
						if (array_key_exists('Accept', $a_settings)) {
							if ( (\fPHP\Helper\forestStringLib::StartsWith($a_settings['Accept'], 'image/')) || (\fPHP\Helper\forestStringLib::StartsWith($a_settings['Accept'], 'IMAGE/')) ) {
								if (array_key_exists('ShowImage', $a_settings)) {
									if ($a_settings['ShowImage'] == true) {
										if (array_key_exists('ImageMaxWidth', $a_settings)) {
											/* render file as thumbnail picture */
											$p_s_value = '<a href="' . $s_path . $o_filesTwig->Name . '" target="_blank" title="' . $o_filesTwig->DisplayName . '"><img src="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'fphp_imageThumbnail', array('fphp_thumbnail' => $o_filesTwig->UUID, 'fphp_thumbnail_width' => $a_settings['ImageMaxWidth'])) . '" alt="image could not be rendered" title="' . $o_filesTwig->DisplayName . '"></a>';
											$b_thumbnail = true;
										}
									}
								}
							}
						}
						
						if (!$b_thumbnail) {
							/* render file as downloadable link element */
							$p_s_value = '<a href="' . $s_path . $o_filesTwig->Name . '" target="_blank" title="' . $o_filesTwig->DisplayName . '" download="' . $o_filesTwig->DisplayName . '"><span class="fas fa-file"></span></a>';
						}
					}
				}
			}
		} else if ($p_s_formElement == \fPHP\Forms\forestFormElement::SELECT) {
			/* render select value by evaluating json settings, show as small labels */
			if (issetStr($p_o_record->{$p_s_column})) {
				$p_s_value = '';
				
				$o_tempFormElement = new \fPHP\Forms\forestFormElement($p_s_formElement);
				$o_tempFormElement->loadJSON($o_glob->TablefieldsDictionary->{$p_s_dictionaryKey}->JSONEncodedSettings);
				
				$a_valueOptions = array();

				if (strpos($p_o_record->{$p_s_column}, ';') !== false) {
					$a_valueOptions = explode(';', $p_o_record->{$p_s_column});
				}
				
				if (count($o_tempFormElement->Options) > 0) {
					$b_isAssoc = ( array_keys($o_tempFormElement->Options) !== range(0, count($o_tempFormElement->Options) - 1) );
					
					foreach ($o_tempFormElement->Options as $s_option_label => $s_option_value) {
						if (is_array($s_option_value)) {
							if (count($s_option_value) > 0) {
								$b_isAssocOpt = ( array_keys($s_option_value) !== range(0, count($s_option_value) - 1) );
								
								foreach ($s_option_value as $s_optgroup_option_label => $s_optgroup_option_value) {
									if (($p_o_record->{$p_s_column} == $s_optgroup_option_value) || (in_array($s_optgroup_option_value, $a_valueOptions))) {
										$p_s_value .= ' ';
										
										if (!empty($a_valueOptions)) {
											$p_s_value .= '<span class="badge badge-primary">';
										}
										
										$p_s_value .= (($b_isAssocOpt) ? $s_optgroup_option_label : $s_optgroup_option_value);
										
										if (!empty($a_valueOptions)) {
											$p_s_value .= '</span>';
										}
									}
								}
							}
						} else {
							if (($p_o_record->{$p_s_column} == $s_option_value) || (in_array($s_option_value, $a_valueOptions))) {
								$p_s_value .= ' ';
										
								if (!empty($a_valueOptions)) {
									$p_s_value .= '<span class="badge badge-primary">';
								}
								
								$p_s_value .= (($b_isAssoc) ? $s_option_label : $s_option_value);
								
								if (!empty($a_valueOptions)) {
									$p_s_value .= '</span>';
								}
							}
						}
					}
				}
			}
		} else if ( ($p_s_formElement == \fPHP\Forms\forestFormElement::CHECKBOX) && ($o_glob->TablefieldsDictionary->{$p_s_dictionaryKey}->ForestDataName == 'forestInt') ) {
			/* render checkbox value with multiple elements, show as small labels */
			$p_s_value = '';
				
			$o_tempFormElement = new \fPHP\Forms\forestFormElement($p_s_formElement);
			$o_tempFormElement->loadJSON($o_glob->TablefieldsDictionary->{$p_s_dictionaryKey}->JSONEncodedSettings);
			$i = 0;
			$b_hasValue = false;
			
			if (count($o_tempFormElement->Options) > 0) {
				$b_isAssoc = ( array_keys($o_tempFormElement->Options) !== range(0, count($o_tempFormElement->Options) - 1) );
				$i_results = substr_count(strval($p_o_record->{$p_s_column}), '1');
				
				foreach ($o_tempFormElement->Options as $s_option_label => $s_option_value) {
					if (strlen($p_o_record->{$p_s_column}) > $i) {
						if (strval($p_o_record->{$p_s_column})[( strlen($p_o_record->{$p_s_column}) - $i - 1 )] == '1') {
							$p_s_value .= ' ';
							
							if ($i_results > 1) {
								$p_s_value .= '<span class="badge badge-primary">';
							}
							
							$p_s_value .= (($b_isAssoc) ? $s_option_label : $s_option_value);
							
							if ($i_results > 1) {
								$p_s_value .= '</span>';
							}
							
							$b_hasValue = true;
						}
					}
					
					$i++;
				}
			}
			
			if (!$b_hasValue) {
				$p_s_value = '-';
			}
		} else if ( ($o_glob->TablefieldsDictionary->Exists($p_s_dictionaryKey)) && ($o_glob->TablefieldsDictionary->{$p_s_dictionaryKey}->ForestDataName == 'forestString') ) {
			/* render string value */
			if (issetStr($p_o_record->{$p_s_column})) {
				$p_s_value = $p_o_record->{$p_s_column};
			}
			
			/* render date interval */
			if ($o_glob->TablefieldsDictionary->Exists($p_s_dictionaryKey)) {
				$s_JSONEncodedSettings = str_replace('&quot;', '"', $o_glob->TablefieldsDictionary->{$p_s_dictionaryKey}->JSONEncodedSettings);
				$a_settings = json_decode($s_JSONEncodedSettings, true);
				
				if (!empty($a_settings)) {
					if (array_key_exists('DateIntervalFormat', $a_settings)) {
						/* check if we want to render value as date interval value */
						if ($a_settings['DateIntervalFormat']) {
							$p_s_value = strval(new \fPHP\Helper\forestDateInterval($p_s_value));
						}
					}
				}
			}
		} else if ( ($o_glob->TablefieldsDictionary->Exists($p_s_dictionaryKey)) && ($o_glob->TablefieldsDictionary->{$p_s_dictionaryKey}->ForestDataName == 'forestCombination') ) {
			/* render forestCombination value */
			$s_JSONEncodedSettings = str_replace('&quot;', '"', $o_glob->TablefieldsDictionary->{$p_s_dictionaryKey}->JSONEncodedSettings);
			$a_settings = json_decode($s_JSONEncodedSettings, true);
			
			if (!empty($a_settings)) {
				if (array_key_exists('forestCombination', $a_settings)) {
					$p_s_value = $p_o_record->CalculateCombination($a_settings['forestCombination']);
					
					/* check if we want to render result as money value */
					if (array_key_exists('MoneyFormat', $a_settings)) {
						if ($a_settings['MoneyFormat']) {
							$p_s_value = \fPHP\Helper\forestStringLib::money_format('%i', $p_s_value);
						}
					}
					/* check if we want to render result as date interval value */
					else if (array_key_exists('DateIntervalFormat', $a_settings)) {
						if ($a_settings['DateIntervalFormat']) {
							$p_s_value = strval(new \fPHP\Helper\forestDateInterval($p_s_value));
						}
					}
				}
			}
		} else if ( ($o_glob->TablefieldsDictionary->Exists($p_s_dictionaryKey)) && ( ($o_glob->TablefieldsDictionary->{$p_s_dictionaryKey}->SqlTypeName == 'decimal') || ($o_glob->TablefieldsDictionary->{$p_s_dictionaryKey}->SqlTypeName == 'double') ) ) {
			/* render double and decimal value */
			$p_s_value = $p_o_record->{$p_s_column};
			
			$s_JSONEncodedSettings = str_replace('&quot;', '"', $o_glob->TablefieldsDictionary->{$p_s_dictionaryKey}->JSONEncodedSettings);
			$a_settings = json_decode($s_JSONEncodedSettings, true);
			
			if (!empty($a_settings)) {
				if (array_key_exists('MoneyFormat', $a_settings)) {
					/* check if we want to render value as money value */
					if ($a_settings['MoneyFormat']) {
						$p_s_value = \fPHP\Helper\forestStringLib::money_format('%i', $p_o_record->{$p_s_column});
						
						if (floatval($p_o_record->{$p_s_column}) < 0.0) {
							$p_s_value = '<span style="color: red;">' . $p_s_value . '</span>';
						}
					}
				}
			}
		} else {
			/* render forestLookup value */
			if ( ($o_glob->TablefieldsDictionary->Exists($p_s_dictionaryKey)) && ($o_glob->TablefieldsDictionary->{$p_s_dictionaryKey}->ForestDataName == 'forestLookup') ) {
				if (strval($p_o_record->{$p_s_column}) == 'table_not_found') {
					$s_primaryValue = $p_o_record->{$p_s_column}->PrimaryValue;
					
					$s_JSONEncodedSettings = str_replace('&quot;', '"', $o_glob->TablefieldsDictionary->{$p_s_dictionaryKey}->JSONEncodedSettings);
					$a_settings = json_decode($s_JSONEncodedSettings, true);
					$a_forestLookupDataFilter = array();
					$s_forestLookupDataConcat = ' - ';
					
					if (array_key_exists('forestLookupDataFilter', $a_settings)) {
						$a_forestLookupDataFilter = $a_settings['forestLookupDataFilter'];
					}
					
					if (array_key_exists('forestLookupDataConcat', $a_settings)) {
						$s_forestLookupDataConcat = $a_settings['forestLookupDataConcat'];
					}
					
					$o_forestLookupData = new \fPHP\Helper\forestLookupData($a_settings['forestLookupDataTable'], $a_settings['forestLookupDataPrimary'], $a_settings['forestLookupDataLabel'], $a_forestLookupDataFilter, $s_forestLookupDataConcat);
					$p_o_record->{$p_s_column}->SetLookupData($o_forestLookupData);
					$p_o_record->{$p_s_column} = $s_primaryValue;
				}
			}
			
			/* render created by and modified by columns as datetime values */
			if ( ($p_s_column == 'CreatedBy') || ($p_s_column == 'ModifiedBy') ) {
				if (issetStr($p_o_record->{$p_s_column})) {
					$p_s_value = $o_glob->GetUserNameByUUID($p_o_record->{$p_s_column});
				}
			} else {
				/* standard rendering value */
				if (issetStr($p_o_record->{$p_s_column})) {
					$p_s_value = $p_o_record->{$p_s_column};
				}
			}
		}
	}
	
	/**
	 * create modal form for selecting hidden columns
	 *
	 * @param forestForm $p_o_form  form object where form elements will be added to add additonal columns to the view
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function GenerateHiddenColumnsModal(\fPHP\Forms\forestForm $p_o_form) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* get table record */
		$o_tableTwig = new \fPHP\Twigs\tableTwig;
		
		/* query table record */
		if (!($o_tableTwig->GetRecordPrimary(array($this->Twig->fphp_Table), array('Name')))) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$o_formelementTwig = new \fPHP\Twigs\formelementTwig;
		
		/* look in tablefields for formobject, if not get the standard by formelementuuid */
		if (!($o_formelementTwig->GetRecordPrimary(array('form'), array('Name')))) {
			throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
		}
		
		$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
		$s_formObjectJSONsettings = '';
		
		if (!($o_tablefieldTwig->GetRecordPrimary(array($o_tableTwig->UUID, $o_formelementTwig->UUID), array('TableUUID', 'FormElementUUID')))) {
			/* no tablefield for table, take standard */
			$s_formObjectJSONsettings = $o_formelementTwig->JSONEncodedSettings;
		} else {
			$s_formObjectJSONsettings = $o_tablefieldTwig->JSONEncodedSettings;
		}
		
		/* create formobject(check $this->Readonly->value), modal object and maybe tab objects */
		$p_o_form->FormObject->loadJSON($s_formObjectJSONsettings);
		$p_o_form->FormModalConfiguration->loadJSON($s_formObjectJSONsettings);
		$p_o_form->FormTabConfiguration->loadJSON($s_formObjectJSONsettings);
		
		/* because it switches back to detail view when original is detail */
		if ($this->OriginalView != $this->StandardView) {
			$s_action = 'view';
		} else {
			$s_action = $o_glob->URL->Action;
		}
		
		$p_o_form->FormObject->Id = $o_glob->URL->Branch . $o_glob->URL->Action . $this->Twig->fphp_Table . 'ListViewAddColumnsForm';
		$p_o_form->FormObject->Name = $p_o_form->FormObject->Id;
		
		/* adjust form action link */
		$a_parameters = $o_glob->URL->Parameters;
		unset($a_parameters['viewKey']);
		unset($a_parameters['editKey']);
		unset($a_parameters['deleteKey']);
		$a_parameters['-hiddencolumns'] = '-hiddencolumnsval';
		$p_o_form->FormObject->Action = \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, $s_action, $a_parameters);
		
		/* adjust modal id and title */
		$p_o_form->FormModalConfiguration->ModalId = $o_glob->URL->Branch . $o_glob->URL->Action . $this->Twig->fphp_Table . 'ListViewAddColumns';
		$p_o_form->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('AddColumnsModalTitle', 1);
		$p_o_form->FormTabConfiguration->Tab = false;
		
		/* create select form element for choosing hidden columns */
		$o_select = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::SELECT);
		$o_select->Label = $o_glob->GetTranslation('AddColumnsLabel', 1);
		$o_select->Id = 'selectHiddenColumns';
		$o_select->Class = 'form-control select-modal-call-add-column';
		$o_select->Size = 8;
		$o_select->Multiple = true;
		$o_select->Data = 'columns=""';
		$o_select->ValMessage = $o_glob->GetTranslation('AddColumnsValMessage', 1);
		
		$a_hidden_columns = array();
		
		/* get hidden columns */
		$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $this->Twig->fphp_TableUUID, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$o_tablefields = $o_tablefieldTwig->GetAllRecords(true);
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		foreach ($o_tablefields->Twigs as $o_tablefield) {
			$s_column = $o_tablefield->FieldName;
			
			/* skip standard Id + UUID column, and all columns which are already shown */
			if ( ($s_column != 'Id') && ($s_column != 'UUID') && (!in_array($s_column, $this->Twig->fphp_View)) ) {
				/* skip fields which have no tablefield setting in database */
				if (!$o_glob->TablefieldsDictionary->Exists($this->Twig->fphp_Table . '_' . $s_column)) {
					continue;
				}
				
				$s_formElement = $o_glob->TablefieldsDictionary->{$this->Twig->fphp_Table . '_' . $s_column}->FormElementName;
				$s_forestData = $o_glob->TablefieldsDictionary->{$this->Twig->fphp_Table . '_' . $s_column}->ForestDataName;
				
				/* skip other tablefields which have no use in a list view */
				if ( ($s_formElement == \fPHP\Forms\forestFormElement::FORM) || ($s_formElement == \fPHP\Forms\forestFormElement::PASSWORD) ||($s_formElement == \fPHP\Forms\forestFormElement::RICHTEXT) || ($s_formElement == \fPHP\Forms\forestFormElement::CAPTCHA) || ($s_formElement == \fPHP\Forms\forestFormElement::DROPZONE) ) {
					continue;
				}
				
				$a_hidden_columns[$o_glob->GetTranslation('sort' . $s_column)] = $s_column;
			}
		}
		
		/* get values for info columns when configured */
		$i_infoColumns = $o_glob->TablesInformation[$this->Twig->fphp_TableUUID]['InfoColumns'];
		
		if ($i_infoColumns == 10) {
			if (!in_array('Created', $this->Twig->fphp_View)) {
				$a_hidden_columns[$o_glob->GetTranslation('sortCreated')] = 'Created';
			}
			
			if (!in_array('CreatedBy', $this->Twig->fphp_View)) {
				$a_hidden_columns[$o_glob->GetTranslation('sortCreatedBy')] = 'CreatedBy';
			}
		} else if ($i_infoColumns == 100) {
			if (!in_array('Modified', $this->Twig->fphp_View)) {
				$a_hidden_columns[$o_glob->GetTranslation('sortModified')] = 'Modified';
			}
			
			if (!in_array('ModifiedBy', $this->Twig->fphp_View)) {
				$a_hidden_columns[$o_glob->GetTranslation('sortModifiedBy')] = 'ModifiedBy';
			}
		} else if ($i_infoColumns == 1000) {
			if (!in_array('Created', $this->Twig->fphp_View)) {
				$a_hidden_columns[$o_glob->GetTranslation('sortCreated')] = 'Created';
			}
			
			if (!in_array('CreatedBy', $this->Twig->fphp_View)) {
				$a_hidden_columns[$o_glob->GetTranslation('sortCreatedBy')] = 'CreatedBy';
			}
			
			if (!in_array('Modified', $this->Twig->fphp_View)) {
				$a_hidden_columns[$o_glob->GetTranslation('sortModified')] = 'Modified';
			}
			
			if (!in_array('ModifiedBy', $this->Twig->fphp_View)) {
				$a_hidden_columns[$o_glob->GetTranslation('sortModifiedBy')] = 'ModifiedBy';
			}
		}
		
		$o_select->Options = $a_hidden_columns;
		
		$o_description = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::DESCRIPTION);
		$o_description->Description = $o_glob->GetTranslation('AddColumnsDescription', 1);
		
		if (count($a_hidden_columns) > 0) {
			$p_o_form->FormElements->Add($o_select);
		} else {
			$p_o_form->FormElements->Add($o_description);
		}
		
		/* render submit button */
		if (count($a_hidden_columns) > 0) {
			if (!($o_formelementTwig->GetRecordPrimary(array('submit'), array('Name')))) {
				throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
			}
			
			$o_button = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::BUTTON);
			$o_button->loadJSON($o_formelementTwig->JSONEncodedSettings);
			$o_button->Class = 'btn btn-success btn-default pull-right button-modal-call-add-column';
			$o_button->Data = 'form_id="' . $p_o_form->FormObject->Id . '"';
			$o_button->ButtonText = htmlspecialchars_decode($o_button->ButtonText);
			$p_o_form->FormFooterElements->Add($o_button);
		}
		
		/* render cancel button */
		if (!($o_formelementTwig->GetRecordPrimary(array('cancel'), array('Name')))) {
			throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
		}
		
		$o_cancel = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::BUTTON);
		$o_cancel->loadJSON($o_formelementTwig->JSONEncodedSettings);
		$o_cancel->ButtonText = htmlspecialchars_decode($o_cancel->ButtonText);
		$p_o_form->FormFooterElements->Add($o_cancel);
	}
	
	/**
	 * render search form
	 *
	 * @param string $p_s_searchForm  form object of the search form
	 * @param string $p_s_searchTerms  string container to list all search terms as badges
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function RenderSearchForm(&$p_s_searchForm, &$p_s_searchTerms) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		if (isset($this->Filter)) {
			if ($this->Filter->value) {
				$p_s_searchForm .= strval($this->FilterForm->FormObject);
				
				$p_s_searchForm .= '
				<div class="input-group">
					<div class="input-group-btn filter-panel dropright">
						<button type="button" class="btn btn-light dropdown-toggle" data-toggle="dropdown">
							<span id="filterDropDownButton">' . $o_glob->GetTranslation('FilterDropDownText', 1) . '</span> <span class="caret"></span>
						</button>
						<div class="dropdown-menu">';
							
							$b_columns = false;
							
							/* if identifier is configured */
							if (issetStr($o_glob->TablesInformation[$this->Twig->fphp_TableUUID]['Identifier']->PrimaryValue)) {
								$p_s_searchForm .= '<a href="#Identifier" class="dropdown-item">' . $o_glob->GetTranslation('sortIdentifier') . '</a>';
								$b_columns = true;
							}
							
							foreach ($this->Twig->fphp_Mapping as $s_column) {
								/* skip fields which are already used within filter */
								if ($o_glob->Security->SessionData->Exists('filter')) {
									if ( (in_array($s_column, array_keys($o_glob->Security->SessionData->{'filter'}))) || (in_array('FilterAllColumns', array_keys($o_glob->Security->SessionData->{'filter'}))) ) {
										continue;
									}
								}
								
								/* skip standard Id + UUID column */
								if ( ($s_column != 'Id') && ($s_column != 'UUID') ) {
									/* add only tablefields which have a configuration in the database */
									if ($o_glob->TablefieldsDictionary->Exists($this->Twig->fphp_Table . '_' . $s_column)) {
										/* skip forestLookup and Password fields */
										if ( ($o_glob->TablefieldsDictionary->{$this->Twig->fphp_Table . '_' . $s_column}->ForestDataName != 'forestLookup') && ($o_glob->TablefieldsDictionary->{$this->Twig->fphp_Table . '_' . $s_column}->FormElementName != \fPHP\Forms\forestFormElement::PASSWORD) ) {
											$p_s_searchForm .= '<a href="#' . $s_column . '" class="dropdown-item">' . $o_glob->GetTranslation('sort' . $s_column) . '</a>';
											$b_columns = true;
										}
									}
								}
							}
							
							/* if info columns are configured */
							$i_infoColumns = $o_glob->TablesInformation[$this->Twig->fphp_TableUUID]['InfoColumns'];
			
							if ($i_infoColumns == 10) {
								$p_s_searchForm .= '<a href="#Created" class="dropdown-item">' . $o_glob->GetTranslation('sortCreated') . '</a>';
								$p_s_searchForm .= '<a href="#CreatedBy" class="dropdown-item">' . $o_glob->GetTranslation('sortCreatedBy') . '</a>';
								$b_columns = true;
							} else if ($i_infoColumns == 100) {
								$p_s_searchForm .= '<a href="#Modified" class="dropdown-item">' . $o_glob->GetTranslation('sortModified') . '</a>';
								$p_s_searchForm .= '<a href="#ModifiedBy" class="dropdown-item">' . $o_glob->GetTranslation('sortModifiedBy') . '</a>';
								$b_columns = true;
							} else if ($i_infoColumns == 1000) {
								$p_s_searchForm .= '<a href="#Created" class="dropdown-item">' . $o_glob->GetTranslation('sortCreated') . '</a>';
								$p_s_searchForm .= '<a href="#CreatedBy" class="dropdown-item">' . $o_glob->GetTranslation('sortCreatedBy') . '</a>';
								$p_s_searchForm .= '<a href="#Modified" class="dropdown-item">' . $o_glob->GetTranslation('sortModified') . '</a>';
								$p_s_searchForm .= '<a href="#ModifiedBy" class="dropdown-item">' . $o_glob->GetTranslation('sortModifiedBy') . '</a>';
								$b_columns = true;
							}
							
							$b_all = true;
							
							/* if we are using at least one column for filtering, we cannot use option to filter "all columns" anymore */
							if ($o_glob->Security->SessionData->Exists('filter')) {
								if (count($o_glob->Security->SessionData->{'filter'}) > 0) {
									$b_all = false;
								}
							}
							
							if ($b_all) {
								$p_s_searchForm .= '<div class="dropdown-divider"></div>
								<a href="#FilterAllColumns" class="dropdown-item">' . $o_glob->GetTranslation('FilterAllColumnsText', 1) . '</a>';
							}
							
							/* if we have no columns, add disabled option that all columns are in use right now */
							if (!$b_columns) {
								$p_s_searchForm .= '<a href="#" class="dropdown-item disabled">' . $o_glob->GetTranslation('FilterAllColumnsUsedText', 1) . '</a>';
							}
							
						$p_s_searchForm .= '</div>
					</div>' . "\n";
				
				foreach ($this->FilterForm->FormElements as $o_formElement) {
					$p_s_searchForm .= strval($o_formElement);
				}
				
				$p_s_searchForm .= '</div>' . "\n";
				$p_s_searchForm .= '</form>' . "\n";
				
				/* render currently used filter values and show them as labels next to search form */
				if ($o_glob->Security->SessionData->Exists('filter')) {
					foreach ($o_glob->Security->SessionData->{'filter'} as $s_filterColumn => $s_filterValue) {
						if ($o_glob->TablefieldsDictionary->Exists($this->Twig->fphp_Table . '_' . $s_filterColumn)) {
							if ($o_glob->TablefieldsDictionary->{$this->Twig->fphp_Table . '_' . $s_filterColumn}->FormElementName == \fPHP\Forms\forestFormElement::PASSWORD) {
								continue;
							}
						}
						
						/* translate user uuid to user name */
						if ( ($s_filterColumn == 'CreatedBy') || ($s_filterColumn == 'ModifiedBy') ) {
							$s_filterValue = $o_glob->GetUserNameByUUID($s_filterValue);
						}
						
						$s_displayFilterColumn = $o_glob->GetTranslation('sort' . $s_filterColumn);
						$s_filterValue = htmlspecialchars($s_filterValue, ( ENT_QUOTES | ENT_HTML5 ));
						$p_s_searchTerms .= '<a href="#' . $s_filterColumn . '"><button type="button" class="btn btn-primary"><span class="fas fa-times text-dark"></span> ' . (($s_filterColumn == 'FilterAllColumns') ? '<span class="fas fa-asterisk"></span> ' : $s_displayFilterColumn) . ': ' . $s_filterValue . '</button></a>' . "\n";
					}
				}
			}
		}
	}
	
	
	/**
	 * generates flex view
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function GenerateFlexView() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$o_options = '<div class="btn-group d-none d-xl-flex">' . "\n" . '<div class="btn-group-lg">' . "\n";
		$o_options_dropdown = '<div class="dropdown d-xl-none dropright">' . "\n" . '<button class="btn btn-light btn-lg" type="button" data-toggle="dropdown"><span class="fas fa-ellipsis-h"></span></button>' . "\n" . '<div class="dropdown-menu btn-actions-collapse">';

		/* list link */
		if ($o_glob->Security->CheckUserPermission(null, 'view')) {
			$o_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, (($this->OriginalView == \fPHP\Branches\forestBranch::LIST) ? null : 'view')) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnListText', 1) . '"><span class="fas fa-th-list text-info"></span></a>' . "\n";
			$o_options_dropdown .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, (($this->OriginalView == \fPHP\Branches\forestBranch::LIST) ? null : 'view')) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnListText', 1) . '"><span class="fas fa-th-list text-info"></span></a>' . "\n";
		}

		$o_options .= '</div>' . "\n" . '</div>' . "\n";
		$o_options_dropdown .= '</div>' . "\n" . '</div>' . "\n";
		
		$o_options = $o_options . $o_options_dropdown;

		/* get all records with limit settings */
		$o_records = $this->Twig->GetAllViewRecords();
		$o_glob->BackupLimit();
		
		$o_flexViewElements = '';
		
		/* render table rows as flex elements */
		if ($o_records->Twigs->Count() > 0) {
			$o_flexTwig = new \fPHP\Twigs\flexTwig;
			$o_generalFlexTwig = new \fPHP\Twigs\flexTwig;
			
			/* get twig general flex record */
			if (!$o_generalFlexTwig->GetRecordPrimary(array($this->Twig->fphp_TableUUID, 'NULL'), array('TableUUID', 'FieldName'))) {
				throw new forestException(0x10001401, array($o_generalFlexTwig->fphp_Table));
			}
			
			/* create general flex element */
			$o_flexViewGeneralElement = new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::FLEXVIEWGENERALELEMENTREADONLY, array( $o_generalFlexTwig->UUID, \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'fphp_updateFlex'), $o_generalFlexTwig->Width, $o_generalFlexTwig->Height ));
			
			foreach ($o_records->Twigs as $o_record) {
				/* add general flex element */
				$o_flexViewElements .= $o_flexViewGeneralElement;
				
				foreach ($o_record->fphp_View as $s_column) {
					if ( ($s_column == 'Id') || ($s_column == 'UUID') ) {
						continue;
					}
					
					if ($o_glob->TablefieldsDictionary->Exists($o_record->fphp_Table . '_' . $s_column)) {
						$s_formElement = $o_glob->TablefieldsDictionary->{$o_record->fphp_Table . '_' . $s_column}->FormElementName;
						$s_forestData = $o_glob->TablefieldsDictionary->{$o_record->fphp_Table . '_' . $s_column}->ForestDataName;
					} else {
						if ( ($s_column == 'Created') || ($s_column == 'Modified') ) {
							$s_formElement = \fPHP\Forms\forestFormElement::DATETIMELOCAL;
							$s_forestData = 'forestObject(\'forestDateTime\')';
						} else if ( ($s_column == 'CreatedBy') || ($s_column == 'ModifiedBy') ) {
							$s_formElement = \fPHP\Forms\forestFormElement::TEXT;
							$s_forestData = 'forestString';
						}
					}
					
					/* do not render elements like FORM, PASSWORD, CAPTCHA and DROPZONE */
					if ( ($s_formElement == \fPHP\Forms\forestFormElement::FORM) || ($s_formElement == \fPHP\Forms\forestFormElement::PASSWORD) || ($s_formElement == \fPHP\Forms\forestFormElement::CAPTCHA) || ($s_formElement == \fPHP\Forms\forestFormElement::DROPZONE) ) {
						continue;
					}
					
					if (!$o_flexTwig->GetRecordPrimary(array($o_record->fphp_TableUUID, $s_column), array('TableUUID', 'FieldName'))) {
						throw new forestException(0x10001401, array($o_flexTwig->fphp_Table));
					}
					
					$s_value = '';
					/* render value */
					$this->ListViewRenderColumnValue($s_value, $s_formElement, $s_column, $o_record, $o_record->fphp_Table . '_' . $s_column);
					
					/* creat flex element */
					$o_flexViewElements .= new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::FLEXVIEWELEMENTREADONLY, array( $s_column, $o_flexTwig->UUID, $o_flexTwig->Width, $o_flexTwig->Height, $o_flexTwig->Top, $o_flexTwig->Left, $s_value ));
				}
				
				/* close general flex element */
				$o_flexViewElements .= '</div>';
			}
		} else {
			/* no records found */
			$o_glob->SystemMessages->Add(new forestException(0x10001400));
		}
		
		/* create search form */
		$s_searchForm = '';
		$s_searchTerms = '';
		$this->RenderSearchForm($s_searchForm, $s_searchTerms);
		
		$o_glob->RestoreLimit();
		$o_glob->Security->SessionData->Add(\fPHP\Branches\forestBranch::FLEX, 'lastView');
		
		/* create flex view top and down elements */
		$o_flexViewTop = new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::FLEXVIEWTOP, array( $o_options, strval($o_glob->Limit), $s_searchForm, $s_searchTerms ));
		$o_flexViewDown = new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::FLEXVIEWDOWN, array( $o_options, strval($o_glob->Limit) ));
		
		/* use template to render general flex view */
		$o_glob->Templates->Add(new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::FLEXVIEW, array($o_flexViewTop, $o_flexViewElements, $o_flexViewDown)), $o_glob->URL->Branch . 'FlexView');
	}
	
	/**
	 * handle edit flex view action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function EditFlexView() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* get first record */
		if (!$this->Twig->GetFirstRecord()) {
			/* no records found */
			$o_glob->SystemMessages->Add(new forestException(0x10001400));
		} else {
			$o_record = $this->Twig;
			
			$o_generalFlexTwig = new \fPHP\Twigs\flexTwig;
			
			/* get twig general flex record */
			if (!$o_generalFlexTwig->GetRecordPrimary(array($this->Twig->fphp_TableUUID, 'NULL'), array('TableUUID', 'FieldName'))) {
				/* create new general flex record for current twig table */
				$o_generalFlexTwig->TableUUID = $o_record->fphp_TableUUID;
				$o_generalFlexTwig->FieldName = 'NULL';
				$o_generalFlexTwig->Top = 0;
				$o_generalFlexTwig->Left = 0;
				$o_generalFlexTwig->Width = 300;
				$o_generalFlexTwig->Height = 500;
				
				/* insert record */
				$i_result = $o_generalFlexTwig->InsertRecord();
				
				/* evaluate result */
				if ($i_result == -1) {
					$this->CreateLogEntry('could not create new record - unique issue - ' . $o_glob->Temp->{'UniqueIssue'}, $o_generalFlexTwig);
					throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
				} else if ($i_result == 0) {
					$this->CreateLogEntry('could not create new record', $o_generalFlexTwig);
					throw new forestException(0x10001402);
				}
				
				$this->CreateLogEntry('new record created', $o_generalFlexTwig);
			}
			
			/* create general flex element */
			$o_flexViewGeneralElement = new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::FLEXVIEWGENERALELEMENT, array( $o_generalFlexTwig->UUID, \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'fphp_updateFlex'), $o_generalFlexTwig->Width, $o_generalFlexTwig->Height ));
			
			$o_flexViewElements = '';
			
			/* add general flex element info line */
			$o_flexViewElements .= '<div id="fphpFlexContainerGeneral">fphp-Flex-Container - width: ' . $o_generalFlexTwig->Width . 'px; height: ' . $o_generalFlexTwig->Height . 'px;</div>' . "\n" . '<hr>' . "\n";
			
			/* render first row as flex element */
			/* add general flex element */
			$o_flexViewElements .= $o_flexViewGeneralElement;
			
			foreach ($o_record->fphp_View as $s_column) {
				if ( ($s_column == 'Id') || ($s_column == 'UUID') ) {
					continue;
				}
				
				$s_formElement = '';
				
				if ($o_glob->TablefieldsDictionary->Exists($o_record->fphp_Table . '_' . $s_column)) {
					$s_formElement = $o_glob->TablefieldsDictionary->{$o_record->fphp_Table . '_' . $s_column}->FormElementName;
				} else {
					if ( ($s_column == 'Created') || ($s_column == 'Modified') ) {
						$s_formElement = \fPHP\Forms\forestFormElement::DATETIMELOCAL;
					} else if ( ($s_column == 'CreatedBy') || ($s_column == 'ModifiedBy') ) {
						$s_formElement = \fPHP\Forms\forestFormElement::TEXT;
					}
				}
				
				/* do not render elements like FORM, PASSWORD, CAPTCHA and DROPZONE */
				if ( ($s_formElement == \fPHP\Forms\forestFormElement::FORM) || ($s_formElement == \fPHP\Forms\forestFormElement::PASSWORD) || ($s_formElement == \fPHP\Forms\forestFormElement::CAPTCHA) || ($s_formElement == \fPHP\Forms\forestFormElement::DROPZONE) ) {
					continue;
				}
				
				$o_flexTwig = new \fPHP\Twigs\flexTwig;
				
				if (!$o_flexTwig->GetRecordPrimary(array($o_record->fphp_TableUUID, $s_column), array('TableUUID', 'FieldName'))) {
					/* create new flex record */
					$o_flexTwig->TableUUID = $o_record->fphp_TableUUID;
					$o_flexTwig->FieldName = $s_column;
					$o_flexTwig->Top = 10;
					$o_flexTwig->Left = 10;
					$o_flexTwig->Width = 150;
					$o_flexTwig->Height = 150;
					
					/* insert record */
					$i_result = $o_flexTwig->InsertRecord();
					
					/* evaluate result */
					if ($i_result == -1) {
						$this->CreateLogEntry('could not create new record - unique issue - ' . $o_glob->Temp->{'UniqueIssue'}, $o_flexTwig);
						throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
					} else if ($i_result == 0) {
						$this->CreateLogEntry('could not create new record', $o_flexTwig);
						throw new forestException(0x10001402);
					}
					
					$this->CreateLogEntry('new record created', $o_flexTwig);
				}
				
				$s_value = '-';
				/* render value */
				$this->ListViewRenderColumnValue($s_value, $s_formElement, $s_column, $o_record, $o_record->fphp_Table . '_' . $s_column);
				
				/* creat flex element */
				$o_flexViewElements .= new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::FLEXVIEWELEMENT, array( $s_column, $o_flexTwig->UUID, $o_flexTwig->Width, $o_flexTwig->Height, $o_flexTwig->Top, $o_flexTwig->Left, $s_value ));
			}
			
			/* close general flex element */
			$o_flexViewElements .= '</div>';
			
			$o_options = '<div class="btn-group">' . "\n";
		
			/* back link */
			$o_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnBack', 1) . '"><span class="fas fa-th-large text-info"></span></a>' . "\n";
			
			$o_options .= '</div>' . "\n";
			
			/* create flex view top and down elements */
			$o_flexViewTop = new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::FLEXVIEWTOP, array( $o_options, '', '', '' ));
			$o_flexViewDown = new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::FLEXVIEWDOWN, array( $o_options, '' ));
			
			/* use template to render general flex view */
			$o_glob->Templates->Add(new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::FLEXVIEW, array($o_flexViewTop, $o_flexViewElements, '<br>' . $o_flexViewDown)), $o_glob->URL->Branch . 'FlexView');
		}
	}
	
	
	/**
	 * generates detail view
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function GenerateView() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		if ( ($o_glob->Security->SessionData->Exists('last_filter')) && (!$o_glob->IsPost) ) {
			$o_glob->Security->SessionData->Add($o_glob->Security->SessionData->{'last_filter'}, 'filter');
		}
		
		/* set limit interval to 1, because of detail view (one record on one page) */
		$this->Twig->fphp_Interval = 1;
		
		/* nevertheless query all records */
		$o_records = $this->Twig->GetAllRecords();
		$o_glob->BackupLimit();
		
		$s_form = '';
		$s_title = '';
		$s_subRecords = '';
		
		if ($o_records->Twigs->Count() > 0) {
			$o_options = '<div class="btn-group d-none d-xl-flex">' . "\n" . '<div class="btn-group-lg">' . "\n";
			$o_options_dropdown = '<div class="dropdown d-xl-none dropright">' . "\n" . '<button class="btn btn-light btn-lg" type="button" data-toggle="dropdown"><span class="fas fa-ellipsis-h"></span></button>' . "\n" . '<div class="dropdown-menu btn-actions-collapse">';

			/* new link */
			if ($o_glob->Security->CheckUserPermission(null, 'new')) {
				$o_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'new') . '" class="btn btn-light"  title="' . $o_glob->GetTranslation('btnNewText', 1) . '"><span class="fas fa-plus text-success"></span></a>' . "\n";
				$o_options_dropdown .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'new') . '" class="btn btn-light"  title="' . $o_glob->GetTranslation('btnNewText', 1) . '"><span class="fas fa-plus text-success"></span></a>' . '<br>' . "\n";
			}

			/* edit link */
			$a_parameters = $o_glob->URL->Parameters;
			unset($a_parameters['newKey']);
			unset($a_parameters['viewKey']);
			unset($a_parameters['editKey']);
			unset($a_parameters['deleteKey']);
			unset($a_parameters['editSubKey']);
			unset($a_parameters['deleteSubKey']);
			unset($a_parameters['deleteFileKey']);
			unset($a_parameters['subConstraintKey']);
			$a_parameters['editKey'] = $o_records->Twigs->{0}->UUID;
			
			if ($o_glob->Security->CheckUserPermission(null, 'edit')) {
				$o_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'edit', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnEditText', 1) . '"><span class="fas fa-edit"></span></a>' . "\n";
				$o_options_dropdown .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'edit', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnEditText', 1) . '"><span class="fas fa-edit"></span></a>' . '<br>' . "\n";
			}

			/* delete link */
			if ($o_glob->Security->CheckUserPermission(null, 'delete')) {
				$o_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'delete', array('deleteKey' => $o_records->Twigs->{0}->UUID)) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="fas fa-trash text-danger"></span></a>' . "\n";
				$o_options_dropdown .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'delete', array('deleteKey' => $o_records->Twigs->{0}->UUID)) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="fas fa-trash text-danger"></span></a>' . '<br>' . "\n";
			}

			/* check if we have SortColumn set for current branch */
			if (array_key_exists($this->Twig->fphp_TableUUID, $o_glob->TablesInformation)) {
				if (issetStr($o_glob->TablesInformation[$this->Twig->fphp_TableUUID]['SortColumn'])) {
					/* move up link */
					/*$a_parameters = $o_glob->URL->Parameters;
					unset($a_parameters['newKey']);
					unset($a_parameters['viewKey']);
					unset($a_parameters['editKey']);
					unset($a_parameters['deleteKey']);
					unset($a_parameters['editSubKey']);
					unset($a_parameters['deleteSubKey']);
					unset($a_parameters['deleteFileKey']);
					unset($a_parameters['subConstraintKey']);
					$a_parameters['editKey'] = $o_records->Twigs->{0}->UUID;
					
					if ($o_glob->Security->CheckUserPermission(null, 'moveUp')) {
						$o_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'moveUp', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnMoveUpText', 1) . '"><span class="fas fa-caret-up"></span></a>' . "\n";
						$o_options_dropdown .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'moveUp', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnMoveUpText', 1) . '"><span class="fas fa-caret-up"></span></a>' . '<br>' . "\n";
					}*/

					/* move down link */
					/*$a_parameters = $o_glob->URL->Parameters;
					unset($a_parameters['newKey']);
					unset($a_parameters['viewKey']);
					unset($a_parameters['editKey']);
					unset($a_parameters['deleteKey']);
					unset($a_parameters['editSubKey']);
					unset($a_parameters['deleteSubKey']);
					unset($a_parameters['deleteFileKey']);
					unset($a_parameters['subConstraintKey']);
					$a_parameters['editKey'] = $o_records->Twigs->{0}->UUID;
					
					if ($o_glob->Security->CheckUserPermission(null, 'moveDown')) {
						$o_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'moveDown', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnMoveDownText', 1) . '"><span class="fas fa-caret-down"></span></a>' . "\n";
						$o_options_dropdown .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'moveDown', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnMoveDownText', 1) . '"><span class="fas fa-caret-down"></span></a>' . '<br>' . "\n";
					}*/
				}
			}
			
			/* check versioning settings of twig */
			if ($o_glob->TablesInformation[$this->Twig->fphp_TableUUID]['Versioning'] > 1) {
				if (!($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_records->Twigs->{0}->UUID), array('ForeignUUID'))) {
					/* checkout link */
					$a_parameters = $o_glob->URL->Parameters;
					unset($a_parameters['newKey']);
					unset($a_parameters['viewKey']);
					unset($a_parameters['editKey']);
					unset($a_parameters['deleteKey']);
					unset($a_parameters['editSubKey']);
					unset($a_parameters['deleteSubKey']);
					unset($a_parameters['deleteFileKey']);
					unset($a_parameters['subConstraintKey']);
					$a_parameters['editKey'] = $o_records->Twigs->{0}->UUID;
					
					if ($o_glob->Security->CheckUserPermission(null, 'checkout')) {
						$o_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'checkout', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnCheckoutText', 1) . '"><span class="fas fa-lock text-warning"></span></a>' . "\n";
						$o_options_dropdown .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'checkout', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnCheckoutText', 1) . '"><span class="fas fa-lock text-warning"></span></a>' . '<br>' . "\n";
					}
				} else {
					/* checkin link */
					$a_parameters = $o_glob->URL->Parameters;
					unset($a_parameters['newKey']);
					unset($a_parameters['viewKey']);
					unset($a_parameters['editKey']);
					unset($a_parameters['deleteKey']);
					unset($a_parameters['editSubKey']);
					unset($a_parameters['deleteSubKey']);
					unset($a_parameters['deleteFileKey']);
					unset($a_parameters['subConstraintKey']);
					$a_parameters['editKey'] = $o_records->Twigs->{0}->UUID;
					
					if ($o_glob->Security->CheckUserPermission(null, 'checkin')) {
						$o_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'checkin', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnCheckinText', 1) . '"><span class="fas fa-lock-open text-primary"></span></a>' . "\n";
						$o_options_dropdown .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'checkin', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnCheckinText', 1) . '"><span class="fas fa-lock-open text-primary"></span></a>' . '<br>' . "\n";
					}
				}
			}
			
			/* list link */
			if ($o_glob->Security->CheckUserPermission(null, 'view')) {
				$o_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, ((($this->OriginalView == \fPHP\Branches\forestBranch::LIST) || ($this->OriginalView == \fPHP\Branches\forestBranch::FLEX)) ? null : 'view')) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnListText', 1) . '"><span class="fas fa-th-list text-info"></span></a>' . "\n";
				$o_options_dropdown .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, ((($this->OriginalView == \fPHP\Branches\forestBranch::LIST) || ($this->OriginalView == \fPHP\Branches\forestBranch::FLEX)) ? null : 'view')) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnListText', 1) . '"><span class="fas fa-th-list text-info"></span></a>' . '<br>' . "\n";
			}

			$o_options .= '</div>' . "\n" . '</div>' . "\n";
			$o_options_dropdown .= '</div>' . "\n" . '</div>' . "\n";
			
			$o_options = $o_options . $o_options_dropdown;

			/* create form with record in read only mode */
			$s_form = new \fPHP\Forms\forestForm($o_records->Twigs->{0}, true, true, false);
			$s_form->FormModalConfiguration->Modal = false;
			
			/* adjust title */
			$s_title = $o_glob->URL->BranchTitle . ' ' . $o_glob->GetTranslation('BranchTitleRecord', 1);
			
			/* render sub records */
			$s_subRecords = $this->ListSubRecords($o_records->Twigs->{0});
		} else {
			$o_options = '<div class="btn-group d-none d-xl-flex">' . "\n" . '<div class="btn-group-lg">' . "\n";
			$o_options_dropdown = '<div class="dropdown d-xl-none dropright">' . "\n" . '<button class="btn btn-light btn-lg dropdown-toggle" type="button" data-toggle="dropdown">Actions</button>' . "\n" . '<div class="dropdown-menu btn-actions-collapse">';
			
			if ($o_glob->Security->CheckUserPermission(null, 'new')) {
				$o_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'new') . '" class="btn btn-light"  title="' . $o_glob->GetTranslation('btnNewText', 1) . '"><span class="fas fa-plus text-success"></span></a>' . "\n";
				$o_options_dropdown .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'new') . '" class="btn btn-light"  title="' . $o_glob->GetTranslation('btnNewText', 1) . '"><span class="fas fa-plus text-success"></span></a>' . '<br>' . "\n";
			}
			
			if ($o_glob->Security->CheckUserPermission(null, 'edit')) {
				$o_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'edit') . '" class="btn btn-light disabled"  title="' . $o_glob->GetTranslation('btnEditText', 1) . '"><span class="fas fa-edit"></span></a>' . "\n";
				$o_options_dropdown .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'edit') . '" class="btn btn-light disabled"  title="' . $o_glob->GetTranslation('btnEditText', 1) . '"><span class="fas fa-edit"></span></a>' . '<br>' . "\n";
			}
			
			if ($o_glob->Security->CheckUserPermission(null, 'delete')) {
				$o_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'delete') . '" class="btn btn-light disabled"  title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="fas fa-trash text-danger"></span></a>' . "\n";
				$o_options_dropdown .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'delete') . '" class="btn btn-light disabled"  title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="fas fa-trash text-danger"></span></a>' . '<br>' . "\n";
			}
			
			if ($o_glob->Security->CheckUserPermission(null, 'view')) {
				$o_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, (($this->OriginalView == \fPHP\Branches\forestBranch::LIST) ? null : 'view')) . '" class="btn btn-light"  title="' . $o_glob->GetTranslation('btnListText', 1) . '"><span class="fas fa-th-list text-info"></span></a>' . "\n";
				$o_options_dropdown .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, (($this->OriginalView == \fPHP\Branches\forestBranch::LIST) ? null : 'view')) . '" class="btn btn-light"  title="' . $o_glob->GetTranslation('btnListText', 1) . '"><span class="fas fa-th-list text-info"></span></a>' . '<br>' . "\n";
			}
			
			$o_options .= '</div>' . "\n" . '</div>' . "\n";
			$o_options_dropdown .= '</div>' . "\n" . '</div>' . "\n";
			
			$o_options = $o_options . $o_options_dropdown;
			
			/* no records found */
			$o_glob->SystemMessages->Add(new forestException(0x10001400));
		}
		
		/* create search form */
		$s_searchForm = '';
		$s_searchTerms = '';
		$this->RenderSearchForm($s_searchForm, $s_searchTerms);
		
		$o_glob->RestoreLimit();
		$o_glob->Security->SessionData->Add(\fPHP\Branches\forestBranch::DETAIL, 'lastView');
		
		$o_viewOptionsTop = new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::VIEWOPTIONSTOP, array( $o_options, strval($o_glob->Limit), $s_searchForm, $s_searchTerms ));
		$o_viewOptionsDown = new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::VIEWOPTIONSDOWN, array($o_options, strval($o_glob->Limit)));
		
		/* use template to render general view */
		$o_glob->Templates->Add(new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::VIEW, array($o_viewOptionsTop, $s_title, strval($s_form), $s_subRecords, $o_viewOptionsDown)), $o_glob->URL->Branch . 'View');
	}
	
	/**
	 * handle sub records display in detail view
	 *
	 * @param forestTwig $p_o_twig  head record where sub records will be queried from
	 * @param bool $p_b_readonly  true - show sub records without manipulation actions, false - normal view of all sub records
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function ListSubRecords(\fPHP\Twigs\forestTwig $p_o_twig, $p_b_readonly = false) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$s_subFormItems = '';
		$b_firstSubElement = false;
		
		/* get value for info columns when configured */
		$i_infoColumns = $o_glob->TablesInformation[$p_o_twig->fphp_TableUUID]['InfoColumns'];
		
		/* check if table of twig parameter is valid table and has tablefields */
		if (array_key_exists($p_o_twig->fphp_Table, $o_glob->Tables)) {
			$s_tableUUID = $o_glob->Tables[$p_o_twig->fphp_Table];
			$s_tableName = array_search($s_tableUUID, $o_glob->Tables);
			
			if (in_array($s_tableUUID, $o_glob->TablesWithTablefields)) {
				/* check for method which can add additional accordion elements to modal form which do not belong to the standard */
				if (method_exists($this, 'additionalListSubRecordsAction')) {
					$this->additionalListSubRecordsAction($p_o_twig, $p_b_readonly, $s_subFormItems, $b_firstSubElement);
				}
				
				/* check if sub constraints exists for current table twig */
				if (array_key_exists($p_o_twig->fphp_TableUUID, $o_glob->SubConstraintsDictionary)) {
					foreach ($o_glob->SubConstraintsDictionary[$p_o_twig->fphp_TableUUID] as $o_subconstraint) {
						/* query sub recors of sub constraint */
						$o_subRecords = $p_o_twig->QuerySubRecords($o_subconstraint);
						
						/* if we have read only mode and no sub records, do not show accordion element */
						if ($p_b_readonly) {
							if ($o_subRecords->Twigs->Count() <= 0) {
								continue;
							}
						}
						
						/* create join twig object by sub constraint sub table setting */
						$s_joinTableName = array_search($o_subconstraint->SubTableUUID->PrimaryValue, $o_glob->Tables);
						$s_tempTable = $s_joinTableName;
						\fPHP\Helper\forestStringLib::RemoveTablePrefix($s_tempTable);
						$s_foo = '\\fPHP\\Twigs\\' . $s_tempTable . 'Twig';
						$o_tempTwig = new $s_foo;
						$a_view = array();
						
						/* use defined view in sub constraint or take view from join twig object */
						if (issetStr($o_subconstraint->View->PrimaryValue)) {
							$a_subconstraintView = explode(';', $o_subconstraint->View->PrimaryValue);
							
							foreach ($a_subconstraintView as $s_value) {
								$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
								
								if (!$o_tablefieldTwig->GetRecord(array($s_value))) {
									throw new forestException(0x10001401, array($o_tablefieldTwig->fphp_Table));
								}
								
								if (!property_exists($o_tempTwig, $o_tablefieldTwig->FieldName)) {
									$a_view = $o_tempTwig->fphp_View;
									break;
								} else {
									$a_view[] = $o_tablefieldTwig->FieldName;
								}
							}
						} else {
							$a_view = $o_tempTwig->fphp_View;
						}
						
						/* get branch id of sub constraint sub table for translation of column headers */
						$i_branchId = 1;
							
						foreach ($o_glob->BranchTree['Id'] as $o_branch) {
							if ($o_branch['Table']->PrimaryValue == $o_subconstraint->SubTableUUID->PrimaryValue) {
								$i_branchId = $o_branch['Id'];
							}
						}
						
						/* ************************* */
						/* ***********HEAD********** */
						/* ************************* */
						
						$s_subTableHead = '';
						
						/* if identifier is configured in sub constraint, render identifier column head */
						if (issetStr($o_subconstraint->IdentifierStart)) {
							$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortIdentifier') . '</th>' . "\n";
						}
						
						/* render join column heads */
						foreach ($a_view as $s_columnHead) {
							/* do not allow to display standard columns */
							if (in_array($s_columnHead, array('Created', 'CreatedBy', 'Modified', 'ModifiedBy', 'Identifier'))) {
								continue;
							}
							
							$s_formElement = $o_glob->TablefieldsDictionary->{$o_tempTwig->fphp_Table . '_' . $s_columnHead}->FormElementName;
							$s_forestData = $o_glob->TablefieldsDictionary->{$o_tempTwig->fphp_Table . '_' . $s_columnHead}->ForestDataName;
							
							if ( ($s_formElement == \fPHP\Forms\forestFormElement::PASSWORD) || ($s_formElement == \fPHP\Forms\forestFormElement::FILE) || ($s_formElement == \fPHP\Forms\forestFormElement::DROPZONE) || ($s_formElement == \fPHP\Forms\forestFormElement::CAPTCHA) ) {
								continue;
							}
							
							$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sort' . $s_columnHead, $i_branchId) . '</th>' . "\n";
						}
						
						/* render sub record column heads */
						foreach ($o_glob->TablefieldsDictionary as $o_tableFieldDictionaryObject) {
							if ($o_tableFieldDictionaryObject->TableUUID == $o_subconstraint->UUID) {
								$s_formElement = $o_tableFieldDictionaryObject->FormElementName;
								$s_forestData = $o_tableFieldDictionaryObject->ForestDataName;
							
								if ( ($s_formElement == \fPHP\Forms\forestFormElement::PASSWORD) || ($s_formElement == \fPHP\Forms\forestFormElement::DROPZONE) || ($s_formElement == \fPHP\Forms\forestFormElement::CAPTCHA) ) {
									continue;
								}
								
								$s_columnHead = $o_glob->GetTranslation('formSub' . $o_tableFieldDictionaryObject->FieldName . 'Label');
								
								if (\fPHP\Helper\forestStringLib::EndsWith($s_columnHead, ':')) {
									$s_columnHead = substr($s_columnHead, 0, -1);
								}
								
								$s_subTableHead .= '<th>' . $s_columnHead . '</th>' . "\n";
							}
						}
						
						/* render info columns when configured */
						if ($i_infoColumns == 10) {
							$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortCreated', 1) . '</th>' . "\n";
							$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortCreatedBy', 1) . '</th>' . "\n";
						} else if ($i_infoColumns == 100) {
							$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortModified', 1) . '</th>' . "\n";
							$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortModifiedBy', 1) . '</th>' . "\n";
						} else if ($i_infoColumns == 1000) {
							$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortCreated', 1) . '</th>' . "\n";
							$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortCreatedBy', 1) . '</th>' . "\n";
							$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortModified', 1) . '</th>' . "\n";
							$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortModifiedBy', 1) . '</th>' . "\n";
						}
						
						/* render option column head */
						$s_subTableHead .= '<th>' . $o_glob->GetTranslation('formSubOptions', 1) . '</th>' . "\n";
						
						/* ************************* */
						/* ***********ROWS********** */
						/* ************************* */
						
						$s_subTableRows = '';
						$i_cnt = 0;
						
						foreach ($o_subRecords->Twigs as $o_subRecord) {
							$o_joinSubRecord = $o_subRecords->JoinTwigs->{$i_cnt};
							
							/* get amount of files of sub record */
							$o_filesTwig = new \fPHP\Twigs\filesTwig; 
							
							$a_sqlAdditionalFilter = array(array('column' => 'ForeignUUID', 'value' => $o_subRecord->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
							$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
							$i_files = $o_filesTwig->GetCount(null, true);
							$o_glob->Temp->Del('SQLAdditionalFilter');
							
							/*echo '<pre>';
							$s_recordFields = $o_subRecord->ShowFields();
							echo $s_recordFields;
							$s_recordFields = $o_joinSubRecord->ShowFields();
							echo $s_recordFields;
							echo '</pre>';*/
							
							/* render records, based on subconstraint view columns, if it is null, just use view setting from join twig object */
							$s_subTableRows .= '<tr';
							
							/* checkout rendering */
							if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_subRecord->UUID), array('ForeignUUID'))) {
								$s_subTableRows .= ' class="bg-warning"';
							}
							
							$s_subTableRows .= '>' . "\n";
							
							/* if identifier is configured in sub constraint */
							if (issetStr($o_subconstraint->IdentifierStart)) {
								$s_subTableRows .=  '<td><span>' . $o_subRecord->Identifier . '</span></td>' . "\n";
							}
							
							/* render join columns */
							foreach ($a_view as $s_column) {
								/* do not allow to display standard columns */
								if (in_array($s_column, array('Created', 'CreatedBy', 'Modified', 'ModifiedBy', 'Identifier'))) {
									continue;
								}
								
								$s_formElement = $o_glob->TablefieldsDictionary->{$o_tempTwig->fphp_Table . '_' . $s_column}->FormElementName;
								$s_forestData = $o_glob->TablefieldsDictionary->{$o_tempTwig->fphp_Table . '_' . $s_column}->ForestDataName;
								
								if ( ($s_formElement == \fPHP\Forms\forestFormElement::PASSWORD) || ($s_formElement == \fPHP\Forms\forestFormElement::FILE) || ($s_formElement == \fPHP\Forms\forestFormElement::DROPZONE) || ($s_formElement == \fPHP\Forms\forestFormElement::CAPTCHA) ) {
									continue;
								}
								
								$s_value = '-';
								$this->ListViewRenderColumnValue($s_value, $s_formElement, $s_column, $o_joinSubRecord, $o_tempTwig->fphp_Table . '_' . $s_column);
								$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
							}
							
							/* render sub record columns */
							foreach ($o_glob->TablefieldsDictionary as $o_tableFieldDictionaryObject) {
								if ($o_tableFieldDictionaryObject->TableUUID == $o_subconstraint->UUID) {
									$s_formElement = $o_tableFieldDictionaryObject->FormElementName;
									$s_forestData = $o_tableFieldDictionaryObject->ForestDataName;
								
									if ( ($s_formElement == \fPHP\Forms\forestFormElement::PASSWORD) || ($s_formElement == \fPHP\Forms\forestFormElement::DROPZONE) || ($s_formElement == \fPHP\Forms\forestFormElement::CAPTCHA) ) {
										continue;
									}
									
									$s_value = '-';
									$this->ListViewRenderColumnValue($s_value, $s_formElement, $o_tableFieldDictionaryObject->SubRecordField, $o_subRecord, $s_tableName . '_' . $s_joinTableName . '_' . $o_tableFieldDictionaryObject->FieldName);
									$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
								}
							}
							
							/* render info columns when configured */
							if ($i_infoColumns == 10) {
								$s_value = '-';
								$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::DATETIMELOCAL, 'Created', $o_subRecord, $s_tableName . '_' . $s_joinTableName . '_Created');
								$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
								
								$s_value = '-';
								$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::TEXT, 'CreatedBy', $o_subRecord, $s_tableName . '_' . $s_joinTableName . '_CreatedBy');
								$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
							} else if ($i_infoColumns == 100) {
								$s_value = '-';
								$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::DATETIMELOCAL, 'Modified', $o_subRecord, $s_tableName . '_' . $s_joinTableName . 'Modified');
								$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
								
								$s_value = '-';
								$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::TEXT, 'ModifiedBy', $o_subRecord, $s_tableName . '_' . $s_joinTableName . '_ModifiedBy');
								$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
							} else if ($i_infoColumns == 1000) {
								$s_value = '-';
								$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::DATETIMELOCAL, 'Created', $o_subRecord, $s_tableName . '_' . $s_joinTableName . '_Created');
								$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
								
								$s_value = '-';
								$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::TEXT, 'CreatedBy', $o_subRecord, $s_tableName . '_' . $s_joinTableName . '_CreatedBy');
								$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
								
								$s_value = '-';
								$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::DATETIMELOCAL, 'Modified', $o_subRecord, $s_tableName . '_' . $s_joinTableName . 'Modified');
								$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
								
								$s_value = '-';
								$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::TEXT, 'ModifiedBy', $o_subRecord, $s_tableName . '_' . $s_joinTableName . '_ModifiedBy');
								$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
							}
							
							/* render options */
							$s_options = '<span class="btn-group">' . "\n";
							
							if (!$p_b_readonly) {
								$a_parameters = $o_glob->URL->Parameters;
								unset($a_parameters['newKey']);
								unset($a_parameters['viewKey']);
								unset($a_parameters['editKey']);
								unset($a_parameters['deleteKey']);
								unset($a_parameters['editSubKey']);
								unset($a_parameters['deleteSubKey']);
								unset($a_parameters['deleteFileKey']);
								unset($a_parameters['subConstraintKey']);
								$a_parameters['editSubKey'] = $o_subRecord->UUID;
								$a_parameters['subConstraintKey'] = $o_subconstraint->UUID;
								
								if ($o_glob->Security->CheckUserPermission(null, 'edit')) {
									$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'edit', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnEditText', 1) . '"><span class="fas fa-edit"></span></a>' . "\n";
								}

								if ($i_files > 0) {
									$a_parameters = $o_glob->URL->Parameters;
									unset($a_parameters['newKey']);
									unset($a_parameters['viewKey']);
									unset($a_parameters['viewSubKey']);
									unset($a_parameters['editKey']);
									unset($a_parameters['deleteKey']);
									unset($a_parameters['editSubKey']);
									unset($a_parameters['deleteSubKey']);
									unset($a_parameters['deleteFileKey']);
									unset($a_parameters['subConstraintKey']);
									$a_parameters['viewSubKey'] = $o_subRecord->UUID;
									$a_parameters['subConstraintKey'] = $o_subconstraint->UUID;
									
									if ($o_glob->Security->CheckUserPermission(null, 'viewFiles')) {
										$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'viewFiles', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnViewFilesText', 1) . '"><span class="fas fa-file"></span></a>' . "\n";
									}
								}
								
								/* check versioning settings of twig */
								if ($o_glob->TablesInformation[$o_subconstraint->TableUUID]['Versioning'] > 1) {
									if (!($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_subRecord->UUID), array('ForeignUUID'))) {
										/* checkout link */
										$a_parameters = $o_glob->URL->Parameters;
										unset($a_parameters['newKey']);
										unset($a_parameters['viewKey']);
										unset($a_parameters['editKey']);
										unset($a_parameters['deleteKey']);
										unset($a_parameters['editSubKey']);
										unset($a_parameters['deleteSubKey']);
										unset($a_parameters['deleteFileKey']);
										unset($a_parameters['subConstraintKey']);
										$a_parameters['editKey'] = $o_subRecord->UUID;
										
										if ($o_glob->Security->CheckUserPermission(null, 'checkout')) {
											$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'checkout', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnCheckoutText', 1) . '"><span class="fas fa-lock text-warning"></span></a>';
										}
									} else {
										/* checkin link */
										$a_parameters = $o_glob->URL->Parameters;
										unset($a_parameters['newKey']);
										unset($a_parameters['viewKey']);
										unset($a_parameters['editKey']);
										unset($a_parameters['deleteKey']);
										unset($a_parameters['editSubKey']);
										unset($a_parameters['deleteSubKey']);
										unset($a_parameters['deleteFileKey']);
										unset($a_parameters['subConstraintKey']);
										$a_parameters['editKey'] = $o_subRecord->UUID;
										
										if ($o_glob->Security->CheckUserPermission(null, 'checkin')) {
											$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'checkin', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnCheckinText', 1) . '"><span class="fas fa-lock-open text-primary"></span></a>';
										}
									}
								}
								
								$a_parameters = $o_glob->URL->Parameters;
								unset($a_parameters['newKey']);
								unset($a_parameters['viewKey']);
								unset($a_parameters['editKey']);
								unset($a_parameters['deleteKey']);
								unset($a_parameters['editSubKey']);
								unset($a_parameters['deleteSubKey']);
								unset($a_parameters['deleteFileKey']);
								unset($a_parameters['subConstraintKey']);
								$a_parameters['deleteSubKey'] = $o_subRecord->UUID;
								
								if ($o_glob->Security->CheckUserPermission(null, 'delete')) {
									$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'delete', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="fas fa-trash text-danger"></span></a>' . "\n";
								}
							} else {
								if ($i_files > 0) {
									$a_parameters = $o_glob->URL->Parameters;
									unset($a_parameters['newKey']);
									unset($a_parameters['viewKey']);
									unset($a_parameters['viewSubKey']);
									unset($a_parameters['editKey']);
									unset($a_parameters['deleteKey']);
									unset($a_parameters['editSubKey']);
									unset($a_parameters['deleteSubKey']);
									unset($a_parameters['deleteFileKey']);
									unset($a_parameters['subConstraintKey']);
									$a_parameters['viewSubKey'] = $o_subRecord->UUID;
									$a_parameters['subConstraintKey'] = $o_subconstraint->UUID;
									
									if ($o_glob->Security->CheckUserPermission(null, 'viewFiles')) {
										$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'viewFiles', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnViewFilesText', 1) . '"><span class="fas fa-file"></span></a>' . "\n";
									}
								} else {
									$s_options .= '-';
								}
							}
							
							$s_options .= '</span>' . "\n";
							$s_subTableRows .=  '<td>' . $s_options . '</td>' . "\n";
							
							$s_subTableRows .=  '</tr>' . "\n";
							
							$i_cnt++;
						}
						
						$s_firstElement = '';
						
						if ($b_firstSubElement == false) {
							$s_firstElement = ' show';
							$b_firstSubElement = true;
						}
						
						if (!$p_b_readonly) {
							$a_parameters = $o_glob->URL->Parameters;
							unset($a_parameters['newKey']);
							unset($a_parameters['viewKey']);
							unset($a_parameters['editKey']);
							unset($a_parameters['deleteKey']);
							unset($a_parameters['editSubKey']);
							unset($a_parameters['deleteSubKey']);
							unset($a_parameters['deleteFileKey']);
							unset($a_parameters['subConstraintKey']);
							$a_parameters['newKey'] = $p_o_twig->UUID;
							$a_parameters['subConstraintKey'] = $o_subconstraint->UUID;
							
							if ($o_glob->Security->CheckUserPermission(null, 'new')) {
								$s_newButton = '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'new', $a_parameters) . '" class="btn btn-light" style="margin-bottom: 5px;" title="' . $o_glob->GetTranslation('btnNewText', 1) . '"><span class="fas fa-plus text-success"></span></a>' . "\n";
							}
						} else {
							$s_newButton = '';
						}
						
						$s_subFormItemContent = new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEWITEMCONTENT, array($s_newButton, $s_subTableHead, $s_subTableRows));
						$s_subFormItems .= new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEWITEM, array('sub' . $s_tempTable, $o_glob->GetTranslation($o_glob->BranchTree['Id'][$o_glob->BranchTree['Name'][$s_tempTable]]['Title'], 1) . ' ' . $o_glob->GetTranslation('Lines', 1) . ' (' . $o_subRecords->Twigs->Count() . ')', $s_firstElement, $s_subFormItemContent));
					}
				}
				
				/* look for files of head record */
				$o_filesTwig = new \fPHP\Twigs\filesTwig;
				
				$a_sqlAdditionalFilter = array(array('column' => 'ForeignUUID', 'value' => $p_o_twig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
				$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
				$o_files = $o_filesTwig->GetAllRecords(true);
				$o_glob->Temp->Del('SQLAdditionalFilter');
				
				if ($o_files->Twigs->Count() > 0) {
					$s_subTableHead = '';
					$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortFile', 1) . '</th>' . "\n";
					
					/* check versioning settings of twig */
					if ($o_glob->TablesInformation[$p_o_twig->fphp_TableUUID]['Versioning'] == 100) {
						$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortVersion', 1) . '</th>' . "\n";
					}
					
					/* render info columns when configured */
					if ($i_infoColumns == 10) {
						$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortCreated', 1) . '</th>' . "\n";
						$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortCreatedBy', 1) . '</th>' . "\n";
					} else if ($i_infoColumns == 100) {
						$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortModified', 1) . '</th>' . "\n";
						$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortModifiedBy', 1) . '</th>' . "\n";
					} else if ($i_infoColumns == 1000) {
						$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortCreated', 1) . '</th>' . "\n";
						$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortCreatedBy', 1) . '</th>' . "\n";
						$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortModified', 1) . '</th>' . "\n";
						$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortModifiedBy', 1) . '</th>' . "\n";
					}
					
					$s_subTableHead .= '<th>' . $o_glob->GetTranslation('formSubOptions', 1) . '</th>' . "\n";
					
					$s_subTableRows = '';
		
					foreach ($o_files->Twigs as $o_file) {
						$s_subTableRows .= '<tr';
							
						if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_file->UUID), array('ForeignUUID'))) {
							$s_subTableRows .= ' class="bg-warning"';
						}
							
						$s_subTableRows .= '>' . "\n";
							
						$s_subTableRows .=  '<td>' . $o_file->DisplayName . '</td>' . "\n";
						
						/* check versioning settings of twig */
						if ($o_glob->TablesInformation[$p_o_twig->fphp_TableUUID]['Versioning'] == 100) {
							$s_subTableRows .= '<td>' . $o_file->Major . $o_glob->Trunk->VersionDelimiter . $o_file->Minor . '</td>' . "\n";
						}
						
						/* render info columns when configured */
						if ($i_infoColumns == 10) {
							$s_value = '-';
							$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::DATETIMELOCAL, 'Created', $o_file, $o_file->fphp_Table . '_Created');
							$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
							
							$s_value = '-';
							$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::TEXT, 'CreatedBy', $o_file, $o_file->fphp_Table . '_CreatedBy');
							$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
						} else if ($i_infoColumns == 100) {
							$s_value = '-';
							$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::DATETIMELOCAL, 'Modified', $o_file, $o_file->fphp_Table . 'Modified');
							$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
							
							$s_value = '-';
							$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::TEXT, 'ModifiedBy', $o_file, $o_file->fphp_Table . '_ModifiedBy');
							$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
						} else if ($i_infoColumns == 1000) {
							$s_value = '-';
							$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::DATETIMELOCAL, 'Created', $o_file, $o_file->fphp_Table . '_Created');
							$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
							
							$s_value = '-';
							$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::TEXT, 'CreatedBy', $o_file, $o_file->fphp_Table . '_CreatedBy');
							$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
							
							$s_value = '-';
							$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::DATETIMELOCAL, 'Modified', $o_file, $o_file->fphp_Table . 'Modified');
							$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
							
							$s_value = '-';
							$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::TEXT, 'ModifiedBy', $o_file, $o_file->fphp_Table . '_ModifiedBy');
							$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
						}
						
						$s_options = '<span class="btn-group">' . "\n";
						
						$s_value = '';
						$s_folder = substr(pathinfo($o_file->Name, PATHINFO_FILENAME), 6, 2);
						$s_path = '';
	
						if (count($o_glob->URL->Branches) > 0) {
							foreach($o_glob->URL->Branches as $s_branch) {
								$s_path .= $s_branch . '/';
							}
						}
						
						$s_path .= $o_glob->URL->Branch . '/';
						
						$s_path = './trunk/' . $s_path . 'fphp_files/' . $s_folder . '/';
						
						if (is_dir($s_path)) {
							if (file_exists($s_path . $o_file->Name)) {
								$s_value = '<a href="' . $s_path . $o_file->Name . '" target="_blank" class="btn btn-light" title="' . $o_file->DisplayName . '" download="' . $o_file->DisplayName . '"><span class="fas fa-download"></span></a>' . "\n";
							}
						}
						
						$s_options .=  $s_value;
						
						/* check if we have files in history for current file record */
						$o_historyFilesTwig = new \fPHP\Twigs\filesTwig;
				
						$a_sqlAdditionalFilter = array(array('column' => 'ForeignUUID', 'value' => $o_file->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
						$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
						$i_historyFiles = $o_historyFilesTwig->GetCount(null, true);
						$o_glob->Temp->Del('SQLAdditionalFilter');
						
						if ($i_historyFiles > 0) {
							$a_parameters = $o_glob->URL->Parameters;
							unset($a_parameters['newKey']);
							unset($a_parameters['viewKey']);
							unset($a_parameters['viewSubKey']);
							unset($a_parameters['editKey']);
							unset($a_parameters['editFileKey']);
							unset($a_parameters['deleteKey']);
							unset($a_parameters['editSubKey']);
							unset($a_parameters['deleteSubKey']);
							unset($a_parameters['deleteFileKey']);
							unset($a_parameters['subConstraintKey']);
							$a_parameters['viewSubKey'] = $o_file->UUID;
							$a_parameters['subConstraintKey'] = $p_o_twig->fphp_TableUUID;
							
							if ($o_glob->Security->CheckUserPermission(null, 'viewFilesHistory')) {
								$s_options .=  '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'viewFilesHistory', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnHistoryText', 1) . ' (' . $i_historyFiles . ')"><span class="fas fa-history"></span></a>' . "\n";
							}
						}
						
						if (!$p_b_readonly) {
							$a_parameters = $o_glob->URL->Parameters;
							unset($a_parameters['newKey']);
							unset($a_parameters['viewKey']);
							unset($a_parameters['viewSubKey']);
							unset($a_parameters['editKey']);
							unset($a_parameters['editFileKey']);
							unset($a_parameters['deleteKey']);
							unset($a_parameters['editSubKey']);
							unset($a_parameters['deleteSubKey']);
							unset($a_parameters['deleteFileKey']);
							unset($a_parameters['subConstraintKey']);
							$a_parameters['editFileKey'] = $o_file->UUID;
							$a_parameters['subConstraintKey'] = $p_o_twig->fphp_TableUUID;
							
							if ($o_glob->Security->CheckUserPermission(null, 'replaceFile')) {
								$s_options .=  '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'replaceFile', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnUploadText', 1) . '"><span class="fas fa-upload"></span></a>' . "\n";
							}
							
							/* check versioning settings of twig */
							if ($o_glob->TablesInformation[$p_o_twig->fphp_TableUUID]['Versioning'] > 1) {
								if (!($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_file->UUID), array('ForeignUUID'))) {
									/* checkout link */
									$a_parameters = $o_glob->URL->Parameters;
									unset($a_parameters['newKey']);
									unset($a_parameters['viewKey']);
									unset($a_parameters['editKey']);
									unset($a_parameters['deleteKey']);
									unset($a_parameters['editSubKey']);
									unset($a_parameters['deleteSubKey']);
									unset($a_parameters['deleteFileKey']);
									unset($a_parameters['subConstraintKey']);
									$a_parameters['editKey'] = $o_file->UUID;
									
									if ($o_glob->Security->CheckUserPermission(null, 'checkout')) {
										$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'checkout', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnCheckoutText', 1) . '"><span class="fas fa-lock text-warning"></span></a>';
									}
								} else {
									/* checkin link */
									$a_parameters = $o_glob->URL->Parameters;
									unset($a_parameters['newKey']);
									unset($a_parameters['viewKey']);
									unset($a_parameters['editKey']);
									unset($a_parameters['deleteKey']);
									unset($a_parameters['editSubKey']);
									unset($a_parameters['deleteSubKey']);
									unset($a_parameters['deleteFileKey']);
									unset($a_parameters['subConstraintKey']);
									$a_parameters['editKey'] = $o_file->UUID;
									
									if ($o_glob->Security->CheckUserPermission(null, 'checkin')) {
										$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'checkin', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnCheckinText', 1) . '"><span class="fas fa-lock-open text-primary"></span></a>';
									}
								}
							}
							
							$a_parameters = $o_glob->URL->Parameters;
							unset($a_parameters['newKey']);
							unset($a_parameters['viewKey']);
							unset($a_parameters['editKey']);
							unset($a_parameters['editSubKey']);
							unset($a_parameters['deleteKey']);
							unset($a_parameters['deleteSubKey']);
							$a_parameters['deleteFileKey'] = $o_file->UUID;
							
							if ($o_glob->Security->CheckUserPermission(null, 'delete')) {
								$s_options .=  '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'delete', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="fas fa-trash text-danger"></span></a>' . "\n";
							}
						}

						
						$s_options .= '</span>' . "\n";
						$s_subTableRows .=  '<td>' . $s_options . '</td>' . "\n";
						$s_subTableRows .=  '</tr>' . "\n";
					}
					
					$s_firstElement = '';
					
					if ($b_firstSubElement == false) {
						$s_firstElement = ' show';
						$b_firstSubElement = true;
					}
					
					$s_subFormItemContent = new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEWITEMCONTENT, array('', $s_subTableHead, $s_subTableRows));
					$s_subFormItems .= new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEWITEM, array('subfiles', $o_glob->GetTranslation('Files', 1) . ' (' . $o_files->Twigs->Count() . ')', $s_firstElement, $s_subFormItemContent));
				}
			}
		}
		
		/* use template to render sub constraints and files of a record */
		return new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEW, array($s_subFormItems));
	}
	
	
	/**
	 * handle view record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function ViewRecord() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'viewKey'), 'viewKey' );
		
		if ( ($o_glob->Temp->Exists('viewKey')) && ($o_glob->Temp->{'viewKey'} != null) ) {
			/* query twig record if we have view key in url parameters */
			if (! ($this->Twig->GetRecord(array($o_glob->Temp->{'viewKey'}))) ) {
				throw new forestException(0x10001401, array($this->Twig->fphp_Table));
			} else {
				if (method_exists($this, 'beforeViewAction')) {
					$this->beforeViewAction();
				}
				
				/* create modal read only form */
				$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig, true, true);
				
				/* add sub constraints and files for modal form */
				$o_glob->PostModalForm->FormModalSubForm = strval($this->ListSubRecords($this->Twig, true));
				
				if (method_exists($this, 'afterViewAction')) {
					$this->afterViewAction();
				}
				
				if ($o_glob->Security->SessionData->Exists('last_filter')) {
					$o_glob->Security->SessionData->Add($o_glob->Security->SessionData->{'last_filter'}, 'filter');
				}
				
				$this->StandardView = \fPHP\Branches\forestBranch::LIST; /* because it only makes sense if we stay in list view, when we open modal read only form for record */
				$this->SetNextAction('init');
			}
		} else {
			if ($this->StandardView == \fPHP\Branches\forestBranch::DETAIL) {
				$this->GenerateListView();
			} else if ($this->StandardView == \fPHP\Branches\forestBranch::LIST) {
				$this->GenerateView();
			} else if ($this->StandardView == \fPHP\Branches\forestBranch::FLEX) {
				if ($o_glob->Security->SessionData->Exists('lastView')) {
					if ( ($o_glob->Security->SessionData->{'lastView'} == \fPHP\Branches\forestBranch::LIST) || ($o_glob->Security->SessionData->{'lastView'} == \fPHP\Branches\forestBranch::DETAIL) ) {
						$this->GenerateView();
					} else {
						$this->GenerateListView();
					}
				} else {
					$this->GenerateListView();
				}
			}
		}
	}
	
	/**
	 * handle view files of sub record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function viewFilesAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'viewSubKey'), 'viewSubKey' );
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'subConstraintKey'), 'subConstraintKey' );
		
		if ($o_glob->Temp->{'subConstraintKey'} != null) {
			/* query sub constraint if we have sub constraint key in url parameters */
			$o_subconstraintTwig = new \fPHP\Twigs\subconstraintTwig;
			
			if (! ($o_subconstraintTwig->GetRecord(array($o_glob->Temp->{'subConstraintKey'}))) ) {
				throw new forestException(0x10001401, array($o_subconstraintTwig->fphp_Table));
			}
			
			/* get value for info columns when configured */
			$i_infoColumns = $o_glob->TablesInformation[$o_subconstraintTwig->TableUUID]['InfoColumns'];
			
			if ($o_glob->Temp->{'viewSubKey'} != null) {
				/* query sub record if we have view key in url parameters */
				$o_subrecordsTwig = new \fPHP\Twigs\subrecordsTwig;
				
				if (! ($o_subrecordsTwig->GetRecord(array($o_glob->Temp->{'viewSubKey'}))) ) {
					throw new forestException(0x10001401, array($o_subrecordsTwig->fphp_Table));
				}
				
				/* get amount of files of sub record */
				$o_filesTwig = new \fPHP\Twigs\filesTwig;
				
				$a_sqlAdditionalFilter = array(array('column' => 'ForeignUUID', 'value' => $o_subrecordsTwig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
				$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
				$o_files = $o_filesTwig->GetAllRecords(true);
				$o_glob->Temp->Del('SQLAdditionalFilter');
				
				/* create modal form for files of sub record */
				$o_glob->PostModalForm = new \fPHP\Forms\forestForm($o_filesTwig);
				$s_title = $o_glob->GetTranslation('Files', 1);
				$o_glob->PostModalForm->CreateModalForm($o_filesTwig, $s_title, false);
				
				if ($o_files->Twigs->Count() == 0) {
					/* add description element to show that no files exists for sub record */
					$o_description = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::DESCRIPTION);
					$o_description->Description = '<div class="alert alert-info">' . $o_glob->GetTranslation('NoFiles', 1) . '</div>';
					$o_description->NoFormGroup = true;
					
					$o_glob->PostModalForm->FormElements->Add($o_description);
				} else {
					$p_b_readonly = false;
					
					if ($o_glob->Security->SessionData->Exists('lastView')) {
						if ($o_glob->Security->SessionData->{'lastView'} == \fPHP\Branches\forestBranch::LIST) {
							$p_b_readonly = true;
						}
					}
					
					/* list files of sub record */
					$s_subTableHead = '';
					$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortFile', 1) . '</th>' . "\n";
					
					/* check versioning settings of twig */
					if ($o_glob->TablesInformation[$o_subconstraintTwig->TableUUID]['Versioning'] == 100) {
						$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortVersion', 1) . '</th>' . "\n";
					}
					
					/* render info columns when configured */
					if ($i_infoColumns == 10) {
						$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortCreated', 1) . '</th>' . "\n";
						$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortCreatedBy', 1) . '</th>' . "\n";
					} else if ($i_infoColumns == 100) {
						$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortModified', 1) . '</th>' . "\n";
						$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortModifiedBy', 1) . '</th>' . "\n";
					} else if ($i_infoColumns == 1000) {
						$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortCreated', 1) . '</th>' . "\n";
						$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortCreatedBy', 1) . '</th>' . "\n";
						$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortModified', 1) . '</th>' . "\n";
						$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortModifiedBy', 1) . '</th>' . "\n";
					}
					
					$s_subTableHead .= '<th>' . $o_glob->GetTranslation('formSubOptions', 1) . '</th>' . "\n";
					
					$s_subTableRows = '';
		
					foreach ($o_files->Twigs as $o_file) {
						$s_subTableRows .= '<tr';
							
						if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_file->UUID), array('ForeignUUID'))) {
							$s_subTableRows .= ' class="bg-warning"';
						}
							
						$s_subTableRows .= '>' . "\n";
							
						$s_subTableRows .=  '<td>' . $o_file->DisplayName . '</td>' . "\n";
						
						/* check versioning settings of twig */
						if ($o_glob->TablesInformation[$o_subconstraintTwig->TableUUID]['Versioning'] == 100) {
							$s_subTableRows .= '<td>' . $o_file->Major . $o_glob->Trunk->VersionDelimiter . $o_file->Minor . '</td>' . "\n";
						}
						
						/* render info columns when configured */
						if ($i_infoColumns == 10) {
							$s_value = '-';
							$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::DATETIMELOCAL, 'Created', $o_file, $o_file->fphp_Table . '_Created');
							$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
							
							$s_value = '-';
							$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::TEXT, 'CreatedBy', $o_file, $o_file->fphp_Table . '_CreatedBy');
							$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
						} else if ($i_infoColumns == 100) {
							$s_value = '-';
							$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::DATETIMELOCAL, 'Modified', $o_file, $o_file->fphp_Table . 'Modified');
							$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
							
							$s_value = '-';
							$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::TEXT, 'ModifiedBy', $o_file, $o_file->fphp_Table . '_ModifiedBy');
							$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
						} else if ($i_infoColumns == 1000) {
							$s_value = '-';
							$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::DATETIMELOCAL, 'Created', $o_file, $o_file->fphp_Table . '_Created');
							$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
							
							$s_value = '-';
							$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::TEXT, 'CreatedBy', $o_file, $o_file->fphp_Table . '_CreatedBy');
							$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
							
							$s_value = '-';
							$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::DATETIMELOCAL, 'Modified', $o_file, $o_file->fphp_Table . 'Modified');
							$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
							
							$s_value = '-';
							$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::TEXT, 'ModifiedBy', $o_file, $o_file->fphp_Table . '_ModifiedBy');
							$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
						}
						
						$s_options = '<span class="btn-group">' . "\n";
						
						$s_value = '';
						$s_folder = substr(pathinfo($o_file->Name, PATHINFO_FILENAME), 6, 2);
						$s_path = '';
	
						if (count($o_glob->URL->Branches) > 0) {
							foreach($o_glob->URL->Branches as $s_branch) {
								$s_path .= $s_branch . '/';
							}
						}
						
						$s_path .= $o_glob->URL->Branch . '/';
						
						$s_path = './trunk/' . $s_path . 'fphp_files/' . $s_folder . '/';
						
						if (is_dir($s_path)) {
							if (file_exists($s_path . $o_file->Name)) {
								$s_value = '<a href="' . $s_path . $o_file->Name . '" target="_blank" class="btn btn-light" title="' . $o_file->DisplayName . '" download="' . $o_file->DisplayName . '"><span class="fas fa-download"></span></a>' . "\n";
							}
						}
						
						$s_options .=  $s_value;
						
						/* check if we have files in history for current file record */
						$o_historyFilesTwig = new \fPHP\Twigs\filesTwig;
				
						$a_sqlAdditionalFilter = array(array('column' => 'ForeignUUID', 'value' => $o_file->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
						$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
						$i_historyFiles = $o_historyFilesTwig->GetCount(null, true);
						$o_glob->Temp->Del('SQLAdditionalFilter');
						
						if ($i_historyFiles > 0) {
							$a_parameters = $o_glob->URL->Parameters;
							unset($a_parameters['newKey']);
							unset($a_parameters['viewKey']);
							unset($a_parameters['viewSubKey']);
							unset($a_parameters['editKey']);
							unset($a_parameters['editFileKey']);
							unset($a_parameters['deleteKey']);
							unset($a_parameters['editSubKey']);
							unset($a_parameters['deleteSubKey']);
							unset($a_parameters['deleteFileKey']);
							unset($a_parameters['subConstraintKey']);
							$a_parameters['viewSubKey'] = $o_file->UUID;
							$a_parameters['subConstraintKey'] = $o_subconstraintTwig->UUID;
							
							if ($o_glob->Security->CheckUserPermission(null, 'viewFilesHistory')) {
								$s_options .=  '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'viewFilesHistory', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnHistoryText', 1) . ' (' . $i_historyFiles . ')"><span class="fas fa-history"></span></a>' . "\n";
							}
						}
						
						if (!$p_b_readonly) {
							$a_parameters = $o_glob->URL->Parameters;
							unset($a_parameters['newKey']);
							unset($a_parameters['viewKey']);
							unset($a_parameters['viewSubKey']);
							unset($a_parameters['editKey']);
							unset($a_parameters['editFileKey']);
							unset($a_parameters['deleteKey']);
							unset($a_parameters['editSubKey']);
							unset($a_parameters['deleteSubKey']);
							unset($a_parameters['deleteFileKey']);
							unset($a_parameters['subConstraintKey']);
							$a_parameters['editFileKey'] = $o_file->UUID;
							$a_parameters['subConstraintKey'] = $o_subconstraintTwig->UUID;
							
							if ($o_glob->Security->CheckUserPermission(null, 'replaceFile')) {
								$s_options .=  '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'replaceFile', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnUploadText', 1) . '"><span class="fas fa-upload"></span></a>' . "\n";
							}
							
							/* check versioning settings of twig */
							if ($o_glob->TablesInformation[$o_subconstraintTwig->TableUUID]['Versioning'] > 1) {
								if (!($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_file->UUID), array('ForeignUUID'))) {
									/* checkout link */
									$a_parameters = $o_glob->URL->Parameters;
									unset($a_parameters['newKey']);
									unset($a_parameters['viewKey']);
									unset($a_parameters['editKey']);
									unset($a_parameters['deleteKey']);
									unset($a_parameters['editSubKey']);
									unset($a_parameters['deleteSubKey']);
									unset($a_parameters['deleteFileKey']);
									unset($a_parameters['subConstraintKey']);
									$a_parameters['editKey'] = $o_file->UUID;
									
									if ($o_glob->Security->CheckUserPermission(null, 'checkout')) {
										$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'checkout', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnCheckoutText', 1) . '"><span class="fas fa-lock text-warning"></span></a>';
									}
								} else {
									/* checkin link */
									$a_parameters = $o_glob->URL->Parameters;
									unset($a_parameters['newKey']);
									unset($a_parameters['viewKey']);
									unset($a_parameters['editKey']);
									unset($a_parameters['deleteKey']);
									unset($a_parameters['editSubKey']);
									unset($a_parameters['deleteSubKey']);
									unset($a_parameters['deleteFileKey']);
									unset($a_parameters['subConstraintKey']);
									$a_parameters['editKey'] = $o_file->UUID;
									
									if ($o_glob->Security->CheckUserPermission(null, 'checkin')) {
										$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'checkin', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnCheckinText', 1) . '"><span class="fas fa-lock-open text-primary"></span></a>';
									}
								}
							}
							
							$a_parameters = $o_glob->URL->Parameters;
							unset($a_parameters['newKey']);
							unset($a_parameters['viewKey']);
							unset($a_parameters['editKey']);
							unset($a_parameters['editSubKey']);
							unset($a_parameters['deleteKey']);
							unset($a_parameters['deleteSubKey']);
							$a_parameters['deleteFileKey'] = $o_file->UUID;
							
							if ($o_glob->Security->CheckUserPermission(null, 'delete')) {
								$s_options .=  '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'delete', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="fas fa-trash text-danger"></span></a>' . "\n";
							}
						}

						
						$s_options .= '</span>' . "\n";
						$s_subTableRows .=  '<td>' . $s_options . '</td>' . "\n";
						$s_subTableRows .=  '</tr>' . "\n";
					}
					
					$s_subFormItemContent = new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEWITEMCONTENT, array('', $s_subTableHead, $s_subTableRows));
					
					/* add description element to show existing files for sub record */
					/* use template to render files of a record */
					$o_description = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::DESCRIPTION);
					$o_description->Description = strval(new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEW, array($s_subFormItemContent)));
					$o_description->NoFormGroup = true;
					
					$o_glob->PostModalForm->FormElements->Add($o_description);
				}
				
				if ($o_glob->Security->SessionData->Exists('last_filter')) {
					$o_glob->Security->SessionData->Add($o_glob->Security->SessionData->{'last_filter'}, 'filter');
				}
			}
		}
		
		if ($o_glob->Security->SessionData->Exists('lastView')) {
			$this->StandardView = $o_glob->Security->SessionData->{'lastView'};
		}
		
		$this->SetNextAction('init');
	}
	
	/**
	 * handle view files history of file record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function viewFilesHistoryAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'viewSubKey'), 'viewSubKey' );
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'subConstraintKey'), 'subConstraintKey' );
		
		/* sub constraint key can be the uuid of the sub constraint or the table uuid */
		if ($o_glob->Temp->{'subConstraintKey'} != null) {
			/* query sub constraint if we have sub constraint key in url parameters */
			$o_subconstraintTwig = new \fPHP\Twigs\subconstraintTwig;
			
			if (! ($o_subconstraintTwig->GetRecord(array($o_glob->Temp->{'subConstraintKey'}))) ) {
				$i_infoColumns = $o_glob->TablesInformation[$o_glob->Temp->{'subConstraintKey'}]['InfoColumns'];
				$i_versioning = $o_glob->TablesInformation[$o_glob->Temp->{'subConstraintKey'}]['Versioning'];
				$s_tableUUID = $o_glob->Temp->{'subConstraintKey'};
			} else {
				/* get value for info columns when configured */
				$i_infoColumns = $o_glob->TablesInformation[$o_subconstraintTwig->TableUUID]['InfoColumns'];
				$i_versioning = $o_glob->TablesInformation[$o_subconstraintTwig->TableUUID]['Versioning'];
				$s_tableUUID = $o_subconstraintTwig->TableUUID;
			}
		}
		
		if ( ($o_glob->Temp->Exists('viewSubKey')) && ($o_glob->Temp->{'viewSubKey'} != null)) {
			/* query sub record if we have view key in url parameters */
			$o_originFile = new \fPHP\Twigs\filesTwig;
			
			if (! ($o_originFile->GetRecord(array($o_glob->Temp->{'viewSubKey'}))) ) {
				throw new forestException(0x10001401, array($o_originFile->fphp_Table));
			}
			
			/* get amount of files of sub record */
			$o_filesTwig = new \fPHP\Twigs\filesTwig;
			
			$a_sqlAdditionalFilter = array(array('column' => 'ForeignUUID', 'value' => $o_originFile->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
			$a_sqlAdditionalSorts = array('Major' => false, 'Minor' => false);
			$o_glob->Temp->Add($a_sqlAdditionalSorts, 'SQLAdditionalSorts');
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$o_files = $o_filesTwig->GetAllRecords(true);
			$o_glob->Temp->Del('SQLAdditionalFilter');
			$o_glob->Temp->Del('SQLAdditionalSorts');
			
			/* create modal form for files of sub record */
			$o_glob->PostModalForm = new \fPHP\Forms\forestForm($o_filesTwig);
			$s_title = $o_glob->GetTranslation('FilesHistory', 1);
			$o_glob->PostModalForm->CreateModalForm($o_filesTwig, $s_title, false);
			
			if ($o_files->Twigs->Count() == 0) {
				/* add description element to show that no files exists for sub record */
				$o_description = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::DESCRIPTION);
				$o_description->Description = '<div class="alert alert-info">' . $o_glob->GetTranslation('NoFiles', 1) . '</div>';
				$o_description->NoFormGroup = true;
				
				$o_glob->PostModalForm->FormElements->Add($o_description);
			} else {
				$p_b_readonly = false;
				
				if ($o_glob->Security->SessionData->Exists('lastView')) {
					if ($o_glob->Security->SessionData->{'lastView'} == \fPHP\Branches\forestBranch::LIST) {
						$p_b_readonly = true;
					}
				}
				
				/* list files of sub record */
				$s_subTableHead = '';
				$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortFile', 1) . '</th>' . "\n";
				
				/* check versioning settings of twig */
				if ($o_glob->TablesInformation[$s_tableUUID]['Versioning'] == 100) {
					$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortVersion', 1) . '</th>' . "\n";
				}
				
				/* render info columns when configured */
				if ($i_infoColumns == 10) {
					$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortCreated', 1) . '</th>' . "\n";
					$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortCreatedBy', 1) . '</th>' . "\n";
				} else if ($i_infoColumns == 100) {
					$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortModified', 1) . '</th>' . "\n";
					$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortModifiedBy', 1) . '</th>' . "\n";
				} else if ($i_infoColumns == 1000) {
					$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortCreated', 1) . '</th>' . "\n";
					$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortCreatedBy', 1) . '</th>' . "\n";
					$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortModified', 1) . '</th>' . "\n";
					$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortModifiedBy', 1) . '</th>' . "\n";
				}
				
				$s_subTableHead .= '<th>' . $o_glob->GetTranslation('formSubOptions', 1) . '</th>' . "\n";
				
				$s_subTableRows = '';
	
				foreach ($o_files->Twigs as $o_file) {
					$s_subTableRows .= '<tr';
					$s_subTableRows .= '>' . "\n";
						
					$s_subTableRows .=  '<td>' . $o_file->DisplayName . '</td>' . "\n";
					
					/* check versioning settings of twig */
					if ($o_glob->TablesInformation[$s_tableUUID]['Versioning'] == 100) {
						$s_subTableRows .= '<td>' . $o_file->Major . $o_glob->Trunk->VersionDelimiter . $o_file->Minor . '</td>' . "\n";
					}
					
					/* render info columns when configured */
					if ($i_infoColumns == 10) {
						$s_value = '-';
						$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::DATETIMELOCAL, 'Created', $o_file, $o_file->fphp_Table . '_Created');
						$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
						
						$s_value = '-';
						$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::TEXT, 'CreatedBy', $o_file, $o_file->fphp_Table . '_CreatedBy');
						$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
					} else if ($i_infoColumns == 100) {
						$s_value = '-';
						$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::DATETIMELOCAL, 'Modified', $o_file, $o_file->fphp_Table . 'Modified');
						$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
						
						$s_value = '-';
						$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::TEXT, 'ModifiedBy', $o_file, $o_file->fphp_Table . '_ModifiedBy');
						$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
					} else if ($i_infoColumns == 1000) {
						$s_value = '-';
						$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::DATETIMELOCAL, 'Created', $o_file, $o_file->fphp_Table . '_Created');
						$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
						
						$s_value = '-';
						$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::TEXT, 'CreatedBy', $o_file, $o_file->fphp_Table . '_CreatedBy');
						$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
						
						$s_value = '-';
						$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::DATETIMELOCAL, 'Modified', $o_file, $o_file->fphp_Table . 'Modified');
						$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
						
						$s_value = '-';
						$this->ListViewRenderColumnValue($s_value, \fPHP\Forms\forestFormElement::TEXT, 'ModifiedBy', $o_file, $o_file->fphp_Table . '_ModifiedBy');
						$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
					}
					
					$s_options = '<span class="btn-group">' . "\n";
					
					$s_value = '';
					$s_folder = substr(pathinfo($o_file->Name, PATHINFO_FILENAME), 6, 2);
					$s_path = '';

					if (count($o_glob->URL->Branches) > 0) {
						foreach($o_glob->URL->Branches as $s_branch) {
							$s_path .= $s_branch . '/';
						}
					}
					
					$s_path .= $o_glob->URL->Branch . '/';
					
					$s_path = './trunk/' . $s_path . 'fphp_files/' . $s_folder . '/';
					
					if (is_dir($s_path)) {
						if (file_exists($s_path . $o_file->Name)) {
							$s_value = '<a href="' . $s_path . $o_file->Name . '" target="_blank" class="btn btn-light" title="' . $o_file->DisplayName . '" download="' . $o_file->DisplayName . '"><span class="fas fa-download"></span></a>' . "\n";
						}
					}
					
					$s_options .=  $s_value;
					
					if ( (!$p_b_readonly) && ($o_glob->TablesInformation[$s_tableUUID]['Versioning'] == 100) ) {
						$a_parameters = $o_glob->URL->Parameters;
						unset($a_parameters['newKey']);
						unset($a_parameters['viewKey']);
						unset($a_parameters['viewSubKey']);
						unset($a_parameters['editKey']);
						unset($a_parameters['editFileKey']);
						unset($a_parameters['deleteKey']);
						unset($a_parameters['editSubKey']);
						unset($a_parameters['deleteSubKey']);
						unset($a_parameters['deleteFileKey']);
						unset($a_parameters['subConstraintKey']);
						$a_parameters['editFileKey'] = $o_file->UUID;
						$a_parameters['subConstraintKey'] = $s_tableUUID;
						
						if ($o_glob->Security->CheckUserPermission(null, 'restoreFile')) {
							$s_options .=  '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'restoreFile', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnRestoreText', 1) . '"><span class="fas fa-redo"></span></a>' . "\n";
						}
					}

					
					$s_options .= '</span>' . "\n";
					$s_subTableRows .=  '<td>' . $s_options . '</td>' . "\n";
					$s_subTableRows .=  '</tr>' . "\n";
				}
				
				$s_subFormItemContent = new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEWITEMCONTENT, array('', $s_subTableHead, $s_subTableRows));
				
				/* add description element to show existing files for sub record */
				/* use template to render files of a record */
				$o_description = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::DESCRIPTION);
				$o_description->Description = strval(new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEW, array($s_subFormItemContent)));
				$o_description->NoFormGroup = true;
				
				$o_glob->PostModalForm->FormElements->Add($o_description);
			}
			
			if ($o_glob->Security->SessionData->Exists('last_filter')) {
				$o_glob->Security->SessionData->Add($o_glob->Security->SessionData->{'last_filter'}, 'filter');
			}
		}
		
		if ($o_glob->Security->SessionData->Exists('lastView')) {
			$this->StandardView = $o_glob->Security->SessionData->{'lastView'};
		}
		
		$this->SetNextAction('init');
	}
	
	
	/**
	 * handle new record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function NewRecord() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'newKey'), 'newKey' );
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'subConstraintKey'), 'subConstraintKey' );
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			if ( ($o_glob->Temp->Exists('newKey')) && ($o_glob->Temp->{'newKey'} != null) && ($o_glob->Temp->Exists('subConstraintKey')) && ($o_glob->Temp->{'subConstraintKey'} != null) ) {
				if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_glob->Temp->{'newKey'}), array('ForeignUUID'))) {
					/* check if user is the same user who has checked out the record */
					if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
						throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
					}
				}
				
				/* add new sub record */
				$this->RenderNewSubRecordForm();
				$this->StandardView = \fPHP\Branches\forestBranch::DETAIL; /* because it only makes sense if we stay in detail view, when we open modal read only form for record */
			} else {
				/* add new record */
				$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig, true);
				$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('NewModalTitle', 1);
			}
		} else {
			if ( (array_key_exists('sys_fphp_subConstraintKey', $_POST)) || (array_key_exists('sys_fphp_newKey', $_POST)) ) {
				/* check posted data for new sub record */
				
				$o_glob->HeadTwig = $this->Twig;
				$this->Twig = new \fPHP\Twigs\subrecordsTwig;
				$o_subconstraintTwig = new \fPHP\Twigs\subconstraintTwig;
				
				/* check if posted sub constraint uuid really exists */
				if (! ($o_subconstraintTwig->GetRecord(array($_POST['sys_fphp_subConstraintKey']))) ) {
					throw new forestException(0x10001402);
				}
				
				$s_tempTable = array_search($o_subconstraintTwig->TableUUID, $o_glob->Tables);
				\fPHP\Helper\forestStringLib::RemoveTablePrefix($s_tempTable);
				$s_foo = '\\fPHP\\Twigs\\' . $s_tempTable . 'Twig';
				$o_headTwig = new $s_foo;
				
				/* check if posted uuid matches with head record */
				if (! ($o_headTwig->GetRecord(array($_POST['sys_fphp_newKey']))) ) {
					throw new forestException(0x10001402);
				}
				
				if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_headTwig->UUID), array('ForeignUUID'))) {
					/* check if user is the same user who has checked out the record */
					if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
						throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
					}
				}
				
				$s_tempTable = array_search($o_subconstraintTwig->SubTableUUID->PrimaryValue, $o_glob->Tables);
				\fPHP\Helper\forestStringLib::RemoveTablePrefix($s_tempTable);
				$s_foo = '\\fPHP\\Twigs\\' . $s_tempTable . 'Twig';
				$o_joinTwig = new $s_foo;
				
				/* check if selected uuid matches with join record */
				if (! ($o_joinTwig->GetRecord(array($_POST['sys_fphp_subconstraint_Lookup']))) ) {
					throw new forestException(0x10001402);
				}
				
				$this->TransferPOST_Twig();
				$this->Twig->HeadUUID = $_POST['sys_fphp_newKey'];
				$this->Twig->JoinUUID = $_POST['sys_fphp_subconstraint_Lookup'];
				
				/* set values for info columns when configured */
				$i_infoColumns = $o_glob->TablesInformation[$o_glob->HeadTwig->fphp_TableUUID]['InfoColumns'];
				
				if ($i_infoColumns == 10) {
					$this->Twig->Created = new \fPHP\Helper\forestDateTime;
					$this->Twig->CreatedBy = $o_glob->Security->UserUUID;
				} else if ($i_infoColumns == 100) {
					$this->Twig->Modified = new \fPHP\Helper\forestDateTime;
					$this->Twig->ModifiedBy = $o_glob->Security->UserUUID;
				} else if ($i_infoColumns == 1000) {
					$this->Twig->Created = new \fPHP\Helper\forestDateTime;
					$this->Twig->CreatedBy = $o_glob->Security->UserUUID;
					$this->Twig->Modified = new \fPHP\Helper\forestDateTime;
					$this->Twig->ModifiedBy = $o_glob->Security->UserUUID;
				}
				
				/* set identifier when configured */
				if (issetStr($o_subconstraintTwig->IdentifierStart)) {
					/* get amount of current sub records */
					$o_subRecords = $o_headTwig->QuerySubRecords($o_subconstraintTwig);
					
					if ($o_subRecords->Twigs->Count() <= 0) {
						/* first new sub record, take identifier start from sub constraint record */
						$this->Twig->Identifier = $o_subconstraintTwig->IdentifierStart;
					} else {
						/* get last sub record's identifier */
						$this->Twig->Identifier = $o_subRecords->Twigs->{($o_subRecords->Twigs->Count() - 1)}->Identifier;
						
						/* increase identifier */
						$this->Twig->Identifier = \fPHP\Helper\forestStringLib::IncreaseIdentifier($this->Twig->Identifier, $o_subconstraintTwig->IdentifierIncrement);
						
						/* check identifier result */
						if ($this->Twig->Identifier == 'INVALID') {
							throw new forestException(0x10001F13, array($o_subRecords->Twigs->{($o_subRecords->Twigs->Count() - 1)}->Identifier));
						} else if ($this->Twig->Identifier == 'OVERFLOW') {
							throw new forestException(0x10001F14, array($o_subRecords->Twigs->{($o_subRecords->Twigs->Count() - 1)}->Identifier));
						}
					}
				}
				
				if (method_exists($this, 'beforeNewSubAction')) {
					$this->beforeNewSubAction();
				}
				
				/* insert record */
				$i_result = $this->Twig->InsertRecord();
				
				if (method_exists($this, 'afterNewSubAction')) {
					$this->afterNewSubAction();
				}
				
				/* evaluate result */
				if ($i_result == -1) {
					$this->UndoFilesEntries();
					$this->CreateLogEntry('could not create new sub record - unique issue - ' . $o_glob->Temp->{'UniqueIssue'}, $this->Twig);
					throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
				} else if ($i_result == 0) {
					$this->UndoFilesEntries();
					$this->CreateLogEntry('could not create new sub record', $this->Twig);
					throw new forestException(0x10001402);
				} else if ($i_result == 1) {
					$this->CreateLogEntry('new sub record created', $this->Twig);
					$o_glob->SystemMessages->Add(new forestException(0x10001404));
				}
				
				/* handle uploads */
				$this->TransferFILES_Twig();
				
				$this->Twig = $o_glob->HeadTwig;
				$this->StandardView = \fPHP\Branches\forestBranch::DETAIL; /* because it only makes sense if we stay in detail view, when we open modal read only form for record */
			} else {
				/* check posted data for new record */
				$this->TransferPOST_Twig();
				
				/* set values for info columns when configured */
				$i_infoColumns = $o_glob->TablesInformation[$this->Twig->fphp_TableUUID]['InfoColumns'];
				
				if ($i_infoColumns == 10) {
					$this->Twig->Created = new \fPHP\Helper\forestDateTime;
					$this->Twig->CreatedBy = $o_glob->Security->UserUUID;
				} else if ($i_infoColumns == 100) {
					$this->Twig->Modified = new \fPHP\Helper\forestDateTime;
					$this->Twig->ModifiedBy = $o_glob->Security->UserUUID;
				} else if ($i_infoColumns == 1000) {
					$this->Twig->Created = new \fPHP\Helper\forestDateTime;
					$this->Twig->CreatedBy = $o_glob->Security->UserUUID;
					$this->Twig->Modified = new \fPHP\Helper\forestDateTime;
					$this->Twig->ModifiedBy = $o_glob->Security->UserUUID;
				}
				
				/* set and increment identifier when configured */
				if (issetStr($o_glob->TablesInformation[$this->Twig->fphp_TableUUID]['Identifier']->PrimaryValue)) {
					$o_identifierTwig = new \fPHP\Twigs\identifierTwig;
					
					/* get identifier record */
					if (! ($o_identifierTwig->GetRecord(array($o_glob->TablesInformation[$this->Twig->fphp_TableUUID]['Identifier']->PrimaryValue))) ) {
						throw new forestException(0x10001401, array($o_identifierTwig->fphp_Table));
					}
					
					/* set identifier for new record */
					$this->Twig->Identifier = $o_identifierTwig->IdentifierNext;
					
					/* increase identifier next */
					$o_identifierTwig->IdentifierNext = \fPHP\Helper\forestStringLib::IncreaseIdentifier($o_identifierTwig->IdentifierNext, $o_identifierTwig->IdentifierIncrement);
					
					/* check identifier result */
					if ($o_identifierTwig->IdentifierNext == 'INVALID') {
						throw new forestException(0x10001F13, array($this->Twig->Identifier));
					} else if ($o_identifierTwig->IdentifierNext == 'OVERFLOW') {
						throw new forestException(0x10001F14, array($this->Twig->Identifier));
					}
					
					/* update identifier record */
					$i_result = $o_identifierTwig->UpdateRecord();
					
					/* evaluate result */
					if ($i_result == -1) {
						throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
					}
				}
				
				if (method_exists($this, 'beforeNewAction')) {
					$this->beforeNewAction();
				}
				
				/* insert record */
				$i_result = $this->Twig->InsertRecord();
				
				if (method_exists($this, 'afterNewAction')) {
					$this->afterNewAction();
				}
				
				/* evaluate result */
				if ($i_result == -1) {
					$this->UndoFilesEntries();
					$this->CreateLogEntry('could not create new record - unique issue - ' . $o_glob->Temp->{'UniqueIssue'}, $this->Twig);
					throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
				} else if ($i_result == 0) {
					$this->UndoFilesEntries();
					$this->CreateLogEntry('could not create new record', $this->Twig);
					throw new forestException(0x10001402);
				} else if ($i_result == 1) {
					$this->CreateLogEntry('new record created', $this->Twig);
					$o_glob->SystemMessages->Add(new forestException(0x10001404));
					
				}
				
				/* handle uploads */
				$this->TransferFILES_Twig();
			}
		}
		
		if (isset($this->KeepFilter)) {
			if ($this->KeepFilter->value) {
				if ($o_glob->Security->SessionData->Exists('last_filter')) {
					$o_glob->Security->SessionData->Add($o_glob->Security->SessionData->{'last_filter'}, 'filter');
				}
			}
		}
		
		$this->SetNextAction('init');
	}
	
	/**
	 * render modal form for new sub record
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function RenderNewSubRecordForm() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$o_subconstraintTwig = new \fPHP\Twigs\subconstraintTwig;
		$o_glob->PostModalForm = new \fPHP\Forms\forestForm($o_subconstraintTwig);
		
		/* get table */
		$o_tableTwig = new \fPHP\Twigs\tableTwig;
		
		/* query table record */
		if (!($o_tableTwig->GetRecordPrimary(array($o_subconstraintTwig->fphp_Table), array('Name')))) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$o_formelementTwig = new \fPHP\Twigs\formelementTwig;
		
		/* look in tablefields for formobject, if not get the standard by formelementuuid */
		if (!($o_formelementTwig->GetRecordPrimary(array('form'), array('Name')))) {
			throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
		}
		
		$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
		$s_formObjectJSONsettings = '';
		
		if (!($o_tablefieldTwig->GetRecordPrimary(array($o_tableTwig->UUID, $o_formelementTwig->UUID), array('TableUUID', 'FormElementUUID')))) {
			/* no tablefield for table, take standard */
			$s_formObjectJSONsettings = $o_formelementTwig->JSONEncodedSettings;
		} else {
			$s_formObjectJSONsettings = $o_tablefieldTwig->JSONEncodedSettings;
		}
		
		/* create formobject(check $this->Readonly->value), modal object and maybe tab objects */
		$o_glob->PostModalForm->FormObject->loadJSON($s_formObjectJSONsettings);
		$o_glob->PostModalForm->FormModalConfiguration->loadJSON($s_formObjectJSONsettings);
		$o_glob->PostModalForm->FormTabConfiguration->loadJSON($s_formObjectJSONsettings);
		
		$o_glob->PostModalForm->FormModalConfiguration->ModalId = $o_glob->PostModalForm->FormObject->Id . 'Modal';
		$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('NewSubModalTitle', 1);
		$o_glob->PostModalForm->FormTabConfiguration->Tab = false;
		
		if (! ($o_subconstraintTwig->GetRecord(array($o_glob->Temp->{'subConstraintKey'}))) ) {
			throw new forestException(0x10001401, array($o_subconstraintTwig->fphp_Table));
		}
		
		/* create lookup for choosing subrecords as input list */
		$s_tempTable = array_search($o_subconstraintTwig->SubTableUUID->PrimaryValue, $o_glob->Tables);
		\fPHP\Helper\forestStringLib::RemoveTablePrefix($s_tempTable);
		$s_foo = '\\fPHP\\Twigs\\' . $s_tempTable . 'Twig';
		$o_lookupTwig = new $s_foo;
		$o_lookupData = new \fPHP\Helper\forestLookupData($o_lookupTwig->fphp_Table, $o_lookupTwig->fphp_Primary, $o_lookupTwig->fphp_View);
		
		$o_lookup = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::LOOKUP);
		$o_lookup->Label = $o_glob->GetTranslation($o_glob->BranchTree['Id'][$o_glob->BranchTree['Name'][$s_tempTable]]['Title'], 1) . ' ' . $o_glob->GetTranslation('BranchTitleRecord', 1);
		$o_lookup->Id = $o_subconstraintTwig->fphp_Table . '_Lookup';
		$o_lookup->Options = $o_lookupData->CreateOptionsArray();
		$o_lookup->Required = true;
		$o_glob->PostModalForm->FormElements->Add($o_lookup);
		
		$o_glob->PostModalForm->FormObject->ValRequiredMessage = $o_glob->GetTranslation('ValRequiredMessage', 1);
		$o_glob->PostModalForm->FormObject->ValRules->Add(new \fPHP\Forms\forestFormValidationRule($o_lookup->Id, 'required', 'true', 'NULL', 'false'));
		
		$this->AddAdditionalSubRecordFormElements($o_subconstraintTwig);
		
		$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
		$o_hidden->Id = 'sys_fphp_subConstraintKey';
		$o_hidden->Value = $o_glob->Temp->{'subConstraintKey'};
		$o_glob->PostModalForm->FormElements->Add($o_hidden);
		
		$o_hidden2 = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
		$o_hidden2->Id = 'sys_fphp_newKey';
		$o_hidden2->Value = $o_glob->Temp->{'newKey'};
		$o_glob->PostModalForm->FormElements->Add($o_hidden2);
		
		/* render submit button */
		if (!($o_formelementTwig->GetRecordPrimary(array('submit'), array('Name')))) {
			throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
		}
		
		$o_button = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::BUTTON);
		$o_button->loadJSON($o_formelementTwig->JSONEncodedSettings);
		$o_button->ButtonText = htmlspecialchars_decode($o_button->ButtonText);
		$o_glob->PostModalForm->FormFooterElements->Add($o_button);
		
		/* render cancel button */
		if (!($o_formelementTwig->GetRecordPrimary(array('cancel'), array('Name')))) {
			throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
		}
		
		$o_cancel = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::BUTTON);
		$o_cancel->loadJSON($o_formelementTwig->JSONEncodedSettings);
		$o_cancel->ButtonText = htmlspecialchars_decode($o_cancel->ButtonText);
		$o_glob->PostModalForm->FormFooterElements->Add($o_cancel);
		
		$o_glob->PostModalForm->AddFormKey();
		$o_glob->PostModalForm->Automatic = true;
	}
	
	/**
	 * add additional sub record field form elements to global post modal form
	 *
	 * @param subconstraintTwig $p_o_subconstraintTwig  sub constraint object where a lookup and form elements will be build dynamically
	 * @param subrecordsTwig $p_o_subrecordsTwig  record data of sub record to give values to form elements
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function AddAdditionalSubRecordFormElements(\fPHP\Twigs\subconstraintTwig $p_o_subconstraintTwig, \fPHP\Twigs\subrecordsTwig $p_o_subrecordsTwig = null) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		if ($p_o_subrecordsTwig == null) {
			$p_o_subrecordsTwig = new \fPHP\Twigs\subrecordsTwig;
		}
		
		/* add tablefields in relation to the sub constraint */
		foreach ($o_glob->TablefieldsDictionary as $o_tableFieldDictionaryObject) {
			if ($o_tableFieldDictionaryObject->TableUUID == $p_o_subconstraintTwig->UUID) {
				$s_formElement = $o_tableFieldDictionaryObject->FormElementName;
				$s_forestData = $o_tableFieldDictionaryObject->ForestDataName;
			
				if ( ($s_formElement == \fPHP\Forms\forestFormElement::CAPTCHA) || ($s_forestData == 'forestCombination') ) {
					continue;
				}
				
				$o_formElement = new \fPHP\Forms\forestFormElement($o_tableFieldDictionaryObject->FormElementName);
				$o_formElement->loadJSON($o_tableFieldDictionaryObject->JSONEncodedSettings);
				$o_formElement->Name = 'sys_fphp_subrecords_' . $o_tableFieldDictionaryObject->SubRecordField;
				
				/* set hidden id for richtext element */
				if ($s_formElement == \fPHP\Forms\forestFormElement::RICHTEXT) {
					$o_formElement->HiddenId = 'sys_fphp_subrecords_' . $o_tableFieldDictionaryObject->SubRecordField;
				}
				
				/* set form id, uploader and deleter for dropzone element */
				if ($s_formElement == \fPHP\Forms\forestformElement::DROPZONE) {
					$o_formElement->FormId = $o_glob->PostModalForm->FormObject->Id;
					$o_formElement->URIFileUploader = \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'fphp_upload');
					$o_formElement->URIFileDeleter = \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'fphp_upload_delete');
				}
				
				if (\fPHP\Helper\forestStringLib::EndsWith($o_formElement->Id, '[]')) {
					$o_formElement->Name .= '[]';
				}
				
				if ($s_formElement == \fPHP\Forms\forestFormElement::LOOKUP) {
					$s_JSONEncodedSettings = str_replace('&quot;', '"', $o_tableFieldDictionaryObject->JSONEncodedSettings);
					$a_settings = json_decode($s_JSONEncodedSettings, true);
					$a_forestLookupDataFilter = array();
					$s_forestLookupDataConcat = ' - ';
					
					if (array_key_exists('forestLookupDataFilter', $a_settings)) {
						$a_forestLookupDataFilter = $a_settings['forestLookupDataFilter'];
					}
					
					if (array_key_exists('forestLookupDataConcat', $a_settings)) {
						$s_forestLookupDataConcat = $a_settings['forestLookupDataConcat'];
					}
					
					$o_forestLookupData = new \fPHP\Helper\forestLookupData($a_settings['forestLookupDataTable'], $a_settings['forestLookupDataPrimary'], $a_settings['forestLookupDataLabel'], $a_forestLookupDataFilter, $s_forestLookupDataConcat);
					$p_o_subrecordsTwig->{$o_tableFieldDictionaryObject->SubRecordField}->SetLookupData($o_forestLookupData);
					$o_formElement->Options = $p_o_subrecordsTwig->{$o_tableFieldDictionaryObject->SubRecordField}->CreateOptionsArray();
				}
				
				$s_value = '';
				
				if ((!$p_o_subrecordsTwig->IsEmpty()) && (property_exists($p_o_subrecordsTwig, $o_tableFieldDictionaryObject->SubRecordField))) {
					/* maybe other casts necessary depending on sqltype info */
					if (is_a($p_o_subrecordsTwig->{$o_tableFieldDictionaryObject->SubRecordField}, '\\fPHP\Helper\\forestDateTime')) {
						if ($s_formElement == \fPHP\Forms\forestFormElement::DATETIMELOCAL) {
							$s_value = $p_o_subrecordsTwig->{$o_tableFieldDictionaryObject->SubRecordField}->ToString('Y-m-d\TH:i:s');
						} else if ($s_formElement == \fPHP\Forms\forestFormElement::DATE) {
							$s_value = $p_o_subrecordsTwig->{$o_tableFieldDictionaryObject->SubRecordField}->ToString('Y-m-d');
						} else if ($s_formElement == \fPHP\Forms\forestFormElement::MONTH) {
							$s_value = $p_o_subrecordsTwig->{$o_tableFieldDictionaryObject->SubRecordField}->ToString('Y-m');
						} else if ($s_formElement == \fPHP\Forms\forestFormElement::TIME) {
							$s_value = $p_o_subrecordsTwig->{$o_tableFieldDictionaryObject->SubRecordField}->ToString('H:i:s');
						} else if ($s_formElement == \fPHP\Forms\forestFormElement::WEEK) {
							$s_value = $p_o_subrecordsTwig->{$o_tableFieldDictionaryObject->SubRecordField}->ToString('Y-\WW');
						} else {
							$s_value = $p_o_subrecordsTwig->{$o_tableFieldDictionaryObject->SubRecordField}->ToString();
						}
					} else if (is_a($p_o_subrecordsTwig->{$o_tableFieldDictionaryObject->SubRecordField}, '\\fPHP\Helper\\forestLookupData')) {
						$s_value = $p_o_subrecordsTwig->{$o_tableFieldDictionaryObject->SubRecordField}->PrimaryValue;
					} else {
						$s_value = strval($p_o_subrecordsTwig->{$o_tableFieldDictionaryObject->SubRecordField});
					}
				}
				
				$o_formElement->Value = $s_value;
				
				$o_glob->PostModalForm->FormElements->Add($o_formElement);
				
				/* get validation rules of tablefield and iterate them */
				$o_querySelect = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::SELECT, 'sys_fphp_tablefield_validationrule');
				
				$column_A = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_A->Column = '*';
				
				$o_querySelect->Query->Columns->Add($column_A);
				
				$join_A = new \fPHP\Base\forestSQLJoin($o_querySelect);
					$join_A->JoinType = 'INNER JOIN';
					$join_A->Table = 'sys_fphp_validationrule';

				$relation_A = new \fPHP\Base\forestSQLRelation($o_querySelect);
				
				$column_B = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_B->Column = 'ValidationruleUUID';
					
				$column_C = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_C->Column = 'UUID';
					$column_C->Table = $join_A->Table;
				
				$relation_A->ColumnLeft = $column_B;
				$relation_A->ColumnRight = $column_C;
				$relation_A->Operator = '=';
				
				$join_A->Relations->Add($relation_A);
					
				$o_querySelect->Query->Joins->Add($join_A);
				
				$column_D = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_D->Column = 'TablefieldUUID';
				
				$where_A = new \fPHP\Base\forestSQLWhere($o_querySelect);
					$where_A->Column = $column_D;
					$where_A->Value = $where_A->ParseValue($o_tableFieldDictionaryObject->UUID);
					$where_A->Operator = '=';
				
				$o_querySelect->Query->Where->Add($where_A);
				
				$o_result = $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_querySelect, false, false);
				
				foreach ($o_result as $o_row) {
					/* render validation rules */
					$s_param01 = ( ((empty($o_row['ValidationRuleParam01'])) || ($o_row['ValidationRuleParam01'] == 'NULL')) ? null : $o_row['ValidationRuleParam01'] );
					$s_param02 = ( ((empty($o_row['ValidationRuleParam02'])) || ($o_row['ValidationRuleParam02'] == 'NULL')) ? null : $o_row['ValidationRuleParam02'] );
					$s_autoRequired = ( (($o_row['ValidationRuleRequired'] == 1)) ? 'true' : 'false' );
					
					$o_glob->PostModalForm->FormObject->ValRules->Add(new \fPHP\Forms\forestFormValidationRule('sys_fphp_subconstraint_' . $o_tableFieldDictionaryObject->FieldName, $o_row['Name'], $s_param01, $s_param02, $s_autoRequired));
				}
			}
		}
	}
	
	
	/**
	 * handle edit record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function EditRecord() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'editKey'), 'editKey' );
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'editSubKey'), 'editSubKey' );
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'subConstraintKey'), 'subConstraintKey' );
		
		if ($o_glob->IsPost) {
			$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
			
			if ( (array_key_exists('sys_fphp_editSubKey', $_POST)) && (array_key_exists('sys_fphp_subConstraintKey', $_POST)) ) {
				/* check posted data for edit sub record */
				
				$o_glob->HeadTwig = $this->Twig;
				$this->Twig = new \fPHP\Twigs\subrecordsTwig;
				
				/* query sub record */
				if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_editSubKey']))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($this->Twig->HeadUUID), array('ForeignUUID'))) {
					/* check if user is the same user who has checked out the record */
					if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
						throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
					}
				}
				
				if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($this->Twig->UUID), array('ForeignUUID'))) {
					/* check if user is the same user who has checked out the record */
					if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
						throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
					}
				}
				
				$this->CreateLogEntry('sub record before edit', $this->Twig);
				
				$this->TransferPOST_Twig();
				
				/* set values for info columns when configured */
				$i_infoColumns = $o_glob->TablesInformation[$o_glob->HeadTwig->fphp_TableUUID]['InfoColumns'];
				
				if ($i_infoColumns == 100) {
					$this->Twig->Modified = new \fPHP\Helper\forestDateTime;
					$this->Twig->ModifiedBy = $o_glob->Security->UserUUID;
				} else if ($i_infoColumns == 1000) {
					$this->Twig->Modified = new \fPHP\Helper\forestDateTime;
					$this->Twig->ModifiedBy = $o_glob->Security->UserUUID;
				}
				
				if (method_exists($this, 'beforeEditSubAction')) {
					$this->beforeEditSubAction();
				}
				
				/* edit recrod */
				$i_result = $this->Twig->UpdateRecord();
				
				if (method_exists($this, 'afterEditSubAction')) {
					$this->afterEditSubAction();
				}
				
				/* evaluate result */
				if ($i_result == -1) {
					$this->UndoFilesEntries();
					$this->CreateLogEntry('could not edit sub record - unique issue - ' . $o_glob->Temp->{'UniqueIssue'}, $this->Twig);
					throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
				} else if ($i_result == 0) {
					$o_glob->SystemMessages->Add(new forestException(0x10001406));
				} else if ($i_result == 1) {
					$this->CreateLogEntry('sub record edited', $this->Twig);
					$o_glob->SystemMessages->Add(new forestException(0x10001407));
				}
				
				/* handle uploads */
				$this->TransferFILES_Twig();
			
				/* check if user chose auto checkin */
				if ( (array_key_exists($this->Twig->fphp_Table . '_AutocheckinStandard', $_POST)) && (boolval($_POST[$this->Twig->fphp_Table . '_AutocheckinStandard'])) ) {
					$i_foo = 0;
					$this->executeCheckin($this->Twig->UUID, $i_foo, $o_glob->HeadTwig);
				}
				
				$this->Twig = $o_glob->HeadTwig;
				$this->StandardView = \fPHP\Branches\forestBranch::DETAIL; /* because it only makes sense if we stay in detail view, when we open modal read only form for record */
			} else {
				/* check posted data for edit record */
				
				/* query record */
				if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_editKey']))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($this->Twig->UUID), array('ForeignUUID'))) {
					/* check if user is the same user who has checked out the record */
					if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
						throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
					}
				}
				
				$this->CreateLogEntry('record before edit', $this->Twig);
				
				$this->TransferPOST_Twig();
				
				/* set values for info columns when configured */
				$i_infoColumns = $o_glob->TablesInformation[$this->Twig->fphp_TableUUID]['InfoColumns'];
				
				if ($i_infoColumns == 100) {
					$this->Twig->Modified = new \fPHP\Helper\forestDateTime;
					$this->Twig->ModifiedBy = $o_glob->Security->UserUUID;
				} else if ($i_infoColumns == 1000) {
					$this->Twig->Modified = new \fPHP\Helper\forestDateTime;
					$this->Twig->ModifiedBy = $o_glob->Security->UserUUID;
				}
				
				if (method_exists($this, 'beforeEditAction')) {
					$this->beforeEditAction();
				}
				
				/* edit recrod */
				$i_result = $this->Twig->UpdateRecord();
				
				if (method_exists($this, 'afterEditAction')) {
					$this->afterEditAction();
				}
				
				/* evaluate result */
				if ($i_result == -1) {
					$this->UndoFilesEntries();
					$this->CreateLogEntry('could not edit record - unique issue - ' . $o_glob->Temp->{'UniqueIssue'}, $this->Twig);
					throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
				} else if ($i_result == 0) {
					$o_glob->SystemMessages->Add(new forestException(0x10001406));
				} else if ($i_result == 1) {
					$this->CreateLogEntry('record edited', $this->Twig);
					$o_glob->SystemMessages->Add(new forestException(0x10001407));
				}
				
				/* handle file uploads */
				$this->TransferFILES_Twig();
				
				/* check if user chose auto checkin */
				if ( (array_key_exists($this->Twig->fphp_Table . '_AutocheckinStandard', $_POST)) && (boolval($_POST[$this->Twig->fphp_Table . '_AutocheckinStandard'])) ) {
					$i_foo = 0;
					$this->executeCheckin($this->Twig->UUID, $i_foo);
				}
			}
		} else {
			if ( ($o_glob->Temp->Exists('editSubKey')) && ($o_glob->Temp->{'editSubKey'} != null) && ($o_glob->Temp->Exists('subConstraintKey')) && ($o_glob->Temp->{'subConstraintKey'} != null) ) {
				if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_glob->Temp->{'editSubKey'}), array('ForeignUUID'))) {
					/* check if user is the same user who has checked out the record */
					if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
						throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
					}
				}
				
				/* edit sub record */
				$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form', true);
				$this->RenderEditSubRecordForm();
				$this->StandardView = \fPHP\Branches\forestBranch::DETAIL; /* because it only makes sense if we stay in detail view, when we open modal read only form for sub record */
			}
		}
		
		/* if we have choosen multiple records for edit in general list view */
		if ( ($o_glob->Temp->Exists('editKey')) && ($o_glob->Temp->{'editKey'} != null) ) {
			$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form', true);
			
			/* get record keys */
			$a_editKeys = explode('~', $o_glob->Temp->{'editKey'});
			
			/* query record */
			if (! ($this->Twig->GetRecord(array($a_editKeys[0]))) ) {
				throw new forestException(0x10001401, array($this->Twig->fphp_Table));
			}
			
			if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($this->Twig->UUID), array('ForeignUUID'))) {
				/* check if user is the same user who has checked out the record */
				if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
					throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
				}
			}
			
			/* build modal form */
			$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig, true);
			$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('EditModalTitle', 1);
			$a_newEditKeys = array();
			
			/* adjust form action parameters for edit keys - remove one key which is opened in current modal form */
			foreach ($a_editKeys as $s_editKey) {
				if ($s_editKey != $a_editKeys[0]) {
					$a_newEditKeys[] = $s_editKey;
				}
			}
			
			$a_parameters = array();
			
			foreach ($o_glob->URL->Parameters as $s_key => $s_value) {
				if ( ($s_key != 'viewKey') && ($s_key != 'editKey') && ($s_key != 'deleteKey') && ($s_key != 'editSubKey') && ($s_key != 'deleteSubKey') && ($s_key != 'deleteFileKey') && ($s_key != 'subConstraintKey') ) {
					$a_parameters[$s_key] = $s_value;
				}
			}
			
			$a_parameters['editKey'] = implode('~', $a_newEditKeys);
			
			$o_glob->PostModalForm->FormObject->Action = \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, $o_glob->URL->Action, $a_parameters);
			
			/* add current record key to modal form */
			$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
			$o_hidden->Id = 'sys_fphp_editKey';
			$o_hidden->Value = $a_editKeys[0];
			
			$o_glob->PostModalForm->FormFooterElements->Add($o_hidden);
		}
		
		if (isset($this->KeepFilter)) {
			if ($this->KeepFilter->value) {
				if ($o_glob->Security->SessionData->Exists('last_filter')) {
					$o_glob->Security->SessionData->Add($o_glob->Security->SessionData->{'last_filter'}, 'filter');
				}
			}
		}
		
		$this->SetNextAction('init');
	}
	
	/**
	 * render modal form for editing sub record
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function RenderEditSubRecordForm() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$o_subconstraintTwig = new \fPHP\Twigs\subconstraintTwig;
		$o_glob->PostModalForm = new \fPHP\Forms\forestForm($o_subconstraintTwig);
		
		/* get table */
		$o_tableTwig = new \fPHP\Twigs\tableTwig;
		
		/* query table record */
		if (!($o_tableTwig->GetRecordPrimary(array($o_subconstraintTwig->fphp_Table), array('Name')))) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$o_formelementTwig = new \fPHP\Twigs\formelementTwig;
		
		/* look in tablefields for formobject, if not get the standard by formelementuuid */
		if (!($o_formelementTwig->GetRecordPrimary(array('form'), array('Name')))) {
			throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
		}
		
		$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
		$s_formObjectJSONsettings = '';
		
		if (!($o_tablefieldTwig->GetRecordPrimary(array($o_tableTwig->UUID, $o_formelementTwig->UUID), array('TableUUID', 'FormElementUUID')))) {
			/* no tablefield for table, take standard */
			$s_formObjectJSONsettings = $o_formelementTwig->JSONEncodedSettings;
		} else {
			$s_formObjectJSONsettings = $o_tablefieldTwig->JSONEncodedSettings;
		}
		
		/* create formobject(check $this->Readonly->value), modal object and maybe tab objects */
		$o_glob->PostModalForm->FormObject->loadJSON($s_formObjectJSONsettings);
		$o_glob->PostModalForm->FormModalConfiguration->loadJSON($s_formObjectJSONsettings);
		$o_glob->PostModalForm->FormTabConfiguration->loadJSON($s_formObjectJSONsettings);
		
		$o_glob->PostModalForm->FormModalConfiguration->ModalId = $o_glob->PostModalForm->FormObject->Id . 'Modal';
		$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('EditSubModalTitle', 1);
		$o_glob->PostModalForm->FormTabConfiguration->Tab = false;
		
		/* query sub constraint */
		if (! ($o_subconstraintTwig->GetRecord(array($o_glob->Temp->{'subConstraintKey'}))) ) {
			throw new forestException(0x10001401, array($o_subconstraintTwig->fphp_Table));
		}
		
		/* query sub record */
		$o_subrecordsTwig = new \fPHP\Twigs\subrecordsTwig;
		
		if (! ($o_subrecordsTwig->GetRecord(array($o_glob->Temp->{'editSubKey'}))) ) {
			$o_glob->SystemMessages->Add(new forestException(0x10001406));
		}
		
		if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_subrecordsTwig->HeadUUID), array('ForeignUUID'))) {
			/* check if user is the same user who has checked out the record */
			if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
				throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
			}
		}
		
		/* create join twig object by sub constraint sub table value */
		$s_subTableName = array_search($o_subconstraintTwig->SubTableUUID->PrimaryValue, $o_glob->Tables);
		\fPHP\Helper\forestStringLib::RemoveTablePrefix($s_subTableName);
		$s_foo = '\\fPHP\\Twigs\\' . $s_subTableName . 'Twig';
		$o_joinTwig = new $s_foo;
		
		/* check if parameter uuid matches with join record */
		if (! ($o_joinTwig->GetRecord(array($o_subrecordsTwig->JoinUUID))) ) {
			$o_glob->SystemMessages->Add(new forestException(0x10001406));
		}
		
		$a_view = array();
		
		/* use defined view in sub constraint or take view from join twig object */
		if (issetStr($o_subconstraintTwig->View->PrimaryValue)) {
			$a_subconstraintView = explode(';', $o_subconstraintTwig->View->PrimaryValue);
			
			foreach ($a_subconstraintView as $s_value) {
				$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
				
				if (!$o_tablefieldTwig->GetRecord(array($s_value))) {
					throw new forestException(0x10001401, array($o_tablefieldTwig->fphp_Table));
				}
				
				if (!property_exists($o_joinTwig, $o_tablefieldTwig->FieldName)) {
					$a_view = $o_joinTwig->fphp_View;
					break;
				} else {
					$a_view[] = $o_tablefieldTwig->FieldName;
				}
			}
		} else {
			$a_view = $o_joinTwig->fphp_View;
		}
		
		/* render join twig fields with read only or disabled mode, just as info */
		foreach ($a_view as $s_column) {
			/* do not allow to display standard columns */
			if (in_array($s_column, array('Created', 'CreatedBy', 'Modified', 'ModifiedBy', 'Identifier'))) {
				continue;
			}
			
			$s_formElement = $o_glob->TablefieldsDictionary->{$o_joinTwig->fphp_Table . '_' . $s_column}->FormElementName;
			$s_forestData = $o_glob->TablefieldsDictionary->{$o_joinTwig->fphp_Table . '_' . $s_column}->ForestDataName;
			$s_formElementJSONSettings = $o_glob->TablefieldsDictionary->{$o_joinTwig->fphp_Table . '_' . $s_column}->JSONEncodedSettings;
			
			if ( ($s_formElement == \fPHP\Forms\forestFormElement::FILE) || ($s_formElement == \fPHP\Forms\forestFormElement::PASSWORD) || ($s_formElement == \fPHP\Forms\forestFormElement::TEXTAREA) ||($s_formElement == \fPHP\Forms\forestFormElement::RICHTEXT) || ($s_formElement == \fPHP\Forms\forestFormElement::DROPZONE) || (($s_formElement == \fPHP\Forms\forestFormElement::CHECKBOX) && ($s_forestData == 'forestInt')) ) {
				continue;
			}
			
			$o_formElement = new \fPHP\Forms\forestFormElement($o_glob->TablefieldsDictionary->{$o_joinTwig->fphp_Table . '_' . $s_column}->FormElementName);
			$o_formElement->loadJSON($o_glob->TablefieldsDictionary->{$o_joinTwig->fphp_Table . '_' . $s_column}->JSONEncodedSettings, $o_glob->BranchTree['Name'][$s_subTableName]);
			$o_formElement->Name = 'readonly_' . $s_column;
			
			if (property_exists($o_formElement->getFormElement(), 'Readonly')) {
				$o_formElement->Readonly = true;
			} else {
				$o_formElement->Disabled = true;
			}
			
			$s_value = '';
			
			if ((!$o_joinTwig->IsEmpty()) && (property_exists($o_joinTwig, $s_column))) {
				/* maybe other casts necessary depending on sqltype info */
				if (is_a($o_joinTwig->{$s_column}, '\\fPHP\Helper\\forestDateTime')) {
					if ($s_formElement == \fPHP\Forms\forestFormElement::DATETIMELOCAL) {
						$s_value = $o_joinTwig->{$s_column}->ToString('Y-m-d\TH:i:s');
					} else if ($s_formElement == \fPHP\Forms\forestFormElement::DATE) {
						$s_value = $o_joinTwig->{$s_column}->ToString('Y-m-d');
					} else if ($s_formElement == \fPHP\Forms\forestFormElement::MONTH) {
						$s_value = $o_joinTwig->{$s_column}->ToString('Y-m');
					} else if ($s_formElement == \fPHP\Forms\forestFormElement::TIME) {
						$s_value = $o_joinTwig->{$s_column}->ToString('H:i:s');
					} else if ($s_formElement == \fPHP\Forms\forestFormElement::WEEK) {
						$s_value = $o_joinTwig->{$s_column}->ToString('Y-\WW');
					} else {
						$s_value = $o_joinTwig->{$s_column}->ToString();
					}
				} else if (is_a($o_joinTwig->{$s_column}, '\\fPHP\Helper\\forestLookupData')) {
					$s_value = $o_joinTwig->{$s_column}->PrimaryValue;
				} else {
					$s_value = strval($o_joinTwig->{$s_column});
					
					$s_JSONEncodedSettings = str_replace('&quot;', '"', $s_formElementJSONSettings);
					$a_settings = json_decode($s_JSONEncodedSettings, true);
					/* check if we want to render value as date interval value */
					if (array_key_exists('DateIntervalFormat', $a_settings)) {
						if ($a_settings['DateIntervalFormat']) {
							$s_value = strval(new \fPHP\Helper\forestDateInterval($s_value));
						}
					}
				}
			}
			
			/* get value for forestCombination field */
			if ( (!$o_joinTwig->IsEmpty()) && ($s_forestData == 'forestCombination') ) {
				$s_JSONEncodedSettings = str_replace('&quot;', '"', $s_formElementJSONSettings);
				$a_settings = json_decode($s_JSONEncodedSettings, true);
				
				if (array_key_exists('forestCombination', $a_settings)) {
					$s_value = $o_joinTwig->CalculateCombination($a_settings['forestCombination']);
					/* check if we want to render value as money value */
					if (array_key_exists('MoneyFormat', $a_settings)) {
						if ($a_settings['MoneyFormat']) {
							$s_value = \fPHP\Helper\forestStringLib::money_format('%i', $s_value);
						}
					}
					/* check if we want to render value as date interval value */
					else if (array_key_exists('DateIntervalFormat', $a_settings)) {
						if ($a_settings['DateIntervalFormat']) {
							$s_value = strval(new \fPHP\Helper\forestDateInterval($s_value));
						}
					}
				}
			}
			
			/* check for money format setting */
			if ( (!$o_joinTwig->IsEmpty()) && ($s_forestData == 'forestFloat') ) {
				$s_JSONEncodedSettings = str_replace('&quot;', '"', $s_formElementJSONSettings);
				$a_settings = json_decode($s_JSONEncodedSettings, true);
				/* check if we want to render value as money value */
				if (array_key_exists('MoneyFormat', $a_settings)) {
					if ($a_settings['MoneyFormat']) {
						$s_value = \fPHP\Helper\forestStringLib::money_format('%i', $o_joinTwig->{$s_column});
						
						if (floatval($o_joinTwig->{$s_column}) < 0.0) {
							$o_formElement->Style = 'color: red;';
						}
					}
				}
			}
			
			$o_formElement->Value = $s_value;
			
			$o_glob->PostModalForm->FormElements->Add($o_formElement);
		}
		
		$o_glob->PostModalForm->FormObject->ValRequiredMessage = $o_glob->GetTranslation('ValRequiredMessage', 1);
		
		$this->AddAdditionalSubRecordFormElements($o_subconstraintTwig, $o_subrecordsTwig);
		
		/* add auto checkin form element if current record is checked out */
		if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_subrecordsTwig->UUID), array('ForeignUUID'))) {
			/* query auto checkin form element */
			if (!($o_formelementTwig->GetRecordPrimary(array(\fPHP\Forms\forestFormElement::AUTOCHECKIN), array('Name')))) {
				throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
			}
			
			/* create auto checkin form element and adjust settings */
			$o_formElement = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::AUTOCHECKIN);
			$o_formElement->loadJSON($o_formelementTwig->JSONEncodedSettings);
			$o_formElement->Id = $o_subrecordsTwig->fphp_Table . '_AutocheckinStandard';
			$o_glob->PostModalForm->FormElements->Add($o_formElement);
		}
		
		$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
		$o_hidden->Id = 'sys_fphp_subConstraintKey';
		$o_hidden->Value = $o_glob->Temp->{'subConstraintKey'};
		$o_glob->PostModalForm->FormElements->Add($o_hidden);
		
		$o_hidden2 = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
		$o_hidden2->Id = 'sys_fphp_editSubKey';
		$o_hidden2->Value = $o_glob->Temp->{'editSubKey'};
		$o_glob->PostModalForm->FormElements->Add($o_hidden2);
		
		/* render submit button */
		if (!($o_formelementTwig->GetRecordPrimary(array('submit'), array('Name')))) {
			throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
		}
		
		$o_button = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::BUTTON);
		$o_button->loadJSON($o_formelementTwig->JSONEncodedSettings);
		$o_button->ButtonText = htmlspecialchars_decode($o_button->ButtonText);
		$o_glob->PostModalForm->FormFooterElements->Add($o_button);
		
		/* render cancel button */
		if (!($o_formelementTwig->GetRecordPrimary(array('cancel'), array('Name')))) {
			throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
		}
		
		$o_cancel = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::BUTTON);
		$o_cancel->loadJSON($o_formelementTwig->JSONEncodedSettings);
		$o_cancel->ButtonText = htmlspecialchars_decode($o_cancel->ButtonText);
		$o_glob->PostModalForm->FormFooterElements->Add($o_cancel);
		
		$o_glob->PostModalForm->AddFormKey();
		$o_glob->PostModalForm->Automatic = true;
	}
	
	/**
	 * write POST data into twig property fields
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function TransferPOST_Twig() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* iterate each column of table twig for possible post data */
		foreach ($this->Twig->fphp_Mapping as $s_column) {
			/* skip standard Id + UUID column */
			if ( ($s_column != 'Id') && ($s_column != 'UUID') ) {
				/* set tablefield identifier */
				$s_tableFieldIdentifier = $this->Twig->fphp_Table . '_' . $s_column;
				$s_forestData = '';
				
				/* check if tablefield is in dictionary */
				if ($o_glob->TablefieldsDictionary->Exists($s_tableFieldIdentifier)) {
					$s_forestData = $o_glob->TablefieldsDictionary->{$s_tableFieldIdentifier}->ForestDataName;
				} else {
					/* if it is not in the dictionary, we may have a sub record */
					if ( ($o_glob->Temp->Exists('subConstraintKey')) && ($o_glob->Temp->{'subConstraintKey'} != null) ) {
						if (array_key_exists($o_glob->HeadTwig->fphp_TableUUID, $o_glob->SubConstraintsDictionary)) {
							$b_found = false;
							
							/* get sub constraint in dictionary by sub constraint key */
							foreach ($o_glob->SubConstraintsDictionary[$o_glob->HeadTwig->fphp_TableUUID] as $o_subconstraint) {
								if ($o_subconstraint->UUID == $o_glob->Temp->{'subConstraintKey'}) {
									/* find tablefield of sub constraint and column name which should be in 'sub record field'-field */
									foreach ($o_glob->TablefieldsDictionary as $s_tableFieldDictionaryKey => $o_tableFieldDictionaryObject) {
										if ( ($o_tableFieldDictionaryObject->TableUUID == $o_subconstraint->UUID) && ($o_tableFieldDictionaryObject->SubRecordField == $s_column) ) {
											$s_tableFieldIdentifier = $s_tableFieldDictionaryKey;
											$s_forestData = $o_glob->TablefieldsDictionary->{$s_tableFieldIdentifier}->ForestDataName;
											$b_found = true;
										}
									}
								}
							}
							
							if (!$b_found) {
								/* field has not been found, but ignore if we have not post data for it */
								if (array_key_exists($this->Twig->fphp_Table . '_' . $s_column, $_POST)) {
									throw new forestException(0x10001401, array($this->Twig->fphp_Table));
								}
							}
						} else {
							/* we have post data for a field, but not configuration of the tablefield in the database */
							throw new forestException(0x10001401, array($this->Twig->fphp_Table));
						}
					}
				}
				
				if (array_key_exists($this->Twig->fphp_Table . '_' . $s_column, $_POST)) {
					/* cast post data to twig property data fields */
					if (($s_forestData == 'forestNumericString') || ($s_forestData == 'forestNumericString(1)') || ($s_forestData == 'forestInt')) {
						if (is_array($_POST[$this->Twig->fphp_Table . '_' . $s_column])) {
							/* post value is array, so we need to valiate multiple checkboxes */
							$i_sum = 0;
							
							foreach ($_POST[$this->Twig->fphp_Table . '_' . $s_column] as $s_checkboxValue) {
								if (!preg_match('/[^01$]/', $s_checkboxValue)) {
									$i_sum += intval($s_checkboxValue);
								}
							}
							
							$this->Twig->{$s_column} = $i_sum;
						} else {
							if ( ($o_glob->TablefieldsDictionary->{$s_tableFieldIdentifier}->FormElementName == \fPHP\Forms\forestFormElement::RADIO) || ($o_glob->TablefieldsDictionary->{$s_tableFieldIdentifier}->FormElementName == \fPHP\Forms\forestFormElement::CHECKBOX) ) {
								if (!preg_match('/[^01$]/', $_POST[$this->Twig->fphp_Table . '_' . $s_column])) {
									$this->Twig->{$s_column} = intval($_POST[$this->Twig->fphp_Table . '_' . $s_column]);
								} else {
									$this->Twig->{$s_column} = 0;
								}
							} else {
								$this->Twig->{$s_column} = intval($_POST[$this->Twig->fphp_Table . '_' . $s_column]);
							}
						}
					} else if ($s_forestData == 'forestFloat') {
						$this->Twig->{$s_column} = floatval($_POST[$this->Twig->fphp_Table . '_' . $s_column]);
					} else if ($s_forestData == 'forestBool') {
						$this->Twig->{$s_column} = boolval($_POST[$this->Twig->fphp_Table . '_' . $s_column]);
					} else if ($s_forestData == 'forestArray') {
						$this->Twig->{$s_column} = null;
					} else if ($s_forestData == 'forestObject') {
						$this->Twig->{$s_column} = null;
					} else if ($s_forestData == 'forestObject(&apos;forestDateTime&apos;)') {
						$this->Twig->{$s_column} = \fPHP\Helper\forestStringLib::TextToDate($_POST[$this->Twig->fphp_Table . '_' . $s_column]);
					} else if ($s_forestData == 'forestLookup') {
						if (is_array($_POST[$this->Twig->fphp_Table . '_' . $s_column])) {
							/* post value is array, so we need to valiate multiple selected items */
							$s_sum = '';
							
							foreach ($_POST[$this->Twig->fphp_Table . '_' . $s_column] as $s_selectOptValue) {
								$s_sum .= strval($s_selectOptValue) . ';';
							}
							
							$s_sum = substr($s_sum, 0, -1);
							$this->Twig->{$s_column} = $s_sum;
						} else {
							$this->Twig->{$s_column} = strval($_POST[$this->Twig->fphp_Table . '_' . $s_column]);
						}
					} else if ($s_forestData == 'forestList') {
						if (!empty($_POST[$this->Twig->fphp_Table . '_' . $s_column])) {
							$this->Twig->{$s_column} = strval($_POST[$this->Twig->fphp_Table . '_' . $s_column]);
						}
					} else {
						if (is_array($_POST[$this->Twig->fphp_Table . '_' . $s_column])) {
							/* post value is array, so we need to valiate multiple selected items */
							$s_sum = '';
							
							foreach ($_POST[$this->Twig->fphp_Table . '_' . $s_column] as $s_selectOptValue) {
								$s_sum .= strval($s_selectOptValue) . ';';
							}
							
							$s_sum = substr($s_sum, 0, -1);
							$this->Twig->{$s_column} = $s_sum;
						} else {
							if ($o_glob->TablefieldsDictionary->{$s_tableFieldIdentifier}->FormElementName == \fPHP\Forms\forestFormElement::PASSWORD) {
								if (!issetStr(strval($_POST[$this->Twig->fphp_Table . '_' . $s_column]))) {
									/* if we have not post any password, do not clear password field, but try to restore the old password value */
									$this->Twig->RestoreFieldValue($s_column);
								} else {
									/* hash password, so we do not store password in plain text */
									$this->Twig->{$s_column} = password_hash(strval($_POST[$this->Twig->fphp_Table . '_' . $s_column]), PASSWORD_DEFAULT);
								}
							} else {
								/* normal string */
								$this->Twig->{$s_column} = strval($_POST[$this->Twig->fphp_Table . '_' . $s_column]);
							}
						}
					}
					
					/* identify posted radio and select data by json encoded settings and it's multi-language values */
					if (issetStr($o_glob->TablefieldsDictionary->{$s_tableFieldIdentifier}->JSONEncodedSettings)) {
						if ($o_glob->TablefieldsDictionary->{$s_tableFieldIdentifier}->FormElementName == \fPHP\Forms\forestFormElement::RADIO) {
							$o_tempFormElement = new \fPHP\Forms\forestFormElement($o_glob->TablefieldsDictionary->{$s_tableFieldIdentifier}->FormElementName);
							$o_tempFormElement->loadJSON($o_glob->TablefieldsDictionary->{$s_tableFieldIdentifier}->JSONEncodedSettings);
							
							if (!in_array($this->Twig->{$s_column}, $o_tempFormElement->Options)) {
								$this->Twig->{$s_column} = 0;
							}
						} else if ($o_glob->TablefieldsDictionary->{$s_tableFieldIdentifier}->FormElementName == \fPHP\Forms\forestFormElement::SELECT) {
							$o_tempFormElement = new \fPHP\Forms\forestFormElement($o_glob->TablefieldsDictionary->{$s_tableFieldIdentifier}->FormElementName);
							$o_tempFormElement->loadJSON($o_glob->TablefieldsDictionary->{$s_tableFieldIdentifier}->JSONEncodedSettings);
							
							$b_value_found = false;
							
							$a_valueOptions = array();
		
							if (strpos($this->Twig->{$s_column}, ';') !== false) {
								$a_valueOptions = explode(';', $this->Twig->{$s_column});
							}
							
							if (count($o_tempFormElement->Options) > 0) {
								foreach ($o_tempFormElement->Options as $s_option_label => $s_option_value) {
									if (is_array($s_option_value)) {
										if (count($s_option_value) > 0) {
											foreach ($s_option_value as $s_optgroup_option_label => $s_optgroup_option_value) {
												if ((issetStr($this->Twig->{$s_column})) && (($this->Twig->{$s_column} == $s_optgroup_option_value) || (in_array($s_optgroup_option_value, $a_valueOptions)))) {
													$b_value_found = true;
												}
											}
										}
									} else {
										if ((issetStr($this->Twig->{$s_column})) && (($this->Twig->{$s_column} == $s_option_value) || (in_array($s_option_value, $a_valueOptions)))) {
											$b_value_found = true;
										}
									}
								}
							}
							
							if (!$b_value_found) {
								$this->Twig->{$s_column} = 'NULL';
							}
						}
					}
					
					$a_validationRules = array();
					$this->GetValidationRules($o_glob->TablefieldsDictionary->{$s_tableFieldIdentifier}->UUID, $a_validationRules);
					
					/* check posted data with configurued validation rules */
					if (count($a_validationRules) > 0) {
						foreach ($a_validationRules as $o_validationRule) {
							if ( ($o_validationRule['Name'] == 'required') || ($o_validationRule['ValidationRuleRequired'] == 1) ) {
								/* d2c('column -> value : ' . $s_column . ' -> ' . $this->Twig->{$s_column} . ' - ' . var_export($this->Twig->{$s_column}, true)); */
								/* d2c('!issetStr -> ' . (!issetStr($this->Twig->{$s_column}))); */
								/* d2c('=== 0 -> ' . ($this->Twig->{$s_column} === 0)); */
								/* d2c('=== floatval(0) -> ' . ($this->Twig->{$s_column} === floatval(0))); */
								/* d2c('=== false -> ' . ($this->Twig->{$s_column} === false)); */
								/* d2c('array count <= 0 -> ' . ((is_array($this->Twig->{$s_column})) && (count($this->Twig->{$s_column}) == 0))); */
								if (is_a($this->Twig->{$s_column}, '\\fPHP\Helper\\forestLookupData')) {
									if (property_exists($this->Twig->{$s_column}, 'PrimaryValue')) {
										if (!issetStr($this->Twig->{$s_column}->PrimaryValue)) {
											throw new forestException(0x10001408, array($s_column));
										}
									}
								} else if ( (!issetStr($this->Twig->{$s_column})) ^ ($this->Twig->{$s_column} === 0) ^ ($this->Twig->{$s_column} === floatval(0)) ^ ($this->Twig->{$s_column} === false) ^ ((is_array($this->Twig->{$s_column})) && (count($this->Twig->{$s_column}) == 0)) ) {
									throw new forestException(0x10001408, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'minlength') {
								if ( (issetStr($this->Twig->{$s_column})) && (strlen($this->Twig->{$s_column}) < intval($o_validationRule['ValidationRuleParam01'])) ) {
									throw new forestException(0x10001409, array($s_column, intval($o_validationRule['ValidationRuleParam01'])));
								}
							} else if ($o_validationRule['Name'] == 'maxlength') {
								if ( (issetStr($this->Twig->{$s_column})) && (strlen($this->Twig->{$s_column}) > intval($o_validationRule['ValidationRuleParam01'])) ) {
									throw new forestException(0x1000140A, array($s_column, intval($o_validationRule['ValidationRuleParam01'])));
								}
							} else if ($o_validationRule['Name'] == 'min') {
								if ($this->Twig->{$s_column} < intval($o_validationRule['ValidationRuleParam01'])) {
									throw new forestException(0x1000140B, array($s_column, intval($o_validationRule['ValidationRuleParam01'])));
								}
							} else if ($o_validationRule['Name'] == 'max') {
								if ($this->Twig->{$s_column} > intval($o_validationRule['ValidationRuleParam01'])) {
									throw new forestException(0x1000140C, array($s_column, intval($o_validationRule['ValidationRuleParam01'])));
								}
							} else if ($o_validationRule['Name'] == 'email') {
								if ( (issetStr($this->Twig->{$s_column})) && (!preg_match("/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/", $this->Twig->{$s_column})) ) {
									throw new forestException(0x1000140D, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'url') {
								if ( (issetStr($this->Twig->{$s_column})) && (!preg_match("_^(https?|ftp)://(\S+(:\S*)?@)?(([1-9]|[1-9]\d|1\d\d|2[0-1]\d|22[0-3])(\.([0-9]|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])){2}(\.([1-9]|[1-9]\d|1\d\d|2[0-4]\d|25[0-4]))|(([a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(\.([a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)*(\.([a-z\x{00a1}-\x{ffff}]{2,})))(:\d{2,5})?(/[^\s]*)?\$_iuS", $this->Twig->{$s_column})) ) {
									throw new forestException(0x1000140E, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'digits') {
								if ( (issetStr($this->Twig->{$s_column})) && (!preg_match("/^\d+$/", $_POST[$this->Twig->fphp_Table . '_' . $s_column])) ) {
									throw new forestException(0x1000140F, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'equalTo') {
								$s_field = str_replace('#' . $this->Twig->fphp_Table . '_', '', strval($o_validationRule['ValidationRuleParam01']));
								
								if ( (issetStr($this->Twig->{$s_column})) && ($this->Twig->{$s_column} != $this->Twig->{$s_field}) ) {
									throw new forestException(0x10001410, array($s_column, $s_field));
								}
							} else if ($o_validationRule['Name'] == 'fphp_dateISO') {
								if ( (issetStr($_POST[$this->Twig->fphp_Table . '_' . $s_column])) && (!preg_match("/^(\d){4}-((0[1-9])|(1[0-2]))-((0[1-9])|(1[0-9])|(2[0-9])|(3[0-1]))$/", $_POST[$this->Twig->fphp_Table . '_' . $s_column])) ) {
									throw new forestException(0x10001411, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'dateISO') {
								if ( (issetStr($_POST[$this->Twig->fphp_Table . '_' . $s_column])) && (!preg_match("/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/", $_POST[$this->Twig->fphp_Table . '_' . $s_column])) ) {
									throw new forestException(0x10001411, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'fphp_dateDMYpoint') {
								if ( (issetStr($_POST[$this->Twig->fphp_Table . '_' . $s_column])) && (!preg_match("/^((0[1-9])|(1[0-9])|(2[0-9])|(3[0-1]))\.((0[1-9])|(1[0-2]))\.(\d){4}$/", $_POST[$this->Twig->fphp_Table . '_' . $s_column])) ) {
									throw new forestException(0x10001412, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'fphp_dateDMYslash') {
								if ( (issetStr($_POST[$this->Twig->fphp_Table . '_' . $s_column])) && (!preg_match("/^((0[1-9])|(1[0-9])|(2[0-9])|(3[0-1]))\/((0[1-9])|(1[0-2]))\/(\d){4}$/", $_POST[$this->Twig->fphp_Table . '_' . $s_column])) ) {
									throw new forestException(0x10001413, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'fphp_dateMDYslash') {
								if ( (issetStr($_POST[$this->Twig->fphp_Table . '_' . $s_column])) && (!preg_match("/^((0[1-9])|(1[0-2]))\/((0[1-9])|(1[0-9])|(2[0-9])|(3[0-1]))\/(\d){4}$/", $_POST[$this->Twig->fphp_Table . '_' . $s_column])) ) {
									throw new forestException(0x10001414, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'fphp_time') {
								if ( (issetStr($_POST[$this->Twig->fphp_Table . '_' . $s_column])) && (!preg_match("/^(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9])(:([0-5][0-9])){0,1}$/", $_POST[$this->Twig->fphp_Table . '_' . $s_column])) ) {
									throw new forestException(0x10001415, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'fphp_datetime') {
								if ( (issetStr($_POST[$this->Twig->fphp_Table . '_' . $s_column])) && (!preg_match("/^((0[1-9])|(1[0-9])|(2[0-9])|(3[0-1]))\.((0[1-9])|(1[0-2]))\.(\d){4}\s(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $_POST[$this->Twig->fphp_Table . '_' . $s_column])) ) {
									throw new forestException(0x10001416, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'fphp_datetimeISO') {
								if ( (issetStr($_POST[$this->Twig->fphp_Table . '_' . $s_column])) && (!preg_match("/^(\d){4}-((0[1-9])|(1[0-2]))-((0[1-9])|(1[0-9])|(2[0-9])|(3[0-1]))(\s|T)(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9])(:([0-5][0-9])){0,1}$/", $_POST[$this->Twig->fphp_Table . '_' . $s_column])) ) {
									throw new forestException(0x10001417, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'fphp_dateinterval') {
								if ( (issetStr($this->Twig->{$s_column})) && ((!preg_match("/^(P(((\d)+Y(\d)+M((\d)+(W|D))?)|((\d)+(Y|M)(\d)+(W|D))|((\d)+(Y|M|W|D)))T(((\d)+H(\d)+M(\d)+S)|((\d)+H(\d)+(M|S))|((\d)+M(\d)+S)|((\d)+(H|M|S))))$/", $this->Twig->{$s_column}))
								&& (!preg_match("/^(PT(((\d)+H(\d)+M(\d)+S)|((\d)+H(\d)+(M|S))|((\d)+M(\d)+S)|((\d)+(H|M|S))))$/", $this->Twig->{$s_column}))
								&& (!preg_match("/^(P(((\d)+Y(\d)+M((\d)+(W|D))?)|((\d)+(Y|M)(\d)+(W|D))|((\d)+(Y|M|W|D))))$/", $this->Twig->{$s_column}))) )
								{
									throw new forestException(0x10001418, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'fphp_password') {
								if ( (issetStr(strval($_POST[$this->Twig->fphp_Table . '_' . $s_column]))) && (!((preg_match("/^[A-Za-z0-9\d=!\-@._*?#§$%&'~:;,]*$/", strval($_POST[$this->Twig->fphp_Table . '_' . $s_column])))
								&& (preg_match("/[=!\-@._*?#§$%&'~:;,]/", strval($_POST[$this->Twig->fphp_Table . '_' . $s_column])))
								&& (preg_match("/[a-z]/", strval($_POST[$this->Twig->fphp_Table . '_' . $s_column])))
								&& (preg_match("/[A-Z]/", strval($_POST[$this->Twig->fphp_Table . '_' . $s_column])))
								&& (preg_match("/\d/", strval($_POST[$this->Twig->fphp_Table . '_' . $s_column]))))) )
								{
									throw new forestException(0x10001419, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'fphp_username') {
								if ( (issetStr($this->Twig->{$s_column})) && (!preg_match("/^[a-zA-Z0-9_\-]*$/", $this->Twig->{$s_column})) ) {
									throw new forestException(0x1000141A, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'fphp_onlyletters') {
								if ( (issetStr($this->Twig->{$s_column})) && (!preg_match("/^[a-zA-Z]*$/", $this->Twig->{$s_column})) ) {
									throw new forestException(0x1000143F, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'number') {
								if ( (issetStr($_POST[$this->Twig->fphp_Table . '_' . $s_column])) && (!preg_match("/^(?:-?\d+|-?\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/", $_POST[$this->Twig->fphp_Table . '_' . $s_column])) ) {
									throw new forestException(0x1000141B, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'range') {
								if ( ($this->Twig->{$s_column} < intval($o_validationRule['ValidationRuleParam01'])) || ($this->Twig->{$s_column} > intval($o_validationRule['ValidationRuleParam02'])) ) {
									throw new forestException(0x1000141C, array($s_column, intval($o_validationRule['ValidationRuleParam01']), intval($o_validationRule['ValidationRuleParam02'])));
								}
							} else if ($o_validationRule['Name'] == 'rangelength') {
								if ( (issetStr($this->Twig->{$s_column})) && ( (strlen($this->Twig->{$s_column}) < intval($o_validationRule['ValidationRuleParam01'])) || (strlen($this->Twig->{$s_column}) > intval($o_validationRule['ValidationRuleParam02'])) ) ) {
									throw new forestException(0x1000141D, array($s_column, intval($o_validationRule['ValidationRuleParam01']), intval($o_validationRule['ValidationRuleParam02'])));
								}
							} else if ($o_validationRule['Name'] == 'fphp_month') {
								if ( (issetStr($_POST[$this->Twig->fphp_Table . '_' . $s_column])) && (!preg_match("/^(\d){4}-((0[1-9])|(1[0-2]))$/", $_POST[$this->Twig->fphp_Table . '_' . $s_column])) ) {
									throw new forestException(0x1000141E, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'fphp_week') {
								if ( (issetStr($_POST[$this->Twig->fphp_Table . '_' . $s_column])) && (!preg_match("/^(\d){4}-W((0[1-9])|([1-4][0-9])|(5[0-3]))$/", $_POST[$this->Twig->fphp_Table . '_' . $s_column])) ) {
									throw new forestException(0x1000141F, array($s_column));
								}
							}
						}
					}
				} else if (array_key_exists($this->Twig->fphp_Table . '_' . $s_column, $_FILES)) {
					/* handle file uploads */
					if ( (!empty($_FILES[$this->Twig->fphp_Table . '_' . $s_column]['name'])) && (!empty($_FILES[$this->Twig->fphp_Table . '_' . $s_column]['tmp_name'])) && (intval($_FILES[$this->Twig->fphp_Table . '_' . $s_column]['size']) != 0) ) {
						$s_fileName = $_FILES[$this->Twig->fphp_Table . '_' . $s_column]['name'];
						$s_newFilename = strtoupper(substr($o_glob->Security->GenRandomHash(), 0, 8));
						$s_extension = pathinfo($s_fileName, PATHINFO_EXTENSION);
						
						$o_filesTwig = new \fPHP\Twigs\filesTwig;
						$o_filesTwig->BranchId = $o_glob->URL->BranchId;
						$o_filesTwig->ForeignUUID = $o_glob->Security->GenUUID();
						$o_filesTwig->Name = $s_newFilename . '.' . $s_extension;
						$o_filesTwig->DisplayName = $s_fileName;
						
						/* set values for info columns when configured */
						$i_infoColumns = $o_glob->TablesInformation[$this->Twig->fphp_TableUUID]['InfoColumns'];
						
						if ($o_glob->HeadTwig != null) {
							$i_infoColumns = $o_glob->TablesInformation[$o_glob->HeadTwig->fphp_TableUUID]['InfoColumns'];
						}
						
						if ($i_infoColumns == 10) {
							$o_filesTwig->Created = new \fPHP\Helper\forestDateTime;
							$o_filesTwig->CreatedBy = $o_glob->Security->UserUUID;
						} else if ($i_infoColumns == 100) {
							$o_filesTwig->Modified = new \fPHP\Helper\forestDateTime;
							$o_filesTwig->ModifiedBy = $o_glob->Security->UserUUID;
						} else if ($i_infoColumns == 1000) {
							$o_filesTwig->Created = new \fPHP\Helper\forestDateTime;
							$o_filesTwig->CreatedBy = $o_glob->Security->UserUUID;
							$o_filesTwig->Modified = new \fPHP\Helper\forestDateTime;
							$o_filesTwig->ModifiedBy = $o_glob->Security->UserUUID;
						}
						
						$i_result = $o_filesTwig->InsertRecord();
			
						if ($i_result == -1) {
							throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
						} else if ($i_result == 0) {
							throw new forestException(0x10001402);
						} else if ($i_result == 1) {
							$o_glob->Temp->Add($o_filesTwig->UUID, $s_column . '_uploadFilesTwigUUID');
							$o_glob->Temp->Add($this->Twig->{$s_column}, $s_column . '_oldFilesTwigUUID');
							$this->Twig->{$s_column} = $o_filesTwig->UUID;
						}
					}
				} else {
					/* columns which are not in post data, e.g. bool unchecked checkboxes */
					if ($s_forestData == 'forestBool') {
						$this->Twig->{$s_column} = false;
					} else if ($s_forestData == 'forestInt') {
						$this->Twig->{$s_column} = 0;
					}
				}
			}
		}
		
		if (array_key_exists('fphp_dropzonePostData', $_POST)) {
			/* handle dropzone file uploads */
			if (!empty($_POST['fphp_dropzonePostData'])) {
				$a_files = explode('/', $_POST['fphp_dropzonePostData']);
				$i = 0;
				
				foreach ($a_files as $s_file) {
					$s_fileName = $s_file;
					$s_newFilename = strtoupper(substr($o_glob->Security->GenRandomHash(), 0, 8));
					$s_extension = pathinfo($s_fileName, PATHINFO_EXTENSION);
					
					$o_filesTwig = new \fPHP\Twigs\filesTwig;
					$o_filesTwig->BranchId = $o_glob->URL->BranchId;
					$o_filesTwig->ForeignUUID = $o_glob->Security->GenUUID();
					$o_filesTwig->Name = $s_newFilename . '.' . $s_extension;
					$o_filesTwig->DisplayName = $s_fileName;
					
					/* set values for info columns when configured */
					$i_infoColumns = $o_glob->TablesInformation[$this->Twig->fphp_TableUUID]['InfoColumns'];
					
					if ($o_glob->HeadTwig != null) {
						$i_infoColumns = $o_glob->TablesInformation[$o_glob->HeadTwig->fphp_TableUUID]['InfoColumns'];
					}
					
					if ($i_infoColumns == 10) {
						$o_filesTwig->Created = new \fPHP\Helper\forestDateTime;
						$o_filesTwig->CreatedBy = $o_glob->Security->UserUUID;
					} else if ($i_infoColumns == 100) {
						$o_filesTwig->Modified = new \fPHP\Helper\forestDateTime;
						$o_filesTwig->ModifiedBy = $o_glob->Security->UserUUID;
					} else if ($i_infoColumns == 1000) {
						$o_filesTwig->Created = new \fPHP\Helper\forestDateTime;
						$o_filesTwig->CreatedBy = $o_glob->Security->UserUUID;
						$o_filesTwig->Modified = new \fPHP\Helper\forestDateTime;
						$o_filesTwig->ModifiedBy = $o_glob->Security->UserUUID;
					}
					
					$i_result = $o_filesTwig->InsertRecord();
		
					if ($i_result == -1) {
						throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
					} else if ($i_result == 0) {
						throw new forestException(0x10001402);
					} else if ($i_result == 1) {
						$o_glob->Temp->Add($o_filesTwig->UUID, 'fphp_dropzonePostData_' . $i);
						$i++;
					}
				}
				
				$o_glob->Temp->Add(count($a_files), 'fphp_dropzonePostDataAmount');
			}
		}
		
		if (array_key_exists($this->Twig->fphp_Table . '_Captcha', $_POST)) {
			/* handle captcha */
			if (!array_key_exists($this->Twig->fphp_Table . '_Captcha_Hidden', $_POST)) {
				throw new forestException(0x10001420);
			}
			
			if (!password_verify($_POST[$this->Twig->fphp_Table . '_Captcha'], $_POST[$this->Twig->fphp_Table . '_Captcha_Hidden'])) {
				throw new forestException(0x10001421);
			}
			
			if ($o_glob->Security->SessionData->Exists('fphp_captcha')) {
				$o_glob->Security->SessionData->Del('fphp_captcha');
			}
			
			if ($o_glob->Security->SessionData->Exists('fphp_captcha_length')) {
				$o_glob->Security->SessionData->Del('fphp_captcha_length');
			}
		}
	}
	
	/**
	 * handle FILES data
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function TransferFILES_Twig() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		$b_upload_done = false;
		
		foreach ($this->Twig->fphp_Mapping as $s_column) {
			if ( ($s_column != 'Id') && ($s_column != 'UUID') ) {
				if (array_key_exists($this->Twig->fphp_Table . '_' . $s_column, $_FILES)) {
					if ( (!empty($_FILES[$this->Twig->fphp_Table . '_' . $s_column]['name'])) && (!empty($_FILES[$this->Twig->fphp_Table . '_' . $s_column]['tmp_name'])) && (intval($_FILES[$this->Twig->fphp_Table . '_' . $s_column]['size']) != 0) ) {
						$o_filesTwig = new \fPHP\Twigs\filesTwig;
						
						if ($o_glob->Temp->Exists($s_column . '_uploadFilesTwigUUID')) {
							/* get versioning settings */
							if ($o_glob->HeadTwig != null) {
								$i_versioning = $o_glob->TablesInformation[$o_glob->HeadTwig->fphp_TableUUID]['Versioning'];
							} else {
								$i_versioning = $o_glob->TablesInformation[$this->Twig->fphp_TableUUID]['Versioning'];
							}
							
							if ($o_filesTwig->GetRecord(array($o_glob->Temp->{$s_column . '_uploadFilesTwigUUID'}))) {
								$o_oldFilesTwig = new \fPHP\Twigs\filesTwig;
								
								/* delete old file */
								if ($o_glob->Temp->Exists($s_column . '_oldFilesTwigUUID')) {
									if ($o_oldFilesTwig->GetRecord(array($o_glob->Temp->{$s_column . '_oldFilesTwigUUID'}))) {
										/* do not delete old file if versioning is activated for twig */
										if ($i_versioning != 100) {
											$s_folder = substr(pathinfo($o_oldFilesTwig->Name, PATHINFO_FILENAME), 6, 2);
											
											$s_path = '';
						
											if (count($o_glob->URL->Branches) > 0) {
												foreach($o_glob->URL->Branches as $s_value) {
													$s_path .= $s_value . '/';
												}
											}
											
											$s_path .= $o_glob->URL->Branch . '/';
											
											$s_path = './trunk/' . $s_path . 'fphp_files/' . $s_folder . '/';
											
											if (is_dir($s_path)) {
												if (file_exists($s_path . $o_oldFilesTwig->Name)) {
													if (!(@unlink($s_path . $o_oldFilesTwig->Name))) {
														throw new forestException(0x10001422, array($s_path . $o_oldFilesTwig->Name));
													}
												}
											}
											
											/* delete old file record */
											$i_return = $o_oldFilesTwig->DeleteRecord();
											
											/* evaluate result */
											if ($i_return <= 0) {
												throw new forestException(0x10001423);
											}
										}
									}
								}
								
								/* check file structure */
								\fPHP\Helper\forestFile::CreateFileFolderStructure($o_glob->URL->BranchId);
								$s_folder = substr(pathinfo($o_filesTwig->Name, PATHINFO_FILENAME), 6, 2);
								
								$s_path = '';
			
								if (count($o_glob->URL->Branches) > 0) {
									foreach($o_glob->URL->Branches as $s_value) {
										$s_path .= $s_value . '/';
									}
								}
								
								$s_path .= $o_glob->URL->Branch . '/';
								
								$s_path = './trunk/' . $s_path . 'fphp_files/' . $s_folder . '/';
								
								if (!is_dir($s_path)) { /* target path does not exists */
									/* delete file record */
									$i_return = $o_filesTwig->DeleteRecord();
									
									/* evaluate result */
									if ($i_return <= 0) {
										throw new forestException(0x10001423);
									}
									
									/* clear file value */
									$this->Twig->{$s_column} = 'NULL';
									/* update record */
									$i_result = $this->Twig->UpdateRecord();
									
									/* evaluate result */
									if ($i_result == -1) {
										throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
									}
									
									throw new forestException(0x10001424, array($s_path));
								}
								
								/* move file to target folder with new name */
								if (!(@move_uploaded_file($_FILES[$this->Twig->fphp_Table . '_' . $s_column]['tmp_name'], $s_path . $o_filesTwig->Name))) {
									/* delete file record */
									$i_return = $o_filesTwig->DeleteRecord();
									
									/* evalute result */
									if ($i_return <= 0) {
										throw new forestException(0x10001423);
									}
									
									/* clear file value */
									$this->Twig->{$s_column} = 'NULL';
									/* update record */
									$i_result = $this->Twig->UpdateRecord();
									
									/* evaluate result */
									if ($i_result == -1) {
										throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
									}
									
									throw new forestException(0x10001425, array($o_filesTwig->DisplayName));
								}
								
								/* check if versioning is activated */
								if ( ($i_versioning == 100) && (!$o_oldFilesTwig->IsEmpty()) ) {
									/* assume checkout if old file record has checkout entry */
									if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_oldFilesTwig->UUID), array('ForeignUUID'))) {
										$o_checkoutTwig->ForeignUUID = $o_filesTwig->UUID;
										
										/* update checkout entry */
										$i_result = $o_checkoutTwig->UpdateRecord();
				
										/* evaluate result */
										if ($i_result == -1) {
											throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
										}
									}
									
									/* assume old version */
									$o_filesTwig->Major = $o_oldFilesTwig->Major;
									$o_filesTwig->Minor = $o_oldFilesTwig->Minor;
									
									/* increase new version */
									/* if head record, current record or file record is checked out, increase minor version */
									if ( ( ($o_glob->HeadTwig != null) && (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($this->Twig->HeadUUID), array('ForeignUUID'))) ) || (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($this->Twig->UUID), array('ForeignUUID'))) || (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_filesTwig->UUID), array('ForeignUUID'))) ) {
										$o_filesTwig->Minor = $o_filesTwig->Minor + 1;
									} else {
										/* otherwise increase major version and reset minor version */
										$o_filesTwig->Major = $o_filesTwig->Major + 1;
										$o_filesTwig->Minor = 0;
									}
									
									/* assume created fields and update modified fields */
									$o_filesTwig->Created = $o_oldFilesTwig->Created;
									$o_filesTwig->CreatedBy = $o_oldFilesTwig->CreatedBy;
									$o_filesTwig->Modified = new \fPHP\Helper\forestDateTime;
									$o_filesTwig->ModifiedBy = $o_glob->Security->UserUUID;
									
									/* update old version entry of old file record */
									$o_oldFilesTwig->ForeignUUID = $o_filesTwig->UUID;
									$i_result = $o_oldFilesTwig->UpdateRecord();
			
									/* evaluate result */
									if ($i_result == -1) {
										throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
									}
									
									/* get all files linked to old version entry */
									$o_filesOldVersionsTwig = new \fPHP\Twigs\filesTwig; 
			
									$a_sqlAdditionalFilter = array(array('column' => 'ForeignUUID', 'value' => $o_oldFilesTwig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
									$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
									$o_files = $o_filesOldVersionsTwig->GetAllRecords(true);
									$o_glob->Temp->Del('SQLAdditionalFilter');
									
									foreach ($o_files->Twigs as $o_file) {
										/* update foreign uuid to new file record */
										$o_file->ForeignUUID = $o_filesTwig->UUID;
										$i_result = $o_file->UpdateRecord();
			
										/* evaluate result */
										if ($i_result == -1) {
											throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
										}
									}
								}
								
								/* append new file to current record */
								$o_filesTwig->ForeignUUID = $this->Twig->UUID;
								
								/* update new version entry of current file record */
								$i_result = $o_filesTwig->UpdateRecord();
								
								/* evaluate result */
								if ($i_result == -1) {
									throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
								}
								
								$b_upload_done = true;
								
								$o_glob->Temp->Del($s_column . '_uploadFilesTwigUUID');
							}
						}
					}
				}
			}
		}
		
		if (array_key_exists('fphp_dropzonePostData', $_POST)) {
			if (!empty($_POST['fphp_dropzonePostData'])) {
				if ($o_glob->Temp->Exists('fphp_dropzonePostDataAmount')) {
					$o_filesTwig = new \fPHP\Twigs\filesTwig;
					
					/* iterate each file in post files data */
					for ($i = 0; $i < intval($o_glob->Temp->{'fphp_dropzonePostDataAmount'}); $i++) {
						if ($o_glob->Temp->Exists('fphp_dropzonePostData_' . $i)) {
							/* get file record which has been already inserted */
							if ($o_filesTwig->GetRecord(array($o_glob->Temp->{'fphp_dropzonePostData_' . $i}))) {
								/* create file folder structure if not created */
								\fPHP\Helper\forestFile::CreateFileFolderStructure($o_glob->URL->BranchId);
								$s_folder = substr(pathinfo($o_filesTwig->Name, PATHINFO_FILENAME), 6, 2);
								
								$s_path = '';
			
								if (count($o_glob->URL->Branches) > 0) {
									foreach($o_glob->URL->Branches as $s_value) {
										$s_path .= $s_value . '/';
									}
								}
								
								$s_path .= $o_glob->URL->Branch . '/';
								
								$s_path = './trunk/' . $s_path . 'fphp_files/' . $s_folder . '/';
								
								if (!is_dir($s_path)) {
									/* delete file record */
									$i_return = $o_filesTwig->DeleteRecord();
									
									/* evalute result */
									if ($i_return <= 0) {
										throw new forestException(0x10001423);
									}
									
									throw new forestException(0x10001424, array($s_path));
								}
								
								if (!(@rename('./temp_files/' . $o_filesTwig->DisplayName, $s_path . $o_filesTwig->Name))) {
									/* delete file record */
									$i_return = $o_filesTwig->DeleteRecord();
									
									/* evalute result */
									if ($i_return <= 0) {
										throw new forestException(0x10001423);
									}
									
									throw new forestException(0x10001425, array($o_filesTwig->DisplayName));
								}
								
								/* append new file to current record */
								$o_filesTwig->ForeignUUID = $this->Twig->UUID;
								/* change filename by deleting the random part */
								$o_filesTwig->DisplayName = substr($o_filesTwig->DisplayName, 7);
								
								/* update file record */
								$i_result = $o_filesTwig->UpdateRecord();
			
								/* evalute result */
								if ($i_result == -1) {
									throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
								}
								
								$b_upload_done = true;
								
								$o_glob->Temp->Del('fphp_dropzonePostData_' . $i);
							}
						}
					}
				}
			}
		}
		
		/* render system message if at least one upload has been done */
		if ($b_upload_done) {
			$o_glob->SystemMessages->Add(new forestException(0x10001426));
		}
	}
	
	/**
	 * undo created file records if there are any exceptions for new records or editing records
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function UndoFilesEntries() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		foreach ($this->Twig->fphp_Mapping as $s_column) {
			if ( ($s_column != 'Id') && ($s_column != 'UUID') ) {
				if (array_key_exists($this->Twig->fphp_Table . '_' . $s_column, $_FILES)) {
					if ( (!empty($_FILES[$this->Twig->fphp_Table . '_' . $s_column]['name'])) && (!empty($_FILES[$this->Twig->fphp_Table . '_' . $s_column]['tmp_name'])) && (intval($_FILES[$this->Twig->fphp_Table . '_' . $s_column]['size']) != 0) ) {
						$o_filesTwig = new \fPHP\Twigs\filesTwig;
						
						if ($o_glob->Temp->Exists($s_column . '_uploadFilesTwigUUID')) {
							if ($o_filesTwig->GetRecord(array($o_glob->Temp->{$s_column . '_uploadFilesTwigUUID'}))) {
								/* delete file record */
								$i_return = $o_filesTwig->DeleteRecord();
								
								/* evaluate result */
								if ($i_return <= 0) {
									throw new forestException(0x10001423);
								}
							}
						}
					}
				}
			}
		}
		
		if (array_key_exists('fphp_dropzonePostData', $_POST)) {
			if (!empty($_POST['fphp_dropzonePostData'])) {
				if ($o_glob->Temp->Exists('fphp_dropzonePostDataAmount')) {
					$o_filesTwig = new \fPHP\Twigs\filesTwig;
					
					/* iterate all files which has been uploaded by dropzone */
					for ($i = 0; $i < intval($o_glob->Temp->{'fphp_dropzonePostDataAmount'}); $i++) {
						if ($o_glob->Temp->Exists('fphp_dropzonePostData_' . $i)) {
							if ($o_filesTwig->GetRecord(array($o_glob->Temp->{'fphp_dropzonePostData_' . $i}))) {
								/* delete file record */
								$i_return = $o_filesTwig->DeleteRecord();
								
								/* evaluate result */
								if ($i_return <= 0) {
									throw new forestException(0x10001423);
								}
							}
						}
					}
				}
			}
		}
	}
	
	/**
	 * get validation rules of form element
	 *
	 * @param string $p_s_tableFieldUUID  uuid of tablefield which is part of validation rule primary key
	 * @param array $p_a_validationRules  array object where found validation rules will be inserted, passed by reference
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function GetValidationRules($p_s_tableFieldUUID, &$p_a_validationRules) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$o_querySelect = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::SELECT, 'sys_fphp_tablefield_validationrule');
		
		$column_A = new \fPHP\Base\forestSQLColumn($o_querySelect);
			$column_A->Column = '*';
		
		$o_querySelect->Query->Columns->Add($column_A);
		
		$join_A = new \fPHP\Base\forestSQLJoin($o_querySelect);
			$join_A->JoinType = 'INNER JOIN';
			$join_A->Table = 'sys_fphp_validationrule';

		$relation_A = new \fPHP\Base\forestSQLRelation($o_querySelect);
		
		$column_B = new \fPHP\Base\forestSQLColumn($o_querySelect);
			$column_B->Column = 'ValidationruleUUID';
			
		$column_C = new \fPHP\Base\forestSQLColumn($o_querySelect);
			$column_C->Column = 'UUID';
			$column_C->Table = $join_A->Table;
		
		$relation_A->ColumnLeft = $column_B;
		$relation_A->ColumnRight = $column_C;
		$relation_A->Operator = '=';
		
		$join_A->Relations->Add($relation_A);
			
		$o_querySelect->Query->Joins->Add($join_A);
		
		$column_D = new \fPHP\Base\forestSQLColumn($o_querySelect);
			$column_D->Column = 'TablefieldUUID';
		
		$where_A = new \fPHP\Base\forestSQLWhere($o_querySelect);
			$where_A->Column = $column_D;
			$where_A->Value = $where_A->ParseValue($p_s_tableFieldUUID);
			$where_A->Operator = '=';
		
		$o_querySelect->Query->Where->Add($where_A);
		
		$o_result = $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_querySelect, false, false);
		
		foreach ($o_result as $o_row) {
			$p_a_validationRules[] = $o_row;
		}
	}
	
	/**
	 * handle replace file record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function replaceFileAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		$o_glob->HeadTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\filesTwig;
		
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'editFileKey'), 'editFileKey' );
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'subConstraintKey'), 'subConstraintKey' );
		
		/* sub constraint key can be the uuid of the sub constraint or the table uuid */
		if ($o_glob->Temp->{'subConstraintKey'} != null) {
			/* query sub constraint if we have sub constraint key in url parameters */
			$o_subconstraintTwig = new \fPHP\Twigs\subconstraintTwig;
			
			if (! ($o_subconstraintTwig->GetRecord(array($o_glob->Temp->{'subConstraintKey'}))) ) {
				$i_infoColumns = $o_glob->TablesInformation[$o_glob->Temp->{'subConstraintKey'}]['InfoColumns'];
				$i_versioning = $o_glob->TablesInformation[$o_glob->Temp->{'subConstraintKey'}]['Versioning'];
			} else {
				/* get value for info columns when configured */
				$i_infoColumns = $o_glob->TablesInformation[$o_subconstraintTwig->TableUUID]['InfoColumns'];
				$i_versioning = $o_glob->TablesInformation[$o_subconstraintTwig->TableUUID]['Versioning'];
			}
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if ($o_glob->IsPost) {
			if (array_key_exists('sys_fphp_editFileKey', $_POST)) {
				/* query sub record */
				if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_editFileKey']))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				$b_superordinateCheckout = false;
				$o_subrecord = new \fPHP\Twigs\subrecordsTwig;
				
				if (($o_subrecord->GetRecord(array($this->Twig->ForeignUUID))) ) {
					if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_subrecord->HeadUUID), array('ForeignUUID'))) {
						$b_superordinateCheckout = true;
						
						/* check if user is the same user who has checked out the record */
						if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
							throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
						}
					}
					
					if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_subrecord->UUID), array('ForeignUUID'))) {
						$b_superordinateCheckout = true;
						
						/* check if user is the same user who has checked out the record */
						if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
							throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
						}
					}
				}
				
				if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($this->Twig->ForeignUUID), array('ForeignUUID'))) {
					$b_superordinateCheckout = true;
					
					/* check if user is the same user who has checked out the record */
					if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
						throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
					}
				}
				
				if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($this->Twig->UUID), array('ForeignUUID'))) {
					/* check if user is the same user who has checked out the record */
					if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
						throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
					}
				}
				
				if (method_exists($this, 'beforeReplaceFileAction')) {
					$this->beforeReplaceFileAction();
				}
				
				/* check if versioning is activated */
				if ($i_versioning != 100) {
					/* delete old file */
					$s_folder = substr(pathinfo($this->Twig->Name, PATHINFO_FILENAME), 6, 2);
					
					$s_path = '';

					if (count($o_glob->URL->Branches) > 0) {
						foreach($o_glob->URL->Branches as $s_value) {
							$s_path .= $s_value . '/';
						}
					}
					
					$s_path .= $o_glob->URL->Branch . '/';
					
					$s_path = './trunk/' . $s_path . 'fphp_files/' . $s_folder . '/';
					
					if (is_dir($s_path)) {
						if (file_exists($s_path . $this->Twig->Name)) {
							if (!(@unlink($s_path . $this->Twig->Name))) {
								throw new forestException(0x10001422, array($s_path . $this->Twig->Name));
							}
						}
					}
				}
				
				/* generate new filename, and get display name */
				$s_fileName = $_FILES['sys_fphp_files_NewFile']['name'];
				$s_newFilename = strtoupper(substr($o_glob->Security->GenRandomHash(), 0, 8));
				$s_extension = pathinfo($s_fileName, PATHINFO_EXTENSION);
				$s_newFilename .= '.' . $s_extension;
				
				/* check file structure */
				\fPHP\Helper\forestFile::CreateFileFolderStructure($o_glob->URL->BranchId);
				$s_folder = substr(pathinfo($s_newFilename, PATHINFO_FILENAME), 6, 2);
				
				$s_path = '';

				if (count($o_glob->URL->Branches) > 0) {
					foreach($o_glob->URL->Branches as $s_value) {
						$s_path .= $s_value . '/';
					}
				}
				
				$s_path .= $o_glob->URL->Branch . '/';
				
				$s_path = './trunk/' . $s_path . 'fphp_files/' . $s_folder . '/';
				
				if (!is_dir($s_path)) { /* target path does not exists */
					$i_return = $this->Twig->DeleteRecord();
					
					if ($i_return <= 0) {
						throw new forestException(0x10001423);
					}
					
					throw new forestException(0x10001424, array($s_path));
				}
				
				/* upload file to target folder with new name */
				if (!(@move_uploaded_file($_FILES['sys_fphp_files_NewFile']['tmp_name'], $s_path . $s_newFilename))) {
					$i_return = $this->Twig->DeleteRecord();
					
					if ($i_return <= 0) {
						throw new forestException(0x10001423);
					}
					
					throw new forestException(0x10001425, array($s_fileName));
				}
				
				/* check if versioning is activated */
				if ($i_versioning == 100) {
					/* create old version entry of current file record */
					$o_oldFile = new \fPHP\Twigs\filesTwig;
					$o_oldFile->BranchId = $this->Twig->BranchId;
					$o_oldFile->ForeignUUID = $this->Twig->UUID;
					$o_oldFile->Name = $this->Twig->Name;
					$o_oldFile->DisplayName = $this->Twig->DisplayName;
					$o_oldFile->Major = $this->Twig->Major;
					$o_oldFile->Minor = $this->Twig->Minor;
					
					/* set values for info columns when configured */
					if ($i_infoColumns == 10) {
						$o_oldFile->Created = $this->Twig->Created;
						$o_oldFile->CreatedBy = $this->Twig->CreatedBy;
					} else if ($i_infoColumns == 100) {
						$o_oldFile->Modified = $this->Twig->Modified;
						$o_oldFile->ModifiedBy = $this->Twig->ModifiedBy;
					} else if ($i_infoColumns == 1000) {
						$o_oldFile->Created = $this->Twig->Created;
						$o_oldFile->CreatedBy = $this->Twig->CreatedBy;
						$o_oldFile->Modified = $this->Twig->Modified;
						$o_oldFile->ModifiedBy = $this->Twig->ModifiedBy;
					}
					
					/* insert old version entry of current file record */
					$i_result = $o_oldFile->InsertRecord();
					
					/* evaluate result */
					if ($i_result == -1) {
						$this->CreateLogEntry('could not save old version of file - unique issue - ' . $o_glob->Temp->{'UniqueIssue'}, $o_oldFile);
						throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
					} else if ($i_result == 0) {
						$this->CreateLogEntry('could not save old version of file', $o_oldFile);
						throw new forestException(0x10001402);
					}
				}
				
				/* change record data */
				$this->Twig->Name = $s_newFilename;
				$this->Twig->DisplayName = $s_fileName;
				
				/* check if versioning is activated */
				if ($i_versioning == 100) {
					/* if file or superordinate elements are checked out, increase minor version */
					if ( (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($this->Twig->UUID), array('ForeignUUID'))) || ($b_superordinateCheckout) ) {
						$this->Twig->Minor = $this->Twig->Minor + 1;
					} else {
						/* otherwise increase major version and reset minor version */
						$this->Twig->Major = $this->Twig->Major + 1;
						$this->Twig->Minor = 0;
					}
				}
				
				/* set values for info columns when configured */
				if ($i_infoColumns == 100) {
					$this->Twig->Modified = new \fPHP\Helper\forestDateTime;
					$this->Twig->ModifiedBy = $o_glob->Security->UserUUID;
				} else if ($i_infoColumns == 1000) {
					$this->Twig->Modified = new \fPHP\Helper\forestDateTime;
					$this->Twig->ModifiedBy = $o_glob->Security->UserUUID;
				}
				
				/* edit file recrod */
				$i_result = $this->Twig->UpdateRecord();
				
				/* evaluate result */
				if ($i_result == -1) {
					$this->CreateLogEntry('could not replace file - unique issue - ' . $o_glob->Temp->{'UniqueIssue'}, $this->Twig);
					throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
				} else if ($i_result == 1) {
					if (method_exists($this, 'afterReplaceFileAction')) {
						$this->afterReplaceFileAction();
					}
					
					$this->CreateLogEntry('file has been replaced', $this->Twig);
					
					$o_glob->SystemMessages->Add(new forestException(0x1000143C));
					
					/* check if user chose auto chekin */
					if ( (array_key_exists('sys_fphp_files_AutocheckinStandard', $_POST)) && (boolval($_POST['sys_fphp_files_AutocheckinStandard'])) ) {
						$i_foo = 0;
						$this->executeCheckin($this->Twig->UUID, $i_foo, $o_glob->HeadTwig);
					}
				}
			}
		} else {
			if ( ($o_glob->Temp->Exists('editFileKey')) && ($o_glob->Temp->{'editFileKey'} != null) ) {
				/* query file record */
				if (! ($this->Twig->GetRecord(array($o_glob->Temp->{'editFileKey'}))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				$o_subrecord = new \fPHP\Twigs\subrecordsTwig;
				
				if (($o_subrecord->GetRecord(array($this->Twig->ForeignUUID))) ) {
					if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_subrecord->HeadUUID), array('ForeignUUID'))) {
						/* check if user is the same user who has checked out the record */
						if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
							throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
						}
					}
					
					if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_subrecord->UUID), array('ForeignUUID'))) {
						/* check if user is the same user who has checked out the record */
						if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
							throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
						}
					}
				}
				
				if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($this->Twig->ForeignUUID), array('ForeignUUID'))) {
					/* check if user is the same user who has checked out the record */
					if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
						throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
					}
				}
				
				if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($this->Twig->UUID), array('ForeignUUID'))) {
					/* check if user is the same user who has checked out the record */
					if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
						throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
					}
				}
				
				/* create modal form for file record */
				$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig);
				$s_title = $o_glob->GetTranslation('ReplaceFile', 1);
				$o_glob->PostModalForm->CreateModalForm($this->Twig, $s_title);
				
				/* create TEXT element to show old file name */
				$o_text = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::TEXT);
				$o_text->Label = $o_glob->GetTranslation('formOldFileLabel', 1);
				$o_text->Id = 'sys_fphp_files_OldFile';
				$o_text->Value = $this->Twig->DisplayName;
				$o_text->Readonly = true;
				$o_glob->PostModalForm->FormElements->Add($o_text);
				
				/* create FILE element to select a new file for replacement */
				$o_file = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::FILE);
				$o_file->Label = $o_glob->GetTranslation('formNewFileLabel', 1);
				$o_file->Id = 'sys_fphp_files_NewFile';
				$o_file->ValMessage = $o_glob->GetTranslation('formNewFileValMessage', 1);
				$o_file->Required = true;
				$o_glob->PostModalForm->FormElements->Add($o_file);
				
				/* add validation rule for manual created FILE form element */
				$o_glob->PostModalForm->FormObject->ValRules->Add(new \fPHP\Forms\forestFormValidationRule('sys_fphp_files_NewFile', 'required', 'true'));
				
				/* add auto checkin form element if current record is checked out */
				if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_glob->Temp->{'editFileKey'}), array('ForeignUUID'))) {
					/* query auto checkin form element */
					if (!(($o_formelementTwig = new \fPHP\Twigs\formelementTwig)->GetRecordPrimary(array(\fPHP\Forms\forestFormElement::AUTOCHECKIN), array('Name')))) {
						throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
					}
					
					/* create auto checkin form element and adjust settings */
					$o_formElement = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::AUTOCHECKIN);
					$o_formElement->loadJSON($o_formelementTwig->JSONEncodedSettings);
					$o_formElement->Id = 'sys_fphp_files_AutocheckinStandard';
					$o_glob->PostModalForm->FormElements->Add($o_formElement);
				}
				
				/* create hidden element to store file UUID */
				$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_editFileKey';
				$o_hidden->Value = $o_glob->Temp->{'editFileKey'};
				$o_glob->PostModalForm->FormElements->Add($o_hidden);
			}
		}
		
		if (isset($this->KeepFilter)) {
			if ($this->KeepFilter->value) {
				if ($o_glob->Security->SessionData->Exists('last_filter')) {
					$o_glob->Security->SessionData->Add($o_glob->Security->SessionData->{'last_filter'}, 'filter');
				}
			}
		}
		
		$this->Twig = $o_glob->HeadTwig;
		$this->StandardView = \fPHP\Branches\forestBranch::DETAIL; /* because it only makes sense if we stay in detail view, when we open modal read only form for sub record */
		$this->SetNextAction('init');
	}
	
	/**
	 * handle restore file record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function restoreFileAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		$o_glob->HeadTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\filesTwig;
		
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'editFileKey'), 'editFileKey' );
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'subConstraintKey'), 'subConstraintKey' );
		
		/* sub constraint key can be the uuid of the sub constraint or the table uuid */
		if ($o_glob->Temp->{'subConstraintKey'} != null) {
			/* query sub constraint if we have sub constraint key in url parameters */
			$o_subconstraintTwig = new \fPHP\Twigs\subconstraintTwig;
			
			if (! ($o_subconstraintTwig->GetRecord(array($o_glob->Temp->{'subConstraintKey'}))) ) {
				$i_infoColumns = $o_glob->TablesInformation[$o_glob->Temp->{'subConstraintKey'}]['InfoColumns'];
				$i_versioning = $o_glob->TablesInformation[$o_glob->Temp->{'subConstraintKey'}]['Versioning'];
			} else {
				/* get value for info columns when configured */
				$i_infoColumns = $o_glob->TablesInformation[$o_subconstraintTwig->TableUUID]['InfoColumns'];
				$i_versioning = $o_glob->TablesInformation[$o_subconstraintTwig->TableUUID]['Versioning'];
			}
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if ($o_glob->IsPost) {
			if ( (array_key_exists('sys_fphp_editFileKey', $_POST)) && ($i_versioning == 100) ) {
				$o_glob->Base->{$o_glob->ActiveBase}->ManualTransaction();
				
				/* query sub record */
				if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_editFileKey']))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				/* query root file record */
				$o_rootFile = new \fPHP\Twigs\filesTwig;
				
				if (! ($o_rootFile->GetRecord(array($this->Twig->ForeignUUID))) ) {
					throw new forestException(0x10001401, array($o_rootFile->fphp_Table));
				}
				
				$o_subrecord = new \fPHP\Twigs\subrecordsTwig;
				
				if (($o_subrecord->GetRecord(array($o_rootFile->ForeignUUID))) ) {
					if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_subrecord->HeadUUID), array('ForeignUUID'))) {
						/* check if user is the same user who has checked out the record */
						if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
							throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
						}
					}
					
					if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_subrecord->UUID), array('ForeignUUID'))) {
						/* check if user is the same user who has checked out the record */
						if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
							throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
						}
					}
				}
				
				if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_rootFile->ForeignUUID), array('ForeignUUID'))) {
					/* check if user is the same user who has checked out the record */
					if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
						throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
					}
				}
				
				if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_rootFile->UUID), array('ForeignUUID'))) {
					/* check if user is the same user who has checked out the record */
					if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
						throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
					}
				}
				
				if (method_exists($this, 'beforeRestoreFileAction')) {
					$this->beforeRestoreFileAction();
				}
				
				/* look for file versions which are higher than the file record we want to restore */
				$o_filesTwig = new \fPHP\Twigs\filesTwig; 
				
				$a_sqlAdditionalFilter = array(
					array('column' => 'UUID', 'value' => $this->Twig->UUID, 'operator' => '<>', 'filterOperator' => 'AND'),
					array('column' => 'ForeignUUID', 'value' => $this->Twig->ForeignUUID, 'operator' => '=', 'filterOperator' => 'AND'),
					array('column' => 'Major', 'value' => $this->Twig->Major, 'operator' => '>', 'filterOperator' => 'AND'),
					array('column' => 'Major', 'value' => $this->Twig->Major, 'operator' => '=', 'filterOperator' => 'OR'),
					array('column' => 'Minor', 'value' => $this->Twig->Minor, 'operator' => '>', 'filterOperator' => 'AND')
				);
				$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
				$o_files = $o_filesTwig->GetAllRecords(true);
				$o_glob->Temp->Del('SQLAdditionalFilter');
				
				foreach ($o_files->Twigs as $o_file) {
					/* delete file */
					$this->executeDeleteFileRecord($o_file, true, false);
				}
				
				/* delete root file */
				$this->executeDeleteFileRecord($o_rootFile, false, false);
				
				/* delete file record */
				$i_return = $this->Twig->DeleteRecord();
				
				/* evaluate the result */
				if ($i_return <= 0) {
					throw new forestException(0x10001423);
				}
				
				/* save information of file record which we want to restore in root file */
				$o_rootFile->Name = $this->Twig->Name;
				$o_rootFile->DisplayName = $this->Twig->DisplayName;
				$o_rootFile->Major = $this->Twig->Major;
				$o_rootFile->Minor = $this->Twig->Minor;
				$o_rootFile->Created = $this->Twig->Created;
				$o_rootFile->CreatedBy = $this->Twig->CreatedBy;
				$o_rootFile->Modified = $this->Twig->Modified;
				$o_rootFile->ModifiedBy = $this->Twig->ModifiedBy;
				
				/* edit file recrod */
				$i_result = $o_rootFile->UpdateRecord(true);
				
				/* evaluate result */
				if ($i_result == -1) {
					$this->CreateLogEntry('could not restore old file - unique issue - ' . $o_glob->Temp->{'UniqueIssue'}, $o_rootFile);
					throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
				} else if ($i_result == 1) {
					if (method_exists($this, 'afterRestoreFileAction')) {
						$this->afterRestoreFileAction();
					}
					
					$this->CreateLogEntry('file has been restored', $o_rootFile);
					
					$o_glob->SystemMessages->Add(new forestException(0x1000143D));
				}
			}
		} else {
			if ( ($o_glob->Temp->Exists('editFileKey')) && ($o_glob->Temp->{'editFileKey'} != null) && ($i_versioning == 100) ) {
				/* query file record */
				if (! ($this->Twig->GetRecord(array($o_glob->Temp->{'editFileKey'}))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				/* query root file record */
				$o_rootFile = new \fPHP\Twigs\filesTwig;
				
				if (! ($o_rootFile->GetRecord(array($this->Twig->ForeignUUID))) ) {
					throw new forestException(0x10001401, array($o_rootFile->fphp_Table));
				}
				
				$o_subrecord = new \fPHP\Twigs\subrecordsTwig;
				
				if (($o_subrecord->GetRecord(array($o_rootFile->ForeignUUID))) ) {
					if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_subrecord->HeadUUID), array('ForeignUUID'))) {
						/* check if user is the same user who has checked out the record */
						if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
							throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
						}
					}
					
					if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_subrecord->UUID), array('ForeignUUID'))) {
						/* check if user is the same user who has checked out the record */
						if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
							throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
						}
					}
				}
				
				if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_rootFile->ForeignUUID), array('ForeignUUID'))) {
					/* check if user is the same user who has checked out the record */
					if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
						throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
					}
				}
				
				if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_rootFile->UUID), array('ForeignUUID'))) {
					/* check if user is the same user who has checked out the record */
					if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
						throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
					}
				}
				
				/* create modal confirmation form for restore file record */
				$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig);
				$s_title = $o_glob->GetTranslation('RestoreFileModalTitle', 1);
				$s_description = '<b>' . \fPHP\Helper\forestStringLib::sprintf2($o_glob->GetTranslation('RestoreFileModalDescription', 1), array($this->Twig->DisplayName, $this->Twig->Major . $o_glob->Trunk->VersionDelimiter . $this->Twig->Minor)) . '</b>';
				$o_glob->PostModalForm->CreateDeleteModalForm($this->Twig, $s_title, $s_description);
				
				/* create hidden element to store file UUID */
				$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_editFileKey';
				$o_hidden->Value = $this->Twig->UUID;
				$o_glob->PostModalForm->FormElements->Add($o_hidden);
			}
		}
		
		if (isset($this->KeepFilter)) {
			if ($this->KeepFilter->value) {
				if ($o_glob->Security->SessionData->Exists('last_filter')) {
					$o_glob->Security->SessionData->Add($o_glob->Security->SessionData->{'last_filter'}, 'filter');
				}
			}
		}
		
		$this->Twig = $o_glob->HeadTwig;
		$this->StandardView = \fPHP\Branches\forestBranch::DETAIL; /* because it only makes sense if we stay in detail view, when we open modal read only form for sub record */
		$this->SetNextAction('init');
	}
	
	
	/**
	 * handle checkout record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function checkoutAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if versioning of twig is really enabled */
		if ($o_glob->TablesInformation[$this->Twig->fphp_TableUUID]['Versioning'] > 1) {
			$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'editKey'), 'editKey' );
			
			if (!$o_glob->IsPost) {
				/* checkout record form */
				if ( ($o_glob->Temp->Exists('editKey')) && ($o_glob->Temp->{'editKey'} != null) ) {
					/* check checkout for superordinate elements */
					$o_filesTwig = new \fPHP\Twigs\filesTwig;
					$o_subrecordsTwig = new \fPHP\Twigs\subrecordsTwig;
					
					/* if record is a file record */
					if ($o_filesTwig->GetRecord(array($o_glob->Temp->{'editKey'}))) {
						/* if superordinate element of file record is a sub record */
						if (($o_subrecordsTwig->GetRecord(array($o_filesTwig->ForeignUUID))) ) {
							/* check head record of sub record */
							if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_subrecordsTwig->HeadUUID), array('ForeignUUID'))) {
								throw new forestException(0x1000143B);
							}
							
							/* check sub record */
							if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_subrecordsTwig->UUID), array('ForeignUUID'))) {
								throw new forestException(0x1000143B);
							}
						}
						
						/* check superordinate element of file record */
						if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_filesTwig->ForeignUUID), array('ForeignUUID'))) {
							throw new forestException(0x1000143B);
						}
					}
					
					/* if record is a sub record */
					if (($o_subrecordsTwig->GetRecord(array($o_glob->Temp->{'editKey'}))) ) {
						/* check head record of sub record */
						if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_subrecordsTwig->HeadUUID), array('ForeignUUID'))) {
							throw new forestException(0x1000143B);
						}
					}
					
					/* create modal confirmation form for checkout record */
					$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig);
					$s_title = $o_glob->GetTranslation('CheckoutModalTitle', 1);
					
					if (count(explode('~', $o_glob->Temp->{'editKey'})) == 1) {
						$s_description = '<b>' . $o_glob->GetTranslation('CheckoutModalDescriptionOne', 1) . '</b>';
					} else {
						$s_description = \fPHP\Helper\forestStringLib::sprintf2('<b>' . $o_glob->GetTranslation('CheckoutModalDescriptionMultiple', 1) . '</b>', array(count(explode('~', $o_glob->Temp->{'editKey'}))));
					}
					
					$o_glob->PostModalForm->CreateDeleteModalForm($this->Twig, $s_title, $s_description);
					
					$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
					$o_hidden->Id = 'sys_fphp_checkoutKey';
					$o_hidden->Value = $o_glob->Temp->{'editKey'};
					$o_glob->PostModalForm->FormElements->Add($o_hidden);
				}
			} else {
				/* checkout record(s) */
				if (array_key_exists('sys_fphp_checkoutKey', $_POST)) {
					$a_checkoutKeys = explode('~', $_POST['sys_fphp_checkoutKey']);
					$i_checkoutDone = 0;
					
					foreach ($a_checkoutKeys as $s_checkoutKey) {
						if (!($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($s_checkoutKey), array('ForeignUUID'))) {
							$o_newCheckoutTwig = new \fPHP\Twigs\checkoutTwig;
							$o_newCheckoutTwig->ForeignUUID = $s_checkoutKey;
							$o_newCheckoutTwig->Timestamp = new \fPHP\Helper\forestDateTime;
							$o_newCheckoutTwig->UserUUID = $o_glob->Security->UserUUID;
							
							/* insert record */
							$i_result = $o_newCheckoutTwig->InsertRecord();
							
							/* evaluate result */
							if ($i_result == -1) {
								$this->CreateLogEntry('could not checkout record - unique issue - ' . $o_glob->Temp->{'UniqueIssue'}, $o_newCheckoutTwig);
								throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
							} else if ($i_result == 0) {
								$this->CreateLogEntry('could not checkout record', $o_newCheckoutTwig);
								throw new forestException(0x10001402);
							} else if ($i_result == 1) {
								$this->CreateLogEntry('record has been checked out', $o_newCheckoutTwig);
								$i_checkoutDone++;
							}
						}
					}
					
					if ($i_checkoutDone == 1) {
						$o_glob->SystemMessages->Add(new forestException(0x10001434));
					} else if ($i_checkoutDone > 1) {
						$o_glob->SystemMessages->Add(new forestException(0x10001435));
					}
				}
			}
		}
		
		if (isset($this->KeepFilter)) {
			if ($this->KeepFilter->value) {
				if ($o_glob->Security->SessionData->Exists('last_filter')) {
					$o_glob->Security->SessionData->Add($o_glob->Security->SessionData->{'last_filter'}, 'filter');
				}
			}
		}
		
		if ($o_glob->Security->SessionData->Exists('lastView')) {
			$this->StandardView = $o_glob->Security->SessionData->{'lastView'};
		}
		
		$this->SetNextAction('init');
	}
	
	/**
	 * handle checkin record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function checkinAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if versioning of twig is really enabled */
		if ($o_glob->TablesInformation[$this->Twig->fphp_TableUUID]['Versioning'] > 1) {
			$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'editKey'), 'editKey' );
			
			if (!$o_glob->IsPost) {
				/* checkin record form */
				if ( ($o_glob->Temp->Exists('editKey')) && ($o_glob->Temp->{'editKey'} != null) ) {
					/* create modal confirmation form for checkin record */
					$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig);
					$s_title = $o_glob->GetTranslation('CheckinModalTitle', 1);
					
					if (count(explode('~', $o_glob->Temp->{'editKey'})) == 1) {
						$s_description = '<b>' . $o_glob->GetTranslation('CheckinModalDescriptionOne', 1) . '</b>';
					} else {
						$s_description = \fPHP\Helper\forestStringLib::sprintf2('<b>' . $o_glob->GetTranslation('CheckinModalDescriptionMultiple', 1) . '</b>', array(count(explode('~', $o_glob->Temp->{'editKey'}))));
					}
					
					$o_glob->PostModalForm->CreateDeleteModalForm($this->Twig, $s_title, $s_description);
					
					$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
					$o_hidden->Id = 'sys_fphp_checkinKey';
					$o_hidden->Value = $o_glob->Temp->{'editKey'};
					$o_glob->PostModalForm->FormElements->Add($o_hidden);
				}
			} else {
				/* checkin record(s) */
				if (array_key_exists('sys_fphp_checkinKey', $_POST)) {
					$a_checkinKeys = explode('~', $_POST['sys_fphp_checkinKey']);
					$i_checkinDone = 0;
					
					foreach ($a_checkinKeys as $s_checkinKey) {
						$this->executeCheckin($s_checkinKey, $i_checkinDone);
					}
					
					if ($i_checkinDone == 1) {
						$o_glob->SystemMessages->Add(new forestException(0x10001436));
					} else if ($i_checkinDone > 1) {
						$o_glob->SystemMessages->Add(new forestException(0x10001437));
					}
				}
			}
		}
		
		if (isset($this->KeepFilter)) {
			if ($this->KeepFilter->value) {
				if ($o_glob->Security->SessionData->Exists('last_filter')) {
					$o_glob->Security->SessionData->Add($o_glob->Security->SessionData->{'last_filter'}, 'filter');
				}
			}
		}
		
		if ($o_glob->Security->SessionData->Exists('lastView')) {
			$this->StandardView = $o_glob->Security->SessionData->{'lastView'};
		}
		
		$this->SetNextAction('init');
	}
	
	/**
	 * re-usable checkin function
	 *
	 * @param string $p_s_recordUUID  uuid of record we want ot checkin
	 * @param integer $p_i_checkinDone  amount of checkin'S which have been done, passed by reference
	 * @param forestTwig $p_o_headTwig  if record is a sub record or file record, we adding head record to the function as parameter
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function executeCheckin($p_s_recordUUID, &$p_i_checkinDone, $p_o_headTwig = null) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		if ($p_o_headTwig != null) {
			$o_saveTwig = $this->Twig;
			$this->Twig = $p_o_headTwig;
		}
		
		/* check if versioning of twig is really enabled */
		if ($o_glob->TablesInformation[$this->Twig->fphp_TableUUID]['Versioning'] > 1) {
			if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($p_s_recordUUID), array('ForeignUUID'))) {
				/* check if user is the same user who has checked out the record */
				if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
					/* if it is not the same user, check if we have a checkout interval in trunk or twig settings */
					if ( (issetStr($o_glob->Trunk->CheckoutInterval)) || (issetStr($o_glob->TablesInformation[$this->Twig->fphp_TableUUID]['CheckoutInterval'])) ) {
						/* twig settings overwrites trunk settings */
						if (issetStr($o_glob->TablesInformation[$this->Twig->fphp_TableUUID]['CheckoutInterval'])) {
							$o_DICheckoutInterval = new \fPHP\Helper\forestDateInterval($o_glob->TablesInformation[$this->Twig->fphp_TableUUID]['CheckoutInterval']);
						} else if (issetStr($o_glob->Trunk->CheckoutInterval)) {
							$o_DICheckoutInterval = new \fPHP\Helper\forestDateInterval($o_glob->Trunk->CheckoutInterval);
						}
						
						/* if checkout interval has not expired, user cannot checkin record */
						$o_checkoutTwig->Timestamp->AddDateInterval($o_DICheckoutInterval->y, $o_DICheckoutInterval->m, $o_DICheckoutInterval->d, $o_DICheckoutInterval->h, $o_DICheckoutInterval->i, $o_DICheckoutInterval->s);
						$o_now = new \fPHP\Helper\forestDateTime($o_glob->Trunk->DateTimeSqlFormat);
						
						if ($o_now < $o_checkoutTwig->Timestamp) {
							$o_diff = $o_checkoutTwig->Timestamp->DateTime->diff($o_now->DateTime);
							$o_DIDiff = new \fPHP\Helper\forestDateInterval();
							$o_DIDiff->SetDateInterval($o_diff);
						
							throw new forestException(0x10001439, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID), strval($o_DIDiff)));
						}
					} else {
						throw new forestException(0x10001438, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
					}
				}
				
				/* delete checkout record */
				$i_return = $o_checkoutTwig->DeleteRecord();
					
				/* evaluate the result */
				if ($i_return > 0) {
					$this->CreateLogEntry('record checked in', $o_checkoutTwig);
					$p_i_checkinDone++;
				} else {
					$this->CreateLogEntry('could not checkin record', $o_checkoutTwig);
				}
				
				/* if checked in element is a record */
				$s_foo = '\\fPHP\\Twigs\\' . $this->Twig->fphp_Table . 'Twig';
				\fPHP\Helper\forestStringLib::RemoveTablePrefix($s_foo);
				if (($o_record = new $s_foo)->GetRecord(array($p_s_recordUUID))) {
					/* iterate sub records of record */
					$o_subrecordsTwig = new \fPHP\Twigs\subrecordsTwig;
					
					$a_sqlAdditionalFilter = array(array('column' => 'HeadUUID', 'value' => $o_record->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
					$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
					$o_subRecords = $o_subrecordsTwig->GetAllRecords(true);
					$o_glob->Temp->Del('SQLAdditionalFilter');
					
					foreach ($o_subRecords->Twigs as $o_subRecord) {
						/* delete checkout of sub record */
						$this->executeDeleteCheckoutRecord($o_subRecord);
						
						/* iterate files of sub record */
						$o_filesTwig = new \fPHP\Twigs\filesTwig; 
	
						$a_sqlAdditionalFilter = array(array('column' => 'ForeignUUID', 'value' => $o_subRecord->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
						$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
						$o_files = $o_filesTwig->GetAllRecords(true);
						$o_glob->Temp->Del('SQLAdditionalFilter');
						
						foreach ($o_files->Twigs as $o_file) {
							/* delete checkout of file */
							$this->executeDeleteCheckoutRecord($o_file);
							
							/* update major version, if minor version > 0 */
							if ($o_file->Minor > 0) {
								$o_file->Major = $o_file->Major + 1;
								$o_file->Minor = 0;
								
								/* edit file recrod */
								$i_result = $o_file->UpdateRecord();
								
								/* evaluate result */
								if ($i_result == -1) {
									throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
								}
							}
						}
					}
					
					/* iterate files of record */
					$o_filesTwig = new \fPHP\Twigs\filesTwig; 

					$a_sqlAdditionalFilter = array(array('column' => 'ForeignUUID', 'value' => $o_record->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
					$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
					$o_files = $o_filesTwig->GetAllRecords(true);
					$o_glob->Temp->Del('SQLAdditionalFilter');
					
					foreach ($o_files->Twigs as $o_file) {
						/* delete checkout of file */
						$this->executeDeleteCheckoutRecord($o_file);
						
						/* update major version, if minor version > 0 */
						if ($o_file->Minor > 0) {
							$o_file->Major = $o_file->Major + 1;
							$o_file->Minor = 0;
							
							/* edit file recrod */
							$i_result = $o_file->UpdateRecord();
							
							/* evaluate result */
							if ($i_result == -1) {
								throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
							}
						}
					}
				}
				
				/* if checked in element is a sub record */
				if (($o_subrecords = new \fPHP\Twigs\subrecordsTwig)->GetRecord(array($p_s_recordUUID))) {
					/* iterate files of sub record */
					$o_filesTwig = new \fPHP\Twigs\filesTwig; 
	
					$a_sqlAdditionalFilter = array(array('column' => 'ForeignUUID', 'value' => $o_subrecords->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
					$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
					$o_files = $o_filesTwig->GetAllRecords(true);
					$o_glob->Temp->Del('SQLAdditionalFilter');
					
					foreach ($o_files->Twigs as $o_file) {
						/* delete checkout of file */
						$this->executeDeleteCheckoutRecord($o_file);
						
						/* update major version, if minor version > 0 */
						if ($o_file->Minor > 0) {
							$o_file->Major = $o_file->Major + 1;
							$o_file->Minor = 0;
							
							/* edit file recrod */
							$i_result = $o_file->UpdateRecord();
							
							/* evaluate result */
							if ($i_result == -1) {
								throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
							}
						}
					}
				}
				
				/* if checked in element is a file */
				if (($o_file = new \fPHP\Twigs\filesTwig)->GetRecord(array($p_s_recordUUID))) {
					/* update major version, if minor version > 0 */
					if ($o_file->Minor > 0) {
						$o_file->Major = $o_file->Major + 1;
						$o_file->Minor = 0;
						
						/* edit file recrod */
						$i_result = $o_file->UpdateRecord();
						
						/* evaluate result */
						if ($i_result == -1) {
							throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
						}
					}
				}
			}
		}
		
		if ($p_o_headTwig != null) {
			$this->Twig = $o_saveTwig;
		}
	}
	
	
	/**
	 * handle delete record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function DeleteRecord() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'deleteKey'), 'deleteKey' );
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'deleteSubKey'), 'deleteSubKey' );
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'deleteFileKey'), 'deleteFileKey' );
		
		if (!$o_glob->IsPost) {
			/* delete record form */
			if ( ($o_glob->Temp->Exists('deleteKey')) && ($o_glob->Temp->{'deleteKey'} != null) ) {
				/* create modal confirmation form for deleting record */
				$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig);
				$s_title = $o_glob->GetTranslation('DeleteModalTitle', 1);
				
				if (count(explode('~', $o_glob->Temp->{'deleteKey'})) == 1) {
					if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_glob->Temp->{'deleteKey'}), array('ForeignUUID'))) {
						/* check if user is the same user who has checked out the record */
						if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
							throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
						}
					}
					
					$s_description = '<b>' . $o_glob->GetTranslation('DeleteModalDescriptionOne', 1) . '</b>';
				} else {
					$s_description = \fPHP\Helper\forestStringLib::sprintf2('<b>' . $o_glob->GetTranslation('DeleteModalDescriptionMultiple', 1) . '</b>', array(count(explode('~', $o_glob->Temp->{'deleteKey'}))));
				}
				
				$o_glob->PostModalForm->CreateDeleteModalForm($this->Twig, $s_title, $s_description);
				
				$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_deleteKey';
				$o_hidden->Value = $o_glob->Temp->{'deleteKey'};
				$o_glob->PostModalForm->FormElements->Add($o_hidden);
			}
			
			/* delete sub record form */
			else if ( ($o_glob->Temp->Exists('deleteSubKey')) && ($o_glob->Temp->{'deleteSubKey'} != null) ) {
				$o_subrecord = new \fPHP\Twigs\subrecordsTwig;
				
				if (! ($o_subrecord->GetRecord(array($o_glob->Temp->{'deleteSubKey'}))) ) {
					/* check if user is the same user who has checked out the record */
					if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
						throw new forestException(0x10001401, array($o_subrecord->fphp_Table));
					}
				}
				
				if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_subrecord->HeadUUID), array('ForeignUUID'))) {
					/* check if user is the same user who has checked out the record */
					if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
						throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
					}
				}
				
				if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_subrecord->UUID), array('ForeignUUID'))) {
					/* check if user is the same user who has checked out the record */
					if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
						throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
					}
				}
				
				$o_subrecordsTwig = new \fPHP\Twigs\subrecordsTwig;
				
				/* create modal confirmation form for deleting record */
				$o_glob->PostModalForm = new \fPHP\Forms\forestForm($o_subrecordsTwig);
				$s_title = $o_glob->GetTranslation('DeleteModalTitle', 1);
				$s_description = '<b>' . $o_glob->GetTranslation('DeleteModalDescriptionLine', 1) . '</b>';
				$o_glob->PostModalForm->CreateDeleteModalForm($o_subrecordsTwig, $s_title, $s_description);
				
				$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_deleteSubKey';
				$o_hidden->Value = $o_glob->Temp->{'deleteSubKey'};
				$o_glob->PostModalForm->FormElements->Add($o_hidden);
				
				$this->StandardView = \fPHP\Branches\forestBranch::DETAIL; /* because it only makes sense if we stay in detail view, when we open modal read only form for record */
			}
			
			/* delete file form */
			else if ( ($o_glob->Temp->Exists('deleteFileKey')) && ($o_glob->Temp->{'deleteFileKey'} != null) ) {
				$o_filerecord = new \fPHP\Twigs\filesTwig;
				$o_subrecord = new \fPHP\Twigs\subrecordsTwig;
				
				if (! ($o_filerecord->GetRecord(array($o_glob->Temp->{'deleteFileKey'}))) ) {
					/* check if user is the same user who has checked out the record */
					if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
						throw new forestException(0x10001401, array($o_subrecord->fphp_Table));
					}
				}
				
				if (($o_subrecord->GetRecord(array($o_filerecord->ForeignUUID))) ) {
					if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_subrecord->HeadUUID), array('ForeignUUID'))) {
						/* check if user is the same user who has checked out the record */
						if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
							throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
						}
					}
					
					if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_subrecord->UUID), array('ForeignUUID'))) {
						/* check if user is the same user who has checked out the record */
						if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
							throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
						}
					}
				}
				
				if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_filerecord->ForeignUUID), array('ForeignUUID'))) {
					/* check if user is the same user who has checked out the record */
					if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
						throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
					}
				}
				
				if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_filerecord->UUID), array('ForeignUUID'))) {
					/* check if user is the same user who has checked out the record */
					if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
						throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
					}
				}
				
				$o_filesTwig = new \fPHP\Twigs\filesTwig;
				
				/* create modal confirmation form for deleting record */
				$o_glob->PostModalForm = new \fPHP\Forms\forestForm($o_filesTwig);
				$s_title = $o_glob->GetTranslation('DeleteModalTitle', 1);
				$s_description = '<b>' . $o_glob->GetTranslation('DeleteModalDescriptionFile', 1) . '</b>';
				$o_glob->PostModalForm->CreateDeleteModalForm($o_filesTwig, $s_title, $s_description);
				
				$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_deleteFileKey';
				$o_hidden->Value = $o_glob->Temp->{'deleteFileKey'};
				$o_glob->PostModalForm->FormElements->Add($o_hidden);
				
				$this->StandardView = \fPHP\Branches\forestBranch::DETAIL; /* because it only makes sense if we stay in detail view, when we open modal read only form for record */
			}
		} else {
			/* delete record */
			if (array_key_exists('sys_fphp_deleteKey', $_POST)) {
				$o_glob->Base->{$o_glob->ActiveBase}->ManualTransaction();
				
				$a_deleteKeys = explode('~', $_POST['sys_fphp_deleteKey']);
				
				foreach ($a_deleteKeys as $s_deleteKey) {
					if (! ($this->Twig->GetRecord(array($s_deleteKey))) ) {
						throw new forestException(0x10001401, array($this->Twig->fphp_Table));
					}
					
					if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($this->Twig->UUID), array('ForeignUUID'))) {
						/* check if user is the same user who has checked out the record */
						if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
							continue;
						}
					}
					
					/* check record relations before deletion */
					$this->CheckandCleanupRecordBeforeDeletion($this->Twig);
					
					if (method_exists($this, 'beforeDeleteAction')) {
						$this->beforeDeleteAction();
					}
					
					/* delete record */
					$this->executeDeleteRecord($this->Twig);
					
					$this->CreateLogEntry('record deleted', $this->Twig);
					
					if (method_exists($this, 'afterDeleteAction')) {
						$this->afterDeleteAction();
					}
				}
				
				if (count($a_deleteKeys) == 1) {
					$o_glob->SystemMessages->Add(new forestException(0x10001427));
				} else {
					$o_glob->SystemMessages->Add(new forestException(0x10001428));
				}
			}
			
			/* delete sub record */
			else if (array_key_exists('sys_fphp_deleteSubKey', $_POST)) {
				$o_subrecordsTwig = new \fPHP\Twigs\subrecordsTwig;
				
				/* query sub record with key */
				if (! ($o_subrecordsTwig->GetRecord(array($_POST['sys_fphp_deleteSubKey']))) ) {
					throw new forestException(0x10001401, array($o_subrecordsTwig->fphp_Table));
				}
				
				if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_subrecordsTwig->HeadUUID), array('ForeignUUID'))) {
					/* check if user is the same user who has checked out the record */
					if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
						throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
					}
				}
				
				if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_subrecordsTwig->UUID), array('ForeignUUID'))) {
					/* check if user is the same user who has checked out the record */
					if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
						throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
					}
				}
				
				if (method_exists($this, 'beforeDeleteSubAction')) {
					$this->beforeDeleteSubAction();
				}
				
				/* delete sub record */
				$this->executeDeleteSubRecord($o_subrecordsTwig);
				
				$this->CreateLogEntry('sub record deleted', $o_subrecordsTwig);
				
				if (method_exists($this, 'afterDeleteSubAction')) {
					$this->afterDeleteSubAction();
				}
				
				$o_glob->SystemMessages->Add(new forestException(0x10001427));
				
				$this->StandardView = \fPHP\Branches\forestBranch::DETAIL; /* because it only makes sense if we stay in detail view, when we open modal read only form for record */
			}
			
			/* delete file */
			else if (array_key_exists('sys_fphp_deleteFileKey', $_POST)) {
				$o_filesTwig = new \fPHP\Twigs\filesTwig;
				
				if (! ($o_filesTwig->GetRecord(array($_POST['sys_fphp_deleteFileKey']))) ) {
					throw new forestException(0x10001401, array($o_filesTwig->fphp_Table));
				}
				
				$o_subrecord = new \fPHP\Twigs\subrecordsTwig;
				
				if (($o_subrecord->GetRecord(array($o_filesTwig->ForeignUUID))) ) {
					if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_subrecord->HeadUUID), array('ForeignUUID'))) {
						/* check if user is the same user who has checked out the record */
						if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
							throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
						}
					}
					
					if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_subrecord->UUID), array('ForeignUUID'))) {
						/* check if user is the same user who has checked out the record */
						if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
							throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
						}
					}
				}
				
				if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_filesTwig->ForeignUUID), array('ForeignUUID'))) {
					/* check if user is the same user who has checked out the record */
					if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
						throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
					}
				}
				
				if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_filesTwig->UUID), array('ForeignUUID'))) {
					/* check if user is the same user who has checked out the record */
					if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
						throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
					}
				}
				
				if (method_exists($this, 'beforeDeleteFileAction')) {
					$this->beforeDeleteFileAction();
				}
				
				/* delete file record */
				$this->executeDeleteFileRecord($o_filesTwig, true, true);
				
				$this->CreateLogEntry('file record deleted', $o_filesTwig);
				
				if (method_exists($this, 'afterDeleteFileAction')) {
					$this->afterDeleteFileAction();
				}
				
				$o_glob->SystemMessages->Add(new forestException(0x10001427));
				
				$this->StandardView = \fPHP\Branches\forestBranch::DETAIL; /* because it only makes sense if we stay in detail view, when we open modal read only form for record */
			}
		}
		
		if (isset($this->KeepFilter)) {
			if ($this->KeepFilter->value) {
				if ($o_glob->Security->SessionData->Exists('last_filter')) {
					$o_glob->Security->SessionData->Add($o_glob->Security->SessionData->{'last_filter'}, 'filter');
				}
			}
		}
		
		$this->SetNextAction('init');
	}
	
	/**
	 * check twigfield relation to other elements
	 *
	 * @param forestTwig $p_o_twig 
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function CheckandCleanupRecordBeforeDeletion(\fPHP\Twigs\forestTwig $p_o_twig) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* look for sub records with current record as join */
		$o_subrecordsTwig = new \fPHP\Twigs\subrecordsTwig;

		$a_sqlAdditionalFilter = array(array('column' => 'JoinUUID', 'value' => $p_o_twig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$i_subRecords = $o_subrecordsTwig->GetCount(null, true, false);
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		/* if sub records exists, abort deletion */
		if ($i_subRecords > 0) {
			throw new forestException(0x1000142D);
		}
		
		$o_forestdataTwig = new \fPHP\Twigs\forestdataTwig;
		
		if (! ($o_forestdataTwig->GetRecordPrimary(array('forestLookup'), array('Name'))) ) {
			throw new forestException(0x10001401, array($o_forestdataTwig->fphp_Table));
		}
		
		$s_forestLookupUUID = $o_forestdataTwig->UUID;
		
		/* look for forestLookup tablefields */
		$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
		
		$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $p_o_twig->fphp_TableUUID, 'operator' => '<>', 'filterOperator' => 'AND'), array('column' => 'ForestDataUUID', 'value' => $s_forestLookupUUID, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$o_tablefields = $o_tablefieldTwig->GetAllRecords(true);
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		foreach ($o_tablefields->Twigs as $o_tablefield) {
			/* get json encoded settings as array */
			$s_JSONEncodedSettings = str_replace('&quot;', '"', $o_tablefield->JSONEncodedSettings);
			$a_settings = json_decode($s_JSONEncodedSettings, true);
			
			/* check if json encoded settings are valid */
			if ($a_settings == null) {
				throw new forestException(0x10001F02, array($s_JSONEncodedSettings));
			}
			
			if (array_key_exists('forestLookupDataTable', $a_settings)) {
				if ($a_settings['forestLookupDataTable'] == $p_o_twig->fphp_Table) {
					/* query all records of tablefields table */
					$o_twig = null;
					$s_fieldName = $o_tablefield->FieldName;
					
					if (issetStr($o_tablefield->SubRecordField)) {
						$o_twig = new \fPHP\Twigs\subrecordsTwig;
						$s_fieldName = $o_tablefield->SubRecordField;
					} else {
						$s_tableName = strval($o_tablefield->TableUUID);
						\fPHP\Helper\forestStringLib::RemoveTablePrefix($s_tableName);
						$s_twigName = '\\fPHP\\Twigs\\' . $s_tableName . 'Twig';
						
						$o_twig = new $s_twigName;
					}
					
					$o_records = $o_twig->GetAllRecords(true);
					
					foreach ($o_records->Twigs as $o_record) {
						/* if forestLookup column has value of current record, truncate it */
						if ($o_record->{$s_fieldName}->PrimaryValue == $p_o_twig->UUID) {
							$o_record->{$s_fieldName} = 'NULL';
							$o_record->UpdateRecord();
						}
					}
				}
			}
		}
	}
	
	/**
	 * re-usable delete record function
	 *
	 * @param forestTwig $p_o_record  record which will be deleted
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function executeDeleteRecord(\fPHP\Twigs\forestTwig $p_o_record) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* delete sub records of records */
		$o_subrecordsTwig = new \fPHP\Twigs\subrecordsTwig;

		$a_sqlAdditionalFilter = array(array('column' => 'HeadUUID', 'value' => $p_o_record->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$o_subrecords = $o_subrecordsTwig->GetAllRecords(true);
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		foreach ($o_subrecords->Twigs as $o_subrecord) {
			/* delete sub record */
			$this->executeDeleteSubRecord($o_subrecord);
		}
		
		/* delete files of record */
		$o_filesTwig = new \fPHP\Twigs\filesTwig; 
		
		$a_sqlAdditionalFilter = array(array('column' => 'ForeignUUID', 'value' => $p_o_record->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$o_files = $o_filesTwig->GetAllRecords(true);
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		foreach ($o_files->Twigs as $o_file) {
			/* delete file record */
			$this->executeDeleteFileRecord($o_file, true, true);
		}
		
		/* delete checkout of record record */
		$this->executeDeleteCheckoutRecord($p_o_record);
		
		/* delete record */
		$i_return = $p_o_record->DeleteRecord();
		
		/* evaluate the result */
		if ($i_return <= 0) {
			throw new forestException(0x10001423);
		}
	}
	
	/**
	 * re-usable delete sub record function
	 *
	 * @param subrecordsTwig $p_o_subrecord  sub record which will be deleted
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function executeDeleteSubRecord(\fPHP\Twigs\subrecordsTwig $p_o_subrecord) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* delete files of sub record */
		$o_filesTwig = new \fPHP\Twigs\filesTwig; 
		
		$a_sqlAdditionalFilter = array(array('column' => 'ForeignUUID', 'value' => $p_o_subrecord->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$o_files = $o_filesTwig->GetAllRecords(true);
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		foreach ($o_files->Twigs as $o_file) {
			/* delete file record */
			$this->executeDeleteFileRecord($o_file, true, true);
		}
		
		/* delete checkout of sub record record */
		$this->executeDeleteCheckoutRecord($p_o_subrecord);
		
		/* delete record */
		$i_return = $p_o_subrecord->DeleteRecord();
		
		/* evaluate the result */
		if ($i_return <= 0) {
			throw new forestException(0x10001423);
		}
	}
	
	/**
	 * re-usable delete file record function with history files option
	 *
	 * @param filesTwig $p_o_file  file record which will be deleted
	 * @param bool $p_b_deleteRecord  true - delete file record as well, false - only delete stored file
	 * @param bool $p_b_deleteHistoryFiles  true - check and delete all history file records as well, false - do not look for history file records
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function executeDeleteFileRecord(\fPHP\Twigs\filesTwig $p_o_file, $p_b_deleteRecord = true, $p_b_deleteHistoryFiles = false) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		if ($p_b_deleteHistoryFiles) {
			/* check if we have history files for file record */
			$o_historyFilesTwig = new \fPHP\Twigs\filesTwig;
	
			$a_sqlAdditionalFilter = array(array('column' => 'ForeignUUID', 'value' => $p_o_file->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$o_historyFiles = $o_historyFilesTwig->GetAllRecords(true);
			$o_glob->Temp->Del('SQLAdditionalFilter');
			
			foreach ($o_historyFiles->Twigs as $o_historyFile) {
				/* delete history file */
				$this->executeDeleteFileRecord($o_historyFile, true, false);
			}
		}
		
		/* delete file */
		$s_folder = substr(pathinfo($p_o_file->Name, PATHINFO_FILENAME), 6, 2);
		
		$s_path = '';

		if (count($o_glob->URL->Branches) > 0) {
			foreach($o_glob->URL->Branches as $s_value) {
				$s_path .= $s_value . '/';
			}
		}
		
		$s_path .= $o_glob->URL->Branch . '/';
		
		$s_path = './trunk/' . $s_path . 'fphp_files/' . $s_folder . '/';
		
		if (is_dir($s_path)) {
			if (file_exists($s_path . $p_o_file->Name)) {
				if (!(@unlink($s_path . $p_o_file->Name))) {
					throw new forestException(0x10001422, array($s_path . $p_o_file->Name));
				}
			}
		}
		
		if ($p_b_deleteRecord) {
			/* delete checkout of file record */
			$this->executeDeleteCheckoutRecord($p_o_file);
			
			/* delete file record */
			$i_return = $p_o_file->DeleteRecord();
				
			/* evaluate the result */
			if ($i_return <= 0) {
				throw new forestException(0x10001423);
			}
		}
	}
	
	/**
	 * re-usable delete checkout record function
	 *
	 * @param forestTwig $p_o_record  record with uuid which can be used deleting checkout record
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function executeDeleteCheckoutRecord(\fPHP\Twigs\forestTwig $p_o_record) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* delete checkout of record */
		if (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($p_o_record->UUID), array('ForeignUUID'))) {
			/* delete checkout record */
			$i_return = $o_checkoutTwig->DeleteRecord();
				
			/* evaluate the result */
			if ($i_return <= 0) {
				throw new forestException(0x10001423);
			}
		}
	}
	
	
	/**
	 * handle action to change order of records, moving one record up
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function MoveUpRecord() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'editKey'), 'editKey' );
		
		/* check if we have SortColumn set for current branch */
		if (array_key_exists($this->Twig->fphp_TableUUID, $o_glob->TablesInformation)) {
			if (issetStr($o_glob->TablesInformation[$this->Twig->fphp_TableUUID]['SortColumn']->PrimaryValue)) {
				if ( ($o_sortColumn = $o_glob->GetTablefieldsDictionaryByUUID($o_glob->TablesInformation[$this->Twig->fphp_TableUUID]['SortColumn']->PrimaryValue) ) != null) {
					/* check if parameter record exists */
					if ( ($o_glob->Temp->Exists('editKey')) && ($o_glob->Temp->{'editKey'} != null) ) {
						/* query record */
						if (! ($this->Twig->GetRecord(array($o_glob->Temp->{'editKey'}))) ) {
							throw new forestException(0x10001401, array($this->Twig->fphp_Table));
						}
						
						if ( ($this->Twig->fphp_HasUUID) && (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($this->Twig->UUID), array('ForeignUUID'))) ) {
							/* check if user is the same user who has checked out the record */
							if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
								throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
							}
						}
						
						$o_currentRecord = clone $this->Twig;
						$s_primaryField = 'UUID';
						
						if (!$o_currentRecord->fphp_HasUUID) {
							$s_primaryField = 'Id';
						}
						
						/* get all records, sort by SortColumn */
						$a_sqlAdditionalSorts = array($o_sortColumn->FieldName => true);
						
						$o_glob->Temp->Add($a_sqlAdditionalSorts, 'SQLAdditionalSorts');
						$o_records = $this->Twig->GetAllRecords(true);
						$o_glob->Temp->Del('SQLAdditionalSorts');
						
						/* iterate all records */
						if ($o_records->Twigs->Count() > 0) {
							$o_lastRecordUUID = null;
							
							foreach ($o_records->Twigs as $o_record) {
								/* get selected record */
								if ($o_record->{$s_primaryField} == $o_currentRecord->{$s_primaryField}) {
									/* check if this is the first record */
									if ($o_lastRecordUUID != null) {
										/* query record */
										$s_foo = '\\fPHP\\Twigs\\' . $this->Twig->fphp_Table . 'Twig';
										\fPHP\Helper\forestStringLib::RemoveTablePrefix($s_foo);
										$o_lastRecord = new $s_foo;
										
										if (! ($o_lastRecord->GetRecord(array($o_lastRecordUUID))) ) {
											throw new forestException(0x10001401, array($o_lastRecord->fphp_Table));
										}
										
										if ( ($o_lastRecord->fphp_HasUUID) && (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_lastRecord->UUID), array('ForeignUUID'))) ) {
											/* check if user is the same user who has checked out the record */
											if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
												throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
											}
										}
										
										/* exchange SortColumn value of current record with previous record, use value 0 as intermediate value */
										$i_oldValue = $o_currentRecord->{$o_sortColumn->FieldName};
										$i_newValue = $o_lastRecord->{$o_sortColumn->FieldName};
										
										$o_currentRecord->{$o_sortColumn->FieldName} = 0;
										$o_glob->Temp->Add(array($o_sortColumn->FieldName), 'SQLUpdateColumns');
										$o_currentRecord->UpdateRecord();
										$o_glob->Temp->Del('SQLUpdateColumns');
										
										$o_lastRecord->{$o_sortColumn->FieldName} = $i_oldValue;
										$o_glob->Temp->Add(array($o_sortColumn->FieldName), 'SQLUpdateColumns');
										$o_lastRecord->UpdateRecord();
										$o_glob->Temp->Del('SQLUpdateColumns');
										
										$o_currentRecord->{$o_sortColumn->FieldName} = $i_newValue;
										$o_glob->Temp->Add(array($o_sortColumn->FieldName), 'SQLUpdateColumns');
										$o_currentRecord->UpdateRecord();
										$o_glob->Temp->Del('SQLUpdateColumns');
										
										$this->CreateLogEntry('record moved up', $o_currentRecord);
										
										$o_glob->SystemMessages->Add(new forestException(0x1000142B));
									}
								}
								
								/* save previous record primary key in help variable */
								$o_lastRecordUUID = $o_record->{$s_primaryField};
							}
						}
					}
				}
			}
		}
		
		$this->SetNextAction('init');
	}
	
	/**
	 * handle action to change order of records, moving one record down
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function MoveDownRecord() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'editKey'), 'editKey' );
		
		/* check if we have SortColumn set for current branch */
		if (array_key_exists($this->Twig->fphp_TableUUID, $o_glob->TablesInformation)) {
			if (issetStr($o_glob->TablesInformation[$this->Twig->fphp_TableUUID]['SortColumn']->PrimaryValue)) {
				if ( ($o_sortColumn = $o_glob->GetTablefieldsDictionaryByUUID($o_glob->TablesInformation[$this->Twig->fphp_TableUUID]['SortColumn']->PrimaryValue) ) != null) {
					/* check if parameter record exists */
					if ( ($o_glob->Temp->Exists('editKey')) && ($o_glob->Temp->{'editKey'} != null) ) {
						/* query record */
						if (! ($this->Twig->GetRecord(array($o_glob->Temp->{'editKey'}))) ) {
							throw new forestException(0x10001401, array($this->Twig->fphp_Table));
						}
						
						if ( ($this->Twig->fphp_HasUUID) && (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($this->Twig->UUID), array('ForeignUUID'))) ) {
							/* check if user is the same user who has checked out the record */
							if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
								throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
							}
						}
						
						$o_currentRecord = clone $this->Twig;
						$s_primaryField = 'UUID';
						
						if (!$o_currentRecord->fphp_HasUUID) {
							$s_primaryField = 'Id';
						}
						
						/* get all records, sort by SortColumn */
						$a_sqlAdditionalSorts = array($o_sortColumn->FieldName => true);
						
						$o_glob->Temp->Add($a_sqlAdditionalSorts, 'SQLAdditionalSorts');
						$o_records = $this->Twig->GetAllRecords(true);
						$o_glob->Temp->Del('SQLAdditionalSorts');
						
						/* iterate all records */
						if ($o_records->Twigs->Count() > 0) {
							$o_nextRecordUUID = null;
							
							foreach ($o_records->Twigs as $o_key => $o_record) {
								/* save next record primary key in help variable */
								if ($o_records->Twigs->Exists($o_key + 1)) {
									$o_nextRecordUUID = $o_records->Twigs->{$o_key + 1}->{$s_primaryField};
								} else {
									$o_nextRecordUUID = null;
								}
								
								/* get selected record */
								if ($o_record->{$s_primaryField} == $o_currentRecord->{$s_primaryField}) {
									/* check if this is the last record */
									if ($o_nextRecordUUID != null) {
										/* query record */
										$s_foo = '\\fPHP\\Twigs\\' . $this->Twig->fphp_Table . 'Twig';
										\fPHP\Helper\forestStringLib::RemoveTablePrefix($s_foo);
										$o_nextRecord = new $s_foo;
										
										if (! ($o_nextRecord->GetRecord(array($o_nextRecordUUID))) ) {
											throw new forestException(0x10001401, array($o_nextRecord->fphp_Table));
										}
										
										if ( ($o_nextRecord->fphp_HasUUID) && (($o_checkoutTwig = new \fPHP\Twigs\checkoutTwig)->GetRecordPrimary(array($o_nextRecord->UUID), array('ForeignUUID'))) ) {
											/* check if user is the same user who has checked out the record */
											if ($o_glob->Security->UserUUID != $o_checkoutTwig->UserUUID) {
												throw new forestException(0x1000143A, array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID)));
											}
										}
										
										/* exchange SortColumn value of current record with next record, use value 0 as intermediate value */
										$i_oldValue = $o_currentRecord->{$o_sortColumn->FieldName};
										$i_newValue = $o_nextRecord->{$o_sortColumn->FieldName};
										
										$o_currentRecord->{$o_sortColumn->FieldName} = 0;
										$o_glob->Temp->Add(array($o_sortColumn->FieldName), 'SQLUpdateColumns');
										$o_currentRecord->UpdateRecord();
										$o_glob->Temp->Del('SQLUpdateColumns');
										
										$o_nextRecord->{$o_sortColumn->FieldName} = $i_oldValue;
										$o_glob->Temp->Add(array($o_sortColumn->FieldName), 'SQLUpdateColumns');
										$o_nextRecord->UpdateRecord();
										$o_glob->Temp->Del('SQLUpdateColumns');
										
										$o_currentRecord->{$o_sortColumn->FieldName} = $i_newValue;
										$o_glob->Temp->Add(array($o_sortColumn->FieldName), 'SQLUpdateColumns');
										$o_currentRecord->UpdateRecord();
										$o_glob->Temp->Del('SQLUpdateColumns');
										
										$this->CreateLogEntry('record moved down', $o_currentRecord);
										
										$o_glob->SystemMessages->Add(new forestException(0x1000142C));
									}
								}
							}
						}
					}
				}
			}
		}
		
		$this->SetNextAction('init');
	}
	
	
	/**
	 * handle upload actions from dropzone
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function fphp_uploadAction() {
		ob_clean();
		
		if (empty($_FILES) && empty($_POST) && isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
			echo 'ERR-4';
		} else {
			if (isset($_FILES['fphp_fileBlob'])) {
				if ($_FILES['fphp_fileBlob']['size'] == 0) {
					echo 'ERR-2';
				} else if ($_FILES['fphp_fileBlob']['size'] > 0) {
					$s_name = $_FILES['fphp_fileBlob']['name'];
					$s_extension = '.jpg';
					
					if (strpos($_FILES['fphp_fileBlob']['name'], '.') !== false) {
						$s_extension = explode('.', $_FILES['fphp_fileBlob']['name'])[1];
					}
					
					if (!empty($_POST)) {
						if (strpos($_POST['fphp_fileName'], '.') === false) {
							$s_name = $_POST['fphp_fileName'] . '.' . $s_extension;
						} else {
							$s_name = $_POST['fphp_fileName'];
						}
					}
					
					if (@move_uploaded_file($_FILES['fphp_fileBlob']['tmp_name'], './temp_files/' . $s_name)) {
						echo 'INF-1';
					} else {
						echo 'ERR-1';
					}
				}
			} else {
				echo 'ERR-3';
			}
		}
		
		exit;
	}
	
	/**
	 * handle upload delete actions from dropzone
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function fphp_upload_deleteAction() {
		ob_clean();
		
		if (empty($_POST) && isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
			echo 'ERR-3';
		} else {
			if (isset($_POST['fphp_fileName'])) {
				if (@unlink('./temp_files/' . $_POST['fphp_fileName'])) {
					echo 'INF-1';
				} else {
					echo 'ERR-1';
				}
			} else {
				echo 'ERR-2';
			}
		}
		
		exit;
	}
	
	/**
	 * function to create and render a captcha picture
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function fphp_captchaAction() {
		ob_clean();
		
		$image = imagecreatetruecolor(200, 50);
 
		imageantialias($image, true);
		 
		$colors = [];
		 
		$red = rand(125, 175);
		$green = rand(125, 175);
		$blue = rand(125, 175);
		 
		for($i = 0; $i < 5; $i++) {
			$colors[] = imagecolorallocate($image, $red - 20*$i, $green - 20*$i, $blue - 20*$i);
		}
		 
		imagefill($image, 0, 0, $colors[0]);
		 
		for($i = 0; $i < 10; $i++) {
			imagesetthickness($image, rand(2, 10));
			$line_color = $colors[rand(1, 4)];
			imagerectangle($image, rand(-10, 190), rand(-10, 10), rand(-10, 190), rand(40, 60), $line_color);
		}
		
		$black = imagecolorallocate($image, 0, 0, 0);
		$white = imagecolorallocate($image, 255, 255, 255);
		$textcolors = [$black, $white];
		 
		for($i = 0; $i < intval($_SESSION['fphp_captcha_length']); $i++) {
			$letter_space = 170/intval($_SESSION['fphp_captcha_length']);
			$initial = 15;

			imagettftext($image, 24, rand(-15, 15), $initial + $i*$letter_space, rand(25, 45), $textcolors[rand(0, 1)], realpath('./src/LiberationMono-Regular.ttf'), $_SESSION['fphp_captcha'][$i]);
		}
		 
		header('Content-type: image/png');
		imagepng($image);
		imagedestroy($image);
		exit;
	}
	
	/**
	 * function to create a thumbnail of an stored image
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function fphp_imageThumbnailAction() {
		ob_clean();
		
		$o_glob = \fPHP\Roots\forestGlobals::init();
		$a_parameters = $o_glob->URL->Parameters;
		$o_filesTwig = new \fPHP\Twigs\filesTwig;
		$b_image_found = false;
		
		if ($o_filesTwig->GetRecord(array($a_parameters['fphp_thumbnail']))) { 
			$s_folder = substr(pathinfo($o_filesTwig->Name, PATHINFO_FILENAME), 6, 2);
				
			$s_path = '';

			if (count($o_glob->URL->Branches) > 0) {
				foreach($o_glob->URL->Branches as $s_branch) {
					$s_path .= $s_branch . '/';
				}
			}
			
			$s_path .= $o_glob->URL->Branch . '/';
			
			$s_path = './trunk/' . $s_path . 'fphp_files/' . $s_folder . '/';
			
			if (is_dir($s_path)) {
				if (file_exists($s_path . $o_filesTwig->Name)) {
					$s_filepath = $s_path . $o_filesTwig->Name;
					$s_extension = strtolower(pathinfo($s_filepath, PATHINFO_EXTENSION));
					
					/* read the source image */
					$o_source_image = null;
					
					switch ($s_extension) {
						case 'jpg':
						case 'jpeg':
							$o_source_image = imagecreatefromjpeg($s_filepath);
						break;
						case 'bmp':
							$o_source_image = imagecreatefrombmp($s_filepath); /* (PHP 7 >= 7.2.0) */
						break;
						case 'gif':
							$o_source_image = imagecreatefromgif($s_filepath);
						break;
						default:
							$o_source_image = imagecreatefrompng($s_filepath);
						break;
					}
					
					if ($o_source_image != null) {
						$i_width = imagesx($o_source_image);
						$i_height = imagesy($o_source_image);
						
						if ($i_width > $i_height) {
							$i_desired_width = intval($a_parameters['fphp_thumbnail_width']);
							
							if ($i_desired_width <= 0) {
								$i_desired_width = $i_width;
							}
							
							/* find the "desired height" of this thumbnail, relative to the desired width  */
							$i_desired_height = floor($i_height * ($i_desired_width / $i_width));
						} else {
							$i_desired_height = intval($a_parameters['fphp_thumbnail_width']);
							
							if ($i_desired_height <= 0) {
								$i_desired_height = $i_height;
							}
							
							/* find the "desired_width" of this thumbnail, relative to the desired height  */
							$i_desired_width = floor($i_width * ($i_desired_height / $i_height));
						}
						
						/* create a new, "virtual" image */
						$o_virtual_image = imagecreatetruecolor($i_desired_width, $i_desired_height);
						
						/* copy source image at a resized size */
						imagecopyresampled($o_virtual_image, $o_source_image, 0, 0, 0, 0, $i_desired_width, $i_desired_height, $i_width, $i_height);
						
						/* create the physical thumbnail image to its destination */
						header('Content-type: image/' . $s_extension);
						
						switch ($s_extension) {
							case 'jpg':
							case 'jpeg':
								imagejpeg($o_virtual_image);
							break;
							case 'bmp':
								imagebmp($o_virtual_image); /* (PHP 7 >= 7.2.0) */
							break;
							case 'gif':
								imagegif($o_virtual_image);
							break;
							default:
								imagepng($o_virtual_image);
							break;
						}
						
						$b_image_found = true;
						imagedestroy($o_virtual_image);
					}
				}
			}
		}
		
		if (!$b_image_found) {
			/* read the source image */
			$o_source_image = imagecreatefrompng('./images/sys_fphp/image_not_found.png');
			
			$i_width = imagesx($o_source_image);
			$i_height = imagesy($o_source_image);
			
			if ($i_width > $i_height) {
				$i_desired_width = intval($a_parameters['fphp_thumbnail_width']);
				
				if ($i_desired_width <= 0) {
					$i_desired_width = $i_width;
				}
				
				/* find the "desired height" of this thumbnail, relative to the desired width  */
				$i_desired_height = floor($i_height * ($i_desired_width / $i_width));
			} else {
				$i_desired_height = intval($a_parameters['fphp_thumbnail_width']);
				
				if ($i_desired_height <= 0) {
					$i_desired_height = $i_height;
				}
				
				/* find the "desired_width" of this thumbnail, relative to the desired height  */
				$i_desired_width = floor($i_width * ($i_desired_height / $i_height));
			}
			
			/* create a new, "virtual" image */
			$o_virtual_image = imagecreatetruecolor($i_desired_width, $i_desired_height);
			
			/* copy source image at a resized size */
			imagecopyresampled($o_virtual_image, $o_source_image, 0, 0, 0, 0, $i_desired_width, $i_desired_height, $i_width, $i_height);
			
			/* create the physical thumbnail image to its destination */
			header('Content-type: image/png');
			imagepng($o_virtual_image);
			imagedestroy($o_virtual_image);
		}
		
		exit;
	}

	/**
	 * function to update flex settings for a record element or general container
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function fphp_updateFlexAction() {
		ob_clean();
		
		if (empty($_POST) && isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
			echo 'ERR-1';
		} else {
			if (isset($_POST['sys_fphp_flex_UUID'])) {
				$o_glob = \fPHP\Roots\forestGlobals::init();
				$o_flexTwig = new \fPHP\Twigs\flexTwig;
				
				if ($o_flexTwig->GetRecord(array($_POST['sys_fphp_flex_UUID']))) { 
					if ( (isset($_POST['sys_fphp_flex_Top'])) && (isset($_POST['sys_fphp_flex_Left'])) && (isset($_POST['sys_fphp_flex_Width'])) && (isset($_POST['sys_fphp_flex_Height'])) ) {
						$o_flexTwig->Top = intval($_POST['sys_fphp_flex_Top']);
						$o_flexTwig->Left = intval($_POST['sys_fphp_flex_Left']);
						$o_flexTwig->Width = intval($_POST['sys_fphp_flex_Width']);
						$o_flexTwig->Height = intval($_POST['sys_fphp_flex_Height']);
						
						/* edit recrod */
						$i_result = $o_flexTwig->UpdateRecord();
						
						/* evaluate result */
						if ($i_result == -1) {
							echo 'ERR-5';
						}
						
						echo 'INF-1';
					} else {
						echo 'ERR-4';
					}
				} else {
					echo 'ERR-3';
				}
			} else {
				echo 'ERR-2';
			}
		}
		
		exit;
	}
}
?>