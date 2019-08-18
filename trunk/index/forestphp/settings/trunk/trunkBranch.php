<?php
class trunkBranch extends forestBranch {
	use forestData;
	
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	protected function initBranch() {
		
	}
	
	protected function init() {
		$o_glob = forestGlobals::init();
		
		$o_trunk = new trunkTwig;
		$o_trunk->GetFirstRecord();
		
		$o_glob->PostModalForm = new forestForm($o_trunk, true, true);
	}
}
?>