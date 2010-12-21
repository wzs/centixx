<?php
class Application_Form_Timesheet_Edit extends Application_Form_Abstract //Zend_Form
{
	/**
	 * @var Centixx_Model_User
	 */
	protected $_projects;

	protected $_date;

	protected $_user;
	protected $_id;

	public function rebuild()
	{
		$this->setMethod(self::METHOD_POST);

		$this->addElement('hidden', 'id', array(
			//'value' => $this->_id,
		));

		if ($this->_user)
			$this->addElement('hidden', 'user', array(
				'value' => $this->_user,
				'required'	=> true,
			));


		if ($this->_date)
			$this->addElement('hidden', 'date', array(
				'value' => $this->_date,
				'required'	=> true,
			));

		//var_dump($this->_projects);

		if ($this->_projects)
			$this->addElement('select', 'project', array(
				'label' => 'Projekt',
				'required'	=> true,
				'multiOptions' => $this->_projects,
			));

		$this->addElement('text', 'hours', array(
			'label'		=> 'Czas pracy',
			'validators' => array(
				new Zend_Validate_Int(),
			),
			'required'	=> true,
			'maxLength' => 2,
			'errorMessages'  => array('Niepoprawna wartość'),
		));

		$this->addElement('text', 'descr', array(
			'label'		=> 'Opis zadań',
			'required'	=> true,
			//'maxLength' => 64,
			'errorMessages'  => array('Niepoprawna wartość'),
		));

		$this->addElement('submit', 'submit', array(
			'label'		=> 'Zapisz',
			'ignore'	=> true,
		));

		/*
		$dbValidatorOptions = array(
			'table' => 'users',
			'field' => 'user_email',
		);

		if ($this->_user) {
			$dbValidatorOptions['exclude'] = array(
				'field' => 'user_id',
				'value' => $this->_user->id
			);
		}
		*/
		/*
		$this->addElement('text', 'email', array(
			'label'		=> 'E-mail',
			'required'	=> true,
			'validators'=> array(
				new Zend_Validate_EmailAddress(),
				new Zend_Validate_Db_NoRecordExists($dbValidatorOptions),
			),
		));

		$this->addElement('text', 'hourRate', array(
			'label' => 'Stawka',
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

		if ($this->_roles) {
			$this->addElement('select', 'role', array(
				'label' => 'Stanowisko',
				'multiOptions' => $this->_roles,
			));
		}

		$this->addElement('submit', 'submit', array(
			'label'		=> 'Zapisz',
			'ignore'	=> true,
		));
		*/
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


		return parent::isValid($data);
	}

	/**
	 * Ustawia parametry formularza
	 * @param array $options
	 * @return Application_Form_User_Edit fluent interface
	 */
	public function setValues($array) {
		//if (array_key_exists('id', $array))
		//	$this->_user = $array['id'];

		if (array_key_exists('user', $array))
			$this->_user = $array['user'];

		if (array_key_exists('date', $array))
			$this->_date = $array['date'];

		if (array_key_exists('projects', $array))
			$this->_projects = $array['projects'];


		$this->rebuild();
		return $this;
	}
}
