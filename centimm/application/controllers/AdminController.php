<?php

class AdminController extends Centixx_Controller_Action
{
    public function indexAction()
    {
		$this->view->logs = $this->_getLogs();
    }
    
    protected function _getLogs()
    {
		$logFile = $this->_config['resources']['log']['writerParams']['stream'];
		$logs = file($logFile);
		foreach ($logs as &$log) {
			$log = trim($log);		
		}
		
		return $logs;
    }
}

