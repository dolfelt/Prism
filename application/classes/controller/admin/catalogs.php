<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Catalogs extends Controller_Admin_Base {

	public function __construct($args)
	{
		parent::__construct($args);
		
		$this->menu['catalogs']['selected'] = TRUE;
		$this->menu['catalogs']['children'] = self::menu();
	}
	
	public static function menu()
	{
		return array(
			'catalogs'	=> array('name'=>'Catalogs','title'=>'Manage Catalogs','url'=>'admin/catalogs/list','top'=>TRUE),
			'all'		=> array('name'=>'All Products','title'=>'All Products','url'=>'admin/products/list','top'=>TRUE),
		);
	}

	public function action_index()
	{
		$this->action_list();
	}
	
	public function action_list($id = FALSE)
	{
		
		Code::css('
			table.data-table .first {
				font-weight:bold;
				font-size: 16px;
				padding-left:24px;
			}
			table.data-table .top-level {
				background:url('.url::site('images/admin/catalog.png').') no-repeat 3px 12px;
			}
			table.data-table .first {
				background-image: url('.url::site('images/admin/folder.png').');
				background-repeat: no-repeat;
				background-position: 3px 13px;
			}
			table.data-table .first small {
				font-size: 12px;
				font-weight:normal;
			}
			table.data-table tr.product td.first {
				background-image: url('.url::site('images/admin/product.png').');
				background-position: 3px 10px;
				font-size: 14px;
			}
		');
		
		Code::js_onload('
			$(".edit-catalog-name").click(function() {
				$(this).parents("td").find(".catalog-name-input").show().end().find(".catalog-name-text, small").hide();
				$(this).parents("td").find(".catalog-name").focus();
			});
			$(".catalog-name").change(function() {
				var obj = this;
				$.post("'.url::site('admin/catalogs/update').'", $(this).serializeArray(), function(data) {
					var td = $(obj).parents("td")
					
					if(data.success == true) {
						td.find(".catalog-name-text").text(td.find(".catalog-name").val());
						console.log(td.find(".catalog-name").val());
					}
					
					td.find(".catalog-name-input").hide();
					td.find(".catalog-name-text").show();
					td.find("small").show();
				}, "json");
			});
			$(".catalog-name").blur(function() {
				$(this).parents("td").find(".catalog-name-input").hide().end().find(".catalog-name-text, small").show();
			});

		');
		
		$this->menu['catalogs']['children']['catalogs']['selected'] = TRUE;
		$this->template->body = View::factory('admin/catalogs/list');
		
		list($id, $path) = Hierarchy::parseid($id);

		$catalogs = Model_Catalog::all($id);
		
		$actions[] = F3::button('add_category')->value("New Catalog")->label(FALSE)->cssParent(array('margin-bottom'=>'0px', 'float'=>'right'))->attr('href', url::site('admin/catalogs/add/'.Hierarchy::buildid($path)));
		
		if($id > 0)
		{

			$actions[] = F3::button('add_product')->value("New Product")->label(FALSE)->cssParent(array('margin-bottom'=>'0px', 'float'=>'right'))->attr('href', url::site('admin/products/add/'.Hierarchy::buildid($path)));

			$info = Model_Catalog::factory($id)->get();
			if(strlen($info['catalog_name']) > 0)
				$catalog_name = $info['catalog_name'];
			else
				$catalog_name = 'New Catalog';
				

			$products = Model_Product::all($id);
			$this->template->body->products = $products;
			
			$select_catalogs = Model_Catalog::pick($path);
			
			$new_path = array();
			foreach($path as $cid)
			{
				$new_path[] = $cid;
				if($cid == $id)
				{
					$name = $info['catalog_name'];
				}
				else
					$name = isset($select_catalogs[$cid]) ? $select_catalogs[$cid]['catalog_name'] : 'Unknown';
				$this->breadcrumb($name, 'admin/catalogs/list/'.implode('_', $new_path));
			}
		
		}
		
		$this->template->page_actions = implode("\n\n", $actions);
		
		$this->template->body->catalog_id = $id;
		$this->template->body->catalog_path = $path;
		$this->template->body->catalogs = $catalogs;
		
		$this->template->notes = '
			<ul>
				<li>Make the catalog have two levels of "categories."
				<li>Top level can be linked to Galleries, with the sub level appearing in the front end.
				<li>On this page the Admin can browse through the categories and sub categories to get to the products.
				<li>No sorting allowed since the sorting on these pages would be representative of the front end.
				<li>Searching would be allowed and pull products out from the heirarchy.
			</ul>
		';		
	}
	
	public function action_add($base = FALSE)
	{
		$this->add_edit($base, FALSE);
	}
	
	
	public function action_delete($path, $id = FALSE)
	{
		$catalog_id = FALSE;
		if($id)
		{
			$catalog_id = $id;
		}
		else
		{
			$catalog_id = $path;
			$path = '';
		}
		
		$catalog = new Model_Catalog($catalog_id);
		if($this->input->get('mode'))
		{
			
			$cat_ids = $catalog->delete();
			print_r($cat_ids);
			die();
			if($this->input->get('mode') == 'all')
			{
				//DB::delete('catalogs');
			}
			
			$this->request->redirect('admin/catalogs/list/'.$path);
		}
		
		$this->panel = TRUE;

		$info = $catalog->get();
		
		
		$this->template->page_title = 'Delete "'.$info['catalog_name'].'" catalog?';
		
		$form[] = F3::open();
		
		$form[] = '<fieldset><legend>Yes, delete...</legend>';
		
		$form[] = '<div style="width:525px; margin:0 auto;">';
		$form[] = F3::button('delete-all')->cssParent(array('margin'=>'0'))
				->value("Delete Catalog and ALL Products")->label(FALSE)
				->posttext('This will delete all catalogs, sub-catalogs and products that are not in any other categories.')
				->attr('href', url::site('admin/catalogs/delete/'.$path.'/'.$catalog_id).'?mode=all');
		
		$form[] = '<strong> - OR - </strong>';
		
		$form[] = F3::button('delete-catalog')
				->value("Delete Catalog, KEEP Products")->label(FALSE)
				->posttext('This will delete catalogs and sub-catalogs only.')
				->attr('href', url::site('admin/catalogs/delete/'.$path.'/'.$catalog_id).'?mode=part');
		$form[] = '</div>';
			
		$form[] = '</fieldset>';
		
		$form[] = '<div style="text-align:center;">';
		$form[] = '<a href="'.url::site('admin/catalogs/list/'.$path).'">Cancel</a>';
		$form[] = '</div>';
		
		$this->template->body = implode("\n\n", $form);
		
		
	}
	
	public function action_edit($path, $id = FALSE)
	{
		if($id)
		{
			$this->add_edit($path, $id);
		}
		else
		{
			$this->add_edit(FALSE, $path);
		}
	}
	
	public function add_edit($path = FALSE, $id = FALSE)
	{
		$this->menu['catalogs']['children']['catalogs']['selected'] = TRUE;
		
		$parent_id = FALSE;
		
		if($path)
		{
			list($parent_id, $path) = Hierarchy::parseid($path);
			if($id) $parent_id = FALSE;
		}
		else
		{
			$path = array();
		}
		
		$catalog = Model_Catalog::factory($id);
				
		/** UPDATE CATALOG **/
		if($this->input->post())
		{
			
			$catalog->set(array(
				'catalog_name' 			=> $this->input->post('catalog_name'),
				'catalog_parent_id' 	=> $this->input->post('catalog_parent_id'),
				'catalog_description' 	=> $this->input->post('catalog_description'),
				'catalog_enabled' 		=> $this->input->post('catalog_enabled'),
			));
			
			
			
			$catalog->save();
			
			$this->request->redirect('admin/catalogs/list/'.Hierarchy::buildid($path));
		}
		
		

		$info = $catalog->get();
		
		if($parent_id) $info['catalog_parent_id'] = $parent_id;

		$select_catalogs = Model_Catalog::pick($path);

		$new_path = array();
		foreach($path as $cid)
		{
			$new_path[] = $cid;
			$name = isset($select_catalogs[$cid]) ? $select_catalogs[$cid]['catalog_name'] : 'Unknown';
			$this->breadcrumb($name, 'admin/catalogs/list/'.implode('_', $new_path));
		}
		if($id)
		{
			$this->breadcrumb($info['catalog_name'], 'admin/catalogs/edit/'.Hierarchy::buildid($new_path).'/'.$id);
		}
		else
		{
			$this->breadcrumb('Add Catalog', 'admin/catalogs/add/'.Hierarchy::buildid($new_path));
		}
		
		$form[] = F3::open();
		
		
		$form[] = F3::text('catalog_name')->label(FALSE)->css(array('font-size'=>'16px', 'font-weight'=>'bold', 'width'=>'90%'))->value($info['catalog_name']);

		$form[] = '<fieldset><legend>General Information</legend>';

		$form_parent = F3::select('catalog_parent_id')->label("Parent Catalog")->value($info['catalog_parent_id'])
						->choice('0', '-- No Parent --');
		
		$parent_catalogs = Model_Catalog::hierarchy();
		$this->populate_select($parent_catalogs, $form_parent);
		
		$form[] = $form_parent;
		

		$form[] = F3::textarea('catalog_description')->label('Description')->value($info['catalog_description']);

		$form[] = F3::radio('catalog_enabled')->label("Status")
							->choice(0, 'Disabled')
							->choice(1, 'Enabled')
							->value($info['catalog_enabled']);
		
		$form[] = '</fieldset>';
		
		$form[] = '<fieldset>';
		$form[] = F3::submit('submit')->value(!$id ? 'Add Catalog' : 'Update Catalog')->label(FALSE)->cssParent(array('margin'=>'0 auto', 'width'=>'100px'))->css('margin', '0');
		$form[] = '</fieldset>';
		
		$form[] = F3::close();
		
		$this->template->body = implode("\n\n", $form);
	}
	
	private function populate_select($items, &$form, $level = 0)
	{
		foreach($items as $item)
		{
			$form->choice($item['catalog_id'], str_repeat('&nbsp;', 4*$level) . $item['catalog_name']);
			if(isset($item['children']) && is_array($item['children']))
				$this->populate_select($item['children'], $form, $level + 1);
		}
	}
	
	public function action_update($type='name')
	{
		$output['success'] = FALSE;
		if($this->input->post())
		{
			$output['success'] = TRUE;
			foreach($this->input->post('catalog_name', array()) as $id=>$name)
			{
				$catalog = Model_Catalog::factory($id);
				
				if($type == 'name')
				{
					$catalog->set(array(
						'catalog_name' => $name,
					));
				}
				
				$catalog->save();
			}
			
		}
		echo json_encode($output);
	}

	
}
