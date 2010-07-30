<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Home extends Controller_Admin_Base {

	public function __construct($args)
	{
		parent::__construct($args);
		
		$this->menu['home']['selected'] = TRUE;
	}
	
	public function action_index()
	{
		$this->template->body = 'hello, world!';
	}

} // End Welcome
