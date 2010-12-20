<?php

class AccountingController extends Centixx_Controller_Action
{
	public function indexAction()
	{	
		$this->view->transactions = Centixx_Model_Mapper_Transaction::factory()->fetchAll();

	}
	

	
	public function printAction()
	{
		$this->_helper->layout()->disableLayout();
		
		$params = $this->getRequest()->getParams();
		$id = $params["id"];
		
		$transaction = Centixx_Model_Mapper_Transaction::factory()->findByField($id,'transaction_id');
		$user = Centixx_Model_Mapper_User::factory()->findByField($transaction->getUser(),'user_id');
		
		$splitedDate = split('-',$transaction->getDate());
		
		$lenght = strlen($transaction->getTitle());
	
		if($lenght < 27){
			$title1 = substr($transaction->getTitle(), 0, $lenght);
			$title2 = "";
		}
		else if($lenght < 54){
			$title1 = substr($transaction->getTitle(), 0, 26);
			$title2 = substr($transaction->getTitle(), 26, $lenght - 1);
		}
		else{
			$title1 = substr($transaction->getTitle(), 0, 26);
			$title2 = substr($transaction->getTitle(), 26, 53);
		}
		
		$data = array(
			'odbiorca1' 		=> $user->getName().' '.$user->getSurname(), 
			'odbiorca2' 		=> 'Klaudyn', 
			'rachunek1' 		=> $user->getAccount(), 
			'rachunek2' 		=> '', 
			'kwota' 			=> $transaction->getValue(), 
			'slownie'			=> getTextAmount($transaction->getValue()), //trzeba polskie znaczki wywalic
			'zleceniodawca1' 	=> 'Centixx S.A.',
			'zleceniodawca2' 	=> 'Warszawa',
			'tytul1' 			=> $title1,
			'tytul2'			=> $title2,
		);
		
		write(array_values($data));
		$this->view->params = $id;
		$this->view->array = $data;
		
		$this->view->user = $user;
		$this->view->transaction = $transaction;
		
//		$odbiorca1;
//		$odbiorca2;
//		$konto1;
//		$konto2;
//		$kwota;
//		$kwotaSlownie;
//		$zlec1;
//		$zlec2;
//		$tyt1;
//		$tyt2;
	}
	
	public function generateAction(){
		
//		$this->_helper->layout()->disableLayout();         
		$this->_helper->viewRenderer->setNoRender();
		
		$users = Centixx_Model_Mapper_User::factory()->fetchAll();
		foreach ($users as $user){
			$user->generateTransaction($user, date("Y-m-d"));
		}
		
		$this->_redirect('accounting');
	}
}