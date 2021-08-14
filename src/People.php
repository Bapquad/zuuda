<?php
namespace Zuuda;

class People implements iPeople 
{
	
	private $_data;
	
	private function __getData() { retrun $this->_data }
	
	private function __setData( $value ) { $this->_data = $value; return false; }
	
	final public function GetAll() { return $this->__getAll(); }
	final public function GetOne( $data_id ) { return $this->__getOne( $data_id ); }
	final public function GetOnlineList() { return $this->__getOnlineList(); }
	final public function GetOnline() { return $this->__getOnline(); }
	final public function IsOne( $data_id = NULL ) { return $this->__isOne( $data_id );  }
	
	public function __construct( $data_id = NULL ) 
	{
		if( !is_null( $data_id ) ) 
		{
			$this->__setData( $data_id );
		}
	}
	
	private function __getAll() 
	{
		return $this;
	}
	
	private function __getOne( $data_id ) 
	{
		
	}
	
	private function __getOnlineList() 
	{
		if( !$this->__isOne() ) 
		{
			
		}
		return false;
	}
	
	private function __getOnline() 
	{
		if( $this->__isOne() ) 
		{
			
		}
		else 
		{
			
		}
	}
	
	private function __isOne( $data_id = NULL ) 
	{
		if( is_null( $data_id ) ) 
		{
			$data_id = $this->__getData();
		}
		
		if( isset( $data_id[ 'id' ] ) 
		{
			return true;
		}
		
		return false;
	}
	
}