<?php 
namespace Zuuda;

abstract class DataCollection implements iData, iDataCollection
{
	protected $_label;
	protected $_field;
	protected $_model;
	
	protected function _getLabel() { return $this->_label; }
	protected function _getField() { return $this->_field; }
	protected function _getModel() { return $this->_model; }
	
	protected function _setLabel( $value ) { $this->_label = $value; return $this; }
	/** protected function _setField */
	protected function _setModel( $value ) { $this->_model = $value; return $this; }
	
	/** Implements the interface iData */
	public function SetModel( $model ) { return $this->_setModel( $model ); }
	
	/** implements the interface iDataCollection */
	public function SetField( $field, $label ) { return $this->_setField( $field, $label ) }
	
	public function __construct( Model $model = NULL ) 
	{
		if( !is_null( $model ) ) 
		{
			$this->_setModel( $model );
		}
	}
	
	protected function _setField( $field, $label ) 
	{
		$this->_field = $field;
		$this->_setLabel( $label );
		return $this;
	}
}