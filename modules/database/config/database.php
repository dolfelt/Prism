<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
	'default' => array
	(
		'type'       => 'mysql',
		'connection' => array(
			/**
			 * The following options are available for MySQL:
			 *
			 * string   hostname
			 * integer  port
			 * string   socket
			 * string   username
			 * string   password
			 * boolean  persistent
			 * string   database
			 */
			'hostname'   => 'db.prismapp.com',
			'username'   => 'prismapp',
			'password'   => '^pr1sm',
			'persistent' => FALSE,
			'database'   => 'prism_dan',
		),
		'table_prefix' => '',
		'charset'      => 'utf8',
		'caching'      => FALSE,
		'profiling'    => TRUE,
	),

);