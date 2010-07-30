<?php defined('SYSPATH') or die('No direct script access.');

class Themes
{
	public $db = 'default';
	
	public $id = FALSE;
	
	public $data = array();
	
	public function __construct($id = FALSE)
	{
		$this->id = $id;
	}
	
	public function set($key, $value=FALSE)
	{
		if(is_array($key))
		{
			$this->data = array_merge($this->data, $key);
		}
		else
		{
			$this->data[$key] = $value;
		}
		return $this;
	}
	
	
	
	
	public static function files()
	{
		$theme_root = DOCROOT . 'themes';
		
		/* Files in the root of the current theme directory and one subdir down */
		$themes_dir = @ opendir($theme_root);

		if ( !$themes_dir )
			return false;

		while ( ($theme_dir = readdir($themes_dir)) !== false ) {
			if ( is_dir($theme_root . '/' . $theme_dir) && is_readable($theme_root . '/' . $theme_dir) ) {
				if ( $theme_dir{0} == '.' || $theme_dir == 'CVS' )
					continue;

				$stylish_dir = @opendir($theme_root . '/' . $theme_dir);
				$found_stylesheet = false;

				while ( ($theme_file = readdir($stylish_dir)) !== false ) {
					if ( $theme_file == 'style.css' ) {
						$theme_files[$theme_dir] = array( 'theme_file' => $theme_dir . '/' . $theme_file, 'theme_root' => $theme_root );
						$found_stylesheet = true;
						break;
					}
				}
				@closedir($stylish_dir);

				if ( !$found_stylesheet ) { // look for themes in that dir
					// BROKEN
				}
			}
		}
		if ( is_dir( $theme_dir ) )
			@closedir( $theme_dir );
	
		return $theme_files;
		
	}
	
	public function parse_data($file)
	{
		
		$default_headers = array( 
			'Name' => 'Theme Name', 
			'URI' => 'Theme URI', 
			'Description' => 'Description', 
			'Author' => 'Author', 
			'AuthorURI' => 'Author URI',
			'Version' => 'Version', 
			'Template' => 'Template', 
			'Status' => 'Status', 
			'Tags' => 'Tags'
			);
	
		$themes_allowed_tags = array(
			'a' => array(
				'href' => array(),'title' => array()
				),
			'abbr' => array(
				'title' => array()
				),
			'acronym' => array(
				'title' => array()
				),
			'code' => array(),
			'em' => array(),
			'strong' => array()
		);
	
		$theme_data = get_file_data( $theme_file, $default_headers, 'theme' );
	
		$theme_data['Name'] = $theme_data['Title'] = wp_kses( $theme_data['Name'], $themes_allowed_tags );
	
		$theme_data['URI'] = esc_url( $theme_data['URI'] );
	
		$theme_data['Description'] = wptexturize( wp_kses( $theme_data['Description'], $themes_allowed_tags ) );
	
		$theme_data['AuthorURI'] = esc_url( $theme_data['AuthorURI'] );
	
		$theme_data['Template'] = wp_kses( $theme_data['Template'], $themes_allowed_tags );
	
		$theme_data['Version'] = wp_kses( $theme_data['Version'], $themes_allowed_tags );
	
		if ( $theme_data['Status'] == '' )
			$theme_data['Status'] = 'publish';
		else
			$theme_data['Status'] = wp_kses( $theme_data['Status'], $themes_allowed_tags );
	
		if ( $theme_data['Tags'] == '' )
			$theme_data['Tags'] = array();
		else
			$theme_data['Tags'] = array_map( 'trim', explode( ',', wp_kses( $theme_data['Tags'], array() ) ) );
	
		if ( $theme_data['Author'] == '' ) {
			$theme_data['Author'] = __('Anonymous');
		} else {
			if ( empty( $theme_data['AuthorURI'] ) ) {
				$theme_data['Author'] = wp_kses( $theme_data['Author'], $themes_allowed_tags );
			} else {
				$theme_data['Author'] = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', $theme_data['AuthorURI'], __( 'Visit author homepage' ), wp_kses( $theme_data['Author'], $themes_allowed_tags ) );
			}
		}
	
		return $theme_data;
		
	}

}