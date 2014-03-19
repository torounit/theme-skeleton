<?php
/**
 *
 * Abstract Singlton Class.
 *
 * Base script http://d.hatena.ne.jp/Yudoufu/20090811/1250021010
 *
 * @link http://d.hatena.ne.jp/Yudoufu/20090811/1250021010
 *
 * */

Abstract Class Singleton
{
	private static $instance = array();

	final private function __construct()
	{
		if (isset(self::$instance[get_called_class()]))
		{
			throw new Exception('You can not create more than one copy of a singleton.');
		}
		static::initialize();
	}

	abstract protected function initialize(); # ここでコンストラクタの初期化実装

	final public static function getInstance()
	{
		$class = get_called_class();
		if (!isset(self::$instance[$class]))
		{
			self::$instance[$class] = new static();
		}
		return self::$instance[$class];
	}

	final private function __clone()
	{
		throw new Exception('You can not clone a singleton.');
	}
}
?>