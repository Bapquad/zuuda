<?php

namespace Zuuda\SQLite;

use Exception;
use Zuuda\SQLiteQuery;

class StdModel extends SQLiteQuery 
{
	
	protected $_propLive = true;
	
	public function __construct( $args, $hoFlg=false ) 
	{
		global $_inflect; 
		$len = count($args);
		$alias = explode( mad, $args[1] );
		sort($alias); 
		$table = array();
		$model = array();
		foreach( $alias as $key => $word ) 
		{
			$alias[$key] = $_inflect->singularize(strtolower($word)); 
			$table[$key] = $_inflect->pluralize(strtolower($word)); 
			$model[$key] = ucfirst($_inflect->singularize(strtolower($word))); 
		}
		$table = implode( mad, $table ); 
		$alias = implode( mad, $alias );
		$model = (!$hoFlg)?implode( EMPTY_CHAR, $model ):$args[0]; 
		$this->__setPrefix($args[$len-1]);
		$this->__setModelName($model); 
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
	
	final public function IsLive() 
	{ 
		return $this->_propLive; 
	} 
	
	final public function Blind() 
	{ 
		$this->_propLive = false; 
		return $this;
	} 
	
	final public function Unblind() 
	{ 
		$this->_propLive = true; 
		return $this; 
	} 
	
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
		return $this->__parseSqlMergeLeft(); 
	} 
	
	final public function ParseSqlMergeRight() 
	{ 
		return $this->__parseSqlMergeRight(); 
	} 
	
	final public function GetForeignKey() 
	{
		return $this->_propForeignKey;
	} 
	
	final public function GetAliasKey() 
	{
		return $this->_propAliasKey; 
	} 
	
	final public function SetAliasModel($model) 
	{
		return $this->__setAliasModel($model); 
	}
	
	protected function __initConn() 
	{
		global $configs;
		$src = $configs['DATASOURCE']['server']['default']; 
		if( isset($configs['DATASOURCE'][$src]) ) 
		{
			if( isset($configs['DATASOURCE']['server'][$configs['DATASOURCE'][$src]['server']]['source']) ) 
			{
				if( $configs['DATASOURCE']['server'][$configs['DATASOURCE'][$src]['server']]['source']!=$src ) 
				{
					$this->__connect($src); 
				} 
				else 
				{ 
					$this->__handled($configs['DATASOURCE']['server'][$configs['DATASOURCE'][$src]['server']]['resource'], $src, true);
				} 
			} 
			else 
			{
				$this->__connect($src); 
			}
		} 
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
		return $this;
	}
	
}