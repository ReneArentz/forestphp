<?php
class subconstraintTwig extends forestTwig {
	use forestData;
	
	/* Fields */
	
	private $Id;
	private $UUID;
	private $TableUUID;
	private $SubTableUUID;
	private $View;
	private $IdentifierStart;
	private $IdentifierIncrement;
	private $Order;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->Id = new forestNumericString(1);
		$this->UUID = new forestString;
		$this->TableUUID = new forestString;
		$this->SubTableUUID = new forestLookup(new forestLookupData('sys_fphp_table', array('UUID'), array('Name')));
		$this->View = new forestLookup(new forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => 'foo')));
		$this->IdentifierStart = new forestString;
		$this->IdentifierIncrement = new forestInt;
		$this->Order = new forestInt(1);

		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_subconstraint';
		$this->fphp_Primary->value = array('Id');
		$this->fphp_Unique->value = array('UUID', 'TableUUID;SubTableUUID');
		$this->fphp_SortOrder->value->Add(true, 'Order');
		$this->fphp_Interval->value = 50;
		$this->fphp_View->value = array('TableUUID', 'SubTableUUID', 'View', 'IdentifierStart', 'IdentifierIncrement');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>