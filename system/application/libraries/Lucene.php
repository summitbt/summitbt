<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lucene {

	protected $CI;

	protected $_config = array(
		'lucene_index_base' => NULL,
		'lucene_normalize_content' => FALSE
	);

	protected $_index = NULL;

	protected $_doc = NULL;

	/**
	 * Constructor
	 *
	 * @param   array   $config
	 */
	public function __construct($config = array())
	{
		$this->CI =& get_instance();

		// Set config values
		if ( ! empty($config))
		{
			$this->initialize($config);
		}

		log_message('debug', 'Zend Lucene Class Initialized');

		// Index base directory not defined. Use general cache directory instead
		if ( ! $this->_config['lucene_index_base'])
		{
			$this->_config['lucene_index_base'] = APPPATH.'cache/';
		}

		// Index base directory not found or not writable
		if ( ! is_dir($this->_config['lucene_index_base']) OR ! is_really_writable($this->_config['lucene_index_base']))
		{
			show_error('Lucene indexing directory not found or not writable: '.$this->_config['lucene_index_base']);
		}

		// Add the Zend package path
		$this->CI->load->add_package_path(APPPATH.'third_party/Zend/');

		// Load the Zend library
		$this->CI->load->library('zend');

		// Load lucene libraries
		$this->CI->zend->load('Zend/Search/Lucene');
	}

	// --------------------------------------------------------------------

	/**
	 * Set config values
	 *
	 * @param   array   $config
	 * @return  Lucene
	 */
	public function initialize($config = array())
	{
		if ( ! empty($config))
		{
			foreach ($config as $key => $value)
			{
				if (array_key_exists($key, $this->_config))
				{
					$this->_config[$key] = $value;
				}
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Create an index
	 *
	 * @param   string  $dir
	 * @return  Lucene
	 */
	public function create($dir = '')
	{
		return $this->_begin_index($dir, TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * Open an index
	 *
	 * @param   string  $dir
	 * @return  Lucene
	 */
	public function open($dir)
	{
		return $this->_begin_index($dir, FALSE);
	}

	// --------------------------------------------------------------------

	/**
	 * Alias for field()
	 *
	 * @param   string  $type
     * @param   string  $name
     * @param   string  $value
     * @param   bool    $normalize
	 * @return  Lucene
	 */
	public function add_field($type = '', $name = '', $value = '', $normalize = NULL)
	{
		return $this->field($type, $name, $value, $normalize);
	}

	// --------------------------------------------------------------------

	/**
	 * Add a field to the document
	 *
	 * @param   string  $type
	 * @param   string  $name
	 * @param   string  $value
	 * @param   bool    $normalize
	 * @return  Lucene
	 */
	public function field($type = '', $name = '', $value = '', $normalize = NULL)
	{
		// Has the document been created?
		if ( ! $this->_doc)
		{
			$this->_doc = new Zend_Search_Lucene_Document();
		}

		// Field type
		$type = $this->_field_type($type);

		// Continue only if it is a value for field type and it has a key
		if ($type AND $name)
		{
			// Normalize content
			if ($normalize === TRUE OR ($normalize === NULL AND $this->_config['lucene_normalize_content'] === TRUE))
			{
				$value = $this->_normalize($value);
			}

			$this->_doc->addField(Zend_Search_Lucene_Field::$type($name, $value, 'utf-8'));
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Finalize the document
	 *
	 * @return  Lucene
	 */
	public function set_document()
	{
		// Only set the document if there is something in it
		if ($this->_doc)
		{
			// Set document
			$this->_index->addDocument($this->_doc);

			// Reset the document so more can be added
			$this->_doc = NULL;
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Alias for set_document()
	 *
	 * @return  Lucene
	 */
	public function document()
	{
		return $this->set_document();
	}

	// --------------------------------------------------------------------

	/**
	 * Commit the document to the index
	 *
	 * @param   bool    $optimize
	 * @return  Lucene
	 */
	public function commit($optimize = TRUE)
	{
		$this->_index->commit();

		if ($optimize)
		{
			return $this->optimize();
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Optimize the index
	 *
	 * @return  Lucene
	 */
	public function optimize()
	{
		$this->_index->optimize();

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Alias for find()
	 *
	 * @param   string  $query
	 * @param   bool    $parse
	 * @return  array|bool
	 */
	public function query($query = '', $parse = TRUE)
	{
		return $this->find($query, $parse = TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * Search the index
	 *
	 * @param   string  $query
	 * @param   bool    $parse
	 * @return  array|bool
	 */
	public function find($query = '', $parse = TRUE)
	{
		if ($query)
		{
			if ($parse)
			{
				$query = Zend_Search_Lucene_Search_QueryParser::parse($query);
			}

			return $this->_index->find($query);
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Remove items from the index
	 *
	 * @param   string  $key
	 * @param   string  $value
	 * @param   bool    $commit
	 * @param   bool    $optimize
	 * @return  Lucene
	 */
	public function remove($key = '', $value = '', $commit = FALSE, $optimize = TRUE)
	{
		// Only continue if there if a key and a value defined
		if ($key AND $value)
		{
			// Get all of the records with a certain key/value pair
			$hits = $this->find($key.':'.$value, FALSE);

			if ( $hits !== FALSE)
			{
				// Remove each result
				foreach ((array)$hits as $hit)
				{
					$this->_index->delete($hit->id);
				}

				// Commit changes
				if ($commit)
				{
					$this->commit($optimize);
				}
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the total number of documents in this index (including deleted documents)
	 *
	 * @return  int
	 */
	public function count()
	{
		return $this->_index->count();
	}

	// --------------------------------------------------------------------

	/**
	 * Returns one greater than the largest possible document number
	 *
	 * @return  int
	 */
	public function max_doc()
	{
		return $this->_index->maxDoc();
	}

	// --------------------------------------------------------------------

	/**
	 * Returns total number of non-deleted documents
	 *
	 * @return  int
	 */
	public function num_docs()
	{
		return $this->_index->numDocs();
	}

	// --------------------------------------------------------------------

	/**
	 * Document field type
	 *
	 * @param   string  $type
	 * @return  string
	 */
	private function _field_type($type = '')
	{
		/**
		 * UnStored
		 *      fields are tokenized and indexed, but not stored in the index. Large amounts of text are best indexed
		 *      using this type of field. Storing data creates a larger index on disk, so if you need to search but not
		 *      redisplay the data, use an UnStored field. UnStored fields are practical when using a Lucene index in
		 *      combination with a relational database. You can index large data fields with UnStored fields for
		 *      searching, and retrieve them from your relational database by using a separate field as an identifier.
		 *      The content in the node body is a good candidate for UnStored fields.
		 *
		 * Keyword
		 *      fields are stored and indexed, meaning that they can be searched as well as displayed in search
		 *      results. They are not split up into separate words by tokenization. Enumerated database fields usually
		 *      translate well to Keyword fields in Search Lucene API. Items like node IDs are best stored in keyword
		 *      fields.
		 *
		 * UnIndexed
		 *      fields are not searchable, but they are returned with search hits. Database timestamps, primary keys,
		 *      file system paths, and other external identifiers are good candidates for UnIndexed fields.
		 *
		 * Text
		 *      fields are stored, indexed, and tokenized. Text fields are appropriate for storing information like
		 *      subjects and titles that need to be searchable as well as returned with search results.
		 *
		 * Binary
		 *      fields are not tokenized or indexed, but are stored for retrieval with search hits. They can be used
		 *      to store any data encoded as a binary string, such as an image icon.
		 *
		 *
		 * Field Type   Stored  Indexed     Tokenized   Binary
		 * ----------   ------  -------     ---------   ------
		 * Keyword      Yes     Yes         No          No
		 * UnIndexed    Yes     No          No          No
		 * Binary       Yes     No          No          Yes
		 * Text         Yes     Yes         Yes         No
		 * UnStored     No      Yes         Yes         No
		 */
		switch (strtolower($type))
		{
			case 'keyword':
				return 'Keyword';
			break;
			case 'unindexed':
				return 'UnIndexed';
			break;
			case 'binary':
				return 'Binary';
			break;
			case 'text':
				return 'Text';
			break;
			case 'unstored':
				return 'UnStored';
			break;
			default:
				return FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Set default character code analyzer
	 *
	 * @param   string  $dir
	 * @param   bool    $create
	 * @return  Lucene
	 */
	private function _begin_index($dir = '', $create = FALSE)
	{
		// Set the index
		$this->_index = new Zend_Search_Lucene($this->_config['lucene_index_base'].$dir, $create);

		// Set default analyzer and case insensitive
		Zend_Search_Lucene_Analysis_Analyzer::setDefault(
			new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8_CaseInsensitive());

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Normalize data by replacing foreign characters
	 *
	 * @param   string  $input
	 * @return  string
	 */
	private function _normalize($input = '')
	{
		return strtr($input, array(
			'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
			'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
			'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
			'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
			'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
			'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
			'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
			'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r'
		));
	}

}