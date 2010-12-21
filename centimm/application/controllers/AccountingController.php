<?php

class AccountingController extends Centixx_Controller_Action
{
	public function indexAction()
	{
		$this->view->transactions = Centixx_Model_Mapper_Transaction::factory()->fetchAll();

	}

	/**
	 * Zwraca plik jpg z wypełnionym druczkiem danej transakcji
	 */
	public function printAction()
	{
		$this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
		

		$params = $this->getRequest()->getParams();
		$id = $params["id"];

		$transaction = Centixx_Model_Mapper_Transaction::factory()->findByField($id,'transaction_id');

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
			'odbiorca1' 		=> strip_pl($transaction->user->getName().' '.$transaction->user->getSurname()),
			'odbiorca2' 		=> '',
//			'odbiorca2' 		=> strip_pl($user->getAddress()),
			'rachunek1' 		=> $transaction->user->getAccount(),
			'rachunek2' 		=> '',
			'kwota' 			=> $transaction->getValue(),
			'slownie'			=> getTextAmount($transaction->getValue()), //trzeba polskie znaczki wywalic
			'zleceniodawca1' 	=> 'Centixx S.A.',
			'zleceniodawca2' 	=> 'Warszawa',
			'tytul1' 			=> strip_pl($title1),
			'tytul2'			=> strip_pl($title2),
		);

		//usuwam stare nagłówki i ustawiam odpowiednie do renderowania obrazka
		$this->getResponse()->clearAllHeaders()->setHeader('content-type', 'image/jpg');

		//renderowanie obrazka
		$img = write(array_values($data));
		imagejpeg($img);
		imagedestroy($img);

	}

	public function generateAction()
	{
		$users = Centixx_Model_Mapper_User::factory()->fetchAll();
		$success = false;
		foreach ($users as $user){
			$res = $user->generateTransaction($user, date("Y-m-d"));
			if ($res) {
				$success = true;
			}
		}

		if ($success) {
			$this->addFlashMessage('Lista przelewów została wygenerowana', false, true);
		} else {
			$this->addFlashMessage('Nie wygenerowano nowych przelewów', true, true);
		}
		$this->redirect('accounting');
	}
}