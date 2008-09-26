<?php
require_once dirname(__FILE__) . '/Bug.php';

class Bugapp_DbTable_IssueType extends Zend_Db_Table
{
    protected $_name    = 'issue_type';
    protected $_primary = 'id';

    protected $_dependentTables = array(
        'Bugapp_DbTable_Bug',
    );
}
