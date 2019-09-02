<?php 
namespace Zuuda;

class Config 
{
	public static function GetInstance() { return self::__getInstance(); }
	public static function Get( $name ) { return self::__get( $name ); }
	public static function Set( $name, $value ) { return self::__set( $name, $value ); }
	public static function Die( $name ) { return self::__die( $name ); } 
	public static function Has( $name ) { return self::__has( $name ); } 
	public static function Trans( $data ) { return self::__trans( $data ); }
	
	private function __construct() {}
	private function __clone() {}
	private static function __getInstance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			$_instance = new Config;
		}
		return $_instance;
	}
	
	private static function __set( $name, $value ) 
	{
		global $configs;
		$configs[ $name ] = $value;
		
		return self::__getInstance();
	} 
	
	private static function __die( $name ) 
	{
		global $configs;
		if( isset($configs[$name]) ) 
		{
			unset($configs[$name]);
		} 
		return self::__getInstance();
	} 
	
	private static function __has( $name ) 
	{
		global $configs;
		if( 'COM'===$name ) 
		{ return $configs['COM']; }
		return isset($configs[$name]);
	}
	
	private static function __get( $name ) 
	{
		global $configs;
		if( isset( $configs[ $name ] ) ) 
		{
			return $configs[ $name ];
		}
		return NULL;
	} 
	
	private static function __trans( $data ) 
	{ 
		global $configs; 
		if( isset($configs['LOCATE']) ) 
			$configs['LOCATE']['TRANS'] = $data; 
		return self::__getInstance();
	} 
	
}