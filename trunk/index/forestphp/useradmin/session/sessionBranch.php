<?php
class sessionBranch extends forestBranch {
	use forestData;
	
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	protected function initBranch() {
		
	}
	
	protected function init() {
		$o_glob = forestGlobals::init();
		
		$o_session = new sessionTwig;
		$o_session->GetFirstRecord();
		
		$o_glob->PostModalForm = new forestForm($o_session, true, true);
	}
}
?>