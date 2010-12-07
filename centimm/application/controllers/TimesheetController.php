<?php
class TimesheetController extends Centixx_Controller_Action
{
	public function indexAction()
	{
		$timesheet = new stdClass();
		$timesheet->user = $this->_currentUser;
		$timesheet->totalTime = rand(120, 160);
		
		$this->view->timesheet = $timesheet; 
	}
}