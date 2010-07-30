<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Orders extends Controller_Admin_Base {

	public function __construct($args)
	{
		parent::__construct($args);
		
		$this->menu['orders']['selected'] = TRUE;
	}
	
	public function action_index()
	{
		$this->template->body = 'Welcome to the orders section.';
	}

} // End Welcome
