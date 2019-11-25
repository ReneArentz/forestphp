<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.4.0 (0x1 0000D)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * object collection of unique twigs, objects of one single database record
 * you enter a sql query result on construction of the twig list
 * it will automatically create the twig objects depending on how many records are present in the query result
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.0 alpha	renatus		2019-08-04	first build
 * 0.1.5 alpha	renatus		2019-10-10	added sub-record join twigs
 */

class forestTwigList {
	use forestData;
	
	/* Fields */
	
	private $Table;
	private $Twig;
	private $Twigs;
	private $JoinTwigs;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct($p_s_table, $p_a_records = array(), $p_s_resultType = forestBase::ASSOC) {
		$this->Table = new forestString($p_s_table);
		$this->JoinTwigs = null;
		
		$o_glob = forestGlobals::init();
		
		/* create twig object by table name construct parameter */
		forestStringLib::RemoveTablePrefix($this->Table->value);
		$s_foo = $this->Table->value . 'Twig';
		$this->Twig = new forestObject(new $s_foo(array(), $p_s_resultType), false);
		
		/* create object list for twig objects */
		$this->Twigs = new forestObject(new forestObjectList($this->Table->value . 'Twig'), false);
		
		/* create from each record in input parameter array an object of the twig class */
		if (is_array($p_a_records)) {
			$s_foo = $this->Table->value . 'Twig';
			
			/* convert each record into twig object */
			foreach ($p_a_records as $a_record) {
				/* array for possible join record */
				$a_joinRecord = array();
				
				if ($p_s_resultType == forestBase::ASSOC) {
					foreach($a_record as $s_key => $o_value) {
						/* if column name contains $, we have a value for a join record */
						if (strpos($s_key, '$') !== false) {
							/* decode join table and join field name */
							$a_keyProperties = explode('$', $s_key);
							/* create join table twig name */
							$s_foo2 = $a_keyProperties[0] . 'Twig';
							
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
				$o_twig = new $s_foo($a_record, $p_s_resultType);
				/* add twig object to object list */
				$this->Twigs->value->Add($o_twig);
				
				if (!empty($a_joinRecord)) {
					if ($this->JoinTwigs == null) {
						/* create object list for join twig objects */
						$this->JoinTwigs = new forestObject(new forestObjectList($s_foo2), false);
					}
					
					/* create new join twig object with join record array and result type */
					$o_joinTwig = new $s_foo2($a_joinRecord, $p_s_resultType);
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