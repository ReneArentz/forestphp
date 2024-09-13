<?php
/**
 * singleton class for temporary values and communication
 * between different classes and build-steps in the script collection
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestany.net>
 * @copyright   (c) 2024 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     1.1.0 stable
 * @link        https://forestany.net
 * @object-id   0x1 00004
 * @since       File available since Release 0.1.0 alpha
 * @deprecated  -
 *
 * @version log Version			Developer	Date		Comment
 * 				0.1.0 alpha		renea		2019-08-04	first build
 * 				0.1.1 alpha		renea		2019-08-07	added trunk, templates, form and tables functionality
 * 				0.1.2 alpha		renea		2019-08-07	added sort, filter and limit functionality
 * 				0.1.5 alpha		renea		2019-10-10	added lookup and sub-constraint dictionary
 * 				0.2.0 beta		renea		2019-10-25	added RootMenu
 * 				0.4.0 beta		renea		2019-11-14	added user dictionary and functions
 * 				0.5.0 beta		renea		2019-12-05	changed ListTables, added CheckoutInterval and Versioning to dictionary
 * 				0.6.0 beta		renea		2019-12-14	changed ListTables, added Versioning and InfoColumns to dictionary
 * 				0.7.0 beta		renea		2020-01-02	changed ListTables, added Maintenance Mode to dictionary, added Maintenance Mode to BranchTree
 * 				0.9.0 beta		renea		2020-01-28	rearranged order of global functions
 * 				1.1.0 stable	renea		2023-11-02	added action chain processing, which makes it possible to use branch templates easily
 * 				1.1.0 stable	renea		2023-11-03	relocate standard view into database
 * 				1.1.0 stable	renea		2024-08-10	changes for bootstrap 5
 */

namespace fPHP\Roots;

use \fPHP\Roots\forestString as forestString;
use \fPHP\Roots\forestList as forestList;
use \fPHP\Roots\forestNumericString as forestNumericString;
use \fPHP\Roots\forestInt as forestInt;
use \fPHP\Roots\forestFloat as forestFloat;
use \fPHP\Roots\forestBool as forestBool;
use \fPHP\Roots\forestArray as forestArray;
use \fPHP\Roots\forestObject as forestObject;
use \fPHP\Roots\forestLookup as forestLookup;
use \fPHP\Helper\forestObjectList;
use \fPHP\Roots\forestException as forestException;

class forestGlobals {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	/* static var for the singleton-instance */
	private static $o_instance;
	
	private $URL;
	private $Security;
	private $Base;
	private $ActiveBase;
	private $IsPost;
	private $FastProcessing;
	private $Trunk;
	private $Temp;
	private $BackupTemp;
	private $SystemMessages;
	private $Templates;
	private $Sorts;
	private $BackupSorts;
	private $Limit;
	private $BackupLimit;
	private $AddHiddenColumns;
	private $PostModalForm;
	private $Leaf;
	private $Navigation;
	private $RootMenu;
	private $BranchTree;
	private $Translations;
	private $Tables;
	private $TablesInformation;
	private $HeadTwig;
	private $TablesWithTablefields;
	private $TablesWithTablefieldsCached;
	private $TablefieldsDictionary;
	private $UsersDictionary;
	private $SubConstraintsDictionary;
	private $LookupResultsDictionary;
	private $StandardView;
	private $StandardViews;
	private $OriginalView;
	private $TabIndex;
	private $ProcessingActionChain;
	private $ActionChainStepNumber;
	private $ActionChainStep;
	private $ActionChain;
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * constructor of forestGlobals class
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function __construct() {
		global $b_write_url_info;
		global $b_write_security_debug;
		
		$this->URL = new forestObject(new \fPHP\Roots\forestURL($b_write_url_info), false); /* bool parameter controls rendering some url information receiving from arranged url-format on screen */
		$this->Security = new forestObject(new \fPHP\Security\forestSecurity($b_write_security_debug), false);
		$this->Base = new forestObject(new forestObjectList('forestBase'), false);
		$this->ActiveBase = new forestString;
		$this->IsPost = new forestBool(false);
		$this->FastProcessing = new forestBool(false);
		$this->Trunk = new forestObject('trunkTwig');
		$this->Temp = new forestObject(new forestObjectList('stdClass'), false);
		$this->BackupTemp = null;
		$this->SystemMessages = new forestObject(new forestObjectList('forestException'), false);
		$this->Templates = new forestObject(new forestObjectList('forestTemplates'), false);
		$this->Sorts = new forestObject(new forestObjectList('forestSort'), false);
		$this->BackupSorts = null;
		$this->Limit = new forestObject(new \fPHP\Branches\forestLimit, false);
		$this->BackupLimit = null;
		$this->AddHiddenColumns = new forestObject(new forestObjectList('stdClass'), false);
		$this->PostModalForm = new forestObject('forestForm');
		$this->Leaf = new forestString;
		$this->Navigation = new forestObject(new \fPHP\Branches\forestNavigation, false);
		$this->RootMenu = new forestString;
		$this->BranchTree = new forestArray;
		$this->Translations = new forestArray;
		$this->Tables = new forestArray;
		$this->TablesInformation = new forestArray;
		$this->HeadTwig = new forestObject('forestTwig');
		$this->TablesWithTablefields = new forestArray;
		$this->TablesWithTablefieldsCached = new forestArray;
		$this->TablefieldsDictionary = new forestObject(new forestObjectList('forestTableFieldProperties'), false);
		$this->UsersDictionary = new forestArray;
		$this->SubConstraintsDictionary = new forestArray;
		$this->LookupResultsDictionary = new forestObject(new forestObjectList('stdClass'), false);
		$this->StandardView = new \fPHP\Roots\forestLookup(new \fPHP\Helper\forestLookupData('sys_fphp_standardviews', array('UUID'), array('Name')));
		$this->StandardViews = new forestArray;
		$this->OriginalView = new forestString;
		$this->TabIndex = new forestInt(100);
		$this->ProcessingActionChain = new forestBool(false);
		$this->ActionChainStepNumber = new forestInt;
		$this->ActionChainStep = new forestArray;
		$this->ActionChain = new forestArray;
	}
	
	/**
	 * method to create singleton object
	 *
	 * @return null
	 *
	 * @access public
	 * @static yes
	 */
	public static function init() {
        if (!isset(self::$o_instance)) {
            $s_selfclass = __CLASS__;
            self::$o_instance = new $s_selfclass();
        }
		
		return self::$o_instance;
    }
		
	/**
	 * prevent cloning singleton object
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __clone() {
		/* cloning forestGlobals is not allowed */
        throw new forestException('Cannot clone forestGlobals');
	}
	
	/**
	 * get forestSort object stored in global list by key
	 *
	 * @param string $p_s_key  index of forestSort object
	 *
	 * @return forestSort
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function GetSort($p_s_key) {
		/* get single sort element by key */
		if ($this->Sorts->value->Exists($p_s_key)) {
			return $this->Sorts->value->{$p_s_key};
		} else {
			$foo = new \fPHP\Branches\forestSort($p_s_key, false);
			$foo->Temp = true;
			return $foo;
		}
	}
	
	/**
	 * backup forestSort object list
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function BackupSorts() {
		$this->BackupSorts = $this->Sorts->value;
		$this->Sorts = new forestObject(new forestObjectList('forestSort'), false);
	}
	
	/**
	 * restore forestSort object list
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function RestoreSorts() {
		$this->Sorts->value = $this->BackupSorts;
	}
	
	/**
	 * backup forestLimit object list
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function BackupLimit() {
		$this->BackupLimit = $this->Limit->value;
		$this->Limit = new forestObject(new \fPHP\Branches\forestLimit, false);
	}
	
	/**
	 * restore forestLimit object list
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function RestoreLimit() {
		$this->Limit->value = $this->BackupLimit;
	}
	
	/**
	 * backup stdclass object list
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function BackupTemp() {
		$this->BackupTemp = $this->Temp->value;
		$this->Temp = new forestObject(new forestObjectList('stdClass'), false);
	}
	
	/**
	 * restore stdclass object list
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function RestoreTemp() {
		$this->Temp->value = $this->BackupTemp;
	}
	
	/**
	 * return tab index integer value for form elements and increase the value right after this call
	 *
	 * @return integer
	 *
	 * @access public
	 * @static no
	 */
	public function GetTabIndex() {
		return $this->TabIndex->value++;
	}
	
	/**
	 * build a tree of the branch-structure which is often used by functions and navigation processes
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function BuildBranchTree() {
		global $b_write_debug_globals;
		
		/* get all actions */
		$o_actionTwig = new \fPHP\Twigs\actionTwig;
		$o_actions = $o_actionTwig->GetAllRecords(true);
		
		/* get all branches */
		$o_branchTwig = new \fPHP\Twigs\branchTwig;
		$o_branches = $o_branchTwig->GetAllRecords(true);
		
		$a_branchTree = array();
		
		if ($o_branches->Twigs->Count() > 0) {
			foreach ($o_branches->Twigs as $o_branch) {
				/* insert all branch information */
				$a_branchTree['Id'][$o_branch->Id]['Id'] = $o_branch->Id;
				$a_branchTree['Id'][$o_branch->Id]['Name'] = $o_branch->Name;
				$a_branchTree['Id'][$o_branch->Id]['ParentBranch'] = $o_branch->ParentBranch;
				$a_branchTree['Id'][$o_branch->Id]['Title'] = $o_branch->Title;
				$a_branchTree['Id'][$o_branch->Id]['Navigation'] = $o_branch->Navigation;
				$a_branchTree['Id'][$o_branch->Id]['NavigationOrder'] = $o_branch->NavigationOrder;
				$a_branchTree['Id'][$o_branch->Id]['Filename'] = $o_branch->Filename;
				$a_branchTree['Id'][$o_branch->Id]['Table'] = $o_branch->Table;
				$a_branchTree['Id'][$o_branch->Id]['StandardView'] = strval($o_branch->StandardView);
				$a_branchTree['Id'][$o_branch->Id]['Filter'] = $o_branch->Filter;
				$a_branchTree['Id'][$o_branch->Id]['KeepFilter'] = $o_branch->KeepFilter;
				$a_branchTree['Id'][$o_branch->Id]['PermissionInheritance'] = $o_branch->PermissionInheritance;
				$a_branchTree['Id'][$o_branch->Id]['MaintenanceMode'] = $o_branch->MaintenanceMode;
				$a_branchTree['Id'][$o_branch->Id]['MaintenanceModeMessage'] = $o_branch->MaintenanceModeMessage;
				
				/* get actions of each branch */
				if ($o_actions->Twigs->Count() > 0) {
					foreach ($o_actions->Twigs as $o_action) {
						if ($o_action->BranchId == 0) {
							/* insert all root actions to every branch */
							$a_branchTree['Id'][$o_branch->Id]['actions']['Id'][$o_action->Id] = $o_action->Name;
							$a_branchTree['Id'][$o_branch->Id]['actions']['Name'][$o_action->Name] = $o_action->Id;
							$a_branchTree['Zero']['actions']['Id'][$o_action->Id] = $o_action->Name;
							$a_branchTree['Zero']['actions']['Name'][$o_action->Name] = $o_action->Id;
						} else if ($o_action->BranchId == $o_branch->Id) {
							/* insert all action information in Id->Name and Name->Id relation */
							$a_branchTree['Id'][$o_branch->Id]['actions']['Id'][$o_action->Id] = $o_action->Name;
							$a_branchTree['Id'][$o_branch->Id]['actions']['Name'][$o_action->Name] = $o_action->Id;
						}
					}
				}
				
				/* name->Id relation for fast access */
				$a_branchTree['Name'][$o_branch->Name] = $o_branch->Id;
			}
		}
		
		if ($b_write_debug_globals) {
			echo '<pre>BuildBranchTree';
			print_r($a_branchTree);
			echo '</pre>';
		}
		
		$this->BranchTree->value = $a_branchTree;
	}
	
	/**
	 * this method is very important to get access to other classes which are not in the current url directory
	 * after this method has been called you can try to call a class which is in the parameter's branch url directory
	 *
	 * @param string $p_s_branch  name of branch
	 * @param string $p_s_action  name of action
	 * @param array $p_a_parameters  parameters array object
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function SetVirtualTarget($p_s_branch, $p_s_action = null, $p_a_parameters = null) {
		/* check if branch and action parameter really exists in branch tree */
		if (is_int($p_s_branch)) {
			if (!array_key_exists($p_s_branch, $this->BranchTree->value['Id'])) {
				throw new forestException('Branch[%0] could not be found with id', array($p_s_branch));
			}
			
			$this->URL->value->VirtualBranch = $this->BranchTree->value['Id'][$p_s_branch]['Name'];
			$this->URL->value->VirtualBranchId = $p_s_branch;
		} else {
			if (!array_key_exists($p_s_branch, $this->BranchTree->value['Name'])) {
				throw new forestException('Branch[%0] could not be found with name', array($p_s_branch));
			}
			
			$this->URL->value->VirtualBranch = $p_s_branch;
			$this->URL->value->VirtualBranchId = $this->BranchTree->value['Name'][$p_s_branch];
		}
		
		/* with our virtualBranchId we start a loop for all parent branches to get our necessary virtualPath */
		$i_parentBranchId = $this->URL->value->VirtualBranchId;
		
		do {
			$a_virtualBranches[] = $this->BranchTree->value['Id'][$i_parentBranchId]['Name'];
			$i_parentBranchId = $this->BranchTree->value['Id'][$i_parentBranchId]['ParentBranch'];
		} while ($i_parentBranchId != 0);
		
		$a_virtualBranches = array_reverse($a_virtualBranches);
		
		$this->URL->value->VirtualBranches = $a_virtualBranches;
		
		if (!is_null($p_s_action)) {
			if (!array_key_exists($p_s_action, $this->BranchTree->value['Id'][$this->URL->value->VirtualBranchId]['actions']['Name'])) {
				throw new forestException('Action[%0] could not be found', array($p_s_action));
			}
			
			$this->URL->value->VirtualActionId = $this->BranchTree->value['Id'][$this->URL->value->VirtualBranchId]['actions']['Name'][$p_s_action];
		}
		
		if (!is_null($p_a_parameters)) {
			foreach($p_a_parameters as $s_key => $s_value) {
				/* decode url-encoded strings like %20 etc. */
				$this->URL->value->VirtualParameters[$s_key] = rawurldecode($s_value);
			}
		}
		
		/*echo '<pre>';
		echo 'VirtualBranchId: '; var_dump($this->URL->value->VirtualBranchId);
		echo 'VirtualBranches: '; print_r($this->URL->value->VirtualBranches);
		echo 'VirtualActionId: '; var_dump($this->URL->value->VirtualActionId);
		echo 'VirtualParameters:'; print_r($this->URL->value->VirtualParameters);
		echo '</pre>';*/
	}
	
	/**
	 * load all translations from database
	 * thus it is not necessary to query always the database for each label, text, etc.
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function ListTranslations() {
		global $b_write_debug_globals;
		
		$o_translationTwig = new \fPHP\Twigs\translationTwig;
		
		/* read all translations records */
		$a_sqlAdditionalFilter = array(
			array('column' => 'LanguageCode', 'value' => $this->Trunk->value->LanguageCode, 'operator' => '=', 'filterOperator' => 'AND')/*,
			array('column' => 'BranchId', 'value' => $this->URL->value->BranchId, 'operator' => '=', 'filterOperator' => 'AND'),
			array('column' => 'BranchId', 'value' => 0, 'operator' => '=', 'filterOperator' => 'OR'),
			array('column' => 'BranchId', 'value' => 1, 'operator' => '=', 'filterOperator' => 'OR')*/
		);
		$this->Temp->value->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$o_result = $o_translationTwig->GetAllRecords(true);
		$this->Temp->value->Del('SQLAdditionalFilter');
		
		/* put translation records into global 3 dimensional array */
		if ($o_result->Twigs->Count() > 0) {
			foreach ($o_result->Twigs as $o_translation) {
				$this->Translations->value[$o_translation->BranchId][$o_translation->Name] = $o_translation->Value;
			}
		}
		
		$this->URL->value->BranchTitle = $this->GetTranslation($this->BranchTree->value['Id'][$this->URL->value->BranchId]['Title'], 1);
		
		if ($b_write_debug_globals) {
			echo '<pre>ListTranslations';
			print_r($this->Translations->value);
			echo '</pre>';
		}
	}
	
	/**
	 * get a translation by name
	 *
	 * @param string $p_s_name  key name of translation
	 * @param integer $p_i_branch_id  id of branch
	 *
	 * @return string  translation string or NO_CAPTION
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function GetTranslation($p_s_name, $p_i_branch_id = null) {
		if (is_null($p_s_name)) {
			throw new forestException('Please use name parameter for GetTranslation function.');
		}
		
		/* if not branch id is set, use standard url branch id */
		if (!is_null($p_i_branch_id)) {
			$i_branch_id = intval($p_i_branch_id);
		} else {
			$i_branch_id = $this->URL->value->BranchId;
		}
		
		/* check if translation can be found */
		if (array_key_exists($i_branch_id, $this->Translations->value)) {
			if (array_key_exists($p_s_name, $this->Translations->value[$i_branch_id])) {
				return $this->Translations->value[$i_branch_id][$p_s_name];
			}
		}
		
		/* check for root translation */
		if (array_key_exists(0, $this->Translations->value)) {
			if (array_key_exists($p_s_name, $this->Translations->value[0])) {
				return $this->Translations->value[0][$p_s_name];
			}
		}
		
		/* check for general translation */
		if (array_key_exists(1, $this->Translations->value)) {
			if (array_key_exists($p_s_name, $this->Translations->value[1])) {
				return $this->Translations->value[1][$p_s_name];
			}
		}
		
		return 'NO_CAPTION';
	}
	
	/**
	 * load all user names from database, after that user names can be called by uuid with function GetUserNameByUUID
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function ListUserNames() {
		global $b_write_debug_globals;
		
		$o_userTwig = new \fPHP\Twigs\userTwig;
		
		/* read all user records */
		$o_result = $o_userTwig->GetAllRecords(true);
		
		/* put translation records into global 3 dimensional array */
		if ($o_result->Twigs->Count() > 0) {
			foreach ($o_result->Twigs as $o_user) {
				$this->UsersDictionary->value[$o_user->UUID] = $o_user->User;
			}
		}
		
		if ($b_write_debug_globals) {
			echo '<pre>ListUserNames';
			print_r($this->UsersDictionary->value);
			echo '</pre>';
		}
	}
	
	/**
	 * fast access to a user name in global dictionary by user uuid
	 *
	 * @return string  user name or empty '-' string
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function GetUserNameByUUID($p_s_uuid) {
		if (array_key_exists($p_s_uuid, $this->UsersDictionary->value)) {
			return $this->UsersDictionary->value[$p_s_uuid];
		} else {
			return '-';
		}
	}
	
	/**
	 * load all table records from database
	 * load all table records with tablefields from database
	 * query all sub-constraints from database
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function ListTables() {
		global $b_write_debug_globals;
		
		$o_tableTwig = new \fPHP\Twigs\tableTwig;
		
		/* query all table records */
		$o_result = $o_tableTwig->GetAllRecords(true);
		
		/* put table records into array */
		if ($o_result->Twigs->Count() > 0) {
			foreach ($o_result->Twigs as $o_table) {
				$this->Tables->value[$o_table->Name] = $o_table->UUID;
				$this->TablesInformation->value[$o_table->UUID]['Name'] = $o_table->Name;
				$this->TablesInformation->value[$o_table->UUID]['Identifier'] = $o_table->Identifier;
				$this->TablesInformation->value[$o_table->UUID]['Unique'] = $o_table->Unique;
				$this->TablesInformation->value[$o_table->UUID]['SortOrder'] = $o_table->SortOrder;
				$this->TablesInformation->value[$o_table->UUID]['Interval'] = $o_table->Interval;
				$this->TablesInformation->value[$o_table->UUID]['View'] = $o_table->View;
				$this->TablesInformation->value[$o_table->UUID]['SortColumn'] = $o_table->SortColumn;
				$this->TablesInformation->value[$o_table->UUID]['InfoColumns'] = $o_table->InfoColumns;
				$this->TablesInformation->value[$o_table->UUID]['InfoColumnsView'] = $o_table->InfoColumnsView;
				$this->TablesInformation->value[$o_table->UUID]['Versioning'] = $o_table->Versioning;
				$this->TablesInformation->value[$o_table->UUID]['CheckoutInterval'] = $o_table->CheckoutInterval;
			}
		}
		
		if ($b_write_debug_globals) {
			echo '<pre>Tables';
			print_r($this->Tables->value);
			echo '</pre>';
			
			echo '<pre>TablesInformation';
			print_r($this->TablesInformation->value);
			echo '</pre>';
		}
		
		/* query tables with table fields by using distinct on table-uuid column */
		$o_querySelect = new \fPHP\Base\forestSQLQuery($this->Base->value->{$this->ActiveBase->value}->BaseGateway, \fPHP\Base\forestSQLQuery::SELECT, 'sys_fphp_tablefield');
		$o_querySelect->Query->Distinct = true;
		
			$column_A = new \fPHP\Base\forestSQLColumn($o_querySelect);
				$column_A->Column = 'TableUUID';
		
		$o_querySelect->Query->Columns->Add($column_A);
		
		$o_result = $this->Base->value->{$this->ActiveBase->value}->FetchQuery($o_querySelect, false, false);
		
		/* put table uuid into array */
		if (count($o_result) > 0) {
			for ($i = 0; $i < count($o_result); $i++) {
				$this->TablesWithTablefields->value[$i] = $o_result[$i]['TableUUID'];
			}
		}
		
		if ($b_write_debug_globals) {
			echo '<pre>TablesWithTablefields';
			print_r($this->TablesWithTablefields->value);
			echo '</pre>';
		}
		
		/* query all sub-constraints */
		$o_subconstraintTwig = new \fPHP\Twigs\subconstraintTwig;
		$o_subconstraints = $o_subconstraintTwig->GetAllRecords(true);
		
		/* put sub constraint records into array, key uuid */
		if ($o_subconstraints->Twigs->Count() > 0) {
			foreach ($o_subconstraints->Twigs as $o_subconstraint) {
				$this->SubConstraintsDictionary->value[$o_subconstraint->TableUUID][] = $o_subconstraint;
			}
		}
		
		if ($b_write_debug_globals) {
			echo '<pre>SubConstraintsDictionary';
			print_r($this->SubConstraintsDictionary->value);
			echo '</pre>';
		}
	}
	
	/**
	 * fast access to a tablefield entry in global dictionary by tablefield uuid
	 *
	 * @return forestTAbleFieldProperties  fPHP-class with many information to a tablefield
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function GetTablefieldsDictionaryByUUID($p_s_uuid) {
		$o_return = null;
		
		foreach ($this->TablefieldsDictionary->value as $o_tablefield) {
			if ($o_tablefield->UUID == $p_s_uuid) {
				$o_return = $o_tablefield;
			}
		}
		
		return $o_return;
	}
}
?>