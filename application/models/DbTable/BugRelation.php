<?php
require_once dirname(__FILE__) . '/Bug.php';
require_once dirname(__FILE__) . '/RelationType.php';

class Bugapp_DbTable_BugRelation extends Zend_Db_Table
{
    protected $_name    = 'bug_relation';
    protected $_primary = 'id';

    protected $_referenceMap = array(
        'BugChildren' => array(
            'columns'       => 'related_id',
            'refTableClass' => 'Bugapp_DbTable_Bug',
            'refColumns'    => 'id',
        ),
        'BugParent' => array(
            'columns'       => 'bug_id',
            'refTableClass' => 'Bugapp_DbTable_Bug',
            'refColumns'    => 'id',
        ),
        'RelationType' => array(
            'columns'       => 'relation_type',
            'refTableClass' => 'Bugapp_DbTable_RelationType',
            'refColumns'    => 'id',
        ),
    );
}
