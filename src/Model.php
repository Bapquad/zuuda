<?php
namespace Zuuda;

class Model extends SQLQuery
{
	protected $_model;
	
	final public function rootName() { return __CLASS__; }

	public function __construct() 
	{
		global $inflect;
		if( method_exists($this, 'init') ) 
			$this->init(); 
		$c1 = isset($this->_table) || isset($this->_propTable);
		$c2 = isset($this->_model) || isset($this->_propModel); 
		$c3 = isset($this->_alias) || isset($this->_propAlias); 
		$c4 = !isset($this->abstract); 
		$c5 = __useDB();
		if( $c1&&$c2&&$c3&&$c4&&$c5 ) 
		{
			$alias = (EMPTY_CHAR!==$this->_propAlias)?$this->_propAlias:$this->_alias; 
			$alias = explode(mad, $alias); 
			sort($alias); 
			foreach( $alias as $key => $word ) 
				$alias[$key] = $inflect->singularize(strtolower($word)); 
			$this->_alias = implode(mad, $alias); 
			$this->__initConn();
		} 
	} 
	
	protected function __initConn() 
	{
		global $configs;
		$this->__mergeTable(); 
		if( !isset( $configs[ 'DATASOURCE' ][ 'HANDLECN' ] ) ) 
		{
			$this->connect( 
				$configs['DATASOURCE']['HOSTNAME'], 
				$configs['DATASOURCE']['USERNAME'], 
				$configs['DATASOURCE']['PASSWORD'], 
				$configs['DATASOURCE']['DATABASE']
			); 
		} 
		else 
		{
			$this->__setDBHandle( $configs[ 'DATASOURCE' ][ 'HANDLECN' ] ); 
		}
		$this->__setupModel();
		return $this;
	}
}