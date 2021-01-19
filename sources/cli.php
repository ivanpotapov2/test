<?php
namespace application;

error_reporting(E_ALL);

ini_set('display_errors', 1);

//require_once dirname(__DIR__) . '/vendor/autoload.php';
include('application/Core.php');
$core = new Core;
if (!empty($argv[1])) {
    $loader = $core->getLoader();
    $loader->load($argv[1]);
} else {
    echo 'USE: cli.php %path/to/file%' . PHP_EOL;
}
