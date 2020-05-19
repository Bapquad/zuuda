<?php

namespace Zuuda;

define( 'M_UUID', "__mUuid" );
define( 'm_uuid', M_UUID );

class Cookie 
{
	
	static protected $data = array();
	static protected $max_expire = 2592000; // 30 days
	static protected $min_expire = 86400; // 1 day
	static protected $def_path = "/";
	private static $this = '\Zuuda\Cookie';
	public static function Instance() { return self::__getInstance(); }
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
			$_instance = new Cookie;
		}
		return $_instance;
	} 
	
	private static function __start() 
	{
		self::$data = $_COOKIE;
	} 
	
	private static function __modify( $name, $value ) 
	{
		$expire = time() + self::$max_expire;
		setcookie( $name, $value, $expire, self::$def_path );
		self::$data[ $name ] = $value;
	} 
	
	private static function __register( $name, $value ) 
	{
		if( !array_key_exists( $name, self::$data ) ) 
		{
			self::__modify( $name, $value );
			return true;
		}
		return false;
	} 
	
	private static function __unregister( $name ) 
	{
		if( array_key_exists( $name, self::$data ) ) 
		{
			$expire = time() - self::$min_expire;
			setcookie( $name, NULL, $expire, self::$def_path );
			unset( self::$data[ $name ] );
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
		if( $name === NULL ) 
		{
			return self::$data;
		}
		if( array_key_exists( $name, self::$data ) ) 
		{
			return self::$data[ $name ];
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