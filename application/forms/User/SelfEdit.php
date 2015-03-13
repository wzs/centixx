<?php
/**
 * Fromularz do edycji własnego konta
 * @author wzs
 */
class Application_Form_User_SelfEdit extends Zend_Form
{
	/**
	 * @var Centixx_Model_User
	 */
	protected $_user;

	public function rebuild()
	{
		$this->setMethod(self::METHOD_POST);

		$this->addElement('hidden', 'user_id', array(
			'value' => $this->_user->id,
		));

		$this->addElement('text', 'email', array(
			'label'		=> 'E-mail',
			'required'	=> true,
			'validators'=> array(
				new Zend_Validate_EmailAddress(),
			),
			'errorMessages'  => array(
				'Wprowadź prawidłowy email',
			),
		));

		$this->addElement('password', 'password', array(
			'label' => 'Hasło',
			'validators' => array(
				array('StringLength', false, array('min' => 3)),
			),
		));

		$this->addElement('password', 'password_retype', array(
			'label' => 'Powtórz hasło',
          	'errorMessages'  => array('Podane hasła nie są identyczne'),
		));


		$this->addElement('submit', 'submit', array(
			'label'		=> 'Zapisz',
			'ignore'	=> true,
		));
	}

	/**
	 * (non-PHPdoc)
	 * @see library/Zend/Zend_Form::isValid()
	 */
	public function isValid($data)
	{
		//nadpisałem, bo nie mogelm zmusic Zend_Validate_Identical do dzialania
		$this->getElement('password_retype')
			->addValidator(new Zend_Validate_Identical($data['password']));
		return parent::isValid($data);
	}

	/**
	 * Ustawia parametry formularza
	 * @param array $options
	 * @return Application_Form_User_Edit fluent interface
	 */
	public function setValues($array) {
		if (array_key_exists('user', $array)) {
			$this->_user = $array['user'];
		}

		$this->rebuild();
		return $this;
	}
}