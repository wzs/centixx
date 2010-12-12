<?php
class ReportsController extends Centixx_Controller_Action {
	
	public function indexAction() {
		$this->view->headScript()->appendFile(
    		$this->baseUrl() . 'js/jquery.js',
    		'text/javascript',
			array()
		);
		$this->view->headScript()->appendFile(
    		$this->baseUrl() . 'js/highcharts.js',
    		'text/javascript',
			array()
		);
		$this->view->headScript()->appendFile(
    		$this->baseUrl() . 'js/yetii.js',
    		'text/javascript',
			array()
		);
		
		$this->view->headLink()->appendStylesheet($this->baseUrl() . 'styles/white.css');
		$this->view->headLink()->appendStylesheet($this->baseUrl() . 'styles/custom.css');
		
		$this->chartOverallTime();
		$this->chartOverallCash();
		
		$this->chartOverallTime_end();
		$this->chartOverallCash_end();
		
	}
	
	protected function baseUrl() {
        $protocol = $_SERVER['HTTPS'] ? 'https' : 'http';
        $server = $_SERVER['HTTP_HOST'];
        $port = $_SERVER['SERVER_PORT'] != 80 ? ":{$_SERVER['SERVER_PORT']}" : '';
        $path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/';             
        return "$protocol://$server$port$path";
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
	
	protected function chartOverallTime_end() {
		$db = $this->_db;
		
		$result = $db->query(
            'SELECT project_name, (SELECT SUM(timesheet_hours) FROM timesheets WHERE timesheet_project = project_id) as hours '.
			'FROM `projects` WHERE project_stop > NOW()'
        );
        
		$data = array();
		while ($row = $result->fetch()) {
    		$data[$row->project_name] = (int)$row->hours;
		}
		
		$this->view->chartOverallTime_end = $data;
	}
	
	protected function chartOverallCash_end() {
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
		
		$this->view->chartOverallCash_end = $data;
	}
	
}
