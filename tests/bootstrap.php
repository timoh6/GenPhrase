<?php
chdir(__DIR__);
error_reporting(E_ALL | E_STRICT);

require_once '../library/GenPhrase/Loader.php';
$loader = new GenPhrase\Loader('GenPhrase');
$loader->register();
