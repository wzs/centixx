<?php

class AdminController extends Centixx_Controller_Action
{
	public function indexAction()
	{
		$this->view->logs = $this->_getLogs();
	}

	/**
	 * Do AJAXowego wczytawania logów
	 */
	public function getLogsAction()
	{
		if ($this->_isAjaxRequest) {
			echo json_encode($this->_getLogs());
		}
	}

	protected function _getLogs()
	{
		$logFile = $this->_config['resources']['log']['writerParams']['stream'];

		//odwracam kolejnosc, tak aby najnowsze logi były na górze
		$lines = array_reverse(file($logFile));

		$formatted = array();
		foreach ($lines as $line) {
			$log = Centixx_Log::parseLog($line);
			$formatted[] = $log['date'] . "\t" . $log['user'] . "\t" . $log['actionName'] . "\t" . $log['message'];
		}

		return $formatted;
	}

	public function makeBackupAction()
	{
		if ($this->getRequest()->isPost() || $this->_isAjaxRequest) {

			$dumpDir = APPLICATION_PATH . '/../data/dump/';
			$dumpFile = date('Ymd-His'). '-dump.sql';

			$dbParams = $this->_config['resources']['db']['params'];

			exec("mysqldump --user={$dbParams['username']} --password={$dbParams['password']} {$dbParams['dbname']} > " . $dumpDir . $dumpFile);

			//ustawiam, aby nikt poza adminem nie mial dostepu do pliku
			exec("chmod 0770 " . $dumpDir . $dumpFile);

			$this->log(Centixx_Log::DB_COPY_CREATED);
			$this->addFlashMessage("Utworzono kopię bazy danych");

			if ($this->_isAjaxRequest) {
				echo json_encode($dumpFile);
			}
		}

		$this->_forward('index');
	}

	/**
	 * Czyści logi systemowe
	 */
	public function clearLogsAction()
	{
		if ($this->getRequest()->isPost() || $this->_isAjaxRequest) {

			$logFile = $this->_config['resources']['log']['writerParams']['stream'];
			$backupFile = dirname($logFile) . '/backups/' . date('Ymd-His'). '.centixx.log.zip';

			exec("zip $backupFile $logFile; cat /dev/null > $logFile");

			$this->log(Centixx_Log::LOG_CLEARED);
			$this->addFlashMessage("Logi zostały wyczyszczone");

			if ($this->_isAjaxRequest) {
				echo json_encode(true);
			}
		}

		$this->_forward('index');
	}
}

