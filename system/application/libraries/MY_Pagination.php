<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Pagination Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Pagination
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/pagination.html
 */

class MY_Pagination extends CI_Pagination {

	var $base_url		    	= ''; // The page we are linking to
	var $prefix			    	= ''; // A custom prefix added to the path.
	var $suffix			    	= ''; // A custom suffix added to the path.

	var $total_rows		    	= ''; // Total number of items (database results)
	var $per_page		    	= 10; // Max number of items you want shown per page
	var $num_links		    	= 2;  // Number of "digit" links to show before/after the currently viewed page
	var $cur_page			    = 1;  // The current page being viewed

	var $first_link			    = '&laquo; First';
	var $first_tag_open		    = '';
	var $first_tag_close	    = '&nbsp;';

	var $prev_link			    = '&#8249;';
	var $prev_tag_open		    = '&nbsp;';
	var $prev_tag_close		    = '';

	var $next_link			    = '&#8250;';
	var $next_tag_open		    = '&nbsp;';
	var $next_tag_close		    = '&nbsp;';

	var $last_link			    = 'Last &raquo;';
	var $last_tag_open		    = '&nbsp;';
	var $last_tag_close		    = '';

	var $uri_segment		    = 3;
	var $full_tag_open		    = '';
	var $full_tag_close		    = '';

	var $first_url			    = ''; // Alternative URL for the First Page.

	var $cur_tag_open		    = '&nbsp;<strong>';
	var $cur_tag_close		    = '</strong>';

	var $num_tag_open		    = '&nbsp;';
	var $num_tag_close		    = '';

	var $page_query_string	    = FALSE;
	var $query_string_segment   = 'per_page';

	var $display_pages		    = TRUE;
	var $show_disabled			= TRUE;

	var $anchor_class		    = '';

	var $ellipsis_link			= '';
	var $ellipsis_tag_open		= '';
	var $ellipsis_tag_close		= '&nbsp;';
	var $ellipsis_inner 		= FALSE;

	var $page_count				= ''; // Something like Page %d of %d
	var $page_count_thousands	= ',';
	var $page_count_open		= '';
	var $page_count_close		= '&nbsp;';

	var $num_pages 	    	    = 0;

	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	array	initialization parameters
	 */
	public function __construct($params = array())
	{
		parent::__construct($params);

		if (count($params) > 0)
		{
			$this->initialize($params);
		}

		log_message('debug', "MY_Pagination Class Initialized");
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize Preferences
	 *
	 * @access	public
	 * @param	array	initialization parameters
	 * @return	void
	 */
	public function initialize($params = array())
	{
		if (count($params) > 0)
		{
			foreach ($params as $key => $val)
			{
				if (isset($this->$key))
				{
					$this->$key = $val;
				}
			}
		}
		// Implode the anchor_class into a string
		if (is_array($this->anchor_class))
		{
			$this->anchor_class = implode(' ', $this->anchor_class);
		}

		// Make string attribute
		$pattern = '/^class[\s]?=/i';
		if ( ! preg_match($pattern, $this->anchor_class, $matches))
		{
			$this->anchor_class = 'class="'.$this->anchor_class.'" ';
		}

	}

	// --------------------------------------------------------------------

	/**
	 * Generate the pagination links
	 *
	 * This is simply an alias for generate().
	 * create_links() is deprecated to keep in line with other classes. 
	 * $params attribute added for compatability purposes.
	 */
	public function create_links($params = array())
	{
		return $this->generate($params);
	}

	// --------------------------------------------------------------------

	/**
	 * Generate the pagination links
	 *
	 * @access	public
	 * @param	array	initialization parameters
	 * @return	string
	 */
	public function generate($params = array())
	{
		// Initialize params
		if ( ! empty($params))
		{
			$this->initialize($params);
		}

		// If our item count or per-page total is zero there is no need to continue.
		if ($this->total_rows == 0 OR $this->per_page == 0)
		{
			log_message('debug', 'Unable to build pagination. Either total_rows or per_page has not been passed.');
			return '';
		}

		// Calculate the total number of pages
		$this->num_pages = ceil($this->total_rows / $this->per_page);

		// Is there only one page? Hm... nothing more to do here then.
		if ($this->num_pages == 1)
		{
			return '';
		}

		// Determine the current page number.
		$CI =& get_instance();

		if ($CI->config->item('enable_query_strings') === TRUE OR $this->page_query_string === TRUE)
		{
			if ($CI->input->get($this->query_string_segment) != 0)
			{
				$this->cur_page = $CI->input->get($this->query_string_segment);

				// Prep the current page - no funny business!
				$this->cur_page = (int) $this->cur_page;
			}
		}
		else
		{
			if ($CI->uri->segment($this->uri_segment) != 0)
			{
				$this->cur_page = $CI->uri->segment($this->uri_segment);

				// Prep the current page - no funny business!
				$this->cur_page = (int) $this->cur_page;
			}
		}

		$this->num_links = (int)$this->num_links;

		if ($this->num_links < 1)
		{
			show_error('Your number of links must be a positive number.');
		}

		if ( ! is_numeric($this->cur_page))
		{
			$this->cur_page = 1;
		}

		// Is the page number before the first result page?
		// If so we show the first page
		if ($this->cur_page < 1)
		{
			$this->cur_page = 1;
		}

		// Is the page number beyond the result range?
		// If so we show the last page
		if ($this->cur_page > $this->num_pages)
		{
			$this->cur_page = $this->num_pages;
		}

		// Is pagination being used over GET or POST?  If get, add a per_page query
		// string. If post, add a trailing slash to the base URL if needed
		if ($CI->config->item('enable_query_strings') === TRUE OR $this->page_query_string === TRUE)
		{
			$this->base_url = rtrim($this->base_url).'&amp;'.$this->query_string_segment.'=';
		}
		else
		{
			$this->base_url = rtrim($this->base_url, '/') .'/';
		}

		// And here we go...
		$output = '';

		// Page X of Y
		if ($this->page_count)
		{
			$x = $this->page_count;
			$y = $this->num_pages;

			// Thousands separator
			if ($this->page_count_thousands)
			{
				$x = number_format( $this->cur_page, 0, '.', $this->page_count_thousands);
				$y = number_format( $this->num_pages, 0, '.', $this->page_count_thousands);
			}

			$count = sprintf($this->page_count, $x, $y);
			$output .= $this->page_count_open.$count.$this->page_count_close;
		}

		// Render the "First" link
		if ($this->first_link)
		{
			// We aren't on the last page
			if ($this->cur_page != 1)
			{
				$first_url = ($this->first_url == '') ? $this->base_url.$this->prefix.'1'.$this->suffix : $this->first_url;
				$output .= $this->first_tag_open.'<a '.$this->anchor_class.'href="'.$first_url.'">'.$this->first_link.'</a>'.$this->first_tag_close;
			}
			// We are on the first page and we are supposed to show the arrows
			elseif ($this->show_disabled === TRUE)
			{
				$output .= $this->first_tag_open.$this->first_link.$this->first_tag_close;
			}
		}

		// Render the "previous" link
		if ($this->prev_link)
		{
			$i = $this->cur_page - 1;

			// We aren't on the last page
			if ($this->cur_page != 1)
			{
				if ($i == 1 AND $this->first_url)
				{
					$output .= $this->prev_tag_open.'<a '.$this->anchor_class.'href="'.$this->first_url.'">'.$this->prev_link.'</a>'.$this->prev_tag_close;
				}
				else
				{

					$output .= $this->prev_tag_open.'<a '.$this->anchor_class.'href="'.$this->base_url.$this->prefix.$i.$this->suffix.'">'.$this->prev_link.'</a>'.$this->prev_tag_close;
				}
			}
			// We are on the first page and we are supposed to show the arrow
			elseif ($this->show_disabled === TRUE)
			{
				$output .= $this->prev_tag_open.$this->prev_link.$this->prev_tag_close;
			}
		}

		// Render the pages
		if ($this->display_pages !== FALSE)
		{
			$truncating = FALSE;
			$ellipsis_truncate = array();

			// Build an array of the numbered pages to display and not display
			if ( $this->ellipsis_link )
			{

				// Left side near the first/prev links
				if ($this->ellipsis_inner)
				{
					$i = 1;
					while ($i <= $this->num_links)
					{
						$ellipsis_truncate[] = $i;
						$i = $i + 1;
					}
				}
				// Sides of current
				$i = $this->cur_page - $this->num_links;
				while ($i <= $this->cur_page + $this->num_links)
				{
					$ellipsis_truncate[] = $i;
					$i = $i + 1;
				}
				// Right side near the next/last links
				if ($this->ellipsis_inner)
				{
					$i = $this->num_pages - $this->num_links + 1;
					while ($i <= $this->num_pages)
					{
						$ellipsis_truncate[] = $i;
						$i = $i + 1;
					}
				}
			}

			// Write the digit links
			for ($i = 1; $i <= $this->num_pages; $i++)
			{

				// Show ellipsis numbered links
				if ( $this->ellipsis_link )
				{

					if (array_search( $i, $ellipsis_truncate ) !== FALSE)
					{
						$output .= $this->item($i);
						$truncating = FALSE;

					}
					else
					{
						if ( $truncating === FALSE )
						{
							$output .= $this->ellipsis_tag_open.$this->ellipsis_link.$this->ellipsis_tag_close;
							$truncating = TRUE;
						}
					}

				}
				// Show general, cut down numbered links
				else
				{

					$output .= $this->item($i);

				}

			}
		}

		// Render the "next" link
		if ($this->next_link)
		{
			// We aren't on the last page
			if ($this->cur_page < $this->num_pages)
			{
				$output .= $this->next_tag_open.'<a '.$this->anchor_class.'href="'.$this->base_url.$this->prefix.($this->cur_page + 1).$this->suffix.'">'.$this->next_link.'</a>'.$this->next_tag_close;
			}
			// We are on the last page and we are supposed to show the arrow
			elseif ($this->show_disabled === TRUE)
			{
				$output .= $this->next_tag_open.$this->next_link.$this->next_tag_close;
			}
		}

		// Render the "Last" link
		if ($this->last_link)
		{
			// We aren't on the last page
			if ($this->cur_page < $this->num_pages)
			{
				$i = $this->num_pages;
				$output .= $this->last_tag_open.'<a '.$this->anchor_class.'href="'.$this->base_url.$this->prefix.$i.$this->suffix.'">'.$this->last_link.'</a>'.$this->last_tag_close;
			}
			// We are on the last page and we are supposed to show the arrows
			elseif ($this->show_disabled === TRUE)
			{
				$output .= $this->last_tag_open.$this->last_link.$this->last_tag_close;
			}
		}

		// Kill double slashes.  Note: Sometimes we can end up with a double slash
		// in the penultimate link so we'll kill all double slashes.
		$output = preg_replace("#([^:])//+#", "\\1/", $output);

		// Add the wrapper HTML if exists
		$output = $this->full_tag_open.$output.$this->full_tag_close;

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Individual pagination link
	 *
	 * @access	private
	 * @return	string
	 */
	private function item($i)
	{
		
		$output = '';
		
		// Current page
		if ($this->cur_page == $i)
		{
			$output .= $this->cur_tag_open.$i.$this->cur_tag_close; // Current page
		}
		// Not the current page
		else
		{

			if ($i == 1 AND $this->first_url != '')
			{
				$output .= $this->num_tag_open.'<a '.$this->anchor_class.'href="'.$this->first_url.'">'.$i.'</a>'.$this->num_tag_close;
			}
			else
			{
				$output .= $this->num_tag_open.'<a '.$this->anchor_class.'href="'.$this->base_url.$this->prefix.$i.$this->suffix.'">'.$i.'</a>'.$this->num_tag_close;
			}
		}

		return $output;
	}
	
}

// END Pagination Class

/* End of file Pagination.php */
/* Location: ./system/libraries/Pagination.php */