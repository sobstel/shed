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
        'construct' => array(4, 5)
      )
    )

Definition options
------------------

    file (file to require if class not exists)
    file_if_class_not_exists (bool)
    class
    callback
    args (either construct args or callback args)
    methods
     name
     args
    properties
     name
     value

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
          'inject' => array('&injected_object'),
        ),
      ),
    )

Usage
-----

Shed extends ArrayObject, thus makes possible to access variables multiple
ways.

    $container = new \Shed\Container();
    $container->var;
    $container['var'];
    $container->getVar();
