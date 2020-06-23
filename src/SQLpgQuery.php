<?php

namespace Zuuda;

use Exception;
use Zuuda\Error; 
use Zuuda\SQLQuery; 
use Zuuda\SQLpg\StdModel; 

abstract class SQLpgQuery extends SQLQuery 
{
	
	private static $this = '\Zuuda\SQLQuery';
	protected $_tildeControl = EMPTY_CHAR;
	
	final public function DriverVersion() { return call_user_func_array(array($this, '__getServerVersion'), array()); } 
	
	protected function __parseDescribe( $tableName ) 
	{
		global $_cache;
		$describe = $_cache->get('describe_'.$tableName);
		if( (empty($describe) || NULL===$describe) && $this->_dbHandle ) 
		{
			$describe = array();
			$sql = "SELECT column_name AS name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '{$this->_propTable}'"; 
			$result = $this->__query( $sql );
			while ($row = $this->fetch_row($result)) 
				$describe[] = $row[0]; 
			$this->free_result($result);
			$_cache->set('describe_'.$tableName,$describe);
		}
		return $describe; 
	} 
	
	protected function __dbList( $args, $argsNum ) 
	{
		try 
		{
			if( !$argsNum ) 
			{
				global $_CONFIG;
				return $this->query("SELECT table_name AS name FROM INFORMATION_SCHEMA.tables WHERE table_schema = 'public' AND table_type = 'BASE TABLE'");
			}
			else 
				throw new Exception( "Usage <strong>Model::dbList()</strong> is incorrect." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	}
	
	protected function __parseSqlReturning() { return " RETURNING ".$this->_primaryKey; }
	protected function __parseSqlStartTransaction() { return "BEGIN TRANSACTION"; } 
	protected function __parseSqlCommitTransaction() { return "COMMIT TRANSACTION"; } 
	protected function __parseSqlRollbackTransaction() { return "ROLLBACK TRANSACTION"; } 
	protected function __parseSqlRollbackCheckpoint( $pt ) { return "ROLLBACK TO ".$pt; } 
	
	protected function __computeSteadModel( $args, $flag=NULL ) 
	{
		return new StdModel( $args, $flag ); 
	}
	
	protected function errno() { return pg_last_error( $this->_dbHandle ); } 
	protected function error() { return pg_last_error( $this->_dbHandle ); } 
	protected function insert_id() { return call_user_func_array(array($this, 'max'), array($this->_primaryKey) );  } 
	protected function escape_string( $str ) { return pg_escape_string( $this->_dbHandle, trim($str) ); } 
	protected function affected_rows( $rs=NULL ) { return ($rs)?pg_affected_rows( $rs ):0; } 
	protected function num_rows( $rs ) { return ($rs)?pg_num_rows( $rs ):0; } 
	protected function fetch_assoc( $rs ) { return ($rs)?pg_fetch_assoc( $rs ):$rs; } 
	protected function fetch_row( $rs ) { return ($rs)?pg_fetch_row( $rs ):$rs; } 
	protected function free_result( $rs ) { return ($rs)?pg_free_result( $rs ):$rs; } 
	
	protected function fetch_field( $rs, &$ts, &$fs ) 
	{
		$ts = array(); 
		$fs = array(); 
		$models = array( $this->_propTable => $this->_propModel );
		foreach( $this->_propsHasOne as $key => $model ) { $models[] = array($model->getTableName()=>$key); } 
		foreach( $this->_propsMerge as $key => $model ) { $models[] = array($model->getTableName()=>$key); } 
		foreach( $this->_propsMergeLeft as $key => $model ) { $models[] = array($model->getTableName()=>$key); } 
		foreach( $this->_propsMergeRight as $key => $model ) { $models[] = array($model->getTableName()=>$key); } 

		$len = pg_num_fields( $rs ); 
		for( $i=0; $i<$len; $i++ ) 
		{
			$ts[] = $this->__parseModelField( $models, $rs, $i ); 
			$fs[] = pg_field_name( $rs, $i ); 
		} 
		return count($fs);
	} 
	
	final private function __parseModelField( $models, $result, $field ) 
	{ 
		$table_name = pg_field_table($result, $field); 
		if( array_key_exists($table_name, $models) ) 
		{
			return $models[$table_name]; 
		} 
		else 
		{
			return $table_name; 
		}
	} 
	
	protected function __query( $sql ) 
	{
		$sql = trim($sql); 
		$result = pg_query( $this->_dbHandle, $sql ); 
		$this->__logsql( $sql ); 
		return $result;
	} 
	
	protected function __handled( $dsl, $src, $less=false ) 
	{
		global $_CONFIG;  
		if( is_resource($dsl) ) 
		{
			$this->_dbHandle = $dsl; 
			if( isset($_CONFIG['DATASOURCE'][$src]) ) 
			{
				$srv = $_CONFIG['DATASOURCE'][$src];
				$_CONFIG['DATASOURCE']['server'][$srv['server']]['source'] = $src; 
				if( isset($this->_prefix) ) 
				{
					$this->__setPrefix( $this->_prefix ); 
					unset($this->_prefix); 
				} 
				else if( EMPTY_CHAR===$this->_propPrefix && isset($srv['prefix']) ) 
				{
					$this->__setPrefix( $srv['prefix'] ); 
				} 
				if( !$less ) 
				{
					$this->__mergeTable(); 
					$this->__setupModel();
				} 
				return $this; 
			} 
		} 
		return false;
	} 
	
	protected function __connect( $src ) 
	{
		global $_CONFIG; 
		try 
		{
			if( isset($_CONFIG['DATASOURCE'][$src]) ) 
			{
				$server = $_CONFIG['DATASOURCE']['server'][$_CONFIG['DATASOURCE'][$src]['server']]; 
				$conn_str = "host={$server['hostname']} port={$server['port']} user={$server['username']} password={$server['password']} options='--client_encoding=UTF8' dbname={$_CONFIG['DATASOURCE'][$src]['database']}";
				$dsl = pg_connect($conn_str);
				if( $dsl ) 
				{
					if( isset($server['source']) ) 
					{
						if( $src!==$server['source'] ) 
						{
							$_CONFIG['DATASOURCE']['server'][$_CONFIG['DATASOURCE'][$src]['server']]['resource'] = $dsl; 
						}
					}
					return $this->__handled( $dsl, $src ); 
				} 
				else 
				{
					$p = "<p>Could not connect to database server:</p>";
					$p.= "<ul>";
					$p.= "<li>Host: {$server['hostname']}</li>";
					$p.= "<li>User: {$server['username']}</li>";
					$p.= "<li>Password: ***</li>";
					$p.= "</ul>";
					throw new Exception($p); 
				}

				return $this;
			} 
		}
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() );
		}
		return $this;
	}
	
	final private function __getServerVersion() 
	{
		$sql = "SELECT version()"; 
		$qr = $this->__query( $sql ); 
		$rs = $this->fetch_row($qr); 
		if( 1===$this->num_rows($qr) ) 
			$rs = $rs[0]; 
		$this->free_result( $qr ); 
		return $rs;
	}
	
}