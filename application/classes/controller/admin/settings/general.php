<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Settings_General extends Controller_Admin_Settings {

	public function __construct($args)
	{
		parent::__construct($args);
		
		$this->menu['settings']['children']['general']['selected'] = TRUE;
		
	}
	
	public function action_index()
	{
		
		$this->breadcrumb('General', 'admin/settings/general/index');
		if($_POST)
		{
			
			Setting::set('general.store_name', $this->input->post('store_name'));
			Setting::set('general.store_email', $this->input->post('store_email'));
			
			$store_address = $this->input->post('store_address');
			Setting::set('general.store_address1', $store_address['address1']);
			Setting::set('general.store_address2', $store_address['address2']);
			Setting::set('general.store_city', $store_address['city']);
			Setting::set('general.store_state', $store_address['state']);
			Setting::set('general.store_postal', $store_address['postal']);
			
			$this->message->add('Settings have been saved.');
			
			$this->request->redirect($this->request->uri);
			
		}
	
		$form[] = F3::open();
		
		$form[] = '<fieldset><legend>Information</legend>';
		
		$form[] = F3::text('store_name')->label('Store Name')->value(Setting::get('general.store_name'));

		$form[] = F3::text('store_email')->label('Store Email')->value(Setting::get('general.store_email'));
		$form[] = F3::add('store_address', 'address')->label(array(
						'address1'	=> 'Store Address',
						'address2'	=> FALSE,
						'city'		=> 'Store City',
						'state'		=> 'Store State',
						'postal'	=> 'Store Postal Code',
					))->value(array(
						'address1'	=> Setting::get('general.store_address1'),
						'address2'	=> Setting::get('general.store_address2'),
						'city'		=> Setting::get('general.store_city'),
						'state'		=> Setting::get('general.store_state'),
						'postal'	=> Setting::get('general.store_postal'),
					));
		
		$form[] = '</fieldset>';


		$form[] = '<fieldset><legend>Email Settings</legend>';

		$form[] = F3::text('email_from')->label('From Address')->value(Setting::get('general.email_from'));
		$form[] = F3::text('email_server')->label('Server')->value(Setting::get('general.email_server'));
		$form[] = F3::text('email_port')->label('Port')->value(Setting::get('general.email_port'))->css(array('width'=>'80px'));

		$form[] = '</fieldset>';


		$form[] = '<fieldset>';
		$form[] = F3::submit('submit')->value('Save Settings')->label(FALSE)->css('margin', '0')->cssParent(array('margin'=>'0 auto', 'width'=>'100px'));
		$form[] = '</fieldset>';
		
		$this->template->body = implode("\n\n", $form);
	}

} // End Welcome
