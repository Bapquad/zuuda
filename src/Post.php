<?php

namespace Zuuda;

class Post 
{
	private static $this = '\Zuuda\Post';
	public static function GetInstance() { return self::__getInstance(); }
	public static function Get( $name ) { return self::__getValue( $name ); }
	
	private function __construct() {} 
	private function __clone() {} 
	private static function __getInstance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			$_instance = new Post;
		}
		return $_instance;
	}
	
	private static function __getValue( $name ) 
	{
		global $_post;
		
		if( isset( $_post[ $name ] ) ) 
		{
			return $_post[ $name ];
		}
		
		return NULL;
	}
	
}