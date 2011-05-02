<?php
namespace Shed\Tests\Container;

class SampleObject
{

	protected $arg1, $arg2, $arg3 = 3;
  
  public $arg4;

	public function __construct($arg1 = 1, $arg2 = 2)
	{
		$this->arg1 = $arg1;
		$this->arg2 = $arg2;
	}

	public function setArg3($arg3)
	{
		$this->arg3 = $arg3;
	}

	public function getArg1()
	{
		return $this->arg1;
	}

	public function getArg2()
	{
		return $this->arg2;
	}

	public function getArg3()
	{
		return $this->arg3;
	}

}
