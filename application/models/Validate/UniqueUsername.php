<?php
class Bugapp_Validate_UniqueUsername extends Zend_Validate_Abstract
{
    const USER_EXISTS = 'userExists';

    protected $_messageTemplates = array(
        self::USER_EXISTS => 'User identified by "%value%" already exists in our system',
    );

    protected $_model;

    public function __construct(Bugapp_User $model)
    {
        $this->_model = $model;
    }

    public function isValid($value, $context = null)
    {
        $this->_setValue($value);

        $user = $this->_model->fetchUser($value);
        if (null === $user) {
            return true;
        }

        $this->_error(self::USER_EXISTS);
        return false;
    }
}
