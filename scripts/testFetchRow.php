<?php
defined('APPLICATION_PATH') 
    or define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
defined('APPLICATION_STATE') 
    or define('APPLICATION_STATE', 'development');
$paths = array(
    '.',
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
);
set_include_path(implode(PATH_SEPARATOR, $paths));
require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

require_once APPLICATION_PATH . '/plugins/Initialize.php';
require_once APPLICATION_PATH . '/models/DbTable/Bug.php';

$init = new Bugapp_Plugin_Initialize(APPLICATION_STATE, APPLICATION_PATH);
$init->initDb();

$table = new Bugapp_DbTable_Bug();
// $row = $table->fetchRow($table->select()->where('id = ?', 1));
$row = $table->fetchAll();
var_export($row->toArray());
