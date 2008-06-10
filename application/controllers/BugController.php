<?php
class BugController extends Zend_Controller_Action
{
    public function preDispatch()
    {
        $authenticated = Zend_Auth::getInstance()->hasIdentity();
        $action        = $this->getRequest()->getActionName();
        if (!$authenticated && in_array($action, array('comment', 'add', 'add-process'))) {
            return $this->_forward('list');
        } elseif ($authenticated) {
            $this->userId = Zend_Auth::getInstance()->getIdentity()->id;
        }
    }

    public function indexAction()
    {
        return $this->listAction();
    }

    public function listAction()
    {
        $developer = $this->_getParam('developer', '');
        $reporter  = $this->_getParam('reporter', '');
        $status    = $this->_getParam('status', 'open');

        $status = ucfirst(strtolower($status));
        if (!in_array($status, array('Open', 'Resolved', 'Closed'))) {
            $status = 'Open';
        }

        $method = 'fetch' . $status . 'Bugs';
        if ('' != $developer) {
            $user    = $this->_helper->getModel('user')->fetchUser($developer);
            if (null !== $user) {
                $method .= 'ByDeveloper';
                $bugs    = $this->_helper->getModel('bug')->$method($developer);
                $this->view->listType = $status . ' bugs owned by ' . $user->username;
            }
        } elseif ('' != $reporter) {
            $user    = $this->_helper->getModel('user')->fetchUser($reporter);
            if (null !== $user) {
                $method .= 'ByReporter';
                $bugs    = $this->_helper->getModel('bug')->$method($developer);
                $this->view->listType = $status . ' bugs reported by ' . $user->username;
            }
        } 

        if (!isset($bugs)) {
            $bugs    = $this->_helper->getModel('bug')->$method();
            $this->view->listType = $status . ' bugs';
        }

        $this->view->bugs = $bugs;
    }

    public function viewAction()
    {
        if (!($id = $this->_getParam('id', false))) {
            return $this->_helper->redirector('list');
        }

        $bug = $this->_helper->getModel('bug')->fetchBug($id);
        if (null === $bug) {
            return $this->render('not-found');
        }

        $commentForm = $this->getCommentForm();
        $commentForm->bug_id->setValue($bug->id);
        $commentForm->user_id->setValue($this->userId);
        $this->view->bug = $bug;
    }

    public function addAction()
    {
        $form = $this->getBugForm();
    }

    public function processAddAction()
    {
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return $this->_helper->redirector('list');
        }

        $form = $this->getBugForm();
        if (!$form->isValid($request->getPost())) {
            // failed
            return $this->render('add');
        }

        $values = $form->getValues();
        $values['reporter_id'] = Zend_Auth::getInstance()->getIdentity()->id;
        $model = $this->_helper->getModel('bug');
        $id = $model->save($values);
        if (null === $id) {
            throw new Exception('Unexpected error saving bug');
        }

        $this->_helper->redirector('view', 'bug', 'default', array('id' => $id));
    }

    public function commentAction()
    {
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return $this->_helper->redirector('list');
        }

        if (!($bugId = $this->_getParam('bug_id'))) {
            return $this->_helper->redirector('list');
        }

        $form = $this->getCommentForm();
        if (!$form->isValid($request->getPost())) {
            $request->setParam('id', $bugId);
            return $this->viewAction();
        }

        $model = $this->_helper->getModel('bug');
        $id = $model->save($form->getValues());
        if (null === $id) {
            throw new Exception('Unexpected error saving bug');
        }

        $this->_helper->redirector('view', 'bug', 'default', array('id' => $id));
    }

    public function getBugForm()
    {
        if (!isset($this->view->bugForm)) {
            $this->view->bugForm  = $this->_helper->getForm(
                'bug', 
                array('action' => '/bug/process-add', 'method' => 'post')
            );
        }
        return $this->view->bugForm;
    }

    public function getCommentForm()
    {
        if (!isset($this->view->commentForm)) {
            $this->view->commentForm  = $this->_helper->getForm(
                'comment', 
                array('action' => '/bug/comment', 'method' => 'post')
            );
            $userId = $this->view->commentForm->user_id;
            $userId->addValidator('Identical', true, array($this->userId));
        }
        return $this->view->commentForm;
    }
}
