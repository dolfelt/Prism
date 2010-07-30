<?php

class Fefiform_Address extends Fefiform_Element
{
								
	
	
	
	
	public function render()
	{
		$output = $this->render_start();
		
		//$output .= $this->render_label();
		
		$output .= '<div class="form-address">';
		
		$base_name = $this->attr['name'];
		
		$form = array();
		
		$form['address1'] 	= F3::text($this->name . '[address1]')->label('Address 1');
		$form['address2'] 	= F3::text($this->name . '[address2]')->label('Address 2');
		$form['city'] 		= F3::text($this->name . '[city]')->label('City');
		$form['state'] 		= F3::text($this->name . '[state]')->label('State');
		$form['postal'] 	= F3::text($this->name . '[postal]')->label('Postal Code');
		
		foreach($form as $key=>&$item)
		{
			if(isset($this->value[$key]))
			{
				$item->value($this->value[$key]);
			}
			if(isset($this->label[$key]))
			{
				$item->label($this->label[$key]);
			}
		}
		
		$output .= implode("\n\n", $form);
		
		$output .= '</div>';
		
		$output .= $this->render_end();
		
		return $output;
	}

}
