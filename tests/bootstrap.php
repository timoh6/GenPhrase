<?php
chdir(__DIR__);
error_reporting(E_ALL | E_STRICT);

// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') && class_exists('\PHPUnit_Framework_TestCase'))
{
	class_alias('\PHPUnit_Framework_TestCase', 'PHPUnit\Framework\TestCase');
}

require_once '../library/GenPhrase/Loader.php';
$loader = new GenPhrase\Loader('GenPhrase');
$loader->register();
