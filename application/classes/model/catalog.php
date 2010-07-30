<?php defined('SYSPATH') or die('No direct script access.');

class Model_Catalog extends Model
{
	
	public static function factory($id = FALSE)
	{
		return new Model_Catalog($id);
	}
	
	
	public function __construct($id = FALSE)
	{
		parent::__construct($id);
		
	}
	
	public function get()
	{
		if($this->id)
		{
			return DB::select()->from('catalogs')->where('catalog_id','=',$this->id)->execute()->current();
		}
		else
		{
			return $this->_get('catalogs');
		}
	}
	
	public function save()
	{
		
		if($this->id)
		{
			DB::update('catalogs')->set($this->data)->where('catalog_id','=',$this->id)->execute();
		}
		else
		{
			list($this->id, $rows) = DB::insert('catalogs', array_keys($this->data))->values(array_values($this->data))->execute();
		}
		
	}
	
	public function delete($id = FALSE)
	{
		$id = ($id) ? $id : $this->id;
		
		DB::delete('catalogs')->where('catalog_id', '=', $id)->execute();
		$children = DB::select()->from('catalogs')->where('catalog_parent_id','=',$id)->execute();
		
		$output[] = $id;
		foreach($children as $child)
		{
			$output = array_merge($output, $this->delete(TRUE, $child['catalog_id']));
		}
		
		return $output;
	}

	public static function all($id = 0)
	{
		$query = DB::select()->from('catalogs')->order_by('catalog_sort', 'ASC')->where('catalog_parent_id','=',$id);
		
		$result = $query->execute()->as_array();
		
		return $result;
	}

	public static function pick($id)
	{
		if(count((array)$id) <= 0) return array();
		
		$query = DB::select()->from('catalogs')->order_by('catalog_sort', 'ASC')->where('catalog_id','IN',(array)$id);
		
		$result = $query->execute();
		
		$output = array();
		foreach($result as $row)
		{
			$output[$row['catalog_id']] = $row;
		}
		
		return $output;
	}

	public static function hierarchy($id = 0)
	{
		$output = array();
		
		$query = DB::select()->from('catalogs')->order_by('catalog_sort', 'ASC')->where('catalog_parent_id','=',$id);
		
		$result = $query->execute()->as_array();
		
		foreach($result as $row)
		{
			$output[$row['catalog_id']] = $row;
			$output[$row['catalog_id']]['children'] = Model_Catalog::hierarchy($row['catalog_id']);
		}
		
		return $output;
	}
	
}