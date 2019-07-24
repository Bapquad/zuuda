<?php
namespace Zuuda;

define ( 'ZUUDA_SECTION_SYMBOL', 'Zuuda\Section' );
define ( 'ZUUDA_WIDGET_SYMBOL', 'Zuuda\Section' );

use Exception;

abstract class Section implements iHTML, iTemplate, iSection, iDeclare, iWidgetHost 
{
	private $_vars = array();
	private $_widgets = array();
	private $_name;
	private $_tpl_name = 'template.tpl';
	
	private $_head_assets = array( 
		STYLE_ASSET => array(), 
		SCRIPT_ASSET=> array(), 
		HTML_ASSET	=> array()
	);
	
	private $_content_assets = array(
		STYLE_ASSET	=> array(), 
		SCRIPT_ASSET=> array(), 
		HTML_ASSET	=> array()
	);
	
	final protected function _getVars() { return $this->_vars; }
	final protected function _getWidgets() { return $this->_widgets; }
	/** private function _getName */
	final protected function _getTemplate() { return $this->_tpl_name; }
	final protected function _getHeadAssets() { return $this->_head_assets; }
	final protected function _getContentAssets() { return $this->_content_assets; }
	
	final protected function _setVars( $vars ) { $this->_vars = $vars; return $this; }
	final protected function _setVar( $name, $value ) { $this->_vars[ $name ] = $value; return $this; }
	final protected function _addVar( $name, $value ) { $this->_vars[ $name ] = $value; return $this; }
	final protected function _setName( $value ) { $this->_name = $value; return $this; }
	final protected function _setTemplate( $value ) { $this->_tpl_name = $value; return $this; }
	final protected function _setLayout( $value ) { $this->_tpl_name = $value; return $this; }
	final protected function _setHeadAssets( $value ) { $this->_head_assets = $value; return $this; }
	final protected function _setContentAssets( $value ) { $this->_content_assets = $value; return $this; }
	
	final public function GetVars() { return $this->_getVars(); }
	final public function SetVar( $name, $value ) { return $this->_setVar( $name, $value ); }
	final public function AddVar( $name, $value ) { return $this->_addVar( $name, $value ); }
	final public function __view__get_head_assets() { return $this->_head_assets; }
	final public function __view__get_content_assets() { return $this->_content_assets; }
	final public function __view__merge_vars( $vars ) { $this->_vars = array_merge( $vars, $this->_vars ); return $this; }

	final public function Share( $name, $value ) { return $this->_share( $name, $value ); }
	final public function Compact( $name, $value ) { return $this->_compact( $name, $value ); }
	final public function Assign( $name, $value ) { return $this->_assign( $name, $value ); }
	final public function Set( $name, $value ) { return $this->_set( $name, $value ); }
	final public function SetTitle( $value ) { return $this->_setTitle( $value ); }
	final public function GetName() { return $this->_getName(); }
	final public function SetName( $value ) { return $this->_setName( $value ); }
	final public function SetTemplate( $tpl_path ) { return $this->_setTemplate( $tpl_path ); }
	final public function SetLayout( $tpl_path ) { return $this->_setLayout( $tpl_path ); } 
	final public function AddWidget( $widget, $force_name = NULL ) { return $this->_addWidget( $widget, $force_name ); }
	final public function GetWidget( $name ) { return $this->_getWidget( $name ); }
	final public function Render( $template = NULL, $args = NULL ) { return $this->_render( $template, $args ); }
	
	final public function HeadAsset( $type, $value ) 
	{
		array_push( $this->_head_assets[ $type ], $value );
	}
	
	final public function PreloadMeta( $value ) 
	{
		$this->headAsset( HTML_ASSET, $value );
	}
	
	final public function PreloadHtml( $value ) 
	{
		$this->headAsset( HTML_ASSET, $value );
	}
	
	final public function PreloadCss( $value ) 
	{
		$this->headAsset( STYLE_ASSET, $value );
	}
	
	final public function PreloadJs( $value ) 
	{
		$this->headAsset( SCRIPT_ASSET, $value );
	}
	
	final public function ContentAsset( $type, $value ) 
	{
		array_push( $this->_content_assets[ $type ], $value );
	}
	
	final public function IncludeMeta( $value ) 
	{
		$this->contentAsset( HTML_ASSET, $value );
	}
	
	final public function IncludeHtml( $value ) 
	{
		$this->contentAsset( HTML_ASSET, $value );
	}
	
	final public function IncludeCss( $value ) 
	{
		$this->contentAsset( STYLE_ASSET, $value );
	}
	
	final public function IncludeJs( $value ) 
	{
		$this->contentAsset( SCRIPT_ASSET, $value );
	}
	
	final public function rootName() { return __CLASS__; }
	
	public function __construct( $section_name = NULL, $section_tpl_name = NULL ) 
	{
		if( !is_null( $section_name ) ) 
		{
			$this->_name = $section_name;
		}
		
		if( !is_null( $section_tpl_name ) ) 
		{
			$this->_tpl_name = $section_tpl_name;
		}
	}
	
	private function _render( $template = NULL, $args = NULL ) 
	{
		if( !is_null( $template ) ) 
		{
			$this->_setTemplate( $template );
		}
		return $this->_renderLayout( $args );
	}
	
	private function _getWidget( $name ) 
	{
		$widgets = $this->_getWidgets();
		
		if( isset( $widgets[ $name ] ) ) 
		{
			return $widgets[ $name ];
		} 
		
		return NULL;
	}
	
	private function _addWidget( $widget, $force_name = NULL ) 
	{
		if( is_object( $widget ) ) 
		{
			if( method_exists( $widget, 'rootName') ) 
			{
				if( $widget->rootName() == ZUUDA_WIDGET_SYMBOL ) 
				{
					$widget_name = ( is_null( $force_name ) ) ? $widget->getName() : $force_name;
					if( !is_null( $widget_name ) ) 
					{
						$this->_widgets[ $widget_name ] = $widget;
						return $this;
					}
				}
			}
		}
		return false;
	}
	
	private function _getName() 
	{
		global $configs;
		try 
		{
			if( is_null( $this->_name ) ) 
			{
				if( $configs[ DEVELOPER_WARNING ] ) 
					throw new Exception( 'Your ' . get_class( $this ) . ' has no name!' ); 
			}
			else 
				return $this->_name;
		}
		catch( Exception $e ) 
		{
			echo 'ERROR (Zudda): ' . $e->getMessage(); 
		}
	}
	
	private function _set( $name, $value )
	{
		if( is_array( $name ) ) 
		{
			$vars  = $this->_getVars();
			$this->_setVars( array_merge( $vars, $name ) );
		} 
		else 
		{
			$this->_addVar( $name, $value );
		} 
		return $this;
	}
	
	private function _assign( $name, $value ) 
	{
		if( is_object( $value ) && $this->_addWidget( $value, $name ) != false ) 
		{
			return $this;
		}
		return $this->_set( $name, $value );
	}
	
	private function _compact( $name, $value ) 
	{
		return  $this->_assign( $name, $value ); 
	}
	
	private function _share( $name, $value ) 
	{
		return  $this->_assign( $name, $value ); 
	}
	
	private function _setTitle( $value ) 
	{
		return $this->_assign( 'title', $value );
	}
	
	private function _renderLayout( $args = NULL ) 
	{
		global $configs, $html, $file, $_get, $_post;
		
		if( !is_null( $this->_tpl_name ) ) 
		{
			try 
			{
				if( !is_null( $this->_vars ) )
				{
					extract( $this->_vars );
				}
				
				if( !empty( $this->_widgets ) ) 
				{
					extract( $this->_widgets );
				}
				
				if( !is_null( $args ) && is_array( $args ) ) 
				{
					extract( $args );
				} 
				include( BLOCK_DIR . _correctPath( str_replace( '\Blocks', '', get_class( $this ) ) ) . DS . $this->_tpl_name );
			}
			catch(Exception $e) 
			{
				$block_name = get_class( $this );
				echo "{ $block_name is missed }";
			}
		}
		
		return $this;
	}
}