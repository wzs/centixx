<?php

class Centixx_Model_Timesheet extends Centixx_Model_Abstract //implements Zend_Acl_Role_Interface
{
	protected $_resourceType = 'timesheet';
	//protected $_data;

	protected $_id;
	protected $_user;
	protected $_project;
	protected $_hours;
	protected $_date;
	protected $_descr;
	protected $_accepted;

	protected $_criteria = array();

	const ACTION_ACCEPT = 'accept';

	/* public function getDay(DateTime $date) {
		return;
	}*/

	public function setId($id)
	{
		$this->_id = $id;
		//var_dump('id', $id);
		return $this;
	}

	public function setUser($user)
	{
		$this->_user = $user;
		//var_dump('user', $user);
		return $this;
	}

	public function setProject($project)
	{
		$this->_project = $project;
		//var_dump('project', $project);
		return $this;
	}

	public function setHours($hours)
	{
		$this->_hours = $hours;
		//var_dump('hours', $hours);
		return $this;
	}

	public function setDate($date)
	{
		$this->_date = $date;
		//var_dump('date', $date);
		return $this;
	}

	public function setDescr($descr)
	{
		$this->_descr = $descr;
		//var_dump('descr', $descr);
		return $this;
	}

	public function setAccepted($accepted)
	{
		$this->_accepted = $accepted;
		//var_dump('accepted', $accepted);
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function getUser($raw = false)
	{
		return $raw ? $this->_user : $this->_mapper->getRelated($this, 'user', 'User');
	}

	public function getProject($raw = false)
	{
		return $raw ? $this->_project : $this->_mapper->getRelated($this, 'project', 'Project');
	}

	public function getHours()
	{
		return $this->_hours;
	}

	public function getDate()
	{
		return $this->_date;
	}

	public function getDescr()
	{
		return $this->_descr;
	}

	public function getAccepted()
	{
		return ($this->_accepted) ? true : false;
	}

	public static function isCorrectPeriod($date)
	{
		// FIXME: wyłączone na czas kodzenia
		//return true;
		
		if ((int)time('H') < 9 || (int)time('H') > 17)
			return false;

		$tmp = explode('-', $date);
		if (count($tmp) < 3)
			return;
		$time = mktime(6, 0, 0, $tmp[1], $tmp[2], $tmp[0]);

		return ($time < time() && date('W', $time) == date('W', time()));
	}

	protected function _customAclAssertion($user, $privilege = null)
    {
    	if (!$user instanceof Centixx_Model_User)
    	{
    		return parent::_customAclAssertion($role, $privilage);
    	}

    	if ($user->hasRole(Centixx_Acl::ROLE_USER))
    	{
    		if ($privilege == self::ACTION_UPDATE)
    		{
    			// użytkownik nie jest właścicielem wpisu
    			if ($user->getId() != $this->_user) {
    				return self::ASSERTION_FAILURE;
    			}

    			// nie można edytować już zaakceptowanych
    			if ($this->_accepted) {
					return self::ASSERTION_FAILURE;
    			}
    		}

			if ($privilege == self::ACTION_READ)
			{
				return self::ASSERT_SUCCESS;
			}

			if ($privilege == self::ACTION_UPDATE || $privilege == self::ACTION_CREATE)
			{
				// nie może modyfikować poza godzinami pracy
				// FIXME: wyłączone na czas kodzenia
				//if ((int)time('H') < 9 || (int)time('H') > 17)
				//	return self::ASSERTION_FAILURE;

				//tylko z tego tygodnia i nie później niż teraz
				//if (isCorrectPeriod($this->_date)
				return self::ASSERTION_SUCCESS;
			}
		}
		if ($user->hasRole(Centixx_Acl::ROLE_GROUP_MANAGER)) {
			if ($privilege == self::ACTION_READ || $privilege == self::ACTION_ACCEPT) {
				// czy użytkownik proszący o dostęp jest menadżerem grupy użytkownika tego wpisu
				if ($user->getId() == $this->getUser()->getGroup()->getManager()->getId())
					return self::ASSERTION_SUCCESS;
			}
    	}
    }
}

