<?php
namespace Zuuda;

abstract class Controller implements iController, iDeclare, iBlock 
{
	private $_module;
	private $_controller;
	private $_action;
	private $_view;
	private $_model;
	
	protected function _getModule() { return $this->_module; }
	protected function _getControler() { return $this->_controller; }
	protected function _getAction() { return $this->_action; }
	protected function _getView() { return $this->_view; }
	protected function _getModel() { return $this->_model; }
	
	private function _setModule( $value ) { $this->_module = $value; return $this; }
	private function _setController( $value ) { $this->_controller = $value; return $this; }
	private function _setAction( $value ) { $this->_action = $value; return $this; }
	private function _setView( $value ) { $this->_view = $value; return $this; }
	private function _setModel( $value ) { $this->_model = $value; return $this; }
	
	protected function GetModule() { return $this->_getModule(); }
	protected function GetController() { return $this->_getControler(); }
	protected function GetAction() { return $this->_getAction(); }
	protected function GetView() { return $this->_getView(); }
	protected function GetModel() { return $this->_getModel(); }
	
	private function SetModule( $value ) { return $this->_setModule( $value ); }
	private function SetController( $value ) { return $this->_setController( $value ); }
	private function SetAction( $value ) { return $this->_setAction( $value ); }
	private function SetView( $value ) { return $this->_setView( $value ); }
	private function SetModel( $value ) { return $this->_setModel( $value ); }
	
	public function __get( $name ) 
	{
		if( $name == 'model' 
		 || $name == 'view' )
			return $this->{ '_' . $name };
	}
	
	/** Implements interface iDeclare */
	public function IncludeMeta( $value ) { return $this->_includeMeta( $value ); }
	public function IncludeHtml( $value ) { return $this->_includeHtml( $value ); }
	public function IncludeCss( $value ) { return $this->_includeCss( $value ); }
	public function IncludeJs( $value ) { return $this->_includeJs( $value ); }
	public function PreloadMeta( $value ) { return $this->_preloadMeta( $value ); }
	public function PreloadHtml( $value ) { return $this->_preloadHtml( $value ); }
	public function PreloadCss( $value ) { return $this->_preloadCss( $value ); }
	public function PreloadJs( $value ) { return $this->_preloadJs( $value ); }
	public function Assign( $name, $value ) { return $this->_assign( $name, $value ); }
	public function Set( $name, $value ) { return $this->_set( $name, $value ); }
	public function Render( $template = NULL, $aggrs = NULL ) { return $this->_render( $template, $aggrs ); }
	
	/** Implements interface iBlock */
	public function AddBlock( $block, $force_name = NULL ) { return $this->_addBlock( $block, $force_name ); }
	
	/** Implement Interface iController */
	public function BeforeAction( $query = NULL ) {}
	public function AfterAction( $query = NULL ) {} 
	public function CheckMass( $query = NULL ) { return $this->_checkMass(); }
	
	final public function rootName() { return __CLASS__; }

	public function __construct() 
	{
		global $inflect;
		global $configs;
		global $url;

		$this->_setModule( $configs['MODULE'] );
		$this->_setController( $configs['CONTROLLER'] );
		$this->_setAction( $configs['ACTION'] );
		
		if( _hasBase() ) 
		{
			$model_class_name = _currentModelClass();
			if( _availbleClass( $model_class_name ) ) 
			{
				$this->_setModel( new $model_class_name );
			}
			else if( $configs[ DEVELOPER_WARNING ] ) 
			{
				echo "<p>Your model '" . $model_class_name . "' had not found</p>";
			}
		} 
		
		$view_class_name = _currentViewClass(); 
		if( _availbleClass( $view_class_name ) )
		{
			$this->_setView( new $view_class_name() );
		}
		else if( $configs[ DEVELOPER_WARNING ] ) 
		{
			echo "<p>Your view" . $view_class_name . " had not found</p>";
		}
	}
	
	protected function _includeMeta( $value ) 
	{
		$view = $this->_getView();
		if( isset( $view ) )
		{
			$view->includeTag( $value );
		}
		return $this;
	}
	
	protected function _includeHtml( $value ) 
	{
		$view = $this->_getView();
		if( isset( $view ) )
		{
			$view->includeTag( $value );
		}
		return $this;
	}
	
	protected function _includeCss( $value ) 
	{
		$view = $this->_getView();
		if( isset( $view ) )
		{
			$view->includeCss( $value );
		}
		return $this;
	}
	
	protected function _includeJs( $value ) 
	{
		$view = $this->_getView();
		if( isset( $view ) )
		{
			$view->includeJs( $value );
		}
		return $this;
	}
	
	protected function _preloadMeta( $value ) 
	{
		$view = $this->_getView();
		if( isset( $view ) )
		{
			$view->preloadTag( $value );
		}
		return $this;
	}
	
	protected function _preloadHtml( $value ) 
	{
		$view = $this->_getView();
		if( isset( $view ) )
		{
			$view->preloadTag( $value );
		}
		return $this;
	}
	
	protected function _preloadCss( $value ) 
	{
		$view = $this->_getView();
		if( isset( $view ) )
		{
			$view->preloadCss( $value );
		}
		return $this;
	}
	
	protected function _preloadJs( $value ) 
	{
		$view = $this->_getView();
		if( isset( $view ) )
		{
			$view->preloadJs( $value );
		}
		return $this;
	}
	
	protected function _assign( $name, $value ) 
	{
		return $this->_set( $name, $value );
	}

	protected function _set( $name, $value ) 
	{
		$view = $this->_getView();
		if( !is_null( $view ) && !$this->_addBlock( $value, $name ) ) 
		{
			$view->set( $name, $value );
		}
		return $this;
	}
	
	protected function _render( $template = NULL, $aggrs = NULL ) 
	{
		$view = $this->_getView();
		
		if( !is_null( $view ) ) 
		{
			if( !is_null( $aggrs ) ) 
			{
				foreach ( $aggrs as $key => $value ) 
				{
					$view->set( $key, $value );
				}
			}
			$view->render( $template );
		}
	}
	
	protected function _addBlock( $block, $force_name = NULL ) 
	{
		$view = $this->_getView();
		
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
	
	protected function _checkMass()  
	{
		global $_post;

		if( !empty( $_POST ) ) 
		{
			Session::Register( "_mass_vertifier", array( 'fixed'=>false, 'data'=>$_POST ) );
			
			if( isset( $_SERVER[ 'REQUEST_URI' ] ) ) 
			{
				$url = $_SERVER[ 'REQUEST_URI' ];
			} 
			else 
			{
				$url = PS . $url;
			}
			_direct( $url );
		}

		$_mass_vertifier_data = Session::Get( "_mass_vertifier" );
		if( !is_null( $_mass_vertifier_data ) ) 
		{
			$_post = $_mass_vertifier_data[ "data" ];
			Session::Unregister( "_mass_vertifier" );
		}
	}
}