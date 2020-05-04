<?php

namespace Zuuda;

class Zipz 
{
	static private $_file;
	
	private static $class = '\Zuuda\Auth';
	final public function rootName() { return __CLASS__; }
	private function __clone() {} 
	private function __construct() {}
	
	public static function __callStatic( $name, $args ) 
	{
		try 
		{ 
			//dd(class_exists('\ZipArchive'));
			
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() ); 
		} 
	}

}