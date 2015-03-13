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
		if($this->_currentUser->hasRole(Centixx_Acl::ROLE_CEO)) {
			$this->view->deny = '0';
		} else {
			$this->view->deny = '1';
		}
	}

	/**
	 * Moja wersja wyświetlania raportów
	 * @throws Centixx_Acl_AuthenticationException
	 * @author Paweł Wrzosek
	 */
	public function index2Action()
	{
		//TODO ograniczyć
    	$this->view->projects = Centixx_Model_Mapper_Abstract::factory('project')->fetchAll();
	}

	public function showAction()
	{
		$this->appendScript(array('js/highcharts.js', 'js/yetii.js'));
		$this->appendStyles(array(
			'styles/white.css',
			'styles/custom.css',
		));

    	$report = new Centixx_Model_ProjectReport();

    	$projectId = $this->getRequest()->getParam('id');
    	$reportType = $this->getRequest()->getParam('type', Centixx_Model_ProjectReport::TYPE_DAILYTIME);


    	$project = Centixx_Model_Mapper_Abstract::factory('project')->find($projectId);
    	$report->setProject($project);
    	$report->setType($reportType);

    	if (!$report->isAllowed($this->_currentUser, Centixx_Model_Abstract::ACTION_READ)) {
    		throw new Centixx_Acl_AuthenticationException('Nieuprawniony dostęp');
    	}

    	$this->view->report = $report;
	}
}
