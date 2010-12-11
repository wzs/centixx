<?php
class Centixx_Model_Timesheet extends Centixx_Model_Abstract
{
	protected $_resourceType = 'timesheet';
	protected $_data;

	protected $_criteria = array();

	public function getDay(DateTime $date) {
		return;
	}

	public function getUser() {
		return;
	}

	public function getProject() {
		return;
	}

	public function setData($data) {
		$this->_data = $data;
	}

	public function getData($data) {
		return $this->_data;
	}
}