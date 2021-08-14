<?php 

namespace Zuuda;

class FileLoader extends FileInfo 
{ 

	public function Copy( $dest_path ) { return $this->__copy($dest_path); } 
	public function Move( $dest_path, $abs = false ) { return $this->__move($dest_path, $abs); } 
	public function Load() { return file_get_contents($this->_path); } 
	public function Save($data) { return file_put_contents($this->_path, $data); } 
	public function Delete() { return $this->__delete(); } 
	public function Rename($new_name) { return $this->__rename($new_name); } 
	public function Eof() { return $this->__eof(); } 
	public function Flush() { return $this->__flush(); } 
	public function GetC() { return $this->__getc(); } 
	public function GetA($delimiter=comma) { return $this->__geta($delimiter); } 
	public function GetS($size=NULL) { return $this->__gets($size); } 
	public function GetL() { return $this->__getl(); } 
	public function Open($mode="r") { return $this->__open($mode); } 
	public function PutA($delimiter=comma, $data) { return $this->__puta($delimiter, $data); } 
	public function PutS($buffer) { return $this->__write($buffer); } 
	
	public function Read($length=NULL) { return $this->__read($length); } 
	public function Scan($f) { return $this->__scan($f); } 
	public function Seek($size, $mode) { return $this->__seek($size, $mode); } 
	public function Rewind() { return $this->__rewind(); } 
	public function Stat() { return $this->__stat(); } 
	public function Tell() { return $this->__tell(); } 
	public function Truncate($size) { return $this->__truncate($size); } 
	public function Write($buffer) { return $this->__write($buffer); } 
	public function Append($buffer) { return $this->__append($buffer); } 
	public function Touch() { return $this->__touch(); } 
	
	public function Close() { return $this->__close(); } 
	public function Buffer() { return $this->__read_to_buffer(); } 
	
	public function __construct( $file_path, $apply_absolute_path = false ) 
	{ 
		parent::__construct( $file_path, $apply_absolute_path ); 
	} 
	
	protected function __close() 
	{ 
		if( !is_null($this->_handle) ) 
		{
			fclose($this->_handle); 
			$this->_handle = NULL; 
		}
		return $this;
	} 

	private function __copy( $dest_path ) 
	{ 
		return copy( $this->_path, $dest_path ); 
	} 
	
	private function __move( $dest_path, $apply_absolute_path=false ) 
	{
		if( is_null($this->_path) ) 
			return; 
		$src = $this->_path; 
		if( $apply_absolute_path ) 
			$dest = $dest_path; 
		else 
			$dest = __correctPath(APP_DIR.$dest_path); 
		copy( $src, $dest ); 
		unlink( $src ); 
		return $this->__setRealPath( $dest ); 
	} 
	
	private function __delete() 
	{ 
		if( !is_null($this->_path) ) 
			unlink($this->_path); 
		return $this; 
	} 
	
	private function __rename($new_name) 
	{ 
		$dest = $this->dirname().DS.$new_name; 
		return $this->__move($dest); 
	} 
	
	private function __eof() 
	{
		if( is_null($this->_handle) ) 
			return; 
		return feof($this->_handle); 
	} 
	
	private function __flush() 
	{ 
		if( !is_null($this->_handle) ) 
			fflush($this->_handle); 
		return $this; 
	} 
	
	private function __getc() 
	{
		if( is_null($this->_handle) ) 
			return; 
		return fgetc($this->_handle); 
	} 
	
	private function __geta($delimiter=comma) 
	{ 
		if( is_null($this->_handle) ) 
			return array(); 
		return fgetcsv($this->_handle, 0, $delimiter); 
	} 
	
	private function __gets($size=NULL) 
	{
		if( is_null($this->_handle) ) 
			return; 
		return fgets($this->_handle, $size); 
	} 
	
	private function __getl() 
	{
		if( is_null($this->_handle) ) 
			return; 
		return fgetss($this->_handle); 
	} 
	
	/**
	 * Mode		Means 
	 * 	r		Open to read.
	 *	w		Open to write.
	 *  a		Open to append.
	 *  x		Create, then open to write.
	 */
	private function __open($mode="r") 
	{ 
		if( is_null($this->_handle) && !is_null($this->_path) ) 
		{
			$this->_handle = fopen($this->_path, $mode); 
		} 
		return $this; 
	} 
	
	private function __puta($delimiter=comma, $data) 
	{
		if( !is_null($this->_handle) && isset($data) ) 
		{
			fputcsv( $this->_handle, $data, $delimiter );
		}
		return $this; 
	} 
	
	private function __read($size=NULL) 
	{
		$out = NULL; 
		if( is_null($this->_handle) ) 
		{
			if( !is_null($this->_path) ) 
			{
				$read_size = ($size==NULL)?filesize($this->_path):$size;
				$fh = fopen( $this->_path, "r" );
				$out = fread( $fh, $read_size );
				fclose($fh);
			}
		} 
		else 
		{
			if( !is_null($this->_path) ) 
			{
				$read_size = ($size==NULL)?filesize($this->_path):$size;
				$out = fread($this->_handle, filesize($this->_path)); 
			}
		} 
		return $out; 
	} 

	/**
	 * Scan the line of file according to a format.
	 * Type			Specifier
	 * string		%s
	 * integer		%d, %u, %c, %o, %x, %X, %b
	 * double		%g, %G, %e, %E, %f, %F
	 */
	private function __scan($format) 
	{ 
		if( !is_null($this->_handle) ) 
			return fscanf( $this->_handle, $format ); 
		return; 
	} 

	/**
	 * Modes:
	 * SEEK_SET - Set position equal to offset bytes.
	 * SEEK_CUR - Set position to current location plus offset.
	 * SEEK_END - Set position to end-of-file plus offset.
	 */
	private function __seek($size, $mode=SEEK_SET) 
	{ 
		if( !is_null($this->_handle) ) 
			fseek($this->_handle, $size); 
		return $this; 
	} 
	
	private function __rewind() 
	{ 
		if( !is_null($this->_handle) ) 
			rewind($this->_handle); 
		return $this; 
	} 
	
	/**
	 * Return array of informations of the file.
	 */
	private function __stat() 
	{ 
		if( !is_null($this->_handle) ) 
			return fstat($this->_handle); 
		return; 
	} 
	
	/**
	 * Returns the current position of the file read/write pointer.
	 */
	private function __tell() 
	{
		if( !is_null($this->_handle) ) 
			ftell($this->_handle); 
		return $this; 
	} 
	
	private function __truncate($size) 
	{ 
		if( !is_null($this->_handle) ) 
			ftruncate($this->_handle, $size); 
		return $this; 
	} 
	
	private function __write($buffer) 
	{ 
		if( !is_null($this->_handle) ) 
		{
			fwrite($this->_handle, $buffer); 
		} 
		else if( is_null($this->_handle) && !is_null($this->_path) ) 
		{ 
			$fp = fopen($this->_path, 'w');
			fwrite($fp, $buffer);
			fclose($fp); 
		} 
		return $this; 
	} 
	
	private function __append($buffer) 
	{ 
		if( !is_null($this->_handle) ) 
		{
			fwrite($this->_handle, $buffer); 
		} 
		else if( is_null($this->_handle) && !is_null($this->_path) ) 
		{ 
			$fp = fopen($this->_path, 'a');
			fwrite($fp, $buffer);
			fclose($fp); 
		} 
		return $this; 
	} 
	
	private function __touch() 
	{ 
		if( !is_null($this->_path) ) 
			touch($this->_path); 
		return $this; 
	} 
	
	private function __read_to_buffer() 
	{ 
		if( !is_null($this->_path) && file_exists($this->_path) ) 
			readfile($this->_path); 
		return $this; 
	} 
	
	public function __download($name=NULL) 
	{
		if( is_null($name) ) 
			$name = $this->__basename(); 
		
		if( !is_null($this->_path) && file_exists($this->_path) ) 
		{
			header('Content-Description: File Transfer');
			header('Content-Type: '.mime_content_type($this->_path));
			header('Content-Disposition: attachment; filename="'.$name.'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($this->_path)); 
			readfile($this->_path); 
		} 
		return $this; 
	}
	
}