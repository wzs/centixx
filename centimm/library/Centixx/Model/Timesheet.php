<?php

class Centixx_Model_Timesheet extends Centixx_Model_Abstract
{
	protected $_resourceType = 'timesheet';
	//protected $_data;
	
	protected $_id;
	protected $_user;
	protected $_project;
	protected $_hours;
	protected $_date;
	protected $_descr;

	protected $_criteria = array();
	
	const ACTION_ACCEPT = 'accept';

	/* public function getDay(DateTime $date) {
		return;
	}*/

	public function setId($id)
	{
		$this->_id = $_id;
		return $this;
	}
	
	public function setUser($user)
	{
		$this->_user = $user;
		return $this;
	}
	
	public function setProject($project)
	{
		$this->_project = $project;
		return $this;
	}
	
	public function setHours($hours)
	{
		$this->_hours = $hours;
		return $this;
	}
	
	public function setDate($date)
	{
		$this->_date = $date;
		return $this;
	}
	
	public function setDescr($descr)
	{
		$this->_descr = $descr;
		return $this;
	}
	
	public function setAccepted($accepted)
	{
		$this->_accepted = $accepted;
		return $this;
	}
	
	public function getId()
	{
		return $this->_id;
	}
	
	public function getUser()
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
		return $this->_accepted;
	}
	
	/*
	public function setData($data) {
		$this->_data = $data;
	}

	public function getData($data) {
		return $this->_data;
	}
	*/
	
	private function isCorrectPeriod($date)
	{
		$tmp = explode('-', $date);
		if (count($tmp) < 3)
			return;
		$time = mktime(6, 0, 0, $tmp[1], $tmp[2], $tmp[0]);
		
		return ($time < time() && date('W', $time) == date('W', time()));
	}
	
	protected function _customAclAssertion($role, $privilege = null)
    {
    	if ($user instanceof Centixx_Model_User) 
    	{
    		if ($user->getRole() == Centixx_Acl::ROLE_USER) 
    		{
    			if (! $user->getId() == $_user)
    				return self::ASSERT_FAILURE;
    				
    			// użytkownik jest właścicielem wpisu 					
				
				if ($privilege == self::ACTION_READ)
					return self::ASSERT_SUCCESS; 
					
				if ($privilege == self::ACTION_UPDATE || $privilege == self::ACTION_CREATE)
				{
					// nie może modyfikować już zaakceptowanych
					if ($this->_accepted)
						return self::ASSERT_FAILURE;
						
					// nie może modyfikować poza godzinami pracy
					if ((int)time('H') < 9 || (int)time('H') > 17)
						return self::ASSERT_FAILURE;
						
					// tylko z tego tygodnia i nie później niż teraz
					//if (isCorrectPeriod($this->_date))
					return self::ASSERT_SUCCESS;
				}
			}
			elseif ($user->getRole() == Centixx_Acl::ROLE_GROUP_MANAGER)
			{
				if ($privilege == self::ACTION_READ || $privilege == self::ACTION_ACCEPT)
				{
					// czy użytkownik proszący o dostęp jest menadżerem grupy użytkownika tego wpisu
					if ($user->getId() == $this->getUser()->getManager(true))
						return self::ASSERT_SUCCESS;
				}
			}
    	}
    	
    	return parent::_customAclAssertion($role, $privilage);
    }
}

