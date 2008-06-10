<?php
class Zend_View_Helper_Comments
{
    public $view;

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    public function comments(Zend_Db_Table_Row_Abstract $bug)
    {
        $comments = $bug->findDependentRowset('Model_Table_Comment');
        if (0 == count($comments)) {
            return '';
        }

        $html = '';
        foreach ($comments as $comment) {
            $user  = $comment->findParentRow('Model_Table_User');
            $html .= '<div class="comment">'
                  .  '<h4>Reported by <a href="/user/view/id/' . $user->id . '">' . $user->fullname . '</a>'
                  .  ' on ' . date('Y-m-d', strtotime($comment->date_created)) . '</h4>'
                  .  '<p>' . $this->view->escape($comment->comment) . '</p>'
                  .  '</div>';
        }
        return $html;
    }
}
