<?php

function datefromweeknr($aYear, $aWeek, $aDay)
{
	$FirstDayOfWeek = 1; //First day of week is Monday
	$BaseDate = 4; //We calculate from 4/1 which is always in week 1
	$CJDDelta = 2415019; //Based on start of Chronological Julian Day
	$StartDate = floor(mktime(1, 0, 0, 01, $BaseDate, $aYear) / 86400) + 25569; //The date to start with
	$Offset = ($aWeek - 1) * 7 - (floor($StartDate) + $CJDDelta + 8 - $FirstDayOfWeek) % 7 + $aDay - 1;
	return (($StartDate + $Offset - 25569) * 86400 - 3600);
}

function my_function($date, $week)
{
	/*
	$tmp = explode('-', $date);
	if (count($tmp) < 3)
		return;
	$time = mktime(6, 0, 0, $tmp[1], $tmp[2], $tmp[0]);
	$edit = "<a href='".Zend_View_Helper_Url::url(
		array('controller' => 'timesheet', 'action' => 'edit'))."/date/$date'>Edytuj</a>";
	if ($time < time())
		return $edit;
	*/
	//var_dump($date, date("Y-m-d", $time), $edit);
	//var_dump($week, date('W'), $week == date('W'));
	static $count = 0;
	$count++;

	$hlp_date = date("Y-m-d", datefromweeknr(2010, $week, $count));

	//if (!($week == date('W')))
	//	return '';

	$edit = "<a href='".Zend_View_Helper_Url::url(
		array('controller' => 'timesheet', 'action' => 'edit', 'date' => $date))."'>Edytuj</a>";
	$add = "<a href='".Zend_View_Helper_Url::url(
		array('controller' => 'timesheet', 'action' => 'add', 'date' => $hlp_date))."'>Dodaj</a>";

	if (Centixx_Model_Timesheet::isCorrectPeriod($date))
	{
		if (empty($date))
			return $add;
		else
			return $edit;
	}

	return '';
}

function my_function2($date, $week)
{
	static $count = 0;
	$count++;
	
	if ($date)
		return $date;
	
	$hlp_date = date("Y-m-d", datefromweeknr(2010, $week, $count));
	return $hlp_date;
}

class TimesheetController extends Centixx_Controller_Action
{
	public function indexAction()
	{
		$week = $this->getRequest()->getParam('week');
		if (is_null($week))
			$week = date('W');


		$timesheet = new stdClass();
		$timesheet->user = $this->_currentUser;
		$timesheet->totalTime = $this->_db->select()->from(
			array('t' => 'timesheets'),
				array(
					'sum' => 'SUM(t.timesheet_hours)'
				)
		)->where('t.timesheet_user = ?', $this->_currentUser->getId())
		->query()
		->fetch();

		$timesheet->weekTime = $this->_db->select()->from(
			array('t' => 'timesheets'),
				array(
					'sum' => 'SUM(t.timesheet_hours)'
				)
		)->where('t.timesheet_user = ?', $this->_currentUser->getId())
		->where('WEEK(t.timesheet_date, 1) = ?', $week)
		->query()
		->fetch();

		if (!$timesheet->totalTime->sum)
			$timesheet->totalTime->sum = 0;
		if (!$timesheet->weekTime->sum)
			$timesheet->weekTime->sum = 0;

		//rand(120, 160);

		$this->view->timesheet = $timesheet;

//		$grid = Bvb_Grid::factory('table', $this->_config);
		$grid = new Bvb_Grid_Deploy_Table($this->_config);

		$grid->setImagesUrl($this->view->basePath . '/img/');
		$grid->setPagination(5);
		$grid->setNoFilters(true);
		$grid->setExport(array('excel'));
		$grid->setGridId('timesheet_grid');

		$select0 = $this->_db->select()
		->from(array('p' => 'projects'),
			array(
				'p.project_name',
				't.timesheet_project',
				't.timesheet_hours',
				't.timesheet_date',
				't.timesheet_descr',
			)
		)->join(array('t' => 'timesheets'),	"p.project_id = t.timesheet_project", array())
		->where('t.timesheet_user = ?', $this->_currentUser->getId())
		->where('WEEK(t.timesheet_date, 1) = ?', $week);


		$select = $this->_db->select()
		->from(array('d' => 'daysofweek'),
			array(
               	'Projekt' => 's.project_name',
               	'Data' => 's.timesheet_date',
				'Dzień' => 'DAYNAME(d.day)',
				'Czas' => 's.timesheet_hours',
				'Opis' => 's.timesheet_descr'
               )
		)->joinLeft(array('s' => new Zend_Db_Expr('('.$select0->__toString().')')),	'DAYNAME(d.day) = DAYNAME(s.timesheet_date)', array());

        //echo $select->__toString();

		$grid->setSource(new Bvb_Grid_Source_Zend_Select($select));

		$prev_href = Zend_View_Helper_Url::url(array('controller' => 'timesheet', 'action' => 'index', 'week' => $week - 1));
		$next_href = Zend_View_Helper_Url::url(array('controller' => 'timesheet', 'action' => 'index', 'week' => $week + 1));

		$rows = new Bvb_Grid_Extra_Rows();
		$rows->addRow('beforePagination', array(
		                array('colspan' => 4, 'content' => "<a href='$prev_href'>Poprzedni tydzień</a>"),
		                array('colspan' => 1, 'content' => "<a href='$next_href'>Następny tydzień</a>"),
		            ));
		if (is_null($this->getRequest()->getParam('_exportTo')))
			$grid->addExtraRows($rows);

		//$edit = "<a href='".Zend_View_Helper_Url::url(
		//array('controller' => 'timesheet', 'action' => 'edit'))."/date/{{Data}}'>Edytuj</a>";
		
		$right = new Bvb_Grid_Extra_Column();
		$right->position('right')->name('edit')->title('Edytuj')
		//->decorator($edit);
		->callback(array('function' => 'my_function', 'params' => array('{{Data}}', $week)));

		$grid->addExtraColumns($right);
		
		//$grid->updateColumn('Data', array('decorator'=>'-{{Data}}-'));
		$grid->updateColumn('Data', array('callback' => array('function' => 'my_function2', 'params' => array('{{Data}}', $week))));

		$timesheet->datagrid = $grid->deploy();
	}

	public function addAction()
	{
		$date = $this->getRequest()->getParam('date');

		if (!Centixx_Model_Timesheet::isCorrectPeriod($date))
			throw new Centixx_Acl_AuthenticationException('Nie masz uprawnień do edycji tej daty');

		$this->_helper->viewRenderer('edit');

		$form = new Application_Form_Timesheet_Edit();

		$projects = Centixx_Model_Mapper_Project::factory()->fetchForDate($date);
		if (count($projects) == 0)
		{
			$this->addFlashMessage('Brak projektów prowadzonych w tym dniu', true, true);
			$this->_redirect('/timesheet');
		}
		

		$form->setValues(array(
			'projects' => $projects,
			'date' => $date,
			'user' => $this->_currentUser->getId(),
		));

		$timesheet = new Centixx_Model_Timesheet();

		if (!$timesheet->isAllowed($this->_currentUser, Centixx_Model_Abstract::ACTION_CREATE))
			throw new Centixx_Acl_AuthenticationException();


		$this->view->headTitle()->prepend('Dodawanie wpisu ');
		$this->view->header = 'Dodajesz wpis dla daty: '.$date;

		if ($this->getRequest()->isPost())
		{
			$data = $this->getRequest()->getPost();
			//$data['user'] = 1;
			if ($form->isValid($data))
			{
				//if ($data['user_id'] != )
				$timesheet->setOptions($data)->save();
				//$this->log(Centixx_Log::TIMESHEET_CREATED, $timesheet);
				$this->addFlashMessage('Wpis został dodany', false, true);
				$this->_forward('index');
			}
			else
				$form->setDefaults($data);
		}

		$this->view->editForm = $form;
	}

	public function editAction()
	{
		$date = $this->getRequest()->getParam('date');
		$userId = $this->_currentUser->getId();

		$timesheet = Centixx_Model_Mapper_Timesheet::factory()->findByUserDate($userId, $date);

		if (!Centixx_Model_Timesheet::isCorrectPeriod($date))
			throw new Centixx_Acl_AuthenticationException('Nie masz uprawnień do edycji tej daty');

		$this->_helper->viewRenderer('edit');

		$form = new Application_Form_Timesheet_Edit();

		$projects = Centixx_Model_Mapper_Project::factory()->fetchForDate($date);
		//var_dump($projects);
		
		$projects = Centixx_Model_Mapper_Project::factory()->fetchForDate($date);
		if (count($projects) == 0)
		{
			$this->addFlashMessage('Brak projektów prowadzonych w tym dniu', true, true);
			$this->_redirect('/timesheet');
		}

		$form->setValues(array(
			'projects' => $projects,
			'date' => $date,
			'user' => $userId,
		));

		//$timesheet = new Centixx_Model_Timesheet();

		if (!$timesheet->isAllowed($this->_currentUser, Centixx_Model_Abstract::ACTION_CREATE))
			throw new Centixx_Acl_AuthenticationException();


		$this->view->headTitle()->prepend('Edycja wpisu ');
		$this->view->header = 'Edytujesz wpis dla daty: '.$date;

		if ($this->getRequest()->isPost())
		{
			$data = $this->getRequest()->getPost();
			//$data['user'] = 1;
			if ($form->isValid($data))
			{
				//if ($data['user_id'] != )
				$timesheet->setOptions($data)->save();
				//$this->log(Centixx_Log::TIMESHEET_CREATED, $timesheet);
				$this->addFlashMessage('Wpis został dodany', false, true);
				$this->_forward('index');
			}
			else
				$form->setDefaults($data);
		}
		else
		{
			$array = $timesheet->toArray();
			$array['user'] = $userId;
			//var_dump($timesheet, $array);
			$form->setDefaults($array);
		}

		$this->view->editForm = $form;
	}
}
