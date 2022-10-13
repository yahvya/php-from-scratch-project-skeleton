<?php

namespace Controller\Attribute;

use \Attribute;

#[Attribute]
class Route
{
    private Array $regex_list;
    private Array $call_method_list;

    /**
        @format call method separated with comma 
    */
    public function __construct(
        private string $call_methods,
        string... $path_list
    )
    {
        $this->call_method_list = array_map(
            fn (string $method):string => strtolower($method),
            explode(',',$call_methods)
        );

        $this->regex_list = array_map(
            fn (string $path):string => str_replace('?','\?',preg_replace('#\{[a-zA-Z\_]+\}#','.+',$path) ),
            $path_list
        );
    }

    public function match_with(string $url_path):bool
    {
        foreach($this->regex_list as $regex)
        {
            if(@preg_match("#^$regex$#",$url_path) )
                return in_array(strtolower($_SERVER['REQUEST_METHOD']),$this->call_method_list);
        }
        
        return false;
    }
}
