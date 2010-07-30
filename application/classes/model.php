<?php defined('SYSPATH') or die('No direct script access.');

class Model
{
	public $db = 'default';
	
	public $id = FALSE;
	
	public $data = array();
	
	public function __construct($id = FALSE)
	{
		$this->id = $id;
	}
	
	public function set($key, $value=FALSE)
	{
		if(is_array($key))
		{
			$this->data = array_merge($this->data, $key);
		}
		else
		{
			$this->data[$key] = $value;
		}
		return $this;
	}
	
	
	
	
	public static function _get($table)
	{
		$columns = Database::instance()->list_columns($table);
		$output = array();
		foreach($columns as $col)
		{
			$output[$col['column_name']] = $col['column_default'];
		}
		return $output;
	}

}