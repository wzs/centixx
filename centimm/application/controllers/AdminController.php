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

	public function makeBackupAction()
	{
		//TODO sprawdzic uprawnienia - stworzyc model?
		if ($this->getRequest()->isPost()) {
			 
			$dumpDir = APPLICATION_PATH . '/../data/dump/';
			$dumpFile = date('Ymd-His'). '-dump.sql';
			
			$dbParams = $this->_config['resources']['db']['params'];
			 
			exec("mysqldump --user={$dbParams['username']} --password={$dbParams['password']} {$dbParams['dbname']} > " . $dumpDir . $dumpFile);
			//TODO trzeba sprawdzic czy plik sie poprawnie utworzyl
			
			$this->_logger->log("{$this->_currentUser} wykonał kopię bazy danych", Centixx_Log::CENTIXX);
			$this->_flashMessenger->addMessage("Utworzono kopię bazy danych");
		}
		$this->_redirect('/admin');
	}
}

