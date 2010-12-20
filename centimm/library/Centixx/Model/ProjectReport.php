<?php

class Centixx_Model_ProjectReport extends Centixx_Model_Abstract {

	//typy wykresów
	const TYPE_CASH 		= 'cash';
	const TYPE_TIME 		= 'time';
	const TYPE_DAILYTIME 	= 'dailytime';

	private $_chartTypes = array(
		self::TYPE_CASH,
		self::TYPE_TIME,
		self::TYPE_DAILYTIME,
	);

	/**
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected $_db;

	protected $_resourceType = 'report';

	/**
	 * @var Centixx_Model_Project
	 */
	protected $_project;

	protected $_type = self::TYPE_DAILYTIME;

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
	 * Ustawia typ wykresu
	 * @param string $type patrz zdefiniowane stałe
	 * @throws Centixx_Model_Exception
	 * @return Centixx_Model_ProjectReport
	 */
	public function setType($type)
	{
		if (!in_array($type, $this->_chartTypes)) {
			throw new Centixx_Model_Exception('Invalid parameter type');
		}
		$this->_type = $type;
		return $this;
	}

	/**
	 * @return string typ wykresu
	 */
	public function getType()
	{
		return $this->_type;
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
			if ($role->hasRole(Centixx_Acl::ROLE_CEO)) {
				return self::ASSERTION_SUCCESS;
			}

			//szef działu ma dostep do raportów projektów, które są w jego dziale
			if ($role->hasRole(Centixx_Acl::ROLE_DEPARTMENT_CHIEF) && $this->project->department->manager->id !== $role->id) {
				return self::ASSERTION_SUCCESS;
			}

			//szef projektu ma dostep do raportu dot. swojego projektu
			if ($role->hasRole(Centixx_Acl::ROLE_PROJECT_MANAGER) && $this->project->manager->id !== $role->id
			) {
				return self::ASSERTION_SUCCESS;
			}
		}
		return parent::_customAclAssertion($role, $privilage);
	}

	protected function getCashData() {

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

	protected function getTimeData() {

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
	 * Zwraca dane dotyczące czasu pracy przy danym projekcie pogrupowane wg dnia
	 */
	protected function getDailyTimeData()
	{
		$result = $this->_db->query(
			"SELECT UNIX_TIMESTAMP(timesheet_date) AS date, SUM( timesheet_hours ) AS time
			FROM `timesheets`
			WHERE timesheet_project = ?
			GROUP BY timesheet_date
			ORDER BY timesheet_date ASC",
			$this->project->id
        );

        //uzupelniam daty pośrednie
		$data = array();
		$dateStart = $this->getProject()->getDateStart(true);
		$dateEnd = new Zend_Date();

		//inicjuje pusta tablice z dniami trwania projektu
		$tmpDate = $dateStart;
		while($dateEnd->compare($tmpDate) != -1) {
			$day = date('Y-m-d', $tmpDate->getTimestamp());
			$data[$day] = array($day, 0);
			$tmpDate->addDay(1);
		};
		//wypelniam tablice pobranymi danymi
		while ($row = $result->fetch()) {
    		$day = date('Y-m-d', $row->date);
			$data[$day] = array($day, (int)$row->time);
		}

		return ($data);
	}

	/**
	 * Zwraca opcje JSON do wyświetlenia wykresu za pomocą HighCharts
	 * Typ generowanewgo wykresu oraz zakres danych jest zalezny od typu (ustawionego za pomocą setType)

	 * @param string $renderTo id html'owego elementu, na którym zostanie wygenerowany wykres
	 * @return string json encoded
	 */
	public function render($renderTo)
	{
		return call_user_func_array(array($this, 'render' . ucfirst($this->_type)), array($renderTo));
	}

	public function getJsFormatterName()
	{
		$formatters = array(
			self::TYPE_CASH => 'cashFormatter',
			self::TYPE_TIME => 'timeFormatter',
			self::TYPE_DAILYTIME => 'timeFormatter',
		);
		return $formatters[$this->_type];
	}

	protected function renderDailyTime($renderTo)
	{
		$data = $this->getDailyTimeData();

		$options = array(
			'chart' => array(
				'renderTo' 	=> $renderTo,
			),
			'title' => array(
				'text' => 'Zestawienie dzienne projektu ' . $this->project->name,
			),
			'series' => array(
				array(
					'type' => 'area',
					'name' => 'Liczba przepracowanych godzin',
					'data' => array_values($data),
				),
			),
			'xAxis' => array(
				'categories' => array_keys($data),
				'labels' => array(
					'rotation' => '65',
					'y' => '100',
				),
			),
			'yAxis' => array(
				'title' => 'Liczba godzin',
			),
		);

		return $this->_renderChartOptions($options);
	}

	/**
	 * Renderuje opcje (tylko opcje!) do wykresu zestawiającego koszt danego projektu
	 * @param string $title tytuł wykresu
	 * @param string $renderTo id elementu w którym zostanie wyrenderowany wykresy
	 */
	protected function renderCash($renderTo)
	{
		$options = array(
			'chart' => array(
				'renderTo' 	=> $renderTo,
			),
			'title' => array(
				'text' => 'Zestawienie kosztowe projektu ' . $this->project->name,
			),
			'series' => array(
				array(
					'type' => 'pie',
					'name' => 'Koszt projektu',
					'data' => $this->getCashData(),
				),
			),
		);

		return $this->_renderChartOptions($options);
	}

	/**
	 * Renderuje opcje (tylko opcje!) do wykresu zestawiającego czas pracy użytkowników w danym projekcie
	 * @param string $title tytuł wykresu
	 * @param string $renderTo id elementu w którym zostanie wyrenderowany wykresy
	 */
	protected function renderTime($renderTo)
	{
		$options = array(
			'chart' => array(
				'renderTo' 	=> $renderTo,
			),
			'title' => array(
				'text' => 'Zestawienie czasowe projektu ' . $this->project->name,
			),
			'series' => array(
				array(
					'type' => 'pie',
					'name' => 'Czas pracy',
					'data' => $this->getTimeData(),
				),
			),
		);

		return $this->_renderChartOptions($options);
	}

	/**
	 * Generuje kod JSON, który można wstawić do konstruktora obiektu chart w JS
	 *
	 * @param array $options opcje
	 * @return string json encoded
	 */
	protected function _renderChartOptions($options)
	{
		$defaults = array(
			'chart' => array(
				'renderTo' 	=> '',
			),
			'title' => array(
				'text' => '',
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
				'line' => array(
					'dataLabels' => array(
						'enabled' => true,
					),
					'enableMouseTracking' => true,
				),
				'area' => array(
		            'stacking' => 'normal',
            		'lineColor' => '#666666',
            		'lineWidth' => 1,
            		'marker' => array(
               			'lineWidth' => 1,
               			'lineColor' => '#666666',
					),
               ),
			),
			'series' => array(
				array(
					'type' => '',
					'name' => '',
					'data' => '',
				),
			),
		);

		echo json_encode(array_merge_recursive_distinct($defaults, $options));
	}

}