<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Clients extends Controller_Admin_Base {

	public function __construct($args)
	{
		parent::__construct($args);
		
		$this->menu['clients']['selected'] = TRUE;
	}
	
	public function action_index()
	{
		$this->template->body = View::factory('admin/clients/list');
		
		$this->template->body->clients = Model_Client::all();
	}
	
	public function action_add()
	{
		$this->action_edit(FALSE);
	}
	
	public function action_edit($id)
	{
		
		$client = Model_Client::factory($id);
		
		if($this->input->post())
		{
			$client->set('client_first_name', $this->input->post('client_first_name'));
			$client->set('client_last_name', $this->input->post('client_last_name'));
			
			$client->set('client_email', $this->input->post('client_email'));
			$client->set('client_phone', $this->input->post('client_phone'));
			$client->set('client_fax', $this->input->post('client_fax'));
			
			$client->set('client_enabled', $this->input->post('client_enabled'));
			
			$client->save();
			
			$this->request->redirect('admin/clients');
		}
		
		$info = $client->get();
		
		$form[] = F3::open();
		
		$form[] = '<div class="content-thin">';
		
		$form[] = F3::text('client_first_name')
						->label(FALSE)
						->cssParent(array('float'=>'left','width'=>'49%'))
						->css(array('font-size'=>'16px', 'font-weight'=>'bold', 'width'=>'95%'))
						->value($info['client_first_name']);
		$form[] = F3::text('client_last_name')
						->label(FALSE)
						->cssParent(array('float'=>'left','width'=>'49%'))
						->css(array('font-size'=>'16px', 'font-weight'=>'bold', 'width'=>'95%'))
						->value($info['client_last_name']);
		
		$form[] = '<div class="clear"></div>';

		$form[] = '<fieldset><legend>Contact Information</legend>';
		$form[] = F3::text('client_email')->label('Email')->value($info['client_email']);
		$form[] = F3::text('client_phone')->label('Telephone')->value($info['client_phone']);
		$form[] = F3::text('client_fax')->label('Fax')->value($info['client_fax']);
		
		
		//$form[] = F3::textarea('product_description')->label('Description')->value($info['product_description']);


		$form[] = F3::radio('client_enabled')->label("Account Status")
							->choice(1, 'Enabled')
							->choice(0, 'Disabled')
							->value($info['client_enabled']);
		$form[] = '</fieldset>';
		
		$form[] = '</div><div class="sidebar">';

		$form[] = '<div class="wrapper">';
		
			$form[] = '<fieldset>';
			$form[] = F3::submit('submit')->value(!$id ? 'Add Client' : 'Update Client')->label(FALSE)->cssParent(array('margin'=>'0 auto', 'width'=>'100px'))->css('margin', '0');
			$form[] = '</fieldset>';
					
		$form[] = '</div></div>';
		
		$form[] = F3::close();
		
		
		$this->template->body = implode("\n\n", $form);
	}
}
