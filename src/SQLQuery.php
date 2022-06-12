<?php

namespace Zuuda;

use Exception;
use Zuuda\Error;

abstract class SQLQuery extends QueryStmt
{
	private static $this = '\Zuuda\SQLQuery';
	protected $_tildeControl	= "`";
	abstract protected function __initConn(); 
	
	final protected function __getOperatorSymbols() 
	{
		return array(
			'between'				=>'BETWEEN', 
			'equal' 				=>'=',
			'==='					=>'=', 
			'=='					=>'=', 
			'='						=>'=', 
			'greater than'			=>'>',
			'>'						=>'>', 
			'greater than or equal'	=>'>=',
			'>='					=>'>=', 
			'in'					=>'IN', 
			'is'					=>'IS', 
			'is not'				=>'IS NOT', 
			'less than' 			=>'<',
			'<' 					=>'<', 
			'less than or equal' 	=>'<=',
			'<='					=>'<=', 
			'like'					=>'LIKE', 
			'not'					=>'NOT', 
			'not between'			=>'NOT BETWEEN',
			'not equal'				=>'!=',
			'!='					=>'!=', 
			'not in'				=>'NOT IN', 
			'not like'				=>'NOT LIKE', 
		);
	}
	
	final private function __parseFields( $data ) 
	{
		$outSql = array(); 
		foreach ( $this->_propsDescribe as $field ) 
		{
			if( $field==$this->_primaryKey && isset($data[$this->_primaryKey]) && is_numeric($data[$field]) ) 
			{
				continue; 
			} 
			elseif( array_key_exists($field, $data) ) 
			{
				if( is_null($data[$field]) ) 
				{
					$outSql[] = "{$this->_tildeControl}{$field}{$this->_tildeControl} = NULL"; 
				} 
				else 
				{
					$value = $this->escape_string( $data[$field] ); 
					$outSql[] = "{$this->_tildeControl}{$field}{$this->_tildeControl} = '{$value}'"; 
				} 
			}
			elseif( is_array($this->_eventRide) ) 
			{ 
				if( array_key_exists($field, $this->_eventRide) ) 
				{
					$value = quote.$this->escape_string($this->_eventRide[$field]).quote; 
					$outSql[] = "{$this->_tildeControl}{$field}{$this->_tildeControl} = '{$value}'"; 
					$data[$field] = $value;
				} 
			} 
			elseif( isset($this->timestamp) && is_array($this->timestamp) ) 
			{
				if( in_array($field, $this->timestamp) ) 
				{
					$value = date('Y-m-d H:i:s'); 
					$outSql[] = "{$this->_tildeControl}{$field}{$this->_tildeControl} = '{$value}'"; 
					$data[$field] = $value;
				} 
			}
		}
		return implode( comma, $outSql ); 
	}
	
	final private function __fetchCQR( $qr ) 
	{
		$out = array(); $ts; $fs; 
		$numf = $this->fetch_field( $qr, $ts, $fs ); 
		while( $r = $this->fetch_row($qr) ) 
		{
			$tmps = array(); 
			for( $i=head; $i<$numf; $i++ ) 
				$tmps[$ts[$i]][$fs[$i]] = $r[$i];
			array_push($out, $tmps);
		} 
		return $out; 
	}
	
	final private function __fetchResult( $qr ) 
	{
		global $configs;
		$out = array(); 
		if( $qr ) 
		{
			$ts; 
			$fs; 
			$numf = $this->fetch_field( $qr, $ts, $fs ); 
			while( $r = $this->fetch_row($qr) ) 
			{
				$tmps = array(); 
				for( $i=head; $i<$numf; $i++ ) 
				{
					if(in_array($ts[$i], $this->_propsDeathMdl)) 
						continue;
					else
						$tmps[$ts[$i]][$fs[$i]] = $r[$i];
				}
				
				if( $this->_flagHasMany && !empty($this->_propsHasMany) ) 
					foreach( $this->_propsHasMany as $key => $model ) 
						if( $model->isLive() )
							$tmps[$key] = $model->where($model->GetForeignKey(), $tmps[$this->_propModel][$this->_primaryKey])->search();
				if( $this->_flagHasMABTM && !empty($this->_propsHasMABTM) ) 
					foreach($this->_propsHasMABTM as $key => $model ) 
						if($model['data']->isLive()) 
						{
							$main = $model['data']; 
							$join = $model['join']; 
							$configs['tmp_fk'] = $join['fk_main'];
							$configs['tmp_val'] = $tmps[$this->_propModel][$this->_primaryKey];
							$main->merge($join['md_name'],$join['as_name'],$join['fk_data'],$main->getPrimaryKey(), function($merge) 
							{
								global $configs;
								$merge->whereOn( $configs['tmp_fk'], $configs['tmp_val'] ); 
								unset( $configs['tmp_fk'], $configs['tmp_val'] );
							});
							$tmps[$key] = $main->search();
						}
						
				array_push($out,$tmps); 
			} 
			$this->free_result($qr); 
		} 
		$this->__clear(); 
		return $out; 
	}
	
	protected function __parseDescribe( $tableName ) 
	{
		global $_cache;
		$describe = $_cache->get('describe_'.$tableName);
		if( (empty($describe) || NULL===$describe) && $this->_dbHandle ) 
		{
			$describe = array();
			$sql = 'DESCRIBE '.$tableName;
			$result = $this->__query( $sql );
			while ($row = $this->fetch_row($result)) 
				array_push($describe,$row[0]); 
			$this->free_result($result);
			$_cache->set('describe_'.$tableName,$describe);
		}
		return $describe; 
	} 
	
	protected function __parseSqlLabelField( $field ) 
	{
		$sql = EMPTY_CHAR; 
		if( NULL!==$field['label'] ) 
		{
			$sql .= " AS {$this->_tildeControl}{$field['label']}{$this->_tildeControl}"; 
		} 
		return $sql; 
	}
	
	protected function __parseSqlStartTransaction() { return "START TRANSACTION"; } 
	protected function __parseSqlCommitTransaction() { return "COMMIT"; } 
	protected function __parseSqlRollbackTransaction() { return "ROLLBACK"; } 
	protected function __parseSqlRollbackCheckpoint( $pt ) { return "ROLLBACK TO SAVEPOINT ".$pt; } 
	protected function __parseSqlReleaseCheckpoint( $pt ) { return "RELEASE SAVEPOINT ".$pt; } 
	protected function __parseSqlCreateCheckpoint( $pt ) { return "SAVEPOINT ".$pt; } 
	
	final protected function __parseSqlSelection( $m, $d ) 
	{
		$sqls = array(); 
		foreach( $d as $f ) 
		{
			if( isset($f['cmd']) && !isset($f['name']) )
			{ 
				$sql = " ".$f['cmd'];
			} 
			else if( isset($f['cmd']) && isset($f['name']) ) 
			{
				$sql = " {$f['cmd']}({$this->_tildeControl}{$m}{$this->_tildeControl}.{$this->_tildeControl}{$f['name']}{$this->_tildeControl})"; 
			} 
			else 
			{
				$sql = " {$this->_tildeControl}{$m}{$this->_tildeControl}.{$this->_tildeControl}{$f['name']}{$this->_tildeControl}"; 
			}
			$sql .= $this->__parseSqlLabelField($f); 
			$sqls[] = $sql; 
		}
		return $sqls; 
	} 
	
	final protected function __parseSqlConditionOr( $m, $c ) 
	{
		$sqls = array(); 
		foreach( $c as $f ) 
		{ 
			if( is_numeric($f[2]) ) 
			{
				$sqls[] = "{$this->_tildeControl}{$m}{$this->_tildeControl}.{$this->_tildeControl}{$f[0]}{$this->_tildeControl} {$f[1]} {$f[2]} ";
			} 
			elseif( is_null($f[2]) ) 
			{
				$sqls[] = "{$this->_tildeControl}{$m}{$this->_tildeControl}.{$this->_tildeControl}{$f[0]}{$this->_tildeControl} {$f[1]} NULL ";
			}
			elseif( is_array($f[2]) ) 
			{
				$values = array(); 
				foreach( $f[2] as $value ) 
					if( is_numeric($value) ) 
						$values[] = $value; 
					else 
						$values[] = "'".$this->escape_string($value)."'"; 
				switch($f[1]) 
				{
					case 'BETWEEN': 
					case 'NOT BETWEEN': 
						$f[2] = implode(' AND ', $values); 
						break; 
					default: 
						$f[2] = "(".implode(comma, $values).")"; 
						break;
				}
				$sqls[] = "{$this->_tildeControl}{$m}{$this->_tildeControl}.{$this->_tildeControl}{$f[0]}{$this->_tildeControl} {$f[1]} {$f[2]}";
			}
			else 
			{
				$sqls[] = "{$this->_tildeControl}{$m}{$this->_tildeControl}.{$this->_tildeControl}{$f[0]}{$this->_tildeControl} {$f[1]} '{$this->escape_string($f[2])}' ";
			}
		} 
		return $sqls;
	} 
	
	final protected function __parseSqlConditionCmd( $m, $c ) { leave($m); }
	
	final protected function __parseSqlCondition( $m, $c ) 
	{
		$sqls = array(); 
		foreach( $c as $f ) 
		{ 
			if( !isset($f[2]) || is_null($f[2]) ) 
			{
				if( array_key_exists('OR', $f) ) 
				{
					$orSqls = $this->__parseSqlConditionOr( $m, $f['OR'] ); 
					$sqls[] = "( ".implode(' OR ', $orSqls).") "; 
				} 
				else 
				{
					$sqls[] = "{$this->_tildeControl}{$m}{$this->_tildeControl}.{$this->_tildeControl}{$f[0]}{$this->_tildeControl} {$f[1]} NULL ";
				}
			}
			elseif( is_numeric($f[2]) ) 
			{
				$sqls[] = "{$this->_tildeControl}{$m}{$this->_tildeControl}.{$this->_tildeControl}{$f[0]}{$this->_tildeControl} {$f[1]} {$f[2]} ";
			} 
			elseif( is_array($f[2]) ) 
			{
				$values = array(); 
				foreach( $f[2] as $value ) 
					if( is_numeric($value) ) 
						$values[] = $value; 
					else 
						$values[] = "'".$this->escape_string($value)."'"; 
				switch($f[1]) 
				{
					case 'BETWEEN': 
					case 'NOT BETWEEN': 
						$f[2] = implode(' AND ', $values); 
						break; 
					default: 
						$f[2] = "(".implode(comma, $values).")"; 
						break;
				}
				$sqls[] = "{$this->_tildeControl}{$m}{$this->_tildeControl}.{$this->_tildeControl}{$f[0]}{$this->_tildeControl} {$f[1]} {$f[2]}";
			}
			else 
			{
				$sqls[] = "{$this->_tildeControl}{$m}{$this->_tildeControl}.{$this->_tildeControl}{$f[0]}{$this->_tildeControl} {$f[1]} '{$this->escape_string($f[2])}' ";
			}
		} 
		return $sqls;
	} 
	
	final protected function __parseSqlHasOne() 
	{
		return "LEFT JOIN {$this->_tildeControl}{$this->_propTable}{$this->_tildeControl} AS {$this->_tildeControl}{$this->_propModel}{$this->_tildeControl} ON {$this->_tildeControl}{$this->_propModel}{$this->_tildeControl}.{$this->_tildeControl}{$this->_propForeignKey}{$this->_tildeControl} = {$this->_tildeControl}{$this->_propAliasModel}{$this->_tildeControl}.{$this->_tildeControl}{$this->_propAliasKey}{$this->_tildeControl} "; 
	} 
	
	final protected function __parseSqlMerge() 
	{
		$conds = implode(space, $this->__buildSqlConditionOn( $this->_propModel )); 
		return "INNER JOIN {$this->_tildeControl}{$this->_propTable}{$this->_tildeControl} AS {$this->_tildeControl}{$this->_propModel}{$this->_tildeControl} ON {$this->_tildeControl}{$this->_propModel}{$this->_tildeControl}.{$this->_tildeControl}{$this->_propForeignKey}{$this->_tildeControl} = {$this->_tildeControl}{$this->_propAliasModel}{$this->_tildeControl}.{$this->_tildeControl}{$this->_propAliasKey}{$this->_tildeControl} {$conds} "; 
	} 
	
	final protected function __parseSqlMergeLeft() 
	{
		$conds = implode(space, $this->__buildSqlConditionOn( $this->_propModel )); 
		return "LEFT JOIN {$this->_tildeControl}{$this->_propTable}{$this->_tildeControl} AS {$this->_tildeControl}{$this->_propModel}{$this->_tildeControl} ON {$this->_tildeControl}{$this->_propModel}{$this->_tildeControl}.{$this->_tildeControl}{$this->_propForeignKey}{$this->_tildeControl} = {$this->_tildeControl}{$this->_propAliasModel}{$this->_tildeControl}.{$this->_tildeControl}{$this->_propAliasKey}{$this->_tildeControl} {$conds} "; 
	} 
	
	final protected function __parseSqlMergeRight() 
	{
		$conds = implode(space, $this->__buildSqlConditionOn( $this->_propModel )); 
		return "RIGHT JOIN {$this->_tildeControl}{$this->_propTable}{$this->_tildeControl} AS {$this->_tildeControl}{$this->_propModel}{$this->_tildeControl} ON {$this->_tildeControl}{$this->_propModel}{$this->_tildeControl}.{$this->_tildeControl}{$this->_propForeignKey}{$this->_tildeControl} = {$this->_tildeControl}{$this->_propAliasModel}{$this->_tildeControl}.{$this->_tildeControl}{$this->_propAliasKey}{$this->_tildeControl}  {$conds} "; 
	}
	
	final protected function __buildSqlSelection() 
	{
		$defSql = "SELECT"; 
		$outSql = EMPTY_STRING; 
		
		$sqls = $this->__parseSqlSelection( $this->_propModel, $this->_propsUndescribe ); 
		
		if( $this->_flagHasOne ) 
			if( $this->_flagHasOne && !empty($this->_propsHasOne) ) 
				foreach( $this->_propsHasOne as $model ) 
					if( $model->isLive() ) 
						$sqls = array_unique(array_merge($sqls, $model->parseSqlSelection())); 
					
		if( !empty($this->_propsMerge) ) 
			foreach( $this->_propsMerge as $model ) 
				$sqls = array_unique(array_merge($sqls, $model->parseSqlSelection())); 
		
		if( !empty($this->_propsMergeLeft) ) 
			foreach( $this->_propsMergeLeft as $model ) 
				$sqls = array_unique(array_merge($sqls, $model->parseSqlSelection())); 
		
		if( !empty($this->_propsMergeRight) ) 
			foreach( $this->_propsMergeRight as $model ) 
				$sqls = array_unique(array_merge($sqls, $model->parseSqlSelection()));
		
		$outSql = $defSql . implode( comma, $sqls ) . space; 
		return $outSql; 
	} 
	
	final protected function __buildSqlFrom() 
	{
		$defSql = "FROM"; 
		$outSql = "{$this->_tildeControl}{$this->_propTable}{$this->_tildeControl} AS {$this->_tildeControl}{$this->_propModel}{$this->_tildeControl} "; 
		
		if( $this->_flagHasOne ) 
			if( $this->_flagHasOne && !empty($this->_propsHasOne) ) 
				foreach( $this->_propsHasOne as $model ) 
					$outSql .= $model->parseSqlHasOne(); 
					
		if( !empty($this->_propsMerge) ) 
			foreach( $this->_propsMerge as $model ) 
				$outSql .= $model->parseSqlMerge(); 
		
		if( !empty($this->_propsMergeLeft) ) 
			foreach( $this->_propsMergeLeft as $model ) 
				$outSql .= $model->parseSqlMergeLeft(); 
		
		if( !empty($this->_propsMergeRight) ) 
			foreach( $this->_propsMergeRight as $model ) 
				$outSql .= $model->parseSqlMergeRight(); 
		
		$outSql = $defSql . space . $outSql; 
		return $outSql;
	} 
	
	final protected function __buildSqlConditionOn( $propModel ) 
	{
		$conds = array(); 
		foreach( $this->_propsCondOn as $key => $cond ) 
		{
			$cond[0] = "AND {$this->_tildeControl}{$propModel}{$this->_tildeControl}.{$this->_tildeControl}{$cond[0]}{$this->_tildeControl}"; 
			$conds[] = implode(space, $cond);
		} 
		return $conds; 
	}
	
	final protected function __buildSqlCondition( $propModel = NULL, $fluid = false ) 
	{ 
		if( $fluid ) 
			$defSql = EMPTY_CHAR; 
		else 
			$defSql = "WHERE 1=1"; 
		if( NULL===$propModel ) 
			$propModel = $this->_propModel; 
		$outSql = EMPTY_STRING; 
		$sqls = array(); 
		$orSqls = array();
		if( !empty($this->_propsCondEx) )
		{
			foreach($this->_propsCondEx as $key => $conds) 
			{
				$sqls += $this->__parseSqlCondition( $key, $conds ); 
			} 
		} 
		if( !empty($this->_propsCond) ) 
		{
			$sqls += $this->__parseSqlCondition( $propModel, $this->_propsCond ); 
		}
		if( !empty($this->_propsCondOr) ) 
			$orSqls += $this->__parseSqlConditionOr( $propModel, $this->_propsCondOr ); 
		if( !empty($this->_propsCondCmd) ) 
			$sqls += $this->__parseSqlConditionCmd( $propModel, $this->_propsCond ); 
		if( !empty($sqls) ) 
			$outSql .= "AND ";
		$outSql .= implode("AND ", $sqls);
		if(!empty($orSqls))
			$outSql .= ' OR '.implode("AND ", $orSqls);
		$outSql = $defSql . space . $outSql;
		return $outSql; 
	} 
	
	final protected function __buildSqlRange() 
	{
		$outSql = EMPTY_STRING;
		if( $this->_propLimit ) 
		{
			if( isset($this->_propPage) )  
				$offset = ( $this->_propPage-1 ) * $this->_propLimit;
			else
				$offset = $this->_propOffset;
			$outSql .= "LIMIT {$this->_propLimit} OFFSET {$offset} ";
		} 
		return $outSql;
	} 
	
	final protected function __buildSqlGroup() 
	{
		$outSql = EMPTY_STRING;
		$pregMatchFunc = "#(.*?)\((?<=\()(.+)(?=\))\)#is";
		if( count($this->_propsGroupBy) ) 
		{
			$sqls = array(); 
			$outSql = "GROUP BY ";
			$m = $this->_propModel; 
			foreach( $this->_propsGroupBy as $field ) 
			{ 
				$f = $field['name']; 
				$match = null;
				if( preg_match($pregMatchFunc, $f, $match) )
				{
					$f = trim($match[2]);
					$sql = $match[1]."({$this->_tildeControl}{$m}{$this->_tildeControl}.{$this->_tildeControl}{$f}{$this->_tildeControl})"; 
				}
				else 
				{
					$sql = "{$this->_tildeControl}{$m}{$this->_tildeControl}.{$this->_tildeControl}{$f}{$this->_tildeControl}"; 
				}
				if( isset($field['cmd']) ) 
				{ 
					$c = $field['cmd']; 
					$sqls[] = "{$c}({$sql})"; 
				} 
				else 
				{
					$sqls[] = $sql; 
				} 
			} 
			$outSql .= implode(', ', $sqls)." "; 
		} 
		return $outSql; 
	} 
	
	final protected function __buildSqlOrder( $fluid = false ) 
	{
		if( $fluid )
			$defSql = EMPTY_CHAR;
		else 
			$defSql = "ORDER BY"; 
		$outSql = EMPTY_STRING;
		if( count($this->_propsOrder) ) 
		{
			$m = $this->_propModel;
			foreach( $this->_propsOrder as $field ) 
			{
				$f = $field['name'];
				$o = $field['orient']; 
				if( isset($field['cmd']) ) 
				{ 
					$c = $field['cmd']; 
					$outSql .= " {$c}({$this->_tildeControl}{$m}{$this->_tildeControl}.{$this->_tildeControl}{$f}{$this->_tildeControl}) {$o} ";
				} 
				else 
				{
					$outSql .= " {$this->_tildeControl}{$m}{$this->_tildeControl}.{$this->_tildeControl}{$f}{$this->_tildeControl} {$o} "; 
				}
			}
		}
		if( EMPTY_STRING!==$outSql ) 
			$outSql = $defSql . space . $outSql; 
		return $outSql;
	} 
	
	final protected function __buildSqlIdCondition( $value ) 
	{
		$outSql = EMPTY_STRING; 
		if( is_string($value) ) 
		{
			$value = $this->escape_string($value);
			$outSql = "WHERE {$this->_tildeControl}{$this->_propModel}{$this->_tildeControl}.{$this->_tildeControl}{$this->_primaryKey}{$this->_tildeControl} = '$value' "; 
		} 
		else if( is_numeric($value) )
		{
			$outSql = "WHERE {$this->_tildeControl}{$this->_propModel}{$this->_tildeControl}.{$this->_tildeControl}{$this->_primaryKey}{$this->_tildeControl} = $value "; 
		} 
		return $outSql;
	} 
	
	final protected function __buildSqlReverseOrder() 
	{
		return "ORDER BY {$this->_tildeControl}{$this->_propModel}{$this->_tildeControl}.{$this->_tildeControl}{$this->_primaryKey}{$this->_tildeControl} DESC ";
	}
	
	final protected function __buildSqlOneRange() 
	{
		return "LIMIT 1 OFFSET 0 ";
	} 
	
	final protected function __buildSqlImport() 
	{
		$sql = EMPTY_CHAR;
		if( !empty($this->_propsImportAll) ) 
		{
			$tmp = array();
			foreach( $this->_propsImportAll as $model ) 
				$tmp[] = $model->fluidSqlQuery(); 
			$sql = ' UNION ALL '. implode(' UNION ALL ', $tmp); 
		} 
		return $sql;
	} 
	
	final protected function __buildSqlImportOnce() 
	{
		$sql = EMPTY_CHAR;
		if( !empty($this->_propsImport) ) 
		{
			$tmp = array();
			foreach( $this->_propsImport as $model ) 
				$tmp[] = $model->fluidSqlQuery(); 
			$sql = ' UNION '. implode(' UNION ', $tmp); 
		} 
		return $sql;
	} 
	
	final protected function __resetAutoIncrement() 
	{
		$sql = ["ALTER TABLE {$this->_tildeControl}{$this->_propTable}{$this->_tildeControl} AUTO_INCREMENT = 1"]; 
		$this->call_user_func_array([$this, mcbm_custom], array($sql, count($sql))); 
		return $this; 
	}
	
	final protected function __func( $args ) 
	{ 
		$out = array(); 
		$params = array_splice( $args, 1 ); 
		$name = $args[0]; 
		if( empty($params) ) 
		{
			$sql = "SELECT {$func}() AS {$this->_tildeControl}data{$this->_tildeControl}"; 
		} 
		else 
		{
			foreach( $params as $key => $param ) 
			{ 
				if( is_string($param) ) 
				{
					$params[$key] = "'".$this->escape_string($param)."'"; 
				} 
				else if( is_numeric($param) ) 
				{ 
					$params[$key] = $param; 
				} 
			} 
			$params = implode(',', $params); 
			$sql = "SELECT {$name}({$params}) as {$this->_tildeControl}data{$this->_tildeControl}"; 
		} 
		$result = $this->__query( $sql ); 
		if( $this->num_rows($result) ) 
		{ 
			$mem = $this->_propModel;
			$this->__setModel( $name );
			$data = $this->__fetchCQR($result);
			$out = array_merge( $out, $data[0] ); 
			$this->free_result( $result ); 
			$this->__setModel( $mem );
		} 
		return $out;
	} 
	
	final protected function __proc( $args ) 
	{ 
		$out = array();
		$params = array_splice( $args, 1 ); 
		$name = $args[0]; 
		if( empty($params) ) 
		{
			$sql = "CALL {$name}()"; 
			return $this->__query($sql); 
		} 
		else 
		{
			$iparams = array(); 
			$oparams = array(); 
			foreach( $params as $key => $param ) 
			{ 
				if( is_string($param) ) 
				{
					$p = '#^&(.*)#'; 
					if(preg_match($p, $param, $m)) 
					{
						$iparams[] = "@".$m[1]; 
						$oparams[] = $m[1]; 
						continue; 
					}
					$iparams[] = "'".$this->escape_string($param)."'"; 
				} 
				else if( is_numeric($param) ) 
				{ 
					$iparams[] = $param; 
				} 
			} 
			$iparams = implode(',', $iparams); 
			$sql = "CALL {$name}({$iparams})"; 
			if( $result = $this->__query($sql) ) 
			{
				foreach( $oparams as $key => $param ) 
				{
					$param = $this->escape_string($param); 
					$oparams[$key] = "@{$param} AS {$this->_tildeControl}{$param}{$this->_tildeControl}"; 
				} 
				$sql = "SELECT ".implode( ", ", $oparams ); 
			} 
			else 
			{
				return $result; 
			}
		} 
		$result = $this->__query( $sql ); 
		if( $this->num_rows($result) ) 
		{ 
			$mem = $this->_propModel;
			$this->__setModel( $name );
			$data = $this->__fetchCQR($result);
			$out = array_merge( $out, $data[0] ); 
			$this->free_result( $result ); 
			$this->__setModel( $mem );
		} 
		return $out; 
	} 
	
	final protected function __view( $view ) 
	{
		$out = array(); 
		$sql = "SELECT * FROM ".$view; 
		$result = $this->__query( $sql ); 
		if( $this->num_rows($result) ) 
		{
			$out = array_merge( $out, $this->__fetchCQR($result) ); 
			$this->free_result( $result ); 
			return $out;
		} 
		return $out;
	}
	
	final protected function __custom( $args, $argsNum, $mn="Custom" ) 
	{
		try 
		{
			if( $argsNum ) 
			{
				$sql = current( $args );
				$sql = str_replace( ':table', $this->_propTable, $sql ); 
				$sql = str_replace( ':model', $this->_propModel, $sql ); 
				$qr = $this->__query( $sql ); 
				if( $qr ) 
				{
					$out = $qr; 
					if( !is_bool($out) ) 
					{
						$out = $this->__fetchCQR( $qr ); 
						$this->free_result( $qr ); 
					}
					return $out;
				} 
				return true;
			} 
			else 
				throw new Exception( "Usage <strong>Model::".$mn."()</strong> is incorrect." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __search( $args, $argsNum ) 
	{
		return $this->__fetchResult( $this->__query($this->__buildSqlQuery()) ); 
	} 
	
	final protected function __find( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
			{
				$selectSql = $this->__buildSQLSelection(); 
				$fromSql = $this->__buildSqlFrom(); 
				$condSql = $this->__buildSqlIdCondition( current($args) ); 
				$groupSql = $this->__buildSqlGroup(); 
				$orderSql = $this->__buildSqlOrder(); 
				$rangeSql = $this->__buildSqlRange(); 
				$sql = $selectSql . $fromSql . $condSql . $groupSql . $orderSql . $rangeSql; 
				$queryResult = $this->__query( $sql ); 
				$out = $this->__fetchResult($queryResult); 
				if( isset($out[head]) )
					return $out[head]; 
				else 
					return $out;
			} 
			else 
				throw new Exception( "Usage <strong>Model::find()</strong> is incorrect." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		}
	}
	
	final protected function __first( $args, $argsNum ) 
	{
		try 
		{ 
			if( 0===$argsNum ) 
			{
				$selectSql = $this->__buildSQLSelection(); 
				$fromSql = $this->__buildSqlFrom(); 
				$condSql = $this->__buildSqlCondition(); 
				$groupSql = $this->__buildSqlGroup(); 
				$orderSql = $this->__buildSqlOrder(); 
				$rangeSql = $this->__buildSqlOneRange(); 
				$sql = $selectSql . $fromSql . $condSql . $groupSql . $orderSql . $rangeSql; 
				$queryResult = $this->__query( $sql ); 
				$dataResult = $this->__fetchResult($queryResult); 
				if( isset($dataResult[head]) )
					return $dataResult[head]; 
				else 
					return $dataResult;
			} 
			else 
				throw new Exception( "Usage <strong>Model::first()</strong> is incorrect." );
		}
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __last( $args, $argsNum ) 
	{
		try 
		{ 
			if( 0===$argsNum ) 
			{
				$selectSql = $this->__buildSQLSelection(); 
				$fromSql = $this->__buildSqlFrom(); 
				$condSql = $this->__buildSqlCondition(); 
				$groupSql = $this->__buildSqlGroup(); 
				$orderSql = $this->__buildSqlReverseOrder(); 
				$rangeSql = $this->__buildSqlOneRange(); 
				$sql = $selectSql . $fromSql . $condSql . $groupSql . $orderSql . $rangeSql; 
				$queryResult = $this->__query( $sql ); 
				$dataResult = $this->__fetchResult($queryResult); 
				if( isset($dataResult[head]) )
					return $dataResult[head]; 
				else 
					return NULL;
			} 
			else 
				throw new Exception( "Usage <strong>Model::last()</strong> is incorrect." );
		}
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	}
	
	final protected function __entity( $args, $argsNum ) 
	{ 
		try 
		{
			if( $argsNum ) 
			{
				$pk = $this->_primaryKey;
				$this->$pk = current($args); 
				$item = call_user_func_array(array($this, '__item'), array([':pk', $this->$pk], 2)); 
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
		try 
		{
			if( zero===$argsNum ) 
			{
				if( $this->_propLimit ) 
					$pattern = "/SELECT (.*?) FROM (.*)LIMIT(.*)/i";
				else
					$pattern = "/SELECT (.*?) FROM (.*)/i"; 
				
				$replacement = "SELECT COUNT({$this->_tildeControl}".$this->_propModel."{$this->_tildeControl}.{$this->_tildeControl}".$this->_primaryKey."{$this->_tildeControl}) AS {$this->_tildeControl}total{$this->_tildeControl} FROM $2";
				$sql = preg_replace( $pattern, $replacement, $this->_querySQL );
				$qr = $this->__query( $sql ); 
				$this->__clear();
				
				if( $qr ) 
				{
					$result = $this->fetch_assoc( $qr ); 
					$this->free_result( $qr ); 
					return (int)$result['total'];
				} 
				
				else return null; 
			} 
			else 
				throw new Exception( "Usage <strong>Model::total()</strong> is unvalidable." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
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
						$data[$key] = $this->escape_string($value); 
					}
					else if( is_array($value) ) 
					{
						$multiple_row = true; 
						foreach( $value as $i => $val ) 
						{ 
							$value[$i] = $this->escape_string($val);
						}
						$data[$key] = "('".implode("','", $value)."')"; 
					}
				}
				if( $multiple_row ) 
				{
					$f = implode("{$this->_tildeControl},{$this->_tildeControl}", $fields);
					$v = implode(", ", $data); 
					$sql = ("INSERT INTO {$this->_tildeControl}{$this->_propTable}{$this->_tildeControl} ({$this->_tildeControl}{$f}{$this->_tildeControl}) VALUES {$v}"); 
					$result = $this->__query($sql); 
					return $this; 
				} 
				else 
				{
					$f = implode("{$this->_tildeControl},{$this->_tildeControl}", $fields);
					$v = implode("','", $data); 
					$sql = ("INSERT INTO {$this->_tildeControl}{$this->_propTable}{$this->_tildeControl} ({$this->_tildeControl}{$f}{$this->_tildeControl}) VALUES ('{$v}')"); 
					$result = $this->__query($sql); 
					return $this; 
				}
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
	
	final protected function __delete( $args, $argsNum ) 
	{
		try 
		{
			$oneArg = 1; 
			if( $oneArg===$argsNum ) 
			{
				$data = current($args); 
				if( method_exists($this, 'down') ) 
					$this->_eventDown = $this->down( array_merge($this->_propsRole, array($this->_primaryKey=>$data)) ); 
				$condSql = $this->__buildSqlCondition(); 
				if( is_string($data) ) 
					$deleteCondSql = "AND {$this->_tildeControl}{$this->_propTable}{$this->_tildeControl}.{$this->_tildeControl}{$this->_primaryKey}{$this->_tildeControl} = '{$data}'"; 
				elseif( is_numeric($data) ) 
					$deleteCondSql = "AND {$this->_tildeControl}{$this->_propTable}{$this->_tildeControl}.{$this->_tildeControl}{$this->_primaryKey}{$this->_tildeControl} =  {$data} "; 
				$deleteSql = "DELETE FROM {$this->_tildeControl}{$this->_propTable}{$this->_tildeControl} "; 
				$sql = $deleteSql . $condSql . $deleteCondSql; 
			} 
			else if( 1<$argsNum ) 
			{
				$data = $args; 
				if( method_exists($this, 'down') ) 
					$this->_eventDown = $this->down( $data ); 
				$condSql = str_replace("{$this->_tildeControl}{$this->_propModel}{$this->_tildeControl}.", "", $this->__buildSqlCondition()); 
				$deleteCondSql = "AND {$this->_tildeControl}{$this->_propTable}{$this->_tildeControl}.{$this->_tildeControl}{$this->_primaryKey}{$this->_tildeControl} IN (".implode(comma, $data).")"; 
				$rangeSql = $this->__buildSqlRange(); 
				$deleteSql = "DELETE FROM {$this->_tildeControl}{$this->_propTable}{$this->_tildeControl} "; 
				$sql = $deleteSql . $condSql . $deleteCondSql . $rangeSql; 
			} 
			else if( zero===$argsNum ) 
			{ 
				if( method_exists($this, 'down') ) 
					$this->_eventDown = $this->down( $args ); 
				if( is_null($this->{$this->_primaryKey}) ) 
					$condSql = str_replace("{$this->_tildeControl}{$this->_propModel}{$this->_tildeControl}.", "", $this->__buildSqlCondition()); 
				else 
					$condSql = " WHERE {$this->_tildeControl}{$this->_propTable}{$this->_tildeControl}.{$this->_tildeControl}{$this->_primaryKey}{$this->_tildeControl} = {$this->{$this->_primaryKey}} ";
				$deleteSql = "DELETE FROM {$this->_tildeControl}{$this->_propTable}{$this->_tildeControl} "; 
				$sql = $deleteSql . $condSql; 
			} 
			$qr = $this->__query( $sql ); 
			$this->clear(); 
			if( $qr ) 
			{
				$data = ( $oneArg===$argsNum || 1<$argsNum ) ? array_merge($this->_propsRole, array($this->_primaryKey=>$data)) : $data = $this->_propsRole; 
				if( method_exists($this, 'ondown') ) 
					$this->_eventOnDown = $this->ondown( $data ); 
				return $data; 
			}
			else 
			{
				return $args; 
			}
		}
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	}

	final protected function __save( $args, $argsNum ) 
	{
		try 
		{
			$qr;
			$data = current($args); 
			if( $argsNum && is_array($data) ) 
			{
				if( is_string(key($data)) ) 
				{
					$c = !empty($this->_propsCond); 
					$p = array_key_exists($this->_primaryKey, $data) && $data[$this->_primaryKey];
					$update = true; 
					if($c || $p) 
					{
						if(method_exists($this, 'ride')) 
							$this->_eventRide = $this->ride( array_merge($this->_propsRole, $data) );
						$saveSql = $this->__parseFields($data); 
						
						if( $c ) 
						{
							$condSql = $this->__buildSqlCondition( $this->_propTable ); 
						}
						elseif( $p ) 
						{
							if( is_string($data[$this->_primaryKey]) ) 
							{
								$condSql = "WHERE {$this->_tildeControl}{$this->_propTable}{$this->_tildeControl}.{$this->_tildeControl}{$this->_primaryKey}{$this->_tildeControl} = '{$data[$this->_primaryKey]}'"; 
								$sql = "SELECT {$this->_tildeControl}{$this->_primaryKey}{$this->_tildeControl} FROM {$this->_tildeControl}{$this->_propTable}{$this->_tildeControl} " . $condSql ." LIMIT 1"; 
								$qr = $this->__query( $sql ); 
								if( $qr ) 
								{
									if( zero===$this->num_rows($qr) ) 
										return $this->__add($data); 
								}
								else 
								{ 
									$data = $this->__getError(); 
									$update = false;
								} 
							}
							elseif( is_numeric($data[$this->_primaryKey]) ) 
							{
								$condSql = "WHERE {$this->_tildeControl}{$this->_propTable}{$this->_tildeControl}.{$this->_tildeControl}{$this->_primaryKey}{$this->_tildeControl} =  {$data[$this->_primaryKey]} "; 
							}
						} 
						
						if( $update ) 
						{
							$sql = "UPDATE {$this->_tildeControl}{$this->_propTable}{$this->_tildeControl} SET {$saveSql} {$condSql}"; 
							$qr = $this->__query( $sql ); 
							$this->clear(); 
							if( $qr ) 
							{
								$data = array_merge($this->_propsRole, $data);
								if( method_exists($this, 'onride') ) 
								{
									$this->_eventOnRide = $this->onride( $data ); 
								}
							}
							else 
							{
								$data = array();
							}
						} 
							
						return $data; 
					}
					else 
					{
						return $this->__add($data); 
					} 
				} 
				else 
				{
					check($data);
				}
			} 
			else 
			{
				$data = array();
				foreach( $this->_propsDescribe as $field ) 
					$data[$field] = $this->{$field}; 
				return call_user_func_array([$this, mcbm_save], array(array($data), 1)); 
			}
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	} 
	
	protected function __parseSqlReturning() { return ""; }
	
	final protected function __add( $data ) 
	{ 
		$fields = array();
		$values = array(); 
		if(method_exists($this, 'boot')) 
			$this->_eventBoot = $this->boot( array_merge($this->_propsRole, $data) );
		foreach ($this->_propsDescribe as $field ) 
			if( array_key_exists($field, $data) ) 
			{
				if( !isset($data[$this->_primaryKey]) && $this->_primaryKey==$field ) 
					continue;
				if( EMPTY_CHAR===$data[$field] || is_null($data[$field]) ) 
					$values[] = "NULL";
				else 
					$values[] = quote.$this->escape_string( $data[$field] ).quote;
				$fields[] = $this->_tildeControl.$field.$this->_tildeControl;
			}
			else if( is_array($this->_eventBoot) && array_key_exists($field, $this->_eventBoot) ) 
			{
				$values[] = quote.$this->_eventBoot[$field].quote; 
				$fields[] = $this->_tildeControl.$field.$this->_tildeControl;
				$data[$field] = $this->_eventBoot[$field]; 
			}
		$fields = implode( comma, $fields );
		$values = implode( ",", $values );
		$sql = "INSERT INTO {$this->_tildeControl}{$this->_propTable}{$this->_tildeControl} ({$fields}) VALUES ({$values})".$this->__parseSqlReturning(); 
		$qr = $this->__query( $sql ); 
		$this->clear(); 
		if( $qr ) 
		{
			if( EMPTY_CHAR===$this->__parseSqlReturning() ) 
			{
				$data[$this->_primaryKey] = (string) $this->insert_id( $qr ); 
			} 
			else 
			{
				$data[$this->_primaryKey] = $this->fetch_row($qr)[0];
			}
			if( method_exists($this, 'onboot') ) 
				$this->_eventOnBoot = $this->onboot( array_merge($this->_propsRole, $data) ); 
			return $data;
		} 
		else 
		{
			$data = $this->__getError(); 
		}
		return $data; 
	} 
	
	final protected function __count( $args, $argsNum ) 
	{
		try 
		{
			if( zero===$argsNum ) 
			{
				$selectSql = "SELECT COUNT({$this->_tildeControl}{$this->_propModel}{$this->_tildeControl}.{$this->_tildeControl}{$this->_primaryKey}{$this->_tildeControl}) AS {$this->_tildeControl}total{$this->_tildeControl} FROM {$this->_tildeControl}{$this->_propTable}{$this->_tildeControl} AS {$this->_tildeControl}{$this->_propModel}{$this->_tildeControl} "; 
				$condSql = $this->__buildSqlCondition(); 
				$groupSql = $this->__buildSqlGroup(); 
				$orderSql = $this->__buildSqlOrder(); 
				$rangeSql = $this->__buildSqlRange(); 
				
				$qr = $this->__query( $selectSql . $condSql . $groupSql . $orderSql . $rangeSql ); 
				$this->__clear(); 
				if( $qr ) 
				{
					$result = $this->fetch_assoc( $qr ); 
					$this->free_result( $qr ); 
					return (int)$result['total'];
				} 
			}
			else 
				throw new Exception( "Usage <strong>Model::count()</strong> is incorrect." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	}  
	
	final protected function __distinct( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
			{
				$f = current($args); 
				/** Note: when use distinct let use with select(distinct(column)) for find most available result */
				$this->_propsUndescribe = array();
				$selectSql = $this->__buildSQLSelection(); 
				if($selectSql==="SELECT ")
					$selectSql = str_replace( "SELECT ", "SELECT DISTINCT({$this->_tildeControl}{$this->_propModel}{$this->_tildeControl}.{$this->_tildeControl}{$f}{$this->_tildeControl}) ", $selectSql ); 
				else 
					$selectSql = str_replace( "SELECT ", "SELECT DISTINCT({$this->_tildeControl}{$this->_propModel}{$this->_tildeControl}.{$this->_tildeControl}{$f}{$this->_tildeControl}), ", $selectSql ); 
				$fromSql = $this->__buildSqlFrom(); 
				$condSql = $this->__buildSqlCondition(); 
				$groupSql = $this->__buildSqlGroup(); 
				$orderSql = $this->__buildSqlOrder(); 
				$rangeSql = $this->__buildSqlRange(); 
				$sql = $selectSql . $fromSql . $condSql . $groupSql . $orderSql . $rangeSql; 
				$qr = $this->__query( $sql ); 
				return $this->__fetchResult( $qr ); 
			}
			else 
				throw new Exception( "Usage <strong>Model::distinct()</strong> is incorrect." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		} 
	} 
	
	final protected function __sum( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
			{
				$f = current($args); 
				$selectSql = "SELECT SUM({$this->_tildeControl}{$this->_propModel}{$this->_tildeControl}.{$this->_tildeControl}{$f}{$this->_tildeControl}) AS {$this->_tildeControl}sum{$this->_tildeControl} "; 
				$fromSql = $this->__buildSqlFrom(); 
				$condSql = $this->__buildSqlCondition(); 
				$groupSql = $this->__buildSqlGroup(); 
				$orderSql = $this->__buildSqlOrder(); 
				$rangeSql = $this->__buildSqlRange(); 
				
				$qr = $this->__query( $selectSql . $fromSql . $condSql . $groupSql . $orderSql . $rangeSql ); 
				$rs = $this->fetch_assoc($qr); 
				if(is_array($rs)) 
				{
					return (int)$rs['sum']; 
				}
				return $rs;
			} 
			else 
				throw new Exception( "Usage <strong>Model::sum()</strong> is incorrect." ); 
		}
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	} 
	
	final protected function __avg( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
			{ 
				$f = current($args); 
				$selectSql = "SELECT AVG({$this->_tildeControl}{$this->_propModel}{$this->_tildeControl}.{$this->_tildeControl}{$f}{$this->_tildeControl}) AS {$this->_tildeControl}avg{$this->_tildeControl} "; 
				$fromSql = $this->__buildSqlFrom(); 
				$condSql = $this->__buildSqlCondition(); 
				$groupSql = $this->__buildSqlGroup(); 
				$orderSql = $this->__buildSqlOrder(); 
				$rangeSql = $this->__buildSqlRange(); 
				
				$qr = $this->__query( $selectSql . $fromSql . $condSql . $groupSql . $orderSql . $rangeSql ); 
				$rs = $this->fetch_assoc($qr); 
				if(is_array($rs)) 
				{
					return (int)$rs['avg']; 
				}
				return $rs;
			} 
			else 
				throw new Exception( "Usage <strong>Model::avg()</strong> is incorrect." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	} 
	
	final protected function __max( $args, $argsNum ) 
	{
		try 
		{ 
			if( $argsNum ) 
			{
				$f = current($args); 
				$selectSql = "SELECT MAX({$this->_tildeControl}{$this->_propModel}{$this->_tildeControl}.{$this->_tildeControl}{$f}{$this->_tildeControl}) AS {$this->_tildeControl}max{$this->_tildeControl} "; 
				$fromSql = $this->__buildSqlFrom(); 
				$condSql = $this->__buildSqlCondition(); 
				$groupSql = $this->__buildSqlGroup(); 
				$orderSql = $this->__buildSqlOrder(); 
				$rangeSql = $this->__buildSqlRange(); 
				
				$qr = $this->__query( $selectSql . $fromSql . $condSql . $groupSql . $orderSql . $rangeSql ); 
				$rs = $this->fetch_assoc($qr); 
				if(is_array($rs)) 
				{
					return (int)$rs['max']; 
				}
				return $rs;
			} 
			else 
				throw new Exception( "Usage <strong>Model::max()</strong> is incorrect." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	} 
	
	final protected function __min( $args, $argsNum ) 
	{ 
		try 
		{ 
			if( $argsNum ) 
			{ 
				$f = current($args); 
				$selectSql = "SELECT MIN({$this->_tildeControl}{$this->_propModel}{$this->_tildeControl}.{$this->_tildeControl}{$f}{$this->_tildeControl}) AS {$this->_tildeControl}min{$this->_tildeControl} "; 
				$fromSql = $this->__buildSqlFrom(); 
				$condSql = $this->__buildSqlCondition(); 
				$groupSql = $this->__buildSqlGroup(); 
				$orderSql = $this->__buildSqlOrder(); 
				$rangeSql = $this->__buildSqlRange(); 
				
				$qr = $this->__query( $selectSql . $fromSql . $condSql . $groupSql . $orderSql . $rangeSql ); 
				$rs = $this->fetch_assoc($qr); 
				if(is_array($rs)) 
				{
					return (int)$rs['min']; 
				}
				return $rs;
			} 
			else 
				throw new Exception( "Usage <strong>Model::min()</strong> is incorrect." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	} 
	
	final protected function __implode( $args, $argsNum ) 
	{ 
		try 
		{ 
			if( $argsNum ) 
			{
				return NULL;
			} 
			else 
				throw new Exception( "Usage <strong>Model::implode()</strong> is incorrect." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		} 
	} 
	
	final protected function __length( $args, $argsNum ) 
	{
		try 
		{
			return call_user_func_array(array($this, '__total'), array($args, $argsNum)); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	} 
	
	protected function __dbList( $args, $argsNum ) 
	{
		try 
		{
			if( !$argsNum ) 
			{
				global $_CONFIG;
				$result = $this->query( 'SELECT table_name as '.$this->_tildeControl.'table_name'.$this->_tildeControl.' FROM information_schema.tables where table_schema="' . $_CONFIG['DATASOURCE'][$_CONFIG['DATASOURCE']['server']['default']]['database'] . '"' );
				$tables = array();
				foreach( $result as $table ) 
					$tables[] = $table['tables']['table_name'];
				return $tables;
			}
			else 
				throw new Exception( "Usage <strong>Model::dbList()</strong> is incorrect." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	}
	
	protected function errno() { return mysqli_errno( $this->_dbHandle ); } 
	protected function error() { return mysqli_error( $this->_dbHandle ); } 
	protected function insert_id() { return mysqli_insert_id( $this->_dbHandle ); } 
	protected function escape_string( $str ) { return mysqli_real_escape_string( $this->_dbHandle, trim($str) ); } 
	protected function affected_rows( $rs=NULL ) { return ($this->_dbHandle)?mysqli_affected_rows( $this->_dbHandle ):0; } 
	protected function num_rows( $rs ) { return ($rs)?mysqli_num_rows( $rs ):0; } 
	protected function fetch_assoc( $rs ) { return ($rs)?mysqli_fetch_assoc( $rs ):$rs; } 
	protected function fetch_row( $rs ) { return ($rs)?mysqli_fetch_row( $rs ):$rs; } 
	protected function free_result( $rs ) { return ($rs)?mysqli_free_result( $rs ):$rs; } 
	
	protected function fetch_field( $rs, &$ts, &$fs ) 
	{
		$ts = array(); 
		$fs = array(); 
		while($f=mysqli_fetch_field($rs)) 
		{
			$ts[] = ($f->table)?$f->table:$this->_propModel;
			$fs[] = $f->name;
		} 
		return count($fs);
	}
	
	protected function __query( $sql ) 
	{
		$sql = trim($sql); 
		$result = mysqli_query( $this->_dbHandle, $sql ); 
		$this->__logsql( $sql ); 
		return $result;
	} 
	
	protected function __handled( $dsl, $src, $less=false ) 
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
				if( isset($server['resource']) ) 
				{
					$dsl = $server['resource']; 
					if( mysqli_select_db($dsl, $_CONFIG['DATASOURCE'][$src]['database']) ) 
					{ 
						mysqli_query( $dsl, 'SET CHARSET utf8' ); 
						$this->__handled( $dsl, $src ); 
					} 
					else 
					{
						if( config::get(DEVELOPMENT_ENVIRONMENT) ) 
						{
							throw new Exception( "<b>MESSAGE:</b> The connected resource link is missed or Database <b>'".$_CONFIG['DATASOURCE'][$src]['database']."'</b> does not exist." ); 
						}
					} 
				} 
				else 
				{
					$hostname = $server['hostname'].colon.$server['port'];
					$username = $server['username'];
					$password = $server['password'];
					$dsl = mysqli_connect($hostname, $username, $password);
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
				return $this;
			} 
		}
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() );
		}
		return $this;
	}  
	
	final protected function __duplicate() 
	{
		$out = deep_copy($this); 
		return $out->__reset(); 
	}
}