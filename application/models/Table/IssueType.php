<?php
require_once dirname(__FILE__) . '/Bug.php';

class Model_Table_IssueType extends Zend_Db_Table
{
    protected $_name    = 'issue_type';
    protected $_primary = 'id';

    protected $_dependentTables = array(
        'Model_Table_Bug',
    );
}
