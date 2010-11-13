<?php

class Application_Model_GuestbookMapper
{
	/**
	 * @var Zend_Db_Table_Abstract
	 */
	protected $_dbTable = null;

	/**
	 * Set Table Gateway
	 * @param string|Zend_Db_Table_Abstract $table
	 * @throws InvalidArgumentException
	 */
	public function setDbTable($table)
	{
		if (is_string($table)) {
			$table = new $table();
		}

		if (!$table instanceof Zend_Db_Table_Abstract) {
			throw new InvalidArgumentException('Invalid Table Gateway Argument provided');
		}

		$this->_dbTable = $table;
		return $this;
	}

	/**
	 * @return Zend_Db_Table_Abstract
	 */
	public function getDbTable()
	{
		if ($this->_dbTable == null) {
			$this->setDbTable('Application_Model_DbTable_Guestbook');
		}
		return $this->_dbTable;
	}

	public function save(Application_Model_Guestbook $guestbook)
	{
		$data = array(
			'email'		=> $guestbook->getEmail(),
			'comment'	=> $guestbook->getComment(),
			'created'	=> date('Y-m-d H:i'),
		);

		if (null === ($data['id'] = $guestbook->getId())) {
			unset($data['id']);
			$this->getDbTable()->insert($data);
		} else {
			$this->getDbTable()->update($data, array('id' => $data['id']));
		}
		return $this;
	}

	public function find($id, Application_Model_DbTable_Guestbook $guestbook)
	{
		$result = $this->getDbTable()->find($id);
		if (0 == count($result)) {
			return null;
		}
		$row = $result->current();
		$this->setUpGuestbook($guestbook, $row);
	}

	public function fetchAll()
	{
		$resultSet = $this->getDbTable()->fetchAll();
		$entries = array();
		foreach ($resultSet as $row) {
			$entry = new Application_Model_Guestbook();
			$this->setUpGuestbook($entry, $row);
			$entries[] = $entry;
		}
		return $entries;
	}

	protected function setUpGuestbook(Application_Model_Guestbook $guestbook, Zend_Db_Table_Row_Abstract $row)
	{
		$guestbook
		->setId($row->id)
		->setEmail($row->email)
		->setComment($row->comment)
		->setCreated($row->created);
	}
}

