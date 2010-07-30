<?php defined('SYSPATH') or die('No direct script access.');

class Code
{
	private static $js = array();
	private static $js_onload = array();
	
	private static $css = array();
	
	private static $js_files = array();
	private static $css_files = array();
	
	const JS = 'js';
	const JS_ONLOAD = 'js_onload';
	const CSS = 'css';
	
	const JS_FILES = 'js_files';
	const CSS_FILES = 'css_files';
	
	public static function js($code)
	{
		$hash  = sha1($code);
		
		if(!isset(self::$js[$hash]))
		{
			self::$js[$hash] = $code;
		}
	}
	
	public static function js_onload($code)
	{
		$hash  = sha1($code);
		
		if(!isset(self::$js_onload[$hash]))
		{
			self::$js_onload[$hash] = $code;
		}
	}
	
	public static function css($code)
	{
		$hash  = sha1($code);
		
		if(!isset(self::$css[$hash]))
		{
			self::$css[$hash] = $code;
		}
	}
	
	public static function js_file($path)
	{
		$hash  = sha1($path);
		
		if(!isset(self::$js_files[$hash]))
		{
			self::$js_files[$hash] = $path;
		}
	}
	
	public static function css_file($path)
	{
		$hash  = sha1($path);
		
		if(!isset(self::$css_files[$hash]))
		{
			self::$css_files[$hash] = $path;
		}
	}
	
	public static function get($type, $return_array = TRUE)
	{
		switch($type)
		{
			case self::JS:
				return ($return_array) ? self::$js : implode("\n\n\n", self::$js);
				break;
				
			case self::JS_ONLOAD:
				return ($return_array) ? self::$js_onload : implode("\n\n\n", self::$js_onload);
				break;
				
			case self::CSS:
				return ($return_array) ? self::$css : implode("\n\n\n", self::$css);
				break;
				
			case self::JS_FILES:
				return ($return_array) ? self::$js_files : implode("\n\n\n", self::$js_files);
				break;
				
			case self::CSS_FILES:
				return ($return_array) ? self::$css_files : implode("\n\n\n", self::$css_files);
				break;
				
			
		}
	}

}