<?php

namespace Zuuda;

use Exception;
use Zuuda\Config;
use Zuuda\Session; 
use Zuuda\Fx;
use Zuuda\AuthAction;

class Auth 
{
	
	private static $this = '\Zuuda\Auth';
	final public function rootName() { return __CLASS__; }
	private function __construct() {}
	private function __clone() {} 
	
	public static function GetAll() { return call_user_func_array(array(self::$this, '__user') , array()); }
	public static function Has() { return call_user_func_array(array(self::$this, '__user'), func_get_args()); }
	public static function All() { return call_user_func_array(array(self::$this, '__user'), array()); }
	public static function Get() { return call_user_func_array(array(self::$this, '__user'), func_get_args()); } 
	public static function Data() { return call_user_func_array(array(self::$this, '__user'), func_get_args()); } 
	public static function Body() { return call_user_func_array(array(self::$this, '__user'), func_get_args()); } 
	public static function Role() { return call_user_func_array(array(self::$this, '__role'), array(func_get_args())); } 
	public static function Apply() { return call_user_func_array(array(self::$this, '__role'), array(func_get_args())); } 
	public static function Status() { return call_user_func_array(array(self::$this, '__status'), array()); }
	public static function Register() { return call_user_func_array(array(self::$this, '__register'), func_get_args()); }
	public static function Destroy() { return call_user_func_array(array(self::$this, '__destroy'), array()); } 
	public static function Update() { return call_user_func_array(array(self::$this, '__update'), func_get_args()); } 
	public static function Instance() { return call_user_func_array(); } 
	
	public static function __callStatic( $name, $args ) 
	{
		try 
		{ 
			if( empty($args) ) 
			{
				if( fx::is_dir(ROOT_DIR.$name) ) 
				{
					return authaction::instance($name);
				} 
				else 
				{ 
					return call_user_func_array( array(authaction::instance('root'), $name), $args ); 
				} 
			}
			else 
			{
				throw new Exception( "<strong>Auth::{$name}()</strong> don't have any parameter. It just received only." ); 
			}
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() ); 
		} 
	}
	
	private static function __instance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			$_instance = new Auth;
		}
		return $_instance;
	} 
	
	private static function __register( $data ) 
	{
		self::__destroy(); 
		session::register( user_auth, $data ); 
		return $data; 
	} 
	
	private static function __update( $data ) 
	{
		session::modify( user_auth, $data ); 
		return $data; 
	}
	
	private static function __destroy() 
	{
		session::unregister( user_auth ); 
		return true; 
	}
	
	private static function __user($prop=NULL) 
	{
		$data = session::get(user_auth); 
		if( is_string($prop) ) 
		{
			if( isset($data[$prop]) ) 
			{
				return session::get(user_auth)[$prop]; 
			}
			else 
			{
				return NULL;
			}
		}
		else if( is_null($prop) ) 
		{
			return $data; 
		} 
	} 
	
	public function __get( $prop ) 
	{
		if($prop) 
		{
			$data = session::get(user_auth); 
			if( isset($data[$prop]) )
				return $data[$prop]; 
		}
	} 
	
	public function __has( $prop ) 
	{
		if($prop) 
		{
			$data = session::get(user_auth); 
			return isset($data[$prop]);
		} 
		return false;
	} 
	
	private static function __login() 
	{
		global $configs;
		try 
		{
			if( isset($configs['AUTH']['login_url']) ) 
				redirect($configs['AUTH']['login_url']); 
			else
				throw new Exception( "You have not authorization." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 403, $e->getMessage() );
		}
	}
	
	private static function __role( $args ) 
	{
		global $configs;
		if(!count($args)) 
		{
			if(is_null(session::get(user_auth))) 
				self::login();
		}
		if( isset($configs['AUTH']) ) 
		{
			if(session::get(user_auth)[$configs['AUTH']['apply_key']]!==current($args)) 
				self::login();
		} 
	} 
	
	private static function __status() 
	{ 
		return $_SESSION[auth];
	} 
	
}