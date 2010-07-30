<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Base extends Controller {

	/**
	 * @var  string  page template
	 */
	public $template = 'admin/template';
	
	public $require_login = TRUE;
	
	public $panel = FALSE;
	/**
	 * @var  boolean  auto render template
	 **/
	public $auto_render = TRUE;
	
	
	public $input;
	public $message;
	
	private $breadcrumbs = FALSE;
	
	/**
	 * @var array 
	 */
	public $menu = array(
		'home'			=> array(
				'name'		=> 'Dashboard',
				'title'		=> 'Recent Overview',
				'url'		=> 'admin/home',
			),
		'catalogs'		=> array(
				'name'		=> 'Catalogs',
				'title'		=> 'Manage Catalogs',
				'url'		=> 'admin/catalogs',
			),
		'clients'		=> array(
				'name'		=> 'Clients',
				'title'		=> 'Meet Your Clients',
				'url'		=> 'admin/clients',
			),
		'orders'		=> array(
				'name'		=> 'Orders',
				'title'		=> 'Cash Some Orders',
				'url'		=> 'admin/orders', 
				'count'		=> 4,
			),
		'promotions'			=> array(
				'name'		=> 'Promotions',
				'title'		=> 'Promotions',
				'url'		=> 'admin/promotions',
			),
		'settings'		=> array(
				'name'		=> 'Settings',
				'title'		=> 'My Settings',
				'url'		=> 'admin/settings',
			),
	);
	
	public function __construct($args)
	{
		parent::__construct($args);
		
		$this->input = new Input();
		$this->message = Message::instance('admin');
		
		Code::js_file('js/jquery.js');
		Code::js_file('js/jquery-ui.js');
	
		Code::css_file('css/reset.css');
		Code::css_file('css/admin/form.css');
		
		if(Request::$is_ajax)
		{
			$this->auto_render = FALSE;
		}
		
	}

	/**
	 * Loads the template View object.
	 *
	 * @return  void
	 */
	public function before()
	{
		$admin = FALSE;
		if($this->require_login && ($admin = Model_Admin::check())==FALSE)
		{
			$this->request->redirect('admin/login');
		}

		Code::css_file('css/admin/style.css');
		Code::css_file('css/admin/header.css');
		
		if ($this->auto_render === TRUE)
		{
			// Load the template
			$this->template = View::factory($this->template);
			
			$this->template->admin = $admin;
		}
	}

	/**
	 * Assigns the template as the request response.
	 *
	 * @param   string   request method
	 * @return  void
	 */
	public function after()
	{
		if ($this->auto_render === TRUE)
		{
			if($this->panel)
			{
				$this->template->_panel_view = TRUE;
				Code::css_file('css/admin/panel.css');
			}
		
			$this->template->menu = $this->menu;
			
			$this->template->breadcrumbs = $this->breadcrumbs;
			
			$this->template->messages = 
						$this->render_messages(Message::ERROR, 'message-error') . "\n\n" . 
						$this->render_messages(Message::INFO, 'message-info');
			
			$this->message->clear();
			
			$this->request->response = $this->template;
		}
	}
	
	
	public function breadcrumb($name, $url)
	{
		$this->breadcrumbs[$url] = $name;
	}
	
	
	private function render_messages($type, $class)
	{
		$msgs = $this->message->get($type);
		$messages = '';
		if(count($msgs) > 0)
		{
			$messages .= '<div class="'.$class.'"><ul>';
			foreach($msgs as $msg)
			{
				$messages .= '<li>'.$msg['message'].'</li>';
			}
			$messages .= '</ul></div>';
		}
		return $messages;
	}
	

}