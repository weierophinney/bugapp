<?php
class Zend_View_Helper_OpenBugs
{
    public $view;

    protected $_model;

    protected $_validUserTypes = array('developer', 'reporter');

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    public function openBugs($userId = null, $userType = null)
    {
        if (!in_array($userType, $this->_validUserTypes)) {
            $userType = 'developer';
        }

        if (null === $userId) {
            $bugs = $this->getModel()->fetchOpenBugs();
        } elseif ('reporter' == $userType) {
            $bugs = $this->getModel()->fetchOpenBugsByReporter($userId);
        } else {
            $bugs = $this->getModel()->fetchOpenBugsByDeveloper($userId);
        }

        $html = "<ul class=\"buglist\">\n";
        if (0 == count($bugs)) {
            $html .= "<li><b>No open bugs</b></li>";
        } else {
            foreach ($bugs as $bug) {
                $html .= '<li>'
                    .  $this->view->bugLink($bug->id, $bug->summary)
                    .  "</li>\n";
            }
        }
        $html .= "</ul>\n";
        return $html;
    }

    public function getModel()
    {
        if (null === $this->_model) {
            require_once dirname(__FILE__) . '/../../models/Bug.php';
            $this->_model = new Model_Bug();
        }
        return $this->_model;
    }
}
