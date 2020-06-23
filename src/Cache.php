<?php

namespace Zuuda;

use Zuuda\FileUploader;
use Zuuda\Fx;

class Cache 
{
	
	private static $this = '\Zuuda\Cache';
	static $describeDir = "tmp/cache/";
	static $templateDir = "tmp/cache/templates/layout/";
	final public function rootName() { return __CLASS__; }
	static $upload_file = [];
	static $upload_type = [];
	static $upload_size = 0;
	static $upload_thread = "";

	function get($fileName) 
	{
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
	
	final static public function LoadTemplateId() 
	{ 
		$id = file_get_contents(ROOT_DIR.self::$templateDir.'empty'); 
		return $id;
	} 
	
	final static public function clear($type) 
	{ 
		// Get the file-system modifier.
		$modifier = cFile::getInstance();
		
		switch( $type ) 
		{
			case 'database': 
				$dir_name = ROOT_DIR.self::$describeDir;
				$files = $modifier->listFile( $dir_name );
				foreach( $files as $file ) 
				{ 
					if( false!==stripos($file, 'describe') ) 
					{
						$modifier->remove($file); 
					} 
				} 
				return;
			case 'template': 
				$dir_name = ROOT_DIR.self::$templateDir; 
				$files = $modifier->listFile( $dir_name ); 
				fx::touch($dir_name.'empty', uniqid());
				break; 
			case 'upload-temp': 
				$dir_name = ROOT_DIR.FileUploader::$uploadDir;
				$files = $modifier->listFile( $dir_name ); 
				break; 
			default: 
				break; 
		} 

		foreach( $files as $file ) 
		{ 
			$modifier->remove($file); 
		} 
	} 
	
	final static public function clearUploadTemp() 
	{
		if( !strlen(self::$upload_thread)&&count(self::$upload_file) ) 
		{ 
			$tmps = \Zuuda\Cache::$upload_file; 
			foreach( $tmps as $tmp ) 
			{ 
				global $_server;
				if( \Zuuda\Fx::file_exists($tmp['tmp_name']) ) 
				{ 
					\Zuuda\Fx::unlink($tmp['tmp_name']);
				} 
			} 
		}
	}

}
