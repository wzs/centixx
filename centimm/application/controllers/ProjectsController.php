<?php

class ProjectsController extends Centixx_Controller_Action
{
	public function indexAction()
	{
		$this->view->projects = Centixx_Model_Mapper_Project::factory()->fetchAll();
	}

	public function deleteAction()
	{
		$projectId = $this->getRequest()->getParam('project_id');
		$project = Centixx_Model_Mapper_Project::factory()->find($projectId);


		if (!$project->isAllowed($this->_currentUser, 'edit')) {
			throw new Centixx_Acl_AuthenticationException();
		}
		$project->delete();

		$this->_flashMessenger->addMessage('Grupa została usunięta');
		$this->_redirect('projects');

	}

	public function showAction()
	{
		$projectId = $this->getRequest()->getParam('id');
		$project = Centixx_Model_Mapper_Project::factory()->find($projectId);

		if (!$project->isAllowed($this->_currentUser, 'view')) {
			throw new Centixx_Acl_AuthenticationException();
		}
		$this->view->headTitle()->prepend('Projekt ' . $project . ' - ');
		$this->view->project = $project;
	}


	public function addgroupAction()
	{
		$request = $this->getRequest();
		if ($request->isPost()) {

			$projectId = $request->getParam('id');
			$groupId =  $request->getParam('new_item');

			$group = Centixx_Model_Mapper_Group::factory()->find($groupId);
			$project = Centixx_Model_Mapper_Project::factory()->find($projectId);

			if (!$project->isAllowed($this->_currentUser, 'edit')) {
				throw new Centixx_Acl_AuthenticationException();
			}

			$project->addGroup($group);

			$this->_logger->log(
				"{$this->_currentUser} przypisał grupę {$group} do projektu {$project}",
				Centixx_Log::CENTIXX
			);
			$this->_redirect($project->getUrl('edit'));
//			$this->_forward('edit', null, null, $request->getParams());
		} else {
			$this->_forward('show', null, null, $request->getParams());
		}
	}


	public function editAction()
	{
		$projectId = $this->getRequest()->getParam('id');
		$project = Centixx_Model_Mapper_Project::factory()->find($projectId);

		$groups = Centixx_Model_Mapper_Group::factory()->fetchFreeGroups($project);

		if (!$project->isAllowed($this->_currentUser, 'edit')) {
			throw new Centixx_Acl_AuthenticationException();
		}

		$form = new Application_Form_Project_Edit();
		$form->setValues(array('project' => $project, 'groups' => $groups));

		$addGroupForm = new Centixx_Form_AddItem();
		$addGroupForm->submitLabel = 'Przypisz';
		$addGroupForm->setValues(array('items' => $groups));

		if ($this->getRequest()->isPost()) {
			try {
				$data = $this->getRequest()->getPost();
				if ($form->isValid($data)) {
					$project->setOptions($data)->save();
					$this->view->messages[] = 'Dane zostały zaktualizowane';
				}
			} catch (Exception $e) {
				echo $e->getMessage();
			}
		} else {
			$array = $project->toArray();
			$array['dateEnd'] =  $array['dateEnd']->get(Zend_Date::DATE_MEDIUM);
			$array['dateStart'] = $array['dateStart']->get(Zend_Date::DATE_MEDIUM);
				
			$form->setDefaults($array);
		}
		$this->view->form = $form;
		$this->view->project = $project;
		$this->view->addGroupForm = $addGroupForm;

	}
}