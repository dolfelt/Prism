<?php defined('SYSPATH') or die('No direct script access.');

class Model_Product extends Model
{
	
	public $parents = array();
	public $attributes = array();
	
	public static $attribute_types = array(
		1 => array(
			'name'		=> 'Dropdown',
			'options'	=> TRUE,
		),
		2 => array(
			'name'		=> 'Radio',
			'options'	=> TRUE,
		),
		3 => array(
			'name'		=> 'Checkbox',
			'options'	=> TRUE,
		),
		4 => array(
			'name'		=> 'Text (One Line)',
			'options'	=> FALSE,
		),
		5 => array(
			'name'		=> 'Text (Multi Line)',
			'options'	=> FALSE,
		),
	);
	
	public static function factory($id = FALSE)
	{
		return new Model_Product($id);
	}
	
	
	public function __construct($id = FALSE)
	{
		parent::__construct($id);
		
	}
	
	public function get()
	{
		if($this->id)
		{
			$output = DB::select()->from('products')->where('product_id','=',$this->id)->execute()->current();
			$output['catalog_ids'] = array();
			$catalogs = DB::select()->from('catalog_products')->where('product_id','=',$this->id)->execute();
			foreach($catalogs as $row)
			{
				$output['catalog_ids'][] = $row['catalog_id'];
			}
		}
		else
		{
			$output = $this->_get('products');
			$output['catalog_ids'] = array();
		}
		
		return $output;
	}
	
	public function attributes()
	{
		$query = DB::select()->from('product_attributes')->where('product_id', '=', $this->id)
				->order_by('product_attribute_parent_id', 'ASC')->order_by('product_attribute_sort', 'ASC');
		
		$options = $query->execute()->as_array();
		$output = array();
		foreach($options as $opt)
		{
			if($opt['product_attribute_parent_id'] > 0)
			{
				$output[$opt['product_attribute_parent_id']]['options'][$opt['product_attribute_id']] = $opt;
			}
			else
			{
				$saved_options = array();
				if(isset($output[$opt['product_attribute_id']]['options']))
				{
					$saved_options = $output[$opt['product_attribute_id']]['options'];
				}
				$output[$opt['product_attribute_id']] = $opt;
				$output[$opt['product_attribute_id']]['options'] = $saved_options;
			}
		}
		
		return $output;
		
	}
	
	public function attribute($id = FALSE)
	{
		return $this->attributes[] = new Model_Product_Attribute($id);
	}
	
	public function parentid($id)
	{
		$this->parents[$id] = TRUE;
	}
	
	public function save()
	{
		
		if($this->id)
		{
			DB::update('products')->set($this->data)->where('product_id','=',$this->id)->execute();
		}
		else
		{
			list($this->id, $rows) = DB::insert('products', array_keys($this->data))->values(array_values($this->data))->execute();
		}
		
		
		/** CUSTOM ATTRIBUTES **/
		

		foreach($this->attributes as $attr)
		{
			if($attr->delete)
			{
				if($attr->id)
				{
					DB::delete('product_attributes')->where('product_attribute_id','=',$attr->id)->execute();
					DB::delete('product_attributes')->where('product_attribute_parent_id','=',$attr->id)->execute();
				}
				continue;
			}
			
			$attr->set('product_id', $this->id);
			
			if($attr->id)
			{
				DB::update('product_attributes')->set($attr->data)->where('product_attribute_id','=',$attr->id)->execute();
			}
			else
			{
				list($attr->id, $rows) = DB::insert('product_attributes', array_keys($attr->data))->values(array_values($attr->data))->execute();
			}
			foreach($attr->options as $opt)
			{
				if($opt->delete)
				{
					if($opt->id)
						DB::delete('product_attributes')->where('product_attribute_id','=',$opt->id)->execute();
					continue;
				}

				$opt->set('product_id', $this->id);
				$opt->set('product_attribute_parent_id', $attr->id);
				
				if($opt->id)
				{
					DB::update('product_attributes')->set($opt->data)->where('product_attribute_id','=',$opt->id)->execute();
				}
				else
				{
					list($opt->id, $rows) = DB::insert('product_attributes', array_keys($opt->data))->values(array_values($opt->data))->execute();
				}
			}
		}
		
		
		/** END ATTRIBUTES **/
		
		DB::delete('catalog_products')->where('product_id', '=', $this->id)->execute();
		foreach($this->parents as $catalog_id=>$na)
		{
			DB::insert('catalog_products', array('catalog_id', 'product_id'))->values(array($catalog_id, $this->id))->execute();
		}
		
		
		return $this->id;
	}
	
	
	public function delete()
	{
		DB::delete('products')->where('product_id', '=', $this->id);
		
		return TRUE;
	}
	

	public static function all($id = FALSE)
	{
	
		if($id)
		{
			$query = DB::select()->from(array('catalog_products', 'cp'))->order_by('product_sort', 'ASC')
						->join(array('products', 'p'), 'left')->on('cp.product_id','=','p.product_id')
						->where('catalog_id','=',$id);
		}
		else
		{
			$query = DB::select('*', array('GROUP_CONCAT("catalog_id")', 'catalogs'))->from(array('products', 'p'))
					->join(array('catalog_products', 'cp'), 'left')
					->on('cp.product_id','=','p.product_id')
					->order_by('product_name', 'ASC')
					->group_by('p.product_id');
		}
		$result = $query->execute();
		$output = array();
		$catalogs = array();
		foreach($result as $row)
		{
			if(isset($row['catalogs']))
			{
				$row['catalogs'] = (array)explode(",", $row['catalogs']);
				$catalogs = array_merge($catalogs, $row['catalogs']);
			}
			
			$output[$row['product_id']] = $row;
		}
		$catalogs = Model_Catalog::pick(array_unique($catalogs));
		if(count($catalogs) > 0)
		{
			foreach($output as &$row)
			{
				if(!isset($row['catalogs']) || !is_array($row['catalogs']))
					continue;
				
				$cats = array();
				foreach($row['catalogs'] as $id)
					if(isset($catalogs[$id]))
						$cats[$id] = $catalogs[$id];
				
				$row['catalogs'] = $cats;
			}
		}
		
		return $output;
	}


	public static function linked($id)
	{
		$query = DB::select()->from(array('catalog_products', 'cp'))->order_by('product_sort', 'ASC')
					->join(array('products', 'p'), 'left')->on('cp.product_id','=','p.product_id')
					->where('catalog_id','=',$id);
		$result = $query->execute()->as_array();
		
		return $result;
	}




	

}


class Model_Product_Attribute extends Model
{
	public $options = array();
	public $delete = FALSE;
	
	public function __construct($id = FALSE)
	{
		$this->id = $id;
	}
	
	public function option($id = FALSE)
	{
		return $this->options[] = new Model_Product_Attribute_Option($id);
	}
}

class Model_Product_Attribute_Option extends Model
{
	public $delete = FALSE;
	
	public function __construct($id = FALSE)
	{
		$this->id = $id;
	}

}



