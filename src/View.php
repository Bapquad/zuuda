<?php
namespace Zuuda;

use Zuuda\Html; 
use Zuuda\Query;
use Zuuda\Request;
use Zuuda\Session;
use Zuuda\Cookie;
use Zuuda\Comsite;
use Zuuda\Config;

abstract class View implements iHTML, iTemplate, iLayout, iDeclare, iBlock 
{
	private $_vars = array();
	private $_blocks = array();
	
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
	
	private $_module;
	private $_controller;
	private $_action;
	private $_tpl_path;
	private $_tpl_id;
	private $_layout_json;
	private $_layout_header_path;
	private $_layout_footer_path;
	private $_layout_main_path;
	private $_layout_engine_path;

	private $_layout_header = false;
	private $_layout_footer = false;
	private $_layout_engine_vars;
	
	protected $html;
	protected $query;
	protected $request;
	protected $session;
	protected $cookie;
	protected $comsite;
	
	final protected function __getVars() { return $this->_vars; }
	final protected function __getBlocks() { return $this->_blocks; }
	final protected function __getHeadAssets() { return $this->_head_assets; }
	final protected function __getContentAssets() { return $this->_content_assets; } 
	final protected function __getModule() { return $this->_module; }
	final protected function __getController() { return $this->_controller; }
	final protected function __getAction() { return $this->_action; }
	final protected function __getTemplatePath() { return $this->_tpl_path; }
	final protected function __getHeaderLayoutPath() { return $this->_layout_header_path; }
	final protected function __getFooterLayoutPath() { return $this->_layout_footer_path; }
	final protected function __getMainLayoutPath() { return $this->_layout_main_path; }
	final protected function __getEngineLayoutPath() { return $this->_layout_engine_path; }
	final protected function __getLayoutHeader() { return $this->_layout_header; }
	final protected function __getLayoutFooter() { return $this->_layout_footer; }
	final protected function __getLayoutEngineVars() { return $this->_layout_engine_vars; }
	
	final protected function __setVars( $value ) { $this->_vars = $value; return $this; }
	final protected function __setBlocks( $value ) { $this->_blocks = $value; return $this; }
	final protected function __setHeadAssets( $value ) { $this->_head_assets = $value; return $this; }
	final protected function __addHeadAsset( $type, $value ) { array_push( $this->_head_assets[ $type ], $value ); return $this; }
	final protected function __setContentAssets( $value ) { $this->_content_assets = $value; return $this; }
	final protected function __addContentAsset( $type, $value ) { array_push( $this->_content_assets[ $type ], $value ); return $this; }
	final protected function __setModule( $value ) { $this->_module = $value; return $this; }
	final protected function __setController( $value ) { $this->_controller = $value; return $this; }
	final protected function __setAction( $value ) { $this->_action = $value; return $this; }
	final protected function __setTemplatePath( $value ) { $this->_tpl_path = $value; return $this; }
	final protected function __setHeaderLayoutPath( $value ) { $this->_layout_header_path = $value; return $this; }
	final protected function __setFooterLayoutPath( $value ) { $this->_layout_footer_path = $value; return $this; }
	final protected function __setMainLayoutPath( $value ) { $this->_layout_main_path = $value; return $this; }
	final protected function __setEngineLayoutPath( $value ) { $this->_layout_header = $value; return $this; }
	final protected function __setLayoutHeader( $value ) { $this->_layout_header = $value; return $this; }
	final protected function __setLayoutFooter( $value ) { $this->_layout_footer = $value; return $this; }
	final protected function __setLayoutEngineVars( $value ) { $this->_layout_engine_vars = $value; return $this; }
	
	final public function GetVars() { return $this->__getVars(); }
	final public function rootName() { return __CLASS__; }
	public function __construct() 
	{
		global $configs;
		
		$this->html = html::instance();
		$this->query = query::instance();
		$this->request = request::instance();
		$this->session = session::instance();
		$this->cookie = cookie::instance();
		$this->comsite = comsite::instance();
		
		$this->__setModule( $configs[ 'MODULE' ] );
		$this->__setController( $configs[ 'CONTROLLER' ] );
		$this->__setAction( [ 'ACTION' ] );
		$this->__setHeaderLayoutPath( 'header.tpl' );
		$this->__setFooterLayoutPath( 'footer.tpl' );
		
		$this->_tpl_id = cache::loadTemplateId();
	}
	
	final public function IncludeAsset( $assets ) { return $this->__includeAsset( $assets ); }
	final public function PreloadAsset() { return $this->__preloadAsset(); }
	final public function CustomAsset() { return $this->__customAsset(); }
	final public function HeadAsset( $type, $value ) { return $this->__headAsset( $type, $value ); }
	final public function HeadHtml( $value ) { return $this->__headHtml( $value ); }
	final public function HeadStyle( $value ) { return $this->__headStyle( $value ); }
	final public function HeadScript( $value ) { return $this->__headScript( $value ); }
	final public function PreloadMeta( $value ) { return $this->__preloadMeta( $value ); } 
	final public function PreloadHtml( $value ) { return $this->__preloadHtml( $value ); }
	final public function PreloadStyle( $value ) { return $this->__preloadStyle( $value ); }
	final public function PreloadScript( $value ) { return $this->__preloadScript( $value ); }
	final public function PreloadTag( $value ) { return $this->__preloadTag( $value ); }
	final public function PreloadCss( $value ) { return $this->__preloadCss( $value ); }
	final public function PreloadJs( $value ) { return $this->__preloadJs( $value ); }
	final public function RequireJs( $value ) { return $this->__preloadJs( $value ); }
	final public function ContentAsset( $type, $value ) { return $this->__contentAsset( $type, $value ); }
	final public function AddContentAsset( $type, $value ) { return $this->__addContentAsset( $type, $value ); }
	final public function ContentHtml( $value ) { return $this->__contentHtml( $value ); }
	final public function ContentStyle( $value ) { return $this->__contentStyle( $value ); }
	final public function ContentScript( $value ) { return $this->__contentScript( $value ); }
	final public function IncludeMeta( $value ) { return $this->__includeMeta( $value ); }
	final public function IncludeHtml( $value ) { return $this->__includeHtml( $value ); } 
	final public function IncludeStyle( $value ) { return $this->__includeStyle( $value ); }
	final public function IncludeScript( $value ) { return $this->__includeScript( $value ); }
	final public function IncludeTag( $value ) { return $this->__includeTag( $value ); }
	final public function IncludeCss( $value ) { return $this->__includeCss( $value ); }
	final public function IncludeJs( $value ) { return $this->__includeJs( $value ); } 

	final public function IncludeJui( $value ) { return $this->__includeJui( $value ); }
	final public function PreloadJui( $value ) { return $this->__preloadJui( $value ); }
	final public function RequireJui( $value ) { return $this->__requireJui( $value ); }
	
	final public function Assign() { return $this->__setVar( func_get_args(), func_num_args() ); } 
	final public function Set() { return $this->__setVar( func_get_args(), func_num_args() ); }
	final public function Share() { return $this->__setVar( func_get_args(), func_num_args() ); }
	final public function Get( $name ) { return $this->__getVar( $name ); }
	final public function AddBlock( $block, $force_name ) { return $this->__addBlock( $block, $force_name ); } 
	
	final public function SetHeaderLayout( $layout ) { return $this->__setHeaderLayout( $layout ); }
	final public function SetFooterLayout( $layout ) { return $this->__setFooterLayout( $layout ); }
	final public function SetMainLayout( $layout ) { return $this->__setMainLayout( $layout ); }
	final public function EngineLayout( $layout_content = NULL, $vars = NULL ) { return $this->__engineLayout( $layout_content, $vars ); }
	final public function RenderLayout( $vars = NULL ) { return $this->__renderLayout( $vars ); } 
	final public function RenderBlock( $block, $args = NULL ) { return $this->__renderBlock( $block, $args ); }
	final public function Include( $tplName ) { return $this->__include( $tplName ); }
	final public function SetTemplate( $tpl_path ) { return $this->__setTemplate( $tpl_path ); } 
	final public function SetLayout( $tpl_path, $type ) { return $this->__setLayout( $tpl_path, $type ); } 
	final public function LoadHeader() { return $this->__loadHeader(); }
	final public function LoadFooter() { return $this->__loadFooter(); }
	final public function LoadLayout() { return $this->__loadLayout(); }
	final public function Display( $blockName ) { return $this->__executeBlock( $blockName ); }
	final public function Equip( $blockName ) { return $this->__executeBlock( $blockName ); }
	final public function ExecuteBlock( $blockName, $methodName ) { return $this->__executeBlock( $blockName, $methodName ); }

	final public function JsonLayout( $data=NULL ) { $this->__jsonLayout( $data ); } 
	final public function DisplayJSON ( $data=NULL ) { $this->__renderJson(); }
	final public function PrintJSON( $data=NULL ) { $this->__renderJson(); }
	final public function OutputJSON( $data=NULL ) { $this->__renderJson(); } 
	final public function RenderJSON() { $this->__renderJson(); }
	final public function IsJson() { return $this->_layout_json; }

	final protected function DisplayAsCss() { return $this->__displayAsCss(); }
	final protected function DisplayAsJs() { return $this->__displayAsJs(); }
	final protected function DisplayAsJson() { return $this->__displayAsJson(); }
	final protected function DisplayAsText() { return $this->__displayAsText(); }
	final protected function DisplayAsStream( $name ) { return $this->__displayAsStream( $name ); }
	
	final protected function MakeStream( $name ) { return $this->__displayAsStream( $name ); }
	final protected function MakeAPIOut() { return $this->CORS(); }
	final protected function MakeAPI() { return $this->CORS(); }

	private function __executeBlock( $blockName, $methodName=NULL ) 
	{
		$methodName = ($methodName) ? $methodName : 'render';
		if( array_key_exists( $blockName, $this->_blocks ) ) 
		{
			call_user_func( array( $this->_blocks[ $blockName ], $methodName ) );
		}
		return $this;
	}

	private function __displayAsCss() 
	{
		ResponseHeader::DisplayCSS(); 
		return $this;
	} 

	private function __displayAsJs() 
	{
		ResponseHeader::DisplayJS();
		return $this;
	} 

	private function __displayAsJson() 
	{
		ResponseHeader::DisplayJSON();
		return $this;
	} 

	private function __displayAsText() 
	{
		ResponseHeader::DisplayText(); 
		return $this;
	} 

	private function __displayAsStream( $name ) 
	{
		ResponseHeader::Stream( $name );
		return $this;
	}
	
	private function __packageVars( $vars=NULL ) 
	{
		if( !is_null( $vars ) )
		{
			$this->_vars = array_merge( $this->_vars, $vars );
		}
	}

	private function __loadHeader() 
	{
		$this->__setLayoutHeader( true );
	}

	private function __loadFooter() 
	{
		$this->__setLayoutFooter( true );
	}

	private function __loadLayout() 
	{
		$this->__loadHeader();
		$this->__loadFooter();
	}

	private function __setTemplate( $tpl_path ) 
	{
		return $this->__setTemplatePath( $tpl_path );
	}
	
	private function __setLayout( $tpl_path, $type ) 
	{
		switch( $type ) 
		{
			case MAIN_LAYOUT: 
				$this->__setMainLayout( $tpl_path ); 
				break;
			case FOOTER_LAYOUT:
				$this->__setFooterLayout( $tpl_path );
				break;
			case HEADER_LAYOUT:
			default:
				$this->__setHeaderLayout( $tpl_path );
				break;
		}
	}
	
	private function __renderLayout( $args = NULL ) 
	{
		global $_configs, $_inflect, $_get, $_post, $_file, $_server; 
		$this->__packageVars( $args );
		extract( $this->_vars );
		extract( $this->_blocks );
		if( $this->_layout_engine_path && is_file($this->_layout_engine_path) )  
			include_once( $this->_layout_engine_path ); 
		if( $this->__finishHeaderPath() && $this->_layout_header && is_file($this->_layout_header_path) ) 
			include_once( $this->_layout_header_path ); 
		if( $this->__finishPath() && is_file($this->_tpl_path) ) 
			include_once( $this->_tpl_path ); 
		if( $this->__finishFooterPath() && $this->_layout_footer && is_file($this->_layout_footer_path) ) 
			include_once( $this->_layout_footer_path );
	}
	
	private function __include( $tplName ) 
	{
		extract( $this->_vars );
		extract( $this->_blocks );
		include( __assetPath(TPL_NAME_DIR . $tplName, true ) ); 
		return $this;
	}
	
	private function __renderBlock( $block, $args = NULL ) 
	{
		$blocks = $this->__getBlocks();
		
		if( in_array( $blocks, $block ) ) 
		{
			$blocks[ $block ]->render( $args );
		}
		
		return $this;
	}
	
	private function __setMainLayout( $layout ) 
	{
		return $this->__setMainLayoutPath( $layout );
	}
	
	private function __setFooterLayout( $layout ) 
	{
		$this->__setFooterLayoutPath( $layout );
		$this->__loadFooter();
	}
	
	private function __setHeaderLayout( $layout ) 
	{
		$this->__setHeaderLayoutPath( $layout );
		$this->__loadHeader();
	}
	
	private function __addBlock( $block, $force_name ) 
	{
		if( is_object( $block ) ) 
		{
			if( method_exists( $block, 'rootName' ) ) 
			{ 
				if( $block->rootName() == ZUUDA_SECTION_SYMBOL ) 
				{
					$blockName = ( is_null( $force_name ) ) ? $block->getName() : $force_name;
					if( !is_null( $blockName ) ) 
					{
						foreach( $block->__view__get_head_assets() as $type => $assets ) { $this->_head_assets[ $type ] = array_merge( $this->_head_assets[ $type ], $assets ); }
						foreach( $block->__view__get_content_assets() as $type => $assets ) { $this->_content_assets[ $type ] = array_merge( $this->_content_assets[ $type ], $assets ); }
						$block->__view__merge_vars( $this->_vars );
						return $this->_blocks[ $blockName ] = $block;
					}
				}
			}
		}
		return false;
	}
	
	private function __setVar( $args, $argsNum ) 
	{
		try 
		{
			if( 1==$argsNum ) 
			{
				$mixed = current($args); 
				$name = key($mixed); 
				$value = current($mixed); 
			} 
			else if( 1<$argsNum ) 
			{ 
				$name = $args[0]; 
				$value = $args[1];
			} 
			else 
			{ 
				throw new \Exception( "The functions of <b>View::Assign(), View::Set(), and View::Share()</b> must be has least one parameter." ); 
			} 
			
			if( is_object( $value ) ) 
			{
				if( method_exists( $value, 'rootName' ) ) 
				{
					$rootName = $value->rootName();
					if( $rootName == ZUUDA_SECTION_SYMBOL ) 
					{
						return;
					}
				}
			}
			$this->_vars[ $name ] = $value;
			return $this; 
		} 
		catch( \Exception $e ) 
		{ 
			abort( 500, $e->getMessage() );
		} 
	} 

	private function __getVar( $name ) 
	{
		if( is_string( $name ) ) 
		{
			if( array_key_exists( $name, $this->_vars ) ) 
			{
				return $this->_vars[ $name ];
			}
		}
		return NULL;
	}
	
	private function __includeJs( $value ) 
	{
		return $this->__includeScript( $value );
	} 
	
	private function __includeCss( $value ) 
	{
		return $this->__includeStyle( $value );
	}
	
	private function __includeTag( $value ) 
	{
		return $this->__includeHtml( $value );
	}
	
	private function __includeScript( $value ) 
	{
		return $this->__contentScript( $value );
	}
	
	private function __includeStyle( $value ) 
	{
		return $this->__contentStyle( $value );
	}
	
	private function __includeHtml( $value ) 
	{
		return $this->__contentHtml( $value );
	}
	
	private function __includeMeta( $value ) 
	{
		return $this->__contentHtml( $value );
	}
	
	private function __contentScript( $value ) 
	{
		return $this->__contentAsset( SCRIPT_ASSET, $value );
	}
	
	private function __contentStyle( $value ) 
	{
		return $this->__headAsset( STYLE_ASSET, $value );
	}
	
	private function __contentHtml( $value ) 
	{
		return $this->__contentAsset( HTML_ASSET, $value );
	}
	
	private function __contentAsset( $type, $value ) 
	{
		return $this->__addContentAsset( $type, $value );
	} 
	
	private function __preloadJs( $value ) 
	{
		return $this->__preloadScript( $value );
	}
	
	private function __preloadCss( $value ) 
	{
		return $this->__preloadStyle( $value );
	}
	
	private function __preloadTag( $value ) 
	{
		return $this->__preloadHtml( $value );
	}
	
	private function __preloadScript( $value ) 
	{
		return $this->__headScript( $value );
	}
	
	private function __preloadStyle( $value ) 
	{
		return $this->__headStyle( $value );
	}
	
	private function __preloadHtml( $value ) 
	{
		return $this->__headHtml( $value );
	}
	
	private function __preloadMeta( $value ) 
	{
		return $this->__headHtml( $value );
	}
	
	private function __headScript( $value ) 
	{
		return $this->__headAsset( SCRIPT_ASSET, $value );
	}
	
	private function __headStyle( $value ) 
	{
		return $this->__headAsset( STYLE_ASSET, $value );
	}
	
	private function __headHtml( $value ) 
	{
		return $this->__headAsset( HTML_ASSET, $value );
	}
	
	private function __headAsset( $type, $value )
	{
		return $this->__addHeadAsset( $type, $value );
	}
	
	private function __customAsset() 
	{
		return $this->__includeAsset( $this->__getContentAssets() );
	}
	
	private function __preloadAsset() 
	{
		return $this->__includeAsset( $this->__getHeadAssets() );
	}
	
	private function __includeAsset( $assets )
	{
		foreach( $assets[HTML_ASSET] as $html ) 
		{
			echo $html . NL;
		}
		
		foreach( $assets[STYLE_ASSET] as $href ) 
		{
			if( preg_match( '/(https)|(http):\/\//', $href ) ) 
				$css_path = $href;
			else 
				$css_path = getSingleton('Html')->assetPath( ((preg_match('/(jui)\//', $href))?PS:'/skin/css/').$href.'.css' ); 
			$css_path .= question.$this->_tpl_id;
$str = <<<EOD
		<link rel="stylesheet" type="text/css" href="$css_path" media="all">\n
EOD;
			echo $str;
		}
		
		foreach( $assets[ SCRIPT_ASSET ] as $src ) 
		{
			if( preg_match( '/(https)|(http):\/\//', $src ) ) 
				$js_path = $src; 
			else 
				$js_path = getSingleton( 'Html' )->assetPath( ((preg_match('/(jui)\//', $src))?PS:'/js/').$src.'.js' );
			$js_path .= question.$this->_tpl_id;
$str = <<<EOD
<script type="text/javascript" src="$js_path"></script>\n
EOD;
			echo $str;
		}
		
		return $this;
	}

	private function __requireJui( $value ) 
	{
		return $this->__preloadJui( $value );
	}
	
	private function __preloadJui( $value ) 
	{
		$this->__headAsset( STYLE_ASSET, 'jui/' . $value );
		$this->__headAsset( SCRIPT_ASSET, 'jui/' . $value );
		return $this;
	}

	private function __includeJui( $value ) 
	{
		$this->__headAsset( STYLE_ASSET, 'jui/' . $value );
		$this->__contentAsset( SCRIPT_ASSET, 'jui/' . $value );
		return $this;
	}

	private function __finishHeaderPath() 
	{
		$this->_layout_header_path = LAYOUT_DIR.$this->_layout_header_path;
		return file_exists( $this->_layout_header_path );
	}

	private function __finishPath($tpl_path=NULL) 
	{
		if(is_null($tpl_path)) 
		{
			$this->_tpl_path = TPL_DIR.$this->_tpl_path;
			return file_exists($this->_tpl_path);
		}
		else 
		{
			$_tpl_path = TPL_DIR.$tpl_path;
			if(file_exists($_tpl_path)) 
			{
				return $_tpl_path;
			}
			else 
			{
				return false;
			}
		}
	}

	private function __finishFooterPath() 
	{
		$this->_layout_footer_path = LAYOUT_DIR.$this->_layout_footer_path;
		return file_exists($this->_layout_footer_path);
	} 
	
	public function CORS() 
	{ 
		response::setcors();
		return $this;
	} 

	private function __jsonLayout( $data_json = NULL ) 
	{
		$data = array();
		if( NULL!==$data_json ) 
			$data = $data_json; 
		else if( NULL!==$this->_vars ) 
			$data = $this->_vars; 
		$this->_layout_json = json_encode($data); 
	} 
	
	private function __renderJson() 
	{
		ResponseHeader::DisplayJSON(); 
		print( $this->_layout_json );
	}
	
	private function __engineLayout( $layout_content = NULL, $vars = NULL ) 
	{
		$this->__packageVars( $vars );
		
		if( !is_null( $layout_content ) ) 
		{
			foreach($layout_content as $template) 
			{
				__assetPath(TPL_NAME_DIR.$template, true); 
			}
			$this->_layout_engine_vars = $layout_content;
			$layout_route = array(
				'/<!--@>(.*)<@-->/' => '<?php include( __assetPath( TPL_NAME_DIR . "\1", true ) ) ?>', 
				'/<!--%>(.*)<%-->/'	  => '<?php include( __assetPath( TPL_NAME_DIR . $this->_layout_engine_vars["\1"], true ) ) ?>', 
			);
		}
		else 
		{
			$layout_route = array(
				'/<!--@>(.*)<@-->/' => '<?php include( __assetPath( TPL_NAME_DIR . "\1", true ) ) ?>', 
				'/<!--%>(.*)<%-->/'	  => '<!-- \1 -->', 
			);
		}

		if( is_null( $this->_layout_main_path ) ) 
		{
			$file_name = __assetPath( TPL_LAYOUT_NAME_DIR.LAYOUT_MAIN, true ); 
			$cache_main_path = LAYOUT_MAIN;
		}
		else 
		{
			$file_name = __assetPath( TPL_NAME_DIR.$this->_layout_main_path, true );
			$cache_main_path = preg_replace( '/[\/\\\]/', '_', $this->_layout_main_path );
		}

		$cache_file_name = correct_path(APP_DIR.CACHE_LAYOUT_NAME_DIR.$cache_main_path);

		if( file_exists( $cache_file_name ) ) 
		{
			$this->_layout_engine_path = $cache_file_name;
		}
		else 
		{
			if( $layout_template = file_get_contents( $file_name ) ) 
			{
				foreach($layout_route as $pattern => $result)
					$layout_template = preg_replace($pattern, $result, $layout_template);
				
				$f = file_put_contents( $cache_file_name, $layout_template );
				
				if( $f ) 
				{
					$this->_layout_engine_path = $cache_file_name;
				}
			}
		}
		
		return $this;
	}
}