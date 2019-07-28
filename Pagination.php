<?php

namespace Zuuda;

use Exception;

class Pagination implements iHTML, iPagination 
{
	private $_classies = array( 'pg-paging', 'pagination', 'pagination-sm' );		// Holds the style class.
	private $_crumbs = '5';										// Holds
	private $_rpp = '10';										// Holds the number of rows per page.
	private $_key = 'page';										// Holds the mark of page symbol.
	private $_path = '';										// Holds the Url link.
	private $_next = 'Next &raquo;';							// Holds the markup next page.
	private $_previous = '&laquo; Previous';					// Holds the markup previous page.
	private $_alwaysShow = false;								// Holds the flag for showing pagination.
	private $_clean = false;									// ...
	private $_current;											// Holds the current page.
	private $_total;											// Holds the total page.
	
	protected function _getClassList() { return $this->_classies; }
	protected function _getCrumbs() { return $this->_crumbs; }
	protected function _getRpp() { return $this->_rpp; }
	protected function _getKey() { return $this->_key; }
	protected function _getPath() { global $url; return ( empty( $this->_path ) ) ? $url : $this->_path; }
	protected function _getNext() { return $this->_next; }
	protected function _getPrevious() { return $this->_previous; }
	protected function _getAlwaysShow() { return $this->_alwaysShow; }
	protected function _getClean() { return $this->_clean; }
	protected function _getCurrent() { return $this->_current; }
	protected function _getTotal() { return $this->_total; }
	
	protected function _addClasses( $values ) { $this->_classies = array_merge( $this->_classies, (array) $values ); return $this; }
	protected function _setClasses( $values ) { $this->_addClasses( $values ); return $this; }
	protected function _setCrumbs( $value ) { $this->_crumbs = $value; return $this; }
	protected function _setRpp( $value ) { $this->_rpp = (int) $value; return $this; }
	protected function _setKey( $value ) { $this->_key = $value; return $this; }
	protected function _setPath( $value ) { $this->_path = $value; return $this; }
	protected function _setNext( $value ) { $this->_next = $value; return $this; }
	protected function _setPrevious( $value ) { $this->_previous = $value; return $this; }
	protected function _setAlwaysShow( $value ) { $this->_alwaysShow = $value; return $this; }
	protected function _setClean( $value = true ) { $this->_clean = $value; return $this; }
	protected function _setFull() { $this->_setClean( false ); return $this; }
	protected function _setCurrent( $value ) { $this->_current = $value; return $this; }
	protected function _setTotal( $value ) { $this->_total = $value; return $this; }
	
	public function AddClasses( $values ) { return $this->_addClasses( $values ); }
	public function AlwaysShow() { return $this->_setAlwaysShow( true ); }
	public function GetCanonicalUrl() { return $this->_getCanonicalUrl(); }
	public function GetPageParam() { return $this->_getPageParam(); }
	public function GetPageUrl( $page = NULL ) { return $this->_getPageUrl( $page ); }
	public function GetRelPrevNextLinkTags() { return $this->_getRelPrevNextLinkTags(); }
	public function Parse() { return $this->_parse(); }
	public function SetClasses( $classes ) { return $this->_setClasses( $classes ); }
	public function SetClean() { return $this->_setClean(); }
	public function SetCrumbs( $crumbs ) { return $this->_setCrumbs( $crumbs ); }
	public function SetCurrent( $current ) { return $this->_setCurrent( $current ); }
	public function SetFull() { return $this->_setFull(); }
	public function SetKey( $key ) { return $this->_setKey( $key ); }
	public function SetNext( $next ) { return $this->_setNext( $next ); }
	public function SetPrevious( $previous ) { return $this->_setPrevious( $previous ); }
	public function SetRpp( $rpp ) { return $this->_setRpp( $rpp ); }
	public function SetPath( $path ) { return $this->_setPath( $path ); }
	public function SetTotal( $total ) { return $this->_setTotal( $total ); }
	
	public function __construct( $current = null, $total = null ) 
	{
		if( !is_null( $current ) ) 
		{
			$this->_setCurrent( $current );
		}
		
		if( !is_null( $total ) ) 
		{
			$this->_setTotal( $total );
		}
	}
	
	protected function _getCanonicalUrl() 
	{
		$url = $this->_getPath();
		$page = (int) $this->_getCurrent();
		$key = "{".$this->_getKey()."}";
		
		if( stripos( $url, $key ) !== false ) 
		{
			if( $page !== 1 ) 
				return ORIGIN_DOMAIN . str_replace( $key, $page, $url );
			return ORIGIN_DOMAIN . ( str_replace( $key, '', $url ) );
		}
		else 
		{
			if( $page !== 1 ) 
				return WEB_PATH . ( $url ) . $this->_getPageParam();
			return WEB_PATH . ( $url );
		}
	}
	
	protected function _getPageParam( $page = NULL ) 
	{
		$key = $this->_getKey();
		
		if( is_null( $page ) ) 
		{
			$page = (int) $this->_getCurrent();
		}
		
		return '?' . ( $key ) . '=' . ( (int) $page );
	}
	
	protected function _getPageUrl( $page = NULL ) 
	{
		global $configs;
		$url = $this->_getPath();
		$key = $this->_getKey();
		$params = ( isset( $configs[ 'REQUEST_VARIABLES' ] ) ) ? $configs[ 'REQUEST_VARIABLES' ] : array();
		
		if( stripos( $url, "{".$key."}") ) 
		{
			$key = "{".$key."}";
			foreach( $params as $name => $value ) 
				$url = str_replace( "{".$name."}", $value, $url );
			$href = $url;
			$href = ORIGIN_DOMAIN . $href;
		}
		else 
		{
			if( $page !== 1 ) 
			{
				$key = $params[ $key ] = "{".$key."}";
			}
			else 
			{
				$key = "{$key}={".$key."}";
			}
			$href = ( $url ) . '?' . urldecode( http_build_query( $params ) );
			$href = WEB_PATH . $href;
		}
		
		$href = preg_replace(
			array( '/=$/', '/=&/' ), 
			array( '', '&' ), 
			$href
		);
		
		if( !is_null( $page ) ) 
		{
			$href = str_replace( $key, ( ( $page !== 1 ) ? $page : '' ), $href );
		}
		
		return $href;
	}
	
	protected function _getRelPrevNextLinkTags() 
	{
		global $configs;
		$key = "{".$this->_getKey()."}";
		$page = (int) $this->_getCurrent();
		$pages = ( (int) ceil( $this->_getTotal() / $this->_getRpp() ) );
		
		$href = $this->_getPageUrl();
		
		if( $page === 1 ) 
		{
			if( $pages > 1 ) 
			{
				$href = str_replace( $key, 2, $href );
				return array( '<link rel="next" href="' . ( $href ) . '" />' );
			}
			return array();
		}
		
		$next_page_tags = array(
			'<link rel="prev" href="' . ( str_replace( $key, $page - 1, $href ) ) . '" />'
		);
		
		if( $pages > $page ) 
		{
			array_push(
				$next_page_tags, 
				'<link rel="next" href="' . ( str_replace( $key, $page + 1, $href ) ) . '" />'
			);
		}
		
		return $next_page_tags;
	}
	
	protected function _parse() 
	{
		global $configs;
		$this->_check();
		
		$classes = $this->_getClassList();
		$crumbs = $this->_getCrumbs();
		$rpp = $this->_getRpp();
		$key = $this->_getKey();
		$url = $this->_getPath();
		$next = $this->_getNext();
		$previous = $this->_getPrevious();
		$alwaysShow = $this->_getAlwaysShow();
		$clean = $this->_getClean();
		$current = $this->_getCurrent();
		$total = $this->_getTotal();
		
		ob_start();
		include( WIDGET_DIR . _correctPath( str_replace( 'zuuda\\', '', strtolower( __CLASS__ ) ) ) . PS . 'render.inc.php' );
		$_response = ob_get_contents();
		ob_end_clean();
		
		return $_response;
	}
	
	protected function _check() 
	{
		$current = $this->_getCurrent();
		$total = $this->_getTotal();
		
		try 
		{
			if( is_null( $current ) ) 
			{
				throw new Exception( 'Phân trang(' . get_class( $this ) . ')::Chưa cài đặt trang hiện tại.' );
			}
			
			if( is_null( $total ) ) 
			{
				throw new Exception( 'Phân trang(' . get_class( $this ) . ')::Chưa cài đặt trang cuối.' );
			}
		} 
		catch(Exception $e) 
		{
			echo $e->getMessage(); 
			var_dump($e->getTrace());
		}
	}
}