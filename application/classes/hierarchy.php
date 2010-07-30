<?php defined('SYSPATH') or die('No direct script access.');

class Hierarchy
{


	
	public static function parseid($path, $return_full_path = TRUE)
	{
		
		$output = array(
			FALSE,
			array(),
		);
		if(!$path) 
			return array(
				FALSE,
				array(),
			);
		
		$split = explode('_', $path);
		if($return_full_path)
		{
			$id = end($split);
		}
		else
		{
			$id = array_pop($split);
		}
		
		return array(
			$id,
			$split,
		);
	}
	
	public static function buildid($path, $id = FALSE)
	{
		if($id)
		{
			if(is_array($path))
				array_push($path, $id);
			elseif(strlen($path) > 0)
				$path = array($path, $id);
			else
				$path = array($id);
		}
		
		return implode('_', $path);
		
	}

}