<?php
class Centixx_Model_Permission extends Centixx_Model_Abstract
{
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
	 * @param Centixx_Model_User|int $for
	 * @return Centixx_Model_Group provides fluent interface
	 */
	public function setFor($for)
	{
		$this->_for = $for;
		return $this;
	}

	/**
	 * @return Centixx_Model_User
	 */
	public function getFor($raw = false)
	{
		$ret = $raw
		? $this->_for
		: $this->_mapper->getRelated($this, 'for', 'User');
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
	public function getDateStart($raw = false)
	{
		return $raw ? $this->_dateStart : $this->_dateStart ? $this->_dateStart->toString($this->_dateFormat) : null;
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
	
    protected function _customAclAssertion($role, $privilage = null)
    {
    	return parent::_customAclAssertion($role, $privilage);
    }
}