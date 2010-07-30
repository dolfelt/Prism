<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Login extends Controller_Admin_Base {

	public function __construct($args)
	{
		parent::__construct($args);
		$this->require_login = FALSE;
		$this->panel = TRUE;
	}
	
	public function action_index()
	{
		
		if($_POST)
		{
			
			if(Model_Admin::login($_POST['username'], $_POST['password']))
			{
				$this->request->redirect('admin/home');
			}
			else
			{
				$this->message->add('The login credentials provided were incorrect. Please try again.', Message::ERROR);
			}
			
		}
		
		$this->template->page_title = "Login to Prism";
		$form = array();
		$form[] = F3::open(NULL, array(), 'Please Enter your Credentials'); //<form action="" method="post">';
		//$form[] = '<fieldset><legend>Please Enter your Credentials</legend>';
		
		$form[] = F3::text('username')->label('Username');
		$form[] = F3::password('password')->label('Password');

		$form[] = F3::submit('submit')->value('Login');
		
		$form[] = F3::close(); //'</fieldset>';
		//$form[] = '</form>';

		$this->template->body = implode("\n\n", $form);
	}
	
	public function action_logout()
	{
		Model_Admin::logout();
		
		$this->request->redirect('admin/login');
	}

} // End Welcome
