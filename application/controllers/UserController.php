<?php
class UserController extends Zend_Controller_Action
{
    protected $_authAdapter;
    protected $_forms = array();

    public function preDispatch()
    {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            if ('logout' != $this->getRequest()->getActionName()) {
                $this->_helper->redirector('index', 'index');
            }
        } else {
            if ('logout' == $this->getRequest()->getActionName()) {
                $this->_helper->redirector('login');
            }
        }
    }

    public function indexAction()
    {
        $this->view->form = $this->getForm();
    }

    public function loginAction()
    {
        $request = $this->getRequest();

        // Check if we have a POST request
        if (!$request->isPost()) {
            return $this->_helper->redirector('index');
        }

        // Get our form and validate it
        $form = $this->getForm();
        if (!$form->isValid($request->getPost())) {
            // Invalid entries
            $this->view->form = $form;
            return $this->render('index'); // re-render the login form
        }

        // Get our authentication adapter and check credentials
        $adapter = $this->getAuthAdapter($form->getValues());
        $auth    = Zend_Auth::getInstance();
        $result  = $auth->authenticate($adapter);
        if (!$result->isValid()) {
            // Invalid credentials
            $form->setDescription('Invalid credentials provided');
            $this->view->form = $form;
            return $this->render('index'); // re-render the login form
        }

        // We're authenticated! Redirect to the home page
        $this->_helper->redirector('index', 'index');
    }

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->redirector('index');
    }

    public function viewAction()
    {
    }

    public function registerAction()
    {
    }

    public function getForm($type = 'login')
    {
        $type = 'Bugapp_Form_' . ucfirst($type);
        if (!array_key_exists($type, $this->_forms)) {
            $this->_forms[$type] = new $type(array(
                'action' => '/user/login',
                'method' => 'post',
            ));
        }
        return $this->_forms[$type];
    }

    public function getAuthAdapter($values)
    {
        if (null === $this->_authAdapter) {
            $this->_authAdapter = new Zend_Auth_Adapter_DbTable(
                Zend_Db_Table_Abstract::getDefaultAdapter(),
                'user',
                'username',
                'password'
            );
        }
        $this->_authAdapter->setIdentity($values['username']);
        $this->_authAdapter->setCredential(md5($values['password']));
        return $this->_authAdapter;
    }
}
