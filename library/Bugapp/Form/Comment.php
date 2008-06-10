<?php
class Bugapp_Form_Comment extends Zend_Form
{
    public function init()
    {
        $comment = $this->addElement('textarea', 'comment', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Comment:',
        ));

        $userId = $this->addElement('hidden', 'user_id', array(
            'filters'    => array('StringTrim'),
            'validators' => array('Int'),
            'required'   => true,
        ));

        $bugId = $this->addElement('hidden', 'bug_id', array(
            'filters'    => array('StringTrim'),
            'validators' => array('Int'),
            'required'   => true,
        ));

        $submit = $this->addElement('submit', 'submit', array(
            'required' => false,
            'ignore'   => true,
            'label'    => 'Submit Comment',
        ));
    }
}
