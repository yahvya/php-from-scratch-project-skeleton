<?php

namespace Controller\Controller;

use \App\Interface\MaintenanceInterface;

class MaintenanceController extends Controller implements MaintenanceInterface
{
	public function can_continue_in_website():bool
	{
		return false;
	}

	public function show_maintenance_page():void
	{
		echo 'page de maintenance';
	}
}