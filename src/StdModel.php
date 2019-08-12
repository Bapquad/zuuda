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
		$this->_setPrefix($args[$len-1]);
		$this->_setModelName( $args[0] ); 
		$this->_setTableName($table); 
		$this->_setAliasName($alias); 
		if( isset($args[3]) )
			$this->_setForeignKey($args[3]); 
		else 
			$this->_setForeignKey($this->_primaryKey); 
		$this->_setAliasKey($args[2]); 
		$this->_setAliasModel($args[$len-2]); 
		$this->_initConn(); 
		$this->_fetchCacheColumns(); 
	} 
	
	final public function IsLive() { return $this->_propLive; } 
	final public function Blind() { return $this->_propLive = false; } 
	final public function Unblind() { return $this->_propLive = true; } 
	
	final public function ParseSqlSelection() 
	{
		return $this->_parseSqlSelection( $this->_propModel, $this->_propsUndescribe );
	} 
	
	final public function ParseSqlHasOne() 
	{
		return $this->_parseSqlHasOne(); 
	} 
	
	final public function ParseSqlMerge() 
	{ 
		return $this->_parseSqlMerge(); 
	} 
	
	final public function ParseSqlMergeLeft() 
	{ 
		return $this->_parseSqlMergeLeft(); 
	} 
	
	final public function ParseSqlMergeRight() 
	{ 
		return $this->_parseSqlMergeRight(); 
	} 
	
	protected function _initConn() 
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
			$this->_setDBHandle($configs['DATASOURCE']['HANDLECN']); 
	} 
	
	private function _setForeignKey( $key ) 
	{
		$this->_propAliasKey = $key; 
		return $this;
	} 
	
	private function _setAliasKey( $key ) 
	{
		$this->_propForeignKey = $key; 
		return $this; 
	} 
	
	private function _setAliasModel( $key ) 
	{
		$this->_propAliasModel = $key; 
	}
	
}