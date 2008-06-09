<?php
/**
 * Database creation.
 *
 * Many versions of sqlite are incompatible with the version shipped with PHP; 
 * this script should be used in such a situation to generate your SQLite 
 * database from the provided schema.
 */
$paths = array(
    '.',
    realpath(dirname(__FILE__) . '/../library'),
    get_include_path(),
);
set_include_path(implode(PATH_SEPARATOR, $paths));
require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

$schemaFile = dirname(__FILE__) . '/../misc/schema/bugs.sql';
$schema     = file_get_contents($schemaFile);
$statements = explode(';', $schema);

$db     = Zend_Db::factory('pdo_sqlite', array('dbname' => dirname(__FILE__) . '/../data/db/bugs.db'));
$conn   = $db->getConnection();
$filter = new Zend_Filter_StringTrim;
foreach ($statements as $statement) {
    $statement = $filter->filter($statement);
    $conn->exec($statement);
}
