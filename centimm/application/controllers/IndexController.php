<?php

class IndexController extends Centixx_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    	if ($this->_currentUser) {
        	$this->view->user = $this->_currentUser;
    	}
    }
}

