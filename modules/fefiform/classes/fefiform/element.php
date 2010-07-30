<?php

class Fefiform_Element
{
	public $type;
	public $name;

	public $id;

	public $value;
	public $data;

	public $choices = array();
	
	public $attr = array();
	public $info = array('label' => '&nbsp;');
	
	public $attrParent = array();
	
	private $wrapper_class = "form-element";
	private $label_class = "form-label";
	
	public $js;
	
	private static $standard_elements = array(
									'text',
									'password',
									'hidden',
									'select',
									'checkbox',
									'radio',
									'textarea',
									'button',
									'submit',
									'file',
									'html',
									'plaintext'
								);
								
	public static function factory($name, $type)
	{
		if(in_array($type, self::$standard_elements))
		{
			return new Fefiform_Element($name, $type);
		}
		else
		{
			$class = "Fefiform_" . ucfirst($type) . "";
			return new $class($name, $type);
		}
	}
	
	public function __construct($name, $type)
	{
		$this->name = $name;
		
		$this->id = strtr($name, array('['=>'_', ']'=>''));
		
		$this->type = $type; 

		$this->attr['name'] = $this->name;
		$this->attr['id'] = $this->id;

	}


	
	/* Manipulation Functions */
	
	public function value($val)
	{
		$this->value = $val;
		return $this;
	}
	
	public function attr($key, $val)
	{
		$this->attr[$key] = $val;
		return $this;
	}
	
	public function css($name, $value = NULL)
	{
		$styles = array();
		if(is_array($name))
		{
			$styles = $name;
		}
		elseif(strlen($name) > 0)
		{
			$styles = array($name=>$value);
		}
		
		foreach($styles as $k=>$v)
		{
			$v = trim($v);
			if(substr($v, -1) != ';') $v .= ';';
			
			$this->attr['style'][$k] = $v;
		}
		
		return $this;
	}
	
	public function addClass($class)
	{
		if(isset($this->attr['class']) && is_array($this->attr['class']))
		{
			$this->attr['class'] = array_merge($this->attr['class'], (array)$class);
		}
		else
		{
			$this->attr['class'] = (array)$class;
		}
		return $this;
	}
	
	public function choice($key, $text, $extra=array())
	{
		$this->choices[] = array('key'=>$key, 'text'=>$text, 'extra'=>$extra);
		
		return $this;
	}

	public function attrParent($key, $val)
	{
		$this->attrParent[$key] = $val;
		return $this;
	}

	public function cssParent($name, $value = NULL)
	{
		$styles = array();
		if(is_array($name))
		{
			$styles = $name;
		}
		elseif(strlen($name) > 0)
		{
			$styles = array($name=>$value);
		}
		
		foreach($styles as $k=>$v)
		{
			$v = trim($v);
			if(substr($v, -1) != ';') $v .= ';';
			
			$this->attrParent['style'][$k] = $v;
		}
		
		return $this;
	}

	public function addClassParent($class)
	{
		if(isset($this->attrParent['class']) && is_array($this->attrParent['class']))
		{
			$this->attrParent['class'] = array_merge($this->attrParent['class'], (array)$class);
		}
		else
		{
			$this->attrParent['class'] = (array)$class;
		}
		return $this;
	}

	
	/* End Manipulation */
	
	/* Custom styles */
	
	
	public function large()
	{
		$this->css(array(
			'font-size' 	=> '18px',
			'font-weight'	=> 'bold',
		));
	}
	
	public function style($type)
	{
		
	}
	
	
	
	/* End custom styles */
	
	
	
	
	
	
	public function render()
	{
		$output = $this->render_start();
		
		if(isset($this->info['pretext']))
			$output .= $this->info['pretext'];
		
		switch($this->type) {
			case 'text':
			case 'password':
			case 'hidden':
			case 'file':
					$output .= $this->render_input($this->type);
					break;
			case 'checkbox':
					$output .= $this->render_checkbox();
					break;
			case 'checkboxes':
					$output .= $this->render_checkboxes();
					break;
			case 'radio':
					$output .= $this->render_radios();
					break;
			case 'select':
					$output .= $this->render_select();
					break;
			case 'textarea':
					$output .= $this->render_textarea();
					break;
			case 'submit':
			case 'button':
					$output .= $this->render_button($this->type);
					break;
			case 'plaintext':
					$output .= $this->render_plaintext();
					break;
		}

		if(isset($this->info['posttext']))
			$output .= $this->info['posttext'];

		$output .= $this->render_end();
		
		return $output;
	}


	private function render_input($type)
	{
		$render = '';
		
		if($type != 'hidden')
		{
			$render .= $this->render_label();
		}
		
		$this->attr['value'] = $this->value;
		
		if($type == 'checkbox')
		{
			$this->attr['name'] .= '[]';
		}
		
		$this->addClass("form-".$type);
		
		$attributes = $this->attributes();
		
		$render .= '<input type="'.$type.'" '.$attributes.' />' . "\n";
		
		return $render;
	}

	private function render_button($type)
	{
		$render = '';
		
		$render .= $this->render_label();
		
		$this->addClass("button");
		$this->addClass("form-".$type);
		
		if(!isset($this->attr['href'])) $this->attr('href', 'javascript:void(0);');
		
		$attributes = $this->attributes();
		
		$render .= '<a '.$attributes.'><span>'.$this->value.'</span></a>' . "\n";

		if($type == 'submit')
		{
			Code::js_onload('$("#'.$this->id.'").click(function() { $(this).parents("form").submit(); });');
			$render .= '<input type="submit" style="display:none; " />';
		}		
		
		return $render;
	}
	
	public function render_label($custom = FALSE)
	{	
		if(!$custom && (!isset($this->info['label']) || $this->info['label'] === FALSE))
			return '';
		
		$render = '<label class="'.$this->label_class.'">';
		$render .= ($custom) ? $custom : $this->info['label'];
		$render .= '</label>';
		
		return $render;
	}

	
	public function render_start()
	{
		$this->addClassParent($this->wrapper_class);
		$output = '<div '.$this->attributes($this->attrParent).'>';
		return $output;
	}
	public function render_end()
	{
		$output = '</div>';
		return $output;
	}
	

	public function attributes($attrs = FALSE)
	{
		if(!$attrs) $attrs = $this->attr;
		
		foreach($attrs as $key => &$attr)
		{
			if($key == 'style')
				$attr = $this->_merge($attr, ': ');
			
			if(is_array($attr))
			{
				$attr = implode(" ", $attr);
			}
		}
		return html::attributes($attrs);
	}
	
	

	
	
	private function render_plaintext() {}
	private function render_file() {}
	private function render_checkbox() {}
	private function render_checkboxes() {}
	private function render_single_checkbox($opt_value, $opt_name) {}
	private function render_radios() {
		$render = $this->render_group('radio');
		
		return $render;
	}
	
	private function render_group($type)
	{
		$render = $this->render_label();
		$render .= '<ul class="form-choice-list">';
		foreach($this->choices as $choice)
		{
			$this->attr['value'] = $choice['key'];
			
			if(in_array($this->attr['value'], (array)$this->value))
			{
				$this->attr['checked'] = 'checked';
			}
			else
			{
				unset($this->attr['checked']);
			}
			
			$render .= '<input type="'.$type.'" '.$this->attributes().' /> <span>' . $choice['text'] . '</span>' . "\n";
		}
		$render .= '</ul>';
		return $render;
	}
	
	private function render_single_radio($opt_value, $opt_name) {}
	
	private function render_textarea() {
		$render = '';
		
		$render .= $this->render_label();
		
		$this->addClass("form-textarea");
		
		$render .= '<textarea '.$this->attributes().'>' . $this->value . '</textarea>' . "\n";
		
		return $render;
	}
	
	private function render_select() {
		$render = '';
		
		$render .= $this->render_label();
		
		$this->addClass("form-select");

		$render .= '<select '.$this->attributes().'>'. "\n";
		foreach($this->choices as $choice)
		{
			$render .= $this->render_option($choice['key'], $choice['text']);
		}
		$render .= '</select>';
		
		return $render;
	}
	private function render_option($value, $name) {
		return '<option value="'.$value.'"'.($this->value == $value ? ' selected="selected"' : '').'>'.$name.'</option>';
	}
	

	
	
	private function _merge($array, $join)
	{
		$output = array();
		foreach($array as $k=>$v)
		{
			$output[] = $k . $join . $v;
		}
		
		return $output;
	}
	
	public function __call($method, $args)
	{
		$attributes = array(
			'value',
			'size',
		);
		
		if(in_array($method, $attributes))
		{
			$this->attr($method, count($args)==1 ? $args[0] : $args);
		}
		else
		{
			$this->info[$method] = count($args)==1 ? $args[0] : $args;
		}
		return $this;
	}
	
	public function __toString()
	{
		$output = '';
		$output .= $this->render();

		if(strlen($this->js)>0)
			Code::js($this->js);

		
		return $output;
	}

	
}
