<?php defined('SYSPATH') OR die('No direct access allowed.');

class Message {
	
	// Message instances
	public static $instances = array();

	const ERROR = "error";
	const INFO = "info";
	
	private $name = 'default';
	private $messages = array();
	
	public static function & instance($name = 'default')
	{
		if ( !isset(Message::$instances[$name]))
		{
			// Create a new instance
			Message::$instances[$name] = new Message($name);
		}

		return Message::$instances[$name];
	}

	public function __construct($name) {
		$this->name = $name;
		
		if(is_array(Session::instance()->get("messages-for-".$this->name)))
			$this->messages = Session::instance()->get("messages-for-".$this->name);
	}
	
	public function add($message, $type=self::INFO, $sticky = FALSE)
	{
		$this->messages[] = array('message'=>$message, 'type'=>$type, 'sticky'=>$sticky);
		$this->_setSession();
	}
	
	public function get($type = FALSE) {
		if($type === FALSE)
			return $this->messages;
		
		$return = array();
		foreach($this->messages as $msg)
		{
			if($msg['type'] == $type)
				$return[] = $msg;
		}
		
		return $return;
	}
	
	public function clear() {
		$this->messages = array();
		$this->_setSession();
	}
	
	private function _setSession()
	{
		Session::instance()->set("messages-for-".$this->name, $this->messages);
	}
	
}
