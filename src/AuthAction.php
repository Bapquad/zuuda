<?php 
namespace Zuuda;

use Exception;
use Zuuda\Fx; 
use Zuuda\Error; 
use Zuuda\Response; 
use Zuuda\Config; 
use Zuuda\Session; 
use Zuuda\ServiceModel; 

class AuthAction  
{ 
	
	private static $this = '\Zuuda\AuthAction';
	private static $inst;
	final private function __clone() {} 
	final private function __construct() {} 
	final static public function Deny() { return call_user_func_array(array(self::$this, '__deny'), array()); } 
	final static public function Goto() { return call_user_func_array(array(self::$this, '__goto'), func_get_args()); } 
	final static public function Back() { return call_user_func_array(array(self::$this, '__back'), array()); } 
	final static public function Home() { return call_user_func_array(array(self::$this, '__home'), array()); } 
	final static public function Head() { return call_user_func_array(array(self::$this, '__goto'), func_get_args()); } 
	final static public function Stay() { return call_user_func_array(array(self::$this, '__stay'), array()); } 
	final static public function Only() { return call_user_func_array(array(self::$this, '__only'), array()); } 
	final static public function Sign() { return call_user_func_array(array(self::$this, '__sign'), array(func_get_args())); } 
	private $_auth_cur;
	private $_auth_dat;
	private $_models = array();

	final static public function Instance() { return call_user_func_array(array(self::$this, '__getInstance'), func_get_args()); } 
	final static protected function __getInstance( $auth_cur=NULL )  
	{ 
		static $inst; 
		if( NULL===$inst ) 
		{
			$inst = self::$inst = new AuthAction; 
		} 
		if( session::has(auth) ) 
			$inst->_auth_dat = session::get(auth); 
		else 
			$inst->_auth_dat = array(); 
		$inst->_auth_cur = $auth_cur;
		return $inst;
	} 
	
	final static public function __callStatic( $name, $args ) 
	{
		return call_user_func_array(array(self::$inst, '__call'), array($name, $args));
	} 
	
	final public function __call( $name, $args ) 
	{ 
		if( empty($args) ) 
		{
			if( 'root'===$this->_auth_cur ) 
			{ 
				return auth::get($name);
			} 
			if( session::has(auth) ) 
			{ 
				if( array_key_exists($this->_auth_cur, $this->_auth_dat) ) 
					if( isset($this->_auth_dat[$this->_auth_cur][$name]) ) 
						return $this->_auth_dat[$this->_auth_cur][$name]; 
			} 
		}
		else 
		{
			throw new Exception( "<strong>Auth::{$name}()</strong> don't have any parameter. It just received only." ); 
		}
	} 

	final static private function __deny() 
	{ 
		if( session::has(auth) ) 
		{
			if( array_key_exists(self::$inst->_auth_cur, self::$inst->_auth_dat) ) 
				return abort(403); 
		} 
		return self::$inst;
	} 
	
	final static private function __goto( $uri ) 
	{ 
		if( session::has(auth) ) 
		{
			if( array_key_exists(self::$inst->_auth_cur, self::$inst->_auth_dat) ) 
				return direct($uri); 
		} 
		return self::$inst;
	} 
	
	final static private function __back() 
	{ 
		if( session::has(auth) ) 
		{
			if( array_key_exists(self::$inst->_auth_cur, self::$inst->_auth_dat) ) 
				return back(); 
		} 
		return self::$inst;
	}
	
	final static private function __stay() 
	{ 
		if( session::has(auth) ) 
		{
			if( array_key_exists(self::$inst->_auth_cur, self::$inst->_auth_dat) ) 
				return self::$inst;
		} 
		return direct(base('')); 
	} 	
	
	final static private function __home() 
	{ 
		if( session::has(auth) ) 
		{
			if( array_key_exists(self::$inst->_auth_cur, self::$inst->_auth_dat) ) 
				return direct(base(self::$inst->_auth_cur)); 
		} 
		return self::$inst;
	} 
	
	final static private function __only() 
	{ 
		return self::__stay();
	} 
	
	final static private function __sign($args) // -out for signout
	{ 
		$state = '-in';
		$params = [];
		foreach( $args as $sign ) 
		{ 
			if( '-in'===$sign || '-out'===$sign || '-up'===$sign ) 
			{
				$state = $sign;
				continue;
			}
			$m = []; 
			if( preg_match_all( '~^[-$]([\w\d]+)(?>(?::|=))([\w\W\d\D]+)$~', $sign, $m ) ) 
			{ 
				$params[] = compact_item(current($m[1]), current($m[2]));
			} 
		} 
		switch( $state ) 
		{ 
			case '-out': 
				return call_user_func_array( array(self::$this, '__signout'), array() );
			case '-in': 
				return call_user_func_array( array(self::$this, '__signin'), array($params) ); 
			case '-up': 
			default:
				return call_user_func_array( array(self::$this, '__signup'), array($params) ); 
		} 
	} 
	
	final static private function __model() 
	{ 
		$model = NULL;
		global $_CONFIG;
		if( isset(self::$inst->models[self::$inst->_auth_cur]) ) 
		{ 
			$model = self::$inst->models[self::$inst->_auth_cur];
		} 
		else 
		{ 
			$def_dsr = $_CONFIG['DATASOURCE'];
			$def_app = $def_dsr['server']['default']; 
			$app_svr = $def_dsr[$def_app]['server'];
			$prefix = $def_dsr[$def_app]['prefix'];
			if( !isset($def_dsr['server'][$app_svr]['resource']) ) 
			{
				return false;
			}
			if( servicemodel::test( $def_dsr['server'][$app_svr]['resource'], $prefix.self::$inst->_auth_cur) ) 
			{ 
				$model = servicemodel::instance()
					->setPrefix($prefix)
					->setAliasName(self::$inst->_auth_cur)
					->setModelName(self::$inst->_auth_cur)
					->setTableName(self::$inst->_auth_cur)
					->start(); 
			} 
			else 
			{ 
				return false; 
			} 
		} 
		return $model;
	} 
	
	final static private function __signin( $request ) 
	{ 
		$model = self::$_inst->__model();
		foreach( $request as $param ) 
		{
			list( $key, $value ) = item($param); 
			$model->equal($key, $value);
		} 
		$rs = $model->search(); 
		if( 1===count($rs) ) 
		{
			if( !session::has(auth) ) 
			{ 
				session::init( auth, fx::current($rs) ); 
			} 
			else 
			{
				session::pull(auth)->merge(current($rs))->commit(); 
			}
			return true; 
		}
		else if( 1<count($rs) )
		{
			error::trigger("You have a unsafe authorization."); 
		} 
		else 
		{
			if( session::has(auth) ) 
			{ 
				session::pull(auth)->find(self::$inst->_auth_cur)->earse()->commit();
			} 
		}
		return false; 
	} 
	
	final static private function __signout() 
	{ 
		session::pull(auth)->find(self::$inst->_auth_cur)->earse()->commit(); 
	} 
	
	final static private function __signup( $request ) 
	{ 
		$model = self::$inst->__model(); 
		$data = array();
		foreach( $request as $item ) 
		{ 
			$data = array_merge( $data, $item );
		} 
		return $model->save($data); 
	} 
	
} 