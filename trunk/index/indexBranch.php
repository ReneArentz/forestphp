<?php
class indexBranch extends forestBranch {
	use forestData;
	
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	protected function initBranch() {
		
	}
	
	protected function init() {
		$o_branch = new branchTwig;
		
		$o_branches = $o_branch->GetAllRecords(true);
		
		if ($o_branches->Twigs->Count() > 0) {
			foreach ($o_branches->Twigs as $o_branchRecord) {
				echo $o_branchRecord->ShowFields(false, true) . '<br>';
			}
		}
	}
}
?>