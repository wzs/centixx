<?php
class Centixx_Model_Reports {
	
	protected $_db;
	
	public function __construct() { 
		$this->_db = Zend_Registry::get('db');
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