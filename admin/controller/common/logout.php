<?php
class ControllerCommonLogout extends Controller {
	public function index() {
		$this->user->logout();
		$this->response->redirect($this->url->link('common/login', '', 'SSL'));
	}
}