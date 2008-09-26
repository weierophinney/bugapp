<?php
require_once dirname(__FILE__) . '/BugRelation.php';

class Bugapp_DbTable_RelationType extends Zend_Db_Table
{
    protected $_name    = 'relation_type';
    protected $_primary = 'id';

    protected $_dependentTables = array(
        'Bugapp_DbTable_BugRelation',
    );
}
