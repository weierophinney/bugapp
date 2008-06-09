<?php
/**
 * Bug application model
 * 
 * @package   Bug
 * @copyright Copyright (C) 2008 - Present, Zend Technologies, Inc.
 * @author    Matthew Weier O'Phinney <matthew@zend.com> 
 * @license   New BSD {@link http://framework.zend.com/license/new-bsd}
 * @version   $Id: $
 */
class Model_Bug
{
    /**
     * @var Zend_Loader_PluginLoader
     */
    protected $_loader;

    /**
     * @var array Sort orders
     */
    protected $_sortOrder = array();

    /**
     * @var array Table instances
     */
    protected $_tables    = array();

    /**
     * Get plugin loader
     * 
     * @return Zend_Loader_PluginLoader
     */
    public function getPluginLoader()
    {
        if (null === $this->_loader) {
            $this->_loader = new Zend_Loader_PluginLoader();
            $this->_loader->addPrefixPath('Model_Table', dirname(__FILE__) . '/Table/');
        }
        return $this->_loader;
    }

    /**
     * Get table class
     * 
     * @param  string $name 
     * @return Zend_Db_Table_Abstract
     */
    public function getTable($name)
    {
        $name  = ucfirst($name);
        if (!array_key_exists($name, $this->_tables)) {
            $class = $this->getPluginLoader()->load($name);
            $this->_tables[$name] = new $class;
        }
        return $this->_tables[$name];
    }

    /**
     * Fetch a bug by ID
     * 
     * @param  int $id 
     * @return Zend_Db_Table_Row|null
     */
    public function fetchBug($id)
    {
        $select = $this->_getSelect();
        $select->where('id = ?', $id);
        return $this->getTable('bug')->fetchRow($select);
    }

    /**
     * Set sort order for returning results
     * 
     * @param  string $field 
     * @param  string $direction 
     * @return Model_Bug
     */
    public function setSortOrder($field, $direction)
    {
        $this->_sortOrder = array(array($field . ' ' . $direction));
        return $this;
    }

    /**
     * Add a sort order for returning results
     * 
     * @param  string $field 
     * @param  string $direction 
     * @return Model_Bug
     */
    public function addSortOrder($field, $direction)
    {
        $this->_sortOrder[] = array($field . ' ' . $direction);
        return $this;
    }

    /**
     * @todo Add bug
     * @todo Update bug
     * @todo Link bugs
     * @todo Delete bug
     */

    /**
     * Insert or update a bug
     * 
     * @param array $bugInfo 
     * @return void
     */
    public function save(array $bugInfo)
    {
        $table = $this->getTable('bug');
        $bugId = null;
        $row   = null;
        if (array_key_exists('bug_id', $bugInfo)) {
            $bugId = $bugInfo['bug_id'];
            unset($bugInfo['bug_id']);
            $matches = $table->find($bugId);
            if (0 < count($matches)) {
                $row = $matches->current();
            }
        }
        if (null === $row) {
            $row = $table->createRow();
        }

        $columns = $table->info('cols');
        foreach ($columns as $column) {
            if (array_key_exists($column, $bugInfo)) {
                $row->$column = $bugInfo[$column];
            }
        }

        $bugId = $row->save();
    }

    /**
     * Link one bug to another
     * 
     * @param  int $originalBug 
     * @param  int $linkedBug 
     * @param  int $linkType 
     * @return true
     */
    public function link($originalBug, $linkedBug, $linkType)
    {
        $table = $this->getTable('BugRelation');
        $select = $table->select();
        $select->where('bug_id = ?', $originalBug)
               ->where('related_id = ?', $linkedBug);
        $links = $table->fetchAll($select);
        if (count($links) > 0) {
            $link = $links->current();
            if ($link->relation_type != $linkType) {
                $link->relation_type = $linkType;
                $link->save();
            }
            return true;
        }

        $select = $table->select();
        $select->where('bug_id = ?', $linkedBug)
               ->where('related_id = ?', $originalBug);
        $links = $table->fetchAll($select);
        if (count($links) > 0) {
            $link = $links->current();
            if ($link->relation_type != $linkType) {
                $link->relation_type = $linkType;
                $link->save();
            }
            return true;
        }

        $data = array(
            'bug_id'        => $originalBug,
            'related_id'    => $linkedBug,
            'relation_type' => $linkType,
        );
        $table->insert($data);
        return true;
    }

    /**
     * Delete a bug
     * 
     * @param  int $bugId 
     * @return int Number of rows updated
     */
    public function delete($bugId)
    {
        $table = $this->getTable('bug');
        $where = $table->getAdapter()->quoteInto('id = ?', $bugId);
        return $table->update(array('date_closed' => date('Y-m-d')), $where);
    }

    /**
     * Get bug types as assoc array
     * 
     * @return array
     */
    public function getTypes()
    {
        $table   = $this->getTable('IssueType');
        $adapter = $table->getAdapter();
        return $adapter->fetchPairs('select id, type FROM issue_type');
    }

    /**
     * Get bug resolutions as assoc array
     * 
     * @return array
     */
    public function getResolutions()
    {
        $table   = $this->getTable('ResolutionType');
        $adapter = $table->getAdapter();
        return $adapter->fetchPairs('select id, resolution FROM resolution_type');
    }

    /**
     * Get bug priorities as assoc array
     * 
     * @return array
     */
    public function getPriorities()
    {
        $table   = $this->getTable('PriorityType');
        $adapter = $table->getAdapter();
        return $adapter->fetchPairs('select id, priority FROM priority_type');
    }

    /**
     * Fetch an individual bug by id
     * 
     * @param  int $id 
     * @return Zend_Db_Table_Row_Abstract|null
     */
    public function fetchBug($id)
    {
        $select = $this->_getSelect();
        $select->where('id = ?', $id)
               ->where('date_deleted IS NULL');
        return $this->getTable('bug')->fetchOne($select);
    }

    /**
     * Fetch all open bugs
     * 
     * @param  int|null $limit 
     * @param  int|null $offset 
     * @return Zend_Db_Table_Rowset|null
     */
    public function fetchOpenBugs($limit = null, $offset = null)
    {
        $select = $this->_getSelect();
        $select->where('date_closed IS NULL');
        $select = $this->_setLimit($select, $limit, $offset);
        $this->_setLimit($select, $limit, $offset)
             ->_setSort($select);
        return $this->getTable('bug')->fetchAll($select);
    }

    /**
     * Fetch all closed bugs
     * 
     * @param  int|null $limit 
     * @param  int|null $offset 
     * @return Zend_Db_Table_Rowset|null
     */
    public function fetchClosedBugs($limit = null, $offset = null)
    {
        $select = $this->_getSelect();
        $select->where('date_closed IS NOT NULL');
        $select = $this->_setLimit($select, $limit, $offset);
        $this->_setLimit($select, $limit, $offset)
             ->_setSort($select);
        return $this->getTable('bug')->fetchAll($select);
    }

    /**
     * Fetch all resolved bugs
     * 
     * @param  int|null $limit 
     * @param  int|null $offset 
     * @return Zend_Db_Table_Rowset|null
     */
    public function fetchResolvedBugs($limit = null, $offset = null)
    {
        $select = $this->_getSelect();
        $select->where('resolution_id > 2')
               ->where('date_closed IS NULL');
        $select = $this->_setLimit($select, $limit, $offset);
        $this->_setLimit($select, $limit, $offset)
             ->_setSort($select);
        return $this->getTable('bug')->fetchAll($select);
    }

    /**
     * Fetch open bugs by reporter ID
     * 
     * @param  int $reporterId 
     * @param  int|null $limit 
     * @param  int|null $offset 
     * @return Zend_Db_Table_Rowset|null
     */
    public function fetchOpenBugsByReporter($reporterId, $limit = null, $offset = null)
    {
        $select = $this->_getSelect();
        $select->where('date_closed IS NULL')
               ->where('reporter_id = ?', $reporterId);
        $select = $this->_setLimit($select, $limit, $offset);
        $this->_setLimit($select, $limit, $offset)
             ->_setSort($select);
        return $this->getTable('bug')->fetchAll($select);
    }

    /**
     * Fetch resolved bugs by reporter
     * 
     * @param  int $reporterId 
     * @param  int|null $limit 
     * @param  int|null $offset 
     * @return Zend_Db_Table_Rowset|null
     */
    public function fetchResolvedBugsByReporter($reporterId, $limit = null, $offset = null)
    {
        $select = $this->_getSelect();
        $select->where('resolution_id > 2')
               ->where('date_closed IS NULL')
               ->where('reporter_id = ?', $reporterId);
        $select = $this->_setLimit($select, $limit, $offset);
        $this->_setLimit($select, $limit, $offset)
             ->_setSort($select);
        return $this->getTable('bug')->fetchAll($select);
    }

    /**
     * Fetch closed bugs by reporter ID
     * 
     * @param  int $reporterId 
     * @param  int|null $limit 
     * @param  int|null $offset 
     * @return Zend_Db_Table_Rowset|null
     */
    public function fetchClosedBugsByReporter($reporterId, $limit = null, $offset = null)
    {
        $select = $this->_getSelect();
        $select->where('date_closed IS NOT NULL')
               ->where('reporter_id = ?', $reporterId);
        $select = $this->_setLimit($select, $limit, $offset);
        $this->_setLimit($select, $limit, $offset)
             ->_setSort($select);
        return $this->getTable('bug')->fetchAll($select);
    }

    /**
     * Fetch open bugs by developer
     * 
     * @param  int $developerId 
     * @param  int|null $limit 
     * @param  int|null $offset 
     * @return Zend_Db_Table_Rowset|null
     */
    public function fetchOpenBugsByDeveloper($developerId, $limit = null, $offset = null)
    {
        $select = $this->_getSelect();
        $select->where('date_closed IS NULL')
               ->where('developer_id = ?', $developerId);
        $select = $this->_setLimit($select, $limit, $offset);
        $this->_setLimit($select, $limit, $offset)
             ->_setSort($select);
        return $this->getTable('bug')->fetchAll($select);
    }

    /**
     * Fetch resolved bugs by developer ID
     * 
     * @param  int $developerId 
     * @param  int|null $limit 
     * @param  int|null $offset 
     * @return Zend_Db_Table_Rowset|null
     */
    public function fetchResolvedBugsByDeveloper($developerId, $limit = null, $offset = null)
    {
        $select = $this->_getSelect();
        $select->where('resolution_id > 2')
               ->where('date_closed IS NULL')
               ->where('developer_id = ?', $developerId);
        $select = $this->_setLimit($select, $limit, $offset);
        $this->_setLimit($select, $limit, $offset)
             ->_setSort($select);
        return $this->getTable('bug')->fetchAll($select);
    }

    /**
     * Fetch closed bugs by developer ID
     * 
     * @param  int $developerId 
     * @param  int|null $limit 
     * @param  int|null $offset 
     * @return Zend_Db_Table_Rowset|null
     */
    public function fetchClosedBugsByDeveloper($developerId, $limit = null, $offset = null)
    {
        $select = $this->_getSelect();
        $select->where('date_closed IS NOT NULL')
               ->where('developer_id = ?', $developerId);
        $select = $this->_setLimit($select, $limit, $offset);
        $this->_setLimit($select, $limit, $offset)
             ->_setSort($select);
        return $this->getTable('bug')->fetchAll($select);
    }

    /**
     * Get select statement for bug table
     * 
     * @return Zend_Db_Table_Select
     */
    protected function _getSelect()
    {
        $bugTable = $this->getTable('bug');
        $select   = $bugTable->select()->setIntegrityCheck(false);
        $select->from(array('b' => 'bug'))
               ->joinLeft(array('i' => 'issue_type'), 'i.id = b.type_id', array('issue_type' => 'type'))
               ->joinLeft(array('r' => 'resolution_type'), 'r.id = b.resolution_id', array('resolution'))
               ->joinLeft(array('p' => 'priority_type'), 'p.id = b.priority_id', array('priority'))
               ->where('date_deleted IS NULL'); // never fetch deleted bugs
        return $select;
    }

    /**
     * Set limit and offset on select object
     * 
     * @param  Zend_Db_Table_Select $select 
     * @param  int|null $limit 
     * @param  int|null $offset 
     * @return Model_Bug
     */
    protected function _setLimit(Zend_Db_Table_Select $select, $limit, $offset)
    {
        if (null !== $limit) {
            if (null === $offset) {
                $offset = 0;
            }

            $select->limit((int) $limit, (int) $offset);
        }
        return $this;
    }

    /**
     * Set sort order on select object
     * 
     * @param  Zend_Db_Table_Select $select 
     * @return Model_Bug
     */
    protected function _setSort(Zend_Db_Table_Select $select)
    {
        if (!empty($this->_sortOrder)) {
            foreach ($this->_sortOrder as $sortSpec) {
                $select->order($sortSpec);
            }
        }
        return $this;
    }
}
