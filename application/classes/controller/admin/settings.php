<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Settings extends Controller_Admin_Base {

	public function __construct($args)
	{
		parent::__construct($args);
		
		$this->menu['settings']['selected'] = TRUE;
		$this->menu['settings']['children'] = self::menu();
	}
	
	public static function menu()
	{
		return array(
			'general'		=> array('name'=>'General','title'=>'General','url'=>'admin/settings/general'),
			'shopping'		=> array('name'=>'Shopping','title'=>'Shopping','url'=>'admin/settings/shopping'),
			'maintenance'	=> array('name'=>'Maintenance','title'=>'Maintenance','url'=>'admin/settings/maintenance'),
			'payments'		=> array('name'=>'Payments','title'=>'Manage Payments','url'=>'admin/settings/payments'),
			'themes'		=> array('name'=>'Themes','title'=>'Themes','url'=>'admin/settings/themes'),
			'api'			=> array('name'=>'API','title'=>'API','url'=>'admin/settings/api'),
		);
	}

} // End Welcome
