<?php
class Application_Form_Auth_Login extends Zend_Form
{
	public function init()
	{
		$this->setMethod(self::METHOD_POST);
		$this->setAttrib('id', 'loginForm');

		$this->addElement('text', 'email', array(
			'label'		=> 'E-mail',
			'required'	=> true,
			'validators'=> array(
				new Zend_Validate_EmailAddress()
			),
		));

		$this->addElement('password', 'password', array(
			'label'		=> 'Hasło',
			'required'	=> true,
		));

		$this->addelement('submit', 'submit', array(
			'label'		=> 'Zaloguj się',
			'ignore'	=> true,
		));
	}
}