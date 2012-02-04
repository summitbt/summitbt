<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Script tag
 *
 * @param   string  $src
 * @param   string  $type
 * @param   bool    $index_page
 * @return  string
 */
if ( ! function_exists('script_tag'))
{
	function script_tag($src = '', $type = 'text/javascript', $index_page = FALSE)
	{
		$CI =& get_instance();

		$script = '<script';

		if (is_array($src))
		{
			foreach ($src as $k=>$v)
			{
				if ($k == 'src' AND strpos($v, '://') === FALSE)
				{
					if ($index_page === TRUE)
					{
						$script .= ' src="'.$CI->config->site_url($v).'"';
					}

					else
					{
						$script .= ' src="'.$CI->config->slash_item('base_url').$v.'"';
					}
				}

				else
				{
					$script .= " $k=\"$v\"";
				}
			}
		}

		else
		{
			if ( strpos($src, '://') !== FALSE)
			{
				$script .= ' src="'.$src.'"';
			}

			elseif ($index_page === TRUE)
			{
				$script .= ' src="'.$CI->config->site_url($src).'"';
			}

			else
			{
				$script .= ' src="'.$CI->config->slash_item('base_url').$src.'"';
			}

			$script .= ' type="'.$type.'"';
		}

		$script .= '></script>'."\n";

		return $script;
	}
}