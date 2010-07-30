<?php defined('SYSPATH') or die('No direct script access.');

class Model_Client extends Model
{
	
	public $addresses = array();
	
	public static function factory($id = FALSE)
	{
		return new Model_Client($id);
	}

	
	public function get()
	{
		if($this->id)
		{
			$output = DB::select()->from('clients')->where('client_id','=',$this->id)->execute()->current();
			$output['client_addresses'] = array();
			$addresses = DB::select()->from('client_addresses')->where('client_id','=',$this->id)->execute();
			foreach($addresses as $row)
			{
				$output['client_addresses'][] = $row;
			}
		}
		else
		{
			$output = $this->_get('clients');
			$output['client_addresses'] = array();
		}
		
		return $output;
	}
	
	public function address($id = FALSE)
	{
		return $this->addresses[] = new Model_Client_Address($id);
	}
	
	public function save()
	{
		
		if($this->id)
		{
			DB::update('clients')->set($this->data)->where('client_id','=',$this->id)->execute();
		}
		else
		{
			list($this->id, $rows) = DB::insert('clients', array_keys($this->data))->values(array_values($this->data))->execute();
		}
		
		
		/** MULTIPLE ADDRESSES **/
		

		foreach($this->addresses as $addr)
		{
			if($addr->delete)
			{
				if($addr->id)
				{
					DB::delete('client_addresses')->where('client_address_id','=',$addr->id)->execute();
				}
				continue;
			}
			
			$addr->set('client_id', $this->id);
			
			if($addr->id)
			{
				DB::update('client_addresses')->set($addr->data)->where('client_address_id','=',$addr->id)->execute();
			}
			else
			{
				list($addr->id, $rows) = DB::insert('client_addresses', array_keys($addr->data))->values(array_values($addr->data))->execute();
			}
		}
		
		
		/** END ADDRESSES **/
		
		
		return $this->id;
	}
	
	
	public function delete()
	{
		DB::delete('clients')->where('client_id', '=', $this->id);
		DB::delete('client_addresses')->where('client_id', '=', $this->id);
		
		return TRUE;
	}
	

	public static function all()
	{
	
		$query = DB::select()->from('clients')->order_by('client_last_name', 'ASC');
		
		$result = $query->execute()->as_array();
		
		return $result;
	}





	

}


class Model_Client_Address extends Model
{
	public $delete = FALSE;
}



