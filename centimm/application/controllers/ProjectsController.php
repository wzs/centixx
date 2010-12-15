<?php

class ProjectsController extends Centixx_Controller_Action
{
	public function indexAction()
	{
		$this->view->projects = Centixx_Model_Mapper_Project::factory()->fetchAll();
	}

	public function deleteAction()
	{
		$projectId = $this->getRequest()->getParam('id');
		$project = Centixx_Model_Mapper_Project::factory()->find($projectId);

		if (!$project->isAllowed($this->_currentUser, Centixx_Model_Abstract::ACTION_DELETE)) {
			throw new Centixx_Acl_AuthenticationException();
		}
		$project->delete();

		$this->_flashMessenger->addMessage('Projekt został usunięty');
		$this->_redirect('projects');

	}

	public function showAction()
	{
		$projectId = $this->getRequest()->getParam('id');
		$project = Centixx_Model_Mapper_Project::factory()->find($projectId);

		if (!$project->isAllowed($this->_currentUser, Centixx_Model_Abstract::ACTION_READ)) {
			throw new Centixx_Acl_AuthenticationException();
		}
		$this->view->headTitle()->prepend('Projekt ' . $project . ' - ');
		$this->view->project = $project;
	}

	/**
	 * @throws Centixx_Acl_AuthenticationException
	 * @deprecated
	 */
	public function addGroupAction()
	{
		$request = $this->getRequest();
		if ($request->isPost()) {

			$projectId = $request->getParam('id');
			$groupId =  $request->getParam('new_item');

			$group = Centixx_Model_Mapper_Group::factory()->find($groupId);
			$project = Centixx_Model_Mapper_Project::factory()->find($projectId);

			if (!$project->isAllowed($this->_currentUser, Centixx_Model_Abstract::ACTION_UPDATE)) {
				throw new Centixx_Acl_AuthenticationException();
			}

			$project->addGroup($group);

			$this->_redirect($project->getUrl('edit'));
		} else {
			$this->_forward('show', null, null, $request->getParams());
		}
	}

	protected function prepare($project, $mode)
	{
		//ustawiam, zeby renderowano ten sam widok co w przypadku editAction
		$this->_helper->viewRenderer('edit');

		$availableUsers = Centixx_Model_Mapper_User::factory()->fetchForProject($project);

		$form = new Application_Form_Project_Edit();
		$form->setValues(array('project' => $project, 'availableUsers' => $availableUsers));

		if ($this->getRequest()->isPost()) {
			try {
				$data = $this->getRequest()->getPost();
				if ($form->isValid($data)) {
					$project->setOptions($data)->save();

					if ($mode == 'edit') {
						$this->_flashMessenger->addMessage('Dane zostały zaktualizowane');
					} else {
						$this->_flashMessenger->addMessage('Projekt został utworzony');
					}
					$this->_redirect($project->getUrl('edit'));
				}
			} catch (Exception $e) {
				echo $e->getMessage();
			}
		} else {
			$form->setDefaults($project->toArray());

			//potrzebne ze wzgledu na "dziwne" zachowanie multicheckboksa
			$form->setDefaults(array(
				'users' => array_keys($project->users),
				'manager' => $project->manager->id,
			));


		}

		$this->view->form = $form;
		$this->view->project = $project;
		$this->view->formAction = $mode;
	}

	public function newAction()
	{
		$project = new Centixx_Model_Project();

		//ustawiam odpowiedni dział dla nowotworzonego projektu
		$department = Centixx_Model_Mapper_Department::factory()->findByManager($this->_currentUser);
		$project->department = $department;

		if (!$project->isAllowed($this->_currentUser, Centixx_Model_Abstract::ACTION_CREATE)) {
			throw new Centixx_Acl_AuthenticationException();
		}

		$this->prepare($project, 'new');
	}

	public function editAction()
	{
		$projectId = $this->getRequest()->getParam('id');
		$project = Centixx_Model_Mapper_Project::factory()->find($projectId);

		if (!$project->isAllowed($this->_currentUser, Centixx_Model_Abstract::ACTION_UPDATE)) {
			throw new Centixx_Acl_AuthenticationException();
		}


		$this->prepare($project, 'edit');
	}
}