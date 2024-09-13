<?php
/**
 * global function __autoload uses this class to create object of classes who were not included before
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestany.net>
 * @copyright   (c) 2024 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     1.0.0 stable
 * @link        https://forestany.net
 * @object-id   0x1 00002
 * @since       File available since Release 0.1.0 alpha
 * @deprecated  -
 *
 * @version	log	Version			Developer	Date		Comment
 * 				0.1.0 alpha		renea		2019-08-04	first build
 * 				0.9.0 beta		renea		2020-01-27	changes for namespaces
 * 				0.9.0 beta		renea		2020-01-27	add adopted folder for root classes
 * 				1.0.0 stable	renea		2020-02-11	changed IsReadable by using function file_exists and not fopen anymore
 */

namespace fPHP\Roots;

use \fPHP\Roots\forestException as forestException;

class forestAutoLoad {
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * constructor of forestAutoLoad class, to load class file
	 *
	 * @param string $p_s_name  class name
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __construct($p_s_name) {
		/* remove namespace path from name parameter */
		if (strrpos($p_s_name, '\\') !== false) {
			$p_s_name = substr($p_s_name, strrpos($p_s_name, '\\') + 1);
		}
		
		/* check if file is readable in suspected directory and calling funtion to include class-file */
		if ($this->IsReadable('./roots/' . $p_s_name . '.php')) {
			$this->getRoot($p_s_name);
		} else if ($this->IsReadable('./roots/adopted/' . $p_s_name . '.php')) {
			$this->getAdoptedRoot($p_s_name);
		} else if ($this->IsReadable('./roots/custom/' . $p_s_name . '.php')) {
			$this->getCustomRoot($p_s_name);
		} else {
			$o_glob = \fPHP\Roots\forestGlobals::init();
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
	
	/**
	 * include function to load php class file in roots folder
	 *
	 * @param string $p_s_name  class name as filename
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static no
	 */
	private function getRoot($p_s_name) {
		if (!(@include_once('./roots/' . $p_s_name . '.php'))) {
			throw new forestException('File[' . $p_s_name . '] could not be loaded in roots-folder');
		}
	}
	
	/**
	 * include function to load php class file in adopted roots folder
	 *
	 * @param string $p_s_name  class name as filename
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static no
	 */
	private function getAdoptedRoot($p_s_name) {
		if (!(@include_once('./roots/adopted/' . $p_s_name . '.php'))) {
			throw new forestException('File[' . $p_s_name . '] could not be loaded in roots/adopted-folder');
		}
	}
	
	/**
	 * include function to load php class file in custom roots folder
	 *
	 * @param string $p_s_name  class name as filename
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static no
	 */
	private function getCustomRoot($p_s_name) {
		if (!(@include_once('./roots/custom/' . $p_s_name . '.php'))) {
			throw new forestException('File[' . $p_s_name . '] could not be loaded in roots/custom-folder');
		}
	}
	
	/**
	 * include function to load php class file in trunk folder
	 *
	 * @param string $p_s_name  class name as filename
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static no
	 */
	private function getContent($p_s_name, $p_path) {
		if (!(@include_once('./trunk/' . $p_path . $p_s_name . '.php'))) {
			throw new forestException('File[' . $p_s_name . '] could not be loaded in trunk-folder');
		}
	}
	
	/**
	 * include function to load php class file in twigs folder
	 *
	 * @param string $p_s_name  class name as filename
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static no
	 */
	private function getTwig($p_s_name) {
		if (!(@include_once('./twigs/' . $p_s_name . '.php'))) {
			throw new forestException('File[' . $p_s_name . '] could not be loaded in twigs-folder');
		}
	}
	
	/**
	 * static helper function to check if a file is readable or exists
	 *
	 * @param string $p_s_filename  path of filename + filename
	 *
	 * @return bool  true - file is readable and exists, false - file does not exists
	 *
	 * @access public
	 * @static yes
	 */
	public static function IsReadable($p_s_filename) {
		return file_exists($p_s_filename);
	}
}
?>