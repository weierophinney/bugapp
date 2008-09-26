<?php
require_once dirname(__FILE__) . '/Bug.php';
require_once dirname(__FILE__) . '/Comment.php';

class Bugapp_DbTable_User extends Zend_Db_Table
{
    protected $_name    = 'user';
    protected $_primary = 'id';

    protected $_dependentTables = array(
        'Bugapp_DbTable_Bug',
        'Bugapp_DbTable_Comment',
    );
}
