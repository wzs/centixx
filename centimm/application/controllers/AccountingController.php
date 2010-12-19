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
		
		$data = array(
			'odbiorca1' 		=> $user->getName().' '.$user->getSurname(), 
			'odbiorca2' 		=> 'Klaudyn', 
			'rachunek1' 		=> $user->getAccount(), 
			'rachunek2' 		=> '', 
			'kwota' 			=> $transaction->getValue(), 
			'slownie'			=> getTextFromNumber($transaction->getValue()), //trzeba polskie znaczki wywalic
			'zleceniodawca1' 	=> 'Centixx S.A.',
			'zleceniodawca2' 	=> 'Warszawa',
			'tytul1' 			=> 'Wynagrodzenie za miesiąc',
			'tytul2'			=> getMonthName($splitedDate[1]-1),
		);
		
		write(array_values($data));
		$this->view->params = $id;
		$this->view->array = $data;
		
		$this->view->user = $user;
		$this->viwe->transaction = $transaction;
		
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
		
		$users = Centixx_Model_Mapper_User::factory()->fetchAll();
		foreach ($users as $user){
			
			
		}
	}
}