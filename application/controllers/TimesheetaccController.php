<?php

class TimesheetaccController extends Centixx_Controller_Action
{
	public function indexAction()
	{
		$timesheet = new stdClass();
		$timesheet->user = $this->_currentUser;
		
		$this->view->timesheet = $timesheet;
		
		$grid = Bvb_Grid::factory('table', $this->_config);
		//$grid->setImagesUrl('/centixx/centimm/public/img/');
		$grid->setImagesUrl($this->view->basePath . '/img/');
		//$grid->setPagination(5);
		$grid->setNoFilters(true);
		$grid->setExport(array());
		$grid->setGridId('timesheet_grid');
		
	
		$select = $this->_db->select()
		->from(array('t' => 'timesheets'),
			array(
				'Grupa' => 'g.group_name',
				'Członek' => "CONCAT_WS(' ', u.user_name, u.user_surname)",
				'Projekt' => 'p.project_name',
				'Data' => 't.timesheet_date',
				'Czas' => 't.timesheet_hours',
				'Opis' => 't.timesheet_descr',
				'timesheet_id' => 't.timesheet_id',
			)
		)
		->join(array('u' => 'users'), 't.timesheet_user = u.user_id', array())
		->join(array('g' => 'groups'), 'u.user_group = g.group_id', array())
		->join(array('p' => 'projects'), 't.timesheet_project = p.project_id', array())
		->where('t.timesheet_accepted = false')
		->where('g.group_manager = ?', $this->_currentUser->getId())
		->order(array('g.group_name', 'u.user_surname', 'p.project_name', 't.timesheet_date'))
        ;
        
        //echo $select->__toString();
		
        //$grid->setRecordsPerPage(5);
        //$grid->setPagination(10);
        $grid->setPaginationInterval(array(10 => 10, 20 => 20, 50 => 50, 100 => 100));
        $grid->setTableGridColumns(array('Grupa', 'Członek', 'Projekt', 'Data', 'Czas', 'Opis'));
        
        $grid->setSource(new Bvb_Grid_Source_Zend_Select($select));
        
        /*
        $massActions = array(
        	array(
            	'url' => $grid->getUrl(), //Zend_View_Helper_Url::url(array('controller' => 'timesheetacc', 'action' => 'accept')),
                'caption' => 'Zatwierdź',
                'confirm' => 'Czy na pewno?',
        	),
        );
 		$grid->setMassAction($massActions);
 		*/
        
        $left = new Bvb_Grid_Extra_Column();
        $left->position('right')->name('')->decorator("<input type='checkbox' name='checkbox[{{timesheet_id}}]' value='x' >");
        $grid->addExtraColumns($left);
		
		$timesheet->datagrid = "<form action='".Zend_View_Helper_Url::url(array('controller' => 'timesheetacc', 'action' => 'accept'))."' method='post'>"; 
		$timesheet->datagrid .= $grid->deploy();
		$timesheet->datagrid .= "<input type='submit' name='submit' value='Zatwierdź'></form>";

	}
	
	public function acceptAction()
	{
		if ($this->getRequest()->isPost()) 
		{
			$data = $this->getRequest()->getPost();
			//var_dump($data);
			
			foreach($data['checkbox'] as $key => $check)
			{
				//var_dump($key, $check);
			
				$timesheet = Centixx_Model_Mapper_Timesheet::factory()->find($key);

				if (!$timesheet->isAllowed($this->_currentUser, Centixx_Model_Timesheet::ACTION_ACCEPT))
					throw new Centixx_Acl_AuthenticationException();
					
				$timesheet->setOptions(array('accepted' => true))->save();
			}
		}
		$this->_forward('index');
	}
}
