<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Orm extends Kohana_ORM implements ArrayAccess
{
	protected $_form_fields = array();
	protected $_grid_columns = array();
	protected $_grid_options = array();

	private $_has_many_to_save = array();

	protected $_render_options = array();
	
	protected $_pdo; 
	
	public function PDO()
	{
		$host = Kohana::$config->load('database.default.connection.hostname');
		$dbname = Kohana::$config->load('database.default.connection.database');
		$name = Kohana::$config->load('database.default.connection.username');
		$password = Kohana::$config->load('database.default.connection.password');
		$encoding = Kohana::$config->load('database.default.charset');
	
		if ($this->_pdo === null) {
			$this->_pdo = new PDO("mysql:host={$host};dbname={$dbname}", $name, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$encoding}"));
        }
 
        return $this->_pdo;
	}
	
	public function query($sql, $fetchList = true, $fetchArray = false)
	{
		$database = self::PDO();

        $debugbar = Debug::StandardDebugBar();
        $debugbar["messages"]->addMessage($sql);

        if (!$database->query($sql)) {
            $debugbar["messages"]->addMessage($database->errorInfo());
            return array();
        }

		if ($fetchList) {
			$resultArray = $database->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		} else {
			$resultArray = $database->query($sql)->fetch(PDO::FETCH_ASSOC);
		}
		
		$countItems = $database->query('SELECT FOUND_ROWS()')->fetchColumn(); 

		if ($fetchArray) {
			return array(
				'items' => $resultArray,
				'total_items' => $countItems
			);
		}
		
		$class_name = get_class($this);

        $arrayObjects = array();

		if ($fetchList) {
			foreach ($resultArray as $item) {
				$class = new $class_name;
				$clone = clone $class;
				foreach ($item as $key => $value) {
					if (array_key_exists($key, $clone->as_array())) {
						$class->$key = $value;
					}
				}
				$arrayObjects[] = $class;
			}
			
			return array(
				'items' => $arrayObjects,
				'total_items' => $countItems
			);
		}

		$class = new $class_name;
		$clone = clone $class;
		foreach ($resultArray as $key => $value) {
			if (array_key_exists($key, $clone->as_array())) {
				$class->$key = $value;
			}
		}
		
		return $arrayObjects;
	}
	
	public function search(
			array $params, 
			$query,
			$limit = 10, 
			$offset = 0,
			$order = '', 
			$dest = '', 
			array $not_zero = array(), 
			array $not_null = array(),
			array $not_empty = array(),
			$as_array = false
		) {
		$class_name = get_class($this);
	
		$query = urldecode($query);
		$query = rawurldecode($query);
		$words = preg_split ('/\s+/' , $query) ;
		
		$queries = array();
		
		foreach ($words as $word) {
			$queries[] =  '%' . Morphy::forms($word) . '%';
		}

		$query_string = join(' ', $queries);
		
		$class = new $class_name;
	
		if (
			$order 
			&& $dest 
			&& in_array($dest, array('asc', 'desc', 'ASC', 'DESC')) 
			&& array_key_exists($order, $class->as_array())
		) {
			$order_by_str = $order ? $order . ' ' . $dest . ', ' : ' ';
		}

		$like = array();
		foreach ($params as $param) {
			$like[] = "{$param} LIKE '{$query_string}'";
		}
		
		$like = '(' . join(' OR ', $like) . ')';
		
		$additional_where = '';
		if ($not_zero) {
			foreach ($not_zero as $item) {
				$additional_where .= ' AND ' . $item . ' <> 0 ';
			}
		}
		if ($not_null) {
			foreach ($not_null as $item) {
				$additional_where .= ' AND ' . $item . ' IS NOT NULL ';
			}
		}
		if ($not_empty) {
			foreach ($not_empty as $item) {
				$additional_where .= ' AND TRIM(' . $item . ') <> "" ';
			}
		}
		
		$sql = "SELECT *, (case when {$like} then 1 else 0 end) as rank FROM {$this->_table_name} WHERE ACTIVE = 1 AND {$like}{$additional_where} ORDER BY {$order_by_str}rank DESC LIMIT {$offset},{$limit}";

        $debugbar = Debug::StandardDebugBar();

		$database = self::PDO();
		$PDO = $database->prepare($sql);
        $PDO->execute();

        $debugbar["messages"]->addMessage($PDO->queryString);
        $error = $PDO->errorInfo();
        if (Arr::get($error, 1)) {
            $debugbar["messages"]->addMessage($PDO->errorInfo());
        }

		$resultArray = $PDO->fetchAll(PDO::FETCH_ASSOC);

		$sql = "SELECT count(*) as count FROM {$this->_table_name} WHERE ACTIVE = 1 AND {$like}{$additional_where}";
		
		$database = self::PDO();
		$PDO = $database->prepare($sql);
        $PDO->execute();
		$count_all = intval($PDO->fetchColumn(0));

        $debugbar["messages"]->addMessage($PDO->queryString);
        $error = $PDO->errorInfo();
        if (Arr::get($error, 1)) {
            $debugbar["messages"]->addMessage($PDO->errorInfo());
        }

        if ($as_array) {
			return array(
				'result' => $resultArray,
				'count_all' => $count_all
			);
		}
		
		$arrayObjects = array();
		foreach ($resultArray as $item) {
			$class = new $class_name;
			foreach ($item as $key => $value) {
				if ($key != 'rank') {
					$class->$key = $value;
				}
			}
			$arrayObjects[] = $class;
		}
		return array(
				'result' => $arrayObjects,
				'count_all' => $count_all
			);
	}
	

	/**
	 * Updates all existing records
	 * Т.к. в 3.2.2 этой ф-ции нет, то приходится реализоовывать здесь.
	 *
	 * @chainable
	 * @return  ORM
	 */
	public function save_all()
	{
		$this->_build(Database::UPDATE);

		if (empty($this->_changed))
			return $this;

		$data = array();
		foreach ($this->_changed as $column)
		{
			// Compile changed data omitting ignored columns
			$data[$column] = $this->_object[$column];
		}

		if (is_array($this->_updated_column))
		{
			// Fill the updated column
			$column = $this->_updated_column['column'];
			$format = $this->_updated_column['format'];

			$data[$column] = $this->_object[$column] = ($format === TRUE) ? time() : date($format);
		}

		$this->_db_builder->set($data)->execute($this->_db);

		return $this;
	}

	/**
	 * Tests if a field exists in the database. This can be used as a
	 * Valdidation callback.
	 *
	 * @param   object    Validate object
	 * @param   string    Field
	 * @param   array     Array with errors
	 * @return  array     (Updated) array with errors
	 */
	public function field_available(Validate $array, $field)
	{
		if ($this->loaded() AND ! Arr::get($this->_changed, $field))
		{
			// This value is unchanged
			return TRUE;
		}

		if( ORM::factory($this->object_name())->where($field,'=',$array[$field])->find_all(1)->count() )
		{
			$array->error($field,'field_available');
		}
	}

	/**
	 * @return Form_ORM
	 */
	public function form()
	{
		throw new Exception('Form not implemented');
	}

	public function grid()
	{
		return Grid::factory(clone $this, $this->_grid_columns, $this->_grid_options);
	}

	public function __get($key)
	{
		if(method_exists($this, 'get_'.$key))
		{
			$getter = 'get_'.$key;
			return $this->$getter();
		}

		if(array_key_exists($key, $this->_has_many))
		{
			if(isset($this->_has_many_to_save[$key]))
			{
				return $this->_has_many_to_save[$key];
			}
		}
		return parent::__get($key);
	}

	public function __set($key, $value)
	{
		if(array_key_exists($key, $this->_has_many))
		{
			// @see Kohana_Validate#654
			/**
			 * Если для поля указан label, то валидатор пытается его найти и, конечно же
			 * если не нашел - указывает NULL, а нам нельзя это обрабатывать.
			 */

			try {
				$id = unserialize($value);
			}
			catch (Exception $e) {
				$id = $value;
			}

			if( ! is_null($value))
			{
				if( ! $value instanceof ORM )
				{
					if( ! empty($value))
					{
						if (is_array($id)) {
							$value = ORM::factory($this->_has_many[$key]['model'])
								->where('id', 'IN', $id);
						}
						else {
							$value = ORM::factory($this->_has_many[$key]['model'])
								->where('id', '=', $id);
						}
					}
					else
					{
						$value = NULL;
					}
				}

			}
			
			$this->_has_many_to_save[$key] = $value;
			
			return;
		}

		if(array_key_exists($key, $this->_belongs_to))
		{
			// та же проблема
			if( ! is_null($value))
			{
				parent::__set($key, $value);
			}
			return;
		}

		return parent::__set($key, $value);
	}

	public function save(Validation $validation = NULL)
	{
		parent::save($validation);

		foreach($this->_has_many_to_save as $key => $value) {
			$this->set_has_many_value($key, $value);
		}

		$this->_has_many_to_save = array();

		if (isset($this->url) && isset($this->name) && !$this->url && isset($this->md5_url)) {
			$url = Text::translit($this->name);
			$url = preg_replace ("/[^a-zA-ZА-Яа-я0-9\s]/"," ",$url);
			$url = trim($url);
			$url = preg_replace('%\s%', '-', $url);
			$url = preg_replace('%-+%', '-', $url);
			$url = mb_strtolower($url);
			$this->url = $url;
			$this->md5_url = md5($url);
			$this->save();
		}

		return $this;
	}

	protected function get_old_many_value($key)
	{
		return parent::__get($key);
	}

	protected function set_has_many_value($key, $value)
	{
		$ids = array();
		if( ! is_null($value))
		{
			$value = $value->find_all();
		}
		else
		{
			$value = array();
		}
		foreach($value as $obj)
		{
			$ids[] = $obj['id'];
		}

		$old_ids = array();
		// delete old objects
		foreach($this->get_old_many_value($key)->find_all() as $obj)
		{
			if(in_array($obj->id, $ids))
			{
				$old_ids[] = $obj->id;
			}
			else
			{
				$this->remove($key, $obj);
			}
		}

		foreach($value as $obj)
		{
			if( ! in_array($obj->id, $old_ids))
			{
				$this->add($key, $obj);
			}
		}
	}

	public function get_rendered($key)
	{
		$value = $this[$key];
		return $this->render_value($key, $value);
	}

	public function render_value($key, $value)
	{
		return Arr::path($this->_render_options, $key.'.'.$value, $value);
	}

	public function offsetGet($offset)
	{
		return $this->__get($offset);
	}

	public function offsetSet($offset, $value)
	{
		return $this->__set($offset, $value);
	}

	public function offsetExists($offset)
	{
		return $this->__isset($offset);
	}

	public function offsetUnset($offset)
	{
		return $this->__unset($offset);
	}

	public function start_transaction()
	{
		$this->_db->query(0, 'BEGIN', FALSE);
	}

	public function commit()
	{
		$this->_db->query(0, 'COMMIT', FALSE);
	}

	public function rollback()
	{
		$this->_db->query(0, 'ROLLBACK', FALSE);
	}

	public function allow_edit()
	{
		return TRUE;
	}

	public function allow_delete()
	{
		return TRUE;
	}

	public function get_page_by_url($url = '')
	{
		return $this->where('url', '=', $url)
			->find();
	}

	public function fetchCountByModelId($id, $item_name)
	{
		return $this->where('id', '=', $id)->find()->$item_name->where('active', '=', 1)->count_all();
	}

	public function fetchPageByParentModelId($id, $limit, $offset = 1, $item_name, $order = '', $dest = '')
	{
		$order_name = 'id';
		$order_dest = 'desc';
		
		if (array_key_exists($order, $this->$item_name->as_array()) && $order && $dest) {
			$order_name = $item_name . '.' . $order;
			$order_dest = $dest;
		}
		
		return $this->where('id', '=', $id)->where('active', '=', 1)->find()->$item_name->where('active', '=', 1)
			->limit($limit)->offset($offset)->order_by($order_name, $order_dest)->find_all();
	}

	public function fetchActive()
	{
		return $this->where('active', '=', 1)->order_by('id', 'ASC')->find_all();
	}
	
	public function fetchSorted($order, $dest)
	{
		return $this->where('active', '=', 1)->order_by($order, $dest)->find_all();
	}

	public function fetchByUrl($url)
	{
		return $this->where('active', '=', 1)->where('md5_url', '=', md5($url))->find();
	}

	public function getColumnByNumber($number)
	{
		$fields = array_keys($this->_grid_columns);

		return arr::get($fields, ($number - 1));
	}

	public function grid_fields()
	{
		return $this->_grid_columns;
	}

	public function grid_value($name)
	{
		$column = $this->_grid_columns[$name];

		if (!is_array($column)) {
			$column = array(
				'type' => $column
			);
		}

		$column_cfg = array(
			'name' => $name,
			'header' => Arr::get($this->labels(), $name),
			'params' => Arr::get($column, 'params', array())
		);

		$column = Arr::merge($column_cfg, (array) $column);
		$column = Grid_Column::factory($column);

		return $column->field($this);
	}

	public function sortable_fields()
	{
		return array();
	}

	public function getUnSortableSortableFields()
	{
		$sortableFields = $this->sortable_fields();

		$unSortableFields = array(0);

		foreach (array_keys($this->grid_fields()) as $index => $key) {
			if (!in_array($key, $sortableFields)) {
				$unSortableFields[] = $index + 1;
			}
		}

		return $unSortableFields;
	}
}