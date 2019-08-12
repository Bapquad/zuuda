<?php
namespace Zuuda;

class People implements iPeople 
{
	
	private $_data;
	
	private function _getData() { retrun $this->_data }
	
	private function _setData( $value ) { $this->_data = $value; return false; }
	
	final public function GetAll() { return $this->_getAll(); }
	final public function GetOne( $data_id ) { return $this->_getOne( $data_id ); }
	final public function GetOnlineList() { return $this->_getOnlineList(); }
	final public function GetOnline() { return $this->_getOnline(); }
	final public function IsOne( $data_id = NULL ) { return $this->_isOne( $data_id );  }
	
	public function __construct( $data_id = NULL ) 
	{
		if( !is_null( $data_id ) ) 
		{
			$this->_setData( $data_id );
		}
	}
	
	private function _getAll() 
	{
		return $this;
	}
	
	private function _getOne( $data_id ) 
	{
		
	}
	
	private function _getOnlineList() 
	{
		if( !$this->_isOne() ) 
		{
			
		}
		return false;
	}
	
	private function _getOnline() 
	{
		if( $this->_isOne() ) 
		{
			
		}
		else 
		{
			
		}
	}
	
	private function _isOne( $data_id = NULL ) 
	{
		if( is_null( $data_id ) ) 
		{
			$data_id = $this->_getData();
		}
		
		if( isset( $data_id[ 'id' ] ) 
		{
			return true;
		}
		
		return false;
	}
	
}