<?php
class Application_Form_User_Add extends Zend_Form
{
	/**
	 * @var Centixx_Model_User
	 */
	protected $_user;

	protected $_roles;

	public function rebuild()
	{
		$this->setMethod(self::METHOD_POST);

		$this->addElement('hidden', 'user_id', array(
			'value' => $this->_user->id,
		));

		$this->addElement('text', 'name', array(
			'label'		=> 'Imię',
			'required'	=> true,
			'maxLength' => 64,
			'errorMessages'  => array('Imię jest wymagane'),
		));

		$this->addElement('text', 'surname', array(
			'label'		=> 'Nazwisko',
			'required'	=> true,
			'maxLength' => 64,
			'errorMessages'  => array('Nazwisko jest wymagane'),
		));

		$this->addElement('text', 'email', array(
			'label'		=> 'E-mail',
			'required'	=> true,
			'validators'=> array(
				new Zend_Validate_EmailAddress()
			),
			'errorMessages'  => array('Wprowadź prawidłowy email'),
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

		if ($this->_roles) {
			$this->addElement('select', 'role', array(
				'label' => 'Stanowisko (! bez sprawdzania narazie)',
				'multiOptions' => $this->_roles,
			));
		}

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
//		$this->getElement('password_retype')
//			->addValidator(new Zend_Validate_Identical($data['password']));
//		return parent::isValid($data);
		return true;
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

		if (array_key_exists('roles', $array)) {
			$this->_roles = $array['roles'];
		}

		$this->rebuild();
		return $this;
	}
}