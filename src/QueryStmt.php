<?php
namespace Zuuda; 

use Exception;
use Zuuda\Error;

ndefine( 'DEF_PKEY',				'id' ); 
ndefine( 'mcbm_order_import_once',	'__orderImport' );
ndefine( 'mcbm_order_import_all', 	'__orderImportAll' );
ndefine( 'mcbm_order_merge', 		'__orderMerge' );
ndefine( 'mcbm_order_merge_left', 	'__orderMergeLeft' );
ndefine( 'mcbm_order_merge_right', 	'__orderMergeRight' );
ndefine( 'mcbm_order_has_one', 		'__orderHasOne' );
ndefine( 'mcbm_rename_has_one', 	'__renameHasOne' );
ndefine( 'mcbm_order_has_many',		'__orderHasMany' );
ndefine( 'mcbm_rename_has_many',	'__renameHasMany' );
ndefine( 'mcbm_order_has_mabtm',	'__orderHasMABTM' );
ndefine( 'mcbm_rename_has_mabtm',	'__renameHasMABTM' );
ndefine( 'mcbm_show_has_one',		'__showHasOne' );
ndefine( 'mcbm_show_has_many',		'__showHasMany' );
ndefine( 'mcbm_show_has_mabtm',		'__showHasManyAndBelongsToMany' );
ndefine( 'mcbm_hide_has_one',		'__hideHasOne' );
ndefine( 'mcbm_hide_has_many',		'__hideHasMany' );
ndefine( 'mcbm_hide_has_mabtm',		'__hideHasManyAndBelongsToMany' );
ndefine( 'mcbm_detach_model',		'__detach_model' ); 
ndefine( 'mcbm_view',				'__view' );
ndefine( 'mcbm_proc',				'__proc' );
ndefine( 'mcbm_func',				'__func' );
ndefine( 'mcbm_transaction',		'__transaction' );
ndefine( 'mcbm_commit',				'__commit' );
ndefine( 'mcbm_rollback',			'__rollback' );
ndefine( 'mcbm_checkpoint',			'__checkpoint' );
ndefine( 'mcbm_release',			'__release' );
ndefine( 'mcbm_search',				'__search' );
ndefine( 'mcbm_custom',				'__custom' );
ndefine( 'mcbm_findid',				'__find' );
ndefine( 'mcbm_item',				'__item' );
ndefine( 'mcbm_first',				'__first' );
ndefine( 'mcbm_last',				'__last' );
ndefine( 'mcbm_entity',				'__entity' );
ndefine( 'mcbm_role',				'__role' );
ndefine( 'mcbm_unit',				'__unit' );
ndefine( 'mcbm_paginate',			'__paginate' );
ndefine( 'mcbm_insert',				'__insert' );
ndefine( 'mcbm_delete',				'__delete' );
ndefine( 'mcbm_save',				'__save' );
ndefine( 'mcbm_total_pages',		'__totalPages' );
ndefine( 'mcbm_total',				'__total' );
ndefine( 'mcbm_count',				'__count' );
ndefine( 'mcbm_distinct',			'__distinct' );
ndefine( 'mcbm_sum',				'__sum' );
ndefine( 'mcbm_avg',				'__avg' );
ndefine( 'mcbm_max',				'__max' );
ndefine( 'mcbm_min',				'__min' );
ndefine( 'mcbm_implode',			'__implode' );
ndefine( 'mcbm_length',				'__length' );
ndefine( 'mcbm_db_list',			'__dbList' );
ndefine( 'mcbm_row',				'__row' );
ndefine( 'mcbm_set_page',			'__setPage' );
ndefine( 'mcbm_set_limit',			'__setLimit' );
ndefine( 'mcbm_bound',				'__bound' );
ndefine( 'mcbm_prefix',				'__prefix' );
ndefine( 'mcbm_affected',			'__affected' );
ndefine( 'mcbm_close',				'__close' );
ndefine( 'mhd', 					'data' );
ndefine( 'mhj', 					'join' );

class QueryStmt 
{
	
	private static $this = '\Zuuda\QueryStmt'; 
	protected $_dbHandle; 
	protected $_primaryKey					= DEF_PKEY; 
	protected $_flagIsolate					= false; 
	protected $_flagCheckpoint				= false; 
	protected $_querySQL;
	protected $_querySQLs 					= array(); 
	protected $_flagHasExe 					= false;
	protected $_propPrefix					= EMPTY_CHAR;
	protected $_propModel					= EMPTY_CHAR;
	protected $_propAlias					= EMPTY_CHAR; 
	protected $_propTable					= EMPTY_CHAR;
	protected $_propCollection				= EMPTY_CHAR; 
	protected $_propDatabase				= EMPTY_CHAR; 
	protected $_propUnits					= 0;
	protected $_propUnitOrigin;
	protected $_propsDescribe 				= array(); 
	protected $_propsPersistentUndescribe 	= array(); 
	protected $_propsUndescribe 		  	= array(); 
	protected $_propsCond 				  	= array(); 
	protected $_propsCondEx					= array(); 
	protected $_propsCondOr 				= array(); 
	protected $_propsCondOn 				= array();
	protected $_propsCondCmd 				= array();
	protected $_propsOrder					= array();
	protected $_propsGroupBy				= array();
	protected $_propPage;
	protected $_propLimit;
	protected $_propOffset					= 0;
	protected $_eventBoot;
	protected $_eventOnBoot;
	protected $_eventRide;
	protected $_eventOnRide;
	protected $_eventDown;
	protected $_eventOnDown;
	protected $_propForeignKey;
	protected $_propAliasKey;
	protected $_propAliasModel; 
	protected $_flagHasOne 					= false; 
	protected $_flagHasMany 				= false; 
	protected $_flagHasMABTM 				= false; 
	protected $_propsImport					= array();
	protected $_propsImportAll				= array();
	protected $_propsMerge					= array(); 
	protected $_propsMergeLeft				= array(); 
	protected $_propsMergeRight				= array(); 
	protected $_propsHasOne 				= array(); 
	protected $_propsHasMany 				= array(); 
	protected $_propsHasMABTM 				= array(); 
	protected $_propsRole	 				= array(); 
	protected $_propsDeathMdl				= array(); 
	
	final public function GetPrefix() { return $this->_propPrefix; }
	final public function GetPrimaryKey() { return $this->_primaryKey; }
	final public function GetModel() { return $this->_propModel; } 
	final public function GetTable() { return $this->_propTable; } 
	final public function GetAlias() { return $this->_propAlias; } 
	final public function GetUnits() { return $this->_propUnits; }
	final public function GetModelName() { return $this->GetModel(); }
	final public function GetTableName() { return $this->GetTable(); }
	final public function GetAliasName() { return $this->GetAlias(); }
	final public function GetUnitsSize() { return $this->GetUnits(); }
	final public function Set() { return $this->__setData( func_get_args(), func_num_args(), __FUNCTION__ ); }
	final public function Assign() { return $this->__setData( func_get_args(), func_num_args(), __FUNCTION__ ); }
	final public function Require( $model ) { return $this->__require( $model ); } 
	final public function SetPrefix( $value ) { return $this->__setPrefix( $value ); }
	final public function SetModel( $value ) { return $this->__setModelName( $value ); }
	final public function SetAlias( $value ) { return $this->__setAliasName( $value ); }
	final public function SetTable( $value ) { return $this->__setTableName( $value ); }
	final public function SetModelName( $value ) { return $this->__setModelName( $value ); }
	final public function SetAliasName( $value ) { return $this->__setAliasName( $value ); }
	final public function SetTableName( $value ) { return $this->__setTableName( $value ); }
	final public function SetUnitsSize( $value ) { return $this->__setUnitsSize( $value ); }
	final public function Select() { return $this->__bound( func_get_args(), func_num_args() ); }
	final public function Grab() { return $this->__bound( func_get_args(), func_num_args() ); }
	final public function Bound() { return $this->__bound( func_get_args(), func_num_args() ); }
	final public function Unselect() { return $this->__unbound( func_get_args(), func_num_args() ); }
	final public function Ungrab() { return $this->__unbound( func_get_args(), func_num_args() ); }
	final public function Unbound() { return $this->__unbound( func_get_args(), func_num_args() ); }
	final public function Secure() { return $this->__secure( func_get_args(), func_num_args() ); }
	final public function Unsecure() { return $this->__unsecure( func_get_args(), func_num_args() ); } 
	final public function Between() { return $this->__between( func_get_args(), func_num_args() ); }
	final public function Equal() { return $this->__equal( func_get_args(), func_num_args() ); }
	final public function Eq() { return $this->__equal( func_get_args(), func_num_args() ); }
	final public function __() { return $this->__equal( func_get_args(), func_num_args() ); }
	final public function Greater() { return $this->__greaterThan( func_get_args(), func_num_args() ); } 
	final public function Gt() { return $this->__greaterThan( func_get_args(), func_num_args() ); } 
	final public function GreaterThan() { return $this->__greaterThan( func_get_args(), func_num_args() ); } 
	final public function GreaterThanOrEqual() { return $this->__greaterThanOrEqual( func_get_args(), func_num_args() ); } 
	final public function Gteq() { return $this->__greaterThanOrEqual( func_get_args(), func_num_args() ); } 
	final public function In() { return $this->__in( func_get_args(), func_num_args() ); }
	final public function Is() { return $this->__is( func_get_args(), func_num_args() ); }
	final public function IsNot() { return $this->__isNot( func_get_args(), func_num_args() ); }
	final public function IsNotNull() { return $this->__isNotNull( func_get_args(), func_num_args() ); }
	final public function IsNull() { return $this->__isNull( func_get_args(), func_num_args() ); }
	final public function Less() { return $this->__lessThan( func_get_args(), func_num_args() ); } 
	final public function Lt() { return $this->__lessThan( func_get_args(), func_num_args() ); } 
	final public function LessThan() { return $this->__lessThan( func_get_args(), func_num_args() ); } 
	final public function LessThanOrEqual() { return $this->__lessThanOrEqual( func_get_args(), func_num_args() ); } 
	final public function Lteq() { return $this->__lessThanOrEqual( func_get_args(), func_num_args() ); } 
	final public function Like() { return $this->__like( func_get_args(), func_num_args() ); }
	final public function Not() { return $this->__not( func_get_args(), func_num_args() ); }
	final public function NotBetween() { return $this->__notBetween( func_get_args(), func_num_args() ); }
	final public function Diff() { return $this->__notEqual( func_get_args(), func_num_args() ); }
	final public function NotEqual() { return $this->__notEqual( func_get_args(), func_num_args() ); }
	final public function ne() { return $this->__notEqual( func_get_args(), func_num_args() ); }
	final public function NotIn() { return $this->__notIn( func_get_args(), func_num_args() ); }
	final public function NotLike() { return $this->__notLike( func_get_args(), func_num_args() ); }
	final public function NotNull() { return $this->__notNull( func_get_args(), func_num_args() ); }
	final public function Where() { return $this->__where( func_get_args(), func_num_args() ); } 
	final public function BetweenOr() { return $this->__betweenOr( func_get_args(), func_num_args() ); } 
	final public function EqualOr() { return $this->__equalOr( func_get_args(), func_num_args() ); } 
	final public function GreaterOr() { return $this->__greaterThanOr( func_get_args(), func_num_args() ); } 
	final public function GreaterThanOr() { return $this->__greaterThanOr( func_get_args(), func_num_args() ); } 
	final public function GreaterThanOrEqualOr() { return $this->__greaterThanOrEqualOr( func_get_args(), func_num_args() ); } 
	final public function InOr() { return $this->__inOr( func_get_args(), func_num_args() ); } 
	final public function IsOr() { return $this->__isOr( func_get_args(), func_num_args() ); } 
	final public function IsNotOr() { return $this->__isNotOr( func_get_args(), func_num_args() ); } 
	final public function IsNotNullOr() { return $this->__isNotNullOr( func_get_args(), func_num_args() ); } 
	final public function IsNullOr() { return $this->__isNullOr( func_get_args(), func_num_args() ); } 
	final public function LessOr() { return $this->__lessThanOr( func_get_args(), func_num_args() ); } 
	final public function LessThanOr() { return $this->__lessThanOr( func_get_args(), func_num_args() ); } 
	final public function LessThanOrEqualOr() { return $this->__lessThanOrEqualOr( func_get_args(), func_num_args() ); } 
	final public function LikeOr() { return $this->__likeOr( func_get_args(), func_num_args() ); } 
	final public function NotOr() { return $this->__notOr( func_get_args(), func_num_args() ); } 
	final public function NotBetweenOr() { return $this->__notBetweenOr( func_get_args(), func_num_args() ); } 
	final public function NotEqualOr() { return $this->__notEqualOr( func_get_args(), func_num_args() ); } 
	final public function NotInOr() { return $this->__notInOr( func_get_args(), func_num_args() ); } 
	final public function NotLikeOr() { return $this->__notLikeOr( func_get_args(), func_num_args() ); } 
	final public function NotNullOr() { return $this->__notNullOr( func_get_args(), func_num_args() ); } 
	final public function WhereOr() { return $this->__whereOr( func_get_args(), func_num_args() ); } 
	final public function BetweenOn() { return $this->__betweenOn( func_get_args(), func_num_args() ); } 
	final public function EqualOn() { return $this->__equalOn( func_get_args(), func_num_args() ); } 
	final public function GreaterOn() { return $this->__greaterThanOn( func_get_args(), func_num_args() ); } 
	final public function GreaterThanOn() { return $this->__greaterThanOn( func_get_args(), func_num_args() ); } 
	final public function GreaterThanOrEqualOn() { return $this->__greaterThanOrEqualOn( func_get_args(), func_num_args() ); } 
	final public function InOn() { return $this->__inOn( func_get_args(), func_num_args() ); } 
	final public function IsOn() { return $this->__isOn( func_get_args(), func_num_args() ); } 
	final public function IsNotOn() { return $this->__isNotOn( func_get_args(), func_num_args() ); } 
	final public function IsNotNullOn() { return $this->__isNotNullOn( func_get_args(), func_num_args() ); } 
	final public function IsNullOn() { return $this->__isNullOn( func_get_args(), func_num_args() ); } 
	final public function LessOn() { return $this->__lessThanOn( func_get_args(), func_num_args() ); } 
	final public function LessThanOn() { return $this->__lessThanOn( func_get_args(), func_num_args() ); } 
	final public function LessThanOrEqualOn() { return $this->__lessThanOrEqualOn( func_get_args(), func_num_args() ); } 
	final public function LikeOn() { return $this->__likeOn( func_get_args(), func_num_args() ); } 
	final public function NotOn() { return $this->__notOn( func_get_args(), func_num_args() ); } 
	final public function NotBetweenOn() { return $this->__notBetweenOn( func_get_args(), func_num_args() ); } 
	final public function NotEqualOn() { return $this->__notEqualOn( func_get_args(), func_num_args() ); } 
	final public function NotInOn() { return $this->__notInOn( func_get_args(), func_num_args() ); } 
	final public function NotLikeOn() { return $this->__notLikeOn( func_get_args(), func_num_args() ); } 
	final public function NotNullOn() { return $this->__notNullOn( func_get_args(), func_num_args() ); } 
	final public function WhereOn() { return $this->__whereOn( func_get_args(), func_num_args() ); } 
	final public function OrBetween() { return $this->__orBetween( func_get_args(), func_num_args() ); }
	final public function OrEqual() { return $this->__orEqual( func_get_args(), func_num_args() ); }
	final public function OrGreater() { return $this->__orGreater( func_get_args(), func_num_args() ); }
	final public function OrGreaterThan() { return $this->__orGreaterThan( func_get_args(), func_num_args() ); }
	final public function OrGreaterThanOrEqual() { return $this->__orGreaterThanOrEqual( func_get_args(), func_num_args() ); }
	final public function OrIn() { return $this->__orIn( func_get_args(), func_num_args() ); }
	final public function OrIs() { return $this->__orIs( func_get_args(), func_num_args() ); }
	final public function OrIsNot() { return $this->__orIsNot( func_get_args(), func_num_args() ); }
	final public function OrIsNotNull() { return $this->__orIsNotNull( func_get_args(), func_num_args() ); }
	final public function OrIsNull() { return $this->__orIsNull( func_get_args(), func_num_args() ); }
	final public function OrLess() { return $this->__orLess( func_get_args(), func_num_args() ); }
	final public function OrLessThan() { return $this->__orLessThan( func_get_args(), func_num_args() ); }
	final public function OrLessThanOrEqual() { return $this->__orLessThanOrEqual( func_get_args(), func_num_args() ); }
	final public function OrLike() { return $this->__orLike( func_get_args(), func_num_args() ); }
	final public function OrNot() { return $this->__orNot( func_get_args(), func_num_args() ); }
	final public function OrNotBetween() { return $this->__orNotBetween( func_get_args(), func_num_args() ); }
	final public function OrNotEqual() { return $this->__orNotEqual( func_get_args(), func_num_args() ); }
	final public function OrNotIn() { return $this->__orNotIn( func_get_args(), func_num_args() ); }
	final public function OrNotLike() { return $this->__orNotLike( func_get_args(), func_num_args() ); }
	final public function OrNotNull() { return $this->__orNotNull( func_get_args(), func_num_args() ); }
	final public function OrWhere() { return $this->__orWhere( func_get_args(), func_num_args() ); } 
	final public function OrBetweenAnd() { return $this->__orBetweenAnd( func_get_args(), func_num_args() ); }
	final public function OrEqualAnd() { return $this->__orEqualAnd( func_get_args(), func_num_args() ); }
	final public function OrGreaterAnd() { return $this->__orGreaterAnd( func_get_args(), func_num_args() ); }
	final public function OrGreaterThanAnd() { return $this->__orGreaterThanAnd( func_get_args(), func_num_args() ); }
	final public function OrGreaterThanOrEqualAnd() { return $this->__orGreaterThanOrEqualAnd( func_get_args(), func_num_args() ); }
	final public function OrInAnd() { return $this->__orInAnd( func_get_args(), func_num_args() ); }
	final public function OrIsAnd() { return $this->__orIsAnd( func_get_args(), func_num_args() ); }
	final public function OrIsNotAnd() { return $this->__orIsNotAnd( func_get_args(), func_num_args() ); }
	final public function OrIsNotNullAnd() { return $this->__orIsNotNullAnd( func_get_args(), func_num_args() ); }
	final public function OrIsNullAnd() { return $this->__orIsNullAnd( func_get_args(), func_num_args() ); }
	final public function OrLessAnd() { return $this->__orLessAnd( func_get_args(), func_num_args() ); }
	final public function OrLessThanAnd() { return $this->__orLessThanAnd( func_get_args(), func_num_args() ); }
	final public function OrLessThanOrEqualAnd() { return $this->__orLessThanOrEqualAnd( func_get_args(), func_num_args() ); }
	final public function OrLikeAnd() { return $this->__orLikeAnd( func_get_args(), func_num_args() ); }
	final public function OrNotAnd() { return $this->__orNotAnd( func_get_args(), func_num_args() ); }
	final public function OrNotBetweenAnd() { return $this->__orNotBetweenAnd( func_get_args(), func_num_args() ); }
	final public function OrNotEqualAnd() { return $this->__orNotEqualAnd( func_get_args(), func_num_args() ); }
	final public function OrNotInAnd() { return $this->__orNotInAnd( func_get_args(), func_num_args() ); }
	final public function OrNotLikeAnd() { return $this->__orNotLikeAnd( func_get_args(), func_num_args() ); }
	final public function OrNotNullAnd() { return $this->__orNotNullAnd( func_get_args(), func_num_args() ); }
	final public function OrWhereAnd() { return $this->__orWhereAnd( func_get_args(), func_num_args() ); } 
	final public function WhereDate(/***/) { return $this->__whereDate( func_get_args(), func_num_args() ); } 
	final public function WhereDay(/***/) { return $this->__whereDay( func_get_args(), func_num_args() ); } 
	final public function WhereMonth(/***/) { return $this->__whereMonth( func_get_args(), func_num_args() ); } 
	final public function WhereYear(/***/) { return $this->__whereYear( func_get_args(), func_num_args() ); } 
	final public function WhereCount(/***/) { return $this->__whereCount( func_get_args(), func_num_args() ); } 
	final public function WhereSum(/***/) { return $this->__whereSum( func_get_args(), func_num_args() ); } 
	final public function WhereAvg(/***/) { return $this->__whereAvg( func_get_args(), func_num_args() ); } 
	final public function WhereMax(/***/) { return $this->__whereMax( func_get_args(), func_num_args() ); } 
	final public function WhereMin(/***/) { return $this->__whereMin( func_get_args(), func_num_args() ); } 
	final public function GroupBy() { return $this->__groupBy( func_get_args(), func_num_args()); } 
	final public function Sort() { return $this->__orderBy( func_get_args(), func_num_args() ); } 
	final public function SortBy() { return $this->__orderBy( func_get_args(), func_num_args() ); } 
	final public function Order() { return $this->__orderBy( func_get_args(), func_num_args() ); } 
	final public function OrderBy() { return $this->__orderBy( func_get_args(), func_num_args() ); } 
	final public function OrderDesc( $field ) { return $this->__orderDesc( $field ); } 
	final public function OrderAsc( $field ) { return $this->__orderAsc( $field ); } 
	final public function Limit() { return $this->__setLimit( func_get_args(), func_num_args() ); }
	final public function Offset() { return $this->__setSeek( func_get_args(), func_num_args() ); }
	final public function Seek() { return $this->__setSeek( func_get_args(), func_num_args() ); }
	final public function SetPage() { return $this->__setPage( func_get_args(), func_num_args() ); }
	final public function Page() { return $this->__setPage( func_get_args(), func_num_args() ); } 
	final public function View() { return call_user_func_array([$this, mcbm_view], func_get_args()); } 
	final public function Call() { return call_user_func_array([$this, mcbm_proc], array(func_get_args())); } 
	final public function Proc() { return call_user_func_array([$this, mcbm_proc], array(func_get_args())); } 
	final public function Func() { return call_user_func_array([$this, mcbm_func], array(func_get_args())); } 
	final public function Transaction() { return call_user_func_array([$this, mcbm_transaction], array(func_get_args())); } 
	final public function Commit() { return call_user_func_array([$this, mcbm_commit], array()); } 
	final public function Rollback() { return call_user_func_array([$this, mcbm_rollback], array(func_get_args())); } 
	final public function Checkpoint() { return call_user_func_array([$this, mcbm_checkpoint], array(func_get_args())); } 
	final public function Release() { return call_user_func_array([$this, mcbm_release], array(func_get_args())); } 
	final public function Death() { return call_user_func_array([$this, mcbm_detach_model], array(func_get_args(), func_num_args())); } 
	final public function Detach() { return call_user_func_array([$this, mcbm_detach_model], array(func_get_args(), func_num_args())); } 
	final public function DeathModel() { return call_user_func_array([$this, mcbm_detach_model], array(func_get_args(), func_num_args())); } 
	final public function DetachModel() { return call_user_func_array([$this, mcbm_detach_model], array(func_get_args(), func_num_args())); } 
	final public function HasOne() { return call_user_func_array([$this, mcbm_order_has_one], array(func_get_args(), func_num_args())); } 
	final public function BlindHasOne() { return call_user_func_array([$this, mcbm_hide_has_one], array()); }
	final public function DisplayHasOne() { return call_user_func_array([$this, mcbm_show_has_one], array()); } 
	final public function RenameHasOne() { return call_user_func_array([$this, mcbm_rename_has_one], array(func_get_args(), func_num_args())); } 
	final public function HasMany() { return call_user_func_array(array($this, mcbm_order_has_many), array(func_get_args(), func_num_args())); } 
	final public function BlindHasMany() { return call_user_func_array([$this, mcbm_hide_has_many], array()); }
	final public function DisplayHasMany() { return call_user_func_array([$this, mcbm_show_has_many], array()); }
	final public function RenameHasMany() { return call_user_func_array([$this, mcbm_rename_has_many], array(func_get_args(), func_num_args())); }
	final public function HasManyAndBelongsToMany() { return call_user_func_array([$this, mcbm_order_has_mabtm], array(func_get_args(), func_num_args())); } 
	final public function BlindHasManyAndBelongsToMany() { return call_user_func_array([$this, mcbm_hide_has_mabtm], array()); }
	final public function DisplayHasManyAndBelongsToMany() { return call_user_func_array([$this, mcbm_show_has_mabtm], array()); }
	final public function RenameHasManyAndBelongsToMany() { return call_user_func_array([$this, mcbm_rename_has_mabtm], array(func_get_args(), func_num_args())); }
	final public function UnionAll() { return call_user_func_array([$this, mcbm_order_import_all], array(func_get_args(), func_num_args())); } 
	final public function Import() { return call_user_func_array([$this, mcbm_order_import_all], array(func_get_args(), func_num_args())); } 
	final public function Union() { return call_user_func_array([$this, mcbm_order_import_once], array(func_get_args(), func_num_args())); } 
	final public function ImportOnce() { return call_user_func_array([$this, mcbm_order_import_once], array(func_get_args(), func_num_args())); } 
	final public function Bind() { return call_user_func_array([$this, mcbm_order_merge], array(func_get_args(), func_num_args())); } 
	final public function Join() { return call_user_func_array([$this, mcbm_order_merge], array(func_get_args(), func_num_args())); } 
	final public function Merge() { return call_user_func_array([$this, mcbm_order_merge], array(func_get_args(), func_num_args())); } 
	final public function JoinLeft() { return call_user_func_array([$this, mcbm_order_merge_left], array(func_get_args(), func_num_args())); } 
	final public function MergeLeft() { return call_user_func_array([$this, mcbm_order_merge_left], array(func_get_args(), func_num_args())); } 
	final public function JoinRight() { return call_user_func_array([$this, mcbm_order_merge_right], array(func_get_args(), func_num_args())); } 
	final public function MergeRight() { return call_user_func_array([$this, mcbm_order_merge_right], array(func_get_args(), func_num_args())); } 
	final public function New() { return $this->__new(); }
	final public function Reset() { return $this->__new(); }
	final public function Clear( $deep=false ) { return $this->__clear( $deep ); } 
	final public function Search() { return call_user_func_array([$this, mcbm_search], array(func_get_args(), func_num_args())); } 
	final public function Custom() { return call_user_func_array([$this, mcbm_custom], array(func_get_args(), func_num_args())); }
	final public function Query() { return call_user_func_array([$this, mcbm_custom], array(func_get_args(), func_num_args(), 'Query')); } 
	final public function Load() { return call_user_func_array([$this, mcbm_findid], array(func_get_args(), func_num_args())); } 
	final public function Find() { return call_user_func_array([$this, mcbm_findid], array(func_get_args(), func_num_args())); } 
	final public function Entity() { return call_user_func_array([$this, mcbm_entity], array(func_get_args(), func_num_args())); } 
	final public function First() { return call_user_func_array([$this, mcbm_first], array(func_get_args(), func_num_args())); } 
	final public function Last() { return call_user_func_array([$this, mcbm_last], array(func_get_args(), func_num_args())); } 
	final public function Item() { return call_user_func_array([$this, mcbm_item], array(func_get_args(), func_num_args())); }
	final public function Paginate() { return call_user_func_array([$this, mcbm_paginate], array(func_get_args(), func_num_args())); }
	final public function TotalPages() { return call_user_func_array([$this, mcbm_total_pages], array(func_get_args(), func_num_args())); } 
	final public function Total() { return call_user_func_array([$this, mcbm_total], array(func_get_args(), func_num_args())); } 
	final public function Length() { return call_user_func_array([$this, mcbm_length], array(func_get_args(), func_num_args())); } 
	final public function Count() { return call_user_func_array([$this, mcbm_count], array(func_get_args(), func_num_args())); } 
	final public function Distinct() { return call_user_func_array([$this, mcbm_distinct], array(func_get_args(), func_num_args())); } 
	final public function Sum() { return call_user_func_array([$this, mcbm_sum], array(func_get_args(), func_num_args())); } 
	final public function Avg() { return call_user_func_array([$this, mcbm_avg], array(func_get_args(), func_num_args())); } 
	final public function Max() { return call_user_func_array([$this, mcbm_max], array(func_get_args(), func_num_args())); } 
	final public function Min() { return call_user_func_array([$this, mcbm_min], array(func_get_args(), func_num_args())); } 
	final public function Implode() { return call_user_func_array([$this, mcbm_implode], array(func_get_args(), func_num_args())); } 
	final public function Row() { return call_user_func_array([$this, mcbm_row], array(func_get_args(), func_num_args())); } 
	final public function Fetch() { return call_user_func_array([$this, mcbm_row], array(func_get_args(), func_num_args())); } 
	final public function Role() { return call_user_func_array([$this, mcbm_role], array(func_get_args(), func_num_args())); } 
	final public function Data() { return call_user_func_array([$this, mcbm_role], array(func_get_args(), func_num_args())); } 
	final public function Unit() { return call_user_func_array([$this, mcbm_unit], array(func_get_args(), func_num_args())); }
	final public function Prefix() { return call_user_func_array([$this, mcbm_prefix], array(func_get_args(), func_num_args())); } 
	final public function Remove() { return call_user_func_array([$this, mcbm_delete], array(func_get_args(), func_num_args())); }
	final public function Delete() { return call_user_func_array([$this, mcbm_delete], array(func_get_args(), func_num_args())); }
	final public function Insert() { return call_user_func_array([$this, mcbm_insert], array(func_get_args(), func_num_args())); }
	final public function Create() { return call_user_func_array([$this, mcbm_insert], array(func_get_args(), func_num_args())); }
	final public function Save() { return call_user_func_array([$this, mcbm_save], array(func_get_args(), func_num_args())); }
	final public function Affected() { return call_user_func_array([$this, mcbm_affected], array(func_get_args(), func_num_args())); } 
	final public function DBList() { return call_user_func_array([$this, mcbm_db_list], array(func_get_args(), func_num_args())); }
		  public function Close() { return call_user_func_array([self::$this, mcbm_close],array()); }
	final public function Connect( $name ) { return $this->__connect( $name ); }
	final public function GetError() { return $this->__getError(); } 
	final public function GetQuery() { return $this->__getQuerySQL(); }
	final public function LastQuery() { return $this->__getQuerySQL(); }
	final public function GetLastQuery() { return $this->__getQuerySQL(); }
	final public function GetQuerys() { return $this->_querySQLs; } 
	final public function GetQuerySQLs() { return $this->_querySQLs; } 
	final public function GetQuerySQL() { return $this->_querySQL; }
	final public function ToSql() { return $this->_querySQL; }
	final public function GetCollectionString() { return $this->__buildCollectionString(); }
	final public function FluidSqlQuery() { return $this->__buildSqlQuery(); } 
	final public function GenRandString( $len=10 ) { return $this->__genRandString($len); }
	
	final protected function __setPrefix( $value ) { $this->_propPrefix = $value; return $this; }
	final protected function __setModel( $value ) { return $this->__setModelName( $value ); }
	final protected function __setAlias( $value ) { return $this->__setAliasName( $value ); }
	final protected function __setTable( $value ) { return $this->__setTableName( $value ); } 
	final protected function __setUnits( $value ) { return $this->__setUnitsSize( $value ); } 
	final protected function __new() { return $this->__clear( true ); } 
	final protected function __reset() { return $this->__clear( true ); } 
	
	final protected function __setData( $args, $argsNum, $method="_setData" ) 
	{
		try 
		{ 
			$twoArg = 2; 
			$oneArg = 1; 
			$dispatcher = $this; 
			if( $argsNum ) 
				if( $oneArg===$argsNum ) 
				{
					$data = current($args); 
					if( is_array($data) ) 
						foreach( $data as $key => $value ) 
							if( is_string($key) && in_array($key, $this->_propsDescribe) )
								$this->{$key} = $value;
				}
				else 
				{
					$field = $args[0]; 
					$value = $args[1]; 
					$dispatcher->{$field} = $value; 
				}
			else 
				throw new Exception( "Usage <strong>Model::$method</strong> is incorrect." ); 
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() );
		}
		return $this;
	} 

	final protected function __boundField( $fieldName, $fieldLabel=NULL ) 
	{
		$patt = '#^([\w\d]+)\(([\w\d]+)\)#';
		if( in_array($fieldName, $this->_propsDescribe) ) 
		{
			$field = [ $fieldName => array( 
				'name'	=> $fieldName, 
				'label'	=> $fieldLabel, 
			)]; 
		} 
		else if( preg_match($patt, $fieldName, $matches) )  
		{
			$field = [ $fieldName => array( 
				'cmd'	=> strtoupper($matches[1]), 
				'name'	=> $matches[2], 
				'label'	=> $fieldLabel, 
			)]; 
		} 
		else 
		{
			$field = [ $fieldName => array( 
				'cmd'	=> $fieldName, 
				'label'	=> $fieldLabel, 
			)];
		}
		$this->_propsUndescribe = array_merge($this->_propsUndescribe, $field); 
		return $this; 
	} 
	
	final protected function __unboundField( $fieldName ) 
	{
		if( isset($this->_propsUndescribe[$fieldName]) ) 
			unset($this->_propsUndescribe[$fieldName]); 
		if( isset($this->_propsProjection) ) 
		{
			if( !in_array($fieldName, $this->_propsProjection) ) 
				$this->_propsProjection[] = $fieldName; 
		}
		return $this; 
	} 
	
	final protected function __unsecureField( $fieldName ) 
	{
		if( !array_key_exists($fieldName, $this->_propsUndescribe) ) 
			return $this->__boundField( $fieldName ); 
	} 
	
	final protected function __affected( $args, $argsNum ) 
	{ 
		try 
		{ 
			return call_user_func_array( array($this, 'affected_rows'), $args ); 
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() ); 
		} 
	} 
	
	final protected function __prefix( $args, $argsNum ) 
	{ 
		try 
		{ 
			if( 1!==$argsNum ) 
			{ 
				throw new Exception( "Using the Model::prefix() with required 1 parameter" ); 
			} 
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage().BL.error::position($e) ); 
		} 
		
		$this->_propPrefix = current($args); 
		
		return $this; 
	} 
	
	final protected function __bound( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
			{
				$oneArg = 1; 
				$zeroArg = 0;
				$dispatcher = $this;
				if( $argsNum===$oneArg ) 
				{
					$data = current($args); 
					if( is_string($data) ) 
					{
						$this->_propsUndescribe = array();
						$this->__boundField( $data ); 
					}
					else if(count($data)===$oneArg) 
					{
						$this->_propsUndescribe = array();
						$this->__boundField( key($data), current($data) ); 
					} 
					else 
					{
						$args[$oneArg] = count($args[$zeroArg]); 
						call_user_func_array( array($dispatcher, '__bound'), $args ); 
					}
				}
				else 
				{
					$this->_propsUndescribe = array();
					foreach( $args as $fieldKey => $fieldValue ) 
						if( is_numeric($fieldKey) ) 
						{
							if( is_array($fieldValue) ) 
								$this->__boundField(key($fieldValue), current($fieldValue));
							else 
								$this->__boundField( $fieldValue ); 
						}
						else if( is_string($fieldKey) ) 
							$this->__boundField( $fieldKey, $fieldValue ); 
				} 
				if( isset($this->_propsProjection) )
					foreach( $this->_propsDescribe as $describe ) 
						if(!array_key_exists($describe, $this->_propsUndescribe)) 
							$this->_propsProjection[] = $describe; 
			} 
			else 
				throw new Exception( "Using <strong>Model::bound()</strong> has a syntax error." );
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() );
		} 
		return $this;
	} 

	final protected function __unbound( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
			{
				$oneArg = 1; 
				$zeroArg = 0;
				$dispatcher = $this;
				if( $argsNum===$oneArg ) 
					if( is_array($args[$zeroArg]) )
					{
						$args[$oneArg] = count($args[$zeroArg]); 
						call_user_func_array( array($dispatcher, '__unbound'), $args );
					} 
					else 
						$this->__unboundField( $args[$zeroArg] ); 
				else 
					foreach( $args as $fieldKey => $fieldValue ) 
						if( is_numeric($fieldKey) ) 
							$this->__unboundField( $fieldValue ); 
			} 
			else 
				throw new Exception( "Using <strong>Model::bound()</strong> has a syntax error." );
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() ); 
		} 
		return $this;
	} 
	
	final protected function __addPersistentUndescrible( $field ) 
	{
		if( is_string($field) && !in_array($field, $this->_propsPersistentUndescribe) ) 
		{
			$this->_propsPersistentUndescribe[] = $field; 
		} 
	} 
	
	final protected function __removePersistentUndescrible( $field ) 
	{
		if( is_string($field) && in_array($field, $this->_propsPersistentUndescribe) ) 
		{
			foreach( $this->_propsPersistentUndescribe as $key => $field ) 
			{
				if( is_string($key) ) 
				{
					unset( $this->_propsPersistentUndescribe[$key] );
				} 
				elseif( is_numeric($key) ) 
				{
					array_splice($this->_propsPersistentUndescribe, $key, 1); 
				}
			}
		}
	}
	
	final protected function __secure( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
			{
				$oneArg = 1; 
				$zeroArg = 0;
				$dispatcher = $this;
				if( $argsNum===$oneArg ) 
				{
					if( is_array($args[$zeroArg]) )
					{
						$args[$oneArg] = count($args[$zeroArg]); 
						call_user_func_array( array($dispatcher, '__secure'), $args );
					} 
					else 
					{
						$field = $args[$zeroArg];
						$this->__unboundField( $field ); 
						$this->__addPersistentUndescrible( $field );
					}
				}
				else 
				{
					foreach( $args as $key => $field ) 
					{
						if( is_numeric($key) ) 
						{
							$this->__unboundField( $field ); 
							$this->__addPersistentUndescrible( $field );
						}
					}
				}
			}
			else 
				throw new Exception( "Using <strong>Model::Unsecure()</strong> has a syntax error." ); 
		}
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() );
		} 
		return $this;
	} 
	
	final protected function __unsecure( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
			{
				$oneArg = 1; 
				$zeroArg = 0;
				$dispatcher = $this;
				if( $argsNum===$oneArg ) 
				{
					if( is_array($args[$zeroArg]) )
					{
						$args[$oneArg] = count($args[$zeroArg]); 
						call_user_func_array( array($dispatcher, '__unsecure'), $args );
					} 
					else 
					{
						$field = $args[$zeroArg];
						$this->__unsecureField( $field ); 
						$this->__removePersistentUndescrible( $field ); 
					}
				}
				else 
				{
					foreach( $args as $key => $field ) 
					{
						if( is_numeric($key) ) 
						{
							$this->__unsecureField( $field ); 
							$this->__removePersistentUndescrible( $field );
						}
					}
				}
			}
			else 
				throw new Exception( "Using <strong>Model::Unsecure()</strong> has a syntax error." ); 
		}
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() );
		} 
		return $this;
	} 

	final protected function __between( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__between_operator'), array($args, $argsNum, 'between') ); 
			else 
				throw new Exception( "Using <strong>Model::between</strong> has a syntax error." ); 
		}
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
		return $this;
	} 

	final protected function __equal( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__where_operator'), array($args, $argsNum, '=', '__equal') ); 
			else 
				throw new Exception( "Using <strong>Model::equal()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() );
		} 
		return $this;
	} 

	final protected function __greaterThan( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__where_operator'), array($args, $argsNum, '>', '__greaterThan'));
			else 
				throw new Exception( "Using <strong>Model::greaterThan()</strong> has a syntax error." );
		}
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() );
		}
		return $this; 
	} 

	final protected function __greaterThanOrEqual( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__where_operator'), array($args, $argsNum, '>=', '__greaterThanOrEqual') );
			else 
				throw new Exception( "Using <strong>Model::greaterThanOrEqual()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
		return $this; 
	} 

	final protected function __in( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__in_operator'), array($args, $argsNum, 'in', '__in') );
			else 
				throw new Exception( "Using <strong>Model::in()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		}
		return $this;
	}

	final protected function __is( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__where_operator'), array($args, $argsNum, 'is', '__is') );
			else 
				throw new Exception( "Using <strong>Model::is()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
		return $this;  
	} 

	final protected function __isNot( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__where_operator'), array($args, $argsNum, 'is not', '__isNot') ); 
			else 
				throw new Exception( "Using <strong>Model::isNot()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
		return $this; 
	} 

	final protected function __isNotNull( $args, $argsNum ) 
	{
		try 
		{ 
			if( $argsNum ) 
				call_user_func_array( array($this, '__null_operator'), array($args, $argsNum, 'is not', '__isNotNull') ); 
			else 
				throw new Exception( "Using <strong>Model::isNotNull()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
		return $this;
	} 

	final protected function __isNull( $args, $argsNum ) 
	{
		try 
		{ 
			if( $argsNum )
				call_user_func_array( array($this, '__null_operator'), array($args, $argsNum, 'is', '__isNull') ); 
			else 
				throw new Exception( "Using <strong>Model::isNull()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
		return $this;
	} 

	final protected function __lessThan( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__where_operator') , array($args, $argsNum, '<', '__lessThan') );
			else 
				throw new Exception( "Using <strong>Model::lessThan()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
		return $this; 
	} 

	final protected function __lessThanOrEqual( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__where_operator'), array($args, $argsNum, '<=', '__lessThanOrEqual') );
			else 
				throw new Exception( "Using <strong>Model::lessThanOrEqual()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
		return $this;
	} 

	final protected function __like( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__where_operator') , array($args, $argsNum, 'like', '__like') );
			else 
				throw new Exception( "Using <strong>Model::like()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
		return $this;
	}

	final protected function __not( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__where_operator'), array($args, $argsNum, '!=', '__not') );
			else 
				throw new Exception( "Using <strong>Model::not()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
		return $this;
	} 
	
	final protected function __notBetween( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__between_operator'), array($args, $argsNum, 'not between') );
			else 
				throw new Exception( "Using <strong>Model::notBetween()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
		return $this;
	} 

	final protected function __notEqual( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__where_operator'), array($args, $argsNum, '!=', '__notEqual') );
			else 
				throw new Exception( "Using <strong>Model::notEqual()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
		return $this;
	}

	final protected function __notIn( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__in_operator'), array($args, $argsNum, 'not in', '__notIn') );
			else 
				throw new Exception( "Using <strong>Model::notIn()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		}
		return $this;
	} 

	final protected function __notLike( $args, $argsNum, $type=NULL ) 
	{
		try 
		{
			if( $argsNum ) 
			{
				call_user_func_array( array($this, '__where_operator'), array($args, $argsNum, 'not like', '__notLike') );
			}
			else 
				throw new Exception( "Using <strong>Model::notLike()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
		return $this;
	}

	final protected function __notNull( $args, $argsNum ) 
	{
		try 
		{ 
			if( $argsNum ) 
				call_user_func_array( array($this, '__null_operator'), array($args, $argsNum, 'is not', '__notNull') ); 
			else 
				throw new Exception( "Using <strong>Model::notNull()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
		return $this;
	} 
	
	final protected function __between_operator( $args, $argsNum, $sign ) 
	{
		$threeArg = 3;
		$twoArg = 2;
		$oneArg = 1; 
		$dispatcher = $this;
		if( $threeArg===$argsNum ) 
		{
			$args[2] = [$args[1], $args[2]]; 
			$args[1] = $sign; 
			call_user_func_array(array($dispatcher, '__where'), array($args, count($args)));
		}
		else if( $twoArg===$argsNum ) 
		{
			$tmp = $args[1]; 
			$args[1] = $sign; 
			$args[2] = $tmp;
			call_user_func_array(array($dispatcher, '__where'), array($args, count($args)));
		}
		else if( $oneArg===$argsNum ) 
		{
			$params = current($args); 
			foreach( $params as $param ) 
			{
				$tmp = array(key($param)); 
				$tmp[] = $sign; 
				$tmp[] = current($param);
				call_user_func_array(array($dispatcher, '__where'), array($tmp, count($tmp)));
			}
		}
	} 
	
	final protected function __in_operator( $args, $argsNum, $sign, $method ) 
	{
		$twoArg = 2; 
		$oneArg = 1; 
		$dispatcher = $this; 
		if( $twoArg===$argsNum ) 
		{
			$tmp = $args[1];
			$args[1] = $sign;
			$args[2] = $tmp; 
			call_user_func_array( array($dispatcher, '__where'), array($args, count($args)) ); 
		} 
		else if( $argsNum>$twoArg ) 
		{
			$tmp = array();
			$tmp[] = array_shift($args); 
			$tmp[] = $sign;
			$tmp[] = $args;
			call_user_func_array( array($dispatcher, '__where'), array($tmp, 3) ); 
		}
		else 
		{
			$params = current($args); 
			if( is_array($params) ) 
				foreach( $params as $args ) 
					call_user_func_array( array($dispatcher, $method), array([key($args), current($args)], $twoArg) ); 
		}
	}
	
	final protected function __null_operator( $args, $argsNum, $sign, $method ) 
	{
		$twoArg = 2; 
		$oneArg = 1; 
		$dispatcher = $this; 
		if( $oneArg===$argsNum ) 
		{
			if( is_string($args[0]) ) 
			{
				$args[] = $sign; 
				$args[] = NULL;
				call_user_func_array( array($dispatcher, '__where'), array( $args, 3 ) );
			}
			else 
			{
				$args = current($args);
				foreach( $args as $arg ) 
					call_user_func_array( array($dispatcher, $method), array((array)$arg, 1) );
			}
		} 
		else if( $twoArg<= $argsNum ) 
			call_user_func_array( array($dispatcher, $method), array(array ($args), $oneArg) );
	}
	
	final protected function __where_operator( $args, $argsNum, $sign, $method ) 
	{
		$twoArg = 2; 
		$oneArg = 1; 
		$dispatcher = $this;
		if( $oneArg===$argsNum ) 
		{
			$params = current($args); 
			if( is_array($params) ) 
			{
				$tmp = array();
				foreach( $params as $key => $value ) 
					if(is_string($key)) 
						call_user_func_array( array($dispatcher, $method), array([$key, $value], $twoArg) ); 
					else if( is_numeric($key) ) 
						$tmp[] = $value;
				if( !empty($tmp) ) 
					call_user_func_array( array($dispatcher, $method), array($tmp, count($tmp)) ); 
			} 
			else 
			{
				if( isset($this->_primaryKey) ) 
				{
					$key = $this->_primaryKey;
					$value = $params;
					call_user_func_array( array($dispatcher, $method), array([$key, $value], $twoArg) ); 
				}
			}
		}
		else if( $twoArg<=$argsNum ) 
		{
			if( is_string($args[0]) ) 
			{
				$argsNum = 3;
				$tmp = $args[1];
				$args[1] = $sign;
				$args[2] = $tmp; 
				call_user_func_array( array($dispatcher, '__where'), array($args, $argsNum) ); 
			} 
			else 
			{
				foreach( $args as $arg ) 
				{
					$argsNum = count($arg);
					$tmp = array(); 
					if( $oneArg===$argsNum ) 
					{
						$tmp[] = key($arg); 
						$tmp[] = $sign;
						$tmp[] = current($arg);
					} 
					else 
					{
						$tmp[] = $arg[0]; 
						$tmp[] = $sign;
						$tmp[] = $arg[1];
					}
					$arg = $tmp;
					call_user_func_array( array($dispatcher, '__where'), array($arg, count($arg)) ); 
				}
			}
		}
	}

	final protected function __where( $args, $argsNum, $embed=false ) 
	{
		try 
		{
			if( $argsNum )
			{
				$fourArg = 4;
				$threeArg = 3;
				$twoArg = 2;
				$oneArg = 1; 
				$dispatcher = $this; 
				if( $twoArg===$argsNum ) 
				{ 
					if( 'is not null' === strtolower($args[1]) ) 
						return call_user_func_array(array($dispatcher, '__where'), array([$args[0], 'is not', NULL], 3));
					elseif( 'not null' === strtolower($args[1]) )
						return call_user_func_array(array($dispatcher, '__where'), array([$args[0], 'not', NULL], 3));
					else
						return call_user_func_array(array($dispatcher, '__where'), array([$args[0], '=', $args[1]], 3));
				} 
				if( $twoArg<$argsNum ) 
				{
					$allowOps = $this->__getOperatorSymbols(); 
					if( $threeArg===$argsNum ) 
					{
						if( is_string($args[2]) ) 
						{
							if( 'is not null' === strtolower($args[2]) ) 
								return call_user_func_array(array($dispatcher, '__where'), array([$args[0], $args[1], 'is not', NULL], 4));
							elseif( 'not null' === strtolower($args[2]) )
								return call_user_func_array(array($dispatcher, '__where'), array([$args[0], $args[1], 'not', NULL], 4));
						}
							
						if( in_array($args[0], $this->_propsDescribe) && array_key_exists($args[1], $allowOps) ) 
						{
							$args[1] = $allowOps[strtolower($args[1])]; 
							if( $embed ) 
								return $args; 
							$this->_propsCond[] = $args;
						} 
						else 
						{
							if( !in_array($args[0], $this->_propsDescribe) && config::get('DEVELOPMENT_ENVIRONMENT') ) 
								throw new Exception( 'Field <strong>'.$args[0].'</strong> doesn\'t exist in <strong>'.$this->_propTable.'</strong>.' ); 
							if( !array_key_exists($args[1], $allowOps) && config::get('DEVELOPMENT_ENVIRONMENT') ) 
								throw new Exception( 'The oparator is\'nt accepted <strong>'.$args[1].'</strong>.' ); 
						}
					} 
					else if( $fourArg===$argsNum ) 
					{
						$args[2] = $allowOps[strtolower($args[2])]; 
						$this->_propsCondEx[$args[0]][] = array_slice($args, 1); 
					} 
				}
				else if( $oneArg===count($args) && isset($this->_primaryKey) ) 
				{
					$value = current($args); 
					if( is_string($value) && method_exists($dispatcher, 'ObjectId') ) 
						$value = $dispatcher->objectId( $value ); 
					$params = array( $this->_primaryKey, '=', $value ); 
					return call_user_func_array(array($dispatcher, '__where'), array($params, count($params))); 
				}
				else 
				{
					$params = current($args); 
					foreach( $params as $args ) 
					{
						if( $oneArg===count($args) ) 
							continue; 
						call_user_func_array(array($dispatcher, '__where'), array($args, count($args)));
					}
				}
			}
		}
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage().BL.error::position($e) );
		}
		return $this;
	} 
	
	final protected function __betweenOr( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__between_or_operator'), array($args, $argsNum, 'between') );
			else 
				throw new Exception( "Using <strong>Model::betweenOr()</strong> has a syntax error." ); 
		}
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	} 
	
	final protected function __equalOr( $args, $argsNum ) 
	{ 
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__where_or_operator'), array($args, $argsNum, '=') );
			else 
				throw new Exception( "Using <strong>Model::equalOr()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
		return $this;
	} 
	
	final protected function __greaterThanOr( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__where_or_operator'), array($args, $argsNum, '>') );
			else 
				throw new Exception( "Using <strong>Model::greaterThanOr()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
		return $this;
	} 
	
	final protected function __greaterThanOrEqualOr( $args, $argsNum ) 
	{ 
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__where_or_operator'), array($args, $argsNum, '>=') );
			else 
				throw new Exception( "Using <strong>Model::greaterThanOrEqualOr()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
		return $this;
	} 
	
	final protected function __inOr( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__where_or_operator'), array($args, $argsNum, 'in') );
			else 
				throw new Exception( "Using <strong>Model::inOr()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
		return $this;
	} 
	
	final protected function __isOr( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__where_or_operator'), array($args, $argsNum, 'is') );
			else 
				throw new Exception( "Using <strong>Model::isOr()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
		return $this;
	} 
	final protected function __isNotOr( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__where_or_operator'), array($args, $argsNum, 'is not') );
			else 
				throw new Exception( "Using <strong>Model::isNotOr()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
		return $this;
	} 
	
	final protected function __isNotNullOr( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__null_or_operator'), array($args, $argsNum, 'is not') );
			else 
				throw new Exception( "Using <strong>Model::isNotNullOr()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
		return $this;
	} 
	
	final protected function __isNullOr( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__null_or_operator'), array($args, $argsNum, 'is') );
			else 
				throw new Exception( "Using <strong>Model::isNullOr()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
		return $this;
	} 
	
	final protected function __lessOr( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__where_or_operator'), array($args, $argsNum, '<') );
			else 
				throw new Exception( "Using <strong>Model::lessOr()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
		return $this;
	} 
	
	final protected function __lessThanOr( $args, $argsNum ) 
	{ 
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__where_or_operator'), array($args, $argsNum, '<') );
			else 
				throw new Exception( "Using <strong>Model::lessThanOr()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
		return $this;
	} 
	
	final protected function __lessThanOrEqualOr( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__where_or_operator'), array($args, $argsNum, '<=') );
			else 
				throw new Exception( "Using <strong>Model::lessThanOrEqualOr()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
		return $this;
	} 
	
	final protected function __likeOr( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__where_or_operator'), array($args, $argsNum, 'like') );
			else 
				throw new Exception( "Using <strong>Model::likeOr()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
		return $this;
	} 
	
	final protected function __notOr( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__where_or_operator'), array($args, $argsNum, 'not') );
			else 
				throw new Exception( "Using <strong>Model::notOr()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
		return $this;
	} 
	
	final protected function __notBetweenOr( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__between_or_operator'), array($args, $argsNum, 'not between') );
			else 
				throw new Exception( "Using <strong>Model::notBetweenOr()</strong> has a syntax error." ); 
		}
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	} 
	
	final protected function __notEqualOr( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__where_or_operator'), array($args, $argsNum, '!=') );
			else 
				throw new Exception( "Using <strong>Model::notEqualOr()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
		return $this;
	}
	
	final protected function __notInOr( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__where_or_operator'), array($args, $argsNum, 'not in') );
			else 
				throw new Exception( "Using <strong>Model::notInOr()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
		return $this;
	}
	
	final protected function __notLikeOr( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__where_or_operator'), array($args, $argsNum, 'not like') );
			else 
				throw new Exception( "Using <strong>Model::notLikeOr()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
		return $this;
	} 
	
	final protected function __notNullOr( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				call_user_func_array( array($this, '__null_or_operator'), array($args, $argsNum, 'is not') );
			else 
				throw new Exception( "Using <strong>Model::notNullOr()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
		return $this;
	} 
	
	final protected function __between_or_operator( $args, $argsNum, $sign ) 
	{
		$oneArg = 1; 
		$dispatcher = $this; 
		if( $oneArg===$argsNum ) 
			$params = current($args); 
		else 
			$params = $args; 
		$args = array();
		foreach( $params as $arg ) 
		{
			$param = array(key($arg)); 
			$param[] = $sign; 
			$param[] = current($arg);
			$args [] = $param;
		}
		call_user_func_array( array($dispatcher, '__whereOr'), array($args, count($args)) );
	}
	
	final protected function __null_or_operator( $args, $argsNum, $sign ) 
	{
		$oneArg = 1; 
		$dispatcher = $this; 
		$oneArg = 1; 
		$dispatcher = $this; 
		$tmps = array(); 
		if( $oneArg<$argsNum ) 
			foreach( $args as $arg ) 
				$tmps[] = array($arg, $sign, NULL); 
		else 
		{
			$args = current($args);
			foreach( $args as $key ) 
			{
				$tmps[] = array($key, $sign, NULL); 
			}
		}
		call_user_func_array( array($dispatcher, '__whereOr'), array($tmps, count($tmps)) ); 
	}
	
	final protected function __where_or_operator( $args, $argsNum, $sign ) 
	{
		$oneArg = 1; 
		$dispatcher = $this; 
		$tmps = array(); 
		if( $oneArg<$argsNum ) 
			foreach( $args as $arg )
				$tmps[] = array(key($arg), $sign, current($arg)); 
		else 
		{
			$args = current($args);
			foreach( $args as $key => $value ) 
				$tmps[] = array($key, $sign, $value); 
		}
		call_user_func_array( array($dispatcher, '__whereOr'), array($tmps, count($tmps)) ); 
	}
	
	final protected function __whereOr( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
			{
				$threeArg = 3; 
				$twoArg = 2; 
				$oneArg = 1; 
				$dispatcher = $this; 
				if( $oneArg===$argsNum ) 
				{
					$params = current($args); 
					if( is_array($params) ) 
					{
						$argsNum = count($params);
						if( $oneArg>=$argsNum ) 
							throw new Exception( "Using <strong>Model::whereOr()</strong> has a syntax error." ); 
					} 
					else 
						throw new Exception( "Using <strong>Model::whereOr()</strong> has a syntax error." ); 
				} 
				else 
					$params = $args; 
				
				$tmps = array(); 
				foreach( $params as $key => $arg ) 
				{
					if( is_string($key) ) 
					{
						$tmp = $arg; 
						$arg = array(
							$key, 
							'=', 
							$tmp 
						);
					}
					else if( $twoArg===count($arg) ) 
					{
						$tmp = $arg[1]; 
						$arg[1] = '='; 
						$arg[2] = $tmp; 
					} 
					else if( $oneArg===count($arg) )
					{
						$tmp = $arg; 
						$arg = array(
							key($tmp), 
							'=', 
							current($tmp) 
						);
					}
					$args = array($arg);
					$args[] = count($arg); 
					$args[] = true;
					$tmps[] = call_user_func_array( array($dispatcher, '__where'), $args ); 
				}
				if( !empty($tmps) )
					$this->_propsCond[] = array('OR'=>$tmps); 
			} 
			else 
				throw new Exception( "Using <strong>Model::whereOr()</strong> has a syntax error." ); 
			return $this;
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __betweenOn( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__between_on_operator'), array($args, $argsNum, 'between') );
			else 
				throw new Exception( "Using <strong>Model::betweenOn()</strong> has a syntax error." ); 
		}
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	} 
	
	final protected function __equalOn( $args, $argsNum ) 
	{ 
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__where_on_operator'), array($args, $argsNum, '=', '__equalOn') );
			else 
				throw new Exception( "Using <strong>Model::equalOn()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __greaterThanOn( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__where_on_operator'), array($args, $argsNum, '>', '__greaterThanOn') );
			else 
				throw new Exception( "Using <strong>Model::greaterThanOn()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __greaterThanOrEqualOn( $args, $argsNum ) 
	{ 
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__where_on_operator'), array($args, $argsNum, '>=', '__greaterThanOrEqualOn') );
			else 
				throw new Exception( "Using <strong>Model::greaterThanOrEqualOn()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __inOn( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__where_on_operator'), array($args, $argsNum, 'in', '__inOn') );
			else 
				throw new Exception( "Using <strong>Model::inOn()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __isOn( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__where_on_operator'), array($args, $argsNum, 'is', '__isOn') );
			else 
				throw new Exception( "Using <strong>Model::isOn()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __isNotOn( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__where_on_operator'), array($args, $argsNum, 'is not', '__isNotOn') );
			else 
				throw new Exception( "Using <strong>Model::isNotOn()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __isNotNullOn( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__null_on_operator'), array($args, $argsNum, 'is not', '__isNotNullOn') );
			else 
				throw new Exception( "Using <strong>Model::isNotNullOn()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __isNullOn( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__null_on_operator'), array($args, $argsNum, 'is', '__isNullOn') );
			else 
				throw new Exception( "Using <strong>Model::isNullOn()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __lessOn( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__where_on_operator'), array($args, $argsNum, '<', '__lessOn') );
			else 
				throw new Exception( "Using <strong>Model::lessOn()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __lessThanOn( $args, $argsNum ) 
	{ 
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__where_on_operator'), array($args, $argsNum, '<', '__lessThanOn') );
			else 
				throw new Exception( "Using <strong>Model::lessThanOn()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __lessThanOrEqualOn( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__where_on_operator'), array($args, $argsNum, '<=', '__lessThanOrEqualOn') );
			else 
				throw new Exception( "Using <strong>Model::lessThanOrEqualOn()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __likeOn( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__where_on_operator'), array($args, $argsNum, 'like', '__likeOn') );
			else 
				throw new Exception( "Using <strong>Model::likeOn()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __notOn( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__where_on_operator'), array($args, $argsNum, 'not', '__notOn') );
			else 
				throw new Exception( "Using <strong>Model::notOn()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __notBetweenOn( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__between_on_operator'), array($args, $argsNum, 'not between') );
			else 
				throw new Exception( "Using <strong>Model::notBetweenOn()</strong> has a syntax error." ); 
		}
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	} 
	
	final protected function __notEqualOn( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__where_on_operator'), array($args, $argsNum, '!=', '__notEqualOn') );
			else 
				throw new Exception( "Using <strong>Model::notEqualOn()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	}
	
	final protected function __notInOn( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__where_on_operator'), array($args, $argsNum, 'not in', '__notInOn') );
			else 
				throw new Exception( "Using <strong>Model::notInOn()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	}
	
	final protected function __notLikeOn( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__where_on_operator'), array($args, $argsNum, 'not like', '__notLikeOn') );
			else 
				throw new Exception( "Using <strong>Model::notLikeOn()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __notNullOn( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__null_on_operator'), array($args, $argsNum, 'is not', '__notNullOn') );
			else 
				throw new Exception( "Using <strong>Model::notNullOn()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __between_on_operator( $args, $argsNum, $sign ) 
	{
		$twoArg = 2;
		$oneArg = 1; 
		$dispatcher = $this;
		if( $argsNum===$twoArg ) 
		{
			$tmp = $args[1]; 
			$args[1] = $sign; 
			$args[2] = $tmp;
			return call_user_func_array(array($dispatcher, '__whereOn'), array($args, count($args)));
		}
		else if($argsNum===$oneArg) 
		{
			$params = current($args); 
			foreach( $params as $param ) 
			{
				$tmp = array(key($param)); 
				$tmp[] = $sign; 
				$tmp[] = current($param);
				call_user_func_array(array($dispatcher, '__whereOn'), array($tmp, count($tmp)));
			}
			return $this;
		}
	}
	
	final protected function __in_on_operator( $args, $argsNum, $sign, $method ) 
	{
		$twoArg = 2; 
		$oneArg = 1; 
		$dispatcher = $this; 
		if( $twoArg===$argsNum ) 
		{
			$argsNum = 3;
			$tmp = $args[1];
			$args[1] = $sign;
			$args[2] = $tmp; 
			return call_user_func_array( array($dispatcher, '__whereOn'), array($args, $argsNum) ); 
		} 
		else if( $argsNum>$twoArg ) 
		{
			$tmp = array();
			$tmp[] = array_shift($args); 
			$tmp[] = $sign;
			$tmp[] = $args;
			return call_user_func_array( array($dispatcher, '__whereOn'), array($tmp, 3) ); 
		}
		else 
		{
			$params = current($args); 
			if( is_array($params) ) 
				foreach( $params as $args ) 
					call_user_func_array( array($dispatcher, $method), array([key($args), current($args)], $twoArg) ); 
			return $this; 
		}
	}
	
	final protected function __null_on_operator( $args, $argsNum, $sign, $method ) 
	{
		$twoArg = 2; 
		$oneArg = 1; 
		$dispatcher = $this; 
		if( $oneArg===$argsNum ) 
		{
			if( is_string($args[0]) ) 
			{
				$args[] = $sign; 
				$args[] = NULL;
				return call_user_func_array( array($dispatcher, '__whereOn'), array( $args, 3 ) );
			}
			else 
			{
				$args = current($args);
				foreach( $args as $arg ) 
					call_user_func_array( array($dispatcher, $method), array((array)$arg, 1) ); 
			}
		} 
		else if( $twoArg<= $argsNum ) 
			call_user_func_array( array($dispatcher, $method), array(array ($args), $oneArg) );
		return $this; 
	}
	
	final protected function __where_on_operator( $args, $argsNum, $sign, $method ) 
	{
		$twoArg = 2; 
		$oneArg = 1; 
		$dispatcher = $this;
		if( $oneArg===$argsNum ) 
		{
			$params = current($args); 
			if( is_array($params) ) 
			{
				$tmp = array();
				foreach( $params as $key => $value ) 
					if(is_string($key)) 
						call_user_func_array( array($dispatcher, $method), array([$key, $value], $twoArg) ); 
					else if( is_numeric($key) ) 
						$tmp[] = $value;
				if( !empty($tmp) ) 
					call_user_func_array( array($dispatcher, $method), array($tmp, count($tmp)) ); 
			}
		}
		else if( $twoArg<=$argsNum ) 
		{
			if( is_string($args[0]) ) 
			{
				$argsNum = 3;
				$tmp = $args[1];
				$args[1] = $sign;
				$args[2] = $tmp; 
				call_user_func_array( array($dispatcher, '__whereOn'), array($args, $argsNum) ); 
			} 
			else 
			{
				foreach( $args as $arg ) 
				{
					$argsNum = count($arg);
					$tmp = array(); 
					if( $oneArg===$argsNum ) 
					{
						$tmp[] = key($arg); 
						$tmp[] = $sign;
						$tmp[] = current($arg);
					} 
					else 
					{
						$tmp[] = $arg[0]; 
						$tmp[] = $sign;
						$tmp[] = $arg[1];
					}
					$arg = $tmp;
					call_user_func_array( array($dispatcher, '__whereOn'), array($arg, count($arg)) ); 
				}
			}
		} 
		return $this;
	}
	
	final protected function __whereOn( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
			{
				$threeArg = 3;
				$twoArg = 2; 
				$oneArg = 1; 
				$dispatcher = $this; 
				$params = array();
				if( $twoArg===$argsNum ) 
				{
					$tmp = $args[1]; 
					$args[1] = '='; 
					$args[2] = $tmp;
					$params[] = call_user_func_array( array($dispatcher, '__where'), array($args, $threeArg, true) ); 
				} 
				else if( $threeArg===$argsNum ) 
				{
					$params[] = call_user_func_array( array($dispatcher, '__where'), array($args, $threeArg, true) );
				} 
				else if( $threeArg<$argsNum ) 
				{
					$tmp = $args;
					$args = array(array_shift($tmp)); 
					$args[] = array_shift($tmp); 
					$args[] = $tmp; 
					$params[] = call_user_func_array( array($dispatcher, '__where'), array($args, $threeArg, true) );
				} 
				else 
				{
					$args = current($args);
					foreach($args as $arg) 
						call_user_func_array( array($dispatcher, '__whereOn'), array($arg, count($arg)) ); 
				}
				if( count($params) ) 
					foreach($params as $args) 
						$this->_propsCondOn[] = $args; 
			} 
			else 
				throw new Exception( "Using <strong>Model::whereOn()</strong> has a syntax error." ); 
			return $this; 
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __orBetween( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_between_operator'), array($args, $argsNum, 'between') );
			else 
				throw new Exception( "Using <strong>Model::orBetween()</strong> has a syntax error." ); 
		}
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	} 
	
	final protected function __orEqual( $args, $argsNum ) 
	{ 
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_where_operator'), array($args, $argsNum, '=', '__orEqual') );
			else 
				throw new Exception( "Using <strong>Model::orEqual()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __orGreater( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_where_operator'), array($args, $argsNum, '>', '__orGreater') );
			else 
				throw new Exception( "Using <strong>Model::orGreater()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __orGreaterThan( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_where_operator'), array($args, $argsNum, '>', '__orGreaterThan') );
			else 
				throw new Exception( "Using <strong>Model::orGreaterThan()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __orGreaterThanOrEqual( $args, $argsNum ) 
	{ 
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_where_operator'), array($args, $argsNum, '>=', '__orGreaterThanOrEqual') );
			else 
				throw new Exception( "Using <strong>Model::orGreaterThanOrEqualOn()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __orIn( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_where_operator'), array($args, $argsNum, 'in', '__orIn') );
			else 
				throw new Exception( "Using <strong>Model::orIn()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __orIs( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_where_operator'), array($args, $argsNum, 'is', '__orIs') );
			else 
				throw new Exception( "Using <strong>Model::orIs()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __orIsNot( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_where_operator'), array($args, $argsNum, 'is not', '__orIsNot') );
			else 
				throw new Exception( "Using <strong>Model::orIsNot()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __orIsNotNull( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_null_operator'), array($args, $argsNum, 'is not', '__orIsNotNull') );
			else 
				throw new Exception( "Using <strong>Model::orIsNotNull()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __orIsNull( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_null_operator'), array($args, $argsNum, 'is', '__orIsNull') );
			else 
				throw new Exception( "Using <strong>Model::orIsNull()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __orLess( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_where_operator'), array($args, $argsNum, '<', '__orLess') );
			else 
				throw new Exception( "Using <strong>Model::orLess()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __orLessThan( $args, $argsNum ) 
	{ 
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_where_operator'), array($args, $argsNum, '<', '__orLessThan') );
			else 
				throw new Exception( "Using <strong>Model::orLessThan()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __orLessThanOrEqual( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_where_operator'), array($args, $argsNum, '<=', '__orLessThanOrEqual') );
			else 
				throw new Exception( "Using <strong>Model::orLessThanOrEqual()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __orLike( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_where_operator'), array($args, $argsNum, 'like', '__orLike') );
			else 
				throw new Exception( "Using <strong>Model::orLike()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __orNot( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_where_operator'), array($args, $argsNum, 'not', '__orNot') );
			else 
				throw new Exception( "Using <strong>Model::orNot()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __orNotBetween( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_between_operator'), array($args, $argsNum, 'not between') );
			else 
				throw new Exception( "Using <strong>Model::orNotBetween()</strong> has a syntax error." ); 
		}
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	} 
	
	final protected function __orNotEqual( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_where_operator'), array($args, $argsNum, '!=', '__orNotEqual') );
			else 
				throw new Exception( "Using <strong>Model::orNotEqual()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	}
	
	final protected function __orNotIn( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_where_operator'), array($args, $argsNum, 'not in', '__orNotIn') );
			else 
				throw new Exception( "Using <strong>Model::orNotIn()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	}
	
	final protected function __orNotLike( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_where_operator'), array($args, $argsNum, 'not like', '__orNotLike') );
			else 
				throw new Exception( "Using <strong>Model::orNotLike()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __orNotNull( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_null_operator'), array($args, $argsNum, 'is not', '__orNotNull') );
			else 
				throw new Exception( "Using <strong>Model::orNotNull()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __or_between_operator( $args, $argsNum, $sign ) 
	{
		$twoArg = 2;
		$oneArg = 1; 
		$dispatcher = $this;
		if( $argsNum===$twoArg ) 
		{
			$tmp = $args[1]; 
			$args[1] = $sign; 
			$args[2] = $tmp;
			return call_user_func_array(array($dispatcher, '__orWhere'), array($args, count($args)));
		}
		else if($argsNum===$oneArg) 
		{
			$params = current($args); 
			foreach( $params as $param ) 
			{
				$tmp = array(key($param)); 
				$tmp[] = $sign; 
				$tmp[] = current($param);
				call_user_func_array(array($dispatcher, '__orWhere'), array($tmp, count($tmp)));
			} 
			return $this;
		}
	}
	
	final protected function __or_in_operator( $args, $argsNum, $sign, $method ) 
	{
		$twoArg = 2; 
		$oneArg = 1; 
		$dispatcher = $this; 
		if( $twoArg===$argsNum ) 
		{
			$argsNum = 3;
			$tmp = $args[1];
			$args[1] = $sign;
			$args[2] = $tmp; 
			return call_user_func_array( array($dispatcher, '__orWhere'), array($args, $argsNum) ); 
		} 
		else if( $argsNum>$twoArg ) 
		{
			$tmp = array();
			$tmp[] = array_shift($args); 
			$tmp[] = $sign;
			$tmp[] = $args;
			return call_user_func_array( array($dispatcher, '__orWhere'), array($tmp, 3) ); 
		}
		else 
		{
			$params = current($args); 
			if( is_array($params) ) 
				foreach( $params as $args ) 
					call_user_func_array( array($dispatcher, $method), array([key($args), current($args)], $twoArg) ); 
			return $this; 
		}
	}
	
	final protected function __or_null_operator( $args, $argsNum, $sign, $method ) 
	{
		$twoArg = 2; 
		$oneArg = 1; 
		$dispatcher = $this; 
		if( $oneArg===$argsNum ) 
		{
			if( is_string($args[0]) ) 
			{
				$args[] = $sign; 
				$args[] = NULL;
				return call_user_func_array( array($dispatcher, '__orWhere'), array( $args, 3 ) );
			}
			else 
			{
				$args = current($args);
				foreach( $args as $arg ) 
					call_user_func_array( array($dispatcher, $method), array((array)$arg, 1) );
			}
		} 
		else if( $twoArg<= $argsNum ) 
			call_user_func_array( array($dispatcher, $method), array(array ($args), $oneArg) );
		return $this; 
	}
	
	final protected function __or_where_operator( $args, $argsNum, $sign, $method ) 
	{
		$twoArg = 2; 
		$oneArg = 1; 
		$dispatcher = $this;
		if( $oneArg===$argsNum ) 
		{
			$params = current($args); 
			if( is_array($params) ) 
			{
				$tmp = array();
				foreach( $params as $key => $value ) 
					if(is_string($key)) 
						call_user_func_array( array($dispatcher, $method), array([$key, $value], $twoArg) ); 
					else if( is_numeric($key) ) 
						$tmp[] = $value;
				if( !empty($tmp) ) 
					call_user_func_array( array($dispatcher, $method), array($tmp, count($tmp)) ); 
			}
		}
		else if( $twoArg<=$argsNum ) 
		{
			if( is_string($args[0]) ) 
			{
				$argsNum = 3;
				$tmp = $args[1];
				$args[1] = $sign;
				$args[2] = $tmp; 
				call_user_func_array( array($dispatcher, '__orWhere'), array($args, $argsNum) ); 
			} 
			else 
			{
				foreach( $args as $arg ) 
				{
					$argsNum = count($arg);
					$tmp = array(); 
					if( $oneArg===$argsNum ) 
					{
						$tmp[] = key($arg); 
						$tmp[] = $sign;
						$tmp[] = current($arg);
					} 
					else 
					{
						$tmp[] = $arg[0]; 
						$tmp[] = $sign;
						$tmp[] = $arg[1];
					}
					$arg = $tmp;
					call_user_func_array( array($dispatcher, '__orWhere'), array($arg, count($arg)) ); 
				}
			}
		} 
		return $this; 
	}
	
	final protected function __orWhere( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
			{
				$threeArg = 3;
				$twoArg = 2; 
				$oneArg = 1; 
				$dispatcher = $this; 
				$params = array();
				if( $twoArg===$argsNum ) 
				{
					$tmp = $args[1]; 
					$args[1] = '='; 
					$args[2] = $tmp;
					$params[] = call_user_func_array( array($dispatcher, '__where'), array($args, $threeArg, true) ); 
				} 
				else if( $threeArg===$argsNum ) 
				{
					$params[] = call_user_func_array( array($dispatcher, '__where'), array($args, $threeArg, true) );
				} 
				else if( $threeArg<$argsNum ) 
				{
					$tmp = $args;
					$args = array(array_shift($tmp)); 
					$args[] = array_shift($tmp); 
					$args[] = $tmp; 
					$params[] = call_user_func_array( array($dispatcher, '__where'), array($args, $threeArg, true) );
				} 
				else 
				{
					$args = current($args);
					foreach($args as $arg) 
						call_user_func_array( array($dispatcher, '__orWhere'), array($arg, count($arg)) ); 
				}
				if( count($params) ) 
					foreach($params as $args) 
						$this->_propsCondOr[] = $args; 
			} 
			else 
				throw new Exception( "Using <strong>Model::orWhere()</strong> has a syntax error." ); 
			return $this;
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __orBetweenAnd( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_between_and_operator'), array($args, $argsNum, 'between') );
			else 
				throw new Exception( "Using <strong>Model::orBetweenAnd()</strong> has a syntax error." ); 
		}
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	} 
	
	final protected function __orEqualAnd( $args, $argsNum ) 
	{ 
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_where_and_operator'), array($args, $argsNum, '=') );
			else 
				throw new Exception( "Using <strong>Model::orEqualAnd()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __orGreaterAnd( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_where_and_operator'), array($args, $argsNum, '>') );
			else 
				throw new Exception( "Using <strong>Model::orGreaterAnd()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __orGreaterThanOrEqualAnd( $args, $argsNum ) 
	{ 
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_where_and_operator'), array($args, $argsNum, '>=') );
			else 
				throw new Exception( "Using <strong>Model::orGreaterThanOrEqualAnd()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __orInAnd( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_where_and_operator'), array($args, $argsNum, 'in') );
			else 
				throw new Exception( "Using <strong>Model::orInAnd()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __orIsAnd( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_where_and_operator'), array($args, $argsNum, 'is') );
			else 
				throw new Exception( "Using <strong>Model::orIsAnd()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __orIsNotAnd( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_where_and_operator'), array($args, $argsNum, 'is not') );
			else 
				throw new Exception( "Using <strong>Model::orIsNotAnd()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __orIsNotNullAnd( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_null_and_operator'), array($args, $argsNum, 'is not') );
			else 
				throw new Exception( "Using <strong>Model::orIsNotNullAnd()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __orIsNullAnd( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_null_and_operator'), array($args, $argsNum, 'is') );
			else 
				throw new Exception( "Using <strong>Model::orIsNullAnd()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __orLessAnd( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_where_and_operator'), array($args, $argsNum, '<') );
			else 
				throw new Exception( "Using <strong>Model::orLessAnd()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __orLessThanAnd( $args, $argsNum ) 
	{ 
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_where_and_operator'), array($args, $argsNum, '<') );
			else 
				throw new Exception( "Using <strong>Model::orLessThanAnd()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __orLessThanOrEqualAnd( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_where_and_operator'), array($args, $argsNum, '<=') );
			else 
				throw new Exception( "Using <strong>Model::orLessThanOrEqualAnd()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __orLikeAnd( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_where_and_operator'), array($args, $argsNum, 'like') );
			else 
				throw new Exception( "Using <strong>Model::orLikeAnd()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __orNotAnd( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_where_and_operator'), array($args, $argsNum, 'not') );
			else 
				throw new Exception( "Using <strong>Model::orNotAnd()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __orNotBetweenAnd( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_between_and_operator'), array($args, $argsNum, 'not between') );
			else 
				throw new Exception( "Using <strong>Model::orBetweenAnd()</strong> has a syntax error." ); 
		}
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	} 
	
	final protected function __orNotNullAnd( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_null_and_operator'), array($args, $argsNum, 'is not') );
			else 
				throw new Exception( "Using <strong>Model::orNotNullAnd()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __orNotEqualAnd( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_where_and_operator'), array($args, $argsNum, '!=') );
			else 
				throw new Exception( "Using <strong>Model::orNotEqualAnd()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	}
	
	final protected function __orNotInAnd( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_where_and_operator'), array($args, $argsNum, 'not in') );
			else 
				throw new Exception( "Using <strong>Model::orNotInAnd()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	}
	
	final protected function __orNotLikeAnd( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
				return call_user_func_array( array($this, '__or_where_and_operator'), array($args, $argsNum, 'not like') );
			else 
				throw new Exception( "Using <strong>Model::orNotLikeAnd()</strong> has a syntax error." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	}
	
	final protected function __or_between_and_operator( $args, $argsNum, $sign ) 
	{
		$oneArg = 1; 
		$dispatcher = $this; 
		if( $oneArg===$argsNum ) 
			$params = current($args); 
		else 
			$params = $args; 
		$args = array();
		foreach( $params as $arg ) 
		{
			$param = array(key($arg)); 
			$param[] = $sign; 
			$param[] = current($arg);
			$args [] = $param;
		}
		return call_user_func_array( array($dispatcher, '__orWhereAnd'), array($args, count($args)) );
	}
	
	final protected function __or_null_and_operator( $args, $argsNum, $sign ) 
	{
		$oneArg = 1; 
		$dispatcher = $this; 
		$oneArg = 1; 
		$dispatcher = $this; 
		$tmps = array(); 
		if( $oneArg<$argsNum ) 
			foreach( $args as $arg ) 
				$tmps[] = array($arg, $sign, NULL); 
		else 
		{
			$args = current($args);
			foreach( $args as $key ) 
			{
				$tmps[] = array($key, $sign, NULL); 
			}
		}
		return call_user_func_array( array($dispatcher, '__orWhereAnd'), array($tmps, count($tmps)) ); 
	}
	
	final protected function __or_where_and_operator( $args, $argsNum, $sign ) 
	{
		$oneArg = 1; 
		$dispatcher = $this; 
		$tmps = array(); 
		if( $oneArg<$argsNum ) 
			foreach( $args as $arg )
				$tmps[] = array(key($arg), $sign, current($arg)); 
		else 
		{
			$args = current($args);
			foreach( $args as $key => $value ) 
				$tmps[] = array($key, $sign, $value); 
		}
		return call_user_func_array( array($dispatcher, '__orWhereAnd'), array($tmps, count($tmps)) ); 
	}
	
	final protected function __orWhereAnd( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
			{
				$threeArg = 3; 
				$twoArg = 2; 
				$oneArg = 1; 
				$dispatcher = $this; 
				if( $oneArg===$argsNum ) 
				{
					$params = current($args); 
					if( is_array($params) ) 
					{
						$argsNum = count($params);
						if( $oneArg>=$argsNum ) 
							throw new Exception( "Using <strong>Model::orWhereAnd()</strong> has a syntax error." ); 
					} 
					else 
						throw new Exception( "Using <strong>Model::orWhereAnd()</strong> has a syntax error." ); 
				} 
				else 
					$params = $args; 
				
				$tmps = array(); 
				foreach( $params as $key => $arg ) 
				{
					if( is_string($key) ) 
					{
						$tmp = $arg; 
						$arg = array(
							$key, 
							'=', 
							$tmp 
						);
					}
					else if( $twoArg===count($arg) ) 
					{
						$tmp = $arg[1]; 
						$arg[1] = '='; 
						$arg[2] = $tmp; 
					} 
					else if( $oneArg===count($arg) )
					{
						$tmp = $arg; 
						$arg = array(
							key($tmp), 
							'=', 
							current($tmp) 
						);
					}
					$args = array($arg);
					$args[] = count($arg); 
					$args[] = true;
					$tmps[] = call_user_func_array( array($dispatcher, '__where'), $args ); 
				}
				if( !empty($tmps) )
					$this->_propsCondOr[] = array('AND'=>$tmps); 
			} 
			else 
				throw new Exception( "Using <strong>Model::orWhereAnd()</strong> has a syntax error." ); 
			return $this; 
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __groupBy( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
			{
				if( 1===$argsNum ) 
				{
					$args = current($args); 
					
					if( is_string($args) ) 
					{
						
						$this->_propsGroupBy[] = $this->__parseField($args); 
						return $this;
					} 
				} 
				foreach( $args as $arg ) 
				{
					if( is_string($arg) )
						call_user_func_array( array($this, '__groupBy'), array([$arg], 1) );
					else if( is_array($arg) )
						call_user_func_array( array($this, '__groupBy'), array($arg, count($arg)) );
				} 
				return $this; 
			}
			else 
				throw new Exception( "Usage <strong>Model::groupBy()</strong> is incorrect." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		}
	} 
	
	final protected function __orderDesc( $field ) 
	{ 
		$this->_propsOrder[] = array(
			'name' 	=> $field, 
			'orient'	=> "DESC" 
		); 
		return $this; 
	} 
	
	final protected function __orderAsc( $field ) 
	{ 
		$this->_propsOrder[] = array(
			'name' 	=> $field, 
			'orient'	=> "ASC" 
		); 
		return $this; 
	} 
	
	final protected function __parseField( $field ) 
	{
		$patt = '#^([\w\d]+)\(([\w\d]+)\)#';
		if( preg_match($patt, $field, $matches) )  
		{
			return array( 
				'cmd'	=> strtoupper($matches[1]), 
				'name'	=> $matches[2]
			); 
		} 
		return array('name'=>$field); 
	}
	
	final protected function __orderBy( $args, $argsNum ) 
	{ 
		try 
		{ 
			if( $argsNum ) 
			{ 
				$twoArg = 2; 
				$oneArg = 1; 
				$dispatcher = $this; 
				if( $oneArg===$argsNum ) 
				{
					$tmp = current($args); 
					if( is_string( $tmp ) ) 
					{
						$orient = 'ASC';
						$field = $this->__parseField($args[0]); 
						$this->_propsOrder[] = array_merge(array('orient' => $orient), $field); 
						goto ORDER_BREAK;
					} 
					else 
						goto ORDER_ARR;
				} 
				else if( $twoArg===$argsNum ) 
				{
					$b0 = is_string($args[0]);
					$b1 = is_string($args[1]);
					if( $b0&&$b1 ) 
					{
						$orient = strtoupper($args[1]);
						$field = $this->__parseField($args[0]); 
						$this->_propsOrder[] = array_merge(array('orient' => $orient), $field); 
						goto ORDER_BREAK;
					} 
					else 
						goto ORDER_ARR;
				}
				
				ORDER_ARR:
				foreach($args as $arg) 
					call_user_func_array( array($dispatcher, '__orderBy'), array($arg, count($arg)) ); 
				ORDER_BREAK:
			} 
			else 
				throw new Exception( "Usage <strong>Model::orderBy()</strong> is incorrect." );
			return $this;
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	}
	
	final protected function __setLimit( $args, $argsNum ) 
	{
		$this->_propLimit = (int) current($args); 
		if( is_null($this->_propPage) ) 
			$this->_propPage = 1; 
		return $this;
	} 

	final protected function __setSeek( $args, $argsNum ) 
	{
		$this->_propOffset = (int) current($args); 
		return $this;
	}
	
	final protected function __setPage( $args, $argsNum ) 
	{
		$this->_propPage = (int) current($args); 
		return $this;
	} 
	
	final protected function __require( $model ) 
	{
		global $_config;
		if( isset($_config['require_seek']) && $_config['require_seek'] ) 
			return null;
		
		if( method_exists($this, $model) ) 
			return call_user_func_array(array($this, $model), array()); 
		return NULL;
	}
	
	final protected function __renameHasOne( $args, $argsNum ) 
	{
		try 
		{
			$twoArg = 2;
			if( $twoArg===$argsNum ) 
			{
				if( array_key_exists($args[head], $this->_propsHasOne) )
				{
					$tmp = $this->_propsHasOne[$args[head]];
					unset($this->_propsHasOne[$args[head]]);
					$this->_propsHasOne[$args[1]] = $tmp;
				} 
				return $this; 
			} 
			else 
				throw new Exception( "Usage <strong>Model::renameHasOne()</strong> is incorrect." ); 
			
		}
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	}
	
	protected function __computeSteadModel( $args, $flag=NULL ) 
	{
		return new StdModel($args, $flag); 
	}
	
	final protected function __orderHasOne( $args, $argsNum ) 
	{
		global $_config;
		try 
		{
			if( $argsNum ) 
			{
				$fourArg = 4; 
				$oneArg = 1; 
				$zeroArg = 0;
				if( $oneArg===$argsNum ) 
				{
					$model = current($args);
					if( array_key_exists($model, $this->_propsHasOne) ) 
						return $this->_propsHasOne[$model]; 
					else 
						return;
				}
				else if( $fourArg===$argsNum ) 
				{
					if( array_key_exists($args[head], $this->_propsHasOne) ) 
						return $this->_propsHasOne[ $args[head] ]; 
					
					$args[] = $this->_propModel;
					$args[] = $this->_propPrefix;
					$_config["require_hasone"] = 1;
					$model = $this->__computeSteadModel( $args, true );
					unset($_config["require_hasone"]);
					$this->_propsHasOne += array( $args[head]=>$model );
					return $model;
				}
				else
					throw new Exception( "Usage <strong>Model::hasOne()</strong> is incorrect." ); 
			} 
			else 
				throw new Exception( "Usage <strong>Model::hasOne()</strong> is incorrect." ); 
			return $this;
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		} 
	} 
	
	final protected function __showHasOne() { $this->_flagHasOne = true; return $this; } 
	final protected function __hideHasOne() { $this->_flagHasOne = false; return $this; } 
	
	final protected function __renameHasMany( $args, $argsNum ) 
	{
		try 
		{
			$twoArg = 2;
			if( $twoArg===$argsNum ) 
			{
				if( array_key_exists($args[head], $this->_propsHasMany) )
				{
					$tmp = $this->_propsHasMany[$args[head]];
					unset($this->_propsHasMany[$args[head]]);
					$this->_propsHasMany[$args[1]] = $tmp;
				} 
				return $this; 
			} 
			else 
				throw new Exception( "Usage <strong>Model::renameHasMany()</strong> is incorrect." ); 
			
		}
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	}
	
	final protected function __orderHasMany( $args, $argsNum ) 
	{ 
		try 
		{
			if( $argsNum ) 
			{
				$fourArg = 4; 
				$oneArg = 1; 
				$zeroArg = 0; 
				if( $oneArg===$argsNum ) 
				{
					$model = current($args);
					if( array_key_exists($model, $this->_propsHasMany) ) 
						return $this->_propsHasMany[$model];
					else 
						return;
				}
				else if( $fourArg===$argsNum ) 
				{
					if( array_key_exists($args[head], $this->_propsHasMany) ) 
						return $this->_propsHasMany[ $args[head] ]; 
					
					$args[] = $this->_propTable;
					$args[] = $this->_propPrefix;
					$model = $this->__computeSteadModel( $args ); 
					$this->_propsHasMany += array( $args[head]=>$model );
					return $model;
				}
				else
					throw new Exception( "Usage <strong>Model::hasMany()</strong> is incorrect." ); 
			}
			else 
				throw new Exception( "Usage <strong>Model::hasMany()</strong> is incorrect." ); 
			return $this;
		}
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		} 
	} 
	
	final protected function __showHasMany() { $this->_flagHasMany = true; return $this; }
	final protected function __hideHasMany() { $this->_flagHasMany = false; return $this; } 

	final protected function __renameHasMABTM( $args, $argsNum ) 
	{
		try 
		{
			$twoArg = 2;
			if( $twoArg===$argsNum ) 
			{
				if( array_key_exists($args[head], $this->_propsHasMABTM) )
				{
					$tmp = $this->_propsHasMABTM[$args[head]];
					unset($this->_propsHasMABTM[$args[head]]);
					$this->_propsHasMABTM[$args[1]] = $tmp;
				}
				return $this; 
			} 
			else 
				throw new Exception( "Usage <strong>Model::renameHasMABTM()</strong> is incorrect." ); 
		}
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	}
	
	final protected function __orderHasMABTM( $args, $argsNum ) 
	{
		global $_inflect; 
		try 
		{
			if( $argsNum ) 
			{ 
				$fiveArg = 5; 
				$sixArg = 6; 
				$oneArg = 1; 
				$zeroArg = 0; 
				if( $oneArg===$argsNum ) 
				{
					$model = current($args); 
					if( array_key_exists($model, $this->_propsHasMABTM) ) 
						return $this->_propsHasMABTM[$model][mhd];
					else 
						return; 
				} 
				else if( $fiveArg===$argsNum ) 
				{
					if( array_key_exists($args[head], $this->_propsHasMABTM) ) 
						return $this->_propsHasMABTM[$args[head]][mhd]; 
					
					$data = array( $args[0], $args[1], $args[2], $args[4], $this->_propTable, $this->_propPrefix ); 
					$dataModel = new StdModel( $data ); 
					$dataAlias = explode(mad, $dataModel->getAliasName()); 
					$mainAlias = explode(mad, $this->getAliasName()); 
					$alias = array_merge($dataAlias, $mainAlias); 
					sort($alias); 
					foreach( $alias as $key => $word ) 
						$alias[$key] = $_inflect->singularize(strtolower($word)); 
					$joinModel = array(
						'md_name' => implode(EMPTY_CHAR, $alias), 
						'as_name' => implode(mad, $alias), 
						'fk_main' => $args[3], 
						'fk_data' => $args[2], 
					);
					$model = array(
						mhd => $dataModel, 
						mhj => $joinModel, 
					); 
					$this->_propsHasMABTM += array( $args[head]=>$model ); 
					return $dataModel; 
				} 
				else if( $sixArg===$argsNum ) 
				{
					if( array_key_exists($args[head], $this->_propsHasMABTM) ) 
						return $this->_propsHasMABTM[$args[head]][mhd]; 
					$data = array( $args[0], $args[1], $args[2], $args[5], $this->_propTable, $this->_propPrefix ); 
					$dataModel = new StdModel( $data ); 
					$alias = explode(mad, $args[3]); 
					sort($alias); 
					foreach( $alias as $key => $word ) 
						$alias[$key] = $_inflect->singularize(strtolower($word)); 
					$joinModel = array(
						'md_name' => implode(EMPTY_CHAR, $alias), 
						'as_name' => implode(mad, $alias), 
						'fk_main' => $args[4], 
						'fk_data' => $args[2], 
					);
					$model = array(
						mhd => $dataModel, 
						mhj => $joinModel, 
					); 
					$this->_propsHasMABTM += array( $args[head]=>$model ); 
					return $dataModel; 
				}
				else 
					throw new Exception( "Usage <strong>Model::hasManyAndBelongsToMany()</strong> is incorrect." ); 
			} 
			else 
				throw new Exception( "Usage <strong>Model::hasManyAndBelongsToMany()</strong> is incorrect." ); 
		}
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	} 

	final protected function __showHasManyAndBelongsToMany() { $this->_flagHasMABTM = true; return $this; } 
	final protected function __hideHasManyAndBelongsToMany() { $this->_flagHasMABTM = false; return $this; } 
	
	final protected function __orderImport( $model ) { $this->_propsImport[] = current($model); return $this; } 
	final protected function __orderImportAll( $model ) { $this->_propsImportAll[] = current($model); return $this; } 
	
	final protected function __orderMerge( $args, $argsNum ) 
	{
		try 
		{
			$fourArg = 4; 
			$fiveArg = 5; 
			if( $fourArg===$argsNum ) 
			{
				if( array_key_exists($args[head], $this->_propsMerge) ) 
					return $this->_propsMerge[$args[head]];
				$args[] = $this->_propModel;
				$args[] = $this->_propPrefix;
				$model = $this->__computeSteadModel( $args ); 
				$this->_propsMerge += array( $args[head]=>$model );
				return $model;
			} 
			else if( $fiveArg===$argsNum ) 
			{
				$cb = array_pop($args);
				$merge = $this->__orderMerge( $args, count($args) );
				$cb($merge);
			}
			else 
				throw new Exception( "Usage <strong>Model::merge()</strong> is incorrect." ); 
		}
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		}
	} 
	
	final protected function __orderMergeLeft( $args, $argsNum ) 
	{
		try 
		{
			$fourArg = 4; 
			if( $fourArg===$argsNum ) 
			{
				if( array_key_exists($args[head], $this->_propsMergeLeft) ) 
					return $this->_propsMergeLeft[$args[head]]; 
				$args[] = $this->_propModel;
				$args[] = $this->_propPrefix; 
				$model = $this->__computeSteadModel( $args ); 
				$this->_propsMergeLeft += array( $args[head]=>$model ); 
				return $model;
			} 
			else 
				throw new Exception( "Usage <strong>Model::mergeLeft()</strong> is incorrect." ); 
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() ); 
		} 
	} 
	
	final protected function __orderMergeRight( $args, $argsNum ) 
	{
		try 
		{ 
			$fourArg = 4;
			if( $fourArg===$argsNum ) 
			{ 
				if( array_key_exists($args[head], $this->_propsMergeRight) ) 
					return $this->_propsMergeRight[$args[head]]; 
				$args[] = $this->_propModel;
				$args[] = $this->_propPrefix; 
				$model = $this->__computeSteadModel( $args ); 
				$this->_propsMergeRight += array( $args[head]=>$model ); 
				return $model; 
			} 
			else 
				throw new Exception( "Usage <strong>Model::mergeRight()</strong> is incorrect." ); 
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() ); 
		} 
	} 
	
	final protected function __getError() 
	{
		return array( 
			'error_msg'	=>$this->error(), 
			'error_no'	=>$this->errno() 
		); 
	} 
	
	final protected function __genRandString( $max_len = 10 ) 
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
	
	final protected function __mergeTable() 
	{ 
		if( EMPTY_CHAR===$this->_propAlias && isset($this->_alias) )
			$this->_propAlias = $this->_alias; 
		if( EMPTY_CHAR===$this->_propTable && isset($this->_table) )
			$this->_propTable = $this->_propPrefix.$this->_table; 
		if( EMPTY_CHAR===$this->_propModel && isset($this->_model) )
			$this->_propModel = $this->_model; 
		if( zero===$this->_propUnits && isset($this->_units) ) 
		{
			$this->_propUnits = $this->_units; 
			$this->_propUnitOrigin = $this->_propTable;
		}
		return $this;
	} 
	
	protected function __setupModel() 
	{
		$this->__fetchCacheColumns(); 
	} 
	
	protected function __fetchCacheColumns() 
	{
		if( empty($this->_propsDescribe) )
			$this->_propsDescribe = $this->__parseDescribe($this->_propTable); 
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
	
	final protected function __transaction( $args ) 
	{
		if( count($args) ) 
		{
			$dispatcher = current($args);
			if( is_callable($dispatcher) ) 
			{
				call_user_func_array(array($this, '__startTransaction'), array()); 
				$this->_flagIsolate = true;
				$dispatcher($this); 
				$this->_flagIsolate = false;
				return call_user_func_array(array($this, '__commit'), array()); 
			} 
			else if( is_string($dispatcher) ) 
			{ 
				switch($dispatcher) 
				{ 
					case 'rollback': 
						$this->_flagCheckpoint = false;
						return call_user_func_array(array($this, '__rollbackTransaction'), array()); 
					case 'commit': 
						$this->_flagCheckpoint = false;
						return call_user_func_array(array($this, '__commitTransaction'), array());
					case 'start': 
					default:
						$this->_flagCheckpoint = true; 
						call_user_func_array(array($this, '__startTransaction'), array()); 
						return call_user_func_array(array($this, '__createCheckpoint'), $args);  
				} 
			} 
		} 
		return call_user_func_array(array($this, '__startTransaction'), array()); 
	}
	
	final protected function __startTransaction() 
	{
		$sql = $this->__parseSqlStartTransaction();
		$result = $this->__query( $sql );
		return $this; 
	} 
	
	final protected function __commitTransaction() 
	{
		$sql = $this->__parseSqlCommitTransaction(); 
		$result = $this->__query( $sql ); 
		return $this; 
	} 
	
	final protected function __rollbackTransaction() 
	{ 
		$sql = $this->__parseSqlRollbackTransaction(); 
		$result = $this->__query( $sql ); 
		return $this; 
	} 
	
	final protected function __rollbackCheckpoint( $pt ) 
	{ 
		$sql = $this->__parseSqlRollbackCheckpoint( $pt ); 
		$result = $this->__query( $sql ); 
		return $this; 
	} 
	
	final protected function __releaseCheckpoint( $pt ) 
	{ 
		$sql = $this->__parseSqlReleaseCheckpoint( $pt ); 
		$result = $this->__query( $sql ); 
		return $this; 
	} 
	
	final protected function __createCheckpoint( $pt ) 
	{ 
		$sql = $this->__parseSqlCreateCheckpoint( $pt ); 
		$result = $this->__query( $sql ); 
		return $this; 
	} 

	final protected function __setAliasName( $value ) 
	{
		if( __useDB() ) 
			$this->_propAlias = $value;
		return $this;
	}
	
	final protected function __setModelName( $value ) 
	{
		if( __useDB() ) 
		{
			$this->_propModel = $value; 
			foreach( $this->_propsHasOne as $m ) 
			{
				$m->setAliasModel($value); 
			}
		}
		return $this;
	}
	
	final protected function __setTableName( $value ) 
	{
		if( __useDB() ) 
		{
			$this->_propTable = $this->_propPrefix.$value; 
			if( isset($this->_units) ) 
			{
				$this->_propUnitOrigin = $this->_propTable;
			}
		}
		return $this;
	} 
	
	final protected function __setUnitsSize( $value ) 
	{ 
		if( __useDB() ) 
			$this->_propUnits = $value; 
		return $this;
	} 
	
	final public function FuildSqlCondation() 
	{
		return $this->__buildSqlCondition( NULL, true ); 
	}
	
	final protected function __buildHasOneSqlCondation() 
	{
		$outSql = EMPTY_CHAR;
		foreach( $this->_propsHasOne as $model ) 
		{
			$outSql .= $model->fuildSqlCondation(); 
		} 
		return $outSql; 
	} 
	
	final protected function __buildMergeSqlCondation() 
	{
		$outSql = EMPTY_CHAR;
		foreach( $this->_propsMerge as $model ) 
		{
			$outSql .= $model->fuildSqlCondation(); 
		} 
		return $outSql; 
	} 
	
	final protected function __buildMergeRSqlCondation() 
	{
		$outSql = EMPTY_CHAR;
		foreach( $this->_propsMergeRight as $model ) 
		{
			$outSql .= $model->fuildSqlCondation(); 
		} 
		return $outSql; 
	} 
	
	final protected function __buildMergeLSqlCondation() 
	{
		$outSql = EMPTY_CHAR;
		foreach( $this->_propsMergeLeft as $model ) 
		{
			$outSql .= $model->fuildSqlCondation(); 
		} 
		return $outSql; 
	}
	
	final public function FluidSqlGroup() 
	{
		return $this->__buildSqlGroup(); 
	} 
	
	final public function FluidSqlOrder() 
	{
		return $this->__buildSqlOrder( true ); 
	} 
	
	final protected function __buildHasOneSqlGroup() 
	{
		$outSql = EMPTY_CHAR;
		foreach( $this->_propsHasOne as $model ) 
		{
			$outSql .= $model->fluidSqlGroup(); 
		} 
		return $outSql; 
	} 
	
	final protected function __buildMergeSqlGroup() 
	{
		$outSql = EMPTY_CHAR;
		foreach( $this->_propsMerge as $model ) 
		{
			$outSql .= $model->fluidSqlGroup(); 
		} 
		return $outSql; 
	} 
	
	final protected function __buildMergeRSqlGroup() 
	{
		$outSql = EMPTY_CHAR;
		foreach( $this->_propsMergeRight as $model ) 
		{
			$outSql .= $model->fluidSqlGroup(); 
		} 
		return $outSql; 
	} 
	
	final protected function __buildMergeLSqlGroup() 
	{
		$outSql = EMPTY_CHAR;
		foreach( $this->_propsMergeLeft as $model ) 
		{
			$outSql .= $model->fluidSqlGroup(); 
		} 
		return $outSql; 
	} 
	
	final protected function __buildHasOneSqlOrder() 
	{
		$outSql = EMPTY_CHAR;
		foreach( $this->_propsHasOne as $model ) 
		{
			$outSql .= $model->fluidSqlOrder(); 
		} 
		return $outSql; 
	} 
	
	final protected function __buildMergeSqlOrder() 
	{
		$outSql = EMPTY_CHAR;
		foreach( $this->_propsMerge as $model ) 
		{
			$outSql .= $model->fluidSqlOrder(); 
		} 
		return $outSql; 
	} 
	
	final protected function __buildMergeRSqlOrder() 
	{
		$outSql = EMPTY_CHAR;
		foreach( $this->_propsMergeRight as $model ) 
		{
			$outSql .= $model->fluidSqlOrder(); 
		} 
		return $outSql; 
	} 
	
	final protected function __buildMergeLSqlOrder() 
	{
		$outSql = EMPTY_CHAR;
		foreach( $this->_propsMergeLeft as $model ) 
		{
			$outSql .= $model->fluidSqlOrder(); 
		} 
		return $outSql; 
	} 
	
	final protected function __buildSqlQuery() 
	{
		$selectSql = $this->__buildSQLSelection(); 
		$fromSql = $this->__buildSqlFrom(); 
		$condSql = $this->__buildSqlCondition(); 
		$condHasOneSql = $this->__buildHasOneSqlCondation();
		$condMergeSql = $this->__buildMergeSqlCondation();
		$condMergeRSql = $this->__buildMergeRSqlCondation();
		$condMergeLSql = $this->__buildMergeLSqlCondation(); 
		$groupSql = $this->__buildSqlGroup(); 
		$groupHasOneSql = $this->__buildHasOneSqlGroup();
		$groupMergeSql = $this->__buildMergeSqlGroup();
		$groupMergeRSql = $this->__buildMergeRSqlGroup();
		$groupMergeLSql = $this->__buildMergeLSqlGroup(); 
		$orderSql = $this->__buildSqlOrder(); 
		$orderHasOneSql = $this->__buildHasOneSqlOrder(); 
		$orderMergeSql = $this->__buildMergeSqlOrder(); 
		$orderMergeRSql = $this->__buildMergeRSqlOrder(); 
		$orderMergeLSql = $this->__buildMergeLSqlOrder(); 
		$rangeSql = $this->__buildSqlRange(); 
		$importSql = $this->__buildSqlImport(); 
		$importOnceSql = $this->__buildSqlImportOnce(); 
		
		return $selectSql 
		. $fromSql 
		. $condSql 
		. $condHasOneSql 
		. $condMergeSql 
		. $condMergeRSql 
		. $condMergeLSql 
		. $groupSql 
		. $groupHasOneSql
		. $groupMergeSql
		. $groupMergeRSql
		. $groupMergeLSql
		. $orderSql 
		. $orderHasOneSql
		. $orderMergeSql
		. $orderMergeRSql 
		. $orderMergeLSql
		. $rangeSql 
		. $importSql 
		. $importOnceSql; 
	} 
	
	final protected function __clear( $deep=false ) 
	{
		// Be keep to forward the counting
		if( $deep ) 
		{
			foreach( $this->_propsDescribe as $field ) 
				$this->$field = NULL;
			$this->_querySQL = NULL; 
			$this->_querySQLs = array();
			$this->_propLimit = NULL;
			$this->_propOffset = 0;
			if( isset($this->_propQuery) ) 
				$this->_propQuery = NULL; 
			if( isset($this->_propsQuery) ) 
				$this->_propsQuery = array(); 
		}
		$this->_propsUndescribe = $this->__fetchCacheColumns(); 
		$this->_propsCond 		= array(); 
		$this->_propsCondEx		= array(); 
		$this->_propsCondOr 	= array(); 
		$this->_propsCondOn 	= array();
		$this->_propsCondCmd 	= array();
		$this->_propsGroupBy	= array();
		$this->_propPage		= NULL;
		$this->_flagHasOne 		= false;
		$this->_flagHasMany 	= false; 
		$this->_flagHasMABTM 	= false; 
		$this->_propsImport		= array();
		$this->_propsImportAll	= array();
		$this->_propsMerge		= array(); 
		$this->_propsMergeLeft	= array(); 
		$this->_propsMergeRight	= array(); 
		$this->_propsOrder 		= array();
		if( isset($this->_propsProjection) ) 
		{
			$this->_propsProjection = array(); 
		}
		
		return $this;
	} 
	
	final protected function __logsql( $sql ) 
	{
		$this->_querySQL = $sql;
		$this->_querySQLs[] = $sql; 
	} 
	
	final protected function __detach_model( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
			{
				if(1===$argsNum) 
				{
					$args = current($args); 
					if( is_string($args) ) 
					{
						array_push($this->_propsDeathMdl, $args); 
						return $this;
					} 
				}
				$this->_propsDeathMdl = array_merge($this->_propsDeathMdl, $args); 
			} 
			else 
			{
				throw new Exception( "Usage <strong>Model::Detach()</strong> is incorrect." ); 
			}
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		} 
		return $this; 
	}
	
	final protected function __commit() 
	{ 
		if( $this->_flagIsolate ) 
		{
			call_user_func_array(array($this, '__commitTransaction'), array()); 
			return call_user_func_array(array($this, '__startTransaction'), array()); 
		} 
		if( $this->_flagCheckpoint ) 
		{
			return $this;
		}
		return call_user_func_array(array($this, '__commitTransaction'), array()); 
	} 
	
	final protected function __rollback( $args ) 
	{ 
		if( 1===count($args) && is_string(current($args)) ) 
		{ 
			return call_user_func_array(array($this, '__rollbackCheckpoint'), $args); 
		}
		if( $this->_flagIsolate ) 
		{ 
			call_user_func_array(array($this, '__rollbackTransaction'), array()); 
			return call_user_func_array(array($this, '__startTransaction'), array()); 
		} 
		if( $this->_flagCheckpoint ) 
		{
			return $this;
		}
		return call_user_func_array(array($this, '__rollbackTransaction'), array()); 
	} 
	
	final protected function __release( $args ) 
	{ 
		return call_user_func_array(array($this, '__releaseCheckpoint'), $args); 
	} 
	
	final protected function __checkpoint( $args ) 
	{ 
		return call_user_func_array(array($this, '__createCheckpoint'), $args); 
	} 
	
	final protected function __role( $args, $argsNum ) 
	{ 
		try 
		{
			$twoArg = 2;
			$oneArg = 1; 
			if( $oneArg===$argsNum ) 
			{
				$values = current($args); 
				if( is_array($values) ) 
					foreach( $values as $key => $value ) 
					{
						$this->_propsRole[$key] = $value; 
					}
				else if( is_string($values) ) 
					return $this->_propsRole[$values]; 
				else 
					throw new Exception( "Usage <strong>Model::role()</strong> is incorrect." ); 
			} 
			else if( $twoArg===$argsNum ) 
			{ 
				$this->_propsRole[$args[0]] = $args[1]; 
			} 
			else 
			{
				return $this->_propsRole; 
			} 
			return $this;
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() );
		}
	} 
	
	final protected function __unit( $args, $argsNum ) 
	{ 
		try 
		{
			if( $this->_propUnits ) 
			{
				if( 1!==$argsNum ) 
				{ 
					throw new Exception("The Model::Unit() need 1 parameter in Number type only."); 
				} 
				
				$unitCurr = current($args); 
				$rstUnit = $unitCurr%$this->_propUnits; 
				if( $rstUnit ) 
				{ 
					$this->_propTable = $this->_propUnitOrigin.underscore.$rstUnit; 
				} 
				else 
				{ 
					$this->_propTable = $this->_propUnitOrigin; 
				} 
			}
			else 			
			{
				throw new Exception("Can't use <b>Model::Unit()</b> function on a model have no extra units."); 
			}
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage().BL.\Zuuda\Error::Position($e) );
		} 
		return $this; 
	} 
	
	public function __get( $name ) 
	{
		if( $name === 'data' ) 
			return $this->_propsRole; 
	} 
	
	final protected function __totalPages( $args, $argsNum ) 
	{
		try 
		{
			if( $this->_propLimit ) 
			{
				$limit = $this->_propLimit; 
				$count = call_user_func_array(array($this, '__total'), array([], 0)); 
				return (int) ceil( $count/$limit ); 
			} 
		}
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	} 
	
	final protected function __paginate( $args, $argsNum ) 
	{
		try 
		{
			if( 1===$argsNum ) 
			{
				$options = current($args); 
			} 
			elseif( 2===$argsNum ) 
			{ 
				$options = array(
					'page'	=> $args[0], 
					'limit'	=> $args[1] 
				); 
			} 
			
			if( isset($options['page']) ) 
			{
				$page = $options['page']; 
				call_user_func_array( [$this, mcbm_set_page], array([$page], 1) ); 
			}
			elseif( is_null($this->_propPage) ) 
			{
				$page = 1; 
				call_user_func_array( [$this, mcbm_set_page], array([$page], 1) ); 
			} 
			else 
			{
				$page = $this->_propPage;
			}
			if( isset($options['limit']) ) 
			{
				$limit = $options['limit'];
				call_user_func_array( [$this, mcbm_set_limit], array([$limit], 1) );
			}
			elseif( is_null($this->_propLimit) ) 
			{
				$limit = 1000; 
				call_user_func_array( [$this, mcbm_set_limit], array([$limit], 1) ); 
			} 
			else 
			{
				$limit = $this->_propLimit; 
			} 
			
			$data = call_user_func_array( [$this, mcbm_search], array([], 0) ); 
			$total = call_user_func_array( [$this, mcbm_total], array([], 0) ); 
			$pages = (int) ceil( $total/$limit ); 
			
			return array(
				'pages'	=> $pages, 
				'total' => $total, 
				'data'	=> $data,
				'page'	=> (int) $page, 
				'limit' => (int) $limit, 
			); 
		}
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	}
	
	final protected function __item( $args, $argsNum ) 
	{
		if( zero===$argsNum ) 
			$mod = ':first'; 
		else if( 2===$argsNum ) 
			$mod = $args[0];
		else 
			$mod = current($args); 
		switch( $mod ) 
		{
			case ':_id': 
			case ':id': 
			case 'id:': 
				$data = call_user_func_array( [$this,mcbm_findid], array([$args[1]], 1) );
				break;
			case ':pk':
			case 'pk:':
				$data = current($this->clear()->equal($this->_primaryKey, $args[1])->limit(1)->search());
				break;
			case ':last':
				$data = call_user_func_array( [$this,mcbm_last], array([], 0) );
				break;
			case ':first': 
			default:
				$data = call_user_func_array( [$this,mcbm_first],array([], 0) ); 
				break;
		} 
		if( isset($data[$this->_propModel]) ) 
			return $data[$this->_propModel]; 
		else 
			return array(); 
	}
	
	final protected function __row( $args, $argsNum ) 
	{
		try 
		{
			if( $argsNum ) 
			{ 
				$row = current( $args ); 
				if( is_array($row) && isset( $row[$this->_propModel] ) ) 
					return $row[$this->_propModel]; 
				else 
					return NULL; 
			} 
			else 
				throw new Exception( "Usage <strong>Model::raw()</strong> is incorrect." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		}
	}
	
	static protected function __close() 
	{
		global $_CONFIG;
		if( isset($_CONFIG['DATASOURCE']) ) 
		{ 
			$ds = $_CONFIG['DATASOURCE']; 
			foreach($ds['server'] as $svr) 
			{ 
				if( is_array($svr) && 
					isset($svr['resource']) &&
					is_object($svr['resource']) )
				{
					if( "mysql"===$svr['driver'] ) 
					{
						mysqli_close( $svr['resource'] ); 
					} 
				}
			} 
		}
	}
	
}