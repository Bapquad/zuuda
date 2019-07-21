<?php 
namespace Zuuda;

class Config 
{
	public static function GetInstance() { return self::_getInstance(); }
	public static function Get( $name ) { return self::_get( $name ); }
	public static function Set( $name, $value ) { return self::_set( $name, $value ); }
	public static function Die( $name ) { return self::_die( $name ); } 
	public static function Has( $name ) { return self::_has( $name ); } 
	public static function Trans( $data ) { return self::_trans( $data ); }
	
	private function __construct() {}
	private function __clone() {}
	private static function _getInstance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			$_instance = new Config;
		}
		return $_instance;
	}
	
	private static function _set( $name, $value ) 
	{
		global $configs;
		$configs[ $name ] = $value;
		
		return self::_getInstance();
	} 
	
	private static function _die( $name ) 
	{
		global $configs;
		if( isset($configs[$name]) ) 
		{
			unset($configs[$name]);
		} 
		return self::_getInstance();
	} 
	
	private static function _has( $name ) 
	{
		global $configs;
		return isset($configs[$name]);
	}
	
	private static function _get( $name ) 
	{
		global $configs;
		if( isset( $configs[ $name ] ) ) 
		{
			return $configs[ $name ];
		}
		return NULL;
	} 
	
	private static function _trans( $data ) 
	{ 
		global $configs; 
		if( isset($configs['LOCATE']) ) 
			$configs['LOCATE']['TRANS'] = $data; 
		return self::_getInstance();
	} 
	
}