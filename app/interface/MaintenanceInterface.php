<?php

namespace App\Interface;

interface MaintenanceInterface
{
	public function can_continue_in_website():bool;

	public function show_maintenance_page():void;
}