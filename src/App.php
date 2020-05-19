<?php

namespace Zuuda; 

class App 
{
	
	private static $this = '\Zuuda\App';
	protected $_codeof;
	protected $_className;
	protected $_class;
	
	public static function Use($class_name, $codeof=NULL) { return self::__use( $class_name, $codeof ); }
	public function Instance() { return $this->__instance( func_get_args() ); } 
	public function rootName() { return __CLASS__; }
	final private function __clone() {} 
	final protected function __construct($n=NULL, $c=NULL) 
	{
		$this->_codeof = $c; 
		$this->_className = $n; 
	} 
	
	private static function __use($n, $c) { return new App($n, $c); } 
	protected function __instance($args) 
	{ 
		global $configs; 
		try
		{
			getSingleton( 'Config' )->set( 'CODE_OF', $this->_codeof );
			$this->_class = new \ReflectionClass($this->_className);
			getSingleton( 'Config' )->die( 'CODE_OF' ); 
			if(!empty($args))
				return $this->_class->newInstanceArgs($args); 
			else 
				return $this->_class->newInstance();
		} 
		catch(\Exception $e) 
		{ 
			$position = error::position($e);
			if( NULL===$this->_codeof ) 
				abort( 500, "class <b>".$this->_className."</b> does not exist.<br /><br />".$position ); 
			else 
				abort( 500, "ERROR: <b>./modules/code/".$this->_codeof."</b> folder does not exist or class <b>".$this->_className."</b> does not exist.<br /><br />".$position );
		} 
	} 
	
}