<?php

class Input {
	 public static function exists($type = 'post'){

	 	switch ($type){
	 		case 'post':
	 			   return (!empty($_POST)) ? true : false;
	 		break;
	 		case 'get':
                   return (!empty($_GET)) ? true : false;
	 		break;
	 		default:
	 		     return false;
	 		break;
	 	}
	
	 }

	 public static function get( $item ){

           if(isset($_POST[$item])){

           	   return $_POST[$item];

           }else if (isset($_GET[$item])){

           	   return $_GET[$item];

           }else if(isset($_FILES[$item])){

			   $images = [];
			   for ($i=0; $i < count($_FILES[$item]["name"]) ; $i++) { 
			   $images[$i] = [  
			   			$_FILES[$item]["name"][$i], 
						$_FILES[$item]["tmp_name"][$i],
						$_FILES[$item]["size"][$i]
					];
			   }

			   return ($images[0][0] == '') ? false : $images;
		   }

           return '';
	 }

	 //this is technical debt; To do: I need to merge it with above or decouple them reasonably
	 public static function getOneImage( $item ){

            if(isset($_FILES[$item])){
			   $image = [  
					$_FILES[$item]["name"], 
					$_FILES[$item]["tmp_name"],
					$_FILES[$item]["size"]
				];
				
			 return ($image[0] == '') ? false : $image;	
			}

           return '';
	 }

}


?>