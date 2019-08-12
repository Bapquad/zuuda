<?php
namespace Zuuda; 

abstract class DataColumn implements iDataColumn 
{
	
	private $_name;
	private $_label;
	private $_width;
	private $_collection;
	private $_type;
	private $_collectionType;
	private $_sort = false;
	private $_default_sort;
	private $_key;
	private $_align;
	private $_onchange = false;
	private $_onchangeData;
	
	protected function _getName() { return $this->_name; }
	protected function _getLabel() { return $this->_label; }
	protected function _getWidth() { return $this->_width; }
	protected function _getCollection() { return $this->_collection; }
	protected function _getType() { return $this->_type; }
	protected function _getCollectionType() { return $this->_collectionType; }
	protected function _getSort() { return $this->_sort; }
	protected function _getDefaultSort() { return $this->_default_sort; }
	protected function _getKey() { return $this->_key; }
	protected function _getAlign() { return $this->_align; }
	protected function _getOnchange() { return $this->_onchange; }
	protected function _getOnchangeData() { return $this->_onchangeData; }
	
	/** protected function _setName() */
	protected function _setLabel( $value ) { $this->_label = $value; return $this; }
	protected function _setWidth( $value ) { $this->_width = $value; return $this; }
	/** protected function _setCollection() */
	protected function _setType( $value ) { $this->_type = $value; return $this; }
	protected function _setCollectionType( $value ) { $this->_collectionType = $value; return $this; }
	protected function _setSort( $value ) { $this->_sort = $value; return $this; }
	protected function _setDefaultSort( $value ) { $this->_default_sort = $value; return $this; }
	protected function _setKey( $value ) { $this->_key = $value; return $this; }
	protected function _setAlign( $value ) { $this->_align = $value; return $this; }
	/** protected function _setOnchange() */
	protected function _setOnchangeData( $value ) { $this->_onchangeData = $value; return $this; }
	
	/** Implements the interface of data column */
	public function SetCollection( $collection, $type = COLLECTION_STATIC_LIST ) { return $this->_setCollection( $collection, $type ); }
	public function SetWidth( $value ) { return $this->_setWidth( $value ); }
	public function SetName( $value ) { return $this->_setName( $value ); }
	public function SetLabel( $value ) { return $this->_setLabel( $value ); }
	public function Sort( $desc = NULL ) { return $this->_sort( $desc ); }
	public function SetOnchange( $onchangeData ) { return $this->_setOnchange( $onchangeData ); }
	public function SetSymbol( $key ) { return $this->_setKey( $key ); }
	public function AlignLeft() { return $this->_setAlignLeft(); }
	public function AlignCenter() { return $this->_setAlignCenter(); }
	public function AlignRight() { return $this->_setAlignRight(); }
	
	public function IsSymbol() { return $this->_isSymbol(); }
	public function IsAllowOnchange() { return $this->_isAllowOnchange(); }
	public function GetOnchangeData() { return $this->_getOnchangeData(); }
	public function GetLabel() { return $this->_getLabel(); }
	public function GetName() { return $this->_getName(); }
	public function GetWidth() { return $this->_getWidth(); }
	public function GetCollection() { return $this->_getCollection(); }
	public function GetType() { return $this->_getType(); }
	public function GetCollectionType() { return $this->_getCollectionType(); }
	public function HasSort() { return $this->_getSort(); }
	public function GetSort() { return $this->_getSort(); }
	public function GetDefaultSort() { return $this->_getDefaultSort(); }
	public function GetAlign() { return $this->_getAlign(); }
	
	public function PrintWidth() { return $this->_printWidth(); }
	public function PrintKey() { return $this->_printKey(); }
	
	public function __construct( $data = NULL ) 
	{
		$this->_setType( COLLECTION_FUNCTION_TYPE );
		
		$this->_setAlignLeft();
		
		if( !is_null( $data ) ) 
		{
			if( isset( $data[ 'name' ] ) )
			{
				$name = $data[ 'name' ];
				$this->_setName( $name );
			}
			
			if( isset( $data[ 'label' ] ) ) 
			{
				$label = $data[ 'label' ];
				$this->_setLabel( $label );
			}
			
			if( isset( $data[ 'width' ] ) ) 
			{
				$width = $data[ 'width' ];
				$this->_setWidth( $width );
			}
			
			if( isset( $data[ 'collection' ] ) )
			{
				$collection = $data[ 'collection' ];
				$this->_setCollection( $collection );
			}
		}
	}
	
	
	protected function _isSymbol() { return $this->_getKey(); }
	protected function _isAllowOnchange() { return $this->_getOnchange(); }
	
	protected function _printKey() 
	{
		echo ( !is_null( $this->_key ) ) ? '<span class="no">' . $this->_key . '</span>' : NULL;
	}
	
	protected function _printWidth() 
	{
		echo ( !is_null( $this->_width ) ) ? ' width="' . $this->_width . '"' : NULL;
	}
	
	protected function _setName( $value ) 
	{
		$this->_name = $value;
		return $this->_setType( COLLECTION_FIELDSET_TYPE );
	}
	
	protected function _setCollection( $value, $type = COLLECTION_STATIC_LIST ) 
	{ 
		$this->_collection = $value; 
		return $this->_setCollectionType( $type );
	}
	
	protected function _sort( $desc = NULL ) 
	{
		$this->_setSort( true );
		return $this->_setDefaultSort( $desc );
	}
	
	protected function _setOnchange( $onchangeData ) 
	{
		$this->_onchange = true;
		return $this->_setOnchangeData( $onchangeData );
	}
	
	protected function _setAlignLeft() { return $this->_setAlign( COLUMN_ALIGN_LEFT ); }
	protected function _setAlignRight() { return $this->_setAlign( COLUMN_ALIGN_RIGHT ); }
	protected function _setAlignCenter() { return $this->_setAlign( COLUMN_ALIGN_CENTER ); }
	
}