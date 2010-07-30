<?php defined('SYSPATH') or die('No direct script access.');

class Input
{
	public function __construct()
	{
		
	}
	
	public function get($key=FALSE, $default=NULL, $raw=FALSE)
	{
		if(!$key)
			return $_GET;
		
		if(isset($_GET[$key]))
		{
			if($raw)
				return $_GET[$key];
			else
				return $this->clean($_GET[$key]);
		}
		else
		{
			return $default;
		}
		
		
	}

	public function post($key=FALSE, $default=NULL, $raw=FALSE)
	{
		if(!$key)
			return $_POST;
		
		if(isset($_POST[$key]))
		{
			if($raw)
				return $_POST[$key];
			else
				return $this->clean($_POST[$key]);
		}
		else
		{
			return $default;
		}
		
	}
	
	private function clean($input)
	{
		$output = FALSE;
		if(is_array($input))
		{
			foreach($input as $key=>$value)
			{
				if(is_array($value))
					$output[$key] = $this->clean($value);
				else
					$output[$key] = Security::xss_clean($value);
			}
		}
		else
		{
			$output = Security::xss_clean($input);
		}
		
		return $output;
	}
	
}