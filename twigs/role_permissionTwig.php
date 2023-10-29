<?php

namespace fPHP\Twigs;
use \fPHP\Roots\forestString as forestString;
use \fPHP\Roots\forestList as forestList;
use \fPHP\Roots\forestNumericString as forestNumericString;
use \fPHP\Roots\forestInt as forestInt;
use \fPHP\Roots\forestFloat as forestFloat;
use \fPHP\Roots\forestBool as forestBool;
use \fPHP\Roots\forestArray as forestArray;
use \fPHP\Roots\forestObject as forestObject;
use \fPHP\Roots\forestLookup as forestLookup;
use \fPHP\Helper\forestLookupData;

class role_permissionTwig extends forestTwig {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	private $roleUUID;
	private $permissionUUID;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->roleUUID = new forestString;
		$this->permissionUUID = new forestString;
		
		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_role_permission';
		$this->fphp_Primary->value = array('roleUUID', 'permissionUUID');
		$this->fphp_Interval->value = 50;
		$this->fphp_View->value = array('roleUUID', 'permissionUUID');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>