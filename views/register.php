<?php

require_once '../classes/init.php';

if(Input::exists()){
	
   if (Token::check(Input::get('token'))) {
  
	$validate = new Validation();
	$validation = $validate->check($_POST, array(
        
        'admin_name' => array(
               'required' => true,
               'min' => 2,
               'max' => 20,
               'unique' => 'biz_admins'

        	),
        'password' => array(
        	   'required' => true,
        	   'min' => 6

        	),
        'password_again' => array(
        	   'required' => true,
        	   'matches' => 'password'
        	)
  
        
		));
	if ($validation->passed()) {
		Session::flash('success', 'You registered successfully');
        $user = new User();
         $salt = Hash::salt(32);
        $admin_name = Input::get('admin_name');
        $admin_pass = Hash::make(Input::get('password'), $salt);
        
        try {
        	$user->create(array(
                 'admin_name' => $admin_name,
                 'admin_pass' => $admin_pass,
				 'salt' => $salt,
        		));

			$user->login($admin_name, $admin_pass);

        	Session::flash('home', 'You have signup successfully');
        	Redirect::to('index');
        	
        } catch (Exception $e){
        	die($e->getMessage());
        }



	}else{
		foreach ($validation->errors() as $error) {
			
			echo $error, '<br>';
		}
	}
}
   }
?>


<form action="" method="post" >
	  <div class="field">
	  	<label for="username">Enter Your Username</label>
	  	<input type="text" name="admin_name" id="admin_name" value="<?php echo escape(Input::get('admin_name')); ?>" autocomplete="off">
	  </div>

	  <div class="field">
	  	<label for="password">Enter Your Password</label>
	  	<input type="password" name="password" id="password" value="" autocomplete="off">
	  </div>

	  <div class="field">
	  	<label for="password_again">Enter Your Password Again</label>
	  	<input type="password" name="password_again" id="password_again" value="" autocomplete="off">
	  </div>
       <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
       <input type="submit" name="register" value="Register now">
	   <br><hr>
     OR
    <a href="http://localhost/initstest/views/login">Login</a>

</form>