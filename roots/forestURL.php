<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.1.3 (0x1 00005)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * class holding specific url information based on an arranged url-format
 * for more information about the arranged url-format, see the documentation
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.0 alpha	renatus		2019-08-04	first build
 * 0.1.2 alpha	renatus		2019-08-26	added parameter identification for sort and limit
 */

class forestURL {
	use forestData;
	
	/* Fields */
	
	private $Branches;
	private $Branch;
	private $BranchTitle;
	private $Action;
	private $Parameters;
	private $BranchId;
	private $ActionId;
	private $LastBranchId;
	private $LastActionId;
	private $ShowNavigation;
	
	private $VirtualBranch;
	private $VirtualBranchId;
	private $VirtualBranches;
	private $VirtualActionId;
	private $VirtualParameters;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct($p_b_show = false) {
		$this->Branches = new forestArray(null, false);
		$this->Branch = new forestString('index', false);
		$this->BranchTitle = new forestString('Home');
		$this->Action = new forestString; // ''
		$this->Parameters = new forestArray(null, false);
		$this->BranchId = new forestInt(null, false); // 1
		$this->ActionId = new forestInt; // 0
		$this->LastBranchId = new forestInt; // 1
		$this->LastActionId = new forestInt; // 0
		$this->ShowNavigation = new forestBool(null, false); 

		$this->VirtualBranch = new forestString;
		$this->VirtualBranchId = new forestInt; // 0
		$this->VirtualBranches = new forestArray;
		$this->VirtualActionId = new forestInt; // 0
		$this->VirtualParameters = new forestArray;
		
        $this->HandleUrl($p_b_show);
	}
	
	/* important function to read the information in url's arranged format */
	/* first parameter controls rendering some url information receiving from arranged url-format on screen */
	private function HandleUrl($p_b_show = false) {
		if (strpos($_SERVER['REQUEST_URI'], '.php?/') !== false) {
			/* split url in two parts */
			$splitUri = preg_split('(\.php\?([a-zA-Z0-9])*/)', $_SERVER['REQUEST_URI']);
			/* work with the seconds part */
			$uri = $splitUri[1];
			
			if ($p_b_show) {
				echo 'URI ELEMENTS:<br />';
				print_r($splitUri);
				echo '<br />';
				echo 'SPLITTED REQUEST_URI: ' . $uri. '<br />';
			}
			
			/* $ sign indicates action information in url */
			if (strpos($uri, '$') !== false) {
				$uriSplit = explode('$', $uri);
				
				/* only one action allowed */
				if (count($uriSplit) > 2) {
					return;
				}
				
				/* slash indicates path and parameter information */
				$uriSite = explode('/', $uriSplit[0]);
				$uriParameters = explode('/', $uriSplit[1]);
				
				if (count($uriParameters) != 2) {
					return;
				}
			} else {
				/* maybe no action information but branch path */
				$uriSite = explode ('/', $uri);
				$uriParameters = array();
			}
			
			/* branch path information available */
			if (count($uriSite) > 0) {
				$temp = array();
				
				/* iterate information array to save path to branch */
				foreach ($uriSite as $value) {
					if (strlen($value) != 0) {
						$temp[] = $value;
					}
				}
				
				$uriSite = $temp;
				
				$this->Branches->value = $uriSite;
				array_pop($this->Branches->value);
				
				/* check branch path for invalid signs */
				foreach ($this->Branches->value as $value) {
					if (strpos($value, '&') !== false) {
						throw new Exception('Invalid \'&\'-sign within url-path');
					}
				}
				
				if ($p_b_show) {
					echo '<pre>Branches';
					print_r($this->Branches->value);
					echo '</pre>';
				}
				
				if (strpos($uriSite[(count($uriSite)-1)], '&') !== false) {
					$splitUriSite = explode('&', $uriSite[(count($uriSite)-1)]);
					
					if (count($splitUriSite) != 2) {
						throw new Exception('Invalid arguments within url');
					}
					
					$this->Branch->value = $splitUriSite[0];
					$this->Action->value = $splitUriSite[1];
				} else {
					$this->Branch->value = $uriSite[(count($uriSite)-1)];
				}
				
				if ($p_b_show) {
					echo 'Branch: ' . $this->Branch->value . "<br />\n";
					echo 'Action: ' . $this->Action->value . "<br />\n";
				}
			}
			
			/* read parameter information by saving keys and vales out of url information */
			if (count($uriParameters) > 0) {
				$uriParametersKeys = explode('&', $uriParameters[0]);
				$uriParametersValues = explode('&', $uriParameters[1]);
				
				if (count($uriParametersKeys) != count($uriParametersValues)) {
					return;
				}
				
				$this->Parameters->value = array_combine($uriParametersKeys, $uriParametersValues);
			}
		}
		
		if ($p_b_show) {
			echo '<pre>HandleUrl<br>';
			echo 'BranchId: '; var_dump($this->BranchId->value);
			echo 'Branch: '; var_dump($this->Branch->value);
			echo 'ActionId: '; var_dump($this->ActionId->value);
			echo 'Action: '; var_dump($this->Action->value);
			echo 'Parameters:';
			print_r($this->Parameters->value);
			echo '</pre>';
		}
	}
	
	/* retrieving detail information out of database based on information read out of url */
	public function RetrieveInformationByURL($p_b_show = false) {
		$o_glob = forestGlobals::init();
		
		if (issetStr($this->Branch->value)) {
			/* check if branch really exists */
			if (!array_key_exists($this->Branch->value, $o_glob->BranchTree['Name'])) {
				throw new forestException('Branch[%0] could not be found', array($this->Branch->value));
			}
			
			$this->BranchId->value = $o_glob->BranchTree['Name'][$this->Branch->value];
			$this->BranchTitle->value = $o_glob->BranchTree['Id'][$this->BranchId->value]['Title'];
			$this->ShowNavigation->value = $o_glob->BranchTree['Id'][$this->BranchId->value]['Navigation'];
			
			/* check if action really exists */
			if (issetStr($this->Action->value)) {
				if (!array_key_exists($this->Action->value, $o_glob->BranchTree['Id'][$this->BranchId->value]['actions']['Name'])) {
					throw new forestException('Action[%0] could not be found', array($this->Action->value));
				}
				
				$this->ActionId->value = $o_glob->BranchTree['Id'][$this->BranchId->value]['actions']['Name'][$this->Action->value];
			} else { /* check if init action exists. it is needed to render at least something for user output */
				if (!array_key_exists('init', $o_glob->BranchTree['Id'][$this->BranchId->value]['actions']['Name'])) {
					throw new forestException('Action[init] could not be found');
				}
				
				$this->ActionId->value = $o_glob->BranchTree['Id'][$this->BranchId->value]['actions']['Name']['init'];
			}
			
			/* check parameters */
			if (count($this->Parameters->value) > 0) {
				foreach($this->Parameters as $s_key => $s_value) {
					/* only allow alphanumeric characters a-z and A-Z, numbers 0-9 and the three characters -, _ and ~ (only in value), otherwise ignore this parameter */
					if ( (preg_match('/[^A-Za-z0-9\-\_$]/', rawurldecode($s_key))) || (preg_match('/[^A-Za-z0-9\-\_$~]/', rawurldecode($s_value))) ) {
						unset($this->Parameters->value[$s_key]);
						continue;
					}
					
					/* decode url-encoded strings like %20 etc. */
					$this->Parameters->value[$s_key] = rawurldecode($s_value);
					
					/* exception paramater-format to recognize sort-fields */
					if ($s_key[0] == '_') {
						$o_glob->Sorts->Add(new forestSort(substr($s_key, 1), ( ($s_value == 'true') ? true : false ) ), substr($s_key, 1));
					}
					
					/* exception paramater-format for paging */
					if ($s_key == 'page') {
						if (is_numeric($s_value)) {
							$o_glob->Limit->Page = intval($s_value);
						}
					}
					
					/* exception parameter-format for adding hidden columns */
					if ( ($s_key[0] == '-') && ($s_value == '-add') ) {
						$o_glob->AddHiddenColumns->Add(substr($s_key, 1));
					}
				}
			}
		}
		
		if ($this->BranchId->value > 0) {
			$this->ShowNavigation->value = true;
		}
		
		if ($p_b_show) {
			echo '<pre>RetrieveInformationByURL<br>';
			echo 'BranchId: '; var_dump($this->BranchId->value);
			echo 'Branch: '; var_dump($this->Branch->value);
			echo 'ActionId: '; var_dump($this->ActionId->value);
			echo 'Action: '; var_dump($this->Action->value);
			echo 'Parameters:';
			print_r($this->Parameters->value);
			echo '</pre>';
		}
	}
}
?>