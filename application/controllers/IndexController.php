<?php

class IndexController extends Centixx_Controller_Action
{
    public function indexAction()
    {
    	if ($this->_currentUser) {
        	$this->view->user = $this->_currentUser;
    	}
    }
}

