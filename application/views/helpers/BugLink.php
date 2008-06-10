<?php
class Zend_View_Helper_BugLink
{
    public $view;

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    public function bugLink($id, $summary)
    {
        return '<a href="/bug/view/id/' . $id . '">' . $this->view->escape($summary) . '</a>';
    }
}
