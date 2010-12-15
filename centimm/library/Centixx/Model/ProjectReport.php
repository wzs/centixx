<?php

class Centixx_Model_ProjectReport extends Centixx_Model_Abstract {

	/**
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected $_db;

	protected $_resourceType = 'report';

	protected $_project;

	public function __construct($options = null)
	{
		if (is_array($options)) {
			$this->setOptions($options);
		}

		$this->_db = Zend_Registry::get('db');
	}

	public function save()
	{
	}

	public function delete()
	{
	}

	/**
	 * @param Centixx_Model_Project $project
	 * @return Centixx_Model_ProjectReport provides fluent interface
	 */
	public function setProject($project)
	{
		$this->_project = $project;
		return $this;
	}

	/**
	 * @return Centixx_Model_Project
	 */
	public function getProject($raw = false)
	{
		if (is_int($this->_project) && $raw == false) {
			$this->_project = Centixx_Model_Mapper_Project::factory()->find($this->_project);
		}

		return $this->_project;
	}

	/**
	 * (non-PHPdoc)
	 * @see library/Centixx/Model/Centixx_Model_Abstract::_customAclAssertion()
	 */
	protected function _customAclAssertion($role, $privilage = null)
	{
		if ($role instanceof Centixx_Model_User) {

			//CEO ma dostep do wszystkich raportów w firmie
			if ($role->getRole() == Centixx_Acl::ROLE_CEO) {
				return self::ASSERTION_SUCCESS;
			}

			//szef działu ma dostep do raportów projektów, które są w jego dziale
			if ($role->getRole() == Centixx_Acl::ROLE_DEPARTMENT_CHIEF
				&& $this->project->department->manager->id == $role->id
			) {
				return self::ASSERTION_SUCCESS;
			}

			//szef projektu ma dostep do raportu dot. swojego projektu
			if ($role->getRole() == Centixx_Acl::ROLE_PROJECT_MANAGER
				&& $this->project->manager->id == $role->id
			) {
				return self::ASSERTION_SUCCESS;
			}
		}
		return parent::_customAclAssertion($role, $privilage);
	}

	public function getCashData() {

		$result = $this->_db->query(
            "SELECT CONCAT(user_name, ' ', user_surname) as user, SUM(timesheet_hours * user_hour_rate) as cash ".
			'FROM timesheets, users, projects ' .
			'WHERE timesheet_project = project_id ' .
			'AND timesheet_user = user_id ' .
			'AND project_id = ?' .
			'GROUP BY user_id ' , $this->project->id
        );


		$data = array();
		while ($row = $result->fetch()) {
    		$data[] = array($row->user, (int)$row->cash);
		}
		return $data;
	}

	public function getTimeData() {

		$result = $this->_db->query(
            "SELECT CONCAT(user_name, ' ', user_surname) AS user, SUM(timesheet_hours) as time " .
			'FROM timesheets, users, projects ' .
			'WHERE timesheet_project = project_id ' .
			'AND timesheet_user = user_id ' .
			'AND project_id = ? ' .
			'GROUP BY user_id', $this->project->id
        );

		$data = array();
		while ($row = $result->fetch()) {
    		$data[] = array($row->user, (int)$row->time);
		}

		return $data;
	}

	/**
	 * Renderuje opcje (tylko opcje!) do wykresu zestawiającego koszt danego projektu
	 * @param string $title tytuł wykresu
	 * @param string $renderTo id elementu w którym zostanie wyrenderowany wykresy
	 */
	public function renderCash($title, $renderTo)
	{
		$options = array(
			'renderTo' => $renderTo,
			'title' => $title,
		);
		return $this->_renderChartOptions($options, $this->getCashData());
	}

	/**
	 * Renderuje opcje (tylko opcje!) do wykresu zestawiającego czas pracy użytkowników w danym projekcie
	 * @param string $title tytuł wykresu
	 * @param string $renderTo id elementu w którym zostanie wyrenderowany wykresy
	 */
	public function renderTime($title, $renderTo)
	{
		$options = array(
			'renderTo' => $renderTo,
			'title' => $title,
		);
		return $this->_renderChartOptions($options, $this->getTimeData());
	}

	/**
	 * Generuje kod JSON, który można wstawić do konstruktora obiektu chart w JS
	 *
	 * @param array $options opcje
	 * @param array $serie dane do wygenerowania wykresu
	 */
	protected function _renderChartOptions($options, $serie)
	{

		$out = array(
			'chart' => array(
				'renderTo' 	=> $options['renderTo'],
			),
			'title' => array(
				'text' => $options['title'],
			),
			'plotArea' => array(
				'shadow' 			=> null,
				'borderWidth' 		=> null,
				'backgroundColor' 	=> null,
			),
			'tooltip' => array(
			),
			'plotOptions' => array(
				'pie' => array(
					'allowPointSelect' => true,
					'cursor' => 'pointer',
					'dataLabels'  =>  array(
						'enabled' => true,
						'color' => '#000000',
						'connectorColor'  => '#000000',
					),
				),
			),
			'series' => array(
				array(
					'type' => 'pie',
					'name' => 'Browser share',
					'data' => $serie,
				),
			),
		);

		echo json_encode($out);
	}

}