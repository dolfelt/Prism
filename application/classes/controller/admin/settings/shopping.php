<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Settings_Shopping extends Controller_Admin_Settings {

	public function __construct($args)
	{
		parent::__construct($args);
		
		$this->menu['settings']['children']['shopping']['selected'] = TRUE;

	}
	
	public function action_index()
	{
		
		$this->breadcrumb('Shopping', 'admin/settings/shopping/index');
		if($_POST)
		{
			
			// Listings
			Setting::set('shopping.products_per_page', $this->input->post('products_per_page'));
			
			// Inventory
			Setting::set('shopping.inventory_enabled', $this->input->post('inventory_enabled'));
			Setting::set('shopping.inventory_notifications', $this->input->post('inventory_notifications'));
			
			$this->message->add('Settings have been saved.');
			
			$this->request->redirect($this->request->uri);
			
		}
	
		$form[] = F3::open();
		
		$form[] = '<fieldset><legend>Listings</legend>';
		
		$form[] = F3::text('products_per_page')->css('width', '60px')->label('Products Per Page')->value(Setting::get('shopping.products_per_page'));


		$form[] = '</fieldset>';
		$form[] = '<fieldset><legend>Inventory</legend>';
		
		$form[] = F3::radio('inventory_enabled')->label("Enable Inventory Tracking")
							->choice(1, 'Enabled')
							->choice(0, 'Disabled')
							->value(Setting::get('shopping.inventory_enabled'));
		$form[] = F3::radio('inventory_notifications')->label("Inventory Notifications")
							->choice(1, 'Enabled')
							->choice(0, 'Disabled')
							->value(Setting::get('shopping.inventory_notifications'));

		$form[] = '</fieldset><fieldset>';
		$form[] = F3::submit('submit')->value('Save Settings')->label(FALSE)->css('margin', '0')->cssParent(array('margin'=>'0 auto', 'width'=>'100px'));
		$form[] = '</fieldset>';
		
		$this->template->body = implode("\n\n", $form);
	}

} // End Welcome
