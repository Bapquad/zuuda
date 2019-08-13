<?php

namespace Zuuda;

class Delete 
{
	public static function GetInstance() { return self::_getInstance(); }
	public static function Get( $name ) { return self::_getValue( $name ); }
	
	private function __construct() {} 
	private function __clone() {} 
	private static function _getInstance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			$_instance = new Delete;
		}
		return $_instance;
	}
	
	private static function _getValue( $name ) 
	{
		global $_delete;
		
		if( isset( $_delete[ $name ] ) ) 
		{
			return $_delete[ $name ];
		}
		
		return NULL;
	}
	
}