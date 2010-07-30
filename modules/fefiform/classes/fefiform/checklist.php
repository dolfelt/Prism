<?php

class Fefiform_Checklist extends Fefiform_Element
{
								
	
	
	
	
	public function render()
	{
		$output = $this->render_start();
		
		$output .= $this->render_label();
		
		unset($this->attr['name']);
		$output .= '<div class="checklist"'.$this->attributes().'><ul>';
		
		foreach($this->choices as $choice)
		{
			$selected = in_array($choice['key'], (array)$this->value);
			$output .= '<li>';
			$output .= '<label>';
			
			if(isset($choice['extra']['level']))
				$output .= str_repeat('&nbsp;', $choice['extra']['level']*4);
			
			$output .= '<input type="checkbox" value="'.$choice['key'].'"'.($selected ? ' checked="checked"' : '').' name="'.$this->name.'[]" />';
			$output .= ' '.$choice['text'] . '</label>';
			$output .= '</li>';
		}
		
		$output .= '</ul></div>';
		
		$output .= $this->render_end();
		
		return $output;
	}

}
