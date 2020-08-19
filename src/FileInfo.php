<?php 
namespace Zuuda;

class FileInfo 
{
	
	protected $_path; 
	protected $_handle;
	
	protected function __getRealPath() { return $this->_path; }
	protected function __getPath() { $this->__getRealPath(); }
	protected function __setRealPath( $value ) { $this->_path = $value; return $this; }
	protected function __setPath( $value ) { return $this->__setRealPath( $value ); }
	
	public function GetRealPath() { return $this->__getRealPath(); }
	public function GetPath() { return $this->__getPath(); }
	public function SetRealPath( $value ) { return $this->__setRealPath( $value ); }
	public function SetPath( $value ) { return $this->__setPath( $value ); }
	public function Exist() { return $this->__exist(); } 
	public function Name() { return $this->__name(); } 
	public function Extension() { return $this->__extension(); } 
	public function Basename() { return $this->__basename(); } 
	public function File() { return $this->__file(); } 
	public function BufferInfo() { return $this->__buffer_info(); } 
	public function Size() { return filesize($this->_path); } 
	public function MimeType() { return mime_content_type($this->_path); } 
	public function Mime() { return mime_content_type($this->_path); } 
	public function Type() { return mime_content_type($this->_path); } 
	public function ContentType() { return mime_content_type($this->_path); } 
	public function SetFlags($options) { return $this->__set_flags($options); } 
	public function Dirname($levels = 1) { return $this->__dirname($levels); } 
	public function Parent() { return $this->__dirname(1); } 
	public function FreeSpace($disk) { return $this->__free_space($disk); } 
	public function DiskSpace($disk) { return $this->__total_space($disk); } 
	public function TotalSpace($disk) { return $this->__total_space($disk); } 
	public function AccessedTime() { return fileatime($this->_path); } 
	public function CreatedTime() { return filectime($this->_path); } 
	public function ModifiedTime() { return filemtime($this->_path); } 
	public function Group() { return posix_getgrgid(filegroup($this->_path)); } 
	public function Owner() { return posix_getpwuid(fileowner($this->_path)); } 
	public function Permission() { return fileperms($this->_path); } 
	public function Content() { return file($this->_path); } 

	public function __construct( $file_path=NULL, $apply_absolute_path = false ) 
	{
		if( NULL!==$file_path ) 
		{
			if( $apply_absolute_path ) 
				$this->__setRealPath( $file_path ); 
			else 
				$this->__setRealPath( __correctPath(APP_DIR.$file_path) ); 
		}
	} 
	
	protected function __exist() 
	{
		if( is_null( $this->_path ) )
			return;
		return file_exists($this->_path);
	} 
	
	protected function __name() 
	{ 
		if( is_null( $this->_path ) )
			return;
		return pathinfo( $this->_path, PATHINFO_FILENAME ); 
	} 
	
	protected function __extension() 
	{ 
		if( is_null( $this->_path ) )
			return;
		return pathinfo( $this->_path, PATHINFO_EXTENSION ); 
	} 
	
	protected function __basename() 
	{ 
		if( is_null($this->_path) ) 
			return; 
		return basename($this->_path); 
	} 
	
	protected function __file() 
	{ 
		if( is_null($this->_path) ) 
			return; 
		
		$finfo = finfo_open(FILEINFO_MIME, NULL);
		if( !$finfo ) 
		{
			// echo "Opening fileinfo database failed";
			// exit();
			return; 
		} 
		
		/* get mime-type for a specific file */
		$rs = finfo_file($finfo, $this->_path); 
		if( $rs ) 
			$rs = explode('; ', $rs); 
		
		/* close connection */
		finfo_close($finfo); 
		
		// Return the result file information.
		return $rs; 
	} 
	
	protected function __buffer_info() 
	{
		if( is_null($this->_path) ) 
			return;  
		
		$finfo = finfo_open(FILEINFO_MIME, NULL);
		if( !$finfo ) 
		{
			// echo "Opening fileinfo database failed";
			// exit();
			return; 
		} 
		
		/* get mime-type for a specific file */ 
		$rs = finfo_buffer($finfo, $this->_path); 
		if( $rs ) 
			$rs = explode('; ', $rs); 
		
		/* close connection */
		finfo_close($finfo); 
		
		// Return the result file information.
		return $rs; 
	} 
	
	protected function __set_flags($options) 
	{
		if( is_null($this->_path) ) 
			return;  
		
		$finfo = finfo_open(FILEINFO_MIME, NULL);
		if( !$finfo ) 
		{
			// echo "Opening fileinfo database failed";
			// exit();
			return; 
		} 

		/* get mime-type for a specific file */ 
		$rs = finfo_set_flags($finfo, $this->_path); 

		/* close connection */
		finfo_close($finfo); 
		
		// Return the result file information.
		return $rs; 
	} 
	
	protected function __dirname($levels) 
	{
		return dirname($this->_path, $levels); 
	} 
	
	protected function __free_space($disk) 
	{ 
		/**
		 * KB (1024)
		 * MB (1048576)
		 * GB (1073741824)
		 */
		return number_format(disk_free_space($disk)/1073741824, 1)."GB"; 
	} 
	
	protected function __total_space($disk) 
	{ 
		/**
		 * KB (1024)
		 * MB (1048576)
		 * GB (1073741824)
		 */
		return number_format(disk_total_space($disk)/1073741824, 1)."GB"; 
	} 
	
	protected function __close() 
	{ 
		if( is_null($this->_handle) ) 
			return $this; 
		fclose($this->_handle); 
	} 
	
}