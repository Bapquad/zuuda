<?php
namespace Zuuda;

abstract class Controller implements iController, iDeclare, iBlock 
{
	private $_module;
	private $_controller;
	private $_action;
	private $_view;
	private $_template;
	private $_model;
	
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
		if( $name == 'model' 
		 || $name == 'view' )
			return $this->{ '_' . $name };
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
	public function Share( $name, $value ) { return $this->__share( $name, $value ); }
	public function Compact( $name, $value ) { return $this->__compact( $name, $value ); }
	public function Assign( $name, $value ) { return $this->__assign( $name, $value ); }
	public function Set( $name, $value ) { return $this->__setVar( $name, $value ); }
	public function Render( $template = NULL, $args = NULL ) { return $this->__render( $template, $args ); } 
	public function Json( $args ) { return $this->__json( $args ); } 
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
	final public function FinalRender( Application $appInst ) { $this->__finalRender( $this->_template ); }

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
			else if( isset($configs['SHOW_MODEL_WARNING']) ) 
			{
				echo "<div style=\"background-color:#000;color:#fff;padding:1rem;\">Your model '<b style=\"font-weight:bold\">" . $model_class_name . "</b>' had not found</div>";
			}
		} 
		
		$view_class_name = __currentViewClass(); 
		if( __availbleClass( $view_class_name ) )
		{
			$this->__setVarView( new $view_class_name() );
		}
		else if( isset($configs['SHOW_VIEW_WARNING']) ) 
		{
			echo "<div style=\"background-color:#000;color:#fff;padding:1rem;\">Your view '<b style=\"font-weight:bold\">" . $view_class_name . "</b>' had not found</div>";
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
	
	final protected function __share( $name, $value ) 
	{
		return $this->__setVar( $name, $value );
	}
	
	final protected function __compact( $name, $value ) 
	{
		return $this->__setVar( $name, $value );
	}
	
	final protected function __assign( $name, $value ) 
	{
		return $this->__setVar( $name, $value );
	}

	final protected function __setVar( $name, $value ) 
	{
		$view = $this->__getView();
		if( !is_null( $view ) && !$this->__addBlock( $value, $name ) ) 
		{
			$view->set( $name, $value );
		}
		return $this;
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
	
	final protected function __finalRender() 
	{
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

		if( isset( $_SERVER[ 'REQUEST_URI' ] ) ) 
		{
			$url = $_SERVER[ 'REQUEST_URI' ];
		} 
		else 
		{
			$url = PS . $url;
		} 

		$thread_id = md5( $url );

		if( !empty( $_FILES ) && isset( $configs[ 'MEDIA' ] ) ) 
		{
			$n = 'name';
			$t = 'type';
			$s = 'size';
			$e = 'error';
			$p = 'tmp_name';
			$md = 'media';

			foreach ($_FILES as $key => $value) 
			{
				if(is_array( $_FILES[ $key ][ $n ] ) ) 
				{
					foreach( $_FILES[ $key ][ $n ] as $akey => $file ) 
					{
						$tn = $_FILES[ $key ][ $p ][ $akey ];
						$fn = $_FILES[ $key ][ $n ][ $akey ];
						$tp = $_FILES[ $key ][ $t ][ $akey ];
						if( file_exists( $tn ) && array_key_exists( $tp, $configs[ 'MEDIA' ] ) ) 
						{
							$tp = TMP_DIR . $md . DS . $fn;
							move_uploaded_file( $tn, $tp );
							$_FILES[ $key ][ $p ][ $akey ] = $tp;
						}
					}
				} 
				else 
				{
					$tn = $_FILES[ $key ][ $p ];
					$fn = $_FILES[ $key ][ $n ];
					$tp = $_FILES[ $key ][ $t ];
					if( file_exists( $tn ) && array_key_exists( $tp, $configs[ 'MEDIA' ] ) ) 
					{
						$tp = TMP_DIR . $md . DS . $fn;
						move_uploaded_file( $tn, $tp );
						$_FILES[ $key ][ $p ] = $tp;
					}
				}
			}
			Session::Register( "_file_vertifier" . $thread_id, array( 'fixed'=>false, 'data'=>$_FILES ) );
		}
		
		if( !empty($_POST) ) 
		{
			Session::Register( "_mass_vertifier" . $thread_id, array( 'fixed'=>false, 'data'=>$_POST ) );
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