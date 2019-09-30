<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.1.4 (0x1 00004)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * singleton class for temporary values and communication
 * between different classes and build-steps in the script collection
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.0 alpha	renatus		2019-08-04	first build
 * 0.1.1 alpha	renatus		2019-08-07	added trunk, templates, form and tables functionality
 * 0.1.2 alpha	renatus		2019-08-07	added sort, filter and limit functionality
 */

class forestGlobals {
	use forestData;
	
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
	private $TwigLists;
	private $Templates;
	private $Sorts;
	private $BackupSorts;
	private $Limit;
	private $BackupLimit;
	private $AddHiddenColumns;
	private $ActionForm;
	private $FilterForm;
	private $PostModalForm;
	private $Leaf;
	private $Navigation;
	private $BranchTree;
	private $Translations;
	private $Tables;
	private $TablesInformation;
	private $HeadTwig;
	private $TablesWithTablefields;
	private $TablesWithTablefieldsCached;
	private $TablefieldsDictionary;
	private $OriginalView;
	private $TabIndex;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct() {
		global $b_write_url_info;
		global $b_write_security_debug;
		
		$this->URL = new forestObject(new forestURL($b_write_url_info), false); // bool parameter controls rendering some url information receiving from arranged url-format on screen
		$this->Security = new forestObject(new forestSecurity($b_write_security_debug), false);
		$this->Base = new forestObject(new forestObjectList('forestBase'), false);
		$this->ActiveBase = new forestString;
		$this->IsPost = new forestBool(false);
		$this->FastProcessing = new forestBool(false);
		$this->Trunk = new forestObject('trunkTwig');
		$this->Temp = new forestObject(new forestObjectList('stdClass'), false);
		$this->BackupTemp = null;
		$this->SystemMessages = new forestObject(new forestObjectList('forestException'), false);
		$this->TwigLists = new forestObject(new forestObjectList('forestTwigList'), false);
		$this->Templates = new forestObject(new forestObjectList('forestTemplates'), false);
		$this->Sorts = new forestObject(new forestObjectList('forestSort'), false);
		$this->BackupSorts = null;
		$this->Limit = new forestObject(new forestLimit, false);
		$this->BackupLimit = null;
		$this->AddHiddenColumns = new forestObject(new forestObjectList('stdClass'), false);
		$this->ActionForm = new forestObject('forestForm');
		$this->FilterForm = new forestObject('forestForm');
		$this->PostModalForm = new forestObject('forestForm');
		$this->Leaf = new forestString;
		$this->Navigation = new forestObject(new forestNavigation, false);
		$this->BranchTree = new forestArray;
		$this->Translations = new forestArray;
		$this->Tables = new forestArray;
		$this->TablesInformation = new forestArray;
		$this->HeadTwig = new forestObject('forestTwig');
		$this->TablesWithTablefields = new forestArray;
		$this->TablesWithTablefieldsCached = new forestArray;
		$this->TablefieldsDictionary = new forestObject(new forestObjectList('forestTableFieldProperties'), false);
		$this->OriginalView = new forestString;
		$this->TabIndex = new forestInt(100);
	}
	
	/* method to create singleton object */
	public static function init() {
        if (!isset(self::$o_instance)) {
            $s_selfclass = __CLASS__;
            self::$o_instance = new $s_selfclass();
        }
		
		return self::$o_instance;
    }
		
	function __clone() {
		/* cloning forestGlobals is not allowed */
        throw new forestException('Cannot clone forestGlobals');
	}
	
	public function GetSort($p_s_key) {
		/* get single sort element by key */
		if ($this->Sorts->value->Exists($p_s_key)) {
			return $this->Sorts->value->{$p_s_key};
		} else {
			$foo = new forestSort($p_s_key, false);
			$foo->Temp = true;
			return $foo;
		}
	}
	
	public function BackupSorts() {
		$this->BackupSorts = $this->Sorts->value;
		$this->Sorts = new forestObject(new forestObjectList('forestSort'), false);
	}
	
	public function RestoreSorts() {
		$this->Sorts->value = $this->BackupSorts;
	}
	
	public function BackupLimit() {
		$this->BackupLimit = $this->Limit->value;
		$this->Limit = new forestObject(new forestLimit, false);
	}
	
	public function RestoreLimit() {
		$this->Limit->value = $this->BackupLimit;
	}
	
	public function BackupTemp() {
		$this->BackupTemp = $this->Temp->value;
		$this->Temp = new forestObject(new forestObjectList('stdClass'), false);
	}
	
	public function RestoreTemp() {
		$this->Temp->value = $this->BackupTemp;
	}
	
	public function GetTabIndex() {
		return $this->TabIndex->value++;
	}
	
	/* build a tree of the branch-structure which is often used by functions and navigation processes */
	public function BuildBranchTree() {
		/* get all actions */
		$o_actionTwig = new actionTwig;
		$o_actions = $o_actionTwig->GetAllRecords(true);
		
		/* get all branches */
		$o_branchTwig = new branchTwig;
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
		
		/*echo '<pre>';
		print_r($a_branchTree);
		echo '</pre>';*/
		
		$this->BranchTree->value = $a_branchTree;
	}
	
	/* this method is very important to get access to other classes which are not in the current url directory */
	public function SetVirtualTarget($p_s_branch, $p_s_action = null, $p_a_parameters = null) {
		/* check if branch and action parameter really exists in branch tree */
		if (is_int($p_s_branch)) {
			if (!array_key_exists($p_s_branch, $this->BranchTree->value['Id'])) {
				throw new forestException('Branch[%0] could not be found', array($p_s_branch));
			}
			
			$this->URL->value->VirtualBranch = $this->BranchTree->value['Id'][$p_s_branch]['Name'];
			$this->URL->value->VirtualBranchId = $p_s_branch;
		} else {
			if (!array_key_exists($p_s_branch, $this->BranchTree->value['Name'])) {
				throw new forestException('Branch[%0] could not be found', array($p_s_branch));
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
				$this->URL->value->VirtualParameters[$s_key] = rawurldecode($s_value); //decode url-encoded strings like %20 etc.
			}
		}
		
		/*echo '<pre>';
		echo 'VirtualBranchId: '; var_dump($this->URL->value->VirtualBranchId);
		echo 'VirtualBranches: '; print_r($this->URL->value->VirtualBranches);
		echo 'VirtualActionId: '; var_dump($this->URL->value->VirtualActionId);
		echo 'VirtualParameters:'; print_r($this->URL->value->VirtualParameters);
		echo '</pre>';*/
	}
	
	/* load all translations from database */
	public function ListTranslations() {
		$o_translationTwig = new translationTwig;
		
		/* read all translations records */
		$a_sqlAdditionalFilter = array(array('column' => 'LanguageCode', 'value' => $this->Trunk->value->LanguageCode, 'operator' => '=', 'filterOperator' => 'AND'));
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
		
		/*echo '<pre>';
		print_r($this->Translations->value);
		echo '</pre>';*/
	}
	
	/* get a translation by name */
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
	
	/* load all table records from database */
	public function ListTables() {
		$o_tableTwig = new tableTwig;
		
		/* query all table records */
		$o_result = $o_tableTwig->GetAllRecords(true);
		
		/* put table records into array */
		if ($o_result->Twigs->Count() > 0) {
			foreach ($o_result->Twigs as $o_table) {
				$this->Tables->value[$o_table->Name] = $o_table->UUID;
				$this->TablesInformation->value[$o_table->UUID]['Name'] = $o_table->Name;
				$this->TablesInformation->value[$o_table->UUID]['Unique'] = $o_table->Unique;
				$this->TablesInformation->value[$o_table->UUID]['SortOrder'] = $o_table->SortOrder;
				$this->TablesInformation->value[$o_table->UUID]['Interval'] = $o_table->Interval;
				$this->TablesInformation->value[$o_table->UUID]['View'] = $o_table->View;
			}
		}
		
		/*echo '<pre>';
		print_r($this->Tables->value);
		echo '</pre>';*/
		
		/*echo '<pre>';
		print_r($this->TablesInformation->value);
		echo '</pre>';*/
		
		/* query tables with table fields by using distinct on table-uuid column */
		$o_querySelect = new forestSQLQuery($this->Base->value->{$this->ActiveBase->value}->BaseGateway, forestSQLQuery::SELECT, 'sys_fphp_tablefield');
		$o_querySelect->Query->Distinct = true;
		
			$column_A = new forestSQLColumn($o_querySelect);
				$column_A->Column = 'TableUUID';
		
		$o_querySelect->Query->Columns->Add($column_A);
		
		$o_result = $this->Base->value->{$this->ActiveBase->value}->FetchQuery($o_querySelect, false, false);
		
		/* put table uuid into array */
		if (count($o_result) > 0) {
			for ($i = 0; $i < count($o_result); $i++) {
				$this->TablesWithTablefields->value[$i] = $o_result[$i]['TableUUID'];
			}
		}
		
		/*echo '<pre>';
		print_r($this->TablesWithTablefields->value);
		echo '</pre>';*/
	}
	
	/* fast access to a tablefield entry in global dictionary by tablefield uuid */
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