<?php

namespace Zuuda;

use Exception;

abstract class SQLQuery 
{
	protected $_dbHandle;
	protected $_result;
	protected $_querySQL;
	protected $_querySQLs = array();
	protected $_table;
	protected $_describe = array(); 	// hold the field info (collumns of table), Don't NULL to it
	protected $_undescrible = array();
	protected $_order_by;
	protected $_order;
	protected $_group_by;
	protected $_extraConditions = EMPTY_CHAR;
	protected $_collection;
	protected $_hasExe = false;
	protected $_hO;
	protected $_hM;
	protected $_hMABTM;
	protected $_hasOneBlind = array();
	protected $_hasManyBlind = array();
	protected $_hasManyAndBelongsToManyBlind = array();
	protected $_page;
	protected $_limit;
	protected $_unions;
	protected $_offset;
	protected $_imerge;
	protected $_expresion;
	protected $_prefix;
	protected $_boot;
	protected $_ride;
	// protected $_ibind;
	// protected $timestamp; 
	// public $id;
	
	final protected function _getDBHandle() { return $this->_dbHandle; }
	final protected function _getResult() { return $this->_result; }
	final protected function _getQuerySQL() { return $this->_querySQL; }
	final protected function _getQuerySQLs() { return $this->_querySQLs; }
	final protected function _getModel() { return ( isset( $this->_model ) ) ? $this->_model : NULL; }
	final protected function _getAlias() { return ( isset( $this->_alias ) ) ? $this->_alias : NULL; }
	final protected function _getModelName() { return $this->_getModel(); }
	final protected function _getTable() { return ( isset( $this->_table ) ) ? $this->_table : NULL; }
	final protected function _getTableName() { return $this->_getTable(); }
	final protected function _getDescribe() { return $this->_describe; }
	final protected function _getOrderBy() { return $this->_order_by; }
	final protected function _getOrder() { return $this->_order; }
	final protected function _getExtraConditions() { return $this->_extraConditions; }
	final protected function _getCollection() { return $this->_collection; }
	final protected function _getHasOne() { return ( isset( $this->_hasOne ) ) ? $this->_hasOne : NULL; }
	final protected function _getHasMany() { return ( isset( $this->_hasMany ) ) ? $this->_hasMany : NULL; }
	final protected function _getHasManyAndBelongsToMany() { return ( isset( $this->_hasManyAndBelongsToMany ) ) ? $this->_hasManyAndBelongsToMany : NULL; }
	final protected function _getPage() { return $this->_page; }
	final protected function _getLimit() { return $this->_limit; }
	final protected function _getPrefix() { return $this->_prefix; }
	
	final protected function _setDBHandle( $value ) { $this->_dbHandle = $value; return $this; }
	final protected function _setResult( $value ) { $this->_result = $value; return $this; }
	final protected function _setQuery( $value ) { $this->_querySQL = $value; return $this; }
	final protected function _setTable( $value ) { return $this->_setTableName( $value ); }
	final protected function _setModel( $value ) { return $this->_setModelName( $value ); }
	final protected function _setAlias( $value ) { return $this->_setAliasName( $value ); }
	final protected function _setOrderBy( $value ) { $this->_order_by = $value; return $this; }
	final protected function _setOrder( $value ) { $this->_order = $value; return $this; }
	final protected function _setExtraConditions( $value ) { $this->_extraConditions = $value; return $this; }
	final protected function _setCollection( $value ) { $this->_collection = $value; return $this; }

	final protected function _setHasOne( $value ) { return $this->_orderHasOne( $value ); }
	final protected function _addHasOne( $value ) { return $this->_orderHasOne( $value ); }
	final protected function _setHasMany( $value ) { return $this->_orderHasMany( $value ); }
	final protected function _addHasMany( $value ) { return $this->_orderHasMany( $value ); }
	final protected function _setHasManyAndBelongsToMany( $value ) { return $this->_orderHMABTM( $value ); }
	final protected function _addHasManyAndBelongsToMany( $value ) { return $this->_orderHMABTM( $value ); }

	final protected function _setPage( &$value ) { $this->_page = $value = (int) $value; return $this; }
	final protected function _setPrefix( $value ) { $this->_prefix = $value; return $this; }
	final protected function _new() { return $this->clear( true ); } 
	
	final public function Connect( $address, $account, $pwd, $name ) { return $this->_connect( $address, $account, $pwd, $name ); }
	final public function Query( $query = NULL ) { return $this->_query( $query ); }
	final public function GetQuery() { return $this->_getQuerySQL(); }
	final public function GetQuerySQLs() { return $this->_getQuerySQLs(); } 
	final public function GetQuerySQL() { return $this->_getQuerySQL(); }
	final public function GetModel() { return $this->_getModel(); }
	final public function GetModelName() { return $this->_getModel(); }
	final public function GetTable() { return $this->_getTable(); }
	final public function GetTableName() { return $this->_getTable(); }
	final public function GetAlias() { return $this->_getAlias(); }
	final public function GetAliasName() { return $this->_getAlias(); }
	final public function Parse( $result ) { return $this->_parse( $result ); }
	final public function First()	{ return $this->_first(); }
	final public function Item( $result, $index = 0 ) { return $this->_item( $result, $index ); }
	final public function GetCollectionString() { return $this->_getCollectionString(); }

	final public function Select( $fields, $label = NULL ) { return $this->_select( $fields, $label ); }
	final public function Unselect( $fields ) { return $this->_unselect( $fields ); }
	final public function Blind( $fields ) { return $this->_unselect( $fields ); } 
	final public function Secure( $fields ) { return $this->_unselect( $fields ); }
	final public function UnBlind( $fields ) { return $this->_unblind( $fields ); }
	final public function Between( $field, $start, $end ) { return $this->_between( $field, $start, $end ); }
	final public function Equal( $field, $value ) { return $this->_equal( $field, $value ); }
	final public function Greater( $field, $value ) { return $this->_greater( $field, $value ); } 
	final public function GreaterThanOrEqual( $field, $value ) { return $this->_greaterThanOrEqual( $field, $value ); } 
	final public function In( $field, $values ) { return $this->_in( $field, $values ); }
	final public function Is( $field, $value ) { return $this->_is( $field, $value ); }
	final public function IsNot( $field, $value ) { return $this->_isNot( $field, $value ); }
	final public function IsNotNull( $field ) { return $this->_isNotNull( $field ); }
	final public function IsNull( $field ) { return $this->_isNull( $field ); }
	final public function Less( $field, $value ) { return $this->_less( $field, $value ); } 
	final public function LessThanOrEqual( $field, $value ) { return $this->_lessThanOrEqual( $field, $value ); } 
	final public function Like( $field, $value ) { return $this->_like( $field, $value ); }
	final public function Not( $field, $value ) { return $this->_not( $field, $value ); }
	final public function NotNull( $field ) { return $this->_notNull( $field ); }
	final public function NotEqual( $field, $value ) { return $this->_notEqual( $field, $value ); }
	final public function NotIn( $field, $values ) { return $this->_notIn( $field, $values ); }
	final public function NotLike( $field, $value ) { return $this->_notLike( $field, $value ); }
	final public function Where( $field, $value, $operaion='=' ) { return $this->_where( $field, $value, $operaion ); }
	
	final public function SelectHasManyAsBelongToMany( $model, $field, $label = NULL ) { return $this->_select_HMABTM( $model, $field, $label ); }
	final public function UnselectHasManyAsBelongToMany( $model, $fields ) { return $this->_unselect_HMABTM( $model, $fields ); } 
	final public function SecureHasManyAsBelongToMany( $model, $fields ) { return $this->_unselect_HMABTM( $model, $fields ); } 
	final public function UnsecureHasManyAsBelongToMany( $model, $fields ) { return $this->_unsecure_HMABTM( $model, $fields ); } 
	final public function BetweenHasManyAsBelongToMany( $model, $field, $start, $end ) { return $this->_between_HMABTM( $model, $field, $start, $end ); }
	final public function EqualHasManyAsBelongToMany( $model, $field, $value ) { return $this->_equal_HMABTM( $model, $field, $value ); } 
	final public function GreaterHasManyAsBelongToMany( $model, $field, $value ) { return $this->_greater_HMABTM( $model, $field, $value ); } 
	final public function GreaterThanOrEqualHasManyAsBelongToMany( $model, $field, $value ) { return $this->_greaterThanOrEqual_HMABTM( $model, $field, $value ); } 
	final public function InHasManyAsBelongToMany( $model, $field, $values ) { return $this->_in_HMABTM( $model, $field, $values ); } 
	final public function IsHasManyAsBelongToMany( $model, $field, $value ) { return $this->_is_HMABTM( $model, $field, $value ); } 
	final public function IsNotHasManyAsBelongToMany( $model, $field, $value ) { return $this->_isNot_HMABTM( $model, $field, $value ); } 
	final public function IsNotNullHasManyAsBelongToMany( $model, $field ) { return $this->_isNotNull_HMABTM( $model, $field ); } 
	final public function IsNullHasManyAsBelongToMany( $model, $field ) { return $this->_isNull_HMABTM( $model, $field ); } 
	final public function LessHasManyAsBelongToMany( $model, $field, $value ) { return $this->_less_HMABTM( $model, $field, $value ); } 
	final public function LessThanOrEqualHasManyAsBelongToMany( $model, $field, $value ) { return $this->_lessThanOrEqual_HMABTM( $model, $field, $value ); } 
	final public function LikeHasManyAsBelongToMany( $model, $field, $value ) { return $this->_like_HMABTM( $model, $field, $value ); } 
	final public function NotHasManyAsBelongToMany( $model, $field, $value ) { return $this->_not_HMABTM( $model, $field, $value ); } 
	final public function NotNullHasManyAsBelongToMany( $model, $field ) { return $this->_notNull_HMABTM( $model, $field ); } 
	final public function NotEqualHasManyAsBelongToMany( $model, $field, $value ) { return $this->_notEqual_HMABTM( $model, $field, $value ); } 
	final public function NotInHasManyAsBelongToMany( $model, $field, $values ) { return $this->_notIn_HMABTM( $model, $field, $values ); } 
	final public function NotLikeHasManyAsBelongToMany( $model, $field, $value ) { return $this->_notLike_HMABTM( $model, $field, $value ); } 
	final public function WhereHasManyAsBelongToMany( $model, $field, $value, $operator = '=') { return $this->_where_HMABTM( $model, $field, $value, $operator ); } 
	final public function LimitHasManyAsBelongToMany( $model, $numrows = 1000 ) { return $this->_limit_HMABTM( $model, $numrows ); }
	final public function ReverseHasManyAsBelongToMany( $model, $field = "id" ) { return $this->_reverse_HMABTM( $model, $field ); }
	final public function HideRelativeHasManyAsBelongToMany( $model ) { return $this->_hide_Relative_HMABTM( $model ); }
	final public function ShowRelativeHasManyAsBelongToMany( $model ) { return $this->_show_Relative_HMABTM( $model ); } 

	final public function SelectHMABTM( $model, $field, $label = NULL ) { return $this->_select_HMABTM( $model, $field, $label ); }
	final public function UnselectHMABTM( $model, $fields ) { return $this->_unselect_HMABTM( $model, $fields ); } 
	final public function SecureHMABTM( $model, $fields ) { return $this->_unselect_HMABTM( $model, $fields ); } 
	final public function UnsecureHMABTM( $model, $fields ) { return $this->_unsecure_HMABTM( $model, $fields ); } 
	final public function BetweenHMABTM( $model, $field, $start, $end ) { return $this->_between_HMABTM( $model, $field, $start, $end ); }
	final public function EqualHMABTM( $model, $field, $value ) { return $this->_equal_HMABTM( $model, $field, $value ); } 
	final public function GreaterHMABTM( $model, $field, $value ) { return $this->_greater_HMABTM( $model, $field, $value ); } 
	final public function GreaterThanOrEqualHMABTM( $model, $field, $value ) { return $this->_greaterThanOrEqual_HMABTM( $model, $field, $value ); } 
	final public function InHMABTM( $model, $field, $values ) { return $this->_in_HMABTM( $model, $field, $values ); } 
	final public function IsHMABTM( $model, $field, $value ) { return $this->_is_HMABTM( $model, $field, $value ); } 
	final public function IsNotHMABTM( $model, $field, $value ) { return $this->_isNot_HMABTM( $model, $field, $value ); } 
	final public function IsNotNullHMABTM( $model, $field ) { return $this->_isNotNull_HMABTM( $model, $field ); } 
	final public function IsNullHMABTM( $model, $field ) { return $this->_isNull_HMABTM( $model, $field ); } 
	final public function LessHMABTM( $model, $field, $value ) { return $this->_less_HMABTM( $model, $field, $value ); } 
	final public function LessThanOrEqualHMABTM( $model, $field, $value ) { return $this->_lessThanOrEqual_HMABTM( $model, $field, $value ); } 
	final public function LikeHMABTM( $model, $field, $value ) { return $this->_like_HMABTM( $model, $field, $value ); } 
	final public function NotHMABTM( $model, $field, $value ) { return $this->_not_HMABTM( $model, $field, $value ); } 
	final public function NotNullHMABTM( $model, $field ) { return $this->_notNull_HMABTM( $model, $field ); } 
	final public function NotEqualHMABTM( $model, $field, $value ) { return $this->_notEqual_HMABTM( $model, $field, $value ); } 
	final public function NotInHMABTM( $model, $field, $values ) { return $this->_notIn_HMABTM( $model, $field, $values ); } 
	final public function NotLikeHMABTM( $model, $field, $value ) { return $this->_notLike_HMABTM( $model, $field, $value ); } 
	final public function WhereHMABTM( $model, $field, $value, $operator = '=') { return $this->_where_HMABTM( $model, $field, $value, $operator ); } 
	final public function LimitHMABTM( $model, $numrows = 1000 ) { return $this->_limit_HMABTM( $model, $numrows ); }
	final public function ReverseHMABTM( $model, $field = "id" ) { return $this->_reverse_HMABTM( $model, $field ); }
	final public function HideRelativeHMABTM( $model ) { return $this->_hide_Relative_HMABTM( $model ); }
	final public function ShowRelativeHMABTM( $model ) { return $this->_show_Relative_HMABTM( $model ); } 

	final public function SelectHasMany( $model, $field, $label = NULL ) { return $this->_select_HasMany( $model, $field, $label ); }
	final public function UnselectHasMany( $model, $fields ) { return $this->_unselect_HasMany( $model, $fields ); } 
	final public function SecureHasMany( $model, $fields ) { return $this->_unselect_HasMany( $model, $fields ); } 
	final public function UnsecureHasMany( $model, $fields ) { return $this->_unsecure_HasMany( $model, $fields ); } 
	final public function BetweenHasMany( $model, $field, $start, $end ) { return $this->_between_HasMany( $model, $field, $start, $end ); }
	final public function EqualHasMany( $model, $field, $value ) { return $this->_equal_HasMany( $model, $field, $value ); } 
	final public function GreaterHasMany( $model, $field, $value ) { return $this->_greater_HasMany( $model, $field, $value ); } 
	final public function GreaterThanOrEqualHasMany( $model, $field, $value ) { return $this->_greaterThanOrEqual_HasMany( $model, $field, $value ); } 
	final public function InHasMany( $model, $field, $values ) { return $this->_in_HasMany( $model, $field, $values ); } 
	final public function IsHasMany( $model, $field, $value ) { return $this->_is_HasMany( $model, $field, $value ); } 
	final public function IsNotHasMany( $model, $field, $value ) { return $this->_isNot_HasMany( $model, $field, $value ); } 
	final public function IsNotNullHasMany( $model, $field ) { return $this->_isNotNull_HasMany( $model, $field ); } 
	final public function IsNullHasMany( $model, $field ) { return $this->_isNull_HasMany( $model, $field ); } 
	final public function LessHasMany( $model, $field, $value ) { return $this->_less_HasMany( $model, $field, $value ); } 
	final public function LessThanOrEqualHasMany( $model, $field, $value ) { return $this->_lessThanOrEqual_HasMany( $model, $field, $value ); } 
	final public function LikeHasMany( $model, $field, $value ) { return $this->_like_HasMany( $model, $field, $value ); } 
	final public function NotHasMany( $model, $field, $value ) { return $this->_not_HasMany( $model, $field, $value ); } 
	final public function NotNullHasMany( $model, $field ) { return $this->_notNull_HasMany( $model, $field ); } 
	final public function NotEqualHasMany( $model, $field, $value ) { return $this->_notEqual_HasMany( $model, $field, $value ); } 
	final public function NotInHasMany( $model, $field, $values ) { return $this->_notIn_HasMany( $model, $field, $values ); } 
	final public function NotLikeHasMany( $model, $field, $value ) { return $this->_notLike_HasMany( $model, $field, $value ); } 
	final public function WhereHasMany( $model, $field, $value, $operator = '=') { return $this->_where_HasMany( $model, $field, $value, $operator ); }
	final public function LimitHasMany( $model, $numrows = 1000 ) { return $this->_limit_HasMany( $model, $numrows ); }
	final public function ReverseHasMany( $model, $field = "id" ) { return $this->_reverse_HasMany( $model, $field ); }

	final public function SelectHM( $model, $field, $label = NULL ) { return $this->_select_HasMany( $model, $field, $label ); }
	final public function UnselectHM( $model, $fields ) { return $this->_unselect_HasMany( $model, $fields ); } 
	final public function SecureHM( $model, $fields ) { return $this->_unselect_HasMany( $model, $fields ); } 
	final public function UnsecureHM( $model, $fields ) { return $this->_unsecure_HasMany( $model, $fields ); } 
	final public function BetweenHM( $model, $field, $start, $end ) { return $this->_between_HasMany( $model, $field, $start, $end ); }
	final public function EqualHM( $model, $field, $value ) { return $this->_equal_HasMany( $model, $field, $value ); } 
	final public function GreaterHM( $model, $field, $value ) { return $this->_greater_HasMany( $model, $field, $value ); } 
	final public function GreaterThanOrEqualHM( $model, $field, $value ) { return $this->_greaterThanOrEqual_HasMany( $model, $field, $value ); } 
	final public function InHM( $model, $field, $values ) { return $this->_in_HasMany( $model, $field, $values ); } 
	final public function IsHM( $model, $field, $value ) { return $this->_is_HasMany( $model, $field, $value ); } 
	final public function IsNotHM( $model, $field, $value ) { return $this->_isNot_HasMany( $model, $field, $value ); } 
	final public function IsNotNullHM( $model, $field ) { return $this->_isNotNull_HasMany( $model, $field ); } 
	final public function IsNullHM( $model, $field ) { return $this->_isNull_HasMany( $model, $field ); } 
	final public function LessHM( $model, $field, $value ) { return $this->_less_HasMany( $model, $field, $value ); } 
	final public function LessThanOrEqualHM( $model, $field, $value ) { return $this->_lessThanOrEqual_HasMany( $model, $field, $value ); } 
	final public function LikeHM( $model, $field, $value ) { return $this->_like_HasMany( $model, $field, $value ); } 
	final public function NotHM( $model, $field, $value ) { return $this->_not_HasMany( $model, $field, $value ); } 
	final public function NotNullHM( $model, $field ) { return $this->_notNull_HasMany( $model, $field ); } 
	final public function NotEqualHM( $model, $field, $value ) { return $this->_notEqual_HasMany( $model, $field, $value ); } 
	final public function NotInHM( $model, $field, $values ) { return $this->_notIn_HasMany( $model, $field, $values ); } 
	final public function NotLikeHM( $model, $field, $value ) { return $this->_notLike_HasMany( $model, $field, $value ); } 
	final public function WhereHM( $model, $field, $value, $operator = '=') { return $this->_where_HasMany( $model, $field, $value, $operator ); }
	final public function LimitHM( $model, $numrows = 1000 ) { return $this->_limit_HasMany( $model, $numrows ); }
	final public function ReverseHM( $model, $id = "id" ) { return $this->_reverse_HasMany( $model, $id ); }
	
	final public function SelectHasOne( $model, $field, $label = NULL ) { return $this->_select_HasOne( $model, $field, $label ); }
	final public function UnselectHasOne( $model, $fields ) { return $this->_unselect_HasOne( $model, $fields ); } 
	final public function SecureHasOne( $model, $fields ) { return $this->_unselect_HasOne( $model, $fields ); } 
	final public function UnsecureHasOne( $model, $fields ) { return $this->_unsecure_HasOne( $model, $fields ); }
		
	final public function SelectHO( $model, $field, $label = NULL ) { return $this->_select_HasOne( $model, $field, $label ); }
	final public function UnselectHO( $model, $fields ) { return $this->_unselect_HasOne( $model, $fields ); } 
	final public function SecureHO( $model, $fields ) { return $this->_unselect_HasOne( $model, $fields ); } 
	final public function UnsecureHO( $model, $fields ) { return $this->_unsecure_HasOne( $model, $fields ); } 

	final public function ShowHasOne() { return $this->_showHasOne(); }
	final public function ShowHasMany() { return $this->_showHasMany(); }
	final public function ShowHMABTM() { return $this->_showHMABTM(); }
	final public function HideHasOne() { return $this->_hideHasOne(); }
	final public function HideHasMany() { return $this->_hideHasMany(); }
	final public function HideHMABTM() { return $this->_hideHMABTM(); }
	final public function ClearHasOne() { return $this->_clearHasOne(); }
	final public function ClearHasMany() { return $this->_clearHasMany(); }
	final public function ClearHMABTM() { return $this->_clearHMABTM(); } 
	final public function ConvertHasOne( $data ) { return $this->_convertHasOne( $data ); } 
	final public function ConvertHasMany( $data ) { return $this->_convertHasMany( $data ); } 
	final public function ConvertHMABTM( $data ) { return $this->_convertHMABTM( $data ); } 
	final public function BlindHasOne( $data ) { return $this->_blindHasOne( $data ); } 
	final public function BlindHasMany( $data ) { return $this->_blindHasMany( $data ); } 
	final public function BlindHMABTM( $data ) { return $this->_blindHMABTM( $data ); } 
	final public function UnblindHasOne( $data ) { return $this->_unblindHasOne( $data ); } 
	final public function UnblindHasMany( $data ) { return $this->_unblindHasMany( $data ); } 
	final public function UnblindHMABTM( $data ) { return $this->_unblindHMABTM( $data ); } 

	final public function SetLimit( $value ) { return $this->_setLimit( $value ); }
	final public function Limit( $value ) { return $this->_setLimit( $value ); }
	final public function Offset( $position ) { return $this->_seek( $position ); }
	final public function Seek( $position ) { return $this->_seek( $position ); }
	final public function SetPage( $value ) { return $this->_setPage( $value ); }
	final public function Page( $value ) { return $this->_setPage( $value ); }
	final public function OrderBy( $order_by, $order = 'ASC' ) { return $this->_orderBy( $order_by, $order ); }
	final public function Order( $order_by, $order = 'ASC' ) { return $this->_orderBy( $order_by, $order ); }
	final public function GroupBy( $field ) { return $this->_groupBy( $field ); }
	final public function GroupByLabel( $label ) { return $this->_groupWith( $label ); }
	final public function GroupWith( $label ) { return $this->_groupWith( $label ); }
	final public function GroupLabel( $label ) { return $this->_groupWith( $label ); }

	final public function Command( $expresion ) { return $this->_command( $expresion ); }
	final public function Output( $label ) { return $this->_output( $label ); }
	final public function Execute() { return $this->_execute(); }

	final public function Load() { return $this->_search(); }
	final public function Search() { return $this->_search(); } 
	final public function Paginate( $page=NULL, $limit=NULL ) { return $this->_paginate( $page, $limit ); }
	final public function Custom( $query ) { return $this->_custom( $query ); }
	final public function Delete( $id=NULL ) { return $this->_delete( $id ); }
	final public function Save( $data=NULL ) { return $this->_save( $data ); }
	final public function Update( $data=NULL ) { return $this->_update( $data ); }
	final public function Clear( $deep=false ) { return $this->_clear( $deep ); }
	final public function TotalPages() { return $this->_totalPages(); } 
	final public function Total() { return $this->_total(); } 
	final public function Count() { return $this->_count(); }
	final public function Length() { return $this->_length(); }
	final public function DBList() { return $this->_dbList(); }
	final public function SetIncreament( $value ) { $this->_setIncreament( $value ); }
	final public function GetId() { return $this->_getId(); }
	final public function SetId( $id ) { return $this->_setId( $id ); } 
	final public function UnsetId() { return $this->_setId(); }
	final public function MaxId() { return $this->_max('id'); }
	final public function GetMaxId() { return $this->_max('id'); }
	final public function Max( $field ) { return $this->_max( $field ); } 
	final public function GetMax( $field ) { return $this->_max( $field ); }
	final public function GetPrefix() { return $this->_getPrefix(); }
	final public function ToArray() { return $this->_toarray(); }
	final public function GetData( $id = NULL ) { return $this->_getData( $id ); }
	final public function GetRowData( $id ) { return $this->_getRowData( $id ); }
	final public function GetLastedData() { return $this->_getLastedData(); }
	final public function GetError() { return $this->_getError(); } 
	final public function SetData( $data, $value = NULL ) { return $this->_setData( $data, $value ); }
	final public function Set( $data, $value = NULL ) { return $this->_setData( $data, $value ); }
	final public function Assign( $data, $value = NULL ) { return $this->_setData( $data, $value ); }
	final public function SetPrefix( $value ) { return $this->_setPrefix( $value ); }
	final public function SetTableName( $value ) { return $this->_setTableName( $value ); }
	final public function SetModelName( $value ) { return $this->_setModelName( $value ); }
	final public function SetAliasName( $value ) { return $this->_setAliasName( $value ); }
	final public function SetHasOne( $value ) { return $this->_setHasOne( $value ); }
	final public function SetHasMany( $value ) { return $this->_setHasMany( $value ); }
	final public function SetHasManyAndBelongsToMany( $value ) { return $this->_setHasManyAndBelongsToMany( $value ); }
	final public function AddHasOne( $value ) { return $this->_addHasOne( $value ); }
	final public function AddHasMany( $value ) { return $this->_addHasMany( $value ); }
	final public function AddHasManyAndBelongsToMany( $value ) { return $this->_addHasManyAndBelongsToMany( $value ); }
	final public function Merge( $value ) { return $this->_merge( $value ); }
	final public function Include( $value ) { return $this->_include( $value ); } 
	final public function Bind( $value ) { return $this->_merge( $value ); } 
	final public function Find( $id ) { return $this->_find( $id ); } 
	final public function FindData( $id ) { return $this->_findData( $id ); }
	final public function New() { return $this->_new(); }
	final public function Reset() { return $this->_new(); }
	final public function Begin() { return $this->_new(); }
	final public function ShareMainQuery() { return $this->_shareMainQuery(); }
	final public function GenRandString( $len=10 ) { return $this->_genRandString($len); }

	// final public function boot() { return NULL; }
	// final public function down() { return NULL; }
	
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
	
	private function _first() 
	{
		$result = $this->_item( $this->_search() );
		$this->_setData($result);
		return $result;
	}
	
	private function _item( $result, $index = 0 ) 
	{
		global $inflect; 
		$model = $inflect->singularize( (string) $this->_model ); 
		if( isset($result[$index][$model]) ) 
			return $result[$index][$model]; 
		else if( isset($result[$index][$this->_model]) ) 
			return $result[$index][$this->_model]; 
		else if( isset($result[$model]) ) 
			return $result[$model]; 
		else if( isset($result[$this->_model]) ) 
			return $result[$this->_model];
		else
			return NULL;
	}

	/** Select Query **/
	
	private function _select( $fields, $label = NULL ) 
	{
		$model = $this->_model;
		
		if( strpos($this->_collection, 'COUNT') ) 
		{ 
			$this->_collection = EMPTY_CHAR;
		}

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

		if( is_string($fields) ) 
		{
			if(strtolower($fields)==='count') 
			{
				if(NULL!==$label) 
				{
					$this->_collection = ' COUNT(*)'; 
					goto ADD_LABEL;
				} 
				else 
				{
					$this->_collection = ' COUNT(*) AS count'; 
					return $this;
				}
			}
			else if( stripos( $fields, DOT ) ) 
			{
				$fields = explode( DOT, $fields );
				if( $fields[ 0 ]!== EMPTY_CHAR ) 
				{
					$model = $fields[ 0 ];
				} 
				$fields = $fields[ 1 ];
			}
		}
		
		$this->_collection .=  ',`' . $model . '`.`' . $fields . '`';
		
		ADD_LABEL:
		if( !is_null( $label ) )
		{
			$this->_collection .= ' AS \'' . $label . '\'';
		}
		return $this;
	} 
	
	private function _unblind( $fields ) 
	{
		foreach( $fields as $key => $field ) 
			if(in_array($field, $this->_undescrible)) 
				unset($this->_undescrible[$key]);
		return $this;
	}

	private function _unselect( $fields ) 
	{
		if( is_array( $fields ) ) 
		{
			foreach( $fields as $key ) 
			{
				$this->_unselect( $key );
			}
			return $this;
		} 

		$this->_undescrible = array_merge( $this->_undescrible, (array) $fields ); 
		return $this;
	}
	
	private function _getCollectionString() 
	{
		if( is_null( $this->_collection ) )
		{
			if( !empty( $this->_undescrible ) && ( (isset($this->_hasOne) && NULL===$this->_hasOne) || ((isset($this->_hasOne) && NULL!==$this->_hasOne && $this->_hO==null)) ) && $this->_imerge===NULL ) 
			{
				$describe = $this->_describe; 
				$hasChange = false;
				foreach( $describe as $key => $value ) 
				{
					if( in_array( $value, $this->_undescrible ) ) 
					{
						unset( $describe[ $key ] );
						$hasChange = true;
					} 
					else 
					{
						$describe[ $key ] = $this->_model . "`.`" . $describe[ $key ];
					}
				}

				if( $hasChange ) 
				{
					return '`'. implode( '`, `', $describe ) .'`';
				}
			} 

			return '*';
		}
		return substr( $this->_collection, 1 ); 
	}

	private function _between( $field, $start_value, $end_value ) 
	{
		if( is_string($start_value) ) 
			$sql_start_value = "'".mysqli_real_escape_string( $this->_dbHandle, $start_value )."'";
		else 
			$sql_start_value = $start_value;

		if( is_string($end_value) ) 
			$sql_end_value = "'".mysqli_real_escape_string( $this->_dbHandle, $end_value )."'";
		else 
			$sql_end_value = $end_value;

		$this->_extraConditions .= "`".$this->_model."`.`".$field."` BETWEEN ".$sql_start_value." AND ".$sql_end_value." AND ";
		
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
		foreach( $values  as $key => $value ) 
		{
			if( is_string($value) ) 
				$values[ $key ] = "'".mysqli_real_escape_string( $this->_dbHandle, $value )."'";
			elseif( is_bool($value) )
				$values[ $key ] = ($value)?1:0;
		}
		return $this->_where( $field, "(".implode( ', ', $values ).")", 'IN' );
	}

	private function _notIn( $field, $values ) 
	{
		foreach( $values  as $key => $value ) 
		{
			if( is_string($value) ) 
				$values[ $key ] = "'".mysqli_real_escape_string( $this->_dbHandle, $value )."'";
			elseif( is_bool($value) )
				$values[ $key ] = ($value)?1:0;
		}
		return $this->where( $field, "(".implode( ', ', $values ).")", 'NOT IN' );
	} 

	private function _is( $field, $value ) 
	{
		return $this->_where( $field, $value, 'IS' ); 
	} 

	private function _isNot( $field, $value ) 
	{
		return $this->_where( $field, $value, 'IS NOT' );
	} 

	private function _isNotNull( $field ) 
	{
		return $this->_where( $field, NULL, 'IS NOT' );
	} 

	private function _isNull( $field ) 
	{
		return $this->_where( $field, NULL, 'IS' );
	} 

	private function _less( $field, $value ) 
	{
		return $this->_where( $field, $value, '<' );
	} 

	private function _lessThanOrEqual( $field, $value ) 
	{
		return $this->_where( $field, $value, '<=' );
	} 

	private function _like( $field, $value ) 
	{
		return $this->where( $field, $value, 'LIKE' );
	}

	private function _notLike( $field, $value ) 
	{
		return $this->where( $field, $value, 'NOT LIKE' );
	}

	private function _not( $field, $value ) 
	{
		return $this->_notEqual( $field, $value );
	} 

	private function _notNull( $field ) 
	{
		return $this->_isNotNull( $field );
	} 

	private function _notEqual( $field, $value ) 
	{
		return $this->_where( $field, $value, '!=' );
	}

	private function _where( $field, $value, $operator='=' ) 
	{
		if( $operator === 'LIKE' || $operator === 'NOT LIKE' ) 
			$sql_value = "'%".mysqli_real_escape_string( $this->_dbHandle, $value )."%'";
		elseif( is_string($value) && $operator !== 'IN' && $operator !== 'NOT IN' ) 
			$sql_value = "'".mysqli_real_escape_string( $this->_dbHandle, $value )."'";
		elseif( is_bool($value) ) 
			$sql_value = ($value)?1:0;
		elseif( is_null($value) ) 
			$sql_value = 'NULL'; 
		else 
			$sql_value = $value;

		$model = $this->_model;

		if( stripos( $field, DOT ) !== false ) 
		{
			$field = explode( DOT, $field );
			if($field[ 0 ] !== "") 
			{
				$model = $field[ 0 ];
			} 
			$field = $field[ 1 ]; 
		}

		$field = '`' . $model . '`.`' . $field . '`'; 
		
		try 
		{
			if( is_array($value) ) 
				throw new Exception( 'Can\'t use <i>$field_value</i> as an array in condation function <i>where( $field_name, $field_value, $operator )</i>.');
			$this->_extraConditions .= $field.' '.$operator.' '.$sql_value.' AND ';
		}
		catch(Exception $e) 
		{
			abort( 400, $e->getMessage() ); 
		}

		return $this;
	} 

	private function _select_HMABTM( $model, $field, $label = NULL ) 
	{
		if( isset($this->_hasManyAndBelongsToMany) && NULL!==$this->_hMABTM ) 
		{
			if( isset($this->_hasManyAndBelongsToMany[ $model ]) ) 
			{
				if( is_array($this->_hasManyAndBelongsToMany[ $model ]) ) 
				{
					if( !isset($this->_hasManyAndBelongsToMany[ $model ][ 'describe' ]) ) 
					{
						$this->_hasManyAndBelongsToMany[ $model ][ 'describe' ] = array();
					}

					$field_agrs = array("field"	=> (array)$field);

					if( !is_null($label) ) 
					{
						$field_name = (is_array($field))?$field[ 0 ]:$field;
						$field_agrs[ "label" ] = array($field_name=>$label);
					}

					array_push( $this->_hasManyAndBelongsToMany[ $model ][ 'describe' ], $field_agrs );
				}
			}
		}
		return $this;
	} 

	private function _unselect_HMABTM( $model, $fields ) 
	{
		if( isset($this->_hasManyAndBelongsToMany) && NULL!==$this->_hMABTM ) 
		{
			if( isset($this->_hasManyAndBelongsToMany[ $model ]) ) 
			{
				if( is_array($this->_hasManyAndBelongsToMany[ $model ]) ) 
				{
					if( !isset($this->_hasManyAndBelongsToMany[ $model ][ 'undescribe' ]) ) 
					{
						$this->_hasManyAndBelongsToMany[ $model ][ 'undescribe' ] = array();
					}
					$this->_hasManyAndBelongsToMany[ $model ][ 'undescribe' ] = array_merge( $this->_hasManyAndBelongsToMany[ $model ][ 'undescribe' ], (array)$fields );
				}
			}
		}
		return $this;
	} 
	
	private function _unsecure_HMABTM( $model, $fields ) 
	{
		// ...
	}

	private function _between_HMABTM( $model, $field, $start, $end ) 
	{
		if( isset($this->_hasManyAndBelongsToMany) && NULL!==$this->_hMABTM ) 
		{
			if( isset($this->_hasManyAndBelongsToMany[ $model ]) ) 
			{
				if( is_array($this->_hasManyAndBelongsToMany[ $model ]) ) 
				{
					if( !isset($this->_hasManyAndBelongsToMany[ $model ][ 'conds' ]) ) 
					{
						$this->_hasManyAndBelongsToMany[ $model ][ 'conds' ] = array();
					} 
					array_push( $this->_hasManyAndBelongsToMany[ $model ][ 'conds' ], array(
						"field"		=> $field, 
						"start"		=> $start, 
						"end"		=> $end, 
						"operator"	=> "BETWEEN" 
					));
				}
			}
		} 
		return $this;
	} 

	private function _equal_HMABTM( $model, $field, $value ) 
	{
		return $this->_where_HMABTM( $model, $field, $value );
	} 

	private function _not_HMABTM( $model, $field, $value ) 
	{
		return $this->_notEqual_HMABTM( $model, $field, $value );
	}

	private function _notNull_HMABTM( $model, $field ) 
	{
		return $this->_isNotNull_HMABTM( $model, $field );
	} 

	private function _notEqual_HMABTM( $model, $field, $value ) 
	{
		return $this->_where_HMABTM( $model, $field, $value, "!=" );
	}

	private function _is_HMABTM( $model, $field, $value ) 
	{
		return $this->_where_HMABTM( $model, $field, $value, "IS" );
	} 

	private function _isNot_HMABTM( $model, $field, $value ) 
	{
		return $this->_where_HMABTM( $model, $field, $value, "IS NOT" );
	} 

	private function _isNotNull_HMABTM( $model, $field ) 
	{
		return $this->_where_HMABTM( $model, $field, NULL, "IS NOT" );
	} 

	private function _greater_HMABTM( $model, $field, $value ) 
	{
		return $this->_where_HMABTM( $model, $field, $value, ">" );
	} 

	private function _greaterThanOrEqual_HMABTM( $model, $field, $value ) 
	{
		return $this->_where_HMABTM( $model, $field, $value, ">=" );
	} 

	private function _less_HMABTM( $model, $field, $value ) 
	{
		return $this->_where_HMABTM( $model, $field, $value, "<" );
	} 

	private function _lessThanOrEqual_HMABTM( $model, $field, $value ) 
	{
		return $this->_where_HMABTM( $model, $field, $value, "<=" );
	} 

	private function _like_HMABTM( $model, $field, $value ) 
	{
		return $this->_where_HMABTM( $model, $field, $value, "LIKE" );
	} 

	private function _notLike_HMABTM( $model, $field, $value ) 
	{
		return $this->_where_HMABTM( $model, $field, $value, "NOT LIKE" );
	} 

	private function _isNull_HMABTM( $model, $field ) 
	{
		return $this->_where_HMABTM( $model, $field, NULL, "IS" );
	} 

	private function _in_HMABTM( $model, $field, $values ) 
	{
		foreach( $values  as $key => $value ) 
		{
			if( is_string($value) ) 
				$values[ $key ] = "'".mysqli_real_escape_string( $this->_dbHandle, $value )."'";
			elseif( is_null($value) ) 
				$values[ $key ] = "NULL";
			elseif( is_bool($value) )
				$values[ $key ] = ($value)?1:0;
		}
		return $this->_where_HMABTM( $model, $field, "(".implode( ', ', $values ).")", "IN" );
	} 

	private function _notIn_HMABTM( $model, $field, $values ) 
	{
		foreach( $values  as $key => $value ) 
		{
			if( is_string($value) ) 
				$values[ $key ] = "'".mysqli_real_escape_string( $this->_dbHandle, $value )."'";
			if( is_null($value) ) 
				$values[ $key ] = "NULL";
			elseif( is_bool($value) )
				$values[ $key ] = ($value)?1:0;
		}
		return $this->_where_HMABTM( $model, $field, "(".implode( ', ', $values ).")", "NOT IN" );
	} 

	private function _where_HMABTM( $model, $field, $value, $operator='=' )  
	{
		if( isset($this->_hasManyAndBelongsToMany) && NULL!==$this->_hMABTM ) 
		{
			if( isset($this->_hasManyAndBelongsToMany[ $model ]) ) 
			{
				if( is_array( $this->_hasManyAndBelongsToMany[ $model ] ) ) 
				{
					if( !isset( $this->_hasManyAndBelongsToMany[ $model ][ 'conds' ] ) ) 
					{
						$this->_hasManyAndBelongsToMany[ $model ][ 'conds' ] = array();
					} 

					array_push( $this->_hasManyAndBelongsToMany[ $model ][ 'conds' ], array(
						"field"		=> $field, 
						"value"		=> $value, 
						"operator"	=> $operator 
					));
				}
			}
		}
		return $this;
	} 

	private function _limit_HMABTM( $model, $numrows=1000 ) 
	{
		if( isset($this->_hasManyAndBelongsToMany) && NULL!==$this->_hMABTM ) 
		{
			if( isset($this->_hasManyAndBelongsToMany[ $model ]) ) 
			{
				if( is_array($this->_hasManyAndBelongsToMany[ $model ]) ) 
				{
					$this->_hasManyAndBelongsToMany[ $model ][ 'num_rows' ] = $numrows;
				}
			}
		}
		
		return $this;
	} 

	private function _reverse_HMABTM( $model, $field="id" ) 
	{
		if( isset($this->_hasManyAndBelongsToMany) && NULL!==$this->_hMABTM ) 
		{
			if( isset($this->_hasManyAndBelongsToMany[ $model ]) ) 
			{
				if( is_array($this->_hasManyAndBelongsToMany[ $model ]) ) 
				{
					$this->_hasManyAndBelongsToMany[ $model ][ 'reverse' ] = $field;
				}
			}
		}
		return $this;
	} 

	private function _hide_Relative_HMABTM( $model ) 
	{
		if( isset($this->_hasManyAndBelongsToMany) && NULL!==$this->_hMABTM ) 
		{
			if( isset($this->_hasManyAndBelongsToMany[ $model ]) ) 
			{
				if( is_array($this->_hasManyAndBelongsToMany[ $model ]) ) 
				{
					$this->_hasManyAndBelongsToMany[ $model ][ 'hide_rel' ] = 1;
				}
			}
		}
		return $this;
	} 

	private function _show_Relative_HMABTM( $model ) 
	{
		if( isset($this->_hasManyAndBelongsToMany) && NULL!==$this->_hMABTM ) 
		{
			if( isset($this->_hasManyAndBelongsToMany[ $model ]) ) 
			{
				if( is_array($this->_hasManyAndBelongsToMany[ $model ]) ) 
				{
					if( isset($this->_hasManyAndBelongsToMany[ $model ][ 'hide_rel' ]) ) 
					{
						unset($this->_hasManyAndBelongsToMany[ $model ][ 'hide_rel' ]); 
					}
				}
			}
		}
		return $this;
	} 

	private function _select_HasOne( $model, $field, $label ) 
	{
		if( isset($this->_hasOne) && NULL!==$this->_hO ) 
		{
			if( isset($this->_hasOne[ $model ]) ) 
			{
				if( is_array($this->_hasOne[ $model ]) ) 
				{
					if( !isset($this->_hasOne[ $model ][ 'describe' ]) ) 
					{
						$this->_hasOne[ $model ][ 'describe' ] = array();
					}

					$field_agrs = array("field"	=> (array)$field);

					if( !is_null($label) ) 
					{
						$field_name = (is_array($field))?$field[ 0 ]:$field;
						$field_agrs[ "label" ] = array($field_name=>$label);
					}
					array_push( $this->_hasOne[ $model ][ 'describe' ], $field_agrs );
				}
			}
		}
		return $this;
	} 

	private function _unselect_HasOne( $model, $fields ) 
	{
		if( isset($this->_hasOne) && NULL!==$this->_hO ) 
		{
			if( isset($this->_hasOne[ $model ]) ) 
			{
				if( is_array($this->_hasOne[ $model ]) ) 
				{
					if( !isset($this->_hasOne[ $model ][ 'undescribe' ]) ) 
					{
						$this->_hasOne[ $model ][ 'undescribe' ] = array();
					}
					$this->_hasOne[ $model ][ 'undescribe' ] = array_merge( $this->_hasOne[ $model ][ 'undescribe' ], (array)$fields );
				}
			}
		}
		return $this;
	} 
	
	private function _unsecure_HasOne( $model, $fields ) 
	{ 
		// ...
	}

	private function _select_HasMany( $model, $field, $label ) 
	{
		if( isset($this->_hasMany) && NULL!==$this->_hM ) 
		{
			if( isset($this->_hasMany[ $model ]) ) 
			{
				if( is_array($this->_hasMany[ $model ]) ) 
				{
					if( !isset($this->_hasMany[ $model ][ 'describe' ]) ) 
					{
						$this->_hasMany[ $model ][ 'describe' ] = array();
					}

					$field_agrs = array("field"	=> (array)$field);

					if( !is_null($label) ) 
					{
						$field_name = (is_array($field))?$field[ 0 ]:$field;
						$field_agrs[ "label" ] = array($field_name=>$label);
					}
					array_push( $this->_hasMany[ $model ][ 'describe' ], $field_agrs );
				}
			}
		}
		return $this;
	} 

	private function _unselect_HasMany( $model, $fields ) 
	{
		if( isset($this->_hasMany) && NULL!==$this->_hM ) 
		{
			if( isset($this->_hasMany[ $model ]) ) 
			{
				if( is_array($this->_hasMany[ $model ]) ) 
				{
					if( !isset($this->_hasMany[ $model ][ 'undescribe' ]) ) 
					{
						$this->_hasMany[ $model ][ 'undescribe' ] = array();
					}
					$this->_hasMany[ $model ][ 'undescribe' ] = array_merge( $this->_hasMany[ $model ][ 'undescribe' ], (array)$fields );
				}
			}
		}
		return $this;
	} 
	
	private function _unsecure_HasMany( $model, $fields ) 
	{
		// ...
	}

	private function _between_HasMany( $model, $field, $start, $end ) 
	{
		if( isset($this->_hasMany) && NULL!==$this->_hM ) 
		{
			if( isset($this->_hasMany[ $model ]) ) 
			{
				if( is_array($this->_hasMany[ $model ]) ) 
				{
					if( !isset($this->_hasMany[ $model ][ 'conds' ]) ) 
					{
						$this->_hasMany[ $model ][ 'conds' ] = array();
					}
					array_push( $this->_hasMany[ $model ][ 'conds' ], array(
						"field"		=> $field, 
						"start"		=> $start, 
						"end"		=> $end, 
						"operator"	=> "BETWEEN" 
					));
				}
			}
		}
		return $this;
	}

	private function _equal_HasMany( $model, $field, $value ) 
	{
		return $this->_where_HasMany( $model, $field, $value );
	} 

	private function _not_HasMany( $model, $field, $value ) 
	{
		return $this->_notEqual_HasMany( $model, $field, $value );
	} 

	private function _notNull_HasMany( $model, $field ) 
	{
		return $this->_isNotNull_HasMany( $model, $field );
	} 

	private function _notEqual_HasMany( $model, $field, $value ) 
	{
		return $this->_where_HasMany( $model, $field, $value, "!=" );
	} 

	private function _is_HasMany( $model, $field, $value ) 
	{
		return $this->_where_HasMany( $model, $field, $value, "IS" );
	}

	private function _isNot_HasMany( $model, $field, $value ) 
	{
		return $this->_where_HasMany( $model, $field, $value, "IS NOT" );
	} 

	private function _isNotNull_HasMany( $model, $field ) 
	{
		return $this->_where_HasMany( $model, $field, NULL, "IS NOT" );
	} 

	private function _greater_HasMany( $model, $field, $value ) 
	{
		return $this->_where_HasMany( $model, $field, $value, ">" );
	} 

	private function _greaterThanOrEqual_HasMany( $model, $field, $value ) 
	{
		return $this->_where_HasMany( $model, $field, $value, ">=" );
	} 

	private function _less_HasMany( $model, $field, $value ) 
	{
		return $this->_where_HasMany( $model, $field, $value, "<" );
	} 

	private function _lessThanOrEqual_HasMany( $model, $field, $value ) 
	{
		return $this->_where_HasMany( $model, $field, $value, ">=" );
	} 

	private function _like_HasMany( $model, $field, $value ) 
	{
		return $this->_where_HasMany( $model, $field, $value, "LIKE" );
	} 

	private function _notLike_HasMany( $model, $field, $value ) 
	{
		return $this->_where_HasMany( $model, $field, $value, "NOT LIKE" );
	}

	private function _isNull_HasMany( $model, $field ) 
	{
		return $this->_where_HasMany( $model, $field, NULL, "IS" );
	} 

	private function _in_HasMany( $model, $field, $values ) 
	{
		foreach( $values  as $key => $value ) 
		{
			if( is_string($value) ) 
				$values[ $key ] = "'".mysqli_real_escape_string( $this->_dbHandle, $value )."'";
			elseif( is_bool($value) )
				$values[ $key ] = ($value)?1:0;
		}
		return $this->_where_HasMany( $model, $field, "(".implode( ', ', $values ).")", "IN" );
	}

	private function _notIn_HasMany( $model, $field, $values ) 
	{
		foreach( $values  as $key => $value ) 
		{
			if( is_string($value) ) 
				$values[ $key ] = "'".mysqli_real_escape_string( $this->_dbHandle, $value )."'";
			elseif( is_bool($value) )
				$values[ $key ] = ($value)?1:0;
		}
		return $this->_where_HasMany( $model, $field, "(".implode( ', ', $values ).")", "NOT IN" );
	} 

	private function _where_HasMany( $model, $field, $value, $operator='=' ) 
	{
		if( isset($this->_hasMany) && NULL!==$this->_hM ) 
		{
			if( isset($this->_hasMany[ $model ]) ) 
			{
				if( is_array($this->_hasMany[ $model ]) ) 
				{
					if( !isset($this->_hasMany[ $model ][ 'conds' ]) ) 
					{
						$this->_hasMany[ $model ][ 'conds' ] = array();
					} 

					array_push( $this->_hasMany[ $model ][ 'conds' ], array(
						"field"		=> $field, 
						"value"		=> $value, 
						"operator"	=> $operator
					));
				}
			}
		}
		return $this;
	} 

	private function _limit_HasMany( $model, $numrows=1000 ) 
	{
		if( isset($this->_hasMany) && NULL!==$this->_hM ) 
		{
			if( isset($this->_hasMany[ $model ]) ) 
			{
				if( is_array($this->_hasMany[ $model ]) ) 
				{
					$this->_hasMany[ $model ][ 'num_rows' ] = $numrows;
				}
			}
		}
		return $this;
	} 

	private function _reverse_HasMany( $model, $field="id" ) 
	{
		if( isset($this->_hasMany) && NULL!==$this->_hM ) 
		{
			if( isset($this->_hasMany[ $model ]) ) 
			{
				if( is_array($this->_hasMany[ $model ]) ) 
				{
					$this->_hasMany[ $model ][ 'reverse' ] = $field;
				}
			}
		}
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
		$this->_hO = NULL;
		return $this;
	} 

	private function _hideHasMany() 
	{
		$this->_hM = NULL;
		return $this;
	}

	private function _hideHMABTM() 
	{
		$this->_hMABTM = NULL;
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

	private function _blindHasOne( $blink_data ) 
	{
		return $this->_blindRelative( 
			$this->_hasOneBlind, 
			$blink_data 
		);
	} 

	private function _blindHasMany( $blink_data ) 
	{
		return $this->_blindRelative( 
			$this->_hasManyBlind, 
			$blink_data 
		);
	} 

	private function _blindHMABTM( $blink_data ) 
	{
		return $this->_blindRelative( 
			$this->_hasManyAndBelongsToManyBlind, 
			$blink_data 
		);
	} 

	private function _blindRelative( &$data_current, $needle_data ) 
	{
		
		if( is_array( $needle_data ) ) 
		{
			foreach( $needle_data as $key => $value ) 
			{
				if( in_array( $value, $data_current ) ) 
				{
					continue;
				} 
				$this->_unblindRelative( $data_current, $value );
			}
		} 
		else 
		{
			foreach( $data_current as $key => $value ) 
			{
				if( $needle_data != $data_current[ $key ] ) 
				{
					continue;
				}
				unset( $data_current[ $key ] );
			}
		} 
		return $this;
	} 

	private function _unblindHasOne( $unblink_data ) 
	{
		return $this->_unblindRelative(
			$this->_hasOneBlind, 
			$unblink_data 
		);
	} 

	private function _unblindHasMany( $unblink_data ) 
	{
		return $this->_unblindRelative(
			$this->_hasManyBlind, 
			$unblink_data 
		); 
	}

	private function _unblindHMABTM( $unblink_data ) 
	{
		return $this->_unblindRelative( 
			$this->_hasManyAndBelongsToManyBlind, 
			$unblink_data 
		);
	}

	private function _unblindRelative( &$data_current, $needle_data ) 
	{
		if( is_array( $needle_data ) ) 
		{
			$data_current = array_merge( $data_current, $needle_data );
		} 
		else 
		{
			array_push( $data_current, $needle_data ); 
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
		global $inflect;
		foreach( $hasOne as $model_child => $alias_child ) 
		{
			if( isset($this->_hasOne[ $model_child ]) && 
				isset($this->_hasOne[ $model_child ][ 'live' ]) ) continue;
			if( is_array($alias_child) ) 
			{
				list( $alias, $key ) = each( $alias_child );
				$alias_key = $key; 
				$alias = explode( '_', $alias );
			} 
			else 
			{
				if( $this->_alias===$alias_child ) 
					$alias_key = $inflect->singularize(strtolower( $model_child )).'_id';
				else
					$alias_key = $alias_child.'_id';
				$alias = explode( '_', $alias_child );
			} 
			foreach( $alias as $key => $value ) 
				$alias[ $key ] = $inflect->pluralize( $value );
			$this->_hasOne[ $model_child ] = array(
				'key'	=> $alias_key, 
				'table'	=> $this->_prefix.implode( '_', $alias ), 
				'live'	=> 1, 
			);
		} 
		return $this;
	}

	protected function _orderHasMany( $hasMany ) 
	{
		global $inflect;
		foreach( $hasMany as $model_child => $alias_child ) 
		{
			if( isset($this->_hasMany[ $model_child ]) && 
				isset($this->_hasMany[ $model_child ][ 'live' ]) ) continue;
			if( is_array($alias_child) ) 
			{
				list( $alias, $key ) = each( $alias_child );
				$alias_key = $key; 
				$alias = explode( '_', $alias );
			} 
			else 
			{
				$alias_key = $this->_alias.'_id';
				$alias = explode( '_', $alias_child );
			}
			foreach( $alias as $key => $value ) 
				$alias[ $key ] = $inflect->pluralize( $value );
			$this->_hasMany[ $model_child ] = array( 
				'key'	=> $alias_key, 
				'table'	=> $this->_prefix.implode( '_', $alias ), 
				'live'	=> 1,
			);
			$this->_hasMany[ $model_child ][ 'live' ] = 1;
		} 
		return $this;
	} 

	protected function _orderHMABTM( $hasMABTM ) 
	{
		global $inflect;
		foreach( $hasMABTM as $model_child => $alias_child ) 
		{
			if( isset($this->_hasManyAndBelongsToMany[ $model_child ]) && 
				isset($this->_hasManyAndBelongsToMany[ $model_child ][ 'live' ]) ) continue;
			$this->_hasManyAndBelongsToMany[ $model_child ] = array();
			if( is_array( $alias_child ) ) 
			{
				if( count( $alias_child )===1 ) 
				{
					list( $alias_child, $foreign_key ) = each( $alias_child ); 
					$table_child = explode( '_', $alias_child ); 
					foreach( $table_child as $key => $value ) 
						$table_child[ $key ] = $inflect->pluralize( $value );
					$data_child = array(
						'key'	=> $foreign_key, 
						'table' => $this->_prefix . implode( '_', $table_child ) 
					);
					$table_child = array( $this->_alias, $alias_child );
					sort($table_child);
					$table_child = explode( '_', implode( '_', $table_child ) );
					foreach( $table_child as $key => $value ) 
						$table_child[ $key ] = $inflect->pluralize( $value );
					$join_child = array( 
						'key'	=> $this->_alias.'_id', 
						'table' => $this->_prefix . implode( '_', $table_child ) 
					);
				} 
				elseif( count( $alias_child )===2 ) 
				{
					list( $alias_data, $foreign_key ) = each( $alias_child ); 
					$table_child = explode( '_', $alias_data ); 
					foreach( $table_child as $key => $value ) 
						$table_child[ $key ] = $inflect->pluralize( $value );
					$data_child = array(
						'key'	=> $foreign_key, 
						'table' => $this->_prefix . implode( '_', $table_child ) 
					);
					list( $alias_join, $foreign_key ) = each( $alias_child ); 
					$table_child = explode( '_', $alias_join ); 
					foreach( $table_child as $key => $value ) 
						$table_child[ $key ] = $inflect->pluralize( $value );
					$join_child = array(
						'key'	=> $foreign_key, 
						'table' => $this->_prefix . implode( '_', $table_child ) 
					);
				}
			} 
			else 
			{
				$table_child = explode( '_', $alias_child ); 
				foreach( $table_child as $key => $value ) 
					$table_child[ $key ] = $inflect->pluralize( $value );
				$data_child = array(
					'key'	=> $alias_child.'_id', 
					'table' => $this->_prefix . implode( '_', $table_child ) 
				); 
				$table_child = array( $this->_alias, $alias_child );
				sort($table_child);
				$table_child = explode( '_', implode( '_', $table_child ) );
				foreach( $table_child as $key => $value ) 
					$table_child[ $key ] = $inflect->pluralize( $value );
				$join_child = array( 
					'key'	=> $this->_alias.'_id', 
					'table' => $this->_prefix . implode( '_', $table_child ) 
				);
			}
			$this->_hasManyAndBelongsToMany[ $model_child ] = array(
				'live'	=> 1, 
				'data'	=> $data_child, 
				'join'	=> $join_child, 
			); 
		}
		return $this;
	}

	private function _setLimit( &$limit ) 
	{
		$this->_limit = $limit = (int) $limit;
		
		if( is_null( $this->_page ) ) 
		{
			$this->_page = 1;
		}
		
		return $this;
	} 

	private function _seek( $position ) 
	{
		$this->_offset = $position; 
		return $this;
	}

	private function _orderBy( $order_by, $order = 'ASC' ) 
	{
		$this->_setOrderBy( $order_by )->_setOrder( $order );
		return $this;
	} 

	private function _groupBy( $field ) 
	{
		$this->_group_by = $field;
		return $this;
	}

	private function _groupWith( $label ) 
	{
		$this->_group_with = $label;
		return $this;
	} 
	
	private function _include( $model ) 
	{
		if(!$this->_unions) 
			$this->_unions = array();
		$this->_unions[] = $model->shareMainQuery( $this->_model );
		return $this;
	}

	/** HOW TO USE MERGE(models) FUNCTION
	 * $this->model->merge([
	 *	'Avatar' 	=> array('media.avatar_id.id', array('id'=>'= 2', 'name'=>'like \'%funny/%\'')), 
	 *	'UserStat' 	=> array('user_stat.id.user_id'),
	 * ])->search();
	 *
	 * If merge model in hasOne, let's use
	 * $this->model->merge(
	 *	'User'
	 * )
	 */
	private function _merge( $models )
	{
		global $inflect;

		$prefix = $this->_retrivePrefix();

		$this->_imerge = NL;

		if( is_string($models) ) 
		{
			if( isset($this->_hasOne[ $models ]) )
			{
				$aliasChild = $this->_hasOne[ $models ];
				$tableChild = $aliasChild[ 'table' ];

				$modelChild = explode( '_', $tableChild ); 
				if( ($modelChild[0].'_')===$prefix ) unset($modelChild[0]);
				$aliasKey = $aliasChild[ 'key' ];
				foreach( $modelChild as $key => $value ) 
				{
					$modelChild[ $key ] = ucfirst( $inflect->singularize( $value ) );
				}
				$modelChild = implode( '', $modelChild );

				$this->_imerge .= "INNER JOIN `" . $tableChild . "` AS `" . $modelChild . "` ON `" . $modelChild . "`.`id` = `" . $this->_model . "`.`" . $aliasKey . "`" . NL;
			}
		} 
		else 
		{
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
		}
		return $this;
	}
	
	private function _shareMainQuery() 
	{
		$remcols = $this->_collection; 
		$this->_collection = '*'; 
		$prefix = $this->_retrivePrefix(); 
		$this->_buildMainQuery( $prefix ); 
		$query = $this->_querySQL; 
		$this->_collection = $remcols; 
		$this->clear(); 
		return substr( $query, 0, strlen($query)-1 ); 
	}

	private function _buildMainQuery( $prefix ) 
	{
		global $inflect;

		$collections = $this->getCollectionString();
		$from = '`'.$this->_table.'` as `'.$this->_model.'` ';
		$conditions = '\'1\'=\'1\' AND ';
		$groupBy = '';
		$groupWith = ''; 
		$orderBy = '';
		$limit = '';
		$hasOne = '';

		if( isset($this->id) ) 
		{
			$conditions .= '`'.$this->_model.'`.`id` = \''.mysqli_real_escape_string( $this->_dbHandle, $this->id ).'\' AND ';
		}
		$conditions .= $this->_extraConditions;
		$conditions = ' WHERE ' . substr( $conditions, 0, -4 );

		if( isset( $this->_group_by ) ) 
		{
			$groupBy .= " GROUP BY `".$this->_model."`.`".$this->_group_by."`";
		} 

		if( isset( $this->_group_with ) ) 
		{
			$groupWith .= " GROUP BY `".$this->_group_with."`";
		} 
		
		if( isset( $this->_order_by ) ) 
		{
			$orderBy .= ' ORDER BY `'.$this->_model.'`.`'.$this->_order_by.'` '.$this->_order;
		}

		if ( isset( $this->_page ) ) 
		{
			if( isset($this->_offset) ) 
				$offset = $this->_offset;
			else
				$offset = ( $this->_page-1 ) * $this->_limit;
			$limit .= ' LIMIT '.$this->_limit.' OFFSET '.$offset;
		}

		if( $this->_hO == 1 && isset( $this->_hasOne ) ) 
		{
			foreach ( $this->_hasOne as $modelChild => $aliasChild ) 
			{
				if( in_array( $modelChild, $this->_hasOneBlind ) ) 
				{
					continue;
				} 
				$aliasKey = $aliasChild[ 'key' ]; 
				$tableChild = $aliasChild[ 'table' ];
				$hasOne .= 'LEFT JOIN `'.$tableChild.'` as `'.$modelChild.'` ';
				$hasOne .= 'ON `'.$this->_model.'`.`'.$aliasKey.'` = `'.$modelChild.'`.`id` ';
			}
		}

		$this->_querySQL = 'SELECT ' . $collections . ' FROM ' . $from . $hasOne . $this->_imerge . $conditions . $groupBy . $groupWith . $orderBy . $limit; 

		if( $this->_unions ) 
		{
			$unionQuery = ' UNION ALL ' . implode( ' UNION ALL ', $this->_unions ); 
			if( $this->_hO == 1 && $hasOne ) 
			{
				$selfQuery = 'SELECT * FROM ' . $from . $conditions; 
				$unionQuery = $selfQuery . $unionQuery;
				$this->_querySQL = 'SELECT ' . $collections . ' FROM ( ' . $unionQuery . ' ) AS `' . $this->_model . '` ' . $hasOne . $groupBy . $groupWith . $orderBy . $limit; 
			}
			else if( false!==strpos($collections, 'COUNT') ) 
			{
				$selfQuery = 'SELECT * FROM ' . $from . $conditions; 
				$unionQuery = $selfQuery . $unionQuery;
				$this->_querySQL = 'SELECT ' . $collections . ' FROM ( ' . $unionQuery . ' ) AS `' . $this->_model . '` ' . $hasOne . $groupBy . $groupWith . $orderBy . $limit; 
			}
			else 
			{
				$this->_querySQL .= $unionQuery; 
			}
		}
	} 

	private function _findData( $id ) 
	{
		return $this->_where( 'id', $id )->_search();
	} 

	private function _find( $id ) 
	{
		return $this->_new()->_setId( $id )->_search();
	}

	private function _execute() 
	{
		if( $this->_hasExe )
			return $this->search();
		return $this;
	} 

	private function _output( $label ) 
	{
		if( is_string( $label ) )
		{
			$this->_collection .= " AS '".$label."' ";
			$this->_hasExe = true; 
		}
		return $this;
	} 

	private function _command( $expresion ) 
	{
		$model = $this->_model;
		if( is_string($expresion) ) 
		{
			$this->_collection .=  ", ".$expresion;
			$this->_hasExe = true; 
		}
		return $this;
	}

	private function _search() 
	{
		global $inflect;
		$commandTable = 'command'; 
		$conditionsChild = '';
		$fromChild = '';
		$prefix = $this->_retrivePrefix();

		$this->_buildMainQuery( $prefix );
		$this->_result = mysqli_query( $this->_dbHandle, $this->_querySQL );
		$result = array();
		$table = array();
		$field = array();
		$tempResults = array();
		$rsNumRows = 0;

		array_push( $this->_querySQLs, $this->_querySQL );
		
		if( $this->_result ) 
		{
			$rsNumRows = mysqli_num_rows( $this->_result );
			if( $rsNumRows > 0 ) 
			{
				$numOfFields = mysqli_num_fields( $this->_result );

				while( $field_info = mysqli_fetch_field($this->_result) )
				{
					$ignoreField = false;
					foreach( $this->_undescrible as $i => $columnName ) 
					{
						if( stripos($columnName, DOT) ) 
						{
							$columnName = explode( '.', $columnName );
							if( "{$field_info->table}.{$field_info->name}" === $columnName[ 0 ] . "." . $columnName [ 1 ] ) 
							{
								$ignoreField = true;
							}
						} 
						else 
						{
							if( $field_info->name === $columnName ) 
							{
								$ignoreField = true;
							}
						}
					} 

					if( !$ignoreField ) 
					{
						if( $field_info->table == EMPTY_CHAR ) 
						{
							if( EMPTY_CHAR!==$this->_model )
								$commandTable = $this->_model;
							array_push( $table, $commandTable );
						}
						else
							array_push( $table, $field_info->table );
						array_push( $field, $field_info->name );
					} 
					else 
					{
						array_push( $table, 0 );
						array_push( $field, 0 );
					}
				}

				while( $row = mysqli_fetch_row( $this->_result ) ) 
				{
					for( $i = 0; $i < $numOfFields; ++$i ) 
					{
						if( $table[ $i ]!==0 && $field[ $i ]!==0 ) 
						{
							if( isset($this->_hasOne[ $table[ $i ] ]) ) 
								if( isset($this->_hasOne[ $table[ $i ] ][ 'undescribe' ]) )
									if( in_array( $field[ $i ], $this->_hasOne[ $table[ $i ] ][ 'undescribe' ] ) ) 
										continue;
							
							$tempResults[ $table[ $i ] ][ $field[ $i ] ] = $row[ $i ];
						}
					}
					if( $this->_hM == 1 && isset( $this->_hasMany ) ) 
					{
						foreach ( $this->_hasMany as $modelChild => $aliasChild ) 
						{
							if( in_array( $modelChild, $this->_hasManyBlind ) ) 
							{
								continue; 
							}

							$queryChild = '';
							$conditionsChild = '';
							$limitChild = NL."LIMIT 1000";
							$orderChild = "";
							$fromChild = '';
							$aliasKey = $aliasChild[ 'key' ];
							$tableChild = $aliasChild[ 'table' ];
							$aliasChild = explode( '_', $aliasChild[ 'table' ] );
							if( ($aliasChild[0].'_')===$prefix ) unset($aliasChild[0]);
							foreach( $aliasChild as $key => $value ) 
								$aliasChild[ $key ] = ucfirst( $inflect->singularize( $value ) );
							$modelAlias = implode( '', $aliasChild );
							$fromChild .= '`'.$tableChild.'` as `'.$modelAlias.'`';
							$conditionsChild .= '`'.$modelAlias.'`.`'.$aliasKey.'` = \''.$tempResults[$this->_model][ID].'\' '.NL;

							if( isset($this->_hasMany[ $modelChild ][ 'conds' ]) ) 
							{
								if( is_array($this->_hasMany[ $modelChild ][ 'conds' ]) ) 
								{
									$conds = $this->_hasMany[ $modelChild ][ 'conds' ];
									foreach( $conds as $cond ) 
									{
										switch( $cond[ 'operator' ] ) 
										{
											case 'BETWEEN':
												if( is_string($cond[ 'start' ]) ) 
													$sql_start_value = "'".mysqli_real_escape_string( $this->_dbHandle, $cond[ 'start' ] )."'";
												elseif( is_bool($cond[ 'start' ]) ) 
													$sql_start_value = ($cond[ 'start' ])?1:0; 
												elseif( is_null($cond[ 'start' ]) ) 
													$sql_start_value = 'NULL';
												else
													$sql_start_value = $cond[ 'start' ];

												if( is_string($cond[ 'end' ]) )
													$sql_end_value = "'".mysqli_real_escape_string( $this->_dbHandle, $cond[ 'end' ] )."'"; 
												elseif( is_bool($cond[ 'end' ]) ) 
													$sql_end_value = ($cond[ 'end' ])?1:0;
												elseif( is_null($cond[ 'end' ]) )
													$sql_end_value = 'NULL';
												else 
													$sql_end_value = $cond[ 'end' ];

												$sql_value = $sql_start_value . " AND " . $sql_end_value;
												break;

											case 'IN': 
											case 'NOT IN': 
												$sql_value = $cond[ 'value' ];
												break; 

											case 'LIKE': 
											case 'NOT LIKE': 
												try 
												{
													if( is_null($cond[ 'value' ]) ) 
														throw new Exception("The value with ".$cond[ 'operator' ]." operator could not be NULL", 1);
													elseif( is_bool($cond[ 'value' ]) ) 
														throw new Exception("The value with ".$cond[ 'operator' ]." operator could not be BOOLEAN", 1);
													else 
														$sql_value = "'%".mysqli_real_escape_string( $this->_dbHandle, $cond[ 'value' ] )."%'";
												} 
												catch( Exception $e ) 
												{
													trace_once( $e );
												}
												break;

											default:
												if( is_string($cond[ 'value' ]) ) 
													$sql_value = "'".mysqli_real_escape_string( $this->_dbHandle, $cond[ 'value' ] )."'";
												elseif( is_null($cond[ 'value' ]) ) 
													$sql_value = 'NULL';
												elseif( is_bool($cond[ 'value' ]) ) 
													$sql_value = ($cond[ 'value' ])?1:0;
												else 
													$sql_value = $cond[ 'value' ];
												break;
										}
										$conditionsChild .= "AND `".$modelAlias."`.`".$cond[ 'field' ]."` ".$cond[ 'operator' ]." ".$sql_value." ".NL;
									}
								}
							} 

							$describeArr_r = array();
							if( isset($this->_hasMany[ $modelChild ][ 'describe' ]) ) 
							{
								$describes = $this->_hasMany[ $modelChild ][ 'describe' ];
								if( is_array($describes) ) 
								{
									foreach( $describes as $describe ) 
									{
										foreach($describe[ 'field' ] as $field_r) 
										{
											$describeSql_r = "`".$modelAlias."`.`".$field_r."`";
											if( isset($describe[ 'label' ]) && isset($describe[ 'label' ][ $field_r ]) )
												$describeSql_r .= " AS '".$describe[ 'label' ][ $field_r ]."'";
											array_push( $describeArr_r, $describeSql_r );
										}
									}
								}
							}
							if( !empty($describeArr_r) ) 
							{
								$includeColume = implode( ', ', $describeArr_r );
							} 
							else 
							{
								$includeColume = '*';
							} 

							if( isset($this->_hasMany[ $modelChild ][ 'num_rows' ]) ) 
							{
								$limitChild = NL."LIMIT ".$this->_hasMany[ $modelChild ][ 'num_rows' ];
							}

							if( isset($this->_hasMany[ $modelChild ][ 'reverse' ]) ) 
							{
								$orderChild = NL."ORDER BY `".$modelAlias."`.`".mysqli_real_escape_string($this->_dbHandle, $this->_hasMany[ $modelChild ][ 'reverse' ])."` DESC";
							}

							$queryChild =  'SELECT '.$includeColume.' FROM '.$fromChild.' WHERE '.$conditionsChild.$orderChild.$limitChild;	
							$resultChild = mysqli_query( $this->_dbHandle, $queryChild );
							
							$tableChild = array();
							$fieldChild = array();
							$temp_results_child = array();
							$results_child = array();
							array_push( $this->_querySQLs, $queryChild );

							if( $resultChild ) 
							{
								if( mysqli_num_rows($resultChild) > 0 ) 
								{
									$undescribes = NULL;
									if( isset($this->_hasMany[ $modelChild ][ 'undescribe' ]) ) 
									{
										$undescribes = $this->_hasMany[ $modelChild ][ 'undescribe' ];
									}
									
									$numOfFieldsChild = mysqli_num_fields( $resultChild );

									while( $field_info = mysqli_fetch_field($resultChild) ) 
									{
										if( NULL!==$undescribes && in_array($field_info->name, $undescribes) ) 
										{
											array_push( $fieldChild, 0 );
										}
										else
										{
											array_push( $fieldChild, $field_info->name );
										}
										array_push( $tableChild, $field_info->table );
									}

									while( $rowChild = mysqli_fetch_row($resultChild) ) 
									{
										for ($j = 0;$j < $numOfFieldsChild; ++$j) 
										{
											if($fieldChild[$j])
												$temp_results_child[$tableChild[$j]][$fieldChild[$j]] = $rowChild[$j];
										}
										array_push( $results_child, $temp_results_child );
									}
								}
								
								if(!empty( $results_child ))
									$tempResults[ $modelChild ] = $results_child;
								else 
									unset( $tempResults[ $modelChild ] );
								
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
							$limitChild = NL."LIMIT 1000";
							$orderChild = "";
							$fromChild = '';

							$cacheChild = $aliasChild;
							
							// $joinKey = strtolower($inflect->singularize($aliasChild)).'_id';
							// if( isset( $cacheChild[ 'join' ] ) ) 
							// {
							// 	$tableModel = $this->_modelSort( $cacheChild['data']['table'] );
							// 	$joinTable = $prefix . $this->_tableSort( $cacheChild['join']['table'] );
							// 	$joinModel = $this->_modelSort( $cacheChild['join']['table'] );
							// 	$aliasKey = $cacheChild['join']['key'];
							// } 
							// else 
							// {
							// 	$pluralAliasTable = strtolower($this->_alias);
							// 	$pluralAliasChild = strtolower($aliasChild);
							// 	$sortTables = explode( '_', $pluralAliasTable.'_'.$pluralAliasChild );
							// 	sort($sortTables);
							// 	foreach( $sortTables as $key => $value ) 
							// 	{
									
							// 	}
							// 	$joinTable = $prefix . implode('_',$sortTables);
							// 	$tableModel = $this->_modelSort( $aliasChild );
							// 	$tableChild = $prefix . $this->_tableSort( $aliasChild );
							// 	$sortAliases = array( $this->_model, $tableModel );
							// 	sort($sortAliases);
							// 	$joinModel = implode('', $sortAliases);
							// 	$aliasKey = $this->_alias.'_id';
							// }
							
							$tableChild = $cacheChild['data']['table'];
							$aliasChild = explode( '_', $tableChild );
							if( ($aliasChild[0].'_')===$prefix ) unset($aliasChild[0]);
							foreach( $aliasChild as $key => $value )
								$aliasChild[ $key ] = ucfirst( $inflect->singularize( $value ) ); 
							$tableModel = implode( '', $aliasChild ); 
							$joinTable = $cacheChild['join']['table'];
							$joinAlias = explode( '_', $joinTable );
							if( ($joinAlias[0].'_')===$prefix ) unset($joinAlias[0]);
							foreach( $joinAlias as $key => $value )
								$joinAlias[ $key ] = ucfirst( $inflect->singularize( $value ) ); 
							$joinModel = implode( '', $joinAlias ); 
							$fromChild .= '`'.$tableChild.'` as `'.$tableModel.'`,';
							$fromChild .= '`'.$joinTable.'` as `'.$joinModel.'`,';
							$conditionsChild .= "`".$joinModel."`.`".$cacheChild['data']['key']."` = `".$tableModel."`.`id`"." ".NL;
							$conditionsChild .= "AND `".$joinModel."`.`".$cacheChild['join']['key']."` = '".$tempResults[$this->_model]['id']."'"." ".NL;

							if( isset($this->_hasManyAndBelongsToMany[ $modelChild ][ 'conds' ]) ) 
							{
								if( is_array($this->_hasManyAndBelongsToMany[ $modelChild ][ 'conds' ] ) ) 
								{
									$conds = $this->_hasManyAndBelongsToMany[ $modelChild ][ 'conds' ];
									foreach( $conds as $cond ) 
									{
										switch( $cond[ 'operator' ] ) 
										{
											case 'BETWEEN':
												if( is_string($cond[ 'start' ]) ) 
													$sql_start_value = "'".mysqli_real_escape_string( $this->_dbHandle, $cond[ 'start' ] )."'";
												elseif( is_bool($cond[ 'start' ]) ) 
													$sql_start_value = ($cond[ 'start' ])?1:0; 
												elseif( is_null($cond[ 'start' ]) ) 
													$sql_start_value = 'NULL';
												else
													$sql_start_value = $cond[ 'start' ];

												if( is_string($cond[ 'end' ]) )
													$sql_end_value = "'".mysqli_real_escape_string( $this->_dbHandle, $cond[ 'end' ] )."'"; 
												elseif( is_bool($cond[ 'end' ]) ) 
													$sql_end_value = ($cond[ 'end' ])?1:0;
												elseif( is_null($cond[ 'end' ]) )
													$sql_end_value = 'NULL';
												else 
													$sql_end_value = $cond[ 'end' ];

												$sql_value = $sql_start_value . " AND " . $sql_end_value;
												break;

											case 'IN': 
											case 'NOT IN': 
												$sql_value = $cond[ 'value' ];
												break; 

											case 'LIKE': 
											case 'NOT LIKE': 
												try 
												{
													if( is_null($cond[ 'value' ]) ) 
														throw new Exception("The value with ".$cond[ 'operator' ]." operator could not be NULL", 1);
													elseif( is_bool($cond[ 'value' ]) ) 
														throw new Exception("The value with ".$cond[ 'operator' ]." operator could not be BOOLEAN", 1);
													else 
														$sql_value = "'%".mysqli_real_escape_string( $this->_dbHandle, $cond[ 'value' ] )."%'";
												} 
												catch( Exception $e ) 
												{
													trace_once( $e );
												}
												break;

											default:
												if( is_string($cond[ 'value' ]) ) 
													$sql_value = "'".mysqli_real_escape_string( $this->_dbHandle, $cond[ 'value' ] )."'";
												elseif( is_null($cond[ 'value' ]) ) 
													$sql_value = 'NULL';
												elseif( is_bool($cond[ 'value' ]) ) 
													$sql_value = ($cond[ 'value' ])?1:0;
												else 
													$sql_value = $cond[ 'value' ];
												break;
										}
										$conditionsChild .= "AND `".$tableModel."`.`".$cond[ 'field' ]."` ".$cond[ 'operator' ]." ".$sql_value." ".NL;
									}
								}
							} 

							$describeArr_r = array();
							if( isset($this->_hasManyAndBelongsToMany[ $modelChild ][ 'describe' ]) ) 
							{
								$describes = $this->_hasManyAndBelongsToMany[ $modelChild ][ 'describe' ];
								if( is_array($describes) ) 
								{
									foreach( $describes as $describe ) 
									{
										foreach($describe[ 'field' ] as $field_r) 
										{
											$describeSql_r = "`".$tableModel."`.`".$field_r."`";
											if( isset($describe[ 'label' ]) && isset($describe[ 'label' ][ $field_r ]) )
												$describeSql_r .= " AS '".$describe[ 'label' ][ $field_r ]."'";
											array_push( $describeArr_r, $describeSql_r );
										}
									}
								}
							}
							if( !empty($describeArr_r) ) 
							{
								$includeColume = implode( ', ', $describeArr_r );
							} 
							else 
							{
								$includeColume = '*';
							} 

							if( isset($this->_hasManyAndBelongsToMany[ $modelChild ][ 'num_rows' ]) ) 
							{
								$limitChild = NL."LIMIT ".$this->_hasManyAndBelongsToMany[ $modelChild ][ 'num_rows' ];
							} 

							if( isset($this->_hasManyAndBelongsToMany[ $modelChild ][ 'reverse' ]) ) 
							{
								$orderChild = NL."ORDER BY `".$tableModel."`.`".mysqli_real_escape_string( $this->_dbHandle, $this->_hasManyAndBelongsToMany[ $modelChild ][ 'reverse' ])."` DESC";
							} 

							$fromChild = substr($fromChild,0,-1);
							$queryChild =  'SELECT '.$includeColume.' FROM '.$fromChild.' WHERE '.$conditionsChild.$orderChild.$limitChild;
							$resultChild = mysqli_query( $this->_dbHandle, $queryChild );
							$tableChild = array();
							$fieldChild = array();
							$temp_results_child = array();
							$results_child = array();
							array_push( $this->_querySQLs, $queryChild );
							
							if( $resultChild ) 
							{
								if ( mysqli_num_rows( $resultChild ) > 0 ) 
								{
									$undescribes = NULL;
									if( isset($this->_hasManyAndBelongsToMany[ $modelChild ][ 'undescribe' ]) ) 
									{
										$undescribes = $this->_hasManyAndBelongsToMany[ $modelChild ][ 'undescribe' ];
									}

									$numOfFieldsChild = mysqli_num_fields( $resultChild );

									while( $field_info = mysqli_fetch_field($resultChild) ) 
									{
										if( NULL!==$undescribes && in_array($field_info->name, $undescribes) ) 
										{
											array_push( $fieldChild, 0 );
										}
										else 
										{
											array_push( $fieldChild, $field_info->name );
										}
										array_push( $tableChild, $field_info->table );
									}

									while ( $rowChild = mysqli_fetch_row( $resultChild ) ) 
									{
										for ( $j = 0;$j < $numOfFieldsChild; ++$j ) 
										{
											if( isset($this->_hasManyAndBelongsToMany[ $modelChild ][ 'hide_rel' ]) ) 
												$showRelative = $joinModel !== $tableChild[$j];
											else
												$showRelative = true;

											if($fieldChild[$j] && $showRelative) 
											{
												$temp_results_child[$tableChild[$j]][$fieldChild[$j]] = $rowChild[$j];
											}
										}
										array_push( $results_child,$temp_results_child );
									}
								}
								
								if( !empty($results_child) ) 
									$tempResults[ $modelChild ] = $results_child; 
								else 
									unset( $tempResults[ $modelChild ] );

								mysqli_free_result( $resultChild );
							}
						}
					}
					array_push( $result,$tempResults );
				}
			} 	
			mysqli_free_result( $this->_result );
		}
		$this->clear();
		// Make eloquent item.
		if( $this->id != NULL && $rsNumRows === 1 ) 
		{
			$this->_setData( $result[0][$this->_model] );
			if( $this->_hO || $this->_hM || $this->_hMABTM ) 
				return $result[0]; 
			else 
				return $this;	// Eloquent item. 
		} 
		else if( strpos($this->_collection, 'COUNT') !== false ) 
		{
			return (int)each($result[0][$commandTable])['value'];
		} 
		return $result;
	}

	/** Custom SQL Query **/ 

	private function _custom( $query ) 
	{
		global $inflect;

		$this->_result = mysqli_query( $this->_dbHandle, $query );
		$this->_querySQL = $query;
		array_push( $this->_querySQLs, $query );
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
		return $result;
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
			$this->$field = NULL;
		}
	}

	/** Delete an Object **/

	private function _delete( $id=NULL ) 
	{
		$cond_clause = ' WHERE 1 AND '.preg_replace("/`".$this->_model."`./", '', $this->_extraConditions);
		$limit_clause = '';

		if( isset($this->_limit) ) 
		{
			$limit_clause = 'LIMIT '.$this->_limit.' ';
		}
		
		if( NULL===$id ) 
		{
			if( isset($this->id) ) 
			{
				$id = $this->id;
			}
		}

		if( $id ) 
		{
			if( is_string($id) ) 
			{
				$id = "'".mysqli_real_escape_string( $this->_dbHandle, $id )."'";
				$cond_clause .= '`id`='.$id.' AND ';
			} 
			elseif( is_array($id) )
			{
				$id = mysqli_real_escape_string( $this->_dbHandle, implode(comma, $id) );
				$cond_clause .= '`id` IN ('.$id.') AND ';
			}
		}
		
		$cond_clause = substr( $cond_clause, 0, -4 ); 
		if( method_exists($this, 'down') ) 
			$this->down();
		$query = 'DELETE FROM `'.$this->_table.'`'.$cond_clause.$limit_clause; 
		$this->_result = mysqli_query( $this->_dbHandle, $query ); 
		if( method_exists($this, 'ondown') )
			$this->ondown(); 
		$this->_querySQL = $query;
		array_push( $this->_querySQLs, $query );
		$this->clear(); 
		if ( $this->_result == 0 ) 
		{
			/** Error Generation **/
			return -1;
		} 
	}

	/** Saves an Object i.e. Updates/Inserts Query **/
	private function _update( $data=NULL ) 
	{
		$this->_updates = "d4adf8b7f2ac";
		return $this->_save( $data );
	}

	private function _save( $data=NULL ) 
	{
		$save_all = NULL;
		$fix_id = NULL;
		$query = ''; 
		$result = NULL;
		
		if( isset($this->_updates) && $this->_updates==="d4adf8b7f2ac" ) 
		{
			$save_all = $this->_updates;
			unset($this->_updates);
		} 
		
		if(is_array($data))
			if( array_key_exists('id', $data) ) 
			{
				$this->id = $data['id']; 
			}

		if( NULL!==$save_all || $this->_extraConditions || isset($this->id) ) 
		{
			$cond_clause = 'WHERE 1 AND '.$this->_extraConditions;
			$limit_clause = '';
			$updates = EMPTY_CHAR;

			if( isset($this->_limit) ) 
			{
				$limit_clause = 'LIMIT '.$this->_limit.' ';
			}
			
			if( NULL!==$data ) 
			{
				$this->_setData( $data );
			} 
			
			if(method_exists($this, 'ride')) 
			{
				$this->_ride = $this->ride();
			}

			foreach ( $this->_describe as $field ) 
			{
				if(isset($this->timestamp) && is_array($this->timestamp)) 
					if( in_array($field, $this->timestamp) ) 
					{
						$dt = new \Datetime(); 
						$this->$field = $dt->format('Y-m-d H:i:s');
					} 
				
				if( is_array($this->_ride) ) 
					if( array_key_exists($field, $this->_ride) ) 
						$this->$field = $this->_ride[$field]; 
				
				if( $field == "id" ) continue; 
				if( !isset($this->$field) ) continue;
				if ( $this->$field || is_string($this->$field) || is_numeric($this->$field) ) 
				{
					$updates .= '`'.$this->_model.'`.`'.$field.'` = \''.mysqli_real_escape_string( $this->_dbHandle, $this->$field ).'\',';
				} 
			}

			if( $updates == EMPTY_CHAR ) 
				return $this;

			$updates = substr( $updates, 0, -1 ).' '; 

			if( isset($this->id) ) 
			{
				$cond_clause .= '`'.$this->_model.'`.`id`=\''.mysqli_real_escape_string( $this->_dbHandle, $this->id ).'\' ';
			} 
			else 
			{
				$cond_clause = substr( $cond_clause, 0, -4 );
			}

			$query = 'UPDATE `'.$this->_table.'` AS `'.$this->_model.'` SET '.$updates.$cond_clause.$limit_clause; 
			$this->_result = mysqli_query( $this->_dbHandle, $query );
			$fix_id = $this->id;
		} 
		else 
		{
			$fields = '';
			$values = '';
			
			if( NULL!==$data ) 
			{
				$this->_setData( $data );
			}
			
			if(method_exists($this, 'boot')) 
			{
				$this->_boot = $this->boot();
			}
			
			foreach ($this->_describe as $field ) 
			{
				if( is_array($this->_boot) ) 
					if( array_key_exists($field, $this->_boot) ) 
						$this->$field = $this->_boot[$field]; 
				
				if ( $this->$field || is_string( $this->$field ) || is_numeric( $this->$field ) ) 
				{
					$fields .= '`'.$field.'`,';
					$values .= '\''.mysqli_real_escape_string( $this->_dbHandle, $this->$field ).'\',';
				}
			}
			$values = substr( $values, 0, -1 );
			$fields = substr( $fields, 0, -1 );
			
			$query = 'INSERT INTO '.$this->_table.' ('.$fields.') VALUES ('.$values.')'; 
			$this->_result = mysqli_query( $this->_dbHandle, $query ); 
		} 

		if ( $this->_result == 0 ) 
		{
			/** Error Generation **/
			$result = $this->_getError();
		} 
		else 
		{
			if( is_null( $fix_id ) ) 
			{
				// Reserve the id from insert.
				$this->id = mysqli_insert_id( $this->_dbHandle ); 
				if( method_exists($this, 'onboot') ) 
					$this->onboot(); 
				$result = array( 'id'=> $this->id );
			} 
			else 
			{
				if( method_exists($this, 'onride') ) 
					$this->onride(); 
				$result = array( 'id'=> $fix_id );
			} 
			
		} 
		$this->_querySQL = $query; 
		array_push( $this->_querySQLs, $query ); 
		$this->clear();
		
		return $result;
	}

	/** Clear All Variables **/

	private function _clear( $deep=false ) 
	{
		// Be keep to forward the counting
		if( $deep ) 
		{
			foreach( $this->_describe as $field ) 
			{
				$this->$field = NULL;
			}
			
			if( isset($this->id) ) 
				$this->id = NULL;
			
			$this->_querySQL = NULL; 
			$this->_querySQLs = array();
			$this->_limit = NULL;
			$this->_offset = NULL;

			// Be store to vertical processing.
			$this->_order_by = NULL;			
			$this->_order = NULL;
		}

		$this->_hO = NULL;
		$this->_hM = NULL;
		$this->_hMABTM = NULL;
		$this->_page = NULL;
		$this->_imerge = NULL;
		$this->_undescrible = array();
		$this->_extraConditions = EMPTY_CHAR;

		return $this;
	} 
	
	/** Pagination */ 
	private function _paginate( $options ) 
	{
		// Set default current page.
		if( isset($options['page']) ) 
		{
			$page = $options['page']; 
			$this->_setPage( $page ); 
		}
		elseif( is_null($this->_page) ) 
		{
			$page = 1; 
			$this->_setPage( $page ); 
		} 
		else 
		{
			$page = $this->_page;
		}
		
		// Set 1000 default limit get rows.
		if( isset($options['limit']) ) 
		{
			$limit = $options['limit']; 
			$this->_setLimit( $limit );
		}
		elseif( is_null($this->_limit) ) 
		{
			$limit = 1000; 
			$this->_setLimit( $limit ); 
		} 
		else 
		{
			$limit = $this->_limit; 
		} 
			
		$data = $this->_search(); 
		$total = $this->_total(); 
		$pages = (int) ceil( $total/$limit ); 
		
		return array(
			'pages'	=> $pages, 
			'total' => $total, 
			'limit' => $limit, 
			'page'	=> $page, 
			'data'	=> $data,
		);
	}

	/** Pagination Count **/
	private function _totalPages() 
	{
		if ( $this->_querySQL && $this->_limit ) 
		{
			$limit = $this->_limit; 
			$count = $this->_total(); 
			return (int) ceil( $count/$limit ); 
		} 
		return 0;
	} 
	
	/** count total record for page */
	private function _total() 
	{
		if ( $this->_querySQL ) 
		{
			if( $this->_limit ) 
				$pattern = '/SELECT (.*?) FROM (.*)LIMIT(.*)/i';
			else
				$pattern = '/SELECT (.*?) FROM (.*)/i'; 
			$replacement = 'SELECT COUNT(*) AS `total` FROM $2';
			$countQuery = preg_replace( $pattern, $replacement, $this->_querySQL );
			$this->_result = mysqli_query( $this->_dbHandle, $countQuery ); 
			$this->_clear( true );
			if( $this->_result ) 
			{
				$result = mysqli_fetch_assoc( $this->_result ); 
				return (int)$result['total'];
			}
			else return 0; 
		} 
		else 
		{
			/* Error Generation Code Here */
			$this->_buildMainQuery( $this->_retrivePrefix() );
			return $this->_total();
		}
	} 
	
	/*** Count the number of rows. */
	private function _count() 
	{
		return $this->_select('count')->_search();
	}
	
	/** Set id for model */
	private function _setId( $id=NULL ) 
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
		if( is_array($data) ) 
		{
			foreach( $data as $key => $value ) 
				$this->{$key} = $value;
			return $this;
		}
		elseif( is_string($data) && !is_null($value) ) 
		{
			$this->{$data} = $value;
		}
		return $this;
	} 

	private function _setAliasName( $value ) 
	{
		if( $this->setTable() == MODEL_SFREE && _hasBase() ) 
			$this->_alias = $value;
		return $this;
	}
	
	/** Set table name */ 
	private function _setModelName( $value ) 
	{
		if( $this->setTable() == MODEL_SFREE && _hasBase() ) 
			$this->_model = $value; 
		return $this;
	}
	
	/** Set table name */ 
	private function _setTableName( $value ) 
	{
		if( $this->setTable() == MODEL_SFREE && _hasBase() ) 
		{
			$prefix = $this->_getPrefix();
			if( !is_null( $prefix ) && $prefix != '' ) 
				$this->_table = $prefix . $value;
			else 
				$this->_table = $value; 
			$this->_startConn();
		}
		return $this;
	} 

	protected function _retrivePrefix() 
	{
		global $inflect;
		if( !is_null($this->_prefix) ) 
			return $this->_getPrefix(); 
		$pluralAliasTable = strtolower( $inflect->pluralize( $this->_alias ) );
		$this->_prefix = str_replace( $pluralAliasTable, '', $this->_table );
	}
	
	/** Get Lasted Data */
	private function _getLastedData() 
	{
		$oneRst = 1; 
		$result = $this->_new()->_setLimit( $oneRst )->_orderBy( 'id', 'desc' )->_search(); 
		if( !empty($result) ) 
			return $result[0]; 
		else 
			return NULL;
	} 
	
	/** Get row data */
	private function _getRowData( $id ) 
	{
		return $this->_new()->_getData($id)[$this->_model];
	}
	
	/** Get data */
	private function _getData( $id = NULL ) 
	{
		if( NULL!==$id ) 
		{
			$oneRst = 1; 
			$cache = NULL;
			if( $this->id != $id ) 
			{
				$cache = $this->_toarray()[$this->_model];
				$this->_new();
				$this->_equal('id', $id); 
			} 
			$result = $this->_setLimit( $oneRst )->_search(); 
			if( $cache ) 
				$this->_setData( $cache ); 
			if( !empty($result) ) 
				return $result[0]; 
			else 
				return NULL;
		} 
		else 
		{
			$result = $this->_search(); 
			if( $this->id ) 
			{
				if( !empty($result) )
					return $result->_toarray(); 
				else 
					return NULL;
			}
			return (!empty($result))?$result:NULL;
		} 
	}
	
	/** Get Array Row Data */ 
	private function _toarray() 
	{
		$model = $this->_model;
		$arrRst = array($model=>[]);
		foreach( $this->_describe as $field ) 
			$arrRst[$model][$field] = $this->$field; 
		return $arrRst;
	}
	
	/** Get database list */
	private function _dbList() 
	{
		global $configs;
		$result = $this->query( 'SELECT table_name as `table_name` FROM information_schema.tables where table_schema="' . $configs['DATASOURCE']['DATABASE'] . '"' );
		return $result;
	}
	
	/** Increament */
	private function _setIncreament( $start ) 
	{
		$this->query( 'ALTER TABLE `' . $this->_table . '`  AUTO_INCREMENT=' . $start );
		return $this;
	}
	
	/** Max of field */
	private function _max( $field ) 
	{
		$result = $this->query( "SELECT MAX(`" . $field . "`) AS `" . $field . "` FROM `" . $this->_table ."` AS `" . $this->_model . "`");
		if( is_null($result) ) 
			return NULL;
		list( $key, $value ) = each( $result[0] );
		return (int) $value[$field];
	}

	/**
	 * Get error string 
	 */
	private function _getError() 
	{
		return array(
			'error_msg'	=>mysqli_error( $this->_dbHandle ), 
			'error_no'	=>mysqli_errno( $this->_dbHandle ) 
		); 
	} 
	
	/**
	 * Generation Random A String
	 */
	private function _genRandString( $max_len = 10 ) 
	{
		$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ$!';
		$output = '';
		$char_len = strlen($chars);
		for ($i = 0; $i < $max_len; $i++) 
		{
			$output .= $chars[rand(0, $char_len - 1)];
		}
		return $output;
	}
}