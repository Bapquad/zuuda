<?php

namespace Zuuda;

use ZipArchive;
use Exception; 

class Zipzer 
{
	private $_zip;
	
	private static $this = '\Zuuda\Zipzer'; 
	const OVERWRITE = 1;
	const CREATE 	= 2;
	const RDONLY 	= 3;
	const EXCL 		= 4;
	const CHECKONS 	= 5;
	final public function rootName() { return __CLASS__; }
	final private function __clone() {} 
	
	final private function __construct(ZipArchive $_zip) 
	{
		$this->_zip = $_zip; 
	} 
	
	final static private function __instance( $filePath, $flag=0 ) 
	{ 
		if( !class_exists('ZipArchive') ) 
			return NULL; 
		
		if( self::CHECKONS<$flag ) 
			return NULL; 
		
		$openResult = false;
		$_zip = new ZipArchive(); 
		switch( $flag ) 
		{ 
			case self::OVERWRITE: 
				$flag = ZipArchive::OVERWRITE; 
				break; 
			case self::CREATE: 
				$flag = ZipArchive::CREATE; 
				break; 
			case self::RDONLY: 
				$flag = ZipArchive::RDONLY; 
				break; 
			case self::EXCL: 
				$flag = ZipArchive::EXCL; 
				break; 
			case self::CHECKONS:
				$flag = ZipArchive::CHECKONS; 
				break; 
			case 0: 
			default: 
				if( TRUE===$_zip->open($filePath) ) 
					$openResult = true; 
				else 
					return NULL;
				break;
		} 
		if( false===$openResult ) 
		{ 
			if( TRUE!==$_zip->open($filePath, $flag) ) 
				return NULL; 
		} 
		return new Zipzer($_zip); 
	} 
	
	public static function __callStatic( $fn, $args ) 
	{
		$fn = strtolower($fn); 
		try 
		{ 
			switch( $fn ) 
			{ 
				case "instance": 
				case "open": 
					return forward_static_call_array(array(self::$this, '__instance'), $args);
					break; 
				default: 
					throw new Exception( "Zipzer::<b>{$fn}()</b> does not exist." ); 
			} 
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() ); 
		} 
	} 
	
	public function __call( $fn, $args ) 
	{ 
		switch( $fn ) 
		{ 
			default:
				call_user_func_array( array($this->_zip, $fn), $args ); 
				break;
		} 
		return $this; 
	} 
	
	public function __get( $property ) 
	{ 
		return $this->_zip->$property; 
	} 

}