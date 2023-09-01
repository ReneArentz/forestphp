<?php

namespace fPHP\Twigs;
use \fPHP\Roots\{forestString, forestList, forestNumericString, forestInt, forestFloat, forestBool, forestArray, forestObject, forestLookup};
use \fPHP\Helper\forestLookupData;

class formkeyTwig extends forestTwig {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	private $Id;
	private $UUID;
	private $SessionUUID;
	private $Timestamp;
	private $BranchId;
	private $ActionId;
	private $FormId;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->Id = new forestNumericString(1);
		$this->UUID = new forestString;
		$this->SessionUUID = new forestString;
		$this->Timestamp = new forestObject('forestDateTime');
		$this->BranchId = new forestInt;
		$this->ActionId = new forestInt;
		$this->FormId = new forestString;
		
		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_formkey';
		$this->fphp_Primary->value = array('Id');
		$this->fphp_Unique->value = array('UUID');
		$this->fphp_SortOrder->value->Add(true, 'Id');
		$this->fphp_Interval->value = 50;
		$this->fphp_View->value = array('Id');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>