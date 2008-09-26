<?php
class Zend_View_Helper_Comments extends Zend_View_Helper_Abstract
{
    public function comments(Zend_Db_Table_Row_Abstract $bug)
    {
        $comments = $bug->findDependentRowset('Bugapp_DbTable_Comment');
        if (0 == count($comments)) {
            return '';
        }

        $html = '';
        foreach ($comments as $comment) {
            $user  = $comment->findParentRow('Bugapp_DbTable_User');
            $html .= '<div class="comment">'
                  .  '<h4>Reported by <a href="/user/view/id/' . $user->id . '">' . $user->fullname . '</a>'
                  .  ' on ' . date('Y-m-d', strtotime($comment->date_created)) . '</h4>'
                  .  '<p>' . $this->view->escape($comment->comment) . '</p>'
                  .  '</div>';
        }
        return $html;
    }
}
