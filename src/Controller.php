<?php
namespace Zuuda;

use Exception; 
use ReflectionClass;

abstract class Controller implements iController, iDeclare, iBlock 
{
	private $_module;
	private $_controller;
	private $_action;
	private $_view;
	private $_template;
	private $_model;
	private $_downloader;
	
	protected function __getModule() { return $this->_module; }
	protected function __getControler() { return $this->_controller; }
	protected function __getAction() { return $this->_action; }
	protected function __getView() { return $this->_view; }
	protected function __getModel() { return $this->_model; } 

	private function __resting( $seconds ) { sleep( $seconds ); } 
	
	private function __setVarModule( $value ) { $this->_module = $value; return $this; }
	private function __setVarController( $value ) { $this->_controller = $value; return $this; }
	private function __setVarAction( $value ) { $this->_action = $value; return $this; }
	private function __setVarView( $value ) { $this->_view = $value; return $this; }
	private function __setVarModel( $value ) { $this->_model = $value; return $this; }
	
	protected function GetModule() { return $this->__getModule(); }
	protected function GetController() { return $this->__getControler(); }
	protected function GetAction() { return $this->__getAction(); }
	protected function GetView() { return $this->__getView(); }
	protected function GetModel() { return $this->__getModel(); }
	
	private function SetModule( $value ) { return $this->__setVarModule( $value ); }
	private function SetController( $value ) { return $this->__setVarController( $value ); }
	private function SetAction( $value ) { return $this->__setVarAction( $value ); }
	private function SetView( $value ) { return $this->__setVarView( $value ); }
	private function SetModel( $value ) { return $this->__setVarModel( $value ); }
	
	public function __get( $name ) 
	{
		if( $name == 'model' || 
			$name == 'view' ) 
		{
			try 
			{
				if( $name == 'model' && NULL===$this->_model ) 
					throw new Exception("Your model <b>" . $model_class_name . "</b> coundn't found.");
				elseif( $name == 'view' && NULL===$this->_model ) 
					throw new Exception("Your view <b>" . $view_class_name . "</b> coundn't found.");
				else 
					return $this->{ '_' . $name }; 
			} 
			catch( Exception $e ) 
			{ 
				if( isset($configs['CONTROLER_ERRORS_WARNING']) ) 
					if( $configs['CONTROLER_ERRORS_WARNING'] ) 
						abort( 500, $e->getMessage() ); 
			} 
		}
	}
	
	/** Implements interface iDeclare */
	public function IncludeMeta( $value ) { return $this->__includeMeta( $value ); }
	public function IncludeHtml( $value ) { return $this->__includeHtml( $value ); }
	public function IncludeCss( $value ) { return $this->__includeCss( $value ); }
	public function IncludeJs( $value ) { return $this->__includeJs( $value ); }
	public function PreloadMeta( $value ) { return $this->__preloadMeta( $value ); }
	public function PreloadHtml( $value ) { return $this->__preloadHtml( $value ); }
	public function PreloadCss( $value ) { return $this->__preloadCss( $value ); }
	public function PreloadJs( $value ) { return $this->__preloadJs( $value ); }
	public function RequireJs( $value ) { return $this->__preloadJs( $value ); }
	public function RequireJui( $value ) { return $this->__requireJui( $value ); }
	public function IncludeJui( $value ) { return $this->__includeJui( $value ); }
	public function Assign() { return $this->__setVar( func_get_args(), func_num_args() ); }
	public function Set() { return $this->__setVar( func_get_args(), func_num_args() ); }
	public function Share() { return $this->__setVar( func_get_args(), func_num_args() ); }
	public function Render( $template = NULL, $args = NULL ) { return $this->__render( $template, $args ); } 
	public function Json( $args ) { return $this->__json( $args ); } 
	public function Cors() { return $this->__cors(); } 
	public function Download( $loader, $name=NULL ) { return $this->__download( $loader, $name ); } 
	public function CustomRender( $renderer, $args = NULL ) { $this->__customRender( $renderer, $args ); }
	public function RenderBy( $renderer, $args = NULL ) { $this->__customRender( $renderer, $args ); }
	public function RenderWith( $renderer, $args = NULL ) { $this->__customRender( $renderer, $args ); }
	
	/** Implements interface iBlock */
	public function AddBlock( $block, $force_name = NULL ) { return $this->__addBlock( $block, $force_name ); }
	
	/** Implement Interface iController */
	public function BeforeAction( $query = NULL ) { /**...*/ }
	public function AfterAction( $query = NULL ) { /**....*/ } 
	public function BeforeRender( $query = NULL ) { /**...*/ }
	public function CheckMass( $method ) { return $this->__checkMass( $method ); } 
	public function Resting( $seconds=2 ) { $this->__resting( $seconds ); } 
	
	final public function rootName() { return __CLASS__; }
	final public function FinalRender( $query = NULL ) { $this->__finalRender( $this->_template ); }

	public function __construct() 
	{
		global $inflect;
		global $configs;
		global $url;

		$this->__setVarModule( $configs['MODULE'] );
		$this->__setVarController( $configs['CONTROLLER'] );
		$this->__setVarAction( $configs['ACTION'] );
		
		if( __useDB() ) 
		{
			$model_class_name = __currentModelClass();
			if( __availbleClass( $model_class_name ) ) 
			{
				$this->__setVarModel( new $model_class_name );
			} 
		} 
		
		$view_class_name = __currentViewClass(); 
		if( __availbleClass( $view_class_name ) )
		{
			$ctrlRefl = new ReflectionClass($view_class_name); 
			$ttrInjts = $ctrlRefl->getConstructor()->getParameters(); 
			$args = array();
			foreach( $ttrInjts as $key => $arg ) 
			{
				$propName = $arg->getClass()->name; 
				$args[] = new $propName;
			}
			$dispatch = (empty($args))?new $view_class_name():$ctrlRefl->newInstanceArgs((array) $args);
			$this->__setVarView( $dispatch );
		}
	}
	
	final protected function __includeMeta( $value ) 
	{
		$view = $this->__getView();
		if( isset( $view ) )
		{
			$view->includeTag( $value );
		}
		return $this;
	}
	
	final protected function __includeHtml( $value ) 
	{
		$view = $this->__getView();
		if( isset( $view ) )
		{
			$view->includeTag( $value );
		}
		return $this;
	}
	
	final protected function __includeCss( $value ) 
	{
		$view = $this->__getView();
		if( isset( $view ) )
		{
			$view->includeCss( $value );
		}
		return $this;
	}
	
	final protected function __includeJs( $value ) 
	{
		$view = $this->__getView();
		if( isset( $view ) )
		{
			$view->includeJs( $value );
		}
		return $this;
	}
	
	final protected function __preloadMeta( $value ) 
	{
		$view = $this->__getView();
		if( isset( $view ) )
		{
			$view->preloadTag( $value );
		}
		return $this;
	}
	
	final protected function __preloadHtml( $value ) 
	{
		$view = $this->__getView();
		if( isset( $view ) )
		{
			$view->preloadTag( $value );
		}
		return $this;
	}
	
	final protected function __preloadCss( $value ) 
	{
		$view = $this->__getView();
		if( isset( $view ) )
		{
			$view->preloadCss( $value );
		}
		return $this;
	}
	
	final protected function __preloadJs( $value ) 
	{
		$view = $this->__getView();
		if( isset( $view ) )
		{
			$view->preloadJs( $value );
		}
		return $this;
	}

	final protected function __requireJui( $value ) 
	{
		$view = $this->__getView();
		if( isset( $view ) ) 
		{
			$view->requireJui( $value );
		} 
		return $this;
	}

	final protected function __includeJui( $value ) 
	{
		$view = $this->__getView();
		if( isset( $view ) ) 
		{
			$view->includeJui( $value );
		} 
		return $this;
	}

	final protected function __setVar( $args, $argsNum ) 
	{
		try 
		{
			if( 1==$argsNum ) 
			{
				$mixed = current($args); 
				$mixed = each($mixed);
				$name = $mixed['key']; 
				$value = $mixed['value']; 
			} 
			else if( 1<$argsNum ) 
			{ 
				$name = $args[0]; 
				$value = $args[1];
			} 
			else 
			{ 
				throw new \Exception( "The functions of <b>Controller::Assign(), Controller::Set(), and Controller::Share()</b> must be has least one parameter." ); 
			}
		
			$view = $this->__getView();
			if( !is_null( $view ) && !$this->__addBlock( $value, $name ) ) 
			{
				$view->set( $name, $value );
			}
			return $this;
		} 
		catch( \Exception $e ) 
		{ 
			abort( 500, $e->getMessage() );
		} 
	}
	
	final protected function __render( $template = NULL, $args = NULL ) 
	{
		$view = $this->__getView();
		
		if( !is_null( $view ) ) 
		{
			if( !is_null( $args ) ) 
			{
				while (list($a, $b) = each($args)) 
				{
					$view->set($a, $b);
				}
			} 
			$this->_template = $template;
		}
	} 
	
	final protected function __json( $args ) { $this->__getView()->jsonLayout( $args ); } 
	
	final protected function __cors() 
	{
		$view = $this->__getView(); 
		if( NULL!== $view ) 
		{
			$view->makeapi(); 
		} 
		else 
		{ 
			response::setcors(); 
			return $this; 
		}
	} 
	
	final protected function __download($fileLoader, $filename) 
	{
		$this->_downloader = $fileLoader->__download($filename);
		return $this;
	} 
	
	final protected function __finalRender() 
	{
		if( isset($this->_downloader) ) 
			return;
		$view = $this->__getView(); 
		if( $json = $view->isJson() ) 
			$view->renderJson( $json ); 
		else 
			$view->render( $this->_template );
	}

	private function __customRender( $render_name, $args = NULL ) 
	{
		$view = $this->__getView();

		if( !is_null( $view ) ) 
		{
			if( !is_null( $args ) ) 
			{
				while (list($a, $b) = each($args)) 
				{
					$view->set($a, $b);
				}
			}
			if( method_exists( $view, $render_name ) ) 
			{
				call_user_func_array( array( $view, $render_name ), array( $args ) );
			} 
			else abort();
		}
	}
	
	protected function __addBlock( $block, $force_name = NULL ) 
	{
		$view = $this->__getView();
		
		if( is_string( $block ) && is_object( $force_name ) ) 
		{
			$rep = $block;
			$block = $force_name;
			$force_name = $rep;
		}
		
		if( is_object( $block ) && !is_null( $view ) ) 
		{
			if( $block->rootName() == ZUUDA_SECTION_SYMBOL ) 
			{
				return $view->addBlock( $block, $force_name );
			}
		}
		return false;
	}
	
	protected function __checkMass( $requestMethod )  
	{
		global $_server, $_get, $_post, $_put, $_delete, $_file, $configs;

		if( isset($_SERVER['REQUEST_URI']) ) 
		{
			$url = $_SERVER['REQUEST_URI'];
		} 
		else 
		{
			$url = PS . $url;
		} 

		$thread_id = md5( $url );

		if( !empty( $_FILES ) && isset( $configs['MEDIA'] ) ) 
		{
			$n = 'name';
			$t = 'type';
			$s = 'size';
			$e = 'error';
			$p = 'tmp_name';
			$md = 'media';
			foreach ($_FILES as $key => $value) 
			{
				if( is_array($_FILES[$key][$n]) ) 
				{
					foreach( $_FILES[$key][$n] as $akey => $file ) 
					{
						$tn = $_FILES[$key][$p][$akey];
						$fn = $_FILES[$key][$n][$akey];
						$tp = $_FILES[$key][$t][$akey];
						if( file_exists($tn) && array_key_exists($tp, $configs['MEDIA']) ) 
						{
							$tp = TMP_DIR . $md . DS . $fn;
							move_uploaded_file( $tn, $tp );
							$_FILES[$key][$p][$akey] = $tp;
						}
					}
				} 
				else 
				{
					$tn = $_FILES[$key][$p];
					$fn = $_FILES[$key][$n];
					$tp = $_FILES[$key][$t];
					if( file_exists($tn) && array_key_exists($tp, $configs['MEDIA']) ) 
					{
						$tp = TMP_DIR . $md . DS . $fn;
						move_uploaded_file( $tn, $tp );
						$_FILES[$key][$p] = $tp;
					}
				}
			}
			Session::Register( "_file_vertifier" . $thread_id, array( 'fixed'=>false, 'data'=>$_FILES ) );
		}
		
		if( !empty($_POST) ) 
		{
			$data = array();
			foreach($_POST as $key => $value) 
			{
				if( !is_array($value) && !strlen($value) ) 
					$value = NULL;
				$data[$key] = $value; 
			}
			Session::Register( "_mass_vertifier" . $thread_id, array( 'fixed'=>false, 'data'=>$data ) );
			__direct( $url );
		}
		$_file_vertifier_data = Session::Get( "_file_vertifier" . $thread_id );
		$_mass_vertifier_data = Session::Get( "_mass_vertifier" . $thread_id );
		
		if( !is_null($_file_vertifier_data) ) 
		{
			$_file = $_file_vertifier_data[ "data" ]; 
			Session::Unregister( "_file_vertifier" . $thread_id );
		}
		
		if( !is_null($_mass_vertifier_data) ) 
		{	
			if( isset($_mass_vertifier_data[ "data" ]['_method']) ) 
			{
				$requestMethod = strtolower($_mass_vertifier_data[ "data" ]['_method']);
				unset( $_mass_vertifier_data[ "data" ]['_method'] ); 
				if( 'put'===$requestMethod ) 
					$_put = $_mass_vertifier_data[ "data" ]; 
				else if( 'delete'===$requestMethod ) 
					$_delete = $_mass_vertifier_data[ "data" ]; 
				else 
				{
					$_post = $_mass_vertifier_data[ "data" ]; 
					$requestMethod = 'post';
				}
			} 
			else
			{				
				$_post = $_mass_vertifier_data[ "data" ]; 
				$requestMethod = 'post';
			}
			Session::Unregister( "_mass_vertifier" . $thread_id ); 
		} 
		else if( 'put'===$requestMethod ) 
		{
			$requestHeaders = getallheaders(); 
			$requestBody = file_get_contents("php://input");
			$_put = json_decode( $requestBody ); 
		} 
		else if( 'delete'===$requestMethod ) 
			$_delete = $_get; 
		else 
			$requestMethod = 'get'; 
		
		$_server['request_method'] = strtoupper($requestMethod); 
		$_server['REQUEST_METHOD'] = $_server['request_method'];
		$configs['request_method'] = $_server['request_method'];
		$configs['REQUEST_METHOD'] = $_server['request_method'];
	}
}