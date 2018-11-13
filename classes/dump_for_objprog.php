<?php 
   
   require_once 'class.init.php';


$usersData = DB::getInstance()->insert('mysignup_da', array(
      'username' => 'Wale',
      'user_email_phon' => '',
      'user_country_id' => '',
      'user_pass' => 'pass'


	));
 
 if(!$usersData){
 	echo ' not working';
 }


$usersData->get('mysignup_data', array('username', '=', 'ayobami'));


if($userData->count()){
	echo $userData->lastResult()->username;
	echo $userData->firstResult()->username;
	echo $userData->results()[0]->username;
	foreach ($userData->results() as $user) {
		echo $userData->username;
	 	return;
	  } 
}

echo 'no user';

?>