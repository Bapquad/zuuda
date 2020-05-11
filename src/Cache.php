<?php

namespace Zuuda;

class Cache 
{
	
	private static $class = '\Zuuda\Auth';
	final public function rootName() { return __CLASS__; }

	function get($fileName) {
		$fileName = ROOT.DS.'tmp'.DS.'cache'.DS.$fileName;
		if (file_exists($fileName)) {
			$handle = fopen($fileName, 'rb');
			$variable = fread($handle, filesize($fileName));
			fclose($handle);
			return unserialize($variable);
		} else {
			return null;
		}
	}
	
	function set($fileName,$variable) {
		$fileName = ROOT.DS.'tmp'.DS.'cache'.DS.$fileName;
		$handle = fopen($fileName, 'a');
		fwrite($handle, serialize($variable));
		fclose($handle);
	} 
	
	function clear($type) 
	{ 
		// Get the file-system modifier.
		$modifier = cFile::getInstance();
		
		switch( $type ) 
		{
			case 'template': 
				$dir_name = ROOT_DIR.'/tmp/cache/templates/layout/'; 
				$files = $modifier->listFile( $dir_name ); 
				foreach( $files as $file ) 
				{ 
				echo $file.NL;
					$modifier->remove($file); 
				} 
				break; 
				
			case 'database': 
			default: 
				$dir_name = ROOT_DIR.'/tmp/cache/';
				$files = $modifier->listFile( $dir_name );
				foreach( $files as $file ) 
				{ 
					if( false!==stripos($file, 'describe') ) 
					{
						$modifier->remove($file); 
					} 
				} 
				break; 
		} 
	} 

}
