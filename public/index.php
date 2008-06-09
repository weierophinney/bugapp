<?php
$root  = realpath(dirname(__FILE__) . '/../');
$paths = array('.', $root . '/library', get_include_path());
set_include_path(implode(PATH_SEPARATOR, $paths));
require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

$front = Zend_Controller_Front::getInstance();
$front->registerPlugin(new Bugapp_Plugin_Initialize('development', $root));
$front->dispatch();
