<?php
namespace Shed\Tests\Container;

class SampleSingleton
{

	static $instance;

	private function __construct()
	{
	}

	static public function get()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new self();
		}
		return self::$instance;
	}

}
