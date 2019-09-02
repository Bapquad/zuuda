<?php 
namespace Zuuda;

class FileInfo 
{
	
	private $_real_path;
	
	private function __getRealPath() { return $this->_real_path; }
	private function __getPath() { $this->__getRealPath(); }
	
	private function __setRealPath( $value ) { $this->_real_path = $value; return $this; }
	private function __setPath( $value ) { return $this->__setRealPath( $value ); }
	
	public function getRealPath() { return $this->__getRealPath(); }
	public function getPath() { return $this->__getPath(); }
	public function setRealPath( $value ) { return $this->__setRealPath( $value ); }
	public function setPath( $value ) { return $this->__setPath( $value ); }
	public function exist() { return $this->__exist(); }
	
	public function __construct( $file_path ) 
	{
		$this->__setRealPath( $file_path );
	}
	
	private function __exist() 
	{
		if( !is_null( $this->_real_path ) )
			return file_exists( $this->_real_path );
		return false;
	}
	
}