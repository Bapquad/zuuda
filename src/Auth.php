<?php

namespace Zuuda;

use Exception;
use Zuuda\Config;
use Zuuda\Fx;

class Auth 
{
	
	private static $this = '\Zuuda\Auth';
	final public function rootName() { return __CLASS__; }
	private function __construct() {}
	private function __clone() {} 
	
	public static function Get($prop) { return self::__user($prop); } 
	public static function GetAll() { return self::__getAll(); }
	public static function Instance() { return self::__getInstance(); }
	public static function Body($prop) { return self::__user($prop); } 
	public static function Role() { return call_user_func_array([self::$this, '__role'], array(func_get_args())); } 
	public static function Apply() { return call_user_func_array([self::$this, '__role'], array(func_get_args())); } 
	public static function Status() { return call_user_func_array([self::$this, '__status'], array()); }
	
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
		{
			$data = session::get(user_auth); 
			if( isset($data[$prop]) )
				return session::get(user_auth)[$prop]; 
			else
				return NULL;
		}
		return self::__getInstance(); 
	} 
	
	private static function __getAll() 
	{ 
		return session::get(user_auth);
	}
	
	public function __get( $prop ) // IMPORTANT: Must be public.
	{
		if($prop) 
		{
			$data = session::get(user_auth); 
			if( isset($data[$prop]) )
				return session::get(user_auth)[$prop]; 
			else
				return NULL;
		}
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