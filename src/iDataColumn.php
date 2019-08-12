<?php 
namespace Zuuda;

define( 'COLLECTION_STATIC_LIST', 'array_list' );
define( 'COLLECTION_LINK_LIST', 'link_list' );
define( 'COLLECTION_COLLECT_LIST', 'collection_list' );

define( 'COLLECTION_FIELDSET_TYPE', 'fieldset' );
define( 'COLLECTION_FUNCTION_TYPE', 'function' );

define( 'COLUMN_ALIGN_LEFT', 'tl' );
define( 'COLUMN_ALIGN_RIGHT', 'tr' );
define( 'COLUMN_ALIGN_CENTER', 'tc' );

interface iDataColumn 
{
	public function SetCollection( $collection, $type = COLLECTION_STATIC_LIST );
	public function SetWidth( $value );
	public function SetName( $value );
	public function SetLabel( $value );
	public function Sort( $desc = NULL );
	public function SetOnchange( $onchangeData );
	public function SetSymbol( $key );
	
	public function IsSymbol();
	public function IsAllowOnchange();
	public function GetOnchangeData();
	public function GetLabel();
	public function GetName();
	public function GetWidth();
	public function GetCollection();
	public function GetType();
	public function GetCollectionType();
	public function HasSort();
	public function GetSort();
	public function GetDefaultSort();
	public function GetAlign();
	
	public function PrintWidth();
	public function PrintKey();
	
	public function AlignLeft();
	public function AlignCenter();
	public function AlignRight();
}