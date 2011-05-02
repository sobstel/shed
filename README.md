Shed
====

Dependency Injection container.

First version of this library was written in 2007.
This is PHP 5.3 version extracted from some of my projects.

Definition
----------

All container variables are defined via simple associative array. You can set 
any value, either scalar values (booleans, integers, floats, strings) and 
compound values (arrays, objects).

    array(
      'bool' => true,
      'int' => 5,
      'float' => 1.2345,
      'string' => 'string',
      'array' => array(1, 2, 3),
      'object' => new SampleObject(),
    )

Shed supports lazy loading, which means the initialization of an object is 
delayed until the first time it is needed.

Definitions are marked with `Container::DEFINITION` set as first element of
array.

    array(
      'lazy_object' => array(
        Container::DEFINITION,
        'class' => 'LazyObject',
        'args' => array(4, 5)
      )
    )

References
----------

Any variable can be injected anywhere you want: constructors arguments, methods 
arguments, etc. Reference is indicated by ampersand sign (&) at the beginning.

    array(
      'injected_object' => array(
        Container::DEFINITION,
        'class' => 'InjectedObject',
      ),
      'lazy_object' => array(
        Container::DEFINITION,
        'class' => 'LazyObject',
        'methods' => array(
          array('name' => 'inject', 'args' => array('&injected_object')),
        ),
      ),
    )

Definition options
------------------

Possible options: `class`, `callback`, `args`, `methods` (`name`, `args`), 
`properties` (`name`, `value`), `files`.

### files

Files to require. Useful if proper autoloading callback is not registered.

    'object' => array(
      Container::DEFINITION,
      'class' => 'SampleObject',
      'file' => __DIR__.'/classes/SampleObject.php'
    )

### class

Class name of object to be initialized on call.

    'object' => array(
      Container::DEFINITION,
      'class' => 'SampleObject',
    )

### callback

Function.

	  'callback' => array(
		  Container::DEFINITION,
		  'callback' => 'floor',
		  'args' => array(4.89)
	  )

Callback with reference (call to object's method).

	  'callback_with_ref' => array(
		  Container::DEFINITION,
		  'callback' => array('&object', 'createObject')
	  )

Singleton.

	  'singleton' => array(
	 	  Container::DEFINITION,
		  'callback' => array('SingletonObject', 'get')
	  )

### args

Either construct arguments or callback arguments.

	  'object' => array(
		  Container::DEFINITION,
		  'class' => 'SampleObject',
		  'args' => array('val1', 'val2')
	  ),

### methods

Methods to call right after initialization.

	'object' => array(
		Container::DEFINITION,
		'class' => 'SampleObject',
		'methods' => array(
      array('name' => 'setArg', 'args' => array('val1')),
      array('name' => 'setArg', 'args' => array('val2')),
		)
	)

### properties

Properties to be set right after initialization.

	'object' => array(
		Container::DEFINITION,
		'class' => 'SampleObject',
		'properties' => array(
      array('name' => 'arg', 'value' => 'val')
		)
	)

Usage
-----

Shed extends ArrayObject, thus makes possible to access variables multiple
ways.

    $container = new \Shed\Container();
    $container->var;
    $container['var'];
    $container->getVar();
