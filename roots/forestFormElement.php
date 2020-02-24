<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.8.0 (0x1 00016)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * class collection for rendering all usable html form elements
 * all necessary properties can be changed and will be considered in the __toString-methods
 * its a full collection of these properties
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.1 alpha	renatus		2019-08-12	added to framework
 * 0.1.3 alpha	renatus		2019-09-06	added validationrules
 * 0.1.4 alpha	renatus		2019-09-23	added file, dropzone and richtext
 * 0.1.5 alpha	renatus		2019-10-04	added forestLookup and Captcha
 * 0.5.0 beta	renatus		2019-12-04	added auto checkin element
 * 0.7.0 beta	renatus		2020-01-03	added mondey-format property to general input attributes
 */

class forestFormElement {
	use forestData;
	
	/* Fields */
	
	const FORM = 'form';
	
	const TEXT = 'text';
	const HIDDEN = 'hidden';
	const PASSWORD = 'password';
	const LIST = 'list';
	const FILE = 'file';
	const RADIO = 'radio';
	const CHECKBOX = 'checkbox';
	const COLOR = 'color';
	const EMAIL = 'email';
	const URL = 'url';
	const DATE = 'date';
	const DATETIMELOCAL = 'datetime-local';
	const MONTH = 'month';
	const NUMBER = 'number';
	const RANGE = 'range';
	const SEARCH = 'search';
	const PHONE = 'phone';
	const TIME = 'time';
	const WEEK = 'week';
	
	const TEXTAREA = 'textarea';
	const SELECT = 'select';
	const LOOKUP = 'lookup';
	const RICHTEXT = 'richtext';
	const DROPZONE = 'dropzone';
	const DESCRIPTION = 'description';
	const BUTTON = 'button';
	const CAPTCHA = 'captcha';
	const AUTOCHECKIN = 'autocheckin';
	const FIELDSET = 'fieldset';
	
	private $Type;
	private $FormElement;
	
	/* Properties */
	
	public function getType() {
		return $this->Type->value;
	}
	
	public function getFormElement() {
		return $this->FormElement->value;
	}
	
	/* Methods */
	
	public function __construct($p_s_type) {
		$this->Type = new forestString($p_s_type, false);
		$this->FormElement = new forestObject('forestFormGeneralAttributes', false);
		
		switch ($this->Type->value) {
			case self::FORM:
				$this->Type->value = self::FORM;
				$this->FormElement->value = new forestFormObject();
			break;
			
			case self::TEXT:
				$this->Type->value = self::TEXT;
				$this->FormElement->value = new forestFormElementText();
			break;
			case self::HIDDEN:
				$this->Type->value = self::HIDDEN;
				$this->FormElement->value = new forestFormElementHidden();
			break;
			case self::PASSWORD:
				$this->Type->value = self::PASSWORD;
				$this->FormElement->value = new forestFormElementPassword();
			break;
			case self::LIST:
				$this->Type->value = self::LIST;
				$this->FormElement->value = new forestFormElementList();
			break;
			case self::FILE:
				$this->Type->value = self::FILE;
				$this->FormElement->value = new forestFormElementFile();
			break;
			case self::RADIO:
				$this->Type->value = self::RADIO;
				$this->FormElement->value = new forestFormElementRadio();
			break;
			case self::CHECKBOX:
				$this->Type->value = self::CHECKBOX;
				$this->FormElement->value = new forestFormElementCheckbox();
			break;
			case self::COLOR:
				$this->Type->value = self::COLOR;
				$this->FormElement->value = new forestFormElementColor();
			break;
			case self::EMAIL:
				$this->Type->value = self::EMAIL;
				$this->FormElement->value = new forestFormElementEmail();
			break;
			case self::URL:
				$this->Type->value = self::URL;
				$this->FormElement->value = new forestFormElementUrl();
			break;
			case self::DATE:
				$this->Type->value = self::DATE;
				$this->FormElement->value = new forestFormElementDate();
			break;
			case self::DATETIMELOCAL:
				$this->Type->value = self::DATETIMELOCAL;
				$this->FormElement->value = new forestFormElementDateTimeLocal();
			break;
			case self::MONTH:
				$this->Type->value = self::MONTH;
				$this->FormElement->value = new forestFormElementMonth();
			break;
			case self::NUMBER:
				$this->Type->value = self::NUMBER;
				$this->FormElement->value = new forestFormElementNumber();
			break;
			case self::RANGE:
				$this->Type->value = self::RANGE;
				$this->FormElement->value = new forestFormElementRange();
			break;
			case self::SEARCH:
				$this->Type->value = self::SEARCH;
				$this->FormElement->value = new forestFormElementSearch();
			break;
			case self::PHONE:
				$this->Type->value = self::PHONE;
				$this->FormElement->value = new forestFormElementPhone();
			break;
			case self::TIME:
				$this->Type->value = self::TIME;
				$this->FormElement->value = new forestFormElementTime();
			break;
			case self::WEEK:
				$this->Type->value = self::WEEK;
				$this->FormElement->value = new forestFormElementWeek();
			break;
			
			case self::TEXTAREA:
				$this->Type->value = self::TEXTAREA;
				$this->FormElement->value = new forestFormElementTextArea();
			break;
			case self::SELECT:
				$this->Type->value = self::SELECT;
				$this->FormElement->value = new forestFormElementSelect();
			break;
			case self::LOOKUP:
				$this->Type->value = self::LOOKUP;
				$this->FormElement->value = new forestFormElementSelect();
			break;
			case self::RICHTEXT:
				$this->Type->value = self::RICHTEXT;
				$this->FormElement->value = new forestFormElementRichtext();
			break;
			case self::DROPZONE:
				$this->Type->value = self::DROPZONE;
				$this->FormElement->value = new forestFormElementDropzone();
			break;
			case self::DESCRIPTION:
				$this->Type->value = self::DESCRIPTION;
				$this->FormElement->value = new forestFormElementDescription();
			break;
			case self::BUTTON:
				$this->Type->value = self::BUTTON;
				$this->FormElement->value = new forestFormElementButton();
			break;
			case self::CAPTCHA:
				$this->Type->value = self::CAPTCHA;
				$this->FormElement->value = new forestFormElementCaptcha();
			break;
			case self::AUTOCHECKIN:
				$this->Type->value = self::AUTOCHECKIN;
				$this->FormElement->value = new forestFormElementRadio();
			break;
			case self::FIELDSET:
				$this->Type->value = self::FIELDSET;
				$this->FormElement->value = new forestFormElementFieldset();
			break;
			default:
				throw new forestException('Invalid form element type[%0]', array($this->Type->value));
			break;
		}
	}
	
	public function __toString() {
		return strval($this->FormElement->value);
	}
	
	public function &__get($p_s_name) {
		return $this->FormElement->value->$p_s_name;
	}
	
	public function __set($p_s_name, $p_o_value) {
		$this->FormElement->value->$p_s_name = $p_o_value;
	}
	
	public function loadJSON($p_s_jsonDataSettings, $p_i_branchId = null) {
		$o_glob = forestGlobals::init();
		
		forestFormElement::JSONSettingsMultilanguage($p_s_jsonDataSettings, $p_i_branchId);
		$a_settings = json_decode($p_s_jsonDataSettings, true);
		
		if ($a_settings != null) {
			foreach ($this->FormElement->value->getObjectVars() as $s_key => $s_value) {
				if (array_key_exists($s_key, $a_settings)) {
					$this->{$s_key} = $a_settings[$s_key];
				}
			}
		} else {
			throw new forestException('Cannot decode json data. Please check json data settings for correct syntax.[%0]', array($p_s_jsonDataSettings));
		}
	}
	
	public static function JSONSettingsMultilanguage(&$p_s_jsonDataSettings, $p_i_branchId = null) {
		$o_glob = forestGlobals::init();
		preg_match_all('/\#([^#]+)\#/', $p_s_jsonDataSettings, $a_matches);
		
		if (count($a_matches) > 1) {
			foreach ($a_matches[1] as $s_match) {
				if (strpos($s_match, '.') !== false) {
					$a_match = explode('.', $s_match);
					$s_translation = $o_glob->GetTranslation($a_match[1], intval($a_match[0]));
				} else {
					if ($p_i_branchId != null) {
						$s_translation = $o_glob->GetTranslation($s_match, $p_i_branchId);
					} else {
						$s_translation = $o_glob->GetTranslation($s_match);
					}
				}
				
				$p_s_jsonDataSettings = str_replace('#' . $s_match . '#', $s_translation, $p_s_jsonDataSettings);
			}
		}
		
		$p_s_jsonDataSettings = str_replace('&quot;', '"', $p_s_jsonDataSettings);
	}
}


abstract class forestFormGeneralAttributes {
	use forestData;
	
	/* Fields */
	
	protected $FormGroupClass;
	protected $Label;
	protected $LabelClass;
	protected $LabelFor;
	protected $FormElementClass;
	
	protected $Class;
	protected $Description;
	protected $DescriptionClass;
	protected $Disabled;
	protected $Id;
	protected $MoneyFormat;
	protected $Name;
	protected $AutoFocus;
	protected $Required;
	protected $Style;
	protected $Value;
	protected $ValMessage;
	
	/* Properties */
	
	/* Methods */
	 
	public function __construct() {
		$this->FormGroupClass = new forestString;
		$this->Label = new forestString;
		$this->LabelClass = new forestString;
		$this->LabelFor = new forestString;
		$this->FormElementClass = new forestString;
		
		$this->Class = new forestString;
		$this->Description = new forestString;
		$this->DescriptionClass = new forestString;
		$this->Disabled = new forestBool;
		$this->Id = new forestString;
		$this->MoneyFormat = new forestBool;
		$this->Name = new forestString;
		$this->AutoFocus = new forestBool;
		$this->Required = new forestBool;
		$this->Style = new forestString;
		$this->Value = new forestString;
		$this->ValMessage = new forestString;
	}
	
	public function __toString() {
		if ( (get_called_class() != 'forestFormElementRadio') && (get_called_class() != 'forestFormElementCheckbox') ) {
			$this->LabelFor->value = $this->Id->value;
		}
		
		if (issetStr($this->FormGroupClass->value)) {
			$s_foo = '<div class="' . $this->FormGroupClass->value . '">';
			
			$s_foo .= '<label';
			
			if (issetStr($this->LabelClass->value)) {
				$s_foo .= ' class="' . $this->LabelClass->value . '"';
			}
			
			if (issetStr($this->LabelFor->value)) {
				$s_foo .= ' for="' . $this->LabelFor->value . '"';
			}
			
			$s_foo .= '>';

			if (issetStr($this->Label->value)) {
				$s_foo .= $this->Label->value;
			}
			
			$s_foo .= '</label>' . "\n";
			
			if (issetStr($this->FormElementClass->value)) {
				$s_foo .= '<div class="' . $this->FormElementClass->value .  '">';
			}
			
			return $s_foo;
		} else {
			return '';
		}
	}
}

abstract class forestFormInputAttributes extends forestFormGeneralAttributes {

	/* Fields */
	
	protected $Accept;
	protected $AutoComplete;
	protected $Capture;
	protected $Dirname;
	protected $List;
	protected $Max;
	protected $Min;
	protected $Multiple;
	protected $Options;
	protected $Pattern;
	protected $PatternTitle;
	protected $Placeholder;
	protected $Readonly;
	protected $Size;
	protected $Step;
	
	protected $Form;
	protected $FormAction;
	protected $FormEnctype;
	protected $FormMethod;
	protected $FormTarget;
	protected $FormNoValidate;
		
	/* Properties */
	
	/* Methods */
	
	public function __construct() {
		parent::__construct();
		
		$this->Accept = new forestString;
		$this->AutoComplete = new forestBool(true);
		$this->Capture = new forestString;
		$this->Dirname = new forestString;
		$this->List = new forestString;
		$this->Max = new forestString;
		$this->Min = new forestString;
		$this->Multiple = new forestBool;
		$this->Options = new forestArray;
		$this->Pattern = new forestString;
		$this->PatternTitle = new forestString;
		$this->Placeholder = new forestString;
		$this->Readonly = new forestBool;
		$this->Size = new forestInt;
		$this->Step = new forestInt;
		
		$this->Form = new forestString;
		$this->FormAction = new forestString;
		$this->FormEnctype = new forestList(array('application/x-www-form-urlencoded', 'multipart/form-data', 'text/plain'));
		$this->FormMethod = new forestList(array('GET', 'POST'));
		$this->FormTarget = new forestList(array('_blank', '_self', '_parent', '_top'));
		$this->FormNoValidate = new forestBool;
	}
	
	public function __toString() {
		return parent::__toString();
	}
}

class forestFormValidationRule {
	use forestData;
	
	/* Fields */
	
	private $FormElementId;
	private $Rule;
	private $RuleParam01;
	private $RuleParam02;
	private $AutoRequired;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct($p_s_formElementId, $p_s_rule, $p_s_ruleParam01, $p_s_ruleParam02 = null, $p_s_autoRequired = 'true') {
		$this->FormElementId = new forestString;
		$this->Rule = new forestString;
		$this->RuleParam01 = new forestString;
		$this->RuleParam02 = new forestString;
		$this->AutoRequired = new forestString;
		
		$this->FormElementId->value = $p_s_formElementId;
		$this->Rule->value = $p_s_rule;
		$this->RuleParam01->value = $p_s_ruleParam01;
		
		if ($p_s_ruleParam02 == null) {
			$this->RuleParam02->value = '';
		} else {
			$this->RuleParam02->value = $p_s_ruleParam02;
		}
		
		if ($p_s_autoRequired == 'true') {
			$this->AutoRequired->value = 'true';
		} else {
			$this->AutoRequired->value = 'false';
		}
	}
}

class forestFormObject extends forestFormGeneralAttributes {
	use forestData;
	
	/* Fields */
	
	private $Action;
	private $Enctype;
	private $Method;
	private $Target;
	private $NoValidate;
	private $AutoComplete;
	private $Role;
	
	private $ClassAll;
	private $RadioClass;
	private $CheckboxClass;
	private $RequiredAll;
	private $ReadonlyAll;
	
	private $ValRequiredMessage;
	private $ValRules;
	
	private $UseCaptcha;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct() {
		parent::__construct();
		
		$o_glob = forestGlobals::init();
		
		$this->Id->value = $o_glob->URL->Branch . $o_glob->URL->Action . 'Form';
		$this->Name->value = $o_glob->URL->Branch . $o_glob->URL->Action . 'Form';
		
		$this->Action = new forestString(forestLink::Link($o_glob->URL->Branch, $o_glob->URL->Action, $o_glob->URL->Parameters));
		$this->AutoComplete = new forestBool(true);
		$this->Enctype = new forestList(array('application/x-www-form-urlencoded', 'multipart/form-data', 'text/plain'), 'application/x-www-form-urlencoded');
		$this->Method = new forestList(array('GET', 'POST'), 'POST');
		$this->Target = new forestList(array('_blank', '_self', '_parent', '_top'), '_self');
		$this->NoValidate = new forestBool(true);
		$this->Role = new forestString('form');
		
		$this->ClassAll = new forestString;
		$this->RadioClass = new forestString;
		$this->CheckboxClass = new forestString;
		$this->RequiredAll = new forestBool;
		$this->ReadonlyAll = new forestBool;
		
		$this->ValRequiredMessage = new forestString;
		$this->ValRules = new forestObject(new forestObjectList('forestFormValidationRule'), false);
		
		$this->UseCaptcha = new forestBool;
	}
	
	public function getObjectVars() {
		return get_object_vars($this);
	}
	
	function __toString() {
		$o_glob = forestGlobals::init();
		
		$s_foo = '<form';
		
		if (issetStr($this->Id->value)) {
			$s_foo.= ' id="' . $this->Id->value . '"';
		}
		
		if (issetStr($this->Name->value)) {
			$s_foo.= ' name="' . $this->Name->value . '"';
		}
		
		if (issetStr($this->Class->value)) {
			$s_foo.= ' class="' . $this->Class->value . '"';
		}
		
		if (issetStr($this->Style->value)) {
			$s_foo.= ' style="' . $this->Style->value . '"';
		}
		
		if (issetStr($this->Action->value)) {
			$s_foo.= ' action="' . $this->Action->value . '"';
		}
		
		if (!$this->AutoComplete->value) {
			$s_foo.= ' autocomplete="off"';
		}
		
		if (issetStr($this->Enctype->value) && ($this->Enctype->value != 'application/x-www-form-urlencoded')) {
			$s_foo.= ' enctype="' . $this->Enctype->value . '"';
		}
		
		if (issetStr($this->Method->value)) {
			$s_foo.= ' method="' . $this->Method->value . '"';
		}
		
		if (issetStr($this->Target->value) && ($this->Target->value != '_self')) {
			$s_foo.= ' target="' . $this->Target->value . '"';
		}
		
		if (issetStr($this->Role->value)) {
			$s_foo.= ' role="' . $this->Role->value . '"';
		}
		
		if ($this->NoValidate->value) {
			$s_foo.= ' novalidate';
		}
		
		$s_foo .= '>' . "\n";
		
		return $s_foo;
	}
}


class forestFormElementText extends forestFormInputAttributes {
	use forestData;
	
	/* Fields */
	
	private $DateIntervalFormat;
	private $NoDisplay;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct() {
		parent::__construct();
		
		$this->DateIntervalFormat = new forestBool;
		$this->NoDisplay = new forestBool;
	}
	
	public function getObjectVars() {
		return get_object_vars($this);
	}
		
	public function __toString() {
		$o_glob = forestGlobals::init();
		
		if (!issetStr($this->Name->value)) {
			$this->Name->value = $this->Id->value;
		}
		
		if (!$this->NoDisplay->value) {
			$s_foo = parent::__toString();
			$s_foo .= '<input type="text" id="' . $this->Id->value . '" name="' . $this->Name->value . '" class="' . $this->Class->value . '"';
			
			if (issetStr($this->Style->value)) {
				$s_foo .= ' style="' . $this->Style->value . '"';
			}
			
			if (issetStr($this->Placeholder->value)) {
				$s_foo .= ' placeholder="' . $this->Placeholder->value . '"';
			}
			
			if (issetStr($this->Value->value)) {
				$s_foo .= ' value="' . $this->Value->value . '"';
			}
			
			if ($this->Size->value > 0) {
				$s_foo .= ' size="' . $this->Size->value . '"';
			}
			
			if (!($this->AutoComplete->value)) {
				$s_foo .= ' autocomplete="off"';
			}
			
			if (issetStr($this->Dirname->value)) {
				$s_foo .= ' dirname="' . $this->Dirname->value . '"';
			}
			
			if (issetStr($this->Pattern->value)) {
				$s_foo .= ' pattern="' . $this->Pattern->value . '"';
			}
			
			if (issetStr($this->PatternTitle->value)) {
				$s_foo .= ' title="' . $this->PatternTitle->value . '"';
			}
			
			if (issetStr($this->ValMessage->value)) {
				$s_foo .= ' data-valmessage="' . $this->ValMessage->value . '"';
			}
			
			
			if (issetStr($this->Form->value)) {
				$s_foo .= ' form="' . $this->Form->value . '"';
			}
			
			if (issetStr($this->FormAction->value)) {
				$s_foo .= ' formaction="' . $this->FormAction->value . '"';
			}
			
			if (issetStr($this->FormEnctype->value)) {
				$s_foo .= ' formenctype="' . $this->FormEnctype->value . '"';
			}
			
			if (issetStr($this->FormMethod->value)) {
				$s_foo .= ' formmethod="' . $this->FormMethod->value . '"';
			}
			
			if ($this->FormNoValidate->value) {
				$s_foo .= ' formnovalidate="formnovalidate"';
			}
			
			if (issetStr($this->FormTarget->value)) {
				$s_foo .= ' formtarget="' . $this->FormTarget->value . '"';
			}
			
			
			$s_foo .= ' tabindex="' . $o_glob->GetTabIndex() . '"';
			
			if ($this->AutoFocus->value) {
				$s_foo .= ' autofocus';
			}
			
			if ($this->Required->value) {
				$s_foo .= ' required';
			}
			
			if ($this->Readonly->value) {
				$s_foo .= ' readonly';
			}
			
			if ($this->Disabled->value) {
				$s_foo .= ' disabled';
			}
			
			$s_foo .= '>' . "\n";
			
			if (issetStr($this->Description->value)) {
				$s_foo .= '<div';
				
				if (issetStr($this->DescriptionClass->value)) {
					$s_foo .= ' class="' . $this->DescriptionClass->value . '"';
				}
				
				$s_foo .= '><small>' . $this->Description->value . '</small></div>' . "\n";
			}
		} else {
			$s_foo = '<input type="text" id="' . $this->Id->value . '" name="' . $this->Name->value . '" value="" style="display:none !important" tabindex="-1" autocomplete="off">';
		}
		
		return forestStringLib::closeHTMLTags($s_foo);
	}
}

class forestFormElementList extends forestFormInputAttributes {
	use forestData;
	
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getObjectVars() {
		return get_object_vars($this);
	}
	
	public function __toString() {
		$o_glob = forestGlobals::init();
		
		if (!issetStr($this->Name->value)) {
			$this->Name->value = $this->Id->value;
		}
		
		$s_foo = parent::__toString();
		
		$s_foo .= '<input list="' . $this->List->value . '" id="' . $this->Id->value . '" name="' . $this->Name->value . '" class="' . $this->Class->value . '"';
		
		if (issetStr($this->Style->value)) {
			$s_foo .= ' style="' . $this->Style->value . '"';
		}
		
		if (issetStr($this->Placeholder->value)) {
			$s_foo .= ' placeholder="' . $this->Placeholder->value . '"';
		}
		
		if (issetStr($this->Value->value)) {
			$s_foo .= ' value="' . $this->Value->value . '"';
		}
		
		if ($this->Size->value > 0) {
			$s_foo .= ' size="' . $this->Size->value . '"';
		}
		
		if (!($this->AutoComplete->value)) {
			$s_foo .= ' autocomplete="off"';
		}
		
		if (issetStr($this->Dirname->value)) {
			$s_foo .= ' dirname="' . $this->Dirname->value . '"';
		}
		
		if (issetStr($this->ValMessage->value)) {
			$s_foo .= ' data-valmessage="' . $this->ValMessage->value . '"';
		}
		
		
		if (issetStr($this->Form->value)) {
			$s_foo .= ' form="' . $this->Form->value . '"';
		}
		
		if (issetStr($this->FormAction->value)) {
			$s_foo .= ' formaction="' . $this->FormAction->value . '"';
		}
		
		if (issetStr($this->FormEnctype->value)) {
			$s_foo .= ' formenctype="' . $this->FormEnctype->value . '"';
		}
		
		if (issetStr($this->FormMethod->value)) {
			$s_foo .= ' formmethod="' . $this->FormMethod->value . '"';
		}
		
		if ($this->FormNoValidate->value) {
			$s_foo .= ' formnovalidate="formnovalidate"';
		}
		
		if (issetStr($this->FormTarget->value)) {
			$s_foo .= ' formtarget="' . $this->FormTarget->value . '"';
		}
		
		
		$s_foo .= ' tabindex="' . $o_glob->GetTabIndex() . '"';
		
		if ($this->AutoFocus->value) {
			$s_foo .= ' autofocus';
		}
		
		if ($this->Required->value) {
			$s_foo .= ' required';
		}
		
		if ($this->Readonly->value) {
			$s_foo .= ' readonly';
		}
		
		if ($this->Disabled->value) {
			$s_foo .= ' disabled';
		}
		
		$s_foo .= '>' . "\n";
		
		if (count($this->Options->value) > 0) {
			$s_foo .= '<datalist id="' . $this->List->value . '">' . "\n";
			
			foreach ($this->Options->value as $s_option_label => $s_option_value) {
				$s_foo .= '<option value="' . $s_option_value . '">' . $s_option_label . '</option>';
			}
			
			$s_foo .= '</datalist>' . "\n";
		}
		
		if (issetStr($this->Description->value)) {
			$s_foo .= '<div';
			
			if (issetStr($this->DescriptionClass->value)) {
				$s_foo .= ' class="' . $this->DescriptionClass->value . '"';
			}
			
			$s_foo .= '><small>' . $this->Description->value . '</small></div>' . "\n";
		}
		
		return forestStringLib::closeHTMLTags($s_foo);
	}
}

class forestFormElementHidden extends forestFormInputAttributes {
	use forestData;
	
	/* Fields */
	
	private $NoFormGroup;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct() {
		parent::__construct();
		
		$this->NoFormGroup = new forestBool(true);
	}
	
	public function getObjectVars() {
		return get_object_vars($this);
	}
	
	public function __toString() {
		$o_glob = forestGlobals::init();
		
		if (!issetStr($this->Name->value)) {
			$this->Name->value = $this->Id->value;
		}
		
		if (!$this->NoFormGroup->value) {
			$s_foo = parent::__toString();
		} else {
			$s_foo = '';
		}
		
		$s_foo .= '<input type="hidden" id="' . $this->Id->value . '" name="' . $this->Name->value . '" value="' . $this->Value->value . '"';
		
		
		if (issetStr($this->Form->value)) {
			$s_foo .= ' form="' . $this->Form->value . '"';
		}
		
		if (issetStr($this->FormAction->value)) {
			$s_foo .= ' formaction="' . $this->FormAction->value . '"';
		}
		
		if (issetStr($this->FormEnctype->value)) {
			$s_foo .= ' formenctype="' . $this->FormEnctype->value . '"';
		}
		
		if (issetStr($this->FormMethod->value)) {
			$s_foo .= ' formmethod="' . $this->FormMethod->value . '"';
		}
		
		if ($this->FormNoValidate->value) {
			$s_foo .= ' formnovalidate="formnovalidate"';
		}
		
		if (issetStr($this->FormTarget->value)) {
			$s_foo .= ' formtarget="' . $this->FormTarget->value . '"';
		}
		
		
		$s_foo .= '>' . "\n";
		
		return forestStringLib::closeHTMLTags($s_foo);
	}
}

class forestFormElementPassword extends forestFormInputAttributes {
	use forestData;
	
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getObjectVars() {
		return get_object_vars($this);
	}
		
	public function __toString() {
		$o_glob = forestGlobals::init();
		
		if (!issetStr($this->Name->value)) {
			$this->Name->value = $this->Id->value;
		}
		
		$s_foo = parent::__toString();
		
		$s_foo .= '<input type="password" id="' . $this->Id->value . '" name="' . $this->Name->value . '" class="' . $this->Class->value . '"';
		
		if (issetStr($this->Style->value)) {
			$s_foo .= ' style="' . $this->Style->value . '"';
		}
		
		if (issetStr($this->Placeholder->value)) {
			$s_foo .= ' placeholder="' . $this->Placeholder->value . '"';
		}
		
		if ($this->Size->value > 0) {
			$s_foo .= ' size="' . $this->Size->value . '"';
		}
		
		if (!($this->AutoComplete->value)) {
			$s_foo .= ' autocomplete="off"';
		}
		
		if (issetStr($this->Dirname->value)) {
			$s_foo .= ' dirname="' . $this->Dirname->value . '"';
		}
		
		if (issetStr($this->ValMessage->value)) {
			$s_foo .= ' data-valmessage="' . $this->ValMessage->value . '"';
		}
		
		
		if (issetStr($this->Form->value)) {
			$s_foo .= ' form="' . $this->Form->value . '"';
		}
		
		if (issetStr($this->FormAction->value)) {
			$s_foo .= ' formaction="' . $this->FormAction->value . '"';
		}
		
		if (issetStr($this->FormEnctype->value)) {
			$s_foo .= ' formenctype="' . $this->FormEnctype->value . '"';
		}
		
		if (issetStr($this->FormMethod->value)) {
			$s_foo .= ' formmethod="' . $this->FormMethod->value . '"';
		}
		
		if ($this->FormNoValidate->value) {
			$s_foo .= ' formnovalidate="formnovalidate"';
		}
		
		if (issetStr($this->FormTarget->value)) {
			$s_foo .= ' formtarget="' . $this->FormTarget->value . '"';
		}
		
		
		$s_foo .= ' tabindex="' . $o_glob->GetTabIndex() . '"';
		
		if ($this->AutoFocus->value) {
			$s_foo .= ' autofocus';
		}
		
		if ($this->Required->value) {
			$s_foo .= ' required';
		}
		
		if ($this->Readonly->value) {
			$s_foo .= ' readonly';
		}
		
		if ($this->Disabled->value) {
			$s_foo .= ' disabled';
		}
		
		$s_foo .= '>' . "\n";
		
		if (issetStr($this->Description->value)) {
			$s_foo .= '<div';
			
			if (issetStr($this->DescriptionClass->value)) {
				$s_foo .= ' class="' . $this->DescriptionClass->value . '"';
			}
			
			$s_foo .= '><small>' . $this->Description->value . '</small></div>' . "\n";
		}
		
		return forestStringLib::closeHTMLTags($s_foo);
	}
}

class forestFormElementFile extends forestFormInputAttributes {
	use forestData;
	
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getObjectVars() {
		return get_object_vars($this);
	}
	
	public function __toString() {
		$o_glob = forestGlobals::init();
		
		if (!issetStr($this->Name->value)) {
			$this->Name->value = $this->Id->value;
		}
		
		$s_foo = parent::__toString();
		
		$s_foo .= '<input type="file" id="' . $this->Id->value . '" name="' . $this->Name->value . '" class="' . $this->Class->value . '"';
		
		if (issetStr($this->Style->value)) {
			$s_foo .= ' style="' . $this->Style->value . '"';
		}
		
		if (issetStr($this->Accept->value)) {
			$s_foo .= ' accept="' . $this->Accept->value . '"';
		}
		
		if (issetStr($this->Capture->value)) {
			$s_foo .= ' capture="' . $this->Capture->value . '"';
		}
		
		if (issetStr($this->ValMessage->value)) {
			$s_foo .= ' data-valmessage="' . $this->ValMessage->value . '"';
		}
		
		
		if (issetStr($this->Form->value)) {
			$s_foo .= ' form="' . $this->Form->value . '"';
		}
		
		if (issetStr($this->FormAction->value)) {
			$s_foo .= ' formaction="' . $this->FormAction->value . '"';
		}
		
		if (issetStr($this->FormEnctype->value)) {
			$s_foo .= ' formenctype="' . $this->FormEnctype->value . '"';
		}
		
		if (issetStr($this->FormMethod->value)) {
			$s_foo .= ' formmethod="' . $this->FormMethod->value . '"';
		}
		
		if ($this->FormNoValidate->value) {
			$s_foo .= ' formnovalidate="formnovalidate"';
		}
		
		if (issetStr($this->FormTarget->value)) {
			$s_foo .= ' formtarget="' . $this->FormTarget->value . '"';
		}
		
		
		$s_foo .= ' tabindex="' . $o_glob->GetTabIndex() . '"';
		
		if ($this->AutoFocus->value) {
			$s_foo .= ' autofocus';
		}
		
		if ($this->Required->value) {
			$s_foo .= ' required';
		}
		
		if ($this->Disabled->value) {
			$s_foo .= ' disabled';
		}
		
		$s_foo .= '>' . "\n";
		
		if (issetStr($this->Description->value)) {
			$s_foo .= '<div';
			
			if (issetStr($this->DescriptionClass->value)) {
				$s_foo .= ' class="' . $this->DescriptionClass->value . '"';
			}
			
			$s_foo .= '><small>' . $this->Description->value . '</small></div>' . "\n";
		}
		
		return forestStringLib::closeHTMLTags($s_foo);
	}
}

class forestFormElementRadio extends forestFormInputAttributes {
	use forestData;
	
	/* Fields */
	
	private $Break;
	private $RadioClass;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct() {
		parent::__construct();
		$this->Break = new forestBool(true);
		$this->RadioClass = new forestString;
	}
	
	public function getObjectVars() {
		return get_object_vars($this);
	}
	
	public function __toString() {
		$o_glob = forestGlobals::init();
		
		if (!issetStr($this->Name->value)) {
			$this->Name->value = $this->Id->value;
			
			if (forestStringLib::EndsWith($this->Id->value, '[]')) {
				$this->Id->value = substr($this->Id->value, 0, -2);
			}
		}
		
		$s_foo = parent::__toString();
		$i = 0;
		
		if (count($this->Options->value) > 0) {
			$b_isAssoc = ( array_keys($this->Options->value) !== range(0, count($this->Options->value) - 1) );
			
			foreach ($this->Options->value as $s_option_label => $s_option_value) {
				if ($this->Break->value) {
					$s_foo .= '<div class="radio-container ' . $this->RadioClass->value . '"><label>';
				} else {
					$s_foo .= '<label class="radio-container ' . $this->RadioClass->value . '">';
				}
				
				$s_foo .= '<input type="radio" id="' . $this->Id->value . '_' . $i . '" name="' . $this->Name->value . '" value="' . $s_option_value . '"';
				
				if (issetStr($this->Class->value)) {
					$s_foo .= 'class="' . $this->Class->value . '"';
				}
				
				if (issetStr($this->Style->value)) {
					$s_foo .= ' style="' . $this->Style->value . '"';
				}
				
				if (issetStr($this->ValMessage->value)) {
					$s_foo .= ' data-valmessage="' . $this->ValMessage->value . '"';
				}
				
				
				if (issetStr($this->Form->value)) {
					$s_foo .= ' form="' . $this->Form->value . '"';
				}
				
				if (issetStr($this->FormAction->value)) {
					$s_foo .= ' formaction="' . $this->FormAction->value . '"';
				}
				
				if (issetStr($this->FormEnctype->value)) {
					$s_foo .= ' formenctype="' . $this->FormEnctype->value . '"';
				}
				
				if (issetStr($this->FormMethod->value)) {
					$s_foo .= ' formmethod="' . $this->FormMethod->value . '"';
				}
				
				if ($this->FormNoValidate->value) {
					$s_foo .= ' formnovalidate="formnovalidate"';
				}
				
				if (issetStr($this->FormTarget->value)) {
					$s_foo .= ' formtarget="' . $this->FormTarget->value . '"';
				}
				
				
				$s_foo .= ' tabindex="' . $o_glob->GetTabIndex() . '"';
				
				if ($this->AutoFocus->value) {
					$s_foo .= ' autofocus';
				}
				
				if ($this->Required->value) {
					$s_foo .= ' required';
				}
				
				if ($this->Readonly->value) {
					$s_foo .= ' readonly';
				}
				
				if ($this->Disabled->value) {
					$s_foo .= ' disabled';
				}
				
				if ((issetStr($this->Value->value)) && ($this->Value->value == $s_option_value)) {
					$s_foo .= ' checked';
				}
				
				$s_foo .= '>' . (($b_isAssoc) ? $s_option_label : $s_option_value);
				
				$s_foo .= '<span class="radio-checkmark"></span>';
				
				if ($this->Break->value) {
					$s_foo .= '</label></div>' . "\n";
				} else {
					$s_foo .= '</label>' . "\n";
				}
				
				$i++;
			}
		}
		
		if (issetStr($this->Description->value)) {
			$s_foo .= '<div';
			
			if (issetStr($this->DescriptionClass->value)) {
				$s_foo .= ' class="' . $this->DescriptionClass->value . '"';
			}
			
			$s_foo .= '><small>' . $this->Description->value . '</small></div>' . "\n";
		}
		
		return forestStringLib::closeHTMLTags($s_foo);
	}
}

class forestFormElementCheckbox extends forestFormInputAttributes {
	use forestData;
	
	/* Fields */
	
	private $Checked;
	private $Break;
	private $CheckboxClass;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct() {
		parent::__construct();
		$this->Checked = new forestBool;
		$this->Break = new forestBool(true);
		$this->CheckboxClass = new forestString;
	}
	
	public function getObjectVars() {
		return get_object_vars($this);
	}
	
	public function __toString() {
		$o_glob = forestGlobals::init();
		
		if (!issetStr($this->Name->value)) {
			$this->Name->value = $this->Id->value;
			
			if (forestStringLib::EndsWith($this->Id->value, '[]')) {
				$this->Id->value = substr($this->Id->value, 0, -2);
			}
		}
		
		$s_foo = parent::__toString();
		$i = 0;
		
		if (count($this->Options->value) > 0) {
			$b_isAssoc = ( array_keys($this->Options->value) !== range(0, count($this->Options->value) - 1) );
			
			foreach ($this->Options->value as $s_option_label => $s_option_value) {
				if ($this->Break->value) {
					$s_foo .= '<div class="checkbox-container ' . $this->CheckboxClass->value . '"><label>';
				} else {
					$s_foo .= '<label class="checkbox-container ' . $this->CheckboxClass->value . '">';
				}
				
				$s_foo .= '<input type="checkbox" id="' . $this->Id->value . '_' . $i . '" name="' . $this->Name->value . '" value="' . $s_option_value . '"';
				
				if (issetStr($this->Class->value)) {
					$s_foo .= 'class="' . $this->Class->value . '"';
				}
				
				if (issetStr($this->Style->value)) {
					$s_foo .= ' style="' . $this->Style->value . '"';
				}
				
				if (issetStr($this->ValMessage->value)) {
					$s_foo .= ' data-valmessage="' . $this->ValMessage->value . '"';
				}
				
				
				if (issetStr($this->Form->value)) {
					$s_foo .= ' form="' . $this->Form->value . '"';
				}
				
				if (issetStr($this->FormAction->value)) {
					$s_foo .= ' formaction="' . $this->FormAction->value . '"';
				}
				
				if (issetStr($this->FormEnctype->value)) {
					$s_foo .= ' formenctype="' . $this->FormEnctype->value . '"';
				}
				
				if (issetStr($this->FormMethod->value)) {
					$s_foo .= ' formmethod="' . $this->FormMethod->value . '"';
				}
				
				if ($this->FormNoValidate->value) {
					$s_foo .= ' formnovalidate="formnovalidate"';
				}
				
				if (issetStr($this->FormTarget->value)) {
					$s_foo .= ' formtarget="' . $this->FormTarget->value . '"';
				}
				
				
				$s_foo .= ' tabindex="' . $o_glob->GetTabIndex() . '"';
				
				if ($this->AutoFocus->value) {
					$s_foo .= ' autofocus';
				}
				
				if ($this->Required->value) {
					$s_foo .= ' required';
				}
				
				if ($this->Readonly->value) {
					$s_foo .= ' readonly';
				}
				
				if ($this->Disabled->value) {
					$s_foo .= ' disabled';
				}
				
				if ($this->Checked->value) {
					$s_foo .= ' checked';
				}
				
				if (issetStr($this->Value->value)) {
					if (strlen($this->Value->value) > $i) {
						if ($this->Value->value[( strlen($this->Value->value) - $i - 1 )] == '1') {
							$s_foo .= ' checked';
						}
					}
				}
				
				$s_foo .= '>' . (($b_isAssoc) ? $s_option_label : $s_option_value);
				
				$s_foo .= '<span class="checkbox-checkmark"></span>';
				
				if ($this->Break->value) {
					$s_foo .= '</label></div>' . "\n";
				} else {
					$s_foo .= '</label>' . "\n";
				}
				
				$i++;
			}
		}
		
		if (issetStr($this->Description->value)) {
			$s_foo .= '<div';
			
			if (issetStr($this->DescriptionClass->value)) {
				$s_foo .= ' class="' . $this->DescriptionClass->value . '"';
			}
			
			$s_foo .= '><small>' . $this->Description->value . '</small></div>' . "\n";
		}
		
		return forestStringLib::closeHTMLTags($s_foo);
	}
}

class forestFormElementColor extends forestFormInputAttributes {
	use forestData;
	
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getObjectVars() {
		return get_object_vars($this);
	}
		
	public function __toString() {
		$o_glob = forestGlobals::init();
		
		if (!issetStr($this->Name->value)) {
			$this->Name->value = $this->Id->value;
		}
		
		$s_foo = parent::__toString();
		
		$s_foo .= '<input type="color" id="' . $this->Id->value . '" name="' . $this->Name->value . '" class="' . $this->Class->value . '"';
		
		if (issetStr($this->Style->value)) {
			$s_foo .= ' style="' . $this->Style->value . '"';
		}
		
		if (issetStr($this->Value->value)) {
			$s_foo .= ' value="' . $this->Value->value . '"';
		}
		
		if (!($this->AutoComplete->value)) {
			$s_foo .= ' autocomplete="off"';
		}
		
		if (issetStr($this->ValMessage->value)) {
			$s_foo .= ' data-valmessage="' . $this->ValMessage->value . '"';
		}
		
		
		if (issetStr($this->Form->value)) {
			$s_foo .= ' form="' . $this->Form->value . '"';
		}
		
		if (issetStr($this->FormAction->value)) {
			$s_foo .= ' formaction="' . $this->FormAction->value . '"';
		}
		
		if (issetStr($this->FormEnctype->value)) {
			$s_foo .= ' formenctype="' . $this->FormEnctype->value . '"';
		}
		
		if (issetStr($this->FormMethod->value)) {
			$s_foo .= ' formmethod="' . $this->FormMethod->value . '"';
		}
		
		if ($this->FormNoValidate->value) {
			$s_foo .= ' formnovalidate="formnovalidate"';
		}
		
		if (issetStr($this->FormTarget->value)) {
			$s_foo .= ' formtarget="' . $this->FormTarget->value . '"';
		}
		
		
		$s_foo .= ' tabindex="' . $o_glob->GetTabIndex() . '"';
		
		if ($this->AutoFocus->value) {
			$s_foo .= ' autofocus';
		}
		
		if ($this->Required->value) {
			$s_foo .= ' required';
		}
		
		if ($this->Readonly->value) {
			$s_foo .= ' readonly';
		}
		
		if ($this->Disabled->value) {
			$s_foo .= ' disabled';
		}
		
		$s_foo .= '>' . "\n";
		
		if (issetStr($this->Description->value)) {
			$s_foo .= '<div';
			
			if (issetStr($this->DescriptionClass->value)) {
				$s_foo .= ' class="' . $this->DescriptionClass->value . '"';
			}
			
			$s_foo .= '><small>' . $this->Description->value . '</small></div>' . "\n";
		}
		
		return forestStringLib::closeHTMLTags($s_foo);
	}
}

class forestFormElementEmail extends forestFormInputAttributes {
	use forestData;
	
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getObjectVars() {
		return get_object_vars($this);
	}
		
	public function __toString() {
		$o_glob = forestGlobals::init();
		
		if (!issetStr($this->Name->value)) {
			$this->Name->value = $this->Id->value;
		}
		
		$s_foo = parent::__toString();
		
		$s_foo .= '<input type="email" id="' . $this->Id->value . '" name="' . $this->Name->value . '" class="' . $this->Class->value . '"';
		
		if (issetStr($this->Style->value)) {
			$s_foo .= ' style="' . $this->Style->value . '"';
		}
		
		if (issetStr($this->Placeholder->value)) {
			$s_foo .= ' placeholder="' . $this->Placeholder->value . '"';
		}
		
		if (issetStr($this->Value->value)) {
			$s_foo .= ' value="' . $this->Value->value . '"';
		}
		
		if ($this->Size->value > 0) {
			$s_foo .= ' size="' . $this->Size->value . '"';
		}
		
		if (!($this->AutoComplete->value)) {
			$s_foo .= ' autocomplete="off"';
		}
		
		if (issetStr($this->Dirname->value)) {
			$s_foo .= ' dirname="' . $this->Dirname->value . '"';
		}
		
		if (issetStr($this->ValMessage->value)) {
			$s_foo .= ' data-valmessage="' . $this->ValMessage->value . '"';
		}
		
		
		if (issetStr($this->Form->value)) {
			$s_foo .= ' form="' . $this->Form->value . '"';
		}
		
		if (issetStr($this->FormAction->value)) {
			$s_foo .= ' formaction="' . $this->FormAction->value . '"';
		}
		
		if (issetStr($this->FormEnctype->value)) {
			$s_foo .= ' formenctype="' . $this->FormEnctype->value . '"';
		}
		
		if (issetStr($this->FormMethod->value)) {
			$s_foo .= ' formmethod="' . $this->FormMethod->value . '"';
		}
		
		if ($this->FormNoValidate->value) {
			$s_foo .= ' formnovalidate="formnovalidate"';
		}
		
		if (issetStr($this->FormTarget->value)) {
			$s_foo .= ' formtarget="' . $this->FormTarget->value . '"';
		}
		
		
		$s_foo .= ' tabindex="' . $o_glob->GetTabIndex() . '"';
		
		if ($this->AutoFocus->value) {
			$s_foo .= ' autofocus';
		}
		
		if ($this->Required->value) {
			$s_foo .= ' required';
		}
		
		if ($this->Readonly->value) {
			$s_foo .= ' readonly';
		}
		
		if ($this->Disabled->value) {
			$s_foo .= ' disabled';
		}
		
		$s_foo .= '>' . "\n";
		
		if (issetStr($this->Description->value)) {
			$s_foo .= '<div';
			
			if (issetStr($this->DescriptionClass->value)) {
				$s_foo .= ' class="' . $this->DescriptionClass->value . '"';
			}
			
			$s_foo .= '><small>' . $this->Description->value . '</small></div>' . "\n";
		}
		
		return forestStringLib::closeHTMLTags($s_foo);
	}
}

class forestFormElementUrl extends forestFormInputAttributes {
	use forestData;
	
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getObjectVars() {
		return get_object_vars($this);
	}
		
	public function __toString() {
		$o_glob = forestGlobals::init();
		
		if (!issetStr($this->Name->value)) {
			$this->Name->value = $this->Id->value;
		}
		
		$s_foo = parent::__toString();
		
		$s_foo .= '<input type="url" id="' . $this->Id->value . '" name="' . $this->Name->value . '" class="' . $this->Class->value . '"';
		
		if (issetStr($this->Style->value)) {
			$s_foo .= ' style="' . $this->Style->value . '"';
		}
		
		if (issetStr($this->Placeholder->value)) {
			$s_foo .= ' placeholder="' . $this->Placeholder->value . '"';
		}
		
		if (issetStr($this->Value->value)) {
			$s_foo .= ' value="' . $this->Value->value . '"';
		}
		
		if ($this->Size->value > 0) {
			$s_foo .= ' size="' . $this->Size->value . '"';
		}
		
		if (!($this->AutoComplete->value)) {
			$s_foo .= ' autocomplete="off"';
		}
		
		if (issetStr($this->Dirname->value)) {
			$s_foo .= ' dirname="' . $this->Dirname->value . '"';
		}
		
		if (issetStr($this->ValMessage->value)) {
			$s_foo .= ' data-valmessage="' . $this->ValMessage->value . '"';
		}
		
		
		if (issetStr($this->Form->value)) {
			$s_foo .= ' form="' . $this->Form->value . '"';
		}
		
		if (issetStr($this->FormAction->value)) {
			$s_foo .= ' formaction="' . $this->FormAction->value . '"';
		}
		
		if (issetStr($this->FormEnctype->value)) {
			$s_foo .= ' formenctype="' . $this->FormEnctype->value . '"';
		}
		
		if (issetStr($this->FormMethod->value)) {
			$s_foo .= ' formmethod="' . $this->FormMethod->value . '"';
		}
		
		if ($this->FormNoValidate->value) {
			$s_foo .= ' formnovalidate="formnovalidate"';
		}
		
		if (issetStr($this->FormTarget->value)) {
			$s_foo .= ' formtarget="' . $this->FormTarget->value . '"';
		}
		
		
		$s_foo .= ' tabindex="' . $o_glob->GetTabIndex() . '"';
		
		if ($this->AutoFocus->value) {
			$s_foo .= ' autofocus';
		}
		
		if ($this->Required->value) {
			$s_foo .= ' required';
		}
		
		if ($this->Readonly->value) {
			$s_foo .= ' readonly';
		}
		
		if ($this->Disabled->value) {
			$s_foo .= ' disabled';
		}
		
		$s_foo .= '>' . "\n";
		
		if (issetStr($this->Description->value)) {
			$s_foo .= '<div';
			
			if (issetStr($this->DescriptionClass->value)) {
				$s_foo .= ' class="' . $this->DescriptionClass->value . '"';
			}
			
			$s_foo .= '><small>' . $this->Description->value . '</small></div>' . "\n";
		}
		
		return forestStringLib::closeHTMLTags($s_foo);
	}
}

class forestFormElementDate extends forestFormInputAttributes {
	use forestData;
	
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getObjectVars() {
		return get_object_vars($this);
	}
		
	public function __toString() {
		$o_glob = forestGlobals::init();
		
		if (!issetStr($this->Name->value)) {
			$this->Name->value = $this->Id->value;
		}
		
		$s_foo = parent::__toString();
		
		$s_foo .= '<input type="date" id="' . $this->Id->value . '" name="' . $this->Name->value . '" class="' . $this->Class->value . '"';
		
		if (issetStr($this->Style->value)) {
			$s_foo .= ' style="' . $this->Style->value . '"';
		}
		
		if (issetStr($this->Value->value)) {
			$s_foo .= ' value="' . $this->Value->value . '"';
		}
		
		if (issetStr($this->Min->value)) {
			if (preg_match('/^ (\d){4} - ((0[1-9])|(1[0-2])) - ((0[1-9])|(1[0-9])|2[0-9]|(3[0-1]))  $/x', $this->Min->value)) {
				$s_foo .= ' min="' . $this->Min->value . '"';
			}
		}
		
		if (issetStr($this->Max->value)) {
			if (preg_match('/^ (\d){4} - ((0[1-9])|(1[0-2])) - ((0[1-9])|(1[0-9])|2[0-9]|(3[0-1]))  $/x', $this->Min->value)) {
				$s_foo .= ' max="' . $this->Max->value . '"';
			}
		}
		
		if (!($this->AutoComplete->value)) {
			$s_foo .= ' autocomplete="off"';
		}
		
		if (issetStr($this->ValMessage->value)) {
			$s_foo .= ' data-valmessage="' . $this->ValMessage->value . '"';
		}
		
		
		if (issetStr($this->Form->value)) {
			$s_foo .= ' form="' . $this->Form->value . '"';
		}
		
		if (issetStr($this->FormAction->value)) {
			$s_foo .= ' formaction="' . $this->FormAction->value . '"';
		}
		
		if (issetStr($this->FormEnctype->value)) {
			$s_foo .= ' formenctype="' . $this->FormEnctype->value . '"';
		}
		
		if (issetStr($this->FormMethod->value)) {
			$s_foo .= ' formmethod="' . $this->FormMethod->value . '"';
		}
		
		if ($this->FormNoValidate->value) {
			$s_foo .= ' formnovalidate="formnovalidate"';
		}
		
		if (issetStr($this->FormTarget->value)) {
			$s_foo .= ' formtarget="' . $this->FormTarget->value . '"';
		}
		
		
		$s_foo .= ' tabindex="' . $o_glob->GetTabIndex() . '"';
		
		if ($this->AutoFocus->value) {
			$s_foo .= ' autofocus';
		}
		
		if ($this->Required->value) {
			$s_foo .= ' required';
		}
		
		if ($this->Readonly->value) {
			$s_foo .= ' readonly';
		}
		
		if ($this->Disabled->value) {
			$s_foo .= ' disabled';
		}
		
		$s_foo .= '>' . "\n";
		
		if (issetStr($this->Description->value)) {
			$s_foo .= '<div';
			
			if (issetStr($this->DescriptionClass->value)) {
				$s_foo .= ' class="' . $this->DescriptionClass->value . '"';
			}
			
			$s_foo .= '><small>' . $this->Description->value . '</small></div>' . "\n";
		}
		
		return forestStringLib::closeHTMLTags($s_foo);
	}
}

class forestFormElementDateTimeLocal extends forestFormInputAttributes {
	use forestData;
	
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getObjectVars() {
		return get_object_vars($this);
	}
		
	public function __toString() {
		$o_glob = forestGlobals::init();
		
		if (!issetStr($this->Name->value)) {
			$this->Name->value = $this->Id->value;
		}
		
		$s_foo = parent::__toString();
		
		$s_foo .= '<input type="datetime-local" id="' . $this->Id->value . '" name="' . $this->Name->value . '" class="' . $this->Class->value . '"';
		
		if (issetStr($this->Style->value)) {
			$s_foo .= ' style="' . $this->Style->value . '"';
		}
		
		if (issetStr($this->Value->value)) {
			$s_foo .= ' value="' . $this->Value->value . '"';
		}
		
		if (issetStr($this->Min->value)) {
			if (preg_match('/^ (\d){4} - ((0[1-9])|(1[0-2])) - ((0[1-9])|(1[0-9])|2[0-9]|(3[0-1])) T (0[0-9]|1[0-9]|2[0-3]) : ([0-5][0-9]) : ([0-5][0-9]) $/x', $this->Min->value)) {
				$s_foo .= ' min="' . $this->Min->value . '"';
			}
		}
		
		if (issetStr($this->Max->value)) {
			if (preg_match('/^ (\d){4} - ((0[1-9])|(1[0-2])) - ((0[1-9])|(1[0-9])|2[0-9]|(3[0-1])) T (0[0-9]|1[0-9]|2[0-3]) : ([0-5][0-9]) : ([0-5][0-9]) $/x', $this->Max->value)) {
				$s_foo .= ' max="' . $this->Max->value . '"';
			}
		}
		
		if (!($this->AutoComplete->value)) {
			$s_foo .= ' autocomplete="off"';
		}
		
		if (issetStr($this->ValMessage->value)) {
			$s_foo .= ' data-valmessage="' . $this->ValMessage->value . '"';
		}
		
		
		if (issetStr($this->Form->value)) {
			$s_foo .= ' form="' . $this->Form->value . '"';
		}
		
		if (issetStr($this->FormAction->value)) {
			$s_foo .= ' formaction="' . $this->FormAction->value . '"';
		}
		
		if (issetStr($this->FormEnctype->value)) {
			$s_foo .= ' formenctype="' . $this->FormEnctype->value . '"';
		}
		
		if (issetStr($this->FormMethod->value)) {
			$s_foo .= ' formmethod="' . $this->FormMethod->value . '"';
		}
		
		if ($this->FormNoValidate->value) {
			$s_foo .= ' formnovalidate="formnovalidate"';
		}
		
		if (issetStr($this->FormTarget->value)) {
			$s_foo .= ' formtarget="' . $this->FormTarget->value . '"';
		}
		
		
		$s_foo .= ' tabindex="' . $o_glob->GetTabIndex() . '"';
		
		if ($this->AutoFocus->value) {
			$s_foo .= ' autofocus';
		}
		
		if ($this->Required->value) {
			$s_foo .= ' required';
		}
		
		if ($this->Readonly->value) {
			$s_foo .= ' readonly';
		}
		
		if ($this->Disabled->value) {
			$s_foo .= ' disabled';
		}
		
		$s_foo .= '>' . "\n";
		
		if (issetStr($this->Description->value)) {
			$s_foo .= '<div';
			
			if (issetStr($this->DescriptionClass->value)) {
				$s_foo .= ' class="' . $this->DescriptionClass->value . '"';
			}
			
			$s_foo .= '><small>' . $this->Description->value . '</small></div>' . "\n";
		}
		
		return forestStringLib::closeHTMLTags($s_foo);
	}
}

class forestFormElementMonth extends forestFormInputAttributes {
	use forestData;
	
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getObjectVars() {
		return get_object_vars($this);
	}
		
	public function __toString() {
		$o_glob = forestGlobals::init();
		
		if (!issetStr($this->Name->value)) {
			$this->Name->value = $this->Id->value;
		}
		
		$s_foo = parent::__toString();
		
		$s_foo .= '<input type="month" id="' . $this->Id->value . '" name="' . $this->Name->value . '" class="' . $this->Class->value . '"';
		
		if (issetStr($this->Style->value)) {
			$s_foo .= ' style="' . $this->Style->value . '"';
		}
		
		if (issetStr($this->Value->value)) {
			$s_foo .= ' value="' . $this->Value->value . '"';
		}
		
		if (issetStr($this->Min->value)) {
			if (preg_match('/^ (\d){4} - ((0[1-9])|(1[0-2])) $/x', $this->Min->value)) {
				$s_foo .= ' min="' . $this->Min->value . '"';
			}
		}
		
		if (issetStr($this->Max->value)) {
			if (preg_match('/^ (\d){4} - ((0[1-9])|(1[0-2])) $/x', $this->Max->value)) {
				$s_foo .= ' max="' . $this->Max->value . '"';
			}
		}
		
		if (!($this->AutoComplete->value)) {
			$s_foo .= ' autocomplete="off"';
		}
		
		if (issetStr($this->ValMessage->value)) {
			$s_foo .= ' data-valmessage="' . $this->ValMessage->value . '"';
		}
		
		
		if (issetStr($this->Form->value)) {
			$s_foo .= ' form="' . $this->Form->value . '"';
		}
		
		if (issetStr($this->FormAction->value)) {
			$s_foo .= ' formaction="' . $this->FormAction->value . '"';
		}
		
		if (issetStr($this->FormEnctype->value)) {
			$s_foo .= ' formenctype="' . $this->FormEnctype->value . '"';
		}
		
		if (issetStr($this->FormMethod->value)) {
			$s_foo .= ' formmethod="' . $this->FormMethod->value . '"';
		}
		
		if ($this->FormNoValidate->value) {
			$s_foo .= ' formnovalidate="formnovalidate"';
		}
		
		if (issetStr($this->FormTarget->value)) {
			$s_foo .= ' formtarget="' . $this->FormTarget->value . '"';
		}
		
		
		$s_foo .= ' tabindex="' . $o_glob->GetTabIndex() . '"';
		
		if ($this->AutoFocus->value) {
			$s_foo .= ' autofocus';
		}
		
		if ($this->Required->value) {
			$s_foo .= ' required';
		}
		
		if ($this->Readonly->value) {
			$s_foo .= ' readonly';
		}
		
		if ($this->Disabled->value) {
			$s_foo .= ' disabled';
		}
		
		$s_foo .= '>' . "\n";
		
		if (issetStr($this->Description->value)) {
			$s_foo .= '<div';
			
			if (issetStr($this->DescriptionClass->value)) {
				$s_foo .= ' class="' . $this->DescriptionClass->value . '"';
			}
			
			$s_foo .= '><small>' . $this->Description->value . '</small></div>' . "\n";
		}
		
		return forestStringLib::closeHTMLTags($s_foo);
	}
}

class forestFormElementNumber extends forestFormInputAttributes {
	use forestData;
	
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getObjectVars() {
		return get_object_vars($this);
	}
		
	public function __toString() {
		$o_glob = forestGlobals::init();
		
		if (!issetStr($this->Name->value)) {
			$this->Name->value = $this->Id->value;
		}
		
		$s_foo = parent::__toString();
		
		$s_foo .= '<input type="number" id="' . $this->Id->value . '" name="' . $this->Name->value . '" class="' . $this->Class->value . '"';
		
		if (issetStr($this->Style->value)) {
			$s_foo .= ' style="' . $this->Style->value . '"';
		}
		
		if (issetStr($this->Placeholder->value)) {
			$s_foo .= ' placeholder="' . $this->Placeholder->value . '"';
		}
		
		if (issetStr($this->Value->value)) {
			$s_foo .= ' value="' . $this->Value->value . '"';
		}
		
		if ($this->Size->value > 0) {
			$s_foo .= ' size="' . $this->Size->value . '"';
		}
		
		if (intval($this->Min->value) != 0) {
			$s_foo .= ' min="' . intval($this->Min->value) . '"';
		}
		
		if (intval($this->Max->value) != 0) {
			$s_foo .= ' max="' . intval($this->Max->value) . '"';
		}
		
		if ($this->Step->value > 0) {
			$s_foo .= ' step="' . $this->Step->value . '"';
		}
		
		if (!($this->AutoComplete->value)) {
			$s_foo .= ' autocomplete="off"';
		}
		
		if (issetStr($this->ValMessage->value)) {
			$s_foo .= ' data-valmessage="' . $this->ValMessage->value . '"';
		}
		
		
		if (issetStr($this->Form->value)) {
			$s_foo .= ' form="' . $this->Form->value . '"';
		}
		
		if (issetStr($this->FormAction->value)) {
			$s_foo .= ' formaction="' . $this->FormAction->value . '"';
		}
		
		if (issetStr($this->FormEnctype->value)) {
			$s_foo .= ' formenctype="' . $this->FormEnctype->value . '"';
		}
		
		if (issetStr($this->FormMethod->value)) {
			$s_foo .= ' formmethod="' . $this->FormMethod->value . '"';
		}
		
		if ($this->FormNoValidate->value) {
			$s_foo .= ' formnovalidate="formnovalidate"';
		}
		
		if (issetStr($this->FormTarget->value)) {
			$s_foo .= ' formtarget="' . $this->FormTarget->value . '"';
		}
		
		
		$s_foo .= ' tabindex="' . $o_glob->GetTabIndex() . '"';
		
		if ($this->AutoFocus->value) {
			$s_foo .= ' autofocus';
		}
		
		if ($this->Required->value) {
			$s_foo .= ' required';
		}
		
		if ($this->Readonly->value) {
			$s_foo .= ' readonly';
		}
		
		if ($this->Disabled->value) {
			$s_foo .= ' disabled';
		}
		
		$s_foo .= '>' . "\n";
		
		if (issetStr($this->Description->value)) {
			$s_foo .= '<div';
			
			if (issetStr($this->DescriptionClass->value)) {
				$s_foo .= ' class="' . $this->DescriptionClass->value . '"';
			}
			
			$s_foo .= '><small>' . $this->Description->value . '</small></div>' . "\n";
		}
		
		return forestStringLib::closeHTMLTags($s_foo);
	}
}

class forestFormElementRange extends forestFormInputAttributes {
	use forestData;
	
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getObjectVars() {
		return get_object_vars($this);
	}
		
	public function __toString() {
		$o_glob = forestGlobals::init();
		
		if (!issetStr($this->Name->value)) {
			$this->Name->value = $this->Id->value;
		}
		
		$s_foo = parent::__toString();
		
		$s_foo .= '<input type="range" id="' . $this->Id->value . '" name="' . $this->Name->value . '" class="' . $this->Class->value . '"';
		
		if (issetStr($this->Style->value)) {
			$s_foo .= ' style="' . $this->Style->value . '"';
		}
		
		if (issetStr($this->Value->value)) {
			$s_foo .= ' value="' . $this->Value->value . '"';
		}
		
		if (intval($this->Min->value) != 0) {
			$s_foo .= ' min="' . intval($this->Min->value) . '"';
		}
		
		if (intval($this->Max->value) != 0) {
			$s_foo .= ' max="' . intval($this->Max->value) . '"';
		}
		
		if ($this->Step->value > 0) {
			$s_foo .= ' step="' . $this->Step->value . '"';
		}
		
		if (!($this->AutoComplete->value)) {
			$s_foo .= ' autocomplete="off"';
		}
		
		if (issetStr($this->Dirname->value)) {
			$s_foo .= ' dirname="' . $this->Dirname->value . '"';
		}
		
		if (issetStr($this->ValMessage->value)) {
			$s_foo .= ' data-valmessage="' . $this->ValMessage->value . '"';
		}
		
		
		if (issetStr($this->Form->value)) {
			$s_foo .= ' form="' . $this->Form->value . '"';
		}
		
		if (issetStr($this->FormAction->value)) {
			$s_foo .= ' formaction="' . $this->FormAction->value . '"';
		}
		
		if (issetStr($this->FormEnctype->value)) {
			$s_foo .= ' formenctype="' . $this->FormEnctype->value . '"';
		}
		
		if (issetStr($this->FormMethod->value)) {
			$s_foo .= ' formmethod="' . $this->FormMethod->value . '"';
		}
		
		if ($this->FormNoValidate->value) {
			$s_foo .= ' formnovalidate="formnovalidate"';
		}
		
		if (issetStr($this->FormTarget->value)) {
			$s_foo .= ' formtarget="' . $this->FormTarget->value . '"';
		}
		
		
		$s_foo .= ' tabindex="' . $o_glob->GetTabIndex() . '"';
		
		if ($this->AutoFocus->value) {
			$s_foo .= ' autofocus';
		}
		
		if ($this->Required->value) {
			$s_foo .= ' required';
		}
		
		if ($this->Readonly->value) {
			$s_foo .= ' readonly';
		}
		
		if ($this->Disabled->value) {
			$s_foo .= ' disabled';
		}
		
		$s_foo .= '>' . "\n";
		
		if (issetStr($this->Description->value)) {
			$s_foo .= '<div';
			
			if (issetStr($this->DescriptionClass->value)) {
				$s_foo .= ' class="' . $this->DescriptionClass->value . '"';
			}
			
			$s_foo .= '><small>' . $this->Description->value . '</small></div>' . "\n";
		}
		
		return forestStringLib::closeHTMLTags($s_foo);
	}
}

class forestFormElementSearch extends forestFormInputAttributes {
	use forestData;
	
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getObjectVars() {
		return get_object_vars($this);
	}
		
	public function __toString() {
		$o_glob = forestGlobals::init();
		
		if (!issetStr($this->Name->value)) {
			$this->Name->value = $this->Id->value;
		}
		
		$s_foo = parent::__toString();
		
		$s_foo .= '<input type="search" id="' . $this->Id->value . '" name="' . $this->Name->value . '" class="' . $this->Class->value . '"';
		
		if (issetStr($this->Style->value)) {
			$s_foo .= ' style="' . $this->Style->value . '"';
		}
		
		if (issetStr($this->Placeholder->value)) {
			$s_foo .= ' placeholder="' . $this->Placeholder->value . '"';
		}
		
		if (issetStr($this->Value->value)) {
			$s_foo .= ' value="' . $this->Value->value . '"';
		}
		
		if ($this->Size->value > 0) {
			$s_foo .= ' size="' . $this->Size->value . '"';
		}
		
		if (!($this->AutoComplete->value)) {
			$s_foo .= ' autocomplete="off"';
		}
		
		if (issetStr($this->Dirname->value)) {
			$s_foo .= ' dirname="' . $this->Dirname->value . '"';
		}
		
		if (issetStr($this->ValMessage->value)) {
			$s_foo .= ' data-valmessage="' . $this->ValMessage->value . '"';
		}
		
		
		if (issetStr($this->Form->value)) {
			$s_foo .= ' form="' . $this->Form->value . '"';
		}
		
		if (issetStr($this->FormAction->value)) {
			$s_foo .= ' formaction="' . $this->FormAction->value . '"';
		}
		
		if (issetStr($this->FormEnctype->value)) {
			$s_foo .= ' formenctype="' . $this->FormEnctype->value . '"';
		}
		
		if (issetStr($this->FormMethod->value)) {
			$s_foo .= ' formmethod="' . $this->FormMethod->value . '"';
		}
		
		if ($this->FormNoValidate->value) {
			$s_foo .= ' formnovalidate="formnovalidate"';
		}
		
		if (issetStr($this->FormTarget->value)) {
			$s_foo .= ' formtarget="' . $this->FormTarget->value . '"';
		}
		
		
		$s_foo .= ' tabindex="' . $o_glob->GetTabIndex() . '"';
		
		if ($this->AutoFocus->value) {
			$s_foo .= ' autofocus';
		}
		
		if ($this->Required->value) {
			$s_foo .= ' required';
		}
		
		if ($this->Readonly->value) {
			$s_foo .= ' readonly';
		}
		
		if ($this->Disabled->value) {
			$s_foo .= ' disabled';
		}
		
		$s_foo .= '>' . "\n";
		
		if (issetStr($this->Description->value)) {
			$s_foo .= '<div';
			
			if (issetStr($this->DescriptionClass->value)) {
				$s_foo .= ' class="' . $this->DescriptionClass->value . '"';
			}
			
			$s_foo .= '><small>' . $this->Description->value . '</small></div>' . "\n";
		}
		
		return forestStringLib::closeHTMLTags($s_foo);
	}
}

class forestFormElementPhone extends forestFormInputAttributes {
	use forestData;
	
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getObjectVars() {
		return get_object_vars($this);
	}
		
	public function __toString() {
		$o_glob = forestGlobals::init();
		
		if (!issetStr($this->Name->value)) {
			$this->Name->value = $this->Id->value;
		}
		
		$s_foo = parent::__toString();
		
		$s_foo .= '<input type="tel" id="' . $this->Id->value . '" name="' . $this->Name->value . '" class="' . $this->Class->value . '"';
		
		if (issetStr($this->Style->value)) {
			$s_foo .= ' style="' . $this->Style->value . '"';
		}
		
		if (issetStr($this->Placeholder->value)) {
			$s_foo .= ' placeholder="' . $this->Placeholder->value . '"';
		}
		
		if (issetStr($this->Value->value)) {
			$s_foo .= ' value="' . $this->Value->value . '"';
		}
		
		if ($this->Size->value > 0) {
			$s_foo .= ' size="' . $this->Size->value . '"';
		}
		
		if (!($this->AutoComplete->value)) {
			$s_foo .= ' autocomplete="off"';
		}
		
		if (issetStr($this->Dirname->value)) {
			$s_foo .= ' dirname="' . $this->Dirname->value . '"';
		}
		
		if (issetStr($this->ValMessage->value)) {
			$s_foo .= ' data-valmessage="' . $this->ValMessage->value . '"';
		}
		
		
		if (issetStr($this->Form->value)) {
			$s_foo .= ' form="' . $this->Form->value . '"';
		}
		
		if (issetStr($this->FormAction->value)) {
			$s_foo .= ' formaction="' . $this->FormAction->value . '"';
		}
		
		if (issetStr($this->FormEnctype->value)) {
			$s_foo .= ' formenctype="' . $this->FormEnctype->value . '"';
		}
		
		if (issetStr($this->FormMethod->value)) {
			$s_foo .= ' formmethod="' . $this->FormMethod->value . '"';
		}
		
		if ($this->FormNoValidate->value) {
			$s_foo .= ' formnovalidate="formnovalidate"';
		}
		
		if (issetStr($this->FormTarget->value)) {
			$s_foo .= ' formtarget="' . $this->FormTarget->value . '"';
		}
		
		
		$s_foo .= ' tabindex="' . $o_glob->GetTabIndex() . '"';
		
		if ($this->AutoFocus->value) {
			$s_foo .= ' autofocus';
		}
		
		if ($this->Required->value) {
			$s_foo .= ' required';
		}
		
		if ($this->Readonly->value) {
			$s_foo .= ' readonly';
		}
		
		if ($this->Disabled->value) {
			$s_foo .= ' disabled';
		}
		
		$s_foo .= '>' . "\n";
		
		if (issetStr($this->Description->value)) {
			$s_foo .= '<div';
			
			if (issetStr($this->DescriptionClass->value)) {
				$s_foo .= ' class="' . $this->DescriptionClass->value . '"';
			}
			
			$s_foo .= '><small>' . $this->Description->value . '</small></div>' . "\n";
		}
		
		return forestStringLib::closeHTMLTags($s_foo);
	}
}

class forestFormElementTime extends forestFormInputAttributes {
	use forestData;
	
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	public function __construct() {
		parent::__construct();
		
		$this->Step->value = 1;
	}
	
	public function getObjectVars() {
		return get_object_vars($this);
	}
		
	public function __toString() {
		$o_glob = forestGlobals::init();
		
		if (!issetStr($this->Name->value)) {
			$this->Name->value = $this->Id->value;
		}
		
		$s_foo = parent::__toString();
		
		$s_foo .= '<input type="time" id="' . $this->Id->value . '" name="' . $this->Name->value . '" class="' . $this->Class->value . '"';
		
		if (issetStr($this->Style->value)) {
			$s_foo .= ' style="' . $this->Style->value . '"';
		}
		
		if (issetStr($this->Value->value)) {
			$s_foo .= ' value="' . $this->Value->value . '"';
		}
		
		if (issetStr($this->Min->value)) {
			if (preg_match('/^ (0[0-9]|1[0-9]|2[0-3]) : ([0-5][0-9]) : ([0-5][0-9]) $/x', $this->Min->value)) {
				$s_foo .= ' min="' . $this->Min->value . '"';
			}
		}
		
		if (issetStr($this->Max->value)) {
			if (preg_match('/^ (0[0-9]|1[0-9]|2[0-3]) : ([0-5][0-9]) : ([0-5][0-9]) $/x', $this->Max->value)) {
				$s_foo .= ' max="' . $this->Max->value . '"';
			}
		}
		
		if ($this->Step->value > 0) {
			$s_foo .= ' step="' . $this->Step->value . '"';
		}
		
		if (!($this->AutoComplete->value)) {
			$s_foo .= ' autocomplete="off"';
		}
		
		if (issetStr($this->Dirname->value)) {
			$s_foo .= ' dirname="' . $this->Dirname->value . '"';
		}
		
		if (issetStr($this->ValMessage->value)) {
			$s_foo .= ' data-valmessage="' . $this->ValMessage->value . '"';
		}
		
		
		if (issetStr($this->Form->value)) {
			$s_foo .= ' form="' . $this->Form->value . '"';
		}
		
		if (issetStr($this->FormAction->value)) {
			$s_foo .= ' formaction="' . $this->FormAction->value . '"';
		}
		
		if (issetStr($this->FormEnctype->value)) {
			$s_foo .= ' formenctype="' . $this->FormEnctype->value . '"';
		}
		
		if (issetStr($this->FormMethod->value)) {
			$s_foo .= ' formmethod="' . $this->FormMethod->value . '"';
		}
		
		if ($this->FormNoValidate->value) {
			$s_foo .= ' formnovalidate="formnovalidate"';
		}
		
		if (issetStr($this->FormTarget->value)) {
			$s_foo .= ' formtarget="' . $this->FormTarget->value . '"';
		}
		
		
		$s_foo .= ' tabindex="' . $o_glob->GetTabIndex() . '"';
		
		if ($this->AutoFocus->value) {
			$s_foo .= ' autofocus';
		}
		
		if ($this->Required->value) {
			$s_foo .= ' required';
		}
		
		if ($this->Readonly->value) {
			$s_foo .= ' readonly';
		}
		
		if ($this->Disabled->value) {
			$s_foo .= ' disabled';
		}
		
		$s_foo .= '>' . "\n";
		
		if (issetStr($this->Description->value)) {
			$s_foo .= '<div';
			
			if (issetStr($this->DescriptionClass->value)) {
				$s_foo .= ' class="' . $this->DescriptionClass->value . '"';
			}
			
			$s_foo .= '><small>' . $this->Description->value . '</small></div>' . "\n";
		}
		
		return forestStringLib::closeHTMLTags($s_foo);
	}
}

class forestFormElementWeek extends forestFormInputAttributes {
	use forestData;
	
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getObjectVars() {
		return get_object_vars($this);
	}
		
	public function __toString() {
		$o_glob = forestGlobals::init();
		
		if (!issetStr($this->Name->value)) {
			$this->Name->value = $this->Id->value;
		}
		
		$s_foo = parent::__toString();
		
		$s_foo .= '<input type="week" id="' . $this->Id->value . '" name="' . $this->Name->value . '" class="' . $this->Class->value . '"';
		
		if (issetStr($this->Style->value)) {
			$s_foo .= ' style="' . $this->Style->value . '"';
		}
		
		if (issetStr($this->Value->value)) {
			$s_foo .= ' value="' . $this->Value->value . '"';
		}
		
		if (issetStr($this->Min->value)) {
			if (preg_match('/^ (\d){4} - W((0[1-9])|([1-4][0-9])|(5[0-3])) $/x', $this->Min->value)) {
				$s_foo .= ' min="' . $this->Min->value . '"';
			}
		}
		
		if (issetStr($this->Max->value)) {
			if (preg_match('/^ (\d){4} - W((0[1-9])|([1-4][0-9])|(5[0-3])) $/x', $this->Max->value)) {
				$s_foo .= ' max="' . $this->Max->value . '"';
			}
		}
		
		if (!($this->AutoComplete->value)) {
			$s_foo .= ' autocomplete="off"';
		}
		
		if (issetStr($this->Dirname->value)) {
			$s_foo .= ' dirname="' . $this->Dirname->value . '"';
		}
		
		if (issetStr($this->ValMessage->value)) {
			$s_foo .= ' data-valmessage="' . $this->ValMessage->value . '"';
		}
		
		
		if (issetStr($this->Form->value)) {
			$s_foo .= ' form="' . $this->Form->value . '"';
		}
		
		if (issetStr($this->FormAction->value)) {
			$s_foo .= ' formaction="' . $this->FormAction->value . '"';
		}
		
		if (issetStr($this->FormEnctype->value)) {
			$s_foo .= ' formenctype="' . $this->FormEnctype->value . '"';
		}
		
		if (issetStr($this->FormMethod->value)) {
			$s_foo .= ' formmethod="' . $this->FormMethod->value . '"';
		}
		
		if ($this->FormNoValidate->value) {
			$s_foo .= ' formnovalidate="formnovalidate"';
		}
		
		if (issetStr($this->FormTarget->value)) {
			$s_foo .= ' formtarget="' . $this->FormTarget->value . '"';
		}
		
		
		$s_foo .= ' tabindex="' . $o_glob->GetTabIndex() . '"';
		
		if ($this->AutoFocus->value) {
			$s_foo .= ' autofocus';
		}
		
		if ($this->Required->value) {
			$s_foo .= ' required';
		}
		
		if ($this->Readonly->value) {
			$s_foo .= ' readonly';
		}
		
		if ($this->Disabled->value) {
			$s_foo .= ' disabled';
		}
		
		$s_foo .= '>' . "\n";
		
		if (issetStr($this->Description->value)) {
			$s_foo .= '<div';
			
			if (issetStr($this->DescriptionClass->value)) {
				$s_foo .= ' class="' . $this->DescriptionClass->value . '"';
			}
			
			$s_foo .= '><small>' . $this->Description->value . '</small></div>' . "\n";
		}
		
		return forestStringLib::closeHTMLTags($s_foo);
	}
}


class forestFormElementTextArea extends forestFormGeneralAttributes {
	use forestData;
	
	/* Fields */
	 
	private $Rows;
	private $Cols;
	private $Dirname;
	private $Placeholder;
	private $Readonly;
	private $Wrap;
	
	/* Properties */
	 
	/* Methods */
	 
	public function __construct() {
		parent::__construct();
		
		$this->Rows = new forestInt;
		$this->Cols = new forestInt;
		$this->Dirname = new forestString;
		$this->Placeholder = new forestString;
		$this->Readonly = new forestBool;
		$this->Wrap = new forestList(array('soft', 'hard'), 'soft');
	}
	
	public function getObjectVars() {
		return get_object_vars($this);
	}
	
	public function __toString() {
		$o_glob = forestGlobals::init();
		
		if (!issetStr($this->Name->value)) {
			$this->Name->value = $this->Id->value;
		}
		
		$s_foo = parent::__toString();
		
		$s_foo .= '<textarea id="' . $this->Id->value . '" name="' . $this->Name->value . '" class="' . $this->Class->value . '"';
		
		if (issetStr($this->Style->value)) {
			$s_foo .= ' style="' . $this->Style->value . '"';
		}
		
		if (issetStr($this->Placeholder->value)) {
			$s_foo .= ' placeholder="' . $this->Placeholder->value . '"';
		}
		
		if ($this->Rows->value > 0) {
			$s_foo .= ' rows="' . $this->Rows->value . '"';
		}
		
		if ($this->Cols->value > 0) {
			$s_foo .= ' cols="' . $this->Cols->value . '"';
		}
		
		if (issetStr($this->Dirname->value)) {
			$s_foo .= ' dirname="' . $this->Dirname->value . '"';
		}
		
		if (issetStr($this->Wrap->value) && ($this->Wrap->value != 'soft')) {
			$s_foo.= ' wrap="' . $this->Wrap->value . '"';
		}
		
		if (issetStr($this->ValMessage->value)) {
			$s_foo .= ' data-valmessage="' . $this->ValMessage->value . '"';
		}
		
		$s_foo .= ' tabindex="' . $o_glob->GetTabIndex() . '"';
		
		if ($this->AutoFocus->value) {
			$s_foo .= ' autofocus';
		}
		
		if ($this->Required->value) {
			$s_foo .= ' required';
		}
		
		if ($this->Readonly->value) {
			$s_foo .= ' readonly';
		}
		
		if ($this->Disabled->value) {
			$s_foo .= ' disabled';
		}
		
		$s_foo .= '>';
		
		if (issetStr($this->Value->value)) {
			$s_foo .= $this->Value->value;
		}
		
		$s_foo .= '</textarea>' . "\n";
		
		if (issetStr($this->Description->value)) {
			$s_foo .= '<div';
			
			if (issetStr($this->DescriptionClass->value)) {
				$s_foo .= ' class="' . $this->DescriptionClass->value . '"';
			}
			
			$s_foo .= '><small>' . $this->Description->value . '</small></div>' . "\n";
		}
		
		return forestStringLib::closeHTMLTags($s_foo);
	}
}

class forestFormElementSelect extends forestFormGeneralAttributes {
	use forestData;
	
	/* Fields */
	
	private $Multiple;	
	private $Options;
	private $Size;
	private $Data;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct() {
		parent::__construct();
		
		$this->Multiple = new forestBool;
		$this->Options = new forestArray;
		$this->Size = new forestInt(1);
		$this->Data = new forestString;
	}
	
	public function getObjectVars() {
		return get_object_vars($this);
	}
	
	public function __toString() {
		$o_glob = forestGlobals::init();
		
		if (!issetStr($this->Name->value)) {
			$this->Name->value = $this->Id->value;
		}
		
		$s_foo = parent::__toString();
		
		$s_foo .= '<select id="' . $this->Id->value . '" name="' . $this->Name->value . '" class="' . $this->Class->value . '"';
		
		if (issetStr($this->Style->value)) {
			$s_foo .= ' style="' . $this->Style->value . '"';
		}
		
		if ($this->Size->value > 0) {
			$s_foo .= ' size="' . $this->Size->value . '"';
		}
		
		if (issetStr($this->Data->value)) {
			$s_foo .= ' data-' . $this->Data->value;
		}
		
		if (issetStr($this->ValMessage->value)) {
			$s_foo .= ' data-valmessage="' . $this->ValMessage->value . '"';
		}
		
		$s_foo .= ' tabindex="' . $o_glob->GetTabIndex() . '"';
		
		if ($this->AutoFocus->value) {
			$s_foo .= ' autofocus';
		}
		
		if ($this->Required->value) {
			$s_foo .= ' required';
		}
		
		if ($this->Multiple->value) {
			$s_foo .= ' multiple';
		}
		
		if ($this->Disabled->value) {
			$s_foo .= ' disabled';
		}
		
		$s_foo .= '>' . "\n";
		
		$a_valueOptions = array();
		
		if (strpos($this->Value->value, ';') !== false) {
			$a_valueOptions = explode(';', $this->Value->value);
		}
		
		if (count($this->Options->value) > 0) {
			$b_isAssoc = ( array_keys($this->Options->value) !== range(0, count($this->Options->value) - 1) );
			$b_firstOption = true;
			
			foreach ($this->Options->value as $s_option_label => $s_option_value) {
				if (is_array($s_option_value)) {
					if (count($s_option_value) > 0) {
						$s_foo .= '<optgroup label="' . $s_option_label . '">' . "\n";
						$b_isAssocOpt = ( array_keys($s_option_value) !== range(0, count($s_option_value) - 1) );
						
						foreach ($s_option_value as $s_optgroup_option_label => $s_optgroup_option_value) {
							$s_foo .= '<option value="' . $s_optgroup_option_value . '"';
					
							if ((issetStr($this->Value->value)) && (($this->Value->value == $s_optgroup_option_value) || (in_array($s_optgroup_option_value, $a_valueOptions)))) {
								$s_foo .= ' selected';
							}
							
							$s_foo .= '>' . (($b_isAssocOpt) ? $s_optgroup_option_label : $s_optgroup_option_value) . '</option>' . "\n";
						}
						
						$s_foo .= '</optgroup>' . "\n";
					}
				} else {
					if ($b_firstOption) {
						if ($this->Required->value) {
							$s_foo .= '<option value="" disabled';
						} else {
							$s_foo .= '<option value="NULL"';
						}
						
						if ( (count($a_valueOptions) <= 0) && (!issetStr($this->Value->value)) ) {
							$s_foo .= ' selected';
						}
						
						$s_foo .= '></option>' . "\n";
						$b_firstOption = false;
					}
					
					$s_foo .= '<option value="' . $s_option_value . '"';
					
					if ((issetStr($this->Value->value)) && (($this->Value->value == $s_option_value) || (in_array($s_option_value, $a_valueOptions)))) {
						$s_foo .= ' selected';
					}
					
					$s_foo .= '>' . (($b_isAssoc) ? $s_option_label : $s_option_value) . '</option>' . "\n";
				}
			}
		}
		
		$s_foo .= '</select>' . "\n";
		
		if (issetStr($this->Description->value)) {
			$s_foo .= '<div';
			
			if (issetStr($this->DescriptionClass->value)) {
				$s_foo .= ' class="' . $this->DescriptionClass->value . '"';
			}
			
			$s_foo .= '><small>' . $this->Description->value . '</small></div>' . "\n";
		}
		
		return forestStringLib::closeHTMLTags($s_foo);
	}
}

class forestFormElementDropzone extends forestFormGeneralAttributes {
	use forestData;
	
	/* Fields */
	
	private $ContainerId;
	private $PostDataId;
	private $PostDataName;
	private $InputFileId;
	private $InputFileName;
	private $ClickId;
	private $IconClass;
	private $Text;
	private $ListContainerId;
	private $ListId;
	private $FormId;
	private $UploadStatusId;
	private $UploadDeleteId;
	private $DeletePostFieldName;
	private $UploadPostFieldNameFile;
	private $UploadPostFieldNameFilename;
	private $URIFileUploader;
	private $URIFileDeleter;
	private $PromptTitle;
	private $PromptFileName;
	private $RandomIdLength;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct() {
		parent::__construct();
		
		$this->ContainerId = new forestString;
		$this->PostDataId = new forestString;
		$this->PostDataName = new forestString;
		$this->InputFileId = new forestString;
		$this->InputFileName = new forestString;
		$this->ClickId = new forestString;
		$this->IconClass = new forestString;
		$this->Text = new forestString;
		$this->ListContainerId = new forestString;
		$this->ListId = new forestString;
		$this->FormId = new forestString;
		$this->UploadStatusId = new forestString;
		$this->UploadDeleteId = new forestString;
		$this->DeletePostFieldName = new forestString;
		$this->UploadPostFieldNameFile = new forestString;
		$this->UploadPostFieldNameFilename = new forestString;
		$this->URIFileUploader = new forestString;
		$this->URIFileDeleter = new forestString;
		$this->PromptTitle = new forestString;
		$this->PromptFileName = new forestString;
		$this->RandomIdLength = new forestInt;
	}
	
	public function getObjectVars() {
		return get_object_vars($this);
	}
	
	public function __toString() {
		$o_glob = forestGlobals::init();
		
		$s_foo = parent::__toString();
		
		// check if values are set
		
		$s_foo .= '<div id="fphp_dropzone">
			{
				"dropzoneContainerId" : "' . $this->ContainerId->value . '",
				"dropzonePostDataId" : "' . $this->PostDataId->value . '",
				"dropzonePostDataName" : "' . $this->PostDataName->value . '",
				"dropzoneInputFileId" : "' . $this->InputFileId->value . '",
				"dropzoneInputFileName" : "' . $this->InputFileName->value . '",
				"dropzoneClickId" : "' . $this->ClickId->value . '",
				"dropzoneId" : "' . $this->Id->value . '",
				"dropzoneIconClass" : "' . $this->IconClass->value . '",
				"dropzoneText" : "' . $this->Text->value . '",
				"dropzoneListContainerId" : "' . $this->ListContainerId->value . '",
				"dropzoneListId" : "' . $this->ListId->value . '",
				
				"s_dropzoneFormId" : "form#' . $this->FormId->value . '",
				"s_dropzonePostDataId" : "input#' . $this->PostDataId->value . '",
				"s_dropzoneInputFileId" : "input#' . $this->InputFileId->value . '",
				"s_dropzoneAId" : "a#' . $this->ClickId->value . '",
				"s_dropzoneId" : "div#' . $this->Id->value . '",
				
				"s_dropzoneListId" : "ul#' . $this->ListId->value . '",
				"s_uploadStatusId" : "span#' . $this->UploadStatusId->value . '_",
				"s_uploadStatusIdValue" : "' . $this->UploadStatusId->value . '_",
				"s_uploadDeleteId" : "span#' . $this->UploadDeleteId->value . '_",
				"s_uploadDeleteIdValue" : "' . $this->UploadDeleteId->value . '_",
				
				"s_deletePostFieldName" : "' . $this->DeletePostFieldName->value . '",
				"s_uploadPostFieldNameFile" : "' . $this->UploadPostFieldNameFile->value . '",
				"s_uploadPostFieldNameFileName" : "' . $this->UploadPostFieldNameFilename->value . '",
				
				"s_uriFileUploader" : "' . $this->URIFileUploader->value . '",
				"s_uriFileDeleter" : "' . $this->URIFileDeleter->value . '",
				
				"s_promptTitle" : "' . $this->PromptTitle->value . '",
				"s_promptFileName" : "' . $this->PromptFileName->value . '",
				
				"i_randomIdLength" : ' . $this->RandomIdLength->value . '
			}
		</div>';
		
		return forestStringLib::closeHTMLTags($s_foo);
	}
}

class forestFormElementRichtext extends forestFormGeneralAttributes {
	use forestData;
	
	/* Fields */
	
	private $HiddenId;
	private $ToolbarId;
	private $DataCommand;
	private $HighlightToolbarBtnClass;
	private $CreateLink;
	private $CreateLinkQuestion;
	private $CreateLinkValue;
	private $DropImage;
	private $AskImageSize;
	private $ImagesWidth;
	private $ImagesHeight;
	private $ImageWidthQuestion;
	private $ImageHeightQuestion;
	private $UndoAndRedo;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct() {
		parent::__construct();
		
		$this->HiddenId = new forestString;
		$this->ToolbarId = new forestString;
		$this->DataCommand = new forestString;
		$this->HighlightToolbarBtnClass = new forestString;
		$this->CreateLink = new forestBool;
		$this->CreateLinkQuestion = new forestString;
		$this->CreateLinkValue = new forestString;
		$this->DropImage = new forestBool;
		$this->AskImageSize = new forestBool;
		$this->ImagesWidth = new forestInt;
		$this->ImagesHeight = new forestInt;
		$this->ImageWidthQuestion = new forestString;
		$this->ImageHeightQuestion = new forestString;
		$this->UndoAndRedo = new forestBool;
	}
	
	public function getObjectVars() {
		return get_object_vars($this);
	}
	
	public function __toString() {
		$o_glob = forestGlobals::init();
		
		$s_foo = parent::__toString();
		
		// check if values are set
		
		$s_foo .= '<div id="fphp_richtext">
			{
				"s_id" : "' . $this->Id->value . ( ($this->Disabled->value) ? '_disabled' : '') . '",
				"s_hiddenId" : "' . $this->HiddenId->value . ( ($this->Disabled->value) ? '_disabled' : '') . '",
				"s_toolbarId" : "' . $this->ToolbarId->value . ( ($this->Disabled->value) ? '_disabled' : '') . '",
				"s_toolbarSelector" : "[data-toolbarId=' . $this->ToolbarId->Value . ( ($this->Disabled->value) ? '_disabled' : '') . ']",
				"s_dataCommand" : "' . $this->DataCommand->Value . '",
				"s_highlightToolbarBtn" : "' . $this->HighlightToolbarBtnClass->Value . '",
				"b_createLink" : ' . ( ($this->CreateLink->Value) ? 'true' : 'false' ) . ',
				"s_createLinkQuestion" : "' . $this->CreateLinkQuestion->Value . '",
				"s_createLinkValue" : "' . $this->CreateLinkValue->Value . '",
				"b_dropImage" : ' . ( ($this->DropImage->Value) ? 'true' : 'false' ) . ',
				"b_askImageSize" : ' . ( ($this->AskImageSize->Value) ? 'true' : 'false' ) . ',
				"i_imagesWidth" : ' . $this->ImagesWidth->Value . ',
				"i_imagesHeight" : ' . $this->ImagesHeight->Value . ',
				"s_imageWidthQuestion" : "' . $this->ImageWidthQuestion->Value . '",
				"s_imageHeightQuestion" : "' . $this->ImageHeightQuestion->Value . '",
				"b_undoAndredo" : ' . ( ($this->UndoAndRedo->Value) ? 'true' : 'false' ) . ',
				
				"s_bTitle" : "' . $o_glob->GetTranslation('richtextBold', 1) . '",
				"s_bButton" : "&lt;span style=\"font-weight: bold;\"&gt;B&lt;/span&gt;",
				"s_iTitle" : "' . $o_glob->GetTranslation('richtextItalic', 1) . '",
				"s_iButton" : "&lt;span style=\"font-style: italic; font-weight: bold;\"&gt;I&lt;/span&gt;",
				"s_uTitle" : "' . $o_glob->GetTranslation('richtextUnderline', 1) . '",
				"s_uButton" : "&lt;span style=\"text-decoration: underline; font-weight: bold;\"&gt;U&lt;/span&gt;",
				"s_sTitle" : "' . $o_glob->GetTranslation('richtextLinethrough', 1) . '",
				"s_sButton" : "&lt;span style=\"text-decoration: line-through; font-weight: bold;\"&gt;S&lt;/span&gt;",
				"s_incFontTitle" : "' . $o_glob->GetTranslation('richtextIncreaseFontsize', 1) . '",
				"s_incFontButton" : "&lt;span class=\"glyphicon glyphicon-text-size\"&gt;&lt;/span&gt;&lt;span style=\"font-size:0.75em;\" class=\"glyphicon glyphicon-triangle-top\"&gt;&lt;/span&gt;",
				"s_decFontTitle" : "' . $o_glob->GetTranslation('richtextDecreaseFontsize', 1) . '",
				"s_decFontButton" : "&lt;span class=\"glyphicon glyphicon-text-size\"&gt;&lt;/span&gt;&lt;span style=\"font-size:0.75em;\" class=\"glyphicon glyphicon-triangle-bottom\"&gt;&lt;/span&gt;",
				"s_foreColorTitle" : "' . $o_glob->GetTranslation('richtextFontColor', 1) . '",
				"s_foreColorButton" : "&lt;span class=\"glyphicon glyphicon-text-color\"&gt;&lt;/span&gt;",
				"s_backColorTitle" : "' . $o_glob->GetTranslation('richtextHiliteColor', 1) . '",
				"s_backColorButton" : "&lt;span class=\"glyphicon glyphicon-text-background\"&gt;&lt;/span&gt;",
				"s_ulTitle" : "' . $o_glob->GetTranslation('richtextUnorderedList', 1) . '",
				"s_ulButton" : "&lt;span class=\"glyphicon glyphicon-list\"&gt;&lt;/span&gt;",
				"s_olTitle" : "' . $o_glob->GetTranslation('richtextOrderedList', 1) . '",
				"s_olButton" : "&lt;span class=\"glyphicon glyphicon-list-alt\"&gt;&lt;/span&gt;",
				"s_outTitle" : "' . $o_glob->GetTranslation('richtextIndent', 1) . '",
				"s_outButton" : "&lt;span class=\"glyphicon glyphicon-indent-right\"&gt;&lt;/span&gt;",
				"s_inTitle" : "' . $o_glob->GetTranslation('richtextOutdent', 1) . '",
				"s_inButton" : "&lt;span class=\"glyphicon glyphicon-indent-left\"&gt;&lt;/span&gt;",
				"s_leftTitle" : "' . $o_glob->GetTranslation('richtextJustifyLeft', 1) . '",
				"s_leftButton" : "&lt;span class=\"glyphicon glyphicon-align-left\"&gt;",
				"s_centerTitle" : "' . $o_glob->GetTranslation('richtextJustifyCenter', 1) . '",
				"s_centerButton" : "&lt;span class=\"glyphicon glyphicon-align-center\"&gt;",
				"s_rightTitle" : "' . $o_glob->GetTranslation('richtextJustifyRight', 1) . '",
				"s_rightButton" : "&lt;span class=\"glyphicon glyphicon-align-right\"&gt;",
				"s_fullTitle" : "' . $o_glob->GetTranslation('richtextJustifyFull', 1) . '",
				"s_fullButton" : "&lt;span class=\"glyphicon glyphicon-align-justify\"&gt;",
				"s_linkTitle" : "' . $o_glob->GetTranslation('richtextHyperlink', 1) . '",
				"s_linkButton" : "&lt;span class=\"glyphicon glyphicon-link\"&gt;",
				"s_unlinkTitle" : "' . $o_glob->GetTranslation('richtextRemoveHyperlink', 1) . '",
				"s_unlinkButton" : "&lt;span class=\"glyphicon glyphicon-scissors\"&gt;&lt;/span&gt;",
				"s_undoTitle" : "' . $o_glob->GetTranslation('richtextUndo', 1) . '",
				"s_undoButton" : "&lt;span style=\"transform: rotateY(180deg);\" class=\"glyphicon glyphicon-repeat\"&gt;",
				"s_redoTitle" : "' . $o_glob->GetTranslation('richtextRedo', 1) . '",
				"s_redoButton" : "&lt;span class=\"glyphicon glyphicon-repeat\"&gt;&lt;/span&gt;",
				"s_removeTitle" : "' . $o_glob->GetTranslation('richtextDeleteFormat', 1) . '",
				"s_removeButton" : "&lt;span class=\"glyphicon glyphicon-erase\"&gt;&lt;/span&gt;",
				"s_value" : "' . ( (issetStr($this->Value->value)) ? $this->Value->value : '' ) . '",
				"b_disabled" : ' . ( ($this->Disabled->value) ? 'true' : 'false' ) . '
			}
		</div>';
		
		return forestStringLib::closeHTMLTags($s_foo);
	}
}

class forestFormElementDescription extends forestFormGeneralAttributes {
	use forestData;
	
	/* Fields */
	
	private $NoFormGroup;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct() {
		parent::__construct();
		
		$this->NoFormGroup = new forestBool;
	}
	
	public function getObjectVars() {
		return get_object_vars($this);
	}
	
	public function __toString() {
		$o_glob = forestGlobals::init();
		
		if (!issetStr($this->Name->value)) {
			$this->Name->value = $this->Id->value;
		}
		
		if (!$this->NoFormGroup->value) {
			$s_foo = parent::__toString();
		} else {
			$s_foo = '';
		}
		
		$s_foo .= '<div';
		
		if (issetStr($this->Class->value)) {
			$s_foo .= ' class="' . $this->Class->value . '"';
		}
		
		if (issetStr($this->Style->value)) {
			$s_foo .= ' style="' . $this->Style->value . '"';
		}
		
		$s_foo .= '>';
		
		if (issetStr($this->Description->value)) {
			$s_foo .= $this->Description->value;
		}
		
		$s_foo .= '</div>';
		
		return forestStringLib::closeHTMLTags($s_foo);
	}
}

class forestFormElementButton extends forestFormInputAttributes {
	use forestData;
	
	/* Fields */
	
	private $ButtonType;
	private $Data;
	private $ButtonText;
	private $NoFormGroup;
	private $WrapSpanClass;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct() {
		parent::__construct();
		
		$this->ButtonType = new forestList(array('button', 'reset', 'submit'), 'submit');
		$this->Data = new forestString;
		$this->ButtonText = new forestString;
		$this->NoFormGroup = new forestBool;
		$this->WrapSpanClass = new forestString;
	}
	
	public function getObjectVars() {
		return get_object_vars($this);
	}
	
	public function __toString() {
		$o_glob = forestGlobals::init();
		
		if (!issetStr($this->Name->value)) {
			$this->Name->value = $this->Id->value;
		}
		
		if (!$this->NoFormGroup->value) {
			$s_foo = parent::__toString();
		} else {
			$s_foo = '';
		}
		
		if (issetStr($this->WrapSpanClass->value)) {
			$s_foo .= '<span class="' . $this->WrapSpanClass->value . '">' . "\n";
		}
		
		$s_foo .= '<button type="' . $this->ButtonType->value . '" id="' . $this->Id->value . '" name="' . $this->Name->value . '"';
				
		if (issetStr($this->Class->value)) {
			$s_foo .= ' class="' . $this->Class->value . '"';
		}
		
		if (issetStr($this->Style->value)) {
			$s_foo .= ' style="' . $this->Style->value . '"';
		}
		
		if (issetStr($this->Value->value)) {
			if (!issetStr($this->ButtonText->value)) {
				$this->ButtonText->value = $this->Value->value;
			}
			
			$s_foo .= ' value="' . $this->Value->value . '"';
		}
		
		if (issetStr($this->Data->value)) {
			$s_foo .= ' data-' . $this->Data->value;
		}
		
		
		if (issetStr($this->Form->value)) {
			$s_foo .= ' form="' . $this->Form->value . '"';
		}
		
		if (issetStr($this->FormAction->value)) {
			$s_foo .= ' formaction="' . $this->FormAction->value . '"';
		}
		
		if (issetStr($this->FormEnctype->value)) {
			$s_foo .= ' formenctype="' . $this->FormEnctype->value . '"';
		}
		
		if (issetStr($this->FormMethod->value)) {
			$s_foo .= ' formmethod="' . $this->FormMethod->value . '"';
		}
		
		if ($this->FormNoValidate->value) {
			$s_foo .= ' formnovalidate="formnovalidate"';
		}
		
		if (issetStr($this->FormTarget->value)) {
			$s_foo .= ' formtarget="' . $this->FormTarget->value . '"';
		}
		
		
		$s_foo .= ' tabindex="' . $o_glob->GetTabIndex() . '"';
		
		if ($this->AutoFocus->value) {
			$s_foo .= ' autofocus';
		}
		
		if ($this->Disabled->value) {
			$s_foo .= ' disabled';
		}
		
		$s_foo .= '>';
		
		if (issetStr($this->ButtonText->value)) {
			$s_foo .= $this->ButtonText->value;
		}
		
		$s_foo .= '</button>' . "\n";
		
		if (issetStr($this->WrapSpanClass->value)) {
			$s_foo .= '</span>' . "\n";
		}
		
		if (issetStr($this->Description->value)) {
			$s_foo .= '<div';
			
			if (issetStr($this->DescriptionClass->value)) {
				$s_foo .= ' class="' . $this->DescriptionClass->value . '"';
			}
			
			$s_foo .= '><small>' . $this->Description->value . '</small></div>' . "\n";
		}
		
		return forestStringLib::closeHTMLTags($s_foo);
	}
}

class forestFormElementCaptcha extends forestFormInputAttributes {
	use forestData;
	
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getObjectVars() {
		return get_object_vars($this);
	}
		
	public function __toString() {
		$o_glob = forestGlobals::init();
		
		if (!issetStr($this->Name->value)) {
			$this->Name->value = $this->Id->value;
		}
		
		$s_foo = parent::__toString();
		
		
		$s_foo .= '<input type="text" id="' . $this->Id->value . '" name="' . $this->Name->value . '" class="' . $this->Class->value . '"';
		
		if (issetStr($this->Style->value)) {
			$s_foo .= ' style="' . $this->Style->value . '"';
		}
		
		if (issetStr($this->Placeholder->value)) {
			$s_foo .= ' placeholder="' . $this->Placeholder->value . '"';
		}
		
		$s_foo .= ' value=""';
		
		if (!($this->AutoComplete->value)) {
			$s_foo .= ' autocomplete="off"';
		}
		
		if (issetStr($this->Dirname->value)) {
			$s_foo .= ' dirname="' . $this->Dirname->value . '"';
		}
		
		if (issetStr($this->ValMessage->value)) {
			$s_foo .= ' data-valmessage="' . $this->ValMessage->value . '"';
		}
		
		
		if (issetStr($this->Form->value)) {
			$s_foo .= ' form="' . $this->Form->value . '"';
		}
		
		if (issetStr($this->FormAction->value)) {
			$s_foo .= ' formaction="' . $this->FormAction->value . '"';
		}
		
		if (issetStr($this->FormEnctype->value)) {
			$s_foo .= ' formenctype="' . $this->FormEnctype->value . '"';
		}
		
		if (issetStr($this->FormMethod->value)) {
			$s_foo .= ' formmethod="' . $this->FormMethod->value . '"';
		}
		
		if ($this->FormNoValidate->value) {
			$s_foo .= ' formnovalidate="formnovalidate"';
		}
		
		if (issetStr($this->FormTarget->value)) {
			$s_foo .= ' formtarget="' . $this->FormTarget->value . '"';
		}
		
		
		$s_foo .= ' tabindex="' . $o_glob->GetTabIndex() . '"';
		
		if ($this->AutoFocus->value) {
			$s_foo .= ' autofocus';
		}
		
		if ($this->Required->value) {
			$s_foo .= ' required';
		}
		
		if ($this->Readonly->value) {
			$s_foo .= ' readonly';
		}
		
		if ($this->Disabled->value) {
			$s_foo .= ' disabled';
		}
		
		$s_foo .= '>' . "\n";
		
		if (issetStr($this->Description->value)) {
			$s_foo .= '<div';
			
			if (issetStr($this->DescriptionClass->value)) {
				$s_foo .= ' class="' . $this->DescriptionClass->value . '"';
			}
			
			$s_foo .= '><small>' . $this->Description->value . '</small></div>' . "\n";
		}
		
		$o_glob->Security->SessionData->Add($this->Size->value, 'fphp_captcha_length');
		$s_captcha = '';
		
		for ($i = 0; $i < $this->Size->value; $i++) {
			$s_captcha .= $o_glob->Security->GenerateCaptchaCharacter();
		}
		
		$o_glob->Security->SessionData->Add($s_captcha, 'fphp_captcha');
		
		$s_foo .= '<input type="hidden" id="' . $this->Id->value . '_Hidden" name="' . $this->Name->value . '_Hidden" value="' . password_hash($s_captcha, PASSWORD_DEFAULT) . '">' . "\n";
		
		$s_foo .= '<br>' . "\n";
		
		$s_foo .= '<img src="' . forestLink::Link($o_glob->URL->Branch, 'fphp_captcha') . '" alt="fphp_captcha could not be rendered" style="margin: 0px 5px 5px 0px;">' . "\n";
		
		return forestStringLib::closeHTMLTags($s_foo);
	}
}

class forestFormElementFieldset extends forestFormGeneralAttributes {
	use forestData;
	
	/* Fields */
	
	private $Legend;
	private $LegendCSS;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct() {
		parent::__construct();
		
		$this->Legend = new forestString;
		$this->LegendCSS = new forestString;
	}
	
	public function getObjectVars() {
		return get_object_vars($this);
	}
	
	function __toString() {
		$s_foo = '<fieldset';
		
		if (issetStr($this->Class->value)) {
			$s_foo .= ' class="' . $this->Class->value . '"';
		}
		
		if (issetStr($this->Style->value)) {
			$s_foo .= ' style="' . $this->Style->value . '"';
		}
		
		$s_foo .= '>' . "\n";
		
		if (issetStr($this->Legend->value)) {
			$s_foo .= '<legend';
			
			if (issetStr($this->LegendCSS->value)) {
				$s_foo .= ' class="' . $this->LegendCSS->value . '"';
			}
			
			$s_foo .= '>' . $this->Legend->value . '</legend>' . "\n";
		}
		
		return $s_foo;
	}
}
?>