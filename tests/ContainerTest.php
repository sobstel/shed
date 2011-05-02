<?php
namespace Shed\Tests\Container;

require_once __DIR__.'/../src/Container.php';

require_once __DIR__.'/files/classes/SampleObject.php';
require_once __DIR__.'/files/classes/SampleSingleton.php';

class ContainerTest extends \PHPUnit_Framework_TestCase
{

  protected $container;

  protected function getContainer() {
    if (!isset($this->container)) {
      $this->container = new \Shed\Container(require(__DIR__.'/files/vars.php'));
    }
    return $this->container;
  }
  
  public function testMiscAccessReferences() {
    $container = $this->getContainer();
    
    $this->assertSame(5, $container->int);
    $this->assertSame(5, $container['int']);
    $this->assertSame(5, $container->getInt());
  }

  public function testVarCanBeScalar() {
    $container = $this->getContainer();

    $this->assertSame(true, $container['bool']);
    $this->assertInternalType('bool', $container['bool']);

    $this->assertSame(5, $container['int']);
    $this->assertInternalType('int', $container['int']);

    $this->assertSame(1.2345, $container['float']);
    $this->assertInternalType('float', $container['float']);
    
    $this->assertSame('string', $container['string']);
    $this->assertInternalType('string', $container['string']);
  }

  public function testVarCanBeArray() {
    $container = $this->getContainer();

    $this->assertSame(array(1, 2, 3), $container['array']);
    $this->assertInternalType('array', $container['array']);
  }

  public function testVarCanBeObject() {
    $container = $this->getContainer();
    
    $this->assertInternalType('object', $container['object']);
    $this->assertThat($container['object'], $this->isInstanceOf('Shed\Tests\Container\SampleObject'));
  }

  public function testVarCanBeDefinedAsObject() {
    $container = $this->getContainer();

    $this->assertInternalType('object', $container['lazy_object']);
    $this->assertThat($container['lazy_object'], $this->isInstanceOf('Shed\Tests\Container\SampleObject'));
  }

  public function testVarCanBeDefinedAsObjectAndRequireFile() {
    $container = $this->getContainer();

    $this->assertInternalType('object', $container['lazy_object_file_not_loaded']);
    $this->assertThat($container['lazy_object_file_not_loaded'], $this->isInstanceOf('Shed\Tests\Container\SampleObjectNotPreloaded'));
  }

  public function testConstructorArgsCanBePassedForLazyObjects() {
    $container = $this->getContainer();

    $this->assertInternalType('object', $container['lazy_object']);

    $this->assertSame(4, $container['lazy_object']->getArg1(), 'Wrong first argument value!');
    $this->assertSame(5, $container['lazy_object']->getArg2(), 'Wrong second argument value!');
    $this->assertSame(6, $container['lazy_object_with_methods']->getArg1(), 'Wrong first argument value!');
    $this->assertSame(7, $container['lazy_object_with_methods']->getArg2(), 'Wrong second argument value!');
  }

  public function testMethodsWithArgsCanBePassedForLazyObjects() {
    $container = $this->getContainer();

    $this->assertInternalType('object', $container['lazy_object_with_methods']);
    $this->assertSame(8, $container['lazy_object_with_methods']->getArg3(), 'Wrong third argument value!');
  }

  public function testPropertiesCanBePassedForLazyObjects() {
    $container = $this->getContainer();

    $this->assertInternalType('object', $container['lazy_object_with_properties']);
    $this->assertSame(10, $container['lazy_object_with_properties']->arg4);
  }

  public function testArgCanBeTheReferenceToOtherVar() {
    $container = $this->getContainer();

    $this->assertInternalType('object', $container['lazy_object_with_ref']);
    $this->assertSame((float)1.2345, $container['lazy_object_with_ref']->getArg1(), 'Wrong first argument value!');
    $this->assertSame(5, $container['lazy_object_with_ref']->getArg3(), 'Wrong third argument value!');
  }

  public function testVarCanBeACallback() {
    $container = $this->getContainer();

    $this->assertSame((float)4, $container['callback']);
  }

  public function testObjectInCallbackCanBeDefinedAsReferenceToOtherVar() {
    $container = $this->getContainer();

    $this->assertSame(3, $container['callback_with_ref']);
  }

  public function testCallbackCanBeUsedToDefineSingleton() {
    $container = $this->getContainer();

    $this->assertInternalType('object', $container['singleton']);
    $this->isInstanceOf('TestFactoryFakeSingleton', $container['singleton']);
  }

  public function testVarCanBeSet() {
    $container = $this->getContainer();

    $this->assertSame(6, $container['int2']);
    $container['int2'] = 7;
    $this->assertSame(7, $container['int2']);
  }
    
  /**
   * @expectedException Shed\Exception
   */
  public function testThrowsExceptionOnNotExistingMethod() {
    $container = $this->getContainer();
    
    $container->notExistentMethod();
  }

}
