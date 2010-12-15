<?php

class ErrorController extends Centixx_Controller_Action
{
	public function errorAction()
	{
		$errors = $this->_getParam('error_handler');
		if ($errors->exception instanceof Centixx_Acl_AuthenticationException) {
			$this->_response->clearBody(); //layout generował się dwa razy
			$this->getResponse()->setHttpResponseCode(403);
			$this->view->message = 'Dostęp zabroniony';

			$uri = $errors->request->getRequestUri();
			$this->log(Centixx_Log::UNAUTHORISED_ACCESS, $uri);

		} else {
			switch ($errors->type) {
				case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
				case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
				case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:

					// 404 error -- controller or action not found
					$this->getResponse()->setHttpResponseCode(404);
					$this->view->message = 'Page not found';
					break;
				default:
					// application error
					$this->getResponse()->setHttpResponseCode(500);
					$this->view->message = 'Application error';
					break;
			}
		}

		// conditionally display exceptions
		if ($this->getInvokeArg('displayExceptions') == true) {
			$this->view->exception = $errors->exception;
		}

		$this->view->request   = $errors->request;

		if ($this->_isAjaxRequest) {
			$this->getResponse()->setHttpResponseCode(200);
			echo json_encode(false);
		}
	}


}

