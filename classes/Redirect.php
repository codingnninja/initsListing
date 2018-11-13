
<?php

class Redirect {
	     public static function to($location = null){
	     	if($location){
	     		if(is_numeric($location)){
	     			switch ($location) {
	     				case 404:
	     				   header('HTTP/0.1 404 Not Found');
	     				   include '../views/404.php';
	     					break;
	     				
	     			}
	     		}
	     	}
	     	header('Location:'.$location);
	     }
}


?>