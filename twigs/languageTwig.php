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

class languageTwig extends forestTwig {
	use \fPHP\Roots\forestData;

	/* Fields */
	
	private $Id;
	private $UUID;
	private $Code;
	private $Language;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->Id = new forestNumericString(1);
		$this->UUID = new forestString;
		$this->Code = new forestString;
		$this->Language = new forestString;
		
		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_language';
		$this->fphp_Primary->value = array('Id');
		$this->fphp_Unique->value = array('UUID', 'Code;Language');
		$this->fphp_SortOrder->value->Add(true, 'Code');
		$this->fphp_Interval->value = 50;
		$this->fphp_View->value = array('Code', 'Language');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>