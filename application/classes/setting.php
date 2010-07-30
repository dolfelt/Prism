<?php defined('SYSPATH') OR die('No direct access allowed.');

class Setting
{
	
	
	private static $settings = array();
	
	const INTEGER = 1;
	const STRING = 2;
	const FLOAT = 3;
	const TEXT = 4;
	const SERIALIZE = 5;
	const DATE = 6;
	
	private static $map = array(
		self::INTEGER 	=> 'setting_value_integer',
		self::STRING 	=> 'setting_value_string',
		self::FLOAT 	=> 'setting_value_float',
		self::TEXT 		=> 'setting_value_text',
		self::SERIALIZE => 'setting_value_text',
		self::DATE 		=> 'setting_value_date',
	);
	
	
	public static function get($name)
	{
		$parts = split("\.", $name);
		
		if(count($parts) != 2)
		{
			return self::group($name);
		}
		
		if(isset(self::$settings[$parts[0]]))
		{
			if(isset(self::$settings[$parts[0]][$parts[1]]))
				return self::$settings[$parts[0]][$parts[1]];
			else
				return FALSE;
		}
		
		$result = DB::select()->from('settings')
					->where('setting_group', '=', $parts[0])
					->where('setting_key', '=', $parts[1])
					->limit(1)
					->execute();
					
		if(!$result->valid())
		{
			return false;
		}
		
		$row = $result->current();

		$column = Arr::get(self::$map, $row['setting_type'], 'setting_value_string');
		
		$value = $row[$column];
		
		if($row['setting_type'] == self::SERIALIZE)
		{
			$value == unserialize($value);
		}
		
		if($row['setting_type'] == self::DATE)
		{
			$value == strtotime($value);
		}
		
		return $value;
	}
	
	public static function set($name, $value, $type = FALSE)
	{
		$parts = split("\.", $name);
		
		if(count($parts) != 2)
		{
			return false;
		}
		
		$result = DB::select()->from('settings')
					->where('setting_group', '=', $parts[0])
					->where('setting_key', '=', $parts[1])
					->limit(1)
					->execute();
		
		$row = $result->current();
		
		if($result->valid() && !$type)
			$type = $row['setting_type'];
		
		if(!$type)
		{
			if(is_numeric($value))
			{
				if(round($value) == $value)
				{
					$type = self::INTEGER;
				}
				else
					$type = self::FLOAT;
			}
			elseif(strlen($value) > 255)
			{
				$type = self::TEXT;
			}
			elseif(is_array($value))
			{
				$type = self::SERIALIZE;
			}
			else
			{
				$type = self::STRING;
			}
		}

		$column = Arr::get(self::$map, $type, 'setting_value_string');
		
		$data = array(
			'setting_group' => $parts[0],
			'setting_key'	=> $parts[1],
			'setting_type'	=> $type,
			$column			=> $value,
		);
		
		if(!$result->valid())
		{
			DB::insert('settings', array_keys($data))->values(array_values($data))->execute();
		}
		else
		{
			DB::update('settings')->set($data)->where('setting_id','=',$row['setting_id'])->execute();
		}
		
		return TRUE;

	}

	private static function group($group)
	{
		
		if(isset(self::$settings[$group])) return self::$settings[$group];
		
		$result = DB::select()->from('settings')->where('setting_group', '=', $group)->execute();
		
		if( !$result->valid())
		{
			return false;
		}
		else
		{
			
			$return_array = array();
			
			foreach($result as $row)
			{
				$value = false;
				
				$column = Arr::get(self::$map, $row->setting_type, 'setting_value_string');
				
				$value = $row[$column];
				
				$return_array[$row['setting_key']] = $value;
				
			}
			self::$settings[$group] = $return_array;
			
			return $return_array;
			
		}
		
		
	}

}
