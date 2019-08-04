<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.1.0 (0x1 00004)   | */
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
	private $Temp;
	private $BackupTemp;
	private $TwigLists;
	private $Leaf;
	private $BranchTree;
	
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
		$this->Temp = new forestObject(new forestObjectList('stdClass'), false);
		$this->BackupTemp = null;
		$this->TwigLists = new forestObject(new forestObjectList('forestTwigList'), false);
		$this->Leaf = new forestString;
		$this->BranchTree = new forestArray;
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
	
	public function BackupTemp() {
		$this->BackupTemp = $this->Temp->value;
		$this->Temp = new forestObject(new forestObjectList('stdClass'), false);
	}
	
	public function RestoreTemp() {
		$this->Temp->value = $this->BackupTemp;
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
}
?>