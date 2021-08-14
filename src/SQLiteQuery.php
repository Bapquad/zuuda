<?php

namespace Zuuda;

use Exception;
use SQLite3; 
use Zuuda\Error; 
use Zuuda\SQLQuery; 
use Zuuda\SQLite\StdModel; 

abstract class SQLiteQuery extends SQLQuery 
{
	
	private static $this = '\Zuuda\SQLiteQuery'; 
	
	final public function DriverVersion() { return call_user_func_array(array($this, '__driverSersion'), array()); }
	
	final private function __fetch_num_rows( $rs ) 
	{
		$count = 0; 
		$rs->reset(); 
		while( $rs->fetchArray() ) 
			$count++;
		$rs->reset(); 
		return $count;
	}
	
	protected function __parseDescribe( $tableName ) 
	{
		global $_cache;
		$describe = $_cache->get('describe_'.$tableName);
		if( (empty($describe) || NULL===$describe) && $this->_dbHandle ) 
		{
			$describe = array();
			$sql = "PRAGMA table_info (".$tableName.")"; 
			$result = $this->__query( $sql );
			while ($row = $this->fetch_row($result)) 
				$describe[] = $row["name"]; 
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
				return $this->query("SELECT `Table`.`name` AS `Table[name]` FROM `sqlite_master` AS 'Table' WHERE `type`='table'");
			}
			else 
				throw new Exception( "Usage <strong>Model::dbList()</strong> is incorrect." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	}
	
	protected function __parseSqlStartTransaction() { return "BEGIN TRANSACTION"; } 
	protected function __parseSqlCommitTransaction() { return "COMMIT TRANSACTION"; } 
	protected function __parseSqlRollbackTransaction() { return "ROLLBACK TRANSACTION"; } 
	
	protected function __parseSqlLabelField( $field ) 
	{
		$sql = EMPTY_CHAR; 
		if( NULL!==$field['label'] ) 
		{
			$sql .= " AS `{$this->_propModel}({$field['label']})`"; 
		} 
		else if( is_null($field['label']) ) 
		{
			$sql .= " AS `{$this->_propModel}[{$field['name']}]`"; 
		}
		return $sql; 
	}
	
	protected function __computeSteadModel( $args, $flag=NULL ) 
	{
		return new StdModel( $args, $flag ); 
	}
	
	protected function errno() { return $this->_dbHandle->lastErrorCode(); } 
	protected function error() { return $this->_dbHandle->lastErrorMsg(); } 
	protected function insert_id() { return $this->_dbHandle->lastInsertRowId(); } 
	protected function escape_string( $str ) { return SQLite3::escapeString( trim($str) ); } 
	protected function affected_rows( $rs=NULL ) { return $this->_dbHandle->changes(); } 
	protected function num_rows( $rs ) { return call_user_func_array(array($this, '__fetch_num_rows'), array($rs)); } 
	protected function fetch_assoc( $rs ) { return ($rs)?$rs->fetchArray( SQLITE3_ASSOC ):$rs; } 
	protected function fetch_row( $rs, $mode=SQLITE3_BOTH ) { return ($rs)?$rs->fetchArray( $mode ):$rs; } 
	protected function free_result( $rs ) { return ($rs)?$rs->finalize():$rs; } 
	
	protected function fetch_field( $rs, &$ts, &$fs ) 
	{
		$ts = array(); 
		$fs = array();
		$len = $rs->numColumns();
		for( $i=0; $i<$len; $i++ ) 
		{
			$pattern = '#^(.*)\[(.*)\]$#'; 
			$colName = $rs->columnName($i);
			if( preg_match( $pattern, $colName, $ms ) ) 
			{
				$ts[] = $ms[1];
				$fs[] = $ms[2]; 
			}
		}
		return count($fs);
	}
	
	protected function __query( $sql ) 
	{
		$result = false; 
		$sql = trim($sql); 
		if( preg_match('#^(CREATE|UPDATE|INSERT|DELETE|ALTER|DROP|BEGIN|COMMIT|ROLLBACK|SAVEPOINT|RELEASE)#', $sql, $ms) ) 
		{ 
			$result = $this->_dbHandle->exec( $sql ); 
		} 
		else 
		{
			$result = $this->_dbHandle->query( $sql ); 
		}
		$this->__logsql( $sql ); 
		return $result;
	} 
	
	final protected function __connect( $src ) 
	{
		global $_CONFIG; 
		
		try 
		{ 
			if( isset($_CONFIG["DATASOURCE"][$src]) ) 
			{
				$srv = $_CONFIG['DATASOURCE'][$src];
				if( isset($srv['database']) ) 
				{
					$path = correct_path(ROOT_DIR.$srv['database']);
				} 
				else 
				{
					throw new Exception( "The database config had been missed." ); 
				}
				$server = $_CONFIG['DATASOURCE']['server'][$srv['server']]; 
				if( isset($server['resource']) ) 
				{
					$dsl = $server['resource'];
					$dsl->close();
					$dsl->open($path);
					$this->__handled( $dsl, $src ); 
				} 
				else 
				{
					$dsl = new SQLite3( $path ); 
					if( $dsl ) 
					{
						$server['resource'] = $_CONFIG['DATASOURCE']['server'][$srv['server']]['resource'] = $dsl; 
						call_user_func_array( array($this, '__connect'), array($src) ); 
					}
					else 
					{
						throw new Exception("Could not connect to database path"); 
					}
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
	
	final protected function __driverSersion() 
	{
		$version = $this->_dbHandle->querySingle('SELECT SQLITE_VERSION()');
		return $version;
		return SQLite3::version();
	}
	
}