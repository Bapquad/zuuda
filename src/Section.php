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
	
	final protected function __getVars() { return $this->_vars; }
	final protected function __getWidgets() { return $this->_widgets; }
	/** private function _getName */
	final protected function __getTemplate() { return $this->_tpl_name; }
	final protected function __getHeadAssets() { return $this->_head_assets; }
	final protected function __getContentAssets() { return $this->_content_assets; }
	
	final protected function __setVars( $vars ) { $this->_vars = $vars; return $this; }
	final protected function __setVar( $name, $value ) { $this->_vars[ $name ] = $value; return $this; }
	final protected function __addVar( $name, $value ) { $this->_vars[ $name ] = $value; return $this; }
	final protected function __setName( $value ) { $this->_name = $value; return $this; }
	final protected function __setTemplate( $value ) { $this->_tpl_name = $value; return $this; }
	final protected function __setLayout( $value ) { $this->_tpl_name = $value; return $this; }
	final protected function __setHeadAssets( $value ) { $this->_head_assets = $value; return $this; }
	final protected function __setContentAssets( $value ) { $this->_content_assets = $value; return $this; }
	
	final public function GetVars() { return $this->__getVars(); }
	final public function SetVar( $name, $value ) { return $this->__setVar( $name, $value ); }
	final public function AddVar( $name, $value ) { return $this->__addVar( $name, $value ); }
	final public function __view__get_head_assets() { return $this->_head_assets; }
	final public function __view__get_content_assets() { return $this->_content_assets; }
	final public function __view__merge_vars( $vars ) { $this->_vars = array_merge( $vars, $this->_vars ); return $this; }

	final public function Share( $name, $value ) { return $this->__share( $name, $value ); }
	final public function Compact( $name, $value ) { return $this->__compact( $name, $value ); }
	final public function Assign( $name, $value ) { return $this->__assign( $name, $value ); }
	final public function Set( $name, $value ) { return $this->__setVar( $name, $value ); }
	final public function SetTitle( $value ) { return $this->__setTitle( $value ); }
	final public function GetName() { return $this->__getName(); }
	final public function SetName( $value ) { return $this->__setName( $value ); }
	final public function SetTemplate( $tpl_path ) { return $this->__setTemplate( $tpl_path ); }
	final public function SetLayout( $tpl_path ) { return $this->__setLayout( $tpl_path ); } 
	final public function AddWidget( $widget, $force_name = NULL ) { return $this->__addWidget( $widget, $force_name ); }
	final public function GetWidget( $name ) { return $this->__getWidget( $name ); }
	final public function Render( $template = NULL, $args = NULL ) { return $this->__render( $template, $args ); }
	
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
	
	private function __render( $template = NULL, $args = NULL ) 
	{
		if( !is_null( $template ) ) 
		{
			$this->__setTemplate( $template );
		}
		return $this->__renderLayout( $args );
	}
	
	private function __getWidget( $name ) 
	{
		$widgets = $this->__getWidgets();
		
		if( isset( $widgets[ $name ] ) ) 
		{
			return $widgets[ $name ];
		} 
		
		return NULL;
	}
	
	private function __addWidget( $widget, $force_name = NULL ) 
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
	
	private function __getName() 
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
	
	private function __setVar( $name, $value )
	{
		if( is_array( $name ) ) 
		{
			$vars  = $this->__getVars();
			$this->__setVars( array_merge( $vars, $name ) );
		} 
		else 
		{
			$this->__addVar( $name, $value );
		} 
		return $this;
	}
	
	private function __assign( $name, $value ) 
	{
		if( is_object( $value ) && $this->__addWidget( $value, $name ) != false ) 
		{
			return $this;
		}
		return $this->__setVar( $name, $value );
	}
	
	private function __compact( $name, $value ) 
	{
		return  $this->__assign( $name, $value ); 
	}
	
	private function __share( $name, $value ) 
	{
		return  $this->__assign( $name, $value ); 
	}
	
	private function __setTitle( $value ) 
	{
		return $this->__assign( 'title', $value );
	}
	
	private function __renderLayout( $args = NULL ) 
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
				include( BLOCK_DIR . __correctPath( str_replace( '\Blocks', '', get_class( $this ) ) ) . DS . $this->_tpl_name );
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