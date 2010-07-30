<?php defined('SYSPATH') or die('No direct script access.');

class Model_Admin extends Model
{
	public $id = FALSE;
	public $name = '';
	
	public static function factory($id = FALSE)
	{
		return new Model_Admin($id);
	}
	
	public static function check()
	{
		$model = Session::instance()->get('admin', FALSE);
		if($model instanceof Model_Admin)
		{
			return $model;
		}
		
		return FALSE;
	}
	
	public function __construct($id = FALSE)
	{
		$this->id = $id;
	}
	
	
	


	public static function login($user, $pass)
	{
		$admin = DB::select('admin_id', 'admin_name')->from('admins')->where('admin_user','=',$user)->where('admin_pass','=',$pass)->execute();
		if(!$admin->valid()) return FALSE;
		
		$admin = $admin->current();
		
		$model = new Model_Admin($admin['admin_id']);
		$model->name = $admin['admin_name'];
		
		
		Session::instance()->set('admin', $model);
		
		return TRUE;
	}

	public static function logout()
	{
		Session::instance()->set('admin', FALSE);
	}
	
}