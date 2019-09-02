<?php 
namespace Zuuda;

abstract class FileUploader 
{
	
	private $_data;

	public function MoveObject( $source_path, $target_path ) { return $this->__moveObject($source_path, $target_path); }

	public function __construct() 
	{
		global $_file;

		$this->_data = $_file;

		// Process the array file;
		var_dump($this->_data);
	} 

	public function __moveObject( $source_path, $target_path ) 
	{
		if( file_exists( $source_path ) ) 
		{
			move_uploaded_file( $source_path, $target_path );
		}
	} 
	
}