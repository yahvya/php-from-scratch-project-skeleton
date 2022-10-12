<?php

namespace App\Helper;

abstract class FileHelper
{
	public static function get_file_extension(string $file_path,bool $combine_multiple_extension = false):?string
	{
		$explode_path = explode('.',$file_path);

		return !$combine_multiple_extension ? array_pop($explode_path) : implode('.',array_slice($explode_path,1) );
	}

	public static function convert_env_file_to_array(string $file_path):Array|NULL
	{
		$result = NULL;

		if(($file = fopen($file_path,'r') ) != false)
		{
			$result = [];

			while(!feof($file) )
			{
				$line_content = explode('=',rtrim(fgets($file),"\r\n") );

				if($line_content < 2)
				{
					$result = NULL;

					break;
				}

				$result[trim($line_content[0])] = implode('=',array_slice($line_content,1) );
			}

			fclose($file);
		}

		return $result;
	}
}