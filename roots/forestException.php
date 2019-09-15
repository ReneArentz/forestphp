<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.1.3 (0x1 0000B)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * class for own exceptions getting exception-description from database table
 * distinguish between three exception types: error, warnung, message - all three types have their own ToString-behaviour
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.0 alpha	renatus		2019-08-04	first build
 */

class forestException extends Exception {
	use forestData;
	
	/* Fields */
		
	const ERROR = 'error';
	const WARNING = 'warning';
	const MESSAGE = 'info';
	
	private $ExceptionType;
	
	/* Properties */

	/* Methods */
	
	public function __construct($p_o_content = null, $p_a_values = array(), $p_b_triggerError = false) {
        /* standard exception type */
		$this->ExceptionType = new forestString(self::ERROR, false);
		
		$o_glob = forestGlobals::init();
		
		$s_code = 0;
		
		/* if we have no code, we cannot create a proper exception */
		if (is_null($p_o_content)) {
			parent::__construct('Excpetion content missing. Please enter a content if you throw a new exception', 0);
		} else {
			if (is_string($p_o_content)) {
				/* implement additional values into exception message */
				if (!empty($p_a_values)) {
					$p_o_content = forestStringLib::sprintf2($p_o_content, $p_a_values);
				}

				/* print exception text before trigger error */
				if ($p_b_triggerError) {
					$this->code = $s_code;
					$this->message = $p_o_content;
					echo $this;
					trigger_error('Exception found', E_USER_ERROR);
				} else {
					/* create php-exception */
					parent::__construct($p_o_content, $s_code);
				}
			} else if (is_int($p_o_content)) {
				/* reading unique exception information out of exception code */
				$s_code = forestStringLib::IntToHex($p_o_content);

				$s_systemMessageType = substr($s_code, 0, 1);
				$s_systemMessageObject = substr($s_code, 1, 5);
				$s_systemMessageException = substr($s_code, 6, 2);
				
				/*echo $s_code . ' = ' . $p_o_content . '<br />';
				echo $s_systemMessageType . ' = ' . forestStringLib::HexToInt($s_systemMessageType) . '<br />';
				echo $s_systemMessageObject . ' = ' . forestStringLib::HexToInt($s_systemMessageObject) . '<br />';
				echo $s_systemMessageException . ' = ' . forestStringLib::HexToInt($s_systemMessageException) . '<br />';*/

				$o_systemMessage = new systemMessageTwig;
				
				/* get system message record */
				if ($o_systemMessage->GetRecordPrimary(array($p_o_content, $o_glob->Trunk->LanguageCode), array('IdInternal', 'LanguageCode'))) {
					/* implement additional values into exception message */
					if (!empty($p_a_values)) {
						$p_o_content = forestStringLib::sprintf2($o_systemMessage->Message, $p_a_values);
					} else {
						$p_o_content = $o_systemMessage->Message;
					}

					$this->ExceptionType->value = $o_systemMessage->Type;
				} else {
					$p_o_content = 'Exception message with the Id [0x' . $s_code . '] could not be found.';
				}

				/* create php-exception */
				parent::__construct($p_o_content, forestStringLib::HexToInt($s_code));
			} else {
				parent::__construct('Invalid exception content. Please enter a valid content which is of type [string] or [int]', 0);
			}
		}
    }

    function __toString() {
		$o_glob = forestGlobals::init();
		$s_foo = '';
		
		if ($this->getCode() == 0x10000600) {
			/* invalid session message */
			$s_foo .= '<div class="alert alert-danger alert-dismissible">';
				$s_foo .= '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' . "\n";
				$s_foo .= '<strong>Error:</strong> ' . $this->message . "\n" . '<br>' . "\n";
				$a_parameters = array();
				
				if (issetStr($o_glob->URL->Branch)) {
					$a_parameters['targetBranch'] = $o_glob->URL->Branch;
				}
				
				if (issetStr($o_glob->URL->Action)) {
					$a_parameters['targetAction'] = $o_glob->URL->Action;
				}
				
				$a_keys = array();
				$a_values = array();
				
				foreach ($o_glob->URL->Parameters as $s_key => $s_value) {
					$a_keys[] = $s_key;
					$a_values[] = $s_value;
				}
				
				$s_foo2 = implode('~', $a_keys);
				$s_foo3 = implode('~', $a_values);
				
				if ( (!(empty($s_foo2))) && (!(empty($s_foo2))) && (count($a_keys) == count($a_values)) ) {
					$a_parameters['targetParametersKeys'] = $s_foo2;
					$a_parameters['targetParametersValues'] = $s_foo3;
				}
				
				$s_foo .= '<a href="' . forestLink::Link('index', 'login', $a_parameters) . '">Login</a>' . "\n";
				$o_glob->Security->Logout();
				
			$s_foo .= '</div>' . "\n";
		} else if ($this->ExceptionType->value == self::WARNING) {
			/* warning message */
			$s_foo .= '<div class="alert alert-warning alert-dismissible">';
				$s_foo .= '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' . "\n";
				$s_foo .= '<strong>Warning:</strong> ' . $this->message . "\n" . '<br>' . "\n";
				
				$s_url = forestLink::Link($o_glob->URL->Branch);
				
				if (array_key_exists('HTTP_REFERER', $_SERVER)) {
					$s_url = $_SERVER['HTTP_REFERER'];
				}
				
				$s_foo .= '<a href="' . $s_url . '">Zur&uuml;ck</a>' . "\n";
			$s_foo .= '</div>' . "\n";
		} else if ($this->ExceptionType->value == self::MESSAGE) {
			/* info message */
			$s_foo .= '<div class="alert alert-info alert-dismissible">';
				$s_foo .= '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' . "\n";
				$s_foo .= '<strong>Info:</strong> ' . $this->message . "\n";
			$s_foo .= '</div>' . "\n";
		} else {
			/* exception message */
			$s_foo .= '<b><i>' . __CLASS__ . ':</i> Error</b>[' . $this->getCode() . '] - ' . $this->message;
			$a_traceArray = array_reverse($this->getTrace());
			
			for ($i = 0; $i < count($a_traceArray); $i++) {
				$s_traceLine = '';
				
				if (array_key_exists('file', $a_traceArray[$i])) {
					/* delete main part's document_root of trace point for clarity */
					$s_traceLine .= '<i>' . str_replace(str_replace('/', '\\', $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI']), '', $a_traceArray[$i]['file']) . '</i>';
				}
				
				if (array_key_exists('line', $a_traceArray[$i])) {
					$s_traceLine .= ' - <b>Line:</b> ' . $a_traceArray[$i]['line'];
				}
				
				if (array_key_exists('class', $a_traceArray[$i])) {
					$s_traceLine .= ' - <b>Class:</b> ' . $a_traceArray[$i]['class'];
				}
				
				if (array_key_exists('function', $a_traceArray[$i])) {
					$s_traceLine .= ' - <b>Function:</b> ' . $a_traceArray[$i]['function'];
				}
				
				$s_foo .= '<br />' . $s_traceLine;
			}
			
			$s_traceLine = '';
			$s_traceLine .= '<i>' . str_replace(str_replace('/', '\\', $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI']), '', $this->getFile()) . '</i>';
			$s_traceLine .= ' - <b>Line:</b> ' . $this->getLine();
			$s_foo .= '<br />' . $s_traceLine;
		}
		
		return $s_foo;
    }
}
?>