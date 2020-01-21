<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.7.0 (0x1 0000E)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * class to capsulate data about table fields
 * storing these objects in a global dictionary will reduce database record access for one session
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.1 alpha	renatus		2019-08-15	added to framework
 */

class forestTableFieldProperties {
	use forestData;
	
	/* Fields */
	
	private $UUID;
	private $TableUUID;
	private $FieldName;
	private $TabId;
	private $JSONEncodedSettings;
	private $FooterElement;
	private $SubRecordField;
	private $Order;
	
	private $FormElementUUID;
	private $FormElementName;
	private $FormElementJSONEncodedSettings;
	
	private $SqlTypeUUID;
	private $SqlTypeName;
	
	private $ForestDataUUID;
	private $ForestDataName;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct(
		$p_s_uuid,
		$p_s_tableUUID,
		$p_s_fieldName,
		$p_s_tabId,
		$p_s_json,
		$p_b_footerElement,
		$p_s_subRecordField,
		$p_i_order,
		$p_s_formUUID,
		$p_s_formName,
		$p_s_formJSON,
		$p_s_sqlUUID,
		$p_s_sqlName,
		$p_s_fdataUUID,
		$p_s_fdataName
	) {
		$this->UUID = new forestString($p_s_uuid);
		$this->TableUUID = new forestString($p_s_tableUUID);
		$this->FieldName = new forestString($p_s_fieldName);
		$this->TabId = new forestString($p_s_tabId);
		$this->JSONEncodedSettings = new forestString($p_s_json);
		$this->FooterElement = new forestBool($p_b_footerElement);
		$this->SubRecordField = new forestString($p_s_subRecordField);
		$this->Order = new forestInt($p_i_order);
		
		$this->FormElementUUID = new forestString($p_s_formUUID);
		$this->FormElementName = new forestString($p_s_formName);
		$this->FormElementJSONEncodedSettings = new forestString($p_s_formJSON);
		
		$this->SqlTypeUUID = new forestString($p_s_sqlUUID);
		$this->SqlTypeName = new forestString($p_s_sqlName);
		
		$this->ForestDataUUID = new forestString($p_s_fdataUUID);
		$this->ForestDataName = new forestString($p_s_fdataName);
	}
}
?>