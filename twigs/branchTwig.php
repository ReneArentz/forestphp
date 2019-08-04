<?php
class branchTwig extends forestTwig {
	use forestData;
	
	/* Fields */
	
	private $Id;
	private $Name;
	private $ParentBranch;
	private $Title;
	private $Navigation;
	private $NavigationOrder;
	private $Filename;
	private $Table;
	private $StandardView;
	private $Filter;
	private $KeepFilter;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->Id = new forestNumericString(1);
		$this->Name = new forestString;
		$this->ParentBranch = new forestInt;
		$this->Title = new forestString;
		$this->Navigation = new forestBool;
		$this->NavigationOrder = new forestInt(1);
		$this->Filename = new forestString;
		$this->Table = new forestString;
		$this->StandardView = new forestInt;
		$this->Filter = new forestBool;
		$this->KeepFilter = new forestBool;
		
		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_branch';
		$this->fphp_Primary->value = array('Id');
		$this->fphp_Unique->value = array('Name;ParentBranch;NavigationOrder');
		$this->fphp_SortOrder->value->Add(true, 'ParentBranch');
		$this->fphp_SortOrder->value->Add(true, 'NavigationOrder');
		$this->fphp_Interval->value = 50;
		$this->fphp_View->value = array('Name', 'Title', 'Navigation', 'NavigationOrder', 'Table', 'StandardView', 'Filter', 'KeepFilter');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>