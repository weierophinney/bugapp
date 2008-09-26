<?php
class Bugapp_Form_Register extends Zend_Form
{
    public function init()
    {
        require_once APPLICATION_PATH . '/models/User.php';

        $this->addElementPrefixPath('Bugapp_Validate', APPLICATION_PATH . '/models/Validate/', 'validate');

        $username = $this->addElement('text', 'username', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'Alpha',
                array('StringLength', true, array(3, 20)),
                array('UniqueUsername', false, array(new Bugapp_User())),
            ),
            'required'   => true,
            'label'      => 'Desired username:',
        ));

        $fullname = $this->addElement('text', 'fullname', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', false, array(3, 60)),
            ),
            'required'   => false,
            'label'      => 'Your full name:',
        ));

        $email = $this->addElement('text', 'email', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('EmailAddress', true),
                array('UniqueUsername', true, array(new Bugapp_User())),
            ),
            'required'   => false,
            'label'      => 'Your email address:',
        ));

        $password = $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'Alnum',
                array('StringLength', false, array(6, 20)),
            ),
            'required'   => true,
            'label'      => 'Password:',
        ));

        $passwordVerification = $this->addElement('password', 'passwordVerification', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'PasswordVerification'
            ),
            'required'   => true,
            'label'      => 'Password Verification:',
        ));

        $register = $this->addElement('submit', 'register', array(
            'required' => false,
            'ignore'   => true,
            'label'    => 'Register',
        ));
    }
}
