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
	
	protected function __getName() { return $this->_name; }
	protected function __getLabel() { return $this->_label; }
	protected function __getWidth() { return $this->_width; }
	protected function __getCollection() { return $this->_collection; }
	protected function __getType() { return $this->_type; }
	protected function __getCollectionType() { return $this->_collectionType; }
	protected function __getSort() { return $this->_sort; }
	protected function __getDefaultSort() { return $this->_default_sort; }
	protected function __getKey() { return $this->_key; }
	protected function __getAlign() { return $this->_align; }
	protected function __getOnchange() { return $this->_onchange; }
	protected function __getOnchangeData() { return $this->_onchangeData; }
	
	/** protected function __setName() */
	protected function __setLabel( $value ) { $this->_label = $value; return $this; }
	protected function __setWidth( $value ) { $this->_width = $value; return $this; }
	/** protected function __setCollection() */
	protected function __setType( $value ) { $this->_type = $value; return $this; }
	protected function __setCollectionType( $value ) { $this->_collectionType = $value; return $this; }
	protected function __setSort( $value ) { $this->_sort = $value; return $this; }
	protected function __setDefaultSort( $value ) { $this->_default_sort = $value; return $this; }
	protected function __setKey( $value ) { $this->_key = $value; return $this; }
	protected function __setAlign( $value ) { $this->_align = $value; return $this; }
	/** protected function __setOnchange() */
	protected function __setOnchangeData( $value ) { $this->_onchangeData = $value; return $this; }
	
	/** Implements the interface of data column */
	public function SetCollection( $collection, $type = COLLECTION_STATIC_LIST ) { return $this->__setCollection( $collection, $type ); }
	public function SetWidth( $value ) { return $this->__setWidth( $value ); }
	public function SetName( $value ) { return $this->__setName( $value ); }
	public function SetLabel( $value ) { return $this->__setLabel( $value ); }
	public function Sort( $desc = NULL ) { return $this->__sort( $desc ); }
	public function SetOnchange( $onchangeData ) { return $this->__setOnchange( $onchangeData ); }
	public function SetSymbol( $key ) { return $this->__setKey( $key ); }
	public function AlignLeft() { return $this->__setAlignLeft(); }
	public function AlignCenter() { return $this->__setAlignCenter(); }
	public function AlignRight() { return $this->__setAlignRight(); }
	
	public function IsSymbol() { return $this->__isSymbol(); }
	public function IsAllowOnchange() { return $this->__isAllowOnchange(); }
	public function GetOnchangeData() { return $this->__getOnchangeData(); }
	public function GetLabel() { return $this->__getLabel(); }
	public function GetName() { return $this->__getName(); }
	public function GetWidth() { return $this->__getWidth(); }
	public function GetCollection() { return $this->__getCollection(); }
	public function GetType() { return $this->__getType(); }
	public function GetCollectionType() { return $this->__getCollectionType(); }
	public function HasSort() { return $this->__getSort(); }
	public function GetSort() { return $this->__getSort(); }
	public function GetDefaultSort() { return $this->__getDefaultSort(); }
	public function GetAlign() { return $this->__getAlign(); }
	
	public function PrintWidth() { return $this->__printWidth(); }
	public function PrintKey() { return $this->__printKey(); }
	
	public function __construct( $data = NULL ) 
	{
		$this->__setType( COLLECTION_FUNCTION_TYPE );
		
		$this->__setAlignLeft();
		
		if( !is_null( $data ) ) 
		{
			if( isset( $data[ 'name' ] ) )
			{
				$name = $data[ 'name' ];
				$this->__setName( $name );
			}
			
			if( isset( $data[ 'label' ] ) ) 
			{
				$label = $data[ 'label' ];
				$this->__setLabel( $label );
			}
			
			if( isset( $data[ 'width' ] ) ) 
			{
				$width = $data[ 'width' ];
				$this->__setWidth( $width );
			}
			
			if( isset( $data[ 'collection' ] ) )
			{
				$collection = $data[ 'collection' ];
				$this->__setCollection( $collection );
			}
		}
	}
	
	
	protected function __isSymbol() { return $this->__getKey(); }
	protected function __isAllowOnchange() { return $this->__getOnchange(); }
	
	protected function __printKey() 
	{
		echo ( !is_null( $this->_key ) ) ? '<span class="no">' . $this->_key . '</span>' : NULL;
	}
	
	protected function __printWidth() 
	{
		echo ( !is_null( $this->_width ) ) ? ' width="' . $this->_width . '"' : NULL;
	}
	
	protected function __setName( $value ) 
	{
		$this->_name = $value;
		return $this->__setType( COLLECTION_FIELDSET_TYPE );
	}
	
	protected function __setCollection( $value, $type = COLLECTION_STATIC_LIST ) 
	{ 
		$this->_collection = $value; 
		return $this->__setCollectionType( $type );
	}
	
	protected function __sort( $desc = NULL ) 
	{
		$this->__setSort( true );
		return $this->__setDefaultSort( $desc );
	}
	
	protected function __setOnchange( $onchangeData ) 
	{
		$this->_onchange = true;
		return $this->__setOnchangeData( $onchangeData );
	}
	
	protected function __setAlignLeft() { return $this->__setAlign( COLUMN_ALIGN_LEFT ); }
	protected function __setAlignRight() { return $this->__setAlign( COLUMN_ALIGN_RIGHT ); }
	protected function __setAlignCenter() { return $this->__setAlign( COLUMN_ALIGN_CENTER ); }
	
}