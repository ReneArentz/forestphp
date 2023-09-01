<?php
/**
 * object collection of unique twigs, objects of one single database record
 * you enter a sql query result on construction of the twig list
 * it will automatically create the twig objects depending on how many records are present in the query result
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestphp.de>
 * @copyright   (c) 2019 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     0.9.0 beta
 * @link        http://www.forestphp.de/
 * @object-id   0x1 0000D
 * @since       File available since Release 0.1.0 alpha
 * @deprecated  -
 *
 * @version log Version		Developer	Date		Comment
 * 		0.1.0 alpha	renatus		2019-08-04	first build
 * 		0.1.5 alpha	renatus		2019-10-10	added sub-record join twigs
 * 		0.9.0 beta	renatus		2020-01-31	added join column identification, not ending with $, because ocisql has some system columns ending with $
 */

namespace fPHP\Twigs;

use \fPHP\Roots\{forestString, forestList, forestNumericString, forestInt, forestFloat, forestBool, forestArray, forestObject, forestLookup};
use \fPHP\Helper\forestObjectList;
use \fPHP\Roots\forestException as forestException;

class forestTwigList {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	private $Table;
	private $Twig;
	private $Twigs;
	private $JoinTwigs;
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * constructor of forestTwigList class, creating a object list of forestTwig objects and possible join records
	 *
	 * @param string $p_s_table  name of table
	 * @param array $p_a_records  raw dataset of records as an array
	 * @param integer $p_i_resultType  standard ASSOC - ASSOC, NUM, BOTH, OBJ, LAZY
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __construct($p_s_table, $p_a_records = array(), $p_i_resultType = \fPHP\Base\forestBase::ASSOC) {
		$this->Table = new forestString($p_s_table);
		$this->JoinTwigs = null;
		
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* create twig object by table name construct parameter */
		\fPHP\Helper\forestStringLib::RemoveTablePrefix($this->Table->value);
		$s_foo = '\\fPHP\\Twigs\\' . $this->Table->value . 'Twig';
		$this->Twig = new forestObject(new $s_foo(array(), $p_i_resultType), false);
		
		/* create object list for twig objects */
		$this->Twigs = new forestObject(new forestObjectList('\\fPHP\\Twigs\\' . $this->Table->value . 'Twig'), false);
		
		/* create from each record in input parameter array an object of the twig class */
		if (is_array($p_a_records)) {
			$s_foo = '\\fPHP\\Twigs\\' . $this->Table->value . 'Twig';
			
			/* convert each record into twig object */
			foreach ($p_a_records as $a_record) {
				/* array for possible join record */
				$a_joinRecord = array();
				
				if ($p_i_resultType == \fPHP\Base\forestBase::ASSOC) {
					foreach($a_record as $s_key => $o_value) {
						/* if column name contains $, we have a value for a join record */
						if ( (strpos($s_key, '$') !== false) && (!\fPHP\Helper\forestStringLib::EndsWith($s_key, '$')) ) {
							/* decode join table and join field name */
							$a_keyProperties = explode('$', $s_key);
							/* create join table twig name */
							$s_foo2 = '\\fPHP\\Twigs\\' . $a_keyProperties[0] . 'Twig';
							
							/* add field value to join record and delete the value in the original record array */
							$a_joinRecord[$a_keyProperties[1]] = $o_value;
							unset($a_record[$s_key]);
						}
					}
				}
				
				/*echo '<pre>';
				print_r($a_record);
				echo '</pre>';*/
				
				/*echo '<pre>';
				print_r($a_joinRecord);
				echo '</pre>';*/
				
				/* create new twig object with record array and result type */
				$o_twig = new $s_foo($a_record, $p_i_resultType);
				/* add twig object to object list */
				$this->Twigs->value->Add($o_twig);
				
				if (!empty($a_joinRecord)) {
					if ($this->JoinTwigs == null) {
						/* create object list for join twig objects */
						$this->JoinTwigs = new forestObject(new forestObjectList($s_foo2), false);
					}
					
					/* create new join twig object with join record array and result type */
					$o_joinTwig = new $s_foo2($a_joinRecord, $p_i_resultType);
					/* add join twig object to join twigs object list */
					$this->JoinTwigs->value->Add($o_joinTwig);
				}
			}
		} else {
			throw new forestException('Parameter is not an array');
		}
	}
}
?>