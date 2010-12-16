<?php
class Centixx_Model_Reports extends Centixx_Model_Abstract {

	protected $_db;

	protected $_resourceType = 'report';

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

			//TODO chcemy taka mozliwosc
			//trzeba zrobić aby raport dotyczył TYLKO jednego projektu
			//i miał na niego referencje
/*
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
*/


		}
		return parent::_customAclAssertion($role, $privilage);
	}

	public function chartProjectsCashData() {
		$db = $this->_db;

		$result = $db->query(
            'SELECT project_name, user_name, user_surname, SUM(timesheet_hours*user_hour_rate) as cash '.
			'FROM timesheets, users, projects WHERE '.
			'timesheet_project = project_id AND timesheet_user = user_id GROUP BY project_name, user_surname'
        );

		$data = array();
		while ($row = $result->fetch()) {
    		$data[$row->project_name] .= $row->user_name.' '.$row->user_surname.','.$row->cash.';';
		}

		return $data;
	}

	public function chartProjectsTimeData() {
		$db = $this->_db;

		$result = $db->query(
            'SELECT project_name, user_name, user_surname, SUM(timesheet_hours) as time '.
			'FROM timesheets, users, projects WHERE '.
			'timesheet_project = project_id AND timesheet_user = user_id GROUP BY project_name, user_surname'
        );

		$data = array();
		while ($row = $result->fetch()) {
    		$data[$row->project_name] .= $row->user_name.' '.$row->user_surname.','.$row->time.';';
		}

		return $data;
	}

	public function chartOverallTime() {
		$db = $this->_db;

		$result = $db->query(
            'SELECT project_name, (SELECT SUM(timesheet_hours) FROM timesheets WHERE timesheet_project = project_id) as hours '.
			'FROM `projects` WHERE project_stop < NOW()'
        );

		$data = array();
		while ($row = $result->fetch()) {
    		$data[$row->project_name] = (int)$row->hours;
		}

		return $data;
	}

	public function chartOverallCash() {
		$db = $this->_db;

		$result = $db->query(
            'SELECT project_name, (SELECT SUM(timesheet_hours*user_hour_rate) '.
			'FROM timesheets, users WHERE timesheet_project = project_id AND timesheet_user = user_id) as cash '.
			'FROM `projects` WHERE project_stop < NOW()'
        );

		$data = array();
		while ($row = $result->fetch()) {
    		$data[$row->project_name] = (int)$row->cash;
		}

		return $data;
	}

	public function chartOverallTime_end() {
		$db = $this->_db;

		$result = $db->query(
            'SELECT project_name, (SELECT SUM(timesheet_hours) FROM timesheets WHERE timesheet_project = project_id) as hours '.
			'FROM `projects` WHERE project_stop > NOW()'
        );

		$data = array();
		while ($row = $result->fetch()) {
    		$data[$row->project_name] = (int)$row->hours;
		}

		return $data;
	}

	public function chartOverallCash_end() {
		$db = $this->_db;

		$result = $db->query(
            'SELECT project_name, (SELECT SUM(timesheet_hours*user_hour_rate) '.
			'FROM timesheets, users WHERE timesheet_project = project_id AND timesheet_user = user_id) as cash '.
			'FROM `projects` WHERE project_stop > NOW()'
        );

		$data = array();
		while ($row = $result->fetch()) {
    		$data[$row->project_name] = (int)$row->cash;
		}

		return $data;
	}

}