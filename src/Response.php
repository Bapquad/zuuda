<?php 
namespace Zuuda;

use Exception;
use Zuuda\ResponseHeader; 

class Response extends ResponseHeader
{
	
	private $_data; 
	private $_dispatcher;
	private static $this = '\Zuuda\Response';
	public static function Redirect( $uri ) { return __direct( $uri ); } 
	public static function Location( $uri ) { return __direct( $uri ); } 
	public static function Goto( $uri ) { return __direct( $uri ); } 
	public static function Back() { return __back(); } 
	
	public static function View( Controller $dispatcher ) { return self::__view($dispatcher); } 
	public static function SetCors() { return self::__setCors(); }
	public static function Instance() { return call_user_func_array(array(self::$this, '__getInstance'), array()); } 
	public function Cors() { return call_user_func_array([$this, '__cors'], array(func_get_args(), func_num_args())); }
	public function With() { return call_user_func_array([$this, '__with'], array(func_get_args(), func_num_args())); } 
	public function Json( $data ) { return $this->__json($data); } 
	public function Render( $tpl=NULL, $data=NULL ) { return $this->__render($tpl, $data); }
	
	final public function SetDispatcher( Controller $dispatcher ) { $this->_dispatcher = $dispatcher; } 
	private function __construct() {}
	private function __clone() {} 
	
	protected static function __getInstance( $inst_of=NULL ) 
	{
		static $_instance;
		if( is_null($_instance) ) 
		{
			$_instance = new Response;
		}
		return $_instance;
	} 
	
	private function __with( $args, $argsNum ) 
	{ 
		try 
		{
			$oneArg = 1; 
			$twoArg = 2; 
			if( $oneArg===$argsNum ) 
			{
				$data = current($args); 
				if( is_string($data) ) 
				{
					return $this->_dispatcher->render( $data ); 
				} 
				else if( is_array($data) ) 
				{
					foreach($data as $key => $value ) 
					{
						$this->_dispatcher->assign( $key, $value );
					}
					return $this;
				} 
				else 
				{
					throw new Exception( "Usage Response::with() is incorrect." ); 
				}
			} 
			else if( $twoArg===$argsNum ) 
			{
				return $this->_dispatcher( $args[0], $args[1] );
			}
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() ); 
		} 
	} 
	
	private function __json( $data ) 
	{
		return $this->_dispatcher->json( $data ); 
	} 
	
	private function __render( $tpl, $data ) 
	{ 
		return $this->_dispatcher->render( $tpl, $data ); 
	} 
	
	private static function __view( $dispatcher ) 
	{
		$_instance = self::__getInstance(); 
		$_instance->setDispatcher( $dispatcher ); 
		return $_instance;
	} 
	
	private function __setDispatcher( $dispatcher ) 
	{
		$this->_dispatcher = $dispatcher; 
	} 
	
	private static function __deny( $msg=NULL ) 
	{ 
		abort( 403, $msg );
	} 
	
	private static function __setCors() 
	{ 
		// Allow from any origin
		if (isset($_SERVER['HTTP_ORIGIN'])) {
			// Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
			// you want to allow, and if so:
			header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
			header('Access-Control-Allow-Credentials: true');
			header('Access-Control-Max-Age: 86400');    // cache for 1 day
		}

		// Access-Control headers are received during OPTIONS requests
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
				header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
				header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
		} 
		return true;
	} 
	
	private function __cors($args, $argsNum) 
	{
		try 
		{ 
			if( 1<$argsNum ) 
			{ 
				throw new Exception("You just use the Response::cors() with 1 or 0 paramerter only"); 
			} 
			else if( 0===$argsNum ) 
			{ 
				$_setCors = true; 
			} 
			else 
			{ 
				$_setCors = current($args); 
			} 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );  
		} 
		
		if( $_setCors ) 
		{
			if( NULL!==$this->_dispatcher ) 
				return $this->_dispatcher->cors(); 
			else 
				response::setcors(); 
		} 
		
		return $this; 
	}
	
}