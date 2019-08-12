<?php
namespace Zuuda;

abstract class RecordSet implements iHtml, iData, iRecordSet 
{
	
	private $_block_list;
	private $_model;
	
	protected function _getBlockList() { return $this->_block_list; }
	protected function _getModel() { return $this->_model; }
	
	protected function _setBlockList( $value ) { $this->_block_list = $value; return $this; }
	protected function _setModel( $value ) { $this->_model = $value; return $this; }
	
	public function IgnoreField( $name ) { return $this->_ignoreField( $name ); }
	public function ExcludeField( $name ) { return $this->_excludeField( $name ); }
	
	public function __construct( Model $model = NULL ) 
	{
		if( !is_null( $model ) )
		{
			$this->model = $model;
		}
	}
	
	protected function _excludeField( $name ) 
	{
		return $this->_ignoreField( $name );
	}
	
	protected function _ignoreField( $name ) 
	{
		$block_list = $this->_getBlockList();
		
		if( !in_array( $block_list, $name ) ) 
		{
			array_push( $block_list, $name );
			$this->_setBlockList( $block_list );
		}
		return $this;
	}
}