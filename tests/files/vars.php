<?php
namespace Shed\Tests\Container;
use Shed\Container as Container;

return array(

	'bool' => true,
	'int' => 5,
	'float' => 1.2345,
	'string' => 'string',

	'array' => array(1, 2, 3),

	'object' => new SampleObject(),

	'lazy_object' => array(
		Container::DEFINITION,
		'class' => '\Shed\Tests\Container\SampleObject',
		'args' => array(4, 5)
	),

  'lazy_object_file_not_loaded' => array(
    Container::DEFINITION,
    'class' => '\Shed\Tests\Container\SampleObjectNotPreloaded',
    'file' => __DIR__.'/classes/SampleObjectNotPreloaded.php'
  ),

	'lazy_object_with_methods' => array(
		Container::DEFINITION,
		'class' => '\Shed\Tests\Container\SampleObject',
		'args' => array(6, 7),
		'methods' => array(
      array('name' => 'setArg3', 'args' => array(8))
		)
	),

	'lazy_object_with_properties' => array(
		Container::DEFINITION,
		'class' => '\Shed\Tests\Container\SampleObject',
		'properties' => array(
      array('name' => 'arg4', 'value' => 10)
		)
	),
  
	'lazy_object_with_ref' => array(
		Container::DEFINITION,
		'class' => '\Shed\Tests\Container\SampleObject',
		'args' => array('&float', 9),
		'methods' => array(
			array('name' => 'setArg3', 'args' => array('&int'))
		)
	),

	'callback' => array(
		Container::DEFINITION,
		'callback' => 'floor',
		'args' => array(4.89)
	),

	'callback_with_ref' => array(
		Container::DEFINITION,
		'callback' => array('&lazy_object', 'getArg3')
	),

	'singleton' => array(
		Container::DEFINITION,
		'callback' => array('\Shed\Tests\Container\SampleSingleton', 'get')
	),

);
