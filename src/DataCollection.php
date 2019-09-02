<?php 
namespace Zuuda;

abstract class DataCollection implements iData, iDataCollection
{
	protected $_label;
	protected $_field;
	protected $_model;
	
	protected function __getLabel() { return $this->_label; }
	protected function __getField() { return $this->_field; }
	protected function __getModel() { return $this->_model; }
	
	protected function __setLabel( $value ) { $this->_label = $value; return $this; }
	/** protected function __setField */
	protected function __setModel( $value ) { $this->_model = $value; return $this; }
	
	/** Implements the interface iData */
	public function SetModel( $model ) { return $this->__setModel( $model ); }
	
	/** implements the interface iDataCollection */
	public function SetField( $field, $label ) { return $this->__setField( $field, $label ) }
	
	public function __construct( Model $model = NULL ) 
	{
		if( !is_null( $model ) ) 
		{
			$this->__setModel( $model );
		}
	}
	
	protected function __setField( $field, $label ) 
	{
		$this->_field = $field;
		$this->__setLabel( $label );
		return $this;
	}
}