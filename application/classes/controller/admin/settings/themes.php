<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Settings_Themes extends Controller_Admin_Settings {

	public function __construct($args)
	{
		parent::__construct($args);
		
		$this->menu['settings']['children']['themes']['selected'] = TRUE;

	}
	
	public function action_index()
	{
		
		print_r(Themes::files());
		
		$form[] = '';
		$this->template->body = implode("\n\n", $form);
	}

} // End Welcome
