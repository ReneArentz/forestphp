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

class permissionTwig extends forestTwig {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	private $Id;
	private $UUID;
	private $Created;
	private $CreatedBy;
	private $Modified;
	private $ModifiedBy;
	private $Name;
	private $Branch;
	private $Action;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->Id = new forestNumericString(1);
		$this->UUID = new forestString;
		$this->Created = new forestObject('forestDateTime');
		$this->CreatedBy = new forestString;
		$this->Modified = new forestObject('forestDateTime');
		$this->ModifiedBy = new forestString;
		$this->Name = new forestString;
		$this->Branch = new forestLookup(new forestLookupData('sys_fphp_branch', array('Id'), array('Title','Name'), array(), ' - '));
		$this->Action = new forestLookup(new forestLookupData('sys_fphp_action', array('Id'), array('Name'), array(), ' - '));
		
		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_permission';
		$this->fphp_Primary->value = array('Id');
		$this->fphp_Unique->value = array('UUID','Branch;Action','Name;Branch');
		$this->fphp_SortOrder->value->Add(true, 'Branch');
		$this->fphp_SortOrder->value->Add(true, 'Name');
		$this->fphp_Interval->value = 50;
		$this->fphp_View->value = array('Name','Branch','Action');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>