<?php

namespace fPHP\Twigs;
use \fPHP\Roots\{forestString, forestList, forestNumericString, forestInt, forestFloat, forestBool, forestArray, forestObject, forestLookup};
use \fPHP\Helper\forestLookupData;

class sessionTwig extends forestTwig {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	private $Id;
	private $UUID;
	private $UserUUID;
	private $Timestamp;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->Id = new forestNumericString(1);
		$this->UUID = new forestString;
		$this->UserUUID = new forestLookup(new forestLookupData('sys_fphp_user', array('UUID'), array('User'), array(), ' - '));
		$this->Timestamp = new forestObject('forestDateTime');
		
		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_session';
		$this->fphp_Primary->value = array('Id');
		$this->fphp_Unique->value = array('UUID');
		$this->fphp_SortOrder->value->Add(true, 'UserUUID');
		$this->fphp_SortOrder->value->Add(false, 'Timestamp');
		$this->fphp_Interval->value = 50;
		$this->fphp_View->value = array('UserUUID','Timestamp');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>