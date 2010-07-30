<?php defined('SYSPATH') or die('No direct script access.');

class Hook
{

	protected static $hooks		= array();
	protected static $has_run	= array();

	
	/**
	 * Add a callback to an hook queue.
	 *
	 * @param   string		$hook				Hook name
	 * @param   array		$callback			http://php.net/callback
	 * @param	boolean		$allow_duplicates	Should the callback be added if it already exists in the stack
	 * @return  boolean	
	 */
	static function add($hook, $callback, $allow_duplicates = FALSE)
	{
		if( ! isset(Hook::$hooks[$hook]))
			Hook::$hooks[$hook] = array();

		if( ! $allow_duplicates AND in_array($callback, Hook::$hooks, TRUE))
			return FALSE;

		Hook::$hooks[$hook][] = $callback;

		return TRUE;
	}
	/**
	 * Add a callback to an hook queue, before a given hook.
	 *
	 * @param   string   $name		Hook name
	 * @param   array    $existing	Existing hook callback
	 * @param   array    $callback	Hook callback
	 * @return  boolean
	 */
	public static function add_before($name, $existing, $callback)
	{
		if (empty(Hook::$hooks[$name]) OR ($key = array_search($existing, Hook::$hooks[$name])) === FALSE)
		{
			// Just add the hook if there are no hooks
			return self::add($name, $callback);
		}
		else
		{
			// Insert the hook immediately before the existing hook
			return self::insert_hook($name, $key, $callback);
		}
	}

	/**
	 * Add a callback to an hook queue, after a given hook.
	 *
	 * @param   string   $name		Hook name
	 * @param   array    $existing	Existing hook callback to add after
	 * @param   array    $callback	Hook callback to add
	 * @return  boolean
	 */
	public static function add_after($name, $existing, $callback)
	{
		if (empty(Hook::$hooks[$name]) OR ($key = array_search($existing, Hook::$hooks[$name])) === FALSE)
		{
			// Just add the hook if there are no hooks
			return self::add($name, $callback);
		}
		else
		{
			// Insert the hook immediately after the existing hook
			return self::insert_hook($name, $key + 1, $callback);
		}
	}

	/**
	 * Inserts a new hook at a specfic key location.
	 *
	 * @param   string   $name		Hook name
	 * @param   integer  $key		Key to insert new hook at
	 * @param   array    $callback	Hook callback
	 * @return  boolean
	 */
	private static function insert_hook($name, $key, $callback)
	{
		if (in_array($callback, Hook::$hooks[$name], TRUE))
			return FALSE;

		// Add the new hook at the given key location
		Hook::$hooks[$name] = array_merge
		(
			// Hooks before the key
			array_slice(Hook::$hooks[$name], 0, $key),
			// New hook callback
			array($callback),
			// Hooks after the key
			array_slice(Hook::$hooks[$name], $key)
		);

		return TRUE;
	}

	/**
	 * Replaces an hook with another hook.
	 *
	 * @param   string   $name		Hook name
	 * @param   array    $existing	Hook to replace
	 * @param   array    $callback	New callback
	 * @return  boolean
	 */
	public static function replace($name, $existing, $callback)
	{
		if (empty(Hook::$hooks[$name]) OR ($key = array_search($existing, Hook::$hooks[$name], TRUE)) === FALSE)
			return FALSE;

		if ( ! in_array($callback, Hook::$hooks[$name], TRUE))
		{
			// Replace the exisiting hook with the new hook
			Hook::$hooks[$name][$key] = $callback;
		}
		else
		{
			// Remove the existing hook from the queue
			unset(Hook::$hooks[$name][$key]);

			// Reset the array so the keys are ordered properly
			Hook::$hooks[$name] = array_values(Hook::$hooks[$name]);
		}

		return TRUE;
	}

	/**
	 * Get all callbacks for an hook.
	 *
	 * @param   string  $name	Hook name
	 * @return  array	Array of callbacks
	 */
	public static function get($name)
	{
		return empty(Hook::$hooks[$name]) ? array() : Hook::$hooks[$name];
	}

	/**
	 * Clear some or all callbacks from an hook.
	 *
	 * @param   string  $name		Hook name
	 * @param   array   $callback	Specific callback to remove, FALSE for all callbacks
	 * @return  void
	 */
	public static function clear($name, $callback = FALSE)
	{
		if ($callback === FALSE)
		{
			Hook::$hooks[$name] = array();
		}
		elseif (isset(Hook::$hooks[$name]))
		{
			// Loop through each of the hook callbacks and compare it to the
			// callback requested for removal. The callback is removed if it
			// matches.
			foreach (Hook::$hooks[$name] as $i => $hook_callback)
			{
				if ($callback === $hook_callback)
				{
					unset(Hook::$hooks[$name][$i]);
				}
			}
		}
	}

	/**
	 * Execute all of the callbacks attached to an hook.
	 *
	 * @param   string	$name	Hook name
	 * @param   array	$data	data to pass to callbacks.  Each element will be passed as an argument
	 * @return  array			data after it's been modified by the callbacks
	 */
	public static function run($name, array $data = array())
	{
		if ( ! empty(Hook::$hooks[$name]))
		{
			$callbacks  =  self::get($name);
			
			$count	= count($data);

			foreach ($callbacks as $callback)
			{
				$data = call_user_func_array($callback, $data);
			}
			
		}

		// The hook has been run!
		self::$has_run[$name] = $name;

		return $data;
	}

	/**
	 * Check if a given hook has been run.
	 *
	 * @param   string   $name	hook name
	 * @return  boolean
	 */
	public static function has_run($name)
	{
		return isset(self::$has_run[$name]);
	}


}
