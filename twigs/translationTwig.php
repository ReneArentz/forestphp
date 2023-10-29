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

class translationTwig extends forestTwig {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	private $Id;
	private $UUID;
	private $BranchId;
	private $LanguageCode;
	private $Name;
	private $Value;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->Id = new forestNumericString(1);
		$this->UUID = new forestString;
		$this->BranchId = new forestInt;
		$this->LanguageCode = new forestLookup(new forestLookupData('sys_fphp_language', array('UUID'), array('Language'), array(), ' - '));
		$this->Name = new forestString;
		$this->Value = new forestString;
		
		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_translation';
		$this->fphp_Primary->value = array('Id');
		$this->fphp_Unique->value = array('UUID','BranchId;LanguageCode;Name');
		$this->fphp_SortOrder->value->Add(true, 'BranchId');
		$this->fphp_SortOrder->value->Add(true, 'LanguageCode');
		$this->fphp_SortOrder->value->Add(true, 'Name');
		$this->fphp_Interval->value = 50;
		$this->fphp_View->value = array('LanguageCode','Name','Value');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>