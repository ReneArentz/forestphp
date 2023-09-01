<?php

namespace fPHP\Twigs;
use \fPHP\Roots\{forestString, forestList, forestNumericString, forestInt, forestFloat, forestBool, forestArray, forestObject, forestLookup};
use \fPHP\Helper\forestLookupData;

class tablefieldTwig extends forestTwig {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	private $Id;
	private $UUID;
	private $TableUUID;
	private $FieldName;
	private $FormElementUUID;
	private $SqlTypeUUID;
	private $ForestDataUUID;
	private $TabId;
	private $JSONEncodedSettings;
	private $FooterElement;
	private $SubRecordField;
	private $Order;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->Id = new forestNumericString(1);
		$this->UUID = new forestString;
		$this->TableUUID = new forestLookup(new forestLookupData('sys_fphp_table', array('UUID'), array('Name')));
		$this->FieldName = new forestString;
		$this->FormElementUUID = new forestLookup(new forestLookupData('sys_fphp_formelement', array('UUID'), array('Name')));
		$this->SqlTypeUUID = new forestLookup(new forestLookupData('sys_fphp_sqltype', array('UUID'), array('Name')));
		$this->ForestDataUUID = new forestLookup(new forestLookupData('sys_fphp_forestdata', array('UUID'), array('Name')));
		$this->TabId = new forestString;
		$this->JSONEncodedSettings = new forestString;
		$this->FooterElement = new forestBool;
		$this->SubRecordField = new forestString;
		$this->Order = new forestInt;
		
		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_tablefield';
		$this->fphp_Primary->value = array('Id');
		$this->fphp_Unique->value = array('UUID', 'TableUUID;FieldName');
		$this->fphp_SortOrder->value->Add(true, 'TableUUID');
		$this->fphp_SortOrder->value->Add(true, 'Order');
		$this->fphp_Interval->value = 50;
		$this->fphp_View->value = array('FieldName', 'FormElementUUID', 'SqlTypeUUID', 'ForestDataUUID', 'TabId', 'JSONEncodedSettings', 'FooterElement', 'SubRecordField', 'Order');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>