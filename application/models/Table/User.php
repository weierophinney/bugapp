<?php
class Model_Table_User extends Zend_Db_Table
{
    protected $_name    = 'user';
    protected $_primary = 'id';

    protected $_dependentTables = array(
        'Model_Table_Bug',
        'Model_Table_Comment',
    );
}
