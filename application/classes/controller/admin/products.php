<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Products extends Controller_Admin_Base {

	public function __construct($args)
	{
		parent::__construct($args);
		
		$this->menu['catalogs']['selected'] = TRUE;
		$this->menu['catalogs']['children'] = Controller_Admin_Catalogs::menu();
	}

	public function action_index()
	{
		$this->action_list();
	}
	
	public function action_list()
	{
		

		Code::css('
			table.data-table .first {
				font-weight:bold;
				font-size: 16px;
				padding-left:24px;
			}
			table.data-table .first small {
				font-size: 12px;
				font-weight:normal;
			}
			table.data-table tr.product td.first {
				background-image: url('.url::site('images/admin/product.png').');
				background-repeat: no-repeat;
				background-position: 3px 10px;
				font-size: 14px;
			}
		');
		
		$this->menu['catalogs']['children']['all']['selected'] = TRUE;
		$this->template->body = View::factory('admin/products/list');
		
		$actions[] = F3::button('add_product')->value("New Product")->label(FALSE)->cssParent(array('margin-bottom'=>'0px', 'float'=>'right'))->attr('href', url::site('admin/products/add/'));
		
		$products = Model_Product::all();
		
		$this->template->page_actions = implode("\n\n", $actions);
		
		$this->template->body->products = $products;

	}
	
	public function action_add($path = FALSE)
	{
		$this->add_edit($path, FALSE, TRUE);
	}
	
	public function action_edit($path, $id = FALSE)
	{
		if($id)
		{
			$this->add_edit($path, $id);
		}
		else
			$this->add_edit(FALSE, $path);
	}
	
	
	public function add_edit($path, $id = FALSE, $add = FALSE)
	{
		
		list($parent_id, $path) = Hierarchy::parseid($path);
		
		
		$product = Model_Product::factory($id);


		/** UPDATE PRODUCT **/
		if($this->input->post())
		{
			$product->set(array(
				'product_name' => $this->input->post('product_name'),
				'product_price' => $this->input->post('product_price'),
				'product_description' => $this->input->post('product_description'),
				'product_stock' => $this->input->post('product_stock'),
				'product_enabled' => $this->input->post('product_enabled'),
			));
			
			foreach($this->input->post('catalog_ids') as $catalog_id)
			{
				$product->parentid($catalog_id);
			}
			
			$attributes = $this->input->post('product_attribute');
			foreach($attributes as $aid=>$attr)
			{
				if($aid == 'hidden') continue;
				
				$prod_attr = $product->attribute(is_numeric($aid) ? $aid : FALSE);
				
				if($attr['delete'] == 'yes')
				{
					$prod_attr->delete = TRUE;
					continue;
				}
				
				$prod_attr->set('product_attribute_name', $attr['name']);
				$prod_attr->set('product_attribute_type', $attr['type']);
				$prod_attr->set('product_attribute_price', $attr['price']);
				$prod_attr->set('product_attribute_sort', $attr['sort']);
				
				if(isset($attr['options']))
				foreach($attr['options'] as $oid=>$opt)
				{
					if($oid == 'hidden') continue;
					
					$prod_opt = $prod_attr->option(is_numeric($oid) ? $oid : FALSE);
					
					if($opt['delete'] == 'yes')
					{
						$prod_opt->delete = TRUE;
						continue;
					}
					
					$prod_opt->set('product_attribute_name', $opt['name']);
					$prod_opt->set('product_attribute_price', $opt['price']);
					$prod_opt->set('product_attribute_sort', $opt['sort']);
					
				}
			}
			
			
			$product->save();
			if($path)
				$this->request->redirect('admin/catalogs/list/'.Hierarchy::buildid($path));
			else
				$this->request->redirect('admin/products');
		}


		
		$info = $product->get();


		if(strlen($info['product_name']) > 0)
			$product_name = $info['product_name'];
		else
			$product_name = 'New Product';
			

		$select_catalogs = Model_Catalog::pick($path);

		if($path)
		{
			$this->menu['catalogs']['children']['catalogs']['selected'] = TRUE;
			$new_path = array();
			foreach($path as $cid)
			{
				$new_path[] = $cid;
				$name = isset($select_catalogs[$cid]) ? $select_catalogs[$cid]['catalog_name'] : 'Unknown';
				$this->breadcrumb($name, 'admin/catalogs/list/'.implode('_', $new_path));
			}
	
			if(!$id)
			{
				$info['catalog_ids'] = (array)$parent_id;
				$this->breadcrumb('Add Product', 'admin/products/add/'.implode('_', $new_path).'');
			}
			else
				$this->breadcrumb($info['product_name'], 'admin/products/edit/'.implode('_', $new_path).'/'.$id);
		}
		else
		{
			$this->menu['catalogs']['children']['all']['selected'] = TRUE;
			if(!$id)
			{
				$this->breadcrumb('Add Product', 'admin/products/add/');
			}
			else
				$this->breadcrumb($info['product_name'], 'admin/products/edit/'.$id);
		}
				
		$form[] = F3::open();
		
		$form[] = '<div class="content-thin">';
		
		$form[] = F3::text('product_name')->label(FALSE)->css(array('font-size'=>'16px', 'font-weight'=>'bold', 'width'=>'98%'))->value($info['product_name']);
		

		$form[] = '<fieldset><legend>General Information</legend>';
		$form[] = F3::text('product_price')->css('width', '80px')->label('Price')->value($info['product_price']);
		$form[] = F3::textarea('product_description')->label('Description')->value($info['product_description']);


		$form[] = F3::radio('product_enabled')->label("Status")
							->choice(1, 'Enabled')
							->choice(0, 'Disabled')
							->value($info['product_enabled']);
		$form[] = '</fieldset>';
		
		$form[] = '<fieldset><legend>Shipping &amp; Inventory</legend>';
		
		$form[] = F3::text('product_stock')->css('width', '60px')->label('Items in Stock')->value($info['product_stock']);
		$form[] = F3::text('product_weight')->css('width', '60px')->label('Weight')->value($info['product_stock']);
		
		$form[] = '</fieldset>';
		
		$form[] = '<fieldset><legend>Custom Attributes</legend>';
		
		$attributes = $product->attributes();
		
		$form[] = '<ul class="form-list" id="attributes-list">';
		$form = array_merge($form, $this->build_attribute());
		
		foreach($attributes as $attr)
		{
			$form = array_merge($form, $this->build_attribute($attr));
		}
		$form[] = '</ul>';

		$form[] = F3::button('add-attribute')
				->value("+ Add Attribute")->label(FALSE)->addClass('add-attribute')
				->css('margin', '3px 0 0')->cssParent(array('float'=>'right', 'margin'=>'0'));
		
		$form[] = '</fieldset>';
		
		
		
		
		/** Side bar information **/
		
		$form[] = '</div><div class="sidebar">';

		$form[] = '<div class="wrapper">';
		
			$form[] = '<fieldset>';
			$form[] = F3::submit('submit')->value(!$id ? 'Add Product' : 'Update Product')->label(FALSE)->cssParent(array('margin'=>'0 auto', 'width'=>'100px'))->css('margin', '0');
			$form[] = '</fieldset>';
			
			$form[] = '<h2>Parent Catalogs</h2>';
			$form_parent = F3::add('catalog_ids', 'checklist')->label(FALSE)->value($info['catalog_ids'])->css(array('width'=>'auto', 'height'=>'300px', 'float'=>'none'));
			
			$parent_catalogs = Model_Catalog::hierarchy();
			$this->populate_select($parent_catalogs, $form_parent);
			
			$form[] = $form_parent;
		
		$form[] = '</div></div>';

		
		$form[] = F3::close();
		
		
		
		$this->template->body = implode("\n\n", $form);
		
		Code::css('
			#attributes-list .form-element {
				margin: 0;
				float: left;
			}
			
			#attributes-list li .handle {
				float: left;
				width: 16px;
				height: 24px;
				text-shadow: 0px 1px 0 #FFFFFF;
				line-height: 7px;
				cursor: move;
			}
			
			.options-expand {
				background-color: #555;
				color: #FFF;
				float: left;
				width: 18px;
				height: 18px;
				line-height: 14px;
				text-align: center;
				font-size: 18px;
				font-weight: bold;
				margin: 3px 4px 0 0;
				text-decoration: none;
			}
			
			.delete-attribute,
			.delete-option {
				float: right;
				line-height: 24px;
			}
			
			
		');
		
		Code::js_onload('
			$("#attributes-list").sortable({
				handle: ".handle",
				axis: "y",
				update: function() {
					$("#attributes-list > li").each(function(i) {
						$(">:not(fieldset) .sort", this).val(i);
					});
				}
			});

			$("#attributes-list .form-list").sortable({
				handle: ".handle",
				axis: "y",
				update: function(event, ui) {
					$("> li", this).each(function(i) {
						$(".sort", this).val(i);
					});
				}
			});
			
			$(".options-expand").click(function() {
				var options = $(this).nextAll(".options");
				if(options.is(":visible")) {
					options.hide();
					$(this).text("+");
				} else {
					options.show();
					$(this).text("-");
				}
			});
			
			$(".add-option").click(function() {
				var list = $(this).parents("li.attribute-item").find("ul.form-list");
				var row = list.find("> li:eq(0)").clone(true);
				var prow = processOption(row);
				list.append(prow);
			});

			$(".add-attribute").click(function() {
				var list = $("#attributes-list");
				var row = list.find("> li:eq(0)").clone(true);
				var prow = processAttribute(row);
				list.append(prow);
			});
			
			$(".delete-attribute").click(function() {
				var parent = $(this).parents(".attribute-item");
				parent.find("> :not(fieldset) .delete").val("yes");
				parent.hide();
			});
			
			$(".delete-option").click(function() {
				var parent = $(this).parents(".option-item");
				parent.find(".delete").val("yes");
				parent.hide();
			});
		');
		
		Code::js('
			function processOption($row) {
				var id = "new-" + Math.round(Math.random() * 4000);
				$row.find("input, select").each(function(i, v) {
					$(this).attr("name", $(this).attr("name").replace("[hidden]", "["+id+"]"));
					$(this).val("");
				});
				$row.show();
				return $row;
			}
			function processAttribute($row) {
				var id = "new-" + Math.round(Math.random() * 4000);
				$row.find("input, select").each(function(i, v) {
					$(this).attr("name", $(this).attr("name").replace("e[hidden]", "e["+id+"]"));
					$(this).val("");
				});
				$row.show();
				return $row;
			}
		');
		
	}
	
	private function populate_select($items, &$form, $level = 0)
	{
		foreach($items as $item)
		{
			$form->choice($item['catalog_id'], $item['catalog_name'], array('level'=>$level));
			if(isset($item['children']) && is_array($item['children']))
				$this->populate_select($item['children'], $form, $level + 1);
		}
	}
	
	private function build_attribute($attr = FALSE)
	{
		$hidden = '';
		if(!$attr)
		{
			$attr = Model::_get('product_attributes');
			$attr['product_attribute_id'] = 'hidden';
			$hidden = 'display:none;';
		}
		
		$form = array();
		
		$form[] = '<li class="attribute-item" style="'.$hidden.'"><span class="handle">&mdash; &mdash; &mdash;</span>';
		
		// Delete Button
		$form[] = '<a href="javascript:void(0)" class="delete-attribute">Delete</a>';
		
		$form[] = '<a href="javascript:void(0)" class="options-expand">+</a>';
		
		// Sort & Delete Hidden Fields
		$form[] = F3::hidden('product_attribute['.$attr['product_attribute_id'].'][sort]')->value($attr['product_attribute_sort'])->addClass('sort');
		$form[] = F3::hidden('product_attribute['.$attr['product_attribute_id'].'][delete]')->value('no')->addClass('delete');
		
		// Attribute Name
		$form[] = F3::text('product_attribute['.$attr['product_attribute_id'].'][name]')->value($attr['product_attribute_name'])->label(FALSE);
		
		// Attribute Type
		$types = F3::select('product_attribute['.$attr['product_attribute_id'].'][type]')
				->value($attr['product_attribute_type'])->label(FALSE)
				->cssParent('margin-left', '5px');
		foreach(Model_Product::$attribute_types as $id=>$type)
		{
			$types->choice($id, $type['name']);
		}
		$form[] = $types;
		
		// Attribute Price
		$form[] = F3::text('product_attribute['.$attr['product_attribute_id'].'][price]')
				->value($attr['product_attribute_price'])->label(FALSE)
				->cssParent('margin-left', '5px')->css(array('width'=>'80px'));
		
		$form[] = '<div class="clear"></div>';
		
		// Begin options listing
		$form[] = '<fieldset class="integral options" style="padding: 2px 8px; margin:0 -4px; display:none; "><legend>Options</legend>';
		$form[] = '<ul class="form-list">';
		$form = array_merge($form, $this->build_attribute_option($attr['product_attribute_id']));
		if(isset($attr['options']))
		{
			foreach($attr['options'] as $id=>$option)
			{
				$form = array_merge($form, $this->build_attribute_option($attr['product_attribute_id'], $option));
			}
		}
		$form[] = '</ul>';
		$form[] = F3::button('add-option-'.$attr['product_attribute_id'])
				->value("+ Add Option")->label(FALSE)->addClass('add-option')
				->css('margin', '3px 0 0')->cssParent(array('float'=>'right', 'margin'=>'0'));
		$form[] = '</fieldset>';
		// End listing
		
		$form[] = '</li>';
		
		return $form;
	}
	
	private function build_attribute_option($id, $option = FALSE)
	{
		$hidden = '';
		if(!$option)
		{
			$option = Model::_get('product_attributes');
			$option['product_attribute_id'] = 'hidden';
			$hidden = 'display:none;';
		}
		
		
		$form[] = '<li class="option-item" style="'.$hidden.'"><span class="handle">&mdash; &mdash; &mdash;</span>';

		// Delete Button
		$form[] = '<a href="javascript:void(0)" class="delete-option">Delete</a>';

		// Sort & Delete Hidden Fields
		$form[] = F3::hidden('product_attribute['.$id.'][options]['.$option['product_attribute_id'].'][sort]')->value($option['product_attribute_sort'])->addClass('sort');
		$form[] = F3::hidden('product_attribute['.$id.'][options]['.$option['product_attribute_id'].'][delete]')->value('no')->addClass('delete');
		
		// Default Checkbox
		$form[] = F3::checkbox('product_attribute['.$id.'][default][]')->value($option['product_attribute_id'])->label(FALSE);

		// Option Name
		$form[] = F3::text('product_attribute['.$id.'][options]['.$option['product_attribute_id'].'][name]')->value($option['product_attribute_name'])->label(FALSE);
		
		// Option Price
		$form[] = F3::text('product_attribute['.$id.'][options]['.$option['product_attribute_id'].'][price]')
				->value($option['product_attribute_price'])->label(FALSE)
				->cssParent('margin-left', '5px')->css(array('width'=>'80px'));
		$form[] = '</li>';
		
		return $form;
	}

	
}
