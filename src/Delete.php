<?php

namespace Zuuda;

class Delete 
{
	private static $this = '\Zuuda\Delete';
	public static function GetInstance() { return self::__getInstance(); }
	public static function Get( $name ) { return self::__getValue( $name ); }
	
	private function __construct() {} 
	private function __clone() {} 
	private static function __getInstance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			$_instance = new Delete;
		}
		return $_instance;
	}
	
	private static function __getValue( $name ) 
	{
		global $_delete;
		
		if( isset( $_delete[ $name ] ) ) 
		{
			return $_delete[ $name ];
		}
		
		return NULL;
	}
	
}