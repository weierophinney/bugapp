<?php
require_once dirname(__FILE__) . '/Model.php';

/**
 * Comment model
 * 
 * @package   Bug
 * @copyright Copyright (C) 2008 - Present, Zend Technologies, Inc.
 * @author    Matthew Weier O'Phinney <matthew@zend.com> 
 * @license   New BSD {@link http://framework.zend.com/license/new-bsd}
 * @version   $Id: $
 */
class Bugapp_Comment extends Bugapp_Model
{
    /**
     * Primary table for operations
     * @var string
     */
    protected $_primaryTable = 'comment';

    /**
     * Columns protected from save operations
     * @var array
     */
    protected $_protectedColumns = array(
        'date_created',
        'date_deleted',
    );

    /**
     * Fetch all comments by bug ID
     * 
     * @param  int $bugId 
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function fetchCommentsByBug($bugId)
    {
        $select = $this->_getSelect()->where('bug_id = ?', $bugId);
        return $this->getTable('comment')->fetchAll($select);
    }

    /**
     * Fetch comments by user ID
     * 
     * @param  int $userId 
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function fetchCommentsByUser($userId)
    {
        $select = $this->_getSelect()->where('user_id = ?', $userId);
        return $this->getTable('comment')->fetchAll($select);
    }

    /**
     * Mark a comment as deleted
     * 
     * @param  int $id 
     * @return int
     */
    public function delete($id)
    {
        $table = $this->getTable('comment');
        $where = $table->getAdapter()->quoteInto('id = ?', $id);
        return $table->update(
            array('date_deleted' => date('Y-m-d')),
            $where
        );
    }

    /**
     * Initialize SELECT statement
     * 
     * @return Zend_Db_Table_Select
     */
    protected function _getSelect()
    {
        $table  = $this->getTable('comment');
        $select = $table->select()->where('date_deleted IS NULL')->order('date_created ASC');
        return $select;
    }
}
