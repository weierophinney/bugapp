<?php
require_once dirname(__FILE__) . '/Bug.php';

class Bugapp_DbTable_ResolutionType extends Zend_Db_Table
{
    protected $_name    = 'resolution_type';
    protected $_primary = 'id';

    protected $_dependentTables = array(
        'Bugapp_DbTable_Bug',
    );
}
