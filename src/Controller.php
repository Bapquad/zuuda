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
	
	protected function _getModule() { return $this->_module; }
	protected function _getControler() { return $this->_controller; }
	protected function _getAction() { return $this->_action; }
	protected function _getView() { return $this->_view; }
	protected function _getModel() { return $this->_model; } 

	private function _resting( $seconds ) { sleep( $seconds ); } 
	
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
	public function RequireJs( $value ) { return $this->_preloadJs( $value ); }
	public function RequireJui( $value ) { return $this->_requireJui( $value ); }
	public function IncludeJui( $value ) { return $this->_includeJui( $value ); }
	public function Share( $name, $value ) { return $this->_share( $name, $value ); }
	public function Compact( $name, $value ) { return $this->_compact( $name, $value ); }
	public function Assign( $name, $value ) { return $this->_assign( $name, $value ); }
	public function Set( $name, $value ) { return $this->_set( $name, $value ); }
	public function Render( $template = NULL, $args = NULL ) { return $this->_render( $template, $args ); } 
	public function Json( $args ) { return $this->_json( $args ); } 
	public function CustomRender( $renderer, $args = NULL ) { $this->_customRender( $renderer, $args ); }
	public function RenderBy( $renderer, $args = NULL ) { $this->_customRender( $renderer, $args ); }
	public function RenderWith( $renderer, $args = NULL ) { $this->_customRender( $renderer, $args ); }
	
	/** Implements interface iBlock */
	public function AddBlock( $block, $force_name = NULL ) { return $this->_addBlock( $block, $force_name ); }
	
	/** Implement Interface iController */
	public function BeforeAction( $query = NULL ) { /**...*/ }
	public function AfterAction( $query = NULL ) { /**....*/ } 
	public function BeforeRender( $query = NULL ) { /**...*/ }
	public function CheckMass( $query = NULL ) { return $this->_checkMass(); } 
	public function Resting( $seconds=2 ) { $this->_resting( $seconds ); } 
	
	final public function rootName() { return __CLASS__; }
	final public function FinalRender( Application $appInst ) { $this->_finalRender( $this->_template ); }

	public function __construct() 
	{
		global $inflect;
		global $configs;
		global $url;

		$this->_setModule( $configs['MODULE'] );
		$this->_setController( $configs['CONTROLLER'] );
		$this->_setAction( $configs['ACTION'] );
		
		if( _useDB() ) 
		{
			$model_class_name = _currentModelClass();

			if( _availbleClass( $model_class_name ) ) 
			{
				$this->_setModel( new $model_class_name );
			}
			else if( isset($configs['SHOW_MODEL_WARNING']) ) 
			{
				echo "<div style=\"background-color:#000;color:#fff;padding:1rem;\">Your model '<b style=\"font-weight:bold\">" . $model_class_name . "</b>' had not found</div>";
			}
		} 
		
		$view_class_name = _currentViewClass(); 
		if( _availbleClass( $view_class_name ) )
		{
			$this->_setView( new $view_class_name() );
		}
		else if( isset($configs['SHOW_VIEW_WARNING']) ) 
		{
			echo "<div style=\"background-color:#000;color:#fff;padding:1rem;\">Your view '<b style=\"font-weight:bold\">" . $view_class_name . "</b>' had not found</div>";
		}
	}
	
	final protected function _includeMeta( $value ) 
	{
		$view = $this->_getView();
		if( isset( $view ) )
		{
			$view->includeTag( $value );
		}
		return $this;
	}
	
	final protected function _includeHtml( $value ) 
	{
		$view = $this->_getView();
		if( isset( $view ) )
		{
			$view->includeTag( $value );
		}
		return $this;
	}
	
	final protected function _includeCss( $value ) 
	{
		$view = $this->_getView();
		if( isset( $view ) )
		{
			$view->includeCss( $value );
		}
		return $this;
	}
	
	final protected function _includeJs( $value ) 
	{
		$view = $this->_getView();
		if( isset( $view ) )
		{
			$view->includeJs( $value );
		}
		return $this;
	}
	
	final protected function _preloadMeta( $value ) 
	{
		$view = $this->_getView();
		if( isset( $view ) )
		{
			$view->preloadTag( $value );
		}
		return $this;
	}
	
	final protected function _preloadHtml( $value ) 
	{
		$view = $this->_getView();
		if( isset( $view ) )
		{
			$view->preloadTag( $value );
		}
		return $this;
	}
	
	final protected function _preloadCss( $value ) 
	{
		$view = $this->_getView();
		if( isset( $view ) )
		{
			$view->preloadCss( $value );
		}
		return $this;
	}
	
	final protected function _preloadJs( $value ) 
	{
		$view = $this->_getView();
		if( isset( $view ) )
		{
			$view->preloadJs( $value );
		}
		return $this;
	}

	final protected function _requireJui( $value ) 
	{
		$view = $this->_getView();
		if( isset( $view ) ) 
		{
			$view->requireJui( $value );
		} 
		return $this;
	}

	final protected function _includeJui( $value ) 
	{
		$view = $this->_getView();
		if( isset( $view ) ) 
		{
			$view->includeJui( $value );
		} 
		return $this;
	}
	
	final protected function _share( $name, $value ) 
	{
		return $this->_set( $name, $value );
	}
	
	final protected function _compact( $name, $value ) 
	{
		return $this->_set( $name, $value );
	}
	
	final protected function _assign( $name, $value ) 
	{
		return $this->_set( $name, $value );
	}

	final protected function _set( $name, $value ) 
	{
		$view = $this->_getView();
		if( !is_null( $view ) && !$this->_addBlock( $value, $name ) ) 
		{
			$view->set( $name, $value );
		}
		return $this;
	}
	
	final protected function _render( $template = NULL, $args = NULL ) 
	{
		$view = $this->_getView();
		
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
	
	final protected function _json( $args ) { $this->_getView()->jsonLayout( $args ); }
	
	final protected function _finalRender() 
	{
		$view = $this->_getView();
		if( $json = $view->isJson() ) 
			$view->renderJson( $json ); 
		else 
			$view->render( $this->_template );
	}

	private function _customRender( $render_name, $args = NULL ) 
	{
		$view = $this->_getView();

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
		global $_post, $_file, $configs;

		if( isset( $_SERVER[ 'REQUEST_URI' ] ) ) 
		{
			$url = $_SERVER[ 'REQUEST_URI' ];
		} 
		else 
		{
			$url = PS . $url;
		} 

		$thread_id = md5( $url );

		if( !empty( $_POST ) || ( !empty( $_FILES ) && isset( $configs[ 'MEDIA' ] ) ) ) 
		{
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
			Session::Register( "_mass_vertifier" . $thread_id, array( 'fixed'=>false, 'data'=>$_POST ) );
			_direct( $url );
		}

		$_file_vertifier_data = Session::Get( "_file_vertifier" . $thread_id );
		$_mass_vertifier_data = Session::Get( "_mass_vertifier" . $thread_id );

		if( !is_null( $_mass_vertifier_data ) ) 
		{
			$_file = $_file_vertifier_data[ "data" ];
			$_post = $_mass_vertifier_data[ "data" ];
			Session::Unregister( "_file_vertifier" . $thread_id );
			Session::Unregister( "_mass_vertifier" . $thread_id );
		}
	}
}