<?php
namespace Shed;

/*
 * This file is part of Shed library.
 *
 * (c) Przemek Sobstel <przemek@sobstel.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Dependency Injection container.
 */
class Container extends \ArrayObject {

  /**
   * Inidicates special definition to resolve var
   */
  const DEFINITION = '_DEF_';

  protected $resolved_vars = array();

  public function __construct(array $array = array()) {
    parent::__construct($array, self::ARRAY_AS_PROPS);
  }

  public function offsetSet($name, $value) {
    if (isset($this->resolved_vars[$name])) {
      unset($this->resolved_vars[$name]);
    }
    return parent::offsetSet($name, $value);
  }

  public function offsetGet($name) {
    $value = (parent::offsetExists($name) ? parent::offsetGet($name) : null);
    if (!isset($this->resolved_vars[$name])) {      
      $value = $this->resolve($value);
      $this->offsetSet($name, $value);
      $this->resolved_vars[$name] = true;
    }
    return $value;
  }

  public function __call($method, $args) {
    if (substr($method, 0, 3) == 'get') {
      $name = strtolower($method{3}).substr($method, 4); // transforming getVarName => var_name
      return $this[$name];
    } else {
      throw new Exception('Call to undefined method'. __CLASS__ .'::'.$method.'()', E_USER_ERROR);
    }
  }

  /**
   * Resolves reference and definitions (if found)
   *
   * @param mixed $var
   * @return mixed
   */
  protected function resolve($var) {
    $var = $this->resolveReference($var);
    $var = $this->resolveDefinition($var);
    return $var;
  }

  /**
   * Resolve many vars in row
   *
   * @param array $array
   * @return mixed
   */
  protected function resolveMany(array $vars) {
    foreach ($vars as &$var) {
      $var = $this->resolve($var);
    }
    return $vars;
  }

  /**
   * Resolves definition (self::DEFINITION)
   *
   * @param mixed $var Definition to resolve or primitive type
   * @return mixed
   */
  protected function resolveDefinition($var) {
    if (is_array($var) && (reset($var) === self::DEFINITION)) {
      $def = $var;

      $is_class = isset($def['class']);
      $is_callback = isset($def['callback']);

      // require files
      if (isset($def['file'])) {
        $files = $def['file'];
        if (!is_array($files)) {
          $files = array($files);
        }
        foreach ($files as $file) {
          require_once $file;
        }
      }

      // args
      $args = array();
      if (isset($def['args'])) {
        $args = $this->resolveMany($def['args']);
      }

      // create instance or call callback
      if ($is_class) {
        $ref_class = new \ReflectionClass($def['class']);
        $var = $ref_class->newInstanceArgs($args);
      } elseif ($is_callback) {
        $callback = $def['callback'];
        if (is_array($callback)) {
          $callback = $this->resolveMany($callback);
        }
        $var = call_user_func_array($callback, $args);
      } else {
        throw new Exception('Either "class" or "callback" must be defined for definition');
      }

      // execute methods
      if (isset($def['methods'])) {
        foreach ($def['methods'] as $method) {
          $method_args = array();
          if (isset($method['args'])) {
            $method_args = $this->resolveMany($method['args']);
          }

          call_user_func_array(array($var, $method['name']), $method_args);
        }
      }

      // set properties
      if (isset($def['properties'])) {
        foreach ($def['properties'] as $property) {
          $var->{$property['name']} = $this->resolve($property['value']);
        }
      }
    }

    return $var;
  }

  /**
   * Resolves reference to other var ("&name")
   *
   * @param mixed $var
   * @return mixed
   */
  protected function resolveReference($var) {
    if (is_string($var) && ($var{0} == '&')) {
      $var = $this[substr($var, 1)];
    }

    return $var;
  }

}

class Exception extends \Exception {
}
