<?php 
namespace Zuuda;
use Exception;

class Query
{
	
	private $_data; 
	private static $this = '\Zuuda\Query';
	
	public static function All() { return self::__params(); } 
	public static function Has($name) { return self::__has($name); }
	public static function Inputs() { return self::__params(); } 
	public static function Params() { return self::__params(); } 
	public static function Body($name=NULL) { return self::__param($name, true); } 
	public static function Data($name=NULL) { return self::__param($name, true); } 
	public static function Get($name=NULL) { return self::__param($name); } 
	public static function Param($name=NULL) { return self::__param($name); } 
	public static function Input($name=NULL) { return self::__param($name); } 
	public static function Instance() { return call_user_func_array([self::$this, '__instance'], array()); }
	public static function Except() { return call_user_func_array([self::$this, '__except'], array(func_get_args(), func_num_args())); } 
	public static function Length() { return call_user_func_array([self::$this, '__length'], array(func_get_args(), func_num_args())); } 
	public static function Empty() { return call_user_func_array([self::$this, '__empty'], array(func_get_args(), func_num_args())); } 
	public static function Encrypt() { return call_user_func_array([self::$this, '__encrypt'], array(func_get_args(), func_num_args())); } 
	public static function Hash() { return call_user_func_array([self::$this, '__encrypt'], array(func_get_args(), func_num_args())); } 
	public static function Merge() { return call_user_func_array([self::$this, '__merge'], array(func_get_args(), func_num_args())); } 
	final public function rootName() { return __CLASS__; } 
	private function __construct() {} 
	private function __clone() {} 
	
	private static function __instance() 
	{ 
		static $_inst; 
		return $_inst ?: $_inst = new Query;
	} 
	
	private static function __merge( $args, $argsNum ) 
	{ 
		global $_get; 
		try 
		{
			if( $argsNum ) 
			{
				$params = current( $args );
				if( is_array($params) ) 
					return array_merge($_get, $params); 
			}
			throw new Exception( "Usage <strong>Request::merge()</strong> is incorrect." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		} 
	} 
	
	private static function __encrypt( $args, $argsNum ) 
	{
		global $_get, $configs;
		$name = current($args); 
		if( isset($_get[$name]) ) 
		{
			$_get[$name] = hash( $configs['ENCRYPT']['query'], $_get[$name] );  
			return $_get[$name];
		} 
		return NULL;
	}
	
	private static function __length( $args, $argsNum ) 
	{
		global $_get; 
		try 
		{
			if( zero===$argsNum ) 
				return count($_get); 
			else if( 1===$argsNum ) 
			{
				$param = $_get[current($args)];
				if( is_array($param) )
					return count($param); 
				else if( is_string($param) ) 
					return strlen($param); 
			}
			else 
				throw new Exception( "Usage <strong>Query::length()</strong> is incorrect." ); 
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() ); 
		} 
	} 
	
	private static function __empty( $args, $argsNum ) 
	{
		global $_get; 
		try 
		{
			if( zero===$argsNum ) 
				return empty($_get); 
			else if( 1===$argsNum ) 
				return empty($_get[current($args)]);
			else 
				throw new Exception( "Usage <strong>Query::empty()</strong> is incorrect." ); 
		}
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() ); 
		} 
	}
	
	public static function __callStatic( $name, $args ) 
	{
		global $_get;
		try 
		{ 
			if( empty($args) ) 
				return $_get[$name]; 
			else 
				$value = current($args);
			$_get[$name] = $value; 
			return $value;	
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() ); 
		} 
	}
	
	private static function __except( $args, $argsNum ) 
	{
		global $_get;
		try 
		{
			$out = array(); 
			foreach($_get as $key => $value) 
				if( !in_array($key, $args) ) 
					$out[$key] = $value;
			return $out; 
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() );
		} 
	}
	
	private static function __params() 
	{
		global $_get; 
		return $_get; 
	} 
	
	private static function __has( $name ) 
	{
		global $_get; 
		return isset($_get[$name]); 
	}
	
	private static function __param( $name=NULL, $body=false ) 
	{
		global $_get;
		try 
		{
			if( NULL===$name && false===$body ) 
				throw new Exception("Usage Query is incorrect."); 
			if( isset($_get[$name]) ) 
			{
				return $_get[$name]; 
			}
			else 
			{
				if( $body )
					return $_get; 
				return NULL;
			}
		}
		catch( Exception $e ) 
		{
			abort( 400, $e->getMessage() ); 
		}
	} 

}