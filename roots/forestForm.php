<?php
/**
 * class-collection for all necessary form data and settings
 * multiple tabs are possible for rendering, also modal form view is supported
 * most settings are based on json encoded strings stored in configuration files or in database records
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestany.net>
 * @copyright   (c) 2024 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     1.1.0 stable
 * @link        https://forestany.net
 * @object-id   0x1 00015
 * @since       File available since Release 0.1.1 alpha
 * @deprecated  -
 *
 * @version log Version			Developer	Date		Comment
 * 				0.1.1 alpha		renea		2019-08-09	added to framework
 * 				0.1.3 alpha		renea		2019-09-06	added formkey and validationrules
 * 				0.1.4 alpha		renea		2019-09-23	added dropzone and richtext
 * 				0.1.5 alpha		renea		2019-10-04	added forestLookup
 * 				0.1.5 alpha		renea		2019-10-05	added forestCombination and Captcha
 * 				0.5.0 beta		renea		2019-12-02	added honeypot fields functionality
 * 				0.5.0 beta		renea		2019-12-04	added auto checkin question
 * 				0.6.0 beta		renea		2019-12-18	added info columns in readonly mode
 * 				0.7.0 beta		renea		2020-01-02	added identifier in readonly mode
 * 				0.7.0 beta		renea		2020-01-03	added money-format display
 * 				0.9.0 beta		renea		2020-01-27	added checkout message in readonly mode
 * 				0.9.0 beta		renea		2020-01-29	changes for bootstrap 4
 * 				1.0.0 stable	renea		2020-02-13	added MongoDB support by breaking up SQL-Join Queries
 * 				1.0.1 stable	renea		2021-04-10	added support for handling forestLookup table field with datalist/list form element
 * 				1.0.1 stable	renea		2021-04-11	added support for thumbnail and detailview image preview
 * 				1.1.0 stable	renea		2023-11-02	modal header close content is optional
 * 				1.1.0 stable	renea		2024-08-10	changes for bootstrap 5
 */

namespace fPHP\Forms;

use \fPHP\Roots\forestString as forestString;
use \fPHP\Roots\forestList as forestList;
use \fPHP\Roots\forestNumericString as forestNumericString;
use \fPHP\Roots\forestInt as forestInt;
use \fPHP\Roots\forestFloat as forestFloat;
use \fPHP\Roots\forestBool as forestBool;
use \fPHP\Roots\forestArray as forestArray;
use \fPHP\Roots\forestObject as forestObject;
use \fPHP\Roots\forestLookup as forestLookup;
use \fPHP\Helper\forestObjectList;
use \fPHP\Roots\forestException as forestException;

class forestForm {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	private $Automatic;
	private $Readonly;
	private $PrintFooter;
	private $FormObject;
	private $FormElements;
	private $FormModalConfiguration;
	private $FormTabConfiguration;
	private $FormTabs;
	private $FormModalSubForm;
	private $CheckoutMessage;
	private $FormFooterElements;
	private $BeforeForm;
	private $AfterForm;
	private $BeforeFormRightAlign;
	private $AfterFormRightAlign;
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * constructor of forestForm class
	 *
	 * @param forestTwig $p_o_twig  forestTwig object to get tablefields and information to render the form for each tablefield
	 * @param bool $p_b_automatic  true - create a new forestForm object and generate form elements by twig object automtically, false - only create a new forestForm object
	 * @param bool $p_b_readonly  true - indicates that the form is in readonly mode, false - normal form
	 * @param bool $p_b_printFooter  true - indicates that the form has a footer, false - form has no footer
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __construct(\fPHP\Twigs\forestTwig $p_o_twig, $p_b_automatic = false, $p_b_readonly = false, $p_b_printFooter = true) {
		$this->Automatic = new forestBool($p_b_automatic);
		$this->Readonly = new forestBool($p_b_readonly);
		$this->PrintFooter = new forestBool($p_b_printFooter);
		$this->FormObject = new forestObject(new forestFormElement(\fPHP\Forms\forestFormElement::FORM), false);
		$this->FormElements = new forestObject(new forestObjectList('forestFormElement'), false);
		$this->FormModalConfiguration = new forestObject(new forestModalConfiguration, false);
		$this->FormTabConfiguration = new forestObject(new forestTabConfiguration, false);
		$this->FormTabs = new forestObject(new forestObjectList('forestFormTab'), false);
		$this->FormModalSubForm = new forestString;
		$this->CheckoutMessage = new forestString;
		$this->FormFooterElements = new forestObject(new forestObjectList('forestFormElement'), false);
		$this->BeforeForm = new forestString;
		$this->AfterForm = new forestString;
		$this->BeforeFormRightAlign = new forestBool(false);
		$this->AfterFormRightAlign = new forestBool(false);
		
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		if ($p_b_automatic) {
			/* get table */
			$o_tableTwig = new \fPHP\Twigs\tableTwig;
			
			/* query table record */
			if (!($o_tableTwig->GetRecordPrimary(array($p_o_twig->fphp_Table), array('Name')))) {
				throw new forestException(0x10001401, array($p_o_twig->fphp_Table));
			}
			
			/* get formobject element */
			$o_formelementTwig = new \fPHP\Twigs\formelementTwig;
			
			/* look in tablefields for formobject, if not get the standard by formelementuuid */
			if (!($o_formelementTwig->GetRecordPrimary(array(\fPHP\Forms\forestFormElement::FORM), array('Name')))) {
				throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
			}
		
			$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
			
			if (!($o_tablefieldTwig->GetRecordPrimary(array($o_tableTwig->UUID, $o_formelementTwig->UUID), array('TableUUID', 'FormElementUUID')))) {
				/* no tablefield for table, take standard */
				$s_formObjectJSONsettings = $o_formelementTwig->JSONEncodedSettings;
			} else {
				$s_formObjectJSONsettings = $o_tablefieldTwig->JSONEncodedSettings;
			}
			
			/*$s_json = '
			{
				"FormTabConfiguration": {
					"Tab" : true,
					"TabMenuClass" : "nav nav-tabs nav-justified",
					"TabLiClass" : "nav-item",
					"TabAClass" : "nav-link",
					"TabActiveClass" : "active",
					"TabToggle" : "tab",
					"TabContentClass" : "tab-content",
					"TabFooterClass" : "tab-footer",
					"TabElementClass" : "container tab-pane fade",
					"TabElementActiveClass" : "container tab-pane active",
					"TabsInfo" : [
						{"TabId" : "general", "TabTitle" : "General"}
					]
				},
				
				"FormModalConfiguration" : {
					"Modal" : true,
					"ModalClass" : "modal fade",
					"ModalId" : "myModal",
					"ModalTitle" : "Modal Form HTML Validation",
					"ModalTitleClass" : "modal-title fs-4",
					"ModalRole" : "NULL",
					"ModalDialogClass" : "modal-dialog modal-xl",
					"ModalDialogContentClass" : "modal-content",
					"ModalHeaderClass" : "modal-header bg-dark text-light text-center",
					"ModalHeaderCloseClass" : "btn-close btn-close-light",
					"ModalHeaderDismissClass" : "modal",
					"ModalHeaderCloseContent" : "NULL",
					"ModalBodyClass" : "modal-body bg-light",
					"ModalFooterClass" : "modal-footer bg-dark text-light"
				},
				
				"Class" : "form-horizontal",
				"FormGroupClass" : "form-group row mb-3 position-relative",
				"LabelClass" : "col-sm-3 col-form-label",
				"FormElementClass" : "col-sm-9",
				"ClassAll" : "form-control",
				"RadioClass" : "form-check",
				"CheckboxClass" : "form-check"
			}
			';*/
			
			/* create formobject(check $this->Readonly->value), modal object and maybe tab objects */
			$this->FormObject->value->loadJSON($s_formObjectJSONsettings);
			$this->FormObject->value->ReadonlyAll = $this->Readonly->value;
			
			/* add _readonly string to FormObject->Id, because of conflict with modal forms in detail view */
			if ($this->FormObject->value->ReadonlyAll) {
				$this->FormObject->value->Id = 'readonly_' . $this->FormObject->value->Id;
			}
			
			$this->FormModalConfiguration->value->loadJSON($s_formObjectJSONsettings);
			
			if ($this->FormModalConfiguration->value->Modal) {
				$this->FormModalConfiguration->value->ModalId = $this->FormObject->value->Id . 'Modal';
				$this->FormModalConfiguration->value->ModalTitle = $o_glob->URL->BranchTitle . ' ' . $o_glob->GetTranslation('BranchTitleRecord', 1);
			}
			
			$this->FormTabConfiguration->value->loadJSON($s_formObjectJSONsettings);
			
			/* get TabsInfo and create tab array */
			\fPHP\Forms\forestFormElement::JSONSettingsMultilanguage($s_formObjectJSONsettings);
			$a_jsonSettings = json_decode($s_formObjectJSONsettings, true);
			$a_tabsInfo = array();
			$o_firstTab = null;
			$o_lastTab = null;
			
			/* if we find FormTabConfiguration with Tab-key and TabsInfo-Key */
			if (array_key_exists('FormTabConfiguration', $a_jsonSettings)) {
				if (array_key_exists('Tab', $a_jsonSettings['FormTabConfiguration'])) {
					if ($a_jsonSettings['FormTabConfiguration']['Tab']) {
						if (array_key_exists('TabsInfo', $a_jsonSettings['FormTabConfiguration'])) {
							foreach ($a_jsonSettings['FormTabConfiguration']['TabsInfo'] as $a_tab) {
								$a_tabsInfo[$a_tab['TabId']] = new \fPHP\Forms\forestFormTab($a_tab['TabId'], $a_tab['TabTitle']);
								$o_lastTab = $a_tabsInfo[$a_tab['TabId']];
								
								if ($o_firstTab == null) {
									$o_firstTab = $a_tabsInfo[$a_tab['TabId']];
								}
							}
						}
					}
				}
			}
			
			/* get tablefields of current record */
			$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $o_tableTwig->UUID, 'operator' => '=', 'filterOperator' => 'AND'), array('column' => 'FieldName', 'value' => \fPHP\Forms\forestFormElement::FORM, 'operator' => '<>', 'filterOperator' => 'AND'));
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$o_tableFields = $o_tablefieldTwig->GetAllRecords(true);
			$o_glob->Temp->Del('SQLAdditionalFilter');
			
			/* render identifier and/or ShowInDetailView pciture - readonly */
			if ($this->FormObject->value->ReadonlyAll) {
				/* if identifier is configured */
				if (issetStr($o_glob->TablesInformation[$p_o_twig->fphp_TableUUID]['Identifier']->PrimaryValue)) {
					$o_formElement = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::TEXT);
					$o_formElement->Id = 'readonly_' . $p_o_twig->fphp_Table . 'Identifier';
					$o_formElement->Label = $o_glob->GetTranslation('sortIdentifier') . ':';
					$o_formElement->Value = $p_o_twig->{'Identifier'};
					
					if ($o_firstTab != null) {
						$o_firstTab->FormElements->Add($o_formElement);
					} else {
						$this->FormElements->value->Add($o_formElement);
					}
				}
				
				/* flag to know if we also need to render showview picture file */
				$b_showview = false;
				$s_showviewField = null;
				
				/* read all tablefields of table */
				foreach ($o_glob->TablefieldsDictionary as $o_tableFieldProperties) {
					/* iterate all tablefields matching current table uuid and form element is FILEDIALOG */
					if ( ($o_tableFieldProperties->TableUUID == $o_tableTwig->UUID) && ($o_tableFieldProperties->FormElementName == \fPHP\Forms\forestFormElement::FILEDIALOG) ) {
						/* get json encoded settings as array */
						$s_JSONEncodedSettings = str_replace('&quot;', '"', $o_tableFieldProperties->JSONEncodedSettings);
						$a_settings = json_decode($s_JSONEncodedSettings, true);
						
						/* if ONLY one tablefield has form element 'file' and json settings with 'Thumbnail' or 'ShowInDetailView' as 'true' */
						if (array_key_exists('ShowInDetailView', $a_settings)) {
							/* already one tablefield with 'ShowInDetailView' as 'true' found */
							if ($b_showview) {
								$b_showview = false;
								break;
							}
							
							/* 'ShowInDetailView' as 'true' found */
							if ( (array_key_exists('ShowInDetailView', $a_settings)) && (array_key_exists('ShowInDetailViewMaxWidth', $a_settings)) ) {
								if ($a_settings['ShowInDetailView'] == true) {
									$s_showviewField = $o_tableFieldProperties->FieldName;
									$b_showview = true;
								}
							}
						}
					}
				}
				
				/* render showview picture file */
				if ($b_showview) {
					$o_formElement = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::DESCRIPTION);
					$o_formElement->Id = 'readonly_' . $p_o_twig->fphp_Table . 'ShowInDetailViewImage';
					$o_formElement->Label = '';
					
					/* get showview picture record */
					$o_filesTwig = new \fPHP\Twigs\filesTwig;
					
					if ($o_filesTwig->GetRecord(array($p_o_twig->{$s_showviewField}))) {
						$s_folder = substr(pathinfo($o_filesTwig->Name, PATHINFO_FILENAME), 6, 2);
						
						$s_path = '';

						if (count($o_glob->URL->Branches) > 0) {
							foreach($o_glob->URL->Branches as $s_branch) {
								$s_path .= $s_branch . '/';
							}
						}
						
						$s_path .= $o_glob->URL->Branch . '/';
						
						$s_path = './trunk/' . $s_path . 'fphp_files/' . $s_folder . '/';
						
						if (is_dir($s_path)) {
							if (file_exists($s_path . 'dv_' . $o_filesTwig->Name)) {
								/* show thumbnail picture file */
								$o_formElement->Description = '<a href="' . $s_path . $o_filesTwig->Name . '" target="_blank" title="' . $o_filesTwig->DisplayName . '"><img src="' . $s_path . 'dv_' . $o_filesTwig->Name . '" alt="image could not be rendered" title="' . $o_filesTwig->DisplayName . '"></a>';
							} else {
								$o_formElement->Description = '<img src="./files/image_not_found.png" alt="image could not be rendered" title="' . $o_filesTwig->DisplayName . '">';
							}
						}
					}
					
					if ($o_firstTab != null) {
						$o_firstTab->FormElements->Add($o_formElement);
					} else {
						$this->FormElements->value->Add($o_formElement);
					}
				}
			}
			
			/* iterate each table field */
			foreach ($o_tableFields->Twigs as $o_tableField) {
				$s_forestdataName = '';
				
				/* query forestdata name, if UUID is set */
				if (issetStr($o_tableField->ForestDataUUID->PrimaryValue)) {
					$o_forestDataTwig = new \fPHP\Twigs\forestdataTwig;
					
					if (! ($o_forestDataTwig->GetRecord(array($o_tableField->ForestDataUUID->PrimaryValue))) ) {
						throw new forestException(0x10001401, array($o_forestDataTwig->fphp_Table));
					}
					
					$s_forestdataName = $o_forestDataTwig->Name;
				}
				
				/* check read only mode */
				if ($this->FormObject->value->ReadonlyAll) {
					/* skip element if we have no table field information in global dictionary, except forestCombination */
					if ( (!$o_glob->TablefieldsDictionary->Exists($p_o_twig->fphp_Table . '_' . $o_tableField->FieldName)) && ($s_forestdataName != 'forestCombination') ) {
						continue;
					}
					
					if ($s_forestdataName != 'forestCombination') {
						/* skip element if it is of type FILE PASSWORD DROPZONE, except forestCombination */
						if ( ($o_glob->TablefieldsDictionary->{$p_o_twig->fphp_Table . '_' . $o_tableField->FieldName}->FormElementName == forestFormElement::FILEDIALOG) || ($o_glob->TablefieldsDictionary->{$p_o_twig->fphp_Table . '_' . $o_tableField->FieldName}->FormElementName == forestformElement::PASSWORD) || ($o_glob->TablefieldsDictionary->{$p_o_twig->fphp_Table . '_' . $o_tableField->FieldName}->FormElementName == forestformElement::DROPZONE) ) {
							continue;
						}
					}
				} else {
					/* if not read only, skip forestCombination fields */
					if ($s_forestdataName == 'forestCombination') {
						continue;
					}
				}
				
				$s_formElementJSONSettings = '';
				
				/* look for settings of tablefield, if not found look for standard with formelementuuid */
				if (!issetStr($o_tableField->JSONEncodedSettings)) {
					if (!($o_formelementTwig->GetRecord(array($o_tableField->FormElementUUID->PrimaryValue)))) {
						continue;
					} else {
						if (!issetStr($o_formelementTwig->JSONEncodedSettings)) {
							continue;
						} else {
							$s_formElementJSONSettings = $o_formelementTwig->JSONEncodedSettings;
						}
					}
				} else {
					$s_formElementJSONSettings = $o_tableField->JSONEncodedSettings;
				}
				
				/* create formelement object */
				if (!($o_formelementTwig->GetRecord(array($o_tableField->FormElementUUID->PrimaryValue)))) {
					continue;
				} else {
					$o_formElement = new \fPHP\Forms\forestFormElement($o_formelementTwig->Name);
					$o_formElement->loadJSON($s_formElementJSONSettings);
					
					/* add _readonly string to $formElement->Id, because of conflict with modal forms in detail view */
					if ($this->FormObject->value->ReadonlyAll) {
						$o_formElement->Id = 'readonly_' . $o_formElement->Id;
						
						if ( (property_exists($o_formElement->getFormElement(), 'Placeholder')) && (issetStr($o_formElement->Placeholder)) ) { 
							$o_formElement->Placeholder = '';
						}
					}
					
					/* set form id, uploader and deleter for dropzone element */
					if ($o_formElement->getType() == forestformElement::DROPZONE) {
						$o_formElement->FormId = $this->FormObject->value->Id;
						$o_formElement->URIFileUploader = \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'fphp_upload');
						$o_formElement->URIFileDeleter = \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'fphp_upload_delete');
					}
					
					if (property_exists($p_o_twig, $o_tableField->FieldName)) {
						/* create options array for lookup field */
						if (is_object($p_o_twig->{$o_tableField->FieldName})) {
							if (is_a($p_o_twig->{$o_tableField->FieldName}, '\\fPHP\Helper\\forestLookupData')) {
								/* if we use lookup data with list form element, we use key value as key and value */
								if ($o_formElement->getType() == forestformElement::LISTTXT) {
									$a_foo = array();
									
									foreach ($p_o_twig->{$o_tableField->FieldName}->CreateOptionsArray() as $s_key => $s_value) {
										$a_foo[$s_key] = $s_key;
									}
									
									$o_formElement->Options = $a_foo;
									$o_formElement->AutoComplete = false;
								} else {
									$o_formElement->Options = $p_o_twig->{$o_tableField->FieldName}->CreateOptionsArray();
								}
							}
						}
					}
					
					/* adopt standard value of json encoded settings */
					if (issetStr($o_formElement->Value)) {
						$s_value = $o_formElement->Value;
					} else {
						$s_value = '';
					}
					
					/* get value for form element, based on parameter twig record */
					if ((!$p_o_twig->IsEmpty()) && (property_exists($p_o_twig, $o_tableField->FieldName))) {
						/* maybe other casts necessary depending on sqltype info */
						if (is_a($p_o_twig->{$o_tableField->FieldName}, '\\fPHP\Helper\\forestDateTime')) {
							if ($o_glob->TablefieldsDictionary->{$p_o_twig->fphp_Table . '_' . $o_tableField->FieldName}->FormElementName == \fPHP\Forms\forestFormElement::DATETIMELOCAL) {
								$s_value = $p_o_twig->{$o_tableField->FieldName}->ToString('Y-m-d\TH:i:s');
							} else if ($o_glob->TablefieldsDictionary->{$p_o_twig->fphp_Table . '_' . $o_tableField->FieldName}->FormElementName == \fPHP\Forms\forestFormElement::DATEINPUT) {
								$s_value = $p_o_twig->{$o_tableField->FieldName}->ToString('Y-m-d');
							} else if ($o_glob->TablefieldsDictionary->{$p_o_twig->fphp_Table . '_' . $o_tableField->FieldName}->FormElementName == \fPHP\Forms\forestFormElement::MONTH) {
								$s_value = $p_o_twig->{$o_tableField->FieldName}->ToString('Y-m');
							} else if ($o_glob->TablefieldsDictionary->{$p_o_twig->fphp_Table . '_' . $o_tableField->FieldName}->FormElementName == \fPHP\Forms\forestFormElement::TIMEINPUT) {
								$s_value = $p_o_twig->{$o_tableField->FieldName}->ToString('H:i:s');
							} else if ($o_glob->TablefieldsDictionary->{$p_o_twig->fphp_Table . '_' . $o_tableField->FieldName}->FormElementName == \fPHP\Forms\forestFormElement::WEEK) {
								$s_value = $p_o_twig->{$o_tableField->FieldName}->ToString('Y-\WW');
							} else {
								$s_value = $p_o_twig->{$o_tableField->FieldName}->ToString();
							}
						} else if (is_a($p_o_twig->{$o_tableField->FieldName}, '\\fPHP\Helper\\forestLookupData')) {
							/* if lookup data is used with form element 'list', get value with stored unique key */
							if ($o_glob->TablefieldsDictionary->{$p_o_twig->fphp_Table . '_' . $o_tableField->FieldName}->FormElementName == \fPHP\Forms\forestFormElement::LISTTXT) {
								$s_value = $p_o_twig->{$o_tableField->FieldName}->__toString();
							} else {
								$s_value = $p_o_twig->{$o_tableField->FieldName}->PrimaryValue;
							}
						} else {
							$s_value = strval($p_o_twig->{$o_tableField->FieldName});
							
							if ($this->FormObject->value->ReadonlyAll) {
								$s_JSONEncodedSettings = str_replace('&quot;', '"', $s_formElementJSONSettings);
								$a_settings = json_decode($s_JSONEncodedSettings, true);
								
								/* check if we want to render value as date interval value */
								if (array_key_exists('DateIntervalFormat', $a_settings)) {
									if ($a_settings['DateIntervalFormat']) {
										$s_value = strval(new \fPHP\Helper\forestDateInterval($s_value));
									}
								}
							}
						}
					}
					
					/* get value for forestCombination field */
					if ( (!$p_o_twig->IsEmpty()) && ($s_forestdataName == 'forestCombination') ) {
						$s_JSONEncodedSettings = str_replace('&quot;', '"', $s_formElementJSONSettings);
						$a_settings = json_decode($s_JSONEncodedSettings, true);
						
						if (array_key_exists('forestCombination', $a_settings)) {
							$s_value = $p_o_twig->CalculateCombination($a_settings['forestCombination']);
							
							/* check if we want to render value as money value */
							if (array_key_exists('MoneyFormat', $a_settings)) {
								if ($a_settings['MoneyFormat']) {
									$s_value = \fPHP\Helper\forestStringLib::money_format('%i', $s_value);
								}
							}
							/* check if we want to render value as date interval value */
							else if ( (array_key_exists('DateIntervalFormat', $a_settings)) && ($this->FormObject->value->ReadonlyAll) ) {
								if ($a_settings['DateIntervalFormat']) {
									$s_value = strval(new \fPHP\Helper\forestDateInterval($s_value));
								}
							}
						}
					}
					
					/* check for money format setting */
					if ( (!$p_o_twig->IsEmpty()) && ($s_forestdataName == 'forestFloat') && ($this->FormObject->value->ReadonlyAll) ) {
						$s_JSONEncodedSettings = str_replace('&quot;', '"', $s_formElementJSONSettings);
						$a_settings = json_decode($s_JSONEncodedSettings, true);
						
						/* check if we want to render value as money value */
						if (array_key_exists('MoneyFormat', $a_settings)) {
							if ($a_settings['MoneyFormat']) {
								$s_value = \fPHP\Helper\forestStringLib::money_format('%i', $p_o_twig->{$o_tableField->FieldName});
								
								if (floatval($p_o_twig->{$o_tableField->FieldName}) < 0.0) {
									$o_formElement->Style = 'color: red;';
								}
							}
						}
					}
					
					$o_formElement->Value = $s_value;
					$b_addTab = false;
					
					/* if TabId isset, add form element to tab, else add form element to object list */
					if (issetStr($o_tableField->TabId)) {
						if (array_key_exists($o_tableField->TabId, $a_tabsInfo)) {
							$a_tabsInfo[$o_tableField->TabId]->FormElements->Add($o_formElement);
							$b_addTab = true;
						}
					}
					
					/* if form element should not be added to tab, it can be added to footer or normal to form elements object list */
					if (!$b_addTab) {
						if ($o_tableField->FooterElement) {
							$this->FormFooterElements->value->Add($o_formElement);
						} else {
							$this->FormElements->value->Add($o_formElement);
						}
					}
					
					/* if we have not read only mode */
					if (!$this->FormObject->value->ReadonlyAll) {
						if ($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway == \fPHP\Base\forestBase::MongoDB) {
							/* get validation rules of tablefield and iterate them */
							$o_querySelect = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::SELECT, 'sys_fphp_tablefield_validationrule');
							
							$column_A = new \fPHP\Base\forestSQLColumn($o_querySelect);
								$column_A->Column = '*';
							
							$o_querySelect->Query->Columns->Add($column_A);
							
							$column_B = new \fPHP\Base\forestSQLColumn($o_querySelect);
								$column_B->Column = 'TablefieldUUID';
							
							$where_A = new \fPHP\Base\forestSQLWhere($o_querySelect);
								$where_A->Column = $column_B;
								$where_A->Value = $where_A->ParseValue($o_tableField->UUID);
								$where_A->Operator = '=';
							
							$o_querySelect->Query->Where->Add($where_A);
							
							$o_resultTablefieldValidationRules = $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_querySelect, false, false);
							
							foreach ($o_resultTablefieldValidationRules as &$o_resultTablefieldValidationRule) {
								$o_querySelect = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::SELECT, 'sys_fphp_validationrule');
							
								$column_A = new \fPHP\Base\forestSQLColumn($o_querySelect);
									$column_A->Column = 'Name';
								
								$o_querySelect->Query->Columns->Add($column_A);
								
								$column_B = new \fPHP\Base\forestSQLColumn($o_querySelect);
									$column_B->Column = 'UUID';
								
								$where_A = new \fPHP\Base\forestSQLWhere($o_querySelect);
									$where_A->Column = $column_B;
									$where_A->Value = $where_A->ParseValue($o_resultTablefieldValidationRule['ValidationruleUUID']);
									$where_A->Operator = '=';
								
								$o_querySelect->Query->Where->Add($where_A);
								
								$o_resultValidationRule = $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_querySelect, false, false);
								
								/* we only expect and accept one record as result; any other result is invalid */
								if (count($o_resultValidationRule) == 1) {
									$o_resultTablefieldValidationRule['Name'] = $o_resultValidationRule[0]['Name'];
								}
							}
							
							$o_result = $o_resultTablefieldValidationRules;
						} else {
							/* get validation rules of tablefield and iterate them */
							$o_querySelect = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::SELECT, 'sys_fphp_tablefield_validationrule');
							
							$column_A = new \fPHP\Base\forestSQLColumn($o_querySelect);
								$column_A->Column = '*';
							
							$o_querySelect->Query->Columns->Add($column_A);
							
							$join_A = new \fPHP\Base\forestSQLJoin($o_querySelect);
								$join_A->JoinType = 'INNER JOIN';
								$join_A->Table = 'sys_fphp_validationrule';

							$relation_A = new \fPHP\Base\forestSQLRelation($o_querySelect);
							
							$column_B = new \fPHP\Base\forestSQLColumn($o_querySelect);
								$column_B->Column = 'ValidationruleUUID';
								
							$column_C = new \fPHP\Base\forestSQLColumn($o_querySelect);
								$column_C->Column = 'UUID';
								$column_C->Table = $join_A->Table;
							
							$relation_A->ColumnLeft = $column_B;
							$relation_A->ColumnRight = $column_C;
							$relation_A->Operator = '=';
							
							$join_A->Relations->Add($relation_A);
								
							$o_querySelect->Query->Joins->Add($join_A);
							
							$column_D = new \fPHP\Base\forestSQLColumn($o_querySelect);
								$column_D->Column = 'TablefieldUUID';
							
							$where_A = new \fPHP\Base\forestSQLWhere($o_querySelect);
								$where_A->Column = $column_D;
								$where_A->Value = $where_A->ParseValue($o_tableField->UUID);
								$where_A->Operator = '=';
							
							$o_querySelect->Query->Where->Add($where_A);
							
							$o_result = $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_querySelect, false, false);
						}
						
						foreach ($o_result as $o_row) {
							/* render validation rules */
							$this->FormObject->value->ValRequiredMessage = $o_glob->GetTranslation('ValRequiredMessage', 1);
							
							$s_param01 = ( ((empty($o_row['ValidationRuleParam01'])) || ($o_row['ValidationRuleParam01'] == 'NULL')) ? null : $o_row['ValidationRuleParam01'] );
							$s_param02 = ( ((empty($o_row['ValidationRuleParam02'])) || ($o_row['ValidationRuleParam02'] == 'NULL')) ? null : $o_row['ValidationRuleParam02'] );
							$s_autoRequired = ( (($o_row['ValidationRuleRequired'] == 1)) ? 'true' : 'false' );
							
							$this->FormObject->value->ValRules->Add(new \fPHP\Forms\forestFormValidationRule($p_o_twig->fphp_Table . '_' . $o_tableField->FieldName, $o_row['Name'], $s_param01, $s_param02, $s_autoRequired));
							
							/* set required setting for form element */
							if ( ($o_row['Name'] == 'required') || ($s_autoRequired == 'true') ) {
								$o_formElement->Required = true;
							}
						}
					}
				}
			}
			
			/* render info columns - readonly */
			if ($this->FormObject->value->ReadonlyAll) {
				/* get values for info columns when configured */
				$i_infoColumns = $o_glob->TablesInformation[$p_o_twig->fphp_TableUUID]['InfoColumns'];
				
				if ($i_infoColumns == 10) {
					$o_formElement = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::DATETIMELOCAL);
					$o_formElement->Id = 'readonly_' . $p_o_twig->fphp_Table . 'Created';
					$o_formElement->Label = $o_glob->GetTranslation('sortCreated') . ':';
					if (strval($p_o_twig->{'Created'}) != 'NULL') {
						$o_formElement->Value = $p_o_twig->{'Created'}->ToString('Y-m-d\TH:i:s');
					}
					
					if ($o_lastTab != null) {
						$o_lastTab->FormElements->Add($o_formElement);
					} else {
						$this->FormElements->value->Add($o_formElement);
					}
					
					$o_formElement = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::TEXT);
					$o_formElement->Id = 'readonly_' . $p_o_twig->fphp_Table . 'CreatedBy';
					$o_formElement->Label = $o_glob->GetTranslation('sortCreatedBy') . ':';
					$o_formElement->Value = $o_glob->GetUserNameByUUID($p_o_twig->{'CreatedBy'});
					
					if ($o_lastTab != null) {
						$o_lastTab->FormElements->Add($o_formElement);
					} else {
						$this->FormElements->value->Add($o_formElement);
					}
				} else if ($i_infoColumns == 100) {
					$o_formElement = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::DATETIMELOCAL);
					$o_formElement->Id = 'readonly_' . $p_o_twig->fphp_Table . 'Modified';
					$o_formElement->Label = $o_glob->GetTranslation('sortModified') . ':';
					if (strval($p_o_twig->{'Modified'}) != 'NULL') {
						$o_formElement->Value = $p_o_twig->{'Modified'}->ToString('Y-m-d\TH:i:s');
					}
					
					if ($o_lastTab != null) {
						$o_lastTab->FormElements->Add($o_formElement);
					} else {
						$this->FormElements->value->Add($o_formElement);
					}
					
					$o_formElement = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::TEXT);
					$o_formElement->Id = 'readonly_' . $p_o_twig->fphp_Table . 'ModifiedBy';
					$o_formElement->Label = $o_glob->GetTranslation('sortModifiedBy') . ':';
					$o_formElement->Value = $o_glob->GetUserNameByUUID($p_o_twig->{'ModifiedBy'});
					
					if ($o_lastTab != null) {
						$o_lastTab->FormElements->Add($o_formElement);
					} else {
						$this->FormElements->value->Add($o_formElement);
					}
				} else if ($i_infoColumns == 1000) {
					$o_formElement = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::DATETIMELOCAL);
					$o_formElement->Id = 'readonly_' . $p_o_twig->fphp_Table . 'Created';
					$o_formElement->Label = $o_glob->GetTranslation('sortCreated') . ':';
					if (strval($p_o_twig->{'Created'}) != 'NULL') {
						$o_formElement->Value = $p_o_twig->{'Created'}->ToString('Y-m-d\TH:i:s');
					}
					
					if ($o_lastTab != null) {
						$o_lastTab->FormElements->Add($o_formElement);
					} else {
						$this->FormElements->value->Add($o_formElement);
					}
					
					$o_formElement = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::TEXT);
					$o_formElement->Id = 'readonly_' . $p_o_twig->fphp_Table . 'CreatedBy';
					$o_formElement->Label = $o_glob->GetTranslation('sortCreatedBy') . ':';
					$o_formElement->Value = $o_glob->GetUserNameByUUID($p_o_twig->{'CreatedBy'});
					
					if ($o_lastTab != null) {
						$o_lastTab->FormElements->Add($o_formElement);
					} else {
						$this->FormElements->value->Add($o_formElement);
					}
					
					$o_formElement = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::DATETIMELOCAL);
					$o_formElement->Id = 'readonly_' . $p_o_twig->fphp_Table . 'Modified';
					$o_formElement->Label = $o_glob->GetTranslation('sortModified') . ':';
					if (strval($p_o_twig->{'Modified'}) != 'NULL') {
						$o_formElement->Value = $p_o_twig->{'Modified'}->ToString('Y-m-d\TH:i:s');
					}
					
					if ($o_lastTab != null) {
						$o_lastTab->FormElements->Add($o_formElement);
					} else {
						$this->FormElements->value->Add($o_formElement);
					}
					
					$o_formElement = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::TEXT);
					$o_formElement->Id = 'readonly_' . $p_o_twig->fphp_Table . 'ModifiedBy';
					$o_formElement->Label = $o_glob->GetTranslation('sortModifiedBy') . ':';
					$o_formElement->Value = $o_glob->GetUserNameByUUID($p_o_twig->{'ModifiedBy'});
					
					if ($o_lastTab != null) {
						$o_lastTab->FormElements->Add($o_formElement);
					} else {
						$this->FormElements->value->Add($o_formElement);
					}
				}
			}
			
			/* add auto checkin form element if current record is checked out */
			$o_checkoutTwig = new \fPHP\Twigs\checkoutTwig;
			
			if ( ($p_o_twig->fphp_HasUUID) && (!$p_o_twig->IsEmpty()) && ($o_checkoutTwig->GetRecordPrimary(array($p_o_twig->UUID), array('ForeignUUID'))) && (!$this->FormObject->value->ReadonlyAll) ) {
				/* query auto checkin form element */
				if (!($o_formelementTwig->GetRecordPrimary(array(\fPHP\Forms\forestFormElement::AUTOCHECKIN), array('Name')))) {
					throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
				}
				
				/* create captcha form element and adjust settings */
				$o_formElement = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::AUTOCHECKIN);
				$o_formElement->loadJSON($o_formelementTwig->JSONEncodedSettings);
				$o_formElement->Id = $p_o_twig->fphp_Table . '_AutocheckinStandard';
				
				/* usually it will be added to the last tab or to form element object list */
				if ($o_lastTab != null) {
					$o_lastTab->FormElements->Add($o_formElement);
				} else {
					$this->FormElements->value->Add($o_formElement);
				}
			}
			
			/* add checkout message in readonly mode if current record is checked out */
			$o_checkoutTwig = new \fPHP\Twigs\checkoutTwig;
			
			if ( ($p_o_twig->fphp_HasUUID) && (!$p_o_twig->IsEmpty()) && ($o_checkoutTwig->GetRecordPrimary(array($p_o_twig->UUID), array('ForeignUUID'))) && ($this->FormObject->value->ReadonlyAll) ) {
				$this->CheckoutMessage->value = '<div class="alert alert-warning alert-dismissible fade show" role="alert">' .
					'<div><span class="bi bi-exclamation-triangle-fill h5"></span>&nbsp;' . \fPHP\Helper\forestStringLib::sprintf2($o_glob->GetTranslation('messageCheckoutText', 1), array($o_glob->GetUserNameByUUID($o_checkoutTwig->UserUUID), $o_checkoutTwig->Timestamp)) . "\n" . '</div>' . "\n" .
					'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' . "\n" .
				'</div>' . "\n";
			}
			
			/* if we are using a captcha element and we have not read only mode */
			if ( ($this->FormObject->value->UseCaptcha) && (!$this->FormObject->value->ReadonlyAll) ) {
				/* query captcha form element */
				if (!($o_formelementTwig->GetRecordPrimary(array(\fPHP\Forms\forestFormElement::CAPTCHA), array('Name')))) {
					throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
				}
				
				/* create captcha form element and adjust settings */
				$o_formElement = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::CAPTCHA);
				$o_formElement->loadJSON($o_formelementTwig->JSONEncodedSettings);
				$o_formElement->Id = $p_o_twig->fphp_Table . '_Captcha';
				$this->FormObject->value->ValRules->Add(new \fPHP\Forms\forestFormValidationRule($p_o_twig->fphp_Table . '_Captcha', 'required', 'true', 'NULL', 'false'));
				
				/* usually it will be added to the last tab or to form element object list */
				if ($o_lastTab != null) {
					$o_lastTab->FormElements->Add($o_formElement);
				} else {
					$this->FormElements->value->Add($o_formElement);
				}
			}
			
			/* print footer flag */
			if ($this->PrintFooter->value) {
				/* if modal, add standard submit + cancel to footer */
				if ($this->FormModalConfiguration->value->Modal) {
					if (!$this->FormObject->value->ReadonlyAll) {
						/* query and add standard submit to footer, if we do not have readonly all flag */
						if (!($o_formelementTwig->GetRecordPrimary(array('submit'), array('Name')))) {
							throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
						}
						
						/* create submit form element and adjust settings */
						$o_submit = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::BUTTON);
						$o_submit->loadJSON($o_formelementTwig->JSONEncodedSettings);
						$o_submit->ButtonText = htmlspecialchars_decode($o_submit->ButtonText);
						$o_submit->Id = $o_submit->Id . '_' . substr($o_glob->Security->GenRandomHash(), 0, 4);
						
						$this->FormFooterElements->value->Add($o_submit);
					}
					
					/* query cancel form element */
					if (!($o_formelementTwig->GetRecordPrimary(array('cancel'), array('Name')))) {
						throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
					}
					
					/* create cancel form element and adjust settings */
					$o_cancel = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::BUTTON);
					$o_cancel->loadJSON($o_formelementTwig->JSONEncodedSettings);
					$o_cancel->ButtonText = htmlspecialchars_decode($o_cancel->ButtonText, ( ENT_QUOTES | ENT_HTML5 ));
					$o_cancel->Id = $o_cancel->Id . '_' . substr($o_glob->Security->GenRandomHash(), 0, 4);
					
					$this->FormFooterElements->value->Add($o_cancel);
				}
			}
			
			/* add form key as hash as hidden field in form footer */
			$this->AddFormKey();
			
			/* use honeypot fields if it is activated and configured in fphp-trunk */
			$this->AddHoneypotFields($p_o_twig);
			
			/* add form tabs if we have any */
			foreach ($a_tabsInfo as $o_tab) {
				if ($this->FormObject->value->ReadonlyAll) {
					$o_tab->ReadOnly = true;
				}
				
				$this->FormTabs->value->Add($o_tab);
			}
			
			/*$s_json_general = '
			{
				"FormGroupClass" : "NULL",
				"Label" : "NULL",
				"LabelClass" : "NULL",
				"LabelFor" : "NULL",
				"FormElementClass" : "NULL",
				
				"Class" : "NULL",
				"Description" : "NULL",
				"DescriptionClass" : "NULL",
				"Disabled" : false,
				"Id" : "NULL",
				"Name" : "NULL",
				"AutoFocus" : false,
				"Required" : false,
				"Style" : "NULL",
				"Value" : "NULL",
				"ValMessage" : "NULL"
			}
			';*/
			
			/*$s_json_input = '
			{
				"FormGroupClass" : "NULL",
				"Label" : "NULL",
				"LabelClass" : "NULL",
				"LabelFor" : "NULL",
				"FormElementClass" : "NULL",
				
				"Class" : "NULL",
				"Description" : "NULL",
				"DescriptionClass" : "NULL",
				"Disabled" : false,
				"Id" : "NULL",
				"Name" : "NULL",
				"AutoFocus" : false,
				"Required" : false,
				"Style" : "NULL",
				"Value" : "NULL",
				"ValMessage" : "NULL",
				
				"Accept" : "NULL",
				"AutoComplete" : true,
				"Capture" : "NULL",
				"Dirname" : "NULL",
				"List" : "NULL",
				"Max" : "NULL",
				"Min" : "NULL",
				"Multiple" : false,
				"Options" : {},
				"Pattern" : "NULL",
				"PatternTitle" : "NULL",
				"Placeholder" : "NULL",
				"Readonly" : false,
				"Size" : 0,
				"Step" : 0,
				
				"Form" : "NULL",
				"FormAction" : "NULL",
				"FormEnctype" : "NULL",
				"FormMethod" : "NULL",
				"FormTarget" : "NULL",
				"FormNoValidate" : false
			}
			';*/
			
			/*$s_json_radio = '
			{
				"FormGroupClass" : "NULL",
				"Label" : "NULL",
				"LabelClass" : "NULL",
				"LabelFor" : "NULL",
				"FormElementClass" : "NULL",
				
				"Class" : "NULL",
				"Description" : "NULL",
				"DescriptionClass" : "NULL",
				"Disabled" : false,
				"Id" : "NULL",
				"Name" : "NULL",
				"AutoFocus" : false,
				"Required" : false,
				"Style" : "NULL",
				"Value" : "NULL",
				"ValMessage" : "NULL",
				
				"Accept" : "NULL",
				"AutoComplete" : true,
				"Capture" : "NULL",
				"Dirname" : "NULL",
				"List" : "NULL",
				"Max" : "NULL",
				"Min" : "NULL",
				"Multiple" : false,
				"Options" : {},
				"Pattern" : "NULL",
				"PatternTitle" : "NULL",
				"Placeholder" : "NULL",
				"Readonly" : false,
				"Size" : 0,
				"Step" : 0,
				
				"Form" : "NULL",
				"FormAction" : "NULL",
				"FormEnctype" : "NULL",
				"FormMethod" : "NULL",
				"FormTarget" : "NULL",
				"FormNoValidate" : false,
				
				"RadioClass" : "NULL",
				"RadioLabelClass" : "NULL"
			}
			';*/
			
			/*$s_json_checkbox = '
			{
				"FormGroupClass" : "NULL",
				"Label" : "NULL",
				"LabelClass" : "NULL",
				"LabelFor" : "NULL",
				"FormElementClass" : "NULL",
				
				"Class" : "NULL",
				"Description" : "NULL",
				"DescriptionClass" : "NULL",
				"Disabled" : false,
				"Id" : "NULL",
				"Name" : "NULL",
				"AutoFocus" : false,
				"Required" : false,
				"Style" : "NULL",
				"Value" : "NULL",
				"ValMessage" : "NULL",
				
				"Accept" : "NULL",
				"AutoComplete" : true,
				"Capture" : "NULL",
				"Dirname" : "NULL",
				"List" : "NULL",
				"Max" : "NULL",
				"Min" : "NULL",
				"Multiple" : false,
				"Options" : {},
				"Pattern" : "NULL",
				"PatternTitle" : "NULL",
				"Placeholder" : "NULL",
				"Readonly" : false,
				"Size" : 0,
				"Step" : 0,
				
				"Form" : "NULL",
				"FormAction" : "NULL",
				"FormEnctype" : "NULL",
				"FormMethod" : "NULL",
				"FormTarget" : "NULL",
				"FormNoValidate" : false,
				
				"CheckboxClass" : "NULL",
				"CheckboxLabelClass" : "NULL"
			}
			';*/
			
			/*$s_json_textarea = '
			{
				"FormGroupClass" : "NULL",
				"Label" : "NULL",
				"LabelClass" : "NULL",
				"LabelFor" : "NULL",
				"FormElementClass" : "NULL",
				
				"Class" : "NULL",
				"Description" : "NULL",
				"DescriptionClass" : "NULL",
				"Disabled" : false,
				"Id" : "NULL",
				"Name" : "NULL",
				"AutoFocus" : false,
				"Required" : false,
				"Style" : "NULL",
				"Value" : "NULL",
				"ValMessage" : "NULL",
				
				"Rows" : 0,
				"Cols" : 0,
				"Dirname" : "NULL",
				"Placeholder" : "NULL",
				"Readonly" : false,
				"Wrap" : false
			}
			';*/
			
			/*$s_json_select = '
			{
				"FormGroupClass" : "NULL",
				"Label" : "NULL",
				"LabelClass" : "NULL",
				"LabelFor" : "NULL",
				"FormElementClass" : "NULL",
				
				"Class" : "NULL",
				"Description" : "NULL",
				"DescriptionClass" : "NULL",
				"Disabled" : false,
				"Id" : "NULL",
				"Name" : "NULL",
				"AutoFocus" : false,
				"Required" : false,
				"Style" : "NULL",
				"Value" : "NULL",
				"ValMessage" : "NULL",
				
				"Multiple : false,	
				"Options : {},
				"Size : 1,
				"Data : "NULL"
			}
			';*/
			
			/*$s_json_button = '
			{
				"FormGroupClass" : "NULL",
				"Label" : "NULL",
				"LabelClass" : "NULL",
				"LabelFor" : "NULL",
				"FormElementClass" : "NULL",
				
				"Class" : "NULL",
				"Description" : "NULL",
				"DescriptionClass" : "NULL",
				"Disabled" : false,
				"Id" : "NULL",
				"Name" : "NULL",
				"AutoFocus" : false,
				"Required" : false,
				"Style" : "NULL",
				"Value" : "NULL",
				"ValMessage" : "NULL",
				
				"Accept" : "NULL",
				"AutoComplete" : true,
				"Capture" : "NULL",
				"Dirname" : "NULL",
				"List" : "NULL",
				"Max" : "NULL",
				"Min" : "NULL",
				"Multiple" : false,
				"Options" : {},
				"Pattern" : "NULL",
				"PatternTitle" : "NULL",
				"Placeholder" : "NULL",
				"Readonly" : false,
				"Size" : 0,
				"Step" : 0,
				
				"Form" : "NULL",
				"FormAction" : "NULL",
				"FormEnctype" : "NULL",
				"FormMethod" : "NULL",
				"FormTarget" : "NULL",
				"FormNoValidate" : false,
				
				"Type" : "NULL",
				"Data" : "NULL",
				"ButtonText" : "NULL",
				"NoFormGroup" : "NULL",
				"WrapSpanClass" : "NULL"
			}
			';*/
		}
	}

	/**
	 * render the complete form with all its settings
	 *
	 * @return string
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __toString() {
		/* check if we have elements of type FILE or DROPZONE */
		if ($this->CheckUploadElementConfigured()) {
			$this->FormObject->value->Enctype = 'multipart/form-data';
		}
		
		$s_foo = '';
		
		/* render modal */
		if ($this->FormModalConfiguration->value->Modal) {
			$this->FormModalConfiguration->value->CheckIsset();
			
			$s_foo .= '<!-- Modal with tabs -->' . "\n";
			$s_foo .= '	<div class="' . $this->FormModalConfiguration->value->ModalClass . '" id="' . $this->FormModalConfiguration->value->ModalId . '"';
			
			if (issetStr($this->FormModalConfiguration->value->ModalRole)) {
				$s_foo .= ' role="' . $this->FormModalConfiguration->value->ModalRole . '"';
			}
			
			$s_foo .= '>' . "\n";
			
			$s_foo .= '		<div class="' . $this->FormModalConfiguration->value->ModalDialogClass . '">' . "\n";

			$s_foo .= '			<!-- Modal content-->' . "\n";
			$s_foo .= '			<div class="' . $this->FormModalConfiguration->value->ModalDialogContentClass . '">' . "\n";
		}
		
		/* render form object */
		$s_foo .= strval($this->FormObject->value);
		
		/* render modal header */
		if ($this->FormModalConfiguration->value->Modal) {
			$s_foo .= '<div class="' . $this->FormModalConfiguration->value->ModalHeaderClass . '">' . "\n";
			$s_foo .= '<h4 class=' . $this->FormModalConfiguration->value->ModalTitleClass . '>' . $this->FormModalConfiguration->value->ModalTitle . '</h4>' . "\n";
			
			$s_headerCloseContent = '';

			if (issetStr($this->FormModalConfiguration->value->ModalHeaderCloseContent)) {
				$s_headerCloseContent = htmlspecialchars_decode($this->FormModalConfiguration->value->ModalHeaderCloseContent, ENT_HTML5);
			}

			$s_foo .= '	<button type="button" class="' . $this->FormModalConfiguration->value->ModalHeaderCloseClass . '" data-bs-dismiss="' . $this->FormModalConfiguration->value->ModalHeaderDismissClass . '" aria-label="Close Modal Form">' . $s_headerCloseContent . '</button>' . "\n";
			
			$s_foo .= '</div>' . "\n";
			$s_foo .= '<div class="' . $this->FormModalConfiguration->value->ModalBodyClass . '">' . "\n";
		}
		
		/* render checkout message */
		if (issetStr($this->CheckoutMessage->value)) {
			$s_foo .= $this->CheckoutMessage->value . "\n";
		}
		
		/* render something before form */
		if (issetStr($this->BeforeForm->value)) {
			if ($this->BeforeFormRightAlign->value) {
				$s_foo .= '<div class="text-end" style="margin-bottom: 5px;">' . "\n";
			} else {
				$s_foo .= '<div style="margin-bottom: 5px;">' . "\n";
			}
			
			$s_foo .= $this->BeforeForm->value . "\n";
			$s_foo .= '</div>' . "\n";
		}
		
		/* render tabs */
		if ($this->FormTabConfiguration->value->Tab) {
			$this->FormTabConfiguration->value->CheckIsset();
			
			if ($this->FormTabs->value->Count() > 0) {
				/* render form elements in tab */
				$this->PrintTabElements($this->FormTabs->value, $s_foo);
				
				if (!$this->FormModalConfiguration->value->Modal) {
					$s_foo .= '<div class="' . $this->FormTabConfiguration->value->TabFooterClass . '">' . "\n";
						$this->PrintFormElements($this->FormFooterElements->value, $s_foo);
					$s_foo .= '</div>' . "\n";
				}
			}
		} else {
			/* render form elements */
			$this->PrintFormElements($this->FormElements->value, $s_foo);
		}
		
		/* render something after form, but before sub form */
		if (issetStr($this->AfterForm->value)) {
			if ($this->AfterFormRightAlign->value) {
				$s_foo .= '<div class="text-end" style="margin-bottom: 5px;">' . "\n";
			} else {
				$s_foo .= '<div style="margin-bottom: 5px;">' . "\n";
			}
			
			$s_foo .= $this->AfterForm->value . "\n";
			$s_foo .= '</div>' . "\n";
		}
		
		/* render modal footer */
		if ($this->FormModalConfiguration->value->Modal) {
			/* append sub form elements */
			if (issetStr($this->FormModalSubForm->value)) {
				$s_foo .= '<div>' . $this->FormModalSubForm->value . '</div>' . "\n";
			}
			
			$s_foo .= '</div>' . "\n"; /* end of modal body */
			
			if ($this->FormFooterElements->value->Count() > 0) {
				$s_foo .= '<div class="' . $this->FormModalConfiguration->value->ModalFooterClass . '">' . "\n";
					$this->PrintFormElements($this->FormFooterElements->value, $s_foo);
				$s_foo .= '</div>' . "\n";
			}
		}
		
		/* render validation rules */
		$this->PrintValRules($s_foo);
		
		return \fPHP\Helper\forestStringLib::closeHTMLTags($s_foo);
	}
	
	/**
	 * create standard modal form elements, by loading standard form formElement, submit and cancel button
	 *
	 * @param forestTwig $p_o_twig
	 * @param string $p_s_title  title of form
	 * @param bool $p_b_showSubmit  true - create submit button, false - do not create submit button
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function CreateModalForm(\fPHP\Twigs\forestTwig $p_o_twig, $p_s_title, $p_b_showSubmit = true) {
		if ($this->Automatic->value) {
			throw new forestException('Form has been already generated automatically.');
		}
		
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* get table */
		$o_tableTwig = new \fPHP\Twigs\tableTwig;
		
		/* query table record */
		if (!($o_tableTwig->GetRecordPrimary(array($p_o_twig->fphp_Table), array('Name')))) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$o_formelementTwig = new \fPHP\Twigs\formelementTwig;
		
		/* look in tablefields for formobject, if not get the standard by formelementuuid */
		if (!($o_formelementTwig->GetRecordPrimary(array('form'), array('Name')))) {
			throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
		}
		
		$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
		$s_formObjectJSONsettings = '';
		
		if (!($o_tablefieldTwig->GetRecordPrimary(array($o_tableTwig->UUID, $o_formelementTwig->UUID), array('TableUUID', 'FormElementUUID')))) {
			/* no tablefield for table, take standard */
			$s_formObjectJSONsettings = $o_formelementTwig->JSONEncodedSettings;
		} else {
			$s_formObjectJSONsettings = $o_tablefieldTwig->JSONEncodedSettings;
		}
		
		/* create formobject(check $this->Readonly->value), modal object and maybe tab objects */
		$this->FormObject->value->loadJSON($s_formObjectJSONsettings);
		$this->FormModalConfiguration->value->loadJSON($s_formObjectJSONsettings);
		$this->FormTabConfiguration->value->loadJSON($s_formObjectJSONsettings);
		
		$this->FormModalConfiguration->value->ModalId = $this->FormObject->value->Id . 'Modal';
		$this->FormModalConfiguration->value->ModalTitle = $p_s_title;
		$this->FormTabConfiguration->value->Tab = false;
		
		/* create standard submit button to modal footer */
		if ($p_b_showSubmit) {
			if (!($o_formelementTwig->GetRecordPrimary(array('submit'), array('Name')))) {
				throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
			}
			
			$o_button = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::BUTTON);
			$o_button->loadJSON($o_formelementTwig->JSONEncodedSettings);
			$o_button->ButtonText = htmlspecialchars_decode($o_button->ButtonText);
			$this->FormFooterElements->value->Add($o_button);
		}
		
		/* create standard cancel button to modal footer */
		if (!($o_formelementTwig->GetRecordPrimary(array('cancel'), array('Name')))) {
			throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
		}
		
		$o_cancel = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::BUTTON);
		$o_cancel->loadJSON($o_formelementTwig->JSONEncodedSettings);
		$o_cancel->ButtonText = htmlspecialchars_decode($o_cancel->ButtonText);
		$this->FormFooterElements->value->Add($o_cancel);
		
		/* add form key as hash as hidden field in form footer */
		$this->AddFormKey();
		
		/* use honeypot fields if it is activated and configured in fphp-trunk */
		$this->AddHoneypotFields($p_o_twig);
		
		/* automatic display of modal form */
		$this->Automatic->value = true;
	}
	
	/**
	 * create standard delete modal form elements, by loading standard form formElement, submit and cancel button
	 *
	 * @param forestTwig $p_o_twig
	 * @param string $p_s_title  title of form
	 * @param string $p_s_description  description for delete form
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function CreateDeleteModalForm(\fPHP\Twigs\forestTwig $p_o_twig, $p_s_title, $p_s_description) {
		if ($this->Automatic->value) {
			throw new forestException('Form has been already generated automatically.');
		}
		
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* get table */
		$o_tableTwig = new \fPHP\Twigs\tableTwig;
		
		/* query table record */
		if (!($o_tableTwig->GetRecordPrimary(array($p_o_twig->fphp_Table), array('Name')))) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$o_formelementTwig = new \fPHP\Twigs\formelementTwig;
		
		/* look in tablefields for formobject, if not get the standard by formelementuuid */
		if (!($o_formelementTwig->GetRecordPrimary(array('form'), array('Name')))) {
			throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
		}
		
		$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
		$s_formObjectJSONsettings = '';
		
		if (!($o_tablefieldTwig->GetRecordPrimary(array($o_tableTwig->UUID, $o_formelementTwig->UUID), array('TableUUID', 'FormElementUUID')))) {
			/* no tablefield for table, take standard */
			$s_formObjectJSONsettings = $o_formelementTwig->JSONEncodedSettings;
		} else {
			$s_formObjectJSONsettings = $o_tablefieldTwig->JSONEncodedSettings;
		}
		
		/* create formobject(check $this->Readonly->value), modal object and maybe tab objects */
		$this->FormObject->value->loadJSON($s_formObjectJSONsettings);
		$this->FormModalConfiguration->value->loadJSON($s_formObjectJSONsettings);
		$this->FormTabConfiguration->value->loadJSON($s_formObjectJSONsettings);
		
		$this->FormModalConfiguration->value->ModalId = $this->FormObject->value->Id . 'Modal';
		$this->FormModalConfiguration->value->ModalTitle = $p_s_title;
		$this->FormTabConfiguration->value->Tab = false;
		
		/* create and add description to modal form */
		$o_description = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::DESCRIPTION);
		$o_description->Description = $p_s_description;
		$o_description->NoFormGroup = true;
		
		$this->FormElements->value->Add($o_description);
		
		/* create standard yes button to modal footer */
		if (!($o_formelementTwig->GetRecordPrimary(array('yes'), array('Name')))) {
			throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
		}
		
		$o_button = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::BUTTON);
		$o_button->loadJSON($o_formelementTwig->JSONEncodedSettings);
		$o_button->ButtonText = htmlspecialchars_decode($o_button->ButtonText);
		$this->FormFooterElements->value->Add($o_button);
		
		/* create standard no button to modal footer */
		if (!($o_formelementTwig->GetRecordPrimary(array('no'), array('Name')))) {
			throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
		}
		
		$o_cancel = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::BUTTON);
		$o_cancel->loadJSON($o_formelementTwig->JSONEncodedSettings);
		$o_cancel->ButtonText = htmlspecialchars_decode($o_cancel->ButtonText);
		$this->FormFooterElements->value->Add($o_cancel);
		
		/* add form key as hash as hidden field in form footer */
		$this->AddFormKey();
		
		/* use honeypot fields if it is activated and configured in fphp-trunk */
		$this->AddHoneypotFields($p_o_twig);
		
		/* automatic display of modal form */
		$this->Automatic->value = true;
	}
	
	/**
	 * add hidden form key field to form footer elements
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function AddFormKey() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* add form key as hash as hidden field in form footer */
		if ( ($o_glob->Trunk->FormKey) && ($o_glob->Security->SessionData->Exists('formkey')) ) {
			$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
			$o_hidden->Id = 'sys_fphp_formkeyHash';
			$o_hidden->Value = password_hash($o_glob->Security->SessionData->{'formkey'}, PASSWORD_DEFAULT);
			
			$this->FormFooterElements->value->Add($o_hidden);
		}
	}

	/**
	 * add hidden honeypot fields to form footer elements
	 *
	 * @param forestTwig $p_o_twig
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function AddHoneypotFields(\fPHP\Twigs\forestTwig $p_o_twig) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		if ( ($o_glob->Trunk->HoneypotFields) && ($o_glob->Trunk->MaxAmountHoneypot > 0) ) {
			$a_memory = array();
			$a_randomNames = array('buzz','rex','bo','hamm','slink','potato','woody','sarge','etch','lenny','squeeze','wheezy','jessie','stretch','buster','bullseye','bookworm','sid');

			$i_amount = mt_rand(1, $o_glob->Trunk->MaxAmountHoneypot);

			for ($i = 0; $i < $i_amount; $i++) {
				$s_hiddenId = null;
				
				do {
					$j = mt_rand(0, (count($a_randomNames) - 1));
					$k = mt_rand(1, 999);
					$s_hiddenId = $p_o_twig->fphp_Table . '_' . $a_randomNames[$j] . $k;
				} while (in_array($s_hiddenId, $a_memory));
				
				$a_memory[] = $s_hiddenId;
				
				$o_hiddenText = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::TEXT);
				$o_hiddenText->Id = $s_hiddenId;
				$o_hiddenText->NoDisplay = true;
				
				$this->FormFooterElements->value->Add($o_hiddenText);
			}
			
			/* insert hidden text ids into session */
			$o_glob->Security->SessionData->Add(implode(';', $a_memory), 'sys_fphp_honeypotfields');
		}
	}

	/**
	 * get form element by form element id
	 *
	 * @param string $p_s_formId
	 *
	 * @return forestFormElement
	 *
	 * @access public
	 * @static no
	 */
	public function GetFormElementByFormId($p_s_formId) {
		$o_return = null;
		
		foreach ($this->FormElements->value as $s_key => $o_formElement) {
			if ($o_formElement->Id == $p_s_formId) {
				$o_return = $o_formElement;
				break;
			}
		}
		
		if ($o_return == null) {
			foreach ($this->FormFooterElements->value as $s_key => $o_formElement) {
				if ($o_formElement->Id == $p_s_formId) {
					$o_return = $o_formElement;
					break;
				}
			}
			
			if ($o_return == null) {
				foreach ($this->FormTabs->value as $o_tabElement) {
					$o_return = $o_tabElement->GetFormElementByFormId($p_s_formId);
					
					if ($o_return != null) {
						break;
					}
				}
			}
		}
		
		return $o_return;
	}
	
	/**
	 * delete form element by form element id
	 *
	 * @param string $p_s_formId
	 *
	 * @return bool  true - form element has been deleted, false - form element has been not found
	 *
	 * @access public
	 * @static no
	 */
	public function DeleteFormElementByFormId($p_s_formId) {
		$b_return = false;
		$s_listKey = null;
		
		foreach ($this->FormElements->value as $s_key => $o_formElement) {
			if ($o_formElement->Id == $p_s_formId) {
				$s_listKey = $s_key;
				$b_return = true;
				break;
			}
		}
		
		if ($b_return) {
			$this->FormElements->value->Del($s_key);
		} else {
			foreach ($this->FormFooterElements->value as $s_key => $o_formElement) {
				if ($o_formElement->Id == $p_s_formId) {
					$s_listKey = $s_key;
					$b_return = true;
					break;
				}
			}
			
			if ($b_return) {
				$this->FormElements->value->Del($s_key);
			} else {
				foreach ($this->FormTabs->value as $o_tabElement) {
					if ($o_tabElement->DeleteFormElementByFormId($p_s_formId)) {
						$b_return = true;
						break;
					}
				}
			}
		}
		
		return $b_return;
	}
	
	/**
	 * add form element to form
	 *
	 * @param forestFormElement $p_o_formElement
	 * @param string $p_s_tabId  indicated on which tab the form element should be added
	 * @param bool $p_b_first  true - add as first element, false - just add to form element list
	 *
	 * @return bool  true - form element has been added, false - form element has been not added
	 *
	 * @access public
	 * @static no
	 */
	public function AddFormElement(\fPHP\Forms\forestFormElement $p_o_formElement, $p_s_tabId = 'general', $p_b_first = false) {
		$b_return = false;
		
		if ($p_s_tabId == null) {
			if ($p_b_first) {
				$this->FormElements->value->AddFirst($p_o_formElement);
			} else {
				$this->FormElements->value->Add($p_o_formElement);
			}
			
			$b_return = true;
		} else {
			foreach ($this->FormTabs->value as $o_tabElement) {
				if ($o_tabElement->TabId == $p_s_tabId) {
					$b_return = $o_tabElement->AddFormElement($p_o_formElement, $p_b_first);
				}
			}
		}
		
		return $b_return;
	}
	
	/**
	 * checks if a form has form elements with upload functionality like FILE or DROPZONE
	 *
	 * @return bool  true - form contains upload form elements, false - form does not contain upload form elements
	 *
	 * @access private
	 * @static no
	 */
	private function CheckUploadElementConfigured() {
		if ($this->FormTabConfiguration->value->Tab) {
			if ($this->FormTabs->value->Count() > 0) {
				foreach ($this->FormTabs->value as $o_tabElement) {
					foreach ($o_tabElement->FormElements as $o_formElement) {
						if ( ($o_formElement->getType() == \fPHP\Forms\forestFormElement::FILEDIALOG) || ($o_formElement->getType() == \fPHP\Forms\forestFormElement::DROPZONE) ) {
							return true;
						}
					}
				}
			}
		}
		
		foreach ($this->FormElements->value as $o_formElement) {
			if ( ($o_formElement->getType() == \fPHP\Forms\forestFormElement::FILEDIALOG) || ($o_formElement->getType() == \fPHP\Forms\forestFormElement::DROPZONE) ) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * render forest form tab elements
	 *
	 * @param forestObjectList('forestFormTab') $p_ol_tabElements
	 * @param string $p_s_tabId  indicated on which tab the form element should be added
	 * @param string $p_s_foo  form string which contains all html form elements, passed by reference
	 *
	 * @return null
	 *
	 * @access private
	 * @static no
	 */
	private function PrintTabElements($p_ol_tabElements, &$p_s_foo) {
		$b_first = true;
		$p_s_foo .= '<ul class="' . $this->FormTabConfiguration->value->TabMenuClass . '">' . "\n";
		
		$s_tabIdPrefix = '';
		
		if ($this->FormObject->value->ReadonlyAll) {
			$s_tabIdPrefix = 'readonly_';
		}
		
		/* render tab buttons */
		foreach ($p_ol_tabElements as $o_tabElement) {
			if ( (issetStr($o_tabElement->TabId)) && (issetStr($o_tabElement->TabTitle)) ) {
				$o_tabElement->CheckIsset();
				
				if ($b_first) {
					$p_s_foo .= '<li class="' . $this->FormTabConfiguration->value->TabLiClass . '"><a data-bs-toggle="' . $this->FormTabConfiguration->value->TabToggle . '" href="#' . $s_tabIdPrefix . $o_tabElement->TabId . '" class="' . $this->FormTabConfiguration->value->TabAClass . ' ' . $this->FormTabConfiguration->value->TabActiveClass . '" aria-current="page">' . $o_tabElement->TabTitle . '</a></li>' . "\n";
					$b_first = false;
				} else {
					$p_s_foo .= '<li class="' . $this->FormTabConfiguration->value->TabLiClass . '"><a data-bs-toggle="' . $this->FormTabConfiguration->value->TabToggle . '" href="#' . $s_tabIdPrefix . $o_tabElement->TabId . '" class="' . $this->FormTabConfiguration->value->TabAClass . '">' . $o_tabElement->TabTitle . '</a></li>' . "\n";
				}
			}
		}
		
		$p_s_foo .= '</ul>' . "\n";
		$p_s_foo .= '<div class="' . $this->FormTabConfiguration->value->TabContentClass . '">' . "\n";
		$b_first = true;
		
		/* render tab form elements */
		foreach ($p_ol_tabElements as $o_tabElement) {
			if ( (issetStr($o_tabElement->TabId)) && (issetStr($o_tabElement->TabTitle)) ) {
				if ($b_first) {
					$o_tabElement->Active = true;
					$b_first = false;
				}
				
				$o_tabElement->TempFormObject = $this->FormObject->value;
				$o_tabElement->TabClass = $this->FormTabConfiguration->value->TabElementClass;
				$o_tabElement->TabActiveClass = $this->FormTabConfiguration->value->TabElementActiveClass;
				
				$o_tabElement->CheckIsset(true);
				
				$p_s_foo .= strval($o_tabElement);
			}
		}
		
		$p_s_foo .= '</div>' . "\n";
	}
	
	/**
	 * render forest form form elements
	 *
	 * @param forestObjectList('forestFormElement') $p_ol_formElements
	 * @param string $p_s_foo  form string which contains all html form elements, passed by reference
	 *
	 * @return null
	 *
	 * @access private
	 * @static no
	 */
	private function PrintFormElements($p_ol_formElements, &$p_s_foo) {
		foreach ($p_ol_formElements as $o_formElement) {
			/* overwrite Form Group Class if it is not set */
			if (issetStr($this->FormObject->value->FormGroupClass)) {
				if (!issetStr($o_formElement->FormGroupClass)) {
					$o_formElement->FormGroupClass = $this->FormObject->value->FormGroupClass;
				}
			}
			
			/* overwrite Label Class if it is not set */
			if (issetStr($this->FormObject->value->LabelClass)) {
				if (!issetStr($o_formElement->LabelClass)) {
					$o_formElement->LabelClass = $this->FormObject->value->LabelClass;
				}
			}
			
			/* overwrite Form Element Class if it is not set */
			if (issetStr($this->FormObject->value->FormElementClass)) {
				if (!issetStr($o_formElement->FormElementClass)) {
					$o_formElement->FormElementClass = $this->FormObject->value->FormElementClass;
				}
			}
			
			/* overwrite Class if it is not set */
			if ( ($o_formElement->getType() != \fPHP\Forms\forestFormElement::RADIO) && ($o_formElement->getType() != \fPHP\Forms\forestFormElement::CHECKBOX) && ($o_formElement->getType() != \fPHP\Forms\forestFormElement::SELECT) && ($o_formElement->getType() != \fPHP\Forms\forestFormElement::LOOKUP) && ($o_formElement->getType() != \fPHP\Forms\forestFormElement::DESCRIPTION) ) {
				if (issetStr($this->FormObject->value->ClassAll)) {
					if (!issetStr($o_formElement->Class)) {
						$o_formElement->Class = $this->FormObject->value->ClassAll;
					}
				}
			}
			
			/* set required flag for all form elements */
			if ($this->FormObject->value->RequiredAll) {
				if ($o_formElement->getType() != \fPHP\Forms\forestFormElement::CHECKBOX) {
					$o_formElement->Required = true;
				}
			}
			
			/* set readonly flag for all form elements */
			if ($this->FormObject->value->ReadonlyAll) {
				if ( ($o_formElement->getType() != \fPHP\Forms\forestFormElement::SELECT) && ($o_formElement->getType() != \fPHP\Forms\forestFormElement::LOOKUP) && ($o_formElement->getType() != \fPHP\Forms\forestFormElement::COLOR) && ($o_formElement->getType() != \fPHP\Forms\forestFormElement::DROPZONE) && ($o_formElement->getType() != \fPHP\Forms\forestFormElement::RICHTEXT) && ($o_formElement->getType() != \fPHP\Forms\forestFormElement::DESCRIPTION) ) {
					$o_formElement->Readonly = true;
				}
				
				/* other elements do not have readonly flag, instead we are using disabled flag */
				if ( ($o_formElement->getType() == \fPHP\Forms\forestFormElement::RICHTEXT) || ($o_formElement->getType() == \fPHP\Forms\forestFormElement::RADIO) || ($o_formElement->getType() == \fPHP\Forms\forestFormElement::CHECKBOX) || ($o_formElement->getType() == \fPHP\Forms\forestFormElement::SELECT) || ($o_formElement->getType() == \fPHP\Forms\forestFormElement::LOOKUP) || ($o_formElement->getType() == \fPHP\Forms\forestFormElement::COLOR) || ( ($o_formElement->getType() == \fPHP\Forms\forestFormElement::BUTTON) && (!$o_formElement->NoFormGroup) ) ) {
					$o_formElement->Disabled = true;
				}
			}
			
			/* overwrite Radio Classes if it is not set */
			if ($o_formElement->getType() == \fPHP\Forms\forestFormElement::RADIO) {
				if (issetStr($this->FormObject->value->RadioContainerClass)) {
					if (!issetStr($o_formElement->RadioContainerClass)) {
						$o_formElement->RadioContainerClass = $this->FormObject->value->RadioContainerClass;
					}
				}
				
				if (issetStr($this->FormObject->value->RadioClass)) {
					if (!issetStr($o_formElement->RadioClass)) {
						$o_formElement->RadioClass = $this->FormObject->value->RadioClass;
					}
				}
				
				if (issetStr($this->FormObject->value->RadioLabelClass)) {
					if (!issetStr($o_formElement->RadioLabelClass)) {
						$o_formElement->RadioLabelClass = $this->FormObject->value->RadioLabelClass;
					}
				}
			}
			
			/* overwrite Checkbox Classes if it is not set */
			if ($o_formElement->getType() == \fPHP\Forms\forestFormElement::CHECKBOX) {
				if (issetStr($this->FormObject->value->CheckboxContainerClass)) {
					if (!issetStr($o_formElement->CheckboxContainerClass)) {
						$o_formElement->CheckboxContainerClass = $this->FormObject->value->CheckboxContainerClass;
					}
				}
				
				if (issetStr($this->FormObject->value->CheckboxClass)) {
					if (!issetStr($o_formElement->CheckboxClass)) {
						$o_formElement->CheckboxClass = $this->FormObject->value->CheckboxClass;
					}
				}
				
				if (issetStr($this->FormObject->value->CheckboxLabelClass)) {
					if (!issetStr($o_formElement->CheckboxLabelClass)) {
						$o_formElement->CheckboxLabelClass = $this->FormObject->value->CheckboxLabelClass;
					}
				}
			}
			
			/* overwrite Select class if it is not set */
			if ( ($o_formElement->getType() == \fPHP\Forms\forestFormElement::SELECT) || ($o_formElement->getType() == \fPHP\Forms\forestFormElement::LOOKUP) ) {
				if (issetStr($this->FormObject->value->SelectClass)) {
					if (!issetStr($o_formElement->Class)) {
						$o_formElement->Class = $this->FormObject->value->SelectClass;
					}
				}
			}
			
			$p_s_foo .= strval($o_formElement);
		}
	}
	
	/**
	 * render forest form validation rules for form
	 *
	 * @param string $p_s_foo  form string which contains all html form elements, passed by reference
	 *
	 * @return null
	 *
	 * @access private
	 * @static no
	 */
	private function PrintValRules(&$p_s_foo) {
		$i_valRules = $this->FormObject->value->ValRules->Count();
		
		if ($i_valRules > 0) {
			/* render standard required message */
			$p_s_foo .= '<div class="fphp_data_validator">
				{
					"s_formId" : "#' . $this->FormObject->value->Id . '",
					"s_requiredDefaultMessage" : "' . $this->FormObject->value->ValRequiredMessage . '",
					"a_rules" : [' . "\n";
			
			$i = 0;
			
			/* render each rule */
			foreach ($this->FormObject->value->ValRules as $o_validationRule){
				if (! ( ($o_validationRule->RuleParam01 == 'true') || ($o_validationRule->RuleParam01 == 'false') || (is_numeric($o_validationRule->RuleParam01)) ) ) {
					$o_validationRule->RuleParam01 = '"' . $o_validationRule->RuleParam01 . '"';
				}
				
				if (! ( ($o_validationRule->RuleParam02 == 'true') || ($o_validationRule->RuleParam02 == 'false') || (is_numeric($o_validationRule->RuleParam02)) ) ) {
					$o_validationRule->RuleParam02 = '"' . $o_validationRule->RuleParam02 . '"';
				}
				
				if (! ( ($o_validationRule->AutoRequired == 'true') || ($o_validationRule->AutoRequired == 'false') || (is_numeric($o_validationRule->AutoRequired)) ) ) {
					$o_validationRule->AutoRequired = '"' . $o_validationRule->AutoRequired . '"';
				}
				
				$p_s_foo .= '{ "s_formElementId" : "#' . $o_validationRule->FormElementId . '", "s_rule" : "' . $o_validationRule->Rule . '", "s_ruleParam01" : ' . $o_validationRule->RuleParam01 . ', "s_ruleParam02" : ' . $o_validationRule->RuleParam02 . ', "s_ruleAutoRequired" : ' . $o_validationRule->AutoRequired . ' }';
				
				if ($i < ($i_valRules - 1)) {
					$p_s_foo .= ',' . "\n";
				}
				
				$i++;
			}
			
			$p_s_foo .= '		]
				}
			</div>' . "\n";
		}
	}
}

class forestFormTab {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	private $TabId;
	private $TabTitle;
	private $FormElements;
	private $Active;
	private $TempFormObject;
	private $TabClass;
	private $TabActiveClass;
	private $ReadOnly;
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * constructor of forestFormTab class
	 *
	 * @param string $p_s_tabId  tab id for html tab element
	 * @param string $p_s_tabTitle  tab title for html tab element
	 * @param string $p_s_tabClass  tab class for html tab element
	 * @param string $p_b_printFooter  tab active class for html tab element
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __construct($p_s_tabId = null, $p_s_tabTitle = null, $p_s_tabClass = null, $p_s_tabActiveClass = null) {
		$this->TabId = new forestString;
		$this->TabTitle = new forestString;
		$this->FormElements = new forestObject(new forestObjectList('forestFormElement'), false);
		$this->Active = new forestBool;
		$this->TempFormObject = new forestObject(new forestFormElement(\fPHP\Forms\forestFormElement::FORM));
		$this->TabClass = new forestString;
		$this->TabActiveClass = new forestString;
		$this->ReadOnly = new forestBool;
		
		if ($p_s_tabId != null) {
			$this->TabId->value = $p_s_tabId;
		}
		
		if ($p_s_tabTitle != null) {
			$this->TabTitle->value = $p_s_tabTitle;
		}
		
		if ($p_s_tabClass != null) {
			$this->TabClass->value = $p_s_tabClass;
		}
		
		if ($p_s_tabActiveClass != null) {
			$this->TabActiveClass->value = $p_s_tabActiveClass;
		}
	}
	
	/**
	 * render the complete form tab with all its settings
	 *
	 * @return string
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __toString() {
		$s_tabId = $this->TabId->value;
		
		if ($this->ReadOnly->value) {
			$s_tabId = 'readonly_' . $s_tabId;
		}
		
		/* render tab container */
		if ($this->Active->value) {
			$s_foo = '<div id="'  . $s_tabId . '" class="' . $this->TabActiveClass->value . '">' . "\n";
		} else {
			$s_foo = '<div id="'  . $s_tabId . '" class="' . $this->TabClass->value . '">' . "\n";
		}
		
		/* render tab form elements */
		$this->PrintFormElements($s_foo);
		
		$s_foo .= '</div>' . "\n";
		
		return $s_foo;
	}
	
	/**
	 * render forest form form elements
	 *
	 * @param string $p_s_foo  form string which contains all html form elements, passed by reference
	 *
	 * @return null
	 *
	 * @access private
	 * @static no
	 */
	private function PrintFormElements(&$p_s_foo) {
		foreach ($this->FormElements->value as $o_formElement) {
			/* overwrite Form Group Class if it is not set */
			if (issetStr($this->TempFormObject->value->FormGroupClass)) {
				if (!issetStr($o_formElement->FormGroupClass)) {
					$o_formElement->FormGroupClass = $this->TempFormObject->value->FormGroupClass;
				}
			}
			
			/* overwrite Label Class if it is not set */
			if (issetStr($this->TempFormObject->value->LabelClass)) {
				if (!issetStr($o_formElement->LabelClass)) {
					$o_formElement->LabelClass = $this->TempFormObject->value->LabelClass;
				}
			}
			
			/* overwrite Form Element Class if it is not set */
			if (issetStr($this->TempFormObject->value->FormElementClass)) {
				if (!issetStr($o_formElement->FormElementClass)) {
					$o_formElement->FormElementClass = $this->TempFormObject->value->FormElementClass;
				}
			}
			
			/* overwrite Class if it is not set */
			if ( ($o_formElement->getType() != \fPHP\Forms\forestFormElement::RADIO) && ($o_formElement->getType() != \fPHP\Forms\forestFormElement::CHECKBOX) && ($o_formElement->getType() != \fPHP\Forms\forestFormElement::SELECT) && ($o_formElement->getType() != \fPHP\Forms\forestFormElement::LOOKUP) && ($o_formElement->getType() != \fPHP\Forms\forestFormElement::DESCRIPTION) ) {
				if (issetStr($this->TempFormObject->value->ClassAll)) {
					if (!issetStr($o_formElement->Class)) {
						$o_formElement->Class = $this->TempFormObject->value->ClassAll;
					}
				}
			}
			
			/* set required flag for all form elements */
			if ($this->TempFormObject->value->RequiredAll) {
				if ($o_formElement->getType() != \fPHP\Forms\forestFormElement::CHECKBOX) {
					$o_formElement->Required = true;
				}
			}
			
			/* set readonly flag for all form elements */
			if ($this->TempFormObject->value->ReadonlyAll) {
				if ( ($o_formElement->getType() != \fPHP\Forms\forestFormElement::SELECT) && ($o_formElement->getType() != \fPHP\Forms\forestFormElement::LOOKUP) && ($o_formElement->getType() != \fPHP\Forms\forestFormElement::COLOR) && ($o_formElement->getType() != \fPHP\Forms\forestFormElement::DROPZONE) && ($o_formElement->getType() != \fPHP\Forms\forestFormElement::RICHTEXT) && ($o_formElement->getType() != \fPHP\Forms\forestFormElement::DESCRIPTION) ) {
					$o_formElement->Readonly = true;
				}
				
				/* other elements do not have readonly flag, instead we are using disabled flag */
				if ( ($o_formElement->getType() == \fPHP\Forms\forestFormElement::RICHTEXT) || ($o_formElement->getType() == \fPHP\Forms\forestFormElement::RADIO) || ($o_formElement->getType() == \fPHP\Forms\forestFormElement::CHECKBOX) || ($o_formElement->getType() == \fPHP\Forms\forestFormElement::SELECT) || ($o_formElement->getType() == \fPHP\Forms\forestFormElement::LOOKUP) || ($o_formElement->getType() == \fPHP\Forms\forestFormElement::COLOR) || ( ($o_formElement->getType() == \fPHP\Forms\forestFormElement::BUTTON)  && (!$o_formElement->NoFormGroup) ) ) {
					$o_formElement->Disabled = true;
				}
			}
			
			/* overwrite Radio Classes if it is not set */
			if ($o_formElement->getType() == \fPHP\Forms\forestFormElement::RADIO) {
				if (issetStr($this->TempFormObject->value->RadioContainerClass)) {
					if (!issetStr($o_formElement->RadioContainerClass)) {
						$o_formElement->RadioContainerClass = $this->TempFormObject->value->RadioContainerClass;
					}
				}
				
				if (issetStr($this->TempFormObject->value->RadioClass)) {
					if (!issetStr($o_formElement->RadioClass)) {
						$o_formElement->RadioClass = $this->TempFormObject->value->RadioClass;
					}
				}
				
				if (issetStr($this->TempFormObject->value->RadioLabelClass)) {
					if (!issetStr($o_formElement->RadioLabelClass)) {
						$o_formElement->RadioLabelClass = $this->TempFormObject->value->RadioLabelClass;
					}
				}
			}
			
			/* overwrite Checkbox Classes if it is not set */
			if ($o_formElement->getType() == \fPHP\Forms\forestFormElement::CHECKBOX) {
				if (issetStr($this->TempFormObject->value->CheckboxContainerClass)) {
					if (!issetStr($o_formElement->CheckboxContainerClass)) {
						$o_formElement->CheckboxContainerClass = $this->TempFormObject->value->CheckboxContainerClass;
					}
				}
				
				if (issetStr($this->TempFormObject->value->CheckboxClass)) {
					if (!issetStr($o_formElement->CheckboxClass)) {
						$o_formElement->CheckboxClass = $this->TempFormObject->value->CheckboxClass;
					}
				}
				
				if (issetStr($this->TempFormObject->value->CheckboxLabelClass)) {
					if (!issetStr($o_formElement->CheckboxLabelClass)) {
						$o_formElement->CheckboxLabelClass = $this->TempFormObject->value->CheckboxLabelClass;
					}
				}
			}
			
			/* overwrite Select class if it is not set */
			if ( ($o_formElement->getType() == \fPHP\Forms\forestFormElement::SELECT) || ($o_formElement->getType() == \fPHP\Forms\forestFormElement::LOOKUP) ) {
				if (issetStr($this->TempFormObject->value->SelectClass)) {
					if (!issetStr($o_formElement->Class)) {
						$o_formElement->Class = $this->TempFormObject->value->SelectClass;
					}
				}
			}
			
			$p_s_foo .= strval($o_formElement);
		}
	}
	
	/**
	 * load forestFormTab class with settings from json object
	 *
	 * @param string $p_s_jsonDataSettings  json settings
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function loadJSON($p_s_jsonDataSettings) {
		\fPHP\Forms\forestFormElement::JSONSettingsMultilanguage($p_s_jsonDataSettings);
		$a_settings = json_decode($p_s_jsonDataSettings, true);
		
		if ($a_settings != null) {
			foreach (get_object_vars($this) as $s_key => $s_value) {
				if (array_key_exists($s_key, $a_settings)) {
					$this->{$s_key}->value = $a_settings[$s_key];
				}
			}
		} else {
			throw new forestException('Cannot decode json data. Please check json data settings for correct syntax.');
		}
	}
	
	/**
	 * check if all necessary settings for forestFormTab are set
	 *
	 * @param boool $p_b_extended  true - check tab class and tab active class, false - check only tab id and tab title
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function CheckIsset($p_b_extended = false) {
		if (!( issetStr($this->TabId->value) && issetStr($this->TabTitle->value) )) {
			throw new forestException('Not all necessary FormTab settings are set.');
		}
		
		if ($p_b_extended) {
			if (!( issetStr($this->TabClass->value) && issetStr($this->TabActiveClass->value) )) {
				throw new forestException('Not all necessary FormTab settings are set.');
			}
		}
	}
	
	/**
	 * get form element by form element id
	 *
	 * @param string $p_s_formId
	 *
	 * @return forestFormElement
	 *
	 * @access public
	 * @static no
	 */
	public function GetFormElementByFormId($p_s_formId) {
		$o_return = null;
		
		foreach ($this->FormElements->value as $s_key => $o_formElement) {
			if ($o_formElement->Id == $p_s_formId) {
				$o_return = $o_formElement;
				break;
			}
		}
		
		return $o_return;
	}

	/**
	 * delete form element by form element id
	 *
	 * @param string $p_s_formId
	 *
	 * @return bool  true - form element has been deleted, false - form element has been not found
	 *
	 * @access public
	 * @static no
	 */
	public function DeleteFormElementByFormId($p_s_formId) {
		$b_return = false;
		$s_listKey = null;
		
		foreach ($this->FormElements->value as $s_key => $o_formElement) {
			if ($o_formElement->Id == $p_s_formId) {
				$s_listKey = $s_key;
				$b_return = true;
				break;
			}
		}
		
		if ($b_return) {
			$this->FormElements->value->Del($s_key);
		}
		
		return $b_return;
	}
	
	/**
	 * add form element to form
	 *
	 * @param forestFormElement $p_o_formElement
	 * @param bool $p_b_first  true - add as first element, false - just add to form element list
	 *
	 * @return bool  true - form element has been added, false - form element has been not added
	 *
	 * @access public
	 * @static no
	 */
	public function AddFormElement(\fPHP\Forms\forestFormElement $p_o_formElement, $p_b_first = false) {
		if ($p_b_first) {
			$this->FormElements->value->AddFirst($p_o_formElement);
		} else {
			$this->FormElements->value->Add($p_o_formElement);
		}
		
		return true;
	}
}

class forestTabConfiguration {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	private $Tab;
	private $TabMenuClass;
	private $TabLiClass;
	private $TabAClass;
	private $TabActiveClass;
	private $TabToggle;
	private $TabContentClass;
	private $TabFooterClass;
	private $TabElementClass;
	private $TabElementActiveClass;
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * constructor of forestTabConfiguration class, holding all information
	 *
	 * @param parameters based on the forestTabConfiguration class
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */	
	public function __construct(
		$p_b_tab = false,
		$p_s_tabMenuClass = null,
		$p_s_tabLiClass = null,
		$p_s_tabAClass = null,
		$p_s_tabActiveClass = null,
		$p_s_tabToggle = null,
		$p_s_tabContentClass = null,
		$p_s_tabFooterClass = null,
		$p_s_tabElementClass = null,
		$p_s_tabElementActiveClass = null
	) {
		$this->Tab = new forestBool($p_b_tab);
		$this->TabMenuClass = new forestString;
		$this->TabLiClass = new forestString;
		$this->TabAClass = new forestString;
		$this->TabActiveClass = new forestString;
		$this->TabToggle = new forestString;
		$this->TabContentClass = new forestString;
		$this->TabFooterClass = new forestString;
		$this->TabElementClass = new forestString;
		$this->TabElementActiveClass = new forestString;
		
		if ($p_s_tabMenuClass != null) {
			$this->TabMenuClass->value = $p_s_tabMenuClass;
		}
		
		if ($p_s_tabLiClass != null) {
			$this->TabLiClass->value = $p_s_tabLiClass;
		}
		
		if ($p_s_tabAClass != null) {
			$this->TabAClass->value = $p_s_tabAClass;
		}
		
		if ($p_s_tabActiveClass != null) {
			$this->TabActiveClass->value = $p_s_tabActiveClass;
		}
		
		if ($p_s_tabToggle != null) {
			$this->TabToggle->value = $p_s_tabToggle;
		}
		
		if ($p_s_tabContentClass != null) {
			$this->TabContentClass->value = $p_s_tabContentClass;
		}
		
		if ($p_s_tabFooterClass != null) {
			$this->TabFooterClass->value = $p_s_tabFooterClass;
		}
		
		if ($p_s_tabElementClass != null) {
			$this->TabElementClass->value = $p_s_tabElementClass;
		}
		
		if ($p_s_tabElementActiveClass != null) {
			$this->TabElementActiveClass->value = $p_s_tabElementActiveClass;
		}
	}
	
	/**
	 * load forestTabConfiguration class with settings from json object
	 *
	 * @param string $p_s_jsonDataSettings  json settings
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function loadJSON($p_s_jsonDataSettings) {
		\fPHP\Forms\forestFormElement::JSONSettingsMultilanguage($p_s_jsonDataSettings);
		$a_settings = json_decode($p_s_jsonDataSettings, true);
		
		if ($a_settings != null) {
			foreach (get_object_vars($this) as $s_key => $s_value) {
				if (array_key_exists($s_key, $a_settings['FormTabConfiguration'])) {
					$this->{$s_key}->value = $a_settings['FormTabConfiguration'][$s_key];
				}
			}
		} else {
			throw new forestException('Cannot decode json data. Please check json data settings for correct syntax.');
		}
	}
	
	/**
	 * check if all necessary settings for forestTabConfiguration are set
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function CheckIsset() {
		if (!( issetStr($this->TabMenuClass->value) && issetStr($this->TabActiveClass->value) && issetStr($this->TabToggle->value) && issetStr($this->TabContentClass->value) && issetStr($this->TabFooterClass->value) && issetStr($this->TabElementClass->value) && issetStr($this->TabElementActiveClass->value) )) {
			throw new forestException('Not all necessary TabConfiguration settings are set.');
		}
	}
}

class forestModalConfiguration {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	private $Modal;
	private $ModalClass;
	private $ModalId;
	private $ModalTitle;
	private $ModalTitleClass;
	private $ModalRole;
	private $ModalDialogClass;
	private $ModalDialogContentClass;
	private $ModalHeaderClass;
	private $ModalHeaderCloseClass;
	private $ModalHeaderDismissClass;
	private $ModalHeaderCloseContent;
	private $ModalBodyClass;
	private $ModalFooterClass;
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * constructor of forestModalConfiguration class, holding all information
	 *
	 * @param parameters based on the forestModalConfiguration class
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */	
	public function __construct(
		$p_b_modal = false,
		$p_s_modalClass = null,
		$p_s_modalId = null,
		$p_s_modalTitle = null,
		$p_s_modalTitleClass = null,
		$p_s_modalRole = null,
		$p_s_modalDialogClass = null,
		$p_s_modalDialogContentClass = null,
		$p_s_modalHeaderClass = null,
		$p_s_modalHeaderCloseClass = null,
		$p_s_modalHeaderDismissClass = null,
		$p_s_modalHeaderCloseContent = null,
		$p_s_modalBodyClass = null,
		$p_s_modalFooterClass = null
	) {
		$this->Modal = new forestBool($p_b_modal);
		$this->ModalClass = new forestString;
		$this->ModalId = new forestString;
		$this->ModalTitle = new forestString;
		$this->ModalTitleClass = new forestString;
		$this->ModalRole = new forestString;
		$this->ModalDialogClass = new forestString;
		$this->ModalDialogContentClass = new forestString;
		$this->ModalHeaderClass = new forestString;
		$this->ModalHeaderCloseClass = new forestString;
		$this->ModalHeaderDismissClass = new forestString;
		$this->ModalHeaderCloseContent = new forestString;
		$this->ModalBodyClass = new forestString;
		$this->ModalFooterClass = new forestString;
		
		if ($p_s_modalClass != null) {
			$this->ModalClass->value = $p_s_modalClass;
		}
		
		if ($p_s_modalId != null) {
			$this->ModalId->value = $p_s_modalId;
		}
		
		if ($p_s_modalTitle != null) {
			$this->ModalTitle->value = $p_s_modalTitle;
		}
		
		if ($p_s_modalTitleClass != null) {
			$this->ModalTitleClass->value = $p_s_modalTitleClass;
		}
		
		if ($p_s_modalRole != null) {
			$this->ModalRole->value = $p_s_modalRole;
		}
		
		if ($p_s_modalDialogClass != null) {
			$this->ModalDialogClass->value = $p_s_modalDialogClass;
		}
		
		if ($p_s_modalDialogContentClass != null) {
			$this->ModalDialogContentClass->value = $p_s_modalDialogContentClass;
		}
		
		if ($p_s_modalHeaderClass != null) {
			$this->ModalHeaderClass->value = $p_s_modalHeaderClass;
		}
		
		if ($p_s_modalHeaderCloseClass != null) {
			$this->ModalHeaderCloseClass->value = $p_s_modalHeaderCloseClass;
		}
		
		if ($p_s_modalHeaderDismissClass != null) {
			$this->ModalHeaderDismissClass->value = $p_s_modalHeaderDismissClass;
		}
		
		if ($p_s_modalHeaderCloseContent != null) {
			$this->ModalHeaderCloseContent->value = $p_s_modalHeaderCloseContent;
		}
		
		if ($p_s_modalBodyClass != null) {
			$this->ModalBodyClass->value = $p_s_modalBodyClass;
		}
		
		if ($p_s_modalFooterClass != null) {
			$this->ModalFooterClass->value = $p_s_modalFooterClass;
		}
	}
	
	/**
	 * load forestModalConfiguration class with settings from json object
	 *
	 * @param string $p_s_jsonDataSettings  json settings
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function loadJSON($p_s_jsonDataSettings) {
		\fPHP\Forms\forestFormElement::JSONSettingsMultilanguage($p_s_jsonDataSettings);
		$a_settings = json_decode($p_s_jsonDataSettings, true);
		
		if ($a_settings != null) {
			foreach (get_object_vars($this) as $s_key => $s_value) {
				if (array_key_exists($s_key, $a_settings['FormModalConfiguration'])) {
					$this->{$s_key}->value = $a_settings['FormModalConfiguration'][$s_key];
				}
			}
		} else {
			throw new forestException('Cannot decode json data. Please check json data settings for correct syntax.');
		}
	}
	
	/**
	 * check if all necessary settings for forestModalConfiguration are set
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function CheckIsset() {
		if (!( 
			issetStr($this->ModalClass->value) && 
			issetStr($this->ModalId->value) && 
			issetStr($this->ModalTitle->value) && 
			issetStr($this->ModalTitleClass->value) && 
			issetStr($this->ModalDialogClass->value) && 
			issetStr($this->ModalDialogContentClass->value) && 
			issetStr($this->ModalHeaderClass->value) && 
			issetStr($this->ModalHeaderCloseClass->value) && 
			issetStr($this->ModalHeaderDismissClass->value) && 
			issetStr($this->ModalBodyClass->value) && 
			issetStr($this->ModalFooterClass->value)
		)) {
			throw new forestException('Not all necessary ModalConfiguration settings are set. [' . $this->ModalId->value . ']');
		}
	}
}

?>