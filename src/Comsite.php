<?php

namespace Zuuda;

use Exception;
use Zuuda\Error;
use Zuuda\Fx;
use Zuuda\Config;
use Zuuda\Cookie;
use Zuuda\Session;
use Zuuda\Cache;
use Zuuda\Query;
use Zuuda\Request;
use Zuuda\ComsiteAction;

class Comsite implements iTaskService, iConnService 
{
	
	public static $this = '\Zuuda\Comsite';
	private static $inst;
	final private function __clone() {} 
	final private function __construct() {} 
	private $_socket = array(
		'host_addr' => empc, 
		'host_port'	=> empc, 
	);
	private $_redis;
	private $_configs = array( 
		'connected' => false, 
		'role' => empc,
	);
	
	public static function Instance() { return call_user_func_array(array(self::$this, '__instance'), array()); } 
	public static function GetInstance() { return call_user_func_array(array(self::$this, '__instance'), array()); } 
	public static function BootService() { return call_user_func_array(array(self::$this, '__bootService'), func_get_args()); } 
	public static function Task( Model $model ) { return call_user_func_array(array(self::$this, '__task'), array()); } 
	public static function Connect() { return call_user_func_array(array(self::$this, '__connect'), array(func_get_args())); } 
	public static function IsConnected() { return call_user_func_array(array(self::$this, '__isConnected'), array()); } 
	public static function Redis() { return call_user_func_array(array(self::$this, '__redis'), array()); } 
	public static function Set() { return call_user_func_array(array(self::$this, '__setStr'), array(func_get_args())); } 
	public static function Get() { return call_user_func_array(array(self::$this, '__getStr'), func_get_args()); } 
	public static function Unlink() { return call_user_func_array(array(self::$this, '__unlink'), func_get_args()); }
	public static function SocketIO() { return call_user_func_array(array(self::$this, '__socketIO'), array(func_get_args())); } 
	public static function Server() { return call_user_func_array(array(self::$this, '__server'), array()); } 
	public static function Client() { return call_user_func_array(array(self::$this, '__client'), array()); } 
	public static function ServiceRole() { return call_user_func_array(array(self::$this, '__serviceRole'), array()); } 
	public static function ServiceHost() { return call_user_func_array(array(self::$this, '__serviceHost'), array()); }
	public static function ServicePort() { return call_user_func_array(array(self::$this, '__servicePort'), array()); }
	
	private static function __applyConfigs() 
	{
		return array
		(
			VENDOR_DIR, 
			'Zuuda\ServiceBooter', 
			'.xml', 
		);
	} 
	
	private static function __checkService() 
	{ 
		try 
		{
			if( !class_exists('Redis') ) 
			{ 
				throw new Exception("Your system has not support Redis.");
			} 
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() ); 
		} 
	}  
	
	private static function __bootService( Application $app ) 
	{
		$service = self::__applyConfigs(); 
		$service_path = correct_path(__dispatch_service_file($service));
		call_user_func_array(array(self::$this, '__connect'), array(func_get_args())); 
	}
	
	private static function __instance() 
	{
		static $_inst;
		return $_inst ?: $_inst = self::$inst = new Comsite;
	}
	
	private static function __connect( $args ) 
	{
		if( self::__isConnected() ) 
		{ 
			return self::$inst; 
		} 
		if(is_object(current($args)) && "Zuuda\Application"===get_class(current($args))) 
		{
			if( config::has('BOARDCAST') ) 
			{
				$boardcast = config::get('BOARDCAST');
				$driver = $boardcast['server']['default'];
				if( array_key_exists($driver, $boardcast) && $boardcast['server']['active'] ) 
				{
					switch( $driver ) 
					{
						case 'redis':
						default: 
							if(call_user_func_array(array(self::$this, '__connect_'.$driver), [array($boardcast[$driver]['host_addr'], $boardcast[$driver]['host_port'])])) 
							{
								self::$inst->_configs['connected'] = true;
								self::$inst->_redis->set("zuuda-boardcast-greet", "Hello! Thank you for using."); 
							}
							break;
					}
				}
			} 
		} 
		else 
		{ 
			if( config::has('BOARDCAST') ) 
			{
				$boardcast = config::get('BOARDCAST');
				if( $boardcast['server']['active'] ) 
					return call_user_func_array(array(self::$this, '__connect_redis'), array($args)); 
			}
		} 
		return self::$inst;
	}
	
	private static function __connect_redis( $args ) 
	{ 
		try 
		{
			if( class_exists('Redis') ) 
			{
				$redis = new \Redis;
				if( isset($args[1]) ) 
				{
					if( $redis->connect($args[0], $args[1]) ) 
						self::$inst->_configs['connected'] = true; 
					else 
						throw new Exception("Couldn't connect to server: <b>{$args[0]}:{$args[1]}</b>"); 
				}
				else 
				{
					if( !isset($args[0]) ) 
						throw new Exception("<b>[ZUUDA COMSITE SERVICE]</b>: The function <code><b>Comsite::Connect(\"127.0.0.1\")</b></code> must be given the server address as a parameter."); 
					if( $redis->connect($args[0]) ) 
						self::$inst->_configs['connected'] = true; 
					else 
						throw new Exception("Couldn't connect to server: <b>{$args[0]}:6379</b>"); 
				}
				self::$inst->_redis = $redis; 
			}
		}
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage().BL.error::position($e) );
		} 
		return self::$inst;
	} 
	
	private static function __isConnected() 
	{ 
		return self::$inst->_configs['connected'];
	} 
	
	private static function __redis() 
	{ 
		if( self::__isConnected() ) 
		{
			return self::$inst->_redis;
		} 
		return NULL;
	} 
	
	private static function __setStr( $args ) 
	{ 
		if( self::__isConnected() ) 
		{ 
			self::$inst->_redis->set( $args[0], $args[1] );
		} 
		return self::$inst;
	} 
	
	private static function __getStr( $arg ) 
	{ 
		if( self::__isConnected() ) 
		{ 
			return self::$inst->_redis->get( $arg ); 
		} 
		return NULL;
	} 
	
	private static function __unlink( $arg ) 
	{ 
		if( self::__isConnected() ) 
		{
			return self::$inst->_redis->unlink( $arg ); 
		} 
		return self::$inst; 
	} 
	
	private static function __socketIO( $args ) 
	{ 
		if( self::__isConnected() ) 
		{
			self::$inst->_socket['host_addr'] = $args[0]; 
			if( isset($args[1]) ) 
				self::$inst->_socket['host_port'] = $args[1]; 
		} 
		return self::$inst; 
	}  
	
	private static function __server() 
	{ 
		if( self::__isConnected() ) 
		{
			self::$inst->_configs['role'] = 'server';
			return comsiteaction::instance();
		} 
		return NULL;
	} 
	
	private static function __client() 
	{
		if( self::__isConnected() ) 
		{
			self::$inst->_configs['role'] = 'client'; 
			return comsiteaction::instance();
		} 
		return NULL;
	} 
	
	private static function __serviceHost() 
	{ 
		if( self::__isConnected() ) 
		{ 
			return self::$inst->_socket['host_addr']; 
		} 
		return false; 
	} 
	
	private static function __servicePort() 
	{
		if( self::__isConnected() ) 
		{ 
			return self::$inst->_socket['host_port'];
		} 
		return false;
	} 
	
	private static function __serviceRole() 
	{
		if( self::__isConnected() ) 
		{ 
			return self::$inst->_configs['role']; 
		} 
		return false;
	}
	
}










