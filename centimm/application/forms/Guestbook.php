<?php

class Application_Form_Guestbook extends Zend_Form
{

    public function init()
    {
    	$this->setMethod(self::METHOD_POST);

    	$this->addElement('text', 'email', array(
    		'label'			=> 'Twój email',
    		'requried'		=>	true,
    		'filters'		=> array('StringTrim'),
    		'validators'	=> array('EmailAddress'),
    	));

    	$this->addElement('textarea', 'comment', array(
    		'label'			=> 'tReSc k0omC1a!',
    		'required'		=> true,
    		'validators'	=> array(
    			array('StringLength', 'options' => array(0,20)),
    		),
    	));

    	$this->addElement('captcha', 'captcha', array(
    		'label'			=> 'Przepisz to',
    		'required'		=> true,
    		'captcha'		=> array(
    			'captcha'		=> 'Figlet',
    			'wordLen'		=> 2,
    			'timeout'		=> 300,
    		),
    	));

    	$this->addElement('submit', 'submit', array(
    		'ignore'	=> true,
    		'label'		=> 'Dodaj!',
    	));

    	$this->addElement('hash', 'csrf', array('ignore'	=> true));
    }


}

