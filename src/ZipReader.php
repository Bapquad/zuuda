<?php

namespace Zuuda;

use Exception;

class ZipReader 
{ 

	private $_resource;
	private $_entryRes;
	private static $this = '\Zuuda\ZipReader';
	final public function rootName() { return __CLASS__; } 
	final private function __clone() {} 
	final private function __construct($resource) 
	{
		$this->_resource = $resource;
		$this->entryOpen(); 
	} 
	
	final static public function open( $path ) 
	{ 
		return self::instance($path);
	} 
	
	final static public function instance($path) 
	{ 
		$res = zip_open($path);
		if( is_resource($res) ) 
		{
			return new ZipReader($res); 
		} 
		else 
		{ 
			return false;
		} 
	} 
	
	final public function ReadEntry( $length = 1024 ) 
	{ 
		if( $this->_resource && $this->_entryRes ) 
		{ 
			return zip_entry_read( $this->_entryRes, $length ); 
		} 
		return false;
	} 
	
	final public function CloseEntry() 
	{ 
		if( $this->_entryRes ) 
		{ 
			return zip_entry_close( $this->_entryRes ); 
		} 
		return false;
	} 
	
	final public function OpenEntry( $mode="rb" ) 
	{ 
		if( $this->_resource && $this->_entryRes ) 
		{ 
			return zip_entry_open( $this->_resource, $this->_entryRes ); 
		} 
		return false; 
	} 
	
	final public function EntryContent( $length = 1024 ) 
	{ 
		$output = EMPTY_CHAR; 
		
		if( $this->_resource && $this->_entryRes ) 
		{ 
			if( zip_entry_open($this->_resource, $this->_entryRes) ) 
			{ 
				if( $this->_entryRes ) 
				{ 
					$data = zip_entry_read( $this->_entryRes, $length ); 
					if( zip_entry_close($this->_entryRes) ) 
					{ 
						$output = $data;
					} 
				}
			} 
		} 
		return $output;
	} 
	
	final public function EntryZipSize() 
	{ 
		if( $this->_entryRes ) 
		{ 
			return zip_entry_compressedsize( $this->_entryRes ); 
		}
	}
	
	final public function EntryZipMethod() 
	{ 
		if( $this->_entryRes ) 
		{ 
			return zip_entry_compressionmethod( $this->_entryRes ); 
		} 
	} 
	
	final public function EntrySize() 
	{ 
		if( $this->_entryRes ) 
		{
			return zip_entry_filesize( $this->_entryRes ); 
		} 
	} 
	
	final public function EntryName() 
	{ 
		if( $this->_entryRes ) 
		{
			return zip_entry_name( $this->_entryRes ); 
		} 
	} 
	
	final public function EntryIsDir() 
	{ 	
		return 0===$this->entrySize() && 0===$this->entryZipSize() && "stored"===$this->entryZipMethod();
	} 
	
	final public function EntryIsFile() 
	{ 
		return !$this->EntryIsDir(); 
	} 
	
	final public function entryOpen() 
	{ 
		return $this->entryNext(); 
	} 
	
	final public function entryNext() 
	{ 
		try 
		{
			if( NULL!==$this->_resource ) 
			{ 
				$this->_entryRes = zip_read($this->_resource); 
			}
		}
		catch( Exception $e ) 
		{ 
			if( \Zuuda\Config::get(DEVELOPER_WARNING) ) 
			{
				abort( 500, $e->getMessage() ); 
			}
		} 
		return $this;
	}
	
	final public function close() 
	{ 
		if( is_resource($this->_resource) ) 
		{ 
			zip_close($this->_resource); 
			return true;
		} 
		return false;
	} 
	
} 