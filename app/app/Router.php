<?php

namespace App\App;

use \App\Helper\FileHelper;

use \App\Interface\MaintenanceInterface;

use \Controller\Attribute\Route;

use \Exception;
use \ReflectionClass;

class Router
{
	private Array $config_file_content;

	private ?string $controller_to_call;
	private string $method_to_call;
	
	/**
		@format route prefixes format ['prefix' => 'linked controller']
		@format config file format (.env or .json) (have to contain maintenance state as bool)
	*/
	public function __construct(
		string $config_file_path,
		string $controllers_namespace,
		string $controller_default_method,
		Array $routes_prefixes,
		?MaintenanceInterface $maintenance_manager = NULL,
		?string $internal_error_controller = NULL,
		?string $page_not_found_controller = NULL
	)
	{
		// get config file content
		if(!$this->set_config_file_content($config_file_path) )
		{
			if($internal_error_controller != NULL)
				new ($controllers_namespace . $internal_error_controller)();
			else 
                                $this->print_default_internal_error_page();

                        die();
		}

		if(!isset($this->config_file_content['maintenance']) )
			throw new Exception('Badly formed config file');

		$this->config_file_content['maintenance'] = boolval($this->config_file_content['maintenance']);

		// manage maintenance mode
		if
		(
			$this->config_file_content['maintenance'] &&
			(
				$maintenance_manager == NULL ||
				!$maintenance_manager->can_continue_in_website()
			)
		)
		{
			if($maintenance_manager != NULL)
			{
				$maintenance_manager->show_maintenance_page();

				die();
			}
			else $this->print_default_maintenance_page();
		}

		// find the controller to call

		$this->set_controller_and_method_to_call(
			$controllers_namespace,
			$controller_default_method,
			$routes_prefixes,
			$page_not_found_controller
		);

		if($this->controller_to_call != NULL)
		{
			$_ENV = $this->config_file_content;
			
			$controller = new $this->controller_to_call();

			$controller->{$this->method_to_call}();

			die();
		}
		else $this->print_default_page_not_found_page();
	}

	private function set_config_file_content(string $config_file_path):bool
	{
		switch(FileHelper::get_file_extension($config_file_path) )
		{
			case 'env':
				
				if(($config_file_content = FileHelper::convert_env_file_to_array($config_file_path) ) != NULL)
				{
					$this->config_file_content = $config_file_content;

					return true;
				}
			; break;

			case 'json':

				if(($config_file_content = @file_get_contents($config_file_path) ) != false)
				{
					$this->config_file_content = json_decode($config_file_content,true);

					return true;
				}
			; break;
		}

		return false;
	}

	private function set_controller_and_method_to_call(
		string $controllers_namespace,
		string $controller_default_method,
		Array $routes_prefixes,
		?string $page_not_found_controller
	):void
	{	
		$url_parts = array_slice(explode('/',$_SERVER['REQUEST_URI']),1);

		$prefix = count($url_parts) == 0 ? '' : $url_parts[0]; 

		if($prefix == '')
			$prefix = '/';

		$this->method_to_call = $controller_default_method;

		if(in_array($prefix,array_keys($routes_prefixes) ) )
		{
			$this->controller_to_call = $controllers_namespace . $routes_prefixes[$prefix];

			$url = implode('/',array_slice($url_parts,1) );

			$reflection_class = new ReflectionClass($this->controller_to_call);

			foreach($reflection_class->getMethods() as $reflection_method)
			{
				$is_found = false;

				foreach($reflection_method->getAttributes() as $reflection_attribute)
				{
					if(($new_instance = $reflection_attribute->newInstance() ) instanceof Route)
					{
						if($new_instance->match_with($url) )
						{
							$this->method_to_call = $reflection_method->getName();

							$is_found = true;

							break;
						}
					}
				}

				if($is_found)
					break;
			}
		}
		else $this->controller_to_call = $page_not_found_controller;
	}

	private function print_default_maintenance_page():void
	{
		echo <<<HTML
			<!DOCTYPE html>
			<html lang="fr">
			<head>
				<meta charset="UTF-8">
				<meta name="viewport" content="width=device-width, initial-scale=1.0">
				<title>Maintenance</title>
			</head>
			<body>
				<style>
					p
					{
						margin-top: 30px;
						font-size: 24px;
						font-weight: bold;
						text-align: center;
						font-family: 'Arial';
					}
				</style>

				<p>Le site est actuellement en maintenance !</p>
			</body>
			</html>
		HTML;

		die;
	}

	private function print_default_internal_error_page():void
	{
		echo <<<HTML
			<!DOCTYPE html>
			<html lang="fr">
			<head>
				<meta charset="UTF-8">
				<meta name="viewport" content="width=device-width, initial-scale=1.0">
				<title>Erreur</title>
			</head>
			<body>
				<style>
					p
					{
						margin-top: 30px;
						font-size: 24px;
						font-weight: bold;
						text-align: center;
						font-family: 'Arial';
					}
				</style>

				<p>Une erreur interne s'est produite, veuillez rafraîchir la page !</p>
			</body>
			</html>
		HTML;

		die;
	}

	private function print_default_page_not_found_page():void
	{
		echo <<<HTML
			<!DOCTYPE html>
			<html lang="fr">
			<head>
				<meta charset="UTF-8">
				<meta name="viewport" content="width=device-width, initial-scale=1.0">
				<title>Page non trouvé</title>
			</head>
			<body>
				<style>
					p
					{
						margin-top: 30px;
						font-size: 24px;
						font-weight: bold;
						text-align: center;
						font-family: 'Arial';
					}
				</style>

				<p>La page que vous cherchez n'a pas été trouvée !</p>
			</body>
			</html>
		HTML;

		die;
	}
}
