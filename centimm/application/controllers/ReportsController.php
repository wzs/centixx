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
		
		$reports = new Centixx_Model_Reports();
		
		$this->view->chartProjectsCashData = $reports->chartProjectsCashData();
		$this->view->chartProjectsTimeData = $reports->chartProjectsTimeData();
		$this->view->chartOverallTime = $reports->chartOverallTime();
		$this->view->chartOverallCash = $reports->chartOverallCash();
		$this->view->chartOverallTime_end = $reports->chartOverallTime_end();
		$this->view->chartOverallCash_end = $reports->chartOverallCash_end();
		
		if($this->_currentUser->getRole() == Centixx_Acl::ROLE_DEPARTMENT_CHIEF) {
			$this->view->deny = '1';
		} else {
			$this->view->deny = '0';
		}
	}
	
	protected function baseUrl() {
        $protocol = $_SERVER['HTTPS'] ? 'https' : 'http';
        $server = $_SERVER['HTTP_HOST'];
        $port = $_SERVER['SERVER_PORT'] != 80 ? ":{$_SERVER['SERVER_PORT']}" : '';
        $path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/';             
        return "$protocol://$server$port$path";
    }

}
