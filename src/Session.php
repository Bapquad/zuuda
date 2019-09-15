<?php

namespace Zuuda;

class Session 
{
	
	static protected $data = array();
	
	public static function GetInstance() { return self::__getInstance(); }
	public static function Start() { return self::__start(); }
	public static function Modify( $name, $value ) { return self::__modify( $name, $value ); }
	public static function Register( $name, $value = NULL ) { return self::__register( $name, $value ); }
	public static function Unregister( $name ) { return self::__unregister( $name ); }
	public static function GetData() { return self::__getData(); } 
	public static function GetAll() { return self::__getData(); }
	public static function Get( $name = NULL ) { return self::__getVar( $name ); }
	public static function Set( $name, $value ) { return self::__setVar( $name, $value ); } 
	public static function Has( $name ) { return self::__has( $name ); } 

	final public function rootName() { return __CLASS__; }
	private function __construct() {}
	private function __clone() {}
	
	private static function __getInstance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			$_instance = new Session;
		}
		return $_instance;
	}
	
	private static function __start() 
	{
		session_start(); 
		self::$data = $_SESSION;
	}
	
	private static function __modify( $name, $value ) 
	{
		$_SESSION[$name] = $value;
		self::$data[$name] = $value;
	}

	private static function __register( $name, $value ) 
	{
		if( !array_key_exists( $name, self::$data ) ) 
		{
			$_SESSION[$name] = $value;
			self::$data[$name] = $value;
			return true;
		}
		return false;
	}

	private static function __unregister( $name ) 
	{
		if( array_key_exists( $name, self::$data ) ) 
		{
			unset( $_SESSION[$name] );
			unset( self::$data[$name] );
		}
	}

	private static function __setVar( $name, $value ) 
	{
		if( array_key_exists( $name, self::$data ) ) 
		{
			self::__modify( $name, $value );
			return true;
		}
		return false;
	} 

	private static function __getVar( $name = NULL ) 
	{
		if( NULL===$name ) 
		{
			return self::$data;
		}
		if( array_key_exists( $name, self::$data ) ) 
		{
			return self::$data[$name];
		}
		else 
		{
			return NULL;
		}
	}
	
	private static function __getData() 
	{
		return self::$data;
	} 
	
	private static function __has( $name ) 
	{ 
		return array_key_exists( $name, self::$data );
	} 
	
}