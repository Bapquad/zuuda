<?php

namespace Zuuda;

class Auth 
{
	
	public static function User($prop=NULL) { return self::_user($prop); } 
	public static function Get($prop) { return self::_user($prop); } 
	public static function GetAll() { return self::_getAll(); }
	public static function Instance() { return self::_getInstance(); }
	
	final public function rootName() { return __CLASS__; }
	private function __construct() {}
	private function __clone() {} 
	
	private static function _getInstance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			$_instance = new Auth;
		}
		return $_instance;
	}
	
	private static function _user($prop) 
	{
		if($prop) 
			return session::get(auth)[$prop]; 
		return self::_getInstance(); 
	} 
	
	private static function _getAll() 
	{ 
		return session::get(auth);
	}
	
	public function __get( $prop ) 
	{
		return session::get(auth)[$prop];
	}
	
}