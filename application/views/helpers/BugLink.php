<?php
class Zend_View_Helper_BugLink extends Zend_View_Helper_Abstract
{
    public function bugLink($id, $summary)
    {
        return '<a href="/bug/view/id/' . $id . '">' . $this->view->escape($summary) . '</a>';
    }
}
