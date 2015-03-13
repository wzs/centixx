<?php
class Application_Form_User_Edit extends Zend_Form
{
	/**
	 * @var Centixx_Model_User
	 */
	protected $_user;

	/**
	 * @var array<Centixx_Model_Department>
	 */
	protected $_departments;

	/**
	 * @var array<Centixx_Model_Role>
	 */
	protected $_roles;

	public function rebuild()
	{
		$this->setMethod(self::METHOD_POST);
		$this->setAttrib('id', 'user-edit');

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

		$dbValidatorOptions = array(
			'table' => 'users',
			'field' => 'user_email',
		);

		if ($this->_user->id) {
			$dbValidatorOptions['exclude'] = array(
				'field' => 'user_id',
				'value' => $this->_user->id
			);
		}

		$this->addElement('text', 'email', array(
			'label'		=> 'E-mail',
			'required'	=> true,
			'validators'=> array(
				new Zend_Validate_EmailAddress(),
				new Zend_Validate_Db_NoRecordExists($dbValidatorOptions),
			),
		));

		$hourRate = $this->addElement('text', 'hourRate', array(
			'label' => 'Stawka',
			'filters' => array(
				new Zend_Filter_LocalizedToNormalized(array('precision' => 2)),
			),
			'validators' => array(
				new Zend_Validate_Float(),
			),
			'required' => true,
			'errorMessages'  => array('Wprowadź prawidłową stawkę'),
		));

		$this->addElement('text', 'account', array(
			'label' => 'Nr konta',
          	'required' => true,
			'errorMessages'  => array('Wprowadź prawidłowy nr konta'),
		));

		//dla nowego uzytkownika wyswietlane jest pole password
		if (!$this->_user->id) {
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
		}

		$this->addElement('textarea', 'address', array(
			'label' => 'Adres',
			'attribs' => array(
				'rows' => 2,
				'cols' => 30,
			),
		));

		if ($this->_roles) {
			$this->addElement('multiCheckbox', 'roles', array(
				'label' => 'Stanowisko',
				'multiOptions' => $this->_roles,
			));
		}
		//var_dump($this->_roles);

		if ($this->_departments) {
			$this->addElement('select', 'department', array(
				'label' => 'Szef działu',
				'multiOptions' => array('' => '--') + $this->_departments,
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
		if ($passwordElement = $this->getElement('password_retype')) {
			$passwordElement->addValidator(new Zend_Validate_Identical($data['password']));
		}

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

		if (array_key_exists('departments', $array)) {
			$this->_departments = $array['departments'];
		}

		if (array_key_exists('roles', $array)) {
			$this->_roles = $array['roles'];
		}

		$this->rebuild();
		return $this;
	}
}