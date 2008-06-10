<?php
class UserController extends Zend_Controller_Action
{
    protected $_authAdapter;
    protected $_forms = array();
    protected $_model;

    public function preDispatch()
    {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            if (!in_array($this->getRequest()->getActionName(), array('logout', 'view'))) {
                $this->_helper->redirector('view');
            }
        } else {
            if (!in_array($this->getRequest()->getActionName(), array('index', 'register', 'login'))) {
                $this->_helper->redirector('index');
            }
        }

        $this->view->loginForm        = $this->getForm('login');
        $this->view->registrationForm = $this->getForm('register');
    }

    public function indexAction()
    {
    }

    public function loginAction()
    {
        $request = $this->getRequest();

        // Check if we have a POST request
        if (!$request->isPost()) {
            return $this->_helper->redirector('index');
        }

        // Get our form and validate it
        $form = $this->view->loginForm;
        if (!$form->isValid($request->getPost())) {
            // Invalid entries
            return $this->render('index'); // re-render the login form
        }

        // Get our authentication adapter and check credentials
        $adapter = $this->getAuthAdapter($form->getValues());
        $auth    = Zend_Auth::getInstance();
        $result  = $auth->authenticate($adapter);
        if (!$result->isValid()) {
            // Invalid credentials
            $form->setDescription('Invalid credentials provided');
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
        $identity = Zend_Auth::getInstance()->getIdentity();
        $user = $this->getModel()->fetchUser($identity);
        $this->view->identity = $identity;
        $this->view->user = $user;
    }

    public function registerAction()
    {
        $request = $this->getRequest();

        // Check if we have a POST request
        if (!$request->isPost()) {
            return $this->_helper->redirector('index');
        }

        // Get our form and validate it
        $form = $this->view->registrationForm;
        if (!$form->isValid($request->getPost())) {
            // Invalid entries
            return $this->render('index'); // re-render the login form
        }

        // Valid form
        $id = $this->getModel()->save($form->getValues());
        if (!is_numeric($id)) {
            // Failure to insert
            throw new Exception('Unexpected error inserting new user');
        }

        $this->_helper->redirector('view');
    }

    public function getForm($type = 'login')
    {
        $class = 'Bugapp_Form_' . ucfirst($type);
        if (!array_key_exists($class, $this->_forms)) {
            $this->_forms[$class] = new $class(array(
                'action' => '/user/' . strtolower($type),
                'method' => 'post',
            ));
        }
        return $this->_forms[$class];
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

    public function getModel()
    {
        if (null === $this->_model) {
            require_once dirname(__FILE__) . '/../models/User.php';
            $this->_model = new Model_User;
        }
        return $this->_model;
    }
}
