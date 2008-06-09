<?php
class Model_Table_Bug extends Zend_Db_Table
{
    protected $_name    = 'bug';
    protected $_primary = 'id';

    protected $_dependentTables = array(
        'Model_Table_BugRelation',
        'Model_Table_Comment',
    );

    protected $_referenceMap = array(
        'IssueType' => array(
            'columns'       => 'type_id',
            'refTableClass' => 'Model_Table_IssueType',
            'refColumns'    => 'id',
        ),
        'ResolutionType' => array(
            'columns'       => 'resolution_id',
            'refTableClass' => 'Model_Table_ResolutionType',
            'refColumns'    => 'id',
        ),
        'PriorityType' => array(
            'columns'       => 'priority_id',
            'refTableClass' => 'Model_Table_PriorityType',
            'refColumns'    => 'id',
        ),
    );
}
