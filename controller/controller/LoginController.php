<?php

namespace Controller\Controller;

use \Controller\Attribute\Route;

class LoginController extends Controller
{
	#[Route('get,post','login','log-in')]
	public function login():void
	{
		// can be accessed with 'login/login' or 'login' thanks to the default method override or 'login/log-in' with get or post method
		die('login page');
	}
	
	public function default():void
	{
		$this->login();
	}
}
