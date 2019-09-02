<?php

namespace Zuuda;

class Auth 
{
	
	public static function User($prop=NULL) { return self::__user($prop); } 
	public static function Get($prop) { return self::__user($prop); } 
	public static function GetAll() { return self::__getAll(); }
	public static function Instance() { return self::__getInstance(); }
	
	final public function rootName() { return __CLASS__; }
	private function __construct() {}
	private function __clone() {} 
	
	private static function __getInstance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			$_instance = new Auth;
		}
		return $_instance;
	}
	
	private static function __user($prop) 
	{
		if($prop) 
			return session::get(auth)[$prop]; 
		return self::__getInstance(); 
	} 
	
	private static function __getAll() 
	{ 
		return session::get(auth);
	}
	
	public function __get( $prop ) 
	{
		return session::get(auth)[$prop];
	}
	
}