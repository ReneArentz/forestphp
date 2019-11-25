<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.4.0 (0x1 00002)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * global function __autoload uses this class to create object of classes who were not included before
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.0 alpha	renatus		2019-08-04	first build
 */

class forestAutoLoad {
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	public function __construct($p_s_name) {
		/* check if file is readable in suspected directory and calling funtion to include class-file */
		if ($this->IsReadable('./roots/' . $p_s_name . '.php')) {
			$this->getRoot($p_s_name);
		} else if ($this->IsReadable('./roots/custom/' . $p_s_name . '.php')) {
			$this->getCustomRoot($p_s_name);
		} else {
			$o_glob = forestGlobals::init();
			$s_path = '';
			
			$a_branches = $o_glob->URL->Branches;
			
			if (!empty($a_branches)) {
				foreach($a_branches as $s_value) {
					$s_path .= $s_value . '/';
				}
			}
			
			$a_branches = $o_glob->URL->Branch;
			
			if (!empty($a_branches)) {
				$s_path .= $a_branches . '/';
			}
			
			if ($this->IsReadable('./trunk/' . $s_path . $p_s_name . '.php')) {
				$this->getContent($p_s_name, $s_path);
			} else if ($this->IsReadable('./twigs/' . $p_s_name . '.php')) {
				$this->getTwig($p_s_name);
			} else {
				$s_virtualPath = '';
				
				$a_branches = $o_glob->URL->VirtualBranches;
				
				if (!empty($a_branches)) {
					foreach($a_branches as $s_value) {
						$s_virtualPath .= $s_value . '/';
					}
				}
				
				if ($this->IsReadable('./trunk/' . $s_virtualPath . $p_s_name . '.php')) {
					$this->getContent($p_s_name, $s_virtualPath);
				} else if ($this->IsReadable('./twigs/' . $p_s_name . '.php')) {
					$this->getTwig($p_s_name);
				} else {
					throw new forestException('File[' . $p_s_name . '] not found for autoload');
				}
			}
		}
	}
	
	private function getRoot($p_s_name) {
		if (!(@include_once('./roots/' . $p_s_name . '.php'))) {
			throw new forestException('File[' . $p_s_name . '] could not be loaded in roots-folder');
		}
	}
	
	private function getCustomRoot($p_s_name) {
		if (!(@include_once('./roots/custom/' . $p_s_name . '.php'))) {
			throw new forestException('File[' . $p_s_name . '] could not be loaded in roots/custom-folder');
		}
	}
	
	private function getContent($p_s_name, $p_path) {
		if (!(@include_once('./trunk/' . $p_path . $p_s_name . '.php'))) {
			throw new forestException('File[' . $p_s_name . '] could not be loaded in trunk-folder');
		}
	}
	
	private function getTwig($p_s_name) {
		if (!(@include_once('./twigs/' . $p_s_name . '.php'))) {
			throw new forestException('File[' . $p_s_name . '] could not be loaded in twigs-folder');
		}
	}
	
	public static function IsReadable($p_s_filename) {
		/* check if file is readable by using fopen function and read access */
		if (!$o_filehandle = @fopen($p_s_filename, 'r')) {
			return false;
		}
		
		@fclose($o_filehandle);
		
		return true;
	}
}
?>