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

class tablefield_validationruleTwig extends forestTwig {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	private $Id;
	private $UUID;
	private $TablefieldUUID;
	private $ValidationruleUUID;
	private $ValidationRuleParam01;
	private $ValidationRuleParam02;
	private $ValidationRuleRequired;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->Id = new forestNumericString(1);
		$this->UUID = new forestString;
		$this->TablefieldUUID = new forestString;
		$this->ValidationruleUUID = new forestLookup(new forestLookupData('sys_fphp_validationrule', array('UUID'), array('Name')));
		$this->ValidationRuleParam01 = new forestString;
		$this->ValidationRuleParam02 = new forestString;
		$this->ValidationRuleRequired = new forestBool;
		
		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_tablefield_validationrule';
		$this->fphp_Primary->value = array('Id');
		$this->fphp_Unique->value = array('UUID','TablefieldUUID;ValidationruleUUID');
		//$this->fphp_SortOrder->value->Add(true, 'TablefieldUUID');
		$this->fphp_Interval->value = 50;
		$this->fphp_View->value = array('ValidationruleUUID', 'ValidationRuleParam01', 'ValidationRuleParam02', 'ValidationRuleRequired');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>