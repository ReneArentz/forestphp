<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.5.0 (0x1 0001E)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * file class for creating and editing files on your own webspace
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.0 alpha	renatus		2019-08-04	first build	
 */

class forestFile {
	use forestData;
	
	/* Fields */
	
	private $LineBreak;
	private $FullFilename;
	private $Filename;
	private $FileHandle = 0;
	private $FileLength;
	private $Ready;
	private $FileContent;
	private $FileContentArray;
	
	/* Properties */
	
	public function FileLines() {
		return count($this->FileContentArray->value);
	}
	
	/* Methods */
	
	public function __construct($p_s_fullfilename, $p_b_new = false) {
		$this->LineBreak = new forestString("\r\n", false);
		$this->FullFilename = new forestString($p_s_fullfilename, false);
		$this->Filename = new forestString('', false);
		$this->FileLength = new forestInt(0, false);
		$this->Ready = new forestBool(false, false, false);
		$this->FileContent = new forestString('', false);
		$this->FileContentArray = new forestArray(array(), false);
		
		/* separate filepath from filename */
		$a_temp = explode('/', $this->FullFilename->value);
		$i_amount = count($a_temp);
		$this->Filename->value = $a_temp[$i_amount - 1];
		
		if ($p_b_new) {
			/* check if file exists */
			if (is_writable($this->FullFilename->value)) {
				throw new forestException('File[%0] does not exists', array($this->Filename->value));
			}
			
			/* check if file can be created */
			if (!($this->FileHandle = fopen($this->FullFilename->value, 'w'))) {
				throw new forestException('File[%0] cannot be created', array($this->Filename->value));
			}
			
			/* calculate filelength */
			$this->FileLength->value = filesize($this->FullFilename->value);
			
			/* close file */
			fclose($this->FileHandle);
			
			/* set ready flag that you can use all other methods on the file */
			$this->Ready->value = true;
		} else {
			if (!is_writable($this->FullFilename->value)) {
				throw new forestException('File[%0] does not exists', array($this->Filename->value));
			}
			
			if (!($this->FileHandle = fopen($this->FullFilename->value, 'r'))) {
				throw new forestException('File[%0] read access not possible', array($this->Filename->value));
			}
			
			/* calculate filelength */
			$this->FileLength->value = filesize($this->FullFilename->value);
			
			/* if filelength > 0, read content of the file */
			if ($this->FileLength->value > 0) {
				$this->FileContent->value = fread($this->FileHandle, $this->FileLength->value);
				$this->FileContentArray->value  = explode($this->LineBreak->value, $this->FileContent->value);
			}
			
			/* if last element is empty, delete it */
			if (empty($this->FileContentArray->value[count($this->FileContentArray->value) - 1])) {
				unset($this->FileContentArray->value[count($this->FileContentArray->value) - 1]);
			}
			
			/* close file */
			fclose($this->FileHandle);
			
			/* set ready flag that you can use all other methods on the file */
			$this->Ready->value = true;
		}
	}
	
	public function DeleteFile() {
		/* delete file */
		if (!(unlink($this->FullFilename->value))) {
			throw new forestException('File[%0] could not be deleted', array($this->Filename->value));
		}
		
		/* reset ready flag */
		$this->Ready->value = false;
	}
	
	public function ReadLine($p_i_line = 0) {
		if (!($this->Ready->value)) {
			throw new forestException('File[%0] cannot be used', array($this->Filename->value));
		}
		
		/* nothing to read in file */
		if ( ($this->FileLength->value == 0) || (!is_writable($this->FullFilename->value)) ) {
			return '';
		}
		
		/* line number must be positive */
		if (!($p_i_line - 1 >= 0)) {
			throw new forestException('Line number[%0] must be greater 0', array($p_i_line));
		}
		
		/* line number must be a numeric value */
		if (!is_numeric($p_i_line - 1)) {
			throw new forestException('Invalid line[%0]', array($p_i_line));
		}
		
		/* if line does not exists */
		if ($p_i_line - 1 >= count($this->FileContentArray->value)) {
			throw new forestException('Invalid line[%0]', array($p_i_line));
		}
		
		/* return line in file content */
		return $this->FileContentArray->value[$p_i_line - 1];
	}
	
	public function WriteLine($p_s_value, $p_i_line = 0) {
		if (!($this->Ready->value)) {
			throw new forestException('File[%0] cannot be used', array($this->Filename->value));
		}
		
		if ($p_i_line == 0) {
			$this->FileContentArray->value[] = $p_s_value;
		} else {
			/* line number must be positive */
			if (!($p_i_line - 1 >= 0)) {
				throw new forestException('Line number[%0] must be greater 0', array($p_i_line));
			}
			
			/* line number must be a numeric value */
			if (!is_numeric($p_i_line - 1)) {
				throw new forestException('Invalid line[%0]', array($p_i_line));
			}
			
			/* if line does not exists */
			if ($p_i_line - 1 >= count($this->FileContentArray->value)) {
				throw new forestException('Invalid line[%0]', array($p_i_line));
			}
			
			$arr_start = array();
			
			/* save all lines from file before the new line in temp-array */
			for ($i = 0; $i < ($p_i_line - 1); $i++) {
				array_push($arr_start, $this->FileContentArray->value[$i]);
			}
			
			/* insert new line */
			$arr_start[] = $p_s_value;
			$arr_end = array();
			
			/* save all lines from file after the new line in temp-array */
			for ($i = ($p_i_line - 1); $i < count($this->FileContentArray->value); $i++) {
				array_push($arr_end, $this->FileContentArray->value[$i]);
			}
			
			/* concat both temp-arrays as new file content */
			$this->FileContentArray->value = array_merge($arr_start, $arr_end);
		}
		
		/* update file content */
		$this->SetFileContent();
	}
	
	public function ReplaceLine($p_s_value, $p_i_line) {
		if (!($this->Ready->value)) {
			throw new forestException('File[%0] cannot be used', array($this->Filename->value));
		}
		
		/* line number must be positive */
		if (!($p_i_line - 1 >= 0)) {
			throw new forestException('Line number[%0] must be greater 0', array($p_i_line));
		}
		
		/* line number must be a numeric value */
		if (!is_numeric($p_i_line)) {
			throw new forestException('Invalid line[%0]', array($p_i_line));
		}
		
		/* if line does not exists */
		if ($p_i_line - 1 >= count($this->FileContentArray->value)) {
			throw new forestException('Invalid line[%0]', array($p_i_line));
		}
		
		/* replace line */
		$this->FileContentArray->value[$p_i_line - 1] = $p_s_value;
		
		/* update file content */
		$this->SetFileContent();
	}
	
	public function DeleteLine($p_i_line) {
		if (!($this->Ready->value)) {
			throw new forestException('File[%0] cannot be used', array($this->Filename->value));
		}
		
		/* line number must be positive */
		if (!($p_i_line - 1 >= 0)) {
			throw new forestException('Line number[%0] must be greater 0', array($p_i_line));
		}
		
		/* line number must be a numeric value */
		if (!is_numeric($p_i_line)) {
			throw new forestException('Invalid line[%0]', array($p_i_line));
		}
		
		/* if line does not exists */
		if ($p_i_line - 1 >= count($this->FileContentArray->value)) {
			throw new forestException('Invalid line[%0]', array($p_i_line));
		}
		
		/* delete line */
		$temp_arr = array();
			
		/* save all lines from file except the line with paramters line number */
		for ($i = 0; $i < count($this->FileContentArray->value); $i++) {
			if ($i != ($p_i_line - 1)) {
				array_push($temp_arr, $this->FileContentArray->value[$i]);
			}
		}
		
		/* replace content */
		$this->FileContentArray->value = $temp_arr;
		
		/* update file content */
		if (count($this->FileContentArray->value) <= 0) {
			$this->TruncateContent();
		} else {
			$this->SetFileContent();
		}
	}
	
	public function ReplaceContent($p_s_value) {
		if (!($this->Ready->value)) {
			throw new forestException('File[%0] cannot be used', array($this->Filename->value));
		}
		
		if (!empty($p_s_value)) {
			/* overwrite file content string */
			$this->FileContent->value = $p_s_value;
			
			/* generate file content array with the new file content string and CRLF as separate sign */
			$this->FileContentArray->value = explode($this->LineBreak->value, $this->FileContent->value);
			
			/* update file content */
			$this->SetFileContent();
		} else {
			$this->TruncateContent();
		}
	}
	
	public function TruncateContent() {
		if (!($this->Ready->value)) {
			throw new forestException('File[%0] cannot be used', array($this->Filename->value));
		}
		
		/* file does not exists */
		if (!is_writable($this->FullFilename->value)) {
			throw new forestException('File[%0] does not exists', array($this->Filename->value));
		}
		
		/* file cannot be open */
		if (!($this->FileHandle = fopen($this->FullFilename->value, 'r+'))) {
			throw new forestException('File[%0] read access not possible', array($this->Filename->value));
		}
		
		$this->FileContent->value = '';
		$this->FileContentArray->value = array();
		
		/* truncate file */
		ftruncate($this->FileHandle, 0);
		
		/* calculate filelength */
		$this->FileLength->value = filesize($this->FullFilename->value);
		
		/* close file */
		fclose($this->FileHandle);
	}
	
	private function SetFileContent() {
		if (!($this->Ready->value)) {
			throw new forestException('File[%0] cannot be used', array($this->Filename->value));
		}
		
		/* file does not exists */
		if (!is_writable($this->FullFilename->value)) {
			throw new forestException('File[%0] does not exists', array($this->Filename->value));
		}
		
		/* file cannot be open */
		if (!($this->FileHandle = fopen($this->FullFilename->value, 'r+'))) {
			throw new forestException('File[%0] read access not possible', array($this->Filename->value));
		}
		
		/* generate file content string with CRLF as separate sign */
		$this->FileContent->value = implode($this->LineBreak->value, $this->FileContentArray->value);
		
		/* set file pointer at start of the file */
		fseek($this->FileHandle,0);
		
		/* write file content */
		if (!fwrite($this->FileHandle, $this->FileContent->value)) {
			throw new forestException('File[%0] write access not possible', array($this->Filename->value));
		}
		
		/* delete old rest in file */
		ftruncate($this->FileHandle, strlen($this->FileContent->value));
		
		/* calculate filelength */
		$this->FileLength->value = filesize($this->FullFilename->value);
		
		/* close file */
		fclose($this->FileHandle);
	}
	
	public static function CreateTempFileCSV($p_s_name) {
		$o_glob = forestGlobals::init();
		
		/* create temporary file */
		$s_temp_file = tempnam(sys_get_temp_dir(), $p_s_name);
		$s_old_temp_file = $s_temp_file;
		
		/* get filename */
		$a_temp = explode('\\', $s_temp_file);
		$s_filename = end($a_temp);
		
		/* generate random hash and get only the last 6 characters */
		$s_hash = $o_glob->Security->GenUUID();
		$s_hash = substr($s_hash, -6);
		
		/* build new filename and rename temporary file, change extension to .csv */
		$s_new_filename = $p_s_name . '_' . $s_hash . '.csv';
		$s_temp_file = str_replace($s_filename, $s_new_filename, $s_temp_file);
		@rename($s_old_temp_file, $s_temp_file);
		
		if (!@copy($s_temp_file, './temp_files/' . $s_new_filename)) {
			throw new forestException('File[%0] could not be copied to [%1]', array($s_temp_file, './temp_files/' . $s_new_filename));
		}
		
		if (!@unlink($s_temp_file)) {
			throw new forestException('File[%0] could not be deleted', array($s_temp_file));
		}
		
		return $s_new_filename;
	}
	
	public static function CreateFileFolderStructure($p_s_branch) {
		$o_glob = forestGlobals::init();
		
		$o_glob->SetVirtualTarget($p_s_branch);
		
		/* generate path */
		$s_path = '';
		
		if (count($o_glob->URL->VirtualBranches) > 0) {
			foreach($o_glob->URL->VirtualBranches as $s_value) {
				$s_path .= $s_value . '/';
			}
		} else {
			$s_path .= $o_glob->URL->VirtualBranch . '/';
		}
		
		/* get directory content of current page into array */
		$a_dirContent = scandir('./trunk/' . $s_path);
		
		/* if we cannot find fphp_files folder and we cannot create fphp_files folder as new directory */
		if (!in_array('fphp_files', $a_dirContent)) {
			if (!mkdir('./trunk/' . $s_path . 'fphp_files/')) {
				throw new forestException('Cannot create directory [%0].', array('./trunk/' . $s_path . 'fphp_files/'));
			}
		}
		
		/* get directory content of current page fphp_files directory */
		$a_dirContent = scandir('./trunk/' . $s_path . 'fphp_files/');
		
		/* if we have not 258 directories */
		if (count($a_dirContent) != 258) {
			for ($i = 0; $i < 256; $i++) {
				/* hex folder name */
				$s_folder = forestStringLib::IntToHex($i);
				
				/* with leading zero */
				if (strlen($s_folder) == 1) {
					$s_folder = '0' . $s_folder;
				}
				
				/* if we cannot find folder and we cannot create folder as new directory */
				if (!in_array($s_folder, $a_dirContent)) {
					if (!mkdir('./trunk/' . $s_path . 'fphp_files/' . $s_folder . '/')) {
						throw new forestException('Cannot create directory [%0].', array('./trunk/' . $s_path . 'fphp_files/' . $s_folder . '/'));
					}
				}
			}
		}
	}
	
	public static function RemoveDirectoryRecursive($p_s_directory) {
		if (is_dir($p_s_directory)) {
			$a_objects = scandir($p_s_directory);
			
			foreach ($a_objects as $o_object) {
				if ( ($o_object != '.') && ($o_object != '..') ) {
					if (filetype($p_s_directory . '/' . $o_object) == 'dir') { 
						forestFile::RemoveDirectoryRecursive($p_s_directory . '/' . $o_object); 
					} else {
						unlink($p_s_directory . '/' . $o_object);
					}
				}
			}
			
			reset($a_objects);
			rmdir($p_s_directory);
		}
	}
	
	public static function CopyRecursive($p_s_srcDirectory, $p_s_dstDirectory) {
        if (file_exists($p_s_dstDirectory)) {
            forestFile::RemoveDirectoryRecursive($p_s_dstDirectory);
		}
		
        if (is_dir($p_s_srcDirectory)) {
            mkdir($p_s_dstDirectory);
            $files = scandir($p_s_srcDirectory);
			
            foreach ($files as $file) {
                if ( ($file != ".") && ($file != "..") ) {
                    forestFile::CopyRecursive("$p_s_srcDirectory/$file", "$p_s_dstDirectory/$file");
				}
			}
        } else if (file_exists($p_s_srcDirectory)) {
            copy($p_s_srcDirectory, $p_s_dstDirectory);
		}
    }
}
?>