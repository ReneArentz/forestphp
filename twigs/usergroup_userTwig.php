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

class usergroup_userTwig extends forestTwig {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	private $usergroupUUID;
	private $userUUID;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->usergroupUUID = new forestString;
		$this->userUUID = new forestString;
		
		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_usergroup_user';
		$this->fphp_Primary->value = array('usergroupUUID', 'userUUID');
		$this->fphp_Interval->value = 50;
		$this->fphp_View->value = array('usergroupUUID', 'userUUID');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>