<?php

namespace Zuuda;

abstract class SQLQuery 
{
	protected $_dbHandle;
	protected $_result;
	protected $_query;
	protected $_table;
	protected $_describe = array();
	protected $_order_by;
	protected $_order;
	protected $_extraConditions;
	protected $_collection;
	protected $_hO;
	protected $_hM;
	protected $_hMABTM;
	protected $_page;
	protected $_limit;
	protected $_prefix;
	
	protected function _getDBHandle() { return $this->_dbHandle; }
	protected function _getResult() { return $this->_result; }
	protected function _getQuery() { return $this->_query; }
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
	protected function _setQuery( $value ) { $this->_query = $value; return $this; }
	protected function _setTable( $value ) { return $this->_setTableName( $value ); }
	protected function _setModel( $value ) { return $this->_setModelName( $value ); }
	protected function _setOrderBy( $value ) { $this->_order_by = $value; return $this; }
	protected function _setOrder( $value ) { $this->_order = $value; return $this; }
	protected function _setExtraConditions( $value ) { $this->_extraConditions = $value; return $this; }
	protected function _setCollection( $value ) { $this->_collection = $value; return $this; }
	protected function _setHasOne( $value ) { $this->_hasOne = $value; return $this; }
	protected function _setHasMany( $value ) { $this->_hasMany = $value; return $this; }
	protected function _setHasManyAndBelongsToMany( $value ) { $this->_hasManyAndBelongsToMany = $value; return $this; }
	protected function _setPage( $value ) { $this->_page = $value; return $this; }
	/** private function _setLimit */
	protected function _setPrefix( $value ) { $this->_prefix = $value; return $this; }
	
	/** Connects to database **/
	public function Connect( $address, $account, $pwd, $name ) { return $this->_connect( $address, $account, $pwd, $name ); }
	public function Query( $query = NULL ) { return $this->_query( $query ); }
	public function GetQuery() { return $this->_getQuery(); }
	public function GetModel() { return $this->_getModel(); }
	public function GetModelName() { return $this->_getModel(); }
	public function GetTable() { return $this->_getTable(); }
	public function GetTableName() { return $this->_getTable(); }
	public function Parse( $result ) { return $this->_parse( $result ); }
	public function Item( $result, $index = NULL ) { return $this->_item( $result, $index ); }
	public function Select( $field, $label = NULL ) { return $this->_select( $field, $label = NULL ); }
	public function GetCollectionString() { return $this->_getCollectionString(); }
	public function Where( $field, $value ) { return $this->_where( $field, $value ); }
	public function Like( $field, $value ) { return $this->_like( $field, $value ); }
	public function ShowHasOne() { return $this->_showHasOne(); }
	public function ShowHasMany() { return $this->_showHasMany(); }
	public function ShowHMABTM() { return $this->_showHMABTM(); }
	public function SetLimit( $value ) { return $this->_setLimit( $value ); }
	public function SetPage( $value ) { return $this->_setPage( $value ); }
	public function OrderBy( $order_by, $order = 'ASC' ) { return $this->_orderBy( $order_by, $order ); }
	public function Search() { return $this->_search(); }
	public function Custom( $query ) { return $this->_custom( $query ); }
	public function Delete() { return $this->_delete(); }
	public function Save() { return $this->_save(); }
	public function Clear() { return $this->_clear(); }
	public function TotalPages() { return $this->_totalPages(); }
	public function Length() { return $this->_length(); }
	public function DBList() { return $this->_dbList(); }
	public function SetIncreament( $value ) { $this->_setIncreament( $value ); }
	public function GetId() { return $this->_getId(); }
	public function SetId( $id ) { return $this->_setId( $id ); } 
	public function MaxId( $label = NULL ) { return $this->_maxId( $label ); }
	public function GetMaxId( $label = NULL ) { return $this->_maxId( $label ); }
	public function GetData( $id = NULL ) { return $this->_getData( $id ); }
	public function GetLastedData() { return $this->_getLastedData(); }
	public function SetData( $data, $value = NULL ) { return $this->_setData( $data, $value ); }
	public function SetPrefix( $value ) { return $this->_setPrefix( $value ); }
	public function SetTableName( $value ) { return $this->_setTableName( $value ); }
	public function SetModelName( $value ) { return $this->_setModelName( $value ); }
	public function SetHasOne( $value ) { return $this->_setHasOne( $value ); }
	public function SetHasMany( $value ) { return $this->_setHasMany( $value ); }
	public function SetHasManyAndBelongsToMany( $value ) { return $this->_setHasManyAndBelongsToMany( $value ); }
	
	abstract protected function _startConn();
	abstract protected function setTable();
	private function _connect( $address, $account, $pwd, $name ) 
	{
		$hl = @mysql_connect($address, $account, $pwd);
		if ( $hl != 0 ) 
		{
			if ( @mysql_select_db( $name, $hl ) ) 
			{
				mysql_query( 'SET CHARSET utf8', $hl );
				
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
			if( isset( $result[ $index ][ $model ] ) ) 
			{
				return $result[ $index ][ $model ];
			} 
			else 
			{
				return $result[ $index ][ $this->_model ];
			}
		}
		else 
		{
			if( isset( $result[ $model ] ) ) 
			{
				return $result[ $model ];
			}
			else 
			{
				return $result[ $this->_model ];
			}
		}
	}

	/** Select Query **/
	
	private function _select( $field, $label = NULL ) 
	{
		$this->_collection .=  ',`' . $this->_model . '`.`' . $field . '`';
		if( !is_null( $label ) )
		{
			 $this->_collection .= ' AS \'' . $label . '\'';
		}
		return $this;
	}
	
	private function _getCollectionString() 
	{
		if( is_null( $this->_collection ) )
		{
			return '*';
		}
		return substr( $this->_collection, 1 );
	}

	private function _where( $field, $value ) 
	{
		$this->_extraConditions .= '`'.$this->_model.'`.`'.$field.'` = \''.@mysql_real_escape_string($value).'\' AND ';
		return $this;
	}

	private function _like($field, $value) 
	{
		$this->_extraConditions .= '`'.$this->_model.'`.`'.$field.'` LIKE \'%'.@mysql_real_escape_string($value).'%\' AND ';
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

	private function _search() 
	{
		global $inflect;

		$from = '`'.$this->_table.'` as `'.$this->_model.'` ';
		$conditions = '\'1\'=\'1\' AND ';
		$conditionsChild = '';
		$fromChild = '';
		
		if( $this->_hO == 1 && isset( $this->_hasOne ) ) 
		{
			foreach ( $this->_hasOne as $alias => $model ) 
			{
				$table = strtolower( $inflect->pluralize( $model ) );
				$singularAlias = strtolower( $alias );
				$from .= 'LEFT JOIN `'.$table.'` as `'.$alias.'` ';
				$from .= 'ON `'.$this->_model.'`.`'.$singularAlias.'_id` = `'.$alias.'`.`id`  ';
			}
		}

		if( isset( $this->id ) ) 
		{
			$conditions .= '`'.$this->_model.'`.`id` = \''.@mysql_real_escape_string( $this->id ).'\' AND ';
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
		
		$this->_query = 'SELECT ' . $this->getCollectionString() . ' FROM '.$from.' WHERE '.$conditions;
		#echo '<!--'.$this->_query.'-->';
		$this->_result = @mysql_query( $this->_query, $this->_dbHandle );
		$result = array();
		$table = array();
		$field = array();
		$tempResults = array();
		$numOfFields = @mysql_num_fields( $this->_result );
		
		for ($i = 0; $i < $numOfFields; ++$i) 
		{
		    array_push($table,@mysql_field_table( $this->_result, $i ) );
		    array_push($field,@mysql_field_name( $this->_result, $i ) );
		}
		if (@mysql_num_rows($this->_result) > 0 ) 
		{
			while( $row = @mysql_fetch_row( $this->_result ) ) 
			{
				for( $i = 0; $i < $numOfFields; ++$i ) 
				{
					$tempResults[ $table[ $i ] ][ $field [ $i ] ] = $row[ $i ];
				}

				if( $this->_hM == 1 && isset( $this->_hasMany ) ) 
				{
					foreach ( $this->_hasMany as $alias_child => $modelChild ) 
					{
						$queryChild = '';
						$conditionsChild = '';
						$fromChild = '';

						$tableChild = strtolower($inflect->pluralize($modelChild));
						$pluralAliasChild = strtolower($inflect->pluralize($alias_child));
						$singularAliasChild = strtolower($alias_child);

						$fromChild .= '`'.$tableChild.'` as `'.$alias_child.'`';
						
						$conditionsChild .= '`'.$alias_child.'`.`'.strtolower($this->_model).'_id` = \''.$tempResults[$this->_model][ID].'\'';

						$queryChild =  'SELECT * FROM '.$fromChild.' WHERE '.$conditionsChild;	
						$resultChild = @mysql_query($queryChild, $this->_dbHandle);
				
						$tableChild = array();
						$fieldChild = array();
						$temp_results_child = array();
						$results_child = array();
						
						if (@mysql_num_rows($resultChild) > 0) 
						{
							$numOfFieldsChild = @mysql_num_fields($resultChild);
							for ($j = 0; $j < $numOfFieldsChild; ++$j) 
							{
								array_push($tableChild,@mysql_field_table($resultChild, $j));
								array_push($fieldChild,@mysql_field_name($resultChild, $j));
							}

							while ($rowChild = @mysql_fetch_row($resultChild)) 
							{
								for ($j = 0;$j < $numOfFieldsChild; ++$j) 
								{
									$temp_results_child[$tableChild[$j]][$fieldChild[$j]] = $rowChild[$j];
								}
								array_push( $results_child, $temp_results_child );
							}
						}
						
						$tempResults[ $alias_child ] = $results_child;
						
						@mysql_free_result($resultChild);
					}
				}


				if ($this->_hMABTM == 1 && isset($this->_hasManyAndBelongsToMany)) 
				{
					foreach ($this->_hasManyAndBelongsToMany as $alias_child => $tableChild) 
					{
						$queryChild = '';
						$conditionsChild = '';
						$fromChild = '';

						$tableChild = strtolower($inflect->pluralize($tableChild));
						$pluralAliasChild = strtolower($inflect->pluralize($alias_child));
						$singularAliasChild = strtolower($alias_child);

						$pluralAliasTable = strtolower( $inflect->pluralize( $this->_model ) );
						$prefix = str_replace( $pluralAliasTable, '', $this->_table );
						
						$sortTables = array( $pluralAliasTable ,$pluralAliasChild );
						sort($sortTables);
						$joinTable = $prefix . implode('_',$sortTables);

						$fromChild .= '`'.$tableChild.'` as `'.$alias_child.'`,';
						$fromChild .= '`'.$joinTable.'`,';
						
						$conditionsChild .= '`'.$joinTable.'`.`'.$singularAliasChild.'_id` = `'.$alias_child.'`.`id` AND ';
						$conditionsChild .= '`'.$joinTable.'`.`'.strtolower($this->_model).'_id` = \''.$tempResults[$this->_model]['id'].'\'';
						$fromChild = substr($fromChild,0,-1);

						$queryChild =  'SELECT * FROM '.$fromChild.' WHERE '.$conditionsChild;	
						$resultChild = @mysql_query($queryChild, $this->_dbHandle);
				
						$tableChild = array();
						$fieldChild = array();
						$temp_results_child = array();
						$results_child = array();
						
						if ( @mysql_num_rows( $resultChild ) > 0 ) 
						{
							$numOfFieldsChild = @mysql_num_fields( $resultChild );
							for ( $j = 0; $j < $numOfFieldsChild; ++$j ) 
							{
								array_push( $tableChild, @mysql_field_table( $resultChild, $j ) );
								array_push( $fieldChild, @mysql_field_name( $resultChild, $j ) );
							}

							while ( $rowChild = @mysql_fetch_row( $resultChild ) ) 
							{
								for ( $j = 0;$j < $numOfFieldsChild; ++$j ) 
								{
									$temp_results_child[$tableChild[$j]][$fieldChild[$j]] = $rowChild[$j];
								}
								array_push( $results_child,$temp_results_child );
							}
						}
						
						$tempResults[ $alias_child ] = $results_child;
						@mysql_free_result( $resultChild );
					}
				}

				array_push( $result,$tempResults );
			}

			if ( @mysql_num_rows( $this->_result ) == 1 && $this->id != null ) 
			{
				$result = $result[0];
			}
		} 
		
		@mysql_free_result( $this->_result );
		$this->clear();
		return $result;
	}

	/** Custom SQL Query **/ 

	private function _custom( $query ) 
	{
		global $inflect;

		$this->_result = @mysql_query($query, $this->_dbHandle);

		$result = array();
		$table = array();
		$field = array();
		$tempResults = array();

		if(substr_count(strtoupper($query),"SELECT")>0) 
		{
			if (@mysql_num_rows($this->_result) > 0) 
			{
				$numOfFields = @mysql_num_fields($this->_result);
				for ($i = 0; $i < $numOfFields; ++$i) 
				{
					array_push($table,@mysql_field_table($this->_result, $i));
					array_push($field,@mysql_field_name($this->_result, $i));
				}
				while ($row = @mysql_fetch_row($this->_result)) 
				{
					for ($i = 0;$i < $numOfFields; ++$i) {
						$table[$i] = $inflect->singularize($table[$i]);
						$tempResults[$table[$i]][$field[$i]] = $row[$i];
					}
					array_push($result,$tempResults);
				}
			}
			@mysql_free_result($this->_result);
		}	
		$this->clear();
		return($result);
	}

	/** Describes a Table **/

	protected function _describe() 
	{
		global $cache;

		$this->_describe = $cache->get('describe'.$this->_table);

		if (!$this->_describe) 
		{
			$this->_describe = array();
			$query = 'DESCRIBE '.$this->_table;
			$this->_result = @mysql_query($query, $this->_dbHandle);
			while ($row = @mysql_fetch_row($this->_result)) 
			{
				 array_push($this->_describe,$row[0]);
			}

			@mysql_free_result($this->_result);
			$cache->set('describe'.$this->_table,$this->_describe);
		}
		
		foreach ($this->_describe as $field) 
		{
			$this->$field = null;
		}
	}

	/** Delete an Object **/

	private function _delete() 
	{
		if ( $this->id ) 
		{
			$query = 'DELETE FROM ' . $this->_table . ' WHERE `id`=\''.@mysql_real_escape_string( $this->id ).'\'';		
			$this->_result = @mysql_query( $query, $this->_dbHandle );
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

	private function _save() 
	{
		$query = '';
		if ( isset( $this->id ) ) 
		{
			$updates = '';
			foreach ( $this->_describe as $field ) 
			{
				if ( $this->$field ) 
				{
					$updates .= '`'.$field.'` = \''.@mysql_real_escape_string( $this->$field ).'\',';
				}
			}

			$updates = substr( $updates, 0, -1 );

			$query = 'UPDATE '.$this->_table.' SET '.$updates.' WHERE `id`=\''.@mysql_real_escape_string($this->id).'\''; 
		} 
		else 
		{
			$fields = '';
			$values = '';
			foreach ($this->_describe as $field ) 
			{
				if ( $this->$field ) 
				{
					$fields .= '`'.$field.'`,';
					$values .= '\''.@mysql_real_escape_string($this->$field).'\',';
				}
			}
			$values = substr( $values, 0, -1 );
			$fields = substr( $fields, 0, -1 );

			$query = 'INSERT INTO '.$this->_table.' ('.$fields.') VALUES ('.$values.')'; 
		} 
		
		$this->_result = @mysql_query( $query, $this->_dbHandle );
		$this->clear();
		if ( $this->_result == 0 ) 
		{
			/** Error Generation **/
			return -1;
		} 
		else 
		{
			return ( int ) @mysql_insert_id( $this->_dbHandle );
		}
	}

	/** Clear All Variables **/

	private function _clear() 
	{
		foreach( $this->_describe as $field ) 
		{
			$this->$field = null;
		}

		$this->_order_by = null;
		$this->_extraConditions = null;
		$this->_hO = null;
		$this->_hM = null;
		$this->_hMABTM = null;
		$this->_page = null;
		$this->_order = null;
		return $this;
	}

	/** Pagination Count **/

	private function _totalPages() 
	{
		if ( $this->_query && $this->_limit ) 
		{
			$pattern = '/SELECT (.*?) FROM (.*)LIMIT(.*)/i';
			$replacement = 'SELECT COUNT(*) FROM $2';
			$countQuery = preg_replace( $pattern, $replacement, $this->_query );
			$this->_result = @mysql_query( $countQuery, $this->_dbHandle );
			$count = @mysql_fetch_row( $this->_result );
			$totalPages = ceil( $count[0]/$this->_limit );
			return $totalPages;
		} 
		else 
		{
			/* Error Generation Code Here */
			return -1;
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
		$cond = 'WHERE "1" = "1" AND ';
		$cond .= $this->_extraConditions;
		$query = 'SELECT COUNT(*) AS \'length\' FROM `' . $this->_table . '` AS `' . $this->_model . '`' . substr( $cond, 0, -4 );
		$this->_result = @mysql_query( $query, $this->_dbHandle );
		$data = @mysql_fetch_assoc( $this->_result );
		return (int) $data[ 'length' ];
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
		
		return $this->_where( 'id', $wid )->_setLimit( 1 )->_search();
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
		return @mysql_error( $this->_dbHandle );
	}
}