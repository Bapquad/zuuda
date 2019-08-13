<?php

namespace Zuuda;

class Put 
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
			$_instance = new Put;
		}
		return $_instance;
	}
	
	private static function _getValue( $name ) 
	{
		global $_put;
		
		if( isset( $_put[ $name ] ) ) 
		{
			return $_put[ $name ];
		}
		
		return NULL;
	}
	
}