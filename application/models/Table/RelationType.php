<?php
require_once dirname(__FILE__) . '/BugRelation.php';

class Model_Table_RelationType extends Zend_Db_Table
{
    protected $_name    = 'relation_type';
    protected $_primary = 'id';

    protected $_dependentTables = array(
        'Model_Table_BugRelation',
    );
}
