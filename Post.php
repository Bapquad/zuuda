<?php

namespace Zuuda;

class Post 
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
			$_instance = new Post;
		}
		return $_instance;
	}
	
	private static function _getValue( $name ) 
	{
		global $_post;
		
		if( isset( $_post[ $name ] ) ) 
		{
			return $_post[ $name ];
		}
		
		return NULL;
	}
	
}