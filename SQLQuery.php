<?php

namespace Zuuda;

abstract class SQLQuery 
{
	protected $_dbHandle;
	protected $_result;
	protected $_querySQL;
	protected $_table;
	protected $_describe = array(); 
	protected $_unDescrible = array();
	protected $_order_by;
	protected $_order;
	protected $_extraConditions;
	protected $_collection;
	protected $_hO;
	protected $_hM;
	protected $_hMABTM;
	protected $_hasOneBlink = array();
	protected $_hasManyBlink = array();
	protected $_hasManyAndBelongsToManyBlink = array();
	protected $_page;
	protected $_limit;
	protected $_imerge;
	protected $_ibind;
	protected $_prefix;
	
	protected function _getDBHandle() { return $this->_dbHandle; }
	protected function _getResult() { return $this->_result; }
	protected function _getQuerySQL() { return $this->_querySQL; }
	protected function _getModel() { return ( isset( $this->_model ) ) ? $this->_model : NULL; }
	protected function _getModelName() { return $this->_getModel(); }
	protected function _getTable() { return ( isset( $this->_table ) ) ? $this->_table : NULL; }
	protected function _getTableName() { return $this->_getTable(); }
	protected function _getDescribe() { return $this->_describe; }
	protected function _getOrderBy() { return $this->_order_by; }
	protected function _getOrder() { return $this->_order; }
	protected function _getExtraConditions() { return $this->_extraConditions; }
	protected function _getCollection() { return $this->_collection; }
	protected function _getHasOne() { return ( isset( $this->_hasOne ) ) ? $this->_hasOne : NULL; }
	protected function _getHasMany() { return ( isset( $this->_hasMany ) ) ? $this->_hasMany : NULL; }
	protected function _getHasManyAndBelongsToMany() { return ( isset( $this->_hasManyAndBelongsToMany ) ) ? $this->_hasManyAndBelongsToMany : NULL; }
	protected function _getPage() { return $this->_page; }
	protected function _getLimit() { return $this->_limit; }
	protected function _getPrefix() { return $this->_prefix; }
	
	protected function _setDBHandle( $value ) { $this->_dbHandle = $value; return $this; }
	protected function _setResult( $value ) { $this->_result = $value; return $this; }
	protected function _setQuery( $value ) { $this->_querySQL = $value; return $this; }
	protected function _setTable( $value ) { return $this->_setTableName( $value ); }
	protected function _setModel( $value ) { return $this->_setModelName( $value ); }
	protected function _setOrderBy( $value ) { $this->_order_by = $value; return $this; }
	protected function _setOrder( $value ) { $this->_order = $value; return $this; }
	protected function _setExtraConditions( $value ) { $this->_extraConditions = $value; return $this; }
	protected function _setCollection( $value ) { $this->_collection = $value; return $this; }

	protected function _setHasOne( $value ) { return $this->_orderHasOne( $value ); }
	protected function _addHasOne( $value ) { return $this->_orderHasOne( $value ); }
	protected function _setHasMany( $value ) { return $this->_orderHasMany( $value ); }
	protected function _addHasMany( $value ) { return $this->_orderHasMany( $value ); }
	protected function _setHasManyAndBelongsToMany( $value ) { return $this->_orderHMABTM( $value ); }
	protected function _addHasManyAndBelongsToMany( $value ) { return $this->_orderHMABTM( $value ); }

	protected function _setPage( $value ) { $this->_page = $value; return $this; }
	/** private function _setLimit */
	protected function _setPrefix( $value ) { $this->_prefix = $value; return $this; }
	
	/** Connects to database **/
	public function Connect( $address, $account, $pwd, $name ) { return $this->_connect( $address, $account, $pwd, $name ); }
	public function Query( $query = NULL ) { return $this->_query( $query ); }
	public function GetQuery() { return $this->_getQuerySQL(); }
	public function GetQuerySQL() { return $this->_getQuerySQL(); }
	public function GetModel() { return $this->_getModel(); }
	public function GetModelName() { return $this->_getModel(); }
	public function GetTable() { return $this->_getTable(); }
	public function GetTableName() { return $this->_getTable(); }
	public function Parse( $result ) { return $this->_parse( $result ); }
	public function Item( $result, $index = NULL ) { return $this->_item( $result, $index ); }
	public function Select( $field, $label = NULL ) { return $this->_select( $field, $label = NULL ); }
	public function UnSelect( $fields ) { return $this->_unSelect( $fields ); }
	public function GetCollectionString() { return $this->_getCollectionString(); }
	public function Between( $field, $start, $end ) { return $this->_between( $field, $start, $end ); }
	public function Equal( $field, $value ) { return $this->_equal( $field, $value ); }
	public function Greater( $field, $value ) { return $this->_greater( $field, $value ); } 
	public function GreaterThanOrEqual( $field, $value ) { return $this->_greaterThanOrEqual( $field, $value ); } 
	public function In( $field, $values ) { return $this->_in( $field, $values ); }
	public function Is( $field, $value ) { return $this->_is( $field, $value ); }
	public function IsNot( $field, $value ) { return $this->_isNot( $field, $value ); }
	public function IsNull( $field ) { return $this->_isNull( $field ); }
	public function Less( $field, $value ) { return $this->_less( $field, $value ); } 
	public function LessThanOrEqual( $field, $value ) { return $this->_lessThanOrEqual( $field, $value ); } 
	public function Like( $field, $value ) { return $this->_like( $field, $value ); }
	public function Not( $field, $value ) { return $this->_not( $field, $value ); }
	public function NotEqual( $field, $value ) { return $this->_notEqual( $field, $value ); }
	public function NotIn( $field, $values ) { return $this->_notIn( $field, $values ); }
	public function NotLike( $field, $value ) { return $this->_notLike( $field, $value ); }
	public function Where( $field, $value, $operaion='=' ) { return $this->_where( $field, $value, $operaion ); }
	public function ShowHasOne() { return $this->_showHasOne(); }
	public function ShowHasMany() { return $this->_showHasMany(); }
	public function ShowHMABTM() { return $this->_showHMABTM(); }
	public function HideHasOne() { return $this->_hideHasOne(); }
	public function HideHasMany() { return $this->_hideHasMany(); }
	public function HideHMABTM() { return $this->_hideHMABTM(); }
	public function ClearHasOne() { return $this->_clearHasOne(); }
	public function ClearHasMany() { return $this->_clearHasMany(); }
	public function ClearHMABTM() { return $this->_clearHMABTM(); } 
	public function ConvertHasOne( $data ) { return $this->_convertHasOne( $data ); } 
	public function ConvertHasMany( $data ) { return $this->_convertHasMany( $data ); } 
	public function ConvertHMABTM( $data ) { return $this->_convertHMABTM( $data ); } 
	public function BlinkHasOne( $data ) { return $this->_blinkHasOne( $data ); } 
	public function BlinkHasMany( $data ) { return $this->_blinkHasMany( $data ); } 
	public function BlinkHMABTM( $data ) { return $this->_blinkHMABTM( $data ); } 
	public function UnBlinkHasOne( $data ) { return $this->_unBlinkHasOne( $data ); } 
	public function UnBlinkHasMany( $data ) { return $this->_unBlinkHasMany( $data ); } 
	public function UnBlinkHMABTM( $data ) { return $this->_unBlinkHMABTM( $data ); } 
	public function SetLimit( $value ) { return $this->_setLimit( $value ); }
	public function Limit( $value ) { return $this->_setLimit( $value ); }
	public function SetPage( $value ) { return $this->_setPage( $value ); }
	public function Page( $value ) { return $this->_setPage( $value ); }
	public function OrderBy( $order_by, $order = 'ASC' ) { return $this->_orderBy( $order_by, $order ); }
	public function Order( $order_by, $order = 'ASC' ) { return $this->_orderBy( $order_by, $order ); }
	public function Load() { return $this->_search(); }
	public function Search() { return $this->_search(); }
	public function Custom( $query ) { return $this->_custom( $query ); }
	public function Delete( $id=NULL ) { return $this->_delete( $id ); }
	public function Save( $data=NULL ) { return $this->_save( $data ); }
	public function Clear( $deep=false ) { return $this->_clear( $deep ); }
	public function TotalPages() { return $this->_totalPages(); } 
	public function Total() { return $this->_total(); } 
	public function Length() { return $this->_length(); }
	public function DBList() { return $this->_dbList(); }
	public function SetIncreament( $value ) { $this->_setIncreament( $value ); }
	public function GetId() { return $this->_getId(); }
	public function SetId( $id ) { return $this->_setId( $id ); } 
	public function MaxId( $label = NULL ) { return $this->_maxId( $label ); }
	public function GetMaxId( $label = NULL ) { return $this->_maxId( $label ); }
	public function GetData( $id = NULL ) { return $this->_getData( $id ); }
	public function GetLastedData() { return $this->_getLastedData(); }
	public function GetError() { return $this->_getError(); } 
	public function SetData( $data, $value = NULL ) { return $this->_setData( $data, $value ); }
	public function SetPrefix( $value ) { return $this->_setPrefix( $value ); }
	public function SetTableName( $value ) { return $this->_setTableName( $value ); }
	public function SetModelName( $value ) { return $this->_setModelName( $value ); }
	public function SetHasOne( $value ) { return $this->_setHasOne( $value ); }
	public function SetHasMany( $value ) { return $this->_setHasMany( $value ); }
	public function SetHasManyAndBelongsToMany( $value ) { return $this->_setHasManyAndBelongsToMany( $value ); }
	public function AddHasOne( $value ) { return $this->_addHasOne( $value ); }
	public function AddHasMany( $value ) { return $this->_addHasMany( $value ); }
	public function AddHasManyAndBelongsToMany( $value ) { return $this->_addHasManyAndBelongsToMany( $value ); }
	public function Merge( $value ) { return $this->_merge( $value ); }
	public function Bind( $value ) { return $this->_bind( $value ); } 
	public function Find( $id ) { return $this->_find( $id ); } 
	
	abstract protected function _startConn();
	abstract protected function setTable();
	private function _connect( $address, $account, $pwd, $name ) 
	{
		$hl = mysqli_connect($address, $account, $pwd);
		if ( $hl !== false ) 
		{
			if ( mysqli_select_db( $hl, $name ) ) 
			{
				mysqli_query( $hl, 'SET CHARSET utf8' );
				
				$this->_setDBHandle( $hl );
				
				return 1;
			}
			else 
			{
				return 0;
			}
		}
		else 
		{
			return 0;
		}
		
		try 
		{
			var_dump( $hl );
		}
		catch (Exception $e) 
		{
			echo $e->message();
		}
	}
	
	private function _query( $query = NULL ) 
	{
		$result = array();
		
		if( is_null( $query ) ) 
		{
			$result = $this->search();
		} 
		else 
		{
			$result = $this->custom( $query );
		} 
		
		return $result;
	} 
	
	private function _parse( $result ) 
	{
		$list_result = array();
		foreach( $result as $key => $value ) 
		{
			array_push( $list_result, $this->item( $value ) );
		}
		return $list_result;
	}
	
	private function _item( $result, $index = NULL ) 
	{
		global $inflect;
		
		$model = $inflect->singularize( (string) $this->_model );
		
		if( !is_null( $index ) ) 
		{
			return ( isset( $result[ $index ][ $model ] ) ) ? $result[ $index ][ $model ] : $result[ $index ][ $this->_model ];
		}
		else 
		{
			if( isset( $result[ 0 ] ) ) 
			{
				return ( isset( $result[ $model ] ) ) ? $result[ 0 ][ $model ] : $result[ 0 ][ $this->_model ];
			} 
			return ( isset( $result[ $model ] ) ) ? $result[ $model ] : ( isset( $result[ $this->_model ] ) ) ? $result[ $this->_model ] : NULL;
		}
	}

	/** Select Query **/
	
	private function _select( $fields, $label = NULL ) 
	{
		if( is_array( $fields ) ) 
		{
			foreach( $fields as $key => $value ) 
			{
				if( is_string( $key ) ) 
				{
					$this->_select( $value, $key );
				} 
				$this->_select( $value );
			} 

			return $this;
		}
		$this->_collection .=  ',`' . $this->_model . '`.`' . $fields . '`';
		if( !is_null( $label ) )
		{
			 $this->_collection .= ' AS \'' . $label . '\'';
		}
		return $this;
	} 

	private function _unSelect( $fields ) 
	{
		if( is_array( $fields ) ) 
		{
			foreach( $fields as $key ) 
			{
				$this->_unSelect( $key );
			}
			return $this;
		} 

		$this->_unDescrible = array_merge( $this->_unDescrible, (array) $fields ); 
		return $this;
	}
	
	private function _getCollectionString() 
	{
		if( is_null( $this->_collection ) )
		{
			$describe = $this->_describe; 

			if( isset( $this->_unDescrible ) ) 
			{
				foreach( $describe as $key => $value ) 
				{
					if( in_array( $value, $this->_unDescrible ) ) 
					{
						unset( $describe[ $key ] );
					}
				}
				return '`'. implode( '`, `', $describe ) .'`';
			} 

			return '*';
		}
		return substr( $this->_collection, 1 ); 
	}

	private function _between( $field, $start_value, $end_value ) 
	{
		$this->_extraConditions .= 'BETWEEN `'.$this->_model.'`.`'.$field.'` >= \''.mysqli_real_escape_string( $this->_dbHandle, $start_value ).'\' AND `'.$this->_model.'`.`'.$field.'` <= \''.mysqli_real_escape_string( $this->_dbHandle, $end_value ).'\' AND ';
		return $this;
	} 

	private function _equal( $field, $value ) 
	{
		return $this->_where( $field, $value );
	} 

	private function _greater( $field, $value ) 
	{
		return $this->_where( $field, $value, '>' );
	} 

	private function _greaterThanOrEqual( $field, $value ) 
	{
		return $this->_where( $field, $value, '>=' );
	} 

	private function _in( $field, $values ) 
	{
		return $this->_where( $field, implode( ', ', $values ), 'IN' );
	}

	private function _is( $field, $value ) 
	{
		return $this->_where( $field, $value, 'IS' ); 
	} 

	public function _isNot( $field, $value ) 
	{
		return $this->_where( $field, $value, 'IS NOT' );
	} 

	public function _isNull( $field ) 
	{
		return $this->_where( $field, 'NULL', 'IS' );
	} 

	public function _less( $field, $value ) 
	{
		return $this->_where( $field, $value, '<' );
	} 

	public function _lessThanOrEqual( $field, $value ) 
	{
		return $this->_where( $field, $value, '<=' );
	} 

	private function _like( $field, $value ) 
	{
		return $this->where( $field, $value, 'LIKE' );
	}

	public function _not( $field, $value ) 
	{
		return $this->_notEqual( $field, $value );
	} 

	public function _notEqual( $field, $value ) 
	{
		return $this->_where( $field, $value, '!=' );
	}

	public function _notIn( $field, $values ) 
	{
		return $this->where( $field, implode( ', ', $values ), 'NOT IN' );
	} 

	public function _notLike( $field, $value ) 
	{
		return $this->where( $field, $value, 'NOT LIKE' );
	}

	private function _where( $field, $value, $operator='=' ) 
	{
		$sql_value = mysqli_real_escape_string( $this->_dbHandle, $value );

		if( $operator === 'LIKE' || $operator === 'NOT LIKE' ) 
		{
			$sql_value = '\'' . '%' . $sql_value . '%' . '\'';
		} 
		else if( $operator === 'IN' || $operator === 'NOT IN' ) 
		{
			$sql_value = '('.$sql_value.')';
		}
		else 
		{
			$sql_value = '\'' . $sql_value . '\'';
		}
		$this->_extraConditions .= '`'.$this->_model.'`.`'.$field.'` '.$operator.' '.$sql_value.' AND ';
		return $this;
	}

	private function _showHasOne() 
	{
		$this->_hO = 1;
		return $this;
	}

	private function _showHasMany() 
	{
		$this->_hM = 1;
		return $this;
	}

	private function _showHMABTM() 
	{
		$this->_hMABTM = 1;
		return $this;
	} 

	private function _hideHasOne() 
	{
		$this->_hO = null;
		return $this;
	} 

	private function _hideHasMany() 
	{
		$this->_hM = null;
		return $this;
	}

	private function _hideHMABTM() 
	{
		$this->_hMABTM = null;
		return $this;
	} 

	private function _clearHasOne() 
	{
		$this->_hasOne = NULL;
		return $this;
	} 

	private function _clearHasMany() 
	{
		$this->_hasMany = NULL; 
		return $this;
	} 

	private function _clearHMABTM() 
	{
		$this->_hasManyAndBelongsToMany = NULL;
		return $this;
	} 

	private function _blinkHasOne( $blink_data ) 
	{
		return $this->_blinkRelative( 
			$this->_hasManyBlink, 
			$blink_data 
		);
	} 

	private function _blinkHasMany( $blink_data ) 
	{
		return $this->_blinkRelative( 
			$this->_hasManyBlink, 
			$blink_data 
		);
	} 

	private function _blinkHMABTM( $blink_data ) 
	{
		return $this->_blinkRelative( 
			$this->_hasManyAndBelongsToManyBlink, 
			$blink_data 
		);
	} 

	private function _blinkRelative( &$data_current, $blink_data ) 
	{
		if( is_array( $blink_data ) ) 
		{
			$data_current = array_merge( $data_current, $blink_data );
		} 
		else 
		{
			array_push( $data_current, $blink_data ); 
		} 
		return $this;
	} 

	private function _unBlinkHasOne( $unblink_data ) 
	{
		return $this->_unBlinkRelative(
			$this->_hasOneBlink, 
			$unblink_data 
		);
	} 

	private function _unBlinkHasMany( $unblink_data ) 
	{
		return $this->_unBlinkRelative(
			$this->_hasManyBlink, 
			$unblink_data 
		); 
	}

	private function _unBlinkHMABTM( $unblink_data ) 
	{
		return $this->_unBlinkRelative( 
			$this->_hasManyAndBelongsToManyBlink, 
			$unblink_data 
		);
	}

	private function _unBlinkRelative( &$data_current, $unblink_data ) 
	{
		if( is_array( $unblink_data ) ) 
		{
			foreach( $unblink_data as $key => $value ) 
			{
				if( !in_array( $value, $data_current ) ) 
				{
					continue;
				} 
				$this->_unBlinkRelative( $data_current, $value );
			}
		} 
		else 
		{
			foreach( $data_current as $key => $value ) 
			{
				if( $unblink_data != $data_current[ $key ] ) 
				{
					continue;
				}
				unset( $data_current[ $key ] );
			}
		} 
		return $this;
	}

	private function _convertHasOne( $convert_data ) 
	{
		return $this->_convertRelative( 
			$this->_hasOne, 
			$convert_data 
		);
	}

	private function _convertHasMany( $convert_data ) 
	{
		return $this->_convertRelative( 
			$this->_hasMany, 
			$convert_data 
		); 
	} 

	private function _convertHMABTM( $convert_data ) 
	{
		return $this->_convertRelative(
			$this->_hasManyAndBelongsToMany, 
			$convert_data 
		); 
	} 

	private function _convertRelative( &$data_current, $convert_data ) 
	{
		foreach( $data_current as $key => $value ) 
		{
			if( array_key_exists( $key, $convert_data ) ) 
			{
				$tmp = $data_current[ $key ];
				$data_current[ $convert_data[ $key ] ] = $tmp; 
				unset( $data_current[ $key ] );
			}
		} 
		return $this;
	}

	protected function _orderHasOne( $hasOne ) 
	{
		foreach( $hasOne as $alias_child => $table_child ) 
		{
			if( is_array($table_child) ) 
			{
				list( $table, $key ) = each( $table_child );
				$this->_hasOne[ $alias_child ] = array( 
					'key'	=> $key, 
					'table'	=> $table 
				);
			} 
			else 
			{
				$this->_hasOne[ $alias_child ] = $table_child;
			}
		} 
		return $this;
	}

	protected function _orderHasMany( $hasMany ) 
	{
		foreach( $hasMany as $alias_child => $table_child ) 
		{
			if( is_array($table_child) ) 
			{
				list( $table, $key ) = each( $table_child );
				$this->_hasMany[ $alias_child ] = array( 
					'key'	=> $key, 
					'table'	=> $table 
				);
			} 
			else 
			{
				$this->_hasMany[ $alias_child ] = $table_child;
			}
		} 
		return $this;
	} 

	protected function _orderHMABTM( $hasMABTM ) 
	{
		foreach( $hasMABTM as $alias_child => $table_child ) 
		{
			if( is_array( $table_child ) ) 
			{
				list( $table, $key ) = each( $table_child[0] );
				$table_child['data'] = array( 
					'key'	=> $key, 
					'table' => $this->_tableSort( $table )  
				);
				unset($table_child[0]);

				list( $table, $key ) = each( $table_child[1] );
				$table_child['join'] = array( 
					'key'	=> $key, 
					'table' => $this->_tableSort( $table )  
				);
				unset($table_child[1]);

				$this->_hasManyAndBelongsToMany[ $alias_child ] = $table_child;
			} 
			else 
			{
				$this->_hasManyAndBelongsToMany[ $alias_child ] = $table_child;
			}
		}
		return $this;
	}

	private function _setLimit( $limit ) 
	{
		$this->_limit = $limit;
		
		if( is_null( $this->_page ) ) 
		{
			$this->_page = 1;
		}
		
		return $this;
	}

	private function _orderBy( $order_by, $order = 'ASC' ) 
	{
		$this->_setOrderBy( $order_by )->_setOrder( $order );
		return $this;
	} 

	/** HOW TO USE MERGE(models) FUNCTION
	 * $this->model->merge([
	 *	'Avatar' 	=> array('media.avatar_id.id', array('id'=>'= 2', 'name'=>'like \'%funny/%\'')), 
	 *	'UserStat' 	=> array('user_stat.id.user_id'),
	 * ])->search();
	 */
	private function _merge( $models )
	{
		global $inflect;

		$prefix = $this->_retrivePrefix();

		$this->_imerge = NL;
		foreach( $models as $alias => $model ) 
		{
			$key = $model[ 0 ];
			$key = explode( '.', $key );
			
			$alias_key = $key[ 2 ];
			$foreign_key = $key[ 1 ];
			$alias_merge = explode( '_', $key[ 0 ] );
			sort( $alias_merge );
			foreach( $alias_merge as $key => $value ) 
			{
				$alias_merge[ $key ] = strtolower($inflect->pluralize($value));
			}

			$table_merge = $prefix . implode( '_', $alias_merge );
			
			$condition = '';
			if( isset($model[ 1 ]) ) 
			{
				foreach( $model[ 1 ] as $key => $value ) 
				{
					$condition .= "AND `" . $alias . "`.`" . $key . "` " . $value . ' '; 
				}
			}

			$this->_imerge .= "INNER JOIN `" . $table_merge . "` AS `" . $alias . "` ON `" . $alias . "`.`" . $alias_key . "` = `" . $this->_model . "`.`" . $foreign_key . "` " . $condition . NL;
		}
		return $this;
	}

	private function _bind( $models ) 
	{
		global $inflect;

		$prefix = $this->_retrivePrefix();

		$this->_ibind = NL;

		$this->_ibind -= "INNER JOIN `Table1` AS Alias ON Alias.`id` = Model.`id`";

		return $this;
	} 

	private function buildMainQuery( $prefix ) 
	{
		global $inflect;

		$from = '`'.$this->_table.'` as `'.$this->_model.'` ';
		$conditions = '\'1\'=\'1\' AND ';
		
		if( $this->_hO == 1 && isset( $this->_hasOne ) ) 
		{
			foreach ( $this->_hasOne as $modelChild => $tableChild ) 
			{
				if( in_array( $modelChild, $this->_hasOneBlink ) ) 
				{
					continue;
				} 

				$aliasKey = '';

				if(is_array( $tableChild )) 
				{
					$aliasKey = $tableChild[ 'key' ];
					$tableChild = $tableChild[ 'table' ];
				}
				
				$this->_tableSort($tableChild);

				if( $aliasKey=='' ) 
					$aliasKey = $tableChild . '_id';

				$tableChild = $prefix . $inflect->pluralize( $tableChild );

				$from .= 'LEFT JOIN `'.$tableChild.'` as `'.$modelChild.'` ';
				$from .= 'ON `'.$this->_model.'`.`'.$aliasKey.'` = `'.$modelChild.'`.`id`  ';
			}
		}

		if( isset( $this->id ) ) 
		{
			$conditions .= '`'.$this->_model.'`.`id` = \''.mysqli_real_escape_string( $this->_dbHandle, $this->id ).'\' AND ';
		}

		if( $this->_extraConditions ) 
		{
			$conditions .= $this->_extraConditions;
		}

		$conditions = substr( $conditions, 0, -4 );
		
		if( isset( $this->_order_by ) ) 
		{
			$conditions .= ' ORDER BY `'.$this->_model.'`.`'.$this->_order_by.'` '.$this->_order;
		}

		if ( isset( $this->_page ) ) 
		{
			$offset = ( $this->_page-1 ) * $this->_limit;
			$conditions .= ' LIMIT '.$this->_limit.' OFFSET '.$offset;
		}
		$this->_querySQL = 'SELECT ' . $this->getCollectionString() . ' FROM ' . $from . $this->_imerge . 'WHERE ' . $conditions;
	} 

	private function _find( $id ) 
	{
		return $this->_where( 'id', $id )->_search();
	}

	private function _search() 
	{
		global $inflect;
		$conditionsChild = '';
		$fromChild = '';
		$prefix = $this->_retrivePrefix();

		$this->buildMainQuery( $prefix );
		
		$this->_result = mysqli_query( $this->_dbHandle, $this->_querySQL );
		$result = array();
		$table = array();
		$field = array();
		$tempResults = array();
		
		if( $this->_result ) 
		{
			if( mysqli_num_rows( $this->_result ) > 0 ) 
			{
				$numOfFields = mysqli_num_fields( $this->_result );

				while( $field_info = mysqli_fetch_field( $this->_result ))
				{
					array_push( $table, $field_info->table );
				    array_push( $field, $field_info->name );
				}

				while( $row = mysqli_fetch_row( $this->_result ) ) 
				{
					for( $i = 0; $i < $numOfFields; ++$i ) 
					{
						$tempResults[ $table[ $i ] ][ $field [ $i ] ] = $row[ $i ];
					}

					if( $this->_hM == 1 && isset( $this->_hasMany ) ) 
					{
						foreach ( $this->_hasMany as $modelChild => $aliasChild ) 
						{
							if( in_array( $modelChild, $this->_hasManyBlink ) ) 
							{
								continue; 
							}

							$queryChild = '';
							$conditionsChild = '';
							$fromChild = '';
							$aliasKey = '';

							if( is_array($aliasChild) ) 
							{
								$aliasKey = $aliasChild[ 'key' ];
								$aliasChild = $aliasChild[ 'table' ];
							} 
							else 
							{
								$aliasKey = $this->_alias . '_id';
							}
							
							$aliasChild = explode( '_', $aliasChild );
							sort($aliasChild);
							$modelAlias = array();
							foreach( $aliasChild as $key => $value )  
							{
								array_push( $modelAlias, ucfirst(strtolower($inflect->singularize($value))) );
								$aliasChild[ $key ] = strtolower($inflect->singularize($value));
							}

							$aliasChild = implode( '_', $aliasChild );

							$modelAlias = implode( '', $modelAlias );

							$tableChild = $prefix . strtolower($inflect->pluralize($aliasChild));

							$fromChild .= '`'.$tableChild.'` as `'.$modelAlias.'`';
							$conditionsChild .= '`'.$modelAlias.'`.`'.$aliasKey.'` = \''.$tempResults[$this->_model][ID].'\'';

							$queryChild =  'SELECT * FROM '.$fromChild.' WHERE '.$conditionsChild;	
							$resultChild = mysqli_query( $this->_dbHandle, $queryChild );
							$tableChild = array();
							$fieldChild = array();
							$temp_results_child = array();
							$results_child = array();

							if( $resultChild ) 
							{
								if( mysqli_num_rows($resultChild) > 0 ) 
								{
									$numOfFieldsChild = mysqli_num_fields( $resultChild );

									while( $field_info = mysqli_fetch_field($resultChild) ) 
									{
										array_push( $tableChild, $field_info->table );
										array_push( $fieldChild, $field_info->name );
									}

									while( $rowChild = mysqli_fetch_row($resultChild) ) 
									{
										for ($j = 0;$j < $numOfFieldsChild; ++$j) 
										{
											$temp_results_child[$tableChild[$j]][$fieldChild[$j]] = $rowChild[$j];
										}
										array_push( $results_child, $temp_results_child );
									}
								}
								
								$tempResults[ $modelChild ] = $results_child;
								
								mysqli_free_result($resultChild);
							}
						}
					}

					if ($this->_hMABTM == 1 && isset($this->_hasManyAndBelongsToMany)) 
					{
						foreach ($this->_hasManyAndBelongsToMany as $modelChild => $aliasChild) 
						{
							$queryChild = '';
							$conditionsChild = '';
							$fromChild = '';

							$cacheChild = $aliasChild;

							if( is_array( $cacheChild ) ) 
							{
								$singularAliasChild = $cacheChild['data']['key'];
								$tableModel = $this->_modelSort( $cacheChild['data']['table'] );
								$tableChild = $prefix . $inflect->pluralize( $cacheChild['data']['table'] );
								$joinTable = $prefix . $inflect->pluralize( $cacheChild['join']['table'] );
								$joinAlias = $this->_modelSort( $cacheChild['join']['table'] );
								$aliasKey = $cacheChild['join']['key'];
							} 
							else 
							{
								$singularAliasChild = strtolower($inflect->singularize($aliasChild)).'_id';
								$pluralAliasTable = strtolower($inflect->pluralize($this->_alias));
								$pluralAliasChild = strtolower($inflect->pluralize($aliasChild));
								$sortTables = array( $pluralAliasTable ,$pluralAliasChild );
								sort($sortTables);
								$joinTable = $prefix . implode('_',$sortTables);
								$tableModel = $this->_modelSort( $aliasChild );
								$tableChild = $prefix . strtolower($inflect->pluralize($aliasChild));
								$sortAliases = array( $this->_model, $tableModel );
								sort($sortAliases);
								$joinAlias = implode('', $sortAliases);
								$aliasKey = $this->_alias.'_id';
							}
							
							$fromChild .= '`'.$tableChild.'` as `'.$tableModel.'`,';
							$fromChild .= '`'.$joinTable.'` as `'.$joinAlias.'`,';
							$conditionsChild .= '`'.$joinAlias.'`.`'.$singularAliasChild.'` = `'.$tableModel.'`.`id` AND ';
							$conditionsChild .= '`'.$joinAlias.'`.`'.$aliasKey.'` = \''.$tempResults[$this->_model]['id'].'\'';
							
							$fromChild = substr($fromChild,0,-1);
							$queryChild =  'SELECT * FROM '.$fromChild.' WHERE '.$conditionsChild;	
							$resultChild = mysqli_query( $this->_dbHandle, $queryChild );

							$tableChild = array();
							$fieldChild = array();
							$temp_results_child = array();
							$results_child = array();
							
							if( $resultChild ) 
							{
								if ( mysqli_num_rows( $resultChild ) > 0 ) 
								{
									$numOfFieldsChild = mysqli_num_fields( $resultChild );

									while( $field_info = mysqli_fetch_field($resultChild) ) 
									{
										array_push( $tableChild, $field_info->table );
										array_push( $fieldChild, $field_info->name );
									}

									while ( $rowChild = mysqli_fetch_row( $resultChild ) ) 
									{
										for ( $j = 0;$j < $numOfFieldsChild; ++$j ) 
										{
											$temp_results_child[$tableChild[$j]][$fieldChild[$j]] = $rowChild[$j];
										}
										array_push( $results_child,$temp_results_child );
									}
								}
								
								$tempResults[ $modelChild ] = $results_child;

								mysqli_free_result( $resultChild );
							}
						}
					}
					array_push( $result,$tempResults );
				}

				if ( mysqli_num_rows( $this->_result ) == 1 && $this->id != null ) 
				{
					$result = $result[0];
				}
			} 
			
			mysqli_free_result( $this->_result );
		}

		$this->clear();
		
		return $result;
	}

	/** Custom SQL Query **/ 

	private function _custom( $query ) 
	{
		global $inflect;

		$this->_result = mysqli_query( $this->_dbHandle, $query );

		$result = array();
		$table = array();
		$field = array();
		$tempResults = array();

		if(substr_count(strtoupper($query),"SELECT")>0) 
		{
			if( $this->_result ) 
			{
				if( mysqli_num_rows($this->_result) > 0 ) 
				{
					$numOfFields = mysqli_num_fields($this->_result);
					
					while ($field_info = mysqli_fetch_field($this->_result)) 
					{
						array_push($table, $field_info->table);
						array_push($field, $field_info->name);
					}
					while ($row = mysqli_fetch_row($this->_result)) 
					{
						for ($i = 0;$i < $numOfFields; ++$i) {
							$table[$i] = $inflect->singularize($table[$i]);
							$tempResults[$table[$i]][$field[$i]] = $row[$i];
						}
						array_push($result,$tempResults);
					}
				}
				mysqli_free_result($this->_result);
			}
		}	
		$this->clear();
		return($result);
	}

	protected function _modelSort( $table ) 
	{
		global $inflect;
		$table = explode( '_', $table );
		foreach( $table as $key => $value ) 
		{
			$model = $inflect->singularize( strtolower( $value ) );
			$table[ $key ] = strtoupper( substr( $model, 0, 1 ) ) . substr( $model, 1 );
		}
		sort($table);
		return implode( '', $table );
	}

	protected function _tableSort( &$table ) 
	{
		global $inflect;
		$table = explode( '_', $table );
		foreach( $table as $key => $value ) 
		{
			$table[ $key ] = $inflect->singularize( strtolower($value) );
		}
		sort($table);
		$table = implode( '_', $table );
		return $table;
	}

	/** Describes a Table **/

	protected function _describe() 
	{
		global $cache;

		$this->_describe = $cache->get('describe'.$this->_table);

		if (!$this->_describe && $this->_dbHandle) 
		{
			$this->_describe = array();
			$query = 'DESCRIBE '.$this->_table;
			$this->_result = mysqli_query( $this->_dbHandle, $query );
			while ($row = @mysqli_fetch_row($this->_result)) 
			{
				 array_push($this->_describe,$row[0]);
			}

			@mysqli_free_result($this->_result);
			$cache->set('describe'.$this->_table,$this->_describe);
		}
		
		foreach ($this->_describe as $field) 
		{
			$this->$field = null;
		}
	}

	/** Delete an Object **/

	private function _delete( $id=NULL ) 
	{
		$delete_id = $this->id;

		if( NULL!=$id ) 
		{
			$delete_id = $id;
		} 

		if( $delete_id ) 
		{
			$query = 'DELETE FROM ' . $this->_table . ' WHERE `id`=\''.mysqli_real_escape_string( $this->_dbHandle, $delete_id ).'\''; 
			$this->_result = mysqli_query( $this->_dbHandle, $query );
			$this->clear(); 
			
			if ( $this->_result == 0 ) 
			{
				/** Error Generation **/
				return -1;
			} 
		} 
		else 
		{
			/** Error Generation **/
			return -1;
		}
	}

	/** Saves an Object i.e. Updates/Inserts Query **/

	private function _save( $data=NULL ) 
	{
		if( NULL!==$data ) 
		{
			$this->_setData( $data );
		}

		$fix_id = null;

		$query = '';
		if ( isset( $this->id ) ) 
		{
			$updates = '';
			foreach ( $this->_describe as $field ) 
			{
				if ( $this->$field || is_string( $this->$field ) || is_numeric( $this->$field ) ) 
				{
					$updates .= '`'.$field.'` = \''.mysqli_real_escape_string( $this->_dbHandle, $this->$field ).'\',';
				}
			}

			$updates = substr( $updates, 0, -1 );

			$query = 'UPDATE '.$this->_table.' SET '.$updates.' WHERE `id`=\''.mysqli_real_escape_string( $this->_dbHandle, $this->id ).'\''; 

			$fix_id = $this->id;
		} 
		else 
		{
			$fields = '';
			$values = '';
			foreach ($this->_describe as $field ) 
			{
				if ( $this->$field || is_string( $this->$field ) || is_numeric( $this->$field ) ) 
				{
					$fields .= '`'.$field.'`,';
					$values .= '\''.mysqli_real_escape_string( $this->_dbHandle, $this->$field ).'\',';
				}
			}
			$values = substr( $values, 0, -1 );
			$fields = substr( $fields, 0, -1 );
			$query = 'INSERT INTO '.$this->_table.' ('.$fields.') VALUES ('.$values.')'; 
		} 
		
		$this->_result = mysqli_query( $this->_dbHandle, $query );
		$this->clear();

		if ( $this->_result == 0 ) 
		{
			/** Error Generation **/
			return $this->_getError();
		} 
		else 
		{
			if( is_null( $fix_id ) ) 
			{
				return array( 'id'=> mysqli_insert_id( $this->_dbHandle ) );
			} 
			else 
			{
				return array( 'id'=> $fix_id );
			}
		}
	}

	/** Clear All Variables **/

	private function _clear( $deep=false ) 
	{
		if( $deep ) 
		{
			foreach( $this->_describe as $field ) 
			{
				$this->$field = null;
			}
			$this->_querySQL = null; 
			$this->_extraConditions = null;
			$this->_order = null;
			$this->_order_by = null;
			$this->_limit = null;
		}

		$this->_hO = null;
		$this->_hM = null;
		$this->_hMABTM = null;
		$this->_page = null;
		$this->_imerge = null;
		
		return $this;
	}

	/** Pagination Count **/

	private function _totalPages() 
	{
		if ( $this->_querySQL && $this->_limit ) 
		{
			$count = $this->_total();
			$totalPages = ceil( $count/$this->_limit );
			return $totalPages;
		} 
		else 
		{
			/* Error Generation Code Here */
			return -1;
		}
	} 

	private function _total() 
	{
		if ( $this->_querySQL ) 
		{
			$pattern = '/SELECT (.*?) FROM (.*)/i'; 

			if( $this->_limit ) 
			{
				$pattern = '/SELECT (.*?) FROM (.*)LIMIT(.*)/i';
			}

			$replacement = 'SELECT COUNT(*) AS `size` FROM $2';
			
			$countQuery = preg_replace( $pattern, $replacement, $this->_querySQL );
			$this->_result = mysqli_query( $this->_dbHandle, $countQuery ); 
			$this->_clear();

			if( $this->_result ) 
				return mysqli_fetch_assoc( $this->_result ); 
			else 
				return array( 'size'=>'0' );
		} 
		else 
		{
			/* Error Generation Code Here */
			$this->buildMainQuery( $this->_retrivePrefix() );
			return $this->_total();
		}
	}
	
	/** Set id for model */
	private function _setId( $id ) 
	{
		$this->id = $id;
		return $this;
	}
	
	/** Get id for model */
	private function _getId() 
	{
		if( isset( $this->id ) ) 
		{
			return $this->id;
		}
		return NULL;
	}
	
	/** Count rows */
	private function _length() 
	{
		return $this->_total();
	}
	
	/** Set row data */ 
	private function _setData( $data, $value = NULL ) 
	{
		if( is_array( $data ) ) 
		{
			foreach( $data as $key => $value ) 
			{
				$this->{$key} = $value;
			}

			return $this;
		}
		elseif( is_string( $data ) && !is_null( $value ) ) 
		{
			$this->{$data} = $value;
		}
		return $this;
	}
	
	/** Set table name */ 
	private function _setModelName( $value ) 
	{
		if( $this->setTable() == MODEL_SFREE && _hasBase() ) 
		{
			$this->_model = $value;
		}
		return $this;
	}
	
	/** Set table name */ 
	private function _setTableName( $value ) 
	{
		if( $this->setTable() == MODEL_SFREE && _hasBase() ) 
		{
			$prefix = $this->_getPrefix();
			
			if( !is_null( $prefix ) && $prefix != '' ) 
			{
				$this->_table = $prefix . $value;
			}
			else 
			{
				$this->_table = $value;
			}
			
			$this->_startConn();
		}
	
		return $this;
	} 

	protected function _retrivePrefix() 
	{
		global $inflect;
		if( !is_null( $this->_prefix ) ) 
		{
			return $this->_getPrefix();
		} 
		$pluralAliasTable = strtolower( $inflect->pluralize( $this->_alias ) );
		$this->_prefix = str_replace( $pluralAliasTable, '', $this->_table );
	}
	
	/** Get Lasted Data */
	private function _getLastedData() 
	{
		return $this->_setLimit( 1 )->_orderBy( 'id', 'desc' )->_search();
	}
	
	/** Get Row Data */ 
	private function _getData( $id = NULL ) 
	{
		if( !is_null( $id ) ) 
		{
			$wid = $id;
		}
		elseif( !is_null( $this->_getId() ) )
		{
			$wid = $id;
		}
		else 
		{
			return NULL;
		}
		
		return $this->_setLimit( 1 )->_search();
	}
	
	/** Get database list */
	private function _dbList() 
	{
		global $configs;
		return $this->query( 'SELECT table_name FROM information_schema.tables where table_schema="' . $configs['DATABASE']['DATABASE'] . '"' );
	}
	
	/** Increament */
	private function _setIncreament( $start ) 
	{
		$this->query( 'ALTER TABLE `' . $this->_table . '`  AUTO_INCREMENT=' . $start );
		return $this;
	}
	
	/** Max id */
	private function _maxId( $label = NULL ) 
	{
		if( is_null( $label ) ) 
		{
			$label_str = 'AS \'id\' ';
		}
		else 
		{
			$label_str = 'AS \'' . $label . '\' ';
		}
		
		$data = $this->query( 'SELECT MAX(`id`) ' . $label_str . 'FROM `' . $this->_getTableName() . '` AS `' . $this->_getModelName() . '` WHERE 1' );
		// $data = $this->query( 'SELECT  MAX(`id`) FROM `' . $this->_getTableName() . '` as `' . $this->_getModelName() . '` WHERE 1' );
		
		if( is_null( $data ) ) 
		{
			return false;
		}
		
		list( $a, $result) = each( $data[ 0 ] );
		
		return (int) $result[ 'id' ];
	}

	/** Get error string **/

	function _getError() 
	{
		return array(
			'error_msg'	=>mysqli_error( $this->_dbHandle ), 
			'error_no'	=>mysqli_errno( $this->_dbHandle ) 
		); 
	}
}