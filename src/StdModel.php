<?php

namespace Zuuda;

use Exception;

class StdModel extends SQLQuery 
{
	
	protected $_propLive = true;
	
	public function __construct( $args ) 
	{
		global $inflect; 
		$model = $this;
		$len = count($args);
		$alias = explode( mad, $args[1] );
		sort($alias); 
		$table = array();
		foreach( $alias as $key => $word ) 
		{
			$alias[$key] = $inflect->singularize(strtolower($word)); 
			$table[$key] = $inflect->pluralize(strtolower($word)); 
		}
		$table = implode( mad, $table ); 
		$alias = implode( mad, $alias ); 
		$this->__setPrefix($args[$len-1]);
		$this->__setModelName( $args[0] ); 
		$this->__setTableName($table); 
		$this->__setAliasName($alias); 
		if( isset($args[3]) )
			$this->__setForeignKey($args[3]); 
		else 
			$this->__setForeignKey($this->_primaryKey); 
		$this->__setAliasKey($args[2]); 
		$this->__setAliasModel($args[$len-2]); 
		$this->__initConn(); 
		$this->__fetchCacheColumns(); 
	} 
	
	final public function IsLive() { return $this->_propLive; } 
	final public function Blind() { return $this->_propLive = false; } 
	final public function Unblind() { return $this->_propLive = true; } 
	
	final public function ParseSqlSelection() 
	{
		return $this->__parseSqlSelection( $this->_propModel, $this->_propsUndescribe );
	} 
	
	final public function ParseSqlHasOne() 
	{
		return $this->__parseSqlHasOne(); 
	} 
	
	final public function ParseSqlMerge() 
	{ 
		return $this->__parseSqlMerge(); 
	} 
	
	final public function ParseSqlMergeLeft() 
	{ 
		return $this->_parseSqlMergeLeft(); 
	} 
	
	final public function ParseSqlMergeRight() 
	{ 
		return $this->_parseSqlMergeRight(); 
	} 
	
	protected function __initConn() 
	{
		global $configs;
		if( !isset( $configs[ 'DATASOURCE' ][ 'HANDLECN' ] ) ) 
			$this->connect( 
				$configs['DATASOURCE']['HOSTNAME'], 
				$configs['DATASOURCE']['USERNAME'], 
				$configs['DATASOURCE']['PASSWORD'], 
				$configs['DATASOURCE']['DATABASE'] 
			); 
		else 
			$this->__setDBHandle($configs['DATASOURCE']['HANDLECN']); 
	} 
	
	private function __setForeignKey( $key ) 
	{
		$this->_propAliasKey = $key; 
		return $this;
	} 
	
	private function __setAliasKey( $key ) 
	{
		$this->_propForeignKey = $key; 
		return $this; 
	} 
	
	private function __setAliasModel( $key ) 
	{
		$this->_propAliasModel = $key; 
	}
	
}