<?php

namespace fPHP\Twigs;
use \fPHP\Roots\{forestString, forestList, forestNumericString, forestInt, forestFloat, forestBool, forestArray, forestObject, forestLookup};
use \fPHP\Helper\forestLookupData;

class usergroup_roleTwig extends forestTwig {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	private $usergroupUUID;
	private $roleUUID;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->usergroupUUID = new forestString;
		$this->roleUUID = new forestString;
		
		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_usergroup_role';
		$this->fphp_Primary->value = array('usergroupUUID', 'roleUUID');
		$this->fphp_Interval->value = 50;
		$this->fphp_View->value = array('usergroupUUID', 'roleUUID');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>