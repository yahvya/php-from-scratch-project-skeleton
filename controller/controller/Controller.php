<?php

namespace Controller\Controller;

abstract class Controller
{
	public function default():void
	{
		$this->redirect();
	}

	public function redirect(string $link = '/'):void
	{
		header("Location: $link");
		die();
	}
}