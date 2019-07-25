<?php
namespace Zuuda;

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
	private $_layout_header_path;
	private $_layout_footer_path;
	private $_layout_main_path;
	private $_layout_engine_path;

	private $_layout_header = false;
	private $_layout_footer = false;
	private $_layout_engine_vars;
	
	final protected function _getVars() { return $this->_vars; }
	final protected function _getBlocks() { return $this->_blocks; }
	final protected function _getHeadAssets() { return $this->_head_assets; }
	final protected function _getContentAssets() { return $this->_content_assets; } 
	final protected function _getModule() { return $this->_module; }
	final protected function _getController() { return $this->_controller; }
	final protected function _getAction() { return $this->_action; }
	final protected function _getTemplatePath() { return $this->_tpl_path; }
	final protected function _getHeaderLayoutPath() { return $this->_layout_header_path; }
	final protected function _getFooterLayoutPath() { return $this->_layout_footer_path; }
	final protected function _getMainLayoutPath() { return $this->_layout_main_path; }
	final protected function _getEngineLayoutPath() { return $this->_layout_engine_path; }
	final protected function _getLayoutHeader() { return $this->_layout_header; }
	final protected function _getLayoutFooter() { return $this->_layout_footer; }
	final protected function _getLayoutEngineVars() { return $this->_layout_engine_vars; }
	
	final protected function _setVars( $value ) { $this->_vars = $value; return $this; }
	final protected function _setBlocks( $value ) { $this->_blocks = $value; return $this; }
	final protected function _setHeadAssets( $value ) { $this->_head_assets = $value; return $this; }
	final protected function _addHeadAsset( $type, $value ) { array_push( $this->_head_assets[ $type ], $value ); return $this; }
	final protected function _setContentAssets( $value ) { $this->_content_assets = $value; return $this; }
	final protected function _addContentAsset( $type, $value ) { array_push( $this->_content_assets[ $type ], $value ); return $this; }
	final protected function _setModule( $value ) { $this->_module = $value; return $this; }
	final protected function _setController( $value ) { $this->_controller = $value; return $this; }
	final protected function _setAction( $value ) { $this->_action = $value; return $this; }
	final protected function _setTemplatePath( $value ) { $this->_tpl_path = $value; return $this; }
	final protected function _setHeaderLayoutPath( $value ) { $this->_layout_header_path = $value; return $this; }
	final protected function _setFooterLayoutPath( $value ) { $this->_layout_footer_path = $value; return $this; }
	final protected function _setMainLayoutPath( $value ) { $this->_layout_main_path = $value; return $this; }
	final protected function _setEngineLayoutPath( $value ) { $this->_layout_header = $value; return $this; }
	final protected function _setLayoutHeader( $value ) { $this->_layout_header = $value; return $this; }
	final protected function _setLayoutFooter( $value ) { $this->_layout_footer = $value; return $this; }
	final protected function _setLayoutEngineVars( $value ) { $this->_layout_engine_vars = $value; return $this; }
	
	final public function GetVars() { return $this->_getVars(); }

	final public function rootName() { return __CLASS__; }
	
	public function __construct() 
	{
		global $configs;
		$this->_setModule( $configs[ 'MODULE' ] );
		$this->_setController( $configs[ 'CONTROLLER' ] );
		$this->_setAction( [ 'ACTION' ] );
		$this->_setHeaderLayoutPath( 'header.tpl' );
		$this->_setFooterLayoutPath( 'footer.tpl' );
	}
	
	final public function IncludeAsset( $assets ) { return $this->_includeAsset( $assets ); }
	final public function PreloadAsset() { return $this->_preloadAsset(); }
	final public function CustomAsset() { return $this->_customAsset(); }
	final public function HeadAsset( $type, $value ) { return $this->_headAsset( $type, $value ); }
	final public function HeadHtml( $value ) { return $this->_headHtml( $value ); }
	final public function HeadStyle( $value ) { return $this->_headStyle( $value ); }
	final public function HeadScript( $value ) { return $this->_headScript( $value ); }
	final public function PreloadMeta( $value ) { return $this->_preloadMeta( $value ); } 
	final public function PreloadHtml( $value ) { return $this->_preloadHtml( $value ); }
	final public function PreloadStyle( $value ) { return $this->_preloadStyle( $value ); }
	final public function PreloadScript( $value ) { return $this->_preloadScript( $value ); }
	final public function PreloadTag( $value ) { return $this->_preloadTag( $value ); }
	final public function PreloadCss( $value ) { return $this->_preloadCss( $value ); }
	final public function PreloadJs( $value ) { return $this->_preloadJs( $value ); }
	final public function ContentAsset( $type, $value ) { return $this->_contentAsset( $type, $value ); }
	final public function AddContentAsset( $type, $value ) { return $this->_addContentAsset( $type, $value ); }
	final public function ContentHtml( $value ) { return $this->_contentHtml( $value ); }
	final public function ContentStyle( $value ) { return $this->_contentStyle( $value ); }
	final public function ContentScript( $value ) { return $this->_contentScript( $value ); }
	final public function IncludeMeta( $value ) { return $this->_includeMeta( $value ); }
	final public function IncludeHtml( $value ) { return $this->_includeHtml( $value ); } 
	final public function IncludeStyle( $value ) { return $this->_includeStyle( $value ); }
	final public function IncludeScript( $value ) { return $this->_includeScript( $value ); }
	final public function IncludeTag( $value ) { return $this->_includeTag( $value ); }
	final public function IncludeCss( $value ) { return $this->_includeCss( $value ); }
	final public function IncludeJs( $value ) { return $this->_includeJs( $value ); } 

	final public function IncludeJui( $value ) { return $this->_includeJui( $value ); }
	final public function PreloadJui( $value ) { return $this->_preloadJui( $value ); }
	final public function RequireJui( $value ) { return $this->_requireJui( $value ); }
	
	final public function Assign( $name, $value ) { return $this->_assign( $name, $value ); } 
	final public function Set( $name, $value ) { return $this->_set( $name, $value ); }
	final public function Get( $name ) { return $this->_get( $name ); }
	final public function AddBlock( $block, $force_name ) { return $this->_addBlock( $block, $force_name ); } 
	
	final public function SetHeaderLayout( $layout ) { return $this->_setHeaderLayout( $layout ); }
	final public function SetFooterLayout( $layout ) { return $this->_setFooterLayout( $layout ); }
	final public function SetMainLayout( $layout ) { return $this->_setMainLayout( $layout ); }
	final public function EngineLayout( $layout_content = NULL, $vars = NULL ) { return $this->_engineLayout( $layout_content, $vars ); }
	final public function RenderLayout( $vars = NULL ) { return $this->_renderLayout( $vars ); } 
	final public function RenderBlock( $block, $args = NULL ) { return $this->_renderBlock( $block, $args ); }
	final public function Include( $tplName ) { return $this->_include( $tplName ); }
	final public function SetTemplate( $tpl_path ) { return $this->_setTemplate( $tpl_path ); } 
	final public function SetLayout( $tpl_path, $type ) { return $this->_setLayout( $tpl_path, $type ); } 
	final public function LoadHeader() { return $this->_loadHeader(); }
	final public function LoadFooter() { return $this->_loadFooter(); }
	final public function LoadLayout() { return $this->_loadLayout(); }
	final public function Display( $blockName ) { return $this->_executeBlock( $blockName ); }
	final public function Equip( $blockName ) { return $this->_executeBlock( $blockName ); }
	final public function ExecuteBlock( $blockName, $methodName ) { return $this->_executeBlock( $blockName, $methodName ); }

	final public function JsonLayout( $data=NULL ) { $this->_jsonLayout( $data ); }
	final public function PrintJSON( $data=NULL ) { $this->_jsonLayout( $data ); }
	final public function OutputJSON( $data=NULL ) { $this->_jsonLayout( $data ); }
	final public function DisplayJSON ( $data=NULL ) { $this->_jsonLayout( $data ); }

	final protected function DisplayAsCss() { return $this->_displayAsCss(); }
	final protected function DisplayAsJs() { return $this->_displayAsJs(); }
	final protected function DisplayAsJson() { return $this->_displayAsJson(); }
	final protected function DisplayAsText() { return $this->_displayAsText(); }
	final protected function DisplayAsStream( $name ) { return $this->_displayAsStream( $name ); }
	
	final protected function MakeStream( $name ) { return $this->_displayAsStream( $name ); }
	final protected function MakeAPIOut() { return $this->CORS(); }
	final protected function MakeAPI() { return $this->CORS(); }

	private function _executeBlock( $blockName, $methodName=NULL ) 
	{
		$methodName = ($methodName) ? $methodName : 'render';
		if( array_key_exists( $blockName, $this->_blocks ) ) 
		{
			call_user_func( array( $this->_blocks[ $blockName ], $methodName ) );
		}
		return $this;
	}

	private function _displayAsCss() 
	{
		RequestHeader::DisplayCSS(); 
		return $this;
	} 

	private function _displayAsJs() 
	{
		RequestHeader::DisplayJS();
		return $this;
	} 

	private function _displayAsJson() 
	{
		RequestHeader::DisplayJSON();
		return $this;
	} 

	private function _displayAsText() 
	{
		RequestHeader::DisplayText(); 
		return $this;
	} 

	private function _displayAsStream( $name ) 
	{
		RequestHeader::Stream( $name );
		return $this;
	}
	
	private function _packageVars( $vars=NULL ) 
	{
		if( !is_null( $vars ) )
		{
			$this->_vars = array_merge( $this->_vars, $vars );
		}
	}

	private function _loadHeader() 
	{
		$this->_setLayoutHeader( true );
	}

	private function _loadFooter() 
	{
		$this->_setLayoutFooter( true );
	}

	private function _loadLayout() 
	{
		$this->_loadHeader();
		$this->_loadFooter();
	}

	private function _setTemplate( $tpl_path ) 
	{
		return $this->_setTemplatePath( $tpl_path );
	}
	
	private function _setLayout( $tpl_path, $type ) 
	{
		switch( $type ) 
		{
			case MAIN_LAYOUT: 
				$this->_setMainLayout( $tpl_path ); 
				break;
			case FOOTER_LAYOUT:
				$this->_setFooterLayout( $tpl_path );
				break;
			case HEADER_LAYOUT:
			default:
				$this->_setHeaderLayout( $tpl_path );
				break;
		}
	}
	
	private function _renderLayout( $args = NULL ) 
	{
		global $configs, $html, $file, $_get, $_post;
		
		$this->_packageVars( $args );
		extract( $this->_vars );
		extract( $this->_blocks );
		
		if( $this->_layout_engine_path ) 
		{
			include_once( $this->_layout_engine_path );
			exit;
		}
		if( $this->_finishHeaderPath() && $this->_layout_header )
		{
			include_once( $this->_layout_header_path );
		}
		if( $this->_finishPath() ) 
		{
			include_once( $this->_tpl_path );
		}
		if( $this->_finishFooterPath() && $this->_layout_footer ) 
		{
			include_once( $this->_layout_footer_path );
		}
	}
	
	private function _include( $tplName ) 
	{
		extract( $this->_vars );
		extract( $this->_blocks );
		include( _assetPath(TPL_NAME_DIR . $tplName, true ) ); 
		return $this;
	}
	
	private function _renderBlock( $block, $args = NULL ) 
	{
		$blocks = $this->_getBlocks();
		
		if( in_array( $blocks, $block ) ) 
		{
			$blocks[ $block ]->render( $args );
		}
		
		return $this;
	}
	
	private function _setMainLayout( $layout ) 
	{
		return $this->_setMainLayoutPath( $layout );
	}
	
	private function _setFooterLayout( $layout ) 
	{
		$this->_setFooterLayoutPath( $layout );
		$this->_loadFooter();
	}
	
	private function _setHeaderLayout( $layout ) 
	{
		$this->_setHeaderLayoutPath( $layout );
		$this->_loadHeader();
	}
	
	private function _addBlock( $block, $force_name ) 
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
	
	private function _set( $name, $value ) 
	{
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

	private function _get( $name ) 
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
	
	private function _assign( $name, $value )
	{
		return $this->_set( $name, $value );
	}
	
	private function _includeJs( $value ) 
	{
		return $this->_includeScript( $value );
	} 
	
	private function _includeCss( $value ) 
	{
		return $this->_includeStyle( $value );
	}
	
	private function _includeTag( $value ) 
	{
		return $this->_includeHtml( $value );
	}
	
	private function _includeScript( $value ) 
	{
		return $this->_contentScript( $value );
	}
	
	private function _includeStyle( $value ) 
	{
		return $this->_contentStyle( $value );
	}
	
	private function _includeHtml( $value ) 
	{
		return $this->_contentHtml( $value );
	}
	
	private function _includeMeta( $value ) 
	{
		return $this->_contentHtml( $value );
	}
	
	private function _contentScript( $value ) 
	{
		return $this->_contentAsset( SCRIPT_ASSET, $value );
	}
	
	private function _contentStyle( $value ) 
	{
		return $this->_headAsset( STYLE_ASSET, $value );
	}
	
	private function _contentHtml( $value ) 
	{
		return $this->_contentAsset( HTML_ASSET, $value );
	}
	
	private function _contentAsset( $type, $value ) 
	{
		return $this->_addContentAsset( $type, $value );
	} 
	
	private function _preloadJs( $value ) 
	{
		return $this->_preloadScript( $value );
	}
	
	private function _preloadCss( $value ) 
	{
		return $this->_preloadStyle( $value );
	}
	
	private function _preloadTag( $value ) 
	{
		return $this->_preloadHtml( $value );
	}
	
	private function _preloadScript( $value ) 
	{
		return $this->_headScript( $value );
	}
	
	private function _preloadStyle( $value ) 
	{
		return $this->_headStyle( $value );
	}
	
	private function _preloadHtml( $value ) 
	{
		return $this->_headHtml( $value );
	}
	
	private function _preloadMeta( $value ) 
	{
		return $this->_headHtml( $value );
	}
	
	private function _headScript( $value ) 
	{
		return $this->_headAsset( SCRIPT_ASSET, $value );
	}
	
	private function _headStyle( $value ) 
	{
		return $this->_headAsset( STYLE_ASSET, $value );
	}
	
	private function _headHtml( $value ) 
	{
		return $this->_headAsset( HTML_ASSET, $value );
	}
	
	private function _headAsset( $type, $value )
	{
		return $this->_addHeadAsset( $type, $value );
	}
	
	private function _customAsset() 
	{
		return $this->_includeAsset( $this->_getContentAssets() );
	}
	
	private function _preloadAsset() 
	{
		return $this->_includeAsset( $this->_getHeadAssets() );
	}
	
	private function _includeAsset( $assets )
	{
		foreach( $assets[ HTML_ASSET ] as $html ) 
		{
			echo $html . NL;
		}
		
		foreach( $assets[ STYLE_ASSET ] as $href ) 
		{
			if( preg_match( '/(https)|(http):\/\//', $href ) ) 
				$css_path = $href;
			else 
				$css_path = getSingleton( 'Html' )->assetPath( ((preg_match('/(jui)\//', $href))?'':'skin/css/').$href.'.css' ); 
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
				$js_path = getSingleton( 'Html' )->assetPath( ((preg_match('/(jui)\//', $src))?'':'js/').$src.'.js' );
$str = <<<EOD
<script type="text/javascript" src="$js_path"></script>\n
EOD;
			echo $str;
		}
		
		return $this;
	}

	private function _requireJui( $value ) 
	{
		return $this->_preloadJui( $value );
	}
	
	private function _preloadJui( $value ) 
	{
		$this->_headAsset( STYLE_ASSET, 'jui/' . $value );
		$this->_headAsset( SCRIPT_ASSET, 'jui/' . $value );
		return $this;
	}

	private function _includeJui( $value ) 
	{
		$this->_headAsset( STYLE_ASSET, 'jui/' . $value );
		$this->_contentAsset( SCRIPT_ASSET, 'jui/' . $value );
		return $this;
	}

	private function _finishHeaderPath() 
	{
		$this->_layout_header_path = LAYOUT_DIR.$this->_layout_header_path;
		return file_exists( $this->_layout_header_path );
	}

	private function _finishPath($tpl_path=NULL) 
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

	private function _finishFooterPath() 
	{
		$this->_layout_footer_path = LAYOUT_DIR.$this->_layout_footer_path;
		return file_exists($this->_layout_footer_path);
	} 
	
	protected function CORS() 
	{ 
		// Allow from any origin
		if (isset($_SERVER['HTTP_ORIGIN'])) {
			// Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
			// you want to allow, and if so:
			header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
			header('Access-Control-Allow-Credentials: true');
			header('Access-Control-Max-Age: 86400');    // cache for 1 day
		}

		// Access-Control headers are received during OPTIONS requests
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
				header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
				header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

			exit(0);
		}
	} 

	private function _jsonLayout( $data_json = NULL ) 
	{
		$data = array();
		
		if( NULL!==$data_json ) 
		{
			$data = $data_json;
		} 
		else if( NULL!==$this->_vars ) 
		{
			$data = $this->_vars;
		}

		RequestHeader::DisplayJSON();
		echo json_encode( $data );

		exit;
	}
	
	private function _engineLayout( $layout_content = NULL, $vars = NULL ) 
	{
		$this->_packageVars( $vars );
		
		if( !is_null( $layout_content ) ) 
		{
			foreach($layout_content as $template) 
			{
				_assetPath(TPL_NAME_DIR . $template, true); 
			}
			$this->_layout_engine_vars = $layout_content;
			$layout_route = array(
				'/<!--@>(.*)<@-->/' => '<?php include( _assetPath( TPL_NAME_DIR . "\1", true ) ) ?>', 
				'/<!--%>(.*)<%-->/'	  => '<?php include( _assetPath( TPL_NAME_DIR . $this->_layout_engine_vars["\1"], true ) ) ?>', 
			);
		}
		else 
		{
			$layout_route = array(
				'/<!--@>(.*)<@-->/' => '<?php include( _assetPath( TPL_NAME_DIR . "\1", true ) ) ?>', 
				'/<!--%>(.*)<%-->/'	  => '<!-- \1 -->', 
			);
		}

		if( is_null( $this->_layout_main_path ) ) 
		{
			$file_name = _assetPath( TPL_LAYOUT_NAME_DIR.LAYOUT_MAIN, true ); 
			$cache_main_path = LAYOUT_MAIN;
		}
		else 
		{
			$file_name = _assetPath( TPL_NAME_DIR.$this->_layout_main_path, true );
			$cache_main_path = preg_replace( '/[\/\\\]/', '_', $this->_layout_main_path );
		}

		$cache_file_name = _assetPath( CACHE_LAYOUT_NAME_DIR.$cache_main_path, true );

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