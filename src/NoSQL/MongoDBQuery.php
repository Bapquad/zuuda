<?php
namespace Zuuda\NoSQL; 

use MongoDB\Driver\Exception\AuthenticationException; 
use MongoDB\Driver\Exception\BulkWriteException; 
use MongoDB\Driver\Exception\CommandException; 
use MongoDB\Driver\Exception\ConnectionException; 
use MongoDB\Driver\Exception\ConnectionTimeoutException; 
use MongoDB\Driver\Exception\EncryptionException; 
use MongoDB\Driver\Exception\ExecutionTimeoutException; 
use MongoDB\Driver\Exception\InvalidArgumentException; 
use MongoDB\Driver\Exception\LogicException; 
use MongoDB\Driver\Exception\RuntimeException; 
use MongoDB\Driver\Exception\ServerException; 
use MongoDB\Driver\Exception\SSLConnectionException; 
use MongoDB\Driver\Exception\UnexpectedValueException; 
use MongoDB\Driver\Exception\WriteException; 

use MongoDB\Driver\Monitoring\CommandFailedEvent; 
use MongoDB\Driver\Monitoring\CommandStartedEvent; 
use MongoDB\Driver\Monitoring\CommandSucceededEvent; 
use MongoDB\Driver\Monitoring\CommandSubscriber; 
use MongoDB\Driver\Monitoring\Subscriber; 

use MongoDB\BSON\Binary;
use MongoDB\BSON\Decimal128; 
use MongoDB\BSON\Javascript; 
use MongoDB\BSON\MaxKey; 
use MongoDB\BSON\MinKey; 
use MongoDB\BSON\ObjectId; 
use MongoDB\BSON\Regex; 
use MongoDB\BSON\Timestamp; 
use MongoDB\BSON\UTCDateTime; 
use MongoDB\BSON\Persistable; 
use MongoDB\BSON\Serializable; 
use MongoDB\BSON\Unserializable; 
use MongoDB\BSON\Int64;  

use MongoDB\Driver\Manager; 
use MongoDB\Driver\Command; 
use MongoDB\Driver\Query; 
use MongoDB\Driver\BulkWrite; 
use MongoDB\Driver\Session; 
use MongoDB\Driver\ClientEncryption; 
use MongoDB\Driver\WriteConcern; 
use MongoDB\Driver\ReadPreference; 
use MongoDB\Driver\ReadConcern; 
use MongoDB\Driver\Cursor; 
use MongoDB\Driver\CursorId; 
use MongoDB\Driver\Server; 
use MongoDB\Driver\WriteConcernError; 
use MongoDB\Driver\WriteError; 
use MongoDB\Driver\WriteResult; 

use Exception;
use Datetime;
use ReflectionClass;
use Zuuda\NoSQLQuery; 
use Zuuda\NoSQL\MongoDB\Bulk; 

ndefine( 'mcbm_find_all',		'__find_all' );
ndefine( 'mcbm_command',		'__exec_custom_command' );
ndefine( 'mcbm_set_collection',	'__setCollection' );
ndefine( 'mcbm_get_collection',	'__getCollection' );

abstract class MongoDBQuery extends NoSQLQuery 
{
	
	private static $this = '\Zuuda\NoSQL\MongoDBQuery'; 
	protected $_primaryKey = "_id"; 
	protected $_propsQuery = array(); 
	protected $_propQuery = NULL;
	protected $_propsCommand = array(); 
	protected $_propBulkWrite = NULL;
	abstract protected function __initConn(); 
	final public function FindAll() { return call_user_func_array([$this, mcbm_find_all], array([], 0)); } 
	final public function Command() { return call_user_func_array([$this, mcbm_command], array(func_get_args(), func_num_args())); } 
	final public function SetCollection() { return call_user_func_array([$this, mcbm_set_collection], func_get_args()); } 
	final public function GetCollection() { return call_user_func_array([$this, mcbm_get_collection], array()); } 
	final public function GetCollectionName() { return call_user_func_array([$this, mcbm_get_collection], array()); } 
	final public function Collection() { return call_user_func_array([$this, mcbm_get_collection], array()); } 
	final public function CollectionName() { return call_user_func_array([$this, mcbm_get_collection], array()); } 
	final public function BuildInfo() { return call_user_func_array([$this, '__getBuildInfo'], array()); }
	final public function Bulk() { return call_user_func_array([$this, '__bulk'], array()); } 
	final public function Write( Bulk $bulk ) { return call_user_func_array([$this, '__write'], func_get_args()); } 
	final public function Update( Bulk $bulk ) { return call_user_func_array([$this, '__write'], func_get_args()); } 
	final public function ObjectId( string $id ) { return call_user_func_array([$this, '__parseIdStmt'], func_get_args()); } 
	final public function OId( string $id ) { return call_user_func_array([$this, '__parseIdStmt'], func_get_args()); } 
	
	final private function __bulk() 
	{
		if( NULL===$this->_propBulkWrite ) 
		{ 
			$this->_propBulkWrite = new Bulk;
		} 
		return $this->_propBulkWrite;
	}
	
	final private function __addQuery( $query ) 
	{
		$this->_propQuery = $query; 
		$this->_propsQuery[] = $query; 
		return $this; 
	} 
	
	final private function __addCommand( $command ) 
	{ 
		$this->_propsCommand[] = current($command); 
		return $this; 
	}
	
	final protected function __selectDB( $db ) 
	{
		$this->_propDatabase = $db; 
		return $this; 
	} 
	
	final protected function __getOperatorSymbols() 
	{
		return array(
			'between'				=>'BETWEEN', 
			'equal' 				=>'$eq',
			'==='					=>'$eq', 
			'=='					=>'$eq', 
			'='						=>'$eq', 
			'greater than'			=>'$gt',
			'>'						=>'$gt', 
			'greater than or equal'	=>'$gte',
			'>='					=>'$gte', 
			'in'					=>'$in', 
			'is'					=>'$eq', 
			'is not'				=>'$ne', 
			'less than' 			=>'$lt',
			'<' 					=>'$lt', 
			'less than or equal' 	=>'$lte',
			'<='					=>'$lte', 
			'like'					=>'$regex', 
			'not'					=>'$not', 
			'not between'			=>'NOT BETWEEN',
			'not equal'				=>'$ne',
			'!='					=>'$ne', 
			'not in'				=>'$nin', 
			'not like'				=>'NOT LIKE', 
		);
	}
	
	final protected function __countStmt() 
	{
		$stmt = array('count'=>$this->_propCollection); 
		$filters = $this->__buildStmtCondition(); 
		if( !empty($filters) ) 
			$stmt['query'] = $filters; 
		return $stmt; 
	} 
	
	final protected function __parseIdStmt( $_id ) 
	{
		return new ObjectId($_id);
	}
	
	final protected function __buildStmtIdCondition( $value ) 
	{
		return array( $this->_primaryKey => $this->__parseIdStmt($value) );
	}
	
	final protected function __custom( $args, $argsNum, $mn="Custom" ) 
	{
		try 
		{ 
			if( $argsNum ) 
			{
				$cr = $this->__query( $args ); 
				if( $cr->isDead() ) return array(); 
				return $this->__fetchCQR( $cr ); 
			} 
			else 
			{
				throw new Exception( "<b>[EXCEPTION]</b> Usage the method <b>MongoDB::query()</b> is incorected." ); 
			}
		} 
		catch( \MongoDB\Driver\Exception\Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	} 
	
	final protected function __exec_custom_command( $args, $argsNum ) 
	{
		try 
		{ 
			if( $argsNum ) 
			{
				$cr = $this->__execute( $args );
				if( $cr->isDead() ) return array(); 
				return $this->__fetchCQR( $cr ); 
			} 
			else 
			{
				throw new \MongoDB\Driver\Exception\CommandException( "<b>[EXCEPTION]</b> Usage the method <b>MongoDB::command()</b> is incorected." ); 
			}
		} 
		catch( \MongoDB\Driver\Exception\CommandException $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	}
	
	final protected function __find_all( $args, $argsNum ) 
	{
		$cr = $this->__query( array([]) ); 
		$this->__clear(); 
		if( $cr ) 
		{
			return $this->__fetchResult( $cr ); 
		}
	}
	
	protected function __toPHP() 
	{ 
		$this->__abort('__toPHP'); 
	}
	
	protected function __fromPHP() 
	{ 
		$this->__abort('__formPHP'); 
	} 
	
	protected function __toJSON() 
	{ 
		$this->__abort('__toJSON'); 
	}
	
	protected function __fromJSON() 
	{ 
		$this->__abort('__formJSON'); 
	} 
	
	protected function __addSubcriber()  
	{ 
		$this->__abort('__addSubcriber'); 
	} 
	
	protected function __removeSubscriber() 
	{
		$this->__abort('__removeSubscriber'); 
	}
	
	protected function __parseCollection() 
	{
		if( EMPTY_CHAR===$this->_propCollection && isset($this->_collection) ) 
			$this->_propCollection = $this->_propPrefix.$this->_collection; 
		if( EMPTY_CHAR===$this->_propModel && isset($this->_model) )
			$this->_propModel = $this->_model; 
		if( zero===$this->_propUnits && isset($this->_units) ) 
		{
			$this->_propUnits = $this->_units; 
			$this->_propUnitOrigin = $this->_propCollection;
		} 
		unset($this->_collection); 
		unset($this->_model); 
		return $this;
	} 
	
	protected function __setupModel() 
	{
		$this->__fetchCacheColumns(); 
	} 
	
	protected function __fetchCacheColumns() 
	{
		if( empty($this->_propsDescribe) ) 
		{
			$this->_propsDescribe = $this->__parseDescribe( $this->_propCollection ); 
		} 
		foreach( $this->_propsDescribe as $f ) 
		{
			$this->$f = NULL; 
			if( in_array($f, $this->_propsPersistentUndescribe) ) 
			{
				$this->__unboundField( $f );
			} 
			else 
			{
				$this->__boundField( $f ); 
			}
		} 
		return $this->_propsUndescribe;
	} 
	
	protected function errno() 
	{
		//...
	} 
	
	protected function error() 
	{ 
		//...
	} 
	
	protected function insert_id() 
	{ 
		//...
	} 
	
	protected function affected_rows( $rs=NULL ) 
	{
		//...
	} 
	
	protected function escape_string( $str ) 
	{
		return addslashes($str); 
	} 
	
	protected function num_rows( $rs ) 
	{
		return count($rs);
	} 
	
	protected function fetch_assoc( $rs ) 
	{
		return $this->__fetchArray( $rs );
	} 
	
	protected function fetch_row( $rs ) 
	{
		return $this->__fetchArray( $rs );
	} 
	
	protected function free_result( $rs ) 
	{
		$this->__abort( "free_result" );
	}
	
	protected function fetch_field( $rs, &$ts, &$fs ) 
	{
		$out = 0; 
		foreach( $rs as $doc ) 
		{ 
			$item = $this->__fetchArray($doc); 
			foreach($this->_propsUndescribe as $field => $ig ) 
			{
				$fs[] = $field;
				$ts[] = $this->_propModel;
			}
			$out = count($item); 
			break; 
		}
		return $out; 
	} 
	
	protected function __computeSteadModel( $args, $flag=NULL ) 
	{
		global $_config;
		$modelRC = new ReflectionClass( $args[1] ); 
		$_config["require_seek"] = true; 
		$model = $modelRC->newInstance(); 
		unset($_config["require_seek"]); 
		$len = count($args);
		if( isset($args[3]) )
			$model->setForeignKey($args[3]); 
		else 
			$model->setForeignKey($this->_primaryKey); 
		$model->setAliasKey($args[2]); 
		$model->setAliasModel($args[$len-2]); 
		if( array_key_exists("require_hasone", $_config) ) 
			$model->setModelName($args[0]); 
		return $model;
	}
	
	final protected function __write( $bulk ) 
	{
		$result = array();
		$writeConcern = new WriteConcern(1);
		$collection = "{$this->_propDatabase}.{$this->_propCollection}"; 
		$bulkWriter = $bulk->compute(); 
		if( $bulkWriter->count() ) 
		{
			$rs = $this->_dbHandle->executeBulkWrite($collection, $bulkWriter, $writeConcern); 
			$bulk->release(); 
			$writeResult = array(
				"deleted"	=> $rs->getDeletedCount(), 
				"inserted"	=> $rs->getInsertedCount(), 
				"modified"	=> $rs->getModifiedCount() 
			); 
			$result = array_merge( $result, $writeResult );
			$bulk->result( $result ); 
		} 
		return $this;
	}
	
	final protected function __save( $args, $argsNum ) 
	{
		if( $argsNum ) 
		{
			$document = current($args); 
			if( is_object($document) ) 
			{ 
				if( "Zuuda\NoSQL\MongoDB\Bulk"===get_class($document) ) 
					return call_user_func_array(array($this, '__write'), $args); 
			} 
			else 
			{
				if( array_key_exists($this->_primaryKey, $document) ) 
				{
					if(method_exists($this, 'ride')) 
						$this->_eventRide = $this->ride( array_merge($this->_propsRole, $document) );
					$command = array(); 
					$update = $this->_propCollection; 
					$updates = array();
					$set = array(); 
					$id = $document[$this->_primaryKey];
					$q = array( $this->_primaryKey => (is_object($id))?$id:$this->__parseIdStmt($id) );
					$data = $this->__parseData( $document, $this->_eventRide ); 
					$u = array( '$set' => $data );
					$set = array_merge( $set, compact('q') );
					$set = array_merge( $set, compact('u') );
					$updates[] = $set; 
					$command = array_merge( $command, compact('update') );
					$command = array_merge( $command, compact('updates') ); 
					$cr = call_user_func_array(array($this, '__execute'), array([$command])); 
					$rs = $this->__fetchArray($cr->toArray()[0]); 
					if( $rs["nModified"] && $rs["n"] && $rs["ok"] ) 
					{
						if( method_exists($this, 'onride') ) 
							$this->_eventOnRide = $this->onride( $data ); 
						$document[$this->_primaryKey] = $q[$this->_primaryKey];
						return $document; 
					} 
					return false; 
				} 
				else 
				{
					return call_user_func_array(array($this, '__add'), $args); 
				}
			}
		} 
		else 
		{ 
			$primary_key = $this->_primaryKey; 
			if( NULL!==$this->$primary_key ) 
			{ 
				$document = array(); 
				foreach( $this->_propsDescribe as $describle ) 
					$document[$describle] = $this->$describle; 
				return call_user_func_array(array($this, '__save'), array([$document], 1)); 
			}
		}
	} 
	
	protected function __delete( $args, $argsNum ) 
	{
		if( 1===$argsNum ) 
		{ 
			$id = current($args); 
			$command = array(); 
			$delete = $this->_propCollection; 
			$deletes = array();
			$set = array(); 
			$q = array( $this->_primaryKey => (is_object($id))?$id:$this->__parseIdStmt($id) );
			if( method_exists($this, 'down') ) 
				$this->_eventDown = $this->down( array_merge($this->_propsRole, $q) ); 
			$limit = 1; 
			$set = array_merge( $set, compact('q') );
			$set = array_merge( $set, compact('limit') );
			$deletes[] = $set; 
			$command = array_merge( $command, compact('delete') );
			$command = array_merge( $command, compact('deletes') ); 
			$cr = call_user_func_array(array($this, '__execute'), array([$command])); 
			$rs = $this->__fetchArray($cr->toArray()[0]); 
			if( $rs["n"] && $rs["ok"] ) 
			{
				if( method_exists($this, 'ondown') ) 
					$this->_eventOnDown = $this->ondown( $q ); 
				return $q; 
			} 
			return false; 
		} 
		else if( 1<$argsNum ) 
		{
			$data = $args; 
			if( method_exists($this, 'down') ) 
				$this->_eventDown = $this->down( $data ); 
		}
		else 
		{
			$pk = $this->_primaryKey;
			if( NULL!==$this->$pk ) 
			{
				$param = [$this->$pk]; 
				return call_user_func_array(array($this, '__delete'), array($param, count($param))); 
			}
		}
	}
	
	final protected function __insert( $args, $argsNum ) 
	{
		try 
		{
			if( 2<=$argsNum ) 
			{
				$fields = array(); 
				$data = null;
				$multiple_row = false; 
				foreach( $args as $key => $value ) 
				{
					if( is_string($value) ) 
						$fields[] = $value; 
					else if( is_array($value) ) 
						$data = $value; 
				} 
				foreach( $data as $key => $value ) 
				{
					if( is_string($value) ) 
					{
						$data[$key] = $value; 
					}
					else if( is_array($value) ) 
					{
						$multiple_row = true; 
						foreach( $value as $i => $val ) 
						{ 
							$value[$i] = $this->escape_string($val);
						}
						$data[$key] = $value; 
					}
				}
				if( !$multiple_row ) 
				{
					foreach( $fields as $key => $f ) 
					{
						$data[$f] = $data[$key];
						unset($data[$key]);
					} 
					return call_user_func_array(array($this, '__add'), array($data)); 
				} 
				$command = array(); 
				$insert = $this->_propCollection; 
				$insertedIds = array(); 
				foreach( $data as $item ) 
				{
					foreach( $fields as $key => $f ) 
					{
						$item[$f] = $item[$key];
						unset($item[$key]);
					} 
					$document = $this->__document($item); 
					$documents[] = $document; 
					$insertedIds[] = $document[$this->_primaryKey]; 
				}
				$command = array_merge( $command, compact('insert') ); 
				$command = array_merge( $command, compact('documents') ); 
				$cr = call_user_func_array(array($this, '__execute'), array([$command])); 
				$rs = $this->__fetchArray($cr->toArray()[0]); 
				return count($insertedIds)===$rs["n"] && $rs["n"] && $rs["ok"]; 
			}
			else 
			{ 
				throw new Exception( "Usage <strong>Model::insert()</strong> is incorrect." ); 
			} 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	} 
	
	final private function __parseStmtData( $data, $describle ) 
	{
		try 
		{
			if(NULL===$data) 
			{
				$basename = 'Null';
			} 
			else 
			{
				$schema = $this->_schema[$describle]; 
				$basename = $schema[0]; 
			}
			switch( $basename )
			{
				case "Binary": 
					$class = new ReflectionClass( "\MongoDB\BSON\Binary" ); 
					return $class->newInstanceArgs( array($data, $schema[1] ) ); 
				case "Decimal128":
					$class = new ReflectionClass( "\MongoDB\BSON\Decimal128" ); 
					return $class->newInstanceArgs( array($data) ); 
				case "Javascript": 
					if( is_array($data) ) 
					{
						$class = new ReflectionClass( "\MongoDB\BSON\Javascript" ); 
						return $class->newInstanceArgs( $data ); 
					} 
					else 
					{
						throw new Exception( "You javascript data must be in array format." ); 
					} 
				case "MaxKey":
					$class = new ReflectionClass( "\MongoDB\BSON\MaxKey" ); 
					return $class->newInstance(); 
				case "MinKey":
					$class = new ReflectionClass( "\MongoDB\BSON\MinKey" ); 
					return $class->newInstance(); 
				case "ObjectId": 
					$class = new ReflectionClass( "\MongoDB\BSON\ObjectId" ); 
					if( NULL===$data ) 
						return $class->newInstanceArgs( array() ); 
					else
						return $class->newInstanceArgs( array($data) ); 
				case "Regex":
					$class = new ReflectionClass( "\MongoDB\BSON\Regex" ); 
					if( isset($schema[1]) ) 
						return $class->newInstanceArgs( array($data, $schema[1]) ); 
					else 
						return $class->newInstanceArgs( array($data) ); 
				case "Timestamp": 
					$class = new ReflectionClass( "\MongoDB\BSON\Timestamp" ); 
					if( isset($schema[1]) ) 
						return $class->newInstanceArgs( array($data, $schema[1]) ); 
					else 
						throw new Exception("Your <b><code>{$describle}</code></b> field has wrong schema."); 
				case "UTCDateTime": 
					$class = new ReflectionClass( "\MongoDB\BSON\UTCDateTime" ); 
					if( is_object($data) && get_class($data) ) 
						return $class->newInstanceArgs( array($data) ); 
					else if( is_string($data) ) 
						return $class->newInstanceArgs( array(new Datetime($data)) ); 
				case "Array": 
					if( is_array($data) ) 
						return (array) $data; 
					else throw new Exception("Your <b><code>{$describle}</code></b> field has expected array format."); 
				case "Document": 
					if( is_object($data) ) 
						return $data; 
					else throw new Exception("Your <b><code>{$describle}</code></b> field has expected document object format."); 
				case "String": 
					if( isset($schema[1]) ) 
					{
						return substr($data, 0, $schema[1]); 
					}
					return $data; 
				case "Int64": 
					$class = new ReflectionClass( "\MongoDB\BSON\Int64" ); 
					return $class->newInstanceArgs( array($data) ); 
				case "Integer": 
				case "Int32": 
					$data_str = "".$data; 
					if( isset($schema[1]) )
					{
						if( strlen($data_str) >= $schema[1] ) 
							return (int) substr($data_str, 0, $schema[1]); 
					}
					return (int)$data; 
				case "Null": 
					return NULL; 
				default:
					return $data; 
			}
		}
		catch( Exception $e )
		{ 
			abort( 500, $e->getMessage() ); 
		}
	}
	
	final private function __parseData( $document, $rideData ) 
	{
		$out = array(); 
		foreach( $this->_propsDescribe as $describle ) 
		{
			$value = NULL;
			if( $this->_primaryKey===$describle ) 
			{
				continue; 
			}
			elseif( NULL!==$rideData ) 
			{
				if( array_key_exists($describle, $rideData) ) 
				{
					$value = $rideData[$describle]; 
				}
			}
			else 
			{
				if( isset($document[$describle]) ) 
					$value = $document[$describle]; 
			} 
			if( isset($value) ) 
			{
				$out[$describle] = $this->__parseStmtData($value, $describle);
			}
		}
		return $out; 
	} 
	
	final private function __document( $data, $bootData = array() ) 
	{
		$out = array(); 
		foreach( $this->_propsDescribe as $describle ) 
		{
			if( $this->_primaryKey===$describle ) 
			{
				$out[$describle] = new ObjectId; 
			}
			elseif( array_key_exists($describle, $data) ) 
			{
				$out[$describle] = $this->__parseStmtData($data[$describle], $describle); 
			}
			else if( NULL!==$bootData ) 
			{
				if( array_key_exists($describle, $bootData) ) 
				{
					$out[$describle] = $this->__parseStmtData($bootData[$describle], $describle); 
				}
			}
			else 
			{
				if( isset($this->_schema[$describle][2]) ) 
				{
					$out[$describle] = $this->__parseStmtData($this->_schema[$describle][2], $describle); 
				} 
			} 
		}
		return $out; 
	}
	
	final protected function __add( $data ) 
	{
		if(method_exists($this, 'boot')) 
			$this->_eventBoot = $this->boot( array_merge($this->_propsRole, $data) );
		$command = array(); 
		$insert = $this->_propCollection; 
		$document = $this->__document( $data, $this->_eventBoot );
		$documents = array( $document ); 
		$command = array_merge( $command, compact('insert') ); 
		$command = array_merge( $command, compact('documents') ); 
		$cr = call_user_func_array(array($this, '__execute'), array([$command])); 
		$rs = $this->__fetchArray($cr->toArray()[0]); 
		if( $rs['n'] && $rs['ok'] ) 
		{
			if( method_exists($this, 'onboot') ) 
				$this->_eventOnBoot = $this->onboot( array_merge($this->_propsRole, $data) ); 
			return $document; 
		} 
		return false; 
	}
	
	final protected function __execute( $command ) 
	{
		$this->__addCommand( $command ); 
		$commandRC = new ReflectionClass( 'MongoDB\Driver\Command' ); 
		$command = $commandRC->newInstanceArgs( (array) $command ); 
		$session = $this->_dbHandle->startSession();
		$session->startTransaction();
		$cr = $this->_dbHandle->executeCommand($this->_propDatabase, $command); 
		// $cr = $this->_dbHandle->executeCommand($this->_propDatabase, $command, ["session"=>$session]); 
		$session->abortTransaction(); 
		$session->endSession(); 
		return $cr; 
	}
	
	final protected function __query( $query ) 
	{
		$this->__addQuery( $query ); 
		$queryRC = new ReflectionClass( 'MongoDB\Driver\Query' ); 
		$query = $queryRC->newInstanceArgs( (array) $query ); 
		$cr = $this->_dbHandle->executeQuery("{$this->_propDatabase}.{$this->_propCollection}", $query); 
		return $cr;
	}
	
	final protected function __handled( $dsl, $src, $less=false ) 
	{
		global $_CONFIG; 
		if( is_object($dsl) ) 
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
					$this->__parseCollection(); 
					$this->__setupModel();
				} 
				return $this; 
			} 
		} 
		return false; 
	}
	
	final protected function __connect( $src )
	{
		global $_CONFIG; 
		try 
		{
			if( isset($_CONFIG['DATASOURCE'][$src]) ) 
			{ 
				$app = $_CONFIG['DATASOURCE'][$src];
				$srv = $app['server']; 
				if( isset($_CONFIG['DATASOURCE']['server'][$srv]) ) 
				{
					$server = $_CONFIG['DATASOURCE']['server'][$srv]; 
					
					if( isset($server['resource']) ) 
					{ 
						$dsl = $server['resource']; 
						$this->__selectDB( $app['database'] ); 
						$this->__handled( $dsl, $src ); 
					} 
					else 
					{ 
						$connString = "{$server['driver']}://{$server['username']}:{$server['password']}@{$server['hostname']}:{$server['port']}/?authSource={$server['authsource']}&retryWrites=false"; 
						$dsl = new Manager($connString); 
						if( $dsl ) 
						{ 
							$_CONFIG['DATASOURCE']['server'][$_CONFIG['DATASOURCE'][$src]['server']]['resource'] = $dsl; 
							return $this->__connect($src); 
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
					} 
				} 
				else 
				{
					throw new Exception("<b><i>[EXCEPTION]</i></b> The <b>\"{$srv}\"</b> configuraion isn't defined."); 
				}
			} 
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() ); 
		} 
		return $this; 
	} 
	
	protected function __getBuildInfo() 
	{
		$cr = $this->__execute( ['buildinfo'=>1] ); 
		$rs = $cr->toArray(); 
		return $rs[0]; 
	}
	
}