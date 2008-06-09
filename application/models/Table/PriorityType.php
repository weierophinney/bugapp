<?php
class Model_Table_PriorityType extends Zend_Db_Table
{
    protected $_name    = 'priority_type';
    protected $_primary = 'id';

    protected $_dependentTables = array(
        'Model_Table_Bug',
    );
}
