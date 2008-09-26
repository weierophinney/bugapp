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

        $this->view->loginForm = $this->_helper->getForm(
            'login', 
            array(
                'method' => 'post',
                'action' => $this->view->url(
                    array(
                        'controller' => 'user',
                        'action'     => 'login',
                    ),
                    'default',
                    true
                ), 
            )
        );
        $this->view->registrationForm = $this->_helper->getForm(
            'register', 
            array(
                'method' => 'post',
                'action' => $this->view->url(
                    array(
                        'controller' => 'user',
                        'action'     => 'register',
                    ),
                    'default',
                    true
                ), 
            )
        );
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

        // Persist some identity details
        $auth->getStorage()->write($adapter->getResultRowObject(array(
            'id', 'username', 'fullname', 'email', 'date_created'
        )));

        // We're authenticated! Redirect to the user page
        $this->_helper->redirector('view');
    }

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->redirector('index');
    }

    public function viewAction()
    {
        $user = Zend_Auth::getInstance()->getIdentity();
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
        $id = $this->_helper->getModel('user')->save($form->getValues());
        if (!is_numeric($id)) {
            // Failure to insert
            throw new Exception('Unexpected error inserting new user');
        }

        // Authenticate and persist user identity
        $userRow = $this->_helper->getModel('user')->fetchUser($id);
        $user = array(
            'id'           => $userRow->id,
            'username'     => $userRow->username,
            'fullname'     => $userRow->fullname,
            'email'        => $userRow->email,
            'date_created' => $userRow->date_created,
        );
        Zend_Auth::getInstance()->getStorage()->write((object) $user);

        $this->_helper->redirector('view');
    }

    public function getAuthAdapter($values)
    {
        if (null === $this->_authAdapter) {
            $this->_authAdapter = new Zend_Auth_Adapter_DbTable(
                Zend_Db_Table_Abstract::getDefaultAdapter(),
                'user',
                'username',
                'password',
                '? AND (date_banned IS NULL)'
            );
        }
        $this->_authAdapter->setIdentity($values['username']);
        $this->_authAdapter->setCredential(md5($values['password']));
        return $this->_authAdapter;
    }
}
