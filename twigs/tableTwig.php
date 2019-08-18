<?php
class tableTwig extends forestTwig {
	use forestData;
	
	/* Fields */
	
	private $Id;
	private $UUID;
	private $Name;
	private $Unique;
	private $SortOrder;
	private $Interval;
	private $View;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->Id = new forestNumericString(1);
		$this->UUID = new forestString;
		$this->Name = new forestString;
		$this->Unique = new forestString;
		$this->SortOrder = new forestString;
		$this->Interval = new forestInt;
		$this->View = new forestString;
		
		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_table';
		$this->fphp_Primary->value = array('Id');
		$this->fphp_Unique->value = array('UUID');
		$this->fphp_SortOrder->value->Add(true, 'Name');
		$this->fphp_Interval->value = 50;
		$this->fphp_View->value = array('Name');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>