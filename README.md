# php-from-scratch-project-skeleton
this repository contain a base structure of an mvc project with a simple routing system 

- add the group url prefix in the array in index file
- create the controller of this group
- using the Route attribute on a controller method you can specify the accepted call method (get,post,put...) and a list of linked url to this method 
- if you work with an url like localhost/project-name/ place in index file in dev mode this line
  $_SERVER['REQUEST_URI'] = str_replace('/project-name', '', $_SERVER ['REQUEST_URI']) ;
