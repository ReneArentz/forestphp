<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.7.0 (0x1 00017)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * class generate link-element out of array-parameters with static functions
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.0 alpha	renatus		2019-08-04	first build	
 */

class forestLink {
	use forestData;
	
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	/* static method to create a url for html a link value */
	public static function Link($p_s_branch = null, $p_s_action = null, array $p_a_parameters = null, $p_s_anchor = null) {
		$o_glob = forestGlobals::init();
		$a_branchTree = $o_glob->BranchTree;
		
		try {
			$s_link = './';
			
			if (!is_null($p_s_branch)) {
				$s_link .= 'index.php?'.htmlspecialchars(SID);
				
				/* check if branch exists to get branchId */
				if (!array_key_exists($p_s_branch, $a_branchTree['Name'])) {
					throw new forestException('Branch[%0] could not be found', array($p_s_branch));
				}
				
				$i_branchId = $i_parentBranchId = $a_branchTree['Name'][$p_s_branch];
				
				/* with our branchId we start a loop for all parent branches to get our necessary path for linking */
				do {
					$a_branches[] = $a_branchTree['Id'][$i_parentBranchId]['Name'];
					$i_parentBranchId = $a_branchTree['Id'][$i_parentBranchId]['ParentBranch'];
				} while ($i_parentBranchId != 0);
				
				$a_branches = array_reverse($a_branches);
				
				/* create link path */
				foreach($a_branches as $s_branch) {
					$s_link .= '/' . $s_branch;
				}
				
				if (!is_null($p_s_action)) {
					/* check if action exists */
					if (array_key_exists($p_s_action, $a_branchTree['Id'][$i_branchId]['actions']['Name'])) {
						$s_link .= '&' . $p_s_action;
					}
				}
				
				if (!is_null($p_a_parameters)) {
					$i = 0;
					$amount = count($p_a_parameters);
					$s_names = '';
					$s_values = '';
					
					/* create parameter names- and values-string for link */
					foreach ($p_a_parameters as $s_name => $s_value) {
						if (!is_null($s_value)) {
							$s_names .= $s_name;
							$s_values .= rawurlencode($s_value);
							
							if ($i != ($amount - 1)) {
								$s_names .= '&';
								$s_values .= '&';
							}
							
							$i++;
						}
					}
					
					/* for the case that parameters-array was empty */
					if ($amount != 0) {
						$s_link .= '$' . $s_names . '/' . $s_values;
					}
				}
				
				/* add anchor */
				if(!is_null($p_s_anchor)) {
					$s_link .= '#' . $p_s_anchor;
				}
			}
			
			return $s_link;
		} catch (forestException $o_exc) {
			echo '"></a>';
			throw $o_exc;
		}
	}
}
?>