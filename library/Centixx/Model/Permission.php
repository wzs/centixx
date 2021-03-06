<?php
class Centixx_Model_Permission extends Centixx_Model_Abstract
{
	const TYPE_ADD_CEO = 'add_ceo';

	protected $_resourceType = 'permission';

	protected $_id;

	/**
	 * @var Zend_Date
	 */
	protected $_dateStart;

	/**
	 * @var Zend_Date
	 */
	protected $_dateEnd;

	/**
	 * @var Centixx_Model_User
	 */
	protected $_from;

	/**
	 * @var Centixx_Model_User
	 */
	protected $_to;

	protected $_type;

	/**
	 * @var int
	 */
	protected $_count;

	/**
	 * W jaki sposób ma być formatowana data
	 * @var string
	 */
	protected $_dateFormat = 'Y-MM-dd';

	public function setType($type)
	{
		$this->_type = $type;
		return $this;
	}

	public function getType()
	{
		return $this->_type;
	}

	/**
	 * @param int $count
	 */
	public function setCount($count)
	{
		$this->_count = $count;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getCount()
	{
		return $this->_count;
	}


	/**
	 * @param Centixx_Model_User|int $from
	 * @return Centixx_Model_Group provides fluent interface
	 */
	public function setFrom($from)
	{
		$this->_from = $from;
		return $this;
	}

	/**
	 * @return Centixx_Model_User
	 */
	public function getFrom($raw = false)
	{
		$ret = $raw
		? $this->_from
		: $this->_mapper->getRelated($this, 'from', 'User');
		return $ret;
	}

	/**
	 * @param Centixx_Model_User|int $to
	 * @return Centixx_Model_Group provides fluent interface
	 */
	public function setTo($to)
	{
		$this->_to = $to;
		return $this;
	}

	/**
	 * @return Centixx_Model_User
	 */
	public function getTo($raw = false)
	{
		$ret = $raw
		? $this->_to
		: $this->_mapper->getRelated($this, 'to', 'User');
		return $ret;
	}

	public function setDateStart($dateStart)
	{
		if (!$dateStart instanceof Zend_Date) {
			//parsowanie daty w formie yyyy-mm-dd
			$tmp = explode('-', $dateStart);
			$dateStart = new Zend_Date(array(
				'year' 	=> $tmp[0],
				'month' => $tmp[1],
				'day' 	=> $tmp[2],
			));
		}

		$this->_dateStart = $dateStart;
		return $this;
	}

	/**
	 * @param bool $raw czy ma być zwrócone jako Zend_Date
	 * @return Zend_Date|string
	 */
	public function getDateStart($format = false)
	{
		if (!$format) {
			$format = $this->_dateFormat;
		}
		return $this->_dateStart->toString($format);
	}


	public function setDateEnd($dateEnd)
	{
		if (!$dateEnd instanceof Zend_Date) {
			//parsowanie daty w formie yyyy-mm-dd
			$tmp = explode('-', $dateEnd);
			$dateEnd = new Zend_Date(array('year' => $tmp[0], 'month' => $tmp[1], 'day' => $tmp[2]));
		}
		$this->_dateEnd = $dateEnd;
		return $this;
	}

	/**
	 * Zwraca datę zakonczenia projektu
	 * @param bool $raw czy ma być zwrócone jako Zend_Date
	 * @return Zend_Date|string
	 */
	public function getDateEnd($raw = false)
	{
		return $raw ? $this->_dateEnd : $this->_dateEnd ? $this->_dateEnd->toString($this->_dateFormat) : null;
	}

	public function __toString()
	{
		return (string)$this->id;
	}

	/**
	 * Zwraca tekstowy, długi opis uprawnienia
	 * @return string
	 */
	public function getDescription()
	{
		$countDesc = $this->count == 1 ? 'jednokrotne' : $this->count . '-krotne';
		$typeDesc = $this->type == self::TYPE_ADD_CEO ? 'do edycji członka zarządu' : '';
		$dateDesc = ($this->_dateStart != null &&  $this->_dateEnd != null) ? " ograniczone czasowo: {$this->dateStart} - {$this->dateEnd} " : '';

		return $countDesc . ' uprawnienie ' . $typeDesc . $dateDesc;
	}
}