<?php

class Fefiform
{
	
	public static $title = FALSE;
	
	public static function text($name) {
		return self::add($name, 'text');
	}
	public static function password($name) {
		return self::add($name, 'password');
	}
	public static function hidden($name) {
		return self::add($name, 'hidden');
	}
	public static function select($name) {
		return self::add($name, 'select');
	}
	public static function checkbox($name) {
		return self::add($name, 'checkbox');
	}
	public static function radio($name) {
		return self::add($name, 'radio');
	}
	public static function textarea($name) {
		return self::add($name, 'textarea');
	}
	
	public static function button($name) {
		return self::add($name, 'button');
	}
	public static function submit($name) {
		return self::add($name, 'submit');
	}
	
	
	public static function section($name, $class="") {
		return '<h2 class="'.$class.'"><b>'.$name.'</b></h2>';
	}
	
	
	public static function add($name, $type)
	{
		return Fefiform_Element::factory($name, $type);
	}

	public static function open($action = NULL, $attr = array(), $title = FALSE)
	{
		// Make sure that the method is always set
		empty($attr['method']) and $attr['method'] = 'post';

		if ($attr['method'] !== 'post' AND $attr['method'] !== 'get')
		{
			// If the method is invalid, use post
			$attr['method'] = 'post';
		}

		if ($action === NULL)
		{
			// Use the current URL as the default action
			$action = url::site(Request::instance()->uri());
		}
		elseif (strpos($action, '://') === FALSE)
		{
			// Make the action URI into a URL
			$action = url::site($action);
		}

		// Set action
		$attr['action'] = $action;
		$attr['class'] = "fefiform";

		
		// Form opening tag
		$form = '<form'.html::attributes($attr).'>'."\n";
		
		if($title)
		{
			self::$title = TRUE;
			$form .= '<fieldset><legend>'.$title.'</legend>';
		}
		else
		{
			self::$title = FALSE;
		}
		// Add hidden fields immediate after opening tag

		return $form;
	}


	public static function close($extra = '')
	{
		$output = '';
		if(self::$title === TRUE)
		{
			$output .= '</fieldset>';
		}
		return $output . '</form>'."\n".$extra;
	}




}
