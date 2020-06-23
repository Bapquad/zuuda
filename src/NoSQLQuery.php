<?php
namespace Zuuda;

use Exception; 
use Zuuda\Error; 

abstract class NoSQLQuery extends QueryStmt 
{
	
	private static $this = '\Zuuda\NoSQLQuery'; 
	abstract protected function __initConn();
	protected $_propsProjection = array(); 
	
	final protected function __parseFields( $data ) 
	{
		$this->__abort('__parseFields');
	} 
	
	final protected function __setCollection( $name ) 
	{
		$this->_propCollection = $this->_propPrefix.$name; 
		return $this; 
	} 
	
	final protected function __getCollection() 
	{ 
		return $this->_propCollection; 
	}
	
	final protected function __fetchCQR( $qr ) 
	{
		$out = array(); $ts; $fs; 
		$rs = $qr->toArray();
		$numf = $this->fetch_field( $rs, $ts, $fs ); 
		foreach( $rs as $doc ) 
		{
			$r = $this->__fetchArray($doc); 
			$tmps = array(); 
			for( $i=0; $i<$numf; $i++ ) 
			{ 
				$f = $fs[$i];
				if( $f===$this->_primaryKey && isset($r[$f]['$oid']) ) 
					$tmps[$ts[$i]][$f] = $r[$f]['$oid']; 
				else 
					$tmps[$ts[$i]][$f] = (isset($r[$f]))?$r[$f]:NULL; 
			} 
			$out[] = $tmps; 
		} 
		return $out;
	} 
	
	final protected function __fetchArray( $object ) 
	{
		return json_decode(json_encode($object), true); 
	}
	
	final protected function __fetchResult( $qr ) 
	{
		$out = array(); $ts; $fs; 
		try 
		{
			$rs = $qr->toArray();
			$numf = $this->fetch_field( $rs, $ts, $fs ); 
			foreach( $rs as $doc ) 
			{
				$r = $this->__fetchArray($doc); 
				$tmps = array(); 
				for( $i=0; $i<$numf; $i++ ) 
				{
					if( isset($fs[$i]) ) 
					{
						$f = $fs[$i]; 
						if( array_key_exists($f, $this->_propsUndescribe) ) 
						{
							if( $f===$this->_primaryKey && isset($r[$f]['$oid']) ) 
								$tmps[$ts[$i]][$f] = $r[$f]['$oid']; 
							else
								$tmps[$ts[$i]][$f] = (isset($r[$f]))?$r[$f]:NULL; 
						}
					}
				} 
				if( $this->_flagHasOne ) 
				{
					foreach( $this->_propsHasOne as $model ) 
					{
						if( $model->isLive() ) 
						{
							$hd = $model->find($r[$model->getAliasKey()]); 
							$tmps = array_merge( $tmps, $hd ); 
						} 
					} 
				} 
				if( $this->_flagHasMany && !empty($this->_propsHasMany) ) 
				{
					foreach( $this->_propsHasMany as $key => $model ) 
					{ 
						if( $model->isLive() ) 
						{
							$tmps[$key] = $model->where($model->GetForeignKey(), $tmps[$this->_propModel][$this->_primaryKey])->search();
						}
					}
				} 
				if( $this->_flagHasMABTM && !empty($this->_propsHasMABTM) ) 
				{
					throw new Exception( "MongoDB is not support HasManyAndBelongsToMany" ); 
				}
				$out[] = $tmps; 
			} 
		} 
		catch( Exception $e ) 
		{
			abort( 500, "<b>[EXCEPTION]</b>".$e->getMessage() ); 
		}
		return $out; 
	}
	
	protected function __parseDescribe( $collectionName ) 
	{
		return $this->_schema;
	}
	
	protected function __parseStmtLabelField( $field ) 
	{
		$this->__abort('__parseStmtLabelField');
	}
	
	protected function __parseStmtStartTransaction() { return "START TRANSACTION"; } 
	protected function __parseStmtCommitTransaction() { return "COMMIT"; } 
	protected function __parseStmtRollbackTransaction() { return "ROLLBACK"; } 
	protected function __parseStmtRollbackCheckpoint( $pt ) { return "ROLLBACK TO SAVEPOINT ".$pt; } 
	protected function __parseStmtReleaseCheckpoint( $pt ) { return "RELEASE SAVEPOINT ".$pt; } 
	protected function __parseStmtCreateCheckpoint( $pt ) { return "SAVEPOINT ".$pt; } 
	
	final protected function __parseStmtSelection( $m, $d ) 
	{
		$this->__abort('__parseStmtSelection'); 
	}
	
	final protected function __parseStmtConditionOr( $m, $c ) 
	{
		$this->__abort('__parseStmtConditionOr'); 
	}
	
	final protected function __parseStmtCondition( $m, $c ) 
	{
		$out = array(); 
		foreach( $c as $clause ) 
		{
			$out[$clause[0]] = array($clause[1]=>$clause[2]);
		}
		return $out;  
	}
	
	final protected function __parseStmtHasOne() 
	{
		$this->__abort('__parseStmtHasOne'); 
	}
	
	final protected function __parseStmtMerge() 
	{
		$this->__abort('__parseStmtMerge'); 
	}
	
	final protected function __parseStmtMergeLeft() 
	{
		$this->__abort('__parseStmtMergeLeft'); 
	}
	
	final protected function __parseStmtMergeRight() 
	{
		$this->__abort('__parseStmtMergeRight'); 
	}
	
	final protected function __buildStmtSelection() 
	{
		$this->__abort('__buildStmtSelection'); 
	} 
	
	final protected function __buildStmtProjection() 
	{
		$out = array(); 
		foreach( $this->_propsProjection as $describe ) 
			$out = array_merge( $out, array($describe => 0) ); 
		return $out; 
	}
	
	final protected function __buildStmtFrom() 
	{
		$this->__abort('__buildStmtFrom');
	}
	
	final protected function __buildStmtConditionOn( $propModel ) 
	{
		$this->__abort('__buildStmtConditionOn');
	}
	
	final protected function __buildStmtCondition( $propModel = NULL, $fluid = false ) 
	{
		$out = array(); 
		if( !empty($this->_propsCond) ) 
		{
			$out = array_merge($out, $this->__parseStmtCondition( $this->_propModel, $this->_propsCond )); 
		} 
		return $out; 
	}
	
	final protected function __buildStmtRange() 
	{
		return array( 'limit' => $this->_propLimit?:20, 'skip' => $this->_propOffset );
	}
	
	final protected function __buildStmtGroup() 
	{
		$this->__abort('__buildStmtGroup');
	}
	
	final protected function __buildStmtOrder( $fluid = false ) 
	{
		$out = array(); 
		if( count($this->_propsOrder) ) 
		{
			$m = $this->_propModel;
			foreach( $this->_propsOrder as $field ) 
			{
				$f = $field['name'];
				$o = ('DESC'===$field['orient'])?-1:1; 
				$out[$f] = $o;
			}
		} 
		return $out; 
	}
	
	final protected function __buildStmtReverseOrder() 
	{
		return array( $this->_primaryKey => -1 ); 
	}
	
	final protected function __buildStmtOneRange() 
	{
		return array( 'limit' => 1, 'skip' => 0 );
	}
	
	final protected function __buildStmtImport() 
	{
		$this->__abort('__buildStmtImport'); 
	}
	
	final protected function __buildStmtImportOnce() 
	{
		$this->__abort('__buildStmtImportOnce');
	}
	
	final protected function __resetAutoIncrement() {}
	
	final protected function __func( $args ) 
	{ 
		$this->__abort('__func'); 
	}
	
	final protected function __proc( $args ) 
	{
		return call_user_func_array(array($this, '__func'), array($args)); 
	}
	
	final protected function __view( $view ) 
	{
		$this->__abort('__view');
	}
	
	protected function __custom( $args, $argsNum, $mn="Custom" ) 
	{
		try 
		{ 
			if( $argsNum ) 
			{
				throw new Exception( "<b><i>[EXCEPTION]</i></b> Use the driver class object instead of this class." ); 
			}
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	}
	
	final protected function __search( $args, $argsNum ) 
	{
		try 
		{ 
			if( $argsNum ) 
			{
				throw new Exception( "Usage the method <b><code>Model::first</code></b> is incorrect." ); 
			} 
			
			$filters = $this->__buildStmtCondition(); 
			
			$range = $this->__buildStmtRange(); 
			$limit = $range["limit"]; 
			$skip = $range["skip"];
			
			$projection = $this->__buildStmtProjection(); 
			
			$sort = $this->__buildStmtOrder(); 

			$options = array(); 
			$options = array_merge( $options, compact('limit') ); 
			$options = array_merge( $options, compact('skip') ); 
			$options = array_merge( $options, compact('sort') ); 
			$cr = call_user_func_array([$this, '__query'], array([$filters, $options], 2)); 
			
			if( $cr ) 
			{
				$out = $this->__fetchResult( $cr ); 
				$this->__clear(); 
				return $out;
			} 
			$this->__clear(); 
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, "<b>[EXCEPTION]</b> ".$e->getMessage() );
		} 
		return []; 
	}
	
	final protected function __find( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
			{
				$id = current($args);
				$filters = array( $this->_primaryKey => (is_object($id))?$id:$this->__parseIdStmt($id) ); 
				$limit = 1; 
				$projection = $this->__buildStmtProjection(); 
				$options = array(); 
				$options = array_merge( $options, compact('limit') ); 
				$options = array_merge( $options, compact('projection') ); 
				$cr = call_user_func_array([$this, '__query'], array([$filters, $options], 2)); 
				$this->__clear(); 
				if( $cr ) 
				{
					$out = $this->__fetchResult( $cr ); 
					return (isset($out[0]))?$out[0]:[];
				} 
			} 
			else 
			{
				throw new Exception( "Usage the method <b><code>Model::find</code></b> is incorrect." ); 
			}
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, "<b>[EXCEPTION]</b> ".$e->getMessage() ); 
		}
		return [];
	} 
	
	final protected function __first( $args, $argsNum ) 
	{
		try 
		{ 
			if( $argsNum ) 
			{
				throw new Exception( "Usage the method <b><code>Model::first</code></b> is incorrect." ); 
			} 
			
			$filters = $this->__buildStmtCondition(); 
			
			$range = $this->__buildStmtOneRange(); 
			$limit = $range["limit"]; 
			$skip = $range["skip"];
			
			$projection = $this->__buildStmtProjection(); 
			
			$sort = array( $this->_primaryKey => 1 ); 

			$options = array(); 
			$options = array_merge( $options, compact('limit') ); 
			$options = array_merge( $options, compact('skip') ); 
			$options = array_merge( $options, compact('sort') ); 
			$options = array_merge( $options, compact('projection') );
			
			$cr = call_user_func_array([$this, '__query'], array([$filters, $options], 2)); 
			$this->__clear(); 
			
			if( $cr ) 
			{
				$out = $this->__fetchResult( $cr ); 
				return $out[0];
			} 
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, "<b>[EXCEPTION]</b> ".$e->getMessage() );
		} 
		return []; 
	}
	
	final protected function __last( $args, $argsNum ) 
	{
		try 
		{ 
			if( $argsNum ) 
			{
				throw new Exception( "Usage the method <b><code>Model::first</code></b> is incorrect." ); 
			} 
			
			$filters = $this->__buildStmtCondition(); 
			
			$range = $this->__buildStmtOneRange(); 
			$limit = $range["limit"]; 
			$skip = $range["skip"];
			
			$projection = $this->__buildStmtProjection(); 
			
			$sort = $this->__buildStmtReverseOrder(); 
			
			$options = array(); 
			$options = array_merge( $options, compact('limit') ); 
			$options = array_merge( $options, compact('skip') ); 
			$options = array_merge( $options, compact('sort') ); 
			$options = array_merge( $options, compact('projection') );
			
			$cr = call_user_func_array([$this, '__query'], array([$filters, $options], 2)); 
			$this->__clear(); 
			
			if( $cr ) 
			{
				$out = $this->__fetchResult( $cr ); 
				return $out[0];
			} 
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, "<b>[EXCEPTION]</b> ".$e->getMessage() );
		} 
		return []; 
	}
	
	final protected function __entity( $args, $argsNum ) 
	{ 
		try 
		{
			if( $argsNum ) 
			{
				$pk = $this->_primaryKey;
				$this->$pk = current($args); 
				$item = call_user_func_array(array($this, '__item'), array([':id', $this->$pk], 2)); 
				$this->__clear(true); 
				$this->$pk = current($args); 
				foreach($item as $field => $value) 
					$this->$field = $value;
				return $this;
			} 
			else 
				throw new Exception( "Usage <strong>Model::entity()</strong> is incorrect." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	}
	
	final protected function __total( $args, $argsNum ) 
	{
		$command = [ "count" => $this->_propCollection ]; 
		$filters = $this->_propQuery[0]; 
		if( !empty($filter) ) 
			$command["query"] = $filter; 
		$cr = call_user_func_array([$this, '__execute'], array([$command])); 
		$this->__clear(); 
		if( $cr->isDead() ) return;
		$rs = $cr->toArray(); 
		return $rs[0]->n;
	}
	
	protected function __insert( $args, $argsNum ) 
	{ 
		$this->__abort('__insert');
	}
	
	protected function __delete( $args, $argsNum ) 
	{
		$this->__abort('__delete');
	}
	
	protected function __save( $args, $argsNum ) 
	{
		$this->__abort('__save');
	}
	
	protected function __add( $data ) 
	{
		$this->__abort('__add');
	}
	
	final protected function __count( $args, $argsNum ) 
	{
		try 
		{
			if( zero===$argsNum ) 
			{ 
				$cr = call_user_func_array([$this, '__execute'], array([$this->__countStmt()])); 
				$this->__clear(); 
				if( $cr->isDead() ) return;
				$rs = $cr->toArray(); 
				return $rs[0]->n;
			} 
			else 
			{
				throw new Exception( "Usage <strong>Model::count()</strong> is incorrect." ); 
			}
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	}
	
	final protected function __distinct( $args, $argsNum ) 
	{
		$field = current($args); 
		$out = array(); 
		$command = [
			"distinct" => $this->_propCollection, 
			"key" => $field, 
		]; 
		$cr = call_user_func_array([$this, '__execute'], array([$command])); 
		$this->__clear(); 
		if( $cr->isDead() ) return $out;
		$r = $this->__fetchArray($cr->toArray())[0]; 
		if( $r['ok'] ) 
			foreach( $r['values'] as $value ) 
				$out[] = array( $this->_propModel => array($field => $value) ); 
		return $out;
	}
	
	final protected function __sum( $args, $argsNum ) 
	{
		$field = current($args); 
		$command = [
			"aggregate" => $this->_propCollection, 
			"pipeline" => [
				['$group' => array(
					'_id'=>NULL, 
					"sum" => [ '$sum' => '$'.$field ], 
				)], 
			], 
			'cursor' => new \StdClass, 
		]; 
		$cr = call_user_func_array([$this, '__execute'], array([$command])); 
		$this->__clear(); 
		if( $cr->isDead() ) return;
		return $this->__fetchArray($cr->toArray())[0]['sum'];
	}
	
	final protected function __avg( $args, $argsNum ) 
	{
		$field = current($args); 
		$command = [
			"aggregate" => $this->_propCollection, 
			"pipeline" => [
				['$group' => array(
					'_id'=>NULL, 
					"avg" => [ '$avg' => '$'.$field ], 
				)], 
			], 
			'cursor' => new \StdClass, 
		]; 
		$cr = call_user_func_array([$this, '__execute'], array([$command])); 
		$this->__clear(); 
		if( $cr->isDead() ) return;
		return $this->__fetchArray($cr->toArray())[0]['avg'];
	}
	
	final protected function __max( $args, $argsNum ) 
	{
		$field = current($args); 
		$command = [
			"aggregate" => $this->_propCollection, 
			"pipeline" => [
				['$sort' => [$field => -1]], 
				['$limit' => 1], 
			], 
			'cursor' => new \StdClass, 
		]; 
		$cr = call_user_func_array([$this, '__execute'], array([$command])); 
		$this->__clear(); 
		if( $cr->isDead() ) return;
		return $this->__fetchArray($cr->toArray())[0][$field];
		
	}
	
	final protected function __min( $args, $argsNum ) 
	{
		$field = current($args); 
		$command = [
			"aggregate" => $this->_propCollection, 
			"pipeline" => [
				['$sort' => [$field => 1]], 
				['$limit' => 1], 
			], 
			'cursor' => new \StdClass, 
		]; 
		$cr = call_user_func_array([$this, '__execute'], array([$command])); 
		$this->__clear(); 
		if( $cr->isDead() ) return;
		return $this->__fetchArray($cr->toArray())[0][$field];
	}
	
	final protected function __implode( $args, $argsNum ) 
	{
		$this->__abort('__implode');
	}
	
	final protected function __length( $args, $argsNum ) 
	{
		return call_user_func_array(array($this, '__total'), array([], 0)); 
	}
	
	protected function __dbList( $args, $argsNum ) 
	{
		$this->__abort('__dbList');
	}
	
	protected function errno() { $this->__abort('errno'); } 
	protected function error() { $this->__abort('error'); } 
	protected function insert_id() { $this->__abort('insert'); } 
	protected function escape_string( $str ) { $this->__abort('escape_string'); } 
	protected function affected_rows( $rs=NULL ) { $this->__abort('affected_rows'); } 
	protected function num_rows( $rs ) { $this->__abort('num_rows'); } 
	protected function fetch_assoc( $rs ) { $this->__abort('fetch_assoc'); } 
	protected function fetch_row( $rs ) { $this->__abort('fetch_row'); } 
	protected function free_result( $rs ) { $this->__abort('free_result'); } 
	
	protected function fetch_field( $rs, &$ts, &$fs ) 
	{
		$this->__abort('__fetch_field');
	}
	
	protected function __query( $sql ) 
	{
		$this->__abort('__query');
	}
	
	protected function __handled( $dsl, $src, $less=false ) 
	{
		$this->__abort('__handled');
	}
	
	protected function __connect( $src )
	{
		$this->__abort('__connect');
	}
	
	final protected function __duplicate() 
	{
		$out = deep_copy($this); 
		return $out->__reset(); 
	}
	
	final protected function __abort( $fn ) 
	{
		abort( 500, "<b><i>[EXCEPTION]</i></b> The function <b>NoSQLModel::{$fn}()</b> hasn't yet support for MongoDB." ); 
	}
	
}