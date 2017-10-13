<?php 
namespace Zuuda;

class FileInfo 
{
	
	private $_real_path;
	
	private function _getRealPath() { return $this->_real_path; }
	private function _getPath() { $this->_getRealPath(); }
	
	private function _setRealPath( $value ) { $this->_real_path = $value; return $this; }
	private function _setPath( $value ) { return $this->_setRealPath( $value ); }
	
	public function getRealPath() { return $this->_getRealPath(); }
	public function getPath() { return $this->_getPath(); }
	public function setRealPath( $value ) { return $this->_setRealPath( $value ); }
	public function setPath( $value ) { return $this->_setPath( $value ); }
	public function exist() { return $this->_exist(); }
	
	public function __construct( $file_path ) 
	{
		$this->_setRealPath( $file_path );
	}
	
	private function _exist() 
	{
		if( !is_null( $this->_real_path ) )
			return file_exists( $this->_real_path );
		return false;
	}
	
}