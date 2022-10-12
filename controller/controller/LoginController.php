<?php

namespace Controller\Controller;

use \Controller\Attribute\Route;

class LoginController extends Controller
{
	#[Route('get','login','log-in')]
	public function login():void
	{
		die('login page');
	}
}
