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
	private $UUIDGuest;
	private $UUIDUsergroup;
	private $SessionIntervalUser;
	private $SessionIntervalGuest;
	private $MaxLoginTrials;
	private $FormKey;
	private $FormKeyInterval;
	private $FormKeyMinimumInterval;
	private $HoneypotFields;
	private $MaxAmountHoneypot;
	private $NavbarAdditionalClass;
	private $NavbarAlign;
	private $NavbarBrandTitle;
	private $NavbarMaxLevel;
	private $NavbarShowLoginPart;
	private $NavbarLoginIcon;
	private $NavbarSignUpIcon;
	private $NavbarShowLogoutPart;
	private $NavbarUserIcon;
	private $NavbarLogoutIcon;
	private $IncContentUTF8Decode;
	private $IncContentUTF8Encode;
	private $OutContentUTF8Decode;
	private $OutContentUTF8Encode;
	private $TempFilesLifetime;
	private $CheckoutInterval;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->Id = new forestNumericString(1);
		$this->UUID = new forestString;
		$this->LanguageCode = new forestLookup(new forestLookupData('sys_fphp_language', array('UUID'), array('Language'), array(), ' - '));
		$this->DateTimeSqlFormat = new forestString;
		$this->DateTimeFormat = new forestString;
		$this->DateFormat = new forestString;
		$this->TimeFormat = new forestString;
		$this->CheckUniqueUUID = new forestBool;
		$this->UUIDGuest = new forestLookup(new forestLookupData('sys_fphp_user', array('UUID'), array('User'), array(), ' - '));
		$this->UUIDUsergroup = new forestLookup(new forestLookupData('sys_fphp_usergroup', array('UUID'), array('Name'), array(), ' - '));
		$this->SessionIntervalUser = new forestString;
		$this->SessionIntervalGuest = new forestString;
		$this->MaxLoginTrials = new forestInt;
		$this->FormKey = new forestBool;
		$this->FormKeyInterval = new forestString;
		$this->FormKeyMinimumInterval = new forestString;
		$this->HoneypotFields = new forestBool;
		$this->MaxAmountHoneypot = new forestInt;
		$this->NavbarAdditionalClass = new forestString;
		$this->NavbarAlign = new forestString;
		$this->NavbarBrandTitle = new forestString;
		$this->NavbarMaxLevel = new forestInt;
		$this->NavbarShowLoginPart = new forestBool;
		$this->NavbarLoginIcon = new forestString;
		$this->NavbarSignUpIcon = new forestString;
		$this->NavbarShowLogoutPart = new forestBool;
		$this->NavbarUserIcon = new forestString;
		$this->NavbarLogoutIcon = new forestString;
		$this->IncContentUTF8Decode = new forestBool;
		$this->IncContentUTF8Encode = new forestBool;
		$this->OutContentUTF8Decode = new forestBool;
		$this->OutContentUTF8Encode = new forestBool;
		$this->TempFilesLifetime = new forestString;
		$this->CheckoutInterval = new forestString;
		
		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_trunk';
		$this->fphp_Primary->value = array('Id');
		$this->fphp_Unique->value = array('UUID');
		$this->fphp_SortOrder->value->Add(true, 'Id');
		$this->fphp_Interval->value = 1;
		$this->fphp_View->value = array('LanguageCode','DateTimeSqlFormat','DateTimeFormat','DateFormat','TimeFormat','CheckUniqueUUID','UUIDGuest','UUIDUsergroup','SessionIntervalUser','SessionIntervalGuest','MaxLoginTrials','FormKey','FormKeyInterval','FormKeyMinimumInterval','HoneypotFields','MaxAmountHoneypot','NavbarAdditionalClass','NavbarAlign','NavbarBrandTitle','NavbarMaxLevel','NavbarShowLoginPart','NavbarLoginIcon','NavbarSignUpIcon','NavbarShowLogoutPart','NavbarUserIcon','NavbarLogoutIcon','IncContentUTF8Decode','IncContentUTF8Encode','OutContentUTF8Decode','OutContentUTF8Encode','TempFilesLifetime','CheckoutInterval');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>