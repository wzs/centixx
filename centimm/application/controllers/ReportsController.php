<?php
class ReportsController extends Centixx_Controller_Action {

	public function indexAction() {

		$this->appendScript(array('js/highcharts.js', 'js/yetii.js'));
		$this->appendStyles(array(
			'styles/white.css',
			'styles/custom.css',
		));

		$reports = new Centixx_Model_Reports();

		$this->view->chartProjectsCashData = $reports->chartProjectsCashData();
		$this->view->chartProjectsTimeData = $reports->chartProjectsTimeData();
		$this->view->chartOverallTime = $reports->chartOverallTime();
		$this->view->chartOverallCash = $reports->chartOverallCash();
		$this->view->chartOverallTime_end = $reports->chartOverallTime_end();
		$this->view->chartOverallCash_end = $reports->chartOverallCash_end();

		//raport zbiorczy tylko dla CEO
		if($this->_currentUser->getRole() == Centixx_Acl::ROLE_DEPARTMENT_CHIEF) {
			$this->view->deny = '1';
		} else {
			$this->view->deny = '0';
		}
	}

	public function showAction()
	{
		$this->appendScript(array('js/highcharts.js', 'js/yetii.js'));
		$this->appendStyles(array(
			'styles/white.css',
			'styles/custom.css',
		));

    	$report = new Centixx_Model_ProjectReport();
    	$report->setProject(Centixx_Model_Mapper_Project::factory()->find($this->getRequest()->getParam('id')));

    	$this->view->report = $report;
	}
}
