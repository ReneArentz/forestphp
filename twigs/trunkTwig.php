<?php
class trunkTwig extends forestTwig {
	use forestData;
	
	/* Fields */
	
	private $Id;
	private $UUID;
	private $LanguageCode;
	private $DateTimeSqlFormat;
	private $DateTimeFormat;
	private $DateFormat;
	private $TimeFormat;
	private $CheckUniqueUUID;
	private $SessionIntervalGuest;
	private $FormKey;
	private $FormKeyInterval;
	private $FormKeyMinimumInterval;
	private $NavbarAdditionalClass;
	private $NavbarAlign;
	private $NavbarBrandTitle;
	private $NavbarMaxLevel;
	private $IncContentUTF8Decode;
	private $IncContentUTF8Encode;
	private $OutContentUTF8Decode;
	private $OutContentUTF8Encode;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->Id = new forestNumericString(1);
		$this->UUID = new forestString;
		$this->LanguageCode = new forestString;
		$this->DateTimeSqlFormat = new forestString;
		$this->DateTimeFormat = new forestString;
		$this->DateFormat = new forestString;
		$this->TimeFormat = new forestString;
		$this->CheckUniqueUUID = new forestBool;
		$this->SessionIntervalGuest = new forestString;
		$this->FormKey = new forestBool;
		$this->FormKeyInterval = new forestString;
		$this->FormKeyMinimumInterval = new forestString;
		$this->NavbarAdditionalClass = new forestString;
		$this->NavbarAlign = new forestString;
		$this->NavbarBrandTitle = new forestString;
		$this->NavbarMaxLevel = new forestInt;
		$this->IncContentUTF8Decode = new forestBool;
		$this->IncContentUTF8Encode = new forestBool;
		$this->OutContentUTF8Decode = new forestBool;
		$this->OutContentUTF8Encode = new forestBool;
		
		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_trunk';
		$this->fphp_Primary->value = array('Id');
		$this->fphp_Unique->value = array('UUID');
		$this->fphp_SortOrder->value->Add(true, 'Id');
		$this->fphp_Interval->value = 1;
		$this->fphp_View->value = array('LanguageCode','DateTimeSqlFormat','DateTimeFormat','DateFormat','TimeFormat','CheckUniqueUUID','SessionIntervalGuest','FormKey','FormKeyInterval','FormKeyMinimumInterval','NavbarAdditionalClass','NavbarAlign','NavbarBrandTitle','NavbarMaxLevel','IncContentUTF8Decode','IncContentUTF8Encode','OutContentUTF8Decode','OutContentUTF8Encode');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>