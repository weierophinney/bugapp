<?php
class ErrorController extends Zend_Controller_Action
{
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
                $this->view->code    = 404;
                $this->view->message = 'Page not found';
                break;
            default:
                // application error
                $this->getResponse()->setRawHeader('HTTP/1.1 500 Application Error');
                $this->view->code    = 500;
                $this->view->message = 'Application error';
                $this->view->info    = $errors->exception;
                break;
        }
    }
}
