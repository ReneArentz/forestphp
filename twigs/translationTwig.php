<?php
class translationTwig extends forestTwig {
	use forestData;
	
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