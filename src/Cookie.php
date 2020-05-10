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
	public static function GetInstance() { return self::_getInstance(); }
	public static function Start() { return self::_start(); }
	public static function Modify( $name, $value ) { return self::_modify( $name, $value ); }
	public static function Register( $name, $value = NULL ) { return self::_register( $name, $value ); }
	public static function Unregister( $name ) { return self::_unregister( $name ); }
	public static function GetData() { return self::_getData(); } 
	public static function GetAll() { return self::_getData(); }
	public static function Get( $name = NULL ) { return self::_get( $name ); }
	public static function Set( $name, $value ) { return self::_set( $name, $value ); }
	public static function Has( $name ) { return self::_has( $name ); } 
	
	final public function rootName() { return __CLASS__; }
	private function __construct() {}
	private function __clone() {} 
	
	private static function _getInstance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			$_instance = new Cookie;
		}
		return $_instance;
	} 
	
	private static function _start() 
	{
		self::$data = $_COOKIE;
	} 
	
	private static function _modify( $name, $value ) 
	{
		$expire = time() + self::$max_expire;
		setcookie( $name, $value, $expire, self::$def_path );
		self::$data[ $name ] = $value;
	} 
	
	private static function _register( $name, $value ) 
	{
		if( !array_key_exists( $name, self::$data ) ) 
		{
			self::_modify( $name, $value );
			return true;
		}
		return false;
	} 
	
	private static function _unregister( $name ) 
	{
		if( array_key_exists( $name, self::$data ) ) 
		{
			$expire = time() - self::$min_expire;
			setcookie( $name, NULL, $expire, self::$def_path );
			unset( self::$data[ $name ] );
		}
	} 

	private static function _set( $name, $value ) 
	{
		if( array_key_exists( $name, self::$data ) ) 
		{
			self::_modify( $name, $value );
			return true;
		}
		return false;
	} 
	
	private static function _get( $name = NULL ) 
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
	
	private static function _getData() 
	{
		return self::$data;
	} 
	
	private static function _has( $name ) 
	{
		return array_key_exists( $name, self::$data );
	}
	
}