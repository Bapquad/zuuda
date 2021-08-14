<?php 
namespace Zuuda;

use Zuuda\Fx;
use Zuuda\Cache;
use Zuuda\Error;
use Exception;

class FileUploader 
{
	private static $this = '\Zuuda\FileUploader';
	static $uploadDir = 'tmp/media/'; 
	static $storeDir = 'media/'; 
	
	protected $_storeDir; 
	protected $_chunkCur;
	protected $_alwUplType;
	protected $_alwUplSize;
	protected $_alwUplLen;
	protected $_alwInpLen;
	protected $_alwInpSize;
	
	private function __construct() {}
	private function __clone() {} 
	final public function rootName() { return __CLASS__; }

	final static public function Backup() { return call_user_func_array((array(self::$this, '__backup')), array(func_get_args())); } 
	final static public function Instance() { return call_user_func_array(array(self::$this, '__instance'), array()); } 
	final public function Config() { return call_user_func_array(array($this, '__config'), array(func_get_args())); } 
	final public function NotEmpty() { return call_user_func_array(array($this, '__notEmpty'), array()); } 
	final public function IsEmpty() { return call_user_func_array(array($this, '__isEmpty'), array()); } 
	final public function Data() { return call_user_func_array(array($this, '__data'), array(func_get_args())); } 
	final public function Has() { return call_user_func_array(array($this, '__has'), array(func_get_args())); } 
	final public function IsAll() { return call_user_func_array(array($this, '__isAll'), array()); } 
	final public function IsFile() { return call_user_func_array(array($this, '__isFile'), array()); } 
	final public function IsFileList() { return call_user_func_array(array($this, '__isFileList'), array()); } 
	final public function IsCustom() { return call_user_func_array(array($this, '__isCustom'), array()); } 
	final public function SelectAll() { return call_user_func_array(array($this, '__selectAll'), array()); } 
	final public function Select() { return call_user_func_array(array($this, '__select'), array(func_get_args())); } 
	final public function Name() { return call_user_func_array(array($this, '__name'), array(func_get_args())); }
	final public function ChangeName() { return call_user_func_array(array($this, '__changeName'), array(func_get_args())); }
	final public function Type() { return call_user_func_array(array($this, '__type'), array()); } 
	final public function Size() { return call_user_func_array(array($this, '__size'), array(func_get_args())); }
	final public function Path() { return call_user_func_array(array($this, '__path'), array()); } 
	final public function Length() { return call_user_func_array(array($this, '__length'), array()); } 
	final public function Status() { return call_user_func_array(array($this, '__status'), array()); }
	final public function Problem() { return call_user_func_array(array($this, '__status'), array()); }
	final public function StoreTo() { return call_user_func_array(array($this, '__copyTo'), array(func_get_args())); } 
	final public function CopyTo() { return call_user_func_array(array($this, '__copyTo'), array(func_get_args())); } 
	final public function MoveTo() { return call_user_func_array(array($this, '__copyTo'), array(func_get_args())); } 
	final public function Upload() { return call_user_func_array(array($this, '__store'), array( func_get_args())); } 
	final public function Store() { return call_user_func_array(array($this, '__store'), array(func_get_args())); } 

	final static private function __instance() 
	{ 
		static $_instance; 
		return $_instance ?: ($_instance = new FileUploader); 
	} 
	
	final private function __config( $args ) 
	{ 
		try 
		{
			if( empty($args) ) 
			{ 
				throw new Exception("The <b>FileUploader::config([])</b> function is must be has a least of one parameter in Array type."); 
			} 
			$args = current($args); 
			if( !is_array($args) ) 
			{ 
				throw new Exception("The <b>FileUploader::config([])</b> parameter is must be an array."); 
			} 
			if( isset($args['store_dir']) ) 
			{ 
				if( !is_string($args['store_dir']) ) 
				{ 
					throw new Exception("The <code>[<b>'store_dir'</b> => \"the/your/path/\"]</code> config is must be a string."); 
				}  
				$this->_storeDir = $args['store_dir']; 
			} 
			if( isset($args['allow_max_upload_length']) ) 
			{ 
				if( !is_numeric($args['allow_max_upload_length']) ) 
				{ 
					throw new Exception("The <code>[<b>'allow_max_upload_length'</b> => 20]</code> config is must be a numeric. Specifies the max allowed length of all uploaded file."); 
				}  
				$this->_alwUplLen = $args['allow_max_upload_length']; 
			} 
			if( isset($args['allow_max_upload_size']) ) 
			{ 
				if( !is_numeric($args['allow_max_upload_size']) ) 
				{ 
					throw new Exception("The <code>[<b>'allow_max_upload_size'</b> => 10]</code> config is must be a numeric under MB unit. (Ex: 10MB) specifies the max allowed size of all uploaded files less than 10MB data."); 
				}  
				$this->_alwUplSize = $args['allow_max_upload_size']; 
			} 
			if( isset($args['allow_max_input_length']) ) 
			{ 
				if( !is_numeric($args['allow_max_input_length']) ) 
				{ 
					throw new Exception("The <code>[<b>'allow_max_input_length'</b> => 20]</code> config is must be a numeric. Specifies the max allowed length of all uploaded file."); 
				}  
				$this->_alwInpLen = $args['allow_max_input_length']; 
			} 
			if( isset($args['allow_max_input_size']) ) 
			{ 
				if( !is_numeric($args['allow_max_input_size']) ) 
				{ 
					throw new Exception("The <code>[<b>'allow_max_input_size'</b> => 10]</code> config is must be a numeric under MB unit. (Ex: 10MB) specifies the max allowed size of all uploaded files less than 10MB data."); 
				}  
				$this->_alwInpSize = $args['allow_max_input_size']; 
			} 
			if( isset($args['allow_type']) ) 
			{ 
				if( !is_array($args['allow_type']) ) 
				{ 
					throw new Exception("The <code>[<b>'allow_type'</b> => [\"audio/mp3\", [...]]]</code> config is must be an array."); 
				} 
				$this->_alwUplType = $args['allow_type'];
			} 
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage().BL.error::position($e) );
		} 
		return $this;
	} 
	
	final private function __isEmpty() 
	{ 
		global $_file; 
		return empty($_file) || NULL===$this->_chunkCur || empty($this->_chunkCur); 
	} 
	
	final private function __notEmpty() 
	{ 
		return !$this->__isEmpty(); 
	} 
	
	final private function __store( $args ) 
	{ 
		if( $this->__isEmpty() ) return NULL;
		if( empty($args) ) 
			$store_dir = (NULL!==$this->_storeDir)?$this->_storeDir:FileUploader::$storeDir; 
		else 
			$store_dir = current($args);
		if( NULL!==$store_dir ) 
		{
			return $this->__copyTo(array($store_dir));
		} 
		return false;
	} 
	
	private function __copyRecrs($set, $fnam, $udir) 
	{ 
		foreach( $set as $key => $tmp_name ) 
		{ 
			if(is_array($tmp_name)) 
			{
				$this->__copyRecrs($tmp_name, $fnam[$key], $udir);
			} 
			else 
			{
				$tp = correct_path(ROOT_DIR.$udir.DS.$fnam[$key]); 
				copy( $tmp_name, $tp ); 
			}
		} 
	}
	
	final private function __copyTo( $args ) 
	{ 
		if( $this->__isEmpty() ) return NULL;
		if( empty($args) ) 
		{ 
			error::trigger("The <code><b>FileUploader::CopyTo(\"upload_dir/\")</b></code> function is should be has a target file path."); 
		} 
		else if( !is_string(current($args)) ) 
		{ 
			error::trigger("The parameter of <code><b>FileUploader::CopyTo(\"upload_dir/\")</b></code> function is should be in <code>String</code> type.");
		} 
		$target_path = current($args); 
		if( $this->__isFile() )	
		{ 
			if( fx::file_exists($this->_chunkCur['tmp_name']) ) 
			{
				$tp = correct_path(ROOT_DIR.$target_path.DS.$this->_chunkCur['name']); 
				copy( $this->_chunkCur['tmp_name'], $tp ); 
			}
		} 
		else if( $this->__isFileList() ) 
		{ 
			foreach( $this->_chunkCur['tmp_name'] as $i => $tn ) 
			{
				if( fx::file_exists($tn) ) 
					copy( $tn, correct_path(ROOT_DIR.$target_path.DS.$this->_chunkCur['name'][$i]) ); 
			}
		} 
		else if( $this->__isAll() ) 
		{ 
			foreach( cache::$upload_file as $file ) 
			{ 
				$tn = $file['tmp_name']; 
				if( fx::file_exists($tn) ) 
					copy( $tn, correct_path(ROOT_DIR.$target_path.DS.$file['name']) ); 
			} 
		} 
		else 
		{
			$this->__copyRecrs($this->_chunkCur['tmp_name'], $this->_chunkCur['name'], $target_path);
		}
		return $this;
	} 
	
	final private function __totalSize() 
	{
		try 
		{
			return cache::$upload_size;
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage().BL.error::position($e) ); 
		}
	} 
	
	final private function __totalLength() 
	{ 
		try 
		{ 
			return count(cache::$upload_type); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage().BL.error::position($e) );
		}
	} 
	
	final private function __typeRecrs( $in, &$out ) 
	{ 
		foreach( $in as $item ) 
		{ 
			if(is_string($item)) 
			{ 
				$out[] = $item;
			} 
			else 
			{ 
				$this->__typeRecrs($item, $out);
			} 
		} 
	} 
	
	final private function __szRecrs( $in, &$out ) 
	{ 
		foreach( $in as $item ) 
		{ 
			if(is_numeric($item)) 
			{ 
				$out += $item;
			} 
			else 
			{ 
				$this->__szRecrs($item, $out);
			} 
		} 
	} 
	
	final private function __lenRecrs( $in, &$out ) 
	{
		foreach( $in as $item ) 
		{ 
			if(is_string($item)) 
			{ 
				$out += 1;
			} 
			else 
			{ 
				$this->__lenRecrs($item, $out);
			} 
		} 
	} 
	
	final private function __selectAll() 
	{ 
		global $_file;
		if( empty($_file) ) return NULL; 
		try 
		{ 
			$this->_chunkCur = (count($_file)===1)?current($_file):$_file;
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() ); 
		} 
		return $this;
	} 
	
	final private function __select( $args ) 
	{ 
		global $_file; 
		if( empty($_file) ) return NULL;
		if( empty($args) ) 
		{ 
			error::trigger("The <code><b>FileUploader::Select('input')</b></code> function is must be has a least of one parameter."); 
		} 
		$key = current($args); 
		if( !is_string($key) ) 
		{ 
			error::trigger("The parameter of <code><b>FileUploader::Select('input')</b></code> function should be a string. Specifies the input key used for upload the files.");
		} 
		if( $key=="*" ) 
		{
			return $this->__selectAll();
		}
		if( isset($_file[$key]) ) 
		{
			$this->_chunkCur = $_file[$key]; 
			return $this; 
		} 
		return NULL;
	} 
	
	final private function __status() 
	{ 
		global $_file;
		if( $this->__isEmpty() ) return NULL;
		global  $_file_uploaded_length, 
				$_file_uploaded_size; 
		$out = 0;
		if( ($this->__isFile() || $this->__isFileList() || $this->__isCustom()) ) 
		{
			if( !$out&&NULL!==$this->_alwUplType ) 
			{ 
				$uplTyps = [];
				if( $this->__isFile() ) $uplTyps[] = $this->__type();  
				else if( $this->__isFileList() ) $uplTyps = $this->__type(); 
				else $this->__typeRecrs( $this->_chunkCur['type'], $uplTyps ); 
				foreach( $uplTyps as $typ ) 
				{ 
					if( !in_array($typ, $this->_alwUplType) ) 
					{
						$out = 1; 
						break;
					}
				} 
			} 
			if( !$out&&NULL!==$this->_alwInpLen ) 
			{ 
				$len = 0;
				if( $this->__isFile() ) $len = 1;  
				else if( $this->__isFileList() ) $len = count($this->__type()); 
				else $this->__lenRecrs( $this->_chunkCur['type'], $len ); 
				if( $this->_alwInpLen<$len ) 
					$out = 2;
			} 
			if( !$out&&NULL!==$this->_alwInpSize ) 
			{ 
				$sz = 0;
				if( $this->__isFile() || $this->__isFileList() ) $sz = $this->__size();  
				else $this->__szRecrs( $this->_chunkCur['size'], $sz ); 
				if( ($this->_alwInpSize*1048576)<$sz ) 
					$out = 3;
			}
		}
		else 
		{
			if( !$out&&NULL!==$this->_alwUplType ) 
			{ 
				$uplTyps = cache::$upload_type; 
				foreach( $uplTyps as $typ ) 
				{ 
					if( !in_array($typ, $this->_alwUplType) ) 
					{
						$out = 1; 
						break;
					}
				} 
			} 
			if( !$out&&NULL!==$this->_alwUplLen ) 
			{ 
				if( $this->_alwUplLen<count(cache::$upload_type) ) 
					$out = 2;
			} 
			if( !$out&&NULL!==$this->_alwUplSize ) 
			{ 
				if( ($this->_alwUplSize*1048576)<cache::$upload_size ) 
					$out = 3;
			} 
		} 
		return $out;
	} 
	
	final private function __changeName( $args ) 
	{ 
		if( $this->__isAll()&&(!$this->__isFile()||!$this->__isFileList()||!$this->__isCustom()) ) 
		{ 
			error::trigger("The function <code><b>FileUploader::ChangeName()</b></code> does not support for <code><b>SelectAll()</b></code> or <code><b>Select('*')</b></code>.");
		} 
		if( empty($args) ) 
			error::trigger("The <code><b>FileUploader::ChangeName()</b></code> function has a least of 1 parameter.");
		return $this->__name( $args ); 
	} 
	
	final private function __name( $args ) 
	{ 
		if( empty($args) ) 
		{
			if( $this->__isEmpty() ) return NULL;
			return (isset($this->_chunkCur['name']))?$this->_chunkCur['name']:NULL;
		} 
		else 
		{ 
			if( $this->__isFile() || $this->__isFileList() || $this->__isCustom() ) 
			{
				if( $this->__isFile() ) 
				{
					$name = current($args); 
					if( is_array($name) ) 
						$args = $name; 
					$name = current($args);
					if( !is_string($name) ) 
						error::trigger("<code><b>FileUploader::Name('Join')</b></code>: Since the input file does not a multiple input. So that, this name is must be a string.");
				} 
				else 
				{ 
					if( !is_array(current($args)) ) 
						error::trigger("<code><b>FileUploader::Name([])</b></code> Since the input file does a multiple input, So that, the name is should be in an array.");
				} 
				$this->_chunkCur['name'] = current($args); 
			}
		} 
		return $this;
	} 
	
	final private function __type() 
	{ 
		if( $this->__isEmpty() ) return NULL; 
		return (isset($this->_chunkCur['type']))?$this->_chunkCur['type']:cache::$upload_type; 
	} 
	
	final private function __size( $args ) 
	{ 
		$out = 0;
		$unit = 1; 
		if( !empty($args) ) 
		{
			$param = current($args);
			if( !is_string($param) ) 
				error::trigger("The parameter of <code><b>FileUploader::Size()</b></code> is must be in String Type. <i>(Ex:\"KB\")</i>"); 
			if( "KB"===$param ) 
				$unit = 1024;
			else if( "MB"===$param ) 
				$unit = 1048576;
		}
		if( $this->__isEmpty() ) return NULL;
		if( $this->__isAll() ) $out = $this->__totalSize(); 
		else if( $this->__isFile() ) $out = $this->_chunkCur['size']; 
		else if( $this->__isFileList() ) $out = array_sum($this->_chunkCur['size']); 
		else $this->__szRecrs( $this->_chunkCur['size'], $out ); 
		return (double) number_format(($out/$unit), 3);
	} 
	
	final private function __length() 
	{ 
		if( $this->__isEmpty() ) return NULL;
		if( $this->__isAll() ) return $this->__totalLength(); 
		else if( $this->__isFile() ) return 1; 
		else if( $this->__isFileList() ) return count($this->_chunkCur['name']); 
		else 
		{
			$out = 0;
			$this->__lenRecrs($this->_chunkCur['name'], $out);
			return $out;
		};
	}
	
	final private function __path() 
	{ 
		if( $this->__isEmpty() ) return NULL;
		return (isset($this->_chunkCur['tmp_name']))?$this->_chunkCur['tmp_name']:cache::$upload_file; 
	} 
	
	final private function __isAll() 
	{ 
		global $_file; 
		if( $this->__isEmpty() ) return NULL;
		return (count($_file)===1)?current($_file)===$this->_chunkCur:$_file===$this->_chunkCur;
	} 
	
	final private function __isFile() 
	{ 
		if( $this->__isEmpty() ) return NULL; 
		if( isset($this->_chunkCur['name']) ) 
			return is_string($this->_chunkCur['name']);
		return false;
	} 
	
	final private function __isFileList() 
	{ 
		if( $this->__isEmpty() ) return NULL;
		if( isset($this->_chunkCur['name']) ) 
			if( is_array($this->_chunkCur['name']) ) 
			{
				if( isset($this->_chunkCur['name'][0]) ) 
				{ 
					return is_string($this->_chunkCur['name'][0]);
				} 
			}
		return false;
	} 
	
	final private function __isCustom() 
	{ 
		global $_file;
		if( $this->__isEmpty() ) return NULL;
		if( count($_file)>1 ) 
		{
			return (!$this->__isFile())&&(!$this->__isFileList())&&(!$this->__isAll());
		} 
		else 
		{ 
			return (!$this->__isFile())&&(!$this->__isFileList());
		}
	} 
	
	final private function __data( $args ) 
	{ 	
		global $_file; 
		try 
		{
			$argsNum = count($args); 
			if( 1<$argsNum ) 
			{ 
				throw new Exception("The <code><b>FileUploader::Data()</b></code> function is has 0 or 1 parameter only."); 
			} 
			if( empty($_file)) return NULL;
			if( 1===$argsNum ) 
			{
				$key = current($args); 
				if( !is_string($key) ) 
				{ 
					throw new Exception("The parameter of <code><b>FileUploader::Data()</b></code> function is must be a string."); 
				} 
				else if( array_key_exists($key, $_file) ) 
				{ 
					return $_file[$key]; 
				} 
				return NULL; 
			} 
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() );
		} 
		return $_file; 
	} 
	
	final private function __has( $args ) 
	{ 
		global $_file; 
		try 
		{
			if( empty($args) ) 
			{ 
				throw new Exception("The <code><b>FileUploader::Has()</b></code> function is must be has a least of one parameter in String type."); 
			} 
			$key = current($args); 
			if( is_string($key) ) 
			{
				return array_key_exists( $key, $_file ); 
			} 
			else 
			{ 
				throw new Exception("The parameter of <code>FileUploader::Has(<b>".'$key'."</b>)</code> function is must a string."); 
			} 
		}
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage().BL.error::position($e) );
		} 
		return false;
	} 
	
	final static private function __backup( $args ) 
	{ 
		$tp = fx::correct_path(ROOT_DIR.fileuploader::$uploadDir.$args[1]);
		$rs = fx::move_uploaded_file( $args[0], $tp ); 
		if( $rs ) 
		{ 
			cache::$upload_file[] = array( 'name'=>$args[2], 'tmp_name'=>$tp ); 
			cache::$upload_size += filesize($tp);
			return $tp;
		} 
		return false;
	} 
	
}