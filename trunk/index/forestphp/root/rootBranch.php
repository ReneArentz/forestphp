<?php

namespace fPHP\Branches;
use \fPHP\Roots\{forestString, forestList, forestNumericString, forestInt, forestFloat, forestBool, forestArray, forestObject, forestLookup};

class rootBranch extends forestBranch {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	protected function initBranch() {
		
	}
	
	protected function init() {
		$this->GenerateLandingPage();
	}
}
?>