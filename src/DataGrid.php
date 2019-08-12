<?php
namespace Zuuda;

use Exception;
use Zuuda\Session;
use Zuuda\RequestHeader;
use Zuuda\Pagination;

abstract class DataGrid implements iHTML, iData, iDataGridv1_0, iSection, iTemplate 
{
	private $_vars = array();			// Holds the widget datas.
	private $_name;						// Holds the widget name.
	private $_title;					// Holds the title of page.
	private $_column_data = array();	// Holds the column details.
	private $_model;					// Holds the model of widget.
	private $_tpl_path;					// Holds the template path.
	private $_option_data;				// Holds the options controls.
	private $_length;					// Holds the number of rows of list.
	private $_total_pages;				// Holds the number of pages.
	private $_current_page;				// Holds the current page.
	private $_page_printer;				// Holds the url paging page.
	private $_default_num_of_rows = 3;	// Holds the default number of rows.
	private $_keyword;					// Holds the keyword of search pharse.
	private $_filter;					// Holds the filter mode.
	private $_sort;						// Holds the sort value.
	private $_order;					// Holds the current field order.
	private $_data_grid;
	private $_paging;
	
	protected function _getVars() { return $this->_vars; }
	protected function _getName() { return $this->_name; }
	protected function _getTitle() { return $this->_title; }
	protected function _getColumnData() { return $this->_column_data; }
	protected function _getModel() { return $this->_model; }
	protected function _getTemplate() { return $this->_tpl_path; }
	protected function _getOptionData() { return $this->_option_data; }
	protected function _getLength() { return $this->_length; }
	protected function _getTotalPages() { return $this->_total_pages; }
	protected function _getCurrentPage() { return $this->_current_page; }
	protected function _getPagePrinter() { return $this->_page_printer; }
	protected function _getDefaultNumOfRows() { return $this->_default_num_of_rows; }
	protected function _getKeyword() { return $this->_keyword; }
	protected function _getFilter() { return $this->_filter; }
	protected function _getSort() { return $this->_sort; }
	protected function _getOrder() { return $this->_order; }
	protected function _getData() { return $this->_data_grid; }
	protected function _getPaging() { return $this->_paging; }
	
	protected function _setVars( $name, $value ) { $this->_vars[ $name ] = $value; return $this; }
	protected function _setName( $value ) { $this->_name = $value; return $this; }
	protected function _setTitle( $value ) { $this->_title = $value; return $this; }
	protected function _setColumnData( $value ) { $this->_column_data = $value; return $this; }
	protected function _setModel( $value ) { $this->_model = $value; return $this; }
	protected function _setTemplate( $value ) { $this->_tpl_path = $value; return $this; }
	protected function _setOptionData( $value ) { $this->_option_data = $value; return $this; }
	protected function _setLength( $value ) { $this->_length = $value; return $this; }
	protected function _setTotalPages( $value ) { $this->_total_pages = $value; return $this; }
	protected function _setCurrentPage( $value ) { $this->_current_page = $value; return $this; }
	protected function _setPagePrinter( $value ) { $this->_page_printer = $value; return $this; }
	protected function _setDefaultNumOfRows( $value ) { $this->_default_num_of_rows = $value; return $this; }
	protected function _setKeyword( $value ) { $this->_keyword = $value; return $this; }
	protected function _setFilter( $value ) { $this->_filter = $value; return $this; }
	protected function _setSort( $value ) { $this->_sort = $value; return $this; }
	protected function _setOrder( $value ) { $this->_order = $value; return $this; }
	protected function _setData( $value ) { $this->_data_grid = $value; return $this->_data_grid; }
	protected function _setPaging( $value ) { $this->_paging = $value; return $this; }
	
	/** Implements Interface iData */
	public function SetModel( Model $model = NULL ) { return $this->_setModel( $model ); }
	public function GetVars() { return $this->_getVars(); }
	public function Assign( $name, $value ) { return $this->_assign( $name, $value ); }
	public function Set( $name, $value ) { return $this->_set( $name, $value ); }
	
	/** Implements Interface iDataGrid */
	public function AddColumn( DataColumn $column ) { return $this->_addColumn( $column ); }
	public function DeleteRow( $id ) { return $this->_deleteRow( $id ); }
	public function Length() { return $this->_getLength(); }
	public function SetLength( $value ) { return $this->_setLength( $value ); }
	public function TotalPages() { return $this->_getTotalPages(); }
	public function SetTotalPages( $value ) { return $this->_setTotalPages( $value ); }
	public function SetCurrentPage( $value ) { return $this->_setCurrentPage( $value ); }
	public function GetCurrentPage() { return $this->_getCurrentPage(); }
	public function SetPagePrinter( $printer ) { return $this->_setPagePrinter( $printer ); }
	public function GetPagePrinter() { return $this->_getPagePrinter(); }
	public function EnablePagePrint() { return $this->_enablePagePrint(); }
	public function DestroyPagePrint() { return $this->_destroyPagePrint(); }
	public function EnableSectionOption( $data ) { return $this->_enableSectionOption( $data ); }
	public function DestroySectionOption() { return $this->_destroySectionOption(); }
	public function EnableOption() { return $this->_enableOption(); }
	public function DestroyOption() { return $this->_destroyOption(); }
	public function EnableScrumb() { return $this->_enableScrumb(); }
	public function DestroyScrumb() { return $this->_destroyScrumb(); }
	public function GetNumberRows() { return $this->_getNumberRows(); }
	public function ChangeNumberRows( $value ) { return $this->_changeNumberRows( $value ); }
	public function SetRowsNumberList( $list ) { return $this->_setRowsNumberList( $list ); }
	public function DestroyRowsNumberList() { return $this->_destroyRowsNumberList(); }
	public function DefaultRowsNumberList() { return $this->_defaultRowsNumberList(); }
	public function SetDefaultNumOfRows( $value ) { return $this->_setDefaultNumOfRows( $value ); }
	public function ResetOptionData() { return $this->_resetOptionData(); }
	public function EnableKeyword( $field, $label, $opera ) { return $this->_enableKeyword( $field, $label, $opera ); }
	public function DestroyKeyword() { return $this->_destroyKeyWord(); }
	public function SetKeyword( $value ) { return $this->_setKeyword( $value ); }
	public function GetPaging() { return $this->_getPaging(); }
	public function SetPaging( $value = true ) { return $this->_setPaging( $value ); }
	public function EnablePaging() { return $this->_setPaging( true ); }
	
	public function SetChangeNumberRowsApi( $value ) { return $this->_setChangeNumberRowsApi( $value ); }
	public function SetScrumbEditApi( $value ) { return $this->_setScrumbEditApi( $value ); }
	public function SetScrumbDeleteApi( $value ) { return $this->_setScrumbDeleteApi( $value ); }
	public function SetResetOptionApi( $value ) { return $this->_setResetOptionApi( $value ); }
	
	/** Implements Interface iSection */
	public function SetTitle( $value ) { return $this->_setTitle( $value ); } 
	public function GetName() { return $this->_getName(); }
	public function SetName( $name ) { return $this->_setName( $name ); }
	public function SetTemplate( $template ) { return $this->_setTemplate( $template ); }
	public function SetLayout( $template ) { return $this->_setLayout( $template ); }
	public function Render( $template = NULL, $data = NULL ) { return $this->_render( $template, $data ); }
	
	public function rootName() { return __CLASS__; }
	
	public function __construct( Model $model = NULL ) 
	{		
		// Iniializing the option data.
		$option_data = Session::get( 'grid' );
		$curr_area = get_class( $this );
		
		// Reset the option data when it has not exist or it be in the other area.
		if( is_null( $option_data ) ) 
		{
			Session::register( 'grid' );	// Initialize a grid session variable.
			$this->_resetOptionData();		// Default the option data.
		}
		else 
		{
			if( $option_data[ 'curr_area' ] != $curr_area ) 
			{
				$this->_resetOptionData();	// Back default option data.
			}
			else 
			{
				$this->_setOptionData( $option_data );
			}
		}
		
		// Save the primary model.
		$this->_setModel( $model );
		
		// Default it not use the session option menu.
		$this->_enableOption();
		$this->_destroySectionOption();
	}
	
	protected function _set( $name, $value ) { return $this->_setVars( $name, $value ); }
	protected function _assign( $name, $value ) { return $this->_setVars( $name, $value ); }
	
	protected function buildData() { return $this->_buildData(); }
	
	protected function _buildData() 
	{
		global $configs;
		$model = $this->_getModel();
		$columns = $this->_buildColumns()->_getColumnData();
		$default_num_rows = $this->_getDefaultNumOfRows();
		$option_data = $this->_getOptionData();
		$num_of_rows = $this->_getNumberRows();
		$current_page = $this->_getCurrentPage();
		$filter = $this->_getFilter();
		$keyword = $this->_getKeyword();
		$printer = $this->_getPagePrinter();
		$extra_printer = '';
		$sort = $this->_getSort();
		$order = $this->_getOrder();
		
		// Field of select.
		foreach( $columns as $key => $column ) 
		{
			if( $column->getType() == COLLECTION_FIELDSET_TYPE ) 
			{
				$model->select( $column->getName() );
			}
		}
		
		if( !is_null( $filter ) && !is_null( $keyword ) ) 
		{
			$opera = strtolower( $filter[ 'opera' ] );
			$model->$opera( $filter[ 'field' ], $keyword );
			$extra_printer .= 'key={key}&';
		}
		
		if( !is_null( $order ) ) 
		{
			if( is_null( $sort ) ) 
			{
				$sort = 'asc';
				$this->_setSort( $sort );
			} 
			if( isset( $configs[ 'REQUEST_VARIABLES' ][ 'order' ] ) ) 
			{
				$extra_printer .= 'sort={sort}&';
				$extra_printer .= 'order={order}&';
			}
			$model->orderBy( $order, $sort );
		}

		// Building page printer.
		$printer .= ( ( $extra_printer != "" ) ? '?' : NULL) . substr( $extra_printer, 0, -1 );
		$this->_setPagePrinter( $printer );
		
		// Computing the total pages.
		$length = $model->length();
		$this->_setLength( $length );
		if( !is_null( $option_data ) ) 
		{
			$num_of_rows = $option_data[ 'num_of_rows' ];
		}
		else 
		{
			$num_of_rows = $default_num_rows;
		} 
		$model->setLimit( $num_of_rows );
		
		$total_pages = (int)ceil( $length/$num_of_rows );
		$this->_setTotalPages( $total_pages );
		
		// Check current page greater total pages.
		if( $current_page > $total_pages ) 
		{
			$current_page = ( $total_pages > 0 ) ? $total_pages : 1;
			$push_state = str_replace( '{page}', ( ( $current_page != 1 ) ? $current_page : NULL ), $printer );
			$push_state = str_replace( '{key}', $keyword, $push_state );
			$push_state = str_replace( '{sort}', $sort, $push_state );
			$push_state = str_replace( '{order}', $order, $push_state );
			$this->_assign( 'push_state', $push_state );
			$this->_setCurrentPage( $current_page );
		}
		
		if( $this->getPaging() === true ) 
		{
			$paging = new Pagination( $current_page, $length );
			// $paging->setPath( 'admin/stat/index/' );
			// $paging->setPath( '/?page={page}' );
			$paging->setPath( $printer );
			$paging->setRpp( $num_of_rows );
			$this->_assign( 'paging', $paging );
			$this->_setPaging( $paging );
		}

		// Final building the data
		return $this->_setData( $model->setPage( $current_page )->query() );
	}
	
	protected function _buildColumns() 
	{
		$this->buildColumns();
		return $this;
	}
	
	protected function _addColumn( DataColumn $column ) 
	{
		global $configs;
		
		$columns = $this->_getColumnData();
		array_push( $columns, $column );
		$this->_setColumnData( $columns );
		if( $column->hasSort() ) 
		{
			if( isset( $configs[ 'REQUEST_VARIABLES' ][ 'order' ] ) ) 
			{
				$order = $configs[ 'REQUEST_VARIABLES' ][ 'order' ];
				if( $column->getName() == $order ) 
				{
					$sort = ( !isset( $configs[ 'REQUEST_VARIABLES' ][ 'sort' ] ) ) ? 'asc' : $configs[ 'REQUEST_VARIABLES' ][ 'sort' ];
					$this->_setOrder( $order );
					$this->_setSort( $sort );
				}
			}
			else if( $column->getDefaultSort() != NULL ) 
			{
				$this->_setOrder( $column->getName() );
				$this->_setSort( $column->getDefaultSort() );
			}
		}
		
		return $this;
	}
	
	protected function _deleteRow( $id ) 
	{
		$this->_getModel()->setId( $id )->delete();
		return $this;
	}
	
	protected function _enablePagePrint() 
	{
		$this->_assign( 'use_pg_print', true );
		
		return $this;
	}
	
	protected function _destroyPagePrint() 
	{
		$this->_assign( 'use_pg_print', false );
		
		return $this;
	}
	
	protected function _enableSectionOption( $data ) 
	{
		$this->_assign( 'sod', $data );
		
		return $this;
	}
	
	protected function _destroySectionOption() 
	{
		$this->_assign( 'sod', NULL );
		
		return $this;
	}
	
	protected function _enableOption() 
	{
		$this->_enableScrumb();
		$this->_enableSelectedEdit();
		$this->_destroyKeyWord();
		$this->_defaultRowsNumberList();
		$this->_enablePagePrint();
		
		// Update option data.
		$this->_updateOptionData();
		
		$this->_assign( 'use_option', true );
		
		return $this;
	}
	
	protected function _destroyOption() 
	{
		$option_data = $this->_getOptionData();
		
		if( $option_data[ 'curr_area' ] == get_class( $this ) ) 
		{
			$this->_destroyOptionData();
		}
		
		return $this->_assign( 'use_option', false );
	}
	
	private function _destroyOptionData() 
	{
		Session::unregister( 'grid' );
		$this->_setOptionData( NULL );
		$this->_destroyScrumb();
		$this->_destroyPagePrint();
		$this->_destroyRowsNumberList();
		$this->_destroyKeyword();
		
		return $this;
	}
	
	private function _resetOptionData() 
	{
		$curr_area = get_class( $this );
		
		$this->_setOptionData( array( 
			'num_of_rows' 	=> $this->_getDefaultNumOfRows(),
			'curr_area'		=> $curr_area 
		));
		
		return $this->_updateOptionData();
	}
	
	private function _updateOptionData() 
	{
		$option_data = $this->_getOptionData();
		Session::set( 'grid', $option_data );
		if( !is_null( $option_data ) ) 
		{
			$this->_assign( 'num_of_rows', $option_data[ 'num_of_rows' ] );
		}
		
		return $this;
	}
	
	protected function _setRowsNumberList( $list = NULL, $new = true ) 
	{
		try 
		{
			if( !is_array( $list ) && !is_null( $list ) ) 
			{
				throw new Exception( 'SetRowsNumberList method has not array value.' );
			}
		}
		catch( Exception $e ) 
		{
			echo $e->getMessage();
		}
		
		if( is_array( $list ) || is_null( $list ) ) 
		{
			$this->_assign( 'number_row_list', $list );
			if( is_array( $list ) && $new ) 
			{
				$option_data = $this->_getOptionData();
				if( !in_array( $option_data[ 'num_of_rows' ], $list ) ) 
				{
					$this->_changeNumberRows( $list[ 0 ] );
				}
			}
			if( is_null( $list ) ) 
			{
				return $this->_changeNumberRows( $this->_getDefaultNumOfRows() );
			}
		}
		return $this;
	}
	
	protected function _defaultRowsNumberList() 
	{
		return $this->_setRowsNumberList( array( 3, 5, 8 ), false );
	}
	
	protected function _destroyRowsNumberList() 
	{
		$this->_assign( 'num_of_rows', NULL );
		return $this->_setRowsNumberList();
	}
	
	protected function _changeNumberRows( $value ) 
	{
		$option_data = $this->_getOptionData();
		if( !is_null( $option_data ) ) 
		{
			$option_data[ 'num_of_rows' ] = $value;
			$this->_setOptionData( $option_data );
			return $this->_updateOptionData();
		}
		return $this;
	}
	
	protected function _getNumberRows() 
	{
		$option_data = $this->_getOptionData();
		
		if( !is_null( $option_data ) && isset( $option_data[ 'num_of_rows' ] ) ) 
		{
			return (int) $option_data[ 'num_of_rows' ];
		}
		return $this->_getDefaultNumOfRows();
	}
	
	protected function _enableScrumb() { return $this->_assign( 'use_scrumb', true ); }
	protected function _destroyScrumb() { return $this->_assign( 'use_scrumb', false ); }
	protected function _enableSelectedEdit() { return $this->_assign( 'selected_edit', true ); }
	protected function _destroySelectedEdit() { return $this->_assign( 'selected_edit', false ); }
	
	protected function _enableKeyword( $field, $label, $opera ) 
	{
		global $configs;
		
		$filter = array( 
			'field' => $field, 
			'label' => $label, 
			'opera' => $opera, 
		);
		
		$this->_setFilter( $filter );
		
		if( isset( $configs[ 'REQUEST_VARIABLES' ][ 'key' ] ) ) 
		{
			$keyword =  $configs[ 'REQUEST_VARIABLES' ][ 'key' ];
			$this->_setKeyword( $keyword );
		}
		
		return $this->_assign( 'key_search', true );
	}
	
	protected function _destroyKeyword() 
	{
		$this->_setFilter( NULL );
		
		return $this->_assign( 'key_search', false ); 
	}
	
	protected function _setChangeNumberRowsApi( $value ) 
	{
		return $this->_assign( 'change_number_rows_api', $value );
	}
	
	protected function _setScrumbEditApi( $value ) 
	{
		return $this->_assign( 'scrumb_edit_api', $value );
	}
	
	protected function _setScrumbDeleteApi( $value ) 
	{
		return $this->_assign( 'scrumb_delete_api', $value );
	}
	
	protected function _setResetOptionApi( $value ) 
	{
		return $this->_assign( 'reset_option_api', $value );
	}
	
	protected function _setLayout( $template ) { return $this->_setTemplate( $template ); }
	protected function _render( $template, $data ) 
	{
		$data_grid = $this->_getData();
		$columns = $this->_getColumnData();
		$printer = $this->_getPagePrinter();
		$length = $this->_getLength();
		$current_page = $this->_getCurrentPage();
		$total_pages = $this->_getTotalPages();
		$title = $this->_getTitle();
		$name = $this->_getName();
		$filter = $this->_getFilter();
		$keyword = $this->_getKeyword();
		$sort = $this->_getSort();
		$order = $this->_getOrder();
			
		if( !is_null( $template ) ) 
		{
			$this->setTemplate( $template );
		}
		
		$this->_assign( 'printer', $printer );				// Distributes page printer datas.
		$this->_assign( 'length', $length );				// Distributes the length value.
		$this->_assign( 'current_page', $current_page );	// Distributes the curr page value.
		$this->_assign( 'total_pages', $total_pages );		// Distributes the total pages value.
		$this->_assign( 'data', $data_grid );				// Distributes the data of grid.
		$this->_assign( 'columns', $columns );				// Distributes the column which grid has.
		$this->_assign( 'title', $title );					// Distributes the title.
		$this->_assign( 'key_filter', $filter );			// Distributes the key search filter.
		$this->_assign( 'keyword', $keyword );				// Distributes the keyword.
		$this->_assign( 'name', $name );					// Distributes the name.
		$this->_assign( 'sort', $sort );					// Distributes the sort, default null.
		$this->_assign( 'order', $order );					// Distributes the order, default null, if any column has sort, and in current order request, it returns it's name value.
		return $this->_renderLayout( $data );
	}
	
	protected function _renderLayout( $args = NULL ) 
	{
		global $configs;
		global $html, $file;
		
		$vars = $this->_getVars();
		$template = $this->_getTemplate();
		
		if( !is_null( $vars ) ) 
		{
			extract( $vars );
		}
		
		if( !is_null( $args ) && is_array( $args ) ) 
		{
			extract( $args );
		}
		
		if( is_null( $template ) ) 
		{
			$path = WIDGET_DIR . _correctPath( str_replace( 'zuuda\\', '', strtolower( __CLASS__ ) ) ) . DS . 'template' . '.tpl'; 
		}
		else 
		{
			$path = WIDGET_DIR . $template;
		}
		
		include( $path );
		
		return $this;
	}
}