<?php
class ReportsController extends Centixx_Controller_Action {
	
	public function indexAction() {
		$this->view->headScript()->appendFile(
    		'js/jquery.js',
    		'text/javascript',
			array()
		);
		$this->view->headScript()->appendFile(
    		'js/highcharts.js',
    		'text/javascript',
			array()
		);
		$this->view->headScript()->appendFile(
    		'js/yetii.js',
    		'text/javascript',
			array()
		);
		
		$this->chartOverallTime();
		$this->chartOverallCash();
		
	}
	
	protected function chartOverallTime() {
		$db = $this->_db;
		
		$result = $db->query(
            'SELECT project_name, (SELECT SUM(timesheet_hours) FROM timesheets WHERE timesheet_project = project_id) as hours '.
			'FROM `projects` WHERE project_stop < NOW()'
        );
        
		$data = array();
		while ($row = $result->fetch()) {
    		$data[$row->project_name] = (int)$row->hours;
		}
		
		$this->view->chartOverallTime = $data;
	}
	
	protected function chartOverallCash() {
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
		
		$this->view->chartOverallCash = $data;
	}
	
}
