<?php

namespace Zuuda;

class Session 
{
	static protected $data = array();
	
	public static function GetInstance() { return self::_getInstance(); }
	public static function Start() { return self::_start(); }
	public static function Modify( $name, $var ) { return self::_modify( $name, $var ); }
	public static function Register( $name, $var = NULL ) { return self::_register( $name, $var ); }
	public static function Unregister( $name ) { return self::_unregister( $name ); }
	public static function GetData() { return self::_getData(); } 
	public static function GetAll() { return self::_getData(); }
	public static function Get( $name = NULL ) { return self::_get( $name ); }
	public static function Set( $name, $var ) { return self::_set( $name, $var ); }

	final public function rootName() { return __CLASS__; }
	private function __construct() {}
	private function __clone() {}
	
	private static function _getInstance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			$_instance = new Session;
		}
		return $_instance;
	}
	
	private static function _start() 
	{
		session_start(); 
		self::$data = $_SESSION;
	}
	
	private static function _modify( $name, $var ) 
	{
		$_SESSION[ $name ] = $var;
		self::$data[ $name ] = $var;
	}

	private static function _register( $name, $var ) 
	{
		if( !array_key_exists( $name, self::$data ) ) 
		{
			$_SESSION[ $name ] = $var;
			self::$data[ $name ] = $var;
			return true;
		}
		return false;
	}

	private static function _unregister( $name ) 
	{
		if( array_key_exists( $name, self::$data ) ) 
		{
			unset( $_SESSION[ $name ] );
			unset( self::$data[ $name ] );
		}
	}
	
	private static function _getData() 
	{
		return self::$data;
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

	private static function _set( $name, $var ) 
	{
		if( array_key_exists( $name, self::$data ) ) 
		{
			self::_modify( $name, $var );
			return true;
		}
		return false;
	}
}