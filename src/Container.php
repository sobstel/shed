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
      $value = $this->resolve($name, $value);
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
      trigger_error('Call to undefined method'. __CLASS__ .'::'.$method.'()', E_USER_ERROR);
    }
  }

  /**
   * Resolves reference and definitions (if found)
   *
   * @param mixed(string|null) $name
   * @param mixed $var
   * @return mixed
   */
  protected function resolve($name, $var) {
    $var = $this->resolveReference($var);
    $var = $this->resolveDefinition($name, $var);
    return $var;
  }

  /**
   * Resolve many vars in row
   *
   * @param mixed(string|null) $name
   * @param array $array
   * @return mixed
   */
  protected function resolveMany($name, array $vars) {
    foreach ($vars as &$var) {
      $this->resolve($name, $var);
    }
    return $vars;
  }

  /**
   * Resolves definition (self::DEFINITION)
   *
   * @param mixed(string|null) $name
   * @param mixed $var Definition to resolve or primitive type
   * @return mixed
   */
  protected function resolveDefinition($name, $var) {
    if (is_array($var) && (reset($var) === self::DEFINITION)) {
      $def = $var;

      $is_class = isset($def['class']);
      $is_callback = isset($def['callback']);

      // require file
      if (isset($def['file']) &&
        (!$is_class || !isset($def['file_if_class_not_exists']) || !class_exists($def['class'], true))) {
        require_once $def['file'];
      }

      // args
      $args = array();
      if (isset($def['args'])) {
        $args = $this->resolveMany(null, $def['args']);
      }

      // create instance or call callback
      if ($is_class) {
        $ref_class = new \ReflectionClass($def['class']);
        $var = $ref_class->newInstanceArgs($args);
      } elseif ($is_callback) {
        $var = call_user_func_array($def['callback'], $args);
      } else {
        trigger_error(
          'Either "class" or "callback" must be defined for "' . $name . '" definition',
          E_USER_ERROR
        );
      }

      // execute methods
      if (isset($def['methods'])) {
        foreach ($def['methods'] as $method) {
          $method_args = array();
          if (isset($method['args'])) {
            $method_args = $this->resolveMany(null, $method['args']);
          }

          call_user_func_array(array($var, $method['method']), $method_args);
        }
      }

      // set properties
      if (isset($def['properties'])) {
        foreach ($def['properties'] as $property) {
          $var->{$poperty['name']} = $this->resolve(null, $property['value']);
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
