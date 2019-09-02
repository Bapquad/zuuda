<?php
namespace Zuuda;

abstract class RecordSet implements iHtml, iData, iRecordSet 
{
	
	private $_block_list;
	private $_model;
	
	protected function __getBlockList() { return $this->_block_list; }
	protected function __getModel() { return $this->_model; }
	
	protected function __setBlockList( $value ) { $this->_block_list = $value; return $this; }
	protected function __setModel( $value ) { $this->_model = $value; return $this; }
	
	public function IgnoreField( $name ) { return $this->__ignoreField( $name ); }
	public function ExcludeField( $name ) { return $this->__excludeField( $name ); }
	
	public function __construct( Model $model = NULL ) 
	{
		if( !is_null( $model ) )
		{
			$this->model = $model;
		}
	}
	
	protected function __excludeField( $name ) 
	{
		return $this->__ignoreField( $name );
	}
	
	protected function __ignoreField( $name ) 
	{
		$block_list = $this->__getBlockList();
		
		if( !in_array( $block_list, $name ) ) 
		{
			array_push( $block_list, $name );
			$this->__setBlockList( $block_list );
		}
		return $this;
	}
}