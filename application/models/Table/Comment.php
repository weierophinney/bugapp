<?php
require_once dirname(__FILE__) . '/User.php';
require_once dirname(__FILE__) . '/Bug.php';

class Model_Table_Comment extends Zend_Db_Table
{
    protected $_name    = 'comment';
    protected $_primary = 'id';

    protected $_referenceMap = array(
        'User' => array(
            'columns'       => 'user_id',
            'refTableClass' => 'Model_Table_User',
            'refColumns'    => 'id',
        ),
        'Bug' => array(
            'columns'       => 'bug_id',
            'refTableClass' => 'Model_Table_Bug',
            'refColumns'    => 'id',
        ),
    );
}
