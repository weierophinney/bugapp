<?php
defined('APPLICATION_PATH')
    or define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
$paths = array(
    APPLICATION_PATH . '/../library', 
    get_include_path()
);
set_include_path(implode(PATH_SEPARATOR, $paths));
require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

try {
    require APPLICATION_PATH . '/bootstrap.php';
} catch (Exception $exception) {
    echo '<html><body><center>'
       . 'An exception occured while bootstrapping the application.';
    if (defined('APPLICATION_ENVIRONMENT') && APPLICATION_STATE != 'production') {
        echo '<br /><br />' . $exception->getMessage() . '<br />'
           . '<div align="left">Stack Trace:' 
           . '<pre>' . $exception->getTraceAsString() . '</pre></div>';
    }
    echo '</center></body></html>';
    exit(1);
}

$front->dispatch();
